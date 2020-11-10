@extends('layouts.sidebar')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-sm-6">
            <h3>Editar Usuario: {{ $users->name }}</h3>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
                 </ul>
                    </div>
                    @endif

<form action="" method="POST">
    @method('PATCH')
    @csrf
  <div class="form-group">
    <label for="name">Nombre</label>
    <input type="text" class="form-control" name="name" value="{{ $users->name }}" placeholder="Escriba nombre operario" required>
  </div>

  
  <form>
  <div class="form-group">
    <label for="email">Correo</label>
    <input type="email" class="form-control" name="email" value="{{ $users->email }}" placeholder="Escriba correo del operario" required>
  </div>

  <button style="margin-top: 20px" type="submit"  class="btn btn-primary">Editar</button>

</form>

        </div>
    </div>
</div>
@endsection