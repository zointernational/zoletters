<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Models\Document;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_templates' => Template::count(),
            'total_documents' => Document::count(),
            'draft_count' => Document::drafts()->count(),
            'final_count' => Document::final()->count(),
            'printed_count' => Document::printed()->count(),
        ];

        $recentDocuments = Document::with('template')
            ->recent(5)
            ->get();

        $draftDocuments = Document::with('template')
            ->drafts()
            ->recent(5)
            ->get();

        return view('dashboard.index', compact('stats', 'recentDocuments', 'draftDocuments'));
    }
}
