<?php
//* Arrancando session
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
// $nivel_validar_roles = '';
// $roles_autorizados = ['OFICIAL_JURIDICO_RA','DIGITADOR_VENTANILLA_RA','IMPRESIONES_RA,SUPERVISOR_RA','SUPERVISOR_VENTANILLA_RA','SUPERVISOR_Administrador','SA'];
//*********************************************************************/
//* Validaciones de seguridad
//*********************************************************************/
// include_once('validar_seguridad.php');

//*configuración del sistema
include_once('configuracion/configuracion.php');
//*configuración del las variables globales del sistema
include_once('configuracion/configuracion_js.php');
?>
<!DOCTYPE html>
<head>
   <?php
   include_once('encabezado.php');
   ?>
</head>

<body>
   <header>
      <?php include_once('menu.php') ?>
   </header>
   <div class="container-fluid">
      <div class="container-fluid d-flex justify-content-center align-items-center" style="height: 100vh; flex-direction: column; text-align: center;">
         <h1 style="font-weight: bold;" class="gobierno1">SISTEMA DE RENOVACIONES AUTOMÁTICAS (RAM)</h1>
         <h1 style="font-weight: bold;" class="gobierno1">IHTT</h1>
      </div>
      <?php
      include_once('pie.php');
      include_once('../modal_pie.php');
      ?>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
      <script>
         <?php
         include_once('../assets/js/openModalLogin.js');
         include_once('../assets/js/login.js');
         ?>
      </script>
   </div>
   <div class="bottom">
      <footer class="d-flex flex-wrap justify-content-between align-items-center py-1 my-1">
         <?php include_once('footer.php') ?>
      </footer>
   </div>
</body>