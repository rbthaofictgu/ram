"use strict";

var isFromfGetInputs = false;
var isError = false;
var isRecordGetted = Array();
var isTab=false; 
var isPrevious = false;
var hayerror = false;
var idinput = '';
var idinputs = Array();
var currentstep=0;
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
var isVehiculeBlock = false;
var checked = false;
var concesionIndex = Array();
var concesionNumber = Array();
var currentConcesionIndex=-1;
var dataConcesion = Array();

function updateCollection(arreglo, elemento) {
  if (arreglo.indexOf(elemento) === -1) {
    arreglo.push(elemento);
  }
  return arreglo.indexOf(elemento);
}

var multas3ra = document.getElementById('btnmultas');
if (multas3ra != null) {
    multas3ra.addEventListener('click', function(event) {
    showConcesion();
  });
}

var consultas3ra = document.getElementById('btnconsultas');
if (consultas3ra != null) {
    consultas3ra.addEventListener('click', function(event) {
    showConcesion();
  });
}  

var perexp3ra = document.getElementById('btnperexp');
if (perexp3ra != null) {
    perexp3ra.addEventListener('click', function(event) {
    showConcesion();
  });
}  


  //************************************************************/
  // Moviendose al siguiente input
  //************************************************************/
  function moveToNextInput(currentInput,value) {
    //var inputs = [].slice.call(document.querySelectorAll('input select'));
    var currentIndex = testinputsArray.indexOf(currentInput);
    var wasMoved=false;
    for (var i = ((currentIndex + 1)+ (value)); i < testinputsArray.length; i++) {
      if (!testinputsArray[i].disabled && !testinputsArray[i].visible && testinputsArray[i].getAttribute('readonly') == null) {      
        testinputsArray[i].focus();
        if (testinputsArray[i].type != 'select-one') {
          testinputsArray[i].select();
        }
        wasMoved=true;
        break;
      }
    }
  }
  
  //**********************************************************************/
  // Obtener todas las entradas enabled para validarlas
  //**********************************************************************/
  function fGetInputs() {
    setFocus = false;
    isTab=true;
    isFromfGetInputs = true;
    // Get the element by its ID
    var element = document.getElementById('test-form-'+(currentstep+1));
    // Get all input elements inside this element
    var inputs = element.querySelectorAll('.test-controls');
    // Convert NodeList to Array for easier manipulation (optional)
    inputs = Array.from(inputs);
    // Iterando entre los inputs para despachar el evento change y validar la data de cada input
    var index = 0;
    inputs.forEach(input => {
        if (input.disabled == false) {
          // Creando un nuevo evento 'change'
          var event = new Event('change', {
              'bubbles': true,
              'cancelable': true
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
    var element = document.getElementById('test-form-'+(currentstep+1));
    // Get all input elements inside this element
    var inputs = element.querySelectorAll('.test-select');
    // Convert NodeList to Array for easier manipulation (optional)
    inputs = Array.from(inputs);
    // Iterando entre los inputs para despachar el evento change y validar la data de cada input
    var index = 0;
    inputs.forEach(input => {

      if (input.disabled == false) {

        if (input.getAttribute('data-valor') > input.value) {        
          
          input.classList.add("errortxt");

          var label = document.getElementById(input.id + 'label');
          if (label != null) {
            label.classList.add("errorlabel");
          }
          
          paneerror[currentstep][idinputs.indexOf(input.id)] = 1;

        } else {

          input.classList.remove("errortxt");

          var label = document.getElementById(input.id + 'label');
          if (label != null) {
            label.classList.remove("errorlabel");
          }

          paneerror[currentstep][idinputs.indexOf(input.id)] = 0;
          //Moverse al siguiente input
          moveToNextInput(input,0);
        }
      }
      index++;
    });
    setFocus = true;
    isTab=false;
  }
  
  //**************************************************************************************/
  //Eliminar todos los mensajes de error
  //**************************************************************************************/
  function fCleanErrorMsg() {
    //console.log('On fCleanErrorMsg()');
    // Obtener el elemento por su ID
    var element = document.getElementById('test-form-'+(currentstep+1));
    // Obtener todos los elementos de entrada dentro de este elemento
    var inputs = element.querySelectorAll('.test-controls');
    // Convertir NodeList a Array para facilitar la manipulación (opcional)
    inputs = Array.from(inputs);
    // Iterar sobre los elementos de entrada y eliminar las clases de error
    inputs.forEach(input => {
      input.classList.remove("errortxt");
      var label = document.getElementById(input.id + 'label');
      if (label != null) {
        label.classList.remove("errorlabel");
      }
      var labelerror = document.getElementById(input.id + 'labelerror');
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
    var element = document.getElementById('test-form-'+(currentstep+1));
    // Obtener todos los elementos de entrada dentro de este elemento
    var inputs = element.querySelectorAll('.test-select');
    // Convertir NodeList a Array para facilitar la manipulación (opcional)
    inputs = Array.from(inputs);
    // Iterar sobre los elementos de entrada y eliminar las clases de error
    inputs.forEach(inputx => {
      inputx.classList.remove("errortxt");
      let id = inputx.id.concat('label');
      var label = document.getElementById(id);
      if (label != null) {
        label.classList.remove("errorlabel");
      }    
    });
  }

document.addEventListener('DOMContentLoaded', function () {

  // Pocicionando el cursos en el primer input de la pantalla id=colapoderado
  function setFocusElement(){
    document.getElementById("colapoderado").focus(); 
  }
   
  //**************************************************************************************/
  //Cargando la información por default que debe usar el formulario
  //**************************************************************************************/
  function f_DataOmision() {
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
    fd.append("action", 'get-datosporomision');
    // Fetch options
    const options = {
      method: 'POST',
      body: fd,
    };
    // Hace la solicitud fetch con un timeout de 2 minutos
    fetchWithTimeout(url, options, 120000)
      .then(response => response.json())
      .then(function (datos) {
        if (typeof datos[0] != 'undefined') {
          if (datos[1].length > 0) {
            fLlenarSelect('entregadocs',datos[1],null,false,{text: 'SELECCIONE UN LUGAR DE ENTREGA', value: '-1'})            
          }
          if (typeof datos[2] != 'undefined') {
            if (datos[2].length > 0) {
              fLlenarSelect('Departamentos',datos[2],-1,false,{text: 'SELECCIONE UN DEPARTAMENTO', value: '-1'})     
              fLlenarSelect('Municipios',[],-1,false,{text: 'SELECCIONE UN MUNICIPIO', value: '-1'})            
              fLlenarSelect('Aldeas',[],-1,false,{text: 'SELECCIONE UNA ALDEA', value: '-1'})            
            }          
          }
        } else {
          if (typeof datos.error != 'undefined') {
            fSweetAlertEventNormal(datos.errorhead, datos.error + '- ' + datos.errormsg , 'error');
          } else {
            fSweetAlertEventNormal('INFORMACIÓN', 'ALGO RARO PASO, INTENTELO DE NUEVO SI EL ERROR PERSISTE CONTACTE AL ADMINISTRADOR DEL SISTEMA', 'error');
          }
        }
      })
        .catch((error) => {
        console.log('error'+error);
        fSweetAlertEventNormal('OPPS', 'ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA', 'error');
    });
  }        

  //**************************************************************************************/
  // Inicio llamado a la función f_DataOmision que carga los datos por defecto
  //**************************************************************************************/
  f_DataOmision();
    //**************************************************************************************/
    // Final llamado a la función f_DataOmision que carga los datos por defecto
    //**************************************************************************************/
    var stepperFormEl = document.querySelector('#stepperForm')
    stepperForm = new Stepper(stepperFormEl, {
      linear: true,
      animation: true
    })
    var btnNextList = [].slice.call(document.querySelectorAll('.btn-next-form'))
    var btnNextListprevious = [].slice.call(document.querySelectorAll('.btn-previous-form'))
    var stepperPanList = [].slice.call(stepperFormEl.querySelectorAll('.bs-stepper-pane'))
    var inputMailForm = document.getElementById('inputMailForm')
    var inputPasswordForm = document.getElementById('inputPasswordForm')
    var form = stepperFormEl.querySelector('.bs-stepper-content form')
    //*****************************************************************************/
    //Creando arreglo de inputs para saber posteriormente el indice de cada input
    //*****************************************************************************/
    var testcontrols = [].slice.call(document.querySelectorAll('.test-controls'));
    var testcontrolsArray  =  Array.from(testcontrols);  
    testinputs = document.querySelectorAll('input, select');
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
      isRecordGetted[i]='';
    }

  btnNextList.forEach(function (btn) {
    btn.addEventListener('click', function () {
        fGetInputs();
        stepperForm.next();
    })
  })

  function fLimpiarPantalla() {
    //*********************************************************************************/
    // Dependiendo del panel actual se ejecuta una función para validar los campos
    //*********************************************************************************/
    switch (currentstep)
    {
      case 0: 
        document.getElementById('nomapoderado').value = '';
        document.getElementById('identidadapod').value = '';
        document.getElementById('dirapoderado').value = '';
        document.getElementById('telapoderado').value = '';                
        document.getElementById('emailapoderado').value = ''; 
        break;
      case 1: 
        document.getElementById('nomsoli').value = '';
        document.getElementById('denominacionsoli').value = '';
        document.getElementById('domiciliosoli').value = '';
        document.getElementById('telapoderado').value = '';
        document.getElementById('emailsoli').value = '';
        // document.getElementById('numescritura').value = '';
        // document.getElementById('fecha').value = '';
        // document.getElementById('lugarcons').value = '';
        // document.getElementById('rtnnotario').value = '';
        // document.getElementById('nombrenotario').value = '';
        document.getElementById('tiposolicitante').value = '';
        document.getElementById('Departamentos').value = '-1'; 
        document.getElementById('Municipios').value = '-1';
        document.getElementById('Municipios').innerHTML = "";
        document.getElementById('Aldeas').value = '-1';
        document.getElementById('Aldeas').innerHTML = "";
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
        document.getElementById('nomapoderado').value = '';
        document.getElementById('identidadapod').value = '';
        document.getElementById('dirapoderado').value = '';
        document.getElementById('telapoderado').value = '';                
        document.getElementById('emailapoderado').value = '';               
        break;
      case 3: 
        document.getElementById('nomapoderado').value = '';
        document.getElementById('identidadapod').value = '';
        document.getElementById('dirapoderado').value = '';
        document.getElementById('telapoderado').value = '';                
        document.getElementById('emailapoderado').value = '';               
        break;
      case 4: 
        document.getElementById('nomapoderado').value = '';
        document.getElementById('identidadapod').value = '';
        document.getElementById('dirapoderado').value = '';
        document.getElementById('telapoderado').value = '';                
        document.getElementById('emailapoderado').value = '';               
        break;
      default: 
        document.getElementById('nomapoderado').value = '';
        document.getElementById('identidadapod').value = '';
        document.getElementById('dirapoderado').value = '';
        document.getElementById('telapoderado').value = '';                
        document.getElementById('emailapoderado').value = ''; 
        break;
    }
  }        

  

  
  function ProcessFormalities () {
    var respuesta = false;
    const chkTramites = document.querySelectorAll('input[name="tramites[]"]');
    if (chkTramites) {	
      chkTramites.forEach(function (chk) {
        if (chk.checked && chk.getAttribute('disabled') != '') {
            respuesta =  true;
        }
      });
    }
    return respuesta;
  }


  btnNextListprevious.forEach(function (btn) {
    btn.addEventListener('click', function () {
        var goPrevious = true;
        if (currentstep < 3) {
          if (isRecordGetted[currentstep] != '') {
            fGetInputs();
          }
        } else {
          if (currentstep == 3) {
            showModalFromShown = false;
            fGetInputs();
            if (ProcessFormalities() == true) {
              fSweetAlertEventNormal('ERROR', 'HAY TRAMITES REGISTRADOS, DEBE SALVAR LA INFORMACIÓN DE LA PANTALLA O DESMARCAR LOS TRAMITES', 'error');
              goPrevious = false;
            }
          }
        }
        // Si no tiene ningun error previo en la validación se entra al siguiente if y se mueve al siguiente panel
        if (goPrevious==true) {
          stepperForm.previous();
        }
    })
  })


  
  function f_FetchCallApoderado(idApoderado,event,idinput) {
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
    fd.append("action", 'get-apoderado');
    //Adjuntando el idApoderado al FormData
    fd.append("idApoderado", idApoderado);
    // Fetch options
    const options = {
      method: 'POST',
      body: fd,
    };
    isRecordGetted[currentstep] = idApoderado;
    // Hace la solicitud fetch con un timeout de 2 minutos
    fetchWithTimeout(url, options, 120000)
      .then(response => response.json())
      .then(function (datos) {
        if (typeof datos.nombre_apoderado != 'undefined') {
          if (datos.nombre_apoderado != '' && datos.nombre_apoderado != null) {
            //Limpiando mensajes de error
            fCleanErrorMsg();
            document.getElementById('nomapoderado').value = datos.nombre_apoderado;
            document.getElementById('identidadapod').value = datos.ident_apoderado;
            document.getElementById('dirapoderado').value = datos.dir_apoderado;
            document.getElementById('telapoderado').value = datos.tel_apoderado;                
            document.getElementById('emailapoderado').value = datos.correo_apoderado; 
            isError = false;
            //Moviendose al siguiente input
            moveToNextInput(event.target,0);
            sendToast("INFORMACIÓN DEL APODERADO RECUPERADA EXITOSAMENTE",$appcfg_milisegundos_toast,'',true,true,"top","right",true,$appcfg_background_toast,function(){},'success',$appcfg_pocision_toast,$appcfg_icono_toast);
          } else {
              fLimpiarPantalla();
              fSweetAlertEventSelect(event,'INFORMACIÓN', 'EL NÚMERO DE COLEGIACIÓN NO EXISTE EN NUESTRA BASE DE DATOS, FAVOR VERIFIQUE EL NÚMERO E INTENTELO NUEVAMENTE', 'warning');
              event.preventDefault();
              event.target.classList.add("errortxt");
              var label = document.getElementById(event.target.id + 'label');
              if (label != null) {
                label.classList.add("errorlabel");
              }
              paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
              isError = true;
          }
        } else {
          if (typeof datos.error != 'undefined') {
            fLimpiarPantalla();
            fSweetAlertEventSelect(event,datos.errorhead, datos.error + '- ' + datos.errormsg , 'error');
            event.preventDefault();
            event.target.classList.add("errortxt");
            var label = document.getElementById(event.target.id + 'label');
            if (label != null) {
              label.classList.add("errorlabel");
            }
            paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
            isError = true;
          } else {
            fLimpiarPantalla();
            fSweetAlertEventSelect(event,'INFORMACIÓN', 'ERROR DESCONOCIDO, INTENTELO DE NUEVO SI EL ERROR PERSISTE CONTACTE AL ADMINISTRADOR DEL SISTEMA', 'error');
            event.preventDefault();
            //event.target.select();
            event.target.classList.add("errortxt");
            var label = document.getElementById(event.target.id + 'label');
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
        fSweetAlertEventSelect(event,'CONEXÍON', 'ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA', 'warning');
        event.preventDefault();
        event.target.classList.add("errortxt");
        var label = document.getElementById(event.target.id + 'label');
        if (label != null) {
          label.classList.add("errorlabel");
        }
        paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
        isError = true;
      });
  }        

  function f_FetchCallSolicitante(idSolicitante,event,idinput) {
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
    fd.append("action", 'get-solicitante');
    //Adjuntando el idApoderado al FormData
    fd.append("idSolicitante", idSolicitante);
    // Fetch options
    const options = {
      method: 'POST',
      body: fd,
    };
    // Hacer al solicitud fetch con un timeout de 2 minutos
    fetchWithTimeout(url, options, 120000)
      .then(response => response.json())
      .then(function (datos) {
        if (typeof datos[0] != 'undefined') {
          if (datos[0] > 0) {
            //Limpiando mensajes de error
            fCleanErrorMsg();
            document.getElementById('nomsoli').value = datos[1].nombre_solicitante;
            document.getElementById('denominacionsoli').value = datos[1].nombre_empresa;
            document.getElementById('domiciliosoli').value = datos[1].dir_solicitante;
            document.getElementById('emailsoli').value = datos[1].correo_solicitante; 
            document.getElementById('telsoli').value = datos[1].tel_solicitante; 
            document.getElementById('tiposolicitante').value = datos[1].DESC_Solicitante;  
            if (datos[1].Departamento != null && datos[1].Departamento != '') {
              document.getElementById('Departamentos').value= datos[1].Departamento;
            }else{
              document.getElementById('Departamentos').value= '-1';
            }
            fLlenarSelect('Municipios',datos[3],datos[1].Municipio,false,{text: 'SELECCIONE UN MUNICIPIO', value: '-1'})            
            fLlenarSelect('Aldeas',datos[4],datos[1].aldea,false,{text: 'SELECCIONE UNA ALDEA', value: '-1'})            
            isError = false;
            moveToNextInput(event.target,0);
            sendToast("INFORMACIÓN DEL SOLICITANTE RECUPERADA EXITOSAMENTE",$appcfg_milisegundos_toast,'',true,true,"top","right",true,$appcfg_background_toast,function(){},'success',$appcfg_pocision_toast,$appcfg_icono_toast);            
          } else {
              fLimpiarPantalla();
              fSweetAlertEventSelect(event,'INFORMACIÓN', 'EL RTN DEL SOLICITANTE NO EXISTE EN NUESTRA BASE DE DATOS, FAVOR VERIFIQUE EL NÚMERO E INTENTELO NUEVAMENTE', 'warning');
              event.preventDefault();
              event.target.classList.add("errortxt");
              var label = document.getElementById(event.target.id + 'label');
              if (label != null) {
                label.classList.add("errorlabel");
              }
              paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
              isError = true;
          }
        } else {
          if (typeof datos.error != 'undefined') {
            fLimpiarPantalla();
            fSweetAlertEventSelect(event, datos.errorhead , datos.error + '- ' + datos.errormsg , 'error');
            event.preventDefault();
            event.target.classList.add("errortxt");
            var label = document.getElementById(event.target.id + 'label');
            if (label != null) {
              label.classList.add("errorlabel");
            }
            paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
            isError = true;
          } else {
            fLimpiarPantalla();
            fSweetAlertEventSelect(event,'INFORMACIÓN', 'ERROR DESCONOCIDO, INTENTELO DE NUEVO EN UN MOMENTO, SI EL ERROR PERSISTE CONTACTE AL ADMINISTRADOR DEL SISTEMA', 'error');
            event.preventDefault();
            //event.target.select();
            event.target.classList.add("errortxt");
            var label = document.getElementById(event.target.id + 'label');
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
        fSweetAlertEventSelect(event,'CONEXÍON', 'ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA', 'warning');
        event.preventDefault();
        event.target.classList.add("errortxt");
        var label = document.getElementById(event.target.id + 'label');
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
      document.getElementById("concesionlabel").innerHTML = datos[1][0]['Tipo_Concesion'];
    }
    
    claseDeServicio = datos[1][0]['ID_Clase_Servico'];
    if (claseDeServicio == 'STPP' || claseDeServicio == 'STPC') {
      esCertificado = true;
      document.getElementById("concesion_perexp").innerHTML = datos[1][0]['N_Permiso_Explotacion'];
      document.getElementById("Permiso_Explotacion").value = datos[1][0]['N_Permiso_Explotacion'];
    } else {
        esCertificado = false;
        document.getElementById("Permiso_Explotacion").value = '';
    }
    document.getElementById("concesion_tramites").innerHTML = datos[1][0]['Tramites'];
    document.getElementById("concesion_concesion").innerHTML = datos[1][0]['N_Certificado'];
    document.getElementById("concesion_fecven").innerHTML = datos[1][0]['Fecha Vencimiento Certificado'];
    document.getElementById("concesion_nombreconcesionario").innerHTML = datos[1][0]['NombreSolicitante'];
    document.getElementById("concesion_rtn").innerHTML = datos[1][0]['RTN_Concesionario'];
    document.getElementById("concesion_fecexp").innerHTML = datos[1][0]['Fecha Emision Certificado'];
    document.getElementById("concesion_resolucion").innerHTML = datos[1][0]['Resolucion'];
    if (datos.length > 1 && datos[1].length > 0 && datos[1][0]['Unidad'] && datos[1][0]['Unidad'].length > 0) {
      document.getElementById("concesion_nombre_propietario").innerHTML = datos[1][0]['Unidad'][0]['Nombre_Revicion'];
      document.getElementById("concesion_identidad_propietario").innerHTML = datos[1][0]['Unidad'][0]['RTN_Propietario'];
      if (datos[1] && datos[1][0] && datos[1][0]['Unidad'] && datos[1][0]['Unidad'][0] && datos[1][0]['Unidad'][0]['ID_Placa_Anterior']) {
        document.getElementById("concesion_placaanterior").style = 'display:inline;';
        document.getElementById("concesion_placaanterior").innerHTML = datos[1][0]['Unidad'][0]['ID_Placa_Anterior'];
      } else {
        document.getElementById("concesion_placaanterior").style = 'display:none;';
        document.getElementById("concesion_placaanterior").innerHTML = '';
      }
      //***********************************************************************************************************************************/
      // INICIO:
      // Marcando tramites obligatrios dependiendo de las condciones de vencimiento de las concesiones y permisos de explotacion
      // y si ya se pago el cambio de placa de la unidad actual, si tuvo cambio de placa
      //***********************************************************************************************************************************/
      var el = document.getElementById("IHTTTRA-02_CLATRA-02_R_CO");
      if (el != null) {
        document.getElementById("IHTTTRA-02_CLATRA-02_R_CO").checked = false;
        document.getElementById("IHTTTRA-02_CLATRA-02_R_CO").disabled = false;
        document.getElementById('row_tramite_X_CO').style.display = "flex";
        document.getElementById('row_tramite_X_PE').style.display = "flex";
      } else {
        document.getElementById("IHTTTRA-02_CLATRA-03_R_PS").checked = false;
        document.getElementById("IHTTTRA-02_CLATRA-03_R_PS").disabled = false;
        document.getElementById('row_tramite_X_PS').style.display = "flex";
      }
      //***********************************************************************************************************************/
      // Se pago el cambio de placa de la unidad actual, esto se hace cuando se detecta que la undiad cambio de placa
      // y no se encuentra en nuestros registros el pago de ese cambio de placa
      //***********************************************************************************************************************/
      document.getElementById("estaPagadoElCambiodePlaca").value = datos[1][0]['Unidad'][0]['estaPagadoElCambiodePlaca'];
      console.log(21);
      if (datos[1][0]['Unidad'][0]['estaPagadoElCambiodePlaca'] == false) {
        document.getElementById("IHTTTRA-03_CLATRA-15_M_CL").checked = true;
        document.getElementById("IHTTTRA-03_CLATRA-15_M_CL").disabled = true;
        document.getElementById("concesion_tramite_placa_CL").style = "display:flex;text-transform: uppercase;";
        document.getElementById("concesion_tramite_placa_CL").value =  datos[1][0]['Unidad'][0]['ID_Placa'];
        document.getElementById("concesion_tramite_placa_CL").setAttribute("readonly", true);
        esCambioDePlaca = true;
      } else {
        esCambioDePlaca = false;
        document.getElementById("IHTTTRA-03_CLATRA-15_M_CL").checked = false;
        document.getElementById("IHTTTRA-03_CLATRA-15_M_CL").disabled = false;
        document.getElementById("concesion_tramite_placa_CL").style = "display:none;text-transform: uppercase;";
        document.getElementById("concesion_tramite_placa_CL").setAttribute("readonly", false);
      }
      if (datos[1][0]['Vencimientos']['renovacion_certificado_vencido'] == true) {
        document.getElementById("RequiereRenovacionConcesion").value = true;
        document.getElementById("IHTTTRA-02_CLATRA-02_R_CO").checked = true;
        document.getElementById("IHTTTRA-02_CLATRA-02_R_CO").disabled = true;
        if (datos[1][0]['Vencimientos']['rencon-cantidad'] > 0) {
          document.getElementById("concesion_fecven").innerHTML =  '  ' +
          datos[1][0]['Vencimientos']['Fecha_Expiracion'] + ' ==> ' + datos[1][0]['Vencimientos']['Nueva_Fecha_Expiracion'] + '   (' + datos[1][0]['Vencimientos']['rencon-cantidad'] + ')';
        }
        //Inicio
        document.getElementById('row_tramite_X_CO').style.display = "none";
        //Final
      } else {
        if (datos[1][0]['Vencimientos']['renovacion_permiso_especial_vencido'] == true) {
            document.getElementById("RequiereRenovacionConcesion").value = true;
            document.getElementById("IHTTTRA-02_CLATRA-03_R_PS").checked = true;
            document.getElementById("IHTTTRA-02_CLATRA-03_R_PS").disabled = true;
           //Inicio
            document.getElementById('row_tramite_X_PS').style.display = "none";
           //Final
        } 
      }
      document.getElementById("RequiereRenovacionPerExp").value = datos[1][0]['Vencimientos']['renovacion_permisoexplotacion_vencido'];
      if (datos[1][0]['Vencimientos']['renovacion_permisoexplotacion_vencido'] == true) {
        console.log(18);
        document.getElementById("IHTTTRA-02_CLATRA-01_R_PE").checked = true;
        console.log(19);
        document.getElementById("IHTTTRA-02_CLATRA-01_R_PE").disabled = true;
        console.log(20);
        if (datos[1][0]['Vencimientos']['renper-explotacion-cantidad'] > 0) {
          document.getElementById("concesion_perexp").innerHTML = document.getElementById("concesion_perexp").innerHTML +  ' ||  ' +  datos[1][0]['Vencimientos']['Fecha_Expiracion_Explotacion'] + ' ==> ' + datos[1][0]['Vencimientos']['Nueva_Fecha_Expiracion_Explotacion']  + '   (' + datos[1][0]['Vencimientos']['renper-explotacion-cantidad'] + ')';
        }
        //Inicio
        document.getElementById('row_tramite_X_PE').style.display = "none";
        //Final
      } else {
        document.getElementById("IHTTTRA-02_CLATRA-01_R_PE").checked = false;
        document.getElementById("IHTTTRA-02_CLATRA-01_R_PE").disabled = false;
      }
      //***********************************************************************************************************************************/
      // INICIO: Caracterización de la Concesion
      //***********************************************************************************************************************************/      
      console.log(10);
      document.getElementById("ID_Categoria").value = datos[1][0]['ID_Categoria'];
      console.log(11);
      document.getElementById("ID_Tipo_Servicio").value = datos[1][0]['ID_Tipo_Servico'];
      console.log(12);
      document.getElementById("ID_Modalidad").value = datos[1][0]['ID_Modalidad'];
      console.log(13);
      document.getElementById("ID_Clase_Servicio").value = datos[1][0]['ID_Clase_Servico'];      
      //***********************************************************************************************************************************/
      // FINAL: Caracterización de la Concesion
      //***********************************************************************************************************************************/      
      //***********************************************************************************************************************************/
      // INICIO:
      // Marcando tramites obligatrios dependiendo de las condciones de vencimiento de las concesiones y permisos de explotacion
      // y si ya se pago el cambio de placa de la unidad actual, si tuvo cambio de placa
      //***********************************************************************************************************************************/      
      console.log(0);
      document.getElementById("NuevaFechaVencimientoConcesion").value = datos[1][0]['Vencimientos']['Nueva_Fecha_Expiracion'];
      document.getElementById("NuevaFechaVencimientoPerExp").value = datos[1][0]['Vencimientos']['Nueva_Fecha_Expiracion_Explotacion'];
      document.getElementById("CantidadRenovacionesConcesion").value = datos[1][0]['Vencimientos']['rencon-cantidad'];
      document.getElementById("CantidadRenovacionesPerExp").value = datos[1][0]['Vencimientos']['renper-explotacion-cantidad']; 
      document.getElementById("combustible").value = datos[1][0]['Unidad'][0]['Combustible'];
      document.getElementById("concesion_vin").value = datos[1][0]['Unidad'][0]['VIN'];
      document.getElementById("concesion_placa").value = datos[1][0]['Unidad'][0]['ID_Placa'];
      document.getElementById("concesion_serie").value = datos[1][0]['Unidad'][0]['Chasis'];
      document.getElementById("concesion_motor").value = datos[1][0]['Unidad'][0]['Motor'];
      console.log(1);
      document.getElementById("concesion_tipo_vehiculo").value = datos[1][0]['Unidad'][0]['Tipo'];
      console.log(2);
      document.getElementById("concesion_modelo_vehiculo").value = datos[1][0]['Unidad'][0]['Modelo'];          
      console.log(3);
      dataConcesion['marcas'] = datos[1][0]['Marcas'];
      console.log(4);
      fLlenarSelect('marcas',datos[1][0]['Marcas'],datos[1][0]['Unidad'][0]['ID_Marca'],false,{text: 'SELECCIONE UN AÑO', value: '-1'});            
      console.log(5);
      dataConcesion['colores'] = datos[1][0]['Colores'];
      fLlenarSelect('colores',datos[1][0]['Colores'],datos[1][0]['Unidad'][0]['ID_Color'],false,{text: 'SELECCIONE UN AÑO', value: '-1'});
      dataConcesion['anios'] = datos[1][0]['Anios'];
      fLlenarSelect('anios',datos[1][0]['Anios'],datos[1][0]['Unidad'][0]['Anio'],false,{text: 'SELECCIONE UN AÑO', value: '-1'});   
      document.getElementById("concesion_tipovehiculo").innerHTML = datos[1][0]['Unidad'][0]['DESC_Tipo_Vehiculo'];
      console.log(6);
    }
    document.getElementById("concesion_cerant").innerHTML = datos[1][0]['Certificado Anterior'];
    document.getElementById("concesion_numregant").innerHTML = datos[1][0]['Registro_Anterior'];
    document.getElementById("concesion_numeroregistro").innerHTML = datos[1][0]['Numero_Registro'];
    document.getElementById("concesion_categoria").innerHTML = datos[1][0]['DESC_Categoria'];
    //document.getElementById("idVista").style = 'background-image: url("data:image/jpeg;base64,assets/images/copc.jpeg")';
    //document.getElementById("concesion").style = "display:inline;";
    document.getElementById("btnconcesion").style = "display:inline;";
    document.getElementById("btnmultas").style = "display:inline;";
    document.getElementById("btnconsultas").style = "display:inline;";
    document.getElementById("btnperexp").style = "display:inline;";
    document.getElementById("concesion_vin").focus();
  }
  
  function f_RenderConcesionTramites() {
    //document.getElementById("idVista").style = "display:block;"  
    //document.getElementById("idVista").innerHTML = datos[1][0]['Vista'];
    var concesionlabel = document.getElementById("concesion1label");
    if (concesionlabel != null) {
      concesionlabel.innerHTML = document.getElementById("concesionlabel").innerHTML;
    }
    document.getElementById("concesion1_concesion").innerHTML = document.getElementById("concesion_concesion").innerHTML;
    document.getElementById("concesion1_perexp").innerHTML = document.getElementById("concesion_perexp").innerHTML;
    document.getElementById("concesion1_fecexp").innerHTML = document.getElementById("concesion_fecexp").innerHTML;
    document.getElementById("concesion1_resolucion").innerHTML = document.getElementById("concesion_resolucion").innerHTML;    
    document.getElementById("concesion1_fecven").innerHTML = document.getElementById("concesion_fecven").innerHTML;
    document.getElementById("concesion1_nombreconcesionario").innerHTML = document.getElementById("concesion_nombreconcesionario").innerHTML;
    document.getElementById("concesion1_rtn").innerHTML = document.getElementById("concesion_rtn").innerHTML;
    document.getElementById("concesion1_fecexp").innerHTML = document.getElementById("concesion1_fecexp").innerHTML;
    document.getElementById("concesion1_resolucion").innerHTML = document.getElementById("concesion1_resolucion").innerHTML;
    document.getElementById("concesion1_nombre_propietario").innerHTML = document.getElementById("concesion_nombre_propietario").innerHTML;
    document.getElementById("concesion1_identidad_propietario").innerHTML = document.getElementById("concesion_identidad_propietario").innerHTML;
    if (document.getElementById("concesion_placaanterior").innerHTML != '') {
      document.getElementById("concesion1_placaanterior").innerHTML = document.getElementById("concesion_placaanterior").innerHTML;
      document.getElementById("concesion1_placaanterior").style = 'display:inline;';
    } else {
      document.getElementById("concesion1_placaanterior").style = 'display:none;';
      document.getElementById("concesion1_placaanterior").innerHTML = '';
    }
    document.getElementById("concesion1_tipo_vehiculo").value = document.getElementById("concesion_tipo_vehiculo").value;
    document.getElementById("concesion1_modelo_vehiculo").value = document.getElementById("concesion_modelo_vehiculo").value;          
    document.getElementById("concesion1_vin").value = document.getElementById("concesion_vin").value;
    document.getElementById("concesion1_placa").value = document.getElementById("concesion_placa").value;
    document.getElementById("concesion1_serie").value = document.getElementById("concesion_serie").value;
    document.getElementById("concesion1_motor").value = document.getElementById("concesion_motor").value;
    fLlenarSelect('marcas1',dataConcesion['marcas'],document.getElementById("marcas").value,false,{text: 'SELECCIONE UN AÑO', value: '-1'});            
    fLlenarSelect('colores1',dataConcesion['colores'],document.getElementById("colores").value,false,{text: 'SELECCIONE UN AÑO', value: '-1'});
    fLlenarSelect('anios1',dataConcesion['anios'],document.getElementById("anios").value,false,{text: 'SELECCIONE UN AÑO', value: '-1'});   
    document.getElementById("concesion1_tipovehiculo").innerHTML = document.getElementById("concesion_tipovehiculo").innerHTML;
    document.getElementById("concesion1_cerant").innerHTML = document.getElementById("concesion_cerant").innerHTML;
    document.getElementById("concesion1_numregant").innerHTML = document.getElementById("concesion_numregant").innerHTML;
    document.getElementById("concesion1_numeroregistro").innerHTML = document.getElementById("concesion_numeroregistro").innerHTML;
    document.getElementById("concesion1_categoria").innerHTML = document.getElementById("concesion_categoria").innerHTML;
    document.getElementById("combustible1").value = document.getElementById("combustible").value;
    document.getElementById("capacidad1").value = document.getElementById("capacidad").value;
    document.getElementById("alto1").value = document.getElementById("alto").value;
    document.getElementById("largo1").value = document.getElementById("largo").value;
    document.getElementById("ancho1").value = document.getElementById("ancho").value;    
  }  

  function f_FetchCallConcesion(idConcesion,event,idinput) {
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
    fd.append("action", 'get-concesion');
    //Adjuntando el idApoderado al FormData
    fd.append("RTN_Concesionario", document.getElementById("rtnsoli").value);
    fd.append("N_Certificado", idConcesion);
    // Fetch options
    const options = {
      method: 'POST',
      body: fd,
    };
    // Hacer al solicitud fetch con un timeout de 2 minutos
    fetchWithTimeout(url, options, 120000)
      .then(response => response.json())
      .then(function (datos) {
        if (typeof datos[0] != 'undefined') {
          if (datos[0] > 0) {
            //**************************************************************************************************************************/
            // Si el vehiculo fue recuperado desde el IP, favor continuar con el proceso normal y mover al informacion a la pantalla
            //**************************************************************************************************************************/
            if (datos[1][0]['Codigo_IP'] == 200 && datos[1][0]['Unidad'][0]['Bloqueado'] == false) {
              if (datos[1][0]['Unidad'][0]['Multas'] && datos[1][0]['Unidad'][0]['Multas'][0].length === 0) {
                //Ocultar Pantalla Modal donde se Ingresa a Concesión
                $('#modalConcesion').modal('hide');
                isRecordGetted[currentstep] = idConcesion;
                f_RenderConcesion(datos);
                //**************************************************************************************************************************/
                //Enviando Toast de Exito en Recuperación la Información de la Concesión
                //**************************************************************************************************************************/
                sendToast("INFORMACIÓN DE LA CONCESIÓN RECUPERADA EXITOSAMENTE",$appcfg_milisegundos_toast,'',true,true,"top","right",true,$appcfg_background_toast,function(){},'success',$appcfg_pocision_toast,$appcfg_icono_toast);
              } else {
                fSweetAlertEventSelect(event,'MULTAS', 'LA UNIDAD TIENE MULTA(S) PENDIENTE(S) DE PAGO, FAVOR PAGAR LAS MULTAS PREVIO A INGRESAR EL TRAMITE', 'error');
                event.preventDefault();
                event.target.classList.add("errortxt");
                var label = document.getElementById(event.target.id + 'label');
                if (label != null) {
                  document.getElementById(event.target.id + 'label').classList.add("errorlabel");
                }
                paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
                isError = true;
                document.getElementById("btnconcesion").style = "display:none;";
                document.getElementById("btnmultas").style = "display:none;";
                document.getElementById("btnconsultas").style = "display:none;";
                document.getElementById("btnperexp").style = "display:none;";              
              }
            } else {
              if (datos[1][0]['Codigo_IP'] == 200 && datos[1][0]['Unidad'][0]['Bloqueado'] == true) {
                fLimpiarPantalla();
                fSweetAlertEventSelect(event,'BLOQUEADA', 'LA UNIDAD ESTA BLOQUEADA EN EL IP, NO SE PUEDE REGISTRAR ESTA UNIDAD', 'error');
                event.preventDefault();
                event.target.classList.add("errortxt");
                var label = document.getElementById(event.target.id + 'label');
                if (label != null) {
                  document.getElementById(event.target.id + 'label').classList.add("errorlabel");
                }
                paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
                isError = true;
                document.getElementById("btnconcesion").style = "display:none;";
                document.getElementById("btnmultas").style = "display:none;";
                document.getElementById("btnconsultas").style = "display:none;";
                document.getElementById("btnperexp").style = "display:none;";              
              } else {
                if (datos[1][0]['Codigo_IP'] != 200) {
                  fLimpiarPantalla();
                  fSweetAlertEventSelect(event,'CONEXIÓN IP', 'LA CONEXIÓN AL IP, ESTA PRESENTANDO PROBLEMAS, FAVOR INTENTELO EN UN MOMENTO Y SI EL ERROR PERSISTE CONTACTE AL ADMON DEL SISTEMA', 'error');
                  event.preventDefault();
                  event.target.classList.add("errortxt");
                  var label = document.getElementById(event.target.id + 'label');
                  if (label != null) {
                    document.getElementById(event.target.id + 'label').classList.add("errorlabel");
                  }
                  paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
                  isError = true;
                  document.getElementById("btnconcesion").style = "display:none;";
                  document.getElementById("btnmultas").style = "display:none;";
                  document.getElementById("btnconsultas").style = "display:none;";
                  document.getElementById("btnperexp").style = "display:none;";              
  
                }
              }
            }
          } else {
              fLimpiarPantalla();
              fSweetAlertEventSelect(event,'INFORMACIÓN', 'LA CONCESIÓN ASOCIADA AL SOLICITANTE ACTUAL NO EXISTE EN NUESTRA BASE DE DATOS, FAVOR VERIFIQUE EL NÚMERO E INTENTELO NUEVAMENTE', 'warning');
              event.preventDefault();
              event.target.classList.add("errortxt");
              var label = document.getElementById(event.target.id + 'label');
              if (label != null) {
                document.getElementById(event.target.id + 'label').classList.add("errorlabel");
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
          if (typeof datos.error != 'undefined') {
            fLimpiarPantalla();
            fSweetAlertEventSelect(event, datos.errorhead , datos.error + '- ' + datos.errormsg , 'error');
            event.preventDefault();
            event.target.classList.add("errortxt");
            var label = document.getElementById(event.target.id + 'label');
            if (label != null) {
              document.getElementById(event.target.id + 'label').classList.add("errorlabel");
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
              fSweetAlertEventSelect(event,'INFORMACIÓN', 'ERROR DESCONOCIDO, INTENTELO DE NUEVO EN UN MOMENTO, SI EL ERROR PERSISTE CONTACTE AL ADMINISTRADOR DEL SISTEMA', 'error');
              event.preventDefault();
              //event.target.select();
              event.target.classList.add("errortxt");
              var label = document.getElementById(event.target.id + 'label');
              if (label != null) {
                document.getElementById(event.target.id + 'label').classList.add("errorlabel");
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
      })
      .catch((error) => {
        fLimpiarPantalla();
        fSweetAlertEventSelect(event,'CONEXÍON', 'ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA', 'warning');
        event.preventDefault();
        event.target.classList.add("errortxt");
        var label = document.getElementById(event.target.id + 'label');
        if (label != null) {
          document.getElementById(event.target.id + 'label').classList.add("errorlabel");
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
  //*********************************************************************************************************************/
  //** Final Function para Establecer la Unidad de los Tramites                                                        **/
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
      var Permiso_Explotacion = document.getElementById("concesion_perexp").innerHTML.split('||')[0];
    } else {
      var Permiso_Especial = document.getElementById("concesion_concesion").innerHTML;
    }
    //*********************************************************************************************************************/
    // Si es Certificado Entra Aqui para establecer el CO y PE
    //*********************************************************************************************************************/
    const ConcesionPreforma={Certificado: Certificado, Permiso_Explotacion: Permiso_Explotacion, Permiso_Especial: Permiso_Especial,ID_Categoria : document.getElementById("ID_Categoria").value,ID_Tipo_Servico : document.getElementById("ID_Tipo_Servicio").value,ID_Modalidad : document.getElementById("ID_Modalidad").value, ID_Clase_Servicio: document.getElementById("ID_Clase_Servicio").value, esCambioDePlaca: esCambioDePlaca,esCambioDeVehiculo: esCambioDeVehiculo};
    return ConcesionPreforma;  
  }
  //*********************************************************************************************************************/
  //** Final Function para Establecer los Codigos de los Tramites                                                        **/
  //*********************************************************************************************************************/    
  //*********************************************************************************************************************/
  //** Inicio Function para Establecer el Apoderado de los Tramites                                                           **/
  //*********************************************************************************************************************/
  function setApoderado() {
    const ApoderadoPreforma = {RTN: document.getElementById("colapoderado").value,Lugar_Entrega: document.getElementById("entregadocs").value,
                              Telefono: document.getElementById("telapoderado").value,Email: document.getElementById("emailapoderado").value,
                              Nombre: document.getElementById("nomapoderado").value,Identidad: document.getElementById("identidadapod").value,
                              Direccion: document.getElementById("dirapoderado").value,Tipo_Presentacion: document.getElementById("tipopresentacion").value};
     return ApoderadoPreforma;                              
  }
  //*********************************************************************************************************************/
  //** Final Function para Establecer el Apoderado de los Tramites                                                     **/
  //*********************************************************************************************************************/
  //*********************************************************************************************************************/
  //** Inicio Function para Establecer el Solicitante de los Tramites                                                  **/
  //*********************************************************************************************************************/
  function setSolicitante() {
    const SolicitantePreforma = {RTN: document.getElementById("rtnsoli").value,Tipo_Solicitante: document.getElementById("tiposolicitante").value,
                                Nombre: document.getElementById("nomsoli").value, Domicilio: document.getElementById("domiciliosoli").value,
                                Denominacion: document.getElementById("denominacionsoli").value,Departamento: document.getElementById("Departamentos").value,
                                Municipio: document.getElementById("Municipios").value,Aldea: document.getElementById("Aldeas").value,
                                Telefono: document.getElementById("telsoli").value,Email: document.getElementById("emailsoli").value};
     return SolicitantePreforma;                              
  }
  //*********************************************************************************************************************/
  //** Final Function para Establecer el Solicitante de los Tramites                                                   **/
  //*********************************************************************************************************************/
  //*********************************************************************************************************************/
  //** Inicio Function para Establecer la Unidad de los Tramites                                                       **/
  //*********************************************************************************************************************/
  function setUnidad() {
    //*********************************************************************************************************************/
    // Si es Certificado Entra Aqui para establecer el CO y PE
    //*********************************************************************************************************************/
    if (esCertificado) {
      var Certificado = document.getElementById("concesion_concesion").innerHTML;
      var Permiso_Explotacion = document.getElementById("concesion_perexp").innerHTML.split('||')[0];
    } else {
      var Permiso_Especial = document.getElementById("concesion_concesion").innerHTML;
    }
    const UnidadPreforma = {VIN: document.getElementById("concesion_vin").value,Placa: document.getElementById("concesion_placa").value,
                            Serie: document.getElementById("concesion_serie").value,Motor: document.getElementById("concesion_motor").value,
                            Marca: document.getElementById("marcas").value,Color: document.getElementById("colores").value,
                            Anio: document.getElementById("anios").value,Combustible: document.getElementById("combustible").value,
                            Capacidad: document.getElementById("capacidad").value,Alto: document.getElementById("alto").value,
                            Largo: document.getElementById("largo").value,Ancho: document.getElementById("ancho").value,
                            Nombre_Propietario: document.getElementById("concesion_nombre_propietario").innerHTML,
                            Identidad_Propietario: document.getElementById("concesion_identidad_propietario").innerHTML,
                            Modelo: document.getElementById("concesion_modelo_vehiculo").value, Tipo: document.getElementById("concesion_tipo_vehiculo").value, ID_Placa_Antes_Replaqueo: document.getElementById("concesion_placaanterior").innerHTML};
     return UnidadPreforma;
  }
  //*********************************************************************************************************************/
  //** Final Function para Establecer la Unidad de los Tramites                                                        **/
  //*********************************************************************************************************************/  
  //*********************************************************************************************************************/
  //** Inicio Function para Establecer la Unidad de los Tramites                                                       **/
  //*********************************************************************************************************************/
  function setUnidad1() {
    const Unidad1Preforma = {VIN: document.getElementById("concesion1_vin").value,Placa: document.getElementById("concesion1_placa").value,
                            Serie: document.getElementById("concesion1_serie").value,Motor: document.getElementById("concesion1_motor").value,
                            Marca: document.getElementById("marcas1").value,Color: document.getElementById("colores1").value,
                            Anio: document.getElementById("anios1").value,Combustible: document.getElementById("combustible1").value,
                            Capacidad: document.getElementById("capacidad1").value,Alto: document.getElementById("alto1").value,
                            Largo: document.getElementById("largo").value,Ancho: document.getElementById("ancho").value,
                            Nombre_Propietario: document.getElementById("concesion1_nombre_propietario").innerHTML,
                            Identidad_Propietario: document.getElementById("concesion1_identidad_propietario").innerHTML,
                            Modelo: document.getElementById("concesion1_modelo_vehiculo").value, Tipo: document.getElementById("concesion1_tipo_vehiculo").value, 
                            ID_Placa_Antes_Replaqueo: document.getElementById("concesion1_placaanterior").innerHTML};
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
            TramitesPreforma.push({Codigo: chk.value, descripcion: document.getElementById("descripcion_"+chk.value).innerHTML, Monto: chk.getAttribute('data-monto'), ID_Categoria : document.getElementById("ID_Categoria").value,ID_Tipo_Servico : document.getElementById("ID_Tipo_Servicio").value,ID_Modalidad : document.getElementById("ID_Modalidad").value,ID_Clase_Servico : document.getElementById("ID_Clase_Servicio").value});
        }
      });
    }
    return TramitesPreforma;  
  }
  //*********************************************************************************************************************/
  //** Final Function para Establecer los Codigos de los Tramites                                                        **/
  //*********************************************************************************************************************/    
  //*********************************************************************************************************************/
  //** Inicio Function para Salvar La Preforma                                                                         **/
  //*********************************************************************************************************************/
  function salvarConcesion() {
    // URL del Punto de Acceso a la API
    const url = $appcfg_Dominio + "Api_Ram.php";
    let fd = new FormData(document.forms.form1);
    // Adjuntando el action al FormData
    fd.append("action", 'save-preforma');    
    console.log('setConcesion()');
    // Adjuntando el Concesion y Caracterización al FormData
    fd.append("Concesion", JSON.stringify(setConcesion()));    
    console.log('setApoderado');
    // Adjuntando el Apoderado al FormData
    fd.append("Apoderado", JSON.stringify(setApoderado()));
    console.log('setSolicitante');
    // Adjuntando el Solicitante al FormData
    fd.append("Solicitante", JSON.stringify(setSolicitante()));
    console.log('setUnidad');
    // Adjuntando el Unidad al FormData
    fd.append("Unidad", JSON.stringify(setUnidad()));
    console.log('setUnidad1');
    // Adjuntando el Unidad1 al FormData
    fd.append("Unidad1", JSON.stringify(setUnidad1()));
    console.log('setTramites');
    // Adjuntando el Tramites al FormData
    fd.append("Tramites",JSON.stringify(setTramites()));
    //  Fetch options
    const options = {
      method: 'POST',
      body: fd,
    };
    // Hacer al solicitud fetch con un timeout de 2 minutos
    fetchWithTimeout(url, options, 120000)
      .then(response => response.json())
      .then(function (datos) { alert('0k');return false;})
      .catch((error) => {
        fSweetAlertEventSelect('','CONEXÍON', 'ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA', 'warning');
        return true;
      });
  }   
  //*********************************************************************************************************************/
  //** Final Function para Salvar La Preforma                                                                          **/
  //*********************************************************************************************************************/
  function f_CaseFetchCalls(value,event,idinput) {
    //*********************************************************************************/
    // Dependiendo del panel actual se ejecuta una función para validar los campos
    //*********************************************************************************/
    switch (currentstep)
    {
      case 0: f_FetchCallApoderado(value,event,idinput); break;
      case 1: f_FetchCallSolicitante(value,event,idinput); break;
      case 2: f_FetchCallConcesion(value,event,idinput); break;
      case 3: f_FetchCallApoderado(value,event,idinput); break;
      case 4: f_FetchCallApoderado(value,event,idinput); break;
      default: f_FetchCallApoderado(value,event,idinput); break;
    }
  }        

  function showConcesion() {
    var event = new Event('change', {
      'bubbles': true,
      'cancelable': true
    });
    var input = document.getElementById('concesion'); 
    input.dispatchEvent(event);
    input.focus();
    input.select();
    f_FetchCallConcesion(input.value,event,input.id)   
  }
  
  var concesion3ra = document.getElementById('btnconcesion');
  if (concesion3ra != null) {
      concesion3ra.addEventListener('click', function(event) {
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
    if (currentstep == 3) {
      const concesionDetails = concesionNumber[currentConcesionIndex];
      // ********************************************************************************************************************/
      // Si ya se salvo la información de la pantalla 3
      // ********************************************************************************************************************/
      if (concesionDetails.Concesion_Salvada == false) {
        f_RenderConcesionTramites();
      }
    }
  }



  function showModalConcesiones(){
    $('#modalConcesion').modal('show');
  }

  addConcesion.addEventListener('click', function(event) {
    showModalFromShown = true;
    const response = {error: false};
    console.log(currentstep);
    if(currentstep != 2) {
        if(currentstep == 3) {
          //**********************************************************************************************************/
          //**Salvar La Concesion Actual (Certificado de Operación o Permiso Especial)                             ***/
          //**********************************************************************************************************/
          alert(1);
          response.error = salvarConcesion();
          alert(2);
        }
      if (response.error == false) {
        stepperForm.to(3);    
       }
     } else {
      if(currentstep == 2) {
        setTimeout(showModalConcesiones, 100);
      }
     }
  });


 
  //Cuando presente el nuevo panel
  stepperFormEl.addEventListener('shown.bs-stepper', function (event) {

    currentstep = event.detail.indexStep;
    setFocus = true;
    isError = false;
    isPrevious = false;

    console.log('currentstep on shown: '+currentstep);


    if (typeof isDirty[currentstep] != 'undefined') {
      isDirty[currentstep] == false;
    } 

    switch (currentstep)
    {
      case 0: 
        if (isRecordGetted[2] == '' && document.getElementById("addConcesion").style.display == 'flex') {
          document.getElementById("addConcesion").style = "display:none;"    
        }
        document.getElementById("concesion_tramites").style = "display:none;"    
        document.getElementById("colapoderado").focus(); 
        break;
      case 1: 
        document.getElementById("concesion_tramites").style = "display:none;"    
        document.getElementById("rtnsoli").focus();      
        break;
      case 2: 
        document.getElementById("addConcesion").innerHTML = '<i class="fa-solid fa-magnifying-glass"></i>&nbsp;&nbsp;BUSCAR'   
        document.getElementById("addConcesion").style = "display:flex;"    
        document.getElementById("concesion_tramites").style = "display:none;"    
        if (showModalFromShown == true) {
          showModalFromShown = false;
          $('#modalConcesion').modal('show');
          setTimeout(setFocusElementConcesion, 250);
        } else {
          document.getElementById("concesion_vin").focus();    
        } 
        break;
      case 3: 
        document.getElementById("addConcesion").innerHTML = '<i class="fa-solid fa-floppy-disk"></i>&nbsp;&nbsp;SALVAR'   
        //**********************************************************************************************************************/
        //*Agregando la concesión al arreglo de indice de concesiones y recuperando el indice de la concesion                  */
        //**********************************************************************************************************************/
        currentConcesionIndex = updateCollection(concesionIndex,document.getElementById("concesion_concesion").innerHTML);
        //**********************************************************************************************************************/
        //* Si es la primera vez que entra a la 4ta Pantalla para esta concesion se guardar el objeto con la concesion          */
        //**********************************************************************************************************************/
        if (!concesionNumber[currentConcesionIndex]) {
          // Recuperando el valor de la Permisos de Explotación
          var perexpobj = document.getElementById("concesion_perexp");
          var PerExp = '';
          if (perexpobj != null) {
            PerExp = perexpobj.innerHTML;
          }
          concesionNumber[currentConcesionIndex]={Concesion: document.getElementById("concesion_concesion").innerHTML, PerExp: PerExp, Concesion_Salvada: false, Cambio_Unidad: false, Cambio_Placa: false, Unidad_Recuperada_IP: false, Requiere_Renovacion_Concesion: false, Requiere_Renovacion_PerExp: false, Renovacion_Concesion_Tramitada: false, Renovacion_PerExp_Tramitada: false, ID_Expediente: '', ID_Formulario_Solicitud: '', CodigoAvisoCobro: 0, ID_Resolucion: ''};
        }        
        document.getElementById("concesion").value='';
        document.getElementById("concesion_tramites").value = '';
        //document.getElementById("addConcesion").style = "display:flex;"    
        document.getElementById("concesion_tramites").style = "display:fixed;"    
        setTimeout(setDataPreviewFormalities, 1000);
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
          var el =  document.getElementById("IHTTTRA-02_CLATRA-03_R_PS");    
          if (el != null) {
            el.focus();    
          }
        }
        break;
      }       
    })




  //Antes de hacer la transición al nuevo panel
  stepperFormEl.addEventListener('show.bs-stepper', function (event) {
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
      if (sum>0){
        event.preventDefault();
        hayerror = true;
      } 
    } else {
      isError = false;
    }
  })      

  var ii = 0;
  while (ii < stepperPanList.length) {
    var element = document.getElementById('test-form-'+(ii+1));
    // Obtener todos los elementos de entrada dentro de este elemento
    var inputselect = element.querySelectorAll('.test-select');
    // Convertir NodeList a Array para facilitar la manipulación (opcional)
    inputselect = Array.from(inputselect);
    // Iterar sobre los elementos de entrada y eliminar las clases de error
    inputselect.forEach(input => {
      //Definiendo evento change para los elementos select
      input.addEventListener('change', function(event) {

        if (event.target.getAttribute('data-valor') > event.target.value) {
          event.preventDefault();

          event.target.classList.add("errortxt");

          var label = document.getElementById(event.target.id + 'label');
          if (label != null) {
            label.classList.add("errorlabel");
          }

          let labelerror = document.getElementById(event.target.id + 'labelerror');
          if (labelerror != null) {
            labelerror.style.visibility = "visible";
          }

          paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
        } else{
          event.target.classList.remove("errortxt");

          var label = document.getElementById(event.target.id + 'label');
          if (label != null) {
            label.classList.remove("errorlabel");
          }

          let labelerror = document.getElementById(event.target.id + 'labelerror');
          if (labelerror != null) {
              labelerror.style.visibility = "hidden";
          }

          paneerror[currentstep][idinputs.indexOf(idinput)] = 0;
          //Moverse al siguiente input
          moveToNextInput(input,0);
          if (event.target.id=='Departamentos') {
            fCargarDwd('get-municipios',event.target.value,'Municipios',-1,{text: 'SELECCIONE UN MUNICIPIO', value: '-1'},['Aldeas'],['<option selected value="-1">SELECCIONE UNA ALDEA</option>']);
          } else {
            if (event.target.id=='Municipios') {
              fCargarDwd('get-aldeas',event.target.value,'Aldeas',-1,{text: 'SELECCIONE UNA ALDEA', value: '-1'})
            }
          }
        }
      });
      //Definiendo evento keydown para los elementos select
      input.addEventListener('keydown', function(event) {
        if (event.key === 'Tab' ||  event.key === 'Enter') {

          if (event.target.getAttribute('data-valor') > event.target.value) {

            event.preventDefault();
            event.target.classList.add("errortxt");
            var label = document.getElementById(event.target.id + 'label');

            if (label != null) {
              label.classList.add("errorlabel");
            }

            let labelerror = document.getElementById(event.target.id + 'labelerror');
            if (labelerror != null) {
              labelerror.style.visibility = "visible";
            }

            paneerror[currentstep][idinputs.indexOf(idinput)] = 1;

          } else{

            event.target.classList.remove("errortxt");

            var label = document.getElementById(event.target.id + 'label');
            if (label != null) {
              label.classList.remove("errorlabel");
            }


            let labelerror = document.getElementById(event.target.id + 'labelerror');
            if (labelerror != null) {
              labelerror.style.visibility = "hidden";
            }

            paneerror[currentstep][idinputs.indexOf(idinput)] = 0;

          }
          //Mover al siguiente input
          if (event.key === 'Enter') {
            moveToNextInput(input,0);
          }
        }
      });
      

    });
    ii++;
  }
  
  testinputs.forEach(function(inputtest) {
    idinputs.push(inputtest.id);
    //********************************************************************/
    //Definiendo el arreglo de errores por panel e input
    //********************************************************************/
  });
    
  
  testcontrols.forEach(function(input) {
    //*****************************************************************************/
    //Creando evento change para cada input
    //*****************************************************************************/
    input.addEventListener('change', function(event) {
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

      var label = document.getElementById(event.target.id + 'label');

      if (label != null) {
        label.classList.add("errorlabel");
      }

      var labelerror = document.getElementById(event.target.id + 'labelerror');

      if (labelerror != null) {
        labelerror.style.visibility = "visible";
      }

      paneerror[currentstep][idinputs.indexOf(idinput)] = 1;

    } else {

      event.target.classList.remove("errortxt");

      var label = document.getElementById(event.target.id + 'label');
      if (label != null) {
        label.classList.remove("errorlabel");
      }

      let labelerror = document.getElementById(event.target.id + 'labelerror');
      if (labelerror != null) {
        labelerror.style.visibility = "hidden";
      }

      paneerror[currentstep][idinputs.indexOf(idinput)] = 0;
      // Si son los input colapoderado o rtnsoli o concesion
      if (idinput=='colapoderado' || idinput=='rtnsoli' || idinput=='concesion') {
        if (typeof isRecordGetted[currentstep] == 'undefined' || isRecordGetted[currentstep] != event.target.value) {
            if (isDirty[currentstep] == false) {
              f_CaseFetchCalls(value,event,idinput);
            } else {
              isDirty[currentstep] == false;
            }
        }
      } else {	
        if (isTab==false) {
          moveToNextInput(input,0);
        }
      }
      isTab=false; 
      isFromfGetInputs=false;
    }
  });

    idVista.addEventListener('ondblclick', function(event) {
      alert('hola');
    });

    // Handle the keydown event to prevent Tab key from moving focus
    input.addEventListener('keydown', function(event) {
      // Obteniendo el id del input
      idinput = event.target.id;
      // Verificar si se presionó la tecla Tab o Enter
      if (event.key === 'Tab' ||  event.key === 'Enter') {
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

          var label = document.getElementById(event.target.id + 'label');
          if (label != null) {
            label.classList.add("errorlabel");
          }

          var labelerror = document.getElementById(event.target.id + 'labelerror');
          if (labelerror != null) {
            labelerror.style.visibility = "visible"
          }

          // Marcando que hay un error en el input actual del panel actual
          paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
        } else {
          
          event.target.classList.remove("errortxt");
          
          var label = document.getElementById(event.target.id + 'label');
          if (label != null) {
            label.classList.remove("errorlabel");
          }
          
          var labelerror = document.getElementById(event.target.id + 'labelerror');
          if (labelerror != null) {
            labelerror.style.visibility = "hidden"
          }

          paneerror[currentstep][idinputs.indexOf(idinput)] = 0;
          if (idinput=='colapoderado' || idinput=='rtnsoli' || idinput=='concesion' && event.key === 'Enter') {
            if (typeof isRecordGetted[currentstep] == 'undefined' || isRecordGetted[currentstep] != event.target.value) {
              f_CaseFetchCalls(value,event,idinput);
              isDirty[currentstep] == true;
            } else {
              //Mover al siguiente input
              if (event.key === 'Enter') {
                moveToNextInput(input,0);
              }
            }	
          } else {
            //Mover al siguiente input
            if (event.key === 'Enter') {
              moveToNextInput(input,0);
            }
          }
        }
      }
      // Marcando que no hay error aun
      isError = false;
    });  
    isDirty[currentstep] = false;
  });
  setTimeout(setFocusElement, 250);
})      

  //**************************************************************************************/
  //Validaciones sobre Check Box de Tramites
  //**************************************************************************************/
  function fReviewCheck(el) {
    // Separando el valor del input
    const [tipo_tramite,clase_tramite,acronimo_tipo,acronimo_clase] = el.id.split('_');

    // Verificadno si es cambio de unidad o cambio de placa
    if (acronimo_clase && (acronimo_clase === 'CU' || acronimo_clase === 'CL')) {
      // Cache the DOM element
      const element = document.getElementById(`concesion_tramite_placa_${acronimo_clase}`);
      if (element) {
        //Validaciones si el elemento viene checked
        if (el.checked) {
          element.style.display = "flex";
          element.focus();
          // Si es cambio de unidad
          if (acronimo_clase === 'CU') {
            esCambioDeVehiculo = true;
            if (document.getElementById('estaPagadoElCambiodePlaca').value == false) {
              document.getElementById('row_tramite_M_CL').style.display = "none";
            }
            document.getElementById('row_tramite_M_CM').style.display = "none";
            document.getElementById('row_tramite_M_CC').style.display = "none";
            document.getElementById('row_tramite_M_CS').style.display = "none";
          }else{
            // Si cambio de placa
            esCambioDePlaca = true;
            document.getElementById('row_tramite_M_CU').style.display = "none";
            document.getElementById('row_tramite_M_CM').style.display = "flex";
            document.getElementById('row_tramite_M_CC').style.display = "flex";
            document.getElementById('row_tramite_M_CS').style.display = "flex";
          }
        } else {
          //Validaciones si el elemento  no viene checked          
          element.style.display = "none";
          element.value = "";
          // Si es cambio de unidad
          if (acronimo_clase === 'CU') {
            esCambioDeVehiculo = false;
            document.getElementById('row_tramite_M_CL').style.display = "flex";
            document.getElementById('row_tramite_M_CM').style.display = "flex";
            document.getElementById('row_tramite_M_CC').style.display = "flex";
            document.getElementById('row_tramite_M_CS').style.display = "flex";
          }else{
            esCambioDePlaca = false;
            // Si cambio de placa
            const checkboxIds = ['IHTTTRA-03_CLATRA-15_M_CL', 'IHTTTRA-03_CLATRA-17_M_CM', 'IHTTTRA-03_CLATRA-18_M_CC', 'IHTTTRA-03_CLATRA-19_M_CS'];
            var checked = false;
            for (let id of checkboxIds) {
              const checkbox = document.getElementById(id);
              if (checkbox && checkbox.checked) {
                var checked = true;
              }
            }          
            if (checked == false) {
              document.getElementById('row_tramite_M_CU').style.display = "flex";
            }
          }
        }
      } else {
        console.error(`Element con id 'concesion_tramite_placa_${acronimo_clase}' no encontrado.`);
      }
    } else {
      // Si son modificaciones
      if (acronimo_tipo === 'M') {
        if (el.checked) {
            // Si cambio de placa
            document.getElementById('row_tramite_M_CU').style.display = "none";
            document.getElementById('row_tramite_M_CL').style.display = "flex";
            document.getElementById('row_tramite_M_CM').style.display = "flex";
            document.getElementById('row_tramite_M_CC').style.display = "flex";
            document.getElementById('row_tramite_M_CS').style.display = "flex";
        } else {
          const checkboxIds = ['IHTTTRA-03_CLATRA-15_M_CL', 'IHTTTRA-03_CLATRA-17_M_CM', 'IHTTTRA-03_CLATRA-18_M_CC', 'IHTTTRA-03_CLATRA-19_M_CS'];
          var checked = false;
          for (let id of checkboxIds) {
            const checkbox = document.getElementById(id);
            if (checkbox && checkbox.checked && checkbox.getAttribute('disabled') != '') {
              var checked = true;
            }
          }          
          if (checked == false) {
            document.getElementById('row_tramite_M_CU').style.display = "flex";
          }
        }
      } else {
        // Si son renovaciones
        if (acronimo_tipo === 'R') {
          if (el.checked) {
            if (acronimo_clase === 'CO') {
                document.getElementById('row_tramite_X_CO').style.display = "none";
            } else {
              if (acronimo_clase === 'PE') {
                  document.getElementById('row_tramite_X_PE').style.display = "none";
              } else {
                if (acronimo_clase === 'PS') {
                  document.getElementById('row_tramite_X_PS').style.display = "none";
                }
              }
            }
          } else {
            if (acronimo_clase === 'CO') {
              document.getElementById('row_tramite_X_CO').style.display = "flex";
            } else {
              if (acronimo_clase === 'PE') {
                document.getElementById('row_tramite_X_PE').style.display = "flex";
              }else {
                if (acronimo_clase === 'PS') {
                  document.getElementById('row_tramite_X_PS').style.display = "flex";
                }
              }
            }
          }
        } else {
          // Si son reimpresiones
          if (acronimo_tipo === 'X') {
            if (el.checked) {
              if (acronimo_clase === 'CO') {
                checkbox = document.getElementById('IHTTTRA-03_CLATRA-20_R_CO');
                if (checkbox && checkbox.getAttribute('disabled') != '') {
                  document.getElementById('row_tramite_R_CO').style.display = "none";
                }
              } else {
                if (acronimo_clase === 'PE') {
                  checkbox = document.getElementById('IHTTTRA-03_CLATRA-20_R_PE');
                  if (checkbox && checkbox.getAttribute('disabled') != '') {
                    document.getElementById('row_tramite_R_PE').style.display = "none";
                  }
                } else {
                  if (acronimo_clase === 'PS') {
                    checkbox = document.getElementById('IHTTTRA-03_CLATRA-20_R_PS');
                    if (checkbox && checkbox.getAttribute('disabled') != '') {
                      document.getElementById('row_tramite_R_PS').style.display = "none";
                    }
                  }
                }
              }
            } else {
              if (acronimo_clase === 'CO') {
                document.getElementById('row_tramite_R_CO').style.display = "flex";
              } else {
                if (acronimo_clase === 'PE') {
                  document.getElementById('row_tramite_R_PE').style.display = "flex";
                } else {
                  if (acronimo_clase === 'PS') {
                    document.getElementById('row_tramite_R_PS').style.display = "flex";
                  }
                }
              }
            }
          }        
        }
      }
    }
  }

  
  function getVehiculoDesdeIP(obj) {

  if (document.getElementById('concesion_tramite_placa_CL').value != document.getElementById('concesion_tramite_placa_CU').value) {
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
      fd.append("action", 'get-vehiculo');
      //Adjuntando el idApoderado al FormData
      fd.append("ID_Placa", obj.value);
      // Fetch options
      const options = {
        method: 'POST',
        body: fd,
      };
      // Hacer al solicitud fetch con un timeout de 2 minutos
      fetchWithTimeout(url, options, 120000)
        .then(response => response.json())
        .then(function (vehiculo) {
          if (!vehiculo.error) {
            if (vehiculo.codigo && vehiculo.codigo == 200) {
              if (vehiculo.cargaUtil.estadoVehiculo == 'NO BLOQUEADO') {
                isVehiculeBlock = false;
                document.getElementById("concesion1_nombre_propietario").innerHTML = vehiculo.cargaUtil.propietario.identificacion;
                document.getElementById("concesion1_identidad_propietario").innerHTML = vehiculo.cargaUtil.propietario.nombre;
                if (vehiculo && vehiculo.cargaUtil && vehiculo.cargaUtil.placaAnterior) {
                  document.getElementById("concesion1_placaanterior").style = 'display:inline;';
                  document.getElementById("concesion1_placaanterior").innerHTML = vehiculo.cargaUtil.placaAnterior;
                } else {
                  document.getElementById("concesion1_placaanterior").style = 'display:none;';
                  document.getElementById("concesion1_placaanterior").innerHTML = '';
                }              
                //document.getElementById("tipo_vehiculo").value = datos[1][0]['Unidad'][0]['tipo'];
                //document.getElementById("modelo_vehiculo").value = datos[1][0]['Unidad'][0]['modelo'];          
                document.getElementById("combustible1").value = vehiculo.cargaUtil.combustible;
                document.getElementById("concesion1_vin").value = vehiculo.cargaUtil.vin;
                document.getElementById("concesion1_placa").value = vehiculo.cargaUtil.placa;
                document.getElementById("concesion1_serie").value = vehiculo.cargaUtil.chasis;
                document.getElementById("concesion1_motor").value = vehiculo.cargaUtil.motor;
                document.getElementById("marcas1").value = vehiculo.cargaUtil.marcacodigo;
                document.getElementById("colores1").value = vehiculo.cargaUtil.colorcodigo;
                document.getElementById("anios1").value = vehiculo.cargaUtil.axo;
                document.getElementById("concesion1_tipo_vehiculo").value = vehiculo.cargaUtil.tipo;
                document.getElementById("concesion1_modelo_vehiculo").value = vehiculo.cargaUtil.modelo;          
                sendToast("INFORMACIÓN DEL VEHICULO RECUPERADO EXITOSAMENTE DESDE EL INSTITUTO DE LA PROPIEDAD",$appcfg_milisegundos_toast,'',true,true,"top","right",true,$appcfg_background_toast,function(){},'success',$appcfg_pocision_toast,$appcfg_icono_toast);
              }  else {
                if (vehiculo.cargaUtil.estadoVehiculo == 'NO BLOQUEADO') {
                  fSweetAlertEventNormal('BLOQUEADO', 'EL VEHICULO ESTA BLOQUEADO EN EL INSTITUTO DE LA PROPIEDAD', 'error');
                  isVehiculeBlock = true;
                } else {
                  if (isset(vehiculo.codigo) == 407 || vehiculo.codigo == 408) {
                    fSweetAlertEventNormal('ADVERTENCIA', 'NO HEMOS PODIDO CONECTARNOS CON EL INSTITUTO DE LA PROPIEDAD, FAVOR INTENTENLO EN UN MOMENTO SI EL PROBLEMA PERSIOSTE CONTACTE AL ADMINISTRADOR DEL SISTEMA', 'warning');
                    isVehiculeBlock = true;
                  } else {
                    fSweetAlertEventNormal('ERROR', 'ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA', 'error');
                    isVehiculeBlock = true;
                  }
                }
              }
            } else {
              fSweetAlertEventNormal('INFORMACIÓN', 'EL VEHICULO NO HA SIDO ENCONTRADO EN LA BASE DE DATOS DEL IP', 'warning');
              isVehiculeBlock = true;
            }
          } else {
            fSweetAlertEventNormal(vehiculo.errorhead, vehiculo.error + '- ' + vehiculo.errormsg , 'error');
          }
        })
        .catch((error) => {
          fSweetAlertEventNormal('ERROR CATCH', 'ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA', 'error');
          console.log(error);
          isVehiculeBlock = true;
        });    
      } else {
        fSweetAlertEventNormal('ERROR EN PLACAS', 'LA PLACA DE LA UNIDAD NUEVA NO PUEDE SER IGUAL A LA PLACA DEL TRAMITE DE CAMBIO DE PLACA', 'warning');
      }
    }  
  
  
//**************************************************************************************/
//Habilitando las teclas F2 y F10 para moverse entre los paneles
//**************************************************************************************/
document.addEventListener('keydown', function(event) {
  if (event.key === 'F2') {
    event.preventDefault();
    var btn = document.getElementById('btnprevious'+currentstep);
    if (btn != null) {
      btn.click();
    } else {
      console.log('No existe el boton: btnprevious'+currentstep);
    }
  } else {
    if (event.key === 'F10') {
      event.preventDefault();
      var btn = document.getElementById('btnnext'+currentstep);
      if (btn != null) {
        btn.click();
      } else {
        console.log('No existe el boton: btnnext'+currentstep);
      }  
    }
  }
}); 

var scrollDiv = document.querySelector('.scroll-div');
var initialOffset = 165; // Offset inicial de 165px desde el top
var minOffset = 75; // Mínimo offset cuando se desplaza más de 165px

window.addEventListener('scroll', () => {
    if (currentstep == 3) {
      // Obtén la posición de desplazamiento vertical actual
      var scrollPosition = window.scrollY;

      // Calcula la nueva posición vertical del div
      var newYPosition = Math.max(initialOffset - scrollPosition, minOffset);

      // Actualiza la posición del div
      scrollDiv.style.top = `${newYPosition}px`;
    }
});