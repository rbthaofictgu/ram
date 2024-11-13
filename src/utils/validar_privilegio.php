<?php
$datos_privilegio = f_validar_privilegio($_SESSION['user_name'],$codigo,$db);

if (is_array($datos_privilegio) <> 1) {	
	if (!isset($_SESSION['hash_privado'])) {//$_SESSION['hash_privado']
		$_SESSION['flashmsg'] = "No tiene privilegio para acceder a la opción solicitada. Privilegio requerido: " . $codigo;
		header("location: ../../../index.php"); 
		exit();
	}		
} 

$tablanext = $datos_privilegio[0]["tabla"];
// echo $tablanext . ' tablanext';
$privilegiorotulo = $datos_privilegio[0]['rotulo'];
$icono_privilegio = $datos_privilegio[0]['icono'];
$currentPage = $_SERVER["PHP_SELF"];
?>