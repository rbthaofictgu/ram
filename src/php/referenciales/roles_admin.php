<?php
/* validando que paso por la pantalla de login */
session_start();

if (!isset($_SESSION['user_name'])) { //tipo
   $appcfg_page_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
   $_SESSION['url'] = $appcfg_page_url;
   $_SESSION['flashmsg'] = "Favor inicie sesión para poder ingresar al sistema";
   header("location: ../../../index.php");
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
   <title>ROLES_ADMIN</title>
   <!-- //*mandando encabezados -->
   <?php require_once('../../../encabezado_body.php'); ?>
   <?php include_once('../../../menu.php') ?>
</head>

<body>
   <div class="main">
      <!-- //*vista del menu de privilegios -->
      <?php require_once('../../../menu_segun_privilegios.php'); ?>
      <input type="hidden" id="ID_Tipo_Servicio" value="">
      <div class="p-3 mb-5 mt-3 mb-5 pb-5">
         <h5>
            <strong>
               <i class="far fa-edit" aria-hidden="true"></i>
               ROLES DEL SISTEMA.
            </strong>
         </h5>


         <style>
            #usuarioSelect,
            #usuarioSelect option,
            #busqueda {
               text-transform: uppercase;
            }
         </style>

         <div class="container-sm mt-2">
            <label for="usuarioSelect" class="form-label">
               <strong><i class="fa-solid fa-magnifying-glass"></i> ROLES:</strong>
            </label>
            <div class="row">
               <!-- Columna para el select -->
               <div class="col-md-6">
                  <select id="usuarioSelect" class="form-select">
                     <option value="">Seleccione un rol</option>
                  </select>
               </div>
               <!-- Columna para el input -->
               <!-- <div class="col-md-6">
                  <input type="text" id="busqueda" class="form-control" placeholder="Buscar por número de RAM O FSL">
               </div> -->
            </div>
         </div>

         </br>
         <div class="container-fluid">

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
</body>