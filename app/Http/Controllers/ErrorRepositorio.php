<?php

namespace App\Http\Controllers;

// Clase encargada de almacenar repositorio de los errores relacionados al sistema web.
class ErrorRepositorio
{   

    private $SWERROR = array(
        "[SWERROR 001]: Problema al conectar con el servidor FTP.",
        "[SWERROR 002]: Problema al ingresar las credenciales de usuario FTP.",
        "[SWERROR 003]: La empresa ya existe en el sistema FTP (Conflicto en OperariosExternos).",
        "[SWERROR 004]: La empresa ya existe en el sistema FTP (Conflicto en OperariosInternos).",
        "[SWERROR 005]: La empresa no existe en el sistema FTP (Conflicto en OperariosExternos).",
        "[SWERROR 006]: La empresa no existe en el sistema FTP (Conflicto en OperariosInternos).",
        "[SWERROR 007]: El operario ya existe en el sistema FTP (Conflicto en OperariosExternos).",
        "[SWERROR 008]: El operario ya existe en el sistema FTP (Conflicto en OperariosInternos).",
        "[SWERROR 009]: El operario no existe en el sistema FTP (Conflicto en OperariosExternos).",
        "[SWERROR 010]: El operario no existe en el sistema FTP (Conflicto en OperariosInternos).",
        "[SWERROR 011]: ???",
    );

    // Dado un valor, retorna el error correspondiente.
    public function ErrorActual($valor){
        return $this->SWERROR[$valor];
    }
}