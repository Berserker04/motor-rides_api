<?php

namespace App\Http\Controllers\Api\V1\shared;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValidateController
{
    public static function validate(Request $data, $rules)
    {
        $validator = Validator::make($data->all(), $rules);

        $validator->setCustomMessages([
            "required" => "El campo :attribute es obligatorio.",
            "numeric" => "El campo :attribute debe ser un número.",
            "string" => "El campo :attribute debe ser un cadena de caracteres.",
            "email" => "El campo :attribute debe ser un emial valido.",
            "max" => "El campo :attribute supera el tamaño permitido.",
            "min" => "El campo :attribute no tiene el valor minimo requerido.",
            "exists" => "El campo :attribute es invalido.",
        ]);

        if($validator->fails()){
            return $validator->messages();
        }
        return null;
    }
}
