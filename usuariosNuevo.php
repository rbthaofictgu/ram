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

//* Obtener el término de búsqueda
$term = '%' . (isset($data['term']) ? trim($data['term']) : '') . '%';

// $term = '%' . $_POST['buscar'] . '%';
// Validar entrada
if (empty($term)) {
   echo json_encode(['error' => 'Término de búsqueda vacío.']);
   exit;
}

try {
   $sql = "SELECT DISTINCT 
            (e.Nombres + ' ' + e.Apellidos) AS nombre_empleado,
            u.Usuario_Nombre AS nombre_usuario
        FROM [IHTT_USUARIOS].[dbo].[TB_Usuarios] u
        JOIN [IHTT_RRHH].[dbo].[TB_Empleados] e ON e.ID_Empleado = u.ID_Empleado
        WHERE u.Estado_Usuario = 1 AND (
        (e.Nombres + ' ' + e.Apellidos) LIKE ?
        OR u.Usuario_Nombre LIKE ?
      )
        order by (e.Nombres + ' ' + e.Apellidos) desc ";
   //       JOIN [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Estados_User] as eu
   //   ON  eu.[usuario]=u.[usuario_Nombre]
   //   where u.[Estado_Usuario]=1

   $stmt = $db->prepare($sql);
   $stmt->execute([$term, $term]);
   // $stmt->execute([
   //    ':term' => '%' . $term . '%'
   // ]);

   $nombres = $stmt->fetchAll(PDO::FETCH_ASSOC); //  devuelve arreglo de objetos con nombre_empleado y nombre_usuario

   echo json_encode($nombres);
} catch (Exception $e) {
   echo json_encode(['error' => 'Error en la búsqueda: ' . $e->getMessage()]);
}
