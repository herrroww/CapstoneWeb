@extends('layouts.sidebar')

@section('content')
@foreach ($users as $user)
<div class="jumbotron jumbotron-fluid">
  <div class="container">
    <h1 class="display-4">{{ $user->name }}</h1>
    <p class="lead">{{ $user->email }}</p>

    
<td><a href="{{ route('edituser', $user->id) }}"><button type="button" class="btn btn-primary">Editar</button></a>
</form>
    </td>
  </div>
</div>


@endforeach

@endsection