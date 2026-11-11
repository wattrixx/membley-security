# Development Setup

## Requirements
- PHP 8.3 or a compatible supported version
- Composer
- MariaDB/MySQL
- Git
- Ubuntu/WSL or another supported PHP environment

## Installation
1. Install PHP extensions required by CodeIgniter.
2. Install Composer dependencies with `composer install`.
3. Copy `env` to `.env`.
4. Configure the application URL and database credentials.
5. Run database migrations.
6. Start locally using `php spark serve`.

## Local Project Path
`~/projects/membley-security`

## Environment Variables
Keep local and production configuration in their respective environment files.
Never commit secrets to version control.
