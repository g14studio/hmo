<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 * @package WordPress
 * @subpackage CarRental
 * @since CarRental 3.0.0 
 */

get_header(); ?>
	
	<section class="content">	

		<div class="container">
		
			<h1 class="entry-title"><?= CarRental::t('This is somewhat embarrassing, isn&rsquo;t it?') ?></h1>

			<div class="entry-content">
				<p><?= CarRental::t('It seems we can&rsquo;t find what you&rsquo;re looking for.') ?></p>
			</div>
			
		</div>
		<!-- .container -->
		
	</section>
	<!-- .content -->			

<?php get_footer(); ?>