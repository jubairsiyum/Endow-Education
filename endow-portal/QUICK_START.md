# 🚀 Quick Start Guide - Endow Global Education Portal

## Prerequisites Check

Before starting, ensure you have:
- ✅ PHP 8.1 or higher installed
- ✅ Composer installed
- ✅ MySQL/MariaDB installed and running
- ✅ Node.js and NPM installed

---

## Step-by-Step Setup (First Time)

### Step 1: Configure Database

1. **Start MySQL Server**
   - Windows: Start MySQL service from Services
   - Mac/Linux: `sudo service mysql start`

2. **Create Database**

Open MySQL command line or phpMyAdmin:

```sql
CREATE DATABASE endow_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

3. **Configure .env File**

Open `d:\Endow Education\endow-portal\.env` and update these lines:

```env
APP_NAME="Endow Global Education"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=endow_portal
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

**⚠️ Important**: Replace `your_mysql_password` with your actual MySQL root password!

### Step 2: Run Migrations and Seeders

Open terminal/PowerShell in project directory:

```bash
cd "d:\Endow Education\endow-portal"

# Run migrations (creates all tables)
php artisan migrate

# Run seeders (creates roles, permissions, and default users)
php artisan db:seed
```

**Expected Output:**
```
   INFO  Roles and Permissions seeded successfully!
   INFO  Super Admin: superadmin@endowglobal.com / password
   INFO  Admin: admin@endowglobal.com / password
   INFO  Employee: employee@endowglobal.com / password
```

### Step 3: Install Frontend Dependencies

```bash
npm install
npm run build
```

### Step 4: Start Development Server

```bash
php artisan serve
```

**Output:**
```
   INFO  Server running on [http://127.0.0.1:8000]
```

### Step 5: Access the Application

Open your browser and visit: **http://localhost:8000**

---

## 🔐 Login Credentials

Use these credentials to login:

| Role | Email | Password |
|------|-------|----------|
| **Super Admin** | superadmin@endowglobal.com | password |
| **Admin** | admin@endowglobal.com | password |
| **Employee** | employee@endowglobal.com | password |

⚠️ **Change these passwords immediately after first login!**

---

## 🎯 Quick Feature Test

### Test 1: Login as Super Admin

1. Go to: http://localhost:8000/login
2. Email: `superadmin@endowglobal.com`
3. Password: `password`
4. Click "Login"
5. You should see the admin dashboard

### Test 2: View Dashboard

After login, you'll see:
- Total students count (currently 0)
- Statistics by status
- Quick action buttons

### Test 3: Create a Student (When Views Are Complete)

1. Click "Add New Student" button
2. Fill in student details:
   - Name: John Doe
   - Email: john@example.com
   - Phone: +1234567890
   - Country: USA
   - Course: Computer Science
3. Click "Create Student"
4. Student will be automatically assigned to you
5. Checklists will be auto-initialized

---

## 🛠️ Troubleshooting

### Issue: "No connection could be made" Error

**Solution:**
1. Check MySQL is running
2. Verify database credentials in `.env`
3. Test MySQL connection:
   ```bash
   mysql -u root -p
   # Enter your password
   ```

### Issue: "Database 'endow_portal' doesn't exist"

**Solution:**
```sql
CREATE DATABASE endow_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Issue: "Class not found" Errors

**Solution:**
```bash
composer dump-autoload
php artisan clear-compiled
php artisan config:clear
```

### Issue: Migration Fails

**Solution:**
```bash
# Reset and re-run migrations
php artisan migrate:fresh --seed
```

### Issue: Permission Denied (Linux/Mac)

**Solution:**
```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R $USER:www-data storage bootstrap/cache
```

---

## 📁 What's Already Working

### ✅ Backend (90% Complete)

1. **Database Structure**
   - All 8 tables created
   - Relationships configured
   - Indexes applied

2. **User Authentication**
   - Login/Register pages
   - Password reset
   - Session management

3. **Role-Based Access Control**
   - 4 roles created (Super Admin, Admin, Employee, Student)
   - 58 permissions defined
   - Authorization policies in place

4. **Models & Business Logic**
   - All 7 models with relationships
   - 3 service classes (PDF, Checklist, ActivityLog)
   - Form validation ready

5. **Student Controller**
   - Create, Read, Update, Delete operations
   - Student approval workflow
   - Activity logging

### ⚠️ Frontend (20% Complete)

**What's Ready:**
- ✅ Layout with navigation
- ✅ Login/Register pages
- ✅ Admin dashboard view

**TODO (Next Steps):**
- ❌ Student list page with filters
- ❌ Student create/edit forms
- ❌ Student profile page
- ❌ Follow-up management interface
- ❌ Document upload interface
- ❌ Checklist management

---

## 📝 Next Development Steps

### Priority 1: Complete Student Views (2-3 days)

1. **Create: `resources/views/students/index.blade.php`**
   - Student list with search and filters
   - Pagination
   - Action buttons (view, edit, delete)

2. **Create: `resources/views/students/create.blade.php`**
   - Student creation form
   - Form validation display
   - Country dropdown

3. **Create: `resources/views/students/edit.blade.php`**
   - Student edit form
   - Pre-filled data
   - Assignment dropdown (Admin only)

4. **Create: `resources/views/students/show.blade.php`**
   - Student profile with tabs:
     - Profile information
     - Follow-ups timeline
     - Checklist progress
     - Uploaded documents
   - Approve/Reject buttons

### Priority 2: Complete Other Controllers (1-2 days)

1. **FollowUpController**
   - CRUD operations
   - Rich text editor integration

2. **DocumentController**
   - Upload handling
   - PDF compression
   - Approve/reject workflows

3. **ChecklistItemController**
   - Checklist template management
   - Reordering functionality

### Priority 3: Implement Notifications (1 day)

1. Email notifications for:
   - Student account approval
   - Document approval/rejection
   - New student registration

---

## 🔧 Useful Commands

### Development

```bash
# Start development server
php artisan serve

# Watch for file changes (CSS/JS)
npm run dev

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild autoloader
composer dump-autoload
```

### Database

```bash
# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Reset database and re-seed
php artisan migrate:fresh --seed

# Check migration status
php artisan migrate:status
```

### View Routes

```bash
# List all routes
php artisan route:list

# Filter by name
php artisan route:list --name=students
```

---

## 📚 File Locations Reference

### Important Files

```
Configuration:
  .env                                    # Database and app config

Database:
  database/migrations/                    # All database tables
  database/seeders/RolePermissionSeeder.php  # Roles & users

Models:
  app/Models/User.php                     # User model
  app/Models/Student.php                  # Student model
  app/Models/FollowUp.php                 # Follow-up model
  app/Models/ChecklistItem.php            # Checklist template
  app/Models/StudentChecklist.php         # Student checklist tracker
  app/Models/StudentDocument.php          # Document storage
  app/Models/ActivityLog.php              # Audit log

Controllers:
  app/Http/Controllers/StudentController.php     # Student management
  app/Http/Controllers/DashboardController.php   # Dashboards

Services:
  app/Services/PdfService.php             # PDF handling
  app/Services/ChecklistService.php       # Checklist logic
  app/Services/ActivityLogService.php     # Logging

Views:
  resources/views/layouts/app.blade.php   # Main layout
  resources/views/dashboard/admin.blade.php  # Admin dashboard
  resources/views/auth/                   # Login/register pages

Routes:
  routes/web.php                          # All web routes
```

---

## 🎓 Learning Resources

### Laravel Documentation
- Official Docs: https://laravel.com/docs/10.x
- Eloquent ORM: https://laravel.com/docs/10.x/eloquent
- Validation: https://laravel.com/docs/10.x/validation
- Authorization: https://laravel.com/docs/10.x/authorization

### Spatie Permission
- Docs: https://spatie.be/docs/laravel-permission/v6

### Bootstrap 5
- Docs: https://getbootstrap.com/docs/5.3

---

## ⚡ Performance Tips

### Development
- Use `php artisan serve` for local development
- Keep `APP_DEBUG=true` in `.env` for detailed errors
- Use browser DevTools for frontend debugging

### Production (When Ready)
- Set `APP_DEBUG=false` in `.env`
- Run `php artisan optimize`
- Enable OPcache
- Use queue workers for background jobs
- Set up Redis for cache

---

## 🔐 Security Checklist

Before going to production:

- [ ] Change all default passwords
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Use strong `APP_KEY` (generated automatically)
- [ ] Enable HTTPS (SSL certificate)
- [ ] Configure firewall
- [ ] Set up regular database backups
- [ ] Implement rate limiting
- [ ] Add two-factor authentication for admins
- [ ] Review and update permissions
- [ ] Test all authorization policies
- [ ] Scan for vulnerabilities

---

## 📞 Support

### Getting Help

1. **Check Documentation**
   - Read `PROJECT_README.md` for features
   - Read `SETUP_GUIDE.md` for detailed setup
   - Read `COMPLETION_SUMMARY.md` for current status

2. **Check Logs**
   ```bash
   # View Laravel logs
   tail -f storage/logs/laravel.log
   ```

3. **Debug Mode**
   - Ensure `APP_DEBUG=true` in `.env` during development
   - Check browser console for JavaScript errors
   - Use `dd()` or `dump()` in code for debugging

---

## 🎉 You're All Set!

The backend infrastructure is **complete and working**. Now you can:

1. ✅ Login with any of the default accounts
2. ✅ View the admin dashboard
3. ✅ Test authentication and authorization
4. 🔜 Start building the remaining views
5. 🔜 Complete the document upload flow
6. 🔜 Add notifications

**Happy Coding! 🚀**

---

**Last Updated**: December 24, 2025  
**Laravel Version**: 10.x  
**Project Status**: Backend 90% Complete, Frontend 20% Complete
