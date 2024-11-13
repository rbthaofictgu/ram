
<?php

// require_once('../../../config/conexion.php');
//**********************************************************************************************************************
// Desarrollado Por: Ronald Thaofic Barrientos Mejía
// Para            : www.grupoyotta.hn
// Lugar           : Tegucigalpa, Honduras
// Fecha           : 2020
// Contactar a     : rbthaofic@grupoyotta.hn Teléfono +504 9561-4451
// Sistema         : BYRY BIENES Y RAICES YOTTA MIEMBRO DEL GRUPO YOTTA WWW.BYRY.HN
//**********************************************************************************************************************
//funcion para recuperar el siguiente ID de na tabla
//**********************************************************************************************************************
function recuperar_siguiente($table,$id,$db)
{
		
	// if (strtoupper($table) == 'USUARIOS' or strtoupper($table) == 'PRIVILEGIO') {
	// 	$query = 'select ISNULL(id,-1) as id from ' . $table .  ' where id > "' . $id . '" limit 1'; 	
	// } else {
	// 	$query = "select ISNULL(id,-1) as id from " . $table .  " where id > " . $id . " limit 1"; 	
	// }
	if (strtoupper($table) == 'USUARIOS' || strtoupper($table) == 'PRIVILEGIO') {
		$query = 'SELECT TOP 1 ISNULL(id, -1) AS id 
					FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].' . $table . ' 
					WHERE id > \'' . $id . '\'';
	} else {
		$query = 'SELECT TOP 1 ISNULL(id, -1) AS id 
					FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].' . $table . ' 
					WHERE id > ' . (int)$id; // Asegúrate de usar (int) si $id es un número
	}

	// Preparando la sentencia
	$stmt = $db->prepare($query);
	// Ejecutanto el query
	$res = $stmt->execute();
	$row_rs_mantenimiento = $stmt->fetch();
	$totalRows_rs_stmt  = $stmt->rowcount();
	if ($totalRows_rs_stmt > 0) {
	// Sino hay error carga el 1er registro	
		return $row_rs_mantenimiento['id'];
	} else {
		
		// if (strtoupper($table) == 'USUARIOS' or strtoupper($table) == 'PRIVILEGIO') {
		// 	$query = 'select ISNULL(id,-1) as id from ' . $table .  ' where id > "a" limit 1'; 	
		// } else {
		// 	$query = "select ISNULL(id,-1) as id from " . $table .  " where id > 0 limit 1"; 	
		// }
		if (strtoupper($table) == 'USUARIOS' or strtoupper($table) == 'PRIVILEGIO') {
			$query = 'SELECT ISNULL(id, -1) as id FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].' . $table . ' WHERE id > 0 ORDER BY id ASC';
		} else {
			$query = 'SELECT TOP 1 ISNULL(id, -1) as id FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].' . $table . ' WHERE id > 0 ORDER BY id ASC';
		}

		$stmt = $db->prepare($query);
		$res = $stmt->execute();
		$row_rs_mantenimiento = $stmt->fetch();
		$totalRows_rs_stmt  = $stmt->rowcount();
		if ($totalRows_rs_stmt > 0) {
			return $row_rs_mantenimiento['id'];
		} else {
			return -1;
		}
	}
}
//**********************************************************************************************************************
//funcion para recuperar el siguiente ID de na tabla
//**********************************************************************************************************************
function recuperar_anterior($table,$id,$db)
{
	// if (strtoupper($table) == 'USUARIOS' or strtoupper($table) == 'PRIVILEGIO') {
		
	// 	$query = 'select ISNULL(id,-1) as id from ' . $table .  ' where id < "' . $id . '" order by id desc limit 1'; 	
	// } else {
	// 	$query = "select ISNULL(id,-1) as id from " . $table .  " where id < " . $id . " order by id desc limit 1"; 
	// }
	if (strtoupper($table) == 'USUARIOS' || strtoupper($table) == 'PRIVILEGIO') {
    $query = 'SELECT TOP 1 ISNULL(id, -1) AS id 
              FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].' . $table . ' 
              WHERE id < ' . (int)$id . ' 
              ORDER BY id DESC'; 
} else {
    $query = 'SELECT TOP 1 ISNULL(id, -1) AS id 
              FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].' . $table . ' 
              WHERE id < ' . (int)$id . ' 
              ORDER BY id DESC'; 
}

	// Preparando la sentencia
	$stmt = $db->prepare($query);
	// Ejecutanto el query
	$res = $stmt->execute();
	$row_rs_stmt = $stmt->fetch();
	$totalRows_rs_stmt  = $stmt->rowcount();
	if ($totalRows_rs_stmt > 0) {
	// Sino hay error carga el 1er registro	
		return $row_rs_stmt['id'];
	} else {
		// if (strtoupper($table) == 'USUARIOS' or strtoupper($table) == 'PRIVILEGIO') {
		// 	$query = 'select ISNULL(id,-1) as id from ' . $table .  ' where id < "ZZZZZZZZZZZZZZZZZZZ" order by id desc limit 1'; 
		// } else {
		// 	$query = "select ISNULL(id,-1) as id from " . $table .  " where id < 999999999 order by id desc limit 1"; 
		// }
		if (strtoupper($table) == 'USUARIOS' || strtoupper($table) == 'PRIVILEGIO') {
			$query = 'SELECT TOP 1 ISNULL(id, -1) AS id 
						FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].' . $table . ' 
						WHERE id < \'ZZZZZZZZZZZZZZZZZZZ\' 
						ORDER BY id DESC'; 
		} else {
			$query = 'SELECT TOP 1 ISNULL(id, -1) AS id 
						FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].' . $table . ' 
						WHERE id < 999999999 
						ORDER BY id DESC'; 
		}

		// Preparando la sentencia
		$stmt = $db->prepare($query);
		// Ejecutanto el query
		$res = $stmt->execute();
		$row_rs_stmt = $stmt->fetch();
		$totalRows_rs_stmt  = $stmt->rowcount();
		if ($totalRows_rs_stmt > 0) {
		// Sino hay error carga el 1er registro	
			return $row_rs_stmt['id'];
		} else {
			return -1;
		}
	}
}
?>
