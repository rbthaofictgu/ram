<?php
if (!isset($_SESSION['url']) && !isset($_SESSION['user_name'])) {
  if ($appcfg_Dominio == (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") {
    $appcfg_page_url =  (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" . "ram.php";
  } else {
    $appcfg_page_url =  (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  }

   if (!isset($_SESSION['user_name'])) { //tipo
      $appcfg_page_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
      $_SESSION['url'] = $appcfg_page_url;
      $_SESSION['flashmsg'] = "Favor inicie sesión para poder ingresar al sistema";
      header("location:inicio.php");
      exit();
   }

   include_once('validar_roles.php');
   if (!array_intersect(['DIGITADOR_VENTANILLA_RA','OFICIAL_JURIDICO_RA','SUPERVISOR_RA'], $_SESSION["ROLESXUSUARIORAM"])) {
      $appcfg_page_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
      $_SESSION['url'] = $appcfg_page_url;
      $_SESSION['flashmsg'] = "No tiene permisos para acceder a esta pantalla (INGRESO DE RAM'S)";
      header("location:inicio.php");
      exit();
   }
} else {
  $appcfg_page_url = '';
}
