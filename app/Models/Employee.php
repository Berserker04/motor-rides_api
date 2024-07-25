<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'document',
        'cellPhone',
        'position_id'
    ];

    public function position()
    {
        return $this->hasOne(Position::class, 'id', 'position_id');
    }
}
