<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $vip
 */
class Tariff extends Model {
    use HasFactory;

    protected $fillable = [
        'name', 'client', 'minute', 'km', 'min_pay', 'min_km', 'out_city', 'vip'
    ];
}
