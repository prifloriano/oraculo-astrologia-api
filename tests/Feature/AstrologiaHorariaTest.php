<?php

namespace Tests\Feature;

use Tests\TestCase;

class AstrologiaHorariaTest extends TestCase
{
    public function test_deve_retornar_a_leitura_horaria_estruturada()
    {
        $payload = [
            "nome" => "Priscila",
            "pergunta" => "Vou conseguir alugar aquele apartamento?",
            "data" => "2026-06-17",
            "hora" => "13:30",
            "latitude" => -25.4284,
            "longitude" => -49.2733
        ];

        $response = $this->postJson('/api/v1/astrologia/horaria', $payload);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'consulente',
                         'leitura' => [
                             'introducao',
                             'protagonistas',
                             'fluxo_eventos',
                             'veredito'
                         ]
                     ]
                 ]);
    }
}