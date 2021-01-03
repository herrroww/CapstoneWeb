<?php

namespace App\Http\Controllers;
use App\Empresa;
use App\Operario;
use App\Historial;

use Illuminate\Http\Request;

class HistorialController extends Controller{

    public function __construct(){

        $this->middleware('auth');
    }
    
    public function index(Request $request){

        if($request){

            $empresas = Empresa::all();
            $operarios = Operario::all();
            
            $query = trim($request->get('search'));


            $audits = \OwenIt\Auditing\Models\Audit::with('user')
                ->where('event',  'LIKE', '%' . $query . '%')
                ->orwhere('created_at',  'LIKE', '%' . $query . '%')
                ->orderBy('created_at', 'desc')
                ->paginate(5);
        
        return view('historiales.index', ['audits' => $audits, 'search' => $query, 'empresas' => $empresas, 'operarios' => $operarios, 'activemenu' => 'historial']);
        }
    }

    public function show($id){

        return view('historiales.show', ['audit' => \OwenIt\Auditing\Models\Audit::findOrFail($id), 'activemenu' => 'historial']);
    }
}
