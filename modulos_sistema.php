<?php
/* validando que paso por la pantalla de login */
session_start();
//* archivo de configuracion de las variables globales
require_once('configuracion/configuracion.php');
//* archivo de configuracion de la base de datos
require_once('../config/conexion.php');


$query_rs_llamado = "SELECT  [ID_Modulo]
      ,[DESC_Modulo]
      ,[Detalle_Modulo]
      ,[ID_Estado]
      ,[ID_Encargado]
  FROM [IHTT_USUARIOS].[dbo].[TB_Modulos]";

try {

   $stmt = $db->prepare($query_rs_llamado);
   $res = $stmt->execute();
   $res = $stmt->errorInfo();
   if (isset($res) and $res == '') {
      $msg = "Error al traer los modulos_sistema.php -> " . geterror($stmt->errorInfo());
      //*liberando memoria de sql server
      $stmt->closeCursor();
      //*desaciendo cambios
      $db->rollBack();
   } else {
      $respuesta = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $encabezado = array();
      $cuerpo = array();
      foreach ($respuesta as $key => $value) {
         $encabezado = array_keys($value);
         $cuerpo = $value;
      }
      $estados = [
         'encabezados' => $encabezado,
         'cuerpo' => $cuerpo,
      ];

      echo json_encode($estados);
   }
} catch (PDOException $e) {
   echo json_encode(['error' => 'Error en la consulta modulos_sistemas.php: ' . $e->getMessage()]);
}
