<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use App\Componente;
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
            exit($SWERROR->ErrorActual('FTPERROR002'));
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
                exit($SWERROR->ErrorActual('FTPERROR017'));
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
                    exit($SWERROR->ErrorActual('FTPERROR018'));
                    unset($SWERROR);
                }else{
                    
                    //Se guardan los cambios en la Base de Datos.
                    $componente->save();

                    //Se aplican los cambios en el servidor FTP.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S mkdir -p /home/Componentes/Externo/'.$componente->IdComponente);
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S mkdir -p /home/Componentes/Interno/'.$componente->IdComponente);
                
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

        //Carga el repositorio de errores.
        $SWERROR = new ErrorRepositorio();

        //Prepara los parametros de conexion al servidor FTP.
        $ftpParameters = new FtpConexion();

        //Se obtiene la informacion del Componente seleccionado.
        $componente = Componente::findOrFail($id);

        //Se guarda de forma temporal la ID original del Componente seleccionado.
        $idComponenteTemp = $componente->idComponente;

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
                exit($SWERROR->ErrorActual('FTPERROR002'));
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
                    exit($SWERROR->ErrorActual('FTPERROR019'));
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
                        exit($SWERROR->ErrorActual('FTPERROR020'));
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
                            exit($SWERROR->ErrorActual('FTPERROR017'));
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
                                exit($SWERROR->ErrorActual('FTPERROR018'));
                                unset($SWERROR);
                            }else{                                

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

                                    $rutOperario = Operario::findOrFail($componenteAsignado->operario_id)->rutOperario;
                                    $rutEmpresa = Empresa::findOrFail($componenteAsignado->empresa_id)->rutEmpresa;

                                    //Elimina el componente antiguo del operario.
                                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Externo/'.$rutEmpresa.'/'.$rutOperario.'/'.$idComponenteTemp);
                                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Interno/'.$rutEmpresa.'/'.$rutOperario.'/'.$idComponenteTemp);

                                    //Asigna la carpeta del Componente al Operario correspondiente.
                                    $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S rsync -av --delete /home/Componentes/Externo/".$componente->idComponente." /home/Externo/".$rutEmpresa."/".$rutOperario);
                                    $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S rsync -av --delete /home/Componentes/Interno/".$componente->idComponente." /home/Interno/".$rutEmpresa."/".$rutOperario);

                                    //Asigna al Operador como propietario del Componente asignado.
                                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S chown -R '.$operario->rutOperario.':operariosftp /home/Externo/'.$rutEmpresa.'/'.$rutOperario.'/'.$componente->idComponente);
                                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S chown -R '.$operario->rutOperario.':operariosftp /home/Interno/'.$rutEmpresa.'/'.$rutOperario.'/'.$componente->idComponente);
                                }                                
                                $ssh->exec('exit');
                                unset($componentesAsignados);
                            }
                        }
                    }
                }
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
            exit($SWERROR->ErrorActual('FTPERROR002'));
            unset($SWERROR);
        }else{

            $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Componentes/Externo/'.$componente->idComponente);
            $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Componentes/Interno/'.$componente->idComponente);

            //Se enlista a todos los operarios que tengan asignado dicho componente.
            $componentesAsignados = DB::table('asignars')
                ->where('asignars.componente_id', '=', $componente->id)
                ->select('asignars.*')
                ->get();

            //Revisa a cada operario en busca del componente con el nombre antiguo.
            foreach($componentesAsignados as $componenteAsignado){

                $rutOperario = Operario::findOrFail($componenteAsignado->operario_id)->rutOperario;
                $rutEmpresa = Empresa::findOrFail($componenteAsignado->empresa_id)->rutEmpresa;

                //Elimina el componente del operario.
                $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Externo/'.$rutEmpresa.'/'.$rutOperario.'/'.$componente->idComponente);
                $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Interno/'.$rutEmpresa.'/'.$rutOperario.'/'.$componente->idComponente);
            }

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