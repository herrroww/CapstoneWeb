<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Asignar extends Model{

    public function componente(){

        return $this->belongsTo('App\Componente', 'componente_id');

    }

    public function operario(){

        return $this->belongsTo('App\Operario', 'operario_id');
    }
}
