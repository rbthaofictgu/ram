<?php
/**
 * Calcula la cantidad de días de semana (lunes a viernes)
 * transcurridos entre dos fechas completas (con hora, minutos y segundos).
 *
 * @param string $fechaInicio Formato 'Y-m-d H:i:s'
 * @param string $fechaFin    Formato 'Y-m-d H:i:s'
 * @return int Número de días hábiles entre ambas fechas
 */
session_start();
setlocale(LC_ALL,"es_ES@euro","es_ES","esp" );
//error_reporting(0);
header('Content-Type: application/x-javascript; charset=utf-8');
header('Access-Control-Allow-Origin: *');
ini_set('post_max_size', '100M');
ini_set('upload_max_filesize', '100M');
ini_set('max_execution_time', '10000');
ini_set('max_input_time', '10000');
ini_set('memory_limit', '256M');
date_default_timezone_set("America/Tegucigalpa");
//*configuración del las variables globales del sistema
include_once('configuracion/configuracion.php');
/**************************************************************************************************/
// Clase de Conexion a la Base De Datos
/*************************************************************************************************/	
include_once('../config/PdoDbAdapter.php');
include_once('../logs/logsClass.php');
include_once('AnularAviso.php');
class AvisosVencidos
{
	protected PdoDbAdapter $PdoDbAdapter;
    protected AnularAvisoCobro $AnularAvisoCobro;
	protected string $dominio;
	protected string $dominio_corto;
	protected string $dominio_raiz;
    protected string $appcfg_fecha_inicial_decreto;
    protected string $appcfg_fecha_final_decreto;
	protected int $appcfg_dias_vencimiento_modulo19;
	protected int $appcfg_dias_vencimiento_modulo15;

	public function __construct(PdoDbAdapter $PdoDbAdapter,
                                AnularAvisoCobro $AnularAvisoCobro,
								string $appcfg_Dominio,
								string $appcfg_Dominio_Corto,
								string $appcfg_Dominio_Raiz,
								string $appcfg_fecha_inicial_decreto,
								string $appcfg_fecha_final_decreto,
								int $appcfg_dias_vencimiento_modulo19,
								int $appcfg_dias_vencimiento_modulo15)
	{
		$this->PdoDbAdapter = $PdoDbAdapter;
        $this->AnularAvisoCobro = $AnularAvisoCobro};
		$this->dominio = $appcfg_Dominio;
		$this->dominio_corto = $appcfg_Dominio_Corto;
		$this->dominio_raiz = $appcfg_Dominio_Raiz;
        $this->appcfg_fecha_inicial_decreto=$appcfg_fecha_inicial_decreto;
        $this->appcfg_fecha_final_decreto =$appcfg_fecha_final_decreto;
		$this->appcfg_dias_vencimiento_modulo19 = $appcfg_dias_vencimiento_modulo19;
		$this->appcfg_dias_vencimiento_modulo15 = $appcfg_dias_vencimiento_modulo15;
		//********************************************************************************/
        // Ejecutando funcion que filtra los avisos de cobro vencidos
		//********************************************************************************/
        $this->getAvisosDeCobro();

    }


	protected function calendarioLaboral(DateTime $fecha):bool {
		// Si se encuentra en el calendario de inhabilitacion devuelve false (no laborable)
		$sql = "SET DATEFORMAT ymd;
            SELECT fecha FROM [IHTT_RENOVACIONES_AUTOMATICAS].[dbo].[TB_Calendario_Inhabilitacion]
            WHERE Estado = 1 AND fecha = cast(:fecha as Date)";
        $parametros = [':fecha'          => $fecha->format('Y-m-d')];
        $rows = $this->PdoDbAdapter->selectOne($sql, $parametros);
        if (!empty($rows)) {
			return false;
		} else {
			return true;
		}
	}
	//*************************************************************************************/
	//* FUNCION QUE DETERMINA LOS DIAS HABILES ENTRE DOS FECHAS
	//*************************************************************************************/
	protected function diasHabilesEntreFechas($fechaInicio, $fechaFin): int {
		$inicio = new DateTime($fechaInicio);
		$fin = new DateTime($fechaFin);
		// Asegurar que la fecha inicial sea menor o igual que la final
		if ($inicio > $fin) {
			[$inicio, $fin] = [$fin, $inicio];
		}
		$diasHabiles = 0;
		// Iterar día por día hasta alcanzar la fecha final
		while ($inicio <= $fin) {
			$diaSemana = (int)$inicio->format('N'); // 1 = Lunes, 7 = Domingo
			if ($diaSemana < 6) { // Lunes (1) a Viernes (5)
				//**********************************************************************************************************/
				// SI LO ENCUENTRA DEVUELVE FALSE Y SINO TRUE
				//**********************************************************************************************************/
				$respuestaCalendario = $this->calendarioLaboral($inicio);
				//**********************************************************************************************************/
				// Si no se encuentra en el calendario de inhabilitacion devolvio true (dia laborable) suma a $diasHabiles
				//**********************************************************************************************************/
				if ($respuestaCalendario) {
					$diasHabiles++;
				}
			}
			$inicio->modify('+1 day');
		}
		return $diasHabiles;
	}

   	//*************************************************************************************/
	//* FUNCION QUE FILTRA LOS AVISOS DE COBRO Y DETERMINA CUALES ESTAN VENCIDOS
	//*************************************************************************************/

    protected function getAvisosDeCobro(): void
    {
        // Normaliza límites (si te pasan fecha con hora, igual funciona)
        $desde = new DateTime($this->appcfg_fecha_inicial_decreto ?? '1970-01-01 00:00:00');
        $hasta = new DateTime($this->appcfg_fecha_final_decreto   ?? '2099-12-31 23:59:59');

        // Hacemos el rango semi-abierto: [desde, hasta+1día)
        $hastaExclusivo = (clone $hasta)->modify('+1 day')->setTime(0, 0, 0);

        $sql = "
			SET DATEFORMAT ymd;
            SELECT
                [CodigoAvisoCobro],
                [FechaEmision],
                [FechaVencimiento],
                [RTNConcesionario],
                [ID_Solicitud],
                [Expediente],
                [CertificadoOperacion],
                [PermisoExplotacion],
                [Placa],
                [CodigoCategoriaServicio],
                [AvisoCobroEstado],
                [Observaciones],
                [CodigoBanco],
                [ReferenciaPago],
                [FechaPagadoAnulado],
                [UsuarioPagoAnulo],
                [ObservacionesAnulacion],
                [SistemaUsuario],
                [SistemaFecha],
                [CodigoRegionalIHTT],
                [IPUsuario],
                [NombreConcesionario],
                [MontoLetras],
                [ID_TipoCobro],
                [NumeroCenso],
                [ID_Modulo],
                [ID_Notificado],
                [ID_Organizacion],
                [Requeridos],
                [Contrato],
                [Resolucion],
                [ID_EstadoCobranza],
                [ID_MotivoAnula],
                [ID_UsuReferencia],
                [Fecha_Reversion],
                [ObservacionReversion]
            FROM [IHTT_Webservice].[dbo].[TB_AvisoCobroEnc]
            WHERE
                [AvisoCobroEstado] = 1
                AND [ID_Modulo] IN (15, 19)
                AND [FechaEmision] >= :desde
                AND [FechaEmision] <  :hastaExclusivo
            ORDER BY [FechaEmision] DESC;
        ";

        $parametros = [
            ':desde'          => $desde->format('Y-m-d H:i:s'),
            ':hastaExclusivo' => $hastaExclusivo->format('Y-m-d H:i:s'),
        ];
        $rows = $this->PdoDbAdapter->select($sql, $parametros);
        if (!empty($rows) && count($rows) > 0) {
            foreach ($rows as $row) {
                $dias = $this->diasHabilesEntreFechas($row['FechaEmision'], date('Y-m-d H:i:s'));
                // SI SE CUMPLE EL NUMERO DE DIAS PARA EL MODULO 19 O 15 PROCEDE A ANULAR EL AVISO DE COBRO
                if (($row['ID_Modulo'] == 19 && $dias > $this->appcfg_dias_vencimiento_modulo19) || ($row['ID_Modulo'] == 15 && $dias > $this->appcfg_dias_vencimiento_modulo15) ) {
                    echo json_encode([
                        "success" => 1,
                        "message" => sprintf(
                            'AVISO DE COBRO: %s FECHA EMISION: %s DIAS TRANSCURRIDOS: %d MODULO: %d',
                            $row['CodigoAvisoCobro'],
                            $row['FechaEmision'],
                            $dias,
                            $row['ID_Modulo']
                        )
                    ]);
                    echo "<br/>";
                    $this->AnularAvisoCobro->anularAvisoCobro((int)$row['CodigoAvisoCobro']);
                }
            }
        } else {
            echo json_encode([
                "error"     => 7015,
                "errorhead" => 'CONVIRTIENDO FLS TO RAM',
                "errormsg"  => 'NO HAY FSL PARA CONVERTIR A RAM'
            ]);
        }
    }

}   

if (!isset($_SESSION["ID_Usuario"]) || !isset($_SESSION["user_name"])) {
	echo json_encode(array("error" => 1100, "errorhead" => "INICIO DE SESSIÓN", "errormsg" => 'NO HAY UNA SESSION INICIADA, FAVOR INICIE SESION Y VUELVA A INTENTARLO'));
} else {
	$logsClass = new logsClass();
    $db->beginTransaction();
    $PdoDbAdapter = new PdoDbAdapter($db,$logsClass,$_SESSION["user_name"]);
    $AnularAvisoCobro = new AnularAvisoCobro($PdoDbAdapter,$appcfg_estadoObjetivo_anulacion,$appcfg_estadoRequerido_anulacion,$appcfg_descripcion_anulacion_aviso_cobro,$_SESSION["user_name"]);        
	$AvisosVencidos = new AvisosVencidos($PdoDbAdapter,
                        $AnularAvisoCobro,
		$appcfg_Dominio,
		$appcfg_Dominio_Corto,
		$appcfg_Dominio_Raiz,
		$appcfg_fecha_inicial_decreto,
		$appcfg_fecha_final_decreto,
		$appcfg_dias_vencimiento_modulo19,
		$appcfg_dias_vencimiento_modulo15);
    $db->rollBack();        
}
