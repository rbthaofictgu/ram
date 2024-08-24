<?php
/* validando que paso por la pantalla de login */
session_start();
if (!isset($_SESSION['tipo'])) {
	header("location: ../inicio.php?msg=Favor inicie sesión para poder ingresar al sistema"); exit();
}
?>
<?php require_once('../utils/tipo_dispositivo.php'); ?>
<?php require_once('../conexion/configuracion.php'); ?>
<?php require_once('../conexion/conexion.php'); ?>
<?php require_once('../funciones/funciones_db.php'); ?>
<?php require_once('../utils/total_pages.php'); ?>
<?php
// Validando privilegio
$id_privilegio = 'PRIVILEGIOS';
require_once('../utils/validar_privilegio.php');
$pantalla = explode('_',$currentPage);
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
$offset = ($pageno-1) * $no_of_records_per_page; 
$total_pages = f_total_pages($tablanext,$no_of_records_per_page,$conn); 

// Armando el query
$query_rs_mantenimiento = "SELECT a.id,a.descripcion,a.nivel_menu,
case when a.estado = 'I' then 'Inactivo' else 'Activo' end as estado 
FROM privilegio a order by a.id LIMIT " . $offset . ','  . $no_of_records_per_page;
// Preparando la sentencia
$stmt = $conn->prepare($query_rs_mantenimiento);
// Ejecutanto el query
$res = $stmt->execute();
// Si hay algun error
if (isset($res) and $res == '') {
	$msg = "Error intentando leer tabla de oficinas, error -> " . geterror($stmt->errorInfo());
	echo $msg;exit();
	$stmt->closeCursor();
	$totalRows_rs_mantenimiento = 0;
} else {
    $row_rs_mantenimiento = $stmt->fetchall(\PDO::FETCH_NUM);
	$totalRows_rs_mantenimiento  = $stmt->rowcount();
	$res = $stmt->execute();
	$row_rs_mantenimiento = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $tablanext;  ?> Lista</title>
		<?php require_once('../encabezado_lista.php'); ?>
    </head>
<body>
<?php require_once('../encabezado_body.php'); ?>
<div class="main">
<?php 
$rotulo = 'Mantenimiento: ' . $tablanext . ' Listado';
require_once('../rotulo_pantalla_lista.php');
require_once('../botones_lista.php');?>
<div class="table-responsive">
<table class="table table-hover" width="100%">
	<thead>
		<tr class="gy_detalle_pantalla">
             <th><div align="left">#</div></th>
             <th><div align="left">ID</div></th>
             <th><div align="left">Descripción</div></th>
             <th><div align="left">Menú</div></th>
             <th><div align="left">Estado</div></th>         
		</tr>
	</thead>
	<tbody>
    <?php 
	for ($i=0;$i<$totalRows_rs_mantenimiento;$i++)  { ?>
	<tr>
        <td><div align="left"><?php echo ($i + 1);?></div></td>
        <td><div align="left"><?php echo $row_rs_mantenimiento['id'];?></div></td>
        <td><div align="left"><?php echo $row_rs_mantenimiento['descripcion']; ?><a hidden="hidden" href="<?PHP echo $pantalla[0];?>.php?id=<?php echo $row_rs_mantenimiento['id']; ?>" class="style3"><?php echo $row_rs_mantenimiento['descripcion']; ?></a></div></td>
        <td><div align="left"><?php echo $row_rs_mantenimiento['nivel_menu']; ?></div></td>
        <td><div align="left"><?php echo $row_rs_mantenimiento['estado']; ?></div></td>
       
        </tr>
<?php $row_rs_mantenimiento = $stmt->fetch();
	} ?>
    </tbody>
</table>
</div>
<?php require_once('../pie.php'); 
// Final de Procesamiento	  
$stmt->closeCursor();
?>
<?PHP require_once('../pie_lista_js.php'); ?>
<?PHP require_once('../modal_pie.php'); ?>
<?PHP require_once('../timer_logout.php'); ?>
<?PHP require_once('../modal_encabezado.php'); ?>
<?PHP require_once('../utils/click_row.js'); ?>	
	</body>
</html>