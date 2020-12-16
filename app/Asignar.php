<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class Asignar extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function componente(){

        return $this->belongsTo('App\Componente', 'componente_id');

    }

    public function operario(){

        return $this->belongsTo('App\Operario', 'operario_id');

    }
}
