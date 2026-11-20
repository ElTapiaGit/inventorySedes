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
        Schema::create('personal_edificio', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id_personal_edificio', true);
            $table->integer('PERSONAL_id_personal');
            $table->integer('EDIFICIO_id_edificio');
            $table->tinyInteger('es_principal')->default(0);

            // Indices
            $table->unique(['PERSONAL_id_personal', 'EDIFICIO_id_edificio'], 'uq_personal_edificio');
            $table->index('PERSONAL_id_personal', 'fk_personal_edificio_personal_idx');
            $table->index('EDIFICIO_id_edificio', 'fk_personal_edificio_edificio_idx');

            // Relaciones foraneas
            $table->foreign('PERSONAL_id_personal', 'fk_pe_personal')
                ->references('id_personal')->on('personal')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('EDIFICIO_id_edificio', 'fk_pe_edificio')
                ->references('id_edificio')->on('edificio')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personal_edificio');
    }
};
