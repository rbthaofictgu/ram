<?php
setlocale(LC_ALL,"es_ES@euro","es_ES","esp" );
//error_reporting(0);
header('Content-Type: application/x-javascript; charset=utf-8');
header('Access-Control-Allow-Origin: *');
session_start();
//******************************************************************/
// Es Renovacion Automatica
//******************************************************************/
if (!isset($_SESSION["Es_Renovacion_Automatica"])) {
	$_SESSION["Es_Renovacion_Automatica"] = true;
}
//******************************************************************/
// Es originado en ventanilla
//******************************************************************/
if (!isset($_SESSION["Originado_En_Ventanilla"])) {
	$_SESSION["Originado_En_Ventanilla"] = true;
}
ini_set('post_max_size', '100M');
ini_set('upload_max_filesize', '100M');
ini_set('max_execution_time', '10000');
ini_set('max_input_time', '10000');
ini_set('memory_limit', '256M');
date_default_timezone_set("America/Tegucigalpa");
/**************************************************************************************************/
// Autoload de las librerias
/*************************************************************************************************/	
require_once('../../vendor/autoload.php');
//*configuración del las variables globales del sistema
include_once('configuracion/configuracion.php');
require_once("../config/conexion.php");
require_once("../logs/logs.php");
 class Api_Secuencias
{
   	protected $db;
	protected $dominio;
	protected $dominio_corto;
	protected $dominio_raiz;
	protected $ip;
	protected $host;
    public function __construct($db,$appcfg_Dominio,$appcfg_Dominio_Corto,$appcfg_Dominio_Raiz)
	{
		$this->db = $db;
		$this->dominio = $appcfg_Dominio;
		$this->dominio_corto = $appcfg_Dominio_Corto;
		$this->dominio_raiz = $appcfg_Dominio_Raiz;
		$this->setIp();
		$this->setHost();
    }

	protected function setIp()
	{
		if (!empty($_SERVER['REMOTE_ADDR'])) {
			$this->ip = $_SERVER['REMOTE_ADDR'];
		} elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$this->ip = $_SERVER['HTTP_CLIENT_IP'];
		} else {
			$this->ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
	}


	protected function setHost()
	{
		$this->host = gethostbyaddr($this->getIp());
	}

	protected function getIp()
	{
		if (!empty($_SERVER['REMOTE_ADDR'])) {
			return $_SERVER['REMOTE_ADDR'];
		} elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			return $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			return '127.0.0.1';
		}
	}


	//*************************************************************************************/
	//* FUNCION PARA EJECUTAR SELECT SOBRE LA BASE DE DATOS
	//*************************************************************************************/
	protected function select($q, $p, $FETCH_GROUP = '')
	{
		try {
			$stmt = $this->db->prepare($q);
			$stmt->execute($p);
			if ($FETCH_GROUP == '') {
				$datos = $stmt->fetchAll();
			} else {
				$datos = $stmt->fetchAll($FETCH_GROUP);
			}
			$res = $stmt->errorInfo();
			if (isset($res) and isset($res[3]) and intval(Trim($res[3])) <> 0) {
				$txt = date('Y m d h:i:s') . ';Api_Secuencias.php	Usuario:; ' . $_SESSION['usuario'] . '; -- ' . 'API_Secuencias.PHP Error Select: Error q; ' . $q . '; $res[0] ' .  $res[0] . ' $res[1] ' . $res[1] . ' $res[2] ' . $res[2] . ' $res[3] ' . $res[3]  . ' Parametros'  .  implode(', ', array_map(fn($k, $v) => "$k=$v", array_keys($p), $p));;
				logErr($txt, '../logs/logs.txt');
				return false;
			} else {
				return $datos;
			}
		} catch (PDOException $e) {
			// Capturar excepciones de PDO (error de base de datos)
			$txt = date('Y m d h:i:s') . 'Api_Secuencias.php	Usuario:; ' . $_SESSION['usuario'] . '; - Error Catch SELECT PDOException; ' . $e->getMessage() . ' QUERY ' . $q . ' Parametros'  .  implode(', ', array_map(fn($k, $v) => "$k=$v", array_keys($p), $p));;
			logErr($txt, '../logs/logs.txt');
			return false; // O devolver un valor indicando error
		}
	}

	//*************************************************************************************/
	//* FUNCION PARA EJECUTAR LA ACTUALIZACION SOBRE LA BASE DE DATOS
	//*************************************************************************************/
	function update($q, $p)
	{
		If (isset($_SESSION['p'])){print_r($p);unset($_SESSION['p']);}
		$stmt = $this->db->prepare($q);
		try {
			$stmt->execute($p);
			$res = $stmt->errorInfo();
			if (isset($res) and isset($res[3]) and intval(Trim($res[3])) <> 0) {
				$txt = date('Y m d h:i:s') . ';Api_Secuencias.php Usuario:; ' . $_SESSION['usuario'] . '; -- ' . ' UPDATE: Error q; ' . $q . '; $res[0] ' .  $res[0] . ' $res[1] ' . $res[1] . ' $res[2] ' . $res[2] . ' $res[3] ' . $res[3]  . ' Parametros'  .  implode(', ', array_map(fn($k, $v) => "$k=$v", array_keys($p), $p));
				logErr($txt, '../logs/logs.txt');
				return false;
			} else {
				return true;
			}
		} catch (\Throwable $th) {
			// Capturar excepciones de PDO (error de base de datos)
			$txt = date('Y m d h:i:s') . 'Api_Secuencias.php	Usuario:; ' . $_SESSION['usuario'] . '; - Error Catch UPDATE PDOException; ' . $th->getMessage() . ' QUERY ' . $q   . ' Parametros'  .  implode(', ', array_map(fn($k, $v) => "$k=$v", array_keys($p), $p));
			logErr($txt, '../logs/logs.txt');
			return false;
		}
	}
	//***************************************************************************************/
	//* FUNCION PARA EJECUTAR LA INSERCIÓN SOBRE LA BASE DE DATOS
	//***************************************************************************************/
	function insert($q, $p)
	{
		$stmt = $this->db->prepare($q);
		try {
			$stmt->execute($p);
			$res = $stmt->errorInfo();
			if (isset($res) and isset($res[3]) and intval(Trim($res[3])) <> 0) {
				$txt = date('Y m d h:i:s') . ';Api_Secuencias.php Usuario:; ' . $_SESSION['usuario'] . '; -- ' . ' INSERT: Error q; ' . $q . '; $res[0] ' .  $res[0] . ' $res[1] ' . $res[1] . ' $res[2] ' . $res[2] . ' $res[3] ' . $res[3]  . ' Parametros'  .  implode(', ', array_map(fn($k, $v) => "$k=$v", array_keys($p), $p));
				logErr($txt, '../logs/logs.txt');
				return false;
			} else {
				return $this->db->lastInsertId();
			}
		} catch (\Throwable $th) {
			// Capturar excepciones de PDO (error de base de datos)
			$txt = date('Y m d h:i:s') . 'Api_Secuencias.php	Usuario:; ' . $_SESSION['usuario'] . '; - Error Catch INSERT PDOException; ' . $th->getMessage() . ' QUERY ' . $q   . ' Parametros'  .  implode(', ', array_map(fn($k, $v) => "$k=$v", array_keys($p), $p));
			logErr($txt, '../logs/logs.txt');
			return false;
		}
	}
	//*************************************************************************************/
	//* FUNCION PARA EJECUTAR LA INSERCIÓN SOBRE LA BASE DE DATOS
	//*************************************************************************************/

	function delete($q, $p)
	{
		$stmt = $this->db->prepare($q);
		try {
			$resp = $stmt->execute($p);
			$res = $stmt->errorInfo();
			if (isset($res) and isset($res[3]) and intval(Trim($res[3])) <> 0) {
				$txt = date('Y m d h:i:s') . ';Api_Secuencias.php Usuario:; ' . $_SESSION['usuario'] . '; -- ' . ' DELETE: Error q; ' . $q . '; $res[0] ' .  $res[0] . ' $res[1] ' . $res[1] . ' $res[2] ' . $res[2] . ' $res[3] ' . $res[3]  . ' Parametros'  .  implode(', ', array_map(fn($k, $v) => "$k=$v", array_keys($p), $p));
				logErr($txt, '../logs/logs.txt');
				return false;
			} else {
				return true;
			}
		} catch (\Throwable $th) {
			// Capturar excepciones de PDO (error de base de datos)
			$txt = date('Y m d h:i:s') . 'Api_Secuencias.php	Usuario:; ' . $_SESSION['usuario'] . '; - Error Catch DELETE PDOException; ' . $th->getMessage() . ' QUERY ' . $q  . ' Parametros'  .  implode(', ', array_map(fn($k, $v) => "$k=$v", array_keys($p), $p));;
			logErr($txt, '../logs/logs.txt');
			return false;
		}
	}

    //*****************************************************************************************************/
	//* Inicio: Recuperando El Siguiente Numero de Concesion
	//*****************************************************************************************************/
	protected function GetNextConcession($ID_Categoria,$Tipo_Concesion,$Tipo_Servicio):int
	{
        if ($Tipo_Servicio == 'CARGA') {
            if ($Tipo_Concesion == 'CERTIFICADO') {
                $q =  "SELECT 
                            top 1
                            CAST(
                                SUBSTRING(
                                    [N_Certificado],
                                    CHARINDEX('-', [N_Certificado], CHARINDEX('-', [N_Certificado]) + 1) + 1,
                                    CHARINDEX('-', [N_Certificado] + '-', CHARINDEX('-', [N_Certificado], CHARINDEX('-', [N_Certificado]) + 1) + 1)
                                        - CHARINDEX('-', [N_Certificado], CHARINDEX('-', [N_Certificado]) + 1) - 1
                                ) AS INT
                            ) AS Ultima_Concesion
                        FROM
                        [IHTT_SGCERP].[DBO].[TB_Certificado_Carga]
                        where [ID_Categoria] = :ID_Categoria and LEN([N_Certificado]) - LEN(REPLACE([N_Certificado], '-', '')) >= 2
                        ORDER BY 
                            Ultima_Concesion desc;";
                $rows = $this->select($q, array(':ID_Categoria' => $this->$ID_Categoria));
            } else {
                if ($Tipo_Concesion == 'PERESP') {
                    $q =  "SELECT 
                            top 1
                            CAST(
                            SUBSTRING(
                            [N_Permiso_Especial_Carga],
                            CHARINDEX('-', [N_Permiso_Especial_Carga], CHARINDEX('-', [N_Permiso_Especial_Carga]) + 1) + 1,
                            CHARINDEX('-', [N_Permiso_Especial_Carga] + '-', CHARINDEX('-', [N_Permiso_Especial_Carga], CHARINDEX('-', [N_Permiso_Especial_Carga]) + 1) + 1)
                            - CHARINDEX('-', [N_Permiso_Especial_Carga], CHARINDEX('-', [N_Permiso_Especial_Carga]) + 1) - 1
                            ) AS INT
                            ) AS Ultima_Concesion
                            FROM
                            [IHTT_SGCERP].[DBO].[TB_Permiso_Especial_Carga]
                            where [ID_Categoria] = :ID_Categoria AND LEN([N_Permiso_Especial_Carga]) - LEN(REPLACE([N_Permiso_Especial_Carga], '-', '')) >= 2
                            ORDER BY  Ultima_Concesion desc;";
                    $rows = $this->select($q, array(':ID_Categoria' => $this->$ID_Categoria));                            
                } else {
                    $q =  "SELECT 
                            top 1
                            CAST(
                            SUBSTRING(
                            [N_Permiso_Explotacion],
                            CHARINDEX('-', [N_Permiso_Explotacion], CHARINDEX('-', [N_Permiso_Explotacion]) + 1) + 1,
                            CHARINDEX('-', [N_Permiso_Explotacion] + '-', CHARINDEX('-', [N_Permiso_Explotacion], CHARINDEX('-', [N_Permiso_Explotacion]) + 1) + 1)
                            - CHARINDEX('-', [N_Permiso_Explotacion], CHARINDEX('-', [N_Permiso_Explotacion]) + 1) - 1
                            ) AS INT
                            ) AS Ultima_Concesion
                            FROM
                            [IHTT_SGCERP].[DBO].[TB_Permiso_Explotacion_Carga]
                            where ID_Categoria = :ID_Categoria AND LEN([N_Permiso_Explotacion]) - LEN(REPLACE([N_Permiso_Explotacion], '-', '')) >= 2
                            ORDER BY  Ultima_Concesion desc;";
                    $rows = $this->select($q, array(':ID_Categoria' => $this->$ID_Categoria));
                }
            }
        } else {
            if ($Tipo_Concesion == 'CERTIFICADO') {
                $q =  "SELECT 
                            top 1
                            CAST(
                                SUBSTRING(
                                    [N_Certificado],
                                    CHARINDEX('-', [N_Certificado], CHARINDEX('-', [N_Certificado]) + 1) + 1,
                                    CHARINDEX('-', [N_Certificado] + '-', CHARINDEX('-', [N_Certificado], CHARINDEX('-', [N_Certificado]) + 1) + 1)
                                        - CHARINDEX('-', [N_Certificado], CHARINDEX('-', [N_Certificado]) + 1) - 1
                                ) AS INT
                            ) AS Ultima_Concesion
                        FROM
                        [IHTT_SGCERP].[DBO].[TB_Certificado_Pasajeros]
                        where [ID_Categoria] = :ID_Categoria and LEN([N_Certificado]) - LEN(REPLACE([N_Certificado], '-', '')) >= 2
                        ORDER BY Ultima_Concesion desc;";
                $rows = $this->select($q, array(':ID_Categoria' => $this->$ID_Categoria));
            } else {
                if ($Tipo_Concesion == 'PERESP') {
                    $q =  "SELECT 
                            top 1
                            CAST(
                            SUBSTRING(
                            [N_Permiso_Especial_Pas],
                            CHARINDEX('-', [N_Permiso_Especial_Pas], CHARINDEX('-', [N_Permiso_Especial_Pas]) + 1) + 1,
                            CHARINDEX('-', [N_Permiso_Especial_Pas] + '-', CHARINDEX('-', [N_Permiso_Especial_Pas], CHARINDEX('-', [N_Permiso_Especial_Pas]) + 1) + 1)
                            - CHARINDEX('-', [N_Permiso_Especial_Pas], CHARINDEX('-', [N_Permiso_Especial_Pas]) + 1) - 1
                            ) AS INT
                            ) AS Ultima_Concesion
                            FROM
                            [IHTT_SGCERP].[DBO].[TB_Permiso_Especial_Pas]
                            where [ID_Categoria] = :ID_Categoria and LEN([N_Permiso_Especial_Pas]) - LEN(REPLACE([N_Permiso_Especial_Pas], '-', '')) >= 2
                            ORDER BY  Ultima_Concesion desc;";
                    $rows = $this->select($q, array(':ID_Categoria' => $this->$ID_Categoria));                            
                } else {
                    $q =  "SELECT 
                            top 1
                            CAST(
                            SUBSTRING(
                            [N_Permiso_Explotacion],
                            CHARINDEX('-', [N_Permiso_Explotacion], CHARINDEX('-', [N_Permiso_Explotacion]) + 1) + 1,
                            CHARINDEX('-', [N_Permiso_Explotacion] + '-', CHARINDEX('-', [N_Permiso_Explotacion], CHARINDEX('-', [N_Permiso_Explotacion]) + 1) + 1)
                            - CHARINDEX('-', [N_Permiso_Explotacion], CHARINDEX('-', [N_Permiso_Explotacion]) + 1) - 1
                            ) AS INT
                            ) AS Ultima_Concesion
                            FROM
                            [IHTT_SGCERP].[DBO].[TB_Permiso_Explotacion_Pas]
                            where ID_Categoria = :ID_Categoria and LEN([N_Permiso_Explotacion]) - LEN(REPLACE([N_Permiso_Explotacion], '-', '')) >= 2
                            ORDER BY  Ultima_Concesion desc;";
                    $rows = $this->select($q, array(':ID_Categoria' => $this->$ID_Categoria));
                }
            }
        }
        if (isset($rows['Ultima_Concesion']) and intval(Trim($rows['Ultima_Concesion'])) >= 0) {
            return intval(intval(Trim($rows['Ultima_Concesion'])) + 1);
        } else {
            if (isset($rows) and $rows != false) {
                return 1;
            } else {
                return false;
            }
        }
	}
    //*****************************************************************************************************/
	//* Final: Recuperando El Siguiente Numero de Concesion
	//*****************************************************************************************************/

    //*****************************************************************************************************/
	//* Inicio: Recuperando El Siguiente Numero de Concesion
	//*****************************************************************************************************/
	protected function GetNextRecordNumber($ID_Categoria,$Tipo_Concesion):int
	{
        if ($Tipo_Concesion == 'CERTIFICADO') {
            $q =  "SELECT 
                    top 1
                    CAST(
                    SUBSTRING(
                    [Numero_Registro],
                    CHARINDEX('-', [Numero_Registro], CHARINDEX('-', [Numero_Registro]) + 1) + 1,
                    CHARINDEX('-', [Numero_Registro] + '-', CHARINDEX('-', [Numero_Registro], CHARINDEX('-', [Numero_Registro]) + 1) + 1)
                    - CHARINDEX('-', [Numero_Registro], CHARINDEX('-', [Numero_Registro]) + 1) - 1
                    ) AS INT
                    ) AS Ultimo_Registro,
                    Numero_Registro
                    FROM
                    [IHTT_SGCERP].[DBO].[TB_Certificado_Pasajeros]
                    where [ID_Categoria] = :ID_Categoria and LEN([Numero_Registro]) - LEN(REPLACE([Numero_Registro], '-', '')) >= 2
                    ORDER BY  Ultimo_Registro desc";
            $rows = $this->select($q, array(':ID_Categoria' => $this->$ID_Categoria));
        } else {
            if ($Tipo_Concesion == 'PERESP') {
                $q =  "SELECT 
                    top 1
                    CAST(
                    SUBSTRING(
                    [Numero_Registro],
                    CHARINDEX('-', [Numero_Registro], CHARINDEX('-', [Numero_Registro]) + 1) + 1,
                    CHARINDEX('-', [Numero_Registro] + '-', CHARINDEX('-', [Numero_Registro], CHARINDEX('-', [Numero_Registro]) + 1) + 1)
                    - CHARINDEX('-', [Numero_Registro], CHARINDEX('-', [Numero_Registro]) + 1) - 1
                    ) AS INT
                    ) AS Ultimo_Registro,
                    Numero_Registro
                    FROM
                    [IHTT_SGCERP].[DBO].[TB_Permiso_Especial_Pas]
                    where [ID_Categoria] = :ID_Categoria and LEN([Numero_Registro]) - LEN(REPLACE([Numero_Registro], '-', '')) >= 2
                    ORDER BY  Ultimo_Registro desc";
                $rows = $this->select($q, array(':ID_Categoria' => $this->$ID_Categoria));                            
            }
        }
        if (isset($rows['Ultimo_Registro']) and intval(Trim($rows['Ultimo_Registro'])) >= 0) {
            return intval(intval(Trim($rows['Ultimo_Registro'])) + 1);
        } else {
            if (isset($rows) and $rows != false) {
                return 1;
            } else {
                return false;
            }
        }
	}
    //*****************************************************************************************************/
	//* Final: Recuperando El Siguiente Numero de Concesion
	//*****************************************************************************************************/
}


if (!isset($_SESSION["ID_Usuario"]) || !isset($_SESSION["user_name"])) {
	echo json_encode(array("error" => 1100, "errorhead" => "INICIO DE SESSIÓN", "errormsg" => 'NO HAY UNA SESSION INICIADA, FAVOR INICIE SESION Y VUELVA A INTENTARLO'));
} else {
	$api = new Api_Secuencias($db,$appcfg_Dominio,$appcfg_Dominio_Corto,$appcfg_Dominio_Raiz,$_POST['ID_Categoria'],$_POST['Tipo_Concesion'],$_POST['ID_Clase_Servico']);
}