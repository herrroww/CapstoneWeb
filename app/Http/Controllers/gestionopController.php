<?php

namespace App\Http\Controllers;

use App\Http\Requests\OperarioFormRequest;
use Illuminate\Http\Request;
use App\Operario;
class gestionopController extends Controller
{
    public function index(){

        $operarios = Operario::all();
        return view('gestionOperarios.index',['operarios' => $operarios]);
    }
    
    public function create(){
        return view('gestionOperarios.create');
    }

    public function store(Request $request){
        $operario = new Operario();

        $operario->nombre = request('nombre');
        $operario->rut = request('rut');
        $operario->correo = request('correo');
        $operario->empresa = request('empresa');
        $operario->tipoOperario = request('tipoOperario');

        $operario->save();

        return redirect('gestionop');

    }

    public function edit($id){
        return view('gestionOperarios.edit', ['operario' => Operario::findOrFail($id)]);
    }

    public function update(OperarioFormRequest $request, $id){
        $operario = Operario::findOrFail($id);
        
        $operario->nombre = $request->get('nombre');
        $operario->rut = $request->get('rut');
        $operario->correo = $request->get('correo');
        $operario->empresa = $request->get('empresa');
        $operario->tipoOperario = $request->get('tipoOperario');

        $operario->update();

        return redirect('gestionop');
    }

    public function destroy($id){
        $operario = Operario::findOrFail($id);

        $operario->delete();

        return redirect('gestionop');

        
    }
}
