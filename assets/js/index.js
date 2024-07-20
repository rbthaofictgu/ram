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

  //************************************************************/
  // Moviendose al siguiente input
  //************************************************************/
  function moveToNextInput(currentInput,value) {
    var inputs = [].slice.call(document.querySelectorAll('.test-controls'));
    var currentIndex = inputs.indexOf(currentInput);
    var wasMoved=false;
    for (var i = ((currentIndex + 1)+ (value)); i < inputs.length; i++) {
      if (!inputs[i].disabled && !inputs[i].visible) {
        inputs[i].focus();
        inputs[i].select();
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
        console.log(input.id);
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
    let inputs = element.querySelectorAll('.test-select');
    // Convert NodeList to Array for easier manipulation (optional)
    inputs = Array.from(inputs);
    // Iterando entre los inputs para despachar el evento change y validar la data de cada input
    var index = 0;
    inputs.forEach(input => {
      console.log('getSelect Input Id'+input.id);
      console.log('getSelect Input'+input);
      if (input.disabled == false) {
        if (input.getAttribute('data-valor') > input.value) {        
          input.classList.add("errortxt");
          document.getElementById(input.id + 'label').classList.add("errorlabel");
          paneerror[currentstep][idinputs.indexOf(input.id)] = 1;
        } else {
          input.classList.remove("errortxt");
          document.getElementById(input.id + 'label').classList.remove("errorlabel");
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
      document.getElementById(input.id + 'label').classList.remove("errorlabel");
      if (typeof document.getElementById(input.id + 'labelerror') != 'undefined') {
        document.getElementById(input.id + 'labelerror').style.visibility = "hidden";
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
      document.getElementById(id).classList.remove("errorlabel");
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
    fd.append("action", 'get-ubicaciondepartamento');
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
            fLlenarSelect('entregadocs',datos[1],null,false,{Text: 'SELECCIONE UN LUGAR DE ENTREGA', Value: '-1'})            
          }
          if (datos[2].length > 0) {
            fLlenarSelect('Departamentos',datos[2],-1,false,{Text: 'SELECCIONE UN DEPARTAMENTO', Value: '-1'})     
            fLlenarSelect('Municipios',[],-1,false,{Text: 'SELECCIONE UN MUNICIPIO', Value: '-1'})            
            fLlenarSelect('Aldeas',[],-1,disabled=false,{Text: 'SELECCIONE UNA ALDEA', Value: '-1'})            
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
        fSweetAlertEventNormal('CONEXÍON', 'ALGO RARO PASO. INTENTALO DE NUEVO EN UN MOMENTO, SI EL PROBLEMA PERSISTE CONTACTO AL ADMINISTRADOR DEL SISTEMA', 'warning');
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
    var testinputs = document.querySelectorAll('input, select');
    console.log('testinputs.length'+testinputs.length);
    var columnas = testinputs.length;
    var filas = stepperPanList.length;
    // Crear una matriz bidimensional vacía
    paneerror = new Array(filas);
    console.log('paneerror'+paneerror);
    for (var i = 0; i < filas; i++) {
      console.log('Panels i => '+i);
      console.log('isDirty[currentstep] On Filas => '+isDirty[currentstep]);
      paneerror[i] = new Array(columnas);
        for (var ii = 0; ii < columnas; ii++) { 
          paneerror[i][ii] = 0;
        }
      isDirty[i] = false;
      isRecordGetted[i]='';
      console.log('paneerror[i] ' + paneerror[i]);
      console.log('isDirty[i] Filas ' + isDirty);
    }
    

  btnNextList.forEach(function (btn) {
    btn.addEventListener('click', function () {
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
        document.getElementById('numescritura').value = '';
        document.getElementById('fecha').value = '';
        document.getElementById('lugarcons').value = '';
        document.getElementById('rtnnotario').value = '';
        document.getElementById('nombrenotario').value = '';
        document.getElementById('tiposolicitante').value = '';
        document.getElementById('Departamentos').value = '-1'; 
        document.getElementById('Municipios').value = '-1';
        document.getElementById('Municipios').innerHTML = "";
        document.getElementById('Aldeas').value = '-1';
        document.getElementById('Aldeas').innerHTML = "";
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

  btnNextListprevious.forEach(function (btn) {
    btn.addEventListener('click', function () {
        isPrevious = true;
        stepperForm.previous();
    })
  })



  function myFunction(item, index, arr) {
    arr[index] = item * 10;
  } 

  
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
          } else {
              fLimpiarPantalla();
              fSweetAlertEventSelect(event,'INFORMACIÓN', 'EL NÚMERO DE COLEGIACIÓN NO EXISTE EN NUESTRA BASE DE DATOS, FAVOR VERIFIQUE EL NÚMERO E INTENTELO NUEVAMENTE', 'warning');
              event.preventDefault();
              event.target.classList.add("errortxt");
              document.getElementById(event.target.id + 'label').classList.add("errorlabel");
              paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
              isError = true;
          }
        } else {
          if (typeof datos.error != 'undefined') {
            fLimpiarPantalla();
            fSweetAlertEventSelect(event,datos.errorhead, datos.error + '- ' + datos.errormsg , 'error');
            event.preventDefault();
            event.target.classList.add("errortxt");
            document.getElementById(event.target.id + 'label').classList.add("errorlabel");
            paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
            isError = true;
          } else {
            fLimpiarPantalla();
            fSweetAlertEventSelect(event,'INFORMACIÓN', 'ERROR DESCONOCIDO, INTENTELO DE NUEVO SI EL ERROR PERSISTE CONTACTE AL ADMINISTRADOR DEL SISTEMA', 'error');
            event.preventDefault();
            //event.target.select();
            event.target.classList.add("errortxt");
            document.getElementById(event.target.id + 'label').classList.add("errorlabel");
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
        document.getElementById(event.target.id + 'label').classList.add("errorlabel");
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
            document.getElementById('telapoderado').value = datos[1].tel_solicitante;                
            document.getElementById('emailsoli').value = datos[1].correo_solicitante; 
            document.getElementById('numescritura').value = datos[1].Numero_Escritura;
            document.getElementById('fecha').value = datos[1].Fecha_Escritura;
            document.getElementById('lugarcons').value = datos[1].Lugar_Escritura;
            document.getElementById('rtnnotario').value = datos[1].ID_Notario;
            document.getElementById('nombrenotario').value = datos[1].Notario;    
            document.getElementById('tiposolicitante').value = datos[1].DESC_Solicitante;  
            if (datos[1].Departamento != null && datos[1].Departamento != '') {
              document.getElementById('Departamentos').value= datos[1].Departamento;
            }else{
              document.getElementById('Departamentos').value= '-1';
            }
            console.log(datos[3]);
            fLlenarSelect('Municipios',datos[3],datos[1].Municipio,false,{Text: 'SELECCIONE UN MUNICIPIO', Value: '-1'})            
            console.log(datos[4]);
            fLlenarSelect('Aldeas',datos[4],datos[1].aldea,disabled=false,{Text: 'SELECCIONE UNA ALDEA', Value: '-1'})            
            isError = false;
            //Moviendose al siguiente input
            moveToNextInput(event.target,0);
          } else {
              fLimpiarPantalla();
              fSweetAlertEventSelect(event,'INFORMACIÓN', 'EL RTN DEL SOLICITANTE NO EXISTE EN NUESTRA BASE DE DATOS, FAVOR VERIFIQUE EL NÚMERO E INTENTELO NUEVAMENTE', 'warning');
              event.preventDefault();
              event.target.classList.add("errortxt");
              document.getElementById(event.target.id + 'label').classList.add("errorlabel");
              paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
              isError = true;
          }
        } else {
          if (typeof datos.error != 'undefined') {
            fLimpiarPantalla();
            fSweetAlertEventSelect(event, datos.errorhead , datos.error + '- ' + datos.errormsg , 'error');
            event.preventDefault();
            event.target.classList.add("errortxt");
            document.getElementById(event.target.id + 'label').classList.add("errorlabel");
            paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
            isError = true;
          } else {
            fLimpiarPantalla();
            fSweetAlertEventSelect(event,'INFORMACIÓN', 'ERROR DESCONOCIDO, INTENTELO DE NUEVO EN UN MOMENTO, SI EL ERROR PERSISTE CONTACTE AL ADMINISTRADOR DEL SISTEMA', 'error');
            event.preventDefault();
            //event.target.select();
            event.target.classList.add("errortxt");
            document.getElementById(event.target.id + 'label').classList.add("errorlabel");
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
        document.getElementById(event.target.id + 'label').classList.add("errorlabel");
        paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
        isError = true;
      });
  }        
          
  function f_CaseFetchCalls(value,event,idinput) {
    //*********************************************************************************/
    // Dependiendo del panel actual se ejecuta una función para validar los campos
    //*********************************************************************************/
    switch (currentstep)
    {
      case 0: f_FetchCallApoderado(value,event,idinput); break;
      case 1: f_FetchCallSolicitante(value,event,idinput); break;
      case 2: f_FetchCallApoderado(value,event,idinput); break;
      case 3: f_FetchCallApoderado(value,event,idinput); break;
      case 4: f_FetchCallApoderado(value,event,idinput); break;
      default: f_FetchCallApoderado(value,event,idinput); break;
    }
  }        

  //Cuando presente el nuevo panel
  stepperFormEl.addEventListener('shown.bs-stepper', function (event) {
    currentstep = event.detail.indexStep;
    setFocus = true;
    isError = false;
    isPrevious = false;

    if (typeof isDirty[currentstep] != 'undefined') {
      console.log('shown.bs-stepper'+currentstep)
      isDirty[currentstep] == false;
    } 

    switch (currentstep)
    {
      case 0: document.getElementById("colapoderado").focus(); break;
      case 1: document.getElementById("rtnsoli").focus();  break;
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
      input.addEventListener('change', function(event) {
        if (event.target.getAttribute('data-valor') > event.target.value) {
          event.preventDefault();
          event.target.classList.add("errortxt");
          document.getElementById(event.target.id + 'label').classList.add("errorlabel");
          var labelid=event.target.id + 'labelerror';
          let labelExists = document.getElementById(labelid) !== null;
          if (labelExists) {
            document.getElementById(event.target.id + 'labelerror').style.visibility = "visible";
          }
          paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
        } else{
          event.target.classList.remove("errortxt");
          document.getElementById(event.target.id + 'label').classList.remove("errorlabel");
          var labelid=event.target.id + 'labelerror';
          console.log('labelid'+labelid);
          let labelExists = document.getElementById(labelid) !== null;
          console.log('labelExists'+labelExists);
          if (labelExists) {
            document.getElementById(event.target.id + 'labelerror').style.visibility = "hidden";
          }
          paneerror[currentstep][idinputs.indexOf(idinput)] = 0;
          //Moverse al siguiente input
          moveToNextInput(input,0);
          if (event.target.id=='Departamentos') {
            fCargarDwd('get-municipios',event.target.value,'Municipios',-1,{Text: 'SELECCIONE UN MUNICIPIO', Value: '-1'},['Aldeas'],['<option selected value="-1">SELECCIONE UNA ALDEA</option>']);
          } else {
            if (event.target.id=='Municipios') {
              fCargarDwd('get-aldeas',event.target.value,'Aldeas',-1,{Text: 'SELECCIONE UNA ALDEA', Value: '-1'})
            }
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
    console.log('change'+event.target.id);
    console.log('idinputs.indexOf(event.target.id)] '+ idinputs.indexOf(event.target.id));
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
      document.getElementById(event.target.id + 'label').classList.add("errorlabel");
      document.getElementById(event.target.id + 'labelerror').style.visibility = "visible";
      var labelid=event.target.id + 'labelerror';
      let labelExists = document.getElementById(labelid) !== null;
      if (labelExists) {
        document.getElementById(event.target.id + 'labelerror').style.visibility = "visible";
      }
      paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
    } else {
      event.target.classList.remove("errortxt");
      document.getElementById(event.target.id + 'label').classList.remove("errorlabel");
      document.getElementById(event.target.id + 'labelerror').style.visibility = "hidden";
      var labelid=event.target.id + 'labelerror';
      let labelExists = document.getElementById(labelid) !== null;
      if (labelExists) {
        document.getElementById(event.target.id + 'labelerror').style.visibility = "hidden";
      }
      paneerror[currentstep][idinputs.indexOf(idinput)] = 0;
      //Moverse al siguiente input
      if (idinput=='colapoderado' || idinput=='rtnsoli') {
        console.log('change');
        console.log('currentstep',currentstep);
        console.log('isDirty[currentstep]',isDirty[currentstep]);
        console.log('isRecordGetted[currentstep]',isRecordGetted[currentstep]);
        console.log('event.target.value',event.target.value);
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
          document.getElementById(event.target.id + 'label').classList.add("errorlabel");
          document.getElementById(event.target.id + 'labelerror').style.visibility = "visible";
          // Marcando que hay un error en el input actual del panel actual
          paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
        } else {
          event.target.classList.remove("errortxt");
          document.getElementById(event.target.id + 'label').classList.remove("errorlabel");
          document.getElementById(event.target.id + 'labelerror').style.visibility = "hidden";
          paneerror[currentstep][idinputs.indexOf(idinput)] = 0;
          if (idinput=='colapoderado' || idinput=='rtnsoli' && event.key === 'Enter') {
            console.log('keyDown');
            console.log('currentstep',currentstep);
            console.log('isDirty[currentstep]',isDirty[currentstep]);
            console.log('isRecordGetted[currentstep]',isRecordGetted[currentstep]);
            console.log('event.target.value',event.target.value);
            if (typeof isRecordGetted[currentstep] == 'undefined' || isRecordGetted[currentstep] != event.target.value) {
              console.log('f_CaseFetchCalls');                
              f_CaseFetchCalls(value,event,idinput);
              isDirty[currentstep] == true;
            } else {
              console.log('Else f_CaseFetchCalls');                
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
//Habilitando las teclas F2 y F10 para moverse entre los paneles
//**************************************************************************************/
document.addEventListener('keydown', function(event) {
  if (event.key === 'F2') {
    event.preventDefault();
    document.getElementById('btnprevious'+currentstep).click(); // Simular un clic en el botón con el ID 'btn-f5-trigger'
  } else {
    if (event.key === 'F10') {
      event.preventDefault();
      console.log('previoclick'+currentstep);
      document.getElementById('btnnext'+currentstep).click(); // Simular un clic en el botón con el ID 'btn-f5-trigger'
      console.log('despuesclick'+currentstep);
    }
  }
});


  