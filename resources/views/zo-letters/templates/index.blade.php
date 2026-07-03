@extends('layouts.app')

@section('title', 'Templates')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4 mb-4 border-bottom">
    <h1 class="h2">Templates</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('templates.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Create Template
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-body">
        @if($templates->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-file-earmark-text display-1 text-muted"></i>
                <p class="text-muted mt-3">No templates created yet.</p>
                <a href="{{ route('templates.create') }}" class="btn btn-primary">Create First Template</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Page Size</th>
                            <th>Orientation</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($templates as $template)
                        <tr>
                            <td>
                                <strong>{{ $template->name }}</strong>
                            </td>
                            <td>{{ Str::limit($template->description, 50) ?? '-' }}</td>
                            <td>{{ $template->page_size }}</td>
                            <td>{{ ucfirst($template->orientation) }}</td>
                            <td>
                                <span class="badge bg-{{ $template->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($template->status) }}
                                </span>
                            </td>
                            <td>{{ $template->created_at->format('M d, Y') }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('templates.show', $template) }}" class="btn btn-outline-secondary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('templates.edit', $template) }}" class="btn btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-delete" 
                                            data-id="{{ $template->id }}" 
                                            data-name="{{ $template->name }}"
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
                {{ $templates->links() }}
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
                <p>Are you sure you want to delete template "<strong id="deleteTemplateName"></strong>"?</p>
                <p class="text-danger small">This action cannot be undone. All associated documents may be affected.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Template</button>
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
            const name = this.dataset.name;
            document.getElementById('deleteTemplateName').textContent = name;
            document.getElementById('deleteForm').action = '/templates/' + id;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
});
</script>
@endpush
