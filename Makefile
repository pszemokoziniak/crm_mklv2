.PHONY: setup up down restart build shell composer-install migrate key-generate mysql npm-prod

# Default command
all: up

# Build and start containers
up:
	docker compose up -d & npm run watch

# Stop containers
down:
	docker compose down

# Rebuild and start containers
build:
	docker compose up -d --build

# Restart containers
restart: down up

# Full setup for the first time
setup: build composer-install key-generate migrate npm-prod

# Install composer dependencies
composer-install:
	docker exec -it crm-app composer install

# Generate Laravel application key
key-generate:
	docker exec -it crm-app php artisan key:generate

# Run database migrations
migrate:
	docker exec -it crm-app php artisan migrate

# Run npm production build
npm-prod:
	docker exec -it crm-app npm run production

# Access the app container shell
shell:
	docker exec -it crm-app bash

# Access MySQL CLI
mysql:
	docker exec -it crm-db mysql -u crm_user -proot crm_database

# Run artisan commands (usage: make artisan cmd="migrate:status")
artisan:
	docker exec -it crm-app php artisan $(cmd)
