@extends('layouts.sidebar')

@section('content')
<div class="container-fluid">


    <div class="col-12 pt-3 pb-3 text-center" >
        <h2>Lista De Componentes</h2>
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

    @if(session('alert'))
      <div class="alert alert-danger" role="alert">
        {{ session('alert') }}
      </div>
    @endif

    <nav class="navbar navbar-light float-right">
                <a class="btn bg-orange color-white mr-4 my-2 my-sm-0" href="{{route('componenteop1')}}"><i class="fas fa-boxes mr-1" aria-hidden="true"></i>Agregar Componente</a>
                <form method="GET" action="{{route('componenteop')}}" class="form-inline">
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
	  <!--<th scope="col">Id</th>-->
    <th scope="col" class="bg-blue color-white">ID</th>
      <th scope="col" class="bg-blue color-white">Nombre</th>
      <th scope="col" class="bg-blue color-white">ID Componente</th>
      <th scope="col" class="bg-blue color-white">Ver Documentos</th>
      <th scope="col" class="bg-blue color-white">Editar</th>
      <th scope="col" class="bg-blue color-white">Eliminar</th>
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
    <td><a href="{{ route('componenteopshow', $componente->id) }}"><button type="button" class="btn bg-orange color-white"><i class="fas fa-file-alt"></i></button></a></td>
    
    <td><a href="{{ route('componenteopedit', $componente->id) }}"><button type="button" class="btn btn-primary"><i class="far fa-edit "></i></button></a></td>
    <td><button name ="eliminar" type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro que quieres eliminar este Componente?, Si elimina este Componente se eliminarán las asignaciones en todos los Operarios que lo contengan.')"><i class="fas fa-times " ></i></button></td>
    </form>
    </td>
    </tr>
	@endforeach
  </tbody>
  
</table>
@if($search)
  <a href="{{route('componenteop')}}">
  <button type="button" class="btn btn-secondary float-right">Volver</button>
</a>
 @endif
    {{ $componentes->appends('search',$search)->links()}}
</div>
@endsection