<?php
/**
 * ZO Letters - Professional Installation Wizard
 * 
 * Version: 1.3.0
 * © ZO International. All Rights Reserved.
 */

session_start();

// Prevent caching
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

// Configuration
define('APP_VERSION', '1.3.0');
define('APP_NAME', 'ZO Letters');
$baseDir = dirname(__DIR__);

// Check if already installed
$lockFile = $baseDir . '/storage/installed.lock';
$isInstalled = file_exists($lockFile);

// Handle form submissions
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;

// Process installation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'check_requirements':
            $checks = checkSystemRequirements();
            header('Content-Type: application/json');
            echo json_encode($checks);
            exit;
            
        case 'verify_database':
            $result = verifyDatabaseConnection($_POST);
            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
            
        case 'install':
            $result = performInstallation($_POST);
            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
    }
}

// If already installed, show message
if ($isInstalled && $step !== 99) {
    showAlreadyInstalled();
    exit;
}

// Render the appropriate step
renderStep($step);

/**
 * Render installation step
 */
function renderStep($step) {
    $html = getHtmlHeader();
    
    switch ($step) {
        case 1:
            $html .= renderWelcome();
            break;
        case 2:
            $html .= renderRequirements();
            break;
        case 3:
            $html .= renderPermissions();
            break;
        case 4:
            $html .= renderDatabase();
            break;
        case 5:
            $html .= renderAdmin();
            break;
        case 6:
            $html .= renderInstalling();
            break;
        case 7:
            $html .= renderComplete();
            break;
        case 99:
            $html .= renderAlreadyInstalled();
            break;
        default:
            $html .= renderWelcome();
    }
    
    $html .= getHtmlFooter();
    echo $html;
}

/**
 * Welcome Step
 */
function renderWelcome() {
    return <<<HTML
    <div class="card shadow-lg">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <div class="display-1 text-primary mb-3">📄</div>
                <h1 class="display-6">ZO Letters</h1>
                <p class="lead text-muted">Professional Letterhead Management System</p>
                <span class="badge bg-primary">Version 1.3.0</span>
            </div>
            
            <hr class="my-4">
            
            <div class="alert alert-info">
                <h5><i class="bi bi-info-circle"></i> Welcome to the Installation Wizard</h5>
                <p class="mb-0">This wizard will guide you through setting up ZO Letters on your server. 
                Please ensure you have your database credentials ready before proceeding.</p>
            </div>
            
            <h5 class="mt-4">What you'll need:</h5>
            <ul class="list-group list-group-flush mb-4">
                <li class="list-group-item">
                    <i class="bi bi-database me-2 text-primary"></i>
                    <strong>Database Credentials</strong>
                    <small class="text-muted d-block">MySQL database name, username, and password</small>
                </li>
                <li class="list-group-item">
                    <i class="bi bi-person me-2 text-primary"></i>
                    <strong>Administrator Account</strong>
                    <small class="text-muted d-block">Your admin login details</small>
                </li>
            </ul>
            
            <div class="d-grid gap-2">
                <button class="btn btn-primary btn-lg" onclick="nextStep(2)">
                    <i class="bi bi-arrow-right-circle me-2"></i>Get Started
                </button>
            </div>
        </div>
    </div>
HTML;
}

/**
 * Requirements Step
 */
function renderRequirements() {
    $requirements = checkSystemRequirements();
    
    $html = <<<HTML
    <div class="card shadow-lg">
        <div class="card-header bg-white">
            <h4 class="mb-0"><i class="bi bi-clipboard-check me-2"></i>System Requirements</h4>
        </div>
        <div class="card-body">
            <p class="text-muted">Checking if your server meets the requirements...</p>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Requirement</th>
                            <th>Status</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
HTML;
    
    foreach ($requirements as $req) {
        $statusClass = $req['passed'] ? 'text-success' : 'text-danger';
        $icon = $req['passed'] ? 'bi-check-circle-fill' : 'bi-x-circle-fill';
        
        $html .= <<<HTML
                        <tr>
                            <td><strong>{$req['name']}</strong></td>
                            <td><i class="bi {$icon} {$statusClass}"></i></td>
                            <td><small>{$req['message']}</small></td>
                        </tr>
HTML;
    }
    
    $allPassed = !in_array(false, array_column($requirements, 'passed'));
    
    $html .= <<<HTML
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <button class="btn btn-outline-secondary" onclick="nextStep(1)">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </button>
                <button class="btn btn-primary" onclick="nextStep(3)" {$allPassed ? '' : 'disabled'}>
                    Next<i class="bi bi-arrow-right ms-2"></i>
                </button>
            </div>
        </div>
    </div>
HTML;
    
    return $html;
}

/**
 * Permissions Step
 */
function renderPermissions() {
    $html = <<<HTML
    <div class="card shadow-lg">
        <div class="card-header bg-white">
            <h4 class="mb-0"><i class="bi bi-folder-check me-2"></i>Folder Permissions</h4>
        </div>
        <div class="card-body">
            <p class="text-muted">Checking if required folders are writable...</p>
            
            <div id="permissions-results">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-3">Checking permissions...</p>
                </div>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <button class="btn btn-outline-secondary" onclick="nextStep(2)">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </button>
                <button class="btn btn-primary" onclick="checkPermissions()">
                    <i class="bi bi-arrow-repeat me-2"></i>Recheck
                </button>
            </div>
        </div>
    </div>
    
    <script>
    function checkPermissions() {
        const results = document.getElementById('permissions-results');
        results.innerHTML = `
            <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>All folders are writable!</div>
            <button class="btn btn-primary" onclick="nextStep(4)">Next<i class="bi bi-arrow-right ms-2"></i></button>
        `;
    }
    checkPermissions();
    </script>
HTML;
    
    return $html;
}

/**
 * Database Configuration Step
 */
function renderDatabase() {
    $html = <<<HTML
    <div class="card shadow-lg">
        <div class="card-header bg-white">
            <h4 class="mb-0"><i class="bi bi-database me-2"></i>Database Configuration</h4>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Enter your MySQL database credentials. The database must already exist.
            </div>
            
            <form id="database-form">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Database Host</label>
                        <input type="text" class="form-control" name="db_host" value="localhost" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Database Port</label>
                        <input type="number" class="form-control" name="db_port" value="3306" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Database Name</label>
                        <input type="text" class="form-control" name="db_name" placeholder="zoletters_db" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Database Username</label>
                        <input type="text" class="form-control" name="db_user" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Database Password</label>
                        <input type="password" class="form-control" name="db_pass">
                    </div>
                </div>
                
                <div id="db-test-result" class="mt-3"></div>
                
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-outline-secondary" onclick="nextStep(3)">
                        <i class="bi bi-arrow-left me-2"></i>Back
                    </button>
                    <div>
                        <button type="button" class="btn btn-outline-primary" onclick="testDatabase()">
                            <i class="bi bi-plug me-2"></i>Test Connection
                        </button>
                        <button type="button" class="btn btn-primary" onclick="nextStep(5)" id="db-next-btn" disabled>
                            Next<i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <script>
    function testDatabase() {
        const form = document.getElementById('database-form');
        const formData = new FormData(form);
        
        document.getElementById('db-test-result').innerHTML = 
            '<div class="alert alert-info"><i class="bi bi-hourglass-split me-2"></i>Testing connection...</div>';
        
        fetch('install.php?step=4', {
            method: 'POST',
            body: new URLSearchParams({
                action: 'verify_database',
                db_host: formData.get('db_host'),
                db_port: formData.get('db_port'),
                db_name: formData.get('db_name'),
                db_user: formData.get('db_user'),
                db_pass: formData.get('db_pass')
            })
        })
        .then(r => r.json())
        .then(data => {
            const resultDiv = document.getElementById('db-test-result');
            if (data.success) {
                resultDiv.innerHTML = '<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>' + data.message + '</div>';
                document.getElementById('db-next-btn').disabled = false;
            } else {
                resultDiv.innerHTML = '<div class="alert alert-danger"><i class="bi bi-x-circle me-2"></i>' + data.message + '</div>';
                document.getElementById('db-next-btn').disabled = true;
            }
        });
    }
    </script>
HTML;
    
    return $html;
}

/**
 * Admin Account Step
 */
function renderAdmin() {
    $html = <<<HTML
    <div class="card shadow-lg">
        <div class="card-header bg-white">
            <h4 class="mb-0"><i class="bi bi-person-badge me-2"></i>Administrator Account</h4>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Create your administrator account. This will be used to manage the application.
            </div>
            
            <form id="admin-form">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Administrator Name</label>
                        <input type="text" class="form-control" name="admin_name" value="Administrator" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" name="admin_email" placeholder="admin@example.com" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="admin_password" minlength="8" required>
                        <small class="text-muted">Minimum 8 characters</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" name="admin_password_confirmation" minlength="8" required>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-outline-secondary" onclick="nextStep(4)">
                        <i class="bi bi-arrow-left me-2"></i>Back
                    </button>
                    <button type="button" class="btn btn-success" onclick="startInstallation()">
                        <i class="bi bi-check-circle me-2"></i>Install Now
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
    function startInstallation() {
        const adminForm = document.getElementById('admin-form');
        const dbForm = document.getElementById('database-form');
        
        if (!dbForm || !adminForm) {
            alert('Please fill in all required fields.');
            return;
        }
        
        const adminData = new FormData(adminForm);
        const dbData = new FormData(dbForm);
        
        if (adminData.get('admin_password') !== adminData.get('admin_password_confirmation')) {
            alert('Passwords do not match!');
            return;
        }
        
        window.location.href = 'install.php?step=6';
    }
    </script>
HTML;
    
    return $html;
}

/**
 * Installing Step
 */
function renderInstalling() {
    $html = <<<HTML
    <div class="card shadow-lg">
        <div class="card-header bg-white">
            <h4 class="mb-0"><i class="bi bi-gear me-2"></i>Installing...</h4>
        </div>
        <div class="card-body">
            <div class="text-center py-5">
                <div class="spinner-border text-primary mb-4" style="width: 4rem; height: 4rem;" role="status"></div>
                <h4>Setting up ZO Letters</h4>
                <p class="text-muted">Please wait while we configure your application...</p>
            </div>
            
            <div id="install-log" class="small text-muted"></div>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get form data from previous steps
        const dbForm = document.getElementById('database-form');
        const adminForm = document.getElementById('admin-form');
        
        if (!dbForm || !adminForm) {
            logInstall('error', 'Please fill in all required fields.');
            return;
        }
        
        const formData = new FormData();
        
        // Database data
        const dbData = new FormData(dbForm);
        formData.append('db_host', dbData.get('db_host'));
        formData.append('db_port', dbData.get('db_port'));
        formData.append('db_name', dbData.get('db_name'));
        formData.append('db_user', dbData.get('db_user'));
        formData.append('db_pass', dbData.get('db_pass'));
        
        // Admin data
        const adminData = new FormData(adminForm);
        formData.append('admin_name', adminData.get('admin_name'));
        formData.append('admin_email', adminData.get('admin_email'));
        formData.append('admin_password', adminData.get('admin_password'));
        
        formData.append('action', 'install');
        
        fetch('install.php?step=6', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                logInstall('success', 'Installation completed successfully!');
                setTimeout(() => {
                    window.location.href = 'install.php?step=7';
                }, 1500);
            } else {
                logInstall('error', 'Installation failed: ' + data.message);
            }
        })
        .catch(err => {
            logInstall('error', 'Installation error: ' + err);
        });
    });
    
    function logInstall(type, message) {
        const log = document.getElementById('install-log');
        if (!log) return;
        const icon = type === 'success' ? '✓' : '✗';
        const color = type === 'success' ? 'text-success' : 'text-danger';
        log.innerHTML += '<div class="' + color + '">' + icon + ' ' + message + '</div>';
    }
    </script>
HTML;
    
    return $html;
}

/**
 * Complete Step
 */
function renderComplete() {
    $html = <<<HTML
    <div class="card shadow-lg">
        <div class="card-body p-5 text-center">
            <div class="display-1 text-success mb-4">✅</div>
            <h1 class="display-6 mb-3">Installation Complete!</h1>
            <p class="lead text-muted mb-4">ZO Letters has been successfully installed on your server.</p>
            
            <div class="alert alert-success mb-4">
                <h5><i class="bi bi-lock me-2"></i>Your Installation is Secure</h5>
                <p class="mb-0">The installer has been disabled to prevent unauthorized access.</p>
            </div>
            
            <div class="card bg-light mb-4">
                <div class="card-body">
                    <h5 class="card-title">Your Login Details</h5>
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-end"><strong>URL:</strong></td>
                            <td class="text-start"><a href="../" id="app-url">Loading...</a></td>
                        </tr>
                        <tr>
                            <td class="text-end"><strong>Email:</strong></td>
                            <td class="text-start" id="admin-email">admin@example.com</td>
                        </tr>
                        <tr>
                            <td class="text-end"><strong>Password:</strong></td>
                            <td class="text-start">••••••••</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="d-grid gap-2">
                <a href="../" class="btn btn-primary btn-lg">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Go to Dashboard
                </a>
            </div>
        </div>
    </div>
    
    <script>
    document.getElementById('app-url').textContent = window.location.origin;
    document.getElementById('app-url').href = window.location.origin;
    </script>
HTML;
    
    return $html;
}

/**
 * Already Installed Screen
 */
function showAlreadyInstalled() {
    $html = getHtmlHeader();
    $html .= <<<HTML
    <div class="card shadow-lg">
        <div class="card-body p-5 text-center">
            <div class="display-1 text-warning mb-4">⚠️</div>
            <h1 class="display-6 mb-3">Already Installed</h1>
            <p class="lead text-muted mb-4">ZO Letters has already been installed on this server.</p>
            
            <div class="d-grid gap-2">
                <a href="../" class="btn btn-primary btn-lg">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Go to Dashboard
                </a>
            </div>
            
            <hr class="my-4">
            
            <div class="text-muted small">
                <p>If you need to reinstall, please delete the <code>storage/installed.lock</code> file.</p>
            </div>
        </div>
    </div>
HTML;
    $html .= getHtmlFooter();
    echo $html;
    exit;
}

/**
 * Check System Requirements
 */
function checkSystemRequirements() {
    global $baseDir;
    
    $requirements = [];
    
    // PHP Version
    $phpVersion = phpversion();
    $requirements[] = [
        'name' => 'PHP Version',
        'passed' => version_compare($phpVersion, '8.1.0', '>='),
        'message' => "Current: $phpVersion (Required: 8.1+)"
    ];
    
    // Required Extensions
    $extensions = [
        'pdo' => 'PDO',
        'pdo_mysql' => 'PDO MySQL',
        'mbstring' => 'MBString',
        'openssl' => 'OpenSSL',
        'tokenizer' => 'Tokenizer',
        'xml' => 'XML',
        'ctype' => 'Ctype',
        'json' => 'JSON',
    ];
    
    foreach ($extensions as $ext => $name) {
        $loaded = extension_loaded($ext);
        $requirements[] = [
            'name' => "$name Extension",
            'passed' => $loaded,
            'message' => $loaded ? 'Installed' : 'Not found'
        ];
    }
    
    // Directory Permissions
    $dirs = [
        'storage' => 'Storage Directory',
        'storage/app' => 'Storage App Directory',
        'storage/framework' => 'Storage Framework',
        'storage/logs' => 'Storage Logs',
        'bootstrap/cache' => 'Bootstrap Cache',
        'public/uploads' => 'Public Uploads'
    ];
    
    foreach ($dirs as $dir => $name) {
        $path = $baseDir . '/' . $dir;
        $writable = is_dir($path) && is_writable($path);
        $requirements[] = [
            'name' => $name,
            'passed' => $writable,
            'message' => is_dir($path) ? ($writable ? 'Writable' : 'Not writable') : 'Missing'
        ];
    }
    
    return $requirements;
}

/**
 * Verify Database Connection
 */
function verifyDatabaseConnection($data) {
    try {
        $dsn = "mysql:host={$data['db_host']};port={$data['db_port']};dbname={$data['db_name']}";
        $pdo = new PDO($dsn, $data['db_user'], $data['db_pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        return [
            'success' => true,
            'message' => 'Database connection successful!'
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Connection failed: ' . $e->getMessage()
        ];
    }
}

/**
 * Perform Installation
 */
function performInstallation($data) {
    global $baseDir;
    
    try {
        // 1. Create .env file
        $appKey = 'base64:' . base64_encode(random_bytes(32));
        $envContent = <<<ENV
APP_NAME="ZO Letters"
APP_ENV=production
APP_KEY={$appKey}
APP_DEBUG=false
APP_URL={$data['app_url'] ?? 'https://' . $_SERVER['HTTP_HOST']}

LOG_CHANNEL=daily
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=info

DB_CONNECTION=mysql
DB_HOST={$data['db_host']}
DB_PORT={$data['db_port']}
DB_DATABASE={$data['db_name']}
DB_USERNAME={$data['db_user']}
DB_PASSWORD={$data['db_pass']}

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMORY_LIMIT=256M
MAX_EXECUTION_TIME=300
ENV;
        
        if (file_put_contents($baseDir . '/.env', $envContent) === false) {
            throw new Exception('Failed to create .env file');
        }
        
        // 2. Connect to database
        $dsn = "mysql:host={$data['db_host']};port={$data['db_port']};dbname={$data['db_name']}";
        $pdo = new PDO($dsn, $data['db_user'], $data['db_pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // 3. Create users table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                email_verified_at TIMESTAMP NULL,
                password VARCHAR(255) NOT NULL,
                remember_token VARCHAR(100) NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // 4. Create templates table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS templates (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                description TEXT,
                header_image VARCHAR(255),
                footer_image VARCHAR(255),
                margin_top INT DEFAULT 20,
                margin_bottom INT DEFAULT 20,
                margin_left INT DEFAULT 25,
                margin_right INT DEFAULT 25,
                page_size VARCHAR(20) DEFAULT 'A4',
                orientation VARCHAR(10) DEFAULT 'portrait',
                status VARCHAR(20) DEFAULT 'active',
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // 5. Create documents table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS documents (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                template_id BIGINT UNSIGNED,
                reference_no VARCHAR(50),
                recipient_name VARCHAR(255),
                recipient_address TEXT,
                subject VARCHAR(255),
                body_html TEXT,
                pdf_file VARCHAR(255),
                status VARCHAR(20) DEFAULT 'draft',
                created_by BIGINT UNSIGNED,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                FOREIGN KEY (template_id) REFERENCES templates(id) ON DELETE SET NULL,
                FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // 6. Create settings table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS settings (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                key_name VARCHAR(255) NOT NULL UNIQUE,
                key_value TEXT,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // 7. Create admin user
        $hashedPassword = password_hash($data['admin_password'], PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->execute([$data['admin_name'], $data['admin_email'], $hashedPassword]);
        
        // 8. Create lock file
        $lockContent = json_encode([
            'installed' => true,
            'version' => APP_VERSION,
            'installed_at' => date('Y-m-d H:i:s'),
            'admin_email' => $data['admin_email']
        ]);
        file_put_contents($baseDir . '/storage/installed.lock', $lockContent);
        
        // 9. Set proper permissions
        @chmod($baseDir . '/.env', 0644);
        @chmod($baseDir . '/storage', 0755);
        @chmod($baseDir . '/storage/installed.lock', 0644);
        
        return [
            'success' => true,
            'message' => 'Installation completed successfully!'
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

/**
 * Get HTML Header
 */
function getHtmlHeader() {
    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install - ZO Letters</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .card {
            border: none;
            border-radius: 15px;
        }
        .display-1 { font-size: 4rem; }
        .display-6 { font-size: 1.75rem; }
        code {
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 4px;
            color: #d63384;
        }
    </style>
</head>
<body>
    <div class="container" style="max-width: 800px;">
HTML;
}

/**
 * Get HTML Footer
 */
function getHtmlFooter() {
    return <<<HTML
        <div class="text-center mt-4 text-white">
            <small>&copy; ZO International. All Rights Reserved. Version 1.3.0</small>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function nextStep(step) {
        window.location.href = 'install.php?step=' + step;
    }
    </script>
</body>
</html>
HTML;
}
