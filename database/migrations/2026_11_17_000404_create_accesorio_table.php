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
        Schema::create('accesorio', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->string('cod_accesorio', 50)->primary();
            $table->string('nombre_acce', 80)->nullable();
            $table->text('descripcion_acce')->nullable();
            $table->text('observacion_ace')->nullable();
            $table->string('estado_acce', 45)->nullable();
            $table->string('vida_util', 45)->nullable();
            $table->string('ubicacion', 120)->nullable();
            $table->date('fch_registro_acce')->nullable();
            $table->integer('FOTO_id_foto');

            // indexs
            //$table->unique('cod_accesorio', 'cod_accesorio_UNIQUE');
            $table->index('FOTO_id_foto', 'fk_ACCESORIO_FOTO1_idx');
            $table->index('nombre_acce', 'nombre_acce');

            // relaciones foraneas
            $table->foreign('FOTO_id_foto', 'fk_ACCESORIO_FOTO1')
                ->references('id_foto')->on('foto')
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
        Schema::dropIfExists('accesorio');
    }
};