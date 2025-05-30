<?php
session_start();

require_once('configuracion/configuracion.php');
require_once('../config/conexion.php');

$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

$role_id = isset($data['role_id']) ? $data['role_id'] : null;
$name_user = isset($data['name_user']) ? $data['name_user'] : null;
$estaActivo = isset($data['estaActivo']) ? $data['estaActivo'] : 1;

function obtenerIPCliente() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    if (!empty($_SERVER['REMOTE_ADDR'])) return $_SERVER['REMOTE_ADDR'];
    return 'IP_NO_DETECTADA';
}

$ip = obtenerIPCliente();
$host = gethostbyaddr($ip);
if ($host === false || $host === $ip) {
    $host = 'HOST_NO_DETECTADO';
}

$usuario_creacion = $_SESSION['user_name'] ?? 'sistema';
$ip_creacion = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
$host_creacion = gethostbyaddr($ip_creacion) ?? 'localhost';

if (!$role_id || !$name_user) {
    echo json_encode(['error' => 'Faltan datos requeridos.']);
    exit;
}

try {
    $db->beginTransaction();

    $checkQuery = "SELECT COUNT(*) as count 
                   FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Roles_User] 
                   WHERE name_user = :name_user AND role_id = :role_id";
    $stmtCheck = $db->prepare($checkQuery);
    $stmtCheck->execute([
        ':name_user' => $name_user,
        ':role_id' => $role_id
    ]);
    $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if ($row['count'] > 0) {
        $updateQuery = "UPDATE [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Roles_User]
                        SET estaActivo = :estaActivo
                        WHERE name_user = :name_user AND role_id = :role_id";
        $stmtUpdate = $db->prepare($updateQuery);
        $stmtUpdate->execute([
            ':estaActivo' => $estaActivo,
            ':name_user' => $name_user,
            ':role_id' => $role_id
        ]);
        $db->commit();
        echo json_encode(['success' => 'Estado actualizado con éxito.']);
    } else {
        $insertQuery = "INSERT INTO [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Roles_User]
                        (role_id, name_user, estaActivo, fecha_creacion, usuario_creacion, ip_creacion, host_creacion)
                        VALUES (:role_id, :name_user, :estaActivo, GETDATE(), :usuario_creacion, :ip_creacion, :host_creacion)";
        $stmtInsert = $db->prepare($insertQuery);
        $stmtInsert->execute([
            ':role_id' => $role_id,
            ':name_user' => $name_user,
            ':estaActivo' => $estaActivo,
            ':usuario_creacion' => $usuario_creacion,
            ':ip_creacion' => $ip_creacion,
            ':host_creacion' => $host_creacion
        ]);
        $db->commit();
        echo json_encode(['success' => 'Inserción exitosa.']);
    }

} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    echo json_encode(['error' => 'Error en la consulta roles_user_insertUpdate: ' . $e->getMessage()]);
}
