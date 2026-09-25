#!/usr/bin/env bash
# Wdrożenie CRM (crm.mkl.pl) na serwerze. Uruchamiać jako root:
#   /var/www/crm_mkl/deploy-prod.sh
# Artisan i npm zawsze w kontenerze crm-app: tam jest PHP 8.2 i Node, a PHP
# hosta (8.1) nie spełnia wymagań CRM.
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

docker exec -u www-data crm-app php artisan migrate --force
docker exec -u www-data crm-app npm run production

# NIE route:cache i NIE optimize: trasy z domknięciami (Closure) zapisane
# w cache wywracają cały CRM na błąd 500. Zapisujemy tylko konfigurację.
docker exec -u www-data crm-app php artisan view:clear
docker exec -u www-data crm-app php artisan config:cache
echo "Wdrożone."
