@extends('layouts.sidebar')

@section('content')
<div class="container">
<table class="table table-hover">
  <thead>
    <tr>
	  <!--<th scope="col">Id</th>-->
      <th scope="col">Nombre</th>
      <th scope="col">Id Componente</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">{{$componente->nombre}}</th>
      <td>{{$componente->idComponente}}</td>
</table>

<a href="{{ route('componenteop') }}">
  <div style="position: absolute; left: 90%; bottom: 10%;">
  <button type="button" class="btn btn-secondary">Back</button>
 </div>
</a>
</div>
      
@endsection