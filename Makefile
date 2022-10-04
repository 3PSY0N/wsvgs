.DEFAULT_GOAL := help

YARN = yarn
SYMFONY = symfony
SF_CONSOLE = $(SYMFONY) console
COMPOSER = $(SYMFONY_BIN) composer
MAKE = make

## —————————— ℹ️ Help ℹ️ ——————————
.PHONY: help
help: ## Get command list
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'


## —————————— ⏯ Project ⏯ ——————————
.PHONY: startsf startyarn start

start: ## Start Dev
	$(MAKE) -j3 startsf startyarn

startsf: ## Start symfony
	$(SYMFONY) server:start

startyarn: ## start webpack in watch mode
	$(YARN) watch

## —————————— 🎵 Symfony 🎵 ——————————
db: ## Build the DB, control the schema validity and check the migration status
	$(SF_CONSOLE) doctrine:cache:clear-metadata
	$(SF_CONSOLE) doctrine:database:create --if-not-exists
	$(SF_CONSOLE) doctrine:schema:drop --force
	$(SF_CONSOLE) doctrine:schema:create
	$(SF_CONSOLE) doctrine:schema:validate

fixtures: db ## Load default fixtures
	$(SF_CONSOLE) doctrine:fixtures:load --no-interaction


## —————————— 🧰 Assets 🧰 ——————————
.PHONY: install

assets: vendor yarnpkg ## Install assets
	@$(YARN) build

vendor: ## Install composer dependencies
	@$(COMPOSER) install

yarnpkg: yarn.lock ## Install yarn dependencies
	@$(YARN) install


## —————————— 🗑️ Cleaning 🗑️ ——————————
clearcache: ## Clear Symfony cache.
	$(SF_CONSOLE) c:c

purgevarlog: ## Delete cache and logs directory
	@rm -rf var/*
	@echo "var/ purged"

purgeassets: ## Delete node_modules and vendor directories
	@rm -rf node_modules/ vendor/
	@echo "Assets purged"

purgebuild: ## Delete public/build/
	@rm -rf public/build/
	@echo "Build Assets purged"

purgeimagecache: ## Delete public/media/cache
	@rm -rf public/media/cache/
	@echo "Image cache purged"

purgeall: purgevarlog purgeassets purgebuild purgeimagecache ## Delete var/ node_modules/ vendor/ public/build/ directories
	@echo "Assets purged"
