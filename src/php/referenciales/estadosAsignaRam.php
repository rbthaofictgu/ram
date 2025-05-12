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
$roles_autorizados = ['SUPERVISOR_RA','SUPERVISOR_VENTANILLA_RA','SA'];
//*********************************************************************/
//* Validaciones de seguridad
//*********************************************************************/
include_once('../../../validar_seguridad.php');

?>

<!-- //* archivo de conexion a la base de datos -->
<?php require_once('../../../../config/conexion.php'); ?>
<!-- //* archivo de configuracion donde se encuentrar las variables globales. -->
<?php require_once('../../../configuracion/configuracion.php'); ?>
<?php require_once('../../../configuracion/configuracion_js.php'); ?>
<!DOCTYPE html>

<head>
   <title>ASIGNAR RAM A USUARIOS DEL SISTEMA</title>
   <!-- //*mandando encabezados -->
   <?php require_once('../../../encabezado_body.php'); ?>
   <?php include_once('../../../menu.php') ?>

</head>

<div class="main">
   <style>
      #usuarioSelect,
      #usuarioSelect option,
      #busqueda {
         text-transform: uppercase;
      }
   </style>
   <input type="hidden" id="ID_Tipo_Servicio" value="">
   <div class="p-4 mb-5 mt-5">
      <h5>
         <strong>
            <i class="far fa-edit" aria-hidden="true"></i>
            COMPARTIR RAM A USUARIOS DEL SISTEMA
         </strong>
      </h5>

      <div class="container-sm mt-5">
         <div class="row">
            <!-- Columna para el select -->
            <div class="col-md-4">
               <label for="usuarioSelect" class="form-label">
                  <strong><i class="far fa-check-circle"></i> SELECCIONAR USUARIO:</strong>
               </label>
               <select id="usuarioSelect" class="form-select">
                  <option value="">Seleccione un Usuario</option>
               </select>
            </div>

            <div class="col-md-4">
               <label for="estadosSelect" class="form-label">
                  <strong><i class="far fa-check-circle"></i> SELECCIONAR ESTADO:</strong>
               </label>
               <select id="estadosSelect" class="form-select">
                  <option value="">SELECCIONAR ESTADO</option>
               </select>
            </div>

            <!-- Columna para el input -->
            <div class="col-md-4">
               <label for="busquedaRam" class="form-label">
                  <strong><i class="fa-solid fa-magnifying-glass"></i>BUSCAR POR RAM:</strong>
               </label>
               <input type="text" id="busqueda" class="form-control" placeholder="Buscar por número de RAM">
            </div>
         </div>
      </div>

      <script>
         document.getElementById("busqueda").addEventListener("input", function() {
            this.value = this.value.toUpperCase();
         });
      </script>

      </br>
      <div class="container-fluid">
         <h4 class="titleTable">
            <stong>LISTADO DE RAMS QUE SE PUEDEDEN COMPARTIR</stong>
         </h4>

         <div id="contenedorTabla" class="table-responsive mb-3"></div>
         <div id="paginacion" class="mt-3"></div>
      </div>
   </div>
   <div class="bottom mt-5">

      <?php include_once('../../../footer.php') ?>

   </div>
   <?php
   include_once('../../../pie.php');
   // include_once('../../../../modal_pie.php');
   ?>


   <script src="https://cdn.jsdelivr.net/npm/@algolia/autocomplete-js"></script>
   <script type="text/javascript" src="<?php echo $appcfg_Dominio; ?>assets/js/estadosAsignaRam.js"></script>
   <script type="text/javascript" src="<?php echo $appcfg_Dominio; ?>assets/js/tablaPreformaAsignacion.js"></script>
   </body>