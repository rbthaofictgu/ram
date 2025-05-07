<?php
require_once('configuracion/configuracion.php');
require_once('../config/conexion.php');

header('Content-Type: application/json');

// Recibir parámetros
$ram = $_GET['ram'] ?? '';
$usuarios = $_GET['usuarios'] ?? ''; // cadena tipo "MGIRON, KPEREZ"

if ($ram === '' || $usuarios === '') {
    echo json_encode(['error' => 'Faltan parámetros']);
    exit;
}

// Convertir la lista de usuarios en un array y limpiar espacios
$listaUsuarios = array_map('trim', explode(',', $usuarios));

// Validar si hay usuarios
if (count($listaUsuarios) === 0) {
    echo json_encode(['error' => 'No hay usuarios válidos']);
    exit;
}

try {
    $resultados = [];

    // Consulta preparada
    $sql = "SELECT 
                ID,
                ID_Formulario_Solicitud,
                Usuario_Comparte,
                Sistema_Usuario,
                CONVERT(VARCHAR(19), Sistema_Fecha, 120) AS Sistema_Fecha,
                Estado
            FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_RAM_A_COMPARTIR]
            WHERE ID_Formulario_Solicitud = :ram
              AND Usuario_Comparte = :usuario";

    $stmt = $db->prepare($sql);

    foreach ($listaUsuarios as $usuario) {
        $stmt->bindValue(':ram', $ram, PDO::PARAM_STR);
        $stmt->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultados = array_merge($resultados, $rows);
    }

    echo json_encode(['datos' => $resultados]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la consulta: ' . $e->getMessage()]);
}
