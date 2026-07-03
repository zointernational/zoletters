<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\SettingsController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['web'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Settings Routes
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'store'])->name('settings.store');

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

    // Document Actions
    Route::get('/documents/{document}/preview', [DocumentController::class, 'preview'])
        ->name('documents.preview');
    Route::get('/documents/{document}/print', [DocumentController::class, 'print'])
        ->name('documents.print');
    Route::post('/documents/{document}/duplicate', [DocumentController::class, 'duplicate'])
        ->name('documents.duplicate');
    Route::post('/documents/{document}/archive', [DocumentController::class, 'archive'])
        ->name('documents.archive');
    Route::post('/documents/{id}/restore', [DocumentController::class, 'restore'])
        ->name('documents.restore')
        ->where('id', '[0-9]+');
    Route::post('/documents/{document}/status', [DocumentController::class, 'updateStatus'])
        ->name('documents.status');

    // PDF Routes
    Route::get('/documents/{document}/pdf/preview', [DocumentController::class, 'previewPdf'])
        ->name('documents.pdf.preview');
    Route::get('/documents/{document}/pdf/download', [DocumentController::class, 'downloadPdf'])
        ->name('documents.pdf.download');
    Route::post('/documents/{document}/pdf/regenerate', [DocumentController::class, 'regeneratePdf'])
        ->name('documents.pdf.regenerate');
});
