<div class="carrental-wrapper">
	
	<?php include CARRENTAL__PLUGIN_DIR . 'views/header.php'; ?>
	
	<div class="row">
		<div class="col-md-12 carrental-main-wrapper">
			<div class="carrental-main-content">
				
				<?php include CARRENTAL__PLUGIN_DIR . 'views/flash_msg.php'; ?>
				
				<div class="row">
					<div class="col-md-11">
						
						<?php $current_lang = (isset($_GET['language']) ? $_GET['language'] : NULL); ?>
						<?php $available_languages = unserialize(get_option('carrental_available_languages')); ?>
						
						<ul class="nav nav-pills">
							<?php if ($available_languages && !empty($available_languages)) { ?>
							<?php if (isset($available_languages['en_GB'])) {
									unset($available_languages['en_GB']);
							} ?>
								<?php foreach ($available_languages as $key => $val) { ?>
						  		<li <?php if ($current_lang == $key) { ?>class="active"<?php } ?>><a href="<?= CarRental_Admin::get_page_url('carrental-translations') ?>&amp;language=<?= $key ?>"><?= $val['lang'] ?> (<?= strtoupper($val['country-www']) ?>)</a></li>
								<?php } ?>
						  <?php } ?>
						  
						  <li <?php if ($current_lang == 'en_GB') { ?>class="active"<?php } ?>><a href="<?= CarRental_Admin::get_page_url('carrental-translations') ?>&amp;language=en_GB">English (GB)</a></li>
						  <li><a href="javascript:void(0);" id="carrental-language-add-button"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add new language</a></li>
						  <li><a href="javascript:void(0);" id="carrental-language-primary-button"><span class="glyphicon glyphicon-star"></span>&nbsp;&nbsp;Set primary language</a></li>
						
						</ul>
						
						<div id="carrental-language-add-form" class="carrental-add-form">
							<form role="form" action="" method="post">
								<div class="row">
									<div class="col-md-6">
									  <div class="form-group">
									    <label for="selectLanguage">Language</label>
									    <select class="form-control" name="language" id="selectLanguage">
									    	<option value="0">- select -</option>
									    	<?php foreach ($languages as $key => $val) { ?>
												<?php if ($key == 'en_GB') { continue; } ?>
									    		<option value="<?= $key ?>"><?= $val['lang'] ?> (<?= strtoupper($val['country-www']) ?>)</option>
									    	<?php } ?>
									    </select>
									  </div>
									  <button type="submit" class="btn btn-warning" name="add_language"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Add new language</button>
									</div>
								</div>
							</form>
						</div>
						
						<div id="carrental-language-primary-form" class="carrental-add-form">
							<form role="form" action="" method="post">
								<div class="row">
									<div class="col-md-6">
									  <div class="form-group">
									    <label for="selectLanguage">Language</label>
									    <?php
									    	$primary_language = 'en_GB';
												$user_set_language = get_option('carrental_primary_language');
												if ($user_set_language && !empty($user_set_language)) {
													$primary_language = $user_set_language;
												}
											?>
											<p>Current primary language is: <strong><?= $languages[$primary_language]['lang'] ?> (<?= strtoupper($languages[$primary_language]['country-www']) ?>)</strong></p>
									    <select class="form-control" name="language" id="selectLanguage">
									    	<option value="en_GB" <?php if ($primary_language == 'en_GB') { ?>selected<?php } ?>>English (UK)</option>
									    	<?php if ($available_languages && !empty($available_languages)) { ?>
													<?php foreach ($available_languages as $key => $val) { ?>
											  		<option value="<?= $key ?>" <?php if ($primary_language == $key) { ?>selected<?php } ?>><?= $val['lang'] ?> (<?= strtoupper($val['country-www']) ?>)</option>
													<?php } ?>
											  <?php } ?>
									    </select>
									  </div>
									  <button type="submit" class="btn btn-warning" name="primary_language"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Set language as primary</button>
									</div>
								</div>
							</form>
						</div>
						
						<hr>
		
						<?php if (!empty($current_lang)) { ?>
							
							<!-- THEME //-->
							<div class="panel panel-warning">
								<div class="panel-heading"><h4><a href="javascript:void(0);" class="carrental_translations_theme_toggle"><span>▼</span>&nbsp;&nbsp;Theme translations</a></h4></div>
							  <div class="panel-body carrental_translations_theme">
							  	
							    <form role="form" action="" method="post">
									  
								  	<?php if ($translations_theme && !empty($translations_theme)) { ?>
									  	<?php foreach ($translations_theme as $key => $val) { ?>
										  	<div class="form-group">
										  		<div class="row">
										  			<div class="col-md-6">
										  				<?= htmlspecialchars(stripslashes(str_replace('\\\\','',$key))) ?>
										  			</div>
										  			<div class="col-md-6">
														<input type="hidden" class="form-control" name="translation[key][]" value="<?= htmlspecialchars(stripslashes(str_replace('\\\\','',$key))) ?>">
										  				<input type="text" class="form-control" name="translation[val][]" value="<?= htmlspecialchars(stripslashes(str_replace('\\\\','',$val))) ?>">
										  			</div>
										  		</div>
										  	</div>
									  	<?php } ?>
								  	<?php } else { ?>
										If there are no strings to translate, visit Appearance-> theme settings. This will automatically activate translations in your theme. If problems persist, make sure your DB is using UTF-8 coding.
								  	<?php } ?>
								  	
									  <input type="hidden" name="language" value="<?= $current_lang ?>">
									  <button type="submit" class="btn btn-warning" name="language_save_theme_translations"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Confirm &amp; Save</button>
									</form>
								</div>
							</div>
							
							<!-- E-MAIL //-->
							<div class="panel panel-warning">	
								<div class="panel-heading"><h4><a href="javascript:void(0);" class="carrental_translations_email_customers_toggle"><span>▼</span>&nbsp;&nbsp;E-mail for customers</a></h4></div>
							  <div class="panel-body carrental_translations_email_customers">
							  	
							  	<?php $email_body = get_option('carrental_reservation_email_' . $current_lang); ?>
								<?php $email_subject = get_option('carrental_reservation_email_subject_' . $current_lang); ?>
							  	
							    <form role="form" action="" method="post">
									  <div class="form-group">
										  <label for="reservation_email_subject">Reservation subject</label>
										  <input class="form-control" type="text" id="reservation_email_subject" name="reservation_email_subject" value="<?php if (!empty($email_subject)) { ?>
<?= $email_subject ?>
<?php } else { ?>
Reservation confirmation #[ReservationNumber]
<?php } ?>">
									    <label for="reservation_email">Reservation e-mail</label>
									    <textarea class="form-control" rows="20" id="reservation_email" name="reservation_email">
<?php if (!empty($email_body)) { ?>
<?= CarRental::removeslashes(stripslashes(str_replace('\\\\','',$email_body))) ?>
<?php } else { ?>
Dear [CustomerName],

thank you for your reservation. Here are your reservation details:
[ReservationDetails]
[ReservationNumber]

You can return to your reservation summary page anytime by going to this link:
[ReservationLink]

We are also sending this information to the email address you have provided.

If you would like to change the reservation details, you can do so by calling our office at:
+123 456 789 or by email example@example.org

[ReservationLinkStart]Click here[ReservationLinkEnd] to print your reservation - takes them to reservation summary print out.

Thank you for your business!
<?php } ?>
									    </textarea>
									  </div>
									  <div class="form-group">
									  	<p><strong>Available variables</strong></p>
									  	<ul style="margin-left:20px;list-style-type:circle;">
									  		<li><strong>[CustomerName]</strong> -> John Doe, Phil Smith, ...</li>
											<li><strong>[Car]</strong> -> Ford GT, ...</li>
											<li><strong>[pickupdate]</strong> -> Date and time eg. 2015-05-01 13:30</li>
											<li><strong>[dropoffdate]</strong> -> Date and time eg. 2015-05-01 13:30</li>
											<li><strong>[pickup_location]</strong> -> New York, ...</li>
											<li><strong>[dropoff_location]</strong> -> Somewhere in the middle of the nowhere, ...</li>
											<li><strong>[total_payment]</strong> -> $1574</li>
											<li><strong>[customer_comment]</strong> -> comment from step 3</li>
											<li><strong>[rate]</strong> -> Display the actually rental rate per day</li>
											<li><strong>[rental_days]</strong> -> Display how many selected rental days</li>
									  		<li><strong>[ReservationDetails]</strong> -> Dates, Address, Selected Car, Price</li>
									  		<li><strong>[ReservationNumber]</strong> -> #123456</li>
									  		<li><strong>[ReservationLink]</strong> -> http://example.org/reservation/123456</li>
									  		<li><strong>[ReservationLinkStart]</strong>Any text<strong>[ReservationLinkEnd]</strong></li>
									  	</ul>
									  </div>
										<p>*You can use HTML tags to format this email.</p>
									  <input type="hidden" name="language" value="<?= $current_lang ?>">
									  <button type="submit" class="btn btn-warning" name="language_save_email"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Confirm &amp; Save</button>
									</form>
							  </div>
							</div>
							
							
							<!-- TERMS and CONDITIONS //-->
							<div class="panel panel-warning">	
								<div class="panel-heading"><h4><a href="javascript:void(0);" class="carrental_translations_terms_toggle"><span>▼</span>&nbsp;&nbsp;Terms &amp; Conditions</a></h4></div>
							  <div class="panel-body carrental_translations_terms">
							  	
							  	<?php $terms_body = get_option('carrental_terms_conditions_' . $current_lang); ?>
							  	
							    <form role="form" action="" method="post">
									  <div class="form-group">
									    <label for="terms_conditions">Terms &amp; Conditions</label>
									    <textarea class="form-control" rows="20" id="terms_conditions" name="terms_conditions">
<?php if (!empty($terms_body)) { ?>
<?= CarRental::removeslashes(stripslashes(str_replace('\\\\','',$terms_body))) ?>
<?php } else { ?>
Terms and Conditions

...
<?php } ?>
									    </textarea>
									  </div>
									  <p>You can use HTML tags to format your terms and conditions here.</p>
									  <input type="hidden" name="language" value="<?= $current_lang ?>">
									  <button type="submit" class="btn btn-warning" name="language_save_terms"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Confirm &amp; Save</button>
									</form>
							  </div>
							</div>
							
							<!-- AUTOMATIC REMINDER EMAIL //-->
							<div class="panel panel-warning">	
								<div class="panel-heading"><h4><a href="javascript:void(0);" class="carrental_translations_email_reminder_customers_toggle"><span>▼</span>&nbsp;&nbsp;E-mail for automatic reminder</a></h4></div>
							  <div class="panel-body carrental_translations_email_reminder_customers">
							  	
							  	<?php $email_body = get_option('carrental_reminder_email_' . $current_lang); ?>
								 <?php $email_subject = get_option('carrental_reminder_subject_' . $current_lang); ?>
							  	
							    <form role="form" action="" method="post">
									  <div class="form-group">
										  <label for="reminder_subject">Automatic reminder subject</label>
										  <input class="form-control" type="text" id="reminder_subject" name="reminder_subject" value="<?php if (!empty($email_subject)) { ?>
<?= $email_subject ?>
<?php } else { ?>
Reservation reminder
<?php } ?>">
										  
									    <label for="reminder_email">Automatic reminder e-mail</label>
									    <textarea class="form-control" rows="20" id="reminder_email" name="reminder_email">
<?php if (!empty($email_body)) { ?>
<?= CarRental::removeslashes(stripslashes(str_replace('\\\\','',$email_body))) ?>
<?php } else { ?>
Dear [CustomerName],

do not forget on your reservation. Here are your reservation details:
[ReservationDetails]
[ReservationNumber]

You can see your reservation summary page anytime by going to this link:
[ReservationLink]

[ReservationLinkStart]Click here[ReservationLinkEnd] to print your reservation - takes them to reservation summary print out.

Thank you for your business!
<?php } ?>
									    </textarea>
									  </div>
									  <div class="form-group">
									  	<p><strong>Available variables</strong></p>
									  	<ul style="margin-left:20px;list-style-type:circle;">
									  		<li><strong>[CustomerName]</strong> -> John Doe, Phil Smith, ...</li>
											<li><strong>[Car]</strong> -> Ford GT, ...</li>
											<li><strong>[pickupdate]</strong> -> Date and time eg. 2015-05-01 13:30</li>
											<li><strong>[dropoffdate]</strong> -> Date and time eg. 2015-05-01 13:30</li>
											<li><strong>[pickup_location]</strong> -> New York, ...</li>
											<li><strong>[dropoff_location]</strong> -> Somewhere in the middle of the nowhere, ...</li>
											<li><strong>[total_payment]</strong> -> $1574</li>
											<li><strong>[customer_comment]</strong> -> comment from step 3</li>
											<li><strong>[rate]</strong> -> Display the actually rental rate per day</li>
											<li><strong>[rental_days]</strong> -> Display how many selected rental days</li>
									  		<li><strong>[ReservationDetails]</strong> -> Dates, Address, Selected Car, Price</li>
									  		<li><strong>[ReservationNumber]</strong> -> #123456</li>
									  		<li><strong>[ReservationLink]</strong> -> http://example.org/reservation/123456</li>
									  		<li><strong>[ReservationLinkStart]</strong>Any text<strong>[ReservationLinkEnd]</strong></li>
									  	</ul>
									  </div>
										<p>*You can use HTML tags to format this email.</p>
									  <input type="hidden" name="language" value="<?= $current_lang ?>">
									  <button type="submit" class="btn btn-warning" name="language_save_email_reminder"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Confirm &amp; Save</button>
									</form>
							  </div>
							</div>
							
							<!-- THANK YOU EMAIL //-->
							<div class="panel panel-warning">	
								<div class="panel-heading"><h4><a href="javascript:void(0);" class="carrental_translations_email_thank_you_toggle"><span>▼</span>&nbsp;&nbsp;Thank you email</a></h4></div>
							  <div class="panel-body carrental_translations_email_thank_you">
							  	
							  	<?php $email_body = get_option('carrental_thank_you_email_' . $current_lang); ?>
								<?php $email_subject = get_option('carrental_thank_you_email_subject_' . $current_lang); ?>
							  	
							    <form role="form" action="" method="post">
									  <div class="form-group">
										  <label for="thank_you_email_subject">Thank you email subject</label>
										  <input class="form-control" type="text" id="thank_you_email_subject" name="thank_you_email_subject" value="<?php if (!empty($email_subject)) { ?>
<?= $email_subject ?>
<?php } else { ?>
Thank for your reservation #[ReservationNumber]
<?php } ?>">
										  
									    <label for="thank_you_email">Thank you e-mail</label>
									    <textarea class="form-control" rows="20" id="thank_you_email" name="thank_you_email">
<?php if (!empty($email_body)) { ?>
<?= CarRental::removeslashes(stripslashes(str_replace('\\\\','',$email_body))) ?>
<?php } else { ?>
Hi [CustomerName],

We hope everything went well with your rental. We loved having you as a customer. Let us know again when you are looking for a good deal on a rental car.

Your rental team
<?php } ?>
									    </textarea>
									  </div>
									  <div class="form-group">
									  	<p><strong>Available variables</strong></p>
									  	<ul style="margin-left:20px;list-style-type:circle;">
									  		<li><strong>[CustomerName]</strong> -> John Doe, Phil Smith, ...</li>
											<li><strong>[Car]</strong> -> Ford GT, ...</li>
											<li><strong>[pickupdate]</strong> -> Date and time eg. 2015-05-01 13:30</li>
											<li><strong>[dropoffdate]</strong> -> Date and time eg. 2015-05-01 13:30</li>
											<li><strong>[pickup_location]</strong> -> New York, ...</li>
											<li><strong>[dropoff_location]</strong> -> Somewhere in the middle of the nowhere, ...</li>
											<li><strong>[total_payment]</strong> -> $1574</li>
											<li><strong>[customer_comment]</strong> -> comment from step 3</li>
											<li><strong>[rate]</strong> -> Display the actually rental rate per day</li>
											<li><strong>[rental_days]</strong> -> Display how many selected rental days</li>
									  		<li><strong>[ReservationDetails]</strong> -> Dates, Address, Selected Car, Price</li>
									  		<li><strong>[ReservationNumber]</strong> -> #123456</li>
									  		<li><strong>[ReservationLink]</strong> -> http://example.org/reservation/123456</li>
									  		<li><strong>[ReservationLinkStart]</strong>Any text<strong>[ReservationLinkEnd]</strong></li>
									  	</ul>
									  </div>
										<p>*You can use HTML tags to format this email.</p>
									  <input type="hidden" name="language" value="<?= $current_lang ?>">
									  <button type="submit" class="btn btn-warning" name="language_save_email_thank_you"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Confirm &amp; Save</button>
									</form>
							  </div>
							</div>
							
							<!-- E-MAIL FOR STATUS PENDING //-->
							<div class="panel panel-warning">	
								<div class="panel-heading"><h4><a href="javascript:void(0);" class="carrental_translations_email_status_pending_toggle"><span>▼</span>&nbsp;&nbsp;E-mail for status pending payment</a></h4></div>
							  <div class="panel-body carrental_translations_email_status_pending">
							  	
							  	<?php $email_body = get_option('carrental_email_status_pending_' . $current_lang); ?>
								<?php $email_subject = get_option('carrental_email_status_pending_subject_' . $current_lang); ?>
							  	
							    <form role="form" action="" method="post">
									  <div class="form-group">
										  <label for="email_status_pending_subject">Status pending payment email subject</label>
										  <input class="form-control" type="text" id="email_status_pending_subject" name="email_status_pending_subject" value="<?php if (!empty($email_subject)) { ?>
<?= $email_subject ?>
<?php } else { ?>
Reservation #[ReservationNumber] is pending
<?php } ?>">
									    <label for="email_status_pending">Pending payment e-mail</label>
									    <textarea class="form-control" rows="20" id="email_status_pending" name="email_status_pending">
<?php if (!empty($email_body)) { ?>
<?= CarRental::removeslashes(stripslashes(str_replace('\\\\','',$email_body))) ?>
<?php } else { ?>
Dear [CustomerName],

thank you for your reservation. We have received it and one of our agents will review it momentarily. At this moment, your reservation is pending payment.

One we have confirmed your reservation, we will inform you via email.

Thank you,

reservation team @websiteurl
<?php } ?>
									    </textarea>
									  </div>
									  <div class="form-group">
									  	<p><strong>Available variables</strong></p>
									  	<ul style="margin-left:20px;list-style-type:circle;">
									  		<li><strong>[CustomerName]</strong> -> John Doe, Phil Smith, ...</li>
											<li><strong>[Car]</strong> -> Ford GT, ...</li>
											<li><strong>[pickupdate]</strong> -> Date and time eg. 2015-05-01 13:30</li>
											<li><strong>[dropoffdate]</strong> -> Date and time eg. 2015-05-01 13:30</li>
											<li><strong>[pickup_location]</strong> -> New York, ...</li>
											<li><strong>[dropoff_location]</strong> -> Somewhere in the middle of the nowhere, ...</li>
											<li><strong>[total_payment]</strong> -> $1574</li>
											<li><strong>[customer_comment]</strong> -> comment from step 3</li>
											<li><strong>[rate]</strong> -> Display the actually rental rate per day</li>
											<li><strong>[rental_days]</strong> -> Display how many selected rental days</li>
									  		<li><strong>[ReservationDetails]</strong> -> Dates, Address, Selected Car, Price</li>
									  		<li><strong>[ReservationNumber]</strong> -> #123456</li>
									  		<li><strong>[ReservationLink]</strong> -> http://example.org/reservation/123456</li>
									  		<li><strong>[ReservationLinkStart]</strong>Any text<strong>[ReservationLinkEnd]</strong></li>
									  	</ul>
									  </div>
										<p>*You can use HTML tags to format this email.</p>
									  <input type="hidden" name="language" value="<?= $current_lang ?>">
									  <button type="submit" class="btn btn-warning" name="language_save_email_status_pending"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Confirm &amp; Save</button>
									</form>
							  </div>
							</div>
							
							<!-- E-MAIL FOR STATUS PENDING OTHER //-->
							<div class="panel panel-warning">	
								<div class="panel-heading"><h4><a href="javascript:void(0);" class="carrental_translations_email_status_pending_other_toggle"><span>▼</span>&nbsp;&nbsp;E-mail for status pending other</a></h4></div>
							  <div class="panel-body carrental_translations_email_status_pending_other">
							  	
							  	<?php $email_body = get_option('carrental_email_status_pending_other_' . $current_lang); ?>
								<?php $email_subject = get_option('carrental_email_status_pending_other_subject_' . $current_lang); ?>
							  	
							    <form role="form" action="" method="post">
									  <div class="form-group">
										  <label for="email_status_pending_other_subject">Status pending other email subject</label>
										  <input class="form-control" type="text" id="email_status_pending_other_subject" name="email_status_pending_other_subject" value="<?php if (!empty($email_subject)) { ?>
<?= $email_subject ?>
<?php } else { ?>
Reservation #[ReservationNumber] is pending
<?php } ?>">
									    <label for="email_status_pending_other">Pending other e-mail</label>
									    <textarea class="form-control" rows="20" id="email_status_pending_other" name="email_status_pending_other">
<?php if (!empty($email_body)) { ?>
<?= CarRental::removeslashes(stripslashes(str_replace('\\\\','',$email_body))) ?>
<?php } else { ?>
Dear [CustomerName],

thank you for your reservation. We have received it and one of our agents will review it momentarily. At this moment, your reservation is pending.

One we have confirmed your reservation, we will inform you via email.

Thank you,

reservation team @ website url
<?php } ?>
									    </textarea>
									  </div>
									  <div class="form-group">
									  	<p><strong>Available variables</strong></p>
									  	<ul style="margin-left:20px;list-style-type:circle;">
									  		<li><strong>[CustomerName]</strong> -> John Doe, Phil Smith, ...</li>
											<li><strong>[Car]</strong> -> Ford GT, ...</li>
											<li><strong>[pickupdate]</strong> -> Date and time eg. 2015-05-01 13:30</li>
											<li><strong>[dropoffdate]</strong> -> Date and time eg. 2015-05-01 13:30</li>
											<li><strong>[pickup_location]</strong> -> New York, ...</li>
											<li><strong>[dropoff_location]</strong> -> Somewhere in the middle of the nowhere, ...</li>
											<li><strong>[total_payment]</strong> -> $1574</li>
											<li><strong>[customer_comment]</strong> -> comment from step 3</li>
											<li><strong>[rate]</strong> -> Display the actually rental rate per day</li>
											<li><strong>[rental_days]</strong> -> Display how many selected rental days</li>
									  		<li><strong>[ReservationDetails]</strong> -> Dates, Address, Selected Car, Price</li>
									  		<li><strong>[ReservationNumber]</strong> -> #123456</li>
									  		<li><strong>[ReservationLink]</strong> -> http://example.org/reservation/123456</li>
									  		<li><strong>[ReservationLinkStart]</strong>Any text<strong>[ReservationLinkEnd]</strong></li>
									  	</ul>
									  </div>
										<p>*You can use HTML tags to format this email.</p>
									  <input type="hidden" name="language" value="<?= $current_lang ?>">
									  <button type="submit" class="btn btn-warning" name="language_save_email_status_pending_other"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Confirm &amp; Save</button>
									</form>
							  </div>
							</div>
							
							<?php
					    	$primary_language = 'en_GB';
								$user_set_language = get_option('carrental_primary_language');
								if ($user_set_language && !empty($user_set_language)) {
									$primary_language = $user_set_language;
								}
							?>
							<?php if ($current_lang != 'en_GB' && $primary_language != $current_lang) { ?>
								<form action="" method="post" class="form" role="form" onsubmit="return confirm('<?= __('Do you really want to disable this language?', 'carrental') ?>');">
									<input type="hidden" name="language" value="<?= $current_lang ?>">
									<button class="btn btn-danger" name="disable_language"><span class="glyphicon glyphicon-remove"></span>&nbsp;&nbsp;Disable this language</button>
									<p class="help-block">
										* You will have to reenable this language to be able to edit it. Disabled language translations are saved in DB, so you do not lose your translations. Once you enable previously disabled language all previous translations will be back (unless you make changes to DB manually)
									</p>
								</form>
								
							<?php } else { ?>
								<p class="help-block">
									* This language cannot be disabled. It's set as a primary or is it default language.
								</p>
							<?php } ?>
								
							<?php if ($current_lang != 'en_GB' && $primary_language != $current_lang) { ?>
								<form action="" method="post" class="form" role="form" onsubmit="return confirm('<?= __('Do you really want to deactivate this language?', 'carrental') ?>');">
									<input type="hidden" name="language" value="<?= $current_lang ?>">
									<?php if ((isset($available_languages[$current_lang]['active']) && $available_languages[$current_lang]['active']) || !isset($available_languages[$current_lang]['active'])) { ?>
										<button class="btn btn-danger" name="deactivate_language"><span class="glyphicon glyphicon-remove"></span>&nbsp;&nbsp;Deactivate this language</button>
									<?php } else { ?>
										<button class="btn btn-success" name="activate_language"><span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;Activate this language</button>
									<?php } ?>
									<p class="help-block">
										* Clients will not see this language on your site. Deactivate a language to edit your translations while users don`t see them. 
									</p>
								</form>
								
							<?php } else { ?>
								<p class="help-block">
									* This language cannot be deactivated. It's set as a primary or is it default language.
								</p>
							<?php } ?>
							<form action="" method="post" class="form" role="form">
								<input type="hidden" name="language" value="<?= $current_lang ?>">
								<button class="btn btn-warning" name="export_language">Export this language</button>
							</form>
								
								<form action="" method="post" class="form" role="form" enctype="multipart/form-data">
								<div class="form-group">
									<input type="file" name="input_file" />
									<input type="hidden" name="language" value="<?= $current_lang ?>">
									<button name="import_language" class="btn btn-success">Import language from file</button>
									<p class="help-block">
										* This file will rewrite currently selected language!
									</p>
								</div>
							</form>
									
						<?php } else { ?>
							<p>
								Please, select language to edit or create new language.
							</p>
						<?php } ?>
						
					</div>
				</div>
				
			</div>
		</div>
	</div>
	
</div>