@extends('layouts.sidebar')

@section('content')

  <div class="container">
    <div class="col-12 pt-3 pb-3 text-center" >
      <h2>Cambios Realizados</h2>
    </div>
    <hr>    
  <div class="container">
    <table class="table table-bordered " >
      <thead >
        <tr>            
          <th scope="col" class="text-center bg-blue color-white">Contenido</th>
        </tr>
      </thead>
          
      <tbody id="historicogestion" >
        <tr>
          <td>
            <table class="table-bordered "></table>

            <table class="table table-bordered">
              <thead></thead></td>
        </tr>
      </tbody>

        <tr>
          <th scope="row">ID:</th>
          <td>{{$historicogestion->id}}</td>
        </tr>

        <tr>
          <th scope="row">Gestión: </th>
          <td>{{$historicogestion->nombreGestion}}</td>
        </tr>

        <tr>
          <th scope="row">Acción: </th>
          <td>{{$historicogestion->tipoGestion}}</td>
        </tr>

        <tr>
          <th scope="row">Responsable: </th>
          <td>{{$historicogestion->responsableGestion}}</td>
        </tr>

        <tr>
          <th scope="row">Descripción: </th>
          <td>{{$historicogestion->descripcionGestion}}</td>
        </tr>
        
        
         
    </table>

    <form>
    <a href="{{ route('reporteop') }}">
    <button style="margin-top: 20px" type="button" class="btn btn-secondary float-right">Volver</button>
    </a>
    </form>      
    </table>  
        
         

  </div>
   
</div>     
@endsection
