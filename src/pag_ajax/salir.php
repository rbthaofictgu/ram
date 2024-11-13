<?PHP
session_start();
if (!isset($_SESSION['user_name'])) {
	 header("location: ../../index.php?msg=Favor inicie sesión para poder ingresar al sistema"); 
	 exit();
} else {

require_once('../utils/session_unset.php');
session_destroy();
}
?>