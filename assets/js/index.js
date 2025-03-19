//*186.2.137.13
"use strict";

var arrayOriginalRows = Array();
var concesionBorradoEnMalla = 0;
var isFromfGetInputs = false;
var isError = false;
var isRecordGetted = Array();
var isTab = false;
var isPrevious = false;
var hayerror = false;
var idinput = "";
var idinputs = Array();
var currentstep = 0;
var stepperForm;
var isDirty = Array();
var setFocus = true;
var paneerror = Array(Array());
var testinputs;
var testinputsArray;
var showModalFromShown = true;
var claseDeServicio = "";
var esCertificado = true;
var esCambioDePlaca = false;
var esCambioDeVehiculo = false;
//* 0=No Necesita Recuperarse                             1=Se agrego tramite de Cambio de Placa o Unidad
//* 2=Se ejecuto la función de recuperacion del vehiculo  3=Vehiculo Recuperado Satisfactoriamente
var seRecuperoVehiculoDesdeIP = 0;
var isVehiculeBlock = false;
var finalizarSalvado = false;
var checked = false;
var concesionIndex = Array();
var concesionNumber = Array();
var concesionForAutoComplete = Array();
var currentConcesionIndex = -1;
var TOTAL_TRAMITES_X_CONCESION = Array();
var Concesion_Global = "";
var dataConcesion = Array();
var esCarga;
var modalidadDeEntrada = "I";
var chkTramites;
var fVieneFuncionEditarConcesion = false;
var requiereCambioDePlaca = false;


function reLoadScreen() {
  window.location.href = window.location.origin + window.location.pathname;
}

function loading(isLoading,currentstep) {
  console.log('isLoading => ' + isLoading);
  if (isLoading) {
    console.log('isLoading True => ' + isLoading);
      //*****************************************************************************************/
      //* INICIO: Oculta la información del stepper content y presenta el gif de procesando    */
      //*****************************************************************************************/
      document.getElementById("id_stepper_gif").style = "display:flex";
      document.getElementById("id_img_stepper_gif").style = "display:flex";
      document.getElementById("id_stepper_content").style = "display:none";
      if (currentstep == 2) {
        document.getElementById("concesion_tramites").style = "display:none;";
      } 
      //*********************************************************************************************/
      //* FINAL: Oculta la información del stepper content y presenta el gif de procesando          */
      //*********************************************************************************************/
  } else {
      console.log('isLoading False => ' + isLoading);
      //*********************************************************************************************/
      //* INICIO: Despliega la información del stepper content y oculta el gif de procesando        */
      //*********************************************************************************************/
      document.getElementById("id_stepper_gif").style = "display:none";
      document.getElementById("id_img_stepper_gif").style = "display:none";
      document.getElementById("id_stepper_content").style = "display:block";
      //*********************************************************************************************/
      //* FINAL: Despliega la información del stepper content y oculta el gif de procesando        */
      //*********************************************************************************************/
  }
}

function showHideTramite(el) {
  document.getElementById(el.id + "T").classList.toggle("showtramites");
}
//*********************************************************************************************************/
//* INICIO: Agregando un tramite a Concesion Number
//*********************************************************************************************************/
function addConcesionNumber(ID, ID_CHECK, Monto, Descripcion, ID_Tramite) {
  for (let i = concesionNumber.length - 1; i >= 0; i--) {
    if (
      concesionNumber[i].Concesion ==
      document.getElementById("concesion_concesion").innerHTML
    ) {
      let Cantidad_Vencimientos = 1;
      let Fecha_Expiracion = "";
      let Fecha_Expiracion_Nueva = "";

      if (ID_CHECK === "IHTTTRA-02_CLATRA-01_R_PE") {
        Cantidad_Vencimientos = document.getElementById(
          "CantidadRenovacionesPerExp"
        ).value;
        Fecha_Expiracion_Nueva = document.getElementById(
          "NuevaFechaVencimientoPerExp"
        ).value;
        Fecha_Expiracion = document.getElementById(
          "NuevaFechaVencimientoPerExp"
        ).value;
      } else {
        if (
          ID_CHECK === "IHTTTRA-02_CLATRA-02_R_CO" ||
          ID_CHECK === "IHTTTRA-02_CLATRA-02_R_PS"
        ) {
          Cantidad_Vencimientos = document.getElementById(
            "CantidadRenovacionesConcesion"
          ).value;
          Fecha_Expiracion_Nueva = document.getElementById(
            "NuevaFechaVencimientoConcesion"
          ).value;
          Fecha_Expiracion = document.getElementById(
            "FechaVencimientoConcesion"
          ).value;
        }
      }

      concesionNumber[i].Tramites.push({
        ID: ID,
        ID_Compuesto: ID_CHECK,
        Codigo: ID_Tramite,
        descripcion: Descripcion,
        ID_Tramite: ID_Tramite,
        Monto: parseFloat(Monto).toFixed(2),
        Total_A_Pagar: parseFloat(
          parseFloat(Monto).toFixed(2) * Cantidad_Vencimientos
        ).toFixed(2),
        Cantidad_Vencimientos: Cantidad_Vencimientos,
        Fecha_Expiracion: Fecha_Expiracion,
        Fecha_Expiracion_Nueva: Fecha_Expiracion_Nueva,
        ID_Categoria: document.getElementById("ID_Categoria").value,
        ID_Tipo_Servicio: document.getElementById("ID_Tipo_Servicio").value,
        ID_Modalidad: document.getElementById("ID_Modalidad").value,
        ID_Clase_Servico: document.getElementById("ID_Clase_Servicio").value,
      });
    }
  }
}
//*********************************************************************************************************/
//* FINAL: Agregando un tramite a Concesion Number
//*********************************************************************************************************/

//*********************************************************************************************************/
//* Inicio: Actualizando arreglo de Tramites dentro de concesionNumber
//*********************************************************************************************************/
function updateConcesionNumber(idTramite, idCheckBox, idConcesion) {
  for (let i = concesionNumber.length - 1; i >= 0; i--) {
    if (concesionNumber[i].Concesion == idConcesion) {
      concesionNumber[i].Tramites = concesionNumber[i].Tramites.filter(
        (tramite) => tramite.ID !== idTramite
      );
      if (idCheckBox.split("_")[3] == "CU") {
        concesionNumber[i].Unidad1 = null;
      }
    }
  }
}
//*********************************************************************************************************/
//* Final: Actualizando arreglo de Tramites dentro de concesionNumber
//*********************************************************************************************************/

//**********************************************************************************************************/
//* INICIO: Funcion para Obtener el Attribute de un elemento
//**********************************************************************************************************/
function getAttribute(Element, Attribute, DeleteAttribute = false) {
  let Attr = "";
  //**********************************************************************************************************/
  //* Validando si el elemento tiene el Attribute a obtener
  //**********************************************************************************************************/
  if (Element.hasAttribute(Attribute) == true) {
    //**********************************************************************************************************/
    //* Obteniendo el Attribute de un elemento
    //**********************************************************************************************************/
    Attr = Element.getAttribute(Attribute);
    if (DeleteAttribute == true) {
      //**********************************************************************************************************/
      //* Borrando el Attribute de un elemento
      //**********************************************************************************************************/
      Element.removeAttribute(Attribute);
    }
  }
  return Attr;
}
//**********************************************************************************************************/
//* FINAL: Funcion para Obtener el Attribute de un elemento
//**********************************************************************************************************/

//**********************************************************************************************************/
//* INICIO: Funcion para Establecer el Attribute de un elemento
//**********************************************************************************************************/
function setAttribute(Element, Attribute, Value) {
  //**********************************************************************************************************/
  //* Asigando el Attribute a un elemento
  //**********************************************************************************************************/
  Element.setAttribute(Attribute, Value);
}
//**********************************************************************************************************/
//* FINAL: Funcion para Establecer el Attribute de un elemento
//**********************************************************************************************************/

//**********************************************************************************************************/
//* INICIO: Funcion para Eliminar/Remover el Attribute de un elemento
//**********************************************************************************************************/
function removeAttribute(Element, Attribute) {
  //**********************************************************************************************************/
  //* Eliminar/Remover el Attribute del elemento
  //**********************************************************************************************************/
  Element.removeAttribute(Attribute);
}
//**********************************************************************************************************/
//* FINAL: Funcion para Eliminar/Remover el Attribute de un elemento
//**********************************************************************************************************/

function fShowConcesiones() {
  concesionBorradoEnMalla = 0
  const myModal = new bootstrap.Modal(document.getElementById("exampleModal"));
  myModal.show();
  mostrarData(concesionNumber, "tabla-container", "CONCESIONES SALVADAS");
}

function updateCollection(Elemento) {
  // console.log(Elemento, 'Elemento updateCollection');
  if (concesionIndex.indexOf(Elemento) === -1) {
    concesionIndex.push(Elemento);
  }
  return concesionIndex.indexOf(Elemento);
}

function clearCollections(Elementos) {
  var seBorraronConcesiones = false;
  var Index = -1;
  Elementos.forEach(function (Elemento) {
    Index = concesionIndex.indexOf(Elemento);
    if (Index != -1) {
      //********************************************************************************/
      //* INICIO: Llamando Funcion para Borrar elementos al arreglo de autocomplete
      //********************************************************************************/
      deleteElementFromAutoComplete(concesionIndex[Index]);
      //********************************************************************************/
      //* FINAL: Llamando Funcion para Borrar elementos al arreglo de autocomplete
      //********************************************************************************/
      concesionIndex[Index] = false;
      concesionNumber[Index] = false;
      seBorraronConcesiones = true;
    } else {
      seBorraronConcesiones = false;
    }
  });
  return seBorraronConcesiones;
}

//********************************************************************************/
//* Inicio Agregar elementos al arreglo de autocomplete
//********************************************************************************/
function addElementToAutoComplete(value, text) {
  let element = { value: value, text: text };
  concesionForAutoComplete.push(element);
}

//********************************************************************************/
//* Inicio Filtrar los elementos que no son iguales a 'Concesion'
//********************************************************************************/
function deleteElementFromAutoComplete(Referencia) {
  //*item !== Concesion
  concesionForAutoComplete = concesionForAutoComplete.filter(
    (Concesion) => !Concesion.text.includes(Referencia)
  );
}

function reduceConcesionNumber(idConcesiones) {
  var total = parseFloat((0.0).toFixed(2));
  var total_concesiones = 0;
  var total_tramites = 0;
  idConcesiones.forEach((idConcesion) => {
    var index = updateCollection(idConcesion);
    var Tramites = concesionNumber?.[index]?.Tramites;
    //* Si no existe o no tiene el array Tramites, se omite
    if (Tramites && Array.isArray(Tramites)) {
      total_concesiones += 1;
      //* Sumamos los Total_A_Pagar de todos los trámites de la concesión actual
      const sumaTramites = Tramites.reduce((acum, Tramite) => {
        total_tramites += 1;
        return parseFloat(acum) + parseFloat(Tramite.Total_A_Pagar);
      }, 0);
      total += sumaTramites;
    }
  });
  return {
    total: total,
    total_concesiones: total_concesiones,
    total_tramites: total_tramites,
  };
}

function resencuenciarConcesionNumberTramites(index, originalrow, labelLine) {
  console.log(index, 'index en resencuenciarConcesionNumberTramites');
  //* Resecuenciando los tramites
  if (concesionNumber?.[index]?.Tramites && Array.isArray(concesionNumber[index].Tramites)) {
    console.log(concesionNumber[index].Tramites, 'concesionNumber[index].Tramites');
    var longitud_arreglo_tramites = concesionNumber[index].Tramites.length;
    //* Resecuenciación
    var contador_tramites = 0;
    for (let j = 0; j < longitud_arreglo_tramites + 1; j++) {
      console.log((parseInt(index)), 'index');
      console.log(originalrow, 'originalrow');
      if (index == originalrow) {
        console.log('iguales');
        console.log(index, 'index');
        console.log(originalrow, 'originalrow');
        console.log(labelLine);
        console.log(concesionBorradoEnMalla);
      } else {
        console.log('diferentes');
        console.log(index, 'index');
        console.log(originalrow, 'originalrow');
      }
      console.log("indice_row_tramite_" + (((index) == originalrow) ? index : originalrow) + "_" + j, '"indice_row_tramite_"');
      var rowt = document.getElementById("indice_row_tramite_" + (((index - 1) == originalrow) ? index : originalrow) + "_" + j);
      console.log(j, 'jjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjj');
      if (rowt) {
        console.log(rowt, 'rowt inside resencuenciar');
        if (parseInt(index) == parseInt(originalrow)) {
          if (concesionBorradoEnMalla == 0) {
            rowt.innerHTML = parseInt(parseInt(index) + 1) + "." + String(parseInt(parseInt(contador_tramites) + 1));
          } else {
            rowt.innerHTML = parseInt(index) + "." + String(parseInt(parseInt(contador_tramites) + 1));
          }
          console.log(concesionBorradoEnMalla == 1, 'concesionBorradoEnMalla == 1');
          console.log(parseInt(index) == parseInt(originalrow));
        } else {
          console.log(concesionBorradoEnMalla != 1, 'concesionBorradoEnMalla != 1');
          console.log(contador_tramites, 'contador_tramites');
          rowt.innerHTML = labelLine + "." + String(parseInt(parseInt(contador_tramites) + 1));
        }
        contador_tramites++;
      }
    }
  }
}

function resencuenciarConcesionNumberTramitesTodos(cantidad) {
  console.log(cantidad, 'index en resencuenciarConcesionNumberTramites Todos');
  //* Resecuenciando los tramites
  if (concesionNumber && Array.isArray(concesionNumber)) {
    var longitud_arreglo = concesionNumber.length;
    //* Resecuenciación
    var contador = 0;
    var contador_nuevo = 0
    for (var i = 0; i < longitud_arreglo; i++) {
      if (concesionNumber?.[i]?.Tramites && Array.isArray(concesionNumber[i].Tramites)) {
        var longitud_arreglo_tramites = concesionNumber[i].Tramites.length;
        //* Resecuenciación
        var contador_tramites = 0;
        console.log(contador, 'contador afuera');
        for (var j = 0; j < longitud_arreglo_tramites; j++) {
          console.log(j, 'j adentro');
          console.log(contador, 'contador adentro');
          console.log(contador_nuevo, 'contador_nuevo');
          console.log("indice_row_tramite_" + (contador) + "_" + j);
          console.log(contador_tramites, 'contador_tramites');
          var rowt = document.getElementById("indice_row_tramite_" + String(contador) + "_" + String(j));
          if (rowt && rowt.id != "indice_row_tramite_" + String(contador_nuevo) + "_" + String(j)) {
            rowt.innerHTML = (parseInt(contador_nuevo) + 1) + "." + String(parseInt(contador_tramites) + 1);
            contador_tramites++;
          } else {
            if (rowt) {
              console.log(rowt.id, 'rowt.id break');
            }
            break;
          }
        }
        contador_nuevo++
      }
      contador++;
    }
  }
}

function getDataOriginalRowForResencuenciarConcesionNumber(rows) {
  var max = rows.length;
  for (i = 0; i < max; i++) {
    arrayOriginalRows.push(document.getElementById('id_trash_idRow' + rows[i]).getAttribute('data-originalrow'));
  }
}

function resencuenciarConcesionNumber(cantidad) {
  //* Resecuenciando los tramites
  if (concesionNumber && Array.isArray(concesionNumber)) {
    var longitud_arreglo = concesionNumber.length;
    //* Resecuenciación
    var contador = 0;
    for (let j = 0; j < longitud_arreglo + cantidad; j++) {
      var rowt = document.getElementById("indice_row_" + j);
      if (rowt) {
        rowt.innerHTML = String(parseInt(contador) + 1);
        contador++;
      }
    }
    concesionBorradoEnMalla += cantidad;
    resencuenciarConcesionNumberTramitesTodos(cantidad);
  }
}

// var multas3ra = document.getElementById("btnmultas");
// if (multas3ra != null) {
//   multas3ra.addEventListener("click", function (event) {
//     showConcesion();
//   });
// }

// var consultas3ra = document.getElementById("btnconsultas");
// if (consultas3ra != null) {
//   consultas3ra.addEventListener("click", function (event) {
//     showConcesion();
//   });
// }

// var perexp3ra = document.getElementById("btnperexp");
// if (perexp3ra != null) {
//   perexp3ra.addEventListener("click", function (event) {
//     showConcesion();
//   });
// }

// var concesion3ra = document.getElementById("btnconcesion");
// if (concesion3ra != null) {
//   concesion3ra.addEventListener("click", function (event) {
//     showConcesion();
//   });
// }

//************************************************************/
//* Moviendose al siguiente input
//************************************************************/
function moveToNextInput(currentInput, value) {
  //var inputs = [].slice.call(document.querySelectorAll('input select'));
  var currentIndex = testinputsArray.indexOf(currentInput);
  var wasMoved = false;
  for (var i = currentIndex + 1 + value; i < testinputsArray.length; i++) {
    if (
      !testinputsArray[i].disabled &&
      !testinputsArray[i].visible &&
      testinputsArray[i].getAttribute("readonly") == null
    ) {
      testinputsArray[i].focus();
      if (testinputsArray[i].type != "select-one") {
        testinputsArray[i].select();
      }
      wasMoved = true;
      break;
    }
  }
}

//**********************************************************************/
//* INICIO: Obtener todas las entradas enabled para validarlas
//**********************************************************************/
function fGetInputs() {
  isTab = true;
  isFromfGetInputs = true;
  // Get the element by its ID
  var element = document.getElementById("test-form-" + (currentstep + 1));
  // Get all input elements inside this element
  var inputs = element.querySelectorAll(".test-controls");
  // Convert NodeList to Array for easier manipulation (optional)
  inputs = Array.from(inputs);
  // Iterando entre los inputs para despachar el evento change y validar la data de cada input
  var index = 0;
  inputs.forEach((input) => {
    if (input.disabled == false) {
      // Creando un nuevo evento 'change'
      var event = new Event("change", {
        bubbles: true,
        cancelable: true,
      });

      input.dispatchEvent(event);
      if (setFocus == true) {
        input.focus();
        input.select();
      }
    }

    index++;
  });
  fGetInputsSelect();
}
//**********************************************************************/
//* FINAL: Obtener todas las entradas enabled para validarlas
//**********************************************************************/

//**********************************************************************/
//* INICIO: Obtener todos los select enabled para validarlas
//**********************************************************************/
function fGetInputsSelect() {
  // Get the element by its ID
  var element = document.getElementById("test-form-" + (currentstep + 1));
  // Get all input elements inside this element
  var inputs = element.querySelectorAll(".test-select");
  // Convert NodeList to Array for easier manipulation (optional)
  inputs = Array.from(inputs);
  // Iterando entre los inputs para despachar el evento change y validar la data de cada input
  var index = 0;
  inputs.forEach((input) => {
    if (input.disabled == false) {
      let parts = input.id.split("_");
      if (
        (parts[0].slice(-1) == 1 && esCambioDeVehiculo == true) ||
        parts[0].slice(-1) != 1
      ) {
        if (input.getAttribute("data-valor") > input.value) {
          input.classList.add("text-error");
          var label = document.getElementById(input.id + "label");
          if (label != null) {
            label.classList.add("errorlabel");
          }
          paneerror[currentstep][idinputs.indexOf(input.id)] = 1;
        } else {
          input.classList.remove("text-error");
          var label = document.getElementById(input.id + "label");
          if (label != null) {
            label.classList.remove("errorlabel");
          }
          paneerror[currentstep][idinputs.indexOf(input.id)] = 0;
          //Moverse al siguiente input
          moveToNextInput(input, 0);
        }
      }
    }
    index++;
  });
  isTab = false;
}
//**********************************************************************/
//* FINAL: Obtener todos los select enabled para validarlas
//**********************************************************************/

//**************************************************************************************/
//* Eliminar todos los mensajes de error
//**************************************************************************************/
function fCleanErrorMsg() {
  //console.log('On fCleanErrorMsg()');
  // Obtener el elemento por su ID
  var element = document.getElementById("test-form-" + (currentstep + 1));
  // Obtener todos los elementos de entrada dentro de este elemento
  var inputs = element.querySelectorAll(".test-controls");
  // Convertir NodeList a Array para facilitar la manipulación (opcional)
  inputs = Array.from(inputs);
  // Iterar sobre los elementos de entrada y eliminar las clases de error
  inputs.forEach((input) => {
    input.classList.remove("text-error");
    var label = document.getElementById(input.id + "label");
    if (label != null) {
      label.classList.remove("errorlabel");
    }
    var labelerror = document.getElementById(input.id + "labelerror");
    if (labelerror != null) {
      labelerror.style.visibility = "hidden";
    }
  });
  //Limpiando mensajes de elementos .test-select
  fCleanSelectErrorMsg();
}

function fCleanSelectErrorMsg() {
  //console.log('On fCleanSelectErrorMsg()');
  // Obtener el elemento por su ID
  var element = document.getElementById("test-form-" + (currentstep + 1));
  // Obtener todos los elementos de entrada dentro de este elemento
  var inputs = element.querySelectorAll(".test-select");
  // Convertir NodeList a Array para facilitar la manipulación (opcional)
  inputs = Array.from(inputs);
  // Iterar sobre los elementos de entrada y eliminar las clases de error
  inputs.forEach((inputx) => {
    inputx.classList.remove("text-error");
    let id = inputx.id.concat("label");
    var label = document.getElementById(id);
    if (label != null) {
      label.classList.remove("errorlabel");
    }
  });
}

function animateValue(
  element,
  start,
  end,
  duration,
  clase = "highlight",
  parse = parseFloat,
  round = 2
) {
  //* Agrega la clase para aplicar el efecto visual
  element.classList.add(clase);
  let startTime = null;
  function animation(currentTime) {
    if (!startTime) startTime = currentTime;
    const elapsed = currentTime - startTime;
    const progress = Math.min(elapsed / duration, 1); // Asegura que progress no supere 1
    start = parse(start);
    end = parse(end);
    const currentValue = parse(
      (start + (end - start) * progress).toFixed(round)
    );
    element.textContent = currentValue.toFixed(round);
    if (progress < 1) {
      requestAnimationFrame(animation);
    } else {
      //* Remueve la clase de resaltado cuando termina la animación numérica
      element.classList.remove(clase);
    }
  }
  requestAnimationFrame(animation);
}

function preDeleteAutoComplete(Concesiones, ElementType = "PLACAS") {
  console.log(ElementType, 'ElementType preDeleteAutoComplete')
  if (ElementType == "PLACAS") {
    currentConcesionIndex = updateCollection(Concesiones);
    console.log(currentConcesionIndex, 'currentConcesionIndex preDeleteAutoComplete')
    console.log(concesionNumber);
    deleteElementFromAutoComplete(
      concesionNumber[currentConcesionIndex].Unidad1["ID_Placa"]
        ? concesionNumber[currentConcesionIndex].Unidad1["ID_Placa"]
        : concesionNumber[currentConcesionIndex].Unidad1["Placa"]
    );
    deleteElementFromAutoComplete(
      concesionNumber[currentConcesionIndex].Unidad1["ID_Placa_Anterior"]
        ? concesionNumber[currentConcesionIndex].Unidad1["ID_Placa_Anterior"]
        : concesionNumber[currentConcesionIndex].Unidad1[
        "ID_Placa_Antes_Replaqueo"
        ]
    );
  } else {
    if (ElementType == "CONCESION") {
      Concesiones.forEach(function (Concesion) {
        currentConcesionIndex = updateCollection(Concesion);
        deleteElementFromAutoComplete(
          concesionNumber[currentConcesionIndex].Concesion
        );
        deleteElementFromAutoComplete(
          concesionNumber[currentConcesionIndex].Permiso_Explotacion
        );
        deleteElementFromAutoComplete(
          concesionNumber[currentConcesionIndex].Unidad["ID_Placa"]
            ? concesionNumber[currentConcesionIndex].Unidad["ID_Placa"]
            : concesionNumber[currentConcesionIndex].Unidad["Placa"]
        );
        deleteElementFromAutoComplete(
          concesionNumber[currentConcesionIndex].Unidad["ID_Placa_Anterior"]
            ? concesionNumber[currentConcesionIndex].Unidad["ID_Placa_Anterior"]
            : concesionNumber[currentConcesionIndex].Unidad[
            "ID_Placa_Antes_Replaqueo"
            ]
        );
        if (
          concesionNumber[currentConcesionIndex].Unidad1 != undefined &&
          concesionNumber[currentConcesionIndex].Unidad1 != "" &&
          concesionNumber[currentConcesionIndex].Unidad1 != null
        ) {
          deleteElementFromAutoComplete(
            concesionNumber[currentConcesionIndex].Unidad1["ID_Placa"]
              ? concesionNumber[currentConcesionIndex].Unidad1["ID_Placa"]
              : concesionNumber[currentConcesionIndex].Unidad1["Placa"]
          );
          deleteElementFromAutoComplete(
            concesionNumber[currentConcesionIndex].Unidad1["ID_Placa_Anterior"]
              ? concesionNumber[currentConcesionIndex].Unidad1[
              "ID_Placa_Anterior"
              ]
              : concesionNumber[currentConcesionIndex].Unidad1[
              "ID_Placa_Antes_Replaqueo"
              ]
          );
        }
      });
    }
  }
  fAutoComplete();
}
//***********************************************************************************************/
//* INICIO: ELIMINAR TRAMITES */
//***********************************************************************************************/
async function fEliminarTramite(
  idConcesion,
  idTramite,
  idRow,
  idRowConcesion,
  Monto,
  idCheckBox,
  ID_Unidad,
  ID_Unidad1,
  dataAgregarFila,
  idTramiteEliminar
) {
  try {
    // URL del Punto de Acceso a la API
    const url = $appcfg_Dominio + "Api_Ram.php";
    let fd = new FormData(document.forms.form1);
    var Tramites = "";
    // Adjuntando el action al FormData
    if (document.getElementById("ID_Expediente").value == "") {
      fd.append("action", "delete-tramite-preforma");
    } else {
      fd.append("action", "delete-tramite-expediente");
      // Enviar el número de Expediente
      fd.append(
        "ID_Expediente",
        document.getElementById("ID_Expediente").value
      );
      // Enviar el número de Solicitud
      fd.append("ID_Solicitud", document.getElementById("ID_Solicitud").value);
    }


    //* ID DEL TRAMITE
    fd.append("idTramite", JSON.stringify(idTramite));
    if (idCheckBox.split("_")[3] == "CU") {
      fd.append("ID_CHECK", JSON.stringify(idCheckBox));
      fd.append("ID_Unidad", JSON.stringify(ID_Unidad));
      fd.append("ID_Unidad1", JSON.stringify(ID_Unidad1));
    }
    const options = {
      method: "POST",
      body: fd,
    };

    const response = await fetchWithTimeout(url, options, 120000);
    const Datos = await response.json();

    if (typeof Datos.ERROR != "undefined") {
      sendToast(
        "INCONVENIENTES ELIMINANDO TRAMITE EN PREFORMA, INTENTELO NUEVAMENTE SI EL ERROR PERSISTE FAVOR CONTACTAR AL ADMINISTRADOR DEL SISTEMA",
        $appcfg_milisegundos_toast,
        "",
        true,
        true,
        "top",
        "right",
        true,
        $appcfg_background_toast,
        function () { },
        "error",
        $appcfg_pocision_toast,
        $appcfg_icono_toast
      );
      return false;
    } else {
      //*******************************************************************************************************/
      //*INICIO: LLAANDO FUNCION DE PREBORRADO DE concesionForAutoComplete
      //*******************************************************************************************************/
      const elemt = document.getElementById("idLengTramites");
      animateValue(
        elemt,
        parseInt(document.getElementById("idLengTramites").textContent),
        parseInt(
          parseInt(document.getElementById("idLengTramites").textContent) -
          parseInt(1)
        ),
        9000,
        "highlightGris",
        parseInt,
        0
      );
      console.log(idConcesion, 'idConcesion');
      preDeleteAutoComplete(idConcesion);
      //*******************************************************************************************************/
      //*INICIO: LLAMANDO FUNCION QUE ACTUALIZA EL ARREGLO DE TRAMITES, ELIMINANDO EL TRAMITE BORRADO EN LA DB
      //*******************************************************************************************************/
      console.log(idTramite, 'idTramite');
      console.log(idCheckBox, 'idCheckBox');
      updateConcesionNumber(idTramite, idCheckBox, idConcesion);
      //*******************************************************************************************************/
      //*FINAL: LLAMANDO FUNCION QUE ACTUALIZA EL ARREGLO DE TRAMITES, ELIMINANDO EL TRAMITE BORRADO EN LA DB
      //*******************************************************************************************************/
      //*******************************************************************************************************/
      //*INICIO: RESTANDO DEL TOTAL POR CONCESION Y TOTAL GENERAL EL MONTO DEL TRAMITE ELMINADO
      //*******************************************************************************************************/
      if (Monto != false) {
        const totalDisplay = document.getElementById("Total" + idRowConcesion);
        if (totalDisplay) {
          //* INICIO */
          const oldTotal = parseFloat(totalDisplay.textContent).toFixed(2);
          const amountToSubtract = parseFloat(Monto).toFixed(2);
          const newTotal = (oldTotal - amountToSubtract).toFixed(2);
          //* Animamos el cambio en 1 segundo (1000ms)
          animateValue(totalDisplay, oldTotal, newTotal, 2500);
          //* FINAL */
          let total_pagar_ele = document.getElementById("Total_A_Pagar");
          let Total_A_Pagar = parseFloat(
            document.getElementById("Total_A_Pagar").innerHTML
          ).toFixed(2);
          animateValue(
            total_pagar_ele,
            Total_A_Pagar,
            parseFloat(Total_A_Pagar - Monto).toFixed(2),
            4000
          );
        }
      }
      //*********************************************************************************************************************************/
      //* Rebajando 1 a la cantidad de tramites por concesion;
      //*********************************************************************************************************************************/
      TOTAL_TRAMITES_X_CONCESION[updateCollection(idConcesion)] = TOTAL_TRAMITES_X_CONCESION[updateCollection(idConcesion)] - 1;
      //*********************************************************************************************************************************/
      //*******************************************************************************************************/
      //*INICIO: ENVIO DE MENSAJE DE BORRADO DEL TRAMITE EXITOSO
      //*******************************************************************************************************/
      sendToast(
        "TRAMITE PRE-FORMA ELIMINADO EXITOSAMENTE",
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
      if (idRow != null && Monto != false) {
        const row = document.getElementById(idRow);
        if (row) {
          // console.log(idRowConcesion, 'idRowConcesion');
          // console.log('id_trash_idRow' + String(idRowConcesion - 1), 'id_trash_idRow+String(idRowConcesion-1)');
          // var checkID = document.getElementById('id_trash_idRow' + String(idRowConcesion - 1));
          // var originalrow = checkID.getAttribute('data-originalrow');
          // row.remove();
          // console.log(originalrow, 'before to call resencuacion');
          // console.log(updateCollection(idConcesion), 'updateCollection(idConcesion)');
          // console.log(updateCollection(idRowConcesion), 'idRowConcesion antes ir a funcion de resecuenciar');
          // console.log(document.getElementById('indice_row_' + parseInt(idRowConcesion)).textContent, 'previo resecuenciacion indice_row_' + parseInt(idRowConcesion).textContent);

          // function eliminar(arreglo, elemento) {
          //   return arreglo.slice(0, elemento).concat(arreglo.slice(elemento + 1));
          // }
          // var tramitesNuevos = eliminar(dataAgregarFila[0], idTramiteEliminar);
          console.log('antes de agregar fila index');
          agregar_fila(dataAgregarFila[0], dataAgregarFila[1], dataAgregarFila[2], dataAgregarFila[3], dataAgregarFila[4] = '', dataAgregarFila[5] = '');

          //resencuenciarConcesionNumberTramites(updateCollection(idConcesion),originalrow,document.getElementById('indice_row_'+parseInt(idRowConcesion)).textContent);        
        } else {
          console.log("Linea # " + idRow + " No Encontrada!");
          alert("Linea # " + idRow + " No Encontrada!");
        }
      }
      //************************************************************************************************/
      //* BLANQUEANDO EL ID_Unidad1 CUANDO SE BORRA LA TRANSACCIÓN DE CAMBIO DE UNIDAD UNICAMENTE     */
      //************************************************************************************************/
      if (idCheckBox.split("_")[3] == "CU") {
        const placa = document.getElementById("idRow" + String(parseInt(idRowConcesion) - 1) + "_placa_" + "1");
        if (placa) {
          placa.classList.add("fade-out");
          placa.addEventListener("transitionend", () => {
            placa.remove();
          });
          const flecha = document.getElementById('idRow' + (idRowConcesion - 1) + "_flecha");
          if (flecha) {
            flecha.classList.add("fade-out");
            flecha.addEventListener("transitionend", () => {
              flecha.remove();
            });
            const placa1 = document.getElementById("idRow" + String(parseInt(idRowConcesion) - 1) + "_placa_" + "0");
            if (placa1) {
              placa1.classList.remove("borderPlacaSale");
              placa1.classList.add("borderPlaca");
            }
          }
        }
        document.getElementById("ID_Unidad1").value == "";
        document.getElementById("btnCambiarUnidad").style.display = "none";
        document.getElementById("idVistaSTPC1").style = "display:fixed;";
        document.getElementById("idVistaSTPC2").style = "display:none;";
        console.log(idRowConcesion, 'idRowConcesion');
        console.log('id_trash_idRow' + (idRowConcesion - 1), 'id_trash_idRow' + String(idRowConcesion - 1));
      }
      //************************************************************************************************/
      return true;
    } // final del If de si Hay error
  } catch (error) {
    console.log(
      "catch error eliminando tramite en preforma fEliminarTramite" + error
    );
    fSweetAlertEventSelect(
      "",
      "ELIMINAR TRAMITE EN PREFORMA",
      "ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA",
      "warning"
    );
    return false;
  }
}

btnCambiarUnidad.addEventListener("click", function (event) {
  if (
    event.target.innerHTML == "<strong>ENTRA</strong>" ||
    event.target.innerHTML == "ENTRA"
  ) {
    event.target.innerHTML = "<strong>SALE</strong>";
    document.getElementById("idVistaSTPC1").style = "display:fixed;";
    document.getElementById("idVistaSTPC2").style = "display:none;";
  } else {
    event.target.innerHTML = "<strong>ENTRA</strong>";
    document.getElementById("idVistaSTPC2").style = "display:fixed;";
    document.getElementById("idVistaSTPC1").style = "display:none;";
  }
});

//**************************************************************************************/
//* Pocicionando el cursos en el primer input de la pantalla id=colapoderado
//**************************************************************************************/
function setFocusElement() {
  document.getElementById("colapoderado").focus();
}

//**************************************************************************************/
//* Crear Malla Automaticamente con la información de los arreglos
//* header el titulo de los campos (el encabezado)
//* array  los datos en si
//* fields los campos que se debe traer el arreglo
//**************************************************************************************/
var Arreglo = Array();
var Encabezado = Array();
var Fields = Array();
var Cols = Array();

//document.getElementById('Malla').value = createMallaDinicamente(Encabezado, Arreglo, Fields,Cols);

function createMallaDinicamente(
  header,
  arrays,
  fields,
  cols = {},
  maxfield = 999,
  clases = []
) {
  var $html = '<div class="row">';
  header.forEach((field, fieldIndex) => {
    if (typeof field.field != "undefined") {
      const campo = field.field;
      const colClass = cols[fieldIndex]?.col
        ? `col-${cols[fieldIndex].col}`
        : "col";
      const fieldValue = field.field ?? "";
      $html += `<div class="${colClass}">${fieldValue}</div>`;
    }
  });
  $html += "</div>";
  arrays.forEach((arrayItem) => {
    $html += `<div class="row">`;
    fields.forEach((field, fieldIndex) => {
      if (field.field !== "undefined") {
        const campo = field.field;
        const colClass = cols[fieldIndex]?.col
          ? `col-${cols[fieldIndex].col}`
          : "col";
        const fieldValue = arrayItem[campo] ?? "";
        $html += `<div class="${colClass}">${fieldValue}</div>`;
      }
    });
    $html += `</div>`;
  });
  $html = $html;
  return $html;
}

function fMarcarRequicitos() {
  document.getElementById("flexSwitchCheckPermisoExplotacion").checked = true;
  document.getElementById("flexSwitchCheckCertificadoOperacion").checked = true;
  document.getElementById("flexSwitchCheckCarnetColegiacion").checked = true;
  document.getElementById(
    "flexSwitchCheckAcreditarRepresentacion"
  ).checked = true;
  document.getElementById("flexSwitchCheckEscritoSolicitud").checked = true;
  document.getElementById("flexSwitchCheckDNI").checked = true;
  document.getElementById("flexSwitchCheckRTN").checked = true;
  document.getElementById("flexSwitchCheckInspeccionFisico").checked = true;
  document.getElementById("flexSwitchCheckBoletaRevision").checked = true;
  document.getElementById(
    "flexSwitchCheckContratoArrendamiento"
  ).checked = true;
  document.getElementById("flexSwitchCheckAutenticidadCarta").checked = true;
  document.getElementById(
    "flexSwitchCheckAutenticidadDocumentos"
  ).checked = true;
}
//**************************************************************************************/
//* Cargando la información por default que debe usar el formulario
//**************************************************************************************/
function f_DataOmision() {
  //*****************************************************************************************/
  //* INICIO: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
  //*****************************************************************************************/
  loading(true,currentstep);
  //*****************************************************************************************/
  //* FINAL: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
  //*****************************************************************************************/
  var datos;
  var response;
  // Get the URL parameters from the current page
  const urlParams = new URLSearchParams(window.location.search);
  // Get a specific parameter by name
  const RAM = urlParams.get("RAM"); // Número de RAM
  if (RAM != null) {
    document.getElementById("RAM-ROTULO").innerHTML =
      "<strong>" + RAM + "</strong>";
    document.getElementById("RAM-ROTULO").style = "display:inline-block;";
    document.getElementById("RAM").value = RAM;
  } else {
    document.getElementById("RAM-ROTULO").style = "display:none;";
    document.getElementById("RAM").value = "";
  }
  // URL del Punto de Acceso a la API
  const url = $appcfg_Dominio + "Api_Ram.php";
  //  Fetch options
  // const options = {
  //   method: 'POST',
  //   body: fd,
  //   headers: {
  //     'Content-Type': 'application/json',
  //   },
  // };
  let fd = new FormData(document.forms.form1);
  //Adjuntando el action al FormData
  fd.append("action", "get-datosporomision");
  fd.append("RAM", RAM);
  // Fetch options
  const options = {
    method: "POST",
    body: fd,
  };
  // Hace la solicitud fetch con un timeout de 2 minutos
  fetchWithTimeout(url, options, 120000)
    .then((response) => response.json())
    .then(function (datos) {
      if (
        document
          .getElementById("Ciudad")
          .value.toUpperCase()
          .substring(0, 11) != "TEGUCIGALPA"
      ) {
        document.getElementById("cargadocs").style = "display:flex";
      }
      if (typeof datos[0] != "undefined") {
        if (typeof datos[2] != "undefined") {
          if (datos[2].length > 0) {
            fLlenarSelect("Departamentos", datos[2], -1, false, {
              text: "SELECCIONE UN DEPARTAMENTO",
              value: "-1",
            });
            fLlenarSelect("Municipios", [], -1, false, {
              text: "SELECCIONE UN MUNICIPIO",
              value: "-1",
            });
            fLlenarSelect("Aldeas", [], -1, false, {
              text: "SELECCIONE UNA ALDEA",
              value: "-1",
            });
          }
        }

        if (
          typeof datos[3] != "undefined" &&
          typeof datos[3][0] != "undefined"
        ) {
          //*Moviendo campos de base de datos a datos de pantalla Apoderado Legal
          document.getElementById("nomapoderado").value =
            datos[3][0]["Nombre_Apoderado_Legal"];
          document.getElementById("colapoderado").value =
            datos[3][0]["ID_Colegiacion"];
          document.getElementById("identidadapod").value =
            datos[3][0]["Ident_Apoderado_Legal"];
          document.getElementById("dirapoderado").value =
            datos[3][0]["Direccion_Apoderado_Legal"];
          document.getElementById("telapoderado").value =
            datos[3][0]["Telefono_Apoderado_Legal"];
          document.getElementById("emailapoderado").value =
            datos[3][0]["Email_Apoderado_Legal"];
          document.getElementById("ID_Apoderado").value = datos[3][0]["ID"];
          fLlenarSelect(
            "entregadocs",
            datos[1],
            datos[4][0]["Entrega_Ubicacion"],
            false,
            {
              text: "SELECCIONE UN LUGAR DE ENTREGA",
              value: "-1",
            }
          );
          document.getElementById("tipopresentacion").value =
            datos[4][0]["Presentacion_Documentos"];
          //* Moviendo campos de base de datos a datos de pantalla Solicitante
          if (typeof datos[4] != "undefined") {
            document.getElementById("rtnsoli").value =
              datos[4][0]["RTN_Solicitante"];
            document.getElementById("nomsoli").value =
              datos[4][0]["Nombre_Solicitante"];
            document.getElementById("denominacionsoli").value =
              datos[4][0]["Denominacion_Social"];
            document.getElementById("domiciliosoli").value =
              datos[4][0]["Domicilo_Solicitante"];
            document.getElementById("idEstado").innerHTML = $appcfg_icono_de_importante + datos[4][0]["DESC_Estado"];
            document.getElementById("telsoli").value =
              datos[4][0]["Telefono_Solicitante"];
            document.getElementById("emailsoli").value =
              datos[4][0]["Email_Solicitante"];
            document.getElementById("tiposolicitante").value =
              datos[4][0]["ID_Tipo_Solicitante"];
            document.getElementById("Departamentos").value =
              datos[4][0]["ID_Departamento"];
            document.getElementById("ID_Solicitante").value = datos[4][0]["ID"];
            document.getElementById("ID_Estado_RAM").value =
              datos[4][0]["Estado_Formulario"];

            var event = new Event("change", {
              bubbles: true,
              cancelable: true,
            });
            document.getElementById("Departamentos").dispatchEvent(event);
            setTimeout(() => {
              document.getElementById("Municipios").value =
                datos[4][0]["ID_Municipio"];
              document.getElementById("Municipios").dispatchEvent(event);
            }, 2000);
            setTimeout(() => {
              //document.getElementById("Municipios").dispatchEvent(event);
              document.getElementById("Aldeas").value = datos[4][0]["ID_Aldea"];
            }, 4000);
          }
          //***************************************************************************/
          //* Armando Objeto de Concesiones Salvadas en Preforma
          //***************************************************************************/
          if (typeof datos[5] != "undefined") {
            guardarConcesionSalvadaPreforma(datos[5], datos[7]);
          }
          //***************************************************************************/
          //* Estableciento el Link del Expediente Cargado para Trabajarlo
          //***************************************************************************/
          if (typeof datos[8] != "undefined" && datos[8] != false) {
            document.getElementById("fileUploaded").style.display = "block";
            document
              .getElementById("fileUploadedLink")
              .setAttribute("href", $appcfg_Dominio + datos[8]);
          } else {
            document.getElementById("fileUploaded").style.display = "none";
          }
          //***************************************************************************/
          //* Marcar requicitos
          //***************************************************************************/
          fMarcarRequicitos();
        } else {
          if (datos[1].length > 0) {
            fLlenarSelect("entregadocs", datos[1], null, false, {
              text: "SELECCIONE UN LUGAR DE ENTREGA",
              value: "-1",
            });
          }
        }
      } else {
        if (typeof datos.error != "undefined") {
          fSweetAlertEventNormal(
            datos.errorhead,
            datos.error + "- " + datos.errormsg,
            "error"
          );
        } else {
          fSweetAlertEventNormal(
            "INFORMACIÓN",
            "ALGO RARO PASO, INTENTELO DE NUEVO SI EL ERROR PERSISTE CONTACTE AL ADMINISTRADOR DEL SISTEMA",
            "error"
          );
        }
      }
      //*****************************************************************************************/
      //* INICIO: Despliega la información del stepper content y oculta el gif de procesando    */
      //*****************************************************************************************/
      loading(false,currentstep);
      if (concesionNumber.length < 1) {
        document.getElementById("input-prefetch").style.display = "none";
        document.getElementById("toggle-icon").style.display = "none";
        document.getElementById("rightDiv").style.display = "none";
      } else {
        document.getElementById("input-prefetch").style.display = "block";
        document.getElementById("toggle-icon").style.display = "block";
        document.getElementById("rightDiv").style.display = "flex";
      }
      startCelebration();
      //*****************************************************************************************/
      //* FINAL: Despliega la información del stepper content y oculta el gif de procesando    */
      //*****************************************************************************************/
    })
    .catch((error) => {
      //*****************************************************************************************/
      //* INICIO: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
      //*****************************************************************************************/
      loading(false,currentstep);
      //*****************************************************************************************/
      //* FINAL: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
      //*****************************************************************************************/
      console.log("error f_DataOmision() " + error);
      fSweetAlertEventNormal(
        "OPPS",
        "ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA",
        "error"
      );
    });
}

function fCerrarProcesoEnDB() {
  //*****************************************************************************************/
  //* INICIO: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
  //*****************************************************************************************/
  loading(true,currentstep);
  //*****************************************************************************************/
  //* FINAL: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
  //*****************************************************************************************/
  // URL del Punto de Acceso a la API
  const url = $appcfg_Dominio + "Api_Ram.php";
  let fd = new FormData(document.forms.form1);
  var Tramites = "";
  // Adjuntando el action al FormData
  if (document.getElementById("ID_Expediente").value == "") {
    fd.append("action", "cerrar-preforma");
    fd.append("idEstado", "IDE-1");
    var text =
      "RAM CERRADA EXITOSAMENTE Y ENVIADA A REVISIÓN LEGAL POR OFICIALES JURIDICOS";
  } else {
    var text =
      "EXPEDIENTE FINALIZADO EXITOSAMENTE, SE GENERO EL RESPECTIVO AUTOMOTIVADO DE INGRESO Y RESOLUCIÓN";
    fd.append("action", "cerrar-expediente");
    // Enviar el número de Expediente
    fd.append("ID_Expediente", document.getElementById("ID_Expediente").value);
    // Enviar el número de Solicitud
    fd.append("ID_Solicitud", document.getElementById("ID_Solicitud").value);
    fd.append("idEstado", "IDE-1");
  }
  // Adjuntando el Concesion y Caracterización al FormData
  fd.append("RAM", document.getElementById("RAM").value);
  const options = {
    method: "POST",
    body: fd,
  };
  // Hacer al solicitud fetch con un timeout de 2 minutos
  fetchWithTimeout(url, options, 120000)
    .then((response) => response.json())
    .then(function (Datos) {
      loading(false,currentstep);      
      if (
        typeof Datos.ERROR != "undefined" ||
        typeof Datos.error != "undefined"
      ) {
        fSweetAlertEventSelect(
          "",
          "INCONVENIENTES SALVADO LA INFORMACIÓN",
          "ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTA AL ADMINISTRADOR DEL SISTEMA",
          "error"
        );
        console.log(Datos);
        return true;
      } else {
        //*******************************************************************************************************/
        //*INICIO: ENVIO DE MENSAJE DE BORRADO DEL TRAMITE EXITOSO
        //*******************************************************************************************************/
        var html = Datos;
        sendToast(
          text,
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
        var title = "SE CERRO SATISFACTORIAMENTE LA PREFORMA " + Datos.SOL2;
        var msg =
          "Su solicitud en línea: <span style='color:#ff8f5e;'>" +
          Datos.SOL2 +
          " Asignada a:" +
          Datos.Nombre_Usuario +
          "/" +
          Datos.Cod_Usuario +
          "</span> se ha guardo.";
        console.log(Datos.url_aviso);
        console.log(Datos.msg);
        var linkaviso =
          '<a style="background-color: rgb(119 183 202); border-radius: 15px; border: solid 4px #33536f;" href="' +
          Datos.url_aviso +
          '" target="_blank"  class="waves-effect waves-green btn-flat btn"><i class="material-icons print"></i>IMPRIMIR ' +
          Datos.msg +
          " CON NÚMERO DE AVISO DE COBRO: " +
          Datos.numero_aviso +
          "</a>";
        var urlcomprobante =
          $appcfg_Dominio_Raiz +
          ":293/api_rep.php?action=get-PDFComprobante&Solicitud=" +
          Datos.SOL +
          "&fls=" +
          Datos.SOL2 +
          "&Nombre_Usuario=" +
          Datos.Nombre_Usuario +
          "&Cod_Usuario=" +
          Datos.Cod_Usuario +
          "&Originano_En_Ventanilla=1&ID_Usuario=" +
          Datos.ID_Usuario +
          "&user_name=" +
          Datos.user_name;
        var linkcomprobante =
          '<a style="background-color: rgb(119 183 202); border-radius: 15px; border: solid 4px #33536f;" href="' +
          urlcomprobante +
          '" target="_blank"  class="waves-effect waves-green btn-flat btn"><i class="material-icons print"></i>IMPRIMIR COMPROBANTE RAM No. ' +
          Datos.SOL2 +
          "</a>";
        var html = linkcomprobante + "<br/>" + linkaviso;
        fSweetAlertEventNormal(title, msg, "info", html, 'FINALIZAR', reLoadScreen);
      } 
    })
    .catch((error) => {
      //*****************************************************************************************/
      //* INICIO: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
      //*****************************************************************************************/
      loading(false,currentstep);
      //*****************************************************************************************/
      //* FINAL: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
      //*****************************************************************************************/
      console.log("catch error fCerrarProcesoEnDB en preforma" + error);
      fSweetAlertEventSelect(
        "",
        "CERRANDO RAM",
        "ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA",
        "warning"
      );
      return true;
    });
}

//**************************************************************************************/
//* INICIO: llamado a la función fCerrarProceso() Realiza Pregunta Si se Cierra o NO
//**************************************************************************************/
function fCerrarProceso() {
  let text = "¿DESEA CERRAR LA RENOVACIÓN MASIVA?";
  if (document.getElementById("ID_Expediente").value != "") {
    let text =
      "¿DESEA CERRAR EL EXPEDIENTE RAM Y GENERAR LOS DOCUMENTOS CORRESPONDIENTES?";
  }
  Swal.fire({
    title: "CERRADO PROCESO",
    text: text,
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "SÍ",
    cancelButtonText: "CANCELAR",
  }).then((result) => {
    //* Si confirma que está seguro de cerrar el expediente.
    if (result.isConfirmed) {
      fCerrarProcesoEnDB();
    }
  });
}
//**************************************************************************************/
//* FINAL: llamado a la función fCerrarProceso() Realiza Pregunta Si se Cierra o NO
//**************************************************************************************/

//**************************************************************************************/
//* Inicio llamado a la función f_DataOmision que carga los datos por defecto
//**************************************************************************************/
f_DataOmision();
//**************************************************************************************/
// Final llamado a la función f_DataOmision que carga los datos por defecto
//**************************************************************************************/
var stepperFormEl = document.querySelector("#stepperForm");
stepperForm = new Stepper(stepperFormEl, {
  linear: true,
  animation: true,
});

var btnNextList = [].slice.call(document.querySelectorAll(".btn-next-form"));
var btnNextListprevious = [].slice.call(
  document.querySelectorAll(".btn-previous-form")
);
var stepperPanList = [].slice.call(
  stepperFormEl.querySelectorAll(".bs-stepper-pane")
);
var inputMailForm = document.getElementById("inputMailForm");
var inputPasswordForm = document.getElementById("inputPasswordForm");
var form = stepperFormEl.querySelector(".bs-stepper-content form");
//*****************************************************************************/
//Creando arreglo de inputs para saber posteriormente el indice de cada input
//*****************************************************************************/
var testcontrols = [].slice.call(document.querySelectorAll(".test-controls"));
var testcontrolsArray = Array.from(testcontrols);
testinputs = document.querySelectorAll("input, select");
testinputsArray = Array.from(testinputs);
var columnas = testinputs.length;
var filas = stepperPanList.length;
// Crear una matriz bidimensional vacía
paneerror = new Array(filas);
for (var i = 0; i < filas; i++) {
  paneerror[i] = new Array(columnas);
  for (var ii = 0; ii < columnas; ii++) {
    paneerror[i][ii] = 0;
  }
  isDirty[i] = false;
  isRecordGetted[i] = "";
}
btnNextList.forEach(function (btn) {
  btn.addEventListener("click", function () {
    if (currentstep == 4) {
      //************************************************************************/
      //* INICIO: Cerrar proceso de registro de conseciones y/o Expediente
      //************************************************************************/
      fCerrarProceso();
      //************************************************************************/
      //* FINAL: Cerrar proceso de registro de conseciones y/o Expediente
      //************************************************************************/
    } else {
      if (
        currentstep != 2 ||
        (seRecuperoVehiculoDesdeIP == 0 &&
          currentstep == 2 &&
          document.getElementById("btnSalvarConcesion").style.display != "none")
      ) {
        fGetInputs();
        stepperForm.next();
      } else {
        if (currentstep == 2 && seRecuperoVehiculoDesdeIP != 0) {
          Swal.fire({
            title: "!INCONVENIENTES CON VEHICULO¡",
            text: "HA REALIZADO EL TRAMITE DE CAMBIO DE PLACA O CAMBIO DE UNIDAD DEBE RECUPERAR LA INFORMACIÓN DEL IP Y SALVAR LA PARA PODER CONTINUAR",
            icon: "warning",
            confirmButtonText: "OK",
          });
        }
      }
    }
  });
});

function fLimpiarPantalla() {
  //*********************************************************************************/
  //* Dependiendo del panel actual se ejecuta una función para validar los campos
  //*********************************************************************************/
  switch (currentstep) {
    case 0:
      document.getElementById("nomapoderado").value = "";
      document.getElementById("identidadapod").value = "";
      document.getElementById("dirapoderado").value = "";
      document.getElementById("telapoderado").value = "";
      document.getElementById("emailapoderado").value = "";
      break;
    case 1:
      document.getElementById("nomsoli").value = "";
      document.getElementById("denominacionsoli").value = "";
      document.getElementById("domiciliosoli").value = "";
      document.getElementById("telapoderado").value = "";
      document.getElementById("emailsoli").value = "";
      // document.getElementById('numescritura').value = '';
      // document.getElementById('fecha').value = '';
      // document.getElementById('lugarcons').value = '';
      // document.getElementById('rtnnotario').value = '';
      // document.getElementById('nombrenotario').value = '';
      document.getElementById("tiposolicitante").value = "";
      document.getElementById("Departamentos").value = "-1";
      document.getElementById("Municipios").value = "-1";
      document.getElementById("Municipios").innerHTML = "";
      document.getElementById("Aldeas").value = "-1";
      document.getElementById("Aldeas").innerHTML = "";
      // document.getElementById('rtnrep').value = '';
      // document.getElementById('numinscripcionrep').value = '';
      // document.getElementById('nomrep').value = '';
      // document.getElementById('domiciliorep').value = '';
      // document.getElementById('numescriturarep').value = '';
      // document.getElementById('rtnnotariorep').value = '';
      // document.getElementById('notarioautorizante').value = '';
      // document.getElementById('lugarescriturarep').value = '';
      // document.getElementById('fechaescriturarep').value = '';
      // document.getElementById('telrep').value = '';
      // document.getElementById('emailrep').value = '';
      break;
    case 2:
      document.getElementById("input-prefetch").style.display = "block";
      document.getElementById("toggle-icon").style.display = "block";
      document.getElementById("rightDiv").style.display = "flex";
      document.getElementById("div-vista-1").style = "display:none;";
      document.getElementById("combustible").value = "";
      document.getElementById("capacidad").value = "";
      document.getElementById("alto").value = "";
      document.getElementById("largo").value = "";
      document.getElementById("ancho").value = "";
      document.getElementById("combustible1").value = "";
      document.getElementById("capacidad1").value = "";
      document.getElementById("alto1").value = "";
      document.getElementById("largo1").value = "";
      document.getElementById("ancho1").value = "";
      document.getElementById("Permiso_Explotacion").value = "";
      document.getElementById("ID_Unidad").value = "";
      document.getElementById("ID_Unidad1").value = "";
      document.getElementById("Concesion_Encriptada").value = "";
      document.getElementById("Permiso_Explotacion_Encriptado").value = "";
      document.getElementById("estaPagadoElCambiodePlaca").value = false;
      document.getElementById("RequiereRenovacionConcesion").value = false;
      document.getElementById("RequiereRenovacionPerExp").value = false;
      document.getElementById("NuevaFechaVencimientoConcesion").value = "";
      document.getElementById("NuevaFechaVencimientoPerExp").value = "";
      document.getElementById("FechaVencimientoConcesion").value = "";
      document.getElementById("FechaVencimientoPerExp").value = "";
      document.getElementById("CantidadRenovacionesConcesion").value = 0;
      document.getElementById("CantidadRenovacionesPerExp").value = 0;
      //***********************************************************************************************************************************/
      //* INICIO: Limpiando Caracterización de la Concesion
      //***********************************************************************************************************************************/
      document.getElementById("ID_Categoria").value = "";
      document.getElementById("ID_Tipo_Servicio").value = "";
      document.getElementById("ID_Modalidad").value = "";
      document.getElementById("ID_Clase_Servicio").value = "";
      //***********************************************************************************************************************************/
      // Final: Limpiando Caracterización de la Concesion
      //***********************************************************************************************************************************/
      // Concesion en pantalla 3
      var concesionlabel = document.getElementById("concesionlabel");
      if (concesionlabel != null) {
        concesionlabel.innerHTML = "";
      }
      document.getElementById("concesion_concesion").innerHTML = "";
      document.getElementById("concesion_perexp").innerHTML = "";
      document.getElementById("concesion_fecexp").innerHTML = "";
      document.getElementById("concesion_resolucion").innerHTML = "";
      document.getElementById("concesion_fecven").innerHTML = "";
      document.getElementById("concesion_nombreconcesionario").innerHTML = "";
      document.getElementById("concesion_rtn").innerHTML = "";
      document.getElementById("concesion_fecexp").innerHTML = "";
      document.getElementById("concesion_resolucion").innerHTML = "";
      document.getElementById("concesion_nombre_propietario").innerHTML = "";
      document.getElementById("concesion_identidad_propietario").innerHTML = "";
      document.getElementById("concesion_placaanterior").style = "";
      document.getElementById("concesion_placaanterior").innerHTML = "";
      document.getElementById("concesion_placa").value = "";
      document.getElementById("concesion_tipo_vehiculo").value = "";
      document.getElementById("concesion_modelo_vehiculo").value = "";
      document.getElementById("concesion_vin").value = "";
      document.getElementById("concesion_serie").value = "";
      document.getElementById("concesion_motor").value = "";
      document.getElementById("marcas").value = "";
      document.getElementById("colores").value = "";
      document.getElementById("anios").value = "";
      document.getElementById("concesion_tipovehiculo").innerHTML = "";
      document.getElementById("concesion_cerant").innerHTML = "";
      document.getElementById("concesion_numregant").innerHTML = "";
      document.getElementById("concesion_numeroregistro").innerHTML = "";
      document.getElementById("concesion_categoria").innerHTML = "";
      document.getElementById("combustible").value = "";
      document.getElementById("capacidad").value = "";
      document.getElementById("alto").value = "";
      document.getElementById("largo").value = "";
      document.getElementById("ancho").value = "";
      // Concesion en Pantalla 4
      var concesionlabel = document.getElementById("concesion1label");
      if (concesionlabel != null) {
        concesionlabel.innerHTML = "";
      }
      document.getElementById("concesion1_concesion").innerHTML = "";
      document.getElementById("concesion1_perexp").innerHTML = "";
      document.getElementById("concesion1_resolucion").innerHTML = "";
      document.getElementById("concesion1_fecven").innerHTML = "";
      document.getElementById("concesion1_nombreconcesionario").innerHTML = "";
      document.getElementById("concesion1_rtn").innerHTML = "";
      document.getElementById("concesion1_fecexp").innerHTML = "";
      document.getElementById("concesion1_resolucion").innerHTML = "";
      document.getElementById("concesion1_nombre_propietario").innerHTML = "";
      document.getElementById("concesion1_identidad_propietario").innerHTML =
        "";
      if (document.getElementById("concesion_placaanterior").innerHTML != "") {
        document.getElementById("concesion1_placaanterior").innerHTML = "";
        document.getElementById("concesion1_placaanterior").style = "";
      } else {
        document.getElementById("concesion1_placaanterior").style = "";
        document.getElementById("concesion1_placaanterior").innerHTML = "";
      }
      document.getElementById("concesion1_tipo_vehiculo").value = "";
      document.getElementById("concesion1_modelo_vehiculo").value = "";
      document.getElementById("concesion1_vin").value = "";
      document.getElementById("concesion1_placa").value = "";
      document.getElementById("concesion1_serie").value = "";
      document.getElementById("concesion1_motor").value = "";
      document.getElementById("marcas1").value = "";
      document.getElementById("colores1").value = "";
      document.getElementById("anios1").value = "";
      document.getElementById("concesion1_tipovehiculo").innerHTML = "";
      document.getElementById("concesion1_cerant").innerHTML = "";
      document.getElementById("concesion1_numregant").innerHTML = "";
      document.getElementById("concesion1_numeroregistro").innerHTML = "";
      document.getElementById("concesion1_categoria").innerHTML = "";
      document.getElementById("combustible1").value = "";
      document.getElementById("capacidad1").value = "";
      document.getElementById("alto1").value = "";
      document.getElementById("largo1").value = "";
      document.getElementById("ancho1").value = "";
      //*******************************************************************************/
      //*Desmarcar tramites
      //*******************************************************************************/
      esCambioDeVehiculo = false;
      esCambioDePlaca = false;
      seRecuperoVehiculoDesdeIP = 0;
      document.getElementById("idVistaSTPC2").style = "display:none;";
      document.getElementById("idVistaSTPC1").style = "display:fixed;";
      ProcessFormalitiesUnCheck();
      var concesion_tramite_placa_CU = document.getElementById(
        "concesion_tramite_placa_CU"
      );
      if (concesion_tramite_placa_CU != null) {
        concesion_tramite_placa_CU.value = "";
      }
      var concesion_tramite_placa_CL = document.getElementById(
        "concesion_tramite_placa_CL"
      );
      if (concesion_tramite_placa_CL != null) {
        concesion_tramite_placa_CL.value = "";
      }

      let chkTramites = document.querySelectorAll('input[name="tramites[]"]');
      if (chkTramites) {
        chkTramites.forEach(function (chk) {
          if (chk.checked) {
            chk.checked = false;
          }
        });
      }

      document.getElementById("concesion_tramites").style.display = "none";

    case 4:
      break;
    case 5:
      break;
    default:
      document.getElementById("nomapoderado").value = "";
      document.getElementById("identidadapod").value = "";
      document.getElementById("dirapoderado").value = "";
      document.getElementById("telapoderado").value = "";
      document.getElementById("emailapoderado").value = "";
      break;
  }
}

function ProcessFormalities() {
  var respuesta = false;
  if (chkTramites) {
    chkTramites.forEach(function (chk) {
      if (chk.checked && chk.getAttribute("disabled") != "") {
        respuesta = true;
      }
    });
  }
  return respuesta;
}

function ProcessFormalitiesUnCheck() {
  if (chkTramites) {
    chkTramites.forEach(function (chk) {
      if (chk.checked) {
        chk.setAttribute("disable", false);
        chk.checked = false;
      }
    });
  }
}

function countFormalities() {
  var contador = 0;
  if (chkTramites) {
    chkTramites.forEach(function (chk) {
      if (chk.checked) {
        contador++;
      }
    });
  }
  return contador;
}

btnNextListprevious.forEach(function (btn) {
  btn.addEventListener("click", function () {
    var goPrevious = true;
    if (currentstep == 2 && seRecuperoVehiculoDesdeIP != 0) {
      Swal.fire({
        title: "!INCONVENIENTES CON VEHICULO¡",
        text: "HA REALIZADO EL TRAMITE DE CAMBIO DE PLACA O CAMBIO DE UNIDAD DEBE RECUPERAR LA INFORMACIÓN DEL IP Y SALVAR LA PARA PODER CONTINUAR",
        icon: "warning",
        confirmButtonText: "OK",
      });
      goPrevious = false;
    } else {
      if (currentstep < 3) {
        if (isRecordGetted[currentstep] != "") {
          fGetInputs();
        }
      } else {
        if (currentstep == 3) {
          showModalFromShown = false;
          fGetInputs();
          if (modalidadDeEntrada == "I" && ProcessFormalities() == true) {
            fSweetAlertEventNormal(
              "ERROR",
              "HAY TRAMITES REGISTRADOS, DEBE SALVAR LA INFORMACIÓN DE LA PANTALLA O DESMARCAR LOS TRAMITES",
              "error"
            );
            goPrevious = false;
          }
        }
      }
    }
    // Si no tiene ningun error previo en la validación se entra al siguiente if y se mueve al siguiente panel
    if (goPrevious == true) {
      stepperForm.previous();
    }
  });
});

function f_FetchCallApoderado(idApoderado, event, idinput) {
  // URL del Punto de Acceso a la API
  const url = $appcfg_Dominio + "Api_Ram.php";
  //  Fetch options
  // const options = {
  //   method: 'POST',
  //   body: fd,
  //   headers: {
  //     'Content-Type': 'application/json',
  //   },
  // };
  let fd = new FormData(document.forms.form1);
  //Adjuntando el action al FormData
  fd.append("action", "get-apoderado");
  //Adjuntando el idApoderado al FormData
  fd.append("idApoderado", idApoderado);
  // Fetch options
  const options = {
    method: "POST",
    body: fd,
  };
  isRecordGetted[currentstep] = idApoderado;
  // Hace la solicitud fetch con un timeout de 2 minutos
  fetchWithTimeout(url, options, 120000)
    .then((response) => response.json())
    .then(function (datos) {
      if (typeof datos.nombre_apoderado != "undefined") {
        if (datos.nombre_apoderado != "" && datos.nombre_apoderado != null) {
          //Limpiando mensajes de error
          fCleanErrorMsg();
          document.getElementById("nomapoderado").value =
            datos.nombre_apoderado;
          document.getElementById("identidadapod").value =
            datos.ident_apoderado;
          document.getElementById("dirapoderado").value = datos.dir_apoderado;
          document.getElementById("telapoderado").value = datos.tel_apoderado;
          document.getElementById("emailapoderado").value =
            datos.correo_apoderado;
          isError = false;
          //Moviendose al siguiente input
          moveToNextInput(event.target, 0);
          sendToast(
            "INFORMACIÓN DEL APODERADO RECUPERADA EXITOSAMENTE",
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
        } else {
          fLimpiarPantalla();
          fSweetAlertEventSelect(
            event,
            "INFORMACIÓN",
            "EL NÚMERO DE COLEGIACIÓN NO ENCONTRADO, FAVOR VERIFIQUE EL NÚMERO E INTENTELO NUEVAMENTE",
            "warning"
          );
          event.preventDefault();
          event.target.classList.add("text-error");
          var label = document.getElementById(event.target.id + "label");
          if (label != null) {
            label.classList.add("errorlabel");
          }
          isError = true;
        }
      } else {
        if (typeof datos.error != "undefined") {
          fLimpiarPantalla();
          fSweetAlertEventSelect(
            event,
            datos.errorhead,
            datos.error + "- " + datos.errormsg,
            "error"
          );
          event.preventDefault();
          event.target.classList.add("text-error");
          var label = document.getElementById(event.target.id + "label");
          if (label != null) {
            label.classList.add("errorlabel");
          }
          isError = true;
        } else {
          fLimpiarPantalla();
          fSweetAlertEventSelect(
            event,
            "INFORMACIÓN",
            "ERROR DESCONOCIDO, INTENTELO DE NUEVO SI EL ERROR PERSISTE CONTACTE AL ADMINISTRADOR DEL SISTEMA",
            "error"
          );
          event.preventDefault();
          //event.target.select();
          event.target.classList.add("text-error");
          var label = document.getElementById(event.target.id + "label");
          if (label != null) {
            label.classList.add("errorlabel");
          }
          isError = true;
        }
      }
    })
    .catch((error) => {
      fLimpiarPantalla();
      fSweetAlertEventSelect(
        event,
        "CONEXÍON",
        "ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA",
        "warning"
      );
      event.preventDefault();
      event.target.classList.add("text-error");
      var label = document.getElementById(event.target.id + "label");
      if (label != null) {
        label.classList.add("errorlabel");
      }
      isError = true;
    });
}

function f_FetchCallSolicitante(idSolicitante, event, idinput) {
  isRecordGetted[currentstep] = idSolicitante;
  // URL del Punto de Acceso a la API
  const url = $appcfg_Dominio + "Api_Ram.php";
  //  Fetch options
  // const options = {
  //   method: 'POST',
  //   body: fd,
  //   headers: {
  //     'Content-Type': 'application/json',
  //   },
  // };
  let fd = new FormData(document.forms.form1);
  //Adjuntando el action al FormData
  fd.append("action", "get-solicitante");
  //Adjuntando el idApoderado al FormData
  fd.append("idSolicitante", idSolicitante);
  // Fetch options
  const options = {
    method: "POST",
    body: fd,
  };
  // Hacer al solicitud fetch con un timeout de 2 minutos
  fetchWithTimeout(url, options, 120000)
    .then((response) => response.json())
    .then(function (datos) {
      if (typeof datos[0] != "undefined") {
        if (datos[0] > 0) {
          //Limpiando mensajes de error
          fCleanErrorMsg();
          document.getElementById("nomsoli").value =
            datos[1].nombre_solicitante;
          document.getElementById("denominacionsoli").value =
            datos[1].nombre_empresa;
          document.getElementById("domiciliosoli").value =
            datos[1].dir_solicitante;
          document.getElementById("emailsoli").value =
            datos[1].correo_solicitante;
          document.getElementById("telsoli").value = datos[1].tel_solicitante;
          document.getElementById("tiposolicitante").value =
            datos[1].DESC_Solicitante;
          document
            .getElementById("tiposolicitante")
            .setAttribute("data-id", datos[1].ID_Tipo_Solicitante);
          if (datos[1].Departamento != null && datos[1].Departamento != "") {
            document.getElementById("Departamentos").value =
              datos[1].Departamento;
          } else {
            document.getElementById("Departamentos").value = "-1";
          }
          fLlenarSelect("Municipios", datos[3], datos[1].Municipio, false, {
            text: "SELECCIONE UN MUNICIPIO",
            value: "-1",
          });
          fLlenarSelect("Aldeas", datos[4], datos[1].aldea, false, {
            text: "SELECCIONE UNA ALDEA",
            value: "-1",
          });
          isError = false;
          moveToNextInput(event.target, 0);
          sendToast(
            "INFORMACIÓN DEL SOLICITANTE RECUPERADA EXITOSAMENTE",
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
        } else {
          fLimpiarPantalla();
          fSweetAlertEventSelect(
            event,
            "INFORMACIÓN",
            "EL RTN DEL SOLICITANTE NO ENCONTRADO, FAVOR VERIFIQUE EL NÚMERO E INTENTELO NUEVAMENTE",
            "warning"
          );
          event.preventDefault();
          event.target.classList.add("text-error");
          var label = document.getElementById(event.target.id + "label");
          if (label != null) {
            label.classList.add("errorlabel");
          }
          isError = true;
        }
      } else {
        if (typeof datos.error != "undefined") {
          fLimpiarPantalla();
          fSweetAlertEventSelect(
            event,
            datos.errorhead,
            datos.error + "- " + datos.errormsg,
            "error"
          );
          event.preventDefault();
          event.target.classList.add("text-error");
          var label = document.getElementById(event.target.id + "label");
          if (label != null) {
            label.classList.add("errorlabel");
          }
          isError = true;
        } else {
          fLimpiarPantalla();
          fSweetAlertEventSelect(
            event,
            "INFORMACIÓN",
            "ERROR DESCONOCIDO, INTENTELO DE NUEVO EN UN MOMENTO, SI EL ERROR PERSISTE CONTACTE AL ADMINISTRADOR DEL SISTEMA",
            "error"
          );
          event.preventDefault();
          //event.target.select();
          event.target.classList.add("text-error");
          var label = document.getElementById(event.target.id + "label");
          if (label != null) {
            label.classList.add("errorlabel");
          }
          isError = true;
        }
      }
    })
    .catch((error) => {
      console.log(error, "catch f_FetchCallSolicitante");
      fLimpiarPantalla();
      fSweetAlertEventSelect(
        event,
        "CONEXÍON",
        "ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA",
        "warning"
      );
      event.preventDefault();
      event.target.classList.add("text-error");
      var label = document.getElementById(event.target.id + "label");
      if (label != null) {
        label.classList.add("errorlabel");
      }
      isError = true;
    });
}

function f_RenderConcesion(datos) {
  var concesionlabel = document.getElementById("concesionlabel");
  if (concesionlabel != null) {
    document.getElementById("concesionlabel").innerHTML =
      datos[1][0]["Tipo_Concesion"];
  }
  claseDeServicio = datos[1][0]["ID_Clase_Servico"];

  if (claseDeServicio == "STPP" || claseDeServicio == "STPC") {
    esCertificado = true;
    if (claseDeServicio == "STPC") {
      esCarga = true;
    } else {
      esCarga = false;
    }

    document.getElementById("Concesion_Encriptada").value =
      datos[1][0]["CertificadoEncriptado"];
    document.getElementById("Permiso_Explotacion_Encriptado").value =
      datos[1][0]["Permiso_Explotacion_Encriptado"];
    document.getElementById("concesion_perexp").innerHTML =
      datos[1][0]["N_Permiso_Explotacion"];
    document.getElementById("Permiso_Explotacion").value =
      datos[1][0]["N_Permiso_Explotacion"];
  } else {
    esCertificado = false;
    if (claseDeServicio == "STEC") {
      esCarga = true;
    } else {
      esCarga = false;
    }

    document.getElementById("Concesion_Encriptada").value =
      datos[1][0]["PermisoEspecialEncriptado:"];
    document.getElementById("Permiso_Explotacion").value = "";
    document.getElementById("Permiso_Explotacion_Encriptado").value = "";
  }

  document.getElementById("concesion_tramites").innerHTML =
    datos[1][0]["Tramites"];

  setaddEventListener();

  document.getElementById("concesion_concesion").innerHTML =
    datos[1][0]["N_Certificado"];

  document.getElementById("concesion_fecven").innerHTML =
    datos[1][0]["Fecha Vencimiento Certificado"];
  document.getElementById("concesion_nombreconcesionario").innerHTML =
    datos[1][0]["NombreSolicitante"];
  document.getElementById("concesion_rtn").innerHTML =
    datos[1][0]["RTN_Concesionario"];
  document.getElementById("concesion_fecexp").innerHTML =
    datos[1][0]["Fecha Emision Certificado"];
  document.getElementById("concesion_resolucion").innerHTML =
    datos[1][0]["Resolucion"];
  if (
    datos.length > 1 &&
    datos[1].length > 0 &&
    datos[1][0]["Unidad"] &&
    datos[1][0]["Unidad"].length > 0
  ) {
    document.getElementById("concesion_nombre_propietario").innerHTML =
      datos[1][0]["Unidad"][0]["Nombre_Revicion"];
    document.getElementById("concesion_identidad_propietario").innerHTML =
      datos[1][0]["Unidad"][0]["RTN_Propietario"];
    if (
      datos[1] &&
      datos[1][0] &&
      datos[1][0]["Unidad"] &&
      datos[1][0]["Unidad"][0] &&
      datos[1][0]["Unidad"][0]["ID_Placa_Anterior"]
    ) {
      document.getElementById("concesion_placaanterior").style =
        "display:inline;";
      document.getElementById("concesion_placaanterior").innerHTML =
        datos[1][0]["Unidad"][0]["ID_Placa_Anterior"];
    } else {
      document.getElementById("concesion_placaanterior").style =
        "display:none;";
      document.getElementById("concesion_placaanterior").innerHTML = "";
    }
    //***********************************************************************************************************************************/
    // INICIO:
    // Marcando tramites obligatrios dependiendo de las condciones de vencimiento de las concesiones y permisos de explotacion
    // y si ya se pago el cambio de placa de la unidad actual, si tuvo cambio de placa
    //***********************************************************************************************************************************/
    document.getElementById("RequiereRenovacionConcesion").value = false;
    document.getElementById("RequiereRenovacionPerExp").value = false;
    var el = document.getElementById("IHTTTRA-02_CLATRA-02_R_CO");
    if (el != null) {
      document.getElementById("IHTTTRA-02_CLATRA-02_R_CO").checked = false;
      document.getElementById("IHTTTRA-02_CLATRA-02_R_CO").disabled = true;
      document.getElementById("IHTTTRA-02_CLATRA-01_R_PE").checked = false;
      document.getElementById("IHTTTRA-02_CLATRA-01_R_PE").disabled = true;
      document.getElementById("row_tramite_X_CO").style.display = "flex";
      document.getElementById("row_tramite_X_PE").style.display = "flex";
    } else {
      el = document.getElementById("IHTTTRA-02_CLATRA-03_R_PS");
      if (el != null) {
        el.checked = false;
        el.disabled = true;
        document.getElementById("row_tramite_X_PS").style.display = "flex";
      }
    }
    //***********************************************************************************************************************/
    //* Se pago el cambio de placa de la unidad actual, esto se hace cuando se detecta que la undiad cambio de placa
    //* y no se encuentra en nuestros registros el pago de ese cambio de placa
    //***********************************************************************************************************************/
    document.getElementById("estaPagadoElCambiodePlaca").value =
      datos[1][0]["Unidad"][0]["estaPagadoElCambiodePlaca"];
    if (datos[1][0]["Unidad"][0]["estaPagadoElCambiodePlaca"] == false) {
      document.getElementById("IHTTTRA-03_CLATRA-15_M_CL").checked = true;
      document.getElementById("IHTTTRA-03_CLATRA-15_M_CL").disabled = true;
      document.getElementById("concesion_tramite_placa_CL").style =
        "display:flex;text-transform: uppercase;";
      document.getElementById("concesion_tramite_placa_CL").value =
        datos[1][0]["Unidad"][0]["ID_Placa"];
      document
        .getElementById("concesion_tramite_placa_CL")
        .setAttribute("readonly", true);
      esCambioDePlaca = true;
      requiereCambioDePlaca = true;
    } else {
      esCambioDePlaca = false;
      requiereCambioDePlaca = false;
      document.getElementById("IHTTTRA-03_CLATRA-15_M_CL").checked = false;
      document.getElementById("IHTTTRA-03_CLATRA-15_M_CL").disabled = false;
      document.getElementById("concesion_tramite_placa_CL").style =
        "display:none;text-transform: uppercase;";
      document.getElementById("concesion_tramite_placa_CL");
    }
    if (datos[1][0]["Vencimientos"]["renovacion_certificado_vencido"] == true) {
      document.getElementById("RequiereRenovacionConcesion").value = true;
      document.getElementById("IHTTTRA-02_CLATRA-02_R_CO").checked = true;
      document.getElementById("IHTTTRA-02_CLATRA-02_R_CO").disabled = true;
      if (datos[1][0]["Vencimientos"]["rencon-cantidad"] > 0) {
        document.getElementById("concesion_fecven").innerHTML =
          "  " +
          datos[1][0]["Vencimientos"]["Fecha_Expiracion"] +
          " ==> " +
          datos[1][0]["Vencimientos"]["Nueva_Fecha_Expiracion"] +
          "   (" +
          datos[1][0]["Vencimientos"]["rencon-cantidad"] +
          ")";
      }
      //Inicio
      document.getElementById("row_tramite_X_CO").style.display = "none";
      //Final
    } else {
      if (
        datos[1][0]["Vencimientos"]["renovacion_permiso_especial_vencido"] ==
        true
      ) {
        document.getElementById("RequiereRenovacionConcesion").value = true;
        document.getElementById("IHTTTRA-02_CLATRA-03_R_PS").checked = true;
        document.getElementById("IHTTTRA-02_CLATRA-03_R_PS").disabled = true;
        //Inicio
        document.getElementById("row_tramite_X_PS").style.display = "none";
        //Final
      }
    }
    document.getElementById("RequiereRenovacionPerExp").value =
      datos[1][0]["Vencimientos"]["renovacion_permisoexplotacion_vencido"];
    if (
      datos[1][0]["Vencimientos"]["renovacion_permisoexplotacion_vencido"] ==
      true
    ) {
      document.getElementById("IHTTTRA-02_CLATRA-01_R_PE").checked = true;
      document.getElementById("IHTTTRA-02_CLATRA-01_R_PE").disabled = true;
      if (datos[1][0]["Vencimientos"]["renper-explotacion-cantidad"] > 0) {
        document.getElementById("concesion_perexp").innerHTML =
          document.getElementById("concesion_perexp").innerHTML +
          " ||  " +
          datos[1][0]["Vencimientos"]["Fecha_Expiracion_Explotacion"] +
          " ==> " +
          datos[1][0]["Vencimientos"]["Nueva_Fecha_Expiracion_Explotacion"] +
          "   (" +
          datos[1][0]["Vencimientos"]["renper-explotacion-cantidad"] +
          ")";
      }
      //Inicio
      document.getElementById("row_tramite_X_PE").style.display = "none";
      //Final
    }
    //***********************************************************************************************************************************/
    // INICIO: Caracterización de la Concesion
    //***********************************************************************************************************************************/
    document.getElementById("ID_Categoria").value = datos[1][0]["ID_Categoria"];
    document.getElementById("ID_Tipo_Servicio").value =
      datos[1][0]["ID_Tipo_Servico"];
    document.getElementById("ID_Modalidad").value = datos[1][0]["ID_Modalidad"];
    document.getElementById("ID_Clase_Servicio").value =
      datos[1][0]["ID_Clase_Servico"];
    //***********************************************************************************************************************************/
    // FINAL: Caracterización de la Concesion
    //***********************************************************************************************************************************/
    //***********************************************************************************************************************************/
    // INICIO:
    // Marcando tramites obligatrios dependiendo de las condciones de vencimiento de las concesiones y permisos de explotacion
    // y si ya se pago el cambio de placa de la unidad actual, si tuvo cambio de placa
    //***********************************************************************************************************************************/
    document.getElementById("NuevaFechaVencimientoConcesion").value =
      datos[1][0]["Vencimientos"]["Nueva_Fecha_Expiracion"];
    document.getElementById("FechaVencimientoConcesion").value =
      datos[1][0]["Vencimientos"]["Fecha_Expiracion"];
    document.getElementById("NuevaFechaVencimientoPerExp").value =
      datos[1][0]["Vencimientos"]["Nueva_Fecha_Expiracion_Explotacion"];
    document.getElementById("FechaVencimientoPerExp").value =
      datos[1][0]["Vencimientos"]["Fecha_Expiracion_Explotacion"];
    document.getElementById("CantidadRenovacionesConcesion").value =
      datos[1][0]["Vencimientos"]["rencon-cantidad"];
    document.getElementById("CantidadRenovacionesPerExp").value =
      datos[1][0]["Vencimientos"]["renper-explotacion-cantidad"];
    document.getElementById("combustible").value =
      datos[1][0]["Unidad"][0]["Combustible"];
    document.getElementById("ID_Unidad").value = ""; //*datos[1][0]["Unidad"][0]["ID_Unidad"];
    document.getElementById("concesion_vin").value =
      datos[1][0]["Unidad"][0]["VIN"];
    document.getElementById("concesion_vin").value =
      datos[1][0]["Unidad"][0]["VIN"];
    document.getElementById("concesion_placa").value =
      datos[1][0]["Unidad"][0]["ID_Placa"];
    document.getElementById("concesion_serie").value =
      datos[1][0]["Unidad"][0]["Chasis"];
    document.getElementById("concesion_motor").value =
      datos[1][0]["Unidad"][0]["Motor"];
    document.getElementById("concesion_tipo_vehiculo").value =
      datos[1][0]["Unidad"][0]["Tipo"];
    document.getElementById("concesion_modelo_vehiculo").value =
      datos[1][0]["Unidad"][0]["Modelo"];
    dataConcesion["marcas"] = datos[1][0]["Marcas"];
    fLlenarSelect(
      "marcas",
      datos[1][0]["Marcas"],
      datos[1][0]["Unidad"][0]["ID_Marca"],
      false,
      { text: "SELECCIONE UNA MARCA", value: "-1" }
    );
    dataConcesion["colores"] = datos[1][0]["Colores"];
    fLlenarSelect(
      "colores",
      datos[1][0]["Colores"],
      datos[1][0]["Unidad"][0]["ID_Color"],
      false,
      { text: "SELECCIONE UN COLOR", value: "-1" }
    );
    dataConcesion["anios"] = datos[1][0]["Anios"];
    fLlenarSelect(
      "anios",
      datos[1][0]["Anios"],
      datos[1][0]["Unidad"][0]["Anio"],
      false,
      { text: "SELECCIONE UN AÑO", value: "-1" }
    );
    document.getElementById("concesion_tipovehiculo").innerHTML =
      datos[1][0]["Unidad"][0]["DESC_Tipo_Vehiculo"];
  }
  document.getElementById("concesion_cerant").innerHTML =
    datos[1][0]["Certificado Anterior"];
  document.getElementById("concesion_numregant").innerHTML =
    datos[1][0]["Registro_Anterior"];
  document.getElementById("concesion_numeroregistro").innerHTML =
    datos[1][0]["Numero_Registro"];
  document.getElementById("concesion_categoria").innerHTML =
    datos[1][0]["DESC_Categoria"];
  // document.getElementById("btnconcesion").style = "display:inline;";
  // document.getElementById("btnmultas").style = "display:inline;";
  // document.getElementById("btnconsultas").style = "display:inline;";
  // document.getElementById("btnperexp").style = "display:inline;";
  document.getElementById("concesion_vin").focus();
  //***********************************************************************************************************************************/
  //* Cargando Información en la Pantalla 2 de Vehiculo Entra (Por si Hay cambio de Unidad)
  //***********************************************************************************************************************************/
  f_RenderConcesionTramites();
}

//*********************************************************************************************************************/
//** Inicio Function Carga Los Datos de la Unidad1 y Presenta la Pantalla con dicha información
//*********************************************************************************************************************/
function f_RenderConcesionTramites() {
  var concesionlabel = document.getElementById("concesion1label");

  if (concesionlabel != null) {
    concesionlabel.innerHTML =
      document.getElementById("concesionlabel").innerHTML;
  }

  document.getElementById("concesion1_concesion").innerHTML =
    document.getElementById("concesion_concesion").innerHTML;
  document.getElementById("concesion1_perexp").innerHTML =
    document.getElementById("concesion_perexp").innerHTML;
  document.getElementById("concesion1_fecexp").innerHTML =
    document.getElementById("concesion_fecexp").innerHTML;
  document.getElementById("concesion1_resolucion").innerHTML =
    document.getElementById("concesion_resolucion").innerHTML;
  document.getElementById("concesion1_fecven").innerHTML =
    document.getElementById("concesion_fecven").innerHTML;
  document.getElementById("concesion1_nombreconcesionario").innerHTML =
    document.getElementById("concesion_nombreconcesionario").innerHTML;
  document.getElementById("concesion1_rtn").innerHTML =
    document.getElementById("concesion_rtn").innerHTML;
  document.getElementById("concesion1_resolucion").innerHTML =
    document.getElementById("concesion_resolucion").innerHTML;
  document.getElementById("concesion1_nombre_propietario").innerHTML =
    document.getElementById("concesion_nombre_propietario").innerHTML;
  document.getElementById("concesion1_identidad_propietario").innerHTML =
    document.getElementById("concesion_identidad_propietario").innerHTML;

  if (document.getElementById("concesion_placaanterior").innerHTML != "") {
    document.getElementById("concesion1_placaanterior").innerHTML =
      document.getElementById("concesion_placaanterior").innerHTML;
    document.getElementById("concesion1_placaanterior").style =
      "display:inline;";
  } else {
    document.getElementById("concesion1_placaanterior").style = "display:none;";
    document.getElementById("concesion1_placaanterior").innerHTML = "";
  }

  document.getElementById("concesion1_tipo_vehiculo").value =
    document.getElementById("concesion_tipo_vehiculo").value;
  document.getElementById("concesion1_modelo_vehiculo").value =
    document.getElementById("concesion_modelo_vehiculo").value;
  document.getElementById("concesion1_vin").value =
    document.getElementById("concesion_vin").value;
  document.getElementById("concesion1_placa").value =
    document.getElementById("concesion_placa").value;
  document.getElementById("concesion1_serie").value =
    document.getElementById("concesion_serie").value;
  document.getElementById("concesion1_motor").value =
    document.getElementById("concesion_motor").value;

  fLlenarSelect(
    "marcas1",
    dataConcesion["marcas"],
    document.getElementById("marcas").value,
    false,
    { text: "SELECCIONE UNA AÑO", value: "-1" }
  );

  fLlenarSelect(
    "colores1",
    dataConcesion["colores"],
    document.getElementById("colores").value,
    false,
    { text: "SELECCIONE UN COLOR", value: "-1" }
  );

  fLlenarSelect(
    "anios1",
    dataConcesion["anios"],
    document.getElementById("anios").value,
    false,
    { text: "SELECCIONE UN AÑO", value: "-1" }
  );
  document.getElementById("concesion1_tipovehiculo").innerHTML =
    document.getElementById("concesion_tipovehiculo").innerHTML;
  document.getElementById("concesion1_cerant").innerHTML =
    document.getElementById("concesion_cerant").innerHTML;
  document.getElementById("concesion1_numregant").innerHTML =
    document.getElementById("concesion_numregant").innerHTML;
  document.getElementById("concesion1_numeroregistro").innerHTML =
    document.getElementById("concesion_numeroregistro").innerHTML;
  document.getElementById("concesion1_categoria").innerHTML =
    document.getElementById("concesion_categoria").innerHTML;
  document.getElementById("combustible1").value =
    document.getElementById("combustible").value;
  document.getElementById("capacidad1").value =
    document.getElementById("capacidad").value;
  document.getElementById("alto1").value =
    document.getElementById("alto").value;
  document.getElementById("largo1").value =
    document.getElementById("largo").value;
  document.getElementById("ancho1").value =
    document.getElementById("ancho").value;
}
//*********************************************************************************************************************/
//** Inicio Function para Establecer la Unidad de los Tramites                                                        **/
//*********************************************************************************************************************/
function f_FetchCallConcesion(idConcesion, event, idinput) {
  //*****************************************************************************************/
  //* INICIO: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
  //*****************************************************************************************/
  loading(true,currentstep);
  //*****************************************************************************************/
  //* FINAL: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
  //*****************************************************************************************/
  // URL del Punto de Acceso a la API
  const url = $appcfg_Dominio + "Api_Ram.php";
  //  Fetch options
  // const options = {
  //   method: 'POST',
  //   body: fd,
  //   headers: {
  //     'Content-Type': 'application/json',
  //   },
  // };
  let fd = new FormData(document.forms.form1);
  //Adjuntando el action al FormData
  fd.append("action", "get-concesion");
  //Adjuntando el idApoderado al FormData
  fd.append("RTN_Concesionario", document.getElementById("rtnsoli").value);
  fd.append("RAM", document.getElementById("RAM").value);
  fd.append("Concesion", idConcesion);
  // Fetch options
  const options = {
    method: "POST",
    body: fd,
  };
  // Hacer al solicitud fetch con un timeout de 2 minutos
  fetchWithTimeout(url, options, 120000)
    .then((response) => response.json())
    .then(function (datos) {
      console.log(datos, "datos en get-concesion");
      if (typeof datos[0] != "undefined") {
        if (datos[0] > 0) {
          //******************************************/
          //* Si el vehiculo fue recuperado desde el IP, favor continuar con el proceso normal y mover al informacion a la pantalla
          //******************************************/
          if (datos[1]?.error !== undefined) {
            fLimpiarPantalla();
            fSweetAlertEventSelect(
              event,
              datos[1].errorhead,
              datos[1].error + "- " + datos[1].errormsg,
              "error"
            );
            isError = true;
          } else {
            if (
              datos[1]?.[0]?.["Bloqueado"] !== undefined &&
              datos[1]?.[0]?.["Unidad"]?.[0]?.["Bloqueado"] === true
            ) {
              fLimpiarPantalla();
              fSweetAlertEventSelect(
                event,
                "BLOQUEADA",
                "LA UNIDAD ESTA BLOQUEADA EN EL IP, NO SE PUEDE REGISTRAR ESTA UNIDAD",
                "error"
              );
              isError = true;
            } else {
              if (
                datos[1]?.[0]?.["Codigo_IP"] !== undefined &&
                datos[1]?.[0]?.["Unidad"]?.[0]?.["Codigo_IP"] === 200
              ) {
                fLimpiarPantalla();
                fSweetAlertEventSelect(
                  event,
                  "CONEXIÓN IP",
                  "LA CONEXIÓN AL IP, ESTA PRESENTANDO PROBLEMAS, FAVOR INTENTELO EN UN MOMENTO Y SI EL ERROR PERSISTE CONTACTE AL ADMON DEL SISTEMA",
                  "error"
                );
                isError = true;
              } else {
                //Ocultar Pantalla Modal donde se Ingresa a Concesión
                $("#modalConcesion").modal("hide");
                isRecordGetted[currentstep] = idConcesion;
                //***************************************************************************************/
                //**Presentando la tabla de tramites                                                    */
                //***************************************************************************************/
                document.getElementById("btnCambiarUnidad").style.display =
                  "none";
                document.getElementById("btnSalvarConcesion").style =
                  "display:fixed;";
                document.getElementById("concesion_tramites").style =
                  "display:fixed;";
                document.getElementById("div-vista-1").style = "display:fixed;";
                document.getElementById("concesion_tramites").value = "";
                f_RenderConcesion(datos);
                seRecuperoVehiculoDesdeIP = 0;
                //****************************************************************************************************/
                //*Enviando Toast de Exito en Recuperación la Información de la Concesión
                //****************************************************************************************************/
                sendToast(
                  "INFORMACIÓN DE LA CONCESIÓN RECUPERADA EXITOSAMENTE",
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
                //******************************************/
                //* Inicio: Modalidad de Entrada I = INSERT
                //******************************************/
                modalidadDeEntrada = "I";
                //*********************************************************************************/
                //* Pocisionandose en la parte superior de la pantalla                            */
                //*********************************************************************************/
                window.scrollTo(0, 0);
                //************************************/
                //* Pocisionandose en el VIN         */
                //************************************/
                var el = document.getElementById("vin");
                if (el != null) {
                  el.focus();
                }

                const unidad = datos[1]?.[0]?.["Unidad"]?.[0];
                var html = "";

                //*console.log(unidad,'unidad previo html');

                if (unidad?.["Multas"]?.[0]) {
                  html = mallaDinamica(
                    {
                      titulo:
                        "CERTIFICADO Y/O UNIDAD(NORMAL O SALE) TIENEN MULTAS PENDIENTES DE PAGO",
                      name: "MULTAS",
                    },
                    unidad["Multas"]
                  );
                }

                if (unidad?.["Multas1"]?.[0]) {
                  html =
                    html +
                    mallaDinamica(
                      {
                        titulo:
                          "CERTIFICADO Y/O UNIDAD(ENTRA)  TIENEN MULTAS PENDIENTES DE PAGO",
                        name: "MULTAS",
                      },
                      unidad["Multas1"]
                    );
                }

                if (unidad?.["Preforma"]?.[0]) {
                  console.log(unidad["Preforma"]);
                  html =
                    html +
                    mallaDinamica(
                      {
                        titulo: "PREFORMAS PENDIENTES DE RESOLUCIÓN",
                        name: "PREFORMA",
                      },
                      unidad["Preforma"]
                    );
                }

                if (unidad?.["Placas"]?.[0]) {
                  html =
                    html +
                    mallaDinamica(
                      {
                        titulo:
                          "CERTIFICADO Y/O UNIDADES TIENEN DOCUMENTOS PARA IMPRESIÓN Y/O ENTREGA",
                        name: "DOCUMENTOS/EXPEDIENTES",
                      },
                      unidad["Placas"]
                    );
                }

                if (html != "") {
                  fSweetAlertEventNormal(
                    "INFORMACIÓN",
                    "FAVOR REVISAR INFORMACIÓN",
                    "warning",
                    html
                  );
                }
              }
            }
          }
        } else {
          if (datos[1]?.error !== undefined) {
            fLimpiarPantalla();
            fSweetAlertEventSelect(
              event,
              datos[1].errorhead,
              datos[1].error + "- " + datos[1].errormsg,
              "error"
            );
            isError = true;
          }
        }
      } else {
        fSweetAlertEventSelect(
          event,
          "OOPS",
          "TENEMOS INCONVENIENTES PARA PROCESAR SU PETICIÓN, INTENTENLO EN UN RATO, SI EL PROBLEMA PERSISTE CONTACTE A INFOTECNOLOGIA",
          "error"
        );
        isError = true;
      }
      //*********************************************************************************************/
      //* error: Si hubo error realiza el prevent default y marca con error el id de donde vino     */
      //*********************************************************************************************/
      if (isError == true) {
        event.preventDefault();
        event.target.classList.add("text-error");
        var label = document.getElementById(event.target.id + "label");
        if (label != null) {
          document
            .getElementById(event.target.id + "label")
            .classList.add("errorlabel");
        }
        // document.getElementById("btnconcesion").style = "display:none;";
        // document.getElementById("btnmultas").style = "display:none;";
        // document.getElementById("btnconsultas").style = "display:none;";
        // document.getElementById("btnperexp").style = "display:none;";
      }
      //*****************************************************************************************/
      //* INICIO: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
      //*****************************************************************************************/
      loading(false,currentstep);
      //*****************************************************************************************/
      //* FINAL: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
      //*****************************************************************************************/
    })
    .catch((error) => {
      //*****************************************************************************************/
      //* INICIO: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
      //*****************************************************************************************/
      loading(false,currentstep);
      //*****************************************************************************************/
      //* FINAL: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
      //*****************************************************************************************/
      console.log("f_FetchCallConcesion catch " + error);
      fSweetAlertEventSelect(
        event,
        "CONEXÍON",
        "ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA",
        "warning"
      );
      event.preventDefault();
      event.target.classList.add("text-error");
      var label = document.getElementById(event.target.id + "label");
      if (label != null) {
        document
          .getElementById(event.target.id + "label")
          .classList.add("errorlabel");
      }
      isError = true;
    });
}
//**************************************************************************************/
//*Inicio: Editando la informacion de las concesiones ya registradas en el expediente
//**************************************************************************************/
function fEditarConcesion(idConcesion) {
  //*****************************************************************************************/
  //* INICIO: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
  //*****************************************************************************************/
  loading(true,currentstep);
  //*****************************************************************************************/
  //* FINAL: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
  //*****************************************************************************************/
  var datos;
  var response;
  // URL del Punto de Acceso a la API
  const url = $appcfg_Dominio + "Api_Ram.php";
  //  Fetch options
  // const options = {
  //   method: 'POST',
  //   body: fd,
  //   headers: {
  //     'Content-Type': 'application/json',
  //   },
  // };
  let fd = new FormData(document.forms.form1);
  //Adjuntando el action al FormData
  fd.append("action", "get-concesion-preforma");
  fd.append("RAM", document.getElementById("RAM").value);
  fd.append("idConcesion", idConcesion);
  // Fetch options
  const options = {
    method: "POST",
    body: fd,
  };
  // Hace la solicitud fetch con un timeout de 2 minutos
  fetchWithTimeout(url, options, 120000)
    .then((response) => response.json())
    .then(function (datos) {
      if (typeof datos != "undefined" && typeof datos[0] != "undefined") {
        if (datos[0] > 0) {
          //**************************************************************************************************************************/
          //* Si el vehiculo fue recuperado desde el IP, favor continuar con el proceso normal y mover al informacion a la pantalla
          //**************************************************************************************************************************/
          if (
            typeof datos[1][0]["Unidad"] == "undefined" ||
            typeof datos[1][0]["Unidad"][0] == "undefined" ||
            typeof datos[1][0]["Unidad"][0]["Bloqueado"] == "undefined" ||
            datos[1][0]["Unidad"][0]["Bloqueado"] == false
          ) {
            if (
              typeof datos[1][0]["Unidad"] == "undefined" ||
              typeof datos[1][0]["Unidad"][0] == "undefined" ||
              typeof datos[1][0]["Unidad"][0]["Multas"] == "undefined" ||
              typeof datos[1][0]["Unidad"][0]["Multas"][0] == "undefined"
            ) {
              //***********************************************************************************/
              //**Presentando la tabla de tramites                                                */
              //***********************************************************************************/
              document.getElementById("btnSalvarConcesion").style =
                "display:fixed;";
              document.getElementById("concesion_tramites").style =
                "display:fixed;";
              document.getElementById("div-vista-1").style = "display:fixed;";
              document.getElementById("concesion_tramites").value = "";
              f_RenderConcesionPreforma(datos);
              //**************************************************************************************************************************/
              //*Enviando Toast de Exito en Recuperación la Información de la Concesión
              //**************************************************************************************************************************/
              sendToast(
                "INFORMACIÓN DE LA CONCESIÓN RECUPERADA EXITOSAMENTE DE PREFORMA",
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
              //**************************************************************************************************************************/
              //* Inicio: Modalidad de Entrada U = UPDATE
              //**************************************************************************************************************************/
              modalidadDeEntrada = "U";
              //**************************************************************************************************************************/
              //* Final: Modalidad de Entrada U = UPDATE
              //**************************************************************************************************************************/
              if (
                typeof datos[1][0] != "undefined" &&
                typeof datos[1][0]["Unidad"] != "undefined" &&
                typeof datos[1][0]["Unidad"][0] != "undefined" &&
                typeof datos[1][0]["Unidad"][0]["Preforma"] != "undefined" &&
                typeof datos[1][0]["Unidad"][0]["Preforma"][0] != "undefined"
              ) {
                var html = "";
                html = mallaDinamica(
                  {
                    titulo: "PREFORMAS PENDIENTES DE RESOLUCIÓN",
                    name: "PREFORMA",
                  },
                  datos[1][0]["Unidad"][0]["Preforma"]
                );

                if (html != "") {
                  fSweetAlertSelect(
                    "INFORMACIÓN",
                    "FAVOR REVISAR CUIDADOSAMENTE LA INFORMACIÓN",
                    "warning",
                    html
                  );
                }
              }
              //*********************************************************************************/
              //* Pocisionandose en el checkbox que corresponde según sea la Clase de Servicio  */
              //*********************************************************************************/
              window.scrollTo(0, 0);
              //*********************************************************************************/
              //* Pocisionandose en el checkbox que corresponde según sea la Clase de Servicio  */
              //*********************************************************************************/
              if (claseDeServicio == "STPP" || claseDeServicio == "STPC") {
                var el = document.getElementById("IHTTTRA-02_CLATRA-01_R_PE");
                if (el != null) {
                  el.focus();
                }
              } else {
                var el = document.getElementById("IHTTTRA-02_CLATRA-03_R_PS");
                if (el != null) {
                  el.focus();
                }
              }
            } else {
              var html = mallaDinamica(
                { titulo: "LISTADO DE MULTAS", name: "MULTAS" },
                datos[1][0]["Unidad"][0]["Multas"]
              );
              fSweetAlertSelect(
                "VALIDACIONES",
                "LA UNIDAD TIENE MULTA(S) PENDIENTE(S) DE PAGO, FAVOR PAGAR LAS MULTAS PREVIO A INGRESAR EL TRAMITE",
                "error",
                html
              );
              isError = true;
              document.getElementById("btnSalvarConcesion").style.display =
                "flex";
              // document.getElementById("btnconcesion").style = "display:none;";
              // document.getElementById("btnmultas").style = "display:none;";
              // document.getElementById("btnconsultas").style = "display:none;";
              // document.getElementById("btnperexp").style = "display:none;";
            }
          }
        }
      } else {
        if (typeof datos.error != "undefined") {
          fLimpiarPantalla();
          fSweetAlertSelect(
            datos.errorhead,
            datos.error + "- " + datos.errormsg,
            "error"
          );
        } else {
          fLimpiarPantalla();
          fSweetAlertSelect(
            "INFORMACIÓN",
            "ERROR DESCONOCIDO, INTENTELO DE NUEVO EN UN MOMENTO, SI EL ERROR PERSISTE CONTACTE AL ADMINISTRADOR DEL SISTEMA",
            "error"
          );
        }
      }
      //*****************************************************************************************/
      //* INICIO: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
      //*****************************************************************************************/
      loading(false,currentstep);
      //*****************************************************************************************/
      //* FINAL: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
      //*****************************************************************************************/
    })
    .catch((error) => {
      //*****************************************************************************************/
      //* INICIO: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
      //*****************************************************************************************/
      loading(false,currentstep);
      //*****************************************************************************************/
      //* FINAL: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
      //*****************************************************************************************/
      console.log("error fEditarConcesion(idConcesion) " + error);
      fSweetAlertEventNormal(
        "OPPS",
        "ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTE AL ADMINISTRADOR DEL SISTEMA",
        "error"
      );
    });
}
//**************************************************************************************/
//*Final: Editando la informacion de las concesiones ya registradas en el expediente
//**************************************************************************************/

//*********************************************************************************************************************/
//** Inicio Function Carga Los Datos de la Unidad1 y Presenta la Pantalla con dicha información
//*********************************************************************************************************************/
function f_RenderConcesionPreforma(datos) {
  //***********************************************************************************************************************************/
  //* Inicio: Se ubica en la pantalla 2 de Concesiones en el caso de estar en otra pantalla
  //***********************************************************************************************************************************/
  if (currentstep != 2) {
    fVieneFuncionEditarConcesion = true;
    stepperForm.to(3);
  }
  //***********************************************************************************************************************************/
  //* Final: Se ubica en la pantalla 2 de Concesiones en el caso de estar en otra pantalla
  //***********************************************************************************************************************************/

  var concesionlabel = document.getElementById("concesionlabel");
  if (concesionlabel != null) {
    document.getElementById("concesionlabel").innerHTML =
      datos[1][0]["Tipo_Concesion"];
  }

  claseDeServicio = datos[1][0]["ID_Clase_Servico"];
  if (claseDeServicio == "STPP" || claseDeServicio == "STPC") {
    esCertificado = true;
    if (claseDeServicio == "STPC") {
      esCarga = true;
    } else {
      esCarga = false;
    }
    document.getElementById("Concesion_Encriptada").value =
      datos[1][0]["CertificadoEncriptado"];
    document.getElementById("Permiso_Explotacion_Encriptado").value =
      datos[1][0]["Permiso_Explotacion_Encriptado"];
    document.getElementById("concesion_perexp").innerHTML =
      datos[1][0]["N_Permiso_Explotacion"];
    document.getElementById("Permiso_Explotacion").value =
      datos[1][0]["N_Permiso_Explotacion"];
  } else {
    esCertificado = false;
    if (claseDeServicio == "STEC") {
      esCarga = true;
    } else {
      esCarga = false;
    }
    document.getElementById("Concesion_Encriptada").value =
      datos[1][0]["PermisoEspecialEncriptado"];
    document.getElementById("Permiso_Explotacion").value = "";
    document.getElementById("Permiso_Explotacion_Encriptado").value = "";
  }

  document.getElementById("concesion_tramites").innerHTML =
    datos[1][0]["Tramites"];

  //*************************************************************************************************************/
  //* Llamar funcion que habilita los Listener de las Placas en el caso de cambio de placa y/o cambio de unidad
  //*************************************************************************************************************/
  setaddEventListener();

  document.getElementById("concesion_concesion").innerHTML =
    datos[1][0]["N_Certificado"];
  document.getElementById("concesion_fecven").innerHTML =
    datos[1][0]["Fecha Vencimiento Certificado"];
  document.getElementById("concesion_nombreconcesionario").innerHTML =
    datos[1][0]["NombreSolicitante"];
  document.getElementById("concesion_rtn").innerHTML =
    datos[1][0]["RTN_Concesionario"];
  document.getElementById("concesion_fecexp").innerHTML =
    datos[1][0]["Fecha Emision Certificado"];
  document.getElementById("concesion_resolucion").innerHTML =
    datos[1][0]["Resolucion"];
  if (
    datos.length > 1 &&
    datos[1].length > 0 &&
    datos[1][0]["Unidad"] &&
    datos[1][0]["Unidad"].length > 0
  ) {
    document.getElementById("concesion_nombre_propietario").innerHTML =
      datos[1][0]["Unidad"][0]["Nombre_Propietario"];
    document.getElementById("concesion_identidad_propietario").innerHTML =
      datos[1][0]["Unidad"][0]["RTN_Propietario"];
    if (
      datos[1] &&
      datos[1][0] &&
      datos[1][0]["Unidad"] &&
      datos[1][0]["Unidad"][0] &&
      datos[1][0]["Unidad"][0]["ID_Placa_Antes_Replaqueo"]
    ) {
      document.getElementById("concesion_placaanterior").style =
        "display:inline;";
      document.getElementById("concesion_placaanterior").innerHTML =
        datos[1][0]["Unidad"][0]["ID_Placa_Antes_Replaqueo"];
    } else {
      document.getElementById("concesion_placaanterior").style =
        "display:none;";
      document.getElementById("concesion_placaanterior").innerHTML = "";
    }

    //***********************************************************************************************************************************/
    //* Inicio: Configuración tramites Obligatorios
    //***********************************************************************************************************************************/
    document.getElementById("RequiereRenovacionConcesion").value = false;
    document.getElementById("RequiereRenovacionPerExp").value = false;
    //***********************************************************************************************************************/
    //* INICIO: Se pago el cambio de placa de la unidad actual, esto se hace cuando se detecta que la undiad cambio de placa
    //* y no se encuentra en nuestros registros el pago de ese cambio de placa
    //***********************************************************************************************************************/
    requiereCambioDePlaca = false;
    document.getElementById("estaPagadoElCambiodePlaca").value =
      datos[1][0]["Unidad"][0]["estaPagadoElCambiodePlaca"];
    if (document.getElementById("IHTTTRA-03_CLATRA-15_M_CL").checked == true) {
      if (document.getElementById("estaPagadoElCambiodePlaca").value == false) {
        document.getElementById("IHTTTRA-03_CLATRA-15_M_CL").disabled = true;
        document
          .getElementById("concesion_tramite_placa_CL")
          .removeAttribute("readonly");
        requiereCambioDePlaca = true;
      }

      document.getElementById("concesion_tramite_placa_CL").style =
        "display:flex;text-transform: uppercase;";
      document.getElementById("concesion_tramite_placa_CL").value =
        datos[1][0]["Unidad"][0]["ID_Placa"];
      if (requiereCambioDePlaca == true) {
        document
          .getElementById("concesion_tramite_placa_CL")
          .setAttribute("readonly", true);
      }
      esCambioDePlaca = true;
    } else {
      esCambioDePlaca = false;
      document.getElementById("IHTTTRA-03_CLATRA-15_M_CL").disabled = false;
      document.getElementById("concesion_tramite_placa_CL").style =
        "display:none;text-transform: uppercase;";
      document
        .getElementById("concesion_tramite_placa_CL")
        .removeAttribute("readonly");
    }

    //***********************************************************************************************************************/
    //* INICIO: Si el certificado no esta vigente y requiere renovacion
    //***********************************************************************************************************************/
    if (datos[1][0]["Vencimientos"]["renovacion_certificado_vencido"] == true) {
      document.getElementById("RequiereRenovacionConcesion").value = true;
      document.getElementById("IHTTTRA-02_CLATRA-02_R_CO").checked = true;
      document.getElementById("IHTTTRA-02_CLATRA-02_R_CO").disabled = true;
      if (datos[1][0]["Vencimientos"]["rencon-cantidad"] > 0) {
        document.getElementById("concesion_fecven").innerHTML =
          "  " +
          datos[1][0]["Vencimientos"]["Fecha_Expiracion"] +
          " ==> " +
          datos[1][0]["Vencimientos"]["Nueva_Fecha_Expiracion"] +
          "   (" +
          datos[1][0]["Vencimientos"]["rencon-cantidad"] +
          ")";
      }
      //***********************************************************************************************************************/
      //*Final Ocultar Linea de Reimpresión de Certificado
      //***********************************************************************************************************************/
    } else {
      //***********************************************************************************************************************/
      //* FINAL: Si el certificado no esta vigente y requiere renovacion
      //***********************************************************************************************************************/

      //***********************************************************************************************************************/
      //* INICIO: Si el permiso especial no esta vigencia y requiere renovacion
      //***********************************************************************************************************************/
      if (
        datos[1][0]["Vencimientos"]["renovacion_permiso_especial_vencido"] ==
        true
      ) {
        document.getElementById("RequiereRenovacionConcesion").value = true;
        document.getElementById("IHTTTRA-02_CLATRA-03_R_PS").checked = true;
        document.getElementById("IHTTTRA-02_CLATRA-03_R_PS").disabled = true;
      }
    }
    //***********************************************************************************************************************/
    //* FINAL: Si el permiso especial no esta vigente y requiere renovacion
    //***********************************************************************************************************************/

    //***********************************************************************************************************************/
    //* INICIO: Si el permiso EXPLOTACION no esta vigente y requiere renovacion
    //***********************************************************************************************************************/
    document.getElementById("RequiereRenovacionPerExp").value =
      datos[1][0]["Vencimientos"]["renovacion_permisoexplotacion_vencido"];
    if (
      datos[1][0]["Vencimientos"]["renovacion_permisoexplotacion_vencido"] ==
      true
    ) {
      document.getElementById("IHTTTRA-02_CLATRA-01_R_PE").checked = true;
      document.getElementById("IHTTTRA-02_CLATRA-01_R_PE").disabled = true;
      if (datos[1][0]["Vencimientos"]["renper-explotacion-cantidad"] > 0) {
        document.getElementById("concesion_perexp").innerHTML =
          document.getElementById("concesion_perexp").innerHTML +
          " ||  " +
          datos[1][0]["Vencimientos"]["Fecha_Expiracion_Explotacion"] +
          " ==> " +
          datos[1][0]["Vencimientos"]["Nueva_Fecha_Expiracion_Explotacion"] +
          "   (" +
          datos[1][0]["Vencimientos"]["renper-explotacion-cantidad"] +
          ")";
      }
    }
    //***********************************************************************************************************************/
    //* FINAL: Si el permiso EXPLOTACION no esta vigente y requiere renovacion
    //***********************************************************************************************************************/
    //***********************************************************************************************************************************/
    //* Final: Configuración tramites  Obligatorios
    //***********************************************************************************************************************************/
    //***********************************************************************************************************************************/
    //* INICIO: Caracterización de la Concesion
    //***********************************************************************************************************************************/
    document.getElementById("ID_Categoria").value = datos[1][0]["ID_Categoria"];
    document.getElementById("ID_Tipo_Servicio").value =
      datos[1][0]["ID_Tipo_Servico"];
    document.getElementById("ID_Modalidad").value = datos[1][0]["ID_Modalidad"];
    document.getElementById("ID_Clase_Servicio").value =
      datos[1][0]["ID_Clase_Servico"];
    //***********************************************************************************************************************************/
    //* FINAL: Caracterización de la Concesion
    //***********************************************************************************************************************************/

    //***********************************************************************************************************************************/
    //* INICIO: Fechas y Cantidad de Renovaciones por Concesion y Permiso de Explotación
    //***********************************************************************************************************************************/
    /*       document.getElementById("NuevaFechaVencimientoConcesion").value =
        datos[1][0]["Vencimientos"]["Nueva_Fecha_Expiracion"];
      document.getElementById("NuevaFechaVencimientoPerExp").value =
        datos[1][0]["Vencimientos"]["Nueva_Fecha_Expiracion_Explotacion"];
      document.getElementById("CantidadRenovacionesConcesion").value =
        datos[1][0]["Vencimientos"]["rencon-cantidad"];
      document.getElementById("CantidadRenovacionesPerExp").value =
        datos[1][0]["Vencimientos"]["renper-explotacion-cantidad"]; */

    document.getElementById("NuevaFechaVencimientoConcesion").value =
      datos[1][0]["Vencimientos"]["Nueva_Fecha_Expiracion"];
    document.getElementById("FechaVencimientoConcesion").value =
      datos[1][0]["Vencimientos"]["Fecha_Expiracion"];
    document.getElementById("NuevaFechaVencimientoPerExp").value =
      datos[1][0]["Vencimientos"]["Nueva_Fecha_Expiracion_Explotacion"];
    document.getElementById("FechaVencimientoPerExp").value =
      datos[1][0]["Vencimientos"]["Fecha_Expiracion_Explotacion"];
    document.getElementById("CantidadRenovacionesConcesion").value =
      datos[1][0]["Vencimientos"]["rencon-cantidad"];
    document.getElementById("CantidadRenovacionesPerExp").value =
      datos[1][0]["Vencimientos"]["renper-explotacion-cantidad"];

    console.log(
      document.getElementById("NuevaFechaVencimientoConcesion").value,
      "NuevaFechaVencimientoConcesion"
    );
    console.log(
      document.getElementById("FechaVencimientoConcesion").value,
      "FechaVencimientoConcesion"
    );
    console.log(
      document.getElementById("NuevaFechaVencimientoPerExp").value,
      "NuevaFechaVencimientoPerExp"
    );
    console.log(
      document.getElementById("FechaVencimientoPerExp").value,
      "FechaVencimientoPerExp"
    );
    console.log(
      document.getElementById("CantidadRenovacionesConcesion").value,
      "CantidadRenovacionesConcesion"
    );
    console.log(
      document.getElementById("CantidadRenovacionesPerExp").value,
      "CantidadRenovacionesPerExp"
    );

    //***********************************************************************************************************************************/
    //* FINAL: Fechas y Cantidad de Renovaciones por Concesion y Permiso de Explotación
    //***********************************************************************************************************************************/

    //***********************************************************************************************************************************/
    //* INICIO: Asignación campos de la unidad (segun sea el caso) a objetos html
    //***********************************************************************************************************************************/
    document.getElementById("combustible").value =
      datos[1][0]["Unidad"][0]["Combustible"];
    document.getElementById("ID_Unidad").value = datos[1][0]["Unidad"][0]["ID"];
    document.getElementById("concesion_vin").value =
      datos[1][0]["Unidad"][0]["VIN"];
    document.getElementById("concesion_vin").value =
      datos[1][0]["Unidad"][0]["VIN"];
    document.getElementById("concesion_placa").value =
      datos[1][0]["Unidad"][0]["ID_Placa"];
    document.getElementById("concesion_serie").value =
      datos[1][0]["Unidad"][0]["Chasis"];
    document.getElementById("concesion_motor").value =
      datos[1][0]["Unidad"][0]["Motor"];
    document.getElementById("concesion_tipo_vehiculo").value =
      datos[1][0]["Unidad"][0]["Tipo"];
    document.getElementById("concesion_modelo_vehiculo").value =
      datos[1][0]["Unidad"][0]["Modelo"];
    document.getElementById("capacidad").value =
      datos[1][0]["Unidad"][0]["Capacidad_Carga"];
    document.getElementById("alto").value = datos[1][0]["Unidad"][0]["Alto"];
    document.getElementById("largo").value = datos[1][0]["Unidad"][0]["Largo"];
    document.getElementById("ancho").value = datos[1][0]["Unidad"][0]["Ancho"];
    dataConcesion["marcas"] = datos[1][0]["Marcas"];
    fLlenarSelect(
      "marcas",
      datos[1][0]["Marcas"],
      datos[1][0]["Unidad"][0]["ID_Marca"],
      false,
      { text: "SELECCIONE UNA MARCA", value: "-1" }
    );
    dataConcesion["colores"] = datos[1][0]["Colores"];
    fLlenarSelect(
      "colores",
      datos[1][0]["Colores"],
      datos[1][0]["Unidad"][0]["ID_Color"],
      false,
      { text: "SELECCIONE UN COLOR", value: "-1" }
    );
    dataConcesion["anios"] = datos[1][0]["Anios"];
    fLlenarSelect(
      "anios",
      datos[1][0]["Anios"],
      datos[1][0]["Unidad"][0]["Anio"],
      false,
      { text: "SELECCIONE UN AÑO", value: "-1" }
    );

    document.getElementById("concesion_tipovehiculo").innerHTML =
      datos[1][0]["Unidad"][0]["DESC_Tipo_Vehiculo"];
    //***********************************************************************************************************************************/
    //* INICIO: Asignación campos de la unidad (segun sea el caso) a objetos html
    //***********************************************************************************************************************************/
  }
  document.getElementById("concesion_cerant").innerHTML =
    datos[1][0]["Certificado Anterior"];
  document.getElementById("concesion_numregant").innerHTML =
    datos[1][0]["Registro_Anterior"];
  document.getElementById("concesion_numeroregistro").innerHTML =
    datos[1][0]["Numero_Registro"];
  document.getElementById("concesion_categoria").innerHTML =
    datos[1][0]["DESC_Categoria"];
  // document.getElementById("btnconcesion").style = "display:inline;";
  // document.getElementById("btnmultas").style = "display:inline;";
  // document.getElementById("btnconsultas").style = "display:inline;";
  // document.getElementById("btnperexp").style = "display:inline;";
  document.getElementById("concesion_vin").focus();

  //***********************************************************************************************************************************/
  //* INICIO: Desplegando información de unidad que entra y ocultando la unidad que sale
  //***********************************************************************************************************************************/
  document.getElementById("idVistaSTPC2").style = "display:none;";
  document.getElementById("idVistaSTPC1").style = "display:fixed;";
  document.getElementById("btnCambiarUnidad").style.display = "none";
  //***********************************************************************************************************************************/
  //* Final: Desplegando información de unidad que entra y ocultando la unidad que sale
  //***********************************************************************************************************************************/

  //***********************************************************************************************************************************/
  //* INICIO: Cargando Información en la Pantalla 2 de Vehiculo Entra (Por si Hay cambio de Unidad)
  //***********************************************************************************************************************************/
  document.getElementById("idVistaSTPC2").style = "display:none;";
  document.getElementById("btnCambiarUnidad").style.display = "none";
  document.getElementById("idVistaSTPC1").style = "display:fixed;";
  esCambioDeVehiculo = false;
  seRecuperoVehiculoDesdeIP = 0;
  if (
    datos.length > 1 &&
    datos[1].length > 0 &&
    datos[1][0]["Unidad"] &&
    datos[1][0]["Unidad"].length > 1
  ) {
    f_RenderConcesionTramitesPreforma(datos);
  }
}

function f_RenderConcesionTramitesPreforma(datos) {
  var concesionlabel = document.getElementById("concesion1label");
  if (concesionlabel != null) {
    concesionlabel.innerHTML =
      document.getElementById("concesionlabel").innerHTML;
  }
  document.getElementById("concesion1_concesion").innerHTML =
    document.getElementById("concesion_concesion").innerHTML;
  document.getElementById("concesion1_perexp").innerHTML =
    document.getElementById("concesion_perexp").innerHTML;
  document.getElementById("concesion1_fecexp").innerHTML =
    document.getElementById("concesion_fecexp").innerHTML;
  document.getElementById("concesion1_resolucion").innerHTML =
    document.getElementById("concesion_resolucion").innerHTML;
  document.getElementById("concesion1_fecven").innerHTML =
    document.getElementById("concesion_fecven").innerHTML;
  document.getElementById("concesion1_nombreconcesionario").innerHTML =
    document.getElementById("concesion_nombreconcesionario").innerHTML;
  document.getElementById("concesion1_rtn").innerHTML =
    document.getElementById("concesion_rtn").innerHTML;
  document.getElementById("concesion1_resolucion").innerHTML =
    document.getElementById("concesion1_resolucion").innerHTML;
  //***********************************************************************************************************************************/
  //* INICIO: Asignación campos de la unidad (segun sea el caso) a objetos html
  //***********************************************************************************************************************************/
  document.getElementById("concesion1_nombre_propietario").innerHTML =
    document.getElementById("concesion_nombre_propietario").innerHTML;
  document.getElementById("concesion1_identidad_propietario").innerHTML =
    document.getElementById("concesion_identidad_propietario").innerHTML;

  if (document.getElementById("concesion_placaanterior").innerHTML != "") {
    document.getElementById("concesion1_placaanterior").innerHTML =
      document.getElementById("concesion_placaanterior").innerHTML;
    document.getElementById("concesion1_placaanterior").style =
      "display:inline;";
  } else {
    document.getElementById("concesion1_placaanterior").style = "display:none;";
    document.getElementById("concesion1_placaanterior").innerHTML = "";
  }

  document.getElementById("concesion1_tipo_vehiculo").value =
    document.getElementById("concesion_tipo_vehiculo").value;
  document.getElementById("concesion1_modelo_vehiculo").value =
    document.getElementById("concesion_modelo_vehiculo").value;
  document.getElementById("concesion1_tipovehiculo").innerHTML =
    document.getElementById("concesion_tipovehiculo").innerHTML;

  document.getElementById("combustible1").value =
    datos[1][0]["Unidad"][1]["Combustible"];
  document.getElementById("ID_Unidad1").value = datos[1][0]["Unidad"][1]["ID"];
  document.getElementById("concesion1_vin").value =
    datos[1][0]["Unidad"][1]["VIN"];
  document.getElementById("concesion1_placa").value =
    datos[1][0]["Unidad"][1]["ID_Placa"];
  document.getElementById("concesion1_serie").value =
    datos[1][0]["Unidad"][1]["Chasis"];
  document.getElementById("concesion1_motor").value =
    datos[1][0]["Unidad"][1]["Motor"];
  document.getElementById("concesion1_tipo_vehiculo").value =
    datos[1][0]["Unidad"][1]["Tipo"];
  document.getElementById("concesion1_modelo_vehiculo").value =
    datos[1][0]["Unidad"][1]["Modelo"];
  document.getElementById("capacidad1").value =
    datos[1][0]["Unidad"][1]["Capacidad_Carga"];
  document.getElementById("alto1").value = datos[1][0]["Unidad"][1]["Alto"];
  document.getElementById("largo1").value = datos[1][0]["Unidad"][1]["Largo"];
  document.getElementById("ancho1").value = datos[1][0]["Unidad"][1]["Ancho"];
  //***********************************************************************************************************************************/
  //* INICIO: DWD Marcas1
  //***********************************************************************************************************************************/
  dataConcesion["marcas"] = datos[1][0]["Marcas"];
  fLlenarSelect(
    "marcas1",
    datos[1][0]["Marcas"],
    datos[1][0]["Unidad"][1]["ID_Marca"],
    false,
    { text: "SELECCIONE UNA MARCA", value: "-1" }
  );
  //***********************************************************************************************************************************/
  //* INICIO: DWD Colores1
  //***********************************************************************************************************************************/
  dataConcesion["colores"] = datos[1][0]["Colores"];
  fLlenarSelect(
    "colores1",
    datos[1][0]["Colores"],
    datos[1][0]["Unidad"][1]["ID_Color"],
    false,
    { text: "SELECCIONE UN COLOR", value: "-1" }
  );
  //***********************************************************************************************************************************/
  //* INICIO: DWD Anios
  //***********************************************************************************************************************************/
  dataConcesion["anios"] = datos[1][0]["Anios"];
  fLlenarSelect(
    "anios1",
    datos[1][0]["Anios"],
    datos[1][0]["Unidad"][1]["Anio"],
    false,
    { text: "SELECCIONE UN AÑO", value: "-1" }
  );

  document.getElementById("concesion_tipovehiculo").innerHTML =
    datos[1][0]["Unidad"][1]["DESC_Tipo_Vehiculo"];
  //***********************************************************************************************************************************/
  //* INICIO: Asignación campos de la unidad (segun sea el caso) a objetos html
  //***********************************************************************************************************************************/
  document.getElementById("concesion1_cerant").innerHTML =
    document.getElementById("concesion_cerant").innerHTML;
  document.getElementById("concesion1_numregant").innerHTML =
    document.getElementById("concesion_numregant").innerHTML;
  document.getElementById("concesion1_numeroregistro").innerHTML =
    document.getElementById("concesion_numeroregistro").innerHTML;
  document.getElementById("concesion1_categoria").innerHTML =
    document.getElementById("concesion_categoria").innerHTML;
  //***********************************************************************************************************************************/
  //* Final: Asignación campos de la unidad (segun sea el caso) a objetos html
  //***********************************************************************************************************************************/

  //***********************************************************************************************************************************/
  //* INICIO: Desplegando información de unidad que entra y ocultando la unidad que sale
  //***********************************************************************************************************************************/
  document.getElementById("idVistaSTPC2").style = "display:fixed;";
  document.getElementById("idVistaSTPC1").style = "display:none;";
  document.getElementById("btnCambiarUnidad").style =
    "display:flex; position: absolute; top: 195px; right: 25px; padding: 10px;";
  esCambioDeVehiculo = true;
  //***********************************************************************************************************************************/
  //* Final: Desplegando información de unidad que entra y ocultando la unidad que sale
  //***********************************************************************************************************************************/
}
//*********************************************************************************************************************/
//** Final Cuando Se Da Click a Element de AutoComplete de Concesiones
//*********************************************************************************************************************/

//*********************************************************************************************************************/
//** Inicio Cuando Funcion Que Verifica Si la Concesion Ya Fue Agregada a la Solicitud o NO y Tra le Información
//** para que sea agregada o para ser editada desde la preforma
//*********************************************************************************************************************/
function verificar_existencia_dato(concesion, event) {
  function buscar_placa_concesion(concesion) {
    return concesionNumber.some((item) => item.Concesion === concesion);
  }

  let existe = buscar_placa_concesion(concesion);

  if (existe) {
    if (currentstep != 2) {
      stepperForm.to(3);
    }
    fEditarConcesion(concesion);
  } else {
    if (concesion == "") {
      Swal.fire({
        title: "!NO HAY ELEMENTO¡",
        text: "INGRESE UNA CONCESION PARA BUSCAR O INGRESAR",
        icon: "warning",
        confirmButtonText: "OK",
      });
    } else {
      Swal.fire({
        title: `!LA CONCESION " ${concesion} " NO EXISTE¡`,
        text: "¿DESEA AGREGAR LA CONCESIÓN A LA SOLICITUD",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "SÍ",
        cancelButtonText: "CANCELAR",
      }).then((result) => {
        //* si confirma que esta seguro de eliminar llamamos la funcion para que eliminar de la base de datos.
        if (result.isConfirmed) {
          if (currentstep != 2) {
            stepperForm.to(3);
          }
          f_FetchCallConcesion(concesion, event, "input-prefetch");
        }
      });
    }
  }
}
//*********************************************************************************************************************/
//** Inicio Cuando Funcion Que Verifica Si la Concesion Ya Fue Agregada a la Solicitud o NO y Tra le Información
//** para que sea agregada o para ser editada desde la preforma
//*********************************************************************************************************************/

//*********************************************************************************************************************/
//** Inicio Function para Establecer los Codigos de los Tramites                                                       **/
//*********************************************************************************************************************/
function setConcesion() {
  //*********************************************************************************************************************/
  // Si es Certificado Entra Aqui para obtener el CO y PE
  //*********************************************************************************************************************/
  if (esCertificado) {
    var Certificado = document.getElementById("concesion_concesion").innerHTML;
    var Permiso_Explotacion = document
      .getElementById("concesion_perexp")
      .innerHTML.split("||")[0];
    var Permiso_Especial = "";
  } else {
    var Permiso_Especial = document.getElementById(
      "concesion_concesion"
    ).innerHTML;
    var Permiso_Explotacion = "";
    var Certificado = "";
  }
  //*********************************************************************************************************************/
  // Si es Certificado Entra Aqui para establecer el CO y PE
  //*********************************************************************************************************************/
  const ConcesionPreforma = {
    ID_Expediente: document.getElementById("ID_Expediente").value,
    RAM: document.getElementById("RAM").value,
    ID_Solicitud: document.getElementById("ID_Solicitud").value,
    ID_Aviso_Cobro: document.getElementById("ID_AvisoCobro").value,
    Certificado: Certificado,
    Permiso_Explotacion: Permiso_Explotacion,
    Permiso_Especial: Permiso_Especial,
    ID_Categoria: document.getElementById("ID_Categoria").value,
    ID_Tipo_Servico: document.getElementById("ID_Tipo_Servicio").value,
    ID_Modalidad: document.getElementById("ID_Modalidad").value,
    ID_Clase_Servicio: document.getElementById("ID_Clase_Servicio").value,
    esCambioDePlaca: esCambioDePlaca,
    esCambioDeVehiculo: esCambioDeVehiculo,
    esCertificado: esCertificado,
    Secuencia: document.getElementById("Secuencia").value,
    esCarga: esCarga,
    finalizarSalvado: finalizarSalvado,
  };
  return ConcesionPreforma;
}
//*********************************************************************************************************************/
//** Final Function para Establecer los Codigos de los Tramites                                                        **/
//*********************************************************************************************************************/
//*********************************************************************************************************************/
//** Inicio Function para Establecer el Apoderado de los Tramites                                                           **/
//*********************************************************************************************************************/
function setApoderado() {
  const ApoderadoPreforma = {
    ID_Apoderado: document.getElementById("ID_Apoderado").value,
    RTN: document.getElementById("identidadapod").value,
    Numero_Colegiacion: document.getElementById("colapoderado").value,
    Lugar_Entrega: document.getElementById("entregadocs").value,
    Telefono: document.getElementById("telapoderado").value,
    Email: document.getElementById("emailapoderado").value,
    Nombre: document.getElementById("nomapoderado").value,
    Identidad: document.getElementById("identidadapod").value,
    Direccion: document.getElementById("dirapoderado").value,
    Tipo_Presentacion: document.getElementById("tipopresentacion").value,
  };
  return ApoderadoPreforma;
}
//*********************************************************************************************************************/
//** Final Function para Establecer el Apoderado de los Tramites                                                     **/
//*********************************************************************************************************************/
//*********************************************************************************************************************/
//** Inicio Function para Establecer el Solicitante de los Tramites                                                  **/
//*********************************************************************************************************************/
function setSolicitante() {
  const SolicitantePreforma = {
    ID_Solicitante: document.getElementById("ID_Solicitante").value,
    RTN: document.getElementById("rtnsoli").value,
    Tipo_Solicitante: document
      .getElementById("tiposolicitante")
      .getAttribute("data-id"),
    Nombre: document.getElementById("nomsoli").value,
    Domicilio: document.getElementById("domiciliosoli").value,
    Denominacion: document.getElementById("denominacionsoli").value,
    Departamento: document.getElementById("Departamentos").value,
    Municipio: document.getElementById("Municipios").value,
    Aldea: document.getElementById("Aldeas").value,
    Telefono: document.getElementById("telsoli").value,
    Email: document.getElementById("emailsoli").value,
  };
  return SolicitantePreforma;
}
//*********************************************************************************************************************/
//** Final Function para Establecer el Solicitante de los Tramites                                                   **/
//*********************************************************************************************************************/
//*********************************************************************************************************************/
//** Inicio Function para Establecer la Unidad de los Tramites                                                       **/
//*********************************************************************************************************************/
function setUnidad() {
  const UnidadPreforma = {
    ID_Unidad: document.getElementById("ID_Unidad").value,
    VIN: document.getElementById("concesion_vin").value,
    Placa: document.getElementById("concesion_placa").value,
    Serie: document.getElementById("concesion_serie").value,
    Motor: document.getElementById("concesion_motor").value,
    Marca: document.getElementById("marcas").value,
    Color: document.getElementById("colores").value,
    Anio: document.getElementById("anios").value,
    Combustible: document.getElementById("combustible").value,
    Capacidad: document.getElementById("capacidad").value,
    Alto: document.getElementById("alto").value,
    Largo: document.getElementById("largo").value,
    Ancho: document.getElementById("ancho").value,
    Nombre_Propietario: document.getElementById("concesion_nombre_propietario")
      .innerHTML,
    RTN_Propietario: document.getElementById("concesion_identidad_propietario")
      .innerHTML,
    Modelo: document.getElementById("concesion_modelo_vehiculo").value,
    Tipo: document.getElementById("concesion_tipo_vehiculo").value,
    ID_Placa_Antes_Replaqueo: document.getElementById("concesion_placaanterior")
      .innerHTML,
  };
  return UnidadPreforma;
}
//*********************************************************************************************************************/
//** Final Function para Establecer la Unidad de los Tramites                                                        **/
//*********************************************************************************************************************/
//*********************************************************************************************************************/
//** Inicio Function para Establecer la Unidad de los Tramites                                                       **/
//*********************************************************************************************************************/
function setUnidad1() {
  const Unidad1Preforma = {
    ID_Unidad: document.getElementById("ID_Unidad1").value,
    VIN: document.getElementById("concesion1_vin").value,
    Placa: document.getElementById("concesion1_placa").value,
    Serie: document.getElementById("concesion1_serie").value,
    Motor: document.getElementById("concesion1_motor").value,
    Marca: document.getElementById("marcas1").value,
    Color: document.getElementById("colores1").value,
    Anio: document.getElementById("anios1").value,
    Combustible: document.getElementById("combustible1").value,
    Capacidad: document.getElementById("capacidad1").value,
    Alto: document.getElementById("alto1").value,
    Largo: document.getElementById("largo1").value,
    Ancho: document.getElementById("ancho1").value,
    Nombre_Propietario: document.getElementById("concesion1_nombre_propietario")
      .innerHTML,
    RTN_Propietario: document.getElementById("concesion1_identidad_propietario")
      .innerHTML,
    Modelo: document.getElementById("concesion1_modelo_vehiculo").value,
    Tipo: document.getElementById("concesion1_tipo_vehiculo").value,
    ID_Placa_Antes_Replaqueo: document.getElementById(
      "concesion1_placaanterior"
    ).innerHTML,
  };
  return Unidad1Preforma;
}
//*********************************************************************************************************************/
//** Final Function para Establecer la Unidad de los Tramites                                                        **/
//*********************************************************************************************************************/
//*********************************************************************************************************************/
//** Inicio Function para Establecer los Codigos de los Tramites                                                       **/
//*********************************************************************************************************************/
function setTramites() {
  //*********************************************************************************************************************/
  // Si es Certificado Entra Aqui para establecer el CO y PE
  //*********************************************************************************************************************/
  const TramitesPreforma = [];
  const chkTramites = document.querySelectorAll('input[name="tramites[]"]');
  if (chkTramites) {
    chkTramites.forEach(function (chk) {
      if (chk.checked) {
        let Cantidad_Vencimientos = 1;
        let Fecha_Expiracion = "";
        let Fecha_Expiracion_Nueva = "";

        if (chk.id === "IHTTTRA-02_CLATRA-01_R_PE") {
          Cantidad_Vencimientos = document.getElementById(
            "CantidadRenovacionesPerExp"
          ).value;
          Fecha_Expiracion_Nueva = document.getElementById(
            "NuevaFechaVencimientoPerExp"
          ).value;
          Fecha_Expiracion = document.getElementById(
            "NuevaFechaVencimientoPerExp"
          ).value;
        } else {
          if (
            chk.id === "IHTTTRA-02_CLATRA-02_R_CO" ||
            chk.id === "IHTTTRA-02_CLATRA-02_R_PS"
          ) {
            console.log(
              document.getElementById("CantidadRenovacionesConcesion").value,
              "CantidadRenovacionesConcesion Setramites"
            );
            console.log(
              document.getElementById("NuevaFechaVencimientoConcesion").value,
              "NuevaFechaVencimientoConcesion Setramites"
            );
            console.log(
              document.getElementById("FechaVencimientoConcesion").value,
              "FechaVencimientoConcesion Setramites"
            );

            Cantidad_Vencimientos = document.getElementById(
              "CantidadRenovacionesConcesion"
            ).value;
            Fecha_Expiracion_Nueva = document.getElementById(
              "NuevaFechaVencimientoConcesion"
            ).value;
            Fecha_Expiracion = document.getElementById(
              "FechaVencimientoConcesion"
            ).value;
          }
        }

        TramitesPreforma.push({
          ID: getAttribute(chk, "data-iddb", false),
          ID_Compuesto: chk.id,
          Codigo: chk.value,
          descripcion: document.getElementById("descripcion_" + chk.value)
            .innerHTML,
          ID_Tramite: chk.getAttribute("data-id"),
          Monto: chk.getAttribute("data-monto"),
          Total_A_Pagar: parseFloat(
            parseFloat(chk.getAttribute("data-monto")).toFixed(2) *
            Cantidad_Vencimientos
          ).toFixed(2),
          Cantidad_Vencimientos: Cantidad_Vencimientos,
          Fecha_Expiracion: Fecha_Expiracion,
          Fecha_Expiracion_Nueva: Fecha_Expiracion_Nueva,
          ID_Categoria: document.getElementById("ID_Categoria").value,
          ID_Tipo_Servicio: document.getElementById("ID_Tipo_Servicio").value,
          ID_Modalidad: document.getElementById("ID_Modalidad").value,
          ID_Clase_Servico: document.getElementById("ID_Clase_Servicio").value,
        });
      }
    });
  }
  return TramitesPreforma;
}
//*********************************************************************************************************************/
//** Final Function para Establecer los Codigos de los Tramites                                                       **/
//*********************************************************************************************************************/
//*********************************************************************************************************/
//* Inicio: Creando objeto de concesion desde Datos de Preformas
//*********************************************************************************************************/
function guardarConcesionSalvadaPreforma(Tramites, Unidades) {
  var index = 0;
  var Concesion = "";
  var Concesion_Encriptada;
  var Permiso_Explotacion_Encriptado;
  var esCarga;
  var esCertificado;
  var Placa = "";
  var Permiso_Explotacion = "";
  var ID_Formulario_Solicitud = "";
  var TramitesPreforma = Array();
  var index = 1;
  var Unidad1 = "";
  //*********************************************************************************************************/
  //* Inicio: Recorriendo arreglo de concesiones y tramites
  //*********************************************************************************************************/
  Tramites.forEach((row) => {
    //*********************************************************************************************************/
    //* La primera vez que entra llena la variable Concesion
    //*********************************************************************************************************/
    if (index == 1) {
      if (row["N_Permiso_Explotacion"] != "") {
        Concesion = row["N_Certificado"];
        Concesion_Encriptada = row["CertificadoEncriptado"];
        Permiso_Explotacion = row["N_Permiso_Explotacion"];
        Permiso_Explotacion_Encriptado = row["Permiso_Explotacion_Encriptado"];
      } else {
        Concesion = row["N_Certificado"];
        Concesion_Encriptada = row["PermisoEspecialEncriptado"];
        Permiso_Explotacion = "";
        Permiso_Explotacion_Encriptado = "";
      }
    }

    if (Concesion == row["N_Certificado"]) {
      esCarga = Boolean(row["esCarga"]);
      esCertificado = Boolean(row["esCertificado"]);
      Placa = row["ID_Placa"];
      var Cantidad_Vencimientos = 1;
      var Fecha_Expiracion_Nueva = "";
      var Fecha_Expiracion = "";
      if (
        row["ID_CHECK"] == "IHTTTRA-02_CLATRA-01_R_PE" &&
        row["Vencimientos"] != false
      ) {
        Fecha_Expiracion = row["Fecha_Expiracion_Explotacion"];
        Cantidad_Vencimientos =
          row["Vencimientos"]["renper-explotacion-cantidad"];
        Fecha_Expiracion_Nueva =
          row["Vencimientos"]["Nueva_Fecha_Expiracion_Explotacion"];
      } else {
        if (
          (row["ID_CHECK"] == "IHTTTRA-02_CLATRA-02_R_CO" ||
            row["ID_CHECK"] == "IHTTTRA-02_CLATRA-02_R_PS") &&
          row["Vencimientos"] != false
        ) {
          Fecha_Expiracion = row["Fecha_Expiracion"];
          Cantidad_Vencimientos = row["Vencimientos"]["rencon-cantidad"];
          Fecha_Expiracion_Nueva =
            row["Vencimientos"]["Nueva_Fecha_Expiracion"];
        }
      }

      ID_Formulario_Solicitud = row["ID_Formulario_Solicitud"];
      TramitesPreforma.push({
        ID: row["ID"],
        ID_Compuesto: row["ID_CHECK"],
        Codigo: row["ID_Tramite"],
        descripcion: row["DESC_Tipo_Tramite"] + " " + row["DESC_Clase_Tramite"],
        ID_Tramite: row["ID_Tramite"],
        Monto: row["Monto"],
        Total_A_Pagar: parseFloat(
          parseFloat(row["Monto"]).toFixed(2) * Cantidad_Vencimientos
        ).toFixed(2),
        Cantidad_Vencimientos: Cantidad_Vencimientos,
        Fecha_Expiracion: Fecha_Expiracion,
        Fecha_Expiracion_Nueva: Fecha_Expiracion_Nueva,
        ID_Categoria: row["ID_Tipo_Categoria"],
        ID_Tipo_Servicio: row["ID_TIpo_Servicio"],
        ID_Modalidad: row["ID_Modalidad"],
        ID_Clase_Servico: row["ID_Clase_Servicio"],
      });
      //*************************************************************/
      //* Si trae Unidad 1
      //*************************************************************/
      Unidad1 = Unidades[Concesion]?.[1] ?? "";
      if (Tramites.length == index) {
        currentConcesionIndex = updateCollection(Concesion);
        concesionNumber[currentConcesionIndex] = {
          esCarga: esCarga,
          esCertificado: esCertificado,
          Concesion_Encriptada: Concesion_Encriptada,
          Concesion: Concesion,
          Permiso_Explotacion_Encriptado: Permiso_Explotacion_Encriptado,
          Permiso_Explotacion: Permiso_Explotacion,
          ID_Expediente: "",
          ID_Solicitud: "",
          ID_Formulario_Solicitud: ID_Formulario_Solicitud,
          CodigoAvisoCobro: "",
          ID_Resolucion: "",
          Placa: Placa,
          Unidad: Unidades[Concesion]?.[0] ?? "",
          Unidad1: Unidad1,
          Tramites: TramitesPreforma,
        };
        //***********************************************************************/
        //* Agregando concesion pura */
        //***********************************************************************/
        addElementToAutoComplete(Concesion, Concesion);
        //***********************************************************************/
        //* Agregando concesion con permiso de explotacion */
        //***********************************************************************/
        if (Permiso_Explotacion != "") {
          addElementToAutoComplete(
            Concesion,
            Permiso_Explotacion + " => " + Concesion
          );
        }
        //***********************************************************************/
        //* Agregando placa actual asociada a concesion */
        //***********************************************************************/
        if (Unidades[Concesion]?.[0]?.ID_Placa != null) {
          addElementToAutoComplete(
            Concesion,
            Unidades[Concesion][0].ID_Placa + " => " + Concesion
          );
        }
        //***********************************************************************/
        //* Agregando placa Anterior Asociada a concesion */
        //***********************************************************************/
        if (
          Unidades[Concesion]?.[0]?.ID_Placa_Antes_Replaqueo != null &&
          Unidades[Concesion]?.[0]?.ID_Placa != null &&
          Unidades[Concesion][0].ID_Placa_Antes_Replaqueo !==
          Unidades[Concesion][0].ID_Placa
        ) {
          addElementToAutoComplete(
            Concesion,
            Unidades[Concesion][0].ID_Placa_Antes_Replaqueo + " => " + Concesion
          );
        }
        if (Unidad1 != "" && Unidad1.ID_Placa != "undefined") {
          //***********************************************************************/
          //* Agregando placa actual asociada a concesion */
          //***********************************************************************/
          addElementToAutoComplete(
            Concesion,
            Unidad1.ID_Placa + " => " + Concesion
          );
          //***********************************************************************/
          //* Agregando placa Anterior Asociada a concesion */
          //***********************************************************************/
          if (
            Unidad1?.ID_Placa_Antes_Replaqueo != null &&
            Unidad1?.ID_Placa != null &&
            Unidad1.ID_Placa_Antes_Replaqueo !== Unidad1.ID_Placa
          ) {
            addElementToAutoComplete(
              Concesion,
              Unidad1.ID_Placa_Antes_Replaqueo + " => " + Concesion
            );
          }
          //***********************************************************************/
        }
      }
    } else {
      //*************************************************************/
      //* Si trae Unidad 1
      //*************************************************************/
      Unidad1 = Unidades[Concesion]?.[1] ?? "";
      //**********************************************************************************************************************/
      //*Agregando la concesión al arreglo de indice de concesiones y recuperando el indice de la concesion                  */
      //**********************************************************************************************************************/
      currentConcesionIndex = updateCollection(Concesion);
      concesionNumber[currentConcesionIndex] = {
        esCarga: esCarga,
        esCertificado: esCertificado,
        Concesion_Encriptada: Concesion_Encriptada,
        Concesion: Concesion,
        Permiso_Explotacion_Encriptado: Permiso_Explotacion_Encriptado,
        Permiso_Explotacion: Permiso_Explotacion,
        ID_Expediente: "",
        ID_Solicitud: "",
        ID_Formulario_Solicitud: ID_Formulario_Solicitud,
        CodigoAvisoCobro: "",
        ID_Resolucion: "",
        Placa: Placa,
        Unidad: Unidades[Concesion]?.[0] ?? "",
        Unidad1: Unidad1,
        Tramites: TramitesPreforma,
      };
      //***********************************************************************/
      //* Agregando concesion pura */
      //***********************************************************************/
      addElementToAutoComplete(Concesion, Concesion);
      //***********************************************************************/
      //* Agregando concesion con permiso de explotacion */
      //***********************************************************************/
      if (Permiso_Explotacion != "") {
        addElementToAutoComplete(
          Concesion,
          Permiso_Explotacion + " => " + Concesion
        );
      }
      //***********************************************************************/
      //* Agregando placa actual asociada a concesion */
      //***********************************************************************/
      if (Unidades[Concesion]?.[0]?.ID_Placa != null) {
        addElementToAutoComplete(
          Concesion,
          Unidades[Concesion][0].ID_Placa + " => " + Concesion
        );
      }
      //***********************************************************************/
      //* Agregando placa Anterior Asociada a concesion */
      //***********************************************************************/
      if (
        Unidades[Concesion]?.[0]?.ID_Placa_Antes_Replaqueo != null &&
        Unidades[Concesion]?.[0]?.ID_Placa != null &&
        Unidades[Concesion][0].ID_Placa_Antes_Replaqueo !==
        Unidades[Concesion][0].ID_Placa
      ) {
        addElementToAutoComplete(
          Concesion,
          Unidades[Concesion][0].ID_Placa_Antes_Replaqueo + " => " + Concesion
        );
      }
      if (Unidad1 != "" && Unidad1.ID_Placa != "undefined") {
        //***********************************************************************/
        //* Agregando placa actual asociada a concesion */
        //***********************************************************************/
        addElementToAutoComplete(
          Concesion,
          Unidad1.ID_Placa + " => " + Concesion
        );
        //***********************************************************************/
        //* Agregando placa Anterior Asociada a concesion */
        //***********************************************************************/
        if (
          Unidad1?.ID_Placa_Antes_Replaqueo != null &&
          Unidad1?.ID_Placa != null &&
          Unidad1.ID_Placa_Antes_Replaqueo !== Unidad1.ID_Placa
        ) {
          addElementToAutoComplete(
            Concesion,
            Unidad1.ID_Placa_Antes_Replaqueo + " => " + Concesion
          );
        }
        //***********************************************************************/
      }

      if (row["N_Permiso_Especial"] == "") {
        Concesion = row["N_Certificado"];
        Concesion_Encriptada = row["CertificadoEncriptado"];
        Permiso_Explotacion = row["Permiso_Explotacion"];
        Permiso_Explotacion_Encriptado = row["Permiso_Explotacion_Encriptado"];
      } else {
        Concesion = row["N_Permiso_Especial"];
        Concesion_Encriptada = row["PermisoEspecialEncriptado"];
        Permiso_Explotacion = "";
      }

      //**********************************************************************************************************************/
      //*Agregando la concesión al arreglo de indice de concesiones y recuperando el indice de la concesion                  */
      //**********************************************************************************************************************/
      /*         if (row['ID_Placa1'] != undefined && row['ID_Placa1'] != '' && row['ID_Placa1'] != null) {
          Placa = row['ID_Placa'] + '->' + row['ID_Placa1'];
        } else {
          Placa = row['ID_Placa'];
        } */

      Placa = row["ID_Placa"];
      let Cantidad_Vencimientos = 1;
      let Fecha_Expiracion_Nueva = "";
      let Fecha_Expiracion = "";
      if (
        row["ID_CHECK"] == "IHTTTRA-02_CLATRA-01_R_PE" &&
        row["Vencimientos"] != false
      ) {
        Fecha_Expiracion = row["Fecha_Expiracion_Explotacion"];
        Cantidad_Vencimientos =
          row["Vencimientos"]["renper-explotacion-cantidad"];
        Fecha_Expiracion_Nueva =
          row["Vencimientos"]["Nueva_Fecha_Expiracion_Explotacion"];
      } else {
        if (
          (row["ID_CHECK"] == "IHTTTRA-02_CLATRA-02_R_CO" ||
            row["ID_CHECK"] == "IHTTTRA-02_CLATRA-02_R_PS") &&
          row["Vencimientos"] != false
        ) {
          Fecha_Expiracion = row["Fecha_Expiracion"];
          Cantidad_Vencimientos = row["Vencimientos"]["rencon-cantidad"];
          Fecha_Expiracion_Nueva =
            row["Vencimientos"]["Nueva_Fecha_Expiracion"];
        }
      }

      ID_Formulario_Solicitud = row["ID_Formulario_Solicitud"];
      TramitesPreforma = [];
      TramitesPreforma.push({
        ID: row["ID"],
        ID_Compuesto: row["ID_CHECK"],
        Codigo: row["ID_Tramite"],
        descripcion: row["DESC_Tipo_Tramite"] + " " + row["DESC_Clase_Tramite"],
        ID_Tramite: row["ID_Tramite"],
        Monto: row["Monto"],
        Total_A_Pagar: parseFloat(
          parseFloat(row["Monto"]).toFixed(2) * Cantidad_Vencimientos
        ).toFixed(2),
        Cantidad_Vencimientos: Cantidad_Vencimientos,
        Fecha_Expiracion: Fecha_Expiracion,
        Fecha_Expiracion_Nueva: Fecha_Expiracion_Nueva,
        ID_Categoria: row["ID_Tipo_Categoria"],
        ID_Tipo_Servicio: row["ID_TIpo_Servicio"],
        ID_Modalidad: row["ID_Modalidad"],
        ID_Clase_Servico: row["ID_Clase_Servicio"],
      });
      //*************************************************************/
      //* Si trae Unidad 1
      //*************************************************************/
      Unidad1 = Unidades[Concesion]?.[1] ?? "";
      if (Tramites.length == index) {
        currentConcesionIndex = updateCollection(Concesion);
        concesionNumber[currentConcesionIndex] = {
          esCarga: esCarga,
          esCertificado: esCertificado,
          Concesion_Encriptada: Concesion_Encriptada,
          Concesion: Concesion,
          Permiso_Explotacion_Encriptado: Permiso_Explotacion_Encriptado,
          Permiso_Explotacion: Permiso_Explotacion,
          ID_Expediente: "",
          ID_Solicitud: "",
          ID_Formulario_Solicitud: ID_Formulario_Solicitud,
          CodigoAvisoCobro: "",
          ID_Resolucion: "",
          Placa: Placa,
          Unidad: Unidades[Concesion]?.[0] ?? "",
          Unidad1: Unidad1,
          Tramites: TramitesPreforma,
        };
        //***********************************************************************/
        //* Agregando concesion pura */
        //***********************************************************************/
        addElementToAutoComplete(Concesion, Concesion);
        //***********************************************************************/
        //* Agregando concesion con permiso de explotacion */
        //***********************************************************************/
        if (Permiso_Explotacion != "") {
          addElementToAutoComplete(
            Concesion,
            Permiso_Explotacion + " => " + Concesion
          );
        }
        //***********************************************************************/
        //* Agregando placa actual asociada a concesion */
        //***********************************************************************/
        if (Unidades[Concesion]?.[0]?.ID_Placa != null) {
          addElementToAutoComplete(
            Concesion,
            Unidades[Concesion][0].ID_Placa + " => " + Concesion
          );
        }
        //***********************************************************************/
        //* Agregando placa Anterior Asociada a concesion */
        //***********************************************************************/
        if (
          Unidades[Concesion]?.[0]?.ID_Placa_Antes_Replaqueo != null &&
          Unidades[Concesion]?.[0]?.ID_Placa != null &&
          Unidades[Concesion][0].ID_Placa_Antes_Replaqueo !==
          Unidades[Concesion][0].ID_Placa
        ) {
          addElementToAutoComplete(
            Concesion,
            Unidades[Concesion][0].ID_Placa_Antes_Replaqueo + " => " + Concesion
          );
        }
        if (Unidad1 != "" && Unidad1.ID_Placa != "undefined") {
          //***********************************************************************/
          //* Agregando placa actual asociada a concesion */
          //***********************************************************************/
          addElementToAutoComplete(
            Concesion,
            Unidad1.ID_Placa + " => " + Concesion
          );
          //***********************************************************************/
          //* Agregando placa Anterior Asociada a concesion */
          //***********************************************************************/
          if (
            Unidad1?.ID_Placa_Antes_Replaqueo != null &&
            Unidad1?.ID_Placa != null &&
            Unidad1.ID_Placa_Antes_Replaqueo !== Unidad1.ID_Placa
          ) {
            addElementToAutoComplete(
              Concesion,
              Unidad1.ID_Placa_Antes_Replaqueo + " => " + Concesion
            );
          }
          //***********************************************************************/
        }
      }
    }
    index++;
  });
  //**********************************************************************************************************************/
  //* Llamando a funcion que habilita el AutoComplete                                                                    */
  //**********************************************************************************************************************/
  fAutoComplete();
}
//*********************************************************************************************************/
//* Final: Creando objeto de concesion desde Datos de Preformas
//*********************************************************************************************************/
//*********************************************************************************************************/
//* Inicio: Creando objeto de concesion
//*********************************************************************************************************/
function guardarConcesionSalvada(Tramites, Unidad, Unidad1) {
  if (Unidad1 == false) {
    Unidad1 = "";
  }
  //**********************************************************************************************************************/
  //*Agregando la concesión al arreglo de indice de concesiones y recuperando el indice de la concesion                  */
  //**********************************************************************************************************************/
  currentConcesionIndex = updateCollection(
    document.getElementById("concesion_concesion").innerHTML
  );
  //**********************************************************************************************************************/
  //* Si es la primera vez que se recupera la concesion se guardar el objeto con la concesion                            */
  //**********************************************************************************************************************/
  // Recuperando el valor de la Permisos de Explotación
  var perexpobj = document.getElementById("concesion_perexp");
  var PerExp = "";
  if (perexpobj != null) {
    PerExp = perexpobj.innerHTML;
  }

  //if (document.getElementById("concesion_placa").value != document.getElementById("concesion1_placa").value && document.getElementById("concesion1_placa").value != '') {
  //  var Placa = document.getElementById("concesion_placa").value + '->' + document.getElementById("concesion1_placa").value;
  //} else {
  //  var Placa = document.getElementById("concesion_placa").value;
  //}

  concesionNumber[currentConcesionIndex] = {
    esCarga: esCarga,
    esCertificado: esCertificado,
    Concesion_Encriptada: document.getElementById("Concesion_Encriptada").value,
    Concesion: document.getElementById("concesion_concesion").innerHTML,
    Permiso_Explotacion_Encriptado: document.getElementById(
      "Permiso_Explotacion_Encriptado"
    ).value,
    Permiso_Explotacion: PerExp,
    ID_Expediente: document.getElementById("ID_Expediente").value,
    ID_Solicitud: document.getElementById("ID_Solicitud").value,
    ID_Formulario_Solicitud: document.getElementById("RAM").value,
    CodigoAvisoCobro: document.getElementById("ID_AvisoCobro").value,
    ID_Resolucion: document.getElementById("ID_Resolucion").value,
    Placa: document.getElementById("concesion_placa").value.toUpperCase(),
    Unidad: Unidad,
    Unidad1: Unidad1,
    Tramites: Tramites,
  };
  //***********************************************************************/
  //* Agregando concesion pura */
  //***********************************************************************/
  addElementToAutoComplete(
    document.getElementById("concesion_concesion").innerHTML,
    document.getElementById("concesion_concesion").innerHTML
  );
  //***********************************************************************/
  //* Agregando concesion con permiso explotacion */
  //***********************************************************************/
  if (PerExp != "") {
    addElementToAutoComplete(
      document.getElementById("concesion_concesion").innerHTML,
      PerExp + " => " + document.getElementById("concesion_concesion").innerHTML
    );
  }
  //***********************************************************************/
  //* Agregando placa actual asociada a concesion */
  //***********************************************************************/
  addElementToAutoComplete(
    document.getElementById("concesion_concesion").innerHTML,
    Unidad.Placa +
    " => " +
    document.getElementById("concesion_concesion").innerHTML
  );
  //***********************************************************************/
  //* Agregando placa Anterior Asociada a concesion */
  //***********************************************************************/
  if (Unidad.ID_Placa_Antes_Replaqueo != Unidad.Placa) {
    addElementToAutoComplete(
      document.getElementById("concesion_concesion").innerHTML,
      Unidad.ID_Placa_Antes_Replaqueo +
      " => " +
      document.getElementById("concesion_concesion").innerHTML
    );
  }
  if (Unidad1 != null && Unidad1 != "" && Unidad1.Placa != "undefined") {
    //***********************************************************************/
    //* Agregando placa actual asociada a concesion */
    //***********************************************************************/
    addElementToAutoComplete(
      document.getElementById("concesion_concesion").innerHTML,
      Unidad1.Placa +
      " => " +
      document.getElementById("concesion_concesion").innerHTML
    );
    //***********************************************************************/
    //* Agregando placa Anterior Asociada a concesion */
    //***********************************************************************/
    if (Unidad1.ID_Placa_Antes_Replaqueo != Unidad1.Placa) {
      addElementToAutoComplete(
        document.getElementById("concesion_concesion").innerHTML,
        Unidad1.ID_Placa_Antes_Replaqueo +
        " => " +
        document.getElementById("concesion_concesion").innerHTML
      );
    }
    //***********************************************************************/
  }
  //**********************************************************************************************************************/
  //* Llamando a funcion que habilita el AutoComplete                                                                     */
  //**********************************************************************************************************************/
  fAutoComplete();
}
//*********************************************************************************************************************/
//** Final Function para Establecer los Codigos de los Tramites                                                        **/
//*********************************************************************************************************************/
//*********************************************************************************************************************/
//** Inicio Function para Salvar Los Requicitos
//*********************************************************************************************************************/
function salvarRequicitos() {
  // URL del Punto de Acceso a la API
  const url = $appcfg_Dominio + "Api_Ram.php";
  let fd = new FormData(document.forms.form1);
  var Tramites = "";
  // Adjuntando el action al FormData
  fd.append("action", "save-requisitos");
  // Adjuntando el Concesion y Caracterización al FormData
  const checkboxes = document.querySelectorAll(
    'input[name="flexSwitchCheck[]"]:checked'
  );
  if (checkboxes != null && checkboxes.length > 10) {
    // Seleccionar todos los checkboxes que tienen el nombre 'flexSwitchCheck[]'
    const selectedValues = Array.from(checkboxes).map(
      (checkbox) => checkbox.value
    );
    fd.append("Requisitos", JSON.stringify(selectedValues));
    fd.append("RAM", document.getElementById("RAM").value);
    //  Fetch options
    const options = {
      method: "POST",
      body: fd,
    };
    // Hacer al solicitud fetch con un timeout de 2 minutos
    fetchWithTimeout(url, options, 120000)
      .then((response) => response.json())
      .then(function (Datos) {
        if (typeof Datos.ERROR != "undefined") {
          sendToast(
            "ERROR SALVANDO LOS REQUISITOS, INTENTELO NUEVAMENTE SI EL ERROR PERSISTE FAVOR CONTACTAR AL ADMINISTRADOR DEL SISTEMA",
            $appcfg_milisegundos_toast,
            "",
            true,
            true,
            "top",
            "right",
            true,
            $appcfg_background_toast,
            function () { },
            "error",
            $appcfg_pocision_toast,
            $appcfg_icono_toast
          );
          return true;
        } else {
          document.getElementById("concesion").value = "";
          //****************************************************************************************************/
          sendToast(
            "REQUICITOS PRE-FORMA SALVADDOS EXITOSAMENTE",
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
          return false;
        } // final del If de si Hay error
      })
      .catch((error) => {
        console.log("salvarRequicitos() catch error" + error);
        fSweetAlertEventSelect(
          "",
          "CONEXÍON",
          "ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA",
          "warning"
        );
        return true;
      });
  } else {
    sendToast(
      "DEBE SELECCIONAR TODOS REQUISITOS CORRESPONDIENTES PARA PODER SALVAR LA INFORMACIÓN",
      $appcfg_milisegundos_toast,
      "",
      true,
      true,
      "top",
      "right",
      true,
      $appcfg_background_toast,
      function () { },
      "error",
      $appcfg_pocision_toast,
      $appcfg_icono_toast
    );
    return true;
  }
}
//*********************************************************************************************************************/
//** Final Function para Salvar La Preforma                                                                          **/
//*********************************************************************************************************************/
//*********************************************************************************************************************/
//** Inicio Function para Salvar La Preforma                                                                         **/
//*********************************************************************************************************************/
function salvarConcesion() {
  // URL del Punto de Acceso a la API
  const url = $appcfg_Dominio + "Api_Ram.php";
  let fd = new FormData(document.forms.form1);
  var Tramites = "";
  var Unidad = "";
  var Unidad1 = "";
  // Adjuntando el action al FormData
  if (document.getElementById("ID_Expediente").value == "") {
    fd.append("action", "save-preforma");
  } else {
    fd.append("action", "save-expediente");
    // Enviar el número de Expediente
    fd.append("ID_Expediente", document.getElementById("ID_Expediente").value);
    // Enviar el número de Solicitud
    fd.append("ID_Solicitud", document.getElementById("ID_Solicitud").value);
  }
  // Modalidad de entrada de la data (I=INSERT, U-UPDATE)
  fd.append("modalidadDeEntrada", modalidadDeEntrada);
  // Adjuntando el Concesion y Caracterización al FormData
  fd.append("Concesion", JSON.stringify(setConcesion()));
  // Adjuntando el Apoderado al FormData
  fd.append("Apoderado", JSON.stringify(setApoderado()));
  // Adjuntando el Solicitante al FormData
  fd.append("Solicitante", JSON.stringify(setSolicitante()));
  // Adjuntando el Unidad al FormData
  Unidad = setUnidad();
  fd.append("Unidad", JSON.stringify(Unidad));
  // Adjuntando el Unidad1 al FormData Solo si Existe el Tramite Cambio de Unidad
  if (esCambioDeVehiculo == true) {
    Unidad1 = setUnidad1();
    fd.append("Unidad1", JSON.stringify(Unidad1));
  }
  // Adjuntando el Tramites al FormData
  Tramites = setTramites();
  fd.append("Tramites", JSON.stringify(Tramites));
  //  Fetch options
  const options = {
    method: "POST",
    body: fd,
  };
  // Hacer al solicitud fetch con un timeout de 2 minutos
  fetchWithTimeout(url, options, 120000)
    .then((response) => response.json())
    .then(function (Datos) {
      console.log(Datos, "Datos en salvarConcesion");
      if (
        typeof Datos.ERROR != "undefined" ||
        typeof Datos.error != "undefined"
      ) {
        if (typeof Datos.ERROR != "undefined") {
          if (typeof Datos.Multas != "undefined") {
            var html = "";
            if (
              typeof Datos.Multas === "object" &&
              !Array.isArray(Datos.Multas) &&
              Object.keys(Datos.Multas).length > 0
            ) {
              console.log(Datos.Multas, "Datos.Multas");
              html = mallaDinamica(
                {
                  titulo:
                    "CERTIFICADO Y/O UNIDAD TIENEN MULTAS PENDIENTES DE PAGO",
                  name: "PREFORMA",
                },
                Datos.Multas
              );
              fSweetAlertEventNormal(
                "INFORMACIÓN",
                "CERTIFICADO Y/O UNIDAD TIENEN MULTAS PENDIENTES DE PAGO",
                "info",
                html
              );
            }
          }

          if (typeof Datos.Multas1 != "undefined") {
            if (
              typeof Datos.Multas1 === "object" &&
              !Array.isArray(Datos.Multas1) &&
              Object.keys(Datos.Multas1).length > 0
            ) {
              console.log(Datos.Multas1, "Datos.Multas1");
              html =
                html +
                mallaDinamica(
                  {
                    titulo:
                      "CERTIFICADO Y/O UNIDAD ENTRANTE TIENEN MULTAS PENDIENTES DE PAGO",
                    name: "PREFORMA",
                  },
                  Datos.Multas1
                );
            }
          }

          if (typeof Datos.Placas != "undefined") {
            console.log(Datos.Placas);
            if (
              typeof Datos.Placas === "object" &&
              !Array.isArray(Datos.Placas) &&
              Object.keys(Datos.Placas).length > 0
            ) {
              html =
                html +
                mallaDinamica(
                  {
                    titulo:
                      "CERTIFICADO Y/O UNIDAD ENTRANTE TIENEN DOCUMENTOS PARA IMPRESIÓN Y/O ENTREGA",
                    name: "DOCUMENTOS/EXPEDIENTES",
                  },
                  Datos.Placas
                );
            }
          }

          if (typeof Datos.Preforma != "undefined") {
            if (
              typeof Datos.Preforma === "object" &&
              !Array.isArray(Datos.Preforma) &&
              Object.keys(Datos.Preforma).length > 0
            ) {
              console.log(Datos.Preforma, "Datos.Preforma");
              html =
                html +
                mallaDinamica(
                  {
                    titulo: "PREFORMAS PENDIENTES DE RESOLUCIÓN",
                    name: "PREFORMA",
                  },
                  Datos.Preforma
                );
            }
          }
          fSweetAlertEventNormal(
            "INFORMACIÓN",
            "LA UNIDAD Y/O EL CERTIFICADO PREFORMAS INGRESADAS PENDIENTES DE RESOLUCIÓN",
            "info",
            html
          );
        } else {
          fSweetAlertEventNormal(
            datos.errorhead,
            datos.error + "- " + datos.errormsg,
            "error"
          );
        }
        return true;
      } else {
        //****************************************************************************************************/
        //* INICIO: CODIGO QUE ESTABLECE LA ETIQUETA DE RAM E ID'S DE TABLAS                                  */
        //****************************************************************************************************/
        if (modalidadDeEntrada == "I") {
          //****************************************************************************************************/
          //* Aqui solo se entra la primera vez que se salga una concesion y se genera el RAM
          //****************************************************************************************************/
          if (document.getElementById("RAM-ROTULO").innerHTML == "") {
            document.getElementById("ID_Bitacora").innerHTML = Datos.Bitacora;
            document.getElementById("ID_Solicitante").value =
              Datos.Solicitante.ID_Solicitante;
            document.getElementById("ID_Apoderado").value = Datos.Apoderado;
            document.getElementById("RAM").value = Datos.RAM;
            document.getElementById("RAM-ROTULO").innerHTML =
              "<strong>" + Datos.RAM + "</strong>";
            document.getElementById("RAM-ROTULO").style = "inline-block;";
          }
          //****************************************************************************************************/
          // Aqui se entra siempre porque es lo que se esta cambiando las unidades y los tramites
          //****************************************************************************************************/
          document.getElementById("ID_Unidad").value = Datos.Unidad;
          console.log(Datos.Unidad, " Datos.Unidad");
          Unidad.ID_Unidad = Datos.Unidad;
          console.log(Unidad.ID_Unidad, "Unidad.ID_Unidad ");
          console.log(Datos.Unidad1, "Datos.Unidad1");
          if (Datos.Unidad1 != false) {
            document.getElementById("ID_Unidad1").value = Datos.Unidad1;
            Unidad1.ID_Unidad = Datos.Unidad1;
            console.log(Datos.Unidad1, "Datos.Unidad1");
            console.log(Unidad1.ID_Unidad, "Unidad1.ID_Unidad");
          } else {
            document.getElementById("ID_Unidad1").value = "";
          }
          for (i = 0; i < Tramites.length; i++) {
            Tramites[i].ID = Datos.Tramites[i].ID;
          }
        } else {
          console.log(Datos, "Datos");
          if (Datos.Unidad1 != undefined && Datos.Unidad1 != false) {
            if (document.getElementById("ID_Unidad1").value == "") {
              console.log(
                document.getElementById("ID_Unidad1").value,
                'document.getElementById("ID_Unidad1").value Antes'
              );
              document.getElementById("ID_Unidad1").value = Datos.Unidad1;
              console.log(
                document.getElementById("ID_Unidad1").value,
                'document.getElementById("ID_Unidad1").value Despues'
              );
              console.log(Datos.Unidad1, "Datos.Unidad1");
              Unidad1.ID_Unidad = Datos.Unidad1;
            }
          } else {
            console.log(
              document.getElementById("ID_Unidad1").value,
              'document.getElementById("ID_Unidad1").value Antes  Sin UNidad1'
            );
            document.getElementById("ID_Unidad1").value = "";
            console.log(
              document.getElementById("ID_Unidad1").value,
              'document.getElementById("ID_Unidad1").value Despues Sin UNidad1'
            );
          }
        }
        //****************************************************************************************************/
        //*Ocultando el boton que permite ver las dos unidades cuando hay cambio de unidad
        //****************************************************************************************************/
        document.getElementById("btnCambiarUnidad").style.display = "none";
        //****************************************************************************************************/
        //* FINAL: CODIGO QUE ESTABLECE LA ETIQUETA DE RAM E ID'S DE TABLAS                                  */
        //****************************************************************************************************/
        //****************************************************************************************************/
        //*Llamando funcion para guardar en memoria la concesion salvada                                     */
        //****************************************************************************************************/
        console.log("guardarConcesionSalvada antes");
        guardarConcesionSalvada(Tramites, Unidad, Unidad1);
        console.log("guardarConcesionSalvada despues");
        //****************************************************************************************************/
        //****************************************************************************************************/
        //*Limpiando pantalla e inicializando banderas para preparar el programa para agregar otra concesion */
        //****************************************************************************************************/
        fLimpiarPantalla();
        //****************************************************************************************************/
        esCambioDePlaca = false;
        esCambioDeVehiculo = false;
        seRecuperoVehiculoDesdeIP = 0;
        isVehiculeBlock = false;
        checked = false;
        document.getElementById("concesion").value = "";
        //****************************************************************************************************/
        sendToast(
          "PRE-FORMA SALVADA EXITOSAMENTE",
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
        //setTimeout(showModalConcesiones, 250);
        document.getElementById("btnCambiarUnidad").style = "display:none;";
        document.getElementById("btnSalvarConcesion").style = "display:none;";
        if (concesionNumber.length > 0) {
          document.getElementById("rightDiv").style.display = "flex";
        }
        return false;
      } // final del If de si Hay error
    })
    .catch((error) => {
      console.log("error save-preforma" + error);
      fSweetAlertEventSelect(
        "",
        "CATCH salvarConcesion() ",
        "ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA",
        "warning"
      );
      return true;
    });
}
//*********************************************************************************************************************/
//** Final Function para Salvar La Preforma                                                                          **/
//*********************************************************************************************************************/
function f_CaseFetchCalls(value, event, idinput) {
  //*********************************************************************************/
  // Dependiendo del panel actual se ejecuta una función para validar los campos
  //*********************************************************************************/
  switch (currentstep) {
    case 0:
      f_FetchCallApoderado(value, event, idinput);
      break;
    case 1:
      f_FetchCallSolicitante(value, event, idinput);
      break;
    case 2:
      f_FetchCallConcesion(value, event, idinput);
      break;
    case 3:
      f_FetchCallApoderado(value, event, idinput);
      break;
    case 4:
      f_FetchCallApoderado(value, event, idinput);
      break;
    default:
      f_FetchCallApoderado(value, event, idinput);
      break;
  }
}

function showConcesion() {
  var event = new Event("change", {
    bubbles: true,
    cancelable: true,
  });
  var input = document.getElementById("concesion");
  input.dispatchEvent(event);
  input.focus();
  input.select();
  f_FetchCallConcesion(input.value, event, input.id);
}

//********************************************************************************************/
//Funcion ejecutada por timeout para pocicionar el cursos en el campo concesión
//********************************************************************************************/
function setFocusElementConcesion() {
  document.getElementById("concesion").focus();
  document.getElementById("concesion").select();
}

//**********************************************************************************************/
// Funcion para mover informacion de la pantalla de concesiones a la de tramites
//**********************************************************************************************/
function setDataPreviewFormalities() {
  if (currentstep == 2) {
    f_RenderConcesionTramites();
  }
}

function showModalConcesiones() {
  $("#modalConcesion").modal("show");
}

btnSalvarConcesion.addEventListener("click", function (event) {
  var response = { error: false };
  if (currentstep == 2) {
    //**********************************************************************************************************/
    //**Salvar La Concesion Actual (Certificado de Operación o Permiso Especial)                             ***/
    //**********************************************************************************************************/
    setFocus = false;
    fGetInputs();
    setFocus = true;
    console.log(seRecuperoVehiculoDesdeIP, "seRecuperoVehiculoDesdeIP");
    if (seRecuperoVehiculoDesdeIP == 0 || seRecuperoVehiculoDesdeIP == 3) {
      const sum = paneerror[currentstep].reduce((acc, val) => acc + val, 0);
      console.log(paneerror[currentstep], "panerror");
      console.log(sum, "sum");
      if (sum == 0) {
        console.log("salvarconcesion llamando");
        salvarConcesion();
      } else {
        fSweetAlertEventSelect(
          event,
          "ERRORES",
          "SE HAN DETECTADO ERROR(ES) DE DATOS EN LA PANTALLA FAVOR CORRIJA Y VUELVA A INTENTAR SALVAR",
          "error"
        );
      }
    } else {
      fSweetAlertEventSelect(
        event,
        "SALVANDO",
        "NO SE HA RECUPERADO/SALVADO LA INFORMACIÓN DEL VEHICULO DESDE EL IP, FAVOR RECUPERAR LA INFORMACIÓN DEL VEHICULO ANTES DE SALVAR LA INFORMACIÓN",
        "error"
      );
    }
  } else {
    if (currentstep == 3) {
      salvarRequicitos();
      // response.error = salvarRequicitos();
      // console.log("btnAddConcesion currentstep == 3 response.error: " + response.error);
    }
  }
});

btnAddConcesion.addEventListener("click", function (event) {
  showModalFromShown = true;
  if (currentstep != 2) {
    stepperForm.to(3);
  }
  setTimeout(showModalConcesiones, 100);
});

//Cuando presente el nuevo panel
stepperFormEl.addEventListener("shown.bs-stepper", function (event) {
  currentstep = event.detail.indexStep;
  setFocus = true;
  isError = false;
  isPrevious = false;

  console.log("currentstep on shown: " + currentstep);

  if (typeof isDirty[currentstep] != "undefined") {
    isDirty[currentstep] == false;
  }

  switch (currentstep) {
    case 0:
      if (
        isRecordGetted[2] == "" &&
        document.getElementById("btnAddConcesion").style.display == "flex"
      ) {
        document.getElementById("btnAddConcesion").style = "display:none;";
      }
      document.getElementById("concesion_tramites").style = "display:none;";
      document.getElementById("colapoderado").focus();
      break;
    case 1:
      document.getElementById("concesion_tramites").style = "display:none;";
      document.getElementById("rtnsoli").focus();
      break;
    case 2:
      document.getElementById("input-prefetch").style.display = "block";
      document.getElementById("toggle-icon").style.display = "block";
      if (concesionNumber.length > 0) {
        document.getElementById("rightDiv").style.display = "flex";
      }

      if (fVieneFuncionEditarConcesion == false) {
        document.getElementById("btnSalvarConcesion").style.display = "none";
      } else {
        fVieneFuncionEditarConcesion = false;
      }

      document.getElementById("btnAddConcesion").innerHTML =
        '<i class="fa-solid fa-magnifying-glass"></i>&nbsp;&nbsp;BUSCAR';
      document.getElementById("btnAddConcesion").style = "display:flex;";
      if (showModalFromShown == true) {
        showModalFromShown = false;
        //modalConcesion").modal("show");
        //tTimeout(setFocusElementConcesion, 250);
      } else {
        document.getElementById("concesion_vin").focus();
      }
      document.getElementById("concesion").value = "";
      break;
    case 3:
      document.getElementById("concesion_tramites").style = "display:none;";
      document.getElementById("btnAddConcesion").style = "display:none;";
      if (modalidadDeEntrada == "I") {
        document.getElementById("btnSalvarConcesion").style = "display:fixed;";
      } else {
        document.getElementById("btnSalvarConcesion").style = "display:none;";
      }
      break;
    case 4:
      document.getElementById("concesion_tramites").style = "display:none;";
      document.getElementById("btnAddConcesion").style = "display:none;";
      document.getElementById("btnSalvarConcesion").style = "display:none;";
      break;
  }
});

//Antes de hacer la transición al nuevo panel
stepperFormEl.addEventListener("show.bs-stepper", function (event) {
  //*********************************************************************************/
  // Si va hacia el panel anterior no validar los campos
  //*********************************************************************************/
  if (isPrevious == false) {
    //*********************************************************************************/
    //Reduciendo el arreglo de errores por panel para saber si existe algun error
    //*********************************************************************************/
    const paneactual = paneerror[currentstep];
    const sum = paneactual.reduce((accumulator, currentValue) => {
      return accumulator + currentValue;
    }, 0);
    //*************************************************************************************/
    //Se previene el cambio de panel si hay algun error en el panel actual procesandose
    //************************************************************************************/
    if (sum > 0) {
      event.preventDefault();
    }
  } else {
    isError = false;
  }
});

var ii = 0;
while (ii < stepperPanList.length) {
  var element = document.getElementById("test-form-" + (ii + 1));
  //* Obtener todos los elementos de entrada dentro de este elemento
  var inputselect = element.querySelectorAll(".test-select");
  //* Convertir NodeList a Array para facilitar la manipulación (opcional)
  inputselect = Array.from(inputselect);
  //* Iterar sobre los elementos de entrada y eliminar las clases de error
  inputselect.forEach((input) => {
    //* Definiendo evento change para los elementos select
    input.addEventListener("change", function (event) {
      if (event.target.getAttribute("data-valor") > event.target.value) {
        event.preventDefault();

        event.target.classList.add("text-error");

        var label = document.getElementById(event.target.id + "label");
        if (label != null) {
          label.classList.add("errorlabel");
        }

        let labelerror = document.getElementById(
          event.target.id + "labelerror"
        );
        if (labelerror != null) {
          labelerror.style.visibility = "visible";
        }

        paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
      } else {
        event.target.classList.remove("text-error");

        var label = document.getElementById(event.target.id + "label");
        if (label != null) {
          label.classList.remove("errorlabel");
        }

        let labelerror = document.getElementById(
          event.target.id + "labelerror"
        );
        if (labelerror != null) {
          labelerror.style.visibility = "hidden";
        }

        paneerror[currentstep][idinputs.indexOf(idinput)] = 0;
        //Moverse al siguiente input
        moveToNextInput(input, 0);
        if (event.target.id == "Departamentos") {
          fCargarDwd(
            "get-municipios",
            event.target.value,
            "Municipios",
            -1,
            { text: "SELECCIONE UN MUNICIPIO", value: "-1" },
            ["Aldeas"],
            ['<option selected value="-1">SELECCIONE UNA ALDEA</option>']
          );
        } else {
          if (event.target.id == "Municipios") {
            fCargarDwd("get-aldeas", event.target.value, "Aldeas", -1, {
              text: "SELECCIONE UNA ALDEA",
              value: "-1",
            });
          }
        }
      }
    });
    //Definiendo evento keydown para los elementos select
    input.addEventListener("keydown", function (event) {
      if (event.key === "Tab" || event.key === "Enter") {
        if (event.target.getAttribute("data-valor") > event.target.value) {
          event.preventDefault();
          event.target.classList.add("text-error");
          var label = document.getElementById(event.target.id + "label");

          if (label != null) {
            label.classList.add("errorlabel");
          }

          let labelerror = document.getElementById(
            event.target.id + "labelerror"
          );
          if (labelerror != null) {
            labelerror.style.visibility = "visible";
          }

          paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
        } else {
          event.target.classList.remove("text-error");

          var label = document.getElementById(event.target.id + "label");
          if (label != null) {
            label.classList.remove("errorlabel");
          }

          let labelerror = document.getElementById(
            event.target.id + "labelerror"
          );
          if (labelerror != null) {
            labelerror.style.visibility = "hidden";
          }

          paneerror[currentstep][idinputs.indexOf(idinput)] = 0;
        }
        //Mover al siguiente input
        if (event.key === "Enter") {
          moveToNextInput(input, 0);
        }
      }
    });
  });
  ii++;
}

testinputs.forEach(function (inputtest) {
  idinputs.push(inputtest.id);
  //********************************************************************/
  //Definiendo el arreglo de errores por panel e input
  //********************************************************************/
});

testcontrols.forEach(function (input) {
  //*****************************************************************************/
  //Creando evento change para cada input
  //*****************************************************************************/
  input.addEventListener("change", function (event) {
    // Obteniendo el id del input
    idinput = event.target.id;
    // Salvar la pocisión del cursor
    var cursorPosition = event.target.selectionStart;
    // Obtener el valor actual del input
    var value = event.target.value;
    // Obteniendo el patrón del atributo 'pattern' del elemento objetivo
    var patternString = event.target.pattern;
    // Crear una instancia de RegExp usando el patrón
    var pattern = new RegExp(patternString);
    // Veriricar si el valor actual del input es válido
    var isValid = pattern.test(value);
    let parts = event.target.id.split("_");
    if (
      (parts[0].slice(-1) == 1 && esCambioDeVehiculo == true) ||
      parts[0].slice(-1) != 1
    ) {
      //* If de si el valor es valido para preventDefault
      if (!isValid) {
        event.preventDefault();
        // Pocicionando el cursor en el input actual
        if (setFocus == true) {
          event.target.focus();
          event.target.select();
        }
        event.target.classList.add("text-error");
        var label = document.getElementById(event.target.id + "label");
        if (label != null) {
          label.classList.add("errorlabel");
        }
        var labelerror = document.getElementById(
          event.target.id + "labelerror"
        );
        if (labelerror != null) {
          labelerror.style.visibility = "visible";
        }
        paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
      } else {
        event.target.classList.remove("text-error");

        var label = document.getElementById(event.target.id + "label");
        if (label != null) {
          label.classList.remove("errorlabel");
        }

        let labelerror = document.getElementById(
          event.target.id + "labelerror"
        );
        if (labelerror != null) {
          labelerror.style.visibility = "hidden";
        }

        paneerror[currentstep][idinputs.indexOf(idinput)] = 0;
        // Si son los input colapoderado o rtnsoli o concesion
        if (
          idinput == "colapoderado" ||
          idinput == "rtnsoli" ||
          idinput == "concesion"
        ) {
          if (
            typeof isRecordGetted[currentstep] == "undefined" ||
            isRecordGetted[currentstep] != event.target.value
          ) {
            if (isDirty[currentstep] == false) {
              f_CaseFetchCalls(value, event, idinput);
            } else {
              isDirty[currentstep] == false;
            }
          }
        } else {
          if (isTab == false) {
            moveToNextInput(input, 0);
          }
        }
        isTab = false;
        isFromfGetInputs = false;
      }
    } else {
      paneerror[currentstep][idinputs.indexOf(idinput)] = 0;
    }
  });

  idVista.addEventListener("ondblclick", function (event) {
    alert("hola");
  });

  // Handle the keydown event to prevent Tab key from moving focus
  input.addEventListener("keydown", function (event) {
    // Obteniendo el id del input
    idinput = event.target.id;
    // Verificar si se presionó la tecla Tab o Enter
    if (event.key === "Tab" || event.key === "Enter") {
      // Obtener el valor actual del input
      var value = event.target.value;
      // Obtener el patrón del atributo 'pattern' del elemento objetivo
      var patternString = event.target.pattern;
      // Crear una instancia de RegExp usando el patrón
      var pattern = new RegExp(patternString);
      // Verificar si el valor actual del input es válido
      var isValid = pattern.test(value);
      // Fi de si el valor es valido para preventDefault
      if (!isValid) {
        event.preventDefault();
        // Pocicionando el cursor en el input actual
        if (setFocus == true) {
          event.target.focus();
          event.target.select();
        }
        // RONALD THAOFIC CAMBIO 2025/02/17
        // } else {
        //   setFocus = true;
        // }

        event.target.classList.add("text-error");

        var label = document.getElementById(event.target.id + "label");
        if (label != null) {
          label.classList.add("errorlabel");
        }

        var labelerror = document.getElementById(
          event.target.id + "labelerror"
        );
        if (labelerror != null) {
          labelerror.style.visibility = "visible";
        }

        // Marcando que hay un error en el input actual del panel actual
        paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
      } else {
        event.target.classList.remove("text-error");

        var label = document.getElementById(event.target.id + "label");
        if (label != null) {
          label.classList.remove("errorlabel");
        }

        var labelerror = document.getElementById(
          event.target.id + "labelerror"
        );
        if (labelerror != null) {
          labelerror.style.visibility = "hidden";
        }

        paneerror[currentstep][idinputs.indexOf(idinput)] = 0;
        if (
          idinput == "colapoderado" ||
          idinput == "rtnsoli" ||
          (idinput == "concesion" && event.key === "Enter")
        ) {
          if (
            typeof isRecordGetted[currentstep] == "undefined" ||
            isRecordGetted[currentstep] != event.target.value
          ) {
            f_CaseFetchCalls(value, event, idinput);
            isDirty[currentstep] == true;
          } else {
            //Mover al siguiente input
            if (event.key === "Enter") {
              moveToNextInput(input, 0);
            }
          }
        } else {
          //Mover al siguiente input
          if (event.key === "Enter") {
            moveToNextInput(input, 0);
          }
        }
      }
    }
    // Marcando que no hay error aun
    isError = false;
  });
  isDirty[currentstep] = false;
});

async function callFunctionBorrarTramite(Concesion, ID, Linea, el) {
  const result = await Swal.fire({
    title: "¿ESTÁ SEGURO?",
    text: `¿QUIERE ELIMINAR ESTE TRAMITE DE LA CONCESION No. ${document.getElementById("concesion_concesion").innerHTML
      } ?`,
    icon: "info",
    showCancelButton: true,
    confirmButtonText: "SÍ, BORRAR",
    cancelButtonText: "CANCELAR",
  });
  if (result.isConfirmed) {
    var success = await fEliminarTramite(
      Concesion,
      ID,
      Linea,
      false,
      false,
      el.id,
      document.getElementById("ID_Unidad").value,
      document.getElementById("ID_Unidad1").value
    );
    if (success) {
      console.log("fEliminarTramite retornó true");
      return true;
    } else {
      console.log("fEliminarTramite retornó false");
      return false;
    }
  } else {
    console.log("Usuario canceló la acción en fEliminarTramite");
    return false;
  }
}

//*********************************************************************************************************************/
//** Inicio Function para Establecer los Codigos de los Tramites                                                       **/
//*********************************************************************************************************************/
function setOneTramites(el) {
  //*********************************************************************************************************************/
  // Si es Certificado Entra Aqui para establecer el CO y PE
  //*********************************************************************************************************************/
  const TramitesPreforma = [];
  TramitesPreforma.push({
    ID_Compuesto: el.id,
    Codigo: el.value,
    descripcion: document.getElementById("descripcion_" + el.value).innerHTML,
    ID_Tramite: el.value,
    Monto: el.getAttribute("data-monto"),
    ID_Categoria: document.getElementById("ID_Categoria").value,
    ID_Tipo_Servicio: document.getElementById("ID_Tipo_Servicio").value,
    ID_Modalidad: document.getElementById("ID_Modalidad").value,
    ID_Clase_Servico: document.getElementById("ID_Clase_Servicio").value,
  });
  return TramitesPreforma;
}
//*********************************************************************************************************************/
//** Final Function para Establecer los Codigos de los Tramites                                                      **/
//*********************************************************************************************************************/
//*********************************************************************************************************************/
//** Inicio Function para Salvar Tramite en Preforma                                                                 **/
//*********************************************************************************************************************/
async function addTramitePreforma(el) {
  try {
    // URL del Punto de Acceso a la API
    const url = $appcfg_Dominio + "Api_Ram.php";
    let fd = new FormData(document.forms.form1);
    var Tramites = "";
    var Unidad = null;
    var Unidad1 = null;
    // Adjuntando el action al FormData
    fd.append("action", "add-tramite-preforma");
    // Enviar el número de Expediente
    fd.append("ID_Expediente", document.getElementById("ID_Expediente").value);
    // Enviar el número de Solicitud
    fd.append("ID_Solicitud", document.getElementById("ID_Solicitud").value);
    // Funcion debe hacer echo no retornar
    fd.append("echo", true);
    // Número de RAM
    fd.append("RAM", document.getElementById("RAM").value);
    // Adjuntando el Concesion y Caracterización al FormData
    fd.append("Concesion", JSON.stringify(setConcesion()));
    // Adjuntando el Tramites al FormData
    Tramites = setOneTramites(el);
    fd.append("Tramites", JSON.stringify(Tramites));
    //  Fetch options
    const options = {
      method: "POST",
      body: fd,
    };
    const response = await fetchWithTimeout(url, options, 120000);
    const Datos = await response.json();
    if (Datos.error) {
      fSweetAlertEventNormal(
        Datos.errorhead,
        Datos.error + "- " + Datos.errormsg,
        "error"
      );
      return false;
    } else {
      if (Datos) {
        setAttribute(el, "data-iddb", Datos[0]["ID"]);
        addConcesionNumber(
          Datos[0]["ID"],
          el.id,
          getAttribute(el, "data-monto"),
          document.getElementById("descripcion_" + el.value).innerHTML,
          el.value
        );
        sendToast(
          "TRAMITE EN PREFORMA INSERTADO SATISFACTORIAMENTE" /* ... otros parámetros ... */
        );
        return true;
      } else {
        el.checked = false;
        fSweetAlertEventSelect(
          "",
          "PRECAUCIÓN",
          "ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA",
          "error"
        );
        return false;
      }
    }
  } catch (error) {
    console.log(
      "CATCH addTramitePreforma()",
      "ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA"
    );
    fSweetAlertEventSelect(
      "",
      "CATCH addTramitePreforma()",
      "ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA",
      "warning"
    );
    return false;
  }
}
//*********************************************************************************************************************/
//** Final Function para Salvar Tramite en Preforma
//*********************************************************************************************************************/

//**************************************************************************************/
//* INICIO: Agregar Tramite a Preforma Cuando ya esta salvada la concesion
//**************************************************************************************/
async function addTramite(el) {
  const result = await Swal.fire({
    title: "¿ESTÁ SEGURO?",
    text: `¿QUIERE AGREGAR ESTE TRAMITE A LA CONCESION No. ${document.getElementById("concesion_concesion").innerHTML
      } ?`,
    icon: "info",
    showCancelButton: true,
    confirmButtonText: "SÍ, AGREGAR",
    cancelButtonText: "CANCELAR",
  });
  if (result.isConfirmed) {
    const success = await addTramitePreforma(el);
    if (success) {
      console.log("addTramitePreforma retornó true");
      return true;
    } else {
      console.log("addTramitePreforma retornó false");
      return false;
    }
  } else {
    console.log("Usuario canceló la acción en addTramite");
    return false;
  }
}
//**************************************************************************************/
//* FINAL: Agregar Tramite a Preforma Cuando ya esta salvada la concesion
//**************************************************************************************/
//**************************************************************************************/
//* INICIO: Funcion hidde and show lineas de tramites dependiente del caso
//**************************************************************************************/
function fHiddenShowTramites(
  el,
  acronimo_tipo,
  acronimo_clase,
  checked = true
) {
  const checkboxIds = {
    ["R"]: [
      "IHTTTRA-08_CLATRA-03_X_PS",
      "IHTTTRA-08_CLATRA-02_X_CO",
      "IHTTTRA-08_CLATRA-01_X_PE",
    ],
    ["M"]: ["IHTTTRA-03_CLATRA-08_M_CU"],
    ["M_CU"]: [
      "IHTTTRA-03_CLATRA-15_M_CL",
      "IHTTTRA-03_CLATRA-17_M_CM",
      "IHTTTRA-03_CLATRA-18_M_CC",
      "IHTTTRA-03_CLATRA-19_M_CS",
    ],
    ["X"]: [
      "IHTTTRA-02_CLATRA-02_R_CO",
      "IHTTTRA-02_CLATRA-01_R_PE",
      "IHTTTRA-02_CLATRA-03_R_PS",
    ],
  };

  var display = "flex";
  var checkedItem = true;
  if (checked == true) {
    display = "none";
    checkedItem = false;
  }

  if (acronimo_clase == "CU" || acronimo_clase == "CL") {
    var placa = document.getElementById(
      "concesion_tramite_placa_" + acronimo_clase
    );
    if (placa) {
      if (checked == true) {
        placa.style.display = "flex";
        placa.disabled = false;
      } else {
        placa.style.display = "none";
        if (requiereCambioDePlaca == true && acronimo_clase == "CL") {
          placa.disabled = true;
        } else {
          placa.disabled = false;
        }
      }
    }
  }

  if (acronimo_tipo != "M") {
    console.log(checkboxIds[acronimo_tipo], "checkboxIds");
    for (let id of checkboxIds[acronimo_tipo]) {
      const checkbox = document.getElementById(id);
      if (checkbox && getAttribute(checkbox, "disabled", false) == false) {
        const [tipo_tramite1, clase_tramite1, acronimo_tipo1, acronimo_clase1] =
          id.split("_");
        if (acronimo_clase === acronimo_clase1) {
          checkbox.checked = false;
          const row = "row_tramite_" + acronimo_tipo1 + "_" + acronimo_clase1;
          const elemento = document.getElementById(row);
          if (elemento) {
            elemento.style.display = display;
            break;
          } else {
            console.log(elemento, "row no encontrada");
          }
        }
      }
    }
  } else {
    const acronimo = acronimo_tipo + "_" + acronimo_clase;
    if (acronimo == "M_CU") {
      for (let id of checkboxIds[acronimo]) {
        const checkbox = document.getElementById(id);
        if (
          (checkbox && checkbox.disabled == false && checkedItem == false) ||
          (checkbox &&
            checkbox.checked == false &&
            checkbox.disabled == false &&
            checkedItem == true)
        ) {
          const [
            tipo_tramite1,
            clase_tramite1,
            acronimo_tipo1,
            acronimo_clase1,
          ] = id.split("_");
          if (acronimo_tipo === acronimo_tipo1) {
            checkbox.checked = false;
            const row = "row_tramite_" + acronimo_tipo1 + "_" + acronimo_clase1;
            const elemento = document.getElementById(row);
            if (elemento) {
              elemento.style.display = display;
            } else {
              console.log(elemento, "row no encontrada");
            }
          }
        }
      }
    } else {
      var contador = 0;
      for (let id of checkboxIds["M_CU"]) {
        const checkbox = document.getElementById(id);
        if (
          checkbox &&
          checkbox.checked == true &&
          checkbox.disabled == false
        ) {
          console.log(checkbox, "checkbox inside");
          contador++;
        }
      }
      console.log(contador, "contador antes");
      if (parseInt(contador) > 0) {
        const elemento = document.getElementById("row_tramite_M_CU");
        if (elemento) {
          elemento.style.display = "none";
          const elementochk = document.getElementById(
            "IHTTTRA-03_CLATRA-08_M_CU"
          );
          if (elementochk) {
            elementochk.checked = false;
          }
        }
      } else {
        const elemento = document.getElementById("row_tramite_M_CU");
        if (elemento) {
          elemento.style.display = "flex";
          const elementochk = document.getElementById(
            "IHTTTRA-03_CLATRA-08_M_CU"
          );
          if (elementochk) {
            elementochk.checked = false;
          }
        }
      }
    }
  }
}
//**************************************************************************************/
//* FINAL: Funcion hidde and show lineas de tramites dependiente del caso
//**************************************************************************************/
//**************************************************************************************/
//* INICIO: Validaciones sobre CheckBox de Tramites
//**************************************************************************************/
function fReviewCheck() {
  var chkTramites = [].slice.call(document.querySelectorAll(".tramiteschk"));
  chkTramites.forEach(function (chk) {
    chk.addEventListener("click", async function (event) {
      //**************************************************************************************/
      //* Separando el valor del input
      //**************************************************************************************/
      const [tipo_tramite, clase_tramite, acronimo_tipo, acronimo_clase] =
        event.target.id.split("_");
      //**************************************************************************************/
      //Validaciones si el elemento viene checked
      if (event.target.checked) {
        //****************************************************************************************/
        //* Inicio Salvando Tramite y Marcado Desmarcado Tramites Incompatibles con el actual
        //****************************************************************************************/
        let iddb = getAttribute(event.target, "data-iddb", false);
        if (iddb == "" && modalidadDeEntrada == "U") {
          const success = await addTramite(event.target);
          if (success) {
            seRecuperoVehiculoDesdeIP = 0;
            //***************************************************************************/
            //* Inicio Marcado / Desmarcando Tramitres no Compatibles con Tramite Actual
            //***************************************************************************/
            fHiddenShowTramites(
              event.target,
              acronimo_tipo,
              acronimo_clase,
              true
            );
            if (acronimo_clase == "CU") {
              esCambioDeVehiculo = true;
              seRecuperoVehiculoDesdeIP = 1;
              document.getElementById("input-prefetch").style.display = "none";
              document.getElementById("toggle-icon").style.display = "none";
              document.getElementById("rightDiv").style.display = "none";
              document.getElementById("concesion_tramite_placa_CU").focus();
            } else {
              if (acronimo_clase == "CL") {
                seRecuperoVehiculoDesdeIP = 1;
                esCambioDePlaca = true;
                document.getElementById("input-prefetch").style.display =
                  "none";
                document.getElementById("toggle-icon").style.display = "none";
                document.getElementById("rightDiv").style.display = "none";
                document.getElementById("concesion_tramite_placa_CL").focus();
              } else {
                document.getElementById("concesion_vin").focus();
              }
            }
            //***************************************************************************/
            //* Final Marcado / Desmarcando Tramitres no Compatibles con Tramite Actual
            //***************************************************************************/
          } else {
            console.log("event.preventDefault() false on AddTramite");
            event.target.checked = false;
          }
          //***************************************************************************/
          //* Final Salvando Tramite
          //***************************************************************************/
        } else {
          //***************************************************************************/
          //* Inicio Marcado / Desmarcando Tramitres no Compatibles con Tramite Actual
          //***************************************************************************/
          console.log("fHiddenShowTramites Add Modalidad I");
          fHiddenShowTramites(
            event.target,
            acronimo_tipo,
            acronimo_clase,
            true
          );
          if (acronimo_clase == "CU") {
            esCambioDeVehiculo = true;
            seRecuperoVehiculoDesdeIP = 1;
            document.getElementById("input-prefetch").style.display = "none";
            document.getElementById("toggle-icon").style.display = "none";
            document.getElementById("rightDiv").style.display = "none";
            document.getElementById("concesion_tramite_placa_CU").focus();
          } else {
            if (acronimo_clase == "CL") {
              seRecuperoVehiculoDesdeIP = 1;
              esCambioDePlaca = true;
              document.getElementById("input-prefetch").style.display = "none";
              document.getElementById("toggle-icon").style.display = "none";
              document.getElementById("rightDiv").style.display = "none";
              document.getElementById("concesion_tramite_placa_CL").focus();
            } else {
              document.getElementById("concesion_vin").focus();
            }
          }
          //***************************************************************************/
          //* Final Marcado / Desmarcando Tramitres no Compatibles con Tramite Actual
          //***************************************************************************/
        }
        //****************************************************************************************/
        //* Final Marcado Desmarcado Tramites Incompatibles con el actual
        //****************************************************************************************/
      } else {
        //****************************************************************************************/
        //* Inicio Salvando Tramite y Marcado Desmarcado Tramites Incompatibles con el actual
        //****************************************************************************************/
        let iddb = getAttribute(event.target, "data-iddb", false);
        if (iddb != "") {
          const success = await callFunctionBorrarTramite(
            document.getElementById("concesion_concesion").innerHTML,
            iddb,
            null,
            event.target
          );
          if (success) {
            if (acronimo_clase == "CU") {
              esCambioDeVehiculo = false;
              seRecuperoVehiculoDesdeIP = 0;
              document.getElementById("input-prefetch").style.display = "block";
              document.getElementById("toggle-icon").style.display = "block";
              document.getElementById("rightDiv").style.display = "flex";
            } else {
              if (acronimo_clase == "CL") {
                document.getElementById("input-prefetch").style.display =
                  "block";
                document.getElementById("toggle-icon").style.display = "block";
                document.getElementById("rightDiv").style.display = "flex";
                esCambioDePlaca = false;
                seRecuperoVehiculoDesdeIP = 0;
              }
            }
            //***************************************************************************/
            //* Inicio Marcado / Desmarcando Tramitres no Compatibles con Tramite Actual
            //***************************************************************************/
            fHiddenShowTramites(
              event.target,
              acronimo_tipo,
              acronimo_clase,
              false
            );
            //***************************************************************************/
            //* Final Marcado / Desmarcando Tramitres no Compatibles con Tramite Actual
            //***************************************************************************/
          } else {
            console.log(
              "event.preventDefault() false en borrar callFunctionBorrarTramite removeAttribute"
            );
            if (acronimo_clase == "CU") {
              esCambioDeVehiculo = false;
              seRecuperoVehiculoDesdeIP = 0;
              document.getElementById("input-prefetch").style.display = "block";
              document.getElementById("toggle-icon").style.display = "block";
              document.getElementById("rightDiv").style.display = "flex";
            } else {
              if (acronimo_clase == "CL") {
                document.getElementById("input-prefetch").style.display =
                  "block";
                document.getElementById("toggle-icon").style.display = "block";
                document.getElementById("rightDiv").style.display = "flex";
                esCambioDePlaca = false;
                seRecuperoVehiculoDesdeIP = 0;
              }
            }
            event.preventDefault();
          }
        } else {
          console.log("fHiddenShowTramites delete cuando es modalidad I");
          //***************************************************************************/
          //* Inicio Marcado / Desmarcando Tramitres no Compatibles con Tramite Actual
          //***************************************************************************/
          fHiddenShowTramites(
            event.target,
            acronimo_tipo,
            acronimo_clase,
            false
          );
          if (acronimo_clase == "CU") {
            esCambioDeVehiculo = false;
            seRecuperoVehiculoDesdeIP = 0;
            document.getElementById("ID_Unidad1").value == "";
            document.getElementById("btnCambiarUnidad").style.display = "none";
            document.getElementById("idVistaSTPC1").style = "display:fixed;";
            document.getElementById("idVistaSTPC2").style = "display:none;";
            document.getElementById("input-prefetch").style.display = "block";
            document.getElementById("toggle-icon").style.display = "block";
            document.getElementById("rightDiv").style.display = "flex";
          } else {
            if (acronimo_clase == "CL") {
              document.getElementById("ID_Unidad1").value == "";
              document.getElementById("btnCambiarUnidad").style.display =
                "none";
              document.getElementById("idVistaSTPC1").style = "display:fixed;";
              document.getElementById("idVistaSTPC2").style = "display:none;";
              document.getElementById("input-prefetch").style.display = "block";
              document.getElementById("toggle-icon").style.display = "block";
              document.getElementById("rightDiv").style.display = "flex";
              esCambioDePlaca = false;
              seRecuperoVehiculoDesdeIP = 0;
            }
          }
          //***************************************************************************/
          //* Final Marcado / Desmarcando Tramitres no Compatibles con Tramite Actual
          //***************************************************************************/
        }
        //****************************************************************************************/
        //* Final Marcado Desmarcado Tramites Incompatibles con el actual
        //****************************************************************************************/
      }
    });
  });

  //*********************************************************************************************/
  //* INICIO: Se ocultan las row de tramites de acuerdo a los checkbox marcados e inabilitacion
  //* de los tramites de renovacion, ya que los mismo solo pueden ser marcados por sistema
  //*********************************************************************************************/
  var el = document.getElementById("IHTTTRA-02_CLATRA-02_R_CO");
  if (el) {
    el.disabled = true;
  }
  el = document.getElementById("IHTTTRA-02_CLATRA-01_R_PE");
  if (el) {
    el.disabled = true;
  }
  el = document.getElementById("IHTTTRA-02_CLATRA-03_R_PS");
  if (el) {
    el.disabled = true;
  }
  chkTramites.forEach(function (chk) {
    if (chk.checked == true) {
      const [tipo_tramite, clase_tramite, acronimo_tipo, acronimo_clase] =
        chk.id.split("_");
      fHiddenShowTramites(chk, acronimo_tipo, acronimo_clase, true);
    }
  });
  //**************************************************************************************/
  //* FINAL: Se ocultan las row de tramites de acuerdo a los checkbox marcados
  //**************************************************************************************/
}
//**************************************************************************************/
//* FINAL: Validaciones sobre Check Box de Tramites
//**************************************************************************************/

function getVehiculoDesdeIPMoveDatos(vehiculo, Tipo_Tramite) {
  console.log(vehiculo, "vehiculo en getVehiculoDesdeIPMoveDatos");
  console.log(Tipo_Tramite, "Tipo_Tramite en getVehiculoDesdeIPMoveDatos");
  var sufijo = "1";
  if (Tipo_Tramite == "CL") {
    sufijo = "";
  } else {
    document.getElementById("capacidad" + sufijo).value = "";
    document.getElementById("alto" + sufijo).value = "";
    document.getElementById("largo" + sufijo).value = "";
    document.getElementById("ancho" + sufijo).value = "";
  }

  console.log(sufijo, "sufijo en getVehiculoDesdeIPMoveDatos");
  document.getElementById("combustible" + sufijo).value =
    sufijo !== ""
      ? document.getElementById("combustible").value
      : vehiculo.cargaUtil.combustible;
  //**********************************************************************************/
  //* INICIO: MOVIEMIENTO DE DATOS DEL IP A PANTALLA
  //**********************************************************************************/
  var concesionlabel = document.getElementById("concesion1label");
  if (concesionlabel != null) {
    concesionlabel.innerHTML =
      document.getElementById("concesionlabel").innerHTML;
  }
  document.getElementById("concesion" + sufijo + "_cerant").innerHTML =
    document.getElementById("concesion_cerant").innerHTML;
  document.getElementById("concesion" + sufijo + "_numregant").innerHTML =
    document.getElementById("concesion_numregant").innerHTML;
  document.getElementById("concesion" + sufijo + "_numeroregistro").innerHTML =
    document.getElementById("concesion_numeroregistro").innerHTML;
  document.getElementById("concesion" + sufijo + "_categoria").innerHTML =
    document.getElementById("concesion_categoria").innerHTML;
  document.getElementById("concesion" + sufijo + "_concesion").innerHTML =
    document.getElementById("concesion_concesion").innerHTML;
  document.getElementById("concesion" + sufijo + "_perexp").innerHTML =
    document.getElementById("concesion_perexp").innerHTML;
  document.getElementById("concesion" + sufijo + "_fecexp").innerHTML =
    document.getElementById("concesion_fecexp").innerHTML;
  document.getElementById("concesion" + sufijo + "_resolucion").innerHTML =
    document.getElementById("concesion_resolucion").innerHTML;
  document.getElementById("concesion" + sufijo + "_fecven").innerHTML =
    document.getElementById("concesion_fecven").innerHTML;
  document.getElementById(
    "concesion" + sufijo + "_nombreconcesionario"
  ).innerHTML = document.getElementById(
    "concesion_nombreconcesionario"
  ).innerHTML;
  document.getElementById("concesion" + sufijo + "_rtn").innerHTML =
    document.getElementById("concesion_rtn").innerHTML;
  document.getElementById("concesion" + sufijo + "_resolucion").innerHTML =
    document.getElementById("concesion_resolucion").innerHTML;

  if (document.getElementById("concesion_placaanterior").innerHTML != "") {
    document.getElementById("concesion" + sufijo + "_placaanterior").innerHTML =
      vehiculo.cargaUtil.placaAnterior;
    document.getElementById("concesion" + sufijo + "_placaanterior").style =
      "display:inline;";
  } else {
    document.getElementById("concesion" + sufijo + "_placaanterior").style =
      "display:none;";
    document.getElementById("concesion" + sufijo + "_placaanterior").innerHTML =
      "";
  }
  document.getElementById(
    "concesion" + sufijo + "_nombre_propietario"
  ).innerHTML = vehiculo.cargaUtil.propietario.nombre;
  document.getElementById(
    "concesion" + sufijo + "_identidad_propietario"
  ).innerHTML = vehiculo.cargaUtil.propietario.identificacion;
  document.getElementById("concesion" + sufijo + "_vin").value =
    vehiculo.cargaUtil.vin;
  document.getElementById("concesion" + sufijo + "_placa").value =
    vehiculo.cargaUtil.placa;
  document.getElementById("concesion" + sufijo + "_serie").value =
    vehiculo.cargaUtil.chasis;
  document.getElementById("concesion" + sufijo + "_motor").value =
    vehiculo.cargaUtil.motor;
  document.getElementById("concesion" + sufijo + "_tipo_vehiculo").value =
    vehiculo.cargaUtil.tipo;
  document.getElementById("concesion" + sufijo + "_modelo_vehiculo").value =
    vehiculo.cargaUtil.modelo;
  fLlenarSelect(
    "marcas" + sufijo,
    dataConcesion["marcas"],
    vehiculo.cargaUtil.marcacodigo,
    false,
    { text: "SELECCIONE UNA AÑO", value: "-1" }
  );
  fLlenarSelect(
    "colores" + sufijo,
    dataConcesion["colores"],
    vehiculo.cargaUtil.colorcodigo,
    false,
    { text: "SELECCIONE UN COLOR", value: "-1" }
  );
  console.log(
    vehiculo.cargaUtil.colorcodigo,
    "vehiculo.cargaUtil.colorcodigo getVehiculo"
  );
  fLlenarSelect(
    "anios" + sufijo,
    dataConcesion["anios"],
    vehiculo.cargaUtil.axo,
    false,
    { text: "SELECCIONE UN AÑO", value: "-1" }
  );
  //**********************************************************************************/
  //* FINAL: MOVIEMIENTO DE DATOS DEL IP A PANTALLA
  //**********************************************************************************/
  seRecuperoVehiculoDesdeIP = 3;
  console.log(Tipo_Tramite, "console.log(Tipo_Tramite);");
  if (Tipo_Tramite == "CU") {
    console.log(Tipo_Tramite, "Tipo_Tramite");
    document.getElementById("btnCambiarUnidad").innerHTML =
      "<strong>ENTRA</strong>";
    document.getElementById("btnCambiarUnidad").style =
      "display:flex; position: absolute; top: 195px; right: 25px; padding: 10px;";
    document.getElementById("idVistaSTPC2").style = "display:fixed;";
    document.getElementById("idVistaSTPC1").style = "display:none;";
  }

  return true;
}

async function getVehiculoDesdeIP(Obj) {
  if (
    (document.getElementById("concesion_tramite_placa_CU").value !== "" &&
      document.getElementById("concesion_tramite_placa_CL").value !== "" &&
      document.getElementById("concesion_tramite_placa_CL").value !==
      document.getElementById("concesion_tramite_placa_CU").value) ||
    (document.getElementById("concesion_tramite_placa_CL").value !== "" &&
      document.getElementById("concesion_tramite_placa_CL").value !==
      document.getElementById("concesion_placa").value) ||
    (document.getElementById("concesion_tramite_placa_CU").value !== "" &&
      document.getElementById("concesion_tramite_placa_CU").value !==
      document.getElementById("concesion_placa").value)
  ) {
    try {
      //*********************************************************************************************************************/
      //* Si es Certificado Entra Aqui para obtener el CO y PE
      //*********************************************************************************************************************/
      if (esCertificado) {
        var Concesion = document.getElementById(
          "concesion_concesion"
        ).innerHTML;
      } else {
        var Concesion = document.getElementById(
          "concesion_concesion"
        ).innerHTML;
      }
      // URL del Punto de Acceso a la API
      const url = $appcfg_Dominio + "Api_Ram.php";
      //  Fetch options
      // const options = {
      //   method: 'POST',
      //   body: fd,
      //   headers: {
      //     'Content-Type': 'application/json',
      //   },
      // };
      let fd = new FormData(document.forms.form1);
      //Adjuntando el action al FormData
      fd.append("action", "get-vehiculo");
      //Adjuntando el idApoderado al FormData
      fd.append("ID_Placa", Obj.value);
      fd.append("RAM", document.getElementById("RAM").value);
      fd.append("Concesion", Concesion);
      // Fetch options
      const options = {
        method: "POST",
        body: fd,
      };

      const response = await fetchWithTimeout(url, options, 120000);
      const vehiculo = await response.json();

      if (!vehiculo.error) {
        if (vehiculo?.codigo && vehiculo.codigo == 200) {
          if (vehiculo.cargaUtil.estadoVehiculo == "NO BLOQUEADO") {
            document.getElementById("concesion_vin").focus();
            isVehiculeBlock = false;
            sendToast(
              "INFORMACIÓN DEL VEHICULO RECUPERADO EXITOSAMENTE DESDE EL INSTITUTO DE LA PROPIEDAD",
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
            var html = "";
            if (vehiculo?.cargaUtil?.Multas?.[0]) {
              html = mallaDinamica(
                { titulo: "LISTADO DE MULTAS", name: "MULTAS" },
                vehiculo.cargaUtil.Multas
              );
            }
            if (vehiculo?.cargaUtil?.Preformas?.[0]) {
              html =
                html +
                mallaDinamica(
                  {
                    titulo: "LISTADO DE PREFORMAS PENDIENTES DE RESOLUCIÓN",
                    name: "PREFORMAS",
                  },
                  vehiculo.cargaUtil.Preformas
                );
            }
            if (vehiculo?.cargaUtil?.Placas?.[0]) {
              html =
                html +
                mallaDinamica(
                  {
                    titulo:
                      "CERTIFICADO Y/O UNIDAD ENTRANTE TIENEN DOCUMENTOS PARA IMPRESIÓN Y/O ENTREGA",
                    name: "PREFORMAS",
                  },
                  vehiculo.cargaUtil.Placas
                );
            }
            if (html != "") {
              const result = await fSweetAlertEventNormal(
                "VALIDACIONES",
                "LA UNIDAD TIENE MULTA(S) PENDIENTE(S) DE PAGO, FAVOR PAGAR LAS MULTAS PREVIO A INGRESAR EL TRAMITE",
                "error",
                html
              );
            }
            getVehiculoDesdeIPMoveDatos(vehiculo, Obj.id.split("_")[3]);
            return true;
          } else {
            if (vehiculo.cargaUtil.estadoVehiculo == "BLOQUEADO") {
              const result = await fSweetAlertEventNormal(
                "BLOQUEADO",
                "EL VEHICULO ESTA BLOQUEADO EN EL INSTITUTO DE LA PROPIEDAD",
                "error"
              );
              isVehiculeBlock = true;
            } else {
              const result = await fSweetAlertEventNormal(
                "ERROR",
                "ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA",
                "error"
              );
            }
          }
          return true;
        } else {
          if (
            vehiculo?.codigo != null &&
            (vehiculo.codigo === 407 || vehiculo.codigo === 408)
          ) {
            const result = await fSweetAlertEventNormal(
              "ADVERTENCIA",
              "NO HEMOS PODIDO CONECTARNOS CON EL INSTITUTO DE LA PROPIEDAD, FAVOR INTENTENLO EN UN MOMENTO SI EL PROBLEMA PERSISTE CONTACTE AL ADMINISTRADOR DEL SISTEMA",
              "warning"
            );
          } else {
            const result = await fSweetAlertEventNormal(
              "INFORMACIÓN",
              "EL VEHICULO NO HA SIDO ENCONTRADO EN LA BASE DE DATOS DEL IP",
              "warning"
            );
          }
        }
        return true;
      } else {
        const result = await fSweetAlertEventNormal(
          vehiculo.errorhead,
          vehiculo.error + "- " + vehiculo.errormsg,
          "error"
        );
        return true;
      }
    } catch (error) {
      console.log(
        "catch error eliminando tramite en preforma fEliminarTramite" + error
      );
      const result = await fSweetAlertEventSelect(
        "",
        "ELIMINAR TRAMITE EN PREFORMA",
        "ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA",
        "warning"
      );
      return true;
    }
  } else {
    const result = await fSweetAlertEventNormal(
      "ERROR EN PLACAS",
      "LA PLACA NO PUEDE SER IGUAL A LA PLACA DEL TRAMITE DE CAMBIO DE PLACA o CAMBIO DE UNIDAD",
      "error"
    );
    return true;
  }
}

var ejecutar = true;

function setaddEventListener() {
  //******************************************************************/
  //* INICIO: Crear eventListener para los checkbox de Tramite
  //******************************************************************/
  fReviewCheck();
  //******************************************************************/
  //* FINAL: Crear eventListener para los checkbox de Tramite
  //******************************************************************/

  ejecutar = true;

  const inputCambioUnidad = document.getElementById(
    "concesion_tramite_placa_CU"
  );
  if (inputCambioUnidad) {
    async function handleChangeCU(event) {
      console.log(ejecutar, "ejecutar");
      console.log(event.target.value, "event.target.value");
      if (ejecutar == true && event.target.value !== "") {
        event.preventDefault();
        ejecutar = false;
        inputCambioUnidad.removeEventListener("keyup", handleKeyUpCU);
        seRecuperoVehiculoDesdeIP = 2;
        ejecutar = await getVehiculoDesdeIP(event.target);
        setTimeout(() => {
          inputCambioUnidad.addEventListener("keyup", handleKeyUpCU);
          console.log("Evento keypress reactivado");
        }, 8000);
      }
    }
    async function handleKeyUpCU(event) {
      if (event.target.value !== "") {
        console.log(ejecutar, "ejecutar keyup");
        console.log(event.key, "event.key");
        if (
          ejecutar == true &&
          (event.key === "Tab" || event.key === "Enter")
        ) {
          ejecutar = false;
          // Deshabilitar el evento change temporalmente
          inputCambioUnidad.removeEventListener("change", handleChangeCU);
          seRecuperoVehiculoDesdeIP = 2;
          event.preventDefault();
          ejecutar = await getVehiculoDesdeIP(event.target);
          // Simular una tarea (por ejemplo, esperar 2 segundos antes de reactivar change)
          setTimeout(() => {
            inputCambioUnidad.addEventListener("change", handleChangeCU);
            console.log("Evento change reactivado");
          }, 8000);
        }
      }
    }
    inputCambioUnidad.addEventListener("change", handleChangeCU);
    inputCambioUnidad.addEventListener("keyup", handleKeyUpCU);
  }

  const inputCambioPlaca = document.getElementById(
    "concesion_tramite_placa_CL"
  );
  if (inputCambioPlaca) {
    async function handleChangeCP(event) {
      if (ejecutar == true && event.target.value !== "") {
        ejecutar = false;
        inputCambioPlaca.removeEventListener("keyup", handleKeyUpCP);
        seRecuperoVehiculoDesdeIP = 2;
        event.preventDefault();
        ejecutar = await getVehiculoDesdeIP(event.target);
        setTimeout(() => {
          inputCambioPlaca.addEventListener("keyup", handleKeyUpCP);
          console.log("Evento keyup reactivado");
        }, 8000);
      }
    }
    async function handleKeyUpCP(event) {
      if (event.target.value !== "") {
        if (
          ejecutar == true &&
          (event.key === "Tab" || event.key === "Enter")
        ) {
          ejecutar = false;
          // Deshabilitar el evento change temporalmente
          inputCambioPlaca.removeEventListener("change", handleChangeCP);
          seRecuperoVehiculoDesdeIP = 2;
          event.preventDefault();
          ejecutar = await getVehiculoDesdeIP(event.target);
          // Simular una tarea (por ejemplo, esperar 2 segundos antes de reactivar change)
          setTimeout(() => {
            inputCambioPlaca.addEventListener("change", handleChangeCP);
            console.log("Evento change reactivado");
          }, 8000);
        }
      }
    }
    inputCambioPlaca.addEventListener("change", handleChangeCP);
    inputCambioPlaca.addEventListener("keyup", handleKeyUpCP);
  }
}
//**************************************************************************************/
//Habilitando las teclas F2 y F10 para moverse entre los paneles
//**************************************************************************************/
document.addEventListener("keydown", function (event) {
  if (event.key === "F2") {
    event.preventDefault();
    var btn = document.getElementById("btnprevious" + currentstep);
    if (btn != null) {
      btn.click();
    } else {
      console.log("No existe el boton: btnprevious" + currentstep);
    }
  } else {
    if (event.key === "F10") {
      event.preventDefault();
      var btn = document.getElementById("btnnext" + currentstep);
      if (btn != null) {
        btn.click();
      } else {
        console.log("No existe el boton: btnnext" + currentstep);
      }
    }
  }
});

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//@@@ FUNCION PARA CARGAR ARCHIVOS ADJUNTOS    @@@
//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//var $contador = 0;
function CargaSolicitudAjax() {
  $contador++;
  $("#numeracion").html("0%");
  $(".determinate").css("width", "0%");
  let fileInput = document.getElementById("EscaneoSolicitud"); // Select the input element
  let firstFile = fileInput.files[0]; // Get the first file
  var form_data = new FormData();
  form_data.append("action", "save-escaneo");
  form_data.append("RAM", document.getElementById("RAM").value);
  form_data.append("Archivo", firstFile);
  sweetAlert.fire({
    title: "Subiendo archivo(s)",
    html: '<div id="barra" > <div class="progress"> <div class="determinate" style="width: 0%"></div> </div> <div id="numeracion" style=" text-align: center; ">0%</div> </div>',
    html: true,
    showConfirmButton: true,
  });
  $("#barra").show(400);
  $.ajax({
    timeout: 0,
    url: $appcfg_Dominio + "Api_Ram.php",
    dataType: "json",
    cache: false,
    contentType: false,
    processData: false,
    data: form_data,
    type: "post",
    success: function (data) {
      var datar = data;
      if (typeof datar.msg != "undefined") {
        swal.fire(
          {
            title: "Archivo(s) Subidos",
            text: "archivo subido con exito ...",
            confirmButtonText: "Presione Ok Para Continuar",
          },
          function () {
            // ArchivosSolicitud();
            // validarCargaDocs();
          }
        );
      } else {
        sweetAlert.fire("Oops...", "Algo Salio Mal!", "error: " + datar.Error);
      }
    },
    xhr: function () {
      // creamos un objeto XMLHttpRequest
      var xhr = new XMLHttpRequest();
      // gestionamos el evento 'progress'
      xhr.upload.addEventListener(
        "progress",
        function (evt) {
          if (evt.lengthComputable) {
            // calculamos el porcentaje completado de la carga de archivos
            var percentComplete = evt.loaded / evt.total;
            percentComplete = parseInt(percentComplete * 100);
            // actualizamos la barra de progreso con el nuevo porcentaje
            $("#swal2-html-container").html(percentComplete + "%");
            $(".determinate").css("width", "" + percentComplete + "%");
            // una vez que la carga llegue al 100%, ponemos la progress bar como Finalizado
            if (percentComplete === 100) {
              $(".progress-bar").html("Finalizado");
            }
          }
        },
        false
      );
      return xhr;
    },
  });
}

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//@@@ FUNCION PARA CARGAR ARCHIVOS ADJUNTOS    @@@
//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
var $contador = 0;

function CargaSolicitud() {
  $contador++;
  let fileInput = document.getElementById("EscaneoSolicitud");
  let firstFile = fileInput.files[0]; // Get the first file

  if (!firstFile) {
    sweetAlert.fire("Error", "No file selected for upload", "error");
    return;
  }

  let formData = new FormData();
  formData.append("action", "save-escaneo");
  formData.append("RAM", document.getElementById("RAM").value);
  formData.append("Archivo", firstFile);

  sweetAlert.fire({
    title: "Subiendo archivo(s)",
    html: '<div id="barra"><div class="progress"><div class="determinate" style="width: 0%"></div></div><div id="numeracion" style="text-align: center;">0%</div></div>',
    showConfirmButton: false,
  });

  document.getElementById("numeracion").innerHTML = "0%";
  document.querySelector(".determinate").style.width = "0%";

  // Create an XMLHttpRequest object
  let xhr = new XMLHttpRequest();

  // Configure it
  xhr.open("POST", $appcfg_Dominio + "Api_Ram.php", true);

  // Track progress
  xhr.upload.addEventListener("progress", function (evt) {
    if (evt.lengthComputable) {
      let percentComplete = Math.round((evt.loaded / evt.total) * 100);
      document.getElementById("numeracion").innerHTML = `${percentComplete}%`;
      document.querySelector(
        ".determinate"
      ).style.width = `${percentComplete}%`;
      if (percentComplete === 100) {
        document.querySelector(".progress").innerHTML = "Finalizado";
      }
    }
  });

  // Handle the response
  xhr.onload = function () {
    if (xhr.status === 200) {
      var data;
      try {
        data = JSON.parse(xhr.responseText);
      } catch (e) {
        sweetAlert.fire("Error", "Invalid server response.", "error");
        return;
      }
      if (typeof data.msg != "undefined") {
        sweetAlert.fire({
          title: "CARGA DE ARCHIVOS",
          text: "Archivo subido exitosamente",
          confirmButtonText: "Cerrar",
        });
      } else {
        sweetAlert.fire(
          "Oops...",
          `Estamos presentando inconvenientes para procesar tu petición: ${data.errormsg}`,
          "error"
        );
      }
    } else {
      sweetAlert.fire("Oops", `HTTP Error: ${xhr.status}`, "error");
    }
  };

  // Handle errors
  xhr.onerror = function () {
    sweetAlert.fire("Error", "Error de conexión al servidor.", "error");
  };

  // Send the request
  xhr.send(formData);
}

var scrollDiv = document.querySelector(".scroll-div");
var initialOffset = 185; // Offset inicial de 170px desde el top
var minOffset = 80; // Mínimo offset cuando se desplaza más de 170px

window.addEventListener("scroll", () => {
  if (currentstep == 2) {
    // Obtén la posición de desplazamiento vertical actual
    var scrollPosition = window.scrollY;
    // Calcula la nueva posición vertical del div
    var newYPosition = Math.max(initialOffset - scrollPosition, minOffset);
    // Actualiza la posición del div
    scrollDiv.style.top = `${newYPosition}px`;
  }
});

function adjustLayout() {
  let divVista1 = document.getElementById("div-vista-1");
  let divVista2 = document.getElementById("div-vista-2");

  if (window.getComputedStyle(divVista1).display === "none") {
    divVista2.classList.add("full-width"); // Center div-vista-2
  } else {
    divVista2.classList.remove("full-width"); // Restore normal width
  }
}

// Run on page load & resize
window.onload = adjustLayout;
window.onresize = adjustLayout;