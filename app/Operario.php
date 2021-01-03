<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use App\Operario;
use App\Empresa;
use OwenIt\Auditing\Contracts\Auditable;


class Operario extends model implements Auditable
{

    use \OwenIt\Auditing\Auditable;
    
    public function empresa (){

        return $this->belongsTo('App\Empresa', 'empresa_id');
    }
    public function asignar(){
        return $this->hasMany('App\Asignar');
        }
   

    
}
