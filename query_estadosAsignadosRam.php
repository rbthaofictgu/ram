<?php
session_start();
//* archivo de configuracion de las variables globales
require_once('configuracion/configuracion.php');
//* archivo de configuracion de la base de datos
require_once('../config/conexion.php');

$query_rs_llamado = "";

try {

   $stmt = $db->prepare($query_rs_llamado);
   $res = $stmt->execute();
   $res = $stmt->errorInfo();
   if (isset($res) and $res == '') {
      $msg = "Error en query_estadosAsigandosRam -> " . geterror($stmt->errorInfo());
      //*liberando memoria de sql server
      $stmt->closeCursor();
      //*desaciendo cambios
      $db->rollBack();
   } else {
      $respuesta = $stmt->fetchAll();
      echo json_encode($respuesta);
   }
} catch (PDOException $e) {
   echo json_encode(['error' => 'Error en la consulta query_estadosAsigandosRam : ' . $e->getMessage()]);
}
