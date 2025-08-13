<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'product_name',
        'product_code',
        'type_price_id',
        'unit_name',
        'unit_abbreviation',
        'quantity',
        'base_quantity',
        'unit_price_without_tax',
        'unit_tax_amount',
        'tax_category_id',
        'tax_category_name',
        'line_subtotal',
        'line_tax_amount',
        'line_total',
        'discount_percentage',
        'discount_amount',
        'product_unit_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'base_quantity' => 'decimal:4',
        'unit_price_without_tax' => 'decimal:4',
        'unit_tax_amount' => 'decimal:4',
        'line_subtotal' => 'decimal:4',
        'line_tax_amount' => 'decimal:4',
        'line_total' => 'decimal:4',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:4',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function typePrice(): BelongsTo
    {
        return $this->belongsTo(TypePrice::class, 'type_price_id');
    }

    public function taxCategory(): BelongsTo
    {
        return $this->belongsTo(TaxCategory::class);
    }

    public function productUnit(): BelongsTo
    {
        return $this->belongsTo(ProductUnit::class);
    }

    public function hasDiscount(): bool
    {
        return $this->discount_amount > 0 || $this->discount_percentage > 0;
    }

    public function hasTax(): bool
    {
        return $this->unit_tax_amount > 0;
    }

    /**
     * Calcula el precio unitario con impuestos
     */
    public function getUnitPriceWithTaxAttribute(): float
    {
        return $this->unit_price_without_tax + $this->unit_tax_amount;
    }

    /**
     * Calcula el subtotal sin descuentos
     */
    public function getSubtotalWithoutDiscountAttribute(): float
    {
        return $this->quantity * $this->unit_price_without_tax;
    }

    /**
     * Calcula el total de impuestos de la línea
     */
    public function getLineTaxTotalAttribute(): float
    {
        return $this->quantity * $this->unit_tax_amount;
    }

    /**
     * Calcula el precio final por unidad (con impuestos y descuentos)
     */
    public function getFinalUnitPriceAttribute(): float
    {
        $unitPriceWithTax = $this->unit_price_without_tax + $this->unit_tax_amount;
        $discountPerUnit = $this->discount_amount / $this->quantity;
        
        return $unitPriceWithTax - $discountPerUnit;
    }

    /**
     * Obtiene el formato de cantidad con unidad
     */
    public function getFormattedQuantityAttribute(): string
    {
        $abbreviation = $this->unit_abbreviation ?: $this->unit_name;
        return number_format($this->quantity, 2) . ' ' . $abbreviation;
    }
}
