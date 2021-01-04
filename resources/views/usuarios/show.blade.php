@extends('layouts.sidebar')

@section('content')

<div class="jumbotron jumbotron-fluid  ">
  <div class="container">
    <h1 class="display-4">{{ $users->name }}</h1>
    <p class="lead">{{ $users->email }}</p>

    
<td><a href="{{ route('edituser', $users->id) }}"><button type="button" class="btn bg-orange color-white">Editar</button></a>
</form>
    </td>
  </div>
</div>




@endsection