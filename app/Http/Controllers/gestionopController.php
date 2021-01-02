<?php

namespace App\Http\Controllers;

use App\Http\Requests\OperarioFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Operario;
use App\Empresa;

use App\Http\Controllers\ErrorRepositorio;
use phpseclib\Net\SSH2;


class gestionopController extends Controller{

    //IP del servidor FTP.
    private $serverFTP = '192.168.0.28';
    
    //Credenciales de usuario FTP.
    private $userFTP= 'capstone';
    private $passFTP= 'capstone';


    public function __construct(){

        $this->middleware('auth');
    }

    public function index(Request $request){

        if($request){
            
            $query = trim($request->get('search'));            

            $operarios = Operario::where('nombre',  'LIKE', '%' . $query . '%')
                ->orwhere('rut',  'LIKE', '%' . $query . '%')
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

        //Genera al Operario y rellena los atributos con la informacion entregada por el usuario.
        
        $operario = new Operario();
        $operario->nombre = request('nombre');
        $operario->rut = preg_replace("/[^A-Za-z0-9]/",'',request('rut'));
        $operario->correo = request('correo');
        $tipoOperarioTemp = request('tipoOperario');
        $operario->tipoOperario = request('tipoOperario');
        $operario->empresa_id = request('empresa');

        $operario->contraseniaOperario = Hash::make(request('contraseniaOperario'));
        $operario->telefonoOperario = request('telefonoOperario');

        $operario->contraseniaOperarioFTP = substr(preg_replace("/[^A-Za-z0-9]/","",$operario->contraseniaOperario),0,10);

        //Obtiene el rut de la Empresa seleccionada.
        $rutEmpresa = Empresa::FindOrFail($operario->empresa_id)->rut;
 
        //Prepara la conexion al servidor FTP.
        $ssh = new SSH2($this->serverFTP);
              
        //Intenta hacer la conexion al servidor FTP.
        if(!$ssh->login($this->userFTP,$this->passFTP)){
            
            //TODO: Actualizar formato de error.
            //[SWERROR 002]: Problema al ingresar las credenciales de usuario FTP.
            exit($SWERROR->ErrorActual(1));
        }else{

            //Verifica si el directorio existe.
            $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$rutEmpresa.'/'.$operario->rut.' ] && echo "1" || echo "0"');
            
            //Limpia la informacion obtenida.
            $estadoExiste = $estadoExiste[0];
            
            if($estadoExiste == '1'){

                //TODO: Actualizar formato de error.
                //[SWERROR 007]: El Operario ya existe en el sistema FTP (Conflicto en OperariosExternos).
                exit($SWERROR->ErrorActual(6));
            }else{

                //Verifica si el directorio existe.
                $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$rutEmpresa.'/'.$operario->rut.' ] && echo "1" || echo "0"');
                
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0];

                if($estadoExiste == '1'){

                    //TODO: Actualizar formato de error.
                    //[SWERROR 008]: El Operario ya existe en el sistema FTP (Conflicto en OperariosInternos).
                    exit($SWERROR->ErrorActual(7));
                }else{
                    
                    //Crea al Operario y lo asigna al grupo "operariosftp".
                    $ssh->exec('echo '.$this->passFTP.' | sudo -S useradd -g operariosftp -s /bin/bash -p $(echo '.$operario->contraseniaOperarioFTP.' | openssl passwd -1 -stdin) '.$operario->rut); 
                    
                    //Crea la carpeta del Operario en la carpeta Interno y le asigna el grupo "operariosftp".
                    $ssh->exec('echo '.$this->passFTP.' | sudo -S mkdir -p /home/Externo/'.$rutEmpresa.'/'.$operario->rut);
                    $ssh->exec('echo '.$this->passFTP.' | sudo -S chown -R '.$operario->rut.':nogroup /home/Externo/'.$rutEmpresa.'/'.$operario->rut);
                    
                    $ssh->exec('echo '.$this->passFTP.' | sudo -S mkdir -p /home/Interno/'.$rutEmpresa.'/'.$operario->rut);
                    $ssh->exec('echo '.$this->passFTP.' | sudo -S chown -R '.$operario->rut.':operariosftp /home/Interno/'.$rutEmpresa.'/'.$operario->rut);
                    
                    //Añade al Operario en la lista de permisos VSFTPD y SSHD.
                    $ssh->exec('echo '.$this->passFTP." | sudo -S sed -i '$ a ".$operario->rut."' /etc/vsftpd.userlist");
                    $ssh->exec('echo '.$this->passFTP." | sudo -S sed -i '$ a DenyUsers ".$operario->rut."' /etc/ssh/sshd_config");
                    

                    //El Operario es Interno, se le reasigna el home.
                    if($operario->tipoOperario=="Interno"){

                        $ssh->exec('echo '.$this->passFTP." | sudo -S usermod -d /home/Interno/".$rutEmpresa." ".$operario->rut);
                    }else{

                        //En cualquier otro caso, se establece Operario Externo por defecto.
                        $ssh->exec('echo '.$this->passFTP." | sudo -S usermod -d /home/Externo/".$rutEmpresa."/".$operario->rut." ".$operario->rut);
                    }

                    //Reinicio de servicios para actualizar permisos.                    
                    $ssh->exec('echo '.$this->passFTP.' | sudo -S service vsftpd restart');
                    $ssh->exec('echo '.$this->passFTP.' | sudo -S service sshd restart');
                    $ssh->exec('exit');

                    //Almacena el Operario en la base de datos.
                    $operario->save();
                }
            }
        } 
            
        //Se liberan los recursos.       
        unset($SWERROR);
        unset($ssh);       

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
        
        //Busca al Operario dada una id de la tabla.
        $operario = Operario::findOrFail($id);

        //Extrae el rut y tipo (Externo o Interno) antiguo del Operario.
        $rutOperarioTemp = $operario->rut;
        $tipoOperarioTemp = $operario->tipoOperario;

        //Obtiene el rut de la Empresa del Operario seleccionado.
        $rutEmpresaTemp = Empresa::FindOrFail($operario->empresa_id)->rut;
        
        //Añaden los nuevos parametros correspondientes.
        $operario->nombre = $request->get('nombre');
        $operario->rut = $request->get('rut');
        $operario->correo = $request->get('correo');
        $operario->tipoOperario = request('tipoOperario');
        $operario->empresa_id = $request->get('empresa');
        $operario->contraseniaOperario = Hash::make(request('contraseniaOperario'));
        $operario->telefonoOperario =  $request->get('telefonoOperario');

        //Flags para actualizar datos de Operario.
        $actualizarGestionOperario = true;
        $actualizarGestionEmpresa = true;
        
        //Obtiene el rut de la nueva Empresa.
        $rutEmpresa = Empresa::FindOrFail($operario->empresa_id)->rut;

        //Se prepara la conexion al servidor FTP.
        $ssh = new SSH2($this->serverFTP);
              
        //Intenta hacer la conexion al servidor FTP.
        if(!$ssh->login($this->userFTP,$this->passFTP)){
            
            //TODO: Actualizar formato de error.
            //[SWERROR 002]: Problema al ingresar las credenciales de usuario FTP.
            exit($SWERROR->ErrorActual(1));
        }else{

            //Verifica si hay algun cambio en el rut del Operario.
            if($rutOperarioTemp != $operario->rut){

                //Verifica si el directorio del Oeprario existe en el directorio Externo.
                $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$rutEmpresaTemp.'/'.$rutOperarioTemp.' ] && echo "1" || echo "0"');
            
                //Limpia la informacion obtenida.
                $estadoExiste = $estadoExiste[0];
            
                if($estadoExiste == '0'){

                    //TODO: Actualizar formato de error.
                    //[SWERROR 007]: El Operario no existe en el sistema FTP (Conflicto en directorio 'Externo').
                    $actualizarGestionOperario = false;
                    exit($SWERROR->ErrorActual(6));
                }else{

                    //Verifica si el directorio existe en el directorio Interno.
                    $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$rutEmpresaTemp.'/'.$rutOperarioTemp.' ] && echo "1" || echo "0"');
                
                    //Limpia la informacion obtenida.
                    $estadoExiste = $estadoExiste[0];

                    if($estadoExiste == '0'){

                        //TODO: Actualizar formato de error.
                        //[SWERROR 008]: El Operario no existe en el sistema FTP (Conflicto en directorio 'Interno').
                        $actualizarGestionOperario = false;
                        exit($SWERROR->ErrorActual(7));
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
                    //[SWERROR 007]: El Operario no existe en el sistema FTP (Conflicto en directorio 'Externo').
                    $actualizarGestionEmpresa = false;
                    exit($SWERROR->ErrorActual(6));
                }else{

                    //Verifica si el directorio de la Empresa origen existe en el directorio Interno.
                    $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$rutEmpresaTemp.' ] && echo "1" || echo "0"');
                
                    //Limpia la informacion obtenida.
                    $estadoExiste = $estadoExiste[0];

                    if($estadoExiste == '0'){

                        //TODO: Actualizar formato de error.
                        //[SWERROR 008]: El Operario no existe en el sistema FTP (Conflicto en directorio 'Interno').
                        $actualizarGestionEmpresa = false;
                        exit($SWERROR->ErrorActual(7));
                    }else{
                     
                        //Verifica si el directorio de la Empresa Destino existe en el directorio Externo.
                        $estadoExiste = $ssh->exec('[ -d /home/Externo/'.$rutEmpresa.' ] && echo "1" || echo "0"');
            
                        //Limpia la informacion obtenida.
                        $estadoExiste = $estadoExiste[0];
            
                        if($estadoExiste == '0'){

                            //TODO: Actualizar formato de error.
                            //[SWERROR 007]: El Operario no existe en el sistema FTP (Conflicto en directorio 'Externo').
                            $actualizarGestionEmpresa = false;
                            exit($SWERROR->ErrorActual(6));
                        }else{

                            //Verifica si el directorio de la Empresa destino existe en el directorio Interno.
                            $estadoExiste = $ssh->exec('[ -d /home/Interno/'.$rutEmpresa.' ] && echo "1" || echo "0"');
                
                            //Limpia la informacion obtenida.
                            $estadoExiste = $estadoExiste[0];

                            if($estadoExiste == '0'){

                                //TODO: Actualizar formato de error.
                                //[SWERROR 008]: El Operario no existe en el sistema FTP (Conflicto en directorio 'Interno').
                                $actualizarGestionEmpresa = false;
                                exit($SWERROR->ErrorActual(7));
                            }
                        }
                    }
                }                
            }
            
            //Verifica si es posible realizar los cambios.
            if($actualizarGestionOperario == true && $actualizarGestionEmpresa == true){

                if($rutOperarioTemp != $operario->rut){

                    //Actualiza el nuevo username del Operario.
                    $ssh->exec('echo '.$this->passFTP.' | sudo -S usermod -l '.$operario->rut.' '.$rutOperarioTemp);

                    //Renombra los directorios relacionados al Operario.
                    $ssh->exec('echo '.$this->passFTP.' | sudo -S mv /home/Externo/'.$rutEmpresaTemp.'/'.$rutOperarioTemp.' /home/Externo/'.$rutEmpresaTemp.'/'.$operario->rut);
                    $ssh->exec('echo '.$this->passFTP.' | sudo -S mv /home/Externo/'.$rutEmpresaTemp.'/'.$rutOperarioTemp.' /home/Externo/'.$rutEmpresaTemp.'/'.$operario->rut);

                    //Añade el nuevo username del operario en la lista de permisos VSFTPD y SSHD.
                    $ssh->exec('echo '.$this->passFTP." | sudo -S sed -i '$ a ".$operario->rut."' /etc/vsftpd.userlist");
                    $ssh->exec('echo '.$this->passFTP." | sudo -S sed -i '$ a DenyUsers ".$operario->rut."' /etc/ssh/sshd_config");

                    //Reinicio de servicios para actualizar permisos.                    
                    $ssh->exec('echo '.$this->passFTP.' | sudo -S service vsftpd restart');
                    $ssh->exec('echo '.$this->passFTP.' | sudo -S service sshd restart');
                }

                if($rutEmpresaTemp != $rutEmpresa){  

                    //Mueve la carpeta del Operario a la nueva Empresa.
                    $ssh->exec('echo '.$this->passFTP.' | sudo -S mv /home/Externo/'.$rutEmpresaTemp.'/'.$operario->rut.' /home/Externo/'.$rutEmpresa.'/'.$operario->rut);
                    $ssh->exec('echo '.$this->passFTP.' | sudo -S mv /home/Interno/'.$rutEmpresaTemp.'/'.$operario->rut.' /home/Interno/'.$rutEmpresa.'/'.$operario->rut);
                }

                //El Operario es Interno, se le reasigna el home.
                if($operario->tipoOperario=="Interno"){

                    $ssh->exec('echo '.$this->passFTP." | sudo -S usermod -d /home/Interno/".$rutEmpresa." ".$operario->rut);
                }else{

                    //En cualquier otro caso, se establece Operario Externo por defecto.
                    $ssh->exec('echo '.$this->passFTP." | sudo -S usermod -d /home/Externo/".$rutEmpresa."/".$operario->rut." ".$operario->rut);
                }

                $ssh->exec('exit');
                $operario->update();        
            }            
        } 
            
        //Finaliza secuencia de comandos.
        $ssh->exec('exit');
        //Se liberan los recursos.       
        unset($SWERROR);
        unset($ssh);  

        return redirect('gestionop')->with('edit','');
    }

    public function destroy($id){

        //Carga el repositorio de errores.
        $SWERROR = new ErrorRepositorio();
        
        //Busca al operario dada una id de la tabla.
        $operario = Operario::findOrFail($id);

        //Se obtiene el rut de la empresa del operario seleccionado.
        $rutEmpresa = Empresa::FindOrFail($operario->empresa_id)->rut;


        //Se prepara la conexion al servidor FTP.
        $ssh = new SSH2($this->serverFTP);
              
        //Intenta hacer la conexion al servidor FTP.
        if(!$ssh->login($this->userFTP,$this->passFTP)){
            
            //[SWERROR 002]: Problema al ingresar las credenciales de usuario FTP.
            //TODO: Actualizar formato de error.
            exit($SWERROR->ErrorActual(1));
        }else{

            //Verifica si el directorio existe.
            $estadoExiste = $ssh->exec('[ -d /home/'.$operario->tipoOperario.'/'.$rutEmpresa.'/'.$operario->rut.' ] && echo "1" || echo "0"');
            
            //Limpia la informacion obtenida.
            $estadoExiste = $estadoExiste[0];
            
            if($estadoExiste != '1'){

                //[SWERROR 009]: El operario no existe en el sistema FTP (Conflicto en OperariosExternos).
                exit($SWERROR->ErrorActual(8));
            }else{

                $ssh->exec('echo '.$this->passFTP.' | sudo -S userdel '.$operario->rut);

                //Se elimina los directorios del operario. (Opcion 1)                  
                $ssh->exec('echo '.$this->passFTP.' | sudo -S rm -r /home/Externo/'.$rutEmpresa.'/'.$operario->rut);
                $ssh->exec('echo '.$this->passFTP.' | sudo -S rm -r /home/Interno/'.$rutEmpresa.'/'.$operario->rut);

                //Se envia el directorio de la empresa a la basura. (Opcion 2)
                //$ssh->exec('echo '.$this->passFTP.' | sudo -S gvfs-trash /home/Externo/'.$rutEmpresa.'/'.$operario->rut);
                //$ssh->exec('echo '.$this->passFTP.' | sudo -S gvfs-trash /home/Interno/'.$rutEmpresa.'/'.$operario->rut);

                //Finaliza secuencia de comandos.
                $ssh->exec('exit');
                //Se elimina el operario en la base de datos.
                $operario->delete();                
            }
        }        

        //Se liberan los recursos.       
        unset($SWERROR);
        unset($ssh);       

        return redirect()->back()->with('success','La empresa a sido eliminada.');
    }
}
