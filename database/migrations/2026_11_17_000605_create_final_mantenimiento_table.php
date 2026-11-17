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
        Schema::create('final_mantenimiento', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id_mante_final', true);
            $table->text('informe_final')->nullable();
            $table->date('fch_final')->nullable();
            $table->integer('INICIO_MANTENIMIENTO_id_mantenimiento_ini');

            $table->unique('id_mante_final', 'id_mante_final_UNIQUE');
            $table->index('INICIO_MANTENIMIENTO_id_mantenimiento_ini', 'fk_FINAL_MANTENIMIENTO_INICIO_MANTENIMIENTO1_idx');

            $table->foreign('INICIO_MANTENIMIENTO_id_mantenimiento_ini', 'fk_FINAL_MANTENIMIENTO_INICIO_MANTENIMIENTO1')
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
        Schema::dropIfExists('final_mantenimiento');
    }
};