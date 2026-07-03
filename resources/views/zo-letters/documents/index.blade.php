@extends('layouts.app')

@section('title', 'Documents')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4 mb-4 border-bottom">
    <h1 class="h2">Documents</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('documents.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Create Document
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('documents.index') }}" class="row g-3">
            <div class="col-md-4">
                <input type="text" class="form-control" name="search" 
                       placeholder="Search by name, reference, or subject..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="template_id">
                    <option value="">All Templates</option>
                    @foreach($templates as $template)
                        <option value="{{ $template->id }}" {{ request('template_id') == $template->id ? 'selected' : '' }}>
                            {{ $template->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-search me-1"></i> Filter
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-x-circle me-1"></i> Clear
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($documents->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-file-earmark display-1 text-muted"></i>
                <p class="text-muted mt-3">No documents found.</p>
                <a href="{{ route('documents.create') }}" class="btn btn-primary">Create First Document</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Reference No</th>
                            <th>Template</th>
                            <th>Recipient</th>
                            <th>Subject</th>
                            <th>PDF</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documents as $document)
                        <tr>
                            <td>
                                <span class="badge bg-secondary">{{ $document->reference_no }}</span>
                            </td>
                            <td>{{ $document->template->name ?? 'N/A' }}</td>
                            <td>{{ Str::limit($document->recipient_name, 30) }}</td>
                            <td>{{ Str::limit($document->subject, 40) }}</td>
                            <td>
                                @if($document->pdf_file)
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Ready</span>
                                @else
                                    <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Pending</span>
                                @endif
                            </td>
                            <td>{{ $document->created_at->format('M d, Y') }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('documents.show', $document) }}" class="btn btn-outline-secondary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($document->template)
                                        <a href="{{ route('documents.pdf.preview', $document) }}" target="_blank" class="btn btn-outline-info" title="Preview PDF">
                                            <i class="bi bi-file-pdf"></i>
                                        </a>
                                        <a href="{{ route('documents.pdf.download', $document) }}" class="btn btn-outline-success" title="Download PDF">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('documents.edit', $document) }}" class="btn btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-delete" 
                                            data-id="{{ $document->id }}" 
                                            data-ref="{{ $document->reference_no }}"
                                            title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $documents->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete document "<strong id="deleteDocRef"></strong>"?</p>
                <p class="text-danger small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Document</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const ref = this.dataset.ref;
            document.getElementById('deleteDocRef').textContent = ref;
            document.getElementById('deleteForm').action = '/documents/' + id;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
});
</script>
@endpush
