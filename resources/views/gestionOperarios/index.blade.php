@extends('layouts.sidebar')

@section('content')

<div class="container-fluid">
    <div class="col-12 pt-3 pb-3 text-center" >
        <h2>Lista de Operarios</h2>
    </div>
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
  </h6>
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

                <form class="float-right">
                    <div class="input-group input-group-sm">
                        <div class="input-group-append">
                        <a href="{{ route('gestionop1') }}"><button type="button" class="btn bg-orange color-white float-right" style="margin-bottom:10px"><i class="fas fa-user-plus"></i> Agregar Operario </button></a>
                        </div>
                    </div>
                </form>
    

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
  @if($search)
  <a href="{{ url()->previous() }}">
  <div style="position: absolute; left: 90%; bottom: 10%;">
  <button type="button" class="btn btn-secondary">Volver</button>
 </div>
</a>
 @endif
</table>
    {{ $operarios->links()}}
</div>
@endsection