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
        Schema::create('contenido_vitrina', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id_cont_vitrina', true);
            $table->string('MOBILIARIO_cod_mueble', 50);
            $table->string('cod_equipo', 50)->nullable();
            $table->string('seccion_vitrina', 45)->nullable();

            $table->unique('id_cont_vitrina', 'id_cont_vitrina_UNIQUE');
            $table->unique('cod_equipo', 'cod_equipo_UNIQUE');
            $table->index('MOBILIARIO_cod_mueble', 'fk_CONTENIDO_VITRINA_MOBILIARIO1_idx');

            $table->foreign('MOBILIARIO_cod_mueble', 'fk_CONTENIDO_VITRINA_MOBILIARIO1')
                ->references('cod_mueble')->on('mobiliario')
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
        Schema::dropIfExists('contenido_vitrina');
    }
};