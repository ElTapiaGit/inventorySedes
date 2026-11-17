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
        Schema::create('componente', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id_compo', true);
            $table->string('nombre_compo', 50)->nullable();
            $table->text('descripcion_compo')->nullable();
            $table->string('estado_compo', 45)->nullable();
            $table->string('EQUIPO_cod_equipo', 50);

            $table->unique('id_compo', 'id_compo_UNIQUE');
            $table->index('EQUIPO_cod_equipo', 'fk_COMPONENTE_EQUIPO1_idx');
            $table->index('nombre_compo', 'nombre_comp');

            $table->foreign('EQUIPO_cod_equipo', 'fk_COMPONENTE_EQUIPO1')
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
        Schema::dropIfExists('componente');
    }
};