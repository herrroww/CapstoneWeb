<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use App\Componente;
use App\Modelo;
use App\Documento;
use App\Http\Controllers\ErrorRepositorio;
use App\Http\Controllers\FtpConexion;
use phpseclib\Net\SSH2;

class ComponenteController extends Controller{

    public function __construct(){
        
        $this->middleware('auth');
    }
    
    public function index(Request $request){

        if($request){
            $query = trim($request->get('search'));

            $componentes = Componente::where('nombreComponente',  'LIKE', '%' . $query . '%')
                ->orwhere('idComponente',  'LIKE', '%' . $query . '%')
                ->orwhere('id',  'LIKE', '%' . $query . '%')
                ->orderBy('id', 'asc')
                ->paginate(7);

            return view('componentes.index', ['componentes' => $componentes, 'search' => $query, 'activemenu' => 'componente']);
        }        
    }
    
    public function create(){

        return view('componentes.create',['activemenu' => 'componente']);
    }

    public function store(Request $request){

        //Carga el repositorio de errores.
        $SWERROR = new ErrorRepositorio();

        //Prepara los parametros de conexion al servidor FTP.
        $ftpParameters = new FtpConexion();

        $componente = new Componente();
        $componente->nombreComponente = request('nombreComponente');
        $componente->IdComponente = request('idComponente');
        
        //Prepara la conexion al servidor FTP.
        $ssh = new SSH2($ftpParameters->getServerFTP());

        //Intenta hacer la conexion al servidor FTP.
        if(!$ssh->login($ftpParameters->getUserFTP(),$ftpParameters->getPassFTP())){
            
            //TODO: Actualizar formato de error.
            //Se liberan los recursos.           
            unset($ssh);
            unset($ftpParameters);
            //[SWERROR 002]: Problema al ingresar las credenciales de usuario FTP.
            exit($SWERROR->ErrorActual(1));
            unset($SWERROR);
        }else{

            //Verifica si el directorio existe.
            $estadoExiste = $ssh->exec('[ -d /home/Componentes/Externo/'.$componente->IdComponente.' ] && echo "1" || echo "0"');
            
            //Limpia la informacion obtenida.
            $estadoExiste = $estadoExiste[0];

            if($estadoExiste == '1'){

                //TODO: Actualizar formato de error.
                //Se liberan los recursos.           
                unset($ssh);
                unset($ftpParameters);
                //El componente ya existe en Externo.
                exit('El componente ya existe en Externo.');
                unset($SWERROR);
            }else{

                //Verifica si el directorio existe.
                $estadoExiste = $ssh->exec('[ -d /home/Componentes/Interno/'.$componente->IdComponente.' ] && echo "1" || echo "0"');
                
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0];

                if($estadoExiste == '1'){

                    //TODO: Actualizar formato de error.
                    //Se liberan los recursos.           
                    unset($ssh);
                    unset($ftpParameters);
                    //el componente ya existe en Interno
                    exit($SWERROR->ErrorActual(7));
                    unset($SWERROR);
                }else{
                    
                    //Se guardan los cambios en la Base de Datos.
                    $componente->save();

                    //Se aplican los cambios en el servidor FTP.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP.' | sudo -S mkdir -p /home/Componentes/Externo/'.$componente->IdComponente);
                    $ssh->exec('echo '.$ftpParameters->getPassFTP.' | sudo -S mkdir -p /home/Componentes/Interno/'.$componente->IdComponente);
                
                    $ssh->exec('exit');
                    //Se liberan los recursos.       
                    unset($SWERROR);
                    unset($ssh);
                    unset($ftpParameters); 
                    return redirect('componenteop')->with('create','El componente se a creado correctamente');
                }
            }
        }                  
    }

    public function edit($id){
        
        return view('componentes.edit', ['componente' => Componente::findOrFail($id), 'activemenu' => 'componente']);
    }

    //TODO: EDITAR EN FTP
    public function update(Request $request, $id){

        //Carga el repositorio de errores.
        $SWERROR = new ErrorRepositorio();

        //Prepara los parametros de conexion al servidor FTP.
        $ftpParameters = new FtpConexion();

        $componente = Componente::findOrFail($id);
        
        $componente->nombreComponente = $request->get('nombreComponente');
        $componente->idComponente = $request->get('idComponente');

        $componente->update();

        return redirect('componenteop')->with('edit','El Componente se a editado');
    }

    //TODO: ELIMINAR EN FTP
    public function destroy($id){

        $componente = Componente::findOrFail($id);        
        $componente->delete();
        $componente->asignar()->delete();
      
        return redirect('componenteop')->with('success','El Componente se a eliminado correctamente.');
    }

    public function show($id){

        Session::put('componente_id',$id);       

        return redirect('documentosop');
    }
}