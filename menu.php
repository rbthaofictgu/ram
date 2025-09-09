<?php
include_once('validar_roles.php');

//*logo y nombre de la aplicacion
$menu = '<div class="d-flex flex-column flex-md-row align-items-center border-bottom menu">
    <a href="' . $appcfg_Dominio . '" class="d-flex align-items-center link-body-emphasis text-decoration-none">
        <img class="logo" title="' . '" src="' . $appcfg_logo . '" width="50px" height="50px">&nbsp;&nbsp;
        <span class="fs-4"><strong style="color:white;">' . $appcfg_Aplicacion . '</strong></span>
    </a>';

$menu .= '<nav class="d-inline-flex mt-2 mt-md-0 ms-md-auto">';

//* Sino existe el elemento de session, inicializarlo
if (!isset($_SESSION["ROLESXUSUARIORAM"])) {
    $_SESSION["ROLESXUSUARIORAM"] = [];
}

//* CONSULTAS RAMS 
if (isset($_SESSION['user_name'])) {

    $roles_autorizados = ['SA', 'SUPERVISOR_VENTANILLA_RA', 'SUPERVISOR_RA'];
    if (estaAutorizado($roles_autorizados, $_SESSION["ROLESXUSUARIORAM"])) {
        $menu .= '<a class="me-3 py-2 link-body-emphasis text-decoration-none" 
        href="' . $appcfg_Dominio . 'src/php/referenciales/estadosAsignaRam.php">
        <strong> <i class="fas fa-share-alt gobierno1"></i> COMPARTIR </strong></a>';
    }
    //*MOSTRAR LA PARTE DE CONSULTA
    $roles_autorizados = ['OFICIAL_JURIDICO_RA', 'DIGITADOR_VENTANILLA_RA', 'IMPRESIONES_RA', 'SUPERVISOR_RA', 'SUPERVISOR_VENTANILLA_RA', 'SUPERVISOR_ADMINISTRADOR', 'SA'];

    if (estaAutorizado($roles_autorizados, $_SESSION["ROLESXUSUARIORAM"])) {
        $menu .= '<a class="me-3 py-2 link-body-emphasis text-decoration-none" 
        href="' . $appcfg_Dominio . 'src/php/referenciales/infoRamConsulta.php">
        <strong> <i class="fas fa-folder gobierno1"></i> CONSULTAS</strong></a>';
    }
    if (estaAutorizado($roles_autorizados, $_SESSION["ROLESXUSUARIORAM"])) {
        //*MOSTRAR LA PARTE DE INGRESO
        $menu .= '<a class="me-3 py-2 link-body-emphasis text-decoration-none"
         href="' . $appcfg_Dominio . 'src/php/referenciales/infoRam.php">
         <strong><i class="fas fa-folder gobierno1"></i>INGRESO</strong></a>';
    }
}

$menu .= '<li class="nav-item dropdow" data-bs-smooth-scroll="true"  style="list-style: none;">';

if (isset($appcfg_user)) {
    if ($appcfg_user == 'LOGIN') {
        //*abir el login si no hay session iniciada.

        $menu .= '<a class="me-3 py-2 link-body-emphasis text-decoration-none" onclick="openModalLogin();"><strong><i class="fa-solid fa-right-to-bracket gobierno1"></i> ' . $appcfg_login . '</strong></a>';
    } else {
        //*encargado de desplegar los datos.
        $menu .= '<a class="nav-link dropdown-toggle pt-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">';
        $menu .= '<strong>' . strtoupper($appcfg_user) . '</strong>';
    }
}

$menu .= '</a><ul class="dropdown-menu ">';
//*login menu para SA
if (!isset($_SESSION['user_name'])) {
    // Si no hay sesión iniciada, mostrar botón de login
    $menu .= '<li><a class="dropdown-item" onclick="openModalLogin();"><strong><i class="fa-solid fa-right-to-bracket gobierno1"></i> ' . $appcfg_login . '</strong></a></li>';
} else {
    //*ASIGNAR ESTADOS
    $roles_autorizados = ['SA', 'SUPERVISOR_VENTANILLA_RA', 'SUPERVISOR_RA'];
    if (estaAutorizado($roles_autorizados, $_SESSION["ROLESXUSUARIORAM"])) {
        $menu .= '<li><a class="dropdown-item" href="' . $appcfg_Dominio .
            'src/php/referenciales/asignar_estados.php"><strong> <i class="fas fa-user-plus gobierno1"></i> ASIGNAR ESTADOS</strong></a></li>';
    }
    //*REASIGNAR RAM
    if (estaAutorizado($roles_autorizados, $_SESSION["ROLESXUSUARIORAM"])) {
        $menu .= '<li><a class="dropdown-item" href="https://satt.transporte.gob.hn:293/Reasignacion_FSL.php" target="_blank"><strong> 
        <i class="fas fa-share gobierno1"></i> REASIGNACIÓN</strong></a></li>';
    }
    //*ROLES
    $roles_autorizados = ['SA'];
    if (estaAutorizado($roles_autorizados, $_SESSION["ROLESXUSUARIORAM"])) {
        $menu .= '<li><a class="dropdown-item" href="' . $appcfg_Dominio . 'src/php/referenciales/roles.php"><strong> <i class="fas fa-user-shield"></i> ROLES</strong></a></li>';
    }
    //*ROLES X USUARIOS
    if (estaAutorizado($roles_autorizados, $_SESSION["ROLESXUSUARIORAM"])) {
        $menu .= '<li><a class="dropdown-item" href="' . $appcfg_Dominio . 'src/php/referenciales/roles_x_usuario.php"><strong> <i class="fas fa-user-check"></i> ROLES X USUARIOS</strong></a></li>';
    }

    //*inadmitidos / cancelados
    if (estaAutorizado($roles_autorizados, $_SESSION["ROLESXUSUARIORAM"])) {
        $menu .= '<li><a class="dropdown-item" href="' . $appcfg_Dominio . 'src/php/referenciales/cancelarInadmitir.php"><strong> <i class="fas fa-wrench"></i> MANTENIMIENTO CANCELACION/ INADMISION</strong></a></li>';
    }
    $roles_autorizados = ['SA', 'SUPERVISOR_RA'];
    if (estaAutorizado($roles_autorizados, $_SESSION["ROLESXUSUARIORAM"])) {
        $menu .= '<li><a class="dropdown-item" href="' . $appcfg_Dominio . 'src/php/referenciales/regenerar.php"><strong><i class="fa-solid fa-rotate-right"></i> REGENERAR</strong></a></li>';
    }
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