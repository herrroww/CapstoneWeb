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
            
            //Se liberan los recursos.           
            unset($ssh,$ftpParameters,$empresa);
            //[FTP-ERROR002]: Problema con las credenciales del servidor FTP.
            exit($SWERROR->ErrorActual('FTPERROR002'));
            unset($SWERROR);

        }else{

            //Verifica si la Empresa existe en el directorio Externo.
            $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$empresa->rutEmpresa.' ] && echo "1" || echo "0"');
            
            //Limpia la informacion obtenida.
            $estadoExiste = $estadoExiste[0];
            
            if($estadoExiste == '1'){

                //Se liberan los recursos.
                unset($ssh,$ftpParameters,$empresa);
                //[FTP-ERROR003]: La empresa ya existe en el sistema (Conflicto en directorio Externo).
                exit($SWERROR->ErrorActual('FTPERROR003'));
                unset($SWERROR);
            }else{

                //Verifica si la Empresa existe en el directorio Interno.
                $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$empresa->rutEmpresa.' ] && echo "1" || echo "0"');
                
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0];

                if($estadoExiste == '1'){

                    //Se liberan los recursos.
                    unset($ssh,$ftpParameters,$empresa);
                    //[FTP-ERROR004]: La empresa ya existe en el sistema (Conflicto en directorio Interno).
                    exit($SWERROR->ErrorActual('FTPERROR004'));
                    unset($SWERROR);

                }else{

                    //Se añade al historico de gestion.
                    DB::table('historicogestion')->insert(['nombreGestion' => 'Empresa', 
                                                           'tipoGestion' => 'Crear',
                                                           'responsableGestion' => $ftpParameters->getUserFTP(),
                                                           'descripcionGestion' => 'Se ha Creado => Empresa: '.$empresa->nombreEmpresa.', Rut: '.$empresa->rutEmpresa.', Compañia: '.$empresa->compania,
                                                           'created_at' => now()]);
                    
                    //Se almacena la empresa en la base de datos.
                    $empresa->save();

                    
                    //Se crea el directorio de la empresa.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S mkdir -p /home/Externo/'.$empresa->rutEmpresa);
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S mkdir -p /home/Interno/'.$empresa->rutEmpresa);
                }
            }
        }
        
        //Finaliza secuencia de comandos.
        $ssh->exec('exit');
        //Se liberan los recursos.       
        unset($SWERROR,$ssh,$ftpParameters,$empresa);

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

        //Se la informacion antigua de la empresa.
        $rutEmpresaTemp = $empresa->rutEmpresa;
        $nombreEmpresaTemp = $empresa->nombreEmpresa;
        $companiaEmpresaTemp = $empresa->compania;

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

                //Se liberan los recursos.           
                unset($ssh,$ftpParameters,$empresa);
                //[FTP-ERROR002]: Problema con las credenciales del servidor FTP.
                exit($SWERROR->ErrorActual('FTPERROR002'));
                unset($SWERROR);
            }else{

                //Verifica si la Empresa existe en el directorio Externo.
                $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$rutEmpresaTemp.' ] && echo "1" || echo "0"');
            
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0];
            
                if($estadoExiste != '1'){

                    //Se liberan los recursos.           
                    unset($ssh,$ftpParameters,$empresa);
                    //[FTP-ERROR005]: La empresa no existe en el sistema (Conflicto en directorio Externo).
                    exit($SWERROR->ErrorActual('FTPERROR005'));
                    unset($SWERROR);
                }else{

                    //Verifica si el directorio existe en el directorio Interno.
                    $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$rutEmpresaTemp.' ] && echo "1" || echo "0"');
                
                    //Limpia la informacion obtenida.
                    $estadoExiste = $estadoExiste[0];

                    if($estadoExiste != '1'){

                        //Se liberan los recursos.           
                        unset($ssh,$ftpParameters,$empresa);
                        //[FTP-ERROR006]: La empresa no existe en el sistema (Conflicto en directorio Interno).
                        exit($SWERROR->ErrorActual('FTPERROR006'));
                        unset($SWERROR);
                    }else{

                        //Se añade al historico de gestion.
                        DB::table('historicogestion')->insert(['nombreGestion' => 'Empresa', 
                                                               'tipoGestion' => 'Editar',
                                                               'responsableGestion' => $ftpParameters->getUserFTP(),
                                                               'descripcionGestion' => 'Modificacion Actual => Empresa: '.$empresa->nombreEmpresa.', Rut: '.$empresa->rutEmpresa.', Compañia: '.$empresa->compania.' | Datos Antiguos => Empresa: '.$nombreEmpresaTemp.', Rut: '.$rutEmpresaTemp.', Compañia: '.$companiaEmpresaTemp,
                                                               'created_at' => now()]);

                        //Se actualizan los cambios en la base de datos.
                        $empresa->update();

                        //Cambia el nombre del directorio de la empresa.
                        $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S mv /home/Externo/'.$rutEmpresaTemp.' /home/Externo/'.$empresa->rutEmpresa);
                        $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S mv /home/Interno/'.$rutEmpresaTemp.' /home/Interno/'.$empresa->rutEmpresa);

                        //Se obtienen todos los operarios vinculados a la empresa.                        
                        $operarios = DB::table('operarios')
                            ->where('operarios.empresa_id', '=', $empresa->id)
                            ->select('*')
                            ->get();
                            
                        //Asigna nuevo home a los Operarios relacionados con la empresa.
                        foreach($operarios as $operario){

                            //El Operario es Interno, se le reasigna el home.
                            if($operario->tipoOperario=="Interno"){

                                $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S usermod -d /home/Interno/ ".$operario->rutOperarioFTP);
                            }else{

                                //En cualquier otro caso, se establece Operario Externo por defecto.
                                $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S usermod -d /home/Externo/".$empresa->rutEmpresa."/".$operario->rutOperarioFTP." ".$operario->rutOperarioFTP);
                            }                      
                        }
                    }
                }
            }
        } 

        //Finaliza secuencia de comandos.
        $ssh->exec('exit'); 
        //Se liberan los recursos.           
        unset($SWERROR,$ssh,$ftpParameters,$empresa);

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
         
            //Se liberan los recursos.           
            unset($ssh,$ftpParameters,$empresa);
            //[FTP-ERROR002]: Problema con las credenciales del servidor FTP.
            exit($SWERROR->ErrorActual('FTPERROR002'));
            unset($SWERROR);
        }else{

            //Verifica si el directorio existe.
            $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$empresa->rutEmpresa.' ] && echo "1" || echo "0"');
            
            //Limpia la informacion obtenida.
            $estadoExiste = $estadoExiste[0];
            
            if($estadoExiste != '1'){

                //Se liberan los recursos.           
                unset($ssh,$ftpParameters,$empresa);
                //[FTP-ERROR005]: La empresa no existe en el sistema (Conflicto en directorio Externo).
                exit($SWERROR->ErrorActual('FTPERROR005'));
                unset($SWERROR);
            }else{

                //Verifica si el directorio existe.
                $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$empresa->rutEmpresa.' ] && echo "1" || echo "0"');
                
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0];

                if($estadoExiste != '1'){

                    //Se liberan los recursos.           
                    unset($ssh,$ftpParameters,$empresa);
                    //[FTP-ERROR006]: La empresa no existe en el sistema (Conflicto en directorio Interno).
                    exit($SWERROR->ErrorActual('FTPERROR006'));
                    unset($SWERROR);
                }else{

                    //Se añade al historico de gestion.
                    DB::table('historicogestion')->insert(['nombreGestion' => 'Empresa', 
                                                           'tipoGestion' => 'Eliminar',
                                                           'responsableGestion' => $ftpParameters->getUserFTP(),
                                                           'descripcionGestion' => 'Se ha Eliminado => Empresa: '.$empresa->nombreEmpresa.', Rut: '.$empresa->rutEmpresa.', Compañia: '.$empresa->compania,
                                                           'created_at' => now()]);

                    //Se elimina el directorio de la empresa.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Externo/'.$empresa->rutEmpresa);
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Interno/'.$empresa->rutEmpresa);

                    //Se envia el directorio de la empresa a la basura. (Version Opcional)
                    //$ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S gvfs-trash /home/Externo/'.$empresa->rutEmpresa);
                    //$ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S gvfs-trash /home/Interno/'.$empresa->rutEmpresa);

                    //Se obtienen todos los operarios vinculados la empresa.
                    $operarios = DB::table('operarios')
                        ->where('operarios.empresa_id', '=', $empresa->id)
                        ->select('operarios.*')
                        ->get();

                    //Elimina las cuentas de Operarios relacionadas con la empresa y las desvincula del servicio FTP.
                    foreach($operarios as $operario){

                        $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S userdel '.$operario->rutOperarioFTP);

                        $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S sed -i '/".$operario->rutOperarioFTP."/d' /etc/vsftpd.userlist");
                        $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S sed -i '/DenyUsers ".$operario->rutOperarioFTP."/d' /etc/ssh/sshd_config");  
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
        unset($SWERROR,$ssh,$ftpParameters,$empresa);

        return redirect()->back()->with('success','La empresa a sido eliminada.');         
    }
}