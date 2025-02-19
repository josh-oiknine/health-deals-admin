# Store Views Documentation

## Overview
The store views handle the display and management of stores in the Health Deals Admin system. These views facilitate store listing, creation, editing, and detailed store information display.

## View Structure

### Store List View (`stores/index.php`)

#### Purpose
Displays a paginated list of stores with filtering and sorting capabilities.

#### Required Data
- `$stores` - Array of store objects
- `$currentPage` - Current page number
- `$totalPages` - Total number of pages
- `$filters` - Applied filter parameters

#### Features
- Search by store name
- Filter by status (active/inactive)
- Sort by name, products count, deals count
- Pagination
- Quick actions (edit, deactivate)

#### Example Usage
```php
// Controller
$stores = Store::findFiltered([
    'search' => $_GET['search'] ?? '',
    'is_active' => $_GET['is_active'] ?? null,
    'page' => $_GET['page'] ?? 1
]);
```

### Store Create View (`stores/create.php`)

#### Purpose
Provides a form for creating new stores.

#### Required Data
None (standalone form)

#### Form Fields
1. **Basic Information**:
   - Store Name (required)
   - Website URL (required)
   - Logo URL (optional)

2. **Status**:
   - Active/Inactive toggle

#### Validation
- Client-side validation using HTML5 and JavaScript
- Server-side validation in Store model
- URL format validation
- Logo image validation

### Store Edit View (`stores/edit.php`)

#### Purpose
Allows editing of existing store information.

#### Required Data
- `$store` - Store object to edit
- `$products` - Array of store's products
- `$deals` - Array of store's deals
- `$metrics` - Store performance metrics

#### Features
- Same fields as create form
- Pre-populated with existing data
- Store performance metrics
- Products and deals listing
- Creation and update timestamps

### Store Details View (`stores/details.php`)

#### Purpose
Shows detailed information about a specific store.

#### Required Data
- `$store` - Store object
- `$products` - Store's products
- `$deals` - Store's deals
- `$metrics` - Performance metrics

#### Sections
1. **Store Information**:
   - Basic details
   - Logo preview
   - Status indicator
   - Website link

2. **Performance Metrics**:
   - Total products
   - Active deals
   - Total revenue
   - Conversion rate

3. **Products Section**:
   - Product grid
   - Price tracking
   - Product status

4. **Deals Section**:
   - Active deals
   - Expired deals
   - Deal performance

## JavaScript Integration

### Required Files
- `public/js/stores.js`
- `public/js/metrics.js` (for performance charts)

### Features
1. **Form Handling**:
   - URL validation
   - Logo preview
   - Form submission
   - Domain extraction

2. **Performance Charts**:
   - Revenue charts
   - Product trends
   - Deal performance
   - Time period selection

3. **Dynamic Updates**:
   - Status toggle
   - Logo updates
   - Metrics refresh

## CSS Styling

### Required Files
- `public/css/stores.css`

### Style Elements
1. **List View**:
   - Store cards
   - Logo containers
   - Status indicators
   - Action buttons

2. **Forms**:
   - Input styling
   - Logo preview
   - URL validation
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
   - Invalid URL format
   - Invalid logo format
   - Duplicate store name

2. **System Errors**:
   - Database connection
   - Logo upload
   - API integration
   - Metrics calculation

### Error Display
- Inline field validation
- Form submission errors
- System error alerts
- Console logging

## Best Practices

1. **Performance**:
   - Optimized logo loading
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
   - Logo optimization
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

3. **Asset Protection**:
   - Secure logo storage
   - Protected metrics
   - Encrypted transmission
   - Audit logging

## Integration Points

1. **Products**:
   - Product listing
   - Price tracking
   - Category mapping
   - Status management

2. **Deals**:
   - Deal creation
   - Performance tracking
   - Expiration handling
   - Revenue calculation

3. **Analytics**:
   - Store performance
   - Product trends
   - Deal effectiveness
   - Revenue tracking 