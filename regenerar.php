<?php
//* archivo de configuracion de las variables globales
require_once('configuracion/configuracion.php');
//* archivo de configuracion de la base de datos
require_once('../config/conexion.php');


$ram = $_POST['ram'] ?? '';

if ($ram === '') {
   echo json_encode(['exito' => false, 'mensaje' => 'RAM vacía']);
   exit;
}
$sql = "SELECT  
R.ID_Resolucion,
A.ID_AutoAdmision,
S.NombreSolicitante, 
S.NombreEmpresa, 
S.RTNSolicitante,
E.Expediente_Estado,
ES.DESC_Estado_Expediente,
(select count(distinct case when ETT.Permiso_Explotacion != '' then ETT.Certificado_Operacion
else ETT.N_Permiso_Especial end)
from [IHTT_DB].[dbo].[TB_Expediente_X_Tipo_Tramite] ETT WHERE E.ID_Solicitud = ETT.ID_Solicitud)  as Concesiones,
(select count(*) from [IHTT_DB].[dbo].[TB_Expediente_X_Tipo_Tramite] ETT WHERE E.ID_Solicitud = ETT.ID_Solicitud)  as Tramites
FROM [IHTT_DB].[dbo].[TB_Expedientes] E
INNER JOIN [IHTT_DB].[dbo].[TB_Solicitante] S ON E.ID_Solicitante = S.ID_Solicitante
INNER JOIN [IHTT_GDL].[dbo].[TB_Resolucion] R on E.ID_Solicitud = R.ID_Solicitud
INNER JOIN [IHTT_GDL].[dbo].[TB_AutoAdmision] A on E.ID_Solicitud = A.ID_Solicitud 
INNER JOIN [IHTT_DB].[dbo].[TB_Expediente_Estado] ES ON (ES.CodigoEstadoExpediente = E.Expediente_Estado OR ES.DESC_Estado_Expediente = E.Expediente_Estado)
WHERE E.ID_Expediente = :RAM";
try {
   $stmt = $db->prepare($sql);
   $stmt->bindParam(':RAM', $ram);
   $stmt->execute();

   $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
   if ($resultado) {
      echo json_encode(array_merge(['exito' => true], $resultado));
   } else {
      echo json_encode(['exito' => false]);
   }
} catch (PDOException $e) {
   echo json_encode(['exito' => false, 'error' => $e->getMessage()]);
}
