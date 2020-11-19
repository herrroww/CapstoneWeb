<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Modelo;


class Componente extends Model
{

    
    public function modelo(){
        return $this->hasMany("App\Modelo");
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
