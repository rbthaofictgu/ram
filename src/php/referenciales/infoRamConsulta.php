<?php
session_start();
if (!isset($_SESSION['user_name'])) { //tipo
   $appcfg_page_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
   $_SESSION['url'] = $appcfg_page_url;
   $_SESSION['flashmsg'] = "Favor inicie sesión para poder ingresar al sistema";
   header("location: ../../../inicio.php");
   exit();
}

include_once('../../../validar_roles.php');

if (!array_intersect(['SUPERVISOR_RA', 'SUPERVISOR_VENTANILLA_RA'], $_SESSION["ROLESXUSUARIORAM"])) {
   $appcfg_page_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
   $_SESSION['url'] = $appcfg_page_url;
   $_SESSION['flashmsg'] = "No tiene permisos para acceder a esta pantalla (COMPARTIR RAMS)";
   header("location: ../../../inicio.php");
   exit();
}
$esConsulta = true;
include "infoRam.php";


?>

