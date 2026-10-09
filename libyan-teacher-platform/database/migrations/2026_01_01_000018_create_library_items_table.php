<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('description');
            $table->string('type');
            $table->string('file_path')->nullable();
            $table->string('url')->nullable();
            $table->foreignId('subject_id')->nullable()->constrained()->onDelete('set null');
            $table->string('grade_level')->nullable();
            $table->integer('downloads')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_items');
    }
};
