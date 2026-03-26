<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servicos', function (Blueprint $col) {
            $col->id();
            $col->unsignedBigInteger('especialidade_id');
            $col->string('nome');
            $col->decimal('preco', 10, 2);
            $col->string('status')->default('ativa');
            $col->timestamps();

            $col->foreign('especialidade_id')->references('id')->on('especialidades')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('servicos');
    }
}
