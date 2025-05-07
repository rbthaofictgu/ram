//**********VARIABLES GLOBALES**********************/
let dataGlobalTable = '';
let estaVacio = false;
//*************************************/
//*LLamado de la funcion al cargar
//************************************/
fetchUsuario();
//******************************************************************************/
//*INICIO:Funcion encargada de hacer el fetch para la consulta de los empleados
//*****************************************************************************/
function fetchUsuario(usuario = '') {
   // console.log(usuario, 'usuario');
   fetch(`${$appcfg_Dominio}/usuarios.php?`)
      .then(response => {
         if (!response.ok) {
            throw new Error('Error en la solicitud de usuario');
         }
         return response.json();
      })
      .then(dataEmpleado => {
         console.log(dataEmpleado, 'usuarios'); // Aquí trabajas con los datos obtenidos
         //!funcion autoCompletar encargada del select
         autoCompletar(dataEmpleado, usuario);
      })
      .catch(error => {
         console.error('Error fetch fetchUsuario=>usuarios:', error);
      });
}
//******************************************************************************/
//*FINAL:Funcion encargada de hacer el fetch para la consulta de los empleados
//*****************************************************************************/

//***********************************************************************************/
//* INICIO:funcion encargada de llenar el input y el autocompletado para seleccionar.
//***********************************************************************************/
function autoCompletar(dataEmpleado, usuario) {
   //*instanciando a los elementos del input y autocompletado
   const searchInput = document.getElementById("searchInput");
   //*iniciando el seleclt
   const autocompleteList = document.getElementById("autocomplete-list");

   //?nota:usuario lo paso si ya habia cargado un usuario anterior mente 
   //* Si ya existe un usuario,que se complete los check
   if (usuario) {
      //*a emp le pasamos los datos del empleado que coinciden con el usuario al hhacer la busqueda en el objeto empleado.
      const emp = dataEmpleado.find(emp => emp.usuario.toLowerCase() === usuario.toLowerCase());

      //*si emp existe
      if (emp) {
         //*asignamos datos del empleado
         asignarDatosEmpleado(emp, '', autocompleteList, searchInput);
      }
   }

   //*añadiendo al buscador el evento input
   searchInput.addEventListener("input", function () {
      //* qyuery opienen los datos del input
      const query = searchInput.value.toLowerCase();
      //* Limpiar las opciones previas
      autocompleteList.innerHTML = '';

      if (!query) {
         //*Si el campo está vacío, no mostrar nada
         return;
      }

      //* Recorrer los datos de empleados
      dataEmpleado.forEach(emp => {
         //*si existe por nombre o usuario
         if (emp.nombre_empleado.toLowerCase().includes(query) || emp.usuario.toLowerCase().includes(query)) {
            //*creamos un contenedor tipo div
            const div = document.createElement("div");
            //*con clase complete
            div.className = "complete";
            //*añadimos el texto a visualizar
            div.textContent = emp.nombre_empleado + " (" + emp.usuario + ")";

            //* Si el campo 'usuario' está vacío (caso normal), agregamos evento de clic
            div.addEventListener("click", function () {
               //*llamamos funcion para signar datos del empleado
               asignarDatosEmpleado(emp, query, autocompleteList, searchInput);
            });
            //*enviamos el div al contenedor principal
            autocompleteList.appendChild(div);
         }
      });
   });

   //* Cerrar la lista de autocompletado si el usuario hace clic fuera del input
   document.addEventListener("click", function (e) {
      if (e.target !== searchInput) {
         //*limpiamos el comple
         autocompleteList.innerHTML = '';
      }
   });
}
//***********************************************************************************/
//*FINAL:funcion encargada de llenar el input y el autocompletado para seleccionar.
//***********************************************************************************/

//**************************************************************/
//*INICIO: Funcion encargada de asignar los datos del empleado.
//**************************************************************/
function asignarDatosEmpleado(emp, query, autocompleteList, searchInput) {
   // console.log(emp, 'emp');
   //* Asignar el nombre al campo de texto
   searchInput.value = emp.nombre_empleado;
   searchInput.dataset.info = emp.usuario;
   searchInput.dataset.estado = emp.estados_relacionados;

   //* Para dividir y poner los estados en el arreglo
   const estadosUser = emp.estados_relacionados.split(',').map(e => e.trim());

   //* Limpiar los checkboxes
   let checkboxes = document.querySelectorAll('.form-check-input');
   checkboxes.forEach(checkbox => {
      checkbox.checked = false;
   });

   //* Llenar los checkboxes con los estados correspondientes
   estadosUser.forEach(element => {
      let est = element.replace(/\s+/g, '');  // Remover cualquier espacio extra
      if (est && typeof est === 'string' && est.trim() !== '') {
         //* Buscar checkbox por el id que coincide con el estado
         let check = document.getElementById(est);
         if (check) {
            //*si existe se marca
            check.checked = true;
         } else {
            console.error(`No se encontró el checkbox con id: ${est}`);
         }
      }
   });

   //* Limpiar las opciones del autocompletado al seleccionar un usuario
   autocompleteList.innerHTML = '';
}
//**************************************************************/
//*FINAL: Funcion encargada de asignar los datos del empleado.
//**************************************************************/

//*************************************************************/
//*INICIO:Fedtche encargado de traer los estados disponibles.
//*************************************************************/
fetch(`${$appcfg_Dominio}/estados_sistema.php?`)
   .then(response => {
      if (!response.ok) {
         throw new Error('Error en la solicitud de los estados_sistema');
      }
      return response.json();
   })
   .then(dataEstados => {
      console.log(dataEstados, 'dataestados'); // Aquí trabajas con los datos obtenidos
      //*funcion encargada de crear la tabla de los estados
      tableState(dataEstados)
   })
   .catch(error => {
      console.error('Error estados_sistema:', error);
   });
//*************************************************************/
//*FINAL:Fedtche encargado de traer los estados disponibles.
//*************************************************************/

//******************************************************************/
//* INICIO: funcion encargada de  mostrar los estados en la tabla.
//*****************************************************************/
function tableState(data) {
   //*varible que contiene los datos
   let info = data.datos;
   //*contenedor de la tabla
   let contenedor = document.getElementById('id_estados');

   contenedor.innerHTML +=
      ` <div class="row estado_tabla text-center">
            <h5><strong>ESTADOS DEL SISTEMA</strong></h5>
      </div>`

   //*recorriendo la data
   info.forEach((element, index) => {
      for (const key in element) {
         if (Object.prototype.hasOwnProperty.call(element, key)) {
            if (key == 'DESC_Estado') {
               const data = element[key];
               const idEstado = element['ID_Estado'];
               // console.log(data)
               contenedor.innerHTML += `<div class="row  border-bottom border-info">
                  <div class="col-2 text-center"> ${index + 1} -</div>
                  <div class="col-2 justify-content-center align-items-center d-flex"> 
                     <div class="form-check">
                     <input  class="form-check-input" type="checkbox"  onclick="checkClick(event)"  data="estado-${+index}" value="${idEstado}" id="${idEstado.replace(/\s+/g, '')}">
                     </div>
                  </div>
       
                  <div class="col-6">${data}</div>              
                  
               </div>`;
            }
         }
      }
   });
}

{/* <div class="col-2">
                  <!-- Checkbox -->
                  <div class="col-2">${index + 1} -</div>
                  <div class="form-check">
                     <input  class="form-check-input" type="checkbox"  onclick="checkClick(event)"  data="estado-${+index}" value="${idEstado}" id="${idEstado.replace(/\s+/g, '')}">
                  </div>
               </div>
                <div class="col-4 d-flex justify-content-between align-items-center">
                  <div> ${data}</div>
               </div> */}
//******************************************************************/
//* FINAL: funcion encargada de  mostrar los estados en la tabla.
//*****************************************************************/
// <!-- ${idEstado.replace(/\s+/g, '')}-->
//*************************************************************/
//*INICIO: funcion encargada de limpiar el input y los check
//************************************************************/
function Limpiar() {
   //*limpiando el input
   document.getElementById("searchInput").value = '';
   //*limpiando el data del input
   document.getElementById('searchInput').dataset.info = '';
   //*limpiando el data de los estados
   let estadoUser = document.getElementById('searchInput').dataset.estado;
   estadoUser = '';
   //*limpiando los check
   let checkboxes = document.querySelectorAll('.form-check-input');
   checkboxes.forEach(checkbox => {
      checkbox.checked = false;
   });
   //*limpiando objeto 
   info = {};
}
//*************************************************************/
//*FINAL: funcion encargada de limpiar el input y los check
//************************************************************/

//**********************************************************************************/
//*INICIO: funcion encargada de traer toda la data que sera insertada en la tabla.
//**********************************************************************************/
function asignarEstados() {
   //*instanciando los el input
   let usuario = document.getElementById('searchInput').value;
   //*el for data que contienen el usuario del sistema
   let dataInfoValue = document.getElementById('searchInput').dataset.info;
   //*los checbox
   let checkboxes = document.querySelectorAll('.form-check-input');
   let selectedData = [];

   //*verificamos que hay estados para el usuario selecionado
   if (document.getElementById('searchInput').dataset.estado != '') {
      console.log('si hay estados')
   }
   //*Recorre todos los checkboxes
   checkboxes.forEach(function (checkbox) {
      //*Si el checkbox está seleccionado
      if (checkbox.checked) {
         //*Obtén el valor del checkbox y el atributo data-info
         selectedData.push({
            estado: checkbox.value,
            //dataInfo: checkbox.dataset.info
         });
      }
   });

   //*data que se enviara
   let info = {
      "estado": selectedData,
      "empleado": usuario,
      "usuario": dataInfoValue,
   }

   // console.log(selectedData);
   //*recorremos objeto para verificar que tenga información
   for (const key in info) {
      if (Object.prototype.hasOwnProperty.call(info, key)) {
         const element = info[key];
         // console.log(element);
         if (element != '') {
            // console.log('tiene datos');
            //*si hay datos verificamos que se encuentre el usuario y los estados
            if (dataInfoValue != '' & selectedData != '') {
               //*si esta vacion
               estaVacio = false;
            }
         } else {
            //*si no hay datos  el estado cambia a ture y sale alerta
            estaVacio = true;
            console.log('NO TIENE DATOS');
         }
      }
   }

   //*evaluamos el estado si el false se envia la informacion
   if (estaVacio != true) {
      //*tiene datos se procede a enviar la informacion a la base de datos
      sendData(info, dataInfoValue);
   } else {
      //*removemnos cual quier alerta existente antes.
      setTimeoutAlert();
      //*asignam la tabla de estados con id =id_estados.
      dataGlobalTable = document.getElementById('id_estados').innerHTML;
      //*limpiamos el campo y asignamos alerta donde estaba la tabla.
      document.getElementById('id_estados').innerHTML = '';
      document.getElementById('id_estados').innerHTML +=
         `<div id='alertWarning' class="alert alert-warning alert-dismissible fade show" role="alert">
                  <strong><i class="fa-solid fa-triangle-exclamation"></i></strong> NECESITA TENER AL MENOS UN ESTADO SELECCIONADO Y UN USUARIO.
                  <button type="button" onclick="mostrar()" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
               </div>` + dataGlobalTable;
      //*removemos la alerta;
      setTimeoutAlert();
   }
}
//**********************************************************************************/
//*FINAL: funcion encargada de traer toda la data que sera insertada en la tabla.
//**********************************************************************************/

//****************************************************************************************************/
//*INICIO: funcion encargada de enviar la información al insert_estado_user.php para buscar en la BD
//****************************************************************************************************/
function sendData(info, dataInfoValue) {
   fetch(`${$appcfg_Dominio}/insert_estado_user.php?`, {
      method: 'POST',
      headers: {
         'Content-Type': 'application/json',
         'Authorization': 'Bearer token',
      },
      body: JSON.stringify(info),
   })
      .then(response => {
         if (!response.ok) {
            throw new Error('Error en la solicitud sendData');
         }
         return response.json();
      })
      .then(data => {
         // console.log('Respuesta:', data);
         //*si fue exitoso el seridor resoindera.
         if (data['success'] == 'Datos insertados y actualizados correctamente') {
            //*creamos dataGlobalTable de la tabla
            let dataGlobalTable = document.getElementById('id_estados').innerHTML
            //*limpiamos contenedor de la tabla.
            document.getElementById('id_estados').innerHTML = '';
            //*asignamos la alerta y la tabla.
            document.getElementById('id_estados').innerHTML =
               `<div id='alertSuccess' class="alert alert-success alert-dismissible fade show" role="alert">
                  <strong><i class="fa-solid fa-circle-check"></i></strong>  ESTADOS ASIGNADOS CORRECTAMENTE
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
               </div>`+ dataGlobalTable;
            //*eliminando alertas.
            setTimeoutAlert();
            // Limpiar();
            //*cargamos los usuario nuevamente
            fetchUsuario(dataInfoValue);
            //*ponemos estado el true.
            estaVacio = true;

         } else {
            //*creamos dataGlobalTable de la tabla
            dataGlobalTable = document.getElementById('id_estados').innerHTML;
            //*limpiamos contenedor de la tabla.
            document.getElementById('id_estados').innerHTML = '';
            //*asignamos la alerta y la tabla.
            document.getElementById('id_estados').innerHTML =
               `<div id='alertError' class="alert alert-danger alert-dismissible fade show" role="alert">
                  <strong><i class="fa-solid fa-bug"></i></strong> ERROR NO SE PUDO INSERTAR LOS ESTADO. INTENTE MAS TARDE Y SI PERSISTE COMUNIQUESE CON TECNOLOGIA
                  <button type="button"  onclick="mostrar()" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
               </div>`;

            //*eliminando alertas.
            setTimeoutAlert();
            //*limpiamos todo
            Limpiar();
            //*ponemos estado el true.
            estaVacio = true;
         }
      })
      .catch(error => {
         console.error('Error:', error);
      });
}
//****************************************************************************************************/
//*FINAL: funcion encargada de enviar la información al insert_estado_user.php para buscar en la BD
//****************************************************************************************************/

//******************************************************************************/
//*INICIO: funcion encargada de eliminar las alertas despues de cirto tiempó.
//******************************************************************************/
function setTimeoutAlert() {
   //*intanciando las laertas
   const alerts = document.querySelectorAll('.alert');
   //* Establecer un setTimeout para cada alerta
   alerts.forEach(alert => {
      setTimeout(() => {
         alert.remove(); //* Eliminar la alerta después de 3 segundos
      }, 3000);
   });
}
//******************************************************************************/
//*FINAL: funcion encargada de eliminar las alertas despues de cirto tiempó.
//******************************************************************************/

//****************************************************************************************/
//*INICIO: para que se muestre la tabla cuando quite el mensaje de error de seleccion.
//****************************************************************************************/
function mostrar(data = dataGlobalTable) {
   //*instanciando las alertas
   const alerts = document.querySelectorAll('.alert');
   //* si el contenedor de la tabla esta lleno
   if (document.getElementById('id_estados') != '') {
      //*y existe una alerta solo removemos la alerat
      if (alerts) {
         alerts.remove();
      }
      //*si el contenedor esta vacio
   } else {
      //*y existe una alerta solo removemos la alerat
      if (alerts) {
         alerts.remove();
      }
      //*y añadimos la tabla.
      document.getElementById('id_estados').innerHTML = data;
   }
}
//****************************************************************************************/
//*FINAL: para que se muestre la tabla cuando quite el mensaje de error de seleccion.
//****************************************************************************************/

//*******************************************************/
//* INICIO: funcion encargada de chequear los chekbox
//*******************************************************/
function checkClick(event) {
   //*intsnaciamos.
   const checkbox = event.target;
   console.log(checkbox);
   //*estamarcado?
   if (!checkbox.checked) {
      //*envaimos alerta
      Swal.fire({
         title: '¿Estás seguro de quitar el estado al usuario?',
         showCancelButton: true,
         confirmButtonText: '¡Desmarcar!',
         cancelButtonText: '¡Cancelar!',
         icon: 'warning',
         customClass: {
            confirmButton: 'swal2-confirm',
            cancelButton: 'swal2-cancel'
         }
      }).then((result) => {
         if (result.isConfirmed) {
            //* Si el usuario confirma desmarcar
            checkbox.checked = false;
            //*y llamos la funcion para actualizar los estados del checkbox en la bd.
            updateCheck(checkbox);
         } else {
            //* Si el usuario cancela mantener marcado
            checkbox.checked = true;
         }
      });
   }
}
//*******************************************************/
//* FINAL: funcion encargada de chequear los chekbox
//*******************************************************/

//*****************************************************/
//*INICIO: funcion encargada de modifcar el checkbox
//****************************************************/
function updateCheck(checkbox) {
   let dataInfoValue = document.getElementById('searchInput').dataset.info;
   let check = checkbox.value;
   // console.log(dataInfoValue, check);

   let info = {
      "estado": check,
      "usuario": dataInfoValue,
   }

   fetch(`${$appcfg_Dominio}/update_estado_user.php?`, {
      method: 'POST',
      headers: {
         'Content-Type': 'application/json',
         'Authorization': 'Bearer token',
      },
      body: JSON.stringify(info),
   })
      .then(response => {
         if (!response.ok) {
            throw new Error('Error en la solicitud update_estado_user');
         }
         return response.json();

      })
      .then(data => {
         // console.log('Respuesta:', data);
         if (data['success'] == 'actualizados correctamente') {
            let dataGlobalTable = document.getElementById('id_estados').innerHTML;
            document.getElementById('id_estados').innerHTML = '';
            document.getElementById('id_estados').innerHTML =
               `<div id='alertSuccess' class="alert alert-success alert-dismissible fade show" role="alert">
                  <strong><i class="fa-solid fa-circle-check"></i></strong> ESTADOS ACTUALIZADOS CORRECTAMENTE
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
               </div>`+ dataGlobalTable;
            // Limpiar();
            setTimeoutAlert();
            //*LLAMAMOS LA FUNCION DE LOS USUARIOS OTRA VEZ
            fetchUsuario(dataInfoValue);
         }
      })
      .catch(error => {
         console.error('Error:', error);
      });
}
//*****************************************************/
//*FINAL: funcion encargada de modifcar el checkbox
//****************************************************/