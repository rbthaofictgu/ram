
<?php
//<div class="d-flex flex-column flex-md-row align-items-center pb-3 mb-4 border-bottom">
$menu = 
'<div class="d-flex flex-column flex-md-row align-items-center border-bottom">
    <a href="'. $appcfg_Dominio . 'src/ramasivas/' .'" class="d-flex align-items-center link-body-emphasis text-decoration-none">
        <img title="' . $appcfg_title_logo . '" src="' . $appcfg_logo .'" width="50" height="42">&nbsp;&nbsp;
        <span class="fs-4"><strong class="gobierno3">'. $appcfg_Aplicacion.'</strong></span>
    </a>';
    $menu = $menu . '<nav class="d-inline-flex mt-2 mt-md-0 ms-md-auto">';
    $menu = $menu .  '<a class="me-3 py-2 link-body-emphasis text-decoration-none" href="'. $appcfg_Dominio_Corto . '"><strong>'.$appcfg_inicio.'</strong></a>';
    $menu = $menu .  '<li class="nav-item dropdown" style=" list-style: none">
        <a class="nav-link dropdown-toggle pt-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">';
        if(isset($appcfg_user)){
            $menu = $menu . '<strong>'.$appcfg_user.'</strong>';
        
        }else{
            $menu = $menu . '<strong> USER</strong>';
        }
    $menu = $menu .' 
        </a>
        <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="'.$appcfg_menu.'"><strong>'.$appcfg_menu_satt.'</strong></a></li>
        <li><a class="dropdown-item" href="'. $appcfg_salir.'"><strong>'.$appcfg_login.'</strong></a></li>
        </ul>
    </li>
    </nav>
</div>';

echo $menu;
?>