<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Modelo;
use App\Documento;
use App\Componente;
use OwenIt\Auditing\Contracts\Auditable;


class Componente extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    /*public function modelo(){
        return $this->hasMany("App\Modelo");
    }*/
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
