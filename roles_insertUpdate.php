<?php
session_start();
require_once('configuracion/configuracion.php');
require_once('../config/conexion.php');

$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

$id = $data['id'] ?? null;
$codigo = $data['codigo'] ?? null;
$descripcion = $data['descripcion'] ?? null;
$estaActivo = isset($data['estado']) ? (int)$data['estado'] : null;
$usuario = $_SESSION['user_name'] ?? 'sistema';

function obtenerIPCliente()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    elseif (!empty($_SERVER['REMOTE_ADDR'])) return $_SERVER['REMOTE_ADDR'];
    return 'IP_NO_DETECTADA';
}

$ip = obtenerIPCliente();
$host = gethostbyaddr($ip);
if ($host === false || $host === $ip) $host = 'HOST_NO_DETECTADO';
$fecha = date('Y-m-d H:i:s');

if (!isset($db)) {
    echo json_encode(['error' => 'No hay conexión a la base de datos.']);
    exit;
}

try {
    $db->beginTransaction();

    // ACTUALIZACIÓN POR ID
    if ($id) {
        if ($estaActivo == 0) {
            $stmt = $db->prepare("UPDATE [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Roles]
                                  SET estaActivo = 0,
                                      fecha_eliminacion = CONVERT(DATE,:fecha,120),
                                      usuario_eliminacion = :usuario,
                                      ip_eliminacion = :ip,
                                      host_eliminacion = :host
                                  WHERE id = :id");
            $stmt->execute([
                ':fecha' => $fecha,
                ':usuario' => $usuario,
                ':ip' => $ip,
                ':host' => $host,
                ':id' => $id
            ]);
        } else {
            $stmt = $db->prepare("UPDATE [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Roles]
                                  SET estaActivo = 1,
                                      fecha_eliminacion = NULL,
                                      usuario_eliminacion = NULL,
                                      ip_eliminacion = NULL,
                                      host_eliminacion = NULL
                                  WHERE id = :id");
            $stmt->execute([':id' => $id]);
        }

        $db->commit();
        echo json_encode(['success' => 'Estado del rol actualizado correctamente.']);
        exit;
    }

    // INSERTAR O ACTUALIZAR POR CÓDIGO (CUANDO NO HAY ID)
    if (!$codigo || !$descripcion || $estaActivo === null) {
        echo json_encode(['error' => 'Faltan datos requeridos.']);
        exit;
    }

    $checkQuery = "SELECT COUNT(*) as count FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Roles] WHERE codigo = :codigo";
    $stmtCheck = $db->prepare($checkQuery);
    $stmtCheck->execute([':codigo' => $codigo]);
    $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if ($row && $row['count'] > 0) {
        if ($estaActivo == 0) {
            $stmt = $db->prepare("UPDATE [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Roles]
                                  SET estaActivo = 0,
                                      fecha_eliminacion = :fecha,
                                      usuario_eliminacion = :usuario,
                                      ip_eliminacion = :ip,
                                      host_eliminacion = :host
                                  WHERE codigo = :codigo");
            $stmt->execute([
                ':fecha' => $fecha,
                ':usuario' => $usuario,
                ':ip' => $ip,
                ':host' => $host,
                ':codigo' => $codigo
            ]);
        } else {
            $stmt = $db->prepare("UPDATE [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Roles]
                                  SET estaActivo = 1,
                                      fecha_eliminacion = NULL,
                                      usuario_eliminacion = NULL,
                                      ip_eliminacion = NULL,
                                      host_eliminacion = NULL
                                  WHERE codigo = :codigo");
            $stmt->execute([':codigo' => $codigo]);
        }

        $db->commit();
        echo json_encode(['success' => 'Rol actualizado correctamente.']);
    } else {
        $stmtInsert = $db->prepare("INSERT INTO [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Roles]
            (codigo, descripcion, estaActivo,fecha_creacion, usuario_creacion, ip_creacion, host_creacion)
            VALUES (:codigo, :descripcion, :estaActivo,GETDATE(), :usuario, :ip, :host)");
        $stmtInsert->execute([
            ':codigo' => $codigo,
            ':descripcion' => $descripcion,
            ':estaActivo' => $estaActivo,
            // ':fecha' => $fecha,
            ':usuario' => $usuario,
            ':ip' => $ip,
            ':host' => $host
        ]);
        $db->commit();
        echo json_encode(['success' => 'Rol insertado correctamente.']);
    }
} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    echo json_encode(['error' => 'Error en la consulta roles_insertUpdate: ' . $e->getMessage()]);
}
