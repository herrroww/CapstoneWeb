@extends('layouts.sidebar')
@section('content')
<div class="jumbotron jumbotron-fluid">
  <div class="container">
    <h1 class="display-4">{{$componente->nombreComponente}}</h1>
    <p class="lead">{{$componente->idComponente}}</p>

  </div>
</div>

<a href="{{ url()->previous() }}">
  <div style="position: absolute; left: 90%; bottom: 10%;">
  <button type="button" class="btn btn-secondary">Volver</button>
 </div>
</a>
</div>
      
@endsection

