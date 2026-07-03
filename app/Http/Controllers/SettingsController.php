<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct()
    {
        Setting::bootDefaultSettings();
    }

    public function index(): View
    {
        $settings = Setting::orderBy('group')->orderBy('sort_order')->get();
        $groupedSettings = $settings->groupBy('group');

        return view('zo-letters.settings.index', compact('groupedSettings'));
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'settings' => 'required|array',
                'settings.*' => 'nullable|string',
            ]);

            foreach ($validated['settings'] as $key => $value) {
                Setting::set($key, $value);
            }

            Log::channel('daily')->info('Settings updated', [
                'updated_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Settings saved successfully.',
            ]);
        } catch (\Throwable $e) {
            $this->logError('Settings save failed', $e);
            Log::channel('daily')->error('Settings save failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save settings. Please try again.',
            ], 500);
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}
