@extends('layouts.sidebar')

@section('content')


<div class="container-fluid">
  <!-- SEARCH FORM -->
  <form class="form-inline ml-3 float-right">
                    <div class="input-group input-group-sm">
                        <input class="form-control form-control-navbar" name="search" type="search" placeholder="Search"
                            aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
  <h2>Reporte De Problemas</h2>
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


    
  </h6>
<table class="table table-hover">
  <thead>
    <tr>
	  <!--<th scope="col">Id</th>-->
     <th scope="col">Id</th>
     <th scope="col">Titulo Reporte</th>
      <th scope="col">Estado</th>
      <th scope="col">Fecha Reporte</th>  
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
  <a href="{{ route('reporteop') }}">
  <div style="position: absolute; left: 90%; bottom: 10%;">
  <button type="button" class="btn btn-secondary">Back</button>
 </div>
</a>
 @endif
</table>
    {{ $reporteproblemas->links()}}
</div>
@endsection