<?php
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
ini_set('max_execution_time', '1000');
ini_set('max_input_time', '1000');
ini_set('memory_limit', '256M');
date_default_timezone_set("America/Tegucigalpa");
require_once("../config/conexion.php");
require_once("../logs/logs.php");

class Api_Ram
{

	protected $db;
	protected $ip;
	protected $host;

	public function __construct($db)
	{
		$this->setDB($db);
		$this->setIp();
		$this->setHost();
		if (isset($_POST["action"])) {
			if ($_POST["action"] == "get-apoderado" && isset($_POST["idApoderado"])) {
				$this->getApoderadoAPI($_POST["idApoderado"]);
			} else if ($_POST["action"] == "get-solicitante" && isset($_POST["idSolicitante"])) {
				$this->getSolicitante();
			} else if ($_POST["action"] == "get-concesion" && isset($_POST["Concesion"])) {
				$this->getConcesion();
			} else if ($_POST["action"] == "get-concesion-preforma" && isset($_POST["idConcesion"])) {
				$this->getConcesionPreforma();
			} else if ($_POST["action"] == "get-datosporomision") {
				$this->getDatosPorOmision();
			} else if ($_POST["action"] == "get-municipios") {
				$this->getMunicipios($_POST["filtro"]);
			} else if ($_POST["action"] == "get-aldeas") {
				$this->getAldeas($_POST["filtro"]);
			} else if ($_POST["action"] == "save-preforma" and $_POST["modalidadDeEntrada"] == "I") {
				$this->savePreforma();
			} else if ($_POST["action"] == "save-preforma" and $_POST["modalidadDeEntrada"] == "U") {
				$this->updatePreforma();
			} else if ($_POST["action"] == "update-expediente" and $_POST["modalidadDeEntrada"] == "U") {
				$this->updateExpediente();
			} else if ($_POST["action"] == "delete-concesion-expediente") {
				$this->deleteConcesionesExpediente();
			} else if ($_POST["action"] == "delete-tramite-expediente") {
				$this->deleteTramiteExpediente($_POST["RAM"]);
			} else if ($_POST["action"] == "add-tramite-expediente") {
				$this->saveTramitesExpediente($_POST["RAM"], $_POST["Tramites"], $_POST['Concesion']);
			} else if ($_POST["action"] == "add-tramite-preforma") {
				$_POST["Tramites"]  = json_decode($_POST["Tramites"], true);
				$_POST["Concesion"]  = json_decode($_POST["Concesion"], true);
				$this->saveTramites($_POST["RAM"], $_POST["Tramites"]);
			} else if ($_POST["action"] == "save-requisitos") {
				$this->saveRequisitos();
			} else if ($_POST["action"] == "delete-concesion-preforma") {
				$this->deleteConcesionesPreforma();
			} else if ($_POST["action"] == "delete-tramite-preforma") {
				$this->deleteTramitePreforma();
			} else if ($_POST["action"] == "get-vehiculo") {
				echo json_encode($this->getDatosUnidadDesdeIP($_POST["ID_Placa"]));
			} else if ($_POST["action"] == "save-escaneo") {
				$this->saveEscaneo($_POST["RAM"]);
			} else if ($_POST["action"] == "update-estado-preforma") {
				$this->updateEstadoPreforma();
			} else {
				echo json_encode(array("error" => 1001, "errorhead" => 'OPPS', "errormsg" => 'NO SE ENCONTRO NINGUNA FUNCION EN EL API PARA LA ACCIÓN REQUERIDA'));
			}
		}
	}

	protected function setDB($db)
	{
		$this->db = $db;
	}

	protected function getDB($db)
	{
		$this->db;
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

	protected function getIp()
	{
		return $this->ip;
	}



	protected function setHost()
	{
		$this->host = gethostbyaddr($this->getIp());
	}

	protected function getHost()
	{
		return $this->host;
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
				$txt = date('Y m d h:i:s') . ';Api_Ram.php	Usuario:; ' . $_SESSION['usuario'] . '; -- ' . 'API_RAM.PHP Error Select: Error q; ' . $q . '; $res[0] ' .  $res[0] . ' $res[1] ' . $res[1] . ' $res[2] ' . $res[2] . ' $res[3] ' . $res[3];
				logErr($txt, '../logs/logs.txt');
				return false;
			} else {
				return $datos;
			}
		} catch (PDOException $e) {
			// Capturar excepciones de PDO (error de base de datos)
			$txt = date('Y m d h:i:s') . 'Api_Ram.php	Usuario:; ' . $_SESSION['usuario'] . '; - Error Catch SELECT PDOException; ' . $e->getMessage() . ' QUERY ' . $q;
			logErr($txt, '../logs/logs.txt');
			return false; // O devolver un valor indicando error
		}
	}

	//*************************************************************************************/
	//* FUNCION PARA EJECUTAR LA ACTUALIZACION SOBRE LA BASE DE DATOS
	//*************************************************************************************/
	function update($q, $p)
	{
		$stmt = $this->db->prepare($q);
		try {
			$resp = $stmt->execute($p);
			$res = $stmt->errorInfo();
			if (isset($res) and isset($res[3]) and intval(Trim($res[3])) <> 0) {
				$txt = date('Y m d h:i:s') . ';Api_Ram.php Usuario:; ' . $_SESSION['usuario'] . '; -- ' . ' UPDATE: Error q; ' . $q . '; $res[0] ' .  $res[0] . ' $res[1] ' . $res[1] . ' $res[2] ' . $res[2] . ' $res[3] ' . $res[3];
				logErr($txt, '../logs/logs.txt');
				return false;
			} else {
				return true;
			}
		} catch (\Throwable $th) {
			// Capturar excepciones de PDO (error de base de datos)
			$txt = date('Y m d h:i:s') . 'Api_Ram.php	Usuario:; ' . $_SESSION['usuario'] . '; - Error Catch UPDATE PDOException; ' . $th->getMessage() . ' QUERY ' . $q;
			logErr($txt, '../logs/logs.txt');
			return false;
		}
	}
	//*************************************************************************************/
	//* FUNCION PARA EJECUTAR LA INSERCIÓN SOBRE LA BASE DE DATOS
	//*************************************************************************************/
	function insert($q, $p)
	{
		$stmt = $this->db->prepare($q);
		try {
			$resp = $stmt->execute($p);
			$res = $stmt->errorInfo();
			if (isset($res) and isset($res[3]) and intval(Trim($res[3])) <> 0) {
				$txt = date('Y m d h:i:s') . ';Api_Ram.php Usuario:; ' . $_SESSION['usuario'] . '; -- ' . ' INSERT: Error q; ' . $q . '; $res[0] ' .  $res[0] . ' $res[1] ' . $res[1] . ' $res[2] ' . $res[2] . ' $res[3] ' . $res[3];
				logErr($txt, '../logs/logs.txt');
				return false;
			} else {
				return $this->db->lastInsertId();
			}
		} catch (\Throwable $th) {
			// Capturar excepciones de PDO (error de base de datos)
			$txt = date('Y m d h:i:s') . 'Api_Ram.php	Usuario:; ' . $_SESSION['usuario'] . '; - Error Catch INSERT PDOException; ' . $th->getMessage() . ' QUERY ' . $q;
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
				$txt = date('Y m d h:i:s') . ';Api_Ram.php Usuario:; ' . $_SESSION['usuario'] . '; -- ' . ' DELETE: Error q; ' . $q . '; $res[0] ' .  $res[0] . ' $res[1] ' . $res[1] . ' $res[2] ' . $res[2] . ' $res[3] ' . $res[3];
				logErr($txt, '../logs/logs.txt');
				return false;
			} else {
				return true;
			}
		} catch (\Throwable $th) {
			// Capturar excepciones de PDO (error de base de datos)
			$txt = date('Y m d h:i:s') . 'Api_Ram.php	Usuario:; ' . $_SESSION['usuario'] . '; - Error Catch DELETE PDOException; ' . $th->getMessage() . ' QUERY ' . $q;
			logErr($txt, '../logs/logs.txt');
			return false;
		}
	}

	//***********************************************************************************************************************/
	//*Inicio                                                                                                               */
	//*rbthaofic@gmail.com 2024/12/05 Borrar Concesiones Preforma */
	//***********************************************************************************************************************/
	protected function deleteConcesionesPreforma()
	{
		$_POST["idConcesiones"] = json_decode($_POST["idConcesiones"], true);
		$return = true;
		$this->db->beginTransaction();
		foreach ($_POST["idConcesiones"] as $Concesion) {
			$query = "DELETE FROM [IHTT_PREFORMA].[dbo].[TB_Solicitud] where N_Certificado = :N_Certificado or N_Permiso_Especial = :N_Permiso_Especial or Permiso_Explotacion = :Permiso_Explotacion";
			$p = array(":N_Certificado" => $Concesion, ":N_Permiso_Especial" => $Concesion, ":Permiso_Explotacion" => $Concesion);
			$return = $this->delete($query, $p);
			if ($return == false) {
				break;
			}
			$query = "DELETE FROM [IHTT_PREFORMA].[dbo].[TB_Vehiculo] where Certificado_Operacion = :Certificado_Operacion or Permiso_Especial = :Permiso_Especial or Permiso_Explotacion = :Permiso_Explotacion";
			$p = array(":Certificado_Operacion" => $Concesion, ":Permiso_Especial" => $Concesion, ":Permiso_Explotacion" => $Concesion);
			$return = $this->delete($query, $p);
			if ($return == false) {
				break;
			}
		}
		if ($return == false) {
			$this->db->rollBack();
			echo json_encode(array("error" => 2000, "errorhead" => "BORRANDO CONCESIONE(S)", "errormsg" => 'ERROR AL INTENTAR CONCESIONES, FAVOR CONTACTE AL ADMON DEL SISTEMA'));
		} else {
			$this->db->commit();
			echo json_encode(['Borrado'  =>  True]);
		}
	}
	//***********************************************************************************************************************/
	//*Inicio                                                                                                               */
	//*rbthaofic@gmail.com 2025/01/22 Add Tramite Preforma                                                                  */
	//***********************************************************************************************************************/
	protected function addTramitePreforma()
	{
		$_POST["idTramite"] = json_decode($_POST["idTramite"], true);
		$this->db->beginTransaction();
		$query = "DELETE FROM [IHTT_PREFORMA].[dbo].[TB_Solicitud] where ID = :ID";
		$p = array(":ID" => $_POST["idTramite"]);
		$return = $this->delete($query, $p);
		if ($return == false) {
			$this->db->rollBack();
			echo json_encode(array("error" => 2000, "errorhead" => "ELIMINAR TRAMITE PREFORMA", "errormsg" => 'ERROR AL INTENTAR ELIMINAR TRAMITE EN PREFORMA, FAVOR CONTACTE AL ADMON DEL SISTEMA'));
		} else {
			//$this->db->rollBack();
			$this->db->commit();			
			echo json_encode(['Borrado'  =>  True]);
		}
	}

	//***********************************************************************************************************************/
	//*Inicio                                                                                                               */
	//*rbthaofic@gmail.com 2024/12/05 Delete Tramite Preforma */
	//***********************************************************************************************************************/
	protected function deleteTramitePreforma()
	{
		$_POST["idTramite"] = json_decode($_POST["idTramite"], true);
		$this->db->beginTransaction();
		$query = "DELETE FROM [IHTT_PREFORMA].[dbo].[TB_Solicitud] where ID = :ID";
		$p = array(":ID" => $_POST["idTramite"]);
		$return = $this->delete($query, $p);
		if ($return == false) {
			$this->db->rollBack();
			echo json_encode(array("error" => 2000, "errorhead" => "ELIMINAR TRAMITE PREFORMA", "errormsg" => 'ERROR AL INTENTAR ELIMINAR TRAMITE EN PREFORMA, FAVOR CONTACTE AL ADMON DEL SISTEMA'));
		} else {
			//$this->db->rollBack();
			$this->db->commit();
			echo json_encode(['Borrado'  =>  True]);
		}
	}


	//*************************************************************************************/
	//* FUNCION PARA RECUPERAR EL APODERADO LEGAL DEL PORTAL DE APODERADOS 
	//*************************************************************************************/
	protected function getApoderadoAPI($col)
	{
		$query = "SELECT ID_ColegiacionAPL, Nombre_Apoderado_Legal, Identidad, Email, Telefono, Direccion FROM [IHTT_DB].[dbo].[TB_Apoderado_Legal] WHERE ID_ColegiacionAPL = :IDCOL";
		$p = array(":IDCOL" => $col);
		if (!isset($_POST["echo"])) {
			$data = $this->select($query, $p);
			if (count($data) > 0) {
				echo json_encode(array("id_colegiacion" => $data[0]["ID_ColegiacionAPL"], "nombre_apoderado" => $data[0]["Nombre_Apoderado_Legal"], "ident_apoderado" => $data[0]["Identidad"], "correo_apoderado" => $data[0]["Email"], "tel_apoderado" => $data[0]["Telefono"], "dir_apoderado" => $data[0]["Direccion"]));
			} else {
				echo json_encode(array("id_colegiacion" => '', "nombre_apoderado" => '', "ident_apoderado" => '', "correo_apoderado" => '', "tel_apoderado" => '', "dir_apoderado" => count($data)));
			}
		} else {
			return json_encode($this->select($query, $p));
		}
	}

	protected function getApoderadoLegalRAM()
	{
		$q = "SELECT * FROM [IHTT_Preforma].[dbo].[TB_Apoderado_Legal] where ID_Formulario_Solicitud = :ID_Formulario_Solicitud";
		if (!isset($_POST["echo"])) {
			return $this->select($q, array(':ID_Formulario_Solicitud' => $_POST["RAM"]));
		} else {
			echo json_encode($this->select($q, array(':ID_Formulario_Solicitud' => $_POST["RAM"])));
		}
	}

	protected function getSolicitanteRAM()
	{
		$q = "SELECT sol.*,ald.ID_Municipio,mn.ID_Departamento,es.DESC_Estado 
		FROM [IHTT_Preforma].[dbo].[TB_Solicitante] sol, [IHTT_SELD].[dbo].[TB_Aldea] ald,[IHTT_SELD].[dbo].[TB_Municipio] mn,[IHTT_Preforma].[dbo].[TB_Estados] es 
		where sol.ID_Formulario_Solicitud = :ID_Formulario_Solicitud  and sol.ID_Aldea = ald.ID_Aldea and ald.ID_Municipio = mn.ID_Municipio and
		sol.Estado_Formulario = es.ID_Estado";
		if (!isset($_POST["echo"])) {
			return $this->select($q, array(':ID_Formulario_Solicitud' => $_POST["RAM"]));
		} else {
			echo json_encode($this->select($q, array(':ID_Formulario_Solicitud' => $_POST["RAM"])));
		}
	}

	protected function getTramitesRAM()
	{
		$q = "SELECT CONCAT(CONCAT(CONCAT(CONCAT(CONCAT(CONCAT(Tra.ID_Tipo_Tramite,'_'),Cla.ID_Clase_Tramite),'_'),Tip.Acronimo_Tramite),'_'),Cla.Acronimo_Clase) AS ID_CHECK,
		md.ID_Clase_Servicio,tip.DESC_Tipo_Tramite,cla.DESC_Clase_Tramite,sol.*,
		(select top 1 b.monto from [IHTT_Webservice].[dbo].[TB_Tarifas] A,[IHTT_Webservice].[dbo].[TB_TarifasHistorico] B where A.CodigoTramite = B.CodigoTramite AND A.CodigoTramite = sol.ID_Tramite ORDER BY B.FechaFin DESC) as Monto,
		(select top 1 ID_Placa from [IHTT_Preforma].[dbo].[TB_Vehiculo] veh where sol.ID_Formulario_Solicitud = veh.ID_Formulario_Solicitud and sol.N_Certificado = veh.Certificado_Operacion and veh.Estado in ('NORMAL','SALE')) AS ID_Placa,
		(select top 1 ID_Placa from [IHTT_Preforma].[dbo].[TB_Vehiculo] veh where sol.ID_Formulario_Solicitud = veh.ID_Formulario_Solicitud and sol.N_Certificado = veh.Certificado_Operacion and veh.Estado = 'ENTRA') AS ID_Placa1,
		CO.PermisoEspecialEncriptado,CO.CertificadoEncriptado,CO.Permiso_Explotacion_Encriptado,CO.[Clase Servicio],
		CASE 
			WHEN CO.[Clase Servicio] = 'STPC' THEN 1
			WHEN CO.[Clase Servicio] = 'STPP' THEN 0
			WHEN CO.[Clase Servicio] = 'STEP' THEN 0
			ELSE 1
		END as esCarga,
		CASE 
			WHEN CO.[Clase Servicio] = 'STPC' THEN 1
			WHEN CO.[Clase Servicio] = 'STPP' THEN 1
			WHEN CO.[Clase Servicio] = 'STEP' THEN 0
			ELSE 0
		END as esCertificado,
		CO.N_Certificado,
		CO.[Fecha Vencimiento Certificado] as Fecha_Expiracion,
		CO.N_Permiso_Explotacion,
		CO.[Fecha Vencimiento Permiso] as Fecha_Expiracion_Explotacion
		FROM [IHTT_Preforma].[dbo].[TB_Solicitud] Sol,[IHTT_DB].[dbo].[TB_Tramite] Tra,[IHTT_DB].[dbo].[TB_Tipo_Tramite] Tip,[IHTT_DB].[dbo].[TB_Clase_Tramite] Cla,[IHTT_DB].[dbo].[TB_Modalidad] md, [IHTT_SGCERP].[dbo].[v_Listado_General] CO
		where sol.ID_Formulario_Solicitud = :ID_Formulario_Solicitud and Sol.ID_Tramite = Tra.ID_Tramite and Tra.ID_Tipo_Tramite = Tip.ID_Tipo_Tramite and tra.ID_Clase_Tramite = cla.ID_Clase_Tramite and
		sol.ID_Modalidad = md.ID_Modalidad and (Sol.N_Certificado = CO.N_Certificado or Sol.N_Permiso_Especial = CO.N_Certificado)
		order by sol.Permiso_Explotacion,sol.N_Certificado,sol.N_Permiso_Especial";
		$rows = $this->select($q, array(':ID_Formulario_Solicitud' => $_POST["RAM"]));
		$max = count($rows);
		for ($i = 0; $i < $max; $i++) {
			$Permiso_Explotacion_Encriptado = '';
			while ($Permiso_Explotacion_Encriptado != $rows[$i]["Permiso_Explotacion_Encriptado"]) {
				$Permiso_Explotacion_Encriptado = $rows[$i]["Permiso_Explotacion_Encriptado"];
				$CertificadoEncriptado = '';
				while ($CertificadoEncriptado != $rows[$i]["CertificadoEncriptado"]) {
					$CertificadoEncriptado = $rows[$i]["CertificadoEncriptado"];
					if ($rows[$i]["ID_CHECK"] === 'IHTTTRA-02_CLATRA-01_R_PE' || $rows[$i]["ID_CHECK"] === 'IHTTTRA-02_CLATRA-02_R_CO' || $rows[$i]["ID_CHECK"] === 'IHTTTRA-02_CLATRA-02_R_PS') {
						$rows[$i]["Vencimientos"] = $this->procesarFechaDeVencimiento($rows[$i], $rows[$i]["ID_Clase_Servicio"])[1];
					} else {
						$rows[$i]["Vencimientos"] = false;
					}
				}
			}
		}

		if (!isset($_POST["echo"])) {
			return $rows;
		} else {
			echo json_encode($rows);
		}
	}

	protected function getDocumentos()
	{
		$q = "SELECT doc.* FROM [IHTT_PREFORMA].[dbo].[TB_Validacion_Documentos] doc where doc.ID_Formulario_Solicitud = :ID_Formulario_Solicitud";
		if (!isset($_POST["echo"])) {
			return $this->select($q, array(':ID_Formulario_Solicitud' => $_POST["RAM"]));
		} else {
			echo json_encode($this->select($q, array(':ID_Formulario_Solicitud' => $_POST["RAM"])));
		}
	}

	protected function getUnidades()
	{
		$q = "SELECT 
				RTRIM(veh.[Certificado_Operacion]) AS Certificado_Operacion,
				veh.[ID_Formulario_Solicitud],
				veh.[ID],
				veh.[RTN_Propietario],
				veh.[Nombre_Propietario],
				veh.[ID_Placa],
				veh.[ID_Marca],
				mar.Desc_Marca,
				veh.[Anio],
				veh.[Modelo],
				veh.[Tipo_Vehiculo],
				veh.[ID_Color],
				col.Desc_Color,
				veh.[Motor],
				veh.[Chasis],
				veh.[Estado],
				veh.[Sistema_Fecha],
				veh.[Permiso_Explotacion],
				veh.[VIN],
				veh.[Combustible],
				veh.[Alto],
				veh.[Ancho],
				veh.[Largo],
				veh.[Capacidad_Carga],
				veh.[Peso_Unidad],
				veh.[ID_Placa_Antes_Replaqueo],
				ISNULL(
					(SELECT TOP 1 c.ID_Memo 
					FROM [IHTT_Autos].[dbo].[TB_Ingreso_Constancias] c 
					WHERE c.Chasis_Entra = veh.Chasis 
					AND c.Placa_Entra = veh.ID_Placa 
					AND c.ID_Estado = 'IDE-1' 
					AND veh.Estado = 'ENTRA'),
					'false'
				) AS ID_Memo
			FROM 
				[IHTT_PREFORMA].[dbo].[TB_Vehiculo] veh
			JOIN 
				[IHTT_SGCERP].[dbo].[TB_Marca_Vehiculo] mar ON veh.ID_Marca = mar.ID_Marca
			JOIN 
				[IHTT_SGCERP].[dbo].[TB_Color_Vehiculos] col ON veh.ID_Color = col.ID_Color
			where veh.ID_Formulario_Solicitud = :ID_Formulario_Solicitud";
		if (!isset($_POST["echo"])) {
			return $this->select($q, array(':ID_Formulario_Solicitud' => $_POST["RAM"]), PDO::FETCH_GROUP);
		} else {
			echo json_encode($this->select($q, array(':ID_Formulario_Solicitud' => $_POST["RAM"]), PDO::FETCH_GROUP));
		}
	}

	protected function getDatosPorOmision()
	{
		$datos[1] = $this->getEntregaUbicacion();
		$datos[2] = $this->getDepartamentos();
		if ($datos[1] != false && $datos[2] != false) {
			$datos[0] = count($datos[1]);
			//*************************************************************************************/
			//* Si es un fsl que ya estaba salvada y se va a continuar trabajando
			//*************************************************************************************/
			if ($_POST["RAM"] != '') {
				$datos[3] = $this->getApoderadoLegalRAM();
				$datos[4] = $this->getSolicitanteRAM();
				$datos[5] = $this->getTramitesRAM();
				$datos[6] = $this->getDocumentos();
				$datos[7] = $this->getUnidades();
				$datos[8] = $this->testFileExists();
			}
			echo json_encode($datos);
		} else {
			echo json_encode(array("error" => 1001, "errormsg" => 'ALGO RARO SUCEDIO RECUPERANDO LOS DATOS DE UBICACIONES Y DEPARTAMENTOS, INTENTELO DE NUEVO. SI EL PERSISTE CONTACTE AL ADMINISTRADOR DEL SISTEMA'));
		}
	}

	protected function testFileExists()
	{
		$directory = "Documentos/" . $_POST["RAM"] . "/";
		$filePath = $directory . $_POST["RAM"] . ".pdf";
		if (file_exists($filePath)) {
			return $filePath;
		} else {
			return false;
		}
	}
	protected function getDepartamentos()
	{
		$q = "SELECT ID_Departamento as value, DESC_Departamento as text FROM [IHTT_SELD].[dbo].TB_Departamento";
		if (!isset($_POST["echo"])) {
			return $this->select($q, array());
		} else {
			echo json_encode($this->select($q, array()));
		}
	}

	protected function getMunicipios($ID_Departamento)
	{
		$q = "SELECT ID_Municipio as value, DESC_Municipio as text FROM [IHTT_SELD].[dbo].TB_Municipio where ID_Departamento = :ID_Departamento";
		if (!isset($_POST["echo"])) {
			return $this->select($q, array(':ID_Departamento' => $ID_Departamento));
		} else {
			echo json_encode($this->select($q, array(':ID_Departamento' => $ID_Departamento)));
		}
	}

	protected function getAldeas($ID_Municipio)
	{
		$q = "SELECT ID_Aldea as value, DESC_Aldea as text FROM [IHTT_PREFORMA].[dbo].[TB_Aldea] where ID_Municipio = :ID_Municipio";
		if (!isset($_POST["echo"])) {
			return $this->select($q, array(':ID_Municipio' => $ID_Municipio));
		} else {
			echo json_encode($this->select($q, array(':ID_Municipio' => $ID_Municipio)));
		}
	}

	protected function getEntregaUbicacion($filtro = null)
	{
		$q = "SELECT ID_Ubicacion as value, DESC_Ubicacion as text FROM IHTT_DB.dbo.TB_Entrega_Ubicaciones   " . ($filtro ? "WHERE ID_Tipo_Solicitante = $filtro " : "") . "  order by DESC_Ubicacion ";
		if (!isset($_POST["echo"])) {
			return $this->select($q, array());
		} else {
			echo json_encode($this->select($q, array()));
		}
	}

	protected function getTipoSolicitante($filtro = null)
	{
		$q = "SELECT * FROM [IHTT_SELD].[dbo].[TB_Tipo_Solicitante]  " . ($filtro ? "WHERE ID_Tipo_Solicitante = $filtro " : "") . "  order by DESC_Solicitante ";
		if (!isset($_POST["echo"])) {
			return $this->select($q, array());
		} else {
			echo json_encode($this->select($q, array()));
		}
	}

	protected function getColor($filtro = null)
	{
		$q = "SELECT ID_Color as value, DESC_Color as text FROM [IHTT_SGCERP].[dbo].[TB_Color_Vehiculos] " . ($filtro ? "WHERE ID_Color = $filtro " : "") . "  order by DESC_Color ";
		if (!isset($_POST["echo"])) {
			return $this->select($q, array());
		} else {
			echo json_encode($this->select($q, array()));
		}
	}

	protected function getMarca($filtro = null)
	{
		$q = "SELECT ID_Marca as value, DESC_Marca as text FROM [IHTT_SGCERP].[dbo].[TB_Marca_Vehiculo] " . ($filtro ? "WHERE ID_Marca = $filtro " : "") . "  order by DESC_Marca ";
		if (!isset($_POST["echo"])) {
			return $this->select($q, array());
		} else {
			echo json_encode($this->select($q, array()));
		}
	}

	protected function getAnios()
	{
		for ($i = 1946; $i <= (date("Y") + 1); $i++) {
			$datos[] = array("value" => $i, "text" => $i);
		}
		if (!isset($_POST["echo"])) {
			return $datos;
		} else {
			echo json_encode($datos);
		}
	}

	protected function getAreaOperacion($filtro = null)
	{
		$q = "SELECT [ID] as value, [DESC_Area_Operacion] as text FROM [IHTT_DB].[dbo].[TB_Area_Operacion] " . ($filtro ? "WHERE ID = $filtro " : "") . " order by [DESC_Area_Operacion]";
		if (!isset($_POST["echo"])) {
			return $this->select($q, array());
		} else {
			echo json_encode($this->select($q, array()));
		}
	}

	protected function getCategoriaEspecilizadaCarga($filtro = null)
	{
		$q = "SELECT [ID_Clase_Servicio] as value, [DESC_Tipo] as text FROM [IHTT_DB].[dbo].[TB_Tipo_Categoria] " . ($filtro ? "WHERE ID = $filtro " : "") . " order by [DESC_Tipo]";
		if (!isset($_POST["echo"])) {
			return $this->select($q, array());
		} else {
			echo json_encode($this->select($q, array()));
		}
	}


	//*************************************************************************************/
	//* FUNCION PARA RECUPERAR UNIDAD ASIGNADA AL CERTIFICADO
	//*************************************************************************************/

	protected function getUnidad($tabla, $campo_filtro, $filtro, $campos)
	{
		$q = "SELECT " . $campos .  " FROM " . $tabla . $campo_filtro . " = :ID_Vehiculo";
		if (!isset($_POST["echo"])) {
			return $this->select($q, array(":ID_Vehiculo" => $filtro));
		} else {
			echo json_encode($this->select($q, array()));
		}
	}

	//*************************************************************************************/
	//* FUNCION PARA RECUPERAR UNIDAD ASIGNADA AL CERTIFICADO
	//*************************************************************************************/

	protected function getUnidadPreforma($RAM, $idConcesion)
	{
		$q = "SELECT [ID]
      ,[ID_Formulario_Solicitud]
      ,[RTN_Propietario]
      ,[Nombre_Propietario]
      ,[ID_Placa]
      ,[ID_Marca]
      ,[Anio]
      ,[Modelo]
      ,[Tipo_Vehiculo]
      ,[ID_Color]
      ,[Motor]
      ,[Chasis]
      ,[Estado]
      ,[Sistema_Fecha]
      ,[Permiso_Explotacion]
      ,[Certificado_Operacion]
      ,[Permiso_Especial]
      ,[VIN]
      ,[Combustible]
      ,[Alto]
      ,[Ancho]
      ,[Largo]
      ,[Capacidad_Carga]
      ,[Peso_Unidad]
      ,[ID_Placa_Antes_Replaqueo]
      ,[Sistema_Usuario]
		FROM [IHTT_PREFORMA].[dbo].[TB_Vehiculo] veh
		where [ID_Formulario_Solicitud] = :ID_Formulario_Solicitud and ([Certificado_Operacion] = :Certificado_Operacion or [Permiso_Especial] = :Permiso_Especial) ORDER BY [Estado] DESC;";
		if (!isset($_POST["echo"])) {
			return $this->select($q, array(":ID_Formulario_Solicitud" => $RAM, ":Certificado_Operacion" => $idConcesion, ":Permiso_Especial" => $idConcesion));
		} else {
			echo json_encode($this->select($q, array()));
		}
	}

	//*************************************************************************************/
	//* FUNCION PARA RECUPERAR Aldea, Departamento y Municipio
	//*************************************************************************************/
	protected function ALDEASDEPARTAMENTO($col)
	{
		$query = "SELECT TB_Departamento.ID_Departamento, TB_Municipio.ID_Municipio, TB_Aldea.ID_Aldea 
		FROM [IHTT_PREFORMA].[dbo].[TB_Departamento] 
		INNER JOIN [IHTT_PREFORMA].[dbo].[TB_Municipio] ON TB_Departamento.ID_Departamento = TB_Municipio.ID_Departamento 
		INNER JOIN [IHTT_PREFORMA].[dbo].[TB_Aldea] ON TB_Municipio.ID_Municipio = TB_Aldea.ID_Municipio WHERE TB_Aldea.ID_Aldea = :IDCOL";
		$p = array(":IDCOL" => $col);
		$data = $this->select($query, $p);
		if (count($data) > 0) {
			return array("Aldea" => $data[0]["ID_Aldea"], "Municipio" => $data[0]["ID_Municipio"], "Departamento" => $data[0]["ID_Departamento"]);
		} else {
			return array("Aldea" => '', "Municipio" => '', "Departamento" => '');
		}
	}


	/*************************************************************************************/
	/* FUNCION PARA RECUPERAR EL SOLICITANTE
	/*************************************************************************************/
	protected function getSolicitante()
	{
		$query = "SELECT a.*,b.DESC_Solicitante,b.ID_Tipo_Solicitante FROM ihtt_preforma.dbo.v_Datos_Solicitante a,[IHTT_SELD].[dbo].[TB_Tipo_Solicitante] b WHERE a.CodigoSolicitanteTipo = b.ID_Tipo_Solicitante and a.ID_Solicitante = :IDSOL";
		$p = array(":IDSOL" => $_POST["idSolicitante"]);
		$data = $this->select($query, $p);
		$datos[0] = count($data);
		if (count($data) > 0) {
			$Aldeas = $this->ALDEASDEPARTAMENTO($data[0]["Aldea"]);
			$datos[1] = array(
				'DESC_Solicitante' => $data[0]["DESC_Solicitante"],
				'ID_Tipo_Solicitante' => $data[0]["ID_Tipo_Solicitante"],
				"rtn_solicitante" => $data[0]["RTNSolicitante"],
				"nombre_solicitante" => $data[0]["NombreSolicitante"],
				"nombre_empresa" => $data[0]["NombreEmpresa"],
				"codigo_tipo" => $data[0]["CodigoSolicitanteTipo"],
				"dir_solicitante" => $data[0]["Direccion"],
				"tel_solicitante" => $data[0]["Telefono"],
				"correo_solicitante" => $data[0]["Email"],
				"aldea" => $data[0]["Aldea"],
				'Aldeas' => $Aldeas['Aldea'],
				'Municipio' => $Aldeas['Municipio'],
				'Departamento' => $Aldeas['Departamento'],
				'Numero_Escritura' => $data[0]['ID_Escritura'],
				'Fecha_Escritura' => $data[0]['Fecha_Elaboracion'],
				'Lugar_Escritura' => $data[0]['Lugar_Elaboracion'],
				'ID_Notario' => $data[0]['ID_Notario'],
				'Notario' => $data[0]['Nombre_Notario'],
				'RTN_Representante' => $data[0]['ID_Representante_Legal'],
				'Nombre_Representante' => $data[0]['Nombre_Representante_Legal'],
				'Telefono_Representante' => $data[0]['Telefono_Representante'],
				'Email_Representante' => $data[0]['Email_Representante'],
				'Direccion_Representante' => $data[0]['Direccion_Representante'],
				'Representante_Escritura' => $data[0]['Representante_Escritura'],
				'Fecha_Elaboracion_Representante' => $data[0]['Fecha_Elaboracion_Representante'],
				'Lugar_Elaboracion_Representante' => $data[0]['Lugar_Elaboracion_Representante'],
				'Numero_Inscripcion' => $data[0]['Numero_Inscripcion'],
				'ID_Notario_Representante' => $data[0]['ID_Notario_Representante'],
				'Nombre_Notario_Representante' => $data[0]['Nombre_Notario_Representante']
			);
			$datos[2] = $this->getTipoSolicitante();
			$datos[3] = $this->getMunicipios(isset($Aldeas['Departamento']) ? $Aldeas['Departamento'] : 0);
			$datos[4] = $this->getAldeas(isset($Aldeas['Municipio']) ? $Aldeas['Municipio'] : 0);
		}
		echo json_encode($datos);
	}

	protected function getAvisodeCobroxPlaca($Numero_Placa)
	{
		$row_rs_stmt['error'] = false;
		$respuesta[0]['errorcode'] = '';
		try {
			$query_rs_stmt = "SELECT [Numero_Placa],[MONTO]
			FROM [IHTT_Webservice].[dbo].[TB_AvisoCobroEnc] ENC,[IHTT_Webservice].[dbo].[TB_AvisoCobroDET] DET, [IHTT_DB].[dbo].[TB_Tramite] TR
			WHERE ENC.AvisoCobroEstado = 2 AND ENC.CodigoAvisoCobro = DET.CodigoAvisoCobro AND DET.CodigoTipoTramite = TR.[ID_Tramite] AND TR.[ID_Tipo_Tramite] = 'IHTTTRA-03' AND (TR.[ID_Clase_Tramite] = 'CLATRA-15' OR TR.[ID_Clase_Tramite] = 'CLATRA-08') AND [Numero_Placa] = :Numero_Placa";
			// Recueprando la información del stmt
			$stmt = $this->db->prepare($query_rs_stmt);
			$stmt->execute(array(':Numero_Placa' => $Numero_Placa));
			$row_rs_stmt = $stmt->fetch();
			$res = $stmt->errorInfo();
			if (isset($res) and isset($res[3]) and intval(Trim($res[3])) <> 0) {
				$row_rs_stmt['error'] = true;
				$respuesta[0]['errorcode'] = $res[1];
				$row_rs_stmt['msg'] = "Mensaje de Error: " . $res[0] . ' ' . $res[1] . ' ' . $res[2] . ' ' . $res[3] . ' ';
				$txt = date('Y m d h:i:s') . '	' . 'Api_Ram.php getAvisodeCobroxPlaca.php Error ' . $query_rs_stmt  . ' res[0]' . $res[0] . ' res[1]' . $res[1] . ' res[2]' . $res[2] . ' res[3]' . $res[3];
				logErr($txt, '../logs/logs.txt');
			}
		} catch (\Throwable $th) {
			$row_rs_stmt['error'] = true;
			$respuesta[0]['errorcode'] = 0;
			$row_rs_stmt['msg'] = "Mensaje de Error: " . $th->getMessage();
			$txt = date('Y m d h:i:s') . '	' . 'Api_Ram.php getAvisodeCobroxPlaca.php catch ' . $query_rs_stmt . ' ERROR ' . $th->getMessage();
			logErr($txt, '../logs/logs.txt');
		}
		return $row_rs_stmt;
	}

	//*****************************************************************************************************/
	//* Inicio: Recuperando Tramites Inicialmente
	//*****************************************************************************************************/
	protected function getTipoTramiteyClaseTramite($filtro = array(), $ID_Categoria)
	{
		$joined_string = "'" . implode("', '", $filtro) . "'";
		$q =  "SELECT CONCAT(CONCAT(CONCAT(CONCAT(CONCAT(CONCAT( T.ID_Tipo_Tramite,'_'),C.ID_Clase_Tramite),'_'),T.Acronimo_Tramite),'_'),C.Acronimo_Clase) AS ID_CHECK,
					CONCAT(CONCAT (T.DESC_Tipo_Tramite,' '),C.DESC_Clase_Tramite) as descripcion_larga,
					T.ID_Tipo_Tramite,
					C.ID_Tipo_Tramite as ID_Tipo_Tramite_Array,
					T.DESC_Tipo_Tramite,
					T.Acronimo_Tramite,
					C.ID_Clase_Tramite,
					C.DESC_Clase_Tramite,
					C.Acronimo_Clase,
					TR.ID_Tramite,
					(select top 1 
					case 
					when B.Monto > 0.00 then Round(B.Monto,2)
					else Round((B.SalarioMinimo * (B.ValorFraccion/100)),2)
					end
					from [IHTT_Webservice].[dbo].[TB_Tarifas] A,[IHTT_Webservice].[dbo].[TB_TarifasHistorico] B 
					where A.CodigoTramite = B.CodigoTramite AND A.CodigoTramite = TR.ID_Tramite order by B.SistemaFecha desc) as Monto					
					FROM 
						[IHTT_DB].[DBO].[TB_TRAMITE] TR,[IHTT_DB].[DBO].[TB_TIPO_TRAMITE] T
					JOIN 
						[IHTT_DB].[DBO].[TB_CLASE_TRAMITE] C
					ON 
						C.ID_Tipo_Tramite LIKE '%' + CAST(T.ID_Tipo_Tramite AS VARCHAR) + '%'
					WHERE 
					    TR.ID_Tipo_Tramite = T.ID_Tipo_Tramite AND
						TR.ID_Clase_Tramite = C.ID_Clase_Tramite AND
						TR.ID_Categoria = :ID_Categoria AND
						T.Es_Renovacion_Automatica = 1 
						AND C.ID_Tipo_Tramite IS NOT NULL 
						AND C.Acronimo_Clase IN (" . $joined_string . ")						
					ORDER BY T.ID_Tipo_Tramite";
		$bandera = 1;
		if ($bandera == 1) {
			$html = '<div class="row border border-primary d-flex justify-content-center"><div class="col-md-12"><strong>LISTADO DE TRAMITES</strong></div></div>';
			$html = $html . '<div class="row"><div class="col-md-1"></div><div class="col-md-8"><strong>TRAMITE</strong></div><div class="col-md-3"><strong>PLACA</strong></div></div>';
			$rows = ($this->select($q, array(':ID_Categoria' => $ID_Categoria)));
			foreach ($rows as $row) {
				if ($row['Acronimo_Clase'] ==  'CU' || $row['Acronimo_Clase'] ==  'CL') {
					$html = $html . '<div class="row border border-info" id="row_tramite_' . $row['Acronimo_Tramite'] . '_' . $row['Acronimo_Clase'] . '">
					<div class="col-md-1">
					  <input data-monto="' . $row['Monto'] . '" 
							 class="form-check-input" 
							 onclick="fReviewCheck(this)" 
							 id="' . $row['ID_CHECK'] . '" 
							 type="checkbox" 
							 name="tramites[]" 
							 value="' . $row["ID_Tramite"] . '">
					</div>
					<div id="descripcion_' . $row["ID_Tramite"] . '" class="col-md-9">' . $row["descripcion_larga"] . '</div>
					<div class="col-md-2">
					  <input onchange="getVehiculoDesdeIP(this, \'' . $row['Acronimo_Clase'] . '\');" 
							 style="display:none; text-transform: uppercase;" 
							 id="concesion_tramite_placa_' . $row['Acronimo_Clase'] . '" 
							 title="La placa debe contener los primeros 3 digitos alfa y los últimos 4 numericos, máximo 7 caracteres" 
							 pattern="^[A-Z]{3}\d{4}$" 
							 placeholder="PLACA" 
							 class="form-control form-control-sm test-controls" 
							 minlength="7" 
							 maxlength="7">
					</div>
				  </div>';
				} else {
					$html = $html . '<div class="row border border-info" id="row_tramite_'  . $row['Acronimo_Tramite'] . '_' . $row['Acronimo_Clase']  .  '"><div class="col-md-1"><input data-monto="' . $row['Monto'] . '" class="form-check-input" onclick="fReviewCheck(this)" ' .  " id=" . $row['ID_CHECK']  .  ' type="checkbox" name="tramites[]" value="' . $row["ID_Tramite"] . '"></div><div id="descripcion_' . $row["ID_Tramite"] . '" class="col-md-9">' . $row["descripcion_larga"] . '</div><div class="col-md-2">&nbsp;</div></div>';
				}
			}
		} else {
			$html = '<div class="row border border-primary d-flex justify-content-center"><div class="col-md-12"><strong>LISTADO DE TRAMITES</strong></div></div>';
			$html = $html . '<div class="row"><div class="col-md-1"></div><div class="col-md-8"><strong>TRAMITE</strong></div><div class="col-md-3"><strong>PLACA</strong></div><div class="col-md-1"></div><div class="col-md-3"><strong>TRAMITE</strong></div><div class="col-md-2"><strong>PLACA</strong></div></div>';
			$rows = ($this->select($q, array(':ID_Categoria' => $ID_Categoria)));
			$process = 0;
			foreach ($rows as $row) {
				if ($process == 0) {
					$html = $html . '<div class="row border border-info" id="row_tramite_'  . $row['Acronimo_Tramite'] . '_' . $row['Acronimo_Clase']  .  '">';
				}
				if ($row['Acronimo_Clase'] ==  'CU' || $row['Acronimo_Clase'] ==  'CL') {
					$html = $html . '<div id="field1_tramite_' . $row['Acronimo_Tramite'] . '_' . $row['Acronimo_Clase'] . '" class="col-md-1">
					<input data-monto="' . $row['Monto'] . '" 
						   class="form-check-input" 
						   onclick="fReviewCheck(this)" 
						   id="' . $row['ID_CHECK'] . '" 
						   type="checkbox" 
						   name="tramites[]" 
						   value="' . $row["ID_Tramite"] . '">
				  </div>
				  <div id="descripcion_' . $row["ID_Tramite"] . '" class="col-md-3">
					' . $row["descripcion_larga"] . '
				  </div>
				  <div class="col-md-2">
					<input onchange="getVehiculoDesdeIP(this, \'' . $row['Acronimo_Clase'] . '\');" 
						   style="display:none; text-transform: uppercase;" 
						   id="concesion_tramite_placa_' . $row['Acronimo_Clase'] . '" 
						   title="La placa debe contener los primeros 3 dígitos alfa y los últimos 4 numéricos, máximo 7 caracteres" 
						   pattern="^[A-Z]{3}\d{4}$" 
						   placeholder="PLACA" 
						   class="form-control form-control-sm test-controls" 
						   minlength="7" 
						   maxlength="7">
				  </div>';
				} else {
					$html = $html . '<div class="col-md-1"><input data-monto="' . $row['Monto'] . '" class="form-check-input" onclick="fReviewCheck(this)" ' .  " id=" . $row['ID_CHECK']  .  ' type="checkbox" name="tramites[]" value="' . $row["ID_Tramite"] . '"></div><div id="descripcion_' . $row["ID_Tramite"] . '" class="col-md-3">' . $row["descripcion_larga"] . '</div><div class="col-md-2">&nbsp;</div>';
				}
				$process++;
				if ($process == 2) {
					$html = $html . '</div>';
					$process = 0;
				}
			}

			if ($process == 1) {
				$html = $html . '<div class="col-md-1"></div><div class="col-md-3"></div><div class="col-md-2"></div></div>';
			}
		}

		if (!isset($_POST["echo"])) {
			return $html;
		} else {
			echo json_encode($html);
		}
	}
	//*****************************************************************************************************/
	//* Final: Recuperando Tramites Inicialmente
	//*****************************************************************************************************/


	//*****************************************************************************************************/
	//* Inicio: Recuperando Tramites Preforma
	//*****************************************************************************************************/
	protected function getTipoTramiteyClaseTramitePreforma($filtro = array(), $ID_Categoria, $RAM, $idConcesion)
	{
		$joined_string = "'" . implode("', '", $filtro) . "'";
		$q =  "SELECT SOL.ID,
    CONCAT(T.ID_Tipo_Tramite, '_', C.ID_Clase_Tramite, '_', T.Acronimo_Tramite, '_', C.Acronimo_Clase) AS ID_CHECK,
    CONCAT(T.DESC_Tipo_Tramite, ' ', C.DESC_Clase_Tramite) AS descripcion_larga,
    T.ID_Tipo_Tramite,
    C.ID_Tipo_Tramite AS ID_Tipo_Tramite_Array,
    T.DESC_Tipo_Tramite,
    T.Acronimo_Tramite,
    C.ID_Clase_Tramite,
    C.DESC_Clase_Tramite,
    C.Acronimo_Clase,
    TR.ID_Tramite,
    (
        SELECT TOP 1 
            CASE 
                WHEN B.Monto > 0.00 THEN ROUND(B.Monto, 2)
                ELSE ROUND((B.SalarioMinimo * (B.ValorFraccion / 100)), 2)
            END AS Monto
        FROM 
            [IHTT_Webservice].[dbo].[TB_Tarifas] A
        INNER JOIN 
            [IHTT_Webservice].[dbo].[TB_TarifasHistorico] B 
        ON 
            A.CodigoTramite = B.CodigoTramite
        WHERE 
            A.CodigoTramite = TR.ID_Tramite
        ORDER BY 
            B.SistemaFecha DESC
    ) AS Monto,
    CASE 
        WHEN isnull(SOL.ID,0) = 0 THEN ''
        ELSE 'checked'
    END AS Checked
		FROM 
			[IHTT_DB].[dbo].[TB_CLASE_TRAMITE] C
		INNER JOIN 
			[IHTT_DB].[dbo].[TB_TIPO_TRAMITE] T 
		ON 
			C.ID_Tipo_Tramite LIKE '%' + CAST(T.ID_Tipo_Tramite AS VARCHAR) + '%'
		INNER JOIN 
			[IHTT_DB].[dbo].[TB_TRAMITE] TR
		ON 
			TR.ID_Tipo_Tramite = T.ID_Tipo_Tramite 
			AND TR.ID_Clase_Tramite = C.ID_Clase_Tramite
		LEFT OUTER JOIN 
			[IHTT_PREFORMA].[dbo].[TB_Solicitud] SOL 
		ON 
			TR.ID_Tramite = SOL.ID_Tramite and SOL.ID_Formulario_Solicitud = :ID_Formulario_Solicitud and (SOL.N_Certificado = :N_Certificado or SOL.N_Permiso_Especial = :N_Permiso_Especial)
		WHERE 
			TR.ID_Categoria = :ID_Categoria
			AND T.Es_Renovacion_Automatica = 1 
			AND C.ID_Tipo_Tramite IS NOT NULL 
			AND C.Acronimo_Clase IN (" . $joined_string . ")
		ORDER BY 
    T.ID_Tipo_Tramite;";
		$bandera = 1;
		if ($bandera == 1) {
			$html = '<div class="row border border-primary d-flex justify-content-center"><div class="col-md-12"><strong>LISTADO DE TRAMITES</strong></div></div>';
			$html = $html . '<div class="row"><div class="col-md-1"></div><div class="col-md-9"><strong>TRAMITE</strong></div><div class="col-md-2"><strong>PLACA</strong></div></div>';
			$rows = ($this->select($q, array(':ID_Formulario_Solicitud' => $RAM, ':N_Certificado' => $idConcesion, ':N_Permiso_Especial' => $idConcesion, ':ID_Categoria' => $ID_Categoria)));
			foreach ($rows as $row) {
				if ($row['Acronimo_Clase'] ==  'CU' || $row['Acronimo_Clase'] ==  'CL') {
					$html = $html . '<div class="row border border-info" id="row_tramite_' . $row['Acronimo_Tramite'] . '_' . $row['Acronimo_Clase'] . '">
					<div class="col-md-1">
					  <input ' . $row['Checked'] . ' data-iddb="' . $row['ID'] . '" data-monto="' . $row['Monto'] . '" 
							 class="form-check-input" 
							 onclick="fReviewCheck(this)" 
							 id="' . $row['ID_CHECK'] . '" 
							 type="checkbox" 
							 name="tramites[]" 
							 value="' . $row["ID_Tramite"] . '">
					</div>
					<div id="descripcion_' . $row["ID_Tramite"] . '" class="col-md-9">' . $row["descripcion_larga"] . '</div>
					<div class="col-md-2">
					  <input onchange="getVehiculoDesdeIP(this, \'' . $row['Acronimo_Clase'] . '\');" 
							 style="display:none; text-transform: uppercase;" 
							 id="concesion_tramite_placa_' . $row['Acronimo_Clase'] . '" 
							 title="La placa debe contener los primeros 3 digitos alfa y los últimos 4 numericos, máximo 7 caracteres" 
							 pattern="^[A-Z]{3}\d{4}$" 
							 placeholder="PLACA" 
							 class="form-control form-control-sm test-controls" 
							 minlength="7" 
							 maxlength="7">
					</div>
				  </div>';
				} else {
					$html = $html . '<div class="row border border-info" id="row_tramite_'  . $row['Acronimo_Tramite'] . '_' . $row['Acronimo_Clase']  .  '"><div class="col-md-1"><input  ' . $row['Checked']  . ' data-iddb="' . $row['ID'] . '"  data-monto="' . $row['Monto'] . '" class="form-check-input" onclick="fReviewCheck(this)" ' .  " id=" . $row['ID_CHECK']  .  ' type="checkbox" name="tramites[]" value="' . $row["ID_Tramite"] . '"></div><div id="descripcion_' . $row["ID_Tramite"] . '" class="col-md-9">' . $row["descripcion_larga"] . '</div><div class="col-md-2">&nbsp;</div></div>';
				}
			}
		} else {
			$html = '<div class="row border border-primary d-flex justify-content-center"><div class="col-md-12"><strong>LISTADO DE TRAMITES</strong></div></div>';
			$html = $html . '<div class="row"><div class="col-md-1"></div><div class="col-md-3"><strong>TRAMITE</strong></div><div class="col-md-2"><strong>PLACA</strong></div><div class="col-md-1"></div><div class="col-md-3"><strong>TRAMITE</strong></div><div class="col-md-2"><strong>PLACA</strong></div></div>';
			$rows = ($this->select($q, array(':ID_Categoria' => $ID_Categoria)));
			$process = 0;
			foreach ($rows as $row) {
				if ($process == 0) {
					$html = $html . '<div class="row border border-info" id="row_tramite_'  . $row['Acronimo_Tramite'] . '_' . $row['Acronimo_Clase']  .  '">';
				}
				if ($row['Acronimo_Clase'] ==  'CU' || $row['Acronimo_Clase'] ==  'CL') {
					$html = $html . '<div id="field1_tramite_' . $row['Acronimo_Tramite'] . '_' . $row['Acronimo_Clase'] . '" class="col-md-1">
					<input ' . $row['Checked']  . ' data-iddb="' . $row['ID'] .  '"  data-monto="' . $row['Monto'] . '" 
						   class="form-check-input" 
						   onclick="fReviewCheck(this)" 
						   id="' . $row['ID_CHECK'] . '" 
						   type="checkbox" 
						   name="tramites[]" 
						   value="' . $row["ID_Tramite"] . '">
				  </div>
				  <div id="descripcion_' . $row["ID_Tramite"] . '" class="col-md-3">
					' . $row["descripcion_larga"] . '
				  </div>
				  <div class="col-md-2">
					<input onchange="getVehiculoDesdeIP(this, \'' . $row['Acronimo_Clase'] . '\');" 
						   style="display:none; text-transform: uppercase;" 
						   id="concesion_tramite_placa_' . $row['Acronimo_Clase'] . '" 
						   title="La placa debe contener los primeros 3 dígitos alfa y los últimos 4 numéricos, máximo 7 caracteres" 
						   pattern="^[A-Z]{3}\d{4}$" 
						   placeholder="PLACA" 
						   class="form-control form-control-sm test-controls" 
						   minlength="7" 
						   maxlength="7">
				  </div>';
				} else {
					$html = $html . '<div class="col-md-1"><input ' . $row['Checked']  . ' data-iddb="' . $row['ID'] .  '" data-monto="' . $row['Monto'] . '" class="form-check-input" onclick="fReviewCheck(this)" ' .  " id=" . $row['ID_CHECK']  .  ' type="checkbox" name="tramites[]" value="' . $row["ID_Tramite"] . '"></div><div id="descripcion_' . $row["ID_Tramite"] . '" class="col-md-3">' . $row["descripcion_larga"] . '</div><div class="col-md-2">&nbsp;</div>';
				}
				$process++;
				if ($process == 2) {
					$html = $html . '</div>';
					$process = 0;
				}
			}

			if ($process == 1) {
				$html = $html . '<div class="col-md-1"></div><div class="col-md-3"></div><div class="col-md-2"></div></div>';
			}
		}

		if (!isset($_POST["echo"])) {
			return $html;
		} else {
			echo json_encode($html);
			exit();
		}
	}
	//*****************************************************************************************************/
	//* Final: Recuperando Tramites Preforma
	//*****************************************************************************************************/	

	protected function getColorByDesc($DescColor)
	{
		$respuesta[0]['msg'] = '';
		$respuesta[0]['errorcode'] = 0;
		$respuesta[0]['error'] = false;
		// Recueprando la información del color
		$query_rs_color = "SELECT ID_Color,DESC_Color FROM [IHTT_SGCERP].[dbo].[TB_Color_Vehiculos] where DESC_Color = :DESC_Color";
		try {
			$color = $this->db->prepare($query_rs_color);
			$color->execute(array(':DESC_Color' => $DescColor));
			$res = $color->errorInfo();
			if (isset($res) and isset($res[3]) and intval(Trim($res[3])) <> 0) {
				$respuesta[0]['error'] = true;
				$respuesta[0]['errorcode'] = $res[1];
				$respuesta[0]['msg'] = "Mensaje de Error: " . $res[0] . ' ' . $res[1] . ' ' . $res[2] . ' ' . $res[3] . ' ';
				$txt = date('Y m d h:i:s') . '	' . 'getColor.php Error ' . $query_rs_color  . ' res[0]' . $res[0] . ' res[1]' . $res[1] . ' res[2]' . $res[2] . ' res[3]' . $res[3];
				logErr($txt, 'logs/logs.txt');
			} else {
				$respuesta[1]['data'] = $color->fetch();
			}
		} catch (\Throwable $th) {
			$respuesta[0]['msg'] = $th->getMessage();
			$respuesta[0]['errorcode'] = -1;
			$respuesta[0]['error'] = true;
			$txt = date('Y m d h:i:s') . '	' . 'getColor.php catch ' . $query_rs_color . ' ERROR ' . $th->getMessage();
			logErr($txt, 'logs/logs.txt');
		}
		return $respuesta;
	}

	protected function getMarcaByDesc($DescMarca)
	{
		$respuesta[0]['msg'] = '';
		$respuesta[0]['errorcode'] = 0;
		$respuesta[0]['error'] = false;
		// Recueprando la información de la marca
		$query_rs_Marca = "SELECT ID_Marca,DESC_Marca FROM [IHTT_SGCERP].[dbo].[TB_Marca_Vehiculo] where DESC_Marca = :DESC_Marca";
		try {
			$marca = $this->db->prepare($query_rs_Marca);
			$marca->execute(array(':DESC_Marca' => $DescMarca));
			$res = $marca->errorInfo();
			if (isset($res) and isset($res[3]) and intval(Trim($res[3])) <> 0) {
				$respuesta[0]['error'] = true;
				$respuesta[0]['errorcode'] = $res[1];
				$respuesta[0]['msg'] = "Mensaje de Error: " . $res[0] . ' ' . $res[1] . ' ' . $res[2] . ' ' . $res[3] . ' ';
				$txt = date('Y m d h:i:s') . '	' . 'getMarca.php Error ' . $query_rs_Marca  . ' res[0]' . $res[0] . ' res[1]' . $res[1] . ' res[2]' . $res[2] . ' res[3]' . $res[3];
				logErr($txt, 'logs/logs.txt');
			} else {
				$respuesta[1]['data'] = $marca->fetch();
			}
		} catch (\Throwable $th) {
			$respuesta[0]['msg'] = $th->getMessage();
			$respuesta[0]['errorcode'] = -1;
			$respuesta[0]['error'] = true;
			$txt = date('Y m d h:i:s') . '	' . 'getMarca.php catch ' . $query_rs_Marca . ' ERROR ' . $th->getMessage();
			logErr($txt, 'logs/logs.txt');
		}
		return $respuesta;
	}

	/*****************************************************************************************/
	/* Funcion que hace el llamado al IP para recupera la información de la unidad
	/*****************************************************************************************/
	protected function file_contents($path)
	{
		try {
			$str = @file_get_contents($path);
			//print('str '. $path . ' '.$str);die();
			if ($str === FALSE) {
				//$txt = date('Y m d h:i:s') . ';  ERROR CATCH 408 LLAMANDO; ' . $path . ";Cannot access to read contents. Favor ingrese el tramite de replaqueo y los datos del vehiculo manualmente, SI APLICA";
				//logErr($txt,'../logs/logs-ip.txt');
				$vehiculo['mensaje'] = 'Conexión con el IP no accessible en este momento, favor ingrese el tramite de replaqueo y los datos del vehiculo manualmente, si aplica. (CODIGO DE MSG-:408)';
				$vehiculo['codigo'] = 408;
				return json_encode($vehiculo);
			} else {
				return $str;
			}
		} catch (Exception $e) {
			//$txt = date('Y m d h:i:s') . '; ERROR CATCH 407 LLAMANDO; ' . $path . '; MSG ERROR;' . $e->getMessage();
			//logErr($txt,'../logs/logs-ip.txt');
			$vehiculo['mensaje'] = 'Conexión con el IP no accesible en este momento, favor ingrese el tramite de replaqueo y los datos del vehiculo manualmente, si aplica. (CODIGO DE MSG-:407) ' . $e->getMessage();
			$vehiculo['codigo'] = 407;
			return json_encode($vehiculo);
		}
	}

	/*****************************************************************************************/
	/* INICIO FUNCION PARA RECUPERAR LA INFORMACIÓN DE LA UNIDAD DEL INSTITUO DE LA PROPIEDAD
	/*****************************************************************************************/
	protected function getDatosUnidadDesdeIP($placa)
	{
		$vehiculo = json_decode($this->file_contents($_SESSION["appcfg_Dominio_Raiz"] . ":184/api/Unidad/ConsultarDatosIP/" . $placa));
		//Recuperando el codigo de la marca del vehiculo
		if (isset($vehiculo->codigo) == true && $vehiculo->codigo == 200) {
			$marca = $this->getMarcaByDesc($vehiculo->cargaUtil->marca);
			if (isset($marca[0]['error']) == true && $marca[0]['error'] == false) {
				if (isset($marca) && isset($marca[1]) && isset($marca[1]['data']) && isset($marca[1]['data']['ID_Marca']) && $marca[1]['data']['ID_Marca'] != '') {
					$vehiculo->cargaUtil->marcacodigo = $marca[1]['data']['ID_Marca'];
				} else {
					$vehiculo->cargaUtil->marcacodigo = '';
				}
			}
			//Recuperando el codigo del color de vehiculo
			$color = $this->getColorByDesc($vehiculo->cargaUtil->color);
			if (isset($color[0]['error']) == true && $color[0]['error'] == false) {
				if (isset($color) && isset($color[1]) && isset($color[1]['data']) && isset($color[1]['data']['ID_Marca']) && $color[1]['data']['ID_Color'] != '') {
					$vehiculo->cargaUtil->colorcodigo = $color[1]['data']['ID_Color'];
				} else {
					$vehiculo->cargaUtil->colorcodigo = '';
				}
			}
			//**********************************************************************************************************************/
			// Recuperando las multas del vehiculo
			//**********************************************************************************************************************/
			if ($_POST["action"] == "get-vehiculo") {
				$vehiculo->cargaUtil->Multas = $this->getDatosMulta($vehiculo->cargaUtil->placa, $vehiculo->cargaUtil->placaAnterior);
				$vehiculo->cargaUtil->Preformas = $this->validarEnPreforma($vehiculo->cargaUtil->placa, $vehiculo->cargaUtil->placaAnterior, $_POST["Concesion"]);
			}
		}
		return $vehiculo;
	}

	/*************************************************************************************/
	/* FUNCION PARA RECUPERAR LA CONCEPCION ACTUAL DEL VEHICULO Y SI NECESITA RENOVACION
	/* POR CUENTOS PERIODOS DE TIEMPO
	/*************************************************************************************/
	protected function procesarFechaDeVencimiento($record, $id_clase_servico)
	{
		$respuesta[0]['msg'] = "";
		$respuesta[0]['error'] = false;
		$respuesta[0]['errorcode'] = '';
		//****************************************************************************//	
		// Si se tramite el tramite de renocación del certificado se calcula la nueva 
		// fecha de vencimiento
		//****************************************************************************//	
		$renovacion_certificado_vencido = false;
		$renovacion_permiso_especial_vencido = false;
		$renovacion_permisoexplotacion_vencido = false;
		if (isset($record["Fecha_Expiracion"])) {
			$Nueva_Fecha_Expiracion = date('Y-m-d', strtotime($record["Fecha_Expiracion"]));
			$hoyplus60 = date('Y-m-d', strtotime('+60 days'));
			$contadorconcesion = 0;
			while ($Nueva_Fecha_Expiracion <= $hoyplus60) {
				$record['rencon'][$contadorconcesion]['periodo'] = ' del ' . $Nueva_Fecha_Expiracion;
				if ($id_clase_servico == 'STPC' or $id_clase_servico == 'STPP') {
					$Nueva_Fecha_Expiracion = date("Y-m-d", strtotime($Nueva_Fecha_Expiracion . "+ 3 years"));
					$renovacion_certificado_vencido = true;
				} else {
					$Nueva_Fecha_Expiracion = date("Y-m-d", strtotime($Nueva_Fecha_Expiracion . "+ 1 year"));
					$renovacion_permiso_especial_vencido = true;
				}
				$record['rencon'][$contadorconcesion]['periodo'] = $record['rencon'][$contadorconcesion]['periodo'] . ' al ' . $Nueva_Fecha_Expiracion;
				$contadorconcesion++;
			}
			$record['rencon-cantidad'] = $contadorconcesion;
			$record['Nueva_Fecha_Expiracion'] = $Nueva_Fecha_Expiracion;
			//****************************************************************************//	
			// Si se tramite el tramite de renocacion del certificado se calcula la nueva 
			// fecha de vencimiento
			//****************************************************************************//	
			if (isset($record["Fecha_Expiracion_Explotacion"])) {
				$Nueva_Fecha_Expiracion = date('Y-m-d', strtotime($record["Fecha_Expiracion_Explotacion"]));
				$hoyplus60 = date('Y-m-d', strtotime('+60 days'));
				$contadorpermisoexplotacion = 0;
				while ($Nueva_Fecha_Expiracion <= $hoyplus60) {
					$record['renperexp'][$contadorpermisoexplotacion]['periodo'] = ' del ' . $Nueva_Fecha_Expiracion;
					$Nueva_Fecha_Expiracion = date("Y-m-d", strtotime($Nueva_Fecha_Expiracion . "+ 12 years"));
					$record['renperexp'][$contadorpermisoexplotacion]['periodo'] = $record['renperexp'][$contadorpermisoexplotacion]['periodo'] . ' al ' . $Nueva_Fecha_Expiracion;
					$renovacion_permisoexplotacion_vencido = true;
					$contadorpermisoexplotacion++;
				}
				$record['renper-explotacion-cantidad'] = $contadorpermisoexplotacion;
				$record['Nueva_Fecha_Expiracion_Explotacion'] = $Nueva_Fecha_Expiracion;
			} else {
				$record['Nueva_Fecha_Expiracion_Explotacion'] = null;
				$record['renper-explotacion-cantidad'] = 0;
			}

			$record['renovacion_permisoexplotacion_vencido'] = $renovacion_permisoexplotacion_vencido;
			$record['renovacion_permiso_especial_vencido'] = $renovacion_permiso_especial_vencido;
			$record['renovacion_certificado_vencido'] = $renovacion_certificado_vencido;
		} else {

			$record['Nueva_Fecha_Expiracion_Explotacion'] = null;
			$record['Nueva_Fecha_Expiracion'] = null;
			$record['renper-explotacion-cantidad'] = 0;
			$record['rencon-cantidad'] = 0;
			$record['renovacion_permiso_especial_vencido'] = $renovacion_permiso_especial_vencido;
			$record['renovacion_certificado_vencido'] = $renovacion_certificado_vencido;
			$record['renovacion_permisoexplotacion_vencido'] = $renovacion_permisoexplotacion_vencido;
		}
		$respuesta[1] = $record;

		return $respuesta;
	}
	//*****************************************************************************************/
	//Funcion para recuperar las multas por placa
	//*****************************************************************************************/
	protected function getDatosMulta($placa, $placa_anterior): mixed
	{
		$query = "SELECT * FROM [IHTT_MULTAS].[dbo].[V_Multas_IHTT_DGT] MUL,[IHTT_Webservice].[dbo].[TB_AvisoCobroEnc] Avi
		left outer join [IHTT_Webservice].[dbo].[TB_ArregloEnc] Enc on Avi.CodigoAvisoCobro  = Enc.ID_Aviso
		WHERE MUL.ID_Estado='1' AND MUL.Multa = avi.ID_Solicitud and 
		(MUL.Placa= :Placa_Actual or MUL.Placa= :Placa_Anterior) and
		(
		(
		select count(*) from [IHTT_Webservice].[dbo].[TB_AvisoCobroEnc] Encx where Encx.AvisoCobroEstado = 1 and
		Encx.ID_Solicitud  = Enc.Numero_Arreglo
		) =  0
		or
		(
		select count(*) from [IHTT_Webservice].[dbo].[TB_AvisoCobroEnc] Encx where Encx.AvisoCobroEstado = 1 and
		Encx.ID_Solicitud  = Enc.Numero_Arreglo and Encx.FechaVencimiento < GETDATE()
		) > 0);";
		$parametros = array(":Placa_Actual" => $placa, ":Placa_Anterior" => $placa_anterior);
		$row = $this->select($query, $parametros);
		if (count($row) > 0) {
			$titulos = [
				0 => 'ID MULTA',
				1  => 'FECHA MULTA',
				2 => 'PROPIETARIO UNIDAD',
				3 => 'IDENTIFICACIÓN',
				4 => 'CONCESIONARIO',
				5 => 'PLACA',
				6 => 'MONTO',
				'Multa' => 'ID MULTA',
				'FECHA MULTA'  => 'FECHA MULTA',
				'PROPIETARIO UNIDAD' => 'PROPIETARIO UNIDAD',
				'IDENTIFICACION' => 'IDENTIFICACIÓN',
				'CONCESIONARIO' => 'CONCESIONARIO',
				'PPLACA' => 'PLACA',
				'MONTO' => 'MONTO'
			];
			$row[count($row) + 1] = $titulos;
		}
		return $row;
	}
	//*************************************************************************************/
	//* FUNCION PARA RECUPERAR EL SOLICITANTE
	//*************************************************************************************/
	protected function getConcesion()
	{

		$query = "select * from IHTT_SGCERP.dbo.v_Listado_General WHERE N_Certificado = :N_Certificado and RTN_Concesionario = :RTN_Concesionario";
		$p = array(":N_Certificado" => $_POST["Concesion"], ":RTN_Concesionario" => $_POST["RTN_Concesionario"]);
		$data = $this->select($query, $p);
		$datos[0] = count($data);

		if ($datos[0] > 0) {
			$data[0]["Marcas"] = $this->getMarca();
			$data[0]["Anios"] = $this->getAnios();
			$data[0]["Colores"] = $this->getColor();
			$data[0]["Tipo_Categoria_Especilizada"] = '';
			$data[0]["Desc_Categoria_Especilizada"] = '';
			$data[0]["ID_Area_Operacion"] = '';
			$data[0]["Desc_Area_Operacion"] = '';
			$record["Fecha_Expiracion"] = $data[0]["Fecha Vencimiento Certificado"];
			$record["Fecha_Expiracion_Explotacion"] = $data[0]["Fecha Vencimiento Permiso"];
			$data[0]["Vencimientos"] = $this->procesarFechaDeVencimiento($record, $data[0]["ID_Clase_Servico"])[1];
			$data[0]["Unidad"][0]['ID_Marca'] = '';
			$data[0]["Unidad"][0]['ID_Color'] = '';
			if ($data[0]["Clase Servicio"] == 'STEC') {
				$data[0]["Link"] = $_SESSION["appcfg_Dominio_Raiz"] . ":172/api_rep.php?action=get-PDFPermisoEsp-Carga&PermisoEspecial=" . $data[0]["CertificadoEncriptado"];
				$data[0]["Vista"] = file_get_contents("vistas/pes_carga.html");
				$data[0]["Unidad"] = $this->getUnidad("[IHTT_SGCERP].[DBO].[TB_Vehiculo_Transporte_Carga] v,IHTT_SGCERP.[DBO].[TB_Vehiculo_Transporte_Carga_x_Placa] p,IHTT_SGCERP.[DBO].[TB_Tipo_Vehiculo_Transporte_Carga] t where v.ID_Vehiculo_Carga = p.ID_Vehiculo_Carga and v.ID_Tipo_Vehiculo_Carga = t.ID_Tipo_Vehiculo_Carga and p.Estado = 'ACTIVA' and ", " v.ID_Vehiculo_Carga ", $data[0]["ID_Vehiculo"], " t.DESC_Tipo_Vehiculo,* ");
				if ($data && $data[0] && $data[0]["Unidad"] && $data[0]["Unidad"][0]['ID_Placa']) {
					$vehiculo = $this->getDatosUnidadDesdeIP($data[0]["Unidad"][0]['ID_Placa']);
					//******************************************************************************************/
					// Guardando el codigo de respuesta de la solicutud al IP
					//******************************************************************************************/
					if (!isset($vehiculo->codigo)) {
						$data[0]["Codigo_IP"] = false;
					} else {
						$data[0]["Codigo_IP"] = $vehiculo->codigo;
					}
					if (isset($vehiculo->codigo) == true && $vehiculo->codigo == 200) {
						$data[0]["Unidad"][0]['estaPagadoElCambiodePlaca'] = false;
						if ($vehiculo->cargaUtil->placa != $vehiculo->cargaUtil->placaAnterior) {
							//**********************************************************************************************************************/
							// Si el vehiculo tiene cambio de placa se verifica si ese cambio de placa esta pagado
							//**********************************************************************************************************************/
							$row_rs_stmt = $this->getAvisodeCobroxPlaca($vehiculo->cargaUtil->placa);
							if (isset($row_rs_stmt['MONTO'])) {
								$data[0]["Unidad"][0]['estaPagadoElCambiodePlaca'] = true;
								$vehiculo->cargaUtil->estaPagadoElCambiodePlaca = true;
							} else {
								$data[0]["Unidad"][0]['estaPagadoElCambiodePlaca'] = false;
								$vehiculo->cargaUtil->estaPagadoElCambiodePlaca = false;
							}
						}
						//**********************************************************************************************************************/
						// Recuperando las multas del vehiculo
						//**********************************************************************************************************************/
						$data[0]["Unidad"][0]['Multas'] = $this->getDatosMulta($vehiculo->cargaUtil->placa, $vehiculo->cargaUtil->placaAnterior);
						$data[0]["Unidad"][0]['Preforma'] = $this->validarEnPreforma($vehiculo->cargaUtil->placa, $vehiculo->cargaUtil->placaAnterior, $_POST["Concesion"]);
						$data[0]["Unidad"][0]['VIN'] = $vehiculo->cargaUtil->vin;
						$data[0]["Unidad"][0]['Motor'] = $vehiculo->cargaUtil->motor;
						$data[0]["Unidad"][0]['Chasis'] = $vehiculo->cargaUtil->chasis;
						if (isset($vehiculo->cargaUtil->marcacodigo)) {
							$data[0]["Unidad"][0]['ID_Marca'] = $vehiculo->cargaUtil->marcacodigo;
						}
						if (isset($vehiculo->cargaUtil->colorcodigo)) {
							$data[0]["Unidad"][0]['ID_Color'] = $vehiculo->cargaUtil->colorcodigo;
						}
						$data[0]["Unidad"][0]['Anio'] = $vehiculo->cargaUtil->axo;
						$data[0]["Unidad"][0]['ID_Placa'] = $vehiculo->cargaUtil->placa;
						$data[0]["Unidad"][0]['ID_Placa_Anterior'] = $vehiculo->cargaUtil->placaAnterior;
						$data[0]["Unidad"][0]['Combustible'] = $vehiculo->cargaUtil->combustible;
						$data[0]["Unidad"][0]['Modelo'] = $vehiculo->cargaUtil->modelo;
						$data[0]["Unidad"][0]['Tipo'] = $vehiculo->cargaUtil->tipo;
						$data[0]["Unidad"][0]['RTN_Propietario'] = $vehiculo->cargaUtil->propietario->identificacion;
						$data[0]["Unidad"][0]['Nombre_Revicion'] = strtoupper($vehiculo->cargaUtil->propietario->nombre);
						$data[0]["Unidad"][0]['Estado_Vehiculo'] = strtoupper($vehiculo->cargaUtil->estadoVehiculo);

						if (strtoupper($vehiculo->cargaUtil->estadoVehiculo) == 'NO BLOQUEADO') {
							$data[0]["Unidad"][0]['Bloqueado'] = false;
							$vehiculo->cargaUtil->Bloqueado = false;
						} else {
							$data[0]["Unidad"][0]['Bloqueado'] = true;
							$vehiculo->cargaUtil->Bloqueado = true;
						}
					}
				} else {
					$data[0]["Unidad_IP"][0] = false;
				}
				$data[0]["Tipo_Concesion"] = 'PERMISO ESPECIAL:';
				$data[0]["Tramites"] = $this->getTipoTramiteyClaseTramite(['PS', 'CU', 'CL', 'CM', 'CC', 'CS', 'X'], $data[0]["Categoria"]);
				$tipo = $this->getCategoriaEspecilizadaCarga();
				if (count($tipo) > 0) {
					$data[0]["Tipo_Categoria_Especilizada"] = $tipo[0]['value'];
					$data[0]["Desc_Categoria_Especilizada"] = $tipo[0]['text'];
				}
			} else {
				if ($data[0]["Clase Servicio"] == 'STEP') {
					$data[0]["Tipo_Concesion"] = 'PERMISO ESPECIAL:';
					$data[0]["Tramites"] = $this->getTipoTramiteyClaseTramite(['PS', 'CU', 'CL', 'CM', 'CC', 'CS', 'X'], $data[0]["Categoria"]);
					$area = $this->getAreaOperacion();
					if (count($area) > 0) {
						$data[0]["Tipo_Categoria_Especilizada"] = $area[0]['value'];
						$data[0]["Desc_Categoria_Especilizada"] = $area[0]['text'];
					}
					$data[0]["Link"] = $_SESSION["appcfg_Dominio_Raiz"] . ":172/api_rep.php?action=get-PDFPermisoEsp-Pas&PermisoEspecial=" . $data[0]["CertificadoEncriptado"];
					$data[0]["Vista"] = file_get_contents("vistas/pes_pasajero.html");
					$data[0]["Unidad"] = $this->getUnidad("[IHTT_SGCERP].[DBO].[TB_Vehiculo_Transporte_Pasajero] v,IHTT_SGCERP.[DBO].[TB_Vehiculo_Transporte_Pasajero_x_Placa] p,IHTT_SGCERP.[DBO].[TB_Tipo_Vehiculo_Transporte_Pasajero] t where v.ID_Vehiculo_Transporte = p.ID_Vehiculo_Transporte and v.ID_Tipo_Vehiculo_Transporte_Pas = t.ID_Tipo_Vehiculo_Transporte_Pas and p.Estado = 'ACTIVA' and ", " v.ID_Vehiculo_Transporte ", $data[0]["ID_Vehiculo"], " DESC_Tipo_Vehiculo_Transporte_Pas as DESC_Tipo_Vehiculo,* ");
					if ($data && $data[0] && $data[0]["Unidad"] && $data[0]["Unidad"][0]['ID_Placa']) {
						$vehiculo = $this->getDatosUnidadDesdeIP($data[0]["Unidad"][0]['ID_Placa']);
						$data[0]["Unidad"][0]['estaPagadoElCambiodePlaca'] = false;
						//******************************************************************************************/
						// Guardando el codigo de respuesta de la solicutud al IP
						//******************************************************************************************/
						if (!isset($vehiculo->codigo)) {
							$data[0]["Codigo_IP"] = false;
						} else {
							$data[0]["Codigo_IP"] = $vehiculo->codigo;
						}
						if (isset($vehiculo->codigo) == true && $vehiculo->codigo == 200) {
							if ($vehiculo->cargaUtil->placa != $vehiculo->cargaUtil->placaAnterior) {
								//**********************************************************************************************************************/
								// Si el vehiculo tiene cambio de placa se verifica si ese cambio de placa esta pagado
								//**********************************************************************************************************************/
								$row_rs_stmt = $this->getAvisodeCobroxPlaca($vehiculo->cargaUtil->placa);
								if (isset($row_rs_stmt['MONTO'])) {
									$data[0]["Unidad"][0]['estaPagadoElCambiodePlaca'] = true;
									$vehiculo->cargaUtil->estaPagadoElCambiodePlaca = true;
								} else {
									$data[0]["Unidad"][0]['estaPagadoElCambiodePlaca'] = false;
									$vehiculo->cargaUtil->estaPagadoElCambiodePlaca = false;
								}
							}
							//**********************************************************************************************************************/
							// Recuperando las multas del vehiculo
							//**********************************************************************************************************************/
							$data[0]["Unidad"][0]['Multas'] = $this->getDatosMulta($vehiculo->cargaUtil->placa, $vehiculo->cargaUtil->placaAnterior);
							$data[0]["Unidad"][0]['Preforma'] = $this->validarEnPreforma($vehiculo->cargaUtil->placa, $vehiculo->cargaUtil->placaAnterior, $_POST["Concesion"]);
							$data[0]["Unidad"][0]['VIN'] = $vehiculo->cargaUtil->vin;
							$data[0]["Unidad"][0]['Motor'] = $vehiculo->cargaUtil->motor;
							$data[0]["Unidad"][0]['Chasis'] = $vehiculo->cargaUtil->chasis;
							if (isset($vehiculo->cargaUtil->marcacodigo)) {
								$data[0]["Unidad"][0]['ID_Marca'] = $vehiculo->cargaUtil->marcacodigo;
							}
							if (isset($vehiculo->cargaUtil->colorcodigo)) {
								$data[0]["Unidad"][0]['ID_Color'] = $vehiculo->cargaUtil->colorcodigo;
							}
							$data[0]["Unidad"][0]['Anio'] = $vehiculo->cargaUtil->axo;
							$data[0]["Unidad"][0]['ID_Placa'] = $vehiculo->cargaUtil->placa;
							$data[0]["Unidad"][0]['ID_Placa_Anterior'] = $vehiculo->cargaUtil->placaAnterior;
							$data[0]["Unidad"][0]['Combustible'] = $vehiculo->cargaUtil->combustible;
							$data[0]["Unidad"][0]['Modelo'] = $vehiculo->cargaUtil->modelo;
							$data[0]["Unidad"][0]['Tipo'] = $vehiculo->cargaUtil->tipo;
							$data[0]["Unidad"][0]['RTN_Propietario'] = $vehiculo->cargaUtil->propietario->identificacion;
							$data[0]["Unidad"][0]['Nombre_Revicion'] = strtoupper($vehiculo->cargaUtil->propietario->nombre);
							$data[0]["Unidad"][0]['Estado_Vehiculo'] = strtoupper($vehiculo->cargaUtil->estadoVehiculo);
							if (strtoupper($vehiculo->cargaUtil->estadoVehiculo) == 'NO BLOQUEADO') {
								$data[0]["Unidad"][0]['Bloqueado'] = false;
							} else {
								$data[0]["Unidad"][0]['Bloqueado'] = true;
							}
						}
						//$data[0]["Unidad_IP"] = $vehiculo;
					}
				} else {
					if ($data[0]["Clase Servicio"] == 'STPC') {
						//$data[0]["Unidad_IP"] = $vehiculo;
						$data[0]["Tipo_Concesion"] = 'CERTIFICADO DE OPERACIÓN:';
						$data[0]["Tramites"] = $this->getTipoTramiteyClaseTramite(['PE', 'CO', 'CL', 'CM', 'CC', 'CS', 'PE', 'CU'], $data[0]["Categoria"]);
						$tipo = $this->getCategoriaEspecilizadaCarga($data[0]["Id_Tipo_Categoria"]);
						if (count($tipo) > 0) {
							$data[0]["Tipo_Categoria_Especilizada"] = $tipo[0]['value'];
							$data[0]["Desc_Categoria_Especilizada"] = $tipo[0]['text'];
						}
						// Pendiente de ir a trae el certificado de explotación
						//$data[0]["Link1"] = "https://satt2.transporte.gob.hn:172/api_rep.php?action=get-PDFPermisoExp-Carga&Permiso=".$data[0]["PerExpEncriptado"];
						$data[0]["Link"] = $_SESSION["appcfg_Dominio_Raiz"] . ":172/api_rep.php?action=get-PDFCertificado-Carga&Certificado=" . $data[0]["CertificadoEncriptado"];
						$data[0]["Vista"] = file_get_contents("vistas/certificado_carga.html");
						$data[0]["Unidad"] = $this->getUnidad("[IHTT_SGCERP].[DBO].[TB_Vehiculo_Transporte_Carga] v,IHTT_SGCERP.[DBO].[TB_Vehiculo_Transporte_Carga_x_Placa] p,IHTT_SGCERP.[DBO].[TB_Tipo_Vehiculo_Transporte_Carga] t where v.ID_Vehiculo_Carga = p.ID_Vehiculo_Carga and v.ID_Tipo_Vehiculo_Carga = t.ID_Tipo_Vehiculo_Carga and p.Estado = 'ACTIVA' and", " v.ID_Vehiculo_Carga ", $data[0]["ID_Vehiculo"], " t.DESC_Tipo_Vehiculo,* ");
						if ($data && $data[0] && $data[0]["Unidad"] && $data[0]["Unidad"][0]['ID_Placa']) {
							$vehiculo = $this->getDatosUnidadDesdeIP($data[0]["Unidad"][0]['ID_Placa']);
							$data[0]["Unidad"][0]['estaPagadoElCambiodePlaca'] = false;
							//******************************************************************************************/
							// Guardando el codigo de respuesta de la solicutud al IP
							//******************************************************************************************/
							if (!isset($vehiculo->codigo)) {
								$data[0]["Codigo_IP"] = false;
							} else {
								$data[0]["Codigo_IP"] = $vehiculo->codigo;
							}
							if (isset($vehiculo->codigo) == true && $vehiculo->codigo == 200) {
								if ($vehiculo->cargaUtil->placa != $vehiculo->cargaUtil->placaAnterior) {
									//**********************************************************************************************************************/
									// Si el vehiculo tiene cambio de placa se verifica si ese cambio de placa esta pagado
									//**********************************************************************************************************************/
									$row_rs_stmt = $this->getAvisodeCobroxPlaca($vehiculo->cargaUtil->placa);
									if (isset($row_rs_stmt['MONTO'])) {
										$data[0]["Unidad"][0]['estaPagadoElCambiodePlaca'] = true;
										$vehiculo->cargaUtil->estaPagadoElCambiodePlaca = true;
									} else {
										$data[0]["Unidad"][0]['estaPagadoElCambiodePlaca'] = false;
										$vehiculo->cargaUtil->estaPagadoElCambiodePlaca = false;
									}
								}
								//**********************************************************************************************************************/
								// Recuperando las multas del vehiculo
								//**********************************************************************************************************************/
								$data[0]["Unidad"][0]['Multas'] = $this->getDatosMulta($vehiculo->cargaUtil->placa, $vehiculo->cargaUtil->placaAnterior);
								$data[0]["Unidad"][0]['Preforma'] = $this->validarEnPreforma($vehiculo->cargaUtil->placa, $vehiculo->cargaUtil->placaAnterior, $_POST["Concesion"]);
								$data[0]["Unidad"][0]['VIN'] = $vehiculo->cargaUtil->vin;
								$data[0]["Unidad"][0]['Motor'] = $vehiculo->cargaUtil->motor;
								$data[0]["Unidad"][0]['Chasis'] = $vehiculo->cargaUtil->chasis;
								if (isset($vehiculo->cargaUtil->marcacodigo)) {
									$data[0]["Unidad"][0]['ID_Marca'] = $vehiculo->cargaUtil->marcacodigo;
								}
								if (isset($vehiculo->cargaUtil->colorcodigo)) {
									$data[0]["Unidad"][0]['ID_Color'] = $vehiculo->cargaUtil->colorcodigo;
								}
								$data[0]["Unidad"][0]['Anio'] = $vehiculo->cargaUtil->axo;
								$data[0]["Unidad"][0]['ID_Placa'] = $vehiculo->cargaUtil->placa;
								$data[0]["Unidad"][0]['ID_Placa_Anterior'] = $vehiculo->cargaUtil->placaAnterior;
								$data[0]["Unidad"][0]['Combustible'] = $vehiculo->cargaUtil->combustible;
								$data[0]["Unidad"][0]['Modelo'] = $vehiculo->cargaUtil->modelo;
								$data[0]["Unidad"][0]['Tipo'] = $vehiculo->cargaUtil->tipo;
								$data[0]["Unidad"][0]['RTN_Propietario'] = $vehiculo->cargaUtil->propietario->identificacion;
								$data[0]["Unidad"][0]['Nombre_Revicion'] = strtoupper($vehiculo->cargaUtil->propietario->nombre);
								$data[0]["Unidad"][0]['Estado_Vehiculo'] = strtoupper($vehiculo->cargaUtil->estadoVehiculo);
								if (strtoupper($vehiculo->cargaUtil->estadoVehiculo) == 'NO BLOQUEADO') {
									$data[0]["Unidad"][0]['Bloqueado'] = false;
								} else {
									$data[0]["Unidad"][0]['Bloqueado'] = true;
								}
							}
						} else {
							//$data[0]["Unidad_IP"] = $vehiculo;
							$data[0]["Tipo_Concesion"] = 'CERTIFICADO DE OPERACIÓN:';
							$data[0]["Tramites"] = $this->getTipoTramiteyClaseTramite(['PE', 'CO', 'CL', 'CM', 'CC', 'CS', 'PE', 'CU'], $data[0]["Categoria"]);
							//$data[0]["Link1"] = "https://satt2.transporte.gob.hn:172/api_rep.php?action=get-PDFPermisoExp-Pas&Permiso=".$data[0]["PerExpEncriptado"];
							$data[0]["Link"] = $_SESSION["appcfg_Dominio_Raiz"] . ":172/api_rep.php?action=get-PDFCertificado&Certificado=" . $data[0]["CertificadoEncriptado"];
							$data[0]["Vista"] = file_get_contents("vistas/certificado_pasajero.html");
							$data[0]["Unidad"] = $this->getUnidad("[IHTT_SGCERP].[DBO].[TB_Vehiculo_Transporte_Pasajero] v,IHTT_SGCERP.[DBO].[TB_Vehiculo_Transporte_Pasajero_x_Placa] p,IHTT_SGCERP.[DBO].[TB_Tipo_Vehiculo_Transporte_Pasajero] t where v.ID_Vehiculo_Transporte = p.ID_Vehiculo_Transporte and v.ID_Tipo_Vehiculo_Transporte_Pas = t.ID_Tipo_Vehiculo_Transporte_Pas and p.Estado = 'ACTIVA' and ", " v.ID_Vehiculo_Transporte ", $data[0]["ID_Vehiculo"], " DESC_Tipo_Vehiculo_Transporte_Pas as DESC_Tipo_Vehiculo,* ");
							if ($data && $data[0] && $data[0]["Unidad"] && $data[0]["Unidad"][0]['ID_Placa']) {
								$vehiculo = $this->getDatosUnidadDesdeIP($data[0]["Unidad"][0]['ID_Placa']);
								$data[0]["Unidad"][0]['estaPagadoElCambiodePlaca'] = false;
								//******************************************************************************************/
								// Guardando el codigo de respuesta de la solicutud al IP
								//******************************************************************************************/
								if (!isset($vehiculo->codigo)) {
									$data[0]["Codigo_IP"] = false;
								} else {
									$data[0]["Codigo_IP"] = $vehiculo->codigo;
								}
								if (isset($vehiculo->codigo) == true && $vehiculo->codigo == 200) {
									if ($vehiculo->cargaUtil->placa != $vehiculo->cargaUtil->placaAnterior) {
										//**********************************************************************************************************************/
										// Si el vehiculo tiene cambio de placa se verifica si ese cambio de placa esta pagado
										//**********************************************************************************************************************/
										$row_rs_stmt = $this->getAvisodeCobroxPlaca($vehiculo->cargaUtil->placa);
										if (isset($row_rs_stmt['MONTO'])) {
											$data[0]["Unidad"][0]['estaPagadoElCambiodePlaca'] = true;
											$vehiculo->cargaUtil->estaPagadoElCambiodePlaca = true;
										} else {
											$data[0]["Unidad"][0]['estaPagadoElCambiodePlaca'] = false;
											$vehiculo->cargaUtil->estaPagadoElCambiodePlaca = false;
										}
									}
									//**********************************************************************************************************************/
									// Recuperando las multas del vehiculo
									//**********************************************************************************************************************/
									$data[0]["Unidad"][0]['Multas'] = $this->getDatosMulta($vehiculo->cargaUtil->placa, $vehiculo->cargaUtil->placaAnterior);
									$data[0]["Unidad"][0]['Preforma'] = $this->validarEnPreforma($vehiculo->cargaUtil->placa, $vehiculo->cargaUtil->placaAnterior, $_POST["Concesion"]);
									$data[0]["Unidad"][0]['VIN'] = $vehiculo->cargaUtil->vin;
									$data[0]["Unidad"][0]['Motor'] = $vehiculo->cargaUtil->motor;
									$data[0]["Unidad"][0]['Chasis'] = $vehiculo->cargaUtil->chasis;
									if (isset($vehiculo->cargaUtil->marcacodigo)) {
										$data[0]["Unidad"][0]['ID_Marca'] = $vehiculo->cargaUtil->marcacodigo;
									}
									if (isset($vehiculo->cargaUtil->colorcodigo)) {
										$data[0]["Unidad"][0]['ID_Color'] = $vehiculo->cargaUtil->colorcodigo;
									}
									$data[0]["Unidad"][0]['Anio'] = $vehiculo->cargaUtil->axo;
									$data[0]["Unidad"][0]['ID_Placa'] = $vehiculo->cargaUtil->placa;
									$data[0]["Unidad"][0]['ID_Placa_Anterior'] = $vehiculo->cargaUtil->placaAnterior;
									$data[0]["Unidad"][0]['Combustible'] = $vehiculo->cargaUtil->combustible;
									$data[0]["Unidad"][0]['Modelo'] = $vehiculo->cargaUtil->modelo;
									$data[0]["Unidad"][0]['Tipo'] = $vehiculo->cargaUtil->tipo;
									$data[0]["Unidad"][0]['RTN_Propietario'] = $vehiculo->cargaUtil->propietario->identificacion;
									$data[0]["Unidad"][0]['Nombre_Revicion'] = strtoupper($vehiculo->cargaUtil->propietario->nombre);
									$data[0]["Unidad"][0]['Estado_Vehiculo'] = strtoupper($vehiculo->cargaUtil->estadoVehiculo);
									if (strtoupper($vehiculo->cargaUtil->estadoVehiculo) == 'NO BLOQUEADO') {
										$data[0]["Unidad"][0]['Bloqueado'] = false;
									} else {
										$data[0]["Unidad"][0]['Bloqueado'] = true;
									}
								}
								//$data[0]["Unidad_IP"] = $vehiculo;
							}
						}
					}
				}
			}
			$datos[1] = $data;
		}
		echo json_encode($datos);
	}

	//*************************************************************************************/
	//* FUNCION PARA RECUPERAR EL SOLICITANTE
	//*************************************************************************************/
	protected function getConcesionPreforma()
	{

		$query = "select * from IHTT_SGCERP.dbo.v_Listado_General WHERE N_Certificado = :N_Certificado";
		$p = array(":N_Certificado" => $_POST["idConcesion"]);
		$data = $this->select($query, $p);
		$datos[0] = count($data);

		if ($datos[0] > 0) {
			$data[0]["Marcas"] = $this->getMarca();
			$data[0]["Anios"] = $this->getAnios();
			$data[0]["Colores"] = $this->getColor();
			$data[0]["Tipo_Categoria_Especilizada"] = '';
			$data[0]["Desc_Categoria_Especilizada"] = '';
			$data[0]["ID_Area_Operacion"] = '';
			$data[0]["Desc_Area_Operacion"] = '';
			$record["Fecha_Expiracion"] = $data[0]["Fecha Vencimiento Certificado"];
			$record["Fecha_Expiracion_Explotacion"] = $data[0]["Fecha Vencimiento Permiso"];
			$data[0]["Vencimientos"] = $this->procesarFechaDeVencimiento($record, $data[0]["ID_Clase_Servico"])[1];
			$data[0]["Unidad"][0]['ID_Marca'] = '';
			$data[0]["Unidad"][0]['ID_Color'] = '';
			if ($data[0]["Clase Servicio"] == 'STEC') {
				$data[0]["Link"] = $_SESSION["appcfg_Dominio_Raiz"] . ":172/api_rep.php?action=get-PDFPermisoEsp-Carga&PermisoEspecial=" . $data[0]["CertificadoEncriptado"];
				$data[0]["Vista"] = file_get_contents("vistas/pes_carga.html");
				$data[0]["Unidad"] = $this->getUnidadPreforma($_POST["RAM"], $_POST["idConcesion"]);
				//**********************************************************************************************************************/
				//* Recuperando Bandera Sobre Si Pago el Cambio de Placa o No
				//**********************************************************************************************************************/
				if (
					isset($data[0]) &&
					isset($data[0]["Unidad"]) &&
					isset($data[0]["Unidad"][0]) &&
					Trim($data[0]["Unidad"][0]['ID_Placa']) != Trim($data[0]["Unidad"][0]['ID_Placa_Antes_Replaqueo'])
				) {
					$row_rs_stmt = $this->getAvisodeCobroxPlaca($data[0]["Unidad"][0]['ID_Placa']);
					if (isset($row_rs_stmt['MONTO'])) {
						$data[0]["Unidad"][0]['estaPagadoElCambiodePlaca'] = true;
					} else {
						$data[0]["Unidad"][0]['estaPagadoElCambiodePlaca'] = false;
					}
				} else {
					$data[0]["Unidad"][0]['estaPagadoElCambiodePlaca'] = false;
				}
				//**********************************************************************************************************************/
				//* Recuperando las multas del vehiculo
				//**********************************************************************************************************************/
				if (isset($data[0]["Unidad"]) and isset($data[0]["Unidad"][0]) and isset($data[0]["Unidad"][0]['ID_Placa'])) {
					$data[0]["Unidad"][0]['Multas'] = $this->getDatosMulta($data[0]["Unidad"][0]['ID_Placa'], $data[0]["Unidad"][0]['ID_Placa_Antes_Replaqueo']);
				}
				if (isset($data[0]["Unidad"]) and isset($data[0]["Unidad"][1]) and isset($data[0]["Unidad"][1]['ID_Placa'])) {
					$data[0]["Unidad"][1]['Multas1'] = $this->getDatosMulta($data[0]["Unidad"][1]['ID_Placa'], $data[0]["Unidad"][1]['ID_Placa_Antes_Replaqueo']);
				}
				$data[0]["Tipo_Concesion"] = 'PERMISO ESPECIAL:';
				$data[0]["Tramites"] = $this->getTipoTramiteyClaseTramitePreforma(['PS', 'CU', 'CL', 'CM', 'CC', 'CS', 'X'], $data[0]["Categoria"], $_POST["RAM"], $_POST["idConcesion"]);
				$tipo = $this->getCategoriaEspecilizadaCarga();
				if (count($tipo) > 0) {
					$data[0]["Tipo_Categoria_Especilizada"] = $tipo[0]['value'];
					$data[0]["Desc_Categoria_Especilizada"] = $tipo[0]['text'];
				}
			} else {
				if ($data[0]["Clase Servicio"] == 'STEP') {
					$data[0]["Link"] = $_SESSION["appcfg_Dominio_Raiz"] . ":172/api_rep.php?action=get-PDFPermisoEsp-Pas&PermisoEspecial=" . $data[0]["CertificadoEncriptado"];
					$data[0]["Vista"] = file_get_contents("vistas/pes_pasajero.html");
					$data[0]["Unidad"] = $this->getUnidadPreforma($_POST["RAM"], $_POST["idConcesion"]);
					//**********************************************************************************************************************/
					//* Recuperando Bandera Sobre Si Pago el Cambio de Placa o No
					//**********************************************************************************************************************/
					if (
						isset($data[0]) &&
						isset($data[0]["Unidad"]) &&
						isset($data[0]["Unidad"][0]) &&
						Trim($data[0]["Unidad"][0]['ID_Placa']) != Trim($data[0]["Unidad"][0]['ID_Placa_Antes_Replaqueo'])
					) {
						$row_rs_stmt = $this->getAvisodeCobroxPlaca($data[0]["Unidad"][0]['ID_Placa']);
						if (isset($row_rs_stmt['MONTO'])) {
							$data[0]["Unidad"][0]['estaPagadoElCambiodePlaca'] = true;
						} else {
							$data[0]["Unidad"][0]['estaPagadoElCambiodePlaca'] = false;
						}
					} else {
						$data[0]["Unidad"][0]['estaPagadoElCambiodePlaca'] = false;
					}
					//**********************************************************************************************************************/
					//* Recuperando las multas del vehiculo
					//**********************************************************************************************************************/
					if (isset($data[0]["Unidad"]) and isset($data[0]["Unidad"][0]) and isset($data[0]["Unidad"][0]['ID_Placa'])) {
						$data[0]["Unidad"][0]['Multas'] = $this->getDatosMulta($data[0]["Unidad"][0]['ID_Placa'], $data[0]["Unidad"][0]['ID_Placa_Antes_Replaqueo']);
					}
					if (isset($data[0]["Unidad"]) and isset($data[0]["Unidad"][1]) and isset($data[0]["Unidad"][1]['ID_Placa'])) {
						$data[0]["Unidad"][1]['Multas1'] = $this->getDatosMulta($data[0]["Unidad"][1]['ID_Placa'], $data[0]["Unidad"][1]['ID_Placa_Antes_Replaqueo']);
					}
					$data[0]["Tipo_Concesion"] = 'PERMISO ESPECIAL:';
					$data[0]["Tramites"] = $this->getTipoTramiteyClaseTramitePreforma(['PS', 'CU', 'CL', 'CM', 'CC', 'CS', 'X'], $data[0]["Categoria"], $_POST["RAM"], $_POST["idConcesion"]);
					$area = $this->getAreaOperacion();
					if (count($area) > 0) {
						$data[0]["Tipo_Categoria_Especilizada"] = $area[0]['value'];
						$data[0]["Desc_Categoria_Especilizada"] = $area[0]['text'];
					}
				} else {
					if ($data[0]["Clase Servicio"] == 'STPC') {
						$data[0]["Link"] = $_SESSION["appcfg_Dominio_Raiz"] . ":172/api_rep.php?action=get-PDFCertificado-Carga&Certificado=" . $data[0]["CertificadoEncriptado"];
						$data[0]["Vista"] = file_get_contents("vistas/certificado_carga.html");
						$data[0]["Unidad"] = $this->getUnidadPreforma($_POST["RAM"], $_POST["idConcesion"]);
						//**********************************************************************************************************************/
						//* Recuperando Bandera Sobre Si Pago el Cambio de Placa o No
						//**********************************************************************************************************************/
						if (
							isset($data[0]) &&
							isset($data[0]["Unidad"]) &&
							isset($data[0]["Unidad"][0]) &&
							Trim($data[0]["Unidad"][0]['ID_Placa']) != Trim($data[0]["Unidad"][0]['ID_Placa_Antes_Replaqueo'])
						) {
							$row_rs_stmt = $this->getAvisodeCobroxPlaca($data[0]["Unidad"][0]['ID_Placa']);
							if (isset($row_rs_stmt['MONTO'])) {
								$data[0]["Unidad"][0]['estaPagadoElCambiodePlaca'] = true;
							} else {
								$data[0]["Unidad"][0]['estaPagadoElCambiodePlaca'] = false;
							}
						} else {
							$data[0]["Unidad"][0]['estaPagadoElCambiodePlaca'] = false;
						}
						//**********************************************************************************************************************/
						//* Recuperando las multas del vehiculo
						//**********************************************************************************************************************/
						if (isset($data[0]["Unidad"]) and isset($data[0]["Unidad"][0]) and isset($data[0]["Unidad"][0]['ID_Placa'])) {
							$data[0]["Unidad"][0]['Multas'] = $this->getDatosMulta($data[0]["Unidad"][0]['ID_Placa'], $data[0]["Unidad"][0]['ID_Placa_Antes_Replaqueo']);
						}
						if (isset($data[0]["Unidad"]) and isset($data[0]["Unidad"][1]) and isset($data[0]["Unidad"][1]['ID_Placa'])) {
							$data[0]["Unidad"][1]['Multas1'] = $this->getDatosMulta($data[0]["Unidad"][1]['ID_Placa'], $data[0]["Unidad"][1]['ID_Placa_Antes_Replaqueo']);
						}
						$data[0]["Tipo_Concesion"] = 'CERTIFICADO DE OPERACIÓN:';
						$data[0]["Tramites"] = $this->getTipoTramiteyClaseTramitePreforma(['PE', 'CO', 'CL', 'CM', 'CC', 'CS', 'CU'], $data[0]["Categoria"], $_POST["RAM"], $_POST["idConcesion"]);
						$tipo = $this->getCategoriaEspecilizadaCarga($data[0]["Id_Tipo_Categoria"]);
						if (count($tipo) > 0) {
							$data[0]["Tipo_Categoria_Especilizada"] = $tipo[0]['value'];
							$data[0]["Desc_Categoria_Especilizada"] = $tipo[0]['text'];
						}
					} else {
						$data[0]["Link"] = $_SESSION["appcfg_Dominio_Raiz"] . ":172/api_rep.php?action=get-PDFCertificado&Certificado=" . $data[0]["CertificadoEncriptado"];
						$data[0]["Vista"] = file_get_contents("vistas/certificado_pasajero.html");
						$data[0]["Unidad"] = $this->getUnidadPreforma($_POST["RAM"], $_POST["idConcesion"]);
						//**********************************************************************************************************************/
						//* Recuperando Bandera Sobre Si Pago el Cambio de Placa o No
						//**********************************************************************************************************************/
						if (
							isset($data[0]) &&
							isset($data[0]["Unidad"]) &&
							isset($data[0]["Unidad"][0]) &&
							Trim($data[0]["Unidad"][0]['ID_Placa']) != Trim($data[0]["Unidad"][0]['ID_Placa_Antes_Replaqueo'])
						) {
							$row_rs_stmt = $this->getAvisodeCobroxPlaca($data[0]["Unidad"][0]['ID_Placa']);
							if (isset($row_rs_stmt['MONTO'])) {
								$data[0]["Unidad"][0]['estaPagadoElCambiodePlaca'] = true;
							} else {
								$data[0]["Unidad"][0]['estaPagadoElCambiodePlaca'] = false;
							}
						} else {
							$data[0]["Unidad"][0]['estaPagadoElCambiodePlaca'] = false;
						}
						//**********************************************************************************************************************/
						//* Recuperando las multas del vehiculo
						//**********************************************************************************************************************/
						if (isset($data[0]["Unidad"]) and isset($data[0]["Unidad"][0]) and isset($data[0]["Unidad"][0]['ID_Placa'])) {
							$data[0]["Unidad"][0]['Multas'] = $this->getDatosMulta($data[0]["Unidad"][0]['ID_Placa'], $data[0]["Unidad"][0]['ID_Placa_Antes_Replaqueo']);
						}
						if (isset($data[0]["Unidad"]) and isset($data[0]["Unidad"][1]) and isset($data[0]["Unidad"][1]['ID_Placa'])) {
							$data[0]["Unidad"][1]['Multas1'] = $this->getDatosMulta($data[0]["Unidad"][1]['ID_Placa'], $data[0]["Unidad"][1]['ID_Placa_Antes_Replaqueo']);
						}
						$data[0]["Tipo_Concesion"] = 'CERTIFICADO DE OPERACIÓN:';
						$data[0]["Tramites"] = $this->getTipoTramiteyClaseTramitePreforma(['PE', 'CO', 'CL', 'CM', 'CC', 'CS', 'CU'], $data[0]["Categoria"], $_POST["RAM"], $_POST["idConcesion"]);
					}
				}
			}
			$datos[1] = $data;
		}
		echo json_encode($datos);
	}

	protected function getUsuarioAsigna()
	{
		//**************************************************************************//
		//RTBM rbthaofic@gmail.com 2022/08/15                                       //
		//**************************************************************************//
		// Aqui se usa el codigo de renovación automatica para definir porque       //
		// proceso se debe ir cuando es renovación automatica                      //
		//**************************************************************************//
		// Se usa el proceso 4 para renovaciones automaticas
		//**************************************************************************//
		$p = array();
		if ($_SESSION["Es_Renovacion_Automatica"] == false) {
			return $this->select("SELECT TOP 1 Codigo_Usuario, Nombre_Usuario, COUNT ( Nombre_Usuario ) AS Preformas_Asignadas FROM ( SELECT A.Codigo_Usuario AS Codigo_Usuario, A.Usuario_Nombre AS Nombre_Usuario FROM [IHTT_DB].[dbo].[TB_GEA_X_Abogados_Asignados] A WHERE Preforma11 = 1 UNION ALL SELECT A.Codigo_Usuario_Acepta AS Codigo_Usuario, A.Usuario_Acepta AS Nombre_Usuario FROM [IHTT_PREFORMA].[dbo].[TB_Solicitante] A WHERE CONVERT ( DATE, Sistema_Fecha ) = CONVERT ( DATE, GETDATE()) AND Codigo_Usuario_Acepta IN ( SELECT Codigo_Usuario FROM [IHTT_DB].[dbo].[TB_GEA_X_Abogados_Asignados] WHERE Preforma11 = 1)) AS b GROUP BY Nombre_Usuario, Codigo_Usuario ORDER BY Preformas_Asignadas ASC", $p);
		} else {
			//***************************************************************************//
			//RTBM rbthaofic@gmail.com 2022/08/15                                        //
			//***************************************************************************//
			// Este el el query que se usa cuando es renovación automatica para obtener  //
			// el usuario al que se debe asignar el tramite                              //
			//***************************************************************************//
			// Se usa el proceso 4 para renovaciones automaticas
			//***************************************************************************//
			return $this->select("SELECT TOP 1 Codigo_Usuario, Nombre_Usuario, COUNT ( Nombre_Usuario ) AS Preformas_Asignadas 
			FROM ( SELECT A.Codigo_Usuario AS Codigo_Usuario, A.Usuario_Nombre AS Nombre_Usuario FROM [IHTT_DB].[dbo].[TB_GEA_X_Abogados_Asignados] A
			WHERE Proceso = 4  and Preforma11 = 0 UNION ALL SELECT A.Codigo_Usuario_Acepta AS Codigo_Usuario, A.Usuario_Acepta AS Nombre_Usuario 
			FROM [IHTT_PREFORMA].[dbo].[TB_Solicitante] A WHERE CONVERT ( DATE, Sistema_Fecha ) = CONVERT ( DATE, GETDATE()) AND 
			Codigo_Usuario_Acepta IN ( SELECT Codigo_Usuario FROM [IHTT_DB].[dbo].[TB_GEA_X_Abogados_Asignados] WHERE  Proceso = 4 and Preforma11 = 0)) AS b 
			GROUP BY Nombre_Usuario, Codigo_Usuario ORDER BY Preformas_Asignadas ASC", $p);
		}
	}

	//*******************************************************************************************************************/
	//* Funcion para salvar la archivo escaneado que se adjunta al expediente cuando se ingresa en SPS
	//*******************************************************************************************************************/
	protected function saveEscaneo($RAM)
	{
		$mover = true;
		try {
			$directory = "Documentos/" . $RAM;
			if (!is_dir($directory)) {
				if (!mkdir($directory, 0777, true)) {
					$response['msgLog'] = "Fallo la creación del directorio: $directory";
					$response['msg'] = 'Algo inesperado sucedio creando el directorio';
					$response['error'] = true;
					$txt = date('Y m d h:i:s') . 'Api_Ram.php	crearCarpeta:; ' . $_SESSION['usuario'] . '; - Error ; ' . "Fallo la creación del directorio: $dirE";
					logErr($txt, '../logs/logs.txt');
					$mover = false;
					echo json_encode(array("error" => 2000, "errorhead" => "CREACIÓN DE DIRECTORIO", "errormsg" => 'NO SE PUEDE CREAR EL DIRECTORIO: ' . $directory));
				} else {
					$mover = true;
				}
			}
			if ($mover == true) {
				try {
					if (isset($_FILES['Archivo'])) {
						$directory = "Documentos/" . $RAM . "/";
						$filePath = $directory . $RAM . ".pdf";
						// Check if the directory exists
						if (!is_dir($directory)) {
							echo json_encode(array("error" => 2100, "errorhead" => "DIRECTORIO", "errormsg" => 'NO EXISTE EL DIRECTORIO: ' . $directory));
						} else {
							// Attempt to delete the existing file
							if (file_exists($filePath) && !@unlink($filePath)) {
								echo json_encode(array("error" => 2200, "errorhead" => "ARCHIVO", "errormsg" => 'NO SE PUEDE BORRAR EL ARCHIVO: ' . $filePath));
							} else {
								// Attempt to move the uploaded file
								if (!move_uploaded_file($_FILES['Archivo']['tmp_name'], $filePath)) {
									echo json_encode(array("error" => 2300, "errorhead" => "MOVIMIENTO ARCHIVO", "errormsg" => 'NO SE PUEDE MOVER EL ARCHIVO: ' . $filePath));
								} else {
									echo json_encode(array("msg" => 'SE MOVIO SATISFACTORIAMENTE EL ARCHIVO: ' . $filePath));
								}
							}
						}
					} else {
						echo json_encode(array("error" => 2600, "errorhead" => "ARCHIVO A CARGAR", "errormsg" => 'NO EXISTE NINGUN ARCHIVO A CARGAR'));
					}
				} catch (Exception $e) {
					// Handle exceptions and display an error message
					echo json_encode(array("error" => 2500, "errorhead" => "CATCH MOVIMIENTO ARCHIVO CREACION DE DIRECTORIO", "errormsg" => 'ERROR DESCONOCIDO AL TRATAR DE MOVER ARCHIVO: ' . $filePath));
				}
			}
		} catch (Exception $e) {
			// Handle the exception
			$response['msgLog'] = 'Caught Exception: ' .  $e->getMessage() . "\n";
			$response['msg'] = 'Algo inesperado sucedio creando el directorio';
			$response['error'] = false;
			$txt = date('Y m d h:i:s') . 'Api_Ram.php	crearCarpeta:; ' . $_SESSION['usuario'] . '; - Error ; ' . $e->getMessage() . "\n";
			logErr($txt, '../logs/logs.txt');
			echo json_encode(array("error" => 2400, "errorhead" => "CATCH MOVIMIENTO ARCHIVO CREACION DE DIRECTORIO", "errormsg" => 'ERROR DESCONOCIDO AL TRATAR DE CREAR DIRECTORIO: ' . $directory));
		}
	}

	//**************************************************************************************/
	//*  Valida que la placa no este asignada a una concesion que este con tramites        */
	//*  pendientes en preforma al igual valida que la concesion no este con                                              */
	//**************************************************************************************/
	protected function validarEnPreforma($ID_Placa, $ID_Placa_Antes_Replaqueo, $Concesion, $RAM = ''): mixed
	{
		//**************************************************************************************/
		//*  CAMBIOS HECHOS RBTHAOFIC@GMAIL.COM 2022/11/17                                    */
		//*  vALIDAR QUE FECHA ACTUAL SEA MENOR O IGUAL A LA FECHA DE VENCIMIENTO             */
		//*  CAMBIOS HECHOS RBTHAOFIC@GMAIL.COM 2023/12/14                                    */
		//*  VALIDAR QUE [N_Certificado] != :N_Certificado y                                  */
		//*  FECHA ACTUAL SEA MAYOR O IGUAL A LA FECHA DE VENCIMIENTO                         */
		//************************************************************************************/
		$query = "SELECT DISTINCT S.ID_Formulario_Solicitud,L.N_Certificado,L.Permiso_Explotacion,l.N_Permiso_Especial,
				S.Sistema_Fecha,A.Nombre_Apoderado_Legal,ID_Colegiacion 
				FROM [IHTT_PREFORMA].[dbo].[TB_SOLICITANTE] S, [IHTT_PREFORMA].[dbo].[TB_Apoderado_Legal] A,[IHTT_PREFORMA].[dbo].[TB_SOLICITUD] L, [IHTT_PREFORMA].[dbo].[TB_Vehiculo] V
				WHERE S.ID_Formulario_Solicitud != :RAM AND
				S.Estado_Formulario in ('IDE-1','IDE-7') AND
				S.ID_Formulario_Solicitud = A.ID_Formulario_Solicitud AND 
				S.ID_Formulario_Solicitud = L.ID_Formulario_Solicitud AND 
				L.ID_Formulario_Solicitud = V.ID_Formulario_Solicitud AND 
				V.Estado IN ('NORMAL','ENTRA') AND
				((L.N_Certificado = :N_Certificado and L.N_Certificado != '')  OR 
				(L.N_Permiso_Especial = :N_Permiso_Especial and L.N_Permiso_Especial != '')  OR 
				V.ID_Placa = :ID_Placa or  
				v.ID_Placa_Antes_Replaqueo = :ID_Placa_Antes_Replaqueo);";
		$parametros = array(":N_Certificado" => $Concesion, ":N_Permiso_Especial" => $Concesion, ":ID_Placa" => $ID_Placa, ":ID_Placa_Antes_Replaqueo" => $ID_Placa_Antes_Replaqueo, ":RAM" => $RAM);
		$row = $this->select($query, $parametros);
		if (count($row) > 0) {
			$titulos = [
				0 => 'RAM',
				1  => 'CERTIFICADO OPERAC',
				2 => 'PER EXP',
				3 => 'PER ESPECIAL',
				4 => 'FECHA',
				5 => 'APODERADO',
				6 => 'CAH No.',
				'ID Formulario Solicitud' => 'ID Formulario Solicitud',
				'Certificado Operación'  => 'Certificado Operación',
				'Permiso de Explotacion' => 'Permiso de Explotacion',
				'Permiso Especial' => 'Permiso Especial',
				'Sistema Fecha' => 'Sistema Fecha',
				'Nombre Apoderado Legal' => 'Nombre Apoderado Legal',
				'CAH No. Carnet' => 'CAH No. Carnet'
			];
			$row[count($row) + 1] = $titulos;
		}
		return $row;
	}

	//**************************************************************************************/
	//*  Valida que la placa no este asignada a una concesion vigente diferente de         */
	//*  de la que estamos tratando de salvar                                              */
	//**************************************************************************************/
	protected function validarPlaca($placa, $placa_anterior, $concesion): mixed
	{
		/**************************************************************************************/
		/*  CAMBIOS HECHOS RBTHAOFIC@GMAIL.COM 2022/11/17                                    */
		/*  vALIDAR QUE FECHA ACTUAL SEA MENOR O IGUAL A LA FECHA DE VENCIMIENTO             */
		/*  CAMBIOS HECHOS RBTHAOFIC@GMAIL.COM 2023/12/14                                    */
		/*  VALIDAR QUE [N_Certificado] != :N_Certificado y                                  */
		/*  FECHA ACTUAL SEA MAYOR O IGUAL A LA FECHA DE VENCIMIENTO                         */
		/************************************************************************************/
		$query = "SELECT * FROM [IHTT_SGCERP].[dbo].[v_Validacion_Placas] 
		WHERE [N_Certificado] != :Concesion and  
		Fecha_Expiracion >= CONVERT(CHAR(8), GETDATE(), 112)  AND ID_Estado IN ('ES-02','ES-04') AND (ID_Placa = :Placa or ID_Placa = :Placa_Anterior)";
		$parametros = array(":Concesion" => $concesion, ":Placa" => $placa, ":Placa_Anterior" => $placa_anterior);
		return $this->select($query, $parametros);
	}

	//*******************************************************************************************************************/
	//* Obtener la ciudad 
	//*******************************************************************************************************************/
	protected function getCiudad($ID_Empleado)
	{
		//***********************************************************************************************************/
		//* rbthaofic@gmail.com 2023/03/04 Pendiente de finalización (recuperar el area del empleados)
		//* Inicio: Agregar usuario que realiza la acción y la ciudad donde se ubica el usuario
		//***********************************************************************************************************/
		$query = "SELECT Ciu.Codigo_Ciudad,Ciu.Acronimo,ciu.Codigo_Ciudad
		FROM [IHTT_RRHH].[dbo].[TB_Empleados] Emp, [IHTT_RRHH].[dbo].[TB_Ciudades] ciu
		where emp.Codigo_Ciudad = ciu.Codigo_Ciudad and ID_Empleado = :ID_Empleado";
		$p = array(":ID_Empleado" => $ID_Empleado);
		return $this->select($query, $p);
	}

	//*******************************************************************************************************************/
	//* Actualizando el registro de secuencia
	//*******************************************************************************************************************/
	protected function updateEstadoPreforma()
	{
		$_POST["echo"] = json_decode($_POST["echo"]);
		$_POST['idEstado'] = json_decode($_POST['idEstado']);
		$_POST["RAM"] = json_decode($_POST["RAM"]);
		if (isset($_POST["echo"])) {
			$this->db->beginTransaction();
		}
		$query = "UPDATE [IHTT_PREFORMA].[DBO].[TB_SOLICITANTE] SET Estado_Formulario = :Estado_Formulario WHERE ID_FORMULARIO_SOLICITUD = :ID_FORMULARIO_SOLICITUD";
		$p = array(":Estado_Formulario" => $_POST['idEstado'], ":ID_FORMULARIO_SOLICITUD" => $_POST["RAM"]);
		$estadoOk = $this->update($query, $p);
		if ($estadoOk == true) {
			if ($_POST['idEstado'] == 'IDE-3') {
				$evento = 'CANCELADO';
				$etapa = 4;
			} else {
				if ($_POST['idEstado'] == 'IDE-4') {
					$evento = 'INADMITIDO';
					$etapa = 5;
				} else {
					if ($_POST['idEstado'] == 'IDE-1') {
						$evento = 'TRABAJO';
						$etapa = 2;
					}
				}
			}
			$saveBitacoraOk = $this->saveBitacora($_POST["RAM"], $evento, $etapa);
			if ($saveBitacoraOk != false) {
				if (!isset($_POST["echo"])) {
					return $saveBitacoraOk;
				} else {
					$this->db->commit();
					echo json_encode($saveBitacoraOk);
				}
			} else {
				echo json_encode(array("error" => 9002, "errorhead" => 'ADVERTENCIA', "errormsg" => 'ESTAMOS PRESENTANDO INCONVENIENTES TEMPORALES AL MOMENTO DE SALVAR LA BITACORA DE LA PREFORMA'));
			}
		} else {
			echo json_encode(array("error" => 9001, "errorhead" => 'ADVERTENCIA', "errormsg" => 'ESTAMOS PRESENTANDO INCONVENIENTES TEMPORALES AL MOMENTO DE CAMBIAR EL ESTADO DE LA PREFORMA'));
		}
	}

	//*******************************************************************************************************************/
	//* Actualizando el registro de secuencia
	//*******************************************************************************************************************/
	protected function updateSiguienteNumeroRAM($query, $numero_actual)
	{
		$p = array(":numero_actual" => $numero_actual, ":usuario_modificacion" => $_SESSION["user_name"], ":ip_modificacion" => $this->getIp(), ":host_modificacion" => $this->getHost());
		return $this->update($query, $p);
	}

	//*******************************************************************************************************************/
	//* Obteniendo el siguiente numero de secuencia
	//*******************************************************************************************************************/
	protected function getSiguienteNumeroRAM($record, $recordRango)
	{
		if (($record['usaRangos'] == 0 and $record['numero_final'] > $record['numero_actual']) || ($record['usaRangos'] == 1 and $recordRango['numero_final'] > $recordRango['numero_actual'])) {
			$response['error'] = false;
			//*******************************************************************************************************************/
			// Calculando el siguiente numero en la secuencia y aramando el query que toca para actualizar la sencuencia
			//*******************************************************************************************************************/
			if ($record['usaRangos'] == 0) {
				$response['numero_actual'] = $record['numero_actual'] + 1;
				$query = "update [IHTT_RENOVACIONES_AUTOMATICAS].[DBO].[TB_Secuencias] set numero_actual=:numero_actual,usuario_modificacion=:usuario_modificacion,ip_modificacion=:ip_modificacion,host_modificacion=:host_modificacion  WHERE ID = " . htmlentities($record['id']);
			} else {
				$response['numero_actual'] = $recordRango['numero_actual'] + 1;
				$query = "update [IHTT_RENOVACIONES_AUTOMATICAS].[DBO].[TB_Secuencias_Rango] set numero_actual=:numero_actual,usuario_modificacion=:usuario_modificacion,ip_modificacion=:ip_modificacion,host_modificacion=:host_modificacion WHERE ID =  " . htmlentities($recordRango['id']);
			}
			//*******************************************************************************************************************/
			//* Armando el prefijo con el año , mes y/o dia si es el caso
			//*******************************************************************************************************************/			
			$prefijo = str_replace("date('Y')", date('Y'), trim($record['prefijo']));
			$prefijo = str_replace("date('m')", date('m'), trim($prefijo));
			$prefijo = str_replace("date('d')", date('d'), trim($prefijo));
			//*******************************************************************************************************************/
			//* Armando el sufijo con el año , mes y/o dia si es el caso
			//*******************************************************************************************************************/			
			$sufijo = str_replace("date('Y')", date('Y'), trim($record['sufijo']));
			$sufijo = str_replace("date('m')", date('m'), trim($sufijo));
			$sufijo = str_replace("date('d')", date('d'), trim($sufijo));
			//*******************************************************************************************************************/			
			//* Llamando funcion de Update el siguiente número en la secuencia
			//*******************************************************************************************************************/			
			$responseUpdateSiguienteNumeroRAM = $this->updateSiguienteNumeroRAM($query, $response['numero_actual']);
			//*******************************************************************************************************************/			
			//* Sino se presento ningun error al momento de actualizar el registro de secuencias
			//*******************************************************************************************************************/			
			if (trim($responseUpdateSiguienteNumeroRAM) == true) {
				//*******************************************************************************************************************/			
				//* Armando el siguiente numero de RAM
				//*******************************************************************************************************************/			
				$response['nuevo_numero'] = trim($prefijo) . (substr((str_repeat($record['caracter_de_relleno'], $record['tamaño_numero'])) . $response['numero_actual'], (-1 * $record['tamaño_numero']))) . trim($sufijo);
				return $response;
			} else {
				return $responseUpdateSiguienteNumeroRAM;
			}
		} else {
			$response['error'] = true;
			$response['msg'] = 'YA NO HAY MÁS NÚMEROS DISPONIBLES EN LA SECUENCIA';
			return $response;
		}
	}

	protected function getSiguienteRAM($ID_Secuencia)
	{
		$query = "select * from [IHTT_RENOVACIONES_AUTOMATICAS].[DBO].[TB_Secuencias] WITH (UPDLOCK) WHERE ID = :ID_Secuencia";
		$p = array(":ID_Secuencia" => $ID_Secuencia);
		$record = $this->select($query, $p);
		if (is_array($record) == true) {
			if ($record[0]['usaRangos'] == 1) {
				$query = "select * from [IHTT_RENOVACIONES_AUTOMATICAS].[DBO].[TB_Secuencias_Rango] WITH (UPDLOCK) WHERE ID = :ID_Secuencia and fecha_final >= CAST(:fecha_final as DATE)";
				$p = array(":ID_Secuencia" => $ID_Secuencia, ":fecha_final" => DATE('Y/m/d'));
				$recordRango = $this->select($query, $p);
				if (is_array($record) == true) {
					return $this->getSiguienteNumeroRAM($record[0], $recordRango[0]);
				} else {
					$response['ok'] = false;
					$response['msg'] = 'YA NO HAY RANGO VALIDO PARA LA FECHA ACTUAL';
					return $response;
				}
			} else {
				return $this->getSiguienteNumeroRAM($record[0], $record[0]);
			}
		} else {
			$response['ok'] = false;
			$response['msg'] = 'YA NO HAY RANGO VALIDO';
			return $response;
		}
	}

	protected function saveSolicitante($Concesion, $Apoderado, $Solicitante, $row_ciudad, $RAM)
	{
		$HASH = hash('SHA512', '%^4#09+-~@%&zfg' . $RAM . date('m/d/Y h:i:s a', time()), false);
		$query = "INSERT INTO [IHTT_PREFORMA].[dbo].[TB_Solicitante] 
		(
		Es_Renovacion_Automatica,
		Originado_En_Ventanilla,
		Usuario_Creacion,
		Codigo_Ciudad,
		ID_Formulario_Solicitud,
		ID_Formulario_Solicitud_Encrypted,
		Nombre_Solicitante,
		ID_Tipo_Solicitante,
		RTN_Solicitante,
		Domicilo_Solicitante,
		Denominacion_Social,
		ID_Aldea,
		Telefono_Solicitante,
		Email_Solicitante,
		Numero_Escritura,
		RTN_Notario,
		Notario_Autorizante,
		Lugar_Constitucion,
		Fecha_Constitucion,
		Estado_Formulario,
		Fecha_Cancelacion,
		Observaciones,
		Usuario_Cancelacion,
		Sistema_Fecha,
		Presentacion_Documentos,
		Etapa_Preforma,
		Usuario_Acepta,
		Fecha_Aceptacion,
		Codigo_Usuario_Acepta,
		Tipo_Solicitud,
		Entrega_Ubicacion) 
		VALUES(
		:Es_Renovacion_Automatica,
		:Originado_En_Ventanilla,
		:Usuario_Creacion,
		:Codigo_Ciudad,
		:ID_Formulario_Solicitud,
		:ID_Formulario_Solicitud_Encrypted,
		:Nombre_Solicitante,
		:ID_Tipo_Solicitante,
		:RTN_Solicitante,
		:Domicilo_Solicitante,
		:Denominacion_Social,
		:ID_Aldea,
		:Telefono_Solicitante,
		:Email_Solicitante,
		:Numero_Escritura,
		:RTN_Notario,
		:Notario_Autorizante,
		:Lugar_Constitucion,
		:Fecha_Constitucion,
		:Estado_Formulario,
		:Fecha_Cancelacion,
		:Observaciones,
		:Usuario_Cancelacion,
		SYSDATETIME(),
		:Presentacion_Documentos,
		:Etapa_Preforma,
		:Usuario_Acepta,
		SYSDATETIME(),
		:Codigo_Usuario_Acepta,
		:Tipo_Solicitud,
		:Entrega_Ubicacion)";
		$parametros = array(
			":Es_Renovacion_Automatica" => $_SESSION["Es_Renovacion_Automatica"],
			":Originado_En_Ventanilla" => $_SESSION["Originado_En_Ventanilla"],
			":Usuario_Creacion" => $_SESSION["user_name"],
			":Codigo_Ciudad" => $row_ciudad[0]['Codigo_Ciudad'],
			":ID_Formulario_Solicitud" => $RAM,
			":ID_Formulario_Solicitud_Encrypted" => $HASH,
			":Nombre_Solicitante" => strtoupper($Solicitante['Nombre']),
			":ID_Tipo_Solicitante" => $Solicitante['Tipo_Solicitante'],
			":RTN_Solicitante" => $Solicitante['RTN'],
			":Domicilo_Solicitante" => strtoupper($Solicitante['Domicilio']),
			":Denominacion_Social" => strtoupper($Solicitante['Denominacion']),
			":ID_Aldea" => $Solicitante['Aldea'],
			":Telefono_Solicitante" => $Solicitante['Telefono'],
			":Email_Solicitante" => $Solicitante['Email'],
			":Numero_Escritura" => '',
			":RTN_Notario" => '',
			":Notario_Autorizante" => '',
			":Lugar_Constitucion" => '',
			":Fecha_Constitucion" => '1900-01-01',
			":Estado_Formulario" => 'IDE-7',
			":Fecha_Cancelacion" => null,
			":Observaciones" => strtoupper(''),
			":Usuario_Cancelacion" => '',
			":Presentacion_Documentos" => $Apoderado['Tipo_Presentacion'],
			":Etapa_Preforma" => 1,
			":Usuario_Acepta" => $_SESSION["user_name"], //$row_usuario_asigna[0]["Nombre_Usuario"], 
			":Codigo_Usuario_Acepta" => $_SESSION["ID_Usuario"], //$row_usuario_asigna[0]["Codigo_Usuario"],
			":Tipo_Solicitud" => $Concesion['esCarga'] = true ? 'CARGA' : 'PASAJEROS',
			":Entrega_Ubicacion" => $Apoderado['Lugar_Entrega']
		);
		$id = $this->insert($query, $parametros);
		$isOk = ['ID_Solicitante' => $id, 'HASH' => $HASH];
		return $isOk;
	}

	protected function saveApoderado($RAM, $Apoderado)
	{
		$query = "INSERT INTO [IHTT_PREFORMA].[dbo].[TB_Apoderado_Legal]
		(ID_Formulario_Solicitud,
		Nombre_Apoderado_Legal,
		Ident_Apoderado_Legal,
		ID_Colegiacion,
		Direccion_Apoderado_Legal,
		Telefono_Apoderado_Legal,
		Email_Apoderado_Legal,
		Sistema_Fecha) 
		VALUES(
		:ID_Formulario_Solicitud,
		:Nombre_Apoderado_Legal,
		:Ident_Apoderado_Legal,
		:ID_Colegiacion,
		:Direccion_Apoderado_Legal,
		:Telefono_Apoderado_Legal,
		:Email_Apoderado_Legal,
		SYSDATETIME())";
		$parametros = array(
			":ID_Formulario_Solicitud" => $RAM,
			":Nombre_Apoderado_Legal" => strtoupper($Apoderado['Nombre']),
			":Ident_Apoderado_Legal" => $Apoderado['RTN'],
			":ID_Colegiacion" => $Apoderado['Numero_Colegiacion'],
			":Direccion_Apoderado_Legal" => strtoupper($Apoderado['Direccion']),
			":Telefono_Apoderado_Legal" => $Apoderado['Telefono'],
			":Email_Apoderado_Legal" => strtoupper($Apoderado['Email'])
		);
		return $this->insert($query, $parametros);
	}

	protected function saveUnidad($RAM, $Unidad, $Concesion, $Estado)
	{
		$query = "INSERT INTO [IHTT_PREFORMA].[dbo].[TB_Vehiculo] (
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
		VIN,
		Combustible,
		Alto,
		Ancho,
		Largo,
		Capacidad_Carga,
		Peso_Unidad,
		Permiso_Explotacion,
		Certificado_Operacion,
		Permiso_Especial,
		Sistema_Fecha,
		Estado,
		ID_Placa_Antes_Replaqueo,
		Sistema_Usuario
		)
		VALUES(
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
		:VIN,
		:Combustible,
		:Alto,
		:Ancho,
		:Largo,
		:Capacidad_Carga,
		:Peso_Unidad,
		:Permiso_Explotacion,
		:Certificado_Operacion,
		:Permiso_Especial,
		SYSDATETIME(),
		:Estado,
		:ID_Placa_Antes_Replaqueo,
		:Sistema_Usuario		
		)";
		$parametros = array(
			":ID_Formulario_Solicitud" => $RAM,
			":RTN_Propietario" => $Unidad['RTN_Propietario'],
			":Nombre_Propietario" => strtoupper($Unidad['Nombre_Propietario']),
			":ID_Placa" => strtoupper($Unidad['Placa']),
			":ID_Marca" => $Unidad['Marca'],
			":Anio" => $Unidad['Anio'],
			":Modelo" => strtoupper($Unidad['Modelo']),
			":Tipo_Vehiculo" => strtoupper($Unidad['Tipo']),
			":ID_Color" => $Unidad['Color'],
			":Motor" => strtoupper($Unidad['Motor']),
			":Chasis" => strtoupper($Unidad['Serie']),
			":VIN" => strtoupper($Unidad['VIN']),
			":Combustible" => strtoupper($Unidad['Combustible']),
			":Alto" => $Unidad['Alto'],
			":Ancho" => $Unidad['Ancho'],
			":Largo" => $Unidad['Largo'],
			":Capacidad_Carga" => $Unidad['Capacidad'],
			":Peso_Unidad" => 0,
			":Permiso_Explotacion" => strtoupper($Concesion['Permiso_Explotacion']),
			":Certificado_Operacion" => strtoupper($Concesion['Certificado']),
			":Permiso_Especial" => strtoupper($Concesion['Permiso_Especial']),
			":Estado" => $Estado,
			":ID_Placa_Antes_Replaqueo" => strtoupper($Unidad['ID_Placa_Antes_Replaqueo']),
			":Sistema_Usuario" => $_SESSION["user_name"]
		);
		return $this->insert($query, $parametros);
	}

	protected function saveTramites($RAM, $Tramites)
	{
		if (isset($_POST["echo"])) {
			$this->db->beginTransaction();
		}
		$contadorInserts = 0;
		$isOk = array();
		$isOk[0] = false;
		$contador = count($_POST["Tramites"]);
		$query = "INSERT INTO [IHTT_PREFORMA].[dbo].[TB_Solicitud](
		ID_Formulario_Solicitud,
		ID_Tramite, 
		ID_Modalidad,
		ID_TIpo_Servicio,
		N_Certificado,
		Permiso_Explotacion,
		Sistema_Fecha,
		Sistema_IP,
		ID_Tipo_Categoria,
		N_Permiso_Especial,
		Tipo_Servicio,
		Es_Renovacion_Automatica,
		Originado_En_Ventanilla,
		Sistema_Usuario) 
		VALUES(
		:ID_Formulario_Solicitud,
		:ID_Tramite, 
		:ID_Modalidad,
		:ID_TIpo_Servicio,
		:N_Certificado,
		:Permiso_Explotacion,
		SYSDATETIME(),
		:Sistema_IP,
		:ID_Tipo_Categoria,
		:N_Permiso_Especial,
		:Tipo_Servicio,
		:Es_Renovacion_Automatica,
		:Originado_En_Ventanilla,
		:Sistema_Usuario)";
		for ($i = 0; $i < $contador; $i++) {
			$parametros = array(
				":ID_Formulario_Solicitud" => $RAM,
				":ID_Tramite" => $Tramites[$i]['Codigo'],
				":ID_Modalidad" => $Tramites[$i]['ID_Modalidad'],
				":ID_TIpo_Servicio" => $Tramites[$i]['ID_Tipo_Servicio'],
				":N_Certificado" => $_POST['Concesion']['Certificado'],
				":Permiso_Explotacion" => $_POST['Concesion']['Permiso_Explotacion'],
				":Sistema_IP" => $this->getIp(),
				":ID_Tipo_Categoria" => $Tramites[$i]['ID_Categoria'],
				":N_Permiso_Especial" => $_POST['Concesion']['Permiso_Especial'],
				":Tipo_Servicio" => $Tramites[$i]['ID_Tipo_Servicio'],
				":Es_Renovacion_Automatica" => $_SESSION["Es_Renovacion_Automatica"],
				":Originado_En_Ventanilla" => $_SESSION["Originado_En_Ventanilla"],
				":Sistema_Usuario" => $_SESSION["user_name"]
			);
			$isOk[$i] = ['ID' => $this->insert($query, $parametros), 'ID_Compuesto' => $Tramites[$i]['ID_Compuesto']];

			if ($isOk[$i]['ID'] == false) {
				$this->db->rollback();
				unset($isOk);
				$isOk = array();
				$isOk[0] = false;
				break;
			} else {
				$contadorInserts++;
			}
		}
		if (!isset($_POST["echo"])) {
			if ($isOk[0] != false) {
				return $isOk;
			} else {
				echo json_encode(array("error" => 7001, "errorhead" => 'ADVERTENCIA', "errormsg" => 'ESTAMOS PRESENTANDO INCONVENIENTES TEMPORALES AL MOMENTO DE SALVAR EL TRAMITE EN PREFORMA'));
			}
		} else {
			if ($isOk[0] != false) {
				if ($contadorInserts > 0) {
					$this->db->commit();
				}
				echo json_encode($isOk);
			} else {
				echo json_encode(array("error" => 7001, "errorhead" => 'ADVERTENCIA', "errormsg" => 'ESTAMOS PRESENTANDO INCONVENIENTES TEMPORALES AL MOMENTO DE SALVAR EL TRAMITE EN PREFORMA'));
			}
		}
	}

	protected function saveBitacora($RAM, $Evento, $Etapa)
	{
		//Insert a la tabla de Bitacora_Preforma
		$query = "INSERT INTO [IHTT_PREFORMA].[dbo].[TB_Bitacora_Movimiento_Preformas] 
		(
		ID_Preforma,
		Evento,
		Etapa,
		Sistema_Usuario,
		Sistema_Fecha) 
		VALUES(
		:ID_Preforma,
		:Evento,
		:Etapa,
		:Sistema_Usuario,
		SYSDATETIME()
		)";
		$parametros = array(
			":ID_Preforma" => $RAM,
			":Evento" => $Evento,
			":Etapa" => $Etapa,
			":Sistema_Usuario" => $_SESSION["user_name"]
		);
		return $this->insert($query, $parametros);
	}

	protected function saveRequisito($RAM, $permiso_explotacion, $certificado_operacion, $Carnet_colegiacion, $acreditar_representracion, $escrito_solicitud, $DNI, $RTN, $inspeccion_fisico, $boleta_revision, $contrato_arrendamiento, $certificado_autencididad_carta, $certificado_autencididad_documentos)
	{
		$query = "INSERT INTO [IHTT_PREFORMA].[dbo].[TB_Validacion_Documentos](ID_Formulario_Solicitud,Check_Permiso_Explotacion,Check_Certificado_Operacion,Check_Carnet_Abogado,Check_Acreditar_Representacion,Check_Escrito_Solicitud, Check_DNI, Check_RTN, Check_Inspeccion_Mecanica, Check_Boleta, Check_Antecedentes, Check_SAR, Check_PGR, Check_Arrendamiento, Check_Escritura_Constitucion, Check_Cert_Auntenticidad_Firma, Check_Autenticidad_Documentos, Sistema_Fecha) VALUES(:IDP,:Check_Permiso_Explotacion,:Check_Certificado_Operacion,:Check_Carnet_Abogado,:Check_Acreditar_Representacion,:CKES,:CKD,:CKR,:CKIM,:CKB,:CKA,:CKS,:CKPGR,:CKARR,:CKESC,:CKCAF,:CKCAD,SYSDATETIME())";
		$parametros = array(":IDP" => $RAM, ":Check_Permiso_Explotacion" => $permiso_explotacion, ":Check_Certificado_Operacion" => $certificado_operacion, ":Check_Carnet_Abogado" => $Carnet_colegiacion, ":Check_Acreditar_Representacion" => $acreditar_representracion, ":CKES" => $escrito_solicitud, ":CKD" => $DNI, ":CKR" => $RTN, ":CKIM" => $inspeccion_fisico, ":CKB" => $boleta_revision, ":CKA" => 'NO', ":CKS" => 'NO', ":CKPGR" => 'NO', ":CKARR" => $contrato_arrendamiento, ":CKESC" => 'NO', ":CKCAF" => $certificado_autencididad_carta, ":CKCAD" => $certificado_autencididad_documentos);
		return $this->insert($query, $parametros);
	}

	protected function saveRequisitos()
	{
		// BANDERA DE ERROR
		$ERROR = false;
		//*******************************************************************************************************************/
		// Inicio Decodificando los json recibidos
		//*******************************************************************************************************************/
		$_POST["Requisitos"] = json_decode($_POST["Requisitos"], true);
		$i = 0;
		$permiso_explotacion = 'NO';
		$certificado_operacion = 'NO';
		$Carnet_colegiacion = 'NO';
		$acreditar_representracion = 'NO';
		$escrito_solicitud = 'NO';
		$DNI = 'NO';
		$RTN = 'NO';
		$inspeccion_fisico = 'NO';
		$boleta_revision = 'NO';
		$contrato_arrendamiento = 'NO';
		$certificado_autencididad_carta = 'NO';
		$certificado_autencididad_documentos = 'NO';
		foreach ($_POST["Requisitos"] as $value) {
			switch ($value) {
				case 1:
					$permiso_explotacion = 'SI';
					break;
				case 2:
					$certificado_operacion = 'SI';
					break;
				case 3:
					$Carnet_colegiacion = 'SI';
					break;
				case 4:
					$acreditar_representracion = 'SI';
					break;
				case 5:
					$escrito_solicitud = 'SI';
					break;
				case 6:
					$DNI = 'SI';
					break;
				case 7:
					$RTN = 'SI';
					break;
				case 8:
					$inspeccion_fisico = 'SI';
					break;
				case 9:
					$boleta_revision = 'SI';
					break;
				case 10:
					$contrato_arrendamiento = 'SI';
					break;
				case 11:
					$certificado_autencididad_carta = 'SI';
					break;
				case 12:
					$certificado_autencididad_documentos = 'SI';
					break;
			}
			$i++;
		}
		//*******************************************************************************************************************/
		//* Si viene algun requisito se salva a nivel de base de datos
		//*******************************************************************************************************************/
		if ($i > 0) {
			$this->db->beginTransaction();
			$isOKRequicito = $this->saveRequisito($_POST["RAM"], $permiso_explotacion, $certificado_operacion, $Carnet_colegiacion, $acreditar_representracion, $escrito_solicitud, $DNI, $RTN, $inspeccion_fisico, $boleta_revision, $contrato_arrendamiento, $certificado_autencididad_carta, $certificado_autencididad_documentos);
			if ($isOKRequicito == false) {
				$this->db->rollBack();
				echo json_encode(['ERROR'  =>  true]);
			} else {
				$this->db->commit();
				echo json_encode(['ID_Documentos'  =>  $isOKRequicito]);
			}
		} else {
			echo json_encode(['ID_Documentos'  =>  0]);
		}
	}

	//*******************************************************************************************************************/
	//* INICIO: funciones de actualizar updateSolicitanteExpediente
	//****************************************************************************************************************** */
	protected function updateSolicitanteExpediente($Concesion, $Apoderado, $Solicitante, $row_ciudad, $RAM)
	{
		$HASH = hash('SHA512', '%^4#09+-~@%&zfg' . $RAM . date('m/d/Y h:i:s a', time()), false);

		$query= "UPDATE [IHTT_DB].[dbo].[TB_Expedientes]
		SET 
			ID_Solicitud = :ID_Solicitud,
			Folio = :Folio ,
			FechaRecibido = SYSDATETIME(),
			ID_Solicitante = :ID_Solicitante,
			NombreSolicitante = :NombreSolicitante,
			Vin = :Vin,
			ID_Placa = :ID_Placa,
			Permiso_Explotacion = :Permiso_Explotacion,
			Certificado_Operacion = :Certificado_Operacion,
			VerificacionFecha = SYSDATETIME(),
			VerificacionEmpleado = :VerificacionEmpleado,
			Observacion = :Observacion,
			Expediente_Actual = :Expediente_Actual,
			SistemaUsuario = :SistemaUsuario,
			SitemaFecha = SYSDATETIME(),
			Fuente = :Fuente,
			SOL_MD5 = :SOL_MD5,
			Preforma = :Preforma,
			Placa_ingresa = :Placa_ingresa,
			Unidad_Censada = :Unidad_Censada,
			Es_Renovacion_Automatica = :Es_Renovacion_Automatica,
			Originado_En_Ventanilla = :Originado_En_Ventanilla,
			N_Permiso_Especial = :N_Permiso_Especial
		WHERE 
			ID_Solicitud = :ID_Solicitud";
			// -- ID_Expediente = :ID_Expediente";


		$parametros =array(
			
			":ID_Solicitud" => $RAM,
			":ID_Expediente" => $Concesion['ID_Expediente'], // $ID_Cate_Acro,  
			":Folio " => '',//
			":ID_Solicitante" => $Solicitante['ID_Solicitante'],
			":NombreSolicitante" =>  $Solicitante['Nombre'],
			":Vin" => '', //
			":ID_Placa" => '', //
			":Permiso_Explotacion" =>$Concesion['Permiso_Explotacion'],
			":Certificado_Operacion" => $Concesion['Certificado'],
			":VerificacionEmpleado" => $_SESSION["user_name"],
			":Observacion" => '',//$Obsexpediente,
			":Expediente_Actual" => '', //$Expeactual,
			":SistemaUsuario" => $_SESSION["user_name"],
			":Fuente" => 'IHTT',
			":SOL_MD5" =>$HASH,
			":Preforma" => $RAM,// $FOR,
			":Placa_ingresa" => '' ,//;	
			":Unidad_Censada" => '',//$Comprobante,
			":Es_Renovacion_Automatica" => $_SESSION["Es_Renovacion_Automatica"],
			":Originado_En_Ventanilla" => $_SESSION["Originado_En_Ventanilla"],
			":N_Permiso_Especial" =>$Concesion['Permiso_Especial'],
		);
	
		$result = $this->update($query, $parametros);

		$isOk = ['ID_Solicitante' => $RAM, 'HASH' => $HASH];
		return $isOk;
	}
	//*******************************************************************************************************************/
	//* FINAL: funciones de actualizar updateSolicitanteExpediente
	//****************************************************************************************************************** */
	
	//*******************************************************************************************************************/
	//* INICIO: funciones de actualizar updateApoderadoExpediente
	//****************************************************************************************************************** */
	protected function updateApoderadoExpediente($RAM, $Apoderado)
	{

		$query = "UPDATE [IHTT_DB].[dbo].[TB_Expediente_X_Apoderado]
		SET 
			ID_ColegiacionAPL = :ID_ColegiacionAPL,
			NombreApoderadoLega = :NombreApoderadoLega,
			OBS_Apoderado = :OBS_Apoderado,
			Fecha_Descargo = :Fecha_Descargo,
			ID_Estado_Apl = :ID_Estado_Apl,
			SistemaUsuario = :SistemaUsuario,
			SistemaFecha = SYSDATETIME()
		WHERE ID_Solicitud = :ID_Solicitud";

		$parametros  = array(
			":ID_ColegiacionAPL" => $Apoderado['Numero_Colegiacion'],
			":NombreApoderadoLega" => $Apoderado['Nombre'],
			":OBS_Apoderado" => '',
			":Fecha_Descargo" => null,
			":ID_Estado_Apl" => 'APL-E-01',
			":SistemaUsuario" => $_SESSION["user_name"],
			":ID_Solicitud" => $RAM
		);

		return $this->update($query, $parametros);
	}
	//*******************************************************************************************************************/
	//* FINAL: funciones de actualizar updateApoderadoExpediente
	//****************************************************************************************************************** */

	//*******************************************************************************************************************/
	//* INICIO: funciones de actualizar updateUnidadExpediente
	//****************************************************************************************************************** */
	protected function updateUnidadExpediente($RAM, $Unidad, $Concesion, $Estado)
	{
		// Consulta SQL para actualizar el vehículo
		$query = "UPDATE [IHTT_DB].[dbo].[TB_Solicitud_Vehiculo_Entra]
			SET 
				RTN_Propietario = :RTN_Propietario,
				Nombre_Propietario = :Nombre_Propietario,
				ID_Placa = :ID_Placa,
				ID_Marca = :ID_Marca,
				Anio = :Anio,
				Modelo = :Modelo,
				Tipo_Vehiculo = :Tipo_Vehiculo,
				ID_Color = :ID_Color,
				Motor = :Motor,
				Chasis = :Chasis,
				VIN = :VIN,
				Combustible = :Combustible,
				Alto = :Alto,
				Ancho = :Ancho,
				Largo = :Largo,
				Capacidad_Carga = :Capacidad_Carga,
				Peso_Unidad = :Peso_Unidad,
				Numero_Certificado = :Numero_Certificado,
				Numero_Explotacion = :Numero_Explotacion,
				Sistema_Usuario = :Sistema_Usuario,
				Sistema_Fecha = SYSDATETIME()
				Numero_PermisoEspecial = :Numero_PermisoEspecial,
			WHERE ID_Solicitud = :ID_Solicitud";

		$parametros = array(
			":RTN_Propietario" => $Unidad['RTN_Propietario'],
			":Nombre_Propietario" => $Unidad['Nombre_Propietario'],
			":ID_Placa" => $Unidad['Placa'],
			":ID_Marca" => $Unidad['Marca'],
			":Anio" => $Unidad['Anio'],
			":Modelo" => $Unidad['Modelo'],
			":Tipo_Vehiculo" => $Unidad['Tipo'],
			":ID_Color" => $Unidad['Color'],
			":Motor" =>  $Unidad['Motor'],
			":Chasis" => $Unidad['Chasis'],
			":VIN" => $Unidad['VIN'],
			":Combustible" => $Unidad['Combustible'],
			":Alto" => $Unidad['Alto'],
			":Ancho" => $Unidad['Ancho'],
			":Largo" => $Unidad['Largo'],
			":Capacidad_Carga" =>$Unidad['Capacidad'],
			":Peso_Unidad" =>'',
			":Numero_Explotacion" => $Unidad['Permiso_Explotacion'],
			":Numero_Certificado" => $Unidad['Certificado'],
			":Sistema_Usuario" => $_SESSION["user_name"],
			":Largo" => $Unidad['Largo'],
			":Largo" => $Unidad['Largo'],
			":Numero_PermisoEspecial"=> $Unidad['Numero_PermisoEspecial'],
			":ID_Solicitud" => $RAM);


		// Ejecutar la actualización (esto usa la función insert, que también puede manejar updates)
		return $this->update($query, $parametros);
	}
	//*******************************************************************************************************************/
	//* FINAL: funciones de actualizar updateUnidadExpediente
	//****************************************************************************************************************** */

	//*******************************************************************************************************************/
	//* INICIO: funciones de actualizar updateUnidadExpediente1
	//****************************************************************************************************************** */
	protected function updateUnidadExpediente1($RAM, $Unidad, $Concesion, $Estado)
	{
		
		$query = "UPDATE [IHTT_DB].[dbo].[TB_Solicitud_Vehiculo_Actual]
			SET 
				RTN_Propietario = :RTN_Propietario,
				Nombre_Propietario = :Nombre_Propietario,
				ID_Placa = :ID_Placa,
				ID_Marca = :ID_Marca,
				Anio = :Anio,
				Modelo = :Modelo,
				Tipo_Vehiculo = :Tipo_Vehiculo,
				ID_Color = :ID_Color,
				Motor = :Motor,
				Chasis = :Chasis,
				VIN = :VIN,
				Combustible = :Combustible,
				Alto = :Alto,
				Ancho = :Ancho,
				Largo = :Largo,
				Capacidad_Carga = :Capacidad_Carga,
				Peso_Unidad = :Peso_Unidad,
				Numero_Certificado = :Numero_Certificado,
				Numero_Explotacion = :Numero_Explotacion,
				Sistema_Usuario = :Sistema_Usuario,
				Sistema_Fecha = SYSDATETIME()
				Numero_PermisoEspecial = :Numero_PermisoEspecial,
				   -- ID_Placa_Antes_Replaqueo = :ID_Placa_Antes_Replaqueo
			WHERE ID_Solicitud = :ID_Solicitud";

		$parametros = array(
			":RTN_Propietario" => $Unidad['RTN_Propietario'],
			":Nombre_Propietario" => $Unidad['Nombre_Propietario'],
			":ID_Placa" => $Unidad['Placa'],
			":ID_Marca" => $Unidad['Marca'],
			":Anio" => $Unidad['Anio'],
			":Modelo" => $Unidad['Modelo'],
			":Tipo_Vehiculo" => $Unidad['Tipo'],
			":ID_Color" => $Unidad['Color'],
			":Motor" =>  $Unidad['Motor'],
			":Chasis" => $Unidad['Chasis'],
			":VIN" => $Unidad['VIN'],
			":Combustible" => $Unidad['Combustible'],
			":Alto" => $Unidad['Alto'],
			":Ancho" => $Unidad['Ancho'],
			":Largo" => $Unidad['Largo'],
			":Capacidad_Carga" =>$Unidad['Capacidad'],
			":Peso_Unidad" =>'',
			":Numero_Explotacion" => $Unidad['Permiso_Explotacion'],
			":Numero_Certificado" => $Unidad['Certificado'],
			":Sistema_Usuario" => $_SESSION["user_name"],
			":Largo" => $Unidad['Largo'],
			":Largo" => $Unidad['Largo'],
			":Numero_PermisoEspecial"=> $Unidad['Numero_PermisoEspecial'],
			":ID_Solicitud" => $RAM
			// ":ID_Placa_Antes_Replaqueo" => $_POST['ID_Placa_Antes_Replaqueo']
			);
		// Ejecutar la actualización (esto usa la función insert, que también puede manejar updates)
		return $this->update($query, $parametros);
	}
	//*******************************************************************************************************************/
	//* FINAL: funciones de actualizar updateUnidadExpediente1
	//****************************************************************************************************************** */

	//*******************************************************************************************************************/
	//* funciones de actualizar preforma
	//****************************************************************************************************************** */
	protected function updateSolicitante($Concesion, $Apoderado, $Solicitante, $row_ciudad, $RAM)
	{
		$HASH = hash('SHA512', '%^4#09+-~@%&zfg' . $RAM . date('m/d/Y h:i:s a', time()), false);

		$query = "UPDATE [IHTT_PREFORMA].[dbo].[TB_Solicitante]
		SET 
			Es_Renovacion_Automatica = :Es_Renovacion_Automatica,
			Originado_En_Ventanilla = :Originado_En_Ventanilla,
			Usuario_Creacion = :Usuario_Creacion,
			Codigo_Ciudad = :Codigo_Ciudad,
			ID_Formulario_Solicitud_Encrypted = :ID_Formulario_Solicitud_Encrypted,
			Nombre_Solicitante = :Nombre_Solicitante,
			ID_Tipo_Solicitante = :ID_Tipo_Solicitante,
			RTN_Solicitante = :RTN_Solicitante,
			Domicilo_Solicitante = :Domicilo_Solicitante,
			Denominacion_Social = :Denominacion_Social,
			ID_Aldea = :ID_Aldea,
			Telefono_Solicitante = :Telefono_Solicitante,
			Email_Solicitante = :Email_Solicitante,
			Numero_Escritura = :Numero_Escritura,
			RTN_Notario = :RTN_Notario,
			Notario_Autorizante = :Notario_Autorizante,
			Lugar_Constitucion = :Lugar_Constitucion,
			Fecha_Constitucion = :Fecha_Constitucion,
			Estado_Formulario = :Estado_Formulario,
			Fecha_Cancelacion = :Fecha_Cancelacion,
			Observaciones = :Observaciones,
			Usuario_Cancelacion = :Usuario_Cancelacion,
			Sistema_Fecha = SYSDATETIME(),
			Presentacion_Documentos = :Presentacion_Documentos,
			Etapa_Preforma = :Etapa_Preforma,
			Usuario_Acepta = :Usuario_Acepta,
			Fecha_Aceptacion = SYSDATETIME(),
			Codigo_Usuario_Acepta = :Codigo_Usuario_Acepta,
			Tipo_Solicitud = :Tipo_Solicitud,
			Entrega_Ubicacion = :Entrega_Ubicacion
		WHERE 
			ID_Formulario_Solicitud = :ID_Formulario_Solicitud";

		$parametros = array(
			":Es_Renovacion_Automatica" => $_SESSION["Es_Renovacion_Automatica"],
			":Originado_En_Ventanilla" => $_SESSION["Originado_En_Ventanilla"],
			":Usuario_Creacion" => $_SESSION["user_name"],
			":Codigo_Ciudad" => $row_ciudad[0]['Codigo_Ciudad'],
			":ID_Formulario_Solicitud" => $RAM,
			":ID_Formulario_Solicitud_Encrypted" => $HASH,
			":Nombre_Solicitante" => strtoupper($Solicitante['Nombre']),
			":ID_Tipo_Solicitante" => $Solicitante['Tipo_Solicitante'],
			":RTN_Solicitante" => $Solicitante['RTN'],
			":Domicilo_Solicitante" => strtoupper($Solicitante['Domicilio']),
			":Denominacion_Social" => strtoupper($Solicitante['Denominacion']),
			":ID_Aldea" => $Solicitante['Aldea'],
			":Telefono_Solicitante" => $Solicitante['Telefono'],
			":Email_Solicitante" => $Solicitante['Email'],
			":Numero_Escritura" => '',
			":RTN_Notario" => '',
			":Notario_Autorizante" => '',
			":Lugar_Constitucion" => '',
			":Fecha_Constitucion" => '1900-01-01',
			":Estado_Formulario" => 'IDE-7',
			":Fecha_Cancelacion" => null,
			":Observaciones" => strtoupper(''),
			":Usuario_Cancelacion" => '',
			":Presentacion_Documentos" => $Apoderado['Tipo_Presentacion'],
			":Etapa_Preforma" => 1,
			":Usuario_Acepta" => $_SESSION["user_name"],
			":Codigo_Usuario_Acepta" => $_SESSION["ID_Usuario"],
			":Tipo_Solicitud" => $Concesion['esCarga'] = true ? 'CARGA' : 'PASAJEROS',
			":Entrega_Ubicacion" => $Apoderado['Lugar_Entrega']
		);

		$result = $this->update($query, $parametros);

		$isOk = ['ID_Solicitante' => $RAM, 'HASH' => $HASH];
		return $isOk;
	}

	protected function updateApoderado($RAM, $Apoderado)
	{

		$query = "UPDATE [IHTT_PREFORMA].[dbo].[TB_Apoderado_Legal]
		SET 
			Nombre_Apoderado_Legal = :Nombre_Apoderado_Legal,
			Ident_Apoderado_Legal = :Ident_Apoderado_Legal,
			ID_Colegiacion = :ID_Colegiacion,
			Direccion_Apoderado_Legal = :Direccion_Apoderado_Legal,
			Telefono_Apoderado_Legal = :Telefono_Apoderado_Legal,
			Email_Apoderado_Legal = :Email_Apoderado_Legal,
			Sistema_Fecha = SYSDATETIME()
		WHERE 
		ID_Formulario_Solicitud = :ID_Formulario_Solicitud AND 
     	Ident_Apoderado_Legal = :Ident_Apoderado_Legal";

		$parametros = array(
			":ID_Formulario_Solicitud" => $RAM,
			":Nombre_Apoderado_Legal" => strtoupper($Apoderado['Nombre']),
			":Ident_Apoderado_Legal" => $Apoderado['RTN'],
			":ID_Colegiacion" => $Apoderado['Numero_Colegiacion'],
			":Direccion_Apoderado_Legal" => strtoupper($Apoderado['Direccion']),
			":Telefono_Apoderado_Legal" => $Apoderado['Telefono'],
			":Email_Apoderado_Legal" => strtoupper($Apoderado['Email'])
		);


		return $this->update($query, $parametros);
	}

	protected function updateUnidad($RAM, $Unidad, $Concesion, $Estado)
	{
		// Consulta SQL para actualizar el vehículo
		$query = "UPDATE [IHTT_PREFORMA].[dbo].[TB_Vehiculo]
		SET 
			RTN_Propietario = :RTN_Propietario,
			Nombre_Propietario = :Nombre_Propietario,
			ID_Marca = :ID_Marca,
			Anio = :Anio,
			Modelo = :Modelo,
			Tipo_Vehiculo = :Tipo_Vehiculo,
			ID_Color = :ID_Color,
			Motor = :Motor,
			Chasis = :Chasis,
			VIN = :VIN,
			Combustible = :Combustible,
			Alto = :Alto,
			Ancho = :Ancho,
			Largo = :Largo,
			Capacidad_Carga = :Capacidad_Carga,
			Peso_Unidad = 0,  
			Permiso_Explotacion = :Permiso_Explotacion,
			Certificado_Operacion = :Certificado_Operacion,
			Permiso_Especial = :Permiso_Especial,
			Sistema_Fecha = SYSDATETIME(),
			Estado = :Estado,
			ID_Placa_Antes_Replaqueo = :ID_Placa_Antes_Replaqueo,
			Sistema_Usuario = :Sistema_Usuario
		WHERE 
			ID_Formulario_Solicitud = :ID_Formulario_Solicitud AND
			ID_Placa = :ID_Placa";

		$parametros = array(
			":ID_Formulario_Solicitud" => $RAM,
			":RTN_Propietario" => $Unidad['RTN_Propietario'],
			":Nombre_Propietario" => strtoupper($Unidad['Nombre_Propietario']),
			":ID_Placa" => strtoupper($Unidad['Placa']),
			":ID_Marca" => $Unidad['Marca'],
			":Anio" => $Unidad['Anio'],
			":Modelo" => strtoupper($Unidad['Modelo']),
			":Tipo_Vehiculo" => strtoupper($Unidad['Tipo']),
			":ID_Color" => $Unidad['Color'],
			":Motor" => strtoupper($Unidad['Motor']),
			":Chasis" => strtoupper($Unidad['Serie']),
			":VIN" => strtoupper($Unidad['VIN']),
			":Combustible" => strtoupper($Unidad['Combustible']),
			":Alto" => $Unidad['Alto'],
			":Ancho" => $Unidad['Ancho'],
			":Largo" => $Unidad['Largo'],
			":Capacidad_Carga" => $Unidad['Capacidad'],
			":Permiso_Explotacion" => strtoupper($Concesion['Permiso_Explotacion']),
			":Certificado_Operacion" => strtoupper($Concesion['Certificado']),
			":Permiso_Especial" => strtoupper($Concesion['Permiso_Especial']),
			":Estado" => $Estado,
			":ID_Placa_Antes_Replaqueo" => strtoupper($Unidad['ID_Placa_Antes_Replaqueo']),
			":Sistema_Usuario" => $_SESSION["user_name"]
		);

		// Ejecutar la actualización (esto usa la función insert, que también puede manejar updates)
		return $this->update($query, $parametros);
	}

	//*******************************************************************************************************************/
	//* Funcion para Actualizar el expediente
	//*******************************************************************************************************************/
	protected function updateExpediente()
	{
		// BANDERA DE ERROR
		$ERROR = false;
		// Start a transaction
		$this->db->beginTransaction();
		//*******************************************************************************************************************/
		//* Inicio Decodificando los json recibidos
		//*******************************************************************************************************************/
		$_POST["Concesion"] = json_decode($_POST["Concesion"], true);
		$_POST["Apoderado"] = json_decode($_POST["Apoderado"], true);
		$_POST["Solicitante"] = json_decode($_POST["Solicitante"], true);
		$_POST["Unidad"] = json_decode($_POST["Unidad"], true);
		//*******************************************************************************************************************/
		// Final Decodificando los json recibidos
		//*******************************************************************************************************************/
		//*******************************************************************************************************************/
		// Inicio Si es Cambio de Unidad
		//*******************************************************************************************************************/
		if ($_POST["Concesion"]['esCambioDeVehiculo'] == true) {
			$_POST["Unidad1"] = json_decode($_POST["Unidad1"], true);
			$responseValidarMultas = $this->getDatosMulta($_POST["Unidad"]['Placa'], $_POST["Unidad"]['ID_Placa_Antes_Replaqueo']);
			$responseValidarMultas1 = $this->getDatosMulta($_POST["Unidad1"]['Placa'], $_POST["Unidad1"]['ID_Placa_Antes_Replaqueo']);
			$responseValidarPlacas = $this->validarPlaca($_POST["Unidad1"]['Placa'], $_POST["Unidad1"]['ID_Placa_Antes_Replaqueo'], isset($_POST["Concesion"]['esCertificado']) ? $_POST["Concesion"]['Certificado'] : $_POST["Concesion"]['Permiso_Especial']);
			$responseValidarPreforma = $this->validarEnPreforma($_POST["Unidad1"]['Placa'], $_POST["Unidad1"]['ID_Placa_Antes_Replaqueo'], isset($_POST["Concesion"]['esCertificado']) ? $_POST["Concesion"]['Certificado'] : $_POST["Concesion"]['Permiso_Especial'], $_POST["Concesion"]['RAM']);
		} else {
			$responseValidarMultas = $this->getDatosMulta($_POST["Unidad"]['Placa'], $_POST["Unidad"]['ID_Placa_Antes_Replaqueo']);
			$responseValidarPlacas = $this->validarPlaca($_POST["Unidad"]['Placa'], $_POST["Unidad"]['ID_Placa_Antes_Replaqueo'], isset($_POST["Concesion"]['esCertificado']) ? $_POST["Concesion"]['Certificado'] : $_POST["Concesion"]['Permiso_Especial']);
			$responseValidarPreforma = $this->validarEnPreforma($_POST["Unidad"]['Placa'], $_POST["Unidad"]['ID_Placa_Antes_Replaqueo'], isset($_POST["Concesion"]['esCertificado']) ? $_POST["Concesion"]['Certificado'] : $_POST["Concesion"]['Permiso_Especial'], $_POST["Concesion"]['RAM']);
		}
		if ($_POST["Concesion"]['RAM'] == '') {
			$responseValidarUsuario = $this->getUsuarioAsigna();
			if ($responseValidarUsuario == false) {
				echo 'responseValidarUsuario == false';
			}
			$responseValidarCiudad = $this->getCiudad($_SESSION["ID_Usuario"]);
			if ($responseValidarCiudad == false) {
				echo 'responseValidarCiudad == false';
			}
			$RAM = $this->getSiguienteRAM($_POST["Concesion"]['Secuencia']);
		} else {
			$RAM['nuevo_numero'] = $_POST["Concesion"]['RAM'];
			//*******************************************************************************************************************/
			//* Aqui se crean estas dos variable solo para que no de error en las siguientes lineas
			//*******************************************************************************************************************/
			$responseValidarUsuario = true;
			$responseValidarCiudad = true;
		}
		if (
			$RAM == false or
			((isset($responseValidarUsuario)   and $responseValidarUsuario  == false  and is_array($responseValidarUsuario) == false))    or
			((isset($responseValidarCiudad)    and $responseValidarCiudad   == false  and is_array($responseValidarCiudad) == false))    or
			((isset($responseValidarPlacas)    and (isset($responseValidarPlacas[1])    and $responseValidarPlacas[1]    > 0))  or ((isset($responseValidarPlacas)   and $responseValidarPlacas    == false and is_array($responseValidarPlacas) == false)))   or
			((isset($responseValidarMultas)    and (isset($responseValidarMultas[1])     and $responseValidarMultas[1]    > 0)) or ((isset($responseValidarMultas)   and $responseValidarMultas    == false and is_array($responseValidarMultas) == false)))   or
			((isset($responseValidarMultas1)   and (isset($responseValidarMultas1[1])    and $responseValidarMultas1[1]   > 0)) or ((isset($responseValidarMultas1)  and $responseValidarMultas1   == false and is_array($responseValidarMultas1) == false)))   or
			((isset($responseValidarPreforma)  and (isset($responseValidarPreforma[1])   and $responseValidarPreforma[1]  > 0)) or ((isset($responseValidarPreforma) and $responseValidarPreforma  == false and is_array($responseValidarPreforma) == false)))
		) {
			$this->db->rollBack();
			echo json_encode([
				'ERROR'  =>  true,
				'RAM'  =>  $RAM,
				'Ciudad'      =>  isset($responseValidarCiudad) ? $responseValidarCiudad : '',
				'Usuario'     =>  isset($responseValidarUsuario) ? $responseValidarUsuario : '',
				'Placas'      =>  $responseValidarPlacas,
				'Multas'      =>  $responseValidarMultas,
				'Multas1'     =>  isset($responseValidarMultas1) ? $responseValidarMultas1 : '',
				'Preforma'   =>   isset($responseValidarPreforma) ? $responseValidarPreforma : ''
			]);
		} else {
			if ($_POST["Concesion"]['RAM'] == '') {
				$isOKSolicitante = $this->updateSolicitanteExpediente($_POST["Concesion"], $_POST["Apoderado"], $_POST["Solicitante"], $responseValidarCiudad, $RAM['nuevo_numero']);
			} else {
				$isOKSolicitante = $_POST["Solicitante"]['ID_Solicitante'];
			}
			if ($isOKSolicitante == false) {
				$this->db->rollBack();
				echo json_encode('solicitante');
			} else {
				if ($_POST["Concesion"]['RAM'] == '') {
					$isOKApoderado = $this->updateApoderadoExpediente($RAM['nuevo_numero'], $_POST["Apoderado"]);
				} else {
					$isOKApoderado = $_POST["Apoderado"]['ID_Apoderado'];
				}
				if ($isOKApoderado == false) {
					$this->db->rollBack();
					echo json_encode('apoderado');
				} else {
					if ($_POST["Concesion"]['esCambioDeVehiculo'] == true) {
						$isOKUnidad = $this->updateUnidadExpediente1($RAM['nuevo_numero'], $_POST["Unidad"], $_POST["Concesion"], 'SALE');
					} else {
						$isOKUnidad = $this->updateUnidadExpediente($RAM['nuevo_numero'], $_POST["Unidad"], $_POST["Concesion"], 'NORMAL');
					}
					if ($isOKUnidad == false) {
						$this->db->rollBack();
						echo json_encode(['UNIDAD'  =>  $RAM['nuevo_numero'], 'ESTADO'  => false]);
					} else {
						if ($_POST["Concesion"]['esCambioDeVehiculo'] == true) {
							$isOKUnidad1 = $this->updateUnidadExpediente($RAM['nuevo_numero'], $_POST["Unidad1"], $_POST["Concesion"], 'ENTRA');
							if ($isOKUnidad1 == false) {
								$this->db->rollBack();
								echo json_encode(['UNIDAD1'  =>  $RAM['nuevo_numero'], 'ESTADO'  => false]);
								$ERROR = true;
							} else {
								$this->db->commit();
								echo json_encode(
									[
										'Solicitante'    =>  isset($isOKSolicitante) ? $isOKSolicitante : false,
										'Apoderado'      =>  isset($isOKApoderado) ? $isOKApoderado : false,
										'Unidad'         =>  isset($isOKUnidad) ? $isOKUnidad : false,
										'Unidad1'        =>  isset($isOKUnidad1) ? $isOKUnidad1 : false
									]
								);
							}
						} else {
							$this->db->commit();
							echo json_encode(
								[
									'Solicitante'    =>  isset($isOKSolicitante) ? $isOKSolicitante : false,
									'Apoderado'      =>  isset($isOKApoderado) ? $isOKApoderado : false,
									'Unidad'         =>  isset($isOKUnidad) ? $isOKUnidad : false,
								]
							);
						}
					}
				}
			}
		}
	}
	
	//*******************************************************************************************************************/
	//* INICIO: funciones de actualizar deleteConcesionesExpediente
	//****************************************************************************************************************** */
	protected function deleteConcesionesExpediente()
	{
		$_POST["idConcesiones"] = json_decode($_POST["idConcesiones"], true);
		$return = true;
		$this->db->beginTransaction();
		foreach ($_POST["idConcesiones"] as $Concesion) {
			$query = "DELETE FROM  [IHTT_DB].[dbo].[TB_Expediente_X_Tipo_Tramite] where Certificado_Operacion = :Certificado_Operacion or N_Permiso_Especial = :N_Permiso_Especial or Permiso_Explotacion = :Permiso_Explotacion";
			$p = array(":Certificado_Operacion" => $Concesion, ":N_Permiso_Especial" => $Concesion, ":Permiso_Explotacion" => $Concesion);
			$return = $this->delete($query, $p);
			if ($return == false) {
				break;
			}
			$query = "DELETE FROM [IHTT_DB].[dbo].[TB_Solicitud_Vehiculo_Actual] where Numero_Certificado = :Numero_Certificado or Numero_PermisoEspecial = :Numero_PermisoEspecial or Numero_Explotacion = :Numero_Explotacion";
			$p = array(":Numero_Certificado" => $Concesion, ":Numero_PermisoEspecial" => $Concesion, ":Numero_Explotacion" => $Concesion);
			$return = $this->delete($query, $p);
			if ($return == false) {
				break;
			}
			$query = "DELETE FROM [IHTT_DB].[dbo].[TB_Solicitud_Vehiculo_Entra] where Numero_Certificado = :Numero_Certificado or Numero_PermisoEspecial = :Numero_PermisoEspecial or Numero_Explotacion = :Numero_Explotacion";
			$p = array(":Numero_Certificado" => $Concesion, ":Numero_PermisoEspecial" => $Concesion, ":Numero_Explotacion" => $Concesion);
			$return = $this->delete($query, $p);
			if ($return == false) {
				break;
			}
		}
		if ($return == false) {
			$this->db->rollBack();
			echo json_encode(array("error" => 2000, "errorhead" => "BORRANDO CONCESIONE(S) EXPEDIENTES", "errormsg" => 'ERROR AL INTENTAR CONCESIONES EXPEDIENTE, FAVOR CONTACTE AL ADMON DEL SISTEMA'));
		} else {
			$this->db->commit();
			echo json_encode(['Borrado'  =>  True]);
		}
	}

	//*******************************************************************************************************************/
	//* FINAL: funciones de actualizar deleteConcesionesExpediente
	//****************************************************************************************************************** */

	//*******************************************************************************************************************/
	//* INICIO: funciones de actualizar deleteTramiteExpediente
	//****************************************************************************************************************** */
	protected function deleteTramiteExpediente($RAM)
	{
		$_POST["idTramite"] = json_decode($_POST["idTramite"], true);
		$this->db->beginTransaction();
		// [IHTT_DB].[dbo].[TB_Expediente_X_Tipo_Tramite] ID_Solicitud
		$query = "DELETE FROM [IHTT_DB].[dbo].[TB_Expediente_X_Tipo_Tramite] where ID_Tramite = :ID_Tramite and ID_Solicitud=:ID_Solicitud";
		$p = array(":ID_Tramite" => $_POST["idTramite"], ":ID_Solicitud"=>$RAM);
		$return = $this->delete($query, $p);
		if ($return == false) {
			$this->db->rollBack();
			echo json_encode(array("error" => 2000, "errorhead" => "ELIMINAR TRAMITE EXPEDIENTE_X_TIPO_TRAMITE", "errormsg" => 'ERROR AL INTENTAR ELIMINAR TRAMITE EN EXPEDIENTE, FAVOR CONTACTE AL ADMON DEL SISTEMA'));
		} else {
			//$this->db->rollBack();
			$this->db->commit();
			echo json_encode(['Borrado'  =>  True]);
		}
	}
	//*******************************************************************************************************************/
	//* FINAL: funciones de actualizar deleteTramiteExpediente
	//****************************************************************************************************************** */

	//*******************************************************************************************************************/
	//* INICIO: funciones de actualizar saveTramitesExpediente
	//****************************************************************************************************************** */
	protected function saveTramitesExpediente($RAM, $Tramites, $Concesion)
	{
		if (isset($_POST["echo"])) {
			$this->db->beginTransaction();
		}
		$contadorInserts = 0;
		$isOk = array();
		$isOk[0] = false;
		$contador = count($_POST["Tramites"]);

		$query = "INSERT INTO [IHTT_DB].[dbo].[TB_Expediente_X_Tipo_Tramite]
        ([ID_Solicitud], [ID_Tramite], [OBS_Expediente], [Estado_gea_tramite], 
        [Observacion_gea_tramite], [SistemaUsuario], [SistemaFecha], 
        [ID_Categoria_Subservicio], [CategoriaSubservicio], [ID_Servicios], 
        [ServiciosNombre], [DescripcionTramite], [ID_Transporte], 
        [Id_Tipo_Categoria], [Certificado_Operacion], [Permiso_Explotacion], 
        [N_Permiso_Especial])
        VALUES 
        (:ID_Solicitud, :ID_Tramite, :OBS_Expediente, :Estado_gea_tramite, 
        :Observacion_gea_tramite, :SistemaUsuario, GETDATE(), 
        :ID_Categoria_Subservicio, :CategoriaSubservicio, :ID_Servicios, 
        :ServiciosNombre, :DescripcionTramite, :ID_Transporte, 
        :Id_Tipo_Categoria, :Certificado_Operacion, :Permiso_Explotacion, 
        :N_Permiso_Especial)";

			for ($i = 0; $i < $contador; $i++) {
				$parametros = array(
						':ID_Solicitud' => $RAM,
						':ID_Tramite' => $Tramites['ID_Tramite'][$i],

						':OBS_Expediente' => '',// $Tramites['ID_Tramite'][$i],
						':Estado_gea_tramite' =>'',// $Tramites['ID_Tramite'][$i],
						':Observacion_gea_tramite' =>'' , //$Tramites['ID_Tramite'][$i],

						':SistemaUsuario' =>  $_SESSION["user_name"],

						':ID_Categoria_Subservicio' =>  '', //$Tramites['ID_Tramite'][$i],
						':CategoriaSubservicio' =>  '', //$Tramites['ID_Tramite'][$i],

						':ID_Servicios' =>  '',//$Tramites['ID_Tipo_Servicio'][$i],
						':ServiciosNombre' =>'',//  $Tramites['ID_Tramite'][$i],

						':DescripcionTramite' => $Tramites['descripcion'][$i],
						':ID_Transporte' => '',//$Tramites['ID_Tramite'][$i],

						':Id_Tipo_Categoria' =>  $Tramites['ID_Categoria'][$i],
						':Certificado_Operacion' => $Concesion['Certificado'],
						':Permiso_Explotacion' =>  $Concesion['Permiso_Explotacion'],
						':N_Permiso_Especial' =>  $Concesion['Permiso_Especial']
				);
				
				// $isOk[$i] = ['ID' => $this->insert($query, $parametros), 'ID_Compuesto' => $Tramites[$i]['ID_Compuesto']];
				$isOk[$i] = ['ID_Solicitud' => $this->insert($query, $parametros)];

				if ($isOk[$i]['ID'] == false) {
					$this->db->rollback();
					unset($isOk);
					$isOk = array();
					$isOk[0] = false;
					break;
				} else {
					$contadorInserts++;
				}
			}
			
			if (!isset($_POST["echo"])) {
				if ($isOk[0] != false) {
					return $isOk;
				} else {
					echo json_encode(array("error" => 7001, "errorhead" => 'ADVERTENCIA', "errormsg" => 'ESTAMOS PRESENTANDO INCONVENIENTES TEMPORALES AL MOMENTO DE SALVAR EL TRAMITE EN TB_Expediente_X_Tipo_Tramite'));
				}
			} else {
				if ($isOk[0] != false) {
					if ($contadorInserts > 0) {
						$this->db->commit();
					}
					echo json_encode($isOk);
				} else {
					echo json_encode(array("error" => 7001, "errorhead" => 'ADVERTENCIA', "errormsg" => 'ESTAMOS PRESENTANDO INCONVENIENTES TEMPORALES AL MOMENTO DE SALVAR EL TRAMITE EN TB_Expediente_X_Tipo_Tramite'));
				}
			}
	}
	//*******************************************************************************************************************/
	//* FINAL: funciones de actualizar saveTramitesExpediente
	//****************************************************************************************************************** */
	

	//*******************************************************************************************************************/
	//* Funcion para Actualizar la preforma
	//*******************************************************************************************************************/
	protected function updatePreforma()
	{
		// BANDERA DE ERROR
		$ERROR = false;
		// Start a transaction
		$this->db->beginTransaction();
		//*******************************************************************************************************************/
		//* Inicio Decodificando los json recibidos
		//*******************************************************************************************************************/
		$_POST["Concesion"] = json_decode($_POST["Concesion"], true);
		$_POST["Apoderado"] = json_decode($_POST["Apoderado"], true);
		$_POST["Solicitante"] = json_decode($_POST["Solicitante"], true);
		$_POST["Unidad"] = json_decode($_POST["Unidad"], true);
		//*******************************************************************************************************************/
		// Final Decodificando los json recibidos
		//*******************************************************************************************************************/
		//*******************************************************************************************************************/
		// Inicio Si es Cambio de Unidad
		//*******************************************************************************************************************/
		if ($_POST["Concesion"]['esCambioDeVehiculo'] == true) {
			$_POST["Unidad1"] = json_decode($_POST["Unidad1"], true);
			$responseValidarMultas = $this->getDatosMulta($_POST["Unidad"]['Placa'], $_POST["Unidad"]['ID_Placa_Antes_Replaqueo']);
			$responseValidarMultas1 = $this->getDatosMulta($_POST["Unidad1"]['Placa'], $_POST["Unidad1"]['ID_Placa_Antes_Replaqueo']);
			$responseValidarPlacas = $this->validarPlaca($_POST["Unidad1"]['Placa'], $_POST["Unidad1"]['ID_Placa_Antes_Replaqueo'], isset($_POST["Concesion"]['esCertificado']) ? $_POST["Concesion"]['Certificado'] : $_POST["Concesion"]['Permiso_Especial']);
			$responseValidarPreforma = $this->validarEnPreforma($_POST["Unidad1"]['Placa'], $_POST["Unidad1"]['ID_Placa_Antes_Replaqueo'], isset($_POST["Concesion"]['esCertificado']) ? $_POST["Concesion"]['Certificado'] : $_POST["Concesion"]['Permiso_Especial'], $_POST["Concesion"]['RAM']);
		} else {
			$responseValidarMultas = $this->getDatosMulta($_POST["Unidad"]['Placa'], $_POST["Unidad"]['ID_Placa_Antes_Replaqueo']);
			$responseValidarPlacas = $this->validarPlaca($_POST["Unidad"]['Placa'], $_POST["Unidad"]['ID_Placa_Antes_Replaqueo'], isset($_POST["Concesion"]['esCertificado']) ? $_POST["Concesion"]['Certificado'] : $_POST["Concesion"]['Permiso_Especial']);
			$responseValidarPreforma = $this->validarEnPreforma($_POST["Unidad"]['Placa'], $_POST["Unidad"]['ID_Placa_Antes_Replaqueo'], isset($_POST["Concesion"]['esCertificado']) ? $_POST["Concesion"]['Certificado'] : $_POST["Concesion"]['Permiso_Especial'], $_POST["Concesion"]['RAM']);
		}
		if ($_POST["Concesion"]['RAM'] == '') {
			$responseValidarUsuario = $this->getUsuarioAsigna();
			if ($responseValidarUsuario == false) {
				echo 'responseValidarUsuario == false';
			}
			$responseValidarCiudad = $this->getCiudad($_SESSION["ID_Usuario"]);
			if ($responseValidarCiudad == false) {
				echo 'responseValidarCiudad == false';
			}
			$RAM = $this->getSiguienteRAM($_POST["Concesion"]['Secuencia']);
		} else {
			$RAM['nuevo_numero'] = $_POST["Concesion"]['RAM'];
			//*******************************************************************************************************************/
			//* Aqui se crean estas dos variable solo para que no de error en las siguientes lineas
			//*******************************************************************************************************************/
			$responseValidarUsuario = true;
			$responseValidarCiudad = true;
		}
		if (
			$RAM == false or
			((isset($responseValidarUsuario)   and $responseValidarUsuario  == false  and is_array($responseValidarUsuario) == false))    or
			((isset($responseValidarCiudad)    and $responseValidarCiudad   == false  and is_array($responseValidarCiudad) == false))    or
			((isset($responseValidarPlacas)    and (isset($responseValidarPlacas[1])    and $responseValidarPlacas[1]    > 0))  or ((isset($responseValidarPlacas)   and $responseValidarPlacas    == false and is_array($responseValidarPlacas) == false)))   or
			((isset($responseValidarMultas)    and (isset($responseValidarMultas[1])     and $responseValidarMultas[1]    > 0)) or ((isset($responseValidarMultas)   and $responseValidarMultas    == false and is_array($responseValidarMultas) == false)))   or
			((isset($responseValidarMultas1)   and (isset($responseValidarMultas1[1])    and $responseValidarMultas1[1]   > 0)) or ((isset($responseValidarMultas1)  and $responseValidarMultas1   == false and is_array($responseValidarMultas1) == false)))   or
			((isset($responseValidarPreforma)  and (isset($responseValidarPreforma[1])   and $responseValidarPreforma[1]  > 0)) or ((isset($responseValidarPreforma) and $responseValidarPreforma  == false and is_array($responseValidarPreforma) == false)))
		) {
			$this->db->rollBack();
			echo json_encode([
				'ERROR'  =>  true,
				'RAM'  =>  $RAM,
				'Ciudad'      =>  isset($responseValidarCiudad) ? $responseValidarCiudad : '',
				'Usuario'     =>  isset($responseValidarUsuario) ? $responseValidarUsuario : '',
				'Placas'      =>  $responseValidarPlacas,
				'Multas'      =>  $responseValidarMultas,
				'Multas1'     =>  isset($responseValidarMultas1) ? $responseValidarMultas1 : '',
				'Preforma'   =>   isset($responseValidarPreforma) ? $responseValidarPreforma : ''
			]);
		} else {
			if ($_POST["Concesion"]['RAM'] == '') {
				$isOKSolicitante = $this->updateSolicitante($_POST["Concesion"], $_POST["Apoderado"], $_POST["Solicitante"], $responseValidarCiudad, $RAM['nuevo_numero']);
			} else {
				$isOKSolicitante = $_POST["Solicitante"]['ID_Solicitante'];
			}
			if ($isOKSolicitante == false) {
				$this->db->rollBack();
				echo json_encode('solicitante');
			} else {
				if ($_POST["Concesion"]['RAM'] == '') {
					$isOKApoderado = $this->updateApoderado($RAM['nuevo_numero'], $_POST["Apoderado"]);
				} else {
					$isOKApoderado = $_POST["Apoderado"]['ID_Apoderado'];
				}
				if ($isOKApoderado == false) {
					$this->db->rollBack();
					echo json_encode('apoderado');
				} else {
					if ($_POST["Concesion"]['esCambioDeVehiculo'] == true) {
						$isOKUnidad = $this->updateUnidad($RAM['nuevo_numero'], $_POST["Unidad"], $_POST["Concesion"], 'SALE');
					} else {
						$isOKUnidad = $this->updateUnidad($RAM['nuevo_numero'], $_POST["Unidad"], $_POST["Concesion"], 'NORMAL');
					}
					if ($isOKUnidad == false) {
						$this->db->rollBack();
						echo json_encode(['UNIDAD'  =>  $RAM['nuevo_numero'], 'ESTADO'  => false]);
					} else {
						if ($_POST["Concesion"]['esCambioDeVehiculo'] == true) {
							$isOKUnidad1 = $this->updateUnidad($RAM['nuevo_numero'], $_POST["Unidad1"], $_POST["Concesion"], 'ENTRA');
							if ($isOKUnidad1 == false) {
								$this->db->rollBack();
								echo json_encode(['UNIDAD1'  =>  $RAM['nuevo_numero'], 'ESTADO'  => false]);
								$ERROR = true;
							} else {
								$this->db->commit();
								echo json_encode(
									[
										'Solicitante'    =>  isset($isOKSolicitante) ? $isOKSolicitante : false,
										'Apoderado'      =>  isset($isOKApoderado) ? $isOKApoderado : false,
										'Unidad'         =>  isset($isOKUnidad) ? $isOKUnidad : false,
										'Unidad1'        =>  isset($isOKUnidad1) ? $isOKUnidad1 : false
									]
								);
							}
						} else {
							$this->db->commit();
							echo json_encode(
								[
									'Solicitante'    =>  isset($isOKSolicitante) ? $isOKSolicitante : false,
									'Apoderado'      =>  isset($isOKApoderado) ? $isOKApoderado : false,
									'Unidad'         =>  isset($isOKUnidad) ? $isOKUnidad : false,
								]
							);
						}
					}
				}
			}
		}
	}

	//*******************************************************************************************************************/
	//* Funcion para salvar la preforma
	//*******************************************************************************************************************/
	protected function savePreforma()
	{
		// BANDERA DE ERROR
		$ERROR = false;
		// Start a transaction
		$this->db->beginTransaction();
		//*******************************************************************************************************************/
		//* Inicio Decodificando los json recibidos
		//*******************************************************************************************************************/
		$_POST["Concesion"] = json_decode($_POST["Concesion"], true);
		$_POST["Apoderado"] = json_decode($_POST["Apoderado"], true);
		$_POST["Solicitante"] = json_decode($_POST["Solicitante"], true);
		$_POST["Tramites"] = json_decode($_POST["Tramites"], true);
		$_POST["Unidad"] = json_decode($_POST["Unidad"], true);
		//*******************************************************************************************************************/
		// Final Decodificando los json recibidos
		//*******************************************************************************************************************/
		//*******************************************************************************************************************/
		// Inicio Si es Cambio de Unidad
		//*******************************************************************************************************************/
		if ($_POST["Concesion"]['esCambioDeVehiculo'] == true) {
			$_POST["Unidad1"] = json_decode($_POST["Unidad1"], true);
			$responseValidarMultas = $this->getDatosMulta($_POST["Unidad"]['Placa'], $_POST["Unidad"]['ID_Placa_Antes_Replaqueo']);
			$responseValidarMultas1 = $this->getDatosMulta($_POST["Unidad1"]['Placa'], $_POST["Unidad1"]['ID_Placa_Antes_Replaqueo']);
			$responseValidarPlacas = $this->validarPlaca($_POST["Unidad1"]['Placa'], $_POST["Unidad1"]['ID_Placa_Antes_Replaqueo'], isset($_POST["Concesion"]['esCertificado']) ? $_POST["Concesion"]['Certificado'] : $_POST["Concesion"]['Permiso_Especial']);
			$responseValidarPreforma = $this->validarEnPreforma($_POST["Unidad1"]['Placa'], $_POST["Unidad1"]['ID_Placa_Antes_Replaqueo'], isset($_POST["Concesion"]['esCertificado']) ? $_POST["Concesion"]['Certificado'] : $_POST["Concesion"]['Permiso_Especial']);
		} else {
			$responseValidarMultas = $this->getDatosMulta($_POST["Unidad"]['Placa'], $_POST["Unidad"]['ID_Placa_Antes_Replaqueo']);
			$responseValidarPlacas = $this->validarPlaca($_POST["Unidad"]['Placa'], $_POST["Unidad"]['ID_Placa_Antes_Replaqueo'], isset($_POST["Concesion"]['esCertificado']) ? $_POST["Concesion"]['Certificado'] : $_POST["Concesion"]['Permiso_Especial']);
			$responseValidarPreforma = $this->validarEnPreforma($_POST["Unidad"]['Placa'], $_POST["Unidad"]['ID_Placa_Antes_Replaqueo'], isset($_POST["Concesion"]['esCertificado']) ? $_POST["Concesion"]['Certificado'] : $_POST["Concesion"]['Permiso_Especial']);
		}
		if ($_POST["Concesion"]['RAM'] == '') {
			$responseValidarUsuario = $this->getUsuarioAsigna();
			if ($responseValidarUsuario == false) {
				echo 'responseValidarUsuario == false';
			}
			$responseValidarCiudad = $this->getCiudad($_SESSION["ID_Usuario"]);
			if ($responseValidarCiudad == false) {
				echo 'responseValidarCiudad == false';
			}
			$RAM = $this->getSiguienteRAM($_POST["Concesion"]['Secuencia']);
		} else {
			$RAM['nuevo_numero'] = $_POST["Concesion"]['RAM'];
			//*******************************************************************************************************************/
			//* Aqui se crean estas dos variable solo para que no de error en las siguientes lineas
			//*******************************************************************************************************************/
			$responseValidarUsuario = true;
			$responseValidarCiudad = true;
		}
		if (
			$RAM == false or
			((isset($responseValidarUsuario)   and $responseValidarUsuario  == false  and is_array($responseValidarUsuario) == false))    or
			((isset($responseValidarCiudad)    and $responseValidarCiudad   == false  and is_array($responseValidarCiudad) == false))    or
			((isset($responseValidarPlacas)    and (isset($responseValidarPlacas[1])    and $responseValidarPlacas[1]    > 0))  or ((isset($responseValidarPlacas)   and $responseValidarPlacas    == false and is_array($responseValidarPlacas) == false)))   or
			((isset($responseValidarMultas)    and (isset($responseValidarMultas[1])     and $responseValidarMultas[1]    > 0)) or ((isset($responseValidarMultas)   and $responseValidarMultas    == false and is_array($responseValidarMultas) == false)))   or
			((isset($responseValidarMultas1)   and (isset($responseValidarMultas1[1])    and $responseValidarMultas1[1]   > 0)) or ((isset($responseValidarMultas1)  and $responseValidarMultas1   == false and is_array($responseValidarMultas1) == false)))   or
			((isset($responseValidarPreforma)  and (isset($responseValidarPreforma[1])   and $responseValidarPreforma[1]  > 0)) or ((isset($responseValidarPreforma) and $responseValidarPreforma  == false and is_array($responseValidarPreforma) == false)))
		) {
			$this->db->rollBack();
			echo json_encode([
				'ERROR'  =>  true,
				'RAM'  =>  $RAM,
				'Ciudad'      =>  isset($responseValidarCiudad) ? $responseValidarCiudad : '',
				'Usuario'     =>  isset($responseValidarUsuario) ? $responseValidarUsuario : '',
				'Placas'      =>  $responseValidarPlacas,
				'Multas'      =>  $responseValidarMultas,
				'Multas1'     =>  isset($responseValidarMultas1) ? $responseValidarMultas1 : '',
				'Preforma'   =>   isset($responseValidarPreforma) ? $responseValidarPreforma : ''
			]);
		} else {
			if ($_POST["Concesion"]['RAM'] == '') {
				$isOKSolicitante = $this->saveSolicitante($_POST["Concesion"], $_POST["Apoderado"], $_POST["Solicitante"], $responseValidarCiudad, $RAM['nuevo_numero']);
			} else {
				$isOKSolicitante = $_POST["Solicitante"]['ID_Solicitante'];
			}
			if ($isOKSolicitante == false) {
				$this->db->rollBack();
				echo json_encode('solicitante');
			} else {
				if ($_POST["Concesion"]['RAM'] == '') {
					$isOKApoderado = $this->saveApoderado($RAM['nuevo_numero'], $_POST["Apoderado"]);
				} else {
					$isOKApoderado = $_POST["Apoderado"]['ID_Apoderado'];
				}
				if ($isOKApoderado == false) {
					$this->db->rollBack();
					echo json_encode('apoderado');
				} else {
					if ($_POST["Concesion"]['esCambioDeVehiculo'] == true) {
						$isOKUnidad = $this->saveUnidad($RAM['nuevo_numero'], $_POST["Unidad"], $_POST["Concesion"], 'SALE');
					} else {
						$isOKUnidad = $this->saveUnidad($RAM['nuevo_numero'], $_POST["Unidad"], $_POST["Concesion"], 'NORMAL');
					}
					if ($isOKUnidad == false) {
						$this->db->rollBack();
						echo json_encode(['UNIDAD'  =>  $RAM['nuevo_numero'], 'ESTADO'  => false]);
					} else {
						if ($_POST["Concesion"]['esCambioDeVehiculo'] == true) {
							$isOKUnidad1 = $this->saveUnidad($RAM['nuevo_numero'], $_POST["Unidad1"], $_POST["Concesion"], 'ENTRA');
							if ($isOKUnidad1 == false) {
								$this->db->rollBack();
								echo json_encode(['UNIDAD1'  =>  $RAM['nuevo_numero'], 'ESTADO'  => false]);
								$ERROR = true;
							}
						}
						if ($ERROR == false) {
							$isOKTramites = $this->saveTramites($RAM['nuevo_numero'], $_POST["Tramites"]);
							if ($isOKTramites[0] == false) {
								$this->db->rollBack();
								echo json_encode(['TRAMITES'  =>  $RAM['nuevo_numero'], 'ESTADO'  => false]);
							} else {
								$isOKBitacora = true;
								if ($_POST["Concesion"]['RAM'] == '') {
									$isOKBitacora = $this->saveBitacora($RAM['nuevo_numero'], 'INGRESO', 1);
								}
								if ($isOKBitacora == false) {
									$this->db->rollBack();
									echo json_encode(['BITACORA'  =>  $RAM['nuevo_numero'], 'ESTADO'  => false]);
								} else {
									$this->db->commit();
									echo json_encode(
										[
											'RAM'  =>  $RAM['nuevo_numero'],
											'Usuario_Asigna' =>  isset($responseValidarUsuario) ? $responseValidarUsuario : false,
											'Ciudad'         =>  isset($responseValidarCiudad)  ? $responseValidarCiudad : false,
											'Solicitante'    =>  isset($isOKSolicitante) ? $isOKSolicitante : false,
											'Apoderado'      =>  isset($isOKApoderado) ? $isOKApoderado : false,
											'Unidad'         =>  isset($isOKUnidad) ? $isOKUnidad : false,
											'Unidad1'        =>  isset($isOKUnidad1) ? $isOKUnidad1 : false,
											'Tramites'       =>  $isOKTramites,
											'Bitacora'       =>  isset($isOKBitacora) ? $isOKBitacora : false
										]
									);
								}
							}
						}
					}
				}
			}
		}
	}
}

if (!isset($_SESSION["ID_Usuario"]) || !isset($_SESSION["user_name"])) {
	echo json_encode(array("error" => 1000, "errorhead" => "INICIO DE SESSIÓN", "errormsg" => 'NO HAY UNA SESSION INICIADA, FAVOR INICIE SESION Y VUELVA A INTENTARLO'));
} else {
	$api = new Api_Ram($db);
}
