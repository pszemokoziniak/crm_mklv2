#!/usr/bin/env bash
# Wdrożenie CRM (crm.mkl.pl) na serwerze. Uruchamiać jako root:
#   /var/www/crm_mkl/deploy-prod.sh
# Artisan, composer i npm zawsze w kontenerze crm-app: tam jest PHP 8.4 i Node,
# a PHP hosta (8.1) nie spełnia wymagań CRM (Laravel 13 wymaga PHP ≥ 8.3).
set -euo pipefail
cd /var/www/crm_mkl

PRZED=$(git rev-parse HEAD)
git pull --ff-only
PO=$(git rev-parse HEAD)

# Obraz przebudowujemy tylko, gdy zmienił się jego przepis. Przebudowa trwa
# i restartuje kontener, a kod i tak jest podpięty z tego katalogu.
if [ "$PRZED" != "$PO" ] && git diff --name-only "$PRZED" "$PO" -- Dockerfile docker-compose.yml .dockerignore | grep -q .; then
    echo "Zmienił się Dockerfile/compose/.dockerignore — przebudowa obrazu."
    docker compose up -d --build
fi

# Zależności PHP tylko wtedy, gdy zmienił się composer.lock. vendor/ jest
# podpięty z hosta i należy do www-data; bez pakietów deweloperskich.
if [ "$PRZED" != "$PO" ] && git diff --name-only "$PRZED" "$PO" -- composer.lock | grep -q .; then
    echo "Zmienił się composer.lock — composer install."
    docker exec -u www-data -e COMPOSER_HOME=/tmp/composer crm-app \
        composer install --no-dev --optimize-autoloader --no-interaction
fi

docker exec -u www-data crm-app php artisan migrate --force

# Pakiety npm tylko wtedy, gdy zmienił się package-lock.json — inaczej front
# budowałby się ze starych node_modules (podpiętych z hosta), niezgodnych z lockiem.
if [ "$PRZED" != "$PO" ] && git diff --name-only "$PRZED" "$PO" -- package-lock.json | grep -q .; then
    echo "Zmienił się package-lock.json — npm ci."
    docker exec -u www-data -e npm_config_cache=/tmp/npm-cache crm-app npm ci --no-audit --no-fund
fi
docker exec -u www-data crm-app npm run production

# NIE route:cache i NIE optimize: trasy z domknięciami (Closure) zapisane
# w cache wywracają cały CRM na błąd 500. Zapisujemy tylko konfigurację.
docker exec -u www-data crm-app php artisan view:clear
docker exec -u www-data crm-app php artisan config:cache
echo "Wdrożone."
