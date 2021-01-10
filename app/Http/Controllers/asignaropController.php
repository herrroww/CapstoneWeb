<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Operario;
use App\Empresa;
use App\Componente;
use App\Asignar;

use App\Http\Controllers\ErrorRepositorio;
use App\Http\Controllers\FtpConexion;
use phpseclib\Net\SSH2;

class asignaropController extends Controller{

    public function __construct(){

        $this->middleware('auth');
    }

    public function index(Request $request){

        if($request){
            
            $query = trim($request->get('search'));
            
            $asignars = Asignar::where('operario_id',  'LIKE', '%' . $query . '%')
                ->orwhere('componente_id',  'LIKE', '%' . $query . '%')
                ->orwhere('id',  'LIKE', '%' . $query . '%')
                ->orderBy('id', 'asc')
                ->paginate(7);

            $operarios = Operario::all();

            return view('asignarComponente.index', ['asignars' => $asignars, 'operarios' => $operarios, 'search' => $query, 'activemenu' => 'asignar']);
        }
    }
    
    public function create(){

        $componente = Componente::all();
        $operario = Operario::all();
        $asignar= Asignar::all();
        $data = array("lista_componentes" => $componente);
        $data1 = array("lista_operarios" => $operario);

        return view('asignarComponente.create', ['activemenu' => 'asignar'], compact('operario','componente'));
    }

    public function store(Request $request){

        //Carga el repositorio de errores.
        $SWERROR = new ErrorRepositorio();

        //Prepara los parametros de conexion al servidor FTP.
        $ftpParameters = new FtpConexion();

        //Busca al Operario dada una id de la tabla.
        $operario = Operario::findOrFail(request('operario'));

        //Busca a la Empresa a la que corresponde el Operario.
        $empresa = Empresa::findOrFail($operario->empresa_id);

        //Busca el Componente dada una id de la tabla.
        $componente = Componente::findOrFail(request('componente'));

        //Prepara la conexion al servidor FTP.
        $ssh = new SSH2($ftpParameters->getServerFTP());

        //Intenta hacer la conexion al servidor FTP.
        if(!$ssh->login($ftpParameters->getUserFTP(),$ftpParameters->getPassFTP())){
                        
            //Se liberan los recursos.       
            unset($ssh,$ftpParameters,$operario,$empresa,$componente);
            //[FTP-ERROR002]: Problema con las credenciales del servidor FTP.
            exit($SWERROR->ErrorActual('FTPERROR002'));
            unset($SWERROR);
        }else{

            //Verifica si el Operario existe en el directorio Externo.
            $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$empresa->rutEmpresa.'/'.$operario->rutOperario.' ] && echo "1" || echo "0"');
            
            //Limpia la informacion obtenida.
            $estadoExiste = $estadoExiste[0];
            
            if($estadoExiste == '0'){

                //Se liberan los recursos.       
                unset($ssh,$ftpParameters,$operario,$empresa,$componente);

                //[FTP-ERROR009]: El Operario no existe en el sistema (Conflicto en directorio Externo).
                exit($SWERROR->ErrorActual('FTPERROR009'));
                unset($SWERROR);
            }else{

                //Verifica si el Operario existe en el directorio Interno.
                $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$empresa->rutEmpresa.'/'.$operario->rutOperario.' ] && echo "1" || echo "0"');
                
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0];

                if($estadoExiste == '0'){

                    //Se liberan los recursos.       
                    unset($ssh,$ftpParameters,$operario,$empresa,$componente);

                    //[FTP-ERROR010]: El Operario no existe en el sistema (Conflicto en directorio Interno).
                    exit($SWERROR->ErrorActual('FTPERROR010'));
                    unset($SWERROR);
                }else{

                    //Verifica si el Operario Destino posee el componente destino en directorio Externo.
                    $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$empresa->rutEmpresa.'/'.$operario->rutOperario.'/'.$componente->idComponente.' ] && echo "1" || echo "0"');
                
                    //Limpia la informacion obtenida.
                    $estadoExiste = $estadoExiste[0];

                    if($estadoExiste == '1'){

                        //Se liberan los recursos.       
                        unset($ssh,$ftpParameters,$asignar,$operario);
                        //[FTP-ERROR025]: El Operario destino ya posee un Componente con dicha ID (Conflicto en directorio Externo).
                        $actualizarGestionOperario = false;
                        exit($SWERROR->ErrorActual('FTPERROR025'));
                        unset($SWERROR);
                    }else{

                        //Verifica si el Operario Destino posee el componente destino en directorio Interno.
                        $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$empresa->rutEmpresa.'/'.$operario->rutOperario.'/'.$componente->idComponente.' ] && echo "1" || echo "0"');
                
                        //Limpia la informacion obtenida.
                        $estadoExiste = $estadoExiste[0];

                        if($estadoExiste == '1'){

                            //Se liberan los recursos.       
                            unset($ssh,$ftpParameters,$asignar,$operario);
                            //[FTP-ERROR026]: El Operario destino ya posee un Componente con dicha ID (Conflicto en directorio Interno).
                            $actualizarGestionOperario = false;
                            exit($SWERROR->ErrorActual('FTPERROR026'));
                            unset($SWERROR);
                        }else{

                            //Verifica si el Componente existe en el directorio Externo del repositorio Componentes.
                            $estadoExiste = $ssh->exec('[ -d /home/Componentes/Externo/'.$componente->idComponente.' ] && echo "1" || echo "0"');
            
                            //Limpia la informacion obtenida.
                            $estadoExiste = $estadoExiste[0];
            
                            if($estadoExiste == '0'){

                                //Se liberan los recursos.       
                                unset($ssh,$ftpParameters,$operario,$empresa,$componente);
                                //[FTP-ERROR019]: El Componente no existe en el repositorio Componentes (Conflicto en directorio Externo).
                                exit($SWERROR->ErrorActual('FTPERROR019'));
                                unset($SWERROR);
                            }else{

                                //Verifica si el Componente existe en el directorio Interno del repositorio Componentes.
                                $estadoExiste = $ssh->exec('[ -d /home/Componentes/Interno/'.$componente->idComponente.' ] && echo "1" || echo "0"');
            
                                //Limpia la informacion obtenida.
                                $estadoExiste = $estadoExiste[0];
            
                                if($estadoExiste == '0'){

                                    //Se liberan los recursos.       
                                    unset($ssh,$ftpParameters,$operario,$empresa,$componente);

                                    //[FTP-ERROR020]: El Componente no existe en el repositorio Componentes (Conflicto en directorio Interno).
                                    exit($SWERROR->ErrorActual('FTPERROR020'));
                                    unset($SWERROR);
                                }else{

                                    //Crea la asignacion el la tabla de BD.
                                    $asignar = new Asignar();
                                    $asignar->operario_id = $operario->id;
                                    $asignar->componente_id = $componente->id;
                                    $asignar->empresa_id = $empresa->id;                            
                            
                                    $asignar->save();
                                    unset($asignar);

                                    //Asigna la carpeta del Componente al Operario correspondiente.
                                    $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S rsync -av --delete /home/Componentes/Externo/".$componente->idComponente." /home/Externo/".$empresa->rutEmpresa."/".$operario->rutOperario);
                                    $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S rsync -av --delete /home/Componentes/Interno/".$componente->idComponente." /home/Interno/".$empresa->rutEmpresa."/".$operario->rutOperario);

                                    //Asigna al Operador como propietario del Componente asignado.
                                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S chown -R '.$operario->rutOperario.' /home/Externo/'.$empresa->rutEmpresa.'/'.$operario->rutOperario.'/'.$componente->idComponente);
                                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S chown -R '.$operario->rutOperario.' /home/Interno/'.$empresa->rutEmpresa.'/'.$operario->rutOperario.'/'.$componente->idComponente);
                                }   
                            }                             
                        }
                    }                                    
                }

                //Termina secuencia de comandos.
                $ssh->exec('exit');
            }
        }    

        //Se liberan los recursos.       
        unset($SWERROR,$ssh,$ftpParameters,$operario,$empresa,$componente);

        return redirect('asignarop')->with('create','Se asigno correctamente');
    }

    public function edit($id){

        $asignar = Asignar::FindOrFail($id);
        $operario= Operario::all();
        $componente= Componente::all();

        return view('asignarComponente.edit', ['activemenu' => 'asignar' ], compact('operario','componente','asignar'));
    }

    public function update(Request $request, $id){

        //Carga el repositorio de errores.
        $SWERROR = new ErrorRepositorio();

        //Prepara los parametros de conexion al servidor FTP.
        $ftpParameters = new FtpConexion();
        
        //Busca la asignacion a editar.
        $asignar = Asignar::findOrFail($id);

        //Obtiene el Operario que posee el Componente.
        $operarioTemp = Operario::findOrFail($asignar->operario_id);

        //Obtiene la Empresa a la que pertenece el Operario.
        $rutEmpresaTemp = Empresa::findOrFail($operarioTemp->empresa_id)->rutEmpresa;
        
        //Obtiene el Componente asignado al Operario.
        $idComponenteTemp = Componente::findOrFail($asignar->componente_id)->idComponente;

        //Obtiene el rut del Operario seleccionado en la Vista.
        $operario = Operario::findOrFail(request('operario'));

        //Obtiene el ID del Componente seleccionado en la Vista.
        $idComponente = Componente::findOrFail(request('componente'))->idComponente;        

        //Verifica si existe algun cambio a realizar.
        if($operarioTemp->rutOperario!=$operario->rutOperario || $idComponenteTemp != $idComponente){

            //Obtiene la Empresa a la que pertenece el Operario.
            $rutEmpresa = Empresa::findOrFail($operario->empresa_id)->rutEmpresa;

            //Prepara la conexion al servidor FTP.
            $ssh = new SSH2($ftpParameters->getServerFTP());

            //Intenta hacer la conexion al servidor FTP.
            if(!$ssh->login($ftpParameters->getUserFTP(),$ftpParameters->getPassFTP())){
            
                //Se liberan los recursos.       
                unset($ssh,$ftpParameters,$asignar,$operario);

                //[FTP-ERROR002]: Problema con las credenciales del servidor FTP.
                exit($SWERROR->ErrorActual('FTPERROR002'));
                unset($SWERROR);
            }else{

                //Verifica si el directorio del Operario Origen existe en el directorio Externo.
                $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$rutEmpresaTemp.'/'.$operarioTemp->rutOperario.' ] && echo "1" || echo "0"');
            
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0];
            
                if($estadoExiste == '0'){

                    //Se liberan los recursos.       
                    unset($ssh,$ftpParameters,$asignar,$operario);
                    //[FTP-ERROR021]: El Operario origen no existe en el sistema (Conflicto en directorio Externo).
                    $actualizarGestionOperario = false;
                    exit($SWERROR->ErrorActual('FTPERROR021'));
                    unset($SWERROR);
                }else{

                    //Verifica si el directorio del Operario Origen existe en el directorio Interno.
                    $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$rutEmpresaTemp.'/'.$operarioTemp->rutOperario.' ] && echo "1" || echo "0"');
                
                    //Limpia la informacion obtenida.
                    $estadoExiste = $estadoExiste[0];

                    if($estadoExiste == '0'){

                        //Se liberan los recursos.       
                        unset($ssh,$ftpParameters,$asignar,$operario);
                        //[FTP-ERROR022]: El Operario origen no existe en el sistema (Conflicto en directorio Interno).
                        $actualizarGestionOperario = false;
                        exit($SWERROR->ErrorActual('FTPERROR022'));
                        unset($SWERROR);
                    }else{

                        //Verifica si el directorio del Operario Destino existe en el directorio Externo.
                        $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$rutEmpresa.'/'.$operario->rutOperario.' ] && echo "1" || echo "0"');
            
                        //Limpia la informacion obtenida.
                        $estadoExiste = $estadoExiste[0];
            
                        if($estadoExiste == '0'){

                            //Se liberan los recursos.       
                            unset($ssh,$ftpParameters,$asignar,$operario);
                            //[FTP-ERROR023]: El Operario destino no existe en el sistema (Conflicto en directorio Externo).
                            $actualizarGestionOperario = false;
                            exit($SWERROR->ErrorActual('FTPERROR023'));
                            unset($SWERROR);
                        }else{

                            //Verifica si el directorio Operario Destino existe en el directorio Interno.
                            $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$rutEmpresa.'/'.$operario->rutOperario.' ] && echo "1" || echo "0"');
                
                            //Limpia la informacion obtenida.
                            $estadoExiste = $estadoExiste[0];

                            if($estadoExiste == '0'){

                                //Se liberan los recursos.       
                                unset($ssh,$ftpParameters,$asignar,$operario);
                                //[FTP-ERROR024]: El Operario destino no existe en el sistema (Conflicto en directorio Interno).
                                $actualizarGestionOperario = false;
                                exit($SWERROR->ErrorActual('FTPERROR024'));
                                unset($SWERROR);
                            }else{

                                //Verifica si el Operario Destino posee el componente destino en directorio Externo.
                                $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$rutEmpresa.'/'.$operario->rutOperario.'/'.$idComponente.' ] && echo "1" || echo "0"');
                
                                //Limpia la informacion obtenida.
                                $estadoExiste = $estadoExiste[0];

                                if($estadoExiste == '1'){

                                    //Se liberan los recursos.       
                                    unset($ssh,$ftpParameters,$asignar,$operario);
                                    //[FTP-ERROR025]: El Operario destino ya posee un Componente con dicha ID (Conflicto en directorio Externo).
                                    $actualizarGestionOperario = false;
                                    exit($SWERROR->ErrorActual('FTPERROR025'));
                                    unset($SWERROR);
                                }else{

                                    //Verifica si el Operario Destino posee el componente destino en directorio Interno.
                                    $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$rutEmpresa.'/'.$operario->rutOperario.'/'.$idComponente.' ] && echo "1" || echo "0"');
                
                                    //Limpia la informacion obtenida.
                                    $estadoExiste = $estadoExiste[0];

                                    if($estadoExiste == '1'){

                                        //Se liberan los recursos.       
                                        unset($ssh,$ftpParameters,$asignar,$operario);
                                        //[FTP-ERROR026]: El Operario destino ya posee un Componente con dicha ID (Conflicto en directorio Interno).
                                        $actualizarGestionOperario = false;
                                        exit($SWERROR->ErrorActual('FTPERROR026'));
                                        unset($SWERROR);
                                    }else{

                                        //Verifica si el Componente existe en el directorio Externo del repositorio Componentes.
                                        $estadoExiste = $ssh->exec('[ -d /home/Componentes/Externo/'.$idComponente.' ] && echo "1" || echo "0"');
            
                                        //Limpia la informacion obtenida.
                                        $estadoExiste = $estadoExiste[0];
            
                                        if($estadoExiste == '0'){

                                            //Se liberan los recursos.       
                                            unset($ssh,$ftpParameters,$asignar,$operario);

                                            //[FTP-ERROR019]: El Componente no existe en el repositorio Componentes (Conflicto en directorio Externo).
                                            exit($SWERROR->ErrorActual('FTPERROR019'));
                                            unset($SWERROR);
                                        }else{

                                            //Verifica si el Componente existe en el directorio Interno del repositorio Componentes.
                                            $estadoExiste = $ssh->exec('[ -d /home/Componentes/Interno/'.$idComponente.' ] && echo "1" || echo "0"');
            
                                            //Limpia la informacion obtenida.
                                            $estadoExiste = $estadoExiste[0];
            
                                            if($estadoExiste == '0'){

                                                //Se liberan los recursos.       
                                                unset($ssh,$ftpParameters,$asignar,$operario);

                                                //[FTP-ERROR020]: El Componente no existe en el repositorio Componentes (Conflicto en directorio Interno).
                                                exit($SWERROR->ErrorActual('FTPERROR020'));
                                                unset($SWERROR);
                                            }else{                    
                            
                                                $asignar->operario_id = $operario->id;
                                                $asignar->componente_id = $request->get('componente');
                                                $asignar->empresa_id = $operario->empresa_id;
                                                $asignar->update();

                                                //Eliminar el componente asignado previamente.
                                                $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Externo/'.$rutEmpresaTemp.'/'.$operarioTemp->rutOperario.'/'.$idComponenteTemp);
                                                $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Interno/'.$rutEmpresaTemp.'/'.$operarioTemp->rutOperario.'/'.$idComponenteTemp);


                                                //Asigna la carpeta del Componente al Operario correspondiente.
                                                $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S rsync -av --delete /home/Componentes/Externo/".$idComponente." /home/Externo/".$rutEmpresa."/".$operario->rutOperario);
                                                $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S rsync -av --delete /home/Componentes/Interno/".$idComponente." /home/Interno/".$rutEmpresa."/".$operario->rutOperario);

                                                //Asigna al Operador como propietario del Componente asignado.
                                                $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S chown -R '.$operario->rutOperario.' /home/Externo/'.$rutEmpresa.'/'.$operario->rutOperario.'/'.$idComponente);
                                                $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S chown -R '.$operario->rutOperario.' /home/Interno/'.$rutEmpresa.'/'.$operario->rutOperario.'/'.$idComponente);

                                                //Termina la secuencia de comandos.
                                                $ssh->exec('exit');
                                                //Se liberan los recursos.       
                                                unset($ssh,$ftpParameters,$asignar,$operario,$SWERROR,$asignar);                                                
                                            }   
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } 
        return redirect('asignarop')->with('edit','se a editado correctamente'); 
    }

    public function destroy($id){

        //Carga el repositorio de errores.
        $SWERROR = new ErrorRepositorio();

        //Prepara los parametros de conexion al servidor FTP.
        $ftpParameters = new FtpConexion();
        
        //Obtiene la asignacion de componente seleccionada.
        $asignar = Asignar::findOrFail($id);

        //Obtiene el Operario que posee el Componente.
        $operario = Operario::findOrFail($asignar->operario_id);

        //Obtiene la Empresa a la que pertenece el Operario.
        $rutEmpresa = Empresa::findOrFail($operario->empresa_id)->rutEmpresa;
        
        //Obtiene el Componente asignado al Operario.
        $idComponente = Componente::findOrFail($asignar->componente_id)->idComponente;

        //Prepara la conexion al servidor FTP.
        $ssh = new SSH2($ftpParameters->getServerFTP());

        //Intenta hacer la conexion al servidor FTP.
        if(!$ssh->login($ftpParameters->getUserFTP(),$ftpParameters->getPassFTP())){
        
            //Se liberan los recursos.       
            unset($ssh,$ftpParameters,$asignar,$operario);

            //[FTP-ERROR002]: Problema con las credenciales del servidor FTP.
            exit($SWERROR->ErrorActual('FTPERROR002'));
            unset($SWERROR);
        }else{

            //Se elimina los directorios del Componente del Operario. (Opcion 1)                  
            $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Externo/'.$rutEmpresa.'/'.$operario->rutOperario.'/'.$idComponente);
            $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Interno/'.$rutEmpresa.'/'.$operario->rutOperario.'/'.$idComponente);

            //Se envia el directorio de la empresa a la basura. (Opcion 2)
            //$ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S gvfs-trash /home/Externo/'.$rutEmpresa.'/'.$operario->rutOperario.'/'.$idComponente);
            //$ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S gvfs-trash /home/Interno/'.$rutEmpresa.'/'.$operario->rutOperario.'/'.$idComponente);

            $asignar->delete();

            return redirect()->back()->with('success','La empresa a sido eliminada.');
        }
    }
}