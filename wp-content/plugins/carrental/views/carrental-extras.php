<div class="carrental-wrapper">
	
	<?php include CARRENTAL__PLUGIN_DIR . 'views/header.php'; ?>
	
	<div class="row">
	
		<div class="col-md-12 carrental-main-wrapper">
			<div class="carrental-main-content">
				
				<?php include CARRENTAL__PLUGIN_DIR . 'views/flash_msg.php'; ?>
				
				<div class="row">
					<div class="col-md-12">
						
						<?php if ($edit == true) { ?>
							<h3>Edit item: <?= $detail->name_admin == '' ? $detail->name : $detail->name_admin ?></h3>
						<?php } else { ?>
							<?php if (isset($_GET['deleted'])) { ?>
								<a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-extras')); ?>" class="btn btn-default" style="float:right;">Show normal</a>
							<?php } else { ?>
								<a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-extras')); ?>&amp;deleted" class="btn btn-default" style="float:right;">Show deleted</a>
							<?php } ?>
							
							<a href="javascript:void(0);" class="btn btn-success" id="carrental-extras-add-button"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add new item or service</a>
						<?php } ?>
						
						<div id="<?= (($edit == true) ? 'carrental-extras-edit-form' : 'carrental-extras-add-form') ?>" class="carrental-add-form">
							<form action="" method="post" role="form" class="form-horizontal" enctype="multipart/form-data">
								
								<div class="row">
									<div class="col-md-11">
										
										<div class="alert alert-info">
											<p><span class="glyphicon glyphicon-share-alt"></span>&nbsp;&nbsp;Whichever field is left blank will not be used in item or service description.</p>
											<p><span class="glyphicon glyphicon-share-alt"></span>&nbsp;&nbsp;Manage your <a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-pricing')); ?>">Pricing schemes</a> first.</p>
										</div>
										
										 <!-- Name admin //-->
									  <div class="form-group">
									    <label for="carrental-name-admin" class="col-sm-3 control-label">Internal name for administration</label>
									     <div class="col-sm-9">
									    	<input type="text" name="name_admin" class="form-control" id="carrental-name-admin" value="<?= (($edit == true) ? $detail->name_admin : '') ?>">
									    </div>
									  </div>
									
										<div class="form-group">
											<div class="col-sm-3"></div>
											<div class="col-sm-9">
												<ul class="nav nav-tabs" role="tablist" style="margin-bottom:10px;">
													<li role="presentation" class="active"><a href="javascript:void(0);" class="edit_extras_name_desc" data-value="gb">English (GB)</a></li>
													<?php $available_languages = unserialize(get_option('carrental_available_languages')); ?>
													<?php if ($available_languages && !empty($available_languages)) { ?>														
														<?php foreach ($available_languages as $key => $val) { ?>
															<?php if ($val['country-www'] == 'gb') {continue;} ?>
															<li role="presentation"><a href="javascript:void(0);" class="edit_extras_name_desc" data-value="<?= strtolower($val['country-www']) ?>"><?= $val['lang'] ?> (<?= strtoupper($val['country-www']) ?>)</a></li>
														<?php } ?>
													<?php } ?>
												</ul>
											</div>
										</div>
										
									  <!-- Name //-->
									  <div class="form-group">
									    <label for="carrental-name" class="col-sm-3 control-label">Name</label>
									    <div class="col-sm-9">
											<div class="carrental_extras_translations" data-lng="gb">
												<input type="text" name="name" class="form-control" id="carrental-name" placeholder="GPS, Additional Driver, ..." value="<?= (($edit == true) ? $detail->name : '') ?>">
											</div>
											<?php if ($edit == true) { ?>
												<?php $name_translations = unserialize($detail->name_translations);?>
												<?php if (empty($name_translations)) { $name_translations = array(); } ?>
											<?php } ?>
											<?php if ($available_languages && !empty($available_languages)) { ?>														
												<?php foreach ($available_languages as $key => $val) { ?>
													<?php if ($val['country-www'] == 'gb') {continue;} ?>
														<div class="carrental_extras_translations" data-lng="<?php echo $val['country-www'];?>" style="display:none;">
															<input type="text" name="name_translations[<?php echo $val['country-www'];?>]" placeholder="Name in <?php echo $val['lang-native'];?>" class="form-control" value="<?= (($edit == true && isset($name_translations[$val['country-www']])) ? $name_translations[$val['country-www']] : '') ?>">
														</div>
												<?php } ?>
											<?php } ?>
									    </div>
									  </div>
									  
									  <!-- Description //-->
									  <div class="form-group">
									    <label for="carrental-description" class="col-sm-3 control-label">Description</label>
									    <div class="col-sm-9">
											<div class="carrental_extras_translations" data-lng="gb">
												<textarea class="form-control" name="description" id="carrental-description" rows="3"><?= (($edit == true) ? CarRental::removeslashes($detail->description) : '') ?></textarea>
											</div>
											<?php if ($edit == true) { ?>
												<?php $description_translations = unserialize($detail->description_translations);?>
												<?php if (empty($description_translations)) { $description_translations = array(); } ?>
											<?php } ?>
											<?php if ($available_languages && !empty($available_languages)) { ?>														
												<?php foreach ($available_languages as $key => $val) { ?>
													<?php if ($val['country-www'] == 'gb') {continue;} ?>
														<div class="carrental_extras_translations" data-lng="<?php echo $val['country-www'];?>" style="display:none;">
															<textarea class="form-control" name="description_translations[<?php echo $val['country-www'];?>]" placeholder="Description in <?php echo $val['lang-native'];?>" rows="3"><?= (($edit == true && isset($description_translations[$val['country-www']])) ? $description_translations[$val['country-www']] : '') ?></textarea>
														</div>
												<?php } ?>
											<?php } ?>
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
																  <div class="col-xs-5">
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
														    	<div class="col-xs-1">
														    		<span class="glyphicon glyphicon-sort" style="margin-top:9px;cursor:move;" title="Move up or down to sort Price scheme. Highest priority first!"></span>
																  </div>														
													    	</div><!-- .row //-->
											    	
															<?php } ?>
														<?php } ?>
										    		
										    	<div id="carrental-prices">
										    		<!-- Price scheme row //-->
										    		<div class="row" style="position: relative;" class="sortable">
														  <div class="col-xs-5">
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
												    	<div class="col-xs-1">
												    		<span class="glyphicon glyphicon-sort" style="margin-top:9px;cursor:move;" title="Move up or down to sort Price scheme. Highest priority first!"></span>
														  </div>														
											    	</div><!-- .row //-->
												  </div>
											    
													<div id="carrental-prices-insert"></div>
												</div>
									    	<a href="javascript:void(0);" id="carrental-add-pricing-scheme" class="btn btn-info btn-xs">Add Pricing Scheme</a>
									    </div>
									  </div>
									  
									  <!-- Mandatory //-->
									  <div class="form-group">
									    <label for="carrental-mandatory" class="col-sm-3 control-label">Mandatory</label>
									    <div class="col-sm-9">
									    	<label class="radio-inline">
												  <input type="radio" name="mandatory" value="1" <?= (($detail->mandatory == 1) ? 'checked="checked"' : '') ?>>&nbsp;&nbsp; Yes
												</label>
												<label class="radio-inline">
												  <input type="radio" name="mandatory" value="0" <?= (($detail->mandatory == 0) ? 'checked="checked"' : '') ?>>&nbsp;&nbsp; No
												</label>
									    </div>
									  </div> 
									  
									  <!-- Internal ID //-->
									  <div class="form-group">
									    <label for="carrental-internal_id" class="col-sm-3 control-label">Internal item or service ID</label>
									    <div class="col-sm-9">
									    	<input type="text" name="internal_id" class="form-control" id="carrental-internal_id" value="<?= (($edit == true) ? $detail->internal_id : '') ?>">
									    </div>
									  </div>
									  
									  <!-- Additional Drivers //-->
									  <div class="form-group">
									    <label for="carrental-max_drivers" class="col-sm-3 control-label">Maximum Additional Drivers</label>
									    <div class="col-sm-9">
									    	<input type="text" name="max_additional_drivers" class="form-control" id="carrental-max_drivers" value="<?= (($edit == true) ? $detail->max_additional_drivers : '') ?>">
									    	<p class="help-block">
													This is a special field. If you want to use this item as a function "<strong>Additional Driver</strong>" in the booking process, insert maximum of additional drivers available (0 = disabled function).
													<br>Every driver will be charched based on price scheme individually.
												</p>
											</div>
									  </div>
									  
									  <!-- Picture of item or service //-->
									  <div class="form-group">
									    <label for="carrental-picture" class="col-sm-3 control-label">Picture of item or service</label>
									    <div class="col-sm-9">
									    	<?php if ($edit == true) { ?>
									    		<div class="panel panel-info">
													  <div class="panel-heading">Current picture</div>
													  <div class="panel-body">
													    <p><img src="<?= $detail->picture ?>" height="80"></p>
													  </div>
													</div>
													<p><strong>Or you can upload new picture for Branch:</strong></p>
									  		<?php } ?>
									    	<input type="file" name="picture" id="carrental-picture">
									    	<p class="help-block">Insert picture of the item or service, 400x400px.</p>
									    </div>
									  </div>
									  
									  <!-- Submit //-->
									  <div class="form-group">
									  	<div class="col-sm-offset-3 col-sm-9">
									  		<?php if ($edit == true) { ?>
									  			<input type="hidden" name="id_extras" value="<?= $detail->id_extras ?>">
									  			<input type="hidden" name="current_picture" value="<?= $detail->picture ?>">
									  			<button type="submit" class="btn btn-warning" name="add_extras"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Confirm &amp; Save</button>
									  		<?php } else { ?>
									  			<button type="submit" class="btn btn-warning" name="add_extras"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Confirm &amp; Add</button>
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
						
						<?php if (isset($extras) && !empty($extras)) { ?>
						<label class="label_select_all"><input type="checkbox" name="select_all" value="1" class="data_table_select_all" data-id="carrental-extras" /> Select all</label>
							<table class="table table-striped" id="carrental-extras">
					      <thead>
					        <tr>
					          <th>#</th>
					          <th>Image</th>
					          <th>Name</th>
					          <th>Pricing schemes</th>
					          <th>Internal ID</th>
					          <th>Description</th>
					          <th>Action</th>
					        </tr>
					      </thead>
					      <tbody>
								<?php foreach ($extras as $key => $val) { ?>
				      		<tr>
					          <td>
					          	<input type="checkbox" class="input-control batch_processing" name="batch[]" value="<?= $val->id_extras ?>">&nbsp;
											<abbr title="Created: <?= $val->created ?>

<?= (!empty($val->updated) ? 'Updated: ' . $val->updated : '') ?>"><?= $val->id_extras ?></abbr>
										</td>
					          <td><img src="<?= $val->picture ?>" height="120"></td>
					          <td><strong><?= ($val->name_admin == '' ? (!empty($val->name) ? $val->name : '- Unknown -') : $val->name_admin) ?></strong></td>
										<td>
											<?php if (!empty($val->pricing_name)) { ?>
												<p><a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-pricing')); ?>&amp;<?= (($val->pricing_type == 1) ? 'get_onetime_price' : 'get_day_ranges') ?>=<?= $val->global_pricing_scheme ?>" class="btn <?= (($val->pricing_type == 1) ? 'btn-info' : 'btn-success') ?> carrental_show_ranges"><?= $val->pricing_name ?></a></p>
												<?php if ($val->pricing_count > 0) { ?>
													<p><a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-pricing')); ?>&amp;get_extras_price_schemes=<?= $val->id_extras ?>" class="btn <?= (($val->pricing_type == 1) ? 'btn-info' : 'btn-success') ?> carrental_show_ranges">+ <?= $val->pricing_count ?> schemes</a></p>
												<?php } ?>
											<?php } else { ?>
												<p><em>- none -</em></p>
											<?php } ?>
										</td>
										<td><?= (!empty($val->internal_id) ? $val->internal_id : '<p><em>- empty -</em></p>') ?></td>
										<td><p style="max-width:200px;"><?= (!empty($val->description) ? $val->description : '<em>- empty -</em>') ?></p></td>
										<td>
											<form action="" method="post" class="form" role="form">
												<div class="form-group">
													<a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-extras')); ?>&amp;edit=<?= $val->id_extras ?>" class="btn btn-primary btn-block">Modify</a>
												</div>
											</form>
											<form action="" method="post" class="form" role="form">
												<div class="form-group">
													<input type="hidden" name="id_extras" value="<?= $val->id_extras ?>">
													<button name="copy_extras" class="btn btn-warning btn-block">Copy</button>
												</div>
											</form>
											<?php if (isset($_GET['deleted'])) { ?>
												<form action="" method="post" class="form" role="form" onsubmit="return confirm('<?= __('Do you really want to restore this Extras?', 'carrental') ?>');">
													<div class="form-group">
														<input type="hidden" name="id_extras" value="<?= $val->id_extras ?>">
														<button name="restore_extras" class="btn btn-success btn-block">Restore</button>
													</div>
												</form>
											<?php } else { ?>
												<form action="" method="post" class="form" role="form" onsubmit="return confirm('<?= __('Do you really want to delete this Extras?', 'carrental') ?>');">
													<div class="form-group">
														<input type="hidden" name="id_extras" value="<?= $val->id_extras ?>">
														<button name="delete_extras" class="btn btn-danger btn-block">Delete</button>
													</div>
												</form>
											<?php } ?>
										</td>
					        </tr>
				      	<?php } ?>
					    	</tbody>
					  	</table>
						<label class="label_select_all"><input type="checkbox" name="select_all" value="1" class="data_table_select_all" data-id="carrental-extras" /> Select all</label>
					  	
					    <h4>Batch action on selected items</h4>
					    
					    <form action="" method="post" class="form" role="form" onsubmit="if (jQuery('[name=batch_processing_values]').val() == '') { alert('No Item is selected to copy.'); return false }; return confirm('<?= __('Do you really want to copy selected Items?', 'carrental') ?>');">
								<div class="form-group">
									<input type="hidden" name="batch_processing_values" value="">
									<button name="batch_copy_extras" class="btn btn-warning">Copy <span class="batch_processing_count"></span>selected Items</button>
								</div>
							</form>
							
					    <form action="" method="post" class="form" role="form" onsubmit="if (jQuery('[name=batch_processing_values]').val() == '') { alert('No Item is selected to delete.'); return false }; return confirm('<?= __('Do you really want to delete selected Items?', 'carrental') ?>');">
								<div class="form-group">
									<input type="hidden" name="batch_processing_values" value="">
									<button name="batch_delete_extras" class="btn btn-danger">Delete <span class="batch_processing_count"></span>selected Items</button>
								</div>
							</form>
							
						<?php } else { ?>
							<div class="alert alert-info">
								<span class="glyphicon glyphicon-info-sign"></span>&nbsp;&nbsp;
								<?= esc_html__( 'You do not have any Extras created yet, please create one clicking on "Add New Item or Service".', 'carrental' ); ?>
							</div>
						<?php } ?>
						
					</div>
				</div>
				
				
				
			</div>
		</div>
	</div>
	
</div>
