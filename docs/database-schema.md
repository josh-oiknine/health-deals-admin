# Database Schema and Relationships

## Overview
The Health Deals Admin application uses PostgreSQL as its primary database. The schema is designed to support the management of health products, deals, stores, and categories, with features for price tracking and user management.

## Tables and Relationships

### Users
Stores administrator information with 2FA support.

**Table: `users`**
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | integer | PK | Primary key |
| `email` | string(255) | UNIQUE, NOT NULL | User's email address |
| `password` | string(255) | NOT NULL | Hashed password |
| `first_name` | string(100) | NOT NULL | User's first name |
| `last_name` | string(100) | NOT NULL | User's last name |
| `totp_secret` | string | NULL | TOTP secret for 2FA |
| `totp_setup_complete` | boolean | DEFAULT false | Whether 2FA setup is complete |
| `last_mfa_at` | timestamp | NULL | Last MFA verification timestamp |
| `is_active` | boolean | DEFAULT true | User account status |
| `created_at` | timestamp | DEFAULT CURRENT_TIMESTAMP | Record creation time |
| `updated_at` | timestamp | DEFAULT CURRENT_TIMESTAMP | Record update time |
| `deleted_at` | timestamp | NULL | Soft delete timestamp |

### Stores
Represents retail stores or online marketplaces.

**Table: `stores`**
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | integer | PK | Primary key |
| `name` | string(255) | NOT NULL | Store name |
| `logo_url` | string(1024) | NULL | URL to store logo |
| `url` | string(1024) | NULL | Store website URL |
| `is_active` | boolean | DEFAULT true | Store status |
| `created_at` | timestamp | DEFAULT CURRENT_TIMESTAMP | Record creation time |
| `updated_at` | timestamp | DEFAULT CURRENT_TIMESTAMP | Record update time |
| `deleted_at` | timestamp | NULL | Soft delete timestamp |

### Categories
Product categories hierarchy.

**Table: `categories`**
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | integer | PK | Primary key |
| `name` | string(100) | NOT NULL | Category name |
| `slug` | string(100) | UNIQUE, NOT NULL | URL-friendly name |
| `color` | string(7) | DEFAULT '#6c757d' | Hex color code for category badge |
| `is_active` | boolean | DEFAULT true | Category status |
| `created_at` | timestamp | DEFAULT CURRENT_TIMESTAMP | Record creation time |
| `updated_at` | timestamp | DEFAULT CURRENT_TIMESTAMP | Record update time |

### Products
Core product information.

**Table: `products`**
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | integer | PK | Primary key |
| `name` | string(255) | NOT NULL | Product name |
| `slug` | string(255) | UNIQUE, NOT NULL | URL-friendly name |
| `url` | string(1024) | NOT NULL | Product URL |
| `category_id` | integer | FK, NULL | Reference to categories.id |
| `store_id` | integer | FK, NOT NULL | Reference to stores.id |
| `user_id` | integer | FK, NULL | Reference to users.id |
| `regular_price` | decimal(10,2) | NOT NULL | Regular product price |
| `sku` | string(50) | UNIQUE, NULL | Stock keeping unit |
| `upc` | string(12) | NULL | Universal Product Code |
| `is_active` | boolean | DEFAULT true | Product status |
| `last_checked` | datetime | NULL | Last price check timestamp |
| `created_at` | timestamp | DEFAULT CURRENT_TIMESTAMP | Record creation time |
| `updated_at` | timestamp | DEFAULT CURRENT_TIMESTAMP | Record update time |
| `deleted_at` | datetime | NULL | Soft delete timestamp |

### Deals
Product deals and promotions.

**Table: `deals`**
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | integer | PK | Primary key |
| `store_id` | integer | FK, NOT NULL | Reference to stores.id |
| `product_id` | integer | FK, NOT NULL | Reference to products.id |
| `category_id` | integer | FK, NULL | Reference to categories.id |
| `title` | string(255) | NOT NULL | Deal title |
| `description` | text | NULL | Deal description |
| `deal_price` | decimal(10,2) | NOT NULL | Deal price |
| `original_price` | decimal(10,2) | NOT NULL | Original price |
| `image_url` | string(1024) | NULL | Deal image URL |
| `affiliate_url` | string(1024) | NOT NULL | Affiliate link URL |
| `is_featured` | boolean | DEFAULT false | Featured deal flag |
| `is_expired` | boolean | DEFAULT false | Deal expiration status |
| `is_active` | boolean | DEFAULT true | Deal status |
| `created_at` | timestamp | DEFAULT CURRENT_TIMESTAMP | Record creation time |
| `updated_at` | timestamp | DEFAULT CURRENT_TIMESTAMP | Record update time |

### Price History
Historical price tracking for products.

**Table: `price_history`**
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | integer | PK | Primary key |
| `product_id` | integer | FK, NOT NULL | Reference to products.id |
| `price` | decimal(10,2) | NOT NULL | Recorded price |
| `created_at` | datetime | NULL | Price record timestamp |

### Scraping Jobs
Manages product price scraping tasks.

**Table: `scraping_jobs`**
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | integer | PK | Primary key |
| `product_id` | integer | FK, NOT NULL | Reference to products.id |
| `job_type` | string(20) | DEFAULT 'hourly' | Job frequency type |
| `status` | string(20) | DEFAULT 'pending' | Job status |
| `started_at` | datetime | NULL | Job start timestamp |
| `completed_at` | datetime | NULL | Job completion timestamp |
| `error_message` | text | NULL | Error details if failed |
| `celery_task_id` | string(255) | NULL | Associated Celery task ID |
| `created_at` | datetime | NULL | Record creation time |
| `updated_at` | datetime | NULL | Record update time |

### Outbox
Message queue for notifications and external communications.

**Table: `outbox`**
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | integer | PK | Primary key |
| `text_description` | text | NOT NULL | Plain text message |
| `html_description` | text | NOT NULL | HTML formatted message |
| `image_url` | string(1024) | NULL | Associated image URL |
| `status` | string(20) | DEFAULT 'pending' | Message status |
| `error_data` | json | NULL | Error information |
| `created_at` | timestamp | DEFAULT CURRENT_TIMESTAMP | Record creation time |
| `updated_at` | timestamp | DEFAULT CURRENT_TIMESTAMP | Record update time |

## Relationships

### One-to-Many Relationships
1. Store → Products
   - A store can have multiple products
   - Foreign key: `products.store_id` → `stores.id`
   - Cascade delete

2. Category → Products
   - A category can have multiple products
   - Foreign key: `products.category_id` → `categories.id`
   - Set NULL on delete

3. User → Products
   - A user can create multiple products
   - Foreign key: `products.user_id` → `users.id`
   - Set NULL on delete

4. Product → Price History
   - A product has multiple price history records
   - Foreign key: `price_history.product_id` → `products.id`
   - Cascade delete

5. Product → Scraping Jobs
   - A product has multiple scraping jobs
   - Foreign key: `scraping_jobs.product_id` → `products.id`
   - Cascade delete

### Many-to-One Relationships
1. Deals → Store
   - Each deal belongs to one store
   - Foreign key: `deals.store_id` → `stores.id`
   - Cascade delete

2. Deals → Product
   - Each deal belongs to one product
   - Foreign key: `deals.product_id` → `products.id`
   - Cascade delete

3. Deals → Category
   - Each deal can belong to one category
   - Foreign key: `deals.category_id` → `categories.id`
   - Set NULL on delete

## Indexes
1. `users`: `email` (UNIQUE)
2. `products`: `slug` (UNIQUE), `sku` (UNIQUE), `deleted_at`
3. `categories`: `slug` (UNIQUE)
4. `stores`: `name`
5. `price_history`: `(product_id, created_at)`
6. `scraping_jobs`: `(status, job_type)`, `celery_task_id`
7. `outbox`: `(status, created_at)`

## Soft Deletes
The following tables implement soft delete functionality using a `deleted_at` timestamp:
- `users`
- `stores`
- `products`

When a record is "deleted" in these tables, it is merely marked as deleted by setting the `deleted_at` timestamp, rather than being physically removed from the database. 