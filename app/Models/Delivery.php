<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property Car $car
 * @property int $id
 * @property int $latitude
 * @property int $longitude
 * @property int $vip
 * @property Tariff $tariff
 * @property int $sum
 */
class Delivery extends Model {
    use HasFactory, HasApiTokens;
    public const FREE = 0;
    public const BUSY = 1;

    protected $fillable = [
        'name', 'surname', 'address', 'phone', 'password', 'birth_date',
        'gender', 'card_number', 'gmail',  'img', 'status',
        'longitude', 'latitude','sum'
    ];

    public function car() {
        return $this->hasOne(Car::class)->with('type');
    }

    public function getActiveAttribute() {
        return $this->car->status;
    }

    public function isVip() {
        return $this->vip;
    }

    public function tariff() {
        return $this->car->tariff;
    }

    public function history() {
        return $this->hasMany(History::class);
    }

    public function writeHistory(array $data) {
        return $this->history()->create($data);
    }

    public function scopeOnline($query) {
        return $query->where('updated_at', '>', now()->subSeconds(20));
    }

    public function scopeStatusActive($query) {
        return $query->where('status', 0);
    }

    public function scopeActive($query) {
        return $query->where('vip', 1)->orWhere('sum', '>', 0);
    }
    protected $table='delivery';
}
