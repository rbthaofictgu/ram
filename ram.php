<?php
//*********************************************************************/
//* PÁGINA PRINCIPAL DEL SISTEMA
//*********************************************************************/
session_start();
//*********************************************************************/
//*configuración del sistema
//*********************************************************************/
include_once('configuracion/configuracion.php');
//*********************************************************************/
//*configuración del las variables globales del sistema
//*********************************************************************/
include_once('configuracion/configuracion_js.php');
//*********************************************************************/
//* Esta variable sirve para que todos los programas ubiquen el codigo
//* include_once('validar_roles.php'); 
//* Ejemplos de los valores que puede llevar nivel
//* ''
//* '../'
//* '../../'
//* '../../../'
//*********************************************************************/
$nivel_validar_roles='';
$roles_autorizados = ['SA','DIGITADOR_VENTANILLA_RA','OFICIAL_JURIDICO_RA','SUPERVISOR_RA'];
//*********************************************************************/
//* Validaciones de seguridad
//*********************************************************************/
include_once('validar_seguridad.php');
//******************************************************************/
//* Es Renovacion Automatica
//******************************************************************/
if (!isset($_SESSION["Es_Renovacion_Automatica"])) {
  $_SESSION["Es_Renovacion_Automatica"] = true;
}
//******************************************************************/
//* Es originado en ventanilla
//******************************************************************/
if (!isset($_SESSION["Originado_En_Ventanilla"])) {
  $_SESSION["Originado_En_Ventanilla"] = true;
}
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
   <?php include_once('menu.php') ?>
  </header>
  <?php if (!isset($_SESSION["Secuencia"])) { ?>
    <input type="hidden" id="Secuencia" value="1">
  <?php } else { ?>
    <input type="hidden" id="Secuencia" value="<?php echo $_SESSION["Secuencia"] ?>">
  <?php } ?>
  <input type="hidden" id="Permiso_Explotacion" value="false">
  <input type="hidden" id="Permiso_Explotacion_Encriptado" value="">
  <input type="hidden" id="Concesion_Encriptada" value="">
  <input type="hidden" id="estaPagadoElCambiodePlaca" value="false">
  <input type="hidden" id="RequiereRenovacionConcesion" value="false">
  <input type="hidden" id="RequiereRenovacionPerExp" value="false">
  <input type="hidden" id="NuevaFechaVencimientoConcesion" value="null">
  <input type="hidden" id="NuevaFechaVencimientoPerExp" value="null">
  <input type="hidden" id="FechaVencimientoConcesion" value="null">
  <input type="hidden" id="FechaVencimientoPerExp" value="null">
  <input type="hidden" id="CantidadRenovacionesConcesion" value="0">
  <input type="hidden" id="CantidadRenovacionesPerExp" value="null">
  <input type="hidden" id="ID_Estado_RAM" value="">
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
  <input type="hidden" id="ID_Solicitante" value="">
  <input type="hidden" id="ID_Apoderado" value="">
  <input type="hidden" id="ID_Unidad" value="">
  <input type="hidden" id="ID_Unidad1" value="">
  <input type="hidden" id="ID_Bitacora" value="">
  <input type="hidden" id="esConculta" value="false">
  <input type="hidden" id="esEditable" value="false">
  <input type="hidden" id="esCompartible" value="false">
  <input type="hidden" id="CodigoAvisoCobro" value="0">
  <input type="hidden" id="esUsuarioPropietario" value="false">
  <input type="hidden" id="AvisoCobroEstado" value="">  
  <?php $idUsuario = isset($_SESSION['ID_Usuario']) ? htmlspecialchars($_SESSION['ID_Usuario'], ENT_QUOTES, 'UTF-8') : ''; ?>
  <input value="<?php echo $idUsuario; ?>" type="hidden" id="ID_Usuario">
  <?php $User_Name = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') : ''; ?>
  <input value="<?php echo $User_Name; ?>" type="hidden" id="User_Name">
  <?php $Ciudad = isset($_SESSION['ciudad']) ? htmlspecialchars($_SESSION['ciudad'], ENT_QUOTES, 'UTF-8') : ''; ?>
  <input value="<?php echo $Ciudad; ?>" type="hidden" id="Ciudad">

  <!-- Cargar el sonido -->
  <audio id="celebrationSound" muted="false" autoplay="false">
    <source src="assets/sounds/397353_plasterbrain_tada-fanfare-g.mp3" type="audio/wav">
  </audio>

    <div class="container-fluid bg-white shadow-sm">
    <!-- ******************************************************* -->
    <!-- Inicio de Modal de Concesiones Salvadas -->
    <!-- ******************************************************* -->
    <div class="modal fade modal-xl" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-scrollable modal-fullscreen">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title fs-5" id="exampleModalLabel"></h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div id="tabla-container" class="container-fluid">
              <div id="idRowInput" class="row">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
    <!-- ******************************************************* -->
    <!-- Final de Modal de Concesiones Salvadas -->
    <!-- ******************************************************* -->
    <!-- ******************************************************* -->
    <!-- Final de Modal de Concesiones Salvadas -->
    <!-- ******************************************************* -->
    <!-- ********************************************************* -->
    <!-- Inicio de Modal de Placas RTBM -->
    <!-- ********************************************************* -->
    <div class="modal fade modal-xl" id="modalPlaca" tabindex="-1" aria-labelledby="modalPlacaLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-fullscreen">
        <div class="modal-content">
          <div class="modal-header">
              <h4 class="modal-title fs-5" id="modalPlacaLabel"></h4>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <div id="tabla-container" class="container-fluid">
              <div id="modalBodyPlaca" class="row">
              </div>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
        </div>
    </div>
    <!-- ********************************************************* -->
    <!-- Final de Modal de Placas RTBM -->
    <!-- ********************************************************* -->
    <!-- ******************************************************* -->
    <!-- Inicio de Modal de Ingreso Número Concesion -->
    <!-- ******************************************************* -->
    <div class="modal fade bd-example-modal-lg" id="modalReports" tabindex="-1" role="dialog" aria-labelledby="modalReportsTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header"> <!--* logo_bg -->
            <div class="modal-title" id="modalReportsLongTitle">
              <strong class="light">
              <i class="fas fa-print fa-2x light"></i><h2>LISTADO DE REPORTES</h2>
            </strong>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
              <span aria-hidden="true"><i class="fas fa-window-close gobierno1"></i></span>
            </button>
          </div>
          <div id="id_reportes" class="modal-body">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
    <!-- ******************************************************* -->
    <!-- Inicio de Modal de Ingreso Número Concesion -->
    <!-- ******************************************************* -->
    <!-- ********************************************************* -->
    <!-- Inicio de Modal de Dictamen de Cambio de Unidad (Revisión Fisico Mecanica) -->
    <!-- ********************************************************* -->
    <div class="modal fade modal-xl" id="modalDictamenten" tabindex="-1" aria-labelledby="modalDictamentenLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-fullscreen">
        <div class="modal-content">
          <div class="modal-header">
              <h4 class="modal-title fs-5" id="modalDictamentenLabel"></h4>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <div id="tabla-container" class="container-fluid">
                <div id="modalBodyDictamenten" class="row"></div>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
        </div>
    </div>
    <!-- ********************************************************* -->
    <!-- Final de Modal de Dictamen de Cambio de Unidad (Revisión Fisico Mecanica) -->
    <!-- ********************************************************* -->
    <!-- ******************************************************* -->
    <!-- Inicio de Div De  Boton Show/Hide Concesion en Pantalla -->
    <!-- ******************************************************* -->
    <button
      data-bs-original-title="Ver tabla de tramites para la concesión actual"
      data-bs-toggle="tooltip"
      data-bs-placement="top"
      onclick="fShowTramites()" style="display: none;" id="rightDivTR" type="button" class="btn btn-primary btn-sm"><i class="fas fa-clipboard-list fa-2x"></i></button>
    <!-- ******************************************************* -->
    <!-- Final de Div De  Boton Show/Hide Concesion en Pantalla -->
    <!-- ******************************************************* -->
    <!-- ******************************************************* -->
    <!-- Inicio de Div De Concesiones y sus Tramites              -->
    <!-- ******************************************************* -->
    <button
      data-bs-original-title="Ver malla de todas las concesiones registradas con sus tramites"
      data-bs-toggle="tooltip"
      data-bs-placement="top"
      onclick="fShowConcesiones()" style="display: flex;" id="rightDiv" type="button" class="btn btn-success btn-sm d-flex justify-content-center align-items-center"><i class="fas fa-binoculars fa-2x"></i></button>
    <!-- ******************************************************* -->
    <!-- Final de Div De Concesiones y sus Tramites              -->
    <!-- ******************************************************* -->
    <!-- ******************************************************* -->
    <!-- Inicio de Div De  Presentar Lista de Reportes           -->
    <!-- ******************************************************* -->
    <button
      data-bs-original-title="Ver los reportes disponibles para la RAM"
      data-bs-toggle="tooltip"
      data-bs-placement="top"
      onclick="fDisplayReports()" style="display: flex;" id="rightDivPR" type="button" class="btn btn-light btn-sm d-flex justify-content-center align-items-center"><i class="fas fa-print fa-2x"></i></button>
    <!-- ******************************************************* -->
    <!-- Inicio de Div De Presentar Lista de Reportes            -->
    <!-- ******************************************************* -->
    <br>
    <br>
    <button title="CAMBIAR DE UNIDAD" id="btnCambiarUnidad" type="button" 
      class="btn btn-success btn-sm"
      style="display: none; position: absolute; top: 195px; right: 25px; padding: 10px">
      <i class="fas fa-truck-moving fa-2x"></i>  <strong>ENTRA</strong>
    </button>
    <!-- ******************************************************* -->
    <!-- Final boton de Ver Unidades                                 -->
    <!-- ******************************************************* -->
    <!-- *** -->
    <!-- Body -->
    <!-- *** -->
    <!-- <div class="mb-4 p-3 bg-white shadow-sm"></div> -->
    <div class="row">

      <div class="col-6">
        <h6 style="font-size: 1.25rem;" class="gobierno2 fw-bolder px-1" style="text-decoration: underline;font-weight: 800;"><i class="fas fa-edit gobierno1"></i>&nbsp;INGRESO DE SOLICITUDES PREFORMA&nbsp;&nbsp;&nbsp;
         <a style="display:none" id="avisocobro" href="#" target="_blank"><i id="avisocobroicon" class="fas fa-dollar-sign"></i></a>
         &nbsp;
         <i style="display:none" id="share" class="fas fa-share-alt text_primary"></i>
         &nbsp;
         <i style="display:none" id="editable" class="fas fa-eye text_secondary"></i>
         &nbsp;
          <span id="rotulo"></span>
          <button style="display:none;" id="RAM-ROTULO" type="button" class="btn btn-outline-<?php echo $appcfg_clase; ?> btn-sm animate__animated animate__backInUp animate__delay-3s"></button>
        </h6>
      </div>

      <div class="col-2">
        <div class="input-container">
          <input  size="30" maxlength="25"  autocomplete="off" type="text" class="form-control input-prefetch" id="input-prefetch" placeholder="EJ: CO-CNE-10231-20 ó PES-CENE-314-19 ó PE-CNE-5421-20">
          <i class="fas fa-search-location" id="toggle-icon"></i>
        </div>
      </div>


      <div class="col-4 d-flex justify-content-start">

        <button
          data-bs-original-title="Salva información en pantalla"
          data-bs-toggle="tooltip"
          data-bs-placement="top"
          id="btnSalvarConcesion" type="button" style="display: none;" class="btn btn-primary btn-sm">
          <i class="fa-solid fa-floppy-disk"></i>&nbsp;&nbsp;SALVAR
        </button>
        &nbsp;&nbsp;

        <h1 id="idEstado" style="font-size: 1.25rem;" class="gobierno3 fw-bolder fst-italic px-1 mx-4 text-end ms-auto" style="font-style: italic;font-weight: 700;">

        </h1>
      </div>

      <!-- <div class="row"> -->
      <!-- <div class="col-2 text-end ">
          <h6 id="idEstado" style="font-size: 1.00rem;" class="gobierno3 fw-bolder fst-italic px-1" style="font-style: italic;font-weight: 700;">
          </h6>
        </div> -->
      <!-- </div> -->


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
            <span class="bs-stepper-label">Requisitos</span>
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


      <div id="id_stepper_content" class="bs-stepper-content">
        <div id="test-form-1" role="tabpanel" class="bs-stepper-pane fade text-left dstepper-none" aria-labelledby="stepperFormTrigger1">
          <hr>
          <div class="row">

            <div class="col-md-3">
              <div class="form-group">
                <label id="colapoderadolabel" class="col-form-label" for="colapoderado">Colegiaci&oacute;n Apoderado Legal:</label>
                <input pattern="^[1-9]\d{2,7}$" class="form-control form-control-sm test-controls" id="colapoderado" minlength="3" maxlength="9">
                <div title="Número de colegiación en el Colegio de Abogados de Honduras es invalido. Debe tener de 3 a 8 digitos enteros positivos" id="colapoderadolabelerror" style="visibility:hidden" class="errorlabel">
                  Número de colegiación invalido.
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label id="entregadocslabel">Entrega de sus Documentos:</label>
                <select data-valor="0" name="entregadocs" id="entregadocs" class="form-control form-control-sm test-select" style="width: 100%;">
                </select>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label id="telapoderadolabel" for="telapoderado">Teléfono del Apoderdo Legal:</label>
                <input pattern="[0-9]{8}" type="text" class="form-control form-control-sm test-controls" id="telapoderado" minlength="8" maxlength="15" placeholder="95614451">
                <div id="telapoderadolabelerror" style="visibility:hidden" class="errorlabel">
                  El número de teléfono es invalido, debe tener 8 digitos.
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label id="emailapoderadolabel" for="emailapoderado">Correo Electrónico:</label>
                <input pattern='^[^\s@]+@[^\s@]+\.[^\s@]+$' style="text-transform: uppercase;" type="email" class="form-control form-control-sm test-controls" id="emailapoderado" placeholder="rbthaofic@gmail.com">
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
                <input pattern="^[a-zA-Z0-9\s,.\-áéíóúÁÉÍÓÚüÜñÑ]{10,300}$" title="La dirección del apoderado legal no puede ser menor de 10 caraceteres ni mayor a 300" type="text" class="form-control form-control-sm test-controls" id="dirapoderado">
                <div id="dirapoderadolabelerror" style="visibility:hidden" class="errorlabel">
                  No puede ser menor de 10 caraceteres ni mayor a 300 o tiene caracteres invalidos (a-z,A-Z,0-9,.,).
                </div>
              </div>
            </div>


            <div class="col-md-3">
              <div class="form-group">
                <label>Presentación Documentos:</label>
                <select id="tipopresentacion" class="form-control form-control-sm" tabindex="-1" aria-hidden="true" style="width: 100%;" readonly>
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
              <button id="btnnext0" type="button" class="btn btn-primary btn-sm btn-next-form"><i class="fa-solid fa-arrow-right"></i> Siguiente (F10)</button>
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
                <select data-valor="0" id="Departamentos" class="form-control form-control-sm test-select" style="width: 100%;">
                </select>
              </div>
            </div>

            <div class="col-md-3">
              <img style="display: none;" width="32px" height="32px" id="Municipiosspinner" src="assets/images/loading-waiting.gif">
              <div id="Municipiosdiv" class="form-group">
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
                Teléfono Invalido, debe tener 8 Digitos.
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
              <button id="btnnext1" type="button" class="btn btn-primary btn-sm btn-sm btn-next-form"><i class="fa-solid fa-arrow-right"></i> Siguiente (F10)</button>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              &nbsp;
            </div>
          </div>

        </div>


        <div id="test-form-3" role="tabpanel" class="bs-stepper-pane readonly dstepper-none" aria-labelledby="stepperFormTrigger3">
          <div class="row justify-content-center">
            <div class="sidebar col-5" id="sidebar" style="display: none;">
              <!-- ******************************************************* -->
              <!-- Inicio de Div De Tabla de Tramites                      -->
              <!-- ******************************************************* -->
              <div id="concesion_tramites"  class="row justify-content-center">
                
              </div>
              <!-- ******************************************************* -->
              <!-- Final de Div De Tabla de Tramites                      -->
              <!-- ******************************************************* -->
            </div>
            <div class="contenido col-10" id="contenido">
              <!--*********************************************************************************************-->
              <!-- INICIO VISTA UNO                                                                            -->
              <!--*********************************************************************************************-->
              <div id="idVistaSTPC1" class="row unbordered-row">

                <div class="col-12 background-top-row-stpc">
                  <div class="form-group"></div>
                </div>


                <span class="background-middle-row-stpc unbordered-row">

                  <div class="row">
                    <div class="col-12">
                      <div class="form-group">&nbsp;</div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-12">
                      <h4 style="text-align: center; font-weight: bold;">PERMISO EXPLOTACIÓN: <span id="concesion_perexp"></span></h4>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-12">
                      <h6 style="text-align: center; font-weight: bold;"><span id="concesionlabel">TIPO DE CONCESION:</span> <span id="concesion_concesion"></span></h6>
                    </div>
                  </div>
                  <div class="row unbordered-row">
                    <div class="col-12">
                      <h4 style="text-align: center; font-weight: bold;">FECHA DE EXPIRACION: <span id="concesion_fecven"></span></h4>
                    </div>
                  </div>
                </span>
                <span class="background-botton-row-stpc unbordered-row">

                  <div class="row">
                    <div class="col-12">
                      <div class="form-group">&nbsp;</div>
                    </div>
                  </div>


                  <div class="row bordered-row-grey">
                    <div class="col-12 d-flex justify-content-between align-items-center">
                      <strong style="font-size: 16px;">1. DATOS DEL CONCESIONARIO</strong>
                      <span
                        data-bs-toggle="tooltip" data-bs-placement="top" title=""
                        style="display: none;"
                        id="btnModalidad"
                        class="pulse-icon">
                      </span>

                        
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
                    <div class="col-12">
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
                      <strong id="concesion_placalabel">2.2 Placa <strong>&nbsp;&nbsp;<h3 title="Placa anterior del vehiculo" class="gobierno3" style="display: none; font: weight 800px;" id="concesion_placaanterior"></h3></strong></strong>
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
                        <input style="text-transform: uppercase;" title="El número de motor no puede ser menor de 4 caracteres ni mayor a 25" pattern="^[a-zA-Z0-9\s,.\-]{4,25}$" class="form-control form-control-sm form-control-unbordered test-controls" id="concesion_motor" minlength="4" maxlength="25">
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
                        <select data-valor="0" id="marcas" class="form-control form-control-sm form-control-unbordered test-select select2bs4" style="width: 100%;">
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
                        <select data-valor="0" id="colores" class="form-control form-control-sm form-control-unbordered test-select select2bs4" style="width: 100%;">
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
                        <select data-valor="0" id="anios" class="form-control form-control-sm form-control-unbordered test-select" style="width: 100%;">
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
                    <div class="col-12">
                      <div class="d-flex justify-content-center">
                        <strong style="font-size: 16px;text-align: center;">NUMERO DE REGISTRO: <span id="concesion_numeroregistro"></span></strong>
                      </div>
                    </div>
                  </div>

                  <div class="row bordered-row-grey">
                    <div class="col-12">
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
                    <div class="col-12">
                      <div class="form-group">&nbsp;</div>
                    </div>
                  </div>

                  <div class="row bordered-row-grey">
                    <div class="col-12">
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
                        <input data-value="" style="text-transform: uppercase;" title="El combustible puede ser DIESEL, GASOLINA, GAS LICUADO y NO APLICA" pattern="^[a-zA-Z]{6,11}$"
                          class="form-control form-control-sm form-control-unbordered test-controls"
                          id="combustible" minlength="6" maxlength="10">
                      </div>
                    </div>

                    <div class="col-md-3 bordered-row">
                      <div class="form-group">
                        <strong id="capacidadlabel">E.2 Capacidad en <span id="capacidadenlabel">Kg</span></strong>
                      </div>
                    </div>

                    <div class="col-md-3 bordered-row">
                      <div class="form-group">
                        <input title="La capacidad de la unidad no puede tener menos de 2 caracteres ni mas de 10 caracteres" pattern="^\d{2,8}(\.\d{1,2})?$"
                          class="form-control form-control-sm form-control-unbordered test-controls" id="capacidad" minlength="6" maxlength="17">
                      </div>
                    </div>

                  </div>

                  <div id="rowalto" class="row p-0m-0">
                    <div class="col-md-3 bordered-row">
                      <div class="form-group">
                        <strong id="altolabel">E.3 Alto</strong>
                      </div>
                    </div>
                    <div class="col-md-3 bordered-row">
                      <div class="form-group">
                        <input title="Alto del unidad no puede tener menos de 1 caracteres ni más de 5" pattern="^\d{1,3}(\.\d{1,2})?$" class="form-control form-control-sm form-control-unbordered test-controls" id="alto" minlength="6" maxlength="17">
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

                  <div  id="rowancho" class="row p-0m-0">

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
                    <div class="col-12">
                      <div class="form-group">
                        <strong style="font-size: 16px;">P. INFORMACIÓN DEL PROPIETARIO DEL VEHÍCULO</strong>
                      </div>
                    </div>
                  </div>

                  <div class="row p-0m-0">

                    <div class="col-md-3 bordered-row">
                      <div class="form-group">
                        <strong>E.6 Nombre Propietario</strong>
                      </div>
                    </div>
                    <div class="col-md-3 bordered-row">
                      <div class="form-group">
                        <strong><span id="concesion_nombre_propietario"></span></strong>
                      </div>
                    </div>
                    <div class="col-md-3 bordered-row">
                      <div class="form-group">
                        <strong>E.7 RTN del Propietario</strong>
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
              <!--*********************************************************************************************-->
              <!-- FINAL VISTA UNO                                                                            -->
              <!--*********************************************************************************************-->
              <!--*********************************************************************************************-->
              <!-- INICIO VISTA DOS                                                                            -->
              <!--*********************************************************************************************-->
              <div id="idVistaSTPC2" class="row unbordered-row" style="display: none;">
                <div class="col-12 background-top-row-stpc">
                  <div class="form-group"></div>
                </div>


                <span class="background-middle-row-stpc unbordered-row">
                  <div class="row">
                    <div class="col-12">
                      <div class="form-group">&nbsp;</div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-12">
                      <h4 style="text-align: center; font-weight: bold;">PERMISO EXPLOTACIÓN: <span id="concesion1_perexp"></span></h4>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-12">
                      <h6 style="text-align: center; font-weight: bold;"><span id="concesionlabel1">TIPO DE CONCESION:</span> <span id="concesion1_concesion"></span></h6>
                    </div>
                  </div>
                  <div class="row unbordered-row">
                    <div class="col-12">
                      <h4 style="text-align: center; font-weight: bold;">FECHA DE EXPIRACION: <span id="concesion1_fecven"></span></h4>
                    </div>
                  </div>
                </span>
                <span class="background-botton-row-stpc unbordered-row">

                  <div class="row">
                    <div class="col-12">
                      <div class="form-group">&nbsp;</div>
                    </div>
                  </div>


                  <div class="row bordered-row-grey">
                    <div class="col-12 d-flex justify-content-between align-items-center">
                      <strong style="font-size: 16px;">1. DATOS DEL CONCESIONARIO</strong>
                      <span
                        data-bs-toggle="tooltip" data-bs-placement="top" title=""
                        id="btnModalidad1"
                        class="pulse-icon">
                      </span>
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
                    <div class="col-12">
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
                      <strong id="concesion1_placalabel">2.2 Placa <strong>&nbsp;&nbsp;<span title="Placa anterior del vehiculo" class="gobierno3" style="display: none; font: weight 800px;" id="concesion1_placaanterior"></span></strong></strong>
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
                        <input style="text-transform: uppercase;" title="El número de motor no puede ser menor de 4 caracteres ni mayor a 25" pattern="^[a-zA-Z0-9\s,.\-]{4,25}$" class="form-control form-control-sm form-control-unbordered test-controls" id="concesion1_motor" minlength="4" maxlength="25">
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
                        <select data-valor="0" id="marcas1" class="form-control form-control-sm form-control-unbordered test-select select2bs4" style="width: 100%;">
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
                        <select data-valor="0" id="colores1" class="form-control form-control-sm form-control-unbordered test-select select2bs4" style="width: 100%;">
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
                        <select data-valor="0" id="anios1" class="form-control form-control-sm form-control-unbordered test-select" style="width: 100%;">
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
                    <div class="col-12">
                      <div class="d-flex justify-content-center">
                        <strong style="font-size: 16px;text-align: center;">NUMERO DE REGISTRO: <span id="concesion1_numeroregistro"></span></strong>
                      </div>
                    </div>
                  </div>

                  <div class="row bordered-row-grey">
                    <div class="col-12">
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
                    <div class="col-12">
                      <div class="form-group">&nbsp;</div>
                    </div>
                  </div>

                  <div class="row bordered-row-grey">
                    <div class="col-12">
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
                        <strong id="capacidad1label">E.2 Capacidad en <span id="capacidad1enlabel">Kg</span></strong>
                      </div>
                    </div>

                    <div class="col-md-3 bordered-row">
                      <div class="form-group">
                        <input title="La capacidad de la unidad no puede tener menos de 3 caracteres ni mas de 10 caracteres" pattern="^\d{2,8}(\.\d{1,2})?$"
                          class="form-control form-control-sm form-control-unbordered test-controls" id="capacidad1" minlength="6" maxlength="17">
                      </div>
                    </div>

                  </div>

                  <div  id="rowalto1" class="row p-0m-0">
                    <div class="col-md-3 bordered-row">
                      <div class="form-group">
                        <strong id="alto1label">E.3 Alto</strong>
                      </div>
                    </div>
                    <div class="col-md-3 bordered-row">
                      <div class="form-group">
                        <input title="Alto del unidad no puede tener menos de 1 caracteres ni más de 5" pattern="^\d{1,3}(\.\d{1,2})?$" class="form-control form-control-sm form-control-unbordered test-controls" id="alto1" minlength="6" maxlength="17">
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

                  <div  id="rowancho1" class="row p-0m-0">

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
                    <div class="col-12">
                      <div class="form-group">
                        <strong style="font-size: 16px;">P. INFORMACIÓN DEL PROPIETARIO DEL VEHÍCULO</strong>
                      </div>
                    </div>
                  </div>

                  <div class="row p-0m-0">

                    <div class="col-md-3 bordered-row">
                      <div class="form-group">
                        <strong>E.6 Nombre Propietario</strong>
                      </div>
                    </div>
                    <div class="col-md-3 bordered-row">
                      <div class="form-group">
                        <strong><span id="concesion1_nombre_propietario"></span></strong>
                      </div>
                    </div>
                    <div class="col-md-3 bordered-row">
                      <div class="form-group">
                        <strong>E.7 RTN del Propietario</strong>
                      </div>
                    </div>

                    <div class="col-md-3 bordered-row">
                      <div class="form-group">
                        <strong><span id="concesion1_identidad_propietario"></span></strong>
                      </div>
                    </div>

                  </div>
              </div>
              <!--*********************************************************************************************-->
              <!-- FINAL VISTA DOS                                                                             -->
              <!--*********************************************************************************************-->
              <!-------             -->
              <!------- FINAL VISTA -->
              <!-------             -->
              <div class="row">
                <div class="col-12">&nbsp;</div>
              </div>

              <div class="row">
                <div class="col-12">&nbsp;</div>
              </div>

              <div class="col-12">
                <div class="form-group">&nbsp;</div>
              </div>


            </div>
        </div>
            <div class="row">
            <div class="col">
              <button id="btnprevious2" type="button" class="btn btn-success btn-sm btn-previous-form"><i class="fa-solid fa-arrow-left"></i> Anterior (F2)</button>
            </div>
            <div class="col-md-2 d-flex justify-content-end">
              <button id="btnnext2" type="button" class="btn btn-primary btn-sm btn-next-form"><i class="fa-solid fa-arrow-right"></i> Siguiente (F10)</button>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="form-group">&nbsp;</div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="form-group">&nbsp;</div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="form-group">&nbsp;</div>
            </div>
          </div>
        </div>


        <div id="test-form-4" role="tabpanel" class="bs-stepper-pane readonly dstepper-none" aria-labelledby="stepperFormTrigger4">
          <!-- ******************************************************  -->
          <!-- RBTHAOFIC@GMAIL.COM 07/11/2022 NUEVOS REQUICITOS SEGUN  -->
          <!-- SECRETARIA GENERAL                                      -->
          <!-- FINAL                                                   -->
          <!-- ******************************************************  -->
          <div class="row">
            <div class="col-md-6">
              <div class="form-check form-switch">
                <input class="form-check-input" value="1" type="checkbox" role="switch" id="flexSwitchCheckPermisoExplotacion" name="flexSwitchCheck[]">
                <label class="form-check-label" for="flexSwitchCheck">Permiso de Explotación</label>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-check form-switch">
                <input class="form-check-input" value="2" type="checkbox" role="switch" id="flexSwitchCheckCertificadoOperacion" name="flexSwitchCheck[]">
                <label class="form-check-label" for="flexSwitchCheck">Certificado de Operación</label>
              </div>
            </div>

          </div>

          <div class="row">

            <div class="col-md-6">
              <div class="form-check form-switch">
                <input class="form-check-input" value="3" type="checkbox" role="switch" id="flexSwitchCheckCarnetColegiacion" name="flexSwitchCheck[]">
                <label class="form-check-label" for="flexSwitchCheck">Carnet de Colegiación de Abogado Vigente</label>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-check form-switch">
                <input class="form-check-input" value="4" type="checkbox" role="switch" id="flexSwitchCheckAcreditarRepresentacion" name="flexSwitchCheck[]">
                <label class="form-check-label" for="flexSwitchCheck">Acreditar Representación Legal de Persona Juridica</label>
              </div>
            </div>

          </div>

          <div class="row">

            <div class="col-md-6">
              <div class="form-check form-switch">
                <input class="form-check-input" value="5" type="checkbox" role="switch" id="flexSwitchCheckEscritoSolicitud" name="flexSwitchCheck[]">
                <label class="form-check-label" for="flexSwitchCheck">Escrito de Solicitud</label>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-check form-switch">
                <input class="form-check-input" value="6" type="checkbox" role="switch" id="flexSwitchCheckDNI" name="flexSwitchCheck[]">
                <label class="form-check-label" for="flexSwitchCheck">DNI</label>
              </div>
            </div>

          </div>

          <div class="row">

            <div class="col-md-6">
              <div class="form-check form-switch">
                <input class="form-check-input" value="7" type="checkbox" role="switch" id="flexSwitchCheckRTN" name="flexSwitchCheck[]">
                <label class="form-check-label" for="flexSwitchCheck">RTN</label>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-check form-switch">
                <input class="form-check-input" value="8" type="checkbox" role="switch" id="flexSwitchCheckInspeccionFisico" name="flexSwitchCheck[]">
                <label class="form-check-label" for="flexSwitchCheck">Inspección Físico Mecánica (FTT03)</label>
              </div>
            </div>

          </div>


          <div class="row">

            <div class="col-md-6">
              <div class="form-check form-switch">
                <input class="form-check-input" value="9" type="checkbox" role="switch" id="flexSwitchCheckBoletaRevision" name="flexSwitchCheck[]">
                <label class="form-check-label" for="flexSwitchCheck">Boleta de Revisión </label>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-check form-switch">
                <input class="form-check-input" value="10" type="checkbox" role="switch" id="flexSwitchCheckContratoArrendamiento" name="flexSwitchCheck[]">
                <label class="form-check-label" for="flexSwitchCheck">Contrato de arrendamiento (si no es el propietario de la unidad) </label>
              </div>
            </div>

          </div>

          <div class="row">

            <div class="col-md-6">
              <div class="form-check form-switch">
                <input class="form-check-input" value="11" type="checkbox" role="switch" id="flexSwitchCheckAutenticidadCarta" name="flexSwitchCheck[]">
                <label class="form-check-label" for="flexSwitchCheck">Certificado de Autenticidad de la firma de la Carta poder </label>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-check form-switch">
                <input class="form-check-input" value="12" type="checkbox" role="switch" id="flexSwitchCheckAutenticidadDocumentos" name="flexSwitchCheck[]">
                <label class="form-check-label" for="flexSwitchCheck">Certificado de Autenticidad de los Documentos</label>
              </div>
            </div>

          </div>

          <div id="cargadocs" class="row" style="display:none">
            <div class="form-group col-12">
              <label for="exampleInputFile">Cargar Documento:</label>
              <div class="input-group" id="cargadocsss">
                <div class="custom-file">
                  <input type="file" class="custom-file-input" id="EscaneoSolicitud" onchange="CargaSolicitud()" type="file" accept="application/pdf">
                  <label class="custom-file-label" for="exampleInputFile" style="border-radius: .25rem;">Dar click para cargar</label>
                </div>
                <!--  <div class="input-group-append">
                              <span class="input-group-text" id="">Upload</span>
                              </div> -->
                <input type="hidden" id="ArchivoTemporal" value="0">
              </div>
            </div>
            <br\>
              <div class="form-group col-12">
                <h3 id="fileUploaded" style="display: none;" class="gobierno1"><a id="fileUploadedLink" href="" target="_blank"><i class="fas fa-file-pdf fa-2x"></i> VER ARCHIVO DE EXPEDIENTE</a></h3>
              </div>
          </div>
          <!-------             -->
          <!------- FINAL VISTA -->
          <!-------             -->

          <div class="row">
            <div class="col-md-2">
              <button id="btnprevious3" type="button" class="btn btn-success btn-sm btn-previous-form"><i class="fa-solid fa-arrow-left"></i> Anterior (F2)</button>
            </div>
            <div class="col-md-2"></div>
            <div class="col-md-2"></div>
            <div class="col-md-2"></div>
            <div class="col-md-2"></div>
            <div class="col-md-2 d-flex justify-content-end">
              <button id="btnnext3" title="Al presionar este boton ira a la pantalla de finalizacion (recuerde que aun podra regresar a las pantallas anteriores)" type="button" class="btn btn-primary btn-sm btn-next-form"><i class="fa-solid fa-arrow-right"></i> Siguiente (F10)</button>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
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
      <div id="id_stepper_gif" class="d-flex justify-content-center align-items-center" style="height: 100vh; display: none;">
        <img id="id_img_stepper_gif" style="height: 100vh; display: none;" width="50px" height="50px" class="content-center img-fluid" src="assets/images/loading-waiting.gif" alt="Cargando pantalla" />
      </div>
    </div>
  </div>
  <!-- *** -->
  <!-- Body -->
  <!-- *** -->
  </div>
  <div class="bottom">
    <footer class="d-flex flex-wrap justify-content-between align-items-center py-1 my-1">
      <?php include_once('footer.php') ?>
    </footer>
  </div>
  <?php
  include_once('pie.php');
  include_once('../modal_pie.php');
  ?>
  <script type="text/javascript" src="<?php echo $appcfg_Dominio; ?>assets/js/ram.js"></script>
  <script type="text/javascript" src="<?php echo $appcfg_Dominio; ?>assets/js/objeto.js"></script>
  <script>
    <?php
    include_once('../assets/js/openModalLogin.js');
    include_once('../assets/js/login.js');
    ?>
  </script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>;
  <script src="https://cdn.jsdelivr.net/gh/xcash/bootstrap-autocomplete@v2.3.7/dist/latest/bootstrap-autocomplete.min.js"></script>
  <script type="text/javascript" src="<?php echo $appcfg_Dominio; ?>assets/js/autocomplet.js"></script>
  <!-- Tom Select CSS -->
  <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet" />
  <!-- Tom Select JS -->
  <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
  <script src="<?php echo $appcfg_Dominio_Corto;?>tools/select2/js/select2.full.min.js"></script>
  <script type="text/javascript" src="<?php echo $appcfg_Dominio; ?>assets/js/select2Inicializar.js"></script>
  <script>
    function toggleSidebar() {
      let sidebar = document.getElementById("sidebar");
      sidebar.classList.toggle("collapsed");
    }
  </script>
</body>

</html>