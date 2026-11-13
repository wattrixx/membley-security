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

The core, Shield authentication and Settings migrations are applied in the
current development database. Review and apply pending migrations before
running the application in a new environment.

The media upload directory is `public/uploads/media/`. The web-server process
needs permission to create files there. Keep its non-executable server rules
enabled; see [Deployment](DEPLOYMENT.md) before enabling uploads on a host.
To use the full 5 MB application upload limit, configure PHP with
`upload_max_filesize=5M` and `post_max_size` greater than 5 MB (for example
`6M`). PHP may impose a lower server-side limit otherwise.

To provision the first Super Admin in an environment with no existing Super
Admin, provide a strong password through the process environment. Avoid putting
the password in shell history or source files:

```sh
read -s -p "Initial admin password: " MEMBLEY_INITIAL_ADMIN_PASSWORD
printf '\n'
export MEMBLEY_INITIAL_ADMIN_PASSWORD
php spark admin:provision-superadmin <username> <email>
unset MEMBLEY_INITIAL_ADMIN_PASSWORD
```

The command refuses to create another Super Admin if one already exists. It
does not print or persist the password. There is no default admin account.

## Tests

```sh
composer test
```

The configured database tests use SQLite in memory and require the PHP
`sqlite3` extension. PHPUnit's configured coverage report also requires a
coverage driver such as Xdebug or PCOV. See [Testing](TESTING.md) for the
current local verification results.
