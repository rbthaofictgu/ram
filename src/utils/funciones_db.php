<?php header('Content-Type: text/html; charset=utf-8'); ?>

<?PHP
//**********************************************************************************************************************
// Funcion para obtener los detalles del error de operaciones sobre tablas
//**********************************************************************************************************************
if (!function_exists("geterror")) {
	function geterror($therror)
	{
		return $therror[1] . ' ' . $therror[2];
	}
}
?>
<?PHP
//**********************************************************************************************************************
// Funcion para recuperar el limite de tiempo para timeout
//**********************************************************************************************************************
if (!function_exists("f_obtener_timeout")) {
	function f_obtener_timeout($id, $db)
	{
		// Creando sentencia select de busqueda del registro requerido
		$query_rs_datgen = "select limite_timeout from parametros_generales where id = :id";
		$datgen = $db->prepare($query_rs_datgen);
		$datgen->execute(array(':id' => $id));
		$res = $datgen->errorInfo();
		if (isset($res) and $res[2] <> '') { // Si hay error en la ejecución de la sentencia SQL
			$datgen->closeCursor();
			return '';
		} else { // Si no hay error en la ejecución de la sentencia SQL
			$row_rs_datgen = $datgen->fetch();
			return $row_rs_datgen['limite_timeout'];
		}
	}
}
?>
<?PHP
//**********************************************************************************************************************
// Funcion para recuperar la ruta
//**********************************************************************************************************************
if (!function_exists("f_obtener_ruta")) {
	function f_obtener_ruta($id, $db)
	{
		// Creando sentencia select de busqueda del registro requerido
		$query_rs_datgen = "select ruta from parametros_generales
		where id = :id";
		$datgen = $db->prepare($query_rs_datgen);
		$datgen->execute(array(':id' => $id));
		$res = $datgen->errorInfo();
		if (isset($res) and $res[2] <> '') { // Si hay error en la ejecución de la sentencia SQL
			$datgen->closeCursor();
			return '';
		} else { // Si no hay error en la ejecución de la sentencia SQL
			$row_rs_datgen = $datgen->fetch();
			return $row_rs_datgen['ruta'];
		}
	}
}
?>
<?PHP
//**********************************************************************************************************************
// Obtener la cantidad maxima de intentos de inicio de sesion fallidos permitidos 
//**********************************************************************************************************************
if (!function_exists("get_intentosMaximos")) {
	function get_intentosMaximos($indice, $db)
	{
		// Creando sentencia select de busqueda del registro requerido
		$query_rs_stmt = "select maximo_intentos_fallidos_permitidos from parametros_generales
		where id=:id";
		$stmt = $db->prepare($query_rs_stmt);
		$stmt->execute(array(':id' => $indice));
		$res = $stmt->errorInfo();
		if (isset($res) and $res[2] <> '') { // Si hay error en la ejecución de la sentencia SQL
			$stmt->closeCursor();
			return -1;
		} else { // Si no hay error en la ejecución de la sentencia SQL
			$row_rs_stmt = $stmt->fetch();
			if (isset($row_rs_stmt['maximo_intentos_fallidos_permitidos']) == true) {
				$stmt->closeCursor();
				return $row_rs_stmt['maximo_intentos_fallidos_permitidos'];
			} else {
				return 0;
			}
		}
	}
}
?>
<?PHP
//**********************************************************************************************************************
// Funcion para validar acceso de usuario por estado
//**********************************************************************************************************************
if (!function_exists("verificar_usuario")) {
	function verificar_usuario($usuario, $munic, $db)
	{
		// Creando sentencia select de busqueda del registro requerido
		$query_rs_stmt = "select [Usuario_Nombre]   FROM [IHTT_USUARIOS].[dbo].[TB_Usuarios]
		WHERE (correo_electronico=:user_name or [Usuario_Nombre]=:user_name)";
		$stmt = $db->prepare($query_rs_stmt);
		$stmt->execute(array(':user_name' => $usuario));
		$res = $stmt->errorInfo();
		if (isset($res) and $res[2] <> '') { // Si hay error en la ejecución de la sentencia SQL
			$stmt->closeCursor();
			return false;
		} else { // Si no hay error en la ejecución de la sentencia SQL
			$row_rs_stmt = $stmt->fetch();
			if (isset($row_rs_stmt['nombre']) == true) {
				$stmt->closeCursor();
				return true;
			} else {
				return false;
			}
		}
	}
}
?>
<?PHP
//**********************************************************************************************************************
// Funcion para otorgar permisos por tipo de usuario
//**********************************************************************************************************************
if (!function_exists("f_permisos_por_tipo")) {
	function f_permisos_por_tipo($id_usuario, $tipo_usuario, $ip, $host, $usuario, $db)
	{
		// Creando sentencia select de busqueda de los privilegios por tipo de usuario
		$query = "select codigo from [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_privilegio_x_tipo_usuario]
	 where id_tipo_usuario = :id_tipo_usuario";
		$privilegio = $db->prepare($query);
		$privilegio->execute(array(':id_tipo_usuario' => $tipo_usuario));
		$totalRows_rs_privilegio  = $privilegio->rowcount();
		$row_rs_privilegio = $privilegio->fetch();

		for ($i = 0; $i < $totalRows_rs_privilegio; $i++) {
			$query_rs_stmt = "insert into [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_privilegio_x_usuario] (codigo,id_usuario,usuario_creacion,fecha_creacion,ip_creacion,host_creacion) values(:codigo,:id_usuario,:usuario_creacion,now(),:ip,:host)";
			//Comensando la transacción
			$stmt = $db->prepare($query_rs_stmt);
			$stmt->execute(array(':codigo' => $row_rs_privilegio['codigo'], ':id_usuario' => $id_usuario, ':usuario_creacion' => $usuario, ':ip' => $ip, ':host' => $host));
			$res = $privilegio->errorInfo();
			if (isset($res) and $res[2] <> '') {
				return false;
			}
			$row_rs_privilegio = $privilegio->fetch();
		}
		return true;
	}
}
?>
<?PHP
//**********************************************************************************************************************
// Funcion para validar privilegio a acceder a cada opción
//**********************************************************************************************************************
if (!function_exists("f_validar_privilegio")) {
	
	function f_validar_privilegio($id_usuario, $codigo, $db)

	{
		// Creando sentencia select de busqueda de los privilegios por tipo de usuario
		// $query = "select b.tabla,b.icono,b.rotulo from[IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_privilegio_x_usuario] a, 
		// [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Privilegio] b where a.codigo = b.id and 
		// a.id_usuario = :id_usuario and b.codigo = :id_privilegio";
		// $privilegio = $db->prepare($query);
		// $privilegio->execute(Array(':id_usuario' => $id_usuario, ':id_privilegio' => $id_privilegio));
		// $totalRows_rs_privilegio  = $privilegio->rowcount(); //fechall
		// echo $totalRows_rs_privilegio .'mensaje'; die();
		// if ($totalRows_rs_privilegio > 0) {
		// 	return $privilegio->fetchall();
		// } else {
		// 	return false;
		// }
		$query = "SELECT b.tabla, b.icono, b.rotulo
          FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_privilegio_x_usuario] a
          JOIN [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Privilegio] b
          ON a.codigo = b.id
          WHERE a.id_usuario = :id_usuario AND b.codigo = :id_privilegio";

		$privilegio = $db->prepare($query);
		$privilegio->execute([':id_usuario' => $id_usuario, ':id_privilegio' => $codigo]);

		// Fetch all rows
		$rows = $privilegio->fetchAll();

		// Count rows
		$totalRows_rs_privilegio = count($rows);

		if ($totalRows_rs_privilegio > 0) {
			return $rows;
		} else {
			return false;
		}
	}
}
?>
<?PHP
//**********************************************************************************************************************
// Funcion para validar acceso de usuario por estado
//**********************************************************************************************************************
if (!function_exists("get_accesos")) {
	function get_accesos($usuario, $estado, $db)
	{
		// Creando sentencia select de busqueda del registro requerido
		$query_rs_estado = "select count(*) as contador from estado_usuario
where id_usuario == :id_usuario and id_estado = :id_estado";
		$estado = $db->prepare($query_rs_estado);
		$estado->execute(array(':id_usuario' => $usuario, ':id_estado' => $estado));
		$res = $estado->errorInfo();
		if (isset($res) and $res[2] <> '') { // Si hay error en la ejecución de la sentencia SQL
			$estado->closeCursor();
			return false;
		} else { // Si no hay error en la ejecución de la sentencia SQL
			$row_rs_estado = $estado->fetch();
			if (isset($row_rs_estado['contador']) > 0) {
				$estado->closeCursor();
				return true;
			} else {
				return false;
			}
		}
	}
}
?>
<?PHP
//**********************************************************************************************************************
// Funcion obtener mensajes a presentar a los usuarios en pantalla
//**********************************************************************************************************************
if (!function_exists("get_mensaje")) {
	function get_mensaje($id_msg, $db)
	{
		// Creando sentencia select de busqueda del registro requerido
		$query_rs_estado = "SELECT id, descripcion, concat(concat(id,'-'),descripcion) as msg FROM mensaje where id = :id";
		$estado = $db->prepare($query_rs_estado);
		$estado->execute(array(':id' => $id_msg));
		$res = $estado->errorInfo();
		if (isset($res) and $res[2] <> '') { // Si hay error en la ejecución de la sentencia SQL
			$estado->closeCursor();
			return -1;
		} else { // Si no hay error en la ejecución de la sentencia SQL
			$row_rs_estado = $estado->fetch();
			if (isset($row_rs_estado['msg']) == true) {
				$estado->closeCursor();
				return $row_rs_estado['msg'];
			} else {
				return 'Error de lectura de mensaje';
			}
		}
	}
}
?>