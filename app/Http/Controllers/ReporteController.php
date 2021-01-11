<?php

namespace App\Http\Controllers;

use App\Http\Controllers\FtpConexion;
use Illuminate\Support\Facades\DB;
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
                ->orwhere('estado',  'LIKE', '%' . $query . '%')
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

        //Prepara los parametros de conexion al servidor FTP.
        $ftpParameters = new FtpConexion();

        $reporteProblema = Reporteproblema::findOrFail($id);

        $estadoReporteTemp = $reporteProblema->estado;

        $reporteProblema->estado = $request->get('estado');

        //Se añade al historico de gestion.
        DB::table('historico_gestions')->insert(['nombreGestion' => 'Reporte', 
                                               'tipoGestion' => 'Editar',
                                               'responsableGestion' => $ftpParameters->getUserFTP(),
                                               'descripcionGestion' => 'Modificacion Actual => Estado de Reporte: '.$reporteProblema->estado.', ID de Reporte: '.$reporteProblema->id.' | Datos Antiguos => Estado de Reporte: '.$estadoReporteTemp.', ID de Reporte: '.$reporteProblema->id,
                                               'created_at' => now()]);
        
        $reporteProblema->update();

        return redirect('reporteop')->with('edit','Se a modificado correctamente');
    }
}
