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
 class Api_Reportes
{
   	protected $db;
	protected $dominio;
	protected $dominio_corto;
	protected $dominio_raiz;
	protected $ip;
	protected $host;
    protected $ID_Solicitud;
    protected $ID_Placa;
    protected $ID_Clase_Servicio;
    public function __construct($db,$appcfg_Dominio,$appcfg_Dominio_Corto,$appcfg_Dominio_Raiz)
	{
		$this->setIp();
    	$this->db = $db;
		$this->dominio = $appcfg_Dominio;
		$this->dominio_corto = $appcfg_Dominio_Corto;
		$this->dominio_raiz = $appcfg_Dominio_Raiz;
        $this->host = gethostbyaddr($this->getIp());
		if (!empty($_SERVER['REMOTE_ADDR'])) {
			$this->ip = $_SERVER['REMOTE_ADDR'];
		} elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$this->ip = $_SERVER['HTTP_CLIENT_IP'];
		} else {
			$this->ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
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
		return $this->ip;
	}

	protected function getIp():string
	{
		return $this->ip;
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
				$txt = date('Y m d h:i:s') . ';Api_Ram.php	Usuario:; ' . $_SESSION['usuario'] . '; -- ' . 'API_RAM.PHP Error Select: Error q; ' . $q . '; $res[0] ' .  $res[0] . ' $res[1] ' . $res[1] . ' $res[2] ' . $res[2] . ' $res[3] ' . $res[3]  . ' Parametros'  .  implode(', ', array_map(fn($k, $v) => "$k=$v", array_keys($p), $p));;
				logErr($txt, '../logs/logs.txt');
				return false;
			} else {
				return $datos;
			}
		} catch (PDOException $e) {
			// Capturar excepciones de PDO (error de base de datos)
			$txt = date('Y m d h:i:s') . 'Api_Ram.php	Usuario:; ' . $_SESSION['usuario'] . '; - Error Catch SELECT PDOException; ' . $e->getMessage() . ' QUERY ' . $q . ' Parametros'  .  implode(', ', array_map(fn($k, $v) => "$k=$v", array_keys($p), $p));;
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
				$txt = date('Y m d h:i:s') . ';Api_Ram.php Usuario:; ' . $_SESSION['usuario'] . '; -- ' . ' UPDATE: Error q; ' . $q . '; $res[0] ' .  $res[0] . ' $res[1] ' . $res[1] . ' $res[2] ' . $res[2] . ' $res[3] ' . $res[3]  . ' Parametros'  .  implode(', ', array_map(fn($k, $v) => "$k=$v", array_keys($p), $p));
				logErr($txt, '../logs/logs.txt');
				return false;
			} else {
				return true;
			}
		} catch (\Throwable $th) {
			// Capturar excepciones de PDO (error de base de datos)
			$txt = date('Y m d h:i:s') . 'Api_Ram.php	Usuario:; ' . $_SESSION['usuario'] . '; - Error Catch UPDATE PDOException; ' . $th->getMessage() . ' QUERY ' . $q   . ' Parametros'  .  implode(', ', array_map(fn($k, $v) => "$k=$v", array_keys($p), $p));
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
				$txt = date('Y m d h:i:s') . ';Api_Ram.php Usuario:; ' . $_SESSION['usuario'] . '; -- ' . ' INSERT: Error q; ' . $q . '; $res[0] ' .  $res[0] . ' $res[1] ' . $res[1] . ' $res[2] ' . $res[2] . ' $res[3] ' . $res[3]  . ' Parametros'  .  implode(', ', array_map(fn($k, $v) => "$k=$v", array_keys($p), $p));
				logErr($txt, '../logs/logs.txt');
				return false;
			} else {
				return $this->db->lastInsertId();
			}
		} catch (\Throwable $th) {
			// Capturar excepciones de PDO (error de base de datos)
			$txt = date('Y m d h:i:s') . 'Api_Ram.php	Usuario:; ' . $_SESSION['usuario'] . '; - Error Catch INSERT PDOException; ' . $th->getMessage() . ' QUERY ' . $q   . ' Parametros'  .  implode(', ', array_map(fn($k, $v) => "$k=$v", array_keys($p), $p));
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
				$txt = date('Y m d h:i:s') . ';Api_Ram.php Usuario:; ' . $_SESSION['usuario'] . '; -- ' . ' DELETE: Error q; ' . $q . '; $res[0] ' .  $res[0] . ' $res[1] ' . $res[1] . ' $res[2] ' . $res[2] . ' $res[3] ' . $res[3]  . ' Parametros'  .  implode(', ', array_map(fn($k, $v) => "$k=$v", array_keys($p), $p));
				logErr($txt, '../logs/logs.txt');
				return false;
			} else {
				return true;
			}
		} catch (\Throwable $th) {
			// Capturar excepciones de PDO (error de base de datos)
			$txt = date('Y m d h:i:s') . 'Api_Ram.php	Usuario:; ' . $_SESSION['usuario'] . '; - Error Catch DELETE PDOException; ' . $th->getMessage() . ' QUERY ' . $q  . ' Parametros'  .  implode(', ', array_map(fn($k, $v) => "$k=$v", array_keys($p), $p));;
			logErr($txt, '../logs/logs.txt');
			return false;
		}
	}

    //*****************************************************************************************************/
	//* Inicio: Recuperando Data de RFM
	//*****************************************************************************************************/
	protected function getDataRFM()
	{
		$q =  "select cxc.id,cxc.numero,c.descripcion,isnull(dcu.cantidad,0) as cantidad,minimo,maximo from IHTT_RENOVACIONES_AUTOMATICAS.dbo.TB_RFM_Componentes c
                inner join IHTT_RENOVACIONES_AUTOMATICAS.dbo.TB_RFM_Componentes_x_Clase cxc on c.id = cxc.RFM_Componentes_id
                left outer join TB_Dictamen_Cambio_Unidad dcu  on  cxc.RFM_Componentes_id = dcu.RFM_Componentes_x_Clase_id  and dcu.ID_Solicitud = :ID_Solicitud 
                where cxc.Clase_Servicio_id = :Clase_Servicio_id
                order by cxc.orden";
		$bandera = 1;
        $rows = ($this->select($q, array(':ID_Solicitud' => $this->ID_Solicitud,'::Clase_Servicio_id' => $this->ID_Clase_Servicio)));
        if (is_array($rows) && count($rows) > 0) {
            $html = '<div class="row border border-primary d-flex justify-content-between align-items-center p-2">
            <div class="col">
                <strong>DATOS DE REVISIÓN FÍSICO MECÁNICA</strong>
            </div>
            <div class="d-flex justify-content-end">
                <i 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    data-bs-original-title="Cerrar pantalla" 
                onClick="fShowTramites();" class="fas fa-window-close fa-2x gobierno1"></i>
            </div>
            </div>';
            $html = $html . '<div class="row"><div class="col-md-1"><strong>No.</strong></div><div class="col-md-5"><strong>DESCRIPCIÓN</strong></div><div class="col-md-1"><strong>CANTIDAD.</strong></div><div class="col-md-1"><strong>MÁXIMO.</strong></div></div>';
            foreach ($rows as $row) {
                $html = $html . '<div class="row border border-info" id="row_tramite_'  . $row['Acronimo_Tramite'] . '_' . $row['Acronimo_Clase']  .  '"><div class="col-md-1"><input data-monto="' . $row['Monto'] . '" class="form-check-input tramiteschk" ' .  " id=" . $row['ID_CHECK']  .  ' type="checkbox" name="tramites[]" value="' . $row["ID_Tramite"] . '"></div><div id="descripcion_' . $row["ID_Tramite"] . '" class="col-md-8">' . $row["descripcion_larga"] . '</div><div class="col-md-3">&nbsp;</div></div>';
                $html = $html . '<div class="row border border-info" id="row_data_'  . $row['cxc.id'] . '">
                <div class="col-md-1"><strong>'.$row['numero'].'</strong></div>
                <div class="col-md-5"><strong>'.$row['descripcion'].'</strong></div>
                <div class="col-md-1">
                           <input style="display:none; text-transform: uppercase;" 
						   id="RFM_Componentes_' . $row['id'] . '" 
                           value="' . $row['cantidad'] . '"
						   title="Ingrese la cantidad de respuestas respondidas por sección de la ficha de revisión físico mecánica" 
						   placeholder="Ejemplo: 1,4,6 u 12" 
						   class="form-control form-control-sm" 
						   size="3"
						   minlength="'. $row['minimo'] . '" 
						   maxlength="'. $row['maximo'] . '">                
                </div>
                <div class="col-md-1"><strong>MÁXIMO.</strong></div>
                </div>';
            }
        } else {
            $html =  false;
        }		
		if (!isset($_POST["echo"])) {
			return $html;
		} else {
			echo json_encode($html);
		}
	}
	//*****************************************************************************************************/
	//* Final: Recuperando Data de RFM
	//*****************************************************************************************************/


}

if (!isset($_SESSION["ID_Usuario"]) || !isset($_SESSION["user_name"])) {
	echo json_encode(array("error" => 1100, "errorhead" => "INICIO DE SESSIÓN", "errormsg" => 'NO HAY UNA SESSION INICIADA, FAVOR INICIE SESION Y VUELVA A INTENTARLO'));
} else {
	$api = new Api_Reportes($db,$appcfg_Dominio,$appcfg_Dominio_Corto,$appcfg_Dominio_Raiz);
}