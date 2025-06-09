<?php

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
   <title>objeto</title>
   <!-- //*mandando encabezados -->
   <?php require_once('../../../encabezado_body.php'); ?>

   <!-- <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"> </script>​​​  -->
</head>

<body>
   <div class="main">
      <!-- Button trigger modal -->
      <button type="button" onclick class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
         modal
      </button>

      <div class="bottom mt-5">
         <footer class="d-flex flex-wrap justify-content-between align-items-center py-1 my-1">
            <?php include_once('../../../footer.php') ?>
         </footer>
      </div>

      <?php include_once('../../../pie.php'); ?>

   </div>
   <?PHP require_once('../../../pie.php'); ?>
   <?php require_once('modal_pie.php'); ?>
   <?php require_once('modal_encabezado.php'); ?>

   <script type="text/javascript" src="<?php echo $appcfg_Dominio; ?>assets/js/objeto.js"></script>


</body>