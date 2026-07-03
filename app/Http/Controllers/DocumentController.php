<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentFormRequest;
use App\Models\Document;
use App\Models\Template;
use App\Services\PdfService;
use App\Services\ReferenceNumberService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentController extends Controller
{
    public function __construct(
        private ReferenceNumberService $referenceService,
        private PdfService $pdfService
    ) {}

    public function index(Request $request): View
    {
        $query = Document::with('template');

        if ($request->boolean('include_archived')) {
            $query->withTrashed();
        }

        $query->when($request->search, function ($q) use ($request) {
            $q->where(function ($subQ) use ($request) {
                $subQ->where('recipient_name', 'like', '%' . $request->search . '%')
                    ->orWhere('reference_no', 'like', '%' . $request->search . '%')
                    ->orWhere('subject', 'like', '%' . $request->search . '%');
            });
        });

        $query->when($request->template_id, function ($q) use ($request) {
            $q->where('template_id', $request->template_id);
        });

        $query->when($request->status, function ($q) use ($request) {
            $q->where('status', $request->status);
        });

        $query->when($request->from_date, function ($q) use ($request) {
            $q->whereDate('created_at', '>=', $request->from_date);
        });

        $query->when($request->to_date, function ($q) use ($request) {
            $q->whereDate('created_at', '<=', $request->to_date);
        });

        $query->when($request->boolean('include_archived'), function ($q) {
            $q->withTrashed();
        }, function ($q) {
            $q->whereNull('deleted_at');
        });

        $documents = $query->orderBy('created_at', 'desc')->paginate(10);
        $templates = Template::active()->get();

        return view('zo-letters.documents.index', compact('documents', 'templates'));
    }

    public function create(): View
    {
        $templates = Template::active()->get();
        $referenceNo = $this->referenceService->generate();

        return view('zo-letters.documents.create', compact('templates', 'referenceNo'));
    }

    public function store(DocumentFormRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $referenceNo = $this->referenceService->generate();

            $data = $request->validated();
            $data['reference_no'] = $referenceNo;
            $data['status'] = Document::STATUS_DRAFT;
            $data['created_by'] = auth()->id();

            $document = Document::create($data);

            $pdfFilename = $this->pdfService->generate($document);
            if ($pdfFilename) {
                $document->update(['pdf_file' => $pdfFilename]);
            }

            DB::commit();

            Log::channel('daily')->info('Document created', [
                'document_id' => $document->id,
                'reference_no' => $referenceNo,
                'created_by' => auth()->id(),
                'pdf_generated' => (bool) $pdfFilename,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Letter created successfully.',
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

    public function preview(Document $document): View
    {
        $document->load('template');
        return view('zo-letters.documents.preview', compact('document'));
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

            $this->pdfService->delete($document);
            $pdfFilename = $this->pdfService->generate($document);
            if ($pdfFilename) {
                $document->update(['pdf_file' => $pdfFilename]);
            }

            DB::commit();

            Log::channel('daily')->info('Document updated', [
                'document_id' => $document->id,
                'reference_no' => $document->reference_no,
                'pdf_regenerated' => (bool) $pdfFilename,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Letter updated successfully.',
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

            $this->pdfService->delete($document);
            $document->delete();

            DB::commit();

            Log::channel('daily')->info('Document deleted', [
                'document_id' => $document->id,
                'reference_no' => $document->reference_no,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Letter deleted successfully.',
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

    public function duplicate(Document $document): JsonResponse
    {
        try {
            DB::beginTransaction();

            $referenceNo = $this->referenceService->generate();

            $newDocument = $document->replicate();
            $newDocument->reference_no = $referenceNo;
            $newDocument->status = Document::STATUS_DRAFT;
            $newDocument->pdf_file = null;
            $newDocument->created_by = auth()->id();
            $newDocument->created_at = now();
            $newDocument->updated_at = now();
            $newDocument->save();

            DB::commit();

            Log::channel('daily')->info('Document duplicated', [
                'original_id' => $document->id,
                'original_ref' => $document->reference_no,
                'new_id' => $newDocument->id,
                'new_ref' => $referenceNo,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Letter duplicated successfully.',
                'reference_no' => $referenceNo,
                'redirect' => route('documents.edit', $newDocument),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->logError('Document duplication failed', $e);
            Log::channel('daily')->error('Document duplication failed', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to duplicate letter. Please try again.',
            ], 500);
        }
    }

    public function archive(Document $document): JsonResponse
    {
        try {
            $document->delete();

            Log::channel('daily')->info('Document archived', [
                'document_id' => $document->id,
                'reference_no' => $document->reference_no,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Letter archived successfully.',
            ]);
        } catch (\Throwable $e) {
            $this->logError('Document archive failed', $e);
            Log::channel('daily')->error('Document archive failed', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to archive letter. Please try again.',
            ], 500);
        }
    }

    public function restore(Request $request, int $id): JsonResponse
    {
        try {
            $document = Document::withTrashed()->findOrFail($id);
            $document->restore();

            Log::channel('daily')->info('Document restored', [
                'document_id' => $document->id,
                'reference_no' => $document->reference_no,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Letter restored successfully.',
            ]);
        } catch (\Throwable $e) {
            $this->logError('Document restore failed', $e);
            Log::channel('daily')->error('Document restore failed', [
                'document_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to restore letter. Please try again.',
            ], 500);
        }
    }

    public function updateStatus(Request $request, Document $document): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:' . implode(',', Document::STATUSES),
            ]);

            $document->update(['status' => $validated['status']]);

            Log::channel('daily')->info('Document status changed', [
                'document_id' => $document->id,
                'reference_no' => $document->reference_no,
                'new_status' => $validated['status'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.',
            ]);
        } catch (\Throwable $e) {
            $this->logError('Status update failed', $e);
            Log::channel('daily')->error('Status update failed', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update status. Please try again.',
            ], 500);
        }
    }

    public function previewPdf(Document $document): BinaryFileResponse|JsonResponse
    {
        try {
            $document->load('template');

            if (!$document->template) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document has no template assigned. Cannot generate PDF preview.',
                ], 400);
            }

            $pdfContent = $this->pdfService->preview($document);

            if (!$pdfContent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate PDF preview. Please check the document content.',
                ], 500);
            }

            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="preview_' . $document->reference_no . '.pdf"',
                'Content-Length' => strlen($pdfContent),
            ]);
        } catch (\Throwable $e) {
            $this->logError('PDF preview failed', $e);
            Log::channel('daily')->error('PDF preview failed', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate PDF preview. Please try again.',
            ], 500);
        }
    }

    public function downloadPdf(Document $document): BinaryFileResponse|JsonResponse
    {
        try {
            $document->load('template');

            if (!$document->template) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document has no template assigned. Cannot download PDF.',
                ], 400);
            }

            if (!$document->pdf_file) {
                $pdfFilename = $this->pdfService->generate($document);
                if ($pdfFilename) {
                    $document->update(['pdf_file' => $pdfFilename]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to generate PDF. Please try regenerating.',
                    ], 500);
                }
            }

            $filepath = $this->pdfService->getFilePath($document);

            if (!$filepath) {
                $pdfFilename = $this->pdfService->generate($document, true);
                if ($pdfFilename) {
                    $document->update(['pdf_file' => $pdfFilename]);
                    $filepath = $this->pdfService->getFilePath($document);
                }

                if (!$filepath) {
                    return response()->json([
                        'success' => false,
                        'message' => 'PDF file not found. Please try regenerating.',
                    ], 404);
                }
            }

            Log::channel('daily')->info('PDF downloaded', [
                'document_id' => $document->id,
                'reference_no' => $document->reference_no,
            ]);

            return response()->download($filepath, $document->reference_no . '.pdf', [
                'Content-Type' => 'application/pdf',
            ]);
        } catch (\Throwable $e) {
            $this->logError('PDF download failed', $e);
            Log::channel('daily')->error('PDF download failed', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to download PDF. Please try again.',
            ], 500);
        }
    }

    public function regeneratePdf(Document $document): JsonResponse
    {
        try {
            $document->load('template');

            if (!$document->template) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document has no template assigned. Cannot regenerate PDF.',
                ], 400);
            }

            $this->pdfService->delete($document);
            $pdfFilename = $this->pdfService->generate($document, true);

            if ($pdfFilename) {
                $document->update(['pdf_file' => $pdfFilename]);

                Log::channel('daily')->info('PDF regenerated', [
                    'document_id' => $document->id,
                    'reference_no' => $document->reference_no,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'PDF regenerated successfully.',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to regenerate PDF. Please check the document content.',
            ], 500);
        } catch (\Throwable $e) {
            $this->logError('PDF regeneration failed', $e);
            Log::channel('daily')->error('PDF regeneration failed', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to regenerate PDF. Please try again.',
            ], 500);
        }
    }

    public function print(Document $document): View
    {
        $document->load('template');
        $document->markAsPrinted();

        Log::channel('daily')->info('Document printed', [
            'document_id' => $document->id,
            'reference_no' => $document->reference_no,
        ]);

        return view('zo-letters.documents.print', compact('document'));
    }
}
