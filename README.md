# Japan Challenge

A web application built with Laravel 12.x, designed to [brief description of your project's purpose].

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- SQLite/MySQL/PostgreSQL

## Setup Instructions

1. Clone the repository:
```bash
git clone [your-repository-url]
cd japan_challenge
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
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
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
- Vite development server

## Development

The project includes several helpful commands:

- `composer run dev` - Start all development servers
- `composer run test` - Run the test suite
- `./vendor/bin/pint` - Format PHP code
- `npm run dev` - Start Vite development server

## Features

[List the main features of your application]

## Tech Stack

- **Framework:** Laravel 12.x
- **Authentication:** Laravel Sanctum
- **Development Tools:**
  - Laravel Sail (Docker)
  - Laravel Pint (Code formatting)
  - Laravel Pail (Log viewer)
  - Vite (Asset bundling)

## Contributing

[Add contribution guidelines if applicable]

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
