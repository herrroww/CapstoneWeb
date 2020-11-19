<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Componente;
use App\Modelo;
use Session;

class ComponenteController extends Controller
{
    public function index(Request $request){

        if($request){
            $query = trim($request->get('search'));

            $componentes = Componente::where('nombre',  'LIKE', '%' . $query . '%')
                ->orwhere('idComponente',  'LIKE', '%' . $query . '%')
                ->orderBy('id', 'asc')
                ->paginate(7);

            return view('componentes.index', ['componentes' => $componentes, 'search' => $query]);
        }
        
        
        
        //$operarios = Operario::all();
        //return view('gestionOperarios.index',['operarios' => $operarios]);
    }
    
    public function create(){
        return view('componentes.create');
    }

    public function store(Request $request){
        $componente = new Componente();

        $componente->nombre = request('nombre');
        $componente->IdComponente = request('idComponente');
        

        $componente->save();

        return redirect('componenteop');

    }

    public function edit($id){
        return view('componentes.edit', ['componente' => Componente::findOrFail($id)]);
    }

    public function update(Request $request, $id){
        $componente = Componente::findOrFail($id);
        
        $componente->nombre = $request->get('nombre');
        $componente->idComponente = $request->get('idComponente');

        $componente->update();

        return redirect('componenteop');
    }

    public function destroy($id){

        $componente = Componente::findOrFail($id);
        
        $componente->delete();
      

        return redirect('componenteop');

        

        
    }

    public function show($id){

        Session::put('componente_id',$id);
        return redirect('modelosop');

        //return view('componentes.show', ['componente' => Componente::findOrFail($id)]);
    }
}


