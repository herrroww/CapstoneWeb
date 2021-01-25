<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Operario;
use App\Empresa;
use App\Componente;
use App\Asignar;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ErrorRepositorio;
use App\Http\Controllers\FtpConexion;
use phpseclib\Net\SSH2;

class asignaropController extends Controller{

    public function __construct(){

        $this->middleware('auth');
    }

    public function index(Request $request){

        if($request){
            
            //Recibe la informacion de busqueda entrante.
            $query = trim($request->get('search')); 
            
            //Prepara variables de Operario temporales para el filtro.
            $operariosTemp = Operario::all();
            $operarios;
            $operarioEncontrado = false;

            //Prepara variables de Componente temporales para el filtro.
            $componentesTemp = Componente::all();
            $componenteEncontrado = false;

            //Verifica si la busqueda ingresada corresponde a algun Componente.
            foreach($componentesTemp as $componente){

                //Si la busqueda no es vacia, la busqueda corresponde al nombre del Componente y mientras no haya encontrado la coincidencia:
                if($query != "" && $componente->nombreComponente == $query && !$componenteEncontrado){

                    //El Componente se ha encontrado.
                    $componenteEncontrado=true;   

                    //Filtra las asignaciones en relacion al Componente encontrado.
                    $asignars = Asignar::where('componente_id',  'LIKE', $componente->id)
                                    ->orderBy('id', 'asc')
                                    ->paginate(7);

                    //Obtiene a todos los operarios.
                    $operarios = $operariosTemp;               
                }
            }

            //Verifica si la busqueda ingresada corresponde a algun Operario.
            foreach($operariosTemp as $operario){

                //Si la busqueda no es vacia, la busqueda corresponde al nombre del Operario y mientras no haya encontrado la coincidencia:
                if($query != "" && $operario->nombreOperario == $query && !$operarioEncontrado){

                    //Prepara la lista de operarios para la filtracion de asignaciones.
                    $operarios = Operario::findOrFail($operario->id);

                    //Operario se ha encontrado.
                    $operarioEncontrado=true;

                    //Filtra las asignaciones en relacion al Operario encontrado.
                    $asignars = Asignar::where('operario_id',  'LIKE', $operario->id)
                                    ->orderBy('id', 'asc')
                                    ->paginate(7);                    

                //Si la busqueda no es vacia, la busqueda corresponde al tipo de Operario y mientras no haya encontrado la coincidencia:
                }elseif($query!= "" && $operario->tipoOperario == $query && !$operarioEncontrado){

                    //Prepara la lista de todos los operarios.
                    $operarios = $operariosTemp;

                    //Encuentra al Operario(s) segun el tipo.
                    $operarioEncontrado=true;

                    //Filtra las asignaciones segun el tipo de Operario encontrado.
                    $asignars = Asignar::where('operarios.tipoOperario', 'LIKE', $query)
                                    ->join('operarios', 'operario_id',  'LIKE', 'operarios.id')
                                    ->orderBy('id', 'asc')
                                    ->select('asignars.*')
                                    ->paginate(7);
                }
            }

            //Si no existe ninguna coincidencia de busqueda.
            if(!$operarioEncontrado && !$componenteEncontrado){

                //Vuelve a llenar la tabla con las asignaciones creadas.
                $operarios = $operariosTemp;

                $asignars = Asignar::where('id',  'LIKE', '%' . $query . '%')
                                    ->orderBy('id', 'asc')
                                    ->paginate(7);  
            }                    
            
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

        //Se establecen las reglas de validacion.
        $validatedData = $request->validate([            
            'operario' => 'required|min:1',
            'componente' => 'required|min:1',
        ]); 

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
            return redirect('asignarop')->with('alert',$SWERROR->ErrorActual('FTPERROR002'));
            unset($SWERROR);
        }else{

            //Verifica si el Operario existe en el directorio Externo.
            $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$empresa->nombreEmpresa.'/'.$operario->rutOperarioFTP.' ] && echo "1" || echo "0"');
            
            //Limpia la informacion obtenida.
            $estadoExiste = $estadoExiste[0];
            
            if($estadoExiste == '0'){

                //Se liberan los recursos.       
                unset($ssh,$ftpParameters,$operario,$empresa,$componente);

                //[FTP-ERROR009]: El Operario no existe en el sistema (Conflicto en directorio Externo).
                return redirect('asignarop')->with('alert',$SWERROR->ErrorActual('FTPERROR009'));
                unset($SWERROR);
            }else{

                //Verifica si el Operario existe en el directorio Interno.
                $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$empresa->nombreEmpresa.'/'.$operario->rutOperarioFTP.' ] && echo "1" || echo "0"');
                
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0];

                if($estadoExiste == '0'){

                    //Se liberan los recursos.       
                    unset($ssh,$ftpParameters,$operario,$empresa,$componente);

                    //[FTP-ERROR010]: El Operario no existe en el sistema (Conflicto en directorio Interno).
                    return redirect('asignarop')->with('alert',$SWERROR->ErrorActual('FTPERROR010'));
                    unset($SWERROR);
                }else{

                    //Verifica si el Operario Destino posee el componente destino en directorio Externo.
                    $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$empresa->nombreEmpresa.'/'.$operario->rutOperarioFTP.'/'.$componente->idComponente.' ] && echo "1" || echo "0"');
                
                    //Limpia la informacion obtenida.
                    $estadoExiste = $estadoExiste[0];

                    if($estadoExiste == '1'){

                        //Se liberan los recursos.       
                        unset($ssh,$ftpParameters,$asignar,$operario);
                        //[FTP-ERROR025]: El Operario destino ya posee un Componente con dicha ID (Conflicto en directorio Externo).
                        $actualizarGestionOperario = false;
                        return redirect('asignarop')->with('alert',$SWERROR->ErrorActual('FTPERROR025'));
                        unset($SWERROR);
                    }else{

                        //Verifica si el Operario Destino posee el componente destino en directorio Interno.
                        $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$empresa->nombreEmpresa.'/'.$operario->rutOperarioFTP.'/'.$componente->idComponente.' ] && echo "1" || echo "0"');
                
                        //Limpia la informacion obtenida.
                        $estadoExiste = $estadoExiste[0];

                        if($estadoExiste == '1'){

                            //Se liberan los recursos.       
                            unset($ssh,$ftpParameters,$asignar,$operario);
                            //[FTP-ERROR026]: El Operario destino ya posee un Componente con dicha ID (Conflicto en directorio Interno).
                            $actualizarGestionOperario = false;
                            return redirect('asignarop')->with('alert',$SWERROR->ErrorActual('FTPERROR026'));
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
                                return redirect('asignarop')->with('alert',$SWERROR->ErrorActual('FTPERROR019'));
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
                                    return redirect('asignarop')->with('alert',$SWERROR->ErrorActual('FTPERROR020'));
                                    unset($SWERROR);
                                }else{

                                    //Se añade al historico de gestion.
                                    DB::table('historico_gestions')->insert(['nombreGestion' => 'Asignacion', 
                                                                           'tipoGestion' => 'Crear',
                                                                           'responsableGestion' => $ftpParameters->getUserFTP(),
                                                                           'descripcionGestion' => 'Se ha Asignado => Rut Operario: '.$operario->rutOperario.', de la Empresa: '.$empresa->nombreEmpresa.' el ID Componente: '.$componente->idComponente,
                                                                           'created_at' => now()]);

                                    //Crea la asignacion el la tabla de BD.
                                    $asignar = new Asignar();
                                    $asignar->operario_id = $operario->id;
                                    $asignar->componente_id = $componente->id;
                                    $asignar->empresa_id = $empresa->id;                            
                            
                                    $asignar->save();
                                    unset($asignar);

                                    //Asigna la carpeta del Componente al Operario correspondiente.
                                    $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S rsync -av --delete /home/Componentes/Externo/".$componente->idComponente." /home/Externo/".$empresa->nombreEmpresa."/".$operario->rutOperarioFTP);
                                    $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S rsync -av --delete /home/Componentes/Interno/".$componente->idComponente." /home/Interno/".$empresa->nombreEmpresa."/".$operario->rutOperarioFTP);

                                    //Asigna al Operador como propietario del Componente asignado.
                                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S chown -R '.$operario->rutOperario.' /home/Externo/'.$empresa->nombreEmpresa.'/'.$operario->rutOperarioFTP.'/'.$componente->idComponente);
                                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S chown -R '.$operario->rutOperario.' /home/Interno/'.$empresa->nombreEmpresa.'/'.$operario->rutOperarioFTP.'/'.$componente->idComponente);
                                }   
                            }                             
                        }
                    }                                    
                }

                //Termina secuencia de comandos.
                $ssh->exec('exit');
                //Se liberan los recursos.       
                unset($SWERROR,$ssh,$ftpParameters,$operario,$empresa,$componente);

                return redirect('asignarop')->with('create','Se asigno correctamente');
            }
        }    

        //Se liberan los recursos.       
        unset($SWERROR,$ssh,$ftpParameters,$operario,$empresa,$componente);

        //[FTP-ERROR046]: Algo ha ocurrido y la Asignación no pudo ser creada.
        return redirect('asignarop')->with('alert',$SWERROR->ErrorActual('FTPERROR046'));                                    
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
        $nombreEmpresaTemp = Empresa::findOrFail($operarioTemp->empresa_id)->nombreEmpresa;
        
        //Obtiene el Componente asignado al Operario.
        $idComponenteTemp = Componente::findOrFail($asignar->componente_id)->idComponente;

        //Obtiene el rut del Operario seleccionado en la Vista.
        $operario = Operario::findOrFail(request('operario'));

        //Obtiene el ID del Componente seleccionado en la Vista.
        $idComponente = Componente::findOrFail(request('componente'))->idComponente;        

        //Verifica si existe algun cambio a realizar.
        if($operarioTemp->rutOperarioFTP!=$operario->rutOperarioFTP || $idComponenteTemp != $idComponente){

            //Obtiene la Empresa a la que pertenece el Operario.
            $rutEmpresa = Empresa::findOrFail($operario->empresa_id)->rutEmpresa;
            $nombreEmpresa =Empresa::findOrFail($operario->empresa_id)->nombreEmpresa;

            //Prepara la conexion al servidor FTP.
            $ssh = new SSH2($ftpParameters->getServerFTP());

            //Intenta hacer la conexion al servidor FTP.
            if(!$ssh->login($ftpParameters->getUserFTP(),$ftpParameters->getPassFTP())){
            
                //Se liberan los recursos.       
                unset($ssh,$ftpParameters,$asignar,$operario);

                //[FTP-ERROR002]: Problema con las credenciales del servidor FTP.
                return redirect('asignarop')->with('alert',$SWERROR->ErrorActual('FTPERROR002'));
                unset($SWERROR);
            }else{

                //Verifica si el directorio del Operario Origen existe en el directorio Externo.
                $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$nombreEmpresaTemp.'/'.$operarioTemp->rutOperarioFTP.' ] && echo "1" || echo "0"');
            
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0];
            
                if($estadoExiste == '0'){

                    //Se liberan los recursos.       
                    unset($ssh,$ftpParameters,$asignar,$operario);
                    //[FTP-ERROR021]: El Operario origen no existe en el sistema (Conflicto en directorio Externo).
                    $actualizarGestionOperario = false;
                    return redirect('asignarop')->with('alert',$SWERROR->ErrorActual('FTPERROR021'));
                    unset($SWERROR);
                }else{

                    //Verifica si el directorio del Operario Origen existe en el directorio Interno.
                    $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$nombreEmpresaTemp.'/'.$operarioTemp->rutOperarioFTP.' ] && echo "1" || echo "0"');
                
                    //Limpia la informacion obtenida.
                    $estadoExiste = $estadoExiste[0];

                    if($estadoExiste == '0'){

                        //Se liberan los recursos.       
                        unset($ssh,$ftpParameters,$asignar,$operario);
                        //[FTP-ERROR022]: El Operario origen no existe en el sistema (Conflicto en directorio Interno).
                        $actualizarGestionOperario = false;
                        return redirect('asignarop')->with('alert',$SWERROR->ErrorActual('FTPERROR022'));
                        unset($SWERROR);
                    }else{

                        //Verifica si el directorio del Operario Destino existe en el directorio Externo.
                        $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$nombreEmpresa.'/'.$operario->rutOperarioFTP.' ] && echo "1" || echo "0"');
            
                        //Limpia la informacion obtenida.
                        $estadoExiste = $estadoExiste[0];
            
                        if($estadoExiste == '0'){

                            //Se liberan los recursos.       
                            unset($ssh,$ftpParameters,$asignar,$operario);
                            //[FTP-ERROR023]: El Operario destino no existe en el sistema (Conflicto en directorio Externo).
                            $actualizarGestionOperario = false;
                            return redirect('asignarop')->with('alert',$SWERROR->ErrorActual('FTPERROR023'));
                            unset($SWERROR);
                        }else{

                            //Verifica si el directorio Operario Destino existe en el directorio Interno.
                            $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$nombreEmpresa.'/'.$operario->rutOperarioFTP.' ] && echo "1" || echo "0"');
                
                            //Limpia la informacion obtenida.
                            $estadoExiste = $estadoExiste[0];

                            if($estadoExiste == '0'){

                                //Se liberan los recursos.       
                                unset($ssh,$ftpParameters,$asignar,$operario);
                                //[FTP-ERROR024]: El Operario destino no existe en el sistema (Conflicto en directorio Interno).
                                $actualizarGestionOperario = false;
                                return redirect('asignarop')->with('alert',$SWERROR->ErrorActual('FTPERROR024'));
                                unset($SWERROR);
                            }else{

                                //Verifica si el Operario Destino posee el componente destino en directorio Externo.
                                $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$nombreEmpresa.'/'.$operario->rutOperarioFTP.'/'.$idComponente.' ] && echo "1" || echo "0"');
                
                                //Limpia la informacion obtenida.
                                $estadoExiste = $estadoExiste[0];

                                if($estadoExiste == '1'){

                                    //Se liberan los recursos.       
                                    unset($ssh,$ftpParameters,$asignar,$operario);
                                    //[FTP-ERROR025]: El Operario destino ya posee un Componente con dicha ID (Conflicto en directorio Externo).
                                    $actualizarGestionOperario = false;
                                    return redirect('asignarop')->with('alert',$SWERROR->ErrorActual('FTPERROR025'));
                                    unset($SWERROR);
                                }else{

                                    //Verifica si el Operario Destino posee el componente destino en directorio Interno.
                                    $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$nombreEmpresa.'/'.$operario->rutOperarioFTP.'/'.$idComponente.' ] && echo "1" || echo "0"');
                
                                    //Limpia la informacion obtenida.
                                    $estadoExiste = $estadoExiste[0];

                                    if($estadoExiste == '1'){

                                        //Se liberan los recursos.       
                                        unset($ssh,$ftpParameters,$asignar,$operario);
                                        //[FTP-ERROR026]: El Operario destino ya posee un Componente con dicha ID (Conflicto en directorio Interno).
                                        $actualizarGestionOperario = false;
                                        return redirect('asignarop')->with('alert',$SWERROR->ErrorActual('FTPERROR026'));
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
                                            return redirect('asignarop')->with('alert',$SWERROR->ErrorActual('FTPERROR019'));
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
                                                return redirect('asignarop')->with('alert',$SWERROR->ErrorActual('FTPERROR020'));
                                                unset($SWERROR);
                                            }else{                    
                            
                                                //Se añade al historico de gestion.
                                                DB::table('historico_gestions')->insert(['nombreGestion' => 'Asignacion', 
                                                                                       'tipoGestion' => 'Editar',
                                                                                       'responsableGestion' => $ftpParameters->getUserFTP(),
                                                                                       'descripcionGestion' => 'Modificacion Actual => Rut Operario: '.$operario->rutOperario.', de la Empresa: '.$nombreEmpresa.' el ID Componente: '.$idComponente.' | Datos Antiguos => Rut Operario: '.$operarioTemp->rutOperarioFTP.', de la Empresa: '.$nombreEmpresaTemp.' el ID Componente: '.$idComponenteTemp,
                                                                                       'created_at' => now()]);
                                                
                                                //Se actualiza la asignacion a la Base de Datos.
                                                $asignar->operario_id = $operario->id;
                                                $asignar->componente_id = $request->get('componente');
                                                $asignar->empresa_id = $operario->empresa_id;
                                                $asignar->update();

                                                //Eliminar el componente asignado previamente.
                                                $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Externo/'.$nombreEmpresaTemp.'/'.$operarioTemp->rutOperarioFTP.'/'.$idComponenteTemp);
                                                $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Interno/'.$nombreEmpresaTemp.'/'.$operarioTemp->rutOperarioFTP.'/'.$idComponenteTemp);


                                                //Asigna la carpeta del Componente al Operario correspondiente.
                                                $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S rsync -av --delete /home/Componentes/Externo/".$idComponente." /home/Externo/".$nombreEmpresa."/".$operario->rutOperarioFTP);
                                                $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S rsync -av --delete /home/Componentes/Interno/".$idComponente." /home/Interno/".$nombreEmpresa."/".$operario->rutOperarioFTP);

                                                //Asigna al Operador como propietario del Componente asignado.
                                                $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S chown -R '.$operario->rutOperarioFTP.' /home/Externo/'.$nombreEmpresa.'/'.$operario->rutOperarioFTP.'/'.$idComponente);
                                                $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S chown -R '.$operario->rutOperarioFTP.' /home/Interno/'.$nombreEmpresa.'/'.$operario->rutOperarioFTP.'/'.$idComponente);

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
        $nombreEmpresa = Empresa::findOrFail($operario->empresa_id)->nombreEmpresa;
        
        //Obtiene el Componente asignado al Operario.
        $idComponente = Componente::findOrFail($asignar->componente_id)->idComponente;

        //Prepara la conexion al servidor FTP.
        $ssh = new SSH2($ftpParameters->getServerFTP());

        //Intenta hacer la conexion al servidor FTP.
        if(!$ssh->login($ftpParameters->getUserFTP(),$ftpParameters->getPassFTP())){
        
            //Se liberan los recursos.       
            unset($ssh,$ftpParameters,$asignar,$operario);

            //[FTP-ERROR002]: Problema con las credenciales del servidor FTP.
            return redirect('asignarop')->with('alert',$SWERROR->ErrorActual('FTPERROR002'));
            unset($SWERROR);
        }else{

            //Se añade al historico de gestion.
            DB::table('historico_gestions')->insert(['nombreGestion' => 'Asignacion', 
                                                   'tipoGestion' => 'Eliminar',
                                                   'responsableGestion' => $ftpParameters->getUserFTP(),
                                                   'descripcionGestion' => 'Se ha Eliminado Asignacion => Rut Operario: '.$operario->rutOperario.', de la Empresa: '.$nombreEmpresa.' el ID Componente: '.$idComponente,
                                                   'created_at' => now()]);


            //Se elimina los directorios del Componente del Operario. (Opcion 1)                  
            $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Externo/'.$nombreEmpresa.'/'.$operario->rutOperarioFTP.'/'.$idComponente);
            $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Interno/'.$nombreEmpresa.'/'.$operario->rutOperarioFTP.'/'.$idComponente);

            //Se envia el directorio de la empresa a la basura. (Opcion 2)
            //$ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S gvfs-trash /home/Externo/'.$nombreEmpresa.'/'.$operario->rutOperarioFTP.'/'.$idComponente);
            //$ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S gvfs-trash /home/Interno/'.$nombreEmpresa.'/'.$operario->rutOperarioFTP.'/'.$idComponente);

            $asignar->delete();
            //Se liberan los recursos.       
            unset($ssh,$ftpParameters,$asignar,$operario,$SWERROR);

            return redirect()->back()->with('success','La empresa a sido eliminada.');
        }

        //Se liberan los recursos.       
        unset($ssh,$ftpParameters,$asignar,$operario,$SWERROR);

        //[FTP-ERROR048]: Algo ha ocurrido y la Asignación no pudo ser eliminada.
        return redirect('asignarop')->with('alert',$SWERROR->ErrorActual('FTPERROR048'));   
    }
}