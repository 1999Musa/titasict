<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'type',
        'month',
        'amount',
        'status',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
