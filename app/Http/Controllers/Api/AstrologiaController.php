<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AstrologiaRequest;
use App\Contracts\AstrologyAiInterface;
use App\Models\Leitura; 
use Illuminate\Http\JsonResponse;

class AstrologiaController extends Controller
{
    public function __construct(
        protected AstrologyAiInterface $aiService 
    ) {}

    public function calcular(AstrologiaRequest $request): JsonResponse
    {
        $dados = $request->validated();

        $agora = now()->timezone('America/Sao_Paulo');
        $dataPergunta = $agora->format('Y-m-d');
        $horaPergunta = $agora->format('H:i');

        $lat = '-25.4284'; 
        $lng = '-49.2733'; 

        $caminhoScript = storage_path('app/scripts/gerar_mapa.py');
        $comando = "python3 {$caminhoScript} 'Pergunta Horaria' '{$dataPergunta}' '{$horaPergunta}' '{$lat}' '{$lng}'";

        $processo = \Illuminate\Support\Facades\Process::run($comando);

        if (!$processo->successful()) {
            return response()->json([
                'erro' => 'O script Python falhou na horária.', 
                'detalhe' => $processo->errorOutput()
            ], 500);
        }

        $dadosAstrologicosReais = json_decode(trim($processo->output()), true);

        if (!isset($dadosAstrologicosReais['sucesso']) || !$dadosAstrologicosReais['sucesso']) {
             return response()->json([
                 'erro' => 'Erro interno no cálculo astronômico da horária.', 
                 'detalhe' => $dadosAstrologicosReais['erro'] ?? 'Erro desconhecido.'
            ], 500);
        }

        $resultado = $this->aiService->gerarLeituraHoraria($dados, $dadosAstrologicosReais);

        $leituraSalva = Leitura::create([
            'nome_consulente'   => $dados['nome'],
            'pergunta'          => $dados['pergunta'],
            'dados_mapa'        => $dadosAstrologicosReais, 
            'resultado_leitura' => $resultado              
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id_leitura' => $leituraSalva->id, 
                'consulente' => $leituraSalva->nome_consulente,
                'leitura'    => $leituraSalva->resultado_leitura
            ]
        ], 200);
    }

    public function gerarMapa(\App\Http\Requests\Api\MapaAstralRequest $request): \Illuminate\Http\JsonResponse
    {
        $dados = $request->validated();

        $caminhoScript = storage_path('app/scripts/gerar_mapa.py');
        
        $comando = "python3 {$caminhoScript} '{$dados['nome']}' '{$dados['data_nascimento']}' '{$dados['hora_nascimento']}' '{$dados['latitude']}' '{$dados['longitude']}'";

        $processo = \Illuminate\Support\Facades\Process::run($comando);

        if (!$processo->successful()) {
            return response()->json([
                'erro' => 'O script Python falhou ao executar.', 
                'detalhe' => $processo->errorOutput()
            ], 500);
        }

        $dadosAstrologicosReais = json_decode(trim($processo->output()), true);

        if (!isset($dadosAstrologicosReais['sucesso']) || !$dadosAstrologicosReais['sucesso']) {
             return response()->json([
                 'erro' => 'Erro interno no cálculo astronômico.', 
                 'detalhe' => $dadosAstrologicosReais['erro'] ?? 'Erro desconhecido.'
            ], 500);
        }

        $resultado = $this->aiService->gerarMapaAstral($dados, $dadosAstrologicosReais);

        $leituraSalva = \App\Models\Leitura::create([
            'nome_consulente'   => $dados['nome'],
            'pergunta'          => 'Leitura de Mapa Astral Natal', 
            'dados_mapa'        => $dadosAstrologicosReais, 
            'resultado_leitura' => $resultado 
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id_leitura' => $leituraSalva->id,
                'consulente' => $leituraSalva->nome_consulente,
                'signos_calculados' => [
                    'sol' => $dadosAstrologicosReais['planetas']['sol']['signo'],
                    'lua' => $dadosAstrologicosReais['planetas']['lua']['signo'],
                    'ascendente' => $dadosAstrologicosReais['pontos_importantes']['ascendente']
                ],
                'leitura' => $leituraSalva->resultado_leitura
            ]
        ], 200);
    }

    public function downloadPdf($id)
    {
        $leitura = \App\Models\Leitura::findOrFail($id);

        if ($leitura->pergunta === 'Leitura de Mapa Astral Natal') {
            $nomeView = 'pdf.mapa-astral';
            $nomeArquivo = 'mapa-astral-';
        } else {
            $nomeView = 'pdf.horaria';
            $nomeArquivo = 'leitura-horaria-';
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($nomeView, ['leitura' => $leitura]);

        return $pdf->download($nomeArquivo . $leitura->nome_consulente . '.pdf');
    }
}