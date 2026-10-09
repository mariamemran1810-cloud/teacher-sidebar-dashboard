<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('classroom_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('objectives')->nullable();
            $table->text('introduction')->nullable();
            $table->text('content')->nullable();
            $table->text('activities')->nullable();
            $table->text('assessment')->nullable();
            $table->text('homework')->nullable();
            $table->string('duration')->default('45 دقيقة');
            $table->date('date')->nullable();
            $table->string('period')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_plans');
    }
};
