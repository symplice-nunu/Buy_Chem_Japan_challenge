# Japan Challenge

A web application built with Laravel 12.x, designed to [brief description of your project's purpose].

## Requirements

- PHP 8.2 or higher
- Composer
- PostgreSQL

## Setup Instructions

1. Clone the repository:
```bash
git clone https://github.com/symplice-nunu/Buy_Chem_Japan_challenge.git
cd Buy_Chem_Japan_challenge
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install JavaScript dependencies:
```bash
npm install
```

4. Set up your environment file:
```bash
cp .env.example .env
php artisan key:generate
```

5. Configure your database in the `.env` file:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=jpc
DB_USERNAME=symplice
DB_PASSWORD=symplice
```

6. Run database migrations:
```bash
php artisan migrate
```

7. Start the development server:
```bash
composer run dev
```

This will start:
- Laravel development server
- Queue worker
- Log viewer

## Development

The project includes several helpful commands:

- `composer run dev` - Start all development servers

## Features

[List the main features of your application]

## Tech Stack

- **Framework:** Laravel 12.x
- **Authentication:** Laravel Sanctum

//readme