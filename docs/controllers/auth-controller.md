# Authentication Controller Documentation

## Overview
The `AuthController` manages user authentication, including login, two-factor authentication (2FA), and JWT token management. It provides both web and API endpoints for authentication services.

## Dependencies
- `Firebase\JWT` - For JWT token generation and validation
- `RobThree\Auth` - For Two-Factor Authentication (2FA)
- `ImageChartsQRCodeProvider` - For generating 2FA QR codes

## Configuration
The controller is initialized with:
- View renderer for web pages
- Database connection
- Two-Factor Authentication setup with:
  - 6-digit codes
  - 30-second validity
  - SHA1 algorithm
  - "Health Deals Admin" as the issuer name

## Methods

### Web Authentication

#### `loginPage(Request $request, Response $response)`
Renders the login page.
- **Method**: GET
- **Route**: `/`
- **Template**: `auth/login.php`
- **Response**: HTML login form

#### `login(Request $request, Response $response)`
Processes login form submission.
- **Method**: POST
- **Route**: `/login`
- **Parameters**:
  - `email` (string) - User's email
  - `password` (string) - User's password
- **Response**: Redirects to:
  - Dashboard if successful
  - 2FA setup if not configured
  - MFA verification if 2FA is enabled
  - Login page with error if failed

#### `setup2faPage(Request $request, Response $response)`
Displays 2FA setup page with QR code.
- **Method**: GET
- **Route**: `/setup-2fa`
- **Template**: `auth/setup-2fa.php`
- **Response**: HTML page with 2FA setup instructions

#### `setup2fa(Request $request, Response $response)`
Validates and completes 2FA setup.
- **Method**: POST
- **Route**: `/setup-2fa`
- **Parameters**:
  - `code` (string) - 6-digit verification code
- **Response**: Redirects to dashboard or shows error

#### `mfaPage(Request $request, Response $response)`
Displays MFA verification page.
- **Method**: GET
- **Route**: `/mfa`
- **Template**: `auth/mfa.php`
- **Response**: HTML page with MFA verification form

#### `verifyMfa(Request $request, Response $response)`
Verifies MFA code during login.
- **Method**: POST
- **Route**: `/verify-mfa`
- **Parameters**:
  - `code` (string) - 6-digit verification code
- **Response**: Redirects to dashboard or shows error

### API Authentication

#### `apiLogin(Request $request, Response $response)`
Handles API login requests.
- **Method**: POST
- **Route**: `/api/login`
- **Parameters**:
  - `email` (string) - User's email
  - `password` (string) - User's password
- **Response**: JSON
  ```json
  {
    "status": "success|error",
    "message": "string",
    "auth_token": "string" // JWT token if successful
  }
  ```
- **Status Codes**:
  - 200: Success
  - 400: Missing credentials
  - 401: Invalid credentials
  - 403: 2FA setup required

#### `apiVerifyToken(Request $request, Response $response)`
Validates JWT tokens.
- **Method**: GET
- **Route**: `/api/verify-token`
- **Headers**:
  - `Authorization: Bearer <token>`
- **Response**: JSON
  ```json
  {
    "status": "success|error",
    "message": "string"
  }
  ```
- **Status Codes**:
  - 200: Valid token
  - 401: Invalid/expired token

#### `handleOptionsRequest(Request $request, Response $response)`
Handles CORS preflight requests.
- **Method**: OPTIONS
- **Routes**: `/api/login`, `/api/verify-token`
- **Response**: Empty response with CORS headers
- **Status Code**: 204

## Authentication Flow

1. **Web Authentication Flow**:
   ```mermaid
   graph TD
   A[Login Page] -->|Submit Credentials| B{Valid Credentials?}
   B -->|Yes| C{2FA Enabled?}
   B -->|No| D[Show Error]
   C -->|Yes| E[MFA Verification]
   C -->|No| F[2FA Setup]
   E -->|Valid Code| G[Dashboard]
   F -->|Setup Complete| G
   ```

2. **API Authentication Flow**:
   ```mermaid
   graph TD
   A[API Login] -->|Submit Credentials| B{Valid Credentials?}
   B -->|Yes| C{2FA Setup Complete?}
   B -->|No| D[401 Unauthorized]
   C -->|Yes| E[Issue JWT Token]
   C -->|No| F[403 2FA Required]
   ```

## Security Considerations

1. **Password Security**:
   - Passwords are hashed before storage
   - Failed login attempts are logged
   - Passwords are never returned in responses

2. **JWT Security**:
   - Tokens include expiration time
   - Tokens are signed with a secret key
   - Token validation on every protected route

3. **2FA Security**:
   - TOTP-based authentication
   - 6-digit codes with 30-second validity
   - Secure QR code generation
   - Verification required after setup

4. **CORS Security**:
   - Configurable origin restrictions
   - Proper headers for API endpoints
   - Options preflight handling

## Error Handling

The controller implements comprehensive error handling:
1. Invalid credentials return appropriate error messages
2. Missing or malformed data is validated
3. Database errors are caught and logged
4. JWT token validation errors are handled gracefully
5. 2FA setup and verification errors provide clear messages

## Usage Examples

### Web Login
```php
// Login form submission
POST /login
Content-Type: application/x-www-form-urlencoded

email=user@example.com&password=secretpass
```

### API Authentication
```php
// API login request
POST /api/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "secretpass"
}

// Token verification
GET /api/verify-token
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
``` 