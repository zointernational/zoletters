<?php

namespace App\Http\Controllers;

use App\Http\Requests\TemplateFormRequest;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TemplateController extends Controller
{
    public function index(Request $request): \Illuminate\View\View
    {
        $templates = Template::orderBy('created_at', 'desc')->paginate(10);
        return view('zo-letters.templates.index', compact('templates'));
    }

    public function create(): \Illuminate\View\View
    {
        return view('zo-letters.templates.create');
    }

    public function store(TemplateFormRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data = $this->processImages($request, $data);

            Template::create($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Template created successfully.',
                'redirect' => route('templates.index'),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->logError('Template creation failed', $e);
            Log::channel('daily')->error('Template creation failed', [
                'request' => $request->except(['header_image', 'footer_image']),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create template. Please try again.',
            ], 500);
        }
    }

    public function show(Template $template): \Illuminate\View\View
    {
        return view('zo-letters.templates.show', compact('template'));
    }

    public function edit(Template $template): \Illuminate\View\View
    {
        return view('zo-letters.templates.edit', compact('template'));
    }

    public function update(TemplateFormRequest $request, Template $template): JsonResponse
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data = $this->processImages($request, $data, $template);

            $template->update($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Template updated successfully.',
                'redirect' => route('templates.index'),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->logError('Template update failed', $e);
            Log::channel('daily')->error('Template update failed', [
                'template_id' => $template->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update template. Please try again.',
            ], 500);
        }
    }

    public function destroy(Template $template): JsonResponse
    {
        try {
            DB::beginTransaction();

            if ($template->header_image) {
                Storage::disk('templates')->delete($template->header_image);
            }
            if ($template->footer_image) {
                Storage::disk('templates')->delete($template->footer_image);
            }

            $template->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Template deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->logError('Template deletion failed', $e);
            Log::channel('daily')->error('Template deletion failed', [
                'template_id' => $template->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete template. Please try again.',
            ], 500);
        }
    }

    private function processImages(TemplateFormRequest $request, array $data, ?Template $template = null): array
    {
        $disk = Storage::disk('templates');

        if ($request->hasFile('header_image')) {
            if ($template && $template->header_image) {
                $disk->delete($template->header_image);
            }
            $file = $request->file('header_image');
            $filename = 'header_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $disk->put($filename, file_get_contents($file->getRealPath()));
            $data['header_image'] = $filename;
        }

        if ($request->hasFile('footer_image')) {
            if ($template && $template->footer_image) {
                $disk->delete($template->footer_image);
            }
            $file = $request->file('footer_image');
            $filename = 'footer_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $disk->put($filename, file_get_contents($file->getRealPath()));
            $data['footer_image'] = $filename;
        }

        return $data;
    }
}
