// import {dictamen} from 'dictamen.js';
var usuario = document.getElementById("ID_Usuario");

if (usuario) {
   var userId = document.getElementById("ID_Usuario")?.value || '';
   var setUnSetEvent1 = localStorage.getItem('setUnSetEvent' + userId);
   setUnSetEvent1 = JSON.parse(setUnSetEvent1); //
   if (setUnSetEvent1 == null) {
      localStorage.setItem('setUnSetEvent' + document.getElementById("ID_Usuario").value, JSON.stringify({ 'setUnSetEvent': 'add' }));
   }
}

// document.addEventListener("DOMContentLoaded", function () {
//* Página actual, comenzamos en la página 1
var currentPage = 1;
//* Número de filas por página 
var rowsPerPage = 10;
var pageNumerClick = 0;
//* Variable global para almacenar las filas de los tramites
var filasTramites = [];
var crearFilaTramites = [];
var getBtndesabilitado = '';
//*para almacenar el tamaño de los tramites
var tamaño_Tramite = 0;
//*se almacenan el arreglo de los id de cada una de las filas de localStorage.
var arregloFilaOculta = [];
//*obtiene los datos de la tabla para utilizarlos en la funcion
//*mostrarAgregar_fila() que se encarga de desplegar los tramites si las filas de estos estan almacenadas en localstorage
var datosMostra = {}
//*contiene el arreglo de los id almacendos en localstoreage de las filas ocultas
var getFilaOculta = [];
var vistaHabilitada = false;
//*Guardamos  una copia de todas las concesiones almacenadas antes de ser eliminadas para comprara con las que quedaron despiues de eiliminar.
var ConcesionNumberAntesEliminar = [];
//*creamos una copia del contenedor de los botones para poder utilizarlo en la funcion btnVerTramites().
var copiDivClear = '';
var suma = 0;
//*definiendo los roles permitidos
var rol = [7, 9];
var modulo = '';
//*rol que tiene el usuario logiado.
var rolUser = [7];
//*variable para obtener el ram en que se esta trabajando.
var ramElement = document.getElementById("RAM");
var ram = ramElement ? ramElement.value ?? 'RAM' : 'RAM';

//*creamos una copia de el contenedor de las filagas agregadas para poder utilizar en otra funcion.
var divAgregar = '';
var arregloDiv = [];
var btnVer = false;
//*titulos variables 
var lengTramites = 0;
//*estados para mostarar cambio de unidad link
const estadosValidosModificacionUnidad = ['IDE-1', 'IDE-2'];
//*estados para mostrar las constancias.
const estadosValidosConstancia = ['IDE-1', 'IDE-2'];
//*estados paraa visualizar certificado y permiso de explotacion
const estadosValidos = ['IDE-1', 'IDE-2', 'IDE-5'];
//*contiene el titulo del modal.
var tituloGlobal = '';
//*se excluyen los encabezados de la tabla de objeto.
const encabezadoExclui = new Set(['Tramites', 'Unidad', 'Unidad1', 'ID_Memo', 'Concesion_Encriptada', 'esCarga', 'esCertificado', 'Permiso_Explotacion_Encriptado']);
//*para saber el estado de la RAM actual.

var estadoRam = document.getElementById("ID_Estado_RAM").value;
// var estadoRam=document.getElementById("idEstado").value;
// if (estadoRam) {
//    console.log('Estado RAM si existe:', estadoRam);
// } else {
//    console.log('Estado RAM noooo existe:', estadoRam);
// }

//***********************************************************************/
//*INICIO:funcion encargada de formatear el monto a mostrar
//***********************************************************************/
function montoFormateado(cantidad) {
   // console.log('cantidad', cantidad);
   let monto = cantidad;
   let montoFormateado = monto.toLocaleString('es-HN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
   return montoFormateado;
}
//***********************************************************************/
//*FINAL:funcion encargada de formatear el monto a mostrar
//***********************************************************************/

//*************************************************************/
//*INICIO:Funcion encargada de filtrar la data de ConcesionNumber;
//*************************************************************/
function filtrarDataConcesionNumber() {
   //*que filtre los elementos que sean distintos de false , blanco y null.
   let resultado = concesionNumber.filter(item => item !== false && item !== "" && item !== null);
   //*creamos arreglo con la informacion a exclir
   let dataExcluir = ['ID_Formulario_Solicitud', 'ID_Expediente', 'ID_Resolucion', 'ID_Solicitud', 'CodigoAvisoCobro'];

   const filtrarData = resultado.map(item => {
      //*creo una copia de de los elementos
      if (item != false && item != 'undefined') {
         const filtrarItem = { ...item };
         dataExcluir.forEach(field => {
            //*eliminar campo que sean iguales a los de dataExcluir.
            delete filtrarItem[field];
         });
         //*retorna el arreglo con los elemento eliminados
         return filtrarItem;
      }
   });
   return filtrarData;
}
//*************************************************************/
//*FINAL:Funcion encargada de filtrar la data de ConcesionNumber;
//*************************************************************/

//****************************************************************************************************/
//*INICIO: funcion encargada de eliminar o poner el efecto de zoom en las placas y las concesiones.
//****************************************************************************************************/
function setUnSetEvent(event, events, accion, handles, prefijo) {
   let limite = concesionNumber.length;
   for (let i = 0; i < limite; i++) {
      if (prefijo == 'idRow') {
         var obj = document.getElementById(prefijo + i + '_placa_0');
         var obj1 = document.getElementById(prefijo + i + '_placa_1');
      } else {
         var obj = document.getElementById(prefijo + i);
      }
      if (obj) {
         if (accion == 'remover') {
            let limitej = events.length;
            for (let j = 0; j < limitej; j++) {
               if (handles[j]) {
                  obj.removeEventListener(events[j], handles[j]);
               }
            }
         } else {
            let limitej = events.length;
            for (let j = 0; j < limitej; j++) {
               if (handles[j]) {
                  obj.addEventListener(events[j], handles[j]);
               }
            }
         }
      }
      if (obj1) {
         if (accion == 'remover') {
            let limitej = events.length;
            for (let j = 0; j < limitej; j++) {
               if (handles[j]) {
                  obj1.removeEventListener(events[j], handles[j]);
               }
            }
         } else {
            let limitej = events.length;
            for (let j = 0; j < limitej; j++) {
               if (handles[j]) {
                  obj1.addEventListener(events[j], handles[j]);
               }
            }
         }
      }
      // console.log("Iteración número:", i);
   }
}
//****************************************************************************************************/
//*FINAL: funcion encargada de eliminar o poner el efecto de zoom en las placas y las concesiones.
//****************************************************************************************************/

//*****************************************************************************/
//*INICIO:funcion que se ejecuta al entrar al modal para mostrar todo objeto
//*****************************************************************************/
function mostrarData(data, contenedorId = 'tabla-container', title = 'Titulo de la Tabla') {
   //*obteniendo las respuestas de los totales.
   let [respuesta] = calculoTotatelesConcesionNumber();
   //*limpiamos el contenedor.
   document.getElementById(contenedorId).innerHTML = '';
   tituloGlobal = title;
   //*creando elementos unicips con set para eliminar repeticion
   let uniqueKeys = new Set(); //*solo permite elementos unicos.
   //*recorremos el arreglo donde cada elemento es la variable item
   filtrarDataConcesionNumber().forEach(item => {
      //?nota:obteniendo un array de clave con Object.keys(item). y lo recorremos
      Object.keys(item).forEach(key => {
         //*asignamos cada key al array donde solo permitira elementos unicos.
         uniqueKeys.add(key);
      });
   });

   //* el array con elementos unicos pasara a ser el encabezado de la tabla.
   //?nota:  "Array.from()" convertir un objeto iterable(set o map), en un array 
   let encabezado = Array.from(uniqueKeys);
   //*ENCABEZADOS DEL MODAL
   let titulo = ` ${tituloGlobal} (<span id="idLengConcesion">${respuesta['totalConcesion']}</span>) <i class="fa-solid fa-cube text-secondary"></i> ${respuesta['numRam']} <i class="fa-duotone fa-solid fa-folder-open text-secondary"></i> TRAMITES (<span id="idLengTramites">${respuesta['totalTramites']}</span>) <i class="fa-solid fa-money-bill-trend-up text-secondary"></i> TOTAL LPS. <span id="Total_A_Pagar">${montoFormateado(respuesta['totalMonto'])}</span>`;
   //!llamamos a la fucnion TablaBusquedaInterna

   if (!data || data.length === 0) {
      document.getElementById(contenedorId).innerHTML = `<div class="d-flex justify-content-center">
         <div class="alert alert-primary text-center mx-auto mt-3" role="alert" style="width: 50%;">
            ¡AÚN NO SE HA INGRESADO NINGUNA <strong>CONCESIÓN</strong>! PARA VISUALIZAR, INGRESE UNA <strong>CONCESIÓN EN EL SOLICITUD</strong>!
         </div>
      </div>
      `;
   } else {
      TablaBusquedaInterna(titulo, encabezado, contenedorId);
   }
}
//*****************************************************************************/
//*FINAL:funcion que se ejecuta al entrar al modal para mostrar todo objeto
//*****************************************************************************/

//**************************************************************************************************************/
//* INICIO: funcion encargada de calcular los totales de concesionNumber (monto,concesiones,tramites y la ram)
//**************************************************************************************************************/
function calculoTotatelesConcesionNumber() {

   let lengTramites = 0;
   var totalMonto = 0;
   var ramElement = document.getElementById("RAM");
   var ram = ramElement ? ramElement.value ?? 'RAM' : 'RAM';

   concesionNumber.forEach(data => {
      for (let key in data) {
         if (key = 'Tramites') {
            const filteredTramites = data[key].filter(item => item !== false);
            //*sumamos los elemenois distintos de false;
            lengTramites += filteredTramites.length;
            data[key].forEach(element => {
               for (let data in element) {
                  if (data == 'Monto') {
                     totalMonto += (element['Cantidad_Vencimientos'] * parseFloat(element[data]));
                  }
               }
            });
         }
         break;
      }
   })
   return [
      {
         'totalConcesion': concesionNumber.length,
         'numRam': ram,
         'totalTramites': lengTramites,
         'totalMonto': totalMonto
      }
   ]
}
//**************************************************************************************************************/
//* FINAL: funcion encargada de calcular los totales de concesionNumber (monto,concesiones,tramites y la ram)
//**************************************************************************************************************/

//*******************************************/
//*INICIO: funcion que crea la tabla dinamica.
//******************************************/
function TablaBusquedaInterna(titulo = '', encabezado, contenedorId) {
   //*creando elemento div con clase row
   const tableRow = document.createElement('div');
   tableRow.id = 'contentRow';
   tableRow.className = 'row';

   //*creamos elemento que contendra la tabla.;
   const tableContainer = document.createElement('div');
   //*para las clases del elemeto
   tableContainer.className = 'table-responsive';

   //*creamos el contenedor del titulo
   const tableTitle = document.getElementById('exampleModalLabel')
   //*aasignamos el texto
   tableTitle.innerHTML = `${titulo}`;
   //*asignamos la clase cque contiene el estilo del titulo
   tableTitle.className = 'titleTable';
   //* enviamos el el div creado al contenedor
   tableContainer.appendChild(tableRow);

   //! funcion createSearchField encargada de crear el fucnionamiento de
   //!busuqeda de la tabla por cada dato o casilla.
   createSearchField(tableRow);

   //*creamos el elemento de la tabla
   const table = document.createElement('table');
   table.innerHTML = '';
   //*las clases para el estilo de la tabla.
   table.className = 'table tableObjeto table-hover  mb-5';
   table.style.display = '';
   //*creamos el contenedor del encabezado de la tabla.
   const thead = document.createElement('thead');
   //*las clases del encabezado de la tabla
   thead.className = 'headTable table-primary';
   thead.style.display = '';
   //*creamos el contenedor el cuerpo de la tabla
   const tbody = document.createElement('tbody');
   //*las clases del cuerpo de la tabla
   tbody.className = 'table-group-divider';
   tbody.id = 'idTbody';
   //*creamos la instancia de las filas del encabezado
   const headerRow = document.createElement('tr');
   const th1 = document.createElement('th');
   //*creamos la columna de enumeracion en el encabezado y asignamos
   //* su texto y lo enviamos al contenedor

   //?NOTA:."some()" si alguno cumple con la condicion.
   headerRow.appendChild(th1).innerHTML = ((rolUser.some(role => rol.includes(role))) ? ((esEditable() != false) ? `<div class="form-check">
         <input class="form-check-input" type="checkbox" onclick="seleccionarTodos();" value="" id="check_trash_all"> 
         <label class="form-check-label" for="flexCheckDefault">
            &nbsp;&nbsp;#
         </label>
         </div>`
      :
      `<div class="form-check">
         <label class="form-check-label" for="flexCheckDefault">
            &nbsp;&nbsp;#
         </label>
         </div>`) : '#');
   th1.style.width = "auto";

   //*recorremos en arreglo que contiene el encabezado de la tabla.

   if (rolUser.some(role => rol.includes(role))) {
      encabezado.forEach((key) => {
         //?nota: "!encabezadoExclui.has(key)", para verificar si la clave está en el conjunto de exclusión
         if (!encabezadoExclui.has(key)) {
            //*creamos la columana de la tabla
            const th = document.createElement('th');
            //*le asignamos el valor
            th.textContent = key;
            if (key == 'Placa') {
               th.setAttribute("colspan", "2");
            }
            //*la enviamos al contenedor de las filas del encabezado
            headerRow.appendChild(th);
         }
      });

      const th = document.createElement('th');
      //*le asignamos el valor
      th.textContent = 'Acción';
      //*la enviamos al contenedor de las filas del encabezado
      headerRow.appendChild(th);
   } else {
      encabezado.forEach((key) => {
         if (!encabezadoExclui.has(key)) {
            //*creamos la columana de la tabla
            const th = document.createElement('th');
            //*le asignamos el valor
            th.textContent = key;
            //*la enviamos al contenedor de las filas del encabezado
            headerRow.appendChild(th);
         }
      });
   }

   //* enviamos la fila que contien los datos al contenedor del encabezado
   thead.appendChild(headerRow);
   //!llamamos la funciónrenderTableRowst encargada de construir el body de la tabla
   renderTableRowst(tbody, rowsPerPage, currentPage);
   //!llamamos la función clearSearchField encargada de limpiar el input y resta
   clearSearchFiled(tableRow, tbody);
   //*enviamos el encabezado y el body al contenedor de la tabla.
   table.appendChild(thead);
   table.appendChild(tbody);
   //* enviamos la tabla al contenedo donde se mostrara la tabla.
   tableContainer.appendChild(table);
   //*creamos el nav que contendra la paginacion.
   const paginationContainer = document.createElement('nav');
   //*creando el id del nav
   paginationContainer.setAttribute('id', 'pagination-nav');
   //*creando las clases del nav de paginacion
   paginationContainer.className = 'mb-5 d-flex justify-content-center bg-light p-3';
   //*enviando nav de paginacion al contenedor principal.
   tableContainer.appendChild(paginationContainer);

   //!llamamos a la función renderPagination que crea la paginacion
   renderPagination(currentPage, rowsPerPage, paginationContainer);
   //*r 
   const contenedor = document.getElementById(contenedorId);
   //*limpiamos el contenedo
   contenedor.innerHTML = '';
   //*enviamos la tablacontainer que es el contenedor de la tabla
   contenedor.appendChild(tableContainer);
}
//*******************************************/
//*FINAL: funcion que crea la tabla dinamica.
//******************************************/

//******************************************/
//*Función que crea el input para buscar 
//******************************************/
function createSearchField(container) {
   //*validando si existe el input si no existe se crea
   if (!container.querySelector('.search-input')) {
      //*creando el elemento input
      const searchInput = document.createElement('input');
      //*el tipo de input texto.
      searchInput.type = 'text';
      searchInput.id = 'buscarEnMalla';
      //*Añadiendo una referencia
      searchInput.placeholder = 'Buscar...';
      //*añadiendo clases al input de boostrap
      searchInput.className = 'form-control mb-3 search-input';
      //*creando un div que contendra el input
      const containerDiv = document.createElement('div');
      //*añadiendo clases
      containerDiv.className = 'col-4';
      //*enviando el input al contenedor del div
      containerDiv.appendChild(searchInput);
      //!añadiendo el evento handleSearch al input
      searchInput.addEventListener('input', (e) => handleSearch(e));
      //*enviando el contenedor del div con el input al contenedor de la tabla.
      container.appendChild(containerDiv);
   } else {
      //*si existe el input
      const searchInput = container.querySelector('.search-input');
      //*limpiamos
      searchInput.value = '';
   }
}
//*******************************************************************************/
//* funcion clearSearchFiled encargada de limpiar el input y restablecer la tabla
//*******************************************************************************/
function clearSearchFiled(tableContainer, tbody) {
   //*creando el boton
   const btn_limpiar = document.createElement('button');
   //*el tipo de boton
   btn_limpiar.type = 'button';
   //*añadiendo el texto al boton
   btn_limpiar.textContent = 'LIMPIAR';
   //*añadiendo las clases del boton y estilos
   btn_limpiar.className = 'btn btn-secondary  btn-sm ml-auto';
   btn_limpiar.setAttribute("data-bs-toggle", "tooltip");
   btn_limpiar.setAttribute("title", "Click para limpiar la busqueda");
   //*crear div que contenga el boton 
   const divClear = document.createElement('div');
   const divClear1 = document.createElement('div');
   divClear1.id = "id_divClear1";
   copiDivClear = divClear;
   //*agregando clases
   divClear.className = 'col  text-end mb-2 w-10';
   divClear1.className = 'col mb-2 w-100';
   //*pasando boton a divClear
   divClear1.appendChild(btn_limpiar);
   //*Función onclick
   btn_limpiar.onclick = function () {
      //*selecciona al input y le asigna '' para blanquear.
      //document.querySelectorAll('input').forEach(input => input.value = '');
      document.getElementById('buscarEnMalla').value = '';
      //!llamando la funcionrenderTableRowst para renderizar la tabla nuevamente 
      renderTableRowst(tbody, rowsPerPage, currentPage);
      renderPagination(currentPage, rowsPerPage, document.querySelector('#pagination-nav'))
   };


   if (esEditable() != false) {
      // console.log('esEditable', esEditable());
      const btn_trash = document.createElement('button');
      if (rolUser.some(role => rol.includes(role))) {
         //*el tipo de boton
         btn_trash.type = 'button';
         //*añadiendo el texto al boton
         btn_trash.textContent = 'BORRAR';
         //*añadiendo las clases del boton y estilos
         btn_trash.className = 'btn btn-danger  mx-2 btn-sm ml-auto'
         btn_trash.setAttribute("data-bs-toggle", "tooltip");
         btn_trash.setAttribute("title", "Click para borrar una concesión seleccionado");

         btn_trash.onclick = () => {
            trash_Consession(filtrarDataConcesionNumber());
         };

         // //*pasando boton a divTrash
         divClear1.appendChild(btn_trash);
         //tableContainer.appendChild(divClear);
         //*Función onclick
      }
   } else {
      // console.log('no es editable', esEditable());
   }

   btnVerTramites();
   // btnVerTramitesFijos();
   btnStyle();

   tableContainer.appendChild(divClear1);
   //*botones secundarios.
   tableContainer.appendChild(copiDivClear);
}
//******************************************************************************/
//*INICIO:funcion encargada de  crear y mostrar el boton para ver los tramites.
//******************************************************************************/
function btnVerTramites() {
   const verDetalleConcesiones = document.createElement('button');
   verDetalleConcesiones.innerHTML = '';
   if (rolUser.some(role => rol.includes(role))) {
      //*el tipo de boton
      verDetalleConcesiones.type = 'button';
      verDetalleConcesiones.id = 'idbtnVerTramites';
      verDetalleConcesiones.setAttribute("data-bs-toggle", "tooltip");
      verDetalleConcesiones.setAttribute("title", 'Click para ver todos los tramites');
      //*añadiendo el texto al boton
      localStorage.getItem('filaOcultaId');

      if (arregloFilaOculta != '') {
         verDetalleConcesiones.innerHTML = `<i class="fa-solid fa-eye"></i>`
      } else {
         verDetalleConcesiones.innerHTML = `<i class="fa-solid fa-eye-slash"></i>`;
      }

      //*añadiendo las clases del boton y estilos
      verDetalleConcesiones.className = 'btn btn-info  mx-2 btn-sm ml-auto'
      verDetalleConcesiones.onclick = () => {
         if (verDetalleConcesiones.innerHTML == '<i class="fa-solid fa-eye" aria-hidden="true"></i>') {
            verDetalleConcesiones.innerHTML = `<i class="fa-solid fa-eye-slash"></i > `
         } else {
            verDetalleConcesiones.innerHTML = '<i class="fa-solid fa-eye"></i>'
         }
         mostrarDetalleTramites();
      };
      // //*pasando boton a divTrash
      copiDivClear.appendChild(verDetalleConcesiones);
      //*Función onclick
   }
}
//******************************************************************************/
//*FINAL:funcion encargada de  crear y mostrar el boton para ver los tramites.
//******************************************************************************/

//******************************************************
//*INICIO:funcion encargada de  crear elbtn de estylos.
//******************************************************
function btnStyle() {
   //*boton para cambiar el estilo.
   const btn_style = document.createElement('button');
   //*el tipo de boton
   btn_style.type = 'button';
   //*añadiendo el texto al boton
   let setUnSetEvent1 = localStorage.getItem('setUnSetEvent' + document.getElementById("ID_Usuario").value);
   setUnSetEvent1 = JSON.parse(setUnSetEvent1); //
   if (setUnSetEvent1.setUnSetEvent == 'add') {
      // console.log(setUnSetEvent1.setUnSetEvent, 'setUnSetEvent1.setUnSetEvent ADD');
      btn_style.innerHTML = '<i class="fas fa-times"></i>';
      btn_style.setAttribute('data-accion', 'remover');
      btn_style.setAttribute("data-bs-toggle", "tooltip");
      btn_style.setAttribute("title", "Click para  DESACTIVAR el zoom de la conesión y las placas");
   } else {
      // console.log(setUnSetEvent1.setUnSetEvent, 'setUnSetEvent1.setUnSetEvent REMOVE');
      btn_style.innerHTML = '<i class="fas fa-check"></i>';
      btn_style.setAttribute('data-accion', 'add');
      btn_style.title = 'Dele click a este boton para ACTIVAR el zoom de la conesión y las placas'
   }
   //*añadiendo las clases del boton y estilos
   btn_style.className = 'justify-content-rigth align-items-rigth btn btn-warning  mx-2 btn-sm';
   btn_style.id = 'btnAccion';
   btn_style.addEventListener('click', (e) => {
      if (e.target.id == 'btnAccion') {
         setUnSetEvent(e, ['mouseenter', 'mouseleave'], e.target.getAttribute('data-accion'), [onmouseenterAccion, onmouseleaveAccion], 'numero_concesion_');
         setUnSetEvent(e, ['mouseenter', 'mouseleave'], e.target.getAttribute('data-accion'), [onmouseenterAccion, onmouseleaveAccion], 'idRow');
         e.target.innerHTML = '';
         if (e.target.getAttribute('data-accion') == 'remover') {
            e.target.setAttribute('data-accion', 'add');
            e.target.innerHTML = '<i class="fas fa-check"></i>';
            localStorage.setItem('setUnSetEvent' + document.getElementById("ID_Usuario").value, JSON.stringify({ 'setUnSetEvent': 'remover' }));

         } else {
            e.target.setAttribute('data-accion', 'remover');
            e.target.innerHTML = '<i class="fas fa-times"></i>';
            localStorage.setItem('setUnSetEvent' + document.getElementById("ID_Usuario").value, JSON.stringify({ 'setUnSetEvent': 'add' }));
         }
         e.stopPropagation();
         e.preventDefault();
      } else {
         // console.log(event.target.id, 'event.target.id en onclick no button accion');
         var evento = new Event("click", {
            bubbles: false,
            cancelable: true,
         });
         document.getElementById("btnAccion").dispatchEvent(evento);
      }
   })


   // //*pasando boton a divTrash
   copiDivClear.appendChild(btn_style);
}
//******************************************************
//*FINCAL:funcion encargada de  crear elbtn de estylos.
//******************************************************

//*****************************************
//*INICIO:funcion encargada deL BTNFIJAR 
//*****************************************
function btnVerTramitesFijos() {
   const verDetallesFijos = document.createElement('button');
   verDetallesFijos.innerHTML = '';
   if (rolUser.some(role => rol.includes(role))) {
      verDetallesFijos.type = 'button';
      verDetallesFijos.id = 'idbtnVerTramitesBloquear';
      verDetallesFijos.title = 'Dele click a este botón para FIJAR LOS TRÁMITES siempre en VISIBLE.';
      verDetallesFijos.innerHTML = `<i class="fa-solid fa-thumbtack"></i>`;
      verDetallesFijos.className = 'btn btn-success mx-2 btn-sm ml-auto';

      verDetallesFijos.addEventListener('click', (e) => {
         //*desabilitando el boton de vista de tramites.
         vistaTramitesFijos();
      });
      copiDivClear.appendChild(verDetallesFijos);
   }
}

function vistaTramitesFijos() {
   if (vistaHabilitada == false) {
      document.getElementById('idbtnVerTramites').innerHTML = `<i class="fa-solid fa-thumbtack-slash"></i>`;
      document.getElementById('idbtnVerTramites').disabled = true;
      // console.log('deshabilitado');

      filtrarDataConcesionNumber().forEach((element, index) => {
         let trId = 'idRow' + (index + recalculandoPaginacion(pageNumerClick));
         let tr = document.getElementById(trId);
         if (tr) {
            tr.onclick = null;
            localStorage.setItem('btndesabilitado', JSON.stringify(true));
         }
      });
      vistaHabilitada = true;
   } else {
      //*habilitando el boton de vista de tramites.
      document.getElementById('idbtnVerTramites').innerHTML = `<i class="fa-solid fa-thumbtack"></i>`;
      // console.log('habilitado');
      document.getElementById('idbtnVerTramites').disabled = false;
      vistaHabilitada = false;

      filtrarDataConcesionNumber().forEach((element, index) => {
         let trId = (index + recalculandoPaginacion(pageNumerClick));
         let tr = document.getElementById('idRow' + trId);
         let concesion = element['Concesion'];
         let unidad1 = element['unidad1'];
         let unidad = element['unidad'];

         if (tr) {
            tr.onclick = (event) => {
               localStorage.setItem('btndesabilitado', JSON.stringify(false));
               //?nota:para obtener el div del elemento o fila con click "arregloDiv[index][0]"
               agregar(event, trId, arregloDiv[index][0], concesion, unidad, unidad1);
            };
         }
      });
   }
}

//****************************************
//*FINAL:funcion encargada deL BTNFIJAR 
//****************************************

//**************************************************************************************************************/
//*INICIO:funcion encargada de generar  agregar fila pero con las condiciones de almacenamiento y visualizacion
//**************************************************************************************************************/
function agregar(e, index, div, concesion, unidad, unidad1) {

   e.stopPropagation();
   if (div.style.display === 'none') {
      div.style.display = 'block'; // Muestra la fila
      div.style.maxWidth = '1200px';
      div.style.minWidth = '1000px';
      div.innerHTML = '';

      //*solo se va a ejecutar una vez.

      //*obtengo los id de las filas que estan visibles.
      arregloFilaOculta = JSON.parse(localStorage.getItem('filaOcultaId')) || [];
      //*pregunto si este id existe si no lo guardo en localStorage.
      if (!arregloFilaOculta.includes(`fila_Oculta_${index}`)) {
         arregloFilaOculta.push(`fila_Oculta_${index}`);
         localStorage.setItem('filaOcultaId', JSON.stringify(arregloFilaOculta));
         //* almaceno el ram
         localStorage.setItem('Ram', JSON.stringify(document.getElementById("RAM").value));
      }
      if (arregloFilaOculta != '') {
         // console.log('hay datos en arregloFilaOculta', arregloFilaOculta);
         document.getElementById('idbtnVerTramites').innerHTML = `<i class="fa-solid fa-eye"></i>`
      }
   } else {
      // document.getElementById('idRow' + index).setAttribute('visible', '0');
      //*obtengo la lista de los id de las filas visibles que estan guardados.
      arregloFilaOculta = JSON.parse(localStorage.getItem('filaOcultaId')) || [];
      //*busco el elemneto que quiero ocultar en el arreglo para que me devuelva un arreglo sin ese elemento
      let eFilaOculta = arregloFilaOculta.filter(item => item !== `fila_Oculta_${index}`);
      //*guardo el elmento sin el id del elemento que estoy ocultando.
      localStorage.setItem('filaOcultaId', JSON.stringify(eFilaOculta));
      //*llamo el elemento para verificar si tienen datos o no y cambiar el icono del ojito.
      arregloFilaOculta = JSON.parse(localStorage.getItem('filaOcultaId')) || [];
      // console.log(arregloFilaOculta, 'arregloFilaoculta tiene que estar vacio');
      if (arregloFilaOculta == '') {
         // console.log('NO HAY DATOS en arregloFilaOculta', arregloFilaOculta);
         document.getElementById('idbtnVerTramites').innerHTML = `<i class="fa-solid fa-eye-slash"></i>`
      }
      div.style.display = 'none'; // Oculta la fila
   }
   idFilaAnterior = 'idRow' + (index);
   // fila['Tramites'], 
   agregar_fila(index, idFilaAnterior, div, concesion, unidad, unidad1);

   if (e.target.type === "checkbox") {
      e.stopPropagation(); // Prevent the click from reaching the outer div
      return; // Optional: Stop further execution if needed
   }
}
//**************************************************************************************************************/
//*FINAL:funcion encargada de generar  agregar fila pero con las condiciones de almacenamiento y visualizacion
//**************************************************************************************************************/

//******************************************************************************/
//*Funcion handSearch encargada de realizar la busqueda del input en la tabla.
//*****************************************************************************/
function handleSearch(event) {
   //*obtenemos el valor del input y lo pasamos a minuscula
   const query = event.target.value.toLowerCase();
   //?nota: some nos ayuda a verificar si hay elementos que coincidan
   //*utilizamos el filtereddata nos permite filtrar los datos que coinciden 
   const filtrarData = filtrarDataConcesionNumber().filter(item =>
      // Filtrar en las propiedades principales del objeto
      Object.values(item).some(value =>
         String(value).toLowerCase().includes(query)
      ) ||
      // Filtrar dentro del arreglo 'Tramites'
      item.Tramites.some(t =>
         Object.values(t).some(value =>
            String(value).toLowerCase().includes(query)
         )
      )
   );

   //*donde inicia la paginacion
   // currentPage = 1;
   //*elemento que contiene la tabla
   const tableContainer = document.querySelector('.table-responsive');
   //!llamamos la funciónrenderTableRowst para que muestre la tabla con la informacion que se encontro
   renderTableRowst(tableContainer.querySelector('tbody'), rowsPerPage, currentPage, filtrarData);
   //*instanciamos la paginacion
   const paginationContainer = document.getElementById('pagination-nav');
   //! llamamos la funcion renderPaginacion para que muestr la paginacion actualizada con los datos enconytrados.
   renderPagination(currentPage, rowsPerPage, paginationContainer);
}

function onmouseleaveAccion(event) {
   // console.log(event.target.id, 'event.target.id leave');
   event.target.style = "fontSize='1rem';";
}
function onmouseenterAccion(event) {
   // console.log(event.target.id, 'event.target.id enter');
   event.target.style.fontSize = "1.5rem";
   event.target.style.fontWeight = "bolder";
}

//***************************************************************/
//*INICIO:funcion encargada de renderizar el body de la tabla.
//***************************************************************/
function renderTableRowst(tbody, rowsPerPage, currentPage, filtrardata = '') {
   getFilaOculta = JSON.parse(localStorage.getItem('filaOcultaId')) || [];
   // getBtndesabilitado = JSON.parse(localStorage.getItem('btndesabilitado')) || [];

   //*limpiando el body de la tabla
   tbody.innerHTML = '';
   //*para saber en que elemento va a iniciar
   const startIndex = (currentPage - 1) * rowsPerPage;
   //*saber en que elemento va a finalizar
   const endIndex = startIndex + rowsPerPage;

   // console.log(startIndex ,endIndex,'currentPage');
   //?nota: "slice()", para extraer una parte del arreglo o cadena.
   var paginatedData = '';
   if (filtrardata == []) {
      paginatedData = filtrarDataConcesionNumber().slice(startIndex, endIndex);
   } else {
      paginatedData = filtrardata.slice(startIndex, endIndex);
   }

   let datos = [];

   paginatedData.forEach((fila, index) => {

      //*creamos la fila de el body
      const row = document.createElement('tr');
      row.id = 'idRow' + (index + recalculandoPaginacion(pageNumerClick));


      //*creamos la columna y añadimos el numero de fila para despues añadir al elemnto de fila
      row.appendChild(document.createElement('td')).innerHTML = ((rolUser.some(role => rol.includes(role))) ? ((esEditable() != false) ? `<div div class="form-check" >
         <input onclick="event.stopPropagation();" class="form-check-input check_trash" type="checkbox" data-originalrow="${index}" data-concesion="${fila['Concesion']}" value="${index}" id="id_trash_${row.id}">
            <label id="indice_row_${startIndex + index}" class="form-check-label" for="flexCheckDefault">
               &nbsp;&nbsp; ${startIndex + index + 1}
            </label>
         </div>`: startIndex + index + 1) : startIndex + index + 1);

      if (rolUser.some(role => rol.includes(role))) {
         //********************************************/
         //* CUANDO EL ROL DEL USUARIO ES IGUAL A 7
         //********************************************/
         //*recorremos la data

         Object.entries(fila).forEach(([key, value]) => {
            //? nota: verificamos que sean distinto a un arreglo y objeto !(Array.isArray(value)) && !(value !== null && typeof value === 'object' )
            //* verificamos si es un arreglo si es distinto se muestra en la fila
            // console.log(key,'value');
            let td = null;
            // !(value !== null && typeof value === 'object')
            if (!(Array.isArray(value)) && (key !== 'Unidad1') && !(value !== null && typeof value === 'object') &&
               !(key == 'ID_Memo') && !(key == 'Concesion_Encriptada') && !(key == 'Permiso_Explotacion_Encriptado')
               && !(key == 'esCarga') && !(key == 'esCertificado')) {

               td = document.createElement('td');//*asignamos la manito a la fila
            }
            if (td !== null) {
               td.style.cursor = 'pointer';
               //*GENERANDO LINK DE LOS
               // console.log(fila['esCarga'], fila['esCertificado'], fila['Concesion_Encriptada'], fila['Permiso_Explotacion_Encriptado'], 'datossssssss');
               let linksConcesion = tipoConcesion(fila['esCarga'], fila['esCertificado'], fila['Concesion_Encriptada'], fila['Permiso_Explotacion_Encriptado']);
               //*validamos si el texto pertenece a la placa para asignar funcion
               if (key == 'Placa') {
                  //*quito el cursos de la fila
                  td.style.cursor = 'pointer';
                  td.setAttribute("colspan", "2");
                  //!funcion que muestra la revision del vehiculo
                  crearSpans(value, fila['Unidad'], fila['Unidad1'], td, row, fila['Concesion'], fila['Tramites']);
               } else {
                  var estadoRam = document.getElementById("ID_Estado_RAM").value;
                  if (key == 'Concesion') {
                     tamaño_Tramite = fila['Tramites'].length;
                     if (estadosValidos.includes(estadoRam) || fila['ID_Expediente']) {
                        linkConcesiones(td, linksConcesion['rutacertificado'], value, tamaño_Tramite, index, tipoConsesion = 'C');
                     } else {
                        var spanConcesion = document.createElement('span');
                        spanConcesion.id = 'numero_concesion_' + index;
                        spanConcesion.innerHTML = value;
                        td.appendChild(spanConcesion);
                        if (JSON.parse(localStorage.getItem('setUnSetEvent' + document.getElementById("ID_Usuario").value)).setUnSetEvent == 'add') {
                           spanConcesion.addEventListener("mouseleave", onmouseleaveAccion);
                           spanConcesion.addEventListener("mouseenter", onmouseenterAccion);
                        }
                        var spanTramite = document.createElement('span');
                        spanTramite.id = `tamañoTramite${index + recalculandoPaginacion(pageNumerClick)}`;
                        spanTramite.style.cssText = "display: inline-block; border-radius: 15%; background-color: rgb(17, 88, 99); color: white; padding: 3px;";
                        spanTramite.innerHTML = tamaño_Tramite;
                        td.appendChild(document.createTextNode("\u00A0")); // Agrega un espacio
                        td.appendChild(spanTramite);
                     }
                  } else {
                     if (key == 'Permiso_Explotacion') {
                        //*asignamos el texto de cada columna\
                        if (estadosValidos.includes(estadoRam) || fila['ID_Expediente']) {
                           linkConcesiones(td, linksConcesion['rutapermisoexplotacion'], value, fila['Tramites'], index, tipoConsesion = 'P');
                        } else {
                           td.textContent = value;
                        }
                     } else {
                        td.innerHTML = value;
                     }
                  }
               }

               const divFila = document.createElement('div');
               divFila.id = `fila_Oculta_${(index + recalculandoPaginacion(pageNumerClick))}`;
               divFila.style.display = 'none';
               // divFila.style.maxWidth = '1200px';
               // divFila.style.minWidth = '1000px';
               // console.log(divFila, 'divFila tabla');
               //*para pasar data y desplegar datos en fila
               arregloDiv[index] = [divFila];
               //*asignado para poder utilizar despues.
               divAgregar = divFila;

               row.onclick = (event) => {
                  //*!llamado de la funcion que contiene agregar_fila.
                  agregar(event, (index + recalculandoPaginacion(pageNumerClick)), divFila, fila['Concesion'], fila['Unidad'], fila['Unidad1']);
               }
               //*añadimos las columnas a la fila
               row.appendChild(td);
            }
         }); //fin del ciclo.
         //*añadimos la columna para la accion
         const td = document.createElement('td');
         const spanAccion = document.createElement('span');

         //*asignamos la manito a la fila
         spanAccion.className = 'end';
         spanAccion.style.cursor = 'pointer';
         spanAccion.innerHTML = `<i id = "est_const"  class="fa-duotone fa-solid fa-pen" data-bs-toggle="tooltip" title="Click para editar concesión"></i > `;
         spanAccion.onclick = function (event) {
            event.stopPropagation();
            Swal.fire({
               title: `¿MODIFICAR LA CONCESION: ${fila['Concesion']}?`,
               text: '¿ESTA SEGURO DE MODIFICAR LA CONCESION',
               icon: 'warning',
               showCancelButton: true,
               confirmButtonText: 'SÍ',
               cancelButtonText: 'CANCELAR'
            }).then((result) => {
               //* si confirma que esta seguro de eliminar llamomos la funcion para que elimine de la base de datos.
               if (result.isConfirmed) {
                  pageNumerClick = 0;
                  //***********************************************************************************/
                  //*Ocultar Pantalla Modal donde se Concesiones Salvadas
                  //***********************************************************************************/
                  $("#exampleModal").modal("hide");
                  //***********************************************************************************/
                  //* Llamando función que permitre editar la información de la concesion en preforma
                  //***********************************************************************************/
                  fEditarConcesion(fila['Concesion']);
               }
            });
         };
         td.appendChild(spanAccion);
         row.appendChild(td);
      } else {

         //********************************************/
         //* CUANDO EL ROL DEL USUARIO ES DISTINTO A 7
         //********************************************/
         Object.entries(fila).forEach(([key, value]) => {

            //? nota: verificamos que sean distinto a un arreglo y objeto !(Array.isArray(value)) && !(value !== null && typeof value === 'object' )

            // console.log(value, 'value');
            //* verificamos si es un arreglo si es distinto se muestra en la fila
            if (!(Array.isArray(value)) && !(value !== null && typeof value === 'object') && !(key == 'ID_Memo')) {
               //*creamos las columnas
               const td = document.createElement('td');
               //*asignamos la manito a la fila
               td.style.cursor = 'pointer';
               //*validamos si el texto pertenece a la placa para asignar funcion
               if (key == 'Placa') {
                  //*quito el cursos de la fila
                  td.style.cursor = 'none';
                  crearSpans(value, fila['Unidad'], fila['Unidad1'], td, row, fila['Concesion'], fila['Tramites']);
               } else {

                  if (key == 'Concesion') {
                     td.textContent = value;
                  } else {
                     if (key == 'Permiso_Explotacion') {
                        //*asignamos el texto de cada columna.
                        td.innerHTML = value;
                     } else {
                        //*asignamos el texto de cada columna.
                        td.innerHTML = value;
                     }
                  }
               }

               //*añadimos las columnas a la fila.
               row.appendChild(td);
               const divFila = document.createElement('div');
               divFila.id = `fila_Oculta_${index}`;
               divFila.style.display = 'none';

               //*para pasar data y desplegar datos en fila.
               row.onclick = (event) => {
                  event.stopPropagation();
                  if (divFila.style.display === 'none') {
                     divFila.style.display = 'block'; // Muestra la fila
                     divFila.innerHTML = '';
                  } else {
                     divFila.style.display = 'none'; // Oculta la fila
                  }
                  idFilaAnterior = ('idRow' + (index + recalculandoPaginacion(pageNumerClick)));
                  // console.log(index + recalculandoPaginacion(pageNumerClick), 'ss');
                  agregar_fila((index + recalculandoPaginacion(pageNumerClick)), idFilaAnterior, divFila, fila['Concesion'], fila['Unidad'], fila['Unidad1']);
               }
            }
         });
      }
      //*enviamos la fila al contenedor del cuerpo de la tabla.
      tbody.appendChild(row);

      datos.push([index + recalculandoPaginacion(pageNumerClick), 'idRow' + (index + recalculandoPaginacion(pageNumerClick)), divAgregar, fila['Concesion'], fila['Unidad'], fila['Unidad1']]);
      datosMostra = {
         'datos': datos
      }
   });
   //*esta funcion muestra todas las filas y luego las cierra para que puedan existir en el DOM
   mostrarAgregar_fila2();

   let RamAlmacena = JSON.parse(localStorage.getItem('Ram')) || [];
   if (RamAlmacena == document.getElementById("RAM").value) {
      //*permite abrir las filas que estan almacenadas en localStorage.
      mostrarAgregar_fila();
   } else {
      localStorage.removeItem('Ram');
      localStorage.removeItem('filaOcultaId');
   }

}
//***************************************************************/
//*FINAL:funcion encargada de renderizar el body de la tabla.
//***************************************************************/

//**************************************************************************************************************************/
//*funcion encargada de mostrar las filas ocultas si estas estaban se podian visualizar antes de cerrar la pantalla o eliminar un dato *//
//***************************************************************************************************************************/
function mostrarAgregar_fila() {
   arregloFilaOculta = JSON.parse(localStorage.getItem('filaOcultaId')) || [];
   for (const key in datosMostra) {
      if (Object.prototype.hasOwnProperty.call(datosMostra, key)) {
         const element = datosMostra[key];
         for (const e of element) {
            let datoEncontrado = arregloFilaOculta.find(elemento => String(elemento) === String(e[2].id));
            if (datoEncontrado !== undefined) {
               setTimeout(() => {
                  e[2].style.display = 'block';
                  e[2].style.maxWidth = '1200px';
                  e[2].style.minWidth = '1000px';
                  e[2].innerHTML = '';
                  var el = document.getElementById(`${e[1]}`);
                  if (el) {
                     document.getElementById(`${e[1]}`).setAttribute('visible', '1');
                     agregar_fila(e[0], e[1], e[2], e[3], e[4], e[5]);
                  }
               }, 100);
            }
         }
      }
   }
}
//***********************************************************************************************/
//*INICIO: funcion que me permite desplegar todas las fila ocultas para que existen en el DOM
//***********************************************************************************************/

function mostrarAgregar_fila2() {
   // console.log(datosMostra,'datosMostra');
   for (const key in datosMostra) {
      if (Object.prototype.hasOwnProperty.call(datosMostra, key)) {
         const element = datosMostra[key];
         for (const e of element) {
            setTimeout(() => {
               e[2].style.display = 'block';
               e[2].style.maxWidth = '1200px';
               e[2].style.minWidth = '1000px';
               e[2].innerHTML = '';
               var el = document.getElementById(`${e[1]}`);
               if (el) {
                  document.getElementById(`${e[1]}`).setAttribute('visible', '0');
                  document.getElementById(`${e[1]}`).setAttribute('fijarVisible', '0');
                  agregar_fila(e[0], e[1], e[2], e[3], e[4], e[5]);
                  // document.getElementById(`${e[1]}`).setAttribute('visible', '0');
                  e[2].style.display = 'none';
               }
            }, 100);
         }
      }
   }
}
//*****************************************************************/
//*FINAL: funcion que me permite desplegar todas las fila ocultas
//*****************************************************************/

//****************************************************************/
//*funcion encargada de crear las subtabla de la tabla principal.
//***************************************************************/

function agregar_fila(index, idFilaAnterior, div, Concesion, unidad = '', unidad1 = '') {

   // console.log(unidad,unidad1, 'unidad y unidad1');

   var ID_Placa = unidad1['ID_Placa'];
   //  console.log(ID_Placa, 'unidad1...');
   //*para limpiar si se elimina un tramite;
   if (document.getElementById(`fila_Oculta_${index}`)) {
      document.getElementById(`fila_Oculta_${index}`).innerHTML = '';
   }

   var data_tramites = [];
   var indiceNuevo = index;

   filtrarDataConcesionNumber().forEach((elemento, indice) => {
      if (indice == indiceNuevo) {
         data_tramites = elemento['Tramites'];
      }
   })

   //*arreglo que se envia a la funcion de eliminar para poder 
   var dataAgregarFila = [index, idFilaAnterior, div, Concesion, unidad = '', unidad1 = ''];
   let indexTotal = index + 1; //indice de la concesion
   var indice = 1;
   suma = 0;
   let ID_Unidad = unidad['ID'] || unidad['ID_Unidad'] || unidad.ID_Unidad;
   // let ID_Unidad1 = unidad1?.ID_Unidad || unidad1?.ID;
   let ID_Unidad1 = unidad1?.['ID_Unidad'] || unidad1?.['ID'];

   // let indexFilaConcesio = index;
   //* tramites contiene la nueva data ya con los elementos excluidos
   let tramites = dataNueva(data_tramites);
   // console.log(idFilaAnterior, 'idFilaAnterior antes de instancia');
   let filaAnterior = document.getElementById(`${idFilaAnterior}`);

   // console.log(filaAnterior, 'filaAnterior');
   //*generando arreglo con los datos diferente a un arreglo y de tamaño>0 con datos distinto de false
   const tramitesValidos = tramites.filter(tramite =>
      !(tramite === false || (tramite && typeof tramite === 'object' && Object.keys(tramite).length === 0))
   );

   var indexRow = index;
   // console.log(tramites, 'tramites');

   tramitesValidos.forEach((tramite, ind) => {

      let row_agregada = document.createElement('tr');
      row_agregada.innerHTML = '';
      row_agregada.id = 'idTramite' + index + ind;
      // row_agregada.style.width = "100%"; 
      const IdTramiteC = 'idTramite' + index + ind;
      row_agregada.setAttribute('data-id-row', 'idRow' + index);
      row_agregada.className = "filas-tramite table-secondary table-hover";

      const td_trash = document.createElement('td');
      td_trash.className = 'table-secondary';
      td_trash.style.cursor = 'pointer';

      const tdfila = document.createElement('td');
      td_trash.className = 'table-secondary text-start';
      td_trash.style.cursor = 'pointer';

      //*indice de las nuevas filas creadas. //'M_CL'
      if (rolUser.some(role => rol.includes(role))) {
         if ((tramite['ID_Compuesto'].split("_")[2] === 'R') || (tramite['ID_Compuesto'].split("_")[3] === 'CL') || (esEditable() == false)) {
            td_trash.innerHTML = '';
            td_trash.style.cursor = 'none';
         } else {
            td_trash.innerHTML = `<span span > <i class="fa-solid fa-trash deleteTramite"></i></span > `;
         }
      } else {
         td_trash.innerHTML = ``;
         td_trash.style.cursor = 'none';
      }
      row_agregada.appendChild(td_trash);

      // var indexLabel = parseInt(document.getElementById("indice_row_"  + String(indexRow)).textContent);
      tdfila.innerHTML = `<span span id = "indice_row_tramite_${(indexTotal) - 1}_${ind}" > ${indexTotal}.${(ind + 1)}</span>`;
      row_agregada.appendChild(tdfila);
      let trashIcon = td_trash.querySelector('.deleteTramite');
      if (trashIcon) {
         trashIcon.addEventListener('click', (e) => {
            //*verificamos que tengamos mas de un elemento
            // console.log(tramitesValidos.length);
            if (TOTAL_TRAMITES_X_CONCESION[updateCollection(Concesion)] > 1) {
               //*e trashIconvitando que se recargue
               e.preventDefault();
               Swal.fire({
                  title: '¿ESTÁ SEGURO?',
                  html: `¿QUIERE ELIMINAR EL TRAMITE DE <strong>${tramite['descripcion']}</strong> DE LA CONCESIÓN <strong>${Concesion}</strong>?`,
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonText: 'SÍ, ELIMINAR',
                  cancelButtonText: 'CANCELAR'
               }).then((result) => {
                  //* si confirma que esta seguro de eliminar llamomos la funcion para que elimine de la base de datos.
                  if (result.isConfirmed) {
                     //*buscamos en el arreglo aquellos elementos que son distintos de false
                     //?nota: tramite['ID'] =idtramite la bd, idTramiteC=tramite de la fila,indexTotal=Indece de la concesion.
                     //?nota: monto,idCompuesto.
                     let idTramiteEliminar = ind;
                     // console.log(ID_Unidad, ID_Unidad1, 'ID_Unidad ID_Unidad1');
                     fEliminarTramite(Concesion, tramite['ID'], IdTramiteC, indexTotal, (parseInt(tramite['Cantidad_Vencimientos']) * parseFloat(tramite['Monto']).toFixed(2)).toFixed(2), tramite['ID_Compuesto'], ID_Unidad, ID_Unidad1, dataAgregarFila, idTramiteEliminar);
                     //*modificando el valor de los tramites en fila principal.
                     tamaño_Tramite = data_tramites.length;
                     // console.log(tamaño_Tramite, 'tama');
                     // let cambioTamañoT = document.getElementById(`tamañoTramite${ indexFilaConcesio } `).textContent;
                     document.getElementById(`tamañoTramite${indiceNuevo}`).textContent = tamaño_Tramite - 1;
                     // document.getElementById(`tamañoTramite${ indexFilaConcesio } `).textContent = tamaño_Tramite;
                  }
               });
            } else {
               Swal.fire('¡ALERTA!', 'NO SE PUEDE DEJAR SIN NINGÚN TRÁMITE.', 'warning');
            }
         });
      }

      for (const key in tramite) {
         if (key !== 'ID_Compuesto') {
            const tdSub = document.createElement('td');
            tdSub.className = "table-secondary text-start";
            if (Object.prototype.hasOwnProperty.call(tramite, key)) {
               if (key === 'Monto') {
                  let monto = +tramite[key]; // Convertir el valor de 'Monto' a número
                  if (!isNaN(monto)) { // Verificar si el valor convertido es un número
                     // Revisamos si el valor es para 'RENOVACIÓN CERTIFICADO DE OPERACIÓN'
                     suma += tramite['Cantidad_Vencimientos'] * monto;
                  }
               }
               tdSub.className = (key === 'Monto') ? 'text-nowrap table-secondary text-end' : 'text-nowrap table-secondary text-start';
               //*si es cambio de MODIFICACIÓN CAMBIO DE UNIDAD el tramite.
               if (tramite['descripcion'] == 'MODIFICACIÓN CAMBIO DE UNIDAD') {
                  var estadoRam = document.getElementById("ID_Estado_RAM").value;
                  // console.log(estadoRam, 'estadoRam dentro de tramite modificacion de unidad');
                  const estadosValidosModificacionUnidad = ['IDE-1', 'IDE-2'];

                  if (estadosValidosModificacionUnidad.includes((estadoRam) ? estadoRam : '')) {
                     const td = document.createElement('td');
                     td.className = "table-secondary text-start";

                     const link = document.createElement('a');
                     link.style.textDecoration = 'none';
                     link.style.cursor = 'pointer';
                     link.target = "_blank";
                     link.href = $appcfg_Dominio_Raiz + `:140/RenovacionesE/NuevoDictamenR.aspx?Solicitud=${ram}&Concesion=${Concesion}&ID_Placa=${ID_Placa}`;

                     // Contenido del enlace
                     link.innerHTML = (tramite[key] == 'MODIFICACIÓN CAMBIO DE UNIDAD') ?
                        `<i id="est_const" class="fas fa-cog" data-bs-toggle="tooltip" title="Click para generar el dictamen de la unidad"></i>
                     <strong> ${tramite[key]} </strong>` : tramite[key];

                     link.addEventListener('click', (e) => {
                        e.stopPropagation(); // Evita que el evento se propague al elemento padre
                        // console.log('click en link', link, 'ram', ram, 'Concesion', Concesion, 'ID_Placa', ID_Placa);
                        // No se necesita e.preventDefault() porque queremos que abra el enlace
                     });

                     td.appendChild(link);
                     tdSub.appendChild(td);

                     // Activar tooltips
                     setTimeout(() => {
                        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                        tooltipTriggerList.map(el => new bootstrap.Tooltip(el));
                     }, 100);
                  } else {
                     const link = document.createElement('td');
                     link.innerHTML = tramite[key];
                     link.className = 'text-nowrap table-secondary text-start';
                     tdSub.appendChild(link);
                  }
               } else {
                  //*cuando el tramite no es cambio de unidad.
                  if (key === 'Monto') {
                     tdSub.className = 'text-nowrap table-secondary text-end';
                  } else {
                     tdSub.className = 'text-nowrap table-secondary text-start';
                  }
                  if (key == 'Fecha_Expiracion') {
                     tdSub.textContent = tramite[key] || "";
                     //*si no existe el dato de fecha que coloque padin para dar el espacio de las fechas.
                     if (tdSub.textContent.trim() === "") {
                        tdSub.style.padding = "0 50px";
                     }
                  } else {
                     if (key == 'Fecha_Expiracion_Nueva') {
                        tdSub.textContent = tramite[key] || "";
                        //*si no existe el dato de fecha que coloque padin para dar el espacio de las fechas.
                        if (tdSub.textContent.trim() === "") {
                           tdSub.style.padding = "0 50px";
                        }
                     } else {
                        tdSub.textContent = tramite[key]// Asigna el valor o una cadena vacía si es undefined/null   
                     }
                  }
               }
               row_agregada.appendChild(tdSub);
            }
         }
      }

      const tdSubMonto = document.createElement('td');
      tdSubMonto.className = 'text-nowrap table-secondary text-end';
      // tdSubMonto.textContent = (tramite['Cantidad_Vencimientos'] * tramite['Monto']) + '.00';
      tdSubMonto.textContent = montoFormateado(tramite['Cantidad_Vencimientos'] * tramite['Monto']);
      row_agregada.appendChild(tdSubMonto);
      filaAnterior.appendChild(row_agregada);
      //* Añadimos la fila al array de filas dinámicas
      filasTramites.push(row_agregada);
      div.appendChild(row_agregada);
      TOTAL_TRAMITES_X_CONCESION[updateCollection(Concesion)] = tramitesValidos.length;
      index++;
   })

   //***************************/
   //* Fila de total (addRoT)
   //***************************/
   let addRoT = document.createElement('tr');
   addRoT.id = 'idTramite' + (indice) + '.' + (index);
   // addRoT.setAttribute('data-id-row', idRow);
   addRoT.className = "filas-tramite table"

   let tdTotalTile = document.createElement('td');
   tdTotalTile.className = 'text-nowrap fw-bold text-end  ms-5 text-table';
   tdTotalTile.setAttribute('colspan', '9');
   addRoT.appendChild(tdTotalTile).textContent = 'TOTAL   ' + ' ';

   let tdTotal = document.createElement('td');
   tdTotal.className = 'text-nowrap  fw-bold text-end text-table';
   addRoT.appendChild(tdTotal).innerHTML = ` Lps.  <span id="Total${indexTotal}"> ${montoFormateado(suma)}</span`

   filaAnterior.appendChild(addRoT);
   div.appendChild(addRoT);
   filasTramites.push(addRoT);
   //* Insertamos las filas en el DOM después de la fila de referencia

   filaAnterior.parentNode.insertBefore(div, filaAnterior.nextSibling);
}
//******************************************************************************************************/
//*funcion encargada de colocar el link para los documentos del certficado y permiso de explotacion
//******************************************************************************************************/

function linkConcesiones(td, ruta, value, tamaño, index, tipo) {

   var data_tramites = [];
   var indiceNuevo = index + recalculandoPaginacion(pageNumerClick);

   filtrarDataConcesionNumber().forEach((elemento, indice) => {
      if (indice == indiceNuevo) {
         data_tramites = elemento['Tramites'];
      }
   })

   tamaño_Tramite = data_tramites.length;
   const link = document.createElement('a');
   if (tipo == 'C') {
      link.innerHTML = value + `<span id="tamañoTramite${index}" style = "display: inline-block; border-radius: 15%; background-color:rgb(31, 109, 121); color: white; padding: 3px 7px; font-size: 10px;"> ${tamaño_Tramite}</span > `;
   } else {
      link.innerHTML = value;
   }
   //*Abrir en una nueva pestaña
   link.target = "_blank";
   link.style.color = '#043653c9';
   link.style.textDecoration = 'underline';
   link.style.cursor = 'pointer';

   link.addEventListener('mouseover', function () {
      // *ambiar el color cuando el ratón pasa por encima
      link.style.color = '#8d685a';
      link.style.textDecoration = 'none';
   });

   //* Restaurar el estilo cuando el ratón deja el enlace
   link.addEventListener('mouseout', function () {
      link.style.color = '#043653c9';
      link.style.textDecoration = 'underline';
   });

   //*Añadir un manejador de clic para evitar la propagación y verificar la ruta
   link.addEventListener('click', function (event) {
      //*Detener la propagación del clic
      event.stopPropagation();

      //*Verificar si la ruta es válida (no vacía, no undefined, etc.)
      if (!ruta || ruta === '') {
         //*Si no hay una ruta válida, mostrar la alerta
         Swal.fire({
            title: '¡ALERTA SIN DATOS!',
            text: 'NO HAY LINK GENERADO',
            icon: 'warning',
            confirmButtonText: 'Aceptar'
         });
         //*Prevenir la acción de abrir el enlace si no hay ruta válida
         event.preventDefault();
      }
   });

   //*Si la ruta es válida, asignamos ruta
   if (ruta && ruta !== '') {
      link.href = `${ruta} `;
   }

   //*Añadir el enlace al <td>
   td.appendChild(link);
}

//***********************************************************************/
//*funcion que se necarga de crear la tabla de la revision del vehiculo
//**********************************************************************/
function crearSpans(value, unidad = '', unidad1 = '', td, row, idConcesion, tramites) {
   console.log(unidad, 'unidad span');
   console.log(unidad1, 'unidad1 span');
   console.log(tramites, 'tramites');
   var texto = '';
   //*inicializando varible para evalual la ocnstancia en cambio de placa.
   var mcp = 'false';
   //console.log(unidad1,unidad,value);
   if (unidad1 != undefined && unidad1 != '' && unidad1 != null) {
      texto = value + '->' + 'S' + '->' + 'C'
   } else {
      texto = value;
   }

   //* Separar el texto por el delimitador '->'
   //?nota: "aplit()", para dividir en partes por el elemento o signo.
   // console.log(value,'value undefined')     ;
   // console.log(texto,'texto undefined')     ;
   var partes = texto.split("->");
   //* Obtener el contenedor donde se agregarán los span
   const container = td;
   //console.log(partes, 'partes');
   //* Crear 3 span individuales
   partes.forEach((valor, index) => {
      //console.log(index, 'index crear span');
      //* Crear el span dinámicamente para cada parte del texto
      const span = document.createElement("span");
      span.id = String(row.id) + '_placa' + '_' + index;
      //* Establecer el espacio entre los spans
      span.style.marginRight = "8px";
      //* Cambiar el cursor cuando se pasa sobre el span
      span.style.cursor = "pointer";

      const spanc = document.createElement("span");
      spanc.id = String(row.id) + '_placa' + '_' + index;
      //* Establecer el espacio entre los spans
      spanc.style.marginRight = "8px";
      //* Cambiar el cursor cuando se pasa sobre el span
      spanc.style.cursor = "pointer";
      //* Asignar texto al span
      // console.log(index,'index');
      if (index === 0) {
         //?nota: como existe la unidad1 la placa original pasa a ser placa que sale 
         if (unidad1 != undefined && unidad1 != '' && unidad1 != null) {
            span.innerHTML = '<strong>' +
               (unidad['ID_Placa'] || unidad['Placa']) +
               '</strong>';
            span.className = "justify-content-center align-items-center borderPlacaSale";
            span.setAttribute("data-bs-toggle", "tooltip");
            span.setAttribute("title", "Click para ver datos del vehículo 1");

            if (JSON.parse(localStorage.getItem('setUnSetEvent' + document.getElementById("ID_Usuario").value)).setUnSetEvent == 'add') {
               span.addEventListener("mouseleave", onmouseleaveAccion);
               span.addEventListener("mouseenter", onmouseenterAccion);
            }
         } else {
            //?nota: si no hay Unidad 1 solo se muestra la unidad
            span.innerHTML = '<strong">' +
               (unidad['ID_Placa'] || unidad['Placa']) +
               '</strong>';
            span.className = "flex justify-content-center align-items-center borderPlaca";
            span.setAttribute("data-bs-toggle", "tooltip");
            span.setAttribute("title", "Click para ver datos del vehículo 2");
            if (JSON.parse(localStorage.getItem('setUnSetEvent' + document.getElementById("ID_Usuario").value)).setUnSetEvent == 'add') {
               span.addEventListener("mouseleave", onmouseleaveAccion);
               span.addEventListener("mouseenter", onmouseenterAccion);
            }
            var tramite = 'MODIFICACIÓN CAMBIO DE PLACA';
            var tamanoPlaca = value.length;
            console.log(tamanoPlaca,'tamanoPlaca');
            tramites.forEach(element => {
               // console.log('tramites recorridos', element, '->', element['descripcion']);
               if (tramite == element['descripcion'] && !tramite.includes('MODIFICACIÓN CAMBIO DE UNIDAD') && tamanoPlaca < 7) {
                  var estadoRam = document.getElementById("ID_Estado_RAM").value;
                  //*siel parametro se manda y hay cambio de unidad se muestra las constancias.
                  if (estadosValidosConstancia.includes(estadoRam)) {
                     // console.log(span, index);
                     //!funcion encargada de actualizar el icono de la constancia de generar a ver constancia
                     //*SE MANDA INDEX=2 YA QUE ES CUANDO SE EJECUTA LA CONSTANCIA LA UNIDAD  Y EL SPANC NUEVO
                     actualizarContenido(spanc, 2, unidad);
                     //*CONDICION PARA INSERTAR EL SPAN AL FINAL DEL SPAN DE LA PLACA
                     mcp = 'true';
                  }
               }
            });
         }
      } else if (index === 1) {
         //*?nota: es S cuando si hay cambio de unidad?
         if (valor == 'S') {
            //*creando un nuevo span para la flecha
            let arrowSpan = document.createElement('span');
            let idspan = String(row.id) + "_flecha";
            arrowSpan.id = idspan;
            arrowSpan.innerHTML = ' <i class="fa-solid fa-arrow-right flex justify-content-center align-items-center"></i>  ';
            //* Añadir el nuevo span al DOMfo
            container.appendChild(arrowSpan);
            //*asignando valor a span origiunal 
            //?nota: la placa que entra es la placa de la unidad1
            // console.log(unidad1['ID_Placa'], 'unidad1["ID_Placa"] antes de asignar');
            span.innerHTML = '<strong>' + (unidad1['ID_Placa'] || unidad1['Placa']) + '</strong>';
            span.className = "justify-content-center align-items-center borderPlacaEntra";
            span.setAttribute("data-bs-toggle", "tooltip");
            span.setAttribute("title", "Click para ver datos del vehículo");
            if (JSON.parse(localStorage.getItem('setUnSetEvent' + document.getElementById("ID_Usuario").value)).setUnSetEvent == 'add') {
               // console.log(JSON.parse(localStorage.getItem('setUnSetEvent' + document.getElementById("ID_Usuario").value)).setUnSetEvent, 'add add');
               span.addEventListener("mouseleave", onmouseleaveAccion);
               span.addEventListener("mouseenter", onmouseenterAccion);
            }
         }
      } else {

         var estadoRam = document.getElementById("ID_Estado_RAM").value;
         //*siel parametro se manda y hay cambio de unidad se muestra las constancias.
         if (estadosValidosConstancia.includes(estadoRam)) {
            // console.log(span, index);
            //!funcion encargada de actualizar el icono de la constancia de generar a ver constancia
            actualizarContenido(span, index, unidad1);
         }

      }

      //* Evento de clic para mostrar la constancia
      span.onclick = function (event) {
         //* Evitar que el evento se propague a otros elementos
         event.stopPropagation();
         //*Dependiendo de la placa seleccionada, pasamos los valores necesarios
         //?nota: es 2 cuando hay cambio de unidad y se deben crear las constancias.
         if (index != 2) {
            //!mostramos la revision de la placa seleccionada.
            mostrarUnidades(unidad, unidad1);
         } else {
            //!llamamos la funcion constancia que decide si genermaos o vemos al constancia.
            constancia(unidad1['ID_Placa'], unidad1['ID_Memo'], unidad1, idConcesion);
         }
      };
      //* Añadir el span al contenedor
      container.appendChild(span);
      //*SI SE CUNPME ESTA CONDICION SE MANDA EL SPANC CUANDO HAY NO HAY CAMBIO DE UNIDAD PERO HAY CAMBIO DE PLACA 
      if (mcp == 'true') {
         //* Evento de clic para mostrar la constancia
         spanc.onclick = function (event) {
            //* Evitar que el evento se propague a otros elementos
            event.stopPropagation();
            //*Dependiendo de la placa seleccionada, pasamos los valores necesarios
            //?nota: es 2 cuando hay cambio de unidad y se deben crear las constancias.

            console.log('entro en generar constancia');
            //!llamamos la funcion constancia que decide si genermaos o vemos al constancia.
            constancia(unidad['ID_Placa'], unidad['ID_Memo'], unidad, idConcesion);
         };
         container.appendChild(spanc);
      }
   });

   //* Añadir el contenedor a la fila (row)
   row.appendChild(container);
}

/***************************************************************************************************/
//*funcion encargada de actulaizar las iconos de la constancia de la placas si hay cambio de unidad.
//**************************************************************************************************/
// function actualizarContenido(span, index, unidad1) {
//    if (index === 2 && unidad1 &&
//       (unidad1['ID_Memo'] === false || unidad1['ID_Memo'] === 'false' || !unidad1['ID_Memo'])
//    ) {

//       span.innerHTML = `<i id="est_const" class="fas fa-cog" data-bs-toggle="tooltip" title="Click para generar constancia"></i>`;
//    } else if (index === 2 && unidad1 &&
//       unidad1['ID_Memo'] !== false && unidad1['ID_Memo'] !== 'false' && unidad1['ID_Memo']
//    ) {
//       span.innerHTML = `<i id="est_const" class="fas fa-file-pdf" data-bs-toggle="tooltip" title="Click para ver la constancia"></i>`;

//    }
// }

/***************************************************************************************************/
//* Función encargada de actualizar los íconos de constancia de las placas si hay cambio de unidad.
/***************************************************************************************************/
function actualizarContenido(span, index, unidad1) {
   // Verificamos que sea el caso de cambio de unidad (index === 2)
   if (index !== 2 || !unidad1) return;

   // Extraemos el valor de ID_Memo
   const memo = unidad1['ID_Memo'];

   // Normalizamos el valor para evaluar correctamente (todo a string)
   const memoStr = String(memo).trim().toLowerCase();

   // CASO 1: No tiene constancia generada → ⚙️
   if (
      memo === false ||             // valor booleano false
      memoStr === 'false' ||        // texto "false"
      memoStr === '' ||             // vacío
      memo === null ||              // nulo
      memo === undefined ||         // indefinido
      memoStr === 'null' ||         // texto "null"
      memoStr === 'undefined'       // texto "undefined"
   ) {
      span.innerHTML = `<i id="est_const" class="fas fa-cog" data-bs-toggle="tooltip" title="Click para generar constancia"></i>`;
      return;
   }

   // CASO 2: Tiene constancia generada → 📄
   span.innerHTML = `<i id="est_const" class="fas fa-file-pdf" data-bs-toggle="tooltip" title="Click para ver la constancia"></i>`;
}

//*******************************************************************/
//*se encarga de generar la data nuevo sin los elementos excluidos.
//******************************************************************/
function dataNueva(data) {

   // console.log(data);
   //*Arreglo con la data a excluir
   let dataExcluir = [
      //"ID_Compuesto",
      "Codigo",
      "ID_Categoria",
      "ID_Tipo_Servicio",
      "ID_Modalidad",
      "ID_Clase_Servico",
      "Total_A_Pagar"
      // Cantidad_Concesion_Vencimientos: 1
      // Cantidad_PerExp_Vencimientos: 1
   ];

   let monto = 0;
   //*rrecorremos la data y asignamos
   const filtrarData = data.map(item => {
      //*creo una copia de de los elementos
      const filtrarItem = { ...item };
      dataExcluir.forEach(field => {
         //*eliminar campo que sean iguales a los de dataExcluir
         delete filtrarItem[field];
      });
      //*retorna el arreglo con los elemento eliminados
      return filtrarItem;
   });

   return filtrarData;
}
//******************************************************/
//*funcion encargada de crear la paginacion de la tabla
//*****************************************************/
//******************************************************/
//*funcion encargada de crear la paginacion de la tabla
//*****************************************************/
function renderPagination(currentPage, rowsPerPage, paginationContainer) {
   //*Limpiar la paginación existente
   paginationContainer.innerHTML = '';
   //*creando  ul de paginación
   const paginationList = document.createElement('ul');
   //*creando clase de boostrap
   paginationList.className = 'pagination';
   //*calculando el total d epaginas con el tamaño del arreglo y el numero de elementos por pagina
   const totalPages = Math.ceil(filtrarDataConcesionNumber().length / rowsPerPage);

   //*creando boton de boostrap
   const prevButton = document.createElement('li');

   //*asignando clases al btn
   prevButton.className = `page - item ${currentPage === 1 ? 'disabled' : ''} `;
   //*creando enlace anterior
   const prevLink = document.createElement('a');
   //*brindando clases al objeto
   prevLink.className = 'page-link';
   //*evitando redireccionamiento
   prevLink.href = '#';
   //*asigannado texto
   prevLink.textContent = 'Anterior';
   //*añadiento evento click al boton
   prevLink.addEventListener('click', (e) => {
      //*evitando que se recargue
      e.preventDefault();
      //*condicion para que no existan acciones si estamos en la pagina 1
      if (currentPage > 1) {
         currentPage--;
         pageNumerClick = currentPage;
         //*llamando a la funcion que renderiza el body de l atbala
         renderTableRowst(document.querySelector('#idTbody'), rowsPerPage, currentPage);
         //*llamando la paginacion
         renderPagination(currentPage, rowsPerPage, paginationContainer);
      }
   });
   //* agragamos el enlace al boton
   prevButton.appendChild(prevLink);
   //*agragamos el boton a la paginacion
   paginationList.appendChild(prevButton);

   // Páginas visibles (máximo 5 botones)
   const maxVisiblePages = 10;
   let startPage = Math.max(1, currentPage - 2);
   let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

   // Ajustar si estamos al final
   if ((endPage - startPage) < (maxVisiblePages - 1)) {
      startPage = Math.max(1, endPage - maxVisiblePages + 1);
   }


   for (let page = startPage; page <= endPage; page++) {
      //*se encarga de crera botones para cada página
      // for (let page = 1; page <= totalPages; page++) {
      //*creamos el elemento li de cadabtn
      const pageItem = document.createElement('li');
      //*añadimos las clases
      pageItem.className = `page - item ${page === currentPage ? 'active' : ''} `;
      //*creamos el elementoa de enalce
      const pageLink = document.createElement('a');
      //*añadimos las clases
      pageLink.className = 'page-link';
      //*evitamos el comportamiento por defecto
      pageLink.href = '#';
      //*añadimos texto
      pageLink.textContent = page;
      //*añadimos el evento click a cada btm
      pageLink.addEventListener('click', (e) => {
         //*evitamos el efecto de recarga por defecto
         e.preventDefault();
         //*asignamos el numero de pagina actual para actualizar
         currentPage = page;
         pageNumerClick = currentPage;
         //*llamamos nmuevamente la tabla y le pasamos los nuevos datos
         renderTableRowst(document.querySelector('#idTbody'), rowsPerPage, currentPage);
         //*llamaos la paginacion y le pasamos los nuevos datos
         renderPagination(currentPage, rowsPerPage, paginationContainer);
      });
      //*añadimos los enlaces de numeros de paginas
      pageItem.appendChild(pageLink);
      //*añadimos pageItem al contenedos principal
      paginationList.appendChild(pageItem);
   }

   //*creando boton siguiente.
   const nextButton = document.createElement('li');
   //*añadimos clases
   nextButton.className = `page - item ${currentPage === totalPages ? 'disabled' : ''} `;
   //*creamos el elemento de enlace
   const nextLink = document.createElement('a');
   //*añadimos las clases
   nextLink.className = 'page-link';
   //*prevenimos que se redireccione
   nextLink.href = '#';
   //*colocamos texto al btn
   nextLink.textContent = 'Siguiente';
   //*creamos el evento click
   nextLink.addEventListener('click', (e) => {
      //*prevenimos recargo
      e.preventDefault();
      //*Solo ejecutará el código siguiente si aún no se ha llegado a la última página.
      if (currentPage < totalPages) {
         //*incrementando 
         currentPage++;
         pageNumerClick = currentPage;
         //*llamamos nuevamnete la tabla que renderiza al body
         renderTableRowst(document.querySelector('#idTbody'), rowsPerPage, currentPage);
         //*llamamos la pagiancion nueva y resaltar la página
         renderPagination(currentPage, rowsPerPage, paginationContainer);
      }
   });
   //* insertamos el link al boton
   nextButton.appendChild(nextLink);
   //*enviamos  el boton al contenedor de la paginacion
   paginationList.appendChild(nextButton);
   //*enviamos la pagiancion completa al contenedor de vista d el apaginacion
   // paginationContainer.appendChild(paginationList);
   paginationContainer.appendChild(paginationList);

}
//******************************************/
//*funcion encargada de mostrar la revision.
//******************************************/
function mostrarUnidades(unidad = '', unidad1 = '') {
   var myModalPlaca = new bootstrap.Modal(document.getElementById('modalPlaca'));
   myModalPlaca.show();

   var title = document.getElementById('modalPlacaLabel').innerHTML = `DATOS DEL VEHÍCULO CON PLACA`;

   var bodymodal = document.getElementById('modalBodyPlaca');
   bodymodal.innerHTML = '';

   var objeto = unidad && Object.keys(unidad).length > 0 ? unidad : null;
   var objeto1 = unidad1 && Object.keys(unidad1).length > 0 ? unidad1 : null;

   var tobjeto = (objeto && objeto1) ? 1 : 0;  // Determinar si hay una o dos unidades

   function crearTabla(objeto, tipo, tobjeto) {
      if (objeto && Object.keys(objeto).length > 0) {
         let obj = Object.fromEntries(Object.entries(objeto).filter(([key]) => isNaN(Number(key))));

         const divTitulo = document.createElement('div');
         divTitulo.className = 'mb-3';

         const titulo = document.createElement('h5');

         const table = document.createElement('table');
         table.className = 'table  table-hover mb-5';

         const tbody = document.createElement('tbody');
         tbody.className = 'table-group-divider';

         //*arreglo para renombrar encabezados para mostrar la tabla de las placas
         const renombrarKey = {
            "ID_Placa": "PLACA",
            "Nombre_Propietario": "PROPIETARIO",
            "ID_Marca": "MARCA",
            "Marca": "MARCA",
            "Anio": "AÑO",
            "Modelo": "MODELO",
            "Tipo_Vehiculo": "TIPO VEHÍCULO",
            "ID_Formulario_Solicitud": "RAM",
            "Combustible": "COMBUSTIBLE",
            "Alto": "ALTO",
            "Ancho": "ANCHO",
            "Motor": "MOTOR",
            "Color": "COLOR",
            "Chasis": "CHASIS",
            "Estado": "ESTADO",
            "Sistema_Fecha": "FECHA",
            "RTN_Propietario": 'RTN_PRPIETARIO',
            "Permiso_Explotacion": "PERMISO_EXPLOTACIÓN",
            "Largo": "LARGO",
            "Capacidad_Carga": "CAPACIDAD",
            "Peso_Unidad": "PESO",
            "ID_Placa_Antes_Replaqueo": "REPLAQUEO",
            "ID_Memo": "MEMO",
            "DESC_Tipo_Vehiculo": "DESC VEHÍCULO",
            "Clase Servicio": "SERVICIO",
            "Desc_Marca": "DES_MARCA"
         };

         for (const key in obj) {
            if (Object.prototype.hasOwnProperty.call(obj, key)) {
               const element = obj[key];

               const tr = document.createElement('tr');
               const th = document.createElement('th');
               const td = document.createElement('td');

               const newKey = renombrarKey[key] || key;
               th.className = "bg-primary-subtle auto";
               th.textContent = newKey;

               // console.log(element, 'element');
               if (newKey == 'Color' || newKey == 'Marca') {
                  // var texto =element.split(" => ");
                  td.textContent = (element.split(" => "))[1];
               } else {
                  td.textContent = element;
               }
               let placa = obj['ID_Placa'] || obj['Placa']
               if (key === 'ID_Placa' || 'Placa') {
                  titulo.innerHTML = tobjeto === 1
                     ? (tipo === 'objeto' ? 'PLACA QUE SALE: ' : 'PLACA QUE ENTRA: ') +
                     `<button button type = "button" class="btn btn-${tipo === 'objeto' ? 'danger' : 'success'}" data - bs - dismiss="modal" > ${placa}</button > `
                     : `PLACA: <button type="button" class="btn btn-info" data-bs-dismiss="modal">${placa}</button>`;
                  divTitulo.appendChild(titulo);
               }
               tr.appendChild(th);
               tr.appendChild(td);
               tbody.appendChild(tr);
            }
         }
         table.appendChild(tbody);
         divTitulo.appendChild(table);
         return divTitulo;
      } else {
         return `<div div class="alert alert-warning" > NO HAY DATOS PARA MOSTRAR</div > `;
      }
   }

   const contenedorTablas = document.createElement('div');
   contenedorTablas.className = 'd-flex';

   if (objeto) contenedorTablas.appendChild(crearTabla(objeto, 'objeto', tobjeto));
   if (objeto1) contenedorTablas.appendChild(crearTabla(objeto1, 'objeto1', tobjeto));

   bodymodal.appendChild(contenedorTablas);

   if (!objeto && !objeto1) {
      bodymodal.innerHTML = `<div div class="alert alert-warning" > NO HAY DATOS PARA MOSTRAR</div > `;
   }
}
//**********************************************************************************************/
//*función encargada de selecionar todas las concesiones y desmarcar si precionan el principal
//**********************************************************************************************/

function seleccionarTodos() {
   document.getElementById('check_trash_all').addEventListener('change', function () {
      //*el check principal esta marcado?
      const isChecked = this.checked;

      //*obtener todos los check con clase check_trash
      const checkboxes = document.querySelectorAll('.check_trash');

      //*recorro los check y le pongo el estado que tiene el principal
      checkboxes.forEach(checkbox => {
         checkbox.checked = isChecked;
      });
   });
}
//************************************************************************************************/
//*funcion encargada de obtener los arreglos de las filas y las concesion que fueorn selecionadas
//************************************************************************************************/
function trash_Consession(data) {
   console.log(data, 'trasconcesion');
   const checkboxes = document.querySelectorAll('.check_trash');
   let originalRows = [];//*instanciando filas.
   let filas = [];//*instanciando filas.
   let concesiones = [];//*instanciando arreglo de concesiones
   checkboxes.forEach(checkbox => {
      if (checkbox.checked == true) {
         let originalRow = checkbox.getAttribute('data-originalrow');
         let concesion = checkbox.getAttribute('data-concesion');
         originalRows.push(originalRow);  //* La primera parte (antes del '/')
         concesiones.push(concesion);  //* La segunda parte (después del '/')
         filas.push(checkbox.value);  //* La primera parte (antes del '/')
      }
   });
   f_FetchDeletConcesiones(concesiones, filas, originalRows);
}
//******************************************************************************/
//*funcion encargada de verificar la decision del usuario y mostar las alertas.
//******************************************************************************/
function f_FetchDeletConcesiones(idConcesiones, filas, originalRows) {
   // console.log(idConcesiones, 'consession');
   let tipoAccion = false;
   if (idConcesiones.length != 0) {
      mostrarAlerta(tipoAccion = true, idConcesiones, filas, originalRows)
   } else {
      mostrarAlerta(tipoAccion, idConcesiones, filas, originalRows);
   }
}
//*****************************************************************************/
//*funcion encargada de mostrar la alerta spara evr si esta seguro de eliminar.
//*****************************************************************************/
function mostrarAlerta(tipoAccion, idConcesiones, filas, originalRows) {
   if (tipoAccion != true) {
      // alert('NO HAY DATOS');
      Swal.fire({
         title: '¡ALERTA SIN DATOS!',
         text: 'NO HAY DATOS SELECCIONADOS PARA ELIMINAR',
         icon: 'success',
         confirmButtonText: 'Aceptar'
      });
   } else {
      Swal.fire({
         title: '¿ESTÁ SEGURO?',
         html: `¿QUIERE ELIMINAR ESTA CONCESIÓN <strong>${idConcesiones}</strong>?`,
         icon: 'warning',
         showCancelButton: true,
         confirmButtonText: 'SÍ, ELIMINAR',
         cancelButtonText: 'CANCELAR'
      }).then((result) => {
         if (result.isConfirmed) {
            //* si confirma que esta seguro de eliminar llamomos la funcion para que elimine de la base de datos.
            console.log(idConcesiones, 'concesion a eliminar en fincion mostrar alerta antes de leiminar');
            ConcesionNumberAntesEliminar = num();
            eliminarConcesionesObjeto(idConcesiones, filas, originalRows);

         } else {
            //* si cancela la eliminacion limpiamos la seleccion de los check
            //* Obtener todos los checkboxes con la clase 'check_trash'
            const checkboxes = document.querySelectorAll('.check_trash');
            //*Recorro todos los checkboxes y desmarco todos
            checkboxes.forEach(checkbox => {
               checkbox.checked = false;
            });
            check_trash_all.checked = false;
         }
      });
   }
}

function localstorage(ram, fecha, ususario, concesiones, estado) {
   console.log(ram, fecha, ususario, concesiones, estado, 'localstorage');
   if (estado == true) {
      localstorageEliminar = {
         'ram': ram,
         'fecha': fecha,
         'ususario': ususario,
         'concesiones': concesiones,
         'estado': estado,
      }
      localStorage.setItem('LOCALSTORAGE_ELIMINAR', JSON.stringify(localstorageEliminar));
   }
   let obtenerError = JSON.parse(localStorage.getItem('LOCALSTORAGE_ELIMINAR')) || [];

   return obtenerError;
}

function num() {
   let dato = [];
   dato = concesionNumber.map((e, index) => {
      return index;
   })
   return dato;
}

//******************************************************************************************/
//*INICIO: funcion encargada de recalcular los valores internos segun el numero de pagian
//******************************************************************************************/
function recalculandoPaginacion(pageNumerClick) {
   let newIndexRow = 0;
   if (pageNumerClick != 0 && pageNumerClick > 1) {
      newIndexRow = ((pageNumerClick * 10) - 10);
   }
   return newIndexRow;
}
//******************************************************************************************/
//*FINAL: funcion encargada de recalcular los valores internos segun el numero de pagian
//******************************************************************************************/

//**************************************************************************************************/
//*INICIO:funcion encargada de mostrar y eliminar de la vista los tramites que ya fuero visibles.
//**************************************************************************************************/
function mostrarDetalleTramites() {
   //*suma 10 si esta en otra pagina si no es 0;
   let existeDatos = JSON.parse(localStorage.getItem('filaOcultaId')) || [];

   if (existeDatos != '') {
      //*si un dato se ejecutara y cerrara los tramites
      filtrarDataConcesionNumber().forEach((data, index) => {
         if (index < ((rowsPerPage + recalculandoPaginacion(pageNumerClick)))) {
            let fila = document.getElementById('idRow' + (index + recalculandoPaginacion(pageNumerClick)));
            if (fila != null) {
               if (fila.getAttribute('visible') == "1") {
                  document.getElementById('idRow' + (index + recalculandoPaginacion(pageNumerClick))).setAttribute('visible', '0');
                  if (document.getElementById(`fila_Oculta_${(index + recalculandoPaginacion(pageNumerClick))}`)) {
                     document.getElementById(`fila_Oculta_${(index + recalculandoPaginacion(pageNumerClick))}`).style.display = 'none';
                  }
               }
            }
         }
         //*removiendo datos de localStorage.
         localStorage.removeItem('filaOcultaId');
         localStorage.removeItem('Ram');
      });
      //*limpiando localstorage
   } else {
      // mostrarAgregar_fila2();
      //*si no existen datos abrira los tramites.
      filtrarDataConcesionNumber().forEach((data, index) => {
         if (index < ((rowsPerPage + recalculandoPaginacion(pageNumerClick)))) {
            let fila = document.getElementById('idRow' + (index + recalculandoPaginacion(pageNumerClick)));
            // console.log(fila, 'fila mostrar')
            if (fila != null) {
               fila.setAttribute('visible', '0');
               if (fila.getAttribute('visible') == "0" && document.getElementById(`fila_Oculta_${(index + recalculandoPaginacion(pageNumerClick))}`)) {
                  document.getElementById('idRow' + (index + recalculandoPaginacion(pageNumerClick))).setAttribute('visible', '1');
                  if (document.getElementById(`fila_Oculta_${(index + recalculandoPaginacion(pageNumerClick))}`).style.display == 'none') {
                     document.getElementById(`fila_Oculta_${(index + recalculandoPaginacion(pageNumerClick))}`).style.display = 'block';
                     arregloFilaOculta = JSON.parse(localStorage.getItem('filaOcultaId')) || [];
                     if (!arregloFilaOculta.includes(`fila_Oculta_${(index + recalculandoPaginacion(pageNumerClick))}`)) {
                        arregloFilaOculta.push(`fila_Oculta_${(index + recalculandoPaginacion(pageNumerClick))}`);
                        localStorage.setItem('filaOcultaId', JSON.stringify(arregloFilaOculta));
                     }
                  }
               }
            }
         }
      });
   }
}
//**************************************************************************************************/
//*FINAL:funcion encargada de mostrar y eliminar de la vista los tramites que ya fuero visibles.
//**************************************************************************************************/

//**************************************************************/
//*INICIO: funcion encargada de eliminar las concesiones */
//**************************************************************/
function eliminarConcesionesObjeto(idConcesiones, idRows, originalRows, Monto = 0) {
   console.log(idConcesiones, 'concesion a eliminar en funcion eliminar');
   // *URL del Punto de Acceso a la API
   var ram = document.getElementById("RAM").value;
   const url = $appcfg_Dominio + "Api_Ram.php";
   let fd = new FormData(document.forms.form1);
   //*Adjuntando el action al FormData
   fd.append("action", "delete-concesion-preforma");
   //*Adjuntando el idApoderado al FormData
   fd.append("idConcesiones", JSON.stringify(idConcesiones));
   fd.append("RAM", JSON.stringify(ram));
   // Fetch options

   // console.log(fd);
   const options = {
      method: "POST",
      body: fd,
   };

   // Hace la solicitud fetch con un timeout de 2 minutos
   fetchWithTimeout(url, options, 120000)
      .then((response) => response.json())
      .then(function (datos) {
         console.log(datos, 'delete -concesion - preforma');
         if (typeof datos.error == "undefined" && datos.Borrado == true) {

            let errorEliminar = localstorage(document.getElementById("RAM").value, new Date(), document.getElementById("ID_Usuario").value, idConcesiones, true);
            console.log(errorEliminar, 'errorEliminar');
            // console.log(idRows, 'dRows');
            // console.log(originalRows, 'originalRows');
            // console.log(idConcesiones, 'idConcesiones');
            console.log('antes de ejecutal las funciones 3');
            //!la fucnion reduceConcesionNumber esta en api_ram
            let respuestareduceConcesionNumber = reduceConcesionNumber(idConcesiones);
            //!la funcion preDeleteAutoComplete esta en api_ram
            preDeleteAutoComplete(idConcesiones, 'CONCESION');
            //* si respuesta existe quiere decir que la funcion clearCollection modifico el arreglo.
            //!la funcion clearCollections esta en api_ram
            let respuestaclearCollections = clearCollections(idConcesiones);
            //*si existe mandamos alerta
            console.log('ya ejecutadas las funciones 3');

            if (respuestaclearCollections) { // si es true se envia senToast de confirmacion exitosa.
               console.log(respuestaclearCollections, 'respuestaclearCollections');
               // mostrarData(concesionNumber);
               sendToast(
                  $appcfg_icono_de_success + "LA CANTIDAD DE " + idConcesiones.length + "  CONCESION(ES) SELECCIONADA(S) SE BORRARON EXITOSAMENTE ",
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
               //**********************************************************************/
               //* INICIO: Borrando La Linea de la Pantalla que contiene la concesion
               //**********************************************************************/
               let contador = idRows.length
               getDataOriginalRowForResencuenciarConcesionNumber(idRows);
               for (i = 0; i < contador; i++) {
                  let element = document.getElementById("idRow" + idRows[i]);
                  if (element) {
                     //*quitamos la fila del div de los tamites de la concesion.
                     element.remove();
                     element = document.getElementById('fila_Oculta_' + idRows[i]);
                     if (element) {
                        element.remove();
                     }
                  }
               }
               //**********************************************************************/
               //* FINAL: Borrando La Linea de la Pantalla que contiene la concesion
               //**********************************************************************/

               //*************************************************************************************************** */
               //*INICIO:segmento que elimina el id de las filas ocultas que estan guardadas y que se estan eliminando
               //*************************************************************************************************** */
               // var valorMinimo = Math.min(idRows);
               idRows.forEach(element => {
                  let valor = (parseInt(element) + recalculandoPaginacion(pageNumerClick));
                  // console.log(valor, 'valor');
                  arregloFilaOculta = JSON.parse(localStorage.getItem('filaOcultaId')) || [];
                  let eFilaOculta = arregloFilaOculta.filter(item => item !== `fila_Oculta_${valor}`);
                  localStorage.setItem('filaOcultaId', JSON.stringify(eFilaOculta));
                  arregloFilaOculta = JSON.parse(localStorage.getItem('filaOcultaId')) || [];
                  // console.log('se elimino el id', valor, arregloFilaOculta);
               });
               //****************************************************************************************************/
               //*FINAL:segmento que elimina el id de las filas ocultas que estan guardadas y que se estan eliminando
               //****************************************************************************************************/

               //********************************************************************************************************/
               //* INICIO:Segmento encargado de restar los valores del local estora despendiendo de los elementos 
               //* eliminados y sus posiciones.
               //********************************************************************************************************/
               //*Obtenemos los valores del local estorage
               arregloFilaOculta = JSON.parse(localStorage.getItem('filaOcultaId')) || [];
               //*reorganiza los elementos de menor a mayor
               arregloFilaOculta.sort();
               var nuevosValoresRestados = arregloFilaOculta.map(valor => {
                  //*encargado de restar
                  var eliminados = idRows.filter(e => e < parseInt(valor.split('_')[2]) + recalculandoPaginacion(pageNumerClick)).length
                  return "fila_Oculta_" + ((parseInt(valor.split('_')[2]) + recalculandoPaginacion(pageNumerClick)) - eliminados);
               });
               //*ingresamos el arreglo con los elementos restados al local storage.
               localStorage.setItem('filaOcultaId', JSON.stringify(nuevosValoresRestados));

               //******************************************************************************************************* */
               //* FINAL:Segmento encargado de restar los valores del local estora despendiendo de los elementos 
               //* eliminados y sus posiciones.
               //******************************************************************************************************* */

               var tbody = document.getElementById('idTbody');
               renderTableRowst(tbody, rowsPerPage, currentPage = (pageNumerClick == 0) ? 1 : pageNumerClick);
               var paginationContainer = document.getElementById('pagination-nav');
               renderPagination(currentPage = (pageNumerClick == 0) ? 1 : pageNumerClick, rowsPerPage, paginationContainer)

               const elem = document.getElementById("idLengConcesion");
               animateValue(elem, parseInt(document.getElementById("idLengConcesion").textContent), parseInt(parseInt(document.getElementById("idLengConcesion").textContent) - parseInt(respuestareduceConcesionNumber.total_concesiones)), 6000, 'highlightRed', parseInt, 0);
               const elemt = document.getElementById("idLengTramites");
               animateValue(elemt, parseInt(document.getElementById("idLengTramites").textContent), parseInt(parseInt(document.getElementById("idLengTramites").textContent) - parseInt(respuestareduceConcesionNumber.total_tramites)), 9000, 'highlightGris', parseInt, 0);
               let total_pagar_ele = document.getElementById("Total_A_Pagar");
               let Total_A_Pagar = parseFloat(document.getElementById("Total_A_Pagar").innerHTML).toFixed(2);
               animateValue(total_pagar_ele, Total_A_Pagar, parseFloat(Total_A_Pagar - respuestareduceConcesionNumber.total).toFixed(2), 13000);
               // resencuenciarConcesionNumber(idConcesiones.length);
            }
         } else {
            if (typeof datos.error != "undefined") {
               //let errormsgcash='NO SE PUDO BORRAR LAS CONCESIONES';
               console.log(datos.error, 'error delete -concesion - preforma');
               fSweetAlertEventNormal(
                  datos.errorhead,
                  datos.error + "- " + datos.errormsg,
                  "error"
               );
            }
         }
      })
      .catch((error) => {
         console.log(error, 'error catch objeto,js funcion eliminar');
         fSweetAlertEventNormal(
            "CONEXÍON",
            "ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA",
            "warning"
         );
      });
}
//**************************************************************/
//*FINAL: funcion encargada de eliminar las concesiones */
//**************************************************************/


//***************************************************************************************/
//*funcion encargada de decidir si se mira o se genera la constancia y evaluar la placa 
//***************************************************************************************/
function constancia(placa, ID_Memo, unidad1, idConcesion) {

   let $placabegin = '';
   //* Obtener los primeros dos caracteres de la placa
   if (placa !== undefined && placa !== null) {
      $placabegin = placa.slice(0, 2);
   }
   //*arreglo de placas permitidas.
   // let $placas = ["TB", "TC", "TE", "TP", "TT", "TR"];
   //* Verificar si $placabegin NO está en el array $placas
   // if (($placas.includes($placabegin))) {
   //*id_memo si genero_contancia es true quiere decir que ya se genero y ponemos 
   //*Memo global que tiene el id_memo que se creo al generar constancia si no es el que se pasa por parametro por que ya existe
   //*Comprobamos si el ID_Memo es una cadena 'true' o un valor booleano true
   if (ID_Memo === 'false' || ID_Memo === false || ID_Memo === '') {
      //* Llamar a la función generarConstancia
      generarConstancia(unidad1, idConcesion);
   } else {
      //*Llamar a la función verConstancia pasando el valor de 'ID_Memo'
      verConstancia(ID_Memo);
   }
   // } else {
   //    alert('La placa no esta dentro de las placas permitidas que son:"TB", "TC", "TE", "TP", "TT", "TR",');
   // }
}
//******************************************/
//*cambio de unidad y placa distinta 
//**************************************
function generarConstancia(unidad1, idConcesion) {

   var Chasis = unidad1['Chasis'];
   var Marca = unidad1['Marca'].split('=>');
   console.log(Marca, 'marca');
   //*globales.

   var ID_Usuario = document.getElementById("ID_Usuario").value;
   var User_Name = document.getElementById("User_Name").value;
   var form_data = new FormData();
   form_data.append("action", "save-ingreso-constancias");
   form_data.append("Referencia", document.getElementById("RAM").value); //$('#numpreforma').val()
   form_data.append("Placa_Entra", unidad1['ID_Placa']);
   form_data.append("Marca_Entra", Marca[1].trim());
   form_data.append("Tipo_Entra", unidad1['Tipo_Vehiculo']);
   form_data.append("Anio_Entra", unidad1['Anio']);
   form_data.append("Motor_Entra", unidad1['Motor']);
   form_data.append("Chasis_Entra", unidad1['Chasis']);
   form_data.append("Vin_Entra", unidad1['VIN']);
   form_data.append("Identidad", unidad1['RTN_Propietario']);
   form_data.append("Nombre_Solicitante", unidad1['Nombre_Propietario']);
   form_data.append("usuario", ID_Usuario);
   form_data.append("user_name", User_Name);

   $.ajax({
      url: $appcfg_Dominio_Raiz + ':288/Api_Autos.php',
      dataType: 'json',
      cache: false,
      contentType: false,
      processData: false,
      data: form_data,
      type: 'post',
      success: function (dataserver) {
         Memo = dataserver.ID_Memo;
         // console.log(dataserver, 'dataserver');
         if (dataserver.status == 2) {
            Swal.fire({
               title: '!NO SE SALVO LA CONSTANCIA¡',
               text: 'La sesión actual a expirado favor inice sesión nuevamente',
               icon: 'warning',
               confirmButtonText: 'OK',
            })
         } else {
            if (dataserver.status == 3) {
               Swal.fire({
                  title: 'NO SE SALVO LA CONSTANCIA',
                  html: 'Ya habia sido emitida una constancia para el CHASIS NO. <b>' + Chasis + '</b> en la fecha ' + dataserver.Sistema_Fecha + ' con el número de memo ' + dataserver.ID_Memo,
                  icon: 'warning',
                  showCancelButton: true,
                  // confirmButtonColor: "#DD6B55",
                  confirmButtonText: 'VER CONSTANCIA EMITIDA PREVIAMENTE',
                  cancelButtonText: 'CANCELAR',
               }).then((result) => {
                  //* Si confirma que está seguro de agregar la concesión, llamamos la función para agregarla.
                  if (result.isConfirmed) {
                     verConstancia(dataserver.ID_Memo);
                  }
               });
            } else {
               $link = $appcfg_Dominio_Raiz + ':288/PDF_Constancia.php?ID_Memo=' + dataserver.ID_Memo + '&FSL=' + document.getElementById("RAM").value;
               //*llamando a la funcion cambiar link para modificar si ya esta generada la constancia.y modificar id_memo de false
               // console.log('link de la constancia', $link);
               cambiarLink(dataserver.ID_Memo, idConcesion); // Llama a la función adicional
               Swal.fire({
                  title: "CONTANCIA SALVADA SATISFACTORIAMENTE",
                  html: "CONSTANCIA GENERADA CORRECTAMENTE CON EL NUMERO DE MEMO " + dataserver.ID_Memo,
                  icon: "success",  // Cambié 'type' por 'icon' que es la nueva convención en SweetAlert2
                  showCancelButton: false,
                  closeOnConfirm: true,
                  confirmButtonText: "IMPRIMIR",
                  showLoaderOnConfirm: true
               }).then(function () {
                  window.open($link, '_blank'); // Abre el enlace
               });
            }
         }
      },
      error: function (xhr) {
         console.log(xhr);
         //alert("An error occured: " + xhr.status + " " + xhr.statusText);
         if (xhr.status == 200) {
            alert(xhr.responseText);
         }
      }
   });
}
//******************************************/
//*funcion encargada de realizar el cambio 
//******************************************/
function cambiarLink(MEMO, idConcesion) {
   //*modificamos arreglo original con el memo nuevo
   modificarIdMemo(idConcesion, MEMO);
   //*llamo a la funcion de la tabla para recargar de nuevo con los datos nuevos y paso el nuevo objeto modificadoi
   mostrarData(concesionNumber);
}
//******************************************/
//*funcion encargada de modificar el arreglo 
//******************************************/
function modificarIdMemo(idConcesion, MEMO) {
   concesionNumber.forEach((element, index) => {
      Object.values(element).forEach(dato => {
         //*comparamos el dato del arreglo con el id del elemento de la concession que selecionamos.
         if (dato == idConcesion) {
            //*si es igual verificamos si la unidad existe y si existe
            if (element['Unidad1'] && typeof element['Unidad1'] === 'object') {
               //*modifico el ID_Memo y inserto el elemento. cambio false por el valor.
               element['Unidad1']['ID_Memo'] = MEMO;
            } else {
               //*si no no hay unidad
               // console.log('no hay unidad => modificarIdMemo');
            }
         }
      });
   });
}
//******************************************/
//*funcion encargada de ver la constancia.
//******************************************/
function verConstancia(MEMO) {
   // MEMO = 'IHTT-CON-1939-23';
   var numPreforma = document.getElementById("RAM").value//UNIDAD1['ID_Formulario_Solicitud']//$('#numpreforma').val(); // 
   var url = $appcfg_Dominio_Raiz + ':288/PDF_Constancia.php?ID_Memo=' + MEMO + '&FSL=' + numPreforma;
   window.open(url, '_blank'); // Abrir en una nueva ventana

   // console.log('dentro de la funcion ver constancia');
   // console.log('url constancia', url);
}
//**************************************************/
//*genra certificado dependiendo de las concesiones.
//**************************************************/
function tipoConcesion(esCarga, esCertificado, Concesion_Encriptada, Permiso_Explotacion_Encriptado) {
   //   console.log(esCarga, esCertificado, Concesion_Encriptada, Permiso_Explotacion_Encriptado, 'dentro para armar link');
   const dominio = `${$appcfg_Dominio_Raiz}:172/api_rep.php?action=`;
   let link = {};  //* Usamos un objeto en lugar de un arreglo

   //* Es Renovación de Certificado
   if (esCertificado) {
      // console.log('esCertficado');
      if (esCarga) { // CARGA
         const rutacertificado = `${dominio}get-PDFCertificado-Carga&Certificado=${Concesion_Encriptada}`;
         const rutapermisoexplotacion = `${dominio}get-PDFPermisoExp-Carga&Permiso=${Permiso_Explotacion_Encriptado}`;
         link['rutacertificado'] = rutacertificado;
         link['rutapermisoexplotacion'] = rutapermisoexplotacion;
      } else { // Otro caso (PES)
         const rutacertificado = `${dominio}get-PDFCertificado&Certificado=${Concesion_Encriptada}`;
         const rutapermisoexplotacion = `${dominio}get-PDFPermisoExp-Pas&Permiso=${Permiso_Explotacion_Encriptado}`;
         link['rutacertificado'] = rutacertificado;
         link['rutapermisoexplotacion'] = rutapermisoexplotacion;
      }

   } else { //* Es Renovación de Permisos Especiales
      // console.log('no esCertficado');
      if (esCarga) { // CARGA
         const rutacertificado = `${dominio}get-PDFPermisoEsp-Carga&PermisoEspecial=${Concesion_Encriptada}`;
         link['rutacertificado'] = rutacertificado;
         link['rutapermisoexplotacion'] = '';

      } else { // Otro caso (PAS)
         const rutacertificado = `${dominio}get-PDFPermisoEsp-Pas&PermisoEspecial=${Concesion_Encriptada}`;
         link['rutacertificado'] = rutacertificado;
         link['rutapermisoexplotacion'] = '';
      }
   }
   return link;
}

