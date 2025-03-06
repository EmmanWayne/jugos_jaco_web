<?php

namespace App\Models;

use App\Enums\DepartmentEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';

    protected $fillable = [
        'first_name',
        'last_name',
        'employee_id',
        'address',
        'phone_number',
        'department',
        'township',
        'type_price_id',
    ];

    /**
     * The attributes that should be cast.
     * 
     * @return array<string, string>
     */
    protected function cast(): array
    {
        return [
            'department' => DepartmentEnum::class,
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function typePrice(): BelongsTo
    {
        return $this->belongsTo(TypePrice::class);
    }

    /**
     * Define a polymorphic relationship with the location model.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function location(): MorphOne
    {
        return $this->morphOne(Location::class, 'model');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
