<!-- Ver 3.0.0 -->
<div class="carrental-wrapper">
	
	<div class="row">
	  <div class="col-md-12">
	  	<h2><?= __('Car Rental Theme settings', 'carrental') ?></h2>
		</div>
	</div>
	
	<br>
	
	<?php if (isset($_SESSION['carrental_flash_msg']) && !empty($_SESSION['carrental_flash_msg']) && !empty($_SESSION['carrental_flash_msg']['msg'])) { ?>
	
		<div class="row">
		  <div class="col-md-12">
		  	<div class="alert alert-<?= $_SESSION['carrental_flash_msg']['status'] ?> alert-dismissable">
		  		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		  		<span class="glyphicon glyphicon-<?= (($_SESSION['carrental_flash_msg']['status'] == 'success') ? 'ok' : 'remove') ?>"></span>&nbsp;&nbsp;
					<?= $_SESSION['carrental_flash_msg']['msg'] ?>
				</div>
			</div>
		</div>
		<br>
		<?php unset($_SESSION['carrental_flash_msg']); // Delete flash msg ?>
	<?php } ?>

	<div class="row">
		<div class="col-md-10">
			
			<!-- Automatic theme update //-->
			<div class="panel panel-default">
				<div class="panel-heading"><h4>Automatic theme update</h4></div>
				<div class="panel-body">
				  
					<div class="row">
						<div class="col-md-12">
							
							<div class="row">
								<div class="col-md-6">
								
									<?php $check = unserialize(get_option('carrental_theme_update_check')); ?>
								
								  <form action="" method="post" role="form" class="form-horizontal">
									  <div class="form-group">
									  	<label class="col-sm-3 control-label" style="padding-top:0;">Last check</label>
									    <div class="col-sm-9">
									    	
									    	<?php if (isset($check['last']) && strtotime($check['last']) != false) { ?>
													<?= Date('Y-m-d', strtotime($check['last'])) ?>
												<?php } else { ?>
													<em>- never -</em>
												<?php } ?>
												
												&nbsp;&nbsp;<button type="submit" name="check_theme_update" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-refresh"></span>&nbsp;&nbsp;Check theme update manually</button>
												
												<?php if (!isset($check['update_available']) || $check['update_available'] == false) { ?>
													<br><br>Current theme version: <strong><?= $theme->version ?></strong>
													<br><br><em>There is no new theme update available.</em>
												<?php } ?>
									    </div>
									  </div>
								  </form>
								  
								  <?php if (isset($check['update_available']) && $check['update_available'] == true) { ?>
									  <form action="" method="post" role="form" class="form-horizontal">
										  <div class="form-group">
										  	<label class="col-sm-3 control-label">Theme update is available!</label>
										    <div class="col-sm-9">
										    	
										    	Current version: <?= $theme->version ?><br>
													<strong>New version: <?= $check['new_version'] ?></strong> (<?= $check['new_version_date'] ?>)<br><br>
										    	
													<button type="submit" name="theme_update" class="btn btn-success"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Download, backup and install update</button>
													
													<br /><em>* Do not close this window while installing new version. Backup will be created automatically and saved under wp-content/themes/carrental/backup.</em>
													
										    </div>
										  </div>
									  </form>
								  <?php } ?>
								  
							  </div>
						  </div>
						  
						</div>
					</div>
				</div>
			</div>
				
			<div class="panel panel-default">
			  <div class="panel-body">
			    
			    <?php $pages = get_pages(); ?>
		 			
		 			<h4>Link plugin to pages</h4><br>
					  
		 			<form action="" class="form" role="form" method="post" enctype="multipart/form-data">
		 			
					  <div class="form-group">
					    <label for="our-cars-page" class="control-label">Plugin: Our cars</label>
							<select name="our_cars_page" id="our-cars-page" class="form-control">
								<option value="">- none -</option>
				        <?php foreach ($pages as $page) { ?>
				          <option value="<?= $page->ID ?>" <?php if (isset($theme_options['our_cars_page']) && $theme_options['our_cars_page'] == $page->ID) { ?>selected<?php } ?>><?= $page->post_title ?></option>
				        <?php } ?>
				    	</select>
				    	<p class="help-block">If you create the page manually, add only tag "<strong>[carrental_cars]</strong>" into the post.</p>
					  </div>
					  
					  <div class="form-group">
					    <label for="our-locations-page" class="control-label">Plugin: Our locations</label>
							<select name="our_locations_page" id="our-cars-page" class="form-control">
								<option value="">- none -</option>
				        <?php foreach ($pages as $page) { ?>
				          <option value="<?= $page->ID ?>" <?php if (isset($theme_options['our_locations_page']) && $theme_options['our_locations_page'] == $page->ID) { ?>selected<?php } ?>><?= $page->post_title ?></option>
				        <?php } ?>
				    	</select>
				    	<p class="help-block">If you create the page manually, add only tag "<strong>[carrental_locations]</strong>" into the post.</p>
					  </div>
					  
					  <div class="form-group">
					    <label for="manage-booking-page" class="control-label">Plugin: Manage booking</label>
							<select name="manage_booking_page" id="manage-booking-page" class="form-control">
								<option value="">- none -</option>
				        <?php foreach ($pages as $page) { ?>
				          <option value="<?= $page->ID ?>" <?php if (isset($theme_options['manage_booking_page']) && $theme_options['manage_booking_page'] == $page->ID) { ?>selected<?php } ?>><?= $page->post_title ?></option>
				        <?php } ?>
				    	</select>
				    	<p class="help-block">If you create the page manually, add only tag "<strong>[carrental_manage_booking]</strong>" into the post.</p>
					  </div>
					  
					  <br><h4>Other settings</h4><br>
					  
					  <!--
					  <div class="form-group">
					    <label for="front-slider-delay" class="control-label">Front slider delay</label>
					    <input type="text" name="front_slider_delay" class="form-control" id="front-slider-delay" placeholder="in seconds" value="<?php if (isset($theme_options['front_slider_delay'])) { echo $theme_options['front_slider_delay']; } ?>">
					  </div>!-->
					  
					  <div class="form-group">
					    <label for="phone-number" class="control-label">Reservation phone number</label>
					    <input type="text" name="phone_number" class="form-control" id="phone-number" placeholder="e.g. 000 800-100-200" value="<?php if (isset($theme_options['phone_number'])) { echo $theme_options['phone_number']; } ?>">
					  </div>
					  
					  <div class="form-group">
						  <label for="date-format" class="control-label">Date format</label>
						  <select name="date_format" id="date-format" class="form-control" style="width:200px;">
								<option value="yyyy-mm-dd"<?= ((isset($theme_options['date_format']) && $theme_options['date_format']  == 'yyyy-mm-dd') ? ' selected="selected"' : '') ?>>yyyy-mm-dd (2014-06-15)</option>
								<option value="dd.mm.yyyy"<?= ((isset($theme_options['date_format']) && $theme_options['date_format']  == 'dd.mm.yyyy') ? ' selected="selected"' : '') ?>>dd.mm.yyyy (15.06.2014)</option>
								<option value="mm/dd/yyyy"<?= ((isset($theme_options['date_format']) && $theme_options['date_format']  == 'mm/dd/yyyy') ? ' selected="selected"' : '') ?>>mm/dd/yyyy (06/15/2014)</option>
								<option value="dd-M-yyyy"<?= ((isset($theme_options['date_format']) && $theme_options['date_format']  == 'dd-M-yyyy') ? ' selected="selected"' : '') ?>>dd-M-yyyy (15-Jun-2014)</option>
								<option value="M-dd-yyyy"<?= ((isset($theme_options['date_format']) && $theme_options['date_format']  == 'M-dd-yyyy') ? ' selected="selected"' : '') ?>>M-dd-yyyy (Jun-15-2014)</option>
						  </select>
					  </div>
					  
					  <div class="form-group">
						  <label for="time-format" class="control-label">Time format</label>
						  <select name="time_format" id="time-format" class="form-control" style="width:200px;">
								<option value="24"<?= ((isset($theme_options['time_format']) && $theme_options['time_format']  == '24') ? ' selected="selected"' : '') ?>>24 hours</option>
								<option value="12"<?= ((isset($theme_options['time_format']) && $theme_options['time_format']  == '12') ? ' selected="selected"' : '') ?>>12 hours</option>
						  </select>
					  </div>
					  
					  <div class="form-group">
						  <label for="date-format-first-day" class="control-label">First day of the week</label>
						  <select name="date_format_first_day" id="date-format-first-day" class="form-control" style="width:150px;">
								<option value="0"<?= ((isset($theme_options['date_format_first_day']) && (int)$theme_options['date_format_first_day']  == 0) ? ' selected="selected"' : '') ?>>Sunday</option>
								<option value="1"<?= ((isset($theme_options['date_format_first_day']) && (int)$theme_options['date_format_first_day']  == 1) ? ' selected="selected"' : '') ?>>Monday</option>
						  </select>
					  </div>
					  
					  <div class="form-group">
						  <label for="car-available-button" class="control-label">Display car available button</label>
						  <select name="car_available_button" id="car-available-button" class="form-control" style="width:100px;">
								<option value="0"<?= ((isset($theme_options['car_available_button']) && $theme_options['car_available_button']  == 0) ? ' selected="selected"' : '') ?>>No</option>
								<option value="1"<?= ((isset($theme_options['car_available_button']) && $theme_options['car_available_button']  == 1) ? ' selected="selected"' : '') ?>>Yes</option>
						  </select>
					  </div>
					  
					  <div class="form-group">
						  <label for="show-breadcrumb" class="control-label">Show breadcrumb</label>
						  <select name="show_breadcrumb" id="show-breadcrumb" class="form-control" style="width:100px;">
							  <option value="1"<?= ((isset($theme_options['show_breadcrumb']) && $theme_options['show_breadcrumb']  == 1) ? ' selected="selected"' : '') ?>>Yes</option>
							  <option value="0"<?= ((isset($theme_options['show_breadcrumb']) && $theme_options['show_breadcrumb']  == 0) ? ' selected="selected"' : '') ?>>No</option>
						  </select>
					  </div>
					  
					  <div class="form-group">
						  <label for="car-default-sort-by" class="control-label">Default sort by value</label>
						  <select name="default_sort_by" id="car-default-sort-by" class="form-control" style="width:100px;">
							    <option value="ordering"<?= ((isset($theme_options['default_sort_by']) && $theme_options['default_sort_by']  == 'ordering') ? ' selected="selected"' : '') ?>>Admin order</option>
								<option value="name"<?= ((isset($theme_options['default_sort_by']) && $theme_options['default_sort_by']  == 'name') ? ' selected="selected"' : '') ?>>Name</option>
								<option value="price"<?= ((isset($theme_options['default_sort_by']) && $theme_options['default_sort_by']  == 'price') ? ' selected="selected"' : '') ?>>Price</option>
						  </select>
					  </div>
					  
					  <br><h4>Pictures</h4><br>
					  <div class="form-group">
					    <h4><label for="carrental-picture-hp" class="control-label">Homepage picture</label></h4>
					    <?php if (isset($theme_options['picture_homepage'])) { ?>
				    		<div class="panel panel-info">
								  <div class="panel-heading">Current picture</div>
								  <div class="panel-body">
								    <p><img src="<?= $theme_options['picture_homepage'] ?>" height="80"></p>
								  </div>
								</div>
								<p><strong>Or you can upload new picture:</strong></p>
								<input type="hidden" name="current_picture_homepage" value="<?= $theme_options['picture_homepage'] ?>">
				  		<?php } ?>
				    	<input type="file" name="picture_homepage" id="carrental-picture-hp">
				    	<p class="help-block">Insert picture 1100px x 342px.</p>
				    	<p><strong>Or you can delete current homepage picture:</strong></p>
							<label><input type="checkbox" class="input-control" name="delete_picture_homepage" value="1">&nbsp;&nbsp;Delete picture</label>
					  </div>
					  
					  <div class="form-group">
					    <h4><label for="carrental-picture-other" class="control-label">Other pages picture</label></h4>
					    <?php if (isset($theme_options['picture_otherpages'])) { ?>
				    		<div class="panel panel-info">
								  <div class="panel-heading">Current picture</div>
								  <div class="panel-body">
								    <p><img src="<?= $theme_options['picture_otherpages'] ?>" height="80"></p>
								  </div>
								</div>
								<p><strong>Or you can upload new picture:</strong></p>
								<input type="hidden" name="current_picture_otherpages" value="<?= $theme_options['picture_otherpages'] ?>">
				  		<?php } ?>
				    	<input type="file" name="picture_otherpages" id="carrental-picture-otherpages">
				    	<p class="help-block">Insert picture 1100px x 160px.</p>
				    	<p><strong>Or you can delete current picture:</strong></p>
							<label><input type="checkbox" class="input-control" name="delete_picture_otherpages" value="1">&nbsp;&nbsp;Delete picture</label>
					  </div>
					  
					  <div class="form-group">
					    <h4><label for="carrental-picture-hp" class="control-label">Logo picture</label></h4>
					    <?php if (isset($theme_options['picture_logo'])) { ?>
				    		<div class="panel panel-info">
								  <div class="panel-heading">Current picture</div>
								  <div class="panel-body">
								    <p><img src="<?= $theme_options['picture_logo'] ?>" height="80"></p>
								  </div>
								</div>
								<p><strong>Or you can upload new picture:</strong></p>
								<input type="hidden" name="current_picture_logo" value="<?= $theme_options['picture_logo'] ?>">
				  		<?php } ?>
				    	<input type="file" name="picture_logo" id="carrental-picture-hp">
				    	<p class="help-block">Insert picture at maximum height 80px.</p>
				    	<p><strong>Or you can delete current logo picture:</strong></p>
							<label><input type="checkbox" class="input-control" name="delete_picture_logo" value="1">&nbsp;&nbsp;Delete picture</label>
					  </div>
					  
					  <br><h4>Translations</h4><br>
					  <div class="form-group">
					  	<p>
								There are <strong><?= $translations ?> strings</strong> found in the template. You can translate it in the Car Rental plugin Translations section.
							</p>
						</div>
					  
					  <br><h4>Filters in search results</h4><br>
					  
					  <div class="form-group">
					    <div class="checkbox">
				        <label>
				          <input type="checkbox" name="filter_price_range" value="1" <?php if (isset($theme_options['filter_price_range']) && $theme_options['filter_price_range'] == 1) { ?>checked<?php } ?>> Show filter: Price range
				        </label>
				      </div>
					  </div>
					  
					  <div class="form-group">
				      <div class="checkbox">
						  <p><strong>Filter price range</strong></p>
						  <label>min. price:</label> <input style="width:75px;" type="text" name="filter_price_range_min" value="<?php echo (isset($theme_options['filter_price_range_min']) && (int)$theme_options['filter_price_range_min'] >= 0) ? (int)$theme_options['filter_price_range_min'] : 0; ?>">
						  <label style="margin-left:10px;">max. price:</label> <input style="width:75px;" type="text" name="filter_price_range_max" value="<?php echo (isset($theme_options['filter_price_range_max']) && (int)$theme_options['filter_price_range_max'] >= 0) ? (int)$theme_options['filter_price_range_max'] : 500; ?>">
						  <p class="help-block">This sets the maximum and minimum values of your price filter shown on the front end. Insert values in your default currency. When clients change currency, values will automatically recalculate according to exchange rates set in settings.</p>
				      </div>
					  </div>
					  
  					<div class="form-group">
				      <div class="checkbox">
				        <label>
				          <input type="checkbox" name="filter_extras" value="1" <?php if (isset($theme_options['filter_extras']) && $theme_options['filter_extras'] == 1) { ?>checked<?php } ?>> Show filter: Extras
				        </label>
				      </div>
					  </div>
					  
					  <div class="form-group">
				      <div class="checkbox">
				        <label>
				          <input type="checkbox" name="filter_fuel" value="1" <?php if (isset($theme_options['filter_fuel']) && $theme_options['filter_fuel'] == 1) { ?>checked<?php } ?>> Show filter: Fuel
				        </label>
					    </div>
					  </div>
					  
					  <div class="form-group">
				      <div class="checkbox">
				        <label>
				          <input type="checkbox" name="filter_passengers" value="1" <?php if (isset($theme_options['filter_passengers']) && $theme_options['filter_passengers'] == 1) { ?>checked<?php } ?>> Show filter: Number of passengers
				        </label>
				      </div>
					  </div>
					  
					  <div class="form-group">
				      <div class="checkbox">
						  <p><strong>Filter Number of passengers</strong></p>
						  <label>min. number of passengers:</label> <input style="width:75px;" type="text" name="filter_passengers_range_min" value="<?php echo (isset($theme_options['filter_passengers_range_min']) && (int)$theme_options['filter_passengers_range_min'] >= 0) ? (int)$theme_options['filter_passengers_range_min'] : 1; ?>">
						  <label style="margin-left:10px;">max. number of passengers:</label> <input style="width:75px;" type="text" name="filter_passengers_range_max" value="<?php echo (isset($theme_options['filter_passengers_range_max']) && (int)$theme_options['filter_passengers_range_max'] >= 0) ? (int)$theme_options['filter_passengers_range_max'] : 8; ?>">
						  <p class="help-block">This sets the maximum and minimum values of your "number of passengers" filter shown on the front end.</p>
				      </div>
					  </div>
					  
					  <div class="form-group">
				      <div class="checkbox">
				        <label>
				          <input type="checkbox" name="filter_vehicle_names" value="1" <?php if (isset($theme_options['filter_vehicle_names']) && $theme_options['filter_vehicle_names'] == 1) { ?>checked<?php } ?>> Show filter: Vehicle names
				        </label>
				      </div>
					  </div>
  					
  					<div class="form-group">
				      <div class="checkbox">
				        <label>
				          <input type="checkbox" name="filter_vehicle_categories" value="1" <?php if (isset($theme_options['filter_vehicle_categories']) && $theme_options['filter_vehicle_categories'] == 1) { ?>checked<?php } ?>> Show filter: Vehicle categories
				        </label>
				      </div>
					  </div>
					  
					  <br>
					  
					  <div class="form-group">
					    <button type="submit" class="btn btn-warning" name="save_theme_settings"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Confirm &amp; Save</button>
					  </div>
					</form>

			    
			  </div>
			</div>

			
			

		</div>
	</div>
	
</div>