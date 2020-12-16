@extends('layouts.sidebar')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-sm-6">

<form action="empresaop1" method="POST">
    @csrf
  <div class="form-group">
    <label for="nombre">Nombre</label>
    <input type="text" class="form-control" name="nombre" placeholder="Escriba nombre empresa" required>
  </div>

  <form>
  <div class="form-group">
  <label style="margin-top: 20px" for="rut">Rut: </label><div style="margin-right: 100px" class="alert alert-info float-right" role="alert">
  Colocar rut con puntos y guion ejemplo: 11.111.111-1</div>
    <input type="text" class="form-control" name="rut" placeholder="Escriba rut de empresa" required>
  </div>

  <form>
  <div class="form-group">
    <label for="compania">Compañia</label>
    <input type="text" class="form-control" name="compania" placeholder="Escriba la compañia" required>
  </div>

  

  
  <button style="margin-top: 20px" type="submit" class="btn btn-primary ">Submit</button>
  
  <a href="{{ route('empresaop') }}">
  <button style="margin-top: 20px" type="button" class="btn btn-secondary float-right">Cancelar</button>
</a>
        </div>
     </div>
</form>
        </div>
    </div>
</div>
@endsection