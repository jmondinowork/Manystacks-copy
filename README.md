# Manystacks

Manystacks is a B2B app to manage a company's IT stack: equipment, software licences,
orders and support, all in one place. Admins order hardware and licences, assign them to
collaborators, and keep track of contracts and support tickets. It connects to Microsoft
365, Google Workspace, TD Synnex (ION) and HR tools (Payfit, Lucca) to sync users,
licences and devices.

It's a Laravel backend with an Inertia + Vue 3 frontend.

## Requirements

- PHP 8.1+
- Composer
- Node.js 18+ and npm
- SQLite (default) or MySQL

## Setup

```bash
composer install
npm install

cp .env .env.local   # or create your own .env, then set APP_KEY
php artisan key:generate
```

The external integrations (Microsoft, Google, ION, Sendinblue, SIRH…) read their keys
from `config/api.php`. This file is not committed, so create it and fill in your own
credentials before using those features.

Create the database and load some demo data:

```bash
touch database/database.sqlite
php artisan migrate --seed
```

The seeder creates a demo company with a few collaborators, products, orders and tickets.
You can log in with `admin@manystacks.co` / `password`.

## Running

```bash
npm run dev        # vite dev server
php artisan serve  # app on http://localhost:8000
```

For production, build the assets instead:

```bash
npm run build
```

## Tests

The suite runs on an in-memory SQLite database, so there's nothing to set up:

```bash
php artisan test
```
