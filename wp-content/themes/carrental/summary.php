<?php
/**
 * Choose car - filter
 *
 * @package WordPress
 * @subpackage CarRental
 * @since CarRental 3.0.0
 */
$disable_time = get_option('carrental_disable_time');
if ($disable_time == 'yes') {$disable_time = true;} else {$disable_time = false;}
get_header(); ?>
	
	<section class="intro">	
		<div>	
			<?php $theme_options = unserialize(get_option('carrental_theme_options')); ?>
			<div class="slideshow-item static"<?php if (isset($theme_options['picture_otherpages']) && !empty($theme_options['picture_otherpages'])) { ?> style="background-image:url('<?= htmlspecialchars($theme_options['picture_otherpages']) ?>');"<?php } ?>>
			</div>
		</div>
	</section>
	
	<hr>

	<section class="content">
		<div class="container">
			<ul class="steps columns-4 no-space">
				<li>
					<a href="javascript:void(0);" title="">
						<span class="steps-number">1</span> <?= CarRental::t('Create request') ?>
						<span class="sprite-arrow-right"></span>
					</a>
				</li>
				<li>
					<a href="javascript:void(0);" title="">
						<span class="steps-number">2</span> <?= CarRental::t('Choose a car') ?> 
						<span class="sprite-arrow-right"></span>
					</a>
				</li>
				<li>
					<a href="javascript:void(0);" title="">
						<span class="steps-number">3</span> <?= CarRental::t('Services &amp; book') ?>
						<span class="sprite-arrow-right"></span>
					</a>
				</li>
				<li class="active">
					<a href="javascript:void(0);" title="">
						<span class="steps-number">4</span> <?= CarRental::t('Summary') ?>
						<span class="sprite-arrow-right"></span>
					</a>
				</li>
			</ul>
		
		<?php if ($summary && !empty($summary)) { ?>
			
			<div class="column column-fluid">
								
						<div class="bordered-content">

							<div class="bordered-content-title"><?= CarRental::t('Summary') ?></div>
							
							<div class="box box-white box-inner">
								
								<div class="row row-message">
									<strong class="success"><?= CarRental::t('Thank you for booking with us!') ?></strong> <?= CarRental::t('Please find the below Details of your Order Summary') ?> #<strong class="success"><?= htmlspecialchars($summary['info']->id_order) ?></strong>
								</div>
								
								<?php $available_payments = unserialize(get_option('carrental_available_payments')); ?>
								<?php if (isset($available_payments) && !empty($available_payments)) { ?>
									<?php if (isset($available_payments['carrental-bank-account']) && !empty($available_payments['carrental-bank-account']) && $summary['info']->payment_option == 'bank') { ?>
										<div class="row row-message">
											<?php $msg_bank = CarRental::t('Please make your payment to <strong class="success">%bank</strong> with reference number <strong class="success">%ref</strong>.'); ?>
											<?php $msg_bank = str_replace('%bank', $available_payments['carrental-bank-account'], $msg_bank); ?>
											<?php $msg_bank = str_replace('%ref', htmlspecialchars($summary['info']->id_order), $msg_bank); ?>
											<?= $msg_bank ?>
										</div>
									<?php } ?>
								<?php } ?>
								
								<div class="columns-2 break-lg">
									
									<div class="column column-thiner">

										<h5><?= CarRental::t('Driver details') ?></h5>

										<div class="row row-boxed"><?= htmlspecialchars($summary['info']->first_name) ?> <?= htmlspecialchars($summary['info']->last_name) ?></div>
										<div class="row row-boxed"><?= htmlspecialchars($summary['info']->email) ?></div>
										<div class="row row-boxed"><?= htmlspecialchars($summary['info']->phone) ?></div>
										
										<?php if (!empty($summary['info']->street)) { ?>	
											<div class="row row-boxed"><?= htmlspecialchars($summary['info']->street) ?></div>
										<?php } ?>
										
										<?php if (!empty($summary['info']->city)) { ?>	
											<div class="row row-boxed">
												<?= htmlspecialchars($summary['info']->city) ?>,
												<?php if (!empty($summary['info']->zip)) { ?><?= htmlspecialchars($summary['info']->zip) ?>,<?php } ?>
												<?php if (!empty($summary['info']->country)) { ?><?= htmlspecialchars($summary['info']->country) ?><?php } ?>
											</div>
										<?php } ?>
										
										<?php if (!empty($summary['info']->company)) { ?>				
											<div class="row row-boxed"><?= htmlspecialchars($summary['info']->company) ?></div>
										<?php } ?>
										
										<?php if (!empty($summary['info']->vat)) { ?>				
											<div class="row row-boxed"><?= htmlspecialchars($summary['info']->vat) ?></div>
										<?php } ?>
										
										<?php if (!empty($summary['info']->flight)) { ?>				
											<div class="row row-boxed"><?= CarRental::t('Flight') ?>: <?= htmlspecialchars($summary['info']->flight) ?></div>
										<?php } ?>
										
										<?php if (!empty($summary['info']->license)) { ?>				
											<div class="row row-boxed"><?= CarRental::t('License') ?>: <?= htmlspecialchars($summary['info']->license) ?></div>
										<?php } ?>
										
										<?php if (!empty($summary['info']->id_card)) { ?>				
											<div class="row row-boxed"><?= CarRental::t('ID / Passport number') ?>: <?= htmlspecialchars($summary['info']->id_card) ?></div>
										<?php } ?>
										
										<?php if (isset($summary['drivers']) && !empty($summary['drivers'])) { ?>
										
											<?php foreach ($summary['drivers'] as $key => $val) { ?>
												<h5><?= ($key+1) ?>. <?= CarRental::t('Additional Driver details') ?></h5>
												<div class="row row-boxed"><?= htmlspecialchars($val->first_name) ?> <?= htmlspecialchars($val->last_name) ?></div>
												<div class="row row-boxed"><?= htmlspecialchars($val->email) ?></div>
												<div class="row row-boxed"><?= htmlspecialchars($val->phone) ?></div>
												<?php if (!empty($val->city)) { ?>	
													<div class="row row-boxed">
														<?= htmlspecialchars($val->city) ?>,
														<?php if (!empty($val->zip)) { ?><?= htmlspecialchars($val->zip) ?>,<?php } ?>
														<?php if (!empty($val->country)) { ?><?= htmlspecialchars($val->country) ?><?php } ?>
													</div>
												<?php } ?>
												
												<?php if (!empty($val->license)) { ?>				
													<div class="row row-boxed"><?= CarRental::t('License') ?>: <?= htmlspecialchars($val->license) ?></div>
												<?php } ?>
												
												<?php if (!empty($val->id_card)) { ?>				
													<div class="row row-boxed"><?= CarRental::t('ID / Passport number') ?>: <?= htmlspecialchars($val->id_card) ?></div>
												<?php } ?>
										
											<?php } ?>
											
										<?php } ?>
										
										<div class="carrental-summary-branch-info">
											<?php if (isset($locations[$summary['info']->id_enter_branch])) { ?>
												<div class="h4"><?= CarRental::t('Pick-up location address') ?>:</div>
									
												<address>
													<?php echo $locations[$summary['info']->id_enter_branch]->name;?><br>
													<?php $googleMap = ''; ?>
													<?php if (!empty($locations[$summary['info']->id_enter_branch]->street)) { echo $locations[$summary['info']->id_enter_branch]->street . '<br>'; $googleMap .= $locations[$summary['info']->id_enter_branch]->street . ', '; } ?>
													<?php if (!empty($locations[$summary['info']->id_enter_branch]->city)) { echo $locations[$summary['info']->id_enter_branch]->city . ', '; $googleMap .= $locations[$summary['info']->id_enter_branch]->city . ', '; } ?>
													<?php if (!empty($locations[$summary['info']->id_enter_branch]->zip)) { echo $locations[$summary['info']->id_enter_branch]->zip . '<br>'; $googleMap .= $locations[$summary['info']->id_enter_branch]->zip . ', '; } ?>
													<?php if (!empty($locations[$summary['info']->id_enter_branch]->country)) { echo $countries[$locations[$summary['info']->id_enter_branch]->country]; $googleMap .= $countries[$locations[$summary['info']->id_enter_branch]->country] . ', '; } ?>
													<?php if (!empty($locations[$summary['info']->id_enter_branch]->state)) { echo '&nbsp;' . $locations[$summary['info']->id_enter_branch]->state; } ?>
													<?php if (!empty($locations[$summary['info']->id_enter_branch]->phone)) { ?>
														<div class="column column-thin">
															<div class="h4"><?= CarRental::t('Phone') ?>:</div>
															<p><?= $locations[$summary['info']->id_enter_branch]->phone ?></p>
														</div>
													<?php } ?>

													<?php if (!empty($locations[$summary['info']->id_enter_branch]->email)) { ?>
														<div class="column column-wide">
															<div class="h4"><?= CarRental::t('E-Mail') ?>:</div>
															<p><a href="mailto:<?= $locations[$summary['info']->id_enter_branch]->email ?>"><?= $locations[$summary['info']->id_enter_branch]->email ?></a></p>
														</div>
													<?php } ?>
												</address>
												
												<div class="h4"><?= CarRental::t('Working Hours') ?>:</div>
									
												<div style="width:60%;">
												<?php if (!empty($locations[$summary['info']->id_enter_branch]->hours)) { ?>
													<?php foreach ($locations[$summary['info']->id_enter_branch]->hours as $kD => $vD) { ?>
														<dl class="dl-boxed">
															<dt><?= CarRental::get_day_name($vD->day, array(CarRental::t('Monday'), CarRental::t('Tuesday'), CarRental::t('Wednesday'), CarRental::t('Thursday'), CarRental::t('Friday'), CarRental::t('Saturday'), CarRental::t('Sunday'))) ?></dt>
															<dd><?= carrental_time_format(substr($vD->hours_from, 0, 5), (isset($theme_options) && isset($theme_options['time_format']) ? $theme_options['time_format'] : 24)) ?> - <?= carrental_time_format(substr($vD->hours_to, 0, 5), (isset($theme_options) && isset($theme_options['time_format']) ? $theme_options['time_format'] : 24)) ?>
															<?php if (isset($vD->hours_from_2) && $vD->hours_from_2 != '' && $vD->hours_from_2 != '00:00:00') { ?>
																<?php if (isset($vD->hours_to_2) && $vD->hours_to_2 != '' && $vD->hours_to_2 != '00:00:00') { ?>
																 | <?= carrental_time_format(substr($vD->hours_from_2, 0, 5), (isset($theme_options) && isset($theme_options['time_format']) ? $theme_options['time_format'] : 24)) ?> - <?= carrental_time_format(substr($vD->hours_to_2, 0, 5), (isset($theme_options) && isset($theme_options['time_format']) ? $theme_options['time_format'] : 24)) ?>
																<?php } ?>
															<?php } ?>
															</dd>
														</dl>
													<?php } ?>
												<?php } ?>
												</div>
											<?php } ?>
										</div>
										
									</div>
									<!-- .column -->

									<div class="column pull-right">
										<div class="summary-details">
											<div class="columns-2">
												<div class="column">

													<h5><?= CarRental::t('Pick Up') ?></h5>
													<p class="point-location"><?= $summary['info']->enter_loc ?></p>

													<div class="icon-text">
														<span class="sprite-calendar"></span><?= Date(date_format_php(isset($theme_options['date_format']) ? $theme_options['date_format'] : ''), strtotime($summary['info']->enter_date)) ?>
													</div>
													<!-- .icon-text -->
													<?php if (!$disable_time) { ?>
													<div class="icon-text">
														<span class="sprite-time"></span><?= carrental_time_format(Date('H:i', strtotime($summary['info']->enter_date)),(isset($theme_options) && isset($theme_options['time_format']) ? $theme_options['time_format'] : 24));?>
													</div>
													<!-- .icon-text -->
													<?php } ?>
													
												</div>
												<!-- .column -->

												<div class="column">
													
													<h5><?= CarRental::t('Drop Off') ?></h5>
													<p class="point-location"><?= $summary['info']->return_loc ?></p>

													<div class="icon-text">
														<span class="sprite-calendar"></span><?= Date(date_format_php(isset($theme_options['date_format']) ? $theme_options['date_format'] : ''), strtotime($summary['info']->return_date)) ?>
													</div>
													<?php if (!$disable_time) { ?>
													<div class="icon-text">
														<span class="sprite-time"></span><?= carrental_time_format(Date('H:i', strtotime($summary['info']->return_date)),(isset($theme_options) && isset($theme_options['time_format']) ? $theme_options['time_format'] : 24)); ?>
													</div>
													<?php } ?>

												</div>
											</div>

											<div class="columns-2">
												<div class="column">

													<h5><?= CarRental::t('Car Type') ?></h5>
													<p><img src="<?= $summary['info']->vehicle_picture ?>" alt="<?= $summary['info']->vehicle ?>" width="135"></p>
													<p class="weak"><?= $summary['info']->vehicle ?></p>
													
												</div>
												<!-- .column -->

												<div class="column">
													
													<h5><?= CarRental::t('Car Details') ?></h5>
													
													<?php if (isset($summary['info']->vehicle_ac) && (int) $summary['info']->vehicle_ac > 0) { ?>
														<div class="icon-text"><span class="sprite-snowflake"></span><?php if ($summary['info']->vehicle_ac == 2) { ?><?= CarRental::t('No A/C') ?><?php } else { ?><?= CarRental::t('A/C') ?><?php } ?></div>
													<?php } ?>
													<?php if (isset($summary['info']->vehicle_luggage) && !empty($summary['info']->vehicle_luggage)) { ?>
														<div class="icon-text"><span class="sprite-briefcase"></span><?= $summary['info']->vehicle_luggage ?>&times; <?= CarRental::t('Luggage Quantity') ?></div>
													<?php } ?>
													<?php if (isset($summary['info']->vehicle_seats) && !empty($summary['info']->vehicle_seats)) { ?>
														<div class="icon-text"><span class="sprite-person"></span><?= $summary['info']->vehicle_seats ?>&times; <?= CarRental::t('Persons') ?></div>
													<?php } ?>
													<?php if (isset($summary['info']->vehicle_fuel) && !empty($summary['info']->vehicle_fuel)) { ?>
														<div class="icon-text"><span class="sprite-fuel"></span><?php if ($summary['info']->vehicle_fuel == 1) { ?><?= CarRental::t('Petrol') ?><?php } else { ?><?= CarRental::t('Diesel') ?><?php } ?></div>
													<?php } ?>
													<?php if (isset($summary['info']->vehicle_consumption) && !empty($summary['info']->vehicle_consumption)) { ?>
														<div class="icon-text"><span class="sprite-timeout"></span><abbr title="<?= CarRental::t('Average Consumption') ?>"><?= $summary['info']->vehicle_consumption ?> <?= (($summary['info']->vehicle_consumption_metric == 'eu') ? 'l/100km' : 'MPG') ?></abbr></div>
													<?php } ?>
													
													<?php if (isset($summary['info']->vehicle_transmission) && !empty($summary['info']->vehicle_transmission)) { ?>
														<div class="icon-text"><?= (($summary['info']->vehicle_transmission == 1) ? CarRental::t('Transmission: Automatic') : CarRental::t('Transmission: Manual')) ?></div>
													<?php } ?>
													<?php if (isset($summary['info']->vehicle_free_distance)) { ?>
														<div class="icon-text"><?= CarRental::t('Free distance') ?>: <?php if ((int) $summary['info']->vehicle_free_distance > 0) { ?><?= $summary['info']->vehicle_free_distance ?><?php } else { ?><?= CarRental::t('Unlimited') ?><?php } ?></div>
													<?php } ?>
													<?php if (isset($summary['info']->vehicle_deposit) && (int) $summary['info']->vehicle_deposit > 0) { ?>
														<div class="icon-text"><?= CarRental::t('Deposit') ?>: <?= $summary['info']->vehicle_deposit ?></div>
													<?php } ?>
													
													
												</div>
											</div>
										</div>
										
										<?php if (isset($summary['prices']) && !empty($summary['prices'])) { ?>
											<?php $total_amount = 0; ?>
											<?php $cc_before = '$'; ?>
											<?php $cc_after = ''; ?>
											<?php foreach ($summary['prices'] as $key => $val) { ?>
												<?php 
													if ($key == 0) {
														$cc_before = CarRental::get_currency_symbol('before', $val->currency);
														$cc_after = CarRental::get_currency_symbol('after', $val->currency);
													}
													$total_amount += number_format($val->price, 2, '.', '');
												?>
												<div class="row row-boxed">
													<p style="float:left;width:80%;margin:0;"><?= translate_extras(reformat_date_string(CarRental::t($val->name),isset($theme_options['date_format']) ? $theme_options['date_format'] : '', isset($theme_options) && isset($theme_options['time_format']) ? $theme_options['time_format'] : 24)); ?></p>
													<span class="pull-right"><?= CarRental::get_currency_symbol('before', $val->currency) ?><?= number_format($val->price, 2, '.', ',') ?><?= CarRental::get_currency_symbol('after', $val->currency) ?></span>
												</div>
											<?php } ?>
										<?php } ?>

										<div class="row row-total">								
											
											<p class="pull-right">
												<strong><?= CarRental::t('Payment Method') ?> </strong><br>
												<span class="additional xxlarge">
													<?php if ($summary['info']->payment_option == 'cash') { ?>
														<?= CarRental::t('Pay by cash upon pick up') ?>
													<?php } elseif ($summary['info']->payment_option == 'cc') { ?>
														<?= CarRental::t('Pay by credit card upon pick up') ?>
													<?php } elseif ($summary['info']->payment_option == 'paypal') { ?>
														<?= CarRental::t('PayPal payment') ?>
													<?php } elseif ($summary['info']->payment_option == 'bank') { ?>
														<?= CarRental::t('Bank transfer payment') ?>
													<?php } else { ?>
														<?= CarRental::t('Pay by credit card') ?>
													<?php } ?>
												</span>
											</p>
										</div>
										
										<div class="row row-total">
											<p class="pull-right">
												<strong><?= CarRental::t('Total Amount') ?> </strong><br>
												<span class="additional xxlarge"><?= $cc_before ?><?= number_format($total_amount, 2, '.', ',') ?><?= $cc_after ?></span>
											</p>
										</div>
										<?php if ($summary['info']->paid_online > 0) { ?>
										<div class="row row-total">
											<p class="pull-right">
												<strong><?= CarRental::t('Paid') ?> </strong><br>
												<span class="additional xxlarge"><?= $cc_before ?><?= number_format($summary['info']->paid_online, 2, '.', ',') ?><?= $cc_after ?></span>
											</p>
										</div>
										<?php } ?>										
										<!-- .row -->
											
									</div>
									<!-- .column -->

								</div>
								<!-- .columns-2 -->

								<div class="control-group control-submit">
									<div class="control-field align-right">
									<br /><br />
										<a href="javascript:window.print();" class="btn btn-primary">
											<span class="sprite-print"></span><?= CarRental::t('Print Order Details') ?>
										</a>
										<a href="?page=carrental&amp;terms=1" target="_blank" class="show_terms btn btn-primary">
											<?= CarRental::t('Show Terms and Conditions') ?>
										</a>
										<?php do_action( 'carrental_summary_print_buttons'); ?>
									</div>
									<!-- .control-field -->
								</div>
								<!-- .control-group -->
								
								<script type="text/javascript">
									
									jQuery(document).ready(function() {
										
										jQuery('.show_terms').on('click', function() {
											window.open(jQuery(this).attr('href'), '_blank', 'menubar=yes,toolbar=no,directories=no,scrollbars=yes,width=700,height=600');
											return false
										});
									});
								
								</script>
								
							</div>
							<!-- .box -->

						</div>
						<!-- .bordered-content -->

			</div>
			<!-- .column -->
		
		<?php } ?>
		
	</div>
	<!-- .container -->
	
</section>
<!-- .content -->	
		
<?php get_footer(); ?>