

// document.addEventListener("DOMContentLoaded", function () {
//* Página actual, comenzamos en la página 1
let currentPage = 1;
//* Número de filas por página 
let rowsPerPage = 10;
//* Variable global para almacenar las filas de los tramites
let filasTramites = [];

let suma = 0;
//*definiendo los roles permitidos
var rol = [7, 9];
var modulo = '';
//*rol que tiene el usuario logiado.
let rolUser = [7];
//*variable para obtener el ram en que se esta trabajando.
var ramElement = document.getElementById("RAM");
var ram = ramElement ? ramElement.value ?? 'RAM' : 'RAM';


//*titulos variables 
var lengTramites = 0;
//*estados para mostarar cambio de unidad link
const estadosValidosModificacionUnidad = ['IDE-1', 'IDE-2'];
//*estados para mostrar las constancias.
const estadosValidosConstancia = ['IDE-1', 'IDE-2'];
//*estados paraa visualizar certificado y permiso de explotacion
const estadosValidos = ['IDE-1', 'IDE-2', 'IDE-5'];

var dataGlobal = [];
var tituloGlobal = '';
//*se excluyen los encabezados de la tabla de objeto.
const encabezadoExclui = new Set(['Tramites', 'Unidad', 'Unidad1', 'ID_Memo', 'Concesion_Encriptada', 'esCarga', 'esCertificado', 'Permiso_Explotacion_Encriptado']);
//*para saber el estado de la RAM actual.
var estadoRam = document.getElementById("ID_Estado_RAM").value;

function mostrarData(data, contenedorId = 'tabla-container', title = 'Titulo de la Tabla') {

   // console.log(data, 'datdInicial');
   document.getElementById(contenedorId).innerHTML = '';

   tituloGlobal = title;
   dataGlobal = [...data];

   //*creando elementos unicips con set para eliminar repeticion
   let uniqueKeys = new Set(); //*solo permite elementos unicos.
   let resultado = dataGlobal.filter(item => item !== false && item !== "" && item !== null);
   //*filtramos los campos que no queremos mostrar
   //*creamos arreglo con la informacion a exclir
   let dataExcluir = ['ID_Formulario_Solicitud', 'ID_Expediente', 'ID_Resolucion', 'ID_Solicitud', 'CodigoAvisoCobro'];

   const filtrarData = resultado.map(item => {
      //*creo una copia de de los elementos
      if (item != false && item != 'undefined') {
         const filtrarItem = { ...item };
         dataExcluir.forEach(field => {
            //*eliminar campo que sean ifuales a los de dataExcluir
            delete filtrarItem[field];
         });
         //*retorna el arreglo con los elemento eliminados
         return filtrarItem;
      }
   });

   //*recorremos el arreglo donde cada elemento es la variable item
   filtrarData.forEach(item => {
      //?nota:obteniendo un array de clave con Object.keys(item). y lo recorremos
      Object.keys(item).forEach(key => {
         //*asignamos cada key al array donde solo permitira elementos unicos.
         uniqueKeys.add(key);
      });
   });

   //* el array con elementos unicos pasara a ser el encabezado de la tabla.
   //?nota:  "Array.from()" convertir un objeto iterable(set o map), en un array 
   let encabezado = Array.from(uniqueKeys);
   let concesion = '';
   var icon = 0;

   var totalMonto = 0;
   var ramElement = document.getElementById("RAM");
   var ram = ramElement ? ramElement.value ?? 'RAM' : 'RAM';

   //*para realizar los calculos 
   dataGlobal.forEach(data => {
      for (let key in data) {
         if (key == 'Concesion') {
            icon++
            concesion = data[key];
         }
         if (key = 'Tramites') {
            const filteredTramites = data[key].filter(item => item !== false);
            //*sumamos los elemenois distintos de false;
            lengTramites += filteredTramites.length;

            //  console.log(data[key]);
            data[key].forEach(element => {
               for (let data in element) {
                  // console.log(data);
                  if (data == 'Monto') {
                     totalMonto += (element['Cantidad_Vencimientos'] * parseFloat(element[data]));
                     //   totalMonto +=  parseFloat(element[data]);

                  }
               }
            });
         }
         break;
      }
   })

   //*ENCABEZADOS DEL MODAL
   let titulo = ` ${tituloGlobal} (<span id="idLengConcesion">${dataGlobal.length}</span>) <i class="fa-solid fa-cube text-secondary"></i> ${ram} <i class="fa-duotone fa-solid fa-folder-open text-secondary"></i> TRAMITES (<span id="idLengTramites">${lengTramites}</span>) <i class="fa-solid fa-money-bill-trend-up text-secondary"></i> TOTAL LPS. <span id="Total_A_Pagar">${totalMonto.toFixed(2)}</span>`;
   //!llamamos a la fucnion TablaBusquedaInterna

   if (!data || data.length === 0) {
      document.getElementById(contenedorId).innerHTML = `<div class="d-flex justify-content-center">
         <div class="alert alert-primary text-center mx-auto mt-3" role="alert" style="width: 50%;">
            ¡AÚN NO SE HA INGRESADO NINGUNA <strong>CONCESIÓN</strong>! PARA VISUALIZAR, INGRESE UNA <strong>CONCESIÓN EN EL SOLICITUD</strong>!
         </div>
      </div>
      `;
   } else {
      TablaBusquedaInterna(filtrarData, titulo, encabezado, contenedorId);
   }

}
//**************************************/
//*funcion que crea la tabla dinamica.
//**************************************/
function TablaBusquedaInterna(data, titulo = '', encabezado, contenedorId) {

   //*creando elemento div con clase row
   const tableRow = document.createElement('div');
   tableRow.id = 'contentRow';
   tableRow.className = 'row';


   //*creamos elemento que contendra la tabla.;
   const tableContainer = document.createElement('div');
   //*para las clases del elemeto
   tableContainer.className = 'table-responsive';

   //*creamos el contenedor del titulo
   // const tableTitle = document.createElement('h3');
   const tableTitle = document.getElementById('exampleModalLabel')
   //*aasignamos el texto
   tableTitle.innerHTML = `${titulo}`;
   // tableTitle.textContent = `${titulo}`;
   //*asignamos la clase cque contiene el estilo del titulo
   tableTitle.className = 'titleTable';
   //* enviamos el el div creado al contenedor
   tableContainer.appendChild(tableRow);

   //! funcion createSearchField encargada de crear el fucnionamiento de
   //!busuqeda de la tabla por cada dato o casilla.
   createSearchField(tableRow, data);

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

   //?NOTA:."some()" si alguno cumple conla condicion.
   headerRow.appendChild(th1).innerHTML = ((rolUser.some(role => rol.includes(role))) ? `<div class="form-check">
         <input class="form-check-input" type="checkbox" onclick="seleccionarTodos();" value="" id="check_trash_all"> 
         <label class="form-check-label" for="flexCheckDefault">
            &nbsp;&nbsp;#
         </label>
         </div>` : '#');
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
         // console.log(key);
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
   renderTableRowst(data, tbody, rowsPerPage, currentPage);
   //!llamamos la función clearSearchField encargada de limpiar el input y resta
   clearSearchFiled(tableRow, data, tbody);

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
   paginationContainer.className = ' mb-5 d-flex justify-content-center bg-light p-3';
   //*enviando nav de paginacion al contenedor principal.
   tableContainer.appendChild(paginationContainer);

   //!llamamos a la función renderPagination que crea la paginacion
   renderPagination(currentPage, data, rowsPerPage, paginationContainer);
   //*r 
   const contenedor = document.getElementById(contenedorId);
   //*limpiamos el contenedo
   contenedor.innerHTML = '';
   //*enviamos la tablacontainer que es el contenedor de la tabla
   contenedor.appendChild(tableContainer);
}
//******************************************/
//*Función que crea el input para buscar 
//******************************************/
function createSearchField(container, data) {
   // console.log(data);
   //*validando si existe el input si no existe se crea
   if (!container.querySelector('.search-input')) {
      //*creando el elemento input
      const searchInput = document.createElement('input');
      //*el tipo de input texto
      searchInput.type = 'text';
      //*Añadiendo una referencia
      searchInput.placeholder = 'Buscar...';
      //*añadiendo clases al input de boostrap
      searchInput.className = 'form-control mb-3 search-input';
      //*creando un div que contendra el input
      const containerDiv = document.createElement('div');
      //*añadiendo clases
      containerDiv.className = 'col-8';
      //*enviando el input al contenedor del div
      containerDiv.appendChild(searchInput);
      //!añadiendo el evento handleSearch al input
      searchInput.addEventListener('input', (e) => handleSearch(e, data));
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
function clearSearchFiled(tableContainer, data, tbody) {
   //*creando el boton
   const btn_limpiar = document.createElement('button');
   //*el tipo de boton
   btn_limpiar.type = 'button';
   //*añadiendo el texto al boton
   btn_limpiar.textContent = 'LIMPIAR';
   //*añadiendo las clases del boton y estilos
   btn_limpiar.className = 'btn btn-secondary  btn-sm ml-auto';
   //*crear div que contenga el boton 
   const divClear = document.createElement('div');
   //*agregando clases
   divClear.className = 'col-2 mb-2';
   //*pasando boton a divClear
   divClear.appendChild(btn_limpiar);
   //*Función onclick
   btn_limpiar.onclick = function () {
      // console.log("El botón LIMPIAR ha sido clickeado");
      //*selecciona al input y le asigna '' para blanquear.
      document.querySelectorAll('input').forEach(input => input.value = '');
      //!llamando la funcionrenderTableRowst para renderizar la tabla nuevamente 
      renderTableRowst(data, tbody, rowsPerPage, currentPage);
      renderPagination(currentPage, data, rowsPerPage, document.querySelector('#pagination-nav'))
   };

   const btn_trash = document.createElement('button');
   if (rolUser.some(role => rol.includes(role))) {
      //*el tipo de boton
      btn_trash.type = 'button';
      //*añadiendo el texto al boton
      btn_trash.textContent = 'BORRAR';
      //*añadiendo las clases del boton y estilos
      btn_trash.className = 'btn btn-danger  mx-2 btn-sm ml-auto'

      btn_trash.onclick = () => {
         trash_Consession(data);
      };

      // //*pasando boton a divTrash
      divClear.appendChild(btn_trash);
      tableContainer.appendChild(divClear);
      //*Función onclick
   }
   //*pasasndo el div que continen el btn a contenedor de la tabla
   tableContainer.appendChild(divClear);
}
//******************************************************************************/
//*Funcion handSearch encargada de realizar la busqueda del input en la tabla.
//*****************************************************************************/
function handleSearch(event, data) {
   // console.log(data);
   //*obtenemos el valor del input y lo pasamos a minuscula
   const query = event.target.value.toLowerCase();

   //?nota: some nos ayuda a verificar si hay elementos que coincidan
   //*utilizamos el filtereddata nos permite filtrar los datos que coinciden 

   const filtrarData = data.filter(item =>
      // Filtrar en las propiedades principales del objeto
      Object.values(item).some(value =>
         String(value).toLowerCase().includes(query)
      ) ||
      // Filtrar dentro del arreglo 'Tramites'
      item.Tramites.some(tramite =>

         Object.values(tramite).some(value =>

            String(value).toLowerCase().includes(query)
         )
      )
   );

   //*donde inicia la paginacion
   // currentPage = 1;
   //*elemento que contiene la tabla
   const tableContainer = document.querySelector('.table-responsive');
   //!llamamos la funciónrenderTableRowst para que muestre la tabla con la informacion que se encontro
   renderTableRowst(filtrarData, tableContainer.querySelector('tbody'), rowsPerPage, currentPage);
   //*instanciamos la paginacion
   const paginationContainer = document.getElementById('pagination-nav');
   //! llamamos la funcion renderPaginacion para que muestr la paginacion actualizada con los datos enconytrados.
   renderPagination(currentPage, filtrarData, rowsPerPage, paginationContainer);
}
//*******************************************************/
//*funcion encargada de renderizar el body de la tabla.
//******************************************************/
function renderTableRowst(data, tbody, rowsPerPage, currentPage) {
   // console.log(data, 'data objeto');
   //*limpiando el body de la tabla
   tbody.innerHTML = '';

   //*para saber en que elemento va a iniciar
   const startIndex = (currentPage - 1) * rowsPerPage;
   //*saber en que elemento va a finalizar
   const endIndex = startIndex + rowsPerPage;

   // console.log(startIndex ,endIndex,'currentPage');
   //?nota: "slice()", para extraer una parte del arreglo o cadena.
   const paginatedData = data.slice(startIndex, endIndex);

   paginatedData.forEach((fila, index) => {
      //*creamos la fila de el body
      const row = document.createElement('tr');
      row.id = 'idRow' + index;

      //*creamos la columna y añadimos el numero de fila para despues añadir al elemnto de fila
      row.appendChild(document.createElement('td')).innerHTML = ((rolUser.some(role => rol.includes(role))) ? `<div class="form-check">
         <input onclick="event.stopPropagation();" class="form-check-input check_trash"  type="checkbox" value="${index}/${fila['Concesion']}/${fila['Tramites']}" id="id_trash_${row.id}"> 
         <label class="form-check-label" for="flexCheckDefault">
            &nbsp;&nbsp; ${startIndex + index + 1}
         </label>
         </div>`  : startIndex + index + 1);

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
               let linksConcesion = tipoConcesion(fila['esCarga'], fila['esCertificado'], fila['Concesion_Encriptada'], fila['Permiso_Explotacion_Encriptado']);

               //*validamos si el texto pertenece a la placa para asignar funcion
               if (key == 'Placa') {
                  //*quito el cursos de la fila
                  td.style.cursor = 'pointer';
                  td.setAttribute("colspan", "2");

                  //!funcion que muestra la revision del vehiculo
                  crearSpans(value, fila['Unidad'], fila['Unidad1'], td, row, fila['Concesion']);
               } else {
                  var estadoRam = document.getElementById("ID_Estado_RAM").value;


                  if (key == 'Concesion') {
                     tamaño_Tramite = fila['Tramites'].length;
                     if (estadosValidos.includes(estadoRam) || fila['ID_Expediente']) {
                        linkConcesiones(td, linksConcesion['rutapermisoexplotacion'], value, tamaño_Tramite, index, tipoConsesion = 'C');
                     } else {
                        td.innerHTML = td.innerHTML = value + ` <span id="tamañoTramite${index}" style="display: inline-block; border-radius: 15%; background-color:rgb(17, 88, 99); color: white; padding: 3px 7px; font-size: 10px;">${tamaño_Tramite}</span>`;
                     }
                  } else {
                     if (key == 'Permiso_Explotacion') {

                        //*asignamos el texto de cada columna
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
               divFila.id = `fila_Oculta_${index}`;
               divFila.style.display = 'none';
               divFila.style.maxWidth = '1200px';
               divFila.style.minWidth = '1000px';


               //*para pasar data y desplegar datos en fila
               row.onclick = (event) => {
                  //*para evitar la propagación del click.
                  event.stopPropagation();
                  if (divFila.style.display === 'none') {
                     divFila.style.display = 'block'; // Muestra la fila
                     divFila.style.maxWidth = '1200px';
                     divFila.style.minWidth = '1000px';
                     divFila.innerHTML = '';
                  } else {
                     divFila.style.display = 'none'; // Oculta la fila
                  }
                  idFilaAnterior = 'idRow' + index;

                  agregar_fila(fila['Tramites'], index, idFilaAnterior, divFila, fila['Concesion'], fila['Unidad'], fila['Unidad1']);

                  if (event.target.type === "checkbox") {
                     event.stopPropagation(); // Prevent the click from reaching the outer div
                     return; // Optional: Stop further execution if needed
                  }
               }

               //*añadimos las columnas a la fila
               row.appendChild(td);

            }

         }); //fin del ciclo.


         //*añadimos la columna para la accion
         const td = document.createElement('td');

         //*asignamos la manito a la fila
         td.className = 'end';
         td.style.cursor = 'pointer';
         td.innerHTML = ` <i id="est_const"  class="fa-duotone fa-solid fa-pen"></i> `;
         td.onclick = function (event) {

            // https://satt2.transporte.gob.hn:285/ram/index.php?RAM=RAM-2024-000000086
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

         row.appendChild(td);
      } else {

         //********************************************/
         //* CUANDO EL ROL DEL USUARIO ES DISTINTO A 7
         //********************************************/
         Object.entries(fila).forEach(([key, value]) => {

            //? nota: verificamos que sean distinto a un arreglo y objeto !(Array.isArray(value)) && !(value !== null && typeof value === 'object' )

            console.log(value, 'value');
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
                  crearSpans(value, fila['Unidad'], fila['Unidad1'], td, row, fila['Concesion']);
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
                  idFilaAnterior = 'idRow' + index;
                  agregar_fila(fila['Tramites'], index, idFilaAnterior, divFila, fila['Concesion'], fila['Unidad'], fila['Unidad1'], td, row);

               }
            }
         });
      }
      //*enviamos la fila al contenedor del cuerpo de la tabla.
      tbody.appendChild(row);
   });
}
//****************************************************************/
//*funcion encargada de crear las subtabla de la tabla principal.
//***************************************************************/
function agregar_fila(data_tramites, index, idFilaAnterior, div, Concesion, unidad = '', unidad1 = '', td, row) {
   console.log(data_tramites, 'dataTramites agregarFilas');
   let indexTotal = index + 1; //indice de la concesion
   var indice = 1;
   suma = 0;

   let ID_Unidad = unidad['ID'] || unidad['ID_Unidad'] || unidad.ID_Unidad;
   // let ID_Unidad1 = unidad1?.ID_Unidad || unidad1?.ID;
   let ID_Unidad1 = unidad1?.['ID_Unidad'] || unidad1?.['ID'];

   let indexFilaConcesio = index;
   //* tramites contiene la nueva data ya con los elementos excluidos
   let tramites = dataNueva(data_tramites);
   let filaAnterior = document.getElementById(idFilaAnterior);

   //*generando arreglo con los datos diferente a un arreglo y de tamaño>0 con datos distinto de false
   const tramitesValidos = tramites.filter(tramite =>
      !(tramite === false || (tramite && typeof tramite === 'object' && Object.keys(tramite).length === 0))
   );

   // console.log(tramites, 'tramites');
   tramitesValidos.forEach((tramite, ind) => {
      let row_agregada = document.createElement('tr');
      row_agregada.id = 'idTramite' + index + ind;
      // row_agregada.style.width = "100%"; 
      const IdTramiteC = 'idTramite' + index + ind;
      row_agregada.setAttribute('data-id-row', 'idRow' + index);
      row_agregada.className = "filas-tramite table-secondary table-hover";

      const td_trash = document.createElement('td');
      td_trash.className = 'table-secondary';
      td_trash.style.cursor = 'pointer';

      const tdfila = document.createElement('td');
      td_trash.className = 'table-secondary';
      td_trash.style.cursor = 'pointer';

      //*indice de las nuevas filas creadas. //'M_CL'
      if (rolUser.some(role => rol.includes(role))) {
         if ((tramite['ID_Compuesto'].split("_")[2] === 'R') || (tramite['ID_Compuesto'].split("_")[3] === 'CL')) {
            td_trash.innerHTML = '';
            td_trash.style.cursor = 'none';
         } else {
            td_trash.innerHTML = `<span><i class="fa-solid fa-trash deleteTramite"></i></span>`;
         }
      } else {
         td_trash.innerHTML = ``;
         td_trash.style.cursor = 'none';
      }

      row_agregada.appendChild(td_trash);
      tdfila.innerHTML = `${indexTotal}.${ind + 1}`;
      row_agregada.appendChild(tdfila);

      let trashIcon = td_trash.querySelector('.deleteTramite');
      if (trashIcon) {
         trashIcon.addEventListener('click', (e) => {
            //*verificamos que tengamos mas de un elemento
            console.log(tramitesValidos.length);
            if (TOTAL_TRAMITES_X_CONCESION[updateCollection(Concesion)] > 1) {
               //*e trashIconvitando que se recargue
               e.preventDefault();
               Swal.fire({
                  title: '¿ESTÁ SEGURO?',
                  text: '¿QUIERE ELIMINAR ESTE TRAMITE?',
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
                     // console.log(ID_Unidad, ID_Unidad1, 'ID_Unidad ID_Unidad1');
                     fEliminarTramite(Concesion, tramite['ID'], IdTramiteC, indexTotal, (parseInt(tramite['Cantidad_Vencimientos']) * parseFloat(tramite['Monto']).toFixed(2)).toFixed(2), tramite['ID_Compuesto'], ID_Unidad, ID_Unidad1);
                     //*modificando el valor de los tramites en fila principal.
                     let cambioTamañoT = document.getElementById(`tamañoTramite${indexFilaConcesio}`).textContent;
                     document.getElementById(`tamañoTramite${indexFilaConcesio}`).textContent = parseInt(cambioTamañoT, 10) - 1;

                     //*modificamos el conteo principal del total de tramites en el titulo
                     // lengTramites -= 1;
                     // document.getElementById('idLengTramites').textContent=`(${lengTramites})`
                     //*crearSpans(value, unidad , unidad1 , td, row, idConcesion)
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
            if (Object.prototype.hasOwnProperty.call(tramite, key)) {
               if (key === 'Monto') {
                  let monto = +tramite[key]; // Convertir el valor de 'Monto' a número
                  if (!isNaN(monto)) { // Verificar si el valor convertido es un número
                     // Revisamos si el valor es para 'RENOVACIÓN CERTIFICADO DE OPERACIÓN'
                     suma += tramite['Cantidad_Vencimientos'] * monto;
                  }
               }
               tdSub.className = (key === 'Monto') ? 'text-nowrap table-secondary' : 'text-nowrap table-secondary';
               //*si es cambio de MODIFICACIÓN CAMBIO DE UNIDAD el tramite.
               if (tramite['descripcion'] == 'MODIFICACIÓN CAMBIO DE UNIDAD') {
                  var estadoRam = document.getElementById("ID_Estado_RAM").value;
                  // const estadosValidosModificacionUnidad = ['IDE-1', 'IDE-2'];
                  //*siel parametro se manda y hay cambio de unidad se muestra las constancias.
                  if (estadosValidosModificacionUnidad.includes(estadoRam)) {
                     //*se muestra texto si no es url o el cambio de unidad
                     const link = document.createElement('a');
                     link.style.textDecoration = 'none';

                     link.innerHTML = (tramite[key] == 'MODIFICACIÓN CAMBIO DE UNIDAD') ? `<i class="fa-sharp fa-solid fa-eye"></i> ${tramite[key]}` : tramite[key];
                     link.target = "_blank";
                     link.href = $appcfg_Dominio_Raiz + `:140/RenovacionesE/NuevoDictamenR.aspx?Solicitud=${ram}`
                     tdSub.appendChild(link);
                  } else {
                     //*se muestra texto si no es url o el cambio de unidad
                     const link = document.createElement('td');
                     link.innerHTML = tramite[key];
                     tdSub.appendChild(link);
                  }
               } else {
                  //*cuando el tramite no es cambio de unidad.
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
                        tdSub.textContent = tramite[key] // Asigna el valor o una cadena vacía si es undefined/null   
                     }
                  }
               }
               row_agregada.appendChild(tdSub);
            }
         }
      }

      const tdSubMonto = document.createElement('td');
      tdSubMonto.className = 'text-nowrap table-secondary text-end';
      tdSubMonto.textContent = (tramite['Cantidad_Vencimientos'] * tramite['Monto']) + '.00';
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
   addRoT.appendChild(tdTotal).innerHTML = ` Lps.  <span id="Total${indexTotal}"> ${suma.toFixed(2)}</span `

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

   const link = document.createElement('a');
   if (tipo == 'C') {
      link.innerHTML = value + ` <span id="tamañoTramite${index}" style="display: inline-block; border-radius: 15%; background-color:rgb(31, 109, 121); color: white; padding: 3px 7px; font-size: 10px;">${tamaño}</span>`;
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
      link.href = `${ruta}`;
   }

   //*Añadir el enlace al <td>
   td.appendChild(link);
}
//***********************************************************************/
//*funcion que se necarga de crear la tabla de la revision del vehiculo
//**********************************************************************/
function crearSpans(value, unidad = '', unidad1 = '', td, row, idConcesion) {
   // console.log(unidad, 'unidad span');
   // console.log(unidad1, 'unidad1 span');
   var texto = '';
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
      span.id = 'id_placa_' + index + '_' + idConcesion;
      //* Establecer el espacio entre los spans
      span.style.marginRight = "8px";
      //* Cambiar el cursor cuando se pasa sobre el span
      span.style.cursor = "pointer";
      //* Asignar texto al span
      // console.log(index,'index');
      if (index === 0) {
         //?nota: como existe la unidad1 la placa original pasa a ser placa que sale 
         if (unidad1 != undefined && unidad1 != '' && unidad1 != null) {
            span.innerHTML = unidad['ID_Placa'] || unidad['Placa']; //value;
            span.className = "borderPlacaSale";
         } else {
            //?nota: si no hay ynidad 1 solo se muestra la unidad
            span.innerHTML = unidad['ID_Placa'] || unidad['Placa'];  //valor;
            span.className = "borderPlaca";
         }
      } else if (index === 1) {
         //*?nota: es S cuando si hay cambio de unidad?
         if (valor == 'S') {
            //*creando un nuevo span para la flecha
            let arrowSpan = document.createElement('span');
            arrowSpan.innerHTML = `<i id="id_flecha_${index}_${idConcesion}" class="fa-solid fa-arrow-right">&nbsp;</i> `;
            //* Añadir el nuevo span al DOM
            container.appendChild(arrowSpan);
            //*asignando valor a span origiunal 
            //?nota: la placa que entra es la placa de la unidad1
            // console.log(unidad1['ID_Placa'], 'unidad1["ID_Placa"] antes de asignar');
            span.innerHTML = unidad1['ID_Placa'] || unidad1['Placa']; // Segunda parte
            span.className = "borderPlacaEntra";
         }
      } else {
         var estadoRam = document.getElementById("ID_Estado_RAM").value;
         //*siel parametro se manda y hay cambio de unidad se muestra las constancias.
         if (estadosValidosConstancia.includes(estadoRam)) {
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
   });

   //* Añadir el contenedor a la fila (row)
   row.appendChild(container);
}
//*******************************************************************/
//*se encarga de generar la data nuevo sin los elementos excluidos.
//******************************************************************/
function dataNueva(data) {

   console.log(data);
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
function renderPagination(currentPage, data, rowsPerPage, paginationContainer) {
   //*Limpiar la paginación existente
   paginationContainer.innerHTML = '';

   //*creando  ul de paginación
   const paginationList = document.createElement('ul');
   //*creando clase de boostrap
   paginationList.className = 'pagination';
   //*calculando el total d epaginas con el tamaño del arreglo y el numero de elementos por pagina
   const totalPages = Math.ceil(data.length / rowsPerPage);
   //*creando boton de boostrap
   const prevButton = document.createElement('li');

   //*asignando clases al btn
   prevButton.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
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
         //*llamando a la funcion que renderiza el body de l atbala
         renderTableRowst(data, document.querySelector('#idTbody'), rowsPerPage, currentPage);
         //*llamando la paginacion
         renderPagination(currentPage, data, rowsPerPage, paginationContainer);
      }
   });
   //* agragamos el enlace al boton
   prevButton.appendChild(prevLink);
   //*agragamos el boton a la paginacion
   paginationList.appendChild(prevButton);
   //*se encarga de crera botones para cada página
   for (let page = 1; page <= totalPages; page++) {
      //*creamos el elemento li de cadabtn
      const pageItem = document.createElement('li');
      //*añadimos las clases
      pageItem.className = `page-item ${page === currentPage ? 'active' : ''}`;
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
         //*llamamos nmuevamente la tabla y le pasamos los nuevos datos
         renderTableRowst(data, document.querySelector('#idTbody'), rowsPerPage, currentPage);
         //*llamaos la paginacion y le pasamos los nuevos datos
         renderPagination(currentPage, data, rowsPerPage, paginationContainer);
      });
      //*añadimos los enlaces de numeros de paginas
      pageItem.appendChild(pageLink);
      //*añadimos pageItem al contenedos principal
      paginationList.appendChild(pageItem);
   }

   //*creando boton siguiente.
   const nextButton = document.createElement('li');
   //*añadimos clases
   nextButton.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
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

         //*llamamos nuevamnete la tabla que renderiza al body
         renderTableRowst(data, document.querySelector('#idTbody'), rowsPerPage, currentPage);
         //*llamamos la pagiancion nueva y resaltar la página
         renderPagination(currentPage, data, rowsPerPage, paginationContainer);
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
            "Anio": "AÑO",
            "Modelo": "MODELO",
            "Tipo_Vehiculo": "TIPO VEHÍCULO",
            "ID_Formulario_Solicitud": "RAM",
            "Combustible": "COMBUSTIBLE",
            "Alto": "ALTO",
            "Ancho": "ANCHO",
            "Motor": "MOTOR",
            "Chasis": "CHASIS",
            "Estado": "ESTADO",
            "Sistema_Fecha": "FCEHA",
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

               // if (tipo != 'objeto1') {
               //    th.className = "bg-primary-subtle auto";
               //    th.textContent = newKey;
               // } else {
               //    th.className = "bg-primary-subtle auto";
               //    th.style.textAlign = "center";  // Centra el contenido horizontalmente
               //    th.style.padding = "8px"; // Puedes agregar un poco de padding si lo deseas para el espaciado
               //    th.innerHTML = '<i class="fa-solid fa-arrow-right" style="font-size: 18px;"></i>';
               // }

               td.textContent = element;

               let placa = obj['ID_Placa'] || obj['Placa']
               if (key === 'ID_Placa' || 'Placa') {
                  titulo.innerHTML = tobjeto === 1
                     ? (tipo === 'objeto' ? 'PLACA QUE SALE: ' : 'PLACA QUE ENTRA: ') +
                     `<button type="button" class="btn btn-${tipo === 'objeto' ? 'danger' : 'success'}" data-bs-dismiss="modal">${placa}</button>`
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
         return `<div class="alert alert-warning">NO HAY DATOS PARA MOSTRAR</div>`;
      }
   }

   const contenedorTablas = document.createElement('div');
   contenedorTablas.className = 'd-flex';

   if (objeto) contenedorTablas.appendChild(crearTabla(objeto, 'objeto', tobjeto));
   if (objeto1) contenedorTablas.appendChild(crearTabla(objeto1, 'objeto1', tobjeto));

   bodymodal.appendChild(contenedorTablas);

   if (!objeto && !objeto1) {
      bodymodal.innerHTML = `<div class="alert alert-warning">NO HAY DATOS PARA MOSTRAR</div>`;
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
   let filas = [];//*instanciando filas.
   let concesiones = [];//*instanciando arreglo de concesiones
   let tramites = 0;
   let montos = [];

   checkboxes.forEach(checkbox => {
      if (checkbox.checked == true) {
         let texto = checkbox.value.split('/');
         filas.push(texto[0]);  //* La primera parte (antes del '/')
         concesiones.push(texto[1]);  //* La segunda parte (después del '/')
      }
   });

   concesiones.forEach(concesion => {

   })

   f_FetchDeletConcesiones(concesiones, filas);
}
//******************************************************************************/
//*funcion encargada de verificar la decision del usuario y mostar las alertas.
//******************************************************************************/
function f_FetchDeletConcesiones(idConcesiones, filas) {
   // console.log(idConcesiones, 'consession');
   let tipoAccion = false;
   if (idConcesiones.length != 0) {
      mostrarAlerta(tipoAccion = true, idConcesiones, filas)
   } else {
      mostrarAlerta(tipoAccion, idConcesiones, filas);
   }
}
//*****************************************************************************/
//*funcion encargada de mostrar la alerta spara evr si esta seguro de eliminar.
//*****************************************************************************/
function mostrarAlerta(tipoAccion, idConcesiones, filas) {
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
         text: '¿QUIERE ELIMINAR LAS CONCESIONES?',
         icon: 'warning',
         showCancelButton: true,
         confirmButtonText: 'SÍ, ELIMINAR',
         cancelButtonText: 'CANCELAR'
      }).then((result) => {
         if (result.isConfirmed) {
            //* si confirma que esta seguro de eliminar llamomos la funcion para que elimine de la base de datos.
            eliminar(idConcesiones, filas);
         } else {
            //* si cancela la eliminacion limpiamos la seleccion de los check
            //* Obtener todos los checkboxes con la clase 'check_trash'
            const checkboxes = document.querySelectorAll('.check_trash');
            //*Recorro todos los checkboxes y desmarco todos
            checkboxes.forEach(checkbox => {
               checkbox.checked = false;
            });
         }
      });
   }
}
//************************************************************/
//*encargada de eliminar las concesione en la base de datos.
//***********************************************************/
function eliminar(idConcesiones, idRows, Monto = 0) {
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
         // console.log(datos);
         if (typeof datos.error == "undefined" && datos.Borrado == true) {
            console.log('Monto Antes');
            let respuestareduceConcesionNumber =  reduceConcesionNumber(idConcesiones);
            preDeleteAutoComplete(idConcesiones, 'CONCESION');
            //* si respuesta existe quiere decir que la funcion clearCollection modifico el arreglo.
            let respuestaclearCollections = clearCollections(idConcesiones);
            //*si existe mandamos alerta
            if (respuestaclearCollections) {
               // mostrarData(concesionNumber);
               sendToast(
                  "LA CANTIDAD DE " + idConcesiones.length + "  CONCESION(ES) SELECCIONADA(S) SE BORRARON EXITOSAMENTE ",
                  $appcfg_milisegundos_toast,
                  "",
                  true,
                  true,
                  "top",
                  "right",
                  true,
                  $appcfg_background_toast,
                  function () { },
                  "success",
                  $appcfg_pocision_toast,
                  $appcfg_icono_toast
               );
               //**********************************************************************/
               //* INICIO: Borrando La Linea de la Pantalla que contiene la concesion
               //**********************************************************************/
               let contador = idRows.length
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
               //**************************/
               //* FINAL: Borrando La Linea de la Pantalla que contiene la concesion
               //**************************/
               const elem = document.getElementById("idLengConcesion");
               animateValue(elem, parseInt(document.getElementById("idLengConcesion").textContent), parseInt(parseInt(document.getElementById("idLengConcesion").textContent) - parseInt(respuestareduceConcesionNumber.total_concesiones)), 6000,'highlightRed',parseInt,0);
               const elemt = document.getElementById("idLengTramites");
               animateValue(elemt, parseInt(document.getElementById("idLengTramites").textContent), parseInt(parseInt(document.getElementById("idLengTramites").textContent) - parseInt(respuestareduceConcesionNumber.total_tramites)), 9000,'highlightGris',parseInt,0);
               let total_pagar_ele = document.getElementById("Total_A_Pagar");
               let Total_A_Pagar = parseFloat(document.getElementById("Total_A_Pagar").innerHTML).toFixed(2);
               animateValue(total_pagar_ele, Total_A_Pagar, parseFloat(Total_A_Pagar - respuestareduceConcesionNumber.total).toFixed(2), 13000);
            }
         } else {
            if (typeof datos.error != "undefined") {
               //let errormsgcash='NO SE PUDO BORRAR LAS CONCESIONES';
               fSweetAlertEventNormal(
                  datos.errorhead,
                  datos.error + "- " + errormsgcash,
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
//***************************************************************************************************/
//*funcion encargada de actulaizar las iconos de la constancia de la placas si hay cambio de unidad.
//**************************************************************************************************/
function actualizarContenido(span, index, unidad1) {
   //*si el indix es 2 es cambio de unidad y ID_Memo false que no tiene constancias generada.
   if (index === 2 && unidad1 != undefined && unidad1 != null && (unidad1['ID_Memo'] === false || unidad1['ID_Memo'] === 'false')) {
      span.innerHTML = `<i id="est_const" class="fas fa-cog"></i> `;
      //*si el indix es 1 es cambio de unidad y ID_Memo tiene datos si tiene constancia entonces la visualizamos
   } else if (index === 2 && unidad1 != undefined && unidad1 != null && (unidad1['ID_Memo'] !== false || unidad1['ID_Memo'] !== 'false')) {
      //* En caso de que 'ID_Memo' no sea false y tenga un ID_Memo.
      span.innerHTML = `<i id="est_const" class="fas fa-file-pdf"></i>`;
   }
}
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
   let $placas = ["TB", "TC", "TE", "TP", "TT", "TR"];

   //* Verificar si $placabegin NO está en el array $placas
   if (($placas.includes($placabegin))) {
      //*id_memo si genero_contancia es true quiere decir que ya se genero y ponemos 
      //*Memo global que tiene el id_memo que se creo al generar constancia si no es el que se pasa por parametro por que ya existe
      //*Comprobamos si el ID_Memo es una cadena 'true' o un valor booleano true
      if (ID_Memo === 'false' || ID_Memo === false) {
         //* Llamar a la función generarConstancia
         generarConstancia(unidad1, idConcesion);
      } else {
         //*Llamar a la función verConstancia pasando el valor de 'ID_Memo'
         verConstancia(ID_Memo);
      }
   } else {
      alert('La placa no esta dentro de las placas permitidas que son:"TB", "TC", "TE", "TP", "TT", "TR",');
   }
}
//******************************************/
//*cambio de unidad y placa distinta 
//**************************************
function generarConstancia(unidad1, idConcesion) {
   //*globales.
   var ID_Usuario = document.getElementById("ID_Usuario").value;
   var User_Name = document.getElementById("User_Name").value;

   var form_data = new FormData();
   form_data.append("action", "save-ingreso-constancias");
   form_data.append("Referencia", document.getElementById("RAM").value); //$('#numpreforma').val()
   form_data.append("Placa_Entra", unidad1['ID_Placa']);
   form_data.append("Marca_Entra", unidad1['ID_Marca']);
   form_data.append("Tipo_Entra", unidad1['Desc_Marca']);
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
         if (dataserver.status == 2) {
            fSweetAlertEventNormal(
               "NO SE SALVO LA CONSTANCIA",
               "La sesión actual a expirado favor inice sesión nuevamente",
               "warning"
            );
         } else {
            if (dataserver.status == 3) {
               Swal.fire({
                  title: "NO SE SALVO LA CONSTANCIA",
                  text: "Ya habia sido emitida una constancia para el CHASIS NO. <b>" + document.getElementById('Chasis').firstChild.data + '</b> en la fecha ' + dataserver.Sistema_Fecha + ' con el número de memo ' + dataserver.ID_Memo,
                  icon: "warning",
                  html: true,
                  showCancelButton: false,
                  closeOnConfirm: true,
                  showLoaderOnConfirm: true,
                  confirmButtonColor: "#DD6B55",
                  confirmButtonText: "VER CONSTANCIA EMITIDA PREVIAMENTE"
               }, function () {
                  verConstancia(dataserver.ID_Memo);
               });
            } else {
               $link = $appcfg_Dominio_Raiz + ':288/PDF_Constancia.php?ID_Memo=' + dataserver.ID_Memo + '&FSL=' + document.getElementById("RAM").value;
               //*llamando a la funcion cambiar link para modificar si ya esta generada la constancia.

               cambiarLink(dataserver.ID_Memo, idConcesion); // Llama a la función adicional
               Swal.fire({
                  title: "CONTANCIA SALVADA SATISFACTORIAMENTE",
                  text: "CONSTANCIA GENERADA CORRECTAMENTE CON EL NUMERO DE MEMO " + dataserver.ID_Memo,
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
               console.log('no hay unidad => modificarIdMemo');
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
}
//**************************************************/
//*genra certificado dependiendo de las concesiones.
//**************************************************/
function tipoConcesion(esCarga, esCertificado, Concesion_Encriptada, Permiso_Explotacion_Encriptado) {
   //  console.log(esCarga, esCertificado, Concesion_Encriptada, Permiso_Explotacion_Encriptado);
   const dominio = `${$appcfg_Dominio_Raiz}:172/api_rep.php?action=`;

   let link = {};  // Usamos un objeto en lugar de un arreglo

   // Es Renovación de Certificado
   if (esCertificado) {
      // console.log('esCertficado');

      if (esCarga) { // CARGA
         const rutacertificado = `${dominio}get-PDFCertificado-Carga&Certificado=${Concesion_Encriptada}`;
         const rutapermisoexplotacion = `${dominio}get-PDFPermisoExp-Carga&Permiso=${Permiso_Explotacion_Encriptado}`;
         link['rutacertificado'] = rutacertificado;
         link['rutapermisoexplotacion'] = rutapermisoexplotacion;

      } else { // Otro caso (PAS)
         const rutacertificado = `${dominio}get-PDFCertificado&Certificado=${Concesion_Encriptada}`;
         const rutapermisoexplotacion = `${dominio}get-PDFPermisoExp-Pas&Permiso=${Permiso_Explotacion_Encriptado}`;
         link['rutacertificado'] = rutacertificado;
         link['rutapermisoexplotacion'] = rutapermisoexplotacion;
      }

   } else { // Es Renovación de Permisos Especiales
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
