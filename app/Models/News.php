<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'title',
        'slug',
        'description',
        'image',
        'user_id',
        'newsState_id'
    ];

    protected $attributes = [
        'newsState_id' => 1,
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function images()
    {
        return $this->hasMany(NewsImage::class, 'news_id');
    }

    public function videos()
    {
        return $this->hasMany(NewsVideo::class, 'news_id');
    }

    public function state()
    {
        return $this->hasOne(NewsState::class, 'id', 'newsState_id');
    }
}
