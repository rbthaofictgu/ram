<?php
session_start();
// Archivo de configuración de las variables globales
require_once('configuracion/configuracion.php');
// Archivo de configuración de la base de datos
require_once('../config/conexion.php');

try {
    $query = "SELECT * FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Roles]";
    $stmt = $db->prepare($query);
    $stmt->execute(); // No pasamos parámetros aquí

    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($resultados) {
        echo json_encode([
            'success' => 'Recuperación exitosa',
            'data' => $resultados
        ]);
    } else {
        echo json_encode([
            'error' => 'No se encontraron registros.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Error en la consulta roles_select: ' . $e->getMessage()]);
}
