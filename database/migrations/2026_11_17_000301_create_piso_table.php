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
        Schema::create('piso', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id_piso', true);
            $table->string('numero_piso', 50)->nullable();
            $table->integer('EDIFICIO_id_edificio');

            // indexs
            $table->unique('id_piso', 'id_piso_UNIQUE');
            $table->index('EDIFICIO_id_edificio', 'fk_PISO_EDIFICIO1_idx');

            // relacion foranea
            $table->foreign('EDIFICIO_id_edificio', 'fk_PISO_EDIFICIO1')
                ->references('id_edificio')->on('edificio')
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
        Schema::dropIfExists('piso');
    }
};