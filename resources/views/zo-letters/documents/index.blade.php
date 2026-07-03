@extends('layouts.app')

@section('title', 'Letters')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4 mb-4 border-bottom">
    <h1 class="h2">Letters</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('documents.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> New Letter
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('documents.index') }}" class="row g-3">
            <div class="col-md-3">
                <input type="text" class="form-control" name="search" 
                       placeholder="Search..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select class="form-select" name="status">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="final" {{ request('status') == 'final' ? 'selected' : '' }}>Final</option>
                    <option value="printed" {{ request('status') == 'printed' ? 'selected' : '' }}>Printed</option>
                </select>
            </div>
            <div class="col-md-2">
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
                <input type="date" class="form-control" name="from_date" 
                       placeholder="From Date" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="to_date" 
                       placeholder="To Date" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
        <div class="row mt-2">
            <div class="col-md-12">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="includeArchived" name="include_archived" value="1" {{ request('include_archived') ? 'checked' : '' }} onchange="this.form.submit()">
                    <label class="form-check-label" for="includeArchived">
                        Include Archived
                    </label>
                </div>
                <a href="{{ route('documents.index') }}" class="btn btn-link btn-sm">
                    <i class="bi bi-x-circle me-1"></i> Clear Filters
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($documents->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-file-earmark display-1 text-muted"></i>
                <p class="text-muted mt-3">No letters found.</p>
                <a href="{{ route('documents.create') }}" class="btn btn-primary">Create First Letter</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Reference No</th>
                            <th>Status</th>
                            <th>Recipient</th>
                            <th>Subject</th>
                            <th>Template</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documents as $document)
                        <tr class="{{ $document->trashed() ? 'table-secondary' : '' }}">
                            <td>
                                <span class="badge bg-secondary">{{ $document->reference_no }}</span>
                                @if($document->trashed())
                                    <span class="badge bg-danger">Archived</span>
                                @endif
                            </td>
                            <td>
                                @switch($document->status)
                                    @case('draft')
                                        <span class="badge bg-secondary">Draft</span>
                                        @break
                                    @case('final')
                                        <span class="badge bg-success">Final</span>
                                        @break
                                    @case('printed')
                                        <span class="badge bg-primary">Printed</span>
                                        @break
                                @endswitch
                            </td>
                            <td>{{ Str::limit($document->recipient_name, 30) }}</td>
                            <td>{{ Str::limit($document->subject, 40) }}</td>
                            <td>{{ $document->template->name ?? 'N/A' }}</td>
                            <td>{{ $document->created_at->format('M d, Y') }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    @if($document->trashed())
                                        <button type="button" class="btn btn-outline-success btn-restore" 
                                                data-id="{{ $document->id }}"
                                                title="Restore">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                    @else
                                        <a href="{{ route('documents.show', $document) }}" class="btn btn-outline-secondary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('documents.preview', $document) }}" class="btn btn-outline-info" title="Preview">
                                            <i class="bi bi-file-text"></i>
                                        </a>
                                        @if($document->template)
                                            <a href="{{ route('documents.pdf.download', $document) }}" class="btn btn-outline-success" title="Download PDF">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        @endif
                                        <button type="button" class="btn btn-outline-warning btn-status" 
                                                data-id="{{ $document->id }}"
                                                data-status="{{ $document->status }}"
                                                title="Change Status">
                                            <i class="bi bi-circle-fill"></i>
                                        </button>
                                        <a href="{{ route('documents.edit', $document) }}" class="btn btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-secondary btn-duplicate" 
                                                data-id="{{ $document->id }}"
                                                title="Duplicate">
                                            <i class="bi bi-copy"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-archive" 
                                                data-id="{{ $document->id }}"
                                                data-ref="{{ $document->reference_no }}"
                                                title="Archive">
                                            <i class="bi bi-archive"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $documents->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="statusForm">
                    @csrf
                    <div class="mb-3">
                        <label for="newStatus" class="form-label">Select Status</label>
                        <select class="form-select" id="newStatus" name="status">
                            <option value="draft">Draft</option>
                            <option value="final">Final</option>
                            <option value="printed">Printed</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveStatus">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Archive Modal -->
<div class="modal fade" id="archiveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Archive Letter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to archive letter "<strong id="archiveDocRef"></strong>"?</p>
                <p class="text-muted small">You can restore it later from the archived letters.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="confirmArchive">Archive</button>
            </div>
        </div>
    </div>
</div>

<!-- Duplicate Modal -->
<div class="modal fade" id="duplicateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Duplicate Letter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Create a duplicate of this letter with a new reference number?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmDuplicate">Duplicate</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentDocId = null;
    const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
    const archiveModal = new bootstrap.Modal(document.getElementById('archiveModal'));
    const duplicateModal = new bootstrap.Modal(document.getElementById('duplicateModal'));

    // Status change
    document.querySelectorAll('.btn-status').forEach(btn => {
        btn.addEventListener('click', function() {
            currentDocId = this.dataset.id;
            document.getElementById('newStatus').value = this.dataset.status;
            statusModal.show();
        });
    });

    document.getElementById('saveStatus').addEventListener('click', function() {
        fetch('/documents/' + currentDocId + '/status', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status: document.getElementById('newStatus').value })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Failed to update status');
            }
        });
    });

    // Archive
    document.querySelectorAll('.btn-archive').forEach(btn => {
        btn.addEventListener('click', function() {
            currentDocId = this.dataset.id;
            document.getElementById('archiveDocRef').textContent = this.dataset.ref;
            archiveModal.show();
        });
    });

    document.getElementById('confirmArchive').addEventListener('click', function() {
        fetch('/documents/' + currentDocId + '/archive', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Failed to archive');
            }
        });
    });

    // Restore
    document.querySelectorAll('.btn-restore').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            fetch('/documents/' + id + '/restore', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Failed to restore');
                }
            });
        });
    });

    // Duplicate
    document.querySelectorAll('.btn-duplicate').forEach(btn => {
        btn.addEventListener('click', function() {
            currentDocId = this.dataset.id;
            duplicateModal.show();
        });
    });

    document.getElementById('confirmDuplicate').addEventListener('click', function() {
        fetch('/documents/' + currentDocId + '/duplicate', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                alert(data.message || 'Failed to duplicate');
                duplicateModal.hide();
            }
        });
    });
});
</script>
@endpush
