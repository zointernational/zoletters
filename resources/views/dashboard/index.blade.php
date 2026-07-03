@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4 mb-4 border-bottom">
    <h1 class="h2">Dashboard</h1>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card card-stat bg-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-white text-primary me-3">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-white-50 mb-0">Total Templates</h6>
                        <h2 class="mt-0 mb-0">{{ $stats['total_templates'] }}</h2>
                    </div>
                </div>
                <a href="{{ route('templates.index') }}" class="stretched-link"></a>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-xl-3">
        <div class="card card-stat bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-white text-success me-3">
                        <i class="bi bi-file-earmark"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-white-50 mb-0">Total Documents</h6>
                        <h2 class="mt-0 mb-0">{{ $stats['total_documents'] }}</h2>
                    </div>
                </div>
                <a href="{{ route('documents.index') }}" class="stretched-link"></a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Documents -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Documents</h5>
        <a href="{{ route('documents.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="card-body">
        @if($recentDocuments->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <p class="text-muted mt-3">No documents created yet.</p>
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
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentDocuments as $document)
                        <tr>
                            <td>
                                <span class="badge bg-secondary">{{ $document->reference_no }}</span>
                            </td>
                            <td>{{ $document->template->name ?? 'N/A' }}</td>
                            <td>{{ Str::limit($document->recipient_name, 30) }}</td>
                            <td>{{ Str::limit($document->subject, 40) }}</td>
                            <td>{{ $document->created_at->format('M d, Y') }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('documents.show', $document) }}" class="btn btn-outline-secondary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('documents.edit', $document) }}" class="btn btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
