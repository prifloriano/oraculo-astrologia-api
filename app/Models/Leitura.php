<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leitura extends Model
{
    protected $fillable = [
        'nome_consulente', 
        'pergunta', 
        'dados_mapa', 
        'resultado_leitura'
    ];

    protected function casts(): array
    {
        return [
            'dados_mapa' => 'array',
            'resultado_leitura' => 'array',
        ];
    }
}