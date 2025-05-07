
<?php
//* PÁGINA PRINCIPAL DEL SISTEMA
session_start();
//*configuración del sistema
include_once('configuracion/configuracion.php');
//*configuración del las variables globales del sistema
include_once('configuracion/configuracion_js.php');
if (!isset($_SESSION['url']) && !isset($_SESSION['user_name'])) {
    if ($appcfg_Dominio == (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") {
        $appcfg_page_url =  (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" . "ram.php";
    } else {
        $appcfg_page_url =  (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }
   if (!isset($_SESSION['user_name'])) { //tipo
      $appcfg_page_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
      $_SESSION['url'] = $appcfg_page_url;
      $_SESSION['flashmsg'] = "Favor inicie sesión para poder ingresar al sistema";
      header("location:inicio.php");
      exit();
   }
   $hayCoincidencia = true;
} else {
    include_once('validar_roles.php');
    if (!array_intersect(['DIGITADOR_VENTANILLA_RA','OFICIAL_JURIDICO_RA','SUPERVISOR_RA'], $_SESSION["ROLESXUSUARIORAM"])) {
       $appcfg_page_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
       $_SESSION['url'] = $appcfg_page_url;
       $_SESSION['flashmsg'] = "No tiene permisos para acceder a esta pantalla (INGRESO DE RAM'S)";
       header("location:inicio.php");
       exit();
    } else {
        $appcfg_page_url = '';
        $hayCoincidencia = true;
    }
}
//******************************************************************/
//* Es Renovacion Automatica
//******************************************************************/
if (!isset($_SESSION["Es_Renovacion_Automatica"])) {
  $_SESSION["Es_Renovacion_Automatica"] = true;
}

//******************************************************************/
//* Es originado en ventanilla
//******************************************************************/
if (!isset($_SESSION["Originado_En_Ventanilla"])) {
  $_SESSION["Originado_En_Ventanilla"] = true;
}
//*logo y nombre de la aplicacion
$menu = '
<div class="d-flex flex-column flex-md-row align-items-center border-bottom menu">
    <a href="' . $appcfg_Dominio . '" class="d-flex align-items-center link-body-emphasis text-decoration-none">
        <img class="logo" title="'. '" src="' . $appcfg_logo . '" width="50px" height="50px">&nbsp;&nbsp;
        <span class="fs-4"><strong style="color:white;">' . $appcfg_Aplicacion . '</strong></span>
    </a>';

$menu .= '<nav class="d-inline-flex mt-2 mt-md-0 ms-md-auto">';

//* INICIO
// $menu .= '<a class="me-3 py-2 link-body-emphasis text-decoration-none" href="' . $appcfg_Dominio . '"><strong>' . $appcfg_inicio . '</strong></a>';

//* CONSULTAS RAMS 
if (isset($_SESSION['user_name'])) {
    if ($hayCoincidencia == true) { // corregido el if
        $menu .= '<a class="me-3 py-2 link-body-emphasis text-decoration-none" href="' . $appcfg_Dominio . 'src/php/referenciales/infoRamConsulta.php"><strong> <i class="fas fa-folder gobierno1"></i> CONSULTAS</strong></a>';
    } else {
        $menu .= '<a class="me-3 py-2 link-body-emphasis text-decoration-none" href="' . $appcfg_Dominio . 'src/php/referenciales/infoRam.php"><strong><i class="fas fa-folder gobierno1"></i>INGRESO</strong></a>';
    }
}

//* Nombre usuario
$menu .= '<li class="nav-item dropdown" style="list-style: none;">
    <a class="nav-link dropdown-toggle pt-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">';

if (isset($appcfg_user)) {
    $menu .= '<strong>' . strtoupper($appcfg_user) . '</strong>';
} else {
    $menu .= '<strong>USER</strong>';
}

$menu .= '</a>
    <ul class="dropdown-menu">';

if (!isset($_SESSION['user_name'])) {
    $menu .= '<li><a class="dropdown-item" onclick="openModalLogin();"><strong><i class="fa-solid fa-right-to-bracket gobierno1"></i> ' . $appcfg_login . '</strong></a></li>';
} else {
    //* Validar si usuario es (admino supervisor) o su rol está aceptado
    if ($hayCoincidencia == true) {
        $menu .= '<li><a class="dropdown-item" href="' . $appcfg_Dominio . 'src/php/referenciales/asignar_estados.php"><strong> <i class="fas fa-user-plus gobierno1"></i> ASIGNAR ESTADOS</strong></a></li>';
        $menu .= '<li><a class="dropdown-item" href="' . $appcfg_Dominio . 'src/php/referenciales/estadosAsignaRam.php"><strong> <i class="fas fa-share-alt gobierno1"></i> COMPARTIR</strong></a></li>';
        $menu .= '<li><a class="dropdown-item" href="' . $appcfg_Dominio . 'src/php/referenciales/roles.php"><strong> <i class="fas fa-user-shield"></i> ROLES</strong></a></li>';
        $menu .= '<li><a class="dropdown-item" href="' . $appcfg_Dominio . 'src/php/referenciales/roles_x_usuario.php"><strong> <i class="fas fa-user-check"></i> ROLES X USUARIOS</strong></a></li>';
    }
    // else {
    //     //*cuando no esta autorizado el usuario
    //     $menu .= '<li><a class="dropdown-item" href="' . $appcfg_Dominio . 'src/php/referenciales/infoRam.php"><strong><i class="fas fa-folder gobierno1"></i>INGRESO</strong></a></li>';
    // }

    //* SALIR CUANDO EL USUARIO EXISTE
    $menu .= '<li><a class="dropdown-item" href="' . $appcfg_Dominio . 'salir.php"><strong><i class="fa-solid fa-right-from-bracket gobierno1"></i> SALIR</strong></a></li>';
}

//*FIN DE MENU 
$menu .= '</ul>
    </li>
    </nav>
</div>';

$menu .= '<div id="reading_progress_masterbar_id" style="display:none;" class="pb"><div class="pb_p" id="reading_progress_bar_id" style="width: 0%;"></div></div>';

echo $menu;
?>
