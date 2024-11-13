<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
	<div class="modal-header logo_bg">
		<h5><strong class="gy_link_navbar_hover"><i class="fad fa-lock-alt fa-2x gy_link_navbar_hover"></i>Bloqueado</strong></h5>
		<i data-dismiss="modal" aria-label="Close" aria-hidden="true" class="fad fa-window-close fa-2x gy_link_navbar_hover"></i>
	</div>
	<div class="modal-body">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-12">
					<div id="ajax_msg" role="alert"></div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-lg-12">
					<div class="form-label-group">
					<input autofocus="autofocus" max="200" type="email" id="txt_usuario_desbloqueo" name="txt_usuario_desbloqueo" class="form-control" placeholder="Correo Electrónico">
					<label for="txt_usuario_desbloqueo">Correo Electrónico</label>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-12">
					 <div class="form-label-group">
						<input autofocus="autofocus" max="30" type="password" id="txt_clave_desbloqueo" name="txt_clave_desbloqueo" class="form-control" placeholder="Clave">
						<label for="txt_usuario_desbloqueo">Clave</label>
					  </div>
				  </div>
				</div>
			
		</div>			
      </div>      
		
		<div class="modal-footer">
			<div class="container-fluid text-center">			
					<div class="col">
						<button type="button" class="btn btn-custom-secondary w-100 mx-auto" onClick="desbloquear();">
							<i class="fad fa-unlock-alt"></i>
							<span class="hidden-xs"><strong>Desbloquear</strong></span>	
						</button>
					</div>							
			</div> 
      </div>
    </div>
  </div>
</div>