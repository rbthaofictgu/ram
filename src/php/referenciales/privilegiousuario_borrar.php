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
<?php require_once('../../../../config/conexion.php'); ?>
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
if (isset($_POST['user_name'])) {
	$rs_tipo_rs_mantenimiento =  $_POST['user_name'];
}
// Sección de inserción del registro
if ($rs_privilegio_rs_mantenimiento <> '' and $rs_tipo_rs_mantenimiento <> '') {
	$insertSQL = "DELETE FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_privilegio_x_usuario]
	WHERE id_usuario = :id_tipo_usuario and codigo = :codigo";
	$db->beginTransaction();
	$stmt = $db->prepare($insertSQL);
	$res = $stmt->execute(array(
		':codigo' => $rs_privilegio_rs_mantenimiento,
		':id_tipo_usuario' => $rs_tipo_rs_mantenimiento
	));
	if (isset($res) and $res[2] <> '') {
		$error = $stmt->errorInfo();
		echo $error;
	} else {
		$stmt->closeCursor();
		$db->commit();
	}
}
?>