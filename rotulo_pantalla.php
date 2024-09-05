<!-- Solo presentar el loading cuando el página a cargar no es tipo inicio -->
<?PHP if (!isset($inicio)) { ?>
<div style="background:#222;" id="loader" class="loading">
  <div class="dot"></div>
  <div class="dot"></div>
  <div class="dot"></div>
  <div class="dot"></div>
  <div class="dot"></div>
</div>	
<?PHP } ?>
<div class="table-responsive gy_rotulo_pantalla">
<table width="100%">
<tr>
<th height="30px" width="96%"><h6><strong>&nbsp;&nbsp;&nbsp;&nbsp;<i class="far fa-edit"></i>&nbsp;&nbsp;&nbsp;<?php echo strtoupper($rotulo); ?></strong><?PHP if (isset($logorotulo) and $logorotulo <> '') {echo $logorotulo;}?></h6></th>
<th height="30px" align="center" width="4%"> 
<!-- Button HTML (to Trigger Modal) de Bloqueo -->
<?php if (isset($_SESSION['tipo']) and $_SESSION['tipo']) { ?>	
<button title="Bloquear Sistema" type="button" id="modal" class="btn btn-warning" name="cmd_retornar">
  <i class="fa-solid fa-lock"></i>
</button>
<?PHP } ?>	
</th>
</tr>
</table>
</div>