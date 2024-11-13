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


<!-- //* archivo de funciones varias(recuperar url,intentos fallidos,validar accesos, etc.) -->
<?php require_once('../../utils/funciones_db.php'); ?>
<!-- //*funcion que permite validar privilegio a acceder a cada opción. -->
<?php require_once('../../utils/create_copy_row.php'); ?>

<?php require_once('../../utils/tipo_dispositivo.php'); ?>
<!-- //* archivo de conexion a la base de datos -->
<?php require_once('../../../../config/conexion.php'); ?>
<!-- //* archivo de configuracion donde se encuentrar las variables globales. -->
<?php require_once('../../../configuracion/configuracion.php'); ?>


<!DOCTYPE html>

<head>
   <title>objeto</title>
   <!-- //*mandando encabezados -->
   <?php require_once('../../../encabezado_body.php'); ?>
</head>

<body>
   <div class="main">
      <!-- //*vista del menu de privilegios -->
      <?php require_once('../../../menu_segun_privilegios.php'); ?>

      <!-- Button trigger modal -->
      <button type="button" onclick class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
         modal
      </button>

      <!-- Modal -->
      <div class="modal fade modal-xl" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
         <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
               <div class="modal-header">
                  <h1 class="modal-title fs-5" id="exampleModalLabel"></h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body">
                  <div id="tabla-container" class="container-fluid">
                     <div id="idRowInput" class="row">
                     </div>
                  </div>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
               </div>
            </div>
         </div>
      </div>

      <div class="bottom mt-5">
         <footer class="d-flex flex-wrap justify-content-between align-items-center py-1 my-1">
            <?php include_once('../../../footer.php') ?>
         </footer>
      </div>

      <?php include_once('../../../pie.php'); ?>

   </div>
   <?PHP require_once('../../../pie.php'); ?>
   <?php require_once('modal_pie.php'); ?>
   <?php require_once('modal_encabezado.php'); ?>
   <?PHP require_once('../../../timer_logout.php'); ?>

   <script type="text/javascript" src="<?php echo $appcfg_Dominio; ?>assets/js/objeto.js"></script>
</body>