<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResourceMedia extends Model
{
    use HasFactory;

    protected $table = 'resources_media';

    protected $fillable = [
        'path',
        'type',
        'model_id',
        'model_type',
    ];

    /**
     * Define relationship polymorphic with model.
     */
    public function model()
    {
        return $this->morphTo();
    }
}
