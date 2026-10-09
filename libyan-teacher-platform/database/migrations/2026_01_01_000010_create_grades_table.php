<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained()->onDelete('set null');
            $table->string('term')->default('الأول'); // الأول، الثاني، الثالث
            $table->decimal('work_marks', 5, 2)->default(0); // أعمال السنة
            $table->decimal('quiz_marks', 5, 2)->default(0); // الفرض
            $table->decimal('exam_marks', 5, 2)->default(0); // الامتحان
            $table->decimal('total', 5, 2)->default(0);
            $table->string('grade')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
