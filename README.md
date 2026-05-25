# Tripistry

Tripistry is a small PHP MVC travel booking application that lets travellers browse packages, confirm bookings, and view generated trip intel on their dashboard.

What follows is a compact README organized exactly as requested.

## What it does
- Browse curated travel packages and view destination details from the landing page.
- Book packages (traveller flow) and manage packages (agency flow).
- Generate trip intelligence (itinerary, required gear, local protocols) after booking using Google Generative Language (Gemini) when available.
- If Gemini is unavailable or returns an error, the app deterministically generates a fallback trip intel JSON and still stores it with the booking.

## Requirements
- PHP 7.4 or later
- MySQL or MariaDB
- PHP extensions: `pdo_mysql` (required), `mbstring` (recommended), `curl`, `json`
- A web server (Nginx/Apache) or PHP built-in server for development

## Environment Setup + All Configuration
Follow these steps to configure a development environment and run Tripistry locally.

1) Install system packages (Debian/Ubuntu example):

```bash
sudo apt update
sudo apt install -y php php-cli php-mysql php-xml php-gd php-mbstring php-curl php-zip mysql-client mysql-server
```

2) Database import

```bash
# create DB and user as appropriate, then import
mysql -u root -p < Tripistry_dump.sql
# or use sample population data
mysql -u root -p < PopulateData.sql
```

3) Configure environment variables

- Required for AI features: `GEMINI_API_KEY`. Set it in your shell, php-fpm pool, or via `.env` (optional).

Temporary (current shell):

```bash
export GEMINI_API_KEY="your_real_key_here"
```

Persistent (example):

```bash
echo 'export GEMINI_API_KEY="your_real_key_here"' >> ~/.bashrc
source ~/.bashrc
```

4) Optional `.env` support

PHP does not load `.env` automatically. To use a `.env` file, install `vlucas/phpdotenv` and load it in `public/index.php` before any config reads:

```bash
composer require vlucas/phpdotenv
```

Add to `public/index.php` near the top:

```php
require_once __DIR__ . '/../vendor/autoload.php';
Dotenv\Dotenv::createImmutable(__DIR__ . '/..')->safeLoad();
```

Create a `.env` (DO NOT commit) with keys such as:

```
GEMINI_API_KEY=your_real_key_here
DB_HOST=127.0.0.1
DB_USER=tripistry_user
DB_PASS=secret
DB_NAME=tripistry
```

5) Web server (development)

```bash
cd public
php -S localhost:8000
```

For production, set `public/` as your document root and provide the `GEMINI_API_KEY` to the php-fpm process (pool config) or the service environment.

6) File permissions

```bash
sudo chown -R www-data:www-data logs/
sudo chmod -R 750 logs/
```

7) Recommended php.ini settings

```
memory_limit = 256M
post_max_size = 20M
upload_max_filesize = 10M
date.timezone = "UTC"
```

8) Quick environment checks

```bash
php -v
php -m | grep pdo_mysql
php -r "echo getenv('GEMINI_API_KEY');"
php -l src/Controllers/BookingController.php
```

If the `GEMINI_API_KEY` check prints nothing, set the var via one of the listed methods or load it via `.env` as shown.

## Traveller and Agency Flow
This section explains the common user flows and where AI integration happens.

- Traveller booking flow (high level):
  1. Traveller selects a package and completes booking from the UI.
  2. `src/Controllers/BookingController.php` creates the booking row in the `bookings` table.
  3. The controller attempts to call Gemini (if `GEMINI_API_KEY` configured) to generate `prep_notes` JSON containing:
     - `itinerary` (array of day/activities),
     - `packing_list` (array of items),
     - `etiquette` (short local protocols/tips).
  4. If the Gemini request fails or returns unusable data, the controller uses a deterministic fallback generator and still writes a valid JSON blob into `bookings.prep_notes`.
  5. Traveller dashboard reads `prep_notes` and renders Itinerary, Required Gear, and Local Protocols.

- Agency flow (high level):
  - Agencies can create/manage packages via `src/Views/agency/*` views and corresponding controllers.
  - Package changes do not automatically regenerate `prep_notes` for existing bookings; `prep_notes` is generated at booking time.

Notes:
- The app logs AI errors to `logs/tripistry_debug.log`. Common failures include API key errors (403) and service availability (503); the fallback ensures bookings remain usable.

## Folder Structure
Top-level layout of the project (relevant files and folders):

```
PopulateData.sql
Tripistry_dump.sql
README.md
config/
    database.php
    secrets.php
logs/
public/
    index.php
    css/StyleGuide.css
    images/
    js/
src/
    Controllers/
    Models/
    Views/
```

See the repository for the full, exact layout — the `src/Views` and `src/Controllers` folders contain the page templates and business logic respectively.

---

If you'd like, I can also:
- add a `.env.example` file with the environment variables (safe to commit), or
- add a short troubleshooting checklist or CI/run scripts.


