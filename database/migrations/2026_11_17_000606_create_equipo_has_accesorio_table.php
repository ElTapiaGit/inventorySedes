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
        Schema::create('equipo_has_accesorio', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->string('EQUIPO_cod_equipo', 50);
            $table->string('ACCESORIO_cod_accesorio', 50);

            $table->primary(['EQUIPO_cod_equipo', 'ACCESORIO_cod_accesorio']);

            $table->index('ACCESORIO_cod_accesorio', 'fk_EQUIPO_has_ACCESORIO_ACCESORIO1_idx');
            $table->index('EQUIPO_cod_equipo', 'fk_EQUIPO_has_ACCESORIO_EQUIPO1_idx');

            $table->foreign('ACCESORIO_cod_accesorio', 'fk_EQUIPO_has_ACCESORIO_ACCESORIO1')
                ->references('cod_accesorio')->on('accesorio')
                ->onDelete('no action')->onUpdate('no action');

            $table->foreign('EQUIPO_cod_equipo', 'fk_EQUIPO_has_ACCESORIO_EQUIPO1')
                ->references('cod_equipo')->on('equipo')
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
        Schema::dropIfExists('equipo_has_accesorio');
    }
};