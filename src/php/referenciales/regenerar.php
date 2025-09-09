<?php
/* validando que paso por la pantalla de login */
session_start();

//*********************************************************************/
//* Esta variable sirve para que todos los programas ubiquen el codigo
//* include_once('validar_roles.php'); 
//* Ejemplos de los valores que puede llevar nivel
//* ''
//* '../'
//* '../../'
//* '../../../'
//*********************************************************************/
$nivel_validar_roles = '../../../';
$roles_autorizados = ['SA','SUPERVISOR_RA'];
//*********************************************************************/
//* Validaciones de seguridad
//*********************************************************************/
include_once('../../../validar_seguridad.php');
//*configuración del sistema
include_once('../../../configuracion/configuracion.php');
//*configuración del las variables globales del sistema
include_once('../../../configuracion/configuracion_js.php');
?>
<!DOCTYPE html>

<head>
   <?php
   include_once('../../../encabezado.php');
   ?>
</head>

<body>
   <header>
      <?php include_once('../../../menu.php') ?>
   </header>

   <div class="p-4 mt-5">
      <h5>
         <strong>
            <i class="far fa-edit" aria-hidden="true"></i>
            REGENERACIÓN DE RESOLUCIÓN Y AUTÓMOTIVADO
         </strong>
      </h5>
   </div>

   <div class="container">

      <body>
         <div class="d-flex justify-content-center border-info">
            <div id="idRowPrincipal" class="row pb-4">
               <div class="col-6">
                  <label for="ram"> <i class="fa-solid fa-magnifying-glass"></i> INGRESE RAM PARA REGENERAR:</label>
                  <input class="form-control" type="text" id="ram" name="ram" style="text-transform: uppercase;">
               </div>
               <div class="col-2 mt-5 d-flex align-items-end">
                  <button class="btn btn-primary" onclick="buscarRAM()">Consultar</button>
               </div>
            </div>
         </div>

         <div id="idDatos" class="row mt-2 mb-5">
            <hr class="text-info">
            <div class="col-12">

               <div class="d-flex justify-content-center">
                  <button id="btnRegenerar" class="btn btn-success" onclick="fRegenerar()" style="display:none; width:98%">REGENERAR RESOLUCIÓN Y AUTO DE INGRESO PARA RAM:</button>
               </div>

               <div class="row d-flex justify-content-center mb-5">
                  <div id="seccionDatos" class="col-5">
                     <p>
                        <strong><i class="fa-solid fa-folder-open text-info"></i> ID_RESOLUCIÓN:</strong>
                        <input class="form-control bg-primary-subtle" type="text" id="ID_Resolucion" name="ID_Resolucion"
                           style="text-transform: uppercase;" disabled>
                     </p>



                     <p>
                        <strong> <i class="fa-solid fa-user-tie text-info"></i> NOMBRE SOLICITANTE:</strong>
                        <input class="form-control" type="text" id="nombreSolicitante" name="nombreSolicitante"
                           style="text-transform: uppercase;" disabled>
                     </p>
                     <p>
                        <strong> <i class="fa-solid fa-building text-info"></i> NOMBRE EMPRESA:</strong>
                        <input class="form-control" type="text" id="nombreEmpresa" name="nombreEmpresae"
                           style="text-transform: uppercase;" disabled>
                     </p>

                  </div>

                  <div id="seccionDatos" class="col-8 mb-5">

                     <div class="row">
                        <p>
                           <strong> <i class="fa-solid fa-file text-info"></i> ID_AUTO_ADMISION:</strong>
                           <input class="form-control bg-primary-subtle" type="text" id="ID_AutoAdmision" name="ID_AutoAdmision"
                              style="text-transform: uppercase;" disabled>
                        </p>

                        <p>
                           <strong> <i class="fa-solid fa-id-card-clip text-info"></i> RTN:</strong>
                           <input class="form-control" type="text" id="rtn" name="rtn" style="text-transform: uppercase;"
                              disabled>
                        </p>
                        <!-- <p>
                           <strong> <i class="fa-solid fa-arrows-spin text-info"></i> EXPEDIENTE ESTADO:</strong>
                           <input class="form-control" type="text" id="Expediente_Estado" name="Expediente_Estado"
                              style="text-transform: uppercase;" disabled>
                        </p> -->
                        <div class="col-6">
                           <strong> N° DE CONCESIONES:</strong>
                           <span class="cuadrado  text-center" id="Concesiones">--</span>
                        </div>
                        <div class="col-6">
                           <strong> N° DE TRAMITES:</strong>
                           <span class="cuadrado text-center" id="Tramites">--</span>
                        </div>

                     </div>
                     <p>
                        <strong> <i class="fa-solid fa-file-invoice text-info"></i> DESCRIPCIÓN ESTADO:</strong>
                        <input class="form-control" type="text" id="DESC_Estado_Expediente"
                           name="DESC_Estado_Expediente" style="text-transform: uppercase;" disabled>
                     </p>
                     
                  </div>
               </div>

            </div>
         </div>
      </body>
   </div>

   <div class="bottom">
      <footer class="d-flex flex-wrap justify-content-between align-items-center py-1 my-1">
         <?php include_once('../../../footer.php') ?>
      </footer>
   </div>
   <?php
   include_once('../../../pie.php');
   include_once('../../../../modal_pie.php');
   ?>
   <script type="text/javascript" src="<?php echo $appcfg_Dominio; ?>assets/js/regenerar.js"></script>

</body>