@extends('layouts.app')

@section('title', 'Document Details')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4 mb-4 border-bottom">
    <h1 class="h2">Document Details</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        @if($document->template)
            <a href="{{ route('documents.pdf.preview', $document) }}" target="_blank" class="btn btn-info me-2">
                <i class="bi bi-file-pdf me-1"></i> Preview PDF
            </a>
            <a href="{{ route('documents.pdf.download', $document) }}" class="btn btn-success me-2">
                <i class="bi bi-download me-1"></i> Download PDF
            </a>
            <button type="button" class="btn btn-warning me-2" id="regeneratePdfBtn" data-document-id="{{ $document->id }}">
                <i class="bi bi-arrow-clockwise me-1"></i> Regenerate
            </button>
        @endif
        <a href="{{ route('documents.edit', $document) }}" class="btn btn-primary me-2">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<!-- PDF Status Alert -->
@if(!$document->pdf_file && $document->template)
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>PDF Not Generated:</strong> The PDF for this document has not been generated yet.
        <button type="button" class="btn btn-sm btn-warning ms-2" id="generatePdfBtn" data-document-id="{{ $document->id }}">
            Generate PDF Now
        </button>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@elseif($document->pdf_file)
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        <strong>PDF Ready:</strong> The document PDF is ready for download.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Regenerate Alert -->
<div class="alert alert-info alert-dismissible fade show d-none" id="regenerateAlert" role="alert">
    <i class="bi bi-info-circle me-2"></i>
    <span id="regenerateMessage">Regenerating PDF...</span>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span class="badge bg-primary fs-6 me-2">{{ $document->reference_no }}</span>
                @if($document->pdf_file)
                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>PDF Ready</span>
                @else
                    <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>PDF Pending</span>
                @endif
            </div>
            <small class="text-muted">Created: {{ $document->created_at->format('F d, Y h:i A') }}</small>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-7">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th class="w-25">Template</th>
                            <td>{{ $document->template->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Recipient Name</th>
                            <td>{{ $document->recipient_name }}</td>
                        </tr>
                        <tr>
                            <th>Recipient Address</th>
                            <td><pre class="mb-0" style="white-space: pre-wrap;">{{ $document->recipient_address }}</pre></td>
                        </tr>
                        <tr>
                            <th>Subject</th>
                            <td>{{ $document->subject }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated</th>
                            <td>{{ $document->updated_at->format('F d, Y h:i A') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="col-md-5">
                <h6>Document Body Preview</h6>
                <div class="border rounded p-3 bg-light" style="max-height: 400px; overflow-y: auto;">
                    {!! $document->body_html !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const regenerateBtn = document.getElementById('regeneratePdfBtn');
    const generateBtn = document.getElementById('generatePdfBtn');
    const alert = document.getElementById('regenerateAlert');
    const message = document.getElementById('regenerateMessage');
    
    function showAlert(msg, type) {
        alert.className = 'alert alert-' + type + ' alert-dismissible fade show';
        message.textContent = msg;
        alert.classList.remove('d-none');
    }
    
    function regeneratePdf() {
        const docId = regenerateBtn.dataset.documentId;
        showAlert('Regenerating PDF...', 'info');
        regenerateBtn.disabled = true;
        
        fetch('/documents/' + docId + '/pdf/regenerate', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('PDF regenerated successfully!', 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showAlert(data.message || 'Failed to regenerate PDF.', 'danger');
                regenerateBtn.disabled = false;
            }
        })
        .catch(error => {
            showAlert('An error occurred. Please try again.', 'danger');
            regenerateBtn.disabled = false;
        });
    }
    
    function generatePdf() {
        const docId = generateBtn.dataset.documentId;
        showAlert('Generating PDF...', 'info');
        generateBtn.disabled = true;
        
        fetch('/documents/' + docId + '/pdf/regenerate', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('PDF generated successfully!', 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showAlert(data.message || 'Failed to generate PDF.', 'danger');
                generateBtn.disabled = false;
            }
        })
        .catch(error => {
            showAlert('An error occurred. Please try again.', 'danger');
            generateBtn.disabled = false;
        });
    }
    
    if (regenerateBtn) {
        regenerateBtn.addEventListener('click', regeneratePdf);
    }
    
    if (generateBtn) {
        generateBtn.addEventListener('click', generatePdf);
    }
});
</script>
@endpush
