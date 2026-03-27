<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('leads')) {
            Schema::create('leads', function (Blueprint $col) {
                $col->id();
                $col->string('tipo'); // paciente ou medico
                $col->string('nome');
                $col->string('telefone');
                $col->string('email')->nullable();
                $col->string('cpf')->nullable();
                $col->string('crm')->nullable();
                $col->string('especialidade')->nullable();
                $col->string('turno')->nullable();
                $col->string('convenio')->nullable();
                $col->string('status')->default('novo');
                $col->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leads');
    }
}
