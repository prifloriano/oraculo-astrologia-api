import sys
import json
import base64
import os
import re
import tempfile
import contextlib

try:
    nome = sys.argv[1]
    data_nascimento = sys.argv[2]
    hora_nascimento = sys.argv[3]
    lat = float(sys.argv[4])
    lng = float(sys.argv[5])

    ano, mes, dia = data_nascimento.split('-')
    hora, minuto = hora_nascimento.split(':')

    pasta_temp = tempfile.gettempdir()

    with contextlib.redirect_stdout(open(os.devnull, 'w')):
        from kerykeion import AstrologicalSubject, KerykeionChartSVG
        import cairosvg
        
        consulente = AstrologicalSubject(
            nome, 
            int(ano), int(mes), int(dia), int(hora), int(minuto), 
            lat=lat, lng=lng, tz_str="America/Sao_Paulo", city="Curitiba", nation="BR", 
            online=False
        )

        chart = KerykeionChartSVG(consulente, chart_type="Natal", new_output_directory=pasta_temp)
        chart.makeSVG()

    caminho_svg = os.path.join(pasta_temp, f"{nome} - Natal Chart.svg")
    caminho_svg_corrigido = os.path.join(pasta_temp, "corrigido.svg")
    caminho_png = os.path.join(pasta_temp, f"{nome} - mandala.png")

    with open(caminho_svg, 'r', encoding='utf-8') as f:
        svg_data = f.read()

    # =========================================================================
    # O PARSER INTELIGENTE DE CORES 
    # Lê a paleta oficial do Kerykeion e injeta as cores reais nos gráficos
    # =========================================================================
    root_match = re.search(r':root\s*\{([^}]+)\}', svg_data)
    if root_match:
        definicoes = root_match.group(1)
        # Captura todos os pares do tipo "--planeta-sol: #f39c12"
        pares = re.findall(r'(--[a-zA-Z0-9_-]+)\s*:\s*([^;}]+)', definicoes)
        
        for nome_var, valor_cor in pares:
            valor_cor = valor_cor.strip()
            
            # Se for cor de fundo, a gente obriga a ser branco puro
            if nome_var in ['--paper-color', '--zodiac-bg', '--zodiac-ring-bg']:
                valor_cor = '#ffffff'
                
            svg_data = svg_data.replace(f'var({nome_var})', valor_cor)

    # Segunda camada de segurança para garantir que as linhas apareçam
    fallbacks = {
        'var(--line-color)': '#333333',
        'var(--text-color)': '#111111',
        'var(--paper-color)': '#ffffff',
        'var(--zodiac-bg)': '#ffffff'
    }
    for var_css, cor_hex in fallbacks.items():
        svg_data = svg_data.replace(var_css, cor_hex)
    # =========================================================================

    # Traduções pro Português
    traducoes = {
        "Elements:": "Elementos:",
        "Fire": "Fogo",
        "Earth": "Terra",
        "Air": "Ar",
        "Water": "Água",
        "Qualities:": "Qualidades:",
        "Cardinal": "Cardeal",
        "Fixed": "Fixo",
        "Mutable": "Mutável",
        "Zodiac: Tropical": "Zodíaco: Tropical",
        "Domification: Placidus": "Casas: Placidus",
        "Lunation Day:": "Dia Lunar:",
        "Lunar phase:": "Fase Lunar:",
        "Waning Gibbous": "Minguante",
        "Perspective: Apparent Geocentric": "Perspectiva: Geocêntrica",
        "Location:": "Local:",
        "Greenwich, GB": "Curitiba, BR",
        "Day of Week:": "Dia da Semana:",
        "Thursday": "Quinta-feira",
        "Birth Chart": "Mapa Astral"
    }

    for eng, pt in traducoes.items():
        svg_data = svg_data.replace(eng, pt)
    
    with open(caminho_svg_corrigido, 'w', encoding='utf-8') as f:
        f.write(svg_data)

    # Gera o PNG
    cairosvg.svg2png(url=caminho_svg_corrigido, write_to=caminho_png, background_color="#ffffff")

    with open(caminho_png, "rb") as imagem:
        mandala_b64 = base64.b64encode(imagem.read()).decode('utf-8')

    os.remove(caminho_svg)
    os.remove(caminho_svg_corrigido)
    os.remove(caminho_png)

    resultado = {
        "sucesso": True,
        "mandala_base64": mandala_b64,
        "planetas": {
            "sol": {"signo": consulente.sun.sign, "casa": consulente.sun.house, "grau": round(consulente.sun.position, 2)},
            "lua": {"signo": consulente.moon.sign, "casa": consulente.moon.house, "grau": round(consulente.moon.position, 2)},
            "ascendente": {"signo": consulente.first_house.sign}
        },
        "pontos_importantes": {
            "ascendente": consulente.first_house.sign
        }
    }
    print(json.dumps(resultado))

except Exception as e:
    import traceback
    print(json.dumps({"sucesso": False, "erro": str(e), "trace": traceback.format_exc()}))