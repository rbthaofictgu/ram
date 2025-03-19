//*Contenedor de los botones
const container = document.getElementById('buttonContainer');

//****************************************************************************/
//* INICIO: Función para cargar los botones de los estados dinámicamente
//****************************************************************************/

function cargarBotones() {

   fetch(`${$appcfg_Dominio}/estados_sistemas_por_user.php`)
      .then(response => response.json())
      .then(data => {
         // console.log(data);
         if (data.error) {
            alert('error respuesta');
            container.innerHTML = `<p>${data.error}</p>`;
         } else {

            let idEstadoArray = data.datos.map(item => [item.ID_Estado, item.DESC_Estado]);
            container.setAttribute('data-info', JSON.stringify(idEstadoArray));

            data.datos.forEach((item, index) => {
               //*Creando elemento en el DOM
               const button = document.createElement('button');
               //*Añadiendo las clases
               button.classList.add('btn', 'btn-light', 'me-2', 'btn-sm', 'btn-custom-secondary');
               //* creando el indice del btn
               // button.id = `estado_${item.ID}`;
               button.id = `estado`;
               //*creando un data
               button.setAttribute('data-id', item.ID_Estado);
               button.setAttribute('data-descrip', item.DESC_Estado);

               //*crear enevento onclick que llama a la funcion vista_Data que crea la tabla dinamica
               button.addEventListener('click', () => {
                  let estado = (item.ID_Estado ? item.ID_Estado : '');
                  let descripcion = (item.DESC_Estado ? item.DESC_Estado : '');
                  vista_data(estado, descripcion,num=1);
                  actualizarEstado(estado, descripcion);
               });

               //*Colocando el texto del btn
               button.textContent = item.DESC_Estado;
               //Agragando btn al contenedor
               container.appendChild(button);

            });

         }
      })
      .catch(error => {
         //*manejando el error
         let title = 'WARNING';
         let msg = `Error al cargar los botones: ${error}`
         let type = "error";
         fSweetAlertEventNormal(title, msg, type, errorcode = 'NO SE CARGARON LOS BOTONES DE ESTADOS COMPRUEBE SI TIENE ASIGNADO UN ESTADO EN EL SISTEMA');
         console.log(error);
         // container.innerHTML = `<p>Error al cargar los botones: ${error}</p>`;
      });
}
//****************************************************************************/
//*FINAL: Función para cargar los botones de los estados dinámicamente
//****************************************************************************/

//! Llamada a la función para cargar los botones al cargar la página
document.addEventListener('DOMContentLoaded', cargarBotones);


