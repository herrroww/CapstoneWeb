<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Documento;
use App\Componente;

class Documento extends Model
{

    public function componente(){
        
        return $this->belongsTo("App\Componente");
    }

    protected $fillable=['nombre','descripcion','file','privacidad','extension'];
}
