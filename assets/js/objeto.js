
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
   //*se asigna el arreglo que se envia a la funcion principal.
   var dataGlobal = [];
   var tituloGlobal = '';
   const encabezadoExclui = new Set(['Tramites', 'Unidad', 'Unidad1', 'ID_Memo', 'Concesion_Encriptada', 'esCarga', 'esCertificado', 'Permiso_Explotacion_Encriptado']);
   
   var estadoRam = document.getElementById("ID_Estado_RAM").value;
   
   function mostrarData(data, contenedorId = 'tabla-container', title = 'Titulo de la Tabla') {
   
      tituloGlobal = title;
      dataGlobal = [...data];
   
      //*creando elementos unicips con set para eliminar repeticion
      let uniqueKeys = new Set(); //*solo permite elementos unicos.
      let resultado = dataGlobal.filter(item => item !== false && item !== "" && item !== null);
      //*filtramos los campos que no queremos mostrar
      //*creamos arreglo con la informacion a exclir
      let dataExcluir = ['ID_Formulario_Solicitud'];
   
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
      var lengTramites = 0;
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
                        totalMonto += parseFloat(element[data]);
                     }
                  }
               });
            }
            break;
         }
      })
   
      //*ENCABEZADOS DEL MODAL
      let titulo = ` ${tituloGlobal} (${dataGlobal.length}) <i class="fa-solid fa-cube text-secondary"></i> ${ram} <i class="fa-duotone fa-solid fa-folder-open text-secondary"></i> TRAMITES (${lengTramites}) <i class="fa-solid fa-money-bill-trend-up text-secondary"></i> TOTAL LPS.${totalMonto.toFixed(2)}`;
      //!llamamos a la fucnion TablaBusquedaInterna
      TablaBusquedaInterna(filtrarData, titulo, encabezado, contenedorId);
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
      table.className = 'table table-hover  mb-5 ';
      //*creamos el contenedor del encabezado de la tabla.
      const thead = document.createElement('thead');
      //*las clases del encabezado de la tabla
      thead.className = 'headTable table-primary';
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
      // console.log(data, 'data');
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
         <input class="form-check-input check_trash"  type="checkbox" value="${index}/${fila['Concesion']}" id="id_trash_${row.id}"> 
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
   
               let td = null;
               if (!(Array.isArray(value)) && !(value !== null && typeof value === 'object') && (key !== 'Unidad1') &&
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
                     td.style.cursor = 'none';
                     td.setAttribute("colspan", "2");
   
                     //!funcion que muestra la revision del vehiculo
                     crearSpans(value, fila['Unidad'], fila['Unidad1'], td, row, fila['Concesion']);
                  } else {
                     var estadoRam = document.getElementById("ID_Estado_RAM").value;
                     const estadosValidos = ['IDE-1', 'IDE-2', 'IDE-5'];
   
                     if (key == 'Concesion') {
                        // console.log(estadoRam + fila['ID_Expediente']);
                        // console.log(estadosValidos.includes(estadoRam), 't');
   
                        if (estadosValidos.includes(estadoRam) || fila['ID_Expediente']) {
                           linkConcesiones(td, linksConcesion['rutapermisoexplotacion'], value);
                        } else {
                           td.textContent = value;
                        }
                     } else {
                        if (key == 'Permiso_Explotacion') {
                           //*asignamos el texto de cada columna
                           if (estadosValidos.includes(estadoRam) || fila['ID_Expediente']) {
                              linkConcesiones(td, linksConcesion['rutapermisoexplotacion'], value);
                           } else {
                              td.textContent = value;
                           }
                        } else {
                           //*asignamos el texto de cada columna
                           td.innerHTML = value;
                        }
                     }
                  }
   
                  //*añadimos las columnas a la fila
                  row.appendChild(td);
   
                  //*para pasar data y desplegar datos en fila
                  row.onclick = (event) => {
                     //*para evitar la propagación del click.
                     event.stopPropagation();
                     agregarFilas(fila['Tramites'], 'idRow' + index, tbody, index, fila['Concesion']);
                  }
               }
            });
   
            //*añadimos la columna para la accion
            const td = document.createElement('td');
   
            //*asignamos la manito a la fila
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
            Object.entries(fila).forEach(([key, value]) => {
   
               //? nota: verificamos que sean distinto a un arreglo y objeto !(Array.isArray(value)) && !(value !== null && typeof value === 'object' )
   
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
   
                     //!funcion que muestra la revision del vehiculo.
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
                  //*para pasar data y desplegar datos en fila.
                  row.onclick = (event) => {
                     event.stopPropagation();
                     agregarFilas(fila['Tramites'], 'idRow' + index, tbody, index, fila['Concesion']);
                  }
               }
            });
         }
         //*enviamos la fila al contenedor del cuerpo de la tabla.
         tbody.appendChild(row);
      });
   }
   //******************************************************************************************************/
   //*funcion encargada de colocar el link para los documentos del certficado y permiso de explotacion
   //******************************************************************************************************/
   function linkConcesiones(td, ruta, value) {
      console.log(ruta);
      const link = document.createElement('a');
      link.textContent = value;
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
      var texto = '';
   
      if (unidad1 != '') {
         texto = value + '->' + 's';
      } else {
         texto = value;
      }
   
      //* Separar el texto por el delimitador '->'
      //?nota: "aplit()", para dividir en partes por el elemento o signo.
      const partes = texto.split("->");
   
      //* Obtener el contenedor donde se agregarán los span
      const container = td;
   
      //* Crear 3 span individuales
      partes.forEach((valor, index) => {
         // console.log(valor, 'valor');
         //* Crear el span dinámicamente para cada parte del texto
         const span = document.createElement("span");
         span.id = 'id_' + index;
         //* Establecer el espacio entre los spans
         span.style.marginRight = "8px";
   
         //* Cambiar el cursor cuando se pasa sobre el span
         span.style.cursor = "pointer";
         //* Asignar texto al span
         if (index === 0) {
            span.innerHTML = valor; // Primer parte
            if (unidad1 != '') {
               span.className = "borderPlacaSale";
            } else {
               span.className = "borderPlaca";
            }
         } else if (index === 1) {
            if (valor != 's') {
               //*creando un nuevo span para la flecha
               let arrowSpan = document.createElement('span');
               arrowSpan.innerHTML = '<i class="fa-solid fa-arrow-right"></i> ';
               //* Añadir el nuevo span al DOM
               container.appendChild(arrowSpan);
               //*asignando valor a span origiunal 
               span.innerHTML += valor; // Segunda parte
               span.className = "borderPlacaEntra";
            }
         } else {
            //!funcion encargada de actualizar el icono de la constancia de generar a ver constancia
            actualizarContenido(span, index, unidad1);
         }
   
         //* Evento de clic para mostrar la constancia
         span.onclick = function (event) {
            //* Evitar que el evento se propague a otros elementos
            event.stopPropagation();
            //*Dependiendo de la placa seleccionada, pasamos los valores necesarios
            if (index != 2) {
               //*mostramos la revision de la placa seleccionada.
               mostrarUnidades(unidad, unidad1, valor, index);
            } else {
               //*llamamos la funcion constancia que decide si genermaos o vemos al constancia.
               constancia(unidad1['ID_Placa'], unidad1['ID_Memo'], unidad1, index, span, idConcesion);
            }
         };
         //* Añadir el span al contenedor
         container.appendChild(span);
      });
   
      //* Añadir el contenedor a la fila (row)
      row.appendChild(container);
   }
   //****************************************************************/
   //*funcion encargada de crear las subtabla de la tabla principal.
   //***************************************************************/
   
   function agregarFilas(data, idRow, tbody, index, Concesion) {
      var indice = 1;
      suma = 0;
      let idT = 'idTramite' + (indice) + '.' + (index);
      //* tramites contiene la nueva data ya con los elementos excluidos
      let tramites = dataNueva(data);
      //* elemento de la fila al cual apuntamos para poner las nuevas filas debajo
      const filaReferencia = document.getElementById(idRow);
   
      //* asignando los atributos data-visible
      if (!filaReferencia.getAttribute('data-visible')) {
         filaReferencia.setAttribute('data-visible', 'false');
      }
   
      //* Estado actual de visibilidad
      const filaEstado = filaReferencia.getAttribute('data-visible');
   
      //* Verificamos si la fila ya está visible o no
      if (filaEstado === 'true') {
         //* Cambiar el atributo 'data-visible' a 'false' para marcar como oculto
         filaReferencia.setAttribute('data-visible', 'false');
   
         //* Ocultar las filas generadas dinámicamente asociadas a esta fila
         filasTramites.forEach(fila => {
            if (fila.getAttribute('data-id-row') === idRow) {
               fila.style.display = 'none';  //* Ocultar la fila correspondiente
            }
         });
         //* Ocultar la fila total (addRoT)
   
         const filaTotal = filasTramites.find(fila => fila.id.includes(idT));
         if (filaTotal) {
            filaTotal.style.display = 'none';  //* Ocultar la fila de total
         }
   
      } else {
         //* Cambiar el atributo 'data-visible' a 'true' para marcar como visible
         filaReferencia.setAttribute('data-visible', 'true');
   
         //* Primero eliminamos las filas previas generadas dinámicamente para esta fila
         filasTramites.forEach(fila => {
            if (fila.getAttribute('data-id-row') === idRow) {
               fila.remove();  //* Eliminar las filas del DOM
            }
         });
   
         //* Limpiamos el array que contiene las filas generadas para esta fila
         filasTramites = filasTramites.filter(fila => fila.getAttribute('data-id-row') !== idRow);
   
         //* Asignamos las nuevas filas
         //?nota: "createDocumentFragment()", crear un fragmento de documento vacío, que actúa como un contenedor ligero para nodos del DOM. lo que significa que las modificaciones realizadas en él no causan cambios inmediatos en la estructura del documento visible.
         const fragmento = document.createDocumentFragment();
         //* Recorremos los trámites que contiene la data
   
         //*generando arreglo con los datos diferente a un arreglo y de tamaño>0 con datos distinto de false
         const tramitesValidos = tramites.filter(tramite =>
            !(tramite === false || (tramite && typeof tramite === 'object' && Object.keys(tramite).length === 0))
         );
         // console.log(tramitesValidos, 'tramitesValidos ');
         tramitesValidos.forEach((obj, i) => {
            // console.log(obj);
            //* Creamos la fila
            const addRow = document.createElement('tr');
            //* Asignamos un ID único para cada fila
            // console.log('antes','idTramite' + (index) + '.' + (i));
            let IdTramiteC = 'idTramite' + (index) + '.' + (i)
            addRow.id = IdTramiteC
            //* Asignamos un atributo para identificar la fila
            addRow.setAttribute('data-id-row', idRow);
            //* Creamos una clase para identificar estas filas
            addRow.classList.add('filas-tramite');
   
            //* Agregar las celdas correspondientes
            let tdd1 = document.createElement('td');
            let tdd = document.createElement('td');
            // console.log(obj, 'obj');
   
            addRow.appendChild(tdd1).innerHTML = (rolUser.some(role => rol.includes(role))) ? `<i class="fa-solid fa-trash deleteTramite"></i>` + '  ' + (index + 1) + '.' + (indice) : (index + 1) + '.' + (indice);;
            tdd1.className = 'table-secondary';
            tdd1.style.cursor = 'pointer';
   
            let trashIcon = addRow.querySelector('.deleteTramite');
            if (trashIcon) {
               trashIcon.addEventListener('click', (e) => {
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
                        // const tramitesValidos = tramites.filter(tramite => tramite !== false);
                        //*verificamos que tengamos mas de un elemento
                        if (tramitesValidos.length > 1) {
                           //* Eliminar Registro de Tramite
                           fEliminarTramite(Concesion, obj['ID'], IdTramiteC);
                        } else {
                           Swal.fire('¡ALERTA!', 'NO SE PUEDE DEJAR SIN NINGÚN TRÁMITE.', 'warning');
                        }
                     }
                  });
               });
            }
            // addRow.appendChild(tdd).textContent = (index + 1) + '.' + (indice);
            tdd.className = 'table-secondary';
            indice++;
            for (let key in obj) {
   
               if (key === 'Monto') {
                  let monto = +obj[key];
                  if (!isNaN(monto)) {
                     suma += monto;
                  }
               }
   
               const link = document.createElement('a');
               const tdSub = document.createElement('td');
               tdSub.className = (key === 'Monto') ? 'text-nowrap table-secondary text-end' : 'text-nowrap table-secondary';
               //*Esto quitará el subrayado
               link.style.textDecoration = 'none';
   
               //*si es cambio de MODIFICACIÓN CAMBIO DE UNIDAD el tramite.
   
               if (obj[key] == obj['descripcion']) {
                  tdSub.setAttribute("colspan", "3");
               }
               if (obj['descripcion'] == 'MODIFICACIÓN CAMBIO DE UNIDAD') {
                  //*se muestra texto si no es url o el cambio de unidad
                  link.innerHTML = (obj[key] == 'MODIFICACIÓN CAMBIO DE UNIDAD') ? `<i class="fa-sharp fa-solid fa-eye"></i> ${obj[key]}` : obj[key];
                  link.target = "_blank";
                  // if (link.tagName === "TD" || link.tagName === "TH") {
   
                  // }
   
                  link.href = $appcfg_Dominio_Raiz + `:140/RenovacionesE/NuevoDictamenR.aspx?Solicitud=${ram}`;
                  // link.href = `https://satt2.transporte.gob.hn:285/ram/index.php?RAM=RAM-000000063-2024`;
                  tdSub.appendChild(link);
   
               } else {
                  //*cuando el tramite no es cambio de unidad.
                  tdSub.textContent = obj[key];
               }
   
               //*se insertan las colunmas siempre que no sea url y cambio de unidad.
               addRow.appendChild(tdSub);
            }
            fragmento.appendChild(addRow);
   
            //* Añadimos la fila al array de filas dinámicas
            filasTramites.push(addRow);
         });
   
         index++;
         //***************************/
         //* Fila de total (addRoT)
         //***************************/
         let addRoT = document.createElement('tr');
         addRoT.id = 'idTramite' + (indice) + '.' + (index);
         addRoT.setAttribute('data-id-row', idRow);
         addRoT.classList.add('filas-tramite');
   
         let tdTotalTile = document.createElement('td');
         tdTotalTile.className = 'text-nowrap fw-bold text-end  ms-5 text-table';
         tdTotalTile.setAttribute('colspan', '6',);
         addRoT.appendChild(tdTotalTile).textContent = 'TOTAL   ' + ' ';
   
         let tdTotal = document.createElement('td');
         tdTotal.className = 'text-nowrap  fw-bold text-end text-table';
         addRoT.appendChild(tdTotal).textContent = 'Lps.  ' + suma.toFixed(2);
   
         fragmento.appendChild(addRoT);
   
         filasTramites.push(addRoT);
   
         //* Insertamos las filas en el DOM después de la fila de referencia
         filaReferencia.parentNode.insertBefore(fragmento, filaReferencia.nextSibling);
   
         //* Mostrar las filas agregadas (tanto las de trámites como la fila de total)
         filasTramites.forEach(fila => {
            if (fila.getAttribute('data-id-row') === idRow) {
               fila.style.display = '';  //* Mostramos la fila correspondiente
            }
         });
      }
   }
   
   //*******************************************************************/
   //*se encarga de generar la data nuevo sin los elementos excluidos.
   //******************************************************************/
   function dataNueva(data) {
   
      //*Arreglo con la data a excluir
      let dataExcluir = ["ID_Compuesto",
         "Codigo",
         "ID_Categoria",
         "ID_Tipo_Servicio",
         "ID_Modalidad",
         "ID_Clase_Servico"];
   
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
   
   
   function mostrarUnidades(unidad = '', unidad1 = '', valor, index, est_constancia) {
      // Instanciamos el modal
      var myModalPlaca = new bootstrap.Modal(document.getElementById('modalPlaca'));
      myModalPlaca.show();
   
      // Título del modal
      var title = document.getElementById('modalPlacaLabel').innerHTML = `DATOS DEL VEHÍCULO CON PLACA`;
   
      // Cuerpo del modal
      var bodymodal = document.getElementById('modalBodyPlaca');
      bodymodal.innerHTML = '';
   
      var objeto = '';
      var objeto1 = '';
   
      // Asignación de valores a objeto y objeto1
      if (unidad !== 'undefined' && unidad1 !== 'undefined') {
         objeto = unidad;
         objeto1 = unidad1;
      } else {
         objeto = ((index == 0) ? unidad : unidad1);
      }
   
      // Función para crear una tabla a partir de un objeto
      function crearTabla(objeto, tipo) {
         if (objeto && Object.keys(objeto).length > 0) {
            let obj = Object.fromEntries(Object.entries(objeto).filter(([key, value]) => isNaN(Number(key))));
   
            const divTitulo = document.createElement('div');
            divTitulo.className = 'mb-3';
   
            const titulo = document.createElement('h5');
   
            const table = document.createElement('table');
            table.className = 'table table-hover mb-5';
   
            const thead = document.createElement('thead');
            thead.className = 'headTable table-primary';
   
            const tbody = document.createElement('tbody');
            tbody.className = 'table-group-divider';
            tbody.id = 'idTbody';
   
            const renombrarKey = {
               "ID_Formulario_Solicitud": "RAM",
               "ID": "ID",
               "RTN_Propietario": "RTN PROPIETARIO",
               "Nombre_Propietario": "PROPIETARIO",
               "ID_Placa": "PLACA",
               "ID_Marca": "MARCA",
               "Desc_Marca": "DSC. MARCA",
               "Anio": "AÑO",
               "Modelo": "MODELO",
               "Tipo_Vehiculo": "TIPO VEHÍCULO",
               "ID_Color": "COLOR",
               "Desc_Color": "DSC. COLOR",
               "Motor": "MOTOR",
               "Chasis": "CHASIS",
               "Estado": "ESTADO",
               "Sistema_Fecha": "FECHA",
               "Permiso_Explotacion": "PERMISO EXPLOTACIÓN",
               "VIN": "VIN",
               "Combustible": "COMBUSTIBLE",
               "Alto": "ALTO",
               "Ancho": "ANCHO",
               "Largo": "LARGO",
               "Capacidad_Carga": "CAPACIDAD",
               "Peso_Unidad": "PESO",
               "ID_Placa_Antes_Replaqueo": "PLACA ANTES DE REPLAQUEO"
            };
   
            // Estilos CSS en línea para asegurar el mismo ancho y grosor
            const tableStyle = 'width: 100%; border: 1px solid #dee2e6; table-layout: fixed;';
            const tdThStyle = 'border: 1px solid #dee2e6; padding: 8px; text-align: left;';
   
            for (const key in obj) {
               if (Object.prototype.hasOwnProperty.call(obj, key)) {
                  const element = obj[key];
   
                  const tr = document.createElement('tr');
   
                  const th = document.createElement('th');
                  th.style.cssText = tdThStyle;  // Aplica el estilo
                  const td = document.createElement('td');
                  td.style.cssText = tdThStyle;  // Aplica el estilo
   
                  const newKey = renombrarKey[key] || key;
   
                  if (tipo != 'objeto1') {
                     th.className = "bg-primary-subtle auto";
                     th.textContent = newKey;
                  } else {
   
                     th.className = "bg-primary-subtle auto";
                     th.style.textAlign = "center";  // Centra el contenido horizontalmente
                     th.style.padding = "8px"; // Puedes agregar un poco de padding si lo deseas para el espaciado
                     th.innerHTML = '<i class="fa-solid fa-arrow-right" style="font-size: 18px;"></i>';
   
                  }
   
                  td.textContent = element;
   
                  if (key == 'ID_Placa') {
                     titulo.innerHTML = tipo === 'objeto' ? 'PLACA QUE SALE: ' + ` <button type="button" class="btn btn-danger" data-bs-dismiss="modal">${element}</button>` : 'PLACA QUE ENTRA: ' + ` <button type="button" class="btn btn-success" data-bs-dismiss="modal">${element}</button>`;
                     divTitulo.appendChild(titulo);
                  }
   
                  tr.appendChild(th);
                  tr.appendChild(td);
                  tbody.appendChild(tr);
               }
            }
   
            table.appendChild(tbody);
            table.style.cssText = tableStyle;  // Aplica el estilo a la tabla
            divTitulo.appendChild(table);
            return divTitulo;
         } else {
            return `<div class="alert alert-warning" role="alert">NO HAY DATOS, NO SE PUEDE MOSTRAR LA REVISIÓN DEL VEHÍCULO CON PLACA <strong> " ${valor} " </strong></div>`;
         }
      }
   
      const contenedorTablas = document.createElement('div');
      contenedorTablas.className = 'd-flex';
   
      if (objeto && Object.keys(objeto).length > 0) {
         const tabla1 = crearTabla(objeto, 'objeto');
         contenedorTablas.appendChild(tabla1);
      }
   
      if (objeto1 && Object.keys(objeto1).length > 0) {
         const tabla2 = crearTabla(objeto1, 'objeto1');
         contenedorTablas.appendChild(tabla2);
      }
   
      bodymodal.appendChild(contenedorTablas);
   
      if (!objeto && !objeto1) {
         bodymodal.innerHTML = `<div class="alert alert-warning" role="alert">NO HAY DATOS, NO SE PUEDE MOSTRAR LA REVISIÓN DEL VEHÍCULO CON PLACA <strong> " ${valor} " </strong></div>`;
      }
   }
   
   //**********************************************************************************************/
   //*función encargada de selecionar todas las concesiones y desmarcar si precionan el principal
   //**********************************************************************************************/
   
   function seleccionarTodos() {
      //console.log('seleccionar todo');
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
      const checkboxes = document.querySelectorAll('.check_trash');
      let filas = [];//*instanciando filas.
      let concesiones = [];//*instanciando arreglo de concesiones
   
      checkboxes.forEach(checkbox => {
         if (checkbox.checked == true) {
            let texto = checkbox.value.split('/');
   
            filas.push(texto[0]);  //* La primera parte (antes del '/')
            concesiones.push(texto[1]);  //* La segunda parte (después del '/')
         }
      });
   
      f_FetchDeletConcesiones(concesiones)
      //console.log(filas, concesiones);
   }
   //******************************************************************************/
   //*funcion encargada de verificar la decision del usuario y mostar las alertas.
   //******************************************************************************/
   function f_FetchDeletConcesiones(idConcesiones) {
      // console.log(idConcesiones, 'consession');
      let tipoAccion = false;
      if (idConcesiones.length != 0) {
         tipoAccion = true;
   
         mostrarAlerta(tipoAccion, idConcesiones)
   
      } else {
         mostrarAlerta(tipoAccion, idConcesiones);
   
      }
   }
   //*****************************************************************************/
   //*funcion encargada de mostrar la alerta spara evr si esta seguro de eliminar.
   //*****************************************************************************/
   function mostrarAlerta(tipoAccion, idConcesiones) {
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
               eliminar(idConcesiones);
               Swal.fire('¡ELIMINADO!', 'LOS REGISTROS HAN SIDO ELIMADOS.', 'success');
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
   function eliminar(idConcesiones) {
      // *URL del Punto de Acceso a la API
      const url = $appcfg_Dominio + "Api_Ram.php";
      let fd = new FormData(document.forms.form1);
      //*Adjuntando el action al FormData
      fd.append("action", "delete-concesion-preforma");
      //*Adjuntando el idApoderado al FormData
      fd.append("idConcesiones", JSON.stringify(idConcesiones));
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
            console.log(datos);
            if (typeof datos.error == "undefined" && datos.Borrado == true) {
               //* si respuesta existe quiere decir que la funcion clearCollection modifico el arreglo.
               let respuesta = clearCollections(idConcesiones);
               //*si existe mandamos alerta
               if (respuesta) {
                  // mostrarData(concesionNumber);
                  sendToast(
                     "LAS CONCESIONES SELECCIONADA SE BORRARON EXITOSAMENTE ",
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
               }
            } else {
               if (typeof datos.error != "undefined") {
                  //let errormsgcash='NO SE PUDO BORRAR LAS CONCESIONES';
                  fSweetAlertSelect(
                     datos.errorhead,
                     datos.error + "- " + errormsgcash,
                     "error"
                  );
               }
            }
         })
         .catch((error) => {
            console.log(error, 'error');
            fSweetAlertEventSelect(
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
      // console.log(unidad1['ID_Memo'], 'idmemo de la unidad');
      if (index === 2 && (unidad1['ID_Memo'] === false || unidad1['ID_Memo'] === 'false')) {
         // span.setAttribute("data-id", "2");
         span.innerHTML = `<i id="est_const" class="fa-solid fa-file-circle-plus"></i>`;
         //*si el indix es 2 es cambio de unidad y ID_Memo tiene datos si tiene constancia entonces la visualizamos
      } else if (index === 2 && (unidad1['ID_Memo'] !== false || unidad1['ID_Memo'] !== 'false')) {
         //* En caso de que 'ID_Memo' no sea false y tenga un ID_Memo.
         span.innerHTML = `<i id="est_const" class="fa-solid fa-file-import"></i>`;
      }
   }
   
   //***************************************************************************************/
   //*funcion encargada de decidir si se mira o se genera la constancia y evaluar la placa 
   //***************************************************************************************/
   function constancia(placa, ID_Memo, unidad1, index, span, idConcesion) {
   
      //console.log(idConcesion, 'constancia');
      // console.log(ID_Memo, 'ID_Memo');
      let $placabegin = '';
      //* Obtener los primeros dos caracteres de la placa
      if (placa !== undefined && placa !== null) {
         $placabegin = placa.slice(0, 2);
      } else {
         console.log(placa, 'placa');
      }
   
      let $placas = ["TB", "TC", "TE", "TP", "TT", "TR"];
   
      //* Verificar si $placabegin NO está en el array $placas
      if (($placas.includes($placabegin))) {
         //*id_memo si genero_contancia es true quiere decir que ya se genero y ponemos 
         //*Memo global que tiene el id_memo que se creo al generar constancia si no es el que se pasa por parametro por que ya existe
         // ID_Memo = (genero_const == true) ? Memo : ID_Memo;
         console.log(ID_Memo, 'ID_Memo y ');
         //*Comprobamos si el ID_Memo es una cadena 'true' o un valor booleano true
         if (ID_Memo === 'false' || ID_Memo === false) {
            //* Llamar a la función generarConstancia
            generarConstancia(unidad1, index, span, idConcesion);
   
         } else {
            // alert(ID_Memo, 'ID_Memo y entro en ver constancia');
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
   
   function generarConstancia(unidad1, index, span, idConcesion) {
   
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
            console.log(dataserver.ID_Memo, 'se genero id_memo');
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
   
                  cambiarLink(dataserver.ID_Memo, span, index, unidad1, idConcesion); // Llama a la función adicional
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
   function cambiarLink(MEMO, span, index, unidad1, idConcesion) {
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
   
      // Retornar el arreglo modificado
      // return concesionNumber;
   }
   
   //*******************************************************************************/
   //* funcion encargada de modificar el tramite en false al momento de eliminar
   //*******************************************************************************/
   
   function EliminarTramite(idConcesion, i, idTramite) {
      //clearCollections(idConcesiones);
      concesionNumber.forEach((element, index) => {
         Object.values(element).forEach(dato => {
            //*comparamos el dato del arreglo con el id del elemento de la concession que selecionamos.
            if (dato == idConcesion) {
               //*si es igual verificamos si la unidad existe y si existe
               if (element['Tramites'] && typeof element['Tramites'] === 'object') {
                  //*modifico el ID_Memo y inserto el elemento. cambio false por el valor.
                  console.log(element['Tramites'][i]);
                  //*cambiando el tramite en la posicion i por false.
                  element['Tramites'][i] = false;
                  //*volver a cargar la tabla con los nuevos datos.
                  mostrarData(concesionNumber, 'tabla-container', tituloGlobal);
                  console.log('llamar data');
                  console.log(concesionNumber);
               } else {
                  //*si no no hay unidad
                  console.log('no hay tramites => para el idConcesion' + idConcesion);
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
      // alert('ver constancia link');'FSL-28897-14159-23
   
      var numPreforma = document.getElementById("RAM").value//UNIDAD1['ID_Formulario_Solicitud']//$('#numpreforma').val(); // Obtener el valor de #numpreforma
      // console.log(numPreforma, 'numPreforma y', MEMO, 'MEMO EN VERCONSTANCIA');
      var url = $appcfg_Dominio_Raiz + ':288/PDF_Constancia.php?ID_Memo=' + MEMO + '&FSL=' + numPreforma;
      window.open(url, '_blank'); // Abrir en una nueva ventana
   }
   //****************************************************************************/
   //*Recupero los estados dependiendo del usuario que esta dentro del sistema
   //****************************************************************************/
   
   function estados() {
      let respuestaEstados = [];
      fetch(`${$appcfg_Dominio}/estados_sistemas_por_user.php`, {
         method: 'GET',
         headers: {
            'Authorization': 'Bearer token',
         },
      })
         .then(response => {
            if (!response.ok) {
               throw new Error('Error en la solicitud estdos en archivo de objeto');
            }
            return response.json();
         })
         .then(data => {
            respuestaEstados = data;
            console.log('Respuesta:', data);
         })
         .catch(error => {
            console.error('Error:', error);
         });
      return respuestaEstados
   }
   
   function tipoConcesion(esCarga, esCertificado, Concesion_Encriptada, Permiso_Explotacion_Encriptado) {
      // console.log(esCarga, esCertificado, Concesion_Encriptada, Permiso_Explotacion_Encriptado);
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
   
   
   // function tipoConcesion(esCarga, esCertificado, Concesion_Encriptada, Permiso_Explotacion_Encriptado) {
   
   //    //**************************//	
   //    // Es Renovacion de Certificado
   //    //**************************//	
   //    let rutacertificado = '';
   //    let rutapermisoexplotacion = '';
   //    if (esCertificado == true) { // if ($tipo_concesion == 'CER') {
   //       if (esCarga == true) { //'CARGA'
   //          rutacertificado = `${$appcfg_Dominio_Raiz}:172/api_rep.php?action=get-PDFCertificado-Carga&Certificado=${Concesion_Encriptada}`;
   //          rutapermisoexplotacion = `${$appcfg_Dominio_Raiz}:172/api_rep.php?action=get-PDFPermisoExp-Carga&Permiso=${Permiso_Explotacion_Encriptado}`;
   
   //  linksConcesion[''];
   //          window.open(rutacertificado, '_blank');
   //          window.open(rutapermisoexplotacion, '_blank');
   //       } else {
   //          rutacertificado = `${$appcfg_Dominio_Raiz}:172/api_rep.php?action=get-PDFCertificado&Certificado=${Concesion_Encriptada}`;
   //          rutapermisoexplotacion = `${$appcfg_Dominio_Raiz}:172/api_rep.php?action=get-PDFPermisoExp-Pas&Permiso=${Permiso_Explotacion_Encriptado}`;
   //          window.open(rutacertificado, '_blank');
   //          window.open(rutapermisoexplotacion, '_blank');
   //       }
   
   //    } else {
   //       console.log(' no esCertificado');
   
   //       //**************************//	
   //       // Es Renovacion de Permisos Especiales
   //       //**************************//	
   //       if (esCarga == true) { //'CARGA'
   //          rutacertificado = `${$appcfg_Dominio_Raiz}:172/api_rep.php?action=get-PDFPermisoEsp-Carga&PermisoEspecial=${Concesion_Encriptada}`
   //          window.open(rutacertificado, '_blank');
   
   //       } else {
   //          rutacertificado = `${$appcfg_Dominio_Raiz}:172/api_rep.php?action=get-PDFPermisoEsp-Pas&PermisoEspecial=${Concesion_Encriptada}`
   //          window.open(rutacertificado, '_blank');
   //       }
   
   //    }
   // }
   
   document.addEventListener("DOMContentLoaded", function () {
   
      const data = [false,
   
         {
            "Concesion": "CO-CGR-1623-19",
            "Permiso_Explotacion": "PE-CNE-3408-19",
            "ID_Expediente": "",
            "": "",
            "ID_Formulario_Solicitud": "RAM-000000033-2024",
            "CodigoAvisoCobro": "",
            "ID_Resolucion": "",
            "Placa": "TRC8490",
            "valor": 'ok',
            "Unidad": {
               "1": "RAM-000000033-2024",
               "2": "70496",
               "3": "05119995190604",
               "4": "TRANSPORTES AGRICOLAS SA CV",
               "5": "TRC8490",
               "6": "MV-489",
               "7": "INTERNATIONAL",
               "8": "1977",
               "9": "",
               "10": "FURGONETA",
               "11": "IDC-45",
               "12": "PLATEADO",
               "13": "B603568E",
               "14": "B603568E",
               "15": "NORMAL",
               "16": "2024-11-14 20:23:38.190",
               "17": "PE-CNE-3408-19                                    ",
               "18": "B603568E",
               "19": "NO UTILIZA",
               "20": "2.00",
               "21": "2.00",
               "22": "12.00",
               "23": "9600.00",
               "24": ".00",
               "25": "RA2999",
               "ID_Formulario_Solicitud": "RAM-000000033-2024",
               "ID": "70496",
               "RTN_Propietario": "05119995190604",
               "Nombre_Propietario": "TRANSPORTES AGRICOLAS SA CV",
               "ID_Placa": "TRC8490",
               "ID_Marca": "MV-489",
               "Desc_Marca": "INTERNATIONAL",
               "Anio": "1977",
               "Modelo": "",
               "Tipo_Vehiculo": "FURGONETA",
               "ID_Color": "IDC-45",
               "Desc_Color": "PLATEADO",
               "Motor": "B603568E",
               "Chasis": "B603568E",
               "Estado": "NORMAL",
               "Sistema_Fecha": "2024-11-14 20:23:38.190",
               "Permiso_Explotacion": "PE-CNE-3408-19                                    ",
               "VIN": "B603568E",
               "Combustible": "NO UTILIZA",
               "Alto": "2.00",
               "Ancho": "2.00",
               "Largo": "12.00",
               "Capacidad_Carga": "9600.00",
               "Peso_Unidad": ".00",
               "ID_Placa_Antes_Replaqueo": "RA2999",
            },
            "Unidad1": "",
            "Tramites": [
               {
                  "ID_Compuesto": "IHTTTRA-02_CLATRA-02_R_CO",
                  "Codigo": "IHTT-363",
                  "descripcion": "RENOVACIÓN CERTIFICADO DE OPERACIÓN",
                  "ID_Tramite": "IHTT-363",
                  "Monto": "1000.00",
                  "ID_Categoria": "CGR",
                  "ID_Tipo_Servicio": "STP",
                  "ID_Modalidad": "MOD-08",
                  "ID_Clase_Servico": "STPC"
               },
               {
                  "ID_Compuesto": "IHTTTRA-03_CLATRA-15_M_CL",
                  "Codigo": "IHTT-523",
                  "descripcion": "MODIFICACIÓN CAMBIO DE PLACA",
                  "ID_Tramite": "IHTT-523",
                  "Monto": "200.00",
                  "ID_Categoria": "CGR",
                  "ID_Tipo_Servicio": "STP",
                  "ID_Modalidad": "MOD-08",
                  "ID_Clase_Servico": "STPC"
               }
            ]
         },
         {
            "Concesion": "CO-CNE-6236-19",
            "Permiso_Explotacion": "PE-CNE-3408-19",
            "ID_Expediente": "",
            "ID_Solicitud": "",
            "ID_Formulario_Solicitud": "RAM-000000033-2024",
            "CodigoAvisoCobro": "",
            "ID_Resolucion": "",
            "Placa": "AAK3148",
            "Unidad": {
               "1": "RAM-000000033-2024",
               "2": "70489",
               "3": "05119995190604",
               "4": "TRANSPORTES AGRICOLAS  S.A. DE C.V.",
               "5": "AAK3148",
               "6": "MV-618",
               "7": "MACK",
               "8": "2000",
               "9": "UNDEFINED",
               "10": "UNDEFINED",
               "11": "IDC-04",
               "12": "BLANCO",
               "13": "9430165537M93545618",
               "14": "1M2E184C3YM002914",
               "15": "NORMAL",
               "16": "2024-11-05 20:33:32.980",
               "17": "PE-CNE-3408-19                                    ",
               "18": "1M2E184C3YM002914",
               "19": "DIESEL",
               "20": "2.00",
               "21": "2.00",
               "22": "12.00",
               "23": "7500.00",
               "24": ".00",
               "25": "",
               "ID_Formulario_Solicitud": "RAM-000000033-2024",
               "ID": "70489",
               "RTN_Propietario": "05119995190604",
               "Nombre_Propietario": "TRANSPORTES AGRICOLAS  S.A. DE C.V.",
               "ID_Placa": "AAK3148",
               "ID_Marca": "MV-618",
               "Desc_Marca": "MACK",
               "Anio": "2000",
               "Modelo": "UNDEFINED",
               "Tipo_Vehiculo": "UNDEFINED",
               "ID_Color": "IDC-04",
               "Desc_Color": "BLANCO",
               "Motor": "9430165537M93545618",
               "Chasis": "1M2E184C3YM002914",
               "Estado": "NORMAL",
               "Sistema_Fecha": "2024-11-05 20:33:32.980",
               "Permiso_Explotacion": "PE-CNE-3408-19                                    ",
               "VIN": "1M2E184C3YM002914",
               "Combustible": "DIESEL",
               "Alto": "2.00",
               "Ancho": "2.00",
               "Largo": "12.00",
               "Capacidad_Carga": "7500.00",
               "Peso_Unidad": ".00",
               "ID_Placa_Antes_Replaqueo": ""
            },
            "Unidad1": "",
            "Tramites": [
               {
                  "ID_Compuesto": "IHTTTRA-02_CLATRA-02_R_CO",
                  "Codigo": "IHTT-362",
                  "descripcion": "RENOVACIÓN CERTIFICADO DE OPERACIÓN",
                  "ID_Tramite": "IHTT-362",
                  "Monto": "1000.00",
                  "ID_Categoria": "CNE",
                  "ID_Tipo_Servicio": "STP",
                  "ID_Modalidad": "MOD-08",
                  "ID_Clase_Servico": "STPC"
               },
               {
                  "ID_Compuesto": "IHTTTRA-03_CLATRA-15_M_CL",
                  "Codigo": "IHTT-650",
                  "descripcion": "MODIFICACIÓN CAMBIO DE PLACA",
                  "ID_Tramite": "IHTT-650",
                  "Monto": "200.00",
                  "ID_Categoria": "CNE",
                  "ID_Tipo_Servicio": "STP",
                  "ID_Modalidad": "MOD-08",
                  "ID_Clase_Servico": "STPC"
               }
            ]
         },
         {
            "Concesion": "CO-CNE-6277-19",
            "Permiso_Explotacion": "PE-CNE-3408-19",
            "ID_Expediente": "",
            "ID_Solicitud": "",
            "ID_Formulario_Solicitud": "RAM-000000033-2024",
            "CodigoAvisoCobro": "",
            "ID_Resolucion": "",
            "Placa": "TCJ1084->TCJ1085",
            "Unidad": {
               "1": "RAM-000000033-2024",
               "2": "70490",
               "3": "05119995190604",
               "4": "TRANSPORTES AGRICOLAS SA CV",
               "5": "TCJ1084",
               "6": "MV-489",
               "7": "INTERNATIONAL",
               "8": "2001",
               "9": "",
               "10": "CABEZAL",
               "11": "IDC-09",
               "12": "ROJO",
               "13": "06R0521289",
               "14": "1C092059",
               "15": "SALE",
               "16": "2024-11-05 20:39:02.933",
               "17": "PE-CNE-3408-19                                    ",
               "18": "2HSCEAMR91C092059",
               "19": "DIESEL",
               "20": "2.00",
               "21": "2.00",
               "22": "12.00",
               "23": "9000.00",
               "24": ".00",
               "25": "AAK6046",
               "ID_Formulario_Solicitud": "RAM-000000033-2024",
               "ID": "70490",
               "RTN_Propietario": "05119995190604",
               "Nombre_Propietario": "TRANSPORTES AGRICOLAS SA CV",
               "ID_Placa": "TCJ1084",
               "ID_Marca": "MV-489",
               "Desc_Marca": "INTERNATIONAL",
               "Anio": "2001",
               "Modelo": "",
               "Tipo_Vehiculo": "CABEZAL",
               "ID_Color": "IDC-09",
               "Desc_Color": "ROJO",
               "Motor": "06R0521289",
               "Chasis": "1C092059",
               "Estado": "SALE",
               "Sistema_Fecha": "2024-11-05 20:39:02.933",
               "Permiso_Explotacion": "PE-CNE-3408-19                                    ",
               "VIN": "2HSCEAMR91C092059",
               "Combustible": "DIESEL",
               "Alto": "2.00",
               "Ancho": "2.00",
               "Largo": "12.00",
               "Capacidad_Carga": "9000.00",
               "Peso_Unidad": ".00",
               "ID_Placa_Antes_Replaqueo": "AAK6046",
               "Constacia_Emitida": 'Si',
            },
            "Unidad1": {
               "1": "RAM-000000033-2024",
               "2": "70491",
               "3": "05119995190604",
               "4": "TRANSPORTES AGRICOLAS SA CV",
               "5": "TCJ1085",
               "6": "MV-489",
               "7": "INTERNATIONAL",
               "8": "2000",
               "9": "941 TM",
               "10": "CABEZAL",
               "11": "IDC-04",
               "12": "BLANCO",
               "13": "11986121",
               "14": "YC058770",
               "15": "ENTRA",
               "16": "2024-11-05 20:39:02.947",
               "17": "PE-CNE-3408-19                                    ",
               "18": "2HSCNAER8YC058770",
               "19": "DIESEL",
               "20": "2.00",
               "21": "2.00",
               "22": "12.00",
               "23": "9000.00",
               "24": ".00",
               "25": "AAK7109",
               "ID_Formulario_Solicitud": "RAM-000000033-2024",
               "ID": "70491",
               "RTN_Propietario": "05119995190604",
               "Nombre_Propietario": "TRANSPORTES AGRICOLAS SA CV",
               "ID_Placa": "TCJ1085",
               "ID_Marca": "MV-489",
               "Desc_Marca": "INTERNATIONAL",
               "Anio": "2000",
               "Modelo": "941 TM",
               "Tipo_Vehiculo": "CABEZAL",
               "ID_Color": "IDC-04",
               "Desc_Color": "BLANCO",
               "Motor": "11986121",
               "Chasis": "YC058770",
               "Estado": "ENTRA",
               "Sistema_Fecha": "2024-11-05 20:39:02.947",
               "Permiso_Explotacion": "PE-CNE-3408-19                                    ",
               "VIN": "2HSCNAER8YC058770",
               "Combustible": "DIESEL",
               "Alto": "2.00",
               "Ancho": "2.00",
               "Largo": "12.00",
               "Capacidad_Carga": "9000.00",
               "Peso_Unidad": ".00",
               "ID_Placa_Antes_Replaqueo": "AAK7109",
               "ID_Memo": false
            },
            "Tramites": [
               {
                  "ID_Compuesto": "IHTTTRA-03_CLATRA-08_M_CU",
                  "Codigo": "IHTT-426",
                  "descripcion": "MODIFICACIÓN CAMBIO DE UNIDAD",
                  "ID_Tramite": "IHTT-426",
                  "Monto": "1000.00",
                  "ID_Categoria": "CNE",
                  "ID_Tipo_Servicio": "STP",
                  "ID_Modalidad": "MOD-08",
                  "ID_Clase_Servico": "STPC"
               }
            ]
         },
         {
            "Concesion": "CO-CNE-6260-19",
            "Permiso_Explotacion": "PE-CNE-3408-19",
            "ID_Expediente": "",
            "ID_Solicitud": "",
            "ID_Formulario_Solicitud": "RAM-000000033-2024",
            "CodigoAvisoCobro": "",
            "ID_Resolucion": "",
            "Placa": "TCJ1084->TCJ1085",
            "Unidad": {
               "1": "RAM-000000033-2024",
               "2": "70490",
               "3": "05119995190604",
               "4": "TRANSPORTES AGRICOLAS SA CV",
               "5": "TCJ1084",
               "6": "MV-489",
               "7": "INTERNATIONAL",
               "8": "2001",
               "9": "",
               "10": "CABEZAL",
               "11": "IDC-09",
               "12": "ROJO",
               "13": "06R0521289",
               "14": "1C092059",
               "15": "SALE",
               "16": "2024-11-05 20:39:02.933",
               "17": "PE-CNE-3408-19                                    ",
               "18": "2HSCEAMR91C092059",
               "19": "DIESEL",
               "20": "2.00",
               "21": "2.00",
               "22": "12.00",
               "23": "9000.00",
               "24": ".00",
               "25": "AAK6046",
               "ID_Formulario_Solicitud": "RAM-000000033-2024",
               "ID": "70490",
               "RTN_Propietario": "05119995190604",
               "Nombre_Propietario": "TRANSPORTES AGRICOLAS SA CV",
               "ID_Placa": "TCJ1084",
               "ID_Marca": "MV-489",
               "Desc_Marca": "INTERNATIONAL",
               "Anio": "2001",
               "Modelo": "",
               "Tipo_Vehiculo": "CABEZAL",
               "ID_Color": "IDC-09",
               "Desc_Color": "ROJO",
               "Motor": "06R0521289",
               "Chasis": "1C092059",
               "Estado": "SALE",
               "Sistema_Fecha": "2024-11-05 20:39:02.933",
               "Permiso_Explotacion": "PE-CNE-3408-19                                    ",
               "VIN": "2HSCEAMR91C092059",
               "Combustible": "DIESEL",
               "Alto": "2.00",
               "Ancho": "2.00",
               "Largo": "12.00",
               "Capacidad_Carga": "9000.00",
               "Peso_Unidad": ".00",
               "ID_Placa_Antes_Replaqueo": "AAK6046",
               "Constacia_Emitida": 'Si',
            },
            "Unidad1": {
               "1": "RAM-000000033-2024",
               "2": "70491",
               "3": "05119995190604",
               "4": "TRANSPORTES AGRICOLAS SA CV",
               "5": "TCJ1085",
               "6": "MV-489",
               "7": "INTERNATIONAL",
               "8": "2000",
               "9": "941 TM",
               "10": "CABEZAL",
               "11": "IDC-04",
               "12": "BLANCO",
               "13": "11986121",
               "14": "YC058770",
               "15": "ENTRA",
               "16": "2024-11-05 20:39:02.947",
               "17": "PE-CNE-3408-19                                    ",
               "18": "2HSCNAER8YC058770",
               "19": "DIESEL",
               "20": "2.00",
               "21": "2.00",
               "22": "12.00",
               "23": "9000.00",
               "24": ".00",
               "25": "AAK7109",
               "ID_Formulario_Solicitud": "RAM-000000033-2024",
               "ID": "70491",
               "RTN_Propietario": "05119995190604",
               "Nombre_Propietario": "TRANSPORTES AGRICOLAS SA CV",
               "ID_Placa": "TCJ1085",
               "ID_Marca": "MV-489",
               "Desc_Marca": "INTERNATIONAL",
               "Anio": "2000",
               "Modelo": "941 TM",
               "Tipo_Vehiculo": "CABEZAL",
               "ID_Color": "IDC-04",
               "Desc_Color": "BLANCO",
               "Motor": "11986121",
               "Chasis": "YC058770",
               "Estado": "ENTRA",
               "Sistema_Fecha": "2024-11-05 20:39:02.947",
               "Permiso_Explotacion": "PE-CNE-3408-19                                    ",
               "VIN": "2HSCNAER8YC058770",
               "Combustible": "DIESEL",
               "Alto": "2.00",
               "Ancho": "2.00",
               "Largo": "12.00",
               "Capacidad_Carga": "9000.00",
               "Peso_Unidad": ".00",
               "ID_Placa_Antes_Replaqueo": "AAK7109",
               "ID_Memo": false
            },
            "Tramites": [
               {
                  "ID_Compuesto": "IHTTTRA-03_CLATRA-08_M_CU",
                  "Codigo": "IHTT-426",
                  "descripcion": "MODIFICACIÓN CAMBIO DE UNIDAD",
                  "ID_Tramite": "IHTT-426",
                  "Monto": "1000.00",
                  "ID_Categoria": "CNE",
                  "ID_Tipo_Servicio": "STP",
                  "ID_Modalidad": "MOD-08",
                  "ID_Clase_Servico": "STPC"
               }
            ]
         },
         false,
         {
            "Concesion": "CO-CNE-6259-24",
            "Permiso_Explotacion": "PE-CNE-3408-19",
            "ID_Expediente": "",
            "ID_Solicitud": "",
            "ID_Formulario_Solicitud": "RAM-000000033-2024",
            "CodigoAvisoCobro": "",
            "ID_Resolucion": "",
            "Placa": "TCB4072->TCB4073",
            "Unidad": {
               "1": "RAM-000000033-2024",
               "2": "70492",
               "3": "05119995190604",
               "4": "TRANSPORTES AGRICOLAS SA CV",
               "5": "TCB4072",
               "6": "MV-489",
               "7": "INTERNATIONAL",
               "8": "2005",
               "9": "9400",
               "10": "CABEZAL",
               "11": "IDC-02",
               "12": "AZUL",
               "13": "79066590",
               "14": "5C047499",
               "15": "SALE",
               "16": "2024-11-06 19:41:00.013",
               "17": "PE-CNE-3408-19                                    ",
               "18": "2HSCNAPR85C047499",
               "19": "DIESEL",
               "20": "2.00",
               "21": "2.00",
               "22": "12.00",
               "23": "7850.00",
               "24": ".00",
               "25": "AAK3118",
               "ID_Formulario_Solicitud": "RAM-000000033-2024",
               "ID": "70492",
               "RTN_Propietario": "05119995190604",
               "Nombre_Propietario": "TRANSPORTES AGRICOLAS SA CV",
               "ID_Placa": "TCB4072",
               "ID_Marca": "MV-489",
               "Desc_Marca": "INTERNATIONAL",
               "Anio": "2005",
               "Modelo": "9400",
               "Tipo_Vehiculo": "CABEZAL",
               "ID_Color": "IDC-02",
               "Desc_Color": "AZUL",
               "Motor": "79066590",
               "Chasis": "5C047499",
               "Estado": "SALE",
               "Sistema_Fecha": "2024-11-06 19:41:00.013",
               "Permiso_Explotacion": "PE-CNE-3408-19                                    ",
               "VIN": "2HSCNAPR85C047499",
               "Combustible": "DIESEL",
               "Alto": "2.00",
               "Ancho": "2.00",
               "Largo": "12.00",
               "Capacidad_Carga": "7850.00",
               "Peso_Unidad": ".00",
               "ID_Placa_Antes_Replaqueo": "AAK3118"
            },
            "Unidad1": {
               "1": "RAM-000000033-2024",
               "2": "70493",
               "3": "16131980007580",
               "4": "GOMEZ RAMIREZ, IVAN EDGARDO",
               "5": "TCB4073",
               "6": "MV-314",
               "7": "FREIGHTLINER",
               "8": "1999",
               "9": "",
               "10": "CABEZAL",
               "11": "IDC-02",
               "12": "AZUL",
               "13": "06R0433624",
               "14": "1FUYDSEB6XPA57619",
               "15": "ENTRA",
               "16": "2024-11-06 19:41:00.013",
               "17": "PE-CNE-3408-19                                    ",
               "18": "1FUYDSEB6XPA57619",
               "19": "GASOLINA",
               "20": "2.00",
               "21": "2.00",
               "22": "12.00",
               "23": "9600.00",
               "24": ".00",
               "25": "AAJ5137",
               "ID_Formulario_Solicitud": "RAM-000000033-2024",
               "ID": "70493",
               "RTN_Propietario": "16131980007580",
               "Nombre_Propietario": "GOMEZ RAMIREZ, IVAN EDGARDO",
               "ID_Placa": "TCB4073",
               "ID_Marca": "MV-314",
               "Desc_Marca": "FREIGHTLINER",
               "Anio": "1999",
               "Modelo": "",
               "Tipo_Vehiculo": "CABEZAL",
               "ID_Color": "IDC-02",
               "Desc_Color": "AZUL",
               "Motor": "06R0433624",
               "Chasis": "1FUYDSEB6XPA57619",
               "Estado": "ENTRA",
               "Sistema_Fecha": "2024-11-06 19:41:00.013",
               "Permiso_Explotacion": "PE-CNE-3408-19                                    ",
               "VIN": "1FUYDSEB6XPA57619",
               "Combustible": "GASOLINA",
               "Alto": "2.00",
               "Ancho": "2.00",
               "Largo": "12.00",
               "Capacidad_Carga": "9600.00",
               "Peso_Unidad": ".00",
               "ID_Placa_Antes_Replaqueo": "AAJ5137",
               "ID_Memo": 'IHTT-CON-1939-23',
            },
            "Tramites": [
               {
                  "ID_Compuesto": "IHTTTRA-02_CLATRA-02_R_CO",
                  "Codigo": "IHTT-362",
                  "descripcion": "RENOVACIÓN CERTIFICADO DE OPERACIÓN",
                  "ID_Tramite": "IHTT-362",
                  "Monto": "1000.00",
                  "ID_Categoria": "CNE",
                  "ID_Tipo_Servicio": "STP",
                  "ID_Modalidad": "MOD-08",
                  "ID_Clase_Servico": "STPC"
               },
               {
                  "ID_Compuesto": "IHTTTRA-03_CLATRA-08_M_CU",
                  "Codigo": "IHTT-426",
                  "descripcion": "MODIFICACIÓN CAMBIO DE UNIDAD",
                  "ID_Tramite": "IHTT-426",
                  "Monto": "1000.00",
                  "ID_Categoria": "CNE",
                  "ID_Tipo_Servicio": "STP",
                  "ID_Modalidad": "MOD-08",
                  "ID_Clase_Servico": "STPC"
               },
               {
                  "ID_Compuesto": "IHTTTRA-03_CLATRA-15_M_CL",
                  "Codigo": "IHTT-650",
                  "descripcion": "MODIFICACIÓN CAMBIO DE PLACA",
                  "ID_Tramite": "IHTT-650",
                  "Monto": "200.00",
                  "ID_Categoria": "CNE",
                  "ID_Tipo_Servicio": "STP",
                  "ID_Modalidad": "MOD-08",
                  "ID_Clase_Servico": "STPC"
               }
            ]
         },
         {
            "Concesion": "CO-CNE-6247-19",
            "Permiso_Explotacion": "PE-CNE-3408-19",
            "ID_Expediente": "",
            "ID_Solicitud": "",
            "ID_Formulario_Solicitud": "RAM-000000033-2024",
            "CodigoAvisoCobro": "",
            "ID_Resolucion": "",
            "Placa": "TCJ1069",
            "Unidad": {
               "1": "RAM-000000033-2024",
               "2": "70494",
               "3": "05119995190604",
               "4": "TRANSPORTES AGRICOLAS SA CV",
               "5": "TCJ1069",
               "6": "MV-489",
               "7": "INTERNATIONAL",
               "8": "2001",
               "9": "9400",
               "10": "CABEZAL",
               "11": "IDC-04",
               "12": "BLANCO",
               "13": "06R0603634",
               "14": "2HSCNAMR21C007627",
               "15": "NORMAL",
               "16": "2024-11-14 20:02:16.427",
               "17": "PE-CNE-3408-19                                    ",
               "18": "2HSCNAMR21C007627",
               "19": "DIESEL",
               "20": "2.00",
               "21": "2.00",
               "22": "12.00",
               "23": "8500.00",
               "24": ".00",
               "25": "AAK6399",
               "ID_Formulario_Solicitud": "RAM-000000033-2024",
               "ID": "70494",
               "RTN_Propietario": "05119995190604",
               "Nombre_Propietario": "TRANSPORTES AGRICOLAS SA CV",
               "ID_Placa": "TCJ1069",
               "ID_Marca": "MV-489",
               "Desc_Marca": "INTERNATIONAL",
               "Anio": "2001",
               "Modelo": "9400",
               "Tipo_Vehiculo": "CABEZAL",
               "ID_Color": "IDC-04",
               "Desc_Color": "BLANCO",
               "Motor": "06R0603634",
               "Chasis": "2HSCNAMR21C007627",
               "Estado": "NORMAL",
               "Sistema_Fecha": "2024-11-14 20:02:16.427",
               "Permiso_Explotacion": "PE-CNE-3408-19                                    ",
               "VIN": "2HSCNAMR21C007627",
               "Combustible": "DIESEL",
               "Alto": "2.00",
               "Ancho": "2.00",
               "Largo": "12.00",
               "Capacidad_Carga": "8500.00",
               "Peso_Unidad": ".00",
               "ID_Placa_Antes_Replaqueo": "AAK6399"
            },
            "Unidad1": "",
            "Tramites": [
               {
                  "ID_Compuesto": "IHTTTRA-02_CLATRA-02_R_CO",
                  "Codigo": "IHTT-362",
                  "descripcion": "RENOVACIÓN CERTIFICADO DE OPERACIÓN",
                  "ID_Tramite": "IHTT-362",
                  "Monto": "1000.00",
                  "ID_Categoria": "CNE",
                  "ID_Tipo_Servicio": "STP",
                  "ID_Modalidad": "MOD-08",
                  "ID_Clase_Servico": "STPC"
               },
               {
                  "ID_Compuesto": "IHTTTRA-03_CLATRA-15_M_CL",
                  "Codigo": "IHTT-650",
                  "descripcion": "MODIFICACIÓN CAMBIO DE PLACA",
                  "ID_Tramite": "IHTT-650",
                  "Monto": "200.00",
                  "ID_Categoria": "CNE",
                  "ID_Tipo_Servicio": "STP",
                  "ID_Modalidad": "MOD-08",
                  "ID_Clase_Servico": "STPC"
               }
            ]
         },
         {
            "Concesion": "CO-CNE-8560-20",
            "Permiso_Explotacion": "PE-CNE-3408-19",
            "ID_Expediente": "",
            "ID_Solicitud": "",
            "ID_Formulario_Solicitud": "RAM-000000033-2024",
            "CodigoAvisoCobro": "",
            "ID_Resolucion": "",
            "Placa": "TCJ1066",
            "Unidad": {
               "1": "RAM-000000033-2024",
               "2": "70495",
               "3": "05119995190604",
               "4": "TRANSPORTES AGRICOLAS SA CV",
               "5": "TCJ1066",
               "6": "MV-489",
               "7": "INTERNATIONAL",
               "8": "2006",
               "9": "9400I",
               "10": "CABEZAL",
               "11": "IDC-04",
               "12": "BLANCO",
               "13": "79137478",
               "14": "6C323640",
               "15": "NORMAL",
               "16": "2024-11-14 20:12:30.830",
               "17": "PE-CNE-3408-19                                    ",
               "18": "2HSCNAPR36C323640",
               "19": "DIESEL",
               "20": "2.00",
               "21": "2.00",
               "22": "12.00",
               "23": "7450.00",
               "24": ".00",
               "25": "AAM2618",
               "ID_Formulario_Solicitud": "RAM-000000033-2024",
               "ID": "70495",
               "RTN_Propietario": "05119995190604",
               "Nombre_Propietario": "TRANSPORTES AGRICOLAS SA CV",
               "ID_Placa": "TCJ1066",
               "ID_Marca": "MV-489",
               "Desc_Marca": "INTERNATIONAL",
               "Anio": "2006",
               "Modelo": "9400I",
               "Tipo_Vehiculo": "CABEZAL",
               "ID_Color": "IDC-04",
               "Desc_Color": "BLANCO",
               "Motor": "79137478",
               "Chasis": "6C323640",
               "Estado": "NORMAL",
               "Sistema_Fecha": "2024-11-14 20:12:30.830",
               "Permiso_Explotacion": "PE-CNE-3408-19                                    ",
               "VIN": "2HSCNAPR36C323640",
               "Combustible": "DIESEL",
               "Alto": "2.00",
               "Ancho": "2.00",
               "Largo": "12.00",
               "Capacidad_Carga": "7450.00",
               "Peso_Unidad": ".00",
               "ID_Placa_Antes_Replaqueo": "AAM2618"
            },
            "Unidad1": "",
            "Tramites": [
               {
                  "ID_Compuesto": "IHTTTRA-03_CLATRA-15_M_CL",
                  "Codigo": "IHTT-650",
                  "descripcion": "MODIFICACIÓN CAMBIO DE PLACA",
                  "ID_Tramite": "IHTT-650",
                  "Monto": "200.00",
                  "ID_Categoria": "CNE",
                  "ID_Tipo_Servicio": "STP",
                  "ID_Modalidad": "MOD-08",
                  "ID_Clase_Servico": "STPC"
               }
            ]
         }
      ]
   
      mostrarData(data);
   });
   
   // function verConstancia($MEMO) {
   //    alert('ver constancia link');
   //    window.open('https://satt2.transporte.gob.hn:288/PDF_Constancia.php?ID_Memo=' + $MEMO + '&FSL=' + $('#numpreforma').val(), '_blank');
   // }
   
   
   // function generarConstancia(unidad1) {
   
   
   //    alert('generarConstancia');
   //    var form_data = new FormData();
   //    form_data.append("action", "save-ingreso-constancias");
   //    form_data.append("Referencia", $('#numpreforma').val());
   //    form_data.append("Placa_Entra", document.getElementById('idplaca').firstChild.firstChild.data);
   //    form_data.append("Marca_Entra", document.getElementById('DESC_Marca').firstChild.data);
   //    form_data.append("Tipo_Entra", document.getElementById('tipo_vehiculo').firstChild.data);
   //    form_data.append("Anio_Entra", document.getElementById('anio').firstChild.data);
   //    form_data.append("Motor_Entra", document.getElementById('Motor').firstChild.data);
   //    form_data.append("Chasis_Entra", document.getElementById('Chasis').firstChild.data);
   //    form_data.append("Vin_Entra", document.getElementById('VIN').firstChild.data);
   //    form_data.append("Identidad", document.getElementById('RTN_Propietario').firstChild.data);
   //    form_data.append("Nombre_Solicitante", document.getElementById('Nombre_Propietario').firstChild.data);
   //    form_data.append("usuario", document.getElementById('idusuario').value);
   //    form_data.append("user_name", document.getElementById('user_name').value);
   //    $.ajax({
   //       url: 'https://satt2.transporte.gob.hn:288/Api_Autos.php',
   //       dataType: 'json',
   //       cache: false,
   //       contentType: false,
   //       processData: false,
   //       data: form_data,
   //       type: 'post',
   //       success: function (dataserver) {
   //          console.log(dataserver)
   //          if (dataserver.status == 2) {
   //             swal({
   //                title: "NO SE SALVO LA CONSTANCIA",
   //                text: "La sesión actual a expirado favor inice sesión nuevamente",
   //                type: "warning",
   //                html: true,
   //                showCancelButton: true,
   //                showConfirmButton: false,
   //             });
   //          } else {
   //             if (dataserver.status == 3) {
   //                swal({
   //                   title: "NO SE SALVO LA CONSTANCIA",
   //                   text: "Ya habia sido emitida una constancia para el CHASIS NO. <b>" + document.getElementById('Chasis').firstChild.data + '</b> en la fecha ' + dataserver.Sistema_Fecha + ' con el número de memo ' + dataserver.ID_Memo,
   //                   type: "warning",
   //                   html: true,
   //                   showCancelButton: false,
   //                   closeOnConfirm: true,
   //                   showLoaderOnConfirm: true,
   //                   confirmButtonColor: "#DD6B55",
   //                   confirmButtonText: "VER CONSTANCIA EMITIDA PREVIAMENTE"
   //                }, function () {
   //                   verConstancia(dataserver.ID_Memo);
   //                });
   //             } else {
   //                $link = 'https://satt2.transporte.gob.hn:288/PDF_Constancia.php?ID_Memo=' + dataserver.ID_Memo + '&FSL=' + $('#numpreforma').val();
   //                swal({
   //                   title: "CONTANCIA SALVADA SATISFACTORIAMENTE",
   //                   text: "CONSTANCIA GENERADA CORRECTAMENTE CON EL NUMERO DE MEMO " + dataserver.ID_Memo,
   //                   type: "success",
   //                   html: true,
   //                   showCancelButton: false,
   //                   closeOnConfirm: true,
   //                   confirmButtonText: 'IMPRIMIR',
   //                   showLoaderOnConfirm: true
   //                }, function () {
   //                   window.open($link, '_blank');
   //                });
   
   //             }
   //          }
   //       },
   //       error: function (xhr) {
   //          console.log(xhr);
   //          //alert("An error occured: " + xhr.status + " " + xhr.statusText);
   //          if (xhr.status == 200) {
   //             alert(xhr.responseText);
   //          }
   //       }
   //    });
   // }
   
   //****************************
   //RBTHAOFIC@GMAIL.COM 2023/06/15 *
   //@@@ FUNCION PARA GENERAR CONSTANCIA DE REPLAQUEO    @@@
   //****************************
   
   // });
   
   
   
   
   // });