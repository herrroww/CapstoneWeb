<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Operario;
use App\Empresa;
use OwenIt\Auditing\Contracts\Auditable;

class Empresa extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function operario (){
    return $this->hasMany('App\Operario');
    }

    public function asignar (){
        return $this->hasMany('App\Asignar');
        }

    
}
