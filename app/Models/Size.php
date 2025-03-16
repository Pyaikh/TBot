<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    use HasFactory;

    protected $fillable = ['value'];

    public function shoes()
    {
        return $this->belongsToMany(Shoe::class);
    }
} 