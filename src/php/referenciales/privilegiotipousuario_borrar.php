<?php
header('Content-type: UTF-8');
/* validando que paso por la pantalla de login */
session_start();
if (!isset($_SESSION['tipo'])) {
	header("location: ../inicio.php?msg=Favor inicie sesión para poder ingresar al sistema"); exit();
}
?>
<?php require_once('../utils/tipo_dispositivo.php'); ?>
<?php require_once('../conexion/configuracion.php'); ?>
<?php require_once('../funciones/funciones_db.php'); ?>
<?php require_once('../conexion/conexion.php'); ?>
<?PHP
// Validando privilegios
$id_privilegio = 'PRIVILEGIOSXTIPODEUSUARIO';
require_once('../utils/validar_privilegio.php');
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
if (isset($_POST['tipo'])) {
	$rs_tipo_rs_mantenimiento =  $_POST['tipo'];
}
// Sección de inserción del registro
if ($rs_privilegio_rs_mantenimiento <> '' and $rs_tipo_rs_mantenimiento > 0) {
	$insertSQL = "delete from privilegio_x_tipo_usuario where id_tipo_usuario = :id_tipo_usuario and id_privilegio = :id_privilegio";
	$conn->beginTransaction();
	$stmt = $conn->prepare($insertSQL);
	$res = $stmt->execute(Array(':id_privilegio' => $rs_privilegio_rs_mantenimiento,
								':id_tipo_usuario' => $rs_tipo_rs_mantenimiento));
	if (isset($res) and $res[2] <> '') {
		$error = $stmt->errorInfo();
		echo $error;
	} else {
		$stmt->closeCursor();
		$conn->commit();
	}
}
?>