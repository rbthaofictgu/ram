<?PHP if (isset($_SESSION['hash_privado'])==false) {?>
<div id="botones" class="table-responsive gy_barra_botones">
 <table width="100%" align="center">
    <tr>
     <td width="96%">
       <div  align="left">
	     &nbsp;&nbsp;

       <span id="boton_salvar">
		   
        <button type="submit" class="btn btn-info btn-sm btn-custom-primary" id="cmd_salvar" name="cmd_salvar" onclick="return f_validar(this)">
            <i class="fa-solid fa-floppy-disk"></i>
            <span class="hidden-xs"><strong>Salvar</strong></span>
        </button>
		   <?PHP if (isset($body_class) and $body_class == "desktop") {?>        

		<button type="submit" class="btn btn-light btn-sm btn-custom-secondary" id="cmd_salvar_close" name="cmd_salvar_close" onclick="return f_validar(this)">
            <i class="fa-solid fa-check"></i>
            <span class="hidden-xs"><strong>Salvar & Cerrar</strong></span>
        </button>

		<button type="submit" class="btn btn-light btn-sm btn-custom-secondary" id="cmd_salvar_nuevo" name="cmd_salvar_nuevo" onclick="return f_validar(this)">
            <i class="fa-solid fa-circle-plus"></i>
            <span class="hidden-xs"><strong>Salvar & Nuevo</strong></span>
        </button>
        </span>

		<button type="submit" class="btn btn-light btn-sm btn-custom-secondary" id="cmd_salvar_copy" name="cmd_salvar_copy" onclick="return f_validar(this)">
            <i class="fa-solid fa-copy"></i>
            <span class="hidden-xs"><strong>Salvar & Copy</strong></span>
        </button>
 	
</span>
		   
		<?PHP if (isset($rs_id_rs_mantenimiento) and $rs_id_rs_mantenimiento > -1) {?>        
        
        <button type="button" class="btn btn-success btn-sm btn-custom-primary" id="cmd_refrescar" name="cmd_refrescar" onclick="window.location='<?PHP echo $currentPage;?>?id=<?PHP echo $rs_id_rs_mantenimiento ;?>'">
            <i class="fa-solid fa-arrows-rotate"></i>
            <span class="hidden-xs"><strong>Refrescar</strong></span>
        </button>
        
		<?PHP if ($botones == 'U') {?>   
		   
        <button type="button" class="btn btn-secondary btn-sm btn-custom-secondary" id="cmd_tipo" name="cmd_tipo" onclick="window.location='privilegiosxusuario.php?id=<?PHP echo $rs_id_rs_mantenimiento;?>'">
            <i class="fa-solid fa-user-gear"></i>
            <span class="hidden-xs"><strong>Privilegio por Usuario</strong></span>
        </button>
        
        <?PHp  } } 
		   ?>

        <button type="button" class="btn btn-info btn-sm btn-custom-primary" id="cmd_cancelar" name="cmd_cancelar" onclick="window.location='<?PHP echo $currentPage;?>'">
            <i class="fa-regular fa-circle-check"></i>
            <span class="hidden-xs"><strong>Limpiar</strong></span>
        </button>

		    <?PHP  }
		   ?>
        <button type="button" class="btn btn-danger btn-sm btn-custom-secondary" id="cmd_retornar" name="cmd_retornar" onclick="window.location='<?PHP echo $pantalla[0];?>_lista.php'">
            <i class="fa-solid fa-rectangle-xmark"></i>
            <span class="hidden-xs"><strong>Cerrar</strong></span>
        </button>
		   
		</div>

		 
		</td>
		 
		 <td width="2%">
			 <?PHP 
			 if ($rs_id_rs_mantenimiento <> -1 and !isset($nobuttomnext)) {   
				 
			 	$idnext = recuperar_anterior($tablanext,  $rs_id_rs_mantenimiento ,$conn);
			 ?>
			 <?PHP if ($idnext <> -1) { ?>
				<div align="right">	 
				 <button type="button" class="btn btn-light" id="cmd_refrescar" name="cmd_refrescar" onclick="window.location='<?PHP echo $currentPage;?>?id=<?PHP echo $idnext ;?>'">
                    <i class="fa-solid fa-arrow-left"></i>
				</button>
					
			 <?PHP }} ?>
		</td>
		 <td width="2%">
			 <?PHP 
			 if ($rs_id_rs_mantenimiento <> -1 and !isset($nobuttomnext)) {   
			 ?>
			 <?PHP $idnext = recuperar_siguiente($tablanext, $rs_id_rs_mantenimiento ,$conn) ;?>
				<?PHP if ($idnext <> -1) { ?>	
				 <button type="button" id="cmd_refrescar" class="btn btn-light" name="cmd_refrescar" onclick="window.location='<?PHP echo $currentPage;?>?id=<?PHP echo $idnext ;?>'">
                    <i class="fa-solid fa-arrow-right"></i>
				</button>
			 <?PHP }} ?>
		</td>

	</tr>
   </table>
</div>
<?PHP } else { ?>
<div id="botones" class="table-responsive bg_botones">
 <table width="100%" align="center">
    <tr height="40px" class="gy_barra_botones">
     <td width="90%">
       <div  align="left">
	     &nbsp;&nbsp;

<span id="boton_salvar">
		<button type="submit" class="btn btn-light btn-sm" id="cmd_salvar_close" name="cmd_salvar_close" onclick="return f_validar(this)">
        <i class="fa-solid fa-floppy-disk"></i>
            <span class="hidden-xs">Salvar y Activar</span>
        </button>
</span>
		   

		 </div>

		 
		</td>
		 
		 <td width="10%">
		</td>
    </tr>
   </table>
</div>
<?PHP } ?>