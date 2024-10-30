<?php
error_reporting(0);
header('Content-Type: application/x-javascript; charset=utf-8');
header('Access-Control-Allow-Origin: *');
session_start();
//******************************************************************/
// Es Renovacion Automatica
//******************************************************************/
if (!isset($_SESSION["Es_Renovacion_Automatica"])) {
	$_SESSION["Es_Renovacion_Automatica"]=true;
}
//******************************************************************/
// Es originado en ventanilla
//******************************************************************/
if (!isset($_SESSION["Originado_En_Ventanilla"])) {
	$_SESSION["Originado_En_Ventanilla"]=true;
}
ini_set('post_max_size', '100M');
ini_set('upload_max_filesize', '100M');
ini_set('max_execution_time', '1000');
ini_set('max_input_time', '1000');
ini_set('memory_limit', '256M');
date_default_timezone_set("America/Tegucigalpa"); 
require_once("../config/conexion.php");
require_once("../logs/logs.php");

class Api_Ram {

	protected $db;
	protected $ip;
	protected $host;

	public function __construct($db){
		$this->setDB($db);
		$this->setIp();
		$this->setHost();
		if(isset($_POST["action"])){
			if ($_POST["action"] == "get-apoderado" && isset($_POST["idApoderado"])) {
				$this->getApoderadoAPI($_POST["idApoderado"]);
			} else if ($_POST["action"] == "get-solicitante" && isset($_POST["idSolicitante"])) {				
				$this->getSolicitante();
			} else if ($_POST["action"] == "get-concesion" && isset($_POST["Concesion"])) {				
				$this->getConcesion();				
			} else if ($_POST["action"] == "get-datosporomision") {				
				$this->getDatosPorOmision();				
			} else if ($_POST["action"] == "get-municipios") {				
				$this->getMunicipios($_POST["filtro"]);								
			} else if ($_POST["action"] == "get-aldeas") {				
				$this->getAldeas($_POST["filtro"]);				
			} else if ($_POST["action"] == "save-preforma") {	
				$this->savePreforma();
			} else if ($_POST["action"] == "get-vehiculo") {				
				echo json_encode($this->getDatosUnidadDesdeIP($_POST["ID_Placa"]));								
			} else { 
				echo json_encode(array("error" =>1001,"errorhead" =>'OPPS',"errormsg" =>'NO SE ENCONTRO NINGUNA FUNCION EN EL API PARA LA ACCIÓN REQUERIDA'));
			}
		}
	}

	protected function setDB($db){
		$this->db=$db;
	}

	protected function getDB($db){
		$this->db;
	}

	protected function setIp(){
		if (!empty($_SERVER['REMOTE_ADDR'])) {
			$this->ip = $_SERVER['REMOTE_ADDR'];
		} elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$this->ip = $_SERVER['HTTP_CLIENT_IP'];
		} else {
			$this->ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
	}

	protected function getIp(){
		return $this->ip;
	}



    protected function setHost() {
		$this->host = gethostbyaddr($this->getIp());
    }

	protected function getHost() {
		return $this->host;
	}

	/*************************************************************************************/
	/* FUNCION PARA EJECUTAR SELECT SOBRE LA BASE DE DATOS
	/*************************************************************************************/
	protected function select($q, $p) {
		try {
			$stmt = $this->db->prepare($q);
			$stmt->execute($p);
			$datos = $stmt->fetchAll();
			$res = $stmt->errorInfo();
			if (isset($res) and isset($res[3]) and intval(Trim($res[3])) <> 0) {
				$txt = date('Y m d h:i:s') . ';Api_Ram.php	Usuario:; ' . $_SESSION['usuario'] . '; -- ' .'API_RAM.PHP Error Select: Error q; ' . $q . '; $res[0] ' .  $res[0] . ' $res[1] ' . $res[1] . ' $res[2] ' .$res[2] . ' $res[3] ' . $res[3];
				logErr($txt,'../logs/logs.txt');
				return false;
			} else {
				return $datos;
			}				
		} catch (PDOException $e) {
			// Capturar excepciones de PDO (error de base de datos)
			$txt = date('Y m d h:i:s') . 'Api_Ram.php	Usuario:; ' . $_SESSION['usuario'] .'; - Error Catch PDOException; ' . $e->getMessage() . ' QUERY ' .$q;
			logErr($txt,'../logs/logs.txt');
			return false; // O devolver un valor indicando error
		}
	}

	/*************************************************************************************/
	/* FUNCION PARA EJECUTAR LA ACTUALIZACION SOBRE LA BASE DE DATOS
	/*************************************************************************************/
	function update($q, $p) {
		$stmt = $this->db->prepare($q);
		try {
			$resp = $stmt->execute($p);
			$res = $stmt->errorInfo();
			if (isset($res) and isset($res[3]) and intval(Trim($res[3])) <> 0) {
				$txt = date('Y m d h:i:s') . ';Api_Ram.php Usuario:; ' . $_SESSION['usuario'] . '; -- ' .' UPDATE: Error q; ' . $q . '; $res[0] ' .  $res[0] . ' $res[1] ' . $res[1] . ' $res[2] ' .$res[2] . ' $res[3] ' . $res[3];
				logErr($txt,'../logs/logs.txt');
				return false;
			} else {
				return true;
			}	
		} catch (\Throwable $th) {
			// Capturar excepciones de PDO (error de base de datos)
			$txt = date('Y m d h:i:s') . 'Api_Ram.php	Usuario:; ' . $_SESSION['usuario'] .'; - Error Catch UPDATE PDOException; ' . $th->getMessage() . ' QUERY ' .$q;
			logErr($txt,'../logs/logs.txt');
			return false;
		}		
	}

	/*************************************************************************************/
	/* FUNCION PARA EJECUTAR LA ACTUALIZACION SOBRE LA BASE DE DATOS
	/*************************************************************************************/
	function insert($q, $p) {
		$stmt = $this->db->prepare($q);
		try {
			$resp = $stmt->execute($p);
			$res = $stmt->errorInfo();
			if (isset($res) and isset($res[3]) and intval(Trim($res[3])) <> 0) {
				$txt = date('Y m d h:i:s') . ';Api_Ram.php Usuario:; ' . $_SESSION['usuario'] . '; -- ' .' UPDATE: Error q; ' . $q . '; $res[0] ' .  $res[0] . ' $res[1] ' . $res[1] . ' $res[2] ' .$res[2] . ' $res[3] ' . $res[3];
				logErr($txt,'../logs/logs.txt');
				return false;
			} else {
				return $this->db->lastInsertId();
			}	
		} catch (\Throwable $th) {
			// Capturar excepciones de PDO (error de base de datos)
			$txt = date('Y m d h:i:s') . 'Api_Ram.php	Usuario:; ' . $_SESSION['usuario'] .'; - Error Catch UPDATE PDOException; ' . $th->getMessage() . ' QUERY ' .$q;
			logErr($txt,'../logs/logs.txt');
			return false;
		}		
	}


	/*************************************************************************************/
	/* FUNCION PARA RECUPERAR EL APODERADO LEGAL DEL PORTAL DE APODERADOS 
	/*************************************************************************************/
	protected function getApoderadoAPI($col) {
		$query = "SELECT ID_ColegiacionAPL, Nombre_Apoderado_Legal, Identidad, Email, Telefono, Direccion FROM [IHTT_DB].[dbo].[TB_Apoderado_Legal] WHERE ID_ColegiacionAPL = :IDCOL";
		$p = array(":IDCOL" => $col);
		if(!isset($_POST["echo"])){
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

	protected function getDatosPorOmision(){
		$datos[1]= $this->getEntregaUbicacion();
		$datos[2]= $this->getDepartamentos();
		if ($datos[1] != false && $datos[2] != false) {
			$datos[0] = count($datos[1]);
			echo json_encode($datos);
		} else {
			echo json_encode(array("error" =>1001,"errormsg" =>'ALGO RARO SUCEDIO RECUPERANDO LOS DATOS DE UBICACIONES Y DEPARTAMENTOS, INTENTELO DE NUEVO. SI EL PERSISTE CONTACTE AL ADMINISTRADOR DEL SISTEMA'));
		}
	}

	protected function getDepartamentos(){
		$q = "SELECT ID_Departamento as value, DESC_Departamento as text FROM [IHTT_SELD].[dbo].TB_Departamento";
		if (!isset($_POST["echo"])) {
			return $this->select($q,array());
		} else {
			echo json_encode($this->select($q,array()));
		}
	}

	protected function getMunicipios($ID_Departamento){
		$q = "SELECT ID_Municipio as value, DESC_Municipio as text FROM [IHTT_SELD].[dbo].TB_Municipio where ID_Departamento = :ID_Departamento";
		if (!isset($_POST["echo"])) {
			return $this->select($q,array(':ID_Departamento'=> $ID_Departamento));
		} else {
			echo json_encode($this->select($q,array(':ID_Departamento'=> $ID_Departamento)));
		}
	}

	protected function getAldeas($ID_Municipio){
		$q = "SELECT ID_Aldea as value, DESC_Aldea as text FROM [IHTT_PREFORMA].[dbo].[TB_Aldea] where ID_Municipio = :ID_Municipio";
		if (!isset($_POST["echo"])) {
			return $this->select($q,array(':ID_Municipio'=> $ID_Municipio));
		} else {
			echo json_encode($this->select($q,array(':ID_Municipio'=> $ID_Municipio)));
		}
	}

	
	protected function getEntregaUbicacion($filtro=null) {
		$q = "SELECT ID_Ubicacion as value, DESC_Ubicacion as text FROM IHTT_DB.dbo.TB_Entrega_Ubicaciones   " . ($filtro ? "WHERE ID_Tipo_Solicitante = $filtro " : "") . "  order by DESC_Ubicacion ";
		if (!isset($_POST["echo"])) {
			return $this->select($q,array());
		} else {
			echo json_encode($this->select($q,array()));
		}
	}

	protected function getTipoSolicitante($filtro=null) {
		$q="SELECT * FROM [IHTT_SELD].[dbo].[TB_Tipo_Solicitante]  " . ($filtro ? "WHERE ID_Tipo_Solicitante = $filtro " : "") . "  order by DESC_Solicitante ";
		if (!isset($_POST["echo"])) {
			return $this->select($q,array());
		} else {
			echo json_encode($this->select($q,array()));
		}		
	}

	protected function getColor($filtro=null) {
		$q = "SELECT ID_Color as value, DESC_Color as text FROM [IHTT_SGCERP].[dbo].[TB_Color_Vehiculos] " . ($filtro ? "WHERE ID_Color = $filtro " : "") . "  order by DESC_Color ";
		if (!isset($_POST["echo"])) {
			return $this->select($q,array());
		} else {
			echo json_encode($this->select($q,array()));
		}
	 }

	 protected function getMarca($filtro=null) {
		$q = "SELECT ID_Marca as value, DESC_Marca as text FROM [IHTT_SGCERP].[dbo].[TB_Marca_Vehiculo] " . ($filtro ? "WHERE ID_Marca = $filtro " : "") . "  order by DESC_Marca ";
		if (!isset($_POST["echo"])) {
			return $this->select($q,array());
		} else {
			echo json_encode($this->select($q,array()));
		}
	 }

	 protected function getAnios() {
		for ($i = 1946; $i <= (date("Y")+1); $i++) {
			$datos[] = array("value" => $i, "text" => $i);
		}
		if (!isset($_POST["echo"])) {
			return $datos;
		} else {
			echo json_encode($datos);
		}
	 }	 

	 protected function getAreaOperacion($filtro=null) {
		$q = "SELECT [ID] as value, [DESC_Area_Operacion] as text FROM [IHTT_DB].[dbo].[TB_Area_Operacion] " . ($filtro ? "WHERE ID = $filtro " : "") . " order by [DESC_Area_Operacion]";
		if (!isset($_POST["echo"])) {
			return $this->select($q,array());
		} else {
			echo json_encode($this->select($q,array()));
		}
	 }

	 
	 protected function getCategoriaEspecilizadaCarga($filtro=null) {
		$q = "SELECT [ID_Clase_Servicio] as value, [DESC_Tipo] as text FROM [IHTT_DB].[dbo].[TB_Tipo_Categoria] " . ($filtro ? "WHERE ID = $filtro " : "") . " order by [DESC_Tipo]";
		if (!isset($_POST["echo"])) {
			return $this->select($q,array());
		} else {
			echo json_encode($this->select($q,array()));
		}
	 }

	 
	 protected function getUnidad($tabla,$campo_filtro,$filtro,$campos) {
		$q = "SELECT " . $campos .  " FROM ". $tabla . $campo_filtro . " = :ID_Vehiculo"; 
		if (!isset($_POST["echo"])) {
			return $this->select($q,array(":ID_Vehiculo"=> $filtro));
		} else {
			echo json_encode($this->select($q,array()));
		}

	 }	 

	/*************************************************************************************/
	/* FUNCION PARA RECUPERAR Aldea, Departamento y Municipio
	/*************************************************************************************/
	protected function ALDEASDEPARTAMENTO($col) {
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
	protected function getSolicitante() {
		$query = "SELECT a.*,b.DESC_Solicitante,b.ID_Tipo_Solicitante FROM ihtt_preforma.dbo.v_Datos_Solicitante a,[IHTT_SELD].[dbo].[TB_Tipo_Solicitante] b WHERE a.CodigoSolicitanteTipo = b.ID_Tipo_Solicitante and a.ID_Solicitante = :IDSOL";
		$p = array(":IDSOL" => $_POST["idSolicitante"]);
		$data = $this->select($query, $p );
		$datos[0]= count($data);
		if (count($data)>0) {
			$Aldeas = $this->ALDEASDEPARTAMENTO($data[0]["Aldea"]);
			$datos[1] = array('DESC_Solicitante' =>$data[0]["DESC_Solicitante"],'ID_Tipo_Solicitante' =>$data[0]["ID_Tipo_Solicitante"],
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
			$datos[3] = $this->getMunicipios(isset($Aldeas['Departamento']) ? $Aldeas['Departamento'] : 0);
			$datos[4] = $this->getAldeas(isset($Aldeas['Municipio']) ? $Aldeas['Municipio'] : 0);
		}
		echo json_encode($datos);
	}

	protected function getAvisodeCobroxPlaca($Numero_Placa) {
		$row_rs_stmt['error'] = false;	
		$respuesta[0]['errorcode'] = '';
		try {
			$query_rs_stmt = "SELECT [Numero_Placa],[MONTO]
			FROM [IHTT_Webservice].[dbo].[TB_AvisoCobroEnc] ENC,[IHTT_Webservice].[dbo].[TB_AvisoCobroDET] DET, [IHTT_DB].[dbo].[TB_Tramite] TR
			WHERE ENC.AvisoCobroEstado = 2 AND ENC.CodigoAvisoCobro = DET.CodigoAvisoCobro AND DET.CodigoTipoTramite = TR.[ID_Tramite] AND TR.[ID_Tipo_Tramite] = 'IHTTTRA-03' AND (TR.[ID_Clase_Tramite] = 'CLATRA-15' OR TR.[ID_Clase_Tramite] = 'CLATRA-08') AND [Numero_Placa] = :Numero_Placa";
			// Recueprando la información del stmt
			$stmt = $this->db->prepare($query_rs_stmt);
			$stmt->execute(Array(':Numero_Placa' => $Numero_Placa));
			$row_rs_stmt = $stmt->fetch();
			$res = $stmt->errorInfo();
			if (isset($res) and isset($res[3]) and intval(Trim($res[3])) <> 0) {
				$row_rs_stmt['error'] = true;
				$respuesta[0]['errorcode'] = $res[1];
				$row_rs_stmt['msg'] = "Mensaje de Error: " . $res[0] . ' ' . $res[1] . ' ' . $res[2] . ' ' . $res[3] . ' ' ;
				$txt = date('Y m d h:i:s') . '	' .'Api_Ram.php getAvisodeCobroxPlaca.php Error '. $query_rs_stmt  . ' res[0]' . $res[0] . ' res[1]' .$res[1] . ' res[2]' .$res[2]. ' res[3]' .$res[3];
				logErr($txt,'../logs/logs.txt');
			}
		} catch (\Throwable $th) {
			$row_rs_stmt['error'] = true;
			$respuesta[0]['errorcode'] = 0;
			$row_rs_stmt['msg'] = "Mensaje de Error: " . $th->getMessage();
			$txt = date('Y m d h:i:s') . '	' .'Api_Ram.php getAvisodeCobroxPlaca.php catch '. $query_rs_stmt . ' ERROR ' . $th->getMessage();
			logErr($txt,'../logs/logs.txt');
		}	
		return $row_rs_stmt;
	}

	protected function getTipoTramiteyClaseTramite ($filtro=Array(),$ID_Categoria) {
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
			$html = $html . '<div class="row"><div class="col-md-1"></div><div class="col-md-9"><strong>TRAMITE</strong></div><div class="col-md-2"><strong>PLACA</strong></div></div>';
			$rows = ($this->select($q,array(':ID_Categoria' => $ID_Categoria)));
			foreach ($rows as $row) {
				if ($row['Acronimo_Clase'] ==  'CU' || $row['Acronimo_Clase'] ==  'CL') {
					$html = $html . '<div class="row border border-info" id="row_tramite_'  . $row['Acronimo_Tramite'] . '_' . $row['Acronimo_Clase']  .  '"><div class="col-md-1"><input data-monto="' . $row['Monto'] . '" class="form-check-input" onclick="fReviewCheck(this)" ' .  " id=" . $row['ID_CHECK']  .  ' type="checkbox" name="tramites[]" value="'.$row["ID_Tramite"].'"></div><div id="descripcion_'. $row["ID_Tramite"] .'" class="col-md-9">'.$row["descripcion_larga"].'</div><div class="col-md-2"><input onchange="getVehiculoDesdeIP(this);"  style="display:none;text-transform: uppercase;" id="concesion_tramite_placa_' .$row['Acronimo_Clase']. '" title="La placa debe contener los primeros 3 digitos alfa y los últimos 4 numericos, máximo 7 caracteres" pattern="^[A-Z]{3}\d{4}$" placeholder="PLACA" class="form-control form-control-sm test-controls" minlength="7" maxlength="7"></div></div>';
				} else {
					$html = $html . '<div class="row border border-info" id="row_tramite_'  . $row['Acronimo_Tramite'] . '_' . $row['Acronimo_Clase']  .  '"><div class="col-md-1"><input data-monto="' . $row['Monto'] . '" class="form-check-input" onclick="fReviewCheck(this)" ' .  " id=" . $row['ID_CHECK']  .  ' type="checkbox" name="tramites[]" value="'.$row["ID_Tramite"].'"></div><div id="descripcion_'. $row["ID_Tramite"] .'" class="col-md-9">'.$row["descripcion_larga"].'</div><div class="col-md-2">&nbsp;</div></div>';
				}
			}

		} else {
			$html = '<div class="row border border-primary d-flex justify-content-center"><div class="col-md-12"><strong>LISTADO DE TRAMITES</strong></div></div>';
			$html = $html . '<div class="row"><div class="col-md-1"></div><div class="col-md-3"><strong>TRAMITE</strong></div><div class="col-md-2"><strong>PLACA</strong></div><div class="col-md-1"></div><div class="col-md-3"><strong>TRAMITE</strong></div><div class="col-md-2"><strong>PLACA</strong></div></div>';
			$rows = ($this->select($q,array(':ID_Categoria' => $ID_Categoria)));
			$process = 0;
			foreach ($rows as $row) {
				if ($process==0) {
					$html = $html . '<div class="row border border-info" id="row_tramite_'  . $row['Acronimo_Tramite'] . '_' . $row['Acronimo_Clase']  .  '">';
				} 
				if ($row['Acronimo_Clase'] ==  'CU' || $row['Acronimo_Clase'] ==  'CL') {
					$html = $html . '<div id="field1_tramite_'  . $row['Acronimo_Tramite'] . '_' . $row['Acronimo_Clase']  .  '" class="col-md-1"><input data-monto="' . $row['Monto'] . '" class="form-check-input" onclick="fReviewCheck(this)" ' .  " id=" . $row['ID_CHECK']  .  ' type="checkbox" name="tramites[]" value="'.$row["ID_Tramite"].'"></div><div id="descripcion_'. $row["ID_Tramite"] .'" class="col-md-3">'.$row["descripcion_larga"].'</div><div class="col-md-2"><input onchange="getVehiculoDesdeIP(this);"  style="display:none;text-transform: uppercase;" id="concesion_tramite_placa_' .$row['Acronimo_Clase']. '" title="La placa debe contener los primeros 3 digitos alfa y los últimos 4 numericos, máximo 7 caracteres" pattern="^[A-Z]{3}\d{4}$" placeholder="PLACA" class="form-control form-control-sm test-controls" minlength="7" maxlength="7"></div>';
				} else {
					$html = $html . '<div class="col-md-1"><input data-monto="' . $row['Monto'] . '" class="form-check-input" onclick="fReviewCheck(this)" ' .  " id=" . $row['ID_CHECK']  .  ' type="checkbox" name="tramites[]" value="'.$row["ID_Tramite"].'"></div><div id="descripcion_'. $row["ID_Tramite"] .'" class="col-md-3">'.$row["descripcion_larga"].'</div><div class="col-md-2">&nbsp;</div>';
				}
				$process++;
				if ($process == 2) {
					$html = $html . '</div>';
					$process = 0;
				   }
			}

			if ($process==1) {
				$html = $html . '<div class="col-md-1"></div><div class="col-md-3"></div><div class="col-md-2"></div></div>';
			}
		}					

		if (!isset($_POST["echo"])) {
			return $html;
		} else {
			echo json_encode($html);
		}	

	}

	protected function getColorByDesc($DescColor) {
		$respuesta[0]['msg'] = '';
		$respuesta[0]['errorcode'] = 0;
		$respuesta[0]['error'] = false;
		// Recueprando la información del color
		$query_rs_color = "SELECT ID_Color,DESC_Color FROM [IHTT_SGCERP].[dbo].[TB_Color_Vehiculos] where DESC_Color = :DESC_Color";
		try {
			$color = $this->db->prepare($query_rs_color);
			$color->execute(Array(':DESC_Color' => $DescColor));
			$res = $color->errorInfo();
			if (isset($res) and isset($res[3]) and intval(Trim($res[3])) <> 0) {
				$respuesta[0]['error'] = true;
				$respuesta[0]['errorcode'] = $res[1];
				$respuesta[0]['msg'] = "Mensaje de Error: " . $res[0] . ' ' . $res[1] . ' ' . $res[2] . ' ' . $res[3] . ' ' ;
				$txt = date('Y m d h:i:s') . '	' .'getColor.php Error '. $query_rs_color  . ' res[0]' . $res[0] . ' res[1]' .$res[1] . ' res[2]' .$res[2]. ' res[3]' .$res[3];
				logErr($txt,'logs/logs.txt');            
			} else {
				$respuesta[1]['data'] = $color->fetch();
			}        
		} catch (\Throwable $th) {
			$respuesta[0]['msg'] = $th->getMessage();
			$respuesta[0]['errorcode'] = -1;
			$respuesta[0]['error'] = true;
			$txt = date('Y m d h:i:s') . '	' .'getColor.php catch '. $query_rs_color . ' ERROR ' . $th->getMessage();
			logErr($txt,'logs/logs.txt');
		}	
		return $respuesta;
	}	

	protected function getMarcaByDesc($DescMarca) {
		$respuesta[0]['msg'] = '';
		$respuesta[0]['errorcode'] = 0;
		$respuesta[0]['error'] = false;
		// Recueprando la información de la marca
		$query_rs_Marca = "SELECT ID_Marca,DESC_Marca FROM [IHTT_SGCERP].[dbo].[TB_Marca_Vehiculo] where DESC_Marca = :DESC_Marca";
		try {
			$marca = $this->db->prepare($query_rs_Marca);
			$marca->execute(Array(':DESC_Marca' => $DescMarca));
			$res = $marca->errorInfo();
			if (isset($res) and isset($res[3]) and intval(Trim($res[3])) <> 0) {
				$respuesta[0]['error'] = true;
				$respuesta[0]['errorcode'] = $res[1];
				$respuesta[0]['msg'] = "Mensaje de Error: " . $res[0] . ' ' . $res[1] . ' ' . $res[2] . ' ' . $res[3] . ' ' ;
				$txt = date('Y m d h:i:s') . '	' .'getMarca.php Error '. $query_rs_Marca  . ' res[0]' . $res[0] . ' res[1]' .$res[1] . ' res[2]' .$res[2]. ' res[3]' .$res[3];
				logErr($txt,'logs/logs.txt');
			} else {
				$respuesta[1]['data'] = $marca->fetch();
			}        
		} catch (\Throwable $th) {
			$respuesta[0]['msg'] = $th->getMessage();
			$respuesta[0]['errorcode'] = -1;
			$respuesta[0]['error'] = true;
			$txt = date('Y m d h:i:s') . '	' .'getMarca.php catch '. $query_rs_Marca . ' ERROR ' . $th->getMessage();
			logErr($txt,'logs/logs.txt');
		}	
		return $respuesta;
	}	

	/*****************************************************************************************/
	/* Funcion que hace el llamado al IP para recupera la información de la unidad
	/*****************************************************************************************/
	protected function file_contents($path) {
		try {
			$str = @file_get_contents($path);
			if ($str === FALSE) {
				//$txt = date('Y m d h:i:s') . ';  ERROR CATCH 408 LLAMANDO; ' . $path . ";Cannot access to read contents. Favor ingrese el tramite de replaqueo y los datos del vehiculo manualmente, SI APLICA";
				//logErr($txt,'../logs/logs-ip.txt');
				$vehiculo['mensaje']='Conexión con el IP no accessible en este momento, favor ingrese el tramite de replaqueo y los datos del vehiculo manualmente, si aplica. (CODIGO DE MSG-:408)';
				$vehiculo['codigo']=408;
				return json_encode($vehiculo);
			} else {
				return $str;
			}
		} catch (Exception $e) {
			//$txt = date('Y m d h:i:s') . '; ERROR CATCH 407 LLAMANDO; ' . $path . '; MSG ERROR;' . $e->getMessage();
			//logErr($txt,'../logs/logs-ip.txt');
			$vehiculo['mensaje']='Conexión con el IP no accesible en este momento, favor ingrese el tramite de replaqueo y los datos del vehiculo manualmente, si aplica. (CODIGO DE MSG-:407) ' . $e->getMessage();
			$vehiculo['codigo']=407;
			return json_encode($vehiculo);
		}
	}
	
	/*****************************************************************************************/
	/* INICIO FUNCION PARA RECUPERAR LA INFORMACIÓN DE LA UNIDAD DEL INSTITUO DE LA PROPIEDAD
	/*****************************************************************************************/
	protected function getDatosUnidadDesdeIP($placa) {
		$vehiculo = json_decode($this->file_contents($_SESSION["appcfg_Dominio_Raiz"].":184/api/Unidad/ConsultarDatosIP/".$placa));
		//Recuperando el codigo de la marca del vehiculo
		if (isset($vehiculo->codigo) == true && $vehiculo->codigo == 200) {
			$marca = $this->getMarcaByDesc($vehiculo->cargaUtil->marca);
			if (isset($marca[0]['error']) == true && $marca[0]['error'] == false){
				if ($marca[1]['data']['ID_Marca'] != ''){
					$vehiculo->cargaUtil->marcacodigo = $marca[1]['data']['ID_Marca'];
				} else {
					$vehiculo->cargaUtil->marcacodigo = '';
				}
			}
			//Recuperando el codigo del color de vehiculo
			
			$color = $this->getColorByDesc($vehiculo->cargaUtil->color);
			if (isset($color[0]['error']) == true && $color[0]['error'] == false){
				if ($color[1]['data']['ID_Color'] != ''){
					$vehiculo->cargaUtil->colorcodigo = $color[1]['data']['ID_Color'];
				} else {
					$vehiculo->cargaUtil->colorcodigo = '';
				}
			}
			//**********************************************************************************************************************/
			// Recuperando las multas del vehiculo
			//**********************************************************************************************************************/
			if ($_POST["action"] == "get-vehiculo") {
				$vehiculo->cargaUtil->Multas = $this->getDatosMulta($vehiculo->cargaUtil->placa,$vehiculo->cargaUtil->placaAnterior);		
				$vehiculo->cargaUtil->Preformas = $this->validarEnPreforma($vehiculo->cargaUtil->placa, $vehiculo->cargaUtil->placaAnterior,$_POST["Concesion"]);		
			}
		}
		return $vehiculo;
	}

	/*************************************************************************************/
	/* FUNCION PARA RECUPERAR LA CONCEPCION ACTUAL DEL VEHICULO Y SI NECESITA RENOVACION
	/* POR CUENTOS PERIODOS DE TIEMPO
	/*************************************************************************************/
	protected function procesarFechaDeVencimiento ($record,$id_clase_servico) {
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
			$Nueva_Fecha_Expiracion = date('Y-m-d',strtotime($record["Fecha_Expiracion"]));
			$hoyplus60 = date('Y-m-d', strtotime('+60 days'));
			$contadorconcesion=0;
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
				$contadorpermisoexplotacion=0;
				while ($Nueva_Fecha_Expiracion <= $hoyplus60) {
					$record['renperexp'][$contadorpermisoexplotacion]['periodo'] = ' del ' . $Nueva_Fecha_Expiracion;
					$Nueva_Fecha_Expiracion = date("Y-m-d",strtotime($Nueva_Fecha_Expiracion."+ 12 years"));
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
	protected function getDatosMulta($placa,$placa_anterior):mixed {
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
		$parametros = array(":Placa_Actual" => $placa,":Placa_Anterior" => $placa_anterior);
		$row = $this->select($query,$parametros);
		if (count($row) > 0 ) {
			$titulos = [0 => 'ID MULTA',1  => 'FECHA MULTA',2=>'PROPIETARIO UNIDAD',3=>'IDENTIFICACIÓN',4=>'CONCESIONARIO',5=>'PLACA',6=>'MONTO',
			'Multa' => 'ID MULTA','FECHA MULTA'  => 'FECHA MULTA','PROPIETARIO UNIDAD'=>'PROPIETARIO UNIDAD','IDENTIFICACION'=>'IDENTIFICACIÓN','CONCESIONARIO'=>'CONCESIONARIO','PPLACA'=>'PLACA','MONTO'=>'MONTO'];
			$row[count($row)+1] = $titulos;
		}
		return $row;
	}	
	/*************************************************************************************/
	/* FUNCION PARA RECUPERAR EL SOLICITANTE
	/*************************************************************************************/
	protected function getConcesion() {

		$query = "select * from IHTT_SGCERP.dbo.v_Listado_General WHERE N_Certificado = :N_Certificado and RTN_Concesionario = :RTN_Concesionario";
		$p = array(":N_Certificado" => $_POST["Concesion"],":RTN_Concesionario" => $_POST["RTN_Concesionario"]);
		$data = $this->select($query, $p );
		$datos[0]= count($data);

		if ($datos[0]>0) {
			$data[0]["Marcas"] = $this->getMarca();
			$data[0]["Anios"] = $this->getAnios();
			$data[0]["Colores"] = $this->getColor();	
			$data[0]["Tipo_Categoria_Especilizada"] = '';
			$data[0]["Desc_Categoria_Especilizada"] = '';
			$data[0]["ID_Area_Operacion"] = '';
			$data[0]["Desc_Area_Operacion"] = '';			
			$record["Fecha_Expiracion"] = $data[0]["Fecha Vencimiento Certificado"];
			$record["Fecha_Expiracion_Explotacion"] = $data[0]["Fecha Vencimiento Permiso"];
			$data[0]["Vencimientos"] = $this->procesarFechaDeVencimiento ($record,$data[0]["ID_Clase_Servico"])[1];
			$data[0]["Unidad"][0]['ID_Marca'] = '';
			$data[0]["Unidad"][0]['ID_Color'] = '';
			if ($data[0]["Clase Servicio"] == 'STEC') {
				$data[0]["Link"] = $_SESSION["appcfg_Dominio_Raiz"].":172/api_rep.php?action=get-PDFPermisoEsp-Carga&PermisoEspecial=".$data[0]["CertificadoEncriptado"];
				$data[0]["Vista"] = file_get_contents("vistas/pes_carga.html");
				$data[0]["Unidad"] = $this->getUnidad("[IHTT_SGCERP].[DBO].[TB_Vehiculo_Transporte_Carga] v,IHTT_SGCERP.[DBO].[TB_Vehiculo_Transporte_Carga_x_Placa] p,IHTT_SGCERP.[DBO].[TB_Tipo_Vehiculo_Transporte_Carga] t where v.ID_Vehiculo_Carga = p.ID_Vehiculo_Carga and v.ID_Tipo_Vehiculo_Carga = t.ID_Tipo_Vehiculo_Carga and p.Estado = 'ACTIVA' and "," v.ID_Vehiculo_Carga ",$data[0]["ID_Vehiculo"]," t.DESC_Tipo_Vehiculo,* ");
				if ($data && $data[0] && $data[0]["Unidad"] && $data[0]["Unidad"][0]['ID_Placa']) {
					$vehiculo = $this->getDatosUnidadDesdeIP($data[0]["Unidad"][0]['ID_Placa']);
					//******************************************************************************************/
					// Guardando el codigo de respuesta de la solicutud al IP
					//******************************************************************************************/
					if (!isset($vehiculo->codigo)){
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
						$data[0]["Unidad"][0]['Multas'] = $this->getDatosMulta($vehiculo->cargaUtil->placa,$vehiculo->cargaUtil->placaAnterior);	
						$data[0]["Unidad"][0]['Preforma'] = $this->validarEnPreforma($vehiculo->cargaUtil->placa, $vehiculo->cargaUtil->placaAnterior,$_POST["Concesion"]);
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

						if (strtoupper($vehiculo->cargaUtil->estadoVehiculo) == 'NO BLOQUEADO'){
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
				$data[0]["Tramites"] = $this->getTipoTramiteyClaseTramite(['PS','CU','CL','CM','CC','CS','X'],$data[0]["Categoria"]);
				$tipo = $this->getCategoriaEspecilizadaCarga();
				if (count($tipo)>0) {
					$data[0]["Tipo_Categoria_Especilizada"] = $tipo[0]['value'];
					$data[0]["Desc_Categoria_Especilizada"] = $tipo[0]['text'];
				}
			} else {
				if ($data[0]["Clase Servicio"] == 'STEP') {
					$data[0]["Tipo_Concesion"] = 'PERMISO ESPECIAL:';
					$data[0]["Tramites"] = $this->getTipoTramiteyClaseTramite(['PS','CU','CL','CM','CC','CS','X'],$data[0]["Categoria"]);
					$area = $this->getAreaOperacion();
					if (count($area)>0) {
						$data[0]["Tipo_Categoria_Especilizada"] = $area[0]['value'];
						$data[0]["Desc_Categoria_Especilizada"] = $area[0]['text'];
					}
					$data[0]["Link"] = $_SESSION["appcfg_Dominio_Raiz"].":172/api_rep.php?action=get-PDFPermisoEsp-Pas&PermisoEspecial=".$data[0]["CertificadoEncriptado"];
					$data[0]["Vista"] = file_get_contents("vistas/pes_pasajero.html");
					$data[0]["Unidad"] = $this->getUnidad("[IHTT_SGCERP].[DBO].[TB_Vehiculo_Transporte_Pasajero] v,IHTT_SGCERP.[DBO].[TB_Vehiculo_Transporte_Pasajero_x_Placa] p,IHTT_SGCERP.[DBO].[TB_Tipo_Vehiculo_Transporte_Pasajero] t where v.ID_Vehiculo_Transporte = p.ID_Vehiculo_Transporte and v.ID_Tipo_Vehiculo_Transporte_Pas = t.ID_Tipo_Vehiculo_Transporte_Pas and p.Estado = 'ACTIVA' and "," v.ID_Vehiculo_Transporte ",$data[0]["ID_Vehiculo"]," DESC_Tipo_Vehiculo_Transporte_Pas as DESC_Tipo_Vehiculo,* ");					
					if ($data && $data[0] && $data[0]["Unidad"] && $data[0]["Unidad"][0]['ID_Placa']) {
						$vehiculo = $this->getDatosUnidadDesdeIP($data[0]["Unidad"][0]['ID_Placa']);
						$data[0]["Unidad"][0]['estaPagadoElCambiodePlaca'] = false;
						//******************************************************************************************/
						// Guardando el codigo de respuesta de la solicutud al IP
						//******************************************************************************************/
						if (!isset($vehiculo->codigo)){
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
							$data[0]["Unidad"][0]['Multas'] = $this->getDatosMulta($vehiculo->cargaUtil->placa,$vehiculo->cargaUtil->placaAnterior);
							$data[0]["Unidad"][0]['Preforma'] = $this->validarEnPreforma($vehiculo->cargaUtil->placa, $vehiculo->cargaUtil->placaAnterior,$_POST["Concesion"]);					
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
							if (strtoupper($vehiculo->cargaUtil->estadoVehiculo) == 'NO BLOQUEADO'){
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
						$data[0]["Tramites"] = $this->getTipoTramiteyClaseTramite(['PE','CO','CL','CM','CC','CS','PE','CU'],$data[0]["Categoria"]);
						$tipo = $this->getCategoriaEspecilizadaCarga($data[0]["Id_Tipo_Categoria"]);
						if (count($tipo)>0) {
							$data[0]["Tipo_Categoria_Especilizada"] = $tipo[0]['value'];
							$data[0]["Desc_Categoria_Especilizada"] = $tipo[0]['text'];
						}
						// Pendiente de ir a trae el certificado de explotación
						//$data[0]["Link1"] = "https://satt2.transporte.gob.hn:172/api_rep.php?action=get-PDFPermisoExp-Carga&Permiso=".$data[0]["PerExpEncriptado"];
						$data[0]["Link"] = $_SESSION["appcfg_Dominio_Raiz"].":172/api_rep.php?action=get-PDFCertificado-Carga&Certificado=".$data[0]["CertificadoEncriptado"];
						$data[0]["Vista"] = file_get_contents("vistas/certificado_carga.html");
						$data[0]["Unidad"] = $this->getUnidad("[IHTT_SGCERP].[DBO].[TB_Vehiculo_Transporte_Carga] v,IHTT_SGCERP.[DBO].[TB_Vehiculo_Transporte_Carga_x_Placa] p,IHTT_SGCERP.[DBO].[TB_Tipo_Vehiculo_Transporte_Carga] t where v.ID_Vehiculo_Carga = p.ID_Vehiculo_Carga and v.ID_Tipo_Vehiculo_Carga = t.ID_Tipo_Vehiculo_Carga and p.Estado = 'ACTIVA' and"," v.ID_Vehiculo_Carga ",$data[0]["ID_Vehiculo"]," t.DESC_Tipo_Vehiculo,* ");					
						if ($data && $data[0] && $data[0]["Unidad"] && $data[0]["Unidad"][0]['ID_Placa']) {
							$vehiculo = $this->getDatosUnidadDesdeIP($data[0]["Unidad"][0]['ID_Placa']);
							$data[0]["Unidad"][0]['estaPagadoElCambiodePlaca'] = false;
							//******************************************************************************************/
							// Guardando el codigo de respuesta de la solicutud al IP
							//******************************************************************************************/
							if (!isset($vehiculo->codigo)){
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
								$data[0]["Unidad"][0]['Multas'] = $this->getDatosMulta($vehiculo->cargaUtil->placa,$vehiculo->cargaUtil->placaAnterior);	
								$data[0]["Unidad"][0]['Preforma'] = $this->validarEnPreforma($vehiculo->cargaUtil->placa, $vehiculo->cargaUtil->placaAnterior,$_POST["Concesion"]);				
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
								if (strtoupper($vehiculo->cargaUtil->estadoVehiculo) == 'NO BLOQUEADO'){
									$data[0]["Unidad"][0]['Bloqueado'] = false;
								} else {
									$data[0]["Unidad"][0]['Bloqueado'] = true;
								}

							}
						} else {
							//$data[0]["Unidad_IP"] = $vehiculo;
							$data[0]["Tipo_Concesion"] = 'CERTIFICADO DE OPERACIÓN:';
							$data[0]["Tramites"] = $this->getTipoTramiteyClaseTramite(['PE','CO','CL','CM','CC','CS','PE','CU'],$data[0]["Categoria"]);
							//$data[0]["Link1"] = "https://satt2.transporte.gob.hn:172/api_rep.php?action=get-PDFPermisoExp-Pas&Permiso=".$data[0]["PerExpEncriptado"];
							$data[0]["Link"] = $_SESSION["appcfg_Dominio_Raiz"].":172/api_rep.php?action=get-PDFCertificado&Certificado=".$data[0]["CertificadoEncriptado"];
							$data[0]["Vista"] = file_get_contents("vistas/certificado_pasajero.html");
							$data[0]["Unidad"] = $this->getUnidad("[IHTT_SGCERP].[DBO].[TB_Vehiculo_Transporte_Pasajero] v,IHTT_SGCERP.[DBO].[TB_Vehiculo_Transporte_Pasajero_x_Placa] p,IHTT_SGCERP.[DBO].[TB_Tipo_Vehiculo_Transporte_Pasajero] t where v.ID_Vehiculo_Transporte = p.ID_Vehiculo_Transporte and v.ID_Tipo_Vehiculo_Transporte_Pas = t.ID_Tipo_Vehiculo_Transporte_Pas and p.Estado = 'ACTIVA' and "," v.ID_Vehiculo_Transporte ",$data[0]["ID_Vehiculo"]," DESC_Tipo_Vehiculo_Transporte_Pas as DESC_Tipo_Vehiculo,* ");											
							if ($data && $data[0] && $data[0]["Unidad"] && $data[0]["Unidad"][0]['ID_Placa']) {
								$vehiculo = $this->getDatosUnidadDesdeIP($data[0]["Unidad"][0]['ID_Placa']);
								$data[0]["Unidad"][0]['estaPagadoElCambiodePlaca'] = false;
								//******************************************************************************************/
								// Guardando el codigo de respuesta de la solicutud al IP
								//******************************************************************************************/
								if (!isset($vehiculo->codigo)){
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
									$data[0]["Unidad"][0]['Multas'] = $this->getDatosMulta($vehiculo->cargaUtil->placa,$vehiculo->cargaUtil->placaAnterior);		
									$data[0]["Unidad"][0]['Preforma'] = $this->validarEnPreforma($vehiculo->cargaUtil->placa, $vehiculo->cargaUtil->placaAnterior,$_POST["Concesion"]);		
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
									if (strtoupper($vehiculo->cargaUtil->estadoVehiculo) == 'NO BLOQUEADO'){
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
			$datos[1]=$data;
		}
		echo json_encode($datos);
	}

	protected function getUsuarioAsigna() {
		//**************************************************************************//
		//RTBM rbthaofic@gmail.com 2022/08/15                                       //
		//**************************************************************************//
		// Aqui se usa el codigo de renovación automatica para definir porque       //
		// proceso se debe ir cuando es renovación automatica                      //
		//**************************************************************************//
		// Se usa el proceso 4 para renovaciones automaticas
		//**************************************************************************//
		$p=array();
		if($_SESSION["Es_Renovacion_Automatica"] == false) {
			return $this->select("SELECT TOP 1 Codigo_Usuario, Nombre_Usuario, COUNT ( Nombre_Usuario ) AS Preformas_Asignadas FROM ( SELECT A.Codigo_Usuario AS Codigo_Usuario, A.Usuario_Nombre AS Nombre_Usuario FROM [IHTT_DB].[dbo].[TB_GEA_X_Abogados_Asignados] A WHERE Preforma11 = 1 UNION ALL SELECT A.Codigo_Usuario_Acepta AS Codigo_Usuario, A.Usuario_Acepta AS Nombre_Usuario FROM [IHTT_PREFORMA].[dbo].[TB_Solicitante] A WHERE CONVERT ( DATE, Sistema_Fecha ) = CONVERT ( DATE, GETDATE()) AND Codigo_Usuario_Acepta IN ( SELECT Codigo_Usuario FROM [IHTT_DB].[dbo].[TB_GEA_X_Abogados_Asignados] WHERE Preforma11 = 1)) AS b GROUP BY Nombre_Usuario, Codigo_Usuario ORDER BY Preformas_Asignadas ASC",$p);
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
			GROUP BY Nombre_Usuario, Codigo_Usuario ORDER BY Preformas_Asignadas ASC",$p);
		} 
		
	}

	protected function crearCarpeta ($RAM){
		try {
			$dirE = "Documentos/".$RAM;
			if (!is_dir($dirE)) {
				if (!mkdir($dirE, 0777, true)) {
					$response['msgLog'] = "Fallo la creación del directorio: $dirE";
					$response['msg'] = 'Algo inesperado sucedio creando el directorio';
					$response['error'] = true;	
					$txt = date('Y m d h:i:s') . 'Api_Ram.php	crearCarpeta:; ' . $_SESSION['usuario'] .'; - Error ; ' . "Fallo la creación del directorio: $dirE";
					logErr($txt,'../logs/logs.txt');
					return false;
				}
			}
			return true;
		} catch (Exception $e) {
			// Handle the exception
			$response['msgLog'] = 'Caught Exception: '.  $e->getMessage(). "\n";
			$response['msg'] = 'Algo inesperado sucedio creando el directorio';
			$response['error'] = false;
			$txt = date('Y m d h:i:s') . 'Api_Ram.php	crearCarpeta:; ' . $_SESSION['usuario'] .'; - Error ; ' . $e->getMessage() . "\n";
			logErr($txt,'../logs/logs.txt');			
			return false;
		}
	}

	/**************************************************************************************/
	/*  Valida que la placa no este asignada a una concesion que este con tramites        */
	/*  pendientes en preforma al igual valida que la concesion no este con                                              */
	/**************************************************************************************/
	protected function validarEnPreforma($ID_Placa,$ID_Placa_Antes_Replaqueo,$Concesion):mixed {
		/**************************************************************************************/
		/*  CAMBIOS HECHOS RBTHAOFIC@GMAIL.COM 2022/11/17                                    */
		/*  vALIDAR QUE FECHA ACTUAL SEA MENOR O IGUAL A LA FECHA DE VENCIMIENTO             */
		/*  CAMBIOS HECHOS RBTHAOFIC@GMAIL.COM 2023/12/14                                    */
		/*  VALIDAR QUE [N_Certificado] != :N_Certificado y                                  */
		/*  FECHA ACTUAL SEA MAYOR O IGUAL A LA FECHA DE VENCIMIENTO                         */
		/************************************************************************************/
		$query="SELECT DISTINCT S.ID_Formulario_Solicitud,L.N_Certificado,L.Permiso_Explotacion,l.N_Permiso_Especial,
				S.Sistema_Fecha,A.Nombre_Apoderado_Legal,ID_Colegiacion 
				FROM [IHTT_PREFORMA].[dbo].[TB_SOLICITANTE] S, [IHTT_PREFORMA].[dbo].[TB_Apoderado_Legal] A,[IHTT_PREFORMA].[dbo].[TB_SOLICITUD] L, [IHTT_PREFORMA].[dbo].[TB_Vehiculo] V
				WHERE S.Estado_Formulario in ('IDE-1','IDE-7') AND
				S.ID_Formulario_Solicitud = A.ID_Formulario_Solicitud AND 
				S.ID_Formulario_Solicitud = L.ID_Formulario_Solicitud AND 
				L.ID_Formulario_Solicitud = V.ID_Formulario_Solicitud AND 
				V.Estado IN ('NORMAL','ENTRA') AND
				((L.N_Certificado = :N_Certificado and L.N_Certificado != '')  OR 
				(L.N_Permiso_Especial = :N_Permiso_Especial and L.N_Permiso_Especial != '')  OR 
				V.ID_Placa = :ID_Placa or  
				v.ID_Placa_Antes_Replaqueo = :ID_Placa_Antes_Replaqueo);";
		$parametros=array(":N_Certificado"=> $Concesion,":N_Permiso_Especial"=> $Concesion,":ID_Placa"=> $ID_Placa,":ID_Placa_Antes_Replaqueo"=> $ID_Placa_Antes_Replaqueo);
		$row = $this->select($query,$parametros);
		if (count($row) >0 ) {
			$titulos = [0 => 'RAM',1  => 'CERTIFICADO OPERAC',2=>'PER EXP',3=>'PER ESPECIAL',4=>'FECHA',5=>'APODERADO',6=>'CAH No.',
			'ID Formulario Solicitud' => 'ID Formulario Solicitud','Certificado Operación'  => 'Certificado Operación','Permiso de Explotacion'=>'Permiso de Explotacion','Permiso Especial'=>'Permiso Especial','Sistema Fecha'=>'Sistema Fecha','Nombre Apoderado Legal'=>'Nombre Apoderado Legal','CAH No. Carnet'=>'CAH No. Carnet'];
			$row[count($row)+1] = $titulos;
		}
		return $row;
	}

	/**************************************************************************************/
	/*  Valida que la placa no este asignada a una concesion vigente diferente de         */
	/*  de la que estamos tratando de salvar                                              */
	/**************************************************************************************/
	protected function validarPlaca($placa,$placa_anterior,$concesion):mixed{
		/**************************************************************************************/
		/*  CAMBIOS HECHOS RBTHAOFIC@GMAIL.COM 2022/11/17                                    */
		/*  vALIDAR QUE FECHA ACTUAL SEA MENOR O IGUAL A LA FECHA DE VENCIMIENTO             */
		/*  CAMBIOS HECHOS RBTHAOFIC@GMAIL.COM 2023/12/14                                    */
		/*  VALIDAR QUE [N_Certificado] != :N_Certificado y                                  */
		/*  FECHA ACTUAL SEA MAYOR O IGUAL A LA FECHA DE VENCIMIENTO                         */
		/************************************************************************************/
		$query="SELECT * FROM [IHTT_SGCERP].[dbo].[v_Validacion_Placas] 
		WHERE [N_Certificado] != :Concesion and  
		Fecha_Expiracion >= CONVERT(CHAR(8), GETDATE(), 112)  AND ID_Estado IN ('ES-02','ES-04') AND (ID_Placa = :Placa or ID_Placa = :Placa_Anterior)";
		$parametros=array(":Concesion"=> $concesion,":Placa"=> $placa,":Placa_Anterior"=> $placa_anterior);
		return $this->select($query,$parametros);
	}

	//*******************************************************************************************************************/
	// Obtener la ciudad 
	//*******************************************************************************************************************/
	protected function getCiudad ($ID_Empleado){
		//***********************************************************************************************************/
		// rbthaofic@gmail.com 2023/03/04 Pendiente de finalización (recuperar el area del empleados)
		// Inicio: Agregar usuario que realiza la acción y la ciudad donde se ubica el usuario
		//********************************************************************************************************* */
		$respuesta[0]['msg'] = '';
		$respuesta[0]['errorcode'] = 0;
		$respuesta[0]['error'] = false;
		// Recueprando la información de la marca
		$query = "SELECT Ciu.Codigo_Ciudad,Ciu.Acronimo
		FROM [IHTT_RRHH].[dbo].[TB_Empleados] Emp, [IHTT_RRHH].[dbo].[TB_Ciudades] ciu
		where emp.Codigo_Ciudad = ciu.Codigo_Ciudad and ID_Empleado = :ID_Empleado";
		$p=array(":ID_Empleado"=> $ID_Empleado);
		return $this->select($query, $p);
	}

	//*******************************************************************************************************************/
	// Actualizando el registro de secuencia
	//*******************************************************************************************************************/
	protected function updateSiguienteNumeroRAM ($query,$numero_actual){
		$p=array(":numero_actual"=> $numero_actual,":usuario_modificacion"=> $_SESSION["user_name"],":ip_modificacion"=> $this->getIp(),":host_modificacion"=> $this->getHost());
		return $this->update($query, $p);		
	}

	//*******************************************************************************************************************/
	// Obteniendo el siguiente numero de secuencia
	//*******************************************************************************************************************/
	protected function getSiguienteNumeroRAM ($record,$recordRango){
		if (($record['usaRangos'] == 0 and $record['numero_final'] > $record['numero_actual']) || ($record['usaRangos'] == 1 and $recordRango['numero_final'] > $recordRango['numero_actual'])) {
			$response['error'] = false;
			//*******************************************************************************************************************/
			// Calculando el siguiente numero en la secuencia y aramando el query que toca para actualizar la sencuencia
			//*******************************************************************************************************************/
			if ($record['usaRangos'] == 0) {
				$response['numero_actual']=$record['numero_actual']+1;
				$query = "update [IHTT_RENOVACIONES_AUTOMATICAS].[DBO].[TB_Secuencias] set numero_actual=:numero_actual,usuario_modificacion=:usuario_modificacion,ip_modificacion=:ip_modificacion,host_modificacion=:host_modificacion  WHERE ID = " . htmlentities($record['id']);
			} else {
				$response['numero_actual']=$recordRango['numero_actual']+1;
				$query = "update [IHTT_RENOVACIONES_AUTOMATICAS].[DBO].[TB_Secuencias_Rango] set numero_actual=:numero_actual,usuario_modificacion=:usuario_modificacion,ip_modificacion=:ip_modificacion,host_modificacion=:host_modificacion WHERE ID =  ". htmlentities($recordRango['id']); 
			}
			//*******************************************************************************************************************/
			// Armando el prefijo con el año , mes y/o dia si es el caso
			//*******************************************************************************************************************/			
			$prefijo = str_replace("date('Y')", date('Y'), trim($record['prefijo']));
			$prefijo = str_replace("date('m')", date('m'), trim($prefijo));
			$prefijo = str_replace("date('d')", date('d'), trim($prefijo));			
			//*******************************************************************************************************************/
			// Armando el sufijo con el año , mes y/o dia si es el caso
			//*******************************************************************************************************************/			
			$sufijo = str_replace("date('Y')", date('Y'), trim($record['sufijo']));
			$sufijo = str_replace("date('m')", date('m'), trim($sufijo));
			$sufijo = str_replace("date('d')", date('d'), trim($sufijo));
			//*******************************************************************************************************************/			
			// Llamando funcion de Update el siguiente número en la secuencia
			//*******************************************************************************************************************/			
			$responseUpdateSiguienteNumeroRAM = $this->updateSiguienteNumeroRAM ($query,$response['numero_actual']);
			//*******************************************************************************************************************/			
			// Sino se presento ningun error al momento de actualizar el registro de secuencias
			//*******************************************************************************************************************/			
			if (trim($responseUpdateSiguienteNumeroRAM) == true) {
				//*******************************************************************************************************************/			
				// Armando el siguiente numero de RAM
				//*******************************************************************************************************************/			
				$response['nuevo_numero'] = trim($prefijo). (substr((str_repeat($record['caracter_de_relleno'], $record['tamaño_numero'])).$response['numero_actual'],(-1 * $record['tamaño_numero']))) . trim($sufijo);
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

	protected function getSiguienteRAM ($ID_Secuencia){
		$query = "select * from [IHTT_RENOVACIONES_AUTOMATICAS].[DBO].[TB_Secuencias] WITH (UPDLOCK) WHERE ID = :ID_Secuencia";
		$p=array(":ID_Secuencia"=> $ID_Secuencia);
		$record = $this->select($query, $p);
		if (is_array($record) == true) {
			if ($record[0]['usaRangos'] == 1) {
				$query = "select * from [IHTT_RENOVACIONES_AUTOMATICAS].[DBO].[TB_Secuencias_Rango] WITH (UPDLOCK) WHERE ID = :ID_Secuencia and fecha_final >= CAST(:fecha_final as DATE)";
				$p=array(":ID_Secuencia"=> $ID_Secuencia,":fecha_final" => DATE('Y/m/d'));
				$recordRango = $this->select($query, $p);
				if (is_array($record) == true) {
					return $this->getSiguienteNumeroRAM($record[0],$recordRango[0]);
				} else {
					$response['ok'] = false;
					$response['msg'] = 'YA NO HAY RANGO VALIDO PARA LA FECHA ACTUAL';
					return $response;
				}
			} else {
				return $this->getSiguienteNumeroRAM($record[0],$record[0]);
			}
		} else {
			$response['ok'] = false;
			$response['msg'] = 'YA NO HAY RANGO VALIDO';
			return $response;
		}
	}

	protected function saveSolicitante($Concesion,$Apoderado,$Solicitante,$row_ciudad,$RAM){
		$HASH = hash('SHA512', '%^4#09+-~@%&zfg' . $RAM . date('m/d/Y h:i:s a', time()),false);
		$query="INSERT INTO [IHTT_PREFORMA].[dbo].[TB_Solicitante] 
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
		$parametros=array(
		":Es_Renovacion_Automatica"=>$_SESSION["Es_Renovacion_Automatica"],
		":Originado_En_Ventanilla"=>$_SESSION["Originado_En_Ventanilla"],
		":Usuario_Creacion"=>$_SESSION["user_name"],
		":Codigo_Ciudad"=>$row_ciudad[0]['Codigo_Ciudad'],
		":ID_Formulario_Solicitud"=>$RAM,
		":ID_Formulario_Solicitud_Encrypted"=>$HASH,
		":Nombre_Solicitante"=>strtoupper($Solicitante['Nombre']),
		":ID_Tipo_Solicitante"=>$Solicitante['Tipo_Solicitante'],
		":RTN_Solicitante"=>$Solicitante['RTN'],
		":Domicilo_Solicitante"=>strtoupper($Solicitante['Domicilio']),
		":Denominacion_Social"=>strtoupper($Solicitante['Denominacion']),":ID_Aldea"=>$Solicitante['Aldea'],
		":Telefono_Solicitante"=>$Solicitante['Telefono'],
		":Email_Solicitante"=>$Solicitante['Email'],
		":Numero_Escritura"=>'',
		":RTN_Notario"=>'',
		":Notario_Autorizante"=>'',
		":Lugar_Constitucion"=>'',
		":Fecha_Constitucion"=>'1900-01-01',
		":Estado_Formulario"=>'IDE-7',
		":Fecha_Cancelacion"=>null,
		":Observaciones"=>strtoupper(''),
		":Usuario_Cancelacion"=>'',
		":Presentacion_Documentos"=>$Apoderado['Tipo_Presentacion'],
		":Etapa_Preforma"=>1,
		":Usuario_Acepta"=>$_SESSION["user_name"],//$row_usuario_asigna[0]["Nombre_Usuario"], 
		":Codigo_Usuario_Acepta"=>$_SESSION["ID_Usuario"],//$row_usuario_asigna[0]["Codigo_Usuario"],
		":Tipo_Solicitud"=> $Concesion['esCarga'] = true ? 'CARGA' : 'PASAJEROS', ":Entrega_Ubicacion"=>$Apoderado['Lugar_Entrega']);
		$id = $this->insert($query, $parametros);
		$isOk = ['ID_Solicitante' => $id, 'HASH' => $HASH];	
		return $isOk;
	}

	protected function saveApoderado($RAM,$Apoderado){
		$query="INSERT INTO [IHTT_PREFORMA].[dbo].[TB_Apoderado_Legal]
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
		$parametros=array(
		":ID_Formulario_Solicitud"=>$RAM,
		":Nombre_Apoderado_Legal"=>strtoupper($Apoderado['Nombre']),
		":Ident_Apoderado_Legal"=>$Apoderado['RTN'],
		":ID_Colegiacion"=>$Apoderado['Numero_Colegiacion'],
		":Direccion_Apoderado_Legal"=>strtoupper($Apoderado['Direccion']),
		":Telefono_Apoderado_Legal"=>$Apoderado['Telefono'],
		":Email_Apoderado_Legal"=>strtoupper($Apoderado['Email']));
		return $this->insert($query, $parametros);		
	}

	protected function saveUnidad($RAM,$Unidad,$Concesion,$Estado){
		$query="INSERT INTO [IHTT_PREFORMA].[dbo].[TB_Vehiculo] (
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
		$parametros=array(
		":ID_Formulario_Solicitud"=>$RAM,
		":RTN_Propietario"=>$Unidad['RTN_Propietario'],
		":Nombre_Propietario"=>strtoupper($Unidad['Nombre_Propietario']),
		":ID_Placa"=>strtoupper($Unidad['Placa']),
		":ID_Marca"=>$Unidad['Marca'],
		":Anio"=>$Unidad['Anio'],
		":Modelo"=>strtoupper($Unidad['Modelo']),
		":Tipo_Vehiculo"=>strtoupper($Unidad['Tipo']),
		":ID_Color"=>$Unidad['Color'],
		":Motor"=>strtoupper($Unidad['Motor']),
		":Chasis"=>strtoupper($Unidad['Serie']),
		":VIN"=>strtoupper($Unidad['VIN']),
		":Combustible"=>strtoupper($Unidad['Combustible']),
		":Alto"=>$Unidad['Alto'],
		":Ancho"=>$Unidad['Ancho'],
		":Largo"=>$Unidad['Largo'],
		":Capacidad_Carga"=> $Unidad['Capacidad'],
		":Peso_Unidad"=> 0,
		":Permiso_Explotacion"=>strtoupper($Concesion['Permiso_Explotacion']),
		":Certificado_Operacion"=>strtoupper($Concesion['Certificado']),
		":Permiso_Especial"=>strtoupper($Concesion['Permiso_Especial']),		
		":Estado"=>$Estado,
		":ID_Placa_Antes_Replaqueo"=>strtoupper($Unidad['ID_Placa_Antes_Replaqueo']),
		":Sistema_Usuario"=>$_SESSION["user_name"]);
		return $this->insert($query,$parametros);		
	}

	protected function saveTramites($RAM,$Tramites,$Unidad,$Concesion) {
		$isOk = Array();
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
		for ($i = 0; $i < $contador; $i++){
			$parametros = array(
			":ID_Formulario_Solicitud"=>$RAM,
			":ID_Tramite"=>$Tramites[$i]['Codigo'],
			":ID_Modalidad"=>$Tramites[$i]['ID_Modalidad'],
			":ID_TIpo_Servicio"=>$Tramites[$i]['ID_Tipo_Servicio'],
			":N_Certificado"=>$_POST['Concesion']['Certificado'],
			":Permiso_Explotacion"=>$_POST['Concesion']['Permiso_Explotacion'],
			":Sistema_IP"=>$this->getIp(),
			":ID_Tipo_Categoria"=>$Tramites[$i]['ID_Categoria'],
			":N_Permiso_Especial"=>$_POST['Concesion']['Permiso_Especial'],
			":Tipo_Servicio"=>$Tramites[$i]['ID_Tipo_Servicio'],
			":Es_Renovacion_Automatica"=>$_SESSION["Es_Renovacion_Automatica"],
			":Originado_En_Ventanilla"=>$_SESSION["Originado_En_Ventanilla"],
			":Sistema_Usuario"=>$_SESSION["user_name"]);	
			$isOk[$i] = ['ID' => $this->insert($query,$parametros), 'ID_Compuesto' => $Tramites[$i]['ID_Compuesto']];
			if ($isOk[$i]['ID'] == false) {
				$this->db->rollback();
				unset($isOk);
				$isOk = Array();
				$isOk[0] = false;
				break;
			}
		}
		return $isOk;
	}

	protected function saveBitacora($RAM,$Evento,$Etapa){
		//Insert a la tabla de Bitacora_Preforma
		$query="INSERT INTO [IHTT_PREFORMA].[dbo].[TB_Bitacora_Movimiento_Preformas] 
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
		$parametros=array(
		":ID_Preforma"=>$RAM,
		":Evento"=>$Evento,
		":Etapa"=>$Etapa,
		":Sistema_Usuario"=>$_SESSION["user_name"]);
		return $this->insert($query,$parametros);		
	}

	protected function savePreforma(){
		// BANDERA DE ERROR
		$ERROR = false;
		// Start a transaction
		$this->db->beginTransaction();
		//*******************************************************************************************************************/
		// Inicio Decodificando los json recibidos
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
			$responseValidarMultas = $this->getDatosMulta($_POST["Unidad"]['Placa'],$_POST["Unidad"]['ID_Placa_Antes_Replaqueo']);					
			$responseValidarMultas1 = $this->getDatosMulta($_POST["Unidad1"]['Placa'],$_POST["Unidad1"]['ID_Placa_Antes_Replaqueo']);	
			$responseValidarPlacas = $this->validarPlaca($_POST["Unidad1"]['Placa'],$_POST["Unidad1"]['ID_Placa_Antes_Replaqueo'],isset($_POST["Concesion"]['esCertificado']) ? $_POST["Concesion"]['Certificado'] : $_POST["Concesion"]['Permiso_Especial']);
			$responseValidarPreforma = $this->validarEnPreforma($_POST["Unidad1"]['Placa'],$_POST["Unidad1"]['ID_Placa_Antes_Replaqueo'],isset($_POST["Concesion"]['esCertificado']) ? $_POST["Concesion"]['Certificado'] : $_POST["Concesion"]['Permiso_Especial']);					
		} else {
			//*******************************************************************************************************************/
			// Inicio: NO NO NO es Cambio de Unidad
			//*******************************************************************************************************************/			
			$responseValidarPlacas = $this->validarPlaca($_POST["Unidad"]['Placa'],$_POST["Unidad"]['ID_Placa_Antes_Replaqueo'],($_POST["Concesion"]['esCertificado']) ? $_POST["Concesion"]['Certificado'] : $_POST["Concesion"]['Permiso_Especial']);
			$responseValidarMultas = $this->getDatosMulta($_POST["Unidad"]['Placa'],$_POST["Unidad"]['ID_Placa_Antes_Replaqueo']);					
			$responseValidarPreforma = $this->validarEnPreforma($_POST["Unidad"]['Placa'],$_POST["Unidad"]['ID_Placa_Antes_Replaqueo'],isset($_POST["Concesion"]['esCertificado']) ? $_POST["Concesion"]['Certificado'] : $_POST["Concesion"]['Permiso_Especial']);			
		}

		if ($_POST["Concesion"]['RAM'] == '') {
			$responseValidarUsuario = $this->getUsuarioAsigna();
			if ($responseValidarUsuario == false){
				echo 'responseValidarUsuario == false';
			}
			$responseValidarCiudad = $this->getCiudad($_SESSION["ID_Usuario"]);
			if ($responseValidarCiudad == false){
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

		if  ($RAM == false or 
			((isset($responseValidarUsuario)   and $responseValidarUsuario  == false  and is_array($responseValidarUsuario) == false))    or 
			((isset($responseValidarCiudad)    and $responseValidarCiudad   == false  and is_array($responseValidarCiudad) == false))    or 
			((isset($responseValidarPlacas)    and (isset($responseValidarPlacas[1])    and $responseValidarPlacas[1]    > 0))  or ((isset($responseValidarPlacas)   and $responseValidarPlacas    == false and is_array($responseValidarPlacas) == false)))   or 
			((isset($responseValidarMultas)    and (isset($responseValidarMultas[1])     and $responseValidarMultas[1]    > 0)) or ((isset($responseValidarMultas)   and $responseValidarMultas    == false and is_array($responseValidarMultas) == false)))   or  
			((isset($responseValidarMultas1)   and (isset($responseValidarMultas1[1])    and $responseValidarMultas1[1]   > 0)) or ((isset($responseValidarMultas1)  and $responseValidarMultas1   == false and is_array($responseValidarMultas1) == false)))   or
			((isset($responseValidarPreforma)  and (isset($responseValidarPreforma[1])   and $responseValidarPreforma[1]  > 0)) or ((isset($responseValidarPreforma) and $responseValidarPreforma  == false and is_array($responseValidarPreforma) == false))))  {
			$this->db->rollBack();
			echo json_encode(['ERROR'  =>  true,
							'RAM'  =>  $RAM,
							'Ciudad'      =>  isset($responseValidarCiudad) ? $responseValidarCiudad : '',  
							'Usuario'     =>  isset($responseValidarUsuario) ? $responseValidarUsuario : '',  
							'Placas'      =>  $responseValidarPlacas, 
							'Multas'      =>  $responseValidarMultas, 
							'Multas1'     =>  isset($responseValidarMultas1) ? $responseValidarMultas1 : '',
							'Preforma'   =>   isset($responseValidarPreforma) ? $responseValidarPreforma : '']);
		} else {
			if ($_POST["Concesion"]['RAM'] == '') {
				$isOKSolicitante = $this->saveSolicitante($_POST["Concesion"],$_POST["Apoderado"],$_POST["Solicitante"],$responseValidarCiudad,$RAM['nuevo_numero']);
			} else {
				$isOKSolicitante = $_POST["Solicitante"]['ID_Solicitante'];
			}
			if ($isOKSolicitante == false) {
				$this->db->rollBack();
				echo json_encode(false);	
			} else {
				if ($_POST["Concesion"]['RAM'] == '') {
					$isOKApoderado = $this->saveApoderado($RAM['nuevo_numero'],$_POST["Apoderado"]);
				} else {
					$isOKApoderado = $_POST["Apoderado"]['ID_Apoderado'];
				}
				if ($isOKApoderado == false) {
					$this->db->rollBack();
					echo json_encode(false);	
				} else {
					if ($_POST["Concesion"]['esCambioDeVehiculo'] == true) {
						$isOKUnidad = $this->saveUnidad($RAM['nuevo_numero'],$_POST["Unidad"],$_POST["Concesion"],'SALE');
					} else {
						$isOKUnidad = $this->saveUnidad($RAM['nuevo_numero'],$_POST["Unidad"],$_POST["Concesion"],'NORMAL');
					}
					if ($isOKUnidad == false) {
						$this->db->rollBack();
						echo json_encode(['UNIDAD'  =>  $RAM['nuevo_numero'],'ESTADO'  =>false]);	
					} else {
						if ($_POST["Concesion"]['esCambioDeVehiculo'] == true) {
							$isOKUnidad1 = $this->saveUnidad($RAM['nuevo_numero'],$_POST["Unidad1"],$_POST["Concesion"],'ENTRA');
							if ($isOKUnidad1 == false) {
								$this->db->rollBack();
								echo json_encode(['UNIDAD1'  =>  $RAM['nuevo_numero'],'ESTADO'  =>false]);	
								$ERROR = true;
							}
						}
						if ($ERROR == false) {
							$isOKTramites = $this->saveTramites($RAM['nuevo_numero'],$_POST["Tramites"],$_POST["Unidad"],$_POST["Concesion"]);
							if ($isOKTramites[0] == false) {
								$this->db->rollBack();
								echo json_encode(['TRAMITES'  =>  $RAM['nuevo_numero'],'ESTADO'  =>false]);	
							} else {
								$isOKBitacora = true;
								if ($_POST["Concesion"]['RAM'] == '') {
									$isOKBitacora = $this->saveBitacora($RAM['nuevo_numero'],'INGRESO',1);
								}
								if ($isOKBitacora == false) {
									$this->db->rollBack();
									echo json_encode(['BITACORA'  =>  $RAM['nuevo_numero'],'ESTADO'  =>false]);	
								} else {
									//$this->db->rollBack();
									$this->db->commit();
									echo json_encode(
										['RAM'  =>  $RAM['nuevo_numero'],
										'Usuario_Asigna' =>  isset($responseValidarUsuario) ? $responseValidarUsuario : false,
										'Ciudad'         =>  isset($responseValidarCiudad)  ? $responseValidarCiudad : false, 
										'Solicitante'    =>  isset($isOKSolicitante) ? $isOKSolicitante : false, 
										'Apoderado'      =>  isset($isOKApoderado) ? $isOKApoderado : false, 
										'Unidad'         =>  isset($isOKUnidad) ? $isOKUnidad : false, 
										'Unidad1'        =>  isset($isOKUnidad1) ? $isOKUnidad1 : false, 
										'Tramites'       =>  $isOKTramites, 
										'Bitacora'       =>  isset($isOKBitacora) ? $isOKBitacora : false]
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
	echo json_encode(array("error" =>1000,"errorhead" =>"INICIO DE SESSIÓN","errormsg" =>'NO HAY UNA SESSION INICIADA, FAVOR INICIE SESION Y VUELVA A INTENTARLO'));
} else {
	$api = new Api_Ram($db);	
}