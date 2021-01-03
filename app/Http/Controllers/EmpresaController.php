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
use App\Http\gestionopController;

use App\Http\Controllers\ErrorRepositorio;
use App\Http\Controllers\FtpConexion;

use phpseclib\Net\SSH2;

class EmpresaController extends Controller{      

    public function __construct(){

        $this->middleware('auth');
    }

    public function index(Request $request){

        if($request){

            $query = trim($request->get('search'));

            $empresas = Empresa::where('nombreEmpresa',  'LIKE', '%' . $query . '%')
                ->orwhere('rutEmpresa',  'LIKE', '%' . $query . '%')
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

        //Prepara los parametros de conexion al servidor FTP.
        $ftpParameters = new FtpConexion();
        
        //Genera a la empresa y rellena los atributos con la informacion entregada por el usuario.
        
        $empresa = new Empresa();
        $empresa->rutEmpresa = request('rutEmpresa');
        $empresa->nombreEmpresa = request('nombreEmpresa');
        $empresa->compania = request('compania');      

        //Se prepara la conexion al servidor FTP.
        $ssh = new SSH2($ftpParameters->getServerFTP());
              
        //Intenta hacer la conexion al servidor FTP.
        if(!$ssh->login($ftpParameters->getUserFTP(),$ftpParameters->getPassFTP())){
            
            //TODO: Actualizar sistema de errores.
            //Se liberan los recursos.           
            unset($ssh);
            unset($ftpParameters);
            //[SWERROR 002]: Problema al ingresar las credenciales de usuario FTP.
            exit($SWERROR->ErrorActual(1));
            unset($SWERROR);

        }else{

            //Verifica si el directorio existe.
            $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$empresa->rutEmpresa.' ] && echo "1" || echo "0"');
            
            //Limpia la informacion obtenida.
            $estadoExiste = $estadoExiste[0];
            
            if($estadoExiste == '1'){

                //TODO: Actualizar sistema de errores.
                //Se liberan los recursos.
                unset($ssh);
                unset($ftpParameters);
                //[SWERROR 003]: La empresa ya existe en el sistema (Conflicto en OperariosExternos).
                exit($SWERROR->ErrorActual(2));
                unset($SWERROR);
            }else{

                //Verifica si el directorio existe.
                $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$empresa->rutEmpresa.' ] && echo "1" || echo "0"');
                
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0];

                if($estadoExiste == '1'){

                    //TODO: Actualizar sistema de errores.
                    //Se liberan los recursos.
                    unset($ssh);
                    unset($ftpParameters);
                    //[SWERROR 004]: La empresa ya existe en el sistema (Conflicto en OperariosInternos).
                    exit($SWERROR->ErrorActual(3));
                    unset($SWERROR);

                }else{

                    //Se almacena la empresa en la base de datos.
                    $empresa->save();

                    //Se crea el directorio de la empresa.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S mkdir /home/Externo/'.$empresa->rutEmpresa);
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S mkdir /home/Interno/'.$empresa->rutEmpresa);
                }
            }
        }
        
        //Finaliza secuencia de comandos.
        $ssh->exec('exit');
        //Se liberan los recursos.       
        unset($SWERROR);
        unset($ssh);  
        unset($ftpParameters);  

        return redirect('empresaop')->with('create','La empresa se a creado correctamente');
    }

    public function edit($id){
        
        return view('empresas.edit', ['empresa' => Empresa::findOrFail($id), 'activemenu' => 'empresa']);
    }

    public function update(Request $request, $id){

        //Carga el repositorio de errores.
        $SWERROR = new ErrorRepositorio();

        //Prepara los parametros de conexion al servidor FTP.
        $ftpParameters = new FtpConexion();        
        
        //Busca a la empresa dada una id de la tabla.
        $empresa = Empresa::findOrFail($id);        

        //Se extrae el rut antiguo de la empresa.
        $rutEmpresaTemp = $empresa->rutEmpresa;

        //Se añaden los nuevos parametros correspondientes.
        $empresa->rutEmpresa = $request->get('rutEmpresa');
        $empresa->nombreEmpresa = $request->get('nombreEmpresa');
        $empresa->compania = $request->get('compania');
        
        //Verifica si el nuevo rut de la empresa es diferente al antiguo.
        if($rutEmpresaTemp != $empresa->rutEmpresa){
            
            //Prepara la conexion al servidor FTP.
            $ssh = new SSH2($ftpParameters->getServerFTP());
              
            //Intenta hacer la conexion al servidor FTP.
            if(!$ssh->login($ftpParameters->getUserFTP(),$ftpParameters->getPassFTP())){
            
                //TODO: Actualizar sistema de errores.
                //Se liberan los recursos.
                unset($ssh);
                unset($ftpParameters);
                //[SWERROR 002]: Problema al ingresar las credenciales de usuario FTP.
                exit($SWERROR->ErrorActual(1));
                unset($SWERROR);
            }else{

                //Verifica si el directorio existe en el directorio Externo.
                $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$rutEmpresaTemp.' ] && echo "1" || echo "0"');
            
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0];
            
                if($estadoExiste != '1'){

                    //TODO: Actualizar sistema de errores.
                    //Se liberan los recursos.
                    unset($ssh);
                    unset($ftpParameters);
                    //[SWERROR 005]: La empresa no existe en el sistema FTP (Conflicto en directorio Externo).
                    exit($SWERROR->ErrorActual(4));
                    unset($SWERROR);
                }else{

                    //Verifica si el directorio existe en el directorio Interno.
                    $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$rutEmpresaTemp.' ] && echo "1" || echo "0"');
                
                    //Limpia la informacion obtenida.
                    $estadoExiste = $estadoExiste[0];

                    if($estadoExiste != '1'){

                        //TODO: Actualizar sistema de errores.
                        //Se liberan los recursos.
                        unset($ssh);
                        unset($ftpParameters);
                        //[SWERROR 006]: La empresa no existe en el sistema FTP (Conflicto en directorio Interno).
                        exit($SWERROR->ErrorActual(5));
                        unset($SWERROR);
                    }else{

                        //Se actualizan los cambios en la base de datos.
                        $empresa->update();

                        //Cambia el nombre del directorio de la empresa.
                        $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S mv /home/Externo/'.$rutEmpresaTemp.' /home/Externo/'.$empresa->rutEmpresa);
                        $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S mv /home/Interno/'.$rutEmpresaTemp.' /home/Interno/'.$empresa->rutEmpresa);

                        //Se obtienen todos los operarios vinculados la empresa.
                        $operarios = DB::table('operarios')
                            ->join('empresas', 'operarios.empresa_id', '=', 'empresas.id')
                            ->select('operarios.*')
                            ->get();

                        //Asigna nuevo home a los Operarios relacionados con la empresa.
                        foreach($operarios as $operario){

                            //El Operario es Interno, se le reasigna el home.
                            if($operario->tipoOperario=="Interno"){

                                $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S usermod -d /home/Interno/".$empresa->rutEmpresa." ".$operario->rutOperario);
                            }else{

                                //En cualquier otro caso, se establece Operario Externo por defecto.
                                $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S usermod -d /home/Externo/".$empresa->rutEmpresa."/".$operario->rutOperario." ".$operario->rutOperario);
                            }                      
                        }
                    }
                }
            }
        } 

        //Finaliza secuencia de comandos.
        $ssh->exec('exit');
        //Se liberan los recursos.       
        unset($SWERROR);
        unset($ssh);  
        unset($ftpParameters);  

        return redirect('empresaop')->with('edit','La empresa se a editado');
    }

    public function destroy($id){

        //Carga el repositorio de errores.
        $SWERROR = new ErrorRepositorio();

        //Prepara los parametros de conexion al servidor FTP.
        $ftpParameters = new FtpConexion();

        //Busca la empresa a eliminar.
        $empresa = Empresa::findOrFail($id);  

        //Se prepara la conexion al servidor FTP.
        $ssh = new SSH2($ftpParameters->getServerFTP());
              
        //Intenta hacer la conexion al servidor FTP.
        if(!$ssh->login($ftpParameters->getUserFTP(),$ftpParameters->getPassFTP())){

            //TODO: Actualizar sistema de errores.
            //Se liberan los recursos.
            unset($ssh);
            unset($ftpParameters);            
            //[SWERROR 002]: Problema al ingresar las credenciales de usuario FTP.
            exit($SWERROR->ErrorActual(1));
            unset($SWERROR);
        }else{

            //Verifica si el directorio existe.
            $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$empresa->rutEmpresa.' ] && echo "1" || echo "0"');
            
            //Limpia la informacion obtenida.
            $estadoExiste = $estadoExiste[0];
            
            if($estadoExiste != '1'){

                //TODO: Actualizar sistema de errores.
                //Se liberan los recursos.
                unset($ssh);
                unset($ftpParameters);
                //[SWERROR 005]: La empresa no existe en el sistema FTP (Conflicto en OperariosExternos).
                exit($SWERROR->ErrorActual(4));
                unset($SWERROR);
            }else{

                //Verifica si el directorio existe.
                $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$empresa->rutEmpresa.' ] && echo "1" || echo "0"');
                
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0];

                if($estadoExiste != '1'){

                    //TODO: Actualizar sistema de errores.
                    //Se liberan los recursos.
                    unset($ssh);
                    unset($ftpParameters);
                    //[SWERROR 006]: La empresa no existe en el sistema FTP (Conflicto en OperariosInternos).
                    exit($SWERROR->ErrorActual(5));
                    unset($SWERROR);
                }else{

                    //Se elimina el directorio de la empresa.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Externo/'.$empresa->rutEmpresa);
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Interno/'.$empresa->rutEmpresa);

                    //Se envia el directorio de la empresa a la basura. (Version Opcional)
                    //$ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S gvfs-trash /home/Externo/'.$empresa->rutEmpresa);
                    //$ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S gvfs-trash /home/Interno/'.$empresa->rutEmpresa);

                    //Se obtienen todos los operarios vinculados la empresa.
                    $operarios = DB::table('operarios')
                        ->join('empresas', 'operarios.empresa_id', '=', 'empresas.id')
                        ->select('operarios.*')
                        ->get();

                    //Elimina las cuentas de Operarios relacionadas con la empresa.
                    foreach($operarios as $operario){

                        $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S userdel '.$operario->rutOperario);
                    }

                    //Se elimina la empresa de la base de datos y los elementos relacionados a ella.
                    $empresa->operario()->delete();
                    $empresa->asignar()->delete();
                    $empresa->delete();                    
                }
            }
        }    
        
        //Finaliza secuencia de comandos.
        $ssh->exec('exit');
        //Se liberan los recursos.       
        unset($SWERROR);
        unset($ssh);  
        unset($ftpParameters);  

        return redirect()->back()->with('success','La empresa a sido eliminada.');         
    }
}