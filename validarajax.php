<?php
if (!isset($body_class)) {

	require_once('configuracion/configuracion.php');
	require_once('../config/conexion.php');
	if (!isset($_SESSION["user_name"])) {
		session_start();
	}
} else {

	$_POST['txt_usuario'] = "rbarrientos";
	$_POST['txt_clave'] = 'ihttroot2020';
}

$rs_usuario_rs_usuarios = "-1";
if (isset($_POST['txt_usuario'])) {
	$rs_usuario_rs_usuarios = $_POST['txt_usuario'];
}

$rs_clave_rs_usuarios = "-1";
if (isset($_POST['txt_clave'])) {
	$rs_clave_rs_usuarios = $_POST['txt_clave'];
}

// echo $rs_usuario_rs_usuarios;
require_once('src/utils/queryValidarUsario.php');
$stmt = $db->prepare($query_rs_usuarios);

// $stmt->execute(Array('correo_electronico' => $rs_usuario_rs_usuarios, 'clave' => 
// hash('SHA512',$rs_clave_rs_usuarios,false)));
// Inicio de Procesamiento
$stmt->execute(array('correo_electronico' => $rs_usuario_rs_usuarios));

$row_rs_usuarios = $stmt->fetchAll();
$totalRows_rs_usuarios = $stmt->rowcount();
//   echo json_encode($row_rs_usuarios) .'validarajax';
// Deifniendo valor inicial de la foto
$json["msg"] = "N";
$json["foto"] = "";
$json["nombre"] = "";

for ($i = 0; $i < $totalRows_rs_usuarios; $i++) {

	if ($totalRows_rs_usuarios > 0 && $row_rs_usuarios[$i]['estado'] == 1) {
		$_SESSION["accion"] = 1;
		$_SESSION['row_rs_usuarios'] = $row_rs_usuarios;
		$_SESSION['$totalRows_rs_usuarios'] = $totalRows_rs_usuarios;

		require_once('src/utils/session_validar.php');

		$json["msg"] = "S";
		
		  $json["foto"] = "https://satt2.transporte.gob.hn:285/ram/assets/images/persona.png";
		//   $json["foto"] = $cfgapp_ruta_completa_foto_usuario . $row_rs_usuarios['foto'] . '.' .  $row_rs_usuarios['extencion_foto'];
		$json["nombre"] = $_SESSION["nombre"];
	} else {

		if ($stmt->rowcount() > 0 && $row_rs_usuarios[$i]['estado'] == 0 & $totalRows_rs_usuarios > 0) {
			$json["msg"] = "I";
		} else {
			$json["msg"] = "E";
		}
	}
	# code...
}

// Final de Procesamiento	  
$stmt->closeCursor();

if (!isset($body_class)) {
	unset($_SESSION['id_user']);
	unset($_SESSION["correo_electronico"]);
	unset($_SESSION["user_name"]);
	unset($_SESSION["nombre"]);
	unset($_SESSION["hash"]);
	unset($_SESSION['tipo']);
	unset($_SESSION['descripcion']);
	unset($_SESSION['STYLE']);
	
} else {
	header('Content-Type: application/json');
	echo trim(json_encode($json));
}
