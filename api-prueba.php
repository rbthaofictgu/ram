<?php
session_start();
//* archivo de configuracion de las variables globales
require_once('configuracion/configuracion.php');
// require_once('configuracion/configuracion_js.php');
//*archivo de configuracion de la base de datos
require_once('../config/conexion.php');
$tipo_transporte = 'CARGA';
// $tipo_transporte  = 'PASAJERO';
$tipo_concesion = 'BIR';
// $tipo_concesion = 'CNE';
// $tipo_concesion='CENE';
// $tipo_concesion= 'MTX';
// $tipo_concesion = 'TES';

//***********************
//*DATOS TB_Permiso_Explotacion_Pas
// $Datos = [
//    'N_Permiso_Explotacion' => 'PE-BIR-196-21',
//    'N_Permiso_Explotacion_Encrypted' => 'PE-BIR-196-21',
//    'RTN_Concesionario' => '08019004512004',
//    'ID_Colegiacion' => '8177',
//    'Resolucion' => 'CDTT-IHTT-T-16339-2020',
//    'ID_Expediente' => '20181227BIR1486',
//    'Fecha_Emision' => '2020-10-08 00:00:00',
//    'ID_Categoria' => 'BIR',
//    'Fecha_Vencimiento' => '2032-10-08 00:00:00',
//    'ID_Ruta' => 'IRT-693',
//    'Codigo_Censo' => 'CENSO-20165365',
//    'Observaciones' => 'ESTO ES DE PRUEBA',
//    'ID_Estado' => 'ES-01',
//    'Sistema_Usuario' => 'CCABALLERO',
//    'Sistema_Fecha' => '2020-10-08 09:44:46',
//    'N_Contrato' => 'CC-BIR-190-20',
//    'Observacion_Cancelacion' => NULL,
//    'Usuario_Cancelacion' => NULL,
//    'Fecha_Cancelacion' => NULL,
//    'Permiso_Anterior' => '239J0020308019004512013',
//    'Comisionado_Gestion' => NULL,
//    'Usuario_Creacion' => 'CCABALLERO',
//    // 'Codigo_Aduanero'=> NULL,
//    // 'Tipo_Generacion' => NULL,
//    // 'URL_Constancia_Transportista' => NULL,
//    'Comisionado_Gestion_Historico' => NULL,
// ];
//**DATOSTB_Permiso_Explotacion_Carga  CNE
$Datos = [
   'N_Permiso_Explotacion' => 'PE-CNE-103468-25',
   'N_Permiso_Explotacion_Encrypted' => 'PE-CNE-103468-25',
   'RTN_Concesionario' => '08251976000217',
   'ID_Colegiacion' => '9934',
   'ID_Expediente' => '20240116CNE00697-01053',
   'Resolucion' => 'PCDTT-IHTT-T-004928-2024',
   'Fecha_Emision' => '2020-10-08 00:00:00',
   'ID_Categoria' => 'CNE',
   'Fecha_Vencimiento' => '2032-10-08 00:00:00',
   'Codigo_Censo' => 'SOL002869860 ',
   'Observaciones' => 'PERIODO DE EMERGE19',
   'ID_Estado' => 'ES-01',
   'Sistema_Usuario' => 'CCABALLERO',
   'Sistema_Fecha' => '2020-10-08 09:44:46',
   'N_Contrato' => 'CC-CNE-10346-24',
   'Observacion_Cancelacion' => NULL,
   'Usuario_Cancelacion' => NULL,
   'Fecha_Cancelacion' => NULL,
   'Permiso_Anterior' => 'N/A',
   'Comisionado_Gestion' => NULL,
   'Usuario_Creacion' => 'CCABALLERO',
   'Codigo_Aduanero' => NULL,
   'Tipo_Generacion' => NULL,
   'URL_Constancia_Transportista' => NULL,
   'Comisionado_Gestion_Historico' => NULL,
];

insertPermisoCargaPermisoPasajero($Datos, $tipo_transporte, $tipo_concesion, $db);

//*****************************************************************************************/
//* INICIO : FUNCION ENCARGADA DE INSRTAR LOS PERMISOS DE EXPLOTACION DE CARGA Y PASAJEROS
//****************************************************************************************/
function insertPermisoCargaPermisoPasajero($Datos, $tipo_transporte, $tipo_concesion, $db)
{
   $query = '';
   $params = [];

   try {
      if ($tipo_transporte == 'CARGA') {
         //******************************************
         //***** INICIO: PERMISO EXPLOTACION CARGA
         //*****************************************
         $query = 'INSERT INTO [IHTT_SGCERP].[dbo].[TB_Permiso_Explotacion_Carga](
               N_Permiso_Explotacion, N_Permiso_Explotacion_Encrypted,RTN_Concesionario,ID_Colegiacion, ID_Expediente,Resolucion, Fecha_Emision,ID_Categoria,Fecha_Vencimiento,Codigo_Censo,Observaciones,ID_Estado,Sistema_Usuario,Sistema_Fecha, N_Contrato, Observacion_Cancelacion,Usuario_Cancelacion,Fecha_Cancelacion,Permiso_Anterior,Comisionado_Gestion,Usuario_Creacion,Codigo_Aduanero,Tipo_Generacion,URL_Constancia_Transportista,
               Comisionado_Gestion_Historico)VALUES(:N_Permiso_Explotacion,:N_Permiso_Explotacion_Encrypted,:RTN_Concesionario,:ID_Colegiacion, :ID_Expediente,:Resolucion,:Fecha_Emision,:ID_Categoria,:Fecha_Vencimiento,:Codigo_Censo,:Observaciones,:ID_Estado,:Sistema_Usuario,SYSDATETIME(),:N_Contrato,:Observacion_Cancelacion,:Usuario_Cancelacion,:Fecha_Cancelacion,:Permiso_Anterior,:Comisionado_Gestion,:Usuario_Creacion,:Codigo_Aduanero,:Tipo_Generacion,:URL_Constancia_Transportista,:Comisionado_Gestion_Historico)';

         $params = [
            ':N_Permiso_Explotacion' => $Datos['N_Permiso_Explotacion'],
            ':N_Permiso_Explotacion_Encrypted' => hash('md5', $Datos['N_Permiso_Explotacion']),
            ':RTN_Concesionario' => $Datos['RTN_Concesionario'],
            ':ID_Colegiacion' => $Datos['ID_Colegiacion'],
            ':ID_Expediente' => $Datos['ID_Expediente'],
            ':Resolucion' => $Datos['Resolucion'],
            ':Fecha_Emision' => $Datos['Fecha_Emision'],
            ':ID_Categoria' => $Datos['ID_Categoria'],
            ':Fecha_Vencimiento' => $Datos['Fecha_Vencimiento'],
            ':Codigo_Censo' => $Datos['Codigo_Censo'],
            ':Observaciones' => $Datos['Observaciones'],
            ':ID_Estado' => $Datos['ID_Estado'],
            ':Sistema_Usuario' => $Datos['Sistema_Usuario'],
            ':N_Contrato' => $Datos['N_Contrato'],
            ':Observacion_Cancelacion' => $Datos['Observacion_Cancelacion'],
            ':Usuario_Cancelacion' => $Datos['Usuario_Cancelacion'],
            ':Fecha_Cancelacion' => $Datos['Fecha_Cancelacion'],
            ':Permiso_Anterior' => $Datos['Permiso_Anterior'],
            ':Comisionado_Gestion' => $Datos['Comisionado_Gestion'],
            ':Usuario_Creacion' => $Datos['Usuario_Creacion'],
            ':Codigo_Aduanero' => $Datos['Codigo_Aduanero'],
            ':Tipo_Generacion' => $Datos['Tipo_Generacion'],
            ':URL_Constancia_Transportista' => $Datos['URL_Constancia_Transportista'],
            ':Comisionado_Gestion_Historico' => $Datos['Comisionado_Gestion_Historico']
         ];
         //******************************************
         //***** FINAL: PERMISO EXPLOTACION CARGA
         //*****************************************
      } else {

         //********************************************
         //***** INICIO: PERMISO EXPLOTACION PASAJERO
         //********************************************
         $query = 'INSERT INTO [IHTT_SGCERP].[dbo].[TB_Permiso_Explotacion_Pas](
            N_Permiso_Explotacion,
            N_Permiso_Explotacion_Encrypted,
            RTN_Concesionario,
            ID_Colegiacion,
            Resolucion,
            ID_Expediente,
            Fecha_Emision,
            ID_Categoria,
            Fecha_Vencimiento,
            ID_Ruta,
            Codigo_Censo,
            Observaciones,
            ID_Estado,
            Sistema_Usuario,
            Sistema_Fecha,
            N_Contrato,
            Observacion_Cancelacion,
            Usuario_Cancelacion,
            Fecha_Cancelacion,
            Permiso_Anterior,
            Comisionado_Gestion,
            Usuario_Creacion,
            Comisionado_Gestion_Historico)
            VALUES(:N_Permiso_Explotacion,
            :N_Permiso_Explotacion_Encrypted,
            :RTN_Concesionario,
            :ID_Colegiacion,
            :Resolucion,
            :ID_Expediente,
            :Fecha_Emision,
            :ID_Categoria,
            :Fecha_Vencimiento,
            :ID_Ruta,
            :Codigo_Censo,
            :Observaciones,
            :ID_Estado,
            :Sistema_Usuario,
            SYSDATETIME(),
            :N_Contrato,
            :Observacion_Cancelacion,
            :Usuario_Cancelacion,
            :Fecha_Cancelacion,
            :Permiso_Anterior,
            :Comisionado_Gestion,
            :Usuario_Creacion,
            :Comisionado_Gestion_Historico)';

         $params = [
            ':N_Permiso_Explotacion' => $Datos['N_Permiso_Explotacion'],
            ':N_Permiso_Explotacion_Encrypted' => hash('md5', $Datos['N_Permiso_Explotacion']),
            ':RTN_Concesionario' => $Datos['RTN_Concesionario'],
            ':ID_Colegiacion' => $Datos['ID_Colegiacion'],
            ':Resolucion' => $Datos['Resolucion'],
            ':ID_Expediente' => $Datos['ID_Expediente'],
            ':Fecha_Emision' => $Datos['Fecha_Emision'],
            ':ID_Categoria' => $Datos['ID_Categoria'],
            ':Fecha_Vencimiento' => $Datos['Fecha_Vencimiento'],
            ':ID_Ruta' => $Datos['ID_Ruta'],
            ':Codigo_Censo' => $Datos['Codigo_Censo'],
            ':Observaciones' => $Datos['Observaciones'],
            ':ID_Estado' => $Datos['ID_Estado'],
            ':Sistema_Usuario' => $_SESSION['user_name'],
            // ':Sistema_Fecha' lo maneja SYSDATETIME() en SQL
            ':N_Contrato' => $Datos['N_Contrato'],
            ':Observacion_Cancelacion' => $Datos['Observacion_Cancelacion'],
            ':Usuario_Cancelacion' => $Datos['Usuario_Cancelacion'],
            ':Fecha_Cancelacion' => $Datos['Fecha_Cancelacion'],
            ':Permiso_Anterior' => $Datos['Permiso_Anterior'],
            ':Comisionado_Gestion' => $Datos['Comisionado_Gestion'],
            ':Usuario_Creacion' => $_SESSION['user_name'],
            ':Comisionado_Gestion_Historico' => $Datos['Comisionado_Gestion_Historico']
         ];
         //********************************************
         //***** INICIO: PERMISO EXPLOTACION PASAJERO
         //********************************************
      }
      // echo json_encode($params);
      $respuesta = insert($query, $params, $db);

      if ($respuesta) {
         echo json_encode(['success' => 1, 'message' => 'Registro insertado correctamente en insertPeCargaPePasajero ' . $tipo_transporte]);
      } else {
         echo json_encode(['success' => 0, 'message' => 'Error al insertar el registro en insertPeCargaPePasajero ' . $tipo_transporte]);
      }
   } catch (PDOException $e) {
      echo json_encode(['success' => 0, 'message en insertPeCargaPePasajero' => 'PDOException: ' . $e->getMessage()]);
   }
}
//*****************************************************************************************/
//* FINAL : FUNCION ENCARGADA DE INSRTAR LOS PERMISOS DE EXPLOTACION DE CARGA Y PASAJEROS
//****************************************************************************************/

//* PERMISO ESPECIAL CARGA
// $tipo_concesion = 'CENE';
// $Datos = [
//    'N_Permiso_Especial_Carga' => 'PES-CENE-7969-25',
//    'N_Permiso_Especial_Carga_Encrypted' => 'PES-CENE-7969-25',
//    'N_Certificado' => 'N/A',
//    'RTN_Concesionario' => '08019002263767',
//    'ID_Vehiculo_Carga' => 'VTP-108806',
//    'Resolucion' => 'PCDTT-IHTT-T-002634-2024',
//    'ID_Expediente' => '20240215CENE00863-00578',
//     'Fecha_Emision' => '2020-09-06 00:00:00.000',
//    'Fecha_Elaboracion' => '2025-08-11 14:48:59.000',
//    'Fecha_Expiracion' => '2025-08-11',
//    'ID_Categoria' => 'CENE',
//    'ID_Colegiacion' => '10649',
//    'Numero_Registro' => '0501-CENE-4266',
//    'Direccion' => 'SAN PEDRO SULA',
//    'Codigo_Censo' => 'SOL002871760',
//    'Observaciones' => NULL,
//    'ID_Estado' => 'ES-02',
//    'Sistema_Usuario' => 'CCORDONEZ',
//    'Observacion_Cancelacion' => NULL,
//    'Usuario_Cancelacion' => NULL,
//    'Fecha_Cancelacion' =>NULL,
//    'ID_Tipo_Categoria' =>NULL,
//    'Comisionado_Gestion' => 'rbarahona',
//    'Pre_Permiso' => 'PEC-CENE-1758-25',
//    'Pre_Estado' => 'GENERADO',
//    'Pre_Fecha' => '2025-07-11 11:10:15',
//    'Plan_Prorenova' => 'NO',
//    'Usuario_Creacion' => 'kordonez',
//    'Codigo_Aduanero' => NULL,
//    'Tipo_Generacion' => NULL,
//    'URL_Constancia_Unidad' => NULL,
//    'Reimpresion' => NULL,
//    'Comisionado_Gestion_Historico' => 'rbarahona',
//    'Gestion_Masiva' => 'NO'
// ];

//*CERTIFICADO CARGA
// $tipo_concesion = 'CNE';

// $Datos = [
//    'N_Certificado' => 'CO-CNE-24323-25',
//    'N_Certificado_Encrypted' => 'CO-CNE-24323-25',
//    'N_Permiso_Explotacion' => 'PE-CNE-7314-21',
//    'RTN_Concesionario' => 'PE-CNE-7314-21',
//    'ID_Vehiculo_Carga' => 'VTP-108817',
//    'Resolucion' => 'PCDTT-IHTT-T-010066-2025',
//    'ID_Expediente' => '20250321CNE01396-01352',
//    'Fecha_Emision' => '2020-09-06 00:00:00.000',
//    'Fecha_Elaboracion' => '2025-08-11 14:48:59.000',
//    'Fecha_Expiracion' => '2025-08-11',
//    'ID_Categoria' => 'CNE',
//    'ID_Colegiacion' => '25360',
//    'Numero_Registro' => '0501-CNE-5579',
//    'Direccion' => 'CHOLOMA, CORTES',
//    'ID_Estado' => 'ES-02',
//    'Codigo_Censo' => 'SOL002895667',
//    'Observaciones' => 'datos de prueba 2...',
//    'Sistema_Usuario' => 'ccaballero',
//    // 'Sistema_Fecha' => date('Y-m-d H:i:s'),
//    'Observacion_Cancelacion' => NULL,
//    'Fecha_Cancelacion' => NULL,
//    'Certificado_Anterior' => NULL,
//    'ID_Tipo_Categoria' => NULL,
//    'Comisionado_Gestion' => 'rbarahona',
//    'Pre_Certificado' => 'PCO-CNE-5722-25',
//    'Pre_Estado' => NULL,
//    'Pre_Fecha' => NULL,
//    'Plan_Prorenova' => NULL,
//    'Usuario_Cancelacion' => 'ccaballero',
//    'Tipo_Generacion' => NULL,
//    'URL_Constancia_Unidad' => NULL,
//    'Reimpresion' => NULL,
//    'Comisionado_Gestion_Historico' => NULL,
//    'Gestion_Masiva' => NULL
// ];

//*DATOS
// $tipo_concesion= 'MTX';
// $Datos = [
//    'N_Certificado' => 'CO-MTX-2615-25',
//    'N_Certificado_Encrypted' =>'CO-MTX-2615-25',
//    'N_Permiso_Explotacion' => 'PE-MTX-2230-25',
//    'RTN_Concesionario' => '13081968001173',
//    'ID_Vehiculo_Transporte' => 'VTP-39222',
//    'ID_Expedient' => '20240806MTX00327-01246',
//    'Fecha_Emision' => '2025-07-09 00:00:00',
//    'Fecha_Elaboracion' => '2025-07-09 10:47:43',
//    'Fecha_Expiracion' => '2028-07-09 00:00:00',
//    'ID_Categoria' => 'MTX',
//    'ID_Colegiacion' => '6636',
//    'Direccion' => 'GRACIAS, LEMPIRAss',
//    'Resolucion' => 'PCDTT-IHTT-T-008408-2025',
//    'ID_Ruta' => NULL,
//    'ID_Estado' => 'ES-02',
//    'Codigo_Censo' => 'SOL002880677',
//    'Observaciones' => 'ESTA ES UNA PRUEBA DE INSERCION DE LA FINCION',
//    'Numero_Registro' => '1301-MTX-0556',
//    'Sistema_Usuario' => 'molmedo',
//    'Observacion_Cancelacion' => NULL,
//    'Usuario_Cancelacion' => NULL,
//    'Fecha_Cancelacion' => NULL,
//    'Certificado_Anterior' => 'N/A',
//    'Comisionado_Gestion' => 'rbarahona',
//    'Pre_Certificado' => 'PCO-MTX-2521-25',
//    'Pre_Estado' => 'GENERADO',
//    'Pre_Fecha' => '2025-07-09 10:46:12',
//    'Usuario_Creacion' => 'molmedo',
//    'Plan_Prorenova' => 'NO',
//    'ID_Area_Operacion' => NULL,
//    'Proyecto_Rosa' => 'NO',
//    'ID_SubCategoria' => NULL,
//    'Comisionado_Gestion_Historico' => 'rbarahona',
//    'Gestion_Masiva' => 'NO',
//    'Reimpresion' => NULL,
// ];

//******************************/
//** permiso especial carga
//******************************/

// $tipo_concesion='TES';
// $Datos = [
//    'N_Permiso_Especial_Pas' => 'PES-TES-34-26',
//    'N_Permiso_Especial_Pas_Encrypted' => 'PES-TES-34-26',
//    'N_Certificado' => 'N/A',
//    'RTN_Concesionario' => '08011987159538',
//    'ID_Departamento' => '08',
//    'ID_Vehiculo_Transporte' => 'VTP-38950',
//    'ID_Expediente' => '20230706TES00404-01053',
//    'Fecha_Emision' => '2020-09-06 00:00:00.000',
//    'Fecha_Elaboracion' => '2025-08-11 14:48:59.000',
//    'Fecha_Expiracion' => '2025-08-11',
//    'ID_Categoria'  => 'TES',
//    'ID_Colegiacion' => '16759',
//    'Direccion'     => 'TEGUCIGALPA',
//    'Resolucion'    => 'PCDTT-IHTT-T-001601-2024',
//    'ID_Estado'     => 'ES-01',
//    'Codigo_Censo'  => 'SOL002856877',
//    'Observaciones' => NULL,
//    'Numero_Registro' => '0801-TES-0028',
//    'Cod_Frecuencia_Salida'  => 'F021',
//    'Sistema_Usuario' => $_SESSION['user_name'],
//    // 'Sistema_Fecha' => NULL,
//    'Observacion_Cancelacion' => null,
//    'Usuario_Cancelacion'    => null,
//    'Fecha_Cancelacion'      => null,
//    'Comisionado_Gestion'    => 'rbarahona',
//    'Pre_Permiso'   => null,
//    'Pre_Estado'    => null,
//    'Pre_Fecha'     => '2025-07-09 10:46:12',
//    'Plan_Prorenova' => null,
//    'Usuario_Creacion'  => NULL,
//    'ID_Ruta'       => NULL,
//    'Comisionado_Gestion_Historico'   => null,
//    'Gestion_Masiva' => null,
//    'Reimpresion'   => NULL
// ];
// 
// insertConcesion($Datos, $tipo_transporte, $tipo_concesion, $db);

//******************************************************************************************/
//* INICIO: FUNCION ENCARGADA DE INSERTAR LOS CERIFICADOS DE CARGA, PERMISO ESPECIAL CARGA
//******************************************************************************************/
function insertConcesion($Datos, $tipo_transporte, $tipo_concesion, $db)
{
   $query = '';
   $params = [];

   try {
      //********************************** */
      //*INICIO: CUANDO ES DE CARGA
      //********************************** */
      if ($tipo_transporte == 'CARGA') {

         if ($tipo_concesion == 'CER') {
            //***************************************/
            //*INICIO: CERTIFICADO CARGA
            //***************************************/
            $query = 'INSERT INTO [IHTT_SGCERP].[dbo].[TB_Certificado_Carga]
            (N_Certificado, N_Certificado_Encrypted, N_Permiso_Explotacion, RTN_Concesionario, ID_Vehiculo_Carga, Resolucion, ID_Expediente, Fecha_Emision, Fecha_Elaboracion, Fecha_Expiracion, ID_Categoria, ID_Colegiacion, Numero_Registro, Direccion, ID_Estado, Codigo_Censo, Observaciones, Sistema_Usuario, Sistema_Fecha, Observacion_Cancelacion, Usuario_Cancelacion, Fecha_Cancelacion, Certificado_Anterior, ID_Tipo_Categoria, Comisionado_Gestion, Pre_Certificado, Pre_Estado, Pre_Fecha, Plan_Prorenova, Usuario_Creacion, Tipo_Generacion, URL_Constancia_Unidad, Reimpresion, Comisionado_Gestion_Historico, Gestion_Masiva)VALUES(:N_Certificado, :N_Certificado_Encrypted, :N_Permiso_Explotacion, :RTN_Concesionario, :ID_Vehiculo_Carga, :Resolucion, :ID_Expediente, :Fecha_Emision, :Fecha_Elaboracion, :Fecha_Expiracion, :ID_Categoria, :ID_Colegiacion, :Numero_Registro, :Direccion, :ID_Estado, :Codigo_Censo, :Observaciones, :Sistema_Usuario, SYSDATETIME(), :Observacion_Cancelacion, :Usuario_Cancelacion, :Fecha_Cancelacion, :Certificado_Anterior, :ID_Tipo_Categoria, :Comisionado_Gestion, :Pre_Certificado, :Pre_Estado, :Pre_Fecha, :Plan_Prorenova, :Usuario_Creacion, :Tipo_Generacion, :URL_Constancia_Unidad, :Reimpresion, :Comisionado_Gestion_Historico, :Gestion_Masiva)';

            $params = [
               ':N_Certificado' => $Datos['N_Certificado'],
               ':N_Certificado_Encrypted' => hash('md5', $Datos['N_Certificado']),
               ':N_Permiso_Explotacion' => $Datos['N_Permiso_Explotacion'],
               ':RTN_Concesionario' => $Datos['RTN_Concesionario'],
               ':Resolucion' => $Datos['Resolucion'],
               ':ID_Vehiculo_Carga' => $Datos['ID_Vehiculo_Carga'],
               ':ID_Expediente' => $Datos['ID_Expediente'],
               ':Fecha_Emision' => $Datos['Fecha_Emision'],
               ':Fecha_Elaboracion' => $Datos['Fecha_Elaboracion'],
               ':Fecha_Expiracion' => $Datos['Fecha_Expiracion'],
               ':ID_Categoria' => $Datos['ID_Categoria'],
               ':ID_Colegiacion' => $Datos['ID_Colegiacion'],
               ':Numero_Registro' => $Datos['Numero_Registro'],
               ':Direccion' => $Datos['Direccion'],
               ':ID_Estado' => $Datos['ID_Estado'],
               ':Codigo_Censo' => $Datos['Codigo_Censo'],
               ':Observaciones' => $Datos['Observaciones'],
               ':Sistema_Usuario' => $_SESSION['user_name'] ?? 'system',
               ':Observacion_Cancelacion' => $Datos['Observacion_Cancelacion'],
               ':Usuario_Cancelacion' => $Datos['Usuario_Cancelacion'],
               ':Fecha_Cancelacion' => $Datos['Fecha_Cancelacion'],
               ':Certificado_Anterior' => $Datos['Certificado_Anterior'],
               ':ID_Tipo_Categoria' => $Datos['ID_Tipo_Categoria'],
               ':Comisionado_Gestion' => $Datos['Comisionado_Gestion'],
               ':Pre_Certificado' => $Datos['Pre_Certificado'],
               ':Pre_Estado' => $Datos['Pre_Estado'],
               ':Pre_Fecha' => $Datos['Pre_Fecha'],
               ':Plan_Prorenova' => $Datos['Plan_Prorenova'],
               ':Usuario_Creacion' => $_SESSION['user_name'] ?? 'system',
               ':Tipo_Generacion' => $Datos['Tipo_Generacion'],
               ':URL_Constancia_Unidad' => $Datos['URL_Constancia_Unidad'],
               ':Reimpresion' => $Datos['Reimpresion'],
               ':Comisionado_Gestion_Historico' => $Datos['Comisionado_Gestion_Historico'],
               ':Gestion_Masiva' => $Datos['Gestion_Masiva']
            ];
            //***********************************************************************/
            //*FINAL: CERTIFICADO CARGA
            //*******************************************************************
         } else {
            //***********************************************************************/
            //* INICIO: PERMISO ESPECIAL CARGA
            //***********************************************************************/

            $query = 'INSERT INTO [IHTT_SGCERP].[dbo].[TB_Permiso_Especial_Carga](N_Permiso_Especial_Carga,N_Permiso_Especial_Carga_Encrypted,N_Certificado,RTN_Concesionario,ID_Vehiculo_Carga,Resolucion,ID_Expediente,Fecha_Emision,Fecha_Elaboracion,Fecha_Expiracion,ID_Categoria,ID_Colegiacion,Numero_Registro,Direccion,Codigo_Censo,Observaciones,ID_Estado,Sistema_Usuario,Sistema_Fecha,Observacion_Cancelacion,Usuario_Cancelacion,Fecha_Cancelacion,ID_Tipo_Categoria,Comisionado_Gestion,Pre_Permiso,Pre_Estado,Pre_Fecha,Plan_Prorenova,Usuario_Creacion,Codigo_Aduanero,Tipo_Generacion,URL_Constancia_Unidad,Reimpresion,Comisionado_Gestion_Historico,Gestion_Masiva)
            VALUES(:N_Permiso_Especial_Carga,:N_Permiso_Especial_Carga_Encrypted,:N_Certificado,:RTN_Concesionario, :ID_Vehiculo_Carga,:Resolucion, :ID_Expediente,:Fecha_Emision,:Fecha_Elaboracion,:Fecha_Expiracion,:ID_Categoria,:ID_Colegiacion, :Numero_Registro,:Direccion,:Codigo_Censo,:Observaciones,:ID_Estado,:Sistema_Usuario,SYSDATETIME(), :Observacion_Cancelacion,:Usuario_Cancelacion,:Fecha_Cancelacion,:ID_Tipo_Categoria,:Comisionado_Gestion, :Pre_Permiso,:Pre_Estado,:Pre_Fecha,:Plan_Prorenova,:Usuario_Creacion,:Codigo_Aduanero,:Tipo_Generacion, :URL_Constancia_Unidad,:Reimpresion,:Comisionado_Gestion_Historico,:Gestion_Masiva)';

            $params = [
               ':N_Permiso_Especial_Carga' => $Datos['N_Permiso_Especial_Carga'],
               ':N_Permiso_Especial_Carga_Encrypted' => hash('md5', $Datos['N_Permiso_Especial_Carga']),
               ':N_Certificado' => $Datos['N_Certificado'],
               ':RTN_Concesionario' => $Datos['RTN_Concesionario'],
               ':ID_Vehiculo_Carga' => $Datos['ID_Vehiculo_Carga'],
               ':Resolucion' => $Datos['Resolucion'],
               ':ID_Expediente' => $Datos['ID_Expediente'],
               ':Fecha_Emision' => $Datos['Fecha_Emision'],
               ':Fecha_Elaboracion' => $Datos['Fecha_Elaboracion'],
               ':Fecha_Expiracion' => $Datos['Fecha_Expiracion'],
               ':ID_Categoria' => $Datos['ID_Categoria'],
               ':ID_Colegiacion' => $Datos['ID_Colegiacion'],
               ':Numero_Registro' => $Datos['Numero_Registro'],
               ':Direccion' => $Datos['Direccion'],
               ':Codigo_Censo' => $Datos['Codigo_Censo'],
               ':Observaciones' => $Datos['Observaciones'],
               ':ID_Estado' => $Datos['ID_Estado'],
               ':Sistema_Usuario' => $_SESSION['user_name'] ?? 'system',
               ':Observacion_Cancelacion' => $Datos['Observacion_Cancelacion'],
               ':Usuario_Cancelacion' => $Datos['Usuario_Cancelacion'],
               ':Fecha_Cancelacion' => $Datos['Fecha_Cancelacion'],
               ':ID_Tipo_Categoria' => $Datos['ID_Tipo_Categoria'],
               ':Comisionado_Gestion' => $Datos['Comisionado_Gestion'],
               ':Pre_Permiso' => $Datos['Pre_Permiso'],
               ':Pre_Estado' => $Datos['Pre_Estado'],
               ':Pre_Fecha' => $Datos['Pre_Fecha'],
               ':Plan_Prorenova' => $Datos['Plan_Prorenova'],
               ':Usuario_Creacion' => $_SESSION['user_name'] ?? 'system',
               ':Codigo_Aduanero' => $Datos['Codigo_Aduanero'],
               ':Tipo_Generacion' => $Datos['Tipo_Generacion'],
               ':URL_Constancia_Unidad' => $Datos['URL_Constancia_Unidad'],
               ':Reimpresion' => $Datos['Reimpresion'],
               ':Comisionado_Gestion_Historico' => $Datos['Comisionado_Gestion_Historico'],
               ':Gestion_Masiva' => $Datos['Gestion_Masiva']
            ];
            //***********************************************************************/
            //* FINAL: PERMISO ESPECIAL CARGA
            //***********************************************************************/
         }
         //********************************** */
         //*FINAL: CUANDO ES DE CARGA
         //********************************** */
      } else {
         //***********************************************/
         //*INICIO: CERTIFICADO PASAJERO
         //***********************************************/
         if ($tipo_concesion == 'TES') { //CER
            //***********************************************************************/
            //*INICIO:CERTIFICADO PASAJERO
            //***********************************************************************/

            $query = 'INSERT INTO [IHTT_SGCERP].[dbo].[TB_Certificado_Pasajeros]
            (N_Certificado, N_Certificado_Encrypted, N_Permiso_Explotacion, RTN_Concesionario, ID_Vehiculo_Transporte,
            ID_Expediente, Fecha_Emision, Fecha_Elaboracion, Fecha_Expiracion, ID_Categoria, ID_Colegiacion, Direccion,
            Resolucion, ID_Ruta, ID_Estado, Codigo_Censo, Observaciones, Numero_Registro, Sistema_Usuario,Sistema_Fecha, Observacion_Cancelacion, Usuario_Cancelacion, Fecha_Cancelacion, Certificado_Anterior,Comisionado_Gestion, Pre_Certificado, Pre_Estado, Pre_Fecha, Usuario_Creacion, Plan_Prorenova,ID_Area_Operacion, Proyecto_Rosa, ID_SubCategoria, Comisionado_Gestion_Historico,Gestion_Masiva, Reimpresion)VALUES(:N_Permiso_Especial_Carga,:N_Certificado,:N_Certificado_Encrypted,:N_Permiso_Explotacion,:RTN_Concesionario,:ID_Vehiculo_Transporte,:ID_Expediente,:Fecha_Expiracion,:ID_Categoria,:ID_Colegiacion,:Direccion, :ID_Ruta,:ID_Estado,:Codigo_Censo,:Observaciones,:Numero_Registro,:Sistema_Usuario,:Observacion_Cancelacion,:Usuario_Cancelacion,:Certificado_Anterior,:Comisionado_Gestion, :Pre_Certificado,:Pre_Estado,:Pre_Fecha,:Usuario_Creacion,:Plan_Prorenova,:ID_Area_Operacion,:Proyecto_Rosa,:ID_SubCategoria,:Comisionado_Gestion_Historico,:Gestion_Masiva,:Reimpresion)';

            $params = [
               ':N_Certificado' => $Datos['N_Certificado'],
               ':N_Permiso_Especial_Carga' => hash('md5', $Datos['N_Permiso_Especial_Carga']),
               ':N_Certificado' => $Datos['Concesion'],
               ':N_Certificado_Encrypted' => $Datos['Concesion_Encrypted'],
               ':N_Permiso_Explotacion' => $Datos['Permiso_Explotacion'],
               ':RTN_Concesionario' => $Datos['RTN_Concesionario'],
               ':ID_Vehiculo_Transporte' => $Datos['ID_Vehiculo_Transporte'],
               ':ID_Expediente' => $Datos['ID_Expediente'],
               ':Fecha_Expiracion' => $Datos['Fecha_Expiracion'],
               ':ID_Categoria' => $Datos['ID_Categoria'],
               ':ID_Colegiacion' => $Datos['ID_Colegiacion'],
               ':Direccion' => $Datos['Direccion'],
               ':Resolucion' => $Datos['Resolucion'],
               ':ID_Ruta' => $Datos['ID_Ruta'],
               ':ID_Estado' => $Datos['ID_Estado'],
               ':Codigo_Censo' => $Datos['Codigo_Censo'],
               ':Observaciones' => $Datos['Observaciones'],
               ':Numero_Registro' => $Datos['Numero_Registro'],
               ':Sistema_Usuario' => $_SESSION['user_name'],
               ':Observacion_Cancelacion' => $Datos['Observacion_Cancelacion'],
               ':Usuario_Cancelacion' => $Datos['Usuario_Cancelacion'],
               ':Certificado_Anterior' => $Datos['Certificado_Anterior'],
               ':Comisionado_Gestion' => $Datos['Comisionado_Gestion'],
               ':Pre_Certificado' => $Datos['Pre_Certificado'],
               ':Pre_Estado' => $Datos['Pre_Estado'],
               ':Pre_Fecha' => $Datos['Pre_Fecha'],
               ':Usuario_Creacion' => $_SESSION['user_name'],
               ':Plan_Prorenova' => $Datos['Plan_Prorenova'],
               ':ID_Area_Operacion' => $Datos['ID_Area_Operacion'],
               ':Proyecto_Rosa' => $Datos['Proyecto_Rosa'],
               ':ID_SubCategoria' => $Datos['ID_SubCategoria'],
               ':Comisionado_Gestion_Historico' => $Datos['Comisionado_Gestion_Historico'],
               ':Gestion_Masiva' => $Datos['Gestion_Masiva'],
               ':Reimpresion' => $Datos['Reimpresion']
            ];
            //***********************************************/
            //* FINAL CERIFICADO PASAJERO
            //******************************************* */
         } else {
            //*********************************************
            //* INICIO: PERMISO ESPECIAL PASAJERO.
            //*********************************************

            $query = 'INSERT INTO [IHTT_SGCERP].[dbo].[TB_Permiso_Especial_Pas] (N_Permiso_Especial_Pas,N_Permiso_Especial_Pas_Encrypted,N_Certificado,RTN_Concesionario,ID_Departamento,ID_Vehiculo_Transporte,   ID_Expediente, Fecha_Emision,Fecha_Elaboracion,Fecha_Expiracion,ID_Categoria,ID_Colegiacion,Direccion,Resolucion,ID_Estado,Codigo_Censo,Observaciones,Numero_Registro,Cod_Frecuencia_Salida,Sistema_Usuario,Sistema_Fecha,Observacion_Cancelacion,Usuario_Cancelacion,Fecha_Cancelacion,Comisionado_Gestion,Pre_Permiso,Pre_Estado,Pre_Fecha,Plan_Prorenova,Usuario_Creacion,ID_Ruta,Comisionado_Gestion_Historico,Gestion_Masiva,   Reimpresion)VALUES (:N_Permiso_Especial_Pas, :N_Permiso_Especial_Pas_Encrypted, :N_Certificado, :RTN_Concesionario, :ID_Departamento, :ID_Vehiculo_Transporte, :ID_Expediente,:Fecha_Emision,:Fecha_Elaboracion, :Fecha_Expiracion, :ID_Categoria, :ID_Colegiacion, :Direccion, :Resolucion, :ID_Estado, :Codigo_Censo, :Observaciones, :Numero_Registro, :Cod_Frecuencia_Salida, :Sistema_Usuario, SYSDATETIME(), :Observacion_Cancelacion, :Usuario_Cancelacion, :Fecha_Cancelacion, :Comisionado_Gestion, :Pre_Permiso, :Pre_Estado, :Pre_Fecha, :Plan_Prorenova, :Usuario_Creacion, :ID_Ruta, :Comisionado_Gestion_Historico, :Gestion_Masiva, :Reimpresion)';

            $params =
               [
                  ':N_Permiso_Especial_Pas' => $Datos['N_Permiso_Especial_Pas'],
                  ':N_Permiso_Especial_Pas_Encrypted' => hash('md5', $Datos['N_Permiso_Especial_Pas']),
                  ':N_Certificado' => $Datos['N_Certificado'],
                  ':RTN_Concesionario' => $Datos['RTN_Concesionario'],
                  ':ID_Departamento' => $Datos['ID_Departamento'],
                  ':ID_Vehiculo_Transporte' => $Datos['ID_Vehiculo_Transporte'],
                  ':ID_Expediente' => $Datos['ID_Expediente'],
                  ':Fecha_Emision' => $Datos['Fecha_Emision'],
                  ':Fecha_Elaboracion' => $Datos['Fecha_Elaboracion'],
                  ':Fecha_Expiracion' => $Datos['Fecha_Expiracion'],
                  ':ID_Categoria' => $Datos['ID_Categoria'],
                  ':ID_Colegiacion' => $Datos['ID_Colegiacion'],
                  ':Direccion'   => $Datos['Direccion'],
                  ':Resolucion'  => $Datos['Resolucion'],
                  ':ID_Estado'   => $Datos['ID_Estado'],
                  ':Codigo_Censo' => $Datos['Codigo_Censo'],
                  ':Observaciones'  => $Datos['Observaciones'],
                  ':Numero_Registro' => $Datos['Numero_Registro'],
                  ':Cod_Frecuencia_Salida' => $Datos['Cod_Frecuencia_Salida'],
                  ':Sistema_Usuario'  => $_SESSION['user_name'],
                  // ':Sistema_Fecha'    => $Datos['Sistema_Fecha'],
                  ':Observacion_Cancelacion' => $Datos['Observacion_Cancelacion'],
                  ':Usuario_Cancelacion'   => $Datos['Usuario_Cancelacion'],
                  ':Fecha_Cancelacion'  => $Datos['Fecha_Cancelacion'],
                  ':Comisionado_Gestion'   => $Datos['Comisionado_Gestion'],
                  ':Pre_Permiso' => $Datos['Pre_Permiso'],
                  ':Pre_Estado'  => $Datos['Pre_Estado'],
                  ':Pre_Fecha'   => $Datos['Pre_Fecha'],
                  ':Plan_Prorenova' => $Datos['Plan_Prorenova'],
                  ':Usuario_Creacion' => $_SESSION['user_name'],
                  ':ID_Ruta'  => $Datos['ID_Ruta'],
                  ':Comisionado_Gestion_Historico' => $Datos['Comisionado_Gestion_Historico'],
                  ':Gestion_Masiva' => $Datos['Gestion_Masiva'],
                  ':Reimpresion' => $Datos['Reimpresion']
               ];
            //***********************************************************
            //* FINAL: PERMISO ESPECIAL PASAJERO.
            //********************************************************
         }
      }

      $respuesta = insert($query, $params, $db);

      if ($respuesta) {
         echo json_encode(['success' => 1, 'message' => 'Registro insertado correctamente en insertConcesion ' . $tipo_transporte . ' ' . $tipo_concesion]);
      } else {
         echo json_encode(['success' => 0, 'message' => 'Error al insertar el registro en insertConcesion ' . $tipo_transporte . ' ' . $tipo_concesion]);
      }
   } catch (PDOException $e) {
      echo json_encode(['success' => 0, 'message en insertConcesion' => 'PDOException: ' . $e->getMessage()]);
   }
}

function insert($q, $p, $db)
{
   $stmt = $db->prepare($q);
   if ($stmt->execute($p)) {
      return true;
   } else {
      return false;
   }
}