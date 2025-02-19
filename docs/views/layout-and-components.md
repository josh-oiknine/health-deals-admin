# Layout and Shared Components Documentation

## Main Layout (`layout.php`)

### Overview
The main layout provides the base HTML structure and navigation for all pages in the Health Deals Admin system.

### Dependencies

#### CSS
- Bootstrap 5.3.0 (CDN)
- Bootstrap Icons 1.7.2 (CDN)
- Custom styles (`/assets/css/style.css`)

#### JavaScript
- Bootstrap 5.3.0 Bundle (CDN)
- Custom scripts (`/assets/js/app.js`)

#### PHP Dependencies
- `Firebase\JWT` - For token validation
- `Firebase\JWT\Key` - For token key handling

### Structure

1. **Head Section**:
   - Meta tags
   - Title
   - Favicon and Apple Touch Icons
   - CSS dependencies

2. **Navigation**:
   - Fixed top navbar
   - Collapsible sidebar
   - Dynamic menu highlighting
   - User-specific menu items

3. **Content Area**:
   - Main content container
   - Fluid width
   - Top padding for navbar

### Navigation Menu

#### Main Items
- Dashboard (`/dashboard`)
- Products (`/products`)
- Deals (`/deals`)
- Outbox (`/outbox`)
- Stores (`/stores`)
- Categories (`/categories`)

#### Admin Items
- Admin Users (`/users`) - Only visible to `josh@udev.com`
- Settings (`/settings`)
- Logout

### Authentication States

1. **Public Pages** (No Layout):
   - Login page (`/`)
   - MFA page (`/mfa`)
   - 2FA Setup (`/setup-2fa`)
   - MFA Verification (`/verify-mfa`)

2. **Protected Pages**:
   - Full layout with navigation
   - Requires valid auth token
   - Role-based menu items

### Icons and Branding
- Logo: `/assets/images/favicon-32x32.png`
- Menu icons: Bootstrap Icons
- Favicon sizes: 16x16, 32x32, 96x96, 192x192
- Apple Touch Icon sizes: 57x57 to 180x180

## Shared Components

### Pagination Component (`components/pagination.php`)

#### Usage
```php
<?php include 'components/pagination.php'; ?>
```

#### Required Variables
- `$currentPage` - Current page number
- `$totalPages` - Total number of pages
- `$baseUrl` - Base URL for pagination links

#### Features
- Previous/Next navigation
- Page number indicators
- Active page highlighting
- Responsive design

#### Example
```php
$currentPage = 1;
$totalPages = 10;
$baseUrl = '/products?page=';
include 'components/pagination.php';
```

## Assets

### CSS (`/assets/css/style.css`)

#### Layout Styles
- Sidebar dimensions and colors
- Navigation spacing
- Content padding
- Responsive breakpoints

#### Component Styles
- Button variations
- Form elements
- Card layouts
- Table styles
- Alert messages

### JavaScript (`/assets/js/app.js`)

#### Features
- Sidebar toggle functionality
- Bootstrap tooltips initialization
- Form validation
- Dynamic content loading
- Alert dismissal

#### Event Handlers
- Sidebar toggle click
- Form submission
- Modal interactions
- Tooltip initialization

## Best Practices

1. **Layout Usage**:
   - Always extend main layout
   - Set page-specific title
   - Include required assets
   - Handle authentication

2. **Component Integration**:
   - Use consistent naming
   - Pass required variables
   - Follow component structure
   - Document dependencies

3. **Asset Management**:
   - Minimize CSS/JS files
   - Use CDN for frameworks
   - Cache static assets
   - Version asset files

## Security Considerations

1. **Authentication**:
   - JWT token validation
   - Role-based access
   - Secure cookie handling
   - Session management

2. **XSS Prevention**:
   - HTML escaping
   - Input sanitization
   - Content Security Policy
   - Safe data output

3. **CSRF Protection**:
   - Form tokens
   - Secure headers
   - Request validation
   - Session verification 