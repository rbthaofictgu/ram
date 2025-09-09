<?php
session_start();
require_once('configuracion/configuracion.php');
require_once('../config/conexion.php');
//*archivo de configuracion de la base de datos

// Detectar si es consulta o si se recibió data para insertar/actualizar
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
   $data = json_decode(file_get_contents("php://input"), true);
   // echo json_encode($data);
   //** Validar que los datos necesarios estén presentes
   $descripcion = $data['descripcion'] ?? null;
   // $otro = $data['otro_espeficique'] ?? null;
   $otro = $data['otro_espeficique'] ?? 0;
   $aplicaCancelacion = $data['aplicaCancelacion'] ?? 0;
   $aplicaInadmicion = $data['aplicaInadmicion'] ?? 0;
   $estaActivo = $data['estaActivo'] ?? 1;
   $id = $data['id'] ?? null;

   $usuario = $_SESSION['usuario'] ?? 'desconocido';
   $ip = $_SERVER['REMOTE_ADDR'];
   $host = gethostname();
   $fecha = date('Y-m-d H:i:s');

   try {
      if ($id) {
         //* Actualizar registro existente
         $query = "UPDATE [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Razon_Cancelacion_Inadmision]
            SET descripcion = :descripcion,
               otro_espeficique = :otro,
               aplicaCancelacion = :aplicaCancelacion,
               aplicaInadmicion = :aplicaInadmicion,
               estaActivo = :estaActivo,
               fecha_modificacion =  SYSDATETIME(),
               usuario_modificacion = :usuario,
               ip_modificacion = :ip,
               host_modificacion = :host
            WHERE id = :id";
      } else {
         //* Comprobar si ya existe un registro con la misma descripción y otro_espeficique
         $query_check = "SELECT COUNT(*) FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Razon_Cancelacion_Inadmision]
         WHERE descripcion = :descripcion AND otro_espeficique = :otro";
         $stmt_check = $db->prepare($query_check);
         $stmt_check->bindParam(':descripcion', $descripcion);
         $stmt_check->bindParam(':otro', $otro);
         $stmt_check->execute();
         $existe = $stmt_check->fetchColumn() > 0;
         //* Insertar nuevo registro
         if ($existe == 0) {
            $query = "INSERT INTO [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Razon_Cancelacion_Inadmision]
                  (descripcion, otro_espeficique, aplicaCancelacion, aplicaInadmicion, estaActivo,
                  fecha_creacion, usuario_creacion, ip_creacion, host_creacion)
                  VALUES (:descripcion, :otro, :aplicaCancelacion, :aplicaInadmicion, :estaActivo,
                  SYSDATETIME(), :usuario, :ip, :host)";
         } else {

            echo json_encode(['success' => false, 'message' => 'Ya existe un registro con la misma descripción y otro especificado']);
            exit;
         }
      }
      //* Preparar y ejecutar la consulta
      $stmt = $db->prepare($query);
      //** Vincular los parámetros para evitar inyecciones SQL
      $stmt->bindParam(':descripcion', $descripcion);
      $stmt->bindParam(':otro', $otro);
      $stmt->bindParam(':aplicaCancelacion', $aplicaCancelacion);
      $stmt->bindParam(':aplicaInadmicion', $aplicaInadmicion);
      $stmt->bindParam(':estaActivo', $estaActivo);
      // $stmt->bindParam(':fecha',$fecha);
      $stmt->bindParam(':usuario', $usuario);
      $stmt->bindParam(':ip', $ip);
      $stmt->bindParam(':host', $host);
      //** Si es una actualización, vincular el ID
      $id = $id ? (int)$id : null; //* Asegurarse de que sea un entero o nulo
      if ($id) {
         $stmt->bindParam(':id', $id);
      }
      //* Ejecutar la consulta
      $stmt->execute();
      echo json_encode(['success' => true, 'message' => $id ? 'Actualizado' : 'Insertado']);
   } catch (Throwable $th) {
      echo json_encode(['success' => false, 'error' => $th->getMessage()]);
   }
} else {
   //* GET: consultar todos
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
    FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Razon_Cancelacion_Inadmision]";

   try {
      $dataQuery = $db->prepare($query_rs_estado);
      $dataQuery->execute();
      $resultados = $dataQuery->fetchAll(PDO::FETCH_ASSOC);

      header('Content-Type: application/json');
      echo json_encode([
         'success' => true,
         'datos' => $resultados
      ]);
      exit;

      // echo json_encode($resultados);
   } catch (Throwable $th) {
      echo "Error en la consulta de query_rs_estado: " . $th->getMessage();
   }
}
