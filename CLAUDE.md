# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

BALTC Torneo is a tournament management web application for the BALTC tennis club, served at `https://www.baltc.net/torneo`. Built on **CodeIgniter 3** (PHP MVC framework).

## Development Environment

**Requirements:**
- PHP 5.3.7+
- MySQL (`baltc_db` database, localhost)
- Apache with `mod_rewrite` enabled
- SSL (HTTPS enforced via `.htaccess`)

**Install dependencies:**
```bash
composer install
```

**Run tests (PHPUnit):**
```bash
./vendor/bin/phpunit -c tests/phpunit.xml
# Run a specific test suite:
./vendor/bin/phpunit -c tests/phpunit.xml --testsuite <SuiteName>
```

**Environment:** Set via `CI_ENV` in `.htaccess` (default: `production`). Change to `development` for verbose error output.

## Architecture

Standard CodeIgniter 3 MVC. Entry point is `index.php` (front controller). All requests are routed through Apache rewrite rules to this file.

```
application/
├── config/          # CI config: database.php, routes.php, autoload.php, email.php
├── controllers/     # HTTP handlers — one class per route group
├── models/          # All DB queries live here; also security and utility models
├── views/
│   ├── web/         # Public-facing pages
│   ├── admin/       # Admin panel pages
│   └── email/       # Email templates
├── helpers/         # utilities_helper.php, MY_email_helper.php
└── libraries/       # MY_Email.php (extends CI email library)
static/              # Frontend assets (CSS, JS, fonts, images, vendor libs)
tests/               # PHPUnit tests with VFS mocks
```

### Key Models

- **User.php** — player lookup and session checks
- **Reservation.php** — tournament inscriptions and categories
- **Administrator.php** — admin users and tournament settings
- **Partido_model.php** — match data and bracket management
- **Protect.php** — acts as security middleware: CSRF tokens, request validation, auth checks
- **Common.php** — shared utilities (captcha, date formatting, timezone conversions for `America/Argentina/Buenos_Aires`)

### Key Controllers

- **Login.php** — player auth and registration form
- **Admin.php** — admin dashboard (main admin entry point)
- **Reserva.php** — inscription/reservation logic
- **Draws.php** — bracket generation and display
- **Mipartido.php** — match result reporting by players
- **Menu.php** — main navigation hub

### Routing

Routes are defined in `application/config/routes.php`. Main public routes: `/login`, `/menu`, `/resultados`, `/programacion`, `/draws`, `/mipartido`. Admin routes all start with `/admin`.

### Auto-loaded Resources

Defined in `application/config/autoload.php`: **database**, **session** libraries; **url**, **utilities** helpers.

### Security Pattern

All controllers that need protection load the `Protect` model, which validates CSRF tokens, enforces request method (GET/POST), verifies AJAX headers, and checks session auth. Admin passwords are stored as SHA1.

### Frontend

Bootstrap-based UI. Vendor libraries in `static/vendor/`: Mustache (templating), Select2 (dropdowns), Bootstrap Typeahead (autocomplete), jQuery DataTables.

## Codebase Notes

- Several controllers and models have `_old`, `_ols2`, `_ok` suffixed backup variants — these are dead code.
- Timezone is hardcoded to `America/Argentina/Buenos_Aires` throughout the application.
- The `settings` DB table stores runtime flags (e.g., `inscripciones_abiertas` to open/close registrations).
- Email configuration is in `application/config/email.php`; custom sending logic is in `MY_Email.php` / `MY_email_helper.php`.
