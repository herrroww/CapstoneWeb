@extends('layouts.sidebar')

@section('content')
<div class="container-fluid">
    <div class="col-12 pt-3 pb-3 text-center" >
        <h2>Lista de Documentos: {{ $componente->nombre }}</h2>
    </div>
    <hr>

<div class="container-fluid">
  <!-- SEARCH FORM -->
  <form class="form-inline ml-3 float-right">
                    <div class="input-group input-group-sm" >
                        <input class="form-control form-control-navbar" name="search" type="search" placeholder="Search"
                            aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn bg-orange color-white" type="submit"><i class="fas fa-search"></i> Buscar
                                
                            </button>
                        </div>
                    </div>
                </form>
 


<a href="{{ route('documentosop1') }}"> <button type="button" class="btn bg-orange color-white float-right" style="margin-bottom:10px">Agregar Documento 
  </button></a>
  

  <h6>
  @if(session('create'))
  <div class="alert alert-sucess" role="alert">
  Se agrego el documento correctamente.
  </div>
    @endif
  

    
  </h6>
<table class="table table-bordered">
  <thead>
    <tr>
	  <!--<th scope="col">Id</th>-->
      <th scope="col" class="bg-blue color-white">Id</th>
      <th scope="col" class="bg-blue color-white">Nombre</th>
      <th scope="col" class="bg-blue color-white">Descripcion</th>
     <!-- <th scope="col" class="bg-blue color-white">Ver</th>-->
      <th scope="col" class="bg-blue color-white">Descargar</th>
    </tr>
  </thead>
  <tbody>
  @foreach($file as $key=>$data)
    <tr>
      
      <th scope="row">{{++$key}}</th>
      <td>{{$data->nombre}}</td>
      <td>{{$data->descripcion}}</td>
      <!--<td><a href="{{ route('documentosopshow', $data->id) }}">View</a></td>-->
      
      <td><a href=" {{ route('documentosopdownload', $data->file) }}">Descargar</a></td>

      
      
    
	@endforeach
  </tbody>

  
  @if($search)
  <a href="{{ route('documentosop') }}">
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