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
        'latitude',
        'longitude',
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
}
