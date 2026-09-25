#!/usr/bin/env bash
#
# Zmiana hasła crm_user (użytkownik bazy CRM). Uruchamiać jako root na serwerze:
#
#   /var/www/crm_mkl/rotuj-haslo-bazy.sh
#
# Nowe hasło powstaje tutaj (openssl) i trafia tylko do .env — nie jest nigdzie
# wypisywane ani podawane w argumentach poleceń. Root MySQL nie jest potrzebny:
# crm_user zmienia własne hasło.
#
# Kolejność: kopia .env → ALTER USER → nowe hasło w .env → odtworzenie
# kontenerów (app i db czytają hasło z .env) → restart web → config:cache →
# sprawdzenie.
# Między ALTER USER a config:cache CRM przez kilka sekund nie łączy się z bazą.
#
set -euo pipefail
cd /var/www/crm_mkl

KOPIA=/root/crm-env.przed-rotacja
[ -e "$KOPIA" ] && { echo "Istnieje $KOPIA — poprzednia rotacja nie skończyła się czysto. Sprawdź i usuń ręcznie."; exit 1; }

# Compose musi już czytać hasło z .env, inaczej po odtworzeniu kontenera
# backup dostałby stare hasło.
grep -q 'MYSQL_PASSWORD: ${DB_PASSWORD' docker-compose.yml || { echo "docker-compose.yml nie czyta hasła z .env — najpierw git pull."; exit 1; }

# .env i kontener bazy muszą dziś mieć to samo hasło.
A=$(docker exec crm-app printenv DB_PASSWORD | sha256sum)
B=$(docker exec crm-db printenv MYSQL_PASSWORD | sha256sum)
[ "$A" = "$B" ] || { echo "Hasło w .env różni się od hasła w kontenerze bazy — nic nie zmieniam."; exit 1; }

install -m 600 .env "$KOPIA"
trap 'echo "PRZERWANE. Kopia starego .env: $KOPIA. Sprawdź, czy baza ma już nowe hasło, a .env stare."' ERR
NOWE=$(openssl rand -hex 24)

# SQL idzie przez stdin, więc hasła nie widać na liście procesów.
docker exec -i crm-db sh -c 'MYSQL_PWD="$MYSQL_PASSWORD" mysql -u"$MYSQL_USER"' <<SQL
ALTER USER CURRENT_USER() IDENTIFIED BY '$NOWE';
SQL
echo "Hasło w bazie zmienione."

NOWE="$NOWE" python3 - <<'PY'
import os, re
p = '/var/www/crm_mkl/.env'
s = open(p).read()
s, n = re.subn(r'(?m)^DB_PASSWORD=.*$', 'DB_PASSWORD=' + os.environ['NOWE'], s)
assert n == 1, 'brak dokładnie jednej linii DB_PASSWORD'
open(p, 'w').write(s)
PY
echo "Hasło w .env zmienione."

# Wymuszone odtworzenie: crm-app ma hasło w zmiennych środowiska (env_file),
# a te zmieniają się tylko przy nowym kontenerze.
docker compose up -d --force-recreate app db
# nginx w crm-web zapamiętuje adres IP crm-app z chwili startu; nowy kontener
# app ma nowy adres, więc bez restartu web strona zwraca 502.
docker restart crm-web >/dev/null
sleep 5
docker exec -u www-data crm-app php artisan config:cache 2>&1 | grep -v thecodingmachine || true

echo "--- sprawdzenie"
OK=1
docker exec crm-app php -r '$p = new PDO("mysql:host=".getenv("DB_HOST").";dbname=".getenv("DB_DATABASE"), getenv("DB_USERNAME"), getenv("DB_PASSWORD")); echo "aplikacja → baza: ", $p->query("select count(*) from clients")->fetchColumn(), " klientów\n";' || OK=0
docker exec crm-db sh -c 'MYSQL_PWD="$MYSQL_PASSWORD" mysqldump -u"$MYSQL_USER" --no-tablespaces --no-data "$MYSQL_DATABASE"' >/dev/null && echo "backup → baza: OK" || OK=0
KOD=$(curl -s -o /dev/null -w "%{http_code}" https://crm.mkl.pl/login); echo "strona logowania: $KOD"; [ "$KOD" = 200 ] || OK=0

if [ "$OK" = 1 ]; then
    shred -u "$KOPIA"
    echo "Gotowe. Stare hasło przestało działać, kopia .env usunięta."
else
    echo "COŚ NIE DZIAŁA. Kopia starego .env: $KOPIA (w bazie jest już NOWE hasło, jest ono w obecnym .env)."
    exit 1
fi
