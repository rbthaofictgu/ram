<?php
    //******************************************************************/
    //* Sino ha iniciado sessión, establecer mensaje de error y        */
    //******************************************************************/
    function isNotLogged($appcfg_Dominio) {
        if ($appcfg_Dominio == (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") {
            $appcfg_page_url =  (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" . "ram.php";
        } else {
            $appcfg_page_url =  (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        }
        $appcfg_page_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $_SESSION['url'] = $appcfg_page_url;
        $_SESSION['flashmsg'] = "Favor inicie sesión para poder ingresar al sistema";
    }
    //******************************************************************/
    //*Validar Roles                                                   */
    //******************************************************************/
    function estaAutorizado($subset, $superset) {
        return count(array_intersect($subset, $superset)) > 0? true: false;
    }
?>