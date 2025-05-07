<?php
session_start();
//* Archivo de configuración de las variables globales
require_once('configuracion/configuracion.php');
//* Archivo de configuración de la base de datos
require_once('../config/conexion.php');

//*Recibir datos POST si es necesario
//* Leer el cuerpo de la solicitud
$json_data = file_get_contents('php://input');

// Decodificar el JSON recibido
$data = json_decode($json_data, true);
$id_estado = isset($data['estado']) ? $data['estado'] : null;
$usuario = isset($data['usuario']) ? $data['usuario'] : null;

echo json_encode($id_estado);
// Verificar si los datos necesarios están presentes[$id_estado]
if (!$id_estado || !$usuario) {
    echo json_encode(['error' => 'Faltan datos requeridos.']);
    exit;
}

// Preparar la consulta de actualización
$query_update="DELETE [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Estados_User]
WHERE 
    [usuario] = :usuario
    AND [ID_Estado] = :id_estado";

$query_update="UPDATE [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Estados_User]
SET 
    [estado] = 0
WHERE 
    [usuario] = :usuario
    AND [ID_Estado] = :id_estado;
 ";



try {
    $db->beginTransaction();
    
    $stmt_update = $db->prepare($query_update);

    // Ejecutar la consulta pasando los parámetros correspondientes
    $stmt_update->execute(array(
        ':id_estado' => $id_estado, 
        ':usuario' => $usuario,
    ));

    $db->commit();
    echo json_encode(['success' => 'actualizados correctamente']);
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode(['error' => 'Error en la consulta: ' . $e->getMessage()]);
}
?>
