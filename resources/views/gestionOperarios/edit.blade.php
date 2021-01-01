@extends('layouts.sidebar')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-6">
            <h3>Editar Operario: {{ $operario->nombre }}</h3>
            <hr>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
                 </ul>
                    </div>
                    @endif

<form action="" method="POST">
    @method('PATCH')
    @csrf
  <div class="form-group">
    <label for="nombre">Nombre</label>
    <input type="text" class="form-control" name="nombre" value="{{ $operario->nombre }}" placeholder="Escriba nombre operario" required>
  </div>

  <form>
  <div class="form-group">
  <label style="margin-top: 20px" for="rut">Rut: </label><div style="margin-right: 100px" class="alert alert-info float-right" role="alert">
  Colocar rut con puntos y guion ejemplo: 11.111.111-1</div>
    <input type="text" class="form-control" name="rut" value="{{ $operario->rut }}" placeholder="Escriba rut del operario" required>
  </div>

  <form>
  <div class="form-group">
    <label for="correo">Correo</label>
    <input type="email" class="form-control" name="correo" value="{{ $operario->correo }}" placeholder="Escriba correo del operario" required>
  </div>

  <div class="form-group">
 <strong style="margin-bot: 10px">Empresa:</strong>
 <select name="empresa" class="form-control">

 @if($operario->empresa == null)
 <option value="">No hay empresa seleccionada</option>
 @else
 <option value="{{ $operario->empresa_id }}" required>{{ $operario->empresa->nombre}}</option>
 @endif
 
 @foreach($empresa as $empresas)
@if($empresas->id != $operario->empresa_id)
  <option value="{{ $empresas->id }}">{{ $empresas->nombre }}</option>
  @endif
  @endforeach
  </select>
  </div>
  
  <div class="form-group">
    <label for="telefonoOperario">Telefono Operario:</label>
    <input type="text" class="form-control" name="telefonoOperario" value="{{ $operario->telefonoOperario}}" placeholder="Escriba el telefono del operario" required>
  </div>

  
<div class="form-group">
    <label for="contraseniaOperario">Contraseñia Operario:</label>
    <input type="text" class="form-control" name="contraseniaOperario" value="{{ $operario->contraseniaOperario}}" placeholder="Escriba la contraseña" required>
  </div>

<form>
<div class="form-group">
<strong >Tipo de Operario </strong>

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

        </div>
    </div>
</div>
@endsection