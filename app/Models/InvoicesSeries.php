<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoicesSeries extends Model
{

    protected $table = 'invoices_series';

    protected $fillable = [
        'cai',
        'initial_range',
        'end_range',
        'expiration_date',
        'status',
        'mask_format',
        'prefix',
        'current_number',
        'branch_id'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
