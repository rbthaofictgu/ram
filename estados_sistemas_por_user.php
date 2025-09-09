<?php
session_start();
//* archivo de configuracion de las variables globales
require_once('configuracion/configuracion.php');
//*archivo de configuracion de la base de datos
require_once('../config/conexion.php');

if (!isset($_SESSION["ID_Usuario"]) || !isset($_SESSION["user_name"])) {
   echo json_encode(array("error" => 1100, "errorhead" => "INICIO DE SESSIÓN", "errormsg" => 'NO HAY UNA SESSION INICIADA, FAVOR INICIE SESION Y VUELVA A INTENTARLO', "errorreason" => "Usuario no ha iniciado sessión en el sistema"));
} else {


   $usuario = $_SESSION["user_name"];
   $usuario_acepta = $usuario;
   $usuario_creacion = $usuario;
   $usuario_aceptaT = $usuario;
   $usuario_creacionT = $usuario;
   $usuario1 = $usuario;

   $query_rs_estado = "SELECT 
    e.[ID_Estado],
    e.[DESC_Estado],
    e.[Sistema_Usuario],
    e.[Sistema_Fecha],
    e.[Estado],
    e.[Orden],
    e.[esEditable],
    e.[puede_agregar],
    e.[esCompartible],
    eu.[usuario],
    -- Total de formularios creados o aceptados por el usuario
    (
        SELECT COUNT(*) 
        FROM [IHTT_PREFORMA].[dbo].[TB_Solicitante] AS s
        WHERE s.Estado_Formulario = e.[ID_Estado] 
          AND (s.Usuario_Acepta = :usuario_acepta OR s.Usuario_Creacion = :usuario_creacion)
    ) AS TOTALES_ESTADOS,

    -- Total de formularios pagados
    ISNULL(p.TOTAL_PAGADAS, 0) AS TOTAL_PAGADAS

FROM [IHTT_PREFORMA].[dbo].[TB_Estados] AS e

INNER JOIN [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Estados_User] AS eu
    ON e.[ID_Estado] = eu.[ID_Estado]

-- Subconsulta para unir el conteo de pagadas por estado
LEFT JOIN (
    SELECT 
        S.Estado_Formulario, 
        COUNT(*) AS TOTAL_PAGADAS
    FROM [IHTT_Webservice].[dbo].[TB_AvisoCobroEnc] AS AC 
    INNER JOIN [IHTT_PREFORMA].[dbo].[TB_Solicitante] AS S
        ON AC.[ID_Solicitud] = S.ID_Formulario_Solicitud
    WHERE AC.AvisoCobroEstado = 2  AND (S.Usuario_Acepta = :usuario_aceptaT OR S.Usuario_Creacion = :usuario_creacionT)
    GROUP BY S.Estado_Formulario
) AS p ON p.Estado_Formulario = e.ID_Estado

WHERE 
    e.[Estado] = 1  
    AND eu.[Estado] = 1 
    AND eu.[usuario] = :usuario1
ORDER BY e.[Orden]";
   try {
      //*preparando query
      $dataQuery = $db->prepare($query_rs_estado);
      //* ejecutando query
      
      $dataQuery->execute(array(
         ':usuario_acepta' => $usuario_acepta,
         ':usuario_creacion' => $usuario_creacion,
         ':usuario_aceptaT' => $usuario_aceptaT,
         ':usuario_creacionT' => $usuario_creacionT,
         ':usuario1' => $usuario1
      ));
      //*obteniendo los resultado de la consulta.
      $resultados = $dataQuery->fetchAll(PDO::FETCH_ASSOC);
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
      echo json_encode(array("error" => 1200, "errorhead" => "CARGANDO BOTONES SEGÚN ESTADOS", "errormsg" => 'ESTAMOS PRESENTANDO INCOVENIENTES PARA CARGAR LA INFORMACIÓN', "errorreason" => $th->getMessage()));
   }
}
