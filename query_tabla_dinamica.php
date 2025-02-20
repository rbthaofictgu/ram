<?php
//* archivo de configuracion de las variables globales
require_once('configuracion/configuracion.php');
//* archivo de configuracion de la base de datos
require_once('../config/conexion.php');

//*obteniendo estado
$idEstado = $_GET['estado'] ?? 'IDE-1';
//* arreglo de comparacion para el tipo de busqueda de campo
$COMPARACION = [
    'SOLICITUD' => 'soli.ID_Formulario_Solicitud',
    'NOMBRE_SOLICITUD' => 'soli.Nombre_Solicitante',
    'RTN_SOL' => 'soli.Nombre_Solicitante',
    'PLACA' => 'v.[ID_Placa]',
];
//* asignando a valor1 el campo si hay si no por defecto se asigna el de solicitud "soli.ID_Formulario_Solicitud"
$valor1 = $_GET['campo'] ?? 'soli.ID_Formulario_Solicitud';

//* Verificar si el valor de $valor1 coincide con alguna de las claves del arreglo de comparacion.
foreach ($COMPARACION as $key => $campo) {
    if ($valor1 == $key) {
        //* Si se encuentra una coincidencia, asignamos el valor correspondiente de $COMPARACION a $campo
        $campo = $campo;
        break; //* Rompe el bucle una vez que se encuentra la coincidencia
    }
}

$datoBuscar = $_GET['datoBuscar'] ?? NULL;
//* asignando el valor del limite del query si no se asigna 10 por default
$limit = $_GET['limit'] ?? 10;
//* asignando el numeo de pagina
$page = $_GET['page'] ?? 1;
//*calculando offset para saber cuales registros devolver
$offset = ($page - 1) * $limit;


//* Definiendo la consulta SQL
$query_rs_llamado = "SELECT DISTINCT
-- soli.ID AS ID, 
soli.ID_Formulario_Solicitud AS SOLICITUD, 
soli.Nombre_Solicitante AS NOMBRE_SOLICITUD, 
soli.RTN_Solicitante AS RTN_SOLICITUD, 
(SELECT TOP 1 v.[ID_Placa] FROM [IHTT_PREFORMA].[dbo].[TB_Vehiculo] AS v 
     WHERE v.ID_Formulario_Solicitud = soli.ID_Formulario_Solicitud) AS PLACA,
(select [DESC_Ciudad] FROM [IHTT_RRHH].[dbo].[TB_Ciudades] as c
where c.Codigo_Ciudad=soli.Codigo_Ciudad) AS CIUDAD,
-- soli.Originado_En_Ventanilla AS ORIGEN, 
soli.Estado_Formulario AS ESTADO, 
soli.Sistema_Fecha AS FECHA, 
-- soli.Etapa_Preforma AS ETAPA, 
soli.Es_Renovacion_Automatica AS RA,
AC.CodigoAvisoCobro,
AC.AvisoCobroEstado
FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Estados_User] es,
[IHTT_PREFORMA].[dbo].[TB_Solicitante] AS soli
left outer join [IHTT_Webservice].[dbo].[TB_AvisoCobroEnc] AC on AC.ID_Solicitud = soli.ID_Formulario_Solicitud
JOIN [IHTT_PREFORMA].[dbo].[TB_Apoderado_Legal] AS a 
    ON soli.ID_Formulario_Solicitud = a.ID_Formulario_Solicitud
JOIN [IHTT_PREFORMA].[dbo].[TB_Solicitud] AS sol 
    ON soli.ID_Formulario_Solicitud = sol.ID_Formulario_Solicitud
JOIN [IHTT_PREFORMA].[dbo].[TB_Vehiculo] AS v 
    ON soli.ID_Formulario_Solicitud = v.ID_Formulario_Solicitud
WHERE es.Usuario = :Usuario and soli.Estado_Formulario = es.ID_Estado and es.estado = 1 and soli.Estado_Formulario = :idEstado";

//* Verificando si hay un campo de búsqueda
if (!empty($datoBuscar) && !empty($campo)) {
    //*si existe asignamos  el campo y la busqueda al query
    $query_rs_llamado .= " AND LOWER($campo) LIKE LOWER(:datoBuscar)";
}
//? nota:
//?OFFSET :offset ROWS omite un numero de filas especifico.
//?FETCH NEXT :limit ROWS ONLY: Limita el número de filas devueltas a la cantidad especificada en el parámetro :limit.

//*paginando los resultados
$query_rs_llamado .= " ORDER BY soli.ID_Formulario_Solicitud  DESC OFFSET :offset ROWS FETCH NEXT :limit ROWS ONLY";

try {
    //*Preparar la consulta
    $stmt = $db->prepare($query_rs_llamado);
    //*enviando los parametros
    $usu = 'ccaballero';
    $stmt->bindParam(':idEstado', $idEstado);
    $stmt->bindParam(':Usuario', $usu);
    //* si existen los parametros los enviamos
    if (!empty($datoBuscar) && !empty($campo)) {
        $likeDatoBuscar = "%$datoBuscar%";
        $stmt->bindParam(':datoBuscar', $likeDatoBuscar);
    }
    //*enviamos parametros y especificamos que es un entero para que sql server lo reconosca
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    //*ejecutamos consulta.
    $stmt->execute();
    //*obtenemos datos de la consulta
    $respuesta = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //*inicializamos arreglos.
    $datos = [];
    $select = [];

    //* verificamos si hay respuesta
    if (!empty($respuesta)) {
        //*creamos un arreglo de estados que servira para hacer el cambio de los elementos 
        $descripcionEstado = [
            'IDE-1' => 'EN PROCESO',
            'IDE-2' => 'FINALIZADO',
            'IDE-3' => 'CANCELADO',
            'IDE-4' => 'INADMISION',
            'IDE-5' => 'REQUERIDO',
            'IDE-6' => 'DESISTIMINETO AUTOMATICO',
            'IDE-7' => 'EN VENTANILLA',
        ];
        //*recorrep¿mos repsuesta para asignar valores
        foreach ($respuesta as $fila) {
            //*cambiando los estados y usamos ?? para que permanesca el valor original si no hay dato;
            $fila['ESTADO'] = $descripcionEstado[$fila['ESTADO']] ?? $fila['ESTADO'];
            //* Convertir 'RA' a "Sí" o "No"
            $fila['RA'] = $fila['RA'] == 1 ? 'Sí' : 'No';
            // $fila['ORIGEN'] = $fila['ORIGEN'] == 1 ? 'Sí' : 'No';
            //*obteniendo el valor de cada llame del arreglo con array_values
            $datos[] = array_values($fila);
        }
        //*realizamos un segundo query para oibtener el total de filas de la consulta
        $totalQuery = "SELECT COUNT(*) FROM [IHTT_PREFORMA].[dbo].[TB_Solicitante] AS soli
        -- JOIN [IHTT_PREFORMA].[dbo].[TB_Apoderado_Legal] AS a 
        --     ON soli.ID_Formulario_Solicitud = a.ID_Formulario_Solicitud
        -- JOIN [IHTT_PREFORMA].[dbo].[TB_Solicitud] AS sol 
        --     ON soli.ID_Formulario_Solicitud = sol.ID_Formulario_Solicitud
        JOIN [IHTT_PREFORMA].[dbo].[TB_Vehiculo] AS v 
            ON soli.ID_Formulario_Solicitud = v.ID_Formulario_Solicitud
        WHERE soli.Estado_Formulario = :idEstado";
        //*aqui enviamos  completamos el segundo query si esisten los elementos
        if (!empty($datoBuscar) && !empty($campo)) {
            $totalQuery .= " AND LOWER($campo) LIKE LOWER(:datoBuscar)";
        }
        //*preparandoc seguinda consulta
        $totalStmt = $db->prepare($totalQuery);
        //*enviando parametros
        $totalStmt->bindParam(':idEstado', $idEstado);
        //* si existen los elemntos los enviamos.
        if (!empty($datoBuscar) && !empty($campo)) {
            $totalStmt->bindParam(':datoBuscar', $likeDatoBuscar);
        }
        //*ejecutamos consulta.
        $totalStmt->execute();
        //*resivimos respuesta.
        $totalRows = $totalStmt->fetchColumn();

        //*arreglo para enviar las claves ()value) de campo a buscar 
        $clavesValidas = ['SOLICITUD', 'NOMBRE_SOLICITUD', 'RTN_SOLICITUD', 'PLACA'];

        //?nota: el array_insert nos permite buscar las claves que estan en ambos arreglos
        //?nota: array_value nos permite convertir el objeto obtenido en array
        //?nota: array_keys nos permite obtenes las llaves de un objeto

        //*obtenemos los elmentos que coinciden con clasesvalidas
        $select = array_values(array_intersect(array_keys($respuesta[0]), $clavesValidas));
        $claves_deseadas=['SOLICITUD','NOMBRE_SOLICITUD','RTN_SOLICITUD','CIUDAD','ESTADO',' RA'];;
        // $resultado = array_intersect_key($respuesta[0], array_flip($claves_deseadas));

        //* Enviar la respuesta JSON
        $valores = [
            'mensaje' => '',
            'encabezados' => array_keys($respuesta[0]),
            'datos' => $datos,
            'totalRows' => $totalRows,
            'dataSelect' => $select
        ];
        //*enviando el arreglo de valors 
        echo json_encode($valores);
    } else {
        //* si bienen elementos en blanco se envia este arreglo bacio.
        $valores = [
            'mensaje' => 'no hay valores para este estado o busqueda',
            'encabezados' => '', // asumiendo que hay datos
            'datos' => '',
            'totalRows' => '',
            'dataSelect' => ''
        ];
        //* se envia el arreglo.
        echo json_encode($valores);
    }
} catch (PDOException $e) {
    //* capturando el error del llamo si es que hay error  */
    echo json_encode(['error' => 'Error en la consulta query_tabla_dinamica: ' . $e->getMessage()]);
}
