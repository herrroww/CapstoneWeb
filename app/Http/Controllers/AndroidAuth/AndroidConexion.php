<?php

// VARIABLES QUE ALMACENAN LA CONEXION A LA BASE DE DATOS
$mysqli = new mysqli(
    "localhost",
    "root",
    "",
    "capstoneweb"
);

// COMPROBAMOS LA CONEXION
if($mysqli->connect_errno) {
    //TODO: AñADIR EXCEPCION
    die("Fallo la conexion");
} else {
    // echo "Conexion exitosa";
}

?>