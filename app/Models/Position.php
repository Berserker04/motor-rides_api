<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'positionState_id'
    ];

    protected $attributes = [
        'positionState_id' => 1,
    ];

    public function state()
    {
        return $this->hasOne(PositionState::class, 'id', 'positionState_id');
    }
}
