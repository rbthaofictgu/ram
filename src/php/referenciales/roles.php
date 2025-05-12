<?php
/* validando que paso por la pantalla de login */
session_start();

//*********************************************************************/
//* Esta variable sirve para que todos los programas ubiquen el codigo
//* include_once('validar_roles.php'); 
//* Ejemplos de los valores que puede llevar nivel
//* ''
//* '../'
//* '../../'
//* '../../../'
//*********************************************************************/
$nivel_validar_roles = '../../../';
$roles_autorizados = ['SUPERVISOR_RA','SUPERVISOR_VENTANILLA_RA','SA'];
//*********************************************************************/
//* Validaciones de seguridad
//*********************************************************************/
include_once('../../../validar_seguridad.php');
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
               ROLES DEL SISTEMA RENOVACIONES AUTOMÁTICA.
            </strong>
         </h5>

         <div class="container mt-5">
            <div class="row">
               <div class="col-4">
                  <label for="searchInput"><strong></i>ROL:</strong></label>
                  <input type="text" id="idCodigo" data-info="" class="form-control" placeholder="Escribe para buscar..." autocomplete="off" style="text-transform: uppercase;">
               </div>
               <div class="col-4">
                  <label for="searchInput"><strong></i>DESCRIPCIÓN:</strong></label>
                  <input type="text" id="idDescripcion" data-info="" class="form-control" placeholder="Escribe para buscar..." autocomplete="off" style="text-transform: uppercase;">
               </div>
               <div class="col-4">
                  <div class="row align-items-end">
                     <div class="col-auto">
                        <label for="estadoCheck"><strong>ESTADO:</strong></label><br>
                        <input type="checkbox" id="estadoCheck" class="form-check-input mt-1">
                     </div>
                     <div class="col-auto">
                        <button type="button" class="btn btn-success" id="btnAgregarRol" data-bs-toggle="modal" data-bs-target="#modalRol" onclick="enviarDatos()">
                           SALVAR
                        </button>
                     </div>
                  </div>


               </div>

            </div>
            </br>
            <div>
               <h6>
                  <strong>
                     <i class="far fa-edit" aria-hidden="true"></i>
                     LISTA DE ROLES EN EL SISTEMA.
                  </strong>
               </h6>

               <div id="idListRoles">
                  <div id="alertaSinDatos" class="alert alert-warning alert-dismissible fade show d-none" role="alert">
                     <strong>¡Atención!</strong> NO HAY DATOS EN LA TABLA.
                     <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                  </div>

                  <!--  table-bordered -->
                  <table class="  table table-striped table-hover mb-5 table-responsive table-sm" id="tablaRoles" style="width:100%">
                     <thead class="table-primary headTable m-0 p-0">
                        <tr>
                           <th class="text-start">#</th>
                           <th class="text-center">ESTADO</th>
                           <th class="text-start">ROL</th>
                           <th class="text-start">DESCRIPCIÓN</th>
                        </tr>
                     </thead>
                     <tbody id="idTablaRoles">

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
      <script type="text/javascript" src="<?php echo $appcfg_Dominio; ?>assets/js/roles.js"></script>
      <script>
         <?php
         include_once('../../../../assets/js/openModalLogin.js');
         include_once('../../../../assets/js/login.js');
         ?>
      </script>

</body>