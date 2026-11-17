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
        Schema::create('usuario', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id_usuario', true);
            $table->string('nombre', 45)->nullable();
            $table->string('apellidos', 45)->nullable();
            $table->string('celular', 15)->nullable();
            $table->integer('TIPO_USUARIO_id_tipo_usu');

            // index
            $table->unique('id_usuario', 'id_usuario_UNIQUE');
            $table->index('TIPO_USUARIO_id_tipo_usu', 'fk_USUARIO_TIPO_USUARIO1_idx');

            // relacion foranea
            $table->foreign('TIPO_USUARIO_id_tipo_usu', 'fk_USUARIO_TIPO_USUARIO1')
                ->references('id_tipo_usu')->on('tipo_usuario')
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
        Schema::dropIfExists('usuario');
    }
};