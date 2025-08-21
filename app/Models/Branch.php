<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Employee;
use App\Models\DailySalesReconciliation;

class Branch extends Model
{
    use HasFactory;

    protected $table = 'branches';

    protected $fillable = [
        'name',
        'address',
        'phone_number',
    ];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'branch_id');
    }
    
    /**
     * Get the daily sales reconciliations for the branch.
     */
    public function dailySalesReconciliations(): HasMany
    {
        return $this->hasMany(DailySalesReconciliation::class, 'branch_id');
    }
}
