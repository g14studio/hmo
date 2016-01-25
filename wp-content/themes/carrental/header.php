<?php
/**
 * The Header template for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage CarRental
 * @since CarRental 3.0.0
 */
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?> class="responsive no-js">
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title>HMO El Salvador</title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<link href="<?php echo get_template_directory_uri(); ?>/css/lightbox.css" rel="stylesheet" />
<?php // Loads HTML5 JavaScript file to add support for HTML5 elements in older IE versions. ?>
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->
<?php wp_head(); ?>
<script type="text/javascript">
if(typeof jQuery == 'undefined'){
	document.write('<script src="http://code.jquery.com/jquery-latest.min.js"></'+'script>');
}
</script>
<script src="<?php echo get_template_directory_uri(); ?>/js/lightbox.min.js"></script>
</head>
<?php $theme_options = unserialize(get_option('carrental_theme_options')); ?>
<body <?php body_class(); ?>>
	
	<header>
			<?php
			//echo 'lng je '.get_query_var( 'lng', 'neni' );
			if (!class_exists('CarRental')) {
				die('<p>Error - Ecalypse Car Rental plugin is not installed or is deactivated.</p></header></body></html>');
			}
			?>
			<div class="container">
				
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
					<?php if (isset($theme_options['picture_logo']) && !empty($theme_options['picture_logo'])) { ?>
						<img src="<?= htmlspecialchars($theme_options['picture_logo']) ?>" class="carrental-logo">
					<?php } else { ?>
						<h1 style="float:left;margin-top:1em;"><?php bloginfo( 'name' ); ?></h1>
					<?php } ?>
				</a><!-- .logo -->		
				
				<hr>

				<div class="header-right">			

					<div class="top-panel form-small">
						<?php do_action( 'carrental_header_before_currency'); ?>
						
						<?php $currency = array(get_option('carrental_global_currency')); ?>
						<?php $av_currencies = unserialize(get_option('carrental_available_currencies')); ?>
						<?php if (!empty($av_currencies)) { $av_currencies = array_keys($av_currencies); $currency = array_merge($currency, $av_currencies); } ?>
						
						<?php if (count($currency) > 1) { ?> 
							<div class="control-field">
								<form action="" method="get">
									<select name="currency" id="carrental_change_currency" style="padding: 1px 3px;">
										<?php foreach ($currency as $cc) { ?>
											<option value="<?= $cc ?>" <?php if (isset($_SESSION['carrental_currency']) && $_SESSION['carrental_currency'] == $cc) { ?>selected<?php } ?>><?= $cc ?></option>
										<?php } ?>
									</select>
									<input type="hidden" name="page" value="carrental">
									<input type="hidden" name="change_currency" value="1">
								 </form>
							</div>
							<script type="text/javascript">
								jQuery(document).ready(function() {
									jQuery('#carrental_change_currency').on('change', function() {
										jQuery(this).parent().submit();
									});
								});
							</script>
						<?php } ?>
						
						<?php $available_languages = unserialize(get_option('carrental_available_languages')); ?>
						<?php if (empty($available_languages)) {$available_languages = array();} ?>
						<?php foreach ($available_languages as $lng_key => $lng) { ?>
							<?php if ((isset($lng['active']) && !$lng['active'])) { ?>
								<?php unset($available_languages[$lng_key]); ?>
							<?php } ?>
						<?php } ?>
						<?php if (count($available_languages) > 0) { ?>
							<div class="control-field">
								<form action="" method="get">
									<select name="language" id="carrental_change_language" style="padding: 1px 3px;">
										<option value="en_GB" <?php if (isset($_SESSION['carrental_language']) && $_SESSION['carrental_language'] == 'en_GB') { ?>selected<?php } ?>><?= CarRental::t('English') ?></option>
										<?php foreach ($available_languages as $key => $val) { 
											if ($key == 'en_GB') {continue;}
											?>
											<option value="<?= $key ?>" <?php if (isset($_SESSION['carrental_language']) && $_SESSION['carrental_language'] == $key) { ?>selected<?php } ?>><?= $val['lang'] ?></option>
										<?php } ?>
									</select>
									<input type="hidden" name="page" value="carrental">
									<input type="hidden" name="change_language" value="1">
								</form>
							</div>
							<script type="text/javascript">
								jQuery(document).ready(function() {
									jQuery('#carrental_change_language').on('change', function() {
										jQuery(this).parent().submit();
									});
								});
							</script>
							
						<?php } ?>
						
					</div>
					<!-- .top-panel -->		

					<hr>

					<nav>
						<div class="navigation-header-trigger"><?= CarRental::t('Navigation') ?></div>
						<?php 
							$lang = 'en_GB';
							if (isset($_SESSION['carrental_language'])) {
								$lang = $_SESSION['carrental_language'];
							}
							$menu = 'menu_' . $lang;
							
							if (!has_nav_menu($menu, 'carrental')) {
								$menu = 'primary';
							}
							
						?>
						<?php if ( has_nav_menu( $menu, 'carrental' ) ) { ?>
							
							<?php wp_nav_menu(array( 'container' => false, 'theme_location' => $menu, 'menu_class' => 'navigation navigation-header' )); ?>
							
						<?php } else { ?>
							<ul class="navigation navigation-header">
					 			<li><a href="<?php echo get_option('home'); ?>"><?= CarRental::t('Home') ?></a></li>
								<?php wp_list_pages('title_li=&depth=4&sort_column=menu_order'); ?>
							</ul>
						<?php	} ?>
						<!-- .navigation -->
					</nav>	

				</div>
				<!-- .header-right -->
			
			</div>
			<!-- .container -->

		</header>

	<hr>
	