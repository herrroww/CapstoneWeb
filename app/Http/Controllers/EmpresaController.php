<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Empresa;
use App\User;
use App\AuditTrail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;

use App\Http\Controllers\ErrorRepositorio;

use phpseclib\Net\SSH2;

class EmpresaController extends Controller
{  

    //IP del servidor FTP.
    private $serverFTP = '192.168.0.28';
    
    //Credenciales de usuario FTP
    private $userFTP= 'capstone';
    private $passFTP= 'capstone';


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

        //Carga el repositorio de errores.
        $SWERROR = new ErrorRepositorio();
        
        /*Genera a la empresa y rellena los atributos con la informacion
        * entregada por el usuario.
        */
        $empresa = new Empresa();
        $empresa->rut = request('rut');
        $empresa->nombre = request('nombre');
        $empresa->compania = request('compania');      

        //Se prepara la conexion al servidor FTP.
        $ssh = new SSH2($this->serverFTP);
              
        //Intenta hacer la conexion al servidor FTP.
        if(!$ssh->login($this->userFTP,$this->passFTP)){
            
            //[SWERROR 002]: Problema al ingresar las credenciales de usuario FTP.
            exit($SWERROR->ErrorActual(1));
        }else{

            //Verifica si el directorio existe.
            $estadoExiste = $ssh->exec('[ -d /home/capstone/ftp/OperariosExternos/'.$empresa->rut.' ] && echo "true" || echo "false"');
            
            //Limpia la informacion obtenida.
            $estadoExiste = $estadoExiste[0].$estadoExiste[1].$estadoExiste[2].$estadoExiste[3];
            
            if($estadoExiste == 'true'){

                //[SWERROR 003]: La empresa ya existe en el sistema (Conflicto en OperariosExternos).
                exit($SWERROR->ErrorActual(2));
            }else{

                //Verifica si el directorio existe.
                $estadoExiste = $ssh->exec('[ -d /home/capstone/ftp/OperariosInternos/'.$empresa->rut.' ] && echo "true" || echo "false"');
                
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0].$estadoExiste[1].$estadoExiste[2].$estadoExiste[3];

                if($estadoExiste == 'true'){

                    //[SWERROR 004]: La empresa ya existe en el sistema (Conflicto en OperariosInternos).
                    exit($SWERROR->ErrorActual(3));
                }else{

                    //Se crea el directorio de la empresa.
                    $ssh->exec('mkdir /home/capstone/ftp/OperariosExternos/'.$empresa->rut);
                    $ssh->exec('mkdir /home/capstone/ftp/OperariosInternos/'.$empresa->rut);

                    //Se almacena la empresa en la base de datos.
                    $empresa->save();
                }
            }
        } 
            
        //Se liberan los recursos.       
        unset($SWERROR);
        unset($ssh);

        return redirect('empresaop')->with('create','La empresa se a creado correctamente');

    }

    public function edit($id){
        
        return view('empresas.edit', ['empresa' => Empresa::findOrFail($id), 'activemenu' => 'empresa']);
    }

    public function update(Request $request, $id){

        //Carga el repositorio de errores.
        $SWERROR = new ErrorRepositorio();
        $empresa = Empresa::findOrFail($id);

        $user = Auth::user();
        
        //Busca a la empresa dada una id de la tabla.
        $empresa = Empresa::findOrFail($id);

        //Se extrae el rut antiguo de la empresa.
        $rutTemp = $empresa->rut;

        //Se añaden los nuevos parametros correspondientes.
        $empresa->rut = $request->get('rut');
        $empresa->nombre = $request->get('nombre');
        $empresa->compania = $request->get('compania');
        
        //Se prepara la conexion al servidor FTP.
        $ssh = new SSH2($this->serverFTP);
              
        //Intenta hacer la conexion al servidor FTP.
        if(!$ssh->login($this->userFTP,$this->passFTP)){
            
            //[SWERROR 002]: Problema al ingresar las credenciales de usuario FTP.
            exit($SWERROR->ErrorActual(1));
        }else{

            //Verifica si el directorio existe.
            $estadoExiste = $ssh->exec('[ -d /home/capstone/ftp/OperariosExternos/'.$rutTemp.' ] && echo "true" || echo "false"');
            
            //Limpia la informacion obtenida.
            $estadoExiste = $estadoExiste[0].$estadoExiste[1].$estadoExiste[2].$estadoExiste[3];
            
            if($estadoExiste != 'true'){

                //[SWERROR 005]: La empresa no existe en el sistema FTP (Conflicto en OperariosExternos).
                exit($SWERROR->ErrorActual(4));
            }else{

                //Verifica si el directorio existe.
                $estadoExiste = $ssh->exec('[ -d /home/capstone/ftp/OperariosInternos/'.$rutTemp.' ] && echo "true" || echo "false"');
                
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0].$estadoExiste[1].$estadoExiste[2].$estadoExiste[3];

                if($estadoExiste != 'true'){

                    //[SWERROR 006]: La empresa no existe en el sistema FTP (Conflicto en OperariosInternos).
                    exit($SWERROR->ErrorActual(5));
                }else{

                    //Se cambia el nombre del directorio de la empresa.
                    $ssh->exec('mv /home/capstone/ftp/OperariosExternos/'.$rutTemp.' /home/capstone/ftp/OperariosExternos/'.$empresa->rut);
                    $ssh->exec('mv /home/capstone/ftp/OperariosInternos/'.$rutTemp.' /home/capstone/ftp/OperariosInternos/'.$empresa->rut);
                    
                    //Se actualizan los cambios en la base de datos.
                    $empresa->update();
                }
            }
        } 
            
        //Se liberan los recursos.       
        unset($SWERROR);
        unset($ssh);        

        return redirect('empresaop')->with('edit','La empresa se a editado');
    }

    public function destroy($id){

        //Carga el repositorio de errores.
        $SWERROR = new ErrorRepositorio();

        $empresa = Empresa::findOrFail($id);        

        //Se prepara la conexion al servidor FTP.
        $ssh = new SSH2($this->serverFTP);
              
        //Intenta hacer la conexion al servidor FTP.
        if(!$ssh->login($this->userFTP,$this->passFTP)){
            
            //[SWERROR 002]: Problema al ingresar las credenciales de usuario FTP.
            exit($SWERROR->ErrorActual(1));
        }else{

            //Verifica si el directorio existe.
            $estadoExiste = $ssh->exec('[ -d /home/capstone/ftp/OperariosExternos/'.$empresa->rut.' ] && echo "true" || echo "false"');
            
            //Limpia la informacion obtenida.
            $estadoExiste = $estadoExiste[0].$estadoExiste[1].$estadoExiste[2].$estadoExiste[3];
            
            if($estadoExiste != 'true'){

                //[SWERROR 005]: La empresa no existe en el sistema FTP (Conflicto en OperariosExternos).
                exit($SWERROR->ErrorActual(4));
            }else{

                //Verifica si el directorio existe.
                $estadoExiste = $ssh->exec('[ -d /home/capstone/ftp/OperariosInternos/'.$empresa->rut.' ] && echo "true" || echo "false"');
                
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0].$estadoExiste[1].$estadoExiste[2].$estadoExiste[3];

                if($estadoExiste != 'true'){

                    //[SWERROR 006]: La empresa no existe en el sistema FTP (Conflicto en OperariosInternos).
                    exit($SWERROR->ErrorActual(5));
                }else{

                    //Se elimina el directorio de la empresa.
                    $ssh->exec('rm -r /home/capstone/ftp/OperariosExternos/'.$empresa->rut);
                    $ssh->exec('rm -r /home/capstone/ftp/OperariosInternos/'.$empresa->rut);

                    //Se envia el directorio de la empresa a la basura. (Version Opcional)
                    //$ssh->exec('gvfs-trash /home/capstone/ftp/OperariosExternos/'.$empresa->rut);
                    //$ssh->exec('gvfs-trash /home/capstone/ftp/OperariosInternos/'.$empresa->rut);
                    
                    //Se elimina la empresa de la base de datos.
                    $empresa->delete();
                }
            }
        } 
            
        //Se liberan los recursos.       
        unset($SWERROR);
        unset($ssh);
        
        return redirect()->back()->with('success','La empresa a sido eliminada.'); 
        
    }
}