@extends('layouts.sidebar')

@section('content')

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
  <h2>Lista de Componentes <a href="{{ route('componenteop1') }}"> <button type="button" class="btn btn-success float-right">Agregar Componente </button></a></h2>
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
	  <!--<th scope="col">Id</th>-->
    <th scope="col">Id</th>
      <th scope="col">Nombre</th>
      <th scope="col">Id Componente</th>
    </tr>
  </thead>
  <tbody>
  @foreach($componentes as $componente)
    <tr>
      
      <th scope="row">{{$componente->id}}</th>
      <td>{{$componente->nombre}}</td>
      <td>{{$componente->idComponente}}</td>
      
    
    <form action="{{ route('componenteopdes', $componente->id) }}" method="POST">
    @method('DELETE')
    @csrf
    <td> <!--<a href="{{ route('componenteopshow', $componente->id) }}"><button type="button" class="btn btn-info">Agregar Modelo</button></a>-->
    <a href="{{ route('componenteopshow', $componente->id) }}"><button type="button" class="btn btn-info">Mostrar componente</button></a>
    <a href="{{ route('componenteopedit', $componente->id) }}"><button type="button" class="btn btn-primary">Editar</button></a>
    <button name ="eliminar" type="submit" class="btn btn-danger">Eliminar</button>
    </form>
    </td>
    </tr>
	@endforeach
  </tbody>
  @if($search)
  <a href="{{ route('componenteop') }}">
  <div style="position: absolute; left: 90%; bottom: 10%;">
  <button type="button" class="btn btn-secondary">Back</button>
 </div>
</a>
 @endif
</table>
    {{ $componentes->links()}}
</div>
@endsection