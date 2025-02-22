<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    use HasFactory;

    protected $table = 'products_prices';

    protected $fillable = [
        'type_price_id',
        'product_id',
        'price',
    ];
}
