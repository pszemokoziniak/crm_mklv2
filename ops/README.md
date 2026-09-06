# Kopie zapasowe CRM (crm.mkl.pl)

System kopii CRM, wzorowany 1:1 na kopiach HRM działających na tym samym serwerze.
**Źródłem prawdy są te pliki w repo** — na serwer instaluje się je stąd, żeby
wersja serwerowa i repo się nie rozjechały (system kopii, który istnieje tylko na
serwerze, ginie razem z serwerem).

## Pliki

| Plik | Cel na serwerze | Rola |
|---|---|---|
| `crm-backup.sh` | `/usr/local/sbin/crm-backup.sh` | codzienny backup (baza + pliki + konfiguracja), lokalnie i do B2 |
| `crm-b2-konfiguruj.sh` | `/usr/local/sbin/crm-b2-konfiguruj.sh` | jednorazowy kreator poświadczeń B2 (DOPISUJE remoty, nie nadpisuje) |
| `JAK-ODTWORZYC.txt` | `/var/backups/crm/JAK-ODTWORZYC.txt` | instrukcja odtwarzania |
| `logrotate-crm-backup` | `/etc/logrotate.d/crm-backup` | rotacja `/var/log/crm-backup.log` |

- Dump bazy: przez `docker exec crm-db` (poświadczenia `crm_user` z env kontenera —
  hasło nie trafia do crontaba ani do listy procesów hosta).
- Retencja lokalna: 30 dumpów dziennych + 13 miesięcznych + 14 migawek plików.
  Migawki plików na twardych dowiązaniach (`rsync --link-dest`).
- B2: dumpy/konfiguracja jako nowe pliki (`rclone copy`, historia w kubełku),
  pliki użytkowników jako jedna aktualna kopia (`rclone sync`). Szyfrowanie po
  naszej stronie (`rclone crypt`). Kubełek CRM **osobny** od HRM.
- Cron: **03:00** (02:30 zajmuje backup HRM).

## Instalacja / aktualizacja na serwerze (z repo)

Z katalogu repo na maszynie z dostępem `ssh mkl`:

```bash
# 1. skopiuj skrypty na serwer
scp ops/crm-backup.sh ops/crm-b2-konfiguruj.sh mkl:/usr/local/sbin/
scp ops/logrotate-crm-backup mkl:/etc/logrotate.d/crm-backup
scp ops/JAK-ODTWORZYC.txt mkl:/root/JAK-ODTWORZYC.crm.txt   # przeniesiemy do /var/backups/crm

# 2. uprawnienia + rozmieszczenie
ssh mkl 'chmod 700 /usr/local/sbin/crm-backup.sh /usr/local/sbin/crm-b2-konfiguruj.sh
         chown root:root /usr/local/sbin/crm-backup.sh /usr/local/sbin/crm-b2-konfiguruj.sh
         chmod 644 /etc/logrotate.d/crm-backup
         mkdir -p /var/backups/crm && chmod 700 /var/backups/crm
         mv /root/JAK-ODTWORZYC.crm.txt /var/backups/crm/JAK-ODTWORZYC.txt'

# 3. wpis w cronie roota o 03:00 (idempotentnie)
ssh mkl 'crontab -l 2>/dev/null | grep -q crm-backup.sh || \
   (crontab -l 2>/dev/null; echo "0 3 * * * /usr/local/sbin/crm-backup.sh >> /var/log/crm-backup.log 2>&1") | crontab -'

# 4. pierwszy przebieg (bez B2 — zrobi tylko kopię lokalną)
ssh mkl '/usr/local/sbin/crm-backup.sh >> /var/log/crm-backup.log 2>&1; tail -8 /var/log/crm-backup.log'
```

## Włączenie wysyłki do B2

1. W panelu Backblaze B2 (konto istnieje, region us-east-005) wyklikaj wg listy
   poniżej: kubełek + Object Lock + lifecycle + klucz.
2. Na serwerze uruchom kreator (pyta o keyID, applicationKey i hasło szyfrujące —
   podajesz je w terminalu, nie w kodzie):
   ```bash
   ssh mkl -t /usr/local/sbin/crm-b2-konfiguruj.sh
   ```
   Kreator: dopisze remoty `[crm-b2]`/`[crm-b2-crypt]` (nie ruszy HRM), przetestuje
   połączenie i szyfrowanie, zweryfikuje Object Lock przez API. Zapisz wypisane hasła.
3. Następny backup (03:00) wyśle dane do B2 sam; ręcznie: `ssh mkl /usr/local/sbin/crm-backup.sh`.

### Kroki do wyklikania w panelu B2 (dokładnie)

**A. Kubełek**
- Create Bucket → nazwa np. `mkl-crm-backup` (OSOBNY, nie współdzielony z HRM)
- Files in Bucket: **Private**
- **Object Lock: Enable** (przy tworzeniu — później się nie włączy)
- Default Encryption: może zostać domyślne (i tak szyfrujemy po swojej stronie)

**B. Domyślna retencja Object Lock (to jest to, co realnie chroni!)**
- Bucket Settings → Object Lock → **Default Retention Period: 30 days**, **Mode: Compliance**
- Uwaga: samo „Object Lock: Enabled" bez domyślnej retencji NIC nie chroni —
  rclone nie wysyła nagłówka retencji per plik; chroni dopiero domyślna retencja kubełka.

**C. Lifecycle**
- Bucket → Lifecycle Settings → Custom:
  „Keep prior versions for this many days: **35**" (musi być > retencji 30),
  czyli `daysFromHidingToDeleting = 35`, `daysFromUploadingToHiding` puste/none.

**D. Klucz aplikacyjny (App Key)**
- App Keys → Add a New Application Key
- Name: `crm-backup`
- **Allow access to Bucket(s): tylko `mkl-crm-backup`** (nie „All")
- Type of Access: **Read and Write**
- **NIE** zaznaczaj „Allow List All Bucket Names" ani uprawnień bypass Object Lock
- Zapisz **keyID** i **applicationKey** (pokazywane raz)

Po wyklikaniu A–D podaj keyID/applicationKey kreatorowi (krok 2 powyżej).
```

## Weryfikacja, że kopia działa (po włączeniu B2)

- Odtworzenie dumpu do osobnego, tymczasowego kontenera i porównanie liczby
  rekordów z produkcją (patrz `JAK-ODTWORZYC.txt`, KROK 1).
- `sha256sum` pliku pobranego z B2 == oryginał na serwerze.
- `rclone delete --b2-hard-delete crm-b2-crypt:<plik>` **musi się nie udać**
  (dowód, że Object Lock trzyma).
- `rclone listremotes` nadal pokazuje remoty HRM (razem 4).
