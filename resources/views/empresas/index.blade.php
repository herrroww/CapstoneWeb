@extends('layouts.sidebar')

@section('content')

<div class="container-fluid">
    <div class="col-12 pt-3 pb-3 text-center" >
        <h2>Lista de Empresas</h2>
    
    <hr>


<div class="container-fluid">

@if(session('success'))
  <div class="alert alert-danger" role="alert">
 La Empresa se ha eliminado correctamente.
  </div>
    @endif

    @if($search)
  <div class="alert alert-primary" role="alert">
  Se encontraron los siguientes resultados:
  </div>
    @endif

    

    @if(session('create'))
  <div class="alert alert-success" role="alert">
  La Empresa se ha creado correctamente.
  </div>
    @endif

    
    @if(session('edit'))
  <div class="alert alert-warning" role="alert">
  La Empresa se ha editado correctamente.
  </div>
    @endif
    </div>

    <nav class="navbar navbar-light float-right">
                <a class="btn bg-orange color-white mr-4 my-2 my-sm-0" href="{{route('empresaop1')}}"><i class="fa fa-building mr-1" aria-hidden="true"></i>Agregar Empresa</a>
                <form method="GET" action="{{route('empresaop')}}" class="form-inline">
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
	  <!--<th scope="col">Id</th>-->
     <th scope="col"  class="bg-blue color-white">ID</th>
      <th scope="col"class="bg-blue color-white">Nombre</th>
      <th scope="col"class="bg-blue color-white">Rut</th>
      <th scope="col"class="bg-blue color-white">Compañía</th>
      <th scope="col"class="bg-blue color-white">Editar</th>
      <th scope="col"class="bg-blue color-white">Eliminar</th>
    </tr>
  </thead>
  <tbody>
  @foreach($empresas as $empresa)
    <tr>
     
    <th scope="row">{{$empresa->id}}</th>
     <th> {{$empresa->nombreEmpresa}}</th>
      <td>{{$empresa->rutEmpresa}}</td>
      <td>{{$empresa->compania}}</td>
    
    <form action="{{ route('empresaopdes', $empresa->id) }}" method="POST" >
    @method('DELETE')
    @csrf
    <td><a href="{{ route('empresaopedit', $empresa->id) }}"><button type="button" class="btn btn-primary "><i class="far fa-edit "></i></button></a>
    <td><button name ="eliminar" type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro que quieres eliminar esta Empresa?, Si elimina esta Empresa se eliminarán todos los Operarios vinculados a esta.')"><i class="fas fa-times " ></i></button></td>
  
 
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
    {{ $empresas->links()}}
</div>

@endsection