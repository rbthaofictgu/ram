<?php
session_start();
//* Archivo de configuración de las variables globales
require_once('configuracion/configuracion.php');
//* Archivo de configuración de la base de datos
require_once('../config/conexion.php');

//* Leer el cuerpo de la solicitud
$json_data = file_get_contents('php://input');

//* Decodificar el JSON recibido
$data = json_decode($json_data, true);

//* Obtener valores necesarios
$id_role = isset($data['id_role']) ? $data['id_role'] : null;
$name_user = isset($data['name_user']) ? $data['name_user'] : null;

//* Verificar si los datos están presentes
if (!$id_role || !$name_user) {
    echo json_encode(['error' => 'Faltan datos requeridos.']);
    exit;
}

try {
    $db->beginTransaction();

    $deleteQuery = "DELETE FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Roles_User] 
                    WHERE [id_role] = :id_role AND [name_user] = :name_user";
    $stmtDelete = $db->prepare($deleteQuery);
    $stmtDelete->execute([
        ':id_role' => $id_role,
        ':name_user' => $name_user,
    ]);

    $db->commit();
    echo json_encode(['success' => 'Eliminación exitosa']);
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode(['error' => 'Error en la consulta roles_user_delete: ' . $e->getMessage()]);
}
