# Server Deployment Checklist

Use this checklist when deploying to your Linux/Unix server.

## ✅ Pre-Deployment

- [ ] All changes committed to version control
- [ ] Database credentials updated in `settings/db_cred.php`
- [ ] Test locally with XAMPP/WAMP
- [ ] Backup existing server data (if updating)

## ✅ Files to Upload

Upload these files/folders to your server:
- [ ] `actions/` folder
- [ ] `admin/` folder
- [ ] `classes/` folder
- [ ] `controllers/` folder
- [ ] `CSS/` folder (uppercase)
- [ ] `db/` folder (SQL files)
- [ ] `helpers/` folder
- [ ] `js/` folder
- [ ] `login/` folder
- [ ] `settings/` folder
- [ ] `uploads/` folder structure
- [ ] Root PHP files (index.php, logout.php, etc.)
- [ ] `.htaccess` file
- [ ] `test_connection.php` (for testing only)

## ✅ Server Configuration

### 1. File Permissions
```bash
# Set correct permissions
chmod 755 -R /path/to/register_sample
chmod 777 uploads/
chmod 777 uploads/images/
```

### 2. Database Setup
- [ ] MySQL/MariaDB is running
- [ ] Database created: `ecommerce_2025A_tresor_ndala`
- [ ] User created with proper privileges: `tresor.ndala`
- [ ] SQL schema imported from `db/dbforlab.sql` (see `db/IMPORT_INSTRUCTIONS.md`)
  - [ ] Login to phpMyAdmin
  - [ ] Select database `ecommerce_2025A_tresor_ndala`
  - [ ] Import tab → Choose file `dbforlab.sql`
  - [ ] Click "Go" and wait for success
  - [ ] Verify 9 tables created
- [ ] Test data loaded (optional)

### 3. PHP Configuration
Check these settings in `php.ini`:
- [ ] `file_uploads = On`
- [ ] `upload_max_filesize = 10M`
- [ ] `post_max_size = 10M`
- [ ] `max_execution_time = 300`
- [ ] `mysqli` extension enabled
- [ ] `session.auto_start = 0`

## ✅ Testing Phase

### Step 1: Test Database Connection
1. [ ] Visit `http://yourserver.com/register_sample/test_connection.php`
2. [ ] Verify "✅ SUCCESS!" message appears
3. [ ] Check that all tables are listed
4. [ ] **DELETE test_connection.php after testing**

### Step 2: Test Registration
1. [ ] Go to `http://yourserver.com/register_sample/login/register.php`
2. [ ] Create a new user account
3. [ ] Verify success message
4. [ ] Check database for new user entry

### Step 3: Test Login
1. [ ] Go to `http://yourserver.com/register_sample/login/login.php`
2. [ ] Login with created account
3. [ ] Verify redirect to index.php
4. [ ] Check session is maintained

### Step 4: Test Admin Features (if user role = 2)
1. [ ] Navigate to admin panel
2. [ ] Test Category CRUD
   - [ ] Create category
   - [ ] View categories
   - [ ] Edit category
   - [ ] Delete category
3. [ ] Test Brand CRUD
   - [ ] Create brand
   - [ ] View brands
   - [ ] Edit brand
   - [ ] Delete brand
4. [ ] Test Product CRUD
   - [ ] Create product (without image)
   - [ ] Create product (with image upload)
   - [ ] View products
   - [ ] Edit product
   - [ ] Delete product

### Step 5: Test File Uploads
1. [ ] Upload a product image
2. [ ] Verify file saved in `uploads/images/`
3. [ ] Check file permissions (should be readable)
4. [ ] Verify image displays on product page

### Step 6: Test Frontend
1. [ ] Visit `http://yourserver.com/register_sample/index.php`
2. [ ] Test product search
3. [ ] Test category filter
4. [ ] Test brand filter
5. [ ] View single product page
6. [ ] View all products page

## ✅ Security Configuration

### Production Mode
- [ ] Disable error display in production:
```php
// Add to index.php or settings/connection.php
error_reporting(0);
ini_set('display_errors', 0);
```

- [ ] Enable error logging:
```php
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/php-error.log');
```

### File Protection
- [ ] `.htaccess` file uploaded and active
- [ ] Test that `settings/db_cred.php` is not accessible via browser
- [ ] Test that SQL files are not downloadable
- [ ] Test that `.env` files are not accessible

### SSL/HTTPS (Recommended)
- [ ] SSL certificate installed
- [ ] Force HTTPS redirects (if using SSL)
- [ ] Update `.htaccess` to force HTTPS:
```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

## ✅ Post-Deployment

### Monitoring
- [ ] Set up error log monitoring
- [ ] Test email notifications (if implemented)
- [ ] Monitor disk space for uploads folder
- [ ] Set up database backup schedule

### Documentation
- [ ] Update team with new URLs
- [ ] Document any server-specific configurations
- [ ] Create backup/restore procedures
- [ ] Document cron jobs (if any)

### Cleanup
- [ ] Delete `test_connection.php` from server
- [ ] Remove any debug code
- [ ] Clear any test/dummy data
- [ ] Review and remove commented code

## ✅ Common Issues & Solutions

### Issue: "Can't connect to database"
**Solution:**
1. Verify `SERVER` is set to `'localhost'` (lowercase) in `settings/db_cred.php`
2. Try using `'127.0.0.1'` instead of `'localhost'`
3. Check MySQL is running: `service mysql status`
4. Verify database exists: `SHOW DATABASES;`

### Issue: "File upload failed"
**Solution:**
1. Check folder permissions: `chmod 777 uploads/images/`
2. Verify PHP settings: `upload_max_filesize` and `post_max_size`
3. Check disk space: `df -h`

### Issue: "CSS files not loading"
**Solution:**
1. Verify folder is uppercase `CSS/` on server
2. Check file permissions: `chmod 755 CSS/`
3. Clear browser cache

### Issue: "Session not working"
**Solution:**
1. Check session directory permissions: `ls -la /var/lib/php/sessions/`
2. Verify `session.save_path` in `php.ini`
3. Ensure cookies are enabled in browser

### Issue: "404 errors on admin pages"
**Solution:**
1. Verify folder names match exactly (case-sensitive)
2. Check `.htaccess` is working: `apache2ctl -M | grep rewrite`
3. Ensure `AllowOverride All` is set in Apache config

## 📞 Support Contacts

- **Server Provider:** [Your Hosting Company]
- **Database Admin:** [Contact Info]
- **Developer:** [Your Contact]

---

**Deployment Date:** _____________  
**Deployed By:** _____________  
**Server URL:** _____________  
**Database Host:** _____________  


