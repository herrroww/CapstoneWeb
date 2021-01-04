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
 
 <select name="empresa" class="form-control">
@foreach($empresa as $empresas)
@empty($empresa)
 <option value="">No hay empresa seleccionada</option>
 @endempty
<option value="{{ $empresas->id }}" required>{{ $empresas->nombreEmpresa }}</oprion>

@endforeach

</select>
</div>

  <div class="form-group">
    <label for="telefonoOperario">Teléfono Operario:</label>
    <input type="text" class="form-control" name="telefonoOperario" placeholder="Escriba el teléfono del operario" required>
  </div>

  
<div class="form-group">
    <label for="contraseniaOperario">Contraseña Operario:</label>
    <input type="text" class="form-control" name="contraseniaOperario" placeholder="Escriba la contraseña" required>
  </div>

  <div class="form-group">
    <label for="contraseniaOperario2">Contraseña Operario2:</label>
    <input type="text" class="form-control" name="contraseniaOperario2" value="" placeholder="Escriba la contraseña" required>
  </div>

  


  
  <form>
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
@endsection