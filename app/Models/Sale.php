<?php

namespace App\Models;

use App\Enums\SaleStatusEnum;
use App\Enums\PaymentTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'invoice_series_id',
        'client_id',
        'employee_id',
        'deposit_id',
        'sale_date',
        'due_date',
        'status',
        'payment_type',
        'cash_amount',
        'subtotal',
        'discount_percentage',
        'discount_amount',
        'discount_reason',
        'total_amount',
        'notes',
        'created_by',
        'updated_by',
        'confirmed_at',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'due_date' => 'date',
        'confirmed_at' => 'datetime',
        'status' => SaleStatusEnum::class,
        'payment_type' => PaymentTypeEnum::class,
        'subtotal' => 'decimal:4',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:4',
        'total_amount' => 'decimal:4',
        'cash_amount' => 'decimal:4',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function invoiceSeries(): BelongsTo
    {
        return $this->belongsTo(InvoicesSeries::class, 'invoice_series_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function taxTotals(): HasMany
    {
        return $this->hasMany(SaleTaxTotal::class);
    }

    public function deposit(): BelongsTo
    {
        return $this->belongsTo(Deposit::class);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', SaleStatusEnum::DRAFT);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', SaleStatusEnum::CONFIRMED);
    }

    public function scopePaid($query)
    {
        return $query->where('status', SaleStatusEnum::PAID);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', SaleStatusEnum::CANCELLED);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('sale_date', [$startDate, $endDate]);
    }

    public function isDraft(): bool
    {
        return $this->status === SaleStatusEnum::DRAFT;
    }

    public function isConfirmed(): bool
    {
        return $this->status === SaleStatusEnum::CONFIRMED;
    }

    public function isPaid(): bool
    {
        return $this->status === SaleStatusEnum::PAID;
    }

    public function isCancelled(): bool
    {
        return $this->status === SaleStatusEnum::CANCELLED;
    }

    public function canBeModified(): bool
    {
        return $this->status->canBeModified();
    }

    public function canBeCancelled(): bool
    {
        return $this->status->canBeCancelled();
    }

    /**
     * Obtiene el número de factura completo con el formato de la serie
     */
    public function getFullInvoiceNumberAttribute(): ?string
    {
        if (!$this->invoice_number || !$this->invoiceSeries) {
            return null;
        }

        $prefix = $this->invoiceSeries->prefix;
        $maskFormat = $this->invoiceSeries->mask_format;
        $formattedNumber = str_pad($this->invoice_number, strlen($maskFormat), '0', STR_PAD_LEFT);
        
        return $prefix . $formattedNumber;
    }

    /**
     * Calcula el total de impuestos
     */
    public function getTotalTaxAmountAttribute(): float
    {
        return $this->taxTotals->sum('total_amount');
    }

    /**
     * Calcula el monto sin impuestos
     */
    public function getAmountWithoutTaxAttribute(): float
    {
        return $this->total_amount - $this->getTotalTaxAmountAttribute();
    }

    /**
     * Verifica si la venta está facturada
     */
    public function isInvoiced(): bool
    {
        return !is_null($this->invoice_number);
    }

    /**
     * Verifica si la venta puede ser facturada
     */
    public function canBeInvoiced(): bool
    {
        return ($this->isConfirmed() || $this->isPaid()) && !$this->isInvoiced();
    }

    /**
     * Obtiene la información de facturación formateada
     */
    public function getInvoiceInfoAttribute(): ?array
    {
        if (!$this->isInvoiced()) {
            return null;
        }

        return [
            'series_id' => $this->invoice_series_id,
            'number' => $this->invoice_number,
            'full_number' => $this->full_invoice_number,
            'series' => $this->invoiceSeries,
        ];
    }
}
