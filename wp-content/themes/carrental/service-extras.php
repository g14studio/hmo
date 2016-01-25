<?php
/**
 * Template Name: G14 Studio: Services Extras
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
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
							<?php if (!isset($theme_options['show_breadcrumb']) || (isset($theme_options['show_breadcrumb']) && $theme_options['show_breadcrumb'] == 1)) { ?>
							<p class="breadcrumb">
								<a href="<?= home_url() ?>" title="<?= CarRental::t('Home') ?>"><?= CarRental::t('Home') ?></a> / <span class="active"><?= CarRental::t('Servicios Exclusivos') ?></span>
							</p>
							<?php } ?>
						</div>
						<!-- .slideshow-item-wrap -->				

					</div>


			</div>
			<!-- .container -->

		</section>
		<!-- .intro -->

		<hr>

		<section class="content">	

			<div class="container-extras">				
				<?php while ( have_posts() ) : the_post(); ?>
					<?php the_content(); ?>
				<?php endwhile; // end of the loop. ?>

			
		</section>
		
<?php get_footer(); ?>