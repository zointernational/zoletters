# ZO Letters Deployment Guide

## Deployment Status Report

**Date:** July 3, 2026  
**Version:** 1.2.0  
**Target:** InterServer Shared Hosting

---

## Deployment Issue

The automated deployment script encountered connection issues when connecting to the server:

1. **SSH Connection**: Authentication failed
2. **FTP Connection**: Timeout errors (worked earlier during file upload)
3. **cPanel**: Could not access (DNS issues with subdomain)

### Actions Completed

✅ Repository files uploaded to: `/domains/locationshub.co.in/public_html/zoletters`

### Actions Required (Manual Steps)

Since automated connection is unavailable, please follow these manual steps:

---

## Manual Deployment Instructions

### Step 1: Access cPanel

1. Go to: https://vda8100.is.cc:2083
2. Login with your cPanel credentials
3. Navigate to **File Manager**

### Step 2: Verify Files Uploaded

Check if files are in: `/public_html/zoletters/`

Expected files:
- `artisan`
- `composer.json`
- `.env.example`
- `app/` directory
- `config/` directory
- `database/` directory
- `public/` directory
- `resources/` directory
- `routes/` directory
- `storage/` directory

If files are not there, you need to upload them via File Manager or another method.

### Step 3: Create Database

1. In cPanel, go to **MySQL Databases**
2. Create a new database: `location_zoletters`
3. Create a user and assign all privileges
4. Note down the credentials

### Step 4: Create .env File

1. In File Manager, navigate to `/public_html/zoletters/`
2. Create a new file named `.env`
3. Copy the content below (replace DB credentials with your actual database info):

```env
APP_NAME="ZO Letters"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://letters.zointernational.in

LOG_CHANNEL=daily
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=location_zoletters
DB_USERNAME=location_zoletters_user
DB_PASSWORD=your_database_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

### Step 5: Open Terminal in cPanel

1. In cPanel, find **Terminal** (under Advanced)
2. Click to open terminal
3. Run the following commands:

```bash
cd /home/location/domains/locationshub.co.in/public_html/zoletters

# Set permissions
chmod 755 storage bootstrap/cache public/uploads

# Install composer dependencies
composer install --no-dev --optimize-autoloader

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Create storage link
php artisan storage:link

# Set correct permissions
chmod -R 775 storage bootstrap/cache
chmod 644 .env
```

### Step 6: Create Admin User

Run this SQL in **phpMyAdmin** (cPanel → Databases → phpMyAdmin):

```sql
-- Create users table
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create admin user
INSERT INTO `users` (`name`, `email`, `password`, `created_at`, `updated_at`)
VALUES ('Admin', 'admin@zointernational.in', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW())
ON DUPLICATE KEY UPDATE `password` = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
```

**Note:** The password hash above is for `password` - you should generate a new one or update it:

```bash
# In terminal, run:
php artisan tinker --execute="echo bcrypt('Admin@123');"
```

Then copy the output and update the password in phpMyAdmin.

### Step 7: Configure Subdomain

1. In cPanel, go to **Domains** → **Subdomains**
2. Create subdomain: `letters.zointernational.in`
3. Document root: `/public_html/zoletters/public`
4. Click **Create**

### Step 8: SSL Certificate

1. In cPanel, go to **SSL/TLS**
2. Click **Manage SSL Sites**
3. Install certificate for `letters.zointernational.in`
4. Or use **Let's Encrypt** for free SSL

### Step 9: Test Application

1. Visit: https://letters.zointernational.in
2. You should see the ZO Letters dashboard
3. Login with:
   - Email: `admin@zointernational.in`
   - Password: `Admin@123`

---

## Troubleshooting

### Error: "No such file or directory"

Check that the subdomain document root points to `/public_html/zoletters/public`

### Error: "403 Forbidden"

Check file permissions and .htaccess in public directory

### Error: "Database connection failed"

Verify database credentials in .env file match cPanel MySQL settings

### Error: "Class not found"

Run `composer install` to install dependencies

---

## Expected Results

After successful deployment:

| Component | Status |
|-----------|--------|
| Dashboard | ✅ Working |
| Templates CRUD | ✅ Working |
| Documents CRUD | ✅ Working |
| PDF Generation | ✅ Working |
| Settings | ✅ Working |
| Search/Filter | ✅ Working |
| Status Management | ✅ Working |
| Archive/Restore | ✅ Working |

---

## Post-Deployment Checklist

- [ ] Change admin password immediately
- [ ] Update company information in Settings
- [ ] Create first template
- [ ] Test document creation
- [ ] Verify PDF generation
- [ ] Configure backup strategy

---

## Support

For issues, please provide:
1. Error messages
2. Server logs (in `/storage/logs/`)
3. PHP error logs
