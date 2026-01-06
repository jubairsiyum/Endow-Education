<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deployment Verification - Endow Education</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 800px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .check-item {
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            background: #f8f9fa;
            border-left: 4px solid #ddd;
        }
        .check-item.success {
            background: #d4edda;
            border-left-color: #28a745;
        }
        .check-item.error {
            background: #f8d7da;
            border-left-color: #dc3545;
        }
        .check-item.warning {
            background: #fff3cd;
            border-left-color: #ffc107;
        }
        .icon {
            width: 24px;
            height: 24px;
            margin-right: 15px;
            font-size: 18px;
        }
        .info {
            background: #e7f3ff;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            border-left: 4px solid #0066cc;
        }
        .info h3 {
            color: #0066cc;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .info p {
            color: #555;
            line-height: 1.6;
            font-size: 14px;
        }
        .code {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            margin-top: 15px;
            overflow-x: auto;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin-top: 20px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Deployment Verification</h1>
        <p class="subtitle">Endow Education Portal - Hostinger Deployment Check</p>

        <?php
        // Check 1: PHP Version
        $phpVersion = PHP_VERSION;
        $phpOk = version_compare($phpVersion, '8.1.0', '>=');
        ?>
        <div class="check-item <?= $phpOk ? 'success' : 'error' ?>">
            <span class="icon"><?= $phpOk ? '✅' : '❌' ?></span>
            <div>
                <strong>PHP Version:</strong> <?= $phpVersion ?>
                <?= $phpOk ? '(Compatible)' : '(Requires PHP 8.1+)' ?>
            </div>
        </div>

        <?php
        // Check 2: .env file
        $envExists = file_exists(__DIR__ . '/../.env');
        ?>
        <div class="check-item <?= $envExists ? 'success' : 'error' ?>">
            <span class="icon"><?= $envExists ? '✅' : '❌' ?></span>
            <div>
                <strong>.env File:</strong>
                <?= $envExists ? 'Found' : 'Missing - Copy .env.example to .env' ?>
            </div>
        </div>

        <?php
        // Check 3: Storage directory writable
        $storagePath = __DIR__ . '/../storage/app/public';
        $storageWritable = is_writable($storagePath);
        ?>
        <div class="check-item <?= $storageWritable ? 'success' : 'error' ?>">
            <span class="icon"><?= $storageWritable ? '✅' : '❌' ?></span>
            <div>
                <strong>Storage Writable:</strong>
                <?= $storageWritable ? 'Yes' : 'No - Set permissions to 775' ?>
            </div>
        </div>

        <?php
        // Check 4: Bootstrap cache writable
        $bootstrapPath = __DIR__ . '/../bootstrap/cache';
        $bootstrapWritable = is_writable($bootstrapPath);
        ?>
        <div class="check-item <?= $bootstrapWritable ? 'success' : 'error' ?>">
            <span class="icon"><?= $bootstrapWritable ? '✅' : '❌' ?></span>
            <div>
                <strong>Bootstrap Cache Writable:</strong>
                <?= $bootstrapWritable ? 'Yes' : 'No - Set permissions to 775' ?>
            </div>
        </div>

        <?php
        // Check 5: Vendor directory exists
        $vendorExists = is_dir(__DIR__ . '/../vendor');
        ?>
        <div class="check-item <?= $vendorExists ? 'success' : 'warning' ?>">
            <span class="icon"><?= $vendorExists ? '✅' : '⚠️' ?></span>
            <div>
                <strong>Composer Dependencies:</strong>
                <?= $vendorExists ? 'Installed' : 'Missing - Run composer install' ?>
            </div>
        </div>

        <?php
        // Check 6: Required PHP extensions
        $extensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'fileinfo', 'gd'];
        $missingExtensions = [];
        foreach ($extensions as $ext) {
            if (!extension_loaded($ext)) {
                $missingExtensions[] = $ext;
            }
        }
        $extensionsOk = empty($missingExtensions);
        ?>
        <div class="check-item <?= $extensionsOk ? 'success' : 'error' ?>">
            <span class="icon"><?= $extensionsOk ? '✅' : '❌' ?></span>
            <div>
                <strong>Required PHP Extensions:</strong>
                <?= $extensionsOk ? 'All present' : 'Missing: ' . implode(', ', $missingExtensions) ?>
            </div>
        </div>

        <div class="info">
            <h3>📋 Next Steps</h3>
            <?php if ($phpOk && $envExists && $storageWritable && $bootstrapWritable && $vendorExists && $extensionsOk): ?>
                <p>✨ <strong>All checks passed!</strong> Your application is ready to run.</p>
                <p style="margin-top: 10px;">You can now:</p>
                <ul style="margin-left: 20px; margin-top: 10px;">
                    <li>Import your database via phpMyAdmin</li>
                    <li>Configure your .env file with database credentials</li>
                    <li>Access your application</li>
                </ul>
                <div class="code">php artisan config:cache<br>php artisan route:cache<br>php artisan view:cache</div>
            <?php else: ?>
                <p><strong>Action Required:</strong> Fix the issues marked with ❌ or ⚠️ above.</p>
                <p style="margin-top: 10px;">Refer to <code>HOSTINGER_DEPLOYMENT.md</code> for detailed instructions.</p>
            <?php endif; ?>
        </div>

        <a href="/" class="btn">Go to Application →</a>
    </div>
</body>
</html>
