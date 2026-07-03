<?php

namespace App\Http\Controllers;

use App\Services\InstallService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Artisan;

class InstallController extends Controller
{
    protected $installService;

    public function __construct(InstallService $installService)
    {
        $this->installService = $installService;
    }

    /**
     * Show installation index
     */
    public function index()
    {
        // If already installed, redirect to home
        if ($this->installService->isInstalled()) {
            return redirect('/')->with('success', 'Application is already installed.');
        }

        return redirect()->route('install.welcome');
    }

    /**
     * Step 1: Welcome
     */
    public function welcome()
    {
        if ($this->installService->isInstalled()) {
            return redirect('/');
        }

        return view('install.welcome');
    }

    /**
     * Step 2: System Requirements
     */
    public function requirements()
    {
        if ($this->installService->isInstalled()) {
            return redirect('/');
        }

        $requirements = $this->installService->checkRequirements();
        $allPassed = $this->checkRequirementsPassed($requirements);

        return view('install.requirements', compact('requirements', 'allPassed'));
    }

    /**
     * Step 3: Folder Permissions
     */
    public function permissions()
    {
        if ($this->installService->isInstalled()) {
            return redirect('/');
        }

        $permissions = $this->installService->checkPermissions();
        $allPassed = $this->checkPermissionsPassed($permissions);

        return view('install.permissions', compact('permissions', 'allPassed'));
    }

    /**
     * Step 4: Database Configuration
     */
    public function database()
    {
        if ($this->installService->isInstalled()) {
            return redirect('/');
        }

        return view('install.database');
    }

    /**
     * Step 5: Administrator Details
     */
    public function administrator(Request $request)
    {
        if ($this->installService->isInstalled()) {
            return redirect('/');
        }

        // Validate database session data
        $request->session()->put('db_host', $request->input('db_host', 'localhost'));
        $request->session()->put('db_port', $request->input('db_port', 3306));
        $request->session()->put('db_name', $request->input('db_name'));
        $request->session()->put('db_user', $request->input('db_user'));
        $request->session()->put('db_pass', $request->input('db_pass'));

        return view('install.administrator');
    }

    /**
     * Verify database connection
     */
    public function verifyDatabase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'db_host' => 'required|string',
            'db_port' => 'required|numeric',
            'db_name' => 'required|string',
            'db_user' => 'required|string',
            'db_pass' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $result = $this->installService->verifyDatabase($request->only([
            'db_host', 'db_port', 'db_name', 'db_user', 'db_pass'
        ]));

        return response()->json($result);
    }

    /**
     * Process installation
     */
    public function install(Request $request)
    {
        if ($this->installService->isInstalled()) {
            return redirect('/');
        }

        // Validate
        $validator = Validator::make($request->all(), [
            'db_host' => 'required|string',
            'db_port' => 'required|numeric',
            'db_name' => 'required|string',
            'db_user' => 'required|string',
            'db_pass' => 'nullable|string',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255',
            'admin_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Prepare data
        $data = [
            'db_host' => $request->input('db_host'),
            'db_port' => $request->input('db_port'),
            'db_name' => $request->input('db_name'),
            'db_user' => $request->input('db_user'),
            'db_pass' => $request->input('db_pass'),
            'admin_name' => $request->input('admin_name'),
            'admin_email' => $request->input('admin_email'),
            'admin_password' => $request->input('admin_password'),
            'app_url' => url('/'),
        ];

        // Run installation
        $result = $this->installService->install($data);

        if ($result['success']) {
            // Clear session
            $request->session()->forget(['db_host', 'db_port', 'db_name', 'db_user', 'db_pass']);
            $request->session()->put('install_completed', true);
            $request->session()->put('admin_email', $result['admin_email']);

            return redirect()->route('install.complete');
        }

        return redirect()->back()
            ->withErrors(['install' => $result['message']])
            ->withInput();
    }

    /**
     * Installation Complete
     */
    public function complete(Request $request)
    {
        if (!$this->installService->isInstalled()) {
            return redirect()->route('install.welcome');
        }

        $adminEmail = session('admin_email', 'admin@example.com');
        
        // Clear install session
        $request->session()->forget(['install_completed', 'admin_email']);

        return view('install.complete', compact('adminEmail'));
    }

    /**
     * Check if all requirements passed
     */
    protected function checkRequirementsPassed(array $requirements): bool
    {
        // Check PHP version
        if (isset($requirements['php_version']) && !$requirements['php_version']['status']) {
            return false;
        }

        // Check all extensions
        if (isset($requirements['extensions'])) {
            foreach ($requirements['extensions'] as $ext) {
                if (!$ext['status']) {
                    return false;
                }
            }
        }

        // Check all functions
        if (isset($requirements['functions'])) {
            foreach ($requirements['functions'] as $func) {
                if (!$func['status']) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check if all permissions passed
     */
    protected function checkPermissionsPassed(array $permissions): bool
    {
        foreach ($permissions as $perm) {
            if (!$perm['status']) {
                return false;
            }
        }

        return true;
    }
}
