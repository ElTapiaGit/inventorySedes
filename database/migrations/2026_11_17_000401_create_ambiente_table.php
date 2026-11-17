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
        Schema::create('ambiente', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id_ambiente', true);
            $table->string('nombre', 45)->nullable();
            $table->text('descripcion_amb')->nullable();
            $table->integer('TIPO_AMBIENTE_id_ambiente');
            $table->integer('PISO_id_piso');

            // indexs
            $table->unique('id_ambiente', 'id_ambiente_UNIQUE');
            $table->index('TIPO_AMBIENTE_id_ambiente', 'fk_AMBIENTE_TIPO_AMBIENTE1_idx');
            $table->index('PISO_id_piso', 'fk_AMBIENTE_PISO1_idx');

            // relaciones foraneas
            $table->foreign('PISO_id_piso', 'fk_AMBIENTE_PISO1')
                ->references('id_piso')->on('piso')
                ->onDelete('no action')->onUpdate('no action');

            $table->foreign('TIPO_AMBIENTE_id_ambiente', 'fk_AMBIENTE_TIPO_AMBIENTE1')
                ->references('id_tipoambiente')->on('tipo_ambiente')
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
        Schema::dropIfExists('ambiente');
    }
};