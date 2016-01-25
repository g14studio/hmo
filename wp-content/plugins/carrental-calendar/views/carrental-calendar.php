<div class="carrental-wrapper">

	<?php include CARRENTAL_CALENDAR__PLUGIN_DIR . 'views/header.php'; ?>

	<div class="row">
		<div class="col-md-12 carrental-main-wrapper">
			<div class="carrental-main-content">

				<?php include CARRENTAL_CALENDAR__PLUGIN_DIR . 'views/flash_msg.php'; ?>
				
				<!-- Automatic upgrade //-->
				<div class="panel panel-default">
					<div class="panel-heading"><h4>Automatic plugin update</h4></div>
					<div class="panel-body">
					  
						<div class="row">
							<div class="col-md-12">
								<?php $check = unserialize(get_option('carrental_calendar_update_check')); ?>
								<?php $apikey = unserialize(get_option('carrental_api_key')); ?>
								<?php $apikey_exp = get_option('carrental_api_key_expiration'); ?>
								<div class="row">
									<div class="col-md-6">
										
										<?php if (!$apikey || empty($apikey)) { ?>
										<p>You must set API KEY in main carrental plugin first.</p>
										<?php } else { ?>
											<form action="" method="post" role="form" class="form-horizontal">
												<div class="form-group">
												  <label class="col-sm-3 control-label uprava1" style="padding-top:0;">Last check</label>
												  <div class="col-sm-9 uprava2">

													  <?php if (isset($check['last']) && strtotime($check['last']) != false) { ?>
															  <?= Date('Y-m-d', strtotime($check['last'])) ?>
														  <?php } else { ?>
															  <em>- never -</em>
														  <?php } ?>

														  &nbsp;&nbsp;<button type="submit" name="calendar_check_plugin_update" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-refresh"></span>&nbsp;&nbsp;Check plugin update manually</button>

														  <?php if (!isset($check['update_available']) || $check['update_available'] == false) { ?>
															  <br><br>Current plugin version: <strong><?= CARRENTAL_CALENDAR_VERSION ?></strong>
															  <br><br><em>There is no new plugin update available.</em>
														  <?php } ?>
												  </div>
												</div>
											</form>

											<?php if (isset($check['update_available']) && $check['update_available'] == true) { ?>
												<form action="" method="post" role="form" class="form-horizontal">
													<div class="form-group">
													  <label class="col-sm-3 control-label">Plugin update is available!</label>
													  <div class="col-sm-9">

														  Current version: <?= CARRENTAL_CALENDAR_VERSION ?><br>
															  <strong>New version: <?= $check['new_version'] ?></strong> (<?= $check['new_version_date'] ?>)<br><br>

															  <button type="submit" name="calendar_plugin_update" class="btn btn-success"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Download, backup and install update</button>

															  <br /><em>* Do not close this window while installing new version. Backup will be created automatically and saved under wp-content/plugins/carrental-calendar/backup.</em>

													  </div>
													</div>
												</form>
											<?php } ?>
									  <?php } ?>
								  </div>
							  </div>
							  
							</div>
						</div>
					</div>
				</div>

				<form action="" method="get" class="filter-form">
					<input type="hidden" name="page" value="<?php echo $_GET['page'];?>" />
					<div class="container">
						<div class="row">
							<div class="col-xs-12 col-sm-3">
								<label for="date_from">Date from:</label> <input type="text" name="date_from" id="date_from" value="<?php echo date('m/d/Y', $date_from_timestamp); ?>" />
							</div>

							<div class="col-xs-12 col-sm-4">
								<label for="date_to">Date to:</label> <input type="text" name="date_to" id="date_to" value="<?php echo date('m/d/Y', $date_to_timestamp); ?>" />
								<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button" aria-disabled="false"><span class="ui-button-icon-primary ui-icon ui-icon-search"></span><span class="ui-button-text">Show</span></button>
							</div>
						</div>
					</div>
				</form>
				
				<?php
				$show_notes = (int)get_option('carrental_calendar_show_note') > 0 ? true :  false;
				?>
				<label class="showNotes"><input type="checkbox" name="show-notes" id="carrental-calendar-show-notes" value="1"<?php echo $show_notes ? ' checked="checked"' : '';?>> Show notes</label>
				
				<div class="table-content dragscroll">
					<table>
						<tr>
							<th>Car name and photo</th>
							<?php foreach ($days as $day) { ?>
								<th><?php echo date('M j Y', $day); ?></th>
							<?php } ?>
						</tr>
						<?php if (!isset($vehicles) || empty($vehicles)) { ?>
						<tr><td colspan="<?php echo count($days) + 1;?>">No bookings found.</td></tr>
						<?php } else { ?>
							<?php foreach ($vehicles as $vehicle) { ?>
								<tr>
									<th>
										<div class="img-area">
											<img src="<?php echo $vehicle['picture']; ?>" alt="<?php echo $vehicle['name']; ?>">
										</div>
										<p class="name"><?php echo $vehicle['name']; ?></p>
								</th>
								<?php
								$positions = array();
								$lastDay = end($days);
								$firstDay = reset($days);
								$rows = array();
								foreach ($days as $day) {
									?>
									<td>
										<?php
										$in_this_day = 0;
										foreach ($vehicle['books'] as $book) {
											$from_day = strtotime(date('Y-m-d', strtotime($book['enter_date'])) . ' 00:00:00');
											$to_day = strtotime(date('Y-m-d', strtotime($book['return_date'])) . ' 00:00:00');
											if ($from_day < $firstDay) {
												$from_day = $firstDay;
											}

											if ($to_day > $lastDay) {
												$to_day = $lastDay;
											}
											if ($from_day == $day) {
												$in_this_day++;
												$margin_top = 0;
												if (isset($rows[$in_this_day]) && $rows[$in_this_day] > $day) {
													$margin_top += 94;
													//$while_days = $in_this_day + 1;
													$in_this_day++;
													while (true) {
														if (isset($rows[$in_this_day]) && $rows[$in_this_day] > $day) {
															$margin_top += 95;
															$in_this_day++;
														} else {
															break;
														}
													}
												}

												if (!isset($rows[$in_this_day])) {
													$rows[$in_this_day] = $to_day;
												}

												if ($rows[$in_this_day] < $to_day) {
													$rows[$in_this_day] = $to_day;
												}


												$day_count = round(($to_day - $from_day) / (3600 * 24));
												?>
												<div class="<?php echo $show_notes ? '' : 'noNotes ';?>bar<?php echo $day_count <= 1 ? ' one-day' : ''; ?><?php echo strtotime($book['enter_date']) < $firstDay ? ' no-start' : ''; ?><?php echo strtotime($book['return_date']) > $lastDay ? ' no-end' : ''; ?>" style="<?php echo ($margin_top > 0) ? 'margin-top:' . ($margin_top) . 'px;' : ''; ?>width: <?php echo (($day_count) * 80) - 3; ?>px;">
													<a title="From: <?php echo date('Y-m-d H:i', strtotime($book['enter_date'])); ?> to <?php echo date('Y-m-d H:i', strtotime($book['return_date'])); ?>" href="<?php echo esc_url(home_url('/')); ?>?page=carrental&summary=<?php echo $book['hash']; ?>" target="_blank" class="ref-number">#<?php echo $book['id_order']; ?></a>
													<input type="text" class="client internal_note" data-id="<?php echo $book['id_booking'];?>" placeholder="notes" value="<?php echo isset($book['internal_note']) ? $book['internal_note'] : $book['internal_note']; ?>">
													<p class="from" title="<?php echo date('Y-m-d H:i', strtotime($book['enter_date'])); ?>"><?php echo date('H:i', strtotime($book['enter_date'])); ?></p>
													<p class="until-text">UNTIL</p>
													<p class="until" title="<?php echo date('Y-m-d H:i', strtotime($book['return_date'])); ?>"><?php echo date('H:i', strtotime($book['return_date'])); ?></p>
												</div>
												<?php
											}
										}
										?>
									</td>
								<?php } ?>
								</tr>
							<?php } ?>
						<?php } ?>
					</table>
				</div>


			</div>
		</div>
	</div>

</div>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		
		$( "#date_from" ).datepicker({
			changeMonth: true,
			numberOfMonths: 3,
			onClose: function( selectedDate ) {
				$( "#date_to" ).datepicker( "option", "minDate", selectedDate );
			}
		});
		$( "#date_to" ).datepicker({						
			changeMonth: true,
			numberOfMonths: 3,
			onClose: function( selectedDate ) {
				$( "#date_from" ).datepicker( "option", "maxDate", selectedDate );
			}
		});
		
		jQuery('#carrental-calendar-show-notes').change(function(){
			var show_note = 0;
			if (jQuery(this).is(":checked")) {
				show_note = 1;
				jQuery('.bar').removeClass('noNotes');
			} else {
				jQuery('.bar.noNotes').removeClass('noNotes');
				jQuery('.bar').addClass('noNotes');
			}
			
			$.ajax({
				url: ajaxurl,
				type: "POST",
				cache: false,
				dataType: 'json',
				data: 'show='+show_note+'&action=show_note'
			}); 
		});
		
		jQuery('.internal_note').blur(function(){
			var linkElement = $(this);
			
			$.ajax({
				url: ajaxurl,
				type: "POST",
				cache: false,
				dataType: 'json',
				data: 'note='+linkElement.val()+'&id='+linkElement.attr('data-id')+'&action=save_note'
			}); 
		});
	});
</script>