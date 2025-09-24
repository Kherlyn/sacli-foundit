# SACLI FOUNDIT - Admin Management Commands

This document describes the available Artisan commands for managing admin users in the SACLI FOUNDIT application.

## Available Commands

### Create Admin User

Create a new admin user or promote an existing user to admin.

```bash
php artisan admin:create
```

#### Options

-   `--name="Admin Name"` - The name of the admin user
-   `--email="admin@example.com"` - The email address of the admin user
-   `--password="securepassword"` - The password for the admin user
-   `--force` - Force creation even if user exists (updates existing user)

#### Examples

**Interactive Mode (Recommended for Production):**

```bash
php artisan admin:create
```

This will prompt you for name, email, and password securely.

**Command Line Mode (Good for Development):**

```bash
php artisan admin:create --name="John Admin" --email="john@example.com" --password="mypassword123"
```

**Force Update Existing User:**

```bash
php artisan admin:create --email="existing@example.com" --name="New Name" --password="newpassword" --force
```

#### Features

-   ✅ Input validation (email format, password length, etc.)
-   ✅ Prevents duplicate admin creation (unless --force is used)
-   ✅ Can promote existing regular users to admin
-   ✅ Auto-verifies admin user emails
-   ✅ Secure password input in interactive mode
-   ✅ Detailed success/error messages

### List Admin Users

Display all admin users or all users in the system.

```bash
php artisan admin:list
```

#### Options

-   `--all` - Show all users (both admin and regular users)

#### Examples

**List Only Admin Users:**

```bash
php artisan admin:list
```

**List All Users:**

```bash
php artisan admin:list --all
```

#### Features

-   📊 Tabular display with user details
-   📈 Summary statistics
-   ✅ Shows verification status
-   🕒 Shows creation and last login dates
-   💡 Helpful command suggestions

## Security Notes

1. **Password Security**: Always use strong passwords for admin accounts
2. **Email Verification**: Admin users are auto-verified, but regular users need email verification
3. **Production Usage**: Use interactive mode in production to avoid password exposure in command history
4. **Regular Audits**: Use `admin:list` regularly to audit admin access

## Troubleshooting

### Common Issues

**"User already exists" Error:**

-   Use `--force` flag to update existing user
-   Or use the promote option when prompted

**"Validation failed" Error:**

-   Check email format is valid
-   Ensure password is at least 8 characters
-   Verify name is provided and not empty

**Permission Issues:**

-   Ensure database connection is working
-   Check that the users table exists (run migrations)
-   Verify proper file permissions

### Getting Help

```bash
php artisan help admin:create
php artisan help admin:list
```

## Integration with Application

Once an admin user is created, they can:

1. **Login** at `/login` with their email and password
2. **Access Admin Panel** at `/admin/dashboard`
3. **Manage Items** - Verify, reject, or resolve submitted items
4. **View Statistics** - Access comprehensive analytics
5. **Manage Categories** - Add, edit, or remove item categories
6. **Handle Notifications** - Manage system notifications

## Example Workflow

```bash
# 1. Create your first admin user
php artisan admin:create

# 2. Verify the user was created
php artisan admin:list

# 3. Login to the web interface
# Navigate to: http://your-domain.com/login
# Use the email and password you just created

# 4. Access admin panel
# Navigate to: http://your-domain.com/admin/dashboard
```

---

**Note**: These commands are part of the SACLI FOUNDIT lost and found management system. For more information about the application features, see the main documentation.
