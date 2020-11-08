@extends('layouts.gestionOperariosL.sidebargestionopcreate')

@section('content2')

<div class="container">
    <div class="row">
        <div class="col-sm-6">

<form action="gestionop1" method="POST">
    @csrf
  <div class="form-group">
    <label for="nombre">Nombre</label>
    <input type="text" class="form-control" name="nombre" placeholder="Escriba nombre operario">
  </div>

  <form>
  <div class="form-group">
    <label for="rut">Rut</label>
    <input type="text" class="form-control" name="rut" placeholder="Escriba rut del operario">
  </div>

  <form>
  <div class="form-group">
    <label for="correo">Correo</label>
    <input type="email" class="form-control" name="correo" placeholder="Escriba correo del operario">
  </div>

  <form>
  <div class="form-group">
    <label for="empresa">Empresa</label>
    <input type="text" class="form-control" name="empresa" placeholder="Escriba empresa del operario">
  </div>

  <form>
  <strong  class="" >Tipo de Operario </strong>

  <div style="margin-top: 10px" class="custom-control custom-radio" >
     <input type="radio" class="custom-control-input" id="tipoOperario1" name="tipoOperario" value="Interno" required>
     <label for="tipoOperario1" class="custom-control-label" value="Interno">Externo</label>
   </div>

   <div class="custom-control custom-radio">
     <input type="radio" class="custom-control-input" id="tipoOperario2" name="tipoOperario" value="Externo" required>
     <label for="tipoOperario2" class="custom-control-label" value="Externo">Interno</label>
   </div>

  
  <button style="margin-top: 20px" type="submit" class="btn btn-primary ">Submit</button>
        </div>
     </div>
</form>
        </div>
    </div>
</div>
@endsection