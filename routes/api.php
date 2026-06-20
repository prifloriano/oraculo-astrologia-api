<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AstrologiaController;

Route::post('/v1/astrologia/horaria', [AstrologiaController::class, 'calcular']);
Route::post('/v1/astrologia/mapa-astral', [AstrologiaController::class, 'gerarMapa']); 
Route::get('/v1/astrologia/download/{id}', [AstrologiaController::class, 'downloadPdf']);