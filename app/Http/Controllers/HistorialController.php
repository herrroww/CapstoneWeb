<?php

namespace App\Http\Controllers;
use App\Empresa;
use App\Operario;
use App\Historial;
use App\HistoricoGestion;

use Illuminate\Support\Facades\DB;

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


            $historicogestion = DB::table('historico_gestions')
                ->where('nombreGestion',  'LIKE', '%' . $query . '%')
                ->orwhere('tipoGestion',  'LIKE', '%' . $query . '%')
                ->orwhere('responsableGestion',  'LIKE', '%' . $query . '%')
                ->orwhere('created_at',  'LIKE', '%' . $query . '%')
                ->orderBy('created_at', 'desc')
                ->paginate(5);
        
        return view('historiales.index', ['historicogestion' => $historicogestion, 'search' => $query, 'activemenu' => 'historial']);
        }
    }

    public function show($id){

        return view('historiales.show', ['historicogestion' => HistoricoGestion::findOrFail($id), 'activemenu' => 'historial']);
    }
}
