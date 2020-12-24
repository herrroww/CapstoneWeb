<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReporteproblemasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reporteproblemas', function (Blueprint $table) {
            $table->id();
            $table->string('rutOperario', 100)->unique();
            $table->string('nombreOperario', 100);
            $table->string('correoOperario', 100);
            $table->string('numeroOperario', 100);
            $table->string('prioridad', 100);
            $table->string('estado', 100)->nullable();
            $table->string('fechaReporteProblema', 100);
            $table->string('tituloReporteProblema', 100);
            $table->string('contenidoReporteProblema', 100);
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
        Schema::dropIfExists('reporteproblemas');
    }
}
