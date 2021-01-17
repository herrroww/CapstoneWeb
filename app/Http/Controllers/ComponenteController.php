<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use App\Componente;
use App\Operario;
use App\Empresa;
use App\Modelo;
use App\Documento;
use App\Http\Controllers\ErrorRepositorio;
use App\Http\Controllers\FtpConexion;
use phpseclib\Net\SSH2;
use Illuminate\Support\Facades\DB;

class ComponenteController extends Controller{

    public function __construct(){
        
        $this->middleware('auth');
    }
    
    public function index(Request $request){

        if($request){
            $query = trim($request->get('search'));

            $componentes = Componente::where('nombreComponente',  'LIKE', '%' . $query . '%')
                ->orwhere('nombreComponente',  'LIKE', '%' . $query . '%')
                ->orwhere('idComponente',  'LIKE', '%' . $query . '%')
                ->orwhere('id',  'LIKE', '%' . $query . '%')
                ->orderBy('id', 'asc')
                ->paginate(7);

            return view('componentes.index', ['componentes' => $componentes, 'search' => $query, 'activemenu' => 'componente']);
        }        
    }
    
    public function create(){

        return view('componentes.create',['activemenu' => 'componente']);
    }

    public function store(Request $request){

        //Se establecen las reglas de validacion.
        $validatedData = $request->validate([
            'nombreComponente' => 'required|min:4|max:100',
            'idComponente' => 'required|min:2|max:20',
        ]); 

        //Carga el repositorio de errores.
        $SWERROR = new ErrorRepositorio();

        //Prepara los parametros de conexion al servidor FTP.
        $ftpParameters = new FtpConexion();

        $componente = new Componente();
        $componente->nombreComponente = request('nombreComponente');
        $componente->IdComponente = request('idComponente');
        
        //Prepara la conexion al servidor FTP.
        $ssh = new SSH2($ftpParameters->getServerFTP());

        //Intenta hacer la conexion al servidor FTP.
        if(!$ssh->login($ftpParameters->getUserFTP(),$ftpParameters->getPassFTP())){
            
            //Se liberan los recursos.
            unset($ftpParameters,$ssh,$componente);
            //[FTP-ERROR002]: Problema con las credenciales del servidor FTP.
            return redirect('componenteop')->with('alert',$SWERROR->ErrorActual('FTPERROR002'));
            unset($SWERROR);
        }else{

            //Verifica si el Componente existe en el directorio Externo del repositorio de Componentes.
            $estadoExiste = $ssh->exec('[ -d /home/Componentes/Externo/'.$componente->IdComponente.' ] && echo "1" || echo "0"');
            
            //Limpia la informacion obtenida.
            $estadoExiste = $estadoExiste[0];

            if($estadoExiste == '1'){

                //Se liberan los recursos.
                unset($ftpParameters,$ssh,$componente);
                //[FTP-ERROR017]: El Componente ya existe en el repositorio Componentes (Conflicto en directorio Externo).
                return redirect('componenteop')->with('alert',$SWERROR->ErrorActual('FTPERROR017'));
                unset($SWERROR);
            }else{

                //Verifica si el Componente existe en el directorio Interno del repositorio de Componentes.
                $estadoExiste = $ssh->exec('[ -d /home/Componentes/Interno/'.$componente->IdComponente.' ] && echo "1" || echo "0"');
                
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0];

                if($estadoExiste == '1'){

                    //Se liberan los recursos.
                    unset($ftpParameters,$ssh,$componente);
                    //[FTP-ERROR018]: El Componente ya existe en el repositorio Componentes (Conflicto en directorio Interno).
                    return redirect('componenteop')->with('alert',$SWERROR->ErrorActual('FTPERROR018'));
                    unset($SWERROR);
                }else{

                    //Se añade al historico de gestion.
                    DB::table('historico_gestions')->insert(['nombreGestion' => 'Componente', 
                                                           'tipoGestion' => 'Crear',
                                                           'responsableGestion' => $ftpParameters->getUserFTP(),
                                                           'descripcionGestion' => 'Se ha Creado => Componente: '.$componente->nombreComponente.', ID: '.$componente->idComponente,
                                                           'created_at' => now()]);
                    
                    //Se guardan los cambios en la Base de Datos.
                    $componente->save();

                    //Se aplican los cambios en el servidor FTP.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S mkdir -p /home/Componentes/Externo/'.$componente->IdComponente);
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S mkdir -p /home/Componentes/Interno/'.$componente->IdComponente);
                
                    //Termina la secuencia de comandos.
                    $ssh->exec('exit');
                    //Se liberan los recursos.       
                    unset($SWERROR,$ssh,$ftpParameters,$componente);
                    return redirect('componenteop')->with('create','El componente se a creado correctamente');
                }
            }
        }                  
    }

    public function edit($id){
        
        return view('componentes.edit', ['componente' => Componente::findOrFail($id), 'activemenu' => 'componente']);
    }

    public function update(Request $request, $id){

        //Se establecen las reglas de validacion.
        $validatedData = $request->validate([
            'nombreComponente' => 'required|min:4|max:100',
            'idComponente' => 'required|min:2|max:20',
        ]); 

        //Carga el repositorio de errores.
        $SWERROR = new ErrorRepositorio();

        //Prepara los parametros de conexion al servidor FTP.
        $ftpParameters = new FtpConexion();

        //Se obtiene la informacion del Componente seleccionado.
        $componente = Componente::findOrFail($id);

        //Se guarda de forma temporal la ID y el nombre original del Componente seleccionado.
        $idComponenteTemp = $componente->idComponente;
        $nombreComponenteTemp = $componente->nombreComponente;

        //Se obtiene la informacion obtenida de la Vista.
        $componente->nombreComponente = $request->get('nombreComponente');
        $componente->idComponente = $request->get('idComponente');

        //Si la ID del Componente sufre cambio, se establece conexion con el servidor FTP.
        if($idComponenteTemp != $componente->idComponente){

            //Prepara la conexion al servidor FTP.
            $ssh = new SSH2($ftpParameters->getServerFTP());

            //Intenta hacer la conexion al servidor FTP.
            if(!$ssh->login($ftpParameters->getUserFTP(),$ftpParameters->getPassFTP())){
            
                //Se liberan los recursos.
                unset($ftpParameters,$ssh,$componente);
                //[FTP-ERROR002]: Problema con las credenciales del servidor FTP.
                return redirect('componenteop')->with('alert',$SWERROR->ErrorActual('FTPERROR002'));
                unset($SWERROR);
            }else{

                //Verifica si el Componente existe en el directorio Externo del repositorio Componentes.
                $estadoExiste = $ssh->exec('[ -d /home/Componentes/Externo/'.$idComponenteTemp.' ] && echo "1" || echo "0"');
            
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0];

                if($estadoExiste == '0'){

                    //Se liberan los recursos.       
                    unset($ssh,$ftpParameters,$asignar,$operario);

                    //[FTP-ERROR019]: El Componente no existe en el repositorio Componentes (Conflicto en directorio Externo).
                    return redirect('componenteop')->with('alert',$SWERROR->ErrorActual('FTPERROR019'));
                    unset($SWERROR);
                }else{

                    //Verifica si el Componente existe en el directorio Interno del repositorio Componentes.
                    $estadoExiste = $ssh->exec('[ -d /home/Componentes/Interno/'.$idComponenteTemp.' ] && echo "1" || echo "0"');
            
                    //Limpia la informacion obtenida.
                    $estadoExiste = $estadoExiste[0];

                    if($estadoExiste == '0'){

                        //Se liberan los recursos.       
                        unset($ssh,$ftpParameters,$asignar,$operario);

                        //[FTP-ERROR020]: El Componente no existe en el repositorio Componentes (Conflicto en directorio Interno).
                        return redirect('componenteop')->with('alert',$SWERROR->ErrorActual('FTPERROR020'));
                        unset($SWERROR);
                    }else{

                        //Verifica si el Componente existe en el directorio Externo del repositorio de Componentes.
                        $estadoExiste = $ssh->exec('[ -d /home/Componentes/Externo/'.$componente->idComponente.' ] && echo "1" || echo "0"');
            
                        //Limpia la informacion obtenida.
                        $estadoExiste = $estadoExiste[0];

                        if($estadoExiste == '1'){

                            //Se liberan los recursos.
                            unset($ftpParameters,$ssh,$componente);
                            //[FTP-ERROR017]: El Componente ya existe en el repositorio Componentes (Conflicto en directorio Externo).
                            return redirect('componenteop')->with('alert',$SWERROR->ErrorActual('FTPERROR017'));
                            unset($SWERROR);
                        }else{

                            //Verifica si el Componente existe en el directorio Interno del repositorio de Componentes.
                            $estadoExiste = $ssh->exec('[ -d /home/Componentes/Interno/'.$componente->idComponente.' ] && echo "1" || echo "0"');
                
                            //Limpia la informacion obtenida.
                            $estadoExiste = $estadoExiste[0];

                            if($estadoExiste == '1'){

                                //Se liberan los recursos.
                                unset($ftpParameters,$ssh,$componente);
                                //[FTP-ERROR018]: El Componente ya existe en el repositorio Componentes (Conflicto en directorio Interno).
                                return redirect('componenteop')->with('alert',$SWERROR->ErrorActual('FTPERROR018'));
                                unset($SWERROR);
                            }else{       
                                
                                //Se añade al historico de gestion.
                                DB::table('historico_gestions')->insert(['nombreGestion' => 'Componente', 
                                                                       'tipoGestion' => 'Editar',
                                                                       'responsableGestion' => $ftpParameters->getUserFTP(),
                                                                       'descripcionGestion' => 'Modificacion Actual => Componente: '.$componente->nombreComponente.', ID: '.$componente->idComponente.' | Datos Antiguos => Componente: '.$nombreComponenteTemp.', ID: '.$idComponenteTemp,
                                                                       'created_at' => now()]);

                                //Se actualiza el componente en la Base de Datos.
                                $componente->update();

                                //Se actualiza el nombre del directorio del Componente.
                                $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S mv /home/Componentes/Interno/'.$idComponenteTemp.' /home/Componentes/Interno/'.$componente->idComponente);
                                $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S mv /home/Componentes/Externo/'.$idComponenteTemp.' /home/Componentes/Externo/'.$componente->idComponente);

                                //Se enlista a todos los operarios que tengan asignado dicho componente.
                                $componentesAsignados = DB::table('asignars')
                                    ->where('asignars.componente_id', '=', $idComponenteTemp)
                                    ->select('asignars.*')
                                    ->get();

                                //Revisa a cada operario en busca del componente con el nombre antiguo.
                                foreach($componentesAsignados as $componenteAsignado){

                                    $rutOperarioFTP = Operario::findOrFail($componenteAsignado->operario_id)->rutOperarioFTP;
                                    $rutEmpresa = Empresa::findOrFail($componenteAsignado->empresa_id)->rutEmpresa;

                                    //Elimina el componente antiguo del operario.
                                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Externo/'.$rutEmpresa.'/'.$rutOperarioFTP.'/'.$idComponenteTemp);
                                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Interno/'.$rutEmpresa.'/'.$rutOperarioFTP.'/'.$idComponenteTemp);

                                    //Asigna la carpeta del Componente al Operario correspondiente.
                                    $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S rsync -av --delete /home/Componentes/Externo/".$componente->idComponente." /home/Externo/".$rutEmpresa."/".$rutOperarioFTP);
                                    $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S rsync -av --delete /home/Componentes/Interno/".$componente->idComponente." /home/Interno/".$rutEmpresa."/".$rutOperarioFTP);

                                    //Asigna al Operador como propietario del Componente asignado.
                                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S chown -R '.$operario->rutOperarioFTP.' /home/Externo/'.$rutEmpresa.'/'.$rutOperarioFTP.'/'.$componente->idComponente);
                                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S chown -R '.$operario->rutOperarioFTP.' /home/Interno/'.$rutEmpresa.'/'.$rutOperarioFTP.'/'.$componente->idComponente);
                                }
                                //Termina secuencia de comandos.                                
                                $ssh->exec('exit');
                                unset($componentesAsignados);
                            }
                        }
                    }
                }
            }

        }else{
            
            //Si la nombre del Componente sufre cambio, se establece conexion con el servidor FTP.
            if($nombreComponenteTemp != $componente->nombreComponente){
                
                //Se añade al historico de gestion.
                DB::table('historico_gestions')->insert(['nombreGestion' => 'Componente', 
                'tipoGestion' => 'Editar',
                'responsableGestion' => $ftpParameters->getUserFTP(),
                'descripcionGestion' => 'Modificacion Actual => Componente: '.$componente->nombreComponente.', ID: '.$componente->idComponente.' | Datos Antiguos => Componente: '.$nombreComponenteTemp.', ID: '.$idComponenteTemp,
                'created_at' => now()]);

                //Se actualiza el componente en la Base de Datos.
                $componente->update();
            }
        }

        //Se liberan los recursos.       
        unset($SWERROR,$ssh,$ftpParameters,$componente);
        return redirect('componenteop')->with('edit','El Componente se a editado');
    }

    public function destroy($id){

        //Carga el repositorio de errores.
        $SWERROR = new ErrorRepositorio();

        //Prepara los parametros de conexion al servidor FTP.
        $ftpParameters = new FtpConexion();

        $componente = Componente::findOrFail($id);
        
        //Prepara la conexion al servidor FTP.
        $ssh = new SSH2($ftpParameters->getServerFTP());

        //Intenta hacer la conexion al servidor FTP.
        if(!$ssh->login($ftpParameters->getUserFTP(),$ftpParameters->getPassFTP())){
        
            //Se liberan los recursos.
            unset($ftpParameters,$ssh,$componente);
            //[FTP-ERROR002]: Problema con las credenciales del servidor FTP.
            return redirect('componenteop')->with('alert',$SWERROR->ErrorActual('FTPERROR002'));
            unset($SWERROR);
        }else{

            //Se añade al historico de gestion.
            DB::table('historico_gestions')->insert(['nombreGestion' => 'Componente', 
                                                   'tipoGestion' => 'Eliminar',
                                                   'responsableGestion' => $ftpParameters->getUserFTP(),
                                                   'descripcionGestion' => 'Se ha Eliminado => Componente: '.$componente->nombreComponente.', ID: '.$componente->idComponente,
                                                   'created_at' => now()]);

            $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Componentes/Externo/'.$componente->idComponente);
            $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Componentes/Interno/'.$componente->idComponente);

            //Se enlista a todos los operarios que tengan asignado dicho componente.
            $componentesAsignados = DB::table('asignars')
                ->where('asignars.componente_id', '=', $componente->id)
                ->select('asignars.*')
                ->get();

            //Revisa a cada operario en busca del componente con el nombre antiguo.
            foreach($componentesAsignados as $componenteAsignado){

                $rutOperarioFTP = Operario::findOrFail($componenteAsignado->operario_id)->rutOperarioFTP;
                $rutEmpresa = Empresa::findOrFail($componenteAsignado->empresa_id)->rutEmpresa;

                //Elimina el componente del operario.
                $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Externo/'.$rutEmpresa.'/'.$rutOperarioFTP.'/'.$componente->idComponente);
                $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Interno/'.$rutEmpresa.'/'.$rutOperarioFTP.'/'.$componente->idComponente);
            }

            //Termina la secuencia de comandos.
            $ssh->exec('exit');
            unset($componentesAsignados);
        }

        $componente->delete();
        $componente->asignar()->delete();
      
        //Se liberan los recursos.       
        unset($SWERROR,$ssh,$ftpParameters,$componente);
        return redirect('componenteop')->with('success','El Componente se a eliminado correctamente.');
    }

    public function show($id){

        Session::put('componente_id',$id);       

        return redirect('documentosop');
    }
}