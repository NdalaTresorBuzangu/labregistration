# Hostinger Setup Instructions

## HTTP 500 Error - Common Causes and Fixes

### 1. **Update Database Credentials**

The file `settings/db_cred.php` currently has LOCAL XAMPP credentials. You MUST update it with your Hostinger database credentials:

```php
// settings/db_cred.php
if (!defined('SERVER')) {
    define('SERVER', 'localhost'); // Usually 'localhost' on Hostinger
}

if (!defined('USERNAME')) {
    define('USERNAME', 'your_hostinger_db_username'); // From Hostinger control panel
}

if (!defined('PASSWD')) {
    define('PASSWD', 'your_hostinger_db_password'); // From Hostinger control panel
}

if (!defined('DATABASE')) {
    define('DATABASE', 'your_hostinger_db_name'); // From Hostinger control panel
}
```

**How to find your Hostinger database credentials:**
1. Log into Hostinger control panel (hPanel)
2. Go to **Databases** → **MySQL Databases**
3. Find your database name, username, and password
4. Update `settings/db_cred.php` with these values

### 2. **Check File Permissions**

On Linux servers (like Hostinger), file permissions matter:
- PHP files should be: `644` or `755`
- Folders should be: `755`
- Uploads folder should be: `755` or `777` (if you need write access)

### 3. **Check PHP Version**

Hostinger should support PHP 7.4 or higher. Check your PHP version in hPanel.

### 4. **Enable Error Logging**

To see the actual error:
1. Upload `error_check.php` to your server
2. Visit: `https://your-domain.com/error_check.php`
3. This will show you exactly what's wrong

### 5. **Common Path Issues**

Since `login.php` and `register.php` are now in the root:
- ✅ `settings/connection.php` - should work
- ✅ `controllers/user_controller.php` - should work
- ✅ `CSS/app.css` - should work
- ✅ `js/register.js` - should work

### 6. **Check .htaccess (if exists)**

If you have an `.htaccess` file, make sure it's not blocking PHP execution.

### 7. **Test Database Connection**

After updating credentials, test the connection by visiting:
- `error_check.php` (will test database connection)

### 8. **Check Server Error Logs**

In Hostinger hPanel:
1. Go to **Files** → **File Manager**
2. Look for error logs in your domain folder
3. Or check **Advanced** → **Error Logs** in hPanel

## Quick Fix Checklist

- [ ] Update `settings/db_cred.php` with Hostinger database credentials
- [ ] Upload `error_check.php` and visit it to see the actual error
- [ ] Check file permissions (should be 644 for files, 755 for folders)
- [ ] Verify all files are uploaded (especially `settings/`, `controllers/`, `classes/` folders)
- [ ] Check PHP version in Hostinger (should be 7.4+)
- [ ] Check server error logs in Hostinger hPanel

## If Still Getting 500 Error

1. Visit `error_check.php` to see the exact error
2. Check Hostinger error logs
3. Make sure database credentials are correct
4. Verify all required PHP files are uploaded

