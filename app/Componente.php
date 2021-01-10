<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Modelo;
use App\Documento;
use App\Componente;


class Componente extends Model{

    public function documento(){

        return $this->hasMany("App\Documento");
    }

    public function asignar(){

        return $this->hasMany('App\Asignar');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'idComponente'
    ];
}
