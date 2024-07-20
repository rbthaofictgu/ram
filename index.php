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
    <div class="container-fluid">
        <!-- *** -->    
        <!-- Body -->
        <!-- *** -->    
        <!-- <div class="mb-4 p-3 bg-white shadow-sm"></div> -->    
        <hr>
        <hr>
        <hr>
        <div class="bg-white shadow-sm">
        <h3 class="gobierno2 fw-bolder px-3" style="text-decoration: underline;">INGRESO DE SOLICITUDES PREFORMA</h3>
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
                <span class="bs-stepper-circle"><i class="far fa-file-alt"></i></span>
                <span class="bs-stepper-label">Tramites</span>
              </button>
            </div>            
            <div class="bs-stepper-line"></div>
            <div class="step" data-target="#test-form-5">
              <button class="step-trigger" role="tab" id="stepperFormTrigger5" aria-controls="test-form-5" aria-selected="false">
                <span class="bs-stepper-circle"><i class="fas fa-bus"></i></span>
                <span class="bs-stepper-label">Unidades</span>
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
              <div id="test-form-1" role="tabpanel" class="bs-stepper-pane fade text-center dstepper-none" aria-labelledby="stepperFormTrigger1">

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                          <label id="colapoderadolabel" class="col-form-label" for="colapoderado">Colegiaci&oacute;n Apoderado Legal:</label>
                          <input pattern="^[1-9]\d{2,7}$" class="form-control form-control-sm test-controls" id="colapoderado" minlength="3" maxlength="9">
                          <div title="Número de colegiación en el Colegio de Abogados de Honduras es invalido. Debe tener de 3 a 8 digitos enteros positivos"  id="colapoderadolabelerror" style="visibility:hidden" class="errorlabel">
                            Número de colegiación invalido.
                          </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                          <label class="col-form-label" for="identidadapod">Identificación del Apoderdo Legal:</label>
                          <input readonly type="text" class="form-control form-control-sm" id="identidadapod" placeholder="" readonly="">
                        </div>
                    </div>
                    <div class="col-md-6">
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
                      <label id="emailapoderadolabel" for="emailapoderado">Correo Electrónico:</label>
                      <input  pattern='^[^\s@]+@[^\s@]+\.[^\s@]+$' type="email" class="form-control form-control-sm test-controls" id="emailapoderado" placeholder="rbthaofic@gmail.com">
                      <div id="emailapoderadolabelerror" style="visibility:hidden" class="errorlabel">
                            El correo electrónico es invalido.
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Presentación Documentos:</label>
                      <select id="tipopresentacion" class="form-control form-control-sm" style="width: 100%;" readonly>
                        <option value="CON">CONCESIONARIO</option>
                        <option selected value="APO" selected>APODERADO LEGAL</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label id="entregadocslabel">Entrega de sus Documentos:</label>
                      <select data-valor="0" id="entregadocs" class="form-control form-control-sm test-select" style="width: 100%;">
                      </select>
                    </div>
                  </div>
                </div>
                <br>                
                <button id="btnnext0" onclick="fGetInputs()" type="button" class="btn btn-primary btn-next-form">Siguiente (F10)</button>
                <br>
                <br>
              </div>

              <div id="test-form-2" role="tabpanel" class="bs-stepper-pane fade text-center dstepper-none" aria-labelledby="stepperFormTrigger2">

              <div class="row">
                  <!--DATOS DEL RTN Y TIPO SOLICITANTE-->
                  <div class="col-md-2">
                    <div class="form-group">
                      <label id="rtnsolilabel" class="col-form-label" for="rtnsoli">RNT Solicitante:</label>
                      <input pattern="^\d{14}$" type="text" class="form-control form-control-sm test-controls" id="rtnsoli" maxlength="14">
                      <div id="rtnsolilabelerror" style="visibility:hidden" class="errorlabel">
                            El RTN invalido.
                      </div>

                    </div>
                  </div>

                  <div class="col-md-2">
                    <div class="form-group">
                      <label>Tipo Solicitante:</label>
                      <input readonly type="text" class="form-control form-control-sm" id="tiposolicitante">
                    </div>
                  </div>

                  <div class="col-md-8">
                    <div class="form-group">
                      <label class="col-form-label" for="nomsoli">Nombre Completo del Solicitante:</label>
                      <input type="text" class="form-control form-control-sm" id="nomsoli" placeholder="" readonly>
                    </div>
                  </div>


                </div>              
                <div class="row">


                  <!--DATOS DEL DOMICILIO Y DENOMINACION SOCIAL-->
                  <div class="col-md-8">
                    <div class="form-group">
                      <label class="col-form-label" for="domiciliosoli">Domicilio del Solicitante:</label>
                      <input type="text" class="form-control form-control-sm" id="domiciliosoli" placeholder="" readonly>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="col-form-label" for="denominacionsoli">Denominación Social:</label>
                      <input type="text" class="form-control form-control-sm" id="denominacionsoli" placeholder="" readonly>
                    </div>
                  </div>

                </div>



                <div id="detconstitucion" >

                  <div class="row" style="display:none">
                    <!--DATOS DE LA ESCRITURA Y NOMBRE NOTARIO-->
                    <div class="col-md-2">
                      <div class="form-group">
                        <label class="col-form-label" for="numescritura">No Escritura de Constituci&oacute;n:</label>
                        <input type="text" class="form-control form-control-sm" id="numescritura" readonly>
                      </div>
                    </div>

                    <div class="col-md-2">
                      <div class="form-group">
                        <label class="col-form-label" for="rtnnotario">RTN Notario Autorizante:</label>
                        <input type="text" class="form-control form-control-sm" id="rtnnotario" readonly>
                      </div>
                    </div>

                    <div class="col-md-3">
                      <div class="form-group">
                        <label class="col-form-label" for="nombrenotario">Notario Autorizante:</label>
                        <input type="text" class="form-control form-control-sm" id="nombrenotario" readonly>
                      </div>
                    </div>

                    <!--DATOS DEL LUGAR Y FECHA DE LA CONSTITUCION-->
                    <div class="col-md-3">
                      <div class="form-group">
                        <label class="col-form-label" for="lugarcons">Lugar de Constituci&oacute;n:</label>
                        <input type="text" class="form-control form-control-sm" id="lugarcons" readonly>
                      </div>
                    </div>

                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="fecha">Fecha Constituci&oacute;n:</label>
                        <input type="text" class="form-control form-control-sm" id="fecha" readonly>
                      </div>
                    </div>

                  </div>

                </div><!--cierre div datos constitucion-->

                <div class="row">

                  <div class="col-md-4">
                    <div class="form-group">
                      <label id="Departamentoslabel">Departamento:</label>
                      <select data-valor="0"  id="Departamentos" class="form-control form-control-sm test-select" style="width: 100%;" readonly>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label id="Municipioslabel">Municipio:</label>
                      <select data-valor="0" id="Municipios" class="form-control form-control-sm test-select" style="width: 100%;" readonly>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label id="Aldeaslabel">Aldea:</label>
                      <select data-valor="0" id="Aldeas" class="form-control form-control-sm test-select" style="width: 100%;" readonly>
                      </select>
                    </div>
                  </div>

                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label id="telsolilabel" for="telsoli">Teléfono/Celular:</label>
                      <input pattern="[0-9]{8}" type="text" class="form-control form-control-sm test-controls" id="telsoli" placeholder="95614451" maxlength="8">
                    </div>
                    <div id="telsolilabelerror" style="visibility: hidden;" class="errorlabel">
                            Teléfono Invalido, debe tener  8 Digitos.
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label id="emailsolilabel" for="emailsoli">Correo Electrónico:</label>
                      <input pattern='^[^\s@]+@[^\s@]+\.[^\s@]+$'  type="email" class="form-control form-control-sm test-controls" id="emailsoli" placeholder="rbthaofic@gmail.com">
                    </div>
                    <div id="emailsolilabelerror" style="visibility: hidden;" class="errorlabel">
                            Email Invalido
                    </div>
                  </div>
                </div>
                <br>
                <button id="btnprevious1" type="button" class="btn btn-success btn-previous-form">Anterior (F2)</button>
                <button id="btnnext1" onclick="fGetInputs()" type="button" class="btn btn-primary btn-next-form">Siguiente (F10)</button>
                <br>
                <br>
              </div>
              <div id="test-form-3" role="tabpanel" class="bs-stepper-pane fade text-center dstepper-none" aria-labelledby="stepperFormTrigger3">
                <button id="btnprevious2" type="button" class="btn btn-success btn-previous-form">Anterior (F2)</button>
                <button id="btnnext2" onclick="fGetInputs()" type="button" class="btn btn-primary btn-next-form">Siguiente (F10)</button>
              </div>
              <div id="test-form-4" role="tabpanel" class="bs-stepper-pane fade text-center dstepper-none" aria-labelledby="stepperFormTrigger4">
                <button id="btnprevious3" type="button" class="btn btn-success btn-previous-form">Anterior (F2)</button>
                <button id="btnnext3" onclick="fGetInputs()" type="button" class="btn btn-primary btn-next-form">Siguiente (F10)</button>
              </div>
              <div id="test-form-5" role="tabpanel" class="bs-stepper-pane fade text-center dstepper-none" aria-labelledby="stepperFormTrigger5">
                <button id="btnprevious4" type="button" class="btn btn-success btn-previous-form">Anterior (F2)</button>
                <button id="btnnext4" onclick="fGetInputs()" type="button" class="btn btn-primary btn-next-form">Siguiente (F10)</button>
              </div>              
              <div id="test-form-6" role="tabpanel" class="bs-stepper-pane fade text-center dstepper-none" aria-labelledby="stepperFormTrigger6">
                <button id="btnprevious5" type="button" class="btn btn-success btn-previous-form">Anterior (F2)</button>
                <button id="btnnext5" onclick="fGetInputs()" type="button" class="btn btn-primary btn-next-form">SALVAR Y CERRAR (F10)</button>
              </div>
          </div>
        </div>
      </div>
        <!-- *** -->    
        <!-- Body -->
        <!-- *** -->    
    </div>
    <div class="bottom">
      <footer class="d-flex flex-wrap justify-content-between align-items-center py-1 my-1">
          <?php include_once('footer.php')?>
      </footer>
    </div>
    <script src="<?php echo $appcfg_Dominio_Corto;?>tools/bootstrap-5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo $appcfg_Dominio_Corto;?>tools/bootstrap-5.3.2/site/static/docs/5.3/assets/js/validate-forms.js"></script>
    <script src="<?php echo $appcfg_Dominio;?>assets/js/fetchWithTimeout.js"></script>
    <script src="<?php echo $appcfg_Dominio;?>assets/js/sweetalert.js"></script>
    <script src="<?php echo $appcfg_Dominio;?>assets/js/readingBar.js"></script>
    <script src="<?php echo $appcfg_Dominio;?>assets/js/fLlenarSelect.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bs-stepper/dist/js/bs-stepper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.3/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.4/dist/sweetalert2.all.min.js"></script>
    <link   href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.4/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://kit.fontawesome.com/d40661685b.js" ></script>
    <script src="<?php echo $appcfg_Dominio;?>assets/js/index.js"></script>
  </body>
</html>