<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'isDeleted',
        'category_id',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'sub_category_id');
    }
}
