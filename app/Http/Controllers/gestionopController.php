<?php

namespace App\Http\Controllers;

use App\Http\Requests\OperarioFormRequest;
use Illuminate\Http\Request;
use App\Operario;
use App\Empresa;
use App\Asignar;

class gestionopController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request){

        if($request){
            
            $query = trim($request->get('search'));
            

            $operarios = Operario::where('nombre',  'LIKE', '%' . $query . '%')
                ->orwhere('rut',  'LIKE', '%' . $query . '%')
                ->orwhere('id',  'LIKE', '%' . $query . '%')
                ->orderBy('id', 'asc')
                ->paginate(7);

            return view('gestionOperarios.index', ['operarios' => $operarios, 'search' => $query, 'activemenu' => 'operario']);
        }
        
        
        
        //$operarios = Operario::all();
        //return view('gestionOperarios.index',['operarios' => $operarios]);
    }
    
    public function create(){

        $empresa = Empresa::all();
        $operario = Operario::all();
        $data = array("lista_empresas" => $empresa);

        return view('gestionOperarios.create',['activemenu' => 'operario'],compact('empresa'));
    }

    public function store(Request $request){
        $operario = new Operario();

        $operario->nombre = request('nombre');
        $operario->rut = request('rut');
        $operario->correo = request('correo');
        $operario->tipoOperario = request('tipoOperario');
        $operario->empresa_id = request('empresa');
        $operario->contraseniaOperario = request('contraseniaOperario');
        $operario->contraseniaOperarioFTP = $operario->rut.$operario->empresa_id.$operario->contraseniaOperario.$operario->nombre;  
        $operario->telefonoOperario = request('telefonoOperario');
        $operario->save();

        return redirect('gestionop')->with('create','El Operario se a creado');

    }

    public function edit($id){
        $operario = Operario::FindOrFail($id);
        $empresa = Empresa::all();
        return view('gestionOperarios.edit', ['activemenu' => 'operario'],compact('operario','empresa'));
    }

    public function update(OperarioFormRequest $request, $id){
        $operario = Operario::findOrFail($id);
        
        $operario->nombre = $request->get('nombre');
        $operario->rut = $request->get('rut');
        $operario->correo = $request->get('correo');
        $operario->tipoOperario = $request->get('tipoOperario');
        $operario->empresa_id = $request->get('empresa');
        $operario->contraseniaOperario = $request->get('contraseniaOperario');
        $operario->contraseniaOperarioFTP = $operario->rut.$operario->empresa_id.$operario->contraseniaOperario.$operario->nombre;
        $operario->telefonoOperario =  $request->get('telefonoOperario');

        $operario->update();

        return redirect('gestionop')->with('edit','El operario se a editado');
    }

    public function destroy($id){

        $operario = Operario::findOrFail($id);

        $operario->asignar()->delete();

        $operario->delete();

       

        return redirect()->back()->with('success','El operario a sido eliminada.');


        

        
    }
}
