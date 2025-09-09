//*****************************************************************************/
//* INICO: funcion encargada de traer toda la informacion de la ram ingresada
//*****************************************************************************/

var loadingIndicator = '';

function buscarRAM() {

   if (loadingIndicator != '') {
      document.getElementById("idDatos").innerHTML = loadingIndicator;
   }
   //*instancia de la ram ingresada
   const ram = document.getElementById('ram').value.trim();
   //*obteniendo los datos que existen en idDatos
   loadingIndicator = document.getElementById("idDatos").innerHTML;

   //*colocando inagne de carga en tabla_container.
   document.getElementById("idDatos").innerHTML =
      '<div class="mt-5"><center><img width="100px" height="100px" src="' + $appcfg_Dominio_Corto + 'ram/assets/images/loading-waiting.gif"></center></div>';

   //* alerta cunado no existe ram a buscar
   if (ram === '') {

      document.getElementById('idDatos').innerHTML = `<div id="alertam" class="alert alert-primary mt-5 text-center" style="text-transform: uppercase;" role="alert">
         <strong> DEBE INGRESAR UNA RAM PARA PODER CONSULTAR.</strong></div>`;
      // Quitar alerta después de 2 segundos (opcional)
      // setTimeout(() => {
      //    document.getElementById('idDatos').innerHTML = loadingIndicator;
      // }, 1000);
      return;
   }

   //*instancia del fetch
   fetch(`${$appcfg_Dominio}regenerar.php`, {
      method: 'POST',
      headers: {
         'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: 'ram=' + encodeURIComponent(ram)
   })
      .then(response => response.json())
      .then(data => {
         //*manejando la respuesta del fectch.
         if (data.exito) {
            document.getElementById("idDatos").innerHTML = loadingIndicator;
            document.getElementById('btnRegenerar').innerHTML += `<strong> ${ram}</strong>`;
            document.getElementById('ID_Resolucion').value = `${data.ID_Resolucion}`;
            document.getElementById('ID_AutoAdmision').value = `${data.ID_AutoAdmision}`
            document.getElementById('nombreSolicitante').value = `${data.NombreSolicitante}`
            document.getElementById('nombreEmpresa').value = `${data.NombreEmpresa}`;
            document.getElementById('rtn').value = `${data.RTNSolicitante}`;
            // document.getElementById('Expediente_Estado').value = `${data.Expediente_Estado}`
            document.getElementById('Concesiones').innerHTML = `${data.Concesiones}`;
            document.getElementById('Tramites').innerHTML = `${data.Tramites}`;
            document.getElementById('DESC_Estado_Expediente').value = `${data.DESC_Estado_Expediente}`
            document.getElementById('btnRegenerar').style.display = 'block';
         } else {
            //*alerta si no se encontro la ram ingresada.
            document.getElementById('idDatos').innerHTML =
               `<div id="alertam" class="alert alert-warning text-center" style="text-transform: uppercase;" role = "alert">
      NO SE ENCONTRO LA <strong> ${ram}</></div>`;
         }
      })
      .catch(error => {
         console.error('Error regenerar.php/buscarRAM:', error);
         Swal.fire({
            title: `ERROR AL CONSULTAR RAM`,
            text: '¡INTENTE DE NUEVO SI PERSITE COMUIQUESE A TECNOLOGÍA!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'SÍ',
         }).then((result) => {
            //* Si confirma que está seguro de agregar la concesión, llamamos la función para agregarla.
            if (result.isConfirmed) {
               document.getElementById("idDatos").innerHTML = loadingIndicator;
               limpiar();
            }
         });
      });
}
//*****************************************************************************/
//* FINAL: funcion encargada de traer toda la informacion de la ram ingresada
//*****************************************************************************/

//******************************************************************************/
//* INICIO: funcion encargada de regenerar la resolucion y auto de ingreso
//******************************************************************************/
function fRegenerar() {

   let fd = new FormData(document.forms.form1);
   var ram = document.getElementById('ram').value.trim();
   var resolucion = document.getElementById('ID_Resolucion').value.trim();
   var automotivado = document.getElementById('ID_AutoAdmision').value.trim();

   var url = '';

   url = `${$appcfg_Dominio}Api_Exp.php`;
   fd.append("action", "regenerar-resolucion");
   fd.append("RAM", ram);
   fd.append("Resolucion", resolucion);
   fd.append("AutoMotivado", automotivado);

   const options = {
      method: "POST",
      body: fd,
   };

   // Hacer al solicitud fetch con un timeout de 2 minutos
   fetchWithTimeout(url, options, 1000000)
      .then((response) => response.json())
      .then(function (Datos) {
         if (typeof Datos.ERROR != "undefined" || typeof Datos.error != "undefined") {
            fSweetAlertEventSelect(
               "",
               Datos.errorhead + '-' + Datos.errorhead,
               Datos.errormsg,
               "error"
            );
            return true;
         } else {

            //*******************************************************************************************************/
            //*INICIO: ENVIO DE MENSAJE DE CIERRE EJECUTADO SATISFACTORIAMENTE
            //*******************************************************************************************************/
            var html = Datos;
            var text = $appcfg_icono_de_success + " REGENERACION DE " + ram + " EXITOSA";
            sendToast(
               text,
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

            var title = "SE REGENERO SACTISFACTORIAMENTE " + ram;
            var html = Datos.AutoIngreso + "<br/>";
            html += Datos.Resolucion + "<br/>";

            fSweetAlertEventNormal(
               title,
               undefined,
               "info",
               html,
               undefined,
               undefined,
               'FINALIZAR',
               () => reLoadScreen(`${$appcfg_Dominio}ram.php?Consulta=true&RAM=` + ram));
// "https://satt.transporte.gob.hn:285/ram/ram.php?Consulta=true&RAM="+
            //***********************************************************************************/
            //*Lanzar iconos de celebración                                                     */
            //***********************************************************************************/
            startCelebration();
         }
      })
      .catch((error) => {
         console.error('Error regenerar.php/fRegenerar:', error);
         //*****************************************************************************************/
         //* INICIO: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
         //*****************************************************************************************/
         //*****************************************************************************************/
         //* FINAL: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
         //*****************************************************************************************/
         fSweetAlertEventSelect(
            "",
            "REGENERANDO RAM",
            "ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA",
            "warning"
         );
         return true;
      });
}
//******************************************************************************/
//* FINAL: funcion encargada de regenerar la resolucion y auto de ingreso
//******************************************************************************/
//******************************************************************************/
//* INICIO: funcion encargada de hacer la busqueda al hacer enter
//******************************************************************************/
document.getElementById('ram').addEventListener('keydown', function (event) {
   if (event.key === 'Enter') {
      buscarRAM();
   }
});
//******************************************************************************/
//* FINAL: funcion encargada de hacer la busqueda al hacer enter
//******************************************************************************/

//*********************************************************************************/
//* INICIO: funcion encargada de seleccionar el campo de la ram al poner el cursor
//*********************************************************************************/
document.getElementById('ram').addEventListener('click', () => {
   document.getElementById('ram').select();
});
//*********************************************************************************/
//* FINAL: funcion encargada de seleccionar el campo de la ram al poner el cursor
//*********************************************************************************/

//********************************************************/
//*INICIO: funcion encargada de limpiar todos los campos
//********************************************************/
function limpiar() {
   console.log('limpiando');
   // document.getElementById("idDatos").innerHTML = loadingIndicator;
   document.getElementById('ID_Resolucion').value = '';
   document.getElementById('ID_AutoAdmision').value = '';
   document.getElementById('nombreSolicitante').value = '';
   document.getElementById('nombreEmpresa').value = '';
   document.getElementById('rtn').value = '';
   // document.getElementById('Expediente_Estado').value = '';
   document.getElementById('Concesiones').innerHTML = '';
   document.getElementById('Tramites').innerHTML = '';
   document.getElementById('DESC_Estado_Expediente').value = '';
}
//********************************************************/
//*FINAL: funcion encargada de limpiar todos los campos
//********************************************************/