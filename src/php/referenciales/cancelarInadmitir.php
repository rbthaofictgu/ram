<?php
session_start();
$nivel_validar_roles = '../../../';
$roles_autorizados = ['SUPERVISOR_RA', 'SA'];
//*********************************************************************/
//* Validaciones de seguridad
//*********************************************************************/
include_once('../../../validar_seguridad.php'); ?>
<!-- //* archivo de conexion a la base de datos -->
<?php require_once('../../../../config/conexion.php'); ?>
<!-- //* archivo de configuracion donde se encuentrar las variables globales. -->
<?php require_once('../../../configuracion/configuracion.php'); ?>
<?php require_once('../../../configuracion/configuracion_js.php'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <?php
   include_once('../../../encabezado.php');
  ?>
   <title>MANTENIMIENTO DE RAZONES DE CANCELACIÓN / INADMISIÓN</title>
</head>
<body>
   <header>
      <?php include_once('../../../menu.php') ?>
   </header>   
   <div class="container">
      <div class="p-4 mt-5">
         <h5>
            <strong>
               <i class="far fa-edit" aria-hidden="true"></i>
               MANTENIMIENTO DE RAZONES DE CANCELACIÓN / INADMISIÓN
            </strong>
         </h5>
      </div>
      <div class="row">
         <form id="formRazon">
            <div class="row m-2">
               <div class=col-4>
                  <input type="hidden" id="id" name="id">
                  <label for="descripcion"><strong> <i class="fas fa-scroll"></i>DESCRIPCIÓN:</strong></label><br>
                  <input type="text" id="descripcion" data-info="" class="form-control" autocomplete="off"
                     style="text-transform: uppercase;" width="50px">
               </div>
               <!-- <div class="col-4">
                  <label for="otro_espeficique"><strong></i>OTRO ESPECIFIQUE:</strong></label><br>
                  <input type="text" id="otro_espeficique" data-info="" class="form-control" autocomplete="off"
                     style="text-transform: uppercase;" width="50%">
               </div> -->
               <div class="col-4 mt-3">
                  <button class="btn btn-success mt-3" type="submit">Guardar</button>
                  <button class="btn btn-danger mt-3" type="button" onclick="resetForm()">Cancelar</button>
               </div>
            </div>
            <div class="row m-2">
               <div class="col ">
                  <label for="aplicaCancelacion"><input class="form-check-input check_trash mr-2" type="checkbox"
                        id="aplicaCancelacion"> Aplica Cancelación </label>

                  <label for="aplicaInadmicionl"><input class="form-check-input check_trash" type="checkbox"
                        id="aplicaInadmicion"> Aplica Inadmición </label>

                  <label for="otro_espeficiquel"><input class="form-check-input check_trash" type="checkbox"
                        id="otro_espeficique"> Aplica Otros</label>

                  <label for="estaActivol"><input class="form-check-input check_trash" type="checkbox"
                        id="estaActivo" checked> Activo</label>
               </div>
            </div>
         </form>
      </div>
      <hr>
      <h5>Listado de Razones Cancelado / Inadmitido</h5>
      <div class="table-responsive mb-5">
         <table id="tablaRazones" class="table table-hover mb-5">
            <thead class="headTable table-primary table table-striped">
               <tr>
                  <th>ID</th>
                  <th class="text-start">DESCRIPCIÓN</th>
                  <th>OTRO</th>
                  <th>CANCELACIÓN</th>
                  <th>INADMICIÓN</th>
                  <th>ACTIVO</th>
                  <th>ACCIÓN</th>
               </tr>
            </thead>

            <tbody></tbody>
         </table>
         <div id="paginacion" class="mt-3"></div>
      </div> <br>
      <div></div>
   </div>

   <div class="bottom mt-5">
      <?php include_once('../../../footer.php') ?>
   </div>
   <?php
   include_once('../../../pie.php');
   include_once('../../../../modal_pie.php');
   ?>
   <script type="text/javascript" src="<?php echo $appcfg_Dominio; ?>assets/js/cancelarIndamitir.js"></script>
</body>
</html>