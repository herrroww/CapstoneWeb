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
<div class="table-responsive">
    <table class="table col-sm-12 table-bordered table-striped table-condensed cf">
  <thead>
    <tr>
	  <!--<th scope="col">Id</th>-->
     <th scope="col" class="bg-blue color-white">Rut Operario</th>
     <th scope="col" class="bg-blue color-white">Nombre Operario</th>
      <th scope="col" class="bg-blue color-white">Correo Operario</th>
      <th scope="col" class="bg-blue color-white">Numero Operario</th>  
      <th scope="col" class="bg-blue color-white">Prioridad</th>
      <th scope="col" class="bg-blue color-white">Fecha Reporte</th>
      <th scope="col" class="bg-blue color-white">Titulo Reporte</th>  
      <th scope="col" class="bg-blue color-white">Contenido Reporte</th>  
    </tr>
  </thead>
  <tbody>
 
    <tr>   
    <th scope="row">{{$reporteproblema->rutOperario}}</th>
    <td>{{$reporteproblema->nombreOperario}}</td>
      <td>{{$reporteproblema->correoOperario}}</td>
      <td>{{$reporteproblema->numeroOperario}}</td>
      <td>{{$reporteproblema->prioridad}}</td>
      <td>{{$reporteproblema->fechaReporteProblema}}</td>
      <td>{{$reporteproblema->tituloReporteProblema}}</td>
      <td>{{$reporteproblema->contenidoReporteProblema}}</td>
</tr>

      <td>
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
  </td>


  </table>
  </div>

<form action="" method="">
    
    <button style="margin-top: 20px" type="submit"  class="btn btn-primary">Aplicar</button>
    </form>
    </td>
    </tr>
	
  </tbody>
  <a href="{{ url()->previous() }}">
  <div style="position: absolute; margin-top: 20px;">
  <button type="button" class="btn btn-secondary">Cancelar</button>
 </div>
</a>

</div>
@endsection
