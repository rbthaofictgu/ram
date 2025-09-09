<?php
setlocale(LC_ALL,"es_ES@euro","es_ES","esp" );
//error_reporting(0);
header('Content-Type: application/x-javascript; charset=utf-8');
header('Access-Control-Allow-Origin: *');
session_start();
ini_set('post_max_size', '100M');
ini_set('upload_max_filesize', '100M');
ini_set('max_execution_time', '10000');
ini_set('max_input_time', '10000');
ini_set('memory_limit', '256M');
date_default_timezone_set("America/Tegucigalpa");
/**************************************************************************************************/
// Autoload de las librerias
/*************************************************************************************************/	
require_once('../../vendor/autoload.php');
//*configuración del las variables globales del sistema
include_once('configuracion/configuracion.php');
require_once("../config/conexion.php");
require_once("../logs/logs.php");
//* Clase para procesamiento de archivos Excel
use PhpOffice\PhpSpreadsheet\IOFactory;
$archivoExcel = 'ruta/del/archivo.xlsx';
// Cargar el archivo
$spreadsheet = IOFactory::load($archivoExcel);
// Obtener el número total de hojas
$totalHojas = $spreadsheet->getSheetCount();
echo "Este archivo tiene $totalHojas hojas:\n\n";
// Recorrer todas las hojas
for ($i = 0; $i < $totalHojas; $i++) {
    $hoja = $spreadsheet->getSheet($i);
    $nombreHoja = $hoja->getTitle();
    echo "Procesando hoja #$i: $nombreHoja\n";
    $datos = $hoja->toArray(null, true, true, true); // Devuelve array asociativo
    foreach ($datos as $filaNumero => $fila) {
        echo "Fila $filaNumero: ";
        print_r($fila); // Puedes procesar como quieras
    }
    echo "\n\n";
}