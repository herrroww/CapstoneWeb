<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Empresa;

use App\Http\Controllers\ErrorRepositorio;

use phpseclib\Net\SSH2;

class EmpresaController extends Controller
{   

    private $server = '192.168.0.28';
    
    private $userFTP= 'capstone';
    private $passFTP= 'capstone';

    public function index(Request $request){

        if($request){
            $query = trim($request->get('search'));

            $empresas = Empresa::where('nombre',  'LIKE', '%' . $query . '%')
                ->orwhere('rut',  'LIKE', '%' . $query . '%')
                ->orderBy('id', 'asc')
                ->paginate(7);

            return view('empresas.index', ['empresas' => $empresas, 'search' => $query]);
        }    
        
    }
    
    public function create(){
        return view('empresas.create');
    }

    public function store(Request $request){

        $SWERROR = new ErrorRepositorio();
        
        $empresa = new Empresa();
        $empresa->rut = request('rut');
        $empresa->nombre = request('nombre');
        $empresa->compania = request('compania');      

        $ssh = new SSH2($this->server);
              
        if(!$ssh->login($this->userFTP,$this->passFTP)){
            
            exit($SWERROR->ErrorActual(1));
        }else{

            $estadoExiste = $ssh->exec('[ -d /home/capstone/ftp/OperariosExternos/'.$empresa->rut.' ] && echo "true" || echo "false"');
            $estadoExiste = $estadoExiste[0].$estadoExiste[1].$estadoExiste[2].$estadoExiste[3];
            //VERIFICAR AQUI SI EL DIRECTORIO EXISTE.
            if($estadoExiste == 'true'){

                exit($SWERROR->ErrorActual(2));
            }else{

                $estadoExiste = $ssh->exec('[ -d /home/capstone/ftp/OperariosInternos/'.$empresa->rut.' ] && echo "true" || echo "false"');
                $estadoExiste = $estadoExiste[0].$estadoExiste[1].$estadoExiste[2].$estadoExiste[3];
                if($estadoExiste == 'true'){

                    exit($SWERROR->ErrorActual(3));
                }else{

                    
                    $ssh->exec('mkdir /home/capstone/ftp/OperariosExternos/'.$empresa->rut);
                    $ssh->exec('mkdir /home/capstone/ftp/OperariosInternos/'.$empresa->rut);
                    $empresa->save();
                }
            }
        } 
            
               
        unset($SWERROR);
        unset($ssh);
        return redirect('empresaop')->with('create','La empresa se a creado correctamente');

    }

    public function edit($id){
        return view('empresas.edit', ['empresa' => Empresa::findOrFail($id)]);
    }

    public function update(Request $request, $id){
        $empresa = Empresa::findOrFail($id);
        
        $empresa->rut = $request->get('rut');
        $empresa->nombre = $request->get('nombre');
        $empresa->compania = $request->get('compania');
        

        $empresa->update();

        return redirect('empresaop')->with('edit','La empresa se a editado');
        ;
    }

    public function destroy($id){

        $empresa = Empresa::findOrFail($id);        

        $ftp_server="192.168.0.28";
        $ftp_usuario="capstone";
        $ftp_pass="capstone";
    
        $con_id=ftp_connect($ftp_server);
        $lr=ftp_login($con_id,$ftp_usuario,$ftp_pass);

        if((!$con_id)||(!$lr)){

            echo "No se pudo conectar al servidor ftp";
            exit;

        }else{

            
            /*if (ftp_chdir($con_id, "ftp/OperariosExternos/")) {

                $lists = ftp_mlsd($con_id, $empresa->nombre);
                unset($lists[0]);
                unset($lists[1]);

                foreach($lists as $list){
                    $full = $directory . '/' . $list['name'];
            
                    if($list['type'] == 'dir'){
                        ftp_rmdir($con_id, $full);
                    }else{
                        ftp_delete($con_id, $full);
                    }
                }

                ftp_rmdir($con_id,$empresa->nombre);

                if (ftp_cdup($con_id)) {

                    if (ftp_chdir($con_id, "ftp/OperariosInternos/")) {

                        $lists = ftp_mlsd($conn_id, $empresa->nombre);
                        unset($lists[0]);
                        unset($lists[1]);

                        foreach($lists as $list){
                            $full = $directory . '/' . $list['name'];
                    
                            if($list['type'] == 'dir'){
                                ftp_rmdir($con_id, $full);
                            }else{
                                ftp_delete($con_id, $full);
                            }
                        }

                    } else { 

                        echo "No existe el directorio ftp/OperariosInternos/ en la raiz del servidor FTP";
                    }

                } else { 
                    
                    ftp_rmdir($con_id, $empresa->nombre);
                    echo "No se pudo cambiar al directorio ftp en la raiz del servidor FTP";
                }
                
            } else { 

                echo "No existe el directorio ftp/OperariosExternos/ en la raiz del servidor FTP";
            }*/
            

            //it's done :D
        }

        ftp_close($con_id);

        $empresa->delete();
        return redirect()->back()->with('success','La empresa a sido eliminada.'); 
        
    }
}