<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Branch;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employees';

    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'address',
        'identity',
        'branch_id',
    ];

    protected $casts = [
        'last_location_at' => 'datetime',
    ];

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
    
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function locations()
    {
        return $this->morphMany(Location::class, 'model');
    }

    public function scopeWithRouteData($query)
    {
        return $query->with(['locations' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }, 'branch']);
    }

    public function scopeActiveToday($query)
    {
        return $query->whereHas('locations', function ($query) {
            $query->whereDate('created_at', now());
        });
    }

    public function getMapDataAttribute(): array
    {
        $lastLocation = $this->locations->first();
        
        return [
            'id' => $this->id,
            'nombre' => $this->full_name,
            'phone_number' => $this->phone_number,
            'identity' => $this->identity,
            'address' => $this->address,
            'branch_name' => $this->branch?->name ?? 'Sin sucursal',
            'has_routes' => $this->locations->isNotEmpty(),
            'en_ruta' => $lastLocation?->created_at->isToday() ?? false,
            'last_location' => $lastLocation ? [
                'timestamp' => $lastLocation->created_at->format('Y-m-d H:i:s'),
                'date' => $lastLocation->created_at->format('Y-m-d')
            ] : null,
            'locations' => $this->locations->map(fn($location) => [
                'lat' => $location->latitude,
                'lng' => $location->longitude,
                'timestamp' => $location->created_at->format('Y-m-d H:i:s'),
                'date' => $location->created_at->format('Y-m-d'),
                'maps_url' => $location->google_maps_url
            ])
        ];
    }
}
