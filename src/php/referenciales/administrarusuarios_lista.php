<?php
/* validando que paso por la pantalla de login */
session_start();
if (!isset($_SESSION['tipo'])) {
	$appcfg_page_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$_SESSION['url'] = $appcfg_page_url;
	$_SESSION['flashmsg'] = "Favor inicie sesión para poder ingresar al sistema";
	header("location: ../inicio.php");
	exit();
}
?>
<?php require_once('../../utils/tipo_dispositivo.php'); ?>
<?php require_once('../../../configuracion/configuracion.php'); ?>
<?php require_once('../../../config/conexion.php'); ?>
<?php require_once('../../utils/funciones_db.php'); ?>
<?php require_once('../../utils/total_pages.php'); ?>
<?php
// Validando privilegio
$codigo = 'ADMINISTRACIONUSUARIOS';
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
$query_rs_mantenimiento ="SELECT u.ID_User as id, u.ID_Empleado as codigo_emepleado, u.Usuario_Nombre as nombre,
CASE WHEN u.Estado_Usuario = 0 THEN 'Inactivo' else 'Activo' end as estado 
FROM [IHTT_USUARIOS].[dbo].[TB_Usuarios] as u ORDER BY u.Usuario_Nombre LIMIT " . $offset . ','  . $no_of_records_per_page;

// "SELECT a.id, a.codigo_emepleado, a.nombre, case when a.estado = 1 then 'Inactivo' else 'Activo' end as estado FROM usuarios a order by a.nombre LIMIT " . $offset . ','  . $no_of_records_per_page;
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
	$total_pages = ceil($totalRows_rs_mantenimiento / $no_of_records_per_page);
	$res = $stmt->execute();
	$row_rs_mantenimiento = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html>

<head>
   <title><?php echo $tablanext; ?> Lista</title>
   <?php require_once('../../../encabezado_lista.php'); ?>

</head>

<body>
   <?php require_once('../../../menu_segun_privilegios.php'); ?>
   <div class="main">
      <?php
		$rotulo = 'MANTENIMIENTO: ' . $tablanext . ' LISTA';
		require_once('../../../rotulo_pantalla_lista.php');
		require_once('../../../botones_lista.php'); ?>
      <div class="table-responsive-lg">
         <table class="table table-hover">
            <thead>
               <tr class="gy_bg_navbar_dwd">
                  <th>
                     <div align="left"><strong>#</strong></div>
                  </th>
                  <th>
                     <div align="left"><strong>USUARIO</strong></div>
                  </th>
                  <th>
                     <div align="left"><strong>NOMBRE</strong></div>
                  </th>
                  <th>
                     <div align="left"><strong>CODIGO_EMPLEADO</strong></div>
                  </th>
                  <th>
                     <div align="left"><strong>ESTADO</strong></div>
                  </th>
                  <th>
                     <div align="left"><strong>VER PRIVILEGIOS</strong></div>
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
                     <div align="left"><?php echo $row_rs_mantenimiento['id']; ?></div>
                  </td>

                  <td>
                     <div align="left"><?php echo $row_rs_mantenimiento['nombre']; ?><a hidden="hidden"
                           href="<?PHP echo $pantalla[0] . '.php?'; ?>id=<?php echo $row_rs_mantenimiento['id']; ?>"
                           class="style3"><?php echo $row_rs_mantenimiento['nombre']; ?></a></div>
                  </td>
                  <td>
                     <div align="left"><?php echo $row_rs_mantenimiento['codigo_emepleado']; ?></div>
                  </td>
                  <td>
                     <div align="left"><?php echo $row_rs_mantenimiento['estado']; ?></div>
                  </td>
                  <td>
                     <div align="left"><a
                           href="privilegiosxusuario.php?id=<?php echo $row_rs_mantenimiento['id']; ?>">Ver</a></div>
                  </td>

               </tr>
               <?php $row_rs_mantenimiento = $stmt->fetch();
					} ?>
            </tbody>
         </table>

         <div class="bottom">
            <footer class="d-flex flex-wrap justify-content-between align-items-center py-1 my-1">
               <?php include_once('footer.php') ?>
            </footer>
         </div>

         <?php
			include_once('pie.php');
			include_once('../sattb/modal_pie.php');
			?>
            
   <script src = "<?PHP echo $appcfg_Dominio; ?>js/administracion_usuario_lista.js" >
   </script>
   <script src="<?PHP echo $appcfg_Dominio; ?>js/expresiones_regulares.js"></script>
   <!-- <?php
	include_once('../sattb/assets/js/openModalLogin.js');
	include_once('../sattb/assets/js/login.js');
	?> -->

      </div>

      <?php require_once('../../pie.php');
		// Final de Procesamiento	  
		$stmt->closeCursor();
		?>
      <?PHP require_once('../../../timer_logout.php'); ?>
      <script src="<?PHP echo $appcfg_Dominio; ?>js/expresiones_regulares.js"></script>
      <?PHP require_once('../../../utils/click_row.js'); ?>
</body>

</html>