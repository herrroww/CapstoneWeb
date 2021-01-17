@extends('layouts.sidebar')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-sm-6">
            <h3>Subir Documento al Componente: {{ $componente->nombreComponente }} - {{ $componente->idComponente }}</h3>
            <hr>

<form action="documentosop1" method="POST" enctype="multipart/form-data">
    @csrf
  <div class="form-group">
    <label for="nombre">Nombre:</label>
    <input type="text" class="form-control" name="nombre" placeholder="Escriba el nombre del Documento" value="{{ old('nombre') }}" required>
    {!! $errors->first('nombre','<div class="alert alert-danger"><small>:message</small></div><br>') !!}
  </div>

  <form>
  <div class="form-group">
    <label for="descripcion">Descripción:</label>
    <input type="text" class="form-control" name="descripcion" placeholder="Escriba la descripción del documento" value="{{ old('descripcion') }}" required>
    {!! $errors->first('descripcion','<div class="alert alert-danger"><small>:message</small></div><br>') !!}
  </div>

  


  <form>
  <div class="form-group">
    <label for="file">Documento:</label>
    <input type="file"  name="file"  required>
  </div>

  <form>
  <strong  class="" >Tipo de Privacidad: </strong>

  <div style="margin-top: 10px" class="custom-control custom-radio" >
     <input type="radio" class="custom-control-input" id="privacidad1" name="privacidad" value="Publico" required>
     <label for="privacidad1" class="custom-control-label" value="Publico">Público</label>
   </div>

   <div class="custom-control custom-radio">
     <input type="radio" class="custom-control-input" id="privacidad2" name="privacidad" value="Privado" required>
     <label for="privacidad2" class="custom-control-label" value="Privado">Privado</label>
   </div>
  

  
  <button style="margin-top: 20px" type="submit" class="btn btn-primary ">Enviar</button>
  
  <a href="{{route('documentosop')}}">
  <button style="margin-top: 20px" type="button" class="btn btn-secondary float-right">Cancelar</button>
</a>
        </div>
     </div>
</form>
        </div>
    </div>
</div>
@endsection