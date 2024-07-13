//*******************************************************/
//******INICIO: FUNCION DE ALERTA PARA LOS ERRORES.******/
//*******************************************************/
function f_sweetalert(title, msg, type, errorcode = null) {
    if (errorcode != null) {
      console.log('errorcode:',errorcode);
    }
    Swal.fire({
      title: title,
      text: msg,
      icon: type,
      confirmButtonText: 'Aceptar'
    })
  }
  