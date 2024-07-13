<?php 
//? PÁGINA PRINCIPAL DEL SISTEMA
session_start();
//*configuración del sistema
include_once('configuracion/configuracion.php');
//*configuración del las variables globales del sistema
include_once('configuracion/configuracion_js.php');
?>
<!doctype html>
<html lang="en" data-bs-theme="auto">
  <head>
    <title>RENOVACIONES MASIVAS</title>
    <meta charset="utf-8">
    <meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="SISTEMA DE RENOVACIONES AUTOMATICAS MASIVAS">
    <meta name="author" content="RONALD THAOFIC BARRIENTOS MEJIA">
    <link rel="apple-touch-icon" sizes="120x120" href="<?php echo $appcfg_Dominio;?>assets/images/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $appcfg_Dominio;?>assets/images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $appcfg_Dominio;?>assets/images/favicon/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="512x512" href="<?php echo $appcfg_Dominio;?>assets/images/favicon/android-chrome-512x512.png">
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo $appcfg_Dominio;?>assets/images/favicon/android-chrome-192x192.png">
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.3/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="<?php echo $appcfg_Dominio_Corto;?>tools/bootstrap-5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <link href="https://cdn.jsdelivr.net/npm/bs-stepper/dist/css/bs-stepper.min.css" rel="stylesheet"> 
    <link href="<?php echo $appcfg_Dominio;?>assets/css/styles.css" rel="stylesheet">
  </head>
    <body>
    <header>
      <?php include_once('menu.php')?>
    </header>
    <div class="container-fluid" style="border:">
        <!-- *** -->    
        <!-- Body -->
        <!-- *** -->    
        <!-- <div class="mb-4 p-3 bg-white shadow-sm"></div> -->    
        <div class="bg-white shadow-sm">
        <h3 class="gobierno1 fw-bolder px-3" style="text-decoration: underline;">INGRESO DE SOLICITUDES PREFORMA</h3>
        <div id="stepperForm" class="bs-stepper linear">
          <div class="bs-stepper-header" role="tablist">
            <div class="step" data-target="#test-form-1">
              <button class="step-trigger" role="tab" id="stepperFormTrigger1" aria-controls="test-form-1" aria-selected="true" disabled="disabled">
                <span class="bs-stepper-circle"><i class="fas fa-gavel"></i></span>
                <span class="bs-stepper-label">Apoderado Legal</span>
              </button>
            </div>
            <div class="bs-stepper-line"></div>
            <div class="step active" data-target="#test-form-2">
              <button class="step-trigger" role="tab" id="stepperFormTrigger2" aria-controls="test-form-2" aria-selected="false">
                <span class="bs-stepper-circle"><i class="fas fa-user-edit"></i></span>
                <span class="bs-stepper-label">Solicitante</span>
              </button>
            </div>
            <div class="bs-stepper-line"></div>
            <div class="step" data-target="#test-form-3">
              <button class="step-trigger" role="tab" id="stepperFormTrigger3" aria-controls="test-form-3" aria-selected="false">
                <span class="bs-stepper-circle"><i class="fas fa-certificate"></i></span>
                <span class="bs-stepper-label">Conceciones</span>
              </button>
            </div>
            <div class="bs-stepper-line"></div>
            <div class="step" data-target="#test-form-4">
              <button class="step-trigger" role="tab" id="stepperFormTrigger4" aria-controls="test-form-4" aria-selected="false">
                <span class="bs-stepper-circle"><i class="fas fa-bus"></i></span>
                <span class="bs-stepper-label">Unidades</span>
              </button>
            </div>            
            <div class="bs-stepper-line"></div>
            <div class="step" data-target="#test-form-5">
              <button class="step-trigger" role="tab" id="stepperFormTrigger5" aria-controls="test-form-5" aria-selected="false">
                <span class="bs-stepper-circle"><i class="far fa-file-alt"></i></span>
                <span class="bs-stepper-label">Tramites</span>
              </button>
            </div>                        
            <div class="bs-stepper-line"></div>
            <div class="step" data-target="#test-form-6">
              <button class="step-trigger" role="tab" id="stepperFormTrigger6" aria-controls="test-form-6" aria-selected="false" disabled="disabled">
                <span class="bs-stepper-circle"><i class="fas fa-flag-checkered"></i></span>
                <span class="bs-stepper-label">Finalizar</span>
              </button>
            </div>            
          </div>
          <div class="bs-stepper-content">
              <div id="test-form-1" role="tabpanel" class="bs-stepper-pane fade dstepper-block dstepper-none" aria-labelledby="stepperFormTrigger1">

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                          <label id="colapoderadolabel" class="col-form-label" for="colapoderado">Colegiaci&oacute;n Apoderado Legal:</label>
                          <input pattern="^[1-9]\d{2,7}$" class="form-control form-control-sm test-controls" id="colapoderado" minlength="3" maxlength="9">
                          <div title="Número de colegiación en el Colegio de Abogados de Honduras es invalido. Debe tener de 3 a 8 caracteres enteros positivos"  id="colapoderadolabelerror" style="visibility:hidden" class="errorlabel">
                            Número de colegiación invalido. Debe tener de 3 a 8 dígitos.
                          </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                          <label class="col-form-label" for="identidadapod">Identificación del Apoderdo Legal:</label>
                          <input readonly type="text" class="form-control form-control-sm" id="identidadapod" placeholder="" readonly="">
                        </div>
                    </div>

                </div>              

                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="col-form-label" for="nomapoderado">Nombre Completo del Apoderado Legal:</label>
                      <input readonly type="text" class="form-control form-control-sm" id="nomapoderado" placeholder="" readonly="">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label id="dirapoderadolabel" class="col-form-label" for="dirapoderado">Direcci&oacute;n del Apoderado Legal:</label>
                      <input pattern="^[a-zA-Z0-9\s,.\-]{10,200}$" title="La dirección del apoderado legal no puede ser menor de 10 caraceteres ni mayor a 200" type="text" class="form-control form-control-sm test-controls" id="dirapoderado">
                      <div  id="dirapoderadolabelerror" style="visibility:hidden" class="errorlabel">
                        La dirección del apoderado legal no puede ser menor de 10 caraceteres ni mayor a 200.
                      </div>
                    </div>
                  </div>
                </div>
    
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label id="telapoderadolabel" for="telapoderado">Tel&eacute;fono del Apoderdo Legal:</label>
                      <input pattern="[0-9]{8}" type="text" class="form-control form-control-sm test-controls" id="telapoderado" minlength="8" maxlength="15" placeholder="95614451">
                      <div  id="telapoderadolabelerror" style="visibility:hidden" class="errorlabel">
                            El número de teléfono es invalido, digite solo numeros sin guiones.
                      </div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label id="emailapoderadolabel" for="emailapoderado">Correo Electr&oacute;nico:</label>
                      <input pattern='^[^\s@]+@[^\s@]+\.[^\s@]+$' type="email" class="form-control form-control-sm test-controls" id="emailapoderado" placeholder="rbthaofic@gmail.com">
                      <div id="emailapoderadolabelerror" style="visibility:hidden" class="errorlabel">
                            El correo electrónico es invalido.
                      </div>
                    </div>
                  </div>
                </div>
                
                <button onclick="fGetInputs()" type="button" class="btn btn-primary btn-next-form">Siguiente</button>

              </div>

              <div id="test-form-2" role="tabpanel" class="bs-stepper-pane fade active dstepper-block" aria-labelledby="stepperFormTrigger2">
                <div class="form-group">
                  <label id="passwordlabel" for="inputPasswordForm">Password <span class="text-danger font-weight-bold">*</span></label>
                  <input pattern="^[a-zA-Z0-9\s,.\-]{4,20}$"  id="password" type="password" class="form-control form-control-sm test-controls" placeholder="Password" required minlength="4">
                  <div id="passwordlabelerror" style="visibility:hidden" class="errorlabel">Please fill the password field</div>
                </div>
                <button type="button" class="btn btn-success btn-previous-form">Previa</button>
                <button type="button" class="btn btn-primary btn-next-form">Siguiente</button>
              </div>
              <div id="test-form-3" role="tabpanel" class="bs-stepper-pane fade text-center dstepper-none" aria-labelledby="stepperFormTrigger3">
                <button type="button" class="btn btn-success btn-previous-form">Previa</button>
                <button type="button" class="btn btn-primary btn-next-form">Siguiente</button>
              </div>
              <div id="test-form-4" role="tabpanel" class="bs-stepper-pane fade text-center dstepper-none" aria-labelledby="stepperFormTrigger4">
                <button type="button" class="btn btn-success btn-previous-form">Previa</button>
                <button type="button" class="btn btn-primary btn-next-form">Siguiente</button>
              </div>
              <div id="test-form-5" role="tabpanel" class="bs-stepper-pane fade text-center dstepper-none" aria-labelledby="stepperFormTrigger5">
                <button type="button" class="btn btn-success btn-previous-form">Previa</button>
                <button type="button" class="btn btn-primary btn-next-form">Siguiente</button>
              </div>              
              <div id="test-form-6" role="tabpanel" class="bs-stepper-pane fade text-center dstepper-none" aria-labelledby="stepperFormTrigger6">
                <button type="button" class="btn btn-success btn-previous-form">Previa</button>
                <button onclick="f_finalizar();" type="button" class="btn btn-primary">Finalizar</button>
              </div>
          </div>
        </div>
      </div>
        <!-- *** -->    
        <!-- Body -->
        <!-- *** -->    
        <div class="bottom">
            <footer class="d-flex flex-wrap justify-content-between align-items-center py-1 my-1">
                <?php include_once('footer.php')?>
            </footer>
        </div>
    </div>
    <script src="<?php echo $appcfg_Dominio_Corto;?>tools/bootstrap-5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo $appcfg_Dominio_Corto;?>tools/bootstrap-5.3.2/site/static/docs/5.3/assets/js/validate-forms.js"></script>
    <script src="<?php echo $appcfg_Dominio;?>assets/js/fetchWithTimeout.js"></script>
    <script src="<?php echo $appcfg_Dominio;?>assets/js/sweetalert.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bs-stepper/dist/js/bs-stepper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.3/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.4/dist/sweetalert2.all.min.js"></script>
    <link   href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.4/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://kit.fontawesome.com/d40661685b.js" ></script>
    <script>
      var isError = false;
      var hayerror = false;
      var idinput = '';
      var idinputs = Array();
      var currentstep;
      var stepperForm;
      var isDirty = Array();
      var hasData = Array();
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
              stepperForm.previous();
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
          // Hacel al solicitud fetch con un timeout de 2 minutos
          fetchWithTimeout(url, options, 120000)
            .then(response => response.json())
            .then(function (datos) {
              console.log(datos);
              if (typeof datos.nombre_apoderado != 'undefined') {
                if (datos.nombre_apoderado != '' && datos.nombre_apoderado != null) {
                  document.getElementById('nomapoderado').value = datos.nombre_apoderado;
                  document.getElementById('identidadapod').value = datos.ident_apoderado;
                  document.getElementById('dirapoderado').value = datos.dir_apoderado;
                  document.getElementById('telapoderado').value = datos.tel_apoderado;                
                  document.getElementById('emailapoderado').value = datos.correo_apoderado; 
                } else {
                  fLimpiarPantalla();
                  f_sweetalert('INFORMACIÓN', 'EL NÚMERO DE COLEGIACIÓN NO EXISTE EN NUESTRA BASE DE DATOS, FAVOR VERIFIQUE EL NÚMERO E INTENTELO NUEVAMENTE', 'warning');
                  event.preventDefault();
                  //event.target.select();
                  event.target.classList.add("errortxt");
                  document.getElementById(event.target.id + 'label').classList.add("errorlabel");
                  paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
                  isError = true;
                }                  
              } else {
                fLimpiarPantalla();
                f_sweetalert('INFORMACIÓN', 'EL NÚMERO DE COLEGIACIÓN NO EXISTE EN NUESTRA BASE DE DATOS, FAVOR VERIFIQUE EL NÚMERO E INTENTELO NUEVAMENTE', 'warning');
                event.preventDefault();
                //event.target.select();
                event.target.classList.add("errortxt");
                document.getElementById(event.target.id + 'label').classList.add("errorlabel");
                paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
                isError = true;
              }
            })
            .catch((error) => {
              fLimpiarPantalla();
              console.error('Error Catch f_FetchCallApoderado:', error);
              f_sweetalert('CONEXÍON', 'Algo raro paso. Intentandolo de nuevo en un momento, si el problema persiste contacta al administrador del sistema', 'warning');
              event.preventDefault();
              //event.target.select();
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
            case 1: f_FetchCallApoderado(value,event,idinput); break;
            case 2: f_FetchCallApoderado(value,event,idinput); break;
            case 3: f_FetchCallApoderado(value,event,idinput); break;
            case 4: f_FetchCallApoderado(value,event,idinput); break;
            default: f_FetchCallApoderado(value,event,idinput); break;
          }
        }        

        //Cuando presente el nuevo panel
        stepperFormEl.addEventListener('shown.bs-stepper', function (event) {
          currentstep = event.detail.indexStep;
        })

        //Antes de hacer la transición al nuevo panel
        stepperFormEl.addEventListener('show.bs-stepper', function (event) {
          //*********************************************************************************/
          // Dependiendo del panel actual se ejecuta una función para validar los campos
          //*********************************************************************************/
          // switch (currentstep)
          // {
          //   case 0: paneerror[currentstep] = f_validarapoderado(); break;
          //   case 1: paneerror[currentstep] = f_validarapoderado(); break;
          //   case 2: paneerror[currentstep] = f_validarapoderado(); break;
          //   case 3: paneerror[currentstep] = f_validarapoderado(); break;
          //   case 4: paneerror[currentstep] = f_validarapoderado(); break;
          //   default: paneerror[currentstep] = f_validarapoderado(); break;
          // }

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
        })      

        setTimeout(setFocusElement, 250);

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
          // Marcando que no hay error aun
          isError == false
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
            event.target.select();
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
              moveToNextInput(input,0);
              if (idinput=='colapoderado') {
                f_CaseFetchCalls(value,event,idinput);
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
              event.target.select();
              event.target.classList.add("errortxt");
              document.getElementById(event.target.id + 'label').classList.add("errorlabel");
              document.getElementById(event.target.id + 'labelerror').style.visibility = "visible";
              paneerror[currentstep][idinputs.indexOf(idinput)] = 1;
            } else {
              event.target.classList.remove("errortxt");
              document.getElementById(event.target.id + 'label').classList.remove("errorlabel");
              document.getElementById(event.target.id + 'labelerror').style.visibility = "hidden";
              paneerror[currentstep][idinputs.indexOf(idinput)] = 0;
              //Mover al siguiente input
              if (event.key === 'Enter') {
                moveToNextInput(input,0);
              }
              if (idinput=='colapoderado') {
                f_CaseFetchCalls(value,event,idinput);
              }
           	}
          }
          // Marcando que no hay error aun
          isError == false
        });        

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

      });
    })      

    function fGetInputs() {
        console.log(currentstep);
        // Get the element by its ID
        var element = document.getElementById('test-form-'+(currentstep+1));
        
        // Get all input elements inside this element
        var inputs = element.querySelectorAll('.test-controls');
        console.log(inputs);
        // Convert NodeList to Array for easier manipulation (optional)
        inputs = Array.from(inputs);
        
        // Log the inputs to the console
        inputs.forEach(input => {
           // Creando un nuevo evento 'change'
           var event = new Event('change', {
                'bubbles': true,
                'cancelable': true
            });

            // Despachando el evento
            input.dispatchEvent(event);
        });
    }

  </script> 
  </body>
</html>