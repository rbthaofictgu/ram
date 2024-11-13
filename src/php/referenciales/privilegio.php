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
<!-- </?php require_once("../../logs/logs.php"); ?> -->
<!-- //*archivo encargado de recuperar el siguiente id de la tabla anterior o siguiente  -->
<?php require_once('../../utils/anterior_siguiente.php'); ?>
<!-- //* archivo de funciones varias(recuperar url,intentos fallidos,validar accesos, etc.) -->
<?php require_once('../../utils/funciones_db.php'); ?>
<!-- //*funcion que permite validar privilegio a acceder a cada opción. -->
<?php require_once('../../utils/create_copy_row.php'); ?>

<?php require_once('../../utils/tipo_dispositivo.php'); ?>
<!-- //*archivo de configuracion de lña conexion con la base de datos -->
<?php require_once('../../../../config/conexion.php'); ?>
<!-- //* archivo de configuracion donde se encuentrar las variables globales. -->
<?php require_once('../../../configuracion/configuracion.php'); ?>
<?php
//*codigo del privilegio a cotejat en tabla.
// $id_privilegio = 'PRIVILEGIOS';
$codigo = 'PRIVILEGIOS';
// * archivo encargado de verificar si tienen permiso otorgado 
require_once('../../utils/validar_privilegio.php');
//*obteniendo el nombre de la ruta actual
$pantallax = explode('/', $currentPage);
//*obteniendo el nombre o el ultimo elemento del la ruta
$pantallax = $pantallax[(count($pantallax) - 1)];
//*eliminando la extension del archivo
$pantallax = explode('.', $pantallax);
//*asigna el nombre de la paguina sin extension.
$pantalla[0] = $pantallax[0];
$clase = 'alert alert-success alert-dismissible';
//*inicializando el id en -.1
$rs_id_rs_mantenimiento = -1;
//*validar si existe el id y asignando si existe;
if (isset($_GET['id'])) {
   $rs_id_rs_mantenimiento =  $_GET['id'];
} else {
   if (isset($_POST['id'])) {
      $rs_id_rs_mantenimiento =  $_POST['id'];
   }
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "frm_mantenimiento")) {
   //* Si se marco como encabezado el privilegio
   $encabezado = 'N';
   if (isset($_POST['chk_encabezado'])) {
      $encabezado = 'S';
   }
   //* Si se marco como visible en menú
   $visible = 'N';
   if (isset($_POST['chk_visible'])) {
      $visible = 'S';
   }
   //* Si se marco que la pagina a abrir es modal
   $modal = 'N';
   if (isset($_POST['chk_modal'])) {
      $modal = 'S';
   }
   //* Si se marco si se debe agrergar el domino al link
   $usar_dominio = 'N';
   if (isset($_POST['chk_dominio'])) {
      $usar_dominio = 'S';
   }
   //*********************************************************** */
   //*Armando query para actaulizar usuario
   //*********************************************************** */
   $updateSQL = "UPDATE [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Privilegio] 
   SET
      modal = :modal,
      usar_dominio = :usar_dominio,
      link_target = :link_target,
      rotulo = :rotulo,
      pagina_movil = :pagina_movil,
      icono = :icono,
      tabla = :tabla,
      visible = :visible,
      es_encabezado = :es_encabezado,
      menu_padre = :menu_padre,
      nivel_menu = :nivel_menu,
      pagina = :pagina,
      descripcion = :descripcion,
      estado = :estado,
      usuario_modificacion = :usuario_modificacion,
      fecha_modificacion = GETDATE(),  -- Cambié 'now()' por 'GETDATE()' que es la función de SQL Server
      ip_modificacion = :ip_modificacion,
      host_modificacion = :host_modificacion  
   WHERE id = :id;";
   //*Comensando la transacción
   $db->beginTransaction();
   //*Preparando ejecución de sentencia SQL
   $stmt = $db->prepare($updateSQL);
   //*Ejecutanto sentencia SQL
   $res = $stmt->execute(array(
      ':modal' => $modal,
      ':usar_dominio' => $usar_dominio,
      ':link_target'  => $_POST['dwd_target'],
      ':rotulo'  => $_POST['txt_rotulo'],
      ':pagina_movil'  => $_POST['txt_paginamovil'],
      ':icono' => $_POST['txt_icono'],
      ':tabla' => $_POST['txt_tabla'],
      ':es_encabezado' => $encabezado,
      ':visible' => $visible,
      ':menu_padre' => strtoupper($_POST['dwd_encabezado']),
      ':nivel_menu' => strtoupper($_POST['txt_menu']),
      ':pagina' => $_POST['txt_pagina'],
      ':descripcion' => $_POST['txt_descripcion'],
      ':estado' => $_POST['dwd_estado'],
      ':id' => $rs_id_rs_mantenimiento,
      ':usuario_modificacion' => $_SESSION['user_name'],
      ':ip_modificacion' => $ip,
      ':host_modificacion' => $host
   ));
   //* Validando que el error de la ejecución si existe
   if (isset($res) and $res == '') {
      $msg = "Error intentando actualizar registro, error -> " . geterror($stmt->errorInfo());
      //*enviando estilo para elñ error */
      $clase = 'alert alert-danger alert-dismissible';
      //*liberando memoria de sql server
      $stmt->closeCursor();
      //*desaciendo cambios
      $db->rollBack();
   } else {
      //*liberando memoria de sql server
      $stmt->closeCursor();
      //*confirmando transaccion en BD
      $db->commit();
      //* Recarga de pantalla con mensaje de procesamiento satisfactorio
      if ((isset($_POST["cmd_salvar_close"]))) {
         $pagina_html = $pantalla[0] . '_lista.php?msg=<strong>Bien Hecho!</strong><br \>El registro ha sido actualizado satisfactoriamente';
         header(sprintf("Location: %s", $pagina_html));
      } else {
         if ((isset($_POST["cmd_salvar_nuevo"]))) {
            $msg = "<strong>Bien Hecho!</strong><br \>El registro ha sido actualizado satisfactoriamente";
            $rs_id_rs_mantenimiento = -1;
         } else {
            if ((isset($_POST["cmd_salvar"]))) {
               $msg = "<strong>Bien Hecho!</strong><br \>El registro ha sido actualizado satisfactoriamente";
            } else {
               $msg = "<strong>Bien Hecho!</strong><br \>El registro ha sido actualizado satisfactoriamente";
               $copy = f_create_copy_row($tablanext, $rs_id_rs_mantenimiento, $db);
            }
         }
      }
   }
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frm_mantenimiento")) {
   //* Si se marco como encabezado el privilegio
   $encabezado = 'N';
   if (isset($_POST['chk_encabezado'])) {
      $encabezado = 'S';
   }
   //* Si se marco como visible en menú
   $visible = 'N';
   if (isset($_POST['chk_visible'])) {
      $visible = 'S';
   }
   //* Si se marco que la pagina a abrir es modal
   $modal = 'N';
   if (isset($_POST['chk_modal'])) {
      $modal = 'S';
   }
   //* Si se marco si se debe agrergar el domino al link
   $usar_dominio = 'N';
   if (isset($_POST['chk_dominio'])) {
      $usar_dominio = 'S';
   }
   //*********************************************************** */
   //* (Inicio) Armando query para insertar registro
   //************************************************************ */
   $insertSQL = "INSERT INTO [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Privilegio]  (
	modal,
	usar_dominio,
	link_target,
	rotulo,
	pagina_movil,
	icono,
	tabla,
	es_encabezado,	
	visible,
	codigo,
	descripcion,
	estado,
	nivel_menu,
	pagina,
	menu_padre,
	usuario_creacion,
	fecha_creacion,
	ip_creacion,
	host_creacion)

 VALUES (
    :modal,:usar_dominio,:link_target,:rotulo,:pagina_movil,:icono,:tabla,:es_encabezado,:visible,:codigo,:descripcion,:estado,:nivel_menu,:pagina,:menu_padre,:usuario_creacion,
    GETDATE(),  -- Cambié 'now()' por 'GETDATE()'
    :ip_creacion,
    :host_creacion
);";
   //*Comensando la transacción
   $db->beginTransaction();
   //* Preparando la sentencia
   $stmt = $db->prepare($insertSQL);
   //*
   $res = $stmt->execute(array(
      ':modal' => $modal,
      ':usar_dominio' => $usar_dominio,
      ':link_target'  => $_POST['dwd_target'],
      ':rotulo'  => $_POST['txt_rotulo'],
      ':pagina_movil'  => $_POST['txt_paginamovil'],
      ':icono' => $_POST['txt_icono'],
      ':tabla' => $_POST['txt_tabla'],
      ':es_encabezado' => $encabezado,
      ':visible' => $visible,
      ':codigo' => strtoupper($_POST['txt_codigo']),
      ':descripcion' => $_POST['txt_descripcion'],
      ':estado' => $_POST['dwd_estado'],
      ':nivel_menu' => strtoupper($_POST['txt_menu']),
      ':pagina' => $_POST['txt_pagina'],
      ':menu_padre' => strtoupper($_POST['dwd_encabezado']),
      ':usuario_creacion' => $_SESSION['user_name'],
      ':ip_creacion' => $ip,
      ':host_creacion' => $host
   ));
   //* Validando que el error de la ejecución si existe
   $res = $stmt->errorInfo();
   if (isset($res) and $res == '') {
      $msg = "Error intentando insertar registro, error -> " . geterror($stmt->errorInfo());
      //*enviando estilo de error.
      $clase = 'alert alert-danger alert-dismissible';
      //*liberando memoria de sql server
      $stmt->closeCursor();
      //*desaciendo transacción
      $db->rollBack();
   } else {
      //*liberando memoria de sql server
      $stmt->closeCursor();
      //*recuperando el id del registro ingresado.
      $rs_id_rs_mantenimiento = $db->lastInsertId();
      //*confirmando transaccion en BD
      $db->commit();
      //* Recarga de pantalla con mensaje de procesamiento satisfactorio
      if ((isset($_POST["cmd_salvar_close"]))) {
         $pagina_html = $pantalla[0] . '_lista.php?msg=<strong>Bien Hecho!</strong><br \>El registro ha sido insertado satisfactoriamente';
         header(sprintf("Location: %s", $pagina_html));
      } else {
         if ((isset($_POST["cmd_salvar_nuevo"]))) {
            $msg = "<strong>Bien Hecho!</strong><br \>El registro ha sido insertado satisfactoriamente";
            $rs_id_rs_mantenimiento = -1;
         } else {
            if ((isset($_POST["cmd_salvar"]))) {
               $msg = "<strong>Bien Hecho!</strong><br \>El registro ha sido insertado satisfactoriamente";
            } else {
               $msg = "<strong>Bien Hecho!</strong><br \>El registro ha sido insertado satisfactoriamente";
               $copy = f_create_copy_row($tablanext, $rs_id_rs_mantenimiento, $db);
            }
         }
      }
   }
}

//************************************************************* */
//* preparando query de la tabla de privilegio menu.
//************************************************************* */
$query_rs_encabezado = "SELECT a.id, nivel_menu,descripcion
 FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Privilegio] a
  where a.es_encabezado = 'S' 
  and a.estado = 'A' and a.descripcion != '' order by descripcion";

//  "SELECT a.id, nivel_menu,descripcion FROM privilegio a where a.es_encabezado = 'S' and a.estado = 'A' and a.descripcion != '' order by descripcion";
//*preparando query
$encabezado = $db->prepare($query_rs_encabezado);
//*ejecutando query
$res = $encabezado->execute();
//*obteniendo datos
$row_rs_encabezado = $encabezado->fetch();
//*obteniendo total de filas afectadas
$totalRows_rs_encabezado = $encabezado->rowcount();
//*nombre del titulo con la tabla modificada o afectada
$rotulo = "MANTENIMIENTO:  Agregando " . $tablanext;

//*************************************************************************** */
//* armando query de la tabla de privilegio con el id espesifico.
//************************************************************************** */
$query_rs_mantenimiento = "SELECT a.*
FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Privilegio] a WHERE id = :id";
//* Preparando la sentencia
$mantenimiento = $db->prepare($query_rs_mantenimiento);
//* Ejecutanto el query
$mantenimiento->execute(array(':id' => $rs_id_rs_mantenimiento));
//* Si hay algun error
$res = $mantenimiento->errorInfo();
if (isset($res) and $res[2] <> '') {
   $msg = "Error intentando leer tabla " . $tablanext   . ", error -> " . $res[2] . $res[1] . $res[0];
   //* limpiando memoria sql server
   $mantenimiento->closeCursor();
   //* asignando id como 0
   $totalRows_rs_mantenimiento = 0;
   //* asignando estilo de la alerta de error.
   $clase = 'alert alert-danger alert-dismissible';
} else {
   //* Sino hay error carga el 1er registro	
   $row_rs_mantenimiento = $mantenimiento->fetch();
   //* Contador de registros de estados afectados
   $totalRows_rs_mantenimiento = $mantenimiento->rowcount();
   //* si es mayor a o el numero de filas afectadas se asigana
   if ($totalRows_rs_mantenimiento > 0) {
      $rotulo = "MANTENIMIENTO: Editando " . $tablanext;
   }
}
//  var_dump($row_rs_mantenimiento);
if ((isset($_POST["cmd_salvar_copy"]))) {
   $rs_id_rs_mantenimiento = -1;
   $row_rs_mantenimiento['descripcion'] = $row_rs_mantenimiento['descripcion'] . ' Copia' . $copy;
}

?>
<!DOCTYPE html>

<head>
   <title>MANTENIMIENTO <?php echo strtoupper($tablanext); ?></title>
   <?php require_once('../../../encabezado_body.php'); ?>
</head>

<body>
   <div class="main">
      <?php require_once('../../../menu_segun_privilegios.php'); ?>

      <form id="form1" name="form1" method="post" action="<?php echo $currentPage; ?>">
         <?php require_once('../../../rotulo_pantalla.php'); ?>
         <?php
         $botones = 'M';
         require_once('../../../botones.php');
         ?>
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
                     <div id="msg-global" class="<?PHP echo $clase; ?> " role="alert">
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
         <div class="table-responsive mb-5">
            <div id="tabla_mantenimiento" class="col-md-5 mb-5">
               <table class="table table-unbordered">
                  <tr>
                     <td width="8%">&nbsp;</td>
                     <td width="20%">
                        <label id="label-id" data-toggle="tooltip" data-placement="top"
                           title="Ingrese aqui el ID del registro, solo caracteres(a-z,A-Z), solo disponible en opción de agregar">
                           <div align="left">ID*</div>
                        </label>
                     </td>
                     <td width="72%"><label for="txt_codigo"></label>
                        <?PHP if (isset($copy) == true or !isset($row_rs_mantenimiento['estado']) == true) { ?>
                        <input type="text" class="form-control" id="txt_codigo" name="txt_codigo"
                           value="<?php echo $row_rs_mantenimiento['codigo'] ?? ''  ?>" maxlength="30"
                           onchange="f_validar_privilegio(<?PHP echo $rs_id_rs_mantenimiento; ?>)">
                        <label for="txt_demo"></label>
                        <?php } else { ?>
                        <input readonly type="text" class="form-control" id="txt_codigo" name="txt_codigo"
                           value="<?php echo $row_rs_mantenimiento['codigo']; ?>" maxlength="30"
                           onchange="f_validar_usuario()">
                        <label for="txt_demo"></label>
                        <?php }  ?>
                  </tr>
                  <tr>
                     <td>&nbsp;</td>
                     <td>
                        <label id="label-descripcion" data-toggle="tooltip" data-placement="top"
                           title="Introduzca la descripción del privilegio, es el texto que aparecera en el menú">
                           <div align="left">Descripción*</div>
                        </label>

                     </td>
                     <td>
                        <textarea rows="5" maxlength="300" required size="100" class="form-control" id="txt_descripcion"
                           name="txt_descripcion"
                           placeholder=""><?php echo $row_rs_mantenimiento['descripcion'] ?? '' ?></textarea>
                     </td>
                  </tr>
                  <tr>
                     <td>&nbsp;</td>
                     <td>
                        <label maxlength="15" id="label-menu" data-toggle="tooltip" data-placement="top"
                           title="Indique el orden en que desea aparezca el menu en la pantalla">
                           <div align="left">Orden Menú *</div>
                        </label>
                     </td>
                     <td><label for="txt_menu"></label>
                        <input class="form-control" type="text" name="txt_menu" id="txt_menu"
                           value="<?php echo $row_rs_mantenimiento['nivel_menu'] ?? ''; ?>"
                           maxlength="20" />
                     </td>
                  </tr>
                  <tr>
                     <td>&nbsp;</td>
                     <td>

                        <label id="label-menuencabezado" data-toggle="tooltip" data-placement="top"
                           title="Indique el menú padre en e menú para este privilegio">
                           <div align="left">Menú Padre</div>
                        </label>

                     </td>

                     <td>
                        <div class="input-group mb-3">
                           <div class="input-group-prepend">
                              <label class="input-group-text" for="inputGroupSelect01">Opciones</label>
                           </div>

                           <select name="dwd_encabezado" id="dwd_encabezado" class="form-control">

                              <option value="0" <?php if ( isset($row_rs_mantenimiento['menu_padre']) && is_array($row_rs_mantenimiento['menu_padre']) && (0 == $row_rs_mantenimiento['menu_padre']) ) {
                                                   echo "selected=\"selected\"";
                                                } ?>>Seleccione el menu encabezado de esta opción</option>
                              <?PHP
                              for ($i = 0; $totalRows_rs_encabezado > $i; $i++) { ?>
                              <option
                                 value="<?php echo $row_rs_encabezado['id'] ?? ''; ?>"
                                 <?php if ($row_rs_encabezado['id'] == $row_rs_mantenimiento['menu_padre']) {
                                    echo "selected=\"selected\"";
                                 } ?>>
                                 <?php echo $row_rs_encabezado['descripcion'] ?? ''; ?>
                              </option>
                              <?PHP
                                 $row_rs_encabezado = $encabezado->fetch();
                              } ?>
                           </select>
                        </div>
                     </td>
                  </tr>
                  <tr>
                     <td>&nbsp;</td>
                     <td>
                        <label id="label-pagina" data-toggle="tooltip" data-placement="top"
                           title="Cuando el dispositivo de acceso es  una computadora y tabled indique la página que abrira este privilegio o la funcion de javascript que llamara cuando es modal, esta funcion debe de existir en la pagina desde donde se habilita esta opción">
                           <div align="left">Página o Función*</div>
                        </label>

                     </td>
                     <td><label for="txt_pagina"></label>
                        <input class="form-control" type="text" name="txt_pagina" id="txt_pagina"
                           value='<?php echo $row_rs_mantenimiento['pagina'] ?? ''; ?>'
                           maxlength="500" />
                     </td>
                  </tr>
                  <tr>
                     <td>&nbsp;</td>
                     <td>
                        <label id="label-paginamovil" data-toggle="tooltip" data-placement="top"
                           title="Cuando es movil(celular) el dispositivo de acceso indique la página que abrira este privilegio o la funcion de javascript que llamara cuando es modal, esta funcion debe de existir en la pagina desde donde se habilita esta opción">
                           <div align="left">Página o Función Movil</div>
                        </label>

                     </td>
                     <td><label for="txt_paginamovil"></label>
                        <input class="form-control" type="text" name="txt_paginamovil" id="txt_paginamovil"
                           value='<?php echo $row_rs_mantenimiento['pagina_movil'] ?? ''; ?>'
                           maxlength="500" />
                     </td>
                  </tr>
                  <tr>
                     <td>&nbsp;</td>
                     <td>

                        <label id="label-target" data-toggle="tooltip" data-placement="top"
                           title="Indique la propiedad target del link">
                           <div align="left">Target</div>
                        </label>

                     </td>
                     <td>
                        <div class="input-group mb-3">
                           <div class="input-group-prepend">
                              <label class="input-group-text" for="inputGroupSelect01">Opciones</label>
                           </div>
                           <select class="form-control" name="dwd_target" id="dwd_target">
                              <option value='' <?php if (isset($row_rs_mantenimiento) && is_array($row_rs_mantenimiento) && '' == $row_rs_mantenimiento['link_target']) {
                                                   echo "selected=\"selected\"";
                                                } ?>>Sin Target</option>
                              <option value='target="_self"' <?php if (isset($row_rs_mantenimiento) && is_array($row_rs_mantenimiento) && 'target="_self"' == $row_rs_mantenimiento['link_target']) {
                                                                  echo "selected=\"selected\"";
                                                               } ?>>Misma Pantalla</option>
                              <option value='target="_blank"' <?php if (isset($row_rs_mantenimiento) && is_array($row_rs_mantenimiento) && 'target="_blank"' == $row_rs_mantenimiento['link_target']) {
                                                                  echo "selected=\"selected\"";
                                                               } ?>>Nueva Pantalla</option>
                              <option value='target="_parent"' <?php if (isset($row_rs_mantenimiento) && is_array($row_rs_mantenimiento) && 'target="_parent"' == $row_rs_mantenimiento['link_target']) {
                                                                  echo "selected=\"selected\"";
                                                               } ?>>Padre</option>
                              <option value='target="_top"' <?php if (isset($row_rs_mantenimiento) && is_array($row_rs_mantenimiento) && 'target="_top"' == $row_rs_mantenimiento['link_target']) {
                                                               echo "selected=\"selected\"";
                                                            } ?>>Top</option>
                           </select>

                        </div>
                     </td>
                  </tr>

                  <tr>
                     <td>&nbsp;</td>
                     <td><label id="label-pagina" data-toggle="tooltip" data-placement="top"
                           title="Indique icono de la libreria de https://fontawesome.com/ que desea usar. Ejemplo: <i class='fas fa-search-location'></i>">
                           <div align="left">Icono</div>
                        </label>
                     </td>
                     <td><textarea rows="5" maxlength="800" required size="200" style="width: 100%;" type="text"
                           class="form-control" id="txt_icono" name="txt_icono"
                           placeholder=""><?php echo $row_rs_mantenimiento['icono'] ?? ''; ?></textarea>
                     </td>
                  </tr>
                  <tr>
                     <td>&nbsp;</td>
                     <td>
                        <label id="label-tabla" data-toggle="tooltip" data-placement="top"
                           title="El nommbre de la tabla para funciones de siguiente registro, anterior registro y funcion copy registro">
                           <div align="left">Tabla</div>
                        </label>
                     </td>
                     <td><input class="form-control" type="text" name="txt_tabla" id="txt_tabla"
                           value="<?php echo $row_rs_mantenimiento['tabla'] ??''; ?>"
                           maxlength="100" /></td>
                  </tr>
                  <tr>
                     <td>&nbsp;</td>
                     <td>
                        <label id="label-rotulo" data-toggle="tooltip" data-placement="top"
                           title="El rotulo que desea aparesca en la página a llamar">
                           <div align="left">Rotulo*</div>
                        </label>
                     </td>
                     <td><input class="form-control" type="text" name="txt_rotulo" id="txt_rotulo"
                           value="<?php echo $row_rs_mantenimiento['rotulo'] ??''; ?>"
                           maxlength="100" /></td>
                  </tr>
                  <tr>
                     <td>&nbsp;</td>
                     <td>

                        <label id="label-encabezado" data-toggle="tooltip" data-placement="top"
                           title="Indique si el privilegio es un encabezado en el menú">
                           <div align="left">Es encabezado</div>
                        </label>
                     </td>


                     <td>
                        <input name="chk_encabezado" type="checkbox" id="chk_encabezado" value="S"
                           <?php if (is_array($row_rs_mantenimiento) && $row_rs_mantenimiento['es_encabezado'] == "S") echo 'checked'; ?> />
                        <label for="chk_encabezado"></label>
                     </td>
                  </tr>

                  <tr>
                     <td>&nbsp;</td>
                     <td>

                        <label id="label-visible" data-toggle="tooltip" data-placement="top"
                           title="Indique si el privilegio es visible en el menú">
                           <div align="left">Visible en Menú</div>
                        </label>

                     </td>
                     <td>
                        <input name="chk_visible" type="checkbox" id="chk_visible" value="S" <?php
                           if (isset($row_rs_mantenimiento['visible']) && $row_rs_mantenimiento['visible'] == "S") {
                              echo 'checked="checked"';
                           }
                           ?> />
                     </td>

                  </tr>

                  <tr>
                     <td>&nbsp;</td>
                     <td>

                        <label id="label-modal" data-toggle="tooltip" data-placement="top"
                           title="Indique si la opción a abrir el modal">
                           <div align="left">Es modal</div>
                        </label>

                     </td>
                     <td>
                        <input 
                        name="chk_modal" 
                        type="checkbox" 
                        id="chk_modal" 
                        value="S" 
                        <?php
                           if (isset($row_rs_mantenimiento['modal']) && $row_rs_mantenimiento['modal'] == "S") {
                                 echo 'checked="checked"';
                           }
                        ?> 
                     />

                     </td>

                  </tr>

                  <tr>
                     <td>&nbsp;</td>
                     <td>
                        <label id="label-dominio" data-toggle="tooltip" data-placement="top"
                           title="Indique si se le agrega el dominio a la página a llamar">
                           <div align="left">Usar Dominio</div>
                        </label>
                     </td>
                     <td>
                     <input 
                        name="chk_dominio"
                        type="checkbox" 
                        id="chk_dominio" 
                        value="S" 
                        <?php
                           if (isset($row_rs_mantenimiento['usar_dominio']) && $row_rs_mantenimiento['usar_dominio'] == "S") {
                                 echo 'checked="checked"';
                           }
                        ?> 
                     />
                  </tr>

                  <tr>
                     <td>&nbsp;</td>
                     <td><label class="tooltip-test" data-placement="top" data-original-title="Seleccione un estado">
                           <div align="left">Estado*</div>
                        </label></td>

                     <td class="field">
                        <select class="form-control" name="dwd_estado" id="dwd_estado">
                           <option value="A"
                              <?php echo (isset($row_rs_mantenimiento) && is_array($row_rs_mantenimiento) && "A" == $row_rs_mantenimiento['estado']) ? 'selected="selected"' : ''; ?>>
                              Activo</option>
                           <option value="I"
                              <?php echo (isset($row_rs_mantenimiento) && is_array($row_rs_mantenimiento) && "I" == $row_rs_mantenimiento['estado']) ? 'selected="selected"' : ''; ?>>
                              Inactivo</option>
                        </select>

                     </td>
                  </tr>
                  <tr>
                     <td>&nbsp;</td>
                     <td><label class="tooltip-test" data-placement="top"
                           data-original-title="Código de usuario que creo este registro">Usuario
                           Creaci&oacute;n</label></td>
                     <td class="field"><input name="txt_usucre" type="text" class="form-control" id="txt_usucre"
                           value="<?php echo isset($row_rs_mantenimiento['usuario_creacion']) ? $row_rs_mantenimiento['usuario_creacion'] : ''; ?>"
                           readonly /></td>
                  </tr>
                  <tr>
                     <td>&nbsp;</td>
                     <td><label class="tooltip-test" data-placement="top"
                           data-original-title="Fecha en que fue creado el registro">Fecha de Creaci&oacute;n</label>
                     </td>
                     <td class="field"><input name="txt_feccre" type="text" class="form-control" id="txt_feccre"
                           value="<?php echo isset($row_rs_mantenimiento['fecha_creacion']) ?  $row_rs_mantenimiento['fecha_creacion'] : ''; ?>"
                           readonly /></td>
                  </tr>
               </table>
            </div>
         </div>

         <?php if (isset($rs_id_rs_mantenimiento) && $rs_id_rs_mantenimiento <> -1) { ?>
         <input type="hidden" name="MM_update" id="MM_update" value="frm_mantenimiento" />
         <input type="hidden" name="id" id="id" value="<?php echo $rs_id_rs_mantenimiento; ?>" />
         <?php } else { ?>
         <input type="hidden" name="MM_insert" id="MM_insert" value="frm_mantenimiento" />
         <?php }  ?>
      </form>

   </div>

   <div class="bottom mt-5">
      <footer class="d-flex flex-wrap justify-content-between align-items-center py-1 my-1">
         <?php include_once('../../../footer.php') ?>
      </footer>
   </div>

   <?php
   include_once('../../../pie.php');
  
   // include_once('../../../../sattb/modal_pie.php');
   ?>


   <!-- <script src="< ? php// echo $appcfg_Dominio; ?>js/administracion_usuario_lista.js"></script> -->
   <!-- <script src="< ?PHP //echo $appcfg_Dominio; ?>assets/js/expresiones_regulares.js"></script> -->
   <?php
   // include_once('../../../../sattb/assets/js/openModalLogin.js');
   // include_once('../../../../sattb/assets/js/login.js');
   ?>



   <?php
   $mantenimiento->closeCursor();
   ?>
   <?php require_once('modal_pie.php'); ?>
   <?php require_once('modal_encabezado.php'); ?>

</body>

</html>