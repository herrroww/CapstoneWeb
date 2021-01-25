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
        'FTPERROR027' => "[FTP-ERROR027]: Problema al conectar al servidor FTP para insertar documento.",
        'FTPERROR028' => "[FTP-ERROR028]: Problema al conectar al servidor FTP para decargar documento.",
        'FTPERROR029' => "[FTP-ERROR029]: El documento ya existe en el Componente (Conflicto en directorio Externo).",
        'FTPERROR030' => "[FTP-ERROR030]: El documento ya existe en el Componente (Conflicto en directorio Interno).",
        'FTPERROR031' => "[FTP-ERROR031]: El documento no existe en el Componente (Conflicto en directorio Externo).",
        'FTPERROR032' => "[FTP-ERROR032]: El documento no existe en el Componente (Conflicto en directorio Interno).",
        'FTPERROR033' => '[FTP-ERROR033]: El documento no puede contener espacios ni los siguientes caracteres: \^£$ºª€%&*()}¡ç!";:{@#~¿?><,|=+¬-',
        'FTPERROR034' => '[FTP-ERROR034]: El documento no puede pesar mas de 200Mb.',
        'FTPERROR035' => '[FTP-ERROR035]: Algo ha ocurrido y la Empresa no pudo ser creada.',
        'FTPERROR036' => '[FTP-ERROR036]: Algo ha ocurrido y la Empresa no pudo ser editada.',
        'FTPERROR037' => '[FTP-ERROR037]: Algo ha ocurrido y la Empresa no pudo ser eliminada.',
        'FTPERROR038' => '[FTP-ERROR038]: Algo ha ocurrido y el Operario no pudo ser creado.',
        'FTPERROR039' => '[FTP-ERROR039]: Algo ha ocurrido y el Operario no pudo ser editado.',
        'FTPERROR040' => '[FTP-ERROR040]: Algo ha ocurrido y el Operario no pudo ser eliminado.',
        'FTPERROR041' => '[FTP-ERROR041]: Algo ha ocurrido y el Componente no pudo ser creado.',
        'FTPERROR042' => '[FTP-ERROR042]: Algo ha ocurrido y el Componente no pudo ser editado.',
        'FTPERROR043' => '[FTP-ERROR043]: Algo ha ocurrido y el Componente no pudo ser eliminado.',
        'FTPERROR044' => '[FTP-ERROR044]: Algo ha ocurrido y el Documento no pudo ser creado.',
        'FTPERROR045' => '[FTP-ERROR045]: Algo ha ocurrido y el Documento no pudo ser eliminado.',
        'FTPERROR046' => '[FTP-ERROR046]: Algo ha ocurrido y la Asignación no pudo ser creada.',
        'FTPERROR047' => '[FTP-ERROR047]: Algo ha ocurrido y la Asignación no pudo ser editada.',
        'FTPERROR048' => '[FTP-ERROR048]: Algo ha ocurrido y la Asignación no pudo ser eliminada.',
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
