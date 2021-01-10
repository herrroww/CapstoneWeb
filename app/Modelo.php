<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Componente;



class Modelo extends Model
{

    protected $fillable = [
        'name', 'idModelo'
    ];
    
}
