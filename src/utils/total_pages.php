<?PHP
//**********************************************************************************************************************
// Funcion para validar privilegio a acceder a cada opción
//**********************************************************************************************************************
if (!function_exists("f_total_pages")) {
function f_total_pages($tablanext,$no_of_records_per_page,$db) 
{

	$total_pages_sql = "SELECT COUNT(*) as contador FROM " . $tablanext;

try {
    $stmt = $db->prepare($total_pages_sql);
    $stmt->execute();
    $contador = $stmt->fetchColumn(); // Obtiene directamente el valor de la columna
	return ceil($contador/ $no_of_records_per_page);
} catch (PDOException $e) {
   //  echo "Error: " . $e->getMessage();
}


}
}
?>