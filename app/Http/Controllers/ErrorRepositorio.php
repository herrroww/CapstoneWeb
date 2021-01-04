<?php

namespace App\Http\Controllers;

// Clase encargada de almacenar repositorio de los errores relacionados al sistema web.
class ErrorRepositorio{   

    private $SWERROR = array(

        'FTPERROR001' => "[FTP-ERROR001]: Problema al conectar con el servidor FTP.",
        'FTPERROR002' => "[FTP-ERROR002]: Problema con las credenciales del servidor FTP.",
        'FTPERROR003' => "[FTP-ERROR003]: La Empresa ya existe en el sistema (Conflicto en directorio Externo).",
        'FTPERROR004' => "[FTP-ERROR004]: La Empresa ya existe en el sistema (Conflicto en directorio Interno).",
        'FTPERROR005' => "[FTP-ERROR005]: La Empresa no existe en el sistema (Conflicto en directorio Externo).",
        'FTPERROR006' => "[FTP-ERROR006]: La Empresa no existe en el sistema (Conflicto en directorio Interno).",
        'FTPERROR007' => "[FTP-ERROR007]: El Operario ya existe en el sistema (Conflicto en directorio Externo).",
        'FTPERROR008' => "[FTP-ERROR008]: El Operario ya existe en el sistema (Conflicto en directorio Interno).",
        'FTPERROR009' => "[FTP-ERROR009]: El Operario no existe en el sistema (Conflicto en directorio Externo).",
        'FTPERROR010' => "[FTP-ERROR010]: El Operario no existe en el sistema (Conflicto en directorio Interno).",
        'FTPERROR011' => "[FTP-ERROR011]: La Empresa destino ya posee un Operador con dicho rut (Conflicto en directorio Externo).",
        'FTPERROR012' => "[FTP-ERROR012]: La Empresa destino ya posee un Operador con dicho rut (Conflicto en directorio Interno).",
        'FTPERROR013' => "[FTP-ERROR013]: La Empresa origen no existe en el sistema (Conflicto en directorio Externo).",
        'FTPERROR014' => "[FTP-ERROR014]: La Empresa origen no existe en el sistema (Conflicto en directorio Interno).",
        'FTPERROR015' => "[FTP-ERROR015]: La Empresa destino no existe en el sistema (Conflicto en directorio Externo).",
        'FTPERROR016' => "[FTP-ERROR016]: La Empresa destino no existe en el sistema (Conflicto en directorio Interno).",
        'FTPERROR017' => "[FTP-ERROR017]: El Componente ya existe en el repositorio Componentes (Conflicto en directorio Externo).",
        'FTPERROR018' => "[FTP-ERROR018]: El Componente ya existe en el repositorio Componentes (Conflicto en directorio Interno).",
        'FTPERROR019' => "[FTP-ERROR019]: El Componente no existe en el repositorio Componentes (Conflicto en directorio Externo).",
        'FTPERROR020' => "[FTP-ERROR020]: El Componente no existe en el repositorio Componentes (Conflicto en directorio Interno).",
        'FTPERROR021' => "[FTP-ERROR021]: El Operario origen no existe en el sistema (Conflicto en directorio Externo).",
        'FTPERROR022' => "[FTP-ERROR022]: El Operario origen no existe en el sistema (Conflicto en directorio Interno).",
        'FTPERROR023' => "[FTP-ERROR023]: El Operario destino no existe en el sistema (Conflicto en directorio Externo).",
        'FTPERROR024' => "[FTP-ERROR024]: El Operario destino no existe en el sistema (Conflicto en directorio Interno).",
        'FTPERROR025' => "[FTP-ERROR025]: El Operario destino ya posee un Componente con dicha ID (Conflicto en directorio Externo).",
        'FTPERROR026' => "[FTP-ERROR026]: El Operario destino ya posee un Componente con dicha ID (Conflicto en directorio Interno).",
        'FTPERROR???' => "[FTP-ERROR???]: ???",
        'WEBERROR001' => "[WEB-ERROR001]: Problema al conectar con el servidor de Base de Datos."
    );

    // Dado un valor, retorna el error correspondiente.
    public function ErrorActual($valor){
        
        return $this->SWERROR[$valor];
    }

    //****HISTORICO DE CONTROL DE ERRORES****

    /*FTPERROR001
      Ubicacion original del error: ./CAPSTONEWEB/vendor/phpseclib\phpseclib/phpseclib/Net/SSH2.php
      Linea de Codigo: 1172
    */

    /*WEBERROR001
      Ubicacion original del error: ./CAPSTONEWEB/vendor/laravel/framework/src/Illuminate/Database/Connection.php
      Linea de Codigo: 674
    */
}
