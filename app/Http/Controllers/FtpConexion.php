<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

// Clase encargada de almacenar repositorio de los errores relacionados al sistema web.
class FtpConexion{   

    //IP del servidor FTP.
    private $serverFTP = '192.168.0.17';
    
    //Credenciales de usuario FTP
    private $userFTP;
    private $passFTP;

    public function __construct(){

        $user = Auth::user();

        $this->userFTP = $user->userFTP;
        $this->passFTP = $user->passFTP;

        unset($user);
    }


    //Retorna la IP del servidor FTP.
    public function getServerFTP(){
        
        return $this->serverFTP;
    }

    //Retorna el nombre de usuario FTP.
    public function getUserFTP(){

        return $this->userFTP;
    }

    //Retorna la contraseña de usuario FTP.
    public function getPassFTP(){

        return $this->passFTP;
    }
}