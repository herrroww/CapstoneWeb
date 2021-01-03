@extends('layouts.sidebar')

@section('content')
<div class="container-fluid">
    <div class="col-12 pt-3 pb-3 text-center" >
        <h2>Historial de Gestión</h2>
    </div>
    <hr>  
    
<div class="container-fluid">

@if($search)
  <div class="alert alert-primary" role="alert">
  Se encontraron los siguientes resultados:
  </div>
    @endif

  <!-- SEARCH FORM -->
  <form class="form-inline ml-3 float-right">
                    <div class="input-group input-group-sm " style="margin-bottom:10px">
                        <input class="form-control form-control-navbar" name="search" type="search" placeholder="Búsqueda"
                            aria-label="Search">
                        <div class="input-group-append">
                        <button class="btn bg-orange color-white" type="submit"><i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                </form>

<div class="container-fluid">
        <table class="table table-bordered" >
          <thead >
            <tr>
             <th scope="col" class="bg-blue color-white">ID</th>
              <th scope="col" class="bg-blue color-white">Modelo</th>
              <th scope="col" class="bg-blue color-white">Acción</th>
              <th scope="col" class="bg-blue color-white">Usuario</th>
              <th scope="col" class="bg-blue color-white">Fecha</th>
              <th scope="col" class="bg-blue color-white">Cambios</th>
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
    <a href="{{ route('historialopshow', $audit->id) }}"><button type="button" class="btn bg-orange color-white">Ver Cambios</button></a>
    </form>
    </td>
    </tr>      
    @endforeach
    </tbody>

        
    @if($search)
  <a href="{{ url()->previous() }}">
  <div style="position: absolute; left: 90%; bottom: 10%;">
  <button type="button" class="btn btn-secondary">Volver</button>
 </div>
</a>
 @endif
        </table>
        {{ $audits->links()}}

      </div>
    @endsection