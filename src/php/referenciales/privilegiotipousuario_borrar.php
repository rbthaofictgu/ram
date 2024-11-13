<?php
header('Content-type: UTF-8');
/* validando que paso por la pantalla de login */
session_start();
if (!isset($_SESSION['user_name'])) { //tipo
	header("location: ../index.php?msg=Favor inicie sesión para poder ingresar al sistema"); 
	exit();
}
?>
<?php require_once('../../utils/tipo_dispositivo.php'); ?>
<?php require_once('../../../configuracion/configuracion.php'); ?>
<?php require_once('../../utils/funciones_db.php'); ?>
<?php require_once("../../../../config/conexion.php"); ?>
<?PHP
// Validando privilegios
$codigo = 'PRIVILEGIOSXTIPODEUSUARIO';
require_once('../../utils/validar_privilegio.php');
$pantallax = explode('/',$currentPage);
$pantallax = $pantallax[(count($pantallax)-1)];
$pantallax = explode('.',$pantallax);
$pantalla[0] = $pantallax[0];

// Obteniendo el privilegio
$rs_privilegio_rs_mantenimiento = "-1";
if (isset($_POST['privilegio'])) {
	$rs_privilegio_rs_mantenimiento =  $_POST['privilegio'];
}
// Obteniendo el tipo de usuario
$rs_tipo_rs_mantenimiento = "-1";
if (isset($_POST['user_name'])) {
	$rs_tipo_rs_mantenimiento =  $_POST['user_name'];
}
// Sección de inserción del registro
if ($rs_privilegio_rs_mantenimiento <> '' and $rs_tipo_rs_mantenimiento > 0) {
	$insertSQL = "DELETE FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_privilegio_x_tipo_usuario]
	WHERE id_tipo_usuario = :id_tipo_usuario AND codigo = :codigo";
	$db->beginTransaction();
	$stmt = $db->prepare($insertSQL);
	$res = $stmt->execute(Array(':codigo' => $rs_privilegio_rs_mantenimiento,
								':id_tipo_usuario' => $rs_tipo_rs_mantenimiento));
	if (isset($res) and $res[2] <> '') {
		$error = $stmt->errorInfo();
		echo $error;
	} else {
		$stmt->closeCursor();
		$db->commit();
	}
}
?>