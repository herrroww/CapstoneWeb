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
  <h2>Lista de Modelos  <a href="{{ route('modelosop1') }}"> <button type="button" class="btn btn-success float-right">Agregar Modelo </button></a></h2>
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
      <th scope="col">Nombre</th>
      <th scope="col">Id Modelo</th>
    </tr>
  </thead>
  <tbody>
  @foreach($modelos as $modelo)
    <tr>
      
      <th scope="row">{{$modelo->nombre}}</th>
      <td>{{$modelo->idModelo}}</td>
      
    
    <form action="{{ route('modelosopdes', $modelo->id) }}" method="POST">
    @method('DELETE')
    @csrf
    <td> 
    <a href="{{ route('modelosopedit', $modelo->id) }}"><button type="button" class="btn btn-primary">Editar</button></a>
    <button name ="eliminar" type="submit" class="btn btn-danger">Eliminar</button>
    </form>
    </td>
    </tr>
	@endforeach
  </tbody>

  @if($search)
  <a href="{{ route('modelosop') }}">
  <div style="position: absolute; left: 90%; bottom: 10%;">
  <button type="button" class="btn btn-secondary">Back</button>
 </div>
</a>
@else
  <a href="{{ route('componenteop') }}">
  <div style="position: absolute; left: 90%; bottom: 10%;">
  <button type="button" class="btn btn-secondary">Back</button>
 </div>
</a>
 @endif

 

</table>


</div>
@endsection