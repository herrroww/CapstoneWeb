@extends('layouts.sidebar')

@section('content')

<div class="container-fluid">
    <div class="col-12 pt-3 pb-3 text-center" >
        <h2>Lista de empresas</h2>
    </div>
    <hr>


<div class="container-fluid">

@if(session('success'))
  <div class="alert alert-danger" role="alert">
  Empresa Eliminada correctamente.
  </div>
    @endif

    @if($search)
  <div class="alert alert-primary" role="alert">
  Se encontraron los siguientes resultados:
  </div>
    @endif

    

    @if(session('create'))
  <div class="alert alert-success" role="alert">
  La empresa se a creado correctamente.
  </div>
    @endif

    
    @if(session('edit'))
  <div class="alert alert-warning" role="alert">
  La empresa se a editado correctamente.
  </div>
    @endif
  <!-- SEARCH FORM -->
  <form class="form-inline ml-3 float-right">
                    <div class="input-group input-group-sm">
                        <input class="form-control form-control-navbar" name="search" type="search" placeholder="Search"
                            aria-label="Search">
                        <div class="input-group-append">
                        <button class="btn bg-orange color-white" type="submit"><i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                </form>
  
   <a href="{{ route('empresaop1') }}"> <button type="button" style="margin-bottom:10px" class="btn  float-right bg-orange color-white" >Agregar Empresa </button></a>
  <h6>
    

    
  </h6>
<table class="table table-bordered ">
  <thead>
    <tr>
	  <!--<th scope="col">Id</th>-->
     <th scope="col"  class="bg-blue color-white">Id</th>
      <th scope="col"class="bg-blue color-white">Nombre</th>
      <th scope="col"class="bg-blue color-white">Rut</th>
      <th scope="col"class="bg-blue color-white">Compañia</th>
      <th scope="col"class="bg-blue color-white">Opciones</th>
    </tr>
  </thead>
  <tbody>
  @foreach($empresas as $empresa)
    <tr>
     
    <th scope="row">{{$empresa->id}}</th>
     <th> {{$empresa->nombre}}</th>
      <td>{{$empresa->rut}}</td>
      <td>{{$empresa->compania}}</td>
    
    
    <form action="{{ route('empresaopdes', $empresa->id) }}" method="POST" >
    @method('DELETE')
    @csrf
    <td><a href="{{ route('empresaopedit', $empresa->id) }}"><button type="button" class="btn btn-primary ">Editar</button></a>
    <button name ="eliminar" type="submit" class="btn btn-danger" onclick="return confirm('¿Estas seguro que quieres eliminar esta empresa?')">Eliminar</button>
    </form>
    </td>
    </tr>
	@endforeach
  </tbody>
  @if($search)
  <a href="{{ url()->previous() }}">
  <div style="position: absolute; left: 90%; bottom: 10%;">
  <button type="button" class="btn btn-secondary">Back</button>
 </div>
</a>
 @endif
</table>
    {{ $empresas->links()}}
</div>
@endsection