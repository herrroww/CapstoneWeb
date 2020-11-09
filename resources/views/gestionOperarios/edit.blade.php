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
    <input type="text" class="form-control" name="nombre" value="{{ $operario->nombre }}" placeholder="Escriba nombre operario" required>
  </div>

  <form>
  <div class="form-group">
    <label for="rut">Rut</label>
    <input type="text" class="form-control" name="rut" value="{{ $operario->rut }}" placeholder="Escriba rut del operario" required>
  </div>

  <form>
  <div class="form-group">
    <label for="correo">Correo</label>
    <input type="email" class="form-control" name="correo" value="{{ $operario->correo }}" placeholder="Escriba correo del operario" required>
  </div>

  <form>
  <div class="form-group">
    <label for="empresa">Empresa</label>
    <input type="text" class="form-control" name="empresa" value="{{ $operario->empresa }}" placeholder="Escriba empresa del operario" required>
  </div>
  
  
<form>
<strong  class="" >Tipo de Operario </strong>

<div style="margin-top: 10px" class="custom-control custom-radio" >
   <input type="radio" class="custom-control-input" id="tipoOperario1" name="tipoOperario" value="Interno" required>
   <label for="tipoOperario1" class="custom-control-label" value="Interno">Externo</label>
 </div>

 <div class="custom-control custom-radio">
   <input type="radio" class="custom-control-input" id="tipoOperario2" name="tipoOperario" value="Externo" required>
   <label for="tipoOperario2" class="custom-control-label" value="Externo">Interno</label>
 </div>
  
  <button style="margin-top: 20px" type="submit"  class="btn btn-primary">Editar</button>

  <button style="margin-top: 20px" type="reset" class="btn btn-secondary float-right">Cancelar</button>

        </div>
     </div>
</form>

        </div>
    </div>
</div>
@endsection