<?php
/* validando que paso por la pantalla de login */
session_start();
// if (!isset($_SESSION['user_name'])) { //tipo
//    $appcfg_page_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
//    $_SESSION['url'] = $appcfg_page_url;
//    $_SESSION['flashmsg'] = "Favor inicie sesión para poder ingresar al sistema";
//    header("location:inicio.php");
//    exit();
// }

// include_once('validar_roles.php');

// if (!array_intersect(['OFICIAL_JURIDICO_RA','DIGITADOR_VENTANILLA_RA','IMPRESIONES_RA','SUPERVISOR_RA','SUPERVISOR_VENTANILLA_RA','SUPER_ADMINISTRADOR'], $_SESSION["ROLESXUSUARIORAM"])) {
//    $appcfg_page_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
//    $_SESSION['url'] = $appcfg_page_url;
//    $_SESSION['flashmsg'] = "No tiene permisos para acceder a esta pantalla (INICIO)";
//     header("location: inicio.php");
//    exit();
// }
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