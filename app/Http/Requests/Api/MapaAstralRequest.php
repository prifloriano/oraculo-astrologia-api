<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class MapaAstralRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'nome'            => 'required|string|max:255',
            'data_nascimento' => 'required|date_format:Y-m-d',
            'hora_nascimento' => 'required|date_format:H:i',
            'latitude'        => 'required|numeric',
            'longitude'       => 'required|numeric',
        ];
    }
}