<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    use HasFactory;
    /** Order is available for sell, anyone can buy it. */
//    const STATE_AVAILABLE = 0;

    /** Pay in progress, order must not be changed. */
    const STATE_WAITING_PAY = 0;

    /** Order completed and not available for sell. */
    const STATE_PAY_ACCEPTED = 10;

    /** Order is cancelled. */
    const STATE_CANCELLED = -2;
    const STATE_FINISHED=4;


    protected $fillable = [
        'customer_id', 'driver_id', 'partner_id', 'total_price',
        'item_count', 'address', 'longitude', 'latitude', 'status',
        'delivery_price','payment_type'
    ];

    public function customer() {
        return $this->belongsTo(Customer::class);
    }
    public function partner() {
        return $this->belongsTo(Partner::class);
    }
    public  function items() {
        return $this->hasMany(OrderItem::class);
    }
    public function driver() {
        return $this->belongsTo(Delivery::class, 'driver_id','id');
    }


//    public function getClientAttribute() {
//        return $this->customer;
//    }
}