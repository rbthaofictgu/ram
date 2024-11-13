<?php
/* validando que paso por la pantalla de login */
session_start();
if (!isset($_SESSION['user_name'])) {
	header("location: ../../index.php?msg=Favor inicie sesión para poder ingresar al sistema"); exit();
}
?>

<?php require_once('../../utils/tipo_dispositivo.php'); ?>
<!-- //* archivo de configuracion donde se encuentrar las variables globales. -->
<?php require_once('../../../configuracion/configuracion.php'); ?>
<!-- //*archivo de configuracion de lña conexion con la base de datos -->
<?php require_once("../../../../config/conexion.php"); ?>
<!-- //* archivo de funciones varias(recuperar url,intentos fallidos,validar accesos, etc.) -->
<?php require_once('../../utils/funciones_db.php'); ?>

<?php
// Validando privilegios
$codigo = 'CAMBIARESTILO';
require_once('../../utils/validar_privilegio.php');
$pantallax = explode('/',$currentPage);
$pantallax = $pantallax[(count($pantallax)-1)];
$pantallax = explode('.',$pantallax);
$pantalla[0] = $pantallax[0];
$msg = '';
$clase = 'alert alert-success alert-dismissible';

if (isset(($_POST["dwd_estilo"]))) {
	$id_estilo = $_POST["dwd_estilo"];
	$_SESSION['id_estilo'] = $id_estilo;
} else {
	$id_estilo = $_SESSION['id_estilo'];
}
// echo $_SESSION['STYLE'];
// echo json_encode($id_estilo);
// Inicio Actualizando registro de disponibilidad
 if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "cambiar") && isset($_POST["cmd_salvar"])) {
	//Armando query para actaulizar usuario
	// $updateSQL = "UPDATE usuarios SET id_estilo_usuario=:id_estilo_usuario WHERE id=:codigo_usuario";
	$updateSQL = "UPDATE [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_estilo] 
					  SET id=:id_estilo_usuario 
					  WHERE usuario_modificacion=:codigo_usuario";
	//Comensando la transacción
	$db->beginTransaction();
	//Preparando ejecución de sentencia SQL
	$stmt = $db->prepare($updateSQL);
	//Ejecutanto sentencia SQL
	$res = $stmt->execute(Array(':id_estilo_usuario' => intval($_POST["dwd_estilo"]),':codigo_usuario' => $_SESSION["user_name"]));
	$res = $stmt->errorInfo();
	// Validando que el error de la ejecución
	if (isset($res) and $res[2] <> '') { // Si hay error en la ejecución de la sentencia SQL
		$msg = "Error intentando actualizar registro de estilos-usuarios, error ->" . geterror($stmt->errorInfo());
		$stmt->closeCursor();
		$db->rollBack();
	} else {
		$stmt->closeCursor();
		$db->commit();
	}
} else {
	  if (isset($_POST["cmd_estilo"])){
		  $query_rs_estilo = "update [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_estilo] set
			descripcion=:descripcion,
			color_texto_body=:color_texto_body,
			color_texto_navbar=:color_texto_navbar,
			color_texto_rotulo=:color_texto_rotulo,
			color_texto_botones=:color_texto_botones,
			color_texto_detalle=:color_texto_detalle,
			color_text_boton_primary=:color_text_boton_primary,
			color_boder_boton_primary=:color_boder_boton_primary,
			color_sombra_boton_primary=:color_sombra_boton_primary,
			color_hover_text_boton_primary=:color_hover_text_boton_primary,
			color_hover_border_boton_primary=:color_hover_border_boton_primary,
			color_hover_sombra_boton_primary=:color_hover_sombra_boton_primary,
			color_text_boton_secondary=:color_text_boton_secondary,
			color_boder_boton_secondary=:color_boder_boton_secondary,
			color_sombra_boton_secondary=:color_sombra_boton_secondary,
			color_sombra_boton_secondary=:color_sombra_boton_secondary,
			color_hover_border_boton_secondary=:color_hover_border_boton_secondary,
			color_hover_sombra_boton_secondary=:color_hover_sombra_boton_secondary,
			color_bg_body=:color_bg_body,
			color_bg_navbar=:color_bg_navbar,
			color_bg_navbar_dwd=:color_bg_navbar_dwd,
			color_bg_rotulo=:color_bg_rotulo,
			color_bg_linea_botones=:color_bg_linea_botones,
			color_bg_detalle=:color_bg_detalle,
			color_bg_boton_primary=:color_bg_boton_primary,
			color_bg_hover_botom_primary=:color_bg_hover_botom_primary,
			color_bg_boton_secondary=:color_bg_boton_secondary,
			color_bg_hover_botom_secondary=:color_bg_hover_botom_secondary,
			color_link=:color_link,
			color_link_visited=:color_link_visited,
			color_link_hover=:color_link_hover,
			color_link_active=:color_link_active,
			color_navbar_link=:color_navbar_link,
			color_navbar_link_visited=:color_navbar_link_visited,
			color_navbar_link_hover=:color_navbar_link_hover,
			color_navbar_link_active=:color_navbar_link_active,
			color_navbar_dwd_link=:color_navbar_dwd_link,
			color_navbar_dwd_link_visited=:color_navbar_dwd_link_visited,
			color_navbar_dwd_link_hover=:color_navbar_dwd_link_hover,
			color_navbar_dwd_link_active=:color_navbar_dwd_link_active,
			color_footer_link=:color_footer_link,
			color_footer_link_visited=:color_footer_link_visited,
			color_footer_link_hover=:color_footer_link_hover,
			color_footer_link_active=:color_footer_link_active
			where
			id=:id";
			$stmt = $db->prepare($query_rs_estilo);
			//Ejecutanto sentencia SQL
			$res = $stmt->execute(Array(':descripcion' => $_POST['descripcion'],
										':color_texto_body' => $_POST['color_texto_body'],
										':color_texto_navbar' => $_POST['color_texto_navbar'],
										':color_texto_rotulo' => $_POST['color_texto_rotulo'],
										':color_texto_botones' => $_POST['color_texto_botones'],
										':color_texto_detalle' => $_POST['color_texto_detalle'],
										'color_text_boton_primary' => $_POST['color_text_boton_primary'],
										':color_boder_boton_primary' => $_POST['color_boder_boton_primary'],
										':color_sombra_boton_primary' => $_POST['color_sombra_boton_primary'],
										':color_hover_text_boton_primary' => $_POST['color_hover_text_boton_primary'],
										':color_hover_border_boton_primary' => $_POST['color_hover_border_boton_primary'],
										':color_hover_sombra_boton_primary' => $_POST['color_hover_sombra_boton_primary'],
										':color_text_boton_secondary' => $_POST['color_text_boton_secondary'],
										':color_boder_boton_secondary' => $_POST['color_boder_boton_secondary'],
										':color_sombra_boton_secondary' => $_POST['color_sombra_boton_secondary'],
										':color_sombra_boton_secondary' => $_POST['color_sombra_boton_secondary'],
										':color_hover_border_boton_secondary' => $_POST['color_hover_border_boton_secondary'],
										':color_hover_sombra_boton_secondary' => $_POST['color_hover_sombra_boton_secondary'],			
										':color_bg_body' => $_POST['color_bg_body'],
										':color_bg_navbar' => $_POST['color_bg_navbar'],
										':color_bg_navbar_dwd' => $_POST['color_bg_navbar_dwd'],
										':color_bg_rotulo' => $_POST['color_bg_rotulo'],
										':color_bg_linea_botones' => $_POST['color_bg_linea_botones'],
										':color_bg_detalle' => $_POST['color_bg_detalle'],
										':color_bg_boton_primary' => $_POST['color_bg_boton_primary'],
										':color_bg_hover_botom_primary' => $_POST['color_bg_hover_botom_primary'],
										':color_bg_boton_secondary' => $_POST['color_bg_boton_secondary'],
										':color_bg_hover_botom_secondary' => $_POST['color_bg_hover_botom_secondary'],
										':color_link' => $_POST['color_link'],
										':color_link_visited' => $_POST['color_link_visited'],
										':color_link_hover' => $_POST['color_link_hover'],
										':color_link_active' => $_POST['color_link_active'],
										':color_navbar_link' => $_POST['color_navbar_link'],
										':color_navbar_link_visited' => $_POST['color_navbar_link_visited'],
										':color_navbar_link_hover' => $_POST['color_navbar_link_hover'],
										':color_navbar_link_active' => $_POST['color_navbar_link_active'],	
										':color_navbar_dwd_link' => $_POST['color_navbar_dwd_link'],
										':color_navbar_dwd_link_visited' => $_POST['color_navbar_dwd_link_visited'],
										':color_navbar_dwd_link_hover' => $_POST['color_navbar_dwd_link_hover'],
										':color_navbar_dwd_link_active' => $_POST['color_navbar_dwd_link_active'],	
										':color_footer_link' => $_POST['color_footer_link'],
										':color_footer_link_visited' => $_POST['color_footer_link_visited'],
										':color_footer_link_hover' => $_POST['color_footer_link_hover'],
										':color_footer_link_active' => $_POST['color_footer_link_active'],	
										':id' => $_POST["dwd_estilo"]));			  
		 //echo $_POST['color_link_hover'];
		 // echo $_POST['color_link_active'];
		 //echo $query_rs_estilo;
		  //echo $_POST["dwd_estilo"];
		  $res = $stmt->errorInfo();
		  //echo $_POST['color_bg_navbar'];
		  //print_r($res);exit();
// Validando que el error de la ejecución
		if (isset($res) and $res[2] <> '') {		
			$msg = "Error intentando actuailzar registro de estilos, error -> " . geterror($stmt->errorInfo());
			$stmt->closeCursor();
		} else {
			$stmt->closeCursor();
		}		  
	  }
 }

// $query_rs_estilo = "SELECT 
// 	id,
// 	color_bg_body,
// 	color_texto_body,
//     color_texto_navbar,
//     color_texto_rotulo,
//     color_texto_botones,
//     color_texto_detalle,
//     color_text_boton_primary,
//     color_boder_boton_primary,
//     color_sombra_boton_primary,
//     color_hover_text_boton_primary,
//     color_hover_border_boton_primary,
//     color_hover_sombra_boton_primary,
//     color_text_boton_secondary,
//     color_boder_boton_secondary,
//     color_sombra_boton_secondary,
//     color_hover_text_boton_secondary,
//     color_hover_border_boton_secondary,
//     color_hover_sombra_boton_secondary,
//     color_bg_body,
//     color_bg_navbar,
//     color_bg_navbar_dwd,
//     color_bg_rotulo,
//     color_bg_linea_botones,
//     color_bg_detalle,
//     color_bg_boton_primary,
//     color_bg_hover_botom_primary,
//     color_bg_boton_secondary,
//     color_bg_hover_botom_secondary,
//     color_link,
//     color_link_visited,
//     color_link_hover,
//     color_link_active,
//     color_navbar_link,
//     color_navbar_link_visited,
//     color_navbar_link_hover,
//     color_navbar_link_active,
//     color_navbar_dwd_link,
//     color_navbar_dwd_link_visited,
//     color_navbar_dwd_link_hover,
//     color_navbar_dwd_link_active,
//     color_footer_link,
//     color_footer_link_visited,
//     color_footer_link_hover,
//     color_footer_link_active,
//     navbar_font,
//     body_font,a.nombre,a.id as id_estilo,a.nombre as estilo,descripcion 
// 	 FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_estilo] a where a.estado = 'A' and a.id = :id";
$query_rs_estilo = "SELECT 
    u.Usuario_Nombre as usuario,
    u.Usuario_Password as clave,
    u.Estado_Usuario as estado,
    u.ID_Empleado,
    e.id as id_estilo,
    e.nombre as estilo,
    e.descripcion as descripcion,
	 e.color_texto_body,
    e.color_texto_navbar,
    e.color_texto_rotulo,
    e.color_texto_botones,
    e.color_texto_detalle,
    e.color_text_boton_primary,
    e.color_boder_boton_primary,
    e.color_sombra_boton_primary,
    e.color_hover_text_boton_primary,
    e.color_hover_border_boton_primary,
    e.color_hover_sombra_boton_primary,
    e.color_text_boton_secondary,
    e.color_boder_boton_secondary,
    e.color_sombra_boton_secondary,
    e.color_hover_text_boton_secondary,
    e.color_hover_border_boton_secondary,
    e.color_hover_sombra_boton_secondary,
    e.color_bg_body,
    e.color_bg_navbar,
    e.color_bg_navbar_dwd,
    e.color_bg_rotulo,
    e.color_bg_linea_botones,
    e.color_bg_detalle as color_bg_detalle,
    e.color_bg_boton_primary,
    e.color_bg_hover_botom_primary,
    e.color_bg_boton_secondary,
    e.color_bg_hover_botom_secondary,
    e.color_link,
    e.color_link_visited,
    e.color_link_hover,
    e.color_link_active,
    e.color_navbar_link,
    e.color_navbar_link_visited,
    e.color_navbar_link_hover,
    e.color_navbar_link_active,
    e.color_navbar_dwd_link,
    e.color_navbar_dwd_link_visited,
    e.color_navbar_dwd_link_hover,
    e.color_navbar_dwd_link_active,
    e.color_footer_link,
    e.color_footer_link_visited,
    e.color_footer_link_hover,
    e.color_footer_link_active,
    e.navbar_font,
    e.body_font
FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_estilo] e
JOIN [IHTT_USUARIOS].[dbo].[TB_Usuarios] u
ON e.[usuario_modificacion] = u.Usuario_Nombre
WHERE e.estado='A' AND  e.id=:id";

		$usuarios = $db->prepare($query_rs_estilo);
		$res = $usuarios->execute(array(':id' => $id_estilo));
	// Validando que el error de la ejecución
		if (isset($res[1]) and $res[2] <> '') {	  	
			$msg = "Error intentando leer registro de estilos, error -> " . geterror($usuarios->errorInfo());
			$stmt->closeCursor();
		} else {
			$row_rs_usuarios = $usuarios->fetch();
			$totalRows_rs_usuario = $usuarios->rowcount();
			if ($totalRows_rs_usuario > 0) {
				$gy_estilos = 'S';
				require_once('session_validar.php');
				// Recarga de pantalla con mensaje de procesamiento satisfactorio
				$msg = "Se ha actualizado las caractristicas del estilo";  
			} else {
				$msg = "Error leyendo registro de estilos";  
			}
		}

// estilos
$query_rs_estilo = "SELECT e.id, e.descripcion FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_estilo] e 
where e.estado = 'A' order by e.descripcion";
$estilo = $db->prepare($query_rs_estilo);
$res = $estilo->execute();
$row_rs_estilo = $estilo->fetch();
$totalRows_rs_estilo = $estilo->rowcount();

$rotulo = 'Cambiar Estilo';
$currentPage = $_SERVER["PHP_SELF"];

if (isset($_GET['msg']) and $_GET['msg'] <> "")  {
	$clase = "alert alert-success";
	$msg = $_GET['msg'];
} else {
	$clase = "X";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
   <title>MANTENIMIENTO <?php echo strtoupper($tablanext) ; ?></title>
	  <?php require_once('../../../encabezado_body.php'); ?>

   <script>
   <?php 
	$display = 'style="display: none"';	
	// if ($row_rs_mantenimiento['habilitar_detalle'] == 'S') { 
	// 	$display = 'style="display: block"';	?>
   // <?php //} ?>
   </script>
   <style>
   .bd-placeholder-img {
      font-size: 1.125rem;
      text-anchor: middle;
      -webkit-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
   }

   @media (min-width: 768px) {
      .bd-placeholder-img-lg {
         font-size: 3.5rem;
      }
   }
   </style>
   <!-- Custom styles for this template -->
   <!-- <link href="form-validation.css" rel="stylesheet"> -->
</head>

<body>
   <?php require_once('../../../menu_segun_privilegios.php'); ?>
   <?php require_once('../../../rotulo_pantalla.php'); ?>
   <form id="form1" name="form1" method="post" action="<?php echo $currentPage; ?>" class="needs-validation" novalidate>
      <div class="table-responsive">
         <table width="100%" align="center" class="paleta_tr3">
            <tr>
               <td width="100%">
                  <div align="left">
                     <button type="submit" class="btn btn btn-success" id="cmd_salvar" name="cmd_salvar"
                        onclick="return f_validar()">
                        <i class="far fa-save"></i>
                        <span class="hidden-xs">Salvar</span>
                     </button>

                     <button type="submit" class="btn btn btn-light" id="cmd_estilo" name="cmd_estilo"
                        onclick="return f_validar_css()">
                        <i class="fab fa-css3-alt"></i>
                        <span class="hidden-xs">Cambiar CSS</span>
                     </button>


                     <?php if ( $Environment == 'DEV') { ?>

                     <button type="button" class="btn btn-secondary" id="cmd_retornar" name="cmd_retornar"
                        onclick="window.location='cambiarestilo.php'">
                        <i class="far fa-window-close"></i>
                        <span class="hidden-xs">Limpiar</span>
                     </button>

                     <a class="btn btn-sm btn-light btn-custom-primary" href="#" role="button">Browse gallery</a>
                     <a class="btn btn-sm btn-secondary btn-custom-secondary" href="#" role="button"><span
                           class="gy_link_navbar"></span>Browse gallery</span></a>
                     <a class="" href="#" role="button">Browse gallery</a>

                     <?php }   ?>

               </td>
            </tr>
         </table>
      </div>
      <div class="table-responsive">
         <table width="100%" class="table table-unbordered">
            <tr>
               <td width="8%">&nbsp;</td>
               <th width="17%"><label class="tooltip-test" title="" data-placement="top"
                     data-original-title="Aqui se muestra el código y nombre del usuario en sesión actualmente">Nombre
                     de Usuario</label></th>
               <td width="85%"><?php echo $_SESSION["user_name"] . '-' . $usuario = $_SESSION["user_name"];?></td>
            </tr>
            <tr>
               <td width="8%">&nbsp;</td>
               <th><label id="label-estilo" class="tooltip-test" title="" data-placement="top"
                     data-original-title="Seleccione un estilo para cambiar la apariencia del sistema">Estilo:<?php echo $_SESSION['STYLE']; echo $_SESSION['id_estilo']; ?></label>
               </th>
               <td><select name="dwd_estilo" id="dwd_estilo" class="form-control">
                     <option value="0">>Seleccione el estilo de usuario</option>
                     <?PHP 
			for ($i=0;$totalRows_rs_estilo>$i;$i++) {?>
                     <option value="<?php echo $row_rs_estilo['id'];?>"
                        <?php if (!(strcmp($row_rs_estilo['id'], $_SESSION['id_estilo']))) {echo "selected=\"selected\"";} ?>>
                        <?php echo $row_rs_estilo['descripcion'];?></option>
                     <?PHP 
				$row_rs_estilo = $estilo->fetch();          
			} ?>
                  </select> </td>
            </tr>
         </table>
      </div>

      <?php if ( $Environment == 'DEV') { ?>
      <div class="row">
         <div class="mb-3"><strong>Nombre:</strong></div>
         <div class="mb-6"><input name="descripcion" type="text" class="form-control" id="descripcion"
               value="<?php echo $row_rs_usuarios['descripcion']; ?>" /></div>
      </div>
      <div class="row">
         <div class="col-md-3 order-md-4">
            <div class="mb-1"><strong>BACKGROUND</strong></div>
            <div class="mb-1"><strong>color_texto_body</strong></div>
            <div class="mb-1"><input style="background-color: <?php echo $row_rs_usuarios['color_texto_body']; ?>"
                  name="color_texto_body" type="text" onchange="viewColor(this)" class="form-control"
                  id="color_texto_body" value="<?php echo $row_rs_usuarios['color_texto_body']; ?>" /></div>
            <div class="mb-1"><strong>color_texto_navbar</strong></div>
            <div class="mb-1"><input style="background-color: <?php echo $row_rs_usuarios['color_texto_navbar']; ?>"
                  name="color_texto_navbar" type="text" onchange="viewColor(this)" class="form-control"
                  id="color_texto_navbar" value="<?php echo $row_rs_usuarios['color_texto_navbar']; ?>" /></div>
            <div class="mb-1"><strong>color_texto_rotulo</strong></div>
            <div class="mb-1"><input style="background-color: <?php echo $row_rs_usuarios['color_texto_rotulo']; ?>"
                  name="color_texto_rotulo" type="text" onchange="viewColor(this)" class="form-control"
                  id="color_texto_rotulo" value="<?php echo $row_rs_usuarios['color_texto_rotulo']; ?>" /></div>
            <div class="mb-1"><strong>color_texto_botones</strong></div>
            <div class="mb-1"><input style="background-color: <?php echo $row_rs_usuarios['color_texto_botones']; ?>"
                  name="color_texto_botones" type="text" onchange="viewColor(this)" class="form-control"
                  id="color_texto_botones" value="<?php echo $row_rs_usuarios['color_texto_botones']; ?>" /></div>
            <div class="mb-1"><strong>color_texto_detalle</strong></div>
            <div class="mb-1">
               <input style="background-color: <?php echo $row_rs_usuarios['color_texto_detalle']; ?>"
                  name="color_texto_detalle" type="text" onchange="viewColor(this)" class="form-control"
                  id="color_texto_detalle" value="<?php echo $row_rs_usuarios['color_texto_detalle']; ?>" />
            </div>
         </div>
         <div class="col-md-3 order-md-3">
            <div class="mb-1"><strong>BACKGROUND</strong></div>
            <div class="mb-1"><strong>BG BODY</strong></div>
            <div class="mb-1"><input style="background-color: <?php echo $row_rs_usuarios['color_bg_body']; ?>"
                  name="color_bg_body" type="text" onchange="viewColor(this)" class="form-control" id="color_bg_body"
                  value="<?php echo $row_rs_usuarios['color_bg_body']; ?>" /></div>
            <div class="mb-1"><strong>BG NAVBAR</strong></div>
            <div class="mb-1"><input style="background-color: <?php echo $row_rs_usuarios['color_bg_navbar']; ?>"
                  name="color_bg_navbar" type="text" onchange="viewColor(this)" class="form-control"
                  id="color_bg_navbar" value="<?php echo $row_rs_usuarios['color_bg_navbar']; ?>" /></div>
            <div class="mb-1"><strong>BG NAVBAR DWD</strong></div>
            <div class="mb-1"><input style="background-color: <?php echo $row_rs_usuarios['color_bg_navbar_dwd']; ?>"
                  name="color_bg_navbar_dwd" type="text" onchange="viewColor(this)" class="form-control"
                  id="color_bg_navbar_dwd" value="<?php echo $row_rs_usuarios['color_bg_navbar_dwd']; ?>" /></div>
            <div class="mb-1"><strong>ROTULO</strong></div>
            <div class="mb-1"><input style="background-color: <?php echo $row_rs_usuarios['color_bg_rotulo']; ?>"
                  name="color_bg_rotulo" type="text" onchange="viewColor(this)" class="form-control"
                  id="color_bg_rotulo" value="<?php echo $row_rs_usuarios['color_bg_rotulo']; ?>" /></div>
            <div class="mb-1"><strong>BOTONES</strong></div>
            <div class="mb-1"><input style="background-color: <?php echo $row_rs_usuarios['color_bg_linea_botones']; ?>"
                  name="color_bg_linea_botones" type="text" onchange="viewColor(this)" class="form-control"
                  id="color_bg_linea_botones" value="<?php echo $row_rs_usuarios['color_bg_linea_botones']; ?>" /></div>
            <div class="mb-1"><strong>DETALLE</strong></div>
            <div class="mb-1"><input style="background-color: <?php echo $row_rs_usuarios['color_bg_detalle']; ?>"
                  name="color_bg_detalle" type="text" onchange="viewColor(this)" class="form-control"
                  id="color_bg_detalle" value="<?php echo $row_rs_usuarios['color_bg_detalle']; ?>" /></div>
         </div>

         <div class="col-md-3 order-md-2">
            <div class="mb-1"><label><strong>LINK</strong></label></div>
            <div class="mb-1"><a herf=""><strong>LINK</strong></a></div>
            <div class="mb-3">
               <input style="background-color: <?php echo $row_rs_usuarios['color_link']; ?>" name="color_link"
                  type="text" onchange="viewColor(this)" class="form-control" id="color_link"
                  value="<?php echo $row_rs_usuarios['color_link']; ?>" />
            </div>
            <div class="mb-1"><a herf=""><strong>Visited</strong></a></div>
            <div class="mb-3">
               <input style="background-color: <?php echo $row_rs_usuarios['color_link_visited']; ?>"
                  name="color_link_visited" type="text" onchange="viewColor(this)" class="form-control"
                  id="color_link_visited" value="<?php echo $row_rs_usuarios['color_link_visited']; ?>" />
            </div>
            <div class="mb-1"><a herf=""><strong>Active</strong></a></div>
            <div class="mb-3">
               <input style="background-color: <?php echo $row_rs_usuarios['color_link_active']; ?>"
                  name="color_link_active" type="text" onchange="viewColor(this)" class="form-control"
                  id="color_link_active" value="<?php echo $row_rs_usuarios['color_link_active']; ?>" />
            </div>
            <div class="mb-1"><a herf=""><strong>HOVER</strong></a></div>
            <div class="mb-3">
               <input style="background-color: <?php echo $row_rs_usuarios['color_link_hover']; ?>"
                  name="color_link_hover" type="text" onchange="viewColor(this)" class="form-control"
                  id="color_link_hover" value="<?php echo $row_rs_usuarios['color_link_visited']; ?>" />
            </div>
            <div class="mb-1"><strong>Link NavBar</strong></div>
            <div class="mb-3">
               <input style="background-color: <?php echo $row_rs_usuarios['color_navbar_link']; ?>"
                  name="color_navbar_link" type="text" onchange="viewColor(this)" class="form-control"
                  id="color_navbar_link" value="<?php echo $row_rs_usuarios['color_navbar_link']; ?>" />
            </div>
            <div class="mb-1">Vistited NavBar</div>
            <div class="mb-3">
               <input style="background-color: <?php echo $row_rs_usuarios['color_navbar_link_visited']; ?>"
                  name="color_navbar_link_visited" type="text" onchange="viewColor(this)" class="form-control"
                  id="color_navbar_link_visited" value="<?php echo $row_rs_usuarios['color_navbar_link_visited']; ?>" />
            </div>
            <div class="mb-1">Active NavBar</div>
            <div class="mb-3">
               <input style="background-color: <?php echo $row_rs_usuarios['color_navbar_link_active']; ?>"
                  name="color_navbar_link_active" type="text" onchange="viewColor(this)" class="form-control"
                  id="color_navbar_link_active" value="<?php echo $row_rs_usuarios['color_navbar_link_active']; ?>" />
            </div>
            <div class="mb-1">HOVER NavBar</div>
            <div class="mb-3">
               <input style="background-color: <?php echo $row_rs_usuarios['color_navbar_link_hover']; ?>"
                  name="color_navbar_link_hover" type="text" onchange="viewColor(this)" class="form-control"
                  id="color_navbar_link_hover" value="<?php echo $row_rs_usuarios['color_navbar_link_hover']; ?>" />
            </div>

            <div class="mb-1"><strong>Link NAVBAR_DWD</strong></div>
            <div class="mb-3">
               <input style="background-color: <?php echo $row_rs_usuarios['color_navbar_dwd_link']; ?>"
                  name="color_navbar_dwd_link" type="text" onchange="viewColor(this)" class="form-control"
                  id="color_navbar_dwd_link" value="<?php echo $row_rs_usuarios['color_navbar_dwd_link']; ?>" />
            </div>
            <div class="mb-1">Vistited NAVBAR_DWD</div>
            <div class="mb-3">
               <input style="background-color: <?php echo $row_rs_usuarios['color_navbar_dwd_link_visited']; ?>"
                  name="color_navbar_dwd_link_visited" type="text" onchange="viewColor(this)" class="form-control"
                  id="color_navbar_dwd_link_visited"
                  value="<?php echo $row_rs_usuarios['color_navbar_dwd_link_visited']; ?>" />
            </div>
            <div class="mb-1">Active NAVBAR_DWD</div>
            <div class="mb-3">
               <input style="background-color: <?php echo $row_rs_usuarios['color_navbar_dwd_link_active']; ?>"
                  name="color_navbar_dwd_link_active" type="text" onchange="viewColor(this)" class="form-control"
                  id="color_navbar_dwd_link_active"
                  value="<?php echo $row_rs_usuarios['color_navbar_dwd_link_active']; ?>" />
            </div>
            <div class="mb-1">HOVER NAVBAR_DWD</div>
            <div class="mb-3">
               <input style="background-color: <?php echo $row_rs_usuarios['color_navbar_dwd_link_hover']; ?>"
                  name="color_navbar_dwd_link_hover" type="text" onchange="viewColor(this)" class="form-control"
                  id="color_navbar_dwd_link_hover"
                  value="<?php echo $row_rs_usuarios['color_navbar_dwd_link_hover']; ?>" />
            </div>


            <div class="mb-1">
               <footer><a herf=""><strong>Link FOOTER</strong></a></footer>
            </div>
            <div class="mb-3">
               <input style="background-color: <?php echo $row_rs_usuarios['color_footer_link']; ?>"
                  name="color_footer_link" type="text" onchange="viewColor(this)" class="form-control"
                  id="color_footer_link" value="<?php echo $row_rs_usuarios['color_footer_link']; ?>" />
            </div>
            <div class="mb-1">
               <footer><a herf="">Vistited FOOTER</a></footer>
            </div>
            <div class="mb-3">
               <input style="background-color: <?php echo $row_rs_usuarios['color_footer_link_visited']; ?>"
                  name="color_footer_link_visited" type="text" onchange="viewColor(this)" class="form-control"
                  id="color_footer_link_visited" value="<?php echo $row_rs_usuarios['color_footer_link_visited']; ?>" />
            </div>
            <div class="mb-1"><a herf="">
                  <footer>Active FOOTER
               </a></footer>
            </div>
            <div class="mb-3">
               <input style="background-color: <?php echo $row_rs_usuarios['color_footer_link_active']; ?>"
                  name="color_footer_link_active" type="text" onchange="viewColor(this)" class="form-control"
                  id="color_footer_link_active" value="<?php echo $row_rs_usuarios['color_footer_link_active']; ?>" />
            </div>
            <div class="mb-1">
               <footer><a herf="">HOVER FOOTER</a></footer>
            </div>
            <div class="mb-3">
               <input style="background-color: <?php echo $row_rs_usuarios['color_footer_link_hover']; ?>"
                  name="color_footer_link_hover" type="text" onchange="viewColor(this)" class="form-control"
                  id="color_footer_link_hover" value="<?php echo $row_rs_usuarios['color_footer_link_hover']; ?>" />
            </div>


         </div>

         <div class="col-md-3 order-md-1">
            <div class="mb-1"><label>PRIMARY</label></div>
            <div class="mb-1"><a herf=""><strong>Link</strong></a></div>
            <div class="mb-1"><a class="btn btn-sm btn-custom-primary" href="#" role="button"><strong>Buttom
                     Link</strong></a></div>
            <div class="mb-1">
               <button type="button" class="btn btn-sm btn-custom-primary" id="cmd_retornar" name="cmd_retornar"
                  onclick="window.location='cambiarestilo.php'">
                  <i class="far fa-window-close"></i>
                  <span class="hidden-xs"><strong>Buttom</strong></span>
               </button>
            </div>
            <div class="mb-1"><label>color_text_boton_primary</label></div>
            <div class="mb-1">
               <input style="background-color: <?php echo $row_rs_usuarios['color_text_boton_primary']; ?>"
                  name="color_text_boton_primary" type="text" onchange="viewColor(this)" class="form-control"
                  id="color_text_boton_primary" value="<?php echo $row_rs_usuarios['color_text_boton_primary']; ?>" />
            </div>

            <div class="mb-1"><label>color_boder_boton_primary</label></div>
            <div class="mb-1">
               <input style="background-color: <?php echo $row_rs_usuarios['color_boder_boton_primary']; ?>"
                  name="color_boder_boton_primary" type="text" onchange="viewColor(this)" class="form-control"
                  id="color_boder_boton_primary" value="<?php echo $row_rs_usuarios['color_boder_boton_primary']; ?>"
                  </div>

               <div class="mb-1"><label>color_sombra_boton_primary</label></div>
               <div class="mb-1">
                  <input style="background-color: <?php echo $row_rs_usuarios['color_sombra_boton_primary']; ?>"
                     name="color_sombra_boton_primary" type="text" onchange="viewColor(this)" class="form-control"
                     id="color_sombra_boton_primary"
                     value="<?php echo $row_rs_usuarios['color_sombra_boton_primary']; ?>" />
               </div>

               <div class="mb-1"><label>color_bg_boton_primary</label></div>
               <div class="mb-1">
                  <input style="background-color: <?php echo $row_rs_usuarios['color_bg_boton_primary']; ?>"
                     name="color_bg_boton_primary" type="text" onchange="viewColor(this)" class="form-control"
                     id="color_bg_boton_primary" value="<?php echo $row_rs_usuarios['color_bg_boton_primary']; ?>" />
               </div>

               <div class="mb-1"><label>PRIMARY OVER</label></div>
               <div class="mb-1"><a class="btn btn-sm btn-custom-primary" href="#" role="button">Browse gallery</a>
               </div>

               <div class="mb-1"><label>color_hover_text_boton_primary</label></div>
               <div class="mb-1">
                  <input style="background-color: <?php echo $row_rs_usuarios['color_hover_text_boton_primary']; ?>"
                     name="color_hover_text_boton_primary" type="text" onchange="viewColor(this)" class="form-control"
                     id="color_hover_text_boton_primary"
                     value="<?php echo $row_rs_usuarios['color_hover_text_boton_primary']; ?>" </div>

                  <div class="mb-1"><label>color_hover_border_boton_primary</label></div>
                  <div class="mb-1">
                     <input
                        style="background-color: <?php echo $row_rs_usuarios['color_hover_border_boton_primary']; ?>"
                        name="color_hover_border_boton_primary" type="text" onchange="viewColor(this)"
                        class="form-control" id="color_bg_boton_primary"
                        value="<?php echo $row_rs_usuarios['color_hover_border_boton_primary']; ?>" </div>

                     <div class="mb-1"><label>color_hover_sombra_boton_primary</label></div>
                     <div class="mb-1">
                        <input
                           style="background-color: <?php echo $row_rs_usuarios['color_hover_sombra_boton_primary']; ?>"
                           name="color_hover_sombra_boton_primary" type="text" onchange="viewColor(this)"
                           class="form-control" id="color_hover_sombra_boton_primary"
                           value="<?php echo $row_rs_usuarios['color_hover_sombra_boton_primary']; ?>" </div>

                        <div class="mb-1"><label>color_bg_hover_boton_primary</label></div>
                        <div class="mb-1">
                           <input
                              style="background-color: <?php echo $row_rs_usuarios['color_bg_hover_botom_primary']; ?>"
                              name="color_bg_hover_botom_primary" type="text" onchange="viewColor(this)"
                              class="form-control" id="color_bg_hover_botom_primary"
                              value="<?php echo $row_rs_usuarios['color_bg_hover_botom_primary']; ?>" </div>
                           <div class="mb-1"><a class="btn btn-sm btn-custom-secondary" href="#"
                                 role="button"><strong>Buttom Link secondary</strong></a></div>
                           <div class="mb-1">
                              <button type="button" class="btn btn-sm btn-custom-secondary" id="cmd_retornar"
                                 name="cmd_retornar" onclick="window.location='cambiarestilo.php'">
                                 <i class="far fa-window-close"></i>
                                 <span class="hidden-xs"><strong>Buttom secondary</strong></span>
                              </button>
                           </div>

                           <div class="mb-1"><label>color_text_boton_secondary</label></div>
                           <div class="mb-1">
                              <input
                                 style="background-color: <?php echo $row_rs_usuarios['color_text_boton_secondary']; ?>"
                                 name="color_text_boton_secondary" type="text" onchange="viewColor(this)"
                                 class="form-control" id="color_text_boton_secondary"
                                 value="<?php echo $row_rs_usuarios['color_text_boton_secondary']; ?>" </div>

                              <div class="mb-1"><label>color_boder_boton_secondary</label></div>
                              <div class="mb-1">
                                 <input
                                    style="background-color: <?php echo $row_rs_usuarios['color_boder_boton_secondary']; ?>"
                                    name="color_boder_boton_secondary" type="text" onchange="viewColor(this)"
                                    class="form-control" id="color_boder_boton_secondary"
                                    value="<?php echo $row_rs_usuarios['color_boder_boton_secondary']; ?>" </div>

                                 <div class="mb-1"><label>color_sombra_boton_secondary</label></div>
                                 <div class="mb-1">
                                    <input
                                       style="background-color: <?php echo $row_rs_usuarios['color_sombra_boton_secondary']; ?>"
                                       name="color_sombra_boton_secondary" type="text" onchange="viewColor(this)"
                                       class="form-control" id="color_sombra_boton_secondary"
                                       value="<?php echo $row_rs_usuarios['color_sombra_boton_secondary']; ?>" </div>

                                    <div class="mb-1"><label>color_bg_boton_secondary</label></div>
                                    <div class="mb-1">
                                       <input
                                          style="background-color: <?php echo $row_rs_usuarios['color_bg_boton_secondary']; ?>"
                                          name="color_bg_boton_secondary" type="text" onchange="viewColor(this)"
                                          class="form-control" id="color_bg_boton_secondary"
                                          value="<?php echo $row_rs_usuarios['color_bg_boton_secondary']; ?>" </div>

                                       <div class="mb-1"><label>color_hover_text_boton_secondary</label></div>
                                       <div class="mb-1">
                                          <input
                                             style="background-color: <?php echo $row_rs_usuarios['color_hover_text_boton_secondary']; ?>"
                                             name="color_hover_text_boton_secondary" type="text"
                                             onchange="viewColor(this)" class="form-control"
                                             id="color_hover_text_boton_secondary"
                                             value="<?php echo $row_rs_usuarios['color_hover_text_boton_secondary']; ?>"
                                             </div>

                                          <div class="mb-1"><label>color_hover_border_boton_secondary</label></div>
                                          <div class="mb-1">
                                             <input
                                                style="background-color: <?php echo $row_rs_usuarios['color_hover_border_boton_secondary']; ?>"
                                                name="color_hover_border_boton_secondary" type="text"
                                                onchange="viewColor(this)" class="form-control"
                                                id="color_hover_border_boton_secondary"
                                                value="<?php echo $row_rs_usuarios['color_hover_border_boton_secondary']; ?>"
                                                </div>

                                             <div class="mb-1"><label>color_hover_sombra_boton_secondary</label></div>
                                             <div class="mb-1">
                                                <input
                                                   style="background-color: <?php echo $row_rs_usuarios['color_hover_sombra_boton_secondary']; ?>"
                                                   name="color_hover_sombra_boton_secondary" type="text"
                                                   onchange="viewColor(this)" class="form-control"
                                                   id="color_hover_sombra_boton_secondary"
                                                   value="<?php echo $row_rs_usuarios['color_hover_sombra_boton_secondary']; ?>"
                                                   </div>

                                                <div class="mb-1"><label>color_bg_hover_botom_secondary</label></div>
                                                <div class="mb-1">
                                                   <input
                                                      style="background-color: <?php echo $row_rs_usuarios['color_bg_hover_botom_secondary']; ?>"
                                                      name="color_bg_hover_botom_secondary" type="text"
                                                      onchange="viewColor(this)" class="form-control"
                                                      id="color_bg_hover_botom_secondary"
                                                      value="<?php echo $row_rs_usuarios['color_bg_hover_botom_secondary']; ?>"
                                                      </div>
                                                </div>
                                             </div>
                                             <?PHp } ?>
                                             <input name="MM_update" type="hidden" id="MM_update" value="cambiar" />
   </form>

   <?php 
	$clase_footer_content = 'footer_content';
	require_once('../default_footer_content.php'); 
	require_once('../../../pie.php'); 
?>
   <script>
   document.getElementById("dwd_estilo").focus();
   </script>
   <script>
   function f_validar() {
      divContenido = document.getElementById('msg-error');
      divContenido.innerHTML = ''
      $error = false;
      if (document.getElementById('dwd_estilo').value == 0) {
         divContenido.innerHTML = 'Campo Invalido:  Seleccione un estilo' + "<br/>";
         $("#label-estilo").addClass("label-error");
         $("#dwd-estilo").addClass("text-error");
         $error = true;
      } else {
         $("#label-estilo").removeClass("label-error");
         $("#dwd_estio").removeClass("text-error");
      }

      if ($error == true) {
         $("#msg-error").addClass("alert alert-danger").removeClass("x");
         return false;
      } else {
         return true;
      }

   }


   function f_validar_css() {
      divContenido = document.getElementById('msg-error');
      divContenido.innerHTML = ''
      $error = 'N';
      if (document.getElementById('dwd_estilo').value == 0) {
         divContenido.innerHTML = 'Campo Invalido:  Seleccione un estilo' + "<br/>";
         $("#label-estilo").addClass("label-error");
         $("#dwd-estilo").addClass("text-error");
         $error = true;
      } else {
         $("#label-estilo").removeClass("label-error");
         $("#dwd_estio").removeClass("text-error");
      }

      if (document.getElementById('color_navbar_link_active').value == '') {
         divContenido.innerHTML = divContenido.innerHTML +
            'Campo Invalido:  Seleccione un estilo para el color_navbar_link_active' + "<br/>";
         $("#label-estilo").addClass("label-error");
         $("#dwd-estilo").addClass("text-error");
         $error = true;
      } else {
         $("#label-estilo").removeClass("label-error");
         $("#dwd_estio").removeClass("text-error");
      }

      if (document.getElementById('color_texto_body').value == '') {
         divContenido.innerHTML = divContenido.innerHTML +
            'Campo Invalido:  Seleccione un estilo para el color_texto_body' + "<br/>";
         $("#label-estilo").addClass("label-error");
         $("#dwd-estilo").addClass("text-error");
         $error = true;
      } else {
         $("#label-estilo").removeClass("label-error");
         $("#dwd_estio").removeClass("text-error");
      }

      if ($error == true) {
         $("#msg-error").addClass("alert alert-danger").removeClass("x");
         return false;
      } else {
         return true;
      }

   }
   </script>
   <script>
   function envio_mensajes(a) {
      alert('La clave actual no es la correcta favor verifique');
   }
   </script>
   <script>
   function viewColor(texto) {
      $(texto).css('background-color', texto.value);
   }
   </script>
   <?PHP require_once('../pie_js.php'); ?>
   <?PHP require_once('../modal_pie.php'); ?>
   <?PHP require_once('../timer_logout.php'); ?>
   <?PHP require_once('../modal_encabezado.php'); ?>
</body>

</html>