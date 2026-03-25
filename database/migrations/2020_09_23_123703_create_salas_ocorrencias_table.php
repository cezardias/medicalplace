<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalasOcorrenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salas_ocorrencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sala_id');
            $table->foreignId('user_id');
            $table->date('data')->nullable();
            $table->time('hora')->nullable();
            $table->string('tipo')->nullable(); // Consulta || Manutenção
            $table->text('comentario')->nullable();
            $table->foreignId('transacao_id')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salas_ocorrencias');
    }
}
