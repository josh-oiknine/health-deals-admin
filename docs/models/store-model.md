# Store Model Documentation

## Overview
The `Store` model represents retail stores or merchants in the Health Deals Admin system. It manages store information, including logos, URLs, and active status.

## Properties

| Property | Type | Description | Nullable |
|----------|------|-------------|-----------|
| `id` | int | Unique identifier | Yes |
| `name` | string | Store name | No |
| `logo_url` | string | URL to store logo | Yes |
| `url` | string | Store website URL | Yes |
| `is_active` | bool | Store status | No |
| `created_at` | string | Creation timestamp | Yes |
| `updated_at` | string | Last update timestamp | Yes |
| `deleted_at` | string | Soft delete timestamp | Yes |

## Constructor

```php
public function __construct(
    string $name = '',
    ?string $logo_url = null,
    ?string $url = null,
    bool $is_active = true
)
```

Creates a new Store instance with the specified properties.

## Methods

### Static Methods

#### `findAll(): array`
Retrieves all non-deleted stores from the database.
- **Returns**: Array of Store objects
- **Order**: By name ascending

#### `findAllActive(): array`
Retrieves all active, non-deleted stores.
- **Returns**: Array of store data as associative arrays
- **Order**: By name ascending

#### `findById(int $id): ?self`
Finds a store by its ID.
- **Parameters**: `$id` - Store ID
- **Returns**: Store object or null if not found

#### `findByDomain(string $url): ?array`
Finds a store by matching URL pattern.
- **Parameters**: `$url` - Store website URL
- **Returns**: Store data as associative array or null if not found

#### `countActive(): int`
Counts the number of active stores.
- **Returns**: Number of active stores

### Instance Methods

#### `save(): bool`
Saves or updates the store in the database.
- **Behavior**:
  - For new stores: Creates new record
  - For existing stores: Updates fields
- **Returns**: true on success, false on failure

#### `softDelete(): bool`
Marks the store as deleted without removing from database.
- **Returns**: true on success, false on failure

### Getters and Setters

- `getId(): ?int`
- `getName(): string`
- `setName(string $name): void`
- `getLogoUrl(): ?string`
- `setLogoUrl(?string $logo_url): void`
- `getUrl(): ?string`
- `setUrl(?string $url): void`
- `isActive(): bool`
- `setIsActive(bool $is_active): void`
- `getCreatedAt(): ?string`
- `getUpdatedAt(): ?string`

## Database Schema

```sql
CREATE TABLE stores (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    logo_url VARCHAR(255),
    url VARCHAR(255),
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP
);
```

## Relationships

1. **Products**:
   - One-to-Many relationship
   - A store can have multiple products
   - Foreign key in `products` table: `store_id`

2. **Deals**:
   - One-to-Many relationship
   - A store can have multiple deals
   - Foreign key in `deals` table: `store_id`

## Usage Examples

### Creating a New Store
```php
$store = new Store(
    'Health Supplements Co',
    'https://example.com/logo.png',
    'https://example.com',
    true
);
$store->save();
```

### Finding and Updating a Store
```php
$store = Store::findById(123);
if ($store) {
    $store->setName('Updated Store Name');
    $store->setLogoUrl('https://example.com/new-logo.png');
    $store->save();
}
```

### Finding Store by Domain
```php
$storeData = Store::findByDomain('example.com');
if ($storeData) {
    // Process store data
}
```

## Error Handling

The model implements comprehensive error handling:
1. Database errors are logged using `error_log()`
2. Failed operations return `false` or `null`
3. PDO exceptions are caught and handled gracefully
4. All database operations are wrapped in try-catch blocks

## Best Practices

1. **Store Management**:
   - Validate URLs before saving
   - Ensure logo URLs are accessible
   - Maintain consistent naming conventions
   - Use appropriate image formats for logos

2. **Data Integrity**:
   - Check relationships before deletion
   - Validate required fields
   - Maintain audit trail through timestamps
   - Use soft deletes to preserve history

3. **Performance**:
   - Cache frequently accessed stores
   - Optimize queries with indexes
   - Use prepared statements
   - Implement efficient filtering

## Implementation Details

### URL Handling
- Store URLs should include protocol (http/https)
- Domain matching is case-insensitive
- URL validation before saving
- Support for various domain formats

### Logo Management
- Support for external image URLs
- Recommended logo dimensions
- Fallback for missing logos
- Image format validation

### Active Status
- Controls store visibility
- Affects product visibility
- Impacts deal availability
- Used for temporary suspension 