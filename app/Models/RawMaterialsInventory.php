<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class RawMaterialsInventory extends Model
{
    use HasFactory;

    protected $table = 'raw_materials_inventory';

    protected $fillable = [
        'name',
        'unit_type',
        'stock',
        'min_stock',
        'branch_id'
    ];

    protected $casts = [
        'stock' => 'decimal:2',
        'min_stock' => 'decimal:2',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function movements(): MorphMany
    {
        return $this->morphMany(ManagementInventory::class, 'model');
    }
}
