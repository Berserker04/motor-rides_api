<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'title',
        'slug',
        'description',
        'image',
        'price',
        'sub_category_id',
        'productState_id'
    ];

    protected $attributes = [
        'productState_id' => 1,
    ];

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }

    public function state()
    {
        return $this->hasOne(ProductState::class, 'id', 'productState_id');
    }
}
