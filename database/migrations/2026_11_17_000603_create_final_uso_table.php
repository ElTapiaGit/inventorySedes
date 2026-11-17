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
        Schema::create('final_uso', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id_uso_fin', true);
            $table->date('fch_fin')->nullable();
            $table->time('hora_fin')->nullable();
            $table->integer('USO_AMBIENTE_id_uso_ambiente');
            $table->integer('PERSONAL_id_personal');

            $table->unique('id_uso_fin', 'id_uso_fin_UNIQUE');
            $table->index('USO_AMBIENTE_id_uso_ambiente', 'fk_FINAL_USO_USO_AMBIENTE1_idx');
            $table->index('PERSONAL_id_personal', 'fk_FINAL_USO_PERSONAL1_idx');

            $table->foreign('PERSONAL_id_personal', 'fk_FINAL_USO_PERSONAL1')
                ->references('id_personal')->on('personal')
                ->onDelete('no action')->onUpdate('no action');

            $table->foreign('USO_AMBIENTE_id_uso_ambiente', 'fk_FINAL_USO_USO_AMBIENTE1')
                ->references('id_uso_ambiente')->on('uso_ambiente')
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
        Schema::dropIfExists('final_uso');
    }
};