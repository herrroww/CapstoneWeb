<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Empresa;

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
        
        $empresa = new Empresa();

        $empresa->rut = request('rut');
        $empresa->nombre = request('nombre');
        $empresa->compania = request('compania');      

        $ftp_server="192.168.0.28";
        $ftp_usuario="capstone";
        $ftp_pass="capstone";
    
        $con_id=ftp_connect($ftp_server);
        $lr=ftp_login($con_id,$ftp_usuario,$ftp_pass);

        if((!$con_id)||(!$lr)){

            echo "No se pudo conectar al servidor ftp";
            exit;

        }else{

            if (ftp_chdir($con_id, "ftp/OperariosExternos/")) {

                if (ftp_mkdir($con_id, $empresa->nombre)) {

                    if (ftp_cdup($con_id)) {

                        if (ftp_chdir($con_id, "OperariosInternos")) {

                            if (ftp_mkdir($con_id, $empresa->nombre)) {
            
                                //popup creacion de directorio
                                $empresa->save(); 
                            } else {
    
                                ftp_chdir($con_id,"OperariosExternos");
                                ftp_rmdir($con_id, $empresa->nombre);
                                echo "El directorio no pudo ser creado en la direccion ftp/OperariosInternos";
                            }
    
                        } else { 
                            
                            ftp_chdir($con_id,"OperariosExternos");
                            ftp_rmdir($con_id, $empresa->nombre);
                            echo "No existe el directorio ftp/OperariosInternos/ en la raiz del servidor FTP";
                        }

                    } else { 
                        
                        ftp_rmdir($con_id, $empresa->nombre);
                        echo "No se pudo cambiar al directorio ftp en la raiz del servidor FTP";
                    }

                } else {
                    
                    echo "El directorio no pudo ser creado en la direccion ftp/OperariosExternos";
                }

            } else { 

                echo "No existe el directorio ftp/OperariosExternos/ en la raiz del servidor FTP";
            }
        }

        ftp_close($con_id);        

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
            //ftp_pasv($con_id, true);     
            //ftp_chdir($con_id, "ftp/OperariosInternos/");
            //$val = ftp_chdir($con_id,"ftp/OperariosInternos");

            //var_dump($val);
            //die();
            //ftp_exec($con_id, "sudo rm -r /home/capstone/ftp/OperariosInternos/".$empresa->nombre);
            //$connection = ssh2_connect('192.168.0.28', 22);
            //ssh2_auth_password($connection, 'capstone', 'capstone');
            //ssh2_exec($connection, "rm -r /home/capstone/ftp/OperariosInternos/".$empresa->nombre);

            
            $ssh = new SSH2($this->server);
            $ssh->login($this->userFTP,$this->passFTP);

            $s = $ssh->exec('echo "baba" | tee -a /etc/vsftpd.userlist');
            var_dump($s);
            unset($ssh);
            die();

            //it's done :D
        }

        ftp_close($con_id);

        return redirect()->back()->with('success','La empresa a sido eliminada.'); 
        
    }
}