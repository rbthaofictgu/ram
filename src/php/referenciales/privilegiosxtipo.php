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

$rotulo = "MANTENIMIENTO:  Agregando ". $tablanext;
// Armando el query
$query_rs_usuario = "SELECT id, descripcion from tipo_usuario order by descripcion";
// Preparando la sentencia
$usuario = $conn->prepare($query_rs_usuario);
// Ejecutanto el query
$res = $usuario->execute();
// Si hay algun error
if (isset($res) and $res == '') {
	$msg = "Error intentando leer tabla de ubicacion, error -> " . geterror($usuario->errorInfo());
	$usuario->closeCursor();
	$totalRows_rs_usuario = 0;
	$clase = "alert alert-danger alert-dismissible";
} else {
	$row_rs_usuario = $usuario->fetch();
	$totalRows_rs_usuario  = $usuario->rowcount();
}

// Si no se ha realizando una eleccion de un tipo de usuario por el usuario se utiliza el primer tipo de usuario en la tabla
if ($rs_tipo_rs_mantenimiento < 1) {
	$rs_tipo_rs_mantenimiento = $row_rs_usuario['id'];
}

// Armando el query
$query_rs_privilegio = "SELECT a.id, a.descripcion, a.menu_padre from privilegio a where (select count(*) from privilegio_x_tipo_usuario b where b.id_tipo_usuario = :id_tipo_usuario and a.id = b.id_privilegio) = 0 order by a.nivel_menu";
// Preparando la sentencia
$privilegio = $conn->prepare($query_rs_privilegio);
// Ejecutanto el query
$res = $privilegio->execute(Array('id_tipo_usuario' => $rs_tipo_rs_mantenimiento));
// Si hay algun error
if (isset($res) and $res == '') {
	$msg = "Error intentando leer tabla de ubicacion, error -> " . geterror($privilegio->errorInfo());
	$privilegio->closeCursor();
	$totalRows_rs_privilegio = 0;
	$clase = "alert alert-danger alert-dismissible";
} else {
	$row_rs_privilegio = $privilegio->fetch();
	$totalRows_rs_privilegio  = $privilegio->rowcount();
}

// Armando el query
$query_rs_privilegioxusuario = "SELECT a.id, a.descripcion,a.menu_padre from privilegio a, privilegio_x_tipo_usuario b where a.id = b.id_privilegio and b.id_tipo_usuario = :id_tipo_usuario  order by a.nivel_menu";
// Preparando la sentencia
$privilegioxusuario = $conn->prepare($query_rs_privilegioxusuario);
// Ejecutanto el query
$res = $privilegioxusuario->execute(Array('id_tipo_usuario' => $rs_tipo_rs_mantenimiento));
// Si hay algun error
if (isset($res) and $res == '') {
	$msg = "Error intentando leer tabla de ubicacion, error -> " . geterror($privilegioxusuario->errorInfo());
	$privilegioxusuario->closeCursor();
	$totalRows_rs_privilegioxusuario = 0;
	$clase = "alert alert-danger alert-dismissible";
} else {
	$row_rs_privilegioxusuario = $privilegioxusuario->fetch();
	$totalRows_rs_privilegioxusuario  = $privilegioxusuario->rowcount();
}

?>
<!doctype html>
<html lang="es">
<head>
<title>MANTENIMIENTO <?php echo strtoupper($tablanext) ; ?></title>
<?php require_once('../encabezado.php'); ?>
<?PHP require_once('../modal_encabezado.php'); ?>
</head>
<body>
<div class="main">
<?php require_once('../encabezado_body.php'); ?>
  <form id="form1" name="form1" method="post" action="<?php echo $currentPage; ?>">
<?php require_once('../rotulo_pantalla.php'); ?>
<div class="table-responsive">
 <table width="100%" align="center" class="paleta_tr3">
    <tr>
    <td width="8.5%"></td>
     <td width="91.5%">
        <div align="left">
            <button type="button" class="btn btn-success btn-sm" id="cmd_salvar" name="cmd_salvar" onclick="f_validar_agregar(this.form.dwd_tipo.value)">
                <i class="fas fa-gavel"></i>
                <span class="hidden-xs">Otorgar</span>
            </button>
            <button type="button" class="btn btn-light btn-sm" id="cmd_tipo" name="cmd_tipo" onclick="f_validar_borrar(this.form.dwd_tipo.value)">
                <i class="fas fa-trash-alt"></i>
                <span class="hidden-xs">Quitar</span>
            </button>
            
            <button type="button" class="btn btn-light btn-sm" id="cmd_limpiar" name="cmd_cancelar" onclick="window.location='<?PHP echo $currentPage;?>'">
                <i class="fas fa-undo-alt"></i>
                <span class="hidden-xs">Limpiar</span>
            </button>
            
            <button type="button" class="btn btn-light btn-sm" id="cmd_retornar" name="cmd_retornar" onclick="window.location='<?PHP echo $pantalla[0] ;?>_lista.php'">
                <i class="far fa-window-close"></i>
                <span class="hidden-xs">Cerrar</span>
            </button>
        </div>
        
		</td>
    </tr>
   </table>
   </div>
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
<div id="msg-global" class="<?PHP echo $clase;?>" role="alert">
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
      <table class="table table-condensed">
        <tr>
          <td width="8%">&nbsp;</td>
          <td width="20%"><label class="tooltip-test" data-placement="top" data-original-title="Seleccione el tipo de usuario al cúal le desea trabajar los privilegios">
          <div id="label-nombre">Rol de Usuario:</div></label></td>
          <td width="72%"><select name="dwd_tipo" id="dwd_tipo" class="form-control" onchange="f_cargar(this.value)">
            <?PHP 
			for ($i=0;$totalRows_rs_usuario>$i;$i++) {?>
            <option value="<?php echo $row_rs_usuario['id'];?>" <?php if (!(strcmp($row_rs_usuario['id'], $rs_tipo_rs_mantenimiento))) {echo "selected=\"selected\"";} ?>><?php echo $row_rs_usuario['descripcion'];?></option>
            <?PHP 
				$row_rs_usuario = $usuario->fetch();          
			} ?>
          </select></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><label class="tooltip-test" data-placement="top" data-original-title="Seleccione los privilegios que desea otorgarle al tipo de usuario">Privilegios</label></td>
          <td><label class="tooltip-test" data-placement="top" data-original-title="Aqui apareceran los privilegios que le han sido asignados al tipo de usuario seleccionado">Privilegios por Rol de Usuario</label>
</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>
            <div align="left">
            <select name="lst_privilegio" id="lst_privilegio" size="<?PHP echo $totalRows_rs_privilegio;?>" multiple="multiple">
            <?PHP 
            for ($i=0;$totalRows_rs_privilegio>$i;$i++) {?>
				<?PHP if($row_rs_privilegio['menu_padre'] == '' ) { ?>
                <option class="text-info" value="<?php echo $row_rs_privilegio['id'];?>"><?php echo $row_rs_privilegio['descripcion'];?></option>
               <?PHP } else { ?>
               <option class="text-success" value="<?php echo $row_rs_privilegio['id'];?>">&nbsp;&nbsp;&nbsp; <?php echo $row_rs_privilegio['descripcion'];?></option>
               <?PHP }  ?>
            <?PHP 
            $row_rs_privilegio = $privilegio->fetch();          
            } ?>
            </select>
            </div>
          </td>
          <td>

            <label class="tooltip-test" data-placement="top" data-original-title="Aqui apareceran los privilegios que le seran otorgados a todos los usuarios que se creen con el tipo de usuario seleccionado"></label>
            <div align="left">
            <select name="lst_privilegioxusuario" id="lst_privilegioxusuario" size="<?PHP echo $totalRows_rs_privilegioxusuario;?>" multiple="multiple">
            <?PHP 
            for ($i=0;$totalRows_rs_privilegioxusuario>$i;$i++) {?>
			<?PHP if($row_rs_privilegioxusuario['menu_padre'] == '' ) { ?>            
	            <option class="text-info" value="<?php echo $row_rs_privilegioxusuario['id'];?>"><?php echo $row_rs_privilegioxusuario['descripcion'];?></option>
            <?PHP } else { ?>
	            <option class="text-success" value="<?php echo $row_rs_privilegioxusuario['id'];?>">&nbsp;&nbsp;&nbsp;<?php echo $row_rs_privilegioxusuario['descripcion'];?></option>
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
    
   <?php if (isset($row_rs_mantenimiento['id'])){ ?>
    <input type="hidden" name="MM_update" id="MM_update" value="frm_mantenimiento" />
    <input type="hidden" name="id"  id="id"  value="<?php echo $rs_id_rs_mantenimiento ;?>" />
   <?php } else { ?>
   	   <input type="hidden" name="MM_insert" id="MM_insert" value="frm_mantenimiento" />
	<?php }  ?>
  </form>
  
  </div>
<script language="JavaScript" type="text/javascript" charset="UTF-8">
// Ubicar el cursos en el campo nombre
document.getElementById("dwd_tipo").focus();
//*************************************************************************************************************	
//*******Función para llamar programa php para salvar los datos de la pantalla de pes
//*************************************************************************************************************
function f_salvar(f_id,f_usuario){
    $.ajax({type: "POST",
			url:"privilegiotipousuario_salvar.php",
			data: {'tipo': f_usuario,'privilegio': f_id},
			success:function(result){
		if (result.trim() != '') {
			alert('Error: ' + result)
		}		
    }});
}
//*************************************************************************************************************	
//*******Función para llamar programa php para salvar los datos de la pantalla de pes
//*************************************************************************************************************
function f_borrar(f_id,f_usuario){	
    $.ajax({type: "POST",
			url:"privilegiotipousuario_borrar.php",
			data: {'tipo': f_usuario,'privilegio': f_id},
			success:function(result){
		if (result.trim() == '') {
			$("#msg-error").html("Privilegios des-otorgados correctamente");
		} else {
			alert('Error: ' + result)
		}		
    }});
}

<!-- Agregar el item del select de estados al de estdos por usuario y lo elimina del de estado, llama al programa de agregar estdo_usuarios
function f_validar_agregar(f_usuario)
{
		var selectedArray = new Array();
		var selObj = document.getElementById('lst_privilegio');
		var selObj1 = document.getElementById('lst_privilegioxusuario');
		var i;
		var count = 0;
		for (i=0; i<selObj.options.length; i++) {
			if (selObj.options[i].selected) {
			  var newOpt1 = new Option(selObj.options[i].text,selObj.options[i].value);
			  selObj1.options[selObj1.options.length] = newOpt1;
			  // rutina de salvado de información
			  f_salvar(selObj.options[i].value,f_usuario)
			  count++;
			  selObj.options[i] = null;
			  i--;
			}
		}
		// Sino se selecciono ningun item se envie este mensaje de error
		if (count == 0) {
			$("#msg-completo").html('<div id="msg-global" class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button><div id="msg-error">Favor seleccione un Item a agregar</div>');		
		} else {
			document.getElementById('lst_privilegioxusuario').size =  selObj1.options.length;
$("#msg-completo").html('<div id="msg-global" class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button><div id="msg-error">Privilegios otorgados correctamente</div>');
		}
}
//-->
<!--
function f_validar_borrar(f_usuario)
{
		var selectedArray = new Array();
		var selObj = document.getElementById('lst_privilegioxusuario');
		var selObj1 = document.getElementById('lst_privilegio');
		var i;
		var count = 0;
		for (i=0; i<selObj.options.length; i++) {
			if (selObj.options[i].selected) {
			  var newOpt1 = new Option(selObj.options[i].text,selObj.options[i].value);
			  selObj1.options[selObj1.options.length] = newOpt1;
			  // rutina de salvado de información
			  f_borrar(selObj.options[i].value,f_usuario)
			  count++;
			  selObj.options[i] = null;
			  i--;
			}
		}

		// Sino se selecciono ningun item se envie este mensaje de error		
		if (count == 0) {
			$("#msg-completo").html('<div id="msg-global" class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button><div id="msg-error">Favor seleccione un Item a borrar</div>');
		} else {
			document.getElementById('lst_privilegio').size =  selObj1.options.length;
$("#msg-completo").html('<div id="msg-global" class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button><div id="msg-error">Privilegios des-otorgados satisfactoriamente</div>');
		}
}
//-->
function f_cargar(f_tipo)
{
	window.location='privilegiosxtipo.php?id='+f_tipo;
}
</script>
<?PHP require_once('../pie.php'); ?>
<?PHP require_once('../pie_js.php'); ?>
<?PHP require_once('../modal_pie.php'); ?>
<?PHP require_once('../timer_logout.php'); ?>  
</body>
<script src="<?PHP echo $Dominio;?>js/expresiones_regulares.js"></script>	
<?php
$usuario->closeCursor();
$privilegio->closeCursor();
$privilegioxusuario->closeCursor();
?>
</html>