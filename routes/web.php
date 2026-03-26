<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
#Route::get('/', function () { return view('welcome'); });

Auth::routes();

Route::match(['get','post'],'/','PublicController@index')->name('home');
Route::match(['get','post'],'salas/{slug}','PublicController@verSala')->name('ver_sala')->middleware('medico');
Route::get('/direct-user', 'HomeController@directUser')->name('direct.user');
Route::post('pre_checkout-agendamento','PublicController@preCheckoutAgendamento')->name('pre_checkout_agendamento');
Route::post('checkout-agendamento','PublicController@checkoutAgendamento')->name('checkout_agendamento')->middleware('medico');
Route::post('checkout-pagamento','PublicController@checkoutPagamento')->name('checkout_pagamento')->middleware('medico');
Route::match(['get','post'],'minha-conta','PublicController@contaMedico')->name('minha_conta')->middleware('medico');
Route::post('cancelar-reserva','PublicController@cancelarReserva')->name('cancelar_reserva')->middleware('medico');
Route::match(['get','post'],'cadastre-se','PublicController@cadastroNovoMedico')->name('cadastro_novo_medico');

Route::get('/test-send-email','PublicController@testSendEmail')->name('testSendEmail');

Route::prefix('admin')->group(function () {
    Route::match(['get','post'],'/','AdminController@index')->name('admin.index')->middleware('administrador');
    Route::match(['get','post'],'agenda','AdminController@agenda')->name('admin.agenda')->middleware('administrador');
    Route::match(['get','post'],'financeiro','AdminController@financeiro')->name('admin.financeiro')->middleware('administrador');
    Route::match(['get','post'],'uso-sala','AdminController@usoSala')->name('admin.uso_sala')->middleware('administrador');
    Route::match(['get','post'],'medicos-cadastrados','AdminController@MedicosCadastrados')->name('admin.medicos_cadastrados')->middleware('administrador');
    Route::match(['get','post'],'faturamento','AdminController@faturamento')->name('admin.faturamento')->middleware('administrador');
    Route::match(['get','post'],'agendamento','AdminController@agendamento')->name('admin.agendamento')->middleware('administrador');
    Route::match(['get','post'],'cadastra-ocorrencia','AdminController@cadastraOcorrencia')->name('admin.cadastra_ocorrencia')->middleware('administrador');

    Route::match(['get','post'],'venda-creditos','AdminController@vendaCreditos')->name('admin.venda_creditos')->middleware('administrador');
    Route::match(['get','post'],'saldo-creditos','AdminController@saldoCreditos')->name('admin.saldo_creditos')->middleware('administrador');

    Route::match(['get','post'],'venda-credito','AdminController@vendaCredito')->name('admin.venda_credito')->middleware('administrador');
    Route::match(['post'],'checkout-venda-credito','AdminController@checkoutVendaCredito')->name('admin.checkout_venda_credito')->middleware('administrador');
    Route::match(['post'],'admin-checkout-agendamento','AdminController@checkoutAgendamento')->name('admin.checkout_agendamento')->middleware('administrador');
    
    Route::resource('salas', 'SalasController')->middleware('administrador');
    Route::match(['post'],'apagar-imagem-sala','SalasController@apagarImagemSala')->name('admin.apagar_imagem_sala')->middleware('administrador');

    Route::resource('usuario', 'UserController')->middleware('administrador');
    #Route::resource('agendamento', 'AgendamentoController')->middleware('administrador');

    Route::get('api-config', 'Admin\ApiController@index')->name('admin.api.index')->middleware('administrador');
    Route::post('api-config/token', 'Admin\ApiController@generateToken')->name('admin.token.generate')->middleware('administrador');
    Route::delete('api-config/token/{id}', 'Admin\ApiController@revokeToken')->name('admin.token.revoke')->middleware('administrador');
    Route::post('api-config/webhook', 'Admin\ApiController@storeWebhook')->name('admin.webhook.store')->middleware('administrador');
    Route::delete('api-config/webhook/{id}', 'Admin\ApiController@deleteWebhook')->name('admin.webhook.delete')->middleware('administrador');
    Route::post('api-config/webhook/{id}/test', 'Admin\ApiController@testWebhook')->name('admin.webhook.test')->middleware('administrador');


});
// Medico

// Secretaria

// Administrador

