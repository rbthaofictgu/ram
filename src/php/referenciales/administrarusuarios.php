<?php header('Content-Type: text/html; charset=utf-8'); ?>
<?php
/* validando que paso por la pantalla de login */
session_start();
if (!isset($_SESSION['user_name']) && !isset($_SESSION['hash_privado'])) {
   $appcfg_page_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
   $_SESSION['url'] = $appcfg_page_url;
   $_SESSION['flashmsg'] = "Favor inicie sesión para poder ingresar al sistema";
   header("location: ../inicio.php");
   exit();
}
?>
<?php require_once('../utils/get_user.php'); ?>
<?php require_once('../../utils/tipo_dispositivo.php'); ?>
<?php require_once('../../../../configuracion/configuracion.php'); ?>
<?php require_once('../utils/anterior_siguiente.php'); ?>
<?php require_once('../../utils/funciones_db.php'); ?>
<?php require_once('../../../config/conexion.php'); ?>
<?php require_once('../../utils/create_copy_row.php'); ?>
<?php
// Sino se ha realizado inicio se sesión (No existe $_SESSION['tipo'])
// Se hace una sesión rapida con el usuario de invitado para cargar
// las variable de style del sitio asociadas al usurio
if (!isset($_SESSION['tipo'])) {
   require_once('../../../validarajax.php');
}
$codigo = 'ADMINISTRACIONUSUARIOS';
if (isset($_SESSION['hash_privado']) == false) {
   require_once('../../utils/validar_privilegio.php');
} else {
   $tablanext = 'Usuarios';
   $icono_privilegio = '<i class="fas fa-users"></i>  ';
   $currentPage = $_SERVER["PHP_SELF"];
   $nobuttomnext = 'S';
}
$pantallax = explode('/', $currentPage);
$pantallax = $pantallax[(count($pantallax) - 1)];
$pantallax = explode('.', $pantallax);
$pantalla[0] = $pantallax[0];

$msg = '';
$clase = 'alert alert-success alert-dismissible';
$display = '';

// Obteniendo el id
$rs_id_rs_mantenimiento = -1;
if (isset($_SESSION['hash_privado'])) {
   $row_user = get_usuario_by_hash($db, strtoupper($_SESSION['hash_privado']));
   $rs_id_rs_mantenimiento = $row_user['id'];
   $_SESSION["user_name"] = $row_user['id'];
   $display = 'style="display: none;"';
} else {
   if (isset($_GET['id'])) {
      $rs_id_rs_mantenimiento =  $_GET['id'];
   } else {
      if (isset($_POST['id'])) {
         $rs_id_rs_mantenimiento =  $_POST['id'];
      } // else {
      //	if (isset($_SESSION["usuario"])) {
      //		$rs_id_rs_mantenimiento =  $_SESSION["usuario"];
      //	}
      //}
   }
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "frm_usuarios")) {
   $es_jefe = 'N';
   // Si esta marcada la casilla de si es jefe			
   if (isset($_POST['chk_jefe'])) {
      $es_jefe = 'S';
   }
   // Si se esta entrando a la actualización de usuarios por medio de link de creacion
   // establesza por omision el estado = A = Activo
   if (isset($_SESSION['hash_privado'])) {
      $_POST['dwd_estado'] = 'A';
   }

   //Armando query para actaulizar usuario
   $updateSQL = "UPDATE [IHTT_USUARIOS].[dbo].[TB_Usuarios] 
   SET [Usuario_Password]=:
      ,[Estado_Usuario]=:
      ,[ID_Empleado]=:
      ,[SistemaFecha]=:
      ,[SistemaUsuario]=:
      ,[ID_User]=:
      WHERE [ID_User]=:id
      ";

   // "UPDATE usuarios 
   // 				SET
   // 				id_trato=:id_trato,
   // 				id_estilo_usuario=:id_estilo_usuario,
   // 				id_prefijo_telefonico=:id_prefijo_telefonico,
   // 				numero_telefono=:numero_telefono,	
   // 				foto=:foto,
   // 				extencion_foto=:extencion_foto, 
   // 				fecha_modificacion=now(),
   // 				nombre=:nombre,
   // 				apellidos=:apellidos,
   // 				correo_electronico=:correo_electronico,
   // 				id_tipo_usuario=:id_tipo_usuario,
   // 				estado=:estado,
   // 				usuario_modificacion=:usuario_modificacion,
   // 				ip_modificacion=:ip_modificacion,
   // 				host_modificacion=:host_modificacion
   // 				WHERE id=:id";
   //Comensando la transacci�n
   $db->beginTransaction();
   //Preparando ejecuci�n de sentencia SQL
   $stmt = $db->prepare($updateSQL);
   //Ejecutanto sentencia SQL
   $res = $stmt->execute(array(
      'Usuario_Nombre' => strtoupper($_POST['txt_nombre']),
      'Estado_Usuario' => $_POST['dwd_estado'],
      'ID_Empleado' => $_POST[''],
      'SistemaFecha' => $_POST[''],
      'SistemaUsuario' => $_POST[''],
      'ID_User' => $_POST[''],

      // ':id_trato' => $_POST['dwd_trato'],
      // ':id_estilo_usuario' => $_POST['dwd_estilo'],
      // ':id_prefijo_telefonico' => $_POST['dwd_pais'],
      // ':numero_telefono' => $_POST['txt_telefono'],
      // ':foto' => $_POST['id_foto'],
      // ':extencion_foto' => $_POST['id_foto_extencion'],
      // ':nombre' => strtoupper($_POST['txt_nombre']),
      // ':apellidos' => strtoupper($_POST['txt_apellidos']),
      // ':correo_electronico' => strtoupper($_POST['txt_correo']),
      // ':id_tipo_usuario' => $_POST['dwd_tipo'],
      // ':estado' => $_POST['dwd_estado'],
      // ':usuario_modificacion' => $_SESSION["usuario"],
      // ':ip_modificacion' => $ip,
      // ':host_modificacion' => $host,
      // ':id' => $rs_id_rs_mantenimiento
   ));
   $res = $stmt->errorInfo();
   //echo $_POST['color_bg_navbar'];
   //print_r($res);exit();
   // Validando que el error de la ejecución
   if (isset($res) and $res[2] <> '') {
      $msg = "Error intentando actualizar registro, error -> " . $res[2] . geterror($stmt->errorInfo());
      $rs_id_rs_mantenimientox = $rs_id_rs_mantenimiento;
      $rs_id_rs_mantenimiento = -1;
      $clase = 'alert alert-danger alert-dismissible';
      $stmt->closeCursor();
      $db->rollBack();
   } else {
      $go = 'S';
      $archivo_origen = '../images/usuario/foto/tmp/'  . $_POST['id_foto'] . '.' . $_POST['id_foto_extencion'];
      $archivo_destino = '../images/usuario/foto/'  . $_POST['id_foto'] . '.' . $_POST['id_foto_extencion'];
      if ($_POST["id_change_foto"] == 'S' && file_exists($archivo_origen) == true) {
         rename($archivo_origen, $archivo_destino);
         $stmt->closeCursor();
         $db->commit();
         $file_unlink = '../images/usuario/foto/' . $_POST["id_foto_anterior"];
         if (file_exists($file_unlink) == true) {
            unlink($file_unlink);
         }
      } else {
         if ($_POST["id_change_foto"] == 'S') {
            $msg = "Error intentando insertar registro de usuarios, error -> " . geterror($stmt->errorInfo());
            $clase = 'alert alert-danger alert-dismissible';
            $stmt->closeCursor();
            $db->rollBack();
            $go = 'N';
         } else {
            $stmt->closeCursor();
            $db->commit();
         }
      }
      if ($go == 'S') {
         // Recarga de pantalla con mensaje de procesamiento satisfactorio
         if ((isset($_POST["cmd_salvar_close"]))) {
            if (isset($_SESSION['hash_privado'])) {
               $pagina_html = $appcfg_Dominio . 'inicio.php?msg=<strong>Bien Hecho!</strong><br \>Su usuario a sido activado satisfactoriamente';
            } else {
               $pagina_html = $pantalla[0] . '_lista.php?msg=<strong>Bien Hecho!</strong><br \>El registro ha sido actualizado satisfactoriamente';
            }
            unset($_SESSION['hash_privado']);
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
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frm_usuarios") && isset($_SESSION['hash_privado']) == false) {
   $es_jefe = 'N';
   // Si esta marcada la casilla de si es jefe			
   if (isset($_POST['chk_jefe'])) {
      $es_jefe = 'S';
   }

   $insertSQL = "INSERT INTO usuarios (
	id_trato,
	id_estilo_usuario,
	id_prefijo_telefonico,
	numero_telefono,
	foto,
	extencion_foto,
	fecha_creacion,
	id,
	nombre,
	apellidos,
	clave,
	correo_electronico,
	id_tipo_usuario,
	estado,
	hash,
	usuario_creacion,
	ip_creacion,
	host_creacion) 
	VALUES (:id_trato,
			:id_estilo_usuario,
			:id_prefijo_telefonico,
			:numero_telefono,
			:foto,
			:extencion_foto,
			now(),
			:id,
			:nombre,
			:apellidos,
			:clave,
			:correo_electronico,
			:id_tipo_usuario,
			:estado,
			:hash,
			:usuario_creacion,
			:ip_creacion,
			:host_creacion)";
   //Comensando la transacción
   $db->beginTransaction();
   // Preparando la sentencia
   $stmt = $db->prepare($insertSQL);
   $res = $stmt->execute(array(
      ':id_trato' => $_POST['dwd_trato'],
      ':id_estilo_usuario,' => $_POST['dwd_estilo'],
      ':numero_telefono' => $_POST['txt_telefono'],
      ':foto' => $_POST['id_foto'],
      ':extencion_foto' => $_POST['id_foto_extencion'],
      ':id' => strtoupper($_POST['txt_usuario']),
      ':nombre' => strtoupper($_POST['txt_nombre']),
      ':apellidos' => strtoupper($_POST['txt_apellidos']),
      ':clave' =>  hash('SHA512', $_POST['txt_clave'], false),
      ':correo_electronico' => strtoupper($_POST['txt_correo']),
      ':id_tipo_usuario' => $_POST['dwd_tipo'],
      ':estado' => $_POST['dwd_estado'],
      ':hash' => hash('SHA512', '%&zfg' . $_POST['txt_correo'] . date('m/d/Y h:i:s a', time()), false),
      ':usuario_creacion' => $_SESSION["user_name"],
      ':ip_creacion' => $ip,
      ':host_creacion' => $host
   ));
   $res = $stmt->errorInfo();
   //echo $_POST['color_bg_navbar'];
   print_r($res);
   exit();
   // Validando que el error de la ejecución
   if (isset($res) and $res[2] <> '') {
      $msg = "Error intentando insertar registro de usuarios, error -> " . geterror($stmt->errorInfo());
      $clase = 'alert alert-danger alert-dismissible';
      $stmt->closeCursor();
      $db->rollBack();
   } else {
      // Llamar a funcion que otorga los permisos acorde al tipo de usuario
      if (f_permisos_por_tipo($_POST['txt_usuario'], $_POST['dwd_tipo'], $ip, $host, $_SESSION["user_name"], $db) == true) {
         $archivo = '../images/usuario/foto/tmp/'  . $_POST['id_foto'] . '.' . $_POST['id_foto_extencion'];
         if (file_exists($archivo) == true) {
            rename($archivo, '../images/usuario/foto/' . $_POST['id_foto'] . '.' . $_POST['id_foto_extencion']);
            $stmt->closeCursor();
            $rs_id_rs_mantenimiento = $db->lastInsertId();
            $db->commit();
            // Recarga de pantalla con mensaje de procesamiento satisfactorio
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
         } else {
            $msg = "Error intentando insertar registro de usuarios, error -> " . geterror($stmt->errorInfo());
            $clase = 'alert alert-danger alert-dismissible';
            $stmt->closeCursor();
            $db->rollBack();
         }
      }
   }
}


// trato que se dara a la persona	
$query_rs_trato = "SELECT a.id, descripcion FROM trato a where a.estado = 'A' order by descripcion";
$trato = $db->prepare($query_rs_trato);
$res = $trato->execute();
$row_rs_trato = $trato->fetch();
$totalRows_rs_trato = $trato->rowcount();

// estilos
$query_rs_estilo = "SELECT a.id, descripcion FROM estilo a where a.estado = 'A' order by descripcion";
$estilo = $db->prepare($query_rs_estilo);
$res = $estilo->execute();
$row_rs_estilo = $estilo->fetch();
$totalRows_rs_estilo = $estilo->rowcount();

// Tipos de Usuario
$query_rs_tipo = "SELECT a.id, descripcion FROM tipo_usuario a where a.estado = 'A' order by descripcion";
$tipo = $db->prepare($query_rs_tipo);
$res = $tipo->execute();
$row_rs_tipo = $tipo->fetch();
$totalRows_rs_tipo = $tipo->rowcount();

// area Funcional
$query_rs_area = "SELECT a.id, descripcion FROM areafuncional a where a.estado = 'A' order by descripcion";
$area = $db->prepare($query_rs_area);
$res = $area->execute();
$row_rs_area = $area->fetch();
$totalRows_rs_area = $area->rowcount();

// Prefijo de País
$query_rs_pais = "SELECT a.id, a.descripcion, a.prefijo, a.ISO_3166_1_alfa_2,a.ISO_3166_1_alfa_3 bandera FROM prefijo_telefonico a where a.estado = 'A' order by descripcion";
$pais = $db->prepare($query_rs_pais);
$res = $pais->execute();
$row_rs_pais = $pais->fetch();
$totalRows_rs_pais = $pais->rowcount();

$DISABLED = '';
$rotulo = "AGREGANDO: " . $tablanext;
// Armando el query
$query_rs_usuarios = "SELECT a.*,b.id as id_prefijo,b.ISO_3166_1_alfa_2 as bandera,b.ISO_3166_1_alfa_3,b.prefijo
FROM usuarios a, prefijo_telefonico b WHERE a.id_prefijo_telefonico = b.id and a.id = :id";
// Preparando la sentencia
$usuarios = $db->prepare($query_rs_usuarios);
// Ejecutanto el query
$usuarios->execute(array(':id' => $rs_id_rs_mantenimiento));
// Si hay algun error
$res = $usuarios->errorInfo();
if (isset($res) and $res[2] <> '') {
   $msg = "<strong>Errores Criticos!<strong><br \>Error intentando leer tabla de estado usuarios, error -> " . $res[2] . $res[1] . $res[0];
   echo $msg;
   exit();
   $usuarios->closeCursor();
   $totalRows_rs_usuarios = 0;
   $clase = "alert alert-danger alert-dismissible";
} else {
   // Sino hay error$ carga el 1er registro	;
   $row_rs_usuarios = $usuarios->fetch();
   // Contador de registros de estados
   $totalRows_rs_usuarios = $usuarios->rowcount();
   if ($totalRows_rs_usuarios > 0) {
      $rotulo = "EDITANDO: " . $tablanext;
      $asterisco = '';
      $row_rs_usuarios['bandera'] = $appcfg_ruta_banderas  . $row_rs_usuarios['bandera'] . '.png';
      //echo '<script>alert("' . $row_rs_usuarios['bandera'].    '")</script>';
      $DISABLED = 'disabled';
   } else {
      $asterisco = '*';
      $row_rs_usuarios = array();
      $row_rs_usuarios['id_prefijo_telefonico'] = $appcfg_prefijo_pais;
      $row_rs_usuarios['bandera'] = $appcfg_nombre_bandera_default;
   }
}
if (isset($_GET['msg']) and $_GET['msg'] <> "") {
   $msg = $_GET['msg'];
}

if (isset($rs_id_rs_mantenimientox) == true) {
   $rs_id_rs_mantenimiento = $rs_id_rs_mantenimientox;
   echo 'XXXX' . $rs_id_rs_mantenimientox;
}

if ((isset($_POST["cmd_salvar_copy"]))) {
   $rs_id_rs_mantenimiento = -1;
   $row_rs_usuarios['nombre'] = $row_rs_usuarios['nombre'] . ' Copia' . $copy;
   $row_rs_usuarios['id'] = '';
   $row_rs_usuarios['correo_electronico'] = '';
   $_POST['txt_usuario'] = '';
   $DISABLED = '';
}
?>
<!DOCTYPE html>

<head>
   <title>MANTENIMIENTO <?php echo strtoupper($tablanext); ?></title>
   <?php require_once('../../../encabezado.php');
   if ($totalRows_rs_usuarios  < 1) { ?>
      <style>
         /* Container */
         .container {
            margin: 0 auto;
            border: 0px solid black;
            width: 50%;
            height: 250px;
            border-radius: 3px;
            background-color: ghostwhite;
            text-align: center;
         }

         /* Preview */
         .preview {
            width: 300px;
            height: 300px;
            border: 2px solid black;
            margin: 0 auto;
            background: white;
         }

         .preview {
            display: none;
         }

         .preview img {
            display: none;
         }

         /* Button */
         .button {
            border: 0px;
            background-color: deepskyblue;
            color: white;
            padding: 5px 15px;
            margin-left: 10px;
         }
      </style>
   <?php } ?>
</head>

<body>
   <div class="main pantalla_al_top">
      <?php
      require_once('../../../encabezado_body.php');
      ?>
      <form action="<?php echo $currentPage; ?>" method="post" enctype="multipart/form-data" name="form1" id="form1"
         class="needs-validation" novalidate>
         <?php require_once('../../../rotulo_pantalla.php'); ?>
         <?php
         $botones = 'U';
         require_once('../../../botones.php'); ?>
         <?php
         $detalle = 'INFORMACIÓN DE ' . $tablanext;
         require_once('../../detalle_pantalla.php'); ?>
         <div class="container pantalla">

            <div id="msg-completo">
               <?PHP
               if (isset($msg) and $msg <> "") {
               ?>
                  <div id="msg-global" class="<?PHP echo $clase; ?>" role="alert">
                     <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span
                           class="sr-only">Cerrar</span></button>
                     <div id="msg-error">
                        <?PHP echo $msg; ?>
                     </div>
                  </div>
               <?PHP  } ?>
            </div>



            <div class="mb-6">
               <label id="label-usuario" data-toggle="tooltip" data-placement="top"
                  title="Introduzca el usuario"><strong>Usuario*</strong>
               </label>
               <input required <?php echo $DISABLED; ?> onChange="f_validar_usuario(this.value)" type="text"
                  class="form-control" id="txt_usuario" name="txt_usuario" value="<?php echo $row_rs_usuarios['id']; ?>"
                  maxlength="30">
               <div class="invalid-feedback">
                  No puede quedar en blanco.
               </div>
            </div>

            <div class="mb-6">
               <label id="label-fileToUpload" data-toggle="tooltip" data-placement="top"
                  title="Seleccione fotografia o un avatar para el perfil de usuario"><strong>Fotografía o
                     Avatar</strong></label>
               <div class="custom-file">
                  <input <?php if ($totalRows_rs_usuarios < 1) {
                              echo "required";
                           } ?> type="file" onChange="fCargarArchivo()" class="custom-file-input" name="file" id="file"
                     aria-describedby="inputGroupFileAddon01">
                  <label class="custom-file-label" for="inputGroupFile01">Elija foto</label>
               </div>
               <div class="invalid-feedback">
                  No puede quedar en blanco.
               </div>
            </div>


            <div class="mb-6">
               <div style="text-align:center" class='preview'>
                  <img id="id_foto"
                     src="<?php echo $ruta_foto_usuario . $row_rs_usuarios['foto'] . '.' . $row_rs_usuarios['extencion_foto']; ?>"
                     class="rounded-circle" alt="Foto Usuario" width="300px" height="300px">;
               </div>
            </div>


            <div id="label-trato" class="mb-6">
               <label id="trato-usuario" data-toggle="tooltip" data-placement="top"
                  title="Seleccione el trato que recibira el usuario"><strong>Trato Usuario*</strong>
               </label>
               <select class="custom-select d-block" name="dwd_trato" id="dwd_trato" autofocus required>
                  <?php for ($i = 0; $i < $totalRows_rs_trato; $i++) { ?>
                     <option value="<?php echo $row_rs_trato['id']; ?>" <?php if ($row_rs_trato['id'] == $row_rs_mantenimiento['estado']) {
                                                                           echo "selected=\"selected\"";
                                                                        } ?>><?php echo $row_rs_trato['descripcion']; ?></option>
                  <?php $row_rs_trato = $trato->fetch();
                  } ?>
               </select>
               <div class="invalid-feedback">
                  No puede quedar en blanco.
               </div>
            </div>



            <div id="label-nombre" class="mb-6">
               <label id="nombre-usuario" data-toggle="tooltip" data-placement="top"
                  title="Introduzca el nombre de usuario"><strong>Nombre de Usuario*</strong>
               </label>
               <input required type="text" class="form-control" id="txt_nombre" name="txt_nombre"
                  value="<?php echo $row_rs_usuarios['nombre']; ?>" maxlength="100">
               <div class="invalid-feedback">
                  No puede quedar en blanco.
               </div>
            </div>

            <div id="label-apellidos" class="mb-6">
               <label id="nombre-usuario" data-toggle="tooltip" data-placement="top"
                  title="Introduzca los apellidos del usuario"><strong>Apellidos del Usuario*</strong>
               </label>
               <input required type="text" class="form-control" id="txt_apellidos" name="txt_apellidos"
                  value="<?php echo $row_rs_usuarios['apellidos']; ?>" maxlength="100">
               <div class="invalid-feedback">
                  No puede quedar en blanco.
               </div>
            </div>

            <?PHP if ($totalRows_rs_usuarios == 0) { ?>

               <div <?PHP echo $display; ?> class="mb-6">
                  <label id="label-clave" data-toggle="tooltip" data-placement="top"
                     title="Introduzca la clave del usuario"><strong>Clave&nbsp;
                        <?PHP echo $asterisco; ?>
                     </strong></label>
                  <input required value="<?php echo $row_rs_usuarios['clave']; ?>" class="form-control" name="txt_clave"
                     type="password" id="txt_clave" maxlength="10" />
                  <div class="invalid-feedback">
                     No puede quedar en blanco.
                  </div>
               </div>
            <?PHP } ?>


            <div <?PHP echo $display; ?> class="mb-6">
               <label id="label-correo" data-toggle="tooltip" data-placement="top"
                  title="Introduzca el correo electrónico, recuerde que este sera el que utilizara para realizar el inicio de sesión"><strong>Correo
                     Electrónico*</strong></label>
               <input required onChange="f_validar_correo(this.value)" type="text" class="form-control"
                  name="txt_correo" id="txt_correo" value="<?php echo $row_rs_usuarios['correo_electronico']; ?>"
                  maxlength="120" />
               <div class="invalid-feedback">
                  No puede quedar en blanco.
               </div>
            </div>

            <div <?PHP echo $display; ?> class="mb-6">
               <label id="label-estilo" data-toggle="tooltip" data-placement="top"
                  title="Seleccione un tipo de estilo, este sirve para darle apariencia y colores a la aplicación"><strong>Estilo*</strong></label>

               <select name="dwd_estilo" id="dwd_estilo" class="form-control">
                  <option value="0" <?php if ($row_rs_estilo['id'] == $row_rs_usuarios['id_estilo_usuario']) {
                                       echo "selected=\"selected\"";
                                    } ?>>Seleccione el estilo de usuario</option>
                  <?PHP
                  for ($i = 0; $totalRows_rs_estilo > $i; $i++) { ?>
                     <option value="<?php echo $row_rs_estilo['id']; ?>" <?php if ($row_rs_estilo['id'] == $row_rs_usuarios['id_estilo_usuario']) {echo "selected=\"selected\"";} ?>><?php echo $row_rs_estilo['descripcion']; ?>
                     </option>
                  <?PHP
                     $row_rs_estilo = $estilo->fetch();
                  } ?>
               </select>
               <div class="invalid-feedback">
                  No puede quedar en blanco.
               </div>
            </div>


            <div <?PHP echo $display; ?> class="mb-6">
               <label id="label-tipo" data-toggle="tooltip" data-placement="top"
                  title="Seleccione un tipo de usuario, este campo define el rol del usuario en el sistema"><strong>Tipo*</strong></label>
               <select name="dwd_tipo" id="dwd_tipo" class="form-control">
                  <option value="0" <?php if (0 == $row_rs_usuarios['id_tipo_usuario']) {
                                       echo "selected=\"selected\"";
                                    } ?>>Seleccione el tipo de usuario</option>
                  <?PHP
                  for ($i = 0; $totalRows_rs_tipo > $i; $i++) { ?>
                     <option value="<?php echo $row_rs_tipo['id']; ?>" 
                     <?php if ($row_rs_tipo['id']   == $row_rs_usuarios['id_tipo_usuario']) 
                     {echo "selected=\"selected\"";} ?>><?php echo $row_rs_tipo['descripcion']; ?>
                     </option>
                  <?PHP
                     $row_rs_tipo = $tipo->fetch();
                  } ?>
               </select>
               <div class="invalid-feedback">
                  No puede quedar en blanco.
               </div>
            </div>

            <div class="mb-6">
               <label id="label-prefijo" data-toggle="tooltip" data-placement="top"
                  title="Seleccione el prefijo telefonico del país"><strong>Prefijo Telefónico*</strong></label>
               <select onChange="fChangePrefijo(this)" name="dwd_pais" id="dwd_pais" class="form-control">
                  <option value="0">Seleccione el prefijo de país</option>
                  <?PHP
                  for ($i = 0; $totalRows_rs_pais > $i; $i++) { ?>
                     <option data-isopais="<?php echo $row_rs_pais['ISO_3166_1_alfa_2']; ?>"
                        data-prefijo="(<?php echo $row_rs_pais['prefijo']; ?>)" value="<?php echo $row_rs_pais['id']; ?>" <?php if (!(strcmp($row_rs_pais['id'], $row_rs_usuarios['id_prefijo_telefonico']))) 
                        { echo "selected=\"selected\"";
                                                                                                                           } ?>>
                        <?php echo $row_rs_pais['descripcion'] . '-(' . $row_rs_pais['prefijo'] . ')'; ?></option>
                  <?PHP
                     $row_rs_pais = $pais->fetch();
                  } ?>
               </select>
               <div class="invalid-feedback">
                  No puede quedar en blanco.
               </div>
            </div>

            <div class="mb-6">
               <label id="label-telefono" data-toggle="tooltip" data-placement="top"
                  title="Ingrese el número telefónico">
                  <strong>Número de Teléfono:*</strong></label>
               <div class="input-group-prepend">
                  <span class="input-group-text gy_link_navbar_hover gy_bg_navbar" id="span_bandera"><img
                        id="id_bandera" width="32px" height="22px" src="<?php echo $row_rs_usuarios['bandera']; ?>">
                     (<?php echo $row_rs_usuarios['prefijo']; ?>)</span>
                  <input type="text" class="form-control" id="txt_telefono" name="txt_telefono" required
                     value="<?php echo $row_rs_usuarios['numero_telefono']; ?>" maxlength="80">
                  <div class="invalid-feedback">
                     No puede quedar en blanco.
                  </div>
               </div>
            </div>

            <div <?PHP echo $display; ?> class="mb-6">
               <label data-toggle="tooltip" data-placement="top"
                  title="Seleccione un estado"><strong>Estado*</strong></label>
               <select class="form-control" name="dwd_estado" id="dwd_estado">
                  <?PHP if (!isset($row_rs_usuarios['estado']) || $row_rs_usuarios['estado'] == 'C') { ?>
                     <option value="A" <?php if ("A" == $row_rs_usuarios['estado']) {
                                          echo "selected=\"selected\"";
                                       } ?>>Activo</option>
                     <option value="I" <?php if ("I" == $row_rs_usuarios['estado']) {
                                          echo "selected=\"selected\"";
                                       } ?>>Inactivo</option>
                     <option value="C" <?php if ("C" == $row_rs_usuarios['estado']) {
                                          echo "selected=\"selected\"";
                                       } ?>>Creado</option>
               </select>
            <?PHP } else { ?>
               <?PHP if ($row_rs_usuarios['estado'] == 'A') { ?>
                  <option value="A" <?php if ("A" == $row_rs_usuarios['estado']) {
                                       echo "selected=\"selected\"";
                                    } ?>>Activo</option>
                  <option value="I" <?php if ("I" == $row_rs_usuarios['estado']) {
                                       echo "selected=\"selected\"";
                                    } ?>>Inactivo</option>
                  </select>
               <?PHP } else { ?>
                  <option value="A" <?php if ("A" == $row_rs_usuarios['estado']) {
                                       echo "selected=\"selected\"";
                                    } ?>>Activo</option>
                  <option value="I" <?php if ("I" == $row_rs_usuarios['estado']) {
                                       echo "selected=\"selected\"";
                                    } ?>>Inactivo</option>
                  </select>
            <?PHP }
                  } ?>

            <div class="invalid-feedback">
               No puede quedar en blanco.
            </div>
            </div>

            <div class="mb-6">
               <label data-toggle="tooltip" data-placement="top"
                  title="Código de usuario que creo este registro">Usuario Creación</label>
               <input name="txt_usucre" type="text" class="form-control" id="txt_usucre"
                  value="<?php echo $row_rs_usuarios['usuario_creacion']; ?>" readonly />
               <div class="invalid-feedback">
                  No puede quedar en blanco.
               </div>
            </div>

            <div class="mb-6">
               <label data-toggle="tooltip" data-placement="top" title="Fecha en que fue creado el registro">Fecha de
                  Creaci&oacute;n</label>
               <input name="txt_feccre" type="text" class="form-control" id="txt_feccre"
                  value="<?php echo $row_rs_usuarios['fecha_creacion']; ?>" readonly />
               <div class="invalid-feedback">
                  No puede quedar en blanco.
               </div>
            </div>
            <input type="hidden" name="id_foto_anterior" id="id_foto_anterior"
               value="<?php echo $row_rs_usuarios['foto'] . '.' . $row_rs_usuarios['extencion_foto']; ?>" />

            <input type="hidden" name="id_change_foto" id="id_change_foto" value="N" />
            <input type="hidden" name="id_foto" id="id_foto" value="<?php echo $row_rs_usuarios['foto']; ?>" />
            <input type="hidden" name="id_foto_extencion" id="id_foto_extencion"
               value="<?php echo $row_rs_usuarios['extencion_foto']; ?>" />

            <?php if (isset($rs_id_rs_mantenimiento) && $rs_id_rs_mantenimiento <> -1) { ?>
               <input type="hidden" name="MM_update" id="MM_update" value="frm_usuarios" />
               <input type="hidden" name="id" id="id" value="<?php echo $rs_id_rs_mantenimiento; ?>" />
            <?php } else { ?>
               <input type="hidden" name="MM_insert" id="MM_insert" value="frm_usuarios" />
               <input type="hidden" name="id" id="id" value="-1" />
            <?php }  ?>
         </div>
      </form>
   </div>

   <div class="bottom">
      <footer class="d-flex flex-wrap justify-content-between align-items-center py-1 my-1">
         <?php include_once('footer.php') ?>
      </footer>
   </div>

   <?php
   include_once('pie.php');
   include_once('../sattb/modal_pie.php');
   ?>

   < script src="<?PHP echo $appcfg_Dominio; ?>js/administracion_usuario_lista.js">
      </script>
      <script src="<?PHP echo $appcfg_Dominio; ?>js/expresiones_regulares.js"></script>
      <?php
      include_once('../sattb/assets/js/openModalLogin.js');
      include_once('../sattb/assets/js/login.js');
      ?>


      <?php
      require_once('../../pie.php');
      $usuarios->closeCursor();
      $tipo->closeCursor();
      ?>

      <?PHP require_once('../../timer_logout.php'); ?>
      <script>
         document.getElementById("txt_usuario").focus();
         // Funci�n que valida los campos de la pantalla
         var carpeta_banderas = "<?php echo $appcfg_ruta_banderas; ?>";
         var mobileJsPath = "<?PHP echo $appcfg_mobileJsPath; ?>";
         // variable para almacenar el boton de submit
         var submitButton;
         // vriable de icono por omision de cargando (loading)	
         var appcfg_loading_icon_default = '<?php echo $appcfg_loading_icon_default; ?>';
         // Varaible para contenido html inicial del botom	
         var innerHTML_buttom = '';

         function fChangePrefijo(X) {
            iso_pais = document.getElementById("dwd_pais").options[document.getElementById("dwd_pais").selectedIndex]
               .getAttribute("data-isopais");
            prefijo_pais = document.getElementById("dwd_pais").options[document.getElementById("dwd_pais").selectedIndex]
               .getAttribute("data-prefijo");
            if (typeof(iso_pais) != "undefined") {
               $img = carpeta_banderas + iso_pais.toLowerCase() + '.png';
               $img = '<img id="id_bandera" width="32px" height="22px"' + 'src="' + $img + '">';
               divContenido = document.getElementById('span_bandera');
               divContenido.innerHTML = $img + ' ' + prefijo_pais;
            }
         }

         var carpeta = "<?php echo $ruta_foto_usuario; ?>";

         function fCargarArchivo() {
            var fd = new FormData();
            var files = $('#file')[0].files[0];
            var deleteImg = 'S';

            document.getElementById('id_change_foto').value = 'N';

            fd.append('file', files);
            fd.append('deleteImg', deleteImg);
            fd.append('carpeta', carpeta);

            $.ajax({
               url: '../utils/file_upload.php',
               type: 'post',
               data: fd,
               contentType: false,
               processData: false,
               success: function(response) {
                  respuestas = response.split('(-)')
                  if (typeof(respuestas[0]) != "undefined") {
                     if (respuestas[0] != 0) {
                        document.getElementById('id_foto').value = respuestas[2];
                        document.getElementById('id_foto_extencion').value = respuestas[3];
                        document.getElementById('id_change_foto').value = 'S';
                        $("#id_foto").attr("src", respuestas[1]);
                        $(".preview").show(); // Display image element
                        $(".preview img").show(); // Display image element
                     } else {
                        divContenido = document.getElementById('msg-completo');
                        document.getElementById('file').value = '';
                        divContenido.innerHTML = "";
                        divContenido.innerHTML =
                           '<div id="msg-completo"><div id="msg-global" class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button><div id="msg-error">' +
                           '<strong>Errores! </strong>' + '<br\>' + respuestas[4] + '</div></div></div>'
                     }
                  }
               },
            });
         }
         // Funcion de validar   bsb = boton de submit	
         // bsb = byry submit buttom	
         function f_validar(bsb) {
            $error = false;
            $error_text = "";
            $menssaje = '';
            $id = bsb.id
            submitButton = bsb;
            innerHTML_buttom = submitButton.innerHTML;
            submitButton.innerHTML = appcfg_loading_icon_default;


            if (document.getElementById('txt_usuario').value == '' || validaAlfanumerico(document.getElementById(
                  'txt_usuario').value.trim()) == false) {
               $menssaje = 'Campo Invalido:  <strong>Usuario</strong>' + "<br/>";
               $("#label-usuario").addClass("label-error").removeClass("normal");
               $("#txt_usuario").addClass("text-error").removeClass("normal");
               $error = true;
            } else {
               $("#label-usuario").removeClass("label-error");
               $("#txt_usuario").removeClass("text-error");
            }


            if (document.getElementById('txt_nombre').value == '' || validaSoloTexto(document.getElementById('txt_nombre')
                  .value.trim()) == false) {
               $menssaje = $menssaje + 'Campo Invalido:  <strong>Nombre de Usuario</strong>' + "<br/>";
               $("#label-nombre").addClass("label-error").removeClass("normal");
               $("#txt_nombre").addClass("text-error").removeClass("normal");
               $error = true;
            } else {
               $("#label-nombre").removeClass("label-error");
               $("#txt_nombre").removeClass("text-error");
            }

            if (document.getElementById('txt_apellidos').value == '' || validaSoloTexto(document.getElementById(
                  'txt_apellidos').value.trim()) == false) {
               $menssaje = $menssaje + 'Campo Invalido:  <strong>Apellidos</strong>' + "<br/>";
               $("#label-apellidos").addClass("label-error").removeClass("normal");
               $("#txt_apellidos").addClass("text-error").removeClass("normal");
               $error = true;
            } else {
               $("#label-apellidos").removeClass("label-error");
               $("#txt_apellidos").removeClass("text-error");
            }


            if (document.getElementById('txt_correo').value.trim() == '' || validaCorreoElectronico(document
                  .getElementById('txt_correo').value) == false) {
               $menssaje = $menssaje + 'Campo Invalido:  <strong>Correo Electronico</strong>' + "<br/>";
               $("#label-correo").addClass("label-error");
               $("#txt_correo").addClass("text-error");
               $error = true;
            } else {
               $("#label-correo").removeClass("label-error");
               $("#txt_correo").removeClass("text-error");
            }

            if (document.getElementById('dwd_estilo').value == 0) {
               $menssaje = $menssaje + 'Campo Invalido:  <strong>Seleccione un estilo</strong>' + "<br/>";
               $("#label-estilo").addClass("label-error");
               $("#dwd_estilo").addClass("text-error");
               $error = true;
            } else {
               $("#label-estilo").removeClass("label-error");
               $("#dwd_estilo").removeClass("text-error");
            }

            if (document.getElementById('dwd_tipo').value == 0) {
               $menssaje = $menssaje + 'Campo Invalido:  <strong>Tipo de usuario</strong>' + "<br/>";
               $("#label-tipo").addClass("label-error");
               $("#dwd_tipo").addClass("text-error");
               $error = true;
            } else {
               $("#label-tipo").removeClass("label-error");
               $("#dwd_tipo").removeClass("text-error");
            }

            if (document.getElementById('dwd_pais').value == 0) {
               $menssaje = $menssaje + 'Campo Invalido:  <strong>Seleccione el prefijo telefonico</strong>' + "<br/>";
               $("#label-prefijo").addClass("label-error");
               $("#dwd_pais").addClass("text-error");
               $error = true;
            } else {
               $("#label-prefijo").removeClass("label-error");
               $("#dwd_pais").removeClass("text-error");
            }

            if (document.getElementById('txt_telefono').value == '' || String(document.getElementById('txt_telefono')
                  .value).length < 8) {
               $menssaje = $menssaje +
                  'Campo Invalido:  <strong>Introduzca un número telefonico no menor de 8 caracteres</strong>' + "<br/>";
               $("#label-telefono").addClass("label-error");
               $("#txt_telefono").addClass("text-error");
               $error = true;
            } else {
               $("#label-telefono").removeClass("label-error");
               $("#txt_telefono").removeClass("text-error");
            }

            if (document.getElementById('id').value == "-1" && document.getElementById('file').value.trim() == '') {
               $menssaje = $menssaje + 'Campo Invalido:  <strong>Seleccione una foto para el perfil de usuario</strong>' +
                  "<br/>";
               $("#label-fileToUpload").addClass("label-error");
               $("#file").addClass("text-error");
               $error = true;
            } else {
               $("#label-fileToUpload").removeClass("label-error");
               $("#file").removeClass("text-error");
            }

            if ($error == true) {
               divContenido = document.getElementById('msg-completo');
               divContenido.innerHTML = "";
               //divContenido.innerHTML = '<strong>Errores! </strong>' + '<br\>' + divContenido.innerHTML;
               //$("#msg-global" ).removeClass( "alert alert-success alert-dismissible" ).addClass( "alert alert-danger" );
               divContenido.innerHTML =
                  '<div id="msg-completo"><div id="msg-global" class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button><div id="msg-error">' +
                  '<strong>Errores! </strong>' + '<br\>' + $menssaje + '</div></div></div>'
               submitButton.innerHTML = innerHTML_buttom;
               return false;
            } else {
               return true;
            }
         }

         //Validar que el correo electronico no este ya registrado para otro usuario
         function f_validar_correo(correo) {
            $.ajax({
               type: "POST",
               url: "../pag_ajax/validar_correo.php",
               data: {
                  'correo': correo,
                  'id': document.getElementById('txt_usuario').value
               },
               success: function(result) {
                  result = result.trim();
                  if (result != '') {
                     // Si esta variable es mayor que cero la funcion f_verificar_detalle encontro un talonario dentro de esta misma numeraci�n
                     result =
                        '<div id="msg-global" class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button><div id="msg-error">' +
                        result + '</div></div>';
                     $("#boton_salvar").html('');
                  } else {
                     $("#boton_salvar").html(
                        '<button class="btn navbar-btn btn-success" id="cmd_salvar" name="cmd_salvar" onclick="return f_validar(this.form.dwd_oficina.value,this.form.txt_nombre.value,this.form.txt_clave.value,this.form.txt_correo.value,this.form.dwd_tipo.value,this.form.dwd_estado.value)"><i class="glyphicon glyphicon-edit"></i><span class="hidden-xs">Salvar</span>        </button>'
                     );
                  }
                  // Poniendo el resultado en el mensaje de texto
                  $("#msg-completo").html(result);
               },
               error: function(xhr, ajaxOptions, thrownError) {
                  alert(xhr.status);
                  alert(thrownError);
               }
            });
         }

         //Validar que el correo electronico no este ya registrado para otro usuario
         function f_validar_usuario(iv_codigo) {
            $.ajax({
               type: "POST",
               url: "../pag_ajax/validar_usuario.php",
               data: {
                  'usuario': document.getElementById('txt_usuario').value,
                  'id': document.getElementById('txt_usuario').value
               },
               success: function(result) {
                  result = result.trim();

                  if (result != '') {
                     // Si esta variable es mayor que cero la funcion f_verificar_detalle encontro un talonario dentro de esta misma numeraci�n
                     result =
                        '<div id="msg-global" class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button><div id="msg-error">' +
                        result + '</div></div>';
                     $("#boton_salvar").html('');
                  } else {
                     $("#boton_salvar").html(
                        '<button class="btn navbar-btn btn-success" id="cmd_salvar" name="cmd_salvar" onclick="return f_validar()"><i class="glyphicon glyphicon-edit"></i><span class="hidden-xs">Salvar</span>        </button>'
                     );
                  }
                  // Poniendo el resultado en el mensaje de texto
                  $("#msg-completo").html(result);
               },
               error: function(xhr, ajaxOptions, thrownError) {
                  alert(xhr.status);
                  alert(thrownError);
               }
            });
         }
      </script>
      <?PHP if ($rs_id_rs_mantenimiento < 0 and isset($copy) == false) { ?>
         <script>
            document.getElementById("txt_nombre").value = '';
            document.getElementById("txt_clave").value = '';
         </script>
      <?PHP } ?>

      <!-- Expand and Reduce icons -->
      <script src="<?PHP echo $appcfg_Dominio; ?>js/expandcollapseicon.js"></script>
      <script src="<?PHP echo $appcfg_Dominio; ?>js/expresiones_regulares.js"></script>
</body>

</html>