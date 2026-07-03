<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\DocumentController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['web'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Template Routes
    Route::resource('templates', TemplateController::class)->names([
        'index' => 'templates.index',
        'create' => 'templates.create',
        'store' => 'templates.store',
        'show' => 'templates.show',
        'edit' => 'templates.edit',
        'update' => 'templates.update',
        'destroy' => 'templates.destroy',
    ]);

    // Document Routes
    Route::resource('documents', DocumentController::class)->names([
        'index' => 'documents.index',
        'create' => 'documents.create',
        'store' => 'documents.store',
        'show' => 'documents.show',
        'edit' => 'documents.edit',
        'update' => 'documents.update',
        'destroy' => 'documents.destroy',
    ]);

    // PDF Routes
    Route::get('/documents/{document}/pdf/preview', [DocumentController::class, 'previewPdf'])
        ->name('documents.pdf.preview');
    Route::get('/documents/{document}/pdf/download', [DocumentController::class, 'downloadPdf'])
        ->name('documents.pdf.download');
    Route::post('/documents/{document}/pdf/regenerate', [DocumentController::class, 'regeneratePdf'])
        ->name('documents.pdf.regenerate');
});
