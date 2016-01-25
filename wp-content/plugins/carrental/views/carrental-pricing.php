<div class="carrental-wrapper">
	
	<?php include CARRENTAL__PLUGIN_DIR . 'views/header.php'; ?>
	
	<div class="row">
		<div class="col-md-12 carrental-main-wrapper">
			<div class="carrental-main-content">
			
				<?php include CARRENTAL__PLUGIN_DIR . 'views/flash_msg.php'; ?>
				
				<div class="row">
					<div class="col-md-12">
						
						<?php if ($edit == true) { ?>
							<h3>Edit Pricing Scheme <?= $detail->name ?></h3>
						<?php } else { ?>
							<?php if (isset($_GET['deleted'])) { ?>
								<a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-pricing')); ?>" class="btn btn-default" style="float:right;">Show normal</a>
							<?php } else { ?>
								<a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-pricing')); ?>&amp;deleted" class="btn btn-default" style="float:right;">Show deleted</a>
							<?php } ?>
							
							<a href="javascript:void(0);" class="btn btn-success" id="carrental-pricing-add-button"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add new Pricing Scheme</a>
						<?php } ?>
						
						<div id="<?= (($edit == true) ? 'carrental-pricing-edit-form' : 'carrental-pricing-add-form') ?>" class="carrental-add-form">
							<form action="" method="post" role="form" class="form-horizontal" enctype="multipart/form-data">
								
								<div class="row">
									<div class="col-md-11">
										
										<!-- Type //-->
									  <div class="form-group">
									    <label class="col-sm-3 control-label">Type</label>
									    <div class="col-sm-9">
									    	<label class="radio-inline">
												  <input type="radio" name="type" value="1" <?= (($detail->type == 1) ? 'checked="checked"' : '') ?>>&nbsp;&nbsp;<strong><abbr title="This is a one time fee, which is paid once per rental and is not dependent on the number of days - typically a pick up or drop off service or similar.">One time</abbr></strong>
												</label>
												<label class="radio-inline">
												  <input type="radio" name="type" value="2" <?= (($detail->type == 2) ? 'checked="checked"' : '') ?>>&nbsp;&nbsp;<strong><abbr title="The price of which is directly dependent on the time for which it is used, this system requires inputting prices according to the extent of use e.g. 400 CZK/day for 1-10 days, 11-20 days 350 CZK/day, etc.">Time based</abbr></strong>
												</label>
											<p class="help-block">One time is a one time charge only; for pricing based on how long users rent, select Time based.</p>
									    </div>
									  </div>
									  
										<!-- Name //-->
									  <div class="form-group">
									    <label for="carrental-name" class="col-sm-3 control-label">Name</label>
									    <div class="col-sm-9">
									    	<input type="text" name="name" class="form-control" id="carrental-name" value="<?= (($edit == true) ? $detail->name : '') ?>">
									    </div>
									  </div>
									  
									  <!-- Default pricing currency //-->
									  <div class="form-group">
									    <label for="carrental-currency" class="col-sm-3 control-label">Default currency</label>
									    <div class="col-sm-9">
									    	<?php $currency = get_option('carrental_global_currency'); ?>
									    	<?php if ($currency && !empty($currency)) { ?>
										    	<select name="currency" id="carrental-currency" class="form-control">
										    		<option value="<?= $currency ?>"><?= $currency ?></option>
										    		<?php $av_currencies = unserialize(get_option('carrental_available_currencies')); ?>
										    		<?php if ($av_currencies && !empty($av_currencies)) { ?>
										    			<?php foreach ($av_currencies as $cc => $rate) { ?>
										    				<option value="<?= $cc ?>" <?php if ($edit == true && $cc == $detail->currency) { ?>selected<?php } ?>><?= $cc ?> (<?= $rate ?> &times; <?= $currency ?>)</option>	
										    			<?php } ?>
										    		<?php } ?>
									    		</select>
									    		
									    	<?php } else { ?>
									    		<p class="help-block">
														Please, set-up your <a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-settings')); ?>#global-settings">Global currency</a> first.
													</p>
									    	<?php } ?>
									    </div>
									  </div>
									  
									  <!-- One time price //-->
									  <div class="form-group type-onetime">
									    <label for="carrental-onetime-price" class="col-sm-3 control-label">One time price</label>
									    <div class="col-sm-9">
									    	<?php if ($currency && !empty($currency)) { ?>
									    		<div class="input-group">
									    			<input type="text" name="onetime_price" class="form-control" id="carrental-onetime-price" value="<?= (($edit == true) ? $detail->onetime_price : '') ?>">
									    			<span class="input-group-addon addon-currency"></span>
									    		</div>
									    	<?php } else { ?>
									    		<p class="help-block">
														Please, set-up your <a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-settings')); ?>#global-settings">Global currency</a> first.
													</p>
									    	<?php } ?>
									    </div>
									  </div>
									  
									  <!-- Time based - days //-->
									  <div class="form-group type-timerelated">
									    <label class="col-sm-3 control-label"><abbr title="Day ranges per day pricing (e.g. for 1-3 days of rental, price will be 20 USD/day; 4-5 days of rental, 18 USD/day etc.)">Day ranges per day pricing</abbr></label>
									    <div class="col-sm-9">
									    	<table class="table" id="carrental-day-range">
									    		<?php if (isset($detail->days) && !empty($detail->days)) { ?>
									    			<?php foreach ($detail->days as $key => $val) { ?>
									    				<tr>
											    			<td>&nbsp;<strong>From</strong>&nbsp;</td>
																<td><input type="text" name="days[from][]" class="form-control" size="2" placeholder="day no." value="<?= (($edit == true) ? $val['from'] : '') ?>"></td>
											    			<td>&nbsp;<strong><abbr title="For the infinite validity, leave the field blank."><span class="glyphicon glyphicon-question-sign"></span>&nbsp;&nbsp;To</abbr></strong>&nbsp;</td>
																<td><input type="text" name="days[to][]" class="form-control" size="2" placeholder="day no." value="<?= (($edit == true) ? $val['to'] : '') ?>"></td>
											    			<td>&nbsp;<strong>Price per day</strong>&nbsp;</td>
																<td>
																	<div class="input-group" style="width:150px;">
																	  <input type="text" name="days_price[]" class="form-control" placeholder="price" value="<?= (($edit == true) ? $val['price'] : '') ?>">
																		<span class="input-group-addon addon-currency"></span>
																	</div>
																</td>
															</tr>
									    			<?php } ?>
									    		<?php } ?>
									    	
									    		<tr id="day-range-row">
									    			<td>&nbsp;<strong>From</strong>&nbsp;</td>
														<td><input type="text" name="days[from][]" class="form-control" size="2" placeholder="day no."></td>
									    			<td>&nbsp;<strong><abbr title="For the infinite validity, leave the field blank."><span class="glyphicon glyphicon-question-sign"></span>&nbsp;&nbsp;To</abbr></strong>&nbsp;</td>
														<td><input type="text" name="days[to][]" class="form-control" size="2" placeholder="day no."></td>
									    			<td>&nbsp;<strong>Price per day</strong>&nbsp;</td>
														<td>
															<div class="input-group" style="width:150px;">
															  <input type="text" name="days_price[]" class="form-control" placeholder="price">
																<span class="input-group-addon addon-currency"></span>
															</div>
														</td>
													</tr>
													<tr id="day-range-row-before"><td colspan="6"></td></tr>
									    	</table>
										    <div id="carrental-dayrange-insert"></div>
										    <p class="help-block" id="days-range-help" style="color:tomato;">Warning! There might be an overlapse in the settings.</p>
												
												<a href="javascript:void(0);" id="carrental-add-day-range" class="btn btn-info btn-xs" title="Watch out for overlapses!">Add Day Range</a>
									    </div>
									  </div>
									  
									  <!-- Time based - hours //-->
									  <div class="form-group type-timerelated">
									    <label class="col-sm-3 control-label"><abbr title="Hour ranges per hour pricing (e.g. for 1-3 hours of rental, price will be 15 USD/hour; 4-6 hours of rental, 25 USD/hour etc.)">Hour ranges per hour pricing</abbr></label>
									    <div class="col-sm-9">
									    	<a href="javascript:void(0);" id="carrental-hour-range-box-show" class="btn btn-warning btn-xs">Setup the hour ranges</a>
									    	<div id="carrental-hour-range-box">
										    	<table class="table" id="carrental-hour-range">
														<?php if (isset($detail->hours) && !empty($detail->hours)) { ?>
										    			<?php foreach ($detail->hours as $key => $val) { ?>
										    				<tr>
												    			<td>&nbsp;<strong>From</strong>&nbsp;</td>
																	<td><input type="text" name="hours[from][]" class="form-control" size="2" placeholder="hour" value="<?= (($edit == true) ? $val['from'] : '') ?>"></td>
												    			<td>&nbsp;<strong>To</strong>&nbsp;</td>
																	<td><input type="text" name="hours[to][]" class="form-control" size="2" placeholder="hour" value="<?= (($edit == true) ? $val['to'] : '') ?>"></td>
												    			<td>&nbsp;<strong>Price per hour</strong>&nbsp;</td>
																	<td>
																		<div class="input-group" style="width:150px;">
																		  <input type="text" name="hours_price[]" class="form-control" placeholder="price" value="<?= (($edit == true) ? $val['price'] : '') ?>">
																			<span class="input-group-addon addon-currency"></span>
																		</div>
																	</td>
																</tr>
										    			<?php } ?>
										    		<?php } ?>
														<tr id="hour-range-row">
										    			<td>&nbsp;<strong>From</strong>&nbsp;</td>
															<td><input type="text" name="hours[from][]" class="form-control" size="2" placeholder="hour"></td>
										    			<td>&nbsp;<strong>To</strong>&nbsp;</td>
															<td><input type="text" name="hours[to][]" class="form-control" size="2" placeholder="hour"></td>
										    			<td>&nbsp;<strong>Price per hour</strong>&nbsp;</td>
															<td>
																<div class="input-group" style="width:150px;">
																  <input type="text" name="hours_price[]" class="form-control" placeholder="price">
																	<span class="input-group-addon addon-currency"></span>
																</div>
															</td>
														</tr>
														<tr id="hour-range-row-before"><td colspan="6"></td></tr>
										    	</table>
											    <div id="carrental-dayrange-insert"></div>
											    <p class="help-block" id="hours-range-help">Warning! There might be an overlapse in the settings.</p>
													<a href="javascript:void(0);" id="carrental-add-hour-range" class="btn btn-info btn-xs" title="Watch out for overlapses!">Add Hour Range</a>
												</div>
									    </div>
									  </div>
									  
									  <!-- Active on these days //-->
									  <div class="form-group">
									    <label for="carrental-promocode" class="col-sm-3 control-label">Pricing is active on these days</label>
									    <div class="col-sm-9">
									    	<?php
													if ($edit == true && !empty($detail->active_days)) {
														$days = explode(';', $detail->active_days);
													} else {
														$days = array(0,1,2,3,4,5,6);
													}
												?>
									    	<label class="radio-inline">Select all <input type="checkbox" class="form-control days-check-all" name="days_all" value="all" style="margin: -2px 0 0 4px;"></label>
									    	<label class="radio-inline">Monday <input type="checkbox" class="form-control days-check" name="active_days[]" value="1" <?php if (in_array(1, $days)) { ?>checked<?php } ?> style="margin: -2px 0 0 4px;"></label>
									    	<label class="radio-inline">Tuesday <input type="checkbox" class="form-control days-check" name="active_days[]" value="2" <?php if (in_array(2, $days)) { ?>checked<?php } ?> style="margin: -2px 0 0 4px;"></label>
									    	<label class="radio-inline">Wednesday <input type="checkbox" class="form-control days-check" name="active_days[]" value="3" <?php if (in_array(3, $days)) { ?>checked<?php } ?> style="margin: -2px 0 0 4px;"></label>
									    	<label class="radio-inline">Thursday <input type="checkbox" class="form-control days-check" name="active_days[]" value="4" <?php if (in_array(4, $days)) { ?>checked<?php } ?> style="margin: -2px 0 0 4px;"></label>
									    	<label class="radio-inline">Friday <input type="checkbox" class="form-control days-check" name="active_days[]" value="5" <?php if (in_array(5, $days)) { ?>checked<?php } ?> style="margin: -2px 0 0 4px;"></label>
									    	<label class="radio-inline">Saturday <input type="checkbox" class="form-control days-check" name="active_days[]" value="6" <?php if (in_array(6, $days)) { ?>checked<?php } ?> style="margin: -2px 0 0 4px;"></label>
									    	<label class="radio-inline">Sunday <input type="checkbox" class="form-control days-check" name="active_days[]" value="0" <?php if (in_array(0, $days)) { ?>checked<?php } ?> style="margin: -2px 0 0 4px;"></label>
									    </div>
									  </div>
									  
									  <!-- Max price //-->
									  <div class="form-group type-timerelated">
									    <label for="carrental-maxprice" class="col-sm-3 control-label">Max total price</label>
									    <div class="col-sm-9">
									    	<?php if ($currency && !empty($currency)) { ?>
										    	<div class="input-group">
													  <input type="text" name="maxprice" class="form-control" id="carrental-maxprice" value="<?= (($edit == true) ? $detail->maxprice : '') ?>">
										    		<span class="input-group-addon addon-currency"></span>
													</div>
													<p class="help-block">Max price that this scheme allows (will be set when calculated price reaches this amount).</p>
												<?php } else { ?>
									    		<p class="help-block">
														Please, set-up your <a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-settings')); ?>#global-settings">Global currency</a> first.
													</p>
									    	<?php } ?>
									    </div>
									  </div>
									  
									  
									  <!-- Promo code //-->
									  <div class="form-group">
									    <label for="carrental-promocode" class="col-sm-3 control-label">Promo code</label>
									    <div class="col-sm-9">
									    	<input type="text" name="promocode" class="form-control" id="carrental-promocode" value="<?= (($edit == true) ? $detail->promocode : '') ?>">
									    	<p class="help-block">If you fill out this field, this pricing scheme will be used ONLY if this promo code is inserted by client. To be active, it must be assigned to a specific vehicle and all criteria must match (days of the week, seasons, etc.).</p>
									    </div>
									  </div>
									  
									  <!-- Active //-->
									  <div class="form-group">
									    <label for="carrental-active" class="col-sm-3 control-label">Active</label>
									    <div class="col-sm-9">
									    	<label class="radio-inline">
												  <input type="radio" name="active" value="1" <?= (($detail->active == 1) ? 'checked="checked"' : '') ?>>&nbsp;&nbsp; Yes
												</label>
												<label class="radio-inline">
												  <input type="radio" name="active" value="0" <?= (($detail->active == 0) ? 'checked="checked"' : '') ?>>&nbsp;&nbsp; No
												</label>
									    </div>
									  </div>
									  
									  <!-- VAT //-->
									  <div class="form-group">
									    <label for="carrental-vat" class="col-sm-3 control-label">VAT</label>
									    <div class="col-sm-9">
									    	<div class="input-group">
												  <input type="text" name="vat" class="form-control" id="carrental-vat" value="<?= (($edit == true) ? $detail->vat : '') ?>">
									    		<span class="input-group-addon">%</span>
												</div>
									    </div>
									  </div>
									  
									  <!-- RATE ID //-->
									  <div class="form-group">
									    <label for="carrental-rate-id" class="col-sm-3 control-label">Rate ID</label>
									    <div class="col-sm-9">
									    	<div class="input-group">
												  <input type="text" name="rate_id" class="form-control" id="carrental-rate-id" value="<?= (($edit == true) ? $detail->rate_id : '') ?>">
												  <p class="help-block">If using TSDweb extension, insert your TSD rate ID here; else, use for internal records.</p>
												</div>
									    </div>
									  </div>
									  
									  <!-- Submit //-->
									  <div class="form-group">
									  	<div class="col-sm-offset-3 col-sm-9">
									  		<?php if ($edit == true) { ?>
									  			<input type="hidden" name="id_pricing" value="<?= $detail->id_pricing ?>">
									  			<button type="submit" class="btn btn-warning" name="add_pricing"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Confirm &amp; Save</button>
									  		<?php } else { ?>
									  			<button type="submit" class="btn btn-warning" name="add_pricing"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Confirm &amp; Add</button>
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
						
						<?php if (isset($pricing) && !empty($pricing)) { ?>
							<label class="label_select_all"><input type="checkbox" name="select_all" value="1" class="data_table_select_all" data-id="carrental-pricing" /> Select all</label>
							<table class="table table-striped" id="carrental-pricing">
					      <thead>
					        <tr>
					          <th>#</th>
					          <th>Type</th>
					          <th>Name</th>
					          <th>Price</th>
					          <th>Max. price</th>
					          <th>Pricing code</th>
					          <th>VAT</th>
					          <th>Usage</th>
					          <th>Action</th>
					        </tr>
					      </thead>
					      <tbody>
					      	<?php foreach ($pricing as $key => $val) { ?>
					      		<?php $total_usage = (int) $val->fleet_usage + (int) $val->extras_usage; ?>
					      		<tr>
						          <td>
						          	<input type="checkbox" class="input-control batch_processing" name="batch[]" value="<?= $val->id_pricing ?>" data-usage="<?= $total_usage ?>">&nbsp;
												<abbr title="Created: <?= $val->created ?>

<?= (!empty($val->updated) ? 'Updated: ' . $val->updated : '') ?>"><?= $val->id_pricing ?></abbr>
											</td>
											<td>
												<?php
													$btn_class = '';
													if ($val->active == 0) {
														$btn_class = 'btn-default';
													} elseif ($val->type == 1) {
														$btn_class = 'btn-info';
													} elseif ($val->type == 2) {
														$btn_class = 'btn-success';
													}
												?>
												<span class="btn btn-xs <?= $btn_class ?>"><?= (($val->type == 1) ? 'ONE TIME' : 'TIME BASED') ?></span>
											</td>
						          <td>
												<strong><?= (!empty($val->name) ? $val->name : '- Unknown -') ?></strong>
												<?php if ($val->active == 0) { ?>&nbsp;&nbsp;<em>(Inactive)</em><?php } ?>
											</td>
											<td>
												<?php if ($val->type == 1) { ?>
													<strong><?= $val->onetime_price ?>&nbsp;<?= $val->currency ?></strong>
												<?php } elseif ($val->type == 2) { ?>
													<a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-pricing')); ?>&get_day_ranges=<?= $val->id_pricing ?>" class="btn btn-xs btn-success carrental_show_ranges">Show ranges</a>
												<?php } ?>
											</td>
											<td>
												<?= (!empty($val->maxprice) ? $val->maxprice . '&nbsp;' . $val->currency : '&mdash;') ?>
											</td>
											<td>
												<?= (!empty($val->promocode) ? $val->promocode : '&mdash;') ?>
											</td>
											<td><?= $val->vat ?>%</td>
											<td><?= $total_usage ?> &times;</td>
						          <td>
												<form action="" method="post" class="form-inline" role="form" style="float: left;margin-right: 10px;">
													<div class="form-group">
														<a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-pricing')); ?>&amp;edit=<?= $val->id_pricing ?>" class="btn btn-xs btn-primary">Modify</a>
													</div>
												</form>
												
												<form action="" method="post" class="form-inline" role="form" style="float: left;margin-right: 10px;">
													<div class="form-group">
														<input type="hidden" name="id_pricing" value="<?= $val->id_pricing ?>">
														<button name="copy_pricing" class="btn btn-xs btn-warning">Copy</button>
													</div>
												</form>
												
												<?php if (isset($_GET['deleted'])) { ?>
													<form action="" method="post" class="form-inline" role="form" style="float: left;margin-right: 10px;" onsubmit="return confirm('<?= __('Do you really want to restore this Pricing scheme?', 'carrental') ?>');">
														<div class="form-group">
															<input type="hidden" name="id_pricing" value="<?= $val->id_pricing ?>">
															<button name="restore_pricing" class="btn btn-xs btn-success">Restore</button>
														</div>
													</form>
												<?php } else { ?>
													<?php if ($total_usage == 0) { ?>
														<form action="" method="post" class="form-inline" role="form" style="float: left;margin-right: 10px;" onsubmit="return confirm('<?= __('Do you really want to delete this Pricing scheme?', 'carrental') ?>');">
															<div class="form-group">
																<input type="hidden" name="id_pricing" value="<?= $val->id_pricing ?>">
																<button name="delete_pricing" class="btn btn-xs btn-danger">Delete</button>
															</div>
														</form>
													<?php } ?>
												<?php } ?>
											</td>
						        </tr>
						        
					      	<?php } ?>
					      </tbody>
					    </table>
					    <label class="label_select_all"><input type="checkbox" name="select_all" value="1" class="data_table_select_all" data-id="carrental-pricing" /> Select all</label>
					    <h4>Batch action on selected items</h4>
					    
					    <form action="" method="post" class="form" role="form" onsubmit="if (jQuery('[name=batch_processing_values]').val() == '') { alert('No Price scheme is selected to copy.'); return false }; return confirm('<?= __('Do you really want to copy selected Pricing schemes?', 'carrental') ?>');">
								<div class="form-group">
									<input type="hidden" name="batch_processing_values" value="">
									<button name="batch_copy_pricing" class="btn btn-warning">Copy <span class="batch_processing_count"></span>selected Pricing schemes</button>
								</div>
							</form>
							
					    <form action="" method="post" class="form" role="form" onsubmit="if (jQuery('[name=batch_processing_values_delete]').val() == '') { alert('No Price scheme is selected to delete.'); return false }; return confirm('<?= __('Do you really want to delete selected Pricing schemes?', 'carrental') ?>');">
								<div class="form-group">
									<input type="hidden" name="batch_processing_values_delete" value="">
									<button name="batch_delete_pricing" class="btn btn-danger">Delete <span class="batch_processing_count_delete"></span>selected Pricing schemes</button>
								</div>
							</form>
					    
						<?php } else { ?>
							<div class="alert alert-info">
								<span class="glyphicon glyphicon-info-sign"></span>&nbsp;&nbsp;
								<?= esc_html__( 'You do not have any Pricing schemes created yet, please create one clicking on "Add New Pricing Scheme".', 'carrental' ); ?>
							</div>
						<?php } ?>
					
					</div>
				</div>
				
					
			</div>
		</div>
	</div>
	
</div>

<script type="text/javascript">

	jQuery(document).ready(function() {
		
		<?php if ($edit == true) { ?>
			<?php if ($detail->type == 1) { ?>
				jQuery('.type-onetime').show();
				jQuery('.type-timerelated').hide();
				jQuery('#carrental-hour-range-box').hide();
			<?php } else { ?>
				jQuery('.type-onetime').hide();
				jQuery('.type-timerelated').show();
				<?php if (isset($detail->hours) && !empty($detail->hours)) { ?>
					jQuery('#carrental-hour-range-box').show();
				<?php } else { ?>
					jQuery('#carrental-hour-range-box').hide();
				<?php } ?>
			<?php } ?>
		<?php } else { ?>
			jQuery('.type-onetime').hide();
			jQuery('.type-timerelated').hide();
			jQuery('#carrental-hour-range-box').hide();
		<?php } ?>
		
	});

</script>