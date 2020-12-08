<?php

    require("AndroidConexion.php");

    // RECIBE LOS DATOS DE LA APP
    $correo = $_POST['correo'];
    $rut = $_POST['rut'];

    // DATOS DE PRUEBA
    // $correo = "admin";
    // $rut = "admin";

    // VERIFICAMOS QUE NO ESTEN VACIAS LAS VARIABLES
    if(empty($correo) || empty($rut)) {

        // SI ALGUNA VARIABLE ESTA VACIA MUESTRA ERROR
        //echo "Se deben llenar los dos campos";
        echo "ERROR 1";

    } else {

        // CREAMOS LA CONSULTA
        $sql = "SELECT * FROM operarios WHERE correo='$correo' AND rut='$rut'";
        $query = $mysqli->query($sql);

        // CREAMOS UN ARRAY PARA GUARDAR LOS VALORES DEL REGISTRO
        $data = array();

        // VARIABLE CON EL TOTAL DE REGISTROS OBTENIDOS
        $num = $query->num_rows;

        //VERIFICAMOS QUE EXISTE ALGUN REGISTRO
        if($num > 0) {
            
            // AGREGAMOS LOS VALORES AL ARRAY
            while($resultado = $query->fetch_assoc()) {
                $data[] = $resultado;

                // CREAMOS EL JSON Y LO MOSTRAMOS
                echo json_encode($data);
            }

        } else {
            // echo "No existe ese registro";
            echo "ERROR 2";
        }
    }

?>