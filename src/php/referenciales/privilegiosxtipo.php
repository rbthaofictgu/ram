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
<?php require_once('../../utils/tipo_dispositivo.php'); ?>
<?php require_once('../../../configuracion/configuracion.php'); ?>
<?php require_once('../../utils/anterior_siguiente.php'); ?>
<?php require_once('../../utils/funciones_db.php'); ?>
<?php require_once("../../../../config/conexion.php"); ?>

<?php require_once('../../utils/create_copy_row.php'); ?>
<?php
// Validando privilegios
// $id_privilegio = 'PRIVILEGIOS';
$codigo = 'PRIVILEGIOS';
require_once('../../utils/validar_privilegio.php');
$pantallax = explode('/', $currentPage);
$pantallax = $pantallax[(count($pantallax) - 1)];
$pantallax = explode('.', $pantallax);
$pantalla[0] = $pantallax[0];
$clase = "alert alert-success alert-dismissible";

// Obteniendo el id
$rs_tipo_rs_mantenimiento = "-1";
if (isset($_GET['id'])) {
   $rs_tipo_rs_mantenimiento =  $_GET['id'];
} else {
   if (isset($_POST['id'])) {
      $rs_tipo_rs_mantenimiento =  $_POST['id'];
   }
}

$rotulo = "MANTENIMIENTO:  Agregando " . $tablanext;
// Armando el query
$query_rs_usuario = "SELECT id, descripcion FROM
[IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_tipo_usuario] order by descripcion";

//SELECT id, descripcion from tipo_usuario order by descripcion
// Preparando la sentencia
$usuario = $db->prepare($query_rs_usuario);
// Ejecutanto el query
$res = $usuario->execute();
// Si hay algun error
if (isset($res) and $res == '') {
   $msg = "Error intentando leer tabla de ubicacion, error -> " . geterror($usuario->errorInfo());
   $usuario->closeCursor();
   $totalRows_rs_usuario = 0;
   $clase = "alert alert-danger alert-dismissible";
} else {
   $row_rs_usuario = $usuario->fetchall(\PDO::FETCH_NUM);
   $totalRows_rs_usuario  = $usuario->rowcount();
   $res = $usuario->execute();
   $row_rs_usuario = $usuario->fetch();
}

// Si no se ha realizando una eleccion de un tipo de usuario por el usuario se utiliza el primer tipo de usuario en la tabla
if ($rs_tipo_rs_mantenimiento < 1) {
   $rs_tipo_rs_mantenimiento = $row_rs_usuario['id'];
}

// Armando el query
$query_rs_privilegio = "SELECT a.id, a.descripcion, a.menu_padre 
from [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Privilegio] a 
where (select count(*) from [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_privilegio_x_tipo_usuario]  b  where b.id_tipo_usuario = :id_tipo_usuario and a.id = b.codigo) = 0 order by a.nivel_menu";

//SELECT a.id, a.descripcion, a.menu_padre from privilegio a where (select count(*) from privilegio_x_tipo_usuario b where b.id_tipo_usuario = :id_tipo_usuario and a.id = b.id_privilegio) = 0 order by a.nivel_menu

// Preparando la sentencia
$privilegio = $db->prepare($query_rs_privilegio);
// Ejecutanto el query
$res = $privilegio->execute(array('id_tipo_usuario' => $rs_tipo_rs_mantenimiento));
// Si hay algun error
if (isset($res) and $res == '') {
   $msg = "Error intentando leer tabla de ubicacion, error -> " . geterror($privilegio->errorInfo());
   $privilegio->closeCursor();
   $totalRows_rs_privilegio = 0;
   $clase = "alert alert-danger alert-dismissible";
} else {
   $row_rs_privilegio = $privilegio->fetchall(\PDO::FETCH_NUM);
   $totalRows_rs_privilegio  = $privilegio->rowcount();
   $res = $privilegio->execute();
   $row_rs_privilegio = $privilegio->fetch();
}

// Armando el query
$query_rs_privilegioxusuario = "SELECT a.id, a.descripcion,a.menu_padre from [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Privilegio] a, [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_privilegio_x_tipo_usuario] b where a.id = b.codigo and b.id_tipo_usuario = :id_tipo_usuario  order by a.nivel_menu";

// SELECT a.id, a.descripcion,a.menu_padre from privilegio a, privilegio_x_tipo_usuario b where a.id = b.id_privilegio and b.id_tipo_usuario = :id_tipo_usuario  order by a.nivel_menu

// Preparando la sentencia
$privilegioxusuario = $db->prepare($query_rs_privilegioxusuario);
// Ejecutanto el query
$res = $privilegioxusuario->execute(array('id_tipo_usuario' => $rs_tipo_rs_mantenimiento));
// Si hay algun error
if (isset($res) and $res == '') {
   $msg = "Error intentando leer tabla de ubicacion, error -> " . geterror($privilegioxusuario->errorInfo());
   $privilegioxusuario->closeCursor();
   $totalRows_rs_privilegioxusuario = 0;
   $clase = "alert alert-danger alert-dismissible";
} else {
   $row_rs_privilegioxusuario = $privilegioxusuario->fetchall(\PDO::FETCH_NUM);
   $totalRows_rs_privilegioxusuario  = $privilegioxusuario->rowcount();
   $res = $privilegioxusuario->execute();
   $row_rs_privilegioxusuario = $privilegioxusuario->fetch();
}

?>
<!doctype html>
<html lang="es">

<head>
   <title>MANTENIMIENTO <?php echo strtoupper($tablanext); ?></title>
   <?php require_once('../../../encabezado_body.php'); ?>
</head>

<body>
   <div class="main">
      <?php require_once('../../../menu_segun_privilegios.php'); ?>
      <form id="form1" name="form1" method="post" action="<?php echo $currentPage; ?>">
         <?php require_once('../../../rotulo_pantalla.php'); ?>
         <div class="table-responsive gy_barra_botones">
            <table width="100%" align="center">
               <tr>
                  <td width="8.5%"></td>
                  <td width="91.5%">
                     <div align="left">
                        <button type="button" class="btn btn-success btn-sm" id="cmd_salvar" name="cmd_salvar"
                           onclick="f_validar_agregar(this.form.dwd_tipo.value)">
                           <i class="fas fa-gavel"></i>
                           <span class="hidden-xs">Otorgar</span>
                        </button>
                        <button type="button" class="btn btn-light btn-sm" id="cmd_tipo" name="cmd_tipo"
                           onclick="f_validar_borrar(this.form.dwd_tipo.value)">
                           <i class="fas fa-trash-alt"></i>
                           <span class="hidden-xs">Quitar</span>
                        </button>

                        <button type="button" class="btn btn-light btn-sm" id="cmd_limpiar" name="cmd_cancelar"
                           onclick="window.location='<?PHP echo $currentPage; ?>'">
                           <i class="fas fa-undo-alt"></i>
                           <span class="hidden-xs">Limpiar</span>
                        </button>

                        <button type="button" class="btn btn-light btn-sm" id="cmd_retornar" name="cmd_retornar"
                           onclick="window.location='<?PHP echo $pantalla[0]; ?>_lista.php'">
                           <i class="far fa-window-close"></i>
                           <span class="hidden-xs">Cerrar</span>
                        </button>
                     </div>

                  </td>
               </tr>
            </table>
         </div>
         <?php
         $detalle = 'INFORMACIÓN DE ' . $tablanext;
         require_once('../../../detalle_pantalla.php'); ?>
         <div class="table-responsive">
            <table width="100%" align="center" cellpadding="5" cellspacing="2">
               <td width="1%"></td>
               <td width="98%">
                  <br />
                  <div id="msg-completo">
                     <?PHP
                     if (isset($msg) and $msg <> "") {
                     ?>
                        <div id="msg-global" class="<?PHP echo $clase; ?>" role="alert">
                           <button type="button" class="close" data-dismiss="alert"><span
                                 aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                           <div id="msg-error">
                              <?PHP echo $msg; ?>
                           </div>
                        </div>
                     <?PHP  } ?>
                  </div>
               </td>
               <td width="1%"></td>
               </tr>
            </table>
         </div>
         <div class="table-responsive">
            <table class="table table-condensed mb-5">
               <tr>
                  <td width="8%">&nbsp;</td>
                  <td width="20%"><label class="tooltip-test" data-placement="top"
                        data-original-title="Seleccione el tipo de usuario al cúal le desea trabajar los privilegios">
                        <div id="label-nombre"><strong>ROL DE USUARIO:</strong></div>
                     </label></td>
                  <td width="72%"><select name="dwd_tipo" id="dwd_tipo" class="form-control"
                        onchange="f_cargar(this.value)">
                        <?PHP
                        for ($i = 0; $totalRows_rs_usuario > $i; $i++) { ?>
                           <option value="<?php echo $row_rs_usuario['id']; ?>" <?php if (!(strcmp($row_rs_usuario['id'], $rs_tipo_rs_mantenimiento))) {
                                                                                    echo "selected=\"selected\"";
                                                                                 }
                                                                                 ?>><?php echo $row_rs_usuario['descripcion']; ?>
                           </option>
                        <?PHP
                           $row_rs_usuario = $usuario->fetch();
                        } ?>
                     </select></td>
               </tr>
               <tr>
                  <td>&nbsp;</td>
                  <td><label class="tooltip-test" data-placement="top"
                        data-original-title="Seleccione los privilegios que desea otorgarle al tipo de usuario"><strong>PRIVILEGIOS</strong></label>
                  </td>
                  <td><label class="tooltip-test" data-placement="top"
                        data-original-title="Aqui apareceran los privilegios que le han sido asignados al tipo de usuario seleccionado"><strong>PRIVILEGIOS
                           POR USUARIO</strong></label>
                  </td>
               </tr>
               <tr>
                  <td>&nbsp;</td>
                  <td>
                     <div align="left">
                        <select name="lst_privilegio" id="lst_privilegio" size="<?PHP echo $totalRows_rs_privilegio; ?>"
                           multiple="multiple">
                           <?PHP
                           for ($i = 0; $totalRows_rs_privilegio > $i; $i++) { ?>
                              <?PHP if ($row_rs_privilegio['menu_padre'] == '') { ?>
                                 <option class="text-info" value="<?php echo $row_rs_privilegio['id']; ?>">
                                    <?php echo $row_rs_privilegio['descripcion']; ?></option>
                              <?PHP } else { ?>
                                 <option class="text-success" value="<?php echo $row_rs_privilegio['id']; ?>">
                                    &nbsp;&nbsp;&nbsp; <?php echo $row_rs_privilegio['descripcion']; ?></option>
                              <?PHP }  ?>
                           <?PHP
                              $row_rs_privilegio = $privilegio->fetch();
                           } ?>
                        </select>
                     </div>
                  </td>
                  <td>
                     <div align="left">
                        <select name="lst_privilegioxusuario" id="lst_privilegioxusuario"
                           size="<?PHP echo $totalRows_rs_privilegioxusuario; ?>" multiple="multiple">
                           <?PHP
                           for ($i = 0; $totalRows_rs_privilegioxusuario > $i; $i++) { ?>
                              <?PHP if ($row_rs_privilegioxusuario['menu_padre'] == '') { ?>
                                 <option class="text-info" value="<?php echo $row_rs_privilegioxusuario['id']; ?>">
                                    <?php echo $row_rs_privilegioxusuario['descripcion']; ?></option>
                              <?PHP } else { ?>
                                 <option class="text-success" value="<?php echo $row_rs_privilegioxusuario['id']; ?>">
                                    &nbsp;&nbsp;&nbsp;<?php echo $row_rs_privilegioxusuario['descripcion']; ?></option>
                              <?PHP }  ?>

                           <?PHP
                              $row_rs_privilegioxusuario = $privilegioxusuario->fetch();
                           } ?>
                        </select>
                     </div>

                  </td>
               </tr>
            </table>
         </div>

         <?php if (isset($row_rs_mantenimiento['id'])) { ?>
            <input type="hidden" name="MM_update" id="MM_update" value="frm_mantenimiento" />
            <input type="hidden" name="id" id="id" value="<?php echo $rs_id_rs_mantenimiento; ?>" />
         <?php } else { ?>
            <input type="hidden" name="MM_insert" id="MM_insert" value="frm_mantenimiento" />
         <?php }  ?>
      </form>
      <div class="bottom mt-5">
         <footer class="d-flex flex-wrap justify-content-between align-items-center py-1 my-1">
            <?php include_once('../../../footer.php') ?>
         </footer>
      </div>

      <?php
      include_once('../../../pie.php');
      // include_once('../../../../sattb/modal_pie.php');
      ?>

      <script src="<?PHP echo $appcfg_Dominio; ?>js/administracion_usuario_lista.js">
      </script>
      <script src="<?PHP echo $appcfg_Dominio; ?>js/expresiones_regulares.js"></script>
      <!-- <?php
            // include_once('../../../../sattb/assets/js/openModalLogin.js');
            // include_once('../../../../sattb/assets/js/login.js');
            ?> -->


   </div>

</body>
<script src="<?PHP echo $appcfg_Dominio; ?>js/expresiones_regulares.js"></script>
<?php
$usuario->closeCursor();
$privilegio->closeCursor();
$privilegioxusuario->closeCursor();
?>

<?php require_once('modal_pie.php'); ?>
<?php require_once('modal_encabezado.php'); ?>

</html>