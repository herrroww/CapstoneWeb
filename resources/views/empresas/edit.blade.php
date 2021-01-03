@extends('layouts.sidebar')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-sm-6">
            <h3>Editar Empresa: {{ $empresa->nombre }}</h3>
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
    <label for="nombreEmpresa">Nombre:</label>
    <input type="text" class="form-control" name="nombreEmpresa" value="{{ $empresa->nombreEmpresa }}" placeholder="Escriba nombre de la empresa" required>
  </div>

  <form>
  <div class="form-group">
  <label style="margin-top: 20px" for="rutEmpresa">Rut: </label><div style="margin-right: 100px" class="alert alert-info float-right" role="alert">
  Colocar rut con puntos y guión ejemplo: 11.111.111-1</div>
    <input type="text" class="form-control" name="rutEmpresa" value="{{ $empresa->rutEmpresa }}" placeholder="Escriba rut de la empresa" required>
  </div>

  <form>
  <div class="form-group">
    <label for="compania">Compañía:</label>
    <input type="text" class="form-control" name="compania" value="{{ $empresa->compania }}" placeholder="Escriba la compañía" required>
  </div>

  
  
  
<form>
  
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