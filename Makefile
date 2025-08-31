up: ## Start stack
	docker compose up -d --build

down: ## Stop stack
	docker compose down -v

bash: ## Shell into PHP
	docker compose exec php bash

install: ## Install PHP deps
	docker compose exec php composer install

migrate: ## Run migrations
	docker compose exec php php bin/console doctrine:migrations:migrate -n

keys: ## Generate JWT keypair
	docker compose exec php php bin/console lexik:jwt:generate-keypair --overwrite

tests:
	docker compose exec php vendor/bin/phpunit