@extends('layouts.sidebar')

@section('content')

<!DOCTYPE html>
<html>
 <head>
  <title>How Send an Email in Laravel</title>
  

  <div class="container-fluid">
    <div class="col-12 pt-3 pb-3 text-center" >
    <h3 align="center">Contacte Con Administrador</h3>
    </div>
    <hr>

 </head>
 <body>
  <br />
  <br />
  <br />
  <div class="container box">
   <br />
   @if (count($errors) > 0)
    <div class="alert alert-danger">
     <button type="button" class="close" data-dismiss="alert">×</button>
     <ul>
      @foreach ($errors->all() as $error)
       <li>{{ $error }}</li>
      @endforeach
     </ul>
    </div>
   @endif
   @if ($message = Session::get('success'))
   <div class="alert alert-success alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
           <strong>{{ $message }}</strong>
   </div>
   @endif
   <form method="post" action="{{url('sendemail/send')}}">
    {{ csrf_field() }}
    <div class="form-group">
     <label>Ingresa tu Nombre:</label>
     <input type="text" name="name" class="form-control" value="" />
    </div>
    <div class="form-group">
     <label>Ingresa tu Correo:</label>
     <input type="text" name="email" class="form-control" value="" />
    </div>
    <div class="form-group">
     <label>Ingresa tu Mensaje:</label>
     <textarea name="message" class="form-control"></textarea>
    </div>
    <div class="form-group">
     <input type="submit" name="send" class="btn btn-info" value="Enviar" />
    </div>
   </form>
   
  </div>
 </body>
</html>

@endsection