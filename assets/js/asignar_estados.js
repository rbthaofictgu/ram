


//*Fetch encargado de traer los datos de los usuarios
fetch(`${$appcfg_Dominio}/usuarios.php?`)
   .then(response => {
      if (!response.ok) {
         throw new Error('Error en la solicitud de usuario');
      }
      return response.json();
   })
   .then(dataEmpleado => {
      // console.log(dataEmpleado, 'usuarios'); // Aquí trabajas con los datos obtenidos
      autoCompletar(dataEmpleado);

   })
   .catch(error => {
      console.error('Error:', error);
   });

//*funcion encargada de llenar el input y el autocompletado para seleccionar.
function autoCompletar(dataEmpleado) {
   //*instanciando a los elementos del input y autocompletado
   const searchInput = document.getElementById("searchInput");
   const autocompleteList = document.getElementById("autocomplete-list");

   //*añadiendo al buscador el evento input
   searchInput.addEventListener("input", function () {
      const query = searchInput.value.toLowerCase();
      //* Limpiar las opciones previas
      autocompleteList.innerHTML = '';

      if (!query) {
         //*Si el campo está vacío, no mostrar nada
         return;
      }
      //*recorremos los datos
      dataEmpleado.forEach(emp => {
         //*vemos que el nombre o el usuario se encuente  dentro del objeto
         if (emp.nombre_empleado.toLowerCase().includes(query) || emp.usuario.toLowerCase().includes(query)) {
            //*creamos un elemento div
            const div = document.createElement("div");
            //*añadimos el nombre y el usuario
            div.textContent = emp.nombre_empleado + " (" + emp.usuario + ")";
            //*creamos el evento click al elemento.
            div.addEventListener("click", function () {
               //* Asignar el nombre al campo de texto
               searchInput.value = emp.nombre_empleado;
               searchInput.dataset.info = emp.usuario;
               //*Limpiar las opciones al seleccionar
               autocompleteList.innerHTML = '';
            });

            //*enviamos el div al contenedor
            autocompleteList.appendChild(div);
         }
      });
   });

   //* Cerrar la lista de autocompletado si el usuario hace clic fuera del input
   document.addEventListener("click", function (e) {
      if (e.target !== searchInput) {
         autocompleteList.innerHTML = '';
      }
   });
}

//*Fedtche encargado de traer los estados disponibles.
fetch(`${$appcfg_Dominio}/estados_sistema.php?`)
   .then(response => {
      if (!response.ok) {
         throw new Error('Error en la solicitud de los estados');
      }
      return response.json();
   })
   .then(dataEstados => {
      // console.log(dataEstados); // Aquí trabajas con los datos obtenidos
      tableState(dataEstados)
   })
   .catch(error => {
      console.error('Error:', error);
   });


//*funcion encargada de  mostrar los estados en la tabla.
function tableState(data) {
   let info = data.datos;
   let contenedor = document.getElementById('id_estados');
   contenedor.innerHTML += ` <div class="row estado_tabla text-center">
               <h5><strong>ESTADOS DEL SISTEMA</strong></h5>
            </div>`
   info.forEach((element, index) => {
      for (const key in element) {
         if (Object.prototype.hasOwnProperty.call(element, key)) {
            if (key == 'DESC_Estado') {
               const data = element[key];
               // console.log(data)
               contenedor.innerHTML += `<div class="row  border-bottom border-info">
               <div class="col-8 d-flex justify-content-between align-items-center">
                  <div>${index + 1} - ${data}</div>
                  <!-- Checkbox -->
                  <div class="form-check">
                     <input class="form-check-input" type="checkbox" value="${data}" id="check_estado">
                  </div>
               </div>
               </div>`;
            }
         }
      }
   });

}

function Limpiar() {
   document.getElementById("searchInput").value = '';
}

 function mostrar(value=data) {
      document.getElementById('id_estados').innerHTML = value
   }

//*funcion encargada de traer toda la data que sera insertada en la tabla.
function asignarEstados() {
   let usuario = document.getElementById('searchInput').value;
   let dataInfoValue = document.getElementById('searchInput').dataset.info;

   let checkboxes = document.querySelectorAll('.form-check-input');
   let selectedData = [];



   //*Recorre todos los checkboxes
   checkboxes.forEach(function (checkbox) {
      //*Si el checkbox está seleccionado
      if (checkbox.checked) {
         //*Obtén el valor del checkbox y el atributo data-info
         selectedData.push({
            estado: checkbox.value,
            //dataInfo: checkbox.dataset.info
         });
      }
   });

   let info = {
      "estado": selectedData,
      "empleado": usuario,
      "usuario": dataInfoValue,
   }

   for (const key in info) {
      if (Object.prototype.hasOwnProperty.call(info, key)) {
         const element = info[key];
         if (element != '') {
               sendData(info);

         } else {
             let data = document.getElementById('id_estados').innerHTML;

            document.getElementById('id_estados').innerHTML = '';
            document.getElementById('id_estados').innerHTML +=
               `<div class="alert alert-warning alert-dismissible fade show" role="alert">
                  <strong><i class="fa-solid fa-circle-exclamation"></i></strong> NECESITA TENER AL MENOS UN ESTADO SELECCIONADO Y UN USUARIO.
                  <button type="button" onclick="mostrar()" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
               </div>`

            // alert("necesita tener al menos un estado seleccionado y un usuario");
         }

      }
   }

   console.log(info);
   // console.log(usuario, dataInfoValue, selectedData);

}

     let info = {
      "estado": selectedData,
      "empleado": usuario,
      "usuario": dataInfoValue,
   }
function sendData(info){
   fetch(`${$appcfg_Dominio}/update_estado_user.php?`, {
     method: 'POST',
     headers: {
       'Content-Type': 'application/json',
       'Authorization': 'Bearer token',
     },
     body: JSON.stringify(info), 
   })
     .then(response => {
       if (!response.ok) {
         throw new Error('Error en la solicitud sendData');
       }
       return response.json();
     })
     .then(data => {
       console.log('Respuesta:', data);
     })
     .catch(error => {
       console.error('Error:', error);
     });
}
