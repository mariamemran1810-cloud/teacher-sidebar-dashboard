<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $table->foreignId('classroom_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('subject_id')->nullable()->constrained()->onDelete('set null');
            $table->string('week_number');
            $table->date('week_start');
            $table->date('week_end');
            $table->text('topics_covered')->nullable();
            $table->text('activities_done')->nullable();
            $table->text('challenges')->nullable();
            $table->text('next_week_plan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_followups');
    }
};
