<?PHP
require_once("/var/www/rbarrientos/config/conexion.php");
require_once('configuracion/configuracion.php');
// ********************************************************************************************************
// (Inicio) Código para armar menú conforme a los privilegios que tiene cada usuario
// ********************************************************************************************************	
$dsuser = false;
if (!isset($_SESSION['user_name'])) {
	$_SESSION['usuario'] = 'INVITADO';
	$_SESSION['accion'] = 0;
	$dsuser = true;
}
if (isset($_SESSION['user_name'])) {
	$DominioBK = $appcfg_Dominio;
	// Creando sentencia select de busqueda de los privilegios por tipo de usuarccio
	$query = "select b.usar_dominio,b.link_target,b.id as id_privilegio,
	b.usar_dominio,b.pagina,b.pagina_movil,b.modal,b.icono, b.menu_padre,
	b.descripcion,b.codigo,b.nivel_menu,(select isnull(count(*),0) 
	from [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Privilegio] x 
	where x.menu_padre = b.codigo) as encabezado_con_hijos 
	from [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_privilegio_x_usuario] a, 
	[IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Privilegio] b 
	where b.estado = 'A' and a.codigo = b.id and 
	a.id_usuario = :id_usuario and b.es_encabezado = 'S' 
	order by b.nivel_menu";
	$privilegiomenu = $db->prepare($query);
	$privilegiomenu->execute(array(':id_usuario' => $_SESSION['user_name'])); //$_SESSION['user_name']
	$rows = $privilegiomenu->fetchAll();
	// Loop de los encabezados de menu
	$res = $privilegiomenu->errorInfo();
	if (isset($res) and isset($res[3]) and intval(Trim($res[3])) <> 0) {
		print_r($res);
		die();
	}
	$i = 0;
	$jj = 0;

	$html =$linea_navabar;

	foreach ($rows as $row_rs_privilegiomenu) {
		$jj = $i;
		$padre_html = '';
		if ($row_rs_privilegiomenu['usar_dominio'] == 'N') {
			$Dominio = '';
		} else {
			$Dominio = $DominioBK; //
		}

		if ($row_rs_privilegiomenu['pagina_movil'] <> '' and $body_class == "mobile") {
			$row_rs_privilegiomenu['pagina'] = $row_rs_privilegiomenu['pagina_movil'];
		}

		if ($row_rs_privilegiomenu['nivel_menu'] <> '999') {
			// Encabezado con hijos
			if ($row_rs_privilegiomenu['encabezado_con_hijos'] > 0) {
				if ($row_rs_privilegiomenu['modal'] == 'N') {
					$str = str_replace('@@rotulo@@', $row_rs_privilegiomenu['icono'] . $row_rs_privilegiomenu['descripcion'], $lineaPadreConHijo);
					if ($row_rs_privilegiomenu['usar_dominio'] == 'S') {
						$str = str_replace('@@pantalla@@', $Dominio . $row_rs_privilegiomenu['pagina'], $str);
					
					} else {
						$str = str_replace('@@pantalla@@', $row_rs_privilegiomenu['pagina'], $str);
					}
					$str = str_replace('@@target@@', $row_rs_privilegiomenu['link_target'], $str);
					$padre_html = $padre_html . $str;
				} else {
					$str = str_replace('@@rotulo@@', $row_rs_privilegiomenu['icono'] . $row_rs_privilegiomenu['descripcion'], $linea_PadreConHijoModal);
					$str = str_replace('@@pantalla@@', $row_rs_privilegiomenu['pagina'], $str);
					$padre_html =  $padre_html . $str;
				}
			} else {
				if ($row_rs_privilegiomenu['modal'] == 'N') {
					$str = str_replace('@@rotulo@@', $row_rs_privilegiomenu['icono'] . $row_rs_privilegiomenu['descripcion'], $lineaPadreSinHijo);
					if ($row_rs_privilegiomenu['usar_dominio'] == 'S') {
						$str = str_replace('@@pantalla@@', $Dominio . $row_rs_privilegiomenu['pagina'], $str);
					} else {
						$str = str_replace('@@pantalla@@', $row_rs_privilegiomenu['pagina'], $str);
					}
					$str = str_replace('@@target@@', $row_rs_privilegiomenu['link_target'], $str);
					$padre_html =  $padre_html . $str;
				} else {
					$str = str_replace('@@rotulo@@', $row_rs_privilegiomenu['icono'] . $row_rs_privilegiomenu['descripcion'], $linea_PadreSinHijoModal);
					$str = str_replace('@@pantalla@@', $row_rs_privilegiomenu['pagina'], $str);
					$padre_html =  $padre_html . $str;
				}
			}
		} else {
			if (isset($_SESSION['user_name'])) { //
				// $img = '<img src="' . $cfgapp_ruta_completa_foto_usuario .  $_SESSION['user_name'] . '.' . $_SESSION['user_name'] . '" class="rounded-circle" alt="Foto Usuario" width="35px" height="35px">';
				$img = '<img src="' . $cfgapp_ruta_completa_foto_usuario.'" class="rounded-circle" alt="Foto Usuario" width="35px" height="35px">';
				$row_rs_privilegiomenu['descripcion'] = '<span id="id_usuariomenu">' . $img . '<span class="caret"></span></span>';
				$row_rs_privilegiomenu['pagina'] = '';
			} else {
				$row_rs_privilegiomenu['descripcion'] = '<span id="id_usuariomenu"><i class="fa-solid fa-user fa-2x fa-fw"></i><span class="caret"></span></span>';
			}

			// Encabezado con hijos
			if ($row_rs_privilegiomenu['encabezado_con_hijos'] > 0) {
				if ($row_rs_privilegiomenu['modal'] == 'N') {
					$str = str_replace('@@rotulo@@', $row_rs_privilegiomenu['descripcion'], $lineaPadreConHijo);
					if ($row_rs_privilegiomenu['usar_dominio'] == 'S') {
						$str = str_replace(
							'@@pantalla@@',
							$Dominio . $row_rs_privilegiomenu['pagina'],
							$str
						);
					} else {
						$str = str_replace(
							'@@pantalla@@',
							$row_rs_privilegiomenu['pagina'],
							$str
						);
					}
					$str = str_replace(
						'@@target@@',
						$row_rs_privilegiomenu['link_target'],
						$str
					);
					$padre_html =  $padre_html . $str . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
					/*$file = fopen("test.txt", "w");
							fwrite($file  , $padre_html  . '</br>');
							fclose($file );
							exit();*/
				} else {
					$str = str_replace('@@rotulo@@', $row_rs_privilegiomenu['descripcion'], $linea_PadreConHijoModal);
					$str = str_replace('@@pantalla@@', $row_rs_privilegiomenu['pagina'], $str);
					$padre_html =  $padre_html . $str . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
					/*
						$file = fopen("test.txt", "w");
						fwrite($file  ,'Pagina ' .  $row_rs_privilegiomenu['pagina'] . ' rotulo ' . $row_rs_privilegiomenu['descripcion'] . '</br>');
						fclose($file );
					*/
				}
			} else {
				if ($row_rs_privilegiomenu['modal'] == 'N') {
					$str = str_replace('@@rotulo@@', $row_rs_privilegiomenu['descripcion'], $lineaPadreSinHijo);
					if ($row_rs_privilegiomenu['usar_dominio'] == 'S') {
						$str = str_replace('@@pantalla@@', $Dominio . $row_rs_privilegiomenu['pagina'], $str);
					} else {
						$str = str_replace('@@pantalla@@', $row_rs_privilegiomenu['pagina'], $str);
					}
					$str = str_replace('@@target@@', $row_rs_privilegiomenu['link_target'], $str);
					$padre_html =  $padre_html . $str . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp';
				} else {
					$str = str_replace('@@rotulo@@', $row_rs_privilegiomenu['descripcion'], $linea_PadreSinHijoModal);
					$str = str_replace('@@pantalla@@', $row_rs_privilegiomenu['pagina'], $str);
					$padre_html =  $padre_html . $str . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp';
				}
			}
		}

		if ($appcfg_menutype == 'clasic' and $jj == $appcfg_linea_navbar) {
			$padre_html = $linea_abrir_ul . $padre_html;
		}

		// query para obtener el detalle de cada encabezado de menu
		$query = "select b.link_target,b.usar_dominio,b.modal,b.icono,
					b.descripcion,a.codigo,b.nivel_menu,b.pagina,b.pagina_movil 
					from [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_privilegio_x_usuario] a,
					[IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Privilegio] b 
					where b.estado = 'A' and a.codigo = b.id and a.id_usuario = :id_usuario and
					b.menu_padre = :menu_padre and b.es_encabezado = 'N' 
					order by b.nivel_menu";
		$privilegiomenudown = $db->prepare($query);
		$privilegiomenudown->execute(array(':id_usuario' => $_SESSION['user_name'], ':menu_padre' => $row_rs_privilegiomenu['codigo']));
		$rowss = $privilegiomenudown->fetchall();
		$hijo_html = '';
		// Loop del detalle de cada encabezado de menu
		$ii = 0;
		$j = 0;
		// Loop del detalle de cada encabezado de menu
		foreach ($rowss as 
		$row_rs_privilegiomenudown) {	
			if ($row_rs_privilegiomenudown
			['usar_dominio'] == 'N') {
				$Dominio = '';
			} else {
				$Dominio = $DominioBK;
			}
			if ($row_rs_privilegiomenudown['pagina_movil'] <> '' and $body_class == "mobile") {

				$row_rs_privilegiomenudown['pagina'] = $row_rs_privilegiomenudown['pagina_movil'];
			}
			if ($row_rs_privilegiomenudown['modal'] == 'N') {
				$str = str_replace('@@rotulo@@', $row_rs_privilegiomenudown['icono'] . $row_rs_privilegiomenudown['descripcion'], $linea_Hijo);
				if ($row_rs_privilegiomenudown['usar_dominio'] == 'S') {
					$str = str_replace('@@pantalla@@', $Dominio . $row_rs_privilegiomenudown['pagina'], $str);
				} else {
					$str = str_replace('@@pantalla@@', $row_rs_privilegiomenudown['pagina'], $str);
				}
				$str = str_replace('@@target@@', $row_rs_privilegiomenudown['link_target'], $str);
				$hijo_html = $hijo_html . $str;
			} else {
				$str = str_replace('@@rotulo@@', $row_rs_privilegiomenudown['descripcion'], $linea_Modal);
				$str = str_replace('@@pantalla@@', $row_rs_privilegiomenudown['pagina'], $str);
				$hijo_html = $hijo_html . $str;
			}
			$ii++;
			
		}	// Fin del loop del detalle de cada encabezado del menu
		$html = $html . str_replace('@@hijos@@', $hijo_html, $padre_html);
		// Si es menú clasic
		if ($appcfg_menutype == 'clasic' and $jj == ($appcfg_linea_navbar - 1)) {
			$html = $html . $linea_cerrar_ul_sola;
		}
		// Leyendo el siguiente registro de encabezados
		$Dominio = $DominioBK;
	
	} // Fin del loop del encabezado del menu
	// Cerrando las etiquetas del menu
	if ($appcfg_menutype == 'clasic' and $jj <= ($appcfg_linea_navbar - 1)) {
		echo $html . $linea_cerrar_div_ul_nav;
	} else {
		if ($appcfg_menutype == 'clasic' and $jj > ($appcfg_linea_navbar - 1)) {
			echo $html . $linea_cerrar_div_ul_nav;
		}
	}
}
// ********************************************************************************************************
// (Final) Código para armar menú conforme a los privilegios que tiene cada usuario
// ********************************************************************************************************
if ($dsuser == true) {
	unset($_SESSION['user_name']);
	unset($_SESSION['accion']);
}
?>
<?php
$llamar_recaptcha = 'S';
require_once('configuracion/configuracion_js.php');
?>

