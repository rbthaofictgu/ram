<?php 
session_start();

require_once('mpdf.php');
include_once('../NumerosALetras.php');
include_once('../config/conexion.php');

$q = "SELECT dbo.[TB_Denuncia].[Sistema_Fecha], DAY(Sistema_Fecha) AS DIA, MONTH(Sistema_Fecha) AS MES, YEAR(Sistema_Fecha) AS ANIO, dbo.[TB_Denuncia].[ID_Denuncia], dbo.[TB_Denuncia].[ID_Tipo_Denuncia], IHTT_DB.dbo.TB_Solicitante.NombreSolicitante AS [Nombre_Denunciante], IHTT_DB.dbo.TB_Solicitante.IDSolicitante AS [ID_Denunciante], IHTT_DB.dbo.TB_Solicitante.Direccion AS [Direccion_Denunciante], IHTT_DB.dbo.TB_Solicitante.Telefono AS [Tel_Celular_Denunciante], IHTT_DB.dbo.TB_Solicitante.Email AS [Correo_Denunciante], dbo.[TB_Denuncia].[Desc_Denuncia], dbo.[TB_Denuncia].[Sistema_Usuario], IHTT_DB.dbo.TB_Apoderado_Legal.Nombre_Apoderado_Legal FROM dbo.[TB_Denuncia] JOIN dbo.[TB_Tipo_Persona] ON dbo.[TB_Denuncia].[ID_Tipo_Persona] = dbo.[TB_Tipo_Persona].[ID_Tipo_Persona] JOIN IHTT_DB.dbo.TB_Solicitante ON dbo.TB_Denuncia.ID_Denunciante = IHTT_DB.dbo.TB_Solicitante.IDSolicitante JOIN IHTT_DB.dbo.TB_Apoderado_Legal ON dbo.TB_Denuncia.Apoderado = IHTT_DB.dbo.TB_Apoderado_Legal.ID_ColegiacionAPL WHERE ID_Denuncia = :ID";
        $p = array(':ID'=>$_GET['Denuncia']);
     $datos = select($q,$p);


function select($q, $p) {
    global $dbd;
    $stmt = $dbd->prepare($q);
    $stmt->execute( $p ) or die(print_r($stmt->errorInfo(), true));
    $datos = $stmt->fetchAll();
    return $datos;
    
  }








function MES($mes){
   if ($mes==1) {
      return 'Enero';
   }elseif ($mes==2) {
      return 'Febrero';
   }elseif ($mes==2) {
      return 'Febrero';
   }elseif ($mes==3) {
      return 'Marzo';
   }elseif ($mes==4) {
      return 'Abril';
   }elseif ($mes==5) {
      return 'Mayo';
   }elseif ($mes==6) {
      return 'Junio';
   }elseif ($mes==7) {
      return 'Julio';
   }elseif ($mes==8) {
      return 'Agosto';
   }elseif ($mes==9) {
      return 'Septiembre';
   }elseif ($mes==10) {
      return 'Octubre';
   }elseif ($mes==11) {
      return 'Noviembre';
   }elseif ($mes==12) {
      return 'Diciembre';
   }
}





$mpdf = new mPDF();

$mpdf->SetHeader('|<div class="imagencenter" ><img class="imagen" src="../assets/images/newlogoihtt.png" alt=""></div>|');

$mpdf->SetFooter('Generado por SIDEN IHTT| Usuario: '.$_SESSION['user_name'].' |Fecha: '.date('d-m-Y').'  Pagina: {PAGENO} ');

$stylesheet = file_get_contents('mpdf.css');
$mpdf->SetTitle('Auto de Denuncia');

$mpdf->WriteHTML($stylesheet,1);

//$mpdf->WriteHTML('<div class="imagencenter" ><img class="imagen" src="../assets/images/newlogoihtt.png" alt=""></div>',0);

$mpdf->WriteHTML('<br><br><br><div class="text"><p ALIGN="justify"><B>INSTITUTO HONDUREÑO DEL TRANSPORTE TERRESTRE. -INSPECTORÍA GENERAL DEL TRANSPORTE. </B> Tegucigalpa, Municipio del Distrito Central, a los'.valorEnLetras($datos[0]['DIA']).' días del mes  '.MES($datos[0]['MES']).' del año '.valorEnLetras($datos[0]['ANIO']).'.<br><br>
    Admítase  la  denuncia  que antecede junto con los documentos acompañados a la misma, presentada por <B>'.$datos[0]['Nombre_Denunciante'].'</B> '.$Apoderado.' contentiva en la denuncia que antecede, consecuentemente procédase a realizar las diligencias investigativas sobre los extremos y hechos denunciados, a efecto de establecer la procedencia de la misma, así como la competencia de éste órgano para seguir conociéndola, tramitándola y pronunciarse sobre la misma, siempre que del hecho denunciado se determine que su resolución está regulado o sea objeto de sanción por este Instituto.  Artículos 60, 61, 62, 63 de la Ley de Procedimiento Administrativo; 4, 5 numeral 16 párrafo primero, 17 numerales 4), 6) y, 7) de la Ley de Transporte Terrestre de Honduras, y, 14, y, 16 numerales 1), 2), 18 numeral  2) entre otras del Reglamento General de la Ley de Transporte Terrestre de Honduras. <B>CÚMPLASE</B></div>', 2);

$mpdf->WriteHTML('<div class="centertext"><p>JOSE ANA LOBO ROMERO</p>  <p>Inspector General</p>  <p>Inspectoría General del Transporte Terrestre</p> <p>Instituto Hondureño del Transporte Terrestre
</p><div>',3);

$mpdf->Output();



?>