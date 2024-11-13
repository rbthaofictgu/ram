<?php header('Content-Type: text/html; charset=utf-8');?>
<?PHP
function get_usuario_by_email($db,$correo_electronico) {
	$query_rs_usuario = "SELECT a.*,b.id as id_trato,b.descripcion as trato,b.codigo 
	FROM usuarios a, trato b where a.id_trato = b.id and a.correo_electronico=:correo_electronico";
	$usuario = $db->prepare($query_rs_usuario);
	$res = $usuario->execute(Array(':correo_electronico' => $correo_electronico));
	$row_rs_usuario = $usuario->fetch();
	$res = $usuario->errorInfo();
	if (isset($res) and $res[2] <> '') {
		return false;
	} else {
		return $row_rs_usuario;
	}
}

function get_usuario_by_hash($db,$hash_user) {
	$query_rs_usuario = "SELECT a.*,b.id as id_trato,b.descripcion as trato,b.codigo FROM usuarios a, trato b where a.id_trato = b.id and a.hash = :hash";
	$usuario = $db->prepare($query_rs_usuario);
	$res = $usuario->execute(Array(':hash' => $hash_user));
	$row_rs_usuario = $usuario->fetch();
	$res = $usuario->errorInfo();
	if (isset($res) and $res[2] <> '') {
		return false;
	} else {
		return $row_rs_usuario;
	}
}

function get_usuario_by_id($db,$user_id) {
	$query_rs_usuario = "SELECT a.*,b.id as id_trato,b.descripcion as trato,b.codigo FROM usuarios a, trato b where a.id_trato = b.id and a.id=:id";
	$usuario = $db->prepare($query_rs_usuario);
	$res = $usuario->execute(Array(':id' => $user_id));
	$row_rs_usuario = $usuario->fetch();
	$res = $usuario->errorInfo();
	if (isset($res) and $res[2] <> '') {
		return false;
	} else {
		return $row_rs_usuario;
	}
}

?>