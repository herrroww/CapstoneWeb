<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;

require("AndroidConexion.php");

// RECIBE LOS DATOS DE LA APP
$rutOperario = $_POST['rutOperario'];
$nombreOperario = $_POST['nombreOperario'];
$correoOperario = $_POST['correoOperario'];
$numeroOperario = $_POST['numeroOperario'];
$prioridad = $_POST['prioridad'];
$estado = $_POST['estado'];
$fechaReporteProblema = $_POST['fechaReporteProblema'];
$tituloReporteProblema = $_POST['tituloReporteProblema'];
$contenidoReporteProblema = $_POST['contenidoReporteProblema'];

$consulta = "INSERT INTO reporteproblemas (rutOperario,nombreOperario,correoOperario,numeroOperario,prioridad,estado,fechaReporteProblema,tituloReporteProblema,contenidoReporteProblema,created_at) VALUES ('$rutOperario','$nombreOperario','$correoOperario','$numeroOperario','$prioridad','$estado','$fechaReporteProblema','$tituloReporteProblema','$contenidoReporteProblema',now())";


// VERIFICAMOS QUE NO ESTEN VACIAS LAS VARIABLES
if(empty($rutOperario) || empty($nombreOperario) || empty($correoOperario) || empty($numeroOperario) || empty($prioridad) || empty($estado) || empty($fechaReporteProblema) || empty($tituloReporteProblema) || empty($contenidoReporteProblema)) {

    // SI ALGUNA VARIABLE ESTA VACIA MUESTRA ERROR
    //echo "Se deben llenar los dos campos";
    echo "ERROR 1";

} else {
   
    if($mysqli->query($consulta)){

        echo "Recibido";
    }else{

        echo "Rechazado";
    }

}
?>