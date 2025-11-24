# Admin System Migration

## Overview

This document describes the migration from a role-based admin system to a separate admin authentication system.

## Changes Made

### 1. Database Migration

Created migration `2025_11_21_100000_migrate_admin_users_to_admins_table.php` that:

-   Copies all users with `role='admin'` from the `users` table to the `admins` table
-   Preserves all user data (name, email, password hash, timestamps)
-   Adds a `role_deprecated` flag to the users table to mark migrated admin accounts
-   Does not delete the original admin users from the users table (for safety)

### 2. Route Updates

Updated `routes/web.php`:

-   Removed the `'auth'` middleware from admin routes (was `['auth', 'admin']`, now just `['admin']`)
-   The `'admin'` middleware now checks `auth()->guard('admin')->check()` instead of role-based checks
-   Admin routes now properly use the admin guard authentication

### 3. Controller Updates

Updated `app/Http/Controllers/AdminController.php`:

-   Changed all `auth()->user()` calls to `auth()->guard('admin')->user()`
-   Updated type hints from `\App\Models\User` to `\App\Models\Admin`
-   Ensures all admin operations use the admin guard

### 4. Middleware

The `AdminMiddleware` was already updated in previous tasks to use the admin guard:

```php
if (!auth()->guard('admin')->check()) {
    return redirect()->route('admin.login');
}
```

### 5. Test Updates

Updated `tests/Feature/AdminVerificationWorkflowTest.php`:

-   Changed from using `User::factory()->create(['role' => 'admin'])` to `Admin::factory()->create()`
-   Updated all `actingAs($this->admin)` calls to `actingAs($this->admin, 'admin')` to specify the guard
-   Updated assertions to expect redirects to `admin.login` instead of `login` or `403` responses

## Migration Status

✅ Admin users migrated from users table to admins table
✅ Routes updated to use admin guard
✅ Controllers updated to use admin guard
✅ Middleware already using admin guard
✅ Tests updated to use Admin model and admin guard
✅ Admin authentication tests passing (8/8)

## Backward Compatibility

-   The `role` column in the users table is preserved (not dropped)
-   A `role_deprecated` flag is added to mark migrated admin accounts
-   Original admin users remain in the users table for rollback purposes
-   The migration can be reversed if needed

## Testing

Run the following tests to verify the migration:

```bash
php artisan test tests/Feature/AdminAuthenticationTest.php
php artisan test tests/Feature/AdminDashboardTest.php
```

## Next Steps

1. Monitor the system for any issues with admin authentication
2. After confirming everything works, consider removing the `role` column from users table in a future migration
3. Update any remaining code that references user roles for admin checks
4. Consider adding admin-specific features that leverage the separate authentication system
