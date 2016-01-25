<?php
/**
 * The Template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

get_header(); ?>

<section class="intro">
		<div>
			<?php $theme_options = unserialize(get_option('carrental_theme_options')); ?>
			<div class="slideshow-item static"<?php if (isset($theme_options['picture_otherpages']) && !empty($theme_options['picture_otherpages'])) { ?> style="background-image:url('<?= htmlspecialchars($theme_options['picture_otherpages']) ?>');"<?php } ?>>
				
				<div class="slideshow-item-wrap">						
					<div class="slideshow-item-content">
						<div class="row">
							<div class="h2">
								<span><?= CarRental::t('Feel the Joy.') ?></span> <?= CarRental::t('Fully inclusive Rates') ?>
							</div>
						</div>
						<div class="row">
							<p><?= CarRental::t('Save up to 35%') ?> <span><?= CarRental::t('Pay Now Rates') ?></span></p>
						</div>
					</div>
					
				</div>
			</div>
		</div>
	</section>

<section class="content">	

		<div class="container">
			
			<div class="columns<?php if ( is_active_sidebar( 'page-sidebar' ) ) { ?>-2<?php } ?> break-md aside-on-right">
				
				<div class="column column-fluid">
					<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'content', get_post_format() ); ?>
					<?php comments_template( '', true ); ?>
			<?php endwhile; // end of the loop. ?>
				</div>
				
				<?php if ( is_active_sidebar( 'page-sidebar' ) ) { ?>
					<div class="column column-fixed sidebar">
						<div class="box box-clean">
							<div class="box-inner-small">
								<div class="invert-columns-2 init-md">
									<?php dynamic_sidebar( 'page-sidebar' ); ?>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
				
			</div>
		</div>
		<!-- .container -->
		
	</section>
	<!-- .content -->

<?php get_footer(); ?>