@extends('layouts.sidebar')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-sm-6">
            <h3>Agregar Componente</h3>
            <hr>



<form action="componenteop1" method="POST">
    @csrf
  <div class="form-group ">
    <label for="nombreComponente">Nombre:</label>
    <input type="text" class="form-control" name="nombreComponente" placeholder="Escriba el nombre del componente" value="{{ old('nombreComponente') }}" required>
    {!! $errors->first('nombreComponente','<div class="alert alert-danger"><small>:message</small></div><br>') !!}
  </div>

  <form>
  <div class="form-group ">
    <label for="idComponente">ID Componente:</label>
    <input type="text" class="form-control" name="idComponente" placeholder="Escriba el id del componente" value="{{ old('idComponente') }}" required>
    {!! $errors->first('idComponente','<div class="alert alert-danger"><small>:message</small></div><br>') !!}
  </div>

  

  
  <button style="margin-top: 20px" type="submit" class="btn btn-primary ">Enviar</button>
  
  <a href="{{route('componenteop')}}">
  <button style="margin-top: 20px" type="button" class="btn btn-secondary float-right">Cancelar</button>
</a>
        </div>
     </div>
</form>
        </div>
    </div>
</div>
@endsection