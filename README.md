# 🌌 Oráculo API — Gerador de Mapas Astrais & IA

> **Um motor de precisão astrológica e interpretação sintética construído com Laravel 13, Python (Kerykeion) e Google Gemini AI.**

O **Oráculo** é uma API RESTful desenvolvida para calcular, interpretar e diagramar Mapas Astrais Natais de forma 100% automatizada. O sistema une a precisão matemática das efemérides suíças (via backend Python) com a capacidade de síntese e acolhimento da Inteligência Artificial Generativa.

O resultado final é um arquivo **PDF de alta resolução**, contendo a mandala astrológica tropical perfeitamente renderizada e uma leitura dividida em arquétipos psicológicos.

---

### 🏗️ Arquitetura (Como a mágica acontece)

1. O **Client** envia um `POST` com os dados de nascimento (Nome, Data, Hora e Coordenadas GPS).
2. O **Laravel 13** invoca um sub-processo rodando um script **Python**.
3. O **Python** (via `Kerykeion`) calcula as posições planetárias exatas, gera o vetor `.SVG` da mandala, aplica a correção de paleta de cores para evitar falhas de renderização, traduz os termos para o português e converte o gráfico para um `.PNG` limpo em Base64.
4. O **Laravel** recebe os dados matemáticos brutos e dispara um prompt super-estruturado para a **Google Gemini AI** via `InterpretationService`.
5. A IA devolve um JSON formatado respondendo pela persona de um astrólogo profissional.
6. A view **Blade** une a mandala em base64 com a leitura da IA e o **DomPDF** entrega o documento final diagramado com quebras de página controladas.

---

### 🛠️ Tecnologias e Bibliotecas

#### Backend (PHP)
* **Framework:** Laravel 13 (PHP 8.2+)
* **Geração de PDF:** `barryvdh/laravel-dompdf`
* **Client HTTP:** `Illuminate\Support\Facades\Http` (Nativo)

#### Motor Astrológico (Python)
* **Interpretador:** Python 3.10+
* **`kerykeion`**: Biblioteca de astrologia baseada no motor suíço *Swiss Ephemeris* (Padrão ouro da astrologia mundial).
* **`cairosvg`**: Motor de renderização que converts a mandala SVG em um PNG de alta fidelidade, tratando as variáveis CSS nativas.

#### Inteligência Artificial
* **Modelo:** Google Gemini (`gemini-2.5-flash`)

---

### 📋 Pré-requisitos de Infraestrutura

Antes de rodar o projeto, seu ambiente precisará de:

1. **PHP 8.2+** e **Composer**
2. **Python 3.10+** e **Pip**
3. **Biblioteca gráfica Cairo** instalada no Sistema Operacional (exigência do `cairosvg`):
   * *Ubuntu/Debian:* `sudo apt-get install libcairo2-dev`
   * *MacOS:* `brew install cairo`
   * *Windows:* Recomendável rodar via **WSL2** (Linux Subsystem).
4. **Postgres Database**


---
