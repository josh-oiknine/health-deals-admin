# Category Model Documentation

## Overview
The `Category` model represents product categories in the Health Deals Admin system. It manages category information, including visual representation through color coding and URL-friendly slugs.

## Properties

| Property | Type | Description | Nullable |
|----------|------|-------------|-----------|
| `id` | int | Unique identifier | Yes |
| `name` | string | Category name | No |
| `slug` | string | URL-friendly identifier | No |
| `is_active` | bool | Category status | No |
| `color` | string | Hex color code | Yes |
| `created_at` | string | Creation timestamp | Yes |
| `updated_at` | string | Last update timestamp | Yes |

## Constructor

```php
public function __construct(
    string $name = '',
    string $slug = '',
    bool $is_active = true,
    ?string $color = '#6c757d'
)
```

Creates a new Category instance with the specified properties.

## Methods

### Static Methods

#### `findAll(): array`
Retrieves all categories from the database.
- **Returns**: Array of Category objects
- **Order**: By name ascending

#### `findAllActive(): array`
Retrieves all active categories.
- **Returns**: Array of category data as associative arrays
- **Order**: By name ascending

#### `findById(int $id): ?self`
Finds a category by its ID.
- **Parameters**: `$id` - Category ID
- **Returns**: Category object or null if not found

#### `countActive(): int`
Counts the number of active categories.
- **Returns**: Number of active categories

### Instance Methods

#### `save(): bool`
Saves or updates the category in the database.
- **Behavior**:
  - For new categories: Creates new record
  - For existing categories: Updates fields
  - Maintains timestamps
- **Returns**: true on success, false on failure

### Getters and Setters

- `getId(): ?int`
- `getName(): string`
- `setName(string $name): void`
- `getSlug(): string`
- `setSlug(string $slug): void`
- `isActive(): bool`
- `setIsActive(bool $is_active): void`
- `getColor(): ?string`
- `setColor(?string $color): void`
- `getCreatedAt(): ?string`
- `getUpdatedAt(): ?string`

## Database Schema

```sql
CREATE TABLE categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT true,
    color VARCHAR(7) DEFAULT '#6c757d',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Relationships

1. **Products**:
   - One-to-Many relationship
   - A category can have multiple products
   - Referenced by `products` table

2. **Deals**:
   - One-to-Many relationship
   - A category can have multiple deals
   - Referenced by `deals` table

## Usage Examples

### Creating a New Category
```php
$category = new Category(
    'Health Supplements',
    'health-supplements',
    true,
    '#ff5733'
);
$category->save();
```

### Finding and Updating a Category
```php
$category = Category::findById(123);
if ($category) {
    $category->setName('Updated Category');
    $category->setColor('#00ff00');
    $category->save();
}
```

### Getting Active Categories
```php
$activeCategories = Category::findAllActive();
foreach ($activeCategories as $category) {
    // Process category data
}
```

## Error Handling

The model implements comprehensive error handling:
1. Database errors are logged using `error_log()`
2. Failed operations return `false` or `null`
3. PDO exceptions are caught and handled gracefully
4. All database operations are wrapped in try-catch blocks

## Best Practices

1. **Category Management**:
   - Generate clean slugs
   - Validate color codes
   - Maintain consistent naming
   - Use meaningful colors

2. **Data Integrity**:
   - Check relationships before updates
   - Validate required fields
   - Maintain audit trail
   - Use proper indexing

3. **Performance**:
   - Cache category lists
   - Optimize queries
   - Use prepared statements
   - Implement efficient filtering

## Implementation Details

### Slug Generation
- Convert to lowercase
- Replace spaces with hyphens
- Remove special characters
- Ensure uniqueness

### Color Management
- Default color: `#6c757d`
- Hex color validation
- Color preview support
- Consistent formatting

### Active Status
- Controls visibility
- Affects product display
- Impacts deal filtering
- Used for organization

## Visual Representation

### Color Usage
- Category badges
- UI elements
- Visual organization
- Brand consistency

### Display Hierarchy
- Active categories first
- Alphabetical ordering
- Color-coded display
- Visual indicators

## Integration Points

1. **Product Management**:
   - Category assignment
   - Product filtering
   - Navigation structure
   - Organization system

2. **Deal Management**:
   - Deal categorization
   - Category-based deals
   - Filter functionality
   - Sorting options

3. **UI Integration**:
   - Color-coded badges
   - Category filters
   - Navigation menus
   - Visual hierarchy 