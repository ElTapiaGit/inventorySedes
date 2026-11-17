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
        Schema::create('uso_ambiente', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id_uso_ambiente', true);
            $table->integer('USUARIO_id_usuario');
            $table->text('descripcion')->nullable();
            $table->string('semestre', 45)->nullable();
            $table->date('fch_uso')->nullable();
            $table->time('hora_uso')->nullable();
            $table->integer('AMBIENTE_id_ambiente');
            $table->integer('PERSONAL_id_personal');

            $table->unique('id_uso_ambiente', 'id_uso_ambiente_UNIQUE');
            $table->index('PERSONAL_id_personal', 'fk_USO_AMBIENTE_PERSONAL1_idx');
            $table->index('AMBIENTE_id_ambiente', 'fk_USO_AMBIENTE_AMBIENTE1_idx');
            $table->index('USUARIO_id_usuario', 'fk_USO_AMBIENTE_USUARIO1_idx');

            // relaciones foraneas
            $table->foreign('AMBIENTE_id_ambiente', 'fk_USO_AMBIENTE_AMBIENTE1')
                ->references('id_ambiente')->on('ambiente')
                ->onDelete('no action')->onUpdate('no action');

            $table->foreign('PERSONAL_id_personal', 'fk_USO_AMBIENTE_PERSONAL1')
                ->references('id_personal')->on('personal')
                ->onDelete('no action')->onUpdate('no action');

            $table->foreign('USUARIO_id_usuario', 'fk_USO_AMBIENTE_USUARIO1')
                ->references('id_usuario')->on('usuario')
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
        Schema::dropIfExists('uso_ambiente');
    }
};