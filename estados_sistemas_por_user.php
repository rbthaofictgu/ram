<?php
session_start();
//* archivo de configuracion de las variables globales
require_once('configuracion/configuracion.php');
//*archivo de configuracion de la base de datos
require_once('../config/conexion.php');
if (!isset($_SESSION["ID_Usuario"]) || !isset($_SESSION["user_name"])) {
   echo json_encode(array("error" => 1100, "errorhead" => "INICIO DE SESSIÓN", "errormsg" => 'NO HAY UNA SESSION INICIADA, FAVOR INICIE SESION Y VUELVA A INTENTARLO', "errorreason" => "Usuario no ha iniciado sessión en el sistema"));
} else {
   //* creando el query
   $query_rs_estado = "SELECT 
    e.[ID_Estado]
      ,e.[DESC_Estado]
      ,e.[Sistema_Usuario]
      ,e.[Sistema_Fecha]
      ,e.[Estado]
      ,e.[Orden]
      ,e.[esEditable]
      ,e.[puede_agregar]
      ,e.[esCompartible]
      ,eu.[usuario]
   FROM [IHTT_PREFORMA].[dbo].[TB_Estados] as e
   inner join [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Estados_User] as eu
      ON e.[ID_Estado] = eu.[ID_Estado]
   WHERE e.[Estado] = 1  and  eu.[Estado]=1 and  eu.[usuario]=:usuario
   ORDER BY [Orden]";
   try {
      //*preparando query
      $dataQuery = $db->prepare($query_rs_estado);
      //* ejecutando query
      $dataQuery->execute(array(':usuario' => $_SESSION['user_name']));
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
      echo json_encode(array("error" => 1200, "errorhead" => "CARGANDO BOTONES SEGÚN ESTADOS", "errormsg" => 'ESTAMOS PRESENTANDO INCOVENIENTES PARA CARGAR LA INFORMACIÓN', "errorreason" => $th->getMessage()));
   }
}
