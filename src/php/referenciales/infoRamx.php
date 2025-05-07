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
   <title>CONSULTAS POR ESTADOS</title>
   <!-- //*mandando encabezados -->
   <?php require_once('../../../encabezado_body.php'); ?>
   <?php require_once('../../../menu.php'); ?>

   <!-- Bootstrap JS -->
</head>

<body>
   <div class="main">
      <!-- //*vista del menu de privilegios -->
      <?php require_once('../../../menu_segun_privilegios.php'); ?>

      <div class="p-3 mb-5 mt-4">
         <h5>
            <strong>
               <i class="far fa-edit" aria-hidden="true"></i>
               CONSULTAS POR ESTADOS
            </strong>
         </h5>

         <div id="buttonContainer" class="mt-2 mb-2"></div>

         <div id="id_filter" class="row">
            <label for=""> <strong> <i class="fa-solid fa-magnifying-glass-chart"></i> SELECCIONE EL TIPO DE BÚSQUEDA</strong></label>
            <div class="col-8">
               <div class="row">
                  <div class="col-5">
                     <select id="id_filtro_select" class="form-select form-select-lg mb-3" aria-label="Large select example">

                     </select>
                  </div>
                  <div class="col-6">
                     <div class="input-group mb-3">
                        <!-- <span class="input-group-text" id="basic-addon1">@</span> -->
                        <input id="id_input_filtro" type="text" class="form-control" placeholder="Ingrese elemento a buscar" aria-describedby="basic-addon1">
                     </div>
                  </div>
               </div>

            </div>
            <div class="col-4">
               <button id="idInputBuscar" class="btn btn-primary" data-bs-toggle="tooltip" title="Boton para realizar la busqueda de solicitud" onclick="vista_data();">
                  <i class="fa-duotone fa-solid fa-magnifying-glass"></i> BUSCAR
               </button>
               <button id="idInputLimpiar" class="btn btn-warning" data-bs-toggle="tooltip" title="Boton para Limpiar busqueda" onclick="limpiar();">
                  <i class="fas fa-broom"></i> LIMPIAR
                  <!-- LIMPIAR -->
               </button>
               <!-- <i  class="fa-solid fa-square-plus"></i> -->
               <button id="idAgregar" class="btn btn-success" data-bs-toggle="tooltip" title="Boton para Agregar una solicitud" onclick="agregarRams();">
                  <i class="fa-solid fa-plus fs-5"></i> NUEVO
               </button>
            </div>
         </div>
         <!-- 
         <div id="tableContainer" class="table-responsive mb-5">
         </div> -->

         <div class="container_fluid" id="tabla-container"></div>
         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

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

   <script type="text/javascript" src="<?php echo $appcfg_Dominio; ?>assets/js/estados_btn.js"></script>
   <script type="text/javascript" src="<?php echo $appcfg_Dominio; ?>assets/js/tabla_dinamica.js"></script>


</body>