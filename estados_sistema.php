<?php
//* archivo de configuracion de las variables globales
require_once('configuracion/configuracion.php');
//*archivo de configuracion de la base de datos
require_once('../config/conexion.php');
//* creando el query
$query_rs_estado = "SELECT 
       [ID],
       [ID_Estado],
       [DESC_Estado],
       [Sistema_Usuario],
       [Sistema_Fecha],
       [Estado],
       [Orden]
  FROM [IHTT_PREFORMA].[dbo].[TB_Estados]
 WHERE [Estado] = 1
 ORDER BY [Orden]";
try {
   //*preparando query
   $dataQuery = $db->prepare($query_rs_estado);
   //* ejecutando query
   $dataQuery->execute();
   //*obteniendo los resultado de la consulta.
   $resultados = $dataQuery->fetchAll(PDO::FETCH_ASSOC);
   //?nota: array_keys devuleve las llaves de objeto.
   //*obteniendo las llaves
   $titleBtn = array_keys($resultados[0]);
   $todo = [];
   //* recorriendolos resultado de la consulya
   foreach ($resultados as $fila) {
      //*asignando datos
      $datos[] = $fila;
   }
   //*creando un arreglo con todos los datos
   $estados = [
      'titulo' => $titleBtn,
      'datos' => $datos,
   ];
   //* enviando arreglo
   echo json_encode($estados);
} catch (\Throwable $th) {
   //*capturando error
   echo "Error en la consulta de query_rs_estado :" . $th->getMessage();
}

?>