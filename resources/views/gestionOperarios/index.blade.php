@extends('layouts.gestionOperariosL.sidebargestionop')

@section('content1')

<div class="container">
	<h2>Lista de operarios <a href="{{ route('gestionop1') }}"> <button type="button" class="btn btn-success float-right">Agregar Operario </button></a></h2>
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
    
    
    <form action="{{ route('gestionopdes', $operario->id) }}s" method="POST">
    @method('DELETE')
    @csrf
    <td><a href="{{ route('gestionopedit', $operario->id) }}"><button type="button" class="btn btn-primary">Editar</button></a>
    <button type="submit" class="btn btn-danger">Eliminar</button>
    </form>
    
    </td>
    </tr>
	@endforeach
  </tbody>
</table>
</div>
@endsection