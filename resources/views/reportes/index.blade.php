@extends('layouts.sidebar')

@section('content')
<div class="container-fluid">
    <div class="col-12 pt-3 pb-3 text-center" >
        <h2>Reporte De Problemas</h2>
    </div>
    <hr> 

<div class="container-fluid">

<h6>
    @if($search)
  <div class="alert alert-primary" role="alert">
  Se encontraron los siguientes resultados:
  </div>
    @endif

    @if(session('success'))
  <div class="alert alert-danger" role="alert">
  Reporte Eliminado correctamente.
  </div>
    @endif

    @if(session('edit'))
  <div class="alert alert-warning" role="alert">
  Se a Modificado correctamente.
  </div>
    @endif

    
  </h6>
  <!-- SEARCH FORM -->
  <form class="form-inline ml-3 float-right">
                    <div class="input-group input-group-sm" style="margin-bottom:10px">
                        <input class="form-control form-control-navbar" name="search" type="search" placeholder="Search"
                            aria-label="Search">
                        <div class="input-group-append">
                        <button class="btn bg-orange color-white" type="submit"><i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                </form>
 
 
<table class="table table-bordered">
  <thead>
    <tr>
	  <!--<th scope="col">Id</th>-->
     <th scope="col" class="bg-blue color-white">Id</th>
     <th scope="col" class="bg-blue color-white">Titulo Reporte</th>
      <th scope="col" class="bg-blue color-white">Estado</th>
      <th scope="col" class="bg-blue color-white">Fecha Reporte</th>  
      <th scope="col" class="bg-blue color-white">Revisar Reporte</th> 
    </tr>
  </thead>
  <tbody>
  @foreach($reporteproblemas as $reporteproblema)
    <tr>
     
    <th scope="row">{{$reporteproblema->id}}</th>
    <td>{{$reporteproblema->tituloReporteProblema}}</td>
    @if($reporteproblema->estado==null)
    <td>Sin Revisar</td>
    @else
      <td>{{$reporteproblema->estado}}</td>
      @endif
      <td>{{$reporteproblema->fechaReporteProblema}}</td>
      
    
    
    
    @csrf
    <td>
    <a href="{{ route('reporteopedit', $reporteproblema->id) }}"><button name ="revisar" type="submit" class="btn btn-primary" >Revisar</button></a>
    </form>
    </td>
    </tr>
	@endforeach
  </tbody>
  @if($search)
  <a href="{{ url()->previous() }}">
  <div style="position: absolute; left: 90%; bottom: 10%;">
  <button type="button" class="btn btn-secondary">Back</button>
 </div>
</a>
 @endif
</table>
    {{ $reporteproblemas->links()}}
</div>
@endsection