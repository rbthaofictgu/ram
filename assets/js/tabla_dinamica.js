//*indica el numero de pagina donde empieza la paginacion
let currentPage = 1;
//*la cnatidad de elemento por pagina
const rowsPerPage = 10;

//*inicializamos el estdo y la descripcion por default
let ultimoEstadoSeleccionado = 'IDE-7';
let ultimoDescripSeleccionado = 'VENTANILLA';

//* funcion que me permite actualizar el estado y la descripcion segun btn de estados
function actualizarEstado(estado, descripcion) {
   ultimoEstadoSeleccionado = estado;
   ultimoDescripSeleccionado = descripcion;
}

function vista_data(estado = '', descripcion = '') {

   //*Campo de filtro con los datos de busqueda.
   let campo = document.getElementById('id_filtro_select').value;
   let datoBuscar = document.getElementById('id_input_filtro').value;
   // let variables=campo + ' Y DATO ' + datoBuscar;
   // copiaEstado = estado;
   // copiaDescripcion = descripcion;
   //*obteniendo lo que se encuentra en tabla_container.
   const loadingIndicator = document.getElementById("tabla-container").innerHTML;
   //*colocando inagne de carga en tabla_container.
   document.getElementById("tabla-container").innerHTML = '<center><img width="500px" height="500px" src="' + $appcfg_Dominio_Corto + 'ram/assets/images/hug.gif"></center>';

   //?nota:en estado si hay estado se envia si no se poner por defecto el ultimo estado seleccionado.
   //*peticion fetch que envia estado datoBuscar, limit, page
   fetch(`${$appcfg_Dominio}/query_tabla_dinamica.php?estado=${estado ? estado : ultimoEstadoSeleccionado}&campo=${campo}&datoBuscar=${datoBuscar}&limit=${rowsPerPage}&page=${currentPage}`)
      .then(response => {
         if (!response.ok) {
            throw new Error('Error en la solicitud');
         }
         return response.json();
      })
      .then(data => {
         // console.log(data);
         //*mandando a tabala-container el elemento antes capturando en loadingIndicador.
         document.getElementById("tabla-container").innerHTML = loadingIndicator;
         //*si existe mendaje de error en blanco o del la consulta
         if (data.mensaje != '' || data.error) {
            //*si es error de blanco se envia un alerte en vez de la tabla.
            if (data.mensaje) {
               // console.log('no hay datos');
               let contenedor = document.getElementById('tabla-container');
               contenedor.innerHTML = `<div class="container-sm"><div class="alert alert-info text-center" role="alert">
            <strong><i class="fa-solid fa-triangle-exclamation"></i> NO HAY DATOS PARA EL ESTADO="${descripcion ? descripcion : ultimoDescripSeleccionado}"</strong></div></div>`
            } else {
               //*si es un error de la consulta se envia un alerta y se limpia la tabla.
               let contenedor = document.getElementById('tabla-container');
               contenedor.innerHTML = ` `;
               let title = "ERROR";
               let msg = data.error;
               let type = "error";
               fSweetAlertEventNormal(title, msg, type, errorcode = 'success')
               console.log(msg);
            }
         } else {
            //* si no hay errores en el llamdo de fetch
            //!funcion que se encarga de llenar el select con los elementso debusqueda.
            llenadoSelect(data);
            //! funcion encargada de crar la tabla dinamica.
            //?nota: se envia la data y la descripcion del tipo de estado si no esta se envia el ultimo esta asignado.
            TablaDinamica(data, descripcion ? descripcion : ultimoDescripSeleccionado);
         }
      })
      .catch(error => {
         //*manejando el error del fetch si es que hay
         console.error('No se pudo obtener la información revisar vista_data:', error);
      });
}
//*funcion encargada de llenar el select de busqueda que envia el campo de busqueda en la tabla.
function llenadoSelect(data) {
   // console.log(data.select, 'llenadoselect');
   //*instanciando el slect
   const selectElement = document.getElementById('id_filtro_select');
   //*obteniendo los datso con los que se llenara el select.
   let datos = data.dataSelect;
   // console.log(datos,'datos');
   //*Limpiar el select antes de llenarlo (por si ya tiene opciones)
   selectElement.innerHTML = '';

   //* Objeto con los valores(value) para mostrar en las opciones
   const detalle = {
      "SOLICITUD": "SOLICITUD",
      "RTN_SOL": "RTN_SOLICITUD",
      "PLACA": "PLACA"
   };
   //*recorriendo la data 
   datos.forEach((dato, index) => {
      //?nota: "hasOwnProperty"=> nos permite verificar que datos es una propiedad del objeto 
      if (detalle.hasOwnProperty(dato)) {
         //*cremao las option y las instanciamos en una variable.
         const option = document.createElement('option');
         //* asignamos su valor
         option.value = dato;
         //* asignamos el texto a visualizar.
         option.textContent = detalle[dato];
         //* Si es el primer elemento, se marca como seleccionado.
         if (index === 0) {
            option.selected = true;
         }
         //*se crean las opciones del select
         selectElement.appendChild(option);
      } else {
         //? warn=>en JavaScript se utiliza para mostrar un mensaje de advertencia en la consola del navegador.
         //*si hay algun error lo envaimos
         console.warn(`El valor '${dato}' no tiene una descripción en 'detalle'.`);
      }
   });
}

//*funcion que crea la tabla dinamica
function TablaDinamica(data, descripcion) {

//?nota:Object.values(obj): Esta parte del código obtiene un array con todos los valores del objeto obj.
//? Por ejemplo, si tienes un objeto como { a: [], b: [], c: [] }, Object.values(obj) devolvería [[ ], [ ], [ ]].
   function verificarArraysVacios(obj) {
      //?nota:.every(): Este método se usa para verificar si todos los elementos de un array cumplen una condición. Si algún elemento no cumple la condición, devuelve false; si todos la cumplen, devuelve true.
      return Object.values(obj).every(array => Array.isArray(array) && array.length === 0);
   }
   //*nos indica si esta vacio o no 
   const sonTodosVacios = verificarArraysVacios(data);

// * si esta vacio enviamos un mensaje y si no enviamos los datos.
   if (sonTodosVacios == true) {
      let contenedor = document.getElementById('tabla-container');
      contenedor.innerHTML =`<div class="container-sm"><div class="alert alert-info text-center" role="alert">
      <strong><i class="fa-solid fa-triangle-exclamation"></i>NO HAY DATOS CON ESTE ESTADO O PARAMETROS ENVIADOS</strong>
      </div></div>`
   } else {
      //*creando el contenedor de donde estara la tabla completa.
      const tableContainer = document.createElement('div');
      //*creando clasess para donde estara la tabla
      tableContainer.className = 'table-responsive mb-5';
      //*creando el elemento donde etsara el titulo de l tabla
      const tableTitle = document.createElement('h4');
      //*enviando texto
      tableTitle.textContent = `INFORMACIÓN DEL ESTADO ${descripcion}`;
      //*realizando estilo de el titulo
      tableTitle.className = 'titleTable';
      //*enviandotitulo an contenedor de la tabla
      tableContainer.appendChild(tableTitle);

      //*creando el contenedor de tabla
      const table = document.createElement('table');
      //*creando los estilos de la tabla
      table.className = 'table table-striped table-hover mb-5';
      //*creando encabezado de tabla.
      const thead = document.createElement('thead');
      //*creando las clases del encabezado
      thead.className = 'headTable table-primary';
      //*creando el contenedor del cuerpo de la tabla
      const tbody = document.createElement('tbody');
      //*creando las clases del cuerpo de la tabla
      tbody.className = 'table-group-divider';
      //*creanod la fila del encabezado
      const headerRow = document.createElement('tr');
      //*creando la columna y asignando tento para el numero a la fila
      headerRow.appendChild(document.createElement('th')).textContent = '#';
      //*recorriendo los datos del encabezado
      data.encabezados.forEach(encabezado => {
         //*creando la columna del encabezado
         const th = document.createElement('th');
         //*asignando data del encabezado
         th.textContent = encabezado;
         //*enviando columnna de encabezado a fila de la tabla
         headerRow.appendChild(th);
      });
      //*enviando fila a contenedor de encabezado
      thead.appendChild(headerRow);
      //!funcion encargada de renderizar el cuerpo de la tabla
      renderTableRows(data.datos, tbody);
      //*enviando el contenedor del encabezado y cuerpor al contenedor de la tabla
      table.appendChild(thead);
      table.appendChild(tbody);
      //*enviando la tabla al contenedor principal de la tabla completa
      tableContainer.appendChild(table);
      //!obteniendo datos de paginacion
      const paginationContainer = createPagination(data['totalRows']);
      //*creando classes para la paginación
      paginationContainer.className = "mb-5";
      //*envuando paginaciona al contenedor principal de la tabla
      tableContainer.appendChild(paginationContainer);
      //*INSTANCIANDO EL CONTENEDOR PRINCIPAL
      const contenedor = document.getElementById('tabla-container');
      //*limpiando contenedor
      contenedor.innerHTML = '';
      //*enviando variable que contiene la tabla completa al conytenedor principal.
      contenedor.appendChild(tableContainer);
   }
}

//*encargada de filtar la informcaion
function filtrado(data, tableContainer) {
   //*creanod el contenedor del filtro
   const contenedorFiltro = document.createElement('div');
   //*creando propiedad del id
   contenedorFiltro.id = "idContenedorFiltro";
   //*creando la clases
   contenedorFiltro.className = "row";
   
   //*creando el contenedor del select
   const selectContainer = document.createElement('div');
   //*creando la propiedad del id del select
   filterContainer.id = 'filter-container';
   //*creando estilos de la tabla.
   filterContainer.className = 'mb-3 col-6';

   //*creando el elemento select
   const filtro = document.createElement('select');
   //*creando el id del select
   filtro.id = 'filtro';
   //*creando la clases
   filtro.className = 'form-select';
   //*añadiendo atributos
   filtro.setAttribute('aria-label', 'Selecciona el campo de busqueda');

   // //*creando las option del select por defecto si quisieramos
   // const option = document.createElement('option');
   // option.value = '';
   // option.textContent = 'Selecciona el campo de busqueda';
   // //*enviando las option al al contenedor del select
   // filtro.appendChild(option);

   //*recorriendo data para las opciones.
   data.encabezados.forEach(encabezado => {
      //*creando las option del select
      const option = document.createElement('option');
      //*asignando datos al value de la option
      option.value = encabezado;
      //*asignando texto
      option.textContent = encabezado;
      //*enviando option al contenedor del select
      filtro.appendChild(option);
   });

   //*Creando el input
   const idInputBuscar = document.createElement('input');
   //*creando las propiedades type,id,class,placeholder
   idInputBuscar.type = 'text';
   idInputBuscar.id = 'idInputBuscar';
   idInputBuscar.className = 'form-control';
   idInputBuscar.placeholder = 'Buscar...';

   //*creando contenedor del boton buscar
   const btnContainer = document.createElement('div');
   btnContainer.id = "idBtnConmtainer";
   btnContainer.className = "col-6"

   //*creando el boton
   const idBtnBuscar = document.createElement('button');
   //*creando las propiedades del boton.
   idBtnBuscar.id = 'idBtnBuscar';
   idBtnBuscar.className = 'btn btn-info';
   idBtnBuscar.textContent = 'Buscar';

   //*creando evento click del boton que llama a la funcion "filterTable"
   idBtnBuscar.onclick = () => {
      filterTable(data, filtro.value, idInputBuscar.value);
   };

   //*enviando los elementos a los contenedores
   filterContainer.appendChild(filtro);
   filterContainer.appendChild(idInputBuscar);
   btnContainer.appendChild(idBtnBuscar);
   contenedorFiltro.appendChild(filterContainer);
   contenedorFiltro.appendChild(btnContainer);
   tableContainer.appendChild(contenedorFiltro);

}

//*funcion encargada de crear el body de la tabla
function renderTableRows(data, tbody) {
   // console.log('rendertabla', data);
   tbody.innerHTML = ''; //*limpiando cuerpo de la tabla
   //*recorriendo datos 
   data.forEach((fila, index) => {
      //*creando las filas del cuerpo
      const row = document.createElement('tr');
      //*creando columna del cuerpo y asignandole el indice de cada fila
      row.appendChild(document.createElement('td')).textContent =  (currentPage - 1) * rowsPerPage + index + 1;
      //  console.log(fila, 'fila');
      //*recorriendo los datos
      fila.forEach((valor, colIndex) => {
         //*creando la comulana de los datos 
         const td = document.createElement('td');
         //*asignando texto
         td.textContent = valor;
         //*asignando el valor del segundo elemento el onclick 
         if (colIndex === 0) {
            //* Cambia el cursor para indicar que es clickeable
            td.style.cursor = 'pointer';
            td.style.color = '#033b4b';
            td.style.textShadow = '3px 3px 5px rgba(5, 5, 5, 0.3)';
            //*evento onclick a la columna que me redirecciona.
            td.onclick = () => {
               //* redireccionando y eviando RAM
               window.location.href = `${$appcfg_Dominio}index.php?RAM=${valor}`;
            };
         }
         //*enviando columna a las filas
         row.appendChild(td);
      });
      //*enviando filas a las columnas
      tbody.appendChild(row);
   });
}

//? field =la columna donde se filtrara.
//? el dato que busco en la columna.

function filterTable(data, field, value) {
   if (!field || !value) {
      return; // si no hay campo no se filtra
   }

   const filteredData = data.datos.filter(row => { //*recorriendo datos
    //?nota: indexOf devuelve la posicion del elemento si no esta envia -1
      const index = data.encabezados.indexOf(field); 
      return index !== -1 && row[index].toString().toLowerCase().includes(value.toLowerCase());
   });

   currentPage = 1; // Reiniciar a la primera página
   //!llamando a la tabla para renderizar nuevamente
   TablaDinamica({ encabezados: data.encabezados, datos: filteredData }, copiaDescripcion);
}

//?nota: Esta pagiancion funciona con el total de filas segun la consulta de los datos
//*Paginacion de la tabla.
function createPagination(totalRows) {
   //*creando el elemento que contiene la paginacion
   const paginationContainer = document.createElement('nav');
   //*asignando los atributos
   paginationContainer.setAttribute('aria-label', 'Page navigation example' );
   //*creando el elemneto ul de la paginación
   const pagination = document.createElement('ul');
   //*asignando las clases
   pagination.className = 'pagination';

   //*claculando el numero total de paginas para la paginación
   const totalPages = Math.ceil(totalRows / rowsPerPage);
   //*creando el elemento li de la pahginación
   const prevItem = document.createElement('li');
   //*ayuda a actualizar segun en la pagian en la que nos encontremos
   prevItem.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
   //*creando el elemento a de la pagiancion 
   const prevLink = document.createElement('a');
   //*asignando las clases
   prevLink.className = 'page-link';
   //*preveniendo el refresh
   prevLink.href = '#';
   //*colocando texto
   prevLink.textContent = 'Previous';
   //*creando evento click
   prevLink.onclick = (e) => {
      //*previniendo el refresh
      e.preventDefault();
      //*condion para saber si hay una pagina anterior
      if (currentPage > 1){ 
         currentPage--;
         //*llama nuevamente a la funcion vista_data.
         vista_data();
      }
   };
   //*asignanmos los link a su contenedor
   prevItem.appendChild(prevLink);
   //*y asignamos el contenedor al contenedor de la
   pagination.appendChild(prevItem);

//*calculando un rango de paginas donde comenzara
   const startPage = Math.max(1, currentPage - 2);
   //*donde finalizara la paginación
   const endPage = Math.min(totalPages, startPage + 4);

//*recorriendo segun numeor de pagians
   for (let page = startPage; page <= endPage; page++) {
      //*creando elemeto li de la paginación
      const pageItem = document.createElement('li');
      //*añadiendo clases para saber si esta activado o no
      pageItem.className = `page-item ${currentPage === page ? 'active' : ''}`;
      //*crear elemento a de pagiancion
      const pageLink = document.createElement('a');
      //*añadiendo clases de elemento a
      pageLink.className = 'page-link';
      pageLink.href = '#';
      pageLink.textContent = page;
      pageLink.onclick = (e) => {
         e.preventDefault();
         //*asignando el numero de pagian siguiente
         currentPage = page;
         //*llamando la función
         vista_data();
      };
      //*enviuando los elementos a sus contenedores
      pageItem.appendChild(pageLink);
      pagination.appendChild(pageItem);
   }

   //*creando el elemento li
   const nextItem = document.createElement('li');
   //*añadiendo las clases
   nextItem.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
   //*creando el elemneto a de la paginación
   const nextLink = document.createElement('a');
   //*creando las clases
   nextLink.className = 'page-link';
   //*evitando el refresh
   nextLink.href = '#';
   //*colocando el texto
   nextLink.textContent = 'Next';
   //*creando el efecto click al boton siguiente
   nextLink.onclick = (e) => {
      //*evitando el refresh
      e.preventDefault();

      if (currentPage < totalPages) {
         currentPage++;
         vista_data();
      }
   };
   //*enviando a los contenedores
   nextItem.appendChild(nextLink);
   pagination.appendChild(nextItem);

   paginationContainer.appendChild(pagination);
   return paginationContainer;

}
//*funcion encargada de limpiar el input
function limpiar() {
   document.getElementById('id_input_filtro').value = '';

}
window.onload = function () {
   //* Llama a la función vista_data con los parámetros deseados
   const estadoInicial = 'IDE-7'; //* Puedes establecer el estado que quieras
   const descripcionInicial = 'VENTANILLA'; //* También puedes cambiar esto
   vista_data(estadoInicial,descripcionInicial);
};
