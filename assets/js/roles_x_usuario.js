let usuarioSeleccionado = '';
let rolesCopia = '';
document.addEventListener('DOMContentLoaded', function () {
   const searchInput = document.getElementById('searchInput');
   const autocompleteList = document.getElementById('autocomplete-list');


   const appcfg_Dominio = window.$appcfg_Dominio || '';

   searchInput.addEventListener('input', function () {
      //** Limpiar la lista de autocompletado y espacios **/
      const term = searchInput.value.trim().toUpperCase();

      if (term === '') {
         autocompleteList.innerHTML = '';
         return;
      }

      fetch(`${appcfg_Dominio}usuariosNuevo.php`, {
         method: 'POST',
         headers: {
            'Content-Type': 'application/json'
         },
         body: JSON.stringify({ term })
      })
         .then(response => response.json())
         .then(data => {
            // console.log('Datos recibidos:', data); // Verifica los datos recibidos
            autocompleteList.innerHTML = '';

            if (data.error) return;
            //** Aseguramos que sea un array antes de usarlo **/
            data.forEach(usuario => {
               //** Crear un elemento para cada usuario encontrado **/
               const item = document.createElement('div');
               item.classList.add('autocomplete-item');
               item.textContent = usuario.nombre_empleado + " (" + (usuario.nombre_usuario).toUpperCase() + ")"; // Lo que se muestra
               item.dataset.usuario = usuario.nombre_usuario; // Lo que se guarda
               //** Agregar un evento de clic al elemento **/
               item.addEventListener('click', () => {
                  searchInput.value = usuario.nombre_empleado + " (" + (usuario.nombre_usuario).toUpperCase() + ")"; // Mostrar en el input
                  autocompleteList.innerHTML = '';
                  usuarioSeleccionado = (usuario.nombre_usuario || usuario.nombre_empleado); // Guardar solo el username real
                  obtenerRolesUser(appcfg_Dominio, usuarioSeleccionado);
               });
               //** Agregar el elemento a la lista de autocompletado **/
               autocompleteList.appendChild(item);

            });
         })
         .catch(error => {
            console.error('Error al buscar:', error);
         });
   });

   document.addEventListener('click', function (e) {
      if (e.target !== searchInput) {
         autocompleteList.innerHTML = '';
      }
   });

   obtenerRoles(appcfg_Dominio);
});

//**************************************************************************/
//*INICIO: funcion encargada de obtener los roles del sistema */
//**************************************************************************/
function obtenerRoles(dominio) {
   fetch(`${dominio}roles_select.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' }
   })
      .then(response => response.json())
      .then(data => {
         //* Verificar si la respuesta es exitosa **/
         if (data.success) {
            //** Llamar a la función para mostrar los roles en la tabla **/
            mostrarRolesEnTabla(data.data);
         } else {
            console.error('Error:', data.error);
         }
      })
      .catch(error => {
         console.error('Error en la solicitud:', error);
      });
}

function limpiar() {
   document.getElementById('searchInput').value = '';

   const select = document.getElementById('searchInput');
   select.selectedIndex = 0; // Esto selecciona la primera opción

   let checkboxes = document.querySelectorAll('.form-check-input');
   checkboxes.forEach(function (checkbox) {
      console.log('hola');
      checkbox.checked = false;
   });

}
//**************************************************************************/
//*FINAL: funcion encargada de obtener los roles del sistema */
//**************************************************************************/

//**************************************************************************/
//*INICIO: funcion encargada de mostrar los roles del sistema en una tabla */
//**************************************************************************/
function mostrarRolesEnTabla(roles) {
   rolesCopia = roles;
   console.log('Roles:', roles); // Verifica los roles recibidos
   //*seleccionar  el tbody de la tabla para agregar los roles*/
   const tbody = document.querySelector('#tablaRoles tbody');
   //*Limpiar el contenido previo del tbody
   tbody.innerHTML = '';

   roles.forEach((rol, index) => {
      const fila = document.createElement('tr');
      const colNum = document.createElement('td');
      colNum.textContent = index + 1;

      //** Crear la celda del checkbox **/
      const colCheck = document.createElement('td');
      colCheck.className = 'text-center'; // Alineación centrada
      colCheck
      const checkbox = document.createElement('input');
      checkbox.type = 'checkbox';
      checkbox.id = `rol_${rol.id}`;
      checkbox.className = 'form-check-input';
      checkbox.value = rol.id;
      //*inabilitando los check de los roles que no estan activos en el sistema
      if (rol.estaActivo == '0') {
         checkbox.disabled = true;

      }

      // Confirmar al hacer clic en el checkbox
      checkbox.addEventListener('change', function () {
         if (!usuarioSeleccionado) {
            Swal.fire({
               icon: 'warning',
               title: 'USUARIO NO SELECCIONADO',
               text: 'DEBES SELECCIONAR UN USURIO PRIMERO .',
            });
            //* Si no hay usuario seleccionado, revertir el estado del checkbox
            checkbox.checked = !checkbox.checked;
            //* Salir de la función
            return;
         }
         //** Si hay usuario seleccionado, mostrar la alerta de confirmación **/
         const accion = checkbox.checked ? 'asignar o activar' : 'desactivar';
         //** Mostrar la alerta de confirmación **/
         Swal.fire({
            title: `¿Deseas ${accion} el rol "${rol.codigo}" al usuario "${usuarioSeleccionado}"?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí',
            cancelButtonText: 'No'
         }).then((result) => {
            if (result.isConfirmed) {
               //** Si el usuario confirma, proceder a insertar o actualizar el rol **/
               insertarActualizarRolIndividual($appcfg_Dominio, rol.id, checkbox.checked);
            } else {
               //** Si el usuario cancela, revertir el estado del checkbox **/
               checkbox.checked = !checkbox.checked;
            }
         });
      });
      //** Agregar el checkbox a la celda **/
      colCheck.appendChild(checkbox);
      //** Agregar la celda del checkbox a la fila **/
      const colCodigo = document.createElement('td');
      colCodigo.className = 'text-start'; // Alineación centrada
      colCodigo.textContent = rol.codigo;
      //** Agregar la celda del código a la fila **/
      const colDescripcion = document.createElement('td');
      colDescripcion.className = 'text-start'; // Alineación centrada
      colDescripcion.textContent = rol.descripcion;
      //** Agregar laS celdaS de la descripción a la fila **/
      fila.appendChild(colNum);
      fila.appendChild(colCheck);
      fila.appendChild(colCodigo);
      fila.appendChild(colDescripcion);
      //** Agregar la fila al tbody **/
      tbody.appendChild(fila);
   });
}
//**************************************************************************/
//*FINAL: funcion encargada de mostrar los roles del sistema en una tabla */
//**************************************************************************/

//**************************************************************************/
//*INICIO: funcion encargada de obtener los roles asignados a un usuario */
//**************************************************************************/

function obtenerRolesUser(dominio, nombreUsuario) {
   fetch(`${dominio}rolesUser_select.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ usuario: nombreUsuario })
   })
      .then(response => response.json())
      .then(data => {
         console.log('Datos recibidos:', data); // Verifica los datos recibidos
         //** Limpiar el contenido previo del tbody **/
         const alerta = document.getElementById('alertaSinDatos');

         const checkboxes = document.querySelectorAll('#tablaRoles tbody input[type="checkbox"]');
         //** Iterar sobre los checkboxes y marcar los que están asignados **/
         checkboxes.forEach(checkbox => {
            //** Obtener el valor del checkbox (ID del rol) **/
            // const roleId = parseInt(checkbox.value);
            //** Verificar si el ID del rol está en la lista de roles asignados **/
            checkbox.checked = false;
         });


         alerta.classList.add('d-none');
         //** Verificar si la respuesta es exitosa **/
         if (data.success) {
            //*Aseguramos que sea un array antes de usarlo
            const roles = Array.isArray(data.data) ? data.data : [];
            // console.log('Roles asignados:', roles);
            //** Llamar a la función para marcar los checkboxes **/
            marcarCheckboxes(roles);
         } else if (data.error === "No se encontró ningún registro.") {
            //** Si no se encontraron roles, mostrar la alerta **/
            alerta.classList.remove('d-none');
            setTimeout(() => {
               alerta.classList.add('d-none');
            }, 3000);
         } else {
            console.error('Error:', data.error);
         }
      })

      .catch(error => {
         console.error('Error al buscar:', error);
      });
}
//**************************************************************************/
//*FINAL: funcion encargada de obtener los roles asignados a un usuario */
//**************************************************************************/

//**************************************************************************/
//*INICIO: funcion encargada de marcar los checkboxes de los roles asignados */
//**************************************************************************/
function marcarCheckboxes(idsRolesAsignados) {
   //** Aseguramos que sea un array antes de usarlo **/
   if (!Array.isArray(idsRolesAsignados)) {
      console.error('Error: idsRolesAsignados no es un array', idsRolesAsignados);
      return;
   }
   //** Limpiar el contenido previo del tbody **/
   const checkboxes = document.querySelectorAll('#tablaRoles tbody input[type="checkbox"]');
   //** Iterar sobre los checkboxes y marcar los que están asignados **/
   checkboxes.forEach(checkbox => {
      //** Obtener el valor del checkbox (ID del rol) **/
      const roleId = parseInt(checkbox.value);
      //** Verificar si el ID del rol está en la lista de roles asignados **/
      checkbox.checked = idsRolesAsignados.includes(roleId);
   });
}
//**************************************************************************/
//*FINAL: funcion encargada de marcar los checkboxes de los roles asignados */
//**************************************************************************/

//**************************************************************************/
//*INICIO: funcion encargada de insertar o actualizar un rol individual */
//**************************************************************************/
function insertarActualizarRolIndividual(dominio, role_id, estaActivo) {
   //** Verificar si el usuario está seleccionado **/
   fetch(`${dominio}rolesUser_insertUpdate.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
         role_id: role_id,
         name_user: usuarioSeleccionado,
         estaActivo: estaActivo ? 1 : 0 // Convertir a 1 o 0
      })
   })
      .then(response => response.json())
      .then(data => {
         //** Verificar si la respuesta es exitosa **/
         if (data.success) {
            Swal.fire({
               icon: 'success',
               title: 'Éxito',
               text: data.success,
               timer: 2000,
               showConfirmButton: false
            });
         } else {
            //** Si hubo un error, mostrar la alerta **/
            Swal.fire({
               icon: 'error',
               title: 'Error al guardar',
               text: data.error || 'ERROR INESPERADO AL CARGAR LOS ROLES.'
            });
         }
      })
      .catch(error => {
         console.error('Error en la solicitud:', error);
         Swal.fire({
            icon: 'error',
            title: 'ERROR DE RED',
            text: 'NO SE PUEDE CONECTAR CON EL SERVIDOR.'
         });
      });
}
//**************************************************************************/
//*FINAL: funcion encargada de insertar o actualizar un rol individual */
//**************************************************************************/
