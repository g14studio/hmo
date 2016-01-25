<div class="carrental-wrapper">
	
	<?php include CARRENTAL__PLUGIN_DIR . 'views/header.php'; ?>
	
	<div class="row">
		<div class="col-md-12 carrental-main-wrapper">
			<div class="carrental-main-content">
				
				<?php include CARRENTAL__PLUGIN_DIR . 'views/flash_msg.php'; ?>
				
				<!-- Automatic upgrade //-->
				<div class="panel panel-default">
					<div class="panel-heading"><h4>Automatic plugin update</h4></div>
					<div class="panel-body">
					  
						<div class="row">
							<div class="col-md-12">
								<?php $check = unserialize(get_option('carrental_update_check')); ?>
								<?php $apikey = unserialize(get_option('carrental_api_key')); ?>
								<?php $apikey_exp = get_option('carrental_api_key_expiration'); ?>
								<div class="row">
									<div class="col-md-6">
										
										<?php if ($apikey && !empty($apikey) && isset($apikey['api_key']) && isset($apikey['date'])) { ?>
											<form role="form" class="form-horizontal">
												<div class="form-group">
											  	<label class="col-sm-3 control-label" style="padding-top:0;">Your API key</label>
											    <div class="col-sm-9">
														<p>
															You saved your key on <?= Date('Y-m-d', strtotime($apikey['date'])) ?>.
															<?php if (isset($apikey_exp) && strtotime($apikey_exp) != false) { ?>
																<br>The key is valid until <?= Date('Y-m-d', strtotime($apikey_exp)) ?>.
															<?php } ?>
															<br><a href="javascript:void(0);" onclick="jQuery('.form-insert-api-key').toggle('fast');">Insert new API key</a>
														</p>
													</div>
												</div>
											</form>
										<?php } ?>
										
										<form action="<?= CarRental_Admin::get_page_url('carrental-settings') ?>" method="post" role="form" class="form-horizontal form-insert-api-key" <?php if ($apikey && !empty($apikey) && isset($apikey['api_key'])) { ?>style="display:none;"<?php } ?>>
											<div class="form-group">
										    <label for="carrental_api" class="col-sm-3 control-label" style="padding-top:0;">Insert API key</label>
										    <div class="col-sm-9">
										    	<input type="text" name="api_key" id="carrental_api" class="form-control" value="" placeholder="Your API key (from plugin provider).">
										    </div>
										  </div>
										  
										  <div class="form-group">
											  <div class="col-sm-offset-3 col-sm-9">
													<button type="submit" name="save_api_key" class="btn btn-warning"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Save API key</button>
												</div>
										  </div>
									  </form>
									  
									  <form action="<?= CarRental_Admin::get_page_url('carrental-settings') ?>" method="post" role="form" class="form-horizontal">
										  <div class="form-group">
										  	<label class="col-sm-3 control-label" style="padding-top:0;">Last check</label>
										    <div class="col-sm-9">
										    
										    	<?php if (isset($check['last']) && strtotime($check['last']) != false) { ?>
														<?= Date('Y-m-d', strtotime($check['last'])) ?>
													<?php } else { ?>
														<em>- never -</em>
													<?php } ?>
													
													&nbsp;&nbsp;<button type="submit" name="check_plugin_update" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-refresh"></span>&nbsp;&nbsp;Check plugin update manually</button>
													
													<?php if (!isset($check['update_available']) || $check['update_available'] == false) { ?>
														<br><br>Current plugin version: <strong><?= CARRENTAL_VERSION ?></strong>
														<br><br><em>There is no new plugin update available.</em>
													<?php } ?>
										    </div>
										  </div>
									  </form>
										
									  <?php if (isset($check['update_available']) && $check['update_available'] == true) { ?>
										  <form action="<?= CarRental_Admin::get_page_url('carrental-settings') ?>" method="post" role="form" class="form-horizontal">
											  <div class="form-group">
											  	<label class="col-sm-3 control-label">Plugin update is available!</label>
											    <div class="col-sm-9">
											    	
											    	Current version: <?= CARRENTAL_VERSION ?><br>
														<strong>New version: <?= $check['new_version'] ?></strong> (<?= $check['new_version_date'] ?>)<br><br>
											    	
														<button type="submit" name="plugin_update" class="btn btn-success"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Download, backup and install update</button>
														
														<br /><em>* Do not close this window while installing new version. Backup will be created automatically and saved under wp-content/plugins/carrental/backup.</em>
														
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
					
					
				<!-- INFO //-->
				<div class="panel panel-default">
					<div class="panel-heading"><h4>Company information</h4></div>
					<div class="panel-body">
					  <form action="" method="post" role="form" class="form-horizontal" enctype="multipart/form-data">
							<?php $company = unserialize(get_option('carrental_company_info')); ?>
							
							<div class="row">
								<div class="col-md-4">
									
									<div class="form-group">
								    <label for="carrental_company_name" class="col-sm-3 control-label">Name</label>
								    <div class="col-sm-9">
								    	<input type="text" name="name" class="form-control" id="carrental_company_name" value="<?= (isset($company['name']) ? $company['name'] : '') ?>">
								    </div>
								  </div>
								  
								  <div class="form-group">
								    <label for="carrental_company_id" class="col-sm-3 control-label">ID no.</label>
								    <div class="col-sm-9">
								    	<input type="text" name="id" class="form-control" id="carrental_company_id" value="<?= (isset($company['id']) ? $company['id'] : '') ?>">
								    </div>
								  </div>
								  
								  <div class="form-group">
								    <label for="carrental_company_vat" class="col-sm-3 control-label">VAT no.</label>
								    <div class="col-sm-9">
								    	<input type="text" name="vat" class="form-control" id="carrental_company_vat" value="<?= (isset($company['vat']) ? $company['vat'] : '') ?>">
								    </div>
								  </div>
								  
								  <div class="form-group">
								    <label for="carrental_company_email" class="col-sm-3 control-label">E-mail</label>
								    <div class="col-sm-9">
								    	<input type="text" name="email" class="form-control" id="carrental_company_email" value="<?= (isset($company['email']) ? $company['email'] : '') ?>">
										<p class="help-block">Booking confirmation emails are sent to this address.</p>
								    </div>
								  </div>
								  <div class="form-group">
								    <label for="carrental_company_phone" class="col-sm-3 control-label">Phone</label>
								    <div class="col-sm-9">
								    	<input type="text" name="phone" class="form-control" id="carrental_company_phone" value="<?= (isset($company['phone']) ? $company['phone'] : '') ?>">
								    </div>
								  </div>
								  
									<div class="form-group">
								    <label for="carrental_company_fax" class="col-sm-3 control-label">Fax</label>
								    <div class="col-sm-9">
								    	<input type="text" name="fax" class="form-control" id="carrental_company_fax" value="<?= (isset($company['fax']) ? $company['fax'] : '') ?>">
								    </div>
								  </div>
								  
								</div>
								
								<div class="col-md-4">
								
								  <div class="form-group">
								    <label for="carrental_company_street" class="col-sm-3 control-label">Street</label>
								    <div class="col-sm-9">
								    	<input type="text" name="street" class="form-control" id="carrental_company_street" value="<?= (isset($company['street']) ? $company['street'] : '') ?>">
								    </div>
								  </div>
								  
								  <div class="form-group">
								    <label for="carrental_company_city" class="col-sm-3 control-label">City</label>
								    <div class="col-sm-9">
								    	<input type="text" name="city" class="form-control" id="carrental_company_city" value="<?= (isset($company['city']) ? $company['city'] : '') ?>">
								    </div>
								  </div>
								  
								  <div class="form-group">
								    <label for="carrental_company_zip" class="col-sm-3 control-label">ZIP code</label>
								    <div class="col-sm-9">
								    	<input type="text" name="zip" class="form-control" id="carrental_company_zip" value="<?= (isset($company['zip']) ? $company['zip'] : '') ?>">
								    </div>
								  </div>
								  
								  <div class="form-group">
								    <label for="carrental_company_country" class="col-sm-3 control-label">Country</label>
								    <div class="col-sm-9">
								    	<select name="country" class="form-control" id="carrental_company_country">
									    	<option value="">- select -</option>
									    	<?php $countries = CarRental_Admin::get_country_list(); ?>
									    	<?php foreach ($countries as $key => $val) { ?>
									    		<option value="<?= $key ?>" <?= ((isset($company['country']) && $key == $company['country']) ? 'selected="selected"' : '') ?>><?= $val ?></option>
									    	<?php } ?>
								    	</select>
								    </div>
								  </div>
								  
								  <div class="form-group">
								    <label for="carrental_company_web" class="col-sm-3 control-label">Webpage URL</label>
								    <div class="col-sm-9">
								    	<input type="text" name="web" class="form-control" id="carrental_company_web" value="<?= (isset($company['web']) ? $company['web'] : '') ?>">
								    </div>
								  </div>
								  
									<!-- Submit //-->
								  <div class="form-group">
								  	<div class="col-sm-offset-3 col-sm-9">
								  		<button type="submit" name="edit_company_info" class="btn btn-warning"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Confirm &amp; Save Company info</button>
								  	</div>
									</div>
							
								</div>
							</div>
							
						</form>
					</div>
				</div><!-- .panel //-->
				
				
				<!-- GLOBAL SETTINGS //-->
				<div class="panel panel-default">
					<div class="panel-heading"><h4 id="global-settings">Global Settings</h4></div>
					<div class="panel-body">
					  
					  <form action="" method="post" role="form" class="form-horizontal" enctype="multipart/form-data">
					
							<div class="row">
								<div class="col-md-6">
									
									<!-- Type of rental //-->
								  <?php $type_of_rental = get_option('carrental_type_of_rental'); ?>
									<div class="form-group">
								    <label for="carrental_type_of_rental" class="col-sm-3 control-label">Type of Rental</label>
								    <div class="col-sm-9">
										<select name="carrental_type_of_rental" id="carrental_type_of_rental">
											<?php foreach (CarRental_Admin::$types_of_rental as $k => $v) { ?>
												<option value="<?php echo $k;?>"<?php echo $type_of_rental == $k ? ' selected="selected"' : '';?>><?php echo $v;?></option>
											<?php } ?>
										</select>
								    	<p class="help-block">
												We will use this information when indexing your website in search results. It is important to target the right audience
											</p>
								    </div>
								  </div>
									
									<!-- Currency //-->
								  <?php $currency = get_option('carrental_global_currency'); ?>
									<div class="form-group">
								    <label for="carrental_global_currency" class="col-sm-3 control-label">Global Currency</label>
								    <div class="col-sm-9">
								    	<input type="text" name="carrental_global_currency" class="form-control" id="carrental_global_currency" placeholder="USD, EUR, CZK, ..." value="<?= (!empty($currency) ? $currency : '') ?>">
								    	<p class="help-block">
												Fill your primary currency (<strong>3 letter code</strong>), do not change it if you already have Price schemes created.<br>
												If you change Global currency, the available currencies will be deleted and you have to correct all your Price schemes.
											</p>
								    </div>
								  </div>
								  
								  <!-- Available currencies //-->
								  <div class="form-group">
								    <label class="col-sm-3 control-label">Other currencies available</label>
								    <div class="col-sm-9 carrental-av-currencies-div">
								    	<?php $av_currencies = unserialize(get_option('carrental_available_currencies')); ?>
								    	<?php if ($currency && !empty($currency) && strlen($currency) == 3) { ?>
									    	<div id="carrental-av-currencies-insert"></div>
									    	<div id="carrental-av-currencies">
									    		<div class="row">
		  											<div class="col-xs-1 text-right"><h4>1</h4></div>
														<div class="col-xs-3"><input type="text" name="av_currencies_cc[]" class="form-control" size="5" placeholder="USD, EUR, ..."></div>
									    			<div class="col-xs-1 text-center"><h4>=</h4></div>
														<div class="col-xs-3"><input type="text" name="av_currencies_rate[]" class="form-control" size="5" placeholder="5, 12.5, ..."></div>
									    			<div class="col-xs-1"><h4><?= $currency ?></h4></div>
									    		</div>
										    </div>
										    <?php if ($av_currencies && !empty($av_currencies)) { ?>
									    		<?php foreach ($av_currencies as $cc => $rate) { ?>
									    			<div class="row">
			  											<div class="col-xs-1 text-right"><h4>1</h4></div>
															<div class="col-xs-3"><input type="text" name="av_currencies_cc[]" class="form-control" size="5" placeholder="USD, EUR, ..." value="<?= $cc ?>"></div>
										    			<div class="col-xs-1 text-center"><h4>=</h4></div>
															<div class="col-xs-3"><input type="text" name="av_currencies_rate[]" class="form-control" size="5" placeholder="5, 12.5, ..." value="<?= $rate ?>"></div>
										    			<div class="col-xs-1"><h4><?= $currency ?></h4></div>
										    		</div>
									    		<?php } ?>
									    	<?php } ?>
										    <p class="help-block">
													You can set-up another available currencies. Fill the 3 letter currency code to first input and appropriate currency exchange to your Global currency.
													<strong>Example</strong>: <em>1 EUR = 1.35 USD</em> OR <em>1 USD = 0.75 EUR</em>
												</p>
												<a href="javascript:void(0);" id="carrental-add-av-currencies" class="btn btn-info btn-xs">Add another Currency</a>
											<?php } else { ?>
							  				<div class="alert alert-warning" role="alert">
													You can set-up more available currencies, but first, set-up Global Currency.
												</div>
							  			<?php } ?>
								    </div>
								  </div>
								  
									<!-- Price for delivery //-->
									<?php $delivery_price = get_option('carrental_delivery_price'); ?>
								  <div class="form-group">
								    <label for="carrental_delivery_price" class="col-sm-3 control-label">Price for car delivery</label>
								    <div class="col-sm-9">
								    	<?php if ($currency && !empty($currency) && strlen($currency) == 3) { ?>
								    		<div class="row">
			  									<div class="col-xs-3"><input type="text" name="carrental_delivery_price" class="form-control" id="carrental_delivery_price" value="<?= (!empty($delivery_price) ? $delivery_price : '') ?>"></div>
								    			<div class="col-xs-1"><h4><?= $currency ?></h4></div>
												</div>
												<p class="help-block">If customer chooses other drop off from pick up location, how much will be charged.<br>Please insert just a number (float possible).</p>
								    	<?php } else { ?>
							  				<div class="alert alert-warning" role="alert">
													You can set-up price for car delivery, but first, set-up Global Currency.
												</div>
							  			<?php } ?>
								    </div>
								  </div>
								  
								  <!-- Consumption //-->
								  <?php $consumption = get_option('carrental_consumption'); ?>
								  <div class="form-group">
								    <label for="carrental_consumption" class="col-sm-3 control-label">Consumption metric</label>
								    <div class="col-sm-9">
								    	<label class="radio-inline">
											  <input type="radio" name="carrental_consumption" id="carrental-consumption-eu" value="eu" <?= (($consumption == 'eu') ? 'checked="checked"' : '') ?>> l / 100 km
											</label>
											<label class="radio-inline">
											  <input type="radio" name="carrental_consumption" id="carrental-consumption-us" value="us" <?= (($consumption == 'us') ? 'checked="checked"' : '') ?>> MPG
											</label>
											<p class="help-block">What metric of consumption will you use?</p>
								    </div>
								  </div>
									
									<!-- Distance Metric //-->
								  <?php $distance_metric = get_option('carrental_distance_metric'); ?>
								  <div class="form-group">
								    <label for="carrental_consumption" class="col-sm-3 control-label">Distance metric</label>
								    <div class="col-sm-9">
								    	<label class="radio-inline">
											  <input type="radio" name="carrental_distance_metric" id="carrental-consumption-km" value="km" <?= (($distance_metric == 'km') ? 'checked="checked"' : '') ?>> kilometers
											</label>
											<label class="radio-inline">
											  <input type="radio" name="carrental_distance_metric" id="carrental-consumption-mi" value="mi" <?= (($distance_metric == 'mi') ? 'checked="checked"' : '') ?>> miles
											</label>
											<p class="help-block">What metric of distance will you use?</p>
								    </div>
								  </div>
								  
								  <!-- Overbooking //-->
								  <?php $overbooking = get_option('carrental_overbooking'); ?>
								  <div class="form-group">
								    <label for="carrental_overbooking" class="col-sm-3 control-label">Allow branches overbooking?</label>
								    <div class="col-sm-9">
									    <label class="radio-inline">
												<input type="radio" name="carrental_overbooking" value="yes" <?= (($overbooking == 'yes') ? 'checked="checked"' : '') ?>>&nbsp; Yes
											</label>
											<label class="radio-inline"><input type="radio" name="carrental_overbooking" value="no" <?= (($overbooking == 'no') ? 'checked="checked"' : '') ?>>
												&nbsp; No
											</label>
											<p class="help-block">Select "yes" to make all your cars always available; select "no" to check reservation requests against cars available.</p>
								    </div>
								  </div>
								  
								   <!-- Detail page //-->
								  <?php $detail_page = get_option('carrental_detail_page'); ?>
								  <div class="form-group">
								    <label for="carrental_detail_page" class="col-sm-3 control-label">Use fleet detail page?</label>
								    <div class="col-sm-9">
									    <label class="radio-inline">
												<input type="radio" name="carrental_detail_page" value="yes" <?= (($detail_page == 'yes') ? 'checked="checked"' : '') ?>>&nbsp; Yes
											</label>
											<label class="radio-inline"><input type="radio" name="carrental_detail_page" value="no" <?= (($detail_page == 'no') ? 'checked="checked"' : '') ?>>
												&nbsp; No
											</label>
											<p class="help-block">Select "yes" to enable fleet detail pages.</p>
								    </div>
								  </div>
								   
								    <!-- Disable Time //-->
								  <?php $disable_time = get_option('carrental_disable_time'); ?>
								  <div class="form-group">
								    <label for="carrental_disable_time" class="col-sm-3 control-label">Disable time selectors</label>
								    <div class="col-sm-9">
									    <label class="radio-inline">
												<input type="radio" name="carrental_disable_time" value="yes" <?= (($disable_time == 'yes') ? 'checked="checked"' : '') ?>>&nbsp; Yes
											</label>
											<label class="radio-inline"><input type="radio" name="carrental_disable_time" value="no" <?= (($disable_time == 'no') ? 'checked="checked"' : '') ?>>
												&nbsp; No
											</label>
											<p class="help-block">Select "yes" show only pickup and return dates without time selectors.</p>
								    </div>
								  </div>
								  
									<!-- Any location //-->
								  <?php $anylocation = get_option('carrental_any_location_search'); ?>
								  <div class="form-group">
								    <label for="carrental_any_location_search" class="col-sm-3 control-label">Any location search?</label>
								    <div class="col-sm-9">
									    <label class="radio-inline">
												<input type="radio" name="carrental_any_location_search" value="yes" <?= (($anylocation == 'yes') ? 'checked="checked"' : '') ?>>&nbsp; Yes
											</label>
											<label class="radio-inline"><input type="radio" name="carrental_any_location_search" value="no" <?= (($anylocation == 'no') ? 'checked="checked"' : '') ?>>
												&nbsp; No
											</label>
											<p class="help-block">Clients can search cars independent of the branch they are assigned to.</p>
								    </div>
								  </div>
									
									
									<!-- Automatic reminder //-->
								  <?php $automatic_reminder = get_option('carrental_reminder_days'); ?>
								  <div class="form-group">
								    <label for="carrental_reminder_days" class="col-sm-3 control-label">Automatic reminder</label>
									<div class="col-sm-9">
								    
										<input type="text" class="form-control" name="carrental_reminder_days" id="carrental_reminder_days" value="<?php echo $automatic_reminder;?>">
									
										<p class="help-block">Set number of days before booking enter date.</p>
										<p class="help-block">To enable this function, you have to visit your hosting settings and allow everyday cron for url:<br><?php echo CARRENTAL__PLUGIN_URL.'cron.php';?></p>
									</div>
								  </div>
									
									<!-- Thank you email//-->
								  <?php $ty_days = get_option('carrental_thank_you_days'); ?>
								  <div class="form-group">
								    <label for="carrental_thank_you_days" class="col-sm-3 control-label">Thank you email</label>
									<div class="col-sm-9">
								    
										<input type="text" class="form-control" name="carrental_thank_you_days" id="carrental_reminder_days" value="<?php echo $ty_days;?>">
									
										<p class="help-block">Set number of days after return the car.</p>
										<p class="help-block">To enable this function, you have to visit your hosting settings and allow everyday cron for url:<br><?php echo CARRENTAL__PLUGIN_URL.'cron.php';?></p>
									</div>
								  </div>
								  
									<!-- Show vat by default //-->
								  <?php $showvat = get_option('carrental_show_vat'); ?>
								  <div class="form-group">
								    <label for="carrental_show_vat" class="col-sm-3 control-label">Show prices with VAT?</label>
								    <div class="col-sm-9">
									    <label class="radio-inline">
												<input type="radio" name="carrental_show_vat" value="yes" <?= (($showvat == 'yes') ? 'checked="checked"' : '') ?>>&nbsp; Yes
											</label>
											<label class="radio-inline"><input type="radio" name="carrental_show_vat" value="no" <?= (($showvat == 'no') ? 'checked="checked"' : '') ?>>
												&nbsp; No
											</label>
											<p class="help-block">Clients will or will not see prices including VAT in the search results.</p>
								    </div>
								  </div>
									
									<!-- Min before days //-->
									<?php $min_before_days = get_option('carrental_min_before_days'); ?>
									<div class="form-group">
										<label for="carrental_min_before_days" class="col-sm-3 control-label">Earliest book time(days)</label>
										<div class="col-sm-9">
											<input type="text" name="carrental_min_before_days" class="form-control" id="carrental_min_before_days" value="<?= (!empty($min_before_days) ? $min_before_days : '') ?>">
											<p class="help-block">Set the earliest day of booking or leave blank (if you set value to 2, clients booking on January 1st will be able to make booking earliest for Jan-3rd).</p>
										</div>
									</div>

									<!-- Max before days //-->
									<?php $max_before_days = get_option('carrental_max_before_days'); ?>
									<div class="form-group">
										<label for="carrental_max_before_days" class="col-sm-3 control-label">Latest book time(days)</label>
										<div class="col-sm-9">
											<input type="text" name="carrental_max_before_days" class="form-control" id="carrental_max_before_days" value="<?= (!empty($max_before_days) ? $max_before_days : '') ?>">
											<p class="help-block">Set the latest day for booking or leave blank for unrestricted (if you set to 20, clients booking on January 1st will be able to make booking latest until Jan-20th).</p>
										</div>
									</div>
											  
								  <!-- PayPal //-->
								  <?php $paypal = get_option('carrental_paypal'); ?>
								  <div class="form-group">
								    <label for="carrental_paypal" class="col-sm-3 control-label">PayPal settings</label>
								    <div class="col-sm-9">
								    	<input type="text" name="carrental_paypal" class="form-control" id="carrental_paypal" value="<?= (!empty($paypal) ? $paypal : '') ?>">
								    	<p class="help-block">Please, insert your PayPal e-mail for receiving payments.</p>
								    </div>
								  </div>
									
									<!-- Require payment //-->
								  <?php $require_payment = get_option('carrental_require_payment'); ?>
								  <div class="form-group">
								    <label for="carrental-payment" class="col-sm-3 control-label">Require payment with booking?</label>
								    <div class="col-sm-9">
									    <label class="radio-inline"><input type="radio" name="carrental_require_payment" value="yes" <?= (($require_payment == 'yes') ? 'checked="checked"' : '') ?>>&nbsp; Yes</label>
											<label class="radio-inline"><input type="radio" name="carrental_require_payment" value="no" <?= (($require_payment == 'no') ? 'checked="checked"' : '') ?>>&nbsp; No</label>
											<p class="help-block">The last step for user will be Checkout with PayPal.</p>
								    </div>
								  </div>
									
									<!-- Where to send emails //-->
								  <?php $book_send_email = get_option('carrental_book_send_email'); ?>
								  <?php if (empty($book_send_email)) { $book_send_email = array('client' => 1, 'admin' => 1); } else { $book_send_email = unserialize($book_send_email); } ?>									
								  <div class="form-group">
								    <label for="carrental-payment" class="col-sm-3 control-label">Confirmation emails</label>
								    <div class="col-sm-9">
									    <label class="radio-inline"><input type="checkbox" name="carrental_book_send_email[client]" value="1" <?= ((!isset($book_send_email['client']) || $book_send_email['client'] == 1) ? 'checked="checked"' : '') ?>>&nbsp; Send to client</label>
										<label class="radio-inline"><input type="checkbox" name="carrental_book_send_email[admin]" value="1" <?= ((!isset($book_send_email['admin']) || $book_send_email['admin'] == 1) ? 'checked="checked"' : '') ?>>&nbsp; Send to admin</label>
										<div>
											<label class="radio-inline"><input type="checkbox" name="carrental_book_send_email[other]" value="1" <?= ((!isset($book_send_email['other']) || $book_send_email['other'] == 1) ? 'checked="checked"' : '') ?>>&nbsp; Send to other email:</label>
											<input type="text" name="carrental_book_send_email[other_email]" style="display: inline-block; margin-left: 15px;margin-top: 5px; position: relative; top: 5px;" value="<?= (isset($book_send_email['other_email']) ? $book_send_email['other_email'] : '') ?>">
										</div>
										<p class="help-block">Where to send email confirmation after booking.</p>
								    </div>
								  </div>
									
									<!-- Disclaimer //-->
								  <div class="form-group">
									<label for="carrental-disclaimer" class="col-sm-3 control-label">Disclaimer</label>
									<div class="col-sm-9">

										<ul class="nav nav-tabs" role="tablist">
											  <li role="presentation" class="active"><a href="javascript:void(0);" class="edit_disclaimer" data-value="gb">English (GB)</a></li>
											  <?php $available_languages = unserialize(get_option('carrental_available_languages')); ?>
												<?php if ($available_languages && !empty($available_languages)) { ?>
													<?php foreach ($available_languages as $key => $val) { ?>
													<?php if ($val['country-www'] == 'gb') {continue;} ?>
													<li role="presentation"><a href="javascript:void(0);" class="edit_disclaimer" data-value="<?= strtolower($val['country-www']) ?>"><?= $val['lang'] ?> (<?= strtoupper($val['country-www']) ?>)</a></li>
													<?php } ?>
											  <?php } ?>
											</ul>

											<?php $disclaimer = get_option('carrental_disclaimer');?>
											<?php $disclaimer = unserialize($disclaimer); ?>
											<?php if ($disclaimer == false) { $disclaimer['gb'] = ''; } ?>
											
											<textarea class="form-control disclaimer disclaimer_gb" name="carrental_disclaimer[gb]" id="carrental-disclaimer" rows="3" placeholder="Brief disclaimer in English (GB)."><?= ((isset($disclaimer['gb']) && !empty($disclaimer['gb'])) ? $disclaimer['gb'] : '') ?></textarea>
											<?php if ($available_languages && !empty($available_languages)) { ?>
												<?php foreach ($available_languages as $key => $val) { ?>
												<?php if ($val['country-www'] == 'gb') {continue;} ?>
												<textarea class="form-control disclaimer disclaimer_<?= strtolower($val['country-www']) ?>" name="carrental_disclaimer[<?= strtolower($val['country-www']) ?>]" rows="3" placeholder="Brief disclaimer in <?= $val['lang'] ?> (<?= strtoupper($val['country-www']) ?>)."><?= ((isset($disclaimer[strtolower($val['country-www'])]) && !empty($disclaimer[strtolower($val['country-www'])])) ? $disclaimer[strtolower($val['country-www'])] : '') ?></textarea>
											<?php } ?>
											<?php } ?>
										<p class="help-block">This is shown before "book now" button on checkout page.</p>
									</div>
								  </div>
								    	
								  <!-- Submit //-->
								  <div class="form-group">
								  	<div class="col-sm-offset-3 col-sm-9">
								  		<button type="submit" name="edit_settings" class="btn btn-warning"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Confirm &amp; Save Settings</button>
								  	</div>
									</div>
									
								</div>
							</div>
							<!-- .row //-->
							
						</form>
					
					</div>
					<!-- .panel-body //-->
				</div>
				<!-- .panel .panel-default //-->
				
				<div class="panel panel-default">
					<div class="panel-heading"><h4 id="global-scheme-replace">Minimum bookingtime per month (in days)</h4></div>
					<div class="panel-body">
					  
						<div class="row">
							<div class="col-md-12">
								<form action="<?= CarRental_Admin::get_page_url('carrental-settings') ?>#min-booking-time" method="post" role="form" class="form-horizontal" enctype="multipart/form-data">
									<?php $min_rental_times =  unserialize(get_option('carrental_minimum_rental_time')); ?>
									<?php if (!is_array($min_rental_times)) { $min_rental_times = array();} ?>
									<?php for ($i=1;$i<=12;$i++) { ?>
										<?php $dt = DateTime::createFromFormat('!m', $i); ?>
									<div class="row" style="margin-bottom:5px">
											<div class="col-md-1">
												<label for="min_rental_time_<?php echo $i;?>"><?php echo $dt->format('F');?></label>
											</div>
											<div class="col-md-1">
												<input type="text" name="minimum_rental_time[<?php echo $i;?>]" class="form-control" id="min_rental_time_<?php echo $i;?>" value="<?= (isset($min_rental_times[$i]) ? $min_rental_times[$i] : '') ?>">
											</div>
										</div>
									<?php } ?>
									
									<div class="row">
										<div class="col-md-4">
											
											<!-- Submit //-->
										  <br><button type="submit" name="save_min_booking_time" class="btn btn-warning"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Save minimum booking times per month</button>
										
										</div>
									</div>
									
									
								</form>
								
							</div>
						</div>
					</div>
				</div>
				
				<div class="panel panel-default">
					<div class="panel-heading"><h4 id="global-scheme-replace">Global price scheme replace</h4></div>
					<div class="panel-body">
					  
						<div class="row">
							<div class="col-md-12">
								<p class="help-block">Replaces all instances of "Original price scheme" with selected scheme.</p>
								<form action="<?= CarRental_Admin::get_page_url('carrental-settings') ?>#vehicle-categories" method="post" role="form" class="form-horizontal" enctype="multipart/form-data">
									
									<div class="row">
										<div class="col-md-4">
											<label for="price_scheme_original">Original price scheme</label>
											<select name="price_scheme_original" id="price_scheme_original" class="form-control">
									    	<option value="0">- none -</option>
									    	<?php if (isset($pricing) && !empty($pricing)) { ?>
										    	<?php foreach ($pricing as $key => $val) { ?>
										    		<option value="<?= $val->id_pricing ?>" <?= (($edit == true && $detail->global_pricing_scheme == $val->id_pricing) ? 'selected="selected"' : '') ?>><?= $val->name ?></option>
										    	<?php } ?>
										    <?php } ?>
								    	</select>
										</div>
									
										<div class="col-md-4">
											<label for="price_scheme_new">Replace for this price scheme</label>
											<select name="price_scheme_new" id="price_scheme_new" class="form-control">
									    	<option value="0">- none -</option>
									    	<?php if (isset($pricing) && !empty($pricing)) { ?>
										    	<?php foreach ($pricing as $key => $val) { ?>
										    		<option value="<?= $val->id_pricing ?>" <?= (($edit == true && $detail->global_pricing_scheme == $val->id_pricing) ? 'selected="selected"' : '') ?>><?= $val->name ?></option>
										    	<?php } ?>
										    <?php } ?>
								    	</select>
										</div>
									</div>
									
									<div class="row">
										<div class="col-md-4">
											
											<!-- Submit //-->
										  <br><button type="submit" name="replace_price_scheme" class="btn btn-warning"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Confirm &amp; Replace Price Scheme</button>
										
										</div>
									</div>
									
									
								</form>
								
							</div>
						</div>
					</div>
				</div>
							
				
				<div class="panel panel-default">
					<div class="panel-heading"><h4 id="vehicle-categories">Vehicle Categories</h4></div>
					<div class="panel-body">
					  
						<div class="row">
							<div class="col-md-12">
								
								<form action="<?= CarRental_Admin::get_page_url('carrental-settings') ?>#vehicle-categories" method="post" role="form" class="form-horizontal" enctype="multipart/form-data">
					
									<!-- Vehicle categories //-->
								  <table class="table" id="carrental-vehicle-categories">
							      <thead>
							        <tr>
							          <th>#</th>
							          <th>Name</th>
							          <th>Current Picture</th>
							          <th>New picture</th>
									  <th>Shortcode</th>
							          <th>No. of vehicles</th>
							          <th>Delete</th>
							        </tr>
							      </thead>
							      <tbody>
							      	
							      	<?php if ($vehicle_categories && !empty($vehicle_categories)) { ?>
								    		<?php foreach ($vehicle_categories as $key => $val) { ?>
								    			<tr>
								    				<td><?= $val->id_category ?></td>
								    				<td><input type="text" name="vehicle_categories_name[<?= $val->id_category ?>]" class="form-control" value="<?= $val->name ?>"></td>
								    				<td class="text-center">
								    					<?php if (!empty($val->picture)) { ?>
									    					<img src="<?= $val->picture ?>" height="80">
									    				<?php } else { ?>
									    					<em>- none -</em>
									    				<?php } ?>
									    				<input type="hidden" name="vehicle_categories_picture[<?= $val->id_category ?>]" class="form-control" value="<?= $val->picture ?>">
								    				</td>
								    				<td>
								    					<input type="file" name="vehicle_categories_file[<?= $val->id_category ?>]">
								    				</td>
													<td>
								    					[carrental_category id="<?= $val->id_category ?>"]
								    				</td>
								    				<td class="text-center"><?= $val->no_vehicles ?></td>
								    				<td>
								    					<?php if ($val->no_vehicles == 0) { ?>
																<div class="checkbox">
															    <label>
															      <input type="checkbox" name="vehicle_categories_delete[<?= $val->id_category ?>]" value="1">&nbsp;&nbsp;Delete
															    </label>
															  </div>
															<?php } ?>
														</td>
								    			</tr>
								    		<?php } ?>
								    	<?php } ?>
								    	
				      			</tbody>
				      		</table>
									
								  <!-- Submit //-->
								  <button type="submit" name="update_vehicle_categories" class="btn btn-warning"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Confirm &amp; Save Vehicle Categories</button>
								  
								</form>
								
						  </div>
						</div>
						<!-- .row //-->
						
						<div class="row">
							<div class="col-md-12">
								<br>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-6">
						  	
						  	<form action="<?= CarRental_Admin::get_page_url('carrental-settings') ?>#vehicle-categories" method="post" role="form" enctype="multipart/form-data">
									
									<h4>Add vehicle category</h4>
									
									<p class="help-block">
										Insert your Vehicle Categories before you create your Fleet. You can also add illustrative picture.
										<br>You can't delete Category with vehicles assigned into it.
									</p>
										
									<div class="form-group">
								    <label for="carrental-category-name">Category name</label>
								    <input type="text" name="vehicle_category_name" id="carrental-category-name" class="form-control" placeholder="Category name">
								  </div>
									
									<div class="form-group">
								    <label for="carrental-category-picture">Category picture</label>
								    <input type="file" name="vehicle_category_picture" id="carrental-category-picture">
								  </div>
								  
								  <button type="submit" class="btn btn-success" name="add_vehicle_category">Add Vehicle Category</button>
									
							  </form>
						  
						  </div>
						</div>
						<!-- .row //-->
							
					</div>
					<!-- .panel-body //-->
				</div>
				
				<div class="panel panel-default">
					<div class="panel-heading"><h4 id="holidays">Holidays</h4></div>
					<div class="panel-body">
						<form action="<?= CarRental_Admin::get_page_url('carrental-settings') ?>#holidays" method="post" role="form" class="form-horizontal" enctype="multipart/form-data">
						<div class="row">
							<div class="col-md-12" id="carrental_holidays_div">
								<?php $holidays = get_option('carrental_holidays'); ?>
									
									<?php $holidays = unserialize($holidays);?>
									<?php if ($holidays && !empty($holidays)) { ?>
										<?php asort($holidays); ?>
										<?php foreach ($holidays as $key => $val) { ?>
												<span style="margin-right:5px;margin-bottom:5px;" class="carrental_holidays btn btn-warning"><?php echo $val;?> <a href="#" class="carrental_remove_holiday">X</a><input type="hidden" name="carrental_holidays[]" value="<?php echo $val;?>"></span>
										<?php } ?>
									<?php } else { ?>
										<p>You have no holidays yet.</p>	
									<?php } ?>

							</div>
						</div>
						<!-- .row //-->

						<div class="row">
							<div class="col-md-6">

								<h4>Add new holiday date</h4>

								<div>
									<label for="carrental-holiday-date">Date</label>
									<input type="text" name="vehicle_holiday_date" id="carrental-holiday-date" class="form-control" placeholder="Date">
								</div>
								<p class="help-block">Please note: dates closed will repeat every year unless deleted.</p>
								<button id="carrental_add_holidays" class="btn btn-success" name="add_holidays">Add this date</button>

								
								<!-- Submit //-->
								<br><br><button type="submit" name="save_holidays" class="btn btn-warning"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Confirm &amp; Save Holidays</button>
							</div>
						</div>
						<!-- .row //-->
						
								</form>
					</div>
					<!-- .panel-body //-->
				</div>
				
				<div class="panel panel-default">
					<div class="panel-heading"><h4 id="reservation-inputs">Reservation inputs</h4></div>
					<div class="panel-body">
						<form action="<?= CarRental_Admin::get_page_url('carrental-settings') ?>#reservation-inputs" method="post" role="form" class="form-horizontal" enctype="multipart/form-data">
						<div class="row">
							<div class="col-md-10">
								<?php
								$inputs_list = array('company' => 'Company', 'vat' => 'VAT', 'flight' => 'Flight number', 'license' => 'License number', 'id_card' => 'ID / Passport number', 'partner_code' => 'Partner code');
								$inputs = get_option('carrental_reservation_inputs');
								$inputs = unserialize($inputs);
								if (empty($inputs)) {
									$inputs = array();
								}
								foreach ($inputs_list as $k => $v) { ?>
									<label style="margin-right:20px;"><input type="checkbox" name="carrental_inputs[<?php echo $k;?>]" value="1"<?php echo !isset($inputs[$k]) ? ' checked="checked"' : '';?>> <span><?php echo $v;?></span></label>
								<?php } ?>
								<p class="help-block">Checked inputs will be shown in reservation form.</p>
								
								<!-- Submit //-->
								<button type="submit" name="save_reservation_inputs" class="btn btn-warning"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Confirm &amp; Save Reservation inputs</button>
							</div>
						</div>
						<!-- .row //-->
						
								</form>
					</div>
					<!-- .panel-body //-->
				</div>
				
				<div class="panel panel-default">
					<div class="panel-heading"><h4>E-mail testing</h4></div>
					<div class="panel-body">
					  
						<div class="row">
							<div class="col-md-12">
							
								<form action="<?= CarRental_Admin::get_page_url('carrental-settings') ?>" method="post" role="form" class="form-horizontal">
										
									<div class="form-group">
								    <label for="smtp-user" class="col-sm-3 control-label">Your e-mail</label>
								    <div class="col-sm-9">
								    	<input type="text" class="form-control" name="email" id="smtp-user" value="<?= (isset($smtp['email']) ? $smtp['email'] : '') ?>" placeholder="User name or e-mail">
								    </div>
								  </div>
									
									<div class="form-group">
										<div class="col-sm-offset-3 col-sm-9">
											<button type="button" name="send_test_email" class="btn btn-info"><span class="glyphicon glyphicon-send"></span>&nbsp;&nbsp;Send test e-mail</button>
										</div>
									</div>
									
									<script>
										
										jQuery(document).ready(function() {
											
											jQuery('[name="send_test_email"]').click(function() {
												jQuery(this).prop('disabled', true);
												jQuery.post("<?= $_SERVER['REQUEST_URI'] ?>", { send_test_email: "1", user: jQuery('#smtp-user').val() })
												  .done(function( data ) {
												  	jQuery('[name="send_test_email"]').prop('disabled', false);
												    alert( data );
												});
											});
											
										});
									
									</script>
									
								</form>
								
							</div>
						</div>
					</div>
				</div>
				
				<!-- SMTP Settings //-->
				<?php /* HIDDEN ?>
				<div class="panel panel-default">
					<div class="panel-heading"><h4>SMTP Settings (for sending e-mails)</h4></div>
					<div class="panel-body">
					  
					  <?php $smtp = unserialize(get_option('carrental_smtp')); ?>
						<div class="row">
							<div class="col-md-12">
							
								<form action="<?= CarRental_Admin::get_page_url('carrental-settings') ?>" method="post" role="form" class="form-horizontal">
									
									<p>
										* This settings is important if you want to send automatic e-mails to customers with their reservation.<br>
										* If some of the options are empty, there is no guarantee that reservation e-mail will be sent.<br><br>
									</p>
											
									<div class="form-group">
								    <label for="smtp-server" class="col-sm-3 control-label">SMTP Server</label>
								    <div class="col-sm-9">
								    	<input type="text" class="form-control" name="server" id="smtp-server" value="<?= (isset($smtp['server']) ? $smtp['server'] : '') ?>" placeholder="SMTP Server / e.g. smtp.gmail.com">
								    </div>
								  </div>
									
									<div class="form-group">
								    <label for="smtp-user" class="col-sm-3 control-label">Username / e-mail</label>
								    <div class="col-sm-9">
								    	<input type="text" class="form-control" name="email" id="smtp-user" value="<?= (isset($smtp['email']) ? $smtp['email'] : '') ?>" placeholder="User name or e-mail">
								    </div>
								  </div>
									
									<div class="form-group">
								    <label for="smtp-pass" class="col-sm-3 control-label">Password</label>
								    <div class="col-sm-9">
								    	<input type="password" class="form-control" name="pwd" id="smtp-pass" value="<?= (isset($smtp['pwd']) ? $smtp['pwd'] : '') ?>" placeholder="Password">
								    </div>
								  </div>
									
									<div class="form-group">
								    <label for="smtp-port" class="col-sm-3 control-label">SMTP Port</label>
								    <div class="col-sm-9">
								    	<input type="text" class="form-control" name="port" id="smtp-port" value="<?= (isset($smtp['port']) ? $smtp['port'] : '') ?>" placeholder="SMTP Port">
								    	<p class="help-block">Set 465 or 587 for Google</p>
								    </div>
								  </div>
									
									<div class="form-group">
								    <label for="smtp-sec" class="col-sm-3 control-label">SMTP Secure</label>
								    <div class="col-sm-9">
								    	<select name="secure" id="smtp-sec"  class="form-control">
								    		<option value="">None</option>
								    		<option value="tls" <?php if (isset($smtp['secure']) && $smtp['secure'] == 'tls') { ?>selected<?php } ?>>TLS</option>
								    		<option value="ssl" <?php if (isset($smtp['secure']) && $smtp['secure'] == 'ssl') { ?>selected<?php } ?>>SSL</option>
								    	</select>
								    </div>
								  </div>
									
									<div class="form-group">
										<div class="col-sm-offset-3 col-sm-9">
											<br><button type="submit" name="save_smtp_settings" class="btn btn-warning"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Confirm &amp; Save settings</button>
											&nbsp;&nbsp;&nbsp;&nbsp;
											<button type="button" name="send_test_email" class="btn btn-info"><span class="glyphicon glyphicon-send"></span>&nbsp;&nbsp;Send test e-mail</button>
										</div>
									</div>
									
									<script>
										
										$(document).ready(function() {
											
											$('[name="send_test_email"]').click(function() {
												$(this).prop('disabled', true);
												$.post("<?= $_SERVER['REQUEST_URI'] ?>", { send_test_email: "1", server: $('#smtp-server').val(), user: $('#smtp-user').val(), pass: $('#smtp-pass').val(), port: $('#smtp-port').val(), secure: $('#smtp-sec').val() })
												  .done(function( data ) {
												  	$('[name="send_test_email"]').prop('disabled', false);
												    alert( data );
												});
											});
											
										});
									
									</script>
									
								</form>
								
							</div>
						</div>
					</div>
				</div>
				<?php /**/ ?>
				
				<?php if (isset($_GET['export'])) { ?>
					<!-- Export/Import //-->
					<div class="panel panel-default">
						<div class="panel-heading"><h4 id="global-scheme-replace">Export data</h4></div>
						<div class="panel-body">
						  
							<div class="row">
								<div class="col-md-12">
								
									<form action="<?= CarRental_Admin::get_page_url('carrental-settings') ?>" method="post" role="form" class="form-horizontal">
										
										<div class="form-group">
											<div class="col-sm-12">
												<div class="checkbox">
											    <label>
											      <input type="checkbox" name="export_structure" value="1">&nbsp;&nbsp;Export structure
											    </label>
											  </div>
											</div>
										</div>
										
										<div class="form-group">
											<div class="col-sm-12">
												<div class="checkbox">
											    <label>
											      <input type="checkbox" name="export_data" value="1">&nbsp;&nbsp;Export data
											    </label>
											  </div>
											</div>
										</div>
														  
										<div class="form-group">
											<div class="col-sm-12">
												<button type="submit" name="export_database" class="btn btn-warning"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Export</button>
											</div>
										</div>
										
										
									</form>
									
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
				
			</div>
		</div>
	</div>
	
</div>


<script type="text/javascript">
	jQuery(document).ready(function ($) {
		$(document).on('click', '#carrental-holiday-date', function () {
			$(this).datepicker({dateFormat: 'mm-dd'}).datepicker('show');
		});
		
		$(document).on('click', '.carrental_remove_holiday', function (e) {
			e.preventDefault();
			$(this).parent().remove();
		});
		
		$('#carrental_add_holidays').click(function(e){
			e.preventDefault();
			if ($('#carrental-holiday-date').val() == '') {
				return;
			}
			$('#carrental_holidays_div p').remove();
			$('#carrental_holidays_div').append('<span style="margin-right:5px;margin-bottom:5px;" class="carrental_holidays btn btn-warning">'+$('#carrental-holiday-date').val()+' <a href="#" class="carrental_remove_holiday">X</a><input type="hidden" name="carrental_holidays[]" value="'+$('#carrental-holiday-date').val()+'"></span>');
			
		});
	});
</script>