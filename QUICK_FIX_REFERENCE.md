# Quick Fix Reference - Case Sensitivity Issues

## 🚨 Critical Fixes Applied

### 1. Database Connection Fix (MOST IMPORTANT)
**File:** `settings/db_cred.php`  
**Line 7:** Changed from `'Localhost'` to `'localhost'`

```php
// ❌ WRONG (Windows only)
define('SERVER', 'Localhost');

// ✅ CORRECT (Linux/Unix compatible)
define('SERVER', 'localhost');
```

**Why?** Linux/Unix servers are case-sensitive. MySQL on Linux requires lowercase `'localhost'`.

---

## 🔍 File Path Case Sensitivity

### Your Folder Structure (Case Matters!)
```
register_sample/
├── actions/          ← lowercase
├── admin/           ← lowercase
├── classes/         ← lowercase
├── controllers/     ← lowercase
├── CSS/            ← UPPERCASE (correct!)
├── db/             ← lowercase
├── helpers/        ← lowercase
├── js/             ← lowercase
├── login/          ← lowercase (not Login!)
├── settings/       ← lowercase
└── uploads/        ← lowercase
```

### ⚠️ Common Mistakes to Avoid

#### Wrong:
```php
include "../Login/login.php";        // Wrong capital L
require_once "../Settings/core.php";  // Wrong capital S
header("Location: ../Admin/index.php"); // Wrong capital A
```

#### Correct:
```php
include "../login/login.php";         // ✅
require_once "../settings/core.php";  // ✅
header("Location: ../admin/index.php"); // ✅
```

---

## 📋 All Fixed Files

### 1. ✅ `settings/db_cred.php`
- Changed `'Localhost'` → `'localhost'`

### 2. ✅ `settings/core.php`
- Changed `../Login/login_register.php` → `../login/login.php`

### 3. ✅ Created `.htaccess`
- Security configuration
- File upload settings
- Protected sensitive files

### 4. ✅ Created Deployment Documentation
- `SERVER_DEPLOYMENT_GUIDE.md`
- `DEPLOYMENT_CHECKLIST.md`
- `test_connection.php`

---

## 🧪 Test Your Deployment

### Quick Test Steps:

1. **Upload files to server**
2. **Visit test page:**
   ```
   http://yourserver.com/register_sample/test_connection.php
   ```
3. **Look for:** ✅ SUCCESS! message
4. **If you see errors**, check:
   - MySQL is running
   - Database exists
   - Credentials are correct
   - Server name is lowercase `'localhost'`

5. **Delete test file after testing:**
   ```bash
   rm test_connection.php
   ```

---

## 🔧 Troubleshooting

### Error: "Can't connect to MySQL server on 'Localhost'"
**Cause:** Case-sensitive hostname  
**Fix:** Change `'Localhost'` to `'localhost'` in `settings/db_cred.php`

### Error: "Warning: require(../Settings/core.php): failed to open stream"
**Cause:** Wrong folder capitalization  
**Fix:** Use lowercase: `../settings/core.php`

### Error: "404 Not Found" for login pages
**Cause:** Login folder reference with capital L  
**Fix:** Use lowercase: `login/login.php` not `Login/login.php`

### CSS Files Not Loading
**Cause:** CSS folder IS uppercase, so this is correct  
**Current:** `CSS/app.css` ✅ (correct!)  
**Don't change** the CSS references - they match your folder name

---

## 💾 File Permissions on Server

After uploading, set these permissions:

```bash
# Navigate to your app directory
cd /path/to/register_sample

# Set folder permissions
chmod 755 -R .

# Set upload directories to writable
chmod 777 uploads/
chmod 777 uploads/images/

# Protect sensitive files
chmod 600 settings/db_cred.php
```

---

## 🔐 Security Checklist

- [ ] `.htaccess` file uploaded
- [ ] `settings/db_cred.php` not accessible via browser
- [ ] Error display disabled in production
- [ ] SQL files not downloadable
- [ ] Uploads folder has correct permissions
- [ ] Test file deleted after testing

---

## 📞 Still Having Issues?

1. **Check server error logs:**
   ```bash
   tail -f /var/log/apache2/error.log
   # or
   tail -f /var/log/php-fpm/error.log
   ```

2. **Check MySQL logs:**
   ```bash
   tail -f /var/log/mysql/error.log
   ```

3. **Test database connection directly:**
   ```bash
   mysql -u tresor.ndala -p -h localhost ecommerce_2025A_tresor_ndala
   ```

---

## ✅ Deployment Summary

**What was wrong:**
- Database server hostname was `'Localhost'` (uppercase L) - doesn't work on Linux
- One old file had wrong login path with capital L

**What was fixed:**
1. Changed to `'localhost'` (lowercase) in `db_cred.php`
2. Fixed login path in `core.php`
3. Added server security via `.htaccess`
4. Created deployment documentation
5. Created test tools

**Your app is now ready for Linux/Unix server deployment! 🚀**


