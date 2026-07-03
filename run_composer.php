<?php
/**
 * ZO Letters - Installation Setup
 * 
 * This script guides you through the setup process.
 * DELETE THIS FILE AFTER COMPLETION!
 */

$baseDir = dirname(__DIR__);
$step = $_GET['step'] ?? 'menu';

// Helper function to check directories
function checkDirectory($path, $name) {
    $exists = is_dir($baseDir . '/' . $path);
    $writable = $exists && is_writable($baseDir . '/' . $path);
    return [
        'name' => $name,
        'path' => $path,
        'exists' => $exists,
        'writable' => $writable,
        'ok' => $exists && $writable
    ];
}

$baseDir = $baseDir; // Make it available in function
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZO Letters - Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 20px; }
        .card { border: none; border-radius: 1rem; box-shadow: 0 25px 50px rgba(0,0,0,0.25); }
        .step-icon { width: 60px; height: 60px; font-size: 24px; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="container" style="max-width: 800px;">
        <div class="card">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <div class="display-4 text-primary mb-2">📄</div>
                    <h1 class="h3">ZO Letters Setup</h1>
                    <p class="text-muted">Version 1.4.0</p>
                </div>
                
                <?php if ($step === 'menu'): ?>
                <!-- Menu -->
                <h5 class="mb-3">Setup Steps</h5>
                <div class="list-group">
                    <a href="?step=check" class="list-group-item list-group-item-action">
                        <i class="bi bi-clipboard-check me-2"></i>Step 1: Check Requirements
                    </a>
                    <a href="?step=composer" class="list-group-item list-group-item-action">
                        <i class="bi bi-download me-2"></i>Step 2: Install Dependencies
                    </a>
                    <a href="?step=permissions" class="list-group-item list-group-item-action">
                        <i class="bi bi-folder-check me-2"></i>Step 3: Set Permissions
                    </a>
                    <a href="?step=clean" class="list-group-item list-group-item-action">
                        <i class="bi bi-trash me-2"></i>Step 4: Cleanup
                    </a>
                </div>
                
                <hr class="my-4">
                
                <div class="alert alert-info">
                    <h5><i class="bi bi-info-circle me-2"></i>Quick Start</h5>
                    <ol class="mb-0">
                        <li>Run each step in order</li>
                        <li>Copy and paste commands when shown</li>
                        <li>Run these via SSH or Hosting Panel Terminal</li>
                    </ol>
                </div>
                
                <?php elseif ($step === 'check'): ?>
                <!-- Check Requirements -->
                <h5 class="mb-3"><i class="bi bi-clipboard-check me-2"></i>System Check</h5>
                
                <?php
                $checks = [
                    checkDirectory('storage', 'Storage Directory'),
                    checkDirectory('storage/app', 'Storage App'),
                    checkDirectory('storage/framework', 'Storage Framework'),
                    checkDirectory('storage/logs', 'Storage Logs'),
                    checkDirectory('bootstrap/cache', 'Bootstrap Cache'),
                    checkDirectory('public/uploads', 'Public Uploads'),
                ];
                ?>
                
                <table class="table">
                    <thead><tr><th>Directory</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php foreach ($checks as $check): ?>
                        <tr>
                            <td><code><?= $check['path'] ?></code></td>
                            <td>
                                <?php if ($check['ok']): ?>
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> OK</span>
                                <?php elseif (!$check['exists']): ?>
                                    <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Missing</span>
                                <?php else: ?>
                                    <span class="badge bg-warning"><i class="bi bi-exclamation"></i> Not Writable</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="mt-3">
                    <a href="?step=menu" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
                    <a href="?step=composer" class="btn btn-primary">Next <i class="bi bi-arrow-right"></i></a>
                </div>
                
                <?php elseif ($step === 'composer'): ?>
                <!-- Composer Install -->
                <h5 class="mb-3"><i class="bi bi-download me-2"></i>Install Dependencies</h5>
                
                <div class="alert alert-warning">
                    <strong>Manual Action Required!</strong><br>
                    Copy and paste the command below into your hosting panel's Terminal or SSH:
                </div>
                
                <pre class="bg-dark text-light">cd <?= $baseDir ?> && composer install --no-dev --optimize-autoloader</pre>
                
                <div class="alert alert-info">
                    <strong>How to run:</strong>
                    <ol class="mb-0">
                        <li>Login to your hosting panel (DirectAdmin/cPanel)</li>
                        <li>Go to <strong>Terminal</strong> or use SSH</li>
                        <li>Paste the command above</li>
                        <li>Press Enter to execute</li>
                    </ol>
                </div>
                
                <div class="mt-3">
                    <a href="?step=menu" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
                    <a href="?step=permissions" class="btn btn-primary">Next <i class="bi bi-arrow-right"></i></a>
                </div>
                
                <?php elseif ($step === 'permissions'): ?>
                <!-- Permissions -->
                <h5 class="mb-3"><i class="bi bi-folder-check me-2"></i>Set Permissions</h5>
                
                <div class="alert alert-warning">
                    <strong>Manual Action Required!</strong><br>
                    Run this command after composer install completes:
                </div>
                
                <pre class="bg-dark text-light">chmod -R 755 <?= $baseDir ?>/storage <?= $baseDir ?>/bootstrap/cache <?= $baseDir ?>/public/uploads</pre>
                
                <div class="mt-3">
                    <a href="?step=menu" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
                    <a href="?step=clean" class="btn btn-primary">Next <i class="bi bi-arrow-right"></i></a>
                </div>
                
                <?php elseif ($step === 'clean'): ?>
                <!-- Cleanup -->
                <h5 class="mb-3"><i class="bi bi-trash me-2"></i>Cleanup</h5>
                
                <div class="alert alert-success">
                    <h5><i class="bi bi-check-circle me-2"></i>Setup Complete!</h5>
                    <p>Now delete this file from your server:</p>
                    <pre class="bg-dark text-light mb-0">rm <?= $baseDir ?>/public/run_composer.php</pre>
                </div>
                
                <div class="alert alert-info">
                    <strong>Next Step:</strong>
                    Visit <a href="../install" class="alert-link">/install</a> to complete the Laravel installation wizard.
                </div>
                
                <div class="mt-3">
                    <a href="?step=menu" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
                    <a href="../install" class="btn btn-success"><i class="bi bi-rocket-takeoff"></i> Go to Installer</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="text-center mt-4 text-white">
            <small>&copy; <?= date('Y') ?> ZO International. All Rights Reserved.</small>
        </div>
    </div>
</body>
</html>
