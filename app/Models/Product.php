<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'code',
        'content_type',
        'content',
        'cost',
        'description',
        'is_active',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function profileImage()
    {
        return $this->morphOne(ResourceMedia::class, 'model')->where('type', 'product');
    }


    public function getImageUrlAttribute()
    {
        return $this->profileImage
            ? asset('storage/' . $this->profileImage->path)
            : asset('/images/producto.png');
    }
}
