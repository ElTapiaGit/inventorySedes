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
        Schema::create('edificio', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id_edificio', true);
            $table->string('nombre_edi', 45)->nullable();
            $table->string('direccion', 80)->nullable();
            $table->integer('SEDE_id_sede');

            // indexs
            $table->unique('id_edificio', 'id_edificio_UNIQUE');
            $table->index('SEDE_id_sede', 'fk_EDIFICIO_SEDE_idx');

            //relacion foranea
            $table->foreign('SEDE_id_sede', 'fk_EDIFICIO_SEDE')
                ->references('id_sede')->on('sede')
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
        Schema::dropIfExists('edificio');
    }
};