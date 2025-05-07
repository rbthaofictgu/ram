


// fModulos_sistemas();
function fModulos_sistemas() {
   fetch(`${$appcfg_Dominio}/modulos_sistema.php?`, {
      method: 'GET',
      headers: {
         'Authorization': 'Bearer token',
      },
   })
      .then(response => {
         if (!response.ok) {
            throw new Error('Error en la solicitud para traer los modulos del sitema');
         }
         return response.json();
      })
      .then(data => {
         console.log('Respuesta:', data);
         fTabla(data);
      })
      .catch(error => {
         console.error('Error:', error);
      });
}

function fTabla(data) {
   const encabezado = [];
   let tabla = document.getElementById('id_modulos');

   // Inicia el contenido HTML de la tabla
   let contenido = `
   <div>
      <div class="row estado_tabla text-center">
         <h5 class="text-center"> <strong>TABLA DE MODULOS DEL SISTEMA </strong></h5>
      </div>
      
      <div>
`;

   // Recorre los elementos de la data para agregar las filas
   data.forEach((elemento, index) => {
      contenido += `
      <div id="idrow${index}" class="row  border-bottom border-info">`;

      for (const key in elemento) {
         if (Object.prototype.hasOwnProperty.call(elemento, key)) {
            encabezado.push(key);
         }
      }
      console.log(encabezado,'encabezado');
      for (const key in elemento) {
         if (Object.prototype.hasOwnProperty.call(elemento, key)) {
            const element = elemento[key];
            contenido += `<div class="col ">${element}</div>`;
         }
      }

      // Columna para el checkbox
      contenido += `
      <div class="col-2">
         <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="flexCheckDisabled${index}" disabled>
            <label class="form-check-label" for="flexCheckDisabled${index}"></label>
         </div>
      </div>
   </div>`;
   });

   // Cierra los elementos HTML de la tabla
   contenido += `</div></div>`;

   // Asigna el contenido generado al innerHTML de la tabla
   tabla.innerHTML = contenido;

}

function fElementHTML(elemento, atributos = [], id = "", clases = "", evento = null) {
   // Crear el elemento HTML
   let elementoCreado = document.createElement(elemento);

   // Asignar el ID y las clases
   elementoCreado.id = id;
   elementoCreado.className = clases;

   // Asignar los atributos si se proporcionan
   if (atributos && atributos.length > 0) {
      atributos.forEach(atr => {
         // Asegúrate de que 'atr' sea un objeto con 'atributo' y 'valor'
         if (atr.atributo && atr.valor) {
            elementoCreado.setAttribute(atr.atributo, atr.valor);
         }
      });
   }

   // Si hay un evento, asignarlo al elemento
   if (evento) {
      const { tipoEvento, handler } = evento;
      if (tipoEvento && handler) {
         elementoCreado.addEventListener(tipoEvento, handler);
      }
   }

   return elementoCreado;
}


//  const fila = fElementHTML(
//          'div',
//          //[{ atributo: 'data-id', valor: '123' }],
//          `idrowModulo${index}`,
//          'row,',
//          // { tipoEvento: 'click', handler: () => alert('Div clickeado!') }
//       );

