<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $table->foreignId('classroom_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('subject_id')->nullable()->constrained()->onDelete('set null');
            $table->string('month');
            $table->integer('year');
            $table->text('topics_covered');
            $table->text('achievements');
            $table->text('challenges');
            $table->text('next_month_plan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_reports');
    }
};
