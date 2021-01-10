@extends('layouts.sidebar')

@section('content')

<div class="container-fluid">
    <div class="col-12 pt-3 pb-3 text-center" >
        <h2>Lista de Operarios</h2>
    
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
  El Operario se ha eliminado correctamente.
  </div>
    @endif

    @if(session('create'))
  <div class="alert alert-success" role="alert">
  El Operario se ha creado correctamente.
  </div>
    @endif

    
    @if(session('edit'))
  <div class="alert alert-warning" role="alert">
  El Operario se ha editado correctamente.
  </div>
    @endif
    </div>
  </h6>
  <nav class="navbar navbar-light float-right">
                <a class="btn bg-orange color-white mr-4 my-2 my-sm-0" href="{{route('gestionop1')}}"><i class="fa fa-user-plus mr-1" aria-hidden="true"></i>Agregar Operario</a>
                <form method="GET" action="{{route('gestionop')}}" class="form-inline">
                    @csrf
                    <input name="search" class="form-control mr-sm-2" type="search" placeholder="Buscar por nombre" aria-label="Search">
                    <button class="btn bg-orange color-white my-2 my-sm-0" type="submit"><i class="fa fa-search mr-1" aria-hidden="true"></i>Buscar</button>
                </form>
            </nav>
  <!-- SEARCH FORM -->
  
    
<div class="col-12 pt-3 pb-3 table-responsive">
<table class="table table-bordered ">
  <thead>
    <tr>
	    <th scope="col" class="bg-blue color-white">ID</th>
      <th scope="col" class="bg-blue color-white">Nombre</th>
      <th scope="col" class="bg-blue color-white">Rut</th>
      <th scope="col" class="bg-blue color-white">Correo</th>
      <th scope="col" class="bg-blue color-white">Tipo De Operario</th>
      <th scope="col" class="bg-blue color-white">Empresa</th>
      <th scope="col" class="bg-blue color-white">Teléfono Operario</th>
      <th scope="col" class="bg-blue color-white">Editar</th>
      <th scope="col" class="bg-blue color-white">Eliminar</th>
    </tr>
  </thead>
  <tbody>
  @foreach($operarios as $operario)
    <tr>
      <th scope="row">{{$operario->id}}</th>
      <td>{{$operario->nombreOperario}}</td>
      <td>{{$operario->rutOperario}}</td>
      <td>{{$operario->correoOperario}}</td>
    <td>{{$operario->tipoOperario}}</td>
    @if($operario->empresa == null)
    <td ></td>
    @method('DELETE')
    @else
    <td>{{$operario->empresa->nombreEmpresa}}</td>
   
    @endif
    
    <td>{{$operario->telefonoOperario}}</td>
    
    
    
    
    <form action="{{ route('gestionopdes', $operario->id) }}" method="POST">
    @method('DELETE')
    @csrf
    <td><a href="{{ route('gestionopedit', $operario->id) }}"><button type="button" class="btn btn-primary"><i class="far fa-edit "></i></button></a></td>
    <td><button name ="eliminar" type="submit" class="btn btn-danger " onclick="return confirm('¿Estás seguro que quieres eliminar este Operario?, Si elimina al Operario perdera la asignación de su(s) componente(s).')"><i class="fas fa-times"></i></button>
    </form>
    </td>
    </tr>
	@endforeach
  </tbody>
 
</table>
@if($search)
  <a href="{{ url()->previous() }}" class="float-right">
  <button type="button" class="btn btn-secondary">Volver</button>
</a>
 @endif
    {{ $operarios->links()}}
</div>
@endsection