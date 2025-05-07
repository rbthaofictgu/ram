<?php
//<div class="d-flex flex-column flex-md-row align-items-center pb-3 mb-4 border-bottom">
$rolesAceptados = ['1', '7', '9'];
$menu =
    '<div class="d-flex flex-column flex-md-row align-items-center border-bottom menu">
    <a href="' . $appcfg_Dominio . '" class="d-flex align-items-center link-body-emphasis text-decoration-none">
        <img class="logo" title="' . $appcfg_title_logo . '" src="' . $appcfg_logo . '" width="50px" height="50px">&nbsp;&nbsp;
        <span class="fs-4"><strong style="color:white;" >' . $appcfg_Aplicacion . '</strong></span>
    </a>';
$menu = $menu . '<nav class="d-inline-flex mt-2 mt-md-0 ms-md-auto">';
$menu = $menu .  '<a class="me-3 py-2 link-body-emphasis text-decoration-none" href="' . $appcfg_Dominio . '"><strong>' . $appcfg_inicio . '</strong></a>';

//*CONULTAS RAMS
if (isset($_SESSION['user_name'])) {
    $menu = $menu . ' <li class="nav-item dropdown" style=" list-style: none"><a class="dropdown-item" href="' . $appcfg_Dominio . 'src/php/referenciales/infoRamConsulta.php"><strong>
            <i class="fas fa-folder gobierno1"></i> CONSULTAS</strong></a></li>';
}
$menu = $menu .  '<li class="nav-item dropdown" style=" list-style: none">
        <a class="nav-link dropdown-toggle pt-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">';
if (isset($appcfg_user)) {
    $menu = $menu . '<strong>' . strtoupper($appcfg_user) . '</strong>';
} else {
    $menu = $menu . '<strong> USER</strong>';
}
$menu = $menu . ' 
        </a>
        <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="' . $appcfg_menu . '"><strong><i class="fa-solid fa-gear gobierno1"></i> ' . $appcfg_menu_satt . '</strong></a></li>';

if (!isset($_SESSION['user_name'])) {
    $menu = $menu . '<li><a class="dropdown-item" onclick="openModalLogin();"><strong><i class="fa-solid fa-right-to-bracket gobierno1"></i> ' . $appcfg_login . '</strong></a></li>';
} else {

    if ($_SESSION['user_name'] == 'ccaballero' || (in_array($_SESSION['roles'], $rolesAceptados))) { //moficiar a roles
        $menu = $menu . '<li><a class="dropdown-item" href="' . $appcfg_Dominio . 'src/php/referenciales/asignar_estados.php"><strong> <i class="fas fa-user-plus gobierno1"></i> ASIGNAR ESTADOS</strong></a></li>';

        $menu = $menu . '<li><a class="dropdown-item" href="' . $appcfg_Dominio . 'src/php/referenciales/estadosAsignaRam.php"><strong> <i class="fas fa-share-alt gobierno1"></i> COMPARTIR </strong></a></li>';

        $menu = $menu . '<li><a class="dropdown-item" href="' . $appcfg_Dominio . 'src/php/referenciales/roles.php"><strong> <i class="fas fa-user-shield"></i> ROLES </strong></a></li>';

        $menu = $menu . '<li><a class="dropdown-item" href="' . $appcfg_Dominio . 'src/php/referenciales/roles_x_usuario.php"><strong> <i class="fas fa-user-check"></i> ROLES X USUARIOS</strong></a></li>';
    } else {
        $menu = $menu . ' <li><a class="dropdown-item" href="' . $appcfg_Dominio . 'src/php/referenciales/infoRam.php"><strong>
            <i class="fas fa-folder gobierno1"></i> RAMS</strong></a></li>';
    }


    $menu = $menu . '<li><a class="dropdown-item" href="' . $appcfg_Dominio . 'salir.php"><strong><i class="fa-solid fa-right-from-bracket gobierno1"></i> SALIR</strong></a></li>';
}

$menu = $menu . '</ul>
    </li>
    </nav>
</div>';

$menu = $menu . '<div id="reading_progress_masterbar_id" style="display:none;" class="pb"><div class="pb_p" id="reading_progress_bar_id" style="width: 0%;"></div></div>';

echo $menu;
