<?php

namespace App\Models;

use App\Enums\DepartmentEnum;
use App\Enums\MunicipalityEnum;
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
            'township' => MunicipalityEnum::getByDepartment($this->department),
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

    /**
     * Get the profile photo of the client with polimorphic relationship.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function profileImage()
    {
        return $this->morphOne(ResourceMedia::class, 'model')->where('type', 'profile');
    }

    /**
     * Get the images of the client with polimorphic relationship.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function businessImages()
    {
        return $this->morphMany(ResourceMedia::class, 'model')->where('type', 'business');
    }

    // Scopes
    public function scopeWithLocationData($query)
    {
        return $query->with(['location', 'employee.branch'])
            ->has('location');
    }

    public function getMapDataAttribute(): array
    {
        return [
            'id' => $this->id,
            'tipo' => 'cliente',
            'nombre' => $this->full_name,
            'direccion' => $this->address,
            'department' => $this->department,
            'township' => $this->township,
            'phone_number' => $this->phone_number,
            'empleado' => $this->employee?->full_name ?? 'Sin asignar',
            'employee_id' => $this->employee_id,
            'has_location' => $this->location !== null,
            'location' => $this->when($this->location, fn() => [
                'lat' => $this->location->latitude,
                'lng' => $this->location->longitude,
                'maps_url' => $this->location->google_maps_url,
                'whatsapp_url' => $this->whatsapp_share_url
            ])
        ];
    }

    public function getWhatsappShareUrlAttribute(): string
    {
        if (!$this->location) return '';

        $message = "*Información del Cliente*\n" .
            "Nombre: {$this->full_name}\n" .
            "Dirección: {$this->address}\n" .
            "Departamento: {$this->department}\n" .
            "Municipio: {$this->township}\n" .
            "Teléfono: {$this->phone_number}\n" .
            "Ubicación: {$this->location->google_maps_url}\n" .
            "Empleado Asignado: " . ($this->employee?->full_name ?? "Sin asignar");

        return "https://wa.me/?text=" . urlencode($message);
    }
}
