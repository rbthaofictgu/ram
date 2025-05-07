
document.addEventListener('DOMContentLoaded', function () {
   //** Variables **//
   const contenedorTabla = document.getElementById('contenedorTabla');
   const buscador = document.getElementById('busqueda');
   const paginacion = document.getElementById('paginacion');
   //*inicializacion de variables de paginacion*//
   let paginaActual = 1;
   const limite = 10;

   //********************************************************/
   //* INICIO: Renderiza la tabla con los datos obtenidos *//
   //********************************************************/
   function cargarDatos(pagina = 1, busqueda = '') {
      fetch(`${$appcfg_Dominio}query_estadoAsignaTablePerforma.php?pagina=${pagina}&busqueda=${encodeURIComponent(busqueda)}`)
         .then(res => res.json())
         .then(response => {
            //* Verifica si la respuesta contiene un error *//
            if (response.error) {
               contenedorTabla.innerHTML = `<div class="alert alert-danger">${response.error}</div>`;
               return;
            }
            //* Verifica si la respuesta contiene un mensaje de error *//
            var datos = response.data;
            var total = response.total;
            paginaActual = response.pagina;
            // Verifica si hay datos disponibles
            if (!datos || datos.length === 0) {
               contenedorTabla.innerHTML = `
               <div class="alert alert-warning text-center">
                  NO HAY DATOS DISPONIBLES O NO SE ENCONTRARÓN RESULTADOS PARA LA BÚSQUEDA.
               </div>`;
               paginacion.innerHTML = ''; // También puedes limpiar la paginación
               return;
            }
            //* Renderiza la tabla y la paginación
            renderizarTabla(datos, paginaActual, limite);
            //* Renderiza la paginación
            renderizarPaginacion(total, limite, paginaActual, busqueda);
         })
         .catch(error => {
            contenedorTabla.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
         });
   }
   //********************************************************/
   //* FINAL: Renderiza la tabla con los datos obtenidos *//
   //********************************************************/

   //***********************************************************/
   //* INICIO: Renderiza la tabla con los datos obtenidos *//
   //***********************************************************/
   function renderizarTabla(data, paginaActual = 1, registrosPorPagina = 10) {
      //* Limpiar el contenedor de la tabla antes de agregar nuevos datos *//
      const table = document.createElement('table');
      table.className = 'table table-striped table-hover mb-5 table-responsive table-sm';
      //* Agregar la clase 'table-responsive' para hacer la tabla responsiva *//
      const thead = document.createElement('thead');
      const trHead = document.createElement('tr');
      //** Agregar encabezados de la tabla **//
      const thCheck = document.createElement('th');
      thCheck.textContent = '#';
      thCheck.style.minWidth = '60px';
      thCheck.style.textAlign = 'center';
      trHead.appendChild(thCheck);
      //* Renderizar encabezados de la tabla *//
      if (data.length > 0) {
         Object.keys(data[0]).forEach(key => {
            const th = document.createElement('th');
            th.textContent = key.replace(/_/g, ' ');
            th.style.paddingTop = '4px';
            th.style.paddingBottom = '4px';
            th.style.fontSize = '15px';
            th.style.lineHeight = '1.2';
            th.style.verticalAlign = 'middle';
            th.style.textAlign = 'center';
            th.classList.add('text-start');
            trHead.appendChild(th);
         });
      }
      //** Agregar encabezados de la tabla **//
      thead.classList.add('table-primary', 'headTable', 'm-0', 'p-0');
      thead.appendChild(trHead);
      table.appendChild(thead);
      //** Agregar encabezados de la tabla **//
      const tbody = document.createElement('tbody');
      //* recorriendo los datos para crear las filas de la tabla *//
      data.forEach((item, index) => {
         //** Crear una fila para cada elemento **//
         const tr = document.createElement('tr');
         //** Agregar la clase 'table-row' para cada fila **//
         const tdCheck = document.createElement('td');
         tdCheck.style.verticalAlign = 'middle';
         tdCheck.style.textAlign = 'center';
         tdCheck.style.padding = '0';
         tdCheck.style.minWidth = '60px';
         //** Crear un contenedor para el checkbox y el número de fila **//
         const divCheck = document.createElement('div');
         divCheck.style.display = 'flex';
         divCheck.style.flexDirection = 'row';
         divCheck.style.alignItems = 'center';
         divCheck.style.justifyContent = 'center';
         divCheck.style.height = '100%';
         divCheck.style.gap = '8px';
         divCheck.style.padding = '8px';
         //** Crear un número de fila **//
         const rowNumber = document.createElement('span');
         rowNumber.textContent = ((paginaActual - 1) * registrosPorPagina) + index + 1;
         //** Crear un checkbox **//
         const checkbox = document.createElement('input');
         checkbox.type = 'checkbox';
         checkbox.classList.add('form-check-input');
         checkbox.value = item.RAM;
         //** Agregar un evento al checkbox **//
         checkbox.addEventListener('click', function () {
            const select = document.getElementById('usuarioSelect');
            const select_estado = document.getElementById('estadosSelect');
            const valorSelect = select.value;
            const valorEstado = select_estado.value;
            const textoSelect = select.options[select.selectedIndex]?.text.toUpperCase() || 'NO SELECCIONADO';
            //** Verificar si el checkbox está seleccionado **//
            if (!valorSelect  && !valorEstado) {
               Swal.fire('Atención', 'DEBE SELECCIONAR UN USUARIO Y UN ESTADO ANTES DE COMPARTIR.', 'warning');
               checkbox.checked = false;
               return;
            }
            //* verifica si el usuario seleccionado es igual al usuario asignado *//
            if (textoSelect === (item.USER_ASIGNADO).toUpperCase()) {
               Swal.fire('Atención', 'LOS USUARIOS NO PUEDEN SER IGUALES', 'warning');
               checkbox.checked = false;
               return;
            }
            //** Verifica si el checkbox está seleccionado **//
            const fila = checkbox.closest('tr');
            const usuariosCompartidos = fila.querySelector('td[data-key="USUARIOS_COMPARTIDOS"]')?.textContent.toUpperCase() || '';
            const yaCompartido = usuariosCompartidos.includes(textoSelect);
            //** Cambia el estado del checkbox **//
            const nuevoEstado = yaCompartido ? '0' : '1';
            //** Verifica si el checkbox está seleccionado **//
            if (nuevoEstado === '1') {
               //** Si el checkbox está seleccionado, muestra un mensaje de confirmación **//
               Swal.fire({
                  title: 'COMPARTIR RAM',
                  html: `¿ESTÁ SEGURO QUE QUIERE COMPARTIR LA RAM <strong>${checkbox.value}</strong> CON EL USUARIO <strong>${textoSelect}</strong>?`,
                  icon: 'question',
                  showCancelButton: true,
                  confirmButtonText: 'Sí, compartir',
                  cancelButtonText: 'Cancelar'
               }).then((result) => {
                  //** Si el usuario confirma, llama a la función fCompartir **//
                  if (result.isConfirmed) {
                     const fila = checkbox.closest('tr');
                     //** Verifica si el checkbox está seleccionado **//
                     const usuariosCompartidos = fila.querySelector('td[data-key="USUARIOS_COMPARTIDOS"]')?.textContent.toUpperCase() || '';
                     //** Verifica si el usuario ya está compartido **//
                     const yaCompartido = usuariosCompartidos.includes(textoSelect);
                     //** Cambia el estado del checkbox **//
                     const nuevoEstado = yaCompartido ? '0' : '1';
                     //** Crea un objeto con los datos a enviar **//
                     const datos = {
                        ID_Formulario_Solicitud: checkbox.value,
                        Usuario_Comparte: textoSelect,
                        Estado: nuevoEstado,
                        Estado_Formulario: valorEstado,
                     };
                     console.log(datos, 'datos enviados......');
                     //** Llama a la función fCompartir **//
                     fCompartir(datos, checkbox);
                  } else {
                     //** Si el usuario cancela, deselecciona el checkbox **//
                     checkbox.checked = false;
                  }
               });

            } else {
               //** Si el checkbox no está seleccionado, muestra un mensaje de confirmación **//
               Swal.fire({
                  title: 'NO COMPARTIR RAM',
                  html: `¿ESTÁ SEGURO QUE QUIERE DEJAR DE COMPARTIR LA RAM <strong>${checkbox.value}</strong> CON EL USUARIO <strong>${textoSelect}</strong>?`,
                  icon: 'question',
                  showCancelButton: true,
                  confirmButtonText: 'Sí,dejar de compartir',
                  cancelButtonText: 'Cancelar'
               }).then((result) => {
                  //** Si el usuario confirma, llama a la función fCompartir **//
                  if (result.isConfirmed) {
                     const fila = checkbox.closest('tr');
                     const usuariosCompartidos = fila.querySelector('td[data-key="USUARIOS_COMPARTIDOS"]')?.textContent.toUpperCase() || '';
                   
                     const yaCompartido = usuariosCompartidos.includes(textoSelect);
                     const nuevoEstado = yaCompartido ? '0' : '1';
                     const datos = {
                        ID_Formulario_Solicitud: checkbox.value,
                        Usuario_Comparte: textoSelect,
                        Estado: nuevoEstado,
                        Estado_Formulario: valorEstado,
                     };

                     fCompartir(datos, checkbox);
                  } else {
                     checkbox.checked = false;
                  }
               });

            }


         });

         divCheck.appendChild(rowNumber);
         divCheck.appendChild(checkbox);
         tdCheck.appendChild(divCheck);
         tr.appendChild(tdCheck);

         Object.entries(item).forEach(([key, value]) => {
            const td = document.createElement('td');
            td.classList.add('text-start');
            td.textContent = value !== null ? value : '';
            td.setAttribute('data-key', key);
            td.style.textAlign = 'center';
            tr.appendChild(td);
         });

         tbody.appendChild(tr);
      });

      table.appendChild(tbody);
      contenedorTabla.innerHTML = '';
      contenedorTabla.appendChild(table);
   }

   function renderizarPaginacion(total, porPagina, paginaActual, busqueda) {
      const totalPaginas = Math.ceil(total / porPagina);
      paginacion.innerHTML = '';
      if (totalPaginas <= 1) return;

      const nav = document.createElement('nav');
      nav.setAttribute('aria-label', 'Paginación');
      const ul = document.createElement('ul');
      ul.className = 'pagination justify-content-start flex-wrap';

      const maxBotones = 5;
      let inicio = Math.max(1, paginaActual - Math.floor(maxBotones / 2));
      let fin = Math.min(totalPaginas, inicio + maxBotones - 1);
      if (fin - inicio < maxBotones - 1) {
         inicio = Math.max(1, fin - maxBotones + 1);
      }

      const crearBoton = (text, page, disabled = false, active = false) => {
         const li = document.createElement('li');
         li.className = 'page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : '');
         const a = document.createElement('a');
         a.className = 'page-link';
         a.href = '#';
         a.textContent = text;
         a.addEventListener('click', e => {
            e.preventDefault();
            if (!disabled && paginaActual !== page) {
               cargarDatos(page, busqueda);
            }
         });
         li.appendChild(a);
         return li;
      };

      ul.appendChild(crearBoton('Anterior', paginaActual - 1, paginaActual === 1));
      for (let i = inicio; i <= fin; i++) {
         ul.appendChild(crearBoton(i, i, false, i === paginaActual));
      }
      ul.appendChild(crearBoton('Siguiente', paginaActual + 1, paginaActual === totalPaginas));

      nav.appendChild(ul);
      paginacion.appendChild(nav);
   }

   function fCompartir(datos, checkbox) {
      fetch(`${$appcfg_Dominio}queryAlmacenarCompartir.php`, {
         method: 'POST',
         headers: {
            'Content-Type': 'application/json'
         },
         body: JSON.stringify(datos)
      })
         .then(response => response.json())
         .then(result => {
            if (result.success) {

               if (datos.Estado === '1') {
                  Swal.fire({
                     title: 'RAM COMPARTIDA',
                     html: `LA RAM <strong>${datos.ID_Formulario_Solicitud}</strong> SE HA COMPARTIDO CON EL USUARIO <strong>${datos.Usuario_Comparte}</strong>`,
                     icon: 'success',
                     confirmButtonText: 'OK',
                  });
               } else {
                  Swal.fire({
                     title: 'RAM NO COMPARTIDA',
                     html: `LA RAM <strong>${datos.ID_Formulario_Solicitud}</strong> YA NO ESTA COMPARTIDA CON EL USUARIO <strong>${datos.Usuario_Comparte}</strong>`,
                     icon: 'success',
                     confirmButtonText: 'OK',
                  });
               }


               checkbox.checked = false;
               const termino = buscador.value.trim();
               cargarDatos(paginaActual, termino);

            } else {
               Swal.fire({
                  title: '!ERROR¡',
                  html: `NO SE PUDO COMPARTIR LA RAM <strong>${datos.ID_Formulario_Solicitud}</strong>`,
                  icon: 'warning',
                  confirmButtonText: 'OK',
               });
               checkbox.checked = false;
            }
         })
         .catch(error => {
            console.error('Error al enviar solicitud:', error);
            checkbox.checked = false;
         });
   }

   buscador.addEventListener('input', () => {
      const termino = buscador.value.trim();
      cargarDatos(1, termino);
   });

   cargarDatos();
});
