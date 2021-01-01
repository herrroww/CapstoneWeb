@extends('layouts.sidebar')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-sm-6">

<form action="documentosop1" method="POST" enctype="multipart/form-data">
    @csrf
  <div class="form-group">
    <label for="nombre">Nombre</label>
    <input type="text" class="form-control" name="nombre" placeholder="Escriba el nombre del Documento" required>
  </div>

  <form>
  <div class="form-group">
    <label for="descripcion">Descripcion</label>
    <input type="text" class="form-control" name="descripcion" placeholder="Escriba la descripción del documento" required>
  </div>

  <form>
  <div class="form-group">
    <label for="file">File</label>
    <input type="file"  name="file"  required>
  </div>

 
  

  
  <button style="margin-top: 20px" type="submit" class="btn btn-primary ">Submit</button>
  
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