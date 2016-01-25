<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
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
			
			<div class="columns-2 break-md aside-on-right">
				
				<div class="column column-fluid">
					<?php while ( have_posts() ) : the_post(); ?>
					
						<h1 class="entry-title"><?php the_title(); ?></h1>
		
						<div class="entry-content">
							
							<?php the_content(); ?>
							<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'twentytwelve' ), 'after' => '</div>' ) ); ?>
								
						</div>
					
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