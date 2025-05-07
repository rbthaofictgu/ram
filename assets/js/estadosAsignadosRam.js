
//******************************************************************************/
//*INICIO:Funcion encargada de hacer el fetch para la consulta de los empleados
//*****************************************************************************/
fetch(`${$appcfg_Dominio}query_asignaUsuario.php`) 
   .then(response => {
      if (!response.ok) {
         throw new Error('Error en la respuesta del servidor');
      }
      return response.json();
   })
   .then(data => {
      if (data.error) {
         console.error('Error desde PHP query_asignaUsuario.php:', data.error);
      } else {
         console.log('Datos recibidos:', data);
         
         
      }
   })
   .catch(error => {
      console.error('Error en fetch:', error);
   });

//******************************************************************************/
//*FINAL:Funcion encargada de hacer el fetch para la consulta de los empleados
//*****************************************************************************/

