<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoricogestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historicogestion', function (Blueprint $table) {
            $table->id();
            $table->string('nombreGestion');
            $table->string('tipoGestion');
            $table->string('responsableGestion');
            $table->string('descripcionGestion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historicogestion');
    }
}
