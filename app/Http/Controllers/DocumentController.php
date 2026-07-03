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

            $this->pdfService->delete($document);
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
}
