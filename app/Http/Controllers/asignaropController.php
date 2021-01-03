<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Operario;
use App\Componente;
use App\Asignar;

class asignaropController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request){

        if($request){
            
            $query = trim($request->get('search'));
            

            $asignars = Asignar::where('operario_id',  'LIKE', '%' . $query . '%')
                ->orwhere('componente_id',  'LIKE', '%' . $query . '%')
                ->orwhere('id',  'LIKE', '%' . $query . '%')
                ->orderBy('id', 'asc')
                ->paginate(7);

            $operarios = Operario::all();

            return view('asignarComponente.index', ['asignars' => $asignars, 'operarios' => $operarios, 'search' => $query, 'activemenu' => 'asignar']);
        }
        
        
        
        //$operarios = Operario::all();
        //return view('gestionOperarios.index',['operarios' => $operarios]);
    }
    
    public function create(){

        $componente = Componente::all();
        $operario = Operario::all();
        $asignar= Asignar::all();
        $data = array("lista_componentes" => $componente);
        $data1 = array("lista_operarios" => $operario);


        return view('asignarComponente.create', ['activemenu' => 'asignar'], compact('operario','componente'));
    }

    public function store(Request $request){
        $asignar = new Asignar();

        $asignar->operario_id = request('operario');
        $asignar->componente_id = request('componente');
        $operario = Operario::findOrFail(request('operario'));
        $asignar->empresa_id = $operario->empresa_id;
        
        

        $asignar->save();

        return redirect('asignarop')->with('create','Se asigno correctamente');

    }

    public function edit($id){
        $asignar = Asignar::FindOrFail($id);
        $operario= Operario::all();
        $componente= Componente::all();
        return view('asignarComponente.edit', ['activemenu' => 'asignar' ], compact('operario','componente','asignar'));
    }

    public function update(Request $request, $id){
        $asignar = Asignar::findOrFail($id);
        $operario = Operario::findOrFail(request('operario'));
        $asignar->operario_id = $request->get('operario');
        $asignar->componente_id = $request->get('componente');
        $asignar->empresa_id = $operario->empresa_id;
       
    

        $asignar->update();

        return redirect('asignarop')->with('edit','se a editado correctamente');
    }

    public function destroy($id){

        $asignar = Asignar::findOrFail($id);

        $asignar->delete();

       

        return redirect()->back()->with('success','La empresa a sido eliminada.');


        

        
    }
}

