
@extends('layouts.sidebar')

@section('content')


<div class="container">
    <div class="row">
        <div class="col-sm-6">
            <h3>Agregar Operario</h3>
            <hr>



<form action="gestionop1" method="POST">
    @csrf
  <div class="form-group">
    <label for="nombre">Nombre:</label>
    <input type="text" class="form-control" name="nombreOperario" placeholder="Escriba nombre operario" required>
  </div>

  <form>
  <div class="form-group">
  
    <label style="margin-top: 20px" for="rut">Rut: </label><div style="margin-right: 100px" class="alert alert-info float-right" role="alert">
  Colocar rut solo con guión ejemplo: 11.111.111-1</div>
  
    <input type="text" class="form-control" name="rutOperario" placeholder="Escriba rut del operario" required>
  </div>

  <form>
  <div class="form-group">
    <label for="correo">Correo:</label>
    <input type="email" class="form-control" name="correoOperario" placeholder="Escriba correo del operario" required>
  </div>

  <div class="form-group">
 <strong style="margin-bot: 10px">Empresa:</strong>
 
 <select name="empresa" class="form-control" id="empresaselect">
 <option value="" required>Seleccionar Empresa</oprion>
@foreach($empresa as $empresas)
<option value="{{ $empresas->id }}" required>{{ $empresas->nombreEmpresa}} | Rut:{{ $empresas->rutEmpresa}}</oprion>
@endforeach

</select>
</div>

  <div class="form-group">
    <label for="telefonoOperario">Teléfono Operario:</label>
    <input type="text" class="form-control" name="telefonoOperario" placeholder="Escriba el teléfono del operario" required>
  </div>
  
		
<!--<form action="gestionop1" method="POST" class="formulario" id="formulario">-->
			<!-- Grupo: Contraseña -->
			<div class="formulario__grupo" id="grupo__contraseniaOperario">
				<label for="contraseniaOperario" class="formulario__label">Contraseña</label>
				<div class="formulario__grupo-input">
					<input type="text" class="form-control" name="contraseniaOperario" id="contraseniaOperario">
					<i class="formulario__validacion-estado fas fa-times-circle float-right"></i>
				</div>
				<p class="formulario__input-error">La contraseña tiene que ser de 4 a 12 dígitos.</p>
			</div>

			<!-- Grupo: Contraseña 2 -->
			<div class="formulario__grupo" id="grupo__contraseniaOperario2">
				<label for="contraseniaOperario2" class="formulario__label">Repetir Contraseña</label>
				<div class="formulario__grupo-input">
					<input type="text" class="form-control" name="contraseniaOperario2" id="contraseniaOperario2">
					<i class="formulario__validacion-estado fas fa-times-circle float-right"></i>
				</div>
				<p class="formulario__input-error">Ambas contraseñas deben ser iguales.</p>
			</div>
      

  

  <strong  class="" >Tipo de Operario: </strong>

  <div style="margin-top: 10px" class="custom-control custom-radio" >
     <input type="radio" class="custom-control-input" id="tipoOperario1" name="tipoOperario" value="Externo" required>
     <label for="tipoOperario1" class="custom-control-label" value="Externo">Externo</label>
   </div>

   <div class="custom-control custom-radio">
     <input type="radio" class="custom-control-input" id="tipoOperario2" name="tipoOperario" value="Interno" required>
     <label for="tipoOperario2" class="custom-control-label" value="Interno">Interno</label>
   </div>

  
  <button style="margin-top: 20px" type="submit" class="btn btn-primary ">Enviar</button>
  
  <a href="{{ url()->previous() }}">
  <button style="margin-top: 20px" type="button" class="btn btn-secondary float-right">Cancelar</button>
</a>
        </div>
     </div>
</form>

        </div>
    </div>
</div>

<script src="js/formulario.js"></script>
	<script src="https://kit.fontawesome.com/2c36e9b7b1.js" crossorigin="anonymous"></script>

<script type="text/javascript">
$('#empresaselect').select2();
</script>


@endsection