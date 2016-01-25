<?php

/**
 * @package CarRental-Client-Area
 * @require CarRental 
 */
/*
  Plugin Name: Ecalypse Car Rental - Client Area
  Plugin URI: http://ecalypse.com/wordpresscarrental/
  Description: Client area plugin for ecalypse carrental plugin
  Version: 1.0
  Author: HOGM s.r.o.
  Author URI: http://ecalypse.com/
  License: GPLv3
  Text Domain: carrental-client-area
 */

if (!function_exists('add_action')) {
	echo "I'm just a plugin, not much I can do when called directly.";
	exit;
}

if (!isset($_SESSION)) {
	session_regenerate_id();
	session_start();
}

define('CARRENTAL_CLIENT_AREA__PLUGIN_URL', plugin_dir_url(__FILE__));
define('CARRENTAL_CLIENT_AREA__PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CARRENTAL_CLIENT_AREA_VERSION', '1.0');
define('CARRENTAL_CLIENT_AREA_UPDATE_URL', 'http://ecalypse.com/?page=carrental-admin&key=a66edcbfd331385ec1b4999f2075c1fd');

register_activation_hook(__FILE__, array('CarRental_Client_Area', 'plugin_activation'));
//register_deactivation_hook( __FILE__, array( 'CarRental', 'plugin_deactivation' ) );

// Test if it is front-end AJAX call
$fe_ajax = false;
if (defined('DOING_AJAX') && DOING_AJAX) {
	if (isset($_REQUEST['fe_ajax'])) {
		$fe_ajax = true;
	}
}

if (is_admin() && !$fe_ajax) {
	if (is_file(CARRENTAL_CLIENT_AREA__PLUGIN_DIR . '../carrental/class.carrental-admin.php')) {
		require_once( CARRENTAL_CLIENT_AREA__PLUGIN_DIR . '../carrental/class.carrental-admin.php' );
		require_once( CARRENTAL_CLIENT_AREA__PLUGIN_DIR . 'class.carrental-client-area.php' );
		add_action('init', array('CarRental_Client_Area', 'init_admin'));
	}
} else {
	if (is_file(CARRENTAL_CLIENT_AREA__PLUGIN_DIR . '../carrental/class.carrental-admin.php')) {
		require_once( CARRENTAL_CLIENT_AREA__PLUGIN_DIR . '../carrental/class.carrental-admin.php' );
		require_once( CARRENTAL_CLIENT_AREA__PLUGIN_DIR . 'class.carrental-client-area.php' );
		add_action('init', array('CarRental_Client_Area', 'init_public'));
	}
}
