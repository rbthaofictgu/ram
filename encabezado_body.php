<?PHP   
	// ********************************************************************************************************
	// (Inicio) Código para armar menú conforme a los privilegios que tiene cada usuario
	// ********************************************************************************************************	
		$dsuser = false;
		if (!isset($_SESSION['usuario'])) { 
			$_SESSION['usuario'] = 'INVITADO';
			$_SESSION['accion'] = 0;
			$dsuser = true;
		}
		if (isset($_SESSION['usuario'])) { 
			$DominioBK = $Dominio;
			// Creando sentencia select de busqueda de los privilegios por tipo de usuarccio
			$query = "select b.usar_dominio,b.link_target,b.id as id_privilegio,b.usar_dominio,b.pagina,b.pagina_movil,b.modal,b.icono,
				b.descripcion,a.id_privilegio,b.nivel_menu,
				(select ifnull(count(*),0) from privilegio x where x.menu_padre = b.id) as encabezado_con_hijos 
				from privilegio_x_usuario a, privilegio b where b.estado = 'A' and a.id_privilegio = b.id and a.id_usuario = :id_usuario and b.es_encabezado = 'S' order by b.nivel_menu";
			$privilegiomenu = $conn->prepare($query);
			$privilegiomenu->execute(Array(':id_usuario' => $_SESSION['usuario']));
			$totalRows_rs_privilegiomenu  = $privilegiomenu->rowcount();
			$row_rs_privilegiomenu = $privilegiomenu->fetch();
			// Loop de los encabezados de menu
			$html = $linea_navabar; 
			$jj = 0;
			for ($i=0;$totalRows_rs_privilegiomenu>$i;$i++) {
				$jj = $i;
				$padre_html = '';
				if ($row_rs_privilegiomenu['usar_dominio'] == 'N') {
					$Dominio = '';
				}
				
				if ($row_rs_privilegiomenu['pagina_movil'] <> '' and $body_class == "mobile") {
					$row_rs_privilegiomenu['pagina'] = $row_rs_privilegiomenu['pagina_movil'];
				}
				
				if ($row_rs_privilegiomenu['nivel_menu'] <> '999') {
					// Encabezado con hijos
					if ($row_rs_privilegiomenu['encabezado_con_hijos'] > 0) {
						if ($row_rs_privilegiomenu['modal'] == 'N') {
							$str = str_replace('@@_rotulo_@@',$row_rs_privilegiomenu['icono'] .  $row_rs_privilegiomenu['descripcion'],$lineaPadreConHijo);
							if ($row_rs_privilegiomenu['usar_dominio'] == 'S') {
								$str = str_replace('@@_pantalla_@@', $Dominio . $row_rs_privilegiomenu['pagina'],$str);
							} else {
								$str = str_replace('@@_pantalla_@@', $row_rs_privilegiomenu['pagina'],$str);
							}
							$str = str_replace('@@_target_@@',$row_rs_privilegiomenu['link_target'],$str);
							$padre_html = $padre_html . $str;
						} else {
							$str = str_replace('@@_rotulo_@@', $row_rs_privilegiomenu['icono'] . $row_rs_privilegiomenu['descripcion'],$linea_PadreConHijoModal);
							$str = str_replace('@@_pantalla_@@', $row_rs_privilegiomenu['pagina'],$str);
							$padre_html =  $padre_html . $str;
						}		
					} else {
						if ($row_rs_privilegiomenu['modal'] == 'N') {
							$str = str_replace('@@_rotulo_@@', $row_rs_privilegiomenu['icono'] . $row_rs_privilegiomenu['descripcion'],$lineaPadreSinHijo);
							if ($row_rs_privilegiomenu['usar_dominio'] == 'S') {
								$str = str_replace('@@_pantalla_@@',$Dominio . $row_rs_privilegiomenu['pagina'],$str);
							} else {
								$str = str_replace('@@_pantalla_@@', $row_rs_privilegiomenu['pagina'],$str);
							}
							$str = str_replace('@@_target_@@', $row_rs_privilegiomenu['link_target'],$str);
							$padre_html =  $padre_html . $str;
						} else {
							$str = str_replace('@@_rotulo_@@', $row_rs_privilegiomenu['icono'] . $row_rs_privilegiomenu['descripcion'],$linea_PadreSinHijoModal);
							$str = str_replace('@@_pantalla_@@', $row_rs_privilegiomenu['pagina'],$str);
							$padre_html =  $padre_html . $str;
						}		
					}
				} else {
					if (isset($_SESSION['tipo'])) { 
						$img = '<img src="' . $cfgapp_ruta_completa_foto_usuario . $_SESSION['foto'] . '.' . $_SESSION['extencion_foto'] . '" class="rounded-circle" alt="Foto Usuario" width="35px" height="35px">';
						$row_rs_privilegiomenu['descripcion'] = '<span id="id_usuariomenu">' . $img . '<span class="caret"></span></span>';
						$row_rs_privilegiomenu['pagina'] = '';
					} else {
						$row_rs_privilegiomenu['descripcion'] = '<span id="id_usuariomenu"><i class="fa-solid fa-user fa-2x fa-fw"></i><span class="caret"></span></span>';
					}
					
					// Encabezado con hijos
					if ($row_rs_privilegiomenu['encabezado_con_hijos'] > 0) {
						if ($row_rs_privilegiomenu['modal'] == 'N') {
							$str = str_replace('@@_rotulo_@@', $row_rs_privilegiomenu['descripcion'],$lineaPadreConHijo);
							if ($row_rs_privilegiomenu['usar_dominio'] == 'S') {
								$str = str_replace('@@_pantalla_@@',$Dominio . $row_rs_privilegiomenu['pagina']
											   ,$str);
							} else {
								$str = str_replace('@@_pantalla_@@',$row_rs_privilegiomenu['pagina']
											   ,$str);
							}
							$str = str_replace('@@_target_@@', $row_rs_privilegiomenu['link_target']
											   ,$str);
							$padre_html =  $padre_html . $str . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
							/*$file = fopen("test.txt", "w");
							fwrite($file  , $padre_html  . '</br>');
							fclose($file );
							exit();*/
						} else {
							$str = str_replace('@@_rotulo_@@',$row_rs_privilegiomenu['descripcion'],$linea_PadreConHijoModal);
							$str = str_replace('@@_pantalla_@@',$row_rs_privilegiomenu['pagina'],$str);
							$padre_html =  $padre_html . $str . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
							/*
							$file = fopen("test.txt", "w");
							fwrite($file  ,'Pagina ' .  $row_rs_privilegiomenu['pagina'] . ' rotulo ' . $row_rs_privilegiomenu['descripcion'] . '</br>');
							fclose($file );
							*/
							
						}		
					} else {
						if ($row_rs_privilegiomenu['modal'] == 'N') {
							$str = str_replace('@@_rotulo_@@',$row_rs_privilegiomenu['descripcion'],$lineaPadreSinHijo);
							if ($row_rs_privilegiomenu['usar_dominio'] == 'S') {
								$str = str_replace('@@_pantalla_@@', $Dominio . $row_rs_privilegiomenu['pagina'],$str);
							} else {
								$str = str_replace('@@_pantalla_@@', $row_rs_privilegiomenu['pagina'],$str);
							}
							$str = str_replace('@@_target_@@', $row_rs_privilegiomenu['link_target'],$str);
							$padre_html =  $padre_html . $str. '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp';
						} else {
							$str = str_replace('@@_rotulo_@@',$row_rs_privilegiomenu['descripcion'],$linea_PadreSinHijoModal);
							$str = str_replace('@@_pantalla_@@', $row_rs_privilegiomenu['pagina'],$str);
							$padre_html =  $padre_html . $str . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp';
						}		
					}
				}

				if ($appcfg_menutype == 'getboostrap' and $jj == $appcfg_linea_navbar) {
					$padre_html = $linea_abrir_ul . $padre_html;
				} else {
					if ($appcfg_menutype == 'clasic' and $jj == $appcfg_linea_navbar) {
						$padre_html = $linea_abrir_ul . $padre_html;
					}	
				}
				
				// query para obtener el detalle de cada encabezado de menu
				$query = "select b.link_target,b.usar_dominio,b.modal,b.icono,b.descripcion,a.id_privilegio,b.nivel_menu,b.pagina,b.pagina_movil from privilegio_x_usuario a, privilegio b where b.estado = 'A' and a.id_privilegio = b.id and a.id_usuario = :id_usuario and b.menu_padre = :menu_padre and b.es_encabezado = 'N' order by b.nivel_menu";
				$privilegiomenudown = $conn->prepare($query);
				$privilegiomenudown->execute(Array(':id_usuario' => $_SESSION['usuario'],':menu_padre' => $row_rs_privilegiomenu['id_privilegio']));
				$totalRows_rs_privilegiomenudown  = $privilegiomenudown->rowcount();
				$row_rs_privilegiomenudown = $privilegiomenudown->fetch();
				$hijo_html = '';
				// Loop del detalle de cada encabezado de menu
				for ($ii=0;$totalRows_rs_privilegiomenudown>$ii;$ii++) {
					if ($row_rs_privilegiomenudown['usar_dominio'] == 'N') {
						$Dominio = '';
					} else {
						$Dominio = $DominioBK;
					}
					if ($row_rs_privilegiomenudown['pagina_movil'] <> '' and $body_class == "mobile") {
						$row_rs_privilegiomenudown['pagina'] = $row_rs_privilegiomenudown['pagina_movil'];
					}					
					if ($row_rs_privilegiomenudown['modal'] == 'N') {
						$str = str_replace('@@_rotulo_@@',$row_rs_privilegiomenudown['icono'] . $row_rs_privilegiomenudown['descripcion'],$linea_Hijo);
						if ($row_rs_privilegiomenudown['usar_dominio'] == 'S') {
							$str = str_replace('@@_pantalla_@@',$Dominio . $row_rs_privilegiomenudown['pagina'],$str);
						} else {
							$str = str_replace('@@_pantalla_@@',$row_rs_privilegiomenudown['pagina'],$str);
						}
						$str = str_replace('@@_target_@@',$row_rs_privilegiomenudown['link_target'],$str);
						$hijo_html = $hijo_html . $str;
					} else {
						$str = str_replace('@@_rotulo_@@',$row_rs_privilegiomenudown['descripcion'],$linea_Modal);
						$str = str_replace('@@_pantalla_@@',$row_rs_privilegiomenudown['pagina'],$str);
						$hijo_html = $hijo_html . $str;
					}
					// leyendo el siguiente registro de detalle de menu de los encabezados
					$row_rs_privilegiomenudown = $privilegiomenudown->fetch();
				}	// Fin del loop del detalle de cada encabezado del menu
				$html = $html . str_replace('@@_hijos_@@',$hijo_html,$padre_html);;
				// Si es menú getboostrap cierre el div y el ul de l primera parte del menu
				if ($appcfg_menutype == 'getboostrap' and $jj == ($appcfg_linea_navbar-1)) {
					$html = $html . $linea_cerrar_div_ul;
				} else {
					if ($appcfg_menutype == 'clasic' and $jj == ($appcfg_linea_navbar-1)) {
						$html = $html . $linea_cerar_ul_sola;
					}
				}
				// Leyendo el siguiente registro de encabezados
				$Dominio = $DominioBK;
				$row_rs_privilegiomenu = $privilegiomenu->fetch();
			} // Fin del loop del encabezado del menu
			// Cerrando las etiquetas del menu
			if ($appcfg_menutype == 'clasic' and $jj <= ($appcfg_linea_navbar-1)) {
				echo $html . $linea_cerrar_div_ul_nav;
			} else {
				if ($appcfg_menutype == 'clasic' and $jj > ($appcfg_linea_navbar-1)) {
					echo $html . $linea_cerrar_div_ul_nav;
				} else {
					if ($appcfg_menutype == 'getboostrap' and $jj <= ($appcfg_linea_navbar-1)) {
						echo $html . $linea_cerrar_div_ul . $linea_cerrar_header;
					} else {
						if ($appcfg_menutype == 'getboostrap' and $jj > ($appcfg_linea_navbar-1)) {
							echo $html . $linea_cerar_ul_sola . $linea_cerrar_header;
						}
					}
				} 
			}
		}
	// ********************************************************************************************************
	// (Final) Código para armar menú conforme a los privilegios que tiene cada usuario
	// ********************************************************************************************************
if ($dsuser == true) {
	unset($_SESSION['usuario']);
	unset($_SESSION['accion']);
}	
?>
<?php 
	$llamar_recaptcha = 'S';
 	require_once('configuracion_js.php'); 
?>

