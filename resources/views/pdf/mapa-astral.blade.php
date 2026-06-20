<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Mapa Astral - {{ $leitura->nome_consulente ?? 'Consulente' }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; line-height: 1.6; margin: 0; padding: 20px; }
        .page-break { page-break-after: always; }
        .center { text-align: center; }
        .mandala-img { width: 450px; background-color: #ffffff; margin-top: 30px; }
        .section { margin-bottom: 25px; page-break-inside: avoid; }
        .section h2 { color: #2980b9; font-size: 18px; border-bottom: 1px solid #ecf0f1; padding-bottom: 5px; }
        .destaque { font-style: italic; color: #d35400; font-weight: bold; margin-top: 10px; display: block; }
        .desafios ul { padding-left: 20px; }
        .desafios li { margin-bottom: 10px; }
    </style>
</head>
<body>

    <div class="center">
        <h1 style="color: #2c3e50; margin-bottom: 5px;">Mapa Astral Natal</h1>
        <p style="color: #7f8c8d; font-size: 14px;">Preparado especialmente para <strong>{{ $leitura->nome_consulente ?? 'Consulente' }}</strong></p>
        
        <img src="data:image/png;base64,{{ $leitura->dados_mapa['mandala_base64'] ?? '' }}" class="mandala-img">
    </div>

    <div class="page-break"></div>

    <div class="section">
        <p>{{ $leitura->resultado_leitura['introducao'] ?? 'Introdução não gerada pela Inteligência Artificial neste mapa.' }}</p>
    </div>

    <div class="section">
        <h2>{{ $leitura->resultado_leitura['essencia_solar']['titulo'] ?? 'A Essência Solar' }}</h2>
        <p>{{ $leitura->resultado_leitura['essencia_solar']['texto'] ?? '' }}</p>
        <span class="destaque">"{{ $leitura->resultado_leitura['essencia_solar']['frase_destaque'] ?? '' }}"</span>
    </div>

    <div class="section">
        <h2>{{ $leitura->resultado_leitura['refugio_lunar']['titulo'] ?? 'O Refúgio Lunar' }}</h2>
        <p>{{ $leitura->resultado_leitura['refugio_lunar']['texto'] ?? '' }}</p>
        <span class="destaque">"{{ $leitura->resultado_leitura['refugio_lunar']['frase_destaque'] ?? '' }}"</span>
    </div>

    <div class="section">
        <h2>{{ $leitura->resultado_leitura['ascendente']['titulo'] ?? 'A Máscara Social' }}</h2>
        <p>{{ $leitura->resultado_leitura['ascendente']['texto'] ?? '' }}</p>
        <span class="destaque">"{{ $leitura->resultado_leitura['ascendente']['frase_destaque'] ?? '' }}"</span>
    </div>

    <div class="section desafios">
        <h2>Pontos de Atenção e Evolução</h2>
        <ul>
            @if(isset($leitura->resultado_leitura['desafios']) && is_array($leitura->resultado_leitura['desafios']))
                @foreach($leitura->resultado_leitura['desafios'] as $desafio)
                    <li>{{ $desafio }}</li>
                @endforeach
            @else
                <li>A leitura de desafios não está disponível para este mapa.</li>
            @endif
        </ul>
    </div>

</body>
</html>