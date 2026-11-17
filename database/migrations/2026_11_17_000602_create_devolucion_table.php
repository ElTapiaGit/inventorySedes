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
        Schema::create('devolucion', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id_devolucion', true);
            $table->date('fch_devolucion')->nullable();
            $table->time('hora_devolucion')->nullable();
            $table->text('descripcion_devolucion')->nullable();
            $table->integer('PRESTAMO_id_prestamo');
            $table->integer('PERSONAL_id_personal');

            $table->unique('id_devolucion', 'id_devolucion_UNIQUE');
            $table->index('PRESTAMO_id_prestamo', 'fk_DEVOLUCION_PRESTAMO1_idx');
            $table->index('PERSONAL_id_personal', 'fk_DEVOLUCION_PERSONAL1_idx');

            $table->foreign('PERSONAL_id_personal', 'fk_DEVOLUCION_PERSONAL1')
                ->references('id_personal')->on('personal')
                ->onDelete('no action')->onUpdate('no action');

            $table->foreign('PRESTAMO_id_prestamo', 'fk_DEVOLUCION_PRESTAMO1')
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
        Schema::dropIfExists('devolucion');
    }
};