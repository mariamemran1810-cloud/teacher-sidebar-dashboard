<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'instructor',
        'duration',
        'start_date',
        'end_date',
        'status',
    ];
}
