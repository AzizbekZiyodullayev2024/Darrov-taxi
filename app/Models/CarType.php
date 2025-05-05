<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarType extends Model {
    use HasFactory;
    public $timestamps = false;

    protected $fillable = ['brand', 'model'];

    public static function table() {
        return static::getTable();
    }
}
