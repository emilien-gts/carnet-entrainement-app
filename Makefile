# Executables (local)
DOCKER_COMP = docker compose

# Docker containers
SYM_CONT = $(DOCKER_COMP) exec symfony

# Executables
PHP      = $(SYM_CONT) php
COMPOSER = $(SYM_CONT) composer
YARN	 = $(SYM_CONT) yarn
SYMFONY  = $(PHP) bin/console

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up start down logs sh composer vendor sf cc

## —— 🎵 🐳 The Symfony Docker Makefile 🐳 🎵 ——————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
dc-install: dc-down dc-start db-create db-update vendor yarn-install yarn-build-dev ## Install project

dc-build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

dc-up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

dc-start: dc-build dc-up ## Build and start the containers

dc-down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

dc-logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

dc-sh: ## Connect to the PHP FPM container
	@$(SYM_CONT) sh

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-progress --no-scripts --no-interaction
vendor: composer

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf

db-create: ##  Create database
	@$(SYMFONY) doctrine:database:create --if-not-exists

db-update: ## Update database
	@$(SYMFONY) doctrine:schema:update --force --dump-sql --complete

validate-schema: ## Valid doctrine mapping
	@$(SYMFONY) doctrine:schema:validate --skip-sync

## —— PHP 🐘 ——————————————————————————————————————————————————————————————————

test: ## Test phpunit
	@$(PHP) bin/phpunit tests -v --testdox

analyse-php: ## Analyse php
	@$(SYM_CONT) ./vendor/bin/phpstan analyse -c phpstan.neon

lint-php: ## Lint php
	@$(SYM_CONT) ./vendor/bin/php-cs-fixer fix --dry-run --diff

fix-php: ## Fix php
	@$(SYM_CONT) ./vendor/bin/php-cs-fixer fix

check: fix-php analyse-php validate-schema test
ccheck: cc check

## —— JS 💄 ———————————————————————————————————————————————————————————————————

yarn-install:
	@$(YARN) install

yarn-build-dev:
	@$(YARN) dev

yarn-watch:
	@$(YARN) watch