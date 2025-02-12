# Health Deals Admin Panel

This is an administration panel for managing health deals, products, and categories. The application is built using PHP with a modern MVC architecture.

## Project Architecture

### Directory Structure
```
├── db/                    # Database migrations and seeds
│   ├── migrations/        # Database schema changes
│   └── seeds/            # Seed data for development
├── public/               # Public directory (web root)
├── src/                  # Application source code
│   ├── Controllers/      # Application controllers
│   ├── Models/          # Database models
│   └── routes.php       # Application routes
├── templates/            # View templates
├── storage/             # Application storage (logs, cache)
├── vendor/              # Composer dependencies
└── .env                 # Environment configuration
```

### Key Components

- **MVC Architecture**: The project follows the Model-View-Controller pattern
  - Models: Located in `src/Models/`
  - Views: Located in `templates/`
  - Controllers: Located in `src/Controllers/`
- **Database**: Uses PostgreSQL with Phinx for migrations
- **Development Environment**: Vagrant-based development environment
- **Code Quality**: PHP-CS-Fixer for code style consistency

## Setup Instructions

### Prerequisites

- Vagrant
- VirtualBox
- PHP 8.2 or higher
- Composer 2

### Initial Setup

1. Clone the repository
2. Start the Vagrant environment:
   ```bash
   vagrant up
   ```

### Database Setup

Inside the Vagrant environment, run the following commands:

1. Run database migrations:
   ```bash
   vagrant ssh
   cd /var/www/health-deals-admin
   php vendor/bin/phinx migrate
   ```

2. Seed the database:
   ```bash
   vagrant ssh
   cd /var/www/health-deals-admin
   php vendor/bin/phinx seed:run
   ```

### Development Commands

#### Database Operations
```bash
# Run migrations
php vendor/bin/phinx migrate

# Create a new migration
php vendor/bin/phinx create MyNewMigration

# Run seeds
php vendor/bin/phinx seed:run

# Run a specific seeder
php vendor/bin/phinx seed:run -s CategorySeeder
```

#### Code Style
```bash
# Fix code style
php vendor/bin/php-cs-fixer fix

# Fix code style for a specific directory
php vendor/bin/php-cs-fixer fix /var/www/health-deals-admin/src/

# Fix code style for a specific file
php vendor/bin/php-cs-fixer fix /var/www/health-deals-admin/src/Controllers/ProductsController.php
```

#### Service Management
```bash
# Restart services (nginx, php-fpm)
./restart-services.sh
```

### Accessing the Application

After setup, you can access the application at:
- Admin Panel: http://localhost:8080

## Development Guidelines

1. Always create migrations for database changes
2. Run PHP-CS-Fixer before committing code
3. Follow PSR-12 coding standards
4. Keep controllers thin and move business logic to models
5. Use meaningful commit messages

## Troubleshooting

If you encounter issues:

1. Ensure all services are running:
   ```bash
   vagrant ssh
   sudo systemctl status nginx
   sudo systemctl status php-fpm
   ```

2. Check logs:
   - Nginx logs: `/var/log/nginx/`
   - PHP-FPM logs: `/var/log/php-fpm/`
   - Application logs: `storage/logs/`

3. Restart services:
   ```bash
   ./restart-services.sh
   ``` 