@extends('layouts.sidebar')

@section('content')
<!DOCTYPE html>
<html lang="es">
	<head>
		<title>Tabla De Datos</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
		<!-- ESTILOS -->
		<link href="css/estilos.css" rel="stylesheet">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
		<!-- SCRIPTS JS-->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="peticion.js"></script>
	</head>
	<body>

		<header>
			<div class="alert alert-info">
			<h2>Tabla de Datos Gestion operarios</h2>
			</div>
		</header>

		<div class="container-fluid " >
            <div class="row-flex " >
   		        <a class="col"  href="index.php" >
   			        <button class="btn btn-primary  ">Volver</button>
   		        </a>
   		    </div>
   		</div>
   	</div>

		<div class="container-fluid " >
            <div class="row-flex " >


		        <a>Insertar Rut</a>

		        <section>
			        <input type="text" name="busqueda" id="busqueda" placeholder="Insertar rut">
		        </section>

		<section id="tabla_resultado">
		<!-- AQUI SE DESPLEGARA NUESTRA TABLA DE CONSULTA -->
		</section>


	</body>
</html>

@endsection