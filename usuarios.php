<?php
//* archivo de configuracion de las variables globales
require_once('configuracion/configuracion.php');
//* archivo de configuracion de la base de datos
require_once('../config/conexion.php');

$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';

$query_rs_llamado = "SELECT distinct
    (e.[Nombres] + ' ' + e.[Apellidos]) AS nombre_empleado,
    u.[Usuario_Nombre] AS usuario,
    u.[Estado_Usuario] AS estado,
    u.[ID_Empleado] AS codigo_empleado,
    -- up.[ID_Rol] AS rol,
    -- up.[ID_Modulo] AS modulo,
    -- r.[DESC_Rol] AS des_rol,
    STUFF((
        SELECT ', ' + eus.[ID_Estado] 
        FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Estados_User] AS eus
        WHERE eus.[usuario] = u.[Usuario_Nombre] and eus.[estado]=1
        FOR XML PATH('')
    ), 1, 2, '') AS estados_relacionados  -- Eliminar la coma extra al principio
FROM 
    [IHTT_USUARIOS].[dbo].[TB_Usuarios] AS u
JOIN 
    [IHTT_USUARIOS].[dbo].[TB_Permisos] AS up
    ON u.[Usuario_Nombre] = up.[Usuario_Nombre]
JOIN 
--     [IHTT_USUARIOS].[dbo].[TB_Roles] AS r
--     ON r.[ID_Rol] = up.[ID_Rol]
-- JOIN 
    [IHTT_RRHH].[dbo].[TB_Empleados] AS e
    ON e.[ID_Empleado] = u.[ID_Empleado]
WHERE 
    u.[Estado_Usuario] = 1
    -- AND up.[ID_Modulo] = '51'
GROUP BY 
    e.[Nombres], e.[Apellidos], u.[Usuario_Nombre], u.[Estado_Usuario], u.[ID_Empleado];
    -- up.[ID_Rol], up.[ID_Modulo], r.[DESC_Rol];
";

try {

   $stmt = $db->prepare($query_rs_llamado);
   $res = $stmt->execute();
   $res = $stmt->errorInfo();
   if (isset($res) and $res == '') {
      $msg = "Error al traer datos usuario -> " . geterror($stmt->errorInfo());
      //*liberando memoria de sql server
      $stmt->closeCursor();
      //*desaciendo cambios
      $db->rollBack();
   } else {
      $respuesta = $stmt->fetchAll();
      echo json_encode($respuesta);
   }
} catch (PDOException $e) {
   echo json_encode(['error' => 'Error en la consulta query_usuario: ' . $e->getMessage()]);
}