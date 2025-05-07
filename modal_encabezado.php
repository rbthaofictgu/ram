<script type="text/javascript">
$(document).ready(function(){

	// Desplegando la forma modal	
	$('#modal').click(function(){
		//Llamando pagina que destruye las variables de sesión.
		url_llamar = "../pag_ajax/salir.php";
		$.ajax({type: "POST",
			url: url_llamar,
			success:function(result){
		}});
		
		// Posicionando el cursor en el campo
		$('#myModal').on("shown.bs.modal", function() {
				$(this).find(".form-control:first").focus();
		});		
		
		// Abriendo la pantalla modal	
		$('#myModal').modal({
		  backdrop: 'static',
		  keyboard: false,
		  locked: true
		});
		document.getElementById('txt_clave_desbloqueo').focus();
	}); 
});

function desbloquear () {
	$.ajax({type: "POST",
		url: DominioSite + "validarajax.php",
		data: {'txt_usuario': document.getElementById('txt_usuario_desbloqueo').value,
			   'txt_clave': document.getElementById('txt_clave_desbloqueo').value,
			   'body_class': body_class},
		dataType: "json",
		success:function(result){
			if (result['msg'] == 'S') {
           		$("#ajax_msg").removeClass("alert alert-danger alert-dismissible");//$('#myModal').modal('hide');
				$("#ajax_msg").html('')
           		$('#myModal').modal('hide');
				// Insertando imagen en el pie de la página	
				$("#idUserFotoFooter").html('<img type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation"  title="' + result['nombre'] + '" src="' + result['foto'] + '" class="rounded-circle" alt="Foto Usuario" width="35px" height="35px">');				
				$img = '<img src="' + result['foto'] + '" class="rounded-circle" alt="Imagen Usuario" width="35px" height="35px">'; 
				$("#id_usuariomenu").html($img);
			} else{
				if (result['msg'] == 'I') {
					//$("#ajax_msg").addClass("alert alert-danger alert-dismissible");
					$("#ajax_msg").html('<div id="msg-completo"><div id="msg-global" class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button><div id="msg-error">' + '<strong>Errores! </strong>' + '<br\>Usuario no esta activo</div></div></div>');
				} else {
					//$("#ajax_msg").addClass("alert alert-danger alert-dismissible");
					$("#ajax_msg").html('<div id="msg-completo"><div id="msg-global" class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button><div id="msg-error">' + '<strong>Errores! </strong>' + '<br\>Su usuario y clave no.</div></div></div>');
				}
			}
	},
		error: function() {
            alert('Error en ajax de validar usuario');
    }});
	document.getElementById('txt_clave_desbloqueo').value = '';
}
</script>