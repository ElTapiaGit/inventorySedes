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
        Schema::create('detalles_mantenimiento', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id_mante', true);
            $table->string('cod_articulo', 50)->nullable();
            $table->integer('INICIO_MANTENIMIENTO_id_mantenimiento_ini');

            $table->unique('id_mante', 'id_mante_UNIQUE');
            $table->unique('cod_articulo', 'cod_equipo_UNIQUE');
            $table->index('INICIO_MANTENIMIENTO_id_mantenimiento_ini', 'fk_DETALLES_MANTENIMIENTO_INICIO_MANTENIMIENTO1_idx');

            $table->foreign('INICIO_MANTENIMIENTO_id_mantenimiento_ini', 'fk_DETALLES_MANTENIMIENTO_INICIO_MANTENIMIENTO1')
                ->references('id_mantenimiento_ini')->on('inicio_mantenimiento')
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
        Schema::dropIfExists('detalles_mantenimiento');
    }
};