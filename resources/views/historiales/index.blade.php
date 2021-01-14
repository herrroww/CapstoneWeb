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
              <th scope="col" class="bg-blue color-white">Modelo</th>
              <th scope="col" class="bg-blue color-white">Acción</th>
              <th scope="col" class="bg-blue color-white">Usuario</th>
              <th scope="col" class="bg-blue color-white">Fecha</th>
              <th scope="col" class="bg-blue color-white">Ver Cambios</th>
            </tr>
          </thead>
          <tbody id="audits">
            @foreach($audits as $audit)
              <tr>
              <td>{{ $audit->id }}</td>
              @if ( $audit->auditable_type == "App\Operario")
                <td>Operario (ID: {{ $audit->auditable_id}})</td>
                
                @elseif ( $audit->auditable_type == "App\Empresa")
                <td>Empresa (ID: {{ $audit->auditable_id}})</td>
                @elseif ($audit->auditable_type == "App\Asignar")
                <td> Asignar Componente (ID: {{ $audit->auditable_id }})</td>
                @elseif ($audit->auditable_type == "App\Componente")
                <td> Componente (ID: {{ $audit->auditable_id}})</td>
                @else
                <td>{{ $audit->auditable_type }}</td>
                @endif
              
                @if ( $audit->event == "created"  )
                <td>Creado </td>
                
                @elseif ( $audit->event == "deleted")
                <td>Eliminado</td>
                @elseif ( $audit->event == "updated" )
                <td>Editado  </td>
                @else
                <td>{{ $audit->event }}</td>
                @endif
                
                
                <td>{{ $audit->user->name }}</td>
                <td>{{ $audit->created_at }}</td>
    
    
    <form action="" method="">
    <td> 
    <a href="{{ route('historialopshow', $audit->id) }}"><button type="button" class="btn bg-orange color-white"><i class="far fa-eye"></i></button></a>
    </form>
    </td>
    </tr>      
    @endforeach
    </tbody>

        
    
        </table>

        @if($search)
  <a href="{{ route('historialop') }}">
  <button type="button" class="btn btn-secondary float-right">Volver</button>
 </div>
</a>
 @endif

        {{ $audits->links()}}

      </div>
    @endsection