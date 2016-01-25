<?php
/**
 * Choose car - filter
 *
 * @package WordPress
 * @subpackage CarRental
 * @since CarRental 3.0.0
 */

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
					<a href="<?= home_url(); ?>">
						<span class="steps-number">1</span> <?= CarRental::t('Create request') ?>
						<span class="sprite-arrow-right"></span>
					</a>
				</li>
				<li class="active">
					<a href="javascript:void(0);">
						<span class="steps-number">2</span> <?= CarRental::t('Choose a car') ?>
						<span class="sprite-arrow-right"></span>
					</a>
				</li>
				<li>
					<a href="javascript:void(0);">
						<span class="steps-number">3</span> <?= CarRental::t('Services &amp; book') ?>
						<span class="sprite-arrow-right"></span>
					</a>
				</li>
				<li>
					<a href="javascript:void(0);">
						<span class="steps-number">4</span> <?= CarRental::t('Summary') ?>
						<span class="sprite-arrow-right"></span>
					</a>
				</li>
			</ul>
		
		<?php include(get_file_template_path('choose-car-content.php')); ?>
		
	</div>
	<!-- .container -->
	
</section>
<!-- .content -->	
		
<?php get_footer(); ?>