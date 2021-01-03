<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Reporteproblema;

class ReporteController extends Controller{

    public function __construct(){

        $this->middleware('auth');
    }

    public function index(Request $request){

        if($request){
            $query = trim($request->get('search'));

            $reporteproblemas = Reporteproblema::where('nombreOperario',  'LIKE', '%' . $query . '%')
                ->orwhere('rutOperario',  'LIKE', '%' . $query . '%')
                ->orderBy('id', 'asc')
                ->orwhere('id',  'LIKE', '%' . $query . '%')
                ->paginate(7);

            return view('reportes.index', ['reporteproblemas' => $reporteproblemas, 'search' => $query, 'activemenu' => 'reporteproblema']);
        }  
    }

    public function edit($id){

        return view('reportes.edit', ['reporteproblema' => Reporteproblema::findOrFail($id), 'activemenu' => 'reporteproblema']);
    }
    
    public function update(Request $request, $id){

        $reporteproblema = Reporteproblema::findOrFail($id);

        $reporteproblema->estado = $request->get('estado');
        
        $reporteproblema->update();

        return redirect('reporteop')->with('edit','Se a modificado correctamente');
    }
}
