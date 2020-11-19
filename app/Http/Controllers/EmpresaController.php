<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Empresa;

class EmpresaController extends Controller
{
    public function index(Request $request){

        if($request){
            $query = trim($request->get('search'));

            $empresas = Empresa::where('nombre',  'LIKE', '%' . $query . '%')
                ->orwhere('rut',  'LIKE', '%' . $query . '%')
                ->orderBy('id', 'asc')
                ->paginate(7);

            return view('empresas.index', ['empresas' => $empresas, 'search' => $query]);
        }
        
        
        
        
    }
    
    public function create(){
        return view('empresas.create');
    }

    public function store(Request $request){
        $empresa = new Empresa();

        $empresa->rut = request('rut');
        $empresa->nombre = request('nombre');
        $empresa->compania = request('compania');
        
        $empresa->save();

        return redirect('empresaop')->with('create','La empresa se a creado correctamente');

    }

    public function edit($id){
        return view('empresas.edit', ['empresa' => Empresa::findOrFail($id)]);
    }

    public function update(Request $request, $id){
        $empresa = Empresa::findOrFail($id);
        
        $empresa->rut = $request->get('rut');
        $empresa->nombre = $request->get('nombre');
        $empresa->compania = $request->get('compania');
        

        $empresa->update();

        return redirect('empresaop')->with('edit','La empresa se a editado');
        ;
    }

    public function destroy($id){

        $empresa = Empresa::findOrFail($id);

        $empresa->delete();

        return redirect()->back()->with('success','La empresa a sido eliminada.');

        

        
    }
}