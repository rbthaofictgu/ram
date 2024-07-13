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

class Api_Ram {
	public function __construct(){
		if(isset($_POST["action"])){
			if ($_POST["action"] == "get-solicitante" && isset($_POST["idsolicitante"])) {
			//***********************************************************************************************/
            //Obtener el solicitante
            //***********************************************************************************************/				
			$this->getSolicitante();
            //***********************************************************************************************/
            //Obtener el Apoderado Legal
            //***********************************************************************************************/                
			}else if ($_POST["action"] == "get-apoderado" && isset($_POST["idApoderado"])) {
			$this->getApoderadoAPI($_POST["idApoderado"]);/// Recupera la información del apoderado legal 
			}
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
				$txt = date('Y m d h:i:s') . '	Usuario: ' . $_SESSION['usuario'] . ' -- ' .'API_MIP.PHP Error Insert: Error q ' . $q . ' $res[0] ' .  $res[0] . ' $res[1] ' . $res[1] . ' $res[2] ' .$res[2] . ' $res[3] ' . $res[3];
				logErr($txt,'logs/logs.txt');
				return false;
			} else {
				return $datos;
			}				
		} catch (PDOException $e) {
			// Capturar excepciones de PDO (error de base de datos)
			echo "Error en la consulta: " . $e->getMessage();
			$txt = date('Y m d h:i:s') . 'Api_Ram.php	Usuario: ' . $_SESSION['usuario'] .' - Error' . $e->getMessage();
			logErr($txt,'logs/logs.txt');
		return false; // O devolver un valor indicando error
		}
	}
	

/*@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
  @@@ FUNCION PARA RECUPERAR LOS DEPARTAMENTOS, MUNICIPIOS Y ALDEA QUE VIAJA POR PLOS PORTALES @@@
  @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@*/
	function ALDEASDEPARTAMENTO($col) {
		$query = "SELECT TB_Departamento.ID_Departamento, TB_Municipio.ID_Municipio, TB_Aldea.ID_Aldea FROM [IHTT_PREFORMA].[dbo].[TB_Departamento] INNER JOIN [IHTT_PREFORMA].[dbo].[TB_Municipio] ON TB_Departamento.ID_Departamento = TB_Municipio.ID_Departamento INNER JOIN [IHTT_PREFORMA].[dbo].[TB_Aldea] ON TB_Municipio.ID_Municipio = TB_Aldea.ID_Municipio WHERE TB_Aldea.ID_Aldea = :IDCOL";
		$p = array(":IDCOL" => $col);
		$data = $this->select($query, $p );
		if ($data != false and count($data)>0) {
		return array("Aldea" =>$data[0]["ID_Aldea"],"Municipio" =>$data[0]["ID_Municipio"],"Departamento" =>$data[0]["ID_Departamento"]);
		} else {
			return array("Aldea" =>'',"Municipio" =>'',"Departamento" =>'');
		}				
	}
	function getSolicitante() {
		global $db;
		//******************************************************************************************************//
		//Inicio Insert de [IHTT_DB].[dbo].[TB_Solicitante_x_Representante_Legal]                                //
		//******************************************************************************************************//
		$respuesta[0]['error'] = false;
		$respuesta[0]['msg']= '';
		//echo 'colegiacion=>' . $_POST["idcolegiacion"] . '<br>';
		//echo 'solicitante=>' . $_POST["idsolicitante"] . '<br>';
		$query = "SELECT ID_Solicitante FROM [IHTT_DB].[dbo].[TB_Apoderado_Legal_X_Solicitante] WHERE ID_ColegiacionAPL = :ID_ColegiacionAPL AND ID_Solicitante = :ID_Solicitante";
		$p = array(":ID_ColegiacionAPL" => $_POST["idcolegiacion"],":ID_Solicitante" => $_POST["idsolicitante"]);
		$ConcxAbog = $db->prepare($query);
		$ConcxAbog->execute($p);
		$row_ConcxAbog = $ConcxAbog->fetch();
		$res = $ConcxAbog->errorInfo();
		if (isset($res) and  isset($res[3]) and intval(Trim($res[3])) <> 0) {
			$respuesta[0]['error'] = true;
			$respuesta[0]['msg'] = "Mensaje de Error SELECT: " . $res[0] . ' ' . $res[1] . ' ' . $res[2] . ' ' . $res[3] . ' ' ;
		}else {
			if (!isset($row_ConcxAbog['ID_Solicitante'])) {
				$query = "INSERT INTO [IHTT_DB].[dbo].[TB_Apoderado_Legal_X_Solicitante] 
				(ID_ColegiacionAPL,ID_Solicitante,Estado,Observacion,SistemaUsuario) VALUES
				(:ID_ColegiacionAPL,:ID_Solicitante,1,'RENOVACIONES AUTOMATICAS EN VENTANILLA','rbarrientos')";
				$ConcxAbog = $db->prepare($query);
				$p = array(":ID_ColegiacionAPL" => $_POST["idcolegiacion"],":ID_Solicitante" => $_POST["idsolicitante"]);
				$ConcxAbog->execute($p);
				$row_ConcxAbog = $ConcxAbog->fetch();
				$row_ConcxAbog = $ConcxAbog->nextRowset();
				$res = $ConcxAbog->errorInfo();
				if (isset($res) and  isset($res[3]) and intval(Trim($res[3])) <> 0) {
					$respuesta[0]['error'] = true;
					$respuesta[0]['msg'] = "Mensaje de Error Insert: " . $res[0] . ' ' . $res[1] . ' ' . $res[2] . ' ' . $res[3] . ' ' ;
				}
			}
		}
		//******************************************************************************************************//
		//Final Insert de [IHTT_DB].[dbo].[TB_Solicitante_x_Representante_Legal]                                //
		//******************************************************************************************************//
		$datos[1]=0;
		if ($respuesta[0]['error'] == false) {
			$query = "SELECT * FROM [IHTT_PREFORMA].[dbo].[v_Datos_Solicitante] WHERE ID_Solicitante = :IDSOL";
			$p = array(":IDSOL" => $_POST["idsolicitante"]);
			$data = $this->select($query, $p );
			$datos[1]=count($data);
			if ($datos[1] > 0) {
				$datos[0]=array();
				$apoderado = $this->getApoderado($_POST["idcolegiacion"]);
				$apoderado = json_decode($apoderado, true);
				$Aldeas = $this->ALDEASDEPARTAMENTO($data[0]["Aldea"]);
				// echo $data[0]["RTNSolicitante"];
				// echo $data[0]["NombreSolicitante"];
				// echo $data[0]["NombreEmpresa"];
				// echo $data[0]["CodigoSolicitanteTipo"];
				// echo $data[0]["Direccion"];
				// echo $data[0]["Telefono"];
				// echo $data[0]["Email"];
				// echo $data[0]["Aldea"];
				// echo $Aldeas['Aldea'];
				// echo $Aldeas['Municipio'];
				// echo $Aldeas['Departamento'];
				// echo $data[0]['ID_Escritura'];
				// echo $data[0]['Fecha_Elaboracion'];
				// echo $data[0]['Lugar_Elaboracion'];
				// echo $data[0]['ID_Notario'];
				// echo $data[0]['Nombre_Notario'];
				// echo $data[0]['ID_Representante_Legal'];
				// echo $data[0]['Nombre_Representante_Legal'];
				// echo $data[0]['Telefono_Representante'];
				// echo $data[0]['Email_Representante'];
				// echo $data[0]['Direccion_Representante'];
				// echo $data[0]['Representante_Escritura'];
				// echo $data[0]['Fecha_Elaboracion_Representante'];
				// echo $data[0]['Lugar_Elaboracion_Representante'];
				// echo $data[0]['Numero_Inscripcion'];
				// echo $data[0]['ID_Notario_Representante'];
				// echo $data[0]['Nombre_Notario_Representante'];
				// echo $apoderado["id_colegiacion"];
				//echo $apoderado['nombre_apoderado'];
				//echo $apoderado['ident_apoderado'];
				//echo $apoderado['correo_apoderado'];
				//echo $apoderado['tel_apoderado'];
				//echo $apoderado['dir_apoderado'];

				$datos[0][] = array("rtn_solicitante" =>$data[0]["RTNSolicitante"],"nombre_solicitante" =>$data[0]["NombreSolicitante"],"nombre_empresa" =>$data[0]["NombreEmpresa"],"codigo_tipo" =>$data[0]["CodigoSolicitanteTipo"],"dir_solicitante" =>$data[0]["Direccion"],"tel_solicitante" =>$data[0]["Telefono"],"correo_solicitante" =>$data[0]["Email"],"aldea" =>$data[0]["Aldea"],'Aldeas'=>$Aldeas['Aldea'],'Municipio'=>$Aldeas['Municipio'],'Departamento'=>$Aldeas['Departamento'],'Numero_Escritura'=>$data[0]['ID_Escritura'],'Fecha_Escritura'=>$data[0]['Fecha_Elaboracion'],'Lugar_Escritura'=>$data[0]['Lugar_Elaboracion'],'ID_Notario'=>$data[0]['ID_Notario'],'Notario'=>$data[0]['Nombre_Notario'],'RTN_Representante'=>$data[0]['ID_Representante_Legal'],'Nombre_Representante'=>$data[0]['Nombre_Representante_Legal'],'Telefono_Representante'=>$data[0]['Telefono_Representante'],'Email_Representante'=>$data[0]['Email_Representante'],'Direccion_Representante'=>$data[0]['Direccion_Representante'],'Representante_Escritura'=>$data[0]['Representante_Escritura'],'Fecha_Elaboracion_Representante'=>$data[0]['Fecha_Elaboracion_Representante'],'Lugar_Elaboracion_Representante'=>$data[0]['Lugar_Elaboracion_Representante'],'Numero_Inscripcion'=>$data[0]['Numero_Inscripcion'],'ID_Notario_Representante'=>$data[0]['ID_Notario_Representante'],'Nombre_Notario_Representante'=>$data[0]['Nombre_Notario_Representante'],"idcolegiacion"=>$_POST["idcolegiacion"],"nombre_apoderado" =>$apoderado["nombre_apoderado"],"ident_apoderado" =>$apoderado["ident_apoderado"],"correo_apoderado" =>$apoderado["correo_apoderado"],"tel_apoderado" =>$apoderado["tel_apoderado"],"dir_apoderado" =>$apoderado["dir_apoderado"]);
			}
		}
		echo json_encode($datos);
	}
	/*@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	  @@@ FUNCION PARA RECUPERAR EL APODERADO LEGAL DEL PORTAL DE APODERADOS @@@
	  @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@*/
	function getApoderado($col) {
		$query = "SELECT ID_ColegiacionAPL, Nombre_Apoderado_Legal, Identidad, Email, Telefono, Direccion FROM [IHTT_DB].[dbo].[TB_Apoderado_Legal] WHERE ID_ColegiacionAPL = :IDCOL";
		$p = array(":IDCOL" => $col);
		$data = $this->select($query, $p );
		if (count($data)>0) {
			return json_encode(array("id_colegiacion" =>$data[0]["ID_ColegiacionAPL"],"nombre_apoderado" =>$data[0]["Nombre_Apoderado_Legal"],"ident_apoderado" =>$data[0]["Identidad"],"correo_apoderado" =>$data[0]["Email"],"tel_apoderado" =>$data[0]["Telefono"],"dir_apoderado" =>$data[0]["Direccion"]));
		} else {
			return json_encode(array("id_colegiacion" =>'',"nombre_apoderado" =>'',"ident_apoderado" =>'',"correo_apoderado" =>'',"tel_apoderado" =>'',"dir_apoderado" =>'',"dir_apoderado" => count($data)));
		}				
	}
	/*@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	  @@@ FUNCION PARA RECUPERAR EL APODERADO LEGAL DEL PORTAL DE APODERADOS @@@
	  @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@*/
	function getApoderadoAPI($col) {
		$query = "SELECT ID_ColegiacionAPL, Nombre_Apoderado_Legal, Identidad, Email, Telefono, Direccion FROM [IHTT_DB].[dbo].[TB_Apoderado_Legal] WHERE ID_ColegiacionAPL = :IDCOL";
		$p = array(":IDCOL" => $col);
		$data = $this->select($query, $p );
		if (count($data)>0) {
			echo json_encode(array("id_colegiacion" =>$data[0]["ID_ColegiacionAPL"],"nombre_apoderado" =>$data[0]["Nombre_Apoderado_Legal"],"ident_apoderado" =>$data[0]["Identidad"],"correo_apoderado" =>$data[0]["Email"],"tel_apoderado" =>$data[0]["Telefono"],"dir_apoderado" =>$data[0]["Direccion"]));
		} else {
			echo json_encode(array("id_colegiacion" =>'',"nombre_apoderado" =>'',"ident_apoderado" =>'',"correo_apoderado" =>'',"tel_apoderado" =>'',"dir_apoderado" => count($data)));
		}				
	}

	/*@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	  @@@ FUNCION PARA RECUPERAR EL APODERADO LEGAL DEL PORTAL DE APODERADOS @@@
	  @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@*/
	function getSolicitanteAPI($col) {
		$query = "SELECT RTNSolicitante, NombreSolicitante, NombreEmpresa, Email, Telefono, Direccion FROM [IHTT_DB].[dbo].[TB_Solicitante] WHERE RTNSolicitante = :RTNSolicitante OR ID_Solicitante = :ID_Solicitante";
		$p = array(":RTNSolicitante" => $col,":ID_Solicitante" => $col);
		$data = $this->select($query, $p );

		if ($data != false and count($data)>0) {
			echo json_encode(array("RTNSolicitante" =>$data[0]["RTNSolicitante"],"NombreSolicitante" =>$data[0]["NombreSolicitante"],"NombreEmpresa" =>$data[0]["NombreEmpresa"],"Email" =>$data[0]["Email"],"Telefono" =>$data[0]["Telefono"],"Direccion" =>$data[0]["Direccion"]));
		} else {
			echo json_encode(array("RTNSolicitante" =>'',"NombreSolicitante" =>'',"NombreEmpresa" =>'',"Email" =>'',"Telefono" =>'',"Direccion" =>''));
		}				
	}

	/*@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	  @@@ FUNCION PARA RECUPERAR EL APODERADO LEGAL DEL PORTAL DE APODERADOS @@@
	  @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@*/
	  function getIdTipodeServicio($expediente) {
		$query = "select C.DESC_Tipo_Servico,C.ID_Tipo_Servico from [IHTT_DB].[dbo].[TB_Expedientes] A,[IHTT_PREFORMA].[dbo].[TB_Solicitud] B,[IHTT_DB].[dbo].[TB_Tipo_Servicio] C
		WHERE A.Preforma=B.ID_Formulario_Solicitud AND B.ID_TIpo_Servicio = C.DESC_Tipo_Servico AND A.ID_Expediente = :ID_Expediente";
		$p = array(":RTNSolicitante" => $col);
		$data = $this->select($query, $p );
		if (count($data)>0) {
			echo json_encode(array("RTNSolicitante" =>$data[0]["RTNSolicitante"],"NombreSolicitante" =>$data[0]["NombreSolicitante"],"NombreEmpresa" =>$data[0]["NombreEmpresa"],"Email" =>$data[0]["Email"],"Telefono" =>$data[0]["Telefono"],"Direccion" =>$data[0]["Direccion"]));
		} else {
			echo json_encode(array("RTNSolicitante" =>'',"NombreSolicitante" =>'',"NombreEmpresa" =>'',"Email" =>'',"Telefono" =>'',"Direccion" =>''));
		}				
	}

	/*@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	  @@@ FUNCION PARA RECUPERAR LA INFORMACIÓN DEL CERTIFICADO @@@
	  @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@*/
	function getDatosConcesion($concesion) {
		$query = "SELECT * FROM [IHTT_PREFORMA].[dbo].[v_Datos_Vehiculo] WHERE N_Certificado = :N_Certificado";
		$p = array(":N_Certificado" => $concesion);
		$data = $this->select($query, $p );
		$datos = array();
		$datos[1] = count($data);
		$datos[0] = $data;
		echo json_encode($datos);
	}

}
$api = new Api_Ram();	
?>