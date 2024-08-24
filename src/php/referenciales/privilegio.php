<?php
/* validando que paso por la pantalla de login */
session_start();
if (!isset($_SESSION['tipo'])) {
	header("location: ../inicio.php?msg=Favor inicie sesión para poder ingresar al sistema"); exit();
}
?>
<?php require_once('../utils/tipo_dispositivo.php'); ?>
<?php require_once('../conexion/configuracion.php'); ?>
<?php require_once('../funciones/anterior_siguiente.php'); ?>
<?php require_once('../funciones/funciones_db.php'); ?>
<?php require_once('../conexion/conexion.php'); ?>
<?php require_once('../utils/create_copy_row.php'); ?>
<?php
// Validando privilegios
$id_privilegio = 'PRIVILEGIOS';
require_once('../utils/validar_privilegio.php');
$pantallax = explode('/',$currentPage);
$pantallax = $pantallax[(count($pantallax)-1)];
$pantallax = explode('.',$pantallax);
$pantalla[0] = $pantallax[0];
$clase = 'alert alert-success alert-dismissible';
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
		// Si se marco como encabezado el privilegio
		$encabezado = 'N';
		if (isset($_POST['chk_encabezado'])) {
			$encabezado = 'S';
		}
		// Si se marco como visible en menú
		$visible = 'N';
		if (isset($_POST['chk_visible'])) {
			$visible = 'S';
		}		
		// Si se marco que la pagina a abrir es modal
		$modal = 'N';
		if (isset($_POST['chk_modal'])) {
			$modal = 'S';
		}
		// Si se marco si se debe agrergar el domino al link
		$usar_dominio = 'N';
		if (isset($_POST['chk_dominio'])) {
			$usar_dominio = 'S';
		}		
		//Armando query para actaulizar usuario
		$updateSQL = "UPDATE privilegio SET
		modal=:modal,usar_dominio=:usar_dominio,link_target=:link_target,rotulo=:rotulo,pagina_movil=:pagina_movil,
		icono=:icono,tabla=:tabla,visible=:visible,es_encabezado=:es_encabezado,
		menu_padre=:menu_padre,nivel_menu=:nivel_menu,pagina=:pagina,descripcion=:descripcion, estado=:estado, usuario_modificacion=:usuario_modificacion, fecha_modificacion=now(), ip_modificacion=:ip_modificacion,host_modificacion=:host_modificacion  WHERE id=:id";
		//Comensando la transacción
		$conn->beginTransaction();
		//Preparando ejecución de sentencia SQL
		$stmt = $conn->prepare($updateSQL);
		//Ejecutanto sentencia SQL
		$res = $stmt->execute(Array(
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
			':usuario_modificacion'=> $_SESSION['usuario'],
			':ip_modificacion'=> $ip,
			':host_modificacion'=> $host));
	// Validando que el error de la ejecución
	if (isset($res) and $res == '') {
		$msg = "Error intentando actualizar registro, error -> " . geterror($stmt->errorInfo());
		$clase = 'alert alert-danger alert-dismissible';
		$stmt->closeCursor();
		$conn->rollBack();
	} else {
		$stmt->closeCursor();
		$conn->commit();
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
					$copy = f_create_copy_row($tablanext,$rs_id_rs_mantenimiento,$conn);
				}
			}
		}
	}
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frm_mantenimiento")) {
	// Si se marco como encabezado el privilegio
	$encabezado = 'N';
	if (isset($_POST['chk_encabezado'])) {
		$encabezado = 'S';
	}
	// Si se marco como visible en menú
	$visible = 'N';
	if (isset($_POST['chk_visible'])) {
		$visible = 'S';
	}		
	// Si se marco que la pagina a abrir es modal
	$modal = 'N';
	if (isset($_POST['chk_modal'])) {
		$modal = 'S';
	}
	// Si se marco si se debe agrergar el domino al link
	$usar_dominio = 'N';
	if (isset($_POST['chk_dominio'])) {
		$usar_dominio = 'S';
	}		
	// (Inicio) Armando query para insertar registro
	$insertSQL ="INSERT INTO privilegio (
	modal,
	usar_dominio,
	link_target,
	rotulo,
	pagina_movil,
	icono,
	tabla,
	es_encabezado,	
	visible,
	id,
	descripcion,
	estado,
	nivel_menu,
	pagina,
	menu_padre,
	usuario_creacion,
	fecha_creacion,
	ip_creacion,
	host_creacion)
	VALUES (:modal,:usar_dominio,:link_target,:rotulo,:pagina_movil,:icono,:tabla,:es_encabezado, :visible,  :id, :descripcion, :estado, :nivel_menu, :pagina, :menu_padre, :usuario_creacion ,now(), :ip_creacion, :host_creacion)";
	// Preparando la sentencia
	//Comensando la transacción
	$conn->beginTransaction();
	$stmt = $conn->prepare($insertSQL);
	$res = $stmt->execute(Array(':modal' => $modal,
								':usar_dominio' => $usar_dominio,
								':link_target'  => $_POST['dwd_target'],
								':rotulo'  => $_POST['txt_rotulo'],
								':pagina_movil'  => $_POST['txt_paginamovil'],
								':icono' => $_POST['txt_icono'],
								':tabla' => $_POST['txt_tabla'],
								':es_encabezado' => $encabezado,
								':visible' => $visible,
								':id' => strtoupper($_POST['txt_id']),
								':descripcion' => $_POST['txt_descripcion'],
								':estado' => $_POST['dwd_estado'],
								':nivel_menu' => strtoupper($_POST['txt_menu']),
								':pagina' => $_POST['txt_pagina'],
								':menu_padre' => strtoupper($_POST['dwd_encabezado']),
								':usuario_creacion' => $_SESSION["usuario"],
								':ip_creacion' => $ip,
								':host_creacion' => $host));
	// Validando que el error de la ejecución
	$res = $stmt->errorInfo();
	if (isset($res) and $res == '') {
		$msg = "Error intentando insertar registro, error -> " . geterror($stmt->errorInfo());
		$clase = 'alert alert-danger alert-dismissible';
		$stmt->closeCursor();
		$conn->rollBack();
	} else {
		$stmt->closeCursor();
		$rs_id_rs_mantenimiento = $conn->lastInsertId();
		$conn->commit();
		// Recarga de pantalla con mensaje de procesamiento satisfactorio
		if ((isset($_POST["cmd_salvar_close"]))) {
			$pagina_html = $pantalla[0]. '_lista.php?msg=<strong>Bien Hecho!</strong><br \>El registro ha sido insertado satisfactoriamente';  
			header(sprintf("Location: %s", $pagina_html));
		} else {
			if ((isset($_POST["cmd_salvar_nuevo"]))) {
				$msg="<strong>Bien Hecho!</strong><br \>El registro ha sido insertado satisfactoriamente"; 
				$rs_id_rs_mantenimiento = -1;
		} else {
				if ((isset($_POST["cmd_salvar"]))) {
					$msg="<strong>Bien Hecho!</strong><br \>El registro ha sido insertado satisfactoriamente";  
				} else {
					$msg="<strong>Bien Hecho!</strong><br \>El registro ha sido insertado satisfactoriamente"; 
					$copy = f_create_copy_row($tablanext,$rs_id_rs_mantenimiento,$conn);
				}
			}
		}		
	}
}


// menús encabezados
$query_rs_encabezado = "SELECT a.id, nivel_menu,descripcion FROM privilegio a where a.es_encabezado = 'S' and a.estado = 'A' and a.descripcion != '' order by descripcion";
$encabezado = $conn->prepare($query_rs_encabezado);
$res = $encabezado->execute();
$row_rs_encabezado = $encabezado->fetch();
$totalRows_rs_encabezado = $encabezado->rowcount();

$rotulo = "MANTENIMIENTO:  Agregando ". $tablanext;
// Armando el query
$query_rs_mantenimiento = "SELECT a.*
FROM privilegio a WHERE id = :id";
// Preparando la sentencia
$mantenimiento = $conn->prepare($query_rs_mantenimiento);
// Ejecutanto el query
$mantenimiento->execute(Array(':id' => $rs_id_rs_mantenimiento));
// Si hay algun error
$res = $mantenimiento->errorInfo();
if (isset($res) and $res[2] <> '') {
	$msg = "Error intentando leer tabla " . $tablanext   .", error -> " . $res[2] . $res[1] . $res[0];
	$mantenimiento->closeCursor();
	$totalRows_rs_mantenimiento = 0;
	$clase = 'alert alert-danger alert-dismissible';
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
<?php require_once('../encabezado.php'); ?>
</head>
<body>
<div class="main">
<?php require_once('../encabezado_body.php'); ?>
  <form id="form1" name="form1" method="post" action="<?php echo $currentPage; ?>">
<?php require_once('../rotulo_pantalla.php'); ?>
<?php 
	  $botones = 'M';
	  require_once('../botones.php'); 
?>
<?php 
$detalle = 'INFORMACIÓN DE '. $tablanext;
require_once('../detalle_pantalla.php'); ?>   
<div class="table-responsive">
    <table width="100%" align="center" cellpadding="5" cellspacing="2">
    <td width="1%"></td>
    <td width="98%">
    <br />
<div id="msg-completo">
<?PHP 
if (isset($msg) and $msg <> "")  {
?>    
<div id="msg-global" class="<?PHP echo $clase;?> " role="alert">
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
      <table class="table table-unbordered">
        <tr>
          <td width="8%">&nbsp;</td>
          <td width="20%">
			  <label id="label-id" data-toggle="tooltip" data-placement="top" title="Ingrese aqui el ID del registro, solo caracteres(a-z,A-Z), solo disponible en opción de agregar"><div align="left">ID*</div></label>
</td>
          <td width="72%"><label for="txt_id"></label>
			<?PHP if (isset($copy) == true or !isset($row_rs_mantenimiento['estado']) == true) { ?>
                <input type="text" class="form-control"  id="txt_id" name="txt_id" value="<?php echo $row_rs_mantenimiento['id']; ?>" maxlength="30" onchange="f_validar_privilegio(<?PHP echo $rs_id_rs_mantenimiento;?>)">
                <label for="txt_demo"></label>
            <?php } else { ?>
				<input readonly type="text" class="form-control"  id="txt_id" name="txt_id" value="<?php echo $row_rs_mantenimiento['id']; ?>" maxlength="30" onchange="f_validar_usuario()">
                <label for="txt_demo"></label>            
            <?php }  ?>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>
			  <label id="label-descripcion" data-toggle="tooltip" data-placement="top" title="Introduzca la descripción del privilegio, es el texto que aparecera en el menú"><div align="left">Descripción*</div></label>
			  
			  </td>
          <td>
			  <textarea rows="5" maxlength="300" required size="100" class="form-control" id="txt_descripcion" name="txt_descripcion" placeholder=""><?php echo $row_rs_mantenimiento['descripcion'];?></textarea>
		</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>
			  <label maxlength="15" id="label-menu" data-toggle="tooltip" data-placement="top" title="Indique el orden en que desea aparezca el menu en la pantalla"><div align="left">Orden Menú *</div></label>
			</td>
          <td><label for="txt_menu"></label>
          <input  class="form-control" type="text" name="txt_menu" id="txt_menu" value="<?php echo $row_rs_mantenimiento['nivel_menu']; ?>" maxlength="20"/></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>
			  
			  <label id="label-menuencabezado" data-toggle="tooltip" data-placement="top" title="Indique el menú padre en e menú para este privilegio"><div align="left">Menú Padre</div></label>
			  
			 </td>
			
          <td><div class="input-group mb-3">
  <div class="input-group-prepend">
    <label class="input-group-text" for="inputGroupSelect01">Opciones</label>
  </div>			  

			  <select name="dwd_encabezado" id="dwd_encabezado" class="form-control">
			   
              <option value="0" <?php if (0 == $row_rs_mantenimiento['menu_padre']) {echo "selected=\"selected\"";} ?>>Seleccione el menu encabezado de esta opción</option>
              <?PHP 
			for ($i=0;$totalRows_rs_encabezado>$i;$i++) {?>
            <option value="<?php echo $row_rs_encabezado['id'];?>" <?php if ($row_rs_encabezado['id'] == $row_rs_mantenimiento['menu_padre']) {echo "selected=\"selected\"";} ?>><?php echo $row_rs_encabezado['descripcion'];?></option>
            <?PHP 
				$row_rs_encabezado = $encabezado->fetch();          
			} ?>
          </select>  </div>  </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>
			   <label id="label-pagina" data-toggle="tooltip" data-placement="top" title="Cuando el dispositivo de acceso es  una computadora y tabled indique la página que abrira este privilegio o la funcion de javascript que llamara cuando es modal, esta funcion debe de existir en la pagina desde donde se habilita esta opción"><div align="left">Página o Función*</div></label>
			  
			  </td>
          <td><label for="txt_pagina"></label>
          <input class="form-control" type="text" name="txt_pagina" id="txt_pagina" value='<?php echo $row_rs_mantenimiento['pagina']; ?>' maxlength="500" /></td>
        </tr>
<tr>
          <td>&nbsp;</td>
          <td>
			   <label id="label-paginamovil" data-toggle="tooltip" data-placement="top" title="Cuando es movil(celular) el dispositivo de acceso indique la página que abrira este privilegio o la funcion de javascript que llamara cuando es modal, esta funcion debe de existir en la pagina desde donde se habilita esta opción"><div align="left">Página o Función Movil</div></label>
			  
			  </td>
          <td><label for="txt_paginamovil"></label>
          <input class="form-control" type="text" name="txt_paginamovil" id="txt_paginamovil" value='<?php echo $row_rs_mantenimiento['pagina_movil']; ?>' maxlength="500" /></td>
        </tr>		  

		    <tr>
          <td>&nbsp;</td>
          <td>
			  
			    <label id="label-target" data-toggle="tooltip" data-placement="top" title="Indique la propiedad target del link"><div align="left">Target</div></label>
			
			</td>
          <td>
			  <div class="input-group mb-3">
  <div class="input-group-prepend">
    <label class="input-group-text" for="inputGroupSelect01">Opciones</label>
  </div>	
			  <select class="form-control" name="dwd_target" id="dwd_target">
            <option value='' <?php if ('' == $row_rs_mantenimiento['link_target']) {echo "selected=\"selected\"";} ?>>Sin Target</option>
			<option value='target="_self"' <?php if ('target="_self"' == $row_rs_mantenimiento['link_target']) {echo "selected=\"selected\"";} ?>>Misma Pantalla</option>
            <option value='target="_blank"' <?php if ('target="_blank"' == $row_rs_mantenimiento['link_target']) {echo "selected=\"selected\"";} ?>>Nueva Pantalla</option>
            <option value='target="_parent"' <?php if ('target="_parent"' == $row_rs_mantenimiento['link_target']) {echo "selected=\"selected\"";} ?>>Padre</option>
            <option value='target="_top"' <?php if ('target="_top"' == $row_rs_mantenimiento['link_target']) {echo "selected=\"selected\"";} ?>>Top</option>

		  </select></div>
				</td>
        </tr>		  

		  
		  <tr>
          <td>&nbsp;</td>
          <td><label id="label-pagina" data-toggle="tooltip" data-placement="top" title="Indique icono de la libreria de https://fontawesome.com/ que desea usar. Ejemplo: <i class='fas fa-search-location'></i>"><div align="left">Icono</div></label>			  
			 </td>
          <td><textarea rows="5" maxlength="800" required size="200" style="width: 100%;" type="text" class="form-control" id="txt_icono" name="txt_icono" placeholder=""><?php echo $row_rs_mantenimiento['icono'];?></textarea></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>
			  <label id="label-tabla" data-toggle="tooltip" data-placement="top" title="El nommbre de la tabla para funciones de siguiente registro, anterior registro y funcion copy registro"><div align="left">Tabla</div></label>	
			</td>
          <td><input class="form-control" type="text" name="txt_tabla" id="txt_tabla" value="<?php echo $row_rs_mantenimiento['tabla']; ?>" maxlength="100" /></td>
        </tr>
		  <tr>
          <td>&nbsp;</td>
          <td>
			  <label id="label-rotulo" data-toggle="tooltip" data-placement="top" title="El rotulo que desea aparesca en la página a llamar"><div align="left">Rotulo*</div></label>	
			</td>
          <td><input class="form-control" type="text" name="txt_rotulo" id="txt_rotulo" value="<?php echo $row_rs_mantenimiento['rotulo']; ?>" maxlength="100" /></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>
			  
			  <label id="label-encabezado" data-toggle="tooltip" data-placement="top" title="Indique si el privilegio es un encabezado en el menú"><div align="left">Es encabezado</div></label>
			  </td>
			
          <td><input <?php if ($row_rs_mantenimiento['es_encabezado'] == "S") {echo "checked=\"checked\"";} ?> name="chk_encabezado" type="checkbox" id="chk_encabezado" value="S" />
          <label for="chk_encabezado"></label></td>
        </tr>

        <tr>
          <td>&nbsp;</td>
          <td>
			  
			    <label id="label-visible" data-toggle="tooltip" data-placement="top" title="Indique si el privilegio es visible en el menú"><div align="left">Visible en Menú</div></label>
			
			</td>
          <td><input <?php if ($row_rs_mantenimiento['visible'] == "S") {echo "checked=\"checked\"";} ?> name="chk_visible" type="checkbox" id="chk_visible" value="S" /></td>
        </tr>

   <tr>
          <td>&nbsp;</td>
          <td>
			  
			    <label id="label-modal" data-toggle="tooltip" data-placement="top" title="Indique si la opción a abrir el modal"><div align="left">Es modal</div></label>
			
			</td>
          <td><input <?php if ($row_rs_mantenimiento['modal'] == "S") {echo "checked=\"checked\"";} ?> name="chk_modal" type="checkbox" id="chk_modal" value="S" /></td>
        </tr>

  <tr>
          <td>&nbsp;</td>
          <td>
			  
			    <label id="label-dominio" data-toggle="tooltip" data-placement="top" title="Indique si se le agrega el dominio a la página a llamar"><div align="left">Usar Dominio</div></label>
			
			</td>
          <td><input <?php if ($row_rs_mantenimiento['usar_dominio'] == "S") {echo "checked=\"checked\"";} ?> name="chk_dominio" type="checkbox" id="chk_dominio" value="S" /></td>
        </tr>		  
		  
        <tr>
          <td>&nbsp;</td>
          <td><label class="tooltip-test" data-placement="top" data-original-title="Seleccione un estado"><div align="left">Estado*</div></label></td>
          <td class="field"><select class="form-control" name="dwd_estado" id="dwd_estado">
            <option value="A" <?php if ("A" == $row_rs_mantenimiento['estado']) {echo "selected=\"selected\"";} ?>>Activo</option>
            <option value="I" <?php if ("I" == $row_rs_mantenimiento['estado']) {echo "selected=\"selected\"";} ?>>Inactivo</option>
          </select></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><label class="tooltip-test" data-placement="top" data-original-title="Código de usuario que creo este registro">Usuario Creaci&oacute;n</label></td>
          <td class="field"><input name="txt_usucre" type="text"  class="form-control" id="txt_usucre" value="<?php echo $row_rs_mantenimiento['usuario_creacion']; ?>" readonly /></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><label class="tooltip-test" data-placement="top" data-original-title="Fecha en que fue creado el registro">Fecha de Creaci&oacute;n</label></td>
          <td class="field"><input name="txt_feccre" type="text"  class="form-control" id="txt_feccre" value="<?php echo $row_rs_mantenimiento['fecha_creacion']; ?>" readonly /></td>
        </tr>
      </table>
</div>
</div>
	
<?php if (isset($rs_id_rs_mantenimiento) && $rs_id_rs_mantenimiento <> -1){ ?>
    <input type="hidden" name="MM_update" id="MM_update" value="frm_mantenimiento" />
    <input type="hidden" name="id"  id="id"  value="<?php echo $rs_id_rs_mantenimiento ;?>" />
   <?php } else { ?>
   	   <input type="hidden" name="MM_insert" id="MM_insert" value="frm_mantenimiento" />
	<?php }  ?>
  </form>
  
  </div>
<?php
require_once('../pie.php'); 
$mantenimiento->closeCursor();
?>
<script>
// Ubicar el cursos en el campo nombre
document.getElementById("txt_descripcion").focus();
function f_validar(){
	$error = false;
	$error_text = "";
	$messaje = '';
	divContenido = document.getElementById('msg-completo');
	divContenido.innerHTML = "";
	
if (typeof(document.getElementById('txt_id').value) != "undefined" & document.getElementById('txt_id').value.trim() == ''){
		$messaje = 'Campo Invalido:  <strong>ID</strong>' + "<br/>"; 
		$("#label-id").addClass("label-error").removeClass("normal");
		$("#txt_id").addClass("text-error").removeClass("normal");
		$error = true;
	}	else {
		$("#label-id").removeClass("label-error");
		$("#txt_id").removeClass("text-error");
	}
	
	if (document.getElementById('txt_descripcion').value.trim() == ''){
		$messaje = 'Campo Invalido:  <strong>Descripción</strong>' + "<br/>"; 
		$("#label-descripcion").addClass("label-error").removeClass("normal");
		$("#txt_descripcion").addClass("text-error").removeClass("normal");
		$error = true;
	}	else {
		$("#label-descripcion").removeClass("label-error");
		$("#txt_descripcion").removeClass("text-error");
	}
	

	if (document.getElementById("txt_menu").value.trim() == ''){
		$messaje = $messaje + 'Campo Invalido:  <strong>Orden Menú</strong>' + "<br/>"; 
		$("#label-menu").addClass("label-error").removeClass("normal");
		$("#txt_menu").addClass("text-error").removeClass("normal");
		$error = true;
	}	else {
		$("#label-menu").removeClass("label-error");
		$("#txt_menu").removeClass("text-error");
	}
	
	if (document.getElementById("txt_pagina").value.trim() == ''){
		$messaje = $messaje + 'Campo Invalido:  <strong>Debe indicar una página</strong>' + "<br/>"; 
		$("#label-pagina").addClass("label-error").removeClass("normal");
		$("#txt_pagina").addClass("text-error").removeClass("normal");
		$error = true;
	}	else {
		$("#label-pagina").removeClass("label-error");
		$("#txt_pagina").removeClass("text-error");
	}

	if (document.getElementById("txt_rotulo").value.trim() == ''){
		$messaje = $messaje + 'Campo Invalido:  <strong>Rotulo Invalido</strong>' + "<br/>"; 
		$("#label-rotulo").addClass("label-error").removeClass("normal");
		$("#txt_rotulo").addClass("text-error").removeClass("normal");
		$error = true;
	}	else {
		$("#label-rotulo").removeClass("label-error");
		$("#txt_rotulo").removeClass("text-error");
	}

	
	if ($error == true) {
		divContenido.innerHTML = '<div id="msg-completo"><div id="msg-global" class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button><div id="msg-error">' + '<strong>Errores! </strong>' + '<br\>' + $messaje + '</div></div></div>'
		return false;
	} else {
		return true;
	}
}

//Validar que el correo electronico no este ya registrado para otro usuario
function f_validar_privilegio () {
    $.ajax({type: "POST",
			url:"../pag_ajax/validar_privilegio.php",
			data: {'id': document.getElementById('txt_id').value},
			success: function(result){
			if (result != '') {
				result = '<div id="msg-global" class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button><div id="msg-error">' + result + '</div></div>';
				document.getElementById('boton_salvar').style='display: none;';			
			} else {
				document.getElementById('boton_salvar').style='display:inline;';			
			}
			alert(document.getElementById('boton_salvar').style);
			// Poniendo el resultado en el mensaje de texto
			$("#msg-completo").html(result);
			},
		    error: function (xhr, ajaxOptions, thrownError) {
				alert(xhr.status);
				alert(thrownError);
	        }
		}
	);
}
</script>
<?PHP require_once('../pie_js.php'); ?>
<?PHP require_once('../modal_pie.php'); ?>
<?PHP require_once('../timer_logout.php'); ?>
<?PHP require_once('../modal_encabezado.php'); ?>
</body>
<script src="<?PHP echo $Dominio;?>js/expresiones_regulares.js"></script>	
</html>
