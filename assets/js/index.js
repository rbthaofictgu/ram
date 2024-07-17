var isError = false;
var isPrevious = false;
var hayerror = false;
var idinput = '';
var idinputs = Array();
var currentstep;
var stepperForm;
var isDirty = Array();
var hasData = Array();
var setFocus = true;
// Pocicionando el cursos en el primer input de la pantalla id=colapoderado
function setFocusElement(){
  document.getElementById("colapoderado").focus(); 
}

document.addEventListener('DOMContentLoaded', function () {
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
  //*****************************************************************/
  // Marcando false al arreglo de isDirty y hasData
  //*****************************************************************/
  for (var i = 0; i < stepperPanList.length; i++) {
    isDirty[i] = false;
    hasData[i] = false;
  }

  btnNextList.forEach(function (btn) {
    btn.addEventListener('click', function () {
        stepperForm.next();
    })
  })

  //************************************************************/
  //Moviendose al siguiente input
  //************************************************************/
  function moveToNextInput(currentInput,value) {
    var inputs = [].slice.call(document.querySelectorAll('.test-controls'));
    var currentIndex = inputs.indexOf(currentInput);
    for (var i = ((currentIndex + 1)+ (value)); i < inputs.length; i++) {
      if (!inputs[i].disabled) {
        inputs[i].focus();
        inputs[i].select();
        break;
      }
    }
  }


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
        document.getElementById('nomapoderado').value = '';
        document.getElementById('identidadapod').value = '';
        document.getElementById('dirapoderado').value = '';
        document.getElementById('telapoderado').value = '';                
        document.getElementById('emailapoderado').value = ''; 
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
    fd.append("echo", true);          
    // Fetch options
    const options = {
      method: 'POST',
      body: fd,
    };
    // Hacel al solicitud fetch con un timeout de 2 minutos
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
            //Moviendose al sigueinte input
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
            fSweetAlertEventSelect(event,'INICIO DE SESSION', datos.error + '- ' + datos.errormsg , 'error');
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
    fd.append("echo", true);          
    // Fetch options
    const options = {
      method: 'POST',
      body: fd,
    };
    // Hacel al solicitud fetch con un timeout de 2 minutos
    fetchWithTimeout(url, options, 120000)
      .then(response => response.json())
      .then(function (datos) {
        console.log(datos);
        console.log(datos[0]);
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
            fLlenarSelect('entregadocs',datos[3],null,false,{Text: 'SELECCIONE UN LUGAR DE ENTREGA', Value: '-1'})            
            fLlenarSelect('Departamentos',datos[4],datos[1].Departamento,false,{Text: 'SELECCIONE UN DEPARTAMENTO', Value: '-1'})            
            fLlenarSelect('Municipios',datos[5],datos[1].Municipio,false,{Text: 'SELECCIONE UN MUNICIPIO', Value: '-1'})            
            fLlenarSelect('Aldeas',datos[6],datos[1].aldea,disabled=false,{Text: 'SELECCIONE UNA ALDEA', Value: '-1'})            
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
            fSweetAlertEventSelect(event,'INICIO DE SESSION', datos.error + '- ' + datos.errormsg , 'error');
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
      } else {
        // Setting the panel isDirty
        isDirty = true;
      }
    } else {
      isError = false;
    }
  })      


    var testcontrols = [].slice.call(document.querySelectorAll('.test-controls'));
    var columnas = testcontrols.length;
    var filas = stepperPanList.length;
    // Crear una matriz bidimensional vacía
    var paneerror = new Array(filas);
    testcontrols.forEach(function(input) {
      //********************************************************************/
      //Definiendo el arreglo de errores por panel e input
      //********************************************************************/
      for (var i = 0; i < filas; i++) {
        paneerror[i] = new Array(columnas);
      }
      //*****************************************************************************/
      //Creando arreglo de inputs para saber posteriormente el indice de cada input
      //*****************************************************************************/
      idinputs.push(input.id);
      //*****************************************************************************/
      //Creando evento change para cada input
      //*****************************************************************************/
      input.addEventListener('change', function(event) {
      // Estableciento si el panel actual isDirty
      isDirty[currentstep] = true;
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
        } else {
          setFocus = true;
        }
        event.target.classList.add("errortxt");
        document.getElementById(event.target.id + 'label').classList.add("errorlabel");
        document.getElementById(event.target.id + 'labelerror').style.visibility = "visible";
        paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
      } else {
          event.target.classList.remove("errortxt");
          document.getElementById(event.target.id + 'label').classList.remove("errorlabel");
          document.getElementById(event.target.id + 'labelerror').style.visibility = "hidden";
          paneerror[currentstep][idinputs.indexOf(idinput)] = 0;
          //Moverse al siguiente input
          if (idinput=='colapoderado' || idinput=='rtnsoli') {
            f_CaseFetchCalls(value,event,idinput);
          } else {
            moveToNextInput(input,0);
          }
      }
 });

  // Handle the keydown event to prevent Tab key from moving focus
  input.addEventListener('keydown', function(event) {
    // Estableciento si el panel actual isDirty
    isDirty[currentstep] = true;
    // Obteniendo el id del input
    idinput = event.target.id;
    // Verificar si se presionó la tecla Tab o Enter
    if (event.key === 'Tab' || (event.key === 'Enter' && isError == false)) {
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
        if (idinput=='colapoderado' || idinput=='rtnsoli') {
          f_CaseFetchCalls(value,event,idinput);
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

});

setTimeout(setFocusElement, 250);

})      

function fGetInputs() {
  setFocus = false;
  // Get the element by its ID
  var element = document.getElementById('test-form-'+(currentstep+1));
  // Get all input elements inside this element
  var inputs = element.querySelectorAll('.test-controls');
  // Convert NodeList to Array for easier manipulation (optional)
  inputs = Array.from(inputs);
  // Iterando entre los inputs para despachar el evento change y validar la data de cada input
  inputs.forEach(input => {
      // Creando un nuevo evento 'change'
      var event = new Event('change', {
          'bubbles': true,
          'cancelable': true
      });
      // Despachando el evento
      input.dispatchEvent(event);
  });
  setFocus = true;
}

//**************************************************************************************/
//Eliminar todos los mensajes de error
//**************************************************************************************/
function fCleanErrorMsg() {
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
    document.getElementById(input.id + 'labelerror').style.visibility = "hidden";
  });
}


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
    document.getElementById('btnnext'+currentstep).click(); // Simular un clic en el botón con el ID 'btn-f5-trigger'
  }
}
});