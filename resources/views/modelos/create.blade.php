@extends('layouts.sidebar')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-sm-6">

<form action="modelosop1" method="POST">
    @csrf
  <div class="form-group">
    <label for="nombre">Nombre</label>
    <input type="text" class="form-control" name="nombre" placeholder="Escriba el nombre del Modelo" required>
  </div>

  <form>
  <div class="form-group">
    <label for="idModelo">Id Modelo</label>
    <input type="text" class="form-control" name="idModelo" placeholder="Escriba el id del Modelo" required>
  </div>

  

  
  <button style="margin-top: 20px" type="submit" class="btn btn-primary ">Submit</button>
  
  <a href="{{ route('modelosop') }}">
  <button style="margin-top: 20px" type="button" class="btn btn-secondary float-right">Cancelar</button>
</a>
        </div>
     </div>
</form>
        </div>
    </div>
</div>
@endsection