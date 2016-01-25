<?php
/**
 * Car detail
 *
 * @package WordPress
 * @subpackage CarRental
 * @since CarRental 3.0.0
 */
get_header();
?>

<section class="intro">	
	<div>
		<?php $theme_options = unserialize(get_option('carrental_theme_options')); ?>
		<div class="slideshow-item static"<?php if (isset($theme_options['picture_otherpages']) && !empty($theme_options['picture_otherpages'])) { ?> style="background-image:url('<?= htmlspecialchars($theme_options['picture_otherpages']) ?>');"<?php } ?>>
		</div>
	</div>
</section>

<hr>

<section class="content carrental-detail">
	<div class="container">


		<div class="columns-2 break-md aside-on-left">
			<div class="column column-fixed">
				<div class="box box-clean">

					<div class="box-title mobile-toggle mobile-toggle-md" data-target="modify-search">
						<?= CarRental::t('Modify reservation') ?>
					</div>
					<!-- .box-title -->

					<div data-id="modify-search" class="box-inner-small box-border-bottom md-hidden">			

						<form action="" method="get" class="form form-vertical form-size-100" id="carrental_booking_form">

							<fieldset>

								<div class="control-group">
									<div class="control-field">
										<select name="el" id="carrental_enter_location" class="size-90">
											<option value=""><?= CarRental::t('Enter Location') ?></option>
											<?php if (isset($locations) && !empty($locations)) { ?>
												<?php $locations_no = count($locations); ?>
												<?php foreach ($locations as $key => $val) { ?>
													<option value="<?= $val->id_branch ?>" <?php if ((isset($_GET['el']) && (int) $_GET['el'] == $val->id_branch) || $locations_no == 1) { ?>selected<?php } ?>><?= $val->name ?></option>
												<?php } ?>	
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="control-group">
									<div class="control-field">
										<label><input name="dl" id="carrental_different_loc" type="checkbox" <?php if (isset($_GET['dl']) && $_GET['dl'] == 'on') { ?>checked<?php } ?>>&nbsp;&nbsp;<?= CarRental::t('Returning to Different location.') ?></label>
									</div>
								</div>

								<div class="control-group">
									<div class="control-field">
										<select name="rl" id="carrental_return_location" class="size-90">
											<option value=""><?= CarRental::t('Return Location') ?></option>
											<?php if (isset($locations) && !empty($locations)) { ?>
												<?php $locations_no = count($locations); ?>
												<?php foreach ($locations as $key => $val) { ?>
													<option value="<?= $val->id_branch ?>" <?php if ((isset($_GET['rl']) && (int) $_GET['rl'] == $val->id_branch) || $locations_no == 1) { ?>selected<?php } ?>><?= $val->name ?></option>
												<?php } ?>	
											<?php } ?>
										</select>
									</div>
								</div>

								<div class="columns-2 control-group">
									<div class="column column-wide" style="width:60.5%">
										<div class="control-group">
											<div class="control-field">
												<span class="control-addon">
													<input type="text" class="control-input" name="fd" id="carrental_from_date" placeholder="<?= CarRental::t('Pick-up date') ?>" <?php if (isset($_GET['fd'])) { ?>value="<?= htmlspecialchars($_GET['fd']) ?>"<?php } ?>>
													<span class="control-addon-item">
														<span class="sprite-calendar"></span>
													</span>
												</span>
											</div>
										</div>
									</div>

									<div class="column column-thin" style="width:39.5%">
										<div class="control-group">
											<div class="control-field">
												<span class="control-addon">
													<select name="fh" id="carrental_from_hour" style="width: 85%; padding:2px 9px; -webkit-border-radius: 4px; border-radius: 4px; font-size: 12px; ">
														<option value=""><?= CarRental::t('Time') ?></option>
														<?php for ($x = 0; $x <= 23; $x++) { ?>
															<option value="<?= str_pad($x, 2, '0', STR_PAD_LEFT) ?>:00" <?php if (isset($_GET['fh']) && $_GET['fh'] == str_pad($x, 2, '0', STR_PAD_LEFT) . ':00') { ?>selected<?php } ?>><?= carrental_time_format(str_pad($x, 2, '0', STR_PAD_LEFT) . ':00', (isset($theme_options) && isset($theme_options['time_format']) ? $theme_options['time_format'] : 24)); ?></option>
															<option value="<?= str_pad($x, 2, '0', STR_PAD_LEFT) ?>:30" <?php if (isset($_GET['fh']) && $_GET['fh'] == str_pad($x, 2, '0', STR_PAD_LEFT) . ':30') { ?>selected<?php } ?>><?= carrental_time_format(str_pad($x, 2, '0', STR_PAD_LEFT) . ':30', (isset($theme_options) && isset($theme_options['time_format']) ? $theme_options['time_format'] : 24)); ?></option>
														<?php } ?>
													</select>

													<span class="control-addon-item" style="right:-8px;">
														<span class="sprite-time"></span>
													</span>
												</span>	

											</div>
										</div>
									</div>
								</div>
								<!-- .columns-2 -->

								<div class="columns-2 control-group">
									<div class="column column-wide" style="width:60.5%">
										<div class="control-group">
											<div class="control-field">
												<span class="control-addon">
													<input type="text" class="control-input" name="td" id="carrental_to_date" placeholder="<?= CarRental::t('Return date') ?>" <?php if (isset($_GET['td'])) { ?>value="<?= htmlspecialchars($_GET['td']) ?>"<?php } ?>>
													<span class="control-addon-item">
														<span class="sprite-calendar"></span>
													</span>
												</span>
											</div>
										</div>
									</div>

									<div class="column column-thin" style="width:39.5%">
										<div class="control-group">
											<div class="control-field">
												<span class="control-addon">
													<select name="th" id="carrental_to_hour" style="width: 85%; padding:2px 9px; -webkit-border-radius: 4px; border-radius: 4px; font-size: 12px; ">
														<option value=""><?= CarRental::t('Time') ?></option>
														<?php for ($x = 0; $x <= 23; $x++) { ?>
															<option value="<?= str_pad($x, 2, '0', STR_PAD_LEFT) ?>:00" <?php if (isset($_GET['th']) && $_GET['th'] == str_pad($x, 2, '0', STR_PAD_LEFT) . ':00') { ?>selected<?php } ?>><?= carrental_time_format(str_pad($x, 2, '0', STR_PAD_LEFT) . ':00', (isset($theme_options) && isset($theme_options['time_format']) ? $theme_options['time_format'] : 24)); ?></option>
															<option value="<?= str_pad($x, 2, '0', STR_PAD_LEFT) ?>:30" <?php if (isset($_GET['th']) && $_GET['th'] == str_pad($x, 2, '0', STR_PAD_LEFT) . ':30') { ?>selected<?php } ?>><?= carrental_time_format(str_pad($x, 2, '0', STR_PAD_LEFT) . ':30', (isset($theme_options) && isset($theme_options['time_format']) ? $theme_options['time_format'] : 24)); ?></option>
														<?php } ?>
													</select>
													<span class="control-addon-item" style="right:-8px;">
														<span class="sprite-time"></span>
													</span>
												</span>	

											</div>
										</div>
									</div>
								</div>

								<div class="control-group">
									<div class="control-field">
										<input type="hidden" name="page" value="carrental">
										<input type="hidden" name="order" value="name" id="carrental_order_input">
										<input type="hidden" name="book_now" value="ok">
										<input type="hidden" name="id_car" value="<?= (int) $_GET['id_car'] ?>">
										<input type="hidden" name="promo" value="<?php if (isset($_GET['promo'])) { ?><?= htmlspecialchars($_GET['promo']) ?><?php } ?>" id="carrental_promocode">
										<input type="submit" name="search" value="<?= CarRental::t('MODIFY') ?>" id="carrental_book_now" class="btn btn-primary btn-block">
									</div>
									<!-- .control-field -->
								</div>
								<!-- .control-group -->

							</fieldset>

							<ul id="carrental_book_errors" style="margin:1em 2em;list-style-type:circle;color:tomato;"></ul>
						</form>
					</div>

					<?php include(get_file_template_path('booking-javascript.php')); ?>

					<?php if (isset($theme_options['phone_number']) && !empty($theme_options['phone_number'])) { ?>
						<div class="box-inner-small">
							<div class="invert-columns-2 init-md">
								<div class="column">
									<div class="box box-inner-small box-contact box-contact-small">
										<div class="h2" style="text-align:center;margin-bottom:15px;">
											<?= CarRental::t('Make a reservation by phone') ?><br>
										</div>
										<div class="h2" style="font-size: 1.75em;margin:0;">
											<strong><?= $theme_options['phone_number'] ?></strong>
										</div>
										<span class="sprite-call-us-small"></span>
									</div>
								</div>
							</div>
						</div>
					<?php } ?>

				</div>
				<!-- .box -->

			</div>
			<!-- .column -->

			<div class="column column-fluid">


				<fieldset>

					<div class="bordered-content">

						<?php if (isset($vehicle) && !empty($vehicle)) { ?>
							<h2 class="detail_name"><?= $vehicle->name ?></h2>
							<div class="list-item-media">
								<?php if (!empty($vehicle->picture)) { ?>
									<img src="<?= $vehicle->picture ?>" alt="<?= $vehicle->name ?>" class="main_image">
										<?php $additional_pictures_count = 0; ?>
										<?php if (isset($vehicle->additional_pictures) && !empty($vehicle->additional_pictures)) { ?>
											<?php $vehicle->additional_pictures = unserialize($vehicle->additional_pictures); ?>
											<?php if (is_array($vehicle->additional_pictures) && count($vehicle->additional_pictures) > 0) { ?>
												<?php $additional_pictures_count = count($vehicle->additional_pictures); ?>
											<?php } ?>
										<?php } ?>
										<?php if ($additional_pictures_count > 0) { ?>
											<a href="<?= $vehicle->picture ?>" data-lightbox="fleet-<?= $vehicle->id_fleet ?>" class="btn btn-small btn-primary btn-book btn-absolute"><?= CarRental::t('Show more pictures') ?> <strong>(<?php echo $additional_pictures_count; ?>)</strong></a>
										<?php } ?>
									
									<div class="hid-imgs">
										<?php if ($additional_pictures_count > 0) { ?>
											<?php foreach ($vehicle->additional_pictures as $adPicture) { ?>
												<a href="<?= $adPicture ?>" data-lightbox="fleet-<?= $vehicle->id_fleet ?>"></a>
											<?php } ?>
										<?php } ?>
									</div>
								<?php } ?>

							</div>
							<div class="box box-white box-inner">

								<div class="columns-2 break-lg">

									<div class="column">
										<?php $distance_metric = get_option('carrental_distance_metric'); ?>

										<div class="column">
											<div class="icon-text-list">
												<?php if (isset($vehicle->ac) && (int) $vehicle->ac > 0) { ?>
													<?php if ($vehicle->ac == 2) { ?><?= CarRental::t('No A/C') ?><?php } else { ?><?= CarRental::t('A/C') ?><?php } ?><br />
												<?php } ?>
												<?php if (isset($vehicle->luggage) && !empty($vehicle->luggage)) { ?>
													<?= $vehicle->luggage ?>&times; <?= CarRental::t('Luggage Quantity') ?><br />
												<?php } ?>
												<?php if (isset($vehicle->seats) && !empty($vehicle->seats)) { ?>
													<?= $vehicle->seats ?>&times; <?= CarRental::t('Persons') ?><br />
												<?php } ?>
												<?php if (isset($vehicle->fuel) && !empty($vehicle->fuel)) { ?>
													<?php if ($vehicle->fuel == 1) { ?><?= CarRental::t('Petrol') ?><?php } else { ?><?= CarRental::t('Diesel') ?><?php } ?><br />
												<?php } ?>
												<?php if (isset($vehicle->consumption) && !empty($vehicle->consumption)) { ?>
													<?php $consumption = get_option('carrental_consumption'); ?>
													<?php
													if (!$consumption || empty($consumption)) {
														$consumption = 'eu';
													}
													?>
													<abbr title="<?= CarRental::t('Average Consumption') ?>"><?= $vehicle->consumption ?> <?= (($consumption == 'eu') ? 'l/100km' : 'MPG') ?></abbr><br />
												<?php } ?>

												<?php if (isset($vehicle->transmission) && !empty($vehicle->transmission)) { ?>
													<?= (($vehicle->transmission == 1) ? CarRental::t('Transmission: Automatic') : CarRental::t('Transmission: Manual')) ?><br />
												<?php } ?>
												<?php if (isset($vehicle->free_distance)) { ?>
													<?= CarRental::t('Free distance') ?>: <?php if ($vehicle->free_distance > 0) { ?><?= $vehicle->free_distance ?>&nbsp;<?= $distance_metric ?><?php } else { ?><?= CarRental::t('Unlimited') ?><?php } ?><br />
												<?php } ?>
												<?php if (isset($vehicle->deposit) && $vehicle->deposit != '' && $vehicle->deposit > 0) { ?>
													<?php
													$global_currency = get_option('carrental_global_currency');
													$av_currencies = unserialize(get_option('carrental_available_currencies'));
													$rate = 1;
													if (isset($_SESSION['carrental_currency']) && !empty($_SESSION['carrental_currency']) && isset($av_currencies[$_SESSION['carrental_currency']])) {
														$current_currency = $_SESSION['carrental_currency'];
													} else {
														$current_currency = $global_currency;
													}
													?>
													<?php
													if ($current_currency != $global_currency && isset($av_currencies[$current_currency])) {
														$rate = $av_currencies[$current_currency];
													}
													?>
													<?= CarRental::t('Deposit') ?>: <?php if ($vehicle->deposit > 0) { ?><?= CarRental::get_currency_symbol('before', $current_currency); ?><?= round(($vehicle->deposit / $rate), 2) ?>&nbsp;<?= CarRental::get_currency_symbol('after', $current_currency); ?><?php } else { ?>0<?php } ?><br />
												<?php } ?>

												<?php $additional_parameters = unserialize($vehicle->additional_parameters); ?>
												<?php $lang = ((isset($_SESSION['carrental_language']) && !empty($_SESSION['carrental_language'])) ? $_SESSION['carrental_language'] : 'en_GB'); ?>
												<?php $lang = strtolower(end(explode('_', $lang))); ?>
												<?php if ($additional_parameters && isset($additional_parameters[$lang]) && !empty($additional_parameters[$lang])) { ?>
													<?php foreach ($additional_parameters[$lang] as $p) { ?>
														<?php
														if (!isset($p['name']) || trim($p['name']) == '') {
															continue;
														}
														?>
														<?php if (trim($p['value']) == '') { ?>
															<?php echo $p['name']; ?><br />
														<?php } else { ?>
															<strong><?php echo $p['name']; ?>:</strong> <span><?php echo $p['value']; ?></span><br />
														<?php } ?>
													<?php } ?>
												<?php } ?>

												<?php foreach ($fleet_parameters_values as $param) { ?>
													<?php echo CarRental::return_parameter_value($param->fleet_parameters_id, $param->value, '', '<br>'); ?>
												<?php } ?>

											</div>
										</div>
									</div>
									<!-- .column -->	


									<div class="column">
										<div class="box box-darken box-inner">
											<h3><?= CarRental::t('Rates') ?></h3>
											<?php
											if ($ranges && !empty($ranges)) {
												$set_type = 0;
												foreach ($ranges as $key => $val) {

													if ($set_type != $val->type) {

														echo '<h4 class="mt">' . (($val->type == 1) ? CarRental::t('Days range') : CarRental::t('Hours range')) . '</h4>';
														$set_type = $val->type;
													}

													echo '<div>';
													echo '<span class="range-days">' . $val->no_from . ' - ' . ((int)$val->no_to == 0 ? '&infin;' : $val->no_to) . ' ' . (($val->type == 1) ? CarRental::t('days') : CarRental::t('hours')) . ': </span>';
													echo '<span class="range-price"><strong>' . $val->price . '&nbsp;' . $val->currency . '</strong> ' . (($val->type == 1) ? '/ ' . CarRental::t('day') : '/ ' . CarRental::t('hour')) . '</span>';
													echo '</div>';
												}
											} else {
												echo '<h4 class="mt">' . CarRental::t('Day or hour ranges are not set.') . '</h4>';
											}
											?>

											<a href="javascript:void(0);" class="btn btn-small btn-primary btn-book carrental-book-this-car-btn bookcar rates-book-now" data-branch-id="<?= $vehicle->id_branch; ?>" data-car-id="<?= $vehicle->id_fleet ?>"><?= CarRental::t('Book This Car') ?></a>
										</div>
									</div>
								</div>
								<hr class="separate">
								<div class="h2 additional"><?= CarRental::t('Additional information') ?></div>
								<?php if (isset($vehicle->description) && !empty($vehicle->description)) { ?>
									<p>
										<?php $fleet_description = unserialize($vehicle->description); ?>
										<?php
										if ($fleet_description == false) {
											$fleet_description['gb'] = $vehicle->description;
										}
										?>
										<?php $lang = ((isset($_SESSION['carrental_language']) && !empty($_SESSION['carrental_language'])) ? $_SESSION['carrental_language'] : 'en_GB'); ?>
										<?php $lang = end(explode('_', $lang)); ?>
										<?= (isset($fleet_description[strtolower($lang)]) ? CarRental::removeslashes($fleet_description[strtolower($lang)]) : CarRental::removeslashes($fleet_description['gb'])) ?>
									</p>
								<?php } ?>
							<?php } else { ?>
								<p><?= CarRental::t('Sorry, we did not found the vehicle in the database.') ?></p>
							<?php } ?>

						</div>
						<!-- .bordered-content -->

				</fieldset>

			</div>
			<!-- .column -->

		</div>
		<!-- .columns-2 -->

	</div>
	<!-- .container -->

</section>
<!-- .content -->	

<div id="carrental-hidden-booking-form">
	<p class="close-win">×</p>
	<h3><?= CarRental::t('Book your car now') ?></h3>
	<?php $carrental_booking_form_id = '_popup'; ?>
	<?php include(get_file_template_path('booking-form.php')); ?>
</div>
<div class="booking-form-overflow"></div>

<?php get_footer(); ?>