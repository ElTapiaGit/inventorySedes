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
        Schema::create('detalle_prestamo', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id_detalle_pres', true);
            $table->string('cod_articulo', 50)->nullable();
            $table->text('observacion_detalle')->nullable();
            $table->integer('PRESTAMO_id_prestamo');

            $table->unique('cod_articulo', 'cod_equipo_UNIQUE');
            $table->index('PRESTAMO_id_prestamo', 'fk_DETALLE_PRESTAMO_PRESTAMO1_idx');

            $table->foreign('PRESTAMO_id_prestamo', 'fk_DETALLE_PRESTAMO_PRESTAMO1')
                ->references('id_prestamo')->on('prestamo')
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
        Schema::dropIfExists('detalle_prestamo');
    }
};