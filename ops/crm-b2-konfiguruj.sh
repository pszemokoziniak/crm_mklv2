#!/bin/bash
#
# Jednorazowa konfiguracja wysylki kopii CRM do Backblaze B2.
# Uruchom na serwerze jako root:  /usr/local/sbin/crm-b2-konfiguruj.sh
#
# Poswiadczenia podajesz tutaj, w swoim terminalu — nigdzie ich nie przesylasz.
#
# ============================ UWAGA / ROZNICA WOBEC HRM ============================
# Kreator HRM nadpisuje CALY plik rclone.conf (cat > ...). TEN kreator DOPISUJE
# sekcje [crm-b2] i [crm-b2-crypt], a remoty HRM ([hrm-b2], [hrm-b2-crypt])
# zostawia nietkniete. Przed i po zmianie robimy kopie pliku i sprawdzamy, ze
# `rclone listremotes` widzi WSZYSTKIE remoty (HRM + CRM). Gdyby liczba spadla,
# przerywamy i przywracamy plik z kopii.
# ==================================================================================
#
set -euo pipefail

KONF=/root/.config/rclone/rclone.conf

echo
echo "=========================================================="
echo " Konfiguracja kopii CRM do Backblaze B2"
echo "=========================================================="
echo
echo "Zanim zaczniesz, w panelu B2 musi juz istniec (patrz JAK-ODTWORZYC.txt):"
echo "  - OSOBNY kubelek CRM (np. mkl-crm-backup) z Object Lock w trybie"
echo "    COMPLIANCE i DOMYSLNA RETENCJA kubelka = 30 dni"
echo "  - OSOBNY klucz aplikacyjny ograniczony do tego kubelka"
echo "    (nie wspoldzielony z HRM)"
echo

read -rp "Nazwa kubelka CRM (np. mkl-crm-backup): " KUBELEK
[ -n "$KUBELEK" ] || { echo "Nazwa kubelka jest wymagana."; exit 1; }

# Zabezpieczenie przed pomylka: nie pozwalamy wskazac kubelka HRM.
case "$KUBELEK" in
    *hrm*) echo "BLAD: to wyglada na kubelek HRM. CRM musi miec WLASNY kubelek."; exit 1 ;;
esac

read -rp "keyID klucza aplikacyjnego CRM: " KEYID
[ -n "$KEYID" ] || { echo "keyID jest wymagane."; exit 1; }

read -rsp "applicationKey (nie bedzie widoczny): " APPKEY; echo
[ -n "$APPKEY" ] || { echo "applicationKey jest wymagany."; exit 1; }

echo
echo "Teraz haslo szyfrujace. To NIM sa szyfrowane dane, zanim opuszcza serwer."
echo "Bez niego kopia jest bezuzyteczna — zapisz je w menedzerze hasel."
echo "MUSI byc INNE niz haslo HRM (osobny kubelek, osobne szyfrowanie)."
echo "Wcisnij Enter, zeby wylosowac mocne haslo."
read -rsp "Haslo szyfrujace: " HASLO; echo
if [ -z "$HASLO" ]; then
    HASLO=$(head -c 32 /dev/urandom | base64 | tr -d '=+/' | head -c 40)
    LOSOWE=1
fi
read -rsp "Drugie haslo (sol, Enter = wylosuj): " SOL; echo
if [ -z "$SOL" ]; then
    SOL=$(head -c 32 /dev/urandom | base64 | tr -d '=+/' | head -c 40)
    LOSOWA_SOL=1
fi

mkdir -p "$(dirname "$KONF")"

# --- KOPIA PLIKU PRZED ZMIANA (ratunek dla remotow HRM) ---
KOPIA=""
if [ -f "$KONF" ]; then
    KOPIA="$KONF.przed-crm-$(date +%F-%H%M%S)"
    cp -a "$KONF" "$KOPIA"
    echo "Kopia rclone.conf: $KOPIA"
fi

PRZED=$(rclone listremotes 2>/dev/null | wc -l)

# --- DOPISANIE sekcji CRM (bez ruszania HRM) ---
# Usuwamy tylko ewentualne stare sekcje [crm-b2]/[crm-b2-crypt] (re-run),
# WSZYSTKO inne przepisujemy 1:1. Potem dopisujemy swieze sekcje na koncu.
NOWY=$(mktemp)
if [ -f "$KONF" ]; then
    awk '
        /^\[/ { drop = ($0 == "[crm-b2]" || $0 == "[crm-b2-crypt]") }
        !drop { print }
    ' "$KONF" > "$NOWY"
fi

# rclone przechowuje hasla "obscured" — to NIE jest szyfrowanie, tylko zakodowanie.
# Stad plik musi byc 600 i tylko dla roota.
{
    echo ""
    echo "[crm-b2]"
    echo "type = b2"
    echo "account = $KEYID"
    echo "key = $APPKEY"
    echo "hard_delete = false"
    echo ""
    echo "[crm-b2-crypt]"
    echo "type = crypt"
    echo "remote = crm-b2:$KUBELEK/crm"
    echo "filename_encryption = standard"
    echo "directory_name_encryption = true"
    echo "password = $(rclone obscure "$HASLO")"
    echo "password2 = $(rclone obscure "$SOL")"
} >> "$NOWY"

install -m 600 "$NOWY" "$KONF"
rm -f "$NOWY"

# --- WERYFIKACJA, ze nie zabilismy remotow HRM ---
# UWAGA: listremotes czytamy RAZ do zmiennej i grepujemy z here-stringa. Potok
# "rclone listremotes | grep -q" pod 'set -o pipefail' potrafi falszywie zwrocic
# blad: grep -q konczy po dopasowaniu, rclone dostaje SIGPIPE, a pipefail uznaje
# caly potok za nieudany — co wczesniej wywolywalo falszywy rollback.
REMOTY=$(rclone listremotes 2>/dev/null || true)
PO=$(grep -c ':' <<<"$REMOTY" || true)
echo
echo "--- remoty rclone po zmianie ---"
printf '%s\n' "$REMOTY"
brak_remote() { ! grep -qx "$1" <<<"$REMOTY"; }
if brak_remote "hrm-b2:" || brak_remote "hrm-b2-crypt:"; then
    echo "BLAD KRYTYCZNY: zniknely remoty HRM! Przywracam plik z kopii."
    [ -n "$KOPIA" ] && cp -a "$KOPIA" "$KONF"
    exit 1
fi
if brak_remote "crm-b2:" || brak_remote "crm-b2-crypt:"; then
    echo "BLAD: nie dodano remotow CRM. Przywracam plik z kopii."
    [ -n "$KOPIA" ] && cp -a "$KOPIA" "$KONF"
    exit 1
fi
echo "OK: remoty HRM nietkniete, remoty CRM dodane ($PRZED -> $PO)."

# --- test polaczenia ---
echo
echo "--- test polaczenia z kubelkiem CRM ---"
if TESTOUT=$(rclone lsd "crm-b2:$KUBELEK" 2>&1); then
    echo "OK: klucz dziala, kubelek $KUBELEK dostepny."
else
    echo "BLAD: nie moge otworzyc kubelka $KUBELEK. Sprawdz nazwe kubelka, keyID i applicationKey."
    echo "Najczestsze przyczyny: literowka w nazwie kubelka; klucz ograniczony do INNEGO"
    echo "kubelka; wklejony keyID zamiast applicationKey (lub odwrotnie)."
    echo "--- pelny komunikat rclone: ---"
    echo "$TESTOUT"
    echo "--- konfiguracja CRM zostaje zapisana; popraw dane i uruchom kreator ponownie ---"
    exit 1
fi

echo "--- test zapisu i odczytu przez szyfrowanie ---"
PROBKA=$(mktemp)
echo "test-crm-$(date +%s)" > "$PROBKA"
rclone copyto "$PROBKA" crm-b2-crypt:test/probka.txt
if [ "$(rclone cat crm-b2-crypt:test/probka.txt)" = "$(cat "$PROBKA")" ]; then
    echo "OK: zapis i odczyt przez szyfrowanie dzialaja."
else
    echo "BLAD: odczytana probka rozni sie od zapisanej."; rm -f "$PROBKA"; exit 1
fi
rm -f "$PROBKA"

# --- WERYFIKACJA Object Lock przez API B2 ---
# Panel pokazuje "Enabled" nawet gdy nie ma DOMYSLNEJ RETENCJI, a to wlasnie
# domyslna retencja realnie chroni pliki (rclone nie wysyla naglowka retencji).
echo
echo "--- weryfikacja Object Lock (API b2_list_buckets) ---"
AUTH=$(curl -s -u "$KEYID:$APPKEY" https://api.backblazeb2.com/b2api/v2/b2_authorize_account || true)
APIURL=$(printf '%s' "$AUTH" | grep -oE '"apiUrl":"[^"]*"'             | head -1 | sed 's/.*":"//; s/"$//')
TOKEN=$(printf '%s'  "$AUTH" | grep -oE '"authorizationToken":"[^"]*"' | head -1 | sed 's/.*":"//; s/"$//')
ACCT=$(printf '%s'   "$AUTH" | grep -oE '"accountId":"[^"]*"'          | head -1 | sed 's/.*":"//; s/"$//')
if [ -z "$APIURL" ] || [ -z "$TOKEN" ]; then
    echo "UWAGA: nie udalo sie zalogowac do API B2 do weryfikacji Object Lock."
    echo "       Sprawdz recznie w panelu: tryb compliance + domyslna retencja 30 dni."
else
    BUCKETS=$(curl -s -H "Authorization: $TOKEN" \
        -d "{\"accountId\":\"$ACCT\",\"bucketName\":\"$KUBELEK\"}" \
        "$APIURL/b2api/v2/b2_list_buckets" || true)
    FL=$(grep -oE '"fileLockConfiguration":\{[^}]*\{[^}]*\}[^}]*\}' <<<"$BUCKETS" | head -1 || true)
    echo "fileLockConfiguration: ${FL:-<brak / klucz bez prawa odczytu ustawien kubelka>}"
    OK_LOCK=1
    grep -q  '"isFileLockEnabled":true' <<<"$BUCKETS" || { echo "  ! Object Lock NIE jest wlaczony"; OK_LOCK=0; }
    grep -q  '"mode":"compliance"'      <<<"$BUCKETS" || { echo "  ! tryb NIE jest compliance (albo brak domyslnej retencji)"; OK_LOCK=0; }
    grep -qE '"duration":30'            <<<"$BUCKETS" || { echo "  ! domyslna retencja NIE wynosi 30"; OK_LOCK=0; }
    if [ "$OK_LOCK" = 1 ]; then
        echo "OK: Object Lock = compliance, domyslna retencja 30 dni — chroni pliki."
    else
        echo "UWAGA: Object Lock nie jest poprawnie skonfigurowany — popraw w panelu B2."
        echo "       (samo 'Enabled' bez domyslnej retencji NIE chroni kopii!)"
    fi
fi

echo
echo "=========================================================="
if [ "${LOSOWE:-0}" = 1 ] || [ "${LOSOWA_SOL:-0}" = 1 ]; then
    echo " ZAPISZ TO TERAZ W MENEDZERZE HASEL — nie da sie tego odzyskac:"
    echo
    [ "${LOSOWE:-0}" = 1 ]     && echo "   haslo szyfrujace CRM: $HASLO"
    [ "${LOSOWA_SOL:-0}" = 1 ] && echo "   drugie haslo (sol):   $SOL"
    echo
    echo " Bez tych hasel kopia CRM z B2 jest nie do odczytania. Nikt ich nie"
    echo " odzyska — ani MKL, ani Backblaze."
    echo
fi
echo " Gotowe. Nastepny backup (03:00) wysle dane CRM do B2 automatycznie."
echo " Recznie:  /usr/local/sbin/crm-backup.sh"
echo "=========================================================="
