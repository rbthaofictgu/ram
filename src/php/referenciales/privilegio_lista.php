<?php
/* validando que paso por la pantalla de login */
session_start();
if (!isset($_SESSION['user_name'])) { //tipo
	$appcfg_page_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$_SESSION['url'] = $appcfg_page_url;
	$_SESSION['flashmsg'] = "Favor inicie sesión para poder ingresar al sistema";
	header("location: ../inicio.php");
	exit();
}
?>

<?php require_once('../../utils/tipo_dispositivo.php'); ?>
<!-- //* archivo de configuracion donde se encuentrar las variables globales. -->
<?php require_once('../../../configuracion/configuracion.php'); ?>
<!-- //*archivo de configuracion de lña conexion con la base de datos -->
<?php require_once("../../../../config/conexion.php"); ?>
<!-- //* archivo de funciones varias(recuperar url,intentos fallidos,validar accesos, etc.) -->
<?php require_once('../../utils/funciones_db.php'); ?>

<?php require_once('../../utils/total_pages.php'); ?>
<?php
// Validando privilegio
$codigo = 'PRIVILEGIOS';
// * archivo encargado de verificar si tienen permiso otorgado 
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

//*********************************************************** */
//* Armando el query de la tabla privilegio
//*********************************************************** */

$query_rs_mantenimiento="SELECT p.id, p.descripcion, p.nivel_menu, 
CASE WHEN p.estado = 'I' THEN 'Inactivo' ELSE 'Activo' END AS estado
FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Privilegio] AS p
ORDER BY p.nivel_menu, p.menu_padre
OFFSET " . $offset . " ROWS 
FETCH NEXT " . $no_of_records_per_page . " ROWS ONLY;";
// $query_rs_mantenimiento = "SELECT p.id, p.descripcion, p.nivel_menu, 
// CASE WHEN  p.estado='I' THEN 'Inactivo' ELSE 'Activo' end AS estado
// FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Privilegio] AS p
// ORDER BY p.nivel_menu, p.menu_padre
// LIMIT " . $offset . ','  . $no_of_records_per_page;

//  "SELECT a.id,a.descripcion,a.nivel_menu,
// case when a.estado = 'I' then 'Inactivo' else 'Activo' end as estado 
// FROM privilegio a order by a.nivel_menu, a.menu_padre LIMIT " . $offset . ','  . $no_of_records_per_page;


//* Preparando la sentencia
$stmt = $db->prepare($query_rs_mantenimiento);
//* Ejecutanto el query
$res = $stmt->execute();
//* Si hay algun error
if (isset($res) and $res == '') {
	$msg = "Error intentando leer tabla de oficinas, error -> " . geterror($stmt->errorInfo());
	echo $msg;
	exit();
   //*limpiando datos del sql server
	$stmt->closeCursor();
	$totalRows_rs_mantenimiento = 0;
} else {
   //*obteniendo datos de la consulta
	$row_rs_mantenimiento = $stmt->fetchall(\PDO::FETCH_NUM);
   //* obteniendo el total de filas afectadas
	$totalRows_rs_mantenimiento  = $stmt->rowcount();
   //*obteniendo respuesta 
	$res = $stmt->execute();
   //*obteniendo eun elemento de la consulta
	$row_rs_mantenimiento = $stmt->fetch();
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
      <div class="table-responsive mb-5">
         <table class="table table-hover mb-5" width="100%">
            <thead>
               <tr class="gy_bg_navbar_dwd">
                  <th>
                     <div align="left">#</div>
                  </th>
                  <th>
                     <div align="left">ID</div>
                  </th>
                  <th>
                     <div align="left">Descripción</div>
                  </th>
                  <th>
                     <div align="left">Menú</div>
                  </th>
                  <th>
                     <div align="left">Estado</div>
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
                     <div align="left"><?php echo $row_rs_mantenimiento['descripcion']; ?><a hidden="hidden"
                           href="<?PHP echo $pantalla[0]; ?>.php?id=<?php echo $row_rs_mantenimiento['id'];?>"
                           class="style3"><?php echo $row_rs_mantenimiento['descripcion']; ?></a></div>
                  </td>
                  <td>
                     <div align="left"><?php echo $row_rs_mantenimiento['nivel_menu']; ?></div>
                  </td>
                  <td>
                     <div align="left"><?php echo $row_rs_mantenimiento['estado']; ?></div>
                  </td>

               </tr>
               <?php $row_rs_mantenimiento = $stmt->fetch();
					} ?>
            </tbody>
         </table>
         <div class="bottom mt-5">
            <footer class="d-flex flex-wrap justify-content-between align-items-center py-1 my-1">
               <?php include_once('../../../footer.php') ?>
            </footer>
         </div>

         <?php
			include_once('../../../pie.php');
         
			// include_once('../sattb/modal_pie.php');
			?>

         <!-- <script src="<?php // echo $appcfg_Dominio; ?>js/administracion_usuario_lista.js"> -->
         </script>
         <!-- <script src="<?php echo $appcfg_Dominio; ?>js/expresiones_regulares.js"></script> -->
         <!-- <?php
			// include_once('../sattb/assets/js/openModalLogin.js');
			// include_once('../sattb/assets/js/login.js');
			?> -->

      </div>
      <?php require_once('../../../pie.php');
		// Final de Procesamiento	  
		$stmt->closeCursor();
		?>
      <?php require_once('../../../timer_logout.php'); ?>
      <?php require_once('modal_encabezado.php'); ?>
        <?php require_once('modal_pie.php'); ?>
      <script src="../../utils/click_row.js"></script>
      <!-- <?php require_once('../../utils/click_row.js'); ?> -->
</body>

</html>