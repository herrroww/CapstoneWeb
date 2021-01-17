@extends('layouts.sidebar')

@section('content')
<div class="container-fluid">
    <div class="col-12 pt-3 pb-3 text-center" >
        <h2>Reporte De Problemas</h2>
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
  <nav class="navbar navbar-light float-right">           
                <form method="GET" action="{{route('reporteop')}}" class="form-inline">
                    @csrf
                    <input name="search" class="form-control mr-sm-2" type="search" placeholder="Buscar por estado" aria-label="Search">
                    <button class="btn bg-orange color-white my-2 my-sm-0" type="submit"><i class="fa fa-search mr-1" aria-hidden="true"></i>Buscar</button>
                </form>
            </nav>
  <!-- SEARCH FORM -->
   
<div class="col-12 pt-3 pb-3 table-responsive">
 
<table class="table table-bordered">
  <thead>
    <tr>
	  <!--<th scope="col">Id</th>-->
     <th scope="col" class="bg-blue color-white">ID</th>
     <th scope="col" class="bg-blue color-white">Título Reporte</th>
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
    <a href="{{ route('reporteopedit', $reporteproblema->id) }}"><button name ="revisar" type="submit" class="btn btn-primary" ><i class="fas fa-clipboard-check"></i></button></a>
    </form>
    </td>
    </tr>
	@endforeach
  </tbody>
  
</table>
@if($search)
  <a href="{{ route('reporteop') }}">
  <button type="button" class="btn btn-secondary float-right">Back</button>
</a>
 @endif
    {{ $reporteproblemas->appends('search',$search)->links()}}
</div>
@endsection