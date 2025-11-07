<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BatchDay extends Model
{
    use HasFactory;

    protected $fillable = ['batch_id', 'days'];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function times()
    {
        return $this->hasMany(BatchTime::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
