@extends('layouts.sidebar')
@section('content')
<div class="jumbotron jumbotron-fluid">
  <div class="container">
    <h1 class="display-4">{{$componente->nombre}}</h1>
    <p class="lead">{{$componente->idComponente}}</p>

  </div>
</div>

<a href="{{ route('componenteop') }}">
  <div style="position: absolute; left: 90%; bottom: 10%;">
  <button type="button" class="btn btn-secondary">Back</button>
 </div>
</a>
</div>
      
@endsection

