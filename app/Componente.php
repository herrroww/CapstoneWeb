<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Modelo;
use OwenIt\Auditing\Contracts\Auditable;


class Componente extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    /*public function modelo(){
        return $this->hasMany("App\Modelo");
    }*/
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'idComponente'
    ];

    

    
}
