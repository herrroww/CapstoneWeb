@extends('layouts.sidebar')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-sm-6">
            <h3>Agregar Empresa</h3>
            <hr>

<form action="empresaop1" method="POST">
    @csrf
  <div class="form-group">
    <label for="nombreEmpresa">Nombre:</label>
    <input type="text" class="form-control" name="nombreEmpresa" placeholder="Escriba nombre empresa" value="{{ old('nombreEmpresa') }}" required>
    {!! $errors->first('nombreEmpresa','<div class="alert alert-danger"><small>:message</small></div><br>') !!}
  </div>

  <form>
  <div class="form-group">
  <label style="margin-top: 20px" for="rutEmpresa">Rut: </label><div style="margin-right: 100px" class="alert alert-info float-right" role="alert">
  Colocar rut con puntos y guión ejemplo: 11.111.111-1</div>
    <input type="text" class="form-control" name="rutEmpresa" placeholder="Escriba rut de empresa" value="{{ old('nombreEmpresa') }}" required>
    {!! $errors->first('rutEmpresa','<div class="alert alert-danger"><small>:message</small></div><br>') !!}
  </div>

  <form>
  <div class="form-group">
    <label for="compania">Compañía:</label>
    <input type="text" class="form-control" name="compania" placeholder="Escriba la compañía" value="{{ old('compania') }}" required>
    {!! $errors->first('compania','<div class="alert alert-danger"><small>:message</small></div><br>') !!}
  </div>
  
  <button style="margin-top: 20px" type="submit" class="btn btn-primary ">Enviar</button>
  
  <a href="{{route('empresaop')}}">
  <button style="margin-top: 20px" type="button" class="btn btn-secondary float-right">Cancelar</button>
</a>
        </div>
     </div>
</form>
        </div>
    </div>
</div>
@endsection