# Server Deployment Guide

## Fixed Issues for Linux/Unix Server Deployment

### 1. Database Connection (✅ FIXED)
**Problem:** Windows is case-insensitive, but Linux/Unix servers are case-sensitive.

**Fixed in:** `settings/db_cred.php`
- Changed `define('SERVER', 'Localhost');` to `define('SERVER', 'localhost');`
- Linux servers require lowercase 'localhost'

### 2. File Path Case Sensitivity

Your application has the following folder structure that must match exactly on the server:
```
├── actions/
├── admin/
├── classes/
├── controllers/
├── CSS/           ← Uppercase (matches current references)
├── db/
├── helpers/
├── js/
├── login/
├── settings/
└── uploads/
```

**Important:** All your CSS references use uppercase `CSS/` which matches your actual folder name. This is correct!

### 3. Potential Issues to Watch

#### A. Old Core File
The file `settings/core.php` contains an old reference:
```php
header("Location: ../Login/login_register.php");
```

This should be updated to match your actual login folder (lowercase):
```php
header("Location: ../login/login.php");
```

**Note:** This file appears to be legacy code and may not be in use.

## Server Configuration Checklist

### Database Setup
1. ✅ Hostname: `localhost` (lowercase)
2. ✅ Username: `tresor.ndala`
3. ✅ Password: `Ndala1950@@`
4. ✅ Database: `ecommerce_2025A_tresor_ndala`

### File Upload Directory
Ensure the server has write permissions for:
```bash
chmod 755 uploads/
chmod 755 uploads/images/
```

### PHP Requirements
- PHP 7.4+ or PHP 8.0+
- MySQLi extension enabled
- Session support enabled
- File upload enabled (`file_uploads = On`)
- Max upload size configured:
  ```ini
  upload_max_filesize = 10M
  post_max_size = 10M
  ```

### Apache/Nginx Configuration

#### For Apache (.htaccess)
```apache
# Enable error display (for debugging only, disable in production)
php_flag display_errors on
php_value error_reporting E_ALL

# File upload settings
php_value upload_max_filesize 10M
php_value post_max_size 10M

# Session settings
php_flag session.auto_start off
```

#### For Production (disable error display)
In your PHP files or server config:
```php
error_reporting(0);
ini_set('display_errors', 0);
```

## Testing Your Deployment

### 1. Test Database Connection
Create a test file `test_connection.php`:
```php
<?php
require_once 'settings/db_cred.php';

$conn = mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);

if ($conn) {
    echo "✅ Database connected successfully!<br>";
    echo "Server: " . SERVER . "<br>";
    echo "Database: " . DATABASE . "<br>";
    mysqli_close($conn);
} else {
    echo "❌ Connection failed: " . mysqli_connect_error();
}
?>
```

### 2. Test File Permissions
```bash
# SSH into your server and run:
cd /path/to/register_sample
ls -la uploads/
ls -la uploads/images/
```

### 3. Test Login
1. Navigate to `http://yourserver.com/register_sample/login/login.php`
2. Try logging in with a test account
3. Check if redirects work correctly

## Common Deployment Errors

### Error: "Can't connect to MySQL server"
**Solution:** Check that MySQL is running and credentials are correct in `settings/db_cred.php`

### Error: "Failed to write file"
**Solution:** Set correct permissions on uploads directory:
```bash
chmod -R 755 uploads/
```

### Error: "File not found" for CSS
**Solution:** Verify the CSS folder is uppercase `CSS/` on the server

### Error: "Session not working"
**Solution:** Ensure PHP sessions are enabled:
```bash
# Check if session directory is writable
ls -la /var/lib/php/sessions/
```

## Security Recommendations

### 1. Protect Sensitive Files
Add to `.htaccess`:
```apache
<FilesMatch "^(db_cred\.php|connection\.php)$">
    Order allow,deny
    Deny from all
</FilesMatch>
```

### 2. Use Environment Variables (Production)
Instead of hardcoding credentials, use environment variables:

Create `.env` file (and add to `.gitignore`):
```
DB_HOST=localhost
DB_USER=tresor.ndala
DB_PASS=Ndala1950@@
DB_NAME=ecommerce_2025A_tresor_ndala
```

Update `db_cred.php`:
```php
<?php
// Load environment variables (requires vlucas/phpdotenv or similar)
define('SERVER', getenv('DB_HOST') ?: 'localhost');
define('USERNAME', getenv('DB_USER') ?: 'tresor.ndala');
define('PASSWD', getenv('DB_PASS') ?: '');
define('DATABASE', getenv('DB_NAME') ?: 'ecommerce_2025A_tresor_ndala');
?>
```

### 3. Disable Directory Listing
Add to `.htaccess`:
```apache
Options -Indexes
```

## Post-Deployment Checklist

- [ ] Database connection working
- [ ] Login/Registration working
- [ ] File uploads working (test product images)
- [ ] All CSS files loading correctly
- [ ] Session management working
- [ ] Admin panel accessible
- [ ] Product CRUD operations working
- [ ] Search functionality working
- [ ] Error logging enabled (not displayed to users)
- [ ] Backup strategy in place

## Support

If you encounter issues, check:
1. PHP error logs: `/var/log/apache2/error.log` or `/var/log/php-fpm/error.log`
2. Database logs: `/var/log/mysql/error.log`
3. Application logs: Enable error logging in PHP

---
**Last Updated:** 2025-11-02
**Application:** Taste of Africa E-Commerce Platform

