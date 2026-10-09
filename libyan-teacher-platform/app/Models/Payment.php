<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'amount',
        'payment_date',
        'payment_method',
        'status',
        'description',
        'receipt_number',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
