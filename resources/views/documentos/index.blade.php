@extends('layouts.sidebar')

@section('content')
<div class="container-fluid">
    <div class="col-12 pt-3 pb-3 text-center" >
        <h2>Lista de Documentos: {{ $componente->nombreComponente }}</h2>
    </div>
    <hr>


<div class="container-fluid">

<h6>
  @if(session('create'))
  <div class="alert alert-success" role="alert">
  Se agregó el documento correctamente.
  </div>
  </h6>
    @endif
  <!-- SEARCH FORM -->
  <form class="form-inline ml-3 float-right">
                    <div class="input-group input-group-sm" >
                        <input class="form-control form-control-navbar" name="search" type="search" placeholder="Búsqueda"
                            aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn bg-orange color-white" type="submit"><i class="fas fa-search"></i> Buscar
                                
                            </button>
                        </div>
                    </div>
                </form>
 


<a href="{{ route('documentosop1') }}"> <button type="button" class="btn bg-orange color-white float-right" style="margin-bottom:10px">Agregar Documento 
  </button></a>
  

  
  

    
  </h6>
<table class="table table-bordered">
  <thead>
    <tr>
	  <!--<th scope="col">Id</th>-->
      <th scope="col" class="bg-blue color-white">ID</th>
      <th scope="col" class="bg-blue color-white">Nombre</th>
      <th scope="col" class="bg-blue color-white">Extensión</th>
      <th scope="col" class="bg-blue color-white">Descripción</th>
      <th scope="col" class="bg-blue color-white">Privacidad</th>
     <!-- <th scope="col" class="bg-blue color-white">Ver</th>-->
      <th scope="col" class="bg-blue color-white" >Descargar</th>
    </tr>
  </thead>
  <tbody>
  @foreach($file as $key=>$data)
    <tr>
      
      <th scope="row">{{++$key}}</th>
      <td>{{$data->nombre}}</td>
      <td>{{$data->extension}}</td>
      <td>{{$data->descripcion}}</td>
      <td>{{$data->privacidad}}</td>
      <!--<td><a href="{{ route('documentosopshow', $data->id) }}">View</a></td>-->
      
      <td><a href=" {{ route('documentosopdownload', $data->file) }}" ><div class="text-center" ><h5><i class="fas fa-file-download "></i></h5></div></a></td>

      
      
    
	@endforeach
  </tbody>

  
  @if($search)
  <a href="{{ route('documentosop') }}">
  <div style="position: absolute; left: 90%; bottom: 10%;">
  <button type="button" class="btn btn-secondary">Volver</button>
 </div>
</a>
@else
  <a href="{{ route('componenteop') }}">
  <div style="position: absolute; left: 90%; bottom: 10%;">
  <button type="button" class="btn btn-secondary">Volver</button>
 </div>
</a>
 @endif

 

</table>


</div>
@endsection