<?php
session_start();
// Archivo de configuración de las variables globales
require_once('configuracion/configuracion.php');
// Archivo de configuración de la base de datos
require_once('../config/conexion.php');

$usuario= isset($data['usuario']) ? $data['usuario'] : null;

// echo $usuario;
$id_rol = [];
$codigo = [];
$descripcion = [];

try {
    $query = "SELECT 
                ru.role_id AS id_rol, 
                ru.name_user AS usuario, 
                r.codigo AS codigo,
                r.descripcion AS descripcion
              FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Roles_User] ru
              JOIN [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Roles] r
                ON r.id = ru.role_id
              WHERE ru.name_user = :usuario AND ru.estaActivo = 1";

    $stmt = $db->prepare($query);
    $stmt->execute([':usuario' => $usuario]);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($resultados) {
        foreach ($resultados as $roles) {
            $id_rol[] = $roles['id_rol'];
            $codigo[] = $roles['codigo'];
            $descripcion[] = $roles['descripcion'];
        }

        // Guardar los IDs de rol en sesión si lo necesitas
        $_SESSION['id_rolRA'] = $id_rol;
         // echo json_encode($id_rol);
        echo json_encode([
            'success' => true,
            'data' => $resultados,
            'id_rol' => $id_rol,
            'codigo' => $codigo,
            'descripcion' => $descripcion
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'No se encontraron registros.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error en la consulta roles_select: ' . $e->getMessage()
    ]);
}
