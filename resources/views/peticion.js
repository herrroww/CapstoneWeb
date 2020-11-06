$(obtener_registros());

function obtener_registros(capstoneweb)
{
	$.ajax({
		url : 'gestionop',
		type : 'POST',
		dataType : 'html',
		data : { capstoneweb: operarios },
		})

	.done(function(resultado){
		$("#tabla_resultado").html(resultado);
	})
}

$(document).on('keyup', '#busqueda', function()
{
	var valorBusqueda=$(this).val();
	if (valorBusqueda!="")
	{
		obtener_registros(valorBusqueda);
	}
	else
		{
			obtener_registros();
		}
});
