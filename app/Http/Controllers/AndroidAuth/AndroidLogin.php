<?php

use Illuminate\Support\Facades\Hash;

require("AndroidConexion.php");

// RECIBE LOS DATOS DE LA APP
$rutOperario = $_POST['rutOperario'];
$contraseniaOperario = $_POST['contraseniaOperario'];

// VERIFICAMOS QUE NO ESTEN VACIAS LAS VARIABLES
if(empty($rutOperario) || empty($contraseniaOperario)) {

    // SI ALGUNA VARIABLE ESTA VACIA MUESTRA ERROR
    //echo "Se deben llenar los dos campos";
    echo "ERROR 1";

} else {

    // CREAMOS LA CONSULTA
    $sql = "SELECT operarios.nombreOperario,operarios.rutOperario,operarios.correoOperario,operarios.contraseniaOperario,operarios.tipoOperario,operarios.contraseniaOperarioFTP,operarios.telefonoOperario,empresas.rutEmpresa FROM operarios JOIN empresas ON empresas.id=operarios.empresa_id WHERE operarios.rutOperario='$rutOperario'";
        
    $query = $mysqli->query($sql);

    // CREAMOS UN ARRAY PARA GUARDAR LOS VALORES DEL REGISTRO
    $data = array();

    // VARIABLE CON EL TOTAL DE REGISTROS OBTENIDOS
    $num = $query->num_rows;

    //VERIFICAMOS QUE EXISTE ALGUN REGISTRO
    if($num > 0) {
            
        $operarioEncontrado = false;        

        // AGREGAMOS LOS VALORES AL ARRAY
        while($resultado = $query->fetch_assoc()) {

            $data[] = $resultado;

            if(password_verify($contraseniaOperario,$data[0]['contraseniaOperario']) && $operarioEncontrado == false){

                $operarioEncontrado = true;

                //Se extrae la informacion obtenida.
                $rutOperario = $data[0]['rutOperario'];
                $nombreOperario = $data[0]['nombreOperario'];

                //Se inserta en la base de datos de registro de sesion.
                $sqlControlSesion = "INSERT INTO registrarsesionoperarios (rutOperario,nombreOperario,mensajeEvento,created_at) VALUES ('$rutOperario','$nombreOperario','Ha iniciado sesion',now())";
                $mysqli->query($sqlControlSesion);

                // CREAMOS EL JSON Y LO MOSTRAMOS
                die(json_encode($data));
            }                
        }

        if($operarioEncontrado == false){ 
            
            // echo usuario o contraseña no validos";
            die("ERROR 3");
        }

    } else {
        // echo "No existe ese registro";
        die("ERROR 2");
    }
}
?>