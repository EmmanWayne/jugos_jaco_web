<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailAssignedProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'sale_quantity',
        'assigned_products_id',
    ];

    protected $cast = [
        'quantity' => 'decimal:2',
        'sale_quantity' => 'decimal:2',
    ];


    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function assignedProduct(): BelongsTo
    {
        return $this->belongsTo(AssignedProduct::class, 'assigned_products_id');
    }

    /**
     * Get the stock attribute.
     *
     * @return int
     */
    protected function stock(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->quantity - $this->sale_quantity,
        );
    }
}
