<div class="carrental-wrapper">
	
	<?php include CARRENTAL__PLUGIN_DIR . 'views/header.php'; ?>
	
	<div class="row">
		<div class="col-md-12 carrental-main-wrapper">
			<div class="carrental-main-content">
				
				<?php include CARRENTAL__PLUGIN_DIR . 'views/flash_msg.php'; ?>
				
				<div class="row">
					<div class="col-md-12">
					
						<?php if ($edit == true) { ?>
							<h3>Editando Reserva: #<?= $detail['info']->id_order ?></h3>
							<a href="<?= esc_url(home_url('/')); ?>?page=carrental&summary=<?= $detail['info']->hash ?>" target="_blank" class="btn btn-info btn-xs">Mostrar Link de confirmación</a>
						<?php } else { ?>
							<?php if (isset($_GET['deleted'])) { ?>
								<a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-booking')); ?>" class="btn btn-default" style="float:right;">Mostrar Normal</a>
							<?php } else { ?>
								<a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-booking')); ?>&amp;deleted" class="btn btn-default" style="float:right;">Mostrar Achivados</a>
							<?php } ?>
							
							<a href="javascript:void(0);" class="btn btn-success" id="carrental-booking-add-button"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Agregar Nueva Reserva</a>
							
						<?php } ?>
						
						<div id="<?= (($edit == true) ? 'carrental-booking-edit-form' : 'carrental-booking-add-form') ?>" class="carrental-add-form">
							<form action="" method="post" role="form" class="form-horizontal">
								
								<div class="row">
									<div class="col-md-11">
										
										<!-- Enter date //-->
									  <div class="form-group">
									    <label for="carrental-enter-date" class="col-sm-3 control-label">Entrega - Fecha y Hora</label>
									    <div class="col-sm-6">
									    	<input type="text" name="enter_date" class="form-control pricing_datepicker" id="carrental-enter-date" value="<?= (($edit == true) ? Date('Y-m-d', strtotime($detail['info']->enter_date)) : '') ?>">
									    </div>
									    <div class="col-sm-3">
									    	<input type="text" name="enter_date_hour" class="form-control" placeholder="12:00" value="<?= (($edit == true) ? Date('H:i', strtotime($detail['info']->enter_date)) : '') ?>">
									    </div>
									  </div>
									  
										<!-- Enter location //-->
									  <div class="form-group">
									    <label for="carrental-enter-location" class="col-sm-3 control-label">Ubicación de Entrega</label>
									    <div class="col-sm-9">
									    	<select name="id_enter_branch" class="form-control">
										    	<option value="0">- Ninguno -</option>
										    	<?php if ($branches && !empty($branches)) { ?>
													<?php $enter_branch = null;?>
										    		<?php foreach ($branches as $key => $val) { ?>
														<?php if ($edit == true && $detail['info']->enter_loc == $val->name) {$enter_branch = $val;} ?>
										    			<option value="<?= $val->id_branch ?>" <?= (($edit == true && ($detail['info']->enter_loc == $val->name || $detail['info']->id_enter_branch == $val->id_branch)) ? 'selected="selected"' : '') ?>><?= $val->name ?></option>
										    		<?php } ?>
										    	<?php } ?>
									    	</select>
									    </div>
									  </div>
										
										<!-- Return date //-->
									  <div class="form-group">
									    <label for="carrental-return-date" class="col-sm-3 control-label">Retorno - Fecha y Hora</label>
									    <div class="col-sm-6">
									    	<input type="text" name="return_date" class="form-control pricing_datepicker" id="carrental-return-date" value="<?= (($edit == true) ? Date('Y-m-d', strtotime($detail['info']->return_date)) : '') ?>">
									    </div>
									    <div class="col-sm-3">
									    	<input type="text" name="return_date_hour" class="form-control" placeholder="12:00" value="<?= (($edit == true) ? Date('H:i', strtotime($detail['info']->return_date)) : '') ?>">
									    </div>
									  </div>
									  
										<!-- Return location //-->
									  <div class="form-group">
									    <label for="carrental-enter-location" class="col-sm-3 control-label">Ubicación de Retorno</label>
									    <div class="col-sm-9">
									    	<select name="id_return_branch" class="form-control">
										    	<option value="0">- Ninguno -</option>
										    	<?php if ($branches && !empty($branches)) { ?>
										    		<?php foreach ($branches as $key => $val) { ?>
										    			<option value="<?= $val->id_branch ?>" <?= (($edit == true && ($detail['info']->return_loc == $val->name || $detail['info']->id_return_branch == $val->id_branch)) ? 'selected="selected"' : '') ?>><?= $val->name ?></option>
										    		<?php } ?>
										    	<?php } ?>
									    	</select>
									    </div>
									  </div>
										
										<!-- Return location //-->
									  <div class="form-group">
									    <label for="carrental-enter-location" class="col-sm-3 control-label">Vehículo</label>
									    <div class="col-sm-4">
									    	<?php if (!empty($detail['info']->vehicle_picture)) { ?>
													<img src="<?= $detail['info']->vehicle_picture ?>" height="60">
													&nbsp;
												<?php } ?>
												<h4><?= $detail['info']->vehicle ?></h4>
												
											</div>
									    <div class="col-sm-5">
									    	<select name="change_vehicle" class="form-control">
									    		<?php if ($edit == true) { ?>
										    		<option value="0">No cambiar vehículo</option>
										    	<?php } else { ?>
										    		<option value="0">- Seleccionar Vehículo -</option>
										    	<?php } ?>
										    	
										    	<?php if ($fleet && !empty($fleet)) { ?>
										    		<?php foreach ($fleet as $key => $val) { ?>
										    			<option value="<?= $val->id_fleet ?>"><?= $val->name ?></option>
										    		<?php } ?>
										    	<?php } ?>
									    	</select>
									    </div>
									  </div>
										
									<!-- Order status //-->
									  <div class="form-group">
									    <label for="carrental-status" class="col-sm-3 control-label">Estado de la Reserva</label>
									    <div class="col-sm-3">
									    	<select name="status" class="form-control">
												<?php foreach (CarRental_Admin::$booking_statuses as $k => $v) {?>
													<option value="<?php echo $k;?>"<?php echo $k == $detail['info']->status ? ' selected="selected"' : '';?>><?php echo $v;?></option>
												<?php } ?>
									    	</select>
									    </div>
										<div class="col-sm-6">
											<?php if ($edit == true) { ?>
									  			<button type="submit" class="btn btn-warning" name="add_booking"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Guarde toda la reserva</button>
												<button type="submit" class="btn btn-warning" name="add_booking_emails"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Guarde toda la reserva y envíe el estado por Email</button>
									  		<?php } ?>
									    </div>
									  </div>
									
									 <!-- Paid online //-->
									  <div class="form-group">
									    <label for="carrental-paid-online" class="col-sm-3 control-label">Pago en Linea</label>
									    <div class="col-sm-1">
									    	<input type="text" name="paid_online" class="form-control" id="carrental-paid-online" value="<?= (($edit == true) ? $detail['info']->paid_online : '0') ?>">
									    </div>
										<div class="col-sm-1">
											<?= (($edit == true) ? $detail['info']->currency : '-') ?>
										</div>
									  </div>
										
									  <div class="form-group">
									  	<div class="col-sm-3"></div>
									    <div class="col-sm-9">
									    	<h3>Detalles del Conductor</h3>
									    </div>
									  </div>
									  
									  <!-- First name //-->
									  <div class="form-group">
									    <label for="carrental-first-name" class="col-sm-3 control-label">Nombre</label>
									    <div class="col-sm-9">
									    	<input type="text" name="first_name" class="form-control" id="carrental-first-name" value="<?= (($edit == true) ? $detail['info']->first_name : '') ?>">
									    </div>
									  </div>
									  
									  <!-- Last name //-->
									  <div class="form-group">
									    <label for="carrental-last-name" class="col-sm-3 control-label">Apellido</label>
									    <div class="col-sm-9">
									    	<input type="text" name="last_name" class="form-control" id="carrental-last-name" value="<?= (($edit == true) ? $detail['info']->last_name : '') ?>">
									    </div>
									  </div>
									  
									  <!-- Contact e-mail //-->
									  <div class="form-group">
									    <label for="carrental-email" class="col-sm-3 control-label">Email</label>
									    <div class="col-sm-9">
									    	<input type="text" name="email" class="form-control" id="carrental-email" value="<?= (($edit == true) ? $detail['info']->email : '') ?>">
									    </div>
									  </div>
									  
									  <!-- Contact phone //-->
									  <div class="form-group">
									    <label for="carrental-phone" class="col-sm-3 control-label">Telefono</label>
									    <div class="col-sm-9">
									    	<input type="text" name="phone" class="form-control" id="carrental-phone" value="<?= (($edit == true) ? $detail['info']->phone : '') ?>">
									    </div>
									  </div>
									  
									  
									  <!-- Street //-->
									  <div class="form-group">
									    <label for="carrental-street" class="col-sm-3 control-label">Dirección</label>
									    <div class="col-sm-9">
									    	<input type="text" name="street" class="form-control" id="carrental-street" value="<?= (($edit == true) ? $detail['info']->street : '') ?>">
									    </div>
									  </div>
									 	
									 	<!-- City //-->
									  <div class="form-group">
									    <label for="carrental-city" class="col-sm-3 control-label">Ciudad</label>
									    <div class="col-sm-9">
									    	<input type="text" name="city" class="form-control" id="carrental-city" placeholder="Selecciona una ciudad" value="<?= (($edit == true) ? $detail['info']->city : '') ?>">
									    </div>
									  </div>
									  
									  <!-- ZIP //-->
									  <div class="form-group">
									    <label for="carrental-zip" class="col-sm-3 control-label">ZIP Code</label>
									    <div class="col-sm-9">
									    	<input type="text" name="zip" class="form-control" id="carrental-zip" value="<?= (($edit == true) ? $detail['info']->zip : '') ?>">
									    </div>
									  </div>
									  
									  <!-- Country //-->
									  <div class="form-group">
									    <label for="carrental-country" class="col-sm-3 control-label">País</label>
									    <div class="col-sm-9">
									    	<select name="country" class="form-control" id="carrental-country">
										    	<option value="">- Seleccionar -</option>
										    	<?php $countries = CarRental_Admin::get_country_list(); ?>
										    	<?php foreach ($countries as $key => $val) { ?>
										    		<option value="<?= $key ?>" <?= (($edit == true && $key == $detail['info']->country) ? 'selected="selected"' : '') ?>><?= $val ?></option>
										    	<?php } ?>
									    	</select>
									    </div>
									  </div>
									  
									  <!-- Company name //-->
									  <div class="form-group">
									    <label for="carrental-company" class="col-sm-3 control-label">Empresa</label>
									    <div class="col-sm-9">
									    	<input type="text" name="company" class="form-control" id="carrental-company" value="<?= (($edit == true) ? $detail['info']->company : '') ?>">
									    </div>
									  </div>
									  
									  <!-- VAT //-->
									  <div class="form-group">
									    <label for="carrental-vat" class="col-sm-3 control-label">Registro Fiscal</label>
									    <div class="col-sm-9">
									    	<input type="text" name="vat" class="form-control" id="carrental-vat" value="<?= (($edit == true) ? $detail['info']->vat : '') ?>">
									    </div>
									  </div>
									  
									  <!-- Flight no. //-->
									  <div class="form-group">
									    <label for="carrental-flight" class="col-sm-3 control-label">DUI</label>
									    <div class="col-sm-9">
									    	<input type="text" name="flight" class="form-control" id="carrental-flight" value="<?= (($edit == true) ? $detail['info']->flight : '') ?>">
									    </div>
									  </div>
									  
									  <!-- Driver's license no //-->
									  <div class="form-group">
									    <label for="carrental-license-no" class="col-sm-3 control-label">Licencia</label>
									    <div class="col-sm-9">
									    	<input type="text" name="license" class="form-control" id="carrental-license-no" value="<?= (($edit == true) ? $detail['info']->license : '') ?>">
									    </div>
									  </div>
									  
									  <!-- Passport / ID number //-->
									  <div class="form-group">
									    <label for="carrental-id-no" class="col-sm-3 control-label">Pasaporte o ID</label>
									    <div class="col-sm-9">
									    	<input type="text" name="id_card" class="form-control" id="carrental-id-no" value="<?= (($edit == true) ? $detail['info']->id_card : '') ?>">
									    </div>
									  </div>
									  
										<!-- Payment option //-->
									  <div class="form-group">
									    <label for="carrental-payment" class="col-sm-3 control-label">Metodo de Pago</label>
									    <div class="col-sm-9">
									    	<select name="payment_option" class="form-control" id="carrental-payment" >
									    		<option value="">Por favor selecciona un metodo de pago</option>
									    		<option <?= (($edit == true && $detail['info']->payment_option == 'cash') ? 'selected="selected"' : '') ?> value="cash">Al Contado</option>
													<option <?= (($edit == true && $detail['info']->payment_option == 'cc') ? 'selected="selected"' : '') ?> value="cc">Tarjeta de Credito</option>
													<option <?= (($edit == true && $detail['info']->payment_option == 'paypal') ? 'selected="selected"' : '') ?> value="paypal">PayPal</option>
													<option <?= (($edit == true && $detail['info']->payment_option == 'bank') ? 'selected="selected"' : '') ?> value="bank">Transferencia Bancaria</option>
									    	</select>
									    </div>
									  </div>
										
										<!-- Partner code -->
										<div class="form-group">
									    <label for="carrental-partner-code" class="col-sm-3 control-label">Nombre del Ejecutivo de Ventas</label>
									    <div class="col-sm-9">
									    	<input type="text" name="partner_code" class="form-control" id="carrental-partner-code" value="<?= (($edit == true) ? $detail['info']->partner_code : '') ?>">
											<p class="help-block">Escriba el Nombre Completo del ejecutivo de ventas que realizo la reserva.</p>
									    </div>
									  </div>
										
										<!-- Comment -->
										<div class="form-group">
									    <label for="carrental-comment" class="col-sm-3 control-label">Comentarios</label>
									    <div class="col-sm-9">
									    	<textarea name="comment" class="form-control" id="carrental-comment"><?= (($edit == true) ? $detail['info']->comment : '') ?></textarea>
									    </div>
									  </div>
										
										<?php do_action( 'carrental_admin_booking_form', $edit ? $detail['info']->id_booking : 0 ); ?>

										
										<div class="form-group">
									  	<div class="col-sm-3"></div>
									    <div class="col-sm-9">
									    	<h3>Conductores Adicionales</h3>
									    </div>
									  </div>
									  
									  
									  <?php if ($edit == true && isset($detail['drivers'])) { ?>
									  	<?php foreach ($detail['drivers'] as $key => $val) { ?>
									  		<?php $drv = $key + 1; ?>
									  		
									  		<div class="form-group additional_driver">
											    <label class="col-sm-3 control-label">
														<a href="javascript:void(0);" class="btn btn-xs btn-danger delete_driver">Borrar</a>
														&nbsp;&nbsp;&nbsp;Conductor no. <?= $drv ?>
													</label>
											    <div class="col-sm-9">
											    	
													  <div class="form-group">
													    <div class="col-sm-6">
													    	<input type="text" name="drv[first_name][]" class="form-control" placeholder="Nombre" value="<?= $val->first_name ?>">
													    </div>
													    <div class="col-sm-6">
													    	<input type="text" name="drv[last_name][]" class="form-control" placeholder="Apellido" value="<?= $val->last_name ?>">
													    </div>
													  </div>
													  
													  <div class="form-group">
													    <div class="col-sm-6">
													    	<input type="text" name="drv[email][]" class="form-control" placeholder="E-mail" value="<?= $val->email ?>">
													    </div>
													    <div class="col-sm-6">
													    	<input type="text" name="drv[phone][]" class="form-control" placeholder="Telefono" value="<?= $val->phone ?>">
													    </div>
													  </div>
													  
													  <!-- Street //-->
													  <div class="form-group">
													    <div class="col-sm-6">
													    	<input type="text" name="drv[street][]" class="form-control" placeholder="Dirección" value="<?= $val->street ?>">
													    </div>
													    <div class="col-sm-6">
													    	<input type="text" name="drv[city][]" class="form-control" placeholder="Ciudad" value="<?= $val->city?>">
													    </div>
													  </div>
													 	
													  <!-- ZIP //-->
													  <div class="form-group">
													    <div class="col-sm-4">
													    	<input type="text" name="drv[zip][]" class="form-control" placeholder="ZIP" value="<?= $val->zip ?>">
													    </div>
													    <div class="col-sm-8">
													    	<select name="drv[country][]" class="form-control">
														    	<option value="">- Seleccionar -</option>
														    	<?php $countries = CarRental_Admin::get_country_list(); ?>
														    	<?php foreach ($countries as $kD => $vD) { ?>
														    		<option value="<?= $kD ?>" <?= (($edit == true && $kD == $val->country) ? 'selected="selected"' : '') ?>><?= $vD ?></option>
														    	<?php } ?>
													    	</select>
													    </div>
													  </div>
													  											    	
											    </div>
											  </div>
									  		
									  	<?php } ?>
									  <?php } ?>
									  
									  <div class="form-group additional_driver additional_driver_new">
									    <label class="col-sm-3 control-label">
												<a href="javascript:void(0);" class="btn btn-xs btn-danger delete_driver">Borrar</a>
												&nbsp;&nbsp;&nbsp;Nuevo Conductor												
											</label>
									    <div class="col-sm-9">
									    	
											  <div class="form-group">
											    <div class="col-sm-6">
											    	<input type="text" name="drv[first_name][]" class="form-control" placeholder="Nombre">
											    </div>
											    <div class="col-sm-6">
											    	<input type="text" name="drv[last_name][]" class="form-control" placeholder="Apellido">
											    </div>
											  </div>
											  
											  <div class="form-group">
											    <div class="col-sm-6">
											    	<input type="text" name="drv[email][]" class="form-control" placeholder="E-mail">
											    </div>
											    <div class="col-sm-6">
											    	<input type="text" name="drv[phone][]" class="form-control" placeholder="Telefono">
											    </div>
											  </div>
											  
											  <div class="form-group">
											    <div class="col-sm-6">
											    	<input type="text" name="drv[street][]" class="form-control" placeholder="Dirección">
											    </div>
											    <div class="col-sm-6">
											    	<input type="text" name="drv[city][]" class="form-control" placeholder="Ciudad">
											    </div>
											  </div>
											 	
											  <div class="form-group">
											    <div class="col-sm-4">
											    	<input type="text" name="drv[zip][]" class="form-control" placeholder="ZIP">
											    </div>
											    <div class="col-sm-8">
											    	<select name="drv[country][]" class="form-control">
												    	<option value="">- Seleccionar -</option>
												    	<?php $countries = CarRental_Admin::get_country_list(); ?>
												    	<?php foreach ($countries as $kD => $vD) { ?>
												    		<option value="<?= $kD ?>"><?= $vD ?></option>
												    	<?php } ?>
											    	</select>
											    </div>
											  </div>
											  		    	
									    </div>
									  </div>
									  
									  <div class="form-group additional_driver_new_button">
									  	<label class="col-sm-3 control-label"></label>
									    <div class="col-sm-9">
									  		<a href="javascript:void(0);" class="btn btn-success add_another_driver">Agregar otro conductor</a>
									  	</div>
									  </div>
									  
									  <script type="text/javascript">
									  
									  	jQuery(document).ready(function() {
												
												jQuery('.additional_driver_new').hide();
												
												jQuery(document).on('click', '.delete_driver', function() {
													jQuery(this).parent().parent().remove();
												});
												
												jQuery('.add_another_driver').on('click', function() {
													jQuery('.additional_driver_new_button').before('<div class="form-group additional_driver">' + jQuery('.additional_driver_new').html() + '</div>');
												});
											
												$('.datepicker').datepicker({dateFormat: 'yy-mm-dd'});
											});
									  
									  </script>
									  
									  <div class="form-group">
									  	<div class="col-sm-3"></div>
									    <div class="col-sm-9">
									    	<h3>Ordenes de Reservas</h3>
									    </div>
									  </div>
										
										
										<?php $currency = array(get_option('carrental_global_currency')); ?>
										<?php $av_currencies = unserialize(get_option('carrental_available_currencies')); ?>
										<?php if (!empty($av_currencies)) { $av_currencies = array_keys($av_currencies); $currency = array_merge($currency, $av_currencies); } ?>
										
										<?php if (isset($detail['prices']) && !empty($detail['prices'])) { ?>
											<?php foreach ($detail['prices'] as $key => $val) { ?>
												<div class="form-group price_row">
											    <label class="col-sm-3 control-label">
														<a href="javascript:void(0);" class="btn btn-xs btn-danger delete_price">Borrar</a>
														&nbsp;&nbsp;&nbsp;Orden No. <?= $key+1 ?>
													</label>
											    <div class="col-sm-9">
													  <div class="form-group">
													    <div class="col-sm-8">
													    	<input type="text" name="prices[name][]" class="form-control" value="<?= (($edit == true) ? $val->name : '') ?>">
													    </div>
													    <div class="col-sm-2">
													    	<div class="form-group">
															    <input type="text" name="prices[price][]" class="form-control" value="<?= (($edit == true) ? $val->price : '') ?>">
															  </div>
													    </div>
													    <div class="col-sm-2">
													    	<div class="form-group">
															    <select name="prices[currency][]" class="form-control price_currency" style="width:70%;margin-left:1em;padding: 3px 3px;height: 2.35em;">
															    	<?php foreach ($currency as $cc) { ?>
																			<option value="<?= $cc ?>" <?php if ($edit == true && $val->currency == $cc) { ?>selected<?php } ?>><?= $cc ?></option>
																		<?php } ?>
															    </select>
															  </div>
													    </div>
													  </div>
											    </div>
											  </div>
											  
											<?php } ?>
										<?php } ?>
										
										<div class="form-group price_new">
									    <label class="col-sm-3 control-label">
												<a href="javascript:void(0);" class="btn btn-xs btn-danger delete_price">Borrar</a>
												&nbsp;&nbsp;&nbsp;Nueva Orden
											</label>
									    <div class="col-sm-9">
											  <div class="form-group">
											    <div class="col-sm-8">
											    	<input type="text" name="prices[name][]" class="form-control" placeholder="Descripción">
											    </div>
											    <div class="col-sm-2">
											    	<div class="form-group">
													    <input type="text" name="prices[price][]" class="form-control" placeholder="Precio">
													  </div>
											    </div>
											    <div class="col-sm-2">
											    	<div class="form-group">
													    <select name="prices[currency][]" class="form-control price_currency" style="width:70%;margin-left:1em;padding: 3px 3px;height: 2.35em;">
													    	<?php foreach ($currency as $cc) { ?>
																	<option value="<?= $cc ?>"><?= $cc ?></option>
																<?php } ?>
													    </select>
													  </div>
											    </div>
											  </div>
									    </div>
									  </div>
										
										
									  <div class="form-group price_new_button">
									  	<label class="col-sm-3 control-label"></label>
									    <div class="col-sm-9">
									  		<a href="javascript:void(0);" class="btn btn-success add_price">Agregar una Orden</a>
									  	</div>
									  </div>
									  
									  <script type="text/javascript">
									  
									  	jQuery(document).ready(function() {
												
												jQuery('.price_new').hide();
												
												jQuery(document).on('click', '.delete_price', function() {
													jQuery(this).parent().parent().remove();
												});
												
												jQuery('.add_price').on('click', function() {
													jQuery('.price_new_button').before('<div class="form-group price_row">' + jQuery('.price_new').html() + '</div>');
													jQuery('.price_currency').val(jQuery('.price_currency').first().val());
												});
												
												jQuery(document).on('change', '.price_currency', function() {
													var currency = jQuery(this).val();
													jQuery('.price_currency').val(currency);
												});
												
											});
									  
									  </script>
									  
									  <!-- Submit //-->
									  <div class="form-group">
									  	<div class="col-sm-offset-3 col-sm-9">
									  		<?php if ($edit == true) { ?>
									  			<input type="hidden" name="id_booking" value="<?= $detail['info']->id_booking ?>">
									  			<button type="submit" class="btn btn-warning" name="add_booking"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Confirmar &amp; Guardar</button>
									  		<?php } else { ?>
									  			<button type="submit" class="btn btn-warning" name="add_booking"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Confirmar &amp; Añadir</button>
									  		<?php } ?>
									  	</div>
									  </div>
										
									</div>
								</div>
								
							</form>
						</div>
						
						<hr>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-12">
						<form class="booking-fulltext-search" action="" method="get">
							<label>Fecha de Regreso: </label>
							<input type="text" name="filter_from" class="datepicker" value="<?= isset($_GET['filter_from']) ? $_GET['filter_from'] : date('Y-m-d') ?>">
							
							<label>Fecha de Regreso: </label>
							<input type="text" name="filter_to" class="datepicker" value="<?= isset($_GET['filter_to']) ? $_GET['filter_to'] : '' ?>">
							
							<label>Buscar: </label>
							<input type="text" name="q" placeholder="Buscar" value="<?php echo isset($_GET['q']) ? $_GET['q'] : '';?>" />
							<input type="hidden" name="page" value="<?php echo $_GET['page'];?>">
							<input type="submit" class="btn btn-default" value="Mostrar">
						</form>
						<?php if (isset($booking) && !empty($booking)) { ?>
						<label class="label_select_all"><input type="checkbox" name="select_all" value="1" class="data_table_select_all" data-id="carrental-booking" /> Seleccionar todo</label>
							<table class="table table-striped" id="carrental-booking">
					      <thead>
					        <tr>
					          <th>#</th>
							  <th>Nombre</th>
							  <th>Email</th>
					          <th>Vehículo</th>
					          <th>Fecha de Entrega</th>
					          <th>Ubicación Entrega</th>
					          <th>Fecha de Regreso</th>
					          <th>Ubicación de regreso</th>
					          <th>Precio</th>
					          <th>ID de la Orden</th>
					          <th>Configuración</th>
					        </tr>
					      </thead>
					      <tbody>
					      	
					      	<?php foreach ($booking as $key => $val) { ?>
				      		<tr>
					          <td>
											<input type="checkbox" class="input-control batch_processing" name="batch[]" value="<?= $val->id_booking ?>">&nbsp;
											<abbr title="Created: <?= $val->created ?>

								<?= (!empty($val->updated) ? 'Updated: ' . $val->updated : '') ?>"><?= $val->id_booking ?></abbr></td>
							  <td><?= $val->first_name.' '.$val->last_name; ?></td>
							  <td><?= $val->email; ?><br>
							  <form action="" method="post" class="form-inline" role="form">
												<div class="form-group">
													<input type="hidden" name="id_booking" value="<?= $val->id_booking ?>">
													<button name="resend_email" class="btn btn-xs btn-success">Enviar Email confirmación</button>
												</div>
											</form>
							  </td>
										<td><strong><?= (!empty($val->vehicle) ? $val->vehicle : '- Unknown -') ?></strong></td>
										<td><?= $val->enter_date ?></td>
										<td><?= $val->enter_loc ?></td>
					          <td><?= $val->return_date ?></td>
					          <td><?= $val->return_loc ?></td>
					          <td><?= CarRental::get_currency_symbol('before', $val->currency) ?><?= number_format($val->total_rental, 2, '.', ',') ?><?= CarRental::get_currency_symbol('after', $val->currency) ?></td>
					          <td><a href="<?= esc_url(home_url('/')); ?>?page=carrental&summary=<?= $val->hash ?>" target="_blank" class="btn btn-info btn-xs">Show #<?= $val->id_order ?></a></td>
										<td>
											<form action="" method="post" class="form-inline" role="form">
												<div class="form-group">
													<a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-booking')); ?>&amp;edit=<?= $val->id_booking ?>" class="btn btn-xs btn-primary">Modificar</a>
												</div>
											</form>
											<form action="" method="post" class="form-inline" role="form">
												<div class="form-group">
													<input type="hidden" name="id_booking" value="<?= $val->id_booking ?>">
													<button name="copy_booking" class="btn btn-xs btn-warning">Copiar</button>
												</div>
											</form>
											<?php if (isset($_GET['deleted'])) { ?>
												<form action="" method="post" class="form-inline" role="form" onsubmit="return confirm('<?= __('Estas seguro de restaurar esta reserva?', 'HMO') ?>');">
													<div class="form-group">
														<input type="hidden" name="id_booking" value="<?= $val->id_booking ?>">
														<button name="restore_booking" class="btn btn-xs btn-success">Restaurar</button>
													</div>
												</form>
											<?php } else { ?>
												<form action="" method="post" class="form-inline" role="form" onsubmit="return confirm('<?= __('Estas seguro de Archivar esta reserva?', 'HMO') ?>');">
													<div class="form-group">
														<input type="hidden" name="id_booking" value="<?= $val->id_booking ?>">
														<button name="delete_booking" class="btn btn-xs btn-danger">Archivar</button>
													</div>
												</form>
											<?php } ?>
										</td>
					        </tr>
									<?php } ?>
					      </tbody>
					    </table>
						<label class="label_select_all"><input type="checkbox" name="select_all" value="1" class="data_table_select_all" data-id="carrental-booking" /> Seleccionar Todo</label>
						
					    <h4>Realizar con todos los items seleccionados</h4>
					    
					    <form action="" method="post" class="form" role="form" onsubmit="if (jQuery('[name=batch_processing_values]').val() == '') { alert('No Booking is selected to copy.'); return false }; return confirm('<?= __('Do you really want to copy selected Bookings?', 'carrental') ?>');">
								<div class="form-group">
									<input type="hidden" name="batch_processing_values" value="">
									<button name="batch_copy_booking" class="btn btn-warning">Copiar <span class="batch_processing_count"></span>Reservas Seleccionadas</button>
								</div>
							</form>
							
						<?php if (isset($_GET['deleted'])) { ?>
							<form action="" method="post" class="form" role="form" onsubmit="if (jQuery('[name=batch_processing_values]').val() == '') { alert('No Booking is selected to delete.'); return false }; return confirm('<?= __('Do you really want to delete selected Bookings?', 'carrental') ?>');">
								<div class="form-group">
									<input type="hidden" name="batch_processing_values" value="">
									<button name="batch_delete_booking" class="btn btn-danger">Borrar <span class="batch_processing_count"></span>Reservas Seleccionadas</button>
								</div>
							</form>
						<?php } else { ?>
					    <form action="" method="post" class="form" role="form" onsubmit="if (jQuery('[name=batch_processing_values]').val() == '') { alert('No Booking is selected to archive.'); return false }; return confirm('<?= __('Do you really want to archive selected Bookings?', 'carrental') ?>');">
								<div class="form-group">
									<input type="hidden" name="batch_processing_values" value="">
									<button name="batch_archive_booking" class="btn btn-danger">Archivar <span class="batch_processing_count"></span>Reservas Seleccionadas</button>
								</div>
							</form>
					    <?php } ?>
					    
						<?php } else { ?>
							<div class="alert alert-info">
								<span class="glyphicon glyphicon-info-sign"></span>&nbsp;&nbsp;
								<?= esc_html__( 'No hay reservas Activas por el momento', 'carrental' ); ?>
							</div>
						<?php } ?>
						
					</div>
				</div>
				
				
				
			</div>
		</div>
	</div>
	
</div>
