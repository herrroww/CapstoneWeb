
@extends('layouts.sidebar')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-sm-6">
            <h3>Asignar Componente</h3>
            <hr>



<div class="form-group">
<form action="asignarop1" method="POST">
    @csrf
    <div class="form-group">
    
    <strong style="margin-bot: 10px">Operario:</strong>
    <select name="operario" class="form-control" id="operariosselect">
    <option value="" required>Seleccionar Operario</oprion>
@foreach($operario as $operarios)

<option value="{{ $operarios->id  }}" required>{{ $operarios->nombreOperario }} | Operario {{ $operarios->tipoOperario }} | Empresa: {{ $operarios->empresa->nombreEmpresa }} |</oprion>


@endforeach

</select>

<div class="form-group">
    
    <strong style="margin-bot: 10px">Operario:</strong>
    <select name="operario" class="form-control" id="operariosselect">
    <option value="" required>Seleccionar Tipo De Operario</oprion>
@foreach($operario as $operarios)

<option value="{{ $operarios->id  }}" required> {{ $operarios->tipoOperario }} </oprion>


@endforeach

</select>

<div class="form-group">
    
    <strong style="margin-bot: 10px">Operario:</strong>
    <select name="operario" class="form-control" id="operariosselect">
    <option value="" required>Seleccionar Empresa de Operario</oprion>
@foreach($operario as $operarios)

<option value="{{ $operarios->id  }}" required> {{ $operarios->empresa->nombreEmpresa }} </oprion>


@endforeach

</select>


</div>
<div class="form-group">
<strong style="margin-bot: 10px">Componente:</strong>
<select name="componente" class="form-control" id="componenteselect">
<option value="" required>Seleccionar Componente</oprion>
@foreach($componente as $componentes)
<option value="{{ $componentes->id }}" required>{{ $componentes->nombreComponente }}</oprion>

@endforeach

</select>
  </div>
  

  
  
  <button style="margin-top: 20px" type="submit" class="btn btn-primary ">Enviar</button>
  
  <a href="{{ url()->previous() }}">
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