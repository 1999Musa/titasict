<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description'];

    public function days()
    {
        return $this->hasMany(BatchDay::class);
    }
}
