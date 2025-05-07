<?php
session_start();
// Archivo de configuración de las variables globales
require_once('configuracion/configuracion.php');
// Archivo de configuración de la base de datos
require_once('../config/conexion.php');

// Leer el cuerpo de la solicitud
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// Validar que el usuario venga en la petición
$name_user = isset($data['usuario']) ? $data['usuario'] : null;

if (!$name_user) {
    echo json_encode(['error' => 'Faltan datos requeridos.']);
    exit;
}

try {
    // Solo traemos los IDs de roles activos asignados al usuario
    $query = "SELECT ru.role_id
              FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Roles_User] ru
              WHERE ru.name_user = :name_user AND ru.estaActivo = 1";

    $stmt = $db->prepare($query);
    $stmt->execute([':name_user' => $name_user]);

    $role_ids = $stmt->fetchAll(PDO::FETCH_COLUMN); // 👈 esto devuelve un array plano de role_id

    if ($role_ids && count($role_ids) > 0) {
        echo json_encode([
            'success' => true,
            'data' => array_map('intval', $role_ids) // Convertimos los valores a enteros
        ]);
    } else {
        echo json_encode(['error' => 'No se encontró ningún registro.']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Error en la consulta rolesUser_select: ' . $e->getMessage()]);
}
