<?php

namespace App\Http\Controllers;

use App\Http\Requests\OperarioFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Operario;
use App\Empresa;
use App\Http\Controllers\ErrorRepositorio;
use App\Http\Controllers\FtpConexion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
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
                ->orwhere('nombreOperario',  'LIKE', '%' . $query . '%')
                ->orwhere('rutOperario',  'LIKE', '%' . $query . '%')
                ->orwhere('correoOperario',  'LIKE', '%' . $query . '%')
                ->orwhere('tipoOperario',  'LIKE', '%' . $query . '%')
                ->orwhere('telefonoOperario',  'LIKE', '%' . $query . '%')
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

        //Se establecen las reglas de validacion.
        $validatedData = $request->validate([
            'nombreOperario' => 'required|min:9|max:100',
            'rutOperario' => 'required|min:11|max:100',
            'correoOperario' => 'required|email|max:100',
            'telefonoOperario' => 'required|max:100',
            'empresa' => 'required|min:1',
            'contraseniaOperario' => 'required|
                                      min:6|
                                      same:contraseniaOperario2'
        ]);        

        //Carga el repositorio de errores.
        $SWERROR = new ErrorRepositorio();

        //Prepara los parametros de conexion al servidor FTP.
        $ftpParameters = new FtpConexion();

        //Genera al Operario y rellena los atributos con la informacion entregada por el usuario.        
        $operario = new Operario();


        $operario->nombreOperario = request('nombreOperario');
        $operario->rutOperario = request('rutOperario');
        $operario->correoOperario = request('correoOperario');
        $operario->tipoOperario = request('tipoOperario');
        $operario->empresa_id = request('empresa');
        $operario->contraseniaOperario = Hash::make(request('contraseniaOperario'));
        $operario->nombreOperario = request('nombreOperario');
        $operario->telefonoOperario = request('telefonoOperario');
        
        $operario->rutOperarioFTP = preg_replace("/[^A-Za-z0-9]/","",$operario->rutOperario);
        $operario->contraseniaOperarioFTP = substr(preg_replace("/[^A-Za-z0-9]/","",$operario->contraseniaOperario),0,5);

        //Obtiene el rut y nombre de la Empresa seleccionada.
        $rutEmpresa = Empresa::FindOrFail($operario->empresa_id)->rutEmpresa;
        $nombreEmpresa = Empresa::FindOrFail($operario->empresa_id)->nombreEmpresa;
 
        //Prepara la conexion al servidor FTP.
        $ssh = new SSH2($ftpParameters->getServerFTP());
              
        //Intenta hacer la conexion al servidor FTP.
        if(!$ssh->login($ftpParameters->getUserFTP(),$ftpParameters->getPassFTP())){
            
            //Se liberan los recursos.           
            unset($ssh,$ftpParameters,$operario);
            //[FTP-ERROR002]: Problema con las credenciales del servidor FTP.
            return redirect('gestionop')->with('alert',$SWERROR->ErrorActual('FTPERROR002'));
            unset($SWERROR);
        }else{

            //Verifica si el directorio existe.
            $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$rutEmpresa.'/'.$operario->rutOperarioFTP.' ] && echo "1" || echo "0"');
            
            //Limpia la informacion obtenida.
            $estadoExiste = $estadoExiste[0];
            
            if($estadoExiste == '1'){

                //Se liberan los recursos.           
                unset($ssh,$ftpParameters,$operario);
                //[FTP-ERROR007]: El Operario ya existe en el sistema (Conflicto en directorio Externo).
                return redirect('gestionop')->with('alert',$SWERROR->ErrorActual('FTPERROR007'));
                unset($SWERROR);
            }else{

                //Verifica si el directorio existe.
                $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$rutEmpresa.'/'.$operario->rutOperarioFTP.' ] && echo "1" || echo "0"');
                
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0];

                if($estadoExiste == '1'){

                    //Se liberan los recursos.           
                    unset($ssh,$ftpParameters,$operario);
                    //[FTP-ERROR008]: El Operario ya existe en el sistema (Conflicto en directorio Interno).
                    return redirect('gestionop')->with('alert',$SWERROR->ErrorActual('FTPERROR008'));
                    unset($SWERROR);
                }else{
                    
                    //Se añade al historico de gestion.
                    DB::table('historico_gestions')->insert(['nombreGestion' => 'Operario', 
                                                           'tipoGestion' => 'Crear',
                                                           'responsableGestion' => $ftpParameters->getUserFTP(),
                                                           'descripcionGestion' => 'Se ha Creado => Operario: '.$operario->nombreOperario.', Rut: '.$operario->rutOperario.', Tipo Operario: '.$operario->tipoOperario.', Empresa: '.$nombreEmpresa,
                                                           'created_at' => now()]);

                    //Almacena el Operario en la base de datos.
                    $operario->save();

                    //Crea al Operario.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S useradd -s /bin/bash -p $(echo '.$operario->contraseniaOperarioFTP.' | openssl passwd -1 -stdin) '.$operario->rutOperarioFTP); 
                    
                    //Crea la carpeta del Operario en la carpeta Interno.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S mkdir -p /home/Externo/'.$rutEmpresa.'/'.$operario->rutOperarioFTP);
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S chown -R '.$operario->rutOperarioFTP.' /home/Externo/'.$rutEmpresa.'/'.$operario->rutOperarioFTP);
                    
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S mkdir -p /home/Interno/'.$rutEmpresa.'/'.$operario->rutOperarioFTP);
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S chown -R '.$operario->rutOperarioFTP.' /home/Interno/'.$rutEmpresa.'/'.$operario->rutOperarioFTP);
                    
                    //Añade al Operario en la lista de permisos VSFTPD y SSHD.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S sed -i '$ a ".$operario->rutOperarioFTP."' /etc/vsftpd.userlist");
                    $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S sed -i '$ a DenyUsers ".$operario->rutOperarioFTP."' /etc/ssh/sshd_config");
                    

                    //El Operario es Interno, se le reasigna el home.
                    if($operario->tipoOperario=="Interno"){

                        $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S usermod -m -d /home/Interno/ ".$operario->rutOperarioFTP);
                    }else{

                        //En cualquier otro caso, se establece Operario Externo por defecto.
                        $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S usermod -m -d /home/Externo/".$rutEmpresa."/".$operario->rutOperarioFTP." ".$operario->rutOperarioFTP);
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

        //Se establecen las reglas de validacion.
        $validatedData = $request->validate([
            'nombreOperario' => 'required|min:9|max:100',
            'rutOperario' => 'required|min:11|max:100',
            'correoOperario' => 'required|email|max:100',
            'telefonoOperario' => 'required|max:100',
            'empresa' => 'required|min:1',
            'contraseniaOperario' => 'nullable|min:6|
                                      same:contraseniaOperario2'
        ]);     

        //Carga el repositorio de errores.
        $SWERROR = new ErrorRepositorio();

        //Prepara los parametros de conexion al servidor FTP.
        $ftpParameters = new FtpConexion();
        
        //Busca al Operario dada una id de la tabla.
        $operario = Operario::findOrFail($id);

        //Extrae el rut y tipo (Externo o Interno) antiguo del Operario.
        $nombreOperarioTemp = $operario->nombreOperario;
        $rutOperarioTemp = $operario->rutOperario;
        $correoOperarioTemp = $operario->correoOperario;
        $tipoOperarioTemp = $operario->tipoOperario;
        $telefonoOperarioTemp = $operario->telefonoOperario;
        $rutOperarioFTPTemp = $operario->rutOperarioFTP;

        //Obtiene el rut de la Empresa del Operario seleccionado.
        $rutEmpresaTemp = Empresa::FindOrFail($operario->empresa_id)->rutEmpresa;
        $nombreEmpresaTemp = Empresa::FindOrFail($operario->empresa_id)->nombreEmpresa;
        
        //Añaden los nuevos parametros correspondientes.        
        $operario->nombreOperario = $request->get('nombreOperario');
        $operario->rutOperario = $request->get('rutOperario');
        $operario->correoOperario = $request->get('correoOperario');
        $operario->tipoOperario = $request->get('tipoOperario');
        $operario->empresa_id = $request->get('empresa');
        if(request('contraseniaOperario') != ""){

            $operario->contraseniaOperario = Hash::make(request('contraseniaOperario'));
        }        
        $operario->telefonoOperario =  $request->get('telefonoOperario');

        $operario->rutOperarioFTP = preg_replace("/[^A-Za-z0-9]/","",$operario->rutOperario);

        //Flags para actualizar datos de Operario.
        $actualizarGestionOperario = true;
        $actualizarGestionEmpresa = true;
        
        //Obtiene el rut y nombre de la nueva Empresa.
        $rutEmpresa = Empresa::FindOrFail($operario->empresa_id)->rutEmpresa;
        $nombreEmpresa = Empresa::FindOrFail($operario->empresa_id)->nombreEmpresa;

        //Se prepara la conexion al servidor FTP.
        $ssh = new SSH2($ftpParameters->getServerFTP());
           
        //Intenta hacer la conexion al servidor FTP.
        if(!$ssh->login($ftpParameters->getUserFTP(),$ftpParameters->getPassFTP())){
            
            //Se liberan los recursos.           
            unset($ssh,$ftpParameters,$operario);
            //[FTP-ERROR002]: Problema con las credenciales del servidor FTP.
            return redirect('gestionop')->with('alert',$SWERROR->ErrorActual('FTPERROR002'));
            unset($SWERROR);
        }else{

            //Verifica si hay algun cambio en el rut del Operario.
            if($rutOperarioFTPTemp != $operario->rutOperarioFTP){

                //Verifica si el directorio del Operario existe en el directorio Externo.
                $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$rutEmpresaTemp.'/'.$rutOperarioFTPTemp.' ] && echo "1" || echo "0"');
            
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0];
            
                if($estadoExiste == '0'){

                    //Se liberan los recursos.           
                    unset($ssh,$ftpParameters,$operario);
                    //[FTP-ERROR009]: El Operario no existe en el sistema (Conflicto en directorio Externo).
                    $actualizarGestionOperario = false;
                    return redirect('gestionop')->with('alert',$SWERROR->ErrorActual('FTPERROR009'));
                    unset($SWERROR);
                }else{

                    //Verifica si el directorio del Operario existe en el directorio Interno.
                    $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$rutEmpresaTemp.'/'.$rutOperarioFTPTemp.' ] && echo "1" || echo "0"');
                
                    //Limpia la informacion obtenida.
                    $estadoExiste = $estadoExiste[0];

                    if($estadoExiste == '0'){

                        //Se liberan los recursos.           
                        unset($ssh,$ftpParameters,$operario);
                        //[FTP-ERROR010]: El Operario no existe en el sistema (Conflicto en directorio Interno).
                        $actualizarGestionOperario = false;
                        return redirect('gestionop')->with('alert',$SWERROR->ErrorActual('FTPERROR010'));
                        unset($SWERROR);
                    }else{

                        //Verifica si el Operario con el nuevo rut ya existe en el directorio de la Empresa(Externo).
                        $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$rutEmpresaTemp.'/'.$operario->rutOperarioFTP.' ] && echo "1" || echo "0"');
                
                        //Limpia la informacion obtenida.
                        $estadoExiste = $estadoExiste[0];

                        if($estadoExiste == '1'){

                            //Se liberan los recursos.           
                            unset($ssh,$ftpParameters,$operario);
                            //[FTP-ERROR011]: La Empresa destino ya posee un Operador con dicho rut (Conflicto en directorio Externo).
                            $actualizarGestionEmpresa = false;
                            return redirect('gestionop')->with('alert',$SWERROR->ErrorActual('FTPERROR011'));
                            unset($SWERROR);
                        }else{

                            //Verifica si el Operario con el nuevo rut ya existe en el directorio de la Empresa(Interno).
                            $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$rutEmpresaTemp.'/'.$operario->rutOperarioFTP.' ] && echo "1" || echo "0"');
                
                            //Limpia la informacion obtenida.
                            $estadoExiste = $estadoExiste[0];

                            if($estadoExiste == '1'){

                                //Se liberan los recursos.           
                                unset($ssh,$ftpParameters,$operario);
                                //[FTP-ERROR012]: La Empresa destino ya posee un Operador con dicho rut (Conflicto en directorio Interno).
                                $actualizarGestionEmpresa = false;
                                return redirect('gestionop')->with('alert',$SWERROR->ErrorActual('FTPERROR012'));
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
                    return redirect('gestionop')->with('alert',$SWERROR->ErrorActual('FTPERROR013'));
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
                        return redirect('gestionop')->with('alert',$SWERROR->ErrorActual('FTPERROR014'));
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
                            return redirect('gestionop')->with('alert',$SWERROR->ErrorActual('FTPERROR015'));
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
                                return redirect('gestionop')->with('alert',$SWERROR->ErrorActual('FTPERROR016'));
                                unset($SWERROR);
                            }else{

                                //Verifica si el Operario ya existe en el directorio de la Empresa destino (Externo).
                                $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$rutEmpresa.'/'.$operario->rutOperarioFTP.' ] && echo "1" || echo "0"');
                
                                //Limpia la informacion obtenida.
                                $estadoExiste = $estadoExiste[0];

                                if($estadoExiste == '1'){

                                    //Se liberan los recursos.           
                                    unset($ssh,$ftpParameters,$operario);
                                    //[FTP-ERROR011]: La Empresa destino ya posee un Operador con dicho rut (Conflicto en directorio Externo).
                                    $actualizarGestionEmpresa = false;
                                    return redirect('gestionop')->with('alert',$SWERROR->ErrorActual('FTPERROR011'));
                                    unset($SWERROR);
                                }else{

                                    //Verifica si el Operario ya existe en el directorio de la Empresa destino (Interno).
                                    $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$rutEmpresa.'/'.$operario->rutOperarioFTP.' ] && echo "1" || echo "0"');
                
                                    //Limpia la informacion obtenida.
                                    $estadoExiste = $estadoExiste[0];

                                    if($estadoExiste == '1'){

                                        //Se liberan los recursos.           
                                        unset($ssh,$ftpParameters,$operario);
                                        //[FTP-ERROR012]: La Empresa destino ya posee un Operador con dicho rut (Conflicto en directorio Interno).
                                        $actualizarGestionEmpresa = false;
                                        return redirect('gestionop')->with('alert',$SWERROR->ErrorActual('FTPERROR012'));
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
                
                //Se añade al historico de gestion.
                DB::table('historico_gestions')->insert(['nombreGestion' => 'Operario', 
                                                       'tipoGestion' => 'Editar',
                                                       'responsableGestion' => $ftpParameters->getUserFTP(),
                                                       'descripcionGestion' => 'Modificacion Actual => Operario: '.$operario->nombreOperario.', Rut: '.$operario->rutOperario.', Correo: '.$operario->correoOperario.', Tipo Operario: '.$operario->tipoOperario.', Empresa: '.$nombreEmpresa.', Contraseña: *, Telefono: '.$operario->telefonoOperario.'  | Datos Antiguos => Operario: '.$nombreOperarioTemp.', Rut: '.$rutOperarioTemp.', Correo: '.$correoOperarioTemp.', Tipo Operario: '.$tipoOperarioTemp.', Empresa: '.$nombreEmpresaTemp.', Contraseña: *, Telefono: '.$telefonoOperarioTemp,
                                                       'created_at' => now()]);

                //Actualiza los cambios en la Base de Datos.
                $operario->update(); 

                if($rutOperarioFTPTemp != $operario->rutOperarioFTP){

                    //Actualiza el nuevo username del Operario.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S usermod -l '.$operario->rutOperarioFTP.' '.$rutOperarioFTPTemp);

                    //Renombra los directorios relacionados al Operario.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S mv /home/Externo/'.$rutEmpresaTemp.'/'.$rutOperarioFTPTemp.' /home/Externo/'.$rutEmpresaTemp.'/'.$operario->rutOperarioFTP);
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S mv /home/Interno/'.$rutEmpresaTemp.'/'.$rutOperarioFTPTemp.' /home/Interno/'.$rutEmpresaTemp.'/'.$operario->rutOperarioFTP);

                    //Añade el nuevo username del operario en la lista de permisos VSFTPD y SSHD.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S sed -i '$ a ".$operario->rutOperarioFTP."' /etc/vsftpd.userlist");
                    $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S sed -i '$ a DenyUsers ".$operario->rutOperarioFTP."' /etc/ssh/sshd_config");

                    //Se elimina el nombre antiguo de los servicios.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S sed -i '/".$rutOperarioFTPTemp."/d' /etc/vsftpd.userlist");
                    $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S sed -i '/DenyUsers ".$rutOperarioFTPTemp."/d' /etc/ssh/sshd_config");

                    //Reinicio de servicios para actualizar permisos.                    
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S service vsftpd restart');
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S service sshd restart');
                }

                if($rutEmpresaTemp != $rutEmpresa){  

                    //Mueve la carpeta del Operario a la nueva Empresa.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S mv /home/Externo/'.$rutEmpresaTemp.'/'.$operario->rutOperarioFTP.' /home/Externo/'.$rutEmpresa.'/'.$operario->rutOperarioFTP);
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S mv /home/Interno/'.$rutEmpresaTemp.'/'.$operario->rutOperarioFTP.' /home/Interno/'.$rutEmpresa.'/'.$operario->rutOperarioFTP);
                }

                //El Operario es Interno, se le reasigna el home.
                if($operario->tipoOperario=="Interno"){

                    $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S usermod -d /home/Interno/ ".$operario->rutOperarioFTP);
                }else{

                    //En cualquier otro caso, se establece Operario Externo por defecto.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S usermod -d /home/Externo/".$rutEmpresa."/".$operario->rutOperarioFTP." ".$operario->rutOperarioFTP);
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

        //Se obtiene el rut y nombre de la empresa del operario seleccionado.
        $rutEmpresa = Empresa::FindOrFail($operario->empresa_id)->rutEmpresa;
        $nombreEmpresa = Empresa::FindOrFail($operario->empresa_id)->nombreEmpresa;    
        
        //Se prepara la conexion al servidor FTP.
        $ssh = new SSH2($ftpParameters->getServerFTP());
              
        //Intenta hacer la conexion al servidor FTP.
        if(!$ssh->login($ftpParameters->getUserFTP(),$ftpParameters->getPassFTP())){        
            
            //Se liberan los recursos.           
            unset($ssh,$ftpParameters,$operario);
            //[FTP-ERROR002]: Problema con las credenciales del servidor FTP.
            return redirect('gestionop')->with('alert',$SWERROR->ErrorActual('FTPERROR002'));
            unset($SWERROR);
        }else{

            //Verifica si el Operario existe en el directorio Externo.
            $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$rutEmpresa.'/'.$operario->rutOperarioFTP.' ] && echo "1" || echo "0"');
            
            //Limpia la informacion obtenida.
            $estadoExiste = $estadoExiste[0];
            
            if($estadoExiste != '1'){

                //Se liberan los recursos.           
                unset($ssh,$ftpParameters,$operario);
                //[FTP-ERROR009]: El Operario no existe en el sistema (Conflicto en directorio Externo).
                return redirect('gestionop')->with('alert',$SWERROR->ErrorActual('FTPERROR009'));
                unset($SWERROR);
            }else{

                //Verifica si el Operario existe en el directorio Interno.
                $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$rutEmpresa.'/'.$operario->rutOperarioFTP.' ] && echo "1" || echo "0"');
            
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0];
            
                if($estadoExiste != '1'){

                    //Se liberan los recursos.           
                    unset($ssh,$ftpParameters,$operario);
                    //[FTP-ERROR010]: El Operario no existe en el sistema (Conflicto en directorio Interno).
                    return redirect('gestionop')->with('alert',$SWERROR->ErrorActual('FTPERROR010'));
                    unset($SWERROR);
                }else{

                    //Se añade al historico de gestion.
                    DB::table('historico_gestions')->insert(['nombreGestion' => 'Operario', 
                                                           'tipoGestion' => 'Eliminar',
                                                           'responsableGestion' => $ftpParameters->getUserFTP(),
                                                           'descripcionGestion' => 'Se ha Eliminado => Operario: '.$operario->nombreOperario.', Rut: '.$operario->rutOperario.', Tipo Operario: '.$operario->tipoOperario.', Empresa: '.$nombreEmpresa,
                                                           'created_at' => now()]);

                    //Se elimina el usuario del sistema.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S userdel '.$operario->rutOperarioFTP);

                    //Se elimina el usuario de los servicios.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S sed -i '/".$operario->rutOperarioFTP."/d' /etc/vsftpd.userlist");
                    $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S sed -i '/DenyUsers ".$operario->rutOperarioFTP."/d' /etc/ssh/sshd_config");

                    //Se elimina los directorios del operario. (Opcion 1)                  
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Externo/'.$rutEmpresa.'/'.$operario->rutOperarioFTP);
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Interno/'.$rutEmpresa.'/'.$operario->rutOperarioFTP);

                    //Se envia el directorio de la empresa a la basura. (Opcion 2)
                    //$ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S gvfs-trash /home/Externo/'.$rutEmpresa.'/'.$operario->rutOperarioFTP);
                    //$ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S gvfs-trash /home/Interno/'.$rutEmpresa.'/'.$operario->rutOperarioFTP);

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
}
