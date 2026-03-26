<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::get('/salas', 'Api\PublicApiController@getSalas');
    Route::get('/agenda', 'Api\PublicApiController@getAgenda');

    // Módulo 1: Especialidades e Serviços
    Route::get('/especialidades', 'Api\V1\ApiController@getEspecialidades');
    Route::get('/servicos/{esp_id}', 'Api\V1\ApiController@getServicos');

    // Módulo 2: Leads
    Route::post('/leads/paciente', 'Api\V1\ApiController@postLeadPaciente');
    Route::post('/leads/medico', 'Api\V1\ApiController@postLeadMedico');

    // Módulo 3: Agenda e Disponibilidade
    Route::get('/disponibilidade', 'Api\V1\ApiController@getDisponibilidade');
    Route::post('/agendamento/reservar', 'Api\V1\ApiController@reservar');
    Route::patch('/agendamento/confirmar', 'Api\V1\ApiController@confirmar');

    // Módulo 4: Regras e Unidades
    Route::get('/regras/locacao', 'Api\V1\ApiController@getRegrasLocacao');
    Route::get('/unidades', 'Api\V1\ApiController@getUnidades');
});
