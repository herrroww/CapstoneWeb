@extends('layouts.sidebar')

@section('content')
<div class="container-fluid">


    <div class="col-12 pt-3 pb-3 text-center" >
        <h2>Lista De Componentes</h2>
    </div>
    <hr>
<div class="container-fluid">

<h6>
    @if($search)
  <div class="alert alert-primary" role="alert">
  Se encontraron los siguientes resultados:
  </div>
    @endif
  </h6>

  @if(session('success'))
  <div class="alert alert-danger" role="alert">
  El Componente se ha eliminado correctamente.
  </div>
    @endif

    @if(session('create'))
  <div class="alert alert-success" role="alert">
  El Componente se ha creado correctamente.
  </div>
    @endif

    
    @if(session('edit'))
  <div class="alert alert-warning" role="alert">
  El Componente se ha editado correctamente.
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
  <a href="{{ route('componenteop1') }}"> <button type="button" class="btn bg-orange color-white float-right" style="margin-bottom:10px">Agregar Componente </button></a>
  
<table class="table table-bordered">
  <thead>
    <tr>
	  <!--<th scope="col">Id</th>-->
    <th scope="col" class="bg-blue color-white">ID</th>
      <th scope="col" class="bg-blue color-white">Nombre</th>
      <th scope="col" class="bg-blue color-white">ID Componente</th>
      <th scope="col" class="bg-blue color-white">Opciones</th>
    </tr>
  </thead>
  <tbody>
  @foreach($componentes as $componente)
    <tr>
      
      <th scope="row">{{$componente->id}}</th>
      <td>{{$componente->nombreComponente}}</td>
      <td>{{$componente->idComponente}}</td>
      
    
    <form action="{{ route('componenteopdes', $componente->id) }}" method="POST">
    @method('DELETE')
    @csrf
    <td> <!--<a href="{{ route('componenteopshow', $componente->id) }}"><button type="button" class="btn btn-info">Agregar Modelo</button></a>-->
    <a href="{{ route('componenteopshow', $componente->id) }}"><button type="button" class="btn bg-orange color-white">Ver Documentos del Componente</button></a>
    <a href="{{ route('componenteopedit', $componente->id) }}"><button type="button" class="btn btn-primary">Editar</button></a>
    <button name ="eliminar" type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro que quieres eliminar este Componente?, Si elimina este Componente se eliminarán las asignaciones en todos los Operarios que lo contengan.')">Eliminar</button>
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
    {{ $componentes->links()}}
</div>
@endsection