//* Página actual, comenzamos en la página 1
let currentPage = 1;
//* Número de filas por página 
let rowsPerPage = 3;

function mostrarData(data, contenedorId = 'tabla-container', title = '') {
   //console.log(data);t
   //*creando elementos unicips con set para eliminar repeticion
   let uniqueKeys = new Set();

   //*filtramos los campos que no queremos mostrar
   //*creamos arreglo con la informacion a exclir
   let dataExcluir = ['ID_Formulario_Solicitud'];

   const filtrarData = data.map(item => {
      //*creo una copia de de los elementos
      const filtrarItem = { ...item };
      dataExcluir.forEach(field => {
         //*eliminar campo que sean ifuales a los de dataExcluir
         delete filtrarItem[field];
      });
      //*retorna el arreglo con los elemento eliminados
      return filtrarItem;
   });


   // console.log('datafiltrada',filtrarData);

   //*recorremos el arreglo donde cada elemento es la variable item
   filtrarData.forEach(item => {
      //*obteniendo un array de clavecon Object.keys(item). y lo recorremos
      Object.keys(item).forEach(key => {
         //*asignamos cada key al array donde solo permitira elementos unicos.
         uniqueKeys.add(key);
      });
   });
   //* el array con elementos unicos pasara a ser el encabezado de la tabla.
   let encabezado = Array.from(uniqueKeys);
   let titulo = "titulo de la tabla";
   //!llamamos a la fucnion TablaBusquedaInterna
   TablaBusquedaInterna(filtrarData, titulo, encabezado, contenedorId);
}


//*funcion que crea la tabla dinamica.
function TablaBusquedaInterna(data, titulo = '', encabezado, contenedorId) {
   // console.log('Creando la tabla');
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
   //*las clases para el estilo de la tabla.
   table.className = 'table table-striped table-hover mb-5';
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
   //*creamos la columna de enumeracion en el encabezado y asignamos
   //* su texto y lo enviamos al contenedor
   headerRow.appendChild(document.createElement('th')).textContent = '#';

   //*recorremos en arreglo que contiene el encabezado de la tabla.
   encabezado.forEach((key) => {
      if (!(key == 'subData')) {
         //*creamos la columana de la tabla
         const th = document.createElement('th');
         //*le asignamos el valor
         th.textContent = key;
         //*la enviamos al contenedor de las filas del encabezado
         headerRow.appendChild(th);
      }
      // console.log(key);
   });
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


//*Función que crea el input para buscar 
function createSearchField(container, data) {
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
      containerDiv.className = 'col-10';
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

//* funcion clearSearchFiled encargada de limpiar el input y restablecer la tabla
function clearSearchFiled(tableContainer, data, tbody) {
   //*creando el boton
   const btn_limpiar = document.createElement('button');
   //*el tipo de boton
   btn_limpiar.type = 'button';
   //*añadiendo el texto al boton
   btn_limpiar.textContent = 'LIMPIAR';
   //*añadiendo las clases del boton y estilos
   btn_limpiar.className = 'btn btn-primary mx-2 btn-sm ml-auto';
   //*crear div que contenga el boton 
   const divClear = document.createElement('div');
   //*agregando clases
   divClear.className = 'col-2 mb-2';
   //*pasando boton a divClear
   divClear.appendChild(btn_limpiar);
   //*Función onclick
   btn_limpiar.onclick = function () {
      console.log("El botón LIMPIAR ha sido clickeado");
      //*selecciona al input y le asigna '' para blanquear.
      document.querySelectorAll('input').forEach(input => input.value = '');
      //!llamando la funcionrenderTableRowst para renderizar la tabla nuevamente 
      renderTableRowst(data, tbody, rowsPerPage, currentPage);
      renderPagination(currentPage, data, rowsPerPage, document.querySelector('#pagination-nav'))
   };
   //*pasasndo el div que continen el btn a contenedor de la tabla
   tableContainer.appendChild(divClear);
}

//*Funcion handSearch encargada de realizar la busqueda del input en la tabla.
function handleSearch(event, data) {
   //*obtenemos el valor del input y lo pasamos a minuscula
   const query = event.target.value.toLowerCase();

   //?nota: some nos ayuda a verificar si hay elementos que coincidan
   //*utilizamos el filtereddata nos permite filtrar los datos que coinciden 
   const filtrarData = data.filter(item =>
      Object.values(item).some(value =>
         String(value).toLowerCase().includes(query)
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

//*funcion encargada de renderizar el body de la tabla.
function renderTableRowst(data, tbody, rowsPerPage, currentPage) {

   //*limpiando el body de la tabla
   tbody.innerHTML = '';

   //*para saber en que elemento va a iniciar
   const startIndex = (currentPage - 1) * rowsPerPage;
   //*saber en que elemento va a finalizar
   const endIndex = startIndex + rowsPerPage;

   // console.log(startIndex ,endIndex,'currentPage');
   const paginatedData = data.slice(startIndex, endIndex);

   paginatedData.forEach((fila, index) => {
      //*creamos la fila de el body
      const row = document.createElement('tr');
      row.id = 'idRow' + index;
      //*creamos la columna y añadimos el numero de fila para despues añadir al elemnto de fila
      row.appendChild(document.createElement('td')).textContent = startIndex + index + 1;

      //*recorremos la data
      Object.entries(fila).forEach(([key, value]) => {

         //* verificamos si es un arreglo si es distinto se muestra en la fila
         if (!(Array.isArray(value))) {
            //*creamos las columnas
            const td = document.createElement('td');
            //*asignamos la manito a la fila
            td.style.cursor = 'pointer';
            //*asignamos el texto de cada columna
            td.textContent = value;
            //*añadimos las columnas a la fila
            row.appendChild(td);
            //*para pasar data y desplegar datos en fila
            row.onclick = () => {
               agregarFilas(fila['subData'], 'idRow' + index, tbody, index);
            }
         }
      });

      //*enviamos la fila al contenedor del cuerpo de la tabla.
      tbody.appendChild(row);
   });
}

function agregarFilas(data, idRow, tbody, index) {
   // console.log(data, 'data');
   //*elemento de la fila seleccionada
   const filaReferencia = document.getElementById(idRow);

   var indice = 1;
   //* creamos un fragmento para guardar todas las filas
   const fragmento = document.createDocumentFragment();

   data.forEach(obj => {
      console.log(obj, 'obj');
      const addRow = document.createElement('tr');
      addRow.id = 'idRow' + (indice) + '.' + (index);
      //*creamos la columna y añadimos el numero de fila para despues añadir al elemnto de fila
      addRow.appendChild(document.createElement('td')).textContent = (index + 1) + '.' + (indice);
      Object.values(obj).forEach((elemento, i) => {
         console.log(elemento);
         const tdSub = document.createElement('td');
         //*asignamos el texto de cada columna
         tdSub.textContent = elemento;
         //*añadimos las columnas a la fila
         addRow.appendChild(tdSub);

      })
        // Aplicar estilos personalizados a toda la fila para sobrescribir Bootstrap
      addRow.style.color = '#FFFFFF'; // Color del texto (por ejemplo, blanco)
      addRow.style.backgroundColor = '#428ea3'; // Color de fondo (por ejemplo, azul claro)

      //*añadiendo todas las filas que se crean al un contenedor provicional
      fragmento.appendChild(addRow);
      //*aumentando indice;
      indice++

   })

   // Ahora insertamos todas las filas de una vez debajo de la fila de referencia
   filaReferencia.parentNode.insertBefore(fragmento, filaReferencia.nextSibling);

}

//*funcion encargada de crear la paginacion de la tabla

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
   prevLink.textContent = 'Previous';
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
   nextLink.textContent = 'Next';
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

document.addEventListener("DOMContentLoaded", function () {
   const data = [
      {
         "Concesion": "CO-CNE-6236-19",
         "Permiso_Explotacion": "PE-CNE-3408-19",
         "ID_Expediente": "",
         "ID_Solicitud": "",
         "ID_Formulario_Solicitud": "RAM-000000033-2024",
         "CodigoAvisoCobro": "",
         "ID_Resolucion": "",
         "Placa": "AAK3148",
         "subData": [
            {
               "Concesion": "CO-CNE-6236-19",
               "Permiso_Explotacion": "PE-CNE-3408-19",
               "ID_Expediente": "",
               "ID_Solicitud": "",
               //   "ID_Formulario_Solicitud": "RAM-000000033-2024",
               "CodigoAvisoCobro": "",
               "ID_Resolucion": "",
               "Placa": "AAK3148"
            },
            {
               "Concesion": "CO-CNE-6236-19",
               "Permiso_Explotacion": "PE-CNE-3408-19",
               "ID_Expediente": "",
               "ID_Solicitud": "",
               // "ID_Formulario_Solicitud": "RAM-000000033-2024",
               "CodigoAvisoCobro": "",
               "ID_Resolucion": "",
               "Placa": "AAK3148",
            }
         ],
      },
      {
         "Concesion": "CO-CNE-6237-19",
         "Permiso_Explotacion": "PE-CNE-3408-19",
         "ID_Expediente": "",
         "ID_Solicitud": "",
         "ID_Formulario_Solicitud": "RAM-000000033-2024",
         "CodigoAvisoCobro": "",
         "ID_Resolucion": "",
         "Placa": "TCJ1084-TCJ1085",
         "subData": [
            {
               "Concesion": "CO-CNE-6236-19",
               "Permiso_Explotacion": "PE-CNE-3408-19",
               "ID_Expediente": "",
               "ID_Solicitud": "",
               "ID_Formulario_Solicitud": "RAM-000000033-2024",
               "CodigoAvisoCobro": "",
               "ID_Resolucion": "",
               "Placa": "AAK3148"
            },
         ]
      },
      {
         "Concesion": "CO-CNE-6239-19",
         "Permiso_Explotacion": "PE-CNE-3408-19",
         "ID_Expediente": "",
         "ID_Solicitud": "",
         "ID_Formulario_Solicitud": "t-000000033-2025",
         "CodigoAvisoCobro": "",
         "ID_Resolucion": "",
         "Placa": "TCB4072-TCB4073"
      },
      {
         "Concesion": "CO-CNE-6239-19",
         "Permiso_Explotacion": "PE-CNE-3408-19",
         "ID_Expediente": "",
         "ID_Solicitud": "",
         "ID_Formulario_Solicitud": "t-000000033-2025",
         "CodigoAvisoCobro": "",
         "ID_Resolucion": "",
         "Placa": "TCB4072-TCB4073"
      }
   ];

   mostrarData(data);
});
