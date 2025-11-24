# SACLI FOUNDIT

A comprehensive Lost and Found management system built with Laravel 11, designed to help communities track and recover lost items efficiently.

## Table of Contents

-   [About](#about)
-   [Features](#features)
-   [Technology Stack](#technology-stack)
-   [Installation](#installation)
-   [Configuration](#configuration)
-   [Admin Management](#admin-management)
-   [User Guide](#user-guide)
-   [Admin Guide](#admin-guide)
-   [Chat Support System](#chat-support-system)
-   [Testing](#testing)
-   [Deployment](#deployment)
-   [Contributing](#contributing)
-   [License](#license)

## About

SACLI FOUNDIT is a modern web application that streamlines the process of reporting and finding lost items. The platform provides a user-friendly interface for submitting lost or found items, searching through listings, and connecting with other users through an integrated chat support system.

## Features

### For Users

-   **Item Submission**: Report lost or found items with detailed descriptions and images
-   **Advanced Search**: Search items by category, location, date, and keywords
-   **User Dashboard**: Manage your submitted items and track their status
-   **Real-time Chat Support**: Get help from administrators through live chat
-   **Email Notifications**: Receive updates when your items are verified or resolved
-   **Responsive Design**: Fully optimized for mobile, tablet, and desktop devices

### For Administrators

-   **Separate Admin Portal**: Dedicated authentication system with independent sessions
-   **Item Verification**: Review and verify submitted items before they go public
-   **Category Management**: Create and manage item categories
-   **Statistics Dashboard**: View comprehensive analytics and reports
-   **Chat Management**: Respond to user inquiries through the chat system
-   **Notification System**: Receive alerts for new submissions and chat messages
-   **Bulk Operations**: Efficiently manage multiple items at once

## Technology Stack

-   **Backend**: Laravel 11 (PHP 8.2+)
-   **Frontend**: Blade Templates, Alpine.js, Tailwind CSS
-   **Database**: MySQL/MariaDB
-   **Authentication**: Laravel Breeze with Multi-Guard Authentication
-   **Real-time Features**: AJAX Polling for chat and notifications
-   **Email**: Laravel Mail with queue support
-   **File Storage**: Laravel Storage (local/S3)
-   **Testing**: Pest PHP

## Installation

### Prerequisites

-   PHP 8.2 or higher
-   Composer
-   Node.js 18+ and NPM
-   MySQL 8.0+ or MariaDB 10.3+
-   Git

### Step 1: Clone the Repository

```bash
git clone <repository-url>
cd sacli-foundit
```

### Step 2: Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

### Step 3: Environment Configuration

```bash
# Copy the environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Configure Database

Edit the `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sacli_foundit
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Step 5: Run Migrations

```bash
# Run database migrations
php artisan migrate

# (Optional) Seed the database with sample data
php artisan db:seed
```

### Step 6: Storage Setup

```bash
# Create symbolic link for storage
php artisan storage:link
```

### Step 7: Build Assets

```bash
# Build frontend assets
npm run build

# Or for development with hot reload
npm run dev
```

### Step 8: Start the Application

```bash
# Start the development server
php artisan serve
```

The application will be available at `http://localhost:8000`

## Configuration

### Mail Configuration

Configure your mail settings in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@saclifoundit.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Queue Configuration

For production, configure a queue driver:

```env
QUEUE_CONNECTION=database
```

Run the queue worker:

```bash
php artisan queue:work
```

### File Upload Limits

Configure upload limits in `php.ini`:

```ini
upload_max_filesize = 10M
post_max_size = 10M
```

## Admin Management

### Creating Admin Accounts

SACLI FOUNDIT uses a separate admin authentication system. Admins are managed through Artisan commands.

#### Create a New Admin

```bash
php artisan admin:create
```

You'll be prompted to enter:

-   Name
-   Email address
-   Password

Example:

```bash
php artisan admin:create

 Name:
 > John Admin

 Email:
 > admin@example.com

 Password:
 > ********

Admin created successfully!
```

#### List All Admins

```bash
php artisan admin:list
```

This displays all admin accounts with their IDs, names, and email addresses.

#### Delete an Admin

```bash
php artisan admin:delete
```

You'll be prompted to enter the admin's email address to delete.

### Admin Login

Admins access the system through a separate login portal:

1. Navigate to `/admin/login`
2. Enter your admin credentials
3. Access the admin dashboard at `/admin/dashboard`

**Important**: Admin sessions are completely separate from user sessions. You can be logged in as both an admin and a regular user simultaneously without conflicts.

## User Guide

### Registering an Account

1. Click "Register" on the homepage
2. Fill in your details (name, email, password, course, year)
3. Verify your email address (if email verification is enabled)
4. Log in to access your dashboard

### Submitting an Item

1. Log in to your account
2. Click "Submit Item" or navigate to `/items/create`
3. Select item type (Lost or Found)
4. Choose a category
5. Fill in item details:
    - Title
    - Description
    - Location
    - Date occurred
    - Contact information
6. Upload up to 5 images
7. Submit for admin verification

### Searching for Items

1. Use the search bar on the homepage
2. Filter by:
    - Item type (Lost/Found)
    - Category
    - Location
    - Date range
    - Keywords
3. Browse results and click for details
4. Contact the submitter if you find a match

### Using Chat Support

1. Click the chat icon (bottom right) while logged in
2. Type your message and press Enter
3. Wait for an admin response
4. View chat history anytime by clicking the chat icon

## Admin Guide

### Admin Dashboard

The admin dashboard provides:

-   Total items count
-   Pending items requiring verification
-   Verified items count
-   Monthly statistics
-   Category breakdown
-   Recent items list
-   Items requiring attention (pending > 7 days)

### Verifying Items

1. Navigate to "Pending Items"
2. Review item details and images
3. Click "Verify" to approve or "Reject" to decline
4. Add optional notes when rejecting
5. User receives email notification of the decision

### Managing Categories

1. Go to "Categories" in admin navigation
2. View existing categories with item counts
3. Create new categories with name and description
4. Edit or delete categories as needed
5. Categories are used for organizing items

### Viewing Statistics

Access comprehensive analytics:

-   Items by status (pending, verified, resolved, rejected)
-   Items by type (lost vs found)
-   Items by category
-   Submission trends over time
-   User activity metrics

### Managing Chat Support

1. Navigate to "Chat Support" in admin menu
2. View all active chat sessions
3. See unread message indicators
4. Click a session to view conversation
5. Type responses and send to users
6. Messages update in real-time via polling

### Notification System

Admins receive notifications for:

-   New item submissions
-   New chat messages from users
-   Pending queue alerts (items waiting > 24 hours)
-   System events

**Managing Notifications**:

1. Click the bell icon in admin header
2. View recent notifications
3. Click to view details or mark as read
4. Access full notification history at `/admin/notifications/page`

**Notification Preferences**:
Admins can customize which notifications they receive (feature coming soon).

## Chat Support System

### How It Works

The chat system uses AJAX polling to provide near-real-time communication:

1. **User Side**:

    - Users click the chat widget to open the interface
    - Messages are sent via AJAX POST requests
    - New messages are fetched every 3-5 seconds
    - Unread count updates automatically

2. **Admin Side**:

    - Admins see all chat sessions in the Chat Support dashboard
    - Sessions show user info and unread message counts
    - Clicking a session opens the conversation
    - Admins can respond directly in the interface
    - New user messages trigger notifications

3. **Technical Details**:
    - Messages stored in `chat_messages` table
    - Sessions tracked in `chat_sessions` table
    - Polymorphic relationships support both user and admin senders
    - Read receipts track when messages are viewed

## Testing

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run specific test file
php artisan test --filter=AdminAuthenticationTest

# Run with coverage
php artisan test --coverage
```

### Test Suites

-   **Feature Tests**: End-to-end functionality tests
-   **Unit Tests**: Individual component tests
-   **Browser Tests**: (Optional) Dusk tests for UI

### Key Test Areas

-   User authentication and registration
-   Admin authentication (separate guard)
-   Item submission workflow
-   Search functionality
-   Chat system
-   Notification system
-   Admin verification workflow

## Deployment

### Production Checklist

1. **Environment**:

    ```bash
    APP_ENV=production
    APP_DEBUG=false
    APP_URL=https://yourdomain.com
    ```

2. **Optimize Application**:

    ```bash
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    composer install --optimize-autoloader --no-dev
    ```

3. **Database**:

    - Run migrations: `php artisan migrate --force`
    - Backup database regularly

4. **Queue Worker**:

    - Set up supervisor to run queue worker
    - Configure `QUEUE_CONNECTION=database` or `redis`

5. **Cron Jobs**:
   Add to crontab:

    ```bash
    * * * * * cd /path-to-app && php artisan schedule:run >> /dev/null 2>&1
    ```

6. **File Permissions**:

    ```bash
    chmod -R 775 storage bootstrap/cache
    chown -R www-data:www-data storage bootstrap/cache
    ```

7. **SSL Certificate**:

    - Install SSL certificate (Let's Encrypt recommended)
    - Force HTTPS in production

8. **Monitoring**:
    - Set up error logging (Sentry, Bugsnag, etc.)
    - Monitor queue jobs
    - Set up uptime monitoring

## Architecture Overview

### Authentication System

The application uses Laravel's multi-guard authentication:

-   **Web Guard**: For regular users

    -   Uses `users` table
    -   Session-based authentication
    -   Routes: `/login`, `/register`, `/dashboard`

-   **Admin Guard**: For administrators
    -   Uses `admins` table
    -   Separate session management
    -   Routes: `/admin/login`, `/admin/dashboard`
    -   Middleware: `admin` (checks admin guard)

### Directory Structure

```
app/
├── Console/Commands/      # Artisan commands (admin management)
├── Http/
│   ├── Controllers/
│   │   ├── Admin/        # Admin controllers
│   │   └── ...           # User controllers
│   └── Middleware/       # Custom middleware
├── Models/               # Eloquent models
├── Notifications/        # Email/database notifications
├── Repositories/         # Data access layer
└── Services/            # Business logic layer

resources/
├── views/
│   ├── admin/           # Admin views
│   ├── auth/            # Authentication views
│   ├── chat/            # Chat interface
│   ├── items/           # Item management
│   └── public/          # Public pages

database/
├── migrations/          # Database migrations
├── factories/           # Model factories
└── seeders/            # Database seeders
```

## Troubleshooting

### Common Issues

**Issue**: "Class not found" errors

```bash
composer dump-autoload
```

**Issue**: Permission denied on storage

```bash
chmod -R 775 storage bootstrap/cache
```

**Issue**: Assets not loading

```bash
npm run build
php artisan storage:link
```

**Issue**: Queue jobs not processing

```bash
php artisan queue:work --tries=3
```

**Issue**: Admin can't log in

-   Verify admin exists: `php artisan admin:list`
-   Create new admin: `php artisan admin:create`
-   Check database connection

## Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Coding Standards

-   Follow PSR-12 coding standards
-   Write tests for new features
-   Update documentation as needed
-   Use meaningful commit messages

## Security

If you discover a security vulnerability, please email security@saclifoundit.com. All security vulnerabilities will be promptly addressed.

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support and questions:

-   Email: support@saclifoundit.com
-   Documentation: [Link to docs]
-   Issues: [GitHub Issues]

## Acknowledgments

Built with:

-   [Laravel](https://laravel.com) - The PHP Framework
-   [Tailwind CSS](https://tailwindcss.com) - Utility-first CSS framework
-   [Alpine.js](https://alpinejs.dev) - Lightweight JavaScript framework
-   [Heroicons](https://heroicons.com) - Beautiful hand-crafted SVG icons

---

**Version**: 1.0.0  
**Last Updated**: November 2025
