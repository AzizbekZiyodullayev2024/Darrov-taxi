<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Client extends Model {
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name',
        'phone',
        'password'
    ];
}
