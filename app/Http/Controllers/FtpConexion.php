<?php

namespace App\Http\Controllers;

// Clase encargada de almacenar repositorio de los errores relacionados al sistema web.
class FtpConexion{   

    //IP del servidor FTP.
    private $serverFTP = '192.168.0.22';
    
    //Credenciales de usuario FTP
    private $userFTP= 'capstone';
    private $passFTP= 'asdasd';

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