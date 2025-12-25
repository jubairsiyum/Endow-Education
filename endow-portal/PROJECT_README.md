# Endow Global Education Portal

A production-ready Laravel 10 student management system for education consultancy offices with role-based access control (RBAC), document management, and student tracking.

## 🚀 Features

### Role-Based Access Control (RBAC)
- **Super Admin**: Full system access
- **Admin**: Full access except managing Super Admin users
- **Employee**: Manage students, follow-ups, checklists, and documents
- **Student**: Access own dashboard, checklist, and document uploads

### Student Management
- Create, view, edit, and delete student records
- Track student status (New, Contacted, Processing, Applied, Approved, Rejected)
- Automatic assignment to creator
- Admin/Super Admin can reassign students
- Student approval workflow

### Follow-up System
- Create follow-ups with rich text notes
- Set next follow-up dates
- Track follow-up history per student
- XSS protection for notes

### Checklist System
- Define checklist items (Passport, Certificates, Bank Statements, etc.)
- Mark items as required/optional
- Track checklist status per student:
  - Pending
  - Submitted
  - Approved
  - Rejected
- Progress tracking

### Document Management (Base64 Storage)
- **STRICT FLOW**: Upload → Compress → Base64 Encode → Database
- PDF-only uploads
- Maximum 10 MB per file
- Base64 encoding for database storage
- Document approval/rejection workflow
- Download and inline view support

### Dashboards
- **Admin/Employee Dashboard**:
  - Total students count
  - Students by status
  - Pending approvals
  - Assigned students list
  
- **Student Dashboard**:
  - Account approval status notice
  - Checklist progress bar
  - Document submission status
  - Assigned counselor information

### Security & Audit
- CSRF protection
- XSS protection for rich text
- Policy-based authorization
- Activity logging for:
  - Student creation
  - Assignment changes
  - Document approvals/rejections

## 📋 Requirements

- PHP >= 8.1
- Composer
- MySQL/PostgreSQL
- Node.js & NPM (for frontend assets)

## 🛠️ Installation

### 1. Clone or Navigate to Project

```bash
cd "d:\Endow Education\endow-portal"
```

### 2. Configure Environment

```bash
cp .env.example .env
```

Edit `.env` file and configure your database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=endow_portal
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 3. Run Migrations

```bash
php artisan migrate
```

### 4. Seed Database (Roles, Permissions, Default Users)

```bash
php artisan db:seed
```

This will create:
- 4 Roles: Super Admin, Admin, Employee, Student
- All necessary permissions
- 3 default users:
  - **Super Admin**: superadmin@endowglobal.com / password
  - **Admin**: admin@endowglobal.com / password
  - **Employee**: employee@endowglobal.com / password

### 5. Generate Application Key (if not done)

```bash
php artisan key:generate
```

### 6. Run the Application

```bash
php artisan serve
```

Visit: `http://localhost:8000`

## 📁 Project Structure

```
endow-portal/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── StudentController.php
│   │   │   ├── FollowUpController.php
│   │   │   ├── ChecklistItemController.php
│   │   │   ├── DocumentController.php
│   │   │   └── DashboardController.php
│   │   └── Requests/
│   │       ├── StoreStudentRequest.php
│   │       ├── UpdateStudentRequest.php
│   │       ├── StoreFollowUpRequest.php
│   │       ├── UploadDocumentRequest.php
│   │       └── StoreChecklistItemRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Student.php
│   │   ├── FollowUp.php
│   │   ├── ChecklistItem.php
│   │   ├── StudentChecklist.php
│   │   ├── StudentDocument.php
│   │   └── ActivityLog.php
│   ├── Policies/
│   │   └── StudentPolicy.php
│   └── Services/
│       ├── PdfService.php
│       ├── ActivityLogService.php
│       └── ChecklistService.php
├── database/
│   ├── migrations/
│   │   ├── 2025_12_24_103338_create_permission_tables.php
│   │   ├── 2025_12_24_103354_create_students_table.php
│   │   ├── 2025_12_24_103401_create_follow_ups_table.php
│   │   ├── 2025_12_24_103401_create_checklist_items_table.php
│   │   ├── 2025_12_24_103401_create_student_checklists_table.php
│   │   ├── 2025_12_24_103402_create_student_documents_table.php
│   │   ├── 2025_12_24_103402_create_activity_logs_table.php
│   │   └── 2025_12_24_103402_add_columns_to_users_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── RolePermissionSeeder.php
└── routes/
    └── web.php
```

## 🔐 Default Credentials

After seeding, use these credentials to login:

| Role | Email | Password |
|------|-------|----------|
| Super Admin | superadmin@endowglobal.com | password |
| Admin | admin@endowglobal.com | password |
| Employee | employee@endowglobal.com | password |

⚠️ **IMPORTANT**: Change these passwords in production!

## 📝 Usage Guide

### Creating Students

1. Login as Admin/Employee
2. Navigate to Students > Create Student
3. Fill in student details
4. Student is automatically assigned to creator
5. Checklists are auto-initialized

### Managing Follow-ups

1. Open a student profile
2. Click "Add Follow-up"
3. Add notes (supports HTML formatting)
4. Set next follow-up date
5. Follow-ups are displayed in timeline

### Document Upload Flow

1. Student or Admin/Employee uploads PDF document
2. System validates: PDF only, max 10 MB
3. PDF is compressed (if needed)
4. Compressed PDF is encoded to Base64
5. Base64 string stored in database
6. Admin/Employee can approve/reject documents

### Checklist Management

1. Admin creates checklist items
2. Mark items as required/optional
3. Students see checklist on their dashboard
4. Upload documents for each checklist item
5. Admin/Employee approves/rejects submissions

## 🏗️ Architecture

### Service-Oriented Architecture

- **PdfService**: Handles PDF compression, Base64 encoding/decoding
- **ActivityLogService**: Centralized activity logging
- **ChecklistService**: Checklist initialization and progress tracking

### Policy-Based Authorization

All actions are protected by Laravel Policies:
- `StudentPolicy`: Controls student CRUD operations
- Permission checks integrated with Spatie Permission

### Form Request Validation

- Input validation separated into Form Request classes
- Custom error messages
- XSS protection for HTML content

### Database Design

- Proper foreign key constraints
- Soft deletes for data recovery
- Indexes on frequently queried columns
- Polymorphic relationships for activity logs

## 🔧 Configuration

### PDF Compression

Currently, the system checks file size limits. For production, implement actual PDF compression:

1. Install Ghostscript
2. Update `PdfService::compressAndEncode()` method
3. Or integrate cloud services (Cloudinary, ImageKit)

### Notifications

TODO markers are placed in controllers for notification implementation:

```php
// TODO: Send notification to student
```

Implement using:
- Laravel Mail
- Laravel Notifications
- Queue jobs for async sending

### Rich Text Editor

For follow-up notes, integrate:
- CKEditor
- TinyMCE
- Or similar WYSIWYG editor

## 🚨 Security Considerations

### Implemented
- ✅ CSRF protection (Laravel default)
- ✅ XSS protection for HTML content
- ✅ Policy-based authorization
- ✅ Form Request validation
- ✅ SQL injection protection (Eloquent ORM)
- ✅ Activity logging

### Recommended for Production
- [ ] Rate limiting on login
- [ ] Two-factor authentication
- [ ] Password complexity requirements
- [ ] Session timeout
- [ ] File upload virus scanning
- [ ] Regular security audits

## 📊 Database Schema

### Key Tables

**users**
- Standard Laravel auth fields
- `phone`, `status`, soft deletes

**students**
- Complete profile information
- `status` (application status)
- `account_status` (approval status)
- Foreign keys: `user_id`, `assigned_to`, `created_by`

**follow_ups**
- `student_id`, `note`, `next_follow_up_date`
- Foreign key: `created_by`

**checklist_items**
- `title`, `description`, `is_required`, `is_active`, `order`

**student_checklists**
- Junction table linking students and checklist items
- `status`: pending, submitted, approved, rejected

**student_documents**
- Stores Base64 encoded PDFs
- `file_data` (LONGTEXT for Base64)
- `filename`, `mime_type`, `file_size`

**activity_logs**
- Polymorphic logging
- `subject_type`, `subject_id`, `causer_type`, `causer_id`

## 🛣️ Roadmap

- [ ] Email notifications
- [ ] Advanced reporting
- [ ] Export to Excel/PDF
- [ ] Real-time notifications (WebSockets)
- [ ] Document versioning
- [ ] Multi-language support
- [ ] API endpoints for mobile app
- [ ] Advanced search and filters
- [ ] Calendar integration for follow-ups
- [ ] SMS notifications

## 🤝 Contributing

This is a production project for Endow Global Education. Internal contributions only.

## 📄 License

Proprietary - Endow Global Education

## 📞 Support

For support, contact the development team.

---

**Built with ❤️ using Laravel 10**
