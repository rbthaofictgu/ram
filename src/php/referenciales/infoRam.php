<?php
/* validando que paso por la pantalla de login */
if (isset($esConsulta) == false) {
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
   $roles_autorizados = ['SA', 'DIGITADOR_VENTANILLA_RA', 'OFICIAL_JURIDICO_RA', 'SUPERVISOR_RA'];
   //*********************************************************************/
   //* Validaciones de seguridad
   //*********************************************************************/
   include_once('../../../validar_seguridad.php');
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
      <input type="text" id="id_user" value="<?php echo isset($_SESSION['user_name']); ?>" hidden>
      <input type="text" id="esConsulta" value="<?php echo isset($esConsulta); ?>" hidden>
      <div class="p-4 mb-5 mt-5">
         <?php if (isset($esConsulta) == True) { ?>
            <h5>
               <strong>
                  <i class="far fa-edit" aria-hidden="true"></i>
                  <?php echo isset($tituloConsulta) ? $tituloConsulta : ''; ?>
               </strong>
            </h5>
         <?php } else { ?>
            <h5>
               <strong>
                  <i class="far fa-edit" aria-hidden="true"></i>
                  <?php echo isset($tituloIngreso) ? $tituloIngreso : ''; ?>
               </strong>
            </h5>
         <?php } ?>
         <!-- botones de estados -->
         <div id="buttonContainer" class="mt-2 mb-2"></div>

         <div id="id_filter" class="row">
            <label for=""> <strong> <i class="fa-solid fa-magnifying-glass-chart"></i> SELECCIONE EL TIPO DE BÚSQUEDA </strong></label>
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
            <!-- Modal Bootstrap -->
            <div class="modal fade" id="modalDescripcion" tabindex="-1" aria-labelledby="modalDescripcionLabel" aria-hidden="true">
               <div class="modal-dialog modal-sm"> <!-- modal-sm lo hace pequeño -->
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="modalDescripcionLabel">Escribe una descripción</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                     </div>
                     <div id="idBody" class="modal-body">
                        <textarea id="descripcionInput" class="form-control" rows="4" style="text-transform: uppercase;"></textarea>
                     </div>
                     <div class="modal-footer">
                        <button id="guardarDescripcion" class="btn btn-primary">CAMBIAR ESTADO</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CERRAR</button>
                     </div>
                  </div>
               </div>
            </div>


            <div id='botones' class="col-4">
               <!--  -->
               <button id="idInputBuscar" class="btn btn-primary" title="Boton para realizar la busqueda de solicitud" onclick="vista_data();">
                  <i class="fa-duotone fa-solid fa-magnifying-glass"></i> BUSCAR
               </button>
               <button id="idInputLimpiar" class="btn btn-warning" title="Boton para Limpiar busqueda" onclick="limpiar();">
                  <i class="fas fa-backspace"></i> LIMPIAR
                  <!-- LIMPIAR -->
               </button>
               <!-- <i  class="fa-solid fa-square-plus"></i> -->

               <button id="idAgregar" data-info1="IDE-7" class="btn btn-success" title="Boton para Agregar una solicitud" onclick="agregarRams();">
                  <i class="fa-solid fa-plus fs-5"></i> NUEVO
               </button>


               <button id="idPagado" data-pagado="ramsPagadas" class="btn btn-light" title="Boton para filtrar pagados" onclick="ramPagada()">
                  <i class="fa-solid fa-dollar-sign"></i>
               </button>




            </div>
         </div>
         <div class="container_fluid" id="tabla-container"></div>
         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
      <script type="text/javascript" src="<?php echo $appcfg_Dominio; ?>assets/js/tabla_dinamica.js"></script>
      <script>
         <?php
         include_once('../../../../assets/js/openModalLogin.js');
         include_once('../../../../assets/js/login.js');
         ?>
      </script>
      <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>;
      <!-- Tom Select CSS -->
      <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet" />
      <!-- Tom Select JS -->
      <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
      <script type="text/javascript" src="<?php echo $appcfg_Dominio; ?>assets/js/select2Inicializar,js"></script>
      <script type="text/javascript" src="<?php echo $appcfg_Dominio; ?>assets/js/estados_btn.js"></script>
      <script type="text/javascript" src="<?php echo $appcfg_Dominio; ?>assets/js/tabla_dinamica.js"></script>
</body>