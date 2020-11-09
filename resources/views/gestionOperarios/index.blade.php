@extends('layouts.gestionOperariosL.sidebargestionop')

@section('content1')

<div class="container">
  <!-- SEARCH FORM -->
  <form class="form-inline ml-3 float-right">
                    <div class="input-group input-group-sm">
                        <input class="form-control form-control-navbar" name="search" type="search" placeholder="Search"
                            aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
  <h2>Lista de operarios <a href="{{ route('gestionop1') }}"> <button type="button" class="btn btn-success float-right">Agregar Operario </button></a></h2>
  <h6>
    @if($search)
  <div class="alert alert-success" role="alert">
  Se encontraron los siguientes resultados:
  </div>
    @endif
  </h6>
<table class="table table-hover">
  <thead>
    <tr>
	  <th scope="col">Id</th>
      <th scope="col">Nombre</th>
      <th scope="col">Rut</th>
      <th scope="col">Correo</th>
      <th scope="col">Empresa</th>
      <th scope="col">Tipo De Operario</th>
      <th scope="col">Fecha De Creación</th>
      <th scope="col">Fecha De Edición</th>
      <th scope="col">Opciones</th>
    </tr>
  </thead>
  <tbody>
  @foreach($operarios as $operario)
    <tr>
      <th scope="row">{{$operario->id}}</th>
      <td>{{$operario->nombre}}</td>
      <td>{{$operario->rut}}</td>
      <td>{{$operario->correo}}</td>
	  <td>{{$operario->empresa}}</td>
    <td>{{$operario->tipoOperario}}</td>
    <td>{{$operario->created_at}}</td>
    <td>{{$operario->updated_at}}</td>
    
    
    <form action="{{ route('gestionopdes', $operario->id) }}" method="POST">
    @method('DELETE')
    @csrf
    <td><a href="{{ route('gestionopedit', $operario->id) }}"><button type="button" class="btn btn-primary">Editar</button></a>
    <button name ="eliminar" type="submit" class="btn btn-danger">Eliminar</button>
    </form>
    </td>
    </tr>
	@endforeach
  </tbody>
</table>
    {{ $operarios->links()}}
</div>
@endsection