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
        Schema::create('inicio_mantenimiento', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id_mantenimiento_ini', true);
            $table->text('informe_inicial')->nullable();
            $table->date('fch_inicio')->nullable();
            $table->integer('TECNICO_id_tecnico');
            $table->integer('PERSONAL_id_personal');

            $table->unique('id_mantenimiento_ini', 'id_mantenimiento_ini_UNIQUE');
            $table->index('TECNICO_id_tecnico', 'fk_INICIO_MANTENIMIENTO_TECNICO1_idx');
            $table->index('PERSONAL_id_personal', 'fk_INICIO_MANTENIMIENTO_PERSONAL1_idx');

            $table->foreign('PERSONAL_id_personal', 'fk_INICIO_MANTENIMIENTO_PERSONAL1')
                ->references('id_personal')->on('personal')
                ->onDelete('no action')->onUpdate('no action');

            $table->foreign('TECNICO_id_tecnico', 'fk_INICIO_MANTENIMIENTO_TECNICO1')
                ->references('id_tecnico')->on('tecnico')
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
        Schema::dropIfExists('inicio_mantenimiento');
    }
};