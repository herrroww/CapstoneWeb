@extends('layouts.sidebar')

@section('content')

<div class="container-fluid">
    <div class="col-12 pt-3 pb-3 text-center" >
        <h2>Asignar Componente</h2>
    </div>
    <hr>


<div class="container-fluid">

@if($search)
  <div class="alert alert-primary" role="alert">
  Se encontraron los siguientes resultados:
  </div>
    @endif

    @if(session('success'))
  <div class="alert alert-danger" role="alert">
  La asignación ha sido eliminada correctamente.
  </div>
    @endif

    @if(session('create'))
  <div class="alert alert-success" role="alert">
  Se asignó componente correctamente.
  </div>
    @endif

    
    @if(session('edit'))
  <div class="alert alert-warning" role="alert">
  La asignación se ha editado correctamente.
  </div>
    @endif

    

  <!-- SEARCH FORM -->
  <form class="form-inline ml-3 float-right">
                    <div class="input-group input-group-sm">
                        <input class="form-control form-control-navbar" name="search" type="search" placeholder="Búsqueda"
                            aria-label="Search">
                        <div class="input-group-append">
                        <button class="btn bg-orange color-white" type="submit"><i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                </form>

               

   <a href="{{ route('asignarop1') }}"> <button type="button" class="btn bg-orange color-white float-right" style="margin-bottom:10px" >Asignar Componente </button></a>
  <h6>
  
  </h6>
<table class="table table-bordered">
  <thead>
    <tr>
	  <th scope="col" class="bg-blue color-white">ID</th>
      <th scope="col" class="bg-blue color-white">Operario</th>
      <th scope="col" class="bg-blue color-white">Tipo de Operario</th>
      <th scope="col" class="bg-blue color-white">Componente</th>
      <th scope="col" class="bg-blue color-white">Opciones</th>
      
      
    </tr>
  </thead>
  <tbody>
  @foreach($asignars as $asignar)
    <tr>
      <th scope="row">{{$asignar->id}}</th>
        @if($asignar->operario == null)
    <td>operario no seleccionada</td>
    @else
    <td>{{$asignar->operario->nombreOperario}}</td>

    <td>{{$asignar->operario->tipoOperario}}</td>
    @endif

    @if($asignar->componente == null)
    <td>componente no seleccionada</td>
    @else
    <td>{{$asignar->componente->nombreComponente}}</td>
    @endif
    
    
    
    <form action="{{ route('asignaropdes', $asignar->id) }}" method="POST">
    @method('DELETE')
    @csrf
    <td><a href="{{ route('asignaropedit', $asignar->id) }}"><button type="button" class="btn btn-primary">Editar</button></a>
    <button name ="eliminar" type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro que quieres eliminar esta asignación?')">Eliminar</button>
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
    {{ $asignars->links()}}
</div>
@endsection