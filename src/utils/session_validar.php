<?php header('Content-Type: text/html; charset=utf-8'); ?>

<?php
// echo  json_encode($row_rs_usuarios);
if (isset($_SESSION['row_rs_usuarios'])) {
	$row_rs_usuarios = $_SESSION['row_rs_usuarios'];
	$totalRows_rs_usuarios = $_SESSION['$totalRows_rs_usuarios'];
	//  print_r($row_rs_usuarios);
	for ($i = 0; $i < $totalRows_rs_usuarios; $i++) {
		//  print_r($row_rs_usuarios[$i]['usuario']);
		if (!isset($gy_estilos)) {
			$_SESSION["user_name"] = $row_rs_usuarios[$i]['usuario'];
			$_SESSION["nombre"]  = strtoupper($row_rs_usuarios[$i]['usuario']);
			$_SESSION["hash"]  = $row_rs_usuarios[$i]['clave'];
			$_SESSION['descripcion'] = $row_rs_usuarios[$i]['descripcion'];
			$_SESSION['STYLE'] = $row_rs_usuarios[$i]['estilo'];
		}
		$_SESSION['id_estilo'] = $row_rs_usuarios[$i]["id_estilo"];
		$_SESSION["color_bg_body"] = $row_rs_usuarios[$i]["color_bg_body"];
		$_SESSION["color_bg_navbar"] = $row_rs_usuarios[$i]["color_bg_navbar"];
		$_SESSION["color_bg_navbar_dwd"] = $row_rs_usuarios[$i]["color_bg_navbar_dwd"];
		$_SESSION["color_bg_rotulo"] = $row_rs_usuarios[$i]["color_bg_rotulo"];
		$_SESSION["color_bg_linea_botones"] = $row_rs_usuarios[$i]["color_bg_linea_botones"];
		$_SESSION["color_bg_detalle"] = $row_rs_usuarios[$i]["color_bg_detalle"];
		$_SESSION["color_bg_boton_primary"] = $row_rs_usuarios[$i]["color_bg_boton_primary"];
		$_SESSION["color_bg_hover_botom_primary"] = $row_rs_usuarios[$i]["color_bg_hover_botom_primary"];
		$_SESSION["color_bg_boton_secondary"] = $row_rs_usuarios[$i]["color_bg_boton_secondary"];
		$_SESSION["color_bg_hover_botom_secondary"] = $row_rs_usuarios[$i]["color_bg_hover_botom_secondary"];
		$_SESSION["color_texto_body"] = $row_rs_usuarios[$i]["color_texto_body"];
		$_SESSION["color_texto_navbar"] = $row_rs_usuarios[$i]["color_texto_navbar"];
		$_SESSION["color_texto_rotulo"] = $row_rs_usuarios[$i]["color_texto_rotulo"];
		$_SESSION["color_texto_botones"] = $row_rs_usuarios[$i]["color_texto_botones"];
		$_SESSION["color_text_boton_primary"] = $row_rs_usuarios[$i]["color_text_boton_primary"];
		$_SESSION["color_boder_boton_primary"] = $row_rs_usuarios[$i]["color_boder_boton_primary"];
		$_SESSION["color_sombra_boton_primary"] = $row_rs_usuarios[$i]["color_sombra_boton_primary"];
		$_SESSION["color_hover_text_boton_primary"] = $row_rs_usuarios[$i]["color_hover_text_boton_primary"];
		$_SESSION["color_hover_border_boton_primary"] = $row_rs_usuarios[$i]["color_hover_border_boton_primary"];
		$_SESSION["color_hover_sombra_boton_primary"] = $row_rs_usuarios[$i]["color_hover_sombra_boton_primary"];
		$_SESSION["color_text_boton_secondary"] = $row_rs_usuarios[$i]["color_text_boton_secondary"];
		$_SESSION["color_boder_boton_secondary"] = $row_rs_usuarios[$i]["color_boder_boton_secondary"];
		$_SESSION["color_sombra_boton_secondary"] = $row_rs_usuarios[$i]["color_sombra_boton_secondary"];
		$_SESSION["color_hover_text_boton_secondary"] = $row_rs_usuarios[$i]["color_hover_text_boton_secondary"];
		$_SESSION["color_hover_border_boton_secondary"] = $row_rs_usuarios[$i]["color_hover_border_boton_secondary"];
		$_SESSION["color_hover_sombra_boton_secondary"] = $row_rs_usuarios[$i]["color_hover_sombra_boton_secondary"];
		$_SESSION["color_link_visited"] = $row_rs_usuarios[$i]["color_link_visited"];
		$_SESSION["color_link_hover"] = $row_rs_usuarios[$i]["color_link_hover"];
		$_SESSION["color_link_active"] = $row_rs_usuarios[$i]["color_link_active"];
		$_SESSION["color_link"] = $row_rs_usuarios[$i]["color_link"];
		$_SESSION["color_navbar_link"] = $row_rs_usuarios[$i]["color_navbar_link"];
		$_SESSION["color_navbar_link_visited"] = $row_rs_usuarios[$i]["color_navbar_link_visited"];
		$_SESSION["color_navbar_link_hover"] = $row_rs_usuarios[$i]["color_navbar_link_hover"];
		$_SESSION["color_navbar_link_active"] = $row_rs_usuarios[$i]["color_navbar_link_active"];
		$_SESSION["color_navbar_dwd_link"] = $row_rs_usuarios[$i]["color_navbar_dwd_link"];
		$_SESSION["color_navbar_dwd_link_visited"] = $row_rs_usuarios[$i]["color_navbar_dwd_link_visited"];
		$_SESSION["color_navbar_dwd_link_hover"] = $row_rs_usuarios[$i]["color_navbar_dwd_link_hover"];
		$_SESSION["color_navbar_dwd_link_active"] = $row_rs_usuarios[$i]["color_navbar_dwd_link_active"];
		$_SESSION["color_footer_link"] = $row_rs_usuarios[$i]["color_footer_link"];
		$_SESSION["color_footer_link_visited"] = $row_rs_usuarios[$i]["color_footer_link_visited"];
		$_SESSION["color_footer_link_hover"] = $row_rs_usuarios[$i]["color_footer_link_hover"];
		$_SESSION["color_footer_link_active"] = $row_rs_usuarios[$i]["color_footer_link_active"];
		$_SESSION["navbar_font"] = $row_rs_usuarios[$i]["navbar_font"];
		$_SESSION["body_font"] = $row_rs_usuarios[$i]["body_font"];
		$_SESSION["color_texto_detalle"] = $row_rs_usuarios[$i]["color_texto_detalle"];
	}
}
?>