const idInput = document.getElementById('input-prefetch');
const input = document.querySelector('.input-container input');
const icon = document.getElementById('toggle-icon');
const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))


icon.addEventListener('click', () => {
   input.classList.toggle('expanded');
   console.log(concesionForAutoComplete);
   // concesionForAutoComplete.forEach(item => {
   //    console.log(item.value, item.text);
   // });
   if (idInput != '') {
      idInput.select();
   }
});

//*******************************************************************************************************/
//* Ejecuta AutoComplet sobre concesionForAutoComplete para buscar Concesiones
//*******************************************************************************************************/
function fAutoComplete() {
   console.log(concesionForAutoComplete);
   // alert('AutoComplete');
   $("#input-prefetch").autocomplete({
      //*Se recorre data.
      source: concesionForAutoComplete.map((item) => item.text),
      minLength: 2,
      select: function (event, ui) {
         //*campos de entrada de data
         const selectedItem = concesionForAutoComplete.find(item => item.text === ui.item.label);
         $("#input-prefetch").val(ui.item.label);  // Mostrar el texto seleccionado
         console.log('Texto seleccionado: ' + ui.item.label);  // Texto seleccionado
         console.log('Valor asociado: ' + selectedItem.value); // Valor asociado  
         verificar_existencia_dato(selectedItem.value, event);
      }
   });


}

//*verifica primero la existencia de los datos para saber si se edita o se ingresa la concesión
function verificar_existencia_dato(concesion, event) {

   //*Debuelve true si existe

   if (concesion.length < 7) {
      console.log(concesion.length, 'dentro');
      Swal.fire({
         title: `!NO EXISTE LA" ${concesion.toUpperCase()}"¡`,
         text: 'LA CONCESIÓN INGRESADA NO EXISTE',
         icon: 'warning',
         confirmButtonText: 'CANCELAR',
      })
   } else {
      let existe = buscar_placa_concesion(concesion);
      if (existe) {
         // console.log('se llama para editar' + concesion);
         if (currentstep != 2) {
            stepperForm.to(3);
         }
         // *Si existe enviamos la concesion y llamamos la funcion de editar
         fEditarConcesion(concesion);
      } else {
         if (concesion.length == '') {
            Swal.fire({
               title: `!NO HAY ELEMENTO¡`,
               text: 'INGRESE UNA CONCESION PARA BUSCAR',
               icon: 'warning',
               confirmButtonText: 'OK',
            })
         } else {
            Swal.fire({
               title: `!LA CONCESION " ${concesion.toUpperCase()}." NO ESTA INGRESADA EN ESTA SOLICITUD¡`,
               text: '¿DESEA INGRESAR LA CONCESIÓN A LA SOLICITUD?',
               icon: 'warning',
               showCancelButton: true,
               confirmButtonText: 'SÍ',
               cancelButtonText: 'CANCELAR'
            }).then((result) => {
               //* si confirma que esta seguro de eliminar llamamos la funcion para que elimine de la base de datos.
               if (result.isConfirmed) {
                  if (currentstep != 2) {
                     stepperForm.to(3);
                  }
                  //*si no existe se pregunta si desea ingresar lo si es asi se llama la funcion para ingresar una concesión
                  f_FetchCallConcesion(concesion, event, 'input-prefetch');
               }
            });
         }


      }
   }

}

//*Realiza la busqueda para ver si ya existe.
function buscar_placa_concesion(concesion) {
   //* Verifica si el valor proporcionado es una concesión o una placa
   return concesionNumber.some(item => item.Concesion === concesion);
}


//*********************************************************************************************************************/
//** Inicio Cuando Se Da Click a Element de AutoComplete de Concesiones
//*********************************************************************************************************************/
const inputPrefetch = document.getElementById("input-prefetch");
inputPrefetch.addEventListener("keydown", function (event) {
   if (event.key === "Enter") {
      const selectedItem = concesionForAutoComplete.find(item => item.text === event.target.value);
      if (selectedItem != undefined) {
         verificar_existencia_dato(selectedItem.value, event);
      } else {
         if (event.target.value.length > 9) {
            verificar_existencia_dato(event.target.value.toUpperCase(), event);
         } else {
           
            Swal.fire({
               title:`!${event.target.value.toUpperCase()}¡`,
               text: 'NO SE PUEDE AGREGAR CONCESION CON LONGITUD MENOR A 10 CARACTERES ',
               icon: 'warning',
               confirmButtonText: 'OK',
            });
         }
      }
   }
});

inputPrefetch.addEventListener("focus", function () {
   inputPrefetch.select(); // Selecciona el texto dentro del input cuando se hace focus
});