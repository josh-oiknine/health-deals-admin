# Product Views Documentation

## Overview
The product views handle the display and management of products in the Health Deals Admin system. These views include listing, creation, editing, and detailed product information.

## View Structure

### Product List View (`products/index.php`)

#### Purpose
Displays a paginated list of products with filtering and sorting capabilities.

#### Required Data
- `$products` - Array of product objects
- `$stores` - Array of store objects for filtering
- `$categories` - Array of category objects for filtering
- `$currentPage` - Current page number
- `$totalPages` - Total number of pages
- `$filters` - Applied filter parameters

#### Features
- Search by product name/SKU
- Filter by store
- Filter by category
- Filter by status (active/inactive)
- Sort by various fields
- Pagination
- Quick actions (edit, delete)

#### Example Usage
```php
// Controller
$products = Product::findFiltered([
    'search' => $_GET['search'] ?? '',
    'store_id' => $_GET['store_id'] ?? null,
    'category_id' => $_GET['category_id'] ?? null,
    'is_active' => $_GET['is_active'] ?? null,
    'page' => $_GET['page'] ?? 1
]);
```

### Product Create View (`products/create.php`)

#### Purpose
Provides a form for creating new products.

#### Required Data
- `$stores` - Array of available stores
- `$categories` - Array of available categories

#### Form Fields
1. **Basic Information**:
   - Product Name (required)
   - SKU (required)
   - UPC (optional)
   - Regular Price (required)
   - Store (required)
   - Category (required)

2. **URLs**:
   - Product URL (required)
   - Image URL (optional)

3. **Status**:
   - Active/Inactive toggle

#### Validation
- Client-side validation using HTML5 and JavaScript
- Server-side validation in Product model

### Product Edit View (`products/edit.php`)

#### Purpose
Allows editing of existing product information.

#### Required Data
- `$product` - Product object to edit
- `$stores` - Array of available stores
- `$categories` - Array of available categories
- `$priceHistory` - Array of price history records

#### Features
- Same fields as create form
- Pre-populated with existing data
- Price history chart
- Last checked timestamp
- Creation and update timestamps

### Product Details View (`products/details.php`)

#### Purpose
Shows detailed information about a specific product.

#### Required Data
- `$product` - Product object
- `$deals` - Related deals
- `$priceHistory` - Price history records

#### Sections
1. **Product Information**:
   - Basic details
   - Store information
   - Category
   - Status

2. **Price Information**:
   - Current price
   - Price history chart
   - Price change percentage

3. **Related Deals**:
   - Active deals
   - Expired deals
   - Deal statistics

4. **Tracking Information**:
   - Last checked
   - Created date
   - Updated date

## JavaScript Integration

### Required Files
- `public/js/products.js`
- `public/js/chart.js` (for price history)

### Features
1. **Form Handling**:
   - Dynamic SKU generation
   - Price formatting
   - URL validation
   - Form submission

2. **Price History Chart**:
   - Interactive chart
   - Date range selection
   - Price trend visualization

3. **Dynamic Updates**:
   - Status toggle
   - Price updates
   - Category changes

## CSS Styling

### Required Files
- `public/css/products.css`

### Style Elements
1. **List View**:
   - Grid layout
   - Responsive tables
   - Filter panel
   - Action buttons

2. **Forms**:
   - Input styling
   - Validation states
   - Help text
   - Error messages

3. **Details View**:
   - Information cards
   - Price chart
   - Status indicators
   - Timeline display

## Error Handling

### Types of Errors
1. **Validation Errors**:
   - Missing required fields
   - Invalid price format
   - Duplicate SKU
   - Invalid URL format

2. **System Errors**:
   - Database connection
   - File upload
   - External API

### Error Display
- Inline field errors
- Top-of-page alerts
- Modal notifications
- Console logging

## Best Practices

1. **Performance**:
   - Lazy loading of images
   - Pagination of large lists
   - Caching of static data
   - Optimized database queries

2. **User Experience**:
   - Clear error messages
   - Intuitive navigation
   - Responsive design
   - Loading indicators

3. **Maintenance**:
   - Commented code
   - Consistent naming
   - Modular structure
   - Documentation

## Security Considerations

1. **Input Validation**:
   - Sanitize all inputs
   - Validate file uploads
   - Check permissions
   - Prevent XSS

2. **Access Control**:
   - Role-based access
   - Action logging
   - Session handling
   - CSRF protection 