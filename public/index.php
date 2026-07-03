<?php
/**
 * ZO Letters - Entry Point
 * Handles both installation wizard and normal Laravel operation
 */

define('LARAVEL_START', microtime(true));

$baseDir = dirname(__DIR__);

// Check if installation is complete
$envFile = $baseDir . '/.env';
$installerLock = $baseDir . '/storage/installed.lock';
$isInstalled = file_exists($installerLock) && filesize($installerLock) > 0;

// If not installed, show installer
if (!$isInstalled && basename($_SERVER['REQUEST_URI']) !== 'install.php') {
    // Redirect to installer if accessing root
    if ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '/index.php') {
        header('Location: /install.php');
        exit;
    }
}

// Check for maintenance mode
if (file_exists($maintenance = $baseDir . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Check if vendor exists (Laravel is ready)
if (!file_exists($baseDir . '/vendor/autoload.php')) {
    // Show setup required page
    http_response_code(503);
    echo '<!DOCTYPE html><html><head><title>Setup Required - ZO Letters</title>';
    echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">';
    echo '<style>body{padding:20px;background:#f5f5f5}.container{max-width:600px;margin-top:100px;}</style>';
    echo '</head><body><div class="container"><div class="card shadow"><div class="card-body text-center">';
    echo '<h2 class="text-warning">⚠️ Setup Required</h2>';
    echo '<p>The application requires initial setup.</p>';
    echo '<a href="/install.php" class="btn btn-primary btn-lg">Start Installation</a>';
    echo '</div></div></div></body></html>';
    exit;
}

// Register the Composer autoloader
require $baseDir . '/vendor/autoload.php';

// Bootstrap Laravel and handle the request
(require_once $baseDir . '/bootstrap/app.php')
    ->handleRequest(Illuminate\Http\Request::capture());
