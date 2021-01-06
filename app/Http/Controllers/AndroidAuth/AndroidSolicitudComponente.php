<?php

use Illuminate\Support\Facades\Hash;

require("AndroidConexion.php");

// RECIBE LOS DATOS DE LA APP
$idOperario = $_POST['idOperario'];
$idComponente = $_POST['idComponente'];

// CREAMOS LA CONSULTA
$sql = "SELECT componentes.id FROM componentes JOIN asignars ON asignars.componente_id=componentes.id AND asignars.operario_id=$idOperario WHERE componentes.idComponente='$idComponente'";
     

//Verifica que los valores de entrada no sean vacios.
if(empty($idOperario) || empty($idComponente)) {
    
    die("ERROR 1");

} else {

    //Realiza la consulta.
    $resultado = $mysqli->query($sql);

    //Obtiene las filas del resultado obtenido.
    $num = $resultado->num_rows;

    //Si existe un resultado o mas.
    if($num > 0){

        die();
    }else{

        die("ERROR 2");
    }
}
?>