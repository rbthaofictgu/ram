<?php
unset($_SESSION['refUrl']);
if (!isset($_SESSION['user_name'])) {
  $_SESSION['refUrl'] = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  $_SESSION['flashmsg'] = "Favor inicie sesión para poder ingresar al sistema";
  header("location:" .  $nivel_validar_roles . "inicio.php");
  exit();
} else {
  include_once($nivel_validar_roles . 'validar_roles.php');
  if (is_array($_SESSION["ROLESXUSUARIORAM"])) {
    if (!estaAutorizado($roles_autorizados, $_SESSION["ROLESXUSUARIORAM"])) {
      $_SESSION['flashmsg'] = "No tiene permisos para acceder a esta pantalla";
      header("location:" .  $nivel_validar_roles ."inicio.php");
      exit();
    }
  } else {
      $_SESSION['flashmsg'] = "No tiene permisos para acceder a esta pantalla";
      header("location:" .  $nivel_validar_roles ."inicio.php");
      exit();
  }
}