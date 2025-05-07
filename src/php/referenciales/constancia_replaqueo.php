<?php
/* validando que paso por la pantalla de login */
session_start();
if (!isset($_SESSION['user_name'])) { //tipo
   $appcfg_page_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
   $_SESSION['url'] = $appcfg_page_url;
   $_SESSION['flashmsg'] = "Favor inicie sesión para poder ingresar al sistema";
   header("location: ../../../index.php");
   exit();
}
?>
<?php require_once('../../../configuracion/configuracion.php'); ?>
<?php require_once("../../../../config/conexion.php"); ?>
<?php

// Para ver los datos que se envian desde el fetch
$datos_json = file_get_contents("php://input");

// Decodifica el JSON
$datos = json_decode($datos_json, true);

$placa = $datos['placa'];
$estado = $datos['estado'];

// echo json_encode($estado);
$placabegin = substr($placa, 0, 2);
$placas = ["TB", "TC", "TE", "TP", "TT", "TR"];
if (!in_array($placabegin, $placas)) {
   if (isset($estado) && $estado == 'true') {
      $file = str_replace("@@USUARIO@@", $_SESSION["user_name"], $file);
      echo "<script>generarConstancia();</script>";
   } else {
      echo "<script>verConstancia($estado);</script>";
     
      //verConstancia(' . "'" . $vehientra['vehiculo'][0]['ID_Memo'] . "'" . ');
   }
}

// $placabegin = substr($vehientra['ID_Placa'], 0, 2);
// $placas = ["TB", "TC", "TE", "TP", "TT", "TR"];
// if (!in_array($placabegin, $placas)) {
//    if (!isset($vehientra['vehiculo'])) {
//       $file = str_replace("@@USUARIO@@", $_SESSION["user_name"], $file);
//       $cardentra = $cardentra . "<div class='row'>
//                      <div class='col-12 col-md-12 text-muted'>
//                         <p class='text-sm'>
//                         <b class='d-block'>
//                         <button class='primary' type='button' onclick='generarConstancia();'>GENERAR CONSTANCIA DE REPLAQUEO PARA LA UNIDAD " . $vehientra['ID_Placa'] . "</button></b></p></div></div>";
//    } else {

//       $cardentra = $cardentra . '<div class="row">
//                      <div class="col-12 col-md-12 text-muted">
//                         <p class="text-sm">
//                         <b class="d-block">
//                         <button class="primary" type="button" onclick="verConstancia(' . "'" . $vehientra['vehiculo'][0]['ID_Memo'] . "'" . ');">YA EXISTE UNA CONSTANCIA GENERADA PARA ESTA FSL Y/O NÚMERO DE CHASIS ' . $vehientra['vehiculo'][0]['Chasis_Entra'] . '/' . $vehientra['vehiculo'][0]['ID_Solicitud'] . ' PRESIONE CLICK PARA VERLA</button></b></p></div></div>';
//    }
// }

// $cardentra = $cardentra . '</div></div></div>';


?>
