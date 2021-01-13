@extends('layouts.sidebar')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-6">
            <h3>Editar Componente: {{ $componente->nombreComponente }}</h3>
            <hr>


<form action="" method="POST">
    @method('PATCH')
    @csrf
  <div class="form-group">
    <label for="nombreComponente">Nombre:</label>
    <input type="text" class="form-control" name="nombreComponente" value="{{ $componente->nombreComponente }}" placeholder="Escriba nombre del componente" required>
    {!! $errors->first('nombreComponente','<div class="alert alert-danger"><small>:message</small></div><br>') !!}
  </div>

  <form>
  <div class="form-group">
    <label for="idComponente">ID Componente:</label>
    <input type="text" class="form-control" name="idComponente" value="{{ $componente->idComponente }}" placeholder="Escriba el id del componente" required>
    {!! $errors->first('idComponente','<div class="alert alert-danger"><small>:message</small></div><br>') !!}
  </div>

  
  
  
  <button style="margin-top: 20px" type="submit"  class="btn btn-primary">Editar</button>

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