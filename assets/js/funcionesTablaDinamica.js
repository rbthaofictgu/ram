
//*********************************************************************************************************/
//*INICIO: funcion encargada de traer los datos de la tabla para llenar el select de cancelar y inadmitido
//********************************************************************************************************/
async function fCancelarInadmitido() {
   const url = `${$appcfg_Dominio}/cancelarInadmitir.php`;
   try {
      const response = await fetch(url, {
         method: 'GET',
         headers: {
            'Authorization': 'Bearer token',
         },
      });
      if (!response.ok) {
         throw new Error('Error en la solicitud');
      }
      const data = await response.json();
      console.log('Respuesta:', data);
      return data;
   } catch (error) {
      console.log('error en la funcion fCancelarIndamitir.');
      console.error('Error fCancelarInadmitido:', error);
      return null; // o false, según cómo quieras manejar el error
   }
}
//*************************************************************************************/
//*FINAL: funcion encargada de traer los datos de la tabla para llenar el select de cancelar y inadmitido
//************************************************************************************/

//*********************************************************************************************/
//*INICIO:Funcion encargada de hacer el metodo POST  al abase de datos para realizar el cmabio.
//*********************************************************************************************/
function fUpdateEstadoFetch(estado, ram, descripcion, numeroFila) {
   //* Crea un objeto FormData a partir de tu formulario
   let fd = new FormData(document.forms.form1);

   //*La URL del API que estás utilizando
   let url = `${$appcfg_Dominio}Api_Ram.php`;

   //* Agregar parámetros adicionales al FormData
   fd.append("action", "update-estado-preforma");
   fd.append("RAM", JSON.stringify(ram));
   fd.append("idEstado", JSON.stringify(estado));
   fd.append("descripcion", JSON.stringify(descripcion));
   fd.append("echo", JSON.stringify(true));

   //*Opciones para la solicitud Fetch
   const options = {
      method: "POST",   //* Método de la solicitud
      body: fd,         //*El cuerpo con los datos del FormData
   };

   //* Realizar la solicitud Fetch
   fetch(url, options)
      .then(response => response.json())
      .then(datos => {
         //*manejando el error
         if (typeof datos.error != "undefined") {
            fSweetAlertEventNormal(
               datos.errorhead,
               datos.error + "- " + datos.errormsg,
               "error"
            );
         } else {
            //* si todo esta bien mandamos notificacion de exito.
            sendToast(
               $appcfg_icono_de_success + "ESTADO DE SOLICITUD CAMBIADO EXITOSAMENTE",
               $appcfg_milisegundos_toast,
               "",
               true,
               true,
               "top",
               $appcfg_pocision_toast,
               true,
               $appcfg_style_toast,
               function () { },
               "success",
               $appcfg_offset_toast,
               $appcfg_icono_toast
            );

            //*TODO: LA FUNCION SE ENCUENTRA APARTE "anularAvisoCobro(ram)" 
            //! FUNCION ENCARGADA DE ANULAR EL AVISO DE COBRO.
            // anularAvisoCobro(ram, descripcion); //*llamando a la funcion para eliminar la fila del cambio de estado.

            //*eliminar fila.del cambio de estado.
            // document.getElementById(numeroFila).remove();
         }
      })
      .catch(error => {
         console.error("Error en la solicitud fUpdateEstadoFetch:", error);
      });
}
//*********************************************************************************************/
//*FINAL:Funcion encargada de hacer el metodo POST  al abase de datos para realizar el cmabio.
//*********************************************************************************************/

//**************************************** */
//*INICIO: funcion para ir a la ram
//**************************************** */
function agregarRams() {
   location.href = `${$appcfg_Dominio}ram.php`
}
//**************************************** */
//*FINAL: funcion para ir a la ram
//**************************************** */

//***************************************************
//* INICIO: FUNCION ENCARGADA DE LIMPIAR LA DATA/
//***************************************************/

//?nota:Object.values(obj): Esta parte del código obtiene un array con todos los valores del objeto obj.
//? Por ejemplo, si tienes un objeto como { a: [], b: [], c: [] }, Object.values(obj) devolvería [[ ], [ ], [ ]].

function verificarArraysVacios(obj) {
   //?nota:.every(): Este método se usa para verificar si todos los elementos de un array cumplen una condición. Si algún elemento no cumple la condición, devuelve false; si todos la cumplen, devuelve true.
   return Object.values(obj).every(array => Array.isArray(array) && array.length === 0);
}
//********************************************************************
//* FINAL: FUNCION ENCARGADA DE LIMPIAR LA DATA/
//************************************************************* */

