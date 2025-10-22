<?php
class AnularAvisoCobro
{
   	protected PdoDbAdapter $PdoDbAdapter;
    protected int $estadoObjetivo_anulacion;
    protected int $estadoRequerido_anulacion;       
    protected string $ObservacionesAnulacion;
    protected string $usuario_creacion;
	public function __construct(PdoDbAdapter $PdoDbAdapter,int $estadoObjetivo_anulacion=3,string $ObservacionesAnulacion="AVISO DE COBRO DECRETO # 2025-548-2",int $estadoRequerido_anulacion=1,string $usuario_creacion='')
	{
		$this->PdoDbAdapter = $PdoDbAdapter;
        $this->estadoObjetivo_anulacion = $estadoObjetivo_anulacion;
        $this->estadoRequerido_anulacion = $estadoRequerido_anulacion;       
        $this->ObservacionesAnulacion = $ObservacionesAnulacion;
        $this->usuario_creacion = $usuario_creacion;
		//********************************************************************************/
        // Ejecutando funcion que filtra los avisos de cobro vencidos
		//********************************************************************************/
    }

    /**
     * Helper para simular NCHAR fijo (recorta/rellena).
    */
    protected function padNChar(string $value, int $len): string
    {
        $value = mb_substr($value, 0, $len);
        $pad   = $len - mb_strlen($value);
        return $pad > 0 ? $value . str_repeat(' ', $pad) : $value;
    }

    /**
     * Anula un aviso de cobro (solo si está en estado 1).
     * Espera: ['idPreforma' => ..., 'razon' => ..., 'usuario_creacion' => ..., 'estado_requerido' => ..., 'estado_objetivo' => ...]
     * Retorna: JSON string
     */
    public function anularAvisoCobro(int $CodigoAvisoCobro): string
    {
        $exists = $this->PdoDbAdapter->selectOne(
            "SELECT 1 AS ok 
               FROM [IHTT_Webservice].[dbo].[TB_AvisoCobroEnc]
              WHERE [CodigoAvisoCobro] = :CodigoAvisoCobro
                AND [AvisoCobroEstado] = :AvisoCobroEstado",
            [':CodigoAvisoCobro' => $CodigoAvisoCobro,':AvisoCobroEstado'=>$this->estadoRequerido_anulacion],
            PDO::FETCH_ASSOC
        );
        if ($exists === false || $exists === null) {
            return json_encode([
                "error"     => 1105,
                "errorhead" => "OBTENIENDO AVISO DE COBRO",
                "errormsg"  => "ALGO INESPERADO SUCEDIO AL INTENTAR ACTUALIZAR EL ESTADO DEL AVISO DE COBRO, VUELVA A INTENTARLO SI EL ERROR PERSISTE CONTACTE AL ADMINISTRADOR DEL SISTEMA",
            ], JSON_UNESCAPED_UNICODE);
        }
        // Actualizar estado
        $q = "
            UPDATE [IHTT_Webservice].[dbo].[TB_AvisoCobroEnc] 
               SET [AvisoCobroEstado]      = :AvisoCobroEstado,
                   [FechaPagadoAnulado]     = SYSDATETIME(),
                   [ObservacionesAnulacion] = TRIM(:ObservacionesAnulacion),
                   [UsuarioPagoAnulo]       = :UsuarioPagoAnulo
             WHERE [CodigoAvisoCobro] = :CodigoAvisoCobro
               AND [AvisoCobroEstado] = :AvisoCobroEstadoRequerido;";

        $ok = $this->PdoDbAdapter->update($q, [
            ':AvisoCobroEstado' => $this->estadoObjetivo_anulacion,
            ':ObservacionesAnulacion' => $this->ObservacionesAnulacion,
            ':UsuarioPagoAnulo'    => $this->usuario_creacion,
            ':CodigoAvisoCobro'   => $CodigoAvisoCobro,
            ':AvisoCobroEstadoRequerido'   => $$this->estadoRequerido_anulacion
        ]);

        if (!$ok) {
            return json_encode([
                "error"     => 1106,
                "errorhead" => "ACTUALIZANDO DE ESTADO",
                "errormsg"  => "ALGO INESPERADO SUCEDIO AL INTENTAR ACTUALIZAR EL ESTADO DEL AVISO DE COBRO, VUELVA A INTENTARLO SI EL ERROR PERSISTE CONTACTE AL ADMINISTRADOR DEL SISTEMA",
            ], JSON_UNESCAPED_UNICODE);
        }
        return true;
    }   
}
