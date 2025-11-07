<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BatchTime extends Model
{
    use HasFactory;

    protected $fillable = ['batch_day_id', 'time'];

    public function batchDay()
    {
        return $this->belongsTo(BatchDay::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
