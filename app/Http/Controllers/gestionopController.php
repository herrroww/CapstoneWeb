<?php

namespace App\Http\Controllers;

use App\Http\Requests\OperarioFormRequest;
use Illuminate\Http\Request;
use App\Operario;
use App\Empresa;
use App\Asignar;
use DB;


class gestionopController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request){

        if($request){
            
            $query = trim($request->get('search'));
            

            $operarios = Operario::where('nombreOperario',  'LIKE', '%' . $query . '%')
                ->orwhere('rutOperario',  'LIKE', '%' . $query . '%')
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

        $operario->nombreOperario = request('nombreOperario');
        $operario->rutOperario = request('rutOperario');
        $operario->correoOperario = request('correoOperario');
        $operario->tipoOperario = request('tipoOperario');
        $operario->empresa_id = request('empresa');
        $operario->contraseniaOperario = request('contraseniaOperario');
        $operario->contraseniaOperarioFTP = $operario->nombreOperario;  
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
        
        $operario->nombreOperario = $request->get('nombreOperario');
        $operario->rutOperario = $request->get('rutOperario');
        $operario->correoOperario = $request->get('correoOperario');
        $operario->tipoOperario = $request->get('tipoOperario');
        $operario->empresa_id = $request->get('empresa');
        $operario->contraseniaOperario = $request->get('contraseniaOperario');
        $operario->contraseniaOperarioFTP = $operario->nombreOperario;
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

    /**function fetch(Request $request)
    {
     if($request->get('query'))
     {
      $query = $request->get('query');
      $data = DB::table('empresas')
        ->where('nombreEmpresa', 'LIKE', "%{$query}%")
        ->get();
      $output = '<ul class="dropdown-menu" style="display:block; position:relative">';
      foreach($data as $row)
      {
       $output .= '<li><a href="#">'.$row->
       nombreEmpresa.'</a></li>';
      }
      $output .= '</ul>';
      echo $output;
     }
    }**/
}
