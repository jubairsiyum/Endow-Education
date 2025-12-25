# Endow Global Education Portal - Setup & Deployment Guide

## 📦 Complete Installation Steps

### Step 1: Install Laravel Authentication

```bash
cd "d:\Endow Education\endow-portal"
composer require laravel/ui
php artisan ui bootstrap --auth
npm install
npm run build
```

### Step 2: Configure Database

1. Create a new MySQL database:
```sql
CREATE DATABASE endow_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Update `.env` file:
```env
APP_NAME="Endow Global Education"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=endow_portal
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Step 3: Run Migrations and Seeders

```bash
# Run migrations
php artisan migrate

# Seed roles, permissions, and default users
php artisan db:seed

# Or run both at once
php artisan migrate:fresh --seed
```

### Step 4: Clear Cache and Optimize

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize
```

### Step 5: Set Permissions (Production)

For Linux/Mac:
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

For Windows (Development):
```bash
# Run as Administrator
icacls "d:\Endow Education\endow-portal\storage" /grant Everyone:(OI)(CI)F /T
icacls "d:\Endow Education\endow-portal\bootstrap\cache" /grant Everyone:(OI)(CI)F /T
```

### Step 6: Start Development Server

```bash
php artisan serve
```

Visit: http://localhost:8000

## 🔐 Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Super Admin | superadmin@endowglobal.com | password |
| Admin | admin@endowglobal.com | password |
| Employee | employee@endowglobal.com | password |

⚠️ **CRITICAL**: Change these passwords immediately after first login!

## 🏗️ Project Architecture Overview

### Models & Relationships

```
User
├── hasMany: assignedStudents (Student)
├── hasMany: createdStudents (Student)
├── hasMany: followUps (FollowUp)
├── hasMany: uploadedDocuments (StudentDocument)
└── hasOne: student (Student)

Student
├── belongsTo: user (User)
├── belongsTo: assignedUser (User)
├── belongsTo: creator (User)
├── hasMany: followUps (FollowUp)
├── hasMany: checklists (StudentChecklist)
└── hasMany: documents (StudentDocument)

ChecklistItem
├── hasMany: studentChecklists (StudentChecklist)
└── hasMany: documents (StudentDocument)

StudentChecklist
├── belongsTo: student (Student)
├── belongsTo: checklistItem (ChecklistItem)
└── hasMany: documents (StudentDocument)

StudentDocument
├── belongsTo: student (Student)
├── belongsTo: checklistItem (ChecklistItem)
├── belongsTo: studentChecklist (StudentChecklist)
└── belongsTo: uploader (User)
```

### Service Classes

**PdfService** (`app/Services/PdfService.php`)
- `compressAndEncode($file)`: Validates, compresses, and encodes PDF to Base64
- `decodeBase64($base64)`: Decodes Base64 to PDF content
- `downloadResponse($base64, $filename)`: Generate download response
- `viewResponse($base64, $filename)`: Generate inline view response

**ChecklistService** (`app/Services/ChecklistService.php`)
- `initializeChecklistsForStudent($student)`: Auto-create checklist items for new students
- `getChecklistProgress($student)`: Calculate completion percentage
- `updateChecklistStatus($checklist, $status, $remarks, $approvedBy)`: Update status with audit

**ActivityLogService** (`app/Services/ActivityLogService.php`)
- `log($logName, $description, $subject, $properties)`: Generic logging
- `logStudentCreated($student)`: Log student creation
- `logStudentAssigned($student, $oldAssignedTo, $newAssignedTo)`: Log reassignment
- `logDocumentApproved($document)`: Log document approval
- `logDocumentRejected($document, $reason)`: Log document rejection

### Controllers

**StudentController** (`app/Http/Controllers/StudentController.php`)
- Full CRUD operations
- `approve($student)`: Approve student account
- `reject($student)`: Reject student account
- Authorization via StudentPolicy
- Activity logging integrated

**DashboardController** (`app/Http/Controllers/DashboardController.php`)
- `admin()`: Admin/Employee dashboard
- `student()`: Student dashboard
- `index()`: Route to appropriate dashboard based on role

**FollowUpController** (TODO: Implement)
- Create/edit/delete follow-ups
- Timeline display
- XSS protection for notes

**DocumentController** (TODO: Implement)
- `upload($request)`: Handle PDF upload → compress → Base64 → store
- `download($document)`: Download PDF
- `view($document)`: View PDF inline
- `approve($document)`: Approve document
- `reject($document)`: Reject document

**ChecklistItemController** (TODO: Implement)
- CRUD for checklist items
- Reordering functionality

### Form Requests

All validation is handled by Form Request classes:
- `StoreStudentRequest`: Validate student creation
- `UpdateStudentRequest`: Validate student updates
- `StoreFollowUpRequest`: Validate follow-ups with XSS protection
- `UploadDocumentRequest`: Validate PDF uploads (type, size)
- `StoreChecklistItemRequest`: Validate checklist items

### Policies

**StudentPolicy** (`app/Policies/StudentPolicy.php`)
- `viewAny()`: Who can view student list
- `view($user, $student)`: Who can view specific student
- `create()`: Who can create students (Students cannot!)
- `update()`: Who can update students
- `delete()`: Who can delete students
- `approve()`: Who can approve student accounts
- `assign()`: Who can reassign students

## 📋 TODO: Complete Implementation

### 1. Authentication UI
```bash
# Install Laravel Breeze or UI
composer require laravel/ui
php artisan ui bootstrap --auth
npm install && npm run build
```

### 2. Remaining Controllers

**FollowUpController**
```php
// TODO: Implement CRUD operations
// - store(): Create follow-up with XSS protection
// - index($student): Show timeline
// - update(): Edit follow-up
// - delete(): Soft delete
```

**DocumentController**
```php
// TODO: Implement document management
// - upload(): PDF validation → compression → Base64 → database
// - download(): Base64 decode → PDF response
// - approve(): Update student_checklist status
// - reject(): Update student_checklist status + remarks
```

**ChecklistItemController**
```php
// TODO: Implement checklist management
// - index(): List all checklist items
// - create(): Add new checklist item
// - update(): Edit checklist item
// - reorder(): Ajax endpoint for drag-drop reordering
```

### 3. Blade Views

Create views in `resources/views/`:

```
students/
├── index.blade.php     # List all students with filters
├── create.blade.php    # Create student form
├── edit.blade.php      # Edit student form
└── show.blade.php      # Student profile with tabs:
                        #   - Profile
                        #   - Follow-ups
                        #   - Checklist
                        #   - Documents

dashboard/
├── admin.blade.php     # Admin/Employee dashboard (✅ CREATED)
└── student.blade.php   # Student dashboard (TODO)

follow-ups/
└── _form.blade.php     # Follow-up form modal

checklist-items/
├── index.blade.php     # List checklist items
└── _form.blade.php     # Checklist item form

documents/
└── _upload.blade.php   # Document upload modal

layouts/
└── app.blade.php       # Main layout (✅ CREATED)
```

### 4. Notifications

Implement Laravel Notifications:

```bash
php artisan make:notification StudentApprovedNotification
php artisan make:notification DocumentApprovedNotification
php artisan make:notification StudentRegisteredNotification
```

Example:
```php
// In StudentController@approve
$student->user->notify(new StudentApprovedNotification($student));

// In DocumentController@approve
$document->student->user->notify(new DocumentApprovedNotification($document));
```

### 5. Queue Configuration

For production, set up queues:

```env
QUEUE_CONNECTION=database
```

```bash
php artisan queue:table
php artisan migrate
php artisan queue:work
```

### 6. PDF Compression Implementation

**Option A: Ghostscript (Recommended)**

Install Ghostscript and update `PdfService`:

```php
public function compressAndEncode($file): array
{
    // ... validation ...

    if ($originalSize > self::MAX_FILE_SIZE) {
        // Compress using Ghostscript
        $tempPath = storage_path('app/temp/' . uniqid() . '.pdf');
        $compressedPath = storage_path('app/temp/' . uniqid() . '_compressed.pdf');

        copy($file->getRealPath(), $tempPath);

        exec("gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/ebook -dNOPAUSE -dQUIET -dBATCH -sOutputFile={$compressedPath} {$tempPath}");

        $compressedContent = file_get_contents($compressedPath);
        $compressedSize = strlen($compressedContent);

        unlink($tempPath);
        unlink($compressedPath);

        if ($compressedSize > self::MAX_FILE_SIZE) {
            throw new Exception('File too large even after compression');
        }

        return [
            'success' => true,
            'base64' => base64_encode($compressedContent),
            'size' => $compressedSize,
            'compressed' => true,
        ];
    }

    // ... rest of code ...
}
```

**Option B: Cloud Service (Cloudinary, ImageKit)**

Integrate cloud PDF optimization service.

### 7. Rich Text Editor for Follow-ups

**Install TinyMCE**:

Add to layout:
```html
<script src="https://cdn.tiny.cloud/1/YOUR_API_KEY/tinymce/6/tinymce.min.js"></script>
<script>
    tinymce.init({
        selector: 'textarea.rich-text',
        plugins: 'lists link',
        toolbar: 'undo redo | bold italic | bullist numlist | link',
        menubar: false
    });
</script>
```

### 8. Activity Log Viewing

Create a page to view activity logs:

```php
// ActivityLogController
public function index(Request $request)
{
    $logs = ActivityLog::with(['subject', 'causer'])
        ->latest()
        ->paginate(50);

    return view('activity-logs.index', compact('logs'));
}
```

## 🚀 Production Deployment

### 1. Server Requirements
- PHP >= 8.1
- MySQL/PostgreSQL
- Composer
- Node.js & NPM
- Nginx or Apache

### 2. Environment Configuration

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Use strong random key
APP_KEY=

# Production database
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=strong-password

# Queue
QUEUE_CONNECTION=database

# Mail
MAIL_MAILER=smtp
MAIL_HOST=your-mail-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS=noreply@endowglobal.com
```

### 3. Deployment Commands

```bash
# On server
composer install --optimize-autoloader --no-dev
npm install && npm run build

php artisan key:generate
php artisan migrate --force
php artisan db:seed --force

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan storage:link

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 4. Nginx Configuration

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/endow-portal/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 5. SSL Certificate

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

### 6. Supervisor for Queues

```bash
sudo apt install supervisor

# Create config: /etc/supervisor/conf.d/endow-worker.conf
[program:endow-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/endow-portal/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/endow-portal/storage/logs/worker.log

sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start endow-worker:*
```

## 🔒 Security Checklist

- [ ] Change all default passwords
- [ ] Set `APP_DEBUG=false` in production
- [ ] Use HTTPS (SSL certificate)
- [ ] Configure firewall (allow only 80, 443, 22)
- [ ] Regular backups (database + uploads)
- [ ] Rate limiting on login/API
- [ ] Enable CSRF protection (default)
- [ ] Validate all user inputs
- [ ] Sanitize HTML content (XSS protection)
- [ ] Use prepared statements (Eloquent default)
- [ ] Keep Laravel and dependencies updated
- [ ] Monitor logs regularly
- [ ] Implement 2FA for admins
- [ ] Use environment variables for secrets

## 📊 Database Backup

```bash
# Backup
mysqldump -u username -p endow_portal > backup_$(date +%Y%m%d_%H%M%S).sql

# Restore
mysql -u username -p endow_portal < backup_20251224_120000.sql

# Automated daily backup (crontab)
0 2 * * * /usr/bin/mysqldump -u username -p'password' endow_portal > /backups/endow_$(date +\%Y\%m\%d).sql
```

## 🆘 Troubleshooting

### Issue: Migration errors
```bash
php artisan migrate:fresh --seed
```

### Issue: Permission denied
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
```

### Issue: Class not found
```bash
composer dump-autoload
php artisan clear-compiled
php artisan config:clear
```

### Issue: 500 error in production
- Check `storage/logs/laravel.log`
- Ensure `.env` is configured correctly
- Run `php artisan config:cache`

## 📞 Support & Maintenance

- Monitor logs: `tail -f storage/logs/laravel.log`
- Queue monitoring: `php artisan queue:work --verbose`
- Performance: Enable OPcache, use Redis for cache
- Regular updates: `composer update`, `npm update`

---

**Happy Coding! 🚀**
