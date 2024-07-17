<?php
//error_reporting(0);
date_default_timezone_set("America/Tegucigalpa"); 
header('Content-Type: application/x-javascript; charset=utf-8');
header('Access-Control-Allow-Origin: *');
session_start();
ini_set('post_max_size', '100M');
ini_set('upload_max_filesize', '100M');
ini_set('max_execution_time', '1000');
ini_set('max_input_time', '1000');
ini_set('memory_limit', '256M');
require_once("../config/conexion.php");
require_once("../logs/logs.php");

class Api_Ram {
	public function __construct(){
		if(isset($_POST["action"])){
			if ($_POST["action"] == "get-apoderado" && isset($_POST["idApoderado"])) {
				$this->getApoderadoAPI($_POST["idApoderado"]);
			} else if ($_POST["action"] == "get-solicitante" && isset($_POST["idSolicitante"])) {				
				$this->getSolicitante();
			} else { echo json_encode(array("error" =>1001,"errormsg" =>'NO SE ENCONTRO NINGUNA FUNCION EN EL API PARA LA RUTA REQUERIDA'));}
		}
	}
	
	function select($q, $p) {
		global $db;
		try {
			$stmt = $db->prepare($q);
			$stmt->execute($p);
			$datos = $stmt->fetchAll();
			$res = $stmt->errorInfo();
			if (isset($res) and isset($res[3]) and intval(Trim($res[3])) <> 0) {
				$txt = date('Y m d h:i:s') . '	Usuario: ' . $_SESSION['usuario'] . ' -- ' .'API_RAM.PHP Error Select: Error q ' . $q . ' $res[0] ' .  $res[0] . ' $res[1] ' . $res[1] . ' $res[2] ' .$res[2] . ' $res[3] ' . $res[3];
				logErr($txt,'../logs/logs.txt');
				return false;
			} else {
				return $datos;
			}				
		} catch (PDOException $e) {
			// Capturar excepciones de PDO (error de base de datos)
			echo "Error en la consulta: " . $e->getMessage();
			$txt = date('Y m d h:i:s') . 'Api_Ram.php	Usuario: ' . $_SESSION['usuario'] .' - Error Catch PDOException ' . $e->getMessage();
			logErr($txt,'../logs/logs.txt');
			return false; // O devolver un valor indicando error
		}
	}
	
	/*************************************************************************************/
	/* FUNCION PARA RECUPERAR EL APODERADO LEGAL DEL PORTAL DE APODERADOS 
	/*************************************************************************************/
	function getApoderadoAPI($col) {
		$query = "SELECT ID_ColegiacionAPL, Nombre_Apoderado_Legal, Identidad, Email, Telefono, Direccion FROM [IHTT_DB].[dbo].[TB_Apoderado_Legal] WHERE ID_ColegiacionAPL = :IDCOL";
		$p = array(":IDCOL" => $col);
		if(isset($_POST["echo"])){
			$data = $this->select($query, $p );
			if (count($data)>0) {
				echo json_encode(array("id_colegiacion" =>$data[0]["ID_ColegiacionAPL"],"nombre_apoderado" =>$data[0]["Nombre_Apoderado_Legal"],"ident_apoderado" =>$data[0]["Identidad"],"correo_apoderado" =>$data[0]["Email"],"tel_apoderado" =>$data[0]["Telefono"],"dir_apoderado" =>$data[0]["Direccion"]));
			} else {
				echo json_encode(array("id_colegiacion" =>'',"nombre_apoderado" =>'',"ident_apoderado" =>'',"correo_apoderado" =>'',"tel_apoderado" =>'',"dir_apoderado" => count($data)));
			}				
		} else {
			return json_encode($this->select($query, $p ));
		}
	}


	function getDepartamentos(){
		$q = "SELECT ID_Departamento as Value, DESC_Departamento as Text FROM [IHTT_SELD].[dbo].TB_Departamento";
		return $this->select($q,array());
	}

	function getMunicipios($ID_Departamento){
		$q = "SELECT ID_Municipio as Value, DESC_Municipio as Text FROM [IHTT_SELD].[dbo].TB_Municipio where ID_Departamento = :ID_Departamento";
		return $this->select($q,array(':ID_Departamento'=> $ID_Departamento));
	}

	function getAldeas($ID_Municipio){
		$q = "SELECT ID_Aldea as Value, DESC_Aldea as Text FROM [IHTT_PREFORMA].[dbo].[TB_Aldea] where ID_Municipio = :ID_Municipio";
		return $this->select($q,array(':ID_Municipio'=> $ID_Municipio));
	}

	
	function getEntregaUbicacion() {
		$q = "SELECT ID_Ubicacion as Value, DESC_Ubicacion as Text FROM IHTT_DB.dbo.TB_Entrega_Ubicaciones";
		return $this->select($q,array());
	 }
	function getTipoSolicitante() {
		$q="SELECT * FROM [IHTT_SELD].[dbo].[TB_Tipo_Solicitante]";
		return $this->select($q,array());
	}

	/*************************************************************************************/
	/* FUNCION PARA RECUPERAR Aldea, Departamento y Municipio
	/*************************************************************************************/
	function ALDEASDEPARTAMENTO($col) {
		$query = "SELECT TB_Departamento.ID_Departamento, TB_Municipio.ID_Municipio, TB_Aldea.ID_Aldea 
		FROM [IHTT_PREFORMA].[dbo].[TB_Departamento] 
		INNER JOIN [IHTT_PREFORMA].[dbo].[TB_Municipio] ON TB_Departamento.ID_Departamento = TB_Municipio.ID_Departamento 
		INNER JOIN [IHTT_PREFORMA].[dbo].[TB_Aldea] ON TB_Municipio.ID_Municipio = TB_Aldea.ID_Municipio WHERE TB_Aldea.ID_Aldea = :IDCOL";
		$p = array(":IDCOL" => $col);
		$data = $this->select($query, $p );
		if (count($data)>0) {
		return array("Aldea" =>$data[0]["ID_Aldea"],"Municipio" =>$data[0]["ID_Municipio"],"Departamento" =>$data[0]["ID_Departamento"]);
		} else {
			return array("Aldea" =>'',"Municipio" =>'',"Departamento" =>'');
		}				
	}


	/*************************************************************************************/
	/* FUNCION PARA RECUPERAR EL SOLICITANTE
	/*************************************************************************************/
	function getSolicitante() {
		$query = "SELECT a.*,b.DESC_Solicitante FROM ihtt_preforma.dbo.v_Datos_Solicitante a,[IHTT_SELD].[dbo].[TB_Tipo_Solicitante] b WHERE a.CodigoSolicitanteTipo = b.ID_Tipo_Solicitante and a.ID_Solicitante = :IDSOL";
		$p = array(":IDSOL" => $_POST["idSolicitante"]);
		$data = $this->select($query, $p );
		$datos[0]= count($data);
		if (count($data)>0) {
			$Aldeas = $this->ALDEASDEPARTAMENTO($data[0]["Aldea"]);
			$datos[1] = array('DESC_Solicitante' =>$data[0]["DESC_Solicitante"],
			"rtn_solicitante" =>$data[0]["RTNSolicitante"],"nombre_solicitante" =>$data[0]["NombreSolicitante"],"nombre_empresa" =>$data[0]["NombreEmpresa"],
			"codigo_tipo" =>$data[0]["CodigoSolicitanteTipo"],"dir_solicitante" =>$data[0]["Direccion"],"tel_solicitante" =>$data[0]["Telefono"],
			"correo_solicitante" =>$data[0]["Email"],"aldea" =>$data[0]["Aldea"],'Aldeas'=>$Aldeas['Aldea'],'Municipio'=>$Aldeas['Municipio'],'Departamento'=>$Aldeas['Departamento'],
			'Numero_Escritura'=>$data[0]['ID_Escritura'],'Fecha_Escritura'=>$data[0]['Fecha_Elaboracion'],'Lugar_Escritura'=>$data[0]['Lugar_Elaboracion'],
			'ID_Notario'=>$data[0]['ID_Notario'],'Notario'=>$data[0]['Nombre_Notario'],'RTN_Representante'=>$data[0]['ID_Representante_Legal'],
			'Nombre_Representante'=>$data[0]['Nombre_Representante_Legal'],'Telefono_Representante'=>$data[0]['Telefono_Representante'],
			'Email_Representante'=>$data[0]['Email_Representante'],'Direccion_Representante'=>$data[0]['Direccion_Representante'],
			'Representante_Escritura'=>$data[0]['Representante_Escritura'],'Fecha_Elaboracion_Representante'=>$data[0]['Fecha_Elaboracion_Representante'],
			'Lugar_Elaboracion_Representante'=>$data[0]['Lugar_Elaboracion_Representante'],'Numero_Inscripcion'=>$data[0]['Numero_Inscripcion'],
			'ID_Notario_Representante'=>$data[0]['ID_Notario_Representante'],'Nombre_Notario_Representante'=>$data[0]['Nombre_Notario_Representante']);
			$datos[2]= $this->getTipoSolicitante();
			$datos[3]= $this->getEntregaUbicacion();
			$datos[4]= $this->getDepartamentos();
			$datos[5] = $this->getMunicipios(isset($Aldeas['Departamento']) ? $Aldeas['Departamento'] : 0);
			$datos[6] = $this->getAldeas(isset($Aldeas['Municipio']) ? $Aldeas['Municipio'] : 0);
		}
		echo json_encode($datos);
	}

}

if (!isset($_SESSION["ID_Usuario"]) || !isset($_SESSION["user_name"])) {
	echo json_encode(array("error" =>1000,"errormsg" =>'NO HAY UNA SESSION INICIADA, FAVOR INICIE SESION Y VUELVA A INTENTARLO'));
} else {
	$api = new Api_Ram();	
}