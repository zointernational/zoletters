<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckInstallation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if .env file exists
        $envPath = base_path('.env');
        
        if (!file_exists($envPath)) {
            // No .env file - redirect to installer
            return redirect()->route('install.index');
        }

        // Check if database is configured
        $dbHost = env('DB_HOST');
        $dbDatabase = env('DB_DATABASE');
        
        if (empty($dbHost) || empty($dbDatabase)) {
            // Database not configured - redirect to installer
            return redirect()->route('install.index');
        }

        // Check if users table has any records
        try {
            \DB::connection()->getPdo();
            $userCount = \DB::table('users')->count();
            
            if ($userCount === 0) {
                // No users - redirect to installer
                return redirect()->route('install.index');
            }
        } catch (\Exception $e) {
            // Database connection failed - redirect to installer
            return redirect()->route('install.index');
        }

        return $next($request);
    }
}
