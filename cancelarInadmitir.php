
<?php
session_start();
//* archivo de configuracion de las variables globales
require_once('configuracion/configuracion.php');
// require_once('configuracion/configuracion_js.php');
//*archivo de configuracion de la base de datos
require_once('../config/conexion.php');

$query_rs_estado = "SELECT [id]
      ,[descripcion]
      ,[otro_espeficique]
      ,[aplicaCancelacion]
      ,[aplicaInadmicion]
      ,[estaActivo]
      ,[fecha_creacion]
      ,[usuario_creacion]
      ,[ip_creacion]
      ,[host_creacion]
      ,[fecha_modificacion]
      ,[usuario_modificacion]
      ,[ip_modificacion]
      ,[host_modificacion]
  FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Razon_Cancelacion_Inadmision]
";
try {
   //*preparando query
   $dataQuery = $db->prepare($query_rs_estado);
   //* ejecutando query
   $dataQuery->execute();
   //*obteniendo los resultado de la consulta.
   $resultados = $dataQuery->fetchAll(PDO::FETCH_ASSOC);
   $cantidad = count($resultados);

   $data=[
      'cantidad' => $cantidad,
      'datos' => $resultados,
      'success' => true,
      'message' => 'Consulta exitosa'
   ];

   //* enviando arreglo
   echo json_encode($data);
} catch (\Throwable $th) {
   //*capturando error
   echo "Error en la consulta de query_rs_estado :" . $th->getMessage();
   // echo json_encode(['success' => false, 'error' => $th->getMessage()]);
   
}
