<?php
//* archivo de configuracion de las variables globales
require_once('configuracion/configuracion.php');
//*archivo de configuracion de la base de datos
require_once('../config/conexion.php');
//* creando el query
// $query_rs_estado = "SELECT 
//        [ID],
//        [ID_Estado],
//        [DESC_Estado],
//        [Sistema_Usuario],
//        [Sistema_Fecha],
//        [Estado],
//        [Orden]
//   FROM [IHTT_PREFORMA].[dbo].[TB_Estados]
//  WHERE [Estado] = 1
//  ORDER BY [Orden]";

$query_rs_estado="SELECT DISTINCT
    e.[ID],
    e.[ID_Estado],
    e.[DESC_Estado],
    e.[Sistema_Usuario],
    e.[Sistema_Fecha],
    e.[Estado],
    e.[Orden],
    -- Total de formularios por estado
    (
        SELECT COUNT(*) 
        FROM [IHTT_PREFORMA].[dbo].[TB_Solicitante] AS s
        WHERE s.Estado_Formulario = e.[ID_Estado]
    ) AS TOTALES_ESTADOS,
    -- Total de pagadas por estado
    ISNULL(p.TOTAL_PAGADAS, 0) AS TOTAL_PAGADAS
FROM [IHTT_PREFORMA].[dbo].[TB_Estados] AS e
INNER JOIN [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Estados_User] AS eu
    ON e.[ID_Estado] = eu.[ID_Estado]
-- LEFT JOIN para traer totales pagadas
LEFT JOIN (
    SELECT S.Estado_Formulario, COUNT(*) AS TOTAL_PAGADAS
    FROM [IHTT_Webservice].[dbo].[TB_AvisoCobroEnc] AS AC
    INNER JOIN [IHTT_PREFORMA].[dbo].[TB_Solicitante] AS S
        ON AC.[ID_Solicitud] = S.ID_Formulario_Solicitud
    WHERE AC.AvisoCobroEstado = 2
    GROUP BY S.Estado_Formulario
) AS p
    ON e.ID_Estado = p.Estado_Formulario

WHERE e.[Estado] = 1 
ORDER BY e.[Orden]";
try {
   //*preparando query
   $dataQuery = $db->prepare($query_rs_estado);
   //* ejecutando query
   $dataQuery->execute();
   //*obteniendo los resultado de la consulta.
   $resultados = $dataQuery->fetchAll(PDO::FETCH_ASSOC);

   // echo json_encode($resultados);
   //?nota: array_keys devuleve las llaves de objeto.
   //*obteniendo las llaves
   $titleBtn = array_keys($resultados[0]);
   $todo = [];
   //* recorriendolos resultado de la consulya
   foreach ($resultados as $fila) {
      //*asignando datos
      $datos[] = $fila;
   }
   //*creando un arreglo con todos los datos
   $estados = [
      'titulo' => $titleBtn,
      'datos' => $datos,
   ];
   //* enviando arreglo
   echo json_encode($estados);
} catch (\Throwable $th) {
   //*capturando error
   echo "Error en la consulta de query_rs_estado :" . $th->getMessage();
}

?>