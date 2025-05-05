<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxiOrder extends Model
{
    use HasFactory;
    protected $fillable = ['driver_id', 'client_id', 'address', 'longitude', 'latitude', 'status'];

    public function client() {
        return $this->belongsTo(Client::class);
    }
}
