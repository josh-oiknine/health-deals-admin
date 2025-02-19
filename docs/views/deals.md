# Deal Views Documentation

## Overview
The deal views manage the display and administration of deals in the Health Deals Admin system. These views handle deal listing, creation, editing, and detailed information display.

## View Structure

### Deal List View (`deals/index.php`)

#### Purpose
Displays a paginated list of deals with comprehensive filtering and sorting capabilities.

#### Required Data
- `$deals` - Array of deal objects
- `$stores` - Array of store objects for filtering
- `$categories` - Array of category objects for filtering
- `$currentPage` - Current page number
- `$totalPages` - Total number of pages
- `$filters` - Applied filter parameters

#### Features
- Search by deal title
- Filter by store
- Filter by category
- Filter by status (active/inactive/expired)
- Filter by featured status
- Sort by various fields
- Pagination
- Quick actions (edit, expire)

#### Example Usage
```php
// Controller
$deals = Deal::findFiltered([
    'search' => $_GET['search'] ?? '',
    'store_id' => $_GET['store_id'] ?? null,
    'category_id' => $_GET['category_id'] ?? null,
    'is_active' => $_GET['is_active'] ?? null,
    'is_featured' => $_GET['is_featured'] ?? null,
    'is_expired' => $_GET['is_expired'] ?? null,
    'page' => $_GET['page'] ?? 1
]);
```

### Deal Create View (`deals/create.php`)

#### Purpose
Provides a form for creating new deals.

#### Required Data
- `$stores` - Array of available stores
- `$categories` - Array of available categories
- `$products` - Array of available products

#### Form Fields
1. **Basic Information**:
   - Deal Title (required)
   - Description (required)
   - Product (required)
   - Store (required)
   - Category (required)

2. **Pricing**:
   - Original Price (required)
   - Deal Price (required)
   - Savings Percentage (auto-calculated)

3. **URLs and Media**:
   - Affiliate URL (required)
   - Image URL (required)

4. **Status**:
   - Active/Inactive toggle
   - Featured toggle
   - Expiration toggle

#### Validation
- Client-side validation using HTML5 and JavaScript
- Server-side validation in Deal model
- Price validation (deal price < original price)

### Deal Edit View (`deals/edit.php`)

#### Purpose
Allows editing of existing deal information.

#### Required Data
- `$deal` - Deal object to edit
- `$stores` - Array of available stores
- `$categories` - Array of available categories
- `$products` - Array of available products

#### Features
- Same fields as create form
- Pre-populated with existing data
- Deal performance metrics
- Creation and update timestamps
- Expiration management

### Deal Details View (`deals/details.php`)

#### Purpose
Shows detailed information about a specific deal.

#### Required Data
- `$deal` - Deal object
- `$product` - Related product
- `$store` - Related store
- `$category` - Related category

#### Sections
1. **Deal Information**:
   - Basic details
   - Pricing information
   - Store and category
   - Status indicators

2. **Performance Metrics**:
   - Views count
   - Click-through rate
   - Conversion rate
   - Revenue generated

3. **Related Information**:
   - Product details
   - Similar deals
   - Store performance

4. **Timeline**:
   - Created date
   - Updated date
   - Expiration date
   - Status changes

## JavaScript Integration

### Required Files
- `public/js/deals.js`
- `public/js/metrics.js` (for performance charts)

### Features
1. **Form Handling**:
   - Dynamic price calculation
   - Image preview
   - URL validation
   - Form submission

2. **Performance Charts**:
   - Interactive metrics
   - Time period selection
   - Trend visualization

3. **Dynamic Updates**:
   - Status toggles
   - Price updates
   - Expiration management

## CSS Styling

### Required Files
- `public/css/deals.css`

### Style Elements
1. **List View**:
   - Grid/List toggle
   - Deal cards
   - Filter panel
   - Status badges

2. **Forms**:
   - Input styling
   - Image preview
   - Price formatting
   - Toggle switches

3. **Details View**:
   - Metric cards
   - Performance charts
   - Status indicators
   - Timeline display

## Error Handling

### Types of Errors
1. **Validation Errors**:
   - Missing required fields
   - Invalid price values
   - Invalid URL format
   - Invalid date ranges

2. **System Errors**:
   - Database connection
   - Image loading
   - API integration
   - Performance tracking

### Error Display
- Inline field validation
- Form submission errors
- System error alerts
- Console logging

## Best Practices

1. **Performance**:
   - Optimized image loading
   - Efficient filtering
   - Cached metrics
   - Paginated results

2. **User Experience**:
   - Clear status indicators
   - Intuitive navigation
   - Responsive design
   - Quick actions

3. **Data Management**:
   - Regular cleanup
   - Expired deal handling
   - Metric aggregation
   - Backup procedures

## Security Considerations

1. **Input Validation**:
   - Sanitize all inputs
   - Validate URLs
   - Check permissions
   - Prevent XSS

2. **Access Control**:
   - Role-based access
   - Action logging
   - Session handling
   - CSRF protection

3. **Data Protection**:
   - Secure affiliate links
   - Protected metrics
   - Encrypted transmission
   - Audit logging 