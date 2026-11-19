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
        Schema::table('edificio', function (Blueprint $table) {
            $table->integer('CARRERA_id_carrera')->nullable()->after('SEDE_id_sede');
            $table->index('CARRERA_id_carrera', 'fk_EDIFICIO_CARRERA1_idx');
            $table->foreign('CARRERA_id_carrera', 'fk_EDIFICIO_CARRERA1')
                ->references('id_carrera')->on('carrera')
                ->onDelete('set null')->onUpdate('cascade');
        });

        Schema::table('ambiente', function (Blueprint $table) {
            $table->integer('CARRERA_id_carrera')->nullable()->after('PISO_id_piso');
            $table->index('CARRERA_id_carrera', 'fk_AMBIENTE_CARRERA1_idx');
            $table->foreign('CARRERA_id_carrera', 'fk_AMBIENTE_CARRERA1')
                ->references('id_carrera')->on('carrera')
                ->onDelete('set null')->onUpdate('cascade');
        });

        Schema::table('personal', function (Blueprint $table) {
            $table->integer('CARRERA_id_carrera')->nullable()->after('EDIFICIO_id_edificio');
            $table->index('CARRERA_id_carrera', 'fk_PERSONAL_CARRERA1_idx');
            $table->foreign('CARRERA_id_carrera', 'fk_PERSONAL_CARRERA1')
                ->references('id_carrera')->on('carrera')
                ->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('personal', function (Blueprint $table) {
            $table->dropForeign('fk_PERSONAL_CARRERA1');
            $table->dropIndex('fk_PERSONAL_CARRERA1_idx');
            $table->dropColumn('CARRERA_id_carrera');
        });

        Schema::table('ambiente', function (Blueprint $table) {
            $table->dropForeign('fk_AMBIENTE_CARRERA1');
            $table->dropIndex('fk_AMBIENTE_CARRERA1_idx');
            $table->dropColumn('CARRERA_id_carrera');
        });

        Schema::table('edificio', function (Blueprint $table) {
            $table->dropForeign('fk_EDIFICIO_CARRERA1');
            $table->dropIndex('fk_EDIFICIO_CARRERA1_idx');
            $table->dropColumn('CARRERA_id_carrera');
        });
    }
};
