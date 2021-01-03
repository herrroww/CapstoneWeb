<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombreOperario', 100);
            $table->string('rutOperario', 100)->unique();
            $table->string('correoOperario', 100)->unique();
            $table->string('tipoOperario', 100);
            $table->string('contraseniaOperario',100);
            $table->string('contraseniaOperarioFTP',100);
            $table->string('telefonoOperario',100);
           
            $table->unsignedBigInteger('empresa_id');
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('operarios');
    }
}
