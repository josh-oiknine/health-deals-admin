# Blog Posts Controller

## Overview
The `BlogPostsController` manages blog post operations in the Health Deals Admin application. It provides functionality for listing, creating, editing, viewing, and deleting blog posts with support for filtering, pagination, and preview capabilities.

## Dependencies

- `App\Models\BlogPost`
- `App\Models\User`
- `Psr\Http\Message\ResponseInterface`
- `Psr\Http\Message\ServerRequestInterface`
- `PhpRenderer` (for view rendering)

## Routes

| Method | Route | Handler | Description |
|--------|-------|---------|-------------|
| GET | `/blog-posts` | `index()` | List all blog posts |
| GET | `/blog-posts/view/{id}` | `view()` | View single blog post |
| GET | `/blog-posts/add` | `add()` | Display add form |
| POST | `/blog-posts/add` | `add()` | Create new blog post |
| GET | `/blog-posts/edit/{id}` | `edit()` | Display edit form |
| POST | `/blog-posts/edit/{id}` | `edit()` | Update blog post |
| POST | `/blog-posts/delete/{id}` | `delete()` | Delete blog post |

## Methods

### Constructor

```php
public function __construct($container)
```

Initializes the controller with the DI container and sets up the view renderer.

### Index

```php
public function index(Request $request, Response $response): Response
```

Lists all blog posts with filtering and pagination support.

**Query Parameters:**
- `page`: Current page number (default: 1)
- `per_page`: Items per page (default: 20)
- `sort_by`: Sort field (default: created_at)
- `sort_order`: Sort direction (default: DESC)
- `keyword`: Search in title and keywords
- `is_published`: Filter by published status
- `user_id`: Filter by author

**View Data:**
- `title`: Page title
- `blogPosts`: Array of blog posts
- `pagination`: Pagination details
- `filters`: Applied filters
- `sorting`: Sort settings
- `users`: List of users
- `currentUserEmail`: Current user's email

### View

```php
public function view(Request $request, Response $response, array $args): Response
```

Displays a single blog post in preview mode.

**Parameters:**
- `id`: Blog post ID

**View Data:**
- `blogPost`: Blog post data

### Add

```php
public function add(Request $request, Response $response): Response
```

Handles both GET (display form) and POST (create) requests for new blog posts.

**Form Fields:**
- `title`: Post title
- `slug`: URL-friendly title
- `body`: Post content (HTML)
- `seo_keywords`: SEO keywords
- `published_at`: Publication date/time
- `user_id`: Author ID

**View Data:**
- `title`: Page title
- `blogPost`: Form data
- `isEdit`: false
- `error`: Error message if any
- `users`: List of users
- `currentUserEmail`: Current user's email

### Edit

```php
public function edit(Request $request, Response $response, array $args): Response
```

Handles both GET (display form) and POST (update) requests for existing blog posts.

**Parameters:**
- `id`: Blog post ID

**Form Fields:**
- Same as Add method

**View Data:**
- Same as Add method, with `isEdit: true`

### Delete

```php
public function delete(Request $request, Response $response, array $args): Response
```

Soft deletes a blog post.

**Parameters:**
- `id`: Blog post ID

## Views

### List View (`blog-posts/index.php`)
- Displays blog posts in a table format
- Includes filtering and sorting options
- Shows post status (published/draft)
- Provides actions (edit, preview, delete)
- Implements pagination

### Form View (`blog-posts/form.php`)
- Common form for both add and edit operations
- Rich text editor (TinyMCE) for content
- Auto-generates slug from title
- Handles draft/publish status
- Author selection (admin only)

### Preview View (`blog-posts/view.php`)
- Clean preview of blog post content
- Displays author information
- Used in modal preview

## JavaScript Features

- Automatic slug generation from title
- TinyMCE integration for rich content editing
- Preview functionality in modal
- Form validation
- Confirmation for delete operations

## Security

- Author selection restricted to admin users
- Form validation on both client and server side
- XSS prevention through proper HTML escaping
- CSRF protection through Slim middleware

## Error Handling

- Comprehensive error logging
- User-friendly error messages
- Form state preservation on error
- Proper HTTP status codes

## Dependencies

The controller relies on the following components:

1. **View Renderer**
   - Uses PHP templates
   - Layout system for consistent UI
   - Component reuse

2. **Database Models**
   - BlogPost model for data operations
   - User model for author information

3. **Middleware**
   - Authentication check
   - View data injection
   - CSRF protection
``` 