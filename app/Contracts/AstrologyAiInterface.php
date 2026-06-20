<?php

namespace App\Contracts;

interface AstrologyAiInterface
{
    /**
     * Gera a leitura horária baseada nos dados do consulente e posições astrológicas.
     */
    public function gerarLeituraHoraria(array $dadosConsulente, array $dadosAstrologicos): array;
    public function gerarMapaAstral(array $dadosConsulente, array $dadosAstrologicos): array;
}