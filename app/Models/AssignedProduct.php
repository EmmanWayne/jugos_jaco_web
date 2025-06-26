<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssignedProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailAssignedProduct::class, 'assigned_products_id');
    }

    public function scopeTodayAssignments($query)
    {
        return $query->whereDate('date', today());
    }
}
