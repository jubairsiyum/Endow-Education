# Student Profile System - Quick Setup Guide

## 🚀 Quick Start

Follow these steps to set up the Student Profile Management System:

### 1. Run Migrations

```bash
php artisan migrate
```

This creates/updates these tables:
- `students` - Enhanced with profile fields
- `student_profiles` - Academic and extended profile data
- `student_profile_photos` - Profile photo management

### 2. Set Up Storage

```bash
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public`.

### 3. Configure Environment

Ensure your `.env` file has:

```env
APP_URL=http://127.0.0.1:8000
FILESYSTEM_DISK=public
```

### 4. Install Image Library (Optional but Recommended)

For better image quality:

```bash
composer require intervention/image
```

If not installed, the system uses PHP GD as fallback.

---

## 📍 Access URLs

### Student Portal
- **Profile View**: `http://127.0.0.1:8000/student/profile`
- **Edit Profile**: `http://127.0.0.1:8000/student/profile/edit`

### Admin/Staff Routes
- **View Student Profile**: `/students/{id}/profile`
- **Edit Student Profile**: `/students/{id}/profile/edit`

### API Endpoints
- Base URL: `/api/student/profile`
- All endpoints require authentication (Sanctum)

---

## 🔑 Key Features

✅ **Full CRUD Operations**
- Create student profiles
- Read/View profiles
- Update profile information
- Soft delete profiles

✅ **Profile Photo Management**
- Upload profile photos (JPG, JPEG, PNG)
- Auto-resize to 300×300px
- Generate thumbnails (150×150px)
- Replace/delete photos

✅ **Security & Authorization**
- Role-based access control
- Students can manage own profiles
- Staff can manage assigned students
- Admins have full access

✅ **Validation**
- Required field validation
- Email uniqueness check
- File type and size validation
- Image dimension validation

---

## 📁 File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── StudentProfileController.php
│   │   └── Api/
│   │       └── StudentProfileApiController.php
│   └── Requests/
│       ├── StoreStudentProfileRequest.php
│       ├── UpdateStudentProfileRequest.php
│       └── UploadProfilePhotoRequest.php
├── Models/
│   ├── Student.php (updated)
│   ├── StudentProfile.php
│   └── StudentProfilePhoto.php
├── Policies/
│   └── StudentPolicy.php (updated)
└── Services/
    ├── StudentProfileService.php
    └── ImageProcessingService.php

database/
└── migrations/
    ├── 2026_01_03_000001_add_student_profile_fields_to_students_table.php
    ├── 2026_01_03_000002_create_student_profiles_table.php
    └── 2026_01_03_000003_create_student_profile_photos_table.php

resources/
└── views/
    └── student/
        └── profile/
            ├── edit.blade.php
            └── show.blade.php

routes/
├── web.php (updated)
└── api.php (updated)
```

---

## 🧪 Testing

### Quick Manual Test

1. **Log in as a student**
2. **Navigate to**: `http://127.0.0.1:8000/student/profile/edit`
3. **Update profile information**
4. **Upload a profile photo**
5. **Save changes**
6. **Verify**: Changes appear on the profile page

### API Test (using curl)

```bash
# Get student profile (replace {id} and {token})
curl -X GET http://127.0.0.1:8000/api/student/profile/{id} \
  -H "Authorization: Bearer {token}"

# Upload photo
curl -X POST http://127.0.0.1:8000/api/student/profile/{id}/photo \
  -H "Authorization: Bearer {token}" \
  -F "photo=@path/to/image.jpg"
```

---

## ⚙️ Configuration

### File Upload Limits

Update `php.ini` if needed:

```ini
upload_max_filesize = 2M
post_max_size = 2M
max_file_uploads = 20
```

### Storage Configuration

In `config/filesystems.php`:

```php
'public' => [
    'driver' => 'local',
    'root' => storage_path('app/public'),
    'url' => env('APP_URL').'/storage',
    'visibility' => 'public',
],
```

---

## 🔧 Troubleshooting

### Issue: Photos not uploading

**Solution:**
```bash
# Check storage permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Recreate storage link
rm public/storage
php artisan storage:link
```

### Issue: Validation errors

**Check:**
- Email is unique
- Required fields are filled
- Image is JPG/PNG and under 2MB
- Image is at least 200×200 pixels

### Issue: Authorization denied

**Verify:**
- User is logged in
- User has correct role
- Student-user relationship exists

---

## 📚 Full Documentation

For complete documentation, see: [STUDENT_PROFILE_DOCUMENTATION.md](STUDENT_PROFILE_DOCUMENTATION.md)

Topics covered:
- Detailed API documentation
- Database schema
- Security features
- Complete troubleshooting guide
- Testing procedures

---

## 🎯 Quick Reference

### Profile Photo Requirements
- **Formats**: JPG, JPEG, PNG
- **Max Size**: 2MB
- **Min Dimensions**: 200×200 pixels
- **Output Size**: 300×300 pixels
- **Thumbnail**: 150×150 pixels

### Required Fields for Student Profile
- Name
- Email (must be unique)
- Phone
- Country

### Optional but Recommended Fields
- Date of Birth
- Gender
- Address
- Passport Information
- Emergency Contact
- Academic Profile

---

## 🔒 Security Notes

- All routes are protected by authentication
- Role-based authorization via policies
- CSRF protection enabled
- File upload validation
- SQL injection prevention via Eloquent
- XSS protection via Blade

---

## 📞 Need Help?

1. Check [STUDENT_PROFILE_DOCUMENTATION.md](STUDENT_PROFILE_DOCUMENTATION.md)
2. Review `storage/logs/laravel.log`
3. Verify database migrations ran successfully
4. Check file permissions

---

**Version**: 1.0.0  
**Date**: January 3, 2026  
**Status**: Production Ready ✅
