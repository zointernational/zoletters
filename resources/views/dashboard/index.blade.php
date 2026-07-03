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
                        <h6 class="card-title text-white-50 mb-0">Total Letters</h6>
                        <h2 class="mt-0 mb-0">{{ $stats['total_documents'] }}</h2>
                    </div>
                </div>
                <a href="{{ route('documents.index') }}" class="stretched-link"></a>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-xl-3">
        <div class="card card-stat bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-dark text-warning me-3">
                        <i class="bi bi-pencil"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-dark-50 mb-0">Drafts</h6>
                        <h2 class="mt-0 mb-0">{{ $stats['draft_count'] }}</h2>
                    </div>
                </div>
                <a href="{{ route('documents.index', ['status' => 'draft']) }}" class="stretched-link"></a>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-xl-3">
        <div class="card card-stat bg-info text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-white text-info me-3">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-white-50 mb-0">Finalized</h6>
                        <h2 class="mt-0 mb-0">{{ $stats['final_count'] }}</h2>
                    </div>
                </div>
                <a href="{{ route('documents.index', ['status' => 'final']) }}" class="stretched-link"></a>
            </div>
        </div>
    </div>
</div>

<!-- Letters Lists -->
<div class="row g-4 mb-4">
    <!-- Draft Letters -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Draft Letters</h5>
                <span class="badge bg-secondary">{{ $draftDocuments->count() }}</span>
            </div>
            <div class="card-body">
                @if($draftDocuments->isEmpty())
                    <p class="text-muted mb-0 text-center py-3">No draft letters.</p>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($draftDocuments as $document)
                            <a href="{{ route('documents.show', $document) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $document->reference_no }}</strong>
                                    <br>
                                    <small class="text-muted">{{ Str::limit($document->subject, 30) }}</small>
                                </div>
                                <small class="text-muted">{{ $document->created_at->diffForHumans() }}</small>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Letters -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recently Created</h5>
            </div>
            <div class="card-body">
                @if($recentDocuments->isEmpty())
                    <p class="text-muted mb-0 text-center py-3">No letters created yet.</p>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($recentDocuments as $document)
                            <a href="{{ route('documents.show', $document) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $document->reference_no }}</strong>
                                    <br>
                                    <small class="text-muted">{{ Str::limit($document->recipient_name, 25) }}</small>
                                </div>
                                <small class="text-muted">{{ $document->created_at->diffForHumans() }}</small>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="card-footer bg-transparent">
                <a href="{{ route('documents.index') }}" class="btn btn-sm btn-outline-primary">View All Letters</a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <a href="{{ route('documents.create') }}" class="btn btn-primary w-100 py-3">
                    <i class="bi bi-plus-circle d-block fs-4 mb-2"></i>
                    New Letter
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('templates.create') }}" class="btn btn-outline-primary w-100 py-3">
                    <i class="bi bi-file-earmark-plus d-block fs-4 mb-2"></i>
                    New Template
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('documents.index', ['status' => 'final']) }}" class="btn btn-outline-success w-100 py-3">
                    <i class="bi bi-check-circle d-block fs-4 mb-2"></i>
                    Finalized Letters
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary w-100 py-3">
                    <i class="bi bi-gear d-block fs-4 mb-2"></i>
                    Settings
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
