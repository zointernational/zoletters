<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentFormRequest;
use App\Models\Document;
use App\Models\Template;
use App\Services\ReferenceNumberService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function __construct(
        private ReferenceNumberService $referenceService
    ) {}

    public function index(Request $request): View
    {
        $documents = Document::with('template')
            ->when($request->search, function ($query) use ($request) {
                $query->where('recipient_name', 'like', '%' . $request->search . '%')
                    ->orWhere('reference_no', 'like', '%' . $request->search . '%')
                    ->orWhere('subject', 'like', '%' . $request->search . '%');
            })
            ->when($request->template_id, function ($query) use ($request) {
                $query->where('template_id', $request->template_id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $templates = Template::active()->get();

        return view('zo-letters.documents.index', compact('documents', 'templates'));
    }

    public function create(): View
    {
        $templates = Template::active()->get();
        return view('zo-letters.documents.create', compact('templates'));
    }

    public function store(DocumentFormRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $referenceNo = $this->referenceService->generate();

            $data = $request->validated();
            $data['reference_no'] = $referenceNo;
            $data['created_by'] = auth()->id();

            Document::create($data);

            DB::commit();

            Log::channel('daily')->info('Document created', [
                'reference_no' => $referenceNo,
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document created successfully.',
                'reference_no' => $referenceNo,
                'redirect' => route('documents.index'),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->logError('Document creation failed', $e);
            Log::channel('daily')->error('Document creation failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create document. Please try again.',
            ], 500);
        }
    }

    public function show(Document $document): View
    {
        $document->load('template');
        return view('zo-letters.documents.show', compact('document'));
    }

    public function edit(Document $document): View
    {
        $templates = Template::active()->get();
        return view('zo-letters.documents.edit', compact('document', 'templates'));
    }

    public function update(DocumentFormRequest $request, Document $document): JsonResponse
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $document->update($data);

            DB::commit();

            Log::channel('daily')->info('Document updated', [
                'document_id' => $document->id,
                'reference_no' => $document->reference_no,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document updated successfully.',
                'redirect' => route('documents.index'),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->logError('Document update failed', $e);
            Log::channel('daily')->error('Document update failed', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update document. Please try again.',
            ], 500);
        }
    }

    public function destroy(Document $document): JsonResponse
    {
        try {
            DB::beginTransaction();

            $document->delete();

            DB::commit();

            Log::channel('daily')->info('Document deleted', [
                'document_id' => $document->id,
                'reference_no' => $document->reference_no,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->logError('Document deletion failed', $e);
            Log::channel('daily')->error('Document deletion failed', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete document. Please try again.',
            ], 500);
        }
    }
}
