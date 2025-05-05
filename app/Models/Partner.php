<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $categories
 * @property  $orders
 */
class Partner extends Model {
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name', 'username', 'password', 'start_time', 'end_time', 'address', 'phone', 'img', 'longitude', 'latitude','open'
        ,'position'
    ];

    protected $hidden = [
        'password'
    ];

    public function categories() {
        return $this->hasMany(Category::class);
    }

    public function orders() {
        return $this->hasMany(Order::class)->with('customer');
    }
}
