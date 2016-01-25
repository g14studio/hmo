<?php
/**
 * The main template file
 *
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage CarRental
 * @since CarRental 3.0.0
 */

if (is_page()) {
	include(get_file_template_path('single.php'));
} else {

get_header(); ?>
	
	<section class="intro">
			
			<div>
					<?php $theme_options = unserialize(get_option('carrental_theme_options')); ?>
					<?php $slider_options = unserialize(get_option('carrental_theme_slider_options')); ?>
					<?php $background_style = "background-image:url('".htmlspecialchars($theme_options['picture_homepage'])."');"; ?>
					<?php 
					$has_slider = false;
					if (is_array($slider_options) && is_array($slider_options['slider-pictures'])) { 
						$background_style = "background-image:none;";
						$has_slider = true;
					} ?>
					<div class="slideshow-item booking<?php echo $has_slider ? ' with-slider' : '';?>" <?php if ((isset($theme_options['picture_homepage']) && !empty($theme_options['picture_homepage'])) && !$has_slider) { ?>style="<?php echo $background_style?>"<?php } ?>>
						<?php if ($has_slider) { ?>
							<div class="static-background" <?php if (isset($theme_options['picture_homepage']) && !empty($theme_options['picture_homepage'])) { ?>style="<?php echo "background-image:url('".htmlspecialchars($theme_options['picture_homepage']).")";?>;"<?php } ?>></div>
						<?php } ?>
						<?php if (is_array($slider_options) && is_array($slider_options['slider-pictures'])) { ?>
						<div id="HPSlider" class="clearfix" style="<?php echo (int)$slider_options['slider-height'] > 0 ? 'height:'.(int)$slider_options['slider-height'].'px' : '';?>" <?php echo 'data-slider-margin="'.((int)$slider_options['slider-margin'] > 0 ? (int)$slider_options['slider-margin'] : 5).'"';?> <?php echo 'data-slider-pager="'.((int)$slider_options['slider-pager'] && $slider_options['slider-pager'] != '' > 0 ? 1 : 0).'"';?> <?php echo 'data-slider-controls="'.((int)$slider_options['slider-controls'] && $slider_options['slider-controls'] != '' > 0 ? 1 : 0).'"';?> <?php echo 'data-slider-height="'.((int)$slider_options['slider-height'] > 0 ? (int)$slider_options['slider-height'] : 340).'"';?> <?php echo 'data-slider-transition="'.($slider_options['slider-transition'] != '' ? $slider_options['slider-transition'] : 'fade').'"';?>>
							<ul class="HPslider">
								<?php foreach ($slider_options['slider-pictures'] as $picture) { ?>
									<li><img alt="" src="<?php echo $picture;?>" /></li>
								<?php } ?>
							</ul>
						</div>
						<?php } ?>
						<div class="slideshow-item-wrap">						
							<div class="slideshow-item-content">
									
								<div class="tabs">
									<ul class="tabs-navigation">
										<li class="tabs-navigation-active">
											<a href="javascript:void(0);" data-tab-target="quick-book"><?= CarRental::t('QUICK BOOK') ?></a>
										</li>
										<li class="tabs-navigation-link">
											<a href="javascript:void(0);" data-tab-target="manage-booking"><?= CarRental::t('MANAGE BOOKING') ?></a>
										</li>
									</ul>

									<div class="tabs-content">
										
										<div data-tab-id="quick-book" class="tabs-content-tab tabs-content-tab-active">
											
											<?php carrental_get_booking_form(); ?>
											
										</div>
										<!-- .tabs-content-tab -->

										<div data-tab-id="manage-booking" class="tabs-content-tab">
											<form action="" method="post" class="form form-request form-vertical form-size-100">
												<fieldset>
													
													<div class="control-group">
														<div class="control-label">
															<label for="carrental_order_number"><?= CarRental::t('Order Number') ?>:</label>
														</div>
														<div class="control-field">
															<input type="text" name="id_order" id="carrental_order_number" class="control-input">
														</div>
													</div>
													
													<div class="control-group">
														<div class="control-label">
															<label for="carrental_order_email"><?= CarRental::t('Your E-mail') ?>:</label>
														</div>
														<div class="control-field">
															<input type="text" name="email" id="carrental_order_email" class="control-input">
														</div>
													</div>
													
													<div class="control-group">
														<div class="control-field align-right">
															<input type="hidden" name="page" value="carrental">
															<button type="submit" name="manage_booking" class="btn btn-primary"><?= CarRental::t('SHOW ORDER DETAILS') ?></button>	
														</div>
													</div>
													
												</fieldset>
											</form>
										</div>
										<!-- .tabs-content-tab -->

									</div>
									<!-- .tabs-content -->
								</div>
								<!-- .tabs -->					

							</div>
							<!-- .slideshow-item-content -->			
							
						</div>
						<!-- .slideshow-item-wrap -->				

					</div>


			</div>
			<!-- .container -->

		</section>
		<!-- .intro -->
	
	<hr>
	
	<?php $theme_options = unserialize(get_option('carrental_theme_options')); ?>
	<section class="content-main">	

		<div class="container">
						
			<div class="columns-3 main-links">
				
				<div class="column">
					
					<?php if (isset($theme_options['our_cars_page']) && !empty($theme_options['our_cars_page'])) { ?>
						<h2>
							<a href="<?= get_permalink($theme_options['our_cars_page']) ?>" title="" class="item">
								<span class="item-thumb">
									<span class="sprite-cars"></span>
								</span>
								<span class="item-content high">
									<?= CarRental::t('Our Cars') ?>
								</span>
							</a>
						</h2>
					<?php } ?>
					
				</div>
				<!-- .column -->

				<div class="column">
					
					<?php if (isset($theme_options['our_locations_page']) && !empty($theme_options['our_locations_page'])) { ?>
						<h2>
							<a href="<?= get_permalink($theme_options['our_locations_page']) ?>" title="" class="item">
								<span class="item-thumb">
									<span class="sprite-location"></span>
								</span>
								<span class="item-content additional">
									<?= CarRental::t('Our Locations') ?>
								</span>
							</a>
						</h2>
					<?php } ?>
				</div>
				<!-- .column -->

				<div class="column">
				
					<?php if (isset($theme_options['manage_booking_page']) && !empty($theme_options['manage_booking_page'])) { ?>
						<h2>
							<a href="<?= get_permalink($theme_options['manage_booking_page']) ?>" title="" class="item">
								<span class="item-thumb">
									<span class="sprite-manage-booking"></span>
								</span>
								<span class="item-content extra">
									<?= CarRental::t('Manage reservation') ?>
								</span>
							</a>
						</h2>
					<?php } ?>
				</div>
				<!-- .column -->

			</div>
			<!-- .columns-4 -->

			<div class="columns-3">
				
				<div class="column">
					
					<?php if ( is_active_sidebar( 'main-content-1' ) ) { ?>
						<div id="main-content-1">
							<?php dynamic_sidebar( 'main-content-1' ); ?>
						</div><!-- #secondary -->
					<?php } ?>
					
				</div>
				<!-- .column -->

				<div class="column">
					
					<?php if ( is_active_sidebar( 'main-content-2' ) ) { ?>
						<div id="main-content-2">
							<?php dynamic_sidebar( 'main-content-2' ); ?>
						</div><!-- #secondary -->
					<?php } ?>

				</div>
				<!-- .column -->

				<div class="column">
					
					<?php if ( is_active_sidebar( 'main-content-3' ) ) { ?>
						<div id="main-content-3">
							<?php dynamic_sidebar( 'main-content-3' ); ?>
						</div><!-- #secondary -->
					<?php } ?>

				</div>
				<!-- .column -->

			</div>
			<!-- .columns-3 -->

			<?php if ( is_active_sidebar( 'main-content-full-size' ) ) { ?>
				<div id="main-content-4">
					<?php dynamic_sidebar( 'main-content-full-size' ); ?>
				</div><!-- #secondary -->
			<?php } ?>
		</div>
		<!-- .container -->
		
	</section>
	<!-- .content -->		
		
<?php get_footer(); ?>
<?php } ?>