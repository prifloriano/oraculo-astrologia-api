<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class AstrologiaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome'      => 'required|string|max:255',
            'pergunta'  => 'required|string|max:500',
            'data'      => 'required|date_format:Y-m-d',
            'hora'      => 'required|date_format:H:i',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ];
    }
}