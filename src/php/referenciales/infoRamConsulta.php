<?php
session_start();
//*********************************************************************/
//* Esta variable sirve para que todos los programas ubiquen el codigo
//* include_once('validar_roles.php'); 
//* Ejemplos de los valores que puede llevar nivel
//* ''
//* '../'
//* '../../'
//* '../../../'
//*********************************************************************/
$nivel_validar_roles = '../../../';
$roles_autorizados = ['OFICIAL_JURIDICO_RA','DIGITADOR_VENTANILLA_RA','IMPRESIONES_RA,SUPERVISOR_RA','SUPERVISOR_VENTANILLA_RA','SUPERVISOR_ADMINISTRADOR','SA'];
//*********************************************************************/
//* Validaciones de seguridad
//*********************************************************************/
include_once('../../../validar_seguridad.php');
$esConsulta = true;
include "infoRam.php";
?>

