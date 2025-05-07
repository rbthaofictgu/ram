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

//* Obtener el valor del código
$codigo = isset($data['codigo']) ? $data['codigo'] : null;

//* Verificar si el código está presente
if (!$codigo) {
    echo json_encode(['error' => 'Faltan datos requeridos.']);
    exit;
}

try {
    $db->beginTransaction();

    $deleteQuery = "DELETE FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Roles] WHERE [codigo] = :codigo";
    $stmtDelete = $db->prepare($deleteQuery);
    $stmtDelete->execute([
        ':codigo' => $codigo,
    ]);

    $db->commit();
    echo json_encode(['success' => 'Eliminación exitosa']);
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode(['error' => 'Error en la consulta roles_delete: ' . $e->getMessage()]);
}


// // 1. Buscar el ID del rol por código
// $getIdQuery = "SELECT id FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Roles] WHERE codigo = :codigo";
// $stmtGetId = $db->prepare($getIdQuery);
// $stmtGetId->execute([':codigo' => $codigo]);
// $role = $stmtGetId->fetch(PDO::FETCH_ASSOC);

// if ($role) {
//     $idRole = $role['id'];

//     // 2. Eliminar relaciones en TB_Roles_User
//     $deleteUsersQuery = "DELETE FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Roles_User] WHERE id_role = :id_role";
//     $stmtDeleteUsers = $db->prepare($deleteUsersQuery);
//     $stmtDeleteUsers->execute([':id_role' => $idRole]);

//     // 3. Eliminar el rol original
//     $deleteRoleQuery = "DELETE FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Roles] WHERE codigo = :codigo";
//     $stmtDeleteRole = $db->prepare($deleteRoleQuery);
//     $stmtDeleteRole->execute([':codigo' => $codigo]);

//     $db->commit();
//     echo json_encode(['success' => 'Rol y relaciones eliminadas correctamente']);
// } else {
//     echo json_encode(['error' => 'No se encontró el rol con ese código']);
// }
