//*Contenedor de los botones
const container = document.getElementById('buttonContainer');
var esConsulta = document.getElementById('esConsulta').value;

//****************************************************************************/
//* INICIO: Función para cargar los botones de los estados dinámicamente
//****************************************************************************/
// console.log('es consulta************', esConsulta);
var url = ``;
if (esConsulta != true) {
   url = `${$appcfg_Dominio}/estados_sistemas_por_user.php`
} else {
   url = `${$appcfg_Dominio}/estados_sistema.php`

}

function cargarBotones() {
   fetch(`${url}`, {
      method: 'GET',
      headers: {
         'Authorization': 'Bearer token',
      },
   }).then(response => response.json())
      .then(data => {
         if (data.error) {
            if (data.error == 1100) {
               fSweetAlertEventNormal(
                  data.errorhead,
                  '',
                  'error',
                  data.error + "- " + data.errormsg,
                  undefined,
                  undefined,
                  undefined,
                  openModalLogin,
               );
            } else {
               fSweetAlertEventNormal(
                  data.errorhead,
                  '',
                  'error',
                  data.error + "- " + data.errormsg,
                  undefined,
                  undefined,
                  undefined
               );
            }
            console.log(data.errorreason);
         } else {
            //*consulta si el objeto data tiene la propiedad datos

            //* Si no es consulta, se cargan los botones de estados */
            let idEstadoArray = data.datos.map(item => [item.ID_Estado, item.DESC_Estado, item.puede_agregar]);
            if (esConsulta == true) {
               idEstadoArray.unshift(['*TODOS', '*TODOS', 0]);

               container.setAttribute('data-info', JSON.stringify(idEstadoArray));
            } else {
               container.setAttribute('data-info', JSON.stringify(idEstadoArray));
            }

            if (esConsulta == true) {
               const button = document.createElement('button');
               button.classList.add('btn', 'btn-light', 'me-2', 'btn-sm', 'btn-custom-secondary', 'mb-2');
               button.id = `estado_*TODOS`;
               button.setAttribute('data-id', '0');
               button.setAttribute('data-descrip', '*TODOS');
               button.setAttribute('data-puedeagregar', '0');
               button.addEventListener('click', () => {
                  let estado = '*TODOS';
                  let descripcion = '*TODOS';
                  let agregar = 0;
                  vista_data(estado, descripcion, agregar, num = 1,);
                  actualizarEstado(estado, descripcion, agregar);
                  activar(estado);
                  //*Colocando el texto del btn
               });
               //*Colocando el texto del btn
               button.textContent = '*TODOS'
               //Agragando btn al contenedor
               container.appendChild(button);
            }
            data.datos.forEach((item, index) => {
               //*Creando elemento en el DOM
               const button = document.createElement('button');
               //*Añadiendo las clases
               button.classList.add('btn', 'btn-light', 'me-2', 'btn-sm', 'btn-custom-secondary', 'mb-2');
               //* creando el indice del btn
               button.id = `estado_${item.ID_Estado}`;
               // button.id = `estado`;
               //*creando un data
               button.setAttribute('data-id', item.ID_Estado);
               button.setAttribute('data-descrip', item.DESC_Estado);
               button.setAttribute('data-puedeagregar', item.puede_agregar);
               //*crear enevento onclick que llama a la funcion vista_Data que crea la tabla dinamica
               button.addEventListener('click', () => {
                  let estado = (item.ID_Estado ? item.ID_Estado : '');
                  let descripcion = (item.DESC_Estado ? item.DESC_Estado : '');
                  let agregar = (item.puede_agregar ? item.puede_agregar : '');
                  vista_data(estado, descripcion, agregar, num = 1,);
                  actualizarEstado(estado, descripcion, agregar);
                  activar(estado);
               });

               if (item.DESC_Estado == 'EN PROCESO') {
                  button.innerHTML = item.DESC_Estado +
                     `<span id='total' class="total"><strong> (${item.TOTALES_ESTADOS}</strong></span><strong>/</strong>
                      <span id='pagadas' class="pagadas"><strong>${item.TOTAL_PAGADAS})</strong></span>`;
               } else {
                  button.innerHTML = item.DESC_Estado +
                     `<span id='total' class="total"><strong> (${item.TOTALES_ESTADOS})</strong></span>`;
               }

               //Agragando btn al contenedor
               //*PARA CUANDO EXISTE CONSULTA SE MUESTRE EL BOTON DE TODOS 

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

function montoFormateado(cantidad) {
   console.log('cantidad', cantidad);
   let monto = cantidad;
   let montoFormateado = monto.toLocaleString('es-HN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
   return montoFormateado;
}
//****************************************************************************/
//*FINAL: Función para cargar los botones de los estados dinámicamente
//****************************************************************************/
//! Llamada a la función para cargar los botones al cargar la página
document.addEventListener('DOMContentLoaded', cargarBotones);

