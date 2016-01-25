<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 *
 * @package WordPress
 * @subpackage CarRental
 * @since CarRental 3.0.0
 */
?>
	
	<hr>

	<footer>
		<div class="footer-info">
			<div class="container">
				<div class="columns-3">
					
					<div class="column column-thiner quick-contact">
						
						<?php if ( is_active_sidebar( 'footer-1' ) ) { ?>
							<div class="footer-info-item">						
								<?php dynamic_sidebar( 'footer-1' ); ?>
							</div><!-- .footer-info-item -->
						<?php } ?>
					
					</div><!-- .column -->

					<div class="column column-wider">
						
						<?php if ( is_active_sidebar( 'footer-2' ) ) { ?>
							<div class="footer-info-item">						
								<?php dynamic_sidebar( 'footer-2' ); ?>
							</div><!-- .footer-info-item -->
						<?php } ?>
						
					</div>
					<!-- .column -->

					<div class="column column-thiner column-break-wide">

						<?php if ( is_active_sidebar( 'footer-3' ) ) { ?>
							<div class="footer-info-item">						
								<?php dynamic_sidebar( 'footer-3' ); ?>
							</div><!-- .footer-info-item -->
						<?php } ?>
						
					</div>
					<!-- .column -->

				</div>
				<!-- .columns-3 -->		

			</div>
			<!-- .container -->
		
		</div>
		<!-- .footer-info -->

		<hr>

		<div class="footer-copyright">

			<div class="container">
				<?php $ecalypse_footer_settings = unserialize(get_option('ecalypse_footer_settings')); ?>
				<p><?= isset($ecalypse_footer_settings['ecalypse-footer-text-left']) && $ecalypse_footer_settings['ecalypse-footer-text-left'] != '' ? stripslashes($ecalypse_footer_settings['ecalypse-footer-text-left']) : '&copy; '.CarRental::t('HMO El Salvador, 2015. Todos los derechos reservados'); ?></p>
				
				<p><?= isset($ecalypse_footer_settings['ecalypse-footer-text-right']) && $ecalypse_footer_settings['ecalypse-footer-text-right'] != '' ? stripslashes($ecalypse_footer_settings['ecalypse-footer-text-right']) : '';?></p>
				
			</div>
			<!-- .container -->

		</div>
		<!-- #copyright -->				
		
	</footer>	
		
<?php wp_footer(); ?>
</body>
</html>