<?php
session_start();

//* Archivo de configuración de las variables globales
require_once('configuracion/configuracion.php');

//* Archivo de configuración de la base de datos
require_once('../config/conexion.php');

//* Query
// $query_rs_llamado = "SELECT [Codigo_Usuario], 
//                      [Usuario_Nombre], 
//                      [Usuario_Estado], 
//                      [Proceso],
//                      [Preforma11], 
//                      [Modalidad]
//                         FROM [IHTT_DB].[dbo].[TB_GEA_X_Abogados_Asignados]
//                         WHERE  Proceso = 4 and Preforma11 = 0";

$query_rs_llamado="SELECT DISTINCT U.[Usuario_Nombre]
      ,U.[Usuario_Password]
      ,U.[Estado_Usuario]
      ,U.[ID_Empleado]
      ,U.[SistemaFecha]
      ,U.[SistemaUsuario]
      ,U.[ID_User]
  FROM [IHTT_USUARIOS].[dbo].[TB_Usuarios] AS U
  JOIN [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Estados_User] AS EU
  ON EU.[usuario]=U.[Usuario_Nombre]
  --JOIN [IHTT_DB].[dbo].[TB_GEA_X_Abogados_Asignados] AS AA
 -- ON AA.[Usuario_Nombre]=U.[Usuario_Nombre]
  WHERE EU.ID_Estado IN('IDE-7','IDE-1')";
//   WHERE WHERE  Proceso = 4 and Preforma11 = 0 AND EU.ID_Estado IN('IDE-7','IDE-1')";
header('Content-Type: application/json');

try {
   $stmt = $db->prepare($query_rs_llamado);

   if ($stmt->execute()) {
      $respuesta = $stmt->fetchAll(PDO::FETCH_ASSOC);
      echo json_encode($respuesta);
   } else {
      $errorInfo = $stmt->errorInfo();
      echo json_encode(['error' => 'Error al ejecutar la consulta: ' . $errorInfo[2]]);
   }

   $stmt->closeCursor();
} catch (PDOException $e) {
   echo json_encode(['error' => 'Error en la consulta query_asignaUsuario: ' . $e->getMessage()]);
}
