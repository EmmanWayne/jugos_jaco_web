<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_type',
        'model_id',
        'amount',
        'balance_after_payment',
        'payment_date',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after_payment' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
