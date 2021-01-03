<!DOCTYPE html>
<html>
<head>
    <title>Detalles</title>
</head>
<body>
<h2>{{$data->nombre}}</h2>
<h3>{{$data->descripcion}}</h3>
<p>
    <iframe src="{{url('storage/'.$data->file)}}" style="width: 10000px;
    height: 10000px;"></iframe>
</p>
</body>
</html>