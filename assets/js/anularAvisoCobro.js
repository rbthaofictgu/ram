
//*************************************************************************** */
//* INCIO: FUNCION ENCARGADA DE ANULAR AVISO DE COBRO
//*************************************************************************** */
function anularAvisoCobro(idPreforma,razon) {

   console.log('Anulando aviso de cobro para idPreforma:', idPreforma);

   let url = `${$appcfg_Dominio}anularAvisoCobro.php`;

   fetch(url, {
      method: 'POST',
      headers: {
         'Content-Type': 'application/json',
         'Authorization': 'Bearer token',
      },
      body: JSON.stringify({
         idPreforma: idPreforma,
         razon: razon,
      })
   })
      .then(response => {
         if (!response.ok) {
            throw new Error('Error en la solicitud');
         }
         return response.json();
      })
      .then(data => {
         console.log('Respuesta anular aviso de cobro:', data);

         if (data.status == 'success') {
            console.log('anulado aviso de cobro exitoso.');
         } else {
            Swal.fire({
               title: '!ERROR EN EL AVISO DE COBRO¡',
               text: 'ERROR AL ANULAR EL AVISO DE COBRO NO SE PUDO ANULAR',
               icon: 'error',
               confirmButtonText: 'OK',
            })
         }
      })
      .catch(error => {
         console.error('Error anularAvisoCobro.js:', error);
      });
}
//*************************************************************************** */
//* FINAL: FUNCION ENCARGADA DE ANULAR AVISO DE COBRO
//*************************************************************************** */
