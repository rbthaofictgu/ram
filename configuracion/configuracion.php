<!-- //? PAGINA DE CONFIGURACION GENERAL DEL SISTEMA. -->
<?php header('Content-Type: text/html; charset=utf-8');?>
<?php
// *************************************************************************************************/
// *Definiendo la zona horaria en que operara el sistema
// ************************************************************************************************/	
date_default_timezone_set('America/Tegucigalpa');
//*************************************************************************************************/
//* Estableciendo el idioma de procesamiento de PHP
//*************************************************************************************************/	
setlocale(LC_ALL,"es_HN");
// ************************************************************************************************/
// *Inicio-> Recuperando IP de la conección
//*************************************************************************************************/	
if (!empty($_SERVER['REMOTE_ADDR'])) {
	$ip = $_SERVER['REMOTE_ADDR'];
} elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	$ip = $_SERVER['HTTP_CLIENT_IP'];
} else {
	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}
// ***********************************************************************************/
//* Inicio-> Recuperando el host de la conexion 
// ***********************************************************************************/	
$host = gethostname();
//*url completa
if (!isset($_GET['refUrl'])) {
	$appcfg_page_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";	 
} else {
	$appcfg_page_url = $_GET['refUrl'];	 
}
if(isset($_SESSION["usuario"])){
	$usuario=$_SESSION["usuario"];
}else{
	$usuario='USUARIO';
}
//************************************************************************************/
//* Ambiente (Dev=Desarrollo PROD=Producción)
//************************************************************************************/	
$Environment  = 'DEV';
//$Environment = 'PROD';
if ($Environment == 'DEV') { //* Ambiente de desarrollo
	$appcfg_Dominio_Raiz = "https://satt2.transporte.gob.hn";
	$appcfg_Dominio = "https://satt2.transporte.gob.hn:285/ram/";
	$appcfg_Dominio_Corto = "https://satt2.transporte.gob.hn:285/";
	$appcfg_Dominio_Puerto = ":293";
	$GLOBALS['appcfg_dominio'] = $appcfg_Dominio;
	$appcfg_menu ="https://satt2.transporte.gob.hn/index.php";
	$appcfg_salir = "https://satt2.transporte.gob.hn/login.php?logout";
} else { //* Ambiente de Producción
	$appcfg_Dominio_Raiz = "https://satt.transporte.gob.hn";
	$appcfg_Dominio = "https://satt.transporte.gob.hn:285/ram/";
	$appcfg_Dominio_Corto = "https://satt.transporte.gob.hn";
	$appcfg_Dominio_Puerto = ":293";
	$GLOBALS['appcfg_dominio'] = $appcfg_Dominio;
	$appcfg_menu ="https://satt.transporte.gob.hn/index.php";
	$appcfg_salir = "https://satt.transporte.gob.hn/login.php?logout";
}	
$_SESSION["appcfg_Dominio_Raiz"] = $appcfg_Dominio_Raiz;
$_SESSION["appcfg_Dominio"] = $appcfg_Dominio;
$_SESSION["appcfg_Dominio_Corto"] = $appcfg_Dominio_Corto;
$_SESSION["appcfg_Dominio_Puerto"] = $appcfg_Dominio_Puerto;
//************************************************************************************/
//* Toast
//************************************************************************************/	
$appcfg_milisegundos_toast = 2500;
$appcfg_icono_toast = $appcfg_Dominio.'/assets/images/check.png';
//!*********************FOOTER******************************************
$appcfg_Aplicacion = 'RENOVACIONES AUTOMATICAS MASIVAS <span class="gobierno2">(RAM)</span>'; 
$appcfg_nombre_aplicacion = 'RENOVACIONES AUTOMATICAS MASIVAS <span class="gobierno2">(RAM)</span>';
$appcfg_large_name = 'INSTITUTO HONDUREÑO DEL TRANSPORTE TERRESTRE';
$appcfg_short_name = 'IHTT';
$appcfg_telefono = '(+504)-9561-4451';
$appcfg_title_logo = "RENOVACIONES AUTOMATICAS MASIVAS";
$appcfg_logo = $appcfg_Dominio . "assets/images/logos/escudo.jpg";

//!*********************MENU*******************************************
$appcfg_inicio='INICIO';
$appcfg_user=$usuario;
$appcfg_menu_satt='MENU SATT';
$appcfg_login='LOGIN';
$appcfg_menu_item = '<a class="me-3 py-2 link-body-emphasis text-decoration-none" href="@@__link__@@">@@__linktext__@@</a>';
?>