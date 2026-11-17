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
        Schema::create('mobiliario', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->string('cod_mueble', 50)->primary();
            $table->text('descripticion_mueb')->nullable();
            $table->date('fch_registro')->nullable();
            $table->string('estado_mueb', 45)->nullable();
            $table->text('observacion')->nullable();
            $table->string('vida_util', 30)->nullable();
            $table->integer('TIPO_MOBILIARIO_id_tipo_mueb');
            $table->integer('AMBIENTE_id_ambiente');
            $table->integer('FOTO_id_foto');

            // indexs
            //$table->unique('cod_mueble', 'cod_mueble_UNIQUE');
            $table->index('AMBIENTE_id_ambiente', 'fk_MOBILIARIO_AMBIENTE1_idx');
            $table->index('TIPO_MOBILIARIO_id_tipo_mueb', 'fk_MOBILIARIO_TIPO_MOBILIARIO1_idx');
            $table->index('FOTO_id_foto', 'fk_MOBILIARIO_FOTO1_idx');

            // relaciones foraneas
            $table->foreign('AMBIENTE_id_ambiente', 'fk_MOBILIARIO_AMBIENTE1')
                ->references('id_ambiente')->on('ambiente')
                ->onDelete('no action')->onUpdate('no action');

            $table->foreign('FOTO_id_foto', 'fk_MOBILIARIO_FOTO1')
                ->references('id_foto')->on('foto')
                ->onDelete('no action')->onUpdate('no action');

            $table->foreign('TIPO_MOBILIARIO_id_tipo_mueb', 'fk_MOBILIARIO_TIPO_MOBILIARIO1')
                ->references('id_tipo_mueb')->on('tipo_mobiliario')
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
        Schema::dropIfExists('mobiliario');
    }
};