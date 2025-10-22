<?php
class Razon_Inhabilitacion{

    protected $setDB;

    public function __construct($db)
    {
        $this->setDB = $db;
		if (isset($_POST["action"])) {
			if ($_POST["action"] == "insert-razon") {
                $data = isset($_POST["Data"]) ? $_POST["Data"] : [];
    			echo $this->insertRazon($data);
            } else if ($_POST["action"] == "update-razon") {
                $id = isset($_POST["id"]) ? (int)$_POST["id"] : 0;
                $data = isset($_POST["Data"]) ? $_POST["Data"] : [];
                echo $this->updateRazon($id, $data);
            } else {
                echo json_encode(array("error" => 10001, "errorhead" => 'NO SE ENCONTRO UNA ACCIÓN CON EL NOMBRE <strong>' .  $_POST["action"] . '.</strong>', "errormsg" => ' REINTENTETE LO NUEVAMENTE, SI EL PROBLEMA PERSISTE CONTACTE AL ADMINISTADOR DEL SISTEMA'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);						
            }
        }

    }
    /**
     * Normaliza cadenas fijas NCHAR: recorta a la izquierda/derecha
     * y rellena a la derecha para cumplir el largo fijo requerido.
     */
    function padNChar(?string $value, int $len): string {
        $value = $value ?? '';
        $value = mb_substr(trim($value), 0, $len);
        return str_pad($value, $len, ' ');
    }

    /* ===========================================================
    [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Razon_Inhabilacion] INSERT
    =========================================================== */

    protected function insertRazon(array $data): mixed {
        if (empty($data['descripcion'])) {
            return json_encode(array("error" => 10002, "errorhead" => '<strong>INFORMACION DE LA RAZON</strong>', "errormsg" => 'REINTENTETE LO NUEVAMENTE, SI EL PROBLEMA PERSISTE CONTACTE AL ADMINISTADOR DEL SISTEMA'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);						
        } else {
            $sql = "
                INSERT [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Razon_Inhabilacion]
                (descripcion, esOtroExplique,
                Estado, usuario_creacion, ip_creacion, host_creacion)
                VALUES
                (:descripcion, :esOtroExplique,
                :Estado, :usuario_creacion, :ip_creacion, :host_creacion);
                SELECT SCOPE_IDENTITY() AS id;
            ";    
            $parametros = [
                ':descripcion'       => $data['descripcion'],
                ':esOtroExplique'    => !empty($data['esOtroExplique']) ? 1 : 0,
                ':Estado'            => array_key_exists('Estado', $data) ? (int)$data['Estado'] : 1,
                ':usuario_creacion'  => $this->padNChar($data['usuario_creacion'] ?? 'system', 30),
                ':ip_modificacion'        => $this->padNChar($this->setDB->getIp() ?? '0.0.0.0', 38),
                ':host_modificacion'      => $this->padNChar($this->setDB->getHost() ?? php_uname('n'), 100),
            ];
            return json_encode(array("id"=> $this->setDB->getDB->insert($sql, $parametros)));
        } 
    }

    /* ===========================================================
    [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Razon_Inhabilacion] Update
    =========================================================== */    
    protected function updateRazon(int $id, array $data): mixed {
        if (!is_numeric($id) || (int)$id <= 0) {
            return json_encode(array("error" => 10003, "errorhead" => '<strong>ID DE LA RAZON</strong>', "errormsg" => 'REINTENTETE LO NUEVAMENTE, SI EL PROBLEMA PERSISTE CONTACTE AL ADMINISTADOR DEL SISTEMA'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);						
        } else {
            if (empty($data['descripcion'])) {
                return json_encode(array("error" => 10004, "errorhead" => '<strong>INFORMACION DE LA RAZON</strong>', "errormsg" => 'REINTENTETE LO NUEVAMENTE, SI EL PROBLEMA PERSISTE CONTACTE AL ADMINISTADOR DEL SISTEMA'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);						
            } else {
                $sql = "
                    UPDATE [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Razon_Inhabilacion]
                    SET descripcion = :descripcion,
                        esOtroExplique = :esOtroExplique,
                        Estado = :Estado,
                        usuario_modificacion = :usuario_modificacion,
                        fecha_modificacion   = GETDATE(),
                        ip_modificacion      = :ip_modificacion,
                        host_modificacion    = :host_modificacion
                    WHERE id = :id;
                ";
                $parametros = [
                    ':descripcion'          => $data['descripcion'],
                    ':esOtroExplique'       => !empty($data['esOtroExplique']) ? 1 : 0,
                    ':Estado'               => array_key_exists('Estado', $data) ? (int)$data['Estado'] : 1,
                    ':usuario_modificacion' => $this->padNChar($data['usuario_modificacion'] ?? 'system', 30),
                    ':ip_modificacion'        => $this->padNChar($this->setDB->getIp() ?? '0.0.0.0', 38),
                    ':host_modificacion'      => $this->padNChar($this->setDB->getHost() ?? php_uname('n'), 100),
                    ':id'                   => $id,
                ];
                return $this->setDB->getDB->update($sql, $parametros);
            }
        }
    }
}