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

	private $Casa=false;

	public function __construct(){
		if(isset($_POST["action"])){
			if ($_POST["action"] == "get-apoderado" && isset($_POST["idApoderado"])) {
				$this->getApoderadoAPI($_POST["idApoderado"]);
			} else if ($_POST["action"] == "get-solicitante" && isset($_POST["idSolicitante"])) {				
				$this->getSolicitante();
			} else if ($_POST["action"] == "get-concesion" && isset($_POST["N_Certificado"])) {				
				$this->getConcesion();				
			} else if ($_POST["action"] == "get-datosporomision") {				
				$this->getDatosPorOmision();				
			} else if ($_POST["action"] == "get-municipios") {				
				$this->getMunicipios($_POST["filtro"]);								
			} else if ($_POST["action"] == "get-aldeas") {				
				$this->getAldeas($_POST["filtro"]);		
			} else if ($_POST["action"] == "get-vehiculo") {				
				echo json_encode($this->getDatosUnidadDesdeIP($_POST["ID_Placa"]));						
			} else if ($_POST["action"] == "get-TipoTramiteyClaseTramite") {				
				$this->getTipoTramiteyClaseTramite();																
			} else { echo json_encode(array("error" =>1001,"errorhead" =>'OPPS',"errormsg" =>'NO SE ENCONTRO NINGUNA FUNCION EN EL API PARA LA ACCIÓN REQUERIDA'));}
		}
	}
	
	function getCasa(){
		return $this->Casa;
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

	function getDatosPorOmision(){
		$datos[1]= $this->getEntregaUbicacion();
		$datos[2]= $this->getDepartamentos();
		if ($datos[1] != false && $datos[2] != false) {
			$datos[0] = count($datos[1]);
			echo json_encode($datos);
		} else {
			echo json_encode(array("error" =>1001,"errormsg" =>'ALGO RARO SUCEDIO RECUPERANDO LOS DATOS DE UBICACIONES Y DEPARTAMENTOS, INTENTELO DE NUEVO. SI EL PERSISTE CONTACTE AL ADMINISTRADOR DEL SISTEMA'));
		}
	}

	function getDepartamentos(){
		$q = "SELECT ID_Departamento as value, DESC_Departamento as text FROM [IHTT_SELD].[dbo].TB_Departamento";
		if (!isset($_POST["echo"])) {
			return $this->select($q,array());
		} else {
			echo json_encode($this->select($q,array()));
		}
	}

	function getMunicipios($ID_Departamento){
		$q = "SELECT ID_Municipio as value, DESC_Municipio as text FROM [IHTT_SELD].[dbo].TB_Municipio where ID_Departamento = :ID_Departamento";
		if (!isset($_POST["echo"])) {
			return $this->select($q,array(':ID_Departamento'=> $ID_Departamento));
		} else {
			echo json_encode($this->select($q,array(':ID_Departamento'=> $ID_Departamento)));
		}
	}

	function getAldeas($ID_Municipio){
		$q = "SELECT ID_Aldea as value, DESC_Aldea as text FROM [IHTT_PREFORMA].[dbo].[TB_Aldea] where ID_Municipio = :ID_Municipio";
		if (!isset($_POST["echo"])) {
			return $this->select($q,array(':ID_Municipio'=> $ID_Municipio));
		} else {
			echo json_encode($this->select($q,array(':ID_Municipio'=> $ID_Municipio)));
		}
	}

	
	function getEntregaUbicacion($filtro=null) {
		$q = "SELECT ID_Ubicacion as value, DESC_Ubicacion as text FROM IHTT_DB.dbo.TB_Entrega_Ubicaciones   " . ($filtro ? "WHERE ID_Tipo_Solicitante = $filtro " : "") . "  order by DESC_Ubicacion ";
		if (!isset($_POST["echo"])) {
			return $this->select($q,array());
		} else {
			echo json_encode($this->select($q,array()));
		}
	 }
	function getTipoSolicitante($filtro=null) {
		$q="SELECT * FROM [IHTT_SELD].[dbo].[TB_Tipo_Solicitante]  " . ($filtro ? "WHERE ID_Tipo_Solicitante = $filtro " : "") . "  order by DESC_Solicitante ";
		if (!isset($_POST["echo"])) {
			return $this->select($q,array());
		} else {
			echo json_encode($this->select($q,array()));
		}		
	}

	function getColor($filtro=null) {
		$q = "SELECT ID_Color as value, DESC_Color as text FROM [IHTT_SGCERP].[dbo].[TB_Color_Vehiculos] " . ($filtro ? "WHERE ID_Color = $filtro " : "") . "  order by DESC_Color ";
		if (!isset($_POST["echo"])) {
			return $this->select($q,array());
		} else {
			echo json_encode($this->select($q,array()));
		}
	 }

	 function getMarca($filtro=null) {
		$q = "SELECT ID_Marca as value, DESC_Marca as text FROM [IHTT_SGCERP].[dbo].[TB_Marca_Vehiculo] " . ($filtro ? "WHERE ID_Marca = $filtro " : "") . "  order by DESC_Marca ";
		if (!isset($_POST["echo"])) {
			return $this->select($q,array());
		} else {
			echo json_encode($this->select($q,array()));
		}
	 }

	 function getAnios() {
		for ($i = 1946; $i <= (date("Y")+1); $i++) {
			$datos[] = array("value" => $i, "text" => $i);
		}
		if (!isset($_POST["echo"])) {
			return $datos;
		} else {
			echo json_encode($datos);
		}
	 }	 

	 function getAreaOperacion($filtro=null) {
		$q = "SELECT [ID] as value, [DESC_Area_Operacion] as text FROM [IHTT_DB].[dbo].[TB_Area_Operacion] " . ($filtro ? "WHERE ID = $filtro " : "") . " order by [DESC_Area_Operacion]";
		if (!isset($_POST["echo"])) {
			return $this->select($q,array());
		} else {
			echo json_encode($this->select($q,array()));
		}
	 }

	 
	 function getCategoriaEspecilizadaCarga($filtro=null) {
		$q = "SELECT [ID_Clase_Servicio] as value, [DESC_Tipo] as text FROM [IHTT_DB].[dbo].[TB_Tipo_Categoria] " . ($filtro ? "WHERE ID = $filtro " : "") . " order by [DESC_Tipo]";
		if (!isset($_POST["echo"])) {
			return $this->select($q,array());
		} else {
			echo json_encode($this->select($q,array()));
		}
	 }

	 
	 function getUnidad($tabla,$campo_filtro,$filtro,$campos) {
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
			$datos[3] = $this->getMunicipios(isset($Aldeas['Departamento']) ? $Aldeas['Departamento'] : 0);
			$datos[4] = $this->getAldeas(isset($Aldeas['Municipio']) ? $Aldeas['Municipio'] : 0);
		}
		echo json_encode($datos);
	}

	function getAvisodeCobroxPlaca($Numero_Placa) {
		global $db;
		$row_rs_stmt['error'] = false;	
		$respuesta[0]['errorcode'] = '';
		try {
			$query_rs_stmt = "SELECT [Numero_Placa],[MONTO]
			FROM [IHTT_Webservice].[dbo].[TB_AvisoCobroEnc] ENC,[IHTT_Webservice].[dbo].[TB_AvisoCobroDET] DET, [IHTT_DB].[dbo].[TB_Tramite] TR
			WHERE ENC.AvisoCobroEstado = 2 AND ENC.CodigoAvisoCobro = DET.CodigoAvisoCobro AND DET.CodigoTipoTramite = TR.[ID_Tramite] AND TR.[ID_Tipo_Tramite] = 'IHTTTRA-03' AND (TR.[ID_Clase_Tramite] = 'CLATRA-15' OR TR.[ID_Clase_Tramite] = 'CLATRA-08') AND [Numero_Placa] = :Numero_Placa";
			// Recueprando la información del stmt
			$stmt = $db->prepare($query_rs_stmt);
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

	function getTipoTramiteyClaseTramite ($filtro=Array()) {
		$joined_string = "'" . implode("', '", $filtro) . "'";
		$q =  "SELECT CONCAT(CONCAT(CONCAT(CONCAT(CONCAT(CONCAT( T.ID_Tipo_Tramite,'_'),C.ID_Clase_Tramite),'_'),T.Acronimo_Tramite),'_'),C.Acronimo_Clase) AS ID_CHECK,
					CONCAT(CONCAT (T.DESC_Tipo_Tramite,' '),C.DESC_Clase_Tramite) as descripcion_larga,
					T.ID_Tipo_Tramite,
					C.ID_Tipo_Tramite as ID_Tipo_Tramite_Array,
					T.DESC_Tipo_Tramite,
					T.Acronimo_Tramite,
					C.ID_Clase_Tramite,
					C.DESC_Clase_Tramite,
					C.Acronimo_Clase
					FROM 
						[IHTT_DB].[DBO].[TB_TIPO_TRAMITE] T
					JOIN 
						[IHTT_DB].[DBO].[TB_CLASE_TRAMITE] C
					ON 
						C.ID_Tipo_Tramite LIKE '%' + CAST(T.ID_Tipo_Tramite AS VARCHAR) + '%'
					WHERE 
						T.Es_Renovacion_Automatica = 1 
						AND C.ID_Tipo_Tramite IS NOT NULL 
						AND C.Acronimo_Clase IN (" . $joined_string . ")						
					ORDER BY 
						T.ID_Tipo_Tramite";
		$html = '<div class="row border border-primary d-flex justify-content-center"><div class="col-md-12"><strong>LISTADO DE TRAMITES</strong></div></div>';
		$html = $html . '<div class="row"><div class="col-md-1"></div><div class="col-md-9"><strong>TRAMITE</strong></div><div class="col-md-2"><strong>PLACA</strong></div></div>';
		$rows = ($this->select($q,array()));
		foreach ($rows as $row) {
			if ($row['Acronimo_Clase'] ==  'CU' || $row['Acronimo_Clase'] ==  'CL') {
				$html = $html . '<div class="row border border-info" id="row_tramite_'  . $row['Acronimo_Tramite'] . '_' . $row['Acronimo_Clase']  .  '"><div class="col-md-1"><input class="form-check-input" onclick="fReviewCheck(this)" ' .  " id=" . $row['ID_CHECK']  .  ' type="checkbox" name="tramites[]" value="'.$row["ID_CHECK"].'"></div><div class="col-md-9">'.$row["descripcion_larga"].'</div><div class="col-md-2"><input onclick="getVehiculoDesdeIP(this);" style="display:none;text-transform: uppercase;" onclick="fGetDataIp()" id="concesion_tramite_placa_' .$row['Acronimo_Clase']. '" title="La placa debe contener los primeros 3 digitos alfa y los últimos 4 numericos, máximo 7 caracteres" pattern="^[A-Z]{3}\d{4}$" placeholder="PLACA" class="form-control form-control-sm test-controls" minlength="7" maxlength="7"></div></div>';
			} else {
				$html = $html . '<div class="row border border-info" id="row_tramite_'  . $row['Acronimo_Tramite'] . '_' . $row['Acronimo_Clase']  .  '"><div class="col-md-1"><input class="form-check-input" onclick="fReviewCheck(this)" ' .  " id=" . $row['ID_CHECK']  .  ' type="checkbox" name="tramites[]" value="'.$row["ID_CHECK"].'"></div><div class="col-md-9">'.$row["descripcion_larga"].'</div><div class="col-md-2">&nbsp;</div></div>';
			}
		}
		if (!isset($_POST["echo"])) {
			return $html;
		} else {
			echo json_encode($html);
		}	
	}

	function getColorByDesc($DescColor) {
		global $db;
		$respuesta[0]['msg'] = '';
		$respuesta[0]['errorcode'] = 0;
		$respuesta[0]['error'] = false;
		// Recueprando la información del color
		$query_rs_color = "SELECT ID_Color,DESC_Color FROM [IHTT_SGCERP].[dbo].[TB_Color_Vehiculos] where DESC_Color = :DESC_Color";
		try {
			$color = $db->prepare($query_rs_color);
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

	function getMarcaByDesc($DescMarca) {
		global $db;
		$respuesta[0]['msg'] = '';
		$respuesta[0]['errorcode'] = 0;
		$respuesta[0]['error'] = false;
		// Recueprando la información de la marca
		$query_rs_Marca = "SELECT ID_Marca,DESC_Marca FROM [IHTT_SGCERP].[dbo].[TB_Marca_Vehiculo] where DESC_Marca = :DESC_Marca";
		try {
			$marca = $db->prepare($query_rs_Marca);
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
	function file_contents($path) {
		try {
			$str = @file_get_contents($path);
			if ($str === FALSE) {
				$txt = date('Y m d h:i:s') . ' ADVERTENCIA 408 LLAMANDO	' . "Cannot access '$path' to read contents. Favor ingrese el tramite de replaqueo y los datos del vehiculo manualmente, SI APLICA";
				logErr($txt,'../logs/logs.txt');
				$vehiculo['mensaje']='Conexión con el IP no accesible en este momento, favor ingrese el tramite de replaqueo y los datos del vehiculo manualmente, si aplica. (CODIGO DE MSG-:408)';
				$vehiculo['codigo']=408;
				return json_encode($vehiculo);
			} else {
				return $str;
			}
		} catch (Exception $e) {
			$txt = date('Y m d h:i:s') . ' ERROR CATCH 407 LLAMANDO ' . $path . ' MSG ERROR' . $e->getMessage();
			logErr($txt,'../logs/logs.txt');
			$vehiculo['mensaje']='Conexión con el IP no accesible en este momento, favor ingrese el tramite de replaqueo y los datos del vehiculo manualmente, si aplica. (CODIGO DE MSG-:407) ' . $e->getMessage();
			$vehiculo['codigo']=407;
			return json_encode($vehiculo);
		}
	}
	
	/*****************************************************************************************/
	/* INICIO FUNCION PARA RECUPERAR LA INFORMACIÓN DE LA UNIDAD DEL INSTITUO DE LA PROPIEDAD
	/*****************************************************************************************/
	function getDatosUnidadDesdeIP($placa) {
		if ($this->getCasa() == false) {
			$vehiculo = json_decode($this->file_contents("https://satt2.transporte.gob.hn:184/api/Unidad/ConsultarDatosIP/".$placa));
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
			}
			return $vehiculo;
		} else{
			return '';
		}
	}

	
	/*************************************************************************************/
	/* FUNCION PARA RECUPERAR EL SOLICITANTE
	/*************************************************************************************/
	function getConcesion() {
		$query = "select * from IHTT_SGCERP.dbo.v_Listado_General WHERE N_Certificado = :N_Certificado and RTN_Concesionario = :RTN_Concesionario";
		$p = array(":N_Certificado" => $_POST["N_Certificado"],":RTN_Concesionario" => $_POST["RTN_Concesionario"]);
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
			if ($data[0]["Clase Servicio"] == 'STEC') {
				$data[0]["Link"] = "https://satt.transporte.gob.hn:172/api_rep.php?action=get-PDFPermisoEsp-Carga&PermisoEspecial=".$data[0]["CertificadoEncriptado"];
				$data[0]["Vista"] = file_get_contents("vistas/pes_carga.html");
				$data[0]["Unidad"] = $this->getUnidad("[IHTT_SGCERP].[DBO].[TB_Vehiculo_Transporte_Carga] v,IHTT_SGCERP.[DBO].[TB_Vehiculo_Transporte_Carga_x_Placa] p,IHTT_SGCERP.[DBO].[TB_Tipo_Vehiculo_Transporte_Carga] t where v.ID_Vehiculo_Carga = p.ID_Vehiculo_Carga and v.ID_Tipo_Vehiculo_Carga = t.ID_Tipo_Vehiculo_Carga and p.Estado = 'ACTIVA' and "," v.ID_Vehiculo_Carga ",$data[0]["ID_Vehiculo"]," t.DESC_Tipo_Vehiculo,* ");
				if ($data && $data[0] && $data[0]["Unidad"] && $data[0]["Unidad"][0]['ID_Placa']) {
					$vehiculo = $this->getDatosUnidadDesdeIP($data[0]["Unidad"][0]['ID_Placa']);
					if (isset($vehiculo->codigo) == true && $vehiculo->codigo == 200) {
						if ($vehiculo->cargaUtil->placa != $vehiculo->cargaUtil->placaAnterior) {
							$row_rs_stmt = $this->getAvisodeCobroxPlaca($vehiculo->cargaUtil->placa);
							if (isset($row_rs_stmt['MONTO'])) {
								$vehiculo->estaPagadoElCambiodePlaca = 'S';
							} else {
								$vehiculo->estaPagadoElCambiodePlaca = 'N';
							}
						}							
						$data[0]["Unidad"][0]['VIN'] = $vehiculo->cargaUtil->vin;
						$data[0]["Unidad"][0]['Motor'] = $vehiculo->cargaUtil->motor;
						$data[0]["Unidad"][0]['Chasis'] = $vehiculo->cargaUtil->chasis;
						if (isset($vehiculo->cargaUtil->marcacodigo)) {
							$data[0]["Unidad"][0]['ID_Marca'] = $vehiculo->cargaUtil->marcacodigo;
						} else {
							$data[0]["Unidad"][0]['ID_Marca'] = '';
						}
						if (isset($vehiculo->cargaUtil->colorcodigo)) {
							$data[0]["Unidad"][0]['ID_Color'] = $vehiculo->cargaUtil->colorcodigo;
						} else {
							$data[0]["Unidad"][0]['ID_Color'] = '';
						}
						$data[0]["Unidad"][0]['Anio'] = $vehiculo->cargaUtil->axo;
						$data[0]["Unidad"][0]['ID_Placa'] = $vehiculo->cargaUtil->placa;
						$data[0]["Unidad"][0]['ID_Placa_Anterior'] = $vehiculo->cargaUtil->placaAnterior;
						$data[0]["Unidad"][0]['Combustible'] = $vehiculo->cargaUtil->combustible;
						$data[0]["Unidad"][0]['Modelo'] = $vehiculo->cargaUtil->modelo;
						$data[0]["Unidad"][0]['Tipo'] = $vehiculo->cargaUtil->tipo;
						$data[0]["Unidad"][0]['Identificacion'] = $vehiculo->cargaUtil->propietario->identificacion;
						$data[0]["Unidad"][0]['Nombre'] = strtoupper($vehiculo->cargaUtil->propietario->nombre);
						$data[0]["Unidad"][0]['Estado_Vehiculo'] = strtoupper($vehiculo->cargaUtil->estadoVehiculo);
						if (strtoupper($vehiculo->cargaUtil->estadoVehiculo) == 'NO BLOQUEADO'){
							$data[0]["Unidad"][0]['Bloqueado'] = false;
						} else {
							$data[0]["Unidad"][0]['Bloqueado'] = true;
						}
					}
					//$data[0]["Unidad_IP"] = $vehiculo;
				} else {
					$data[0]["Unidad_IP"][0] = false; 
				}
				$data[0]["Tipo_Concesion"] = 'PERMISO ESPECIAL:';
				$data[0]["Tramites"] = $this->getTipoTramiteyClaseTramite(['PS','CU','CL','CM','CC','CS','X']);
				$tipo = $this->getCategoriaEspecilizadaCarga();
				if (count($tipo)>0) {
					$data[0]["Tipo_Categoria_Especilizada"] = $tipo[0]['value'];
					$data[0]["Desc_Categoria_Especilizada"] = $tipo[0]['text'];
				}
			} else {
				if ($data[0]["Clase Servicio"] == 'STEP') {
					$data[0]["Link"] = "https://satt.transporte.gob.hn:172/api_rep.php?action=get-PDFPermisoEsp-Pas&PermisoEspecial=".$data[0]["CertificadoEncriptado"];
					$data[0]["Vista"] = file_get_contents("vistas/pes_pasajero.html");
					$data[0]["Unidad"] = $this->getUnidad("[IHTT_SGCERP].[DBO].[TB_Vehiculo_Transporte_Pasajero] v,IHTT_SGCERP.[DBO].[TB_Vehiculo_Transporte_Pasajero_x_Placa] p,IHTT_SGCERP.[DBO].[TB_Tipo_Vehiculo_Transporte_Pasajero] t where v.ID_Vehiculo_Transporte = p.ID_Vehiculo_Transporte and v.ID_Tipo_Vehiculo_Transporte_Pas = t.ID_Tipo_Vehiculo_Transporte_Pas and p.Estado = 'ACTIVA' and "," v.ID_Vehiculo_Transporte ",$data[0]["ID_Vehiculo"]," DESC_Tipo_Vehiculo_Transporte_Pas as DESC_Tipo_Vehiculo,* ");					
					if ($data && $data[0] && $data[0]["Unidad"] && $data[0]["Unidad"][0]['ID_Placa']) {
						$vehiculo = $this->getDatosUnidadDesdeIP($data[0]["Unidad"][0]['ID_Placa']);
						if (isset($vehiculo->codigo) == true && $vehiculo->codigo == 200) {
							if ($vehiculo->cargaUtil->placa != $vehiculo->cargaUtil->placaAnterior) {
								$row_rs_stmt = $this->getAvisodeCobroxPlaca($vehiculo->cargaUtil->placa);
								if (isset($row_rs_stmt['MONTO'])) {
									$vehiculo->estaPagadoElCambiodePlaca = 'S';
								} else {
									$vehiculo->estaPagadoElCambiodePlaca = 'N';
								}
							}							
							$data[0]["Unidad"][0]['VIN'] = $vehiculo->cargaUtil->vin;
							$data[0]["Unidad"][0]['Motor'] = $vehiculo->cargaUtil->motor;
							$data[0]["Unidad"][0]['Chasis'] = $vehiculo->cargaUtil->chasis;
							if (isset($vehiculo->cargaUtil->marcacodigo)) {
								$data[0]["Unidad"][0]['ID_Marca'] = $vehiculo->cargaUtil->marcacodigo;
							} else {
								$data[0]["Unidad"][0]['ID_Marca'] = '';
							}
							if (isset($vehiculo->cargaUtil->colorcodigo)) {
								$data[0]["Unidad"][0]['ID_Color'] = $vehiculo->cargaUtil->colorcodigo;
							} else {
								$data[0]["Unidad"][0]['ID_Color'] = '';
							}
							$data[0]["Unidad"][0]['Anio'] = $vehiculo->cargaUtil->axo;
							$data[0]["Unidad"][0]['ID_Placa'] = $vehiculo->cargaUtil->placa;
							$data[0]["Unidad"][0]['ID_Placa_Anterior'] = $vehiculo->cargaUtil->placaAnterior;
							$data[0]["Unidad"][0]['Combustible'] = $vehiculo->cargaUtil->combustible;
							$data[0]["Unidad"][0]['Modelo'] = $vehiculo->cargaUtil->modelo;
							$data[0]["Unidad"][0]['Tipo'] = $vehiculo->cargaUtil->tipo;
							$data[0]["Unidad"][0]['Identificacion'] = $vehiculo->cargaUtil->propietario->identificacion;
							$data[0]["Unidad"][0]['Nombre'] = strtoupper($vehiculo->cargaUtil->propietario->nombre);
							$data[0]["Unidad"][0]['Estado_Vehiculo'] = strtoupper($vehiculo->cargaUtil->estadoVehiculo);
							if (strtoupper($vehiculo->cargaUtil->estadoVehiculo) == 'NO BLOQUEADO'){
								$data[0]["Unidad"][0]['Bloqueado'] = false;
							} else {
								$data[0]["Unidad"][0]['Bloqueado'] = true;
							}
						}
						//$data[0]["Unidad_IP"] = $vehiculo;
					} else {
						$data[0]["Unidad_IP"][0] = false; 
					}
					$data[0]["Tipo_Concesion"] = 'PERMISO ESPECIAL:';
					$data[0]["Tramites"] = $this->getTipoTramiteyClaseTramite(['PS','CU','CL','CM','CC','CS','X']);
					$area = $this->getAreaOperacion();
					if (count($area)>0) {
						$data[0]["Tipo_Categoria_Especilizada"] = $area[0]['value'];
						$data[0]["Desc_Categoria_Especilizada"] = $area[0]['text'];
					}
				} else {
				if ($data[0]["Clase Servicio"] == 'STPC') {
					$data[0]["Link"] = "https://satt.transporte.gob.hn:172/api_rep.php?action=get-PDFCertificado-Carga&Certificado=".$data[0]["CertificadoEncriptado"];
					$data[0]["Vista"] = file_get_contents("vistas/certificado_carga.html");
					$data[0]["Unidad"] = $this->getUnidad("[IHTT_SGCERP].[DBO].[TB_Vehiculo_Transporte_Carga] v,IHTT_SGCERP.[DBO].[TB_Vehiculo_Transporte_Carga_x_Placa] p,IHTT_SGCERP.[DBO].[TB_Tipo_Vehiculo_Transporte_Carga] t where v.ID_Vehiculo_Carga = p.ID_Vehiculo_Carga and v.ID_Tipo_Vehiculo_Carga = t.ID_Tipo_Vehiculo_Carga and p.Estado = 'ACTIVA' and"," v.ID_Vehiculo_Carga ",$data[0]["ID_Vehiculo"]," t.DESC_Tipo_Vehiculo,* ");					
					if ($data && $data[0] && $data[0]["Unidad"] && $data[0]["Unidad"][0]['ID_Placa']) {
						$vehiculo = $this->getDatosUnidadDesdeIP($data[0]["Unidad"][0]['ID_Placa']);
						if (isset($vehiculo->codigo) == true && $vehiculo->codigo == 200) {
							if ($vehiculo->cargaUtil->placa != $vehiculo->cargaUtil->placaAnterior) {
								$row_rs_stmt = $this->getAvisodeCobroxPlaca($vehiculo->cargaUtil->placa);
								if (isset($row_rs_stmt['MONTO'])) {
									$vehiculo->estaPagadoElCambiodePlaca = 'S';
								} else {
									$vehiculo->estaPagadoElCambiodePlaca = 'N';
								}
							}							
							$data[0]["Unidad"][0]['VIN'] = $vehiculo->cargaUtil->vin;
							$data[0]["Unidad"][0]['Motor'] = $vehiculo->cargaUtil->motor;
							$data[0]["Unidad"][0]['Chasis'] = $vehiculo->cargaUtil->chasis;
							if (isset($vehiculo->cargaUtil->marcacodigo)) {
								$data[0]["Unidad"][0]['ID_Marca'] = $vehiculo->cargaUtil->marcacodigo;
							} else {
								$data[0]["Unidad"][0]['ID_Marca'] = '';
							}
							if (isset($vehiculo->cargaUtil->colorcodigo)) {
								$data[0]["Unidad"][0]['ID_Color'] = $vehiculo->cargaUtil->colorcodigo;
							} else {
								$data[0]["Unidad"][0]['ID_Color'] = '';
							}
							$data[0]["Unidad"][0]['Anio'] = $vehiculo->cargaUtil->axo;
							$data[0]["Unidad"][0]['ID_Placa'] = $vehiculo->cargaUtil->placa;
							$data[0]["Unidad"][0]['ID_Placa_Anterior'] = $vehiculo->cargaUtil->placaAnterior;
							$data[0]["Unidad"][0]['Combustible'] = $vehiculo->cargaUtil->combustible;
							$data[0]["Unidad"][0]['Modelo'] = $vehiculo->cargaUtil->modelo;
							$data[0]["Unidad"][0]['Tipo'] = $vehiculo->cargaUtil->tipo;
							$data[0]["Unidad"][0]['Identificacion'] = $vehiculo->cargaUtil->propietario->identificacion;
							$data[0]["Unidad"][0]['Nombre'] = strtoupper($vehiculo->cargaUtil->propietario->nombre);
							$data[0]["Unidad"][0]['Estado_Vehiculo'] = strtoupper($vehiculo->cargaUtil->estadoVehiculo);
							if (strtoupper($vehiculo->cargaUtil->estadoVehiculo) == 'NO BLOQUEADO'){
								$data[0]["Unidad"][0]['Bloqueado'] = false;
							} else {
								$data[0]["Unidad"][0]['Bloqueado'] = true;
							}
						}
						//$data[0]["Unidad_IP"] = $vehiculo;
						$data[0]["Tipo_Concesion"] = 'CERTIFICADO DE OPERACIÓN:';
						$data[0]["Tramites"] = $this->getTipoTramiteyClaseTramite(['PE','CO','CL','CM','CC','CS','PE','CU']);
						$tipo = $this->getCategoriaEspecilizadaCarga($data[0]["Tipo_Concesion"]);
						if (count($tipo)>0) {
							$data[0]["Tipo_Categoria_Especilizada"] = $tipo[0]['value'];
							$data[0]["Desc_Categoria_Especilizada"] = $tipo[0]['text'];
						}
						$data[0]["Link1"] = "https://satt2.transporte.gob.hn:172/api_rep.php?action=get-PDFPermisoExp-Carga&Permiso=".$data[0]["PerExpEncriptado"];
					} else {
						$data[0]["Link"] = "https://satt.transporte.gob.hn:172/api_rep.php?action=get-PDFCertificado&Certificado=".$data[0]["CertificadoEncriptado"];
						$data[0]["Vista"] = file_get_contents("vistas/certificado_pasajero.html");
						$data[0]["Unidad"] = $this->getUnidad("[IHTT_SGCERP].[DBO].[TB_Vehiculo_Transporte_Pasajero] v,IHTT_SGCERP.[DBO].[TB_Vehiculo_Transporte_Pasajero_x_Placa] p,IHTT_SGCERP.[DBO].[TB_Tipo_Vehiculo_Transporte_Pasajero] t where v.ID_Vehiculo_Transporte = p.ID_Vehiculo_Transporte and v.ID_Tipo_Vehiculo_Transporte_Pas = t.ID_Tipo_Vehiculo_Transporte_Pas and p.Estado = 'ACTIVA' and "," v.ID_Vehiculo_Transporte ",$data[0]["ID_Vehiculo"]," DESC_Tipo_Vehiculo_Transporte_Pas as DESC_Tipo_Vehiculo,* ");											
						if ($data && $data[0] && $data[0]["Unidad"] && $data[0]["Unidad"][0]['ID_Placa']) {
							$vehiculo = $this->getDatosUnidadDesdeIP($data[0]["Unidad"][0]['ID_Placa']);
							if (isset($vehiculo->codigo) == true && $vehiculo->codigo == 200) {
								if ($vehiculo->cargaUtil->placa != $vehiculo->cargaUtil->placaAnterior) {
									$row_rs_stmt = $this->getAvisodeCobroxPlaca($vehiculo->cargaUtil->placa);
									if (isset($row_rs_stmt['MONTO'])) {
										$vehiculo->estaPagadoElCambiodePlaca = 'S';
									} else {
										$vehiculo->estaPagadoElCambiodePlaca = 'N';
									}
								}							
								$data[0]["Unidad"][0]['VIN'] = $vehiculo->cargaUtil->vin;
								$data[0]["Unidad"][0]['Motor'] = $vehiculo->cargaUtil->motor;
								$data[0]["Unidad"][0]['Chasis'] = $vehiculo->cargaUtil->chasis;
								if (isset($vehiculo->cargaUtil->marcacodigo)) {
									$data[0]["Unidad"][0]['ID_Marca'] = $vehiculo->cargaUtil->marcacodigo;
								} else {
									$data[0]["Unidad"][0]['ID_Marca'] = '';
								}
								if (isset($vehiculo->cargaUtil->colorcodigo)) {
									$data[0]["Unidad"][0]['ID_Color'] = $vehiculo->cargaUtil->colorcodigo;
								} else {
									$data[0]["Unidad"][0]['ID_Color'] = '';
								}
								$data[0]["Unidad"][0]['Anio'] = $vehiculo->cargaUtil->axo;
								$data[0]["Unidad"][0]['ID_Placa'] = $vehiculo->cargaUtil->placa;
								$data[0]["Unidad"][0]['ID_Placa_Anterior'] = $vehiculo->cargaUtil->placaAnterior;
								$data[0]["Unidad"][0]['Combustible'] = $vehiculo->cargaUtil->combustible;
								$data[0]["Unidad"][0]['Modelo'] = $vehiculo->cargaUtil->modelo;
								$data[0]["Unidad"][0]['Tipo'] = $vehiculo->cargaUtil->tipo;
								$data[0]["Unidad"][0]['Identificacion'] = $vehiculo->cargaUtil->propietario->identificacion;
								$data[0]["Unidad"][0]['Nombre'] = strtoupper($vehiculo->cargaUtil->propietario->nombre);
								$data[0]["Unidad"][0]['Estado_Vehiculo'] = strtoupper($vehiculo->cargaUtil->estadoVehiculo);
								if (strtoupper($vehiculo->cargaUtil->estadoVehiculo) == 'NO BLOQUEADO'){
									$data[0]["Unidad"][0]['Bloqueado'] = false;
								} else {
									$data[0]["Unidad"][0]['Bloqueado'] = true;
								}
							}
							//$data[0]["Unidad_IP"] = $vehiculo;
						} else {
							$data[0]["Unidad_IP"][0] = false; 
						}
						//$data[0]["Unidad_IP"] = $vehiculo;
						$data[0]["Tipo_Concesion"] = 'CERTIFICADO DE OPERACIÓN:';
						$data[0]["Tramites"] = $this->getTipoTramiteyClaseTramite(['PE','CO','CL','CM','CC','CS','PE','CU']);
						$data[0]["Link1"] = "https://satt2.transporte.gob.hn:172/api_rep.php?action=get-PDFPermisoExp-Pas&Permiso=".$data[0]["PerExpEncriptado"];
					}
				}
			}
			$datos[1]=$data;
		}
		echo json_encode($datos);
	}
	/*************************************************************************************/
	/* FUNCION PARA RECUPERAR LOS TIPOS DE TRAMITES
	/*************************************************************************************/
	function getTipoTramite(){
		if($_POST["Es_Renovacion_Automatica"] == true) {
			$query ="SELECT [IHTT_DB].[dbo].TB_Tipo_Tramite.DESC_Tipo_Tramite, [IHTT_DB].[dbo].TB_Tipo_Tramite.ID_Tipo_Tramite FROM [IHTT_DB].[dbo].TB_Tipo_Tramite 
			INNER JOIN [IHTT_DB].[dbo].TB_Tramite ON [IHTT_DB].[dbo].TB_Tipo_Tramite.ID_Tipo_Tramite = [IHTT_DB].[dbo].TB_Tramite.ID_Tipo_Tramite 
			WHERE TB_Tramite.ID_Categoria=:CAT and [IHTT_DB].[dbo].TB_Tipo_Tramite.Es_Renovacion_Automatica = :Es_Renovacion_Automatica  AND TB_Tramite.ID_Tipo_Tramite NOT IN ( 'IHTTTRA-06', 'IHTTTRA-04', 'IHTTTRA-05', 'IHTTTRA-07', 'IHTTTRA-09', 'IHTTTRA-10', 'IHTTTRA-11', 'IHTTTRA-14', 'IHTTTRA-15', 'IHTTTRA-16') GROUP BY DESC_Tipo_Tramite, TB_Tipo_Tramite.ID_Tipo_Tramite";
			//WHERE TB_Tramite.ID_Categoria=:CAT and [IHTT_DB].[dbo].TB_Tipo_Tramite.Es_Renovacion_Automatica = :Es_Renovacion_Automatica  AND TB_Tramite.ID_Tipo_Tramite NOT IN ( 'IHTTTRA-06', 'IHTTTRA-04', 'IHTTTRA-05', 'IHTTTRA-07', 'IHTTTRA-08', 'IHTTTRA-09', 'IHTTTRA-10', 'IHTTTRA-11', 'IHTTTRA-14', 'IHTTTRA-15', 'IHTTTRA-16') GROUP BY DESC_Tipo_Tramite, TB_Tipo_Tramite.ID_Tipo_Tramite";
			$p = array(":CAT" => $_POST["categoria"],":Es_Renovacion_Automatica" => $_POST["Es_Renovacion_Automatica"]);
			
		} else {
			$query ="SELECT [IHTT_DB].[dbo].TB_Tipo_Tramite.DESC_Tipo_Tramite, [IHTT_DB].[dbo].TB_Tipo_Tramite.ID_Tipo_Tramite FROM [IHTT_DB].[dbo].TB_Tipo_Tramite 
			INNER JOIN [IHTT_DB].[dbo].TB_Tramite ON [IHTT_DB].[dbo].TB_Tipo_Tramite.ID_Tipo_Tramite = [IHTT_DB].[dbo].TB_Tramite.ID_Tipo_Tramite 
			WHERE TB_Tramite.ID_Categoria=:CAT AND TB_Tramite.ID_Tipo_Tramite NOT IN ( 'IHTTTRA-06', 'IHTTTRA-04', 'IHTTTRA-05', 'IHTTTRA-07', 'IHTTTRA-08', 'IHTTTRA-09', 'IHTTTRA-10', 'IHTTTRA-11', 'IHTTTRA-14', 'IHTTTRA-15', 'IHTTTRA-16') GROUP BY DESC_Tipo_Tramite, TB_Tipo_Tramite.ID_Tipo_Tramite";
			$p = array(":CAT" => $_POST["categoria"]);
		}	
		$data = $this->select($query, $p );
		$datos[1]=count($data);
		$datos[0]=array();
			for($i=0;$i<count($data);$i++){
				$datos[0][] = array("TipoTramite" => "<option value='".$data[$i]["ID_Tipo_Tramite"]."' >".$data[$i]["DESC_Tipo_Tramite"]."</option>");
			}
		echo json_encode($datos);
	}

	/*************************************************************************************/
	/* FUNCION PARA RECUPERAR LA CLASE DE TRAMITES
	/*************************************************************************************/
	function getTipoClaseTramite(){
		if($_POST["Es_Renovacion_Automatica"] == true) {
			$query="SELECT [IHTT_DB].[dbo].TB_Clase_Tramite.ID_Clase_Tramite, [IHTT_DB].[dbo].TB_Clase_Tramite.DESC_Clase_Tramite 
			FROM [IHTT_DB].[dbo].TB_Clase_Tramite 
			INNER JOIN [IHTT_DB].[dbo].TB_Tramite ON 
			[IHTT_DB].[dbo].TB_Clase_Tramite.ID_Clase_Tramite = [IHTT_DB].[dbo].TB_Tramite.ID_Clase_Tramite 
			WHERE [IHTT_DB].[dbo].TB_Clase_Tramite.Es_Renovacion_Automatica = 'True' and 
			TB_Tramite.ID_Tipo_Tramite = :TIPO AND 
			TB_Clase_Tramite.ID_Clase_Tramite IN ('CLATRA-01','CLATRA-02','CLATRA-08','CLATRA-03','CLATRA-59','CLATRA-45','CLATRA-15','CLATRA-17','CLATRA-18','CLATRA-19') 
			AND TB_Tramite.ID_Categoria = :CAT 
			--AND TB_Tramite.ID_Tipo_Tramite <> 'IHTTTRA-01' 
			GROUP BY TB_Clase_Tramite.ID_Clase_Tramite, TB_Clase_Tramite.DESC_Clase_Tramite";
		} else {
			$query="SELECT [IHTT_DB].[dbo].TB_Clase_Tramite.ID_Clase_Tramite, [IHTT_DB].[dbo].TB_Clase_Tramite.DESC_Clase_Tramite 
			FROM [IHTT_DB].[dbo].TB_Clase_Tramite 
			INNER JOIN [IHTT_DB].[dbo].TB_Tramite ON 
			[IHTT_DB].[dbo].TB_Clase_Tramite.ID_Clase_Tramite = [IHTT_DB].[dbo].TB_Tramite.ID_Clase_Tramite 
			WHERE TB_Tramite.ID_Tipo_Tramite = :TIPO AND 
			TB_Clase_Tramite.ID_Clase_Tramite IN ('CLATRA-01','CLATRA-02','CLATRA-08','CLATRA-03','CLATRA-59','CLATRA-45','CLATRA-15','CLATRA-17','CLATRA-18','CLATRA-19') 
			AND TB_Tramite.ID_Categoria = :CAT 
			--AND TB_Tramite.ID_Tipo_Tramite <> 'IHTTTRA-01' 
			GROUP BY TB_Clase_Tramite.ID_Clase_Tramite, TB_Clase_Tramite.DESC_Clase_Tramite";
		}
		// SELECT [IHTT_DB].[dbo].TB_Clase_Tramite.ID_Clase_Tramite, [IHTT_DB].[dbo].TB_Clase_Tramite.DESC_Clase_Tramite FROM [IHTT_DB].[dbo].TB_Clase_Tramite INNER JOIN [IHTT_DB].[dbo].TB_Tramite ON [IHTT_DB].[dbo].TB_Clase_Tramite.ID_Clase_Tramite = [IHTT_DB].[dbo].TB_Tramite.ID_Clase_Tramite WHERE TB_Tramite.ID_Tipo_Tramite = :TIPO AND TB_Clase_Tramite.ID_Clase_Tramite IN ('CLATRA-01','CLATRA-02','CLATRA-08','CLATRA-03','CLATRA-59','CLATRA-45','CLATRA-15','CLATRA-17','CLATRA-18','CLATRA-19') AND TB_Tramite.ID_Categoria = :CAT GROUP BY TB_Clase_Tramite.ID_Clase_Tramite, TB_Clase_Tramite.DESC_Clase_Tramite

		$p = array(":TIPO" => $_POST["TipoTramite"],":CAT" => $_POST["categoria"]);
		$data = $this->select($query, $p );
		$datos[1]=count($data);
		$datos[0]=array();
			for($i=0;$i<count($data);$i++){
				$datos[0][] = array("clasetramite" => "<option value='".$data[$i]["ID_Clase_Tramite"]."' >".$data[$i]["DESC_Clase_Tramite"]."</option>");
			}
		echo json_encode($datos);
	}

	function getDatosMulta() {
		if (!$_POST["Cambio_Unidad"]) {
			//$query = "SELECT * FROM [IHTT_MULTAS].[dbo].[V_Multas_IHTT_DGT] WHERE ID_Estado='1' AND Placa= :IDP";
			$query = "SELECT * FROM [IHTT_MULTAS].[dbo].[V_Multas_IHTT_DGT] MUL,[IHTT_Webservice].[dbo].[TB_AvisoCobroEnc] Avi
			left outer join [IHTT_Webservice].[dbo].[TB_ArregloEnc] Enc on Avi.CodigoAvisoCobro  = Enc.ID_Aviso
			WHERE MUL.ID_Estado='1' AND MUL.Multa = avi.ID_Solicitud and 
			(MUL.Placa= :Placa_Actual or MUL.Placa= :Placa_Actual) and
			(
			(
			select count(*) from [IHTT_Webservice].[dbo].[TB_AvisoCobroEnc] Encx where Encx.AvisoCobroEstado = 1 and
			Encx.ID_Solicitud  = Enc.Numero_Arreglo
			) =  0
			or
			(
			select count(*) from [IHTT_Webservice].[dbo].[TB_AvisoCobroEnc] Encx where Encx.AvisoCobroEstado = 1 and
			Encx.ID_Solicitud  = Enc.Numero_Arreglo and Encx.FechaVencimiento < GETDATE()
			) > 0)";
			$p = array(":Placa_Actual" => $_POST["Placa_Actual"],":Placa_Vieja" => $_POST["Placa_Vieja"]);
		} else {
			//$query = "SELECT * FROM [IHTT_MULTAS].[dbo].[V_Multas_IHTT_DGT] WHERE ID_Estado='1' AND Placa= :IDP";
			$query = "SELECT * FROM [IHTT_MULTAS].[dbo].[V_Multas_IHTT_DGT] MUL,[IHTT_Webservice].[dbo].[TB_AvisoCobroEnc] Avi
			left outer join [IHTT_Webservice].[dbo].[TB_ArregloEnc] Enc on Avi.CodigoAvisoCobro  = Enc.ID_Aviso
			WHERE MUL.ID_Estado='1' AND MUL.Multa = avi.ID_Solicitud and 
			(MUL.Placa= :Placa_Actual or MUL.Placa= :Placa_Nueva or MUL.Placa= :Placa_Actual_Entra or MUL.Placa= :Placa_Nueva_Entra) and
			(
			(
			select count(*) from [IHTT_Webservice].[dbo].[TB_AvisoCobroEnc] Encx where Encx.AvisoCobroEstado = 1 and
			Encx.ID_Solicitud  = Enc.Numero_Arreglo
			) =  0
			or
			(
			select count(*) from [IHTT_Webservice].[dbo].[TB_AvisoCobroEnc] Encx where Encx.AvisoCobroEstado = 1 and
			Encx.ID_Solicitud  = Enc.Numero_Arreglo and Encx.FechaVencimiento < GETDATE()
			) > 0)";
			$p = array(":Placa_Actual" => $_POST["Placa_Actual"],":Placa_Nueva" => $_POST["Placa_Vieja"],":Placa_Actual_Entra" => $_POST["Placa_Actual_Entra"],":Placa_Nueva_Entra" => $_POST["Placa_Nueva_Entra"]);
		}
		$data = $this->select($query, $p );
		$datos = array();
		$datos[1] = count($data);
		$datos[0] = $data;
		if(!isset($_POST["echo"])){
			return $datos;
		} else {
			echo json_encode($datos);
		}	
	}

}

if (!isset($_SESSION["ID_Usuario"]) || !isset($_SESSION["user_name"])) {
	echo json_encode(array("error" =>1000,"errorhead" =>'INICIO DE SESSIÓN',"errormsg" =>'NO HAY UNA SESSION INICIADA, FAVOR INICIE SESION Y VUELVA A INTENTARLO'));
} else {
	$api = new Api_Ram();	
}