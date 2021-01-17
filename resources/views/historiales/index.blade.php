@extends('layouts.sidebar')

@section('content')
<div class="container-fluid">
    <div class="col-12 pt-3 pb-3 text-center" >
        <h2>Historial de Gestión</h2>
    
    <hr>  
    
<div class="container-fluid">

@if($search)
  <div class="alert alert-primary" role="alert">
  Se encontraron los siguientes resultados:
  </div>
    @endif

  <!-- SEARCH FORM -->
  <nav class="navbar navbar-light float-right">           
                <form method="GET" action="{{route('historialop')}}" class="form-inline">
                    @csrf
                    <input name="search" class="form-control mr-sm-2" type="search" placeholder="Buscar por nombre" aria-label="Search">
                    <button class="btn bg-orange color-white my-2 my-sm-0" type="submit"><i class="fa fa-search mr-1" aria-hidden="true"></i>Buscar</button>
                </form>
            </nav>
  <!-- SEARCH FORM -->
  
    
<div class="col-12 pt-3 pb-3 table-responsive">
<div class="container-fluid">
        <table class="table table-bordered" >
          <thead >
            <tr>
             <th scope="col" class="bg-blue color-white">ID</th>
              <th scope="col" class="bg-blue color-white">Gestión</th>
              <th scope="col" class="bg-blue color-white">Acción</th>
              <th scope="col" class="bg-blue color-white">Usuario</th>
              <th scope="col" class="bg-blue color-white">Fecha</th>
              <th scope="col" class="bg-blue color-white">Ver Cambios</th>
            </tr>
          </thead>
          <tbody id="historicogestion">
            @foreach($historicogestion as $historicoaux)
              <tr>
              <td>{{$historicoaux->id}}</td>
              <td>{{$historicoaux->nombreGestion}}</td>
              <td>{{$historicoaux->tipoGestion}}</td>
              <td>{{$historicoaux->responsableGestion}}</td>
              <td>{{$historicoaux->created_at}}</td>     
    
    <form action="" method="">
    <td>
    <a href="{{ route('historialopshow', $historicoaux->id) }}"><button type="button" class="btn bg-orange color-white"><i class="far fa-eye"></i></button></a> 
    </form>
    </td>
    </tr>      
    @endforeach
    </tbody>

        
    
        </table>

        @if($search)
  <a href="{{route('historialop')}}">
  <button type="button" class="btn btn-secondary float-right">Volver</button>
 </div>
</a>
@endif
  
    
    {{ $historicogestion->appends('search',$search)->links() }}
    

      </div>
    @endsection