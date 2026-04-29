# Conventionist

Symfony backoffice for managing conventions. Built on EasyAdmin with Doctrine entities for Creators, Permissions, and role-based access.

## Tech stack

- **PHP 8.4** (see `.php-version`)
- **Symfony 7.2** (framework-bundle, security, form, mailer, asset-mapper…)
- **Doctrine ORM 3.5** + DBAL 4.3, MariaDB 11.4 via Docker (see `compose.yaml`)
- **EasyAdmin** (dev branch, path repository at `/var/www/EasyAdminBundle`)
- **Twig** templates + Tailwind (Asset Mapper / importmap, no Node toolchain)
- **PHPUnit 12** with DAMA doctrine-test-bundle, smoke-testing, doctrine-fixtures
- **PHP-CS-Fixer** via Docker container (`ghcr.io/php-cs-fixer/php-cs-fixer:3-php8.3`)
- **Symfony CLI** (`symfony ...`) wraps PHP, composer, server, messenger

## Commands (use the Makefile)

| Command | Purpose |
| --- | --- |
| `make install` | Full bootstrap: docker, vendor, db, test-db, assets, cs |
| `make start` / `make stop` / `make restart` | Lifecycle |
| `make db` | Drop + create dev DB, migrate, load fixtures |
| `make test-db` | Same, for the test env |
| `make fixtures` | Reload dev fixtures |
| `make assets` | Install + compile asset map |
| `make cc` | Clear cache |
| `make cs` | Run PHP-CS-Fixer (Docker) |
| `make test` | PHPUnit via `symfony php bin/phpunit` |
| `make coverage` | PHPUnit with XDEBUG coverage |

Always prefer `symfony console …` over `php bin/console` — the Symfony CLI wires env vars and PHP version correctly.

## Conventions

- PSR-12 + Symfony rules enforced by `.php-cs-fixer.dist.php`. Run `make cs` before committing.
- `App\` autoloads from `src/`, `App\Tests\` from `tests/`.
- Doctrine behaviours (timestampable, sluggable…) come from `stof/doctrine-extensions-bundle`.
- Entities live under `src/Entity/`, repositories under `src/Repository/`, EasyAdmin controllers under `src/Admin/`.
- Authentication uses `symfonycasts/reset-password-bundle` and `symfonycasts/verify-email-bundle`.
- No PHPStan installed — don't propose `vendor/bin/phpstan` unless you also add it to `composer.json`.

## Testing

- PHPUnit config: `phpunit.xml.dist`. Tests live under `tests/` mirroring `src/`.
- DAMA bundle wraps each test in a transaction — fixtures load once per suite.
- Use the `SmokeTesting` trait from `pierstoval/smoke-testing` to smoke-test routes.
- The test DB must exist: run `make test-db` before the first test run.

## Useful paths

- `src/` — application code (Admin, Controller, Entity, Enum, EventListener, Form, Mailer, Repository, Security, Twig, Validator)
- `config/packages/` — per-bundle configuration
- `migrations/` — Doctrine migrations
- `templates/` — Twig templates (grouped by feature)
- `devtools/` — local dev helpers
