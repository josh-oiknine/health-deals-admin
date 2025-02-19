# User Administration Views Documentation

## Overview
The user administration views manage the administration of user accounts in the Health Deals Admin system. These views are restricted to admin users (specifically `josh@udev.com`) and handle user listing, creation, editing, and management of user-specific features like MFA.

## View Structure

### User List View (`users/index.php`)

#### Purpose
Displays a paginated list of system users with management options.

#### Required Data
- `$users` - Array of user objects
- `$currentPage` - Current page number
- `$totalPages` - Total number of pages
- `$filters` - Applied filter parameters

#### Features
- Search by name/email
- Filter by status
- Sort by various fields
- Pagination
- Quick actions
- MFA status indicators

#### Access Control
- Restricted to `josh@udev.com`
- Requires admin privileges
- Session validation
- Action logging

### User Create View (`users/create.php`)

#### Purpose
Provides a form for creating new user accounts.

#### Required Data
None (standalone form)

#### Form Fields
1. **Basic Information**:
   - Email Address (required)
   - Password (required)
   - First Name (required)
   - Last Name (required)
   - Active Status

2. **Security Options**:
   - Force Password Change
   - Enable 2FA
   - Account Restrictions

#### Validation
- Email uniqueness
- Password strength
- Required fields
- Format validation

### User Edit View (`users/edit.php`)

#### Purpose
Allows editing of existing user accounts.

#### Required Data
- `$user` - User object to edit
- `$activityLog` - User activity history
- `$securityInfo` - Security-related data

#### Features
1. **Profile Management**:
   - Basic information update
   - Password management
   - Status control
   - Role assignment

2. **Security Management**:
   - MFA configuration
   - Session management
   - Access history
   - Security logs

3. **Activity Tracking**:
   - Login history
   - Action logs
   - System access
   - Change history

### User Details View (`users/details.php`)

#### Purpose
Shows detailed information about a specific user.

#### Required Data
- `$user` - User object
- `$activity` - User activity data
- `$security` - Security information
- `$metrics` - User-specific metrics

#### Sections
1. **User Information**:
   - Profile details
   - Account status
   - Created/Updated dates
   - Role information

2. **Security Status**:
   - MFA status
   - Last login
   - Password age
   - Security events

3. **Activity Log**:
   - Recent actions
   - Login attempts
   - System access
   - Data modifications

## JavaScript Integration

### Required Files
- `public/js/users.js`
- `public/js/security.js`

### Features
1. **Form Handling**:
   - Real-time validation
   - Password strength
   - Email verification
   - Status updates

2. **Security Features**:
   - MFA management
   - Session control
   - Access validation
   - Activity monitoring

3. **Dynamic Updates**:
   - Status changes
   - Role updates
   - Security settings
   - Activity tracking

## CSS Styling

### Required Files
- `public/css/users.css`

### Style Elements
1. **User List**:
   - Grid layout
   - Status indicators
   - Action buttons
   - Search filters

2. **Forms**:
   - Input styling
   - Validation states
   - Security options
   - Role selectors

3. **Details View**:
   - Information cards
   - Activity timeline
   - Security badges
   - Status indicators

## Error Handling

### Types of Errors
1. **Validation Errors**:
   - Invalid email
   - Weak password
   - Missing fields
   - Duplicate email

2. **Security Errors**:
   - Access denied
   - Session expired
   - Invalid token
   - MFA failure

### Error Display
- Inline validation
- Form-level errors
- System messages
- Security alerts

## Best Practices

1. **User Management**:
   - Regular audits
   - Password policies
   - Access control
   - Activity monitoring

2. **Security**:
   - MFA enforcement
   - Session management
   - Role validation
   - Audit logging

3. **Performance**:
   - Efficient queries
   - Cached data
   - Optimized loading
   - Resource management

## Security Considerations

1. **Access Control**:
   - Admin-only access
   - Role validation
   - Permission checks
   - Action logging

2. **Data Protection**:
   - Password hashing
   - Secure transmission
   - Data encryption
   - Privacy compliance

3. **Audit Trail**:
   - Action logging
   - Change tracking
   - Access monitoring
   - Security events

## Integration Points

1. **Authentication System**:
   - Login management
   - MFA integration
   - Session handling
   - Password policies

2. **Activity Tracking**:
   - User actions
   - System access
   - Data changes
   - Security events

3. **Security Services**:
   - MFA providers
   - Password validation
   - Session management
   - Access control

## Maintenance

1. **User Cleanup**:
   - Inactive accounts
   - Failed logins
   - Old sessions
   - Expired tokens

2. **Security Updates**:
   - Password resets
   - MFA updates
   - Role reviews
   - Access audits

3. **Documentation**:
   - User guides
   - Security policies
   - Access procedures
   - Troubleshooting

## Future Enhancements

1. **Advanced Features**:
   - Role management
   - Group permissions
   - Custom fields
   - Advanced search

2. **Security Features**:
   - Enhanced MFA
   - IP restrictions
   - Access policies
   - Security alerts

3. **Integration Options**:
   - SSO support
   - Directory services
   - API access
   - Audit tools 