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
        Schema::create('descarte', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id_descarte', true);
            $table->string('codigo', 50)->nullable();
            $table->string('nombre', 60)->nullable();
            $table->text('descrpcion_descarte')->nullable();
            $table->string('orden_desacarte', 50)->nullable();
            $table->date('fch_descarte')->nullable();
            $table->integer('PERSONAL_id_personal');

            $table->unique('id_descarte', 'id_descarte_UNIQUE');
            $table->unique('codigo', 'cod_equipo_UNIQUE');
            $table->index('PERSONAL_id_personal', 'fk_DESCARTE_PERSONAL1_idx');

            $table->foreign('PERSONAL_id_personal', 'fk_DESCARTE_PERSONAL1')
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
        Schema::dropIfExists('descarte');
    }
};