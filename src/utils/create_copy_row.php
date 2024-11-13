<?PHP
//***********************************************************************************************************
// Funcion para validar privilegio a acceder a cada opción
//************************************************************************************************************
if (!function_exists("f_create_copy_row")) {
function f_create_copy_row($tablanext,$rs_id_rs_mantenimiento,$db) 
{
// Creando sentencia select de busqueda de los privilegios por tipo de usuario
	$query = "select (contador+1) as contador from [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_contador_copia] a where a.id_tabla = :id_tabla";
	$privilegio = $db->prepare($query);
	$privilegio->execute(Array(':id_tabla' => $tablanext));
	$totalRows_rs_privilegio  = $privilegio->rowcount();
	if ($totalRows_rs_privilegio > 0) {	
		$row_rs_privilegio = $privilegio->fetch();
		$CONTADOR = $row_rs_privilegio['contador'];
		$query = "update [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_contador_copia] a set contador = (contador + 1) where a.id_tabla = :id_tabla";
		$privilegio = $db->prepare($query);
		$privilegio->execute(Array(':id_tabla' => $tablanext));
		return $CONTADOR;
	} else {
		// Creando sentencia select de busqueda de los privilegios por tipo de usuario
		// $query = 'insert into [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_contador_copia] 
		// (id_tabla,contador) values ("' . $tablanext . '",' . 1 . ')';
		$query = 'INSERT INTO [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_contador_copia] 
          (id_tabla, contador) 
          VALUES (\'' . $tablanext . '\', 1)';

		$privilegio = $db->prepare($query);
		$privilegio->execute();
		return 1;
	}
}
}
?>