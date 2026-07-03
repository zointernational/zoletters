<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('header_image')->nullable();
            $table->string('footer_image')->nullable();
            $table->decimal('margin_top', 6, 2)->default(20.00);
            $table->decimal('margin_bottom', 6, 2)->default(20.00);
            $table->decimal('margin_left', 6, 2)->default(25.00);
            $table->decimal('margin_right', 6, 2)->default(25.00);
            $table->enum('page_size', ['A4', 'A5', 'Letter', 'Legal'])->default('A4');
            $table->enum('orientation', ['portrait', 'landscape'])->default('portrait');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
