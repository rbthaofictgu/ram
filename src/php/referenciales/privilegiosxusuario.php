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
<!-- //* archivo de configuracion donde se encuentrar las variables globales. -->
<?php require_once('../../../configuracion/configuracion.php'); ?>
<!-- //*archivo encargado de recuperar el siguiente id de la tabla anterior o siguiente  -->
<?php require_once('../../utils/anterior_siguiente.php'); ?>
<!-- //* archivo de funciones varias(recuperar url,intentos fallidos,validar accesos, etc.) -->
<?php require_once('../../utils/funciones_db.php'); ?>
<!-- //*archivo de configuracion de lña conexion con la base de datos -->
<?php require_once("../../../../config/conexion.php"); ?>
<!-- //*funcion que permite validar privilegio a acceder a cada opción. -->
<?php require_once('../../utils/create_copy_row.php'); ?>
<?php

//*codigo del privilegio a cotejat en tabla.
$codigo = 'PRIVILEGIOXUSUARIO';
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


//*inicializando variables para el id y nombre user 
$rs_tipo_rs_mantenimiento = "";
$rs_usuario = "";
//*validando que exista id y nombre que se trajo de otra oagina y si 
//*existe se asigna.
if (isset($_GET['id']) and isset($_GET['nombre'])) {
   $rs_tipo_rs_mantenimiento =  $_GET['id'];
   $rs_usuario = $_GET['nombre'];
} else {
   if (isset($_POST['id']) and isset($_POST['nombre'])) {
      $rs_tipo_rs_mantenimiento =  $_POST['id'];
      $rs_usuario = $_POST['nombre'];
   }
}

$rotulo = "Privilegios:  Administrando Privilegio por Usuario";
//********************************************************************** */
//* armando query que trae todos los usuarios del sistema.
//********************************************************************* */
$query_rs_usuario = "SELECT u.ID_User as id, u.ID_Empleado as codigo_empleado, u.Usuario_Nombre as descripcion
FROM [IHTT_USUARIOS].[dbo].[TB_Usuarios] as u ORDER BY descripcion";

// "SELECT id, nombre as descripcion from usuarios order by descripcion";

//*preparando la sentencia
$usuario = $db->prepare($query_rs_usuario);
//* ejecutanto el query
$res = $usuario->execute();
//*validacion de error si existe
if (isset($res) and $res == '') {
   $msg = "Error intentando leer tabla de ubicacion, error -> " . geterror($usuario->errorInfo());
   //*liberando memoria de sql server
   $usuario->closeCursor();
   //*asignando valor en 0;
   $totalRows_rs_usuario = 0;
} else {
   //*recuperando los registros
   $row_rs_usuario = $usuario->fetchall(\PDO::FETCH_NUM); //*ayuda a devolver los datos como arreglo indexado (0,1,2, etc)
   //*numero total de filas afectadas.
   $totalRows_rs_usuario  = $usuario->rowcount();
   //*verificando consulta
   $res = $usuario->execute();
   $row_rs_usuario = $usuario->fetch();
}

//* Si no se ha realizando una eleccion de un tipo de usuario y id por el usuario se utiliza el primer tipo de usuario en la tabla y su id.
if ($rs_tipo_rs_mantenimiento == "" and  $rs_usuario == "") {
   $rs_tipo_rs_mantenimiento = $row_rs_usuario['id'];
   $rs_usuario = $row_rs_usuario['descripcion'];
}
//************************************************** */
//* Armando el query privilegios.
//************************************************** */
$query_rs_privilegio = "SELECT p.icono,p.id, p.descripcion, p.menu_padre 
FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Privilegio] p 
WHERE (SELECT count(*) FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_privilegio_x_usuario] b 
WHERE b.id_usuario = :id_tipo_usuario and b.id = b.codigo) = 0 ORDER BY  p.nivel_menu";
//* Preparando la sentencia
$privilegio = $db->prepare($query_rs_privilegio);
//*ejecutanto el query
$res = $privilegio->execute(array(':id_tipo_usuario' => $rs_tipo_rs_mantenimiento));
//*validacion de error si existe
if (isset($res) and $res == '') {
   $msg = "Error intentando leer tabla de ubicacion, error -> " . geterror($privilegio->errorInfo());
   //*liberando memoria de sql server
   $privilegio->closeCursor();
   $totalRows_rs_privilegio = 0;
} else {
   //*obteniendo los resultados
   $row_rs_privilegio = $privilegio->fetchall(\PDO::FETCH_NUM); //*ayuda a devolver los datos como arreglo indexado (0,1,2, etc)
   //*numero de filas afectadas.
   $totalRows_rs_privilegio  = $privilegio->rowcount();
   //*verificando consulta
   $res =  $privilegio->execute();
   $row_rs_privilegio =  $privilegio->fetch();
}
//******************************************************************** */
//* Armando el query de los privilegios por usuario 
//******************************************************************** */
$query_rs_privilegioxusuario = "SELECT p.icono,p.id, p.menu_padre, p.descripcion 
FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Privilegio] p, 
[IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_privilegio_x_usuario] b 
WHERE p.id = b.codigo and b.id_usuario = :id_usuario ORDER BY p.nivel_menu";

//* Preparando la sentencia
$privilegioxusuario = $db->prepare($query_rs_privilegioxusuario);
//* Ejecutanto el query
$res = $privilegioxusuario->execute(array('id_usuario' => $rs_usuario));
//*validando errores si hay
if (isset($res) and $res == '') {
   $msg = "Error intentando leer tabla de ubicacion, error -> " . geterror($privilegioxusuario->errorInfo());
   //*liberando memoria de sql server
   $privilegioxusuario->closeCursor();
   $totalRows_rs_privilegioxusuario = 0;
} else {
   //*recuperando datos
   $row_rs_privilegioxusuario = $privilegioxusuario->fetchall(\PDO::FETCH_NUM); //*ayuda a devolver los datos como arreglo indexado (0,1,2, etc)
   //*devolviendo numero de filas afectadas.
   $totalRows_rs_privilegioxusuario  = $privilegioxusuario->rowcount();
   //*validando consulta
   $res = $privilegioxusuario->execute();
   $row_rs_privilegioxusuario = $privilegioxusuario->fetch();
}

//* rotulo de la tabla que se esta modificando.
$rotulo = "MANTENIMIENTO:  Editando " . $tablanext;

?>
<!DOCTYPE html>

<head>
   <title>MANTENIMIENTO <?php echo strtoupper($tablanext); ?></title>
   <!-- //*mandando encabezados -->
   <?php require_once('../../../encabezado_body.php'); ?>
</head>

<body>
   <div class="main">
      <!-- //*vista del menu de privilegios -->
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
         <div class="table-responsive pantalla_inicio mb-5">
            <div class="table-responsive">
               <table width="100%" align="center" cellpadding="5" cellspacing="2" class="mb-5">
                  <td width="1%"></td>
                  <td width="98%">
                     <br />
                     <div id="msg-completo">
                        <?PHP
                        if (isset($msg) and $msg <> "") {
                        ?>
                           <div id="msg-global" class="alert alert-success alert-dismissible" role="alert">
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

            <table class="table table-condensed mb-5">
               <tr>
                  <td width="8%">&nbsp;</td>
                  <td align="left" width="20%">
                     <label data-toggle="tooltip" data-placement="top" title="Hooray!"
                        data-original-title="Seleccione el usuario con el cúal desea trabajar sus privilegios">
                        <div id="label-nombre"><strong>USUARIO:</strong></div>
                     </label>
                  </td>
                  <td width="72%"><select name="dwd_tipo" id="dwd_tipo" class="form-control"
                        onchange="f_cargar(this.value,this.options[this.selectedIndex].getAttribute('data'))">
                        <?PHP
                        for ($i = 0; $totalRows_rs_usuario > $i; $i++) {  ?>

                           <option data="<?php echo $row_rs_usuario['descripcion']; ?>"
                              value="<?php echo $row_rs_usuario['id']; ?>"
                              <?php
                              if (!(strcmp($row_rs_usuario['id'], $rs_tipo_rs_mantenimiento))) {
                                 echo "selected=\"selected\"";
                              } ?>>
                              <?php echo $row_rs_usuario['descripcion']; ?></option>
                        <?PHP
                           $row_rs_usuario = $usuario->fetch(); //* actualiza  $row_rs_usuario para la siguiente interaccion del bucle
                        } ?>
                     </select></td>
               </tr>
               <tr>
                  <td>&nbsp;</td>
                  <td><label class="tooltip-test" data-placement="top"
                        data-original-title="Seleccione los privilegios que desea otorgarle al usuario"><strong>PRIVILEGIOS</strong></label>
                  </td>
                  <td><label class="tooltip-test" data-placement="top"
                        data-original-title="Aqui apareceran los privilegios que le han sido asignados al usuario"><strong>PRIVILEGIOS
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
                           for ($i = 0; $totalRows_rs_privilegio > $i; $i++) {
                              if (trim($row_rs_privilegio['descripcion']) == '') {
                                 $row_rs_privilegio['descripcion'] = $row_rs_privilegio['icono'];
                              }            ?>

                              <?PHP if ($row_rs_privilegio['menu_padre'] == '') {   ?>
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
                           for ($i = 0; $totalRows_rs_privilegioxusuario > $i; $i++) {

                              if (trim($row_rs_privilegioxusuario['descripcion']) == '') {
                                 $row_rs_privilegioxusuario['descripcion'] = $row_rs_privilegioxusuario['icono'];
                              }

                           ?>
                              <?PHP if ($row_rs_privilegioxusuario['menu_padre'] == '') { ?>
                                 <option class="text-info" value='<?php echo $row_rs_privilegioxusuario['id']; ?>'>
                                    <?php echo $row_rs_privilegioxusuario['descripcion']; ?></option>
                              <?PHP } else { ?>
                                 <option class="text-success" value='<?php echo $row_rs_privilegioxusuario['id']; ?>'>
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

      <!-- <script src="<?PHP //echo $appcfg_Dominio; 
                        ?>js/administracion_usuario_lista.js"> -->
      </script>
      <!-- <script src="<?PHP //echo $appcfg_Dominio; 
                        ?>js/expresiones_regulares.js"></script> -->
      <!-- <?php
            // include_once('../../../../sattb/assets/js/openModalLogin.js');
            // include_once('../../../../sattb/assets/js/login.js');
            ?> -->

   </div>
   <script language="JavaScript" type="text/javascript" charset="UTF-8">
      // Ubicar el cursos en el campo nombre
      document.getElementById("dwd_tipo").focus();
      //*************************************************************************************************************	
      //*******Función para llamar programa php para salvar los datos de la pantalla de pes
      //*************************************************************************************************************
      function f_salvar(f_id, f_usuario) {
         $.ajax({
            type: "POST",
            url: "privilegiousuario_salvar.php",
            data: {
               'tipo': f_usuario,
               'privilegio': f_id
            },
            success: function(result) {
               if (result != '') {
                  //alert('Error: ' + result)
               }
            }
         });
      }



      //*************************************************************************************************************	
      //*******Función para llamar programa php para salvar los datos de la pantalla de pes
      //*************************************************************************************************************
      function f_borrar(f_id, f_usuario) {
         $.ajax({
            type: "POST",
            url: "privilegiousuario_borrar.php",
            data: {
               'tipo': f_usuario,
               'privilegio': f_id
            },
            success: function(result) {
               if (result == '') {
                  $("#msg-error").html("Privilegios des-otorgados correctamente");
               } else {
                  // alert('Error: ' + result)
               }
            }
         });
      }

      // <
      // !--Agregar el item del select de estados al de estdos por usuario y lo elimina del de estado,
      // llama al programa de agregar estdo_usuarios

      function f_validar_agregar(f_usuario) {
         var selectedArray = new Array();
         var selObj = document.getElementById('lst_privilegio');
         var selObj1 = document.getElementById('lst_privilegioxusuario');
         var i;
         var count = 0;
         for (i = 0; i < selObj.options.length; i++) {
            if (selObj.options[i].selected) {
               var newOpt1 = new Option(selObj.options[i].text, selObj.options[i].value);
               selObj1.options[selObj1.options.length] = newOpt1;
               // rutina de salvado de información
               f_salvar(selObj.options[i].value, f_usuario)
               count++;
               selObj.options[i] = null;
               i--;
            }
         }
         // Sino se selecciono ningun item se envie este mensaje de error
         if (count == 0) {
            $("#msg-completo").html(
               '<div id="msg-global" class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button><div id="msg-error">Favor seleccione un Item a agregar</div>'
            );
         } else {
            document.getElementById('lst_privilegioxusuario').size = selObj1.options.length;
            $("#msg-completo").html(
               '<div id="msg-global" class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button><div id="msg-error">Privilegios otorgados correctamente</div>'
            );
         }
      }
      //-->
      // <
      // !--
      function f_validar_borrar(f_usuario) {
         var selectedArray = new Array();
         var selObj = document.getElementById('lst_privilegioxusuario');
         var selObj1 = document.getElementById('lst_privilegio');
         var i;
         var count = 0;
         for (i = 0; i < selObj.options.length; i++) {
            if (selObj.options[i].selected) {
               var newOpt1 = new Option(selObj.options[i].text, selObj.options[i].value);
               selObj1.options[selObj1.options.length] = newOpt1;
               // rutina de salvado de información
               f_borrar(selObj.options[i].value, f_usuario)
               count++;
               selObj.options[i] = null;
               i--;
            }
         }

         // Sino se selecciono ningun item se envie este mensaje de error		
         if (count == 0) {
            $("#msg-completo").html(
               '<div id="msg-global" class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button><div id="msg-error">Favor seleccione un Item a borrar</div>'
            );
         } else {
            document.getElementById('lst_privilegio').size = selObj1.options.length;
            $("#msg-completo").html(
               '<div id="msg-global" class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button><div id="msg-error">Privilegios des-otorgados satisfactoriamente</div>'
            );
         }
      }
      //-->
      function f_cargar(f_tipo, nombre) {
         console.log('privilegiosxusuario.php?id=' + f_tipo + '&nombre=' + nombre);
         window.location = 'privilegiosxusuario.php?id=' + f_tipo + '&nombre=' + nombre;
      }
   </script>
   <?PHP require_once('../../../pie.php'); ?>

   <?php require_once('modal_pie.php'); ?>
   <?php require_once('modal_encabezado.php'); ?>
   <?PHP require_once('../../../timer_logout.php'); ?>
   <!-- <script src="<?php // echo $appcfg_Dominio; 
                     ?>js/expresiones_regulares.js"></script> -->
</body>

</html>