<?php

namespace App\Http\Controllers;

use App\Http\Requests\OperarioFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Operario;
use App\Empresa;
use App\Http\Controllers\ErrorRepositorio;
use App\Http\Controllers\FtpConexion;
use phpseclib\Net\SSH2;
use App\Asignar;


class gestionopController extends Controller{

    public function __construct(){

        $this->middleware('auth');
    }

    public function index(Request $request){

        if($request){
            
            $query = trim($request->get('search'));            

            $operarios = Operario::where('nombreOperario',  'LIKE', '%' . $query . '%')
                ->orwhere('rutOperario',  'LIKE', '%' . $query . '%')
                ->orwhere('id',  'LIKE', '%' . $query . '%')
                ->orderBy('id', 'asc')
                ->paginate(7);

            return view('gestionOperarios.index', ['operarios' => $operarios, 'search' => $query, 'activemenu' => 'operario']);
        }
    }
    
    public function create(){

        $empresa = Empresa::all();
        $operario = Operario::all();
        $data = array("lista_empresas" => $empresa);

        return view('gestionOperarios.create',['activemenu' => 'operario'],compact('empresa'));
    }   

    public function store(Request $request){

        //Carga el repositorio de errores.
        $SWERROR = new ErrorRepositorio();

        //Prepara los parametros de conexion al servidor FTP.
        $ftpParameters = new FtpConexion();

        //Genera al Operario y rellena los atributos con la informacion entregada por el usuario.        
        $operario = new Operario();


        $operario->nombreOperario = request('nombreOperario');
        //TODO: UTILIZAR VARIABLE RUTOPERARIOFTP PARA USUARIO FTP
        $operario->rutOperario = request('rutOperario');
        $operario->correoOperario = request('correoOperario');
        $operario->tipoOperario = request('tipoOperario');
        $operario->empresa_id = request('empresa');
        $operario->contraseniaOperario = Hash::make(request('contraseniaOperario'));
        $operario->nombreOperario = request('nombreOperario');
        $operario->telefonoOperario = request('telefonoOperario');

        //$operario->rutOperarioFTP = preg_replace("/[^A-Za-z0-9]/",'',$operario->rutOperario);
        $operario->contraseniaOperarioFTP = substr(preg_replace("/[^A-Za-z0-9]/","",$operario->contraseniaOperario),0,10);

        //Obtiene el rut de la Empresa seleccionada.
        $rutEmpresa = Empresa::FindOrFail($operario->empresa_id)->rutEmpresa;
 
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
            $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$rutEmpresa.'/'.$operario->rutOperario.' ] && echo "1" || echo "0"');
            
            //Limpia la informacion obtenida.
            $estadoExiste = $estadoExiste[0];
            
            if($estadoExiste == '1'){

                //TODO: Actualizar formato de error.
                //Se liberan los recursos.
                unset($ssh);
                unset($ftpParameters);
                //[SWERROR 007]: El Operario ya existe en el sistema FTP (Conflicto en OperariosExternos).
                exit($SWERROR->ErrorActual(6));
                unset($SWERROR);
            }else{

                //Verifica si el directorio existe.
                $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$rutEmpresa.'/'.$operario->rutOperario.' ] && echo "1" || echo "0"');
                
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0];

                if($estadoExiste == '1'){

                    //TODO: Actualizar formato de error.
                    //Se liberan los recursos.
                    unset($ssh);
                    unset($ftpParameters);
                    //[SWERROR 008]: El Operario ya existe en el sistema FTP (Conflicto en OperariosInternos).
                    exit($SWERROR->ErrorActual(7));
                    unset($SWERROR);
                }else{
                    
                    //Almacena el Operario en la base de datos.
                    $operario->save();
                    
                    //Crea al Operario y lo asigna al grupo "operariosftp".
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S useradd -g operariosftp -s /bin/bash -p $(echo '.$operario->contraseniaOperarioFTP.' | openssl passwd -1 -stdin) '.$operario->rutOperario); 
                    
                    //Crea la carpeta del Operario en la carpeta Interno y le asigna el grupo "operariosftp".
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S mkdir -p /home/Externo/'.$rutEmpresa.'/'.$operario->rutOperario);
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S chown -R '.$operario->rutOperario.':nogroup /home/Externo/'.$rutEmpresa.'/'.$operario->rutOperario);
                    
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S mkdir -p /home/Interno/'.$rutEmpresa.'/'.$operario->rutOperario);
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S chown -R '.$operario->rutOperario.':operariosftp /home/Interno/'.$rutEmpresa.'/'.$operario->rutOperario);
                    
                    //Añade al Operario en la lista de permisos VSFTPD y SSHD.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S sed -i '$ a ".$operario->rutOperario."' /etc/vsftpd.userlist");
                    $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S sed -i '$ a DenyUsers ".$operario->rutOperario."' /etc/ssh/sshd_config");
                    

                    //El Operario es Interno, se le reasigna el home.
                    if($operario->tipoOperario=="Interno"){

                        $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S usermod -d /home/Interno/".$rutEmpresa." ".$operario->rutOperario);
                    }else{

                        //En cualquier otro caso, se establece Operario Externo por defecto.
                        $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S usermod -d /home/Externo/".$rutEmpresa."/".$operario->rutOperario." ".$operario->rutOperario);
                    }

                    //Reinicio de servicios para actualizar permisos.                    
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S service vsftpd restart');
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S service sshd restart');                    
                }
            }
        }

        //Finaliza secuencia de comandos.
        $ssh->exec('exit');
        //Se liberan los recursos.       
        unset($SWERROR);
        unset($ssh);  
        unset($ftpParameters);  

        return redirect('gestionop')->with('create','');
    }

    public function edit($id){

        $operario = Operario::FindOrFail($id);
        $empresa = Empresa::all();
        return view('gestionOperarios.edit', ['activemenu' => 'operario'],compact('operario','empresa'));
    }

    public function update(OperarioFormRequest $request, $id){

        //Carga el repositorio de errores.
        $SWERROR = new ErrorRepositorio();

        //Prepara los parametros de conexion al servidor FTP.
        $ftpParameters = new FtpConexion();
        
        //Busca al Operario dada una id de la tabla.
        $operario = Operario::findOrFail($id);

        //Extrae el rut y tipo (Externo o Interno) antiguo del Operario.
        $rutOperarioTemp = $operario->rutOperario;
        $tipoOperarioTemp = $operario->tipoOperario;

        //Obtiene el rut de la Empresa del Operario seleccionado.
        $rutEmpresaTemp = Empresa::FindOrFail($operario->empresa_id)->rutEmpresa;
        
        //Añaden los nuevos parametros correspondientes.        
        $operario->nombreOperario = $request->get('nombreOperario');
        $operario->rutOperario = $request->get('rutOperario');
        $operario->correoOperario = $request->get('correoOperario');
        $operario->tipoOperario = $request->get('tipoOperario');
        $operario->empresa_id = $request->get('empresa');
        $operario->contraseniaOperario = Hash::make(request('contraseniaOperario'));
        $operario->telefonoOperario =  $request->get('telefonoOperario');

        //Flags para actualizar datos de Operario.
        $actualizarGestionOperario = true;
        $actualizarGestionEmpresa = true;
        
        //Obtiene el rut de la nueva Empresa.
        $rutEmpresa = Empresa::FindOrFail($operario->empresa_id)->rutEmpresa;

        //Se prepara la conexion al servidor FTP.
        $ssh = new SSH2($ftpParameters->getServerFTP);
              
        //Intenta hacer la conexion al servidor FTP.
        if(!$ssh->login($ftpParameters->getUserFTP,$ftpParameters->getPassFTP)){
            
            //TODO: Actualizar formato de error.
            //Se liberan los recursos.
            unset($ssh);
            unset($ftpParameters);
            //[SWERROR 002]: Problema al ingresar las credenciales de usuario FTP.
            exit($SWERROR->ErrorActual(1));
            unset($SWERROR);
        }else{

            //Verifica si hay algun cambio en el rut del Operario.
            if($rutOperarioTemp != $operario->rutOperario){

                //Verifica si el directorio del Operario existe en el directorio Externo.
                $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$rutEmpresaTemp.'/'.$rutOperarioTemp.' ] && echo "1" || echo "0"');
            
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0];
            
                if($estadoExiste == '0'){

                    //TODO: Actualizar formato de error.
                    //Se liberan los recursos.
                    unset($ssh);
                    unset($ftpParameters);
                    //[SWERROR 007]: El Operario no existe en el sistema FTP (Conflicto en directorio 'Externo').
                    $actualizarGestionOperario = false;
                    exit($SWERROR->ErrorActual(6));
                    unset($SWERROR);
                }else{

                    //Verifica si el directorio existe en el directorio Interno.
                    $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$rutEmpresaTemp.'/'.$rutOperarioTemp.' ] && echo "1" || echo "0"');
                
                    //Limpia la informacion obtenida.
                    $estadoExiste = $estadoExiste[0];

                    if($estadoExiste == '0'){

                        //TODO: Actualizar formato de error.
                        //Se liberan los recursos.
                        unset($ssh);
                        unset($ftpParameters);
                        //[SWERROR 008]: El Operario no existe en el sistema FTP (Conflicto en directorio 'Interno').
                        $actualizarGestionOperario = false;
                        exit($SWERROR->ErrorActual(7));
                        unset($SWERROR);
                    }
                }
            }

            //Verifica si el Operario pertenece a una nueva Empresa.
            if($rutEmpresaTemp != $rutEmpresa){                

                //Verifica si el directorio de la Empresa origen existe en el directorio Externo.
                $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$rutEmpresaTemp.' ] && echo "1" || echo "0"');
            
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0];
            
                if($estadoExiste == '0'){

                    //TODO: Actualizar formato de error.
                    //Se liberan los recursos.
                    unset($ssh);
                    unset($ftpParameters);
                    //La empresa origen no existe.
                    $actualizarGestionEmpresa = false;
                    exit($SWERROR->ErrorActual(6));
                    unset($SWERROR);
                }else{

                    //Verifica si el directorio de la Empresa origen existe en el directorio Interno.
                    $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$rutEmpresaTemp.' ] && echo "1" || echo "0"');
                
                    //Limpia la informacion obtenida.
                    $estadoExiste = $estadoExiste[0];

                    if($estadoExiste == '0'){

                        //TODO: Actualizar formato de error.
                        //Se liberan los recursos.
                        unset($ssh);
                        unset($ftpParameters);
                        //la empresa origen no existe.
                        $actualizarGestionEmpresa = false;
                        exit($SWERROR->ErrorActual(7));
                        unset($SWERROR);
                    }else{
                     
                        //Verifica si el directorio de la Empresa Destino existe en el directorio Externo.
                        $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$rutEmpresa.' ] && echo "1" || echo "0"');
            
                        //Limpia la informacion obtenida.
                        $estadoExiste = $estadoExiste[0];
            
                        if($estadoExiste == '0'){

                            //TODO: Actualizar formato de error.
                            //Se liberan los recursos.
                            unset($ssh);
                            unset($ftpParameters);
                            //la empresa destino no existe.
                            $actualizarGestionEmpresa = false;
                            exit($SWERROR->ErrorActual(6));
                            unset($SWERROR);
                        }else{

                            //Verifica si el directorio de la Empresa destino existe en el directorio Interno.
                            $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$rutEmpresa.' ] && echo "1" || echo "0"');
                
                            //Limpia la informacion obtenida.
                            $estadoExiste = $estadoExiste[0];

                            if($estadoExiste == '0'){

                                //TODO: Actualizar formato de error.
                                //Se liberan los recursos.
                                unset($ssh);
                                unset($ftpParameters);
                                //la empresa destino no existe.
                                $actualizarGestionEmpresa = false;
                                exit($SWERROR->ErrorActual(7));
                                unset($SWERROR);
                            }
                        }
                    }
                }                
            }
            
            //Verifica si es posible realizar los cambios.
            if($actualizarGestionOperario == true && $actualizarGestionEmpresa == true){
                

                //Actualiza los cambios en la Base de Datos.
                $operario->update(); 

                if($rutOperarioTemp != $operario->rutOperario){

                    //Actualiza el nuevo username del Operario.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP.' | sudo -S usermod -l '.$operario->rutOperario.' '.$rutOperarioTemp);

                    //Renombra los directorios relacionados al Operario.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP.' | sudo -S mv /home/Externo/'.$rutEmpresaTemp.'/'.$rutOperarioTemp.' /home/Externo/'.$rutEmpresaTemp.'/'.$operario->rutOperario);
                    $ssh->exec('echo '.$ftpParameters->getPassFTP.' | sudo -S mv /home/Externo/'.$rutEmpresaTemp.'/'.$rutOperarioTemp.' /home/Externo/'.$rutEmpresaTemp.'/'.$operario->rutOperario);

                    //Añade el nuevo username del operario en la lista de permisos VSFTPD y SSHD.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP." | sudo -S sed -i '$ a ".$operario->rutOperario."' /etc/vsftpd.userlist");
                    $ssh->exec('echo '.$ftpParameters->getPassFTP." | sudo -S sed -i '$ a DenyUsers ".$operario->rutOperario."' /etc/ssh/sshd_config");

                    //TODO: ELIMINA EL ANTIGUO RUT DEL OPERARIO DE LOS SERVICIOS.

                    //Reinicio de servicios para actualizar permisos.                    
                    $ssh->exec('echo '.$ftpParameters->getPassFTP.' | sudo -S service vsftpd restart');
                    $ssh->exec('echo '.$ftpParameters->getPassFTP.' | sudo -S service sshd restart');
                }

                if($rutEmpresaTemp != $rutEmpresa){  

                    //Mueve la carpeta del Operario a la nueva Empresa.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP.' | sudo -S mv /home/Externo/'.$rutEmpresaTemp.'/'.$operario->rutOperario.' /home/Externo/'.$rutEmpresa.'/'.$operario->rutOperario);
                    $ssh->exec('echo '.$ftpParameters->getPassFTP.' | sudo -S mv /home/Interno/'.$rutEmpresaTemp.'/'.$operario->rutOperario.' /home/Interno/'.$rutEmpresa.'/'.$operario->rutOperario);
                }

                //El Operario es Interno, se le reasigna el home.
                if($operario->tipoOperario=="Interno"){

                    $ssh->exec('echo '.$ftpParameters->getPassFTP." | sudo -S usermod -d /home/Interno/".$rutEmpresa." ".$operario->rutOperario);
                }else{

                    //En cualquier otro caso, se establece Operario Externo por defecto.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP." | sudo -S usermod -d /home/Externo/".$rutEmpresa."/".$operario->rutOperario." ".$operario->rutOperario);
                }       
            }            
        } 
            
        //Finaliza secuencia de comandos.
        $ssh->exec('exit');
        //Se liberan los recursos.       
        unset($SWERROR);
        unset($ssh);  
        unset($ftpParameters);         

        return redirect('gestionop')->with('edit','El operario se a editado');
    }

    public function destroy($id){

        //Carga el repositorio de errores.
        $SWERROR = new ErrorRepositorio();

        //Prepara los parametros de conexion al servidor FTP.
        $ftpParameters = new FtpConexion();
        
        //Busca al operario dada una id de la tabla.
        $operario = Operario::findOrFail($id);

        //Se obtiene el rut de la empresa del operario seleccionado.
        $rutEmpresa = Empresa::FindOrFail($operario->empresa_id)->rutEmpresa;   
        
        //Se prepara la conexion al servidor FTP.
        $ssh = new SSH2($ftpParameters->getServerFTP);
              
        //Intenta hacer la conexion al servidor FTP.
        if(!$ssh->login($ftpParameters->getUserFTP,$ftpParameters->getPassFTP)){        
            
            //TODO: Actualizar formato de error.
            //Se liberan los recursos.
            unset($ssh);
            unset($ftpParameters);
            //[SWERROR 002]: Problema al ingresar las credenciales de usuario FTP.
            exit($SWERROR->ErrorActual(1));
            unset($SWERROR);
        }else{

            //Verifica si el directorio existe.
            $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$rutEmpresa.'/'.$operario->rutOperario.' ] && echo "1" || echo "0"');
            
            //Limpia la informacion obtenida.
            $estadoExiste = $estadoExiste[0];
            
            if($estadoExiste != '1'){

                //TODO: Actualizar formato de error.
                //Se liberan los recursos.
                unset($ssh);
                unset($ftpParameters);
                //[SWERROR 009]: El operario no existe en el sistema FTP (Conflicto en Externo).
                exit($SWERROR->ErrorActual(8));
                unset($SWERROR);
            }else{

                //Verifica si el directorio existe.
                $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$rutEmpresa.'/'.$operario->rutOperario.' ] && echo "1" || echo "0"');
            
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0];
            
                if($estadoExiste != '1'){

                    //TODO: Actualizar formato de error.
                    //Se liberan los recursos.
                    unset($ssh);
                    unset($ftpParameters);
                    //[SWERROR 009]: El operario no existe en el sistema FTP (Conflicto en Internos).
                    exit($SWERROR->ErrorActual(8));
                    unset($SWERROR);
                }else{

                    $ssh->exec('echo '.$ftpParameters->getPassFTP.' | sudo -S userdel '.$operario->rutOperario);

                    //Se elimina los directorios del operario. (Opcion 1)                  
                    $ssh->exec('echo '.$ftpParameters->getPassFTP.' | sudo -S rm -r /home/Externo/'.$rutEmpresa.'/'.$operario->rutOperario);
                    $ssh->exec('echo '.$ftpParameters->getPassFTP.' | sudo -S rm -r /home/Interno/'.$rutEmpresa.'/'.$operario->rutOperario);

                    //Se envia el directorio de la empresa a la basura. (Opcion 2)
                    //$ssh->exec('echo '.$ftpParameters->getPassFTP.' | sudo -S gvfs-trash /home/Externo/'.$rutEmpresa.'/'.$operario->rutOperario);
                    //$ssh->exec('echo '.$ftpParameters->getPassFTP.' | sudo -S gvfs-trash /home/Interno/'.$rutEmpresa.'/'.$operario->rutOperario);

                    //Se elimina el operario en la base de datos.
                    $operario->asignar()->delete();
                    $operario->delete();    
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
