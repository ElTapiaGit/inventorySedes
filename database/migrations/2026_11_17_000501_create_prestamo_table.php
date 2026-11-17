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
        Schema::create('prestamo', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id_prestamo', true);
            $table->string('nombre_solicitante', 50)->nullable();
            $table->text('descripcion_prestamo')->nullable();
            $table->date('fch_prestamo')->nullable();
            $table->time('hora_prestamo')->nullable();
            $table->integer('PERSONAL_id_personal');

            $table->unique('id_prestamo', 'id_prestamo_UNIQUE');
            $table->index('PERSONAL_id_personal', 'fk_PRESTAMOS_PERSONAL1_idx');

            // relacion foranea
            $table->foreign('PERSONAL_id_personal', 'fk_PRESTAMOS_PERSONAL1')
                ->references('id_personal')->on('personal')
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
        Schema::dropIfExists('prestamo');
    }
};