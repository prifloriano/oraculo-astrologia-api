<?php

namespace Tests\Unit;

use Tests\TestCase; 
use App\Services\InterpretationService;
use Illuminate\Support\Facades\Http;

class InterpretationServiceTest extends TestCase
{
    public function test_deve_retornar_array_estruturado_ao_consultar_ia()
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => '{"introducao": "Olá Priscila...", "protagonistas": {"titulo": "As Forças", "texto": "Marte forte", "frase_destaque": "Foco"}, "fluxo_eventos": {"titulo": "A Lua", "texto": "Lua em Escorpião", "frase_destaque": "Cuidado"}, "veredito": {"titulo": "O Resultado", "texto": "Vai dar boa", "conselho": "Tenha paciência"}}'
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $service = new InterpretationService();
        
        $dadosConsulente = [
            'nome' => 'Priscila', 
            'pergunta' => 'Teste unitário funciona?'
        ];
        
        $dadosAstrologicos = [
            'regente_asc' => 'Marte', 
            'signo_asc'   => 'Áries', 
            'lua_signo'   => 'Escorpião', 
            'lua_graus'   => '14'
        ];

        $resultado = $service->gerarLeituraHoraria($dadosConsulente, $dadosAstrologicos);

        $this->assertIsArray($resultado);
        $this->assertArrayHasKey('introducao', $resultado);
        $this->assertArrayHasKey('protagonistas', $resultado);
        $this->assertArrayHasKey('veredito', $resultado);
        $this->assertEquals('Olá Priscila...', $resultado['introducao']);
    }
}