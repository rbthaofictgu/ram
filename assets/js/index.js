"use strict";

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
var seRecuperoVehiculoDesdeIP = 0;
var isVehiculeBlock = false;
var finalizarSalvado = false;
var checked = false;
var concesionIndex = Array();
var concesionNumber = Array();
var concesionForAutoComplete = Array();
var currentConcesionIndex = -1;
var dataConcesion = Array();
var esCarga;
var modalidadDeEntrada = 'I';
var chkTramites;

function showHideTramite(el){
  document.getElementById(el.id+'T').classList.toggle('showtramites');
}

//*********************************************************************************************************/
//* INICIO: Agregando un tramite a Concesion Number
//*********************************************************************************************************/  
function addConcesionNumber (ID,ID_CHECK,Monto,Descripcion,ID_Tramite) {
  for (let i=(concesionNumber.length-1);i>=0;i--) {

    if (concesionNumber[i].Concesion == document.getElementById("concesion_concesion").innerHTML)  {  

      let Cantidad_Vencimientos = 1;
      let Fecha_Expiracion = '';
      let Fecha_Expiracion_Nueva = '';
      
      if (ID_CHECK === 'IHTTTRA-02_CLATRA-01_R_PE') {
        Cantidad_Vencimientos = document.getElementById("CantidadRenovacionesPerExp").value;
        Fecha_Expiracion_Nueva = document.getElementById("NuevaFechaVencimientoPerExp").value;
        Fecha_Expiracion = document.getElementById("NuevaFechaVencimientoPerExp").value;
      } else {
        if (ID_CHECK === 'IHTTTRA-02_CLATRA-02_R_CO' || ID_CHECK === 'IHTTTRA-02_CLATRA-02_R_PS') {
          Cantidad_Vencimientos = document.getElementById("CantidadRenovacionesConcesion").value;
          Fecha_Expiracion_Nueva = document.getElementById("NuevaFechaVencimientoConcesion").value;
          Fecha_Expiracion = document.getElementById("FechaVencimientoConcesion").value;
        }      
      }

      concesionNumber[i].Tramites.push({
        ID: ID,
        ID_Compuesto: ID_CHECK,
        Codigo: ID_Tramite,
        descripcion: Descripcion,
        ID_Tramite: ID_Tramite,
        Monto: parseFloat(Monto).toFixed(2),
        Total_A_Pagar: parseFloat(parseFloat(Monto).toFixed(2) * Cantidad_Vencimientos).toFixed(2),
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
function updateConcesionNumber (idTramite) {
  for (let i=(concesionNumber.length-1);i>=0;i--) {
    if (concesionNumber[i].Concesion == document.getElementById("concesion_concesion").innerHTML)  {  
      concesionNumber[i].Tramites = concesionNumber[i].Tramites.filter(tramite => tramite.ID !== idTramite);
    }
  }
}
//*********************************************************************************************************/
//* Final: Actualizando arreglo de Tramites dentro de concesionNumber
//*********************************************************************************************************/  

//**********************************************************************************************************/
//* INICIO: Funcion para Obtener el Attribute de un elemento
//**********************************************************************************************************/
function getAttribute(Element,Attribute,DeleteAttribute=false){
  let Attr = '';
  //**********************************************************************************************************/
  //* Validando si el elemento tiene el Attribute a obtener
  //**********************************************************************************************************/
  if (Element.hasAttribute(Attribute) == true) {
    //**********************************************************************************************************/
    //* Obteniendo el Attribute de un elemento
    //**********************************************************************************************************/
    Attr = Element.getAttribute(Attribute);
    if (DeleteAttribute==true) {
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
function setAttribute(Element,Attribute,Value){
  //**********************************************************************************************************/
  //* Asigando el Attribute a un elemento
  //**********************************************************************************************************/
  Element.setAttribute(Attribute,Value);
}
//**********************************************************************************************************/
//* FINAL: Funcion para Establecer el Attribute de un elemento
//**********************************************************************************************************/

//**********************************************************************************************************/
//* INICIO: Funcion para Eliminar/Remover el Attribute de un elemento
//**********************************************************************************************************/
function removeAttribute(Element,Attribute){
  //**********************************************************************************************************/
  //* Eliminar/Remover el Attribute del elemento
  //**********************************************************************************************************/
  Element.removeAttribute(Attribute);
}
//**********************************************************************************************************/
//* FINAL: Funcion para Eliminar/Remover el Attribute de un elemento
//**********************************************************************************************************/

function fShowConcesiones(){
  const myModal = new bootstrap.Modal(document.getElementById('exampleModal'));
  myModal.show();
  mostrarData(concesionNumber, 'tabla-container',  'CONCESIONES SALVADAS');
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
  Elementos.forEach(function(Elemento) {
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
      concesionNumber[Index]  = false;
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
function addElementToAutoComplete(value,text) {   
  let element ={ value: value, text: text };
  concesionForAutoComplete.push(element);
}

//********************************************************************************/
//* Inicio Filtrar los elementos que no son iguales a 'Concesion'
//********************************************************************************/
function deleteElementFromAutoComplete(Concesion) {   
  //*item !== Concesion
  concesionForAutoComplete = concesionForAutoComplete.filter(Concesion => Concesion.value !== Concesion);
}


var multas3ra = document.getElementById("btnmultas");
if (multas3ra != null) {
  multas3ra.addEventListener("click", function (event) {
    showConcesion();
  });
}

var consultas3ra = document.getElementById("btnconsultas");
if (consultas3ra != null) {
  consultas3ra.addEventListener("click", function (event) {
    showConcesion();
  });
}

var perexp3ra = document.getElementById("btnperexp");
if (perexp3ra != null) {
  perexp3ra.addEventListener("click", function (event) {
    showConcesion();
  });
}

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
// Obtener todas las entradas enabled para validarlas
//**********************************************************************/
function fGetInputs() {
  setFocus = false;
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
      if (index == 0) {
        input.focus();
        input.select();
      }
    }

    index++;
  });
  fGetInputsSelect();
}

//**********************************************************************/
// Obtener todas las entradas enabled para validarlas
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
      if (input.getAttribute("data-valor") > input.value) {
        input.classList.add("errortxt");

        var label = document.getElementById(input.id + "label");
        if (label != null) {
          label.classList.add("errorlabel");
        }

        paneerror[currentstep][idinputs.indexOf(input.id)] = 1;
      } else {
        input.classList.remove("errortxt");

        var label = document.getElementById(input.id + "label");
        if (label != null) {
          label.classList.remove("errorlabel");
        }

        paneerror[currentstep][idinputs.indexOf(input.id)] = 0;
        //Moverse al siguiente input
        moveToNextInput(input, 0);
      }
    }
    index++;
  });
  setFocus = true;
  isTab = false;
}

//**************************************************************************************/
//Eliminar todos los mensajes de error
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
    input.classList.remove("errortxt");
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
    inputx.classList.remove("errortxt");
    let id = inputx.id.concat("label");
    var label = document.getElementById(id);
    if (label != null) {
      label.classList.remove("errorlabel");
    }
  });
}

  function fEliminarTramite(idConcesion,idTramite,idRow){
    // URL del Punto de Acceso a la API
    const url = $appcfg_Dominio + "Api_Ram.php";
    let fd = new FormData(document.forms.form1);
    var Tramites = '';
    // Adjuntando el action al FormData
    fd.append("action", "delete-tramite-preforma");
    // Adjuntando el Concesion y Caracterización al FormData
    fd.append("idTramite", JSON.stringify(idTramite));
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
            "ERROR ELIMINANDO TRAMITE EN PREFORMA, INTENTELO NUEVAMENTE SI EL ERROR PERSISTE FAVOR CONTACTAR AL ADMINISTRADOR DEL SISTEMA",
            $appcfg_milisegundos_toast,
            "",
            true,
            true,
            "top",
            "right",
            true,
            $appcfg_background_toast,
            function () {},
            "error",
            $appcfg_pocision_toast,
            $appcfg_icono_toast
          );
          return true;
        } else {
          //*******************************************************************************************************/
          //*INICIO: LLAMANDO FUNCION QUE ACTUALIZA EL ARREGLO DE TRAMITES, ELIMINANDO EL TRAMITE BORRADO EN LA DB
          //*******************************************************************************************************/
          updateConcesionNumber (idTramite)
          //*******************************************************************************************************/
          //*FINAL: LLAMANDO FUNCION QUE ACTUALIZA EL ARREGLO DE TRAMITES, ELIMINANDO EL TRAMITE BORRADO EN LA DB
          //*******************************************************************************************************/
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
            function () {},
            "success",
            $appcfg_pocision_toast,
            $appcfg_icono_toast
          );
          if (idRow != null) {
            const row = document.getElementById(idRow);
            if (row) {
              row.remove();
            } else {
              console.log("Linea # " + idRow +" No Encontrada!");
              alert("Linea # " + idRow +" No Encontrada!");
            }
          }
          return false;
        } // final del If de si Hay error
      })
      .catch((error) => {
        console.log('catch error eliminando tramite en preforma' + error);
        fSweetAlertEventSelect(
          "",
          "ELIMINAR TRAMITE EN PREFORMA",
          "ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA",
          "warning"
        );
        return true;
      });
  }

  btnCambiarUnidad.addEventListener("click", function (event) {
    if (event.target.innerHTML == "<strong>ENTRA</strong>" || event.target.innerHTML == "ENTRA") {
      event.target.innerHTML = "<strong>SALE</strong>";
      document.getElementById("idVistaSTPC1").style = "display:fixed;";                
      document.getElementById("idVistaSTPC2").style = "display:none;";                
    } else {
      event.target.innerHTML = "<strong>ENTRA</strong>";
      document.getElementById("idVistaSTPC2").style = "display:fixed;";                
      document.getElementById("idVistaSTPC1").style = "display:none;";                
    }
  });

  // Pocicionando el cursos en el primer input de la pantalla id=colapoderado
  function setFocusElement() {
    document.getElementById("colapoderado").focus();
  }

  //**************************************************************************************/
  //*Crear Malla Automaticamente con la información de los arreglos
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

  function fMarcarRequicitos () {
    document.getElementById("flexSwitchCheckPermisoExplotacion").checked = true;
    document.getElementById("flexSwitchCheckCertificadoOperacion").checked = true;
    document.getElementById("flexSwitchCheckCarnetColegiacion").checked = true;
    document.getElementById("flexSwitchCheckAcreditarRepresentacion").checked = true;
    document.getElementById("flexSwitchCheckEscritoSolicitud").checked = true;
    document.getElementById("flexSwitchCheckDNI").checked = true;
    document.getElementById("flexSwitchCheckRTN").checked = true;
    document.getElementById("flexSwitchCheckInspeccionFisico").checked = true;    
    document.getElementById("flexSwitchCheckBoletaRevision").checked = true;
    document.getElementById("flexSwitchCheckContratoArrendamiento").checked = true;    
    document.getElementById("flexSwitchCheckAutenticidadCarta").checked = true;
    document.getElementById("flexSwitchCheckAutenticidadDocumentos").checked = true;        
  }
  //**************************************************************************************/
  //* Cargando la información por default que debe usar el formulario
  //**************************************************************************************/
  function f_DataOmision() {
    //*****************************************************************************************/
    //* INICIO: Despliega la información del stepper content y oculta el gif de procesando    */
    //*****************************************************************************************/
    document.getElementById("id_stepper_gif").style = 'display:flex';
    document.getElementById("id_img_stepper_gif").style = 'display:flex';
    document.getElementById("concesion_tramites").style = "display:none;";
    document.getElementById("id_stepper_content").style = 'display:none';
    //*****************************************************************************************/
    //* FINAL: Despliega la información del stepper content y oculta el gif de procesando    */
    //*****************************************************************************************/
    var datos;
    var response;
    // Get the URL parameters from the current page
    const urlParams = new URLSearchParams(window.location.search);
    // Get a specific parameter by name
    const RAM = urlParams.get('RAM'); // Número de RAM
    if (RAM != null) {
      document.getElementById("RAM-ROTULO").innerHTML = "<strong>" + RAM + "</strong>";
      document.getElementById("RAM-ROTULO").style = "display:inline-block;";    
      document.getElementById('RAM').value = RAM;
    } else {
      document.getElementById("RAM-ROTULO").style = "display:none;";    
      document.getElementById('RAM').value = '';
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
        if (document.getElementById("Ciudad").value.toUpperCase().substring(0, 11) != "TEGUCIGALPA") {
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

          if (typeof datos[3] != "undefined" && typeof datos[3][0] != "undefined") {
            //*Moviendo campos de base de datos a datos de pantalla Apoderado Legal
            document.getElementById("nomapoderado").value =  datos[3][0]['Nombre_Apoderado_Legal'];
            document.getElementById("colapoderado").value =  datos[3][0]['ID_Colegiacion'];
            document.getElementById("identidadapod").value = datos[3][0]['Ident_Apoderado_Legal'];
            document.getElementById("dirapoderado").value = datos[3][0]['Direccion_Apoderado_Legal'];
            document.getElementById("telapoderado").value = datos[3][0]['Telefono_Apoderado_Legal'];
            document.getElementById("emailapoderado").value = datos[3][0]['Email_Apoderado_Legal'];
            document.getElementById("ID_Apoderado").value = datos[3][0]['ID'];            
            fLlenarSelect("entregadocs", datos[1], datos[4][0]['Entrega_Ubicacion'], false, {
              text: "SELECCIONE UN LUGAR DE ENTREGA",
              value: "-1",
            });
            document.getElementById("tipopresentacion").value = datos[4][0]['Presentacion_Documentos'];
            //* Moviendo campos de base de datos a datos de pantalla Solicitante
            if (typeof datos[4] != "undefined") {
              document.getElementById("rtnsoli").value = datos[4][0]['RTN_Solicitante'];
              document.getElementById("nomsoli").value = datos[4][0]['Nombre_Solicitante'];
              document.getElementById("denominacionsoli").value = datos[4][0]['Denominacion_Social'];
              document.getElementById("domiciliosoli").value = datos[4][0]['Domicilo_Solicitante'];
              document.getElementById("telsoli").value = datos[4][0]['Telefono_Solicitante'];
              document.getElementById("emailsoli").value = datos[4][0]['Email_Solicitante'];
              document.getElementById("tiposolicitante").value = datos[4][0]['ID_Tipo_Solicitante'];
              document.getElementById("Departamentos").value = datos[4][0]['ID_Departamento'];
              document.getElementById("ID_Solicitante").value  = datos[4][0]['ID'];
              document.getElementById("ID_Estado_RAM").value = datos[4][0]['Estado_Formulario']
              var event = new Event("change", {
                bubbles: true,
                cancelable: true,
              });
              document.getElementById("Departamentos").dispatchEvent(event);
              setTimeout(() => {
                document.getElementById("Municipios").value = datos[4][0]['ID_Municipio'];
                document.getElementById("Municipios").dispatchEvent(event);
              }, 2000);              
              setTimeout(() => {
                //document.getElementById("Municipios").dispatchEvent(event);
                document.getElementById("Aldeas").value = datos[4][0]['ID_Aldea'];;
              }, 4000);              
            }
            //***************************************************************************/
            //* Armando Objeto de Concesiones Salvadas en Preforma
            //***************************************************************************/
            if (typeof datos[5] != "undefined") {
              guardarConcesionSalvadaPreforma(datos[5],datos[7]);
            }
            //***************************************************************************/
            //* Estableciento el Link del Expediente Cargado para Trabajarlo
            //***************************************************************************/
            if (typeof datos[8] != "undefined" && datos[8] != false) {
              document.getElementById("fileUploaded").style.display = 'block';
              document.getElementById("fileUploadedLink").setAttribute("href", $appcfg_Dominio + datos[8]);;
            } else {
              document.getElementById("fileUploaded").style.display = 'none';
            }
            //***************************************************************************/
            //* Marcar requicitos
            //***************************************************************************/
            console.log('REQUICITOS');
            fMarcarRequicitos ();
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
        document.getElementById("id_stepper_gif").style = 'display:none';
        document.getElementById("id_img_stepper_gif").style = 'display:none';
        document.getElementById("id_stepper_content").style = 'display:block';
        //*****************************************************************************************/
        //* FINAL: Despliega la información del stepper content y oculta el gif de procesando    */
        //*****************************************************************************************/
      })
      .catch((error) => {
        //*****************************************************************************************/
        //* INICIO: Despliega la información del stepper content y oculta el gif de procesando    */
        //*****************************************************************************************/
        document.getElementById("id_stepper_gif").style = 'display:none';
        document.getElementById("id_img_stepper_gif").style = 'display:none';
        document.getElementById("id_stepper_content").style = 'display:block';
        //*****************************************************************************************/
        //* FINAL: Despliega la información del stepper content y oculta el gif de procesando    */
        //*****************************************************************************************/
        console.log("error f_DataOmision() " + error);
        fSweetAlertEventNormal(
          "OPPS",
          "ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA",
          "error"
        );
      });
  }

  //**************************************************************************************/
  // Inicio llamado a la función f_DataOmision que carga los datos por defecto
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
      if (currentstep != 2 || (currentstep == 2 && concesionNumber.length == 0)) {
        fGetInputs();
      }
      stepperForm.next();
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
        document.getElementById("Permiso_Explotacion").value = "";
        document.getElementById("ID_Unidad").value = "";
        document.getElementById("ID_Unidad1").value = "";
        document.getElementById("Concesion_Encriptada").value = "";
        document.getElementById("Permiso_Explotacion_Encriptado").value = "";
        document.getElementById("estaPagadoElCambiodePlaca").value  = false;
        document.getElementById("RequiereRenovacionConcesion").value = false;
        document.getElementById("RequiereRenovacionPerExp").value = false;
        document.getElementById("NuevaFechaVencimientoConcesion").value = "";
        document.getElementById("NuevaFechaVencimientoPerExp").value  ="";
        document.getElementById("FechaVencimientoConcesion").value = "";
        document.getElementById("FechaVencimientoPerExp").value  ="";
        document.getElementById("CantidadRenovacionesConcesion").value = 0;
        document.getElementById("CantidadRenovacionesPerExp").value = 0;
        //***********************************************************************************************************************************/
        //* INICIO: Limpiando Caracterización de la Concesion
        //***********************************************************************************************************************************/
        document.getElementById("ID_Categoria").value = "";
        document.getElementById("ID_Tipo_Servicio").value  = "";
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
        document.getElementById("concesion1_fecexp").innerHTML = "";
        document.getElementById("concesion1_resolucion").innerHTML = "";
        document.getElementById("concesion1_fecven").innerHTML = "";
        document.getElementById("concesion1_nombreconcesionario").innerHTML = "";
        document.getElementById("concesion1_rtn").innerHTML = "";
        document.getElementById("concesion1_fecexp").innerHTML = "";
        document.getElementById("concesion1_resolucion").innerHTML = "";
        document.getElementById("concesion1_nombre_propietario").innerHTML = "";
        document.getElementById("concesion1_identidad_propietario").innerHTML = "";
        if (
          document.getElementById("concesion_placaanterior").innerHTML != ""
        ) {
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
        seRecuperoVehiculoDesdeIP = 0;
        document.getElementById("idVistaSTPC2").style = "display:none;";                
        document.getElementById("idVistaSTPC1").style = "display:fixed;";                
        ProcessFormalitiesUnCheck();
        var concesion_tramite_placa_CU = document.getElementById("concesion_tramite_placa_CU");
        if (concesion_tramite_placa_CU != null) {
          concesion_tramite_placa_CU.value = ''
        }
        var concesion_tramite_placa_CL = document.getElementById("concesion_tramite_placa_CL");
        if (concesion_tramite_placa_CL != null) {
          concesion_tramite_placa_CL.value = ''
        }
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
          chk.setAttribute('disable',false);
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
      if (currentstep < 3) {
        if (isRecordGetted[currentstep] != "") {
          fGetInputs();
        }
      } else {
        if (currentstep == 3) {
          showModalFromShown = false;
          fGetInputs();
          if (modalidadDeEntrada == 'I' && ProcessFormalities() == true) {
            fSweetAlertEventNormal(
              "ERROR",
              "HAY TRAMITES REGISTRADOS, DEBE SALVAR LA INFORMACIÓN DE LA PANTALLA O DESMARCAR LOS TRAMITES",
              "error"
            );
            goPrevious = false;
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
              function () {},
              "success",
              $appcfg_pocision_toast,
              $appcfg_icono_toast
            );
          } else {
            fLimpiarPantalla();
            fSweetAlertEventSelect(
              event,
              "INFORMACIÓN",
              "EL NÚMERO DE COLEGIACIÓN NO EXISTE EN NUESTRA BASE DE DATOS, FAVOR VERIFIQUE EL NÚMERO E INTENTELO NUEVAMENTE",
              "warning"
            );
            event.preventDefault();
            event.target.classList.add("errortxt");
            var label = document.getElementById(event.target.id + "label");
            if (label != null) {
              label.classList.add("errorlabel");
            }
            paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
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
            event.target.classList.add("errortxt");
            var label = document.getElementById(event.target.id + "label");
            if (label != null) {
              label.classList.add("errorlabel");
            }
            paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
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
            event.target.classList.add("errortxt");
            var label = document.getElementById(event.target.id + "label");
            if (label != null) {
              label.classList.add("errorlabel");
            }
            paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
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
        event.target.classList.add("errortxt");
        var label = document.getElementById(event.target.id + "label");
        if (label != null) {
          label.classList.add("errorlabel");
        }
        paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
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
              function () {},
              "success",
              $appcfg_pocision_toast,
              $appcfg_icono_toast
            );
          } else {
            fLimpiarPantalla();
            fSweetAlertEventSelect(
              event,
              "INFORMACIÓN",
              "EL RTN DEL SOLICITANTE NO EXISTE EN NUESTRA BASE DE DATOS, FAVOR VERIFIQUE EL NÚMERO E INTENTELO NUEVAMENTE",
              "warning"
            );
            event.preventDefault();
            event.target.classList.add("errortxt");
            var label = document.getElementById(event.target.id + "label");
            if (label != null) {
              label.classList.add("errorlabel");
            }
            paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
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
            event.target.classList.add("errortxt");
            var label = document.getElementById(event.target.id + "label");
            if (label != null) {
              label.classList.add("errorlabel");
            }
            paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
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
            event.target.classList.add("errortxt");
            var label = document.getElementById(event.target.id + "label");
            if (label != null) {
              label.classList.add("errorlabel");
            }
            paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
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
        event.target.classList.add("errortxt");
        var label = document.getElementById(event.target.id + "label");
        if (label != null) {
          label.classList.add("errorlabel");
        }
        paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
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
      document.getElementById("Permiso_Explotacion_Encriptado").value = datos[1][0]["Permiso_Explotacion_Encriptado"];              
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
      document.getElementById("Concesion_Encrypted").value =
      datos[1][0]["PermisoEncriptado"];            
      document.getElementById("Permiso_Explotacion").value = "";
      document.getElementById("Permiso_Explotacion_Encriptado").value  = "";
    }

    document.getElementById("concesion_tramites").innerHTML =
      datos[1][0]["Tramites"];

    chkTramites = document.querySelectorAll('input[name="tramites[]"]');      
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
        document.getElementById("IHTTTRA-02_CLATRA-03_R_PS").checked = false;
        document.getElementById("IHTTTRA-02_CLATRA-03_R_PS").disabled = true;
        document.getElementById("row_tramite_X_PS").style.display = "flex";
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
      } else {
        esCambioDePlaca = false;
        document.getElementById("IHTTTRA-03_CLATRA-15_M_CL").checked = false;
        document.getElementById("IHTTTRA-03_CLATRA-15_M_CL").disabled = false;
        document.getElementById("concesion_tramite_placa_CL").style =
          "display:none;text-transform: uppercase;";
        document
          .getElementById("concesion_tramite_placa_CL")
          .removeAttribute("readonly");
      }
      if (
        datos[1][0]["Vencimientos"]["renovacion_certificado_vencido"] == true
      ) {
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
      document.getElementById("ID_Categoria").value =
        datos[1][0]["ID_Categoria"];
      document.getElementById("ID_Tipo_Servicio").value =
        datos[1][0]["ID_Tipo_Servico"];
      document.getElementById("ID_Modalidad").value =
        datos[1][0]["ID_Modalidad"];
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
      document.getElementById("ID_Unidad").value = 
        datos[1][0]["Unidad"][0]["ID_Unidad"];
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
    document.getElementById("btnconcesion").style = "display:inline;";
    document.getElementById("btnmultas").style = "display:inline;";
    document.getElementById("btnconsultas").style = "display:inline;";
    document.getElementById("btnperexp").style = "display:inline;";
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
    document.getElementById("concesion1_fecexp").innerHTML =
      document.getElementById("concesion1_fecexp").innerHTML;
    document.getElementById("concesion1_resolucion").innerHTML =
      document.getElementById("concesion1_resolucion").innerHTML;
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
      document.getElementById("concesion1_placaanterior").style =
        "display:none;";
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
    //* INICIO: Despliega la información del stepper content y oculta el gif de procesando    */
    //*****************************************************************************************/
    document.getElementById("id_stepper_gif").style = 'display:flex';
    document.getElementById("id_img_stepper_gif").style = 'display:flex';   
    document.getElementById("concesion_tramites").style = "display:none;"; 
    document.getElementById("id_stepper_content").style = 'display:none';
    //*****************************************************************************************/
    //* FINAL: Despliega la información del stepper content y oculta el gif de procesando    */
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
        if (typeof datos[0] != "undefined") {
          if (datos[0] > 0) {
            //**************************************************************************************************************************/
            // Si el vehiculo fue recuperado desde el IP, favor continuar con el proceso normal y mover al informacion a la pantalla
            //**************************************************************************************************************************/
            if (typeof(datos[1][0]["Unidad"]) == "undefined" || typeof(datos[1][0]["Unidad"][0]) == "undefined" || typeof(datos[1][0]["Unidad"][0]["Bloqueado"]) == "undefined" || datos[1][0]["Unidad"][0]["Bloqueado"] == false 
            ) {
              if (typeof datos[1][0]["Unidad"] == "undefined" || typeof datos[1][0]["Unidad"][0] == "undefined" || typeof datos[1][0]["Unidad"][0]["Multas"] == "undefined" || typeof datos[1][0]["Unidad"][0]["Multas"][0] == "undefined") {
                //Ocultar Pantalla Modal donde se Ingresa a Concesión
                $("#modalConcesion").modal("hide");
                isRecordGetted[currentstep] = idConcesion;
                //***********************************************************************************/
                //**Presentando la tabla de tramites                                                */
                //***********************************************************************************/        
                document.getElementById("btnSalvarConcesion").style = "display:fixed;";
                document.getElementById("concesion_tramites").style = "display:fixed;";
                document.getElementById("div-vista-1").style = "display:fixed;";                
                document.getElementById("concesion_tramites").value = "";
                f_RenderConcesion(datos);
                seRecuperoVehiculoDesdeIP = 0;
                //**************************************************************************************************************************/
                //*Enviando Toast de Exito en Recuperación la Información de la Concesión
                //**************************************************************************************************************************/
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
                  function () {},
                  "success",
                  $appcfg_pocision_toast,
                  $appcfg_icono_toast
                );
                //**************************************************************************************************************************/
                //* Inicio: Modalidad de Entrada I = INSERT
                //**************************************************************************************************************************/
                modalidadDeEntrada = 'I';
                //**************************************************************************************************************************/
                //* Final: Modalidad de Entrada I = INSERT
                //**************************************************************************************************************************/
                if (typeof datos[1][0] != "undefined" &&
                    typeof datos[1][0]["Unidad"] != "undefined" &&
                    typeof datos[1][0]["Unidad"][0] != "undefined" &&
                    typeof datos[1][0]["Unidad"][0]["Preforma"] != "undefined" &&
                    typeof datos[1][0]["Unidad"][0]["Preforma"][0] != "undefined"
                ) {

                  var html = mallaDinamica(
                    {
                      titulo: "PREFORMAS PENDIENTES DE RESOLUCIÓN",
                      name: "PREFORMA",
                    },
                    datos[1][0]["Unidad"][0]["Preforma"]
                  );

                  fSweetAlertEventSelect(
                    event,
                    "INFORMACIÓN",
                    "LA UNIDAD Y/O EL CERTIFICADO PREFORMAS INGRESADAS PENDIENTES DE RESOLUCIÓN",
                    "info",
                    html
                  );
                }
                //*********************************************************************************/
                //* Pocisionandose en la parte superior de la pantalla                            */
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
                fSweetAlertEventSelect(
                  event,
                  "VALIDACIONES",
                  "LA UNIDAD TIENE MULTA(S) PENDIENTE(S) DE PAGO, FAVOR PAGAR LAS MULTAS PREVIO A INGRESAR EL TRAMITE",
                  "error",
                  html
                );
                event.preventDefault();
                event.target.classList.add("errortxt");
                var label = document.getElementById(event.target.id + "label");
                if (label != null) {
                  document
                    .getElementById(event.target.id + "label")
                    .classList.add("errorlabel");
                }
                paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
                isError = true;
                document.getElementById("btnconcesion").style = "display:none;";
                document.getElementById("btnmultas").style = "display:none;";
                document.getElementById("btnconsultas").style = "display:none;";
                document.getElementById("btnperexp").style = "display:none;";
              }
            } else {
              if (
                datos[1][0]["Codigo_IP"] == 200 &&
                datos[1][0]["Unidad"][0]["Bloqueado"] == true
              ) {
                fLimpiarPantalla();
                fSweetAlertEventSelect(
                  event,
                  "BLOQUEADA",
                  "LA UNIDAD ESTA BLOQUEADA EN EL IP, NO SE PUEDE REGISTRAR ESTA UNIDAD",
                  "error"
                );
                event.preventDefault();
                event.target.classList.add("errortxt");
                var label = document.getElementById(event.target.id + "label");
                if (label != null) {
                  document
                    .getElementById(event.target.id + "label")
                    .classList.add("errorlabel");
                }
                paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
                isError = true;
                document.getElementById("btnconcesion").style = "display:none;";
                document.getElementById("btnmultas").style = "display:none;";
                document.getElementById("btnconsultas").style = "display:none;";
                document.getElementById("btnperexp").style = "display:none;";
              } else {
                if (datos[1][0]["Codigo_IP"] != 200) {
                  fLimpiarPantalla();
                  fSweetAlertEventSelect(
                    event,
                    "CONEXIÓN IP",
                    "LA CONEXIÓN AL IP, ESTA PRESENTANDO PROBLEMAS, FAVOR INTENTELO EN UN MOMENTO Y SI EL ERROR PERSISTE CONTACTE AL ADMON DEL SISTEMA",
                    "error"
                  );
                  event.preventDefault();
                  event.target.classList.add("errortxt");
                  var label = document.getElementById(
                    event.target.id + "label"
                  );
                  if (label != null) {
                    document
                      .getElementById(event.target.id + "label")
                      .classList.add("errorlabel");
                  }
                  paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
                  isError = true;
                  document.getElementById("btnconcesion").style =
                    "display:none;";
                  document.getElementById("btnmultas").style = "display:none;";
                  document.getElementById("btnconsultas").style =
                    "display:none;";
                  document.getElementById("btnperexp").style = "display:none;";
                }
              }
            }

          } else {
            fLimpiarPantalla();
            fSweetAlertEventSelect(
              event,
              "INFORMACIÓN",
              "LA CONCESIÓN ASOCIADA AL SOLICITANTE ACTUAL NO EXISTE EN NUESTRA BASE DE DATOS, FAVOR VERIFIQUE EL NÚMERO E INTENTELO NUEVAMENTE",
              "warning"
            );
            event.preventDefault();
            event.target.classList.add("errortxt");
            var label = document.getElementById(event.target.id + "label");
            if (label != null) {
              document
                .getElementById(event.target.id + "label")
                .classList.add("errorlabel");
            }
            paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
            isError = true;
            //document.getElementById("idVista").style = "display:none;";
            document.getElementById("btnconcesion").style = "display:none;";
            document.getElementById("btnmultas").style = "display:none;";
            document.getElementById("btnconsultas").style = "display:none;";
            document.getElementById("btnperexp").style = "display:none;";
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
            event.target.classList.add("errortxt");
            var label = document.getElementById(event.target.id + "label");
            if (label != null) {
              document
                .getElementById(event.target.id + "label")
                .classList.add("errorlabel");
            }
            paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
            isError = true;
            //document.getElementById("idVista").style = "display:none;";
            document.getElementById("btnconcesion").style = "display:none;";
            document.getElementById("btnmultas").style = "display:none;";
            document.getElementById("btnconsultas").style = "display:none;";
            document.getElementById("btnperexp").style = "display:none;";
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
            event.target.classList.add("errortxt");
            var label = document.getElementById(event.target.id + "label");
            if (label != null) {
              document
                .getElementById(event.target.id + "label")
                .classList.add("errorlabel");
            }
            paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
            isError = true;
            //document.getElementById("idVista").style = "display:none;";
            document.getElementById("btnconcesion").style = "display:none;";
            document.getElementById("btnmultas").style = "display:none;";
            document.getElementById("btnconsultas").style = "display:none;";
            document.getElementById("btnperexp").style = "display:none;";
          }
        }
        //*****************************************************************************************/
        //* INICIO: Despliega la información del stepper content y oculta el gif de procesando    */
        //*****************************************************************************************/
        document.getElementById("id_stepper_gif").style = 'display:none';
        document.getElementById("id_img_stepper_gif").style = 'display:none';
        document.getElementById("id_stepper_content").style = 'display:block';
        //*****************************************************************************************/
        //* FINAL: Despliega la información del stepper content y oculta el gif de procesando    */
        //*****************************************************************************************/
      })
      .catch((error) => {
        //*****************************************************************************************/
        //* INICIO: Despliega la información del stepper content y oculta el gif de procesando    */
        //*****************************************************************************************/
        document.getElementById("id_stepper_gif").style = 'display:none';
        document.getElementById("id_img_stepper_gif").style = 'display:none';
        document.getElementById("id_stepper_content").style = 'display:block';
        //*****************************************************************************************/
        //* FINAL: Despliega la información del stepper content y oculta el gif de procesando    */
        //*****************************************************************************************/
        //fLimpiarPantalla();
        console.log('f_FetchCallConcesion catch ' + error);
        fSweetAlertEventSelect(
          event,
          "CONEXÍON",
          "ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA",
          "warning"
        );
        event.preventDefault();
        event.target.classList.add("errortxt");
        var label = document.getElementById(event.target.id + "label");
        if (label != null) {
          document
            .getElementById(event.target.id + "label")
            .classList.add("errorlabel");
        }
        paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
        isError = true;
        //document.getElementById("idVista").style = "display:none;";
        document.getElementById("btnconcesion").style = "display:none;";
        document.getElementById("btnmultas").style = "display:none;";
        document.getElementById("btnconsultas").style = "display:none;";
        document.getElementById("btnperexp").style = "display:none;";
      });
  }
  //**************************************************************************************/
  //*Inicio: Editando la informacion de las concesiones ya registradas en el expediente
  //**************************************************************************************/
  function fEditarConcesion(idConcesion){
    //*****************************************************************************************/
    //* INICIO: Despliega la información del stepper content y oculta el gif de procesando    */
    //*****************************************************************************************/
    document.getElementById("id_stepper_gif").style = 'display:flex';
    document.getElementById("id_img_stepper_gif").style = 'display:flex';
    document.getElementById("id_stepper_content").style = 'display:none';
    document.getElementById("concesion_tramites").style = "display:none;";
    //*****************************************************************************************/
    //* FINAL: Despliega la información del stepper content y oculta el gif de procesando    */
    //*****************************************************************************************/
    var datos;
    var response;
    // Get the URL parameters from the current page
    const urlParams = new URLSearchParams(window.location.search);
    // Get a specific parameter by name
    const RAM = urlParams.get('RAM'); // Número de RAM
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
    fd.append("RAM", RAM);
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
            if (typeof(datos[1][0]["Unidad"]) == "undefined" || typeof(datos[1][0]["Unidad"][0]) == "undefined" || typeof(datos[1][0]["Unidad"][0]["Bloqueado"]) == "undefined" || datos[1][0]["Unidad"][0]["Bloqueado"] == false 
            ) {
              if (typeof datos[1][0]["Unidad"] == "undefined" || typeof datos[1][0]["Unidad"][0] == "undefined" || typeof datos[1][0]["Unidad"][0]["Multas"] == "undefined" || typeof datos[1][0]["Unidad"][0]["Multas"][0] == "undefined") {
                //***********************************************************************************/
                //**Presentando la tabla de tramites                                                */
                //***********************************************************************************/        
                document.getElementById("btnSalvarConcesion").style = "display:fixed;";
                document.getElementById("concesion_tramites").style = "display:fixed;";
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
                  function () {},
                  "success",
                  $appcfg_pocision_toast,
                  $appcfg_icono_toast
                );
                //**************************************************************************************************************************/
                //* Inicio: Modalidad de Entrada U = UPDATE
                //**************************************************************************************************************************/
                modalidadDeEntrada = 'U';
                //**************************************************************************************************************************/
                //* Final: Modalidad de Entrada U = UPDATE
                //**************************************************************************************************************************/
                if (typeof datos[1][0] != "undefined" &&
                    typeof datos[1][0]["Unidad"] != "undefined" &&
                    typeof datos[1][0]["Unidad"][0] != "undefined" &&
                    typeof datos[1][0]["Unidad"][0]["Preforma"] != "undefined" &&
                    typeof datos[1][0]["Unidad"][0]["Preforma"][0] != "undefined"
                ) {

                  var html = mallaDinamica(
                    {
                      titulo: "PREFORMAS PENDIENTES DE RESOLUCIÓN",
                      name: "PREFORMA",
                    },
                    datos[1][0]["Unidad"][0]["Preforma"]
                  );

                  fSweetAlertSelect(
                    "INFORMACIÓN",
                    "LA UNIDAD Y/O EL CERTIFICADO PREFORMAS INGRESADAS PENDIENTES DE RESOLUCIÓN",
                    "info",
                    html
                  );
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
                document.getElementById("btnconcesion").style = "display:none;";
                document.getElementById("btnmultas").style = "display:none;";
                document.getElementById("btnconsultas").style = "display:none;";
                document.getElementById("btnperexp").style = "display:none;";
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
        //* INICIO: Despliega la información del stepper content y oculta el gif de procesando    */
        //*****************************************************************************************/
        document.getElementById("id_stepper_gif").style = 'display:none';
        document.getElementById("id_img_stepper_gif").style = 'display:none';        
        document.getElementById("id_stepper_content").style = 'display:block';
        //*****************************************************************************************/
        //* FINAL: Despliega la información del stepper content y oculta el gif de procesando    */
        //*****************************************************************************************/
      })
      .catch((error) => {
        //*****************************************************************************************/
        //* INICIO: Despliega la información del stepper content y oculta el gif de procesando    */
        //*****************************************************************************************/
        document.getElementById("id_stepper_gif").style = 'display:none';
        document.getElementById("id_img_stepper_gif").style = 'display:none';
        document.getElementById("id_stepper_content").style = 'display:block';
        //*****************************************************************************************/
        //* FINAL: Despliega la información del stepper content y oculta el gif de procesando    */
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
      document.getElementById("Permiso_Explotacion_Encriptado").value = datos[1][0]["Permiso_Explotacion_Encriptado"];              
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
      document.getElementById("Concesion_Encrypted").value =
      datos[1][0]["PermisoEncriptado"];            
      document.getElementById("Permiso_Explotacion").value = "";
      document.getElementById("Permiso_Explotacion_Encriptado").value  = "";
    }

    document.getElementById("concesion_tramites").innerHTML =
      datos[1][0]["Tramites"];

    chkTramites = document.querySelectorAll('input[name="tramites[]"]');      
  
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
      var el = document.getElementById("IHTTTRA-02_CLATRA-02_R_CO");
      if (el != null) {
        document.getElementById("IHTTTRA-02_CLATRA-02_R_CO").checked = false;
        document.getElementById("IHTTTRA-02_CLATRA-02_R_CO").disabled = true;
        document.getElementById("IHTTTRA-02_CLATRA-01_R_PE").checked = false;
        document.getElementById("IHTTTRA-02_CLATRA-01_R_PE").disabled = true;
        document.getElementById("row_tramite_X_CO").style.display = "flex";
        document.getElementById("row_tramite_X_PE").style.display = "flex";
      } else {
        document.getElementById("IHTTTRA-02_CLATRA-03_R_PS").checked = false;
        document.getElementById("IHTTTRA-02_CLATRA-03_R_PS").disabled = true;
        document.getElementById("row_tramite_X_PS").style.display = "flex";
      }
      //***********************************************************************************************************************/
      //* INICIO: Se pago el cambio de placa de la unidad actual, esto se hace cuando se detecta que la undiad cambio de placa
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
      } else {
        esCambioDePlaca = false;
        document.getElementById("IHTTTRA-03_CLATRA-15_M_CL").checked = false;
        document.getElementById("IHTTTRA-03_CLATRA-15_M_CL").disabled = false;
        document.getElementById("concesion_tramite_placa_CL").style =
          "display:none;text-transform: uppercase;";
        document
          .getElementById("concesion_tramite_placa_CL")
          .removeAttribute("readonly");
      }
      //***********************************************************************************************************************/
      //* FINAL: Se pago el cambio de placa de la unidad actual, esto se hace cuando se detecta que la undiad cambio de placa
      //* y no se encuentra en nuestros registros el pago de ese cambio de placa
      //***********************************************************************************************************************/

      //***********************************************************************************************************************/
      //* INICIO: Si el certificado no esta vigente y requiere renovacion 
      //***********************************************************************************************************************/
      if (
        datos[1][0]["Vencimientos"]["renovacion_certificado_vencido"] == true
      ) {
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
        //*Inicio Ocultar Linea de Reimpresión de Certificado
        //***********************************************************************************************************************/
        document.getElementById("row_tramite_X_CO").style.display = "none";
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
          //***********************************************************************************************************************/
          //*Inicio Ocultar Linea de Reimpresión de Permiso Especial
          //***********************************************************************************************************************/
          document.getElementById("row_tramite_X_PS").style.display = "none";
          //***********************************************************************************************************************/
          //*Final  Ocultar Linea de Reimpresión de Permiso Especial
          //***********************************************************************************************************************/
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
        //Inicio
        document.getElementById("row_tramite_X_PE").style.display = "none";
        //Final
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
      document.getElementById("ID_Categoria").value =
        datos[1][0]["ID_Categoria"];
      document.getElementById("ID_Tipo_Servicio").value =
        datos[1][0]["ID_Tipo_Servico"];
      document.getElementById("ID_Modalidad").value =
        datos[1][0]["ID_Modalidad"];
      document.getElementById("ID_Clase_Servicio").value =
        datos[1][0]["ID_Clase_Servico"];
      //***********************************************************************************************************************************/
      //* FINAL: Caracterización de la Concesion
      //***********************************************************************************************************************************/
      
      //***********************************************************************************************************************************/
      //* INICIO: Fechas y Cantidad de Renovaciones por Concesion y Permiso de Explotación
      //***********************************************************************************************************************************/
      document.getElementById("NuevaFechaVencimientoConcesion").value =
        datos[1][0]["Vencimientos"]["Nueva_Fecha_Expiracion"];
      document.getElementById("NuevaFechaVencimientoPerExp").value =
        datos[1][0]["Vencimientos"]["Nueva_Fecha_Expiracion_Explotacion"];
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
      document.getElementById("ID_Unidad").value = 
        datos[1][0]["Unidad"][0]["ID_Unidad"];
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
      document.getElementById("alto").value =
        datos[1][0]["Unidad"][0]["Alto"];
      document.getElementById("largo").value =
        datos[1][0]["Unidad"][0]["Largo"];
      document.getElementById("ancho").value =
        datos[1][0]["Unidad"][0]["Ancho"];
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
    document.getElementById("btnconcesion").style = "display:inline;";
    document.getElementById("btnmultas").style = "display:inline;";
    document.getElementById("btnconsultas").style = "display:inline;";
    document.getElementById("btnperexp").style = "display:inline;";
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
    document.getElementById("concesion1_fecexp").innerHTML =
      document.getElementById("concesion1_fecexp").innerHTML;
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
      document.getElementById("concesion1_placaanterior").style =
        "display:none;";
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
    document.getElementById("ID_Unidad1").value = 
      datos[1][0]["Unidad"][1]["ID_Unidad"];
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
    document.getElementById("alto1").value =
      datos[1][0]["Unidad"][1]["Alto"];
    document.getElementById("largo1").value =
      datos[1][0]["Unidad"][1]["Largo"];
    document.getElementById("ancho1").value =
      datos[1][0]["Unidad"][1]["Ancho"];
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
    document.getElementById("btnCambiarUnidad").style.display = "flex";
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
       return concesionNumber.some(item => item.Concesion === concesion);
    }
 
    let existe = buscar_placa_concesion(concesion);
 
    if (existe) {
       if (currentstep != 2) {
          stepperForm.to(3);
       }
       fEditarConcesion(concesion);
    } else {
       if (concesion == '') {
          Swal.fire({
             title: "!NO HAY ELEMENTO¡",
             text: 'INGRESE UNA CONCESION PARA BUSCAR O INGRESAR',
             icon: 'warning',
             confirmButtonText: 'OK',
          })
       } else {
          Swal.fire({
             title: `!LA CONCESION " ${concesion} " NO EXISTE¡`,
             text: '¿DESEA AGREGAR LA CONCESIÓN A LA SOLICITUD',
             icon: 'warning',
             showCancelButton: true,
             confirmButtonText: 'SÍ',
             cancelButtonText: 'CANCELAR'
          }).then((result) => {
             //* si confirma que esta seguro de eliminar llamamos la funcion para que eliminar de la base de datos.
             if (result.isConfirmed) {
                if (currentstep != 2) {
                   stepperForm.to(3);
                }
                f_FetchCallConcesion(concesion, event, 'input-prefetch');
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
      var Certificado = document.getElementById(
        "concesion_concesion"
      ).innerHTML;
      var Permiso_Explotacion = document
        .getElementById("concesion_perexp")
        .innerHTML.split("||")[0];
      var Permiso_Especial = "";
    } else {
      var Permiso_Especial = document.getElementById(
        "concesion_concesion"
      ).innerHTML;
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
      Nombre_Propietario: document.getElementById(
        "concesion_nombre_propietario"
      ).innerHTML,
      RTN_Propietario: document.getElementById(
        "concesion_identidad_propietario"
      ).innerHTML,
      Modelo: document.getElementById("concesion_modelo_vehiculo").value,
      Tipo: document.getElementById("concesion_tipo_vehiculo").value,
      ID_Placa_Antes_Replaqueo: document.getElementById(
        "concesion_placaanterior"
      ).innerHTML,
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
      Nombre_Propietario: document.getElementById(
        "concesion1_nombre_propietario"
      ).innerHTML,
      RTN_Propietario: document.getElementById(
        "concesion1_identidad_propietario"
      ).innerHTML,
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
          let Fecha_Expiracion = '';
          let Fecha_Expiracion_Nueva = '';

          if (chk.id === 'IHTTTRA-02_CLATRA-01_R_PE') {
            Cantidad_Vencimientos = document.getElementById("CantidadRenovacionesPerExp").value;
            Fecha_Expiracion_Nueva = document.getElementById("NuevaFechaVencimientoPerExp").value
            Fecha_Expiracion = document.getElementById("NuevaFechaVencimientoPerExp").value
          } else {
            if (chk.id === 'IHTTTRA-02_CLATRA-02_R_CO' || chk.id === 'IHTTTRA-02_CLATRA-02_R_PS') {
              Cantidad_Vencimientos = document.getElementById("CantidadRenovacionesConcesion").value;
              Fecha_Expiracion_Nueva = document.getElementById("NuevaFechaVencimientoConcesion").value
              Fecha_Expiracion = document.getElementById("FechaVencimientoConcesion").value
            }      
          }

          TramitesPreforma.push({
            ID_Compuesto: chk.id,
            Codigo: chk.value,
            descripcion: document.getElementById("descripcion_" + chk.value)
              .innerHTML,
            ID_Tramite: chk.getAttribute("data-id"),
            Monto: chk.getAttribute("data-monto"),
            Total_A_Pagar: parseFloat(parseFloat(chk.getAttribute("data-monto")).toFixed(2) * Cantidad_Vencimientos).toFixed(2),
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
  function guardarConcesionSalvadaPreforma (Tramites,Unidades) {
    var index = 0;
    var Concesion = '';
    var Concesion_Encriptada;
    var Permiso_Explotacion_Encriptado;
    var esCarga;
    var esCertificado;
    var Placa  = '';
    var Permiso_Explotacion = '';
    var ID_Formulario_Solicitud  = '';
    var TramitesPreforma = Array();
    var index = 1;
    var Unidad1 = '';
    //*********************************************************************************************************/
    //* Inicio: Recorriendo arreglo de concesiones y tramites
    //*********************************************************************************************************/
    Tramites.forEach((row) => {
      //*********************************************************************************************************/
      //* La primera vez que entra llena la variable Concesion
      //*********************************************************************************************************/
      if (index == 1) {
        if (row['N_Permiso_Especial'] == '') {
          Concesion = row['N_Certificado'];
          Concesion_Encriptada = row['CertificadoEncriptado'];
          Permiso_Explotacion = row['Permiso_Explotacion'];
          Permiso_Explotacion_Encriptado = row['Permiso_Explotacion_Encriptado'];
        } else {
          Concesion = row['N_Permiso_Especial'];
          Concesion_Encriptada = row['PermisoEspecialEncriptado'];
          Permiso_Explotacion = '';
        }
      }
      if (Concesion == row['N_Permiso_Especial'] || Concesion == row['N_Certificado']) {
        
        esCarga = Boolean(row['esCarga']);
        esCertificado = Boolean(row['esCertificado']);

        if (row['ID_Placa1'] != null) {
          Placa = row['ID_Placa'] + '->' + row['ID_Placa1'];
        } else {
          Placa = row['ID_Placa'];
        }

        var Cantidad_Vencimientos = 1
        var Fecha_Expiracion_Nueva = '';
        var Fecha_Expiracion = '';
        if (row['ID_CHECK'] == 'IHTTTRA-02_CLATRA-01_R_PE' && row["Vencimientos"] != false) {
          Fecha_Expiracion = row['Fecha_Expiracion_Explotacion'];
          Cantidad_Vencimientos = row["Vencimientos"]["renper-explotacion-cantidad"];
          Fecha_Expiracion_Nueva = row["Vencimientos"]["Nueva_Fecha_Expiracion_Explotacion"];
        } else {
          if ((row['ID_CHECK'] == 'IHTTTRA-02_CLATRA-02_R_CO' || row['ID_CHECK'] == 'IHTTTRA-02_CLATRA-02_R_PS') && (row["Vencimientos"] != false)) {
            Fecha_Expiracion = row['Fecha_Expiracion'];
            Cantidad_Vencimientos =  row["Vencimientos"]["rencon-cantidad"];
            Fecha_Expiracion_Nueva = row["Vencimientos"]["Nueva_Fecha_Expiracion"];
          }      
        }

        ID_Formulario_Solicitud = row['ID_Formulario_Solicitud'];
        TramitesPreforma.push({
          ID: row['ID'],
          ID_Compuesto: row['ID_CHECK'],
          Codigo: row['ID_Tramite'],
          descripcion: row['DESC_Tipo_Tramite'] + ' ' + row['DESC_Clase_Tramite'],
          ID_Tramite: row['ID_Tramite'],
          Monto: row['Monto'],
          Total_A_Pagar: parseFloat(parseFloat(row['Monto']).toFixed(2) * Cantidad_Vencimientos).toFixed(2),
          Cantidad_Vencimientos: Cantidad_Vencimientos,
          Fecha_Expiracion: Fecha_Expiracion,
          Fecha_Expiracion_Nueva: Fecha_Expiracion_Nueva,
          ID_Categoria: row['ID_Tipo_Categoria'],
          ID_Tipo_Servicio: row['ID_TIpo_Servicio'],
          ID_Modalidad: row['ID_Modalidad'],
          ID_Clase_Servico: row['ID_Clase_Servicio'],
        });
        //*************************************************************/
        //* Si trae Unidad 1
        //*************************************************************/
        Unidad1 = '';
        if (typeof Unidades[Concesion] != 'undefined' && typeof Unidades[Concesion][1] != 'undefined') {
          Unidad1 = Unidades[Concesion][1];
        }
        if (Tramites.length == index){
          currentConcesionIndex = updateCollection(Concesion);
          concesionNumber[currentConcesionIndex] = {
            esCarga: esCarga,
            esCertificado: esCertificado,
            Concesion_Encriptada: Concesion_Encriptada,
            Concesion: Concesion,
            Permiso_Explotacion_Encriptado: Permiso_Explotacion_Encriptado,
            Permiso_Explotacion: Permiso_Explotacion,
            ID_Expediente: '',
            ID_Solicitud: '',
            ID_Formulario_Solicitud: ID_Formulario_Solicitud,
            CodigoAvisoCobro: '',
            ID_Resolucion: '',
            Placa: Placa,
            Unidad: Unidades[Concesion][0],
            Unidad1: Unidad1,
            Tramites: TramitesPreforma,
          }
          //***********************************************************************/
          //* Agregando concesion pura */
          //***********************************************************************/
          addElementToAutoComplete(Concesion,Concesion)
          //***********************************************************************/
          //* Agregando concesion con permiso de explotacion */
          //***********************************************************************/
          if (Permiso_Explotacion != '') {
            addElementToAutoComplete(Concesion, Permiso_Explotacion + ' => ' + Concesion)        
          }
          //***********************************************************************/
          //* Agregando placa actual asociada a concesion */
          //***********************************************************************/
          addElementToAutoComplete(Concesion,Unidades[Concesion][0].ID_Placa + ' => ' + Concesion)    
          //***********************************************************************/
          //* Agregando placa Anterior Asociada a concesion */
          //***********************************************************************/
          if (Unidades[Concesion][0].ID_Placa_Antes_Replaqueo != Unidades[Concesion][0].ID_Placa) {
            addElementToAutoComplete(Concesion,Unidades[Concesion][0].ID_Placa_Antes_Replaqueo + ' => ' + Concesion)    
          }
          if (Unidad1 != '' && Unidad1.ID_Placa != 'undefined') {
            //***********************************************************************/
            //* Agregando placa actual asociada a concesion */
            //***********************************************************************/
            addElementToAutoComplete(Concesion,Unidad1.ID_Placa + ' => ' + Concesion)    
            //***********************************************************************/
            //* Agregando placa Anterior Asociada a concesion */
            //***********************************************************************/
            if (Unidad1.ID_Placa_Antes_Replaqueo != Unidad1.ID_Placa && Unidad1.ID_Placa_Antes_Replaqueo != 'undefined') {
              addElementToAutoComplete(Concesion,Unidad1.ID_Placa_Antes_Replaqueo + ' => ' + Concesion)    
            }
            //***********************************************************************/      
          }
        }
      } else {
        //*************************************************************/
        //* Si trae Unidad 1
        //*************************************************************/
        Unidad1 = '';
        if (typeof Unidades[Concesion] != 'undefined' && typeof Unidades[Concesion][1] != 'undefined') {
          Unidad1 = Unidades[Concesion][1];
        }
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
          ID_Expediente: '',
          ID_Solicitud: '',
          ID_Formulario_Solicitud: ID_Formulario_Solicitud,
          CodigoAvisoCobro: '',
          ID_Resolucion: '',
          Placa: Placa,
          Unidad: Unidades[Concesion][0],
          Unidad1: Unidad1,
          Tramites: TramitesPreforma,
        };
        //***********************************************************************/
        //* Agregando concesion pura */
        //***********************************************************************/
        addElementToAutoComplete(Concesion,Concesion)
        //***********************************************************************/
        //* Agregando concesion con permiso de explotacion */
        //***********************************************************************/
        if (Permiso_Explotacion != '') {
          addElementToAutoComplete(Concesion, Permiso_Explotacion + ' => ' + Concesion)        
        }
        //***********************************************************************/
        //* Agregando placa actual asociada a concesion */
        //***********************************************************************/
        addElementToAutoComplete(Concesion,Unidades[Concesion][0].ID_Placa + ' => ' + Concesion);   
        //***********************************************************************/
        //* Agregando placa Anterior Asociada a concesion */
        //***********************************************************************/
        if (Unidades[Concesion][0].ID_Placa_Antes_Replaqueo != Unidades[Concesion][0].ID_Placa) {
          addElementToAutoComplete(Concesion,Unidades[Concesion][0].ID_Placa_Antes_Replaqueo + ' => ' + Concesion);
        }
        if (Unidad1 != '' && Unidad1.ID_Placa != 'undefined') {
          //***********************************************************************/
          //* Agregando placa actual asociada a concesion */
          //***********************************************************************/
          addElementToAutoComplete(Concesion,Unidad1.ID_Placa + ' => ' + Concesion);    
          //***********************************************************************/
          //* Agregando placa Anterior Asociada a concesion */
          //***********************************************************************/
          if (Unidad1.ID_Placa_Antes_Replaqueo != Unidad1.ID_Placa && Unidad1.ID_Placa_Antes_Replaqueo != 'undefined') {
            addElementToAutoComplete(Concesion,Unidad1.ID_Placa_Antes_Replaqueo + ' => ' + Concesion); 
          }
          //***********************************************************************/      
        }
        if (row['N_Permiso_Especial'] == '') {
          Concesion = row['N_Certificado'];
          Concesion_Encriptada = row['CertificadoEncriptado'];
          Permiso_Explotacion = row['Permiso_Explotacion'];
          Permiso_Explotacion_Encriptado = row['Permiso_Explotacion_Encriptado'];
        } else {
          Concesion = row['N_Permiso_Especial'];
          Concesion_Encriptada = row['PermisoEspecialEncriptado'];
          Permiso_Explotacion = '';
        }
        //**********************************************************************************************************************/
        //*Agregando la concesión al arreglo de indice de concesiones y recuperando el indice de la concesion                  */
        //**********************************************************************************************************************/
        if (row['ID_Placa1'] != null) {
          Placa = row['ID_Placa'] + '->' + row['ID_Placa1'];
        } else {
          Placa = row['ID_Placa'];
        }

        let Cantidad_Vencimientos = 1
        let Fecha_Expiracion_Nueva = '';
        let Fecha_Expiracion = '';
        if (row['ID_CHECK'] == 'IHTTTRA-02_CLATRA-01_R_PE' && row["Vencimientos"] != false) {
          Fecha_Expiracion = row['Fecha_Expiracion_Explotacion'];
          Cantidad_Vencimientos = row["Vencimientos"]["renper-explotacion-cantidad"];
          Fecha_Expiracion_Nueva = row["Vencimientos"]["Nueva_Fecha_Expiracion_Explotacion"];
        } else {
          if ((row['ID_CHECK'] == 'IHTTTRA-02_CLATRA-02_R_CO' || row['ID_CHECK'] == 'IHTTTRA-02_CLATRA-02_R_PS') && (row["Vencimientos"] != false)) {
            Fecha_Expiracion = row['Fecha_Expiracion'];
            Cantidad_Vencimientos =  row["Vencimientos"]["rencon-cantidad"];
            Fecha_Expiracion_Nueva = row["Vencimientos"]["Nueva_Fecha_Expiracion"];
          }      
        }
        
        ID_Formulario_Solicitud = row['ID_Formulario_Solicitud'];
        TramitesPreforma = [];
        TramitesPreforma.push({
          ID: row['ID'],
          ID_Compuesto: row['ID_CHECK'],
          Codigo: row['ID_Tramite'],
          descripcion: row['DESC_Tipo_Tramite'] + ' ' + row['DESC_Clase_Tramite'],
          ID_Tramite: row['ID_Tramite'],
          Monto: row['Monto'],
          Total_A_Pagar: parseFloat(parseFloat(row['Monto']).toFixed(2) * Cantidad_Vencimientos).toFixed(2),
          Cantidad_Vencimientos: Cantidad_Vencimientos,
          Fecha_Expiracion: Fecha_Expiracion,
          Fecha_Expiracion_Nueva: Fecha_Expiracion_Nueva,
          ID_Categoria: row['ID_Tipo_Categoria'],
          ID_Tipo_Servicio: row['ID_TIpo_Servicio'],
          ID_Modalidad: row['ID_Modalidad'],
          ID_Clase_Servico: row['ID_Clase_Servicio'],
        });
        //*************************************************************/
        //* Si trae Unidad 1
        //*************************************************************/
        Unidad1 = '';
        if (typeof Unidades[Concesion] != 'undefined' && typeof Unidades[Concesion][1] != 'undefined') {
          Unidad1 = Unidades[Concesion][1];
        }   
        if (Tramites.length == index) {
          currentConcesionIndex = updateCollection(Concesion);
          concesionNumber[currentConcesionIndex] = {
            esCarga: esCarga,
            esCertificado: esCertificado,
            Concesion_Encriptada: Concesion_Encriptada,
            Concesion: Concesion,
            Permiso_Explotacion_Encriptado: Permiso_Explotacion_Encriptado,
            Permiso_Explotacion: Permiso_Explotacion,
            ID_Expediente: '',
            ID_Solicitud: '',
            ID_Formulario_Solicitud: ID_Formulario_Solicitud,
            CodigoAvisoCobro: '',
            ID_Resolucion: '',
            Placa: Placa,
            Unidad: Unidades[Concesion][0],
            Unidad1: Unidad1,
            Tramites: TramitesPreforma,
          }
          //***********************************************************************/
          //* Agregando concesion pura */
          //***********************************************************************/
          addElementToAutoComplete(Concesion,Concesion)
          //***********************************************************************/
          //* Agregando concesion con permiso de explotacion */
          //***********************************************************************/
          if (Permiso_Explotacion != '') {
            addElementToAutoComplete(Concesion, Permiso_Explotacion + ' => ' + Concesion);        
          }
          //***********************************************************************/
          //* Agregando placa actual asociada a concesion */
          //***********************************************************************/
          addElementToAutoComplete(Concesion,Unidades[Concesion][0].ID_Placa + ' => ' + Concesion);
          //***********************************************************************/
          //* Agregando placa Anterior Asociada a concesion */
          //***********************************************************************/
          if (Unidades[Concesion][0].ID_Placa_Antes_Replaqueo != Unidades[Concesion][0].ID_Placa) {
            addElementToAutoComplete(Concesion,Unidades[Concesion][0].ID_Placa_Antes_Replaqueo + ' => ' + Concesion);
          }
          if (Unidad1 != '' && Unidad1.ID_Placa != 'undefined') {
            //***********************************************************************/
            //* Agregando placa actual asociada a concesion */
            //***********************************************************************/
            addElementToAutoComplete(Concesion,Unidad1.ID_Placa + ' => ' + Concesion);    
            //***********************************************************************/
            //* Agregando placa Anterior Asociada a concesion */
            //***********************************************************************/
            if (Unidad1.ID_Placa_Antes_Replaqueo != Unidad1.ID_Placa && Unidad1.ID_Placa_Antes_Replaqueo != 'undefined') {
              addElementToAutoComplete(Concesion,Unidad1.ID_Placa_Antes_Replaqueo + ' => ' + Concesion);    
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
  function guardarConcesionSalvada (Tramites,Unidad,Unidad1) {
    //**********************************************************************************************************************/
    //*Agregando la concesión al arreglo de indice de concesiones y recuperando el indice de la concesion                  */
    //**********************************************************************************************************************/
    currentConcesionIndex = updateCollection(document.getElementById("concesion_concesion").innerHTML);
    //**********************************************************************************************************************/
    //* Si es la primera vez que se recupera la concesion se guardar el objeto con la concesion                            */
    //**********************************************************************************************************************/
    // Recuperando el valor de la Permisos de Explotación
    var perexpobj = document.getElementById("concesion_perexp");
    var PerExp = "";
    if (perexpobj != null) {
      PerExp = perexpobj.innerHTML;
    }

    if (document.getElementById("concesion_placa").value != document.getElementById("concesion1_placa").value) {
      var Placa = document.getElementById("concesion_placa").value + '->' + document.getElementById("concesion1_placa").value;
    } else {
      var Placa = document.getElementById("concesion_placa").value;
    }

    concesionNumber[currentConcesionIndex] = {
      esCarga: esCarga,
      esCertificado: esCertificado,
      Concesion_Encriptada: document.getElementById("Concesion_Encriptada").value,
      Concesion: document.getElementById("concesion_concesion").innerHTML,
      Permiso_Explotacion_Encriptado: document.getElementById("Permiso_Explotacion_Encriptado").value,
      Permiso_Explotacion: PerExp,
      ID_Expediente: document.getElementById("ID_Expediente").value,
      ID_Solicitud: document.getElementById("ID_Solicitud").value,
      ID_Formulario_Solicitud: document.getElementById("RAM").value,
      CodigoAvisoCobro: document.getElementById("ID_AvisoCobro").value,
      ID_Resolucion: document.getElementById("ID_Resolucion").value,
      Placa: Placa,
      Unidad: Unidad,
      Unidad1: Unidad1,
      Tramites: Tramites,
    };
    //***********************************************************************/
    //* Agregando concesion pura */
    //***********************************************************************/
    addElementToAutoComplete(document.getElementById("concesion_concesion").innerHTML,document.getElementById("concesion_concesion").innerHTML)
    //***********************************************************************/
    //* Agregando concesion con permiso explotacion */
    //***********************************************************************/
    if (PerExp != "") {
      addElementToAutoComplete(document.getElementById("concesion_concesion").innerHTML,PerExp  + ' => ' + document.getElementById("concesion_concesion").innerHTML)
    }
    //***********************************************************************/
    //* Agregando placa actual asociada a concesion */
    //***********************************************************************/
    addElementToAutoComplete(document.getElementById("concesion_concesion").innerHTML,Unidad.Placa   + ' => ' + document.getElementById("concesion_concesion").innerHTML)    
    //***********************************************************************/ 
    //* Agregando placa Anterior Asociada a concesion */
    //***********************************************************************/
    if (Unidad.ID_Placa_Antes_Replaqueo != Unidad.Placa) {
      addElementToAutoComplete(document.getElementById("concesion_concesion").innerHTML,Unidad.ID_Placa_Antes_Replaqueo   + ' => ' + document.getElementById("concesion_concesion").innerHTML)    
    }
    if (Unidad1 != null && Unidad1 != '' && Unidad1.Placa != 'undefined') {
      //***********************************************************************/
      //* Agregando placa actual asociada a concesion */
      //***********************************************************************/
      addElementToAutoComplete(document.getElementById("concesion_concesion").innerHTML,Unidad1.Placa   + ' => ' + document.getElementById("concesion_concesion").innerHTML)    
      //***********************************************************************/
      //* Agregando placa Anterior Asociada a concesion */
      //***********************************************************************/
      if (Unidad1.ID_Placa_Antes_Replaqueo != Unidad1.Placa) {
        addElementToAutoComplete(document.getElementById("concesion_concesion").innerHTML,Unidad1.ID_Placa_Antes_Replaqueo  + ' => ' + document.getElementById("concesion_concesion").innerHTML)    
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
    var Tramites = '';
    // Adjuntando el action al FormData
    fd.append("action", "save-requisitos");
    // Adjuntando el Concesion y Caracterización al FormData
    const checkboxes = document.querySelectorAll('input[name="flexSwitchCheck[]"]:checked');
    if (checkboxes != null && checkboxes.length > 10) {
      // Seleccionar todos los checkboxes que tienen el nombre 'flexSwitchCheck[]'
      const selectedValues = Array.from(checkboxes).map(checkbox => checkbox.value);
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
              function () {},
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
              function () {},
              "success",
              $appcfg_pocision_toast,
              $appcfg_icono_toast
            );
            return false;
          } // final del If de si Hay error
        })
        .catch((error) => {
          console.log('salvarRequicitos() catch error' + error);
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
        function () {},
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
    var Tramites = '';
    var Unidad = null;
    var Unidad1 = null;
    // Adjuntando el action al FormData
    if (document.getElementById("ID_Expediente").value == ''){
      fd.append("action", "save-preforma");
    } else {
      fd.append("action", "save-expediente");
      // Enviar el número de Expediente
      fd.append("ID_Expediente",  document.getElementById("ID_Expediente").value);
      // Enviar el número de Solicitud
      fd.append("ID_Solicitud",  document.getElementById("ID_Solicitud").value);    
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
        if (typeof Datos.ERROR != "undefined") {
          if (typeof Datos.Multas != "undefined") {
            var html = '';
            if (typeof Datos.Multas === "object" && !Array.isArray(Datos.Multas) && Object.keys(Datos.Multas).length > 0) {
              html = mallaDinamica(
                {
                  titulo: "CERTIFICADO Y/O UNIDAD TIENEN MULTAS PENDIENTES DE PAGO",
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
            if (typeof Datos.Multas1 === "object" && !Array.isArray(Datos.Multas1) && Object.keys(Datos.Multas1).length > 0) {
              html = html + mallaDinamica(
                {
                  titulo: "CERTIFICADO Y/O UNIDAD ENTRANTE TIENEN MULTAS PENDIENTES DE PAGO",
                  name: "PREFORMA",
                },
                Datos.Multas1
              );
            }
          }

          if (typeof Datos.Placas != "undefined") {
            if (typeof Datos.Placas === "object" && !Array.isArray(Datos.Placas) && Object.keys(Datos.Placas).length > 0) {
              fSweetAlertEventNormal(
                "INFORMACIÓN",
                "CERTIFICADO EN ESTADO DE IMPRESIÓN O LISTO PARA ENTREGA",
                "info"
              );
            }
          }

          if (typeof Datos.Preforma != "undefined") {
            if (typeof Datos.Preforma === "object" && !Array.isArray(Datos.Preforma) && Object.keys(Datos.Preforma).length > 0) {
             html = html + mallaDinamica(
                {
                  titulo: "PREFORMAS PENDIENTES DE RESOLUCIÓN",
                  name: "PREFORMA",
                },
                Datos.Preforma
              );
              fSweetAlertEventNormal(
                "INFORMACIÓN",
                "LA UNIDAD Y/O EL CERTIFICADO PREFORMAS INGRESADAS PENDIENTES DE RESOLUCIÓN",
                "info",
                html
              );
            }
          }

          return true;

        } else {
          //****************************************************************************************************/
          //* INICIO: CODIGO QUE ESTABLECE LA ETIQUETA DE RAM E ID'S DE TABLAS                                  */
          //****************************************************************************************************/
          if (modalidadDeEntrada == 'I') {
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
            alert(1);
            if (
              typeof Datos.Unidad1 != "undefined" &&
              typeof Datos.Unidad1 != null &&
              typeof Datos.Unidad1 != "false"
            ) {
              document.getElementById("ID_Unidad1").value = Datos.Unidad1;
            } else {
              document.getElementById("ID_Unidad1").value = "";
            }
            alert(2);
            Datos.Tramites.forEach(function (Tramite) {
              var chk = document.getElementById(Tramite.ID_Compuesto);
              if (chk != null) {
                chk.setAttribute("data-id", Tramite.ID);
              }
            });
          }
          alert(3);
          //****************************************************************************************************/
          //*Ocultando el boton que permite ver las dos unidades cuando hay cambio de unidad
          //****************************************************************************************************/
          document.getElementById("btnCambiarUnidad").style.display = "none";
          alert(4);
          //****************************************************************************************************/
          //* FINAL: CODIGO QUE ESTABLECE LA ETIQUETA DE RAM E ID'S DE TABLAS                                  */
          //****************************************************************************************************/
          alert(5);
          //****************************************************************************************************/
          //*Llamando funcion para guardar en memoria la concesion salvada                                     */
          //****************************************************************************************************/
          guardarConcesionSalvada (Tramites,Unidad,Unidad1);
          alert(6);
          //****************************************************************************************************/
          //****************************************************************************************************/
          //*Limpiando pantalla e inicializando banderas para preparar el programa para agregar otra concesion */
          //****************************************************************************************************/
          fLimpiarPantalla();
          alert(7);
          //****************************************************************************************************/
          esCambioDePlaca = false;
          esCambioDeVehiculo = false;
          seRecuperoVehiculoDesdeIP = 0;
          isVehiculeBlock = false;
          checked = false;
          alert(8);
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
            function () {},
            "success",
            $appcfg_pocision_toast,
            $appcfg_icono_toast
          );
          //setTimeout(showModalConcesiones, 250);
          document.getElementById("btnSalvarConcesion").style='display:none;';
          return false;
        } // final del If de si Hay error
      })
      .catch((error) => {
        console.log('error save-preforma' + error);
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

  var concesion3ra = document.getElementById("btnconcesion");
  if (concesion3ra != null) {
    concesion3ra.addEventListener("click", function (event) {
      showConcesion();
    });
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
      var response = {error: false};
      if (currentstep == 2) {
        //**********************************************************************************************************/
        //**Salvar La Concesion Actual (Certificado de Operación o Permiso Especial)                             ***/
        //**********************************************************************************************************/
        if (seRecuperoVehiculoDesdeIP == 0 || seRecuperoVehiculoDesdeIP == 1) {
          salvarConcesion();
          // response.error = salvarConcesion();
          // console.log("btnAddConcesion response.error: " + response.error);
          // if (response.error == false) {
          //   setTimeout(showModalConcesiones, 100);
          //   document.getElementById("btnSalvarConcesion").style='display:none;';
          // }
        } else {
          fSweetAlertEventSelect(
            event,
            "SALVANDO",
            "NO SE HA RECUPERADO LA INFORMACIÓN DEL VEHICULO DESDE EL IP, FAVOR RECUPERAR LA INFORMACIÓN DEL VEHICULO ANTES DE SALVAR LA INFORMACIÓN",
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
        if (modalidadDeEntrada == 'I') {
          document.getElementById("btnSalvarConcesion").style = "display:fixed;";
        } else {
          document.getElementById("btnSalvarConcesion").style = "display:none;";
        }
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
    // Obtener todos los elementos de entrada dentro de este elemento
    var inputselect = element.querySelectorAll(".test-select");
    // Convertir NodeList a Array para facilitar la manipulación (opcional)
    inputselect = Array.from(inputselect);
    // Iterar sobre los elementos de entrada y eliminar las clases de error
    inputselect.forEach((input) => {
      //Definiendo evento change para los elementos select
      input.addEventListener("change", function (event) {
        if (event.target.getAttribute("data-valor") > event.target.value) {
          event.preventDefault();

          event.target.classList.add("errortxt");

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
          event.target.classList.remove("errortxt");

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
            event.target.classList.add("errortxt");
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
            event.target.classList.remove("errortxt");

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
      // If de si el valor es valido para preventDefault
      if (!isValid) {
        event.preventDefault();
        // Pocicionando el cursor en el input actual
        if (setFocus == true) {
          event.target.focus();
          event.target.select();
        }

        event.target.classList.add("errortxt");

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
        event.target.classList.remove("errortxt");

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
          } else {
            setFocus = true;
          }

          event.target.classList.add("errortxt");

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
          event.target.classList.remove("errortxt");

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

function callFunctionBorrarTramite (Concesion,ID,Linea,el) {
  Swal.fire({
      title: '¿ESTÁ SEGURO?',
      text: `¿QUIERE ELIMINAR ESTE TRAMITE DE LA CONCESION No. ${document.getElementById("concesion_concesion").innerHTML} ?`,
      icon: 'info',
      showCancelButton: true,
      confirmButtonText: 'SÍ, ELIMINAR',
      cancelButtonText: 'CANCELAR'
  }).then((result) => {
      //****************************************************************************************/
      //* si confirma que esta seguro de eliminar llamomos la funcion para que elimine de la base de datos.
      //****************************************************************************************/
      if (result.isConfirmed) {
        //*****************************************************************************/
        //*verificamos que tengamos mas de un elemento
        //*****************************************************************************/
        if (countFormalities() > 0) {
            //*****************************************************************************/
            //* Eliminar Registro de Tramite
            //*****************************************************************************/
            if (fEliminarTramite(Concesion, ID, Linea) == true) {
              el.checked = true;  
            } else {
              removeAttribute(el,'data-iddb');
            }
        } else {
            el.checked = true;  
            Swal.fire('¡ALERTA!', 'NO SE PUEDE DEJAR SIN NINGÚN TRÁMITE.', 'warning');
        }
      } else {
        el.checked = true;  
      }
  });
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
      descripcion: document.getElementById("descripcion_" + el.value)
        .innerHTML,
      ID_Tramite: el.value,
      Monto: el.getAttribute("data-monto"),
      ID_Categoria: document.getElementById("ID_Categoria").value,
      ID_Tipo_Servicio: document.getElementById("ID_Tipo_Servicio").value,
      ID_Modalidad: document.getElementById("ID_Modalidad").value,
      ID_Clase_Servico:
        document.getElementById("ID_Clase_Servicio").value,
    });
    return TramitesPreforma;
  }
  //*********************************************************************************************************************/
  //** Final Function para Establecer los Codigos de los Tramites                                                      **/
  //*********************************************************************************************************************/
  //*********************************************************************************************************************/
  //** Inicio Function para Salvar Tramite en Preforma                                                                 **/
  //*********************************************************************************************************************/
  function addTramitePreforma(el) {
    // URL del Punto de Acceso a la API
    const url = $appcfg_Dominio + "Api_Ram.php";
    let fd = new FormData(document.forms.form1);
    var Tramites = '';
    var Unidad = null;
    var Unidad1 = null;
    // Adjuntando el action al FormData
    fd.append("action", "add-tramite-preforma");
    // Enviar el número de Expediente
    fd.append("ID_Expediente",  document.getElementById("ID_Expediente").value);
    // Enviar el número de Solicitud
    fd.append("ID_Solicitud",  document.getElementById("ID_Solicitud").value);    
    // Funcion debe hacer echo no retornar
    fd.append("echo", true);
    // Número de RAM
    fd.append("RAM", document.getElementById('RAM').value);
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
    // Hacer al solicitud fetch con un timeout de 2 minutos
    fetchWithTimeout(url, options, 120000)
      .then((response) => response.json())
      .then(function (Datos) {
        if (typeof Datos.error != "undefined") {
          fSweetAlertEventNormal(
            Datos.errorhead,
            Datos.error + "- " + Datos.errormsg,
            "error"
          );
        } else {
          if (typeof Datos != false) {
            setAttribute(el,'data-iddb',Datos[0]['ID']);
            addConcesionNumber (Datos[0]['ID'],el.id,getAttribute(el,'data-monto'),document.getElementById('descripcion_'+el.value).innerHTML ,el.value)
            sendToast(
              "TRAMITE EN PREFORMA INSERTADO SATISFACTORIAMENTE",
              $appcfg_milisegundos_toast,
              "",
              true,
              true,
              "top",
              "right",
              true,
              $appcfg_background_toast,
              function () {},
              "error",
              $appcfg_pocision_toast,
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
      })
      .catch((error) => {
        el.checked = false;
        console.log('error addTramitePreforma' + error);
        fSweetAlertEventSelect(
          "",
          "CATCH addTramitePreforma() ",
          "ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA",
          "warning"
        );
        return true;
      });
  }
  //*********************************************************************************************************************/
  //** Final Function para Salvar Tramite en Preforma
  //*********************************************************************************************************************/

//**************************************************************************************/
//* INICIO: Agregar Tramite a Preforma Cuando ya esta salvada la concesion
//**************************************************************************************/
function addTramite (el) {
  //**************************************************************************************/
  //* Separando el valor del input
  //**************************************************************************************/
  const [tipo_tramite, clase_tramite, acronimo_tipo, acronimo_clase] =
    el.id.split("_");
  //**************************************************************************************/
  Swal.fire({
      title: '¿ESTÁ SEGURO?',
      text: `¿QUIERE AGREGAR ESTE TRAMITE A LA CONCESION No. ${document.getElementById("concesion_concesion").innerHTML} ?`,
      icon: 'info',
      showCancelButton: true,
      confirmButtonText: 'SÍ, AGREGAR',
      cancelButtonText: 'CANCELAR'
  }).then((result) => {
      //****************************************************************************************/
      //* si confirma que esta seguro de eliminar llamamos la funcion para que elimine de la base de datos.
      //****************************************************************************************/
      if (result.isConfirmed) {
        //*****************************************************************************/
        //* Eliminar Registro de Tramite
        //*****************************************************************************/
        if (addTramitePreforma(el) == true) {
          el.checked = false;  
          //**************************************************************************************/
          //* Verificadno si es cambio de unidad o cambio de placa
          //**************************************************************************************/
          if (acronimo_clase && (acronimo_clase === "CU" || acronimo_clase === "CL")) {
            // Cache the DOM element
            const element = document.getElementById(`concesion_tramite_placa_${acronimo_clase}`);
            if (element) { element.style.display = "none";}          
          }
        }
      } else {
        el.checked = false;  
        //**************************************************************************************/
        //* Verificadno si es cambio de unidad o cambio de placa
        //**************************************************************************************/
        if (acronimo_clase && (acronimo_clase === "CU" || acronimo_clase === "CL")) {
          // Cache the DOM element
          const element = document.getElementById(`concesion_tramite_placa_${acronimo_clase}`);
          if (element) { element.style.display = "none";}          
        }
      }
  });
}
//**************************************************************************************/
//* FINAL: Agregar Tramite a Preforma Cuando ya esta salvada la concesion
//**************************************************************************************/

//**************************************************************************************/
//* INICIO: Validaciones sobre CheckBox de Tramites
//**************************************************************************************/
function fReviewCheck(el) {
  var checkbox = '';
  //**************************************************************************************/
  //* INICIO: Arreglo de codigos de tramites de Cambios 
  //**************************************************************************************/
  const checkboxIds = [
    "IHTTTRA-03_CLATRA-15_M_CL",
    "IHTTTRA-03_CLATRA-17_M_CM",
    "IHTTTRA-03_CLATRA-18_M_CC",
    "IHTTTRA-03_CLATRA-19_M_CS",
  ];
  //**************************************************************************************/
  //* Final: Arreglo de codigos de tramites de Cambios 
  //**************************************************************************************/

  //**************************************************************************************/
  //* Separando el valor del input
  //**************************************************************************************/
  const [tipo_tramite, clase_tramite, acronimo_tipo, acronimo_clase] = el.id.split("_");
  //**************************************************************************************/

  //**************************************************************************************/
  //* Verificadno si es cambio de unidad o cambio de placa
  //**************************************************************************************/
  if (acronimo_clase && (acronimo_clase === "CU" || acronimo_clase === "CL")) {
    // Cache the DOM element
    const element = document.getElementById(
      `concesion_tramite_placa_${acronimo_clase}`
    );
    if (element) {
      //Validaciones si el elemento viene checked
      if (el.checked) {
        element.style.display = "flex";
        element.focus();
        // Si es cambio de unidad
        if (acronimo_clase === "CU") {
          esCambioDeVehiculo = true;
          //******************************************************************************************/
          //* Debe recuperarse porque se marco el tramite de cambio de unidad
          //******************************************************************************************/
          seRecuperoVehiculoDesdeIP = 1;
          if (
            document.getElementById("estaPagadoElCambiodePlaca").value == false
          ) {
            document.getElementById("row_tramite_M_CL").style.display = "none";
          }

          elem = document.getElementById("row_tramite_M_CL");
          if (elem != null && document.getElementById("IHTTTRA-03_CLATRA-15_M_CL").hasAttribute('disabled') == false) {
            document.getElementById("row_tramite_M_CL").style.display = "none";          
          }

          var elem = document.getElementById("row_tramite_M_CM");
          if (elem != null){
            document.getElementById("row_tramite_M_CM").style.display = "none";
          }

          elem = document.getElementById("row_tramite_M_CS");
          if (elem != null){          
            document.getElementById("row_tramite_M_CS").style.display = "none";
          }

          elem = document.getElementById("row_tramite_M_CC");
          if (elem != null){          
            document.getElementById("row_tramite_M_CC").style.display = "none";
          }

          //******************************************************************************************/
          //* Desmarcando checks de cambios en la unidad cuando se check = true en el cambio de unidad
          //******************************************************************************************/
          var checked = false;
          for (let id of checkboxIds) {
            const checkbox = document.getElementById(id);
            if (checkbox && checkbox.checked) {
              checkbox.checked = false;
            }
          }
          //************************************************************************/
          //*Salvando Tramite
          //************************************************************************/
          let iddb = getAttribute(el,'data-iddb',false);
          if (iddb == '' && modalidadDeEntrada == 'U') {
            addTramite (el);
          }
        } else {
          //******************************************************************************************/
          //* Else cuando el cambio de unidad no esta check
          //******************************************************************************************/
          esCambioDePlaca = true;
          var elem = document.getElementById("row_tramite_M_CM");
          if (elem != null){
            document.getElementById("row_tramite_M_CM").style.display = "flex";
          }

          elem = document.getElementById("row_tramite_M_CS");
          if (elem != null){          
            document.getElementById("row_tramite_M_CS").style.display = "flex";
          }

          elem = document.getElementById("row_tramite_M_CC");
          if (elem != null){          
            document.getElementById("row_tramite_M_CC").style.display = "flex";
          }

          document.getElementById("row_tramite_M_CU").style.display = "none";

          //************************************************************************/
          //*Salvando Tramite
          //************************************************************************/
          let iddb = getAttribute(el,'data-iddb',false);
          if (iddb == '' && modalidadDeEntrada == 'U') {
            addTramite (el);
          }

        }
      } else {
        //Validaciones si el elemento  no viene checked
        element.style.display = "none";
        element.value = "";
        // Si es cambio de unidad
        if (acronimo_clase == "CU") {
          esCambioDeVehiculo = false;
          document.getElementById("btnCambiarUnidad").style.display = "none";
          //******************************************************************************************/
          //* No Debe recuperarse porque se desmarco el tramite de cambio de unidad
          //******************************************************************************************/
          seRecuperoVehiculoDesdeIP = 2;
          document.getElementById("row_tramite_M_CL").style.display = "flex";
          document.getElementById("row_tramite_M_CM").style.display = "flex";
          document.getElementById("row_tramite_M_CC").style.display = "flex";
          document.getElementById("row_tramite_M_CS").style.display = "flex";
        } else {
          esCambioDePlaca = false;
          // Si cambio de placa
          var checked = false;
          for (let id of checkboxIds) {
            const checkbox = document.getElementById(id);
            if (checkbox && checkbox.checked) {
              var checked = true;
            }
          }
          if (checked == false) {
            document.getElementById("row_tramite_M_CU").style.display = "flex";
          }
        }
        //**********************************************************************************************************/
        //* INICIO: Borrando Tramite al Desmarcar Checkbox y iddb != '' (esto indica que ya habia sido salvado el tramite)
        //**********************************************************************************************************/
        let iddb = getAttribute(el,'data-iddb',false);
        if (iddb != '') {
          callFunctionBorrarTramite (document.getElementById("concesion_concesion").innerHTML,iddb,null,el);
        } else {
          console.log('Attribute data-iddb se encuentra en blanco o no se encuentra');
        }
        //**********************************************************************************************************/
        //* FINAL: Borrando Tramite al Desmarcar Checkbox y iddb != '' (esto indica que ya habia sido salvado el tramite)
        //**********************************************************************************************************/
      }
    } else {
      console.error(
        `Element con id 'concesion_tramite_placa_${acronimo_clase}' no encontrado.`
      );
    }
  } else {
    // Si son modificaciones
    if (acronimo_tipo === "M") {
      if (el.checked) {
        // Si cambio de placa

        document.getElementById("row_tramite_M_CU").style.display = "none";
        document.getElementById("row_tramite_M_CL").style.display = "flex";

        var elem = document.getElementById("row_tramite_M_CM");
        if (elem) {
          document.getElementById("row_tramite_M_CM").style.display = "flex";
        }

        elem = document.getElementById("row_tramite_M_CC");
        if (elem) {        
          document.getElementById("row_tramite_M_CC").style.display = "flex";
        }

        elem = document.getElementById("row_tramite_M_CS");
        if (elem) {        
          document.getElementById("row_tramite_M_CS").style.display = "flex";
        }
        //************************************************************************/
        //*Salvando Tramite
        //************************************************************************/
        let iddb = getAttribute(el,'data-iddb',false);
        if (iddb == '' && modalidadDeEntrada == 'U') {
          addTramite (el);
        }
      } else {
        var checked = false;
        for (let id of checkboxIds) {
          const checkbox = document.getElementById(id);
          if (
            checkbox &&
            checkbox.checked 
          ) {
            var checked = true;
          }
        }
        if (checked == false) {
          document.getElementById("row_tramite_M_CU").style.display = "flex";
        }
        //**********************************************************************************************************/
        //* INICIO: Borrando Tramite al Desmarcar Checkbox y iddb != '' (esto indica que ya habia sido salvado el tramite)
        //**********************************************************************************************************/
        let iddb = getAttribute(el,'data-iddb',false);
        if (iddb != '') {
          callFunctionBorrarTramite (document.getElementById("concesion_concesion").innerHTML,iddb,null,el);
        } else {
          console.log('Attribute data-iddb se encuentra en blanco o no se encuentra');
        }
        //**********************************************************************************************************/
        //* FINAL: Borrando Tramite al Desmarcar Checkbox y iddb != '' (esto indica que ya habia sido salvado el tramite)
        //**********************************************************************************************************/
      }
    } else {
      // Si son renovaciones
      if (acronimo_tipo === "R") {
        if (el.checked) {
          if (acronimo_clase === "CO") {
            document.getElementById("row_tramite_X_CO").style.display = "none";
          } else {
            if (acronimo_clase === "PE") {
              document.getElementById("row_tramite_X_PE").style.display =
                "none";
            } else {
              if (acronimo_clase === "PS") {
                document.getElementById("row_tramite_X_PS").style.display =
                  "none";
              }
            }
          }
          //************************************************************************/
          //*Salvando Tramite
          //************************************************************************/
          let iddb = getAttribute(el,'data-iddb',false);
          if (iddb == '' && modalidadDeEntrada == 'U') {
            addTramite (el);
          }
        } else {
          if (acronimo_clase === "CO") {
            document.getElementById("row_tramite_X_CO").style.display = "flex";
          } else {
            if (acronimo_clase === "PE") {
              document.getElementById("row_tramite_X_PE").style.display =
                "flex";
            } else {
              if (acronimo_clase === "PS") {
                document.getElementById("row_tramite_X_PS").style.display =
                  "flex";
              }
            }
          }
          //**********************************************************************************************************/
          //* INICIO: Borrando Tramite al Desmarcar Checkbox y iddb != '' (esto indica que ya habia sido salvado el tramite)
          //**********************************************************************************************************/
          let iddb = getAttribute(el,'data-iddb',false);
          if (iddb != '') {
            callFunctionBorrarTramite (document.getElementById("concesion_concesion").innerHTML,iddb,null,el);
          } else {
            console.log('Attribute data-iddb se encuentra en blanco o no se encuentra');
          }
          //**********************************************************************************************************/
          //* FINAL: Borrando Tramite al Desmarcar Checkbox y iddb != '' (esto indica que ya habia sido salvado el tramite)
          //**********************************************************************************************************/
        }
      } else {
        // Si son reimpresiones
        if (acronimo_tipo === "X") {
          if (el.checked) {
            if (acronimo_clase === "CO") {
              checkbox = document.getElementById("IHTTTRA-03_CLATRA-20_R_CO");
              if (checkbox && checkbox.getAttribute("disabled") != "") {
                document.getElementById("row_tramite_R_CO").style.display =
                  "none";
              }
            } else {
              if (acronimo_clase === "PE") {
                checkbox = document.getElementById("IHTTTRA-03_CLATRA-20_R_PE");
                if (checkbox && checkbox.getAttribute("disabled") != "") {
                  document.getElementById("row_tramite_R_PE").style.display =
                    "none";
                }
              } else {
                if (acronimo_clase === "PS") {
                  checkbox = document.getElementById(
                    "IHTTTRA-03_CLATRA-20_R_PS"
                  );
                  if (checkbox && checkbox.getAttribute("disabled") != "") {
                    document.getElementById("row_tramite_R_PS").style.display =
                      "none";
                  }
                }
              }
            }
            //************************************************************************/
            //*Salvando Tramite
            //************************************************************************/
            let iddb = getAttribute(el,'data-iddb',false);
            if (iddb == '' && modalidadDeEntrada == 'U') {
              addTramite (el);
            }
          } else {
            if (acronimo_clase === "CO") {
              document.getElementById("row_tramite_R_CO").style.display =
                "flex";
            } else {
              if (acronimo_clase === "PE") {
                document.getElementById("row_tramite_R_PE").style.display =
                  "flex";
              } else {
                if (acronimo_clase === "PS") {
                  document.getElementById("row_tramite_R_PS").style.display =
                    "flex";
                }
              }
            }
          }
          //**********************************************************************************************************/
          //* INICIO: Borrando Tramite al Desmarcar Checkbox y iddb != '' (esto indica que ya habia sido salvado el tramite)
          //**********************************************************************************************************/
          let iddb = getAttribute(el,'data-iddb',false);
          if (iddb != '') {
            callFunctionBorrarTramite (document.getElementById("concesion_concesion").innerHTML,iddb,null,el);
          } else {
            console.log('Attribute data-iddb se encuentra en blanco o no se encuentra');
          }
          //**********************************************************************************************************/
          //* FINAL: Borrando Tramite al Desmarcar Checkbox y iddb != '' (esto indica que ya habia sido salvado el tramite)
          //**********************************************************************************************************/
        }
      }
    }
  }
}
//**************************************************************************************/
//* FINAL: Validaciones sobre Check Box de Tramites
//**************************************************************************************/

function getVehiculoDesdeIPMoveDatos(vehiculo,Tipo_Tramite) {
  var sufijo = '1'
  if (Tipo_Tramite == 'CL') {
    sufijo = ''
  }
  document.getElementById(
    "concesion"+sufijo+"_nombre_propietario"
  ).innerHTML = vehiculo.cargaUtil.propietario.nombre;
  document.getElementById(
    "concesion"+sufijo+"_identidad_propietario"
  ).innerHTML = vehiculo.cargaUtil.propietario.identificacion;
  if (
    vehiculo &&
    vehiculo.cargaUtil &&
    vehiculo.cargaUtil.placaAnterior
  ) {
    document.getElementById("concesion"+sufijo+"_placaanterior").style =
      "display:inline;";
    document.getElementById(
      "concesion"+sufijo+"_placaanterior"
    ).innerHTML = vehiculo.cargaUtil.placaAnterior;
  } else {
    document.getElementById("concesion"+sufijo+"_placaanterior").style =
      "display:none;";
    document.getElementById(
      "concesion"+sufijo+"_placaanterior"
    ).innerHTML = "";
  }
  //document.getElementById("tipo_vehiculo").value = datos[1][0]['Unidad'][0]['tipo'];
  //document.getElementById("modelo_vehiculo").value = datos[1][0]['Unidad'][0]['modelo'];
  document.getElementById("combustible"+sufijo).value =  vehiculo.cargaUtil.combustible;
  document.getElementById("concesion"+sufijo+"_vin").value = vehiculo.cargaUtil.vin;
  document.getElementById("concesion"+sufijo+"_placa").value =
    vehiculo.cargaUtil.placa;
  document.getElementById("concesion"+sufijo+"_serie").value =
    vehiculo.cargaUtil.chasis;
  document.getElementById("concesion"+sufijo+"_motor").value =
    vehiculo.cargaUtil.motor;
  document.getElementById("marcas"+sufijo+"").value =
    vehiculo.cargaUtil.marcacodigo;
  document.getElementById("colores"+sufijo+"").value =
    vehiculo.cargaUtil.colorcodigo;
  document.getElementById("anios"+sufijo+"").value =
    vehiculo.cargaUtil.axo;
  document.getElementById("concesion"+sufijo+"_tipo_vehiculo").value =
    vehiculo.cargaUtil.tipo;
  document.getElementById("concesion"+sufijo+"_modelo_vehiculo").value =
    vehiculo.cargaUtil.modelo;
  seRecuperoVehiculoDesdeIP = 1;
  if (Tipo_Tramite == 'CU') {
    document.getElementById("btnCambiarUnidad").style.display = "flex";
    document.getElementById("btnCambiarUnidad").innerHTML = "<strong>ENTRA</strong>";
    document.getElementById("idVistaSTPC2").style = "display:fixed;";                
    document.getElementById("idVistaSTPC1").style = "display:none;";                
  }
}

function getVehiculoDesdeIP(Obj,Tipo_Tramite) {
  if (
    document.getElementById("concesion_tramite_placa_CL").value !=
    document.getElementById("concesion_tramite_placa_CU").value
  ) {
    //*********************************************************************************************************************/
    //* Si es Certificado Entra Aqui para obtener el CO y PE
    //*********************************************************************************************************************/
    if (esCertificado) {
      var Concesion = document.getElementById("concesion_concesion").innerHTML;
    } else {
      var Concesion = document.getElementById("concesion_concesion").innerHTML;
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
    fd.append("Concesion", Concesion);
    // Fetch options
    const options = {
      method: "POST",
      body: fd,
    };
    // Hacer al solicitud fetch con un timeout de 2 minutos
    fetchWithTimeout(url, options, 120000)
      .then((response) => response.json())
      .then(function (vehiculo) {
        if (!vehiculo.error) {
          if (vehiculo.codigo && vehiculo.codigo == 200) {
            if (vehiculo.cargaUtil.estadoVehiculo == "NO BLOQUEADO") {
              if (
                typeof vehiculo.cargaUtil.Multas[0] == "undefined" &&
                typeof vehiculo.cargaUtil.Preformas[0] == "undefined"
              ) {
                isVehiculeBlock = false;
                getVehiculoDesdeIPMoveDatos(vehiculo,Tipo_Tramite);                
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
                  function () {},
                  "success",
                  $appcfg_pocision_toast,
                  $appcfg_icono_toast
                );
              } else {
                var html = "";
                if (typeof vehiculo.cargaUtil.Multas[0] != "undefined") {
                  html = mallaDinamica(
                    { titulo: "LISTADO DE MULTAS", name: "MULTAS" },
                    vehiculo.cargaUtil.Multas
                  );
                }
                if (typeof vehiculo.cargaUtil.Preformas[0] != "undefined") {
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
                fSweetAlertEventNormal(
                  "VALIDACIONES",
                  "LA UNIDAD TIENE MULTA(S) PENDIENTE(S) DE PAGO, FAVOR PAGAR LAS MULTAS PREVIO A INGRESAR EL TRAMITE",
                  "error",
                  html
                );
              }
            } else {
              if (vehiculo.cargaUtil.estadoVehiculo == "NO BLOQUEADO") {
                fSweetAlertEventNormal(
                  "BLOQUEADO",
                  "EL VEHICULO ESTA BLOQUEADO EN EL INSTITUTO DE LA PROPIEDAD",
                  "error"
                );
                isVehiculeBlock = true;
              } else {
                if (isset(vehiculo.codigo) == 407 || vehiculo.codigo == 408) {
                  fSweetAlertEventNormal(
                    "ADVERTENCIA",
                    "NO HEMOS PODIDO CONECTARNOS CON EL INSTITUTO DE LA PROPIEDAD, FAVOR INTENTENLO EN UN MOMENTO SI EL PROBLEMA PERSIOSTE CONTACTE AL ADMINISTRADOR DEL SISTEMA",
                    "warning"
                  );
                  isVehiculeBlock = true;
                } else {
                  fSweetAlertEventNormal(
                    "ERROR",
                    "ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA",
                    "error"
                  );
                  isVehiculeBlock = true;
                }
              }
            }
          } else {
            fSweetAlertEventNormal(
              "INFORMACIÓN",
              "EL VEHICULO NO HA SIDO ENCONTRADO EN LA BASE DE DATOS DEL IP",
              "warning"
            );
            isVehiculeBlock = true;
          }
        } else {
          fSweetAlertEventNormal(
            vehiculo.errorhead,
            vehiculo.error + "- " + vehiculo.errormsg,
            "error"
          );
        }
      })
      .catch((error) => {
        fSweetAlertEventNormal(
          "ERROR CATCH",
          "ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA",
          "error"
        );
        console.log('getVehiculoDesdeIP catch error' + error);
        isVehiculeBlock = true;
      });
  } else {
    fSweetAlertEventNormal(
      "ERROR EN PLACAS",
      "LA PLACA DE LA UNIDAD NUEVA NO PUEDE SER IGUAL A LA PLACA DEL TRAMITE DE CAMBIO DE PLACA",
      "warning"
    );
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
function CargaSolicitudAjax(){
  $contador++
  $('#numeracion').html('0%');
  $('.determinate').css('width','0%')
  let fileInput = document.getElementById('EscaneoSolicitud'); // Select the input element
  let firstFile = fileInput.files[0]; // Get the first file
  var form_data = new FormData();
  form_data.append("action","save-escaneo");
  form_data.append("RAM",document.getElementById('RAM').value);
  form_data.append("Archivo",firstFile);
  sweetAlert.fire({ title: "Subiendo archivo(s)",html: '<div id="barra" > <div class="progress"> <div class="determinate" style="width: 0%"></div> </div> <div id="numeracion" style=" text-align: center; ">0%</div> </div>',
    html: true,
    showConfirmButton: true
  });
  $("#barra").show(400);
    $.ajax({
    timeout: 0,
    url: $appcfg_Dominio + 'Api_Ram.php',
    dataType: 'json',
    cache: false,
    contentType: false,
    processData: false,
    data: form_data,
    type: 'post',
    success: function(data){
        var datar = data;
        if(typeof datar.msg != 'undefined'){
            swal.fire({title: "Archivo(s) Subidos",
                  text: "archivo subido con exito ...",
                  confirmButtonText: "Presione Ok Para Continuar"},
                function(){
                // ArchivosSolicitud(); 
                // validarCargaDocs();
            });
        } else {
          sweetAlert.fire("Oops...", "Algo Salio Mal!", "error: "+datar.Error);
        }
    },
    xhr: function() {
    // creamos un objeto XMLHttpRequest
    var xhr = new XMLHttpRequest();
    // gestionamos el evento 'progress'
    xhr.upload.addEventListener('progress', function(evt) {
      if (evt.lengthComputable) {
        // calculamos el porcentaje completado de la carga de archivos
        var percentComplete = evt.loaded / evt.total;
        percentComplete = parseInt(percentComplete * 100);
        // actualizamos la barra de progreso con el nuevo porcentaje
        $('#swal2-html-container').html(percentComplete+'%');
        $('.determinate').css('width',''+percentComplete+'%');
        // una vez que la carga llegue al 100%, ponemos la progress bar como Finalizado
        if (percentComplete === 100) {
          $('.progress-bar').html('Finalizado');
        }
      }
    }, false);
    return xhr;
  }
  })
}


//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//@@@ FUNCION PARA CARGAR ARCHIVOS ADJUNTOS    @@@
//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
var $contador = 0;

function CargaSolicitud() {
  $contador++;
  let fileInput = document.getElementById('EscaneoSolicitud');
  let firstFile = fileInput.files[0]; // Get the first file

  if (!firstFile) {
    sweetAlert.fire("Error", "No file selected for upload", "error");
    return;
  }

  let formData = new FormData();
  formData.append("action", "save-escaneo");
  formData.append("RAM", document.getElementById('RAM').value);
  formData.append("Archivo", firstFile);

  sweetAlert.fire({
    title: "Subiendo archivo(s)",
    html: '<div id="barra"><div class="progress"><div class="determinate" style="width: 0%"></div></div><div id="numeracion" style="text-align: center;">0%</div></div>',
    showConfirmButton: false,
  });

  document.getElementById('numeracion').innerHTML = '0%';
  document.querySelector('.determinate').style.width = '0%';

  // Create an XMLHttpRequest object
  let xhr = new XMLHttpRequest();

  // Configure it
  xhr.open('POST', $appcfg_Dominio + 'Api_Ram.php', true);

  // Track progress
  xhr.upload.addEventListener('progress', function (evt) {
    if (evt.lengthComputable) {
      let percentComplete = Math.round((evt.loaded / evt.total) * 100);
      document.getElementById('numeracion').innerHTML = `${percentComplete}%`;
      document.querySelector('.determinate').style.width = `${percentComplete}%`;
      if (percentComplete === 100) {
        document.querySelector('.progress').innerHTML = 'Finalizado';
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
      if (typeof data.msg != 'undefined') {
        sweetAlert.fire({
          title: "CARGA DE ARCHIVOS",
          text: "Archivo subido exitosamente",
          confirmButtonText: "Cerrar",
        });
      } else {
        sweetAlert.fire("Oops...", `Algo salió mal! Error: ${data.errormsg}`, "error");
      }
    } else {
      sweetAlert.fire("Error", `HTTP Error: ${xhr.status}`, "error");
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

setTimeout(setFocusElement, 250);