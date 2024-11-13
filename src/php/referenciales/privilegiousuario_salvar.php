<?php
header('Content-type: UTF-8');
/* validando que paso por la pantalla de login */
session_start();
if (!isset($_SESSION['user_name'])) { //tipo
	header("location: ../inicio.php?msg=Favor inicie sesión para poder ingresar al sistema");
	exit();
}
?>
<?php require_once('../../utils/tipo_dispositivo.php'); ?>
<?php require_once('../../../configuracion/configuracion.php'); ?>
<?php require_once('../../utils/funciones_db.php'); ?>
<?php require_once("../../../../config/conexion.php"); ?>
<?PHP
// Validando privilegios
$codigo = 'PRIVILEGIOXUSUARIO';
require_once('../../utils/validar_privilegio.php');
$pantallax = explode('/', $currentPage);
$pantallax = $pantallax[(count($pantallax) - 1)];
$pantallax = explode('.', $pantallax);
$pantalla[0] = $pantallax[0];

// Obteniendo el privilegio
$rs_privilegio_rs_mantenimiento = "";
if (isset($_POST['privilegio'])) {
	$rs_privilegio_rs_mantenimiento =  $_POST['privilegio'];
}
// Obteniendo el tipo de usuario
$rs_tipo_rs_mantenimiento = "";
if (isset($_POST['tipo'])) {
	$rs_tipo_rs_mantenimiento =  $_POST['tipo'];
}
// Sección de inserción del registro
if ($rs_privilegio_rs_mantenimiento <> '' and $rs_tipo_rs_mantenimiento <> '') {
	$insertSQL = "INSERT INTO  FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_privilegio_x_usuario] (
									codigo, 
									id_usuario,
									usuario_creacion, 
									fecha_creacion,
									ip_creacion,
									host_creacion)
							VALUES (:codigo, 
									:id_tipo_usuario,
									:usuario_creacion,
									now(),
									:ip,
									:host)";
	$stmt = $db->prepare($insertSQL);
	$res = $stmt->execute(array(
		':codigo' => $rs_privilegio_rs_mantenimiento,
		':id_tipo_usuario' => $rs_tipo_rs_mantenimiento,
		':usuario_creacion' => $_SESSION["user_name"],
		':ip' => $ip,
		':host' => $host
	));
	if (isset($res) and $res[2] <> '') {
		$error = "Error intentando insertar registro de privilegios por tipo de usuario, error -> " . $res[2] . $res[1] . $res[0];
		echo ($error);
	} else {
		$stmt->closeCursor();
	}
} else {
	echo 'xxx';
}


?>