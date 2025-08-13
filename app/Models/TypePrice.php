<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypePrice extends Model
{
    use HasFactory;

    protected $table = 'types_prices';

    protected $fillable = [
        'name',
    ];

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class, 'type_price_id');
    }

    public function clients()
    {
        return $this->hasMany(Client::class, 'type_price_id');
    }
}
