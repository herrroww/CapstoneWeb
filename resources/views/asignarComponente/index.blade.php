@extends('layouts.sidebar')

@section('content')

<div class="container-fluid">
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
  <h2>Asignar Componentes <a href="{{ route('asignarop1') }}"> <button type="button" class="btn btn-success float-right">Asignar Componente </button></a></h2>
  <h6>
  @if($search)
  <div class="alert alert-primary" role="alert">
  Se encontraron los siguientes resultados:
  </div>
    @endif

    @if(session('success'))
  <div class="alert alert-danger" role="alert">
  El Operario se a Eliminada correctamente.
  </div>
    @endif

    @if(session('create'))
  <div class="alert alert-success" role="alert">
  El operario se a creado correctamente.
  </div>
    @endif

    
    @if(session('edit'))
  <div class="alert alert-warning" role="alert">
  El operario se a editado correctamente.
  </div>
    @endif
  </h6>
<table class="table table-bordered">
  <thead>
    <tr>
	  <th scope="col">Id</th>
      <th scope="col">Operario</th>
      <th scope="col">Tipo de Operario</th>
      <th scope="col">Componente</th>
      
      
    </tr>
  </thead>
  <tbody>
  @foreach($asignars as $asignar)
    <tr>
      <th scope="row">{{$asignar->id}}</th>
        @if($asignar->operario == null)
    <td>operario no seleccionada</td>
    @else
    <td>{{$asignar->operario->nombre}}</td>

    <td>{{$asignar->operario->tipoOperario}}</td>
    @endif

    @if($asignar->componente == null)
    <td>componente no seleccionada</td>
    @else
    <td>{{$asignar->componente->nombre}}</td>
    @endif
    
    
    
    <form action="{{ route('asignaropdes', $asignar->id) }}" method="POST">
    @method('DELETE')
    @csrf
    <td><a href="{{ route('asignaropedit', $asignar->id) }}"><button type="button" class="btn btn-primary">Editar</button></a>
    <button name ="eliminar" type="submit" class="btn btn-danger" onclick="return confirm('¿Estas seguro que quieres eliminar este operario?')">Eliminar</button>
    </form>
    </td>
    </tr>
	@endforeach
  </tbody>
  @if($search)
  <a href="{{ route('asignarop') }}">
  <div style="position: absolute; left: 90%; bottom: 10%;">
  <button type="button" class="btn btn-secondary">Back</button>
 </div>
</a>
 @endif
</table>
    {{ $asignars->links()}}
</div>
@endsection