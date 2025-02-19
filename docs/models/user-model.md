# User Model Documentation

## Overview
The `User` model represents user accounts in the Health Deals Admin system. It manages user authentication, profile information, and MFA (Multi-Factor Authentication) settings.

## Properties

| Property | Type | Description | Nullable |
|----------|------|-------------|-----------|
| `id` | int | Unique identifier | Yes |
| `email` | string | User's email address | No |
| `password` | string | Hashed password | No |
| `first_name` | string | User's first name | No |
| `last_name` | string | User's last name | No |
| `is_active` | bool | Account status | No |
| `created_at` | string | Creation timestamp | Yes |
| `updated_at` | string | Last update timestamp | Yes |
| `deleted_at` | string | Soft delete timestamp | Yes |
| `totp_secret` | string | TOTP secret for 2FA | Yes |
| `totp_setup_complete` | bool | 2FA setup status | Yes |

## Constructor

```php
public function __construct(
    string $email = '',
    string $password = '',
    string $first_name = '',
    string $last_name = '',
    bool $is_active = true
)
```

Creates a new User instance with the specified properties.

## Methods

### Static Methods

#### `findAll(): array`
Retrieves all non-deleted users from the database.
- **Returns**: Array of User objects
- **Order**: By email ascending

#### `findById(int $id): ?self`
Finds a user by their ID.
- **Parameters**: `$id` - User ID
- **Returns**: User object or null if not found

#### `findByEmail(string $email): ?self`
Finds a user by their email address.
- **Parameters**: `$email` - User's email
- **Returns**: User object or null if not found

### Instance Methods

#### `save(): bool`
Saves or updates the user in the database.
- **Behavior**:
  - For new users: Hashes password and creates record
  - For existing users: Updates fields, only hashes password if changed
- **Returns**: true on success, false on failure

#### `softDelete(): bool`
Marks the user as deleted without removing from database.
- **Returns**: true on success, false on failure

#### `removeMfa(int $id): bool`
Removes MFA configuration for a user.
- **Parameters**: `$id` - User ID
- **Returns**: true on success, false on failure

### Getters and Setters

- `getId(): ?int`
- `getEmail(): string`
- `setEmail(string $email): void`
- `setPassword(string $password): void`
- `getFirstName(): string`
- `setFirstName(string $first_name): void`
- `getLastName(): string`
- `setLastName(string $last_name): void`
- `isActive(): bool`
- `setIsActive(bool $is_active): void`
- `getCreatedAt(): ?string`
- `getUpdatedAt(): ?string`

## Database Schema

```sql
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT true,
    totp_secret VARCHAR(255),
    totp_setup_complete BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP
);
```

## Security Features

1. **Password Security**:
   - Passwords are hashed using PHP's `password_hash()` with `PASSWORD_DEFAULT`
   - Passwords are never stored in plain text
   - Password hashing only occurs on creation or explicit password changes

2. **Two-Factor Authentication**:
   - TOTP (Time-based One-Time Password) support
   - Configurable setup process
   - Removable MFA configuration

3. **Soft Deletes**:
   - Users are never physically deleted
   - `deleted_at` timestamp marks deletion
   - Preserves referential integrity

## Usage Examples

### Creating a New User
```php
$user = new User(
    'john.doe@example.com',
    'securepassword',
    'John',
    'Doe',
    true
);
$user->save();
```

### Finding and Updating a User
```php
$user = User::findByEmail('john.doe@example.com');
if ($user) {
    $user->setFirstName('Jonathan');
    $user->save();
}
```

### Removing MFA
```php
$user = User::findById(123);
if ($user) {
    $user->removeMfa($user->getId());
}
```

## Error Handling

The model implements comprehensive error handling:
1. Database errors are logged using `error_log()`
2. Failed operations return `false` or `null`
3. PDO exceptions are caught and handled gracefully
4. All database operations are wrapped in try-catch blocks

## Best Practices

1. **Email Management**:
   - Always validate email format
   - Ensure email uniqueness
   - Case-sensitive comparison

2. **Password Management**:
   - Use strong password policies
   - Only hash passwords when necessary
   - Never store plain text passwords

3. **Data Access**:
   - Always use prepared statements
   - Filter deleted users from queries
   - Maintain audit trail through timestamps 