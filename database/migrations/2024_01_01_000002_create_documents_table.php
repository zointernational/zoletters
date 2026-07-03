<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained()->onDelete('cascade');
            $table->string('reference_no')->unique();
            $table->string('recipient_name');
            $table->text('recipient_address');
            $table->string('subject');
            $table->longText('body_html');
            $table->string('pdf_file')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index('reference_no');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
