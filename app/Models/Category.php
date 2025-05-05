<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Product $products
 */
class Category extends Model {
    use HasFactory;

    protected $fillable = ['name', 'partner_id', 'img'];

    public function products() {
        return $this->hasMany(Product::class);
    }
}
