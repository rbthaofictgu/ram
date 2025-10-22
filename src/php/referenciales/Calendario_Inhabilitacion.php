<?php
class Razon_Inhabilitacion{

    protected $setDB;

    public function __construct($db)
    {
        $this->setDB = $db;
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

    /* ================================================================================
    [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Calendario_Inhabilitacion] INSERT
    ===================================================================================*/

    protected function insertCalendario(array $data): int {
        // Sugerido: valida formato de fecha antes (Y-m-d)
        $sql = "
            INSERT INTO [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Calendario_Inhabilitacion]
            (fecha, id_razon_inhabilitacion, otra_razon_inhabilitacion,
            Estado, usuario_creacion, ip_creacion, host_creacion)
            VALUES
            (:fecha, :id_razon, :otra,
            :Estado, :usuario_creacion, :ip_creacion, :host_creacion);
            SELECT SCOPE_IDENTITY() AS id;
        ";
        $parametros = [
            ':fecha'               => $data['fecha'], // 'YYYY-MM-DD' o DateTime->format('Y-m-d')
            ':id_razon'            => (int)$data['id_razon_inhabilitacion'],
            ':otra'                => $data['otra_razon_inhabilitacion'] ?? null,
            ':Estado'              => array_key_exists('Estado', $data) ? (int)$data['Estado'] : 1,
            ':usuario_creacion'    => $this->padNChar($data['usuario_creacion'] ?? 'system', 30),
            ':ip_modificacion'        => $this->padNChar($this->setDB->getIp() ?? '0.0.0.0', 38),
            ':host_modificacion'      => $this->padNChar($this->setDB->getHost() ?? php_uname('n'), 100),
        ];
        return $this->setDB->getDB->insert($sql, $parametros);;
    }

    /* ================================================================================
    [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Calendario_Inhabilitacion] Update
    ===================================================================================*/    
    protected function updateCalendario(int $id, array $data): bool {
        $sql = "UPDATE [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Calendario_Inhabilitacion]
            SET fecha = :fecha,
                id_razon_inhabilitacion = :id_razon,
                otra_razon_inhabilitacion = :otra,
                Estado = :Estado,
                usuario_modificacion = :usuario_modificacion,
                fecha_modificacion   = GETDATE(),
                ip_modificacion      = :ip_modificacion,
                host_modificacion    = :host_modificacion
            WHERE id = :id;
        ";
        $parametros = [
            ':fecha'                  => $data['fecha'], // 'YYYY-MM-DD'
            ':id_razon'               => (int)$data['id_razon_inhabilitacion'],
            ':otra'                   => $data['otra_razon_inhabilitacion'] ?? null,
            ':Estado'                 => array_key_exists('Estado', $data) ? (int)$data['Estado'] : 1,
            ':usuario_modificacion'   => $this->padNChar($data['usuario_modificacion'] ?? 'system', 30),
            ':ip_modificacion'        => $this->padNChar($this->setDB->getIp() ?? '0.0.0.0', 38),
            ':host_modificacion'      => $this->padNChar($this->setDB->getHost() ?? php_uname('n'), 100),
            ':id'                     => $id,
        ];
        return $this->setDB->getDB->update($sql, $parametros);
    }

}