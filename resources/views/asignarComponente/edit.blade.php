@extends('layouts.sidebar')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-sm-6">
            <h3>Editar</h3>
            <hr>

<form action="" method="POST">
    @method('PATCH')
    @csrf

  <div class="form-group">
 <strong style="margin-bot: 10px">Operario:</strong>
 <select name="operario" class="form-control" id="operariosselect">

 
 <option value="{{ $asignar->operario_id }}" required>{{ $asignar->operario->nombreOperario}}</option>
  @foreach($operario as $operarios)
    @if($operarios->id != $asignar->operario_id)
        <option value="{{ $operarios->id }}">{{ $operarios->nombreOperario }}</option>
    @endif
  @endforeach
  </select>
  </div>
  
  <div class="form-group">
 <strong style="margin-bot: 10px">Componente:</strong>
 <select name="componente" class="form-control" id="componenteselect">


 <option value="{{ $asignar->componente_id }}" required>{{ $asignar->componente->nombreComponente}}</option>
 @foreach($componente as $componente)
@if($componente->id != $asignar->componente_id)
  <option value="{{ $componente->id }}">{{ $componente->nombreComponente }}</option>
  @endif
  @endforeach
  </select>
  </div>

  
 
  
  <button style="margin-top: 20px" type="submit"  class="btn btn-primary">Editar</button>

  <a href="{{route('asignarop')}}">
  <button style="margin-top: 20px" type="button" class="btn btn-secondary float-right">Cancelar</button>
 </a>

        </div>
     </div>
</form>
<script type="text/javascript">
$('#operariosselect').select2();
</script>

<script type="text/javascript">
$('#componenteselect').select2();
</script>

        </div>
    </div>
</div>
@endsection