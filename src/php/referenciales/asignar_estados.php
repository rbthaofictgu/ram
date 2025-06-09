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
$roles_autorizados = ['SUPERVISOR_RA', 'SUPERVISOR_VENTANILLA_RA', 'SA'];
//*********************************************************************/
//* Validaciones de seguridad
//*********************************************************************/
include_once('../../../validar_seguridad.php');

?>

<!-- //* archivo de conexion a la base de datos -->
<?php require_once('../../../../config/conexion.php'); ?>
<!-- //* archivo de configuracion donde se encuentrar las variables globales. -->
<?php require_once('../../../configuracion/configuracion.php'); ?>


<!DOCTYPE html>

<head>
   <title>ASIGNAR ESTADOS</title>
   <!-- //*mandando encabezados -->
   <?php require_once('../../../encabezado_body.php'); ?>
   <?php include_once('../../../menu.php') ?>
</head>
<!-- //* archivo de configuracion donde se encuentrar las variables globales. -->
<?php require_once('../../../configuracion/configuracion_js.php'); ?>
<div class="main">
   <div class="p-3 mb-5 mt-3">
      <h5>
         <strong>
            <i class="far fa-edit" aria-hidden="true"></i>
            ASIGNAR ESTADOS
         </strong>
      </h5>


      <div class="row">
         <div class="col-6">
            <div class="container mt-2">
               <label for="searchInput"><strong> <i class="fa-solid fa-magnifying-glass"></i>BUSCAR USUARIO:</strong></label>
               <input type="text" id="searchInput" data-info="" class="form-control" placeholder="Escribe para buscar..." autocomplete="off">
               <div class="col-8" id="autocomplete-list" class="autocomplete-items"></div>
            </div>
         </div>
         <div class="col-4 mt-5">
            <div class="d-flex justify-content-center">
               <button class="btn btn-info me-2" onclick=" Limpiar()">Limpiar</button>
               <button class="btn btn-success" onclick=" asignarEstados()">Asignar Estados</button>
            </div>
         </div>
      </div>


      <div class="p-4">
         <div class="row">
            <div class="col-4 mb-5">
               <div id="id_estados"></div>
            </div>
         </div>
      </div>

   </div>

   <div class="bottom mt-5">
      <footer class="d-flex flex-wrap justify-content-between align-items-center py-1 my-1">
         <?php include_once('../../../footer.php') ?>
      </footer>
   </div>

</div>


<?PHP require_once('../../../pie.php'); ?>

<?php require_once('modal_pie.php'); ?>
<?php require_once('modal_encabezado.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/@algolia/autocomplete-js"></script>
<script type="text/javascript" src="<?php echo $appcfg_Dominio; ?>assets/js/asignar_estados.js"></script>

</body>