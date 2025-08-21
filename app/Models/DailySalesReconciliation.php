<?php

namespace App\Models;

use App\Enums\ReconciliationStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailySalesReconciliation extends Model
{
    use HasFactory;

    protected $table = 'daily_sales_reconciliations';

    protected $fillable = [
        'employee_id',
        'cashier_id',
        'branch_id',
        'reconciliation_date',
        'total_cash_sales',
        'total_credit_sales',
        'total_sales',
        'total_cash_received',
        'total_deposits',
        'total_collections',
        'total_cash_expected',
        'cash_difference',
        'notes',
        'status',
    ];

    protected $casts = [
        'reconciliation_date' => 'date',
        'total_cash_sales' => 'decimal:2',
        'total_credit_sales' => 'decimal:2',
        'total_sales' => 'decimal:2',
        'total_cash_received' => 'decimal:2',
        'total_deposits' => 'decimal:2',
        'total_collections' => 'decimal:2',
        'total_cash_expected' => 'decimal:2',
        'cash_difference' => 'decimal:2',
        'status' => ReconciliationStatusEnum::class,
    ];

    /**
     * Get the employee that owns the reconciliation.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the cashier (user) that created the reconciliation.
     */
    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    /**
     * Get the branch that the reconciliation belongs to.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Calculate the expected cash based on sales and collections.
     */
    public function calculateExpectedCash(): float
    {
        return $this->total_cash_sales + $this->total_collections - $this->total_deposits;
    }

    /**
     * Calculate the cash difference.
     */
    public function calculateCashDifference(): float
    {
        return $this->total_cash_received - $this->calculateExpectedCash();
    }

    /**
     * Update the status based on cash difference.
     */
    public function updateStatus(): void
    {
        if ($this->cash_difference != 0) {
            $this->status = ReconciliationStatusEnum::WITH_DIFFERENCES;
        } else {
            $this->status = ReconciliationStatusEnum::COMPLETED;
        }
        
        $this->save();
    }

    /**
     * Recalculate all totals and update the record.
     */
    public function recalculateTotals(): void
    {
        // Calculate total sales
        $this->total_sales = $this->total_cash_sales + $this->total_credit_sales;
        
        // Calculate expected cash
        $this->total_cash_expected = $this->calculateExpectedCash();
        
        // Calculate cash difference
        $this->cash_difference = $this->calculateCashDifference();
        
        // Update status
        $this->updateStatus();
    }
}