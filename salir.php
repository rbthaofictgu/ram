<?php
//? *****************************************************************************
//? archivo encargado de eliminar el inicio de session  y recargar la paguina
//? ****************************************************************************
//* archivo de configuracion del sistema
include_once('configuracion/configuracion.php');
//* archivp de cpnfiguracion de las variables  javascript
require_once('configuracion/configuracion_js.php');
function salir($dominio)
{
  session_start();
  unset($_SESSION['user_name']);
  unset($_SESSION["id_sesion"]);
  unset($_SESSION["ID_Usuario"]);
  unset($_SESSION["imgperfil"]);
  unset($_SESSION["imgperfil"]);
  session_destroy();
  // $urlServer variable de direccion global
  header("Location: " .  $dominio);
  //  header("Location: " . "https://satt2.transporte.gob.hn:285/");
  // exit();
}
//* ejecucion de la funcion */
salir($appcfg_Dominio);
