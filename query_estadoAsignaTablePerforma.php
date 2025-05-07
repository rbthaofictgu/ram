<?php
session_start();
require_once('configuracion/configuracion.php');
require_once('../config/conexion.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
$fecha = isset($_GET['fecha']) ? trim($_GET['fecha']) : '';

$limite = 10;
$offset = ($pagina - 1) * $limite;

// ----------- TOTAL -------------
$whereTotal = "WHERE 1=1 AND e.esCompartible = 1 ";
$paramsTotal = [];

if ($busqueda !== '') {
   $whereTotal .= " AND (
        soli.Nombre_Solicitante LIKE :busq1 OR 
        soli.RTN_Solicitante LIKE :busq2 OR 
        soli.ID_Formulario_Solicitud LIKE :busq3
    )";
   $paramsTotal[':busq1'] = '%' . $busqueda . '%';
   $paramsTotal[':busq2'] = '%' . $busqueda . '%';
   $paramsTotal[':busq3'] = '%' . $busqueda . '%';
}


if ($fecha !== '') {
   $whereTotal .= " AND CONVERT(VARCHAR(10), soli.Sistema_Fecha, 120) = :fecha";
   $paramsTotal[':fecha'] = $fecha;
}



$sqlTotal = "
   SELECT COUNT(*) AS total 
   FROM [IHTT_PREFORMA].[dbo].[TB_Solicitante] AS soli
   JOIN [IHTT_PREFORMA].[dbo].[TB_Estados] AS e ON e.ID_Estado = soli.Estado_Formulario
   $whereTotal
";

// ----------- DATOS -------------
$whereDatos = "WHERE 1=1 AND e.esCompartible = 1";
$paramsDatos = [];

if ($busqueda !== '') {
   $whereDatos .= " AND (
        soli.Nombre_Solicitante LIKE :busq1 OR 
        soli.RTN_Solicitante LIKE :busq2 OR 
        soli.ID_Formulario_Solicitud LIKE :busq3
    )";
   $paramsDatos[':busq1'] = '%' . $busqueda . '%';
   $paramsDatos[':busq2'] = '%' . $busqueda . '%';
   $paramsDatos[':busq3'] = '%' . $busqueda . '%';
}


if ($fecha !== '') {
   $whereDatos .= " AND CONVERT(VARCHAR(10), soli.Sistema_Fecha, 120) = :fecha";
   $paramsDatos[':fecha'] = $fecha;
}

$sqlDatos = "SELECT 
            soli.ID_Formulario_Solicitud AS RAM,
            soli.Nombre_Solicitante AS SOLICITANTE,
            soli.RTN_Solicitante AS RTN,
            CONVERT(VARCHAR(10), soli.Sistema_Fecha, 120) AS FECHA,
            soli.Usuario_Creacion AS USER_CREACION,
            soli.Usuario_Acepta AS USER_ASIGNADO,

            CASE 
               WHEN comp.estado = 1 THEN 'Compartido'
               ELSE 'No compartido'
            END AS ESTADO,

            ISNULL(usuarios_compartidos.USUARIOS_COMPARTIDOS, 'N/A') AS USUARIOS_COMPARTIDOS,
            ISNULL(ram_estados.ESTADO_RAM, 'N/A') AS ESTADO_RAM

         FROM [IHTT_PREFORMA].[dbo].[TB_Solicitante] AS soli

         JOIN [IHTT_PREFORMA].[dbo].[TB_Estados] AS e 
            ON e.ID_Estado = soli.Estado_Formulario

         LEFT JOIN (
            SELECT 
               ID_Formulario_Solicitud, 
               MAX(CAST(estado AS INT)) AS estado
            FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_RAM_A_COMPARTIR]
            GROUP BY ID_Formulario_Solicitud
         ) AS comp 
            ON soli.ID_Formulario_Solicitud = comp.ID_Formulario_Solicitud

         LEFT JOIN (
            SELECT 
               ID_Formulario_Solicitud,
               STUFF((
                     SELECT ', ' + Usuario_Comparte
                     FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_RAM_A_COMPARTIR] AS inner_comp
                     WHERE inner_comp.ID_Formulario_Solicitud = outer_comp.ID_Formulario_Solicitud
                     AND inner_comp.Estado = 1
                     FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 2, '') AS USUARIOS_COMPARTIDOS
            FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_RAM_A_COMPARTIR] AS outer_comp
            GROUP BY ID_Formulario_Solicitud
         ) AS usuarios_compartidos 
            ON soli.ID_Formulario_Solicitud = usuarios_compartidos.ID_Formulario_Solicitud

         LEFT JOIN (
            SELECT 
               ID_Formulario_Solicitud,
               STUFF((
                     SELECT ', ' + CAST(Estado_Formulario AS NVARCHAR)
                     FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_RAM_A_COMPARTIR] AS inner_ram
                     WHERE inner_ram.ID_Formulario_Solicitud = outer_ram.ID_Formulario_Solicitud
                     AND inner_ram.Estado = 1
                     FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 2, '') AS ESTADO_RAM
            FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_RAM_A_COMPARTIR] AS outer_ram
            GROUP BY ID_Formulario_Solicitud
         ) AS ram_estados
            ON soli.ID_Formulario_Solicitud = ram_estados.ID_Formulario_Solicitud
         $whereDatos
         ORDER BY soli.ID_Formulario_Solicitud DESC
         OFFSET :offset ROWS FETCH NEXT :limite ROWS ONLY";


try {
   // -------- DEPURACIÓN --------
   error_log("SQL TOTAL => $sqlTotal");
   error_log("PARAMS TOTAL => " . json_encode($paramsTotal));

   // -------- TOTAL --------
   $stmtTotal = $db->prepare($sqlTotal);

   foreach ($paramsTotal as $key => $value) {
      $stmtTotal->bindValue($key, $value, PDO::PARAM_STR);
   }


   $stmtTotal->execute();
   $total = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

   // -------- DEPURACIÓN --------
   error_log("SQL DATOS => $sqlDatos");
   error_log("PARAMS DATOS => " . json_encode($paramsDatos));

   // -------- DATOS --------
   $stmt = $db->prepare($sqlDatos);

   foreach ($paramsDatos as $key => $value) {
      $stmt->bindValue($key, $value, PDO::PARAM_STR);
   }
   $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
   $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);

   $stmt->execute();
   $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

   echo json_encode([
      'data' => $data,
      'total' => $total,
      'pagina' => $pagina,
      'limite' => $limite
   ]);
} catch (PDOException $e) {
   http_response_code(500);
   echo json_encode(['error' => 'Error en la consulta: ' . $e->getMessage()]);
}
