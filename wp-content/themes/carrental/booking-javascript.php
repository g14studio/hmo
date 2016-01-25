<script type="text/javascript">
<?php if (defined('CARRENTAL_FIXED_DATES_VERSION')) { ?>
	var carrental_fixed_dates = <?php echo CarRental_Fixed_dates::get_active_pricing(true);?>;
<?php } ?>
<?php $disable_time = get_option('carrental_disable_time'); ?>
<?php if ($disable_time == 'yes') {$disable_time = true;} else {$disable_time = false;} ?>
<?php $theme_options = unserialize(get_option('carrental_theme_options')); ?>
	var carrental_timeformat = <?php echo isset($theme_options['time_format']) ? (int) $theme_options['time_format'] : '24'; ?>;
	
	<?php $holidays = unserialize(get_option('carrental_holidays')); ?>
	var holidays = <?php echo is_array($holidays) ? json_encode($holidays) : '{}'; ?>;
	
	<?php $carrental_minimum_rental_time = unserialize(get_option('carrental_minimum_rental_time')); ?>
	var carrental_minimum_booking_time = <?php echo is_array($carrental_minimum_rental_time) ? json_encode($carrental_minimum_rental_time) : '{}'; ?>;
	var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
	jQuery(document).ready(function() {

		var branch_hours = {};
		var branches = {};
		<?php
		if (isset($_SESSION['carrental_flash_manage_booking'])) {
			echo "alert('".addslashes(CarRental::t('Sorry, could not find your reference number.'))."');";
			?>
			jQuery('#carrental_order_number').val('<?php echo $_SESSION['carrental_flash_manage_booking']['id_order'];?>');
			jQuery('#carrental_order_email').val('<?php echo $_SESSION['carrental_flash_manage_booking']['email'];?>');
			jQuery('a[data-tab-target=manage-booking]').click();	
			<?php
			unset($_SESSION['carrental_flash_manage_booking']);
		}
		?>
				
<?php if (isset($locations) && !empty($locations)) { ?>
	<?php foreach ($locations as $key => $val) { ?>
				branches[<?= $val->id_branch ?>] = {};
				<?php if (isset($val->specific_times) && $val->specific_times == 1) { ?>
					branches[<?= $val->id_branch ?>]['specific_times'] = true;
					<?php if (isset($val->enter_hours) && !empty($val->enter_hours)) { ?>
						branches[<?= $val->id_branch ?>]['enter_hours'] = {};
						<?php foreach ($val->enter_hours as $kD => $vD) { ?>
							<?php if ($vD['from'] == '' || $vD['to'] == '') {continue;} ?>
								branches[<?= $val->id_branch ?>]['enter_hours'][<?= $kD+1 ?>] = {'from': '<?= carrental_time_format($vD['from'], (isset($theme_options['time_format']) ? $theme_options['time_format'] : 24)) ?>', 'to': '<?= carrental_time_format($vD['to'], (isset($theme_options['time_format']) ? $theme_options['time_format'] : 24)) ?>'};
								<?php if ($vD['from_2'] == '' || $vD['to_2'] == '') {continue;} ?>
								branches[<?= $val->id_branch ?>]['enter_hours'][<?= $kD+1 ?>]['from_2'] = '<?= carrental_time_format($vD['from_2'], (isset($theme_options['time_format']) ? $theme_options['time_format'] : 24)) ?>';
								branches[<?= $val->id_branch ?>]['enter_hours'][<?= $kD+1 ?>]['to_2'] = '<?= carrental_time_format($vD['to_2'], (isset($theme_options['time_format']) ? $theme_options['time_format'] : 24)) ?>';
						<?php } ?>
					<?php } ?>
					<?php if (isset($val->return_hours) && !empty($val->return_hours)) { ?>
						branches[<?= $val->id_branch ?>]['return_hours'] = {};
						<?php foreach ($val->return_hours as $kD => $vD) { ?>
							<?php if ($vD['from'] == '' || $vD['to'] == '') {continue;} ?>
								branches[<?= $val->id_branch ?>]['return_hours'][<?= $kD+1 ?>] = {'from': '<?= carrental_time_format($vD['from'], (isset($theme_options['time_format']) ? $theme_options['time_format'] : 24)) ?>', 'to': '<?= carrental_time_format($vD['to'], (isset($theme_options['time_format']) ? $theme_options['time_format'] : 24)) ?>'};
								<?php if ($vD['from_2'] == '' || $vD['to_2'] == '') {continue;} ?>
								branches[<?= $val->id_branch ?>]['return_hours'][<?= $kD+1 ?>]['from_2'] = '<?= carrental_time_format($vD['from_2'], (isset($theme_options['time_format']) ? $theme_options['time_format'] : 24)) ?>';
								branches[<?= $val->id_branch ?>]['return_hours'][<?= $kD+1 ?>]['to_2'] = '<?= carrental_time_format($vD['to_2'], (isset($theme_options['time_format']) ? $theme_options['time_format'] : 24)) ?>';
						<?php } ?>
					<?php } ?>
				<?php } else { ?>
					branches[<?= $val->id_branch ?>]['specific_times'] = false;
				<?php } ?>
				
				branch_hours[<?= $val->id_branch ?>] = {};
		<?php if (isset($val->hours) && !empty($val->hours)) { ?>
			<?php foreach ($val->hours as $kD => $vD) { ?>
						branch_hours[<?= $val->id_branch ?>][<?= $vD->day ?>] = {'from': '<?= carrental_time_format($vD->hours_from, (isset($theme_options['time_format']) ? $theme_options['time_format'] : 24)) ?>', 'to': '<?= carrental_time_format($vD->hours_to, (isset($theme_options['time_format']) ? $theme_options['time_format'] : 24)) ?>'};
						<?php if (!isset($vD->hours_from_2) || $vD->hours_from_2 == '' || $vD->hours_from_2 == '00:00:00') {continue;} ?>
						<?php if (!isset($vD->hours_to_2) || $vD->hours_to_2 == '' || $vD->hours_to_2 == '00:00:00') {continue;} ?>
						branch_hours[<?= $val->id_branch ?>][<?= $vD->day ?>]['from_2'] = '<?= carrental_time_format($vD->hours_from_2, (isset($theme_options['time_format']) ? $theme_options['time_format'] : 24)) ?>';
						branch_hours[<?= $val->id_branch ?>][<?= $vD->day ?>]['to_2'] = '<?= carrental_time_format($vD->hours_to_2, (isset($theme_options['time_format']) ? $theme_options['time_format'] : 24)) ?>';
			<?php } ?>
		<?php } ?>
	<?php } ?>
<?php } ?>
	
		var car_availability = {};
		var ajax_loading = {};
		jQuery('.car-available-button').datepicker({
				//showOn: "both",
				dateFormat: "<?php echo date_format_js(isset($theme_options['date_format']) ? $theme_options['date_format'] : ''); ?>",
				firstDay: "<?php echo (isset($theme_options['date_format_first_day']) ? (int) $theme_options['date_format_first_day'] : 0); ?>",
				dayNamesMin: ["<?php echo CarRental::t('Su') ?>", "<?php echo CarRental::t('Mo') ?>", "<?php echo CarRental::t('Tu') ?>", "<?php echo CarRental::t('Wu') ?>", "<?php echo CarRental::t('Th') ?>", "<?php echo CarRental::t('Fr') ?>", "<?php echo CarRental::t('Sa') ?>"],
				monthNames: ["<?php echo CarRental::t('January') ?>", "<?php echo CarRental::t('February') ?>", "<?php echo CarRental::t('March') ?>", "<?php echo CarRental::t('April') ?>", "<?php echo CarRental::t('May') ?>", "<?php echo CarRental::t('June') ?>", "<?php echo CarRental::t('July') ?>", "<?php echo CarRental::t('August') ?>", "<?php echo CarRental::t('September') ?>", "<?php echo CarRental::t('October') ?>", "<?php echo CarRental::t('November') ?>", "<?php echo CarRental::t('December') ?>"],
				dayNames: ["<?php echo CarRental::t('Sunday') ?>", "<?php echo CarRental::t('Monday') ?>", "<?php echo CarRental::t('Tuesday') ?>", "<?php echo CarRental::t('Wednesday') ?>", "<?php echo CarRental::t('Thursday') ?>", "<?php echo CarRental::t('Friday') ?>", "<?php echo CarRental::t('Saturday') ?>"],
				nextText: "<?php echo CarRental::t('Next') ?>",
				prevText: "<?php echo CarRental::t('Prev') ?>",
				onChangeMonthYear: function(year, month, instance) {
					var key = jQuery(instance.input).attr('data-car-id')+'-'+year+'-'+(month < 10 ? '0'+month : month);
					if (car_availability[key] === undefined) {
						carrental_load_availability(jQuery(instance.input).attr('data-car-id'), year, month);
					}
				},
				beforeShow: function(element, instance) {
					month = new Date().getMonth()+1;
					var key = jQuery(instance.input).attr('data-car-id')+'-'+(new Date().getFullYear())+'-'+(month < 10 ? '0'+month : month);
					if (car_availability[key] === undefined) {
						carrental_load_availability(jQuery(instance.input).attr('data-car-id'), new Date().getFullYear(), new Date().getMonth()+1);
					}
				},
				beforeShowDay: function(date) {
					month = date.getMonth()+1;
					var key = jQuery(this).attr('data-car-id')+'-'+date.getFullYear()+'-'+(month < 10 ? '0'+month : month);
					if (car_availability[key] === undefined) {
						return [false, 'carrental-car-available'];
					}
					if (car_availability[key][date.getDate()] === undefined) {
						return [false, 'carrental-car-available'];
					}
					return [false, car_availability[key][date.getDate()] ? 'carrental-car-no-available' : 'carrental-car-available'];
				}
		});
		
		var carrental_load_availability = function(car_id, year, month) {
			var key = car_id+'-'+year+'-'+month;
			if (car_availability[key] === undefined) {
				jQuery.ajax({
					url: ajaxurl,
					type: "POST",
					cache: false,
					dataType: 'json',
					async: false,
					data: 'fe_ajax=1&car_id='+car_id+'&year='+year+'&month='+month+'&action=carrental_available_cars',
					success: function(data){
							jQuery.each(data, function(k,v){
								car_availability[k] = v;
							})
						}
				}); 
			}
		};
		
		jQuery('.carrental-book-this-car-btn').on('click', function() {
			jQuery('#carrental-hidden-booking-form').attr('data-car-id',jQuery(this).attr('data-car-id'));
			jQuery('#carrental-hidden-booking-form').attr('data-branch-id',jQuery(this).attr('data-branch-id'));
			var id_branch = parseInt(jQuery(this).attr('data-branch-id'));
			
			jQuery('#carrental_enter_location_popup option:selected').removeAttr('selected');
			if (jQuery('#carrental_enter_location_popup option[value='+id_branch+']').length) {				
				jQuery('#carrental_enter_location_popup option').hide();
				jQuery('#carrental_enter_location_popup option[value='+id_branch+']').show().attr('selected',true);
			}
			
			jQuery('.booking-form-overflow').fadeIn(400);
			jQuery('#carrental-hidden-booking-form').fadeIn(800);
		});
		
		jQuery('#carrental-hidden-booking-form #carrental_booking_form_popup').submit(function(e){
			var is_available = true;
			jQuery('#carrental_booking_form_id_car').val(jQuery('#carrental-hidden-booking-form').attr('data-car-id'));
			jQuery.ajax({
				url: ajaxurl,
				type: "POST",
				cache: false,
				dataType: 'json',
				async: false,
				data: 'fe_ajax=1&car_id='+jQuery('#carrental-hidden-booking-form').attr('data-car-id')+'&enter_location='+jQuery('#carrental_enter_location_popup').val()+'&pickup_date='+jQuery('#carrental_from_date_popup').val()+'&pickup_time='+jQuery('#carrental_from_hour_popup').val()+'&return_date='+jQuery('#carrental_to_date_popup').val()+'&return_time='+jQuery('#carrental_to_hour_popup').val()+'&action=carrental_book_now_check',
				success: function(data){
						if (data && data == '1') {
							is_available = true;
						} else {
							is_available = false;
						}
					}
			});
			if (!is_available) {
				jQuery('#carrental_book_errors_popup').html('<li><?php echo addslashes(CarRental::t('Car not available on these dates, please try again or try to search all cars for those dates.'));?></li>');
				e.preventDefault();
			}
		});

		jQuery('p.close-win').on('click', function() {
			jQuery('#carrental-hidden-booking-form').fadeOut(400);
			jQuery('.booking-form-overflow').fadeOut(800);
		});

		jQuery('.booking-form-overflow').on('click', function() {
			jQuery('#carrental-hidden-booking-form').fadeOut(400);
			jQuery(this).fadeOut(800);
		});

		if (jQuery('#carrental_from_hour').length) {
			// call time update after page reload
			carrental_booking_init('');
			init_fixed_dates('');
			carrental_update_business_hours('');
		}

		if (jQuery('#carrental_from_hour_popup').length) {
			// call time update after page reload
			carrental_booking_init('_popup');
			init_fixed_dates('_popup');
			carrental_update_business_hours('_popup');
		}
		
		jQuery('.carrental_car_details').hide();
		jQuery('.carrental_car_details_link').click(function() {			
			jQuery(this).parent().parent().find('.carrental_car_details').toggle('fast');
		});
		
		function init_fixed_dates(element_id) {
			var date_from = jQuery('#carrental_from_date' + element_id).val();
			if (typeof date_from === "undefined" || date_from == '') {
				date_from = jQuery.datepicker.formatDate('<?php echo date_format_js(isset($theme_options['date_format']) ? $theme_options['date_format'] : ''); ?>', new Date());
			}

			var date_to = jQuery('#carrental_to_date' + element_id).val();
			if (typeof date_to === "undefined" || date_to == '') {
				date_to = jQuery.datepicker.formatDate('<?php echo date_format_js(isset($theme_options['date_format']) ? $theme_options['date_format'] : ''); ?>', new Date());
			}

			// reformat to YYYY-MM-DD
			date_from = jQuery.datepicker.formatDate('yy-mm-dd', jQuery.datepicker.parseDate('<?php echo date_format_js(isset($theme_options['date_format']) ? $theme_options['date_format'] : ''); ?>', date_from));
			date_to = jQuery.datepicker.formatDate('yy-mm-dd', jQuery.datepicker.parseDate('<?php echo date_format_js(isset($theme_options['date_format']) ? $theme_options['date_format'] : ''); ?>', date_to));
			if (typeof carrental_fixed_dates_call == 'function') { 
				if (carrental_fixed_dates_call(element_id, date_from, date_to) >= 0) {
					var errors = [];
					errors.push('<?= addslashes(CarRental::t('There is fixed price on this dates.')) ?>');
					jQuery('#carrental_book_errors' + element_id).html('<li>' + errors.join('</li><li>') + '</li>');
				} else {
					jQuery('#carrental_book_errors' + element_id + ' li' ).remove();
				}
			}
		}

		function carrental_booking_init(element_id) {

			jQuery('#carrental_return_location' + element_id).hide();

<?php if (isset($_GET['rl']) && !empty($_GET['rl']) && isset($_GET['dl']) && $_GET['dl'] == 'on') { ?>
				jQuery('#carrental_return_location' + element_id).show();
<?php } ?>
	
			jQuery('#carrental_different_loc' + element_id).click(function() {
				jQuery('#carrental_return_location' + element_id).toggle('fast');
			});
			
			jQuery('#carrental_from_date' + element_id).parent().click(function(){
				jQuery( '#carrental_from_date' + element_id).datepicker( "show" );
			});
			
			jQuery('#carrental_to_date' + element_id).parent().click(function(){
				jQuery( '#carrental_to_date' + element_id).datepicker( "show" );
			});

			jQuery('#carrental_from_date' + element_id + ', #carrental_to_date' + element_id).datepicker({
				//showOn: "both",
				beforeShow: carrental_customRange,
				dateFormat: "<?php echo date_format_js(isset($theme_options['date_format']) ? $theme_options['date_format'] : ''); ?>",
				firstDay: "<?php echo (isset($theme_options['date_format_first_day']) ? (int) $theme_options['date_format_first_day'] : 0); ?>",
				dayNamesMin: ["<?php echo CarRental::t('Su') ?>", "<?php echo CarRental::t('Mo') ?>", "<?php echo CarRental::t('Tu') ?>", "<?php echo CarRental::t('Wu') ?>", "<?php echo CarRental::t('Th') ?>", "<?php echo CarRental::t('Fr') ?>", "<?php echo CarRental::t('Sa') ?>"],
				monthNames: ["<?php echo CarRental::t('January') ?>", "<?php echo CarRental::t('February') ?>", "<?php echo CarRental::t('March') ?>", "<?php echo CarRental::t('April') ?>", "<?php echo CarRental::t('May') ?>", "<?php echo CarRental::t('June') ?>", "<?php echo CarRental::t('July') ?>", "<?php echo CarRental::t('August') ?>", "<?php echo CarRental::t('September') ?>", "<?php echo CarRental::t('October') ?>", "<?php echo CarRental::t('November') ?>", "<?php echo CarRental::t('December') ?>"],
				dayNames: ["<?php echo CarRental::t('Sunday') ?>", "<?php echo CarRental::t('Monday') ?>", "<?php echo CarRental::t('Tuesday') ?>", "<?php echo CarRental::t('Wednesday') ?>", "<?php echo CarRental::t('Thursday') ?>", "<?php echo CarRental::t('Friday') ?>", "<?php echo CarRental::t('Saturday') ?>"],
				nextText: "<?php echo CarRental::t('Next') ?>",
				prevText: "<?php echo CarRental::t('Prev') ?>",				
				onSelect: function() {
					carrental_update_business_hours(element_id);
					if (carrental_fixed_dates.length) {
						init_fixed_dates(element_id);
						carrental_update_business_hours(element_id);
					}
				},
				beforeShowDay: function(date) {
					// test if is not holiday
					month = date.getMonth()+1;
					month = month < 10 ? '0'+month : month;
					day = date.getDate();
					day = day < 10 ? '0'+day : day;
					if (typeof holidays[month+'-'+day] !== "undefined") {
						return [false, ''];
					}
					return [true, ''];
				}
			});

			jQuery('#carrental_enter_location' + element_id).on('change', function() {
				carrental_update_business_hours(element_id);
			});

			jQuery('#carrental_return_location' + element_id).on('change', function() {
				carrental_update_business_hours(element_id);
			});

			jQuery('#carrental_different_loc' + element_id).on('click', function() {
				carrental_update_business_hours(element_id);
			});

			jQuery('#carrental_booking_form' + element_id).on('submit', function() {

				var errors = [];

				// Check enter location
				if (jQuery('#carrental_enter_location' + element_id).val() > 0) {
				} else {
					errors.push('<?= addslashes(CarRental::t('Please, select enter location.')) ?>');
				}

				// Check return location (if checked)
				if (jQuery('#carrental_different_loc' + element_id + ':checked').val() == 'on') {
					if (jQuery('#carrental_return_location' + element_id).val() > 0) {
					} else {
						errors.push('<?= addslashes(CarRental::t('Please, select return location or disable the *Returning to Different location*.')) ?>');
					}
				}

				// Check dates (from and to)
				var from_date = jQuery.datepicker.parseDate('<?php echo date_format_js(isset($theme_options['date_format']) ? $theme_options['date_format'] : ''); ?>', jQuery('#carrental_from_date' + element_id).val());
				
				var date_compare = new Date();
				date_compare.setHours(0,0,0,0);
				
				if (from_date != null && from_date != 'Invalid Date' && from_date >= date_compare) {
				} else {
					errors.push('<?= addslashes(CarRental::t('Please, select pick-up date properly.')) ?>');
				}

				var to_date = jQuery.datepicker.parseDate('<?php echo date_format_js(isset($theme_options['date_format']) ? $theme_options['date_format'] : ''); ?>', jQuery('#carrental_to_date' + element_id).val());
				if (to_date != null && to_date != 'Invalid Date' && to_date >= date_compare) {
				} else {
					errors.push('<?= addslashes(CarRental::t('Please, select return date properly.')) ?>');
				}

				// Check times (from and to)

				if (jQuery('#carrental_from_hour' + element_id).val() != '') {
					if (from_date != null && from_date != 'Invalid Date') {
						time = jQuery('#carrental_from_hour' + element_id).val().split(':');
						from_date.setHours(time[0]);
						from_date.setMinutes(time[1]);
					}
				} else {
					errors.push('<?= addslashes(CarRental::t('Please, select pick-up time properly.')) ?>');
				}

				if (jQuery('#carrental_to_hour' + element_id).val() != '') {
					if (to_date != null && to_date != 'Invalid Date') {
						time = jQuery('#carrental_to_hour' + element_id).val().split(':');
						to_date.setHours(time[0]);
						to_date.setMinutes(time[1]);
					}
				} else {
					errors.push('<?= addslashes(CarRental::t('Please, select return time properly.')) ?>');
				}
				
				// check minimum rental times
				if (from_date != null && from_date != 'Invalid Date') {
					var diff_hours = Math.abs(to_date - from_date) / 36e5;				
					key = parseInt(from_date.getMonth())+1;
					if (carrental_minimum_booking_time[key] !== undefined && carrental_minimum_booking_time[key] > 0) {
						if (diff_hours < (carrental_minimum_booking_time[key] * 24)) {
							errors.push('<?= addslashes(CarRental::t('Minimum days of booking for this month is ')); ?>'+carrental_minimum_booking_time[key]);
						}
					}
				}
				
				// Filters
				var flt = [];

				// Price range
				if (jQuery('#carrental_filter_price_range' + element_id).is(':hidden') == false) {
					flt.push('spr:' + parseInt(jQuery('#carrental_filter_price_range' + element_id + ' .inputSliderMin').val()));
					flt.push('epr:' + parseInt(jQuery('#carrental_filter_price_range' + element_id + ' .inputSliderMax').val()));
				}

				// Extras
				if (jQuery('#carrental_filter_extras' + element_id).is(':hidden') == false) {
					if (jQuery('[name=ac]').is(':checked') == true) {
						flt.push('ac:' + parseInt(jQuery('[name=ac]').val()));
					}
					if (jQuery('[name=nonac]').is(':checked') == true) {
						flt.push('nac:' + parseInt(jQuery('[name=nonac]').val()));
					}
				}

				// Fuel
				if (jQuery('#carrental_filter_fuel' + element_id).is(':hidden') == false) {
					if (jQuery('[name=petrol]').is(':checked') == true) {
						flt.push('pl:' + parseInt(jQuery('[name=petrol]').val()));
					}
					if (jQuery('[name=diesel]').is(':checked') == true) {
						flt.push('dl:' + parseInt(jQuery('[name=diesel]').val()));
					}
				}

				// Passengers
				if (jQuery('#carrental_filter_passangers' + element_id).is(':hidden') == false) {
					flt.push('sp:' + parseInt(jQuery('#carrental_filter_passangers' + element_id + ' .slider-input-start').val()));
					flt.push('ep:' + parseInt(jQuery('#carrental_filter_passangers' + element_id + ' .slider-input-end').val()));
				}

				// Categories
				if (jQuery('#carrental_filter_categories' + element_id).is(':hidden') == false) {
					var cats = [];
					jQuery('.categories_checkall:checked').each(function() {
						cats.push(jQuery(this).val());
					});
					if (cats.length > 0) {
						flt.push('cats:' + cats.join(','));
					}
				}

				// Vehicles
				if (jQuery('#carrental_filter_vehicles' + element_id).is(':hidden') == false) {
					var cats = [];
					jQuery('.vehicles_checkall:checked').each(function() {
						cats.push(jQuery(this).val());
					});
					if (cats.length > 0) {
						flt.push('vh:' + cats.join(','));
					}
				}
				
				//seri = jQuery('.custom_parameter_checkbox:checked, .custom_parameter_input.slider-input-start, .custom_parameter_input.slider-input-end').serializeArray();
				
				// custom parameters
				if (jQuery('.custom-parameter-values').length) {
					jQuery.each(jQuery('.custom-parameter-values'), function (k, v){
						if (jQuery(v).is(':hidden')) {
							return;
						}
						checkbox = jQuery(v).find('.custom_parameter_checkbox:checked');
						var cats = [];
						parameter_id = 0;
						checkbox.each(function(kk, vv){
							cats.push(parseInt(jQuery(vv).val()));
							parameter_id = parseInt(jQuery(vv).attr('data-parameter'));
						});
						flt.push('cp-'+parameter_id+':' + cats.join(','));
					});
				}
				
				if (jQuery('.custom-parameter-range').length) {
					jQuery.each(jQuery('.custom-parameter-range'), function (k, v){
						if (jQuery(v).is(':hidden')) {
							return;
						}
						flt.push('cp-'+jQuery(v).find('.slider-input-start').attr('data-parameter')+'-range:' + parseInt(jQuery(v).find('.slider-input-start').val()) + '-' +parseInt(jQuery(v).find('.slider-input-end').val()));
						//flt.push('cp-'+jQuery(v).find('.slider-input-end').attr('data-parameter')+'-t:' + parseInt(jQuery(v).find('.slider-input-end').val()));
					});
				}
				
				if (flt.length > 0) {
					jQuery('[name=flt]').val(flt.join('|'));
				} else {
					jQuery('[name=flt]').val('');
				}

				if (errors.length == 0) {
					return true;
				} else {
					jQuery('#carrental_book_errors' + element_id).html('<li>' + errors.join('</li><li>') + '</li>');
					return false;
				}
			});
		}

		function carrental_customRange(input) {
			<?php
			$days = (int)get_option('carrental_min_before_days');
			$days_max = (int)get_option('carrental_max_before_days');
			if ($days_max < 1) {
				$days_max = 999;
			}
			?>
			var min_time = new Date(<?php echo strtotime('+'.$days.' day') * 1000;?>);
			var max_time = new Date(<?php echo strtotime('+'.$days_max.' day') * 1000;?>);
			
			if (input.id.substr(0, 17) == 'carrental_to_date') {
				var postfix = '';
				if (input.id.length > 17) {
					postfix = input.id.substr(17);
				}
				
				if (jQuery('#carrental_from_date' + postfix).val() == '') {
					alert('<?php echo addslashes(CarRental::t('Set pick-up date first.'));?>');					
				}
				
				var minDate = jQuery.datepicker.parseDate('<?php echo date_format_js(isset($theme_options['date_format']) ? $theme_options['date_format'] : ''); ?>', jQuery('#carrental_from_date' + postfix).val()); //new Date(jQuery.datepicker.formatDate('yy-mm-dd', );jQuery('#carrental_from_date').val());
				minDate.setDate(minDate.getDate());
				
				enterDate = jQuery( "#carrental_from_date"+postfix ).datepicker( "getDate" );
				key = parseInt(enterDate.getMonth())+1;
				if (carrental_minimum_booking_time[key] !== undefined && carrental_minimum_booking_time[key] > 0) {
					minTime = enterDate;
					minTime.setDate(minTime.getDate() + carrental_minimum_booking_time[key]);
					if (minTime > minDate) {
						minDate = minTime;
					}
				}
				
				return {minDate: minDate};
			}
			return {minDate: min_time, maxDate: max_time}
		}

		function carrental_update_business_hours(element_id) {
			try {

				var id_branch = jQuery('#carrental_enter_location' + element_id).val();
				var id_branch_return = jQuery('#carrental_return_location' + element_id).val();

				if (typeof id_branch_return === "undefined" || id_branch_return == '' || jQuery('#carrental_different_loc' + element_id + ':checked').val() != 'on') {
					id_branch_return = id_branch;
				}
				
				var date_from = jQuery('#carrental_from_date' + element_id).val();
				if (typeof date_from === "undefined" || date_from == '') {
					date_from = jQuery.datepicker.formatDate('<?php echo date_format_js(isset($theme_options['date_format']) ? $theme_options['date_format'] : ''); ?>', new Date());
				}

				var date_to = jQuery('#carrental_to_date' + element_id).val();
				if (typeof date_to === "undefined" || date_to == '') {
					date_to = jQuery.datepicker.formatDate('<?php echo date_format_js(isset($theme_options['date_format']) ? $theme_options['date_format'] : ''); ?>', new Date());
				}

				// reformat to YYYY-MM-DD
				date_from = jQuery.datepicker.formatDate('yy-mm-dd', jQuery.datepicker.parseDate('<?php echo date_format_js(isset($theme_options['date_format']) ? $theme_options['date_format'] : ''); ?>', date_from));
				date_to = jQuery.datepicker.formatDate('yy-mm-dd', jQuery.datepicker.parseDate('<?php echo date_format_js(isset($theme_options['date_format']) ? $theme_options['date_format'] : ''); ?>', date_to));

				var full_date_from = new Date(date_from);
				var day_week_from = full_date_from.getUTCDay();
				if (day_week_from == 0) {
					day_week_from = 7;
				} // sunday

				var full_date_to = new Date(date_to);
				var day_week_to = full_date_to.getUTCDay();
				if (day_week_to == 0) {
					day_week_to = 7;
				} // sunday
				
				if (id_branch != '' && typeof branches[id_branch]['specific_times'] !== 'undefined' && branches[id_branch]['specific_times']) {
					
					// DATE FROM
					if (typeof branches[id_branch]['enter_hours'][day_week_from] !== "undefined" && branches[id_branch]['enter_hours'][day_week_from]) {

						var from = carrental_time_format(branches[id_branch]['enter_hours'][day_week_from]['from'], 24);//.substring(0, 5); // get off seconds
						var to = carrental_time_format(branches[id_branch]['enter_hours'][day_week_from]['to'], 24);//.substring(0, 5);
						var prev_val = jQuery("#carrental_from_hour" + element_id).val();

						jQuery("#carrental_from_hour" + element_id).attr('disabled', false);
						jQuery('#carrental_from_hour' + element_id).find('option').remove().end().append('<option value=""><?= CarRental::t('Time') ?></option>'); // delete all previous options

						for (x = parseInt(from); x <= parseInt(to); x++) {
							var hour = String(x);
							if (hour.length == 1) {
								hour = '0' + hour;
							}

							if (x == parseInt(from) && parseInt(from.substr(-2)) >= 30) {
								// Do not show
							} else {
								newTime = carrental_time_format(hour + ':00', carrental_timeformat);
								jQuery("#carrental_from_hour" + element_id).append(new Option(newTime, hour + ':00'));
							}

							if (x == parseInt(to) && parseInt(to.substr(-2)) < 30) {
								// Do not show
							} else {
								newTime = carrental_time_format(hour + ':30', carrental_timeformat);
								jQuery("#carrental_from_hour" + element_id).append(new Option(newTime, hour + ':30'));
							}
						}
						
						// FROM_2
						if (branches[id_branch]['enter_hours'][day_week_from]['from_2'] && branches[id_branch]['enter_hours'][day_week_from]['to_2']) {
							var from = carrental_time_format(branches[id_branch]['enter_hours'][day_week_from]['from_2'], 24);//.substring(0, 5); // get off seconds
							var to = carrental_time_format(branches[id_branch]['enter_hours'][day_week_from]['to_2'], 24);//.substring(0, 5);
							for (x = parseInt(from); x <= parseInt(to); x++) {
								var hour = String(x);
								if (hour.length == 1) {
									hour = '0' + hour;
								}

								if (x == parseInt(from) && parseInt(from.substr(-2)) >= 30) {
									// Do not show
								} else {
									newTime = carrental_time_format(hour + ':00', carrental_timeformat);
									jQuery("#carrental_from_hour" + element_id).append(new Option(newTime, hour + ':00'));
								}

								if (x == parseInt(to) && parseInt(to.substr(-2)) < 30) {
									// Do not show
								} else {
									newTime = carrental_time_format(hour + ':30', carrental_timeformat);
									jQuery("#carrental_from_hour" + element_id).append(new Option(newTime, hour + ':30'));
								}
							}
						}

						if (prev_val != '' && jQuery("#carrental_from_hour" + element_id + " option[value='" + prev_val + "']").val() !== undefined) {
							jQuery("#carrental_from_hour" + element_id).val(prev_val);
						}

					} else {
						jQuery('#carrental_from_hour' + element_id).find('option').remove().end().append('<option value=""><?= CarRental::t('Closed') ?></option>'); // delete all previous options
						jQuery("#carrental_from_hour" + element_id).attr('disabled', true);
					}
				
				} else {
					// DATE FROM
						if (id_branch != '' && typeof branch_hours[id_branch][day_week_from] !== "undefined" && branch_hours[id_branch][day_week_from]) {

							var from = carrental_time_format(branch_hours[id_branch][day_week_from]['from'], 24);//.substring(0, 5); // get off seconds
							var to = carrental_time_format(branch_hours[id_branch][day_week_from]['to'], 24);//.substring(0, 5);
							var prev_val = jQuery("#carrental_from_hour" + element_id).val();

							jQuery("#carrental_from_hour" + element_id).attr('disabled', false);
							jQuery('#carrental_from_hour' + element_id).find('option').remove().end().append('<option value=""><?= CarRental::t('Time') ?></option>'); // delete all previous options

							for (x = parseInt(from); x <= parseInt(to); x++) {
								var hour = String(x);
								if (hour.length == 1) {
									hour = '0' + hour;
								}

								if (x == parseInt(from) && parseInt(from.substr(-2)) >= 30) {
									// Do not show
								} else {
									newTime = carrental_time_format(hour + ':00', carrental_timeformat);
									jQuery("#carrental_from_hour" + element_id).append(new Option(newTime, hour + ':00'));
								}

								if (x == parseInt(to) && parseInt(to.substr(-2)) < 30) {
									// Do not show
								} else {
									newTime = carrental_time_format(hour + ':30', carrental_timeformat);
									jQuery("#carrental_from_hour" + element_id).append(new Option(newTime, hour + ':30'));
								}
							}
							
							if (branch_hours[id_branch][day_week_from]['from_2'] && branch_hours[id_branch][day_week_from]['to_2']) {
								var from = carrental_time_format(branch_hours[id_branch][day_week_from]['from_2'], 24);//.substring(0, 5); // get off seconds
								var to = carrental_time_format(branch_hours[id_branch][day_week_from]['to_2'], 24);//.substring(0, 5);

								for (x = parseInt(from); x <= parseInt(to); x++) {
									var hour = String(x);
									if (hour.length == 1) {
										hour = '0' + hour;
									}

									if (x == parseInt(from) && parseInt(from.substr(-2)) >= 30) {
										// Do not show
									} else {
										newTime = carrental_time_format(hour + ':00', carrental_timeformat);
										jQuery("#carrental_from_hour" + element_id).append(new Option(newTime, hour + ':00'));
									}

									if (x == parseInt(to) && parseInt(to.substr(-2)) < 30) {
										// Do not show
									} else {
										newTime = carrental_time_format(hour + ':30', carrental_timeformat);
										jQuery("#carrental_from_hour" + element_id).append(new Option(newTime, hour + ':30'));
									}
								}
							}

							if (prev_val != '' && jQuery("#carrental_from_hour" + element_id + " option[value='" + prev_val + "']").val() !== undefined) {
								jQuery("#carrental_from_hour" + element_id).val(prev_val);
							}

						} else {
							jQuery('#carrental_from_hour' + element_id).find('option').remove().end().append('<option value=""><?= CarRental::t('Closed') ?></option>'); // delete all previous options
							jQuery("#carrental_from_hour" + element_id).attr('disabled', true);
						}
					}
					
					if (id_branch_return != '' && typeof branches[id_branch_return]['specific_times'] !== 'undefined' && branches[id_branch_return]['specific_times']) {
						// DATE TO
						if (typeof branches[id_branch_return]['return_hours'][day_week_to] !== "undefined" && branches[id_branch_return]['return_hours'][day_week_to]) {

							var from = carrental_time_format(branches[id_branch_return]['return_hours'][day_week_to]['from'], 24);//.substring(0, 5); // get off seconds
							var to = carrental_time_format(branches[id_branch_return]['return_hours'][day_week_to]['to'], 24);//.substring(0, 5);
							var prev_val = jQuery("#carrental_to_hour" + element_id).val();

							jQuery("#carrental_to_hour" + element_id).attr('disabled', false);
							jQuery('#carrental_to_hour' + element_id).find('option').remove().end().append('<option value=""><?= CarRental::t('Time') ?></option>'); // delete all previous options

							for (x = parseInt(from); x <= parseInt(to); x++) {
								var hour = String(x);
								if (hour.length == 1) {
									hour = '0' + hour;
								}

								if (x == parseInt(from) && parseInt(from.substr(-2)) >= 30) {
									// Do not show
								} else {
									newTime = carrental_time_format(hour + ':00', carrental_timeformat);
									jQuery("#carrental_to_hour" + element_id).append(new Option(newTime, hour + ':00'));
								}

								if (x == parseInt(to) && parseInt(to.substr(-2)) < 30) {
									// Do not show
								} else {
									//if (prev_val == hour + ':30') { var selected = true; } else { var selected = false; }
									newTime = carrental_time_format(hour + ':30', carrental_timeformat);
									jQuery("#carrental_to_hour" + element_id).append(new Option(newTime, hour + ':30'));
								}

							}
							
							// FROM_2
							if (branches[id_branch_return]['return_hours'][day_week_to]['from_2'] && branches[id_branch_return]['return_hours'][day_week_to]['to_2']) {
								var from = carrental_time_format(branches[id_branch_return]['return_hours'][day_week_to]['from_2'], 24);//.substring(0, 5); // get off seconds
								var to = carrental_time_format(branches[id_branch_return]['return_hours'][day_week_to]['to_2'], 24);//.substring(0, 5);

								for (x = parseInt(from); x <= parseInt(to); x++) {
									var hour = String(x);
									if (hour.length == 1) {
										hour = '0' + hour;
									}

									if (x == parseInt(from) && parseInt(from.substr(-2)) >= 30) {
										// Do not show
									} else {
										newTime = carrental_time_format(hour + ':00', carrental_timeformat);
										jQuery("#carrental_to_hour" + element_id).append(new Option(newTime, hour + ':00'));
									}

									if (x == parseInt(to) && parseInt(to.substr(-2)) < 30) {
										// Do not show
									} else {
										//if (prev_val == hour + ':30') { var selected = true; } else { var selected = false; }
										newTime = carrental_time_format(hour + ':30', carrental_timeformat);
										jQuery("#carrental_to_hour" + element_id).append(new Option(newTime, hour + ':30'));
									}

								}
							}

							if (prev_val != '' && jQuery("#carrental_to_hour" + element_id + " option[value='" + prev_val + "']").val() !== undefined) {
								jQuery("#carrental_to_hour" + element_id).val(prev_val);
							}

						} else {
							jQuery('#carrental_to_hour' + element_id).find('option').remove().end().append('<option value=""><?= CarRental::t('Closed') ?></option>');
							jQuery("#carrental_to_hour" + element_id).attr('disabled', true);
						}
					} else {
						// DATE TO
						if (id_branch_return != '' && typeof branch_hours[id_branch_return][day_week_to] !== "undefined" && branch_hours[id_branch_return][day_week_to]) {

							var from = carrental_time_format(branch_hours[id_branch_return][day_week_to]['from'], 24);//.substring(0, 5); // get off seconds
							var to = carrental_time_format(branch_hours[id_branch_return][day_week_to]['to'], 24);//.substring(0, 5);
							var prev_val = jQuery("#carrental_to_hour" + element_id).val();

							jQuery("#carrental_to_hour" + element_id).attr('disabled', false);
							jQuery('#carrental_to_hour' + element_id).find('option').remove().end().append('<option value=""><?= CarRental::t('Time') ?></option>'); // delete all previous options

							for (x = parseInt(from); x <= parseInt(to); x++) {
								var hour = String(x);
								if (hour.length == 1) {
									hour = '0' + hour;
								}

								if (x == parseInt(from) && parseInt(from.substr(-2)) >= 30) {
									// Do not show
								} else {
									newTime = carrental_time_format(hour + ':00', carrental_timeformat);
									jQuery("#carrental_to_hour" + element_id).append(new Option(newTime, hour + ':00'));
								}

								if (x == parseInt(to) && parseInt(to.substr(-2)) < 30) {
									// Do not show
								} else {
									//if (prev_val == hour + ':30') { var selected = true; } else { var selected = false; }
									newTime = carrental_time_format(hour + ':30', carrental_timeformat);
									jQuery("#carrental_to_hour" + element_id).append(new Option(newTime, hour + ':30'));
								}

							}
							
							if (branch_hours[id_branch_return][day_week_to]['from_2'] && branch_hours[id_branch_return][day_week_to]['to_2']) {
								var from = carrental_time_format(branch_hours[id_branch_return][day_week_to]['from_2'], 24);//.substring(0, 5); // get off seconds
								var to = carrental_time_format(branch_hours[id_branch_return][day_week_to]['to_2'], 24);//.substring(0, 5);

								for (x = parseInt(from); x <= parseInt(to); x++) {
									var hour = String(x);
									if (hour.length == 1) {
										hour = '0' + hour;
									}

									if (x == parseInt(from) && parseInt(from.substr(-2)) >= 30) {
										// Do not show
									} else {
										newTime = carrental_time_format(hour + ':00', carrental_timeformat);
										jQuery("#carrental_to_hour" + element_id).append(new Option(newTime, hour + ':00'));
									}

									if (x == parseInt(to) && parseInt(to.substr(-2)) < 30) {
										// Do not show
									} else {
										//if (prev_val == hour + ':30') { var selected = true; } else { var selected = false; }
										newTime = carrental_time_format(hour + ':30', carrental_timeformat);
										jQuery("#carrental_to_hour" + element_id).append(new Option(newTime, hour + ':30'));
									}

								}
							}

							if (prev_val != '' && jQuery("#carrental_to_hour" + element_id + " option[value='" + prev_val + "']").val() !== undefined) {
								jQuery("#carrental_to_hour" + element_id).val(prev_val);
							}

						} else {
							jQuery('#carrental_to_hour' + element_id).find('option').remove().end().append('<option value=""><?= CarRental::t('Closed') ?></option>');
							jQuery("#carrental_to_hour" + element_id).attr('disabled', true);
						}

					}
				
			} catch (e) {
				alert(e);
			}
		}

	});


</script>