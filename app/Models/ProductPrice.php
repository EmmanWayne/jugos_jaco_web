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
        'product_unit_id',
        'price',
        'tax_category_id',
        'price_include_tax',
    ];

    protected function casts()
    {
        return [
            'price' => 'decimal:2',
            'price_include_tax' => 'boolean',
        ];
    }

    /**
     * Calcula el precio base sin impuesto
     */
    public function getPriceWithoutTax()
    {
        if (!$this->price_include_tax || !$this->taxCategory || $this->taxCategory->rate == 0) {
            return $this->price;
        }

        return $this->price / (1 + ($this->taxCategory->rate / 100));
    }

    /**
     * Calcula el impuesto del precio
     */
    public function getTaxAmount()
    {
        if (!$this->taxCategory) {
            return 0;
        }

        $basePrice = $this->getPriceWithoutTax();
        return $this->taxCategory->calculateTax($basePrice);
    }

    /**
     * Calcula el precio con impuesto incluido
     */
    public function getPriceWithTax()
    {
        if ($this->price_include_tax) {
            return $this->price;
        }

        return $this->price + $this->getTaxAmount();
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function typePrice()
    {
        return $this->belongsTo(TypePrice::class);
    }

    public function taxCategory()
    {
        return $this->belongsTo(TaxCategory::class);
    }

    public function productUnit()
    {
        return $this->belongsTo(ProductUnit::class);
    }
}
