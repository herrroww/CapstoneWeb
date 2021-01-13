@extends('layouts.sidebar')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-6">
            <h3>Editar Operario: {{ $operario->nombreOperario }}</h3>
            <hr>

<form action="" method="POST">
    @method('PATCH')
    @csrf
  <div class="form-group">
    <label for="nombreOperario">Nombre:</label>
    <input type="text" class="form-control" name="nombreOperario" value="{{ $operario->nombreOperario }}" placeholder="Escriba nombre operario" required>
    {!! $errors->first('nombreOperario','<div class="alert alert-danger"><small>:message</small></div><br>') !!}
  </div>

  <form>
  <div class="form-group">
  <label style="margin-top: 20px" for="rutOperario">Rut: </label><div style="margin-right: 100px" class="alert alert-info float-right" role="alert">
  Colocar rut con puntos y guión ejemplo: 11.111.111-1</div>
    <input type="text" class="form-control" name="rutOperario" value="{{ $operario->rutOperario }}" placeholder="Escriba rut del operario" required>
    {!! $errors->first('rutOperario','<div class="alert alert-danger"><small>:message</small></div><br>') !!}
  </div>

  <form>
  <div class="form-group">
    <label for="correoOperario">Correo:</label>
    <input type="email" class="form-control" name="correoOperario" value="{{ $operario->correoOperario }}" placeholder="Escriba correo del operario" required>
    {!! $errors->first('correoOperario','<div class="alert alert-danger"><small>:message</small></div><br>') !!}
  </div>

  <div class="form-group">
    <label for="telefonoOperario">Teléfono Operario:</label>
    <input type="text" class="form-control" name="telefonoOperario" value="{{ $operario->telefonoOperario}}" placeholder="Escriba el teléfono del operario" required>
    {!! $errors->first('telefonoOperario','<br><div class="alert alert-danger"><small>:message</small></div><br>') !!}
  </div>

<div class="form-group">
  <strong style="margin-bot: 10px">Empresa:</strong>
 
  <select name="empresa" class="form-control" id="empresaselect">
    @if($operario->empresa == null)
      <option value="">No hay empresa seleccionada</option>
    @else
      <option value="{{ $operario->empresa_id }}" required>{{ $operario->empresa->nombreEmpresa}} | Rut:{{ $operario->empresa->rutEmpresa}}</option>
    @endif
 
    @foreach($empresa as $empresas)
      @if($empresas->id != $operario->empresa_id)
        <option value="{{ $empresas->id }}">{{ $operario->empresa->nombreEmpresa}} | Rut:{{ $operario->empresa->rutEmpresa}}</option>
      @endif
    @endforeach
  </select>
  {!! $errors->first('empresa','<div class="alert alert-danger"><small>:message</small></div><br>') !!}
</div>

<div class="form-group">
    <label for="contraseniaOperario">Nueva Contraseña Operario:</label>
    <input type="text" class="form-control" name="contraseniaOperario" value="" placeholder="Escriba la contraseña">
    {!! $errors->first('contraseniaOperario','<div class="alert alert-danger"><small>:message</small></div><br>') !!}
  </div>

  <div class="form-group">
    <label for="contraseniaOperario2">Confirme Nueva Contraseña Operario:</label>
    <input type="text" class="form-control" name="contraseniaOperario2" value="" placeholder="Escriba la contraseña">
    {!! $errors->first('contraseniaOperario','<div class="alert alert-danger"><small>:message</small></div><br>') !!}
  </div>

<form>
<div class="form-group">
<strong >Tipo de Operario: </strong>

<div style="margin-top: 10px" class="custom-control custom-radio" >
   <input type="radio" class="custom-control-input" id="tipoOperario1" name="tipoOperario" value="Externo" required>
   <label for="tipoOperario1" class="custom-control-label" value="Externo">Externo</label>
 </div>

 <div class="custom-control custom-radio">
   <input type="radio" class="custom-control-input" id="tipoOperario2" name="tipoOperario" value="Interno" required>
   <label for="tipoOperario2" class="custom-control-label" value="Interno">Interno</label>
 </div>

 
  
  <button style="margin-top: 20px" type="submit"  class="btn btn-primary">Editar</button>

  <a href="{{ url()->previous() }}">
  <button style="margin-top: 20px" type="button" class="btn btn-secondary float-right">Cancelar</button>
 </a>

        </div>
     </div>
</form>
<script type="text/javascript">
$('#empresaselect').select2();
</script>

        </div>
    </div>
</div>
@endsection