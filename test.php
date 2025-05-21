<?php
ob_start();
ob_end_flush();
flush();
ob_flush();
// Establecer la hora de inicio
$startTime = time();
// Establecer la hora de fin (3 minutos después de la hora de inicio)
$endTime = $startTime + 3 * 60; // 3 minutos en segundos
// Deshabilitar el almacenamiento en búfer de salida (si es necesario)
// Ciclo que imprime la hora actual hasta 3 minutos después de la hora de inicio
while (time() < $endTime) {
    // Imprime la hora actual en formato de 24 horas (HH:MM:SS)
    echo "Hora actual: " . date("H:i:s") . "\n" . '</br>';
    // Forzar el vaciado del búfer de salida y enviar el contenido inmediatamente
    flush();
    ob_flush();
    // Espera 1 segundo antes de la siguiente impresión
    sleep(1);
}
ob_end_flush();
?>
