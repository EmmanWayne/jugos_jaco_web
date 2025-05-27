<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class FinishedProductInventory extends Model
{
    use HasFactory;

    protected $table = 'finished_product_inventories';

    protected $fillable = [
        'product_id',
        'stock',
        'min_stock',
        'branch_id',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function movements(): MorphMany
    {
        return $this->morphMany(ManagementInventory::class, 'model');
    }
}
