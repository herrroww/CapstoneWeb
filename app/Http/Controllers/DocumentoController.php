<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Documento;
use Session;
use App\Componente;
use App\Operario;
use App\Empresa;
use App\Asignar;


use App\Http\Controllers\ErrorRepositorio;
use App\Http\Controllers\FtpConexion;
use phpseclib\Net\SSH2;
use phpseclib\Net\SFTP;

use Illuminate\Support\Facades\DB;


class DocumentoController extends Controller{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(){

        $this->middleware('auth');
    }

    public function index(Request $request){
        
        if(!empty(Session::get('componente_id')) && $request){
            $query = trim($request->get('search'));

            $file = Documento::whereComponente_id(Session::get('componente_id') )
                ->where('nombre',  'LIKE', '%' . $query . '%')
                ->orderBy('id', 'asc')
                ->paginate(7);

            $componente = Componente::findOrFail(Session::get('componente_id'));

            return view("documentos.index", ['file' =>$file, 'search' => $query, 'activemenu' => 'componente','componente' =>$componente]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){

        $componente = Componente::findOrFail(Session::get('componente_id'));
        return view('documentos.create',['activemenu' => 'componente','componente' =>$componente]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){

        //Se establecen las reglas de validacion.
        $validatedData = $request->validate([
            'nombre' => 'required|min:4|max:100',
            'descripcion' => 'required|max:100',
        ]); 

        //Carga el repositorio de errores.
        $SWERROR = new ErrorRepositorio();

        //Prepara los parametros de conexion al servidor FTP.
        $ftpParameters = new FtpConexion();

        //Carga el PK del Componente seleccionado.
        $pkComponenteSeleccionado = Session::get('componente_id');
        
        $idComponenteSeleccionado = Componente::FindOrFail($pkComponenteSeleccionado)->idComponente;

        //Prepara el Documento a leer.
        $data= new Documento;

        //Si se ingresa un Documento.
        if($request->file('file')){

            //Prepara parametros para la conexion FTP.
            $conn_id = ftp_connect($ftpParameters->getServerFTP());            

            if((!$conn_id) || @!ftp_login($conn_id, $ftpParameters->getUserFTP(), $ftpParameters->getPassFTP())){

                unset($ftpParameters,$pkComponenteSeleccionado,$idComponenteSeleccionado,$data);
                //[FTP-ERROR027]: Problema al conectar al servidor FTP para insertar documento.
                return redirect('documentosop')->with('alert',$SWERROR->ErrorActual('FTPERROR027'));
                unset($SWERROR);
            }else{

                //Se obtiene el Documento.
                $file=$request->file('file');
                
                //Obtiene el tipo de privacidad del Componente seleccionado.
                $tipoPrivacidad = $request->privacidad;

                //Prepara la privacidad del Componente destino.
                $ubicacionComponente;

                if($tipoPrivacidad == "Publico"){

                    $ubicacionComponente = "Externo";
                }else{

                    $ubicacionComponente = "Interno";
                }
            
                //Obtiene la ubicacion del documento temporal.
                $temp = explode(".",$_FILES['file']['name']);
                $source_file = $_FILES['file']['tmp_name'];

                //Prepara la ubicacion destino del Documento a subir.
                $dst = 'ComponentesTemp';

                //Obtiene el nombre y la extension del documento a enviar.
                $nombre = $_FILES['file']['name'];
                $ext = $nombre;

                //Se prepara la conexion al servidor FTP.
                $ssh = new SSH2($ftpParameters->getServerFTP());

                //Intenta hacer la conexion al servidor FTP.
                if(!$ssh->login($ftpParameters->getUserFTP(),$ftpParameters->getPassFTP())){        
            
                    //Se liberan los recursos.           
                    unset($ssh,$ftpParameters);
                    //[FTP-ERROR002]: Problema con las credenciales del servidor FTP.
                    return redirect('documentosop')->with('alert',$SWERROR->ErrorActual('FTPERROR002'));
                    unset($SWERROR);
                }else{

                    //Verifica si el Documento existe en el directorio del Componente seleccionado en la ubicacion seleccionada.
                    $estadoExiste = $ssh->exec('[ -f /home/Componentes/'.$ubicacionComponente.'/'.$idComponenteSeleccionado.'/'.$nombre.' ] && echo "1" || echo "0"');
            
                    //Limpia la informacion obtenida.
                    $estadoExiste = $estadoExiste[0];
            
                    if($estadoExiste != '0'){

                        //Se liberan los recursos.           
                        unset($ftpParameters,$pkComponenteSeleccionado,$idComponenteSeleccionado,$data);
                        if($ubicacionComponente == "Externo"){

                            //[FTP-ERROR029]: El documento ya existe en el Componente (Conflicto en directorio Externo).
                            return redirect('documentosop')->with('alert',$SWERROR->ErrorActual('FTPERROR029'));
                            unset($SWERROR);
                        }else{

                            //[FTP-ERROR030]: El documento ya existe en el Componente (Conflicto en directorio Interno).
                            return redirect('documentosop')->with('alert',$SWERROR->ErrorActual('FTPERROR030'));
                            unset($SWERROR);
                        }                        
                    }else{         
                        
                        //Se obtiene la informacion del Documento.
                        $data->nombre = $request->nombre;
                        $data->descripcion=$request->descripcion;
                        $data->privacidad = $tipoPrivacidad;
                        $data->extension = $ext;
                        $data->componente_id = $pkComponenteSeleccionado;

                        //Se añade al historico de gestion.
                        DB::table('historico_gestions')->insert(['nombreGestion' => 'Documento', 
                                                               'tipoGestion' => 'Crear',
                                                               'responsableGestion' => $ftpParameters->getUserFTP(),
                                                               'descripcionGestion' => 'Se ha Creado => Documento: '.$data->nombre.', Archivo: '.$data->extension.', en el Componente: '.$idComponenteSeleccionado,
                                                               'created_at' => now()]);

                        //Se almacena la informacion del documento en la Base de Datos.
                        $data->save();

                        //Limpia todo el contenido del directorio ComponenteTemp del usuario FTP.
                        $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/'.$ftpParameters->getUserFTP().'/ComponentesTemp/*');

                        //Activar modo pasivo
                        ftp_pasv($conn_id, true);

                        //TODO: NOTIFICAR EL PESO DE LOS ARCHIVOS.
                        //Envia el componente al directorio ComponenteTemp del usuario FTP.
                        ftp_put($conn_id,$dst.'/'.$nombre,$source_file,FTP_BINARY);

                        /*Mueve el archivo en la ubicacion que corresponda.
                        * (Si es publico, sera guardado en el directorio Externo e Interno.)
                        * (Si es privado, sera guardado en el directorio Interno.)
                        */                        
                        if($ubicacionComponente == "Externo"){

                            $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S cp /home/'.$ftpParameters->getUserFTP().'/ComponentesTemp/'.$nombre.' /home/Componentes/Externo/'.$idComponenteSeleccionado);                            
                            $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S cp /home/'.$ftpParameters->getUserFTP().'/ComponentesTemp/'.$nombre.' /home/Componentes/Interno/'.$idComponenteSeleccionado);
                        }else{

                            $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S cp /home/'.$ftpParameters->getUserFTP().'/ComponentesTemp/'.$nombre.' /home/Componentes/Interno/'.$idComponenteSeleccionado);
                        }
                        
                        //Limpia todo el contenido del directorio ComponenteTemp del usuario FTP.
                        $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/'.$ftpParameters->getUserFTP().'/ComponentesTemp/*');

                        //Se enlista a todos los operarios que tengan asignado dicho componente.
                        $componentesAsignados = DB::table('asignars')
                        ->where('asignars.componente_id', '=', $pkComponenteSeleccionado)
                        ->select('asignars.*')
                        ->get();

                        //Revisa a cada operario en busca del componente con el nombre antiguo.
                        foreach($componentesAsignados as $componenteAsignado){

                            $rutOperarioFTP = Operario::findOrFail($componenteAsignado->operario_id)->rutOperarioFTP;
                            $rutEmpresa = Empresa::findOrFail($componenteAsignado->empresa_id)->rutEmpresa;

                            //Elimina el componente antiguo del operario.
                            $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Externo/'.$rutEmpresa.'/'.$rutOperarioFTP.'/'.$idComponenteSeleccionado);
                            $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Interno/'.$rutEmpresa.'/'.$rutOperarioFTP.'/'.$idComponenteSeleccionado);

                            //Asigna la carpeta del Componente al Operario correspondiente.
                            $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S rsync -av --delete /home/Componentes/Externo/".$idComponenteSeleccionado." /home/Externo/".$rutEmpresa."/".$rutOperarioFTP);
                            $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S rsync -av --delete /home/Componentes/Interno/".$idComponenteSeleccionado." /home/Interno/".$rutEmpresa."/".$rutOperarioFTP);

                            //Asigna al Operador como propietario del Componente asignado.
                            $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S chown -R '.$rutOperarioFTP.' /home/Externo/'.$rutEmpresa.'/'.$rutOperarioFTP.'/'.$idComponenteSeleccionado);
                            $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S chown -R '.$rutOperarioFTP.' /home/Interno/'.$rutEmpresa.'/'.$rutOperarioFTP.'/'.$idComponenteSeleccionado);
                        }                        
                    }   

                    //Limpia todo el contenido del directorio ComponenteTemp del usuario FTP.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/'.$ftpParameters->getUserFTP().'/ComponentesTemp/*');

                    //Se termina secuencia de comandos.
                    $ssh->exec('exit');   
                    //Se liberan los recursos.           
                    unset($ssh,$ftpParameters);  
                }
            }
        }
        return redirect('documentosop')->with('create','Se creo correctamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){

        $data=Documento::find($id);
        return view('documentos.details',['activemenu' => 'componente'],compact('data'));       
    }

    public function download($id){

        //Carga el repositorio de errores.
        $SWERROR = new ErrorRepositorio();

        //Prepara los parametros de conexion al servidor FTP.
        $ftpParameters = new FtpConexion();

        //Carga el PK del Componente seleccionado.
        $pkComponenteSeleccionado = Session::get('componente_id');
        
        $idComponenteSeleccionado = Componente::FindOrFail($pkComponenteSeleccionado)->idComponente;

        //Prepara el Documento a leer.
        $data = Documento::findOrFail($id);

        //Prepara la privacidad del Componente destino.
        $ubicacionComponente;

        if($data->privacidad == "Publico"){

            $ubicacionComponente = "Externo";
        }else{

            $ubicacionComponente = "Interno";
        }

        //Prepara la conexion al servidor FTP.
        $ssh = new SSH2($ftpParameters->getServerFTP());

        //Prepara parametros para la conexion FTP.
        $conn_id = ftp_connect($ftpParameters->getServerFTP());
        
        if((!$conn_id) || @!ftp_login($conn_id, $ftpParameters->getUserFTP(), $ftpParameters->getPassFTP())){

            unset($ftpParameters,$pkComponenteSeleccionado,$idComponenteSeleccionado,$data);
            //[FTP-ERROR027]: Problema al conectar al servidor FTP para insertar documento.
            return redirect('documentosop')->with('alert',$SWERROR->ErrorActual('FTPERROR027'));
            unset($SWERROR);
        }else{

            //Intenta hacer la conexion al servidor FTP.
            if(!$ssh->login($ftpParameters->getUserFTP(),$ftpParameters->getPassFTP())){
        
                //Se liberan los recursos.
                unset($documento,$ftpParameters,$ssh);
                //[FTP-ERROR002]: Problema con las credenciales del servidor FTP.
                return redirect('documentosop')->with('alert',$SWERROR->ErrorActual('FTPERROR002'));
                unset($SWERROR);
            }else{

                //Verifica si el Documento existe en el directorio del Componente seleccionado en la ubicacion del documento seleccionado.
                $estadoExiste = $ssh->exec('[ -f /home/Componentes/'.$ubicacionComponente.'/'.$idComponenteSeleccionado.'/'.$data->extension.' ] && echo "1" || echo "0"');

                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0];
            
                if($estadoExiste != '1'){

                    //Se liberan los recursos.           
                    unset($ftpParameters,$pkComponenteSeleccionado,$idComponenteSeleccionado,$data);
                    if($ubicacionComponente == "Externo"){

                        //[FTP-ERROR031]: El documento no existe en el Componente (Conflicto en directorio Externo).
                        return redirect('documentosop')->with('alert',$SWERROR->ErrorActual('FTPERROR031'));
                        unset($SWERROR);
                    }else{

                        //[FTP-ERROR032]: El documento no existe en el Componente (Conflicto en directorio Interno).
                        return redirect('documentosop')->with('alert',$SWERROR->ErrorActual('FTPERROR032'));
                        unset($SWERROR);
                    }                        
                }else{

                    //Limpia todo el contenido del directorio ComponenteTemp del usuario FTP.
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/'.$ftpParameters->getUserFTP().'/ComponentesTemp/*');

                    /*Mueve el archivo correspondiente a la carpeta ComponentesTemp del supervisor.
                    */                        
                    $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S cp /home/Componentes/'.$ubicacionComponente.'/'.$idComponenteSeleccionado.'/'.$data->extension.' /home/'.$ftpParameters->getUserFTP().'/ComponentesTemp');
                    
                    $documentoFTP = '/ComponentesTemp/'.$data->extension;
                    
                    //Activar modo pasivo
                    ftp_pasv($conn_id, true);
                    
                    // intenta descargar $server_file y guardarlo en $local_file
                    if (ftp_get($conn_id,'storage/'.$data->extension,$documentoFTP,FTP_BINARY)){

                        //Se ha descargado el archivo con exito
                    }
                    ftp_close($conn_id);  
                }

                //Limpia todo el contenido del directorio ComponenteTemp del usuario FTP.
                $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/'.$ftpParameters->getUserFTP().'/ComponentesTemp/*');
                
                //Se termina secuencia de comandos.
                $ssh->exec('exit');   
                //Se liberan los recursos.           
                unset($ssh,$ftpParameters);  
            }
        }
        
        return response()->download('storage/'.$data->extension);       
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){   

        //Carga el repositorio de errores.
        $SWERROR = new ErrorRepositorio();

        //Prepara los parametros de conexion al servidor FTP.
        $ftpParameters = new FtpConexion();

        //Carga el PK del Componente seleccionado.
        $pkComponenteSeleccionado = Session::get('componente_id');
        
        $idComponenteSeleccionado = Componente::FindOrFail($pkComponenteSeleccionado)->idComponente;

        //Busca el Documento seleccionado en la Base de Datos.
        $documento = Documento::findOrFail($id);
        
        //Prepara la conexion al servidor FTP.
        $ssh = new SSH2($ftpParameters->getServerFTP());

        //Intenta hacer la conexion al servidor FTP.
        if(!$ssh->login($ftpParameters->getUserFTP(),$ftpParameters->getPassFTP())){
        
            //Se liberan los recursos.
            unset($documento,$ftpParameters,$ssh);
            //[FTP-ERROR002]: Problema con las credenciales del servidor FTP.
            return redirect('documentosop')->with('alert',$SWERROR->ErrorActual('FTPERROR002'));
            unset($SWERROR);
        }else{

            //Se añade al historico de gestion.
            DB::table('historico_gestions')->insert(['nombreGestion' => 'Documento', 
                                                   'tipoGestion' => 'Eliminar',
                                                   'responsableGestion' => $ftpParameters->getUserFTP(),
                                                   'descripcionGestion' => 'Se ha Eliminado => Documento: '.$documento->nombre.', Archivo: '.$documento->extension.', en el Componente: '.$idComponenteSeleccionado,
                                                   'created_at' => now()]);

            //Elimina el Componente de la Base de Datos.
            $documento->delete();

            //Elimina el Documento del Componente ubicado en el repositorio Componentes.
            $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Componentes/Externo/'.$idComponenteSeleccionado.'/'.$documento->extension);

            //Elimina el Documento del Componente ubicado en el repositorio Componentes.
            $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Componentes/Interno/'.$idComponenteSeleccionado.'/'.$documento->extension);

            //Se enlista a todos los operarios que tengan asignado dicho componente.
            $componentesAsignados = DB::table('asignars')
                ->where('asignars.componente_id', '=', $pkComponenteSeleccionado)
                ->select('asignars.*')
                ->get();

            //Revisa a cada operario en busca del componente con el nombre antiguo.
            foreach($componentesAsignados as $componenteAsignado){

                $rutOperarioFTP = Operario::findOrFail($componenteAsignado->operario_id)->rutOperarioFTP;
                $rutEmpresa = Empresa::findOrFail($componenteAsignado->empresa_id)->rutEmpresa;

                //Elimina el componente antiguo del operario.
                $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Externo/'.$rutEmpresa.'/'.$rutOperarioFTP.'/'.$idComponenteSeleccionado);
                $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Interno/'.$rutEmpresa.'/'.$rutOperarioFTP.'/'.$idComponenteSeleccionado);
                
                //Asigna la carpeta del Componente al Operario correspondiente.
                $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S rsync -av --delete /home/Componentes/Externo/".$idComponenteSeleccionado." /home/Externo/".$rutEmpresa."/".$rutOperarioFTP);
                $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S rsync -av --delete /home/Componentes/Interno/".$idComponenteSeleccionado." /home/Interno/".$rutEmpresa."/".$rutOperarioFTP);
                
                //Asigna al Operador como propietario del Componente asignado.
                $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S chown -R '.$rutOperarioFTP.' /home/Externo/'.$rutEmpresa.'/'.$rutOperarioFTP.'/'.$idComponenteSeleccionado);
                $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S chown -R '.$rutOperarioFTP.' /home/Interno/'.$rutEmpresa.'/'.$rutOperarioFTP.'/'.$idComponenteSeleccionado);
            } 
            
            //Se termina secuencia de comandos.
            $ssh->exec('exit');   
            //Se liberan los recursos.           
            unset($documento,$ssh,$ftpParameters);  
        }       

        return redirect()->back()->with('success','El documento a sido eliminado.');
    }
}
