let usuarioSeleccionado = '';

document.addEventListener('DOMContentLoaded', function () {
    obtenerRoles();
    //* Obtener el elemento del botón "Guardar" y agregar el evento de clic
    const btnGuardar = document.getElementById('btnGuardar');
    if (btnGuardar) {
        //* Agregar el evento de clic al botón "Guardar"
        btnGuardar.addEventListener('click', enviarDatos);
    }
});
//*************************************/
//* INICIO: obtener roles y cargarlos
//*************************************/
function obtenerRoles() {
    fetch(`${$appcfg_Dominio}roles_select.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        //* Verificamos si la respuesta es exitosa y contiene datos
        if (data.success) {
            //* Llamamos a la función para mostrar los roles en la tabla
            mostrarRolesEnTabla(data.data);
        } else {
            console.error('Error:', data.error);
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
    });
}
//*************************************/
//* FINAL: obtener roles y cargarlos
//**************************************/

//*************************************/
//* INICIO: enviar nuevo rol
//*************************************/
function enviarDatos() {
    //*obtenemos datos para enviar al fetch
    const codigo = document.getElementById('idCodigo').value.toUpperCase();
    const descripcion = document.getElementById('idDescripcion').value.toUpperCase();
    const estado = document.getElementById('estadoCheck').checked ? "1" : "0"; //**validamos que los campos no esten vacios

    //**validamos que los campos no esten vacios
    const data = {
        codigo: codigo,
        descripcion: descripcion,
        estado: estado
    };

    fetch(`${$appcfg_Dominio}roles_insertUpdate.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(resultado => {
            //**verificamos si la respuesta es exitosa y contiene datos
            if (resultado.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: resultado.success,
                    timer: 2000,
                    showConfirmButton: false
                });
                //* Limpiamos los campos de entrada
                obtenerRoles(); // Recargar la tabla
            } else if (resultado.error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: resultado.error
                });
            }
        })
        .catch(error => {
            console.error('Error en la petición:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error de red',
                text: 'No se pudo conectar con el servidor.'
            });
        });
}
//*************************************/
//* FINAL: enviar nuevo rol
//*************************************/

//***************************************/
//* INICIO: mostrar roles en la tabla
//***************************************/
function mostrarRolesEnTabla(roles) {
    const tbody = document.querySelector('#tablaRoles tbody');
    console.log(tbody,'tbody');
    //* Limpiar el contenido actual de la tabla
    tbody.innerHTML = '';
    //* Iterar sobre los roles y crear filas en la tabla
    roles.forEach((rol, index) => {
        //* Crear una fila para cada rol
        const fila = document.createElement('tr');
        const colNum = document.createElement('td');
        colNum.textContent = index + 1;
        //** Crear una celda para el checkbox
        const colCheck = document.createElement('td');
        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.id = `rol_${rol.id}`;
        checkbox.setAttribute('data-estaactivo', rol.estaActivo);
        checkbox.className = 'form-check-input text-start';
        checkbox.value = rol.id;
        checkbox.checked = rol.estaActivo == 1;
        //** Agregar evento de cambio al checkbox
        checkbox.addEventListener('change', function () {
            //** Verificar si el checkbox está marcado o desmarcado
            const accion = checkbox.checked ? 'activar' : 'desactivar';
            //** Mostrar un mensaje de confirmación antes de cambiar el estado
            Swal.fire({
                title: `¿DESEAS ${(accion).toUpperCase()} EL ROL "${(rol.codigo).toUpperCase()}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí',
                cancelButtonText: 'No'
            }).then((result) => {
                //** Si el usuario confirma la acción
                if (result.isConfirmed) {
                    if (usuarioSeleccionado) {
                        //** Si hay un usuario seleccionado, llamamos a la función para insertar o actualizar el rol
                        insertarActualizarRolIndividual($appcfg_Dominio, rol, checkbox.checked);
                    } else {
                        fetch(`${$appcfg_Dominio}roles_insertUpdate.php`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                id: rol.id,
                                estado: checkbox.checked ? "1" : "0",
                                codigo: rol.codigo,
                                descripcion: rol.descripcion
                            })
                        })
                            .then(response => response.json())
                            .then(data => {
                                //** Verificamos si la respuesta es exitosa y contiene datos
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'ESTADO ACTUALIZADO',
                                        text: data.success,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                    //** Recargar la tabla de roles
                                    obtenerRoles();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: data.error || 'NO SE PUEDE ACTUALIZAR EL ESTADO DEL ROL.'
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
                } else {
                    checkbox.checked = !checkbox.checked;
                }
            });
        });
        //** Agregar el checkbox a la celda
        colCheck.appendChild(checkbox);
        //** Agregar la celda a la fila
        const colCodigo = document.createElement('td');
        colCodigo.className = 'text-start';
        colCodigo.textContent = rol.codigo;
        //** Agregar la celda a la fila
        const colDescripcion = document.createElement('td');
        colDescripcion.className = 'text-start';
        colDescripcion.textContent = rol.descripcion;
        //** Agregar la celda a la fila
        fila.appendChild(colNum);
        fila.appendChild(colCheck);
        fila.appendChild(colCodigo);
        fila.appendChild(colDescripcion);
        //** Agregar la fila al cuerpo de la tabla
        tbody.appendChild(fila);
    });
}
//***************************************/
//* FINAL: mostrar roles en la tabla
//***************************************/

//*****************************************************/
//*INICIO: insertar o actualizar el rol para un usuario
//*****************************************************/
function insertarActualizarRolIndividual(dominio, rol, estaActivo) {
    //** inicializando datos para enviar al fetch
    const data = {
        id: rol.id,
        estado: estaActivo ? "1" : "0",
        codigo: rol.codigo,
        descripcion: rol.descripcion
    };

    //** Verificamos si hay un usuario seleccionado
    const endpoint = usuarioSeleccionado
        ? `${dominio}rolesUser_insertUpdate.php`
        : `${dominio}roles_insertUpdate.php`;

    //** Si hay un usuario seleccionado, agregamos el nombre de usuario y el ID del rol al objeto data
    if (usuarioSeleccionado) {
        data.name_user = usuarioSeleccionado;
        data.role_id = rol.id;
    }

    fetch(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(data => {
            //** Verificamos si la respuesta es exitosa y contiene datos
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: data.success,
                    timer: 2000,
                    showConfirmButton: false
                });
                //** Limpiamos los campos de entrada
                obtenerRoles(); // Recargar
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'ERROR AL GUARDAR',
                    text: data.error || ' ERROR INESPERADO AL GUARDAR EL ROL.'
                });
            }
        })
        .catch(error => {
            console.error('Error en la solicitud:', error);
            Swal.fire({
                icon: 'error',
                title: 'ERROR DE RED.',
                text: 'NO SE PUDO CONECTAR CON EL SERVIDOR.'
            });
        });
}
//*****************************************************/
//*FINAL: insertar o actualizar el rol para un usuario
//*****************************************************/

