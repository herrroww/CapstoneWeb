<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Empresa;
use App\Operario;
use App\User;
use App\AuditTrail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;
use App\Http\gestionopController;

class EmpresaController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request){

        if($request){
            $query = trim($request->get('search'));

            $empresas = Empresa::where('nombreEmpresa',  'LIKE', '%' . $query . '%')
                ->orwhere('rutEmpresa',  'LIKE', '%' . $query . '%')
                ->orderBy('id', 'asc')
                ->orwhere('id',  'LIKE', '%' . $query . '%')
                ->paginate(7);

            return view('empresas.index', ['empresas' => $empresas, 'search' => $query, 'activemenu' => 'empresa']);
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

        return view('empresas.create',['activemenu' => 'empresa']);

       
        
    }

    public function store(Request $request){
        $empresa = new Empresa();
        $user = Auth::user();
        
        $empresa->rutEmpresa = request('rutEmpresa');
        $empresa->nombreEmpresa = request('nombreEmpresa');
        $empresa->compania = request('compania');
        
        $empresa->save();
        

        return redirect('empresaop')->with('create','La empresa se a creado correctamente');

    }

    public function edit($id){
        return view('empresas.edit', ['empresa' => Empresa::findOrFail($id), 'activemenu' => 'empresa']);
    }

    public function update(Request $request, $id){
        $empresa = Empresa::findOrFail($id);

        $user = Auth::user();
        
        $empresa->rutEmpresa = $request->get('rutEmpresa');
        $empresa->nombreEmpresa = $request->get('nombreEmpresa');
        $empresa->compania = $request->get('compania');
        
        $empresa->update();

        return redirect('empresaop')->with('edit','La empresa se a editado');
          }

    public function destroy($id){

        $empresa = Empresa::findOrFail($id);



        $empresa->operario()->delete();

        $empresa->asignar()->delete();


       


        $empresa->delete();


        return redirect()->back()->with('success','La empresa a sido eliminada.');

        

        
    }
}