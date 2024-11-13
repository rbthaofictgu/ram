<?php
/* validando que paso por la pantalla de login */
session_start();
if (!isset($_SESSION['user_name'])) { //tipo
	$appcfg_page_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$_SESSION['url'] = $appcfg_page_url;
	$_SESSION['flashmsg'] = "Favor inicie sesión para poder ingresar al sistema";
	header("location: ../index.php");
	exit();
}
?>
<?php require_once('../../utils/tipo_dispositivo.php'); ?>
<?php require_once('../../../configuracion/configuracion.php'); ?>
<?php require_once('../../../../config/conexion.php'); ?>
<?php require_once('../../utils/funciones_db.php'); ?>
<?php require_once('../../utils/total_pages.php'); ?>
<?php
// Validando privilegio
$codigo = 'PRIVILEGIOXUSUARIO';
require_once('../../utils/validar_privilegio.php');
$pantalla = explode('_', $currentPage);

if (isset($_GET['pageno'])) {
	$pageno = $_GET['pageno'];
} else {
	$pageno = 1;
}

if (isset($_GET['no_of_records_per_page'])) {
	$no_of_records_per_page = $_GET['no_of_records_per_page'];
} else {
	$no_of_records_per_page = $no_of_records_per_page_base;
}

$offset = ($pageno - 1) * $no_of_records_per_page;

$total_pages = f_total_pages($tablanext, $no_of_records_per_page, $db);
// Armando el query
$row_rs_mantenimientocopia = [];
$query_rs_mantenimiento = "SELECT u.ID_User as id, u.ID_Empleado as codigo_empleado,
 u.Usuario_Nombre as nombre,
CASE WHEN u.Estado_Usuario = 0 THEN 'Inactivo' else 'Activo' end as estado 
FROM [IHTT_USUARIOS].[dbo].[TB_Usuarios] as u ORDER BY u.Usuario_Nombre";

// "SELECT a.id, a.correo_electronico, a.nombre, 
// case when a.estado = 1 then 'Inactivo' else 'Activo' end as estado 
// FROM usuarios a order by a.nombre";
// Preparando la sentencia
$stmt = $db->prepare($query_rs_mantenimiento);
// Ejecutanto el query
$res = $stmt->execute();
// Si hay algun error
if (isset($res) and $res == '') {
	$msgerror = "Error intentando leer tabla de ubicacion, error -> " . geterror($stmt->errorInfo());
	$stmt->closeCursor();
	$totalRows_rs_mantenimiento = 0;
} else {
	$row_rs_mantenimiento = $stmt->fetchall(\PDO::FETCH_NUM);
	$totalRows_rs_mantenimiento  = $stmt->rowcount();
	$res = $stmt->execute();
	$row_rs_mantenimiento = $stmt->fetchAll();
	$row_rs_mantenimientocopia = $row_rs_mantenimiento;
}
?>
<!DOCTYPE html>
<html>

<head>
	<title><?php echo $tablanext;  ?> Lista</title>
	<?php require_once('../../../encabezado_lista.php'); ?>
</head>

<body>
	<?php require_once('../../../encabezado_body.php'); ?>
	<?php require_once('../../../menu_segun_privilegios.php'); ?>
	<div class="main">
		<?php
		$rotulo = 'Mantenimiento: ' . $tablanext . ' Listado';
		require_once('../../../rotulo_pantalla_lista.php');
		require_once('../../../botones_lista.php'); ?>
		<div class="table-responsive">
			<table class="table table-hover" width="100%">
				<thead>
					<tr class="gy_bg_navbar_dwd">
						<th>
							<div align="left">#</div>
						</th>
						<th>
							<div align="left">USUARIO</div>
						</th>
						<th>
							<div align="left">NOMBRE</div>
						</th>
						<th>
							<div align="left">CODIGO_EMPLEADO</div>
						</th>
						<th>
							<div align="left">ESTADO</div>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
					for ($i = 0; $i < $totalRows_rs_mantenimiento; $i++) { ?>
						<tr>
							<td>
								<div align="left"><?php echo ($i + 1); ?></div>
							</td>

							<td>
								<div align="left"><?php echo $row_rs_mantenimiento[$i]['id']; ?></div>
							</td>

							<td>
								<div align="left"><?php echo $row_rs_mantenimiento[$i]['nombre']; ?><a hidden="hidden" href="<?PHP echo $pantalla[0]; ?>.php?id=<?php echo $row_rs_mantenimiento[$i]['id']; ?>&nombre=<?php echo $row_rs_mantenimiento[$i]['nombre']; ?>" class="style3"><?php echo $row_rs_mantenimiento['nombre']; ?></a></div>
							</td>

							<td>
								<div align="left"><?php echo $row_rs_mantenimiento[$i]['codigo_empleado']; ?></div>
							</td>

							<td>
								<div align="left"><?php echo $row_rs_mantenimiento[$i]['estado']; ?></div>
							</td>

						</tr>
					<?php $totalRows_rs_mantenimiento;
					} ?>
				</tbody>
			</table>
		</div>

		<div class="bottom">
			<footer class="d-flex flex-wrap justify-content-between align-items-center py-1 my-1">
				<?php include_once('../../../footer.php') ?>
			</footer>
		</div>

		<?php
		include_once('../../../pie.php');
		// include_once('../sattb/modal_pie.php');
		?>

		<!-- <script src = "<?PHP echo $appcfg_Dominio; ?>js/administracion_usuario_lista.js" >
		</script> -->
		<!-- <script src="<?PHP echo $appcfg_Dominio; ?>js/expresiones_regulares.js"></script> -->
		<!-- <?php
				include_once('../sattb/assets/js/openModalLogin.js');
				include_once('../sattb/assets/js/login.js');
				?> -->

		<?php require_once('../../../pie.php');
		include_once('modal_encabezado.php');
		include_once('modal_pie.php');
		// Final de Procesamiento	  
		$stmt->closeCursor();
		?>
		<?php require_once('modal_pie.php'); ?>
		<?php require_once('modal_encabezado.php'); ?>
		<?PHP require_once('../../../timer_logout.php'); ?>
		<script src="../../utils/click_row.js"></script>
		<!-- <?PHP //require_once('../../utils/click_row.js'); 
				?> -->
</body>

</html>