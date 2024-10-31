<?php 
//* PÁGINA PRINCIPAL DEL SISTEMA
session_start();
//*configuración del sistema
include_once('configuracion/configuracion.php');
if (!isset($_SESSION['url']) && !isset($_SESSION['user_name'])) {
    if ($appcfg_Dominio == (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") {
      $appcfg_page_url =  (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" . "index.php";	 
    }else {
      $appcfg_page_url =  (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";	 
    }
} else {
  $appcfg_page_url='';
}

//******************************************************************/
// Es Renovacion Automatica
//******************************************************************/
if (!isset($_SESSION["Es_Renovacion_Automatica"])) {
  $_SESSION["Es_Renovacion_Automatica"]=true;
}
//******************************************************************/
// Es originado en ventanilla
//******************************************************************/
if (!isset($_SESSION["Originado_En_Ventanilla"])) {
  $_SESSION["Originado_En_Ventanilla"]=true;
}
//*configuración del las variables globales del sistema
include_once('configuracion/configuracion_js.php');
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
  <head>
  <?php 
    include_once('encabezado.php');
  ?>
  </head>
    <body>
    <header>
    <?php include_once('menu.php')?>
    </header>
    <?php if (!isset($_SESSION["Secuencia"])) {?>
      <input type="hidden" id="Secuencia" value="1">
    <?php } else { ?>
      <input type="hidden" id="Secuencia" value="<?php echo $_SESSION["Secuencia"] ?>">
    <?php } ?>

    <input type="hidden" id="Permiso_Explotacion" value="false">
    <input type="hidden" id="estaPagadoElCambiodePlaca" value="false">
    <input type="hidden" id="RequiereRenovacionConcesion" value="false">
    <input type="hidden" id="RequiereRenovacionPerExp" value="false">
    <input type="hidden" id="NuevaFechaVencimientoConcesion" value="null">
    <input type="hidden" id="NuevaFechaVencimientoPerExp" value="null">
    <input type="hidden" id="CantidadRenovacionesConcesion" value="0">
    <input type="hidden" id="CantidadRenovacionesPerExp" value="null">
    <input type="hidden" id="ID_Categoria" value="">
    <input type="hidden" id="ID_Clase_Servicio" value="">
    <input type="hidden" id="ID_Modalidad" value="">
    <input type="hidden" id="ID_Tipo_Servicio" value="">
    <input type="hidden" id="Malla" value="">
    <input type="hidden" id="RAM" value="">
    <input type="hidden" id="ID_Expediente" value="">
    <input type="hidden" id="ID_Solicitud" value="">
    <input type="hidden" id="ID_Resolucion" value="">
    <input type="hidden" id="ID_AvisoCobro" value="">
    <input type="hidden" id="ID_Solicitud" value="">
    <input type="hidden" id="ID_Solicitante" value="">
    <input type="hidden" id="ID_Apoderado" value="">
    <input type="hidden" id="ID_Unidad" value="">
    <input type="hidden" id="ID_Unidad1" value="">
    <input type="hidden" id="ID_Bitacora" value="">

    <div class="container-fluid bg-white shadow-sm">
        <!-- ******************************************************* -->
        <!-- Inicio de Modal de Ingreso Número Concesion -->
        <!-- ******************************************************* -->
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
        <!-- ******************************************************* -->
        <!-- Final de Modal de Ingreso Número Concesion -->
        <!-- ******************************************************* -->
        <!-- ******************************************************* -->
        <!-- Inicio de Div De Tramites                               -->
        <!-- ******************************************************* -->
        <div style="display: none;" id="concesion_tramites" class="col-md-5 scroll-div">
        </div>
        <!-- ******************************************************* -->
        <!-- Final de Div De Tramites                               -->
        <!-- ******************************************************* -->
        <button onclick="fShowConcesiones()" style="display: flex;" id="rightDiv"  type="button" class="btn btn-warning btn-sm"><i class="fas fa-binoculars"></i></button>          
        <!-- ******************************************************* -->
        <!-- Inicio de Div De Tramites                               -->
        <!-- ******************************************************* -->
        <br>
        <br>
        <button title="CAMBIAR DE UNIDAD"  id="btnCambiarUnidad" type="button" style="display: none; position: absolute; top: 150px; right: 75px; padding: 10px" class="btn btn-light btn-sm scroll-btn">
              <strong>ENTRA</strong>
        </button> 
        <!-- ******************************************************* -->
        <!-- Final boton de Ver Unidades                                 -->
        <!-- ******************************************************* -->
        <!-- *** -->    
        <!-- Body -->
        <!-- *** -->    
        <!-- <div class="mb-4 p-3 bg-white shadow-sm"></div> -->    
        <div class="row">
          <div class="col-md-8">
            <h6 style="font-size: 1.25rem;" class="gobierno2 fw-bolder px-1" style="text-decoration: underline;font-weight: 800;"><i class="fas fa-edit gobierno1"></i>&nbsp;INGRESO DE SOLICITUDES PREFORMA&nbsp;&nbsp;&nbsp;
            <button style="display:none;" id="RAM-ROTULO" type="button" class="btn btn-outline-<?php  echo $appcfg_clase;?> btn-sm"></button>
            </h6>
          </div>
          <div class="col-md-4 d-flex justify-content-end">
            <button title="Salvar la información en actual"  id="btnSalvarConcesion" type="button" style="display: none;" class="btn btn-primary btn-sm">
              <i class="fa-solid fa-floppy-disk"></i>&nbsp;&nbsp;SALVAR
            </button> 
            &nbsp;&nbsp;
            <button title="Agregar una nueva concesion y/o verificar si una concesion ya fue ingresada a esta solicitud"  style="display: none;" id="btnAddConcesion" type="button" class="btn btn-success btn-sm">
              <i class="fa-solid fa-magnifying-glass"></i>&nbsp;&nbsp;BUSCAR
            </button> 
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
                    <input  pattern='^[^\s@]+@[^\s@]+\.[^\s@]+$' style="text-transform: uppercase;" type="email" class="form-control form-control-sm test-controls" id="emailapoderado" placeholder="rbthaofic@gmail.com">
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
                <div class="col-md-2 d-flex justify-content-end">
                  <button id="btnnext0"  type="button" class="btn btn-primary btn-sm btn-next-form"><i class="fa-solid fa-arrow-right"></i> Siguiente (F10)</button>
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
                      <input readonly type="text" data-id="" class="form-control form-control-sm" id="tiposolicitante">
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
                      <input pattern="[0-9]{8}" type="text" class="form-control form-control-sm test-controls" id="telsoli" placeholder="Ej: 95614451" maxlength="8">
                    </div>
                    <div id="telsolilabelerror" style="visibility: hidden;" class="errorlabel">
                            Teléfono Invalido, debe tener  8 Digitos.
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label id="emailsolilabel" for="emailsoli">Correo Electrónico:</label>
                      <input pattern="^[^\s@]+@[^\s@]+\.[^\s@]+$" style="text-transform: uppercase;" type="email" class="form-control form-control-sm test-controls" id="emailsoli" placeholder="Ej: rbthaofic@gmail.com">
                    </div>
                    <div id="emailsolilabelerror" style="visibility: hidden;" class="errorlabel">
                            Email Invalido
                    </div>
                  </div>
                </div>

              <br>

              <div class="row">
                <div class="col-md-2">
                  <button id="btnprevious1" type="button" class="btn btn-success btn-sm btn-previous-form"><i class="fa-solid fa-arrow-left"></i> Anterior (F2)</button>
                </div>
                <div class="col-md-2"></div>
                <div class="col-md-2"></div>
                <div class="col-md-2"></div>
                <div class="col-md-2"></div>                  
                <div class="col-md-2 d-flex justify-content-end">
                  <button id="btnnext1"  type="button" class="btn btn-primary btn-sm btn-sm btn-next-form"><i class="fa-solid fa-arrow-right"></i> Siguiente (F10)</button>
                </div>                  
              </div>                  

              <div class="row">
                <div class="col-md-12">
                  &nbsp;
                </div>
              </div>
                
            </div>


            <div id="test-form-3" role="tabpanel" class="bs-stepper-pane readonly dstepper-none" aria-labelledby="stepperFormTrigger3">
              <div id="idVista" class="row d-flex justify-content-center">
                <div style="display: none;" id="div-vista-1" class="col-md-5"></div>
                <div id="div-vista-2" class="col-md-7">
                  <!--*********************************************************************************************-->                  
                  <!-- INICIO VISTA UNO                                                                            -->
                  <!--*********************************************************************************************-->
                  <span id="idVistaSTPC1">
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
                                      </div>
                                  </div>
                              </div>

                              <div class="row p-0m-0">

                                  <div class="col-md-3 bordered-row">
                                      <strong id="concesion_vinlabel">2.1 VIN</strong>
                                  </div>

                                  <div class="col-md-3 bordered-row">
                                      <div class="form-group">
                                          <input type="hidden" id="concesion_modelo_vehiculo" value="">
                                          <input type="hidden" id="concesion_tipo_vehiculo" value="">
                                          <input style="text-transform: uppercase;" title="El vin no puede ser menor de 6 caracteres ni mayor a 17" pattern="^[a-zA-Z0-9]{6,17}$" class="form-control form-control-sm form-control-unbordered test-controls" id="concesion_vin" minlength="6" maxlength="17">
                                      </div>
                                  </div>    

                                  <div class="col-md-3 bordered-row">
                                      <strong id="concesion_placalabel">2.2 Placa <strong>&nbsp;&nbsp;<span title="Placa anterior del vehiculo" class="gobierno1" style="display: none; font: weight 800px;" id="concesion_placaanterior"></span></strong></strong>
                                  </div>

                                  <div class="col-md-3 bordered-row">
                                      <div class="form-group">
                                          <input style="text-transform: uppercase;" id="concesion_placa" title="La placa debe contener los primeros 3 digitos alfa y los últimos 4 numericos, máximo 7 caracteres" pattern="^[A-Z]{3}\d{4}$" class="form-control form-control-sm form-control-unbordered test-controls" minlength="7" maxlength="7"></td>            
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
                                          <input style="text-transform: uppercase;" title="La serie no puede ser menor de 6 caracteres ni mayor a 17" pattern="^[a-zA-Z0-9\s,.\-]{6,17}$" class="form-control form-control-sm form-control-unbordered test-controls" id="concesion_serie" minlength="6" maxlength="17">
                                      </div>
                                  </div>    

                                  <div class="col-md-3 bordered-row">
                                      <div class="form-group">
                                              <strong id="concesion_motorlabel">2.4 Motor</strong>
                                      </div>
                                  </div>

                                  <div class="col-md-3 bordered-row">
                                      <div class="form-group">
                                          <input style="text-transform: uppercase;" title="El número de motor no puede ser menor de 6 caracteres ni mayor a 17"  pattern="^[a-zA-Z0-9\s,.\-]{6,17}$" class="form-control form-control-sm form-control-unbordered test-controls" id="concesion_motor" minlength="6" maxlength="17">
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
                                          <input style="text-transform: uppercase;" title="El combustible puede ser DIESEL, GASOLINA y GAS LICUADO" pattern="^[a-zA-Z]{6,11}$" 
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

                              <div class="row bordered-row-grey">
                                  <div class="col-md-12">
                                      <div class="form-group">
                                              <strong style="font-size: 16px;">P. INFORMACIÓN DEL PROPIETARIO DEL VEHÍCULO</strong>
                                      </div>
                                  </div>
                              </div>

                              <div class="row p-0m-0">

                                <div class="col-md-3 bordered-row">
                                    <div class="form-group">
                                            <strong >E.6 Nombre Propietario</strong>
                                    </div>
                                </div>
                                <div class="col-md-3 bordered-row">
                                    <div class="form-group">
                                      <strong><span id="concesion_nombre_propietario"></span></strong>
                                    </div>
                                </div>    
                                <div class="col-md-3 bordered-row">
                                    <div class="form-group">
                                            <strong >E.7 Identidad del Propietario</strong>
                                    </div>
                                </div>

                                <div class="col-md-3 bordered-row">
                                    <div class="form-group">
                                    <strong><span id="concesion_identidad_propietario"></span></strong>
                                    </div>
                                </div>        

                              </div>

                              
                        </span>
                    </div>
                  </span>
                  <!--*********************************************************************************************-->                  
                  <!-- FINAL VISTA UNO                                                                            -->
                  <!--*********************************************************************************************-->
                  <!--*********************************************************************************************-->                  
                  <!-- INICIO VISTA DOS                                                                            -->
                  <!--*********************************************************************************************-->
                  <span style="display: none;" id="idVistaSTPC2">
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
                                          <h4 style="text-align: center; font-weight: bold;">PERMISO EXPLOTACIÓN:  <span id="concesion1_perexp"></span></h4>
                                  </div>
                              </div>
                              <div class="row">
                                  <div class="col-md-12">
                                          <h6 style="text-align: center; font-weight: bold;"><span id="concesionlabel">TIPO DE CONCESION:</span>  <span id="concesion1_concesion"></span></h6>
                                  </div>                          
                              </div>
                              <div class="row unbordered-row">
                                  <div class="col-md-12">
                                      <h4 style="text-align: center; font-weight: bold;">FECHA DE EXPIRACION:  <span id="concesion1_fecven"></span></h4>
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
                                              <strong><span id="concesion1_nombreconcesionario"></span></strong>
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
                                                <strong><span id="concesion1_afiliado">NO APLICA</span></strong>
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
                                                <strong><span id="concesion1_rtn"></span></strong>
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
                                                <strong><span id="concesion1_fecexp"></span></strong>
                                        </div>
                                    </div>    

                                    <div class="col-md-3">
                                        <div class="form-group">
                                                <strong>1.5 Resolución</strong>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                                <strong><span id="concesion1_resolucion"></span></strong>
                                        </div>
                                    </div>        

                                </div>

                                <div class="row bordered-row-grey">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                                <strong style="font-size: 16px;">2. CARACTERISTICAS DEL VEHICULO</strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="row p-0m-0">

                                    <div class="col-md-3 bordered-row">
                                        <strong id="concesion1_vinlabel">2.1 VIN</strong>
                                    </div>

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                            <input type="hidden" id="concesion1_modelo_vehiculo" value="">
                                            <input type="hidden" id="concesion1_tipo_vehiculo" value="">
                                            <input style="text-transform: uppercase;" title="El vin no puede ser menor de 6 caracteres ni mayor a 17" pattern="^[a-zA-Z0-9]{6,17}$" class="form-control form-control-sm form-control-unbordered test-controls" id="concesion1_vin" minlength="6" maxlength="17">
                                        </div>
                                    </div>    

                                    <div class="col-md-3 bordered-row">
                                    <strong id="concesion1_placalabel">2.2 Placa <strong>&nbsp;&nbsp;<span title="Placa anterior del vehiculo" class="gobierno1" style="display: none; font: weight 400px;" id="concesion1_placaanterior"></span></strong></strong>
                                    </div>

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                            <input style="text-transform: uppercase;" id="concesion1_placa" title="La placa debe contener los primeros 3 digitos alfa y los últimos 4 numericos, máximo 7 caracteres" pattern="^[A-Z]{3}\d{4}$" class="form-control form-control-sm form-control-unbordered test-controls" minlength="7" maxlength="7"></td>            
                                        </div>
                                    </div>        

                                </div>

                                <div class="row p-0m-0">

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                                <strong id="concesion1_serielabel">2.3 Serie</strong>
                                        </div>
                                    </div>

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                            <input style="text-transform: uppercase;" title="La serie no puede ser menor de 6 caracteres ni mayor a 17" pattern="^[a-zA-Z0-9\s,.\-]{6,17}$" class="form-control form-control-sm form-control-unbordered test-controls" id="concesion1_serie" minlength="6" maxlength="17">
                                        </div>
                                    </div>    

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                                <strong id="concesion1_motorlabel">2.4 Motor</strong>
                                        </div>
                                    </div>

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                            <input style="text-transform: uppercase;" title="El número de motor no puede ser menor de 6 caracteres ni mayor a 17"  pattern="^[a-zA-Z0-9\s,.\-]{6,17}$" class="form-control form-control-sm form-control-unbordered test-controls" id="concesion1_motor" minlength="6" maxlength="17">
                                        </div>
                                    </div>        

                                </div>

                                <div class="row p-0m-0">

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                                <strong id="marcas1label">2.5 Marca</strong>
                                        </div>
                                    </div>

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                            <select data-valor="0"  id="marcas1" class="form-control form-control-sm form-control-unbordered test-select" style="width: 100%;">
                                            </select>
                                        </div>
                                    </div>    

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                                <strong id="colores1label">2.6 Color</strong>
                                        </div>
                                    </div>

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                            <select data-valor="0"  id="colores1" class="form-control form-control-sm form-control-unbordered test-select" style="width: 100%;">
                                            </select>
                                        </div>
                                    </div>        

                                </div>

                                <div class="row p-0m-0">

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                                <strong id="anios1label">2.7 Año</strong>
                                        </div>
                                    </div>

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                            <select data-valor="0"  id="anios1" class="form-control form-control-sm form-control-unbordered test-select" style="width: 100%;">
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
                                            <span id="concesion1_tipovehiculo"></span>
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
                                            <strong><span id="concesion1_cerant"></span></strong>
                                        </div>
                                    </div>    
                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                                <strong>2.10 Número de Registro DGT </strong>
                                        </div>
                                    </div>
                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                            <strong><span id="concesion1_numregant"></span></strong>
                                        </div>
                                    </div>        
                                </div>

                                <div class="row bordered-row-grey">
                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-center">
                                                <strong style="font-size: 16px;text-align: center;">NUMERO DE REGISTRO: <span id="concesion1_numeroregistro"></span></strong>
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
                                                <strong><span id="concesion1_categoria"></span></strong>
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
                                                <strong id="combustible1label">E.1 Combustible</strong>
                                        </div>
                                    </div>

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                            <input style="text-transform: uppercase;" title="El combustible puede ser DIESEL, GASOLINA y GAS LICUADO" pattern="^[a-zA-Z]{6,11}$" 
                                            class="form-control form-control-sm form-control-unbordered test-controls"
                                            id="combustible1" minlength="6" maxlength="10">
                                        </div>
                                    </div>    

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                                <strong id="capacidad1label">E.2 Capacidad en Kg</strong>
                                        </div>
                                    </div>

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                            <input title="La capacidad de la unidad no puede tener menos de 3 caracteres ni mas de 10 caracteres" pattern="^\d{3,8}(\.\d{1,2})?$" 
                                            class="form-control form-control-sm form-control-unbordered test-controls" id="capacidad1" minlength="6" maxlength="17">
                                        </div>
                                    </div>        

                                </div>

                                <div class="row p-0m-0">
                                  <div class="col-md-3 bordered-row">
                                      <div class="form-group">
                                              <strong id="alto1label">E.3 Alto</strong>
                                      </div>
                                  </div>
                                  <div class="col-md-3 bordered-row">
                                      <div class="form-group">
                                          <input  title="Alto del unidad no puede tener menos de 1 caracteres ni más de 5" pattern="^\d{1,3}(\.\d{1,2})?$" class="form-control form-control-sm form-control-unbordered test-controls" id="alto1" minlength="6" maxlength="17">
                                      </div>
                                  </div>    
                                  <div class="col-md-3 bordered-row">
                                      <div class="form-group">
                                              <strong id="largo1label">E.4 Largo</strong>
                                      </div>
                                  </div>

                                  <div class="col-md-3 bordered-row">
                                      <div class="form-group">
                                          <input title="Largo de la unidad no puede tener menos de 1 caracteres ni más de 5" pattern="^\d{1,3}(\.\d{1,2})?$" class="form-control form-control-sm form-control-unbordered test-controls" id="largo1" minlength="6" maxlength="17">
                                      </div>
                                  </div>        
                                </div>

                                <div class="row p-0m-0">

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                                <strong id="ancho1label">E.5 Ancho</strong>
                                        </div>
                                    </div>

                                    <div class="col-md-9 bordered-row">
                                        <div class="form-group">
                                            <input title="El ancho de la unidad no puede tener menos de 1 caracteres ni más de 5" pattern="^\d{1,3}(\.\d{1,2})?$" class="form-control form-control-sm form-control-unbordered test-controls" id="ancho1" minlength="6" maxlength="17">
                                        </div>
                                    </div> 
                                </div>

                                <div class="row bordered-row-grey">
                                  <div class="col-md-12">
                                      <div class="form-group">
                                              <strong style="font-size: 16px;">P. INFORMACIÓN DEL PROPIETARIO DEL VEHÍCULO</strong>
                                      </div>
                                  </div>
                              </div>

                              <div class="row p-0m-0">
                                
                                <div class="col-md-3 bordered-row">
                                    <div class="form-group">
                                            <strong >E.6 Nombre Propietario</strong>
                                    </div>
                                </div>
                                <div class="col-md-3 bordered-row">
                                    <div class="form-group">
                                      <strong><span id="concesion1_nombre_propietario"></span></strong>
                                    </div>
                                </div>    
                                <div class="col-md-3 bordered-row">
                                    <div class="form-group">
                                            <strong >E.7 Identidad del Propietario</strong>
                                    </div>
                                </div>

                                <div class="col-md-3 bordered-row">
                                    <div class="form-group">
                                    <strong><span id="concesion1_identidad_propietario"></span></strong>
                                    </div>
                                </div>        

                              </div>

                          </span>
                      </div>
                  </span>
                  <!--*********************************************************************************************-->                  
                  <!-- FINAL VISTA DOS                                                                             -->
                  <!--*********************************************************************************************-->
                </div>
              </div>

              <!-------             -->    
              <!------- FINAL VISTA -->    
              <!-------             -->    
              <div class="row">
                <div class="col-md-12">&nbsp;</div>
              </div>

              <div class="row">
                <div class="col-md-12">&nbsp;</div>
              </div>

              <div class="col-md-12">
                  <div class="form-group">&nbsp;</div>
              </div>
            

              <div class="row">
                <div class="col-md-2">
                    <button id="btnprevious2" type="button" class="btn btn-success btn-sm btn-previous-form"><i class="fa-solid fa-arrow-left"></i> Anterior (F2)</button>
                </div>
                <div class="col-md-2">
                  <button style="display: none;" id="btnconcesion" type="button" class="btn btn-light btn-sm"><i class="fa-solid fa-rotate-right"></i> Recargar</button>  
                </div>
                <div class="col-md-2">
                  <button style="display: none;" id="btnmultas"  type="button" class="btn btn-secondary btn-sm"><i class="fas fa-coins btn-custom"></i> Multas</button>  
                </div>
                <div class="col-md-2">
                  <button style="display: none;" id="btnconsultas"  type="button" class="btn btn-warning btn-sm"><i class="fas fa-binoculars"></i> Consultas</button>  
                </div>
                <div class="col-md-2">
                  <button style="display: none;" id="btnperexp"  type="button" class="btn btn-danger btn-sm"><i class="fa-solid fa-file"></i> Per Exp</button>  
                </div>
                <div class="col-md-2 justify-content-right">
                  <button id="btnnext2" type="button" class="btn btn-primary btn-sm btn-sm btn-next-form"><i class="fa-solid fa-arrow-right"></i> Siguiente (F10)</button>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">&nbsp;</div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                <div class="form-group">&nbsp;</div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                <div class="form-group">&nbsp;</div>
                </div>
              </div>

            </div>               

            
            <div id="test-form-4" role="tabpanel" class="bs-stepper-pane readonly dstepper-none" aria-labelledby="stepperFormTrigger4">

              <!-------             -->    
              <!------- FINAL VISTA -->    
              <!-------             -->    
              <div id="idVista1" class="row d-flex justify-content-center">
                  <div class="col-md-5">
                    &nbsp;
                  </div>
                  <div class="col-md-7">
                    <span id="idVistaSTPC1">
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
                                          <h4 style="text-align: center; font-weight: bold;">PERMISO EXPLOTACIÓN:  <span id="concesion1_perexp"></span></h4>
                                  </div>
                              </div>
                              <div class="row">
                                  <div class="col-md-12">
                                          <h6 style="text-align: center; font-weight: bold;"><span id="concesionlabel">TIPO DE CONCESION:</span>  <span id="concesion1_concesion"></span></h6>
                                  </div>                          
                              </div>
                              <div class="row unbordered-row">
                                  <div class="col-md-12">
                                      <h4 style="text-align: center; font-weight: bold;">FECHA DE EXPIRACION:  <span id="concesion1_fecven"></span></h4>
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
                                              <strong><span id="concesion1_nombreconcesionario"></span></strong>
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
                                                <strong><span id="concesion1_afiliado">NO APLICA</span></strong>
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
                                                <strong><span id="concesion1_rtn"></span></strong>
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
                                                <strong><span id="concesion1_fecexp"></span></strong>
                                        </div>
                                    </div>    

                                    <div class="col-md-3">
                                        <div class="form-group">
                                                <strong>1.5 Resolución</strong>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                                <strong><span id="concesion1_resolucion"></span></strong>
                                        </div>
                                    </div>        

                                </div>

                                <div class="row bordered-row-grey">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                                <strong style="font-size: 16px;">2. CARACTERISTICAS DEL VEHICULO</strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="row p-0m-0">

                                    <div class="col-md-3 bordered-row">
                                        <strong id="concesion1_vinlabel">2.1 VIN</strong>
                                    </div>

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                            <input type="hidden" id="concesion1_modelo_vehiculo" value="">
                                            <input type="hidden" id="concesion1_tipo_vehiculo" value="">
                                            <input style="text-transform: uppercase;" title="El vin no puede ser menor de 6 caracteres ni mayor a 17" pattern="^[a-zA-Z0-9]{6,17}$" class="form-control form-control-sm form-control-unbordered test-controls" id="concesion1_vin" minlength="6" maxlength="17">
                                        </div>
                                    </div>    

                                    <div class="col-md-3 bordered-row">
                                    <strong id="concesion1_placalabel">2.2 Placa <strong>&nbsp;&nbsp;<span title="Placa anterior del vehiculo" class="gobierno1" style="display: none; font: weight 400px;" id="concesion1_placaanterior"></span></strong></strong>
                                    </div>

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                            <input style="text-transform: uppercase;" id="concesion1_placa" title="La placa debe contener los primeros 3 digitos alfa y los últimos 4 numericos, máximo 7 caracteres" pattern="^[A-Z]{3}\d{4}$" class="form-control form-control-sm form-control-unbordered test-controls" minlength="7" maxlength="7"></td>            
                                        </div>
                                    </div>        

                                </div>

                                <div class="row p-0m-0">

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                                <strong id="concesion1_serielabel">2.3 Serie</strong>
                                        </div>
                                    </div>

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                            <input style="text-transform: uppercase;" title="La serie no puede ser menor de 6 caracteres ni mayor a 17" pattern="^[a-zA-Z0-9\s,.\-]{6,17}$" class="form-control form-control-sm form-control-unbordered test-controls" id="concesion1_serie" minlength="6" maxlength="17">
                                        </div>
                                    </div>    

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                                <strong id="concesion1_motorlabel">2.4 Motor</strong>
                                        </div>
                                    </div>

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                            <input style="text-transform: uppercase;" title="El número de motor no puede ser menor de 6 caracteres ni mayor a 17"  pattern="^[a-zA-Z0-9\s,.\-]{6,17}$" class="form-control form-control-sm form-control-unbordered test-controls" id="concesion1_motor" minlength="6" maxlength="17">
                                        </div>
                                    </div>        

                                </div>

                                <div class="row p-0m-0">

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                                <strong id="marcas1label">2.5 Marca</strong>
                                        </div>
                                    </div>

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                            <select data-valor="0"  id="marcas1" class="form-control form-control-sm form-control-unbordered test-select" style="width: 100%;">
                                            </select>
                                        </div>
                                    </div>    

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                                <strong id="colores1label">2.6 Color</strong>
                                        </div>
                                    </div>

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                            <select data-valor="0"  id="colores1" class="form-control form-control-sm form-control-unbordered test-select" style="width: 100%;">
                                            </select>
                                        </div>
                                    </div>        

                                </div>

                                <div class="row p-0m-0">

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                                <strong id="anios1label">2.7 Año</strong>
                                        </div>
                                    </div>

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                            <select data-valor="0"  id="anios1" class="form-control form-control-sm form-control-unbordered test-select" style="width: 100%;">
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
                                            <span id="concesion1_tipovehiculo"></span>
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
                                            <strong><span id="concesion1_cerant"></span></strong>
                                        </div>
                                    </div>    
                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                                <strong>2.10 Número de Registro DGT </strong>
                                        </div>
                                    </div>
                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                            <strong><span id="concesion1_numregant"></span></strong>
                                        </div>
                                    </div>        
                                </div>

                                <div class="row bordered-row-grey">
                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-center">
                                                <strong style="font-size: 16px;text-align: center;">NUMERO DE REGISTRO: <span id="concesion1_numeroregistro"></span></strong>
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
                                                <strong><span id="concesion1_categoria"></span></strong>
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
                                                <strong id="combustible1label">E.1 Combustible</strong>
                                        </div>
                                    </div>

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                            <input style="text-transform: uppercase;" title="El combustible puede ser DIESEL, GASOLINA y GAS LICUADO" pattern="^[a-zA-Z]{6,11}$" 
                                            class="form-control form-control-sm form-control-unbordered test-controls"
                                            id="combustible1" minlength="6" maxlength="10">
                                        </div>
                                    </div>    

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                                <strong id="capacidad1label">E.2 Capacidad en Kg</strong>
                                        </div>
                                    </div>

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                            <input title="La capacidad de la unidad no puede tener menos de 3 caracteres ni mas de 10 caracteres" pattern="^\d{3,8}(\.\d{1,2})?$" 
                                            class="form-control form-control-sm form-control-unbordered test-controls" id="capacidad1" minlength="6" maxlength="17">
                                        </div>
                                    </div>        

                                </div>

                                <div class="row p-0m-0">
                                  <div class="col-md-3 bordered-row">
                                      <div class="form-group">
                                              <strong id="alto1label">E.3 Alto</strong>
                                      </div>
                                  </div>
                                  <div class="col-md-3 bordered-row">
                                      <div class="form-group">
                                          <input  title="Alto del unidad no puede tener menos de 1 caracteres ni más de 5" pattern="^\d{1,3}(\.\d{1,2})?$" class="form-control form-control-sm form-control-unbordered test-controls" id="alto1" minlength="6" maxlength="17">
                                      </div>
                                  </div>    
                                  <div class="col-md-3 bordered-row">
                                      <div class="form-group">
                                              <strong id="largo1label">E.4 Largo</strong>
                                      </div>
                                  </div>

                                  <div class="col-md-3 bordered-row">
                                      <div class="form-group">
                                          <input title="Largo de la unidad no puede tener menos de 1 caracteres ni más de 5" pattern="^\d{1,3}(\.\d{1,2})?$" class="form-control form-control-sm form-control-unbordered test-controls" id="largo1" minlength="6" maxlength="17">
                                      </div>
                                  </div>        
                                </div>

                                <div class="row p-0m-0">

                                    <div class="col-md-3 bordered-row">
                                        <div class="form-group">
                                                <strong id="ancho1label">E.5 Ancho</strong>
                                        </div>
                                    </div>

                                    <div class="col-md-9 bordered-row">
                                        <div class="form-group">
                                            <input title="El ancho de la unidad no puede tener menos de 1 caracteres ni más de 5" pattern="^\d{1,3}(\.\d{1,2})?$" class="form-control form-control-sm form-control-unbordered test-controls" id="ancho1" minlength="6" maxlength="17">
                                        </div>
                                    </div> 
                                </div>

                                <div class="row bordered-row-grey">
                                  <div class="col-md-12">
                                      <div class="form-group">
                                              <strong style="font-size: 16px;">P. INFORMACIÓN DEL PROPIETARIO DEL VEHÍCULO</strong>
                                      </div>
                                  </div>
                              </div>

                              <div class="row p-0m-0">
                                
                                <div class="col-md-3 bordered-row">
                                    <div class="form-group">
                                            <strong >E.6 Nombre Propietario</strong>
                                    </div>
                                </div>
                                <div class="col-md-3 bordered-row">
                                    <div class="form-group">
                                      <strong><span id="concesion1_nombre_propietario"></span></strong>
                                    </div>
                                </div>    
                                <div class="col-md-3 bordered-row">
                                    <div class="form-group">
                                            <strong >E.7 Identidad del Propietario</strong>
                                    </div>
                                </div>

                                <div class="col-md-3 bordered-row">
                                    <div class="form-group">
                                    <strong><span id="concesion1_identidad_propietario"></span></strong>
                                    </div>
                                </div>        

                              </div>

                          </span>
                      </div>
                    </span>
                  </div>
              </div>

              <!-------             -->    
              <!------- FINAL VISTA -->    
              <!-------             -->    
              <div class="row">
                <div class="col-md-12">
                  &nbsp;
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  &nbsp;
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  &nbsp;
                </div>
              </div>

              <div class="row">
                <div class="col-md-2">
                  <button id="btnprevious3" type="button" class="btn btn-success btn-sm btn-previous-form"><i class="fa-solid fa-arrow-left"></i> Anterior (F2)</button>
                </div>
                <div class="col-md-2"></div>
                <div class="col-md-2"></div>
                <div class="col-md-2"></div>
                <div class="col-md-2"></div>                  
                <div class="col-md-2 d-flex justify-content-end">
                  <button id="btnnext3" title="Al presionar este boton ira a la pantalla de finalizacion (recuerde que aun podra regresar a las pantallas anteriores)"  type="button" class="btn btn-primary btn-sm btn-next-form"><i class="fa-solid fa-arrow-right"></i> Siguiente (F10)</button>
                </div>                  
              </div>

              <div class="row">
                <div class="col-md-12">
                  &nbsp;
                </div>
              </div>


            </div>

              <div id="test-form-5" role="tabpanel" class="bs-stepper-pane readonly dstepper-none" aria-labelledby="stepperFormTrigger5">
                <hr>
                <div class="row">
                 <div div class="col-md-2">
                    <button id="btnprevious4" type="button" class="btn btn-success btn-sm btn-previous-form"><i class="fa-solid fa-arrow-left"></i> Anterior (F2)</button>
                  </div>
                  <div class="col-md-2"></div>
                  <div class="col-md-2"></div>
                  <div class="col-md-2"></div>
                  <div class="col-md-2"></div>                  
                  <div class="col-md-2 d-flex justify-content-end">
                    <button id="btnnext4" title="Una presionado este botón y finalizado el proceso se da por concluido el registro de la PREFORMA. Ya no podrá continuar agregando más concesiones y/o tramites y el expediente pasa a revisión de los abogados  a espera del pago para trabajarlo."
                    type="button" class="btn btn-primary btn-sm btn-next-form"><i class="fa-solid fa-floppy-disk"></i> Salvar y Finalizar (F10)</button>
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
    <?php 
    include_once('pie.php');
    include_once('../sattb/modal_pie.php');    
    ?>
    <script>
      <?php 
      include_once('../sattb/assets/js/openModalLogin.js');
      include_once('../sattb/assets/js/login.js');
      ?>
    </script>    
  </body>
</html>