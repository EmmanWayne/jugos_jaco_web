<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function typePrice()
    {
        return $this->belongsTo(TypePrice::class);
    }

    public function location()
    {
        return $this->morphOne(Location::class, 'model');
    }

    public function getFullNameAttribute()
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
}
