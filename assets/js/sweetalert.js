//*******************************************************/
//******INICIO: FUNCION DE ALERTA PARA LOS ERRORES.******/
//*******************************************************/
function fSweetAlertEventNormal(title, msg, type,  html = '', width=1400,errorcode = 'success',textBotton = 'Aceptar',funcion = '') {
    const swalWithBootstrapButtons = Swal.mixin({
      customClass: {
        confirmButton: "btn btn-"+errorcode
      },
      buttonsStyling: false
    });
  
    if (html == '') {
      swalWithBootstrapButtons.fire({
        title: title,
        text: msg,
        icon: type,
        confirmButtonText: textBotton,
        confirmButtonColor: "#3085d6",
      }).then((result) => {
        if (result.isConfirmed) {
          if (typeof funcion === 'function') {
            funcion();
          }
        }
      });
    } else {
      swalWithBootstrapButtons.fire({
        title: title,
        html: html,
        icon: type,
        width: width, 
        confirmButtonText: textBotton,
        confirmButtonColor: "#3085d6",
      }).then((result) => {
        if (result.isConfirmed) {
          if (typeof funcion === 'function') {
            funcion();
          }
        }
      });
    }
  } 

function fSweetAlertEventSelect(event='',title, msg, type, html = '', width=1400, errorcode = 'success',textBotton = 'Aceptar',funcion = '') {
  const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
      confirmButton: "btn btn-"+errorcode
    },
    buttonsStyling: false
  });

  if (html == '') {
    swalWithBootstrapButtons.fire({
      title: title,
      text: msg,
      icon: type,
      confirmButtonText: textBotton,
      confirmButtonColor: "#3085d6",
    }).then((result) => {
      if (result.isConfirmed) {
        //Si se paso un evento real no un string
        if (typeof event != 'string') {
          if (event.target.type == 'text') {
            event.target.select();
          }
        }
        if (typeof funcion === 'function') {
          funcion();
        }
      }
    });
  } else {
    swalWithBootstrapButtons.fire({
      title: title,
      html: html,
      icon: type,
      width: width, 
      confirmButtonText: textBotton,
      confirmButtonColor: "#3085d6",
    }).then((result) => {
      if (result.isConfirmed) {
        //Si se paso un evento real no un string
        if (typeof event != 'string') {
          if (event.target.type == 'text') {
            event.target.select();
          }
        }
        if (typeof funcion === 'function') {
          funcion();
        }
      }
    });
  }
}