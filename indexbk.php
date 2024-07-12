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
    <link href="<?php echo $appcfg_Dominio;?>css/styles.css" rel="stylesheet">
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
            <form class="needs-validation was-validated"  novalidate>
              <div id="test-form-1" role="tabpanel" class="bs-stepper-pane fade dstepper-block dstepper-none" aria-labelledby="stepperFormTrigger1">
                <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="col-form-label" for="colapoderado">Colegiaci&oacute;n Apoderado Legal:</label>
                          <input type="num" class="form-control form-control-lg" id="colapoderado" placeholder="Número de carnet de colegiación al CAH" minlength="3" maxlength="8">
                          <div class="invalid-feedback">
                            El número de carnet de colegiación es requerido y debe tener un mínimo de 3 caracteres y un máximo de 8 caracteres.
                        </div>
                        </div>
                      </div>
                    <div class="col-md-6">
                        <div class="form-group">
                          <label class="col-form-label" for="identidadapod">Identificación del Apoderdo Legal:</label>
                          <input type="text" class="form-control form-control-lg" id="identidadapod" placeholder="" readonly="">
                        </div>
                    </div>
                </div>              

                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="col-form-label" for="nomapoderado">Nombre Completo del Apoderado Legal:</label>
                      <input type="text" class="form-control form-control-lg" id="nomapoderado" placeholder="" readonly="">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="col-form-label" for="dirapoderado">Direcci&oacute;n del Apoderado Legal:</label>
                      <input type="text" class="form-control form-control-lg" id="dirapoderado" placeholder="" readonly="">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="telapoderado">Tel&eacute;fono del Apoderdo Legal:</label>
                      <input type="text" class="form-control form-control-lg" id="telapoderado" placeholder="" minlength="4" maxlength="8">
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="emailapoderado">Correo Electr&oacute;nico:</label>
                      <input type="email" class="form-control form-control-lg" id="emailapoderado" placeholder="">
                    </div>
                  </div>
                </div>
                <br>
                <button type="button" class="btn btn-primary btn-next-form">Siguiente</button>
              </div>
            </form>


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
                <button type="submit" class="btn btn-success btn-previous-form">Previa</button>
                <button type="submit" class="btn btn-primary btn-next-form">Siguiente</button>
              </div>
              <div id="test-form-4" role="tabpanel" class="bs-stepper-pane fade text-center dstepper-none" aria-labelledby="stepperFormTrigger4">
                <button type="submit" class="btn btn-success btn-previous-form">Previa</button>
                <button type="submit" class="btn btn-primary btn-next-form">Siguiente</button>
              </div>
              <div id="test-form-5" role="tabpanel" class="bs-stepper-pane fade text-center dstepper-none" aria-labelledby="stepperFormTrigger5">
                <button type="submit" class="btn btn-success btn-previous-form">Previa</button>
                <button type="submit" class="btn btn-primary btn-next-form">Siguiente</button>
              </div>              
              <div id="test-form-6" role="tabpanel" class="bs-stepper-pane fade text-center dstepper-none" aria-labelledby="stepperFormTrigger6">
                <button type="submit" class="btn btn-success btn-previous-form mt-5">Previa</button>
                <button type="submit" class="btn btn-primary btn-next-form mt-5">Finalizar</button>
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
      var stepperForm
      var formisvalid = false;
      var movimiento = 'next';
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

        stepperFormEl.addEventListener('shown.bs-stepper', function (event) {
          console.warn('shown'+event.detail.indexStep);
          alert('shown.bs-stepper'+formisvalid);
        })

        stepperFormEl.addEventListener('show.bs-stepper', function (event) {

          form.classList.remove('was-validated')

          var nextStep = event.detail.indexStep
          var currentStep = nextStep
          console.warn('show'+event.detail.indexStep);
          if (currentStep > 0) {
            currentStep--
          }

          var stepperPan = stepperPanList[currentStep]

          alert(currentStep);
          alert('show.bs-stepper'+formisvalid);
          

        })      
      })      

    </script> 
    </body>
</html>