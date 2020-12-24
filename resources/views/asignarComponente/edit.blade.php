@extends('layouts.sidebar')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-sm-6">
            <h3>Editar:</h3>
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
 <strong style="margin-bot: 10px">Operario:</strong>
 <select name="operario" class="form-control">

 @if($asignar->operario == null)
 <option value="">No hay operario seleccionada</option>
 @else
 <option value="{{ $asignar->operario_id }}" required>{{ $asignar->operario->nombre}}</option>
 @endif
 
 @foreach($operario as $operarios)
@if($operarios->id != $asignar->operario_id)
  <option value="{{ $operarios->id }}">{{ $operarios->nombre }}</option>
  @endif
  @endforeach
  </select>
  </div>
  
  <div class="form-group">
 <strong style="margin-bot: 10px">Componente:</strong>
 <select name="componente" class="form-control">

 @if($asignar->componente == null)
 <option value="">No hay componente seleccionada</option>
 @else
 <option value="{{ $asignar->componente_id }}" required>{{ $asignar->componente->nombre}}</option>
 @endif
 
 @foreach($componente as $componente)
@if($componente->id != $asignar->componente_id)
  <option value="{{ $componente->id }}">{{ $componente->nombre }}</option>
  @endif
  @endforeach
  </select>
  </div>

  
 
  
  <button style="margin-top: 20px" type="submit"  class="btn btn-primary">Editar</button>

  <a href="{{ route('asignarop') }}">
  <button style="margin-top: 20px" type="button" class="btn btn-secondary float-right">Cancelar</button>
 </a>

        </div>
     </div>
</form>

        </div>
    </div>
</div>
@endsection