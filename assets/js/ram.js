//*186.2.137.13
"use strict";

var isSaving = false;
var cargadocs = false;
var estanCargadocs = false;
var requicitosRecuperados = false;
var arrayOriginalRows = Array();
var concesionBorradoEnMalla = 0;
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
var Reportes = '';
const LinksConsulta = new Map([
  ["STEC", ":122/PV/Permiso_Especial_Carga_IHTT.php?permiso="],
  ["STEP", ":122/PV/Permiso_Especial_Pas_IHTT.php?permiso="],
  ["STPC", ":122/PV/Certificado_Carga_IHTT.php?certificado="],
  ["STPP", ":122/PV/Certificado_Pasajero_IHTT.php?certificado="]
]);


// function ////showConcesionTramites(noOcultar = false) {
//   var ct = document.getElementById("sidebar");
//   if (document.getElementById("concesion_vin").value != "") {
//     if (ct) {
//       if (ct.style.display !== "none") {
//         if (noOcultar === false) {
//           ct.style = "display: none;";
//           document.getElementById("contenido").classList.remove("col-7");
//           document.getElementById("contenido").classList.add("col-10");
//         }
//       } else {
//         ct.style = "display: block;";
//         document.getElementById("contenido").classList.remove("col-10");
//         document.getElementById("contenido").classList.add("col-7");
//       }
//     }
//   }
// }

//*****************************************************************************************/
//* INICIO: Abrir Modal para Primera Vez o Incremento de Concesion
//*****************************************************************************************/
function open1raVezModal () {
  const myModal = new bootstrap.Modal(document.getElementById("primeraVezModal"));
  myModal.show();
}
//*****************************************************************************************/
//* FINAL: Abrir Modal para Primera Vez o Incremento de Concesion
//*****************************************************************************************/

//*****************************************************************************************/
//* INICIO: Esta en un estado editable el expediente                                      */
//*****************************************************************************************/
function esEditable () {
  return Boolean(Number(document.getElementById("esEditable").value));
}
//*****************************************************************************************/
//* FINAL: Esta en un estado editable el expediente                                      */
//*****************************************************************************************/
//*****************************************************************************************/
//* INICIO: Esta en un estado compartible el expediente                                      */
//*****************************************************************************************/
function esCompartible () {
  return document.getElementById("esCompartible").value;
}
//*****************************************************************************************/
//* FINAL: Esta en un estado compartible el expediente                                      */
//*****************************************************************************************/
function lockFormElements() {
  const elements = document.querySelectorAll('input, textarea, select, checkbox, radio');
  elements.forEach(el => {
    // Saltar si ya está marcado como "lockeado"
    if (el.dataset.state !== undefined) return;

    // Guardar estado original
    if (el.disabled) {
      el.dataset.state = 'disabled';
    } else if (el.readOnly) {
      el.dataset.state = 'readonly';
    } else {
      el.dataset.state = 'enabled';
    }
    // Aplicar restricciones según tipo
    if (el.tagName === 'TEXTAREA' || el.type === 'text' || el.type === 'number' || el.type === 'email' || el.type === 'password' || el.type === 'search' || el.type === 'url') {
      //el.readOnly = true;
      el.disabled = true;
    } else {
      el.disabled = true;
    }
  });
  document.getElementById("editable").style.display = "inline";
  document.getElementById("editable").setAttribute("title", "Usted esta en modalidad de consulta");
}

function unlockFormElements() {
  const elements = document.querySelectorAll('input, textarea, select, button');

  elements.forEach(el => {
    const state = el.dataset.state;
    if (state === undefined) return;

    if (state === 'disabled') {
      el.disabled = true;
    } else if (state === 'readonly') {
      el.readOnly = true;
    } else {
      el.disabled = false;
      el.readOnly = false;
    }

    // Limpiar el atributo data-state
    delete el.dataset.state;
  });
}

//*****************************************************************************************/
//* INICIO: Funcion que presenta en gif de loading                                        */
//*****************************************************************************************/
function loading(isLoading, currentstep) {
  if (isLoading) {
    //*****************************************************************************************/
    //* INICIO: Oculta la información del stepper content y presenta el gif de procesando    */
    //*****************************************************************************************/
    document.getElementById("id_stepper_gif").style = "display:flex";
    document.getElementById("id_img_stepper_gif").style = "display:flex";
    document.getElementById("id_stepper_content").style = "display:none";
    if (currentstep == 2) {
      //////showConcesionTramites(true);
    }
    //*********************************************************************************************/
    //* FINAL: Oculta la información del stepper content y presenta el gif de procesando          */
    //*********************************************************************************************/
  } else {
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
//*****************************************************************************************/
//* FINAL: Funcion que presenta en gif de loading                                        */
//*****************************************************************************************/

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
          ID_CHECK === "IHTTTRA-02_CLATRA-03_R_PS"
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

function fShowTramites(forzarShow=false) {
  var ct = document.getElementById("sidebar");
  if (ct) {
    if (ct.style.display !== "none" && forzarShow == false) {
      ct.style = "display: none;";
      document.getElementById("contenido").classList.remove("col-7");
      document.getElementById("contenido").classList.add("col-10");
    } else {
      ct.style = "display: block;";
      document.getElementById("contenido").classList.remove("col-10");
      document.getElementById("contenido").classList.add("col-7");
    }
  }
}
function fShowConcesiones() {
  let tooltipTrigger  = document.getElementById('rightDiv');
  const instance = bootstrap.Tooltip.getInstance(tooltipTrigger);
  if (instance && instance._isShown) {
    instance.hide();
  }
  concesionBorradoEnMalla = 0
  const myModal = new bootstrap.Modal(document.getElementById("exampleModal"));
  myModal.show();
  mostrarData(concesionNumber, "tabla-container", "CONCESIONES SALVADAS");
}

function updateCollection(Elemento) {
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
  //* Resecuenciando los tramites
  if (concesionNumber?.[index]?.Tramites && Array.isArray(concesionNumber[index].Tramites)) {
    var longitud_arreglo_tramites = concesionNumber[index].Tramites.length;
    //* Resecuenciación
    var contador_tramites = 0;
    for (let j = 0; j < longitud_arreglo_tramites + 1; j++) {
      var rowt = document.getElementById("indice_row_tramite_" + (((index - 1) == originalrow) ? index : originalrow) + "_" + j);
      if (rowt) {
        if (parseInt(index) == parseInt(originalrow)) {
          if (concesionBorradoEnMalla == 0) {
            rowt.innerHTML = parseInt(parseInt(index) + 1) + "." + String(parseInt(parseInt(contador_tramites) + 1));
          } else {
            rowt.innerHTML = parseInt(index) + "." + String(parseInt(parseInt(contador_tramites) + 1));
          }
        } else {
          rowt.innerHTML = labelLine + "." + String(parseInt(parseInt(contador_tramites) + 1));
        }
        contador_tramites++;
      }
    }
  }
}

function resencuenciarConcesionNumberTramitesTodos(cantidad) {
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
        for (var j = 0; j < longitud_arreglo_tramites; j++) {
          var rowt = document.getElementById("indice_row_tramite_" + String(contador) + "_" + String(j));
          if (rowt && rowt.id != "indice_row_tramite_" + String(contador_nuevo) + "_" + String(j)) {
            rowt.innerHTML = (parseInt(contador_nuevo) + 1) + "." + String(parseInt(contador_tramites) + 1);
            contador_tramites++;
          } else {
            if (rowt) {
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
  console.log(currentInput,'currentInput On moveToNextInput()');
  console.log(value,'Value On moveToNextInput()');
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
  // Get the element by its ID
  var element = document.getElementById("test-form-" + (currentstep + 1));
  // Get all input elements inside this element
  var inputs = element.querySelectorAll(".test-controls");
  // Convert NodeList to Array for easier manipulation (optional)
  inputs = Array.from(inputs);
  // Iterando entre los inputs para despachar el evento change y validar la data de cada input
  var index = 0;
  inputs.forEach((input) => {
    //var idIgnore = input.getAttribute('data-validar-salvar');
    //if (input.disabled == false && idIgnore!=false) {
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
          if (isSaving == false) {
            moveToNextInput(input, 0);
          }
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
  if (ElementType == "PLACAS") {
    currentConcesionIndex = updateCollection(Concesiones);
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
    if (document.getElementById("ID_Expediente").value == '') {
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

    const response = await fetchWithTimeout(url, options, 300000);
    const Datos = await response.json();

    if (typeof Datos.ERROR != "undefined") {
      sendToast(
        $appcfg_icono_de_error + " INCONVENIENTES ELIMINANDO TRAMITE EN PREFORMA, INTENTELO NUEVAMENTE SI EL ERROR PERSISTE FAVOR CONTACTAR AL ADMINISTRADOR DEL SISTEMA",
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
      return false;
    } else {
      //*******************************************************************************************************/
      //*INICIO: LLAANDO FUNCION DE PREBORRADO DE concesionForAutoComplete
      //*******************************************************************************************************/
      const elemt = document.getElementById("idLengTramites");
      if (elemt) {
        animateValue(
          elemt,
          parseInt(elemt.textContent),
          parseInt(parseInt(elemt.textContent) - parseInt(1)),
          9000,
          "highlightGris",
          parseInt,
          0
        );
      }
      preDeleteAutoComplete(idConcesion);
      //*******************************************************************************************************/
      //*INICIO: LLAMANDO FUNCION QUE ACTUALIZA EL ARREGLO DE TRAMITES, ELIMINANDO EL TRAMITE BORRADO EN LA DB
      //*******************************************************************************************************/
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
        $appcfg_icono_de_success + " TRAMITE ELIMINADO EXITOSAMENTE",
        $appcfg_milisegundos_toast,
        "",
        true,
        true,
        "top",
        $appcfg_pocision_toast,
        true,
        $appcfg_style_toast,
        function () { },
        "success",
        $appcfg_offset_toast,
        $appcfg_icono_toast
      );
      if (idRow != null && Monto != false) {
        const row = document.getElementById(idRow);
        if (row) {
          agregar_fila(dataAgregarFila[0], dataAgregarFila[1], dataAgregarFila[2], dataAgregarFila[3], dataAgregarFila[4] = '', dataAgregarFila[5] = '');
        } else {
          console.log("Linea # " + idRow + " No Encontrada!");
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
      "ELIMINAR TRAMITE",
      "ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA",
      "warning"
    );
    return false;
  }
}


btnCambiarUnidad.addEventListener("click", function (event) {
  const btn = event.target.closest("#btnCambiarUnidad");
  if (btn) {
    const texto = btn.textContent.trim();
    if (texto === "ENTRA") {
      btn.innerHTML = '<i class="fas fa-truck-moving fa-2x"></i>  <strong>SALE</strong>';
      btn.classList.value = "btn btn-primary btn-sm scroll-btn";
      document.getElementById("idVistaSTPC1").style.display = "block";
      document.getElementById("idVistaSTPC2").style.display = "none";
    } else {
      btn.innerHTML = '<i class="fas fa-truck-moving fa-2x"></i>  <strong>ENTRA</strong>';
      btn.classList.value = "btn btn-success btn-sm scroll-btn";
      document.getElementById("idVistaSTPC2").style.display = "block";
      document.getElementById("idVistaSTPC1").style.display = "none";
    }

    event.stopPropagation();
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
  clases = [],
  ruta = $appcfg_Dominio_Raiz + ":90/api_rep.php?ra=S&action=get-facturaPdf&nu=",
  CodigoAvisoCobro, 
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
  document.getElementById("flexSwitchCheckAcreditarRepresentacion").checked = true;
  document.getElementById("flexSwitchCheckEscritoSolicitud").checked = true;
  document.getElementById("flexSwitchCheckDNI").checked = true;
  document.getElementById("flexSwitchCheckRTN").checked = true;
  document.getElementById("flexSwitchCheckInspeccionFisico").checked = true;
  document.getElementById("flexSwitchCheckBoletaRevision").checked = true;
  if ((esCambioDeVehiculo == false && document.getElementById("concesion_rtn").textContent != '' && document.getElementById("concesion_rtn").textContent != document.getElementById("concesion_identidad_propietario").textContent) ||
      (esCambioDeVehiculo == true && document.getElementById("concesion_rtn").textContent != '' && document.getElementById("concesion_rtn").textContent != document.getElementById("concesion1_identidad_propietario").textContent)) {
    document.getElementById("flexSwitchCheckContratoArrendamiento").checked = true;
  } else {
    document.getElementById("flexSwitchCheckContratoArrendamiento").checked = false;
  }
  document.getElementById("flexSwitchCheckAutenticidadCarta").checked = true;
  document.getElementById(
    "flexSwitchCheckAutenticidadDocumentos"
  ).checked = true;
}
//**************************************************************************************/
//* Procesar Reportes y Establecerlos en Modal de Reportes
//**************************************************************************************/
function procesarDatosIHTT(Reportes) {
  var detalle = document.getElementById("id_reportes");
  var keys = 	['botoncertificado','botonexplotacion','botonexpediente',
                'botonavisocobro','botoncomprobante','botonportada',
                'botonauto','botonresolucion'];

  for (const key in Reportes) {
    if (Reportes.hasOwnProperty(key)) {
      if (keys.includes(key)) {
        const valor = Reportes[key];
        detalle.innerHTML = detalle.innerHTML + valor; 
      }
    }
  }
}
//**************************************************************************************/
//* Cargando la información por default que debe usar el formulario
//**************************************************************************************/
//**************************************************************************************/
//* Cargando la información por default que debe usar el formulario
//**************************************************************************************/
function f_DataOmisionPaginacion(total_tramites, total_unidades,RAM_O_EXP='RAM') {
  const pageSize = parseInt(document.getElementById("pageSize").value, 10) || 1;
  const total_paginas_tramites = Math.ceil(total_tramites / pageSize);
  const total_paginas_unidades = Math.ceil(total_unidades / pageSize);

  // Get the URL parameters from the current page
  const urlParams = new URLSearchParams(window.location.search);
  let RAM = urlParams.get("RAM");       // Número de RAM
  let Consulta = urlParams.get("Consulta"); // Flag de consulta (puede venir null)

  Consulta = (Consulta == null) ? false : Consulta;

  if (RAM != null) {
    document.getElementById("RAM-ROTULO").innerHTML = "<strong>" + RAM + "</strong>";
    document.getElementById("RAM-ROTULO").style = "display:inline-block;";
    document.getElementById("RAM").value = RAM;
  } else {
    RAM = '';
    document.getElementById("esEditable").value = 1;
    document.getElementById("RAM-ROTULO").style = "display:none;";
    document.getElementById("RAM").value = "";
  }
  const url = $appcfg_Dominio + "Api_Ram.php";
  // Función interna recursiva que llama por página
  const fetchPagina = (pageTramites, pageUnidades) => {
    let fd = new FormData(document.forms.form1);
    fd.append("action", "get-datosporomisionpaginacion");
    fd.append("RAM", RAM);
    fd.append("RAM_O_EXP", RAM_O_EXP);
    fd.append("page", pageTramites);
    fd.append("pageSize", pageSize);
    fd.append("Consulta", Consulta);

    const options = { method: "POST", body: fd };

    // timeout alto (ya lo tienes), ajusta si deseas
    return fetchWithTimeout(url, options, 1000000)
      .then((response) => response.json())
      .then(function (datos) {
        if (typeof datos.error !== "undefined") {
          if (datos.error == 1003) {
            fSweetAlertEventNormal(
              datos.errorhead,
              '',
              'error',
              datos.error + "- " + datos.errormsg,
              undefined,
              undefined,
              undefined,
              () => reLoadScreen('src/php/referenciales/infoRam.php'),
            );
            return; // corta flujo
          } else if (datos.error == 1100) {
            fSweetAlertEventNormal(
              datos.errorhead,
              '',
              'error',
              datos.error + "- " + datos.errormsg,
              undefined,
              undefined,
              undefined,
              openModalLogin,
            );
            return;
          } else {
            fSweetAlertEventNormal(
              datos.errorhead,
              '',
              'error',
              datos.error + "- " + datos.errormsg
            );
            return;
          }
        }
        //***************************************************************************/
        //* Armando Objeto de Concesiones Salvadas en Preforma
        //***************************************************************************/
        if (typeof datos[5] !== "undefined") {
          // datos[5] => payload principal; datos[7] => (según tu lógica actual)
          guardarConcesionSalvadaPreforma(datos[5], datos[7]);
        }
        //*****************************************
        //*Recuperando Reportes
        //*****************************************
        if (datos?.['Reportes']) { 
          procesarDatosIHTT(datos['Reportes'])
        }        
        // Calcula siguientes páginas
        const nextPageTramites = pageTramites + 1;
        const nextPageUnidades = pageUnidades + 1;
        // Mientras quede AL MENOS una paginación pendiente, sigue llamando
        const quedanTramites  = nextPageTramites <= total_paginas_tramites;
        const quedanUnidades  = nextPageUnidades <= total_paginas_unidades;
        if (concesionNumber.length < 1) {
          document.getElementById("input-prefetch").style.display = "none";
          document.getElementById("toggle-icon").style.display = "none";
          document.getElementById("rightDiv").style.display = "none";
          document.getElementById("rightDivPR").style.display = "none";
        } else {
          if (esEditable() == true) {
            document.getElementById("input-prefetch").style.display = "block";
            document.getElementById("toggle-icon").style.display = "block";
          } else {
            document.getElementById("input-prefetch").style.display = "none";
            document.getElementById("toggle-icon").style.display = "none";
          }
          document.getElementById("rightDiv").style.display = "flex";
          document.getElementById("rightDivPR").style.display = "flex";
        }
        if (quedanTramites) {
          // Avanza 1 y 1, como pediste
          return fetchPagina(nextPageTramites, nextPageUnidades);
        }
        var el = document.getElementById("procesandose_omision");
        if (el) {
          document.getElementById("procesandose_omision").style.display = "none";
        }
        // Si ya no quedan, fin feliz
        return;
      })
      .catch((error) => {
        console.log(error, 'catch f_DataOmisionPaginacion');
        fSweetAlertEventNormal(
          "OPPS",
          "ALGO RARO PASÓ. INTÉNTALO DE NUEVO EN UN MOMENTO. SI EL PROBLEMA PERSISTE CONTACTA AL ADMINISTRADOR DEL SISTEMA",
          "error"
        );
      });
  };
  // Primera llamada: página 1 y 1
  fetchPagina(1, 1);
}

//**************************************************************************************/
//* Cargando la información por default que debe usar el formulario
//**************************************************************************************/
function f_DataOmision() {
  //*****************************************************************************************/
  //* INICIO: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
  //*****************************************************************************************/
  loading(true, currentstep);
  //*****************************************************************************************/
  //* FINAL: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
  //*****************************************************************************************/
  var datos;
  var response;
  // Get the URL parameters from the current page
  const urlParams = new URLSearchParams(window.location.search);
  // Get a specific parameter by name
  var RAM = urlParams.get("RAM"); // Número de RAM
  var Consulta = urlParams.get("Consulta"); // Número de RAM
  if (Consulta == null) {
    Consulta = false;
  }
  if (RAM != null) {
    document.getElementById("RAM-ROTULO").innerHTML =
      "<strong>" + RAM + "</strong>";
    document.getElementById("RAM-ROTULO").style = "display:inline-block;";
    document.getElementById("RAM").value = RAM;
  } else {
    RAM  = '';
    document.getElementById("esEditable").value = 1;
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
  fd.append("page", 1);
  fd.append("pageSize", document.getElementById("pageSize").value);  
  fd.append("Consulta", Consulta);  
  // Fetch options
  const options = {
    method: "POST",
    body: fd,
  };
  // Hace la solicitud fetch con un timeout de 2 minutos
  fetchWithTimeout(url, options, 1000000)
    .then((response) => response.json())
    .then(function (datos) {
      Reportes = '';
      cargadocs=false;
      estanCargadocs=true;
      if (document.getElementById("Ciudad").value.toUpperCase().substring(0, 11) != "TEGUCIGALPA") {
        document.getElementById("cargadocs").style = "display:flex";
        cargadocs=true;
        estanCargadocs=false;
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
        //*******************************************************************************************************************/
        //*Si se recupero la información de apoderado ingresa a este if                                                     */
        //*******************************************************************************************************************/
        if (typeof datos[3] != "undefined" && typeof datos[3][0] != "undefined") {
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
            if (datos[4].length > 0) {
              fLlenarSelect("Departamentos", datos[11], datos[4][0]["ID_Departamento"], false, {
                text: "SELECCIONE UN DEPARTAMENTO",
                value: "-1",
              });
              fLlenarSelect("Municipios", datos[10], datos[4][0]["ID_Municipio"], false, {
                text: "SELECCIONE UN MUNICIPIO",
                value: "-1",
              });
              fLlenarSelect("Aldeas", datos[9], datos[4][0]["ID_Aldea"], false, {
                text: "SELECCIONE UNA ALDEA",
                value: "-1",
              });
            }
            //*******************************************************************************************************************/
            //* Si el elemento 12 existe quiero decir que ya esta convertido a Expediente la FLS
            //*******************************************************************************************************************/
            if (datos?.[12]) {
              document.getElementById("ID_Expediente").value = datos[12];
              document.getElementById("ID_Solicitud").value = datos[12];
              if (datos[4][0]["Usuario_Acepta"] != document.getElementById("User_Name").value) {
                document.getElementById("esUsuarioPropietario").value = true;         
                document.getElementById("btnnext4").style.display = "none";   
                document.getElementById("share").style.display = "inline";   
                document.getElementById("share").setAttribute('title','Asignado a: ' + datos[4][0]["Usuario_Acepta"]);     
              }
              //*******************************************************************************************************************/
              //* Si el aviso de cobro ya no ha sido pagado se inahibilita el boton de siguiente(cerrar en pantalla 4)
              //*******************************************************************************************************************/
              document.getElementById("CodigoAvisoCobro").value = datos[4][0]['CodigoAvisoCobro'];
              document.getElementById("AvisoCobroEstado").value = datos[4][0]['AvisoCobroEstado'];
              document.getElementById("avisocobro").style.display = "inline";   
              document.getElementById("avisocobro").href =  $appcfg_Dominio_Raiz + ":90/api_rep.php?ra=S&action=get-facturaPdf&nu="+datos[4][0]["CodigoAvisoCobro"];   
              document.getElementById("avisocobro").setAttribute('title','Número de Aviso de Cobro: ' + datos[4][0]["CodigoAvisoCobro"]);     
              //*******************************************************************************************************************/
              if (Number(datos[4][0]['AvisoCobroEstado']) != 2) {
                document.getElementById("btnnext4").style.display = "none";
                document.getElementById("avisocobroicon").classList.remove("text-success");
                document.getElementById("avisocobroicon").classList.add("text-error");   
              } else {
                document.getElementById("avisocobroicon").classList.remove("text-error");
                document.getElementById("avisocobroicon").classList.add("text-success");
              }
            } else {
              document.getElementById("ID_Expediente").value = '';
              document.getElementById("ID_Solicitud").value = '';
              if (datos[4][0]["Usuario_Creacion"] != document.getElementById("User_Name").value) {
                document.getElementById("esUsuarioPropietario").value = true;         
                document.getElementById("btnnext4").style.display = "none"; 
                document.getElementById("share").style.display = "inline";     
                document.getElementById("share").setAttribute('title','Creado por: ' + datos[4][0]["Usuario_Creacion"]);     
              }            
            }
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
            document.getElementById("tipopresentacion").value = datos[4][0]["Presentacion_Documentos"];
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
              document.getElementById("ID_Estado_RAM").value = datos[4][0]["Estado_Formulario"];
              document.getElementById("telsoli").value =
                datos[4][0]["Telefono_Solicitante"];
              document.getElementById("emailsoli").value =
                datos[4][0]["Email_Solicitante"];
              document.getElementById("tiposolicitante").value = datos[4][0]["DESC_Solicitante"];
              document.getElementById("tiposolicitante").setAttribute('data-id',datos[4][0]["ID_Tipo_Solicitante"]);
              document.getElementById("Departamentos").value =
                datos[4][0]["ID_Departamento"];
              document.getElementById("ID_Solicitante").value = datos[4][0]["ID"];
              document.getElementById("esEditable").value = datos[4][0]["esEditable"];
              //***********************************************************************************************************************/
              //*Si esta en modo consulta no importa el estado en que se encuentre la solicitud siempre esta en modo editable = false
              //***********************************************************************************************************************/
              if (Boolean(Consulta) == true) { document.getElementById("esEditable").value = false;}
              document.getElementById("esCompartible").value = datos[4][0]["esCompartible"];
              if (esEditable() == false) {
                disabledEdit();
                lockFormElements();   
              }
            } 
            //***************************************************************************/
            //* Estableciento el Link del Expediente Cargado para Trabajarlo
            //***************************************************************************/
            if (typeof datos[8] != "undefined" && datos[8] != false) {
              document.getElementById("fileUploaded").style.display = "block";
              document.getElementById("fileUploadedLink").setAttribute("href", $appcfg_Dominio + datos[8]);
            } else {
              document.getElementById("fileUploaded").style.display = "none";
            }
            //***************************************************************************/
            //* Marcar requicitos
            //***************************************************************************/
            if (typeof datos[6] != "undefined" && datos[6] != false) {
              requicitosRecuperados = true;
              fMarcarRequicitos();
            } else {
              requicitosRecuperados = false;
            }

            if (typeof datos[8] != "undefined" && datos[8] != false) {
                estanCargadocs = true;
            } else {
              if (cargadocs==true) {
                estanCargadocs = false;
              } else {
                estanCargadocs = true;
              }
          }
          var total_tramites = datos?.[13]?.[0]?.['total_tramites'] ?? 0;
          var total_unidades = datos?.[14]?.[0]?.['total_Unidades'] ?? 0;
          //***************************************************************************/
          //* Armando Objeto de Concesiones Salvadas en Preforma
          //***************************************************************************/
          var el = document.getElementById("procesandose_omision");
          if (el) {
            document.getElementById("procesandose_omision").style.display = "flex";
          }
          f_DataOmisionPaginacion(total_tramites, total_unidades,datos[99]);
        } else {
          if (typeof datos[15] != "undefined" && datos[15] != false) {
            document.getElementById("idEstado").innerHTML = $appcfg_icono_de_importante + ' ' + datos[15].Desc_Estado;
          }
          if (datos[1].length > 0) {
            fLlenarSelect("entregadocs", datos[1], null, false, {
              text: "SELECCIONE UN LUGAR DE ENTREGA",
              value: "-1",
            });
          }
        }
      //***************************************************************************************************/
      //* El de if (typeof datos[0] != "undefined") {
      //***************************************************************************************************/
      } else {
        if (typeof datos.error != "undefined") {
          if (datos.error == 1003) {
            fSweetAlertEventNormal(
              datos.errorhead,
              '',
              'error',
              datos.error + "- " + datos.errormsg,
              undefined,
              undefined,
              undefined,
              () => reLoadScreen('src/php/referenciales/infoRam.php'),
            );
          } else {
            if (datos.error == 1100) {
              fSweetAlertEventNormal(
                datos.errorhead,
                '',
                'error',
                datos.error + "- " + datos.errormsg,
                undefined,
                undefined,
                undefined,
                openModalLogin,
              );
            } else {            
              fSweetAlertEventNormal(
                datos.errorhead,
                '',
                'error',
                datos.error + "- " + datos.errormsg
              );
            }
          }
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
      loading(false, currentstep);
      //*****************************************************************************************/
      //* FINAL: Despliega la información del stepper content y oculta el gif de procesando    */
      //*****************************************************************************************/
    })
    .catch((error) => {
      //*****************************************************************************************/
      //* INICIO: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
      //*****************************************************************************************/
      loading(false, currentstep);
      //*****************************************************************************************/
      //* FINAL: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
      //*****************************************************************************************/
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
  loading(true, currentstep);
  //*****************************************************************************************/
  //* FINAL: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
  //*****************************************************************************************/
  let fd = new FormData(document.forms.form1);
  var Tramites = "";
  var url = '';
  // Adjuntando el action al FormData
  if (document.getElementById("ID_Expediente").value == "") {
    // URL del Punto de Acceso a la API
    url = $appcfg_Dominio + "Api_Ram.php";
    fd.append("action", "cerrar-preforma");
    fd.append("idEstado", "IDE-1");
    var text =
      "RAM CERRADA EXITOSAMENTE Y ENVIADA A REVISIÓN LEGAL POR OFICIALES JURIDICOS";
    var cierre = 'FSL';
  } else {
    var text =
      "EXPEDIENTE FINALIZADO EXITOSAMENTE, SE GENERO EL RESPECTIVO AUTOMOTIVADO DE INGRESO Y RESOLUCIÓN";
    fd.append("action", "cerrar-expediente");
    // Enviar el número de Expediente
    fd.append("ID_Expediente", document.getElementById("ID_Expediente").value);
    // Enviar el número de Solicitud
    fd.append("ID_Solicitud", document.getElementById("ID_Solicitud").value);
    fd.append("idEstado", "IDE-2");
    // URL del Punto de Acceso a la API
    url = $appcfg_Dominio + "Api_Exp.php";
    var cierre = 'Expediente';
  }
  // Adjuntando el Concesion y Caracterización al FormData
  fd.append("RAM", document.getElementById("RAM").value);
  const options = {
    method: "POST",
    body: fd,
  };
  
  // Hacer al solicitud fetch con un timeout de 2 minutos
  fetchWithTimeout(url, options, 1000000)
    .then((response) => response.json())
    .then(function (Datos) {
      loading(false, currentstep);
      if (typeof Datos.ERROR != "undefined" || typeof Datos.error != "undefined") {
        fSweetAlertEventSelect(
          "",
          Datos.errorhead + '-' + Datos.errorhead,
          Datos.errormsg,
          "error"
        );
        return true;
      } else {
        //*******************************************************************************************************/
        if (typeof Datos.Placas !== 'undefined') {
          var html = mallaDinamica(
          {
          titulo:"EL EXPEDIENTE PRESENTA UNIDADES CON PLACAS QUE NO SON DEL IHTT",
          name: "UNIDADES CON PLACAS NO IHTT",
          },
          Datos.Placas,
          {},
          {
            title: "text-center fw-bold",
            encabezado: "border-bottom fw-bold p-1 bg-success-subtle text-success-emphasis",
            bodyRow: "border-bottom shadow-sm p-1 bg-body-tertiary tHover",
          },          
          false,
          -1,
          );
          if (html != "") {
            fSweetAlertEventNormal(
            "INFORMACIÓN",
            "FAVOR REVISAR INFORMACIÓN",
            "error",
            html
            );
          }
        } else {
          //*******************************************************************************************************/
          //*INICIO: ENVIO DE MENSAJE DE CIERRE EJECUTADO SATISFACTORIAMENTE
          //*******************************************************************************************************/
          var html = Datos;
          sendToast(
            text,
            $appcfg_milisegundos_toast,
            "",
            true,
            true,
            "top",
            $appcfg_pocision_toast,
            true,
            $appcfg_style_toast,
            function () { },
            "success",
            $appcfg_offset_toast,
            $appcfg_icono_toast
          );
          if (cierre == 'FSL') {
            var title = "SE CERRO SATISFACTORIAMENTE LA PREFORMA " + Datos.SOL2;
            var msg =
            "Su solicitud en línea: <span style='color:#ff8f5e;'>" +
            Datos.SOL2 +
            " Asignada a:" +
            Datos.Nombre_Usuario +
            "/" +
            Datos.Cod_Usuario +
            "</span> se ha guardo.";
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
            //***********************************************************************************/
            //*Lanzar iconos de celebración                                                     */
            //***********************************************************************************/
            startCelebration();

            fSweetAlertEventNormal(
              title,
              undefined,
              "info",
              html,
              undefined,
              undefined,
              'FINALIZAR',
              () => reLoadScreen('src/php/referenciales/infoRam.php'));
          } else {
            var html = Datos.AutoIngreso + "<br/>";
            html += Datos.Resolucion + "<br/>";
            html += Datos.Comprobante + "<br/>";
            html += Datos.Portada + "<br/>";
            console.log(Datos.Concesion);
            if (Datos.Concesion && Datos.Concesion != '') {
              html += Datos.Concesion + "<br/>";
            }
            console.log(Datos.ConcesionesExplotacion);
            if (Datos.ConcesionesExplotacion && Datos.ConcesionesExplotacion != '') {
              html += Datos.ConcesionesExplotacion + "<br/>";
            }
            //***********************************************************************************/
            //*Lanzar iconos de celebración                                                     */
            //***********************************************************************************/
            startCelebration();
            fSweetAlertEventNormal(
              title,
              undefined,
              "info",
              html,
              undefined,
              undefined,
              'FINALIZAR',
              () => reLoadScreen('src/php/referenciales/infoRam.php'));
          }
        }
      }
    })
    .catch((error) => {
      //*****************************************************************************************/
      //* INICIO: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
      //*****************************************************************************************/
      loading(false, currentstep);
      //*****************************************************************************************/
      //* FINAL: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
      //*****************************************************************************************/
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
  if (document.getElementById("ID_Expediente").value == "") {
    let text =
      "¿DESEA CERRAR LA PREFORMA: '  + document.getElementById('RAM').value + 'Y GENERAR LOS DOCUMENTOS CORRESPONDIENTES?";
  } else{
    let text =
      "¿DESEA CERRAR EL EXPEDIENTE: '  + document.getElementById('ID_Expediente').value + ' ACTUALIZAR CONCESIONES Y GENERAR LOS DOCUMENTOS CORRESPONDIENTES(AUTOMOTIVADO, RESOLUCIÓN)])?";
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
  btn.addEventListener("click", async function () {
    var error = false;
    if (currentstep == 4) {
      //************************************************************************/
      //* INICIO: Cerrar proceso de registro de conseciones y/o Expediente
      //************************************************************************/
      fCerrarProceso();
      //************************************************************************/
      //* FINAL: Cerrar proceso de registro de conseciones y/o Expediente
      //************************************************************************/
    } else {
      if (currentstep == 3) {
        if (esEditable()==true) {
          //************************************************************************************/
          //* INICIO: Salvado de Requicitos si no se han salvado y validaciones de pantalla
          //************************************************************************************/
          const CA = document.getElementById("flexSwitchCheckContratoArrendamiento");
          if ((esCambioDeVehiculo == false &&
              document.getElementById("concesion_rtn").textContent != '' && 
              document.getElementById("concesion_rtn").textContent != document.getElementById("concesion_identidad_propietario").textContent &&
              CA.checked == false) &&
              (esCambioDeVehiculo == true &&
                document.getElementById("concesion_rtn").textContent != '' && 
                document.getElementById("concesion_rtn").textContent != document.getElementById("concesion1_identidad_propietario").textContent &&
                CA.checked == false)) {
              fSweetAlertEventNormal(
                "SALVANDO",
                "EL DUEÑO DE LA UNIDAD ES DISTINTO AL CONCESIONARIO, FAVOR SELECCION EL CONTRATO DE ARRENDAMIENTO",
                "error"
              );
              error = true;
          } else {
            if (estanCargadocs==false) {
              fSweetAlertEventNormal(
                "SALVANDO",
                "DEBE CARGAR EL ARCHIVO PDF DEL EXPEDIENTE",
                "error"
              );
              error = true;
            } else {
              if (requicitosRecuperados == false) {
                error = await salvarRequicitos();
              }
            }
          } 
          //************************************************************************/
          //* INICIO: Salvado de Requicitos si no se han salvado y validaciones de pantalla
          //************************************************************************/
        }
      } else {
        if (currentstep == 2) {
            if (seRecuperoVehiculoDesdeIP != 0) {
              Swal.fire({
                title: "!INCONVENIENTES CON VEHICULO¡",
                text: "HA REALIZADO EL TRAMITE DE CAMBIO DE PLACA O CAMBIO DE UNIDAD DEBE RECUPERAR LA INFORMACIÓN DEL IP Y SALVAR LA PARA PODER CONTINUAR",
                icon: "warning",
                confirmButtonText: "OK",
              });
              error = true;
            } else {
                if (document.getElementById('concesion_concesion').textContent != '') {
                  isSaving = true;
                  fGetInputs();
                  isSaving = false;
              }
            }
          //stepperForm.next();
        } else {
          if (currentstep == 0 || currentstep == 1) {
            fGetInputs();
            const sum = paneerror[currentstep].reduce((acc, val) => acc + val, 0);
            if (sum > 0) { error = true;}
          }
        }
      }
    }
    
    if (error === false) {
      stepperForm.next();
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
      if (esEditable() == true) {
        document.getElementById("input-prefetch").style.display = "block";
        document.getElementById("toggle-icon").style.display = "block";
      }
      document.getElementById("rightDiv").style.display = "none";
      document.getElementById("rightDivPR").style.display = "none";
      document.getElementById("combustible").value = "";
      document.getElementById("capacidad").value = "";

      ["alto", "largo", "ancho", "alto1", "largo1", "ancho1"].forEach(id => {
          const el = document.getElementById(id);
          if (el) el.value = "";
      });

      document.getElementById("combustible1").value = "";
      document.getElementById("capacidad1").value = "";
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
      document.getElementById("concesion1_identidad_propietario").innerHTML ="";
      document.getElementById("concesion_placaanterior").innerHTML = "";
      document.getElementById("concesion_placaanterior").style = "display:none";
      document.getElementById("concesion1_placaanterior").innerHTML = "";
      document.getElementById("concesion1_placaanterior").style = "display:none";
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
      ////showConcesionTramites();
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
    if (currentstep == 0) {
      isSaving = true;
      fGetInputs();
      isSaving = false;
    } else {        
      if (currentstep == 1) {
        isSaving = true;
        fGetInputs();
        isSaving = false;
      } else {    
        if (currentstep == 2) {
            if (document.getElementById("concesion_concesion").textContent != '') {
              if (seRecuperoVehiculoDesdeIP != 0) {
                Swal.fire({
                  title: "!INCONVENIENTES CON VEHICULO¡",
                  text: "HA REALIZADO EL TRAMITE DE CAMBIO DE PLACA O CAMBIO DE UNIDAD DEBE RECUPERAR LA INFORMACIÓN DEL IP Y SALVAR LA PARA PODER CONTINUAR",
                  icon: "warning",
                  confirmButtonText: "OK",
                });
                error = true;
              } else {
                  if (document.getElementById('concesion_concesion').textContent != '') {
                    isSaving = true;
                    fGetInputs();
                    isSaving = false;
                }
              }
            }
        } else {
          if (currentstep == 3) {
            //************************************************************************************/
            //* INICIO: Salvado de Requicitos si no se han salvado y validaciones de pantalla
            //************************************************************************************/
            const CA = document.getElementById("flexSwitchCheckContratoArrendamiento");
            if ((esCambioDeVehiculo == false &&
                document.getElementById("concesion_rtn").textContent != '' && 
                document.getElementById("concesion_rtn").textContent != document.getElementById("concesion_identidad_propietario").textContent &&
                CA.checked == false) &&
                (esCambioDeVehiculo == true &&
                  document.getElementById("concesion_rtn").textContent != '' && 
                  document.getElementById("concesion_rtn").textContent != document.getElementById("concesion1_identidad_propietario").textContent &&
                  CA.checked == false)) {
                fSweetAlertEventNormal(
                  "SALVANDO",
                  "EL DUEÑO DE LA UNIDAD ES DISTINTO AL CONCESIONARIO, FAVOR SELECCION EL CONTRATO DE ARRENDAMIENTO",
                  "error"
                );
                error = true;
            } else {
              if (estanCargadocs==false) {
                fSweetAlertEventNormal(
                  "SALVANDO",
                  "DEBE CARGAR EL ARCHIVO PDF DEL EXPEDIENTE",
                  "error"
                );
                error = true;
              }
            }
          }
        }
      }
    }
    //******************************************************************************************************/
    //* Si no tiene ningun error previo en la validación se entra al siguiente if y se mueve al siguiente pane
    //******************************************************************************************************/
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
  fetchWithTimeout(url, options, 300000)
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
            $appcfg_icono_de_success + " INFORMACIÓN DEL APODERADO RECUPERADA EXITOSAMENTE",
            $appcfg_milisegundos_toast,
            "",
            true,
            true,
            "top",
            $appcfg_pocision_toast,
            true,
            $appcfg_style_toast,
            function () { },
            "success",
            $appcfg_offset_toast,
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
  fetchWithTimeout(url, options, 300000)
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
          document.getElementById("tiposolicitante").value =datos[1].DESC_Solicitante;
          document.getElementById("tiposolicitante").setAttribute("data-id", datos[1].ID_Tipo_Solicitante);
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
            $appcfg_icono_de_success + " INFORMACIÓN DEL SOLICITANTE RECUPERADA EXITOSAMENTE",
            $appcfg_milisegundos_toast,
            "",
            true,
            true,
            "top",
            $appcfg_pocision_toast,
            true,
            $appcfg_style_toast,
            function () { },
            "success",
            $appcfg_offset_toast,
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
    document.getElementById("Concesion_Encriptada").value = datos[1][0]["PermisoEspecialEncriptado:"];
    document.getElementById("Permiso_Explotacion").value = "";
    document.getElementById("Permiso_Explotacion_Encriptado").value = "";
  }

  document.getElementById("concesion_tramites").innerHTML =  datos[1][0]["Tramites"];
  //*****************************************************************************************/
  //*Presentar la llama de tramites asociadas a la concesion
  //*****************************************************************************************/
  fShowTramites(true);
  document.getElementById("rightDivTR").style.display = "flex";
  //*****************************************************************************************/
  //* Activar Eventos para inputs concesion_tramite_placa_CL y concesion_tramite_placa_CU
  //*****************************************************************************************/
  setaddEventListener();
  seRecuperoVehiculoDesdeIP = 0;
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
      document.getElementById("concesion_placaanterior").style = "display:inline;";
      document.getElementById("concesion_placaanterior").innerHTML = datos[1][0]["Unidad"][0]["ID_Placa_Anterior"];
    } else {
      document.getElementById("concesion_placaanterior").style ="display:none;";
      document.getElementById("concesion_placaanterior").innerHTML = "";
    }
    //***********************************************************************************************************************************/
    //* INICIO:
    //* Marcando tramites obligatrios dependiendo de las condciones de vencimiento de las concesiones y permisos de explotacion
    //* y si ya se pago el cambio de placa de la unidad actual, si tuvo cambio de placa
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
    document.getElementById("estaPagadoElCambiodePlaca").value = datos[1][0]["Unidad"][0]["estaPagadoElCambiodePlaca"];
    if ( Boolean(datos[1][0]["Unidad"][0]["estaPagadoElCambiodePlaca"]) == false) {
      document.getElementById("IHTTTRA-03_CLATRA-15_M_CL").checked = true;
      //*document.getElementById("IHTTTRA-03_CLATRA-15_M_CL").disabled = true;
      document.getElementById("concesion_tramite_placa_CL").style = "display:flex;text-transform: uppercase;";
      document.getElementById("concesion_tramite_placa_CL").value = datos[1][0]["Unidad"][0]["ID_Placa"];
      document.getElementById("concesion_tramite_placa_CL").setAttribute("readonly", true);
      esCambioDePlaca = true;
      requiereCambioDePlaca = true;
    } else {
      esCambioDePlaca = false;
      requiereCambioDePlaca = false;
      document.getElementById("IHTTTRA-03_CLATRA-15_M_CL").checked = false;
      document.getElementById("IHTTTRA-03_CLATRA-15_M_CL").disabled = false;
      document.getElementById("concesion_tramite_placa_CL").style = "display:none;text-transform: uppercase;";
    }

    if (datos[1][0]["Vencimientos"]["renovacion_certificado_vencido"] == true) {
      document.getElementById("RequiereRenovacionConcesion").value = true;
      var rco = document.getElementById("IHTTTRA-02_CLATRA-02_R_CO");
      if (rco) {
        rco.checked = true;
        rco.disabled = true;
        document.getElementById("row_tramite_X_CO").style.display = "none";
      }
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
    } else {
      if (
        datos[1][0]["Vencimientos"]["renovacion_permiso_especial_vencido"] ==
        true
      ) {
        document.getElementById("RequiereRenovacionConcesion").value = true;
        var rps = document.getElementById("IHTTTRA-02_CLATRA-03_R_PS");
        if (rps) {
          rps.checked = true;
          rps.disabled = true;
          //Inicio
          document.getElementById("row_tramite_X_PS").style.display = "none";
        }
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
        //Final
      }
    }
    document.getElementById("RequiereRenovacionPerExp").value = datos[1][0]["Vencimientos"]["renovacion_permisoexplotacion_vencido"];
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
  ////showConcesionTramites(true);
  //***********************************************************************************************************************************/
  //* Cargando Información en la Pantalla 2 de Vehiculo Entra (Por si Hay cambio de Unidad)
  //***********************************************************************************************************************************/
  f_RenderConcesionTramites();
}

function hideAltoAnchoLargo() {
  if (claseDeServicio == "STPP" || claseDeServicio == "STPC") {
    if (claseDeServicio == "STPP") {
      var el = document.getElementById('alto');
      if (el) {el.setAttribute('disabled',true);}
      el = document.getElementById('ancho');
      if (el) {el.setAttribute('disabled',true);}
      el = document.getElementById('largo');
      if (el) {el.setAttribute('disabled',true);}
      el = document.getElementById('alto1');
      if (el) {el.setAttribute('disabled',true);}
      el = document.getElementById('ancho1');
      if (el) {el.setAttribute('disabled',true);}
      el = document.getElementById('largo1');      
      if (el) {el.setAttribute('disabled',true);}
      el = document.getElementById('rowalto');      
      if (el) {el.style = 'display:none';}      
      el = document.getElementById('rowalto1');      
      if (el) {el.style = 'display:none';}  
      el = document.getElementById('rowancho');      
      if (el) {el.style = 'display:none';}                      
      el = document.getElementById('rowancho1');      
      if (el) {el.style = 'display:none';}                            
      document.getElementById('capacidadenlabel').innerHTML = 'Pasajeros';
      document.getElementById('capacidad1enlabel').innerHTML = 'Pasajeros';
      document.getElementById('capacidad').setAttribute('title','La capacidad de la unidad no puede tener menos de 1 caracter ni mas de 2 caracteres');
      document.getElementById('capacidad').setAttribute('pattern','^[1-9][0-9]?$');
      document.getElementById('capacidad1').setAttribute('title','La capacidad de la unidad no puede tener menos de 1 caracter ni mas de 2 caracteres');
      document.getElementById('capacidad1').setAttribute('pattern','^[1-9][0-9]?$');
      document.getElementById('capacidad1').setAttribute('minlength',1);
      document.getElementById('capacidad1').setAttribute('maxlength',2);
      document.getElementById('capacidad').setAttribute('minlength',1);
      document.getElementById('capacidad').setAttribute('maxlength',2);
    } 
  } else {
    if (claseDeServicio == "STEP") {
      var el = document.getElementById('alto');
      if (el) {el.remove();}
      el = document.getElementById('ancho');
      if (el) {el.remove();}
      el = document.getElementById('largo');
      if (el) {el.remove();}
      el = document.getElementById('alto1');
      if (el) {el.remove();}
      el = document.getElementById('ancho1');
      if (el) {el.remove();}
      el = document.getElementById('largo1');      
      if (el) {el.remove();}
      document.getElementById('capacidadenlabel').textContent = 'Pasajeros';
      document.getElementById('capacidad1enlabel').textContent = 'Pasajeros';
      document.getElementById('capacidad1').setAttribute('title','La capacidad de la unidad no puede tener menos de 1 caracter ni mas de 2 caracteres');
      document.getElementById('capacidad1').setAttribute('pattern','^[1-9][0-9]?$');
      document.getElementById('capacidad').setAttribute('title','La capacidad de la unidad no puede tener menos de 1 caracter ni mas de 2 caracteres');
      document.getElementById('capacidad').setAttribute('pattern','^[1-9][0-9]?$');
      document.getElementById('capacidad1').setAttribute('minlength',1);
      document.getElementById('capacidad1').setAttribute('maxlength',2);
      document.getElementById('capacidad').setAttribute('minlength',1);
      document.getElementById('capacidad').setAttribute('maxlength',2);
    }
  }
}
//*********************************************************************************************************************/
//** Inicio Function Carga Los Datos de la Unidad1 y Presenta la Pantalla con dicha información
//*********************************************************************************************************************/
function f_RenderConcesionTramites() {
  document.getElementById("concesionlabel1").textContent = document.getElementById("concesionlabel").textContent;
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
    document.getElementById("concesion1_placaanterior").innerHTML = document.getElementById("concesion_placaanterior").innerHTML;
    document.getElementById("concesion1_placaanterior").style = "display:inline;";
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
    { text: "SELECCIONE UNA MARCA", value: "-1" }
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

  if (document.getElementById("alto")?.value !== undefined) {
    document.getElementById("alto1").value =  document.getElementById("alto").value;
    document.getElementById("largo1").value =   document.getElementById("largo").value;
    document.getElementById("ancho1").value =  document.getElementById("ancho").value;
  }

}
//*********************************************************************************************************************/
//** Inicio Function para Establecer la Unidad de los Tramites                                                        **/
//*********************************************************************************************************************/
function f_FetchCallConcesion(idConcesion, event, idinput) {
  //*****************************************************************************************/
  //* INICIO: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
  //*****************************************************************************************/
  loading(true, currentstep);
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
  fetchWithTimeout(url, options, 300000)
    .then((response) => response.json())
    .then(function (datos) {
      if (typeof datos[0] != "undefined") {
        if (datos[0] > 0) {
          //******************************************************************************************************************************/
          //* Si el vehiculo fue recuperado desde el IP, favor continuar con el proceso normal y mover al informacion a la pantalla
          //******************************************************************************************************************************/
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
                document.getElementById("btnCambiarUnidad").style.display = "none";
                document.getElementById("concesion_tramites").value = "";
                f_RenderConcesion(datos);
                seRecuperoVehiculoDesdeIP = 0;
                //****************************************************************************************************/
                //*Enviando Toast de Exito en Recuperación la Información de la Concesión
                //****************************************************************************************************/
                sendToast(
                  $appcfg_icono_de_success + "INFORMACIÓN DE LA CONCESIÓN RECUPERADA EXITOSAMENTE",
                  $appcfg_milisegundos_toast,
                  "",
                  true,
                  true,
                  "top",
                  $appcfg_pocision_toast,
                  true,
                  $appcfg_style_toast,
                  function () { },
                  "success",
                  $appcfg_offset_toast,
                  $appcfg_icono_toast
                );
                //**********************************************************************************/
                //* Inicio: Si el expediente esta en un estado que no es editable
                //**********************************************************************************/
                if (esEditable() == false) {
                  disabledEdit();
                  lockFormElements();   
                } else {
                  document.getElementById("btnSalvarConcesion").style = "display:fixed;";
                }
                //************************************************************************************/
                //* Inicio: Modalidad de Entrada I = INSERT
                //************************************************************************************/
                modalidadDeEntrada = "I";
                var btn = document.getElementById("btnModalidad");
                if (btn) {
                  btn.style.display = "flex";
                  btn.setAttribute("data-bs-original-title", "La modalidad de entrada de datos: INSERTANDO");
                  btn.innerHTML = '<i class="fas fa-plus-circle fa-2x gobierno1"></i>';
                  document.getElementById("btnModalidad1").setAttribute("data-bs-original-title", "La modalidad de entrada de datos: INSERTANDO");
                  document.getElementById("btnModalidad1").innerHTML = '<i class="fas fa-plus-circle fa-2x gobierno1"></i>';
                  document.getElementById("btnModalidad1").style.display = "flex";
                }
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

                if (unidad?.["Expedientes"]?.[0]) {
                  html =
                    html +
                    mallaDinamica(
                      {
                        titulo:
                          "CERTIFICADO Y/O UNIDAD(ENTRA)  TIENEN EXPEDIENTES EN TRAMITE",
                        name: "EXPEDIENTES",
                      },
                      unidad["Expedientes"],                      
                      {},
                      {
                      title: "text-center fw-bold",
                      encabezado: "border-bottom fw-bold p-1 bg-success-subtle text-success-emphasis",
                      bodyRow: "border-bottom shadow-sm p-1 bg-body-tertiary tHover",
                      },
                      $appcfg_Dominio_Raiz + ':85/Detalle_Expediente.php?idExpediente=@@__0__@@&idSolicitud=@@__1__@@',
                      99,
                    );
                }
                
                if (unidad?.["Preforma"]?.[0]) {
                  html =
                    html +
                    mallaDinamica(
                      {
                        titulo: "PREFORMAS PENDIENTES DE RESOLUCIÓN",
                        name: "PREFORMA",
                      },
                      unidad["Preforma"],
                      {},
                      {
                      title: "text-center fw-bold",
                      encabezado: "border-bottom fw-bold p-1 bg-success-subtle text-success-emphasis",
                      bodyRow: "border-bottom shadow-sm p-1 bg-body-tertiary tHover",
                      },
                      $appcfg_Dominio + 'ram.php?consulta=true&RAM=',
                      1,
                    );
                }

                var links = $appcfg_Dominio_Raiz + LinksConsulta.get(document.getElementById("ID_Clase_Servicio").value);

                if (unidad?.["Placas"]?.[0]) {
                  html =
                    html +
                    mallaDinamica(
                      {
                        titulo:
                          "CERTIFICADO Y/O UNIDADES TIENEN DOCUMENTOS PARA IMPRESIÓN Y/O ENTREGA",
                        name: "DOCUMENTOS/EXPEDIENTES",
                      },
                      unidad["Placas"],
                      {},
                      {
                      title: "text-center fw-bold",
                      encabezado: "border-bottom fw-bold p-1 bg-success-subtle text-success-emphasis",
                      bodyRow: "border-bottom shadow-sm p-1 bg-body-tertiary tHover",
                      },
                      links,
                      0
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
                //****************************************************************************************************/
                //* Inicializar tomselect                                                                            */
                //****************************************************************************************************/
                inicialitarTomSelect();
                hideAltoAnchoLargo();
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
      loading(false, currentstep);
      //*****************************************************************************************/
      //* FINAL: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
      //*****************************************************************************************/
    })
    .catch((error) => {
      //*****************************************************************************************/
      //* INICIO: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
      //*****************************************************************************************/
      loading(false, currentstep);
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
  loading(true, currentstep);
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
  if (document.getElementById("ID_Expediente").value == '') {
    fd.append("action", "get-concesion-preforma");
  } else {
    fd.append("action", "get-concesion-expediente");
  }
  fd.append("RAM", document.getElementById("RAM").value);
  fd.append("idConcesion", idConcesion);
  // Fetch options
  const options = {
    method: "POST",
    body: fd,
  };
  // Hace la solicitud fetch con un timeout de 2 minutos
  fetchWithTimeout(url, options, 300000)
    .then((response) => response.json())
    .then(function (datos) {
      if (datos?.[0] > 0) {
        if (typeof datos.error != "undefined") {
          fLimpiarPantalla();
          fSweetAlertSelect(
            datos.errorhead,
            datos.error + "- " + datos.errormsg,
            "error"
          );
        } else {
          const unidad = datos[1]?.[0]?.["Unidad"]?.[0];
          //***********************************************************************************/
          //**Presentando la tabla de tramites                                                */
          //***********************************************************************************/
          if (esEditable() == true) {
            document.getElementById("btnSalvarConcesion").style ="display:fixed;";
          }
          document.getElementById("concesion_tramites").value = "";
          f_RenderConcesionPreforma(datos);
          //**************************************************************************************************************************/
          //*Enviando Toast de Exito en Recuperación la Información de la Concesión
          //**************************************************************************************************************************/
          sendToast(
            $appcfg_icono_de_success + " INFORMACIÓN DE LA CONCESIÓN RECUPERADA EXITOSAMENTE DE PREFORMA",
            $appcfg_milisegundos_toast,
            "",
            true,
            true,
            "top",
            $appcfg_pocision_toast,
            true,
            $appcfg_style_toast,
            function () { },
            "success",
            $appcfg_offset_toast,
            $appcfg_icono_toast
          );
          //**************************************************************************************************************************/
          //* Inicio: Modalidad de Entrada U = UPDATE
          //**************************************************************************************************************************/
          modalidadDeEntrada = "U";
          var btn = document.getElementById("btnModalidad");
          if (btn) {
            btn.style.display = "flex";
            btn.setAttribute("data-bs-original-title", "La modalidad de entrada de datos: EDITANDO");
            btn.innerHTML = '<i class="fas fa-edit fa-2x gobierno1"></i>';
            document.getElementById("btnModalidad1").setAttribute("data-bs-original-title", "La modalidad de entrada de datos: EDITANDO");
            document.getElementById("btnModalidad1").innerHTML = '<i class="fas fa-edit fa-2x gobierno1"></i>';
            document.getElementById("btnModalidad1").style.display = "flex";
          }
          //**************************************************************************************************************************/
          //* Final: Modalidad de Entrada U = UPDATE
          //**************************************************************************************************************************/
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

          if (unidad?.["Expedientes"]?.[0]) {
            html =
              html +
              mallaDinamica(
                {
                  titulo:
                    "CERTIFICADO Y/O UNIDAD(ENTRA) TIENEN EXPEDIENTES EN TRAMITE",
                  name: "EXPEDIENTES",
                },
                unidad["Expedientes"],                      
                {},
                {
                title: "text-center fw-bold",
                encabezado: "border-bottom fw-bold p-1 bg-success-subtle text-success-emphasis",
                bodyRow: "border-bottom shadow-sm p-1 bg-body-tertiary tHover",
                },
                $appcfg_Dominio_Raiz + ':85/Detalle_Expediente.php?idExpediente=@@__0__@@&idSolicitud=@@__1__@@',
                99,
              );
          }

          if (unidad?.["Preforma"]?.[0]) {
            html =
              html +
              mallaDinamica(
                {
                  titulo: "PREFORMAS PENDIENTES DE RESOLUCIÓN",
                  name: "PREFORMA",
                },
                unidad["Preforma"],
                {},
                {
                title: "text-center fw-bold",
                encabezado: "border-bottom fw-bold p-1 bg-success-subtle text-success-emphasis",
                bodyRow: "border-bottom shadow-sm p-1 bg-body-tertiary tHover",
                },
                $appcfg_Dominio + 'ram.php?consulta=true&RAM=',
                1,
              );
          }

          if (unidad?.["Placas"]?.[0]) {
            var links = $appcfg_Dominio_Raiz + LinksConsulta.get(document.getElementById("ID_Clase_Servicio").value);
            html =
              html +
              mallaDinamica(
                {
                  titulo:
                    "CERTIFICADO Y/O UNIDADES TIENEN DOCUMENTOS PARA IMPRESIÓN Y/O ENTREGA",
                  name: "DOCUMENTOS/EXPEDIENTES",
                },
                unidad["Placas"],
                {},
                {
                  title: "text-center fw-bold",
                  encabezado: "border-bottom fw-bold p-1 bg-success-subtle text-success-emphasis",
                  bodyRow: "border-bottom shadow-sm p-1 bg-body-tertiary tHover",
                },
                links,
                0
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
          //****************************************************************************************************/
          //* Inicializar tomselect                                                                            */
          //****************************************************************************************************/
          inicialitarTomSelect();
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
          if (esEditable() == false) {
            disabledEdit();
            lockFormElements();   
          }

          //*****************************************************************************************/
          //* INICIO: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
          //*****************************************************************************************/
          loading(false, currentstep);
          //*****************************************************************************************/
          //* FINAL: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
          //*****************************************************************************************/
          hideAltoAnchoLargo();
        }
      } else {
        if (typeof datos.error != "undefined") {
          document.getElementById("txt_clave_desbloqueo").removeAttribute("disabled");
          document.getElementById("txt_usuario_desbloqueo").removeAttribute("disabled");
          fLimpiarPantalla();
          fSweetAlertEventNormal(
            datos.errorhead,
            undefined,
            "error",
            datos.error + "- " + datos.errormsg,
            undefined,
            undefined,
            'IR A INICIO DE SESIÓN',
            openModalLogin);
        } else {
          fLimpiarPantalla();
          fSweetAlertSelect(
            "INFORMACIÓN",
            "ERROR DESCONOCIDO, INTENTELO DE NUEVO EN UN MOMENTO, SI EL ERROR PERSISTE CONTACTE AL ADMINISTRADOR DEL SISTEMA",
            "error"
          );        
        }
      }
    })
    .catch((error) => {
      //*****************************************************************************************/
      //* INICIO: Despliega u Oculta la información del stepper content y oculta el gif de procesando    */
      //*****************************************************************************************/
      loading(false, currentstep);
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

  setTimeout(() => {
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
  }, 1000);

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
  //* Presentar tramites asociados a la concesion actual 
  //*************************************************************************************************************/
  fShowTramites(true);
  document.getElementById("rightDivTR").style.display = "flex";
  //*************************************************************************************************************/
  //* Llamar funcion que habilita los Listener de las Placas en el caso de cambio de placa y/o cambio de unidad
  //*************************************************************************************************************/
  setaddEventListener();

  seRecuperoVehiculoDesdeIP = 0;

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
      document.getElementById("concesion_placaanterior").innerHTML =   datos[1][0]["Unidad"][0]["ID_Placa_Antes_Replaqueo"];
    } else {
      document.getElementById("concesion_placaanterior").style = "display:none;";
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
    document.getElementById("estaPagadoElCambiodePlaca").value = datos[1][0]["Unidad"][0]["estaPagadoElCambiodePlaca"];
    if (document.getElementById("IHTTTRA-03_CLATRA-15_M_CL").checked == true) {

      if ( Boolean(document.getElementById("estaPagadoElCambiodePlaca").value) == false) {
        //*document.getElementById("IHTTTRA-03_CLATRA-15_M_CL").disabled = true;
        document.getElementById("concesion_tramite_placa_CL").removeAttribute("readonly");
        requiereCambioDePlaca = false;
      } else {
        requiereCambioDePlaca = true;
      }
      document.getElementById("concesion_tramite_placa_CL").style = "display:flex;text-transform: uppercase;";
      document.getElementById("concesion_tramite_placa_CL").value =  datos[1][0]["Unidad"][0]["ID_Placa"];
      if (requiereCambioDePlaca == true) {
        //*document.getElementById("concesion_tramite_placa_CL").setAttribute("readonly", true);
        esCambioDePlaca = true;
      } else {
        esCambioDePlaca = false;
      }
      
    } else {
      esCambioDePlaca = false;
      document.getElementById("IHTTTRA-03_CLATRA-15_M_CL").disabled = false;
      document.getElementById("concesion_tramite_placa_CL").style = "display:none;text-transform: uppercase;";
      document.getElementById("concesion_tramite_placa_CL").removeAttribute("readonly");
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
    document.getElementById("ID_Clase_Servicio").value = datos[1][0]["ID_Clase_Servico"];
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

    if (datos[1][0]["ID_Clase_Servico"] == "STPC" || datos[1][0]["ID_Clase_Servico"] == "STEC") {
      document.getElementById("capacidad").value = datos[1][0]["Unidad"][0]["Capacidad_Carga"];
    } else {
      document.getElementById("capacidad").value = parseInt(datos[1][0]["Unidad"][0]["Capacidad_Carga"]);
    }
    
    if (document.getElementById("alto")?.value !== undefined) {
      document.getElementById("alto").value = datos[1][0]["Unidad"][0]["Alto"];
      document.getElementById("largo").value = datos[1][0]["Unidad"][0]["Largo"];
      document.getElementById("ancho").value = datos[1][0]["Unidad"][0]["Ancho"];
    }

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
  document.getElementById("concesionlabel1").textContent = document.getElementById("concesionlabel").textContent;
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

  if (datos[1][0]["ID_Clase_Servico"] == "STPC" || datos[1][0]["ID_Clase_Servico"] == "STEC") {
    document.getElementById("capacidad1").value = datos[1][0]["Unidad"][1]["Capacidad_Carga"];
  } else {
    document.getElementById("capacidad1").value = parseInt(datos[1][0]["Unidad"][1]["Capacidad_Carga"]);
  }

  if (document.getElementById("alto1")?.value !== undefined) {   
    document.getElementById("alto1").value = datos[1][0]["Unidad"][1]["Alto"];
    document.getElementById("largo1").value = datos[1][0]["Unidad"][1]["Largo"];
    document.getElementById("ancho1").value = datos[1][0]["Unidad"][1]["Ancho"];
  }
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
  document.getElementById("btnCambiarUnidad").style = "display:flex; position: absolute; top: 195px; right: 25px; padding: 10px;";
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
    var Permiso_Explotacion = document.getElementById("concesion_perexp").innerHTML.split("||")[0];
    var Permiso_Especial = "";
  } else {
    var Permiso_Especial = document.getElementById("concesion_concesion").innerHTML;
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
    ID_Aviso_Cobro: document.getElementById("CodigoAvisoCobro").value,
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
    Capacidad: document.getElementById("capacidad")?.value || 0,
    Alto: document.getElementById("alto")?.value || 0,
    Largo: document.getElementById("largo")?.value || 0,
    Ancho: document.getElementById("ancho")?.value || 0,
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
    Capacidad: document.getElementById("capacidad1")?.value || 0,
    Alto: document.getElementById("alto1")?.value || 0,
    Largo: document.getElementById("largo1")?.value || 0,
    Ancho: document.getElementById("ancho1")?.value || 0,
    Nombre_Propietario: document.getElementById("concesion1_nombre_propietario")
      .innerHTML,
    RTN_Propietario: document.getElementById("concesion1_identidad_propietario")
      .innerHTML,
    Modelo: document.getElementById("concesion1_modelo_vehiculo").value,
    Tipo: document.getElementById("concesion1_tipo_vehiculo").value,
    ID_Placa_Antes_Replaqueo: document.getElementById("concesion1_placaanterior").innerHTML,
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
          Cantidad_Vencimientos = document.getElementById("CantidadRenovacionesPerExp").value;
          Fecha_Expiracion_Nueva = document.getElementById("NuevaFechaVencimientoPerExp").value;
          Fecha_Expiracion = document.getElementById("NuevaFechaVencimientoPerExp").value;
        } else {
          if (
            chk.id === "IHTTTRA-02_CLATRA-02_R_CO" ||
            chk.id === "IHTTTRA-02_CLATRA-03_R_PS"
          ) {
            Cantidad_Vencimientos = document.getElementById("CantidadRenovacionesConcesion").value;
            Fecha_Expiracion_Nueva = document.getElementById("NuevaFechaVencimientoConcesion").value;
            Fecha_Expiracion = document.getElementById("FechaVencimientoConcesion").value;
          }
        }

        TramitesPreforma.push({
          ID: getAttribute(chk, "data-iddb", false),
          ID_Compuesto: chk.id,
          Codigo: chk.value,
          descripcion: document.getElementById("descripcion_" + chk.value).innerHTML,
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
function guardarConcesionSalvadaPreforma(Tramites, Unidades, actualizar=false) {
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
      esCarga = Boolean(Number(row["esCarga"]));
      esCertificado = Boolean(Number(row["esCertificado"]));      
      Placa = row["ID_Placa"];
      var Cantidad_Vencimientos = 1;
      var Fecha_Expiracion_Nueva = "";
      var Fecha_Expiracion = "";
      if (
        row["ID_CHECK"] == "IHTTTRA-02_CLATRA-01_R_PE" &&
        row["Vencimientos"] != false
      ) {
        Fecha_Expiracion = row["Fecha_Expiracion_Explotacion"];
        Cantidad_Vencimientos = row["Vencimientos"]["renper-explotacion-cantidad"];
        Fecha_Expiracion_Nueva = row["Vencimientos"]["Nueva_Fecha_Expiracion_Explotacion"];
      } else {
        console.log(2,'2');
        if (
          (row["ID_CHECK"] == "IHTTTRA-02_CLATRA-02_R_CO" ||
           row["ID_CHECK"] == "IHTTTRA-02_CLATRA-03_R_PS") &&
           row["Vencimientos"] != false
        ) {
          console.log(3,'3');
          Fecha_Expiracion = row["Fecha_Expiracion"];
          console.log(31,'31');
          Cantidad_Vencimientos = row?.["Vencimientos"]?.["rencon-cantidad"] ?? 0;
          console.log(32,'32');
          Fecha_Expiracion_Nueva = row?.["Vencimientos"]?.["Nueva_Fecha_Expiracion"]??0;
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
        console.log(6,'6');
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
      console.log(7,'7');
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
            row["ID_CHECK"] == "IHTTTRA-02_CLATRA-03_R_PS") &&
          row["Vencimientos"] != false
        ) {
          Fecha_Expiracion = row["Fecha_Expiracion"];
          Cantidad_Vencimientos = row?.["Vencimientos"]?.["rencon-cantidad"];
          Fecha_Expiracion_Nueva =row?.["Vencimientos"]?.["Nueva_Fecha_Expiracion"];
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
    CodigoAvisoCobro: document.getElementById("CodigoAvisoCobro").value,
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
async function salvarRequicitos() {
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
    try {
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
      const response = await fetchWithTimeout(url, options, 300000);
      const Datos = await response.json();
      if (typeof Datos.ERROR != "undefined") {
        sendToast(
          $appcfg_icono_de_error + " ERROR SALVANDO LOS REQUISITOS, INTENTELO NUEVAMENTE SI EL ERROR PERSISTE FAVOR CONTACTAR AL ADMINISTRADOR DEL SISTEMA",
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
        return true;
      } else {
        //****************************************************************************************************/
        sendToast(
          $appcfg_icono_de_success + " REQUICITOS PRE-FORMA SALVADOS EXITOSAMENTE",
          $appcfg_milisegundos_toast,
          "",
          true,
          true,
          "top",
          $appcfg_pocision_toast,
          true,
          $appcfg_style_toast,
          function () { },
          "success",
          $appcfg_offset_toast,
          $appcfg_icono_toast
        );
        requicitosRecuperados = true;
        return false;
      } // final del If de si Hay error
    } catch (error) {
      console.log(
        "catch error salvar requicito en salvarRequicitos()" + error
      );
      fSweetAlertEventSelect(
        "",
        "SALVANDO REQUICITOS",
        "ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA",
        "warning"
      );
      return true;
    }
  } else {
    fSweetAlertEventSelect(
      "",
      "PROCESO DE SALVADO",
      "DEBE SELECCIONAR TODOS REQUISITOS CORRESPONDIENTES PARA PODER SALVAR LA INFORMACIÓN",
      "error"
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
  if (concesionNumber.length > 0 && document.getElementById("RAM").value === '') {
    fSweetAlertEventSelect(
      "",
      "ERROR EN RAM",
      "EL NÚMERO DE RAM NO PUEDE ESTAR VACÍO, FAVOR DE VERIFICAR",
      "error"
    );
  } else {
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
    fetchWithTimeout(url, options, 300000)
    .then((response) => response.json())
    .then(function (Datos) {
      if (typeof Datos.error != "undefined") {
        fSweetAlertEventNormal(
        Datos.errorhead,
        '',
        'error',
        Datos.error + "- " + Datos.errormsg
        );
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
          Unidad.ID_Unidad = Datos.Unidad;
          if (Datos.Unidad1 != false) {
            document.getElementById("ID_Unidad1").value = Datos.Unidad1;
            Unidad1.ID_Unidad = Datos.Unidad1;
          } else {
            document.getElementById("ID_Unidad1").value = "";
          }
          for (i = 0; i < Tramites.length; i++) {
            Tramites[i].ID = Datos.Tramites[i].ID;
          }
        } else {
          if (Datos.Unidad1 != undefined && Datos.Unidad1 != false) {
            if (document.getElementById("ID_Unidad1").value == "") {
              document.getElementById("ID_Unidad1").value = Datos.Unidad1;
              Unidad1.ID_Unidad = Datos.Unidad1;
            }
          } else {
            document.getElementById("ID_Unidad1").value = "";
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
        guardarConcesionSalvada(Tramites, Unidad, Unidad1);
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
        //****************************************************************************************************/
        sendToast(
          $appcfg_icono_de_success + " PRE-FORMA SALVADA EXITOSAMENTE",
          $appcfg_milisegundos_toast,
          "",
          true,
          true,
          "top",
          $appcfg_pocision_toast,
          true,
          $appcfg_style_toast,
          function () { },
          "success",
          $appcfg_offset_toast,
          $appcfg_icono_toast
        );
        document.getElementById("btnCambiarUnidad").style = "display:none;";
        document.getElementById("btnSalvarConcesion").style = "display:none;";
        if (concesionNumber.length > 0) {
          document.getElementById("rightDiv").style.display = "flex";
          document.getElementById("rightDivPR").style.display = "flex";
        }
        var btn = document.getElementById("btnModalidad");
        if (btn) {
          btn.style.display = "none";
          btn.innerHTML = '';
        }
        return false;
      }
    }
    )
    .catch((error) => {
      console.log("catch error save-preforma" + error);
      fSweetAlertEventSelect(
        "",
        "SALVANDO PRE-FORMA",
        "ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA",
        "warning"
      );
      return true;
    });
  }
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

function fDisplayReports() {
  $("#modalReports").modal("show");
}


// === COPY & PASTE ===

// Requiere que ya tengas el HTML del spinner:
/// const $loading_icon_default = '<i class="fad fa-cog fa-spin fa-3x fa-fw"></i><span class="gobierno1"><strong>Loading...</strong></span>';

// ---- Configuración de visibilidad ----
const MIN_LOADING_MS = 3000;   // mínimo visible del spinner (ajusta: 400..1000 ms)
const HOLD_AFTER_OK_MS = 1000; // extra tras terminar OK, para que se note (0 para desactivar)

// ---- Helpers de UI ----
function mostrarLoading(btn, mostrar) {
  if (!btn) return;
  if (mostrar) {
    btn.dataset.original = btn.innerHTML;
    btn.innerHTML = $loading_icon_default;
    btn.disabled = true;
  } else {
    if (btn.dataset.original !== undefined) btn.innerHTML = btn.dataset.original;
    btn.disabled = false;
  }
}

// Asegura repintado antes de iniciar tarea
function ensureRepaint() {
  return new Promise(requestAnimationFrame).then(() => new Promise(requestAnimationFrame));
}

// Ejecuta una tarea (sync o async) garantizando duración mínima y “hold” opcional al final
async function runWithTiming(taskLike, { minMs = 400, holdAfterOkMs = 0 } = {}) {
  const t0 = performance.now();
  let error;
  try {
    const res = await Promise.resolve(taskLike);
    const elapsed = performance.now() - t0;
    if (elapsed < minMs) await new Promise(r => setTimeout(r, minMs - elapsed));
    if (holdAfterOkMs > 0) await new Promise(r => setTimeout(r, holdAfterOkMs));
    return res;
  } catch (e) {
    error = e;
    const elapsed = performance.now() - t0;
    if (elapsed < minMs) await new Promise(r => setTimeout(r, minMs - elapsed));
    throw error;
  }
}

// ---- Guard reentradas ----
let isSavingNow = false;

// Asegura type="button" para evitar submit si está en un <form>
const btnSalvarConcesion = document.getElementById("btnSalvarConcesion");
btnSalvarConcesion?.setAttribute("type", "button");

// === Reemplaza tu listener actual por este ===
btnSalvarConcesion?.addEventListener("click", async function (event) {
  event.preventDefault?.();
  if (isSavingNow) return;
  // --- Tus validaciones existentes (idénticas) ---
  if (currentstep == 2) {
    if (seRecuperoVehiculoDesdeIP == 0 || seRecuperoVehiculoDesdeIP == 3) {
      // const inputPlaca = document.getElementById('concesion_placa');
      // let valorPlaca = inputPlaca ? inputPlaca.value.toUpperCase() : '';
      // if (!esCambioDeVehiculo && !$appcfg_placas.includes(valorPlaca.substring(0,2))) {
      //   fSweetAlertEventSelect(event, "ERROR SALVANDO", '', "error",
      //     "LOS PRIMEROS DOS DIGITOS DE LA PLACA <strong>("+ valorPlaca +")</strong> DEBE DE ESTAR DENTRO DEL GRUPO: </br><strong>" + $appcfg_placas.join(' , ')  + '</strong>');
      //   return;
      // } else {
        const inputPlaca1 = document.getElementById('concesion1_placa');
        let valorPlaca1 = inputPlaca1 ? inputPlaca1.value.toUpperCase() : '';
        if (esCambioDeVehiculo && document.getElementById('ID_Estado_RAM').value == 'IDE-1'
            && !$appcfg_placas.includes(valorPlaca1.substring(0,2))) {
          fSweetAlertEventSelect(event, "ERROR SALVANDO", '', "error",
            "LOS PRIMEROS DOS DIGITOS DE LA PLACA QUE ENTRA <strong>("+ valorPlaca1 +")</strong> DEBE DE ESTAR DENTRO DEL GRUPO: </br><strong>" + $appcfg_placas.join(' , ')  + '</strong>');
          return;
        } else {
          //**********************************************************************************************************/
          //**Salvar La Concesion Actual (Certificado de Operación o Permiso Especial)                             ***/
          //**********************************************************************************************************/
          setFocus = false;
          isSaving = true;
          fGetInputs();
          isSaving = false;
          setFocus = true;

          const sum = paneerror[currentstep].reduce((acc, val) => acc + val, 0);
          if (sum == 0) {
            try {
              isSavingNow = true;
              mostrarLoading(btnSalvarConcesion, true);
              await ensureRepaint(); // que el spinner se pinte antes de bloquear

              // salva con duración mínima garantizada (sirve si salvarConcesion es sync o async)
              await runWithTiming(
                Promise.resolve(salvarConcesion()),
                { minMs: MIN_LOADING_MS, holdAfterOkMs: HOLD_AFTER_OK_MS }
              );

            } finally {
              mostrarLoading(btnSalvarConcesion, false);
              isSavingNow = false;
            }
          } else {
            fSweetAlertEventSelect(
              event,
              "ERRORES",
              "SE HAN DETECTADO ERROR(ES) DE DATOS EN LA PANTALLA FAVOR CORRIJA Y VUELVA A INTENTAR SALVAR",
              "error"
            );
            return;
          }
        }
      //}
    } else {
      fSweetAlertEventSelect(
        event,
        "SALVANDO",
        "NO SE HA RECUPERADO/SALVADO LA INFORMACIÓN DEL VEHICULO DESDE EL IP, FAVOR RECUPERAR LA INFORMACIÓN DEL VEHICULO ANTES DE SALVAR LA INFORMACIÓN",
        "error"
      );
      return;
    }
  } else if (currentstep == 3) {
    //************************************************************************************/
    //* Salvado de Requisitos y validaciones                                             */
    //************************************************************************************/
    const CA = document.getElementById("flexSwitchCheckContratoArrendamiento");
    const rtn = document.getElementById("concesion_rtn").textContent || '';
    const idProp  = document.getElementById("concesion_identidad_propietario").textContent || '';
    const idProp1 = document.getElementById("concesion1_identidad_propietario")?.textContent || '';
    if (
      ((esCambioDeVehiculo == false && rtn && rtn !== idProp  && !CA.checked)) &&
      ((esCambioDeVehiculo == true  && rtn && rtn !== idProp1 && !CA.checked))
    ) {
      fSweetAlertEventNormal(
        "SALVANDO",
        "EL DUEÑO DE LA UNIDAD ES DISTINTO AL CONCESIONARIO, FAVOR SELECCION EL CONTRATO DE ARRENDAMIENTO",
        "error"
      );
      return;
    }
    if (!estanCargadocs) {
      fSweetAlertEventNormal("SALVANDO", "DEBE CARGAR EL ARCHIVO PDF DEL EXPEDIENTE", "error");
      return;
    }
    if (!requicitosRecuperados) {
      // Si deseas spinner aquí también, envuélvelo igual con mostrarLoading + runWithTiming
      try {
        mostrarLoading(btnSalvarConcesion, true);
        await ensureRepaint(); // que el spinner se pinte antes de bloquear
        // salva con duración mínima garantizada (sirve si salvarConcesion es sync o async)
        await runWithTiming(
          Promise.resolve(salvarRequicitos()),
          { minMs: MIN_LOADING_MS, holdAfterOkMs: HOLD_AFTER_OK_MS }
        );
      } finally {
        mostrarLoading(btnSalvarConcesion, false);
      }
    }
  }
});


function disabledEdit(){
  document.getElementById("input-prefetch").style.display = "none";
  document.getElementById("toggle-icon").style.display = "none";
  document.getElementById("btnnext4").style.display = "none";
}

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
  document.getElementById("rightDivTR").style.display = "none";
  switch (currentstep) {
    case 0:
      document.getElementById("btnCambiarUnidad").style.display = "none";
      if (document.getElementById("RAM").value != '') {
        //******************************************************************************/
        //* Si el expediente esta en un estado editable de habilita el boton salvar    */
        //******************************************************************************/
        if (esEditable()== true) {
          document.getElementById("btnSalvarConcesion").style = "display:fixed;";
        }
        //document.getElementById("rightDiv").style.display = "flex";
        //document.getElementById("rightDivPR").style.display = "flex";
      }
      document.getElementById("colapoderado").focus();
      break;
    case 1:
      document.getElementById("btnCambiarUnidad").style.display = "none";
      if (document.getElementById("RAM").value != '') {
        //******************************************************************************/
        //* Si el expediente esta en un estado editable de habilita el boton salvar    */
        //******************************************************************************/
        if (esEditable()==true) {
          document.getElementById("btnSalvarConcesion").style = "display:fixed;";
        }
        //document.getElementById("rightDiv").style.display = "flex";
        //document.getElementById("rightDivPR").style.display = "flex";
      }
      document.getElementById("rtnsoli").focus();
      break;
    case 2:
      if (document.getElementById("concesion_concesion").textContent != "") {
        //document.getElementById("rightDivTR").style.display = "flex";
        //******************************************************************************/
        //* Si el expediente esta en un estado editable de habilita el boton salvar    */
        //******************************************************************************/
        if (esEditable ()== true) {
          document.getElementById("btnSalvarConcesion").style = "display:fixed;";
        }
        if (esCambioDeVehiculo) {
          document.getElementById("btnCambiarUnidad").style = "display:flex; position: absolute; top: 195px; right: 25px; padding: 10px;";
        }
      }  else {
        document.getElementById("btnSalvarConcesion").style.display = "none";
        document.getElementById("btnCambiarUnidad").style.display = "none";
        if (esEditable() == true) {
          document.getElementById("input-prefetch").style.display = "block";
          document.getElementById("toggle-icon").style.display = "block";
        } else {
          document.getElementById("input-prefetch").style.display = "none";
          document.getElementById("toggle-icon").style.display = "none";
        }
        
        if (concesionNumber.length > 0) {
          document.getElementById("rightDiv").style.display = "flex";
          document.getElementById("rightDivPR").style.display = "flex";
        }
        if (showModalFromShown == true) {
          showModalFromShown = false;
        } else {
          document.getElementById("concesion_vin").focus();
        }
      }
     break;
    case 3:
      if (requicitosRecuperados == false) {
        document.getElementById("btnCambiarUnidad").style.display = "none";
      }
      if (esEditable()==true) {
        document.getElementById("btnSalvarConcesion").style = "display:fixed;";
      }      ////showConcesionTramites();
      break;
    case 4:
      document.getElementById("btnCambiarUnidad").style.display = "none";
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
        if (event.key === "Enter" && isSaving == false) {
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
    event.target.value = event.target.value.toUpperCase();
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
        if (isTab == false && isSaving == false) {
          moveToNextInput(input, 0);
        }
        isTab = false;
      }
    } else {
      paneerror[currentstep][idinputs.indexOf(idinput)] = 0;
    }
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
            if (event.key === "Enter" && isSaving == false) {
              moveToNextInput(input, 0);
            }
          }
        } else {
          //Mover al siguiente input
          if (event.key === "Enter" && isSaving == false) {
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

async function callFunctionBorrarTramite(Concesion, ID, Linea, el,tramiteDescripcion) {
  const result = await Swal.fire({
    title: "¿ESTÁ SEGURO?",
    html: `¿QUIERE ELIMINAR El TRAMITE: <strong>${tramiteDescripcion}</strong></br> DE LA CONCESION No. <strong>${document.getElementById("concesion_concesion").innerHTML
      }</strong>?`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "SÍ, ELIMINAR",
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
      return true;
    } else {
      return false;
    }
  } else {
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
    if (document.getElementById("ID_Expediente").value == '') {
      fd.append("action", "add-tramite-preforma");
    } else {
      fd.append("action", "add-tramite-expediente");
    }
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
    const response = await fetchWithTimeout(url, options, 300000);
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
        $appcfg_icono_de_success + " TRAMITE EN PREFORMA INSERTADO SATISFACTORIAMENTE",
        $appcfg_milisegundos_toast,
        "",
        true,
        true,
        "top",
        $appcfg_pocision_toast,
        true,
        $appcfg_style_toast,
        function () { },
        "success",
        $appcfg_offset_toast,
        $appcfg_icono_toast
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
      "OPPS",
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
async function addTramite(el,tramiteDescripcion) {
  const result = await Swal.fire({
    title: "¿ESTÁ SEGURO?",
    html: `¿QUIERE AGREGAR EL TRAMITE <strong>${tramiteDescripcion}</strong></br> A LA CONCESION No. <strong>${document.getElementById("concesion_concesion").innerHTML
      }</strong> ?`,
    icon: "info",
    showCancelButton: true,
    confirmButtonText: "SÍ, AGREGAR",
    cancelButtonText: "CANCELAR",
  });
  if (result.isConfirmed) {
    const success = await addTramitePreforma(el);
    if (success) {
      return true;
    } else {
      return false;
    }
  } else {
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
      //*"IHTTTRA-03_CLATRA-15_M_CL",
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

  //**************************************************************************************/
  //** SECCIÓN QUE OCULTA O PRESENTA EL CAMPO DE PLACA */
  //**************************************************************************************/
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

  //**************************************************************************************/
  //** SI NO ES UN TRAMITE DE MODIFICACIÓN */
  //**************************************************************************************/
  if (acronimo_tipo != "M") {
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
    //**************************************************************************************/
    //** SI ES CAMBIO DE UNIDAD */
    //**************************************************************************************/
    if (acronimo == "M_CU") {
      for (let id of checkboxIds[acronimo]) {
        const checkbox = document.getElementById(id);
        if ((checkbox && checkbox.disabled == false && checkedItem == false) ||
          (checkbox && checkbox.checked == false &&  checkbox.disabled == false && checkedItem == true)) {
          const [tipo_tramite1,clase_tramite1,acronimo_tipo1,acronimo_clase1,] = id.split("_");
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
      //**************************************************************************************/
      //** SI NO ES CAMBIO DE UNIDAD */
      //**************************************************************************************/
      for (let id of checkboxIds["M_CU"]) {
        const checkbox = document.getElementById(id);
        if (checkbox && checkbox.checked == true && checkbox.disabled == false) {
          console.log(id, "id en el for inside if");
          contador++;
        }
      }
      //**************************************************************************************/
      //** SI SE ENCONTRO ALGUN TRAMITE DE CAMBIO SE OCULTA LA LINEA DE CAMBIO DE UNIDAD */
      //**************************************************************************************/
      if (parseInt(contador) > 0) {
        const elemento = document.getElementById("row_tramite_M_CU");
        if (elemento) {
          elemento.style.display = "none";
          const elementochk = document.getElementById("IHTTTRA-03_CLATRA-08_M_CU");
          if (elementochk) {
            elementochk.checked = false;
          }
        }
      } else {
        //***************************************************************************************/
        //** SI NO SE ENCONTRO ALGUN TRAMITE DE CAMBIO SE PRESENTA LA LINEA DE CAMBIO DE UNIDAD */
        //***************************************************************************************/
        const elemento = document.getElementById("row_tramite_M_CU");
        if (elemento) {
          elemento.style.display = "flex";
          const elementochk = document.getElementById(
            "IHTTTRA-03_CLATRA-08_M_CU"
          );
          if (elementochk) {
            //elementochk.checked = true;
            var elementox = document.getElementById("concesion_tramite_placa_CU");
            if (elementox) {
              elementox.style.display = "flex";
            }
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
          const success = await addTramite(event.target,document.getElementById("descripcion_"+event.target.value).textContent);
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
              document.getElementById("rightDivPR").style.display = "none";
              document.getElementById("concesion_tramite_placa_CU").focus();
            } else {
              if (acronimo_clase == "CL") {
                seRecuperoVehiculoDesdeIP = 1;
                esCambioDePlaca = true;
                document.getElementById("input-prefetch").style.display ="none";
                document.getElementById("toggle-icon").style.display = "none";
                document.getElementById("rightDiv").style.display = "none";
                document.getElementById("rightDivPR").style.display = "none";
                document.getElementById("concesion_tramite_placa_CL").focus();
              } else {
                document.getElementById("concesion_vin").focus();
              }
            }
            //***************************************************************************/
            //* Final Marcado / Desmarcando Tramitres no Compatibles con Tramite Actual
            //***************************************************************************/
          } else {
            event.target.checked = false;
          }
          //***************************************************************************/
          //* Final Salvando Tramite
          //***************************************************************************/
        } else {
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
            document.getElementById("rightDivPR").style.display = "none";
            document.getElementById("concesion_tramite_placa_CU").focus();
          } else {
            if (acronimo_clase == "CL") {
              seRecuperoVehiculoDesdeIP = 1;
              esCambioDePlaca = true;
              document.getElementById("input-prefetch").style.display = "none";
              document.getElementById("toggle-icon").style.display = "none";
              document.getElementById("rightDiv").style.display = "none";
              document.getElementById("rightDivPR").style.display = "none";
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
            event.target,
            document.getElementById("descripcion_"+event.target.value).textContent
          );
          if (success) {
            chk.setAttribute('data-iddb','');
            if (acronimo_clase == "CU") {
              esCambioDeVehiculo = false;
              seRecuperoVehiculoDesdeIP = 0;
              document.getElementById("ID_Unidad1").value = '';
              document.getElementById("input-prefetch").style.display = "block";
              document.getElementById("toggle-icon").style.display = "block";
              document.getElementById("rightDiv").style.display = "flex";
              document.getElementById("rightDivPR").style.display = "flex";
            } else {
              if (acronimo_clase == "CL") {
                document.getElementById("input-prefetch").style.display = "block";
                document.getElementById("toggle-icon").style.display = "block";
                document.getElementById("rightDiv").style.display = "flex";
                document.getElementById("rightDivPR").style.display = "flex";
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
            if (acronimo_clase == "CU") {
              esCambioDeVehiculo = false;
              seRecuperoVehiculoDesdeIP = 0;
              document.getElementById("input-prefetch").style.display = "block";
              document.getElementById("toggle-icon").style.display = "block";
              document.getElementById("rightDiv").style.display = "flex";
              document.getElementById("rightDivPR").style.display = "flex";
            } else {
              if (acronimo_clase == "CL") {
                document.getElementById("input-prefetch").style.display = "block";
                document.getElementById("toggle-icon").style.display = "block";
                document.getElementById("rightDiv").style.display = "flex";
                document.getElementById("rightDivPR").style.display = "flex";
                esCambioDePlaca = false;
                seRecuperoVehiculoDesdeIP = 0;
              }
            }
            event.target.checked = true;
          }
        } else {
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
            document.getElementById("rightDivPR").style.display = "flex";
          } else {
            if (acronimo_clase == "CL") {
              document.getElementById("ID_Unidad1").value == "";
              document.getElementById("btnCambiarUnidad").style.display = "none";
              document.getElementById("idVistaSTPC1").style = "display:fixed;";
              document.getElementById("idVistaSTPC2").style = "display:none;";
              document.getElementById("input-prefetch").style.display = "block";
              document.getElementById("toggle-icon").style.display = "block";
              document.getElementById("rightDiv").style.display = "flex";
              document.getElementById("rightDivPR").style.display = "flex";
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
  var sufijo = "1";
  if (Tipo_Tramite == "CL") {
    sufijo = "";
  } else {
    document.getElementById("capacidad" + sufijo).value = "";
    document.getElementById("alto" + sufijo).value = "";
    document.getElementById("largo" + sufijo).value = "";
    document.getElementById("ancho" + sufijo).value = "";
  }

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

  if (vehiculo.cargaUtil.placaAnterior != "") {
    document.getElementById("concesion" + sufijo + "_placaanterior").innerHTML =  vehiculo.cargaUtil.placaAnterior;
    document.getElementById("concesion" + sufijo + "_placaanterior").style = "display:inline;";
  } else {
    document.getElementById("concesion" + sufijo + "_placaanterior").style = "display:none;";
    document.getElementById("concesion" + sufijo + "_placaanterior").innerHTML = "";
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
  if (Tipo_Tramite == "CU") {
    console.log(Tipo_Tramite,'Tipo_Tramite en getVehiculosDesdeIpMoveDatos');
    document.getElementById("btnCambiarUnidad").innerHTML = '<i class="fas fa-truck-moving fa-2x"></i>  <strong>ENTRA</strong>';
    document.getElementById("btnCambiarUnidad").style = "display:flex; position: absolute; top: 195px; right: 25px; padding: 10px;";
    document.getElementById("idVistaSTPC2").style = "display:fixed;";
    document.getElementById("idVistaSTPC1").style = "display:none;";
  }

  return true;
}

async function getVehiculoDesdeIP(Obj) {
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

    const response = await fetchWithTimeout(url, options, 300000);
    const vehiculo = await response.json();

    if (!vehiculo.error) {
      if (vehiculo?.codigo && vehiculo.codigo == 200) {
        if (vehiculo.cargaUtil.estadoVehiculo == "NO BLOQUEADO") {
          document.getElementById("concesion_vin").focus();
          isVehiculeBlock = false;
          sendToast(
            $appcfg_icono_de_success + " INFORMACIÓN DEL VEHICULO RECUPERADO EXITOSAMENTE DESDE EL INSTITUTO DE LA PROPIEDAD",
            $appcfg_milisegundos_toast,
            "",
            true,
            true,
            "top",
            $appcfg_pocision_toast,
            true,
            $appcfg_style_toast,
            function () { },
            "success",
            $appcfg_offset_toast,
            $appcfg_icono_toast
          );

          var html = "";

          if (vehiculo?.cargaUtil?.Multas?.[0]) {
            html = mallaDinamica(
              { titulo: "LISTADO DE MULTAS", name: "MULTAS" },
              vehiculo.cargaUtil.Multas
            );
          }

          if (vehiculo?.cargaUtil?.Expedientes?.[0]) {
            html = html +
              mallaDinamica(
                {
                  titulo:
                    "CERTIFICADO Y/O UNIDAD(ENTRA)  TIENEN EXPEDIENTES EN TRAMITE",
                  name: "EXPEDIENTES",
                },
                vehiculo.cargaUtil.Expedientes,                      
                {},
                {
                title: "text-center fw-bold",
                encabezado: "border-bottom fw-bold p-1 bg-success-subtle text-success-emphasis",
                bodyRow: "border-bottom shadow-sm p-1 bg-body-tertiary tHover",
                },
                $appcfg_Dominio_Raiz + ':85/Detalle_Expediente.php?idExpediente=@@__0__@@&idSolicitud=@@__1__@@',
                99,
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
                vehiculo.cargaUtil.Preformas,
                {},
                {
                  title: "text-center fw-bold",
                  encabezado: "border-bottom fw-bold p-1 bg-success-subtle text-success-emphasis",
                  bodyRow: "border-bottom shadow-sm p-1 bg-body-tertiary tHover",
                },                                
                $appcfg_Dominio + 'ram.php?consulta=true&RAM=',
                1,
              );
          }

          if (vehiculo?.cargaUtil?.Placas?.[0]) {
            var links = $appcfg_Dominio_Raiz + LinksConsulta.get(document.getElementById("ID_Clase_Servicio").value);
            html =
              html +
              mallaDinamica(
                {
                  titulo:
                    "CERTIFICADO Y/O UNIDAD ENTRANTE TIENEN DOCUMENTOS PARA IMPRESIÓN Y/O ENTREGA",
                  name: "PREFORMAS",
                },
                vehiculo.cargaUtil.Placas,
                {},
                {
                  title: "text-center fw-bold",
                  encabezado: "border-bottom fw-bold p-1 bg-success-subtle text-success-emphasis",
                  bodyRow: "border-bottom shadow-sm p-1 bg-body-tertiary tHover",
                },                
                links,
                0
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
      if (isSaving == false) {
        if (ejecutar == true && event.target.value !== "") {
          event.preventDefault();
          ejecutar = false;
          inputCambioUnidad.removeEventListener("keyup", handleKeyUpCU);
          seRecuperoVehiculoDesdeIP = 2;
          ejecutar = await getVehiculoDesdeIP(event.target);
          setTimeout(() => {
            inputCambioUnidad.addEventListener("keyup", handleKeyUpCU);
          }, 8000);
        }
      }
    }
    async function handleKeyUpCU(event) {
      if (isSaving == false) {
        if (event.target.value !== "") {
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
    }
    inputCambioUnidad.addEventListener("change", handleChangeCU);
    inputCambioUnidad.addEventListener("keyup", handleKeyUpCU);
  }

  const inputCambioPlaca = document.getElementById(
    "concesion_tramite_placa_CL"
  );
  if (inputCambioPlaca) {
    async function handleChangeCP(event) {
      if (esCambioDeVehiculo == true && event.target.value != document.getElementById("concesion1_placa").value || esCambioDeVehiculo == false) {
        if (isSaving == false) {
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
      } else {
        seRecuperoVehiculoDesdeIP = 3;
      }
    }
    async function handleKeyUpCP(event) {
      if (esCambioDeVehiculo == true && event.target.value != document.getElementById("concesion1_placa").value || esCambioDeVehiculo == false) {
        if (isSaving == false) {
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
      } else {
        seRecuperoVehiculoDesdeIP = 3;
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
        estanCargadocs = true;
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

if (scrollDiv) {
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
}