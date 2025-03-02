# Blog Post Model

## Overview
The `BlogPost` model manages blog content in the Health Deals Admin application. It provides functionality for creating, updating, and managing blog posts with features like draft/publish status, SEO support, and soft deletion.

## Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | ?int | Primary key identifier |
| `title` | string | Blog post title |
| `slug` | string | URL-friendly version of title |
| `body` | string | HTML content of the blog post |
| `seo_keywords` | ?string | SEO meta keywords |
| `user_id` | int | Author's user ID |
| `created_at` | ?DateTime | Creation timestamp |
| `updated_at` | ?DateTime | Last update timestamp |
| `published_at` | ?DateTime | Publication timestamp |
| `featured_image_url` | ?string | Featured image URL |
| `deleted_at` | ?DateTime | Soft deletion timestamp |

## Constructor

```php
public function __construct(
    string $title = '',
    string $slug = '',
    string $body = '',
    ?string $seo_keywords = null,
    ?DateTime $published_at = null,
    int $user_id = 0,
    ?string $featured_image_url = null
)
```

Creates a new BlogPost instance with the specified properties.

## Public Methods

### Validation

```php
public function validate(): bool
```

Validates the blog post data before saving. Throws `InvalidArgumentException` if validation fails.

**Validation Rules:**
- Title is required
- Slug is required
- User ID is required

### CRUD Operations

```php
public function save(): bool
```

Saves or updates the blog post in the database. Returns `true` on success, `false` on failure.

```php
public function softDelete(): bool
```

Marks the blog post as deleted by setting the `deleted_at` timestamp.

### Static Query Methods

```php
public static function findAll(): array
```

Retrieves all non-deleted blog posts with author information.

```php
public static function findFiltered(
    array $filters = [],
    string $sortBy = 'created_at',
    string $sortOrder = 'DESC',
    int $page = 1,
    int $perPage = 20
): array
```

Retrieves blog posts with filtering, sorting, and pagination.

**Filter Options:**
- `keyword`: Search in title and SEO keywords
- `is_published`: Filter by published status
- `user_id`: Filter by author

**Sort Options:**
- `title`
- `created_at`
- `published_at`
- `updated_at`

```php
public static function findById(int $id): ?array
```

Retrieves a single blog post by ID with author information.

```php
public static function findBySlug(string $slug): ?array
```

Retrieves a single blog post by slug with author information.

```php
public static function findAllPublished(): array
```

Retrieves all published blog posts.

### Statistics Methods

```php
public static function countPublished(): int
```

Returns the count of published blog posts.

```php
public static function countDrafts(): int
```

Returns the count of draft (unpublished) blog posts.

## Usage Examples

### Creating a New Blog Post

```php
$blogPost = new BlogPost(
    'Health Benefits of Green Tea',
    'health-benefits-green-tea',
    '<p>Detailed article about green tea benefits...</p>',
    'green tea, health benefits, antioxidants',
    new DateTime(),
    1, // user_id
    'https://example.com/green-tea.jpg' // featured_image_url
);

if ($blogPost->save()) {
    // Blog post saved successfully
}
```

### Finding Blog Posts with Filters

```php
$filters = [
    'keyword' => 'health',
    'is_published' => true,
    'user_id' => 1
];

$posts = BlogPost::findFiltered(
    $filters,
    'published_at',
    'DESC',
    1,
    20
);
```

### Updating a Blog Post

```php
$blogPost = new BlogPost(
    'Updated Title',
    'updated-slug',
    'Updated content...',
    'updated, keywords',
    new DateTime(),
    1,
    'https://example.com/updated-image.jpg'
);
$blogPost->setId($existingId);
$blogPost->save();
```

### Soft Deleting a Blog Post

```php
$blogPost = new BlogPost();
$blogPost->setId($id);
$blogPost->softDelete();
```

## Error Handling

The model implements comprehensive error logging for database operations. All errors are logged using PHP's error_log function with detailed context information.

## Database Relationships

- Belongs to one User (author)
- Implements soft deletion through `deleted_at` timestamp 