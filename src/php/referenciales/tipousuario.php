<?php
/* validando que paso por la pantalla de login */
session_start();
if (!isset($_SESSION['user_name'])) {
	header("location: ../index.php?msg=Favor inicie sesión para poder ingresar al sistema"); 
	exit();
}

?>

<?php  require_once('../../utils/anterior_siguiente.php'); ?>
<?php  require_once('../../utils/tipo_dispositivo.php'); ?>
<?php  require_once('../../../configuracion/configuracion.php'); ?>
<?php  require_once('../../../../config/conexion.php'); ?>
<?php  require_once('../../utils/funciones_db.php'); ?>
<?php  require_once('../../utils/create_copy_row.php'); ?>
<?php  require_once('../../utils/total_pages.php'); ?>
<?php

$codigo = 'TIPODEUSUARIOS';

require_once('../../utils/validar_privilegio.php');

$pantallax = explode('/',$currentPage);
$pantallax = $pantallax[(count($pantallax)-1)];
$pantallax = explode('.',$pantallax);
$pantalla[0] = $pantallax[0];

// Obteniendo el id
$rs_id_rs_mantenimiento = -1;
if (isset($_GET['id'])) {
	$rs_id_rs_mantenimiento =  $_GET['id'];
} else {
	if (isset($_POST['id'])) {
		$rs_id_rs_mantenimiento =  $_POST['id'];
	}
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "frm_mantenimiento")) {
		//Armando query para actaulizar usuario
		$updateSQL = "UPDATE  [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_tipo_usuario] SET descripcion=:descripcion, estado=:estado, usuario_modificacion=:usuario_modificacion, fecha_modificacion= GETDATE(), ip_modificacion=:ip_modificacion,host_modificacion=:host_modificacion  WHERE id=:id";
		//Comensando la transacción
		$db->beginTransaction();
		//Preparando ejecución de sentencia SQL
		$stmt = $db->prepare($updateSQL);
		//Ejecutanto sentencia SQL
		$res = $stmt->execute(Array(':descripcion' => strtoupper($_POST['txt_nombre']), ':estado' => $_POST['dwd_estado'],
		':id' => $rs_id_rs_mantenimiento, ':usuario_modificacion'=> $_SESSION['user_name'], ':ip_modificacion'=> $ip, ':host_modificacion'=> $host));

	// Validando que el error de la ejecución
	if (isset($res) and $res == '') {
		$msg = "Error intentando actualizar registro, error -> " . geterror($stmt->errorInfo());
		$stmt->closeCursor();
		$db->rollBack();
	} else {
		$stmt->closeCursor();
		$db->commit();
		// Recarga de pantalla con mensaje de procesamiento satisfactorio
		if ((isset($_POST["cmd_salvar_close"]))) {
			$pagina_html = $pantalla[0]. '_lista.php?msg=<strong>Bien Hecho!</strong><br \>El registro ha sido actualizado satisfactoriamente';  
			header(sprintf("Location: %s", $pagina_html));
		} else {
			if ((isset($_POST["cmd_salvar_nuevo"]))) {
				$msg="<strong>Bien Hecho!</strong><br \>El registro ha sido actualizado satisfactoriamente"; 
				$rs_id_rs_mantenimiento = -1;
		} else {
				if ((isset($_POST["cmd_salvar"]))) {
					$msg="<strong>Bien Hecho!</strong><br \>El registro ha sido actualizado satisfactoriamente"; 
				} else {
					$msg="<strong>Bien Hecho!</strong><br \>El registro ha sido actualizado satisfactoriamente"; 
					$copy = f_create_copy_row($tablanext,$rs_id_rs_mantenimiento,$db);
				}
			}
		}
	}
  
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frm_mantenimiento")) {
// (Final) Determinando el siguiente numero de tarea
	$insertSQL ="INSERT INTO [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_tipo_usuario] (
	descripcion,
	estado,
	usuario_creacion,
	fecha_creacion,
	ip_creacion,
	host_creacion)
	VALUES (:descripcion, :estado, :usuario_creacion , GETDATE(),:ip_creacion,:host_creacion)";
	// Preparando la sentencia
	//Comensando la transacción
	$db->beginTransaction();
	$stmt = $db->prepare($insertSQL);
	$res = $stmt->execute(Array(':descripcion' => strtoupper($_POST['txt_nombre']), ':estado' => $_POST['dwd_estado'],':usuario_creacion' => $_SESSION["user_name"],':ip_creacion' => $ip,':host_creacion' => $host));
	// Validando que el error de la ejecución
	if (isset($res) and $res == '') {
		$msg = "Error intentando insertar registro, error -> " . geterror($stmt->errorInfo());
		$stmt->closeCursor();
		$db->rollBack();
	} else {
		$stmt->closeCursor();
		$rs_id_rs_mantenimiento = $db->lastInsertId();
		$db->commit();
		// Recarga de pantalla con mensaje de procesamiento satisfactorio
		if ((isset($_POST["cmd_salvar_close"]))) {
			$pagina_html = $pantalla[0]. '_lista.php?msg=<strong>Bien Hecho!</strong><br \>El registro ha sido actualizado satisfactoriamente';  
			header(sprintf("Location: %s", $pagina_html));
		} else {
			if ((isset($_POST["cmd_salvar_nuevo"]))) {
				$msg="<strong>Bien Hecho!</strong><br \>El registro ha sido actualizado satisfactoriamente"; 
				$rs_id_rs_mantenimiento = -1;
		} else {
				if ((isset($_POST["cmd_salvar"]))) {
					$msg="<strong>Bien Hecho!</strong><br \>El registro ha sido actualizado satisfactoriamente"; 
				} else {
					$msg="<strong>Bien Hecho!</strong><br \>El registro ha sido actualizado satisfactoriamente"; 
					$copy = f_create_copy_row($tablanext,$rs_id_rs_mantenimiento,$db);
				}
			}
		}
	}
}

$rotulo = "MANTENIMIENTO:  EDITANDO " . $tablanext;
// Armando el query
$query_rs_mantenimiento = "SELECT a.*
FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_tipo_usuario] a WHERE id = :id";
// Preparando la sentencia
$mantenimiento = $db->prepare($query_rs_mantenimiento);
// Ejecutanto el query
$mantenimiento->execute(Array(':id' => $rs_id_rs_mantenimiento));
// Si hay algun error
$res = $mantenimiento->errorInfo();
if (isset($res) and $res[2] <> '') {
	$msg = "Error intentando leer tabla de bancos, error -> " . $res[2] . $res[1] . $res[0];
	$mantenimiento->closeCursor();
	$totalRows_rs_mantenimiento = 0;
	$clase = "X";
} else {
	// Sino hay error carga el 1er registro	
	$row_rs_mantenimiento = $mantenimiento->fetch();
	// Contador de registros de estados
	$totalRows_rs_mantenimiento = $mantenimiento->rowcount();
	if ($totalRows_rs_mantenimiento > 0) {
		$rotulo = "MANTENIMIENTO:  Editando " . $tablanext;
	}
}
if ((isset($_POST["cmd_salvar_copy"]))) {
	$rs_id_rs_mantenimiento = -1;
	$row_rs_mantenimiento['descripcion'] = $row_rs_mantenimiento['descripcion'] . ' Copia' . $copy;
}
?>

<!DOCTYPE html>
<head>
<title>MANTENIMIENTO <?php echo strtoupper($tablanext) ; ?></title>
<?php require_once('../../../encabezado.php'); ?>
</head>
<body>
<div class="main">
<?php require_once('../../../encabezado_body.php'); ?>
<?php require_once('../../../menu_segun_privilegios.php'); ?>
  <form id="form1" name="form1" method="post" action="<?php echo $currentPage; ?>">
<?php require_once('../../../rotulo_pantalla.php'); ?>
<?php 
	  $botones = 'M';
	  require_once('../../../botones.php'); 
?>
<?php 
$detalle = 'INFORMACIÓN DE '. $tablanext;
require_once('../../../detalle_pantalla.php'); ?>   
<div class="table-responsive">
    <table width="100%" align="center" cellpadding="5" cellspacing="2">
    <td width="1%"></td>
    <td width="98%">
    <br />
<div id="msg-completo">
<?PHP 
if (isset($msg) and $msg <> "")  {
?>    
<div id="msg-global" class="alert alert-success alert-dismissible" role="alert">
  	<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
	 <div id="msg-error">  
		<?PHP echo $msg;?>
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
	<div id="tabla_mantenimiento" class="col-md-5">	
	  <table class="table table-borderless">
        <tr>
          <td width="8%">&nbsp;</td>
          <td width="17%"><label class="tooltip-test" title="" data-placement="top" data-original-title="Aqui se presenta el ID del registro, solo aplica a la opción de editar">Código</label>
          </td>
          <td width="85%">
		  	<?php  echo $row_rs_mantenimiento['id'] ?? '#'; ?>
          </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><label class="tooltip-test" data-placement="top" data-original-title="Introduzca la descripción">
          <div id="label-nombre">Nombre Rol*</div></label></td>
          <td>
            <input type="text" class="form-control"  id="text_nombre" name="txt_nombre" 
				value="<?php echo $row_rs_mantenimiento['descripcion'] ?? ''; ?>" maxlength="200">
		</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><label class="tooltip-test" data-placement="top" data-original-title="Seleccione un estado"><div align="left">Estado*</div></label></td>
          <td class="field">
				<select class="form-control" name="dwd_estado" id="dwd_estado">
					<option value="A"
							<?php if ($row_rs_mantenimiento && !(strcmp("A", $row_rs_mantenimiento['estado']))) {
								echo "selected=\"selected\"";
							} ?>>Activo
					</option>
					<option value="I" 
							<?php if ($row_rs_mantenimiento && !(strcmp("I", $row_rs_mantenimiento['estado']))) {
								echo "selected=\"selected\"";
							} ?>>Inactivo
					</option>
				</select>
			</td>

        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><label class="tooltip-test" data-placement="top" data-original-title="Código de usuario que creo este registro">Usuario Creaci&oacute;n</label></td>
          <td class="field"><input name="txt_usucre" type="text"  class="form-control" id="txt_usucre"
			  value="<?php echo $row_rs_mantenimiento['usuario_creacion'] ?? ''; ?>" readonly /></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><label class="tooltip-test" data-placement="top" data-original-title="Fecha en que fue creado el registro">Fecha de Creaci&oacute;n</label></td>
          <td class="field"><input name="txt_feccre" type="text"  class="form-control" id="txt_feccre" 
			 value="<?php echo $row_rs_mantenimiento['fecha_creacion'] ?? ''; ?>" readonly /></td>
        </tr>
      </table>
 </div>		
</div>
    
   <?php if (isset($rs_id_rs_mantenimiento) && $rs_id_rs_mantenimiento <> -1){ ?>
    <input type="hidden" name="MM_update" id="MM_update" value="frm_mantenimiento" />
    <input type="hidden" name="id"  id="id"  value="<?php echo $rs_id_rs_mantenimiento ?? '' ;?>" />
   <?php } else { ?>
   	   <input type="hidden" name="MM_insert" id="MM_insert" value="frm_mantenimiento" />
	<?php }  ?>
  </form>
  
  </div>
<?php
require_once('../../../pie.php'); 
$mantenimiento->closeCursor();
?>
<script>
// Ubicar el cursos en el campo nombre
document.getElementById("text_nombre").focus();
function f_validar(){
	$error = false;
	$error_text = "";
	$messaje = '';
	if (document.getElementById('text_nombre').value ==''){
		$messaje = 'Campo Invalido:  <strong>Nombre</strong>' + "<br/>"; 
		$("#label-nombre").addClass("label-error").removeClass("normal");
		$("#text_nombre").addClass("text-error").removeClass("normal");
		$error = true;
	}	else {
		$("#label-nombre").removeClass("label-error");
		$("#text_nombre").removeClass("text-error");
	}
	
	if ($error == true) {
		divContenido = document.getElementById('msg-completo');
		divContenido.innerHTML = "";
		divContenido.innerHTML = '<div id="msg-completo"><div id="msg-global" class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button><div id="msg-error">' + '<strong>Errores! </strong>' + '<br\>' + $messaje + '</div></div></div>'
		return false;
	} else {
		return true;
	}
}
</script>
<?PHP //require_once('../pie_js.php'); ?>
<?PHP //require_once('../modal_pie.php'); ?>
<?PHP require_once('../../../timer_logout.php'); ?>
<?PHP //require_once('../modal_encabezado.php'); ?>	
</body>
</html>
