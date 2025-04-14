<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RawMaterialsInventory extends Model
{
    use HasFactory;

    protected $table = 'raw_materials_inventory';

    protected $fillable = [
        'name',
        'unit',
        'quantity',
        'minimum_stock',
        'description',
        'branch_id'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'minimum_stock' => 'decimal:2',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
