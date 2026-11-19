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
        Schema::create('equipo', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->string('cod_equipo', 50)->primary();
            $table->string('nombre_equi', 100)->nullable();
            $table->string('marca', 60)->nullable();
            $table->string('modelo', 50)->nullable();
            $table->text('descripcion_equi')->nullable();
            $table->tinyInteger('empotrado')->nullable();
            $table->string('estado_equi', 45)->nullable();
            $table->text('observaciones_equi')->nullable();
            $table->string('vida_util', 50)->nullable();
            $table->date('fch_registro')->nullable();
            $table->integer('AMBIENTE_id_ambiente');
            $table->integer('FOTO_id_foto');

            // indexs
            $table->index('AMBIENTE_id_ambiente', 'fk_EQUIPO_AMBIENTE1_idx');
            $table->index('FOTO_id_foto', 'fk_EQUIPO_FOTO1_idx');

            // relaciones foraneas
            $table->foreign('AMBIENTE_id_ambiente', 'fk_EQUIPO_AMBIENTE1')
                ->references('id_ambiente')->on('ambiente')
                ->onDelete('no action')->onUpdate('no action');

            $table->foreign('FOTO_id_foto', 'fk_EQUIPO_FOTO1')
                ->references('id_foto')->on('foto')
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
        Schema::dropIfExists('equipo');
    }
};