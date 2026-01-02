# Student Profile System - Deployment Checklist

## Pre-Deployment Verification

### ✅ Code Review
- [ ] All files committed to version control
- [ ] No debug code or console.log statements
- [ ] No hardcoded credentials or sensitive data
- [ ] All TODO comments resolved
- [ ] Code follows PSR-12 standards

### ✅ Configuration
- [ ] `.env` file properly configured
- [ ] `APP_URL` set correctly
- [ ] `FILESYSTEM_DISK` set to 'public'
- [ ] Database credentials correct
- [ ] Mail settings configured (for notifications)

### ✅ Dependencies
- [ ] `composer install` completed successfully
- [ ] `npm install` completed (if needed)
- [ ] Optional: `composer require intervention/image` (recommended)
- [ ] All packages up to date

---

## Deployment Steps

### Step 1: Database Migration
```bash
# Run migrations
php artisan migrate

# Verify migrations
php artisan migrate:status

# Check for any errors
php artisan migrate:status
```

**Expected Output:**
- 3 new migrations should show as "Ran"
- No errors should appear

### Step 2: Storage Setup
```bash
# Create storage link
php artisan storage:link

# Verify link created
ls -la public/storage

# Set permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 755 public/storage
```

**Verification:**
- `public/storage` should be a symlink to `storage/app/public`
- No permission errors

### Step 3: Clear Caches
```bash
# Clear all caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 4: Seed Test Data (Optional)
```bash
# Only in development environment
php artisan db:seed --class=StudentProfileSeeder
```

**This creates 5 sample students for testing**

### Step 5: Verify Installation
```bash
# Check routes
php artisan route:list | grep profile

# Check migrations
php artisan migrate:status

# Test storage access
php artisan storage:link
```

---

## Post-Deployment Testing

### 1. Web Interface Testing

#### Student Profile Access
- [ ] Navigate to `http://your-domain.com/student/profile/edit`
- [ ] Log in as a student
- [ ] Verify profile form loads correctly
- [ ] Fill in profile information
- [ ] Upload a profile photo
- [ ] Submit form
- [ ] Verify success message
- [ ] Check profile photo appears correctly

#### Admin Profile Management
- [ ] Log in as admin
- [ ] Navigate to student management
- [ ] View a student profile
- [ ] Edit a student profile
- [ ] Upload photo for a student
- [ ] Verify changes saved

### 2. API Testing

#### Test API Endpoints
```bash
# Get authentication token first
TOKEN="your-sanctum-token"

# List students
curl -X GET http://your-domain.com/api/student/profile \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"

# Get single student
curl -X GET http://your-domain.com/api/student/profile/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"

# Upload photo
curl -X POST http://your-domain.com/api/student/profile/1/photo \
  -H "Authorization: Bearer $TOKEN" \
  -F "photo=@/path/to/test-image.jpg"
```

**Expected Results:**
- All endpoints return proper JSON responses
- Status codes are correct (200, 201, 404, etc.)
- Authentication is required for all endpoints

### 3. Authorization Testing

#### Test Access Control
- [ ] **As Student:**
  - Can view own profile ✓
  - Cannot view other profiles ✗
  - Can edit own profile ✓
  - Cannot edit other profiles ✗

- [ ] **As Employee:**
  - Can view assigned students ✓
  - Cannot view unassigned students ✗
  - Can edit assigned students ✓
  - Cannot edit unassigned students ✗

- [ ] **As Admin:**
  - Can view all students ✓
  - Can edit all students ✓
  - Can delete students ✓
  - Can approve/reject students ✓

### 4. File Upload Testing

#### Test Photo Upload
- [ ] Upload JPG file (should succeed)
- [ ] Upload PNG file (should succeed)
- [ ] Upload GIF file (should fail)
- [ ] Upload file > 2MB (should fail)
- [ ] Upload file < 200x200 pixels (should fail)
- [ ] Upload non-image file (should fail)
- [ ] Replace existing photo (old photo deleted)
- [ ] Delete photo (file removed from storage)

### 5. Validation Testing

#### Test Form Validation
- [ ] Submit empty form (should show required field errors)
- [ ] Submit duplicate email (should show uniqueness error)
- [ ] Submit invalid email format (should show format error)
- [ ] Submit invalid date (should show date error)
- [ ] Submit GPA > 4.0 (should show range error)
- [ ] Submit GPA < 0.0 (should show range error)
- [ ] Submit valid data (should succeed)

---

## Performance Verification

### 1. Page Load Times
- [ ] Profile edit page loads in < 2 seconds
- [ ] Profile view page loads in < 1 second
- [ ] Photo upload completes in < 5 seconds
- [ ] API responses return in < 500ms

### 2. Database Queries
```bash
# Enable query logging
php artisan tinker
DB::enableQueryLog();
# Perform action
# Check queries
DB::getQueryLog();
```

**Check for:**
- [ ] No N+1 query issues
- [ ] Proper use of eager loading
- [ ] Indexed columns being used

### 3. Storage Usage
```bash
# Check storage space
du -sh storage/app/public/student-photos
```

**Monitor:**
- [ ] Photos are being resized properly
- [ ] Thumbnails are being generated
- [ ] Old photos are being deleted

---

## Security Verification

### 1. Authentication
- [ ] All routes require authentication
- [ ] Unauthenticated users redirected to login
- [ ] API returns 401 for unauthenticated requests

### 2. Authorization
- [ ] Policy checks on all sensitive operations
- [ ] Users cannot access unauthorized data
- [ ] Admin-only functions protected

### 3. Input Validation
- [ ] All user input is validated
- [ ] XSS prevention working
- [ ] SQL injection prevention working
- [ ] CSRF tokens on all forms

### 4. File Upload Security
- [ ] Only allowed file types accepted
- [ ] File size limits enforced
- [ ] Files stored securely
- [ ] Unique filenames generated
- [ ] MIME type validation working

---

## Environment-Specific Checks

### Production Environment
- [ ] `APP_DEBUG=false` in `.env`
- [ ] `APP_ENV=production` in `.env`
- [ ] HTTPS enabled
- [ ] Error logging configured
- [ ] Backups scheduled
- [ ] Monitoring setup

### Staging Environment
- [ ] Test data seeded
- [ ] `APP_DEBUG=true` (for testing)
- [ ] Same configuration as production
- [ ] Accessible to QA team

### Development Environment
- [ ] `APP_DEBUG=true`
- [ ] Sample data available
- [ ] All debugging tools enabled

---

## Rollback Plan

### If Issues Are Found

#### Database Rollback
```bash
# Rollback last 3 migrations
php artisan migrate:rollback --step=3
```

#### Code Rollback
```bash
# Revert to previous commit
git revert HEAD
# Or rollback to specific commit
git reset --hard <commit-hash>
```

#### Quick Fix
```bash
# Disable problematic routes temporarily
# Comment out in routes/web.php and routes/api.php
```

---

## Monitoring Setup

### What to Monitor

#### Application Logs
```bash
tail -f storage/logs/laravel.log
```

Watch for:
- Error messages
- Failed file uploads
- Authorization failures
- Database query errors

#### Server Logs
```bash
# Apache
tail -f /var/log/apache2/error.log

# Nginx
tail -f /var/log/nginx/error.log
```

#### Storage Usage
```bash
# Monitor storage growth
watch -n 300 'du -sh storage/app/public/student-photos'
```

---

## Success Criteria

### ✅ Deployment is Successful When:

1. **Database**
   - [ ] All migrations ran successfully
   - [ ] No migration errors in logs
   - [ ] Tables created with proper structure

2. **Storage**
   - [ ] Symbolic link created successfully
   - [ ] File uploads work correctly
   - [ ] Photos display properly

3. **Functionality**
   - [ ] CRUD operations work for profiles
   - [ ] Photo upload/delete works
   - [ ] Validation catches errors
   - [ ] Authorization works correctly

4. **Performance**
   - [ ] Pages load quickly
   - [ ] No N+1 query issues
   - [ ] Images optimized properly

5. **Security**
   - [ ] Authentication required
   - [ ] Authorization enforced
   - [ ] File upload security working
   - [ ] No security warnings

6. **API**
   - [ ] All endpoints accessible
   - [ ] Returns proper responses
   - [ ] Error handling works
   - [ ] Authentication required

---

## Post-Deployment Tasks

### Immediate (Day 1)
- [ ] Monitor error logs for first 24 hours
- [ ] Check performance metrics
- [ ] Verify user feedback
- [ ] Test critical paths

### Short Term (Week 1)
- [ ] Review user adoption
- [ ] Collect user feedback
- [ ] Monitor storage growth
- [ ] Check for any edge cases

### Long Term (Month 1)
- [ ] Review performance analytics
- [ ] Plan optimizations
- [ ] Consider feature enhancements
- [ ] Review security logs

---

## Support Documentation

### For End Users
- [ ] User guide created
- [ ] FAQ documented
- [ ] Video tutorials (optional)
- [ ] Help desk informed

### For Developers
- [ ] API documentation accessible
- [ ] Code comments reviewed
- [ ] README updated
- [ ] Architecture documented

### For System Admins
- [ ] Deployment guide provided
- [ ] Troubleshooting guide ready
- [ ] Backup procedures documented
- [ ] Monitoring setup documented

---

## Contact Information

### In Case of Issues

**Development Team**
- Review: [STUDENT_PROFILE_DOCUMENTATION.md](STUDENT_PROFILE_DOCUMENTATION.md)
- Setup: [STUDENT_PROFILE_SETUP.md](STUDENT_PROFILE_SETUP.md)
- Summary: [IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md)

**Emergency Rollback**
```bash
php artisan migrate:rollback --step=3
```

---

## Sign-Off

### Deployment Verification

**Deployed By:** ___________________
**Date:** ___________________
**Environment:** ___________________
**Status:** ☐ Success  ☐ Issues Found

### Verification Sign-Off

**Tested By:** ___________________
**Date:** ___________________
**All Tests Passed:** ☐ Yes  ☐ No

**Notes:**
_________________________________
_________________________________
_________________________________

---

**Checklist Version:** 1.0  
**Last Updated:** January 3, 2026  
**Status:** ✅ Ready for Deployment
