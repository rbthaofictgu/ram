<script>
$(document).ready(function() {
   // Desplegando la forma modal	

   $('#modal').click(function() {
      // Llamando pagina que destruye las variables de sesión.
      url_llamar = "../../pag_ajax/salir.php";
      $.ajax({
         type: "POST",
         url: url_llamar,
         success: function(result) {}
      });

      // Posicionando el cursor en el campo
      $('#myModal').on("shown.bs.modal", function() {
         $(this).find(".form-control:first").focus();
      });

      // Abriendo la pantalla modal	
      $('#myModal').modal('show');
      // $('#myModal').modal({
      // 	backdrop: 'static',
      // 	keyboard: false,
      // 	locked: true
      // });

      document.getElementById('txt_clave_desbloqueo').focus();
   });
});


// function desbloquear() {
// 	console.log('desbloqueado function');
// 	// console.log($body_class);
// 	$.ajax({
// 		type: "POST",
// 		// url: $appcfg_Dominio + "validarajax.php",
// 		url: $appcfg_Dominio_Corto + "/config/api_login.php",
// 		data: {
// 			'nombre': document.getElementById('txt_usuario_desbloqueo').value,
// 			'password': document.getElementById('txt_clave_desbloqueo').value,
// 			'appid': '89b473b3ea9d5b6719c8ee8ce0c247d5',
// 			'action': 'do-login-web',
// 			'modulo': 76,
// 			// 'body_class': $body_class
// 		},
// 		dataType: "json",
// 		success: function(result) {
// 			if (result[0].result == 1) { // Asegúrate de acceder al índice correcto
// 				console.log('éxito');
// 				// Lógica para usuario válido
// 				$(document).ready(function() {
// 					$("#ajax_msg").removeClass("alert alert-danger alert-dismissible");
// 					$("#ajax_msg").html('');
// 					$('#myModal').modal('hide');
// 				});

// 				// Acceder a la información del usuario
// 				const usuario = result[1]; // Segundo objeto en el array
// 				console.log(usuario.perfil.Nombre);
// 				$("#idUserFotoFooter").html(
// 					'<img src="' + usuario.imagen.img + '" class="rounded-circle" alt="Foto Usuario" width="35px" height="35px" title="' + usuario.perfil.Nombre + '">'
// 				);
// 				$("#id_usuariomenu").html('<img src="' + usuario.imagen.img + '" class="rounded-circle" alt="Imagen Usuario" width="35px" height="35px">');
// 			} else if (result[0].result == -1) {
// 				console.log(result + ' error');
// 				$("#ajax_msg").html(
// 					'<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button><strong>Errores! </strong><br>Usuario no está activo</div>'
// 				);
// 			} else {
// 				// Lógica para usuario o clave incorrectos
// 				$("#ajax_msg").html(
// 					'<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button><strong>Errores! </strong><br>Usuario y clave incorrectos.</div>'
// 				);
// 			}
// 		},
// 		error: function() {
// 			alert('Error en ajax de validar usuario');
// 		}
// 	});
// 	document.getElementById('txt_clave_desbloqueo').value = '';
// }

function desbloquear() {
   console.log('desbloqueado function');

   // Primera llamada para obtener datos de usuario
   $.ajax({
      type: "POST",
      url: $appcfg_Dominio_Corto + "/config/api_login.php", // Primer URL para validar
      data: {
         'nombre': document.getElementById('txt_usuario_desbloqueo').value,
         'password': document.getElementById('txt_clave_desbloqueo').value,
         'appid': '89b473b3ea9d5b6719c8ee8ce0c247d5',
         'action': 'do-login-web',
         'modulo': 76
      },
      dataType: "json",
      success: function(result) {
         if (result[0].result == 1) { // Usuario válido
            console.log('Validación inicial exitosa');
            // Mostrar datos del usuario
            const dataUser = result[1];

				f_validarajax(dataUser,);
            // Segunda llamada para validar usuario adicionalmente
         } else if (result[0].result == -1) {
            console.log(result + ' error');
            $("#ajax_msg").html(
               '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button><strong>Errores! </strong><br>Usuario no está activo</div>'
            );
         } else {
            $("#ajax_msg").html(
               '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button><strong>Errores! </strong><br>Usuario y clave incorrectos.</div>'
            );
         }
      },
      error: function() {
         alert('Error en ajax de obtener datos de usuario');
      }
   });
   document.getElementById('txt_clave_desbloqueo').value = '';
}

function f_validarajax(dataUser) {
	console.log('dentro de f_validarAjax');
   $.ajax({
      type: "POST",
      url: $appcfg_Dominio + "validarajax.php", // Segundo URL para validación adicional
      data: {
         'txt_usuario': dataUser.usuario,
         'txt_clave': dataUser.session_key,
         'body_class': $body_class,
      },
      dataType: "json",
      success: function(json) {
			 if (result[0].result == 1) {
            console.log('Validación adicional exitosa');

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
           
         } else if (validationResult[0].result == -1) {
            console.log(validationResult + ' error');
           
         }

      },
      error: function() {
         alert('Error en ajax para validación adicional');
      }
   });
}

function cerrarModal(myModal) {
   $('#myModal').modal('hide');
}
</script>

  <!-- 
         // if (result[0].result == 1) {
         //    console.log('Validación adicional exitosa');

			// 	// if (result['msg'] == 'S') {
         //   	// 	$("#ajax_msg").removeClass("alert alert-danger alert-dismissible");//$('#myModal').modal('hide');
			// 	// $("#ajax_msg").html('')
         //   	// 	$('#myModal').modal('hide');
			// 	// // Insertando imagen en el pie de la página	
			// 	// $("#idUserFotoFooter").html('<img type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation"  title="' + result['nombre'] + '" src="' + result['foto'] + '" class="rounded-circle" alt="Foto Usuario" width="35px" height="35px">');				
			// 	// $img = '<img src="' + result['foto'] + '" class="rounded-circle" alt="Imagen Usuario" width="35px" height="35px">'; 
			// 	// $("#id_usuariomenu").html($img);
			// // } else{
			// // 	if (result['msg'] == 'I') {
			// // 		//$("#ajax_msg").addClass("alert alert-danger alert-dismissible");
			// // 		$("#ajax_msg").html('<div id="msg-completo"><div id="msg-global" class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button><div id="msg-error">' + '<strong>Errores! </strong>' + '<br\>Usuario no esta activo</div></div></div>');
			// // 	} else {
			// // 		//$("#ajax_msg").addClass("alert alert-danger alert-dismissible");
			// // 		$("#ajax_msg").html('<div id="msg-completo"><div id="msg-global" class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button><div id="msg-error">' + '<strong>Errores! </strong>' + '<br\>Su usuario y clave no.</div></div></div>');
			// // 	}
			// // }
           
         // } else if (validationResult[0].result == -1) {
         //    console.log(validationResult + ' error');
           
         // } -->