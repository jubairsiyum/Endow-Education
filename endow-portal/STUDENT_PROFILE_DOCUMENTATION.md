# Student Profile Management System - Documentation

## Overview

This document provides comprehensive information about the Student Profile Management System integrated into the Endow Education Portal. The system enables full CRUD operations on student profiles with secure profile picture upload functionality.

---

## 📋 Table of Contents

1. [System Architecture](#system-architecture)
2. [Database Schema](#database-schema)
3. [Installation & Setup](#installation--setup)
4. [API Documentation](#api-documentation)
5. [Features](#features)
6. [Security](#security)
7. [Testing](#testing)
8. [Troubleshooting](#troubleshooting)

---

## System Architecture

The system follows a clean, layered architecture:

```
┌─────────────────────────────────────┐
│         Routes (Web/API)            │
├─────────────────────────────────────┤
│         Controllers                 │
├─────────────────────────────────────┤
│      Form Request Validators        │
├─────────────────────────────────────┤
│         Service Layer               │
│  - StudentProfileService            │
│  - ImageProcessingService           │
├─────────────────────────────────────┤
│         Models & ORM                │
│  - Student                          │
│  - StudentProfile                   │
│  - StudentProfilePhoto              │
├─────────────────────────────────────┤
│         Database Layer              │
└─────────────────────────────────────┘
```

### Key Components

#### Controllers
- **StudentProfileController**: Handles web requests for profile management
- **StudentProfileApiController**: Handles API requests with JSON responses

#### Services
- **StudentProfileService**: Business logic for profile CRUD operations
- **ImageProcessingService**: Handles image upload, processing, and storage

#### Models
- **Student**: Main student model with extended profile fields
- **StudentProfile**: Extended academic and personal profile information
- **StudentProfilePhoto**: Manages profile photos with active status tracking

#### Form Requests
- **StoreStudentProfileRequest**: Validation for creating new student profiles
- **UpdateStudentProfileRequest**: Validation for updating existing profiles
- **UploadProfilePhotoRequest**: Validation for profile photo uploads

---

## Database Schema

### Table: `students`

Enhanced table with extended profile fields.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary Key |
| registration_id | varchar(255) | Unique registration identifier (STU2026XXXX) |
| user_id | bigint | Foreign key to users table (nullable) |
| name | varchar(255) | Full name (required) |
| surname | varchar(255) | Surname |
| given_names | varchar(255) | Given names |
| father_name | varchar(255) | Father's name |
| mother_name | varchar(255) | Mother's name |
| email | varchar(255) | Unique email (required) |
| password | varchar(255) | Encrypted password |
| phone | varchar(20) | Contact phone (required) |
| date_of_birth | date | Date of birth |
| gender | enum | male, female, other |
| passport_number | varchar(50) | Passport number |
| passport_expiry_date | date | Passport expiry date |
| nationality | varchar(100) | Nationality |
| country | varchar(100) | Country (required) |
| address | text | Full address |
| city | varchar(100) | City |
| postal_code | varchar(20) | Postal/ZIP code |
| status | enum | new, contacted, processing, applied, approved, rejected |
| account_status | enum | pending, approved, rejected |
| assigned_to | bigint | Foreign key to users (assigned staff) |
| created_by | bigint | Foreign key to users (creator) |
| notes | text | Internal notes |
| emergency_contact_name | varchar(255) | Emergency contact name |
| emergency_contact_phone | varchar(20) | Emergency contact phone |
| emergency_contact_relationship | varchar(100) | Relationship to student |
| created_at | timestamp | Creation timestamp |
| updated_at | timestamp | Last update timestamp |
| deleted_at | timestamp | Soft delete timestamp |

### Table: `student_profiles`

Extended academic and personal profile information.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary Key |
| student_id | bigint | Foreign key to students (cascade delete) |
| student_id_number | varchar(50) | Unique student ID number |
| academic_level | varchar(100) | Academic level (e.g., Undergraduate, Graduate) |
| major | varchar(255) | Major field of study |
| minor | varchar(255) | Minor field of study |
| gpa | decimal(3,2) | Grade Point Average (0.00-4.00) |
| enrollment_date | date | Date of enrollment |
| expected_graduation_date | date | Expected graduation date |
| bio | text | Student biography (max 1000 chars) |
| interests | text | Personal interests (max 500 chars) |
| skills | text | Skills (max 500 chars) |
| languages | json | Array of languages spoken |
| social_links | json | Social media links |
| preferences | json | User preferences |
| profile_notes | text | Additional profile notes |
| created_at | timestamp | Creation timestamp |
| updated_at | timestamp | Last update timestamp |

### Table: `student_profile_photos`

Manages profile photos with versioning.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary Key |
| student_id | bigint | Foreign key to students (cascade delete) |
| photo_path | varchar(255) | Storage path to photo |
| thumbnail_path | varchar(255) | Storage path to thumbnail |
| original_filename | varchar(255) | Original uploaded filename |
| mime_type | varchar(100) | File MIME type |
| file_size | bigint | File size in bytes |
| is_active | boolean | Active status (only one active per student) |
| created_at | timestamp | Upload timestamp |
| updated_at | timestamp | Last update timestamp |

**Indexes:**
- `student_id` - For quick lookups
- `(student_id, is_active)` - Composite index for active photo queries

---

## Installation & Setup

### Step 1: Run Migrations

```bash
php artisan migrate
```

This will create/update the following tables:
- `students` (add new columns)
- `student_profiles`
- `student_profile_photos`

### Step 2: Configure Storage

Ensure the public disk is properly configured in `config/filesystems.php`:

```php
'public' => [
    'driver' => 'local',
    'root' => storage_path('app/public'),
    'url' => env('APP_URL').'/storage',
    'visibility' => 'public',
],
```

Create the symbolic link for storage:

```bash
php artisan storage:link
```

### Step 3: Install Image Processing Library (Optional but Recommended)

For better image quality and processing:

```bash
composer require intervention/image
```

If not installed, the system will fall back to PHP GD library.

### Step 4: Set File Upload Limits

Update `php.ini` or `.htaccess`:

```ini
upload_max_filesize = 2M
post_max_size = 2M
```

### Step 5: Generate Registration IDs

The system automatically generates unique registration IDs in the format: `STU{YEAR}{4-DIGIT-RANDOM}`

Example: `STU20260001`, `STU20260002`, etc.

---

## API Documentation

### Base URL

```
/api/student/profile
```

### Authentication

All API endpoints require authentication using Laravel Sanctum:

```http
Authorization: Bearer {token}
```

### Endpoints

#### 1. List Students

**GET** `/api/student/profile`

**Query Parameters:**
- `search` (string): Search by name, email, phone, or registration ID
- `status` (string): Filter by application status
- `account_status` (string): Filter by account status (pending, approved, rejected)
- `gender` (string): Filter by gender
- `per_page` (int): Results per page (default: 15)

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "registration_id": "STU20260001",
        "name": "John Doe",
        "email": "john@example.com",
        "status": "new",
        "account_status": "pending",
        "profile": {...},
        "active_profile_photo": {...}
      }
    ],
    "total": 50,
    "per_page": 15
  }
}
```

#### 2. Create Student Profile

**POST** `/api/student/profile`

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+1234567890",
  "country": "USA",
  "date_of_birth": "2000-01-15",
  "gender": "male",
  "address": "123 Main St",
  "city": "New York",
  "postal_code": "10001",
  "profile": {
    "academic_level": "Undergraduate",
    "major": "Computer Science",
    "gpa": 3.75
  }
}
```

**Response:**
```json
{
  "success": true,
  "message": "Student profile created successfully!",
  "data": {
    "id": 1,
    "registration_id": "STU20260001",
    "name": "John Doe",
    "email": "john@example.com",
    ...
  }
}
```

#### 3. Get Student Profile

**GET** `/api/student/profile/{id}`

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "registration_id": "STU20260001",
    "name": "John Doe",
    "email": "john@example.com",
    "profile": {
      "id": 1,
      "academic_level": "Undergraduate",
      "major": "Computer Science",
      ...
    },
    "active_profile_photo": {
      "id": 1,
      "photo_url": "/storage/student-photos/1/profile_abc.jpg",
      "thumbnail_url": "/storage/student-photos/1/thumb_profile_abc.jpg"
    }
  }
}
```

#### 4. Update Student Profile

**PUT** `/api/student/profile/{id}`

**Request Body:** (All fields optional)
```json
{
  "name": "John Smith",
  "phone": "+1234567891",
  "profile": {
    "gpa": 3.85,
    "bio": "Updated bio text"
  }
}
```

**Response:**
```json
{
  "success": true,
  "message": "Student profile updated successfully!",
  "data": {...}
}
```

#### 5. Delete Student Profile

**DELETE** `/api/student/profile/{id}`

**Response:**
```json
{
  "success": true,
  "message": "Student profile deleted successfully!"
}
```

#### 6. Upload Profile Photo

**POST** `/api/student/profile/{id}/photo`

**Request:** (multipart/form-data)
- `photo` (file): Image file (JPG, JPEG, PNG, max 2MB, min 200x200px)

**Response:**
```json
{
  "success": true,
  "message": "Profile photo uploaded successfully!",
  "data": {
    "photo_url": "/storage/student-photos/1/profile_xyz.jpg",
    "thumbnail_url": "/storage/student-photos/1/thumb_profile_xyz.jpg",
    "photo_id": 5
  }
}
```

#### 7. Delete Profile Photo

**DELETE** `/api/student/profile/{id}/photo`

**Response:**
```json
{
  "success": true,
  "message": "Profile photo deleted successfully!"
}
```

### Error Responses

All error responses follow this format:

```json
{
  "success": false,
  "message": "Error message",
  "error": "Detailed error information"
}
```

**Common Status Codes:**
- `200` - Success
- `201` - Created
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

---

## Features

### 1. Profile Management

#### Create Profile
- Auto-generate unique registration ID
- Validate all input fields
- Support for partial data submission
- Automatic timestamp tracking

#### Read Profile
- View complete profile information
- Display profile photo
- Show profile completion percentage
- Access control based on user role

#### Update Profile
- Partial updates supported
- Validation on all fields
- Track update history via timestamps
- Role-based editing permissions

#### Delete Profile
- Soft delete by default
- Prevent accidental deletion
- Admin approval for permanent deletion
- Cascade delete related data

### 2. Profile Photo Management

#### Upload Photo
- Supported formats: JPG, JPEG, PNG
- Max file size: 2MB
- Minimum dimensions: 200x200 pixels
- Auto-resize to 300x300 pixels
- Generate thumbnail (150x150 pixels)
- Maintain aspect ratio
- Optimize for web delivery

#### Photo Processing
- Primary library: Intervention Image
- Fallback: PHP GD library
- Quality optimization (85% JPEG)
- Automatic format conversion to JPG

#### Photo Management
- One active photo per student
- Automatic deactivation of old photos
- Replace existing photo
- Delete photo with confirmation
- Automatic file cleanup on deletion

### 3. Authorization & Security

#### Role-Based Access Control
- **Students**: Can view and edit their own profile only
- **Employees**: Can view and edit assigned students
- **Admins**: Can view, edit, and delete all profiles
- **Super Admins**: Full access including force delete

#### Data Protection
- CSRF protection on all forms
- SQL injection prevention via Eloquent ORM
- XSS protection via Blade templating
- File upload validation
- Malicious file detection

### 4. Profile Completion Tracking

The system tracks profile completion percentage based on:
- Student ID number
- Academic level
- Major
- GPA
- Enrollment date
- Expected graduation date
- Bio
- Interests
- Skills

Formula: `(Completed Fields / Total Fields) × 100`

---

## Security

### File Upload Security

#### Validation Rules
```php
- File type: image/jpeg, image/jpg, image/png only
- File size: Max 2MB
- Dimensions: Min 200x200 pixels
- MIME type verification
- Real image validation (not just extension check)
```

#### Storage Security
- Files stored outside web root
- Symbolic link for public access
- Unique filename generation
- No original filename in storage path

### Data Validation

All input is validated through Form Request classes:
- Type checking
- Format validation
- Range validation
- Unique constraints
- Relationship existence checks

### Access Control

Implemented through Laravel Policies:
- `StudentPolicy` handles all authorization
- Automatic policy resolution
- Middleware protection on routes

---

## Testing

### Manual Testing Checklist

#### Profile CRUD Operations
- [ ] Create student profile with all fields
- [ ] Create student profile with minimum fields
- [ ] View student profile
- [ ] Update student profile (partial)
- [ ] Update student profile (full)
- [ ] Delete student profile
- [ ] Verify soft delete works
- [ ] Verify timestamps update correctly

#### Photo Upload
- [ ] Upload valid JPG photo
- [ ] Upload valid PNG photo
- [ ] Upload photo larger than 2MB (should fail)
- [ ] Upload non-image file (should fail)
- [ ] Upload photo smaller than 200x200 (should fail)
- [ ] Replace existing photo
- [ ] Delete photo
- [ ] Verify old photos are cleaned up

#### Authorization
- [ ] Student can view own profile
- [ ] Student cannot view other profiles
- [ ] Student can edit own profile
- [ ] Student cannot edit other profiles
- [ ] Employee can view assigned students
- [ ] Employee cannot view unassigned students
- [ ] Admin can view all profiles
- [ ] Admin can edit all profiles

#### Validation
- [ ] Required fields validation
- [ ] Email format validation
- [ ] Email uniqueness validation
- [ ] Date format validation
- [ ] GPA range validation (0-4)
- [ ] Phone number validation

### API Testing

Use tools like Postman or curl:

```bash
# Get all students
curl -X GET http://127.0.0.1:8000/api/student/profile \
  -H "Authorization: Bearer {token}"

# Create student
curl -X POST http://127.0.0.1:8000/api/student/profile \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"name":"Test Student","email":"test@example.com","phone":"1234567890","country":"USA"}'

# Upload photo
curl -X POST http://127.0.0.1:8000/api/student/profile/1/photo \
  -H "Authorization: Bearer {token}" \
  -F "photo=@/path/to/image.jpg"
```

---

## Troubleshooting

### Common Issues

#### 1. Image Upload Fails

**Symptom**: "Failed to upload photo" error

**Solutions:**
- Check PHP upload_max_filesize setting
- Check post_max_size setting
- Verify storage directory is writable
- Check storage symbolic link exists
- Verify GD or Intervention Image is installed

```bash
# Check GD support
php -i | grep -i gd

# Recreate storage link
php artisan storage:link
```

#### 2. Profile Photo Not Displaying

**Symptom**: Broken image or 404 error

**Solutions:**
- Verify storage link exists
- Check file permissions
- Verify APP_URL in .env matches actual URL
- Check browser console for errors

```bash
# Fix storage link
rm public/storage
php artisan storage:link

# Fix permissions
chmod -R 755 storage
chmod -R 755 public/storage
```

#### 3. Validation Errors

**Symptom**: "The given data was invalid" or specific field errors

**Solutions:**
- Check all required fields are provided
- Verify data types match validation rules
- Check email uniqueness
- Verify date formats (Y-m-d)
- Check GPA is between 0 and 4

#### 4. Authorization Denied

**Symptom**: "This action is unauthorized" error

**Solutions:**
- Verify user is authenticated
- Check user role permissions
- Verify student-user relationship is correct
- Check assigned_to field for employees

#### 5. Migration Errors

**Symptom**: Column already exists or table not found

**Solutions:**
```bash
# Check migration status
php artisan migrate:status

# Rollback and re-migrate
php artisan migrate:rollback
php artisan migrate

# Fresh migration (WARNING: deletes all data)
php artisan migrate:fresh
```

### Debug Mode

Enable debug mode in `.env` for detailed error messages:

```env
APP_DEBUG=true
```

**Note**: Never enable debug mode in production!

### Logs

Check Laravel logs for detailed error information:

```bash
tail -f storage/logs/laravel.log
```

---

## Additional Notes

### Future Enhancements

Potential features for future development:
- Bulk photo upload
- Photo cropping tool
- Multiple photo albums
- Cloud storage integration (AWS S3, Google Cloud)
- Face recognition/verification
- ID card generation
- QR code profiles
- Export to PDF
- Advanced search filters
- Profile analytics dashboard

### Performance Optimization

For large-scale deployments:
- Implement caching for frequently accessed profiles
- Use queue jobs for image processing
- CDN for static assets
- Database indexing optimization
- Lazy loading for relationships

### Compliance

The system is designed with data privacy in mind:
- GDPR compliance ready
- Data minimization principle
- Right to erasure (soft delete)
- Data portability support
- Audit trail via timestamps

---

## Support

For issues, questions, or feature requests:
- Check this documentation first
- Review Laravel logs
- Check database constraints
- Verify file permissions
- Test in isolated environment

---

## Version History

- **v1.0.0** (January 3, 2026)
  - Initial release
  - Full CRUD operations
  - Profile photo upload
  - Role-based authorization
  - API endpoints
  - Comprehensive validation

---

**Last Updated**: January 3, 2026
**System Version**: 1.0.0
**Laravel Version**: Compatible with Laravel 10.x+
