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
        Schema::create('personal', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id_personal', true);
            $table->string('ap_paterno', 15)->nullable();
            $table->string('ap_materno', 15)->nullable();
            $table->string('nombre', 20)->nullable();
            $table->string('celular', 15)->nullable();
            $table->tinyInteger('estado')->nullable();
            $table->integer('TIPO_PERSONAL_id_tipo_per');
            $table->integer('EDIFICIO_id_edificio');

            // indexs
            $table->unique('id_personal', 'id_personal_UNIQUE');
            $table->index('TIPO_PERSONAL_id_tipo_per', 'fk_PERSONAL_TIPO_PERSONAL1_idx');
            $table->index('EDIFICIO_id_edificio', 'fk_PERSONAL_EDIFICIO1_idx');
            $table->index('ap_paterno', 'ap_paterno');

            // relaciones foranea
            $table->foreign('EDIFICIO_id_edificio', 'fk_PERSONAL_EDIFICIO1')
                ->references('id_edificio')->on('edificio')
                ->onDelete('no action')->onUpdate('no action');

            $table->foreign('TIPO_PERSONAL_id_tipo_per', 'fk_PERSONAL_TIPO_PERSONAL1')
                ->references('id_tipo_per')->on('tipo_personal')
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
        Schema::dropIfExists('personal');
    }
};