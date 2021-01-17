@extends('layouts.sidebar')

@section('content')

<div class="container-fluid">
    <div class="col-12 pt-3 pb-3 text-center" >
        <h2>Asignar Componente</h2>
   
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

    @if(session('alert'))
      <div class="alert alert-danger" role="alert">
        {{ session('alert') }}
      </div>
    @endif

    <nav class="navbar navbar-light float-right">
                <a class="btn bg-orange color-white mr-4 my-2 my-sm-0" href="{{route('asignarop1')}}"><i class="fas fa-people-carry mr-1" aria-hidden="true"></i>Asignar Componente</a>
                <form method="GET" action="{{route('asignarop')}}" class="form-inline">
                    @csrf
                    <input name="search" class="form-control mr-sm-2" type="search" placeholder="Buscar por nombre" aria-label="Search">
                    <button class="btn bg-orange color-white my-2 my-sm-0" type="submit"><i class="fa fa-search mr-1" aria-hidden="true"></i>Buscar</button>
                </form>
            </nav>

  <!-- SEARCH FORM -->
<div class="col-12 pt-3 pb-3 table-responsive">
<table class="table table-bordered">
  <thead>
    <tr>
	  <th scope="col" class="bg-blue color-white">ID</th>
      <th scope="col" class="bg-blue color-white">Operario</th>
      <th scope="col" class="bg-blue color-white">Tipo de Operario</th>
      <th scope="col" class="bg-blue color-white">Componente</th>
      <th scope="col" class="bg-blue color-white">Editar</th>
      <th scope="col" class="bg-blue color-white">Eliminar</th>
      
      
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
    <td><a href="{{ route('asignaropedit', $asignar->id) }}"><button type="button" class="btn btn-primary"><i class="far fa-edit "></i></button></a>
    <td><button name ="eliminar" type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro que quieres eliminar esta asignación?')"><i class="fas fa-times " ></i></button></td>
    </form>
    </td>
    </tr>
	@endforeach
  </tbody>
</table>
@if($search)
  <a href="{{route('asignarop')}}">
  <button type="button" class="btn btn-secondary float-right">Volver</button>
</a>
 @endif
    {{ $asignars->appends('search',$search)->links()}}
</div>
@endsection