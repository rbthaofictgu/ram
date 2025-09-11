<?php
declare(strict_types=1);

// 1) Conexión PDO ya creada en $db (o inclúyela aquí)
require_once("../config/conexion.php");

// 2) Leer JSON del cuerpo y convertirlo a array PHP
$raw = file_get_contents('php://input');

$payload = json_decode($raw, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    exit('JSON inválido: ' . json_last_error_msg());
}


// 3) Llamar a la función que te generé
try {
    cargarRAM($db, $payload);
    echo "OK: " . count($payload) . " registros procesados.";
} catch (Throwable $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage();
}

/**
 * Inserta registros en TB_Solicitud y TB_Vehiculo desde un arreglo dado.
 * Requiere: $db => instancia PDO (sqlsrv) ya conectada.
 * N_Certificado y Permiso_Explotacion SIEMPRE en blanco ('') en ambas tablas.
 */


// =================== HELPERS ===================
function nvl($v) {
    if ($v === null) return null;
    if (is_string($v)) {
        $t = trim($v);
        return ($t === '' ? null : $t);
    }
    return $v;
}
function toFloatOrNull($v) {
    $v = nvl($v);
    if ($v === null) return null;
    $v = str_replace([' ', ','], ['', ''], $v);
    if ($v === '.00') $v = '0';
    return is_numeric($v) ? (float)$v : null;
}
function codeBeforeArrow($str) {
    $str = (string)$str;
    $parts = explode('=>', $str);
    return trim($parts[0] ?? $str);
}

/**
 * @param PDO   $db     Conexión PDO ya abierta (driver sqlsrv).
 * @param array $items  Arreglo de unidades (como las del JSON adjunto).
 */
function cargarRAM(PDO $db, array $items): void
{

    define('FIX_ID_MODALIDAD', 'MOD-15');
    define('FIX_SISTEMA_IP', '190.130.16.247');
    define('FIX_SISTEMA_FECHA_SOLICITUD', '2025-09-10 12:27:25.987');
    define('FIX_ID_TIPO_CATEGORIA', 'CENE');
    define('FIX_TIPO_SERVICIO', 'STE');
    define('FIX_ES_RENOVACION_AUTOMATICA', 1);
    define('FIX_ORIGINADO_EN_VENTANILLA', 1);
    define('FIX_ESTA_FIJADO', 0);
    define('FIX_ESTA_INADMITIDO', 0);
    define('FIX_SISTEMA_USUARIO', 'ymejia');
    define('FIX_ESTA_BORRADO', 0);

    define('FIX_ESTADO_VEHICULO', 'NORMAL');
    define('FIX_SISTEMA_FECHA_VEHICULO', '2025-09-10 12:27:25.983');
    define('FIX_REVISION', null);    

    // Aplana si viene como [[ {...}, {...} ]]
    if (isset($items[0]) && is_array($items[0]) && isset($items[0][0])) {
        $items = $items[0];
    }

    // ---------- PREPARE SENTENCIAS ----------
    $sqlSolicitud = "
        INSERT INTO [IHTT_PREFORMA].[dbo].[TB_Solicitud] (
            ID_Formulario_Solicitud,
            ID_Tramite,
            ID_Modalidad,
            ID_Tipo_Servicio,
            N_Certificado,
            Permiso_Explotacion,
            Observaciones,
            Sistema_Fecha,
            Sistema_IP,
            ID_Tipo_Categoria,
            N_Permiso_Especial,
            Tipo_Servicio,
            Es_Renovacion_Automatica,
            Originado_En_Ventanilla,
            estaFijado,
            estaInadmitido,
            Sistema_Usuario,
            estaBorrado
        ) VALUES (
            :ID_Formulario_Solicitud,
            :ID_Tramite,
            :ID_Modalidad,
            :ID_Tipo_Servicio,
            :N_Certificado,
            :Permiso_Explotacion,
            :Observaciones,
            :Sistema_Fecha,
            :Sistema_IP,
            :ID_Tipo_Categoria,
            :N_Permiso_Especial,
            :Tipo_Servicio,
            :Es_Renovacion_Automatica,
            :Originado_En_Ventanilla,
            :estaFijado,
            :estaInadmitido,
            :Sistema_Usuario,
            :estaBorrado
        );
    ";
    $stSolicitud = $db->prepare($sqlSolicitud);

    $sqlVehiculo = "
        INSERT INTO [IHTT_PREFORMA].[dbo].[TB_Vehiculo] (
            ID_Formulario_Solicitud,
            RTN_Propietario,
            Nombre_Propietario,
            ID_Placa,
            ID_Marca,
            Anio,
            Modelo,
            Tipo_Vehiculo,
            ID_Color,
            Motor,
            Chasis,
            Estado,
            Sistema_Fecha,
            Permiso_Explotacion,
            Certificado_Operacion,
            VIN,
            Combustible,
            Alto,
            Ancho,
            Largo,
            Capacidad_Carga,
            Peso_Unidad,
            ID_Placa_Antes_Replaqueo,
            Permiso_Especial,
            Sistema_Usuario,
            Revision
        ) VALUES (
            :ID_Formulario_Solicitud,
            :RTN_Propietario,
            :Nombre_Propietario,
            :ID_Placa,
            :ID_Marca,
            :Anio,
            :Modelo,
            :Tipo_Vehiculo,
            :ID_Color,
            :Motor,
            :Chasis,
            :Estado,
            :Sistema_Fecha,
            :Permiso_Explotacion,
            :Certificado_Operacion,
            :VIN,
            :Combustible,
            :Alto,
            :Ancho,
            :Largo,
            :Capacidad_Carga,
            :Peso_Unidad,
            :ID_Placa_Antes_Replaqueo,
            :Permiso_Especial,
            :Sistema_Usuario,
            :Revision
        );
    ";
    $stVehiculo = $db->prepare($sqlVehiculo);

    try {
        $db->beginTransaction();

        foreach ($items as $item) {
            $tramite = $item['Tramites'][0] ?? [];
            $unidad  = $item['Unidad'] ?? [];

            // -------- Solicitud --------
            $idFormulario = nvl($item['ID_Formulario_Solicitud'] ?? $unidad['ID_Formulario_Solicitud'] ?? null);
            $idTramite    = nvl($tramite['ID_Tramite'] ?? $tramite['Codigo'] ?? null);

            $paramsSolicitud = [
                ':ID_Formulario_Solicitud'  => $idFormulario,
                ':ID_Tramite'               => $idTramite,
                ':ID_Modalidad'             => FIX_ID_MODALIDAD,
                ':ID_Tipo_Servicio'         => FIX_TIPO_SERVICIO,
                ':N_Certificado'            => '',                            // SIEMPRE BLANCO
                ':Permiso_Explotacion'      => '',                            // SIEMPRE BLANCO
                ':Observaciones'            => null,
                ':Sistema_Fecha'            => FIX_SISTEMA_FECHA_SOLICITUD,
                ':Sistema_IP'               => FIX_SISTEMA_IP,
                ':ID_Tipo_Categoria'        => FIX_ID_TIPO_CATEGORIA,
                ':N_Permiso_Especial'       => nvl($item['Concesion'] ?? null),
                ':Tipo_Servicio'            => FIX_TIPO_SERVICIO,
                ':Es_Renovacion_Automatica' => FIX_ES_RENOVACION_AUTOMATICA,
                ':Originado_En_Ventanilla'  => FIX_ORIGINADO_EN_VENTANILLA,
                ':estaFijado'               => FIX_ESTA_FIJADO,
                ':estaInadmitido'           => FIX_ESTA_INADMITIDO,
                ':Sistema_Usuario'          => FIX_SISTEMA_USUARIO,
                ':estaBorrado'              => FIX_ESTA_BORRADO,
            ];
            $stSolicitud->execute($paramsSolicitud);

            // -------- Vehículo --------
            $rtn       = nvl($unidad['RTN_Propietario'] ?? null);
            $nomProp   = nvl($unidad['Nombre_Propietario'] ?? null);
            $placa     = nvl($unidad['ID_Placa'] ?? $item['Placa'] ?? null);
            $marcaRaw  = nvl($unidad['Marca'] ?? $unidad['8'] ?? null);
            $idMarca   = $marcaRaw !== null ? codeBeforeArrow($marcaRaw) : null;
            $anio      = nvl($unidad['Anio'] ?? null);
            $modelo    = nvl($unidad['Modelo'] ?? null);
            $tipoVeh   = nvl($unidad['Tipo_Vehiculo'] ?? null);
            $colorRaw  = nvl($unidad['Color'] ?? $unidad['12'] ?? null);
            $idColor   = $colorRaw !== null ? codeBeforeArrow($colorRaw) : null;
            $motor     = nvl($unidad['Motor'] ?? null);
            $chasis    = nvl($unidad['Chasis'] ?? null);
            $vin       = nvl($unidad['VIN'] ?? null);
            $comb      = nvl($unidad['Combustible'] ?? null);
            $alto      = toFloatOrNull($unidad['Alto'] ?? null);
            $ancho     = toFloatOrNull($unidad['Ancho'] ?? null);
            $largo     = toFloatOrNull($unidad['Largo'] ?? null);
            $capCarga  = toFloatOrNull($unidad['Capacidad_Carga'] ?? null);
            $pesoUni   = toFloatOrNull($unidad['Peso_Unidad'] ?? null);
            $placaAnt  = nvl($unidad['ID_Placa_Antes_Replaqueo'] ?? null);

            $paramsVehiculo = [
                ':ID_Formulario_Solicitud' => $idFormulario,
                ':RTN_Propietario'         => $rtn,
                ':Nombre_Propietario'      => $nomProp,
                ':ID_Placa'                => $placa,
                ':ID_Marca'                => $idMarca,
                ':Anio'                    => $anio,
                ':Modelo'                  => $modelo,
                ':Tipo_Vehiculo'           => $tipoVeh,
                ':ID_Color'                => $idColor,
                ':Motor'                   => $motor,
                ':Chasis'                  => $chasis,
                ':Estado'                  => FIX_ESTADO_VEHICULO,
                ':Sistema_Fecha'           => FIX_SISTEMA_FECHA_VEHICULO,
                ':Permiso_Explotacion'     => '',                 // SIEMPRE BLANCO
                ':Certificado_Operacion'   => '',                 // si también debe ir blanco
                ':VIN'                     => $vin,
                ':Combustible'             => $comb,
                ':Alto'                    => $alto,
                ':Ancho'                   => $ancho,
                ':Largo'                   => $largo,
                ':Capacidad_Carga'         => $capCarga,
                ':Peso_Unidad'             => $pesoUni,
                ':ID_Placa_Antes_Replaqueo'=> $placaAnt,
                ':Permiso_Especial'        => nvl($item['Concesion'] ?? null),
                ':Sistema_Usuario'         => FIX_SISTEMA_USUARIO,
                ':Revision'                => FIX_REVISION,
            ];
            $stVehiculo->execute($paramsVehiculo);
            // echo $idColor . '<br/>';
            // echo $idMarca . '<br/>';
            // echo $idFormulario  . '<br/>';
            // echo $item['Concesion'] . '<br/>';
        }
        $db->commit();
        echo "Carga exitosa: " . count($items) . " solicitudes y " . count($items) . " vehículos insertados.\n";
    } catch (Throwable $e) {
        $db->rollBack();
        throw $e; // permite manejar el error desde el caller
    }
}