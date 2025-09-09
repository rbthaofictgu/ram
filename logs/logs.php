<?php header('Content-Type: text/html; charset=utf-8');?>
<?PHP
function logErr($data,$file){
  $mode = (!file_exists($file)) ? 'w':'a';
  $logfile = fopen($file, $mode);
  fwrite($logfile, "\r\n". $data);
  fclose($logfile);
}
?>