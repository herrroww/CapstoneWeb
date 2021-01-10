<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Operario;
use App\Empresa;

class Empresa extends Model{
    
    public function operario (){

        return $this->hasMany('App\Operario');
    }

    public function asignar (){

        return $this->hasMany('App\Asignar');
    }
}
