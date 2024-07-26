//*******************************************************/
//******INICIO: FUNCION DE ALERTA PARA LOS ERRORES.******/
//*******************************************************/
function fSweetAlertEventNormal(title, msg, type, errorcode = 'success') {
    if (errorcode != null) {
      console.log('errorcode:',errorcode);
    }

    const swalWithBootstrapButtons = Swal.mixin({
      customClass: {
        confirmButton: "btn btn-"+errorcode
      },
      buttonsStyling: false
    });
  
    swalWithBootstrapButtons.fire({
      title: title,
      text: msg,
      icon: type,
      confirmButtonText: 'Aceptar'
    })
  } 

function fSweetAlertEventSelect(event,title, msg, type, errorcode = null) {
  if (errorcode != null) {
    console.log('errorcode:',errorcode);
  }

  const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
      confirmButton: "btn btn-success"
    },
    buttonsStyling: false
  });

  swalWithBootstrapButtons.fire({
    title: title,
    text: msg,
    icon: type,
    confirmButtonText: 'Aceptar',
    confirmButtonColor: "#3085d6",
  }).then((result) => {
    if (result.isConfirmed) {
      if (event.target.type == 'text') {
        event.target.select();
      }
    }
  });
}