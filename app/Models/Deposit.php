<?php

namespace App\Models;

use App\Enums\BankEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'amount',
        'reference_number',
        'bank',
        'model_id',
        'branch_id',
    ];

    protected $casts = [
        'bank' => BankEnum::class,
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
