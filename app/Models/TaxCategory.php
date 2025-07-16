<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxCategory extends Model
{
    use HasFactory;

    protected $table = 'tax_categories';

    protected $fillable = [
        'name',
        'rate',
        'sequence_invoice',
        'type_tax_use',
        'is_active',
        'description',
        'display_type',
        'base_tax_id',
        'is_for_products',
        'calculation_type',
    ];

    protected function casts()
    {
        return [
            'rate' => 'decimal:2',
            'sequence_invoice' => 'integer',
            'is_active' => 'boolean',
            'is_for_products' => 'boolean',
        ];
    }

    /**
     * Relación con precios de productos
     */
    public function productPrices()
    {
        return $this->hasMany(ProductPrice::class, 'tax_category_id');
    }

    /**
     * Relación con la categoría base (para líneas de display)
     */
    public function baseTaxCategory()
    {
        return $this->belongsTo(TaxCategory::class, 'base_tax_id');
    }

    /**
     * Relación con las líneas de display derivadas
     */
    public function displayLines()
    {
        return $this->hasMany(TaxCategory::class, 'base_tax_id');
    }

    /**
     * Relación con categorías derivadas (para líneas de display)
     */
    public function derivedTaxCategories()
    {
        return $this->hasMany(TaxCategory::class, 'base_tax_id');
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class, 'tax_category_id');
    }

    public function saleTaxTotals()
    {
        return $this->hasMany(SaleTaxTotal::class, 'tax_category_id');
    }

    /**
     * Scope para categorías que se asignan a productos
     */
    public function scopeForProducts($query)
    {
        return $query->where('is_for_products', true);
    }

    /**
     * Scope para líneas de display en factura
     */
    public function scopeForInvoiceDisplay($query)
    {
        return $query->where('is_for_products', false);
    }

    /**
     * Scope para categorías activas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para categorías de venta
     */
    public function scopeForSales($query)
    {
        return $query->whereIn('type_tax_use', ['sale', 'all']);
    }

    /**
     * Scope para categorías de compra
     */
    public function scopeForPurchases($query)
    {
        return $query->whereIn('type_tax_use', ['purchase', 'all']);
    }

    /**
     * Scope ordenado por secuencia de factura
     */
    public function scopeOrderedForInvoice($query)
    {
        return $query->orderBy('sequence_invoice', 'asc');
    }

    /**
     * Calcula el impuesto para un monto base
     */
    public function calculateTax($baseAmount)
    {
        return $baseAmount * ($this->rate / 100);
    }

    /**
     * Obtiene las categorías para mostrar en productos (UX limpio)
     */
    public static function getForProductSelection()
    {
        return static::forProducts()
            ->active()
            ->forSales()
            ->orderBy('sequence_invoice')
            ->get()
            ->mapWithKeys(function ($category) {
                $label = $category->rate == 0 
                    ? "{$category->name} (Libre de impuesto)"
                    : "{$category->name} ({$category->rate}%)";
                
                return [$category->id => $label];
            });
    }
}
