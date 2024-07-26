<?php 
//* PÁGINA PRINCIPAL DEL SISTEMA
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
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
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
    <!--<input id="dominio_raiz" name="dominio_raiz" type="hidden" value=""> -->
    <div class="container-fluid bg-white shadow-sm">
        <!-- *** -->    
        <!-- Body -->
        <!-- *** -->    
        <!-- <div class="mb-4 p-3 bg-white shadow-sm"></div> -->    
        <hr>
        <hr>
        <hr>
      <div class="row">
        <div class="col-md-10">
          <h6 style="font-size: 1.25rem;" class="gobierno2 fw-bolder px-1" style="text-decoration: underline;font-weight: 400;"><i class="fas fa-edit gobierno1"></i>&nbsp;INGRESO DE SOLICITUDES PREFORMA  
        </div>
        <div class="col-md-2 justify-content-rigth">
          <button style="display: none;" id="addConcesion" type="button" class="btn btn-success">
            <i title="Agregar una nueva concesion y/o verificar si una concesion ya fue ingresada a esta solicitud" 
            class="fa-solid fa-plus fa-x2"></i>Concesión
          </button> 
          </h6>
        </div>
        </div>
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
                <span class="bs-stepper-label">Concesiones</span>
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
              <button class="step-trigger" role="tab" id="stepperFormTrigger5" aria-controls="test-form-5" aria-selected="false" disabled="disabled">
                <span class="bs-stepper-circle"><i class="fas fa-flag-checkered"></i></span>
                <span class="bs-stepper-label">Finalizar</span>
              </button>
            </div>            
          </div>

          <div class="bs-stepper-content">
            <div id="test-form-1" role="tabpanel" class="bs-stepper-pane fade text-left dstepper-none" aria-labelledby="stepperFormTrigger1">
            <hr>
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
                    <label id="entregadocslabel">Entrega de sus Documentos:</label>
                    <select data-valor="0" id="entregadocs" class="form-control form-control-sm test-select" style="width: 100%;">
                    </select>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group">
                    <label id="telapoderadolabel" for="telapoderado">Teléfono del Apoderdo Legal:</label>
                    <input pattern="[0-9]{8}" type="text" class="form-control form-control-sm test-controls" id="telapoderado" minlength="8" maxlength="15" placeholder="95614451">
                    <div  id="telapoderadolabelerror" style="visibility:hidden" class="errorlabel">
                          El número de teléfono es invalido, debe tener 8 digitos.
                    </div>
                  </div>
                </div>

                <div class="col-md-3">
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

                <div class="col-md-3">
                    <div class="form-group">
                      <label class="col-form-label" for="identidadapod">Documento de Identificación</label>
                      <input readonly type="text" class="form-control form-control-sm" id="identidadapod" placeholder="" readonly="">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                      <label class="col-form-label" for="nomapoderado">Nombre Completo del Apoderado Legal:</label>
                      <input readonly type="text" class="form-control form-control-sm" id="nomapoderado" placeholder="" readonly="">
                    </div>
                  </div>

                  <div class="col-md-3">
                  <div class="form-group">
                    <label id="dirapoderadolabel" class="col-form-label" for="dirapoderado">Direcci&oacute;n del Apoderado Legal:</label>
                    <input pattern="^[a-zA-Z0-9\s,.\-]{10,200}$" title="La dirección del apoderado legal no puede ser menor de 10 caraceteres ni mayor a 200" type="text" class="form-control form-control-sm test-controls" id="dirapoderado">
                    <div  id="dirapoderadolabelerror" style="visibility:hidden" class="errorlabel">
                        No puede ser menor de 10 caraceteres ni mayor a 200.
                    </div>
                  </div>
                </div>


                <div class="col-md-3">
                  <div class="form-group">
                    <label>Presentación Documentos:</label>
                    <select id="tipopresentacion" class="form-control form-control-sm" style="width: 100%;" readonly>
                      <option value="CON">CONCESIONARIO</option>
                      <option selected value="APO" selected>APODERADO LEGAL</option>
                    </select>
                  </div>
                </div>


              </div>

              <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-2"></div>
                <div class="col-md-2"></div>
                <div class="col-md-2"></div>
                <div class="col-md-2"></div>                  
                <div class="col-md-2">
                  <button id="btnnext0"  type="button" class="btn btn-primary btn-next-form"><i class="fa-solid fa-arrow-right"></i> Siguiente (F10)</button>
                </div>                  
              </div>                  

              <br>
              <br>
            </div>

            <div id="test-form-2" role="tabpanel" class="bs-stepper-pane readonly dstepper-none" aria-labelledby="stepperFormTrigger2">
              <hr>
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

                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="col-form-label" for="nomsoli">Nombre Completo del Solicitante:</label>
                      <input type="text" class="form-control form-control-sm" id="nomsoli" placeholder="" readonly>
                    </div>
                  </div>

                  <!--DATOS DEL DOMICILIO Y DENOMINACION SOCIAL-->
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="col-form-label" for="domiciliosoli">Domicilio del Solicitante:</label>
                      <input type="text" class="form-control form-control-sm" id="domiciliosoli" placeholder="" readonly>
                    </div>
                  </div>


              </div>              

              <div class="row">

                <div class="col-md-3">
                  <div class="form-group">
                    <label class="col-form-label" for="denominacionsoli">Denominación Social:</label>
                    <input type="text" class="form-control form-control-sm" id="denominacionsoli" placeholder="" readonly>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-group">
                      <label id="Departamentoslabel">Departamento:</label>
                      <select data-valor="0"  id="Departamentos" class="form-control form-control-sm test-select" style="width: 100%;">
                      </select>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <img style="display: none;" width="32px" height="32px" id="Municipiosspinner" src="assets/images/loading-waiting.gif">
                    <div  id="Municipiosdiv" class="form-group">
                      <label id="Municipioslabel">Municipio:</label>
                      <select data-valor="0" id="Municipios" class="form-control form-control-sm test-select" style="width: 100%;">
                      </select>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <img style="display: none;" width="32px" height="32px" id="Aldeasspinner" src="assets/images/loading-waiting.gif">
                    <div id="Aldeasdiv" class="form-group">
                      <label id="Aldeaslabel">Aldea:</label>
                      <select data-valor="0" id="Aldeas" class="form-control form-control-sm test-select" style="width: 100%;">
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
                      <input pattern="^[^\s@]+@[^\s@]+\.[^\s@]+$"  type="email" class="form-control form-control-sm test-controls" id="emailsoli" placeholder="rbthaofic@gmail.com">
                    </div>
                    <div id="emailsolilabelerror" style="visibility: hidden;" class="errorlabel">
                            Email Invalido
                    </div>
                  </div>
                </div>

              <br>

              <div class="row">
                <div class="col-md-2">
                  <button id="btnprevious1" type="button" class="btn btn-success btn-previous-form"><i class="fa-solid fa-arrow-left"></i> Anterior (F2)</button>
                </div>
                <div class="col-md-2"></div>
                <div class="col-md-2"></div>
                <div class="col-md-2"></div>
                <div class="col-md-2"></div>                  
                <div class="col-md-2 justify-content-right">
                  <button id="btnnext1"  type="button" class="btn btn-primary btn-next-form"><i class="fa-solid fa-arrow-right"></i> Siguiente (F10)</button>
                </div>                  
              </div>                  

                <br>
                <br>
                
            </div>

           
            <div id="test-form-4" role="tabpanel" class="bs-stepper-pane readonly dstepper-none" aria-labelledby="stepperFormTrigger4">
              <hr>
                <div class="row">
                  <div class="col-md-2">
                    <button id="btnprevious4" type="button" class="btn btn-success btn-previous-form"><i class="fa-solid fa-arrow-left"></i> Anterior (F2)</button>
                  </div>
                  <div class="col-md-2"></div>
                  <div class="col-md-2"></div>
                  <div class="col-md-2"></div>
                  <div class="col-md-2"></div>                  
                  <div class="col-md-2 justify-content-right">
                    <button id="btnnext4"   type="button" class="btn btn-primary btn-next-form"><i class="fa-solid fa-arrow-right"></i> Siguiente (F10)</button>
                  </div>                  
              </div>
            </div>

              <div id="test-form-5" role="tabpanel" class="bs-stepper-pane readonly dstepper-none" aria-labelledby="stepperFormTrigger5">
                <hr>
                <div class="row">
                  <div class="col-md-2">
                    <button id="btnprevious6" type="button" class="btn btn-success btn-previous-form"><i class="fa-solid fa-arrow-left"></i> Anterior (F2)</button>
                  </div>
                  <div class="col-md-2"></div>
                  <div class="col-md-2"></div>
                  <div class="col-md-2"></div>
                  <div class="col-md-2"></div>                  
                  <div class="col-md-2 justify-content-right">
                    <button id="btnnext6" title="Una presionado este botón y finalizado el proceso se da por concluido el registro de Expedientes Mavins. Ya no podrá continuar agregando más concesiones y/o tramites y el expediente pasa a revisión a espera del pago para trabajarlo."
                    type="button" class="btn btn-primary btn-next-form"><i class="fa-solid fa-floppy-disk"></i> Salvar y Finalizar (F10)</button>
                  </div>                  
              </div>
              </div>

              <div id="test-form-3" role="tabpanel" class="bs-stepper-pane readonly dstepper-none" aria-labelledby="stepperFormTrigger3">
              <div class="d-flex justify-content-center row">
                <!-- **************************************************************************************************************** -->
                <!-- ID de Vista -->
                <!-- **************************************************************************************************************** -->
                <div class="col-md-1">
                  <div class="form-group">&nbsp;</div>
                </div>
                <div id="idVista" class="col-md-10">
                  <span id="idVistaSTPC">
                    <!-- ******************************************************* -->
                    <!-- Ingreso de Concesion -->
                    <!-- Modal -->
                    <!-- ******************************************************* -->
                    <button style="display: none;" type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalConcesion">
                      Launch demo modal
                    </button>
                    <div class="modal fade bd-example-modal-lg" id="modalConcesion" tabindex="-1" role="dialog" aria-labelledby="modalConcesionTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header" style="background-image: url('assets/images/logos/banner-xiomara.jpg');background-repeat: no-repeat;	background-size: cover;">
                            <h5 class="modal-title" id="exampleModalLongTitle">INGRESE UNA CONCESIÓN</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true"><i class="fas fa-window-close fa-2x gobierno1"></i></span>
                            </button>
                          </div>
                          <div class="modal-body">
                      <input placeholder="EJ: CO-CNE-10231-20 ó PES-CENE-314-19 ó PE-CNE-5421-20" pattern="^[A-Z0-9\-]{10,20}$" id="concesion" class="form-control form-control-sm test-controls" minlength="10" maxlength="25" title="Digite el CO, PES o PER EXP">
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
                          </div>
                        </div>
                      </div>
                    </div>                 

                      <div class="row unbordered-row">
                          <div class="col-md-12 background-top-row-stpc"><div class="form-group"></div>
                      </div>

                      
                      <span class="background-middle-row-stpc unbordered-row">
                          <div class="row">
                              <div class="col-md-12">
                                  <div class="form-group">&nbsp;</div>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-md-12">
                                      <h4 style="text-align: center; font-weight: bold;">PERMISO EXPLOTACIÓN:  <span id="concesion_perexp"></span></h4>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-md-12">
                                      <h6 style="text-align: center; font-weight: bold;"><span id="concesionlabel">TIPO DE CONCESION:</span>  <span id="concesion_concesion"></span></h6>
                              </div>                          
                          </div>
                          <div class="row unbordered-row">
                              <div class="col-md-12">
                                  <h4 style="text-align: center; font-weight: bold;">FECHA DE EXPIRACION:  <span id="concesion_fecven"></span></h4>
                              </div>
                          </div>
                      </span>

                      <span class="background-botton-row-stpc unbordered-row">

                          <div class="row">
                              <div class="col-md-12">
                                  <div class="form-group">&nbsp;</div>
                              </div>
                          </div>


                          <div class="row bordered-row-grey">
                              <div class="col-md-12">
                                  <div class="form-group">
                                          <strong style="font-size: 16px;">1. DATOS DEL CONCESIONARIO</strong>
                                  </div>
                              </div>
                          </div>

                          <div class="row bordered-row">
                              <div class="col-md-3">
                                  <div class="form-group">
                                          <strong>1.1 Concesionario</strong>
                                  </div>
                              </div>
                              <div class="col-md-9">
                                  <div class="form-group">
                                          <strong><span id="concesion_nombreconcesionario"></span></strong>
                                  </div>
                              </div>    
                          </div>
                          <div class="row bordered-row">
                              <div class="col-md-3">
                                  <div class="form-group">
                                          <strong>1.2 Afiliado/Socio </strong>
                                  </div>
                              </div>
                              <div class="col-md-9">
                                  <div class="form-group">
                                          <strong><span id="concesion_afiliado">NO APLICA</span></strong>
                                  </div>
                              </div>    
                          </div>

                          <div class="row bordered-row">
                              <div class="col-md-3">
                                  <div class="form-group">
                                          <strong>1.3 RTN Concesionario </strong>
                                  </div>
                              </div>
                              <div class="col-md-9">
                                  <div class="form-group">
                                          <strong><span id="concesion_rtn"></span></strong>
                                  </div>
                              </div>    
                          </div>

                          <div class="row bordered-row">
                              <div class="col-md-3">
                                  <div class="form-group">
                                          <strong>1.4 Fecha Expedición </strong>
                                  </div>
                              </div>
                              <div class="col-md-3">
                                  <div class="form-group">
                                          <strong><span id="concesion_fecexp"></span></strong>
                                  </div>
                              </div>    
                              <div class="col-md-3">
                                  <div class="form-group">
                                          <strong>1.5 Resolución</strong>
                                  </div>
                              </div>
                              <div class="col-md-3">
                                  <div class="form-group">
                                          <strong><span id="concesion_resolucion"></span></strong>
                                  </div>
                              </div>        
                          </div>

                          <div class="row bordered-row-grey">
                              <div class="col-md-12">
                                  <div class="form-group">
                                          <strong style="font-size: 16px;">2. CARACTERISTICAS DEL VEHICULO</strong>
                                          <button title="Presione el boton para actualizar los datos del vehiculo con la información del Instituto de la Propiedad" id="btnaddvehiculo" class="btn btn-primary btn-sm" style="float:right;">OBTENER INFORMACIÓN DEL <i class="fa-solid fa-van-shuttle"></i><strong> DESDE EL IP</strong></button>
                                  </div>
                              </div>
                          </div>

                          <div class="row p-0m-0">
                              <div class="col-md-3 bordered-row">
                                  <strong id="concesion_vinlabel">2.1 VIN</strong>
                              </div>
                              <div class="col-md-3 bordered-row">
                                  <div class="form-group">
                                      <input title="El vin no puede ser menor de 6 caracteres ni mayor a 17" pattern="^[a-zA-Z0-9]{6,17}$" class="form-control form-control-sm form-control-unbordered test-controls" id="concesion_vin" minlength="6" maxlength="17">
                                  </div>
                              </div>    
                              <div class="col-md-3 bordered-row">
                                  <strong id="concesion_placalabel">2.2 Placa</strong>
                              </div>
                              <div class="col-md-3 bordered-row">
                                  <div class="form-group">
                                      <input id="concesion_placa" title="La placa debe contener los primeros 3 digitos alfa y los últimos 4 numericos, máximo 7 caracteres" pattern="^[A-Z]{3}\d{4}$" class="form-control form-control-sm form-control-unbordered test-controls" minlength="7" maxlength="7"></td>            
                                  </div>
                              </div>        
                          </div>

                          <div class="row p-0m-0">
                              <div class="col-md-3 bordered-row">
                                  <div class="form-group">
                                          <strong id="concesion_serielabel">2.3 Serie</strong>
                                  </div>
                              </div>
                              <div class="col-md-3 bordered-row">
                                  <div class="form-group">
                                      <input title="La serie no puede ser menor de 6 caracteres ni mayor a 17" pattern="^[a-zA-Z0-9\s,.\-]{6,17}$" class="form-control form-control-sm form-control-unbordered test-controls" id="concesion_serie" minlength="6" maxlength="17">
                                  </div>
                              </div>    
                              <div class="col-md-3 bordered-row">
                                  <div class="form-group">
                                          <strong id="concesion_motorlabel">2.4 Motor</strong>
                                  </div>
                              </div>
                              <div class="col-md-3 bordered-row">
                                  <div class="form-group">
                                      <input title="El número de motor no puede ser menor de 6 caracteres ni mayor a 17"  pattern="^[a-zA-Z0-9\s,.\-]{6,17}$" class="form-control form-control-sm form-control-unbordered test-controls" id="concesion_motor" minlength="6" maxlength="17">
                                  </div>
                              </div>        
                          </div>

                          <div class="row p-0m-0">
                              <div class="col-md-3 bordered-row">
                                  <div class="form-group">
                                          <strong id="marcaslabel">2.5 Marca</strong>
                                  </div>
                              </div>
                              <div class="col-md-3 bordered-row">
                                  <div class="form-group">
                                      <select data-valor="0"  id="marcas" class="form-control form-control-sm form-control-unbordered test-select" style="width: 100%;">
                                      </select>
                                  </div>
                              </div>    
                              <div class="col-md-3 bordered-row">
                                  <div class="form-group">
                                          <strong id="coloreslabel">2.6 Color</strong>
                                  </div>
                              </div>
                              <div class="col-md-3 bordered-row">
                                  <div class="form-group">
                                      <select data-valor="0"  id="colores" class="form-control form-control-sm form-control-unbordered test-select" style="width: 100%;">
                                      </select>
                                  </div>
                              </div>        
                          </div>

                          <div class="row p-0m-0">
                              <div class="col-md-3 bordered-row">
                                  <div class="form-group">
                                          <strong id="anioslabel">2.7 Año</strong>
                                  </div>
                              </div>
                              <div class="col-md-3 bordered-row">
                                  <div class="form-group">
                                      <select data-valor="0"  id="anios" class="form-control form-control-sm form-control-unbordered test-select" style="width: 100%;">
                                      </select>
                                  </div>
                              </div>    
                              <div class="col-md-3 bordered-row">
                                  <div class="form-group">
                                          <strong>2.8 Tipo</strong>
                                  </div>
                              </div>
                              <div class="col-md-3 bordered-row">
                                  <div class="form-group">
                                      <span id="concesion_tipovehiculo"></span>
                                  </div>
                              </div>        
                          </div>

                          <div class="row p-0m-0">
                              <div class="col-md-3 bordered-row">
                                  <div class="form-group">
                                          <strong>2.9 Certificado Anterior</strong>
                                  </div>
                              </div>
                              <div class="col-md-3 bordered-row">
                                  <div class="form-group">
                                      <strong><span id="concesion_cerant"></span></strong>
                                  </div>
                              </div>    
                              <div class="col-md-3 bordered-row">
                                  <div class="form-group">
                                          <strong>2.10 Número de Registro DGT </strong>
                                  </div>
                              </div>
                              <div class="col-md-3 bordered-row">
                                  <div class="form-group">
                                      <strong><span id="concesion_numregant"></span></strong>
                                  </div>
                              </div>        
                          </div>

                          <div class="row bordered-row-grey">
                              <div class="col-md-12">
                                  <div class="d-flex justify-content-center">
                                          <strong style="font-size: 16px;text-align: center;">NUMERO DE REGISTRO: <span id="concesion_numeroregistro"></span></strong>
                                  </div>
                              </div>
                          </div>

                          <div class="row bordered-row-grey">
                              <div class="col-md-12">
                                  <div class="form-group">
                                          <strong style="font-size: 16px;">3. DATOS DEL SERVICIO</strong>
                                  </div>
                              </div>
                          </div>

                          <div class="row bordered-row">
                              <div class="col-md-3">
                                  <div class="form-group">
                                          <strong>3.1 Categoría</strong>
                                  </div>
                              </div>
                              <div class="col-md-9">
                                  <div class="form-group">
                                          <strong><span id="concesion_categoria"></span></strong>
                                  </div>
                              </div>    
                          </div>

                          <div class="row">
                              <div class="col-md-12">
                              <div class="form-group">&nbsp;</div></div>
                          </div>

                          <div class="row bordered-row-grey">
                              <div class="col-md-12">
                                  <div class="form-group">
                                          <strong style="font-size: 16px;">E. EXTRAS</strong>
                                  </div>
                              </div>
                          </div>

                          <div class="row p-0m-0">

                              <div class="col-md-3 bordered-row">
                                  <div class="form-group">
                                          <strong id="combustiblelabel">E.1 Combustible</strong>
                                  </div>
                              </div>

                              <div class="col-md-3 bordered-row">
                                  <div class="form-group">
                                      <input title="El combustible puede ser DIESEL, GASOLINA y GAS LICUADO" pattern="^[a-zA-Z]{6,11}$" 
                                      class="form-control form-control-sm form-control-unbordered test-controls"
                                      id="combustible" minlength="6" maxlength="10">
                                  </div>
                              </div>    

                              <div class="col-md-3 bordered-row">
                                  <div class="form-group">
                                          <strong id="capacidadlabel">E.2 Capacidad en Kg</strong>
                                  </div>
                              </div>

                              <div class="col-md-3 bordered-row">
                                  <div class="form-group">
                                      <input title="La capacidad de la unidad no puede tener menos de 3 caracteres ni mas de 10 caracteres" pattern="^\d{3,8}(\.\d{1,2})?$" 
                                      class="form-control form-control-sm form-control-unbordered test-controls" id="capacidad" minlength="6" maxlength="17">
                                  </div>
                              </div>        

                          </div>

                          <div class="row p-0m-0">
                              <div class="col-md-3 bordered-row">
                                  <div class="form-group">
                                          <strong id="altolabel">E.3 Alto</strong>
                                  </div>
                              </div>
                              <div class="col-md-3 bordered-row">
                                  <div class="form-group">
                                      <input  title="Alto del unidad no puede tener menos de 1 caracteres ni más de 5" pattern="^\d{1,3}(\.\d{1,2})?$" class="form-control form-control-sm form-control-unbordered test-controls" id="alto" minlength="6" maxlength="17">
                                  </div>
                              </div>    
                              <div class="col-md-3 bordered-row">
                                  <div class="form-group">
                                          <strong id="largolabel">E.4 Largo</strong>
                                  </div>
                              </div>

                              <div class="col-md-3 bordered-row">
                                  <div class="form-group">
                                      <input title="Largo de la unidad no puede tener menos de 1 caracteres ni más de 5" pattern="^\d{1,3}(\.\d{1,2})?$" class="form-control form-control-sm form-control-unbordered test-controls" id="largo" minlength="6" maxlength="17">
                                  </div>
                              </div>        

                          </div>

                          <div class="row p-0m-0">

                              <div class="col-md-3 bordered-row">
                                  <div class="form-group">
                                          <strong id="ancholabel">E.5 Ancho</strong>
                                  </div>
                              </div>

                              <div class="col-md-9 bordered-row">
                                  <div class="form-group">
                                      <input title="El ancho de la unidad no puede tener menos de 1 caracteres ni más de 5" pattern="^\d{1,3}(\.\d{1,2})?$" class="form-control form-control-sm form-control-unbordered test-controls" id="ancho" minlength="6" maxlength="17">
                                  </div>
                              </div> 
                          </div>
                        </span>
                  </span>
                </div>
                <div class="col-md-1">
                  <div class="form-group">&nbsp;</div>
                </div>
              </div>
            

              <div class="row">
                <div class="col-md-2">
                    <button id="btnprevious2" type="button" class="btn btn-success btn-previous-form"><i class="fa-solid fa-arrow-left"></i> Anterior (F2)</button>
                </div>
                <div class="col-md-2">
                  <button style="display: none;" id="btnconcesion" type="button" class="btn btn-light"><i class="fa-solid fa-rotate-right"></i> Recargar</button>  
                </div>
                <div class="col-md-2">
                  <button style="display: none;" id="btnmultas"  type="button" class="btn btn-secondary"><i class="fas fa-coins btn-custom"></i> Multas</button>  
                </div>
                <div class="col-md-2">
                  <button style="display: none;" id="btnconsultas"  type="button" class="btn btn-warning"><i class="fas fa-binoculars"></i> Consultas</button>  
                </div>
                <div class="col-md-2">
                  <button style="display: none;" id="btnperexp"  type="button" class="btn btn-danger"><i class="fa-solid fa-file"></i> Per Exp</button>  
                </div>
                <div class="col-md-2 justify-content-right">
                  <button id="btnnext2" type="button" class="btn btn-primary btn-next-form"><i class="fa-solid fa-arrow-right"></i> Siguiente (F10)</button>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                <div class="form-group">&nbsp;</div>
                </div>
              </div>

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
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
  </body>
</html>