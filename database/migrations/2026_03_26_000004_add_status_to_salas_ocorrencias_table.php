<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToSalasOcorrenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('salas_ocorrencias', function (Blueprint $table) {
            if (!Schema::hasColumn('salas_ocorrencias', 'status')) {
                $table->string('status')->default('confirmado')->after('transacao_id');
            }
            if (!Schema::hasColumn('salas_ocorrencias', 'google_event_id')) {
                $table->string('google_event_id')->nullable()->after('status');
            }
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
            $table->dropColumn(['status', 'google_event_id']);
        });
    }
}
