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
        Schema::create('movimiento', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id_movimiento', true);
            $table->string('cod_equipo', 50)->nullable();
            $table->string('detalle', 80)->nullable();
            $table->date('fch_registro')->nullable();
            $table->integer('PERSONAL_id_personal');

            $table->unique('id_movimiento', 'id_movimiento_UNIQUE');
            $table->index('PERSONAL_id_personal', 'fk_MOVIMIENTO_PERSONAL1_idx');

            $table->foreign('PERSONAL_id_personal', 'fk_MOVIMIENTO_PERSONAL1')
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
        Schema::dropIfExists('movimiento');
    }
};