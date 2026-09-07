#!/bin/bash
#
# Kopia zapasowa CRM (crm.mkl.pl): baza crm_database, pliki uzytkownikow ze
# storage/app oraz konfiguracja (.env + docker-compose.yml). Bez APP_KEY z .env
# nie odczytasz zaszyfrowanych pol z bazy.
#
# Wzorowane 1:1 na /usr/local/sbin/hrm-backup.sh. Roznica: CRM chodzi w Dockerze,
# wiec dump robimy przez `docker exec crm-db` uzywajac poswiadczen crm_user, ktore
# kontener bazy juz ma w swoim srodowisku (MYSQL_USER/MYSQL_PASSWORD/MYSQL_DATABASE).
# Dzieki temu haslo nie trafia ani do crontaba, ani do listy procesow hosta, ani nie
# musimy parsowac .env (haslo ma znaki specjalne i cudzyslowy).
#
# Kopia lezy lokalnie w /var/backups/crm i — po skonfigurowaniu — trafia
# zaszyfrowana do Backblaze B2 (patrz crm-b2-konfiguruj.sh).
#
# Uruchamiane raz na dobe z crona roota o 03:00. Dziennik: /var/log/crm-backup.log
# Odtwarzanie: patrz /var/backups/crm/JAK-ODTWORZYC.txt
#
set -euo pipefail

APKA=/var/www/crm_mkl
CEL=/var/backups/crm
BAZA=crm_database
KONTENER_DB=crm-db
COMPOSE="$APKA/docker-compose.yml"
DZIS=$(date +%F)

# Ile kopii trzymamy (sztuk, nie dni — liczy sie to, ile sie faktycznie udalo).
ILE_DUMPOW=30
ILE_MIESIECZNYCH=13
ILE_SKANOW=14
# Baza ma 46 tabel; ponizej tego progu dump uznajemy za urwany/niepelny.
MIN_TABEL=40

log() { echo "$(date '+%F %T') $*"; }

# Dwa backupy naraz zrobilyby sobie nawzajem balagan w katalogu tymczasowym.
exec 9>/var/lock/crm-backup.lock
flock -n 9 || { log "POMINIETE: poprzedni backup jeszcze trwa"; exit 0; }

trap 'log "BLAD (linia $LINENO) — backup NIEUKONCZONY"' ERR

mkdir -p "$CEL"/{baza,pliki,konfiguracja}
chmod 700 "$CEL"

# --- baza ---
# Dump przez kontener bazy: poswiadczenia crm_user bierzemy z jego srodowiska,
# MYSQL_PWD zamiast -p, zeby mysqldump nie ostrzegal o hasle na wierszu polecen.
TMP="$CEL/baza/.$BAZA-$DZIS.sql.gz.tmp"
# --no-tablespaces: crm_user nie ma globalnego PROCESS (ma prawa tylko do swojej
# bazy), a tablespace'y i tak nie sa potrzebne do logicznego odtworzenia — bez
# tego mysqldump 8.0 wypisywalby "Access denied ... PROCESS privilege".
docker exec "$KONTENER_DB" sh -c \
    'exec env MYSQL_PWD="$MYSQL_PASSWORD" mysqldump -u"$MYSQL_USER" \
        --single-transaction --quick --routines --triggers --no-tablespaces \
        --default-character-set=utf8mb4 "$MYSQL_DATABASE"' \
    | gzip -9 > "$TMP"

# Uciety dump wyglada jak plik, wiec sprawdzamy gzipa i stopke mysqldumpa.
gzip -t "$TMP"
zcat "$TMP" | tail -5 | grep -q "Dump completed" || { log "BLAD: dump urwany"; rm -f "$TMP"; exit 1; }
TABEL=$(zcat "$TMP" | grep -c "^CREATE TABLE" || true)
[ "$TABEL" -ge "$MIN_TABEL" ] || { log "BLAD: w dumpie tylko $TABEL tabel (min $MIN_TABEL)"; rm -f "$TMP"; exit 1; }

mv "$TMP" "$CEL/baza/$BAZA-$DZIS.sql.gz"
log "baza: $(du -h "$CEL/baza/$BAZA-$DZIS.sql.gz" | cut -f1), tabel: $TABEL"

# Kopia z 1. dnia miesiaca zostaje na rok — twarde dowiazanie, wiec nie
# zajmuje drugi raz miejsca, dopoki dzienna kopia jeszcze istnieje.
if [ "$(date +%d)" = "01" ]; then
    ln -f "$CEL/baza/$BAZA-$DZIS.sql.gz" "$CEL/baza/miesieczna-$BAZA-$DZIS.sql.gz"
fi

# --- pliki uzytkownikow (storage/app) ---
# Migawka na twardych dowiazaniach: pliki, ktore sie nie zmienily, nie zajmuja
# miejsca po raz drugi, a kazdy katalog wyglada jak pelna kopia.
POPRZEDNIA=$(find "$CEL/pliki" -maxdepth 1 -type d -name '20*' | sort | tail -1)
LINK=()
[ -n "$POPRZEDNIA" ] && LINK=(--link-dest="$POPRZEDNIA")

rm -rf "$CEL/pliki/.$DZIS.tmp"
rsync -a --delete "${LINK[@]}" "$APKA/storage/app/" "$CEL/pliki/.$DZIS.tmp/"
rm -rf "$CEL/pliki/$DZIS"
mv "$CEL/pliki/.$DZIS.tmp" "$CEL/pliki/$DZIS"
log "pliki: $(find "$CEL/pliki/$DZIS" -type f | wc -l) plikow"

# --- konfiguracja ---
install -m 600 "$APKA/.env" "$CEL/konfiguracja/env-$DZIS"
if [ -f "$COMPOSE" ]; then
    install -m 600 "$COMPOSE" "$CEL/konfiguracja/docker-compose-$DZIS.yml"
else
    log "UWAGA: brak $COMPOSE — pomijam kopie docker-compose.yml"
fi

# --- sprzatanie: zostawiamy N najnowszych, reszte kasujemy ---
posprzataj() { # katalog wzorzec ile
    find "$1" -maxdepth 1 -name "$2" | sort | head -n "-$3" | tr '\n' '\0' | xargs -0 -r rm -rf
}
posprzataj "$CEL/baza"          "$BAZA-*.sql.gz"            "$ILE_DUMPOW"
posprzataj "$CEL/baza"          "miesieczna-$BAZA-*.sql.gz" "$ILE_MIESIECZNYCH"
posprzataj "$CEL/pliki"         "20*"                       "$ILE_SKANOW"
posprzataj "$CEL/konfiguracja"  "env-*"                     "$ILE_DUMPOW"
posprzataj "$CEL/konfiguracja"  "docker-compose-*.yml"      "$ILE_DUMPOW"

# --- kopia poza serwer (Backblaze B2, szyfrowana po naszej stronie) ---
# Dopoki nie ma skonfigurowanego zdalnego "crm-b2-crypt", ta czesc nic nie robi
# i backup lokalny dziala jak dotad. Konfiguracja: crm-b2-konfiguruj.sh
ZDALNY=crm-b2-crypt
# listremotes czytamy do zmiennej i grepujemy z here-stringa — potok
# "rclone listremotes | grep -q" pod 'set -o pipefail' potrafi falszywie
# zwrocic blad (grep -q konczy wczesniej -> rclone dostaje SIGPIPE), przez co
# wysylka bylaby pomijana mimo istniejacego remotu.
REMOTY=$(rclone listremotes 2>/dev/null || true)
if grep -qx "$ZDALNY:" <<<"$REMOTY"; then
    WYSLANO=1

    # Dump i konfiguracje dokladamy jako nowe pliki — w kubelku zostaje historia.
    rclone copy "$CEL/baza/$BAZA-$DZIS.sql.gz"                  "$ZDALNY:baza/"          --no-traverse || WYSLANO=0
    rclone copy "$CEL/konfiguracja/env-$DZIS"                   "$ZDALNY:konfiguracja/"  --no-traverse || WYSLANO=0
    [ -f "$CEL/konfiguracja/docker-compose-$DZIS.yml" ] && \
        rclone copy "$CEL/konfiguracja/docker-compose-$DZIS.yml" "$ZDALNY:konfiguracja/" --no-traverse || true

    # Pliki uzytkownikow trzymamy w chmurze jako JEDNA aktualna kopia. Historii nie
    # robimy katalogami (w object storage nie ma twardych dowiazan — 14 migawek to
    # byloby 14 x te same dane); daje ja wersjonowanie kubelka razem z Object Lock.
    rclone sync "$APKA/storage/app/" "$ZDALNY:pliki/" || WYSLANO=0

    if [ "$WYSLANO" = 1 ]; then
        date +%s > "$CEL/.ostatnia-wysylka"
        log "wysylka do B2: OK ($(rclone size "$ZDALNY:" 2>/dev/null | tail -1))"
    else
        log "BLAD WYSYLKI do B2 — kopia lokalna jest, poza serwerem NIE MA"
    fi
else
    log "wysylka do B2 pominieta (brak zdalnego $ZDALNY — uruchom crm-b2-konfiguruj.sh)"
fi

# Cicha awaria wysylki jest gorsza niz jej brak, wiec glosno mowimy o zastoju.
if [ -f "$CEL/.ostatnia-wysylka" ]; then
    WIEK=$(( ($(date +%s) - $(cat "$CEL/.ostatnia-wysylka")) / 86400 ))
    [ "$WIEK" -ge 3 ] && log "UWAGA: ostatnia udana wysylka poza serwer byla $WIEK dni temu"
fi

log "gotowe — $CEL zajmuje $(du -sh "$CEL" | cut -f1), wolne na dysku: $(df -h / | awk 'NR==2{print $4}')"
