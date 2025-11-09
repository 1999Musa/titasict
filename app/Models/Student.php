<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_day_id',
        'batch_time_id',
        'name',
        'mobile_number',
        'joining_month',
        'guardian_mobile_number',
        'gender',
        'exam_year',
        'status',
    ];

    public function batchDay()
    {
        return $this->belongsTo(BatchDay::class);
    }

    public function batchTime()
    {
        return $this->belongsTo(BatchTime::class);
    }

    public function payments()
{
    return $this->hasMany(Payment::class);
}

}
