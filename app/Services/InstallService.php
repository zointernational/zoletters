<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use PDO;
use PDOException;

class InstallService
{
    /**
     * Check system requirements
     */
    public function checkRequirements(): array
    {
        $requirements = [];

        // PHP Version
        $phpVersion = phpversion();
        $requirements['php_version'] = [
            'name' => 'PHP Version',
            'status' => version_compare($phpVersion, '8.1.0', '>='),
            'required' => '8.1.0+',
            'current' => $phpVersion,
        ];

        // Required Extensions
        $extensions = [
            'pdo' => 'PDO',
            'pdo_mysql' => 'PDO MySQL',
            'mbstring' => 'MBString',
            'openssl' => 'OpenSSL',
            'tokenizer' => 'Tokenizer',
            'xml' => 'XML',
            'ctype' => 'CType',
            'json' => 'JSON',
            'fileinfo' => 'Fileinfo',
            'curl' => 'cURL',
        ];

        foreach ($extensions as $ext => $name) {
            $loaded = extension_loaded($ext);
            $requirements['extensions'][$ext] = [
                'name' => $name,
                'status' => $loaded,
            ];
        }

        // Required Functions
        $functions = [
            'proc_open' => 'proc_open',
            'shell_exec' => 'shell_exec',
            'exec' => 'exec',
        ];

        foreach ($functions as $func => $name) {
            $enabled = function_exists($func);
            $requirements['functions'][$func] = [
                'name' => $name,
                'status' => $enabled,
            ];
        }

        return $requirements;
    }

    /**
     * Check folder permissions
     */
    public function checkPermissions(): array
    {
        $basePath = base_path();
        $permissions = [];

        $directories = [
            'storage' => 'Storage Directory',
            'storage/app' => 'Storage App',
            'storage/app/public' => 'Storage App Public',
            'storage/framework' => 'Storage Framework',
            'storage/framework/cache' => 'Storage Cache',
            'storage/framework/sessions' => 'Storage Sessions',
            'storage/framework/views' => 'Storage Views',
            'storage/logs' => 'Storage Logs',
            'bootstrap/cache' => 'Bootstrap Cache',
            'public/uploads' => 'Public Uploads',
        ];

        foreach ($directories as $dir => $name) {
            $path = $basePath . '/' . $dir;
            $exists = is_dir($path);
            $writable = $exists && is_writable($path);
            
            $permissions[$dir] = [
                'name' => $name,
                'path' => $dir,
                'exists' => $exists,
                'writable' => $writable,
                'status' => $exists && $writable,
            ];
        }

        return $permissions;
    }

    /**
     * Verify database connection
     */
    public function verifyDatabase(array $data): array
    {
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                $data['db_host'],
                $data['db_port'] ?? 3306,
                $data['db_name']
            );

            $pdo = new PDO(
                $dsn,
                $data['db_user'],
                $data['db_pass'] ?? '',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );

            // Test query
            $pdo->query('SELECT 1');

            return [
                'success' => true,
                'message' => 'Database connection successful!',
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Create .env file
     */
    public function createEnvFile(array $data): bool
    {
        $appKey = 'base64:' . base64_encode(random_bytes(32));
        $appUrl = $data['app_url'] ?? 'https://' . request()->getHost();

        $envContent = <<<ENV
APP_NAME="ZO Letters"
APP_ENV=production
APP_KEY={$appKey}
APP_DEBUG=false
APP_URL={$appUrl}

LOG_CHANNEL=daily
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=info

DB_CONNECTION=mysql
DB_HOST={$data['db_host']}
DB_PORT={$data['db_port'] ?? 3306}
DB_DATABASE={$data['db_name']}
DB_USERNAME={$data['db_user']}
DB_PASSWORD={$data['db_pass'] ?? ''}

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
ENV;

        $envPath = base_path('.env');
        
        // Backup existing .env if exists
        if (file_exists($envPath)) {
            copy($envPath, $envPath . '.backup');
        }

        return file_put_contents($envPath, $envContent) !== false;
    }

    /**
     * Run database migrations
     */
    public function runMigrations(): array
    {
        try {
            // Clear config cache
            if (file_exists(base_path('bootstrap/cache/config.php'))) {
                unlink(base_path('bootstrap/cache/config.php'));
            }

            // Run migrations
            \Artisan::call('migrate', ['--force' => true]);
            $output = \Artisan::output();

            return [
                'success' => true,
                'message' => 'Migrations completed successfully',
                'output' => $output,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Migration failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Create administrator user
     */
    public function createAdminUser(array $data): array
    {
        try {
            $hashedPassword = bcrypt($data['admin_password']);

            // Create users table if not exists
            if (!Schema::hasTable('users')) {
                Schema::create('users', function ($table) {
                    $table->id();
                    $table->string('name');
                    $table->string('email')->unique();
                    $table->timestamp('email_verified_at')->nullable();
                    $table->string('password');
                    $table->rememberToken();
                    $table->timestamps();
                });
            }

            // Check if admin already exists
            $existingAdmin = DB::table('users')
                ->where('email', $data['admin_email'])
                ->first();

            if ($existingAdmin) {
                // Update existing admin
                DB::table('users')
                    ->where('email', $data['admin_email'])
                    ->update([
                        'name' => $data['admin_name'],
                        'password' => $hashedPassword,
                        'updated_at' => now(),
                    ]);
            } else {
                // Create new admin
                DB::table('users')->insert([
                    'name' => $data['admin_name'],
                    'email' => $data['admin_email'],
                    'password' => $hashedPassword,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return [
                'success' => true,
                'message' => 'Administrator account created successfully',
                'email' => $data['admin_email'],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create admin: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Create storage link
     */
    public function createStorageLink(): bool
    {
        try {
            $publicStorage = public_path('storage');
            $storageAppPublic = storage_path('app/public');

            // Remove existing symlink if exists
            if (is_link($publicStorage)) {
                unlink($publicStorage);
            }

            // Create symlink
            symlink($storageAppPublic, $publicStorage);

            return is_link($publicStorage) || is_dir($publicStorage);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Optimize Laravel
     */
    public function optimizeLaravel(): array
    {
        try {
            // Clear caches
            \Artisan::call('config:clear');
            \Artisan::call('cache:clear');
            \Artisan::call('route:clear');
            \Artisan::call('view:clear');

            // Create caches
            \Artisan::call('config:cache');
            \Artisan::call('route:cache');
            \Artisan::call('view:cache');

            return [
                'success' => true,
                'message' => 'Laravel optimized successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Optimization failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Run full installation
     */
    public function install(array $data): array
    {
        $results = [
            'success' => true,
            'steps' => [],
        ];

        // Step 1: Create .env file
        if (!$this->createEnvFile($data)) {
            return [
                'success' => false,
                'message' => 'Failed to create .env file',
                'steps' => [['name' => 'Environment', 'success' => false]],
            ];
        }
        $results['steps'][] = ['name' => 'Environment Configuration', 'success' => true];

        // Clear config cache to load new .env
        if (file_exists(base_path('bootstrap/cache/config.php'))) {
            unlink(base_path('bootstrap/cache/config.php'));
        }

        // Step 2: Run migrations
        $migrateResult = $this->runMigrations();
        $results['steps'][] = ['name' => 'Database Migrations', 'success' => $migrateResult['success']];
        
        if (!$migrateResult['success']) {
            $results['success'] = false;
            $results['message'] = $migrateResult['message'];
            return $results;
        }

        // Step 3: Create admin user
        $adminResult = $this->createAdminUser($data);
        $results['steps'][] = ['name' => 'Administrator Account', 'success' => $adminResult['success']];
        $results['admin_email'] = $adminResult['email'] ?? $data['admin_email'];

        if (!$adminResult['success']) {
            $results['success'] = false;
            $results['message'] = $adminResult['message'];
            return $results;
        }

        // Step 4: Create storage link
        $storageResult = $this->createStorageLink();
        $results['steps'][] = ['name' => 'Storage Link', 'success' => $storageResult];

        // Step 5: Optimize Laravel (optional - catch errors)
        $optimizeResult = $this->optimizeLaravel();
        $results['steps'][] = ['name' => 'Laravel Optimization', 'success' => $optimizeResult['success']];

        // Set session for installation
        session(['install_completed' => true]);

        return $results;
    }

    /**
     * Check if already installed
     */
    public function isInstalled(): bool
    {
        $envPath = base_path('.env');
        
        if (!file_exists($envPath)) {
            return false;
        }

        try {
            // Check if database is configured
            $dbHost = env('DB_HOST');
            $dbDatabase = env('DB_DATABASE');

            if (empty($dbHost) || empty($dbDatabase)) {
                return false;
            }

            // Check database connection
            DB::connection()->getPdo();

            // Check if users exist
            $userCount = DB::table('users')->count();

            return $userCount > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
}
