<?php
session_start();
//* Archivo de configuración de las variables globales
require_once('configuracion/configuracion.php');
//* Archivo de configuración de la base de datos
require_once('../config/conexion.php');

//* Recibir datos POST si es necesario
//* Leer el cuerpo de la solicitud
$json_data = file_get_contents('php://input');

// Decodificar el JSON recibido
$data = json_decode($json_data, true);
$estados = $data['estado'];
$usuario = $data['usuario'];
$empleado = $data['empleado'];

// echo json_encode($estados);

//* Consulta para insertar datos en la tabla
// $query_insert = "MERGE INTO [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Estados_User] AS target
// USING (SELECT :ID_Estado AS ID_Estado, :sistema_usuario AS Sistema_Usuario, :usuario AS usuario, :estado AS estado) AS source
// ON target.ID_Estado = source.ID_Estado AND target.usuario = source.usuario AND target.estado = source.estado
// WHEN NOT MATCHED BY TARGET THEN 
// INSERT ([ID_Estado], [Sistema_Usuario], [Sistema_Fecha], [usuario],[estado])
// VALUES (source.ID_Estado, source.Sistema_Usuario, GETDATE(), source.usuario, source.estado);

$query_insert="MERGE INTO [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Estados_User] AS target
USING (SELECT :ID_Estado AS [ID_Estado], :sistema_usuario AS Sistema_Usuario, :usuario AS usuario, :estado AS estado) AS source
ON target.[ID_Estado] = source.[ID_Estado] 
   AND target.usuario = source.usuario
-- Si existe la fila y el estado es 0, actualiza el estado a 1
WHEN MATCHED AND target.estado = 0 THEN
    UPDATE SET target.estado = 1
-- Si no existe la fila, inserta una nueva
WHEN NOT MATCHED BY TARGET THEN 
    INSERT ([ID_Estado], [Sistema_Usuario], [Sistema_Fecha], [usuario], [estado])
    VALUES (source.[ID_Estado], source.Sistema_Usuario, GETDATE(), source.usuario, source.estado);

";

try {
   $db->beginTransaction();
  $stmt_insert = $db->prepare($query_insert);

   foreach ($estados as $estado) {
      // echo json_encode($estado);
      foreach ($estado as $value) {
         $stmt_insert->execute(array(':ID_Estado' => $value, ':sistema_usuario' => $_SESSION['user_name'], ':usuario' => $usuario, ':estado' => 1));
      }
   }
   $db->commit();
   echo json_encode(['success' => 'Datos insertados y actualizados correctamente']);
} catch (Exception $e) {
   // Revertir la transacción en caso de error
   if ($db->inTransaction()) {
      $db->rollBack();
   }
   echo json_encode(['error' => 'Error en la consulta: ' . $e->getMessage()]);
}
