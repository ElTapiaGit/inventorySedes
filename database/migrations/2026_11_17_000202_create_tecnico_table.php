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
        Schema::create('tecnico', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id_tecnico', true);
            $table->string('nombre', 60)->nullable();
            $table->string('ap_paterno', 60)->nullable();
            $table->string('ap_materno', 60)->nullable();
            $table->string('celular', 20)->nullable();
            $table->string('direccion', 150)->nullable();

            // indexs
            $table->unique('id_tecnico', 'id_tecnico_UNIQUE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tecnico');
    }
};