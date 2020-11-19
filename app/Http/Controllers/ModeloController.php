<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Componente;
use App\Modelo;
use Session;

class ModeloController extends Controller
{
    public function index(Request $request){

        

        if(!empty(Session::get('componente_id')) && $request){

            $modelos = Modelo::whereComponente_id(Session::get('componente_id'))->get();
           
            
            
            $query = trim($request->get('search'));


                return view("modelos.index", [ 'search' => $query, 'modelos' => $modelos]);

                
        }
        
    }
    

        
    

    public function create(){
        return view('modelos.create');
    }

    public function store(Request $request){
        $modelo = new Modelo();

        $modelo->nombre = request('nombre');
        $modelo->idModelo = request('idModelo');
        $modelo->componente_id = Session::get('componente_id');

        $modelo->save();

        return redirect('modelosop');

    }

    public function edit($id){
        return view('modelos.edit', ['modelo' => Modelo::findOrFail($id)]);
    }

    public function update(Request $request, $id){
        $modelo = Modelo::findOrFail($id);
        
        $modelo->nombre = $request->get('nombre');
        $modelo->idModelo = $request->get('idModelo');

        $modelo->update();

        return redirect('modelosop');
    }

    public function destroy($id){

        $modelo = Modelo::findOrFail($id);

        $modelo->delete();

        return redirect('modelosop');

        

        
    }

        
}
