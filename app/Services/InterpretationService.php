<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Contracts\AstrologyAiInterface;

class InterpretationService implements AstrologyAiInterface
{
    public function gerarLeituraHoraria(array $dadosConsulente, array $dadosAstrologicos): array
    {
        $template = "Atue como um astrólogo moderno, didático e objetivo, especialista em Astrologia Horária. Sua linguagem deve ser formal, porém acessível e fácil de entender, traduzindo o 'astrologuês' para termos práticos. Evite fatalismos ou jargões excessivamente antigos.

        Dados da Questão:
        - Consulente: {nome}
        - Pergunta: {pergunta}
        - Regente do Ascendente: {regente_asc} em {signo_asc}
        - Posição da Lua: {lua_signo} a {lua_graus} graus
        
        Você DEVE retornar APENAS um objeto JSON válido, sem formatação markdown em volta, com a seguinte estrutura exata:
        {
            \"introducao\": \"Um parágrafo de acolhimento dirigido a {nome}, reformulando a pergunta para confirmar o entendimento.\",
            \"protagonistas\": {
                \"titulo\": \"As Forças em Jogo\",
                \"texto\": \"Explicação acessível sobre a força do regente do ascendente e o que isso diz sobre a capacidade de ação do consulente.\",
                \"frase_destaque\": \"Uma frase curta e de impacto sobre o posicionamento principal.\"
            },
            \"fluxo_eventos\": {
                \"titulo\": \"O Desenrolar da Situação\",
                \"texto\": \"Explicação didática sobre a posição da Lua e o que ela indica sobre os obstáculos ou facilidades.\",
                \"frase_destaque\": \"Uma frase curta e de impacto sobre a dinâmica do momento.\"
            },
            \"veredito\": {
                \"titulo\": \"A Resposta dos Astros\",
                \"texto\": \"A conclusão clara, direta e empática respondendo à pergunta.\",
                \"conselho\": \"Um conselho prático sobre como lidar com essa resposta.\"
            }
        }";

        $promptFinal = Str::replace(
            ['{nome}', '{pergunta}', '{regente_asc}', '{signo_asc}', '{lua_signo}', '{lua_graus}'],
            [
                $dadosConsulente['nome'], 
                $dadosConsulente['pergunta'], 
                $dadosAstrologicos['regente_asc'], 
                $dadosAstrologicos['signo_asc'], 
                $dadosAstrologicos['lua_signo'], 
                $dadosAstrologicos['lua_graus']
            ],
            $template
        );

        // Requisição para a API do Gemini
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . env('GEMINI_API_KEY'), [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $promptFinal]
                    ]
                ]
            ]
        ]);

        if ($response->successful()) {
            $textoIa = $response->json('candidates.0.content.parts.0.text');
            
            $textoIa = str_replace(['```json', '```'], '', $textoIa);
            $textoIa = trim($textoIa);

            return json_decode($textoIa, true) ?? [
                "erro" => "A IA não retornou um JSON válido."
            ];
        }

        return [
            "erro" => "Erro do Google: " . $response->body()
        ];
    }

    public function gerarMapaAstral(array $dadosConsulente, array $dadosAstrologicos): array
    {
        $template = "Atue como um astrólogo moderno, didático e empático. Sua linguagem deve ser formal, porém acessível e fácil de entender, focada no desenvolvimento pessoal.

        Dados do Consulente:
        - Nome: {nome}
        - Sol em {sol}
        - Lua em {lua}
        - Ascendente em {ascendente}
        
        Você DEVE retornar APENAS um objeto JSON válido, sem formatação markdown, com a seguinte estrutura exata:
        {
            \"introducao\": \"Um parágrafo de boas-vindas acolhedor para {nome}.\",
            \"essencia_solar\": {
                \"titulo\": \"A Essência Solar\",
                \"texto\": \"Explicação acessível sobre o Sol em {sol}.\",
                \"frase_destaque\": \"Uma frase de impacto sobre o ego e brilho pessoal.\"
            },
            \"refugio_lunar\": {
                \"titulo\": \"O Refúgio Lunar\",
                \"texto\": \"Explicação didática sobre a Lua em {lua} e como a pessoa lida com as emoções.\",
                \"frase_destaque\": \"Uma frase curta sobre a natureza emocional.\"
            },
            \"ascendente\": {
                \"titulo\": \"A Máscara Social\",
                \"texto\": \"Como o Ascendente em {ascendente} afeta a primeira impressão.\",
                \"frase_destaque\": \"Uma frase sobre a postura diante do mundo.\"
            },
            \"desafios\": [
                \"Ponto de atenção construtivo 1 sobre os posicionamentos.\",
                \"Ponto de atenção construtivo 2 sobre os posicionamentos.\"
            ]
        }";

        // AQUI ESTÁ A MÁGICA DA CORREÇÃO:
        $promptFinal = Str::replace(
            ['{nome}', '{sol}', '{lua}', '{ascendente}'],
            [
                $dadosConsulente['nome'], 
                $dadosAstrologicos['planetas']['sol']['signo'], 
                $dadosAstrologicos['planetas']['lua']['signo'], 
                $dadosAstrologicos['pontos_importantes']['ascendente']
            ],
            $template
        );

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . env('GEMINI_API_KEY'), [
            'contents' => [['parts' => [['text' => $promptFinal]]]]
        ]);

        if ($response->successful()) {
            $textoIa = $response->json('candidates.0.content.parts.0.text');
            $textoIa = str_replace(['```json', '```'], '', $textoIa);
            return json_decode(trim($textoIa), true) ?? ["erro" => "A IA não retornou um JSON válido."];
        }

        return ["erro" => "Erro do Google: " . $response->body()];
    }
}