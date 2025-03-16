<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'shoe_id',
        'color_id',
        'size_id',
        'address',
        'entrance',
        'apartment',
        'payment_method',
        'status'
    ];

    public function shoe()
    {
        return $this->belongsTo(Shoe::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }
} 