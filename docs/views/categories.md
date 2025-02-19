# Category Views Documentation

## Overview
The category views manage the display and administration of categories in the Health Deals Admin system. These views handle category listing, creation, editing, and detailed category information display.

## View Structure

### Category List View (`categories/index.php`)

#### Purpose
Displays a paginated list of categories with filtering and sorting capabilities.

#### Required Data
- `$categories` - Array of category objects
- `$currentPage` - Current page number
- `$totalPages` - Total number of pages
- `$filters` - Applied filter parameters

#### Features
- Search by category name
- Filter by status (active/inactive)
- Sort by name, products count
- Pagination
- Quick actions (edit, deactivate)
- Color indicators

#### Example Usage
```php
// Controller
$categories = Category::findFiltered([
    'search' => $_GET['search'] ?? '',
    'is_active' => $_GET['is_active'] ?? null,
    'page' => $_GET['page'] ?? 1
]);
```

### Category Create View (`categories/create.php`)

#### Purpose
Provides a form for creating new categories.

#### Required Data
None (standalone form)

#### Form Fields
1. **Basic Information**:
   - Category Name (required)
   - Slug (auto-generated, editable)
   - Color (required, color picker)

2. **Status**:
   - Active/Inactive toggle

#### Validation
- Client-side validation using HTML5 and JavaScript
- Server-side validation in Category model
- Slug uniqueness check
- Color format validation

### Category Edit View (`categories/edit.php`)

#### Purpose
Allows editing of existing category information.

#### Required Data
- `$category` - Category object to edit
- `$products` - Array of category's products
- `$deals` - Array of category's deals
- `$metrics` - Category performance metrics

#### Features
- Same fields as create form
- Pre-populated with existing data
- Category performance metrics
- Products and deals listing
- Creation and update timestamps

### Category Details View (`categories/details.php`)

#### Purpose
Shows detailed information about a specific category.

#### Required Data
- `$category` - Category object
- `$products` - Category's products
- `$deals` - Category's deals
- `$metrics` - Performance metrics

#### Sections
1. **Category Information**:
   - Basic details
   - Color display
   - Status indicator
   - Slug preview

2. **Performance Metrics**:
   - Total products
   - Active deals
   - Product distribution
   - Deal performance

3. **Products Section**:
   - Product grid
   - Price ranges
   - Store distribution

4. **Deals Section**:
   - Active deals
   - Deal statistics
   - Performance metrics

## JavaScript Integration

### Required Files
- `public/js/categories.js`
- `public/js/metrics.js` (for performance charts)

### Features
1. **Form Handling**:
   - Slug generation
   - Color picker
   - Form submission
   - Validation

2. **Performance Charts**:
   - Product distribution
   - Deal performance
   - Time period selection
   - Trend visualization

3. **Dynamic Updates**:
   - Status toggle
   - Color preview
   - Metrics refresh

## CSS Styling

### Required Files
- `public/css/categories.css`

### Style Elements
1. **List View**:
   - Category cards
   - Color indicators
   - Status badges
   - Action buttons

2. **Forms**:
   - Input styling
   - Color picker
   - Slug preview
   - Toggle switches

3. **Details View**:
   - Metric cards
   - Product grid
   - Deal listings
   - Performance charts

## Error Handling

### Types of Errors
1. **Validation Errors**:
   - Missing required fields
   - Invalid color format
   - Duplicate slug
   - Invalid status

2. **System Errors**:
   - Database connection
   - Metrics calculation
   - Product association
   - Deal linking

### Error Display
- Inline field validation
- Form submission errors
- System error alerts
- Console logging

## Best Practices

1. **Performance**:
   - Efficient filtering
   - Cached metrics
   - Optimized queries
   - Paginated results

2. **User Experience**:
   - Clear color indicators
   - Intuitive navigation
   - Responsive design
   - Quick actions

3. **Data Management**:
   - Regular cleanup
   - Metric aggregation
   - Backup procedures
   - Audit logging

## Security Considerations

1. **Input Validation**:
   - Sanitize all inputs
   - Validate colors
   - Check permissions
   - Prevent XSS

2. **Access Control**:
   - Role-based access
   - Action logging
   - Session handling
   - CSRF protection

## Integration Points

1. **Products**:
   - Category assignment
   - Product listing
   - Status management
   - Bulk operations

2. **Deals**:
   - Deal categorization
   - Performance tracking
   - Category metrics
   - Deal distribution

3. **Analytics**:
   - Category performance
   - Product distribution
   - Deal effectiveness
   - Trend analysis

## Maintenance

1. **Category Management**:
   - Regular review
   - Unused category cleanup
   - Metric verification
   - Performance optimization

2. **Data Integrity**:
   - Slug uniqueness
   - Color consistency
   - Relationship maintenance
   - Status synchronization

3. **Documentation**:
   - Usage guidelines
   - API documentation
   - Integration notes
   - Troubleshooting guide 