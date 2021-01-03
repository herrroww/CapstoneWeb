<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComponentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('componentes', function (Blueprint $table) {
            
            $table->id();
            $table->string('nombreComponente', 100);
            $table->string('idComponente', 100)->unique();     
            $table->string('codigoQR', 100)->nullable();      
            $table->string('codigoIdentificador', 100)->nullable(); 
            $table->string('codigoNFC', 100)->nullable();  
            $table->string('linkMemoriaDeCalculo', 100)->nullable();      
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
        Schema::dropIfExists('componentes');
        
    }
}
