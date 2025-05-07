<?php
/* validando que paso por la pantalla de login */
session_start();
if (!isset($_SESSION['user_name'])) { //tipo
   $appcfg_page_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
   $_SESSION['url'] = $appcfg_page_url;
   $_SESSION['flashmsg'] = "Favor inicie sesión para poder ingresar al sistema";
   header("location: ../../../inicio.php");
   exit();
}

include_once('../../../validar_roles.php');

if (!array_intersect(['SUPERVISOR_RA', 'SUPERVISOR_VENTANILLA_RA'], $_SESSION["ROLESXUSUARIORAM"])) {
   $appcfg_page_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
   $_SESSION['url'] = $appcfg_page_url;
   $_SESSION['flashmsg'] = "No tiene permisos para acceder a esta pantalla (ROLES_X_USUARIOS)";
   header("location: ../../../inicio.php");
   exit();
}

//*configuración del sistema
include_once('../../../configuracion/configuracion.php');
//*configuración del las variables globales del sistema
include_once('../../../configuracion/configuracion_js.php');
?>
<!DOCTYPE html>

<head>
   <?php
   include_once('../../../encabezado.php');
   ?>
</head>

<body>
   <header>
      <?php include_once('../../../menu.php') ?>
   </header>
   <div class="main">
      <div class="p-4 mb-5 mt-5">
         <h5>
            <strong>
               <i class="far fa-edit" aria-hidden="true"></i>
               ROLES POR USUARIOS.
            </strong>
         </h5>

         <div class="container mt-2">
            <div class="row mb-3">
               <div class="col-8">
                  <label for="searchInput"><strong> <i class="fa-solid fa-magnifying-glass"></i>BUSCAR USUARIO:</strong></label>
                  <input type="text" id="searchInput" data-info="" class="form-control" placeholder="Escribe para buscar..." autocomplete="off" styles="text-transform: uppercase;">
                  <div class="col-8" id="autocomplete-list" class="autocomplete-items"></div>
               </div>
               <div class="col-4 mt-3">
                  <button id="idInputLimpiar" class="btn btn-warning mt-3" title="Boton para Limpiar busqueda" onclick="limpiar();">
                     <i class="fas fa-backspace"></i> LIMPIAR
                     <!-- LIMPIAR -->
                  </button>
               </div>
            </div>

            </br>
            <div>
               <h6>
                  <strong>
                     <i class="far fa-edit" aria-hidden="true"></i>
                     LISTA DE ROLES.
                  </strong>
               </h6>

               <div id="idListRoles">
                  <div id="alertaSinDatos" class="alert alert-warning alert-dismissible fade show d-none" role="alert">
                     <strong>¡Atención!</strong> EL USUARIO NO TIENE NINGUN ROL ASIGNADO POR EL MOMENTO.
                     <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                  </div>

                  <table id="tablaRoles" border="1" class="table table-striped table-hover mb-5 table-responsive table-sm" style="width:100%">
                     <thead class="table-primary headTable m-0 p-0">
                        <tr>
                           <th class="text-center">#</th>
                           <th class="text-center">MARCAR</th>
                           <th class="text-start">CÓDIGO</th>
                           <th class="text-start">DESCRIPCIÓN</th>
                        </tr>
                     </thead>
                     <tbody>
                        <!-- Aquí se insertarán las filas dinámicamente -->
                     </tbody>
                  </table>


               </div>

            </div>

         </div>



      </div>

      <div class="bottom">
         <footer class="d-flex flex-wrap justify-content-between align-items-center py-1 my-1">
            <?php include_once('../../../footer.php') ?>
         </footer>
      </div>
      <?php
      include_once('../../../pie.php');
      include_once('../../../../modal_pie.php');
      ?>
      <script type="text/javascript" src="<?php echo $appcfg_Dominio; ?>assets/js/roles_x_usuario.js"></script>
      <script>
         <?php
         include_once('../../../../assets/js/openModalLogin.js');
         include_once('../../../../assets/js/login.js');
         ?>
      </script>
      <script src="https://cdn.jsdelivr.net/npm/@algolia/autocomplete-js"></script>
</body>