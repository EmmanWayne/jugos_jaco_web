<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleTaxTotal extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'tax_category_id',
        'tax_category_name',
        'total_amount',
    ];

    protected $casts = [
        'total_amount' => 'decimal:4',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function taxCategory(): BelongsTo
    {
        return $this->belongsTo(TaxCategory::class);
    }

    // Métodos de utilidad
    public function getFormattedAmountAttribute(): string
    {
        return 'L. ' . number_format($this->total_amount, 2);
    }

    /**
     * Obtiene el porcentaje de impuesto de la categoría fiscal
     */
    public function getTaxPercentageAttribute(): ?float
    {
        return $this->taxCategory?->rate;
    }
}
