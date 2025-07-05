<?php

namespace App\Models;

use App\Enums\UnitCategoryEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'abbreviation',
        'category',
        'description',
        'is_active',
    ];

    protected function casts()
    {
        return [
            'is_active' => 'boolean',
            'category' => UnitCategoryEnum::class,
        ];
    }

    /**
     * Scope para unidades activas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para filtrar por categoría
     */
    public function scopeByCategory($query, UnitCategoryEnum $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Obtener el nombre de la categoría traducido
     */
    public function getCategoryNameAttribute(): string
    {
        return $this->category?->getLabel() ?? 'Sin categoría';
    }
}
