<?php

/**
 * CarRental functions and definitions
 */
define('CARRENTAL__THEME_URL', get_template_directory_uri());
define('CARRENTAL__THEME_DIR', dirname(realpath(__FILE__)));
define('CARRENTAL_THEME_UPDATE_URL', 'http://ecalypse.com/?page=carrental-admin&key=060d826500b7df947bc5e6afaad3a73b');

if (!isset($_SESSION)) {
	session_regenerate_id();
	session_start();
}

/**
 * CarRental setup.
 */
function carrental_setup() {

	add_editor_style();

	// This theme uses wp_nav_menu() in one location.
	register_nav_menu('primary', __('Primary Menu', 'carrental'));

	// Check theme activation
	$installed = get_option('carrental_theme_installed');

	if (is_null($installed) || empty($installed)) {

		// Add pages
		$theme_options = array();

		$our_cars = array(
			'post_title' => 'Our cars',
			'post_name' => 'our-cars',
			'post_content' => '[carrental_cars]',
			'post_status' => 'publish',
			'post_author' => 1,
			'post_type' => 'page',
			'page_template' => 'our-cars-template.php'
		);
		$theme_options['our_cars_page'] = wp_insert_post($our_cars, $wp_error);

		$our_locs = array(
			'post_title' => 'Our locations',
			'post_name' => 'our-locations',
			'post_content' => '[carrental_locations]',
			'post_status' => 'publish',
			'post_author' => 1,
			'post_type' => 'page',
			'page_template' => 'our-locations-template.php'
		);
		$theme_options['our_locations_page'] = wp_insert_post($our_locs);

		$our_cars = array(
			'post_title' => 'Manage booking',
			'post_name' => 'manage-booking',
			'post_content' => '[carrental_manage_booking]',
			'post_status' => 'publish',
			'post_author' => 1,
			'post_type' => 'page',
			'page_template' => 'manage-booking-template.php'
		);
		$theme_options['manage_booking_page'] = wp_insert_post($our_cars);

		update_option('carrental_theme_options', serialize($theme_options));
		update_option('sidebars_widgets', '');

		update_option('carrental_theme_installed', 1);
	}
}

add_action('after_setup_theme', 'carrental_setup');

/**
 * 	Menu translations
 */
add_action('init', 'carrental_register_additional_menus', 11);

function carrental_register_additional_menus() {

	$available_languages = unserialize(get_option('carrental_available_languages'));
	if ($available_languages && !empty($available_languages)) {
		foreach ($available_languages as $key => $val) {
			register_nav_menu('menu_' . $key, $val['lang'] . ' (' . strtoupper($val['country-www']) . ')');
		}
	}
}

/**
 * Enqueue scripts and styles for front-end.
 */
function carrental_scripts_styles() {
	global $wp_styles;

	// Adds JavaScript for handling the navigation menu hide-and-show behavior.
	wp_enqueue_script('carrental-app', get_template_directory_uri() . '/js/app.js', array());

	// Loads our main stylesheet.
	wp_enqueue_style('carrental-style', get_stylesheet_uri());

	// Loads our main stylesheet.
	wp_enqueue_style('carrental-print', get_template_directory_uri() . '/css/print.css', array('carrental-style'), '', 'print');

	// Loads the Internet Explorer specific stylesheet.
	wp_enqueue_style('carrental-ie', get_template_directory_uri() . '/css/main-ie.css', array('carrental-style'), '');
	$wp_styles->add_data('carrental-ie', 'conditional', 'lt IE 9');
}

add_action('wp_enqueue_scripts', 'carrental_scripts_styles');

function carrental_wp_title($title, $sep) {
	global $paged, $page;

	if (is_feed())
		return $title;

	// Add the site name.
	$title .= get_bloginfo('name', 'display');

	// Add the site description for the home/front page.
	$site_description = get_bloginfo('description', 'display');
	if ($site_description && ( is_home() || is_front_page() ))
		$title = "$title $sep $site_description";

	// Add a page number if necessary.
	if ($paged >= 2 || $page >= 2)
		$title = "$title $sep " . sprintf(__('Page %s', 'carrental'), max($paged, $page));

	return $title;
}

add_filter('wp_title', 'carrental_wp_title', 10, 2);

/**
 * Register sidebars.
 *
 * Registers our main widget area and the front page widget areas.
 *
 * @since Twenty Twelve 1.0
 */
function carrental_widgets_init() {

	register_sidebar(array(
		'name' => __('Footer 1st column', 'carrental'),
		'id' => 'footer-1',
		'description' => __('First column in the Footer (for About us, Company info or similar)', 'carrental'),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));

	register_sidebar(array(
		'name' => __('Footer 2nd column', 'carrental'),
		'id' => 'footer-2',
		'description' => __('Second column in the Footer (for Usefull links, Blog posts or similar)', 'carrental'),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));

	register_sidebar(array(
		'name' => __('Footer 3rd column', 'carrental'),
		'id' => 'footer-3',
		'description' => __('Third column in the Footer (for Social icons, Photos or similar)', 'carrental'),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));

	register_sidebar(array(
		'name' => __('Homepage 1st column', 'carrental'),
		'id' => 'main-content-1',
		'description' => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));

	register_sidebar(array(
		'name' => __('Homepage 2nd column', 'carrental'),
		'id' => 'main-content-2',
		'description' => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));

	register_sidebar(array(
		'name' => __('Homepage 3rd column', 'carrental'),
		'id' => 'main-content-3',
		'description' => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));

	register_sidebar(array(
		'name' => __('Homepage full size info', 'carrental'),
		'id' => 'main-content-full-size',
		'description' => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));

	register_sidebar(array(
		'name' => __('Page Sidebar', 'carrental'),
		'id' => 'page-sidebar',
		'description' => '',
		'before_widget' => '<div id="%1$s" class="column widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));
}

add_action('widgets_init', 'carrental_widgets_init');

/**
 * Extend the default WordPress body classes.

 * @param array $classes Existing class values.
 * @return array Filtered class values.
 */
function carrental_body_class($classes) {
	$background_color = get_background_color();
	$background_image = get_background_image();

	$classes[] = 'full-width';

	if (empty($background_image)) {
		if (empty($background_color))
			$classes[] = 'custom-background-empty';
		elseif (in_array($background_color, array('fff', 'ffffff')))
			$classes[] = 'custom-background-white';
	}

	if (is_home()) {
		$classes[] = 'homepage';
	}

	return $classes;
}

add_filter('body_class', 'carrental_body_class');

/**
 * 	Theme settings
 */
function setup_theme_admin_menus() {

	add_theme_page(__('Theme settings', 'carrental'), __('Theme settings', 'carrental'), 'manage_options', 'theme-settings', 'carrental_theme_settings');
	add_theme_page(__('Slider settings', 'carrental'), __('Slider settings', 'carrental'), 'manage_options', 'slider-settings', 'carrental_slider_settings');
}

function carrental_theme_settings() {
	wp_register_style('bootstrap.css', CARRENTAL__PLUGIN_URL . 'assets/bootstrap.css', array(), CARRENTAL_VERSION);
	wp_enqueue_style('bootstrap.css');

	$theme_options = unserialize(get_option('carrental_theme_options'));

	// Get theme translations
	$translations = get_theme_translations();

	$theme = wp_get_theme();

	include(get_file_template_path('theme-settings.php'));
}

function carrental_slider_settings() {
	wp_register_style('bootstrap.css', CARRENTAL__PLUGIN_URL . 'assets/bootstrap.css', array(), CARRENTAL_VERSION);
	wp_enqueue_style('bootstrap.css');
	
	wp_register_style('admin-template-carrental.css', get_template_directory_uri() . '/css/admin.css');
	wp_enqueue_style('admin-template-carrental.css');
	
	wp_enqueue_media();
	
	wp_register_style('jquery-ui.css', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.css', array());
	wp_enqueue_style('jquery-ui.css');

	wp_register_style('jquery.dataTables.css', '//cdn.datatables.net/1.10.0/css/jquery.dataTables.css', array());
	wp_enqueue_style('jquery.dataTables.css');

	wp_deregister_script('jquery');
	wp_register_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js', array());
	wp_enqueue_script('jquery');

	wp_deregister_script('jqueryui');
	wp_register_script('jqueryui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js', array());
	wp_enqueue_script('jqueryui');

	wp_register_script('bootstrap.min.js', CARRENTAL__PLUGIN_URL . 'assets/bootstrap.min.js', array(), CARRENTAL_VERSION);
	wp_enqueue_script('bootstrap.min.js');

	wp_register_style('jquery.dataTables.css', '//cdn.datatables.net/1.10.0/css/jquery.dataTables.css', array());
	wp_enqueue_style('jquery.dataTables.css');

	$theme_slider_options = unserialize(get_option('carrental_theme_slider_options'));

	$theme = wp_get_theme();

	include(get_file_template_path('slider-settings.php'));
}

add_action("admin_menu", "setup_theme_admin_menus");

if (isset($_POST['check_theme_update'])) {
	try {

		$theme = wp_get_theme();

		if (defined("CARRENTAL_THEME_UPDATE_URL") && CARRENTAL_THEME_UPDATE_URL != '') {
			$url = CARRENTAL_THEME_UPDATE_URL . '&v=' . urlencode($theme->version) . '&theme=' . urlencode($theme->name) . '&server=' . urlencode($_SERVER['SERVER_NAME']) . '&version=' . urlencode($theme->version);
			//exit($url);

			$data = json_decode(@file_get_contents($url));

			if ($data && !empty($data)) {

				$check = array();
				$check['last'] = Date('Y-m-d H:i:s');
				$check['update_available'] = false;

				if (!empty($theme->version)) {
					$current_version = $theme->version;
					if ($current_version != $data->version) {
						$check['new_version'] = $data->version;
						$check['new_version_date'] = $data->date;
						$check['new_version_url'] = $data->url;
						$check['update_available'] = true;
					}
				}

				update_option('carrental_theme_update_check', serialize($check));

				$_SESSION['carrental_flash_msg'] = array('status' => 'success', 'msg' => __('Theme update was successfully checked.', 'carrental'));
				Header('Location: ' . add_query_arg(array('page' => 'theme-settings'), admin_url('themes.php')));
				Exit;
			} else {
				throw new Exception('No data from update URL.');
			}
		} else {
			throw new Exception('Updating URL is not defined, please contact us.');
		}
	} catch (Exception $e) {
		$_SESSION['carrental_flash_msg'] = array('status' => 'danger', 'msg' => __('Theme update was not checked due to error (' . $e->getMessage() . ').', 'carrental'));
		Header('Location: ' . add_query_arg(array('page' => 'theme-settings'), admin_url('themes.php')));
		Exit;
	}
}

if (isset($_POST['theme_update'])) {
	process_theme_update();
}

if (isset($_POST['save_slider_settings'])) {
	unset($_POST['save_slider_settings']);
	$_POST['slider-margin'] = (int)$_POST['slider-margin'];
	$_POST['slider-height'] = (int)$_POST['slider-height'];
	
	update_option('carrental_theme_slider_options', serialize($_POST));
	
	$_SESSION['carrental_flash_msg'] = array('status' => 'success', 'msg' => __('Settings was successfully saved.', 'carrental'));
	Header('Location: ' . add_query_arg(array('page' => 'slider-settings'), admin_url('themes.php')));
	Exit;
}

if (isset($_POST['save_theme_settings'])) {
	unset($_POST['save_theme_settings']);

	$picture_filename_hp = (isset($_POST['current_picture_homepage']) ? $_POST['current_picture_homepage'] : NULL);
	if (isset($_FILES['picture_homepage']) && !empty($_FILES['picture_homepage']['tmp_name'])) {
		if (!function_exists('wp_handle_upload')) {
			require_once(ABSPATH . 'wp-admin/includes/file.php');
		}
		$uploadedfile = $_FILES['picture_homepage'];
		$upload_overrides = array('test_form' => false);
		$movefile = wp_handle_upload($uploadedfile, $upload_overrides);
		if ($movefile) {
			$picture_filename_hp = $movefile['url'];
		}
	}
	$_POST['picture_homepage'] = $picture_filename_hp;

	if (isset($_POST['delete_picture_homepage']) && $_POST['delete_picture_homepage'] == 1) {
		$_POST['picture_homepage'] = NULL;
	}

	$picture_filename_other = (isset($_POST['current_picture_otherpages']) ? $_POST['current_picture_otherpages'] : NULL);
	if (isset($_FILES['picture_otherpages']) && !empty($_FILES['picture_otherpages']['tmp_name'])) {
		if (!function_exists('wp_handle_upload')) {
			require_once(ABSPATH . 'wp-admin/includes/file.php');
		}
		$uploadedfile = $_FILES['picture_otherpages'];
		$upload_overrides = array('test_form' => false);
		$movefile = wp_handle_upload($uploadedfile, $upload_overrides);
		if ($movefile) {
			$picture_filename_other = $movefile['url'];
		}
	}
	$_POST['picture_otherpages'] = $picture_filename_other;

	if (isset($_POST['delete_picture_otherpages']) && $_POST['delete_picture_otherpages'] == 1) {
		$_POST['picture_otherpages'] = NULL;
	}

	$picture_filename_logo = (isset($_POST['current_picture_logo']) ? $_POST['current_picture_logo'] : NULL);
	if (isset($_FILES['picture_logo']) && !empty($_FILES['picture_logo']['tmp_name'])) {
		if (!function_exists('wp_handle_upload')) {
			require_once(ABSPATH . 'wp-admin/includes/file.php');
		}
		$uploadedfile = $_FILES['picture_logo'];
		$upload_overrides = array('test_form' => false);
		$movefile = wp_handle_upload($uploadedfile, $upload_overrides);
		if ($movefile) {
			$picture_filename_logo = $movefile['url'];
		}
	}
	$_POST['picture_logo'] = $picture_filename_logo;

	if (isset($_POST['delete_picture_logo']) && $_POST['delete_picture_logo'] == 1) {
		$_POST['picture_logo'] = NULL;
	}

	update_option('carrental_theme_options', serialize($_POST));
	$_SESSION['carrental_flash_msg'] = array('status' => 'success', 'msg' => __('Settings was successfully saved.', 'carrental'));
	Header('Location: ' . add_query_arg(array('page' => 'theme-settings'), admin_url('themes.php')));
	Exit;
}

/**
 * Pages (create when plugin is installed)
 */
function carrental_manage_booking() {

	include(get_file_template_path('manage-booking.php'));
	include_once (get_file_template_path('booking-javascript.php'));
}

add_shortcode('carrental_manage_booking', 'carrental_manage_booking');

function carrental_locations() {

	$locations = CarRental::get_locations();

	include(get_file_template_path('our-locations.php'));
}

add_shortcode('carrental_locations', 'carrental_locations');

function carrental_cars() {

	// Locations + business hours
	$locations = CarRental::get_locations();
	$vehicle_cats = CarRental::get_vehicle_categories();
	$vehicle_names = CarRental::get_vehicle_names();
	$params = array();
	if (isset($_GET['order'])) {
		$params['order'] = $_GET['order'];
	}
	$vehicles = CarRental::get_vehicles($params);
	$fleet_parameters = CarRental::get_fleet_parameters(true);

	wp_register_style('jquery-ui.css', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.css', array());
	wp_enqueue_style('jquery-ui.css');


	include(get_file_template_path('our-cars.php'));
}

add_shortcode('carrental_cars', 'carrental_cars');

function carrental_category($params) {
	$category_id = $params['id'];

	// test if category exists
	// Locations + business hours
	$locations = CarRental::get_locations();
	$vehicle_cats = CarRental::get_vehicle_categories();
	$vehicle_names = CarRental::get_vehicle_names();
	$params = array('cats' => $category_id);
	if (isset($_GET['order'])) {
		$params['order'] = $_GET['order'];
	}
	$vehicles = CarRental::get_vehicles($params);
	$fleet_parameters = CarRental::get_fleet_parameters(true);

	wp_register_style('jquery-ui.css', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.css', array());
	wp_enqueue_style('jquery-ui.css');

	ob_start();
	include(get_file_template_path('our-cars.php'));
	$box = ob_get_clean();
	return $box;
}

add_shortcode('carrental_fleet', 'carrental_fleet');

function carrental_fleet($params) {
	global $carrental_fleet_loaded;
	
	$fleet_id = (int)$params['id'];
	if ($fleet_id < 1) {
		return;
	}

	// test if fleet exists
	$vehicle = CarRental::get_vehicle($fleet_id);
	if (!$vehicle) {
		return;
	}
	$vehicle_cats_raw = CarRental::get_vehicle_categories();
	$vehicle_cats = array();
	foreach ($vehicle_cats_raw as $val) {
		 $vehicle_cats[$val->id_category] = $val;
	}
	
	if (!$carrental_fleet_loaded) {
		$locations = CarRental::get_locations();
		//include(get_file_template_path('booking-form.php'));
		include(get_file_template_path('booking-javascript.php'));
	}
	
	wp_register_style('jquery-ui.css', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.css', array());
	wp_enqueue_style('jquery-ui.css');
	ob_start();
	include(get_file_template_path('shortcode-fleet.php'));
	$box = ob_get_clean();
	$carrental_fleet_loaded = true;
	return $box;
}

add_shortcode('carrental_category', 'carrental_category');

function carrental_book_box($params) {
	ob_start();
	include(get_file_template_path('book-box.php'));
	$box = ob_get_clean();

	return $box;
}

add_shortcode('carrental_book_box', 'carrental_book_box');

add_filter('widget_text', 'shortcode_unautop');
add_filter('widget_text', 'do_shortcode');

/* Booking form on homepage */

function carrental_get_booking_form() {
	global $theme_options;

	// Locations + business hours
	$locations = CarRental::get_locations();
	$vehicle_cats = CarRental::get_vehicle_categories();
	$vehicle_names = CarRental::get_vehicle_names();

	wp_register_style('jquery-ui.css', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.css', array());
	wp_enqueue_style('jquery-ui.css');

	include(get_file_template_path('booking-form.php'));
	include(get_file_template_path('booking-javascript.php'));
}

/**
 * Format time to 12 hours or 24 hours format
 * @param type $time
 * @param int $format
 */
function carrental_time_format($time, $format = 24) {
	$format = (int) $format;
	if ($format != 24 && $format != 12) {
		$format = 24;
	}

	if (strpos($time, ':') !== false) {
		$hours = (int) substr($time, 0, strpos($time, ':'));
		$minutes = (int) substr($time, strpos($time, ':') + 1, 2);
	} else {
		$hours = (int) $time;
		$minutes = 0;
	}

	if (strpos($time, 'pm') !== false) {
		if ($hours != 12) {
			$hours += 12;
		}
	}

	if (strpos($time, 'am') !== false) {
		if ($hours == 12) {
			$hours = 0;
		}
	}

	if ($format == 24) {
		return str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
	} else {
		$timeType = 'am';
		if ($hours == 0) {
			$hours = 12;
		} else {
			if ($hours > 11) {
				$timeType = 'pm';
			}

			if ($hours > 12) {
				$hours -= 12;
			}
		}
		return str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT) . ' ' . $timeType;
	}
}

function get_theme_translations() {
	global $wpdb;

	$dir = TEMPLATEPATH;
	$counter = 0;

	foreach (glob($dir . '/*.php') as $filename) {

		$translations = array();
		$content = file_get_contents($filename);

		preg_match_all("#CarRental\:\:t\('([^']+)'\)#", $content, $out);
		if (isset($out[1]) && !empty($out[1])) {
			$translations = array_merge($translations, $out[1]);
		}

		preg_match_all('#CarRental\:\:t\("([^"]+)"\)#', $content, $out);
		if (isset($out[1]) && !empty($out[1])) {
			$translations = array_merge($translations, $out[1]);
		}


		if (!empty($translations)) {
			foreach ($translations as $val) {
				$wpdb->query($wpdb->prepare('INSERT IGNORE INTO `' . CarRental::$db['translations'] . '` (`original`) VALUES (%s)', $val));
			}
		}

		$counter += count($translations);
	}

	return $counter;
}

function process_theme_update() {
	try {

		$log = 'Theme update: ' . Date('Y-m-d H:i:s') . "\r\n";
		set_time_limit(0);

		// Backup files
		$log .= 'Backuping files...' . "\r\n";
		if (!file_exists(dirname(__FILE__) . '/backup/')) {
			mkdir(dirname(__FILE__) . '/backup/', 0777);
		}

		$backupFolder = dirname(__FILE__) . '/';
		$time = time();
		$finalZip = dirname(__FILE__) . '/backup/backup_' . $time . '.zip';
		$exclude = array('carrental/backup', 'carrental/zip', 'carrental/download');
		$eza = new ExtZipArchive;
		$res = $eza->open($finalZip, ZipArchive::CREATE);

		if ($res === TRUE) {
			$eza->addDir($backupFolder, basename($backupFolder), $exclude);
			$eza->close();
		} else {
			throw new Exception('Could not create backup.');
		}
		$log .= 'Done: ' . Date('Y-m-d H:i:s') . "\r\n";

		// Download new files and unzip
		$log .= 'Downloading...' . "\r\n";
		$check = unserialize(get_option('carrental_theme_update_check'));
		if (isset($check['new_version_url']) && !empty($check['new_version_url'])) {
			$zip = file_get_contents($check['new_version_url']);
			$log .= 'Done: ' . Date('Y-m-d H:i:s') . "\r\n";
			if ($zip && !empty($zip)) {

				if (!file_exists(dirname(__FILE__) . '/download/')) {
					mkdir(dirname(__FILE__) . '/download/', 0777);
				}
				if (!file_exists(dirname(__FILE__) . '/zip/')) {
					mkdir(dirname(__FILE__) . '/zip/', 0777);
				}

				$tempFileName = dirname(__FILE__) . '/download/theme_update.zip';
				if (file_exists($tempFileName)) {
					unlink($tempFileName);
				}

				file_put_contents($tempFileName, $zip);

				$log .= 'Unziping...' . "\r\n";
				$zip = new ZipArchive;
				$res = $zip->open($tempFileName);
				if ($res === TRUE) {
					$zip->extractTo(dirname(__FILE__) . '/zip/');
					$zip->close();
				} else {
					$zip->close();
					throw new Exception('ZIP error.');
				}
				$log .= 'Done: ' . Date('Y-m-d H:i:s') . "\r\n";
			} else {
				throw new Exception('Invalid file.');
			}
		} else {
			throw new Exception('Invalid download URL.');
		}

		update_option('carrental_theme_update_check', '');
		@file_put_contents(dirname(__FILE__) . '/backup/log_' . $time . '.txt', $log);

		// Redirect to rewrite files
		$_SESSION['carrental_flash_msg'] = array('status' => 'success', 'msg' => __('Theme was successfully updated.', 'carrental'));
		Header('Location: ' . CARRENTAL__THEME_URL . '/carrental-theme-updater.php?key=CR157TUXX&time=' . $time);
		Exit;

		return true;
	} catch (Exception $e) {
		exit($e->getMessage());
		return false;
	}
}

function date_format_php($input_format) {
	switch ($input_format) {
		case 'dd.mm.yyyy':
			return 'd.m.Y';
			break;
		case 'mm/dd/yyyy':
			return 'm/d/Y';
			break;
		case 'dd-M-yyyy':
			return 'd-M-Y';
			break;
		case 'M-dd-yyyy':
			return 'M-d-Y';
			break;
		default:
			return 'Y-m-d';
			break;
	}
}

function date_format_js($input_format) {
	switch ($input_format) {
		case 'dd.mm.yyyy':
			return 'dd.mm.yy';
			break;
		case 'mm/dd/yyyy':
			return 'mm/dd/yy';
			break;
		case 'dd-M-yyyy':
			return 'dd-M-yy';
			break;
		case 'M-dd-yyyy':
			return 'M-dd-yy';
			break;
		default:
			return 'yy-mm-dd';
			break;
	}
}

/**
 * Find all yyyy-mm-yy date in input string and replace it with new date format given by format parameter
 * @param type $string
 * @param type $format
 */
function reformat_date_string($string, $format = '', $timeFormat = 24) {
	if ($format == '' && $timeFormat == 24) {
		return $string;
	}
	$dateArray = preg_match_all("/(\d{4}-\d{2}-\d{2})/", $string, $match);
	if (is_array($match)) {
		$match = $match[0];
	}
	foreach ($match as $d) {
		$string = str_replace($d, date(date_format_php($format), strtotime($d)), $string);
	}
	
	if ($timeFormat != 24) {
		$timeArray = preg_match_all("/(\d{2}:\d{2}:\d{2})/", $string, $match);
		if (is_array($match)) {
			$match = $match[0];
		}
		foreach ($match as $d) {
			$string = str_replace($d, carrental_time_format(substr($d, 0, 5), $timeFormat), $string);
		}
	}
	
	$disable_time = get_option('carrental_disable_time');
	if ($disable_time == 'yes') {
		$timeArray = preg_match_all("/(\d{2}:\d{2}:\d{2})/", $string, $match);
		if (is_array($match)) {
			$match = $match[0];
		}
		foreach ($match as $d) {
			$string = str_replace($d, '', $string);
		}
	}
	
	return $string;
}

/**
 * Translate extras to current lang
 * @param type $string
 * @return type
 */
$extrasTranslations = null;

function translate_extras($string) {
	global $extrasTranslations, $wpdb;
	$lang = ((isset($_SESSION['carrental_language']) && !empty($_SESSION['carrental_language'])) ? $_SESSION['carrental_language'] : 'en_GB');
	$lang = strtolower(end(explode('_', $lang)));

	if ($lang == 'gb') {
		return $string;
	}

	if (!$extrasTranslations) {
		$extras = $wpdb->get_results('SELECT * FROM `' . CarRental::$db['extras'] . '`');
		$available_languages = unserialize(get_option('carrental_available_languages'));
		if ($extras) {
			foreach ($extras as $e) {
				$name_translations = unserialize($e->name_translations);
				if (empty($name_translations)) {
					$name_translations = array();
				}
				if ($available_languages && !empty($available_languages)) {
					foreach ($available_languages as $key => $val) {
						if ($val['country-www'] == 'gb') {
							continue;
						}
						if (!isset($extrasTranslations[$val['country-www']])) {
							$extrasTranslations[$val['country-www']] = array();
						}
						if (trim($name_translations[$val['country-www']]) != '') {
							$extrasTranslations[$val['country-www']][$e->name] = $name_translations[$val['country-www']];
						}
					}
				}
			}
		}
	}

	if (isset($extrasTranslations[$lang])) {
		$replaceFrom = array();
		$replaceTo = array();
		foreach ($extrasTranslations[$lang] as $from => $to) {
			$replaceFrom[] = $from;
			$replaceTo[] = $to;
		}
		$replaceFrom[] = 'Value Added Tax';
		$replaceTo[] = CarRental::t('Value Added Tax');
		return str_replace($replaceFrom, $replaceTo, $string);
	}
	return $string;
}

/**
 * Get template file path - search in child theme if exists
 * @param type $name
 * @return type
 */
function get_file_template_path($name) {
	$path = TEMPLATEPATH;
	if (STYLESHEETPATH != TEMPLATEPATH) {
		if (is_file(STYLESHEETPATH . '/' . $name)) {
			$path = STYLESHEETPATH;
		}
	}
	return $path . '/' . $name;
	
}
