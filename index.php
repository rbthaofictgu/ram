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
    <div class="container">
        <header>
            <?php include_once('menu.php')?>
        </header>
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
                          <input disabled type="text" class="form-control form-control-sm" id="identidadapod" placeholder="" readonly="">
                        </div>
                    </div>

                </div>              

                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="col-form-label" for="nomapoderado">Nombre Completo del Apoderado Legal:</label>
                      <input disabled type="text" class="form-control form-control-sm" id="nomapoderado" placeholder="" readonly="">
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
                      <input pattern="[0-9]{8}" type="text" class="form-control form-control-sm test-controls" id="telapoderado" minlength="8" maxlength="8" placeholder="95614451">
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
                
                <button type="button" class="btn btn-primary btn-next-form">Siguiente</button>

              </div>

              <div id="test-form-2" role="tabpanel" class="bs-stepper-pane fade active dstepper-block" aria-labelledby="stepperFormTrigger2">
                <div class="form-group">
                  <label for="inputPasswordForm">Password <span class="text-danger font-weight-bold">*</span></label>
                  <input id="inputPasswordForm" type="password" class="form-control" placeholder="Password" required minlength="4">
                  <div class="invalid-feedback">Please fill the password field</div>
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
    <script src="https://cdn.jsdelivr.net/npm/bs-stepper/dist/js/bs-stepper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.3/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.4/dist/sweetalert2.all.min.js"></script>
    <link   href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.4/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://kit.fontawesome.com/d40661685b.js" ></script>
    <script>
      var hayerror = false;
      var idinput = '';
      var idinputs = Array();
      var currentstep;
      var stepperForm;
      var dirty = false;

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


        btnNextList.forEach(function (btn) {
          btn.addEventListener('click', function () {
              stepperForm.next();
          })
        })


        btnNextListprevious.forEach(function (btn) {
          btn.addEventListener('click', function () {
              stepperForm.previous();
          })
        })

        //Cuando presente el nuevo panel
        stepperFormEl.addEventListener('shown.bs-stepper', function (event) {
          currentstep = event.detail.indexStep;
        })

        //Antes de hacer la transición al nuevo panel
        stepperFormEl.addEventListener('show.bs-stepper', function (event) {
          let error = false;
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
            // Setting the panel dirty
            dirty = true;
          }
        })      

        setTimeout(setFocusElement, 250);

        function f_validarapoderado() {
          return 0;
        }

        var testcontrols = [].slice.call(document.querySelectorAll('.test-controls'));
        var columnas = testcontrols.length;
        var filas = stepperPanList.length;
        // Crear una matriz bidimensional vacía
        var paneerror = new Array(filas);
        testcontrols.forEach(function(input) {
          for (var i = 0; i < filas; i++) {
            paneerror[i] = new Array(columnas);
          }
          idinputs.push(input.id);
          input.addEventListener('change', function(event) {
          // Setting the panel dirty
          dirty = true;
          // Getting the input element id
          idinput = event.target.id;
          // Save cursor position
          var cursorPosition = event.target.selectionStart;
          // Get the current value of the input
          var value = event.target.value;
          // Get the pattern from the 'pattern' attribute of the target element
          var patternString = event.target.pattern;
          // Create a RegExp instance using the pattern
          var pattern = new RegExp(patternString);
          // Check if the current input value is valid
          var isValid = pattern.test(value);
          // If the value is not valid, prevent the default behavior and restore the cursor position
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
            moveToNextInput(input);
          }
        });

        // Handle the keydown event to prevent Tab key from moving focus
        input.addEventListener('keydown', function(event) {
          // Setting the panel dirty
          dirty = true;
          // Getting the input element id
          idinput = event.target.id;
          // Check if the Tab key is pressed
          if (event.key === 'Tab' || event.key === 'Enter') {
            // Get the current value of the input
            var value = event.target.value;
            // Get the pattern from the 'pattern' attribute of the target element
            var patternString = event.target.pattern;
            // Create a RegExp instance using the pattern
            var pattern = new RegExp(patternString);
            // Check if the current input value is valid
            var isValid = pattern.test(value);
            // If the value is not valid, prevent the default behavior
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
                moveToNextInput(input);
              }
           	}
          }
        });        

        function moveToNextInput(currentInput) {
          var inputs = [].slice.call(document.querySelectorAll('.test-controls'));
          var currentIndex = inputs.indexOf(currentInput);
          for (var i = currentIndex + 1; i < inputs.length; i++) {
            if (!inputs[i].disabled) {
              inputs[i].focus();
              inputs[i].select();
              break;
            }
          }
        }

      });
    })      
  </script> 
  </body>
</html>