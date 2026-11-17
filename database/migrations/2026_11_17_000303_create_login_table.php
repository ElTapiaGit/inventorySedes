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
        Schema::create('login', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

            $table->integer('id_acceso', true);
            $table->string('nombre', 50)->nullable()->collation('utf8_bin');
            $table->string('contrasena', 80)->nullable();
            $table->integer('PERSONAL_id_personal');

            // index
            $table->unique('id_acceso', 'id_usuario_UNIQUE');
            $table->index('PERSONAL_id_personal', 'fk_USUARIO_PERSONAL1_idx');

            // relacion foranea 
            $table->foreign('PERSONAL_id_personal', 'fk_USUARIO_PERSONAL1')
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
        Schema::dropIfExists('login');
    }
};