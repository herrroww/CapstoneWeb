@extends('layouts.sidebar')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-sm-6">
            <h3>Revisar Reporte: {{ $reporteproblema->tituloReporteProblema }}</h3>
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
    <form>
<div class="form-group">
<div class="form-control">
    <label for="rutOperario">Rut operario: {{ $reporteproblema->rutOperario}}</label>
  </div>

  <div class="form-control">
    <label for="nombreOperario">Nombre Operario: {{ $reporteproblema->nombreOperario}}</label>
  </div>

  <div class="form-control">
    <label for="nombreOperario">Correo Operario: {{ $reporteproblema->correoOperario}}</label>
  </div>

  <div class="form-control">
    <label for="numeroOperario">Numero Operario: {{ $reporteproblema->numeroOperario}}</label>
  </div>

  <div class="form-control">
    <label for="prioridad">Prioridad: {{ $reporteproblema->prioridad}}</label>
  </div>

  <div class="form-control">
    <label for="fechaReporteProblema">Fecha Reporte: {{ $reporteproblema->fechaReporteProblema}}</label>
  </div>

  <div class="form-control">
    <label for="tituloReporteProblema">Titulo De Reporte: {{ $reporteproblema->tituloReporteProblema}}</label>
  </div>

  <div class="form-control">
    <label for="contenidoReporteProblema">Contenido De Reporte: {{ $reporteproblema->contenidoReporteProblema}}</label>
  </div>




<strong >Estado: </strong>

<div style="margin-top: 10px" class="custom-control custom-radio" >
   <input type="radio" class="custom-control-input" id="estado1" name="estado" value="Sin Revisar" required>
   <label for="estado1" class="custom-control-label" value="Sin Revisar">Sin Revisar</label>
 </div>

 <div class="custom-control custom-radio">
   <input type="radio" class="custom-control-input" id="estado2" name="estado" value="Pendiente" required>
   <label for="estado2" class="custom-control-label" value="Pendiente">Pendiente</label>
 </div>

 <div class="custom-control custom-radio">
   <input type="radio" class="custom-control-input" id="estado3" name="estado" value="Finalizado" required>
   <label for="estado3" class="custom-control-label" value="Finalizado">Finalizado</label>
 </div>
  
<form>
  
  <button style="margin-top: 20px" type="submit"  class="btn btn-primary">Aplicar</button>

  <a href="{{ route('reporteop') }}">
  <button style="margin-top: 20px" type="button" class="btn btn-secondary float-right">Cancelar</button>
 </a>

        </div>
     </div>
</form>

        </div>
    </div>
</div>
@endsection