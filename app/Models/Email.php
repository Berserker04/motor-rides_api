<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'fullName',
        'email',
        'cellPhone',
        'subject',
        'message',
        'emailState_id',
    ];

    protected $attributes = [
        'emailState_id' => 1,
    ];

    public function state()
    {
        return $this->hasOne(EmailState::class, 'id', 'emailState_id');
    }
}
