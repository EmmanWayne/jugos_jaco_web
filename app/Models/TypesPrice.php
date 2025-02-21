<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypesPrice extends Model
{
    use HasFactory;

    protected $table = 'types_prices';

    protected $fillable = [
        'name',
    ];
}
