@extends('layouts.gestionOperariosL.sidebargestionopedit')

@section('contentedit')

<div class="container">
    <div class="row">
        <div class="col-sm-6">
            <h3>Editar Operario: {{ $operario->nombre }}</h3>
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
    <input type="text" class="form-control" name="nombre" value="{{ $operario->nombre }}" placeholder="Escriba nombre operario">
  </div>

  <form>
  <div class="form-group">
    <label for="rut">Rut</label>
    <input type="text" class="form-control" name="rut" value="{{ $operario->rut }}" placeholder="Escriba rut del operario">
  </div>

  <form>
  <div class="form-group">
    <label for="correo">Correo</label>
    <input type="email" class="form-control" name="correo" value="{{ $operario->correo }}" placeholder="Escriba correo del operario">
  </div>

  <form>
  <div class="form-group">
    <label for="empresa">Empresa</label>
    <input type="text" class="form-control" name="empresa" value="{{ $operario->empresa }}" placeholder="Escriba empresa del operario">
  </div>
  
  
<form>
  <div class="form-group">
    <label for="tipoOperario">Tipo De Operario</label>
    <input type="text" class="form-control" name="tipoOperario" value="{{ $operario->tipoOperario }}" placeholder="Escriba el tipo de operario">
  </div>
  
  <button type="submit"  class="btn btn-primary">Editar</button>
        </div>
     </div>
</form>

        </div>
    </div>
</div>
@endsection