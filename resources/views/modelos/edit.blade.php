@extends('layouts.sidebar')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-sm-6">
            <h3>Editar Modelo: {{ $modelo->nombre }}</h3>
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
    <input type="text" class="form-control" name="nombre" value="{{ $modelo->nombre }}" placeholder="Escriba nombre del modelo" required>
  </div>

  <form>
  <div class="form-group">
    <label for="idModelo">idModelo</label>
    <input type="text" class="form-control" name="idModelo" value="{{ $modelo->idModelo }}" placeholder="Escriba el id del modelo" required>
  </div>

  
  
  
  <button style="margin-top: 20px" type="submit"  class="btn btn-primary">Editar</button>

  <a href="{{ route('modelosop') }}">
  <button style="margin-top: 20px" type="button" class="btn btn-secondary float-right">Cancelar</button>
 </a>

        </div>
     </div>
</form>

        </div>
    </div>
</div>
@endsection