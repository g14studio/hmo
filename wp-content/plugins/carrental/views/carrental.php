<div class="carrental-wrapper">
	
	<?php include CARRENTAL__PLUGIN_DIR . 'views/header.php'; ?>

	<div class="row">
		<div class="col-md-12 carrental-main-wrapper">
			<div class="carrental-main-content">
				
				<?php include CARRENTAL__PLUGIN_DIR . 'views/flash_msg.php'; ?>
				
				<div class="row">
					<div class="col-md-11">
						
						<?php $currency = get_option('carrental_global_currency'); ?>
						<?php if (!$currency || strlen($currency) != 3) { ?>
							<div class="alert alert-danger">Please, set-up your <strong>Global Currency</strong> in <a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-settings')); ?>">Settings</a>.</div>
						<?php } ?>
							
						<p class="help-block">To change pictures, logos , filter settings and others, go to Appearance->Theme settings (must have compatible theme active).</p>
						
						<div class="col-md-6">
							<div class="panel panel-info">
							  <div class="panel-heading">Ecalypse News</div>
							  <div class="panel-body" id="ecalypse-news-div">
								  <div id="ecalypse-news-ajax-loader">
									  <img src="<?php echo CARRENTAL__PLUGIN_URL;?>/assets/ajax-loader.gif">
									  <span style="margin-left: 15px;"> We are loading actual news for you. Please wait a second.</span>
								  </div>
							  </div>
							</div>
						</div>
						<div id="ecalypse-feed-dialog" title=""></div>
						<div class="col-md-4">
						<?php if (isset($quick_info) && !empty($quick_info)) { ?>
							
							<?php if (isset($quick_info['booking_progress']) && !empty($quick_info['booking_progress'])) { ?>
								<div class="alert alert-success">
									<p class="lead">
										You have <strong><?= $quick_info['booking_progress'] ?> bookings</strong> in progress
										<?php if (isset($quick_info['booking_future']) && !empty($quick_info['booking_future'])) { ?>
										 and <strong><?= $quick_info['booking_future'] ?> bookings</strong> in the future.
										<?php } ?>
										<a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-booking')); ?>" class="btn btn-default">Show bookings</a>
									</p>
								</div>
							<?php } ?>
							
							<div class="panel panel-info">
							  <div class="panel-heading">Quick info</div>
							  <div class="panel-body">
							  	<?php if (isset($quick_info['fleet']) && !empty($quick_info['fleet'])) { ?>
							    	<p class="lead">You have <strong><?= $quick_info['fleet'] ?></strong> active vehicles in the <a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-fleet')); ?>" class="btn btn-default"><strong>Fleet</strong></a></p>
							  	<?php } ?>
							  	<?php if (isset($quick_info['extras']) && !empty($quick_info['extras'])) { ?>
										<p class="lead">You have <strong><?= $quick_info['extras'] ?></strong> active items in the <a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-extras')); ?>" class="btn btn-default"><strong>Extras</strong></a></p>
									<?php } ?>
									<?php if (isset($quick_info['branches']) && !empty($quick_info['branches'])) { ?>
										<p class="lead">You have <strong><?= $quick_info['branches'] ?></strong> active locations in the <a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-branches')); ?>" class="btn btn-default"><strong>Branches</strong></a></p>
									<?php } ?>
									<?php if (isset($quick_info['pricing']) && !empty($quick_info['pricing'])) { ?>
										<p class="lead">You have <strong><?= $quick_info['pricing'] ?></strong> active schemes in the <a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-pricing')); ?>" class="btn btn-default"><strong>Pricing</strong></a></p>
									<?php } ?>
									<?php if (isset($quick_info['deleted']) && !empty($quick_info['deleted'])) { ?>
										<p class="lead">You have <strong><?= $quick_info['deleted'] ?></strong> deleted items.</a></p>
									<?php } ?>
							  </div>
							</div>
						<?php } else { ?>
							
							<h3>Database is empty now, please continue to <a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-settings')); ?>">Settings</a> first.</h3>
							
							<?php /* HIDDEN ?>
							<h3>Database is empty now, do you wish to import demo data?</h3>
							<form action="" method="post" class="form" role="form" onsubmit="return confirm('Do you really want to import demo data?');">
								
								<p>
									* This action will import 3 vehicle categories, 3 cars, 2 extras, 2 branches and 4 pricing schemes.
								</p>
								
								<!-- Submit //-->
							  <div class="form-group">
							  	<div class="col-sm-4">
							  		<button type="submit" name="import_demo_data" class="btn btn-warning"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Import demo data</button>
							  	</div>
								</div>
							
							</form>
							<?php /**/ ?>
							
						<?php } ?>
							</div>
					</div>
				</div>
				
			</div>
		</div>
	</div>
	
</div>