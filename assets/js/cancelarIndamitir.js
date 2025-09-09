document.addEventListener('DOMContentLoaded', cargarDatos);

//****************************************************************************/
//******INICIO:VARIABLES DE CONFIGURACION LINK PARA LA DIRECCIONES************** */
//****************************************************************************/
const url1 = `${$appcfg_Dominio}cancelarInadmitir.php?`;
const url2 = `${$appcfg_Dominio}insertCancelarInadmitirTb.php?`;

//****************************************************************************/
//******INICIO: SE ENCARGA DE LLENAR LA INFORMACION DE LA TABLA************** */
//****************************************************************************/
let datosOriginales = [];
let currentPage = 1;
const rowsPerPage = 5;

async function cargarDatos(result) {
   const res = await fetch(url1);
   const data = await res.json();

   if (data.success === true || data.success === 'true') {
      if (data.message === 'Actualizado') {
         sendToast(
            $appcfg_icono_de_success + "INFORMACIÓN ACTUALIZADA CORRECTAMENTE",
            $appcfg_milisegundos_toast,
            "",
            true,
            true,
            "top",
            $appcfg_pocision_toast,
            true,
            $appcfg_style_toast,
            function () { },
            "error",
            $appcfg_offset_toast,
            $appcfg_icono_toast
         );

         datosOriginales = data['datos'] || [];
         mostrarPagina(currentPage);
         renderizarPaginacion();
      } else {
         datosOriginales = data['datos'] || [];
         mostrarPagina(currentPage);
         renderizarPaginacion();
      }
   } else {
      console.log(result, 'DATOS')
      Swal.fire({
         title: '!ERROR¡',
         text: 'SE PRESENTO UN ERROR INTENTE DE NUEVO O PONGASE EN CONTACTO CON EL ADMINISTRADOR',
         icon: 'error',
         confirmButtonText: 'OK',
      })
   }
}
//****************************************************************************/
//******FINAL: SE ENCARGA DE LLENAR LA INFORMACION DE LA TABLA************** */
//****************************************************************************/

function mostrarPagina(pagina) {
   currentPage = pagina;
   const tbody = document.querySelector('#tablaRazones tbody');
   tbody.innerHTML = '';

   const inicio = (pagina - 1) * rowsPerPage;
   const fin = inicio + rowsPerPage;
   const datosPagina = datosOriginales.slice(inicio, fin);

   //  <td class="text-start">${r.otro_espeficique || '-------'}</td>
   datosPagina.forEach(r => {
      const fila = document.createElement('tr');
      fila.innerHTML = `
         <td>${r.id}</td>
         <td class="text-start">${r.descripcion}</td>
         <td><span class="badge ${r.otro_espeficique === '1' ? 'badge-si' : 'badge-no'}">${r.otro_espeficique === '1' ? 'Sí' : 'NO'}</span></td>
         <td><span class="badge ${r.aplicaCancelacion === '1' ? 'badge-si' : 'badge-no'}">${r.aplicaCancelacion === '1' ? 'Sí' : 'NO'}</span></td>
         <td><span class="badge ${r.aplicaInadmicion === '1' ? 'badge-si' : 'badge-no'}">${r.aplicaInadmicion === '1' ? 'Sí' : 'NO'}</span></td>
         <td><span class="badge ${r.estaActivo === '1' ? 'badge-si' : 'badge-no'}">${r.estaActivo === '1' ? 'Sí' : 'NO'}</span></td>
         <td><button class="btn btn-info btn-sm" onclick='editar(${JSON.stringify(r)})'>Editar</button></td>
      `;
      tbody.appendChild(fila);
   });
}

function renderizarPaginacion() {
   const paginacionContainer = document.getElementById('paginacion');
   paginacionContainer.innerHTML = '';
   const total = datosOriginales.length;
   const paginacion = createPagination(total);
   paginacionContainer.appendChild(paginacion);
}

function createPagination(totalRows) {
   const paginationContainer = document.createElement('nav');
   paginationContainer.setAttribute('aria-label', 'Page navigation');

   const pagination = document.createElement('ul');
   pagination.className = 'pagination justify-content-start';

   const totalPages = Math.ceil(totalRows / rowsPerPage);

   if (totalPages <= 1) return document.createElement('div');

   // Botón anterior
   const prevItem = document.createElement('li');
   prevItem.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
   const prevLink = document.createElement('a');
   prevLink.className = 'page-link';
   prevLink.href = '#';
   prevLink.textContent = 'Anterior';
   prevLink.onclick = (e) => {
      e.preventDefault();
      if (currentPage > 1) {
         mostrarPagina(currentPage - 1);
         renderizarPaginacion();
      }
   };
   prevItem.appendChild(prevLink);
   pagination.appendChild(prevItem);

   // Máximo 5 botones visibles
   const maxVisiblePages = 5;
   let startPage = Math.max(1, currentPage - 2);
   let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

   if (endPage - startPage < maxVisiblePages - 1) {
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
         mostrarPagina(page);
         renderizarPaginacion();
      };
      pageItem.appendChild(pageLink);
      pagination.appendChild(pageItem);
   }

   // Botón siguiente
   const nextItem = document.createElement('li');
   nextItem.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
   const nextLink = document.createElement('a');
   nextLink.className = 'page-link';
   nextLink.href = '#';
   nextLink.textContent = 'Siguiente';
   nextLink.onclick = (e) => {
      e.preventDefault();
      if (currentPage < totalPages) {
         mostrarPagina(currentPage + 1);
         renderizarPaginacion();
      }
   };
   nextItem.appendChild(nextLink);
   pagination.appendChild(nextItem);

   paginationContainer.appendChild(pagination);
   return paginationContainer;
}

//****************************************************************************/
//******INICIO SE ENCARGA DE INSERTAR O EDITAR LA INFORMACION DE LATABLA***** */
//****************************************************************************/
document.getElementById('formRazon').addEventListener('submit', async function (e) {
   e.preventDefault();

   const descripcion = document.getElementById('descripcion').value.trim();
   // const otro_espeficique = document.getElementById('otro_espeficique').value.trim();

   // Validaciones
   if (descripcion === '') {
      Swal.fire('Advertencia', 'El campo "Descripción" es obligatorio.', 'warning');
      return;
   }

   //* Construcción del objeto
   const data = {
      id: document.getElementById('id').value || null,
      descripcion,
      otro_espeficique: document.getElementById('otro_espeficique').checked ? 1 : 0,
      aplicaCancelacion: document.getElementById('aplicaCancelacion').checked ? 1 : 0,
      aplicaInadmicion: document.getElementById('aplicaInadmicion').checked ? 1 : 0,
      estaActivo: document.getElementById('estaActivo').checked ? 1 : 0
   };

   //* Envío datos al fetch
   const res = await fetch(url2, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
   });

   const result = await res.json();
   console.log(result.message, 'resultados');

   if (result.success) {
      sendToast(
         $appcfg_icono_de_success + "LA INFORMACION SE GUARDO CORRECTAMENTE",
         $appcfg_milisegundos_toast,
         "",
         true,
         true,
         "top",
         $appcfg_pocision_toast,
         true,
         $appcfg_style_toast,
         function () { },
         "error",
         $appcfg_offset_toast,
         $appcfg_icono_toast
      );

      resetForm();
      cargarDatos(result.message);
   } else {
      if (result.message === 'Ya existe un registro con la misma descripción y otro especificado') {
         Swal.fire({
            title: '!ERROR¡',
            text: 'YA EXISTE UN REGISTRO CON LA MISMA DESCRIPCIÓN Y OTRO ESPECIFICADO',
            icon: 'warning',
            confirmButtonText: 'OK',
         });
         return;
      } else {
         Swal.fire({
            title: '!ERROR¡',
            text: 'SE PRESENTO UN ERROR INTENTE DE NUEVO O PONGASE EN CONTACTO CON EL ADMINISTRADOR',
            icon: 'error',
            confirmButtonText: 'OK',
         });
      }
   }
});
//****************************************************************************/
//******FINAL SE ENCARGA DE INSERTAR O EDITAR LA INFORMACION DE LATABLA***** */
//****************************************************************************/

//****************************************************************************/
//******INICIO: SE ENCARGA DE EDITAR LA INFORMACION DE LA TABLA************** */
//****************************************************************************/
function editar(datos) {
   document.getElementById('id').value = datos.id;
   document.getElementById('descripcion').value = datos.descripcion;
   // document.getElementById('otro_espeficique').value = datos.otro_espeficique;
   if (datos.otro_espeficique === '1' || datos.otro_espeficique === 1) {
      document.getElementById('otro_espeficique').checked = true;
   }
   if (datos.aplicaCancelacion === '1' || datos.aplicaCancelacion === 1) {
      document.getElementById('aplicaCancelacion').checked = true;
   }
   if (datos.aplicaInadmicion === '1' || datos.aplicaInadmicion === 1) {
      document.getElementById('aplicaInadmicion').checked = true;
   }
   if (datos.estaActivo === '1' || datos.estaActivo === 1) {
      document.getElementById('estaActivo').checked = true;
   }

}
//****************************************************************************/
//******FINAL: SE ENCARGA DE EDITAR LA INFORMACION DE LA TABLA************** */|
//****************************************************************************/

//****************************************************************************/
//****** INICIO:SE ENCARGA DE RESETEAR EL FORMULARIO************** */
//****************************************************************************/
function resetForm() {
   document.getElementById('formRazon').reset();
   document.getElementById('id').value = '';
}
//****************************************************************************/
//****** FINAL:SE ENCARGA DE RESETEAR EL FORMULARIO************** */
//****************************************************************************/
