<div class="table-responsive bg_botones_lista gy_barra_botones">
	<button class="btn btn-info btn-sm btn-custom-secondary" id="cmd_salvar" name="cmd_salvar" onclick="window.location='<?PHP echo $pantalla[0] . '.php?';?>'">
		<span class="hidden-xs"><strong><i class="fa-solid fa-circle-plus"></i><strong>Nuevo</strong></strong></span>
	</button>
</div>
<?PHP  if ($total_pages >= 1 ) { ?>
		<div width="100%" class="btn-group bg_paginacion_lista" role="group" aria-label="Button group with nested dropdown">
	<?PHP  	
		for ($i=1; $i <= $total_pages; $i++ ) { ?>    
			<?PHP  if ($pageno == $i ) { ?>
				<button onclick="window.location='<?PHP echo $pantalla[0] . '_lista.php?pageno=' . $i ;?>'" type="button" class="btn btn-info"><?PHP  echo $i; ?></button>
			<?PHP } else {   ?>
			<button onclick="window.location='<?PHP echo $pantalla[0] . '_lista.php?pageno=' . $i ;?>'" type="button" class="btn btn-light"><?PHP  echo $i; ?></button>
			<?PHP } ?>	
	<?PHP } ?>	
	<div class="btn-group" role="group">
		<button id="btnGroupDrop1" type="button" class="btn btn-light dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<?PHP echo $no_of_records_per_page; ?>	      
		</button>
		<div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
	<a class="dropdown-item" href="<?PHP echo $pantalla[0] . '_lista.php?pageno=' . 1;?>&no_of_records_per_page=10">10</a>	
		<a class="dropdown-item" href="<?PHP echo $pantalla[0] . '_lista.php?pageno=' . 1;?>&no_of_records_per_page=20">20</a>
		<a class="dropdown-item" href="<?PHP echo $pantalla[0] . '_lista.php?pageno=' . 1;?>&no_of_records_per_page=30">30</a>
		<a class="dropdown-item" href="<?PHP echo $pantalla[0] . '_lista.php?pageno=' . 1;?>&no_of_records_per_page=50">50</a>
		<a class="dropdown-item" href="<?PHP echo $pantalla[0] . '_lista.php?pageno=' . 1;?>&no_of_records_per_page=100">100</a>
		</div>
	</div></div>	
	<?PHP } ?>    
	</div>					
	<?PHP 
	if (isset($_GET['msg']) and $_GET['msg'] <> "")  {
	?>    
	<div id="msg-global" class="alert alert-success alert-dismissible fade show"  role="alert">
		<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
		<div id="msg-error">  
			<?PHP echo $_GET['msg'];?>
		</div>
	</div>
<?PHP  } ?>