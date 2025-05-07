<script>
var limite_logout = <?PHP echo $appcfg_max_time_logout;?>;
var d_logout = new Date();
var n_logout = d_logout.getTime(); 
var myVar = setInterval(function(){myTimer(limite_logout)},limite_logout);
	
function myTimer(f_limite) {
    var dd = new Date();
	var nn = dd.getTime(); 
    if ((nn - n_logout) > f_limite) {
		window.clearInterval(myVar);
		location.href='<?PHP echo $Dominio;?>salir.php?flag=A&inactividad='+(nn - n_logout);
	}
}	
</script>	