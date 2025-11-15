# Registration Error Fix Guide

## Error: "Oops... An error occurred! Please try again later."

This error means the AJAX call to the registration action is failing. Let's debug it step by step.

---

## 🔍 Step 1: Run the Debug Script

Visit this page to see exactly what's wrong:

```
http://localhost/register_sample/test_registration.php
```

This will test:
1. ✅ Database connection
2. ✅ Customer table exists
3. ✅ User creation functionality
4. ✅ Show you the exact error if there is one

**Look for any RED ❌ messages on that page!**

---

## 🔧 Common Issues & Solutions

### Issue 1: Database Not Imported
**Symptom:** "customer table does NOT exist"

**Solution:**
1. Import the database first
2. Open phpMyAdmin (http://localhost/phpmyadmin)
3. Select database: `ecommerce_2025A_tresor_ndala`
4. Import → Choose file: `db/dbforlab.sql`
5. Click "Go"

### Issue 2: Database Connection Failed
**Symptom:** "Database connection failed"

**Solution:**
1. Make sure XAMPP MySQL is running
2. Check `settings/db_cred.php` credentials:
   - Host: `localhost` (lowercase!)
   - Username: `tresor.ndala`
   - Database: `ecommerce_2025A_tresor_ndala`

### Issue 3: Password Requirements
**Symptom:** Registration form shows password error

**Requirements:**
- At least 6 characters
- At least one lowercase letter (a-z)
- At least one uppercase letter (A-Z)
- At least one number (0-9)

**Example valid passwords:**
- `Test123`
- `Password1`
- `MyPass99`

### Issue 4: Email Already Exists
**Symptom:** "Failed to register. Email may already be in use"

**Solution:**
- Use a different email address
- Or delete the existing user from database

---

## ✅ What Was Fixed

### File: `actions/register_user_action.php`
Added better error handling:
- Now shows actual error messages instead of generic "Oops"
- Catches exceptions and displays them
- Better error logging

### File: `classes/user_class.php`
Added:
- Error logging for database errors
- Better error handling for prepare/execute
- Fixed missing fields (country, city)

---

## 📋 Test Registration Checklist

- [ ] XAMPP MySQL is running
- [ ] Database imported (`db/dbforlab.sql`)
- [ ] Run test script: `test_registration.php`
- [ ] All tests show ✅ green checks
- [ ] Try registration form again
- [ ] Use valid password (Test123)
- [ ] Use unique email
- [ ] Registration should work!

---

## 🎯 Quick Test

After running `test_registration.php`, try registering with these details:

**Test Registration:**
- Full name: `John Doe`
- Email: `john@example.com`
- Password: `Test123`
- Phone: `+233123456789`
- Role: Customer

If registration works:
1. ✅ You should see "Registered successfully"
2. ✅ Page redirects to login
3. ✅ Login with the same email/password
4. ✅ Should redirect to index.php

---

## 🐛 Still Getting Errors?

If `test_registration.php` shows a specific error:

1. **Take a screenshot** of the error message
2. **Check the error** - it will tell you exactly what's wrong
3. **Common errors:**
   - "Duplicate entry" → Email already exists
   - "Unknown column" → Database needs re-import
   - "Access denied" → Wrong database credentials
   - "Table doesn't exist" → Database not imported

---

## 🔐 Browser Console Errors

To see detailed AJAX errors:

1. Open browser Developer Tools (F12)
2. Go to "Console" tab
3. Try to register
4. Look for red error messages
5. Check "Network" tab
6. Click on "register_user_action.php" request
7. Check the "Response" - it will show the actual error!

---

## ✅ After It Works

Once registration works:

1. **DELETE** these test files:
   - `test_registration.php`
   - `setup_test_user.php`
   - `test_connection.php`

2. **Test full workflow:**
   - Register a new user
   - Login with that user
   - Access admin panel (if owner role)
   - Create categories/brands/products

---

## 📞 Debugging Steps Summary

```
1. Visit: test_registration.php
   → See the exact error

2. Fix the error shown

3. Try registration form again

4. If works → Delete test files

5. If still fails → Check browser console
```

---

**Most Common Fix:**

The database probably isn't imported yet. Run these steps:

1. Open: `http://localhost/phpmyadmin`
2. Create database: `ecommerce_2025A_tresor_ndala`
3. Select it
4. Import → `db/dbforlab.sql`
5. Visit: `test_registration.php`
6. Should now show all ✅ green checks!

---

Updated: 2025-11-02  
File: `REGISTRATION_ERROR_FIX.md`


