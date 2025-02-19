# Deal Model Documentation

## Overview
The `Deal` model represents product deals in the Health Deals Admin system. It manages deal information, including pricing, product associations, and deal status (active, featured, expired).

## Properties

| Property | Type | Description | Nullable |
|----------|------|-------------|-----------|
| `id` | int | Unique identifier | Yes |
| `product_id` | int | Associated product ID | No |
| `store_id` | int | Associated store ID | No |
| `category_id` | int | Associated category ID | Yes |
| `title` | string | Deal title | No |
| `description` | string | Deal description | No |
| `affiliate_url` | string | Affiliate link URL | No |
| `image_url` | string | Deal image URL | No |
| `original_price` | float | Original product price | No |
| `deal_price` | float | Deal price | No |
| `is_active` | bool | Deal status | No |
| `is_featured` | bool | Featured status | No |
| `is_expired` | bool | Expiration status | No |
| `created_at` | DateTime | Creation timestamp | Yes |
| `updated_at` | DateTime | Last update timestamp | Yes |

## Constructor

```php
public function __construct(
    string $title,
    string $description,
    string $affiliate_url,
    string $image_url,
    int $product_id,
    int $store_id,
    ?int $category_id,
    float $original_price,
    float $deal_price,
    bool $is_active = true,
    bool $is_featured = false,
    bool $is_expired = false
)
```

Creates a new Deal instance with the specified properties. Validates required fields and price values.

## Methods

### Static Methods

#### `findFiltered(array $filters, string $sortBy = 'created_at', string $sortOrder = 'DESC', int $page = 1, int $perPage = 20): array`
Retrieves filtered deals with pagination.
- **Parameters**:
  - `$filters` - Array of filter conditions
  - `$sortBy` - Field to sort by
  - `$sortOrder` - Sort direction (ASC/DESC)
  - `$page` - Page number
  - `$perPage` - Items per page
- **Returns**: Array with deals and pagination info

### Instance Methods

#### `save(): bool`
Saves or updates the deal in the database.
- **Behavior**:
  - For new deals: Creates new record
  - For existing deals: Updates fields
  - Maintains timestamps
- **Returns**: true on success, false on failure

#### `setId(int $id): void`
Sets the deal ID.
- **Parameters**: `$id` - Deal ID

## Database Schema

```sql
CREATE TABLE deals (
    id SERIAL PRIMARY KEY,
    store_id INTEGER NOT NULL REFERENCES stores(id),
    product_id INTEGER NOT NULL REFERENCES products(id),
    category_id INTEGER REFERENCES categories(id),
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    deal_price DECIMAL(10,2) NOT NULL,
    original_price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    affiliate_url VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT true,
    is_featured BOOLEAN DEFAULT false,
    is_expired BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Relationships

1. **Product**:
   - Many-to-One relationship
   - Each deal belongs to one product
   - Foreign key: `product_id`

2. **Store**:
   - Many-to-One relationship
   - Each deal belongs to one store
   - Foreign key: `store_id`

3. **Category**:
   - Many-to-One relationship
   - Each deal can belong to one category
   - Foreign key: `category_id`

## Validation

### Required Fields
- Title
- Description
- Affiliate URL
- Store ID
- Product ID
- Original Price
- Deal Price

### Price Validation
- Original price must be non-negative
- Deal price must be non-negative
- Deal price should be less than original price

## Usage Examples

### Creating a New Deal
```php
$deal = new Deal(
    'Summer Health Sale',
    'Get 30% off on vitamins',
    'https://example.com/affiliate',
    'https://example.com/image.jpg',
    123, // product_id
    456, // store_id
    789, // category_id
    29.99, // original_price
    19.99, // deal_price
    true, // is_active
    true, // is_featured
    false // is_expired
);
$deal->save();
```

### Finding Deals with Filters
```php
$filters = [
    'keyword' => 'vitamin',
    'store_id' => 456,
    'category_id' => 789,
    'is_active' => true,
    'is_featured' => true
];
$deals = Deal::findFiltered($filters, 'created_at', 'DESC', 1, 20);
```

## Error Handling

The model implements comprehensive error handling:
1. Input validation with specific error messages
2. Database errors are logged and re-thrown
3. Price validation checks
4. Required field validation

## Best Practices

1. **Deal Management**:
   - Validate all URLs
   - Ensure price accuracy
   - Maintain image quality
   - Track deal performance

2. **Status Management**:
   - Active status for visibility
   - Featured status for promotion
   - Expired status for lifecycle
   - Status transitions

3. **Performance**:
   - Efficient filtering
   - Proper indexing
   - Optimized queries
   - Caching strategies

## Implementation Details

### Status Types

1. **Active Status** (`is_active`):
   - Controls deal visibility
   - Used for temporary suspension
   - Default: true
   - Affects listing filters

2. **Featured Status** (`is_featured`):
   - Highlights special deals
   - Used for promotions
   - Default: false
   - Affects sorting/display

3. **Expired Status** (`is_expired`):
   - Marks completed deals
   - Used for archiving
   - Default: false
   - Affects availability

### Price Management
- Decimal precision (10,2)
- Price comparison logic
- Savings calculation
- Price history tracking

### URL Management
- Affiliate link validation
- Image URL validation
- URL format standardization
- Link tracking support

### Filtering System
- Keyword search in title/description
- Store and category filtering
- Status-based filtering
- Price range filtering
- Date range filtering 