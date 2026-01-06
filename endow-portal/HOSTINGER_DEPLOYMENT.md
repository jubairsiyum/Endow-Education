# Hostinger Deployment Guide - Endow Education Portal

## Pre-Deployment Checklist

### 1. Code Preparation (Already Done)
✅ Storage helper created (`app/Helpers/StorageHelper.php`)
✅ Helper function added (`app/Helpers/helpers.php`)
✅ Storage route added to serve files without symlink
✅ All views updated to use `storage_url()` helper
✅ .htaccess configured with security headers
✅ Composer autoload updated

### 2. Run This Command Locally Before Upload
```bash
composer dump-autoload
```

## Hostinger Deployment Steps

### Step 1: Prepare Files
1. **Compress the project** (excluding certain folders):
   - DO NOT include: `node_modules/`, `vendor/`, `.git/`, `storage/logs/`, `storage/framework/cache/`
   - Include everything else

### Step 2: Upload to Hostinger
1. Login to Hostinger File Manager or use FTP
2. Navigate to your domain folder (e.g., `public_html` or `domains/yourdomain.com`)
3. Upload the compressed project file
4. Extract it

### Step 3: Set Up Folder Structure
Your structure should look like this:
```
public_html/              (or domains/yourdomain.com/)
├── .htaccess            ← ROOT .htaccess (redirects to public/)
├── public/              ← Laravel public folder
│   ├── index.php
│   ├── .htaccess
│   ├── css/
│   ├── js/
│   └── robots.txt
├── app/
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env
└── composer.json
```

### Step 4: Configure .htaccess Redirect (Hostinger Method)
**Important**: On Hostinger shared hosting, you cannot change the document root via control panel.
Instead, use the root `.htaccess` file to redirect to the `public` folder.

1. The root `.htaccess` file is already included in the project
2. Edit the root `.htaccess` file (NOT `public/.htaccess`)
3. Replace `yourdomain.com` with your actual domain name:

```apache
RewriteCond %{HTTP_HOST} ^yourdomain\.com$ [NC,OR]
RewriteCond %{HTTP_HOST} ^www\.yourdomain\.com$
```

For example, if your domain is `endowedu.com`:
```apache
RewriteCond %{HTTP_HOST} ^endowedu\.com$ [NC,OR]
RewriteCond %{HTTP_HOST} ^www\.endowedu\.com$
```

4. Save the file
5. **Do NOT change Document Root in Hostinger control panel** - let it point to your root folder

### Step 5: Install Composer Dependencies
1. Use Hostinger SSH access or File Manager terminal
2. Navigate to project root (not public folder)
3. Run:
```bash
cd /home/username/public_html  # Adjust path as needed
composer install --optimize-autoloader --no-dev
```

If composer is not available, download dependencies locally and upload the `vendor` folder.

### Step 6: Set Up .env File
1. Rename `.env.example` to `.env` or create new `.env`
2. Update these critical settings:

```env
APP_NAME="Endow Education Portal"
APP_ENV=production
APP_KEY=base64:YOUR_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

FILESYSTEM_DISK=public

SESSION_DRIVER=file
QUEUE_CONNECTION=sync

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=your@email.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your@email.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Step 7: Generate Application Key
```bash
php artisan key:generate
```

### Step 8: Set Up Database
1. Create MySQL database in Hostinger control panel
2. Import your database SQL file via phpMyAdmin:
   - Login to phpMyAdmin
   - Select your database
   - Click "Import"
   - Choose your SQL file
   - Click "Go"

### Step 9: Set Folder Permissions
Set these permissions via File Manager or FTP:

```
storage/                     775 or 755
storage/app/                 775 or 755
storage/app/public/          775 or 755
storage/framework/           775 or 755
storage/framework/cache/     775 or 755
storage/framework/sessions/  775 or 755
storage/framework/views/     775 or 755
storage/logs/                775 or 755
bootstrap/cache/             775 or 755
```

### Step 10: Clear and Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 11: Test Storage Route
1. Upload a test image to `storage/app/public/test.jpg`
2. Visit: `https://yourdomain.com/storage/test.jpg`
3. Image should display (served through the storage route)

### Step 12: Security Check
- [ ] Verify `.env` is not accessible via browser
- [ ] Verify `storage` folder is not accessible directly
- [ ] Test file uploads work correctly
- [ ] Test file downloads work correctly
- [ ] Test all image/document displays

## Important Notes

### Storage Without Symlink
This project is configured to work WITHOUT `php artisan storage:link` command. Files are served through a route:

- **Route**: `/storage/{path}` → `routes/web.php`
- **Helper**: `storage_url($path)` → replaces `asset('storage/'.$path)`
- **Storage**: Files stored in `storage/app/public/`
- **Access**: Via route, not direct file access

### Common Issues

#### Issue 1: 500 Internal Server Error
**Solution**: 
- Check folder permissions (755 for directories, 644 for files)
- Check `.env` configuration
- Check error logs: `storage/logs/laravel.log`

#### Issue 2: Images Not Loading
**Solution**:
- Verify files exist in `storage/app/public/`
- Check storage route is working: visit `/storage/test.jpg`
- Clear cache: `php artisan cache:clear`

#### Issue 3: Database Connection Error
**Solution**:
- Verify database credentials in `.env`
- Check database exists in Hostinger
- Verify database user has proper privileges

#### Issue 4: Upload Fails
**Solution**:
- Check folder permissions on `storage/app/public/`
- Verify upload limits in `.env` or php.ini
- Check `storage/logs/laravel.log` for errors

## Maintenance Commands

### Clear All Cache
```bash
php artisan optimize:clear
```

### View Logs
```bash
tail -f storage/logs/laravel.log
```

### Database Backup (Regular basis)
- Use phpMyAdmin Export feature
- Or use command: `mysqldump -u user -p database > backup.sql`

## Support Checklist
- [ ] Application loads without errors
- [ ] Admin login works
- [ ] Student login works
- [ ] File uploads work
- [ ] File downloads work
- [ ] Images display correctly
- [ ] Email sending works (test with password reset)
- [ ] All permissions work correctly

## Post-Deployment
1. Test all functionality thoroughly
2. Monitor `storage/logs/laravel.log` for errors
3. Set up regular database backups
4. Set up automated Laravel scheduler (if using cron jobs)
5. Consider setting up SSL certificate (Hostinger provides free SSL)

## Rollback Plan
If something goes wrong:
1. Restore previous files from backup
2. Restore database from backup
3. Clear all cache
4. Verify `.env` configuration

---

**Last Updated**: January 7, 2026
**Deployment Ready**: ✅ Yes
