<?php
session_start();
//* archivo de configuracion de las variables globales
require_once('configuracion/configuracion.php');
//* archivo de configuracion de la base de datos
require_once('../config/conexion.php');

//*obteniendo estado
$idEstado = $_GET['estado'] ?? 'IDE-1';
$infoRamPagadas = $_GET['pagados'];
$esConsultas = isset($_GET['esConsultas']) ? $_GET['esConsultas'] : '0';

// echo json_encode($esConsultas).'</br>';
if (isset($esConsultas) == '1') {
    // $esConsultas = true;
    // echo json_encode($esConsultas);
    $COMPARACION = [
        'SOLICITUD' => 'soli.ID_Formulario_Solicitud',
        'NOMBRE_SOLICITUD' => 'soli.Nombre_Solicitante',
        'RTN_SOLICITUD' => 'soli.RTN_Solicitante',
        'PLACA' => 'v.ID_Placa',
        'PLACA1' => 'v.ID_Placa_Antes_Replaqueo',
        'USUARIO_CREACION' => 'soli.usuario_creacion',
        'USUARIO_ACEPTA' => 'soli.Usuario_Acepta',
    ];

} else {
    // $esConsultas = false;
    // echo json_encode($esConsultas);
    $COMPARACION = [
        'SOLICITUD' => 'soli.ID_Formulario_Solicitud',
        'NOMBRE_SOLICITUD' => 'soli.Nombre_Solicitante',
        'RTN_SOLICITUD' => 'soli.RTN_Solicitante',
        'PLACA' => 'v.ID_Placa',
        'PLACA1' => 'v.ID_Placa_Antes_Replaqueo'
    ];
}


//* asignando a valor1 el campo si hay si no por defecto se asigna el de solicitud "soli.ID_Formulario_Solicitud"
$valor1 = $_GET['campo'] ?? 'soli.ID_Formulario_Solicitud';

$campo = $COMPARACION['SOLICITUD']; // valor por defecto
$campo1 = $COMPARACION['SOLICITUD']; // valor por defecto
//* Verificar si el valor de $valor1 coincide con alguna de las claves del arreglo de comparacion.
foreach ($COMPARACION as $key => $val) {

    if ($valor1 == $key) {
        //* Si se encuentra una coincidencia, asignamos el valor correspondiente de $COMPARACION a $campo
        $campo = $val;
        if ($key == 'PLACA') {
            $campo1 = 'v.ID_Placa_Antes_Replaqueo';
        }
        break;
    }
}

$datoBuscar = $_GET['datoBuscar'] ?? NULL;
$datoBuscar1 = $_GET['datoBuscar'] ?? NULL;
//* asignando el valor del limite del query si no se asigna 10 por default
$limit = $_GET['limit'] ?? 10;
//* asignando el numeo de pagina
$page = $_GET['page'] ?? 1;
//*calculando offset para saber cuales registros devolver
$offset = ($page - 1) * $limit;


$joinsAdicionales = '';
$filtroAvisoCobro = '';
$condicionWhere = '';
if ($infoRamPagadas == 'ramsPagadas') {
    $joinsAdicionales .= " JOIN [IHTT_Webservice].[dbo].[TB_AvisoCobroEnc] AS AC 
                           ON AC.ID_Solicitud = soli.ID_Formulario_Solicitud ";
    $filtroAvisoCobro .= " AND AC.AvisoCobroEstado = 2 ";
}

if ($esConsultas == '1' and $idEstado == '*TODOS') {
    $condicionWhere = "WHERE es.estado = 1";
} else {
    $condicionWhere = "WHERE soli.Estado_Formulario = :idEstado2 AND es.estado = 1 AND soli.Es_Renovacion_Automatica=1";
}

if ($campo == 'v.ID_Placa' || $campo == 'v.ID_Placa_Antes_Replaqueo') {
    $query_rs_llamado = "SELECT DISTINCT
                soli.ID_Formulario_Solicitud AS SOLICITUD,
                soli.Nombre_Solicitante AS NOMBRE_SOLICITUD,
                soli.RTN_Solicitante AS RTN_SOLICITUD,
                v.ID_Placa AS PLACA,
                v.ID_Placa_Antes_Replaqueo AS PLACA_REPLAQUEO,
                soli.Estado_Formulario AS ESTADO,
                soli.Sistema_Fecha AS FECHA,
                soli.Es_Renovacion_Automatica AS RA,
                (SELECT TOP 1 CONCAT(isnull(AC.CodigoAvisoCobro,0),'-',isnull(AC.AvisoCobroEstado,''))
                FROM [IHTT_Webservice].[dbo].[TB_AvisoCobroEnc] AC
                WHERE AC.ID_Solicitud = soli.ID_Formulario_Solicitud) AS Aviso_Cobro,
                comp.estado AS COMPARTIDO,
                soli.usuario_creacion as USUARIO_CREACION,
				soli.Usuario_Acepta as USUARIO_ACEPTA,
                 (
                    SELECT STUFF((
                        SELECT ', ' + RC.Usuario_Comparte
                        FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_RAM_A_COMPARTIR] RC
                        WHERE RC.ID_Formulario_Solicitud = soli.ID_Formulario_Solicitud  and RC.Estado_Formulario =:idEstado
                        FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 2, ''
                    )
                ) AS USUARIOS_COMPARTIDOS
            FROM 
                [IHTT_PREFORMA].[dbo].[TB_Solicitante] AS soli
            JOIN 
                [IHTT_PREFORMA].[dbo].[TB_Apoderado_Legal] AS a 
                ON soli.ID_Formulario_Solicitud = a.ID_Formulario_Solicitud
            JOIN 
                [IHTT_PREFORMA].[dbo].[TB_Solicitud] AS sol 
                ON soli.ID_Formulario_Solicitud = sol.ID_Formulario_Solicitud
            LEFT OUTER JOIN 
                [IHTT_PREFORMA].[dbo].[TB_Vehiculo] v 
                ON v.ID_Formulario_Solicitud = soli.ID_Formulario_Solicitud 
                AND (v.Certificado_Operacion = sol.N_Certificado 
                    OR v.Permiso_Especial = sol.N_Permiso_Especial)
            JOIN 
                [IHTT_PREFORMA].[dbo].[TB_Estados] AS e
                ON soli.Estado_Formulario = e.ID_Estado
            JOIN 
                [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Estados_User] AS es
                ON soli.Estado_Formulario = es.ID_Estado
            LEFT JOIN 
                [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_RAM_A_COMPARTIR] AS comp
                ON comp.ID_Formulario_Solicitud = soli.ID_Formulario_Solicitud and  comp.Estado_Formulario = :idEstado1
        $joinsAdicionales
        $condicionWhere
        --  WHERE soli.Estado_Formulario = :idEstado2 AND es.estado = 1
        $filtroAvisoCobro
        ";
} else {
    $query_rs_llamado = "SELECT DISTINCT 
                soli.ID_Formulario_Solicitud AS SOLICITUD, 
                soli.Nombre_Solicitante AS NOMBRE_SOLICITUD, 
                soli.RTN_Solicitante AS RTN_SOLICITUD,
                (SELECT TOP 1 v.ID_Placa 
                FROM [IHTT_PREFORMA].[dbo].[TB_Vehiculo] AS v 
                WHERE v.ID_Formulario_Solicitud = soli.ID_Formulario_Solicitud) AS PLACA,
                (SELECT Acronimo 
                FROM [IHTT_RRHH].[dbo].[TB_Ciudades] c 
                WHERE c.Codigo_Ciudad = soli.Codigo_Ciudad) AS CIUDAD,
                soli.Estado_Formulario AS ESTADO,
                soli.Sistema_Fecha AS FECHA, 
                soli.Es_Renovacion_Automatica AS RA,
                (SELECT TOP 1 CONCAT(isnull(AC.CodigoAvisoCobro,0),'-',isnull(AC.AvisoCobroEstado,''))
                FROM [IHTT_Webservice].[dbo].[TB_AvisoCobroEnc] AC
                WHERE AC.ID_Solicitud = soli.ID_Formulario_Solicitud) AS Aviso_Cobro,
                comp.estado AS COMPARTIDO,
                soli.usuario_creacion as USUARIO_CREACION,
				soli.Usuario_Acepta as USUARIO_ACEPTA,
                 (
    SELECT STUFF((
        SELECT ', ' + RC.Usuario_Comparte
        FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_RAM_A_COMPARTIR] RC
        WHERE RC.ID_Formulario_Solicitud = soli.ID_Formulario_Solicitud and RC.Estado_Formulario =:idEstado
        FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 2, ''
    )
) AS USUARIOS_COMPARTIDOS
            FROM 
                [IHTT_PREFORMA].[dbo].[TB_Solicitante] AS soli
            JOIN 
                [IHTT_PREFORMA].[dbo].[TB_Apoderado_Legal] AS a 
                ON soli.ID_Formulario_Solicitud = a.ID_Formulario_Solicitud
            JOIN 
                [IHTT_PREFORMA].[dbo].[TB_Solicitud] AS sol  
                ON soli.ID_Formulario_Solicitud = sol.ID_Formulario_Solicitud 
            JOIN 
                [IHTT_PREFORMA].[dbo].[TB_Estados] AS e
                ON soli.Estado_Formulario = e.ID_Estado
            JOIN 
                [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Estados_User] AS es
                ON soli.Estado_Formulario = es.ID_Estado
            LEFT JOIN 
                [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_RAM_A_COMPARTIR] AS comp
                ON soli.ID_Formulario_Solicitud = comp.ID_Formulario_Solicitud AND comp.Estado_Formulario = :idEstado1
            $joinsAdicionales
            $condicionWhere
            -- WHERE soli.Estado_Formulario = :idEstado2 AND es.estado = 1 
            $filtroAvisoCobro
            ";
}

//*Que se pongan los filtros solo cuando si existe consulta
if ($esConsultas !== '1') {
    //comp.estado=1
    if ($idEstado == 'IDE-7') { //en ventanilla
        $query_rs_llamado .= " AND (soli.Usuario_Creacion=:usuario_creacion OR comp.Usuario_Comparte=:usuario_comparte)";
    } else {
        if ($idEstado == 'IDE-1') { //en proceso
            $query_rs_llamado .= " AND (soli.Usuario_Acepta=:usuario_acepta OR comp.Usuario_Comparte=:usuario_comparte)";
        } else {
            // if ($idEstado == 'IDE-2') { // finalizado
            //     $query_rs_llamado .= " AND soli.Usuario_finalizado=:usuario_finaliza";
            // } else {
            if ($idEstado == 'IDE-3') { //cancelado
                $query_rs_llamado .= " AND soli.Usuario_Cancelacion=:usuario_cancelacion";
            } else {
                if ($idEstado == 'IDE-4') { //inadmision
                    $query_rs_llamado .= " AND soli.Usuario_Inadmision=:usuario_inadmitido";
                } else {
                    // if ($idEstado == 'IDE-5') { //requerido
                    //     $query_rs_llamado .= " AND soli.Usuario_Requerido=:usuario_requerido";
                    // } else {
                    //     if ($idEstado == 'IDE-2') { //desestimiento
                    //         $query_rs_llamado .= " AND soli.Usuario_Desestimiento=:usuario_desestimiento";
                    //     } else {
                    //     }
                    // }
                }
            }
            // }
        }
    }
}

//and  soli.Usuario_Acepta='ccaballero'3

//* Verificando si hay un campo de búsqueda
if (!empty($datoBuscar) && !empty($campo)) {
    //  echo json_encode( $query_rs_llamado) .'</br>';
    //*si existe asignamos  el campo y la busqueda al query

    if ($campo == 'v.ID_Placa') {
        $query_rs_llamado .= " AND ((LOWER($campo) LIKE LOWER(:buscarPlaca1)) OR
    (ISNULL(v.ID_Placa_Antes_Replaqueo, '') != '' AND LOWER($campo1) LIKE LOWER(:buscarPlaca2)))";
    } else {
        $query_rs_llamado .= " AND LOWER($campo) LIKE LOWER(:buscarGeneral)";
    }
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
    $usu = $_SESSION['user_name']; //'ccaballero'
    $stmt->bindParam(':idEstado', $idEstado);
    $stmt->bindParam(':idEstado1', $idEstado);

    if ($idEstado !== '*TODOS') {
        $stmt->bindParam(':idEstado2', $idEstado);
    }
    // $stmt->bindParam(':Usuario', $usu);

    $usuario_actual = $_SESSION['user_name'];
    $usuario_creacion = $usuario_actual;
    $usuario_acepta = $usuario_actual;
    $usuario_finaliza = $usuario_actual;
    $usuario_cancelacion = $usuario_actual;
    $usuario_inadmitido = $usuario_actual;
    $usuario_requerido = $usuario_actual;
    $usuario_desestimiento = $usuario_actual;
    $usuario_comparte = $usuario_actual;

    //*que se envien los parametros sino existe la consulta.
    if ($esConsultas !== '1') {
        if ($idEstado == 'IDE-7') {
            $stmt->bindParam(':usuario_creacion', $usuario_creacion);
            $stmt->bindParam(':usuario_comparte', $usuario_comparte);
        } else {
            if ($idEstado == 'IDE-1') {
                $stmt->bindParam(':usuario_acepta', $usuario_acepta);
                $stmt->bindParam(':usuario_comparte', $usuario_comparte);
            } else {
                // if ($idEstado == 'IDE-2') {
                //     $stmt->bindParam(':usuario_finaliza', $usuario_finaliza);
                // } else {
                if ($idEstado == 'IDE-3') {
                    $stmt->bindParam(':usuario_cancelacion', $usuario_cancelacion);
                } else {
                    if ($idEstado == 'IDE-4') {
                        $stmt->bindParam(':usuario_inadmitido', $usuario_inadmitido);
                    } else {
                        // if ($idEstado == 'IDE-5') {
                        //     $stmt->bindParam(':usuario_requerido', $usuario_requerido);
                        // } else {
                        //         if ($idEstado == 'IDE-6') {
                        //             $stmt->bindParam(':usuario_desestimiento', $usuario_desestimiento);
                        //         }
                        // }
                    }
                }
                // }
            }
        }
    }
    //* si existen los parametros los enviamos
    if (!empty($datoBuscar) && !empty($campo)) {
        $likeDatoBuscar = "%$datoBuscar%";
        $likeDatoBuscar1 = "%$datoBuscar1%";

        if ($campo == 'v.ID_Placa') {
            $stmt->bindValue(':buscarPlaca1', $likeDatoBuscar);
            $stmt->bindValue(':buscarPlaca2', $likeDatoBuscar1);
        } else {
            $stmt->bindValue(':buscarGeneral', $likeDatoBuscar);
        }
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
            'IDE-4' => 'INADMISIÓN',
            'IDE-5' => 'REQUERIDO',
            'IDE-6' => 'DESISTIMINETO AUTOMÁTICO',
            'IDE-7' => 'EN VENTANILLA',
            'IDE-8' => 'RETROTRAIDO',
        ];
        //*recorrep¿mos repsuesta para asignar valores
        foreach ($respuesta as $fila) {
            //*cambiando los estados y usamos ?? para que permanesca el valor original si no hay dato;
            $fila['ESTADO'] = $descripcionEstado[$fila['ESTADO']] ?? $fila['ESTADO'];
            //* Convertir 'RA' a "Sí" o "No"
            $fila['RA'] = $fila['RA'] == 1 ? 'Sí' : 'No';
            // $fila['ORIGEN'] = $fila['ORIGEN'] == 1 ? 'Sí' : 'No';
            //*obteniendo el valor de cada llame del arreglo con array_values
            $value[] = $fila;
            $datos[] = array_values($fila);
        }
        //*realizamos un segundo query para oibtener el total de filas de la consulta
        $condicionWherePag = '';

        if ($esConsultas == '1' and $idEstado == '*TODOS') {
            $condicionWherePag = "WHERE es.estado = 1";
        } else {
            $condicionWherePag = "WHERE  soli.Estado_Formulario = :idEstado  AND es.estado = 1 AND soli.Es_Renovacion_Automatica=1";
        }

        $totalQuery = "SELECT COUNT(DISTINCT soli.ID_Formulario_Solicitud) AS cantidad
                        FROM [IHTT_PREFORMA].[dbo].[TB_Solicitante] AS soli
                        JOIN [IHTT_PREFORMA].[dbo].[TB_Apoderado_Legal] AS a 
                            ON soli.ID_Formulario_Solicitud = a.ID_Formulario_Solicitud
                        JOIN [IHTT_PREFORMA].[dbo].[TB_Solicitud] AS sol  
                            ON soli.ID_Formulario_Solicitud = sol.ID_Formulario_Solicitud 
                        JOIN [IHTT_PREFORMA].[dbo].[TB_Estados] AS e
                            ON soli.Estado_Formulario = e.ID_Estado
                        JOIN [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Estados_User] AS es
                            ON soli.Estado_Formulario = es.ID_Estado
                        LEFT JOIN [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_RAM_A_COMPARTIR] AS comp
                            ON comp.ID_Formulario_Solicitud = soli.ID_Formulario_Solicitud
                        LEFT JOIN [IHTT_PREFORMA].[dbo].[TB_Vehiculo] AS v 
                            ON v.ID_Formulario_Solicitud = soli.ID_Formulario_Solicitud
                            AND (v.Certificado_Operacion = sol.N_Certificado OR v.Permiso_Especial = sol.N_Permiso_Especial)
                            $condicionWherePag
                            ";
        //*aqui enviamos  completamos el segundo query si esisten los elementos
        if (!empty($datoBuscar) && !empty($campo)) {
            if ($campo == 'v.ID_Placa') {
                $totalQuery .= " AND ((LOWER($campo) LIKE LOWER(:buscarPlaca1)) OR
            (ISNULL(v.ID_Placa_Antes_Replaqueo, '') != '' AND LOWER($campo1) LIKE LOWER(:buscarPlaca2)))";
            } else {
                $totalQuery .= " AND LOWER($campo) LIKE LOWER(:buscarGeneral)";
            }
        }

        //*si existe la consulta se envian las condiciones.
        if ($esConsultas !== '1') {

            if ($idEstado == 'IDE-7') {
                $totalQuery .= " AND soli.Usuario_Creacion = :usuario_creacion ";
            } elseif ($idEstado == 'IDE-1') {
                $totalQuery .= " AND (soli.Usuario_Acepta = :usuario_acepta OR comp.Usuario_Comparte = :usuario_comparte)";
            } elseif ($idEstado == 'IDE-3') {
                $totalQuery .= " AND soli.Usuario_Cancelacion = :usuario_cancelacion";
            } elseif ($idEstado == 'IDE-4') {
                $totalQuery .= " AND soli.Usuario_Inadmision = :usuario_inadmitido";
            }
        }
        //*preparandoc seguinda consulta
        $totalStmt = $db->prepare($totalQuery);
        //*enviando parametros

        if ($idEstado !== '*TODOS') {
            $totalStmt->bindParam(':idEstado', $idEstado);
        }

        //* si existen los elemntos los enviamos.

        if (!empty($datoBuscar) && !empty($campo)) {
            $likeDatoBuscar = "%$datoBuscar%";
            $likeDatoBuscar1 = "%$datoBuscar1%";
            if ($campo == 'v.ID_Placa') {
                $totalStmt->bindValue(':buscarPlaca1', $likeDatoBuscar);
                $totalStmt->bindValue(':buscarPlaca2', $likeDatoBuscar1);
            } else {
                $totalStmt->bindValue(':buscarGeneral', $likeDatoBuscar);
            }
        }

        //*si existe la consulta  se envian
        if ($esConsultas !== '1') {
            if ($idEstado == 'IDE-7') {
                $totalStmt->bindParam(':usuario_creacion', $usuario_creacion);
            } elseif ($idEstado == 'IDE-1') {
                $totalStmt->bindParam(':usuario_acepta', $usuario_acepta);
                $totalStmt->bindParam(':usuario_comparte', $usuario_comparte);
            } elseif ($idEstado == 'IDE-3') {
                $totalStmt->bindParam(':usuario_cancelacion', $usuario_cancelacion);
            } elseif ($idEstado == 'IDE-4') {
                $totalStmt->bindParam(':usuario_inadmitido', $usuario_inadmitido);
            }
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
        // $select = array_values(array_intersect(array_keys($respuesta[0]), $clavesValidas));
        if ($esConsultas !== '1') {
            $select = ['SOLICITUD', 'NOMBRE_SOLICITUD', 'RTN_SOLICITUD', 'PLACA'];
            $claves_deseadas = ['SOLICITUD', 'NOMBRE_SOLICITUD', 'RTN_SOLICITUD', 'CIUDAD', 'ESTADO', ' RA'];
        } else {
            $select = ['SOLICITUD', 'NOMBRE_SOLICITUD', 'RTN_SOLICITUD', 'PLACA', 'USUARIO_CREACION', 'USUARIO_ACEPTA'];
            $claves_deseadas = ['SOLICITUD', 'NOMBRE_SOLICITUD', 'RTN_SOLICITUD', 'CIUDAD', 'ESTADO', ' RA'];
        }

        // $resultado = array_intersect_key($respuesta[0], array_flip($claves_deseadas));

        //* Enviar la respuesta JSON
        $valores = [
            'mensaje' => '',
            'encabezados' => array_keys($respuesta[0]),
            'datos' => $datos,
            'values' => $value,
            'totalRows' => $totalRows,
            'dataSelect' => $select,

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
            'dataSelect' => '',

        ];
        //* se envia el arreglo.
        echo json_encode($valores);
    }
} catch (PDOException $e) {
    //* capturando el error del llamo si es que hay error  */
    echo json_encode(['error' => 'Error en la consulta query_tabla_dinamica: ' . $e->getMessage()]);
}
