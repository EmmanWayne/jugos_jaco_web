<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{

    use HasFactory;

    protected $table = 'locations';

    protected $fillable = [
        'latitude',
        'longitude',
        'plus_code',
        'model_id',
        'model_type',
    ];

    protected function cast(): array
    {
        return [
            'latitude' => 'double',
            'longitude' => 'double',
        ];
    }

    public function model()
    {
        return $this->morphTo();
    }

    public function getGoogleMapsUrlAttribute(): string
    {
        return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
    }
}
