<?php

namespace App\Http\Controllers;

use App\Http\Requests\OperarioFormRequest;
use Illuminate\Http\Request;
use App\Operario;
use App\Empresa;

use App\Http\Controllers\ErrorRepositorio;

use phpseclib\Net\SSH2;


class gestionopController extends Controller
{

    //IP del servidor FTP.
    private $serverFTP = '192.168.0.28';
    
    //Credenciales de usuario FTP
    private $userFTP= 'capstone';
    private $passFTP= 'capstone';


    public function index(Request $request){

        if($request){
            
            $query = trim($request->get('search'));
            

            $operarios = Operario::where('nombre',  'LIKE', '%' . $query . '%')
                ->orwhere('rut',  'LIKE', '%' . $query . '%')
                ->orderBy('id', 'asc')
                ->paginate(7);

            return view('gestionOperarios.index', ['operarios' => $operarios, 'search' => $query]);
        }
        
        
        
        //$operarios = Operario::all();
        //return view('gestionOperarios.index',['operarios' => $operarios]);
    }
    
    public function create(){

        $empresa = Empresa::all();
        $operario = Operario::all();
        $data = array("lista_empresas" => $empresa);

        return view('gestionOperarios.create',compact('empresa'));
    }

    public function store(Request $request){

        //Carga el repositorio de errores.
        $SWERROR = new ErrorRepositorio();

        /*Genera al operario y rellena los atributos con la informacion
        * entregada por el usuario.
        */
        $operario = new Operario();
        $operario->nombre = request('nombre');
        $operario->rut = request('rut');
        $operario->correo = request('correo');
        $operario->tipoOperario = request('tipoOperario');
        $operario->empresa_id = request('empresa');

        //Se obtiene el rut de la empresa seleccionada.
        $rutEmpresa = Empresa::FindOrFail($operario->empresa_id)->rut;
 
        //Se prepara la conexion al servidor FTP.
        $ssh = new SSH2($this->serverFTP);
              
        //Intenta hacer la conexion al servidor FTP.
        if(!$ssh->login($this->userFTP,$this->passFTP)){
            
            //[SWERROR 002]: Problema al ingresar las credenciales de usuario FTP.
            exit($SWERROR->ErrorActual(1));
        }else{

            //Verifica si el directorio existe.
            $estadoExiste = $ssh->exec('[ -d /home/capstone/ftp/OperariosExternos/'.$rutEmpresa.'/'.$operario->rut.' ] && echo "true" || echo "false"');
            
            //Limpia la informacion obtenida.
            $estadoExiste = $estadoExiste[0].$estadoExiste[1].$estadoExiste[2].$estadoExiste[3];
            
            if($estadoExiste == 'true'){

                //[SWERROR 007]: El operario ya existe en el sistema FTP (Conflicto en OperariosExternos).
                exit($SWERROR->ErrorActual(6));
            }else{

                //Verifica si el directorio existe.
                $estadoExiste = $ssh->exec('[ -d /home/capstone/ftp/OperariosInternos/'.$rutEmpresa.'/'.$operario->rut.' ] && echo "true" || echo "false"');
                
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0].$estadoExiste[1].$estadoExiste[2].$estadoExiste[3];

                if($estadoExiste == 'true'){

                    //[SWERROR 008]: El operario ya existe en el sistema FTP (Conflicto en OperariosInternos).
                    exit($SWERROR->ErrorActual(7));
                }else{

                    //Se crea el directorio del operario.
                    $ssh->exec('mkdir /home/capstone/ftp/OperariosExternos/'.$rutEmpresa.'/'.$operario->rut);
                    $ssh->exec('mkdir /home/capstone/ftp/OperariosInternos/'.$rutEmpresa.'/'.$operario->rut);

                    //Se almacena el operario en la base de datos.
                    $operario->save();
                }
            }
        } 
            
        //Se liberan los recursos.       
        unset($SWERROR);
        unset($ssh);       

        return redirect('gestionop')->with('create','');

    }

    public function edit($id){

        $operario = Operario::FindOrFail($id);
        $empresa = Empresa::all();
        
        return view('gestionOperarios.edit', compact('operario','empresa'));
    }

    public function update(OperarioFormRequest $request, $id){

        //Carga el repositorio de errores.
        $SWERROR = new ErrorRepositorio();
        
        //Busca al operario dada una id de la tabla.
        $operario = Operario::findOrFail($id);

        //Se extrae el rut antiguo del operario.
        $rutOperarioTemp = $operario->rut;

        //Se obtiene el rut de la empresa del operario seleccionado.
        $rutEmpresa = Empresa::FindOrFail($operario->empresa_id)->rut;
        
        //Se añaden los nuevos parametros correspondientes.
        $operario->nombre = $request->get('nombre');
        $operario->rut = $request->get('rut');
        $operario->correo = $request->get('correo');
        $operario->tipoOperario = $request->get('tipoOperario');
        $operario->empresa_id = $request->get('empresa');


        //Se prepara la conexion al servidor FTP.
        $ssh = new SSH2($this->serverFTP);
              
        //Intenta hacer la conexion al servidor FTP.
        if(!$ssh->login($this->userFTP,$this->passFTP)){
            
            //[SWERROR 002]: Problema al ingresar las credenciales de usuario FTP.
            exit($SWERROR->ErrorActual(1));
        }else{

            //Verifica si el directorio existe.
            $estadoExiste = $ssh->exec('[ -d /home/capstone/ftp/OperariosExternos/'.$rutEmpresa.'/'.$rutOperarioTemp.' ] && echo "true" || echo "false"');
            
            //Limpia la informacion obtenida.
            $estadoExiste = $estadoExiste[0].$estadoExiste[1].$estadoExiste[2].$estadoExiste[3];
            
            if($estadoExiste != 'true'){

                //[SWERROR 009]: El operario no existe en el sistema FTP (Conflicto en OperariosExternos).
                exit($SWERROR->ErrorActual(8));
            }else{

                //Verifica si el directorio existe.
                $estadoExiste = $ssh->exec('[ -d /home/capstone/ftp/OperariosInternos/'.$rutEmpresa.'/'.$rutOperarioTemp.' ] && echo "true" || echo "false"');
                
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0].$estadoExiste[1].$estadoExiste[2].$estadoExiste[3];

                if($estadoExiste != 'true'){

                    //[SWERROR 010]: El operario no existe en el sistema FTP (Conflicto en OperariosInternos).
                    exit($SWERROR->ErrorActual(9));
                }else{

                    //Se crea el directorio del operario.
                    $ssh->exec('rm -r /home/capstone/ftp/OperariosExternos/'.$rutEmpresa.'/'.$operario->rut);
                    $ssh->exec('rm -r /home/capstone/ftp/OperariosInternos/'.$rutEmpresa.'/'.$operario->rut);

                    //Se envia el directorio de la empresa a la basura. (Version Opcional)
                    //$ssh->exec('gvfs-trash /home/capstone/ftp/OperariosExternos/'.$rutEmpresa.'/'.$operario->rut);
                    //$ssh->exec('gvfs-trash /home/capstone/ftp/OperariosInternos/'.$rutEmpresa.'/'.$operario->rut);

                    //Se almacena el operario en la base de datos.
                    $operario->update();
                }
            }
        }        

        //Se liberan los recursos.       
        unset($SWERROR);
        unset($ssh);   

        return redirect('gestionop');
    }

    public function destroy($id){

        //Carga el repositorio de errores.
        $SWERROR = new ErrorRepositorio();
        
        //Busca al operario dada una id de la tabla.
        $operario = Operario::findOrFail($id);

        //Se obtiene el rut de la empresa del operario seleccionado.
        $rutEmpresa = Empresa::FindOrFail($operario->empresa_id)->rut;


        //Se prepara la conexion al servidor FTP.
        $ssh = new SSH2($this->serverFTP);
              
        //Intenta hacer la conexion al servidor FTP.
        if(!$ssh->login($this->userFTP,$this->passFTP)){
            
            //[SWERROR 002]: Problema al ingresar las credenciales de usuario FTP.
            exit($SWERROR->ErrorActual(1));
        }else{

            //Verifica si el directorio existe.
            $estadoExiste = $ssh->exec('[ -d /home/capstone/ftp/OperariosExternos/'.$rutEmpresa.'/'.$rutOperarioTemp.' ] && echo "true" || echo "false"');
            
            //Limpia la informacion obtenida.
            $estadoExiste = $estadoExiste[0].$estadoExiste[1].$estadoExiste[2].$estadoExiste[3];
            
            if($estadoExiste != 'true'){

                //[SWERROR 009]: El operario no existe en el sistema FTP (Conflicto en OperariosExternos).
                exit($SWERROR->ErrorActual(8));
            }else{

                //Verifica si el directorio existe.
                $estadoExiste = $ssh->exec('[ -d /home/capstone/ftp/OperariosInternos/'.$rutEmpresa.'/'.$rutOperarioTemp.' ] && echo "true" || echo "false"');
                
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0].$estadoExiste[1].$estadoExiste[2].$estadoExiste[3];

                if($estadoExiste != 'true'){

                    //[SWERROR 010]: El operario no existe en el sistema FTP (Conflicto en OperariosInternos).
                    exit($SWERROR->ErrorActual(9));
                }else{

                    //Se crea el directorio del operario.
                    $ssh->exec('mv /home/capstone/ftp/OperariosExternos/'.$rutEmpresa.'/'.$rutOperarioTemp.' /home/capstone/ftp/OperariosExternos/'.$rutEmpresa.'/'.$operario->rut);
                    $ssh->exec('mv /home/capstone/ftp/OperariosInternos/'.$rutEmpresa.'/'.$rutOperarioTemp.' /home/capstone/ftp/OperariosInternos/'.$rutEmpresa.'/'.$operario->rut);

                    //Se almacena el operario en la base de datos.
                    $operario->delete();
                }
            }
        }        

        //Se liberan los recursos.       
        unset($SWERROR);
        unset($ssh);

        $operario->delete();

       

        return redirect()->back()->with('success','La empresa a sido eliminada.');


        

        
    }
}
