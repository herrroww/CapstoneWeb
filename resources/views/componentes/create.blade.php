@extends('layouts.sidebar')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-sm-6">
            <h3>Agregar Componente</h3>
            <hr>



<form action="componenteop1" method="POST">
    @csrf
  <div class="form-group ">
    <label for="nombre">Nombre</label>
    <input type="text" class="form-control" name="nombre" placeholder="Escriba el nombre del componente" required>
  </div>

  <form>
  <div class="form-group ">
    <label for="idComponente">Id componente</label>
    <input type="text" class="form-control" name="idComponente" placeholder="Escriba el id del componente" required>
  </div>

  

  
  <button style="margin-top: 20px" type="submit" class="btn btn-primary ">Submit</button>
  
  <a href="{{ url()->previous() }}">
  <button style="margin-top: 20px" type="button" class="btn btn-secondary float-right">Cancelar</button>
</a>
        </div>
     </div>
</form>
        </div>
    </div>
</div>
@endsection