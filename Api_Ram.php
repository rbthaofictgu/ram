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
ini_set('max_execution_time', '1000');
ini_set('max_input_time', '1000');
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
// Funcion de Envio de Correos Electronicos
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';
// Libreria generadora de codigos QR
require_once("../qr/qrlib.php");

 class Api_Ram
{

	protected $db;
	protected $dominio;
	protected $dominio_corto;
	protected $dominio_raiz;
	protected $server_smtp;
	protected $server_smtp_port;
	protected $server_smtp_user;
	protected $server_smtp_password;
	protected $ip;
	protected $host;

	public function __construct($db,$appcfg_Dominio,$appcfg_Dominio_Corto,$appcfg_Dominio_Raiz,$appcfg_smtp_server,$appcfg_smtp_port,$appcfg_smtp_user,$appcfg_smtp_password)
	{
		$this->setDB(        $db,
		         $appcfg_Dominio,
		   $appcfg_Dominio_Corto,
		    $appcfg_Dominio_Raiz,
			 $appcfg_smtp_server,
			   $appcfg_smtp_port,
			   $appcfg_smtp_user,
		   $appcfg_smtp_password);

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
				$_POST['idEstado'] = json_decode($_POST['idEstado']);
				$_POST["RAM"] = json_decode($_POST["RAM"]);
				$this->updateEstadoPreforma($_POST["RAM"],$_POST['idEstado']);	
			} else if ($_POST["action"] == "cerrar-preforma") {
				$this->cerrarPreforma();					
			} else {
				echo json_encode(array("error" => 1001, "errorhead" => 'OPPS', "errormsg" => 'NO SE ENCONTRO NINGUNA FUNCION EN EL API PARA LA ACCIÓN REQUERIDA'));
			}
		}
	}

	
	protected function setDB($db,$appcfg_Dominio,$appcfg_Dominio_Corto,$appcfg_Dominio_Raiz,$appcfg_smtp_server,$appcfg_smtp_port,$appcfg_smtp_user,$appcfg_smtp_password): void	{
		$this->db = $db;
		$this->dominio = $appcfg_Dominio;
		$this->dominio_corto = $appcfg_Dominio_Corto;
		$this->dominio_raiz = $appcfg_Dominio_Raiz;
		$this->server_smtp = $appcfg_smtp_server;
		$this->server_smtp_port = $appcfg_smtp_port;
		$this->server_smtp_user = $appcfg_smtp_user;
		$this->server_smtp_password = $appcfg_smtp_password;		
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

	protected function getIp():string
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
			$stmt->execute($p);
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
			$stmt->execute($p);
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
	protected function saveIhttDbSoliciante($RAM,$RTN) : string {
		$query = "MERGE INTO [IHTT_DB].[dbo].[TB_Solicitante] AS target
				USING (
				SELECT 
					[RTN_Solicitante], 
					[Nombre_Solicitante], 
					[Denominacion_Social], 
					[ID_Tipo_Solicitante], 
					[Domicilo_Solicitante], 
					[Telefono_Solicitante], 
					[Email_Solicitante], 
					[Observaciones], 
					[ID_Aldea], 
					[Usuario_Creacion], 
					[Sistema_Fecha]
				FROM [IHTT_PREFORMA].[dbo].[TB_Solicitante] 
				WHERE RTN_Solicitante = :RTN_Solicitante and ID_Formulario_Solicitud = :ID_Formulario_Solicitud
				) AS source
				ON target.RTNSolicitante = source.RTN_Solicitante
				WHEN MATCHED THEN 
					UPDATE SET 
						target.NombreSolicitante = source.Nombre_Solicitante,
						target.NombreEmpresa = source.Denominacion_Social,
						target.CodigoSolicitanteTipo = source.ID_Tipo_Solicitante,
						target.Direccion = source.Domicilo_Solicitante,
						target.Telefono = source.Telefono_Solicitante,
						target.Email = source.Email_Solicitante,
						target.Observaciones = source.Observaciones,
						target.Aldea = source.ID_Aldea,
						target.SistemaUsuario = source.Usuario_Creacion,
						target.SistemaFecha = source.Sistema_Fecha
				WHEN NOT MATCHED THEN 
				INSERT (RTNSolicitante, NombreSolicitante, NombreEmpresa, CodigoSolicitanteTipo, Direccion, Telefono, Email, Observaciones, Aldea, SistemaUsuario, SistemaFecha)
				VALUES (source.RTN_Solicitante, source.Nombre_Solicitante, source.Denominacion_Social, source.ID_Tipo_Solicitante, source.Domicilo_Solicitante, source.Telefono_Solicitante, source.Email_Solicitante, source.Observaciones, source.ID_Aldea, source.Usuario_Creacion, source.Sistema_Fecha);";
		$parametros = array(":RTN_Solicitante" => $RTN,":ID_Formulario_Solicitud" => $RAM);
		return $this->update($query, $parametros);		
	}
	protected function saveIhttDbApoderadoLegal($RAM,$ID_Colegiacion) : string {
		$query = "MERGE INTO [IHTT_DB].[dbo].[TB_Apoderado_Legal] AS target
					USING (
						SELECT 
							[ID_Colegiacion], 
							[Nombre_Apoderado_Legal], 
							[Ident_Apoderado_Legal], 
							[Direccion_Apoderado_Legal], 
							[Telefono_Apoderado_Legal], 
							[Email_Apoderado_Legal], 
							[Sistema_Fecha]
						FROM [IHTT_PREFORMA].[dbo].[TB_Apoderado_Legal]
						WHERE ID_Colegiacion IS NOT NULL  and
							ID_Colegiacion = :ID_Colegiacion and ID_Formulario_Solicitud = :ID_Formulario_Solicitud
					) AS source
					ON target.ID_ColegiacionAPL = source.ID_Colegiacion
					WHEN MATCHED THEN 
						UPDATE SET 
							target.Nombre_Apoderado_Legal = source.Nombre_Apoderado_Legal,
							target.Identidad = source.Ident_Apoderado_Legal,
							target.Direccion = source.Direccion_Apoderado_Legal,
							target.Telefono = source.Telefono_Apoderado_Legal,
							target.Email = source.Email_Apoderado_Legal,
							target.SistemaFecha = source.Sistema_Fecha
					WHEN NOT MATCHED THEN 
						INSERT (ID_ColegiacionAPL, Nombre_Apoderado_Legal, Identidad, Direccion, Telefono, Email, SistemaFecha)
						VALUES (source.ID_Colegiacion, source.Nombre_Apoderado_Legal, source.Ident_Apoderado_Legal, source.Direccion_Apoderado_Legal, source.Telefono_Apoderado_Legal, source.Email_Apoderado_Legal, source.Sistema_Fecha);";
		$parametros = array(":ID_Colegiacion" => $ID_Colegiacion,":ID_Formulario_Solicitud" => $RAM);
		return $this->update($query, $parametros);		
	}	
	
	protected function saveIhttDbSolicitanteRepresentanteLegal($RAM) : string {
		$query = "MERGE INTO [IHTT_DB].[dbo].[TB_Solicitante_Representante_Legal] AS target
					USING (
						SELECT 
							RL.[ID_Formulario_Solicitud], 
							RL.[Nombre_Representante_Legal], 
							RL.[RTN_Representante_Legal], 
							RL.[Numero_Inscripcion], 
							RL.[Numero_Escritura], 
							RL.[Notario_Autorizante], 
							RL.[Lugar_Escritura], 
							RL.[Fecha_Escritura], 
							RL.[Domicilio_Representante_Legal], 
							RL.[Telefono_Representante_Legal] AS Telefono, 
							RL.[Correo_Representante_Legal] AS Email, 
							RL.[Sistema_Fecha], 
							RL.[RTN_Notario_Representante]
						FROM [IHTT_PREFORMA].[dbo].[TB_Representante_Legal] RL
						WHERE RL.ID_Formulario_Solicitud = :ID_Formulario_Solicitud 
						AND RL.ID_Formulario_Solicitud IS NOT NULL
					) AS source
					ON target.ID_Representante_Legal = source.RTN_Representante_Legal
					WHEN MATCHED THEN 
						UPDATE SET 
							target.Nombre_Representante_Legal = source.Nombre_Representante_Legal,
							target.Direccion = source.Domicilio_Representante_Legal,
							target.Telefono = source.Telefono,
							target.Email = source.Email,
							target.Sistema_Fecha = source.Sistema_Fecha
					WHEN NOT MATCHED THEN 
						INSERT (ID_Representante_Legal,         Nombre_Representante_Legal,        Direccion,                            Telefono,        Email,        Sistema_Fecha,   Sistema_Usuario)
						VALUES (source.RTN_Representante_Legal, source.Nombre_Representante_Legal, source.Domicilio_Representante_Legal, source.Telefono, source.Email, SYSDATETIME(), :Sistema_Usuario);";
		$parametros = array(":ID_Formulario_Solicitud" => $RAM,":Sistema_Usuario" => $_SESSION['usuario']);
		return $this->update($query, $parametros);		
	}	

	
	protected function saveIhttDbSolicitantexRepresentanteLegal($RAM) : string {
		$query = "MERGE INTO [IHTT_DB].[dbo].[TB_Solicitante_x_Representante_Legal] AS target
					USING (
						SELECT RL.[RTN_Representante_Legal] AS ID_Representante_Legal,
							SL.[ID_Formulario_Solicitud] AS ID_Solicitante,
							'EST-1' AS ID_Estado,
							:USUARIO AS Sistema_Usuario,
							SYSDATETIME() AS Sistema_Fecha
						FROM [IHTT_PREFORMA].[dbo].[TB_Representante_Legal] SL
						JOIN [IHTT_PREFORMA].[dbo].[TB_Representante_Legal] RL
						ON SL.[ID_Formulario_Solicitud] = RL.[ID_Formulario_Solicitud]
						WHERE SL.[ID_Formulario_Solicitud] = :ID_Formulario_Solicitud
					) AS source
					ON target.ID_Solicitante = source.ID_Solicitante AND target.ID_Representante_Legal = source.ID_Representante_Legal
					WHEN MATCHED THEN
						UPDATE SET 
							target.ID_Estado = source.ID_Estado,
							target.Sistema_Usuario = source.Sistema_Usuario,
							target.Sistema_Fecha = source.Sistema_Fecha
					WHEN NOT MATCHED THEN
						INSERT ([ID_Solicitante], [ID_Representante_Legal], [ID_Estado], [Sistema_Usuario], [Sistema_Fecha])
						VALUES (source.ID_Solicitante, source.ID_Representante_Legal, source.ID_Estado, source.Sistema_Usuario, source.Sistema_Fecha);";
		$parametros = array(":ID_Formulario_Solicitud" => $RAM,":USUARIO" => $RAM);
		return $this->update($query, $parametros);		
	}	
	
	protected function saveIhttDbExpedientes($RAM) : string {
		$query = "MERGE INTO [IHTT_DB].[dbo].[TB_Expedientes] AS target
					USING (
						SELECT 
							SUBSTRING(s.ID_Formulario_Solicitud, 1, 25) AS ID_Solicitud, 
							SUBSTRING(s.ID_Formulario_Solicitud, 1, 25) AS ID_Expediente, 
							SUBSTRING(s.Nombre_Solicitante, 1, 300) AS NombreSolicitante, 
							s.ID_Tipo_Solicitante, 
							s.RTN_Solicitante, 
							SUBSTRING(s.Domicilo_Solicitante, 1, 500) AS Domicilo_Solicitante, 
							SUBSTRING(s.Denominacion_Social, 1, 300) AS Denominacion_Social, 
							s.ID_Aldea, 
							SUBSTRING(s.Telefono_Solicitante, 1, 8) AS Telefono_Solicitante, 
							SUBSTRING(s.Email_Solicitante, 1, 80) AS Email_Solicitante, 
							SUBSTRING(s.Numero_Escritura, 1, 20) AS Numero_Escritura, 
							SUBSTRING(s.RTN_Notario, 1, 15) AS RTN_Notario, 
							SUBSTRING(s.Notario_Autorizante, 1, 100) AS Notario_Autorizante, 
							SUBSTRING(s.Lugar_Constitucion, 1, 300) AS Lugar_Constitucion, 
							s.Fecha_Constitucion, 
							SUBSTRING(s.Estado_Formulario, 1, 50) AS Estado_Formulario, 
							s.Fecha_Cancelacion, 
							SUBSTRING(s.Observaciones, 1, 300) AS Observacion, 
							SUBSTRING(s.Usuario_Cancelacion, 1, 50) AS Usuario_Cancelacion, 
							SUBSTRING(s.Aviso_Cobro, 1, 4) AS Aviso_Cobro, 
							SUBSTRING(s.Presentacion_Documentos, 1, 10) AS Presentacion_Documentos, 
							s.Sistema_Fecha, 
							s.Etapa_Preforma, 
							SUBSTRING(s.Usuario_Acepta, 1, 50) AS Usuario_Acepta, 
							s.Fecha_Aceptacion, 
							SUBSTRING(s.Codigo_Usuario_Acepta, 1, 5) AS Codigo_Usuario_Acepta, 
							SUBSTRING(s.Observacion_Cancelacion, 1, 400) AS Observacion_Cancelacion, 
							SUBSTRING(s.Usuario_Inadmision, 1, 50) AS Usuario_Inadmision, 
							s.Fecha_Inadmision, 
							SUBSTRING(s.Observacion_Inadmision, 1, 400) AS Observacion_Inadmision, 
							SUBSTRING(s.Tipo_Solicitud, 1, 10) AS Tipo_Solicitud, 
							SUBSTRING(s.Entrega_Ubicacion, 1, 15) AS Entrega_Ubicacion, 
							SUBSTRING(s.Usuario_Creacion, 1, 50) AS Usuario_Creacion, 
							SUBSTRING(s.Codigo_Ciudad, 1, 6) AS Codigo_Ciudad, 
							GETDATE() AS FechaRecibido, 
							0 AS Folio, 
							'' AS Vin, 
							'' AS ID_Placa, 
							'' AS Certificado_Operacion, 
							NULL AS VerificacionFecha, 
							'' AS VerificacionEmpleado, 
							'' AS Expediente_Actual, 
							SUBSTRING(s.Usuario_Creacion, 1, 50) AS SistemaUsuario, 
							GETDATE() AS SitemaFecha,  
							'IHTT' AS Fuente, 
							CONVERT(VARCHAR(128), HASHBYTES('SHA2_512', 
								CAST(SUBSTRING(s.ID_Formulario_Solicitud, 1, 128) AS VARCHAR(128)) + 
								CAST(NEWID() AS VARCHAR(36)) 
							), 2) AS SOL_MD5, 
							SUBSTRING(s.ID_Formulario_Solicitud, 1, 50) AS Preforma, 
							'' AS ID_gea, 
							'' AS Estado_gea_solicitud, 
							'' AS Observacion_gea_solicitud, 
							'' AS Expediente_Secuestrado, 
							'' AS Expediente_Desistido, 
							'' AS Placa_ingresa, 
							'ESTADO-020' AS Expediente_Estado, 
							'' AS ID_Ticket, 
							'' AS Unidad_Censada, 
							'' AS Area_Operacion_MTX, 
							s.Es_Renovacion_Automatica, 
							s.Originado_En_Ventanilla, 
							'' AS N_Permiso_Especial, 
							'' AS RTN_Taller 
						FROM [IHTT_PREFORMA].[dbo].[TB_Solicitante] s 
						WHERE s.ID_Formulario_Solicitud = :ID_Formulario_Solicitud
					) AS source
					ON target.ID_Expediente = source.ID_Solicitud
					WHEN MATCHED THEN 
					UPDATE SET 
						target.FechaRecibido = source.FechaRecibido,
						target.ID_Solicitante = source.RTN_Solicitante,
						target.NombreSolicitante = source.NombreSolicitante,
						target.Permiso_Explotacion = '',
						target.Observacion = source.Observacion,
						target.SistemaUsuario = source.SistemaUsuario,
						target.SitemaFecha = SYSDATETIME(),
						target.Es_Renovacion_Automatica = source.Es_Renovacion_Automatica,
						target.Originado_En_Ventanilla = source.Originado_En_Ventanilla
					WHEN NOT MATCHED THEN
					INSERT (
						ID_Solicitud, ID_Expediente, Folio, FechaRecibido, ID_Solicitante, 
						NombreSolicitante, Vin, ID_Placa, Permiso_Explotacion, Certificado_Operacion, 
						VerificacionFecha, VerificacionEmpleado, Observacion, Expediente_Actual, 
						SistemaUsuario, SitemaFecha, Fuente, SOL_MD5, Preforma, 
						ID_gea, Estado_gea_solicitud, Observacion_gea_solicitud, Expediente_Secuestrado, 
						Expediente_Desistido, Placa_ingresa, Expediente_Estado, ID_Ticket, Unidad_Censada, 
						Area_Operacion_MTX, Es_Renovacion_Automatica, Originado_En_Ventanilla, 
						N_Permiso_Especial, RTN_Taller
					) 
					VALUES (
						source.ID_Solicitud, source.ID_Expediente, source.Folio, source.FechaRecibido, source.RTN_Solicitante, 
						source.NombreSolicitante, source.Vin, source.ID_Placa, '', source.Certificado_Operacion, 
						source.VerificacionFecha, source.VerificacionEmpleado, source.Observacion, source.Expediente_Actual, 
						source.SistemaUsuario, source.SitemaFecha, source.Fuente, source.SOL_MD5, source.Preforma, 
						source.ID_gea, source.Estado_gea_solicitud, source.Observacion_gea_solicitud, source.Expediente_Secuestrado, 
						source.Expediente_Desistido, source.Placa_ingresa, source.Expediente_Estado, source.ID_Ticket, source.Unidad_Censada, 
						source.Area_Operacion_MTX, source.Es_Renovacion_Automatica, source.Originado_En_Ventanilla, 
						source.N_Permiso_Especial, source.RTN_Taller
					);";
		$parametros = array(":ID_Formulario_Solicitud" => $RAM);
		return $this->update($query, $parametros);
	}

	protected function saveIhttDbSolicitudVehiculoActual($RAM) : string {
		$query = "MERGE INTO [IHTT_DB].[dbo].[TB_Solicitud_Vehiculo_Actual] AS target
				USING (
					SELECT  
						s.ID_Formulario_Solicitud AS ID_Solicitud,
						s.RTN_Propietario,
						s.Nombre_Propietario,
						s.ID_Placa,
						s.ID_Marca,
						s.Anio,
						s.Modelo,
						s.Tipo_Vehiculo,
						s.ID_Color,
						s.Motor,
						s.Chasis,
						s.Sistema_Usuario,
						s.Sistema_Fecha,
						s.Combustible,
						s.VIN,
						s.Alto,
						s.Ancho,
						s.Largo,
						s.Capacidad_Carga,
						s.Peso_Unidad,
						s.Certificado_Operacion AS Numero_Certificado,
						s.Permiso_Explotacion AS Numero_Explotacion,
						s.ID_Placa_Antes_Replaqueo,
						s.Permiso_Especial AS Numero_PermisoEspecial
					FROM [IHTT_PREFORMA].[dbo].[TB_Vehiculo] s
					WHERE 
						s.ID_Formulario_Solicitud = :ID_Formulario_Solicitud
						AND s.Estado IN ('NORMAL', 'SALE')
				) AS source
				ON (target.ID_Solicitud = source.ID_Solicitud 
					AND ((target.Numero_Certificado = source.Numero_Certificado and source.Numero_Certificado != '')
						OR (target.Numero_PermisoEspecial = source.Numero_PermisoEspecial and source.Numero_PermisoEspecial != '')))
				WHEN MATCHED THEN
					UPDATE SET 
						target.RTN_Propietario = source.RTN_Propietario,
						target.Nombre_Propietario = source.Nombre_Propietario,
						target.ID_Placa = source.ID_Placa,
						target.ID_Marca = source.ID_Marca,
						target.Anio = source.Anio,
						target.Modelo = source.Modelo,
						target.Tipo_Vehiculo = source.Tipo_Vehiculo,
						target.ID_Color = source.ID_Color,
						target.Motor = source.Motor,
						target.Chasis = source.Chasis,
						target.Sistema_Usuario = source.Sistema_Usuario,
						target.Sistema_Fecha = source.Sistema_Fecha,
						target.Combustible = source.Combustible,
						target.VIN = source.VIN,
						target.Alto = source.Alto,
						target.Ancho = source.Ancho,
						target.Largo = source.Largo,
						target.Capacidad_Carga = source.Capacidad_Carga,
						target.Peso_Unidad = source.Peso_Unidad,
						target.ID_Placa_Antes_Replaqueo = source.ID_Placa_Antes_Replaqueo
				WHEN NOT MATCHED THEN
					INSERT ([ID_Solicitud], [RTN_Propietario], [Nombre_Propietario], 
							[ID_Placa], [ID_Marca], [Anio], [Modelo], [Tipo_Vehiculo], 
							[ID_Color], [Motor], [Chasis], [Sistema_Usuario], 
							[Sistema_Fecha], [Combustible], [VIN], [Alto], [Ancho], 
							[Largo], [Capacidad_Carga], [Peso_Unidad], [Numero_Certificado], 
							[Numero_Explotacion], [ID_Placa_Antes_Replaqueo], [Numero_PermisoEspecial])
					VALUES (source.ID_Solicitud, source.RTN_Propietario, source.Nombre_Propietario, 
							source.ID_Placa, source.ID_Marca, source.Anio, source.Modelo, 
							source.Tipo_Vehiculo, source.ID_Color, source.Motor, source.Chasis, 
							source.Sistema_Usuario, source.Sistema_Fecha, source.Combustible, 
							source.VIN, source.Alto, source.Ancho, source.Largo, source.Capacidad_Carga, 
							source.Peso_Unidad, source.Numero_Certificado, source.Numero_Explotacion, 
							source.ID_Placa_Antes_Replaqueo, source.Numero_PermisoEspecial);";
		$parametros = array(":ID_Formulario_Solicitud" => $RAM);
		return $this->update($query, $parametros);
	}	
	protected function saveIhttDbSolicitudVehiculoEntra($RAM) : string {
		$query = "MERGE INTO [IHTT_DB].[dbo].[TB_Solicitud_Vehiculo_Entra] AS target
					USING (
						SELECT  
							s.ID_Formulario_Solicitud AS ID_Solicitud,
							s.RTN_Propietario,
							s.Nombre_Propietario,
							s.ID_Placa,
							s.ID_Marca,
							s.Anio,
							s.Modelo,
							s.Tipo_Vehiculo,
							s.ID_Color,
							s.Motor,
							s.Chasis,
							s.Sistema_Usuario,
							s.Sistema_Fecha,
							s.Combustible,
							s.VIN,
							s.Alto,
							s.Ancho,
							s.Largo,
							s.Capacidad_Carga,
							s.Peso_Unidad,
							s.Certificado_Operacion AS Numero_Certificado,
							s.Permiso_Explotacion AS Numero_Explotacion,
							s.ID_Placa_Antes_Replaqueo,
							s.Permiso_Especial AS Numero_PermisoEspecial
						FROM [IHTT_PREFORMA].[dbo].[TB_Vehiculo] s
						WHERE 
							s.ID_Formulario_Solicitud = :ID_Formulario_Solicitud 
							AND s.Estado = 'ENTRA'
					) AS source
					ON (target.ID_Solicitud = source.ID_Solicitud 
						AND ((target.Numero_Certificado = source.Numero_Certificado and source.Numero_Certificado != '') 
							OR (target.Numero_PermisoEspecial = source.Numero_PermisoEspecial and source.Numero_PermisoEspecial != '')))
					WHEN MATCHED THEN
						UPDATE SET 
							target.RTN_Propietario = source.RTN_Propietario,
							target.Nombre_Propietario = source.Nombre_Propietario,
							target.ID_Placa = source.ID_Placa,
							target.ID_Marca = source.ID_Marca,
							target.Anio = source.Anio,
							target.Modelo = source.Modelo,
							target.Tipo_Vehiculo = source.Tipo_Vehiculo,
							target.ID_Color = source.ID_Color,
							target.Motor = source.Motor,
							target.Chasis = source.Chasis,
							target.Sistema_Usuario = source.Sistema_Usuario,
							target.Sistema_Fecha = source.Sistema_Fecha,
							target.Combustible = source.Combustible,
							target.VIN = source.VIN,
							target.Alto = source.Alto,
							target.Ancho = source.Ancho,
							target.Largo = source.Largo,
							target.Capacidad_Carga = source.Capacidad_Carga,
							target.Peso_Unidad = source.Peso_Unidad,
							target.ID_Placa_Antes_Replaqueo = source.ID_Placa_Antes_Replaqueo
					WHEN NOT MATCHED THEN
						INSERT ([ID_Solicitud], [RTN_Propietario], [Nombre_Propietario], 
								[ID_Placa], [ID_Marca], [Anio], [Modelo], [Tipo_Vehiculo], 
								[ID_Color], [Motor], [Chasis], [Sistema_Usuario], 
								[Sistema_Fecha], [Combustible], [VIN], [Alto], [Ancho], 
								[Largo], [Capacidad_Carga], [Peso_Unidad], [Numero_Certificado], 
								[Numero_Explotacion], [ID_Placa_Antes_Replaqueo], Numero_PermisoEspecial)
						VALUES (source.ID_Solicitud, source.RTN_Propietario, source.Nombre_Propietario, 
								source.ID_Placa, source.ID_Marca, source.Anio, source.Modelo, 
								source.Tipo_Vehiculo, source.ID_Color, source.Motor, source.Chasis, 
								source.Sistema_Usuario, source.Sistema_Fecha, source.Combustible, 
								source.VIN, source.Alto, source.Ancho, source.Largo, source.Capacidad_Carga, 
								source.Peso_Unidad, source.Numero_Certificado, source.Numero_Explotacion, 
								source.ID_Placa_Antes_Replaqueo, source.Numero_PermisoEspecial);";
		$parametros = array(":ID_Formulario_Solicitud" => $RAM);
		return $this->update($query, $parametros);
	}

	protected function saveIhttDbExpedientexApoderado($RAM) : string {
		$query = "MERGE INTO [IHTT_DB].[dbo].[TB_Expediente_X_Apoderado] AS target
					USING (
						SELECT  
							s.ID_Formulario_Solicitud AS ID_Solicitud,
							s.ID_Colegiacion AS ID_ColegiacionAPL,
							s.Nombre_Apoderado_Legal AS NombreApoderadoLega,
							s.Direccion_Apoderado_Legal AS OBS_Apoderado,
							s.Sistema_Fecha AS SistemaFecha,
							s.Telefono_Apoderado_Legal,
							s.Email_Apoderado_Legal,
							s.Ident_Apoderado_Legal,
							s.Sistema_Fecha,
							s.Sistema_Fecha AS Fecha_Cargo,
							NULL AS Fecha_Descargo,
							'APL-E-01' AS ID_Estado_Apl, 
							s.Sistema_Fecha AS SistemaUsuario
						FROM [IHTT_PREFORMA].[dbo].[TB_Apoderado_Legal] s where s.ID_Formulario_Solicitud = :ID_Formulario_Solicitud
					) AS source
					ON (target.ID_Solicitud = source.ID_Solicitud)
					WHEN MATCHED THEN
						UPDATE SET 
							target.ID_ColegiacionAPL = source.ID_ColegiacionAPL,
							target.NombreApoderadoLega = source.NombreApoderadoLega,
							target.OBS_Apoderado = source.OBS_Apoderado,
							target.Fecha_Cargo = source.Fecha_Cargo,
							target.Fecha_Descargo = source.Fecha_Descargo,
							target.ID_Estado_Apl = source.ID_Estado_Apl,
							target.SistemaUsuario = source.SistemaUsuario,
							target.SistemaFecha = source.SistemaFecha
					WHEN NOT MATCHED THEN
						INSERT ([ID_Solicitud], [ID_ColegiacionAPL], [NombreApoderadoLega], 
								[OBS_Apoderado], [Fecha_Cargo], [Fecha_Descargo], [ID_Estado_Apl], 
								[SistemaUsuario], [SistemaFecha])
						VALUES (source.ID_Solicitud, source.ID_ColegiacionAPL, source.NombreApoderadoLega, 
								source.OBS_Apoderado, source.Fecha_Cargo, source.Fecha_Descargo, 
								source.ID_Estado_Apl, source.SistemaUsuario, source.SistemaFecha);";
		$parametros = array(":ID_Formulario_Solicitud" => $RAM);
		return $this->update($query, $parametros);
	}	


	protected function saveIhttDbExpedientexTipoTramite($RAM) : string {
		$query = "MERGE INTO [IHTT_DB].[dbo].[TB_Expediente_X_Tipo_Tramite] AS target
				USING (
					SELECT 
						ID_Formulario_Solicitud,
						ID_Tramite,
						Observaciones,
						Sistema_Usuario,
						Sistema_Fecha,
						ID_Tipo_Categoria,
						Tipo_Servicio,
						N_Certificado,
						Permiso_Explotacion,
						N_Permiso_Especial
					FROM [IHTT_PREFORMA].[dbo].[TB_Solicitud]
					WHERE ID_Formulario_Solicitud = :ID_Formulario_Solicitud
				) AS source
				ON target.ID_Solicitud = source.ID_Formulario_Solicitud
				AND target.ID_Tramite = source.ID_Tramite
				AND (
				(target.Certificado_Operacion = source.N_Certificado and source.N_Certificado  != '')
				OR  
				(target.N_Permiso_Especial = source.N_Permiso_Especial and source.N_Permiso_Especial   != '')
				)
				WHEN NOT MATCHED BY SOURCE 
					AND target.ID_Solicitud = :ID_Solicitud THEN 
					DELETE
				WHEN NOT MATCHED BY TARGET THEN 
					INSERT (ID_Solicitud, ID_Tramite, OBS_Expediente, Estado_gea_tramite, 
							Observacion_gea_tramite, SistemaUsuario, SistemaFecha, 
							ID_Categoria_Subservicio, CategoriaSubservicio, ID_Servicios, 
							ServiciosNombre, DescripcionTramite, ID_Transporte, 
							Id_Tipo_Categoria, Certificado_Operacion, Permiso_Explotacion, 
							N_Permiso_Especial)
					VALUES (source.ID_Formulario_Solicitud, source.ID_Tramite, source.Observaciones, 
							'', 
							'',
							source.Sistema_Usuario, source.Sistema_Fecha, 
							source.ID_Tipo_Categoria, source.Tipo_Servicio, 
							'','', '', '', 
							source.ID_Tipo_Categoria, source.N_Certificado, 
							source.Permiso_Explotacion, source.N_Permiso_Especial);";
		$parametros = array(":ID_Formulario_Solicitud" => $RAM,":ID_Solicitud" => $RAM);
		return $this->update($query, $parametros);
	}	

	protected function saveIhttDbExpedienteMovimiento($RAM) : string {
		$row_ciudad = $this->getCiudad($_SESSION['ID_Usuario']);
		//*******************************************************************************************************************/
		//**Cuando ls fsl venga de Sps ubicar el expediente fisico en SPS, tomando la ciudad del FSL Codigo_Ciudad en      **/
		//**[IHTT_PREFORMA].[dbo].[TB_Solicitud], hacer cambio aqui                                                        **/
		//*******************************************************************************************************************/
		if ($row_ciudad[0]['Codigo_Ciudad'] == 'TGU') {
			$ID_AREA_VENTANILLA = 'IHTT08-02';
		}else if ($row_ciudad[0]['Codigo_Ciudad'] == 'SPS') {
			$ID_AREA_VENTANILLA = 'IHTT08-04';
		}else if ($row_ciudad[0]['Codigo_Ciudad'] == 'CHO') {
			$ID_AREA_VENTANILLA = 'IHTT08-06';
		}else {
			$ID_AREA_VENTANILLA = 'IHTT08-05';
		}
		$query = "INSERT INTO [IHTT_DB].[dbo].[TB_Expediente_Movimiento] 
				(ID_Solicitud, ID_Area_Actual, SistemaUsuario, Observaciones, Numero_Folio, 
				ID_Area_Iba, ID_Actividad, FechaRecibe, FechaEnvia, CodigoEstadoMovimiento, 
				ID_EmpleadoResponsable, ID_Usuario, SistemaFecha)
			SELECT :ID_Solicitud, :ID_Area_Actual, :SistemaUsuario, 'PARA REVISION OFICIAL JURIDICO', 
				0, '', 'ACT-01', SYSDATETIME(), NULL, 'TRABAJO', '', '', SYSDATETIME()
			WHERE NOT EXISTS (
				SELECT 1 FROM [IHTT_DB].[dbo].[TB_Expediente_Movimiento]
				WHERE ID_Solicitud = :ID_Solicitud AND ID_Actividad = 'ACT-01'
			);";
		$parametros = array(":ID_Solicitud" =>$RAM, ":ID_Area_Actual" =>$ID_AREA_VENTANILLA, ":SistemaUsuario"=>$_SESSION["user_name"]);
		return $this->insert($query, $parametros);
	}	

	protected function saveIhttDbExpedienteMovimientoInterno($RAM,$ID_Movimiento) : string {
		$query = "INSERT INTO [IHTT_DB].[dbo].[TB_Movimiento_Interno] 
				([ID_Movimiento], [Fecha_Entreda], [Fecha_Salida], [ID_Empleado], [Cod_Movimiento_Interno], 
				[ID_Usuario], [Fecha_Sistema], [Observacion], [ID_Actividad], [ID_AUTO])
			SELECT :ID_Movimiento, SYSDATETIME(), NULL, '', 'TRABAJO', '', SYSDATETIME(), 
				'PARA REVISION', 'ACT-29', NULL
			WHERE NOT EXISTS (
				SELECT 1 
				FROM [IHTT_DB].[dbo].[TB_Expediente_Movimiento]
				WHERE ID_Solicitud = :ID_Solicitud 
				AND ID_Actividad = 'ACT-01'
			);";
		$parametros = array(":ID_Movimiento" => $ID_Movimiento,":ID_Solicitud" => $RAM);
		return $this->insert($query, $parametros);
	}	

	protected function cerrarPreforma() {
		$this->db->beginTransaction();
		$respuestaPDFAvisodeCobroVentanillaApi = $this->PDFAvisodeCobroVentanillaApi($_POST["RAM"]);
		if (!isset($respuestaPDFAvisodeCobroVentanillaApi['error'])) {
			$respuestaGetEmpleado = $this->getEmpleado($respuestaPDFAvisodeCobroVentanillaApi['usuario_acepta']);
			if ($respuestaGetEmpleado != false) {
				$respuestaupdateEstadoPreforma = $this->updateEstadoPreforma($_POST["RAM"],$_POST['idEstado']);
				if (!isset($respuestaupdateEstadoPreforma['error'])) {
					$respuestasaveIhttDbSoliciante = $this->saveIhttDbSoliciante($_POST["RAM"],$respuestaPDFAvisodeCobroVentanillaApi['RTN_Solicitante']);
					if ($respuestasaveIhttDbSoliciante != false) {
						$respuestasaveIhttDbApoderadoLegal = $this->saveIhttDbApoderadoLegal($_POST["RAM"],$respuestaPDFAvisodeCobroVentanillaApi['ID_ColegiacionAPL']);
						if ($respuestasaveIhttDbApoderadoLegal != false) {
							$respuestasaveIhttDbSolicitanteRepresentanteLegal = $this->saveIhttDbSolicitanteRepresentanteLegal($_POST["RAM"]);
							if ($respuestasaveIhttDbSolicitanteRepresentanteLegal != false) {
								$respuestasaveIhttDbSolicitantexRepresentanteLegal = $this->saveIhttDbSolicitantexRepresentanteLegal($_POST["RAM"]);
								if ($respuestasaveIhttDbSolicitantexRepresentanteLegal != false) {
									$respuestasaveIhttDbExpedientes = $this->saveIhttDbExpedientes($_POST["RAM"]);
									if ($respuestasaveIhttDbExpedientes != false) {
										$respuestasaveIhttDbSolicitudVehiculoActual = $this->saveIhttDbSolicitudVehiculoActual($_POST["RAM"]);
										if ($respuestasaveIhttDbSolicitudVehiculoActual != false) {
											$respuestasaveIhttDbSolicitudVehiculoEntra = $this->saveIhttDbSolicitudVehiculoEntra($_POST["RAM"]);
											if ($respuestasaveIhttDbSolicitudVehiculoEntra != false) {
												$respuestasaveIhttDbExpedientexApoderado = $this->saveIhttDbExpedientexApoderado($_POST["RAM"]);
												if ($respuestasaveIhttDbExpedientexApoderado != false) {
													$respuestasaveIhttDbExpedientexTipoTramite = $this->saveIhttDbExpedientexTipoTramite($_POST["RAM"]);
													if ($respuestasaveIhttDbExpedientexTipoTramite != false) {
														$respuestasaveIhttDbExpedienteMovimiento = $this->saveIhttDbExpedienteMovimiento($_POST["RAM"]);
														if ($respuestasaveIhttDbExpedienteMovimiento != false) {
															$respuestasaveIhttDbExpedienteMovimientoInterno = $this->saveIhttDbExpedienteMovimientoInterno($_POST["RAM"],$respuestasaveIhttDbExpedienteMovimiento);
															if ($respuestasaveIhttDbExpedienteMovimientoInterno != false) {
																$respuesta['SOL'] =  $respuestaPDFAvisodeCobroVentanillaApi['formulario_encriptado'];
																$respuesta['SOL2'] = $_POST["RAM"];
																$respuesta['Cod_Usuario'] =  $respuestaPDFAvisodeCobroVentanillaApi['usuario_acepta'];
																$respuesta['Nombre_Usuario_Largo'] =  $respuestaGetEmpleado['0']['Apellidos'] . ' ' . $respuestaGetEmpleado['0']['Nombres'];
																$respuesta['Nombre_Usuario'] = $respuestaPDFAvisodeCobroVentanillaApi['usuario_acepta'];
																$respuesta['url_aviso'] = $respuestaPDFAvisodeCobroVentanillaApi['url_aviso'];
																$respuesta['numero_aviso'] = $respuestaPDFAvisodeCobroVentanillaApi['numero_aviso'];
																$respuesta['msg'] =  $_POST["RAM"];
																$respuesta['user_name'] =  $_SESSION["user_name"];
																$respuesta['ID_Usuario'] = $_SESSION["ID_Usuario"];
																echo json_encode($respuesta);
																$this->db->rollBack();
																//$this->db->commit();
															} else {
																$this->db->rollBack();
																echo json_encode(array("error" => 7012, "errorhead" => "SALVANDO MOVIMIENTO INTERNO", "errormsg" => 'ESTAMOS PRESENTANDO INCONVENIENTES PARA SALVAR LA INFORMACIÓN'));
															}																												
														} else {
															$this->db->rollBack();
															echo json_encode(array("error" => 7011, "errorhead" => "SALVANDO MOVIMIENTO", "errormsg" => 'ESTAMOS PRESENTANDO INCONVENIENTES PARA SALVAR LA INFORMACIÓN'));
														}												
													} else {
														$this->db->rollBack();
														echo json_encode(array("error" => 7010, "errorhead" => "SALVANDO TRAMITES EXPEDIENTE", "errormsg" => 'ESTAMOS PRESENTANDO INCONVENIENTES PARA SALVAR LA INFORMACIÓN'));
													}												
												} else {
													$this->db->rollBack();
													echo json_encode(array("error" => 7009, "errorhead" => "SALVANDO APODERADO EXPEDIENTE", "errormsg" => 'ESTAMOS PRESENTANDO INCONVENIENTES PARA SALVAR LA INFORMACIÓN'));
												}												
											} else {
												$this->db->rollBack();
												echo json_encode(array("error" => 7008, "errorhead" => "SALVANDO VEHICULO ENTRA", "errormsg" => 'ESTAMOS PRESENTANDO INCONVENIENTES PARA SALVAR LA INFORMACIÓN'));
											}
										} else {
											$this->db->rollBack();
											echo json_encode(array("error" => 7007, "errorhead" => "SALVANDO VEHICULO NORMAL/SALE", "errormsg" => 'ESTAMOS PRESENTANDO INCONVENIENTES PARA SALVAR LA INFORMACIÓN'));
										}
									} else {
										$this->db->rollBack();
										echo json_encode(array("error" => 7006, "errorhead" => "SALVANDO EXPEDIENTE", "errormsg" => 'ESTAMOS PRESENTANDO INCONVENIENTES PARA SALVAR EL EXPEDIENTE'));
									}
								} else {
									$this->db->rollBack();
									echo json_encode(array("error" => 7005, "errorhead" => "SALVANDO APODERADO LEGAL X SOLICITANTE", "errormsg" => 'ESTAMOS PRESENTANDO INCONVENIENTES PARA SALVAR EL APODERADO LEGAL POR SOLICITANTE'));
								}
							} else {
								$this->db->rollBack();
								echo json_encode(array("error" => 7004, "errorhead" => "SALVANDO APODERADO LEGAL", "errormsg" => 'ESTAMOS PRESENTANDO INCONVENIENTES PARA SALVAR EL APODERADO LEGAL SOLICITANTE'));
							}
						} else {
							$this->db->rollBack();
							echo json_encode(array("error" => 7003, "errorhead" => "SALVANDO APODERADO LEGAL", "errormsg" => 'ESTAMOS PRESENTANDO INCONVENIENTES PARA SALVAR EL APODERADO LEGAL DE EXPEDIENTE'));
						}
					} else {
						$this->db->rollBack();
						echo json_encode(array("error" => 7002, "errorhead" => "SALVANDO SOLICITANTE", "errormsg" => 'ESTAMOS PRESENTANDO INCONVENIENTES PARA SALVAR EL SOLICITANTE DE EXPEDIENTE'));
					}
				} else {
					$this->db->rollBack();
					echo json_encode(array("error" => 7001, "errorhead" => "ACTUALIZAICON DE ESTADO", "errormsg" => 'ESTAMOS PRESENTANDO INCONVENIENTES PARA ACTUALIZAR EL ESTADO DE LA RAM'));
				}
			} else {
				echo json_encode(array("error" => 7000, "errorhead" => "USUARIO ACEPTA", "errormsg" => 'NO SE ENCUENTRA EL USUARIO ACEPTA EN RRHH'));
			}
		} else {
			$this->db->rollBack();
			echo json_encode($respuestaPDFAvisodeCobroVentanillaApi);
		}
	}
	//***********************************************************************************************************************/
	//*Inicio                                                                                                               */
	//*rbthaofic@gmail.com 2024/12/05 Borrar Concesiones Preforma */
	//***********************************************************************************************************************/
	protected function deleteConcesionesPreforma()
	{
		$_POST["idConcesiones"] = json_decode($_POST["idConcesiones"], true);
		$_POST["RAM"] = json_decode($_POST["RAM"], true);
		$return = true;
		$this->db->beginTransaction();
		foreach ($_POST["idConcesiones"] as $Concesion) {
			$query = "DELETE FROM [IHTT_PREFORMA].[dbo].[TB_Solicitud] where ID_Formulario_Solicitud = :ID_Formulario_Solicitud and (N_Certificado = :N_Certificado or N_Permiso_Especial = :N_Permiso_Especial or Permiso_Explotacion = :Permiso_Explotacion)";
			$p = array(":ID_Formulario_Solicitud" => $_POST["RAM"], ":N_Certificado" => $Concesion, ":N_Permiso_Especial" => $Concesion, ":Permiso_Explotacion" => $Concesion);
			$return = $this->delete($query, $p);
			if ($return == false) {
				break;
			}
			$query = "DELETE FROM [IHTT_PREFORMA].[dbo].[TB_Vehiculo] where ID_Formulario_Solicitud = :ID_Formulario_Solicitud and (Certificado_Operacion = :Certificado_Operacion or Permiso_Especial = :Permiso_Especial or Permiso_Explotacion = :Permiso_Explotacion)";
			$p = array(":ID_Formulario_Solicitud" => $_POST["RAM"], ":Certificado_Operacion" => $Concesion, ":Permiso_Especial" => $Concesion, ":Permiso_Explotacion" => $Concesion);
			$return = $this->delete($query, $p);
			if ($return == false) {
				break;
			}
		}
		if ($return == false) {
			$this->db->rollBack();
			echo json_encode(array("error" => 2000, "errorhead" => "BORRANDO CONCESIONE(S)", "errormsg" => 'ERROR AL INTENTAR CONCESIONES, FAVOR CONTACTE AL ADMON DEL SISTEMA'));
		} else {
			$this->db->rollBack();
			//$this->db->commit();
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

		if (isset($_POST["ID_Unidad"])) {
			$_POST["ID_Unidad"] = json_decode($_POST["ID_Unidad"], true);
		}
		if (isset($_POST["ID_Unidad1"])) {
			$_POST["ID_Unidad1"] = json_decode($_POST["ID_Unidad1"], true);
		}

		$this->db->beginTransaction();
		$query = "DELETE FROM [IHTT_PREFORMA].[dbo].[TB_Solicitud] where ID = :ID";
		$p = array(":ID" => $_POST["idTramite"]);
		$return = $this->delete($query, $p);
		if ($return == false) {
			$this->db->rollBack();
			echo json_encode(array("error" => 2000, "errorhead" => "ELIMINAR TRAMITE PREFORMA", "errormsg" => 'ERROR AL INTENTAR ELIMINAR TRAMITE EN PREFORMA, FAVOR CONTACTE AL ADMON DEL SISTEMA'));
		} else {
			if (isset($_POST["ID_Unidad1"])) {
				$query = "UPDATE [IHTT_PREFORMA].[dbo].[TB_Vehiculo] SET ESTADO = 'NORMAL' where ID = :ID";
				$p = array(":ID" => intval($_POST["ID_Unidad"]));
				$return = $this->update($query, $p);
				if ($return == false) {
					$this->db->rollBack();
					echo json_encode(array("error" => 2001, "errorhead" => "ELIMINAR TRAMITE PREFORMA", "errormsg" => 'INCONVENIENTES AL INTENTAR ACTUALIZAR LA UNIDAD SALIENTE'));
				} else {			
					$query = "DELETE FROM [IHTT_PREFORMA].[dbo].[TB_Vehiculo] where ID = :ID";
					$p = array(":ID" => intval($_POST["ID_Unidad1"])); 
					$return = $this->delete($query, $p);
					if ($return == false) {
						$this->db->rollBack();
						echo json_encode(array("error" => 2002, "errorhead" => "ELIMINAR TRAMITE PREFORMA", "errormsg" => 'INCONVENIENTES AL INTENTAR ELIMINAR LA UNIDAD ENTRANTE'));
					} else {								
						//$this->db->commit();
						$this->db->rollBack();
						echo json_encode(['Borrado'  =>  True,'Adentro'  =>  True, 'ID_Unidad1' => intval($_POST["ID_Unidad1"]), 'ID_Unidad' => intval($_POST["ID_Unidad"])]);
					}
				}
			} else {
				//$this->db->commit();
				$this->db->rollBack();
				echo json_encode(['Borrado'  =>  True,'ID_Unidad1'  =>  'NO SET()', 'ID_TRAMITE' => $_POST["idTramite"]]);
			}
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
		$q = "SELECT es.DESC_Estado,sol.*,ald.ID_Municipio,mn.ID_Departamento 
		FROM [IHTT_Preforma].[dbo].[TB_Solicitante] sol, [IHTT_SELD].[dbo].[TB_Aldea] ald,[IHTT_SELD].[dbo].[TB_Municipio] mn,[IHTT_Preforma].[dbo].[TB_Estados] es,
		[IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Estados_User] eu
		where sol.ID_Formulario_Solicitud = :ID_Formulario_Solicitud and sol.ID_Aldea = ald.ID_Aldea and ald.ID_Municipio = mn.ID_Municipio and
		(sol.Estado_Formulario = es.ID_Estado or sol.Estado_Formulario = es.DESC_Estado) and eu.usuario = sol.Usuario_Creacion and
		sol.Estado_Formulario = eu.ID_Estado";
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
		(select top 1 ID_Placa from [IHTT_Preforma].[dbo].[TB_Vehiculo] veh where sol.ID_Formulario_Solicitud = veh.ID_Formulario_Solicitud and (sol.N_Certificado != '' and sol.N_Certificado = veh.Certificado_Operacion or sol.N_Permiso_Especial = veh.Permiso_Especial) and veh.Estado in ('NORMAL','SALE')) AS ID_Placa,
		(select top 1 ID_Placa from [IHTT_Preforma].[dbo].[TB_Vehiculo] veh where sol.ID_Formulario_Solicitud = veh.ID_Formulario_Solicitud and (sol.N_Certificado != '' and sol.N_Certificado = veh.Certificado_Operacion or sol.N_Permiso_Especial = veh.Permiso_Especial) and veh.Estado = 'ENTRA') AS ID_Placa1,
		CO.PermisoEspecialEncriptado,CO.PermisoEspecialEncriptado,CO.CertificadoEncriptado,CO.Permiso_Explotacion_Encriptado,CO.[Clase Servicio],
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
		order by sol.N_Certificado,sol.N_Permiso_Especial";
		$rows = $this->select($q, array(':ID_Formulario_Solicitud' => $_POST["RAM"]));
		$max = count($rows);
		for ($i=0; $i<$max; $i++) {
			$Permiso_Explotacion_Encriptado = '';
			while($Permiso_Explotacion_Encriptado != $rows[$i]["Permiso_Explotacion_Encriptado"]){        
				$Permiso_Explotacion_Encriptado = $rows[$i]["Permiso_Explotacion_Encriptado"];
				$CertificadoEncriptado = '';
				while($CertificadoEncriptado != $rows[$i]["CertificadoEncriptado"]){        
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
		case 
			when veh.[Permiso_Explotacion] != '' then RTRIM(veh.[Certificado_Operacion])
			else RTRIM(veh.[Permiso_Especial])
		end	AS Certificado_Operacion,				
		vl.[Clase Servicio],
		case 
			when vl.[Clase Servicio] = 'STEC' then 
			(SELECT DESC_Tipo_Vehiculo FROM 
			[IHTT_SGCERP].[DBO].[TB_Vehiculo_Transporte_Carga] vv,
			[IHTT_SGCERP].[DBO].[TB_Vehiculo_Transporte_Carga_x_Placa] pp,
			[IHTT_SGCERP].[DBO].[TB_Tipo_Vehiculo_Transporte_Carga] tt 
			where vv.ID_Vehiculo_Carga = pp.ID_Vehiculo_Carga and vv.ID_Tipo_Vehiculo_Carga = tt.ID_Tipo_Vehiculo_Carga and pp.Estado = 'ACTIVA' and vv.ID_Vehiculo_Carga = vl.ID_Vehiculo)
			when vl.[Clase Servicio] = 'STPC' then 
			(SELECT DESC_Tipo_Vehiculo FROM 
			[IHTT_SGCERP].[DBO].[TB_Vehiculo_Transporte_Carga] vv,
			[IHTT_SGCERP].[DBO].[TB_Vehiculo_Transporte_Carga_x_Placa] pp,
			[IHTT_SGCERP].[DBO].[TB_Tipo_Vehiculo_Transporte_Carga] tt 
			where vv.ID_Vehiculo_Carga = pp.ID_Vehiculo_Carga and vv.ID_Tipo_Vehiculo_Carga = tt.ID_Tipo_Vehiculo_Carga and pp.Estado = 'ACTIVA' and vv.ID_Vehiculo_Carga = vl.ID_Vehiculo)
			else 
			(SELECT DESC_Tipo_Vehiculo_Transporte_Pas FROM 
			[IHTT_SGCERP].[DBO].[TB_Vehiculo_Transporte_Pasajero] vv,
			[IHTT_SGCERP].[DBO].[TB_Vehiculo_Transporte_Pasajero_x_Placa] pp,
			[IHTT_SGCERP].[DBO].[TB_Tipo_Vehiculo_Transporte_Pasajero] tt 
			where vv.ID_Tipo_Vehiculo_Transporte_Pas = pp.ID_Vehiculo_Transporte and vv.ID_Tipo_Vehiculo_Transporte_Pas = tt.ID_Tipo_Vehiculo_Transporte_Pas and pp.Estado = 'ACTIVA' and vv.ID_Vehiculo_Transporte = vl.ID_Vehiculo)
			end	AS DESC_Tipo_Vehiculo,				
			veh.[ID_Formulario_Solicitud],
		veh.[ID],
		veh.[RTN_Propietario],
		veh.[Nombre_Propietario],
		veh.[ID_Placa],
		concat(veh.[ID_Marca],' => ',mar.Desc_Marca) as [Marca],
		veh.[Anio],
		veh.[Modelo],
		veh.[Tipo_Vehiculo],
		concat(veh.[ID_Color],' => ',col.Desc_Color) as [Color],
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
			JOIN
				[IHTT_SGCERP].[dbo].[v_Listado_General] vl on vl.N_Certificado = 
				(select top 1
				case 
				when soli.[Permiso_Explotacion] != '' then RTRIM(soli.[N_Certificado])
				else RTRIM(soli.[N_Permiso_Especial])
				end 
				from [IHTT_PREFORMA].[dbo].[TB_Solicitud] soli where soli.ID_Formulario_Solicitud = veh.ID_Formulario_Solicitud)	
			WHERE veh.ID_Formulario_Solicitud = :ID_Formulario_Solicitud order by veh.Certificado_Operacion,veh.Permiso_Especial,veh.Estado DESC";

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

	protected function testFileExists() {
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
		$q = "SELECT 	
		case 
			when veh.[Permiso_Explotacion] != '' then RTRIM(veh.[Certificado_Operacion])
			else RTRIM(veh.[Permiso_Especial])
		end	AS Certificado_Operacion,				
		vl.[Clase Servicio],
		case 
			when vl.[Clase Servicio] = 'STEC' then 
			(SELECT DESC_Tipo_Vehiculo FROM 
			[IHTT_SGCERP].[DBO].[TB_Vehiculo_Transporte_Carga] vv,
			[IHTT_SGCERP].[DBO].[TB_Vehiculo_Transporte_Carga_x_Placa] pp,
			[IHTT_SGCERP].[DBO].[TB_Tipo_Vehiculo_Transporte_Carga] tt 
			where vv.ID_Vehiculo_Carga = pp.ID_Vehiculo_Carga and vv.ID_Tipo_Vehiculo_Carga = tt.ID_Tipo_Vehiculo_Carga and pp.Estado = 'ACTIVA' and vv.ID_Vehiculo_Carga = vl.ID_Vehiculo)
			when vl.[Clase Servicio] = 'STPC' then 
			(SELECT DESC_Tipo_Vehiculo FROM 
			[IHTT_SGCERP].[DBO].[TB_Vehiculo_Transporte_Carga] vv,
			[IHTT_SGCERP].[DBO].[TB_Vehiculo_Transporte_Carga_x_Placa] pp,
			[IHTT_SGCERP].[DBO].[TB_Tipo_Vehiculo_Transporte_Carga] tt 
			where vv.ID_Vehiculo_Carga = pp.ID_Vehiculo_Carga and vv.ID_Tipo_Vehiculo_Carga = tt.ID_Tipo_Vehiculo_Carga and pp.Estado = 'ACTIVA' and vv.ID_Vehiculo_Carga = vl.ID_Vehiculo)
			else 
			(SELECT DESC_Tipo_Vehiculo_Transporte_Pas FROM 
			[IHTT_SGCERP].[DBO].[TB_Vehiculo_Transporte_Pasajero] vv,
			[IHTT_SGCERP].[DBO].[TB_Vehiculo_Transporte_Pasajero_x_Placa] pp,
			[IHTT_SGCERP].[DBO].[TB_Tipo_Vehiculo_Transporte_Pasajero] tt 
			where vv.ID_Tipo_Vehiculo_Transporte_Pas = pp.ID_Vehiculo_Transporte and vv.ID_Tipo_Vehiculo_Transporte_Pas = tt.ID_Tipo_Vehiculo_Transporte_Pas and pp.Estado = 'ACTIVA' and vv.ID_Vehiculo_Transporte = vl.ID_Vehiculo)
			end	AS DESC_Tipo_Vehiculo,				
			veh.[ID_Formulario_Solicitud],
		veh.[ID],
		veh.[RTN_Propietario],
		veh.[Nombre_Propietario],
		veh.[ID_Placa],
		veh.[ID_Marca],
		concat(veh.[ID_Marca],' => ',mar.Desc_Marca) as [Marca],
		veh.[Anio],
		veh.[Modelo],
		veh.[Tipo_Vehiculo],
		veh.[ID_Color],
		concat(veh.[ID_Color],' => ',col.Desc_Color) as [Color],
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
			JOIN
				[IHTT_SGCERP].[dbo].[v_Listado_General] vl on vl.N_Certificado = 
				(select top 1
				case 
				when soli.[Permiso_Explotacion] != '' then RTRIM(soli.[N_Certificado])
				else RTRIM(soli.[N_Permiso_Especial])
				end 
				from [IHTT_PREFORMA].[dbo].[TB_Solicitud] soli where soli.ID_Formulario_Solicitud = veh.ID_Formulario_Solicitud)	
			where [ID_Formulario_Solicitud] = :ID_Formulario_Solicitud and ([Certificado_Operacion] = :Certificado_Operacion or [Permiso_Especial] = :Permiso_Especial) ORDER BY veh.[Estado] DESC";
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
							 class="form-check-input tramiteschk" 
							 id="' . $row['ID_CHECK'] . '" 
							 type="checkbox" 
							 name="tramites[]" 
							 value="' . $row["ID_Tramite"] . '">
					</div>
					<div id="descripcion_' . $row["ID_Tramite"] . '" class="col-md-9">' . $row["descripcion_larga"] . '</div>
					<div class="col-md-2">
					  <input style="display:none; text-transform: uppercase;" 
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
					$html = $html . '<div class="row border border-info" id="row_tramite_'  . $row['Acronimo_Tramite'] . '_' . $row['Acronimo_Clase']  .  '"><div class="col-md-1"><input data-monto="' . $row['Monto'] . '" class="form-check-input tramiteschk" ' .  " id=" . $row['ID_CHECK']  .  ' type="checkbox" name="tramites[]" value="' . $row["ID_Tramite"] . '"></div><div id="descripcion_' . $row["ID_Tramite"] . '" class="col-md-9">' . $row["descripcion_larga"] . '</div><div class="col-md-2">&nbsp;</div></div>';
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
						   class="form-check-input tramiteschk" 
						   id="' . $row['ID_CHECK'] . '" 
						   type="checkbox" 
						   name="tramites[]" 
						   value="' . $row["ID_Tramite"] . '">
				  </div>
				  <div id="descripcion_' . $row["ID_Tramite"] . '" class="col-md-3">
					' . $row["descripcion_larga"] . '
				  </div>
				  <div class="col-md-2">
					<input style="display:none; text-transform: uppercase;" 
						   id="concesion_tramite_placa_' . $row['Acronimo_Clase'] . '" 
						   title="La placa debe contener los primeros 3 dígitos alfa y los últimos 4 numéricos, máximo 7 caracteres" 
						   pattern="^[A-Z]{3}\d{4}$" 
						   placeholder="PLACA" 
						   class="form-control form-control-sm test-controls" 
						   minlength="7" 
						   maxlength="7">
				  </div>';
				} else {
					$html = $html . '<div class="col-md-1"><input data-monto="' . $row['Monto'] . '" class="form-check-input tramiteschk" ' .  " id=" . $row['ID_CHECK']  .  ' type="checkbox" name="tramites[]" value="' . $row["ID_Tramite"] . '"></div><div id="descripcion_' . $row["ID_Tramite"] . '" class="col-md-3">' . $row["descripcion_larga"] . '</div><div class="col-md-2">&nbsp;</div>';
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
        ELSE 'checked=true'
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
		ORDER BY T.ID_Tipo_Tramite, TR.ID_Clase_Tramite;";
		$bandera = 1;
		if ($bandera == 1) {
			$html = '<div class="row border border-primary d-flex justify-content-center"><div class="col-md-12"><strong>LISTADO DE TRAMITES</strong></div></div>';
			$html = $html . '<div class="row"><div class="col-md-1"></div><div class="col-md-9"><strong>TRAMITE</strong></div><div class="col-md-2"><strong>PLACA</strong></div></div>';
			$rows = ($this->select($q, array(':ID_Formulario_Solicitud' => $RAM, ':N_Certificado' => $idConcesion, ':N_Permiso_Especial' => $idConcesion, ':ID_Categoria' => $ID_Categoria)));
			foreach ($rows as $row) {
				if ($row['Acronimo_Clase'] ==  'CU' || $row['Acronimo_Clase'] ==  'CL') {
					$display = 'display:' .($row['Checked'] == 'checked') ? 'flex;' : 'none;';
					$html = $html . '<div class="row border border-info" id="row_tramite_' . $row['Acronimo_Tramite'] . '_' . $row['Acronimo_Clase'] . '">
					<div class="col-md-1">
					  <input ' . $row['Checked'] . ' data-iddb="' . $row['ID'] . '" data-monto="' . $row['Monto'] . '" 
							 class="form-check-input tramiteschk" 
							 id="' . $row['ID_CHECK'] . '" 
							 type="checkbox" 
							 name="tramites[]" 
							 value="' . $row["ID_Tramite"] . '">
					</div>
					<div id="descripcion_' . $row["ID_Tramite"] . '" class="col-md-9">' . $row["descripcion_larga"] . '</div>
					<div class="col-md-2">
					  <input style="display:none; text-transform: uppercase;" 
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
					$html = $html . '<div class="row border border-info" id="row_tramite_'  . $row['Acronimo_Tramite'] . '_' . $row['Acronimo_Clase']  .  '"><div class="col-md-1"><input  ' . $row['Checked']  . ' data-iddb="' . $row['ID'] . '"  data-monto="' . $row['Monto'] . '" class="form-check-input tramiteschk" ' .  " id=" . $row['ID_CHECK']  .  ' type="checkbox" name="tramites[]" value="' . $row["ID_Tramite"] . '"></div><div id="descripcion_' . $row["ID_Tramite"] . '" class="col-md-9">' . $row["descripcion_larga"] . '</div><div class="col-md-2">&nbsp;</div></div>';
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
						   class="form-check-input tramiteschk" 
						   id="' . $row['ID_CHECK'] . '" 
						   type="checkbox" 
						   name="tramites[]" 
						   value="' . $row["ID_Tramite"] . '">
				  </div>
				  <div id="descripcion_' . $row["ID_Tramite"] . '" class="col-md-3">
					' . $row["descripcion_larga"] . '
				  </div>
				  <div class="col-md-2">
					<input style="display:none; text-transform: uppercase;" 
						   id="concesion_tramite_placa_' . $row['Acronimo_Clase'] . '" 
						   title="La placa debe contener los primeros 3 dígitos alfa y los últimos 4 numéricos, máximo 7 caracteres" 
						   pattern="^[A-Z]{3}\d{4}$" 
						   placeholder="PLACA" 
						   class="form-control form-control-sm test-controls" 
						   minlength="7" 
						   maxlength="7">
				  </div>';
				} else {
					$html = $html . '<div class="col-md-1"><input ' . $row['Checked']  . ' data-iddb="' . $row['ID'] .  '" data-monto="' . $row['Monto'] . '" class="form-check-input tramiteschk" ' .  " id=" . $row['ID_CHECK']  .  ' type="checkbox" name="tramites[]" value="' . $row["ID_Tramite"] . '"></div><div id="descripcion_' . $row["ID_Tramite"] . '" class="col-md-3">' . $row["descripcion_larga"] . '</div><div class="col-md-2">&nbsp;</div>';
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
				logErr($txt, '../logs/logs.txt');
			} else {
				$respuesta[1]['data'] = $color->fetch();
			}
		} catch (\Throwable $th) {
			$respuesta[0]['msg'] = $th->getMessage();
			$respuesta[0]['errorcode'] = -1;
			$respuesta[0]['error'] = true;
			$txt = date('Y m d h:i:s') . '	' . 'getColor.php catch ' . $query_rs_color . ' ERROR ' . $th->getMessage();
			logErr($txt, '../logs/logs.txt');
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
				logErr($txt, '../logs/logs.txt');
			} else {
				$respuesta[1]['data'] = $marca->fetch();
			}
		} catch (\Throwable $th) {
			$respuesta[0]['msg'] = $th->getMessage();
			$respuesta[0]['errorcode'] = -1;
			$respuesta[0]['error'] = true;
			$txt = date('Y m d h:i:s') . '	' . 'getMarca.php catch ' . $query_rs_Marca . ' ERROR ' . $th->getMessage();
			logErr($txt, '../logs/logs.txt');
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
		$vehiculo = json_decode($this->file_contents($_SESSION["appcfg_Dominio_Raiz"] . ":184/api/Unidad/ConsultarDatosIP/" . strtoupper($placa)));
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
				$vehiculo->cargaUtil->Preformas = $this->validarEnPreforma($vehiculo->cargaUtil->placa, $vehiculo->cargaUtil->placaAnterior, $_POST["Concesion"], $_POST["RAM"]);
				$vehiculo->cargaUtil->Placas = $this->validarPlaca($vehiculo->cargaUtil->placa, $vehiculo->cargaUtil->placaAnterior, $_POST["Concesion"]);
			}
		}
		if (!isset($vehiculo->codigo)) {
			$txt = date('Y m d h:i:s') . 'Api_Ram.php	getDatosUnidadDesdeIP():; ' . $_SESSION['usuario'] . '; - Error ;  NO RESPONDIO CONSULTA AL IP: ' . $_SESSION["appcfg_Dominio_Raiz"] . ':184/api/Unidad/ConsultarDatosIP/' . $placa;
			logErr($txt, '../logs/logs-ip.txt');
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
		$query = "SELECT MUL.Multa,Avi.CodigoAvisoCobro,MUL.Fecha,MUL.Propietario,MUL.Identidad_RTN,MUL.Certificado,MUL.Placa,convert(numeric(10,2), MUL.Total) as Total 
		FROM [IHTT_MULTAS].[dbo].[V_Multas_IHTT_DGT] MUL,[IHTT_Webservice].[dbo].[TB_AvisoCobroEnc] Avi
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
		if ($row != null) {
			$titulos = [
				0 => 'ID MULTA',
				1 => 'AVISO COBRO',
				2  => 'FECHA MULTA',
				3 => 'PROPIETARIO UNIDAD',
				4 => 'IDENTIFICACIÓN',
				5 => 'CONCESION',
				6 => 'PLACA',
				7 => 'MONTO',
				'Multa' => 'ID MULTA',
				'AVISO COBRO' => 'AVISO COBRO',
				'FECHA MULTA'  => 'FECHA MULTA',
				'PROPIETARIO UNIDAD' => 'PROPIETARIO UNIDAD',
				'IDENTIFICACION' => 'IDENTIFICACIÓN',
				'CONCESIONARIO' => 'CONCESIONARIO',
				'PLACA' => 'PLACA',
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

		$query = "select * from IHTT_SGCERP.dbo.v_Listado_General WHERE N_Certificado = :N_Certificado";
		$p = array(":N_Certificado" => $_POST["Concesion"]);
		$data = $this->select($query, $p);
		$datos[0] = count($data);

		if ($datos[0] > 0 and $data[0]['RTN_Concesionario'] == $_POST["RTN_Concesionario"]) {
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
						$data[0]["Unidad"][0]['Preforma'] = $this->validarEnPreforma($vehiculo->cargaUtil->placa, $vehiculo->cargaUtil->placaAnterior, $_POST["Concesion"],$_POST['RAM']);
						$data[0]["Unidad"][0]['Placas'] = $this->validarPlaca($vehiculo->cargaUtil->placa, $vehiculo->cargaUtil->placaAnterior, $_POST["Concesion"]);
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
							$data[0]["Unidad"][0]['Preforma'] = $this->validarEnPreforma($vehiculo->cargaUtil->placa, $vehiculo->cargaUtil->placaAnterior, $_POST["Concesion"],$_POST['RAM']);
							$data[0]["Unidad"][0]['Placas'] = $this->validarPlaca($vehiculo->cargaUtil->placa, $vehiculo->cargaUtil->placaAnterior, $_POST["Concesion"]);
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
								$data[0]["Unidad"][0]['Preforma'] = $this->validarEnPreforma($vehiculo->cargaUtil->placa, $vehiculo->cargaUtil->placaAnterior, $_POST["Concesion"],$_POST['RAM']);
								$data[0]["Unidad"][0]['Placas'] = $this->validarPlaca($vehiculo->cargaUtil->placa, $vehiculo->cargaUtil->placaAnterior, $_POST["Concesion"]);
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
									$data[0]["Unidad"][0]['Preforma'] = $this->validarEnPreforma($vehiculo->cargaUtil->placa, $vehiculo->cargaUtil->placaAnterior, $_POST["Concesion"],$_POST['RAM']);
									$data[0]["Unidad"][0]['Placas'] = $this->validarPlaca($vehiculo->cargaUtil->placa, $vehiculo->cargaUtil->placaAnterior, $_POST["Concesion"]);
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
		} else {
			if ($datos[0] > 0) {
				$data = (array("error" => 40001, "errorhead" => 'ASIGNADA A OTRO', "errormsg" => "SE EN ENCONTRO LA CONCESIÓN NO. " . $_POST["Concesion"] . " PERO ESTA ASIGNADA A OTRO CONCESIONARIO CON NÚMERO DE RTN: " . $data[0]['RTN_Concesionario']));
			} else {
				$data = (array("error" => 40002, "errorhead" => 'CONCESION NO ENCONTRADA', "errormsg" => "NO EN ENCONTRO LA CONCESIÓN NO. " . $_POST["Concesion"]));
			}
		}
		$datos[1] = $data;
		echo json_encode($datos);

	}

	//*************************************************************************************/
	//* FUNCION PARA RECUPERAR EL SOLICITANTE
	//*************************************************************************************/
	protected function getConcesionPreforma()
	{

		$query = "select * from IHTT_SGCERP.dbo.v_Listado_General WHERE N_Certificado = :N_Certificado order by Sistema_Fecha";
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
	//* Funcion para Crear Carpeta de RAM en Documentos
	//*******************************************************************************************************************/
	protected function crearCarpeta($RAM)
	{
		$mover = true;
		try {
			$directory = "Documentos/" . $RAM;
			if (!is_dir($directory)) {
				if (!mkdir($directory, 0777, true)) {
					$response['msgLog'] = "Fallo la creación del directorio: $directory";
					$response['msg'] = 'Algo inesperado sucedio creando el directorio';
					$response['error'] = true;
					$txt = date('Y m d h:i:s') . 'Api_Ram.php	crearCarpeta:; ' . $_SESSION['usuario'] . '; - Error ; ' . "Fallo la creación del directorio: $directory";
					logErr($txt, '../logs/logs.txt');
					$mover = false;
					if (!isset($_POST["echo"])) {
						return json_encode(array("error" => 11000, "errorhead" => "CREACIÓN DE DIRECTORIO", "errormsg" => 'NO SE PUEDE CREAR EL DIRECTORIO: ' . $directory));
					} else {
						echo json_encode(array("error" => 11000, "errorhead" => "CREACIÓN DE DIRECTORIO", "errormsg" => 'NO SE PUEDE CREAR EL DIRECTORIO: ' . $directory));
					}
				} else {
					if (!isset($_POST["echo"])) {
						return true;
					} else {
						echo true;
					}
				}
			}
		} catch (Exception $e) {
			// Handle the exception
			$response['msgLog'] = 'Caught Exception: ' .  $e->getMessage() . "\n";
			$response['msg'] = 'Algo inesperado sucedio creando el directorio';
			$response['error'] = false;
			$txt = date('Y m d h:i:s') . 'Api_Ram.php	crearCarpeta:; ' . $_SESSION['usuario'] . '; - Error ; ' . $e->getMessage() . "\n";
			logErr($txt, '../logs/logs.txt');
			if (!isset($_POST["echo"])) {
				return json_encode(array("error" => 11001, "errorhead" => "CATCH MOVIMIENTO ARCHIVO CREACION DE DIRECTORIO", "errormsg" => 'ERROR DESCONOCIDO AL TRATAR DE CREAR DIRECTORIO: ' . $directory));
			} else {
				echo json_encode(array("error" => 11001, "errorhead" => "CATCH MOVIMIENTO ARCHIVO CREACION DE DIRECTORIO", "errormsg" => 'ERROR DESCONOCIDO AL TRATAR DE CREAR DIRECTORIO: ' . $directory));
			}
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
					$txt = date('Y m d h:i:s') . 'Api_Ram.php	crearCarpeta:; ' . $_SESSION['usuario'] . '; - Error ; ' . "Fallo la creación del directorio: $directory";
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
	//*  pendientes en preforma al igual valida que la concesion no este con               */
	//**************************************************************************************/
	protected function validarEnPreforma($ID_Placa, $ID_Placa_Antes_Replaqueo, $Concesion, $RAM=''): mixed
	{
		//**************************************************************************************/
		//*  CAMBIOS HECHOS RBTHAOFIC@GMAIL.COM 2022/11/17                                    */
		//*  vALIDAR QUE FECHA ACTUAL SEA MENOR O IGUAL A LA FECHA DE VENCIMIENTO             */
		//*  CAMBIOS HECHOS RBTHAOFIC@GMAIL.COM 2023/12/14                                    */
		//*  VALIDAR QUE [N_Certificado] != :N_Certificado y                                  */
		//*  FECHA ACTUAL SEA MAYOR O IGUAL A LA FECHA DE VENCIMIENTO                         */
		//************************************************************************************/
		$query = "SELECT DISTINCT S.ID_Formulario_Solicitud,S.RTN_Solicitante,S.Nombre_Solicitante, L.N_Certificado,L.Permiso_Explotacion,l.N_Permiso_Especial,V.ID_Placa, v.ID_Placa_Antes_Replaqueo,
				S.Sistema_Fecha,A.Nombre_Apoderado_Legal,ID_Colegiacion 
				FROM [IHTT_PREFORMA].[dbo].[TB_SOLICITANTE] S, [IHTT_PREFORMA].[dbo].[TB_Apoderado_Legal] A,[IHTT_PREFORMA].[dbo].[TB_SOLICITUD] L, [IHTT_PREFORMA].[dbo].[TB_Vehiculo] V
				WHERE 
				(
				(
				S.ID_Formulario_Solicitud != :RAM AND
				S.Estado_Formulario in ('IDE-1','IDE-7') AND
				S.ID_Formulario_Solicitud = A.ID_Formulario_Solicitud AND 
				S.ID_Formulario_Solicitud = L.ID_Formulario_Solicitud AND 
				(
				L.ID_Formulario_Solicitud = V.ID_Formulario_Solicitud AND 
				(
					(L.N_Certificado = V.Certificado_Operacion and L.N_Certificado != '') OR 
					(L.N_Permiso_Especial = V.Permiso_Especial and L.N_Permiso_Especial != '')
				) 
				) AND 
					V.Estado IN ('NORMAL','ENTRA')
				) AND
				(

					(
						(L.N_Certificado = :N_Certificado and L.N_Certificado != '')  OR 
						(L.N_Permiso_Especial = :N_Permiso_Especial and L.N_Permiso_Especial != '')
					) 
					or 
					(V.ID_Placa = :ID_Placa or isnull(v.ID_Placa_Antes_Replaqueo,'') = :ID_Placa_Antes_Replaqueo)
				)
				)
				or
				(
				S.ID_Formulario_Solicitud = :RAM1 AND
				S.Estado_Formulario in ('IDE-1','IDE-7') AND
				S.ID_Formulario_Solicitud = A.ID_Formulario_Solicitud AND 
				S.ID_Formulario_Solicitud = L.ID_Formulario_Solicitud AND 
				L.ID_Formulario_Solicitud = V.ID_Formulario_Solicitud AND 
				(V.ID_Placa = :ID_Placa1 or isnull(v.ID_Placa_Antes_Replaqueo,'XXXXXXX') = :ID_Placa_Antes_Replaqueo1) and
				((L.N_Certificado = V.Certificado_Operacion and L.N_Certificado != '') OR (L.N_Permiso_Especial = V.Permiso_Especial and L.N_Permiso_Especial != '')) AND 
				V.Estado IN ('NORMAL','ENTRA') AND
				((L.N_Certificado != '' and L.N_Certificado != :N_Certificado1)  OR (L.N_Permiso_Especial != '' and L.N_Permiso_Especial != :N_Permiso_Especial1)))
				order by S.ID_Formulario_Solicitud,v.ID_Placa";
				$parametros = array(":RAM" => $RAM,
									":N_Certificado" => $Concesion,
									":N_Permiso_Especial" => $Concesion,
									":ID_Placa" => $ID_Placa,
									":ID_Placa_Antes_Replaqueo" => $ID_Placa_Antes_Replaqueo,
							 		":RAM1" => $RAM,
									":ID_Placa1" => $ID_Placa,
									":ID_Placa_Antes_Replaqueo1" => $ID_Placa_Antes_Replaqueo,
									":N_Certificado1" => $Concesion,
									":N_Permiso_Especial1" => $Concesion);
		$row = $this->select($query, $parametros);
		//print_r($row); echo '<br/>';
		//echo $ID_Placa;  echo '<br/>';
		//echo $ID_Placa_Antes_Replaqueo;  echo '<br/>';
		//echo $Concesion;  echo '<br/>';
		//echo $RAM;  echo '<br/>';
		if ($row != false) {
			$titulos = [
				0 => 'RAM',
				1 => 'RTN SOLI',
				2 => 'NOMBRE SOLI',
				3  => 'CERTIFICADO OPERAC',
				4 => 'PER EXP',
				5 => 'PER ESPECIAL',
				6 => 'PLACA ACT',
				7 => 'PLACA ANT',
				8 => 'FECHA SOL',
				9 => 'APODERADO',
				10 => 'CAH No.',
				'RAM' => 'RAM',
				'RTN SOLI' => 'RTN SOLI',
				'NOMBRE SOLI' => 'NOMBRE SOLI',
				'CERTIFICADO OPERAC'  => 'CERTIFICADO OPERAC',
				'PER EXP' => 'PER EXP',
				'PER ESPECIAL' => 'PER ESPECIAL',
				'PLACA ACT' => 'PLACA ACT',
				'PLACA ANT' => 'PLACA ANT',
				'FECHA SOL' => 'FECHA SOL',
				'APODERADO' => 'APODERADO',
				'CAH No.' => 'CAH No.',
			];
			$row[count($row) + 1] = $titulos;
		}
		return $row;
	}

	//**************************************************************************************/
	//*  Valida que la placa no este asignada a una concesion vigente diferente de         */
	//*  de la que estamos tratando de salvar o que la concesion vigente  no tenga un     */
	//*  concesion lista para impresion                                                    */
	//**************************************************************************************/
	protected function validarPlaca($placa, $placa_anterior, $concesion): mixed
	{
		$query = "SELECT N_certificado as Concesion,N_Permiso_Explotacion,ID_Expediente,RTN_Concesionario,NombreSolicitante,ID_Placa FROM [IHTT_SGCERP].[dbo].[v_Validacion_Placas] 
		WHERE ([N_Certificado] = :Concesion and ID_Estado IN ('ES-02','ES-04')  and Fecha_Expiracion >= CONVERT(CHAR(8), GETDATE(), 112)) or
		([N_Certificado] != :Concesion1 and Fecha_Expiracion >= CONVERT(CHAR(8), GETDATE(), 112) AND ID_Estado IN ('ES-02','ES-04') AND (ID_Placa = :Placa or ID_Placa = :Placa_Anterior))";
		$parametros = array(":Concesion" => $concesion,":Concesion1" => $concesion, ":Placa" => $placa, ":Placa_Anterior" => $placa_anterior);
		$row = $this->select($query, $parametros);
		if ($row != false) {
			$titulos = [
				0 => 'CONCESION',
				1 => 'PER EXPLOTACIÓN',
				2 => 'ID EXPEDIENTE',
				3 => 'RTN CONCESIONARIO',
				4 => 'NOMBRE CONCESIONARIO',
				'PLACA' => 'PLACA',
				'CONCESION' => 'CONCESION',
				'PER EXPLOTACIÓN' => 'PER EXPLOTACIÓN',
				'ID EXPEDIENTE' => 'ID EXPEDIENTE',
				'RTN CONCESIONARIO' => 'RTN CONCESIONARIO',
				'NOMBRE CONCESIONARIO' => 'NOMBRE CONCESIONARIO'
			];
			$row[count($row) + 1] = $titulos;
		}
		return $row;

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
	protected function updateEstadoPreforma($RAM,$idEstado)
	{
		if (isset($_POST["echo"])) {
			$_POST["echo"] = json_decode($_POST["echo"]);
			$this->db->beginTransaction();
		}


		$query = "UPDATE [IHTT_PREFORMA].[DBO].[TB_SOLICITANTE] SET Estado_Formulario = :Estado_Formulario WHERE ID_FORMULARIO_SOLICITUD = :ID_FORMULARIO_SOLICITUD";
		$p = array(":Estado_Formulario" => $idEstado, ":ID_FORMULARIO_SOLICITUD" => $RAM);
		$estadoOk = $this->update($query, $p);
		if ($estadoOk == true) {

			if ($idEstado == 'IDE-3') {
				$eventox = 'CANCELADO';
				$etapax = 4;
			} else {
				if ($idEstado == 'IDE-4') {
					$eventox = 'INADMITIDO';
					$etapax = 5;
				} else {
					if ($idEstado == 'IDE-1') {
						$eventox = 'INICIO';
						$etapax = 1;
					}
				}	
			}

			$saveBitacoraOk = $this->saveBitacora($_POST["RAM"], $eventox , $etapax);
			
			if ($saveBitacoraOk != false) {
				if (!isset($_POST["echo"])) {
					return $saveBitacoraOk;
				} else {
					$this->db->commit();
					echo json_encode($saveBitacoraOk);
				}
			} else {
				if (!isset($_POST["echo"])) {
					return json_encode(array("error" => 9002, "errorhead" => 'ADVERTENCIA', "errormsg" => 'ESTAMOS PRESENTANDO INCONVENIENTES TEMPORALES AL MOMENTO DE SALVAR LA BITACORA DE LA PREFORMA'));
				} else {
					echo json_encode(array("error" => 9002, "errorhead" => 'ADVERTENCIA', "errormsg" => 'ESTAMOS PRESENTANDO INCONVENIENTES TEMPORALES AL MOMENTO DE SALVAR LA BITACORA DE LA PREFORMA'));
				}
			}
		} else {
			if (!isset($_POST["echo"])) {
				return json_encode(array("error" => 9001, "errorhead" => 'ADVERTENCIA', "errormsg" => 'ESTAMOS PRESENTANDO INCONVENIENTES TEMPORALES AL MOMENTO DE CAMBIAR EL ESTADO DE LA PREFORMA'));
			} else {
				echo json_encode(array("error" => 9001, "errorhead" => 'ADVERTENCIA', "errormsg" => 'ESTAMOS PRESENTANDO INCONVENIENTES TEMPORALES AL MOMENTO DE CAMBIAR EL ESTADO DE LA PREFORMA'));
			}
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
			$prefijo = str_replace('date("Y")', date('Y'), trim($record['prefijo']));
			$prefijo = str_replace('date("m")', date('m'), trim($prefijo));
			$prefijo = str_replace('date("d")', date('d'), trim($prefijo));
			$prefijo = str_replace('date(Y)', date('Y'), trim($record['prefijo']));
			$prefijo = str_replace('date(m)', date('m'), trim($prefijo));
			$prefijo = str_replace('date(d)', date('d'), trim($prefijo));
			$prefijo = str_replace("date('Ymd')", date('Ymd'), trim($record['prefijo']));	
			$prefijo = str_replace('date("Ymd")', date('Ymd'), trim($prefijo));
			$prefijo = str_replace("date(Ymd)", date('Ymd'), trim($prefijo));			
			//*******************************************************************************************************************/
			//* Armando el sufijo con el año , mes y/o dia si es el caso
			//*******************************************************************************************************************/			
			$sufijo = str_replace("date('Y')", date('Y'), trim($record['sufijo']));
			$sufijo = str_replace("date('m')", date('m'), trim($sufijo));
			$sufijo = str_replace("date('d')", date('d'), trim($sufijo));
			$sufijo = str_replace('date("Y")', date('Y'), trim($record['sufijo']));
			$sufijo = str_replace('date("m")', date('m'), trim($sufijo));
			$sufijo = str_replace('date("d")', date('d'), trim($sufijo));
			$sufijo = str_replace('date(Y)', date('Y'), trim($record['sufijo']));
			$sufijo = str_replace('date(m)', date('m'), trim($sufijo));
			$sufijo = str_replace('date(d)', date('d'), trim($sufijo));
			$sufijo = str_replace("date('Ymd')", date('Ymd'), trim($record['sufijo']));			
			$sufijo = str_replace('date("Ymd")', date('Ymd'), trim($record['sufijo']));
			$sufijo = str_replace("date(Ymd)", date('Ymd'), trim($record['sufijo']));
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
				$query = "select * from [IHTT_RENOVACIONES_AUTOMATICAS].[DBO].[TB_Secuencias_Rango] WITH (UPDLOCK) WHERE secuencia_id = :ID_Secuencia and fecha_final >= CAST(:fecha_final as DATE)";
				$p = array(":ID_Secuencia" => $ID_Secuencia, ":fecha_final" => DATE('Y/m/d'));
				$recordRango = $this->select($query, $p);
				if (is_array($record) == true and isset($recordRango[0]) and isset($recordRango[0]['id'])) {
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

		IF (isset($_POST['Concesion']['Permiso_Especial']) AND $_POST['Concesion']['Permiso_Especial'] == "") {
			$PERMISO_ESPECIAL = "";
			$PERMISO_EXPLOTACION = $_POST['Concesion']['Permiso_Explotacion'];
			$CERTIFICADO_OPERACION = $_POST['Concesion']['Certificado'];
		} ELSE {
			$PERMISO_ESPECIAL = $_POST['Concesion']['Permiso_Especial'];
			$PERMISO_EXPLOTACION = "";
			$CERTIFICADO_OPERACION = "";
		}

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
			":Alto" =>  floatval($Unidad['Alto']),
			":Ancho" => floatval($Unidad['Ancho']),
			":Largo" => floatval($Unidad['Largo']),
			":Capacidad_Carga" => floatval($Unidad['Capacidad']),
			":Peso_Unidad" => 0,
			":Permiso_Explotacion" => strtoupper($PERMISO_EXPLOTACION),
			":Certificado_Operacion" => strtoupper($CERTIFICADO_OPERACION),
			":Permiso_Especial" => strtoupper(string: $PERMISO_ESPECIAL),
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

		IF (isset($_POST['Concesion']['Permiso_Especial']) AND $_POST['Concesion']['Permiso_Especial'] == "") {
			$PERMISO_ESPECIAL = "";
			$PERMISO_EXPLOTACION = $_POST['Concesion']['Permiso_Explotacion'];
			$CERTIFICADO_OPERACION = $_POST['Concesion']['Certificado'];
		} ELSE {
			$PERMISO_ESPECIAL = $_POST['Concesion']['Permiso_Especial'];
			$PERMISO_EXPLOTACION = "";
			$CERTIFICADO_OPERACION = "";
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
				":N_Certificado" => strtoupper($CERTIFICADO_OPERACION),
				":Permiso_Explotacion" => strtoupper($PERMISO_EXPLOTACION),
				":Sistema_IP" => $this->getIp(),
				":ID_Tipo_Categoria" => $Tramites[$i]['ID_Categoria'],
				":N_Permiso_Especial" => strtoupper($PERMISO_ESPECIAL),
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
			Fecha_Aceptacion = SYSDATETIME(),
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

		IF (isset($_POST['Concesion']['Permiso_Especial']) AND $_POST['Concesion']['Permiso_Especial'] == "") {
			$PERMISO_ESPECIAL = "";
			$PERMISO_EXPLOTACION = $_POST['Concesion']['Permiso_Explotacion'];
			$CERTIFICADO_OPERACION = $_POST['Concesion']['Certificado'];
		} ELSE {
			$PERMISO_ESPECIAL = $_POST['Concesion']['Permiso_Especial'];
			$PERMISO_EXPLOTACION = "";
			$CERTIFICADO_OPERACION = "";
		} 

		// Consulta SQL para actualizar el vehículo
		$query = "UPDATE [IHTT_PREFORMA].[dbo].[TB_Vehiculo]
		SET RTN_Propietario = :RTN_Propietario,
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
			ID_Placa = :ID_Placa,
			Sistema_Usuario = :Sistema_Usuario
		WHERE 
			ID = :ID";  
		$parametros = array(
			":RTN_Propietario" => $Unidad['RTN_Propietario'],
			":Nombre_Propietario" => strtoupper($Unidad['Nombre_Propietario']),
			":ID_Marca" => $Unidad['Marca'],
			":Anio" => $Unidad['Anio'],
			":Modelo" => strtoupper($Unidad['Modelo']),
			":Tipo_Vehiculo" => strtoupper($Unidad['Tipo']),
			":ID_Color" => $Unidad['Color'],
			":Motor" => strtoupper($Unidad['Motor']),
			":Chasis" => strtoupper($Unidad['Serie']),
			":VIN" => strtoupper($Unidad['VIN']),
			":Combustible" => strtoupper($Unidad['Combustible']),
			":Alto" => floatval($Unidad['Alto']),
			":Ancho" => floatval($Unidad['Ancho']),
			":Largo" => floatval($Unidad['Largo']),
			":Capacidad_Carga" => floatval($Unidad['Capacidad']),
			":Permiso_Explotacion" => strtoupper($PERMISO_EXPLOTACION),
			":Certificado_Operacion" => strtoupper($CERTIFICADO_OPERACION),
			":Permiso_Especial" => strtoupper($PERMISO_ESPECIAL),
			":Estado" => $Estado,
			":ID_Placa_Antes_Replaqueo" => strtoupper($Unidad['ID_Placa_Antes_Replaqueo']),
			":ID_Placa" => strtoupper($Unidad['Placa']),
			":Sistema_Usuario" => $_SESSION["user_name"],
			":ID" => intval($Unidad['ID_Unidad'])
		);

		// Ejecutar la actualización (esto usa la función insert, que también puede manejar updates)
		return $this->update($query, $parametros);
	}

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
			if ($_POST["Concesion"]['esCertificado'] == True) {
				$responseValidarPlacas = $this->validarPlaca($_POST["Unidad1"]['Placa'], $_POST["Unidad1"]['ID_Placa_Antes_Replaqueo'], $_POST["Concesion"]['Certificado']);
			} else {
				$responseValidarPlacas = $this->validarPlaca($_POST["Unidad1"]['Placa'], $_POST["Unidad1"]['ID_Placa_Antes_Replaqueo'], $_POST["Concesion"]['Permiso_Especial']);
			}
			if ($_POST["Concesion"]['esCertificado'] == true) {
				$responseValidarPreforma = $this->validarEnPreforma($_POST["Unidad1"]['Placa'], $_POST["Unidad1"]['ID_Placa_Antes_Replaqueo'], $_POST["Concesion"]['Certificado'],$_POST["Concesion"]['RAM']);
			} else {
				$responseValidarPreforma = $this->validarEnPreforma($_POST["Unidad1"]['Placa'], $_POST["Unidad1"]['ID_Placa_Antes_Replaqueo'], $_POST["Concesion"]['Permiso_Especial'],$_POST["Concesion"]['RAM']);
			}
		} else {
			$responseValidarMultas = $this->getDatosMulta($_POST["Unidad"]['Placa'], $_POST["Unidad"]['ID_Placa_Antes_Replaqueo']);
			if ($_POST["Concesion"]['esCertificado'] == true) {
				$responseValidarPlacas = $this->validarPlaca($_POST["Unidad"]['Placa'], $_POST["Unidad"]['ID_Placa_Antes_Replaqueo'], $_POST["Concesion"]['Certificado']);
			} else {
				$responseValidarPlacas = $this->validarPlaca($_POST["Unidad"]['Placa'], $_POST["Unidad"]['ID_Placa_Antes_Replaqueo'], $_POST["Concesion"]['Permiso_Especial']);
			}			

			if ($_POST["Concesion"]['esCertificado'] == true) {
				$responseValidarPreforma = $this->validarEnPreforma($_POST["Unidad"]['Placa'], $_POST["Unidad"]['ID_Placa_Antes_Replaqueo'],  $_POST["Concesion"]['Certificado'],$_POST["Concesion"]['RAM']);
			} else {
				$responseValidarPreforma = $this->validarEnPreforma($_POST["Unidad"]['Placa'], $_POST["Unidad"]['ID_Placa_Antes_Replaqueo'], $_POST["Concesion"]['Permiso_Especial'],$_POST["Concesion"]['RAM']);
			}
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
		//print_r($responseValidarPreforma);die();
		if (
			$RAM == false or
			((isset($responseValidarUsuario)   and $responseValidarUsuario  == false  and is_array($responseValidarUsuario) == false))    or
			((isset($responseValidarCiudad)    and $responseValidarCiudad   == false  and is_array($responseValidarCiudad) == false))    or
			((isset($responseValidarPlacas)    and isset($responseValidarPlacas[0]) == true)  or ((isset($responseValidarPlacas)   and $responseValidarPlacas    == false and is_array($responseValidarPlacas) == false)))   or
			((isset($responseValidarMultas)    and isset($responseValidarMultas[0]) == true) or ((isset($responseValidarMultas)   and $responseValidarMultas    == false and is_array($responseValidarMultas) == false)))   or
			((isset($responseValidarMultas1)   and isset($responseValidarMultas1[0]) == true) or ((isset($responseValidarMultas1)  and $responseValidarMultas1   == false and is_array($responseValidarMultas1) == false)))   or
			((isset($responseValidarPreforma)  and isset($responseValidarPreforma[0]) == true) or ((isset($responseValidarPreforma) and $responseValidarPreforma  == false and is_array($responseValidarPreforma) == false)))
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
				echo json_encode(array("error" => 4000, "errorhead" => "INCONVENIENTES", "errormsg" => 'INTENTANTO ACTUALIZAR EL SOLICITANTE'));
			} else {
				if ($_POST["Concesion"]['RAM'] == '') {
					$isOKApoderado = $this->updateApoderado($RAM['nuevo_numero'], $_POST["Apoderado"]);
				} else {
					$isOKApoderado = $_POST["Apoderado"]['ID_Apoderado'];
				}
				if ($isOKApoderado == false) {
					$this->db->rollBack();
					echo json_encode(array("error" => 4001, "errorhead" => "INCONVENIENTES", "errormsg" => 'INTENTANTO ACTUALIZAR EL APODERADO'));
				} else {
					$isOKUnidad = $_POST["Unidad"]['ID_Unidad'];
					if ($_POST["Concesion"]['esCambioDeVehiculo'] == true) {
						$isOKUnidad = $this->updateUnidad($RAM['nuevo_numero'], $_POST["Unidad"], $_POST["Concesion"], 'SALE');
					} else {
						$isOKUnidad = $this->updateUnidad($RAM['nuevo_numero'], $_POST["Unidad"], $_POST["Concesion"], 'NORMAL');
					}
					if ($isOKUnidad == false) {
						$this->db->rollBack();
						echo json_encode(array("error" => 4002, "errorhead" => "INCONVENIENTES", "errormsg" => 'INTENTANTO ACTUALIZAR LA UNIDAD'));
					} else {
						if ($_POST["Concesion"]['esCambioDeVehiculo'] == true) {
							if ($_POST["Unidad1"]['ID_Unidad'] !== '' && $_POST["Unidad1"]['ID_Unidad'] !== null) {
								$isOKUnidad1 = $this->updateUnidad($RAM['nuevo_numero'], $_POST["Unidad1"], $_POST["Concesion"], 'ENTRA');
							} else {
								$isOKUnidad1 = $this->saveUnidad($RAM['nuevo_numero'], $_POST["Unidad1"], $_POST["Concesion"], 'ENTRA');
							}
							if ($isOKUnidad1 == false) {
								$this->db->rollBack();
								echo json_encode(array("error" => 4003, "errorhead" => "INCONVENIENTES", "errormsg" => 'INTENTANTO ACTUALIZAR LA UNIDAD 1'));
								$ERROR = true;
							} else {
								$this->db->commit();
								//$this->db->rollBack();
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
							//$this->db->rollBack();
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
	protected function savePreforma()	{
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
			if ($_POST["Concesion"]['esCertificado'] == true) {
				$responseValidarPlacas = $this->validarPlaca($_POST["Unidad1"]['Placa'], $_POST["Unidad1"]['ID_Placa_Antes_Replaqueo'], $_POST["Concesion"]['Certificado']);
			} else {
				$responseValidarPlacas = $this->validarPlaca($_POST["Unidad1"]['Placa'], $_POST["Unidad1"]['ID_Placa_Antes_Replaqueo'], $_POST["Concesion"]['Permiso_Especial']);
			}
			if ($_POST["Concesion"]['esCertificado'] == true) {
				$responseValidarPreforma = $this->validarEnPreforma($_POST["Unidad1"]['Placa'], $_POST["Unidad1"]['ID_Placa_Antes_Replaqueo'], $_POST["Concesion"]['Certificado'],$_POST["Concesion"]['RAM']);
			} else {
				$responseValidarPreforma = $this->validarEnPreforma($_POST["Unidad1"]['Placa'], $_POST["Unidad1"]['ID_Placa_Antes_Replaqueo'], $_POST["Concesion"]['Permiso_Especial'],$_POST["Concesion"]['RAM']);
			}
		} else {

			$responseValidarMultas = $this->getDatosMulta($_POST["Unidad"]['Placa'], $_POST["Unidad"]['ID_Placa_Antes_Replaqueo']);

			if ($_POST["Concesion"]['esCertificado'] == true) {
				$responseValidarPlacas = $this->validarPlaca($_POST["Unidad"]['Placa'], $_POST["Unidad"]['ID_Placa_Antes_Replaqueo'], $_POST["Concesion"]['Certificado']);				
			} else {
				$responseValidarPlacas = $this->validarPlaca($_POST["Unidad"]['Placa'], $_POST["Unidad"]['ID_Placa_Antes_Replaqueo'], $_POST["Concesion"]['Permiso_Especial']);				
			}		

			if ($_POST["Concesion"]['esCertificado'] == true) {
				$responseValidarPreforma = $this->validarEnPreforma($_POST["Unidad"]['Placa'], $_POST["Unidad"]['ID_Placa_Antes_Replaqueo'], $_POST["Concesion"]['Certificado'],$_POST["Concesion"]['RAM']);
			} else {
				$responseValidarPreforma = $this->validarEnPreforma($_POST["Unidad"]['Placa'], $_POST["Unidad"]['ID_Placa_Antes_Replaqueo'], $_POST["Concesion"]['Permiso_Especial'],$_POST["Concesion"]['RAM']);					
			}						
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
		//print_r($responseValidarPreforma);die();
		if (
			$RAM == false or
			((isset($responseValidarUsuario)   and $responseValidarUsuario  == false  and is_array($responseValidarUsuario) == false))    or
			((isset($responseValidarCiudad)    and $responseValidarCiudad   == false  and is_array($responseValidarCiudad) == false))    or
			((isset($responseValidarPlacas)    and isset($responseValidarPlacas[0]) == true)  or ((isset($responseValidarPlacas)   and $responseValidarPlacas    == false and is_array($responseValidarPlacas) == false)))   or
			((isset($responseValidarMultas)    and isset($responseValidarMultas[0]) == true) or ((isset($responseValidarMultas)   and $responseValidarMultas    == false and is_array($responseValidarMultas) == false)))   or
			((isset($responseValidarMultas1)   and isset($responseValidarMultas1[0]) == true) or ((isset($responseValidarMultas1)  and $responseValidarMultas1   == false and is_array($responseValidarMultas1) == false)))   or
			((isset($responseValidarPreforma)  and isset($responseValidarPreforma[0]) == true) or ((isset($responseValidarPreforma) and $responseValidarPreforma  == false and is_array($responseValidarPreforma) == false)))
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
				echo json_encode(array("error" => 4000, "errorhead" => "INCONVENIENTES", "errormsg" => 'INTENTANTO SALVAR EL SOLICITANTE'));
			} else {
				if ($_POST["Concesion"]['RAM'] == '') {
					$isOKApoderado = $this->saveApoderado($RAM['nuevo_numero'], $_POST["Apoderado"]);
				} else {
					$isOKApoderado = $_POST["Apoderado"]['ID_Apoderado'];
				}
				if ($isOKApoderado == false) {
					$this->db->rollBack();
					echo json_encode(array("error" => 4001, "errorhead" => "INCONVENIENTES", "errormsg" => 'INTENTANTO SALVAR EL APODERADO'));
				} else {
					if ($_POST["Concesion"]['esCambioDeVehiculo'] == true) {
						$isOKUnidad = $this->saveUnidad($RAM['nuevo_numero'], $_POST["Unidad"], $_POST["Concesion"], 'SALE');
					} else {
						$isOKUnidad = $this->saveUnidad($RAM['nuevo_numero'], $_POST["Unidad"], $_POST["Concesion"], 'NORMAL');
					}
					if ($isOKUnidad == false) {
						$this->db->rollBack();
						echo json_encode(array("error" => 4002, "errorhead" => "INCONVENIENTES", "errormsg" => 'INTENTANTO SALVAR LA UNIDAD'));
					} else {
						if ($_POST["Concesion"]['esCambioDeVehiculo'] == true) {
							$isOKUnidad1 = $this->saveUnidad($RAM['nuevo_numero'], $_POST["Unidad1"], $_POST["Concesion"], 'ENTRA');
							if ($isOKUnidad1 == false) {
								$this->db->rollBack();
								echo json_encode(array("error" => 4003, "errorhead" => "INCONVENIENTES", "errormsg" => 'INTENTANTO SALVAR LA UNIDAD 1'));
								$ERROR = true;
							}
						}
						if ($ERROR == false) {
							$isOKTramites = $this->saveTramites($RAM['nuevo_numero'], $_POST["Tramites"]);
							if ($isOKTramites[0] == false) {
								$this->db->rollBack();
								echo json_encode(array("error" => 4004, "errorhead" => "INCONVENIENTES", "errormsg" => 'INTENTANTO SALVAR LOS TRAMITES'));
							} else {
								$isOKBitacora = true;
								if ($_POST["Concesion"]['RAM'] == '') {
									$isOKBitacora = $this->saveBitacora($RAM['nuevo_numero'], 'INGRESO', 1);
								}
								if ($isOKBitacora == false) {
									$this->db->rollBack();
									echo json_encode(array("error" => 4005, "errorhead" => "INCONVENIENTES", "errormsg" => 'INTENTANTO SALVAR LA BITACORA'));
								} else {
									$isOKCrearCarpeta = true;
									if ($_POST["Concesion"]['RAM'] == '') {
										$isOKCrearCarpeta = $this->crearCarpeta($RAM['nuevo_numero']);
									}
									if ($isOKCrearCarpeta != true) {
										echo json_encode(array("error" => 4006, "errorhead" => "INCONVENIENTES", "errormsg" => 'INTENTANTO CREAR LA CARPETA PARA ALMACENAR DOCUMENTOS'));
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
												'Bitacora'       =>  isset($isOKBitacora) ? $isOKBitacora : false,
												'Tramites'       =>  isset($isOKTramites) ? $isOKTramites : false,
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

	protected function getTemplate ($rs_id_rs_template):mixed {
		$query_rs_template = "SELECT [id]
		  ,[template_tipo_id]
		  ,[descripcion]
		  ,[template]
		  ,[estado]
		  ,[id_usuario_creacion]
		  ,[fecha_creacion]
		  ,[encabezado]
		  ,[pie]
		  ,[titulo]
	  FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_template] WHERE id =:Id";
	  try {
		// Recueprando la informacion del template
		$template = $this->db->prepare($query_rs_template);
		$template->execute(Array(':Id' => $rs_id_rs_template));
		$res =$template->errorInfo();
		if (isset($res) and isset($res[3]) and intval(Trim($res[3])) <> 0) {
		  $respuesta[0]['error'] = true;
		  $respuesta[0]['msg'] = "Mensaje de Error getTemplate: " . $res[0] . ' ' . $res[1] . ' ' . $res[2] . ' ' . $res[3] . ' ' ;
		  $txt = date('Y m d h:i:s') . '	' .'getTemplate.php error Linea 22: ' . $query_rs_template . $res[0] . ' ' . $res[1] . ' ' . $res[2] . ' ' . $res[3] . ' ' ;
		  logErr($txt, '../logs/logs.txt');
		  return $respuesta;
		} else {
		  $row_rs_template = $template->fetch();
		  return $row_rs_template['template'];
		}    
		} catch (\Throwable $th) {
			$txt = date('Y m d h:i:s') . '	' .'getTemplate.php catch '. $query_rs_template . ' ERROR ' . $th->getMessage();
			logErr($txt, '../logs/logs.txt');
			$respuesta[0]['error'] = true;
			$respuesta[0]['msg'] = $txt;
			return $respuesta;
		}	    
	}

	protected function enviarNotificacion($Data):array {
		$error['error'] = false;
		$error['msg'] = "";
		$Body = $Data[0]['Template'];
		$Body = str_replace('@@NOMBREABOGADO@@',$Data[0]['NombreApoderadoLega'],$Body);
		$Body = str_replace('@@avisodecobro@@',$Data[0]['Numero_Aviso'],$Body);
		$Body = str_replace('@@monto@@',$Data[0]['MontoLetras'] . ' (L. ' .  number_format($Data[0]['Monto_Total'],2) . ')' ,$Body);
		$Body = str_replace('@@_expediente_@@',$Data[0]['ID_Solicitud'] . '/' . $Data[0]['ID_Expediente'],$Body);
		$Body = str_replace('@@tramites@@',substr($Data[0]['Tramitex'],0,(strlen($Data[0]['Tramitex'])-2)),$Body);
		//$Body = str_replace('@@resolucion@@',$Data[0]['ID_Resolucion'],$Body);
		// Inicio
		try {
			$mail = new PHPMailer(true);
			$mail = new PHPMailer;
			//$mail->SMTPDebug = SMTP::DEBUG_SERVER;                         // Mostrar salida (Desactivar en producción)
			//$mail->SMTPDebug = 2; // Set to 3 or 4 for more details
			//$mail->Debugoutput = 'html';
			$mail->SMTPOptions =  array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_sifned' => true  ) );
			$mail->isSMTP();      
			$mail->Host = $this->server_smtp;  
			$mail->SMTPAuth = true;                               
			$mail->Username = $this->server_smtp_user;
			$mail->Password = $this->server_smtp_password;
			$mail->SMTPSecure = 'ssl';                           
			$mail->Port = $this->server_smtp_port;
			$mail->setFrom($this->server_smtp_user, 'IHTT NOTIFICACIÓN');
			if ($_SESSION['Environment'] =='DEV') {
				$mail->addAddress('rbthaofic@gmail.com');
				$mail->addAddress('oscaricalix@gmail.com');
				$mail->addAddress('copy@transporte.gob.hn'); 
			} else {
				$mail->addAddress(trim($Data[0]['Email_Apoderado']), trim($Data[0]['NombreApoderadoLega'])); 
				$mail->addCC(trim($Data[0]['Email']), trim($Data[0]['NombreSolicitante'])); 
				$mail->addCC('copy@transporte.gob.hn'); 
			}	
			$mail->isHTML(true); 
			$mail->CharSet = 'UTF-8';
			$mail->Subject = $Data[0]['Titulo_Correo'];
			$mail->Body    = $Body;
			$mail->AltBody = $Body;
			

			if ($Data[0]['Aviso_Ruta'] != '') {
				$mail->addAttachment($Data[0]['Aviso_Ruta']);        //Add 
			}
			/*
			if ($Data[0]['Auto_Ruta'] != '') {
				$mail->addAttachment($Data[0]['Auto_Ruta']);         //Add 
			}
			if ($Data[0]['Ruta'] != '') {
				$mail->addAttachment($Data[0]['Ruta']);              //Add 
			}
			*/
	
			$mail->send();
			//Attachments
			//*******************************************************//
			// Incio de generando Logs de Envio Fallido de email
			//******************************************************//
			if (isset($mail->ErrorInfo) && $mail->ErrorInfo != '') {
				$txt = date('Y m d h:i:s') . '	' . 'Send_Mail Enviado-> Envio Fallido de Notificación de Aviso de Cobro: $mail->ErrorInfo:' . '	' . trim($Data[0]['Email_Apoderado']) . $Data[0]['NombreApoderadoLega'] . $mail->ErrorInfo;
				logErr($txt,'../logs/logs.txt');
				$error['error'] = true;
			}

			return $error;
		} catch (Exception $e) {
			$error['error'] = true;
			$error['msg'] = "El mensaje no se ha enviado. Mailer Error: {$mail->ErrorInfo}";
			//*******************************************************//
			// Incio de generando Logs de Envio Fallido de email
			//******************************************************//
			$txt = date('Y m d h:i:s') . '	' . 'Send_Mail Exception Fallido-> Envio Fallido de Notificación de Aviso de Cobro: Exception $mail->ErrorInfo' . '	' . $mail->ErrorInfo;
			logErr($txt,'../logs/logs.txt');
			$txt = date('Y m d h:i:s') . '	' . 'Send_Mail Exception Fallido-> Envio Fallido de Notificación de Aviso de Cobro: Excepcion: $e' . '	' . $e;
			logErr($txt,'../logs/logs.txt');
			return $error;
		}
	}	
	protected function getSiguienteId($tabla,$max) {
		$respuesta[0]['msg'] = "";
		$respuesta[0]['error'] = false;	
		$respuesta[0]['siguiente_id']=-1;
		$respuesta[0]['errorcode'] = '';
			$query_rs_siguiente = "SELECT " . $max . " FROM " . $tabla;
			// Recueprando la informacion del Siguiente
			$Siguiente = $this->db->prepare($query_rs_siguiente);
		try {
			$Siguiente->execute();
			$res = $Siguiente->errorInfo();
			if (isset($res) and  isset($res[3]) and intval(Trim($res[3])) <> 0) {
				$respuesta[0]['error'] = true;
				$respuesta[0]['errorcode'] = $res[1];
				$respuesta[0]['msg'] = "Mensaje de Error: " . $res[0] . ' ' . $res[1] . ' ' . $res[2] . ' ' . $res[3] . ' ' ;
				$txt = date('Y m d h:i:s') . '	' .'getSiguienteID.php error Linea 20: ' . $query_rs_siguiente . $res[0] . ' ' . $res[1] . ' ' . $res[2] . ' ' . $res[3] . ' ' ;;
				logErr($txt, '../logs/logs.txt');
			}else{
				$row_rs_Siguiente = $Siguiente->fetch();
				$respuesta[0]['siguiente_id'] = $row_rs_Siguiente['siguiente_id'];
			}
		} catch (\Throwable $th) {
			$respuesta[0]['error'] = true;
			$respuesta[0]['errorcode'] = 0;
			$respuesta[0]['msg'] = "Mensaje de Error: " . $th->getMessage();
			$txt = date('Y m d h:i:s') . '	' .'getSiguienteID.php Catch Linea 27: ' .  $th->getMessage() . ' QUERY'  . $query_rs_siguiente;
			logErr($txt, '../logs/logs.txt');
		}	
		return $respuesta;
	}

	protected function saveAvisoCobro ($Data) {
		date_default_timezone_set('America/Guatemala');
		$respuesta[0]['error'] = false;
		$respuesta[0]['msg'] = "";
		$respuesta_enc[0]['CodigoAvisoCobro'] = '';
		foreach ($Data as $EncTramite){
			$respuesta_enc = $this->getSiguienteId('[IHTT_Webservice].[dbo].[TB_AvisoCobroEnc]',' (max(CodigoAvisoCobro)+1) as siguiente_id ');
			if ($respuesta_enc[0]['error'] == false) {
				try {
					$query_rs_TB_AvisoCobroEnc = "INSERT INTO [IHTT_Webservice].[dbo].[TB_AvisoCobroEnc] 
					([CodigoAvisoCobro]
				   ,[FechaEmision]
				   ,[FechaVencimiento]
				   ,[RTNConcesionario]
				   ,[ID_Solicitud]
				   ,[Expediente]
				   ,[CertificadoOperacion]
				   ,[PermisoExplotacion]
				   ,[Placa]
				   ,[AvisoCobroEstado]
				   ,[Observaciones]
				   ,[SistemaUsuario]
				   ,[SistemaFecha]
				   ,[IPUsuario]
				   ,[NombreConcesionario]
				   ,[MontoLetras]
				   ,[ID_TipoCobro]
				   ,[ID_Modulo]
				   ,[ID_Notificado]
				   ,[ID_Organizacion]
				   ,[Resolucion]
				   ,[ID_EstadoCobranza]
				   ,[CodigoRegionalIHTT]) 
				   VALUES (
				   :CodigoAvisoCobro
				   ,SYSDATETIME()
				   ,DATEADD(MONTH,3,SYSDATETIME())
				   ,:RTNConcesionario
				   ,:ID_Solicitud
				   ,:Expediente
				   ,:CertificadoOperacion
				   ,:PermisoExplotacion
				   ,:Placa
				   ,:AvisoCobroEstado
				   ,:Observaciones
				   ,:SistemaUsuario
				   ,SYSDATETIME()
				   ,:IPUsuario
				   ,:NombreConcesionario
				   ,:MontoLetras
				   ,:ID_TipoCobro
				   ,:ID_Modulo
				   ,:ID_Notificado
				   ,:ID_Organizacion
				   ,:Resolucion
				   ,:ID_EstadoCobranza
				   ,'0801');";
					// Recueprando la informacion del Resolucion
					$AvisoEnc = $this->db->prepare($query_rs_TB_AvisoCobroEnc);
					$AvisoEnc->execute(Array(
						':CodigoAvisoCobro' => $respuesta_enc[0]['siguiente_id'],
						':RTNConcesionario' => $EncTramite['RTN_Solicitante'],
						':ID_Solicitud' => $EncTramite['ID_Solicitud'],
						':Expediente' => $EncTramite['ID_Expediente'],
						':CertificadoOperacion' => $EncTramite['Certificado_Operacion'],
						':PermisoExplotacion' => $EncTramite['Permiso_Explotacion'],
						':Placa' => $EncTramite['ID_Placa'],
						':AvisoCobroEstado' => 1,
						':Observaciones' => $EncTramite['Observacion'],
						':SistemaUsuario' => $EncTramite['usuario'],
						':IPUsuario' => $EncTramite['IPUsuario'],
						':NombreConcesionario' => $EncTramite['NombreSolicitante'],
						':MontoLetras' => $EncTramite['MontoLetras'],
						':ID_TipoCobro' => 1,
						':ID_Modulo' => $EncTramite['Modulo'],
						':ID_Notificado' => 0,
						':ID_Organizacion' => 1,
						':Resolucion' => $EncTramite['Resolucion'],
						':ID_EstadoCobranza' => 1));
					$res = $AvisoEnc->errorInfo();
					if (isset($res) and isset($res[3]) and intval(Trim($res[3])) <> 0) {
						$respuesta[0]['error'] = true;
						$respuesta[0]['msg'] = "Mensaje de Error AvisoEnc: " . $res[0] . ' ' . $res[1] . ' ' . $res[2] . ' ' . $res[3] . ' ' ;
						break;
					} else {
					  foreach ($EncTramite['Tramites'] as $DetTramite){
							try {
								$query_rs_TB_AvisoCobroDet = "INSERT INTO [IHTT_Webservice].[dbo].[TB_AvisoCobroDET](
								[CodigoAvisoCobro]
								,[CodigoTipoTramite]
								,[DescripcionDetalle]
								,[IDHistoricoTarifas]
								,[Monto]
								,[SistemaFecha]
								,[SistemaUsuario]
								,[Certificado_Operacion]
								,[Numero_Placa]
								,[Expediente_Det]) 
								VALUES 
								(:CodigoAvisoCobro
								,:CodigoTipoTramite
								,:DescripcionDetalle
								,:IDHistoricoTarifas
								,:Monto
								,SYSDATETIME()
								,:SistemaUsuario
								,:Certificado_Operacion
								,:Numero_Placa
								,:Expediente_Det);";
								// Recueprando la informacion del Resolucion
								$AvisoDet = $this->db->prepare($query_rs_TB_AvisoCobroDet);
								$AvisoDet->execute(Array(
									':CodigoAvisoCobro' => $respuesta_enc[0]['siguiente_id'],
									':CodigoTipoTramite' => $DetTramite['ID_tramite'],
									':DescripcionDetalle' => $DetTramite['DescripcionDetalle'],
									':IDHistoricoTarifas' => $DetTramite['IDHistoricoTarifas'],
									':Monto' => $DetTramite['Monto'],
									':SistemaUsuario' => $EncTramite['usuario'],
									':Certificado_Operacion' => $DetTramite['Certificado_Operacion'],
									':Numero_Placa' => $DetTramite['ID_Placa'],
									':Expediente_Det' => $DetTramite['Expediente_Det']));
								$res = $AvisoDet->errorInfo();
								if (isset($res) and isset($res[3]) and intval(Trim($res[3])) <> 0) {
									$respuesta[0]['error'] = true;
									$respuesta[0]['msg'] = "Mensaje de Error AvisoDet: " . $res[0] . ' ' . $res[1] . ' ' . $res[2] . ' ' . $res[3] . ' ' ;
									$txt = date('Y m d h:i:s') . '	' .'saveAvisoCobro.php error Detalle: ' . $query_rs_TB_AvisoCobroDet . $res[0] . ' ' . $res[1] . ' ' . $res[2] . ' ' . $res[3] . ' ' ;
									logErr($txt, '../logs/logs.txt');
									break;
								}else {
									$respuesta[0]['CodigoAvisoCobro']=$respuesta_enc[0]['siguiente_id'];
								}
							} catch (\Throwable $th) {
								$respuesta[0]['error'] = true;
								$respuesta[0]['msg'] = "Mensaje de Error en Try Catch EvisoDet: " . $th->getMessage();
								$txt = date('Y m d h:i:s') . '	' .'saveAvisoCobro.php Catch Aviso Det: ' .  $th->getMessage() . ' QUERY'  . $query_rs_TB_AvisoCobroDet;
								logErr($txt, '../logs/logs.txt');
								break;
							}
					  }
					}
				} catch (\Throwable $th) {
					$respuesta[0]['error'] = true;
					$respuesta[0]['msg'] =  "Mensaje de Error en Try Catch EvisoENC: " . $th->getMessage();
					$txt = date('Y m d h:i:s') . '	' .'saveAvisoCobro.php: AvisoEnca ' .  $th->getMessage() . ' QUERY'  . $query_rs_TB_AvisoCobroDet;
					logErr($txt, '../logs/logs.txt');
					break;
				}
			} else {
				$respuesta[0]['error'] = true;
				$respuesta[0]['msg'] = 'getSiguienteId ' . $respuesta_enc[0]['msg'];
				$txt = date('Y m d h:i:s') . '	' .'saveAvisoCobro.php else getSiguienteID';
				logErr($txt, '../logs/logs.txt');
			}
		}
		return $respuesta;
	}

	protected function unidad($numuero):string {
		switch ($numuero)
		{
			case 9:
			{
				$numu = "NUEVE";
				break;
			}
			case 8:
			{
				$numu = "OCHO";
				break;
			}
			case 7:
			{
				$numu = "SIETE";
				break;
			}
			case 6:
			{
				$numu = "SEIS";
				break;
			}
			case 5:
			{
				$numu = "CINCO";
				break;
			}
			case 4:
			{
				$numu = "CUATRO";
				break;
			}
			case 3:
			{
				$numu = "TRES";
				break;
			}
			case 2:
			{
				$numu = "DOS";
				break;
			}
			case 1:
			{
				$numu = "UNO";
				break;
			}
			case 0:
			{
				$numu = "";
				break;
			}
		}
		return $numu;
	}
	
	 
	
	function decena($numero):string{
	
	 
	
			if ($numero >= 90 && $numero <= 99)
	
			{
	
				$numd = "NOVENTA ";
	
				if ($numero > 90)
	
					$numd = $numd."Y ".($this->unidad($numero - 90));
	
			}
	
			else if ($numero >= 80 && $numero <= 89)
	
			{
	
				$numd = "OCHENTA ";
	
				if ($numero > 80)
	
					$numd = $numd."Y ".($this->unidad($numero - 80));
	
			}
	
			else if ($numero >= 70 && $numero <= 79)
	
			{
	
				$numd = "SETENTA ";
	
				if ($numero > 70)
	
					$numd = $numd."Y ".($this->unidad($numero - 70));
	
			}
	
			else if ($numero >= 60 && $numero <= 69)
	
			{
	
				$numd = "SESENTA ";
	
				if ($numero > 60)
	
					$numd = $numd."Y ".($this->unidad($numero - 60));
	
			}
	
			else if ($numero >= 50 && $numero <= 59)
	
			{
	
				$numd = "CINCUENTA ";
	
				if ($numero > 50)
	
					$numd = $numd."Y ".($this->unidad($numero - 50));
	
			}
	
			else if ($numero >= 40 && $numero <= 49)
	
			{
	
				$numd = "CUARENTA ";
	
				if ($numero > 40)
	
					$numd = $numd."Y ".($this->unidad($numero - 40));
	
			}
	
			else if ($numero >= 30 && $numero <= 39)
	
			{
	
				$numd = "TREINTA ";
	
				if ($numero > 30)
	
					$numd = $numd."Y ".($this->unidad($numero - 30));
	
			}
	
			else if ($numero >= 20 && $numero <= 29)
	
			{
	
				if ($numero == 20)
	
					$numd = "VEINTE ";
	
				else
	
					$numd = "VEINTI".($this->unidad($numero - 20));
	
			}
	
			else if ($numero >= 10 && $numero <= 19)
	
			{
	
				switch ($numero){
	
				case 10:
	
				{
	
					$numd = "DIEZ ";
	
					break;
	
				}
	
				case 11:
	
				{
	
					$numd = "ONCE ";
	
					break;
	
				}
	
				case 12:
	
				{
	
					$numd = "DOCE ";
	
					break;
	
				}
	
				case 13:
	
				{
	
					$numd = "TRECE ";
	
					break;
	
				}
	
				case 14:
	
				{
	
					$numd = "CATORCE ";
	
					break;
	
				}
	
				case 15:
	
				{
	
					$numd = "QUINCE ";
	
					break;
	
				}
	
				case 16:
	
				{
	
					$numd = "DIECISEIS ";
	
					break;
	
				}
	
				case 17:
	
				{
	
					$numd = "DIECISIETE ";
	
					break;
	
				}
	
				case 18:
	
				{
	
					$numd = "DIECIOCHO ";
	
					break;
	
				}
	
				case 19:
	
				{
	
					$numd = "DIECINUEVE ";
	
					break;
	
				}
	
				}
	
			}
	
			else
	
				$numd = $this->unidad($numero);
	
		return $numd;
	
	}
	
	 
	
	protected function centena($numc):string {

		if ($numc >= 100)

		{

			if ($numc >= 900 && $numc <= 999)

			{

				$numce = "NOVECIENTOS ";

				if ($numc > 900)

					$numce = $numce.($this->decena($numc - 900));

			}

			else if ($numc >= 800 && $numc <= 899)

			{

				$numce = "OCHOCIENTOS ";

				if ($numc > 800)

					$numce = $numce.($this->decena($numc - 800));

			}

			else if ($numc >= 700 && $numc <= 799)

			{

				$numce = "SETECIENTOS ";

				if ($numc > 700)

					$numce = $numce.($this->decena($numc - 700));

			}

			else if ($numc >= 600 && $numc <= 699)

			{

				$numce = "SEISCIENTOS ";

				if ($numc > 600)

					$numce = $numce.($this->decena($numc - 600));

			}

			else if ($numc >= 500 && $numc <= 599)

			{

				$numce = "QUINIENTOS ";

				if ($numc > 500)

					$numce = $numce.($this->decena($numc - 500));

			}

			else if ($numc >= 400 && $numc <= 499)

			{

				$numce = "CUATROCIENTOS ";

				if ($numc > 400)

					$numce = $numce.($this->decena($numc - 400));

			}

			else if ($numc >= 300 && $numc <= 399)

			{

				$numce = "TRESCIENTOS ";

				if ($numc > 300)

					$numce = $numce.($this->decena($numc - 300));

			}

			else if ($numc >= 200 && $numc <= 299)

			{

				$numce = "DOSCIENTOS ";

				if ($numc > 200)

					$numce = $numce.($this->decena($numc - 200));

			}

			else if ($numc >= 100 && $numc <= 199)

			{

				if ($numc == 100)

					$numce = "CIEN ";

				else

					$numce = "CIENTO ".($this->decena($numc - 100));

			}

		}

		else

			$numce = $this->decena($numc);


		return $numce;
	
	}
	
	 
	
	protected function miles($nummero){

		if ($nummero >= 1000 && $nummero < 2000){

			$numm = "MIL ".($this->centena($nummero%1000));

		}

		if ($nummero >= 2000 && $nummero <10000){

			$numm = $this->unidad(Floor($nummero/1000))." MIL ".($this->centena($nummero%1000));

		}

		if ($nummero < 1000) {
			$numm = $this->centena($nummero);
		}

		return $numm;

	}

	 
	
	protected function decmiles($numdmero):string{

		if ($numdmero == 10000)

			$numde = "DIEZ MIL";

		if ($numdmero > 10000 && $numdmero <20000){

			$numde = $this->decena(Floor($numdmero/1000))."MIL ".($this->centena($numdmero%1000));

		}

		if ($numdmero >= 20000 && $numdmero <100000){

			$numde = $this->decena(Floor($numdmero/1000))." MIL ".($this->miles($numdmero%1000));

		}

		if ($numdmero < 10000) {
			$numde = $this->miles($numdmero);
		}

		return $numde;

	}

	 
	
	protected function cienmiles($numcmero):string{

		if ($numcmero == 100000)

			$num_letracm = "CIEN MIL";

		if ($numcmero >= 100000 && $numcmero <1000000){

			$num_letracm = $this->centena(Floor($numcmero/1000))." MIL ".($this->centena($numcmero%1000));

		}

		if ($numcmero < 100000) {
			$num_letracm = $this->decmiles($numcmero);
		}

		return $num_letracm;

	}

	 
	
	protected function millon($nummiero):string {

		if ($nummiero >= 1000000 && $nummiero <2000000){

			$num_letramm = "UN MILLON ".($this->cienmiles($nummiero%1000000));

		}

		if ($nummiero >= 2000000 && $nummiero <10000000){

			$num_letramm = $this->unidad(Floor($nummiero/1000000))." MILLONES ".($this->cienmiles($nummiero%1000000));

		}

		if ($nummiero < 1000000) {
			$num_letramm = $this->cienmiles($nummiero);
		}

	

		return $num_letramm;

	}

	 
	
	protected function decmillon($numerodm):string{

		if ($numerodm == 10000000)

			$num_letradmm = "DIEZ MILLONES";

		if ($numerodm > 10000000 && $numerodm <20000000){

			$num_letradmm = $this->decena(Floor($numerodm/1000000))."MILLONES ".($this->cienmiles($numerodm%1000000));

		}

		if ($numerodm >= 20000000 && $numerodm <100000000){

			$num_letradmm = $this->decena(Floor($numerodm/1000000))." MILLONES ".($this->millon($numerodm%1000000));

		}

		if ($numerodm < 10000000) {
			$num_letradmm = $this->millon($numerodm);
		}	

		return $num_letradmm;

	}
	
	 
	
	protected function cienmillon($numcmeros):string {

		if ($numcmeros == 100000000)

			$num_letracms = "CIEN MILLONES";

		if ($numcmeros >= 100000000 && $numcmeros <1000000000){

			$num_letracms = $this->centena(Floor($numcmeros/1000000))." MILLONES ".($this->millon($numcmeros%1000000));

		}

		if ($numcmeros < 100000000) {
			$num_letracms = $this->decmillon($numcmeros);
		}

		return $num_letracms;

	}

	

	protected function milmillon($nummierod):string{

		if ($nummierod >= 1000000000 && $nummierod <2000000000){
			$num_letrammd = "MIL ".($this->cienmillon($nummierod%1000000000));
		}
		if ($nummierod >= 2000000000 && $nummierod <10000000000){
			$num_letrammd = $this->unidad(Floor($nummierod/1000000000))." MIL ".($this->cienmillon($nummierod%1000000000));
		}

		if ($nummierod < 1000000000) {
			$num_letrammd = $this->cienmillon($nummierod);
		}

		return $num_letrammd;

	}
	
	 
	
	protected function convertirMontoaLetras($numero){
		$num = str_replace(",","",$numero);
		$num = number_format($num,2,'.','');
		$cents = substr($num,strlen($num)-2,strlen($num)-1);
		$num = (int)$num;
		$numf = $this->milmillon($num);
		return trim($numf) ." LEMPIRAS CON ".$cents."/100 CENTAVOS";
	}
	 
	protected function getTarifa($ID_Tramite):mixed {
		$row_rs_Tarifa['error'] = false;	
		$row_rs_Tarifa['siguiente_Tarifa']=-1;
		$respuesta[0]['errorcode'] = '';
		try {
			$query_rs_Tarifa = "SELECT TOP (1) B.FechaFin ,B.[CodigoTramite],B.[SalarioMinimo],B.[ValorFraccion],
									B.[Monto],B.[Normativa],B.[IDHistoricoTarifas],A.DESC_Tarifa
									FROM [IHTT_Webservice].[dbo].[TB_Tarifas] A,[IHTT_Webservice].[dbo].[TB_TarifasHistorico] B
									WHERE A.CodigoTramite = B.CodigoTramite AND A.CodigoTramite = :CodigoTramite
									ORDER BY B.FechaFin DESC";
			// Recueprando la informaci[on del Tarifa
			$Tarifa = $this->db->prepare($query_rs_Tarifa);
			$Tarifa->execute(Array(':CodigoTramite' => $ID_Tramite));
			$row_rs_Tarifa = $Tarifa->fetch();
			$res = $Tarifa->errorInfo();
			if (isset($res) and isset($res[3]) and intval(Trim($res[3])) <> 0) {
				$row_rs_Tarifa['error'] = true;
				$respuesta[0]['errorcode'] = $res[1];
				$row_rs_Tarifa['msg'] = "Mensaje de Error: " . $res[0] . ' ' . $res[1] . ' ' . $res[2] . ' ' . $res[3] . ' ' ;
				$txt = date('Y m d h:i:s') . '	' .'getTarifa.php Error '. $query_rs_Tarifa  . ' res[0]' . $res[0] . ' res[1]' .$res[1] . ' res[2]' .$res[2]. ' res[3]' .$res[3];
				logErr($txt, '../logs/logs.txt');
			}
		} catch (\Throwable $th) {
			$row_rs_Tarifa['error'] = true;
			$respuesta[0]['errorcode'] = 0;
			$row_rs_Tarifa['msg'] = "Mensaje de Error: " . $th->getMessage();
			$txt = date('Y m d h:i:s') . '	' .'getTarifa.php catch '. $query_rs_Tarifa . ' ERROR ' . $th->getMessage();
			logErr($txt, '../logs/logs.txt');
		}	
		return $row_rs_Tarifa;
	}
	protected function getVehiculosPreforma($ID_Formulario_Solicitud,$Concesion):mixed {
		$row_rs_Usuario['error'] = false;	
		$row_rs_Usuario['siguiente_Usuario']=-1;
		$respuesta[0]['errorcode'] = '';
		try {
			$query_rs_stmt = "SELECT  V.Estado,CL.DESC_Color,MR.DESC_Marca,V.[RTN_Propietario],V.[Nombre_Propietario],V.[ID_Placa],V.[ID_Marca],V.[Anio],V.[Modelo],V.[Tipo_Vehiculo],V.[ID_Color],V.[Motor],V.[Chasis],V.[Permiso_Explotacion],V.[Certificado_Operacion]
			from  [IHTT_PREFORMA].[dbo].[TB_Solicitante] M,
			[IHTT_PREFORMA].[dbo].[TB_Vehiculo] V,
			[IHTT_SGCERP].[dbo].[TB_Color_Vehiculos] CL,
			[IHTT_SGCERP].[dbo].[TB_Marca_Vehiculo] MR
			where M.ID_Formulario_Solicitud = V.ID_Formulario_Solicitud and
			V.Estado in ('ENTRA','NORMAL','SALE') AND
			V.ID_Color = CL.ID_Color AND
			V.ID_Marca = MR.ID_Marca AND
			M.ID_Formulario_Solicitud = :ID_Formulario_Solicitud and
			(V.Certificado_Operacion = :Certificado_Operacion or Permiso_Especial = :Permiso_Especial)
			Order by V.Estado";
			// Recueprando la informaci[on del Usuario
			$stmt = $this->db->prepare($query_rs_stmt);
			$stmt->execute(Array(':ID_Formulario_Solicitud' => $ID_Formulario_Solicitud,':Certificado_Operacion' => $Concesion,':Permiso_Especial' => $Concesion));
			$row_rs_stmt = $stmt->fetchall();
			$res = $stmt->errorInfo();
			if (isset($res) and isset($res[3]) and intval(Trim($res[3])) <> 0) {
				$row_rs_stmt['error'] = true;
				$respuesta['errorcode'] = $res[1];
				$row_rs_Usuario['msg'] = "Mensaje de Error: " . $res[0] . ' ' . $res[1] . ' ' . $res[2] . ' ' . $res[3] . ' ' ;
				$txt = date('Y m d h:i:s') . '	' .'getvehiculosPreforma.php error: ' . $query_rs_stmt . $res[0] . ' ' . $res[1] . ' ' . $res[2] . ' ' . $res[3] . ' ' ;
				logErr($txt, '../logs/logs.txt');
			}
		} catch (\Throwable $th) {
			$respuesta[0]['error'] = true;
			$respuesta[0]['msg'] = $th->getMessage();
			$txt = date('Y m d h:i:s') . '	' .'getVehiculosPreforma.php catch '. $query_rs_stmt . ' ERROR ' . $th->getMessage();
			$row_rs_stmt='';
			logErr($txt, '../logs/logs.txt');
		}	
		return $row_rs_stmt;
	}

	protected function getSolicitudADCEV ($rs_id_rs_solicitud,$rs_id_rs_template): mixed {
		$respuesta[0]['msg'] = "";
		$respuesta[0]['error'] = false;	
		$respuesta[0]['errorcode'] = '';
		try {
			$query_rs_expediente = "SELECT  M.Usuario_Acepta,M.ID_Formulario_Solicitud_Encrypted,M.Email_Solicitante,g.N_Permiso_Especial,cs.ID_Clase_Servico,S.ID_Modalidad,
			G.ID_Modalidad,G.ID_Tipo_Categoria,Q.Email_Apoderado_Legal,G.ID_Formulario_Solicitud as Preforma,M.RTN_Solicitante,
			D.ID_Tramite,G.Permiso_Explotacion,G.N_Certificado as Certificado_Operacion,
			(select ISNULL(concat(concat(Y.Nombres,' '),Y.Apellidos),'') from [IHTT_RRHH].[dbo].[TB_Empleados] Y where Y.ID_Empleado = L.id_comisionado) as firma_comisionado,
			L.titulo_cargo_comisionado,F.DESC_tipo_tramite,
			F.Acronimo_Tramite,N.[DESC_Clase_Tramite],D.ID_Clase_Tramite,G.ID_Modalidad,
			G.ID_TIpo_Servicio,L.Titulo_Cargo,
			concat(concat(LL.Nombres,' '),LL.Apellidos) as Nombre_Firma,
			k.template,
			F.DESC_Tipo_Tramite,G.Sistema_Fecha,M.Sistema_Fecha as FechaRecibido,M.Nombre_Solicitante as NombreSolicitante,
			Q.Nombre_Apoderado_Legal as NombreApoderadoLega,Q.ID_Colegiacion as ID_ColegiacionAPL,S.ID_Clase_Servicio,D.ID_Tipo_Tramite
			from  [IHTT_PREFORMA].[dbo].[TB_Solicitante] M,
			[IHTT_PREFORMA].[dbo].[TB_Solicitud] G,
			[IHTT_PREFORMA].[dbo].[TB_Apoderado_Legal] Q,
			[IHTT_DB].[dbo].[TB_Tramite] D, 
			[IHTT_DB].[dbo].[TB_Tipo_Tramite] F, 
			[IHTT_DB].[dbo].[TB_Modalidad] S,
			[IHTT_DB].[dbo].[TB_Clase_Servicio] CS,
			[IHTT_DB].[dbo].[TB_Clase_Tramite] N, 
			[IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_template] K
			LEFT OUTER JOIN [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_template_firma] L  
			ON k.id = L.template_id
			LEFT OUTER JOIN [IHTT_RRHH].[dbo].[TB_Empleados] LL  
			ON L.usuario_firma_id = LL.ID_Empleado
			where M.ID_Formulario_Solicitud = G.ID_Formulario_Solicitud AND
			G.ID_Formulario_Solicitud = Q.ID_Formulario_Solicitud and
			G.ID_Tramite = D.ID_Tramite and  
			D.ID_Tipo_Tramite = F.ID_Tipo_Tramite AND 
			D.ID_Clase_Tramite = N.ID_Clase_Tramite and
			(G.ID_Modalidad = S.DESC_Modalidad or G.ID_Modalidad = S.ID_Modalidad)  and
			S.ID_Clase_Servicio != 'FTT03' and
			S.ID_Clase_Servicio = CS.ID_Clase_Servico and
			M.ID_Formulario_Solicitud = :ID_Formulario_Solicitud and k.id = :ID_Template
			order by G.N_Certificado,G.N_Permiso_Especial,D.ID_Clase_Tramite";
			// Recuperando la información del expediente
			$expediente = $this->db->prepare($query_rs_expediente);
			$expediente->execute(Array(':ID_Formulario_Solicitud' => $rs_id_rs_solicitud,':ID_Template' => $rs_id_rs_template));
			$res = $expediente->errorInfo();
			if (isset($res) and isset($res[3]) and intval(Trim($res[3])) <> 0) {
				$respuesta[0]['error'] = true;
				$respuesta[0]['errorcode'] = $res[1];
				$respuesta[0]['msg'] = "Mensaje de Error: " . $res[0] . ' ' . $res[1] . ' ' . $res[2] . ' ' . $res[3] . ' ' ;
				$txt = date('Y m d h:i:s') . '	' .'getSolicitudADCEV.php Error '. $query_rs_expediente  . ' res[0]' . $res[0] . ' res[1]' .$res[1] . ' res[2]' .$res[2]. ' res[3]' .$res[3];
				logErr($txt, '../logs/logs.txt');
			} else {
				return $expediente->fetchAll();
			}
		} catch (\Throwable $th) {
			$respuesta[0]['msg'] = $th->getMessage();
			$respuesta[0]['errorcode'] = 0;
			$respuesta[0]['error'] = true;
			$txt = date('Y m d h:i:s') . '	' .'getSolicitudADCEV.php catch '. $query_rs_expediente . ' ERROR ' . $th->getMessage();
			logErr($txt, '../logs/logs.txt');
		}	
		return $respuesta;
	}
	protected function desmebrarFecha($fecha_completa_inicial):array {
	
	$mesesN=array('Blanco',"enero","febrero","marzo","abril","mayo","junio","julio",
				 "agosto","septiembre","octubre","noviembre","diciembre");
	
	$diasN=array("cero","uno","dos","tres","cuatro","cinco","seis","siete",
				 "ocho","nueve","diez","once","doce","trece","catorce","quince","diez y seis","diez y siete","diez y ocho","diez y nueve",
				 "veinte","veinte y uno","veinte y dos","veinte y tres","veinte y cuatro","veinte y cinco","veinte y seis","veinte y siete","veinte y ocho","veinte y nueve","treinta","treinta y uno");

	$minutosN=array("cero",
					"uno",
					"dos",
					"tres",
					"cuatro",
					"cinco",
					"seis",
					"siete",
					"ocho",
					"nueve",
					"diez",
					"once",
					"doce",
					"trece",
				 	"catorce",
					"quince",
					"diez y seis",
					"diez y siete",
					"diez y ocho",
					"diez y nueve",
					"veinte",
					"veinte y uno",
					"veinte y dos",
					"veinte y tres",
					"veinte y cuatro",
					"veinte y cinco",
					"veinte y seis",
					"veinte y siete",
					"veinte y ocho",
					"veinte y nueve",
					"treinta",
					"treinta y uno",
					"treinta y dos",
					"treinta y tres",
					"treinta y cuatro",
					"treinta y cinco",
					"treinta y seis",
					"treinta y siete",
					"treinta y ocho",
					"treinta y nueve",
					"cuarenta",
					"cuarenta y uno",
					"cuarenta y dos",
					"cuarenta y tres",
					"cuarenta y cuatro",
					"cuarenta y cinco",
					"cuarenta y seis",
					"cuarenta y siete",
					"cuarenta y ocho",
					"cuarenta y nueve",
					"cincuenta",
					"cincuenta y uno",
					"cincuenta y dos",
					"cincuenta y tres",
					"cincuenta y cuatro",
					"cincuenta y cinco",
					"cincuenta y seis",
					"cincuenta y siete",
					"cincuenta y ocho",
					"cincuenta y nueve",
					"sesenta");
	$aniosN=array("cero","uno","dos","tres","cuatro","cinco","seis","siete",
				 "ocho","nueve","diez","once","doce","trece","catorce","quince","diez y seis","diez y siete","diez y ocho","diez y nueve",
				 "veinte","veinte y uno","veinte y dos","veinte y tres","veinte y cuatro","veinte y cinco","veinte y seis","veinte y siete","veinte y ocho","veinte y nueve","treinta","treinta y uno","treinta y dos","treinta y tres","treinta y cuatro","treinta y cinco","treinta y seis","treinta y siete","treinta y ocho","treinta y nueve","cuarenta","cuarenta y uno","cuanrenta y dos","cuarenta y tres","cuarenta y cuatro","cuarenta y cinco","cuarenta y seis","cuarenta y siete","cuarenta y ocho","cuarenta y nueve","cincuenta","cincuenta y uno");
	$fecha_desmebrada['fecha_completa']=  date("Y/m/d h:i:sa", strtotime($fecha_completa_inicial));
	$fecha_desmebrada['fecha_dma']=  date("d/m/Y", strtotime($fecha_completa_inicial));
	$fecha_desmebrada['fecha_hmsampm']=  date("h:i:sa", strtotime($fecha_completa_inicial));
	$fecha_desmebrada['anio_numerico']=  date("Y", strtotime($fecha_completa_inicial));
	$fecha_desmebrada['anio_letras']= $aniosN[substr(date("Y", strtotime($fecha_completa_inicial)),2,2)];
	$fecha_desmebrada['mes_numerica']=   date("m", strtotime($fecha_completa_inicial));
	$fecha_desmebrada['mes_letras']=   $mesesN[intval(date("m", strtotime($fecha_completa_inicial)))];
	$fecha_desmebrada['dia_numerico']=  date("d", strtotime($fecha_completa_inicial));
	$fecha_desmebrada['dia_letras']=   $diasN[intval(date("d", strtotime($fecha_completa_inicial)))];
	$fecha_desmebrada['hora_numerica']=   date("h", strtotime($fecha_completa_inicial));
	$fecha_desmebrada['hora_letras']=   $diasN[intval(date("h", strtotime($fecha_completa_inicial)))];
	$minutos_cero = date("i", strtotime($fecha_completa_inicial));
	$minutos = intval($minutos_cero);
	$fecha_desmebrada['minutos_numerico']=  $minutos_cero;
	$fecha_desmebrada['minutos_letras']=  $minutosN[$minutos];
	$segundos_cero = date("s", strtotime($fecha_completa_inicial));
	$segundos = intval($segundos_cero);
	$fecha_desmebrada['segundos_numerico']=  $segundos_cero;
	$fecha_desmebrada['segundos_letras']=   $minutosN[$segundos];
	$fecha_desmebrada['ampm'] = substr($fecha_completa_inicial,19,2);
	return $fecha_desmebrada;
}		
	//******************************************************************************/
	//* INICIO: Mover Datos del Registro al Arreglo Data
	//******************************************************************************/
	protected function moverData($Data,$row_rs_expediente,$vehiculos,$row_rs_Tarifa,$contador):mixed {
		//***********************************************************************************************
		//*Inicio
		//***********************************************************************************************
		//*Armando datos para generar aviso de cobro
		//***********************************************************************************************
		if ($contador == 0) {
			$Data[0]['MSGVIGENCIA'] =  'ESTE AVISO DE COBRO TIENE 2 DÍAS HABILES DE VIGENCIA, UNA VEZ VENCIDO SERA ANULADO Y SE HARA UNA INADMISIÓN DE OFICIO Y DEBERA PRESENTAR NUEVAMENTE EL TRAMITE';
			$Data[0]['Modulo'] = 15;
			$Data[0]['Usuario'] = 'avisosra';
			$Data[0]['Clave'] = 'IhTt@2o23%';
			$Data[0]['ID_Formulario_Solicitud_Encrypted'] = $row_rs_expediente['ID_Formulario_Solicitud_Encrypted'];
			$Data[0]['IPUsuario'] = $this->getIp();
			$Data[0]['Preforma'] = $row_rs_expediente['Preforma'];
			$Data[0]['Email'] = $row_rs_expediente['Email_Solicitante'];
			$Data[0]['Email_Apoderado'] = $row_rs_expediente['Email_Apoderado_Legal'];
			$Data[0]['RTN_Solicitante'] = $row_rs_expediente['RTN_Solicitante'];
			$Data[0]['ID_Solicitud'] = $row_rs_expediente['Preforma'];
			$Data[0]['ID_Expediente'] = $row_rs_expediente['Preforma'];
			$Data[0]['Preforma'] = $row_rs_expediente['Preforma'];
			$Data[0]['NombreSolicitante'] = $row_rs_expediente['NombreSolicitante'];
			$Data[0]['NombreApoderadoLega'] = $row_rs_expediente['NombreApoderadoLega'];
			If ($row_rs_expediente['N_Permiso_Especial'] != '') {
				$Data[0]['Certificado_Operacion'] = $row_rs_expediente['N_Permiso_Especial'];
			} else {
				$Data[0]['Certificado_Operacion'] = $row_rs_expediente['Certificado_Operacion'];
				$Data[0]['Permiso_Explotacion'] = $row_rs_expediente['Permiso_Explotacion'];
			}
			$Data[0]['ID_Placa'] = $vehiculos[0]['ID_Placa'];
			$Data[0]['usuario'] = $_SESSION["user_name"];//$_SESSION['usuario'];
			$Data[0]['Observacion'] = 'RENOVACIONES AUTOMATICAS';
			$Data[0]['Permiso_Explotacion'] = $row_rs_expediente['Permiso_Explotacion'];
			$Data[0]['Resolucion'] = '';
			$url_auto='';
			$url_aviso='Documentos/' . $row_rs_expediente['Preforma'] .'/' . 'AvisodeCobro_' . $row_rs_expediente['Preforma'] . '.pdf';
			$Data[0]['Auto_Ruta']=$url_auto;
			$Data[0]['Aviso_Ruta']=$url_aviso;
			$Data[0]['Monto_Total'] = 0;
		}
		// ***********************************************************************************************
		// Inicio: Datos por cada tramite
		// ***********************************************************************************************
		$Data[0]['Monto_Total'] = $Data[0]['Monto_Total'] +  $row_rs_Tarifa['Monto'];
		$Data[0]['Tramites'][$contador]['ID_tramite'] = $row_rs_expediente['ID_Tramite'];
		If ($row_rs_expediente['N_Permiso_Especial'] != '') {
			$Data[0]['Tramites'][$contador]['Certificado_Operacion'] = $row_rs_expediente['N_Permiso_Especial'];
		} else {
			$Data[0]['Tramites'][$contador]['Certificado_Operacion'] = $row_rs_expediente['Certificado_Operacion'];
		}
		$Data[0]['Tramites'][$contador]['Acronimo_Tramite'] = $vehiculos[0]['ID_Placa'];
		$Data[0]['Tramites'][$contador]['ID_Placa'] = $vehiculos[0]['ID_Placa'];
		$Data[0]['Tramites'][$contador]['Monto'] = $row_rs_Tarifa['Monto'];
		$Data[0]['Tramites'][$contador]['IDHistoricoTarifas'] = $row_rs_Tarifa['IDHistoricoTarifas'];
		$Data[0]['Tramites'][$contador]['Expediente_Det'] = $row_rs_expediente['Solicitud'];
		$Data[0]['Tramites'][$contador]['DescripcionDetalleImpresion'] = $row_rs_Tarifa['DESC_Tarifa'];
		// ***********************************************************************************************
		// Dependiendo de la si es certificado o permiso se ajusta la referencia del aviso de cobro
		// ***********************************************************************************************
		If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-01') {
			if ($row_rs_expediente['Acronimo_Tramite'] == 'R') {
				$Data[0]['Tramites'][$contador]['DescripcionDetalle'] = $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ' CON NÚMERO: ' . $row_rs_expediente['Permiso_Explotacion'] . $row_rs_expediente['Periodo-Explotacion'];
			} else {
				$Data[0]['Tramites'][$contador]['DescripcionDetalle'] = $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ' CON NÚMERO: ' . $row_rs_expediente['Permiso_Explotacion'];
			}
		} else {
			If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-02') {
				if ($row_rs_expediente['Acronimo_Tramite'] == 'R') {
					$Data[0]['Tramites'][$contador]['DescripcionDetalle'] = $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ' CON NÚMERO: ' . $row_rs_expediente['Certificado_Operacion'] . $row_rs_expediente['Periodo'];
				} else {
					$Data[0]['Tramites'][$contador]['DescripcionDetalle'] = $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ' CON NÚMERO: ' . $row_rs_expediente['Certificado_Operacion'];
				}
			} else {        
				If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-03') {
					if ($row_rs_expediente['Acronimo_Tramite'] == 'R') {
						$Data[0]['Tramites'][$contador]['DescripcionDetalle'] = $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite']  . ' CON NÚMERO: ' . $row_rs_expediente['N_Permiso_Especial'] . $row_rs_expediente['Periodo'];
					} else {
						$Data[0]['Tramites'][$contador]['DescripcionDetalle'] = $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite']  . ' CON NÚMERO: ' . $row_rs_expediente['N_Permiso_Especial'];
					}
				} else {
					If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-08') {
						$Data[0]['Tramites'][$contador]['DescripcionDetalle'] = $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite']  . ' A NUEVO NÚMERO DE PLACA: ' . $vehiculos[0]['ID_Placa'];
					} else {
						If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-15') {
							$Data[0]['Tramites'][$contador]['DescripcionDetalle'] = $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite']  . ' A NUEVO NÚMERO DE PLACA: ' . $vehiculos[0]['ID_Placa'];
						} else {
							If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-17') {
								$Data[0]['Tramites'][$contador]['DescripcionDetalle'] = $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite']  . ' A NUEVO NUMERO DE MOTOR: ' . $vehiculos[0]['Motor'] ;
							} else {
								If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-18') {
									$Data[0]['Tramites'][$contador]['DescripcionDetalle'] = $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ' A NUEVO COLOR: ' . $vehiculos[0]['DESC_Color'] ;
								} else {
									$Data[0]['Tramites'][$contador]['DescripcionDetalle'] = $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite']  . ' A NUEVO NUMERO DE CHASIS: ' . $vehiculos[0]['Chasis'] ;
								}
							}
						}
					}
				}
			}
		}
		$Data[0]['Tramitex'] = (isset($Data[0]['Tramitex'])? $Data[0]['Tramitex']: '<br/>') . $Data[0]['Tramites'][$contador]['DescripcionDetalle']  . '<br/>';
		return $Data;
		//***********************************************************************************************
		//* final: Datos por cada tramite
		//***********************************************************************************************
	}
	//******************************************************************************/
	//* FINAL: Mover Datos del Registro al Arreglo Data
//******************************************************************************/


	function pdfAvisodeCobro ($Data,$cfg_institucion= 'INSTITUTO HONDUREÑO DEL TRANSPORTE TERRESTRE') {
		//****************************************************************************//
		//Definiendo encabezado de aviso de cobro //
		//****************************************************************************//
		$enc_avi_cob = '<table style="border:4px solid #58d1e2;" height="100%" width="100%">
		<tr><td colspan="2">&nbsp;</td></tr>
		@@__MSGVIGENCIA__@@
		<tr>
			<td align="left" width="60%"><img height="30%" width="70%" alt="encabezado" src="assets/images/encabezado-pagina1.png"></td>
			<td align="left" width="40%">
				<table style="border:2px solid #58d1e2;" "100%" height="100%" width="100%">
				<tr><td align="left" colspan="2"><strong>RENOVACIONES AUTOMATICAS</strong></td></tr>
				<tr><td align="left" style="border:2px solid #58d1e2;"><strong>AVISO DE COBRO:</strong></td><td align="right" style="border:2px solid #58d1e2;"><strong>@@__ACO__@@</strong></td></tr>
				<tr><td align="left" style="border:2px solid #58d1e2;"><strong>FECHA EMISIÓN</strong>:</td><td align="right" style="border:2px solid #58d1e2;"><strong>@@__FEM__@@</strong></td></tr>
				<tr><td align="left" style="border:2px solid #58d1e2;"><strong>FECHA VENCIMIENTO:</strong></td><td align="right" style="border:2px solid #58d1e2;"></td></tr>
				<tr><td align="left" colspan="2"><strong>DETALLE AVISO DE COBRO</strong></td></tr>
				</table>
			</td>
		</tr>
		</table>';
		$fecha_emision = date("Y/m/d");
		$enc_avi_cob = str_replace('@@__FEM__@@',$fecha_emision,$enc_avi_cob);
		$enc_avi_cob = str_replace('@@__ACO__@@',$Data[0]['Numero_Aviso'],$enc_avi_cob);
		$enc_avi_cob = str_replace('@@__MSGVIGENCIA__@@','<tr style="border:3px solid #E80646;"><td style="border:3px solid #E80646;" colspan="2"><p style="text-align: justify; font-family: Cambria, "Hoefler Text", "Liberation Serif", Times, "Times New Roman", "serif"; font-size: 18; color:#E80646"><strong>'.$Data[0]['MSGVIGENCIA'].'</strong></p></td></tr>',$enc_avi_cob);
		//****************************************************************************//
		//Recuperando template de aviso de cobro //
		//****************************************************************************//
		$template = $this->getTemplate(5);
				// Institucion
		$template = str_replace('@@institucion@@',$cfg_institucion,$template);
		$template = str_replace('@@__RTN__@@',$Data[0]['RTN_Solicitante'],$template);
		$template = str_replace('@@__NOMCON__@@',$Data[0]['NombreSolicitante'],$template);
		$template = str_replace('@@__OBSERVACIONES__@@',$Data[0]['Observacion'],$template);
		$template = str_replace('@@__TOTAL__@@','L ' . number_format($Data[0]['Monto_Total'],2),$template);
		$template = str_replace('@@__CERTIFICADO__@@',$Data[0]['Certificado_Operacion'],$template);
		$template = str_replace('@@__PERMISO__@@',$Data[0]['Permiso_Explotacion'],$template);	
		$template = str_replace('@@__PLACA__@@',$Data[0]['ID_Placa'],$template);
		$template = str_replace('@@__EXPEDIENTE__@@',$Data[0]['ID_Expediente'],$template);	
			
		$template = str_replace('@@__CERTPERM__@@','|Solicitud-> ' . $Data[0]['ID_Solicitud'] . ' |Permiso-> ' . $Data[0]['Permiso_Explotacion'],$template);
		$row = '';
		$contador = 1;
		foreach ($Data[0]['Tramites'] as $DetTramite){
			$row = $row . '<tr><td width="5%" style="font-family: Cambria, "Hoefler Text", "Liberation Serif", Times, "Times New Roman", "serif"; font-size: 10" align="left">'. $contador . '</td><td width="25%" style="font-family: Cambria, "Hoefler Text", "Liberation Serif", Times, "Times New Roman", "serif"; font-size: 10" align="left">' . $DetTramite['ID_tramite']. '</td><td width="50%" style="font-family: Cambria, "Hoefler Text", "Liberation Serif", Times, "Times New Roman", "serif"; font-size: 10" align="left">' .  $DetTramite['DescripcionDetalle'] . '</td><td width="20%" style="font-family: Cambria, "Hoefler Text", "Liberation Serif", Times, "Times New Roman", "serif"; font-size: 10" align="right">L ' . number_format($DetTramite['Monto'],2) . '</td></tr>';	
			$contador++;
		}
		$template = str_replace('@@__ROWTRAMITES___@@',$row,$template);	
		// Generando llave privada
		$llave_publica = hash('SHA512','/I(h$T@t%&)' . $Data[0]['Numero_Aviso']. date("Y/m/d h:i:sa"),false);
		// GENERAMOS EL CODIGO QR de la Validacion Firma
		$URL = $this->dominio_raiz .":150/api_rep.php?action=get-facturaPdf&nu=".$Data[0]['Numero_Aviso']."&usu=".$Data[0]['usuario'];
		QRcode::png($URL,"../qr/temp/".$Data[0]['Numero_Aviso'].".png",QR_ECLEVEL_M,5,4);
		//$pdf->Image("qr/temp/".$validacion_firma.".png",'90','214','25','25','PNG');
		$template = str_replace('../../qr/temp/CQR.PNG',$this->dominio_raiz. ":285/qr/temp/".$Data[0]['Numero_Aviso'].".png",$template);
		//$mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-L']);
		try {
			$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [215.9, 355.6],'tempDir' => sys_get_temp_dir()]);
			$mpdf->pdf_version = '1.7';
			$mpdf->SetTitle('Aviso de Cobro');
			$mpdf->SetAuthor('Instituto Hondureño de Transporte Terrestre');
			$mpdf->SetCreator('SATT');
			$mpdf->SetSubject('Documentos Legales IHTT');
			$mpdf->PDFXauto = true;
			$mpdf->SetWatermarkText("IHTT");
			$mpdf->showWatermarkText = true;
			$mpdf->watermark_font = 'DejaVuSansCondensed';
			$mpdf->watermarkTextAlpha = 0.10;
			$mpdf->margin_header = 0;
			$mpdf->SetMargins(0, 0, 75);
			$mpdf->SetAutoPageBreak(true, 30);
			$mpdf->SetHTMLHeader($enc_avi_cob,'O', true);
			$style="text-align:center;vertical-align: middle;";  
			$mpdf->SetHTMLFooter('<table width="100%"; height="60px"><tr><td width="20%" align="left">Página(s): {PAGENO} de {nbpg}</td>
			<td width="20%" align="center">' . date("Y/m/d h:i:sa") . '</td><td width="20%" style="'. $style .'"><img src="assets/images/xsc.jpg" alt="Xiomara Si Cumple" width="75px" height="75px"></td><td width="20%" align="center">Aviso: '.$Data[0]['Numero_Aviso'] . ' -RA-<td width="20%" align="right">'. $Data[0]['usuario'] .'</td></tr></table>');
			$mpdf->WriteHTML($template);
			$ruta='Documentos/' . $Data[0]['Preforma'] .'/' . 'AvisodeCobro_' . $Data[0]['Preforma'] . '.pdf';
			$mpdf->Output($ruta, \Mpdf\Output\Destination::FILE);
			return true;
		} catch (\Mpdf\MpdfException $e) {
			echo "mPDF Error: " . $e->getMessage();
		} catch (\Exception $e) {
			echo "General Error: " . $e->getMessage();
		}
	}	
	//***********************************************************************************************
	//* FINAL: FUNCION FINAL QUE GENERA EL ARCHIVO PDF
	//***********************************************************************************************

	//***********************************************************************************************
	//* FINAL: RECUPERACION CERTIFCIADO ACTUAL
	//***********************************************************************************************
	protected function getCertificadoActual ($rs_id_rs_concesion,$id_clase_servico):array {
		//***********************************************************************/
		//Si el proceso es de Certificado de Operación de Carga
		//***********************************************************************/
		if ($id_clase_servico == 'STPC') {
			$query_rs_concesion = "SELECT Con.N_Certificado  as Concesion,Con.ID_Vehiculo_Carga as ID_Vehiculo,vehp.[ID_Placa],
			Veh.ID_Marca,veh.Anio,veh.ID_Tipo_Vehiculo_Carga as ID_Tipo_Vehiculo,tv.DESC_Tipo_Vehiculo,Veh.ID_Color,Veh.Motor,Veh.Chasis,Veh.VIN,
			Veh.Alto,Veh.Ancho,Veh.Largo,Veh.Combustible,con.Fecha_Expiracion,Expl.Fecha_Vencimiento as Fecha_Expiracion_Explotacion,Expl.N_Permiso_Explotacion_Encrypted as Permiso_Explotacion_Encrypted,
			con.N_Certificado_Encrypted as Concesion_Encrypted,Expl.N_Permiso_Explotacion as Permiso_Explotacion
			FROM [IHTT_SGCERP].[dbo].[TB_Permiso_Explotacion_Carga] Expl,
			[IHTT_SGCERP].[dbo].[TB_Certificado_Carga] Con,
			[IHTT_SGCERP].[dbo].[TB_Vehiculo_Transporte_Carga] Veh,
			[IHTT_SGCERP].[dbo].[TB_Tipo_Vehiculo_Transporte_Carga] tv,
			[IHTT_SGCERP].[dbo].[TB_Vehiculo_Transporte_Carga_x_Placa] vehp
			where Expl.N_Permiso_Explotacion = Con.N_Permiso_Explotacion and
			Con.ID_Vehiculo_Carga = Veh.ID_Vehiculo_Carga and  veh.ID_Tipo_Vehiculo_Carga = tv.ID_Tipo_Vehiculo_Carga and
			Con.ID_Vehiculo_Carga = vehp.ID_Vehiculo_Carga and vehp.Estado = 'ACTIVA' AND N_Certificado = :Concesion;";
		} else {
			if ($id_clase_servico == 'STEC') {
				//***********************************************************************/
				//Si el proceso es de Permiso Especial de Carga
				//***********************************************************************/
				$query_rs_concesion = "SELECT Con.N_Permiso_Especial_Carga as Concesion,Con.ID_Vehiculo_Carga as ID_Vehiculo,vehp.[ID_Placa],
				Veh.ID_Marca,veh.Anio,veh.ID_Tipo_Vehiculo_Carga as ID_Tipo_Vehiculo,tv.[DESC_Tipo_Vehiculo],Veh.ID_Color,Veh.Motor,Veh.Chasis,Veh.VIN,
				Veh.Alto,Veh.Ancho,Veh.Largo,Veh.Combustible,con.Fecha_Expiracion,Con.N_Permiso_Especial_Carga_Encrypted as Concesion_Encrypted
				FROM 
				[IHTT_SGCERP].[dbo].[TB_Permiso_Especial_Carga] Con,
				[IHTT_SGCERP].[dbo].[TB_Vehiculo_Transporte_Carga] Veh,
				[IHTT_SGCERP].[dbo].[TB_Tipo_Vehiculo_Transporte_Carga] tv,
				[IHTT_SGCERP].[dbo].[TB_Vehiculo_Transporte_Carga_x_Placa] vehp
				where Con.ID_Vehiculo_Carga = Veh.ID_Vehiculo_Carga and  veh.ID_Tipo_Vehiculo_Carga = tv.ID_Tipo_Vehiculo_Carga and 
				Con.ID_Vehiculo_Carga = vehp.ID_Vehiculo_Carga and vehp.Estado = 'ACTIVA' AND N_Permiso_Especial_Carga = :Concesion;";
			} else {
				if ($id_clase_servico == 'STPP') {
					$query_rs_concesion = "SELECT Con.N_Certificado  as Concesion,Con.ID_Vehiculo_Transporte as ID_Vehiculo,vehp.[ID_Placa],
					Veh.ID_Marca,veh.Anio,veh.ID_Tipo_Vehiculo_Transporte_Pas as ID_Tipo_Vehiculo,tv.DESC_Tipo_Vehiculo_Transporte_Pas as DESC_Tipo_Vehiculo,
					Veh.ID_Color,Veh.Motor,Veh.Chasis,Veh.VIN,Veh.Combustible,con.Fecha_Expiracion,
					Expl.Fecha_Vencimiento as Fecha_Expiracion_Explotacion,Expl.N_Permiso_Explotacion_Encrypted as Permiso_Explotacion_Encrypted,
					Con.N_Certificado_Encrypted as Concesion_Encrypted,Expl.N_Permiso_Explotacion as Permiso_Explotacion
					FROM 
					[IHTT_SGCERP].[dbo].[TB_Permiso_Explotacion_Pas] Expl,
					[IHTT_SGCERP].[dbo].[TB_Certificado_Pasajeros] Con,
					[IHTT_SGCERP].[dbo].[TB_Vehiculo_Transporte_Pasajero] Veh,
					[IHTT_SGCERP].[dbo].[TB_Tipo_Vehiculo_Transporte_Pasajero] tv,
					[IHTT_SGCERP].[dbo].[TB_Vehiculo_Transporte_Pasajero_x_Placa] vehp
					where Expl.N_Permiso_Explotacion = Con.N_Permiso_Explotacion and
					Con.ID_Vehiculo_Transporte = Veh.ID_Vehiculo_Transporte and veh.ID_Tipo_Vehiculo_Transporte_Pas = tv.ID_Tipo_Vehiculo_Transporte_Pas and
					Con.ID_Vehiculo_Transporte = vehp.ID_Vehiculo_Transporte and vehp.Estado = 'ACTIVA' AND N_Certificado = :Concesion;";
				} else {
					//***********************************************************************/
					//Si el proceso es de Permiso Especial de Pasajeros
					//***********************************************************************/
					$query_rs_concesion = "SELECT Con.N_Permiso_Especial_Pas  as Concesion,Con.ID_Vehiculo_Transporte as ID_Vehiculo,vehp.[ID_Placa],
					Veh.ID_Marca,veh.Anio,veh.ID_Tipo_Vehiculo_Transporte_Pas as ID_Tipo_Vehiculo,tv.DESC_Tipo_Vehiculo_Transporte_Pas as DESC_Tipo_Vehiculo,
					Veh.ID_Color,Veh.Motor,Veh.Chasis,Veh.VIN,Veh.Combustible,con.Fecha_Expiracion,Con.N_Permiso_Especial_Pas_Encrypted as Concesion_Encrypted
					FROM 
					[IHTT_SGCERP].[dbo].[TB_Permiso_Especial_Pas] Con,
					[IHTT_SGCERP].[dbo].[TB_Vehiculo_Transporte_Pasajero] Veh,
					[IHTT_SGCERP].[dbo].[TB_Tipo_Vehiculo_Transporte_Pasajero] tv,
					[IHTT_SGCERP].[dbo].[TB_Vehiculo_Transporte_Pasajero_x_Placa] vehp
					where Con.ID_Vehiculo_Transporte = Veh.ID_Vehiculo_Transporte and veh.ID_Tipo_Vehiculo_Transporte_Pas = tv.ID_Tipo_Vehiculo_Transporte_Pas and
					Con.ID_Vehiculo_Transporte = vehp.ID_Vehiculo_Transporte and vehp.Estado = 'ACTIVA' AND N_Permiso_Especial_Pas = :Concesion;";
				}
			}
		}
	
		$respuesta[0]['msg'] = "";
		$respuesta[0]['error'] = false;	
		$respuesta[0]['errorcode'] = '';
		try {
			// Recueprando la información del expediente
			$concesion = $this->db->prepare($query_rs_concesion);
			$res = $concesion->execute(Array(':Concesion' => $rs_id_rs_concesion));
			$res = $concesion->errorInfo();
			if (isset($res) and isset($res[3]) and intval(trim($res[3])) <> 0) {
				$respuesta[0]['error'] = true;
				$respuesta[0]['errorcode'] = $res[1];
				$respuesta[0]['msg'] = "Mensaje de Error: " . $res[0] . ' ' . $res[1] . ' ' . $res[2] . ' ' . $res[3] . ' ' ;
				$txt = date('Y m d h:i:s') . '	' .'getCertificadoActual.php Error '. $query_rs_concesion  . ' res[0]' . $res[0] . ' res[1]' .$res[1] . ' res[2]' .$res[2]. ' res[3]' .$res[3];
				logErr($txt, '../logs/logs.txt');
				return $respuesta;
			} else {
				$respuesta[0]['query'] = $query_rs_concesion . ' concesion: ' . $rs_id_rs_concesion;
				$respuesta[0]['clase_servicio'] = $id_clase_servico;            
				$record = $concesion->fetch();
				//****************************************************************************//	
				// Si se tramite el tramite de renocación del certificado se calcula la nueva 
				// fecha de vencimiento
				//****************************************************************************//	
				$renovacion_certificado_vencido = false;
				$renovacion_permiso_especial_vencido = false;
				$permisoexplotacion_vencido = false;
				if (isset($record["Fecha_Expiracion"])) {
					$Nueva_Fecha_Expiracion = date('Y-m-d',strtotime($record["Fecha_Expiracion"]));
					$hoyplus60 = date('Y-m-d', strtotime('+60 days'));
					$contadorconcesion=1;
					while ($Nueva_Fecha_Expiracion <= $hoyplus60) {
						$record['rencon'][$contadorconcesion]['periodo'] = ' del ' . $Nueva_Fecha_Expiracion;
						if ($id_clase_servico == 'STPC' or $id_clase_servico == 'STPP') {
							$Nueva_Fecha_Expiracion = date("Y-m-d",strtotime($Nueva_Fecha_Expiracion."+ 3 years"));
							$renovacion_certificado_vencido = true;
						} else {
							$Nueva_Fecha_Expiracion = date("Y-m-d",strtotime($Nueva_Fecha_Expiracion."+ 1 year"));
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
						$Nueva_Fecha_Expiracion = date('Y-m-d',strtotime($record["Fecha_Expiracion_Explotacion"]));
						$hoyplus60 = date('Y-m-d', strtotime('+60 days'));
						$contadorpermisoexplotacion=1;
						while ($Nueva_Fecha_Expiracion <= $hoyplus60) {
							$record['renperexp'][$contadorpermisoexplotacion]['periodo'] = ' del ' . $Nueva_Fecha_Expiracion;
							$Nueva_Fecha_Expiracion = date("Y-m-d",strtotime($Nueva_Fecha_Expiracion."+ 12 years"));
							$record['renperexp'][$contadorpermisoexplotacion]['periodo'] = $record['renperexp'][$contadorpermisoexplotacion]['periodo'] . ' al ' . $Nueva_Fecha_Expiracion;
							$permisoexplotacion_vencido = true;                
							$contadorpermisoexplotacion++;
						}
						$record['renper-explotacion-cantidad'] = $contadorpermisoexplotacion;
						$record['Nueva_Fecha_Expiracion_Explotacion'] = $Nueva_Fecha_Expiracion;
					} else {
						$record['Nueva_Fecha_Expiracion_Explotacion'] = '';
						$record["Fecha_Expiracion_Explotacion"] = '';
						$record['renper-explotacion-cantidad'] = 0;
					}
	
					$record['permisoexplotacion_vencido'] = $permisoexplotacion_vencido;
					$record['renovacion_permiso_especial_vencido'] = $renovacion_permiso_especial_vencido;
					$record['renovacion_certificado_vencido'] = $renovacion_certificado_vencido;
				} else {
					$record['Nueva_Fecha_Expiracion_Explotacion'] = '';
					$record["Fecha_Expiracion_Explotacion"] = '';
					$record['Nueva_Fecha_Expiracion'] = '';
					$record["Fecha_Expiracion"] = '';
					$record['permisoexplotacion_vencido'] = $permisoexplotacion_vencido;
					$record['renovacion_permiso_especial_vencido'] = $renovacion_permiso_especial_vencido;
					$record['renovacion_certificado_vencido'] = $renovacion_certificado_vencido;
				}
	
				$respuesta[1] = $record;
				return $respuesta;
			}
		} catch (\Throwable $th) {
			$respuesta[0]['msg'] = 'getCertificadoVehiculo catch ' . $th->getMessage();
			$respuesta[0]['errorcode'] = 0;
			$respuesta[0]['error'] = true;
			$txt = date('Y m d h:i:s') . '	' .'getCertificadoActual.php catch '. $query_rs_concesion . ' ERROR ' . $th->getMessage();
			logErr($txt, '../logs/logs.txt');
			return $respuesta;
		}	
	}
	//***********************************************************************************************
	//* FINAL: RECUPERACION CERTIFCIADO ACTUAL
	//***********************************************************************************************

	//***********************************************************************************************
	//* INICIO: RECUPERACION EMPLEADO
	//***********************************************************************************************
	protected function getEmpleado($Usuario_Nombre)	{
		//***********************************************************************************************************/
		//* rbthaofic@gmail.com 2023/03/04 Pendiente de finalización (recuperar el area del empleados)
		//* Inicio: Agregar usuario que realiza la acción y la ciudad donde se ubica el usuario
		//***********************************************************************************************************/
		$query = "select E.Nombres,E.Apellidos from [IHTT_USUARIOS].[dbo].[TB_USUARIOS] U,[IHTT_RRHH].[dbo].[TB_Empleados] E
		WHERE U.Usuario_Nombre = :Usuario_Nombre and u.ID_Empleado = E.ID_Empleado";
		$p = array(":Usuario_Nombre" => $Usuario_Nombre);
		return $this->select($query, $p);
	}	
	//***********************************************************************************************
	//* FINAL: RECUPERACION EMPLEADO
	//***********************************************************************************************

	//***********************************************************************************************
	//* Inicio: FUNCION INICIAL DE GENERACION DE PDF
	//***********************************************************************************************
	protected function PDFAvisodeCobroVentanillaApi($rs_id_rs_solicitud,$rs_id_rs_template=5):array {
		$esCobroPeriodosAtrasados = true;
		$formulario_encriptado = '';
		$usuario_acepta = '';
		$cfg_institucion = 'INSTITUTO HONDUREÑO DEL TRANSPORTE TERRESTRE';
		/**************************************************************************************************/
		// Inicio-> Recuperando IP de la conexión
		/*************************************************************************************************/	
		// Bandera de Error
		$Error_Proceso = false;
		// Variable para almacenar el contenido del pdf que generara este programa
		$pagina_inicio='';
		$tramitepeticion = '';
		$url_aviso = '';
		$tramite = '';
		$RTN='';
		$ID_ColegiacionAPL='';
		$msg = '';
		$Numero_Aviso=0;
		// Funcion que recupera los datos para insertar en el template
		$row_rs_todos_los_registros =  $this->getSolicitudADCEV($rs_id_rs_solicitud,$rs_id_rs_template);
		$monto_total = 0;
		$contador=0;
		$Data=Array();
		$total_registros = count($row_rs_todos_los_registros);
		//echo '$total_registros' . $total_registros . '<br>';die();
		$ConcesionValue = '';
		foreach ($row_rs_todos_los_registros as $row_rs_expediente){
			$formulario_encriptado = $row_rs_expediente['ID_Formulario_Solicitud_Encrypted'];
			$usuario_acepta  = $row_rs_expediente['Usuario_Acepta'];
			$RTN = $row_rs_expediente['RTN_Solicitante'];
			$ID_ColegiacionAPL = $row_rs_expediente['ID_ColegiacionAPL'];			
			// Si se recuperaron datos del expediente procesar
			if ($row_rs_expediente['ID_Tramite'] != '') {
				$preforma = $row_rs_expediente['Preforma'];
				if ($ConcesionValue != ($row_rs_expediente['Certificado_Operacion'] != '')? $row_rs_expediente['Certificado_Operacion'] : $row_rs_expediente['N_Permiso_Especial']) {
					$vehiculos = $this->getVehiculosPreforma($rs_id_rs_solicitud,($row_rs_expediente['Certificado_Operacion'] != '')? $row_rs_expediente['Certificado_Operacion'] : $row_rs_expediente['N_Permiso_Especial']);
					if (isset($vehiculos[0])) {
						$vehiculoentra = "MARCA: " . $vehiculos[0]['DESC_Marca'] . ", MODELO: " . $vehiculos[0]['Modelo'] . ", COLOR: " . $vehiculos[0]['DESC_Color']. ", MOTOR: " . $vehiculos[0]['Motor']. ", CHASIS: " . $vehiculos[0]['Chasis'] . "Y NÚMERO DE PLACA : " . $vehiculos[0]['ID_Placa'];
						if (isset($vehiculos[1])) {
							$vehiculosale = "MARCA: " . $vehiculos[1]['DESC_Marca'] . ", MODELO: " . $vehiculos[1]['Modelo'] . ", COLOR: " . $vehiculos[1]['DESC_Color']. ", MOTOR: " . $vehiculos[1]['Motor']. ", CHASIS: " . $vehiculos[1]['Chasis'] . "Y NÚMERO DE PLACA " . $vehiculos[1]['ID_Placa'];
						}                    
					}        
					$ConcesionValue = ($row_rs_expediente['Certificado_Operacion'] != '')? $row_rs_expediente['Certificado_Operacion'] : $row_rs_expediente['N_Permiso_Especial'];
				}
				// Recuperando la tarifa del tramite
				$row_rs_Tarifa = $this->getTarifa($row_rs_expediente['ID_Tramite']);
				// Si encontro la tarifa del tramite prosiga
				If (isset($row_rs_Tarifa['Monto'])) {			
					$row_rs_expediente['Solicitud'] = $rs_id_rs_solicitud;
					$concesionx = $this->getCertificadoActual (($row_rs_expediente['Certificado_Operacion'] != '')? $row_rs_expediente['Certificado_Operacion'] : $row_rs_expediente['N_Permiso_Especial'],$row_rs_expediente['ID_Clase_Servicio']);
					$concesion = $concesionx[1];
					if (isset($concesion['rencon']) && isset($concesion['rencon'][1]) && isset($concesion['rencon'][1]['periodo'])){
						$row_rs_expediente['Periodo'] = $concesion['rencon'][1]['periodo'];
					}
					If ($row_rs_expediente['Permiso_Explotacion'] != '') {
						if (isset($concesion['renper-explotacion-cantidad']) && isset($concesion['renper-explotacion-cantidad'][1]) && isset($concesion['renper-explotacion-cantidad'][1]['periodo'])){
							$row_rs_expediente['Periodo-Explotacion'] = $concesion['renperexp'][1]['periodo'];
						}
					}
					//***********************************************************************************/
					//Moviendo data para armar arreglo de datos para generar aviso de cobro
					//***********************************************************************************/
					$Data = $this->moverData($Data,$row_rs_expediente,$vehiculos,$row_rs_Tarifa,$contador);  
					//*********************************************************************************************************/
					// Si esta habilitado el cobro de periodos atrasados y el tipo de tramite es Renovaciones (IHTTTRA-02)
					//*********************************************************************************************************/
					if ($esCobroPeriodosAtrasados == true && $row_rs_expediente['ID_Tipo_Tramite'] == 'IHTTTRA-02')          {
						//**********************************************************************************************************************************************/
						// Si hay más de un periodo de cobrar de Co y PES comienza a parir del 2 en este ciclo porque el segundo periodo ya quedo en el primer ciclo
						// primer ciclo ya fue procesado anteriormente
						//**********************************************************************************************************************************************/
						if ($concesion['rencon-cantidad'] > 1 && ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-02' || $row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-03'))   {
							for ($i=2; $i<$concesion['rencon-cantidad']; $i++) {
								$contador++;
								//***********************************************************************************/
								//Recuperando el rotulo del periodo a renovar de la concesion
								//***********************************************************************************/
								$row_rs_expediente['Periodo'] = $concesion['rencon'][$i]['periodo'];
								//***********************************************************************************/
								//Moviendo data para armar arreglo de datos para generar aviso de cobro
								//***********************************************************************************/
								$Data = $this->moverData($Data,$row_rs_expediente,$vehiculos,$row_rs_Tarifa,$contador);
							}
						} else {
						//**********************************************************************************************************************************************/
						// Si hay más de un periodo de cobrar de Per Exp comienza a parir del 2 en este ciclo porque el segundo periodo ya quedo en el primer ciclo
						// primer ciclo ya fue procesado anteriormente
						//**********************************************************************************************************************************************/  
							if ($concesion['renper-explotacion-cantidad'] > 1 && $row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-01')   {                        
								for ($i=2; $i<$concesion['renper-explotacion-cantidad']; $i++) {
									$contador++;
									//***********************************************************************************/
									//*Recuperando el rotulo del periodo a renovar de la concesion
									//***********************************************************************************/
									$row_rs_expediente['Periodo-Explotacion'] = $concesion['renper-explotacion-cantidad'][$i]['periodo'];
									$Data = $this->moverData($Data,$row_rs_expediente,$vehiculos,$row_rs_Tarifa,$contador);
								}
							}
						}
					}
					$contador++;
				} else {
					$msg = 'Error obteniedo la tarifa. Funcion getTarifa ' . $row_rs_Tarifa['msg'];
					$Error_Proceso = true;
					break;
				}
			} else {
				$msg = 'Error en la busqueda de la Solicitud row_rs_expediente[ID_Tramite]';
				$Error_Proceso = true;
				break;
			}
	
		}
	
		if ($contador == 0 and $msg == '') {
			$respuesta[0]['error'] = true;
			$respuesta[0]['msg'] = 'getSolicitudResolucion: NO HAY TRAMITES ASOCIADOS A ESTE EXPEDIENTE';
			$Error_Proceso = true;
		} else {
			if ($msg != '') {
				$respuesta[0]['error'] = true;
				$respuesta[0]['msg'] = $msg;
			}
		}
	
		//***********************************************************************************/
		// Si se proceso bien todo genere pdf
		//***********************************************************************************/
		if ($Error_Proceso == false) {
			// Si todo va bien, insertar aviso de cobro
			if (isset($Data)){
				$Data[0]['MontoLetras'] = $this->convertirMontoaLetras($Data[0]['Monto_Total']);
				//***********************************************************************************/
				//Salvar el aviso de cobro
				//***********************************************************************************/
				$respuesta_aviso = $this->saveAvisoCobro($Data);
			} 
			//***********************************************************************************/
			//Sino hubo errar al Salvar el aviso de cobro
			//***********************************************************************************/
			if ($respuesta_aviso[0]['error'] == false) {
				//***********************************************************************************/
				//Recuperando template de notifiacion //
				//***********************************************************************************/
				$Data[0]['Template'] = 	$this->getTemplate(4);
				//***********************************************************************************/
				// Agregando el numero de aviso de cobro recuperado al retornar saveAvisoCobro
				//***********************************************************************************/
				$Data[0]['Numero_Aviso'] = $respuesta_aviso[0]['CodigoAvisoCobro'];
				//***********************************************************************************/
				// Ruta PDF Aviso de Cobro
				//***********************************************************************************/
				//$url_aviso_calificada = 'https://satt2.transporte.gob.hn:90/api_rep.php?ra=S&action=get-facturaPdf&nu=' . $respuesta_aviso[0]['CodigoAvisoCobro'] ;
				$url_aviso_calificada =  'Documentos/' . $Data[0]['Preforma'] .'/' . 'AvisodeCobro_' . $Data[0]['Preforma'] . '.pdf';                
				//***********************************************************************************/
				// Agregando el numero de aviso de cobro recuperado al retornar saveAvisoCobro
				//***********************************************************************************/
				$Data[0]['Ruta'] = '';
				//***********************************************************************************/
				// Generar Pdf de Aviso de Cobro
				//***********************************************************************************/
				// Ya no se generara el documento de aviso de cobro, se generara en tiempo real
				// RTBM 2024-04-22  RBTHAOFIC@GMAIL.COM
				$this->pdfAvisodeCobro($Data);
				// Enviando Notitifación por correo electronico al apoderado legal
				$Data[0]['Titulo_Correo'] = 'AVISO DE COBRO No.- ' . $Data[0]['Numero_Aviso'];	
				// Deshabilitada la notificación por correo electronico deshabilitada
				$respuesta = $this->enviarNotificacion($Data);
				$response['numero_aviso'] = $Data[0]['Numero_Aviso'];
				$response['msg'] = 'IMPRIMIR AVISO DE COBRO NO:'. $Data[0]['Numero_Aviso'];
				$response['url_aviso'] = $url_aviso_calificada;
				$response['formulario_encriptado'] = $formulario_encriptado;
				$response['usuario_acepta'] = $usuario_acepta;	
				$response['RTN_Solicitante'] = $RTN;	
				$response['ID_ColegiacionAPL'] = $ID_ColegiacionAPL;	
			} else {
				$response['ERROR'] = true;
				$response['msg'] = 'saveAvisoCobro ' . $respuesta_aviso[0]['msg'];
				$response['numero_aviso'] = '';
				$response['url_aviso'] = '';
			}
			return $response;
		} else {
			$response['ERROR'] = true;
			$response['msg'] = $respuesta[0]['msg'];
			$response['numero_aviso'] = '';
			$response['url_aviso'] = '';
			return $response;
		}
	}
	//***********************************************************************************************
	//* Final: Generar Aviso de Cobro
	//***********************************************************************************************
}

if (!isset($_SESSION["ID_Usuario"]) || !isset($_SESSION["user_name"])) {
	echo json_encode(array("error" => 1000, "errorhead" => "INICIO DE SESSIÓN", "errormsg" => 'NO HAY UNA SESSION INICIADA, FAVOR INICIE SESION Y VUELVA A INTENTARLO'));
} else {
	$api = new Api_Ram($db,
			$appcfg_Dominio,
		$appcfg_Dominio_Corto,
		$appcfg_Dominio_Raiz,
		$appcfg_smtp_server,
		$appcfg_smtp_port,
		$appcfg_smtp_user,
		$appcfg_smtp_password);
}

https://satt2.transporte.gob.hn:293/api_rep.php?action=get-PDFComprobante&Solicitud=ff59d187c38b13b562a50194420a15eee44df2f27105d1e48f0363dc8323816fafe325f8ae8c82b303371933e0b9505141ef117e0716f3895f7457d57a6592f2&fls=RAM-20250228-000000059&Nombre_Usuario=ORDO%C3%91EZ%20BARAHONA%20CARLOS%20RIDEL&Cod_Usuario=cordonez&Originano_En_Ventanilla=1&ID_Usuario=1059&user_name=ccaballero