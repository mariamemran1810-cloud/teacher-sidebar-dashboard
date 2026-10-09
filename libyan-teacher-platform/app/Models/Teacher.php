<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'email',
        'specialization',
        'qualification',
        'hire_date',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lessonPlans(): HasMany
    {
        return $this->hasMany(LessonPlan::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function weeklyFollowups(): HasMany
    {
        return $this->hasMany(WeeklyFollowup::class);
    }

    public function monthlyReports(): HasMany
    {
        return $this->hasMany(MonthlyReport::class);
    }

    public function parentMessages(): HasMany
    {
        return $this->hasMany(ParentMessage::class);
    }

    public function libraryItems(): HasMany
    {
        return $this->hasMany(LibraryItem::class);
    }
}
