<?php

namespace App\Http\Controllers;

use App\Http\Requests\OperarioFormRequest;
use Illuminate\Http\Request;
use App\Operario;
use App\Empresa;
class gestionopController extends Controller
{
    public function index(Request $request){

        if($request){
            
            $query = trim($request->get('search'));
            

            $operarios = Operario::where('nombre',  'LIKE', '%' . $query . '%')
                ->orwhere('rut',  'LIKE', '%' . $query . '%')
                ->orderBy('id', 'asc')
                ->paginate(7);

            return view('gestionOperarios.index', ['operarios' => $operarios, 'search' => $query]);
        }
        
        
        
        //$operarios = Operario::all();
        //return view('gestionOperarios.index',['operarios' => $operarios]);
    }
    
    public function create(){

        $empresa = Empresa::all();
        $operario = Operario::all();
        $data = array("lista_empresas" => $empresa);

        return view('gestionOperarios.create',compact('empresa'));
    }

    public function store(Request $request){
        $operario = new Operario();

        $operario->nombre = request('nombre');
        $operario->rut = request('rut');
        $operario->correo = request('correo');
        $operario->tipoOperario = request('tipoOperario');
        $operario->empresa_id = request('empresa');

        $operario->save();

        return redirect('gestionop')->with('create','');

    }

    public function edit($id){
        $operario = Operario::FindOrFail($id);
        $empresa = Empresa::all();
        return view('gestionOperarios.edit', compact('operario','empresa'));
    }

    public function update(OperarioFormRequest $request, $id){
        $operario = Operario::findOrFail($id);
        
        $operario->nombre = $request->get('nombre');
        $operario->rut = $request->get('rut');
        $operario->correo = $request->get('correo');
        $operario->tipoOperario = $request->get('tipoOperario');
        $operario->empresa_id = $request->get('empresa');

        $operario->update()->with('edit','');

        return redirect('gestionop');
    }

    public function destroy($id){

        $operario = Operario::findOrFail($id);

        $operario->delete();

       

        return redirect()->back()->with('success','La empresa a sido eliminada.');


        

        
    }
}
