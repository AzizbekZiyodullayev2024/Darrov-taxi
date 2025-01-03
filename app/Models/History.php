<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;
    protected $fillable = ['driver_id', 'order_id', 'order_type', 'fare', 'minute', 'path', 'status'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}