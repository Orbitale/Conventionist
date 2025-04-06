SHELL=bash

_WARN := "\033[33m[%s]\033[0m %s\n"  # Yellow text for "printf"
_TITLE := "\033[32m[%s]\033[0m %s\n" # Green text for "printf"
_ERROR := "\033[31m[%s]\033[0m %s\n" # Red text for "printf"

PHP_CS_FIXER := docker run --rm -v $$(pwd):/code ghcr.io/php-cs-fixer/php-cs-fixer:3-php8.3

##
## General
## -------
##

.DEFAULT_GOAL := help
help: ## Show this help message
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-18s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help

install: docker start vendor db test-db assets cs-install ## Install the project and start it
.PHONY: install

restart: stop start ## Restart the project
.PHONY: start

docker:
	@docker compose pull --include-deps
	@docker compose build --force-rm --compress
.PHONY: docker

start: ## Start the project
	symfony server:start --daemon
	docker compose up --detach --wait
	-symfony local:run --daemon symfony console messenger:consume --all -vvv
.PHONY: start

stop: ## Stop the project
	@symfony server:stop
	@docker compose stop
.PHONY: stop

##
## Development
## -----------
##

vendor: ## Install composer dependencies
	@symfony composer install
.PHONY: vendor

# The CI environment variable can be set to a non-empty string,
# it'll bypass this command that will "return true", as a "yes" answer.
confirm:
	@if [[ -z "$(CI)" ]]; then \
		REPLY="" ; \
		read -p "⚠ Are you sure? [y/n] > " -r ; \
		if [[ ! $$REPLY =~ ^[Yy]$$ ]]; then \
			printf $(_ERROR) "KO" "Stopping" ; \
			exit 1 ; \
		else \
			printf $(_TITLE) "OK" "Continuing" ; \
			exit 0; \
		fi \
	fi
.PHONY: confirm

reset-project: ## Remove everything: containers, cache, data, unversioned/ignored files...
	@if $(MAKE) -s confirm 2>/dev/null ; then \
		git clean -ndx -e .idea -e .localdev ; \
		git clean -fdx -e .idea -e .localdev ; \
		docker compose down --volumes --remove-orphans --force ; \
	else \
		echo "Nothing to do" ; \
	fi
.PHONY: reset-project

db: ## Recreate development database and its schema
	@symfony console --env=dev doctrine:database:drop --no-interaction --if-exists --force
	@symfony console --env=dev doctrine:database:create --no-interaction --if-not-exists
	@symfony console --env=dev doctrine:migrations:migrate --no-interaction
	@$(MAKE) fixtures
.PHONY: db

fixtures: ## Add default data to the development database
	@symfony console --env=dev doctrine:fixtures:load --no-interaction
.PHONY: fixtures

assets: ## Install all assets
	@git clean -fdx public/assets public/bundles assets/vendor
	@symfony console assets:install
	@symfony console importmap:install
	@symfony console asset-map:compile
.PHONY: assets

cc: ## Clear the cache (without warming it up)
	@symfony console cache:clear --no-warmup
.PHONY: cc

cs-install:
	@$(PHP_CS_FIXER) --version
.PHONY: cs-install

cs: ## Runs php-cs-fixer
	@$(PHP_CS_FIXER) fix
.PHONY: cs

##
## Testing
## -------
##

test: ## Run PHPUnit tests
	@symfony php bin/phpunit
.PHONY: test

coverage: ## Run PHPUnit tests, but with code coverage
	@XDEBUG_MODE=coverage symfony php bin/phpunit --coverage-clover=.phpunit.cache/coverage.xml --coverage-html=.phpunit.cache/coverage/
.PHONY: test

test-db: ## Recreate automated testing database with default data
	@symfony console --env=test doctrine:database:drop --no-interaction --if-exists --force
	@symfony console --env=test doctrine:database:create --no-interaction --if-not-exists
	@symfony console --env=test doctrine:migrations:migrate --no-interaction
	@symfony console --env=test doctrine:fixtures:load --no-interaction
.PHONY: test-db

##
