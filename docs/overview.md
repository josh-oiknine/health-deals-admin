# Health Deals Admin - Project Overview

## Introduction
Health Deals Admin is a web-based administration panel designed for managing health-related products, deals, categories, and blog content. The application is built using modern PHP with a robust MVC architecture, providing a secure and efficient platform for managing health product deals and related data.

## Technology Stack
- **Backend Framework**: PHP 8.2 with Slim Framework 4.x
- **Database**: PostgreSQL
- **Frontend**: Bootstrap 5.3, JavaScript
- **Development Environment**: Vagrant with Ubuntu 22.04 LTS
- **Authentication**: JWT-based with Two-Factor Authentication (2FA)
- **Code Quality**: PHP-CS-Fixer with PSR-12 standards

## Key Features
1. **User Management**
   - Secure authentication with 2FA
   - Role-based access control
   - Password management
   - MFA device management

2. **Product Management**
   - Product CRUD operations
   - Price history tracking
   - UPC code support
   - Product information scraping

3. **Deals Management**
   - Deal creation and management
   - Deal status tracking
   - Deal-product associations

4. **Store Management**
   - Store CRUD operations
   - Store-product relationships

5. **Category Management**
   - Category hierarchy
   - Category-product associations

6. **Blog Management**
   - Blog post creation and editing
   - Rich text content editing
   - SEO optimization support
   - Draft/publish workflow
   - Content preview
   - Author management

## Documentation Structure

### Database Documentation
- [Database Schema](database-schema.md) - Complete database structure and relationships

### Model Documentation
1. [User Model](models/user-model.md) - User account management
2. [Product Model](models/product-model.md) - Product data management
3. [Deal Model](models/deal-model.md) - Deal management and tracking
4. [Store Model](models/store-model.md) - Store information management
5. [Category Model](models/category-model.md) - Category system
6. [Price History Model](models/price-history-model.md) - Price tracking system
7. [Blog Post Model](models/blog-post-model.md) - Blog content management

### Controller Documentation
1. [Users Controller](controllers/users-controller.md) - User management endpoints
2. [Products Controller](controllers/products-controller.md) - Product management
3. [Deals Controller](controllers/deals-controller.md) - Deal operations
4. [Stores Controller](controllers/stores-controller.md) - Store management
5. [Categories Controller](controllers/categories-controller.md) - Category operations
6. [Dashboard Controller](controllers/dashboard-controller.md) - Dashboard functionality
7. [Auth Controller](controllers/auth-controller.md) - Authentication system
8. [Blog Posts Controller](controllers/blog-posts-controller.md) - Blog management

### View Documentation
1. [Layout and Components](views/layout-and-components.md) - Shared layout and components
2. [Product Views](views/products.md) - Product management interfaces
3. [Deal Views](views/deals.md) - Deal management interfaces
4. [Store Views](views/stores.md) - Store management interfaces
5. [Category Views](views/categories-doc.md) - Category management interfaces
6. [Authentication Views](views/auth-views.md) - Login and authentication interfaces
7. [Dashboard Views](views/dashboard-views.md) - Dashboard and analytics
8. [Settings Views](views/settings-views.md) - System configuration
9. [User Admin Views](views/user-admin-views.md) - User administration
10. [Blog Post Views](views/blog-posts.md) - Blog content management

## Project Structure
```
├── db/                    # Database migrations and seeds
│   ├── migrations/        # Database schema changes
│   └── seeds/            # Seed data for development
├── docs/                  # Project documentation
│   ├── controllers/      # Controller documentation
│   ├── models/           # Model documentation
│   ├── views/            # View documentation
│   └── database-schema.md # Database documentation
│   └── overview.md        # Project overview
├── public/               # Public directory (web root)
│   ├── assets/           # Static assets (CSS, JS, images)
│   └── index.php         # Application entry point
├── src/                  # Application source code
│   ├── Controllers/      # Application controllers
│   ├── Database/         # Database connection management
│   ├── Middleware/       # Application middleware
│   ├── Models/           # Database models
│   ├── Services/         # Business logic services
│   └── routes.php        # Application routes
├── templates/            # View templates
│   ├── auth/            # Authentication templates
│   ├── blog-posts/      # Blog post templates
│   ├── categories/      # Category templates
│   ├── components/      # Shared components
│   ├── dashboard/       # Dashboard templates
│   ├── deals/           # Deals templates
│   ├── layout/          # Layout templates
│   ├── products/        # Product templates
│   ├── settings/        # Settings templates
│   ├── stores/          # Store templates
│   └── users/           # User management templates
├── storage/             # Application storage
│   ├── logs/            # Application logs
│   └── framework/       # Framework storage
├── vendor/              # Composer dependencies
└── .env                 # Environment configuration
```

## Setup Instructions

### Prerequisites
1. Install the following software:
   - VirtualBox (latest version)
   - Vagrant (latest version)
   - Git

### Development Environment Setup
1. Clone the repository
2. Navigate to the project directory
3. Copy `.env.example` to `.env` and configure environment variables
4. Start the Vagrant environment:
   ```bash
   vagrant up
   ```
5. SSH into the Vagrant box:
   ```bash
   vagrant ssh
   ```
6. Install dependencies:
   ```bash
   cd /var/www/health-deals-admin
   composer install
   ```
7. Run database migrations and seeds:
   ```bash
   php vendor/bin/phinx migrate
   php vendor/bin/phinx seed:run
   ```

### Accessing the Application
- Admin Panel: http://localhost:8080
- Database: localhost:5433 (PostgreSQL)
- Redis: localhost:6379

## Development Guidelines

### Code Style
- Follow PSR-12 coding standards
- Use PHP-CS-Fixer for code formatting:
  ```bash
  php vendor/bin/php-cs-fixer fix
  ```

### Database Changes
1. Create new migrations for schema changes:
   ```bash
   php vendor/bin/phinx create MyNewMigration
   ```
2. Apply migrations:
   ```bash
   php vendor/bin/phinx migrate
   ```

### Best Practices
1. **Security**
   - Never commit sensitive data or credentials
   - Use environment variables for configuration
   - Implement proper input validation
   - Follow secure coding practices

2. **Code Organization**
   - Keep controllers thin
   - Place business logic in Services
   - Use Models for database interactions
   - Follow single responsibility principle

3. **Version Control**
   - Write meaningful commit messages
   - Create feature branches for new development
   - Review code before merging

## API Integration
The application provides API endpoints for integration with other services:

1. **Authentication**
   - POST `/api/login` - Obtain JWT token
   - GET `/api/verify-token` - Verify JWT token

2. **Products**
   - GET `/api/products/fetch-info` - Fetch product information
   - POST `/api/products/add` - Add new product
   - GET `/api/products/find` - Search products
   - POST `/api/products/update-price` - Update product price

3. **Deals**
   - GET `/api/deals/fetch-info` - Fetch deal information

## Troubleshooting

### Common Issues
1. **Vagrant Setup Issues**
   - Ensure VirtualBox is properly installed
   - Check if virtualization is enabled in BIOS
   - Verify port conflicts (8080, 5433, 6379)

2. **Database Connection Issues**
   - Verify PostgreSQL service is running
   - Check database credentials in `.env`
   - Ensure migrations are up to date

3. **Application Errors**
   - Check application logs in `storage/logs/`
   - Verify PHP version compatibility
   - Ensure all required PHP extensions are installed

### Service Management
Restart services if needed:
```bash
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
sudo systemctl restart postgresql
sudo systemctl restart redis-server
```

## Support and Resources
- Report issues through the project's issue tracker
- Refer to the Slim Framework documentation: https://www.slimframework.com/docs/v4/
- PostgreSQL documentation: https://www.postgresql.org/docs/ 