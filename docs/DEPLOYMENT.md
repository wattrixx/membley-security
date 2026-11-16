# Ubuntu staging deployment

These are **manual, unexecuted** instructions for Ubuntu 24.04, Apache,
PHP 8.3 and MySQL 8.0. No server has been provisioned or verified. First
obtain the existing GitHub repository URL, server access, a staging DNS name
pointing to the server, and an HTTPS certificate. Do not expose real
submissions in staging. MySQL 8.0 migration and runtime compatibility must
be verified there; local testing used MariaDB and SQLite.

Run the commands below over SSH as a non-root user with `sudo`. They assume
the server is dedicated to this staging site; review Apache/firewall changes
first if it is shared. Replace requested values with verified values. Never
paste credentials into Git or shell history. Keep staging behind HTTP Basic
authentication, use HTTPS and limit network access where possible.

## 1. Install and obtain the code

```sh
sudo apt update
sudo apt install -y git composer apache2 libapache2-mod-php8.3 \
  php8.3-cli php8.3-mysql php8.3-intl php8.3-mbstring php8.3-xml \
  php8.3-curl php8.3-gd php8.3-zip unzip certbot apache2-utils
php -v
php -m
sudo a2enmod rewrite ssl
```

Provide a MySQL 8.0 database and a dedicated least-privilege application user
with schema migration permissions. Provision these securely through your
MySQL administrator; do not put a database password on a command line. Do not
use or overwrite an existing production database. Back up any existing
staging database before migration. MySQL can be hosted separately; configure
TLS and network access appropriately for the chosen server.
For a fresh, dedicated local MySQL installation, install `mysql-server` and
create the database and user in an interactive administrator session:

```sh
sudo apt install -y mysql-server
sudo env MYSQL_HISTFILE=/dev/null mysql
```

In the MySQL prompt, replace each placeholder with values you choose. Do not
copy this example literally or paste passwords into chat:

```sql
CREATE DATABASE `YOUR_DB_NAME` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE USER 'YOUR_DB_USER'@'localhost' IDENTIFIED BY 'YOUR_NEW_DB_PASSWORD';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX, DROP, REFERENCES
  ON `YOUR_DB_NAME`.* TO 'YOUR_DB_USER'@'localhost';
EXIT;
```

Use the corresponding database credentials in `.env` below. The interactive
session suppresses MySQL history; protect terminal recordings as well. If the
database is remote or managed, follow the provider's instructions instead.

The GitHub repository must already exist and the server must have authorized
read access. Do not put a token in the URL. The current branch is `master`.
The guard below refuses to overwrite an existing checkout:

```sh
read -r -p "Existing GitHub clone URL (SSH recommended): " REPO_URL
test ! -e /var/www/membley-staging || { echo "Path already exists; stop."; return 1 2>/dev/null || exit 1; }
sudo install -d -o "$USER" -g www-data -m 0750 /var/www/membley-staging
git clone --branch master "$REPO_URL" /var/www/membley-staging
cd /var/www/membley-staging
composer install --no-dev --optimize-autoloader --no-interaction
composer check-platform-reqs --no-dev
```

For private repositories, provision a read-only deploy key before cloning.
If clone fails, diagnose access and clean up only the empty checkout directory
after confirming it contains nothing needed; never overwrite existing data.

## 2. Configure and migrate

The repository ships an example named `env`, **not** `.env.example`.
Create the private environment file once, edit it and restrict access:

```sh
test ! -e .env || { echo ".env already exists; stop."; return 1 2>/dev/null || exit 1; }
cp env .env
chmod 0640 .env
sudo chgrp www-data .env
nano .env
```

Set these entries in `.env`, using the actual staging HTTPS URL (with a
trailing slash) and the dedicated database credentials. Do not leave the
placeholder values:

```ini
CI_ENVIRONMENT = production
app.baseURL = 'https://YOUR_STAGING_HOST/'
app.indexPage = ''
app.forceGlobalSecureRequests = true
database.default.hostname = YOUR_DB_HOST
database.default.database = YOUR_DB_NAME
database.default.username = YOUR_DB_USER
database.default.password = YOUR_DB_PASSWORD
database.default.DBDriver = MySQLi
database.default.port = 3306
publicSubmissions.notificationsEnabled = false
```

Use the actual database port if different. CI's `.env` parser has quoting
rules: quote values containing special characters as needed and verify the
connection without printing secrets. Generate a unique encryption key only
for this new installation; **never regenerate an existing deployment's key**
without a key-rotation plan:

```sh
php spark key:generate
sudo install -d -o www-data -g www-data -m 0770 \
  writable/cache writable/logs writable/session writable/uploads \
  writable/debugbar public/uploads/media
sudo chgrp -R www-data writable public/uploads/media
sudo chmod -R g+rwX writable public/uploads/media
sudo -u www-data php spark migrate:status
sudo -u www-data php spark migrate --all
sudo -u www-data php spark migrate:status
sudo -u www-data php spark routes
```

Review migration output and stop on errors. `--all` includes Shield and
Settings migrations. No application seed is required or provided; do not run
sample seeders in staging. New databases start without CMS content, users,
submissions or uploaded images. Database records and uploaded media are not
included in a Git clone; transfer only approved data with a separate secure,
reviewed backup/restore procedure and preserve existing records.

Create the **first** Super Admin only on a fresh database. Choose a new,
strong password and a verified username/email; do not reuse passwords previously
shared in chat. The command refuses to create another initial Super Admin:

```sh
sudo -u www-data bash
cd /var/www/membley-staging
read -r -p "Admin username: " ADMIN_USERNAME
read -r -p "Admin email: " ADMIN_EMAIL
read -r -s -p "New admin password: " MEMBLEY_INITIAL_ADMIN_PASSWORD
printf '\n'
export MEMBLEY_INITIAL_ADMIN_PASSWORD
php spark admin:provision-superadmin "$ADMIN_USERNAME" "$ADMIN_EMAIL"
unset MEMBLEY_INITIAL_ADMIN_PASSWORD ADMIN_USERNAME ADMIN_EMAIL
exit
```

## 3. Apache and HTTPS

Enter the real staging DNS hostname and staging access username:

```sh
read -r -p "Staging DNS hostname: " STAGING_HOST
read -r -p "Staging access username: " STAGING_USER
test ! -e /etc/apache2/membley-staging.htpasswd || { echo "Password file already exists; stop."; return 1 2>/dev/null || exit 1; }
sudo htpasswd -c /etc/apache2/membley-staging.htpasswd "$STAGING_USER"
```

Only use `htpasswd -c` for the first user; it overwrites the password file.
Do not continue if a site configuration with this name already exists:

```sh
test ! -e /etc/apache2/sites-available/membley-staging.conf || { echo "Site already exists; stop."; return 1 2>/dev/null || exit 1; }
sudo tee /etc/apache2/sites-available/membley-staging.conf >/dev/null <<EOF
<VirtualHost *:80>
    ServerName $STAGING_HOST
    DocumentRoot /var/www/membley-staging/public
    <Directory /var/www/membley-staging/public>
        AllowOverride All
        Options -Indexes +FollowSymLinks
        Require all denied
    </Directory>
    <Location "/.well-known/acme-challenge/">
        Require all granted
    </Location>
</VirtualHost>
EOF
sudo a2ensite membley-staging.conf
sudo apache2ctl configtest
sudo systemctl reload apache2
sudo certbot certonly --webroot -w /var/www/membley-staging/public -d "$STAGING_HOST"
```

Certbot requires valid DNS and inbound port 80. Follow its prompts and stop if
certificate issuance fails. The temporary HTTP virtual host denies site access
except ACME challenges; do not send staging credentials over HTTP. After
issuance, replace only the staging configuration with:

```sh
sudo tee /etc/apache2/sites-available/membley-staging.conf >/dev/null <<EOF
<VirtualHost *:80>
    ServerName $STAGING_HOST
    Redirect permanent / https://$STAGING_HOST/
</VirtualHost>
<VirtualHost *:443>
    ServerName $STAGING_HOST
    DocumentRoot /var/www/membley-staging/public
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/$STAGING_HOST/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/$STAGING_HOST/privkey.pem
    <Directory /var/www/membley-staging/public>
        AllowOverride All
        Options -Indexes +FollowSymLinks
        AuthType Basic
        AuthName "Membley staging"
        AuthUserFile /etc/apache2/membley-staging.htpasswd
        Require valid-user
    </Directory>
</VirtualHost>
EOF
sudo apache2ctl configtest
sudo systemctl reload apache2
sudo certbot renew --dry-run
```

Set `upload_max_filesize=5M` and `post_max_size` above 5 MB in Apache's
PHP 8.3 configuration and reload Apache. Verify that
`public/uploads/media/.htaccess` is honored and scripts in the media directory
cannot execute. If not, disable uploads until equivalent server rules are
proven. Keep the entire project except `public/` outside the document root.

## 4. Verify before release

- Verify HTTPS, certificate renewal, staging Basic authentication, public
  pages, application/enquiry forms, admin login, CSRF, all role boundaries,
  CMS and media uploads. Use synthetic data and a staging database.
- Confirm PHP errors are not public; verify no access to `.env`, `writable/`
  or directory listings, and test that media scripts cannot execute.
- Approve the privacy notice, retention schedule and privacy contact before
  collecting real submissions. Notifications remain disabled until verified
  `email.fromEmail`, `email.fromName`, SMTP transport/credentials and
  `publicSubmissions.collegeRecipient` and
  `publicSubmissions.securityRecipient` are configured. Other optional email
  keys are listed in `env`.
- Back up the database, `.env`, encryption key and uploaded media securely;
  test restoration. Protect backups from public access.
- Local changes are not pushed automatically by Git configuration. To update,
  commit and push locally, then on the server back up, review changes and run
  `git pull --ff-only`, `composer install --no-dev --optimize-autoloader`,
  `sudo -u www-data php spark migrate --all`, and the verification checks.
  Never replace the server `.env`, database or runtime uploads during updates.
