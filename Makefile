# Makefile readme (en): <https://www.gnu.org/software/make/manual/html_node/index.html#SEC_Contents>

SHELL = /bin/bash

APP_CONTAINER_NAME := api-users-app
NGINX_CONTAINER_NAME := api-users-nginx

.PHONY: help
help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

.DEFAULT_GOAL := help

# === Docker commands ===

up: ## Start all containers
	docker compose up -d

build: ## Build and start containers (auto-setup)
	docker compose up -d --build

build-app: ## Rebuild only app container
	docker-compose build app
	docker-compose up -d app

build-nginx: ## Rebuild only nginx container
	docker compose build nginx
	docker compose up -d nginx

down: ## Stop all containers
	docker compose down

restart: down up ## Restart all containers

logs: ## Show logs from all containers
	docker compose logs -f

logs-app: ## Show logs from app container
	docker compose logs -f app

logs-nginx: ## Show logs from nginx container
	docker compose logs -f nginx

# === Shell access ===

php: ## Enter php container
	@docker exec -w /var/www/html -it $(APP_CONTAINER_NAME) bash

php-root: ## Enter php container as root
	@docker exec -u root -w /var/www/html -it $(APP_CONTAINER_NAME) bash

init-db: ## Run migrations and load fixtures
	@echo "Running migrations..."
	@docker exec -w /var/www/html -it $(APP_CONTAINER_NAME)  php bin/console doctrine:migrations:migrate --no-interaction
	@echo "Loading fixtures..."
	@docker exec -w /var/www/html -it $(APP_CONTAINER_NAME)  php bin/console doctrine:fixtures:load --no-interaction

nginx: ## Enter nginx container
	@docker exec -it $(NGINX_CONTAINER_NAME) bash
