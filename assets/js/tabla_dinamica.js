var currentPage = 1;
var rowsPerPage = 10;
var estadoInicial = '' //* Puedes establecer el estado 
var descripcionInicial = '';
var agregar = '';
var estadosSistem = ['IDE-1', 'IDE-2', 'IDE-3', 'IDE-4', 'IDE-5', 'IDE-6'];
var descripcionEstado=['EN PROCESO','EN VENTANILLA','CANCELADO','FINALIZADO','INADMISION','REQUERIDO'];
var usuario = (document.getElementById('id_user').value).toUpperCase();

//*esConsulta puede ser true (modo consulta) o false (modo estados);
var esConsultas = document.getElementById('esConsulta').value;
//*********************************************************************************************/
//* INICIO: funcion que me permite actualizar el estado y la descripcion segun btn de estados
//*********************************************************************************************/
//se ejecuta en estado_btn.js;
function actualizarEstado(estado, descripcion, agregar) {
   //*para que cuando cambie de estado se posicione en la pagina 1 
   currentPage = 1;
   // console.log(currentPage, 'currentPage se actualizo ');
   ultimoEstadoSeleccionado = estado;
   ultimoDescripSeleccionado = descripcion;
   ultimoagregado = agregar;
   console.log(agregar, 'agregar en actualizarEstado');
}
//*********************************************************************************************/
//* FINAL: funcion que me permite actualizar el estado y la descripcion segun btn de estados
//*********************************************************************************************/

//***********************************************************************/
//*INICIO: Funcion encargda de traer la informacion de la base de datos
//************************************************************* ********/
var pagados = '';
//*********************************************************************/
//*INICIO: Funcion encargada de mostrar las ram que estan pagadas 
//*********************************************************************/
function ramPagada() {
   var elemento = document.getElementById('idPagado');
   pagados = elemento.getAttribute('data-pagado');

   if (pagados === 'ramsPagadas') {
      // Cambiar a "no pagadas"
      elemento.setAttribute('data-pagado', 'RamsSinPagos');
      elemento.classList.remove('btn-light');
      elemento.classList.add('btn-success');

      vista_data('', '', ultimoagregado);
   } else {
      // Cambiar a "pagadas"
      elemento.setAttribute('data-pagado', 'ramsPagadas');
      elemento.classList.remove('btn-success');
      elemento.classList.add('btn-light');
      vista_data('', '', ultimoagregado);
   }
}
//*********************************************************************/
//*FINAL: Funcion encargada de mostrar las ram que estan pagadas 
//*********************************************************************/

function vista_data(estado = '', descripcion = '', agregar, num) {

   //*******************************************************************************/
   //*INICO: oculta el boton de agregar para los estados que son diferente a IDE-7;
   //*******************************************************************************/

   var elemento = document.getElementById('idAgregar');


   if (agregar !== '1' || esConsultas == true) {
      elemento.style.display = 'none';
      console.log('agregar no está habilitado', agregar);
   } else {
      elemento.style.display = 'inline';
      console.log('agregar', agregar);
   }

   //*******************************************************************************/
   //*INICO: oculta el boton de agregar para los estados que son diferente a IDE-7;
   //*******************************************************************************/

   //*Campo de filtro con los datos de busqueda.
   let campo = document.getElementById('id_filtro_select').value;
   let datoBuscar = document.getElementById('id_input_filtro').value;

   var url = '';
   if (esConsultas == true) {
      url = `${$appcfg_Dominio}/query_tabla_dinamica.php?estado=${(estado != '') ? estado : ultimoEstadoSeleccionado}&campo=${campo}&datoBuscar=${datoBuscar}&limit=${rowsPerPage}&page=${(num) ? num : currentPage}&pagados=${pagados}&esConsultas=${esConsultas}`;
   } else {
      url = `${$appcfg_Dominio}/query_tabla_dinamica.php?estado=${(estado != '') ? estado : ultimoEstadoSeleccionado}&campo=${campo}&datoBuscar=${datoBuscar}&limit=${rowsPerPage}&page=${(num) ? num : currentPage}&pagados=${pagados}`;
   }
   //*obteniendo lo que se encuentra en tabla_container.
   const loadingIndicator = document.getElementById("tabla-container").innerHTML;
   //*colocando inagne de carga en tabla_container.
   document.getElementById("tabla-container").innerHTML = '<center><img width="100px" height="100px" src="' + $appcfg_Dominio_Corto + 'ram/assets/images/loading-waiting.gif"></center>';

   //?nota:en estado si hay estado se envia si no se poner por defecto el ultimo estado seleccionado.
   //*peticion fetch que envia estado datoBuscar, limit, page
   fetch(url)
      .then(response => {
         if (!response.ok) {
            throw new Error('Error en la solicitud');
         }
         return response.json();
      })
      .then(data => {
         console.log(data, 'dataValores');

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
               fSweetAlertEventNormal(title, msg, type, errorcode = 'Se encontro un problema al traer la informacion intente de nuevo y si persiste contacte a tecnologia.')
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
         Swal.fire({
            title: '!ERROR DE DATOS¡',
            text: `OCURRIO UN PROBLEMA INTENTE MASTARDE ${error}`,
            icon: 'error',
            confirmButtonText: 'OK',
         });
         document.getElementById("tabla-container").innerHTML = loadingIndicator;
         console.error('No se pudo obtener la información revisar vista_data:', error);

      });
}
//***********************************************************************/
//*FINAL: Funcion encargda de traer la informacion de la base de datos
//************************************************************* ********/

//****** ***************************************************************************************************/
//* INICIO: funcion encargada de llenar el select de busqueda que envia el campo de busqueda en la tabla.
//*********************************************************************************************************/
// Obtener elementos
var selectElement = document.getElementById('id_filtro_select');
var inputElement = document.getElementById('id_input_filtro');
// var valor = document.getElementById('id_input_filtro').value.toUpperCase();


// Solo seleccionamos el input cuando el usuario haga clic directamente sobre él
inputElement.addEventListener('click', () => {
   inputElement.select();
});

// Esta es tu función ajustada para que el select funcione bien
function llenadoSelect(data) {
   const datos = data.dataSelect;
   const valorAnterior = selectElement.value;

   selectElement.innerHTML = ''; // Limpia el select
   var detalle = {}
   if (esConsultas == true) {
      detalle = {
         "SOLICITUD": "SOLICITUD",
         "NOMBRE_SOLICITUD": "NOMBRE_SOLICITUD",
         "RTN_SOLICITUD": "RTN_SOLICITUD",
         "PLACA": "PLACA",
         "USUARIO_CREACION": "USUARIO_CREACION",
         "USUARIO_ACEPTA": "USUARIO_ACEPTA",
      };
   } else {
      detalle = {
         "SOLICITUD": "SOLICITUD",
         "NOMBRE_SOLICITUD": "NOMBRE_SOLICITUD",
         "RTN_SOLICITUD": "RTN_SOLICITUD",
         "PLACA": "PLACA"
      };
   }

   datos.forEach((dato) => {
      if (detalle.hasOwnProperty(dato)) {
         const option = document.createElement('option');
         option.value = dato.toUpperCase();;
         option.textContent = detalle[dato];

         if (dato === valorAnterior) {
            option.selected = true;
         }

         selectElement.appendChild(option);
      }
   });
}

//****** ***************************************************************************************************/
//* FINAL: funcion encargada de llenar el select de busqueda que envia el campo de busqueda en la tabla.
//*********************************************************************************************************/

//*********************************************/
//*INICIO: funcion que crea la tabla dinamica
//*********************************************/
function TablaDinamica(data, descripcion) {
   // console.log(data, 'data tabladinamica');
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
      contenedor.innerHTML = `<div class="container-sm"><div class="alert alert-info text-center" role="alert">
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
      if (esConsultas == true && descripcion === '*TODOS') {
         tableTitle.innerHTML = `<strong>INFORMACIÓN ${descripcion}</strong>`;
      } else {
         if ((esConsultas == true || esConsultas == false) && descripcion !== '*TODOS') {
            tableTitle.innerHTML = `<strong>INFORMACIÓN ESTADO ${descripcion}</strong>`;
         }
      }
      //*realizando estilo de el titulo
      tableTitle.className = 'titleTable';
      //*enviandotitulo an contenedor de la tabla
      tableContainer.appendChild(tableTitle);

      //*creando el contenedor de tabla
      const table = document.createElement('table');
      //*creando los estilos de la tabla
      table.className = 'table table-striped table-hover mb-5 table-responsive table-sm';
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

      const th1 = document.createElement('th');
      th1.textContent = '#';
      th1.style.margin = '0px';
      //*creando la columna y asignando tento para el numero a la fila
      headerRow.appendChild(th1);
      //*recorriendo los datos del encabezado
      encabezadoExcluye = ['estadoCompartido', 'USUARIO_ACEPTA', 'USUARIO_CREACION', 'AGREGRAR', 'COMPARTIDO', 'Aviso_Cobro', 'USUARIOS_COMPARTIDOS'];
      //  let dataOmicionEncabezado = dataNueva(data);
      data.encabezados.forEach(encabezado => {
         // console.log(encabezado,'en....cabezado');
         // if(encabezadoExcluye.includes(encabezado))
         if (!(encabezadoExcluye.includes(encabezado))) { //&& encabezado !== 'lINK_PAGO'
            //*creando la columna del encabezado
            const th = document.createElement('th');
            th.className = "text-start";
            th.style.textAlign = 'center';
            //*asignando data del encabezado
            th.textContent = encabezado;
            //*enviando columnna de encabezado a fila de la tabla
            headerRow.appendChild(th);
         }
      });

      if (descripcion == 'EN PROCESO' || descripcion == 'EN VENTANILLA' || descripcion == 'FINALIZADO' || descripcion == 'CANCELADO' || descripcion == 'INADMISION' || descripcion == '*TODOS') {
         const th_accion = document.createElement('th');
         th_accion.style.textAlign = 'center';
         //*asignando data del encabezado
         th_accion.textContent = 'ACCIONES';
         //*enviando columnna de encabezado a fila de la tabla
         headerRow.appendChild(th_accion);
      }
      //*enviando fila a contenedor de encabezado
      thead.appendChild(headerRow);

      //!funcion encargada de renderizar el cuerpo de la tabla
      renderTableRows(data.datos, tbody, data, descripcion, data);
      //*enviando el contenedor del encabezado y cuerpor al contenedor de la tabla
      table.appendChild(thead);
      table.appendChild(tbody);
      //*enviando la tabla al contenedor principal de la tabla completa
      tableContainer.appendChild(table);
      //!obteniendo datos de paginacion

      const paginationContainer = createPagination(data['totalRows']);
      //  const paginationContainer = createPagination((data.datos).length);
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
//*********************************************/
//*FINAL: funcion que crea la tabla dinamica
//*********************************************/

function dataNueva(data) {

   console.log(data);
   //*Arreglo con la data a excluir
   let dataExcluir = ["lINK_PAGO", "ESTADO_Pago",];
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

//**********************************************/
//*INICIO: encargada de filtar la información
//**********************************************/
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
   filterContainer.className = 'mb-3 col-4';

   //*creando el elemento select
   const filtro = document.createElement('select');
   //*creando el id del select
   filtro.id = 'filtro';
   //*creando la clases
   filtro.className = 'form-select';
   //*añadiendo atributos
   filtro.setAttribute('aria-label', 'Selecciona el campo de busqueda');

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
   btnContainer.className = "col-8"

   //*creando el boton
   const idBtnBuscar = document.createElement('button');
   //*creando las propiedades del boton.
   idBtnBuscar.id = 'idBtnBuscar';
   idBtnBuscar.className = 'btn btn-info';
   idBtnBuscar.textContent = 'Buscar';

   const idBtnAgregar = document.createElement('button');
   idBtnAgregar.id = 'idBtnAgregar';
   idBtnAgregar.className = 'btn btn-success';
   idBtnAgregar.innerHTML = `<i class="fa-solid fa-square-plus"></i> AGREGAR`;

   //*creando evento click del boton que llama a la funcion "filterTable"
   idBtnBuscar.onclick = () => {
      filterTable(data, filtro.value, idInputBuscar.value);
   };


   //*enviando los elementos a los contenedores
   filterContainer.appendChild(filtro);
   filterContainer.appendChild(idInputBuscar);
   btnContainer.appendChild(idBtnBuscar);
   btnContainer.appendChild(idBtnAgregar);
   contenedorFiltro.appendChild(filterContainer);
   contenedorFiltro.appendChild(btnContainer);

   tableContainer.appendChild(contenedorFiltro);

}

function agregarRams() {
   location.href = `${$appcfg_Dominio}ram.php`
}
//**********************************************/
//*FINAL: encargada de filtar la información
//**********************************************/

//***********************************************************/
//*INICIO: funcion encargada de crear el body de la tabla
//***********************************************************/
function renderTableRows(data, tbody, todo, descripcion) {
   // console.log(data, 'datassss.');
   tbody.innerHTML = ''; //*limpiando cuerpo de la tabla
   //*recorriendo datos 
   data.forEach((fila, index) => {
      //*creando las filas del cuerpo
      const row = document.createElement('tr');
      //*creando columna del cuerpo y asignandole el indice de cada fila
      let tdn = document.createElement('td');

      tdn.textContent = (currentPage - 1) * rowsPerPage + index + 1;
      tdn.style.margin = '0px';
      row.appendChild(tdn)
      // console.log(fila[8], 'fila');
      //*para dividir los elementos del pago.
      var dividirDatosPagos = [];
      if (fila[8] !== null) {
         dividirDatosPagos = fila[8].split("-");
         //  console.log(dividirDatosPagos);
      }
      var activo = '';
      var folioPago = '';
      var url = '';
      //*recorriendo los datos
      fila.forEach((valor, colIndex) => {
         activo = (dividirDatosPagos[1] != undefined) ? dividirDatosPagos[1] : 'NO ESTA ACTIVO';
         folioPago = (dividirDatosPagos[0] != undefined) ? dividirDatosPagos[0] : 'NO HAY AVISO DE COBRO';
         //?nota:Fila[0] es el ram
         // https://satt2.transporte.gob.hn:90/api_rep.php?ra=S&action=get-facturaPdf&nu=205463
         url = `${$appcfg_Dominio_Raiz}:90/api_rep.php?ra=S&action=get-facturaPdf&nu=${fila[0]}`
         // url = `${$appcfg_Dominio}Documentos/${fila[0]}/AvisodeCobro_${fila[0]}.pdf`
         if (colIndex !== 8 && colIndex !== 9 && colIndex !== 10 && colIndex !== 11 && colIndex !== 12 && colIndex !== 13) {
            // console.log(activo, folioPago, 'activoFolioPago');
            //*creando la comulana de los datos 
            const td = document.createElement('td');
            td.className = 'text-start'
            td.style.textAlign = 'center text-center';

            if (colIndex == 7) {
               td.innerHTML = `<span class="badge badge-no">NO</span>`;
               actualizarBadge(valor, td); // Inicialmente muestra "NO"
               setTimeout(() => {
                  actualizarBadge(valor, td); // Después de un tiempo, muestra "SI"
               }, 2000);
            } else {
               if (colIndex === 6) {
                  const soloFecha = valor.split(" ")[0];
                  td.textContent = soloFecha;
               } else {
                  td.textContent = valor;
               }
            }
            //*asignando el valor del segundo elemento el onclick 
            if (colIndex === 0) {
               //* Cambia el cursor para indicar que es clickeable
               td.style.cursor = 'pointer';
               td.style.color = '#033b4b';
               td.style.textShadow = '3px 3px 5px rgba(5, 5, 5, 0.3)';
               //*evento onclick a la columna que me redirecciona.
               td.onclick = () => {
                  //* redireccionando y eviando RAM
                  if (esConsultas != true) {
                     window.location.href = `${$appcfg_Dominio}ram.php?RAM=${valor}`;
                  } else {
                     //*link para cunado es solo consulta y no se puede modificar nada
                     window.location.href = `${$appcfg_Dominio}ram.php?RAM=${valor}&consulta=true`;
                  }
               };
            }
            //*enviando columna a las filas
            row.appendChild(td);
         }
      });

      //*Para que los iconos solo esten el en estado de PROCESO
      if (descripcion == 'EN PROCESO') {
         //*creamos una columna de la tabla
         const td_accion = document.createElement('td');  // Crea la celda principal para la acción
         td_accion.style.textAlign = 'center';  // Centra los íconos en la celda principal
         td_accion.className = `fondoAccion `;

         // Inicializar tooltip de Bootstrap
         setTimeout(() => {
            tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
               return new bootstrap.Tooltip(tooltipTriggerEl);
            });
         }, 100);
         //* Crea la fila de acción
         const row_accion = document.createElement('tr');
         // row_accion.className = "fondoAccion";
         row_accion.style.textAlign = 'center';

         const td_dollar = document.createElement('td');

         //*creando el icono de cancelar
         td_dollar.innerHTML = ` <div style="display: flex; align-items: center; gap: 5px;"> 
                    ${activo === '2'
               ? `<span class="text-center">
                           <a href="${url}" target="_blank"  style="display: flex; flex-direction: column; align-items: center; cursor: pointer; text-decoration:none">
                              <i class="fas fa-dollar-sign dolar-span-activo tooltip-verde  text-center" data-bs-toggle="tooltip" title="${folioPago}" style="font-size: 23px;">
                              </i>
                              <span class="dolar-span-activo-text" style="font-size: 12px;"></span>
                           <a/>   
                           
                        </span>`
               : `<span style="display: flex; flex-direction: column; align-items: center; cursor: pointer;">
               <a href="${url}" target="_blank"  style="display: flex; flex-direction: column; align-items: center; cursor: pointer; text-decoration:none">
                           <i class="fas fa-dollar-sign dolar-span text-center"  data-bs-toggle="tooltip" title="${folioPago}" style="font-size: 23px;">
                           </i>
                           <span class="dolar-span-text " style="font-size: 12px;"></span>
                        </span>
                        <a/> 
                        ` //${valor}

            }
                  </div>`;
         // Inicializar tooltip de Bootstrap
         setTimeout(() => {
            tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
               return new bootstrap.Tooltip(tooltipTriggerEl);
            });
         }, 100);

         //*Crea la celda para el primer ícono (ventana de cierre)

         if (esConsultas != true) {
            var td_cancelar = document.createElement('td');
            // td_cancelar.style.fontSize = '30px'; // Cambia el tamaño del ícono

            //*creando el icono de cancelar
            td_cancelar.innerHTML = `
            <span onclick="fUpdateEstado('IDE-3','${fila[0]}')" 
            class="accion-item " data-bs-toggle="tooltip" title="Boton para Cancelar solicitud" style="display: flex; flex-direction: column; align-items: center; cursor: pointer;">
               <i class="fas fa-window-close cancelar" style="font-size: 35px;"></i>
               <span class="cancelar" style="font-size: 12px;">CANCELAR</span>
            </span>
            `;
            //*creando el icono de inadmitido
            var td_prohibido = document.createElement('td');
            td_prohibido.innerHTML = `
            <span class="inadmitido" onclick="fUpdateEstado('IDE-4','${fila[0]}')" 
            class="accion-item inadmitido" data-bs-toggle="tooltip" title="Boton para Inadmitir solicitud" style="display: flex; flex-direction: column; align-items: center; cursor: pointer;">
               <i class="fas fa-ban inadmitido" style="font-size: 35px;"></i>
               <span class="inadmitido" style="font-size: 12px;">INADMITIDO</span>
            </span>
            `;

         }
         //*creando el icono de compartido
         const td_compartido = document.createElement('td');
         td_compartido.className = 'center'; // Centra los íconos en la celda principal
         if (fila[9] && fila[12] != null) {
            td_compartido.innerHTML = `
            <span class="compartiendo text-center"  
            class="accion-item compartiendo"  data-bs-toggle="tooltip" class="accion-item compartiendo"  data-bs-toggle="tooltip" title="${(fila[11] && fila[11] !== usuario)
                  ? 'PERTENECE A: ' + fila[11].toUpperCase() +
                  ' COMPARTIDO A: ' + fila[12]
                  // .split(',')
                  // .map(u => u.trim())
                  // .filter(u => u !== usuario)
                  // .join(', ')
                  // .toUpperCase()
                  : ''}"
             style="display: flex; flex-direction: column; align-items: center; cursor: pointer;">
            <i class="fas fa-share-alt compartiendo" style="font-size: 24px;"></i>
            <span class="compartiendo" style="font-size: 12px;">COMPARTIDO</span>
            </span> `;
         } else {
            td_compartido.innerHTML = `
            <span class="noCompartido text-center"  
            class="accion-item noCompartido text-center" data-bs-toggle="tooltip" title="NO ESTA COMPARTIDA"  style="display: flex; flex-direction: column; align-items: center; cursor: pointer;">
            <i class="fas fa-share-alt noCompartido" style="font-size: 24px;"></i>
            <span class="noCompartido text-center" style="font-size: 12px;">NO COMPARTIDO</span>
            </span> `;
         }
         //*Agregar las celdas a la fila de acción
         row_accion.appendChild(td_dollar);

         if (esConsultas != true) {
            row_accion.appendChild(td_cancelar);
            row_accion.appendChild(td_prohibido);
         }
         row_accion.appendChild(td_compartido);

         //*Agregar la fila de acción a la fila principal
         td_accion.appendChild(row_accion);
         row.appendChild(td_accion);
      }

      //*Para que los iconos solo esten el en estado de PROCESO
      if (descripcion == 'EN VENTANILLA' || descripcion == 'FINALIZADO' || descripcion == 'CANCELADO' || descripcion == 'INADMISION' || descripcion == '*TODOS') {
         const td_accion = document.createElement('td');
         td_accion.className = 'fondoAccion';
         td_accion.style.textAlign = 'center';  // Centrado horizontal

         const row_accion = document.createElement('tr');
         row_accion.style.textAlign = 'center';  // Centrado horizontal

         const td_dollar = document.createElement('td');
         // td_cancelar.style.fontSize = '30px'; // Cambia el tamaño del ícono

         //*creando el icono de cancelar
         td_dollar.innerHTML = ` <div style="display: flex; align-items: center; gap: 5px;"> 
                    ${activo === '2'
               ? `<span class="text-center">
                           <a href="${url}" target="_blank"  style="display: flex; flex-direction: column; align-items: center; cursor: pointer; text-decoration:none">
                              <i class="fas fa-dollar-sign dolar-span-activo tooltip-verde  text-center" data-bs-toggle="tooltip" title="${folioPago}" style="font-size: 23px;">
                              </i>
                              <span class="dolar-span-activo-text" style="font-size: 12px;"></span>
                           <a/>   
                           
                        </span>`
               : `<span style="display: flex; flex-direction: column; align-items: center; cursor: pointer;">
               <a href="${url}" target="_blank"  style="display: flex; flex-direction: column; align-items: center; cursor: pointer; text-decoration:none">
                           <i class="fas fa-dollar-sign dolar-span text-center"  data-bs-toggle="tooltip" title="${folioPago}" style="font-size: 23px;">
                           </i>
                           <span class="dolar-span-text " style="font-size: 12px;"></span>
                        </span>
                        <a/> 
                        ` //${valor}
            }
                  </div>`;
         // Inicializar tooltip de Bootstrap
         setTimeout(() => {
            tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
               return new bootstrap.Tooltip(tooltipTriggerEl);
            });
         }, 100);

         const td_compartido = document.createElement('td');
         td_compartido.className = 'center';
         td_compartido.style.display = 'flex';
         td_compartido.style.flexDirection = 'column';
         td_compartido.style.alignItems = 'center';
         td_compartido.style.justifyContent = 'center';  // Centrado vertical adicional si hace falta
         td_compartido.classList.add('td-flex-center');

         if (fila[9] && fila[12] != null) {
            td_compartido.innerHTML = `
         <span class="accion-item compartiendo" data-bs-toggle="tooltip" title="${(fila[10] && fila[10] !== usuario)
                  ? 'PERTENECE A: ' + fila[10].toUpperCase() +
                  ' COMPARTIDO A: ' + fila[12]
                  // .split(',')
                  // .map(u => u.trim())
                  // .filter(u => u !== usuario)
                  // .join(', ')
                  // .toUpperCase()
                  : ''}"
            style="display: flex; flex-direction: column; align-items: center; cursor: pointer;">
            <i class="fas fa-share-alt compartiendo" style="font-size: 24px;"></i>
            <span class="compartiendo" style="font-size: 12px;">COMPARTIDO</span>
         </span>
      `;
         } else {
            td_compartido.innerHTML = `
         <span class="noCompartido accion-item noCompartido" data-bs-toggle="tooltip" title=""  style="display: flex; flex-direction: column; align-items: center; cursor: pointer;">
            <i class="fas fa-share-alt noCompartido" style="font-size: 24px;"></i>
            <span class="noCompartido" style="font-size: 12px;">NO COMPARTIDO</span>
         </span>
      `;
         }
         row_accion.appendChild(td_dollar);
         console.log('esConsultas dentro de proceso', esConsultas);

         row_accion.appendChild(td_compartido);
         td_accion.appendChild(row_accion);

         row.appendChild(td_accion);
         // Tooltips
         setTimeout(() => {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(el => new bootstrap.Tooltip(el));
         }, 100);
      }

      //*enviando filas a las columnas
      tbody.appendChild(row);
   });
}
//*********************************************************/
//*FINAL: funcion encargada de crear el body de la tabla
//*********************************************************/

// td.innerHTML = `<span id="miBadge" class="badge">NO</span>`;


function actualizarBadge(valor, td) {
   let badge = td.querySelector('.badge');
   if (!badge) return; // Previene errores si no existe el span

   if (valor === "Sí" || valor === "SI") {
      badge.textContent = "SI";
      badge.classList.remove("badge-no");
      badge.classList.add("badge-si");
   } else {
      badge.textContent = "NO";
      badge.classList.remove("badge-si");
      badge.classList.add("badge-no");
   }
}

//**********************************************************************************************/
//*INICIO: funcion encargada de cambiar los estado de la solicitud por cancelado o inadmitido
//************************************************************************************************/
function fUpdateEstado(estado, ram, echo) {

   //*confirmamos si el usuario esta seguro de hacer el cambio de estado coon un alerte.
   Swal.fire({
      title: `CONFIRMACIÓN`,
      html: `¿ESTA SEGURO DE CAMBIAR EL ESTADO DE LA SOLICITUD  <strong>${ram}</strong>?`,
      icon: 'info',
      showCancelButton: true,
      confirmButtonText: 'SÍ',
      cancelButtonText: 'CANCELAR',
   }).then((result) => {
      //* Si confirma que está seguro de agregar la concesión, llamamos la función para agregarla.
      if (result.isConfirmed) {
         //*! llamamos la funcion que hace el cambio en la base de datos 
         fUpdateEstadoFetch(estado, ram, echo);
      }
   });

}
//*Funcion encargada de hacer el metodo POST  al abase de datos para realizar el cmabio.
function fUpdateEstadoFetch(estado, ram, echo) {
   //* Crea un objeto FormData a partir de tu formulario
   let fd = new FormData(document.forms.form1);

   //*La URL del API que estás utilizando
   let url = `${$appcfg_Dominio}Api_Ram.php`;

   //* Agregar parámetros adicionales al FormData
   fd.append("action", "update-estado-preforma");
   fd.append("RAM", JSON.stringify(ram));
   fd.append("idEstado", JSON.stringify(estado));
   fd.append("echo", JSON.stringify(true));

   //*Opciones para la solicitud Fetch
   const options = {
      method: "POST",   //* Método de la solicitud
      body: fd,         //*El cuerpo con los datos del FormData
   };

   //* Realizar la solicitud Fetch
   fetch(url, options)
      .then(response => response.json())
      .then(datos => {
         // console.log("Respuesta del servidor:", dato);
         //*manejando el error
         if (typeof datos.error != "undefined") {
            fSweetAlertEventNormal(
               datos.errorhead,
               datos.error + "- " + datos.errormsg,
               "error"
            );
         } else {
            //* si todo esta bien mandamos notificacion de exito.
            sendToast(
               "ESTADO DE SOLICITUD CAMBIADO EXITOSAMENTE",
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
      })
      .catch(error => {
         console.error("Error en la solicitud:", error);
      });
}
//**********************************************************************************************/
//*FINAL: funcion encargada de cambiar los estado de la solicitud por cancelado o inadmitido
//************************************************************************************************/

//? field =la columna donde se filtrara.
//? el dato que busco en la columna.

//*********************************************************************/
//* INICIO: Funcion encargado de filtrar la informacion en la tabla
//*********************************************************************/
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
//*********************************************************************/
//* FINAL: Funcion encargado de filtrar la informacion en la tabla
//*********************************************************************/

//?nota: Esta pagiancion funciona con el total de filas segun la consulta de los datos
//*****************************************************************/
//*INICIO: funcion encargada de realizar la Paginacion de la tabla.
//*****************************************************************/
function createPagination(totalRows) {
   // Crear el contenedor de la paginación
   const paginationContainer = document.createElement('nav');
   paginationContainer.setAttribute('aria-label', 'Page navigation example');

   // Contenedor <ul> para los botones
   const pagination = document.createElement('ul');
   pagination.className = 'pagination justify-content-start'; // ← alineado a la izquierda

   // Convertir a número y calcular total de páginas
   const total = parseInt(totalRows);
   const totalPages = Math.ceil(total / rowsPerPage);

   // Verificar si se necesita paginación
   if (totalPages <= 1) {
      return document.createElement('div');
   }

   // Botón "Anterior"
   const prevItem = document.createElement('li');
   prevItem.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
   const prevLink = document.createElement('a');
   prevLink.className = 'page-link';
   prevLink.href = '#';
   prevLink.textContent = 'Anterior';
   prevLink.onclick = (e) => {
      e.preventDefault();
      if (currentPage > 1) {
         currentPage--;
         vista_data('', '', agregar);
      }
   };
   prevItem.appendChild(prevLink);
   pagination.appendChild(prevItem);

   // Páginas visibles (máximo 5 botones)
   const maxVisiblePages = 5;
   let startPage = Math.max(1, currentPage - 2);
   let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

   // Ajustar si estamos al final
   if ((endPage - startPage) < (maxVisiblePages - 1)) {
      startPage = Math.max(1, endPage - maxVisiblePages + 1);
   }

   for (let page = startPage; page <= endPage; page++) {
      const pageItem = document.createElement('li');
      pageItem.className = `page-item ${currentPage === page ? 'active' : ''}`;
      const pageLink = document.createElement('a');
      pageLink.className = 'page-link';
      pageLink.href = '#';
      pageLink.textContent = page;
      pageLink.onclick = (e) => {
         e.preventDefault();
         currentPage = page;
         vista_data('', '', agregar);
      };
      pageItem.appendChild(pageLink);
      pagination.appendChild(pageItem);
   }

   // Botón "Siguiente"
   const nextItem = document.createElement('li');
   nextItem.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
   const nextLink = document.createElement('a');
   nextLink.className = 'page-link';
   nextLink.href = '#';
   nextLink.textContent = 'Siguiente';
   nextLink.onclick = (e) => {
      e.preventDefault();
      if (currentPage < totalPages) {
         currentPage++;
         vista_data('', '', agregar);
      }
   };
   nextItem.appendChild(nextLink);
   pagination.appendChild(nextItem);

   paginationContainer.appendChild(pagination);
   return paginationContainer;
}


//*****************************************************************/
//*FINAL: funcion encargada de realizar la Paginacion de la tabla.
//*****************************************************************/
//***********************************************************/
//*INICIO:funcion encargada de limpiar el input
//***********************************************************/


function limpiar() {
   document.getElementById('id_input_filtro').value = '';

   const select = document.getElementById('id_filtro_select');
   select.selectedIndex = 0; // Esto selecciona la primera opción

   vista_data(estadoInicial, descripcionInicial, agregar);
   pagados = '';
}

//***********************************************************/
//*FINAL:funcion encargada de limpiar el input
//***********************************************************/

window.onload = function () {
   setTimeout(() => {
      let elemento = document.getElementById("buttonContainer");
      let infoEstado = JSON.parse(elemento?.getAttribute('data-info'));

      if (infoEstado != null) {
         infoEstado.forEach((element, index) => {
            if (index == 0) {
               console.log(element, 'element');
               estadoInicial = element[0];
               descripcionInicial = element[1];
               agregar = element[2];
            }
         })

         ultimoagregado = agregar;
         // console.log(ultimoagregado, 'ultimo');

         actualizarEstado(estadoInicial, descripcionInicial, agregar);
         //* Llama a la función vista_data con los parámetros deseados
         vista_data(estadoInicial, descripcionInicial, agregar);

      }

   }, 1000);
};

