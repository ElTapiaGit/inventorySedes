<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historial_accesorio', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id_historial', true);
            $table->string('motivo_cambio', 80)->nullable();
            $table->date('fch_cambio')->nullable();
            $table->string('ACCESORIO_cod_accesorio', 50);

            $table->unique('id_historial', 'id_historial_UNIQUE');
            $table->index('ACCESORIO_cod_accesorio', 'fk_HISTORIAL_ACCESORIO_ACCESORIO1_idx');
            $table->index(['fch_cambio', 'ACCESORIO_cod_accesorio'], 'fch_cambio');

            $table->foreign('ACCESORIO_cod_accesorio', 'fk_HISTORIAL_ACCESORIO_ACCESORIO1')
                ->references('cod_accesorio')->on('accesorio')
                ->onDelete('no action')->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historial_accesorio');
    }
};