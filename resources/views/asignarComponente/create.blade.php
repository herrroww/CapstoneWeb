
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
    <select name="operario" class="form-control">
    <strong style="margin-bot: 10px">Operario:</strong>
@foreach($operario as $operarios)
@empty($operario)
 <option value="">No hay operario seleccionada</option>
 @endempty

 

<option value="{{ $operarios->id  }}" required>{{ $operarios->nombre }} | Operario {{ $operarios->tipoOperario }} | Empresa: {{ $operarios->empresa->nombre }} |</oprion>


@endforeach

</select>


</div>
<div class="form-group">
<strong style="margin-bot: 10px">Componente:</strong>
<select name="componente" class="form-control">
<strong style="margin-bot: 10px">Componente:</strong>
@foreach($componente as $componentes)
@empty($componente)
 <option value="">No hay componente seleccionada</option>
 @endempty
<option value="{{ $componentes->id }}" required>{{ $componentes->nombre }}</oprion>

@endforeach

</select>
  </div>
  

  
  
  <button style="margin-top: 20px" type="submit" class="btn btn-primary ">Submit</button>
  
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