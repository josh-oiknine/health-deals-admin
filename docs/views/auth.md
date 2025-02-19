# Authentication Views Documentation

## Overview
The authentication views handle user authentication, two-factor authentication (2FA), and security features in the Health Deals Admin system. These views manage login, MFA setup, and verification processes.

## View Structure

### Login View (`auth/login.php`)

#### Purpose
Provides the main login form and handles user authentication.

#### Required Data
- `$error` - Error message (if any)
- `$email` - Previously entered email (if any)

#### Features
- Email/password login form
- Remember me functionality
- Error message display
- MFA redirection
- Password requirements

#### Form Fields
1. **Login Information**:
   - Email Address (required)
   - Password (required)
   - Remember Me (checkbox)

#### Validation
- Client-side validation using HTML5
- Server-side validation in AuthController
- CSRF protection
- Rate limiting

### MFA Setup View (`auth/setup-2fa.php`)

#### Purpose
Guides users through the 2FA setup process.

#### Required Data
- `$qrCode` - QR code image data
- `$secret` - TOTP secret key
- `$error` - Error message (if any)

#### Features
- QR code display
- Manual entry key
- Setup instructions
- Verification step
- Backup codes

#### Form Fields
1. **Verification**:
   - 6-digit code (required)
   - Verify button

### MFA Verification View (`auth/mfa.php`)

#### Purpose
Handles 2FA code verification during login.

#### Required Data
- `$error` - Error message (if any)
- `$email` - User's email address

#### Features
- Code input field
- Resend code option
- Error handling
- Session management
- Timeout handling

#### Form Fields
1. **MFA Code**:
   - 6-digit code (required)
   - Submit button

### Password Reset Views

#### Request Reset View (`auth/forgot-password.php`)
- Email input form
- Reset instructions
- Error handling
- Success messages

#### Reset Password View (`auth/reset-password.php`)
- New password form
- Password requirements
- Token validation
- Success redirection

## JavaScript Integration

### Required Files
- `public/js/auth.js`
- `public/js/mfa.js`

### Features
1. **Form Validation**:
   - Real-time validation
   - Password strength meter
   - Input formatting
   - Error highlighting

2. **MFA Handling**:
   - Code input masking
   - Auto-submission
   - Timer countdown
   - Resend functionality

3. **Security Features**:
   - Session timeout
   - Auto logout
   - Remember me
   - Device verification

## CSS Styling

### Required Files
- `public/css/auth.css`

### Style Elements
1. **Login Form**:
   - Clean layout
   - Responsive design
   - Error states
   - Loading indicators

2. **MFA Setup**:
   - QR code display
   - Step indicators
   - Code input styling
   - Success states

3. **Password Reset**:
   - Form layout
   - Progress indicators
   - Validation feedback
   - Success messages

## Error Handling

### Types of Errors
1. **Authentication Errors**:
   - Invalid credentials
   - Account locked
   - MFA required
   - Session expired

2. **MFA Errors**:
   - Invalid code
   - Expired code
   - Setup failed
   - Device mismatch

### Error Display
- Inline validation
- Form-level errors
- Session messages
- Alert notifications

## Best Practices

1. **Security**:
   - Rate limiting
   - Session management
   - CSRF protection
   - XSS prevention

2. **User Experience**:
   - Clear instructions
   - Error guidance
   - Progress indication
   - Helpful messages

3. **Performance**:
   - Minimal redirects
   - Efficient validation
   - Quick responses
   - Smooth transitions

## Security Considerations

1. **Authentication**:
   - Secure password storage
   - MFA enforcement
   - Session security
   - Token management

2. **Access Control**:
   - Rate limiting
   - IP blocking
   - Device tracking
   - Audit logging

3. **Data Protection**:
   - Secure transmission
   - Token encryption
   - Cookie security
   - Data sanitization

## Integration Points

1. **User Management**:
   - Account creation
   - Profile updates
   - Password changes
   - MFA management

2. **Session Handling**:
   - Token generation
   - Session storage
   - Timeout management
   - Logout handling

3. **Security Services**:
   - MFA providers
   - Password hashing
   - Token validation
   - Rate limiting

## Maintenance

1. **Security Updates**:
   - Regular audits
   - Vulnerability checks
   - Dependency updates
   - Protocol updates

2. **User Support**:
   - Reset handling
   - MFA recovery
   - Account unlocking
   - Issue resolution

3. **Documentation**:
   - Setup guides
   - Recovery procedures
   - Security policies
   - Troubleshooting 