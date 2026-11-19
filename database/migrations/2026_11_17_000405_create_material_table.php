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
        Schema::create('material', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->string('cod_mate', 50)->primary();
            $table->string('tipo_mate', 50)->nullable();
            $table->text('descripcion_mate')->nullable();
            $table->string('estado_mate', 45)->nullable();
            $table->text('observacion_mate')->nullable();
            $table->date('fch_registrada')->nullable();
            $table->integer('AMBIENTE_id_ambiente');
            $table->integer('FOTO_id_foto');

            //indexs
            //$table->unique('cod_mate', 'cod_mate_UNIQUE');
            $table->index('AMBIENTE_id_ambiente', 'fk_MATERIAL_AMBIENTE1_idx');
            $table->index('FOTO_id_foto', 'fk_MATERIAL_FOTO1_idx');

            // relaciones foraneas
            $table->foreign('AMBIENTE_id_ambiente', 'fk_MATERIAL_AMBIENTE1')
                ->references('id_ambiente')->on('ambiente')
                ->onDelete('no action')->onUpdate('no action');

            $table->foreign('FOTO_id_foto', 'fk_MATERIAL_FOTO1')
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
        Schema::dropIfExists('material');
    }
};