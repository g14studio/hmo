<?php

/*
  Version: 1.1
 */

class CarRental_Calendar extends CarRental_Admin {

	private static $initiated = false;
	public static $db = array();

	public static function init() {
		global $wpdb, $carrental_db;

		self::$db = $carrental_db;

		if (!self::$initiated) {
			self::init_hooks();
		}
		
		if (isset($_POST['calendar_check_plugin_update'])) {
			$msg = self::check_plugin_update();
			if ($msg === true) {
				self::set_flash_msg('success', __('Plugin updates was successfully checked.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-calendar')); Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__('Plugin updates was not checked due to error (%s).', 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-calendar')); Exit;
			}
		}
		
		if (isset($_POST['calendar_plugin_update'])) {
			$msg = self::process_plugin_update();
			if ($msg === true) {
				self::set_flash_msg('success', __('Plugin was successfully updated.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-calendar')); Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__('Plugin was not updated due to error (%s).', 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-calendar')); Exit;
			}
		}
	}

	public static function plugin_activation() {
		global $wpdb, $carrental_db;
		try {
			$wpdb->query("ALTER TABLE `" . $carrental_db['booking'] . "` ADD COLUMN `internal_note` VARCHAR(255) NULL DEFAULT '' AFTER `comment`");
		} catch (Exception $ex) {
			return $e->getMessage();
		}
	}

	/**
	 * Initializes WordPress hooks
	 */
	public static function init_hooks() {
		if (!current_user_can('manage_options')) {
			return;
		}
		
		add_action('admin_menu', array('CarRental_Calendar', 'admin_menu'));
		add_action('admin_enqueue_scripts', array('CarRental_Calendar', 'load_resources'));
		add_filter('plugin_action_links_' . plugin_basename(plugin_dir_path(__FILE__) . 'carrental-calendar.php'), array('CarRental_Calendar', 'admin_plugin_settings_link'));
		add_action('wp_ajax_save_note', array('CarRental_Calendar', 'ajax_save_note'));
		add_action('wp_ajax_show_note', array('CarRental_Calendar', 'ajax_show_note'));
		self::$initiated = true;
	}
	
	public function admin_error_notice() {
		?>
		<div class="error">
	        <p><?php _e( 'Ecalypse Car Rental plugin is not installed or is deactivated.', 'my-text-domain' ); ?></p>
		</div>
    <?php
	
	}

	public static function admin_menu() {

		$hook = add_menu_page(__('Car Rental Calendar', 'carrental'), __('Calendar', 'carrental'), 'manage_options', 'carrental-calendar', array('CarRental_Calendar', 'display_page'), plugin_dir_url(__FILE__) . '/assets/carrental_menu_icon.png');
	}

	public static function admin_plugin_settings_link($links) {
		$settings_link = '<a href="' . self::get_page_url() . '">' . __('Settings', 'carrental') . '</a>';
		array_unshift($links, $settings_link);
		return $links;
	}

	public static function ajax_save_note() {
		global $wpdb, $carrental_db;
		$wpdb->query($wpdb->prepare('UPDATE '.$carrental_db['booking'].' SET `internal_note` = %s WHERE `id_booking` = %d LIMIT 1', $_POST['note'], $_POST['id']));
	}
	
	public static function ajax_show_note() {
		if ($_POST['show'] == 1) {
			update_option('carrental_calendar_show_note', 1);
		} else {
			update_option('carrental_calendar_show_note', 0);
		}
		echo 1;
		exit;
	}

	public static function display_page() {
		global $wpdb;
		
		if (!class_exists('CarRental')) {
			self::admin_error_notice();
			return;
		}
		self::auto_check_plugin_update();
		
		$tpl = array();
		$date_from = strtotime('-7 day', strtotime('midnight'));
		if (isset($_GET['date_from']) && strtotime($_GET['date_from']) !== false) {
			$date_from = strtotime($_GET['date_from']);
		}
		$date_to = strtotime('+21 day', strtotime('midnight'));
		if (isset($_GET['date_to']) && strtotime($_GET['date_to']) !== false) {
			$date_to = strtotime($_GET['date_to']);
		}
		$date_from_timestamp = $date_from;
		$date_to_timestamp = $date_to;

		$date_from = date('Y-m-d', $date_from);
		$date_to = date('Y-m-d', $date_to);

		$data = $wpdb->get_results($wpdb->prepare('SELECT *, MD5(CONCAT(b.`id_order`, "' . CarRental::$hash_salt . '", b.`email`)) as `hash` FROM `' . CarRental::$db['booking'] . '` as b WHERE `deleted` IS NULL AND ((DATE(`enter_date`) >= %s && DATE(`enter_date`) <= %s) OR (DATE(`return_date`) >= %s && DATE(`return_date`) <= %s)) ORDER BY `enter_date` ASC', $date_from, $date_to, $date_from, $date_to), ARRAY_A);
		$idCars = array();
		foreach ($data as $book) {
			$idCars[$book['vehicle_id']] = $book['vehicle_id'];
		}
		
		$tpl['date_from_timestamp'] = $date_from_timestamp;
		$tpl['date_to_timestamp'] = $date_to_timestamp;

		$days = array();
		$curr_date = $date_from_timestamp;
		while (true) {
			if ($curr_date > $date_to_timestamp) {
				break;
			}
			$days[] = $curr_date;
			$curr_date = strtotime('+1 day', $curr_date);
		}
		$tpl['days'] = $days;

		if (empty($idCars)) {
			$tpl['cars'] = false;
			self::view('carrental-calendar', $tpl);
			return;
		}

		// get all vehicles
		$vehicles = $wpdb->get_results('SELECT * FROM `' . CarRental::$db['fleet'] . '` WHERE `id_fleet` IN (' . implode(',', $idCars) . ') ORDER BY `id_fleet` ASC', ARRAY_A);
		foreach ($vehicles as $vId => $vehicle) {
			$vehicles[$vId]['books'] = array();
			foreach ($data as $book) {
				if ($book['vehicle_id'] == $vehicle['id_fleet']) {
					$vehicles[$vId]['books'][] = $book;
				}
			}
		}
		$tpl['vehicles'] = $vehicles;
		
		self::view('carrental-calendar', $tpl);
	}

	public static function get_page_url($page = 'carrental') {

		$arr = array('carrental-calendar');

		if (in_array($page, $arr)) {
			$url = add_query_arg(array('page' => $page), admin_url('admin.php'));
		} else {
			$url = add_query_arg(array('page' => 'carrental'), admin_url('admin.php'));
		}

		return $url;
	}

	public static function view($name, array $args = array()) {

		foreach ($args AS $key => $val) {
			$$key = $val;
		}

		$cr_title = ucfirst(end(explode('-', $name)));

		$file = CARRENTAL_CALENDAR__PLUGIN_DIR . 'views/' . $name . '.php';
		include($file);
	}

	public static function load_resources() {
		global $hook_suffix;

		$arr = array('carrental-calendar');

		$exp = explode('_', $hook_suffix);
		$page = end($exp);

		if (in_array($page, $arr)) {
			
			wp_register_style( 'bootstrap.css', CARRENTAL_CALENDAR__PLUGIN_URL . 'assets/bootstrap.css', array() );
			wp_enqueue_style( 'bootstrap.css');

			wp_register_style('jquery-ui.css', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.css', array());
			wp_enqueue_style('jquery-ui.css');

			wp_deregister_script('jquery');
			wp_register_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js', array());
			wp_enqueue_script('jquery');

			wp_deregister_script('jqueryui');
			wp_register_script('jqueryui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js', array());
			wp_enqueue_script('jqueryui');

			wp_register_style('carrental-calendar.css', CARRENTAL_CALENDAR__PLUGIN_URL . 'assets/style.css', array());
			wp_enqueue_style('carrental-calendar.css');
			
			wp_register_script('dragscroll', CARRENTAL_CALENDAR__PLUGIN_URL . 'assets/dragscroll.js', array());
			wp_enqueue_script('dragscroll');
		}
	}
	
	public function auto_check_plugin_update() {
		
		if (isset($_SESSION['carrental_calendar_auto_check_update']) && ($_SESSION['carrental_calendar_auto_check_update'] + 86400) > time()) {
			return true;
		}
		
		$check = unserialize(get_option('carrental_calendar_update_check'));
		if (isset($check['last']) && strtotime($check['last']) != false && (strtotime($check['last']) + 86400) < time()) {
			self::check_plugin_update();
		}
		$_SESSION['carrental_calendar_auto_check_update'] = time();
		
		return true;
		
	}
	
	public function check_plugin_update() {
		global $wpdb;
		
		try {
			
			$apikey = unserialize(get_option('carrental_api_key'));
			
			if (empty($apikey) || empty($apikey['api_key'])) {
				throw new Exception('Invalid API key.');
			}
			
			if (defined("CARRENTAL_CALENDAR_UPDATE_URL")) {
				if (CARRENTAL_CALENDAR_UPDATE_URL != '') {
					$url = CARRENTAL_CALENDAR_UPDATE_URL . '&api=' . $apikey['api_key'] . '&url=' . urlencode($_SERVER['SERVER_NAME']) . '&server=' . urlencode($_SERVER['SERVER_NAME']). '&version=' . urlencode(CARRENTAL_CALENDAR_VERSION);
				} else {
					throw new Exception('Undefined update URL.');
				}
			} else {
				throw new Exception('Undefined update URL.');
			}
			
			$data = json_decode(@file_get_contents($url));
			
			if ($data && !empty($data)) {
				
				$check = array();
				$check['last'] = Date('Y-m-d H:i:s');
				$check['update_available'] = false;
				
				if (isset($data->api_expiration)) {
					update_option('carrental_api_key_expiration', $data->api_expiration);
				}
				
				if (defined("CARRENTAL_CALENDAR_VERSION")) {
					$current_version = CARRENTAL_CALENDAR_VERSION;
					if ($current_version != $data->version) {
						$check['new_version'] = $data->version;
						$check['new_version_date'] = $data->date;
						$check['new_version_url'] = $data->url;
						$check['update_available'] = true;
					}
				}
				
				update_option('carrental_calendar_update_check', serialize($check));
				
			} else {
				throw new Exception('No data from update URL.');
			}
			
			return true;
		} catch (Exception $e) {
	  	return $e->getMessage();
	  }
	}
	
	
	public function process_plugin_update() {
		try {
    	
    	$log = 'Plugin update: ' . Date('Y-m-d H:i:s') . "\r\n";
    	set_time_limit(0);
    	
			// Backup files
				$log .= 'Backuping files...' . "\r\n";
    		if (!file_exists(dirname(__FILE__) . '/backup/')) { mkdir(dirname(__FILE__) . '/backup/', 0777); }
				if (!file_exists(dirname(__FILE__) . '/assets/swf/')) { mkdir(dirname(__FILE__) . '/assets/swf/', 0777); }
				
		    $backupFolder = dirname(__FILE__) . '/';
		    $time = time();
				$finalZip = dirname(__FILE__) . '/backup/backup_' . $time . '.zip';
				$exclude = array('carrental-calendar/backup', 'carrental-calendar/zip', 'carrental-calendar/download');
				$eza = new ExtZipArchive;
				$res = $eza->open($finalZip, ZipArchive::CREATE);
				 
				if($res === TRUE) {
				  $eza->addDir($backupFolder, basename($backupFolder), $exclude);
				  $eza->close();
				} else {
				  throw new Exception('Could not create backup.');
				}
				$log .= 'Done: ' . Date('Y-m-d H:i:s') . "\r\n";
    	
			// Backup DB
				$log .= 'Backuping database...' . "\r\n";
				$_POST['export_structure'] = $_POST['export_data'] = 1; 
				file_put_contents(dirname(__FILE__) . '/backup/sql_' . $time . '.sql', parent::export_database());
				$log .= 'Done: ' . Date('Y-m-d H:i:s') . "\r\n";
			
			// Download new files and unzip
				$log .= 'Downloading...' . "\r\n";
				$check = unserialize(get_option('carrental_calendar_update_check'));
				if (isset($check['new_version_url']) && !empty($check['new_version_url'])) {
					$zip = file_get_contents($check['new_version_url']);
					$log .= 'Done: ' . Date('Y-m-d H:i:s') . "\r\n";
					if ($zip && !empty($zip)) {
						
						if (!file_exists(dirname(__FILE__) . '/download/')) { mkdir(dirname(__FILE__) . '/download/', 0777); }
						if (!file_exists(dirname(__FILE__) . '/zip/')) { mkdir(dirname(__FILE__) . '/zip/', 0777); }
						
						$tempFileName = dirname(__FILE__) . '/download/plugin_update.zip';
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
			
			// Update DB
				$log .= 'Updating database...' . "\r\n";
				update_option('carrental_calendar_do_database_update', 1);
				$log .= 'Done: ' . Date('Y-m-d H:i:s') . "\r\n";
				
				update_option('carrental_calendar_update_check', '');
				
				@file_put_contents(dirname(__FILE__) . '/backup/log_' . $time . '.txt', $log);
				
			// Redirect to rewrite files
				self::set_flash_msg('success', __('Plugin was successfully updated.', 'carrental'));
				Header('Location: ' . CARRENTAL_CALENDAR__PLUGIN_URL . 'carrental-calendar-plugin-updater.php?key=cfc4e5c2ac&time=' . $time); Exit;
			
			return true;
			
	  } catch (Exception $e) {
	  	exit($e->getMessage());
	  	return false;
	  }
	}
	
	public function rrmdir($dir) {
	  foreach(glob($dir . '/*') as $file) { 
	    if(is_dir($file)) self::rrmdir($file); else unlink($file); 
	  } rmdir($dir); 
	}

}
