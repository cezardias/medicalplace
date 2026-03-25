<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToSalasOcorrenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('salas_ocorrencias', function (Blueprint $table) {
            $table->index('sala_id');
            $table->index('user_id');
            $table->index('data');
            $table->index('hora');
            $table->index('tipo');
            $table->index('transacao_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('salas_ocorrencias', function (Blueprint $table) {
            $table->dropIndex(['sala_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['data']);
            $table->dropIndex(['hora']);
            $table->dropIndex(['tipo']);
            $table->dropIndex(['transacao_id']);
        });
    }
}
