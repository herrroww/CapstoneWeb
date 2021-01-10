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

        return view('documentos.create',['activemenu' => 'componente']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){

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
            $login_result = ftp_login($conn_id, $ftpParameters->getUserFTP(), $ftpParameters->getPassFTP());
            
            if((!$conn_id) || (!$login_result)){

                unset($ftpParameters,$pkComponenteSeleccionado,$idComponenteSeleccionado,$data);
                //[FTP-ERROR027]: Problema al conectar al servidor FTP para insertar documento.
                exit($SWERROR->ErrorActual('FTPERROR027'));
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
                $ext = pathinfo($nombre, PATHINFO_EXTENSION);

                //Se prepara la conexion al servidor FTP.
                $ssh = new SSH2($ftpParameters->getServerFTP());

                //Intenta hacer la conexion al servidor FTP.
                if(!$ssh->login($ftpParameters->getUserFTP(),$ftpParameters->getPassFTP())){        
            
                    //Se liberan los recursos.           
                    unset($ssh,$ftpParameters);
                    //[FTP-ERROR002]: Problema con las credenciales del servidor FTP.
                    exit($SWERROR->ErrorActual('FTPERROR002'));
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
                            exit($SWERROR->ErrorActual('FTPERROR029'));
                            unset($SWERROR);
                        }else{

                            //[FTP-ERROR030]: El documento ya existe en el Componente (Conflicto en directorio Interno).
                            exit($SWERROR->ErrorActual('FTPERROR030'));
                            unset($SWERROR);
                        }                        
                    }else{         
                        
                        $data->nombre = pathinfo($nombre, PATHINFO_FILENAME);
                        $data->privacidad = $tipoPrivacidad;
                        $data->extension = $ext;
                        $data->componente_id = $pkComponenteSeleccionado;
                        $data->save();

                        //Limpia todo el contenido del directorio ComponenteTemp del usuario FTP.
                        $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/'.$ftpParameters->getUserFTP().'/ComponentesTemp/*');

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

                            $rutOperario = Operario::findOrFail($componenteAsignado->operario_id)->rutOperario;
                            $rutEmpresa = Empresa::findOrFail($componenteAsignado->empresa_id)->rutEmpresa;

                            //Elimina el componente antiguo del operario.
                            $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Externo/'.$rutEmpresa.'/'.$rutOperario.'/'.$idComponenteSeleccionado);
                            $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S rm -r /home/Interno/'.$rutEmpresa.'/'.$rutOperario.'/'.$idComponenteSeleccionado);

                            //Asigna la carpeta del Componente al Operario correspondiente.
                            $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S rsync -av --delete /home/Componentes/Externo/".$idComponenteSeleccionado." /home/Externo/".$rutEmpresa."/".$rutOperario);
                            $ssh->exec('echo '.$ftpParameters->getPassFTP()." | sudo -S rsync -av --delete /home/Componentes/Interno/".$idComponenteSeleccionado." /home/Interno/".$rutEmpresa."/".$rutOperario);

                            //Asigna al Operador como propietario del Componente asignado.
                            $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S chown -R '.$rutOperario.' /home/Externo/'.$rutEmpresa.'/'.$rutOperario.'/'.$idComponenteSeleccionado);
                            $ssh->exec('echo '.$ftpParameters->getPassFTP().' | sudo -S chown -R '.$rutOperario.' /home/Interno/'.$rutEmpresa.'/'.$rutOperario.'/'.$idComponenteSeleccionado);
                        }                        
                    }   

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

    public function download($file){

        return response()->download('storage/'.$file);       
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
        //
    }
}
