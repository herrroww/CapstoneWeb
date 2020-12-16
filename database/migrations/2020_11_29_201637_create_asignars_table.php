<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsignarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asignars', function (Blueprint $table) {
            $table->id();
            $table->integer('operario_id')->unsigned();
            $table->integer('componente_id')->unsigned();
            $table->timestamps();

            $table->foreign('operario_id')->references('id')->on('operarios')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('componente_id')->references('id')->on('componentes')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operario_componente');
    }
}
