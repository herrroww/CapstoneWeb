<?php 
$conexion=mysqli_connect('localhost','root','','capstoneweb');
$operario=$_POST['operario'];

	$sql="SELECT id,
			 tipoOperario
		from operarios 
		where id='$operario'";

	$result=mysqli_query($conexion,$sql);

	$cadena="<label>SELECT 2 (paises)</label> 
			<select id='lista2' name='lista2'>";

	while ($ver=mysqli_fetch_row($result)) {
		$cadena=$cadena.'<option value='.$ver[0].'>'.utf8_encode($ver[2]).'</option>';
	}

	echo  $cadena."</select>";
	

?>