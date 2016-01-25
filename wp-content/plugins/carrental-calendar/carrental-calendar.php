<?php
/**
 * @package CarRental-Calendar
 * @require CarRental 
 */
/*
Plugin Name: Ecalypse Car Rental - Calendar
Plugin URI: http://ecalypse.com/wordpresscarrental/
Description: Ecalypse Car Rental enables complete rental management of cars, bikes and other equipment.
Version: 1.2
Author: HOGM s.r.o.
Author URI: http://ecalypse.com/
License: GPLv3
Text Domain: carrental-calendar
*/

if ( !function_exists( 'add_action' ) ) {
	echo "I'm just a plugin, not much I can do when called directly."; exit;
}

if (!isset($_SESSION)) {
	session_regenerate_id();
	session_start();
}
    
define( 'CARRENTAL_CALENDAR__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CARRENTAL_CALENDAR__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CARRENTAL_CALENDAR_VERSION', '1.2'); 
define( 'CARRENTAL_CALENDAR_UPDATE_URL', 'http://ecalypse.com/?page=carrental-admin&key=7dc8842e824d014c7f44db25aa284903');

register_activation_hook( __FILE__, array( 'CarRental_Calendar', 'plugin_activation' ) ); 

//register_deactivation_hook( __FILE__, array( 'CarRental', 'plugin_deactivation' ) );

if (is_admin()) {
	if (is_file(CARRENTAL_CALENDAR__PLUGIN_DIR . '../carrental/class.carrental-admin.php')) {
		require_once( CARRENTAL_CALENDAR__PLUGIN_DIR . '../carrental/class.carrental-admin.php' );
		require_once( CARRENTAL_CALENDAR__PLUGIN_DIR . 'class.carrental-calendar.php' );
		add_action( 'init', array( 'CarRental_Calendar', 'init' ) );
	}
}