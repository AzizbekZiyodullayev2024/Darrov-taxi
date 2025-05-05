<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Tariff $tariff
 */
class Car extends Model {
    use HasFactory;

    protected $fillable = [
        'number', 'color', 'count_seats', 'manufacture_date', 'driver_id', 'type_id', 'tariff_id', 'status'
    ];

    public function driver() {
        return $this->belongsTo(Driver::class);
    }

    public function tariff() {
        return $this->belongsTo(Tariff::class);
    }

    public function type() {
        return $this->belongsTo(CarType::class);
    }
}
