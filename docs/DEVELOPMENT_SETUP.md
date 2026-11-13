# Development Setup

## Requirements

- PHP 8.2 or newer in the Composer constraint; the project has been exercised
  with PHP 8.3.
- Composer.
- MariaDB/MySQL and the PHP MySQLi extension for the configured database.
- CodeIgniter-required PHP extensions.
- Ubuntu/WSL or another supported PHP environment.

The repository currently has a local `.env` file, which is ignored by Git.
Keep its database credentials and encryption key private. If setting up a new
checkout, copy `env` to `.env` and configure the local base URL and database
connection there.

## Install and run

From `~/projects/membley-security`:

```sh
composer install
php spark migrate:status
php spark serve --host 127.0.0.1 --port 8080
```

The core migration is already applied in the current development database.
Only run pending migrations after reviewing their changes.

## Tests

```sh
composer test
```

The configured database tests use SQLite in memory and require the PHP
`sqlite3` extension. PHPUnit's configured coverage report also requires a
coverage driver such as Xdebug or PCOV. See [Testing](TESTING.md) for the
current local verification results.
