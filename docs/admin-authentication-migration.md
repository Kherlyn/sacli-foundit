# Admin Authentication System Migration Guide

## Overview

The admin authentication system has been updated to use Laravel's multi-guard authentication instead of the previous role-based approach. This provides better security and separation of concerns between regular users and administrators.

## What's Changed

### Before (Role-Based)

-   Admins were regular users with `role = 'admin'` in the users table
-   Admin routes checked `Auth::user()->isAdmin()`
-   Single authentication guard for both users and admins

### After (Multi-Guard)

-   Admins have their own `admins` table and `Admin` model
-   Admin routes use the `admin` guard: `auth()->guard('admin')->check()`
-   Separate authentication sessions for users and admins
-   Dedicated admin login at `/admin/login`

## Implementation Status

### ✅ Completed

1. **Database Schema**

    - `admins` table created
    - `admin_password_reset_tokens` table created
    - Admin model with proper authentication

2. **Authentication System**

    - Multi-guard configuration in `config/auth.php`
    - Admin authentication controller (`Admin\AuthController`)
    - Admin login view at `resources/views/admin/auth/login.blade.php`
    - Admin middleware (`AdminMiddleware`)

3. **Routes**

    - `routes/admin.php` created with admin authentication routes
    - Admin routes properly configured in `bootstrap/app.php`
    - Admin dashboard route using new authentication

4. **Controllers**

    - `Admin\AuthController` for login/logout
    - `Admin\DashboardController` for admin dashboard

5. **Views**

    - Admin login view
    - Admin dashboard updated to work with new authentication
    - Navigation updated to check admin guard

6. **Tests**
    - Comprehensive admin authentication tests
    - Admin dashboard tests
    - Session independence tests

### 🔄 In Progress / To Do

The following admin routes in `routes/web.php` still use the old role-based authentication and should be migrated:

```php
Route::middleware(['auth', 'admin', 'throttle:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->controller(AdminController::class)
    ->group(function () {
        // These routes need to be migrated to routes/admin.php
        Route::get('/pending-items', 'pendingItems')->name('pending-items');
        Route::post('/items/{item}/verify', 'verify')->name('items.verify');
        Route::post('/items/bulk-action', 'bulkAction')->name('items.bulk-action');
        Route::get('/items', 'items')->name('items');
        Route::get('/items/{item}', 'showItem')->name('items.show');
        Route::get('/categories', 'categories')->name('categories');
        Route::post('/categories', 'storeCategory')->name('categories.store');
        Route::put('/categories/{category}', 'updateCategory')->name('categories.update');
        Route::delete('/categories/{category}', 'deleteCategory')->name('categories.destroy');
        Route::get('/statistics', 'statistics')->name('statistics');
        Route::get('/statistics/data', 'statisticsData')->name('statistics.data');
        Route::get('/statistics/export', 'exportStatistics')->name('statistics.export');
        Route::get('/notifications', 'notifications')->name('notifications');
        Route::post('/notifications/mark-read', 'markNotificationsRead')->name('notifications.mark-read');
        Route::post('/notifications/test', 'sendTestNotification')->name('notifications.test');
    });
```

## Migration Steps for Remaining Routes

### Step 1: Move Routes

Move the routes from `routes/web.php` to `routes/admin.php` under the admin middleware group.

### Step 2: Update Controllers

If needed, move controller methods from `AdminController` to appropriate controllers in the `Admin` namespace.

### Step 3: Update Middleware

Change from:

```php
Route::middleware(['auth', 'admin'])
```

To:

```php
Route::middleware('admin')  // AdminMiddleware already checks admin guard
```

### Step 4: Update Views

Ensure views use the admin guard for authentication checks:

**Before:**

```blade
@if (Auth::user()->isAdmin())
```

**After:**

```blade
@if (auth()->guard('admin')->check())
```

### Step 5: Test

Run the test suite to ensure all functionality works:

```bash
php artisan test --filter="Admin"
```

## Creating Admin Accounts

Use the artisan command to create admin accounts:

```bash
php artisan admin:create
```

Or programmatically:

```php
use App\Models\Admin;

Admin::create([
    'name' => 'Admin Name',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
]);
```

## Accessing Admin Panel

-   **Admin Login:** `/admin/login`
-   **Admin Dashboard:** `/admin/dashboard`
-   **Admin Logout:** POST to `/admin/logout`

## Session Management

Admins and users maintain separate sessions:

-   An admin can be logged into the admin panel while a user is logged into the main site
-   Sessions are completely independent
-   Logging out from one does not affect the other

## Security Considerations

1. **Separate Tables:** Admins are in a separate table from users
2. **Separate Guards:** Admin authentication uses the `admin` guard
3. **Separate Sessions:** Admin and user sessions are independent
4. **Dedicated Login:** Admin login is at a separate URL
5. **Middleware Protection:** All admin routes are protected by `AdminMiddleware`

## Backward Compatibility

During the migration period:

-   Old role-based admin routes continue to work
-   New admin guard routes are available
-   Both systems can coexist temporarily
-   Gradual migration is supported

## Testing

Run admin-specific tests:

```bash
# All admin tests
php artisan test --filter="Admin"

# Authentication tests
php artisan test tests/Feature/AdminAuthenticationTest.php

# Dashboard tests
php artisan test tests/Feature/AdminDashboardTest.php
```

## Troubleshooting

### Admin can't log in

-   Verify admin exists in `admins` table (not `users` table)
-   Check password is properly hashed
-   Verify `admin` guard is configured in `config/auth.php`

### Admin routes redirect to user login

-   Ensure routes use `admin` middleware
-   Check `AdminMiddleware` is registered in `bootstrap/app.php`
-   Verify route is in `routes/admin.php` or uses admin guard

### Session conflicts

-   Clear sessions: `php artisan session:clear`
-   Clear cache: `php artisan cache:clear`
-   Regenerate config: `php artisan config:clear`

## Next Steps

1. Migrate remaining admin routes from `web.php` to `admin.php`
2. Create admin-specific controllers in `Admin` namespace
3. Update all admin views to use admin guard
4. Remove role-based authentication code
5. Remove `role` column from users table (optional)
