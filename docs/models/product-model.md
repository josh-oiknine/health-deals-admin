# Product Model Documentation

## Overview
The `Product` model represents products in the Health Deals Admin system. It manages product information, pricing, categorization, and store associations. Products can be tracked for price changes and associated with deals.

## Properties

| Property | Type | Description | Nullable |
|----------|------|-------------|-----------|
| `id` | int | Unique identifier | Yes |
| `store_id` | int | Associated store ID | No |
| `name` | string | Product name | No |
| `slug` | string | URL-friendly name | No |
| `url` | string | Product URL | No |
| `category_id` | int | Associated category ID | Yes |
| `regular_price` | float | Regular product price | No |
| `sku` | string | Stock Keeping Unit | Yes |
| `upc` | string | Universal Product Code | Yes |
| `is_active` | bool | Product status | No |
| `user_id` | int | Associated user ID | Yes |
| `created_at` | string | Creation timestamp | Yes |
| `updated_at` | string | Last update timestamp | Yes |
| `deleted_at` | string | Soft delete timestamp | Yes |
| `last_checked` | string | Last price check timestamp | Yes |

## Constructor

```php
public function __construct(
    string $name = '',
    string $slug = '',
    string $url = '',
    ?int $category_id = null,
    int $store_id = 0,
    float $regular_price = 0.0,
    ?string $sku = null,
    ?string $upc = null,
    bool $is_active = true,
    ?int $user_id = null
)
```

Creates a new Product instance with the specified properties.

## Methods

### Static Methods

#### `findFiltered(array $filters = [], string $sortBy = 'created_at', string $sortOrder = 'DESC', int $page = 1, int $perPage = 20): array`
Retrieves filtered products with pagination.
- **Parameters**:
  - `$filters` - Array of filter conditions
  - `$sortBy` - Field to sort by
  - `$sortOrder` - Sort direction (ASC/DESC)
  - `$page` - Page number
  - `$perPage` - Items per page
- **Returns**: Array with products and pagination info

#### `findAll(): array`
Retrieves all non-deleted products with related data.
- **Returns**: Array of product data with store, category, and user information

#### `findByStore(int $store_id): array`
Finds products by store ID.
- **Parameters**: `$store_id` - Store ID
- **Returns**: Array of products

#### `findById(int $id): ?array`
Finds a product by its ID with related data.
- **Parameters**: `$id` - Product ID
- **Returns**: Product data array or null if not found

#### `findBySku(string $sku): ?array`
Finds a product by its SKU with related data.
- **Parameters**: `$sku` - Product SKU
- **Returns**: Product data array or null if not found

#### `findBySlug(string $slug): ?array`
Finds a product by its slug.
- **Parameters**: `$slug` - Product slug
- **Returns**: Product data array or null if not found

#### `countBySlug(string $slug): ?int`
Counts products with a specific slug.
- **Parameters**: `$slug` - Product slug
- **Returns**: Count of matching products

#### `countActive(): int`
Counts active products.
- **Returns**: Number of active products

#### `getProductsPerDay(int $days = 7): array`
Gets product creation statistics per day.
- **Parameters**: `$days` - Number of days to look back
- **Returns**: Array of daily product counts

### Instance Methods

#### `save(): bool`
Saves or updates the product in the database.
- **Behavior**:
  - Validates required fields
  - Creates or updates record
  - Maintains timestamps
- **Returns**: true on success, false on failure

#### `softDelete(): bool`
Marks the product as deleted without removing from database.
- **Returns**: true on success, false on failure

#### `getStore(): ?Store`
Gets the associated store object.
- **Returns**: Store object or null if not found

### Getters and Setters
- `getId(): ?int`
- `setId(?int $id): void`
- `getName(): string`
- `setName(string $name): void`
- `getSlug(): string`
- `setSlug(string $slug): void`
- `getUrl(): string`
- `setUrl(string $url): void`
- `getCategoryId(): ?int`
- `setCategoryId(?int $category_id): void`
- `getStoreId(): int`
- `setStoreId(int $store_id): void`
- `getRegularPrice(): float`
- `setRegularPrice(float $regular_price): void`
- `getSku(): ?string`
- `setSku(?string $sku): void`
- `getUpc(): ?string`
- `setUpc(?string $upc): void`
- `getIsActive(): bool`
- `setIsActive(bool $is_active): void`
- `getUserId(): ?int`
- `setUserId(?int $user_id): void`
- `getLastChecked(): ?string`
- `setLastChecked(?string $last_checked): void`

## Database Schema

```sql
CREATE TABLE products (
    id SERIAL PRIMARY KEY,
    store_id INTEGER NOT NULL REFERENCES stores(id),
    category_id INTEGER REFERENCES categories(id),
    user_id INTEGER REFERENCES users(id),
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    url VARCHAR(255) NOT NULL,
    regular_price DECIMAL(10,2) NOT NULL,
    sku VARCHAR(255),
    upc VARCHAR(255),
    is_active BOOLEAN DEFAULT true,
    last_checked TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP,
    UNIQUE(sku, store_id)
);
```

## Relationships

1. **Store**:
   - Many-to-One relationship
   - Each product belongs to one store
   - Foreign key: `store_id`

2. **Category**:
   - Many-to-One relationship
   - Each product can belong to one category
   - Foreign key: `category_id`

3. **User**:
   - Many-to-One relationship
   - Each product can be associated with one user
   - Foreign key: `user_id`

4. **Price History**:
   - One-to-Many relationship
   - Each product can have multiple price history records
   - Referenced by `price_history` table

5. **Deals**:
   - One-to-Many relationship
   - Each product can have multiple deals
   - Referenced by `deals` table

## Usage Examples

### Creating a New Product
```php
$product = new Product(
    'Health Supplement',
    'health-supplement',
    'https://example.com/product',
    1, // category_id
    123, // store_id
    29.99, // regular_price
    'SKU123',
    'UPC123',
    true
);
$product->save();
```

### Finding Products with Filters
```php
$filters = [
    'keyword' => 'vitamin',
    'store_id' => 123,
    'category_id' => 1,
    'is_active' => true
];
$products = Product::findFiltered($filters, 'name', 'ASC', 1, 20);
```

### Updating Product Price
```php
$product = Product::findBySku('SKU123');
if ($product) {
    $product->setRegularPrice(24.99);
    $product->setLastChecked(date('Y-m-d H:i:s'));
    $product->save();
}
```

## Error Handling

The model implements comprehensive error handling:
1. Database errors are logged using `error_log()`
2. Failed operations return `false` or `null`
3. PDO exceptions are caught and handled gracefully
4. Input validation before save operations

## Best Practices

1. **Product Management**:
   - Generate clean, unique slugs
   - Validate URLs and prices
   - Maintain consistent SKU format
   - Track price changes

2. **Data Integrity**:
   - Validate required fields
   - Ensure referential integrity
   - Use soft deletes
   - Maintain audit trail

3. **Performance**:
   - Use efficient queries
   - Implement proper indexes
   - Cache frequent lookups
   - Optimize filtering

## Implementation Details

### Filtering System
- Keyword search across name and SKU
- Store and category filtering
- Active status filtering
- User association filtering
- Price range filtering

### Price Management
- Decimal precision handling
- Price history tracking
- Regular price updates
- Price change validation

### Slug Generation
- URL-friendly format
- Uniqueness checking
- Special character handling
- Length limitations 