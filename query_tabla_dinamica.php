<?php
// SIMULACIÓN PARA EJECUTAR POR CONSOLA

session_start();
//* archivo de configuracion de las variables globales
require_once('configuracion/configuracion.php');
//* archivo de configuracion de la base de datos
require_once('../config/conexion.php');

require_once("../logs/logs.php");

//* Obtener parámetros de entrada
$idEstado = $_GET['estado'] ?? '*TODOS';
$infoRamPagadas = $_GET['pagados'] ?? null;
$esConsultas = $_GET['esConsultas'] ?? '0';
$claveCampo = $_GET['campo'] ?? 'SOLICITUD / EXPEDIENTE';
$datoBuscar = $_GET['datoBuscar'] ?? null;

//*cuando consulta es 1 esta en modo consulta

if ($esConsultas == '1' and $claveCampo == 'USUARIO') {
    $usuarioFiltro = $_GET['datoBuscar'] ?? null;
} else {
    if ($esConsultas == '0') {
        $usuarioFiltro = $_SESSION['user_name'] ?? '';
    }
}

$limit = (int)($_GET['limit'] ?? 10);
$page = (int)($_GET['page'] ?? 1);
$offset = ($page - 1) * $limit;

$condicionEstado = '';
//*SI NO HAY ESTADO SELECCIONADO NO SE PONE EL FILTRO POR ESTADO.
if ($idEstado != '*TODOS') {
    $condicionEstado .= " AND soli.Estado_Formulario = '" . $idEstado . "'";
}

//*SI ES INGRESO
$condicionUsuario = '';
if ($esConsultas == '0') {
    $condicionUsuario .= " AND ( (soli.Usuario_Acepta = '" . $usuarioFiltro . "' 
        OR soli.Usuario_Creacion = '" . $usuarioFiltro . "' 
        OR (SELECT COUNT(*) FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_RAM_A_COMPARTIR] comp 
            WHERE soli.ID_Formulario_Solicitud = comp.ID_Formulario_Solicitud 
            AND comp.Usuario_Comparte = '" . $usuarioFiltro . "' 
            AND comp.Estado_Formulario = '" . $idEstado . "') > 0
        )
        AND (SELECT COUNT(*) FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Estados_User] es 
            WHERE soli.Estado_Formulario = es.ID_Estado AND es.estado = 1) > 0
    )";
} else {

    //*cuando es consulta
    if (isset($usuarioFiltro) && $usuarioFiltro != '') {
        $condicionUsuario .= " AND (
		(soli.Usuario_Acepta = '" . $usuarioFiltro . "') -- PARAMETRO USUARIO
    OR  (soli.Usuario_Creacion = '" . $usuarioFiltro . "') -- PARAMETRO USUARIO1
	OR  (SELECT COUNT(*) FROM[IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_RAM_A_COMPARTIR] comp
		where soli.ID_Formulario_Solicitud = comp.ID_Formulario_Solicitud 
        AND comp.Usuario_Comparte = '" . $usuarioFiltro . "') > 0 -- PARAMETRO USUARIO2 y Estado_Formulario
		)";
    }
}
// echo 'infoRamPagadas: ' . $infoRamPagadas . '</br>';
$condicionPagadas = '';
if ($infoRamPagadas == 'ramsPagadas') {
    $condicionPagadas .= " AND (SELECT COUNT(*) FROM [IHTT_Webservice].[dbo].[TB_AvisoCobroEnc] ac 
WHERE AC.ID_Solicitud = soli.ID_Formulario_Solicitud AND AC.AvisoCobroEstado = 2) > 0";
}

//***************************************************************************/
//*ENCARGADA DE UNIR LAS CONDICIONES AL QUERY SEGUN EL CAMPO SELECCIONADO
//***************************************************************************/
function parametros($datoBuscar, $claveCampo)
{
    $condicion = '';
    if ($datoBuscar != null && $datoBuscar != '') {

        switch ($claveCampo) {
            case 'SOLICITUD / EXPEDIENTE':
                $condicion .= " AND (SELECT COUNT(*) FROM [IHTT_DB].[dbo].[TB_Expedientes] ex 
        WHERE soli.ID_Formulario_Solicitud = ex.Preforma and
        (ex.ID_Solicitud = '" . $datoBuscar . "' or ex.ID_Expediente = '" . $datoBuscar . "')) > 0 ";
                break;
            case 'CONCESION':
                $condicion .= " AND (SELECT COUNT(*) FROM [IHTT_PREFORMA].[dbo].[TB_Solicitud] slt 
            WHERE soli.ID_Formulario_Solicitud = slt.ID_Formulario_Solicitud and
            (isnull(slt.N_Certificado ,'') = '" . $datoBuscar . "' or isnull(slt.N_Permiso_Especial,'') = '" . $datoBuscar . "' 
            or isnull(slt.Permiso_Explotacion,'') = '" . $datoBuscar . "') ) > 0";
                break;
            case 'FSL / RAM':
                $condicion .= " AND soli.ID_Formulario_Solicitud = '" . $datoBuscar . "'"; // PARAMETRO DE RAM
                break;
            case 'NOMBRE_SOLICITUD':
                $condicion .= " AND (soli.Denominacion_Social like '%" . $datoBuscar . "%' or soli.Nombre_Solicitante like '%" . $datoBuscar . "%')"; // PARAMETRO DE NOMBRE
                break;
            case 'RTN_SOLICITUD':
                $condicion .= " AND soli.RTN_Solicitante = '" . $datoBuscar . "'"; // PARAMETRO DE RTN
                break;
            case 'PLACA / PLACA_REPLAQUEO':
                $condicion .= "AND (
                    EXISTS (
                        SELECT 1
                        FROM [IHTT_PREFORMA].[dbo].[TB_Vehiculo] v 
                        WHERE soli.ID_Formulario_Solicitud = v.ID_Formulario_Solicitud 
                        AND (
                            ISNULL(v.ID_Placa, '') = '" . $datoBuscar . "' 
                            OR ISNULL(v.ID_Placa_Antes_Replaqueo, '') = '" . $datoBuscar . "'
                        )
                    )
                    OR EXISTS (
                        SELECT 1
                        FROM [IHTT_DB].[dbo].[TB_Solicitud_Vehiculo_Entra] v
                        INNER JOIN [IHTT_DB].[dbo].[TB_Expedientes] e ON e.ID_Solicitud = v.ID_Solicitud
                        WHERE e.Preforma = soli.ID_Formulario_Solicitud 
                        AND (
                            ISNULL(v.ID_Placa, '') = '" . $datoBuscar . "' 
                            OR ISNULL(v.ID_Placa_Antes_Replaqueo, '') = '" . $datoBuscar . "'
                        )
                    )
                )
                AND (
                    EXISTS (
                        SELECT 1
                        FROM [IHTT_PREFORMA].[dbo].[TB_Vehiculo] v 
                        WHERE soli.ID_Formulario_Solicitud = v.ID_Formulario_Solicitud 
                        AND (
                            ISNULL(v.ID_Placa, '') = '" . $datoBuscar . "' 
                            OR ISNULL(v.ID_Placa_Antes_Replaqueo, '') = '" . $datoBuscar . "'
                        )
                    )
                    OR EXISTS (
                        SELECT 1
                        FROM [IHTT_DB].[dbo].[TB_Solicitud_Vehiculo_Entra] v
                        INNER JOIN [IHTT_DB].[dbo].[TB_Expedientes] e ON e.ID_Solicitud = v.ID_Solicitud
                        WHERE e.Preforma = soli.ID_Formulario_Solicitud 
                        AND (
                            ISNULL(v.ID_Placa, '') = '" . $datoBuscar . "' 
                            OR ISNULL(v.ID_Placa_Antes_Replaqueo, '') = '" . $datoBuscar . "'
                        )
                    )
                )";
                break;
        }
    }
    return $condicion;
}

$condicionBusqueda = parametros($datoBuscar, $claveCampo);

//*********************************************************/
//*QUERY BASE PARA OBTENER LOS DATOS DE LA TABLA DINAMICA
//*********************************************************/
$query = "SELECT 
    soli.ID_Formulario_Solicitud AS SOLICITUD, 
    soli.Nombre_Solicitante AS NOMBRE_SOLICITUD, 
    soli.RTN_Solicitante AS RTN_SOLICITUD,
    (SELECT TOP 1 v.ID_Placa
             FROM [IHTT_PREFORMA].[dbo].[TB_Vehiculo] v
             WHERE v.ID_Formulario_Solicitud = soli.ID_Formulario_Solicitud AND v.estado = 'NORMAL'
             ORDER BY v.ID) AS PLACA,
	(SELECT TOP 1 v.ID_Placa
             FROM [IHTT_PREFORMA].[dbo].[TB_Vehiculo] v
             WHERE v.ID_Formulario_Solicitud = soli.ID_Formulario_Solicitud AND v.estado = 'SALE'
             ORDER BY v.ID) AS PLACA_SALE,
	(SELECT TOP 1 v.ID_Placa
             FROM [IHTT_PREFORMA].[dbo].[TB_Vehiculo] v
             WHERE v.ID_Formulario_Solicitud = soli.ID_Formulario_Solicitud AND v.estado = 'ENTRA'
             ORDER BY v.ID) AS PLACA_ENTRA,

	(SELECT TOP 1 v.ID_Placa
             FROM [IHTT_DB].[dbo].[TB_Solicitud_Vehiculo_Entra] v,[IHTT_DB].[dbo].[TB_Expedientes] E
             WHERE E.Preforma = soli.ID_Formulario_Solicitud and e.ID_Solicitud = v.ID_Solicitud
             ORDER BY v.ID) AS PLACA_ENTRA_EXP,
    (SELECT Acronimo 
     FROM [IHTT_RRHH].[dbo].[TB_Ciudades] c 
     WHERE c.Codigo_Ciudad = soli.Codigo_Ciudad) AS CIUDAD,
    soli.Estado_Formulario AS ESTADO,
    -- soli.Observaciones as OBSERVACIONES,
    soli.Sistema_Fecha AS FECHA, 
    soli.usuario_creacion AS USUARIO_CREACION,
    soli.Usuario_Acepta AS USUARIO_ACEPTA,
    soli.Es_Renovacion_Automatica AS RA,
    (SELECT TOP 1 CONCAT(ISNULL(AC.CodigoAvisoCobro, 0), '-', ISNULL(AC.AvisoCobroEstado, '')) 
     FROM [IHTT_Webservice].[dbo].[TB_AvisoCobroEnc] AC
     WHERE AC.ID_Solicitud = soli.ID_Formulario_Solicitud AND AC.AvisoCobroEstado != 3) 
	 AS Aviso_Cobro,
    (SELECT STUFF((
        SELECT ', ' + RC.Usuario_Comparte
        FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_RAM_A_COMPARTIR] RC
        WHERE RC.ID_Formulario_Solicitud = soli.ID_Formulario_Solicitud AND
		RC.Estado_Formulario = 'IDE-1'
        FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 2, '')
    ) AS USUARIOS_COMPARTIDOS,
    (SELECT TOP 1 v3.estado CO
     FROM [IHTT_PREFORMA].[dbo].[TB_Vehiculo] v3 
     WHERE v3.ID_Formulario_Solicitud = soli.ID_Formulario_Solicitud) AS ESTADO_PLACA,
    -- CONCESIONES
    (
        SELECT COUNT(DISTINCT 
            CASE 
                WHEN ETT.Permiso_Explotacion != '' THEN ETT.Certificado_Operacion
                ELSE ETT.N_Permiso_Especial 
            END
        )
        FROM [IHTT_DB].[dbo].[TB_Expediente_X_Tipo_Tramite] ETT 
        WHERE ETT.ID_Solicitud = soli.ID_Formulario_Solicitud
    ) AS CONCESIONES,
    -- TRAMITES
        (
            SELECT COUNT(*)
            FROM [IHTT_DB].[dbo].[TB_Expediente_X_Tipo_Tramite] ETT 
            WHERE ETT.ID_Solicitud = soli.ID_Formulario_Solicitud
        ) AS TRAMITES
    FROM 
        [IHTT_PREFORMA].[dbo].[TB_Solicitante] AS soli
    JOIN 
        [IHTT_PREFORMA].[dbo].[TB_Apoderado_Legal] AS a 
        ON soli.ID_Formulario_Solicitud = a.ID_Formulario_Solicitud
    JOIN 
        [IHTT_PREFORMA].[dbo].[TB_Estados] AS e
        ON soli.Estado_Formulario = e.ID_Estado
    WHERE 
         soli.Es_Renovacion_Automatica = 1
         $condicionEstado
         $condicionUsuario
         $condicionPagadas
         $condicionBusqueda
         ";
$query .= " ORDER BY soli.ID_Formulario_Solicitud DESC OFFSET :offset ROWS FETCH NEXT :limit ROWS ONLY";

$stmt = $db->prepare($query);
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);

$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$datos = [];

//* Diccionario de estados descriptivos
$descripcionEstado = [
    'IDE-1' => 'EN PROCESO',
    'IDE-2' => 'FINALIZADO',
    'IDE-3' => 'CANCELADO',
    'IDE-4' => 'INADMISIÓN',
    'IDE-5' => 'REQUERIDO',
    'IDE-6' => 'DESISTIMIENTO AUTOMÁTICO',
    'IDE-7' => 'EN VENTANILLA',
    'IDE-8' => 'RETROTRAÍDO',
];

try {
    if (!empty($results)) {
        // Transformar cada fila
        foreach ($results as $fila) {
            $fila['ESTADO'] = $descripcionEstado[$fila['ESTADO']] ?? $fila['ESTADO'];
            $fila['RA'] = $fila['RA'] == 1 ? 'Sí' : 'No';
            // $datos[] = array_values($fila);
            $dato[] = $fila;
        }
        //**************************************************/
        //***Consulta para obtener el total de registros ***/
        //**************************************************/
        $consultaTotal = "SELECT COUNT(*) AS total
        FROM 
            [IHTT_PREFORMA].[dbo].[TB_Solicitante] AS soli
        JOIN 
            [IHTT_PREFORMA].[dbo].[TB_Apoderado_Legal] AS a 
            ON soli.ID_Formulario_Solicitud = a.ID_Formulario_Solicitud
        JOIN 
            [IHTT_PREFORMA].[dbo].[TB_Estados] AS e
            ON soli.Estado_Formulario = e.ID_Estado
        WHERE 
        soli.Es_Renovacion_Automatica = 1
        $condicionEstado
        $condicionUsuario
        $condicionPagadas
        $condicionBusqueda
        ";

        $total = $db->prepare($consultaTotal);
        $total->execute();
        $totalRows = $total->fetchColumn();

        // Enviar respuesta JSON
        echo json_encode([
            'mensaje' => '',
            'encabezados' => array_keys($results[0]),
            // 'datos' => $datos,
            'dato' => $dato,
            'totalRows' => $totalRows,
        ]);
    } else {
        echo json_encode([
            'mensaje' => 'no hay valores para este estado o búsqueda',
            'encabezados' => '',
            'datos' => '',
            'totalRows' => '',
        ]);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error en la consulta query_tabla_dinamica: ' . $e->getMessage()]);
}