var currentPage = 1;
var rowsPerPage = 10;
var estadoInicial = '' //* Puedes establecer el estado 
var descripcionInicial = '';
var agregar = '';
var estadosSistem = ['IDE-1', 'IDE-2', 'IDE-3', 'IDE-4', 'IDE-5', 'IDE-6', 'IDE-7', 'IDE-8'];
var descripcionEstado = ['EN PROCESO', 'EN VENTANILLA', 'CANCELADO', 'FINALIZADO', 'INADMISION', 'REQUERIDO'];
var usuario = (document.getElementById('id_user').value).toUpperCase();

//*esConsulta puede ser true (modo consulta) o false (modo estados);
var esConsultas = document.getElementById('esConsulta').value;
var aumento = 0;
//*********************************************************************************************/
//* INICIO: funcion que me permite actualizar el estado y la descripcion segun btn de estados
//*********************************************************************************************/
//?NOTA:se ejecuta en estado_btn.js;
function actualizarEstado(estado, descripcion, agregar) {
   //*para que cuando cambie de estado se posicione en la pagina 1 
   currentPage = 1;
   ultimoEstadoSeleccionado = estado;
   ultimoDescripSeleccionado = descripcion;
   ultimoagregado = agregar;
}
//*********************************************************************************************/
//* FINAL: funcion que me permite actualizar el estado y la descripcion segun btn de estados
//*********************************************************************************************/

//*****************************************************************/
//*INICIO:cuncion encargada de poner el color activo a los botones
//*****************************************************************/
//?NOTA:se ejecuta en estado_btn.js; Y  cuando se carga por primera vez.
function activar(boton) {
   //*obteniendo todos los botones con la clase btn-ligth.
   const botones = document.querySelectorAll('button.btn-light');
   //*boton precionado
   const botonSeleccionado = document.getElementById(`estado_${boton}`);
   //*quitar la clase activo
   botones.forEach(btn => btn.classList.remove('btn-light-activo'));
   //*al boton seleccionado le asignamos la clase activo
   botonSeleccionado.classList.add('btn-light-activo');
}
//*****************************************************************/
//*FINAL:cuncion encargada de poner el color activo a los botones
//*****************************************************************/

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
      //* Cambiar a "no pagadas"
      elemento.setAttribute('data-pagado', 'RamsSinPagos');
      elemento.classList.remove('btn-light');
      elemento.classList.add('btn-success');

      vista_data('', '', ultimoagregado);
   } else {
      //* Cambiar a "pagadas"
      elemento.setAttribute('data-pagado', 'ramsPagadas');
      elemento.classList.remove('btn-success');
      elemento.classList.add('btn-light');
      vista_data('', '', ultimoagregado);
   }
}
//*********************************************************************/
//*FINAL: Funcion encargada de mostrar las ram que estan pagadas
//*********************************************************************/

//*******************************************************************************/
//*INICO: oculta el boton de agregar para los estados que son diferente a IDE-7;
//*******************************************************************************/

function vista_data(estado, descripcion = '', agregar, num) {
   //*******************************************************************************/
   //*INICO: oculta el boton de agregar para los estados que son diferente a IDE-7;
   //*******************************************************************************/

   var elemento = document.getElementById('idAgregar');

   if (agregar !== '1' || esConsultas == true) {
      elemento.style.display = 'none';
   } else {
      elemento.style.display = 'inline';
   }

   //*Campo de filtro con los datos de busqueda.
   var campo = document.getElementById('id_filtro_select').value;
   var datoBuscar = document.getElementById('id_input_filtro').value.trim();

   if (document.getElementById('id_filtro_select').value != 'BLANCO') {
      document.getElementById('id_input_filtro').style.display = 'inline';
   } else {
      document.getElementById('id_input_filtro').style.display = 'none';
   }

   document.getElementById('id_input_filtro').addEventListener('keydown', function (event) {
      if (event.key === 'Enter') {
         if (document.getElementById('id_input_filtro').value.trim() === '') {
            Swal.fire({
               title: '!ALERTA¡',
               text: 'NO HAY CRITERIO DE BÚSQUEDA INGRESE CRITERIO DE BUSQUEDA',
               icon: 'warning',
               confirmButtonText: 'OK',
            })
         } else {
            vista_data();
         }
      }
   });

   document.getElementById('idInputBuscar').addEventListener('click', function (event) {
      if (document.getElementById('id_input_filtro').value.trim() === '') {
         Swal.fire({
            title: '!ALERTA¡',
            text: 'NO HAY CRITERIO DE BÚSQUEDA INGRESE CRITERIO DE BUSQUEDA',
            icon: 'warning',
            confirmButtonText: 'OK',
         })
      } else {
         vista_data();
      }
   })

   var url = '';
   //*asignando los estados.
   if (estado == undefined) {
      estado = ultimoEstadoSeleccionado
   } else {
      if (estado == '') {
         estado = ultimoEstadoSeleccionado
      } else {
         if (estado == 'undefined') {
            estado = ultimoEstadoSeleccionado;
         }
      }
   }

   if (esConsultas == true) {
      url = `${$appcfg_Dominio}/query_tabla_dinamica.php?estado=${estado}&campo=${campo}&datoBuscar=${datoBuscar}&limit=${rowsPerPage}&page=${(num) ? num : currentPage}&pagados=${pagados}&esConsultas=${esConsultas}`;
   } else {
      url = `${$appcfg_Dominio}/query_tabla_dinamica.php?estado=${estado}&campo=${campo}&datoBuscar=${datoBuscar}&limit=${rowsPerPage}&page=${(num) ? num : currentPage}&pagados=${pagados}`;
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
//* Obtener elementos
var selectElement = document.getElementById('id_filtro_select');
var inputElement = document.getElementById('id_input_filtro');

//* Solo seleccionamos el input cuando el usuario haga clic directamente sobre él
inputElement.addEventListener('click', () => {
   inputElement.select();
});

//*********************************************/
//*INICIO: funcion que crea la tabla dinamica
//*********************************************/
function TablaDinamica(data, descripcion) {

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

      var tituloEstado = '';
      document.getElementById('tituloPagina').innerHTML = '';
      if (esConsultas == true && descripcion === '*TODOS') {
         tituloEstado = `<strong class="titleTable">INFORMACIÓN  ${descripcion}</strong>`;
         document.getElementById('tituloPagina').innerHTML = '<strong>CONSULTA DE RENOVACIONES AUTOMATICAS </strong>' + tituloEstado;
      } else {
         if ((esConsultas == true || esConsultas == false) && descripcion !== '*TODOS') {
            tituloEstado = `<strong class="titleTable">INFORMACIÓN ESTADO ${descripcion}</strong>`;
            document.getElementById('tituloPagina').innerHTML = '<strong>INGRESO DE RENOVACIONES AUTOMATICAS </strong>' + tituloEstado;
         }
      }
      //*creando el contenedor de tabla
      const table = document.createElement('table');
      //*creando los estilos de la tabla
      table.className = 'table table-striped table-hover mb-5  table-sm';
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
      //*para saber el estado de la RAM actual.
      encabezadoExcluye = new Set(['PLACA_ENTRA_EXP', 'estadoCompartido', 'AGREGRAR', 'COMPARTIDO', 'Aviso_Cobro', 'USUARIOS_COMPARTIDOS', 'ESTADO_PLACA', 'TOTAL_PAGADAS', 'CONCESIONES', 'TRAMITES', 'PLACA_ENTRA', 'PLACA_SALE']);

      data.encabezados.forEach((encabezado, index) => {
         if (!encabezadoExcluye.has(encabezado)) {
            //*creando la columna del encabezado
            const th = document.createElement('th');
            th.textContent = (encabezado == 'USUARIO_CREACION') ? 'CREACIÓN' :
               (encabezado == 'USUARIO_ACEPTA') ? 'ACEPTA' : (encabezado == 'PLACA') ? 'PLACA(S)/CANTIDADES' : encabezado;

            if (encabezado === 'PLACA') {
               th.className = "text-center";
            } else {
               th.className = "text-start";
            }
            //*enviando columnna de encabezado a fila de la tabla
            headerRow.appendChild(th);
         }
      });

      if (descripcion == 'EN PROCESO' || descripcion == 'EN VENTANILLA' || descripcion == 'FINALIZADO' || descripcion == 'CANCELADO' || descripcion == 'INADMISION' || descripcion == '*TODOS' || descripcion == 'RETROTRAIDO POR ERROR DE USUARIO') {
         const th_accion = document.createElement('th');
         th_accion.style.textAlign = 'center';
         //*asignando data del encabezado
         th_accion.textContent = 'ACCIONES';
         //*enviando columnna de encabezado a fila de la tabla
         headerRow.appendChild(th_accion);
      }
      //*enviando fila a contenedor de encabezado
      thead.appendChild(headerRow);
      console.log(data.dato, 'data.datos');
      //!funcion encargada de renderizar el cuerpo de la tabla
      renderTableRows(data.dato, tbody, data, descripcion, data);
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
//**********************************************/
//*FINAL: encargada de filtar la información
//**********************************************/

//***********************************************************/
//*INICIO: funcion encargada de crear el body de la tabla
//***********************************************************/
function renderTableRows(data, tbody, todo, descripcion) {
   console.log(data, 'datassss.');
   tbody.innerHTML = ''; //*limpiando cuerpo de la tabla
   //*recorriendo datos 
   data.forEach((fila, index) => {
      // console.log(fila, 'fila');
      //*creando las filas del cuerpo
      const row = document.createElement('tr');
      row.id = `fila-${index}`;
      //*creando columna del cuerpo y asignandole el indice de cada fila
      let tdn = document.createElement('td');

      tdn.textContent = (currentPage - 1) * rowsPerPage + index + 1;
      tdn.style.margin = '0px';
      row.appendChild(tdn)

      //*para dividir los elementos del pago.
      var dividirDatosPagos = [];
      if (typeof fila['Aviso_Cobro'] === 'string' && fila['Aviso_Cobro'].includes('-')) {
         dividirDatosPagos = fila['Aviso_Cobro'].split('-');
      }

      var activo = '';
      var folioPago = '';
      var url = '';

      for (const key in fila) {
         if (Object.prototype.hasOwnProperty.call(fila, key)) {
            const element = fila[key];

            activo = (dividirDatosPagos[1] != undefined) ? dividirDatosPagos[1] : 'NO ESTA ACTIVO';
            folioPago = (dividirDatosPagos[0] != undefined) ? dividirDatosPagos[0] : 'NO HAY AVISO DE COBRO';
            // console.log(folioPago, 'folioPago');
            //?nota:Fila[0] es el ram
            // https://satt2.transporte.gob.hn:90/api_rep.php?ra=S&action=get-facturaPdf&nu=205463
            url = `${$appcfg_Dominio_Raiz}:90/api_rep.php?ra=S&action=get-facturaPdf&nu=${folioPago}`
            // url = `${$appcfg_Dominio}Documentos/${fila[0]}/AvisodeCobro_${fila[0]}.pdf`

            if (key !== 'PLACA_SALE' && key !== 'PLACA_ENTRA' && key !== 'PLACA_ENTRA_EXP' && key !== ('Aviso_Cobro') &&
               key !== ('USUARIOS_COMPARTIDOS') && key !== ('ESTADO_PLACA') && key !== ('CONCESIONES') && key !== ('TRAMITES') && key !== 'OBSERVACIONES') {

               //*creando la comulana de los datos 
               const td = document.createElement('td');

               if (key == 'SOLICITUD') {
                  td.innerHTML = `${fila['SOLICITUD']}`;
               } else {
                  //*para darle estilo a la placa si es mormal, sale,entra.
                  var trPlacas = document.createElement('tr');
                  var trPlacas1 = document.createElement('tr');
                  trPlacas.style = "text-align: center;"
                  trPlacas.className = "d-flex justify-content-center align-items-center";
                  trPlacas1.style = "text-align: center;"
                  trPlacas1.className = "d-flex justify-content-center align-items-center";

                  if (key == 'PLACA') {
                     var tdPlaca = document.createElement('td');
                     var tdConcesionTramites = document.createElement('td');
                     tdConcesionTramites.style = "text-align: center;"
                     tdConcesionTramites.className = " p-2";
                     tdPlaca.style.display = "flex";
                     tdConcesionTramites.style.display = "flex";

                     var tdFlecha = document.createElement('td');
                     tdFlecha.className = "p-0";

                     var tdConTra = document.createElement('td');

                     if (fila['PLACA_SALE'] == null && fila['PLACA_ENTRA'] == null && element != null) { // si concesion es !=1 y no existe placa entra y sale.
                        //*placas normales solo segun su color.
                        if (fila['ESTADO_PLACA'] == 'SALE') {
                           tdPlaca.innerHTML = `<span id="placaTabla" class="text-center borderPlacaSale">${fila['PLACA']}</span>`;
                        } else {
                           if (fila['ESTADO_PLACA'] == 'NORMAL') {
                              tdPlaca.innerHTML = `<span id="placaTabla" class="text-center borderPlaca">${fila['PLACA']}</span>`;
                           } else {
                              tdPlaca.innerHTML = `<span id="placaTabla" class="text-center borderPlacaEntra">${fila['PLACA']}</span>`;
                           }
                        }
                        tdConcesionTramites.style.gap = "8px";
                        tdConcesionTramites.innerHTML = `<span data-bs-toggle="tooltip" title="# De Concesiones" class="badge concesion">
                        <strong>${fila['CONCESIONES']}</strong></span><span class="mt-1"><strong>  /  </strong></span>
                        <span  data-bs-toggle="tooltip" title="# De Tramites" class="badge tramites"><strong>${fila['TRAMITES']}</strong></span>`;
                        trPlacas.appendChild(tdPlaca);
                        trPlacas.appendChild(tdConcesionTramites);

                     } else {

                        if (fila['PLACA_ENTRA'] != fila['PLACA_ENTRA_EXP'] && fila['PLACA_ENTRA_EXP'] !== 'null') {
                           var tdPlacasExp = document.createElement('td');
                           tdPlacasExp.style.display = "flex";

                           var tdFlecha1 = document.createElement('td');
                           tdFlecha1.className = "p-0";
                           //* si las placas son frente a la misma placa entra y placa extra exp se ponen las 3 placas

                           tdPlaca.innerHTML = `<span id="placaTabla" class="text-center borderPlacaSale">${fila['PLACA_SALE']}</span>`;
                           tdFlecha.innerHTML = `<span class="mt-2 p-0">
                     <i class="fa-solid fa-arrow-right flex justify-content-center align-items-center p-0" aria-hidden="true"></i>
                     </span>`;

                           tdConcesionTramites.innerHTML = `<span id="placaTabla" class="text-center borderPlacaEntra">${fila['PLACA_ENTRA']}</span>`;
                           tdFlecha1.innerHTML = `<span class="mt-2 p-0">
                     <i class="fa-solid fa-arrow-right flex justify-content-center align-items-center p-0" aria-hidden="true"></i>
                     </span>`;
                           tdPlacasExp.innerHTML = `<span id="placaTabla" class="text-center borderPlaca">${fila['PLACA_ENTRA_EXP']}</span>  </br>`;

                           tdConTra.style.gap = "8px";
                           tdConTra.innerHTML = `<span data-bs-toggle="tooltip" title="# De Concesiones" class="badge concesion">
                        <strong>${fila['CONCESIONES']}</strong></span><span class="mt-1"><strong>  /  </strong></span>
                        <span  data-bs-toggle="tooltip" title="# De Tramites" class="badge tramites"><strong>${fila['TRAMITES']}</strong></span>`;
                        } else {
                           //*solo se ponen la placa que entra y la placa que sale.
                           tdPlaca.innerHTML = `<span id="placaTabla" class="text-center borderPlacaSale">${fila['PLACA_SALE']}</span>`;
                           tdFlecha.innerHTML = `<span class="mt-2 p-0">
                     <i class="fa-solid fa-arrow-right flex justify-content-center align-items-center p-0" aria-hidden="true"></i>
                     </span>`;
                           tdConcesionTramites.innerHTML = `<span id="placaTabla" class="text-center borderPlacaEntra">${fila['PLACA_ENTRA']}</span> 
                     </br>`;
                           tdConTra.style.gap = "8px";
                           tdConTra.innerHTML = `<span data-bs-toggle="tooltip" title="# De Concesiones" class="badge concesion">
                        <strong>${fila['CONCESIONES']}</strong></span><span class="mt-1"><strong>  /  </strong></span>
                        <span  data-bs-toggle="tooltip" title="# De Tramites" class="badge tramites"><strong>${fila['TRAMITES']}</strong></span>`;
                        }
                        //*null.
                        if (fila['PLACA_SALE'] != null) {
                           trPlacas.appendChild(tdPlaca);
                           trPlacas.appendChild(tdFlecha);
                           trPlacas.appendChild(tdConcesionTramites);
                        }
                        if (fila['PLACA_ENTRA'] != fila['PLACA_ENTRA_EXP'] && fila['PLACA_ENTRA_EXP'] != null) {
                           trPlacas.appendChild(tdFlecha1);
                           trPlacas.appendChild(tdPlacasExp);
                        }
                        if (fila['CONCESIONES'] != 0 && fila['TRAMITES'] != 0) {
                           trPlacas1.appendChild(tdConTra);
                        }

                     }
                     td.appendChild(trPlacas);
                     //fila extra para cambio de unidad 
                     if (tdConTra != '') {
                        td.appendChild(trPlacas1);
                     }

                  } else {
                     if (key === 'FECHA') {//6 sin concesion si no 7
                        const soloFecha = fila['FECHA'].split(" ")[0];
                        td.textContent = soloFecha;
                     } else {
                        if (key == 'RA') { //9 si no 10 
                           // console.log(fila[9], 'fila[9]');
                           td.innerHTML = `<span data-bs-toggle="tooltip" class="badge badge-no">NO</span>`;
                           actualizarBadge(fila['RA'], td); // Inicialmente muestra "NO"
                           setTimeout(() => {
                              actualizarBadge(fila['RA'], td); // Después de un tiempo, muestra "SI"
                           }, 2000);
                        } else {
                           // if (key == 'ESTADO') {
                           //    if (element == ('CANCELADO' || 'INADMISIÓN')) {
                           //       //*colocar la observacion cuando es cancelacion y inadmision.
                           //       let observaciones = fila['OBSERVACIONES'];
                           //       td.innerHTML = `<span data-bs-toggle="tooltip" title="${observaciones}">${element}</span>`;
                           //    } else {
                           //       td.innerHTML = `<span>${element}</span>`;
                           //    }
                           // } else {
                           td.textContent = (!element || element === 'null') ? '' : element.toString().toUpperCase();
                           // }
                        }
                     }
                  }
               }


               //*asignando el valor del segundo elemento el onclick 
               if (key === 'SOLICITUD') {
                  //* Cambia el cursor para indicar que es clickeable
                  td.style.cursor = 'pointer';
                  td.style.color = '#033b4b';
                  td.style.textShadow = '3px 3px 5px rgba(5, 5, 5, 0.3)';
                  //*evento onclick a la columna que me redirecciona.
                  td.onclick = () => {
                     //* redireccionando y eviando RAM
                     if (esConsultas != true) {
                        window.location.href = `${$appcfg_Dominio}ram.php?RAM=${fila['SOLICITUD']}`;
                     } else {
                        //*link para cunado es solo consulta y no se puede modificar nada
                        window.location.href = `${$appcfg_Dominio}ram.php?RAM=${fila['SOLICITUD']}&Consulta=true`;
                     }
                  };
               }
               //*enviando columna a las filas
               row.appendChild(td);
            }
         }
      }
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
         //* Crea la fila de acciones
         const row_accion = document.createElement('tr');
         // row_accion.className = "fondoAccion";
         row_accion.style.textAlign = 'center';

         const td_dollar = document.createElement('td');

         //*creando el icono de cancelar
         td_dollar.innerHTML = ` <div style="display: flex; align-items: center; gap: 5px;"> 
                  ${activo === '2' //pagado
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

            //*creando el icono de cancelar  row.id=`'fila-${index}'`;
            // console.log(fila[0], 'fila[0] inadmitir');
            td_cancelar.innerHTML = `
            <span onclick="fUpdateEstado('IDE-3','${fila['SOLICITUD']}','CANCELADO','fila-${index}')" 
            class="accion-item " data-bs-toggle="tooltip" title="Boton para Cancelar solicitud" style="display: flex; flex-direction: column; align-items: center; cursor: pointer;">
               <i class="fas fa-window-close cancelar" style="font-size: 35px;"></i>
               <span class="cancelar" style="font-size: 12px;">CANCELAR</span>
            </span>
            `;
            //*creando el icono de inadmitido
            var td_prohibido = document.createElement('td');
            td_prohibido.innerHTML = `
            <span class="inadmitido" onclick="fUpdateEstado('IDE-4','${fila['SOLICITUD']}','INADMITIDO','fila-${index}')" 
            class="accion-item inadmitido" data-bs-toggle="tooltip" title="Boton para Inadmitir solicitud" style="display: flex; flex-direction: column; align-items: center; cursor: pointer;">
               <i class="fas fa-ban inadmitido" style="font-size: 35px;"></i>
               <span class="inadmitido" style="font-size: 12px;">INADMITIDO</span>
            </span>
            `;
         }
         //*creando el icono de compartido 
         const td_compartido = document.createElement('td');
         td_compartido.className = 'center'; // Centra los íconos en la celda principal
         if (fila['USUARIO_ACEPTA'] && fila['USUARIOS_COMPARTIDOS'] != null) {
            td_compartido.innerHTML = `
            <span class="compartiendo text-center"  
         class="accion-item compartiendo"  data-bs-toggle="tooltip" class="accion-item compartiendo"  data-bs-toggle="tooltip" title="${(fila['USUARIOS_COMPARTIDOS'] && fila['USUARIOS_COMPARTIDOS'] !== usuario) //sin concesion 12, si no 13
                  ? 'PERTENECE A: ' + fila['USUARIO_ACEPTA'].toUpperCase() +  // sin concesion 8 sino 9
                  ' COMPARTIDO A: ' + fila['USUARIOS_COMPARTIDOS'] // sin concesion 9 10
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
      console.log(descripcion, 'descripcion');
      //*Para que los iconos solo esten el en estado de PROCESO
      if (descripcion == 'EN VENTANILLA' || descripcion == 'FINALIZADO' || descripcion == 'CANCELADO' || descripcion == 'INADMISION' || descripcion == '*TODOS' || descripcion == 'RETROTRAIDO POR ERROR DE USUARIO') {
         const td_accion = document.createElement('td');
         td_accion.className = 'fondoAccion';
         td_accion.style.textAlign = 'center';  // Centrado horizontal.

         const row_accion = document.createElement('tr');
         row_accion.style.textAlign = 'center';  // Centrado horizontal.

         const td_dollar = document.createElement('td');

         //*creando el icono de cancelar
         td_dollar.innerHTML = ` <div style="display: flex; align-items: center; gap: 5px;"> 
               ${activo === '2'
               ? `<span class="text-center">
                           <a href="${url}" target="_blank"  style="display: flex; flex-direction: column; align-items: center; cursor: pointer; text-decoration:none">
                              <i class="fas fa-dollar-sign dolar-span-activo tooltip-verde  text-center" data-bs-toggle="tooltip" title="${folioPago}" style="font-size: 23px;">
                              </i>
                              <span class="dolar-span-activo-text" style="font-size:12px;"></span>
                           <a/>   
                        </span>`
               : `<span style="display: flex; flex-direction: column; align-items: center; cursor: pointer;">
               <a href="${url}" target="_blank"  style="display: flex; flex-direction: column; align-items: center; cursor: pointer; text-decoration:none">
                           <i class="fas fa-dollar-sign dolar-span text-center"  data-bs-toggle="tooltip" title="${folioPago}" style="font-size: 23px;">
                           </i>
                           <span class="dolar-span-text " style="font-size: 12px;"></span>
                        </span>
                        <a/> 
                        `
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

         if (fila['USUARIO_ACEPTA'] && fila['USUARIOS_COMPARTIDOS'] != null) { // sin concesion 9,'USUARIO_ACEPTA' si no 10,12
            td_compartido.innerHTML = `
            <span class="compartiendo text-center"  
            class="accion-item compartiendo"  data-bs-toggle="tooltip" class="accion-item compartiendo"  
            data-bs-toggle="tooltip" title="${(fila['USUARIOS_COMPARTIDOS'] && fila['USUARIOS_COMPARTIDOS'] !== usuario) // sin concesion 12 si no 'USUARIOS_COMPARTIDOS',
                  ? (descripcion == 'EN VENTANILLA') ? 'PERTENECE A: ' + fila['USUARIO_CREACION'].toUpperCase() +  // sin concesion 8 sino 9
                     ' COMPARTIDO A: ' + fila['USUARIOS_COMPARTIDOS'] // sin concesion 9

                     : 'PERTENECE A: ' + fila['USUARIO_CREACION'].toUpperCase() + ', ' + fila['USUARIO_ACEPTA'].toUpperCase() + // sin concesion 8 sino 9
                     ' COMPARTIDO A: ' + fila['USUARIOS_COMPARTIDOS'] : fila['USUARIOS_COMPARTIDOS']}"
             style="display: flex; flex-direction: column; align-items: center; cursor: pointer;">
            <i class="fas fa-share-alt compartiendo" style="font-size: 24px;"></i>
            <span class="compartiendo" style="font-size: 12px;">COMPARTIDO</span>
            </span> `;

         } else {
            td_compartido.innerHTML = `
         <span class="noCompartido accion-item noCompartido" data-bs-toggle="tooltip" title=""  
         style="display: flex; flex-direction: column; align-items: center; cursor: pointer;">
            <i class="fas fa-share-alt noCompartido" style="font-size: 24px;"></i>
            <span class="noCompartido" style="font-size: 12px;">NO COMPARTIDO</span>
         </span>
      `;
         }

         var td_cancelar = '';

         if (descripcion == 'EN VENTANILLA' && esConsultas != true) {
            td_cancelar = document.createElement('td');
            // td_cancelar.style.fontSize = '30px'; // Cambia el tamaño del ícono

            //*creando el icono de cancelar
            td_cancelar.innerHTML = `
            <span onclick="fUpdateEstado('IDE-3','${fila['SOLICITUD']}','CANCELADO','fila-${index}')" 
            class="accion-item " data-bs-toggle="tooltip" title="Boton para Cancelar solicitud" style="display: flex; flex-direction: column; align-items: center; cursor: pointer;">
               <i class="fas fa-window-close cancelar" style="font-size: 35px;"></i>
               <span class="cancelar" style="font-size: 12px;">CANCELAR</span>
            </span>`;
         }

         row_accion.appendChild(td_dollar);
         row_accion.appendChild(td_compartido);

         if (descripcion == 'EN VENTANILLA' && esConsultas != true) {
            row_accion.appendChild(td_cancelar);
         }

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
//**********************************************************************************************/
//*INICIO: funcion encargada de cambiar los estado de la solicitud por cancelado o inadmitido
//************************************************************************************************/

//* Variables globales para el modal y selectores, para evitar duplicaciones
var modalDescripcion = document.getElementById('modalDescripcion');
var myModal = new bootstrap.Modal(modalDescripcion, {
   backdrop: true,
   keyboard: true
});
var select = document.getElementById('selectD');
var descripcionOtros = document.getElementById('descripcionOtros');
var btnGuardar = document.getElementById('guardarDescripcion');
var btnCerrar = document.getElementById('idCerrar');
var iconoCerrar = document.getElementById('cerrar');
var tituloModal = document.getElementById('modalDescripcionLabel');

var selectedValue = '';

function limpiarModal() {
   select.innerHTML = '';
   descripcionOtros.innerHTML = '';
   selectedValue = '';
   // Eliminar cualquier backdrop que haya quedado
   document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
   document.body.classList.remove('modal-open');
}

var ramEstado = '';
function fUpdateEstado(estado, ram, title, numeroFila) {

   //* Mostrar el modal
   myModal.show();

   tituloModal.innerHTML = `RAZÓN DEL CAMBIO DE ESTADO A <strong>${title}</strong>`;

   //* Limpiar antes de llenar
   limpiarModal();

   //* Llenar el select
   //!fCancelarInadmitido en el archivo funcionesTablaDinamica.js
   fCancelarInadmitido().then((datosDescripcion) => {
      console.log(datosDescripcion, 'description');
      select.innerHTML = `<option selected disabled> SELECCIONE UNA OPCIÓN</option>`;

      datosDescripcion.datos.forEach((element, index) => {
         const aplica = title === 'CANCELADO' ? element['aplicaCancelacion'] === "1" : element['aplicaInadmicion'] === "1";

         if (aplica) {
            const option = document.createElement('option');
            option.value = element.descripcion;
            option.innerHTML = element.descripcion;
            option.id = `option-${index}`;
            option.className = 'text-uppercase';
            select.appendChild(option);
         }
      });

      //* Agregar opción "OTRO"
      const optionOtro = document.createElement('option');
      optionOtro.value = 'OTRO';
      optionOtro.textContent = 'OTRO';
      optionOtro.id = 'option-otro';
      optionOtro.className = 'text-uppercase';
      select.appendChild(optionOtro);
   });

   //* Event listener para guardar — definido una vez
   btnGuardar.addEventListener('click', function () {
      let descripcion = selectedValue;

      if (descripcion === 'OTRO') {
         const textarea = document.getElementById('datoSelecOtros');
         descripcion = textarea ? textarea.value : '';
      }

      if (!descripcion || descripcion.trim() === '') {
         Swal.fire({
            title: '¡ERROR!',
            html: `LA DESCRIPCIÓN PARA CAMBIAR EL ESTADO A <strong>${title}</strong> NO PUEDE ESTAR VACÍA.`,
            icon: 'error',
            confirmButtonText: 'OK',
         });
         return;
      }
      Swal.fire({
         title: 'CONFIRMACIÓN',
         html: `¿ESTÁ SEGURO DE CAMBIAR EL ESTADO DE LA SOLICITUD <strong>${ram}</strong> A el estado <strong>${title}</strong> con la razon de<strong>${descripcion}</strong>?`,
         icon: 'info',
         showCancelButton: true,
         confirmButtonText: 'SÍ',
         cancelButtonText: 'CANCELAR',
      }).then((result) => {
         if (result.isConfirmed) {
            //? la funcion "fUpdateEstadoFetch" se encuentra en el archivo funcionesTablaDinamica.js
            fUpdateEstadoFetch(estado, ram, descripcion, numeroFila);
            limpiarModal();
            cargarBotones();
            myModal.hide();
         }
      });
   });
}

//* Event listener para select — definido solo una vez
document.getElementById('selectD').addEventListener('change', function () {
   selectedValue = this.value;

   if (selectedValue === 'OTRO') {
      descripcionOtros.innerHTML = `
         <label for="descripcion" class="form-label text-primary-emphasis">INGRESE DESCRIPCIÓN:</label>
         <textarea class="form-control" id="datoSelecOtros" rows="3" placeholder="DESCRIPCIÓN" style="text-transform: uppercase;"></textarea>
      `;
   } else {
      descripcionOtros.innerHTML = '';
   }
});

//*Event listeners para cerrar — definidos una sola vez
[btnCerrar, iconoCerrar].forEach(el => {
   el.addEventListener('click', () => {
      limpiarModal();
      myModal.hide();
   });
});

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
   //* Crear el contenedor de la paginación
   const paginationContainer = document.createElement('nav');
   paginationContainer.setAttribute('aria-label', 'Page navigation example');

   //* Contenedor <ul> para los botones
   const pagination = document.createElement('ul');
   pagination.className = 'pagination justify-content-start'; // ← alineado a la izquierda

   //* Convertir a número y calcular total de páginas
   const total = parseInt(totalRows);
   const totalPages = Math.ceil(total / rowsPerPage);

   //* Verificar si se necesita paginación
   if (totalPages <= 1) {
      return document.createElement('div');
   }

   //* Botón "Anterior"
   const prevItem = document.createElement('li');
   prevItem.className = `page - item ${currentPage === 1 ? 'disabled' : ''} `;
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

   //* Páginas visibles (máximo 5 botones)
   const maxVisiblePages = 10;
   let startPage = Math.max(1, currentPage - 2);
   let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

   //* Ajustar si estamos al final
   if ((endPage - startPage) < (maxVisiblePages - 1)) {
      startPage = Math.max(1, endPage - maxVisiblePages + 1);
   }

   for (let page = startPage; page <= endPage; page++) {
      const pageItem = document.createElement('li');
      pageItem.className = `page - item ${currentPage === page ? 'active' : ''} `;
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

   //* Botón "Siguiente"
   const nextItem = document.createElement('li');
   nextItem.className = `page - item ${currentPage === totalPages ? 'disabled' : ''} `;
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

      var detalle = {};
      if (esConsultas == true) {
         detalle = {
            "BLANCO": "",
            "FSL / RAM": "FSL / RAM",
            "SOLICITUD / EXPEDIENTE": "SOLICITUD / EXPEDIENTE",
            "NOMBRE_SOLICITUD": "NOMBRE_SOLICITUD",
            "RTN_SOLICITUD": "RTN_SOLICITUD",
            "CONCESION": "CONCESION",
            "PLACA / PLACA_REPLAQUEO": "PLACA / PLACA REPLAQUEO",
            "USUARIO": "USUARIO",
         };
      } else {
         detalle = {
            "BLANCO": "",
            "FSL / RAM": "FSL / RAM",
            "SOLICITUD / EXPEDIENTE": "SOLICITUD / EXPEDIENTE",
            "NOMBRE_SOLICITUD": "NOMBRE_SOLICITUD",
            "RTN_SOLICITUD": "RTN_SOLICITUD",
            "CONCESION": "CONCESION",
            "PLACA / PLACA_REPLAQUEO": "PLACA / PLACAREPLAQUEO",
         };
      }

      for (const key in detalle) {
         if (Object.prototype.hasOwnProperty.call(detalle, key)) {
            const element = detalle[key];
            const option = document.createElement('option');
            option.value = key.toUpperCase();
            option.textContent = element.toUpperCase();

            if (element === selectElement.value) {
               option.selected = true;
            }

            document.getElementById('id_filtro_select').addEventListener('change', function () {

               if (document.getElementById('id_filtro_select').value != 'BLANCO') {
                  document.getElementById('id_input_filtro').style.display = 'inline';
               } else {
                  document.getElementById('id_input_filtro').style.display = 'none';
               }
            })
            selectElement.appendChild(option);
         }
      }

      if (infoEstado != null) {
         infoEstado.forEach((element, index) => {
            if (index == 0) {
               estadoInicial = element[0];
               descripcionInicial = element[1];
               agregar = element[2];
            }
         })

         ultimoagregado = agregar;
         actualizarEstado(estadoInicial, descripcionInicial, agregar);
         //* Llama a la función vista_data con los parámetros deseados

         vista_data(estadoInicial, descripcionInicial, agregar);
         activar(estadoInicial);
      }
   }, 1000);
};

