<?php

namespace App\Models;

use App\Enums\DepartmentEnum;
use App\Enums\MunicipalityEnum;
use App\Enums\VisitDayEnum;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Client extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    protected $table = 'clients';

    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'business_name',
        'address',
        'department',
        'township',
        'employee_id',
        'type_price_id',
    ];

    public array $sortable = [
        'order_column_name' => 'position',
        'sort_when_creating' => true,
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
            'township' => MunicipalityEnum::getByDepartment(DepartmentEnum::from($this->department)),
        ];
    }

    public function buildSortQuery()
    {
        return static::query()->where('visit_day', $this->visit_day);
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
    public function profileImage(): MorphOne
    {
        return $this->morphOne(ResourceMedia::class, 'model')->where('type', 'profile');
    }

    /**
     * Get the images of the client with polimorphic relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function businessImages(): MorphMany
    {
        return $this->morphMany(ResourceMedia::class, 'model')->where('type', 'business');
    }

    /**
     * Get the visit days for the client.
     */
    public function visitDays(): HasMany
    {
        return $this->hasMany(ClientVisitDay::class);
    }

    /**
     * Scope a query to only include clients for a especific day.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $day
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithVisitDaysForDay($query, $day = null): Builder
    {
        if (!$day) return $query->with('visitDays');
        
        return $query->with(['visitDays' => function ($query) use ($day) {
            $query->where('visit_day', $day);
        }]);
    }

    public function scopeOrderByVisitDay($query, $day): Builder
    {
        if (!$day) return $query;
        return $query->join('client_visit_days', 'clients.id', '=', 'client_visit_days.client_id')
            ->orderBy('client_visit_days.position')
            ->where('client_visit_days.visit_day', $day)
            ->select('clients.*');
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

    public function getProfileImageUrlAttribute(): string
    {
        return $this->profileImage ? asset('storage/' . $this->profileImage->path) : asset('images/avatar.png');
    }
}
