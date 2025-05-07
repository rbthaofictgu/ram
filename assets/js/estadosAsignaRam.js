
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

         const select = document.getElementById('usuarioSelect');
         data.forEach(usuario => {
            const option = document.createElement('option');
            option.value = usuario.Codigo_Usuario;
            option.textContent = usuario.Usuario_Nombre;
            select.appendChild(option);
         });
      }
   })
   .catch(error => {
      console.error('Error en fetch:', error);
   });

//******************************************************************************/
//*FINAL:Funcion encargada de hacer el fetch para la consulta de los empleados
//*****************************************************************************/


//******************************************************************************/
//*INICIO:Funcion encargada de hacer el fetch para la consulta de los estados
//*****************************************************************************/
fetch(`${$appcfg_Dominio}estados_sistema.php`)
   .then(response => {
      if (!response.ok) {
         throw new Error('Error en la respuesta del servidor');
      }
      return response.json();
   })
   .then(data => {
      if (data.error) {
         console.error('Error desde PHP estados_sistemas.php:', data.error);
      } else {
         console.log('Datos estado recibidos:', data);

         const select = document.getElementById('estadosSelect');

         for (const key in data) {
            if (Object.prototype.hasOwnProperty.call(data, key)) {
               const element = data[key];

               if(key=='datos'){
                  var datos=
                  element.map((estado) => {
                     return {
                        ID_Estado: estado.ID_Estado,
                        DESC_Estado: estado.DESC_Estado
                     };
                  });
                  Object(datos).forEach(estado => {
                     const option = document.createElement('option');
                     option.value = estado.ID_Estado;
                     option.textContent = estado.DESC_Estado;
                     select.appendChild(option);
                  });

               }
               
            }
         }
      }
   })
   .catch(error => {
      console.error('Error en fetch:', error);
   });

//******************************************************************************/
//*FINAL:Funcion encargada de hacer el fetch para la consulta de los estados
//*****************************************************************************/

