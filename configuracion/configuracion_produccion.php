<?php
//************************************************************************/
//*<!-- //? PAGINA DE CONFIGURACION GENERAL DEL SISTEMA. --> 
//************************************************************************/
?>
<?php header('Content-Type: text/html; charset=utf-8'); ?>
<?php
$roles_sistema_menu=['SUPERVISOR_VENTANILLA_RA','SUPER_ADMINISTRADOR','SUPERVISOR_RA','SA'];
//*registrar
$roles_sistema_ingreso=['OFICIAL_JURIDICO_RA','DIGITADOR_VENTANILLA_RA'];
// *************************************************************************************************/
// *Definiendo la zona horaria en que operara el sistema
// ************************************************************************************************/	
date_default_timezone_set('America/Tegucigalpa');
//*************************************************************************************************/
//* Estableciendo el idioma de procesamiento de PHP
//*************************************************************************************************/	
setlocale(LC_ALL, "es_HN");
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
if (!isset($_SESSION['refUrl'])) {
	$appcfg_page_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
} else {
	$appcfg_page_url = $_SESSION['refUrl'];
}

if (isset($_SESSION["user_name"])) {
	$usuario = $_SESSION["user_name"];
} else {
	$usuario = 'LOGIN';
}
$appcfg_fecha_inicial_decreto = '2025-10-06 00:00:01';
$appcfg_fecha_final_decreto = '2027-08-31 11:59:59';
$appcfg_dias_vencimiento_modulo19 = 10;
$appcfg_dias_vencimiento_modulo15 = 3;
$appcfg_descripcion_anulacion_aviso_cobro = 'ANULACION AUTOMATICA POR SISTEMA DE RAM POR VENCIMIENTO DEL AVISO DE COBRO';
$appcfg_estadoObjetivo_anulacion = 5; // 3=Anulado
$appcfg_estadoRequerido_anulacion = 1; // 1=Activo
// ***********************************************************************************/
//* Estado Inicial Por Omisión
// ***********************************************************************************/	
$appcfg_estado_inicial = 'IDE-7';
$appcfg_estado_inicial_descripcion = 'EN VENTANILLA';
$tituloConsulta = 'CONSULTA DE RENOVACIONES AUTOMATICAS';
$tituloIngreso = 'INGRESO DE RENOVACIONES AUTOMATICAS';
//*ROLES ADMINISTRATIVOS RA INICIO, CONSULTA.
$todos_los_roles=['OFICIAL_JURIDICO_RA','DIGITADOR_VENTANILLA_RA','IMPRESIONES_RA','SUPERVISOR_RA','SUPERVISOR_VENTANILLA_RA','SUPER_ADMINISTRADOR'];
//* aplica para LAS PAGINAS ASIGIENTES : ASIGNAR ESTADOS, COMPARTIR,ROLES,ROLES X USUARIOS
$roles_sistema_menu=['SUPERVISOR_RA','SUPERVISOR_VENTANILLA_RA'];
// ***********************************************************************************/
//* Version del Sistema
// ***********************************************************************************/	
$appcfg_software_version = 'VERSION 3.0.100 RTBM';
// ***********************************************************************************/
//* fecha de Cambio de Version, utilizada como fecha de referencia de cambio de versión 
//* y como fecha de partida para definir cuando aun se considera una version reciente
// ***********************************************************************************/	
$appcfg_software_version_fecha_inicio = '2025/03/27';
// ***********************************************************************************/
//* Días máximos que sera considerada reciente la nueva version
//***********************************************************************************/	
$appcfg_software_dias_version_reciente = 30;
//***********************************************************************************/
//* Inicio-> version del Sistema
//***********************************************************************************/	
//$appcfg_icono_de_importante = '<i class="fas fa-exclamation-triangle gobierno2"></i>  ';
//$appcfg_icono_de_importante = '<i class="fas fa-star gobierno2"></i>  ';
$appcfg_icono_de_importante = '<i class="fas fa-exclamation-circle text-info"></i>  ';
$appcfg_icono_de_success = '<i class="fas fa-check fa-2x text-success"></i>';
$appcfg_icono_de_error = '<i class="fas fa-exclamation-triangle fa-2x text-error"></i>';
//$appcfg_icono_de_importante = '<i class="far fa-star gobierno2"></i>  ';
//$appcfg_icono_de_importante = '<i class="fas fa-exclamation gobierno2"></i>  ';
//************************************************************************************/
//* Ambiente (Dev=Desarrollo PROD=Producción)
//************************************************************************************/	
//$_SESSION['Environment']  = 'DEV';
$_SESSION['Environment'] = 'PROD';
if ($_SESSION['Environment'] == 'DEV') { //* Ambiente de desarrollo
	$appcfg_Dominio_Raiz = "https://satt2.transporte.gob.hn";
	$appcfg_Dominio = "https://satt2.transporte.gob.hn:285/ram/";
	$appcfg_Dominio_Corto = "https://satt2.transporte.gob.hn:285/";
	$appcfg_Dominio_Puerto = ":285";
	$GLOBALS['appcfg_dominio'] = $appcfg_Dominio;
	$appcfg_menu = "https://satt2.transporte.gob.hn/index.php";
	$appcfg_salir = "https://satt2.transporte.gob.hn/login.php?logout";
} else { //* Ambiente de Producción
	$appcfg_Dominio_Raiz = "https://satt.transporte.gob.hn";
	$appcfg_Dominio = "https://satt.transporte.gob.hn:285/ram/";
	$appcfg_Dominio_Corto = "https://satt.transporte.gob.hn:285/";
	$appcfg_Dominio_Puerto = ":285";
	$GLOBALS['appcfg_dominio'] = $appcfg_Dominio;
	$appcfg_menu = "https://satt.transporte.gob.hn/index.php";
	$appcfg_salir = "https://satt.transporte.gob.hn/login.php?logout";
}
$_SESSION["appcfg_Dominio_Raiz"] = $appcfg_Dominio_Raiz;
$_SESSION["appcfg_Dominio"] = $appcfg_Dominio;
$_SESSION["appcfg_Dominio_Corto"] = $appcfg_Dominio_Corto;
$_SESSION["appcfg_Dominio_Puerto"] = $appcfg_Dominio_Puerto;
//************************************************************************************/
//* Maxima cantidad de periodos anteriores a cobrar
//************************************************************************************/
$appcfg_max_periodos_anterior_cobrar = 2;
//************************************************************************************/
//* Tramites en una sola linea
//************************************************************************************/
$appcfg_tramites_una_linea = true;
//************************************************************************************/
//* Toast
//************************************************************************************/	
$appcfg_milisegundos_toast = 6000;
$appcfg_icono_toast = $appcfg_Dominio . 'assets/images/logos/escudo.jpg';
$appcfg_max_field_on_net = 1000;
//!*********************FOOTER******************************************
$appcfg_Aplicacion = '<span style=color:white">RAM</span>';
$appcfg_nombre_aplicacion = 'RENOVACIONES AUTOMATICAS MASIVAS <span class="gobierno2">(RAM)</span>';
$appcfg_large_name = 'INSTITUTO HONDUREÑO DEL TRANSPORTE TERRESTRE';
$appcfg_short_name = 'IHTT';
$appcfg_telefono = '(+504)-9561-4451';
$appcfg_title_logo = "RENOVACIONES AUTOMATICAS MASIVAS";
$appcfg_logo = $appcfg_Dominio . "assets/images/logos/escudo.jpg";

//!*********************MENU*******************************************
$appcfg_inicio = 'INICIO';
$appcfg_user = $usuario;
$appcfg_menu_satt = 'MENU SATT';
$appcfg_login = 'LOGIN';
$appcfg_menu_item = '<a class="me-3 py-2 link-body-emphasis text-decoration-none" href="@@__link__@@">@@__linktext__@@</a>';
$logo = '';
//****************************/
//* RAM-ROTULO CLASE
//****************************/	
$appcfg_clase = 'badge text-bg-primary';
//!*********************MENU DINAMICO*******************************************
$cfgapp_ruta_completa_foto_usuario = $appcfg_Dominio . 'assets/images/check.png';
$appcfg_width_logo = "50px";
$appcfg_height_logo = "50px";
$appcfg_width_logo_modal = "50px";
$appcfg_height_logo_modal = "50px";
$no_of_records_per_page_base = 100;
//$loading_icon_default = '<i class="fad fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>';
//$loading_icon_default ='<i class="fad fa-circle-notch fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>';
//!!!$loading_icon_default = '<span style="display:inline-flex;align-items:center;gap:6px;"><i class="fa-solid fa-circle-notch fa-spin" style="font-size:1.4em;"></i><span class="gobierno1"><strong>Loading...</strong></span></span>';
$loading_icon_default ='<span style="display:inline-flex;align-items:center;gap:6px;">
    <i class="fa-solid fa-gear fa-spin fa-2x" style="color:#333;"></i>
    <span class="gobierno1" style="font-size:1.1em;">
      <strong>Procesando...</strong>
    </span>
  </span>';
$appcfg_favicon = $appcfg_Dominio . "favico.ico";
$appcfg_background = $appcfg_Dominio . "assets/images/backgrond/tegucigalpa.jpg";
$logo = $appcfg_Dominio . "assets/images/logos/logotop300x40.jpg"; //"images/logos/logo_b_vb.png";
$appcfg_logo_pie = $appcfg_Dominio . "assets/images/logos/logocuadrado.jpg";
$logo_letras = $appcfg_Dominio . "assets/images/logo_url.jpg";
$Ruta_favicon = $appcfg_Dominio . "assets/images/favicon/favicon.ico";

// Máximo tiempo antes de hacer logout automaticamente
$appcfg_max_time_logout = 90000000;
// Tiempo de espera antes de esconder logo de recaptCha
$appcfg_delay_recaptCha = 10000;
// Tiempo de omision en que permanecera la pantalla de loading
$appcfg_delay_loading = 1500;
// Tamaño maximo de imagenes a subir
$max_foto_size = 5000000;
// Tamaño maximo de archivos a subir
$max_file_size = 50000000;
// Maximo tiempo antes de someter
$max_time_submit = 2000;
// Id Estilo por Omisión
$appcfg_estilo_default = 8;

if (isset($body_class) and $body_class != "desktop") {
	$logo = $appcfg_Dominio . "assets/images/logos/logomenu.jpeg"; //"images/logos/logo_b_vb.png";
	//$logo = $Dominio . "images/logos/logotop250x40.jpg";//"images/logos/logo_b_vb.png";
	$appcfg_width_logo = "50px";
	$appcfg_height_logo = "50px";
	$appcfg_width_logo_modal = "50px";
	$appcfg_height_logo_modal = "50px";
	$style_colonia = '';
} else {
	$style_colonia = 'style="width: 40%; min-height: auto; height: auto;"';
}
// if (!isset($_SESSION['tipo'])) {
if (!isset($_SESSION['user_name'])) {
	$appcfg_linea_navbar = 1;
} else {
	$appcfg_linea_navbar = 2;
}
$id_logo = 'logo_animation';
if (isset($inicio)) {
	$id_logo = 'logo_animation';
}
$appcfg_menutype = 'clasic';
$appcfg_width_logo = "25%";
$appcfg_height_logo = "25%";
$linea_navabar = '<nav class="navbar navbar-expand-lg bg-body-tertiary">
<div class="container-fluid">
<a class="navbar-brand" href="' . $appcfg_Dominio . 'index.php"><img src="' . $logo . '" width="' . $appcfg_width_logo . '" height="' . $appcfg_height_logo . '" class="align-top" alt=""></a>
<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
	<span class="navbar-toggler-icon"></span>
</button>
<div class="collapse navbar-collapse" id="navbarSupportedContent">';

$lineaPadreConHijo = '<li class="nav-item dropdown">
		<a class="nav-item nav-link dropdown-toggle mr-md-2" @@target@@ href="@@pantalla@@" id="bd-versions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@@rotulo@@</a>
		<div class="dropdown-menu dropdown-menu-md-left" aria-labelledby="bd-versions">
			@@hijos@@
		</div>
		</li>
';

$lineaPadreConHijo = '<li class="nav-item dropdown">
		<a class="nav-link dropdown-toggle" @@target@@ href="@@pantalla@@" id="bd-versions" role="button" data-bs-toggle="dropdown" aria-expanded="false">@@rotulo@@</a>		  
		<ul class="dropdown-menu">
			@@hijos@@
		</ul>
	</li>';

$linea_PadreConHijoModal = '<li class="nav-item dropdown">
		<a class="nav-link dropdown-toggle" onclick="@@pantalla@@" id="bd-versions" role="button" data-bs-toggle="dropdown" aria-expanded="false">@@rotulo@@</a>		  
		<ul class="dropdown-menu">
			@@hijos@@
		</ul>
	</li>';

$linea_PadreSinHijoModal  = '<li class="nav-item dropdown">
		<a class="nav-link dropdown-toggle" onclick="@@pantalla@@" id="bd-versions" role="button" data-bs-toggle="dropdown" aria-expanded="false">@@rotulo@@</a>		  
	</li>';
$lineaPadreSinHijo = '<li class="nav-item dropdown">
		<a class="nav-link dropdown-toggle" @@target@@ href="@@pantalla@@" id="bd-versions" role="button" data-bs-toggle="dropdown" aria-expanded="false">@@rotulo@@</a>		  
	</li>';
$linea_Hijo = '<li><a class="dropdown-item" @@target@@ href="@@pantalla@@">@@rotulo@@</a></li>';
$linea_Modal = '<li><a class="dropdown-item" onclick="@@pantalla@@">@@rotulo@@</a></li>';
$linea_cerrar_div_ul = '';
$linea_abrir_ul = '<ul class="dropdown-menu">';
$linea_cerrar_ul_sola = '</ul>';
$linea_cerrar_div_ul_nav = '</div></div></nav>';
$linea_cerrar_header = '';
$toast = '<div aria-live="polite" aria-atomic="true" style="position: relative; min-height: 100px;">
  <div class="toast" data-animation="true" data-autohide="true" data-delay="1000" style="position: absolute; top: 10px; right: 30px;">
    <div class="toast-header logo_derecha_bg">
      &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
      <strong class="mr-auto"></strong>&nbsp&nbsp&nbsp
      <small class="gy_navbar">@@time@@</small>
      <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="toast-body">@@msg@@</div>
  </div>
</div>';
$appcfg_smtp_port = 465;
$appcfg_smtp_server = "122.8.183.193";
$appcfg_smtp_user = "notificacionessecretariageneral@transporte.gob.hn";
$appcfg_smtp_password = "Ihtt2024";
$appcfg_placas = ['RA', 'TB', 'TC', 'TE', 'TP', 'TT', 'TR', 'EA', 'SJ'];
?>