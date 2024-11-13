<script>
//* funcion encargada de abrirl el modal y eleiminar las variables de session
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

///* funcion encargada de validar el usuario y contraseña en el archivo de api_login 
function desbloquear() {
   // console.log('desbloqueado function 1');

   const usuario = document.getElementById('txt_usuario_desbloqueo').value;
   const clave = document.getElementById('txt_clave_desbloqueo').value;

   // Primera llamada para obtener datos de usuario
   $.ajax({
      type: "POST",
      url: `${$appcfg_Dominio_Corto}/config/api_login.php`,
      data: {
         'nombre': usuario,
         'password': clave,
         'appid': '89b473b3ea9d5b6719c8ee8ce0c247d5',
         'action': 'do-login-web',
         'modulo': 76
      },
      dataType: "json",
      success: function(result) {
         //! funcion encargada de manejar la varte valida del llamo
         //! llamando el archivo de validarajax
         leLoginResponse(result);
      },
      error: function() {
         alert('Error en ajax de obtener datos de usuario');
      }
   });

   // Limpiar la contraseña
   document.getElementById('txt_clave_desbloqueo').value = '';
}

//* funcion encargada de llamr el jquery de validarajax
function leLoginResponse(result) {
   if (result[0].result === 1) {
      console.log('Validación inicial exitosa 2');
      //!llamando funcion
      f_validarajax(result[1]);
   } else {
      const errorMsg = result[0].result === -1 ?
         'Usuario no está activo' :
         'Usuario y clave incorrectos.';
      manejoError(errorMsg);
   }
}

//* llamndo 
function f_validarajax(dataUser) {
   console.log('dentro de f_validarAjax 3');
   // console.log(dataUser);
   // console.log(dataUser.imagen.object.img);
   //`${$appcfg_Dominio}validarajax.php`,
   
   $.ajax({
      type: "POST",
      url: 'https://satt2.transporte.gob.hn:285/ram/validarajax.php',
      data: {
         'txt_usuario': dataUser.usuario,
         'txt_clave': dataUser.session_key,
         'body_class': $body_class,
      },
      dataType: 'json', //"text",
      success: function(result) {
         //  console.log('Respuesta recibida:', result);
         try {
            const parsedResult = result; // Intenta parsear manualmente
            // console.log(parsedResult.msg);
            validationResponse(parsedResult);
         } catch (e) {
            console.error('Error al parsear JSON:', e);
         }
      },
      error: function() {
         alert('Error en ajax para validación adicional');
      }
   });
}

function validationResponse(result) {
   if (result['msg'] === 'S') {
      console.log('Validación adicional exitosa 4');
      //*actualizando datos del modal y mensajes de error
      updateUserInterface(result);
   } else {
      const errorMsg = result['msg'] === 'I' ?
         'Usuario no está activo' :
         'Su usuario y clave no son correctos.';
      manejoError(errorMsg);
   }
}

//* funcion qu emaneja el error 
function manejoError(message) {
   $("#ajax_msg").html(
      `<div class="alert alert-danger alert-dismissible" role="alert">
         <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
         <strong>Errores! </strong><br>${message}
      </div>`
   );
}

function updateUserInterface(result) {
   console.log('actualizando 5')
   $("#ajax_msg").removeClass("alert alert-danger alert-dismissible").html('');
   $('#myModal').modal('hide');

   const userFoto =
      `<img type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" title="${result['nombre']}" src="${result['foto']}" class="rounded-circle" alt="Foto Usuario" width="35px" height="35px">`;
   $("#idUserFotoFooter").html(userFoto);
   $("#id_usuariomenu").html(
      `<img src="${result['foto']}" class="rounded-circle" alt="Imagen Usuario" width="35px" height="35px">`);
}



function cerrarModal(myModal) {
   $('#myModal').modal('hide');
}
</script>