<?php

session_start();
require_once('configuracion/configuracion.php');
require_once('../config/conexion.php');

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$ID_Formulario_Solicitud = $data['ID_Formulario_Solicitud'] ?? null;
$Usuario_Comparte = $data['Usuario_Comparte'] ?? null;
$Estado = $data['Estado'] ?? null;
$Estado_Formulario = $data['Estado_Formulario'] ?? null;
$Sistema_Usuario = $_SESSION['user_name'] ?? null;
$Sistema_Fecha = date('Y-m-d H:i:s');

if (!$ID_Formulario_Solicitud || !$Usuario_Comparte || !$Sistema_Usuario || $Estado === null || $Estado_Formulario === null) {
   echo json_encode(['error' => 'Faltan parámetros obligatorios.']);
   exit;
}

// Verificamos si ya existe la combinación
$checkQuery = "
SELECT COUNT(*) AS total
FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_RAM_A_COMPARTIR]
WHERE ID_Formulario_Solicitud = :ram AND Usuario_Comparte = :usuario AND Estado_Formulario=:estado_formulario
";

try {
   $stmt = $db->prepare($checkQuery);
   $stmt->bindParam(':ram', $ID_Formulario_Solicitud);
   $stmt->bindParam(':usuario', $Usuario_Comparte);
   $stmt->bindParam(':estado_formulario', $Estado_Formulario);
   $stmt->execute();
   $existe = $stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0;

   if ($existe) {
      // UPDATE con conversión segura de fecha
      $insertUpdateQuery = "
         UPDATE [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_RAM_A_COMPARTIR]
         SET Estado = :estado,
             Sistema_Fecha = CONVERT(datetime, :fecha, 120),
             Sistema_Usuario = :sistema_usuario
         WHERE
         ID_Formulario_Solicitud = :ram AND Usuario_Comparte = :usuario AND Estado_Formulario=:estado_formulario
      ";
   } else {
      // INSERT con conversión segura de fecha
      $insertUpdateQuery = "
         INSERT INTO [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_RAM_A_COMPARTIR]
         (ID_Formulario_Solicitud, Usuario_Comparte, Sistema_Usuario, Sistema_Fecha, Estado,Estado_Formulario)
         VALUES (:ram, :usuario, :sistema_usuario, CONVERT(datetime, :fecha, 120), :estado, :estado_formulario)
      ";
   }

   $stmt = $db->prepare($insertUpdateQuery);
   $stmt->bindParam(':ram', $ID_Formulario_Solicitud);
   $stmt->bindParam(':usuario', $Usuario_Comparte);
   $stmt->bindParam(':estado', $Estado);
   $stmt->bindParam(':fecha', $Sistema_Fecha);
   $stmt->bindParam(':sistema_usuario', $Sistema_Usuario);
   $stmt->bindParam(':estado_formulario', $Estado_Formulario);

   $stmt->execute();

   echo json_encode(['success' => 'Estado actualizado correctamente.']);
} catch (PDOException $e) {
   echo json_encode(['error' => 'Error en la consulta: ' . $e->getMessage()]);
}
