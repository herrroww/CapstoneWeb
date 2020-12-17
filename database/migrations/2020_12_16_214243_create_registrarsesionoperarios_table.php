<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrarsesionoperariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registrarsesionoperarios', function (Blueprint $table) {
            $table->id();
            $table->string('rutOperario', 100)->unique();
            $table->string('nombreOperario', 100);
            $table->string('mensajeEvento', 100);
            $table->string('fechaEvento', 100);

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
        Schema::dropIfExists('registrarsesionoperarios');
    }
}
