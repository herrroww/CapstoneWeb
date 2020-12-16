<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Empresa;
use App\User;
use App\AuditTrail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;

class EmpresaController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request){

        if($request){
            $query = trim($request->get('search'));

            $empresas = Empresa::where('nombre',  'LIKE', '%' . $query . '%')
                ->orwhere('rut',  'LIKE', '%' . $query . '%')
                ->orderBy('id', 'asc')
                ->orwhere('id',  'LIKE', '%' . $query . '%')
                ->paginate(7);

            return view('empresas.index', ['empresas' => $empresas, 'search' => $query]);
        }
        
        
        
        
    }
    
    public function create(Request $request){
        

        /*$user = Auth::user();
        $data = $request->validate(
            [
                'rut' => 'required',
                'nombre' => 'required',
                'compañia' => 'required',
            ]
            );
        DB::beginTransaction();
            $empresa_data = EmpresaData::query()->create($data);
            $user->log("CREATED DATA {$empresa_data->nombre}");
            
        DB::commit();*/

        return view('empresas.create');

       
        
    }

    public function store(Request $request){
        $empresa = new Empresa();
        $user = Auth::user();
        $log = new AuditTrail();

        $log->user_id = ($user->id);
        $log->name = ($empresa->nombre = request('nombre'));
        $log->date =('12321');
        $log->activity = ('Created');
        $empresa->rut = request('rut');
        $empresa->nombre = request('nombre');
        $empresa->compania = request('compania');
        
        $log->save();
        $empresa->save();
        

        return redirect('empresaop')->with('create','La empresa se a creado correctamente');

    }

    public function edit($id){
        return view('empresas.edit', ['empresa' => Empresa::findOrFail($id)]);
    }

    public function update(Request $request, $id){
        $empresa = Empresa::findOrFail($id);

        $user = Auth::user();
        $log = new AuditTrail();

        $log->user_id = ($user->id);
        $log->name = ($empresa->nombre = $request->get('nombre'));
        $log->date =('12321');
        $log->activity = ('Edited');
        
        $empresa->rut = $request->get('rut');
        $empresa->nombre = $request->get('nombre');
        $empresa->compania = $request->get('compania');
        
        $log->save();
        $empresa->update();

        return redirect('empresaop')->with('edit','La empresa se a editado');
          }

    public function destroy($id){

        $empresa = Empresa::findOrFail($id);

        $empresa->delete();

        return redirect()->back()->with('success','La empresa a sido eliminada.');

        

        
    }
}