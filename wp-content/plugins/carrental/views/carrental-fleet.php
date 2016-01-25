<div class="carrental-wrapper">

	<?php include CARRENTAL__PLUGIN_DIR . 'views/header.php'; ?>

	<div class="row">

		<div class="col-md-12 carrental-main-wrapper">
			<div class="carrental-main-content">

				<?php include CARRENTAL__PLUGIN_DIR . 'views/flash_msg.php'; ?>

				<div class="row">
					<div class="col-md-12">

						<?php if ($edit == true) { ?>
							<h3>Edit vehicle: <?= $detail->name ?></h3>
						<?php } else { ?>
							<?php if (isset($_GET['deleted'])) { ?>
								<a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-fleet')); ?>" class="btn btn-default" style="float:right;">Show normal</a>
							<?php } else { ?>
								<a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-fleet')); ?>&amp;deleted" class="btn btn-default" style="float:right;">Show deleted</a>
							<?php } ?>

							<a href="javascript:void(0);" class="btn btn-success" id="carrental-fleet-add-button"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add new vehicle</a>
							
							<a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-fleet-parameters')); ?>" class="btn btn-warning"><span class="glyphicon glyphicon-list"></span>&nbsp;&nbsp;Manage fleet parameters</a>
						<?php } ?>

						<div id="<?= (($edit == true) ? 'carrental-fleet-edit-form' : 'carrental-fleet-add-form') ?>" class="carrental-add-form">
							<form action="" method="post" role="form" class="form-horizontal" enctype="multipart/form-data">

								<div class="row">
									<div class="col-md-11">

										<div class="alert alert-info">
											<p><span class="glyphicon glyphicon-share-alt"></span>&nbsp;&nbsp;Whichever field is left blank will not be used in car description.</p>
											<p><span class="glyphicon glyphicon-share-alt"></span>&nbsp;&nbsp;Manage your <a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-settings')); ?>#vehicle-categories">Vehicle categories</a>, <a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-pricing')); ?>">Pricing schemes</a> and <a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-extras')); ?>">Extras</a> first.</p>
										</div>

										<!-- Name //-->
										<div class="form-group">
											<label for="carrental-type" class="col-sm-3 control-label">Name</label>
											<div class="col-sm-9">
												<input type="text" name="name" class="form-control" id="carrental-type" placeholder="Ford Mondeo / SUV / Mid-size" value="<?= (($edit == true) ? $detail->name : '') ?>">
											</div>
										</div>

										<!-- Vehicle Category //-->
										<div class="form-group">
											<label for="carrental-category" class="col-sm-3 control-label">Vehicle category</label>
											<div class="col-sm-9">
												<select name="id_category" id="carrental-category" class="form-control">
													<option value="none">- none -</option>
													<?php if ($vehicle_categories && !empty($vehicle_categories)) { ?>
														<?php foreach ($vehicle_categories as $key => $val) { ?>
															<option value="<?= $val->id_category ?>" <?= (($edit == true && $detail->id_category == $val->id_category) ? 'selected="selected"' : '') ?>><?= $val->name ?></option>
														<?php } ?>
													<?php } ?>
												</select>
												<p class="help-block">To select from vehicle categories, first create them in settings module.</p>
											</div>
										</div>

										<!-- Current location //-->
										<div class="form-group">
											<label for="carrental-location" class="col-sm-3 control-label">Current location</label>
											<div class="col-sm-9">
												<select name="id_branch" id="carrental-location" class="form-control">
													<option value="0">- none -</option>
													<option value="-1">Unassigned (unavailable for rent)</option>
													<?php if ($branches && !empty($branches)) { ?>
														<?php foreach ($branches as $key => $val) { ?>
															<option value="<?= $val->id_branch ?>" <?= (($edit == true && $detail->id_branch == $val->id_branch) ? 'selected="selected"' : '') ?>><?= $val->name ?></option>
														<?php } ?>
													<?php } ?>
												</select>
												<p class="help-block">To select from locations, go to branches module and create a branch.</p>
											</div>
										</div>

										<!-- Global Pricing Scheme //-->
										<div class="form-group">
											<label class="col-sm-3 control-label">Global Pricing scheme</label>
											<div class="col-sm-9">
												<select name="global_pricing_scheme" class="form-control">
													<option value="0">- none -</option>
													<?php if (isset($pricing) && !empty($pricing)) { ?>
														<?php foreach ($pricing as $key => $val) { ?>
															<option value="<?= $val->id_pricing ?>" <?= (($edit == true && $detail->global_pricing_scheme == $val->id_pricing) ? 'selected="selected"' : '') ?>><?= $val->name ?></option>
														<?php } ?>
													<?php } ?>
												</select>
												<p class="help-block">This pricing scheme is used when no other pricing scheme is active or usable.</p>
												<p class="help-block">To assign a pricing scheme, go to pricing module and create a pricing scheme.</p>
											</div>
										</div>

										<!-- Price Scheme //-->
										<div class="form-group">
											<label class="col-sm-3 control-label"><abbr title="Highest priority first!">Pricing scheme</abbr></label>
											<div class="col-sm-9">
												<div id="pricing_sort">

													<?php if ($edit == true && isset($detail->pricing) && !empty($detail->pricing)) { ?>
														<?php foreach ($detail->pricing as $key => $val) { ?>

															<!-- Price scheme row //-->
															<div class="row" style="position: relative;" class="sortable">
																<div class="col-xs-4">
																	<select name="pricing[]" class="form-control">
																		<option value="0">- none -</option>
																		<?php if (isset($pricing) && !empty($pricing)) { ?>
																			<?php foreach ($pricing as $kD => $vD) { ?>
																				<option value="<?= $vD->id_pricing ?>" <?= (($val->id_pricing == $vD->id_pricing) ? 'selected="selected"' : '') ?>><?= $vD->name ?></option>
																			<?php } ?>
																		<?php } ?>
																	</select>
																</div>
																<div class="col-xs-3">
																	<div class="form-group has-feedback">
																		<input type="text" name="pricing_from[]" class="form-control pricing_datepicker" placeholder="Valid from" value="<?= (($val->valid_from != '0000-00-00') ? $val->valid_from : '') ?>">
																		<span class="glyphicon glyphicon-calendar form-control-feedback"></span>
																	</div>
																</div>
																<div class="col-xs-3">
																	<div class="form-group has-feedback">
																		<input type="text" name="pricing_to[]" class="form-control pricing_datepicker" placeholder="Valid until" value="<?= (($val->valid_to != '0000-00-00') ? $val->valid_to : '') ?>">
																		<span class="glyphicon glyphicon-calendar form-control-feedback"></span>
																	</div>
																</div>
																<div class="col-xs-1" style="padding-right: 0px;">
																	<div class="checkbox">
																		<label title="Repeat every year">
																			<input type="checkbox" name="pricing_repeat[]" value="1" <?= ($val->repeat == 1 ? 'checked="checked"' : '') ?>>&nbsp; Repeat
																		</label>
																	</div>
																</div>
																<div class="col-xs-1">
																	<span class="glyphicon glyphicon-sort" style="margin-top:9px;cursor:move;" title="Move up or down to sort Price scheme. Highest priority first!"></span>
																</div>														
															</div><!-- .row //-->

														<?php } ?>
													<?php } ?>

													<div id="carrental-prices">
														<!-- Price scheme row //-->
														<div class="row" style="position: relative;" class="sortable">
															<div class="col-xs-4">
																<select name="pricing[]" class="form-control">
																	<option value="0">- none -</option>
																	<?php if (isset($pricing) && !empty($pricing)) { ?>
																		<?php foreach ($pricing as $key => $val) { ?>
																			<option value="<?= $val->id_pricing ?>"><?= $val->name ?></option>
																		<?php } ?>
																	<?php } ?>
																</select>
															</div>
															<div class="col-xs-3">
																<div class="form-group has-feedback">
																	<input type="text" name="pricing_from[]" class="form-control pricing_datepicker" placeholder="Valid from">
																	<span class="glyphicon glyphicon-calendar form-control-feedback"></span>
																</div>
															</div>
															<div class="col-xs-3">
																<div class="form-group has-feedback">
																	<input type="text" name="pricing_to[]" class="form-control pricing_datepicker" placeholder="Valid until">
																	<span class="glyphicon glyphicon-calendar form-control-feedback"></span>
																</div>
															</div>
															<div class="col-xs-1"  style="padding-right: 0px;">
																<div class="checkbox">
																	<label title="Repeat every year">
																		<input type="checkbox" name="pricing_repeat[]" value="1" <?= ($val->repeat == 1 ? 'checked="checked"' : '') ?>>&nbsp; Repeat
																	</label>
																</div>
															</div>
															<div class="col-xs-1">
																<span class="glyphicon glyphicon-sort" style="margin-top:9px;cursor:move;" title="Move up or down to sort Price scheme. Highest priority first!"></span>
															</div>														
														</div><!-- .row //-->
													</div>

													<div id="carrental-prices-insert"></div>
													<p class="help">*select repeat to repeat this pricing scheme each year</p>
												</div>
												<a href="javascript:void(0);" id="carrental-add-pricing-scheme" class="btn btn-info btn-xs">Add Pricing Scheme</a>
											</div>
										</div>
										
										<!-- Price from //-->
										<div class="form-group">
											<label for="price-from" class="col-sm-3 control-label">Price from</label>
											<div class="col-sm-9">
												<div class="input-group">
													<?php $global_currency = get_option('carrental_global_currency');?>
													<?php if (CarRental::get_currency_symbol('before', $global_currency) != '') {?><span class="input-group-addon"><?= CarRental::get_currency_symbol('before', $global_currency) ?></span><?php } ?>
													<input type="text" name="price_from" class="form-control" id="price-from" placeholder="Set default price from" value="<?= (($edit == true) ? $detail->price_from : '') ?>">	
													<?php if (CarRental::get_currency_symbol('after', $global_currency) != '') {?><span class="input-group-addon"><?= CarRental::get_currency_symbol('after', $global_currency) ?></span><?php } ?>
												</div>
												<p class="help-block">* if you want to set a price from displayed on the front end when clients browse through cars, insert it here.</p>
											</div>
										</div>

										<!-- Extras //-->
										<div class="form-group">
											<label for="carrental-extras" class="col-sm-3 control-label">Extras</label>
											<div class="col-sm-9">
												<?php if ($extras && !empty($extras)) { ?>
													<?php foreach ($extras as $key => $val) { ?>
														<div class="checkbox">
															<label>
																<input type="checkbox" name="extras[]" value="<?= $val->id_extras ?>" <?= (($edit == true && !empty($detail->extras) && in_array($val->id_extras, explode(',', $detail->extras))) ? 'checked="checked"' : '') ?>>&nbsp; <?= $val->name_admin == '' ? $val->name : $val->name_admin ?>
															</label>
														</div>
													<?php } ?>
												<?php } ?>
												<p class="help-block">To select what extras are offered with this vehicle, create them in Extras module first.</p>
											</div>
										</div>

										<!-- Minimum rental time //-->
										<div class="form-group">
											<label for="carrental-min-time" class="col-sm-3 control-label">Minimum rental time</label>
											<div class="col-sm-9">
												<input type="text" name="min_rental_time" class="form-control" id="carrental-min-time" placeholder="In hours: 1, 2, 4, 8, 12, 24, ..." value="<?= (($edit == true) ? $detail->min_rental_time : '') ?>">
												<p class="help-block">In whole hours, minimum value = 1</p>
											</div>
										</div>

										<!-- Number of Seats //-->
										<div class="form-group">
											<label for="carrental-seats" class="col-sm-3 control-label">Seats</label>
											<div class="col-sm-9">
												<input type="text" name="seats" class="form-control" id="carrental-seats" placeholder="Number of seats: 2, 4, 5, 6, 7, ..." value="<?= (($edit == true) ? $detail->seats : '') ?>">
											</div>
										</div>

										<!-- Number of Doors //-->
										<div class="form-group">
											<label for="carrental-doors" class="col-sm-3 control-label">Doors</label>
											<div class="col-sm-9">
												<input type="text" name="doors" class="form-control" id="carrental-doors" placeholder="Number of doors: 2, 4, 5" value="<?= (($edit == true) ? $detail->doors : '') ?>">
											</div>
										</div>

										<!-- Number of Luggage //-->
										<div class="form-group">
											<label for="carrental-luggage" class="col-sm-3 control-label">Luggage</label>
											<div class="col-sm-9">
												<input type="text" name="luggage" class="form-control" id="carrental-luggage" placeholder="Number of luggage: 2, 3, 4, 5, ..." value="<?= (($edit == true) ? $detail->luggage : '') ?>">
											</div>
										</div>

										<!-- Transmission //-->
										<div class="form-group">
											<label for="carrental-transmission" class="col-sm-3 control-label">Transmission</label>
											<div class="col-sm-9">
												<label class="radio-inline">
													<input type="radio" name="transmission" id="carrental-transmission-automatic" value="0" <?= (($detail->transmission == 0) ? 'checked="checked"' : '') ?>>&nbsp;&nbsp;Not use
												</label>
												<label class="radio-inline">
													<input type="radio" name="transmission" id="carrental-transmission-automatic" value="1" <?= (($detail->transmission == 1) ? 'checked="checked"' : '') ?>>&nbsp;&nbsp;Automatic
												</label>
												<label class="radio-inline">
													<input type="radio" name="transmission" id="carrental-transmission-manual" value="2" <?= (($detail->transmission == 2) ? 'checked="checked"' : '') ?>>&nbsp;&nbsp;Manual
												</label>
											</div>
										</div>

										<!-- Free km / miles //-->
										<div class="form-group">
											<label for="carrental-free-dist" class="col-sm-3 control-label">Free distance (km/mi)</label>
											<div class="col-sm-9">
												<input type="text" name="free_distance" class="form-control" id="carrental-free-dist" placeholder="Free distance in kilometers or miles." value="<?= (($edit == true) ? $detail->free_distance : '') ?>">
												<p class="help-block">0 = unlimited</p>
											</div>
										</div>

										<!-- A/C //-->
										<div class="form-group">
											<label for="carrental-ac" class="col-sm-3 control-label">A/C</label>
											<div class="col-sm-9">
												<label class="radio-inline">
													<input type="radio" name="ac" id="carrental-ac-not" value="0" <?= (($detail->ac == 0) ? 'checked="checked"' : '') ?>>&nbsp;&nbsp;Not use
												</label>
												<label class="radio-inline">
													<input type="radio" name="ac" id="carrental-ac-yes" value="1" <?= (($detail->ac == 1) ? 'checked="checked"' : '') ?>>&nbsp;&nbsp;Yes
												</label>
												<label class="radio-inline">
													<input type="radio" name="ac" id="carrental-ac-no" value="2" <?= (($detail->ac == 2) ? 'checked="checked"' : '') ?>>&nbsp;&nbsp;No
												</label>
											</div>
										</div>

										<!-- Fuel //-->
										<div class="form-group">
											<label for="carrental-ac" class="col-sm-3 control-label">Fuel</label>
											<div class="col-sm-9">
												<label class="radio-inline">
													<input type="radio" name="fuel" id="carrental-fuel-not" value="0" <?= (($detail->fuel == 0) ? 'checked="checked"' : '') ?>>&nbsp;&nbsp;Not use
												</label>
												<label class="radio-inline">
													<input type="radio" name="fuel" id="carrental-fuel-yes" value="1" <?= (($detail->fuel == 1) ? 'checked="checked"' : '') ?>>&nbsp;&nbsp;Petrol
												</label>
												<label class="radio-inline">
													<input type="radio" name="fuel" id="carrental-fuel-no" value="2" <?= (($detail->fuel == 2) ? 'checked="checked"' : '') ?>>&nbsp;&nbsp;Diesel
												</label>
											</div>
										</div>
										
										<!-- All custom parameters //-->
										<?php foreach ($params as $param) { ?>
											<?php $name = unserialize($param->name);?>
											<!-- <?php echo $name['gb'];?> //-->
											<div class="form-group">
												<label for="carrental-param-<?php echo $param->id_fleet_parameter;?>" class="col-sm-3 control-label"><?php echo $name['gb'];?></label>
												<div class="col-sm-9">
													<?php if ($param->type == 2) { ?>
														<?php $values = unserialize($param->values); ?>
														<?php foreach ($values['gb'] as $key => $value) { ?>
															<label class="radio-inline">
																<input type="radio" name="custom_parameters[<?php echo $param->id_fleet_parameter;?>]" value="<?php echo $key;?>"<?php echo isset($params_values[$param->id_fleet_parameter]) && $params_values[$param->id_fleet_parameter] == $key ? ' checked="checked"' : '';?>>&nbsp;&nbsp;<?php echo $value == '' ? '-not-set-' : $value;?>
															</label>
														<?php } ?>
													<?php } else { ?>
														<input type="text" name="custom_parameters[<?php echo $param->id_fleet_parameter;?>]" class="form-control" id="carrental-param-<?php echo $param->id_fleet_parameter;?>" value="<?php echo isset($params_values[$param->id_fleet_parameter]) ? $params_values[$param->id_fleet_parameter] : '';?>">
														<p class="help-block">Enter number between <?php echo $param->range_from;?> and <?php echo $param->range_to;?>.</p>
													<?php } ?>
												</div>
											</div>
										<?php } ?>
										
										<!-- Number of vehicles //-->
										<div class="form-group">
											<label for="carrental-number-vehicles" class="col-sm-3 control-label">Available vehicles</label>
											<div class="col-sm-9">
												<input type="text" name="number_vehicles" class="form-control" id="carrental-number-vehicles" placeholder="Number of available vehicles." value="<?= (($edit == true) ? $detail->number_vehicles : '') ?>">
												<p class="help-block">If you allow branches overbooking in Settings module, this value will be overridden and set to unlimited.</p>
											</div>
										</div>

										<!-- Consumption //-->
										<div class="form-group">
											<label for="carrental-consumption" class="col-sm-3 control-label">Consumption</label>
											<div class="col-sm-9">
												<input type="text" name="consumption" class="form-control" id="carrental-consumption" placeholder="Vehicle consumption (in l/100 km or MPG)" value="<?= (($edit == true) ? $detail->consumption : '') ?>">
												<p class="help-block">Set mpg or l/100km in Settings module.</p>
											</div>
										</div>

										<!-- Additional parameters //-->
										<div class="form-group">
											<label class="col-sm-3 control-label">Parameters</label>

											<div class="col-sm-9">
												<ul class="nav nav-tabs" role="tablist" style="margin-bottom:10px;">
													<li role="presentation" class="active"><a href="javascript:void(0);" class="edit_fleet_parameters" data-value="gb">English (GB)</a></li>
													<?php $available_languages = unserialize(get_option('carrental_available_languages')); ?>
													<?php if ($available_languages && !empty($available_languages)) { ?>														
														<?php foreach ($available_languages as $key => $val) { ?>
															<?php if ($val['country-www'] == 'gb') {continue;} ?>
															<li role="presentation"><a href="javascript:void(0);" class="edit_fleet_parameters" data-value="<?= strtolower($val['country-www']) ?>"><?= $val['lang'] ?> (<?= strtoupper($val['country-www']) ?>)</a></li>
														<?php } ?>
													<?php } ?>
												</ul>
												
												<div id="additional_parameters_sort_gb" class="additional_parameters_tab" data-lng="gb">
													<div id="carrental-additional-parameters-gb" style="display: none;">
														<!-- Additional parameter row //-->
														<div class="row" style="position: relative;" class="sortable" data-row-i="0">
															<div class="col-xs-3">
																<div class="">
																	<input type="text" name="additional_parameters[gb][0][name]" class="form-control fleet-parameter-name" placeholder="Parameter name">
																</div>
															</div>
															<div class="col-xs-4">
																<div class="form-group has-feedback">
																	<input type="text" name="additional_parameters[gb][0][value]" class="form-control" placeholder="Parameter value">
																</div>
															</div>
															<div class="col-xs-1">
																<span class="glyphicon glyphicon-sort" style="margin-top:9px;cursor:move;" title="Move up or down to sort parameters!"></span>
																<span class="glyphicon glyphicon-remove fleet-delete-parameter" style="margin-top:9px;margin-left: 5px;cursor:pointer;" title="Remove this parameter!"></span>
															</div>
														</div><!-- .row //-->
													</div>
													<?php if ($edit == true && isset($detail->additional_parameters) && !empty($detail->additional_parameters)) { ?>
														<?php $additional_parameters = unserialize($detail->additional_parameters); ?>														
														<?php if (!isset($additional_parameters['gb'])) { $additional_parameters['gb'] = array();} ?>
														<?php $i =0;foreach ($additional_parameters['gb'] as $key => $val) { $i++; ?>
															<!-- Additional parameter row //-->
															<div class="row" style="position: relative;" class="sortable" data-row-i="<?php echo $i;?>">
																<div class="col-xs-3">
																	<div class="">
																		<input type="text" name="additional_parameters[gb][<?php echo $i;?>][name]" class="form-control fleet-parameter-name" placeholder="<?php echo CarRental_Admin::fleet_placeholder_param($i, $additional_parameters);?>" value="<?php echo $val['name'];?>">
																	</div>
																</div>
																<div class="col-xs-4">
																	<div class="form-group has-feedback">
																		<input type="text" name="additional_parameters[gb][<?php echo $i;?>][value]" class="form-control" placeholder="Parameter value" value="<?php echo $val['value'];?>">
																	</div>
																</div>
																<div class="col-xs-1">
																	<span class="glyphicon glyphicon-sort" style="margin-top:9px;cursor:move;" title="Move up or down to sort parameters!"></span>
																	<span class="glyphicon glyphicon-remove fleet-delete-parameter" style="margin-top:9px;margin-left: 5px;cursor:pointer;" title="Remove this parameter!"></span>
																</div>
															</div><!-- .row //-->														
														<?php } ?>
													<?php } ?>
													
													<div id="carrental-additional-parameters-insert-gb"></div>
													
													<div class="carrental-insert-existing-parameter" style="margin-bottom:10px;">
														<?php if (isset($all_additional_parameters['gb'])) { ?>
															<?php foreach ($all_additional_parameters['gb'] as $k => $v) { ?>
															<?php if ($v == '') { continue;}?>
															<a href="#" class="carrental-insert-parameter-link" data-name="<?php echo $v;?>"><?php echo $v;?></a> | 
															<?php } ?>
														<?php } ?>
													</div>
												</div>
												
												<?php if ($available_languages && !empty($available_languages)) { ?>														
													<?php foreach ($available_languages as $key => $val) { ?>
														<?php if ($val['country-www'] == 'gb') {continue;} ?>
														<div id="additional_parameters_sort_<?php echo $val['country-www'];?>" class="additional_parameters_tab" data-lng="<?php echo $val['country-www'];?>" style="display: none;">
															<div id="carrental-additional-parameters-<?php echo $val['country-www'];?>" style="display: none;">
																<!-- Additional parameter row //-->
																<div class="row" style="position: relative;" class="sortable" data-row-i="0">
																	<div class="col-xs-3">
																		<div class="">
																			<input type="text" name="additional_parameters[<?php echo $val['country-www'];?>][0][name]" class="form-control fleet-parameter-name" placeholder="Parameter name">
																		</div>
																	</div>
																	<div class="col-xs-4">
																		<div class="form-group has-feedback">
																			<input type="text" name="additional_parameters[<?php echo $val['country-www'];?>][0][value]" class="form-control" placeholder="Parameter value">
																		</div>
																	</div>
																	<div class="col-xs-1">
																		<span class="glyphicon glyphicon-sort" style="margin-top:9px;cursor:move;" title="Move up or down to sort parameters!"></span>
																		<span class="glyphicon glyphicon-remove fleet-delete-parameter" style="margin-top:9px;margin-left: 5px;cursor:pointer;" title="Remove this parameter!"></span>
																	</div>
																</div><!-- .row //-->
															</div>
															<?php if ($edit == true && isset($detail->additional_parameters) && !empty($detail->additional_parameters)) { ?>
																<?php $additional_parameters = unserialize($detail->additional_parameters); ?>
																<?php if (!isset($additional_parameters[$val['country-www']])) { $additional_parameters[$val['country-www']] = array();} ?>
																<?php $i =0;foreach ($additional_parameters[$val['country-www']] as $pkey => $pval) { $i++; ?>
																	<!-- Additional parameter row //-->
																	<div class="row" style="position: relative;" class="sortable" data-row-i="<?php echo $i;?>">
																		<div class="col-xs-3">
																			<div class="">
																				<input type="text" name="additional_parameters[<?php echo $val['country-www'];?>][<?php echo $i;?>][name]" class="form-control fleet-parameter-name" placeholder="<?php echo CarRental_Admin::fleet_placeholder_param($i, $additional_parameters);?>" value="<?php echo $pval['name'];?>">
																			</div>
																		</div>
																		<div class="col-xs-4">
																			<div class="form-group has-feedback">
																				<input type="text" name="additional_parameters[<?php echo $val['country-www'];?>][<?php echo $i;?>][value]" class="form-control" placeholder="Parameter value" value="<?php echo $pval['value'];?>">
																			</div>
																		</div>
																		<div class="col-xs-1">
																			<span class="glyphicon glyphicon-sort" style="margin-top:9px;cursor:move;" title="Move up or down to sort parameters!"></span>
																			<span class="glyphicon glyphicon-remove fleet-delete-parameter" style="margin-top:9px;margin-left: 5px;cursor:pointer;" title="Remove this parameter!"></span>
																		</div>
																	</div><!-- .row //-->														
																<?php } ?>
															<?php } ?>

															<div id="carrental-additional-parameters-insert-<?php echo $val['country-www'];?>"></div>
															<div class="carrental-insert-existing-parameter" style="margin-bottom:10px;">
																<?php if (isset($all_additional_parameters[$val['country-www']])) { ?>
																	<?php foreach ($all_additional_parameters[$val['country-www']] as $k => $v) { ?>
																	<?php if ($v == '') { continue;}?>
																	<a href="#" class="carrental-insert-parameter-link" data-name="<?php echo $v;?>"><?php echo $v;?></a> | 
																	<?php } ?>
																<?php } ?>
															</div>
														</div>
													<?php } ?>
												<?php } ?>
												
												<a href="javascript:void(0);" id="carrental-add-additional-parameter" class="btn btn-info btn-xs">Add New Parameter</a>
											</div>
										</div>

										<!-- Description //-->
										<div class="form-group">
											<label for="carrental-description" class="col-sm-3 control-label">Description</label>
											<div class="col-sm-9">

												<ul class="nav nav-tabs" role="tablist">
													<li role="presentation" class="active"><a href="javascript:void(0);" class="edit_fleet_description" data-value="gb">English (GB)</a></li>
													<?php $available_languages = unserialize(get_option('carrental_available_languages')); ?>
													<?php if ($available_languages && !empty($available_languages)) { ?>
														<?php foreach ($available_languages as $key => $val) { ?>
															<?php if ($val['country-www'] == 'gb') {continue;} ?>
															<li role="presentation"><a href="javascript:void(0);" class="edit_fleet_description" data-value="<?= strtolower($val['country-www']) ?>"><?= $val['lang'] ?> (<?= strtoupper($val['country-www']) ?>)</a></li>
														<?php } ?>
													<?php } ?>
												</ul>

												<?php if ($edit == true) { ?>
													<?php $fleet_description = unserialize($detail->description); ?>
													<?php
													if ($fleet_description == false) {
														$fleet_description['gb'] = $detail->description;
													}
													?>
												<?php } ?>

												<textarea class="form-control fleet_description fleet_description_gb" name="description[gb]" id="carrental-description" rows="3" placeholder="Brief description of cars in English (GB)."><?= ((isset($fleet_description['gb']) && !empty($fleet_description['gb'])) ? $fleet_description['gb'] : '') ?></textarea>
												<?php if ($available_languages && !empty($available_languages)) { ?>
													<?php foreach ($available_languages as $key => $val) { ?>
														<?php if ($val['country-www'] == 'gb') {continue;} ?>
														<textarea class="form-control fleet_description fleet_description_<?= strtolower($val['country-www']) ?>" name="description[<?= strtolower($val['country-www']) ?>]" rows="3" placeholder="Brief description of cars in <?= $val['lang'] ?> (<?= strtoupper($val['country-www']) ?>)."><?= ((isset($fleet_description[strtolower($val['country-www'])]) && !empty($fleet_description[strtolower($val['country-www'])])) ? CarRental::removeslashes($fleet_description[strtolower($val['country-www'])]) : '') ?></textarea>
	<?php } ?>
<?php } ?>
												<p class="help-block">This is shown under "show more info".</p>
											</div>
										</div>

										<!-- Deposit //-->
										<div class="form-group">
											<label for="carrental-deposit" class="col-sm-3 control-label">Deposit</label>
											<div class="col-sm-9">
												<input type="text" name="deposit" class="form-control" id="carrental-deposit" placeholder="How much the deposit on the car will be." value="<?= (($edit == true) ? $detail->deposit : '') ?>">
												<p class="help-block">This field is only informative in car details. Leave empty to hide. Set to 0 to show 0.</p>
											</div>
										</div>

										<!-- License registration number //-->
										<div class="form-group">
											<label for="carrental-license" class="col-sm-3 control-label">License registration number</label>
											<div class="col-sm-9">
												<input type="text" name="license" class="form-control" id="carrental-license" value="<?= (($edit == true) ? $detail->license : '') ?>">
											</div>
										</div>

										<!-- VIN code //-->
										<div class="form-group">
											<label for="carrental-vin" class="col-sm-3 control-label">VIN code</label>
											<div class="col-sm-9">
												<input type="text" name="vin" class="form-control" id="carrental-vin" value="<?= (($edit == true) ? $detail->vin : '') ?>">
											</div>
										</div>

										<!-- Internal Car ID //-->
										<div class="form-group">
											<label for="carrental-internal-id" class="col-sm-3 control-label">Internal car ID</label>
											<div class="col-sm-9">
												<input type="text" name="internal_id" class="form-control" id="carrental-internal-id" value="<?= (($edit == true) ? $detail->internal_id : '') ?>">
											</div>
										</div>

										<!-- Class Code //-->
										<div class="form-group">
											<label for="carrental-class-code" class="col-sm-3 control-label">Class code</label>
											<div class="col-sm-9">
												<input type="text" name="class_code" class="form-control" id="carrental-class-code" value="<?= (($edit == true) ? $detail->class_code : '') ?>">
												<p class="help-block">If using TSDweb extension, insert your TSD car class code here; else, use for internal records.</p>
											</div>
										</div>

										<!-- Picture of vehicle //-->
										<div class="form-group">
											<label for="carrental-picture" class="col-sm-3 control-label">Main picture of vehicle</label>
											<div class="col-sm-9">
<?php if ($edit == true) { ?>
													<div class="panel panel-info">
														<div class="panel-heading">Current picture</div>
														<div class="panel-body">
															<p><img src="<?= $detail->picture ?>" height="80"></p>
														</div>
													</div>
													<p><strong>Or you can upload new picture for Vehicle:</strong></p>
<?php } ?>
												<input type="file" name="picture" id="carrental-picture">
												<p class="help-block">Insert picture of the item or service, 400x400px.</p>
											</div>
										</div>

										<!-- Additional pictures of vehicle //-->
										<div class="form-group">
											<label for="carrental-picture" class="col-sm-3 control-label">Additional pictures</label>
											<div class="col-sm-9">
												<div class="panel panel-info">
													<div class="panel-heading">Additional pictures</div>
													<div class="panel-body">
														<ul class="additional-pictures" id="additional-pictures-ul">

															<?php if (isset($detail->additional_pictures) && !empty($detail->additional_pictures)) { ?>
																<?php $detail->additional_pictures = unserialize($detail->additional_pictures); ?>
																<?php if (is_array($detail->additional_pictures)) { ?>
																	<?php foreach ($detail->additional_pictures as $picture) { ?>
																		<li><input type="hidden" name="additional-pictures[]" value="<?php echo $picture; ?>" class="media-input" /><img src="<?php echo $picture; ?>" /><div class="buttons"><a href="#" class="btn btn-danger btn-block delete-button">X</a></div></li>
																	<?php } ?>
	<?php } ?>
<?php } ?>
														</ul>
													</div>
												</div>
												<button class="media-button">Add new picture</button>
											</div>
										</div>
										
										<div class="form-group">
											<label for="carrental-picture" class="col-sm-3 control-label">Similar cars</label>
											<div class="col-sm-9">
												<div class="panel panel-info">
													<div class="panel-heading">Similar cars</div>
														<div class="panel-body">
															<div class="" id="carrental_similar_cars_div">
																<?php $detail->similar_cars = unserialize($detail->similar_cars); ?>

																	<?php if ($detail->similar_cars && !empty($detail->similar_cars)) { ?>
																		<?php foreach ($detail->similar_cars as $key => $val) { ?>
																				<span style="margin-right:5px;margin-bottom:5px;" class="carrental_similar_car btn btn-warning"><?php echo $fleet_by_id[$val];?> <a href="#" class="carrental_remove_similar_car">X</a><input type="hidden" name="similar_cars[]" value="<?php echo $key;?>"></span>
																		<?php } ?>
																	<?php } else { ?>
																		<p>You have no similar cars yet.</p>	
																	<?php } ?>

															</div>

														<!-- .row //-->

														<div class="row">
															<div class="col-md-6">

																<h4>Add new similar car</h4>

																<div>																	
																	<select id="carrental-similar-car-select" class="form-control">
																		<option value="">Select car</option>
																		<?php foreach ($fleet as $f) { ?>
																			<?php if ($edit == true && $f->id_fleet == $detail->id_fleet) { continue;}?>
																			<option value="<?php echo $f->id_fleet;?>"><?php echo $f->name.' (ID: '.$f->id_fleet.')';?></option>
																		<?php } ?>
																	</select>
																</div>
																<button id="carrental_add_similar_car" class="btn btn-success">Add this car</button>
															</div>
														</div>
													<!-- .row //-->
													</div>
												</div>
											</div>
											<!-- .panel-body //-->
										</div>

										<!-- Submit //-->
										<div class="form-group">
											<div class="col-sm-offset-3 col-sm-9">
<?php if ($edit == true) { ?>
													<input type="hidden" name="id_fleet" value="<?= $detail->id_fleet ?>">
													<input type="hidden" name="current_picture" value="<?= $detail->picture ?>">
													<button type="submit" class="btn btn-warning" name="add_fleet"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Confirm &amp; Save</button>
												<?php } else { ?>
													<button type="submit" class="btn btn-warning" name="add_fleet"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Confirm &amp; Add</button>
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

						<?php if (isset($fleet) && !empty($fleet)) { ?>

							<?php $distance_metric = get_option('carrental_distance_metric'); ?>
	<?php $consumption = get_option('carrental_consumption'); ?>
	<?php $currency = get_option('carrental_global_currency'); ?>

							<label class="label_select_all"><input type="checkbox" name="select_all" value="1" class="data_table_select_all" data-id="carrental-fleet" /> Select all</label>
							<table class="table table-striped" id="carrental-fleet">
								<thead>
									<tr>
										<th>#</th>
										<th>Image</th>
										<th>Name</th>
										<th>Pricing schemes</th>
										<th>Parameters</th>
										<th>Parameters</th>
										<th>Extras</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
	<?php foreach ($fleet as $key => $val) { ?>
										<tr fleetId="<?php echo $val->id_fleet; ?>">
											<td>
												<input type="checkbox" class="input-control batch_processing" name="batch[]" value="<?= $val->id_fleet ?>">&nbsp;
												<abbr title="Created: <?= $val->created ?>

		<?= (!empty($val->updated) ? 'Updated: ' . $val->updated : '') ?>"><?= $val->id_fleet ?></abbr>
											</td>
											<td class="sortableTD"><img src="<?= $val->picture ?>" height="120"></td>
											<td class="sortableTD">
												<strong><?= (!empty($val->name) ? $val->name : '- Unknown -') ?></strong>
												<?php if ($val->id_branch == -1) { ?>
													<br><small>Unassigned (unavailable for rent)</small>
												<?php } elseif (!empty($val->branch_name)) { ?>
													<br><small>(Loc.: <?= $val->branch_name ?>)</small>
												<?php } ?>
											</td>
											<td>
												<?php if (!empty($val->pricing_name)) { ?>
													<p><a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-pricing')); ?>&amp;<?= (($val->pricing_type == 1) ? 'get_onetime_price' : 'get_day_ranges') ?>=<?= $val->global_pricing_scheme ?>" class="btn <?= (($val->pricing_type == 1) ? 'btn-info' : 'btn-success') ?> carrental_show_ranges"><?= $val->pricing_name ?></a></p>
													<?php if ($val->pricing_count > 0) { ?>
														<p><a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-pricing')); ?>&amp;get_fleet_price_schemes=<?= $val->id_fleet ?>" class="btn <?= (($val->pricing_type == 1) ? 'btn-info' : 'btn-success') ?> carrental_show_ranges">+ <?= $val->pricing_count ?> schemes</a></p>
													<?php } ?>
												<?php } else { ?>
													<p><em>- none -</em></p>
		<?php } ?>
											</td>
											<td class="sortableTD">
												<table class="table carrental-fleet-parameters">
													<tr>
														<td>Min. rent. time</td>
														<td><?= $val->min_rental_time ?> h</td>
													</tr>
													<tr>
														<td>Seats/Doors/Luggage</td>
														<td><?= $val->seats ?>/<?= $val->doors ?>/<?= $val->luggage ?></td>
													</tr>
													<tr>
														<td>Transmission</td>
														<td>
															<?php if ($val->transmission == 1) { ?>
																Automatic
															<?php } elseif ($val->transmission == 2) { ?>
																Manual
															<?php } else { ?>
																Not use
		<?php } ?>
														</td>
													</tr>
													<tr>
														<td>AC</td>
														<td>
															<?php if ($val->ac == 1) { ?>
																YES
															<?php } elseif ($val->ac == 2) { ?>
																NO
															<?php } else { ?>
																Not use
		<?php } ?>
														</td>
													</tr>
													<tr>
														<td>Fuel</td>
														<td>
															<?php if ($val->fuel == 1) { ?>
																Petrol
															<?php } elseif ($val->fuel == 2) { ?>
																Diesel
															<?php } else { ?>
																Not use
		<?php } ?>
														</td>
													</tr>
												</table>
											</td>
											<td class="sortableTD">
												<table class="table table-condensed carrental-fleet-parameters">
													<tr>
														<td>Free distance</td>
														<td><?= $val->free_distance ?>&nbsp;<?= (!empty($distance_metric) ? ' ' . $distance_metric : '') ?></td>
													</tr>
													<tr>
														<td>Consumption</td>
														<td><?= $val->consumption ?>&nbsp;<?php
													if (!empty($consumption)) {
														echo ($consumption == 'us' ? ' MPG' : ' l/100km');
													}
													?></td>
													</tr>
													<tr>
														<td>Available vehicles</td>
														<td><?= $val->number_vehicles ?></td>
													</tr>
													<tr>
														<td>Deposit</td>
														<td><?= $val->deposit ?>&nbsp;<?= (!empty($currency) ? ' ' . $currency : '') ?></td>
													</tr>
												</table>
											</td>
											<td>
													<?php if ($extras && !empty($extras) && !empty($val->extras)) { ?>
													<ul>
														<?php foreach ($extras as $kD => $vD) { ?>
														<?php if (in_array($vD->id_extras, explode(',', $val->extras))) { ?>
																<li><?= $vD->name ?></li>
				<?php } ?>
			<?php } ?>
													</ul>
		<?php } ?>
											</td>
											<td>
												<form action="" method="post" class="form" role="form">
													<div class="form-group">
														<a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-fleet')); ?>&amp;edit=<?= $val->id_fleet ?>" class="btn btn-primary btn-block">Modify</a>
													</div>
												</form>
												<form action="" method="post" class="form" role="form">
													<div class="form-group">
														<input type="hidden" name="id_fleet" value="<?= $val->id_fleet ?>">
														<button name="copy_fleet" class="btn btn-warning btn-block">Copy</button>
													</div>
												</form>
		<?php if (isset($_GET['deleted'])) { ?>
													<form action="" method="post" class="form" role="form" onsubmit="return confirm('<?= __('Do you really want to restore this Vehicle?', 'carrental') ?>');">
														<div class="form-group">
															<input type="hidden" name="id_fleet" value="<?= $val->id_fleet ?>">
															<button name="restore_fleet" class="btn btn-success btn-block">Restore</button>
														</div>
													</form>
		<?php } else { ?>
													<form action="" method="post" class="form" role="form" onsubmit="return confirm('<?= __('Do you really want to delete this Vehicle?', 'carrental') ?>');">
														<div class="form-group">
															<input type="hidden" name="id_fleet" value="<?= $val->id_fleet ?>">
															<button name="delete_fleet" class="btn btn-danger btn-block">Delete</button>
														</div>
													</form>
										<?php } ?>
											</td>
										</tr>

	<?php } ?>
								</tbody>
							</table>
							<label class="label_select_all"><input type="checkbox" name="select_all" value="1" class="data_table_select_all" data-id="carrental-fleet" /> Select all</label>

							<h4>Batch action on selected items</h4>

							<form action="" method="post" class="form" role="form" onsubmit="if (jQuery('[name=batch_processing_values]').val() == '') {
										alert('No Vehicle is selected to copy.');
										return false
									}
									;
									return confirm('<?= __('Do you really want to copy selected Vehicles?', 'carrental') ?>');">
								<div class="form-group">
									<input type="hidden" name="batch_processing_values" value="">
									<button name="batch_copy_fleet" class="btn btn-warning">Copy <span class="batch_processing_count"></span>selected Vehicles</button>
								</div>
							</form>
							<?php if (isset($_GET['deleted'])) { ?>
								<form action="" method="post" class="form" role="form" onsubmit="if (jQuery('[name=batch_processing_values]').val() == '') {
											alert('No Vehicle is selected to delete from database.');
											return false
										}
										;
										return confirm('<?= __('This action cannot be reversed, are you sure?', 'carrental') ?>');">
									<div class="form-group">
										<input type="hidden" name="batch_processing_values" value="">
										<button name="batch_delete_db_fleet" class="btn btn-danger">Delete <span class="batch_processing_count"></span>selected Vehicles from database</button>
									</div>
								</form>
							<?php } else { ?>
								<form action="" method="post" class="form" role="form" onsubmit="if (jQuery('[name=batch_processing_values]').val() == '') {
											alert('No Vehicle is selected to delete.');
											return false
										}
										;
										return confirm('<?= __('Do you really want to delete selected Vehicles?', 'carrental') ?>');">
									<div class="form-group">
										<input type="hidden" name="batch_processing_values" value="">
										<button name="batch_delete_fleet" class="btn btn-danger">Delete <span class="batch_processing_count"></span>selected Vehicles</button>
									</div>
								</form>
								<?php } ?>

						<?php } else { ?>
							<div class="alert alert-info">
								<span class="glyphicon glyphicon-info-sign"></span>&nbsp;&nbsp;
	<?= esc_html__('You do not have any Vehicles created yet, please create one clicking on "Add New Vehicle".', 'carrental'); ?>
							</div>
<?php } ?>

					</div>
				</div>



			</div>
		</div>
	</div>

</div>
<script language="JavaScript">
	var gk_media_init = function(button_selector) {
		jQuery(button_selector).click(function(event) {
			event.preventDefault();

			// check for media manager instance
			if (wp.media.frames.gk_frame) {
				wp.media.frames.gk_frame.open();
				return;
			}
			// configuration of the media manager new instance
			wp.media.frames.gk_frame = wp.media({
				title: 'Select image',
				multiple: true,
				library: {
					type: 'image'
				},
				button: {
					text: 'Use selected image'
				}
			});

			// Function used for the image selection and media manager closing
			var gk_media_set_image = function() {
				var selection = wp.media.frames.gk_frame.state().get('selection');

				// no selection
				if (!selection) {
					return;
				}
				console.log(selection);
				// iterate through selected elements
				selection.each(function(attachment) {
					var url = attachment.attributes.url;
					// add to additional images
					jQuery('#additional-pictures-ul').append('<li><input type="hidden" name="additional-pictures[]" value="' + url + '" class="media-input" /><img src="' + url + '" /><div class="buttons"><a href="#" class="btn btn-danger btn-block delete-button">X</a></div></li>');
				});
			};

			// closing event for media manger
			//wp.media.frames.gk_frame.on('close', gk_media_set_image);
			// image selection event
			wp.media.frames.gk_frame.on('select', gk_media_set_image);
			// showing media manager
			wp.media.frames.gk_frame.open();
		});

	};

	gk_media_init('.media-button');

	jQuery(document).ready(function($) {
	
		$(document).on('click', '.carrental_remove_similar_car', function (e) {
			e.preventDefault();
			$(this).parent().remove();
		});
		
		$('#carrental_add_similar_car').click(function(e){
			e.preventDefault();
			if ($('#carrental-similar-car-select').val() == '') {
				return;
			}
			$('#carrental_similar_cars_div p').remove();
			$('#carrental_similar_cars_div').append('<span style="margin-right:5px;margin-bottom:5px;" class="carrental_similar_car btn btn-warning">'+$('#carrental-similar-car-select option:selected').text()+' <a href="#" class="carrental_remove_similar_car">X</a><input type="hidden" name="similar_cars[]" value="'+$('#carrental-similar-car-select').val()+'"></span>');
			
		});
	
		jQuery("#additional-pictures-ul").sortable({
			handle: 'img',
			cursor: 'move'
		});
		jQuery("#additional-pictures-ul").disableSelection();

		jQuery(document).on('mouseover', "#additional-pictures-ul li", function() {
			jQuery(this).children('.buttons').show();
		});

		jQuery(document).on('mouseout', "#additional-pictures-ul li", function() {
			jQuery(this).children('.buttons').hide();
		});

		jQuery(document).on('click', "#additional-pictures-ul li .delete-button", function(event) {
			event.preventDefault();
			jQuery(this).parent().parent().remove();
		});

		var fixHelper = function(e, ui) {
			ui.children().each(function() {
				jQuery(this).width(jQuery(this).width());
			});
			return ui;
		};

		jQuery('a.move_button').click(function() {
			return false;
		});

		jQuery("table#carrental-fleet tbody").sortable({
			helper: fixHelper,
			cursor: 'move',
			handle: 'td.sortableTD',
			update: function(event, ui) {
				var newOrdering = jQuery(this).sortable('toArray', {attribute: 'fleetId'})
				jQuery.ajax({
					url: ajaxurl,
					global: false,
					type: "POST",
					data: ({
						action: 'carrental_save_fleet_order',
						ordering: newOrdering
					}),
					dataType: "script",
					async: true
				});
			}
		}).disableSelection();
	});
</script>
