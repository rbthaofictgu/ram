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

 class Api_Exp
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
	protected $appcfg_estado_inicial;
	protected $appcfg_estado_inicial_descripcion;
	public function __construct($db,$appcfg_Dominio,$appcfg_Dominio_Corto,$appcfg_Dominio_Raiz,$appcfg_smtp_server,$appcfg_smtp_port,$appcfg_smtp_user,$appcfg_smtp_password,$appcfg_estado_inicial,$appcfg_estado_inicial_descripcion)
	{
		$this->setDB(        $db,
		         $appcfg_Dominio,
		   $appcfg_Dominio_Corto,
		    $appcfg_Dominio_Raiz,
			 $appcfg_smtp_server,
			   $appcfg_smtp_port,
			   $appcfg_smtp_user,
		   $appcfg_smtp_password,
		   $appcfg_estado_inicial,
		   $appcfg_estado_inicial_descripcion);

		$this->setIp();
		$this->setHost();
		if (isset($_POST["action"])) {
			if ($_POST["action"] == "cerrar-expediente" && isset($_POST["RAM"])) {
				$this->cerrarRAM($_POST["RAM"]);
			} else {
				echo json_encode(array("error" => 1001, "errorhead" => 'OPPS', "errormsg" => 'NO SE ENCONTRO NINGUNA FUNCION EN EL API PARA LA ACCIÓN REQUERIDA'));
			}
		}
	}
    
	protected function setDB($db,$appcfg_Dominio,$appcfg_Dominio_Corto,$appcfg_Dominio_Raiz,$appcfg_smtp_server,$appcfg_smtp_port,$appcfg_smtp_user,$appcfg_smtp_password,$appcfg_estado_inicial,$appcfg_estado_inicial_descripcion): void	{
		$this->db = $db;
		$this->dominio = $appcfg_Dominio;
		$this->dominio_corto = $appcfg_Dominio_Corto;
		$this->dominio_raiz = $appcfg_Dominio_Raiz;
		$this->server_smtp = $appcfg_smtp_server;
		$this->server_smtp_port = $appcfg_smtp_port;
		$this->server_smtp_user = $appcfg_smtp_user;
		$this->server_smtp_password = $appcfg_smtp_password;
		$this->appcfg_estado_inicial = $appcfg_estado_inicial;
		$this->appcfg_estado_inicial_descripcion = $appcfg_estado_inicial_descripcion;		
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
			if (isset($res) and isset($res[3]) and intval(Trim($res[3])) != 0) {
				$txt = date('Y m d h:i:s') . ';Api_Ram.php	Usuario:; ' . $_SESSION['usuario'] . '; -- ' . 'API_RAM.PHP Error Select: Error q; ' . $q . '; $res[0] ' .  $res[0] . ' $res[1] ' . $res[1] . ' $res[2] ' . $res[2] . ' $res[3] ' . $res[3] . ' Parametros'  .  implode(', ', array_map(fn($k, $v) => "$k=$v", array_keys($p), $p));
				logErr($txt, '../logs/logs.txt');
				return false;
			} else {
				return $datos;
			}
		} catch (PDOException $e) {
			// Capturar excepciones de PDO (error de base de datos)
			$txt = date('Y m d h:i:s') . 'Api_Ram.php	Usuario:; ' . $_SESSION['usuario'] . '; - Error Catch SELECT PDOException; ' . $e->getMessage() . ' QUERY ' . $q . ' Parametros'  .  implode(', ', array_map(fn($k, $v) => "$k=$v", array_keys($p), $p));
			logErr($txt, '../logs/logs.txt');
			return false; // O devolver un valor indicando error
		}
	}

	protected function selectOne($q, $p, $FETCH_GROUP = '')
	{
		try {
			$stmt = $this->db->prepare($q);
			$stmt->execute($p);
			if ($FETCH_GROUP == '') {
				$datos = $stmt->fetch();
			} else {
				$datos = $stmt->fetch($FETCH_GROUP);
			}
			$res = $stmt->errorInfo();
			if (isset($res) and isset($res[3]) and intval(Trim($res[3])) != 0) {
				$txt = date('Y m d h:i:s') . ';Api_Ram.php	Usuario:; ' . $_SESSION['usuario'] . '; -- ' . 'API_RAM.PHP Error Select: Error q; ' . $q . '; $res[0] ' .  $res[0] . ' $res[1] ' . $res[1] . ' $res[2] ' . $res[2] . ' $res[3] ' . $res[3] . ' Parametros'  .  implode(', ', array_map(fn($k, $v) => "$k=$v", array_keys($p), $p));
				logErr($txt, '../logs/logs.txt');
				return false;
			} else {
				return $datos;
			}
		} catch (PDOException $e) {
			// Capturar excepciones de PDO (error de base de datos)
			$txt = date('Y m d h:i:s') . 'Api_Ram.php	Usuario:; ' . $_SESSION['usuario'] . '; - Error Catch SELECT PDOException; ' . $e->getMessage() . ' QUERY ' . $q . ' Parametros'  .  implode(', ', array_map(fn($k, $v) => "$k=$v", array_keys($p), $p));
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
			if (isset($res) and isset($res[3]) and intval(Trim($res[3])) != 0) {
				$txt = date('Y m d h:i:s') . ';Api_Ram.php Usuario:; ' . $_SESSION['usuario'] . '; -- ' . ' UPDATE: Error q; ' . $q . '; $res[0] ' .  $res[0] . ' $res[1] ' . $res[1] . ' $res[2] ' . $res[2] . ' $res[3] ' . $res[3] . ' parametros: '  .  implode(', ', array_map(fn($k, $v) => "$k=$v", array_keys($p), $p));
				logErr($txt, '../logs/logs.txt');
				return false;
			} else {
				return true;
			}
		} catch (\Throwable $th) {
			// Capturar excepciones de PDO (error de base de datos)
			$txt = date('Y m d h:i:s') . 'Api_Ram.php	Usuario:; ' . $_SESSION['usuario'] . '; - Error Catch UPDATE PDOException; ' . $th->getMessage() . ' QUERY ' . $q  . ' parametros: ' .  implode(', ', array_map(fn($k, $v) => "$k=$v", array_keys($p), $p));
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
			if (isset($res) and isset($res[3]) and intval(Trim($res[3])) != 0) {
				$txt = date('Y m d h:i:s') . ';Api_Ram.php Usuario:; ' . $_SESSION['usuario'] . '; -- ' . ' INSERT: Error q; ' . $q . '; $res[0] ' .  $res[0] . ' $res[1] ' . $res[1] . ' $res[2] ' . $res[2] . ' $res[3] ' . $res[3] . ' parametros: ' .  implode(', ', array_map(fn($k, $v) => "$k=$v", array_keys($p), $p));
				logErr($txt, '../logs/logs.txt');
				return false;
			} else {
                $lastInsertId = $this->db->lastInsertId();
                return ($lastInsertId !== false && $lastInsertId !== null && $lastInsertId !== '') ? $lastInsertId : true;

			}
		} catch (\Throwable $th) {
			// Capturar excepciones de PDO (error de base de datos)
			$txt = date('Y m d h:i:s') . 'Api_Ram.php	Usuario:; ' . $_SESSION['usuario'] . '; - Error Catch INSERT PDOException; ' . $th->getMessage() . ' QUERY ' . $q  . ' parametros: ' .  implode(', ', array_map(fn($k, $v) => "$k=$v", array_keys($p), $p));
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
			if (isset($res) and isset($res[3]) and intval(Trim($res[3])) != 0) {
				$txt = date('Y m d h:i:s') . ';Api_Ram.php Usuario:; ' . $_SESSION['usuario'] . '; -- ' . ' DELETE: Error q; ' . $q . '; $res[0] ' .  $res[0] . ' $res[1] ' . $res[1] . ' $res[2] ' . $res[2] . ' $res[3] ' . $res[3] . ' Parametros'  .  implode(', ', array_map(fn($k, $v) => "$k=$v", array_keys($p), $p));;
				logErr($txt, '../logs/logs.txt');
				return false;
			} else {
				return true;
			}
		} catch (\Throwable $th) {
			// Capturar excepciones de PDO (error de base de datos)
			$txt = date('Y m d h:i:s') . 'Api_Ram.php	Usuario:; ' . $_SESSION['usuario'] . '; - Error Catch DELETE PDOException; ' . $th->getMessage() . ' QUERY ' . $q . ' Parametros'  .  implode(', ', array_map(fn($k, $v) => "$k=$v", array_keys($p), $p));;
			logErr($txt, '../logs/logs.txt');
			return false;
		}
	}

    //*******************************************************************************************/
	//* INICIO: SALVADO DE BITACORA DE PREFORMA
	//*******************************************************************************************/
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

    //***********************************************************************************************
	//* FINAL: SALVADO DE BITACORA DE PREFORMA
	//***********************************************************************************************    
  	//*******************************************************************************************/
	//* INICIO: ACTUALIACIÓN DEL ESTADO DE LA PREFOMRA Y LLAMADO A FUNCION QUE GUARDA BITACORA
	//*******************************************************************************************/
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
			if ($idEstado == 'IDE-2') {
				$eventox = 'FINAL';
				$etapax = 3;
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
                        return json_encode(array("error" => 9003, "errorhead" => 'ADVERTENCIA', "errormsg" => 'ESTAMOS PRESENTANDO INCONVENIENTES TEMPORALES AL MOMENTO DE SALVAR LA BITACORA DE LA PREFORMA'));
                    } else {
                        echo json_encode(array("error" => 9003, "errorhead" => 'ADVERTENCIA', "errormsg" => 'ESTAMOS PRESENTANDO INCONVENIENTES TEMPORALES AL MOMENTO DE SALVAR LA BITACORA DE LA PREFORMA'));
                    }
                }
			} else {
				if (!isset($_POST["echo"])) {
					return json_encode(array("error" => 9002, "errorhead" => 'ADVERTENCIA', "errormsg" => 'EL ESTADO A PROCESAR NO HABILITADO'));
				} else {
					echo json_encode(array("error" => 9002, "errorhead" => 'ADVERTENCIA', "errormsg" => 'EL ESTADO A PROCESAR NO HABILITADO'));
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
    //*******************************************************************************************/
	//* FINAL: ACTUALIACIÓN DEL ESTADO DE LA PREFOMRA Y LLAMADO A FUNCION QUE SALVA BITACORA
	//*******************************************************************************************/
    


    //***********************************************************************************************
	//* Inicio: Funcion para obtener Concesiones Que Pertenecen al RAM
	//***********************************************************************************************    
    function getSolicitud ($RAM,$Concesion,$idTemplate) {
        try {
            $query = "SELECT  A.Preforma,L.Titulo_Cargo,
            concat(concat(LL.Nombres,' '),LL.Apellidos) as Nombre_Firma,
            L.titulo_cargo_comisionado,
            (select ISNULL(concat(concat(Y.Nombres,' '),Y.Apellidos),'') from [IHTT_RRHH].[dbo].[TB_Empleados] Y where Y.ID_Empleado = L.id_comisionado) as firma_comisionado,
            k.template,
            A.SOL_MD5,F.DESC_Tipo_Tramite,A.SitemaFecha as Sistema_Fecha,A.ID_Expediente,A.FechaRecibido,A.ID_Solicitud,A.NombreSolicitante,B.NombreApoderadoLega,B.ID_ColegiacionAPL,F.DESC_tipo_tramite,
            VDoc.*,F.DESC_Tipo_Tramite,G.ID_Clase_Tramite,G.DESC_Clase_Tramite,C.Permiso_Explotacion,C.Certificado_Operacion,
            A.ID_Placa,C.N_Permiso_Especial
            from [IHTT_DB].[dbo].[TB_Expedientes] A, 
            [IHTT_PREFORMA].[dbo].[TB_Validacion_Documentos] VDoc,
            [IHTT_DB].[dbo].[TB_Expediente_X_Apoderado] B, 
            [IHTT_DB].[dbo].[TB_Expediente_X_Tipo_Tramite] C, 
            [IHTT_DB].[dbo].[TB_Tipo_Tramite] F, 
            [IHTT_DB].[dbo].[TB_Tramite] D,
            [IHTT_DB].[dbo].[TB_Clase_Tramite] G, 
            [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_template] K
            LEFT OUTER JOIN [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_template_firma] L  
             ON k.id = L.template_id
            LEFT OUTER JOIN [IHTT_RRHH].[dbo].[TB_Empleados] LL  
             ON L.usuario_firma_id = LL.ID_Empleado
            where A.Preforma = VDoc.ID_Formulario_Solicitud and
            A.ID_Solicitud = b.ID_Solicitud AND 
            A.ID_Solicitud = C.ID_Solicitud and 
            C.ID_Tramite = D.ID_Tramite and
            D.ID_Tipo_Tramite = F.ID_Tipo_Tramite and
            D.ID_Clase_Tramite = G.ID_Clase_Tramite and
			((C.Certificado_Operacion != '' and C.Certificado_Operacion = :Concesion)  or (C.N_Permiso_Especial != '' and C.N_Permiso_Especial = :Concesion1)) and
            A.ID_Expediente = :ID_Expediente and 
            k.id =:ID_Template order by C.Permiso_Explotacion,C.Certificado_Operacion,C.N_Permiso_Especial";
            //*********************************************************************/
            //* Recueprando la información del expediente
            //*********************************************************************/
            return $this->select($query, Array(':Concesion' => $Concesion,':Concesion1' => $Concesion,':ID_Expediente' => $RAM,':ID_Template' => $idTemplate));
        } catch (\Throwable $th) {
            $txt = date('Y m d h:i:s') . '	' .'getSolicitud.php catch '. (isset($query) ? $query : 'undefined query') . ' ERROR ' . $th->getMessage();
            logErr($txt,'../logs/logs.txt');
            return false;
        }	
    }
    //***********************************************************************************************
	//* Final: Funcion para obtener Concesiones Que Pertenecen al RAM
	//***********************************************************************************************    
    //***********************************************************************************************
	//* Inicio: Obtener todas las concesiones asociadas al expediente (RAM)
	//***********************************************************************************************    
    protected function getSolicitudesByRAM($RAM)
    {
        try {
            $query = "select distinct 
                    case 
                    when Permiso_Explotacion != '' then RTRIM(Certificado_Operacion)
                    else RTRIM(N_Permiso_Especial)
                end	AS Concesion
                from [IHTT_DB].[dbo].[TB_Expediente_X_Tipo_Tramite] 
                where ID_Solicitud = :ID_Solicitud
                order by 
                    case 
                        when Permiso_Explotacion != '' then RTRIM(Certificado_Operacion)
                    else RTRIM(N_Permiso_Especial)
                end";
            //*********************************************************************/
            //* Recueprando la información del Expediente
            //*********************************************************************/
            return $this->select($query, Array(':ID_Solicitud' => $RAM));
        } catch (\Throwable $th) {
            $txt = date('Y m d h:i:s') . '	' .'getSolicitud.php catch '. (isset($query) ? $query : 'undefined query') . ' ERROR ' . $th->getMessage();
            logErr($txt,'../logs/logs.txt');
            return false;
        }	
    }
    //***********************************************************************************************
	//* Final: Obtener todas las concesiones asociadas al expediente (RAM)
	//***********************************************************************************************    

    //***********************************************************************************************
	//* Inicio: Obtener el vehiculo asociado al expediente y concesion
	//***********************************************************************************************        
    protected function getVehiculosExpediente($RAM,$Concesion,$tabla) {
        try {
            $q = "SELECT e.[ID]
                ,e.[ID_Solicitud]
                ,e.[RTN_Propietario]
                ,e.[Nombre_Propietario]
                ,e.[ID_Placa]
                ,e.[ID_Marca]
                ,e.[Anio]
                ,case when e.[Modelo] = '' then 'NO APLICA' ELSE e.[Modelo] END AS Modelo
                ,e.[Tipo_Vehiculo]
                ,e.[ID_Color]
                ,e.[Motor]
                ,e.[Chasis]
                ,e.[Sistema_Usuario]
                ,e.[Sistema_Fecha]
                ,e.[Combustible]
                ,e.[VIN]
                ,e.[Alto]
                ,e.[Ancho]
                ,e.[Largo]
                ,e.[Capacidad_Carga]
                ,e.[Peso_Unidad]
                ,e.[Numero_Certificado]
                ,e.[Numero_Explotacion]
                ,e.[ID_Placa_Antes_Replaqueo],CL.*,MR.* FROM " . $tabla .",[IHTT_DB].[dbo].[TB_Expedientes] ex,[IHTT_SGCERP].[dbo].[TB_Color_Vehiculos] CL,[IHTT_SGCERP].[dbo].[TB_Marca_Vehiculo] MR
                where ((e.Numero_Certificado != '' and e.Numero_Certificado= :Numero_Certificado) or (e.Numero_PermisoEspecial != '' and e.Numero_PermisoEspecial= :Numero_Certificado1)) 
            and ex.ID_Expediente= :ID_Expediente and ex.ID_Solicitud = e.ID_Solicitud and e.ID_Color = CL.ID_Color and e.ID_Marca = MR.ID_Marca";
            $p = Array(':ID_Expediente' => $RAM,':Numero_Certificado' => $Concesion,':Numero_Certificado1' => $Concesion);
            //*********************************************************************/
            //* Recueprando la información del expediente
            //*********************************************************************/
            return $this->selectOne($q, $p);
        } catch (\Throwable $th) {
            $txt = date('Y m d h:i:s') . '	' .'getVehiculosxExpediente.php catch '. $q . ' ERROR ' . $th->getMessage();
            logErr($txt,'../logs/logs.txt');
        }	
    }
    //***********************************************************************************************
	//* Final: Obtener el vehiculo asociado al expediente y concesion
	//***********************************************************************************************  

    //***********************************************************************************************
	//* Inicio: Salvado de Bitacora de la Firma
	//***********************************************************************************************    
    protected function saveBitacoraFirma ($ID_Documento,$tipodocumento,$SistemaUsuario,$Id_Documento_MD5) {
        try {  
            //***********************************************************************************************     
            //* Query Update Bitacora
            //***********************************************************************************************
            $q = "UPDATE [IHTT_SGCERP].[dbo].[TB_Bitacora_Validacion_Firma] 
            set Estado='BAJA', Estado_Documento='BAJA', Sistema_Usuario=:Sistema_Usuario, 
            Sistema_Fecha_Modificacion=SYSDATETIME() 
            where ID_Documento=:ID_Documento;";
            //***********************************************************************************************     
            //* Parametros
            //***********************************************************************************************
            $p = Array(':Sistema_Usuario'=>$SistemaUsuario,':ID_Documento'=>$ID_Documento);
            if ($this->update($q, $p) == false) {
                return false;
            }
            try {        
                //***********************************************************************************************     
                //* Query Insert Validación de Firma
                //***********************************************************************************************     
                $q = "INSERT INTO [IHTT_SGCERP].[dbo].[TB_Bitacora_Validacion_Firma]
                                        ([ID_Documento],[Tipo_Documento],[Sistema_Usuario],[Id_Documento_MD5],[Estado_Documento],[Estado]) VALUES 
                                        (:ID_Documento,:Tipo_Documento,:Sistema_Usuario,:Id_Documento_MD5,'PENDIENTE','ACTIVO');";
                $p = Array(':ID_Documento'=>$ID_Documento,':Tipo_Documento'=>$tipodocumento,':Sistema_Usuario'=>$SistemaUsuario,':Id_Documento_MD5'=>$Id_Documento_MD5);
                return $this->insert($q, $p);
            } catch (\Throwable $th) {
                $txt = date('Y m d h:i:s') . '	' .'saveBitacoraFirma(): Update Documento' .  $th->getMessage() . ' QUERY'  . $q;
                logErr($txt,'../logs/logs.txt');
                return false;
            }	    
        } catch (\Throwable $th) {
            $txt = date('Y m d h:i:s') . '	Usuario:' . $_SESSION['user_name'] .' catch saveBitacoraFirma(): INSERT INTO [IHTT_SGCERP].[dbo].[TB_Bitacora_Validacion_Firma]' .  $th->getMessage() . ' QUERY'  . $q;
            logErr($txt,'../logs/logs.txt');
            return false;
        }	            
    }
    //***********************************************************************************************
	//* Inicio: Salvado de Bitacora de la Firma
	//***********************************************************************************************    

    //***********************************************************************************************
	//* Inicio: Siguiente 
	//***********************************************************************************************    

    //***********************************************************************************************
	//* Inicio: Salvar Automotivado
	//***********************************************************************************************  


    //***********************************************************************************************
	//* Inicio: Salvar Automotivado
	//***********************************************************************************************  
    protected function saveAutoMotivado ($ID_Solicitud,$id_oficial,$SistemaUsuario) {
        try {
        //***********************************************************************************************
        //* Query Insert AutoAdmision
        //***********************************************************************************************
        $q = "INSERT INTO [IHTT_GDL].[dbo].[TB_AutoAdmision] 
                                ([ID_Solicitud],
                                [ID_OficialJuridico],
                                [SistemaUsuario],
                                [ID_Escrito_Expediente],
                                [SustitucionPoder], 
                                [MCLExtraprocesal],
                                [ComercianteIndividual],
                                [MPCambioPlaca]) VALUES 
        (:ID_Solicitud,:ID_OficialJuridico,:SistemaUsuario,'',0,0,0,0);";
            $p = Array(':ID_Solicitud'=>$ID_Solicitud,':ID_OficialJuridico'=>$id_oficial,':SistemaUsuario'=>$SistemaUsuario);
            return $this->insert($q, $p);
        } catch (\Throwable $th) {
            $txt = date('Y m d h:i:s') . '	' .'saveAutoMotivado catch '. $q . ' ERROR ' . $th->getMessage();
            logErr($txt,'../logs/logs.txt');
            return false;
        }	
    }
    //***********************************************************************************************
	//* Final: Salvar Automotivado
	//***********************************************************************************************    
    //***********************************************************************************************
	//* Inicio: Funcion De Desmembramiento de la Fecha
	//***********************************************************************************************    
    protected function desmebrarFecha($fecha_completa_inicial){
	
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
    //***********************************************************************************************
	//* Final: Funcion De Desmembramiento de la Fecha
	//***********************************************************************************************    
    //***********************************************************************************************
	//* Inicio: Funcion Principal de Generación de Documento de Automotivado
	//***********************************************************************************************    
    protected function pdfAutoMotivadoIngresoApi($RAM,$numeroauto,$template=1) {
        $rs_id_rs_numeroauto = $numeroauto;
        $rs_id_rs_expediente = $RAM;
        $rs_id_rs_template = $template;
        $requicitos = '';
        $pagina_inicio = '';
        $cfg_institucion = 'INSTITUTO HONDUREÑO DEL TRANSPORTE TERRESTRE';
        //***********************************************************************************************    
        //* Funcion que recupera los datos para insertar en el template (getSolicitud.php)
        //***********************************************************************************************    
        $CONCESIONES = $this->getSolicitudesByRAM($RAM);
        //***********************************************************************************************    
        //* INICIO: Recuperando Vehiculos del tramite
        //***********************************************************************************************    
        $tramite = ''; // Initialize the variable to avoid undefined variable error
        $contador=0;
        foreach ($CONCESIONES as $CONCESION){
            //***********************************************************************************************    
            //* Funcion que recupera los datos para insertar en el template (getSolicitud.php)
            //***********************************************************************************************    
            $row_rs_todos_los_registros = $this->getSolicitud($RAM,$CONCESION['Concesion'],$rs_id_rs_template);

            //***********************************************************************************************    
            //* INICIO: Recuperando Vehiculos del tramite
            //***********************************************************************************************    
            $vehiculoactual = $this->getVehiculosExpediente($RAM,$CONCESION['Concesion'],$tabla="[IHTT_DB].[dbo].[TB_Solicitud_Vehiculo_Actual] e"); 
            $vehiculoentra =  $this->getVehiculosExpediente($RAM,$CONCESION['Concesion'],$tabla="[IHTT_DB].[dbo].[TB_Solicitud_Vehiculo_Entra] e"); 
            $detallevehiculoactual = " MARCA: " . $vehiculoactual['DESC_Marca'] . ", MODELO: " . $vehiculoactual['Modelo'] . ", COLOR: " . $vehiculoactual['DESC_Color']. ", MOTOR: " . $vehiculoactual['Motor']. ", CHASIS: " . $vehiculoactual['Chasis'] . " Y PLACA: " . $vehiculoactual['ID_Placa'];
            $id_placa = $vehiculoactual['ID_Placa'];
            $detallevehiculoentra = '';
            if (isset($vehiculoentra['DESC_Marca'])) {
                $detallevehiculoentra = " MARCA: " . $vehiculoentra['DESC_Marca'] . ", MODELO: " . $vehiculoentra['Modelo'] . ", COLOR: " . $vehiculoentra['DESC_Color']. ", MOTOR: " . $vehiculoentra['Motor']. ", CHASIS: " . $vehiculoentra['Chasis'] . " Y PLACA: " . $vehiculoentra['ID_Placa'];
                $id_placa = $vehiculoentra['ID_Placa'];
            }
            //***********************************************************************************************    
            //* FINAL: Recuperando Vehiculos del tramite
            //***********************************************************************************************    
            $Error_Proceso = false;
            $total_registros = count($row_rs_todos_los_registros);
            $tipo_documento='';
            $contador_tramites = 0;
            foreach ($row_rs_todos_los_registros as $row_rs_expediente){
                if ($contador == 0 and $requicitos == '') {
                    $requicitos = 'FORMULARIO O SOLICITUD DEL TRAMITE';
                    if (strtoupper($row_rs_expediente['Check_Cert_Auntenticidad_Firma']) == 'SI') {
                        $requicitos = $requicitos . ','. 'CARTA PODER DEBIDAMENTE AUTENTICADA';
                    }
                    if (strtoupper($row_rs_expediente['Check_DNI']) == 'SI') {
                        $requicitos = $requicitos . ','. 'DOCUMENTO DE IDENTIFICACION NACIONAL (DNI)';
                    }
                    if (strtoupper($row_rs_expediente['Check_RTN']) == 'SI') {
                        $requicitos = $requicitos . ','. 'REGISTRO TRIBUTARIO NACIONAL(RTN)';
                    }
                    $requicitos = $requicitos . ','. 'FOTOCOPIA DE PERMISO DE EXPLOTACION Y CERTIFICADO DE OPERACION';
                    if (strtoupper($row_rs_expediente['Check_Cert_Auntenticidad_Firma']) == 'SI') {
                        $requicitos = $requicitos . ','. 'REPRESENTACION LEGAL DE PERSONA JURIDICA';
                    }
                    if (strtoupper($row_rs_expediente['Check_Arrendamiento']) == 'SI') {
                        $requicitos = $requicitos . ','. 'CONTRATO DE ARRENDAMIENTO DEBIDAMENTE AUTENTICADO';
                    }
                    if (strtoupper($row_rs_expediente['Check_Autenticidad_Documentos']) == 'SI') {
                        $requicitos = $requicitos . ','. 'AUTENTICA DE FOTOCOPIAS';
                    }
                    $requicitos = $requicitos . ','. 'CARNET DE COLEGIACION  DEL PROFESIONAL DEL DERECHO VIGENTE';
                    if (strtoupper($row_rs_expediente['Check_Inspeccion_Mecanica']) == 'SI') {
                        $requicitos = $requicitos . ','. 'ESTADO FISICO DE LA UNIDAD FTT03';
                    }
                    if (strtoupper($row_rs_expediente['Check_Boleta']) == 'SI') {
                        $requicitos = $requicitos . ' Y '. 'COPIA DE BOLETA DE REVISION DE LA UNIDAD';
                    }
                    // Generando llave publica la que sera la llave que se exponga al publico quedando la llave privada(md5 del numero de soicitud)
                    $llave_publica = $row_rs_expediente['SOL_MD5'];
                    date_default_timezone_set('America/Guatemala');
                    $fi = $this->desmebrarFecha(date("Y/m/d h:i:sa"));
                    // Fecha Presentacion
                    $hora = substr($row_rs_expediente['Sistema_Fecha'],11,2);
                    // echo $row_rs_expediente['Sistema_Fecha'] . '<br>';
                    // echo (new DateTime($row_rs_expediente['Sistema_Fecha']))->format("Y-m-d H:i:s");
                    // echo intval($hora);
                    // die();
                    if (intval($hora) >12){
                        $hora = intval($hora) -12;
                        if (strlen($hora) == 1) {
                            $hora = '0' . $hora;
                        }
                        $row_rs_expediente['Sistema_Fecha'] = substr($row_rs_expediente['Sistema_Fecha'], 0, 10) . ' ' . $hora . substr($row_rs_expediente['Sistema_Fecha'], 13) . 'pm';
                    } else {
                        $row_rs_expediente['Sistema_Fecha'] .= 'am';
                    }
                    $fp = $this->desmebrarFecha(date("Y/m/d h:i:sa",strtotime($row_rs_expediente['Sistema_Fecha'])));
                    //Fecha Recibido
                    $hora = substr($row_rs_expediente['FechaRecibido'],11,2);
                    if (intval($hora)>12){
                        $hora = intval($hora) -12;
                        if (strlen($hora) == 1) {
                            $hora = '0' . $hora;
                        }
                        $row_rs_expediente['FechaRecibido'] = substr($row_rs_expediente['FechaRecibido'], 0, 10) . ' ' . $hora . substr($row_rs_expediente['FechaRecibido'], 13) . 'pm';
                    } else {
                        $row_rs_expediente['FechaRecibido'] .= 'am';
                    }                
                    $fr = $this->desmebrarFecha(date("Y/m/d h:i:sa",strtotime($row_rs_expediente['FechaRecibido'])));
                    $pagina_inicio = $row_rs_expediente['template'];
                    $pagina_inicio = str_replace('@@requicitos@@',$requicitos,$pagina_inicio);
                    // Institucion
                    $pagina_inicio = str_replace('@@institucion@@',$cfg_institucion,$pagina_inicio);
                    // fecha impresion
                    $pagina_inicio = str_replace('@@hli@@',$fi['hora_letras'],$pagina_inicio);
                    $pagina_inicio = str_replace('@@mili@@',$fi['minutos_letras'],$pagina_inicio);
                    $pagina_inicio = str_replace('@@ampmi@@',$fi['ampm'],$pagina_inicio);
                    $pagina_inicio = str_replace('@@dli@@',$fi['dia_letras'],$pagina_inicio);
                    $pagina_inicio = str_replace('@@mli@@',$fi['mes_letras'],$pagina_inicio);
                    $pagina_inicio = str_replace('@@ali@@',$fi['anio_letras'],$pagina_inicio);
                    $pagina_inicio = str_replace('@@hmsampmi@@',$fi['fecha_hmsampm'],$pagina_inicio);
                    $pagina_inicio = str_replace('@@dmai@@',$fi['fecha_dma'],$pagina_inicio);
                    // fecha de recepcion
                    $pagina_inicio = str_replace('@@dlr@@',$fr['dia_letras'],$pagina_inicio);
                    $pagina_inicio = str_replace('@@mlr@@',$fr['mes_letras'],$pagina_inicio);
                    $pagina_inicio = str_replace('@@alr@@',$fr['anio_letras'],$pagina_inicio);
                    $pagina_inicio = str_replace('@@ampmr@@',$fr['ampm'],$pagina_inicio);
                    // Fecha de Presentacion
                    $pagina_inicio = str_replace('@@hmsampmp@@',$fp['fecha_hmsampm'],$pagina_inicio);	
                    $pagina_inicio = str_replace('@@dlp@@',$fp['dia_letras'],$pagina_inicio);
                    $pagina_inicio = str_replace('@@mlp@@',$fp['mes_letras'],$pagina_inicio);
                    $pagina_inicio = str_replace('@@dmap@@',$fp['fecha_dma'],$pagina_inicio);
                    $pagina_inicio = str_replace('@@hlp@@',$fp['hora_letras'],$pagina_inicio);
                    $pagina_inicio = str_replace('@@ampmp@@',$fp['ampm'],$pagina_inicio);
                    $pagina_inicio = str_replace('@@dlp@@',$fp['dia_letras'],$pagina_inicio);
                    $pagina_inicio = str_replace('@@anp@@',$fp['anio_letras'],$pagina_inicio);
                    $pagina_inicio = str_replace('@@mnp@@',$fp['mes_numerica'],$pagina_inicio);
                    $pagina_inicio = str_replace('@@milp@@',$fp['minutos_letras'],$pagina_inicio);
                    $pagina_inicio = str_replace('@@ns@@',$row_rs_expediente['ID_Solicitud'].'/'.$rs_id_rs_expediente,$pagina_inicio);
                    $pagina_inicio = str_replace('@@cs@@',$row_rs_expediente['NombreSolicitante'],$pagina_inicio);
                    $pagina_inicio = str_replace('@@rl@@',$row_rs_expediente['NombreApoderadoLega'],$pagina_inicio);
                    $pagina_inicio = str_replace('@@CAHN@@',$row_rs_expediente['ID_ColegiacionAPL'],$pagina_inicio);
                    $pagina_inicio = str_replace('@@ncf@@',$row_rs_expediente['Nombre_Firma'],$pagina_inicio);
                    $pagina_inicio = str_replace('@@tcf@@',$row_rs_expediente['Titulo_Cargo'],$pagina_inicio);
                    if ($row_rs_expediente['N_Permiso_Especial'] != '') {
                        $tipo_documento = 'PERMISO ESPECIAL';
                        $row_rs_expediente['Certificado_Operacion'] = $row_rs_expediente['N_Permiso_Especial'];
                    } else {
                        $tipo_documento = 'CERTIFICADO DE OPERACIÓN';
                    }
                    //* GENERAMOS EL CODIGO QR de la Validacion Firma
                    $URL = $this->dominio_raiz . ':293/ra/VFRA.php?td=am&Automotivado=S&Llave_Publica='.$llave_publica;
                    QRcode::png($URL,"../qr/temp/".$llave_publica.".png",QR_ECLEVEL_M,5,4);
                    $pagina_inicio = str_replace('../../qr/temp/QR.PNG',"../qr/temp/".$llave_publica.".png",$pagina_inicio);
                    // Inicio QR Comisionado Presidente
                    if ($row_rs_expediente['titulo_cargo_comisionado'] != '') {
                        $pagina_inicio = str_replace('@@ncfc@@',$row_rs_expediente['firma_comisionado'],$pagina_inicio);
                        $pagina_inicio = str_replace('@@tcfc@@',$row_rs_expediente['titulo_cargo_comisionado'],$pagina_inicio);
                        $pagina_inicio = str_replace('../../qr/temp/CQR.PNG',"../qr/temp/".$llave_publica.".png",$pagina_inicio);
                    }
                    If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-01') {
                        $tramite = $row_rs_expediente['DESC_Tipo_Tramite'] . ' ' . $row_rs_expediente['DESC_Clase_Tramite'] . ' CON NÚMERO ' . $row_rs_expediente['Permiso_Explotacion'];
                    } else {
                        If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-02') {
                            $tramite = $row_rs_expediente['DESC_Tipo_Tramite'] . ' ' . $row_rs_expediente['DESC_Clase_Tramite'] . ' CON NÚMERO ' . $row_rs_expediente['Certificado_Operacion']  . ' ASOCIADO AL PERMISO DE EXPLOTACIÓN NÚMERO ' . $row_rs_expediente['Permiso_Explotacion'];
                        } else	{
                            If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-03') {
                                $tramite = $row_rs_expediente['DESC_Tipo_Tramite'] . ' ' . $row_rs_expediente['DESC_Clase_Tramite'] . ' CON NÚMERO PERMISO ESPECIAL ' . $row_rs_expediente['N_Permiso_Especial'];									
                            } else	{
                                If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-15') {
                                    if ($detallevehiculoentra == '') {
                                        $tramite = $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', A NÚMERO DE PLACA: ' . $vehiculoactual['ID_Placa']  . ' EN EL '. $tipo_documento .' NUMERO: ' . $row_rs_expediente['Certificado_Operacion'];									
                                    } else {
                                        $tramite = $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', A NÚMERO DE PLACA: ' . $vehiculoentra['ID_Placa'] . ' EN EL '. $tipo_documento .' NUMERO: ' . $row_rs_expediente['Certificado_Operacion'];									
                                    }
                                } else {
                                    If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-17') {
                                        $tramite = $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', A NUMERO DE MOTOR: ' . $vehiculoactual['Motor'] . ' EN EL '. $tipo_documento .' NUMERO: ' . $row_rs_expediente['Certificado_Operacion'];									
                                    } else {
                                        If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-18') {
                                            $tramite = $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', A COLOR: ' . $vehiculoactual['DESC_Color'] . ' EN EL '. $tipo_documento .' NUMERO: ' . $row_rs_expediente['Certificado_Operacion'];									
                                        } else {
                                            If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-19') {
                                                $tramite = $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', A NÚMERO DE CHASIS: ' . $vehiculoactual['Chasis'] . ' EN EL '. $tipo_documento .' NUMERO: ' . $row_rs_expediente['Certificado_Operacion'];									
                                            }else {
                                                If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-08') {
                                                    $tramite = $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', DE LA UNIDAD <strong>' . $detallevehiculoactual . ' POR LA NUEVA UNIDAD: ' . $detallevehiculoentra . ' EN EL '. $tipo_documento .' NUMERO: ' . $row_rs_expediente['Certificado_Operacion'];									
                                                }
                                            }
                                        }
                                    }
                                }
                            }		
                        }
                    }        
                    $ruta='Documentos/' .$RAM . '/AutoMotivado_' . $row_rs_expediente['Preforma'] . '.pdf';
                    $pagina_inicio = str_replace('@@AUTMOT@@',$rs_id_rs_numeroauto,$pagina_inicio);
                } else {
                    // ***********************************************************************************************
                    // Else de contador mayor que cero
                    // ***********************************************************************************************
                    $conjunccion = '';
                    if ($total_registros == ($contador_tramites+1)){
                        $conjunccion = ' Y ';
                    } else {
                        if ($contador_tramites > 0) {
                            $conjunccion = ',&nbsp;';
                        }
                    } 
    
                    if ($row_rs_expediente['N_Permiso_Especial'] != '') {
                        $row_rs_expediente['Certificado_Operacion'] = $row_rs_expediente['N_Permiso_Especial'];
                    }
                    If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-01') {
                        $tramite = $tramite . $conjunccion . $row_rs_expediente['DESC_Tipo_Tramite'] . ' ' . $row_rs_expediente['DESC_Clase_Tramite'] . ' CON NÚMERO ' . $row_rs_expediente['Permiso_Explotacion'];
                    } else {
                        If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-02') {
                            $tramite = $tramite . $conjunccion . $row_rs_expediente['DESC_Tipo_Tramite'] . ' ' . $row_rs_expediente['DESC_Clase_Tramite'] . ' CON NÚMERO ' . $row_rs_expediente['Certificado_Operacion']  . ' ASOCIADO AL PERMISO DE EXPLOTACIÓN NÚMERO ' . $row_rs_expediente['Permiso_Explotacion'];
                        } else	{
                            If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-03') {
                                $tramite = $tramite . $conjunccion . $row_rs_expediente['DESC_Tipo_Tramite'] . ' ' . $row_rs_expediente['DESC_Clase_Tramite'] . ' CON NÚMERO DE PERMISO ESPECIAL ' . $row_rs_expediente['N_Permiso_Especial'];									
                            } else	{
                                If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-15') {
                                    if ($detallevehiculoentra == '') {
                                        $tramite = $tramite . $conjunccion . $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', A NÚMERO PLACA: ' . $vehiculoactual['ID_Placa'] . ' EN EL '. $tipo_documento .' NUMERO: ' . $row_rs_expediente['Certificado_Operacion'];									
                                    } else {
                                        $tramite = $tramite . $conjunccion . $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', A NÚMERO PLACA: ' . $vehiculoentra['ID_Placa'] . ' EN EL '. $tipo_documento .' NUMERO: ' . $row_rs_expediente['Certificado_Operacion'];									
                                    }
                                } else {
                                    If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-17') {
                                        $tramite = $tramite . $conjunccion . $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', A NUMERO MOTOR: ' . $vehiculoactual['Motor'] . ' EN EL '. $tipo_documento .' NUMERO: ' . $row_rs_expediente['Certificado_Operacion'];									
                                    } else {
                                        If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-18') {
                                            $tramite = $tramite . $conjunccion . $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', A COLOR: ' . $vehiculoactual['DESC_Color'] . ' EN EL '. $tipo_documento .' NUMERO: ' . $row_rs_expediente['Certificado_Operacion'];									
                                        } else {
                                            If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-19') {
                                                $tramite = $tramite . $conjunccion . $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', A NÚMERO DE CHASIS: ' . $vehiculoactual['Chasis'] . ' EN EL '. $tipo_documento .' NUMERO: ' . $row_rs_expediente['Certificado_Operacion'];									
                                            }else {
                                                If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-08') {
                                                    $tramite = $tramite . $conjunccion . $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', DE LA UNIDAD <strong>' . $detallevehiculoactual . ' POR LA NUEVA UNIDAD: ' . $detallevehiculoentra . ' EN EL '. $tipo_documento .' NUMERO: ' . $row_rs_expediente['Certificado_Operacion'];									
                                                }
                                            }
                                        }
                                    }
                                }
                            }		
                        }
                    }
                }
                $contador++;
                $contador_tramites++;
            }
            $tramite=$tramite.';&nbsp;';
            if ($Error_Proceso == true) {
                break;
            }
        }

        if ($pagina_inicio != '' && $Error_Proceso == false) {
            $pagina_inicio = str_replace('@@tramites@@',$tramite,$pagina_inicio);
            $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [215.9, 355.6],'tempDir' => sys_get_temp_dir()]);
            $mpdf->pdf_version = '1.7';
            $mpdf->SetTitle('Auto_de_Admision');
            $mpdf->SetAuthor('Instituto Hondureño del Transporte Terrestre');
            $mpdf->SetCreator('SATT');
            $mpdf->SetSubject('Documentos Legales IHTT');
            $mpdf->PDFXauto = true;
            $mpdf->SetWatermarkText("IHTT");
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = 'DejaVuSansCondensed';
            $mpdf->watermarkTextAlpha = 0.10;
            $mpdf->margin_header = 0;
            $mpdf->SetMargins(0, 0, 30);
            $mpdf->SetHTMLHeader('<div height="100%" width="100%">
            <img height="15%" width="75%" alt="encabezado" src="assets/images/encabezado-pagina1.png">
            </div>','O', true);
            $mpdf->SetHTMLFooter('<table width="100%"><tr><td width="50%" align="left">Página(s): {PAGENO} de {nbpg}</td>
            <td width="50%" align="right">'. $_SESSION["user_name"] .'</td></tr></table>');
            $mpdf->WriteHTML($pagina_inicio);
            $mpdf->Output($ruta, \Mpdf\Output\Destination::FILE);
            if (file_exists("../qr/temp/".$llave_publica.".png")){ unlink("../qr/temp/".$llave_publica.".png");}
            $respuestaretornar['error']=false;
            $respuestaretornar['msg']='<strong>  IMPRIMIR AUTO DE INGRESO CON EL NÚMERO: ' . $rs_id_rs_numeroauto . '  </strong>';
            $respuestaretornar['Boton']='<a style="background-color: rgb(119 183 202); border-radius: 15px; border: solid 4px #33536f;" href="'.$ruta.'" target="_blank"  class="waves-effect waves-green btn-flat btn"><i class="material-icons print"></i>'.$respuestaretornar['msg'].'</a>';
            $respuestaretornar['ruta']=$ruta;
            $respuestaretornar['numero_auto']=$rs_id_rs_numeroauto;
            $respuestaretornar['llave_publica']=$llave_publica;
            return $respuestaretornar;
        } else {
            return false;
        }
    }
    //***********************************************************************************************
	//* Inicio: Funcion principal de Generacion de AutoMotivado de Ingreso
	//***********************************************************************************************    
    protected function saveBitacoraNumeroAuto ($RAM) {
        //* Salvando bitacora de la firma del automotivado
        //***********************************************************************************************
        $llave_publica = hash('SHA512','/I(h$T@t%&)' . $RAM . date("Y/m/d h:i:sa"),false);
        $respuestasaveBitacoraFirma = $this->saveBitacoraFirma($RAM,'AUTOMOTIVADO',$_SESSION["user_name"],$llave_publica);	
        if ($respuestasaveBitacoraFirma != false) {
            //***********************************************************************************************			
            //* Salvando Automotivado 
            //***********************************************************************************************
            return  $this->saveAutoMotivado($RAM,$_SESSION["ID_Usuario"],$_SESSION["user_name"]);
        }				
    }
    //***********************************************************************************************
	//* Final: Funcion principal de Generacion de AutoMotivado de Ingreso
	//***********************************************************************************************
    //***********************************************************************************************
	//* INICIO: UPDATE VEHICULO X PLACA
	//***********************************************************************************************
    function updateVehiculoxPlaca ($rs_id_rs_placa,$estado,$tipo_transporte) {
        if ($tipo_transporte == 'CARGA') {
            $query = "UPDATE [IHTT_SGCERP].[dbo].[TB_Vehiculo_Transporte_Carga_x_Placa] set Estado = :Estado where ID_Placa=:ID_Placa";
        } else {
            $query = "UPDATE [IHTT_SGCERP].[dbo].[TB_Vehiculo_Transporte_Pasajero_x_Placa] set Estado = :Estado where ID_Placa=:ID_Placa";
        }
        try {
            return $this->update($query,Array(':Estado' => $estado,':ID_Placa' => $rs_id_rs_placa));
        } catch (\Throwable $th) {
            $txt = date('Y m d h:i:s') . '	Usuario:' . $_SESSION['user_name']  .'updateVehiculoxPlaca() catch '. $query . ' ERROR ' . $th->getMessage();
            logErr($txt,'../logs/logs.txt');
            return false;
        }	
    }    
    //***********************************************************************************************
	//* FINAL: UPDATE VEHICULO X PLACA
	//***********************************************************************************************

    //***********************************************************************************************
	//* INICIO: SALVAR PLACA X VEHICULO
	//***********************************************************************************************
    function savePlacaxVehiculo ($ID_Vehiculo,$id_placa_entrante,$placa_saliente,$es_renovacion_tipo_transporte) {
        $insertPlaca = $this->insertPlaca ($id_placa_entrante);
        if ($insertPlaca != false) {
            $updateVehiculoxPlaca = $this->updateVehiculoxPlaca($placa_saliente, 'INACTIVA',$es_renovacion_tipo_transporte);
            if ($updateVehiculoxPlaca != false) {
                return $this->insertVehiculoPlaca($ID_Vehiculo,$id_placa_entrante,$es_renovacion_tipo_transporte);
            } else {
                return false;                                                    
            }
        }
    }    
    //***********************************************************************************************
	//* FINAL: SALVAR PLACA X VEHICULO
	//***********************************************************************************************
    //***********************************************************************************************
	//* INICIO: Funcion Obtiene la Siguiente Resolucion
	//***********************************************************************************************
    protected function getSiguienteResolucion() {
        $query = '';
        try {
            $query = "SELECT TOP 1 (cast(substring(ID_Resolucion,14,6) as int)+1) as siguiente_resolucion
            FROM [IHTT_GDL].[dbo].[TB_Resolucion] 
            where YEAR(FechaResolucion) = :Year and len(ID_Resolucion) = 24
            order by substring(ID_Resolucion,14,6) desc";
            $siguiente = (intval($this->selectOne($query, Array(':Year' => date('Y')))) + 1);
            if ($siguiente!= false) {
                $numero_resolucion = '000000' . $siguiente;
                $numero_resolucion = substr($numero_resolucion,-6);
                return  'PCDTT-IHTT-T-'. $numero_resolucion . '-'. date('Y');
            }
        } catch (\Throwable $th) {
            $txt = date('Y m d h:i:s') . '	Usuario: ' .  $_SESSION['user_name']  .' getSiguienteResolucion.php catch '. $query . ' ERROR ' . $th->getMessage();
            logErr($txt,'../logs/logs.txt');
            return false;
        }	
    }
    //***********************************************************************************************
	//* FINAL: Funcion Obtiene la Siguiente Resolucion
	//***********************************************************************************************    

    //***********************************************************************************************
	//*INICIO: Funcion Obtiene el Listado de Concesines que trae el RAM
	//***********************************************************************************************    
    function getSolicitudResolucionConcesiones ($rs_id_rs_expediente) {
        $query = "SELECT distinct
                case
                    when H.N_Permiso_Especial != '' then H.N_Permiso_Especial
                    else H.Certificado_Operacion
                end as Concesion,
                E.SOL_MD5
                from [IHTT_DB].[dbo].[TB_Expedientes] E,[IHTT_DB].[dbo].[TB_Expediente_X_Tipo_Tramite] H
                where E.ID_Solicitud = H.ID_Solicitud and H.ID_Solicitud = :ID_Solicitud";
        try {
            return $this->select($query, Array(':ID_Solicitud' => $rs_id_rs_expediente));
        } catch (\Throwable $th) {
            $txt = date('Y m d h:i:s') . '	Usuario: ' . $_SESSION['user_name'] .' getSolicitudResolucion.php catch '. $query . ' ERROR ' . $th->getMessage();
            logErr($txt,'../logs/logs.txt');
            return false;
        }	
    }    
    //***********************************************************************************************
	//*FINAL: Funcion Obtiene loS tramites por expediente y concesion
	//***********************************************************************************************    

    //***********************************************************************************************
	//*INICIO: Funcion Obtiene loS tramites por expediente y concesion
	//***********************************************************************************************    
    function getSolicitudResolucion ($rs_id_rs_expediente,$Concesion,$rs_id_rs_template) {
        $query = "SELECT M.Email_Solicitante,A.ID_Placa,CS.ID_Clase_Servico,S.DESC_Modalidad,G.ID_Modalidad,G.ID_Categoria as ID_Tipo_Categoria,
        A.ID_gea,Q.Email_Apoderado_Legal,A.Preforma,M.RTN_Solicitante,A.ID_Expediente,A.ID_Solicitud,D.ID_Tramite,
        H.Permiso_Explotacion,
        H.Certificado_Operacion,
        H.N_Permiso_Especial,
        (select ISNULL(concat(concat(Y.Nombres,' '),Y.Apellidos),'') from [IHTT_RRHH].[dbo].[TB_Empleados] Y 
        where Y.ID_Empleado = L.id_comisionado) as firma_comisionado,
        L.titulo_cargo_comisionado,F.ID_Tipo_Tramite,F.DESC_tipo_tramite,
        F.Acronimo_Tramite,N.[DESC_Clase_Tramite],D.ID_Clase_Tramite,G.ID_Modalidad,CS.DESC_Clase_Servico AS ID_Tipo_Servicio,H.ServiciosNombre,H.CategoriaSubservicio,L.Titulo_Cargo,
        concat(concat(LL.Nombres,' '),LL.Apellidos) as Nombre_Firma,
        k.template,
        A.SOL_MD5,F.DESC_Tipo_Tramite,A.SitemaFecha as Sistema_Fecha,A.FechaRecibido,A.NombreSolicitante,B.NombreApoderadoLega,B.ID_ColegiacionAPL,
        (select ID_Resolucion FROM [IHTT_GDL].[dbo].[TB_Resolucion] Res Where Res.ID_Solicitud = A.ID_Solicitud) as ID_Resolucion,
        (select TOP 1 ISNULL(CodigoAvisoCobro,0) from [IHTT_Webservice].[dbo].[TB_AvisoCobroEnc]  AC Where LTRIM(RTRIM(A.Preforma)) = LTRIM(RTRIM(AC.Expediente))) as CodigoAvisoCobro,
        S.ID_Clase_Servicio as CS,A.SOL_MD5
        from [IHTT_DB].[dbo].[TB_Expedientes] A, 
        [IHTT_PREFORMA].[dbo].[TB_Solicitante] M,
        [IHTT_DB].[dbo].[TB_Expediente_X_Apoderado] B,  
        [IHTT_DB].[dbo].[TB_Expediente_X_Tipo_Tramite] H,
        [IHTT_DB].[dbo].[TB_Tramite] D,
        [IHTT_DB].[dbo].[TB_Tipo_Tramite] F,
        [IHTT_DB].[dbo].[TB_Categoria] G,
        [IHTT_DB].[dbo].[TB_Modalidad] S,
        [IHTT_DB].[dbo].[TB_Clase_Servicio] CS,
        [IHTT_DB].[dbo].[TB_Clase_Tramite] N,
        [IHTT_PREFORMA].[dbo].[TB_Apoderado_Legal] Q,
        [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_template] K
        LEFT OUTER JOIN [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_template_firma] L  
        ON k.id = L.template_id
        LEFT OUTER JOIN [IHTT_RRHH].[dbo].[TB_Empleados] LL  
        ON L.usuario_firma_id = LL.ID_Empleado
        where A.Preforma = M.ID_Formulario_Solicitud and 
            A.ID_Solicitud = B.ID_Solicitud AND 
            A.ID_Solicitud = H.ID_Solicitud AND
            H.ID_Tramite = D.ID_Tramite and
            D.ID_Tipo_Tramite = F.ID_Tipo_Tramite AND 
            D.ID_Categoria = G.ID_Categoria AND 
            G.ID_Modalidad = S.ID_Modalidad and
            S.ID_Clase_Servicio = CS.ID_Clase_Servico and
            A.Preforma = Q.ID_Formulario_Solicitud and
            D.ID_Clase_Tramite = n.ID_Clase_Tramite and
            A.ID_Expediente = :ID_Expediente and k.id =:Id_Template AND
            A.Es_Renovacion_Automatica = 1 and
            (H.Certificado_Operacion != '' AND H.Certificado_Operacion = :Concesion OR H.N_Permiso_Especial != '' AND H.N_Permiso_Especial = :Concesion1)
            order by H.Certificado_Operacion,H.N_Permiso_Especial,F.ID_Tipo_Tramite,D.ID_Clase_Tramite";
        try {
            return $this->select($query, Array(':ID_Expediente' => $rs_id_rs_expediente,':Id_Template' => $rs_id_rs_template,':Concesion' => $Concesion,':Concesion1' => $Concesion));
        } catch (\Throwable $th) {
            $txt = date('Y m d h:i:s') . '	Usuario: ' . $_SESSION['user_name'] .' getSolicitudResolucion.php catch '. $query . ' ERROR ' . $th->getMessage();
            logErr($txt,'../logs/logs.txt');
            return false;
        }	
    }    
    //***********************************************************************************************
	//*FINAL: Funcion Obtiene loS tramites por expediente y concesion
	//***********************************************************************************************    
    //***********************************************************************************************
	//* INICIO: Obteniendo la Tarfia del Tramite
	//***********************************************************************************************
    protected function getTarifa($ID_Tramite):mixed {
        $query = "SELECT TOP (1) B.FechaFin ,B.[CodigoTramite],B.[SalarioMinimo],B.[ValorFraccion],
                                B.[Monto],B.[Normativa],B.[IDHistoricoTarifas],A.DESC_Tarifa
                                FROM [IHTT_Webservice].[dbo].[TB_Tarifas] A,[IHTT_Webservice].[dbo].[TB_TarifasHistorico] B
                                WHERE A.CodigoTramite = B.CodigoTramite AND A.CodigoTramite = :CodigoTramite
                                ORDER BY B.FechaFin DESC";
        try {
            return $this->selectOne($query, Array(':CodigoTramite' => $ID_Tramite));
        } catch (\Throwable $th) {
			$txt = date('Y m d h:i:s') . '	Usuario: ' .   $_SESSION['user_name'] .'getTarifa.php catch '. $query . ' ERROR ' . $th->getMessage();
			logErr($txt, '../logs/logs.txt');
            return false;
		}	
	}
    //***********************************************************************************************
	//* FINAL: Obteniendo la Tarfia del Tramite
	//***********************************************************************************************
    //***********************************************************************************************
	//* INICIO: Salvar Resolucion
	//***********************************************************************************************
    function saveResolucion ($ID_Resolucion,$ID_Solicitud,$id_oficial,$SistemaUsuario) {
        $query = "INSERT INTO [IHTT_GDL].[dbo].[TB_Resolucion] 
                                (ID_Resolucion,ID_Solicitud,ID_OficialJuridico,SistemaUsuario) VALUES 
                                (:ID_Resolucion,:ID_Solicitud,:ID_OficialJuridico,:SistemaUsuario);";
        try {        
            return $this->insert($query, Array(':ID_Resolucion' =>  $ID_Resolucion,':ID_Solicitud' => $ID_Solicitud,':ID_OficialJuridico' => $id_oficial,':SistemaUsuario' => $SistemaUsuario));            
        } catch (\Throwable $th) {
            $txt = date('Y m d h:i:s') . '	Usuario: ' .   $_SESSION['user_name'] .' getConstanciaReplaqueo.php: Update Documento' .  $th->getMessage() . ' QUERY'  . $query;
            logErr($txt,'../logs/logs.txt');
            return false;
        }	  		
    }    
    //***********************************************************************************************
	//* FINAL: Salvar Resolucion
	//***********************************************************************************************
    //***********************************************************************************************
	//* INICIO: Actualizar Aviso de Cobro con el Numero de Expediente
	//***********************************************************************************************
    function updateAvisoCobrobyFsl ($dbsol,$ID_Fsl,$expediente) {
        date_default_timezone_set('America/Guatemala');
        $query = "UPDATE [IHTT_Webservice].[dbo].[TB_AvisoCobroEnc] 
        SET [Expediente] = :Expediente Where  [ID_Solicitud] = :ID_Fsl;";
        try {
            return $this->update($query, Array(':Expediente' => $expediente,':ID_Fsl' => $ID_Fsl));                        
        } catch (\Throwable $th) {
            $txt = date('Y m d h:i:s') . '	Usuario: ' .   $_SESSION['user_name'] .' updateAvisoCobrobyFsl(): Update Documento' .  $th->getMessage() . ' QUERY'  . $query;
            logErr($txt,'../logs/logs.txt');
            return false;
        }
    }
    //***********************************************************************************************
	//* FINAL: Actualizar Aviso de Cobro con el Numero de Expediente
	//***********************************************************************************************
    
    //***********************************************************************************************
	//* INICIO: OBTENER VEHICULO POR PLACA
	//***********************************************************************************************
    protected function getVehiculoxPlaca ($rs_id_rs_placa,$rs_id_placa_antes_replaqueo,$tipo_transporte) {
        //***********************************************************************/
        //*Si el proceso es de Transporte de Carca
        //***********************************************************************/
        if ($tipo_transporte == 'CARGA') {
            $query = "SELECT vehp.ID_Vehiculo_Carga as ID_Vehiculo,vehp.[ID_Placa],
            Veh.ID_Marca,veh.Anio,veh.ID_Tipo_Vehiculo_Carga as ID_Tipo_Vehiculo,tv.[DESC_Tipo_Vehiculo],Veh.ID_Color,Veh.Motor,Veh.Chasis,Veh.VIN,
            Veh.Alto,Veh.Ancho,Veh.Largo
            FROM [IHTT_SGCERP].[dbo].[TB_Vehiculo_Transporte_Carga] Veh,
            [IHTT_SGCERP].[dbo].[TB_Tipo_Vehiculo_Transporte_Carga] tv,
            [IHTT_SGCERP].[dbo].[TB_Vehiculo_Transporte_Carga_x_Placa] vehp
            where veh.ID_Vehiculo_Carga = Veh.ID_Vehiculo_Carga and  veh.ID_Tipo_Vehiculo_Carga = tv.ID_Tipo_Vehiculo_Carga and
            veh.ID_Vehiculo_Carga = vehp.ID_Vehiculo_Carga and (vehp.[ID_Placa] = :ID_Placa or vehp.[ID_Placa] = :ID_Placa_Antes_Replaqueo);";
        //***********************************************************************/
        //Else de Si el proceso es de Transporte de Pasajeros
        //***********************************************************************/
        } else {
            $query = "SELECT veh.ID_Vehiculo_Transporte as ID_Vehiculo,vehp.[ID_Placa],
            Veh.ID_Marca,veh.Anio,veh.ID_Tipo_Vehiculo_Transporte_Pas as ID_Tipo_Vehiculo,tv.[DESC_Tipo_Vehiculo_Transporte_Pas] as DESC_Tipo_Vehiculo,Veh.ID_Color,Veh.Motor,Veh.Chasis,Veh.VIN
            FROM 			
            [IHTT_SGCERP].[dbo].[TB_Vehiculo_Transporte_Pasajero] Veh,
            [IHTT_SGCERP].[dbo].[TB_Tipo_Vehiculo_Transporte_Pasajero] tv,
            [IHTT_SGCERP].[dbo].[TB_Vehiculo_Transporte_Pasajero_x_Placa] vehp
            where veh.ID_Tipo_Vehiculo_Transporte_Pas = tv.ID_Tipo_Vehiculo_Transporte_Pas and
            veh.ID_Vehiculo_Transporte = vehp.ID_Vehiculo_Transporte and (vehp.[ID_Placa] = :ID_Placa  or vehp.[ID_Placa] = :ID_Placa_Antes_Replaqueo);";
        }
        try {
            return $this->selectOne($query,Array(':ID_Placa' => $rs_id_rs_placa,':ID_Placa_Antes_Replaqueo' => $rs_id_placa_antes_replaqueo));
        } catch (\Throwable $th) {
            $txt = date('Y m d h:i:s') . '	Usuario: '.$_SESSION['user_name']  .'getVehiculoxPlaca.php catch '. $query . ' ERROR ' . $th->getMessage();
            logErr($txt,'../logs/logs.txt');
            return false;
        }	
    }
    //***********************************************************************************************
	//* FINAL: OBTENER VEHICULO POR PLACA
	//***********************************************************************************************
    //***********************************************************************************************
	//* INICIO: OBTENER CONCESION ACTUAL CON SU RESPECTIVO VEHICULO
	//***********************************************************************************************
    protected function getCertificadoVehiculo ($rs_id_rs_concesion,$tipo_transporte,$tipo_concesion) {
        //***********************************************************************/
        //Si el proceso es de Transporte de Carca
        //***********************************************************************/
        if ($tipo_transporte == 'CARGA') {
            //***********************************************************************/
            //Si el proceso es de Certificado de Operación de Carga
            //***********************************************************************/
            if ($tipo_concesion == 'CER') {
                $query = "SELECT Con.N_Certificado  as Concesion,Con.ID_Vehiculo_Carga as ID_Vehiculo,vehp.[ID_Placa],
                Veh.ID_Marca,veh.Anio,veh.ID_Tipo_Vehiculo_Carga as ID_Tipo_Vehiculo,tv.DESC_Tipo_Vehiculo,Veh.ID_Color,Veh.Motor,Veh.Chasis,Veh.VIN,
                Veh.Alto,Veh.Ancho,Veh.Largo,Veh.Combustible,veh.Tara,veh.Neto,con.Fecha_Expiracion,Expl.Fecha_Vencimiento as Fecha_Expiracion_Explotacion,Expl.N_Permiso_Explotacion_Encrypted as Permiso_Explotacion_Encrypted,
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
                //***********************************************************************/
                //Si el proceso es de Permiso Especial de Carga
                //***********************************************************************/
                $query = "SELECT Con.N_Permiso_Especial_Carga as Concesion,Con.ID_Vehiculo_Carga as ID_Vehiculo,vehp.[ID_Placa],
                Veh.ID_Marca,veh.Anio,veh.ID_Tipo_Vehiculo_Carga as ID_Tipo_Vehiculo,tv.[DESC_Tipo_Vehiculo],Veh.ID_Color,Veh.Motor,Veh.Chasis,Veh.VIN,
                Veh.Alto,Veh.Ancho,Veh.Largo,Veh.Combustible,veh.Tara,veh.Neto,con.Fecha_Expiracion,Con.N_Permiso_Especial_Carga_Encrypted as Concesion_Encrypted
                FROM 
                [IHTT_SGCERP].[dbo].[TB_Permiso_Especial_Carga] Con,
                [IHTT_SGCERP].[dbo].[TB_Vehiculo_Transporte_Carga] Veh,
                [IHTT_SGCERP].[dbo].[TB_Tipo_Vehiculo_Transporte_Carga] tv,
                [IHTT_SGCERP].[dbo].[TB_Vehiculo_Transporte_Carga_x_Placa] vehp
                where Con.ID_Vehiculo_Carga = Veh.ID_Vehiculo_Carga and  veh.ID_Tipo_Vehiculo_Carga = tv.ID_Tipo_Vehiculo_Carga and 
                Con.ID_Vehiculo_Carga = vehp.ID_Vehiculo_Carga and vehp.Estado = 'ACTIVA' AND N_Permiso_Especial_Carga = :Concesion;";
            }
        //***********************************************************************/
        //Else de Si el proceso es de Transporte de Pasajeros
        //***********************************************************************/
        } else {
            //***********************************************************************/
            //Si el proceso es de Certificado de Operación de Pasajeros
            //***********************************************************************/
            if ($tipo_concesion == 'CER') {
                $query = "SELECT Con.N_Certificado  as Concesion,Con.ID_Vehiculo_Transporte as ID_Vehiculo,vehp.[ID_Placa],
                Veh.ID_Marca,veh.Anio,veh.ID_Tipo_Vehiculo_Transporte_Pas as ID_Tipo_Vehiculo,tv.DESC_Tipo_Vehiculo_Transporte_Pas as DESC_Tipo_Vehiculo,
                Veh.ID_Color,Veh.Motor,Veh.Chasis,Veh.VIN,Veh.Combustible,con.Fecha_Expiracion,
                Expl.Fecha_Vencimiento as Fecha_Expiracion_Explotacion,Expl.N_Permiso_Explotacion_Encrypted as Permiso_Explotacion_Encrypted,
                Con.N_Certificado_Encrypted as Concesion_Encrypted,Expl.N_Permiso_Explotacion as Permiso_Explotacion,Veh.Capacidad
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
                $query = "SELECT Con.N_Permiso_Especial_Pas  as Concesion,Con.ID_Vehiculo_Transporte as ID_Vehiculo,vehp.[ID_Placa],
                Veh.ID_Marca,veh.Anio,veh.ID_Tipo_Vehiculo_Transporte_Pas as ID_Tipo_Vehiculo,tv.DESC_Tipo_Vehiculo_Transporte_Pas as DESC_Tipo_Vehiculo,
                Veh.ID_Color,Veh.Motor,Veh.Chasis,Veh.VIN,Veh.Combustible,con.Fecha_Expiracion,Con.N_Permiso_Especial_Pas_Encrypted as Concesion_Encrypted,
                Veh.Capacidad
                FROM 
                [IHTT_SGCERP].[dbo].[TB_Permiso_Especial_Pas] Con,
                [IHTT_SGCERP].[dbo].[TB_Vehiculo_Transporte_Pasajero] Veh,
                [IHTT_SGCERP].[dbo].[TB_Tipo_Vehiculo_Transporte_Pasajero] tv,
                [IHTT_SGCERP].[dbo].[TB_Vehiculo_Transporte_Pasajero_x_Placa] vehp
                where Con.ID_Vehiculo_Transporte = Veh.ID_Vehiculo_Transporte and veh.ID_Tipo_Vehiculo_Transporte_Pas = tv.ID_Tipo_Vehiculo_Transporte_Pas and
                Con.ID_Vehiculo_Transporte = vehp.ID_Vehiculo_Transporte and vehp.Estado = 'ACTIVA' AND N_Permiso_Especial_Pas = :Concesion;";
            }
        }
        try {
            return $this->selectOne($query,Array(':Concesion' => $rs_id_rs_concesion));
        } catch (\Throwable $th) {
            $txt = date('Y m d h:i:s') . '	' .'getCertificadoVehiculo() catch '. $query . ' ERROR ' . $th->getMessage();
            logErr($txt,'../logs/logs.txt');
            return false;
        }	
    }
    //***********************************************************************************************
	//* FINAL: OBTENER CONCESION ACTUAL CON SU RESPECTIVO VEHICULO
	//***********************************************************************************************

    //***********************************************************************************************
	//* INICIO: OBTENER EL TEMPLATE DESDE LA BASE DE DATOS
	//***********************************************************************************************
    protected function getTemplate ($rs_id_rs_template):mixed {
        $query = "SELECT [id]
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
            $row_rs_template = $this->selectOne($query, Array(':Id' => $rs_id_rs_template));
            return $row_rs_template['template'];
        } catch (\Throwable $th) {
            $txt = date('Y m d h:i:s') . '	Usuario: '. $_SESSION['user_name']  .'getTemplate.php catch '. $query . ' ERROR ' . $th->getMessage();
            logErr($txt, '../logs/logs.txt');
            return false;
        }	    
	}
    //***********************************************************************************************
	//* FINAL: OBTENER EL TEMPLATE DESDE LA BASE DE DATOS
	//***********************************************************************************************

    //***********************************************************************************************
	//* INICIO: OBTENER SIGUIENTE NUMERO DE UNIDAD
	//***********************************************************************************************
    protected function getSiguienteVehiculo ($tipo_transporte) {
            if ($tipo_transporte == 'CARGA') {
                $query = "SELECT TOP 1 CONCAT('VTP-',(CASE WHEN ISNUMERIC(REPLACE(ID_Vehiculo_Carga, 'VTP-', '')) = 1
                THEN CAST(REPLACE(ID_Vehiculo_Carga, 'VTP-', '') AS INT)
                ELSE 0
           END) + 1) AS siguiente_vehiculo
    FROM [IHTT_SGCERP].[dbo].[TB_Vehiculo_Transporte_Carga]
    WHERE ID_Vehiculo_Carga LIKE 'VTP-%'
    ORDER BY (CASE WHEN ISNUMERIC(REPLACE(ID_Vehiculo_Carga, 'VTP-', '')) = 1
                THEN CAST(REPLACE(ID_Vehiculo_Carga, 'VTP-', '') AS INT)
                ELSE 0
           END) + 1 DESC;";
            } else {
                $query = "SELECT TOP 1 CONCAT('VTP-',(CASE WHEN ISNUMERIC(REPLACE(ID_Vehiculo_Transporte, 'VTP-', '')) = 1
                THEN CAST(REPLACE(ID_Vehiculo_Transporte, 'VTP-', '') AS INT)
                ELSE 0
           END) + 1) AS siguiente_vehiculo
    FROM [IHTT_SGCERP].[dbo].[TB_Vehiculo_Transporte_Pasajero]
    WHERE ID_Vehiculo_Transporte LIKE 'VTP-%'
    ORDER BY (CASE WHEN ISNUMERIC(REPLACE(ID_Vehiculo_Transporte, 'VTP-', '')) = 1
                THEN CAST(REPLACE(ID_Vehiculo_Transporte, 'VTP-', '') AS INT)
                ELSE 0
           END) + 1 DESC;";
            }
        try {
            return $this->selectOne($query,array());
        } catch (\Throwable $th) {
            $txt = date('Y m d h:i:s') . '	Usuario:' . $_SESSION['user_name']  .'getSiguienteVehiculo.php catch '. $query . ' ERROR ' . $th->getMessage();
            logErr($txt,'../logs/logs.txt');
            return false;
        }	
    }    
    //***********************************************************************************************
	//* FINAL: OBTENER SIGUIENTE NUMERO DE UNIDAD
	//***********************************************************************************************

    //***********************************************************************************************
	//* INICIO: Insertar Placa
	//***********************************************************************************************
    protected function insertPlaca ($rs_id_rs_placa) {
        try {
            $query = "SELECT [ID_Placa] FROM [IHTT_SGCERP].[dbo].[TB_Placa] WHERE [ID_Placa] = :ID_Placa;";
            $datos = $this->selectOne($query,Array(':ID_Placa' => $rs_id_rs_placa));
            if ($datos == false) {
                return false;
            } else {
                if (!isset($datos['ID_Placa'])) {
                    $query = "INSERT INTO [IHTT_SGCERP].[dbo].[TB_Placa] ([ID_Placa],[ID_Tipo_Placa]) VALUES(:ID_Placa,'TP-02');";
                    return $this->insert($query,Array(':ID_Placa' => $rs_id_rs_placa));
                } else {
                    return true;
                }			
            }		
        } catch (\Throwable $th) {
            $txt = date('Y m d h:i:s') . '	Usuario:' . $_SESSION['user_name']  .'insertPlaca.php catch '. $query . ' ERROR ' . $th->getMessage();
            logErr($txt,'../logs/logs.txt');
            return false;
        }	
    }    
    //***********************************************************************************************
	//* FINAL: Insertar Placa
	//***********************************************************************************************  
    function insertVehiculoPlaca ($id_vehiculo,$id_placa,$tipo_transporte) {
        if ($tipo_transporte == 'CARGA') {
            //******************************************************************************************/
            //Aramando QUERY para insertar registro [IHTT_SGCERP].[dbo].[TB_Vehiculo_Transporte_Carga]
            //******************************************************************************************/					
            $query = "INSERT INTO [IHTT_SGCERP].[dbo].[TB_Vehiculo_Transporte_Carga_x_Placa]
            ([ID_Placa],[ID_Vehiculo_Carga],[Estado],[Sistema_Usuario],[Sistema_Fecha])
            VALUES (:ID_Placa,:ID_Vehiculo,'ACTIVA',:Sistema_Usuario,SYSDATETIME())";
            // Recueprando la informaci[on del expediente
        } else {
            $query = "INSERT INTO [IHTT_SGCERP].[dbo].[TB_Vehiculo_Transporte_Pasajero_x_Placa]
            ([ID_Placa],[ID_Vehiculo_Transporte],[Estado],[Sistema_Usuario],[Sistema_Fecha])
            VALUES (:ID_Placa,:ID_Vehiculo,'ACTIVA',:Sistema_Usuario,SYSDATETIME())";
        }
        try {
            return $this->insert($query,Array(':ID_Placa' => $id_placa,':ID_Vehiculo' => $id_vehiculo,':Sistema_Usuario' => $_SESSION['user_name']));
        } catch (\Throwable $th) {
            $txt = date('Y m d h:i:s') . '	Usuario: ' . $_SESSION['user_name'] .'insertVehiculoHistorico.php catch RTBM'. $query . ' ERROR ' . $th->getMessage();
            logErr($txt,'../logs/logs.txt');
            return false;
        }	        
    }      
    //***********************************************************************************************
	//* FINAL: Insertar Vehiculo
	//***********************************************************************************************    
    //***********************************************************************************************
	//* INICIO: Insertar Vehiculo
	//***********************************************************************************************
    protected function insertVehiculo ($vehiculoanterior,$vehiculonuevo,$tipo_transporte) {
        //*****************************************************************************/
        //*Salvando la placa nueva en la tabla [IHTT_SGCERP].[dbo].[TB_Placa]  
        //*****************************************************************************/
        $respuestainsertPlaca=$this->insertPlaca($vehiculonuevo['ID_Placa']);
        if ($respuestainsertPlaca != false) {
            //*****************************************************************************/
            //*Recuperando al esiguiente ID_VEHICULO
            //*****************************************************************************/
            $respuestagetSiguienteVehiculo = $this->getSiguienteVehiculo($tipo_transporte);
            if ($respuestagetSiguienteVehiculo != false) {
                $record = $respuestagetSiguienteVehiculo;
                if ($tipo_transporte == 'CARGA') {
                    //******************************************************************************************/
                    //Aramando QUERY para insertar registro [IHTT_SGCERP].[dbo].[TB_Vehiculo_Transporte_Carga]
                    //******************************************************************************************/					
                    $query = "INSERT INTO [IHTT_SGCERP].[dbo].[TB_Vehiculo_Transporte_Carga]
                    ([ID_Vehiculo_Carga],[ID_Tipo_Vehiculo_Carga],[ID_Marca],[Motor],[VIN],[Ancho],[Alto],[Largo],[RTN_Propietario],[Nombre_Revicion],[ID_Color],[Anio],
                    [Combustible],[Chasis],[Observaciones],[Capacidad],[Sistema_Usuario],[Sistema_Fecha])
                    VALUES (:ID_Vehiculo_Carga,:ID_Tipo_Vehiculo_Carga,:ID_Marca,:Motor,:VIN,:Ancho,:Alto,:Largo,:RTN_Propietario,:Nombre_Revicion,:ID_Color,:Anio,:Combustible,:Chasis,'GENERADO POR RENOVACIONES AUTOMATICAS',0,:Sistema_Usuario,SYSDATETIME())";
                    try {
                        $vehiculo = $this->insert($query,Array(':ID_Vehiculo_Carga' => $record['siguiente_vehiculo'],':ID_Tipo_Vehiculo_Carga' => $vehiculoanterior['ID_Tipo_Vehiculo'],':ID_Marca' => $vehiculonuevo['ID_Marca'],
                        ':Motor' => $vehiculonuevo['Motor'] ,':VIN' => $vehiculonuevo['VIN'],':Ancho' =>$vehiculonuevo['Ancho'],':Alto' => $vehiculonuevo['Alto'],
                        ':Largo' => $vehiculonuevo['Largo'],':RTN_Propietario'  => $vehiculonuevo['RTN_Propietario'] , 
                        ':Nombre_Revicion'  => $vehiculonuevo['Nombre_Propietario'] ,
                        ':ID_Color'  => $vehiculonuevo['ID_Color'] ,
                        ':Anio'  => $vehiculonuevo['Anio'] ,
                        ':Combustible'  => $vehiculonuevo['Combustible'] , ':Chasis'  => $vehiculonuevo['Chasis'] ,':Sistema_Usuario' => $_SESSION['user_name'] ));
                        if ($vehiculo == false) {
                            return false;
                        } else {
                            //******************************************************************************************/
                            //Llamando funcion para salvar vehiculo x placa
                            //******************************************************************************************/					
                            $respuestasaveVehiculoPlaca = $this->insertVehiculoPlaca ($respuestagetSiguienteVehiculo['siguiente_vehiculo'],$vehiculonuevo['ID_Placa'],$tipo_transporte);
                            if ($respuestasaveVehiculoPlaca != false) {
                                return $record['siguiente_vehiculo'];
                            } else {
                                $txt = date('Y m d h:i:s') . '9145-Usuario: ' . $_SESSION['user_name'] .'insertVehiculo() if ($respuestasaveVehiculoPlaca != false) {';
                                logErr($txt,'../logs/logs.txt');
                                return false;
                            }
                        }
                    } catch (\Throwable $th) {
                        $txt = date('Y m d h:i:s') . '	' .'catch insertVehiculo Carga '. $query . ' ERROR ' . $th->getMessage();
                        logErr($txt,'../logs/logs.txt');
                        return false;
                    }				
                } else {
                    //******************************************************************************************/
                    //Aramando QUERY para insertar registro [IHTT_SGCERP].[dbo].[TB_Vehiculo_Transporte_Pasajero]
                    //******************************************************************************************/					
                    $query = "INSERT INTO [IHTT_SGCERP].[dbo].[TB_Vehiculo_Transporte_Pasajero]
                    ([ID_Vehiculo_Transporte],
                        [ID_Tipo_Vehiculo_Transporte_Pas],
                        [Motor],
                        [VIN],
                        [ID_Marca],
                        [RTN_Propietario],
                        [Nombre_Revicion],
                        [ID_Color],
                        [Anio],
                        [Combustible],
                        [Capacidad],
                        [Chasis],
                        [Observaciones],
                        [Sistema_Usuario],
                        [Sistema_Fecha])
                    VALUES (:ID_Vehiculo_Transporte,
                            :ID_Tipo_Vehiculo_Transporte_Pas,
                            :Motor,
                            :VIN,
                            :ID_Marca,
                            :RTN_Propietario,
                            :Nombre_Revicion,
                            :ID_Color,
                            :Anio,
                            :Combustible,
                            :Capacidad,
                            :Chasis,
                            'GENERADO POR RENOVACIONES AUTOMATICAS',
                            :Sistema_Usuario,
                            SYSDATETIME())";
                    try {
                        $vehiculo = $this->insert($query,Array(':ID_Vehiculo_Transporte' => $record['siguiente_vehiculo'],
                        ':ID_Tipo_Vehiculo_Transporte_Pas' => $vehiculoanterior['ID_Tipo_Vehiculo'],
                        ':Motor' => $vehiculonuevo['Motor'] ,
                        ':VIN' => $vehiculonuevo['VIN'],
                        ':ID_Marca' => $vehiculonuevo['ID_Marca'],
                        ':RTN_Propietario'  => $vehiculonuevo['RTN_Propietario'],
                        ':Nombre_Revicion'  => $vehiculonuevo['Nombre_Propietario'] ,
                        ':ID_Color'  => $vehiculonuevo['ID_Color'] ,
                        ':Anio'  => $vehiculonuevo['Anio'],
                        ':Combustible' => $vehiculonuevo['Combustible'],
                        ':Capacidad' =>  $vehiculoanterior['Capacidad'],
                        ':Chasis'  => $vehiculonuevo['Chasis'],
                        ':Sistema_Usuario' => $_SESSION['user_name']));
                        if ($vehiculo == false) {
                            return false;
                        } else {
                            $respuestasaveVehiculoPlaca = $this->insertVehiculoPlaca ($respuestagetSiguienteVehiculo['siguiente_vehiculo'],$vehiculonuevo['ID_Placa'],$tipo_transporte);
                            if ($respuestasaveVehiculoPlaca != false) {
                                return $record['siguiente_vehiculo'];
                            }
                        }
                    } catch (\Throwable $th) {
                        $txt = date('Y m d h:i:s') . ' 9049-Usuario: ' . $_SESSION['user_name']  . 'catch insertVehiculo '. $query . ' ERROR ' . $th->getMessage();
                        logErr($txt,'../logs/logs.txt');
                        return false;
                    }
                }
            } else {
                $txt = date('Y m d h:i:s') . ' 9053-Usuario: ' . $_SESSION['user_name']  . 'if ($respuestagetSiguienteVehiculo != false) { '. $query . ' ERROR ' . $th->getMessage();
                logErr($txt,'../logs/logs.txt');
                return false;
            }
        } 
    }
    //***********************************************************************************************
	//* FINAL: Insertar Vehiculo
	//***********************************************************************************************

    //***********************************************************************************************
	//* FINAL: Insertar en Certificado Historico
	//***********************************************************************************************
    protected function insertCertificadoHistorico ($rs_id_rs_concesion,$tipo_transporte,$tipo_concesion) {
        $respuesta[0]['msg'] = "";
        $respuesta[0]['error'] = false;	
        $respuesta[0]['errorcode'] = '';
        //***********************************************************************/
        //Si el proceso es de Transporte de Carca
        //***********************************************************************/
        if ($tipo_transporte == 'CARGA') {
            //***********************************************************************/
            //Si el proceso es de Certificado de Operación de Carga
            //***********************************************************************/
            if ($tipo_concesion == 'CER') {
                $query = "INSERT INTO [IHTT_SGCERP].[dbo].[TB_Historico_Certificado_Carga] ( N_Certificado, N_Certificado_Encrypted, N_Permiso_Explotacion, ID_Socio, RTN_Concesionario, ID_Vehiculo_Carga, Resolucion, ID_Expediente, Fecha_Emision, Fecha_Elaboracion, Fecha_Expiracion, ID_Categoria, ID_Tipo_Categoria, ID_Colegiacion, Numero_Registro, Direccion, ID_Estado, Codigo_Censo, Observaciones, Certificado_Anterior, Sistema_Usuario, Sistema_Fecha, Plan_Prorenova, Tipo_Generacion, URL_Constancia_Unidad) SELECT c.N_Certificado, c.N_Certificado_Encrypted, c.N_Permiso_Explotacion, cc.ID_Socio, c.RTN_Concesionario, c.ID_Vehiculo_Carga, c.Resolucion, c.ID_Expediente, c.Fecha_Emision, c.Fecha_Elaboracion, c.Fecha_Expiracion, c.ID_Categoria, c.ID_Tipo_Categoria, c.ID_Colegiacion, c.Numero_Registro, c.Direccion, c.ID_Estado, c.Codigo_Censo, c.Observaciones, c.Certificado_Anterior, c.Sistema_Usuario, c.Sistema_Fecha, c.Plan_Prorenova, c.Tipo_Generacion, c.URL_Constancia_Unidad FROM [IHTT_SGCERP].[dbo].[TB_Certificado_Carga] AS c LEFT OUTER JOIN [IHTT_SGCERP].[dbo].[TB_Contactos_Certificados] AS cc ON cc.N_Certificado = c.N_Certificado WHERE c.N_Certificado = :Concesion;";
            } else {
                //***********************************************************************/
                //Si el proceso es de Permiso Especial de Carga
                //***********************************************************************/
                $query = "INSERT INTO [IHTT_SGCERP].[dbo].[TB_Historico_Permiso_Especial_Carga] ( N_Permiso_Especial_Carga, N_Permiso_Especial_Carga_Encrypted, N_Certificado, RTN_Concesionario, ID_Vehiculo_Carga, Resolucion, ID_Expediente, Fecha_Emision, Fecha_Elaboracion, Fecha_Expiracion, ID_Categoria, ID_Colegiacion, Numero_Registro, Direccion, Codigo_Censo, Observaciones, ID_Estado, Sistema_Usuario, Sistema_Fecha, ID_Tipo_Categoria, Plan_Prorenova, Codigo_Aduanero, Tipo_Generacion, URL_Constancia_Unidad, Reimpresion) SELECT N_Permiso_Especial_Carga, N_Permiso_Especial_Carga_Encrypted, N_Certificado, RTN_Concesionario, ID_Vehiculo_Carga, Resolucion, ID_Expediente, Fecha_Emision, Fecha_Elaboracion, Fecha_Expiracion, ID_Categoria, ID_Colegiacion, Numero_Registro, Direccion, Codigo_Censo, Observaciones, ID_Estado, Sistema_Usuario, Sistema_Fecha, ID_Tipo_Categoria, Plan_Prorenova, Codigo_Aduanero, Tipo_Generacion, URL_Constancia_Unidad, Reimpresion FROM [IHTT_SGCERP].[dbo].[TB_Permiso_Especial_Carga] WHERE N_Permiso_Especial_Carga = :Concesion;";
            }
        //***********************************************************************/
        //Else de Si el proceso es de Transporte de Pasajeros
        //***********************************************************************/
        } else {
            //***********************************************************************/
            //Si el proceso es de Certificado de Operación de Pasajeros
            //***********************************************************************/
            if ($tipo_concesion == 'CER') {
                $query = "INSERT INTO [IHTT_SGCERP].[dbo].[TB_Historico_Certificado_Pasajeros] ( N_Certificado, N_Certificado_Encrypted, N_Permiso_Explotacion, ID_Socio, RTN_Concesionario, ID_Vehiculo_Transporte, ID_Expediente, Fecha_Emision, Fecha_Elaboracion, Fecha_Expiracion, ID_Categoria, ID_Colegiacion, Direccion, Resolucion, ID_Ruta, Cod_Ruta, DESC_Ruta, Ruta_Intermedia, Cod_Horario, HoraInicio, HoraFinal, Cod_Frecuencia_Salida, Frecuencia, Cod_Tarifa, Tarifa, ID_Estado, Codigo_Censo, Observaciones, Numero_Registro, Certificado_Anterior, Sistema_Usuario, Sistema_Fecha, Plan_Prorenova ) SELECT c.N_Certificado, c.N_Certificado_Encrypted, c.N_Permiso_Explotacion, cc.ID_Socio, c.RTN_Concesionario, c.ID_Vehiculo_Transporte, c.ID_Expediente, c.Fecha_Emision, c.Fecha_Elaboracion, c.Fecha_Expiracion, c.ID_Categoria, c.ID_Colegiacion, c.Direccion, c.Resolucion, c.ID_Ruta, r.Cod_Ruta, dr.Descripcion, dr.RutaIntermedia, r.Cod_Horario, rh.HoraInicio, rh.HoraFinal, r.Cod_Frecuencia_Salida, rf.Frecuencia, r.Cod_Tarifa, rt.Tarifa, c.ID_Estado, c.Codigo_Censo, c.Observaciones, c.Numero_Registro, c.Certificado_Anterior, c.Sistema_Usuario, c.Sistema_Fecha, c.Plan_Prorenova 
                FROM [IHTT_SGCERP].[dbo].[TB_Certificado_Pasajeros] AS c LEFT OUTER JOIN [IHTT_SGCERP].[dbo].[TB_Rutas] AS r ON r.ID_Ruta = c.ID_Ruta LEFT OUTER JOIN [IHTT_SIPET].[dbo].[v_rutas_certificado] AS dr ON dr.CodigoRutaDescriptivo = r.Cod_Ruta LEFT OUTER JOIN [IHTT_SIPET].[dbo].[TB_Ruta_Horario] AS rh ON rh.CodigoHorario = r.Cod_Horario LEFT OUTER JOIN [IHTT_SIPET].[dbo].[TB_Ruta_Frecuencia] AS rf ON rf.CodigoFrecuencia = r.Cod_Frecuencia_Salida LEFT OUTER JOIN [IHTT_SIPET].[dbo].[TB_Ruta_Tarifa] AS rt ON rt.CodigoTarifa = r.Cod_Tarifa LEFT OUTER JOIN [IHTT_SGCERP].[dbo].[TB_Contactos_Certificados] AS cc ON cc.N_Certificado = c.N_Certificado WHERE c.N_Certificado = :Concesion;";
            } else {
                //***********************************************************************/
                //Si el proceso es de Permiso Especial de Pasajeros
                //***********************************************************************/
                $query = "INSERT INTO [IHTT_SGCERP].[dbo].[TB_Historico_Permiso_Especial_Pasajero] ( N_Permiso_Especial_Pas, N_Permiso_Especial_Pas_Encrypted, N_Certificado, RTN_Concesionario, ID_Departamento, ID_Vehiculo_Transporte, ID_Expediente, Fecha_Emision, Fecha_Elaboracion, Fecha_Expiracion, ID_Categoria, ID_Colegiacion, Direccion, Resolucion, ID_Estado, Codigo_Censo, Observaciones, Numero_Registro, Frecuencia, Sistema_Usuario, Sistema_Fecha, ID_Area_Operacion, Plan_Prorenova ) SELECT p.N_Permiso_Especial_Pas, p.N_Permiso_Especial_Pas_Encrypted, p.N_Certificado, p.RTN_Concesionario, p.ID_Departamento, p.ID_Vehiculo_Transporte, p.ID_Expediente, p.Fecha_Emision, p.Fecha_Elaboracion, p.Fecha_Expiracion, p.ID_Categoria, p.ID_Colegiacion, p.Direccion, p.Resolucion, p.ID_Estado, p.Codigo_Censo, p.Observaciones, p.Numero_Registro, rf.Frecuencia, p.Sistema_Usuario, p.Sistema_Fecha, reg.ID_Area_Operacion, p.Plan_Prorenova FROM [IHTT_SGCERP].[dbo].[TB_Permiso_Especial_Pas] AS p LEFT OUTER JOIN [IHTT_SIPET].[dbo].[TB_Ruta_Frecuencia] AS rf ON rf.CodigoFrecuencia = p.Cod_Frecuencia_Salida LEFT OUTER JOIN [IHTT_SGCERP].[dbo].[TB_Registro_Vehicular] AS reg ON reg.ID_Registro = p.Numero_Registro WHERE N_Permiso_Especial_Pas = :Concesion;";
            }
        }
        try {
            return $this->insert($query,Array(':Concesion' => $rs_id_rs_concesion));
        } catch (\Throwable $th) {
            $txt = date('Y m d h:i:s') . '	Usuario:' . $_SESSION['user_name']  .'insertCertificadoHistorico.php catch '. $query . ' ERROR ' . $th->getMessage();
            logErr($txt,'../logs/logs.txt');
            return false;
        }	
    }    
    //***********************************************************************************************
	//* FINAL: Insertar en Certificado Historico
	//***********************************************************************************************

    //***********************************************************************************************
	//* INICIO: ACTUALIZACIÓN DE CONCESION
	//***********************************************************************************************
    function updateCertificado ($tipo_transporte, $tipo_concesion, $id_concesion, $id_vehiculo, $id_expediente, $Fecha_Expiracion, $id_resolucion, $id_apoderado, $id_comisionado) {
        $query = "";
        try {
        //***********************************************************************/
        //Si el proceso es de Transporte de Carca
        //***********************************************************************/
        if ($tipo_transporte == 'CARGA') {
            //***********************************************************************/
            //Si el proceso es de Certificado de Operación de Carga
            //***********************************************************************/
            if ($tipo_concesion == 'CER') {
                $query = "UPDATE [IHTT_SGCERP].[dbo].[TB_Certificado_Carga] set ID_Vehiculo_Carga = :ID_Vehiculo_Carga,
                ID_Expediente=:ID_Expediente,Fecha_Emision=SYSDATETIME(), Fecha_Expiracion=CONVERT(DATE,:Fecha_Expiracion, 111), ID_Colegiacion=:ID_Colegiacion, Resolucion=:Resolucion, 
                Sistema_Usuario=:Sistema_Usuario, Sistema_Fecha=SYSDATETIME(),
                ID_Estado='ES-02', Observaciones='RENOVACIONES AUTOMATICAS, GENERADO POR PROGRAMA',Comisionado_Gestion=:Comisionado_Gestion
                where N_Certificado=:Concesion;";
                $p=Array(':ID_Vehiculo_Carga' => $id_vehiculo,
                                            ':ID_Expediente' => $id_expediente,
                                            ':Fecha_Expiracion' => $Fecha_Expiracion,
                                            ':ID_Colegiacion' => $id_apoderado,
                                            ':Resolucion' => $id_resolucion,
                                            ':Sistema_Usuario' => $_SESSION['user_name'],
                                            ':Comisionado_Gestion' => $id_comisionado,
                                            ':Concesion' => $id_concesion);
            } else {
                //***********************************************************************/
                //Si el proceso es de Permiso Especial de Carga
                //***********************************************************************/
                $query = "UPDATE [IHTT_SGCERP].[dbo].[TB_Permiso_Especial_Carga] set 
                ID_Vehiculo_Carga=:ID_Vehiculo_Carga,ID_Expediente=:ID_Expediente, 
                Fecha_Emision=SYSDATETIME(),Fecha_Expiracion=CONVERT(DATE,:Fecha_Expiracion, 111),
                ID_Colegiacion=:ID_Colegiacion, Resolucion=:Resolucion, 
                Sistema_Usuario=:Sistema_Usuario,Sistema_Fecha=SYSDATETIME(), ID_Estado='ES-02', 
                Observaciones='RENOVACIONES AUTOMATICAS, GENERADO POR PROGRAMA', 
                Comisionado_Gestion=:Comisionado_Gestion where N_Permiso_Especial_Carga=:Concesion;";
                $p = Array(':ID_Vehiculo_Carga' => $id_vehiculo,
                                            ':ID_Expediente' => $id_expediente,
                                            ':Fecha_Expiracion' => $Fecha_Expiracion,
                                            ':ID_Colegiacion' => $id_apoderado,
                                            ':Resolucion' => $id_resolucion,
                                            ':Sistema_Usuario' => $_SESSION['user_name'],
                                            ':Comisionado_Gestion' => $id_comisionado,
                                            ':Concesion' => $id_concesion);
            }
        //***********************************************************************/
        //Else de Si el proceso es de Transporte de Pasajeros
        //***********************************************************************/
        } else {
            //***********************************************************************/
            //Si el proceso es de Certificado de Operación de Pasajeros
            //***********************************************************************/
            if ($tipo_concesion == 'CER') {
                //***********************************************************************/
                //Si el proceso es de Certificado de Pasajeros
                //***********************************************************************/
                $query = "UPDATE [IHTT_SGCERP].[dbo].[TB_Certificado_Pasajeros] set 
                ID_Vehiculo_Transporte=:ID_Vehiculo_Transporte,
                ID_Expediente=:ID_Expediente, 
                Fecha_Emision=SYSDATETIME(), 
                Fecha_Expiracion=CONVERT(DATE,:Fecha_Expiracion, 111),
                ID_Colegiacion=:ID_Colegiacion,
                Resolucion=:Resolucion, 
                Sistema_Usuario=:Sistema_Usuario, 
                Sistema_Fecha=SYSDATETIME(), 
                ID_Estado='ES-02', 
                Observaciones='RENOVACIONES AUTOMATICAS, GENERADO POR PROGRAMA', 
                Comisionado_Gestion=:Comisionado_Gestion 
                where N_Certificado=:Concesion;";
                $p = Array(':ID_Vehiculo_Transporte' => $id_vehiculo,
                                            ':ID_Expediente' => $id_expediente,
                                            ':Fecha_Expiracion' => $Fecha_Expiracion,
                                            ':ID_Colegiacion' => $id_apoderado,
                                            ':Resolucion' => $id_resolucion,
                                            ':Sistema_Usuario' => $_SESSION['user_name'],
                                            ':Comisionado_Gestion' => $id_comisionado,
                                            ':Concesion' => $id_concesion);
            } else {
                //***********************************************************************/
                //Si el proceso es de Permiso Especial de Pasajeros
                //***********************************************************************/
                $query = "UPDATE [IHTT_SGCERP].[dbo].[TB_Permiso_Especial_Pas] set 
                ID_Vehiculo_Transporte=:ID_Vehiculo_Transporte,ID_Expediente=:ID_Expediente, 
                Fecha_Emision=SYSDATETIME(), Fecha_Expiracion=CONVERT(DATE,:Fecha_Expiracion, 111), ID_Colegiacion=:ID_Colegiacion, Resolucion=:Resolucion, Sistema_Usuario=:Sistema_Usuario, Sistema_Fecha=SYSDATETIME(), ID_Estado='ES-02', Observaciones='RENOVACIONES AUTOMATICAS, GENERADO POR PROGRAMA', Comisionado_Gestion=:Comisionado_Gestion
                 where N_Permiso_Especial_Pas=:Concesion;";
                $p =  Array(':ID_Vehiculo_Transporte' => $id_vehiculo,
                                            ':ID_Expediente' => $id_expediente,
                                            ':Fecha_Expiracion' => $Fecha_Expiracion,
                                            ':ID_Colegiacion' => $id_apoderado,
                                            ':Resolucion' => $id_resolucion,
                                            ':Sistema_Usuario' => $_SESSION['user_name'],
                                            ':Comisionado_Gestion' => $id_comisionado,
                                            ':Concesion' => $id_concesion);
                }	
            }
            return $this->update($query,$p);
        } catch (\Throwable $th) {
            $txt = date('Y m d h:i:s') . '	Usuario: ' . $_SESSION['user_name']  .'updateCertificado.php catch '. $query . ' ERROR ' . $th->getMessage();
            logErr($txt,'../logs/logs.txt');
            return false;
        }	
    }
    //***********************************************************************************************
	//* FINAL: ACTUALIZACIÓN DE CONCESION
	//***********************************************************************************************

    //***********************************************************************************************
	//* INICIO: INSERTAR LA BITACORA DEL CAMBIO DE TRAMITES
	//***********************************************************************************************
    function insertBitacoraCambioTramites ($Evento, $Tipo_Evento, $Numero_Documento, $ID_Expediente, $ID_Clase_Servicio, $Tipo_Documento, $Replaqueo) {
            $query = "INSERT INTO [IHTT_SGCERP].[dbo].[TB_Bitacora_Cambio_Tramites] 
            (Evento, 
             Usuario,
             Tipo_Evento,
             Numero_Documento,
             ID_Expediente,
             ID_Clase_Servicio,
             Tipo_Documento,
             Replaqueo,
             Sistema_Fecha) 
            VALUES(:Evento,
                   :Usuario,
                   :Tipo_Evento,
                   :Numero_Documento,
                   :ID_Expediente,
                   :ID_Clase_Servicio,
                   :Tipo_Documento,
                   :Replaqueo,
                   SYSDATETIME())";
        try {
            return $this->insert($query,Array(':Evento' =>mb_convert_encoding($Evento, 'UTF-8', 'ISO-8859-1'),
            ':Usuario' => $_SESSION['user_name'],
            ':Tipo_Evento' =>  mb_convert_encoding($Tipo_Evento, 'UTF-8', 'ISO-8859-1'),
            ':Numero_Documento' => $Numero_Documento,
            ':ID_Expediente' => $ID_Expediente,
            ':ID_Clase_Servicio' => $ID_Clase_Servicio,
            ':Tipo_Documento' => $Tipo_Documento,
            ':Replaqueo' => $Replaqueo));                                        
        } catch (\Throwable $th) {
            $txt = date('Y m d h:i:s') . '	Usuario: ' . $_SESSION['user_name'] . 'insertBitacoraCambioTramite() catch '. $query . ' ERROR ' . $th->getMessage();
            logErr($txt,'../logs/logs.txt');
            return false;
        }	
    }    
    //***********************************************************************************************
	//* INICIO: INSERTAR LA BITACORA DEL CAMBIO DE TRAMITES
	//***********************************************************************************************

    //***********************************************************************************************
	//* INICIO: INSERTAR EL VEHICULO EN LA TABLA DE HISTORICOS DE VEHICULOS (UNDIADES)
	//***********************************************************************************************
    function insertVehiculoHistorico ($rs_id_rs_vehiculo,$tipo_transporte) {
        //***********************************************************************/
        //* Si el proceso es de Transporte de Carca
        //***********************************************************************/
        if ($tipo_transporte == 'CARGA') {
            $query = "INSERT INTO [IHTT_SGCERP].[DBO].[TB_Historico_Vehiculo_Transporte_Carga]
            ( ID_Vehiculo_Carga, ID_Tipo_Vehiculo_Carga, DESC_Vehiculo_Carga, ID_Marca, Motor, VIN, Tara, 
            Neto, Ancho, Alto, Largo, RTN_Propietario, Nombre_Revicion, ID_Color, DESC_Color, Anio, 
            Combustible, Chasis, Observaciones, Capacidad, ID_Inspeccion, 
            ID_Piloto, Repotenciacion, Fecha_Repotenciacion, Sistema_Usuario, 
            Sistema_Fecha, ID_Tipo_Remolque, ID_Placa ) 
            SELECT top 1 a.ID_Vehiculo_Carga, b.ID_Tipo_Vehiculo_Carga, a.DESC_Vehiculo_Carga, a.ID_Marca, a.Motor, a.VIN, a.Tara, a.Neto, a.Ancho, a.Alto, a.Largo, a.RTN_Propietario, a.Nombre_Revicion, a.ID_Color, cv.DESC_Color, a.Anio, a.Combustible, a.Chasis, a.Observaciones, a.Capacidad, a.ID_Inspeccion, a.ID_Piloto, a.Repotenciacion, a.Fecha_Repotenciacion, a.Sistema_Usuario, a.Sistema_Fecha, a.ID_Tipo_Remolque, cp.ID_Placa 
            FROM [IHTT_SGCERP].[DBO].[TB_Vehiculo_Transporte_Carga] a INNER JOIN [IHTT_SGCERP].[DBO].[TB_Tipo_Vehiculo_Transporte_Carga] b 
            ON b.ID_Tipo_Vehiculo_Carga = a.ID_Tipo_Vehiculo_Carga 
            INNER JOIN [IHTT_SGCERP].[DBO].[TB_Vehiculo_Transporte_Carga_x_Placa] cp 
            ON cp.ID_Vehiculo_Carga = A.ID_Vehiculo_Carga 
            INNER JOIN [IHTT_SGCERP].[DBO].[TB_Color_Vehiculos] cv 
            ON cv.ID_Color = a.ID_Color 
            WHERE a.ID_Vehiculo_Carga = :ID_Vehiculo;";
        //***********************************************************************/
        //*Else de Si el proceso es de Transporte de Pasajeros
        //***********************************************************************/
        } else {
            //***********************************************************************/
            //*Si el proceso es de Permiso Especial de Pasajeros
            //***********************************************************************/
            $query = "INSERT INTO [IHTT_SGCERP].[DBO].[TB_Historico_Vehiculo_Transporte_Pasajero] ( ID_Vehiculo_Transporte, ID_Inspeccion, ID_Piloto, Motor, VIN, 
                    ID_Marca, ID_Tipo_Vehiculo_Transporte_Pas, DESC_Vehiculo_Pas, RTN_Propietario, Nombre_Revicion, ID_Color, DESC_Color, Anio, Combustible, Capacidad, 
                    Chasis, Observaciones, Sistema_Usuario, Sistema_Fecha, ID_Placa ) 
                    SELECT top 1 v.ID_Vehiculo_Transporte, v.ID_Inspeccion, v.ID_Piloto, v.Motor, v.VIN, v.ID_Marca, v.ID_Tipo_Vehiculo_Transporte_Pas, 
                    v.DESC_Vehiculo_Pas, v.RTN_Propietario, v.Nombre_Revicion, v.ID_Color, cv.DESC_Color, v.Anio, v.Combustible, v.Capacidad, v.Chasis, 
                    v.Observaciones, v.Sistema_Usuario, v.Sistema_Fecha, vp.ID_Placa 
                    FROM [IHTT_SGCERP].[DBO].[TB_Vehiculo_Transporte_Pasajero] AS v 
                    INNER JOIN [IHTT_SGCERP].[DBO].[TB_Vehiculo_Transporte_Pasajero_x_Placa] AS vp 
                    ON vp.ID_Vehiculo_Transporte = v.ID_Vehiculo_Transporte 
                    INNER JOIN [IHTT_SGCERP].[DBO].[TB_Color_Vehiculos] AS cv ON cv.ID_Color = v.ID_Color
                    WHERE v.ID_Vehiculo_Transporte = :ID_Vehiculo;";
        }
        try {
            return $this->insert($query,Array(':ID_Vehiculo' => $rs_id_rs_vehiculo));
        } catch (\Throwable $th) {
            $txt = date('Y m d h:i:s') . '	Usuario: ' . $_SESSION['user_name'] .'insertVehiculoHistorico.php catch '. $query . ' ERROR ' . $th->getMessage();
            logErr($txt,'../logs/logs.txt');
            return false;
        }	
    }    
    //***********************************************************************************************
	//* FINAL: INSERTAR EL VEHICULO EN LA TABLA DE HISTORICOS DE VEHICULOS (UNDIADES)
	//***********************************************************************************************

    //**********************************************************************************************/
    //* INICIO: INSERTAR EN HISTORICO DE PERMISOS DE EXPLOTACIÓN
    //**********************************************************************************************/
    protected function insertPerExpHistorico ($rs_id_rs_concesion,$tipo_transporte) {
        //***********************************************************************/
        //* Si el proceso es de Transporte de Carca
        //***********************************************************************/
        if ($tipo_transporte == 'CARGA') {
            //***********************************************************************/
            //* Si el proceso es de Certificado de Operación de Carga
            //***********************************************************************/
            $query = "INSERT INTO [IHTT_SGCERP].[dbo].[TB_Historico_Permiso_Explotacion_Carga] ( N_Permiso_Explotacion, N_Permiso_Explotacion_Encrypted, RTN_Concesionario, ID_Representante_Legal, ID_Colegiacion, ID_Expediente, Resolucion, N_Contrato, Fecha_Emision, ID_Categoria, Fecha_Vencimiento, Codigo_Censo, Observaciones, ID_Estado, Permiso_Anterior, Codigo_Aduanero, Tipo_Generacion, URL_Constancia_Transportista, Sistema_Usuario, Sistema_Fecha ) SELECT p.N_Permiso_Explotacion, p.N_Permiso_Explotacion_Encrypted, p.RTN_Concesionario, cp.ID_Representante_Legal, p.ID_Colegiacion, p.ID_Expediente, p.Resolucion, p.N_Contrato, p.Fecha_Emision, p.ID_Categoria, p.Fecha_Vencimiento, p.Codigo_Censo, p.Observaciones, p.ID_Estado, p.Permiso_Anterior, p.Codigo_Aduanero, p.Tipo_Generacion, p.URL_Constancia_Transportista, p.Sistema_Usuario, p.Sistema_Fecha FROM [IHTT_SGCERP].[dbo].[TB_Permiso_Explotacion_Carga] AS p LEFT OUTER JOIN [IHTT_SGCERP].[dbo].[TB_Contactos_Permisos_Explotacion] AS cp ON cp.N_Permiso_Explotacion = p.N_Permiso_Explotacion WHERE p.N_Permiso_Explotacion = :Concesion;";
        } else {
            //***********************************************************************/
            //* Si el proceso es de Permiso Especial de Pasajeros
            //***********************************************************************/

            $query = "INSERT INTO [IHTT_SGCERP].[dbo].[TB_Historico_Permiso_Explotacion_Pasajeros] ( N_Permiso_Explotacion, N_Permiso_Explotacion_Encrypted, RTN_Concesionario, ID_Representante_Legal, ID_Colegiacion, Resolucion, N_Contrato, ID_Expediente, Fecha_Emision, ID_Categoria, Fecha_Vencimiento, ID_Ruta, Cod_Ruta, DESC_Ruta, Ruta_Intermedia, Cod_Horario, HoraInicio, HoraFinal, Cod_Frecuencia_Salida, Frecuencia, Cod_Tarifa, Tarifa, Codigo_Censo, Observaciones, ID_Estado, Permiso_Anterior, Sistema_Usuario, Sistema_Fecha ) SELECT p.N_Permiso_Explotacion, p.N_Permiso_Explotacion_Encrypted, p.RTN_Concesionario, cp.ID_Representante_Legal, p.ID_Colegiacion, p.Resolucion, p.N_Contrato, p.ID_Expediente, p.Fecha_Emision, p.ID_Categoria, p.Fecha_Vencimiento, p.ID_Ruta, r.Cod_Ruta, dr.Descripcion, dr.RutaIntermedia, r.Cod_Horario, rh.HoraInicio, rh.HoraFinal, r.Cod_Frecuencia_Salida, rf.Frecuencia, r.Cod_Tarifa, rt.Tarifa, p.Codigo_Censo, p.Observaciones, p.ID_Estado, p.Permiso_Anterior, p.Sistema_Usuario, p.Sistema_Fecha FROM [IHTT_SGCERP].[dbo].[TB_Permiso_Explotacion_Pas] AS p LEFT OUTER JOIN [IHTT_SGCERP].[dbo].[TB_Rutas] AS r ON r.ID_Ruta = p.ID_Ruta LEFT OUTER JOIN [IHTT_SIPET].[dbo].[v_rutas_certificado] AS dr ON dr.CodigoRutaDescriptivo = r.Cod_Ruta LEFT OUTER JOIN [IHTT_SIPET].[dbo].[TB_Ruta_Horario] AS rh ON rh.CodigoHorario = r.Cod_Horario LEFT OUTER JOIN [IHTT_SIPET].[dbo].[TB_Ruta_Frecuencia] AS rf ON rf.CodigoFrecuencia = r.Cod_Frecuencia_Salida LEFT OUTER JOIN [IHTT_SIPET].[dbo].[TB_Ruta_Tarifa] AS rt ON rt.CodigoTarifa = r.Cod_Tarifa LEFT OUTER JOIN [IHTT_SGCERP].[dbo].[TB_Contactos_Permisos_Explotacion] AS cp ON cp.N_Permiso_Explotacion = p.N_Permiso_Explotacion WHERE p.N_Permiso_Explotacion = :Concesion;";
        }
        try {
            return $this->insert($query,Array(':Concesion' => $rs_id_rs_concesion));
        } catch (\Throwable $th) {
            $txt = date('Y m d h:i:s') . '	Usuario:' . $_SESSION['user_name'] . 'insertPerExpHistorico() catch '. $query . ' ERROR ' . $th->getMessage();
            logErr($txt,'../logs/logs.txt');
            return false;
        }	
    }
    //**********************************************************************************************/
    //* FINAL: INSERTAR EN HISTORICO DE PERMISOS DE EXPLOTACIÓN
    //**********************************************************************************************/
    function updatePermisoExplotacion ( $tipo_transporte, $id_concesion, $id_expediente, $Fecha_Vencimiento, $id_resolucion, $id_apoderado, $id_comisionado) {
        $respuesta[0]['msg'] = "";
        $respuesta[0]['error'] = false;	
        $respuesta[0]['errorcode'] = '';
        try {
            //***********************************************************************/
            //*Si el proceso es de Transporte de Carca
            //***********************************************************************/
            if ($tipo_transporte == 'CARGA') {
                $query = "UPDATE [IHTT_SGCERP].[dbo].[TB_Permiso_Explotacion_Carga] set 
                ID_Expediente=:ID_Expediente, Fecha_Vencimiento=CONVERT(DATE,:Fecha_Vencimiento, 111), 
                ID_Colegiacion=:ID_Colegiacion, Resolucion=:Resolucion, Sistema_Usuario=:Sistema_Usuario, 
                ID_Estado='ES-02', Observaciones='RENOVACIONES AUTOMATICAS, GENERADO POR PROGRAMA', 
                Comisionado_Gestion=:Comisionado_Gestion,
                Fecha_Emision=SYSDATETIME(), Sistema_Fecha=SYSDATETIME() 
                where N_Permiso_Explotacion=:Concesion;";
                $p =  Array(':ID_Expediente' => $id_expediente,':Fecha_Vencimiento' => $Fecha_Vencimiento,
                                            ':ID_Colegiacion' => $id_apoderado,':Resolucion' => $id_resolucion,
                                            ':Sistema_Usuario' => $_SESSION['user_name'],':Comisionado_Gestion' => $id_comisionado,
                                            ':Concesion' => $id_concesion);
            //***********************************************************************/
            //Else de Si el proceso es de Transporte de Pasajeros
            //***********************************************************************/
            } else {
                //***********************************************************************/
                //Si el proceso es de Certificado de Pasajeros
                //***********************************************************************/
                $query = "UPDATE [IHTT_SGCERP].[dbo].[TB_Permiso_Explotacion_Pas] set 
                ID_Expediente=:ID_Expediente,
                Fecha_Vencimiento=CONVERT(DATE,:Fecha_Vencimiento, 111), 
                ID_Colegiacion=:ID_Colegiacion,
                Resolucion=:Resolucion,
                Sistema_Usuario=:Sistema_Usuario,
                Comisionado_Gestion=:Comisionado_Gestion, 
                Fecha_Emision=SYSDATETIME(),
                Sistema_Fecha=SYSDATETIME(),
                ID_Estado='ES-02',
                Observaciones='RENOVACIONES AUTOMATICAS, GENERADO POR PROGRAMA'
                where N_Permiso_Explotacion=:Concesion;";
                $p = Array(':ID_Expediente' => $id_expediente, ':Fecha_Vencimiento' => $Fecha_Vencimiento,
                                            ':ID_Colegiacion' => $id_apoderado, ':Resolucion' => $id_resolucion,
                                            ':Sistema_Usuario' => $_SESSION['user_name'], ':Comisionado_Gestion' => $id_comisionado,
                                            ':Concesion' => $id_concesion);
            }
            return $this->update($query,$p);
        } catch (\Throwable $th) {
            $txt = date('Y m d h:i:s') . '	Usuario:' . $_SESSION['user_name'] .'updatePermisoExplotacion() catch '. $query . ' ERROR ' . $th->getMessage();
            logErr($txt,'../logs/logs.txt');
            return $respuesta;
        }	
    }    
    //***********************************************************************************************
	//* FINAL: ACTUALIZAR PERMISO DE EXPLOTACIÓN
	//***********************************************************************************************
    //***********************************************************************************************
	//* INICIO: Funcion para procesar todas las actualizaciones correspondientes en SGCERP
	//***********************************************************************************************
  
    protected function fProcesarConcesionesEnSGCERP($es_renovacion_explotacion,$es_renovacion_certificado,$es_renovacion_permisoespecial,$cambio_placa,$cambio_en_unidad,$nueva_unidad,$id_placa,$id_placa_antes_replaqueo,$es_renovacion_tipo_transporte,$tipo_concesion,$vehiculoentra,$vehiculoactual,$Data,$tramitepeticion,$ID_Resolucion,$ID_ColegiacionAPL,$es_replaqueo,$detallevehiculoentra){
        if ($detallevehiculoentra == '') {
            unset($detallevehiculoentra);
        }
        //***********************************************************************************************
        //*Si es cambio de unidad o cambio en la unidad(Motor, Color, Chasis o VIN)
        //***********************************************************************************************
        if ($nueva_unidad == true or $cambio_en_unidad == true) {
            $respuestagetVehiculoxPlaca = $this->getVehiculoxPlaca ($id_placa,$id_placa_antes_replaqueo,$es_renovacion_tipo_transporte);
            if (isset($respuestagetVehiculoxPlaca['ID_Vehiculo']) == true) {
                if (isset($detallevehiculoentra) == false or
                    $respuestagetVehiculoxPlaca['ID_Marca'] != $vehiculoentra['ID_Marca'] ||
                    $respuestagetVehiculoxPlaca['Anio']  != $vehiculoentra['Anio'] ||
                    $respuestagetVehiculoxPlaca['ID_Color'] != $vehiculoentra['ID_Color'] ||
                    strtoupper($respuestagetVehiculoxPlaca['Motor'])  != strtoupper($vehiculoentra['Motor']) ||
                    strtoupper($respuestagetVehiculoxPlaca['Chasis']) != strtoupper($vehiculoentra['Chasis']) ||
                    strtoupper($respuestagetVehiculoxPlaca['VIN'])   != strtoupper($vehiculoentra['VIN'])){
                    $respuestagetCertificadoVehiculo = $this->getCertificadoVehiculo ($Data[0]['Certificado_Operacion'],$es_renovacion_tipo_transporte,$tipo_concesion);
                    if (isset($respuestagetCertificadoVehiculo['ID_Vehiculo'])) {                       
                        $record = $respuestagetCertificadoVehiculo;
                        if (isset($detallevehiculoentra)) {
                            $respuestasaveVehiculo['ID_Vehiculo'] = $this->insertVehiculo($respuestagetCertificadoVehiculo,$vehiculoentra,$es_renovacion_tipo_transporte);
                        } else {
                            $respuestasaveVehiculo['ID_Vehiculo'] = $this->insertVehiculo($respuestagetCertificadoVehiculo,$vehiculoactual,$es_renovacion_tipo_transporte);
                        }
                        if ($respuestasaveVehiculo['ID_Vehiculo']==false) {
                            $txt = date('Y m d h:i:s') . ' 9078-Usuario:' . $_SESSION['user_name'] .'9000-if (isset($detallevehiculoentra) == false y luego if (!isset($respuestasaveVehiculo[ID_Vehiculo]))';
                            logErr($txt,'../logs/logs.txt');
                            return false;                                                        
                        }
                    } else {
                        $txt = date('Y m d h:i:s') . '	' .'9001-if (isset($detallevehiculoentra) == false y luego if (isset($respuestagetCertificadoVehiculo[ID_Color])) {  Error de Datos';
                        logErr($txt,'../logs/logs.txt');
                        return false;                                                        
                    }
                } else {
                    $respuestagetCertificadoVehiculo = $this->getCertificadoVehiculo ($Data[0]['Certificado_Operacion'],$es_renovacion_tipo_transporte,$tipo_concesion);
                    if (isset($respuestagetCertificadoVehiculo['ID_Vehiculo'])) {                       
                        $recordXPlaca = $respuestagetVehiculoxPlaca;
                        $record = $respuestagetCertificadoVehiculo;
                        //******************************************************************************************/
                        //* Si el vehiculo se encontro por la placa vieja antes del replaqueo, quiere decir que no
                        //* existe la placa nueva registrada en el sistema para el vehiculo por ende
                        //* se registra la nueva placa asociada al vehiculo encontrado 
                        //******************************************************************************************/
                        if ($id_placa_antes_replaqueo == strtoupper($recordXPlaca['ID_Placa'])) {
                            $respuestasavePlacaxVehiculo = $this->savePlacaxVehiculo($recordXPlaca['ID_Vehiculo'],$id_placa,$id_placa_antes_replaqueo,$es_renovacion_tipo_transporte);
                            if ($respuestasavePlacaxVehiculo == false) {
                                $txt = date('Y m d h:i:s') . '	Usuario:' . $_SESSION['user_name'] .'9002-if (isset($detallevehiculoentra) == false and if ($respuestasavePlacaxVehiculo == false) {';
                                logErr($txt,'../logs/logs.txt');
                                return false;                                                        
                            }
                        } else {
                            //***************************************************************************************************/
                            //* Sino y el vehiculo esta ya registrado correctamente con la placa nueva que se ingreso
                            //* se proceder asignar el mismo id de vehiculo y se actuakiza a ACTIVO el estado del registro de palca
                            //***************************************************************************************************/
                            $respuestaupdateVehiculoxPlaca = $this->updateVehiculoxPlaca ( $recordXPlaca['ID_Placa'],'ACTIVA',$es_renovacion_tipo_transporte);
                            if ($respuestaupdateVehiculoxPlaca == false) {
                                $txt = date('Y m d h:i:s') . '	Usuario:' . $_SESSION['user_name'] .' 9003-$respuestaupdateVehiculoxPlaca = $this->updateVehiculoxPlaca if ($respuestaupdateVehiculoxPlaca == false) {';
                                logErr($txt,'../logs/logs.txt');
                                return false;                                                    
                            }
                        }
                    } else {
                        $txt = date('Y m d h:i:s') . '	' .'9004-else if (isset($detallevehiculoentra) == false if (isset($respuestagetCertificadoVehiculo[ID_Color])) { Error de Datos';
                        logErr($txt,'../logs/logs.txt');
                        return false;                                                        
                    }
                }
            } else {
                $respuestagetCertificadoVehiculo = $this->getCertificadoVehiculo ($Data[0]['Certificado_Operacion'],$es_renovacion_tipo_transporte,$tipo_concesion);
                if (isset($respuestagetCertificadoVehiculo['ID_Color'])) {                       
                    $record = $respuestagetCertificadoVehiculo;
                    if (isset($detallevehiculoentra)) {
                        $respuestasaveVehiculo['ID_Vehiculo'] = $this->insertVehiculo($respuestagetCertificadoVehiculo,$vehiculoentra,$es_renovacion_tipo_transporte);
                    } else {
                        $respuestasaveVehiculo['ID_Vehiculo'] = $this->insertVehiculo($respuestagetCertificadoVehiculo,$vehiculoactual,$es_renovacion_tipo_transporte);
                    }
                    if ($respuestasaveVehiculo['ID_Vehiculo']==false) {
                        $txt = date('Y m d h:i:s') . '	Usuario:' . $_SESSION['user_name'] .' 9016-else if (isset($respuestagetVehiculoxPlaca[ID_Vehiculo]) == true) { if ($respuestasaveVehiculo==false) { error de data';
                        logErr($txt,'../logs/logs.txt');
                        return false;                                                    
                    }
                } else {
                    $txt = date('Y m d h:i:s') . '	' .' 9007-else if (isset($detallevehiculoentra) == false if (isset($respuestagetCertificadoVehiculo[ID_Color])) { Error de Datos';
                    logErr($txt,'../logs/logs.txt');
                    return false;                                                        
                }
            }
        } else {
            $respuestagetCertificadoVehiculo = $this->getCertificadoVehiculo ($Data[0]['Certificado_Operacion'],$es_renovacion_tipo_transporte,$tipo_concesion);
            if ($cambio_placa == true or $id_placa != $respuestagetCertificadoVehiculo['ID_Placa']) {
                if (isset($respuestagetCertificadoVehiculo['ID_Placa']) == true) {      
                    $record = $respuestagetCertificadoVehiculo;     
                    if ($record['ID_Placa'] != $id_placa) {
                        $respuestasavePlacaxVehiculo = $this->savePlacaxVehiculo ($record['ID_Vehiculo'],$id_placa,$record['ID_Placa'],$es_renovacion_tipo_transporte);
                        if ($respuestasavePlacaxVehiculo == false) {
                            $txt = date('Y m d h:i:s') . '	Usuario:' . $_SESSION['user_name'] .' 9010-else if ($nueva_unidad == true or $cambio_en_unidad == true) { and if ($respuestasavePlacaxVehiculo == false) { error de data';
                            logErr($txt,'../logs/logs.txt');
                            return false;                                                    
                        }
                    } else {
                        $txt = date('Y m d h:i:s') . '	Usuario:' . $_SESSION['user_name'] .' 9050-else if ($nueva_unidad == true or $cambio_en_unidad == true) { and  else if ($respuestasavePlacaxVehiculo == false) { error de data';
                        logErr($txt,'../logs/logs.txt');
                        return false;                                                    
                    }
                } else {
                    $txt = date('Y m d h:i:s') . '	' .' 9011-else if ($nueva_unidad == true or $cambio_en_unidad == true) { and if ($respuestagetCertificadoVehiculo != false) { Error de Datos';
                    logErr($txt,'../logs/logs.txt');
                    return false;                                                        
                }
            } else {
                if ($respuestagetCertificadoVehiculo == false) {
                    $txt = date('Y m d h:i:s') . '	' .' 9012-else if ($nueva_unidad == true or $cambio_en_unidad == true) { and  if ($cambio_placa == true or $id_placa != $respuestagetCertificadoVehiculo[ID_Placa]) { Error de Datos';
                    logErr($txt,'../logs/logs.txt');
                    return false;                                                        
                } else {
                    $record = $respuestagetCertificadoVehiculo;
                }
            }
        }
        $respuestainsertCertificadoHistorico = $this->insertCertificadoHistorico ($Data[0]['Certificado_Operacion'],$es_renovacion_tipo_transporte,$tipo_concesion);
        if ($respuestainsertCertificadoHistorico == false) {
            $txt = date('Y m d h:i:s') . '	' . ' 9013-respuestainsertCertificadoHistorico  Certificado_Operacion ' . $Data[0]['Certificado_Operacion'] . ' error de data';
            logErr($txt,'../logs/logs.txt');
            return false;                                                        
        }
        $salvar_historico_vehiculo = false;
        if (isset($respuestasaveVehiculo['ID_Vehiculo'])) {
            $id_vehiculo = $respuestasaveVehiculo['ID_Vehiculo'];
            $salvar_historico_vehiculo = true;
        } else {
            if (isset($recordXPlaca['ID_Vehiculo'])) {
                $id_vehiculo = $recordXPlaca['ID_Vehiculo'];
                $salvar_historico_vehiculo = true;
            } else {
                $id_vehiculo = $record['ID_Vehiculo'];
            }
        }
        if ($es_renovacion_certificado == true || $es_renovacion_permisoespecial == true) {
            $Nueva_Fecha_Expiracion = date('Y-m-d',strtotime($record["Fecha_Expiracion"]));
            $hoyplus60 = date('Y-m-d', strtotime('+60 days'));
            while ($Nueva_Fecha_Expiracion <= $hoyplus60) {
                if ($tipo_concesion == 'CER') {
                    $Nueva_Fecha_Expiracion = date("Y-m-d",strtotime($Nueva_Fecha_Expiracion."+ 3 years"));
                } else {
                    $Nueva_Fecha_Expiracion = date("Y-m-d",strtotime($Nueva_Fecha_Expiracion."+ 1 year"));
                }
            }
        } else {
            $Nueva_Fecha_Expiracion = date('Y-m-d',strtotime($record["Fecha_Expiracion"]));
        }
        $respuestaupdateCertificado = $this->updateCertificado ( $es_renovacion_tipo_transporte, $tipo_concesion, $Data[0]['Certificado_Operacion'],
            $id_vehiculo, $Data[0]['ID_Expediente'], $Nueva_Fecha_Expiracion, $ID_Resolucion, $ID_ColegiacionAPL, 'rbarahona');
        if ($respuestaupdateCertificado == false) {
            $txt = date('Y m d h:i:s') . '	' . ' 9014-if ($respuestaupdateCertificado == false) { ' . $Data[0]['Certificado_Operacion'] . ' error de data';
            logErr($txt,'../logs/logs.txt');
            return false;                                                        
        }
        $respuestainsertBitacoraCambioTramites = $this->insertBitacoraCambioTramites ( substr($tramitepeticion,0,500), substr($tramitepeticion,0,200), $Data[0]['Certificado_Operacion'], $Data[0]['ID_Expediente'], $Data[0]['ID_Clase_Servico'], $Data[0]['Tipo_Documento'], $es_replaqueo);
        if ($respuestainsertBitacoraCambioTramites == false) {
            $txt = date('Y m d h:i:s') . '	' . ' if ($respuestainsertBitacoraCambioTramites == false) { ' . $Data[0]['Certificado_Operacion'] . ' error de data';
            logErr($txt,'../logs/logs.txt');
            return false;                                                        
        }
        if ($salvar_historico_vehiculo == true){
            $respuestainsertVehiculoHistorico = $this->insertVehiculoHistorico ($record['ID_Vehiculo'],$es_renovacion_tipo_transporte);
            if ($respuestainsertVehiculoHistorico == false) {
                $txt = date('Y m d h:i:s') . '	' . ' 9015-if ($respuestainsertVehiculoHistorico == false) { ' . $Data[0]['Certificado_Operacion'] . ' error de data';
                logErr($txt,'../logs/logs.txt');
                return false;                                                        
            }
        }
        $llave_publica =  $Data[0]['SOL_MD5'];
        $respuestasaveBitacoraFirma = $this->saveBitacoraFirma( $Data[0]['Certificado_Operacion'],$Data[0]['Tipo_Documento'],$_SESSION["user_name"],$llave_publica);	                            
        if ($respuestasaveBitacoraFirma == false) {
            $txt = date('Y m d h:i:s') . '	' . ' 9016-if ($respuestasaveBitacoraFirma == false) { ' . $Data[0]['Certificado_Operacion'] . ' error de data';
            logErr($txt,'../logs/logs.txt');
            return false;                                                        
        }
        if ($es_renovacion_explotacion == true){
            $respuestainsertPerExpHistorico = $this->insertPerExpHistorico ($Data[0]['Permiso_Explotacion'],$es_renovacion_tipo_transporte);
            if ($respuestainsertPerExpHistorico ==false) {
                $txt = date('Y m d h:i:s') . '	' . ' 9017-if ($respuestainsertPerExpHistorico ==false) { ' . $Data[0]['Certificado_Operacion'] . ' error de data';
                logErr($txt,'../logs/logs.txt');
                return false;                                                        
            }
            $Nueva_Fecha_Expiracion = date('Y-m-d',strtotime($record["Fecha_Expiracion_Explotacion"]));
            $hoyplus60 = date('Y-m-d', strtotime('+60 days'));
            while ($Nueva_Fecha_Expiracion <= $hoyplus60) {
                $Nueva_Fecha_Expiracion = date("Y-m-d",strtotime($Nueva_Fecha_Expiracion."+ 12 years"));
            }
            $respuestaupdatePermisoExplotacion = $this->updatePermisoExplotacion ( $es_renovacion_tipo_transporte, $Data[0]['Permiso_Explotacion'],  $Data[0]['ID_Expediente'], $Nueva_Fecha_Expiracion, $ID_Resolucion, $ID_ColegiacionAPL, 'rbarahona');
            if ($respuestaupdatePermisoExplotacion == false) {
                $txt = date('Y m d h:i:s') . '	' . ' 9018-if ($respuestaupdatePermisoExplotacion == false) { ' . $Data[0]['Certificado_Operacion'] . ' error de data';
                logErr($txt,'../logs/logs.txt');
                return false;                                                        
            }
            $respuestainsertBitacoraCambioTramites = $this->insertBitacoraCambioTramites (substr($tramitepeticion,0,500), substr($tramitepeticion,0,200), $Data[0]['Permiso_Explotacion'], $Data[0]['ID_Expediente'], $Data[0]['ID_Clase_Servico'], $Data[0]['Tipo_Documento'], $es_replaqueo);
            if ($respuestainsertBitacoraCambioTramites == false) {
                $txt = date('Y m d h:i:s') . '	' . ' 9019-if ($respuestainsertBitacoraCambioTramites == false) { ' . $Data[0]['Certificado_Operacion'] . ' error de data';
                logErr($txt,'../logs/logs.txt');
                return false;                                                        
            }
            $llave_publica = hash('SHA512','/I(h$T@t%&)' . 'PERMISO EXPLOTACIÓN' . $Data[0]['ID_Solicitud'] .date("Y/m/d h:i:sa"),false);
            $respuestasaveBitacoraFirma = $this->saveBitacoraFirma($Data[0]['Permiso_Explotacion'],'PERMISO EXPLOTACIÓN',$_SESSION["user_name"],$llave_publica);	                            
            if ($respuestasaveBitacoraFirma == false) {
                $txt = date('Y m d h:i:s') . '	' . ' 9020-if ($respuestasaveBitacoraFirma == false) { ' . $Data[0]['Certificado_Operacion'] . ' error de data';
                logErr($txt,'../logs/logs.txt');
                return false;                                                        
            }
        }          
        if ($es_renovacion_explotacion == true) {
            if ($es_renovacion_tipo_transporte == 'CARGA') {
                $rutapermisoexplotacion = $this->dominio_raiz  . ":172/PDFPermisoEspecialCarga_Mixto.php?modo=visualizacion&PermisoEspecial=".$record['Permiso_Explotacion_Encrypted'];
            } else {
                $rutapermisoexplotacion = $this->dominio_raiz  . ":172/api_rep.php?action=get-PDFPermisoExp-Pas&Permiso=".$record['Permiso_Explotacion_Encrypted'];
            }
            $respuestaretornar['errorexplotacion']=false;
            $respuestaretornar['msgexplotacion']='<strong>  IMPRIMIR PERMISO DE EXPLOTACIÓN NO.- ' . $record['Permiso_Explotacion'] . '</strong>';
            $respuestaretornar['botonexplotacion']='<a style="background-color: #F663BE; border-radius: 15px; border: solid 4px #09057B;" href="'.$rutapermisoexplotacion.'" target="_blank"  class="waves-effect waves-green btn-flat btn"><i class="material-icons print"></i>'.$respuestaretornar['msgexplotacion'].'</a>';
            $respuestaretornar['rutaexplotacion']=$rutapermisoexplotacion;
            $respuestaretornar['id_explotacion']=$record['Permiso_Explotacion'];
        }
        if ($tipo_concesion == 'CER') {
            if ($nueva_unidad == true or $cambio_en_unidad == true or $es_renovacion_certificado == true or $cambio_placa == true) {
                if ($es_renovacion_tipo_transporte == 'CARGA') {
                    $rutacertificado = $this->dominio_raiz  . ":172/PDFCertificadoCarga_Mixto.php?modo=visualizacion&Certificado=@@__CONCESIONES__@@";
                } else {
                    $rutacertificado = $this->dominio_raiz  . ":172/PDFCertificadoPasajeros_Mixto.php?modo=visualizacion&Certificado=@@__CONCESIONES__@@";
                }
                $respuestaretornar['errorcertificado']=false;
                $respuestaretornar['msgcertificado']='<strong>  IMPRIMIR BORRADOR DE CERTIFICADO(s)</strong>';
                $respuestaretornar['botoncertificado']='<a style="background-color: #4BEE56; border-radius: 15px; border: solid 4px #0E0E0E;" href="'.$rutacertificado.'" target="_blank"  class="waves-effect waves-green btn-flat btn"><i class="material-icons print"></i>'.$respuestaretornar['msgcertificado'].'</a>';
                $respuestaretornar['rutacertificado']=$rutacertificado;
                $respuestaretornar['id_certificado']=$record['Concesion'];
            }
        } else {
            if ($nueva_unidad == true or $cambio_en_unidad == true or $es_renovacion_permisoespecial == true or $cambio_placa == true) {
                if ($es_renovacion_tipo_transporte == 'CARGA') {
                    $rutacertificado = $this->dominio_raiz  . ":172/PDFPermisoEspecialCarga_Mixto.php?modo=visualizacion&PermisoEspecial=@@__CONCESIONES__@@";
                } else {
                    $rutacertificado = $this->dominio_raiz  .":172/PDFPermisoEspecialPasajero_Mixto.php?modo=visualizacion&PermisoEspecial=@@__CONCESIONES__@@";
                }
                $respuestaretornar['errorcertificado']=false;
                $respuestaretornar['msgcertificado']='<strong>  IMPRIMIR PERMISO(S) ESPECIAL(ES)</strong>';
                $respuestaretornar['botoncertificado']='<a style="background-color: #4BEE56; border-radius: 15px; border: solid 4px #0E0E0E;" href="'.$rutacertificado.'" target="_blank"  class="waves-effect waves-green btn-flat btn"><i class="material-icons print"></i>'.$respuestaretornar['msgcertificado'].'</a>';
                $respuestaretornar['id_certificado']=$rutacertificado;
                $respuestaretornar['id_especial']=$record['Concesion'];
            }
        }
        return $respuestaretornar;
    }
    //***********************************************************************************************
	//* INICIO: Funcion para procesar todas las actualizaciones correspondientes en SGCERP
	//***********************************************************************************************
    //***********************************************************************************************
	//* INICIO: Funcion principal de Generacion de Resolucion
	//***********************************************************************************************
    function PDFResolucionApi($rs_id_rs_expediente,$ID_Resolucion,$rs_id_rs_template=3,$cfg_institucion='INSTITUTO HONDUREÑO DEL TRANSPORTE TERRESTRE'){
        ini_set("default_charset", 'utf-8');
        if (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        // Bandera de Tramites de Renovación
        $Es_Renovacion = false;
        // Bandera de Si la funcion presento un error
        $Error_Proceso = false;
        // Monto total a pagar por los tramites incluidos en ese expediente
        $monto_total=0;
        // Cambio de caractetisticas o nueva unidad
        $Es_Cambio = false;	
        // Variable para almacenar el contenido del pdf que generara este programa
        $pagina_inicio = '';
        $tramitepeticion = '';
        $tramite = '';
        // msg variable para almacenar mensaje de error
        $msg = '';
        // Funcion que recupera los datos para insertar en el template
        $CONCESIONES =  $this-> getSolicitudResolucionConcesiones($rs_id_rs_expediente);
        $tramite = ''; // Initialize the variable to avoid undefined variable error
        $contador=0;
        $concesionesnumeros='';
        //* INICIO: CICLO QUE PROCESA TODAS LAS CONCESIONES REGISTRADAS EN EL EXPEDIENTE
        foreach ($CONCESIONES as $CONCESION){
            $ConcesionesEncryptada[] = $CONCESION['SOL_MD5'];
            $ConcesionesNumero[] = "'" . $CONCESION['Concesion'] . "'";
            // Existe un tramite para creacion de una nueva unidad
            $nueva_unidad = false;
            // Existe un tramite para cambio de placa
            $cambio_placa = false;
            // Existe un tramite para cambio en la unidad (que no es cambio de unidad y placa)
            $cambio_en_unidad = false;
            // Es Renovacion de Permiso Explotación
            $es_renovacion_explotacion = false;
            // Es Renovacion de Certificado
            $es_renovacion_certificado = false;
            // Es Renovacion de Permiso Especial
            $es_renovacion_permisoespecial = false;    
            // Tipo de concesion
            $tipo_concesion = '';
            // Es Renovacion de Tipo de Transporte
            $es_renovacion_tipo_transporte = '';        
            // Funcion que recupera los datos para insertar en el template
            $row_rs_todos_los_registros =  $this->getSolicitudResolucion($rs_id_rs_expediente,$CONCESION['Concesion'],$rs_id_rs_template);
            // INICIO: Recuperando Vehiculos del tramite
            $vehiculoactual = $this->getVehiculosExpediente($rs_id_rs_expediente,$CONCESION['Concesion'],$tabla="[IHTT_DB].[dbo].[TB_Solicitud_Vehiculo_Actual] e"); 
            $vehiculoentra = $this->getVehiculosExpediente($rs_id_rs_expediente,$CONCESION['Concesion'],$tabla="[IHTT_DB].[dbo].[TB_Solicitud_Vehiculo_Entra] e"); 
            $detallevehiculoactual = " MARCA: " . $vehiculoactual['DESC_Marca'] . ", MODELO: " . $vehiculoactual['Modelo'] . ", COLOR: " . $vehiculoactual['DESC_Color']. ", MOTOR: " . $vehiculoactual['Motor']. ", CHASIS: " . $vehiculoactual['Chasis'] . " Y PLACA: " . $vehiculoactual['ID_Placa'];
            $id_placa = $vehiculoactual['ID_Placa'];
            $id_placa_antes_replaqueo = 'XXXXXXXXXX';
            $detallevehiculoentra='';
            if (isset($vehiculoentra['DESC_Marca'])) {
                $detallevehiculoentra = " MARCA: " . $vehiculoentra['DESC_Marca'] . ", MODELO: " . $vehiculoentra['Modelo'] . ", COLOR: " . $vehiculoentra['DESC_Color']. ", MOTOR: " . $vehiculoentra['Motor']. ", CHASIS: " . $vehiculoentra['Chasis'] . " Y PLACA: " . $vehiculoentra['ID_Placa'];
                $id_placa = $vehiculoentra['ID_Placa'];
                $id_placa_antes_replaqueo = $vehiculoentra['ID_Placa_Antes_Replaqueo'];
                if ($id_placa_antes_replaqueo == '' or $id_placa_antes_replaqueo == null) {
                    $id_placa_antes_replaqueo = 'XXXXXXXXXX';
                }
            }
            // FINAL: Recuperando Vehiculos del tramite
            // contador de registros procesados
            // total de registro recuperados por el query de getSolicitudResolucion($dbsol,$rs_id_rs_expediente,$rs_id_rs_template)
            $total_registros = count($row_rs_todos_los_registros);
            $contador_tramites=0;
            $es_renovacion_tipo_transporte = '';        
            $tipo_concesion = '';            
            $es_renovacion_explotacion = false;
            $es_renovacion_certificado = false;
            $es_renovacion_permisoespecial = false;
            $es_replaqueo = 'NO';
            $cambio_placa = false;
            $cambio_en_unidad = false;
            $nueva_unidad = false;                                                        
            // for para iterar en los registro recuperados
            foreach ($row_rs_todos_los_registros as $row_rs_expediente){
                // Si se recuperaron datos del expediente procesa
                if ($row_rs_expediente['ID_Solicitud'] != ''){
                    $preforma = $row_rs_expediente['Preforma'];
                    // Recuperando la tarifa del tramite
                    $row_rs_Tarifa = $this->getTarifa($row_rs_expediente['ID_Tramite']);
                    // Si encontro la tarifa del tramite prosiga
                    If (isset($row_rs_Tarifa['Monto'])) {
                        // Hacer esto solo cuando es la primera linea
                        if ($contador == 0) {
                            $llave_publica = $row_rs_expediente['SOL_MD5'];
                            // ***********************************************************************************************
                            // Comenzando transaccion a ninivel de base de datos
                            // ***********************************************************************************************
                            //$dbsol->beginTransaction();
                            // ***********************************************************************************************
                            // Inicio
                            // ***********************************************************************************************
                            $rtn_solicitante = $row_rs_expediente['RTN_Solicitante'];
                            $ID_ColegiacionAPL = $row_rs_expediente['ID_ColegiacionAPL'];
                            $NombreApoderadoLega = $row_rs_expediente['NombreApoderadoLega'];
                            // ***********************************************************************************************
                            // Dependiendo si es Permiso Especial o Permiso de explotación se pone el campo en la variable
                            // ***********************************************************************************************
                            If ($row_rs_expediente['N_Permiso_Especial'] != '') {
                                $permiso_ac = $row_rs_expediente['N_Permiso_Especial'];
                                $Data[0]['Tipo_Documento'] = 'PERMISO ESPECIAL';
                            } else {
                                $permiso_ac = $row_rs_expediente['Permiso_Explotacion'];
                                $Data[0]['Tipo_Documento'] = 'CERTIFICADO DE OPERACIÓN';
                            }
                            // ***********************************************************************************************
                            // Final
                            // ***********************************************************************************************
                            // Inicio
                            // ***********************************************************************************************
                            // Armando datos para generar aviso de cobro
                            // ***********************************************************************************************
                            $es_replaqueo = 'NO';
                            $Data[0]['Modulo'] = 15;
                            $Data[0]['Usuario'] = 'avisosra';
                            $Data[0]['Clave'] = 'IhTt@2o23%';
                            $Data[0]['IPUsuario'] = $ip;
                            $Data[0]['Preforma'] = $preforma;
                            $Data[0]['SOL_MD5'] = $row_rs_expediente['SOL_MD5'];
                            $Data[0]['Email'] = $row_rs_expediente['Email_Solicitante'];
                            $Data[0]['Email_Apoderado'] = $row_rs_expediente['Email_Apoderado_Legal'];
                            $Data[0]['RTN_Solicitante'] = $row_rs_expediente['RTN_Solicitante'];
                            $Data[0]['CodigoAvisoCobro'] = $row_rs_expediente['CodigoAvisoCobro'];
                            $Data[0]['ID_Solicitud'] = $row_rs_expediente['ID_Solicitud'];
                            $Data[0]['ID_Expediente'] = $row_rs_expediente['ID_Expediente'];
                            $Data[0]['Preforma'] = $row_rs_expediente['Preforma'];
                            $Data[0]['NombreSolicitante'] = $row_rs_expediente['NombreSolicitante'];
                            $Data[0]['NombreApoderadoLega'] = $row_rs_expediente['NombreApoderadoLega'];
                            $Data[0]['ID_Placa'] = $id_placa;
                            $Data[0]['usuario'] = $_SESSION["user_name"];//$_SESSION['usuario'];
                            $Data[0]['Observacion'] = 'RENOVACIONES AUTOMATICAS';
                            $Data[0]['Permiso_Explotacion'] = $permiso_ac;
                            $Data[0]['ID_Clase_Servico'] = $row_rs_expediente['ID_Clase_Servico'];
                            $Data[0]['Tramites'][$contador]['ID_tramite'] = $row_rs_expediente['ID_Tramite'];
                            If ($row_rs_expediente['N_Permiso_Especial'] != '') {
                                $Data[0]['Tramites'][$contador]['Certificado_Operacion'] = $row_rs_expediente['N_Permiso_Especial'];
                                $Data[0]['Certificado_Operacion'] = $row_rs_expediente['N_Permiso_Especial'];
                                $Data[0]['Tipo_Documento'] = 'PERMISO ESPECIAL';
                            } else {
                                $Data[0]['Tramites'][$contador]['Certificado_Operacion'] = $row_rs_expediente['Certificado_Operacion'];
                                $Data[0]['Certificado_Operacion'] = $row_rs_expediente['Certificado_Operacion'];
                                $Data[0]['Tipo_Documento'] = 'CERTIFICADO DE OPERACIÓN';
                            }
                            $Data[0]['Tramites'][$contador]['ID_Placa'] = $id_placa;
                            $Data[0]['Tramites'][$contador]['Monto'] = $row_rs_Tarifa['Monto'];
                            $monto_total = $row_rs_Tarifa['Monto'];
                            $Data[0]['Tramites'][$contador]['IDHistoricoTarifas'] = $row_rs_Tarifa['IDHistoricoTarifas'];
                            $Data[0]['Tramites'][$contador]['Expediente_Det'] = $row_rs_expediente['ID_Expediente'];
                            $Data[0]['Tramites'][$contador]['DescripcionDetalle'] = $row_rs_expediente['DESC_Tipo_Tramite'] . ' ' . $row_rs_expediente['DESC_Clase_Tramite'] . ' CON NÚMERO ';
                            // Dependiendo de la si es certificado o permiso se ajusta la referencia
                            If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-03' || $row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-01') {
                                    $Data[0]['Tramites'][$contador]['DescripcionDetalle'] = $Data[0]['Tramites'][$contador]['DescripcionDetalle'] . $permiso_ac;
                            } else {
                                $Data[0]['Tramites'][$contador]['DescripcionDetalle'] = $Data[0]['Tramites'][$contador]['DescripcionDetalle']  . $row_rs_expediente['Certificado_Operacion'];
                            }
                            // ***********************************************************************************************
                            // Final
                            // ***********************************************************************************************
                            // Armando datos para generar aviso de cobro
                            // ***********************************************************************************************
                            // ***********************************************************************************************
                            // Inicio
                            // ***********************************************************************************************
                            // Armando número de resolución
                            // ***********************************************************************************************
                            $Data[0]['ID_Resolucion'] = $ID_Resolucion;
                            // ***********************************************************************************************
                            //Guardando Resolucion para ser enviada a aviso de cobro
                            $Data[0]['Resolucion'] = $ID_Resolucion;
                            // Generando llave publica la que sera la llave que se exponga al publico quedando la llave privada(md5 del numero de soicitud)
                            $llave_publica = $row_rs_expediente['SOL_MD5'];
                            date_default_timezone_set('America/Guatemala');
                            $fi = $this->desmebrarFecha(date("Y/m/d h:i:sa"));
                            // Fecha de Impresion
                            $hora = substr($row_rs_expediente['Sistema_Fecha'],11,2);
                            if (intval($hora)>12){
                                $hora = intval($hora) -12;
                                if (strlen($hora) == 1) {
                                    $hora = '0' . $hora;
                                }
                                $row_rs_expediente['Sistema_Fecha'] = substr($row_rs_expediente['Sistema_Fecha'], 0, 10) . ' ' . $hora . substr($row_rs_expediente['Sistema_Fecha'], 13) . 'pm';
                            } else {
                                $row_rs_expediente['Sistema_Fecha'] .= 'am';
                            }
                            // Desmebrar fecha de Presentación de la Solicitud
                            $fp = $this->desmebrarFecha(date("Y/m/d h:i:sa",strtotime($row_rs_expediente['Sistema_Fecha'])));
                            //Fecha Recibido
                            $hora = substr($row_rs_expediente['FechaRecibido'],11,2);
                            if (intval($hora) >12){
                                $hora = intval($hora) -12;
                                if (strlen($hora) == 1) {
                                    $hora = '0' . $hora;
                                }
                                $row_rs_expediente['FechaRecibido'] = substr($row_rs_expediente['FechaRecibido'], 0, 10) . ' ' . $hora . substr($row_rs_expediente['FechaRecibido'], 13) . 'pm';
                            } else {
                                $row_rs_expediente['FechaRecibido'] .= 'am';
                            }
                            $url_auto='Documentos/' . $row_rs_expediente['Preforma'] .'/AutoMotivado_' . $row_rs_expediente['Preforma'] . '.pdf';
                            $ruta_resolucion_impresion = 'Documentos/'.  $row_rs_expediente['Preforma']  .'/RESOLUCION_NO._' .  $ID_Resolucion;
                            $url_aviso='';
                            $Data[0]['Auto_Ruta']=$url_auto;
                            $Data[0]['Aviso_Ruta']=$url_aviso;
                            // Desmembrar fecha de Recibido
                            $fr = $this->desmebrarFecha(date("Y/m/d h:i:sa",strtotime($row_rs_expediente['FechaRecibido'])));
                            $pagina_inicio = $row_rs_expediente['template'];//file_get_contents('docsrc/providencia.html');
                            //***************************************************************************************************
                            // Inicio
                            //***************************************************************************************************
                            // Comenzando armar template con los datos correpondientes
                            //***************************************************************************************************
                            // Institucion
                            $pagina_inicio = str_replace('@@institucion@@',$cfg_institucion,$pagina_inicio);
                            //Vigencia
                            // Número de Resolución
                            $pagina_inicio = str_replace('@@numres@@',$ID_Resolucion,$pagina_inicio);
                            // fecha impresion
                            $pagina_inicio = str_replace('@@hli@@',$fi['hora_letras'],$pagina_inicio);
                            $pagina_inicio = str_replace('@@mili@@',$fi['minutos_letras'],$pagina_inicio);
                            $pagina_inicio = str_replace('@@ampmi@@',$fi['ampm'],$pagina_inicio);
                            $pagina_inicio = str_replace('@@dli@@',$fi['dia_letras'],$pagina_inicio);
                            $pagina_inicio = str_replace('@@mli@@',$fi['mes_letras'],$pagina_inicio);
                            $pagina_inicio = str_replace('@@ali@@',$fi['anio_letras'],$pagina_inicio);
                            $pagina_inicio = str_replace('@@hmsampmi@@',$fi['fecha_hmsampm'],$pagina_inicio);
                            $pagina_inicio = str_replace('@@dmai@@',$fi['fecha_dma'],$pagina_inicio);
                            $pagina_inicio = str_replace('@@alp@@',$fi['anio_letras'],$pagina_inicio);
                            // fecha de recepcion
                            $pagina_inicio = str_replace('@@dlr@@',$fr['dia_letras'],$pagina_inicio);
                            $pagina_inicio = str_replace('@@mlr@@',$fr['mes_letras'],$pagina_inicio);
                            $pagina_inicio = str_replace('@@alr@@',$fr['anio_letras'],$pagina_inicio);
                            $pagina_inicio = str_replace('@@ampmr@@',$fr['ampm'],$pagina_inicio);
                            // Fecha de Presentacion
                            $pagina_inicio = str_replace('@@hmsampmp@@',$fp['fecha_hmsampm'],$pagina_inicio);	
                            $pagina_inicio = str_replace('@@dlp@@',$fp['dia_letras'],$pagina_inicio);
                            $pagina_inicio = str_replace('@@mlp@@',$fp['mes_letras'],$pagina_inicio);
                            $pagina_inicio = str_replace('@@dmap@@',$fp['fecha_dma'],$pagina_inicio);
                            $pagina_inicio = str_replace('@@hlp@@',$fp['hora_letras'],$pagina_inicio);
                            $pagina_inicio = str_replace('@@ampmp@@',$fp['ampm'],$pagina_inicio);
                            $pagina_inicio = str_replace('@@dlp@@',$fp['dia_letras'],$pagina_inicio);
                            $pagina_inicio = str_replace('@@anp@@',$fp['anio_letras'],$pagina_inicio);
                            $pagina_inicio = str_replace('@@mnp@@',$fp['mes_numerica'],$pagina_inicio);
                            $pagina_inicio = str_replace('@@milp@@',$fp['minutos_letras'],$pagina_inicio);
                            $pagina_inicio = str_replace('@@ns@@',$row_rs_expediente['ID_Solicitud'].' / '.$row_rs_expediente['ID_Expediente'],$pagina_inicio);
                            $pagina_inicio = str_replace('@@cs@@',$row_rs_expediente['NombreSolicitante'],$pagina_inicio);
                            $pagina_inicio = str_replace('@@rtncs@@',$row_rs_expediente['RTN_Solicitante'],$pagina_inicio);
                            $pagina_inicio = str_replace('@@rl@@',$row_rs_expediente['NombreApoderadoLega'],$pagina_inicio);
                            $pagina_inicio = str_replace('@@CAHN@@',$row_rs_expediente['ID_ColegiacionAPL'],$pagina_inicio);
                            $pagina_inicio = str_replace('@@solictud@@',$row_rs_expediente['ID_Solicitud'].' / '.$row_rs_expediente['ID_Expediente'],$pagina_inicio);
                            //$pagina_inicio = str_replace('@@tramites@@',$row_rs_expediente['DESC_Tipo_Tramite'] . ' AUTOMATICA',$pagina_inicio);
                            $pagina_inicio = str_replace('@@modalidad@@',$row_rs_expediente['ID_Tipo_Servicio'] . ' DE ' . $row_rs_expediente['DESC_Modalidad'] ,$pagina_inicio);
                            If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-01') {
                                $tramite = $row_rs_expediente['DESC_Tipo_Tramite'] . ' ' . $row_rs_expediente['DESC_Clase_Tramite'] . ' CON NÚMERO ' . $row_rs_expediente['Permiso_Explotacion'] . ' POR EL TERMINO DE DOCE AÑOS (12 AÑOS)';
                                $tramitepeticion = $row_rs_expediente['DESC_Tipo_Tramite'] . ' ' . $row_rs_expediente['DESC_Clase_Tramite'] . ' CON NÚMERO ' . $row_rs_expediente['Permiso_Explotacion'];
                                $es_renovacion_explotacion = true;
                            } else {
                                If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-02') {
                                    $tramite = $row_rs_expediente['DESC_Tipo_Tramite'] . ' ' . $row_rs_expediente['DESC_Clase_Tramite'] . ' CON NÚMERO ' . $row_rs_expediente['Certificado_Operacion']  . ' ASOCIADO AL PERMISO DE EXPLOTACIÓN NÚMERO ' . $row_rs_expediente['Permiso_Explotacion']  . ' POR EL TERMINO DE TRES AÑOS (3 AÑOS)';
                                    $tramitepeticion = $row_rs_expediente['DESC_Tipo_Tramite'] . ' ' . $row_rs_expediente['DESC_Clase_Tramite'] . ' CON NÚMERO ' . $row_rs_expediente['Certificado_Operacion']  . ' ASOCIADO AL PERMISO DE EXPLOTACIÓN NÚMERO ' . $row_rs_expediente['Permiso_Explotacion'];
                                    $es_renovacion_certificado = true;
                                } else	{
                                    If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-03') {
                                        $tramite = $row_rs_expediente['DESC_Tipo_Tramite'] . ' ' . $row_rs_expediente['DESC_Clase_Tramite'] . ' CON NÚMERO DE PERMISO ESPECIAL' . $row_rs_expediente['N_Permiso_Especial']  . ' POR EL TERMINO DE UN AÑO (1 AÑO)';									
                                        $tramitepeticion = $row_rs_expediente['DESC_Tipo_Tramite'] . ' ' . $row_rs_expediente['DESC_Clase_Tramite'] . ' CON NÚMERO DE PERMISO ESPECIAL ' . $row_rs_expediente['N_Permiso_Especial'];									
                                        $es_renovacion_permisoespecial = true;
                                    } else	{
                                        If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-15') {
                                            $tramite = $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', A NÚMERO DE PLACA: ' . $id_placa . ' EN EL '. $Data[0]['Tipo_Documento'] .' NUMERO: ' . $Data[0]['Certificado_Operacion'];									
                                            $tramitepeticion = $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', A NÚMERO DE PLACA: ' . $id_placa . ' EN EL '. $Data[0]['Tipo_Documento'] .' NUMERO: ' . $Data[0]['Certificado_Operacion'];									
                                            $es_replaqueo = 'SI';
                                            $cambio_placa = true;
                                        } else {
                                            If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-17') {
                                                $tramite = $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', A NUMERO DE MOTOR: ' . $vehiculoactual['Motor'] . ' EN EL '. $Data[0]['Tipo_Documento'] .' NUMERO: ' . $Data[0]['Certificado_Operacion'];									
                                                $tramitepeticion = $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', A NUMERO DE MOTOR: ' . $vehiculoactual['Motor'] . ' EN EL '. $Data[0]['Tipo_Documento'] .' NUMERO: ' . $Data[0]['Certificado_Operacion'];									
                                                $cambio_en_unidad = true;
                                            } else {
                                                If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-18') {
                                                    $tramite = $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', A COLOR: ' . $vehiculoactual['DESC_Color'] . ' EN EL '. $Data[0]['Tipo_Documento'] .' NUMERO: ' . $Data[0]['Certificado_Operacion'];									
                                                    $tramitepeticion = $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', A COLOR: ' . $vehiculoactual['DESC_Color'] . ' EN EL '. $Data[0]['Tipo_Documento'] .' NUMERO: ' . $Data[0]['Certificado_Operacion'];									
                                                    $cambio_en_unidad = true;
                                                } else {
                                                    If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-19') {
                                                        $tramite = $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', A NÚMERO DE CHASIS: ' . $vehiculoactual['Chasis'] . ' EN EL '. $Data[0]['Tipo_Documento'] .' NUMERO: ' . $Data[0]['Certificado_Operacion'];									
                                                        $tramitepeticion = $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', A NÚMERO DE CHASIS: ' . $vehiculoactual['Chasis'] . ' EN EL '. $Data[0]['Tipo_Documento'] .' NUMERO: ' . $Data[0]['Certificado_Operacion'];									
                                                        $cambio_en_unidad = true;
                                                        $es_replaqueo = 'SI';
                                                    }else {
                                                        If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-08') {
                                                            $tramite = $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', DE LA UNIDAD: ' . $detallevehiculoactual . ' POR LA NUEVA UNIDAD: ' . $detallevehiculoentra . ' EN EL '. $Data[0]['Tipo_Documento'] .' NUMERO: ' . $Data[0]['Certificado_Operacion'];									
                                                            $tramitepeticion = $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', DE LA UNIDAD: ' . $detallevehiculoactual . ' POR LA NUEVA UNIDAD: ' . $detallevehiculoentra . ' EN EL '. $Data[0]['Tipo_Documento'] .' NUMERO: ' . $Data[0]['Certificado_Operacion'];							
                                                            $es_replaqueo = 'SI';
                                                            $nueva_unidad = true;                                                        
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }		
                                }
                            }
                            $pagina_inicio = str_replace('@@ncf@@',$row_rs_expediente['Nombre_Firma'],$pagina_inicio);
                            $pagina_inicio = str_replace('@@tcf@@',$row_rs_expediente['Titulo_Cargo'],$pagina_inicio);
                            $qrubicacion= "../qr/temp/".$row_rs_expediente['SOL_MD5'].".png";
                            $URL = $this->dominio .'ra/VFRA.php?template='. $rs_id_rs_template .'&Resolucion=S&Llave_Publica='.$llave_publica;
                            QRcode::png($URL,$qrubicacion,QR_ECLEVEL_M,5,4);                            
                            $pagina_inicio = str_replace('qr/temp/QR.PNG',$qrubicacion,$pagina_inicio);
                            //* Inicio QR Comisionado Presidente
                            if ($row_rs_expediente['titulo_cargo_comisionado'] != '') {
                                $pagina_inicio = str_replace('@@ncfc@@',$row_rs_expediente['firma_comisionado'],$pagina_inicio);
                                $pagina_inicio = str_replace('@@tcfc@@',$row_rs_expediente['titulo_cargo_comisionado'],$pagina_inicio);
                                $pagina_inicio = str_replace('qr/temp/CQR.PNG',$qrubicacion,$pagina_inicio);
                            }
                            //**************************************************************************************************************************************/
                            //* Inicio                                                                                                                              */
                            //**************************************************************************************************************************************/
                            if ($row_rs_expediente['ID_Clase_Servico']== 'STEC') {
                                //* Es Renovacion de Tipo de Transporte
                                $es_renovacion_tipo_transporte = 'CARGA';        
                                $tipo_concesion = 'PES';    
                            } else {
                                if ($row_rs_expediente['ID_Clase_Servico']== 'STEP') {
                                    $es_renovacion_tipo_transporte = 'PASAJEROS';        
                                    $tipo_concesion = 'PES';
                                } else {
                                    if ($row_rs_expediente['ID_Clase_Servico']== 'STPC') {
                                        $es_renovacion_tipo_transporte = 'CARGA';        
                                        $tipo_concesion = 'CER';
                                    } else {
                                        //*****************************************************************************************
                                        //* Aqui entra al STPP Servicio Publico de Transporte de Pasajeros
                                        //*****************************************************************************************
                                        //* Tramites de Transporte de Pasajeros
                                        //*****************************************************************************************
                                        $es_renovacion_tipo_transporte = 'PASAJEROS';        
                                        $tipo_concesion = 'CER';
                                    }
                                }
                            }
                            //**************************************************************************************************************************************/
                            //* Armando URL dpara presentar Resolución, AutoMotivado y Renovación del Cetticiado y/o Permiso de Explotación o Permiso Especial      */
                            //* Final                                                                                                                               */
                            //**************************************************************************************************************************************/						
                        } else {
                            //**************************************************************************************************************************************/
                            //* Inicio                                                                                                                              */
                            //**************************************************************************************************************************************/
                            if ($row_rs_expediente['ID_Clase_Servico']== 'STEC') {
                                //* Es Renovacion de Tipo de Transporte
                                $es_renovacion_tipo_transporte = 'CARGA';        
                                $tipo_concesion = 'PES';    
                            } else {
                                if ($row_rs_expediente['ID_Clase_Servico']== 'STEP') {
                                    $es_renovacion_tipo_transporte = 'PASAJEROS';        
                                    $tipo_concesion = 'PES';
                                } else {
                                    if ($row_rs_expediente['ID_Clase_Servico']== 'STPC') {
                                        $es_renovacion_tipo_transporte = 'CARGA';        
                                        $tipo_concesion = 'CER';
                                    } else {
                                        //*****************************************************************************************
                                        //* Aqui entra al STPP Servicio Publico de Transporte de Pasajeros
                                        //*****************************************************************************************
                                        //* Tramites de Transporte de Pasajeros
                                        //*****************************************************************************************
                                        $es_renovacion_tipo_transporte = 'PASAJEROS';        
                                        $tipo_concesion = 'CER';
                                    }
                                }
                            }
                            // ***********************************************************************************************
                            // * Inicio
                            // ***********************************************************************************************
                            // * Armando datos para generar aviso de cobro
                            // ***********************************************************************************************
                            $Data[0]['Tramites'][$contador]['ID_tramite'] = $row_rs_expediente['ID_Tramite'];
                            If ($row_rs_expediente['N_Permiso_Especial'] != '') {
                                $Data[0]['Tramites'][$contador]['Certificado_Operacion'] = $row_rs_expediente['N_Permiso_Especial'];
                            } else {
                                $Data[0]['Tramites'][$contador]['Certificado_Operacion'] = $row_rs_expediente['Certificado_Operacion'];
                            }
                            $Data[0]['Tramites'][$contador]['ID_Placa'] = $id_placa;
                            $Data[0]['Tramites'][$contador]['Monto'] = $row_rs_Tarifa['Monto'];
                            $monto_total = $monto_total + $row_rs_Tarifa['Monto'];
                            $Data[0]['Tramites'][$contador]['IDHistoricoTarifas'] = $row_rs_Tarifa['IDHistoricoTarifas'];
                            $Data[0]['Tramites'][$contador]['Expediente_Det'] = $row_rs_expediente['ID_Expediente'];
                            $Data[0]['Tramites'][$contador]['DescripcionDetalle'] = $row_rs_expediente['DESC_Tipo_Tramite'] . ' ' . $row_rs_expediente['DESC_Clase_Tramite'] . ' CON NÚMERO ';
                            // Dependiendo de la si es certificado o permiso se ajusta la referencia
                            If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-03' || $row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-01') {
                                    $Data[0]['Tramites'][$contador]['DescripcionDetalle'] = $Data[0]['Tramites'][$contador]['DescripcionDetalle'] . $permiso_ac;
                            } else {
                                $Data[0]['Tramites'][$contador]['DescripcionDetalle'] = $Data[0]['Tramites'][$contador]['DescripcionDetalle']  . $row_rs_expediente['Certificado_Operacion'];
                            }						
                            // ***********************************************************************************************
                            // * Final
                            // ***********************************************************************************************
                            // * Armando datos para generar aviso de cobro
                            // ***********************************************************************************************
                            // * Si es el ultimo tramite a procesar la conjunción es Y
                            // * Ejemplo: RENOVACIO AUTOMATICA DE CERTIFICADO CO-XXX.XXXX Y RENOVACION DE PERMISOS DE EXPLOTAICON O
                            // * Ejemplo: RENOVACIO AUTOMATICA DE CERTIFICADO CO-XXX.XXXX, RENOVACION DE PERMISOS DE EXPLOTAICON Y CAMBIO DE UNIDAD (A FUTURO)
                            // ***********************************************************************************************
                            $conjunccion = '';
                            if ($total_registros == ($contador_tramites+1)){
                                $conjunccion = ' Y ';
                            } else {
                                if ($contador_tramites > 0) {
                                    $conjunccion = ',&nbsp;';
                                }
                            } 
                            If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-01') {
                                $tramite = $tramite . $conjunccion . $row_rs_expediente['DESC_Tipo_Tramite'] . ' ' . $row_rs_expediente['DESC_Clase_Tramite'] . ' CON NÚMERO ' . $row_rs_expediente['Permiso_Explotacion'] . ' POR EL TERMINO DE DOCE AÑOS (12 AÑOS)';
                                $tramitepeticion = $tramitepeticion . $conjunccion . $row_rs_expediente['DESC_Tipo_Tramite'] . ' ' . $row_rs_expediente['DESC_Clase_Tramite'] . ' CON NÚMERO ' . $row_rs_expediente['Permiso_Explotacion'];
                                $es_renovacion_explotacion = true;
                            } else {
                                If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-02') {
                                    $tramite = $tramite . $conjunccion . $row_rs_expediente['DESC_Tipo_Tramite'] . ' ' . $row_rs_expediente['DESC_Clase_Tramite'] . ' CON NÚMERO ' . $row_rs_expediente['Certificado_Operacion']  . ' ASOCIADO AL PERMISO DE EXPLOTACIÓN NÚMERO ' . $row_rs_expediente['Permiso_Explotacion']  . ' POR EL TERMINO DE TRES AÑOS (3 AÑOS)';
                                    $tramitepeticion = $tramitepeticion . $conjunccion . $row_rs_expediente['DESC_Tipo_Tramite'] . ' ' . $row_rs_expediente['DESC_Clase_Tramite'] . ' CON NÚMERO ' . $row_rs_expediente['Certificado_Operacion']  . ' ASOCIADO AL PERMISO DE EXPLOTACIÓN NÚMERO ' . $row_rs_expediente['Permiso_Explotacion'];
                                    $es_renovacion_certificado = true;
                                } else	{
                                    If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-03') {
                                        $tramite = $tramite . $conjunccion . $row_rs_expediente['DESC_Tipo_Tramite'] . ' ' . $row_rs_expediente['DESC_Clase_Tramite'] . ' CON NÚMERO ' . $row_rs_expediente['N_Permiso_Especial']  . ' POR EL TERMINO DE UN AÑO (1 AÑO)';									
                                        $tramitepeticion = $tramitepeticion . $conjunccion . $row_rs_expediente['DESC_Tipo_Tramite'] . ' ' . $row_rs_expediente['DESC_Clase_Tramite'] . ' CON NÚMERO ' . $row_rs_expediente['N_Permiso_Especial'];									
                                        $es_renovacion_permisoespecial = true;
                                    } else	{
                                        If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-15') {
                                            $tramite = $tramite . $conjunccion . $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', A NÚMERO DE PLACA: ' . $id_placa . ' EN EL '. $Data[0]['Tipo_Documento'] .' NUMERO: ' . $Data[0]['Certificado_Operacion'];									
                                            $tramitepeticion = $tramitepeticion . $conjunccion . $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', A NÚMERO DE PLACA: ' . $id_placa . ' EN EL '. $Data[0]['Tipo_Documento'] .' NUMERO: ' . $Data[0]['Certificado_Operacion'];									
                                            $es_replaqueo = 'SI';
                                            $cambio_placa = true;
                                        } else {
                                            If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-17') {
                                                $tramite = $tramite . $conjunccion . $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', A NUMERO DE MOTOR: ' . $vehiculoactual['Motor']  . ' EN EL '. $Data[0]['Tipo_Documento'] .' NUMERO: ' . $Data[0]['Certificado_Operacion'];									
                                                $tramitepeticion = $tramitepeticion . $conjunccion . $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', A NUMERO DE MOTOR: ' . $vehiculoactual['Motor'] . ' EN EL '. $Data[0]['Tipo_Documento'] .' NUMERO: ' . $Data[0]['Certificado_Operacion'];									
                                                $cambio_en_unidad = true;
                                            } else {
                                                If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-18') {
                                                    $tramite = $tramite . $conjunccion . $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', A COLOR: ' . $vehiculoactual['DESC_Color'] . ' EN EL '. $Data[0]['Tipo_Documento'] .' NUMERO: ' . $Data[0]['Certificado_Operacion'];									
                                                    $tramitepeticion = $tramitepeticion . $conjunccion . $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', A COLOR: ' . $vehiculoactual['DESC_Color'] . ' EN EL '. $Data[0]['Tipo_Documento'] .' NUMERO: ' . $Data[0]['Certificado_Operacion'];									
                                                    $cambio_en_unidad = true;
                                                } else {
                                                    If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-19') {
                                                        $tramite = $tramite . $conjunccion . $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite']  . ', A NÚMERO DE CHASIS: ' . $vehiculoactual['Chasis'] . ' EN EL '. $Data[0]['Tipo_Documento'] .' NUMERO: ' . $Data[0]['Certificado_Operacion'];									
                                                        $tramitepeticion = $tramitepeticion . $conjunccion . $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', A NÚMERO DE CHASIS: ' . $vehiculoactual['Chasis'] . ' EN EL '. $Data[0]['Tipo_Documento'] .' NUMERO: ' . $Data[0]['Certificado_Operacion'];									
                                                        $cambio_en_unidad = true;
                                                    }else {
                                                        If ($row_rs_expediente['ID_Clase_Tramite'] == 'CLATRA-08') {
                                                            $tramite = $tramite . $conjunccion . $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', DE LA UNIDAD ' . $detallevehiculoactual . ' POR LA NUEVA UNIDAD: ' . $detallevehiculoentra . ' EN EL '. $Data[0]['Tipo_Documento'] .' NUMERO: ' . $Data[0]['Certificado_Operacion'];
                                                            $tramitepeticion = $tramitepeticion . $conjunccion . $row_rs_expediente['DESC_Tipo_Tramite'] . ' DE ' . $row_rs_expediente['DESC_Clase_Tramite'] . ', DE LA UNIDAD ' . $detallevehiculoactual . ' POR LA NUEVA UNIDAD: ' . $detallevehiculoentra . ' EN EL '. $Data[0]['Tipo_Documento'] .' NUMERO: ' . $Data[0]['Certificado_Operacion'];
                                                            $es_replaqueo = 'SI';
                                                            $nueva_unidad = true;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }		
                                }
                            }
                        }
                        $contador++;
                        $contador_tramites++;
                    } else {
                        $msg =  'getTarifa ' . $row_rs_Tarifa['msg'];
                        $Error_Proceso = true;
                        break;
                    }
                } else {
                    $msg =  'row_rs_expediente["ID_Solicitud"] $total_registros' . $total_registros;
                    $Error_Proceso = true;
                    break;
                }
            }

            $tramite=$tramite.';&nbsp;';
            $tramitepeticion=$tramitepeticion.';&nbsp;';

            if ($Error_Proceso == true) {
                break;
            } else {
                if ($contador > 0) {
                    $fProcesarConcesionesEnSGCERP = $this->fProcesarConcesionesEnSGCERP($es_renovacion_explotacion,$es_renovacion_certificado,$es_renovacion_permisoespecial,$cambio_placa,$cambio_en_unidad,$nueva_unidad,$id_placa,$id_placa_antes_replaqueo,$es_renovacion_tipo_transporte,$tipo_concesion,$vehiculoentra,$vehiculoactual,$Data,$tramitepeticion,$ID_Resolucion,$ID_ColegiacionAPL,$es_replaqueo,isset($detallevehiculoentra)?$detallevehiculoentra:'');
                    if (isset($fProcesarConcesionesEnSGCERP['botoncertificado']) && $fProcesarConcesionesEnSGCERP['botoncertificado'] == '') {
                        $Error_Proceso = true;
                        break;
                    } else {
                        if ($concesionesnumeros == '') {
                            $concesionesnumeros = $fProcesarConcesionesEnSGCERP['botoncertificado'];
                        }
                    }
                }
            }            
        } 
        //******************************************************************************************/
        //* FINAL: CICLO QUE PROCESA TODAS LAS CONCESIONES REGISTRADAS EN EL EXPEDIENTE
        //******************************************************************************************/
        // Valores por omision de respuesta
        $respuestaretornar['boton']='';
        $respuestaretornar['ruta']='';
        $respuestaretornar['id_resolucion']='';
        if ($contador == 0) {
            $respuestaretornar['error']=true;
            if ($msg == '') {
                $respuestaretornar['msg']='funcion Principal No Hay Datos';
            }else {
                $respuestaretornar['msg']=$msg;
            }
            return $respuestaretornar;
        }
        //**********************************************************************************/
        //* Si se proceso bien todo genere pdf
        //**********************************************************************************/
        if ($pagina_inicio != '' && $Error_Proceso == false) {
            // Si todo va bien, insertar aviso de cobro
            if (isset($Data)){
                $Data[0]['Monto_Total'] = $monto_total;
                $Data[0]['Tramite'] = $tramite;
                // Poniendo la descripción de todos los tramites que lleva el expediente
                $pagina_inicio = str_replace('@@tramites@@',$tramite,$pagina_inicio);
                $pagina_inicio = str_replace('@@tramitespeticion@@',$tramitepeticion,$pagina_inicio);
                //echo $pagina_inicio;die();
                $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [215.9, 355.6],'tempDir' => sys_get_temp_dir()]);
                $mpdf->pdf_version = '1.7';
                $mpdf->SetTitle('Resolución '. $ID_Resolucion);
                $mpdf->SetAuthor('Instituto Hondureño de Transporte Terrestre');
                $mpdf->SetCreator('SATT');
                $mpdf->SetSubject('Documentos Legales IHTT');
                //$mpdf->SetWatermarkImage('../images/background.jpg');
                //$mpdf->showWatermarkImage = true;
                $mpdf->PDFXauto = true;
                $mpdf->showWatermarkText = true;
                $mpdf->SetWatermarkText("IHTT");
                $mpdf->watermark_font = 'DejaVuSansCondensed';
                $mpdf->watermarkTextAlpha = 0.10;
                $mpdf->margin_header = 0;
                $mpdf->SetMargins(0, 0, 30);
                $mpdf->SetDisplayMode('fullwidth');
                //$mpdf->SetDisplayPreferences('/HideMenubar/HideToolbar/DisplayDocTitle');
                $mpdf->SetHTMLHeader('<div height="100%" width="100%"><img height="15%" width="75%" alt="encabezado" src="assets/images/encabezado-pagina1.png"></div>','O', true);
                $mpdf->SetHTMLFooter('<table width="100%"><tr><td width="20%" align="center">Página(s): {PAGENO} de {nbpg}</td><td width="50%" align="center">'.$_SESSION["user_name"].'</td><td width="50%" align="right">Resolución No. '.$ID_Resolucion.'</td></tr></table>');
                $mpdf->WriteHTML($pagina_inicio);
                //$mpdf->Output($row_rs_expediente['ID_Solicitud'] . '_Resolucion'  .'.pdf','D');
                $directory='Documentos/' . $preforma;
                if(!is_dir($directory)) {
                    if (!mkdir($directory, 0777, true)) {
                    } 
                }                     
                $ruta = $directory . '/'. $ID_Resolucion . '.pdf';
                if (file_exists($ruta)){ unlink($ruta);}
                $mpdf->Output($ruta, \Mpdf\Output\Destination::FILE);
                if (file_exists($qrubicacion)){ unlink($qrubicacion);}
                $Data[0]['Ruta']  = $ruta;
                $Data[0]['Template'] = 	$this->getTemplate(7);
                $Data[0]['Titulo_Correo'] = 'AUTOMOTIVADO Y RESOLUCIÓN';
                if ($Data[0]['Template'] != false) {
                } 
                //**********************************************************************************/
                // Respuesta para Comprobante de ingreso al SICE
                //**********************************************************************************/
                $rutacomprobante = $this->dominio_raiz . ":84/Recepcion_Solicitudes_Masivo/api_imprimir.php?action=get-Solicitud-new&sol=". $Data[0]['SOL_MD5'];
                $respuestaretornar['errorcomprobante']=false;
                $respuestaretornar['msgcomprobante']='<strong>  IMPRIMIR COMPROBANTE DE INGRESO AL SICE DEL EXPEDIENTE/SOLICITUD: ' . $Data[0]['ID_Expediente'].'/'.$Data[0]['ID_Solicitud'] . '  </strong>';
                $respuestaretornar['botoncomprobante']='<a style="background-color: #F163D3; border-radius: 15px; border: solid 4px #1B0354D0;" href="'.$rutacomprobante.'" target="_blank"  class="waves-effect waves-green btn-flat btn"><i class="material-icons print"></i>'.$respuestaretornar['msgcomprobante'].'</a>';
                $respuestaretornar['rutacomprobante']=$rutacomprobante;
                //**********************************************************************************/
                // Respuesta para ver Portada
                //**********************************************************************************/
                $rutaportada = $this->dominio_raiz . ":84/Recepcion_Solicitudes_Masivo/api_imprimir_exp.php?action=get-Expediente&sol=". $Data[0]['ID_Solicitud'];
                $respuestaretornar['errorportada']=false;
                $respuestaretornar['msgportada']='<strong>  IMPRIMIR PORTADA DEL EXPEDIENTE/SOLICITUD: ' . $Data[0]['ID_Expediente'].'/'.$Data[0]['ID_Solicitud'] . '  </strong>';
                $respuestaretornar['botonportada']='<a style="background-color: #E46A0C; border-radius: 15px; border: solid 4px #151414;" href="'.$rutaportada.'" target="_blank"  class="waves-effect waves-green btn-flat btn"><i class="material-icons print"></i>IMPRIMIR PORTADA EXPEDIENTE No. '.$Data[0]['ID_Expediente'].'</a>';
                $respuestaretornar['rutaportada']=$rutaportada;
                //**********************************************************************************/
                // Respuesta para ver Resolucion
                //**********************************************************************************/
                $respuestaretornar['error']=false;
                $respuestaretornar['msg']='<strong>  IMPRIMIR RESOLUCION CON NÚMERO: ' . $ID_Resolucion .  '  </strong>';
                $respuestaretornar['boton']='<a style="background-color: #D8E42D; border-radius: 15px; border: solid 4px #0E0E0D;" href="'.$ruta.'" target="_blank"  class="waves-effect waves-green btn-flat btn"><i class="material-icons print"></i>'.$respuestaretornar['msg'].'</a>';
                $respuestaretornar['ruta']=$ruta;
                $respuestaretornar['id_resolucion']=$ID_Resolucion;
                $respuestaretornar['ConcesionesEncryptada'] = $ConcesionesEncryptada;
                $respuestaretornar['ConcesionesNumero'] = $ConcesionesNumero;
                $respuestaretornar['botoncertificado'] = $concesionesnumeros;
                return $respuestaretornar;
                //$respuestaretornar['id_vehiculo']=$id_vehiculo;
            } 
        }  else {
            echo 'Error al generar PDF';
            return false;            
        }       
    }    
    //***********************************************************************************************
	//* FINAL: Funcion principal de Generacion de Resolucion
	//***********************************************************************************************    
    //***********************************************************************************************
	//* INICIO: Funcion principal de cierre de RAM ya es Expediente
	//***********************************************************************************************
	protected function cerrarRAM($RAM){
        $this->db->beginTransaction();
        //***********************************************************************************************
        //* Salvando La Bitacora y el Número de AutoMotivado
        //***********************************************************************************************
        $saveBitacoraNumeroAuto = $this->saveBitacoraNumeroAuto($RAM);
        if ($saveBitacoraNumeroAuto != false) {
            //***********************************************************************************************
            //* Actualizando el Estado del RAM en Preforma
            //***********************************************************************************************
            $updateEstadoPreforma = $this->updateEstadoPreforma($_POST["RAM"],$_POST['idEstado']);
            if (!isset($respuestaupdateEstadoPreforma['error'])) {
                $pdfAutoMotivadoIngresoApi = $this->pdfAutoMotivadoIngresoApi($RAM, $saveBitacoraNumeroAuto, $template=1);
                if ($pdfAutoMotivadoIngresoApi != false) {
                    //***********************************************************************************************
                    //* Recuperando el siguiente numero de resolucion
                    //***********************************************************************************************
                    $getSiguienteResolucion = $this->getSiguienteResolucion();
                    if ($getSiguienteResolucion != false) {
                        $saveResolucion = $this->saveResolucion ($getSiguienteResolucion ,$_POST["RAM"],$_SESSION['ID_Usuario'],$_SESSION['user_name']);
                        if ($saveResolucion != false) {
                            $saveBitacoraFirma = $this->saveBitacoraFirma($getSiguienteResolucion,   'RESOLUCION', $_SESSION["user_name"],  $pdfAutoMotivadoIngresoApi['llave_publica']);	
                            if ($saveBitacoraFirma != false) {
                                $PDFResolucionApi = $this->PDFResolucionApi($_POST["RAM"],$getSiguienteResolucion,$rs_id_rs_template=3,$cfg_institucion='INSTITUTO HONDUREÑO DEL TRANSPORTE TERRESTRE');
                                if ($PDFResolucionApi != false) {
                                    echo json_encode(array("AutoIngreso" => $pdfAutoMotivadoIngresoApi['Boton'],
                                    "Resolucion" => $PDFResolucionApi['boton'],
                                    "Comprobante" => $PDFResolucionApi['botoncomprobante'],
                                    "Portada" => $PDFResolucionApi['botonportada'],
                                    'ConcesionesEncryptada' => $PDFResolucionApi['ConcesionesEncryptada'],
                                    'ConcesionesNumero' => $PDFResolucionApi['ConcesionesNumero'],
                                    'Concesion' => str_replace('@@__CONCESIONES__@@',$miString = implode(',', $PDFResolucionApi['ConcesionesNumero']),$PDFResolucionApi['botoncertificado']),));
                                    //$this->db->rollBack();
                                    $this->db->commit();
                                } else {
                                    $this->db->rollBack();
                                    echo json_encode(array("error" => 6006, "errorhead" => 'GENERANDO RESOLUCIÓN', "errormsg" => 'ESTAMOS PRESENTANDO INCONVENIENTES, INTENTANLO NUEVAMENTE SI EL INCONVENIENE PERSISTE CONTACTE AL ADMINISTRADOR DEL SISTEMA'));
                                }                        
                            } else {
                                $this->db->rollBack();
                                echo json_encode(array("error" => 6005, "errorhead" => 'GENERANDO RESOLUCIÓN', "errormsg" => 'ESTAMOS PRESENTANDO INCONVENIENTES, INTENTANLO NUEVAMENTE SI EL INCONVENIENE PERSISTE CONTACTE AL ADMINISTRADOR DEL SISTEMA'));
                            }                        
                        } else {
                            $this->db->rollBack();
                            echo json_encode(array("error" => 6004, "errorhead" => 'SALVANDO RESOLUCIÓN', "errormsg" => 'ESTAMOS PRESENTANDO INCONVENIENTES, INTENTANLO NUEVAMENTE SI EL INCONVENIENE PERSISTE CONTACTE AL ADMINISTRADOR DEL SISTEMA'));
                        }                        
                    } else {
                        $this->db->rollBack();
                        echo json_encode(array("error" => 6003, "errorhead" => 'OBTENIENDO SIGUIENTE NUMERO DE RESOLUCION', "errormsg" => 'ESTAMOS PRESENTANDO INCONVENIENTES, INTENTANLO NUEVAMENTE SI EL INCONVENIENE PERSISTE CONTACTE AL ADMINISTRADOR DEL SISTEMA'));
                    }
                } else {
                    $this->db->rollBack();
                    echo json_encode(array("error" => 6002, "errorhead" => 'AUTOMOTIVADO PDF', "errormsg" => 'ESTAMOS PRESENTANDO INCONVENIENTES, INTENTANLO NUEVAMENTE SI EL INCONVENIENE PERSISTE CONTACTE AL ADMINISTRADOR DEL SISTEMA'));
                }
            } else {
                echo $updateEstadoPreforma;
            }
        } else {
            $this->db->rollBack();
            echo json_encode(array("error" => 6001, "errorhead" => 'SALVANDO BITACORA Y AUTOMOTIVADO', "errormsg" => 'ESTAMOS PRESENTANDO INCONVENIENTES, INTENTANLO NUEVAMENTE SI EL INCONVENIENE PERSISTE CONTACTE AL ADMINISTRADOR DEL SISTEMA'));
        }
    }
	//***********************************************************************************************
	//* Final: Funcion principal de cierre de RAM ya es Expediente
	//***********************************************************************************************


}

if (!isset($_SESSION["ID_Usuario"]) || !isset($_SESSION["user_name"])) {
	echo json_encode(array("error" => 1000, "errorhead" => "INICIO DE SESSIÓN", "errormsg" => 'NO HAY UNA SESSION INICIADA, FAVOR INICIE SESION Y VUELVA A INTENTARLO'));
} else {
	$api = new Api_Exp($db,
			$appcfg_Dominio,
            $appcfg_Dominio_Corto,
            $appcfg_Dominio_Raiz,
            $appcfg_smtp_server,
            $appcfg_smtp_port,
            $appcfg_smtp_user,
            $appcfg_smtp_password,
            $appcfg_estado_inicial,
            $appcfg_estado_inicial_descripcion);
}