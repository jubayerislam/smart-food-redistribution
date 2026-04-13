# Smart Food

Smart Food is a Laravel 12 food redistribution platform where donors can post surplus food and receiver organizations can claim and complete pickups.

## Features

- Donor and receiver registration
- MySQL-backed donation marketplace
- Donation claim and pickup completion flow
- Dashboard stats and recent notifications
- Impact page with seeded sample data
- PHPUnit feature tests for core flows

## Tech Stack

- PHP 8.2
- Laravel 12
- MySQL
- Blade
- Tailwind/Vite
- PHPUnit 11

## Local Setup

1. Install dependencies:

```bash
composer install
npm install
```

2. Copy env and generate key if needed:

```bash
copy .env.example .env
php artisan key:generate
```

3. Set MySQL credentials in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smart_food
DB_USERNAME=root
DB_PASSWORD=
```

4. Prepare the database:

```bash
php artisan migrate:fresh --seed
php artisan optimize:clear
```

5. Run the app:

```bash
php artisan serve
npm run dev
```

## Demo Seed Accounts

These are created by `ImpactSeeder`:

- Donor: `donor@example.com` / `password`
- Receiver: `receiver@example.com` / `password`

The default Breeze registration flow can also be used to create fresh accounts.

## Testing

Run the full suite:

```bash
php artisan test
```

Run only the main flow tests:

```bash
php artisan test tests/Feature/Auth/RegistrationTest.php tests/Feature/ProfileTest.php tests/Feature/DonationFlowTest.php
```

## Notes

- The project is configured to run on MySQL, not SQLite.
- A Windows-safe filesystem fallback is registered during testing to avoid Blade compiled-view rename lock issues.
- Session, queue, and cache are currently database-backed in the main app environment.

## Important Paths

- Home page: `resources/views/home.blade.php`
- Marketplace controller: `app/Http/Controllers/DonationController.php`
- Dashboard controller: `app/Http/Controllers/DashboardController.php`
- Seeders: `database/seeders`
- Tests: `tests/Feature`
