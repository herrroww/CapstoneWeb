<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Componente;



class Modelo extends Model
{

    public function componente(){
        return $this->belongsTo("App\Componente");
    }

    protected $fillable = [
        'name', 'idModelo'
    ];
    
}
