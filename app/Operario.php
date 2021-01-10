<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use App\Operario;
use App\Empresa;


class Operario extends model{

    public function empresa (){

        return $this->belongsTo('App\Empresa', 'empresa_id');
    }

    public function asignar(){

        return $this->hasMany('App\Asignar');
    }
}
