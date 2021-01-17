@extends('layouts.sidebar')

@section('content')
<div class="container-fluid">
    <div class="col-12 pt-3 pb-3 text-center" >
        <h2>Lista de Documentos: {{ $componente->nombreComponente }} - {{ $componente->idComponente }}</h2>
    
    <hr>


<div class="container-fluid">

<h6>
  @if(session('create'))
  <div class="alert alert-success" role="alert">
  Se agregó el documento correctamente.
  </div>
  </h6>
    @endif

    
@if(session('success'))
  <div class="alert alert-danger" role="alert">
 El Documento se ha eliminado correctamente.
  </div>
    @endif

    @if(session('alert'))
      <div class="alert alert-danger" role="alert">
        {{ session('alert') }}
      </div>
    @endif
  <!-- SEARCH FORM -->

 
  <nav class="navbar navbar-light float-right">
                <a class="btn bg-orange color-white mr-4 my-2 my-sm-0" href="{{route('documentosop1')}}"><i class="fas fa-folder-plus mr-1" aria-hidden="true"></i>Agregar Documentos</a>
                <form method="GET" action="{{route('documentosop')}}" class="form-inline">
                    @csrf
                    <input name="search" class="form-control mr-sm-2" type="search" placeholder="Buscar por nombre" aria-label="Search">
                    <button class="btn bg-orange color-white my-2 my-sm-0" type="submit"><i class="fa fa-search mr-1" aria-hidden="true"></i>Buscar</button>
                </form>
            </nav>
            
  <div class="col-12 pt-3 pb-3 table-responsive">
<table class="table table-bordered">
  <thead>
    <tr>
	  <!--<th scope="col">Id</th>-->
      <th scope="col" class="bg-blue color-white">ID</th>
      <th scope="col" class="bg-blue color-white">Nombre</th>
      <th scope="col" class="bg-blue color-white">Archivo</th>
      <th scope="col" class="bg-blue color-white">Descripción</th>
      <th scope="col" class="bg-blue color-white">Privacidad</th>
     <!-- <th scope="col" class="bg-blue color-white">Ver</th>-->
      <th scope="col" class="bg-blue color-white" >Descargar</th>
      <th scope="col" class="bg-blue color-white" >Eliminar</th>
    </tr>
  </thead>
  <tbody>
  @foreach($file as $key=>$data)
    <tr>
      
      <th scope="row">{{++$key}}</th>
      <td>{{$data->nombre}}</td>
      <td>{{$data->extension}}</td>
      <td>{{$data->descripcion}}</td>
      @if($data->privacidad=="Publico")
        <td>Público</td>
      @else
        <td>{{$data->privacidad}}</td>
      @endif

      <form action="{{ route('documentoopdes', $data->id) }}" method="POST" >
    @method('DELETE')
    @csrf
    <td><a href=" {{ route('documentosopdownload', $data->id) }}" ><div class="text-center" ><h5><i class="fas fa-file-download "></i></h5></div></a></td>
    <td><button name ="eliminar" type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro que quieres eliminar este Documento? "><i class="fas fa-times " ></i></button></td>
  
    </form>
      </tr>
     

      
      
    
	@endforeach
  </tbody>

  

</table>
@if($search)
  <a href="{{ route('documentosop') }}" >
  <button type="button" class="btn btn-secondary float-right">Volver</button>
  </a>
@else
  <a href="{{ route('componenteop') }}"  >
  <button type="button" class="btn btn-secondary float-right">Volver</button>
  </a>
 @endif

{{ $file->appends('search',$search)->links()}}


</div>
@endsection