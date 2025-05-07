

<?php 
    //* archivo de configuracion de las variables globales
	require_once('configuracion/configuracion.php');
    //*archivo de configuracion de la base de datos
	require_once('../config/conexion.php');

//* llamdo del id
if (isset($_GET['ram'])) {
    $idFormularioSolicitud = $_GET['ram'];
}

/**SELECT soli.ID, soli.ID_Formulario_Solicitud, soli.ID_Formulario_Solicitud_Encrypted, 
soli.Nombre_Solicitante, soli.ID_Tipo_Solicitante, soli.RTN_Solicitante, soli.Domicilo_Solicitante, soli.Denominacion_Social, 
soli.ID_Aldea, soli.Telefono_Solicitante, soli.Email_Solicitante, soli.Numero_Escritura, soli.RTN_Notario, 
soli.Notario_Autorizante, soli.Lugar_Constitucion, soli.Fecha_Constitucion, soli.Estado_Formulario, soli.Fecha_Cancelacion, 
soli.Observaciones, soli.Usuario_Cancelacion, soli.Aviso_Cobro, soli.Presentacion_Documentos, soli.Sistema_Fecha, 
soli.Etapa_Preforma, soli.Usuario_Acepta, soli.Fecha_Aceptacion, soli.Codigo_Usuario_Acepta, soli.Observacion_Cancelacion,
soli.Usuario_Inadmision, soli.Fecha_Inadmision, soli.Observacion_Inadmision, soli.Tipo_Solicitud, soli.Entrega_Ubicacion, 
soli.Usuario_Creacion, soli.Codigo_Ciudad, soli.Originado_En_Ventanilla, soli.Es_Renovacion_Automatica
 */
//* definiendo la consulta SQL
$query_rs_llamado = "SELECT 
soli.ID as ID, soli.ID_Formulario_Solicitud AS SOL, 
soli.Nombre_Solicitante AS NOMBRE_SOL, 
soli.ID_Tipo_Solicitante AS IS_TIPO_SOL, 
soli.RTN_Solicitante AS RTN_SOL, 
soli.Domicilo_Solicitante AS DOMICILIO_SOL, 
soli.Denominacion_Social AS DENOMINACION_SOL, 
soli.Telefono_Solicitante AS TEL_SOL, 
soli.Email_Solicitante AS EMAIL_SOL,
soli.Estado_Formulario AS ESTADO, 
soli.Fecha_Cancelacion AS FECHA_CAN, 
soli.Observaciones AS OBSER,
soli.Aviso_Cobro AS AVISO_COBRO, 
soli.Sistema_Fecha AS FECHA, 
soli.Etapa_Preforma ETAPA, 
soli.Tipo_Solicitud TIPO_SOL,
soli.Usuario_Creacion USER_CREACION, 
soli.Originado_En_Ventanilla AS ORIGEN, 
soli.Es_Renovacion_Automatica AS RA
FROM [IHTT_PREFORMA].[dbo].[TB_Solicitante] AS soli
JOIN [IHTT_PREFORMA].[dbo].[TB_Apoderado_Legal] AS a 
    ON soli.ID_Formulario_Solicitud = a.ID_Formulario_Solicitud
JOIN [IHTT_PREFORMA].[dbo].[TB_Solicitud] AS sol 
    ON soli.ID_Formulario_Solicitud = sol.ID_Formulario_Solicitud
WHERE soli.Estado_Formulario = 'IDE-7' 
AND soli.ID_Formulario_Solicitud = :idFormularioSolicitud
ORDER BY sol.Sistema_Fecha DESC";

//* Preparar y ejecutar la consulta
try {
    $dataQuery = $db->prepare($query_rs_llamado);
    $dataQuery->execute(array('idFormularioSolicitud' =>  $idFormularioSolicitud));

    //* Obtener los resultados en un array
    $resultados = $dataQuery->fetchAll(PDO::FETCH_ASSOC);
    
    //*Crear arreglos para encabezados y datos
    //*obteniendo la llave de cada elemento en la posicion 0 con un array_keys y inicializando arreglos
    $encabezados = array_keys($resultados[0]); 
    $datos = [];
    $todo=[];
    
    //*recoriendo cada elemento
    foreach ($resultados as $fila) {
        //*obteniendo el valor de cada llame del arreglo con array_values
        $datos[] = array_values($fila);
        $todo[]=$fila;
    }
    //* creando un solo arreglos
    $valores = [
        'encabezados' => $encabezados,
        'datos' => $datos,
        'todo'=>$todo
    ];
    //* Enviar la respuesta en formato JSON
    echo json_encode($valores);
} catch (PDOException $e) {
    echo "Error en la consulta de query_rs_llamado: " . $e->getMessage();
}




?>