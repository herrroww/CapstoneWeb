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
use DB;



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
        $operario->contraseniaOperarioFTP = substr(preg_replace("/[^A-Za-z0-9]/","",$operario->contraseniaOperario),0,5);

        //Obtiene el rut de la Empresa seleccionada.
        $rutEmpresa = Empresa::FindOrFail($operario->empresa_id)->rutEmpresa;
 
        //Prepara la conexion al servidor FTP.
        $ssh = new SSH2($ftpParameters->getServerFTP());
              
        //Intenta hacer la conexion al servidor FTP.
        if(!$ssh->login($ftpParameters->getUserFTP(),$ftpParameters->getPassFTP())){
            
            //Se liberan los recursos.           
            unset($ssh,$ftpParameters,$operario);
            //[FTP-ERROR002]: Problema con las credenciales del servidor FTP.
            exit($SWERROR->ErrorActual('FTPERROR002'));
            unset($SWERROR);
        }else{

            //Verifica si el directorio existe.
            $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$rutEmpresa.'/'.$operario->rutOperario.' ] && echo "1" || echo "0"');
            
            //Limpia la informacion obtenida.
            $estadoExiste = $estadoExiste[0];
            
            if($estadoExiste == '1'){

                //Se liberan los recursos.           
                unset($ssh,$ftpParameters,$operario);
                //[FTP-ERROR007]: El Operario ya existe en el sistema (Conflicto en directorio Externo).
                exit($SWERROR->ErrorActual('FTPERROR007'));
                unset($SWERROR);
            }else{

                //Verifica si el directorio existe.
                $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$rutEmpresa.'/'.$operario->rutOperario.' ] && echo "1" || echo "0"');
                
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0];

                if($estadoExiste == '1'){

                    //Se liberan los recursos.           
                    unset($ssh,$ftpParameters,$operario);
                    //[FTP-ERROR008]: El Operario ya existe en el sistema (Conflicto en directorio Interno).
                    exit($SWERROR->ErrorActual('FTPERRROR008'));
                    unset($SWERROR);
                }else{
                    
                    //Almacena el Operario en la base de datos.
                    $operario->save();

                    //Crea al Operario y lo asigna al grupo "operariosftp".
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S useradd -s /bin/bash -p $(echo '.$operario->contraseniaOperarioFTP.' | openssl passwd -1 -stdin) '.$operario->rutOperario); 
                    
                    //Crea la carpeta del Operario en la carpeta Interno y le asigna el grupo "operariosftp".
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S mkdir -p /home/Externo/'.$rutEmpresa.'/'.$operario->rutOperario);
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S chown -R '.$operario->rutOperario.' /home/Externo/'.$rutEmpresa.'/'.$operario->rutOperario);
                    
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S mkdir -p /home/Interno/'.$rutEmpresa.'/'.$operario->rutOperario);
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S chown -R '.$operario->rutOperario.' /home/Interno/'.$rutEmpresa.'/'.$operario->rutOperario);
                    
                    //Añade al Operario en la lista de permisos VSFTPD y SSHD.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S sed -i '$ a ".$operario->rutOperario."' /etc/vsftpd.userlist");
                    $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S sed -i '$ a DenyUsers ".$operario->rutOperario."' /etc/ssh/sshd_config");
                    

                    //El Operario es Interno, se le reasigna el home.
                    if($operario->tipoOperario=="Interno"){

                        $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S usermod -m -d /home/Interno/ ".$operario->rutOperario);
                    }else{

                        //En cualquier otro caso, se establece Operario Externo por defecto.
                        $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S usermod -m -d /home/Externo/".$rutEmpresa."/".$operario->rutOperario." ".$operario->rutOperario);
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
        unset($SWERROR,$ssh,$ftpParameters,$operario);

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
        $ssh = new SSH2($ftpParameters->getServerFTP());
              
        //Intenta hacer la conexion al servidor FTP.
        if(!$ssh->login($ftpParameters->getUserFTP(),$ftpParameters->getPassFTP())){
            
            //Se liberan los recursos.           
            unset($ssh,$ftpParameters,$operario);
            //[FTP-ERROR002]: Problema con las credenciales del servidor FTP.
            exit($SWERROR->ErrorActual('FTPERROR002'));
            unset($SWERROR);
        }else{

            //Verifica si hay algun cambio en el rut del Operario.
            if($rutOperarioTemp != $operario->rutOperario){

                //Verifica si el directorio del Operario existe en el directorio Externo.
                $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$rutEmpresaTemp.'/'.$rutOperarioTemp.' ] && echo "1" || echo "0"');
            
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0];
            
                if($estadoExiste == '0'){

                    //Se liberan los recursos.           
                    unset($ssh,$ftpParameters,$operario);
                    //[FTP-ERROR009]: El Operario no existe en el sistema (Conflicto en directorio Externo).
                    $actualizarGestionOperario = false;
                    exit($SWERROR->ErrorActual('FTPERROR009'));
                    unset($SWERROR);
                }else{

                    //Verifica si el directorio del Operario existe en el directorio Interno.
                    $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$rutEmpresaTemp.'/'.$rutOperarioTemp.' ] && echo "1" || echo "0"');
                
                    //Limpia la informacion obtenida.
                    $estadoExiste = $estadoExiste[0];

                    if($estadoExiste == '0'){

                        //Se liberan los recursos.           
                        unset($ssh,$ftpParameters,$operario);
                        //[FTP-ERROR010]: El Operario no existe en el sistema (Conflicto en directorio Interno).
                        $actualizarGestionOperario = false;
                        exit($SWERROR->ErrorActual('FTPERROR010'));
                        unset($SWERROR);
                    }else{

                        //Verifica si el Operario con el nuevo rut ya existe en el directorio de la Empresa(Externo).
                        $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$rutEmpresaTemp.'/'.$operario->rutOperario.' ] && echo "1" || echo "0"');
                
                        //Limpia la informacion obtenida.
                        $estadoExiste = $estadoExiste[0];

                        if($estadoExiste == '1'){

                            //Se liberan los recursos.           
                            unset($ssh,$ftpParameters,$operario);
                            //[FTP-ERROR011]: La Empresa destino ya posee un Operador con dicho rut (Conflicto en directorio Externo).
                            $actualizarGestionEmpresa = false;
                            exit($SWERROR->ErrorActual('FTPERROR011'));
                            unset($SWERROR);
                        }else{

                            //Verifica si el Operario con el nuevo rut ya existe en el directorio de la Empresa(Interno).
                            $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$rutEmpresaTemp.'/'.$operario->rutOperario.' ] && echo "1" || echo "0"');
                
                            //Limpia la informacion obtenida.
                            $estadoExiste = $estadoExiste[0];

                            if($estadoExiste == '1'){

                                //Se liberan los recursos.           
                                unset($ssh,$ftpParameters,$operario);
                                //[FTP-ERROR012]: La Empresa destino ya posee un Operador con dicho rut (Conflicto en directorio Interno).
                                $actualizarGestionEmpresa = false;
                                exit($SWERROR->ErrorActual('FTPERROR012'));
                                unset($SWERROR);
                            }
                        }
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

                    //Se liberan los recursos.           
                    unset($ssh,$ftpParameters,$operario);
                    //[FTP-ERROR013]: La Empresa origen no existe en el sistema (Conflicto en directorio Externo).
                    $actualizarGestionEmpresa = false;
                    exit($SWERROR->ErrorActual('FTPERROR013'));
                    unset($SWERROR);
                }else{

                    //Verifica si el directorio de la Empresa origen existe en el directorio Interno.
                    $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$rutEmpresaTemp.' ] && echo "1" || echo "0"');
                
                    //Limpia la informacion obtenida.
                    $estadoExiste = $estadoExiste[0];

                    if($estadoExiste == '0'){

                        //Se liberan los recursos.           
                        unset($ssh,$ftpParameters,$operario);
                        //[FTP-ERROR014]: La Empresa origen no existe en el sistema (Conflicto en directorio Interno).
                        $actualizarGestionEmpresa = false;
                        exit($SWERROR->ErrorActual('FTPERROR014'));
                        unset($SWERROR);
                    }else{
                     
                        //Verifica si el directorio de la Empresa Destino existe en el directorio Externo.
                        $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$rutEmpresa.' ] && echo "1" || echo "0"');
            
                        //Limpia la informacion obtenida.
                        $estadoExiste = $estadoExiste[0];
            
                        if($estadoExiste == '0'){

                            //Se liberan los recursos.           
                            unset($ssh,$ftpParameters,$operario);
                            //[FTP-ERROR015]: La Empresa destino no existe en el sistema (Conflicto en directorio Externo).
                            $actualizarGestionEmpresa = false;
                            exit($SWERROR->ErrorActual('FTPERROR015'));
                            unset($SWERROR);
                        }else{

                            //Verifica si el directorio de la Empresa destino existe en el directorio Interno.
                            $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$rutEmpresa.' ] && echo "1" || echo "0"');
                
                            //Limpia la informacion obtenida.
                            $estadoExiste = $estadoExiste[0];

                            if($estadoExiste == '0'){

                                //Se liberan los recursos.           
                                unset($ssh,$ftpParameters,$operario);
                                //[FTP-ERROR016]: La Empresa destino no existe en el sistema (Conflicto en directorio Interno).
                                $actualizarGestionEmpresa = false;
                                exit($SWERROR->ErrorActual('FTPERROR016'));
                                unset($SWERROR);
                            }else{

                                //Verifica si el Operario ya existe en el directorio de la Empresa destino (Externo).
                                $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$rutEmpresa.'/'.$operario->rutOperario.' ] && echo "1" || echo "0"');
                
                                //Limpia la informacion obtenida.
                                $estadoExiste = $estadoExiste[0];

                                if($estadoExiste == '1'){

                                    //Se liberan los recursos.           
                                    unset($ssh,$ftpParameters,$operario);
                                    //[FTP-ERROR011]: La Empresa destino ya posee un Operador con dicho rut (Conflicto en directorio Externo).
                                    $actualizarGestionEmpresa = false;
                                    exit($SWERROR->ErrorActual('FTPERROR011'));
                                    unset($SWERROR);
                                }else{

                                    //Verifica si el Operario ya existe en el directorio de la Empresa destino (Interno).
                                    $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$rutEmpresa.'/'.$operario->rutOperario.' ] && echo "1" || echo "0"');
                
                                    //Limpia la informacion obtenida.
                                    $estadoExiste = $estadoExiste[0];

                                    if($estadoExiste == '1'){

                                        //Se liberan los recursos.           
                                        unset($ssh,$ftpParameters,$operario);
                                        //[FTP-ERROR012]: La Empresa destino ya posee un Operador con dicho rut (Conflicto en directorio Interno).
                                        $actualizarGestionEmpresa = false;
                                        exit($SWERROR->ErrorActual('FTPERROR012'));
                                        unset($SWERROR);
                                    }
                                }
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
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S usermod -l '.$operario->rutOperario.' '.$rutOperarioTemp);

                    //Renombra los directorios relacionados al Operario.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S mv /home/Externo/'.$rutEmpresaTemp.'/'.$rutOperarioTemp.' /home/Externo/'.$rutEmpresaTemp.'/'.$operario->rutOperario);
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S mv /home/Externo/'.$rutEmpresaTemp.'/'.$rutOperarioTemp.' /home/Externo/'.$rutEmpresaTemp.'/'.$operario->rutOperario);

                    //Añade el nuevo username del operario en la lista de permisos VSFTPD y SSHD.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S sed -i '$ a ".$operario->rutOperario."' /etc/vsftpd.userlist");
                    $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S sed -i '$ a DenyUsers ".$operario->rutOperario."' /etc/ssh/sshd_config");

                    //Se elimina el nombre antiguo de los servicios.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S sed -i '/".$rutOperarioTemp."/d' /etc/vsftpd.userlist");
                    $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S sed -i '/DenyUsers ".$rutOperarioTemp."/d' /etc/vsftpd.userlist");

                    //Reinicio de servicios para actualizar permisos.                    
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S service vsftpd restart');
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S service sshd restart');
                }

                if($rutEmpresaTemp != $rutEmpresa){  

                    //Mueve la carpeta del Operario a la nueva Empresa.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S mv /home/Externo/'.$rutEmpresaTemp.'/'.$operario->rutOperario.' /home/Externo/'.$rutEmpresa.'/'.$operario->rutOperario);
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S mv /home/Interno/'.$rutEmpresaTemp.'/'.$operario->rutOperario.' /home/Interno/'.$rutEmpresa.'/'.$operario->rutOperario);
                }

                //El Operario es Interno, se le reasigna el home.
                if($operario->tipoOperario=="Interno"){

                    $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S usermod -d /home/Interno/ ".$operario->rutOperario);
                }else{

                    //En cualquier otro caso, se establece Operario Externo por defecto.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S usermod -d /home/Externo/".$rutEmpresa."/".$operario->rutOperario." ".$operario->rutOperario);
                }       
            }            
        } 
            
        //Finaliza secuencia de comandos.
        $ssh->exec('exit');
        //Se liberan los recursos.           
        unset($SWERROR,$ssh,$ftpParameters,$operario);       

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
        $ssh = new SSH2($ftpParameters->getServerFTP());
              
        //Intenta hacer la conexion al servidor FTP.
        if(!$ssh->login($ftpParameters->getUserFTP(),$ftpParameters->getPassFTP())){        
            
            //Se liberan los recursos.           
            unset($ssh,$ftpParameters,$operario);
            //[FTP-ERROR002]: Problema con las credenciales del servidor FTP.
            exit($SWERROR->ErrorActual('FTPERROR002'));
            unset($SWERROR);
        }else{

            //Verifica si el Operario existe en el directorio Externo.
            $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$rutEmpresa.'/'.$operario->rutOperario.' ] && echo "1" || echo "0"');
            
            //Limpia la informacion obtenida.
            $estadoExiste = $estadoExiste[0];
            
            if($estadoExiste != '1'){

                //Se liberan los recursos.           
                unset($ssh,$ftpParameters,$operario);
                //[FTP-ERROR009]: El Operario no existe en el sistema (Conflicto en directorio Externo).
                exit($SWERROR->ErrorActual('FTPERROR009'));
                unset($SWERROR);
            }else{

                //Verifica si el Operario existe en el directorio Interno.
                $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$rutEmpresa.'/'.$operario->rutOperario.' ] && echo "1" || echo "0"');
            
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0];
            
                if($estadoExiste != '1'){

                    //Se liberan los recursos.           
                    unset($ssh,$ftpParameters,$operario);
                    //[FTP-ERROR010]: El Operario no existe en el sistema (Conflicto en directorio Interno).
                    exit($SWERROR->ErrorActual('FTPERROR010'));
                    unset($SWERROR);
                }else{

                    //Se elimina el usuario del sistema.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S userdel '.$operario->rutOperario);

                    //Se elimina el usuario de los servicios.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S sed -i '/".$operario->rutOperario."/d' /etc/vsftpd.userlist");
                    $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S sed -i '/DenyUsers ".$operario->rutOperario."/d' /etc/ssh/sshd_config");

                    //Se elimina los directorios del operario. (Opcion 1)                  
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Externo/'.$rutEmpresa.'/'.$operario->rutOperario);
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Interno/'.$rutEmpresa.'/'.$operario->rutOperario);

                    //Se envia el directorio de la empresa a la basura. (Opcion 2)
                    //$ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S gvfs-trash /home/Externo/'.$rutEmpresa.'/'.$operario->rutOperario);
                    //$ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S gvfs-trash /home/Interno/'.$rutEmpresa.'/'.$operario->rutOperario);

                    //Se elimina el operario en la base de datos.
                    $operario->asignar()->delete();
                    $operario->delete();    
                }                          
            }
        }      
        
        //Finaliza secuencia de comandos.
        $ssh->exec('exit');
        //Se liberan los recursos.           
        unset($ssh,$ftpParameters,$operario);  

        return redirect()->back()->with('success','La empresa a sido eliminada.');
    }

    /**function fetch(Request $request)
    {
     if($request->get('query'))
     {
      $query = $request->get('query');
      $data = DB::table('empresas')
        ->where('nombreEmpresa', 'LIKE', "%{$query}%")
        ->get();
      $output = '<ul class="dropdown-menu" style="display:block; position:relative">';
      foreach($data as $row)
      {
       $output .= '<li><a href="#">'.$row->
       nombreEmpresa.'</a></li>';
      }
      $output .= '</ul>';
      echo $output;
     }
    }**/
}
