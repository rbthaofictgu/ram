<?php
session_start();
//* archivo de configuracion de las variables globales
require_once('configuracion/configuracion.php');
//* archivo de configuracion de la base de datos
require_once('../config/conexion.php');

require_once("../logs/logs.php");

//******************************************/
//* estado aviso de cobro 7: sin pago.
//* estado aviso de cobro 2: pagado.
//* estado aviso de cobro 3: anulado.
//*****************************************/

//* Leer el cuerpo de la solicitud
$json_data = file_get_contents('php://input');

//* Decodificar el JSON recibido
$data = json_decode($json_data, true);
//* idPreforma es la ram o fsl
$idpreforma = isset($data['idPreforma']) ? $data['idPreforma'] : null;
$razon = isset($data['razon']) ? $data['razon'] : null;

$q = "UPDATE [IHTT_Webservice].[dbo].[TB_AvisoCobroEnc] 
      SET [AvisoCobroEstado]=3, [FechaPagadoAnulado]=SYSDATETIME(),
         [ObservacionesAnulacion]=LTRIM(CONCAT([ObservacionesAnulacion],:RAZON)), 
         [UsuarioPagoAnulo]=:UC 	
	   WHERE [ID_Solicitud]=:IDP AND ([AvisoCobroEstado] = 7)";

//en estado 1 para que le ing lo revise el corecto es 2.
try {
   $db->beginTransaction();
   $dataQuery = $db->prepare($q);
   $dataQuery->execute(array(":UC" => $_SESSION["user_name"], ":IDP" => $idpreforma, ":RAZON" => $razon));

   $data = array(
      "status" => "success",
      "message" => "Aviso de cobro anulado correctamente."
   );
   $db->commit();
   echo json_encode($data);
} catch (\Throwable $th) { //Exception $e
   if ($db->inTransaction()) {
      $db->rollBack();
   }
   $data = array(
      "status" => "error",
      "message" => "No se pudo anular el aviso de cobro."
   );
   echo json_encode($data);
}