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

use App\Http\Controllers\ErrorRepositorio;

use phpseclib\Net\SSH2;

class EmpresaController extends Controller{  

    //IP del servidor FTP.
    private $serverFTP = '192.168.0.28';
    
    //Credenciales de usuario FTP
    private $userFTP= 'capstone';
    private $passFTP= 'capstone';


    public function __construct(){

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
            
            //TODO: Actualizar sistema de errores.
            //Se liberan los recursos.       
            unset($SWERROR);
            unset($ssh);
            //[SWERROR 002]: Problema al ingresar las credenciales de usuario FTP.
            exit($SWERROR->ErrorActual(1));

        }else{

            //Verifica si el directorio existe.
            $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$empresa->rut.' ] && echo "1" || echo "0"');
            
            //Limpia la informacion obtenida.
            $estadoExiste = $estadoExiste[0];
            
            if($estadoExiste == '1'){

                //TODO: Actualizar sistema de errores.
                //Se liberan los recursos.       
                unset($SWERROR);
                unset($ssh);
                //[SWERROR 003]: La empresa ya existe en el sistema (Conflicto en OperariosExternos).
                exit($SWERROR->ErrorActual(2));
            }else{

                //Verifica si el directorio existe.
                $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$empresa->rut.' ] && echo "1" || echo "0"');
                
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0];

                if($estadoExiste == '1'){

                    //TODO: Actualizar sistema de errores.
                    //Se liberan los recursos.       
                    unset($SWERROR);
                    unset($ssh);
                    //[SWERROR 004]: La empresa ya existe en el sistema (Conflicto en OperariosInternos).
                    exit($SWERROR->ErrorActual(3));

                }else{

                    //Se crea el directorio de la empresa.
                    $ssh->exec('echo '.$this->passFTP.' | sudo -S mkdir /home/Externo/'.$empresa->rut);
                    $ssh->exec('echo '.$this->passFTP.' | sudo -S mkdir /home/Interno/'.$empresa->rut);

                    //Se almacena la empresa en la base de datos.
                    $empresa->save();

                    //Se liberan los recursos.       
                    unset($SWERROR);
                    unset($ssh);
                }
            }
        } 
        return redirect('empresaop')->with('create','La empresa se a creado correctamente');
    }

    public function edit($id){
        
        return view('empresas.edit', ['empresa' => Empresa::findOrFail($id), 'activemenu' => 'empresa']);
    }

    public function update(Request $request, $id){

        //Carga el repositorio de errores.
        $SWERROR = new ErrorRepositorio();
        
        $user = Auth::user();
        
        //Busca a la empresa dada una id de la tabla.
        $empresa = Empresa::findOrFail($id);        

        //Se extrae el rut antiguo de la empresa.
        $rutTemp = $empresa->rut;

        //Se añaden los nuevos parametros correspondientes.
        $empresa->rut = $request->get('rut');
        $empresa->nombre = $request->get('nombre');
        $empresa->compania = $request->get('compania');
        
        //Verifica si el nuevo rut de la empresa es diferente al antiguo.
        if($rutTemp != $empresa->rut){

            //TODO: Actualizar sistema de errores.
            //Se liberan los recursos.       
            unset($SWERROR);
            unset($ssh);
            //Prepara la conexion al servidor FTP.
            $ssh = new SSH2($this->serverFTP);
              
            //Intenta hacer la conexion al servidor FTP.
            if(!$ssh->login($this->userFTP,$this->passFTP)){
            
                //TODO: Actualizar sistema de errores.
                //Se liberan los recursos.       
                unset($SWERROR);
                unset($ssh);
                //[SWERROR 002]: Problema al ingresar las credenciales de usuario FTP.
                exit($SWERROR->ErrorActual(1));
            }else{

                //Verifica si el directorio existe en el directorio Externo.
                $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$rutTemp.' ] && echo "1" || echo "0"');
            
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0];
            
                if($estadoExiste != '1'){

                    //TODO: Actualizar sistema de errores.
                    //Se liberan los recursos.       
                    unset($SWERROR);
                    unset($ssh);
                    //[SWERROR 005]: La empresa no existe en el sistema FTP (Conflicto en directorio Externo).
                    exit($SWERROR->ErrorActual(4));
                }else{

                    //Verifica si el directorio existe en el directorio Interno.
                    $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$rutTemp.' ] && echo "1" || echo "0"');
                
                    //Limpia la informacion obtenida.
                    $estadoExiste = $estadoExiste[0];

                    if($estadoExiste != '1'){

                        //TODO: Actualizar sistema de errores.
                        //Se liberan los recursos.       
                        unset($SWERROR);
                        unset($ssh);
                        //[SWERROR 006]: La empresa no existe en el sistema FTP (Conflicto en directorio Interno).
                        exit($SWERROR->ErrorActual(5));
                    }else{

                        //Cambia el nombre del directorio de la empresa.
                        $ssh->exec('echo '.$this->passFTP.' | sudo -S mv /home/Externo/'.$rutTemp.' /home/Externo/'.$empresa->rut);
                        $ssh->exec('echo '.$this->passFTP.' | sudo -S mv /home/Interno/'.$rutTemp.' /home/Interno/'.$empresa->rut);

                        //Se obtienen todos los operarios vinculados la empresa.
                        $operarios = DB::table('operarios')
                            ->join('empresas', 'operarios.empresa_id', '=', 'empresas.id')
                            ->select('operarios.*')
                            ->get();

                        //Asigna nuevo home a los Operarios relacionados con la empresa.
                        foreach($operarios as $operario){

                            //El Operario es Interno, se le reasigna el home.
                            if($operario->tipoOperario=="Interno"){

                                $ssh->exec('echo '.$this->passFTP." | sudo -S usermod -d /home/Interno/".$empresa->rut." ".$operario->rut);
                            }else{

                                //En cualquier otro caso, se establece Operario Externo por defecto.
                                $ssh->exec('echo '.$this->passFTP." | sudo -S usermod -d /home/Externo/".$empresa->rut."/".$operario->rut." ".$operario->rut);
                            }                      
                        }

                        //Se actualizan los cambios en la base de datos.
                        $empresa->update();
                        //Se liberan los recursos.       
                        unset($SWERROR);
                        unset($ssh);
                    }
                }
            }
        }    

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

            //TODO: Actualizar sistema de errores.
            //Se liberan los recursos.       
            unset($SWERROR);
            unset($ssh);            
            //[SWERROR 002]: Problema al ingresar las credenciales de usuario FTP.
            exit($SWERROR->ErrorActual(1));
        }else{

            //Verifica si el directorio existe.
            $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$empresa->rut.' ] && echo "1" || echo "0"');
            
            //Limpia la informacion obtenida.
            $estadoExiste = $estadoExiste[0];
            
            if($estadoExiste != '1'){

                //TODO: Actualizar sistema de errores.
                //Se liberan los recursos.       
                unset($SWERROR);
                unset($ssh);
                //[SWERROR 005]: La empresa no existe en el sistema FTP (Conflicto en OperariosExternos).
                exit($SWERROR->ErrorActual(4));
            }else{

                //Verifica si el directorio existe.
                $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$empresa->rut.' ] && echo "1" || echo "0"');
                
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0];

                if($estadoExiste != '1'){

                    //Se liberan los recursos.       
                    unset($SWERROR);
                    unset($ssh);
                    //[SWERROR 006]: La empresa no existe en el sistema FTP (Conflicto en OperariosInternos).
                    exit($SWERROR->ErrorActual(5));
                }else{

                    //Se elimina el directorio de la empresa.
                    $ssh->exec('echo '.$this->passFTP.' | sudo -S rm -r /home/Externo/'.$empresa->rut);
                    $ssh->exec('echo '.$this->passFTP.' | sudo -S rm -r /home/Interno/'.$empresa->rut);

                    //Se envia el directorio de la empresa a la basura. (Version Opcional)
                    //$ssh->exec('echo '.$this->passFTP.' | sudo -S gvfs-trash /home/Externo/'.$empresa->rut);
                    //$ssh->exec('echo '.$this->passFTP.' | sudo -S gvfs-trash /home/Interno/'.$empresa->rut);

                    //Se obtienen todos los operarios vinculados la empresa.
                    $operarios = DB::table('operarios')
                        ->join('empresas', 'operarios.empresa_id', '=', 'empresas.id')
                        ->select('operarios.*')
                        ->get();

                    //Elimina las cuentas de Operarios relacionadas con la empresa.
                    foreach($operarios as $operario){

                        $ssh->exec('echo '.$this->passFTP.' | sudo -S userdel '.$operario->rut);
                    }

                    $ssh->exec('exit');

                    //Se elimina la empresa de la base de datos.
                    $empresa->delete();
                       
                    //Se liberan los recursos.       
                    unset($SWERROR);
                    unset($ssh);
                }
            }
        }  
        
        return redirect()->back()->with('success','La empresa a sido eliminada.');         
    }
}