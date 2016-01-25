<?php

/*
  Version: 1.0
 */

class CarRental_Client_Area extends CarRental_Admin {

	private static $initiated_admin = false;
	private static $initiated = false;
	public static $db = array();
	private static $title;
	private static $url = 'client-area';

	public static function init_admin() {
		global $wpdb, $carrental_db;

		self::$db = $carrental_db;

		if (!self::$initiated_admin) {
			self::init_admin_hooks();
		}

		$update_db = get_option('carrental_client_area_do_database_update');
		if ($update_db && $update_db == 1) {
			self::plugin_activation('carrental_client_area_do_database_update');
			update_option('carrental_client_area_do_database_update', 0);
			self::set_flash_msg('success', __('Plugin was successfully updated.', 'carrental'));
			Header('Location: ' . self::get_page_url());
			Exit;
		}
	}

	public static function init_public() {
		global $wpdb, $carrental_db;
		self::$db = $carrental_db;

		if (!self::$initiated) {
			self::init_hooks();
		}

		if (isset($_POST['carrental-client-area-sign-in'])) {
			self::sign_in_action();
			exit;
		}

		if (isset($_POST['carrental-client-area-login'])) {
			self::log_in_action();
			exit;
		}

		if (isset($_POST['carrental-client-area-lost-password'])) {
			self::lost_password_action();
			exit;
		}

		if (isset($_POST['carrental-client-area-change-password'])) {
			self::change_password_action();
			exit;
		}

		if (isset($_POST['carrental-client-area-change-email'])) {
			self::change_email_action();
			exit;
		}

		if (isset($_POST['carrental-client-area-change-my-details'])) {
			self::change_my_details_action();
			exit;
		}
	}

	/**
	 * Initializes WordPress hooks
	 */
	public static function init_hooks() {
		add_action('carrental_header_before_currency', array('CarRental_Client_Area', 'fe_header_links'), 11, 1);
		add_action('carrental_services_book_after_driver_details', array('CarRental_Client_Area', 'fe_include_form'), 11, 1);
		add_action('carrental_service_book_js_submit_form', array('CarRental_Client_Area', 'book_js_submit_form'), 1);
		//add_filter( 'plugin_action_links_' . plugin_basename( plugin_dir_path( __FILE__ ) . 'carrental-client-area.php'), array( 'CarRental_Client_Area', 'admin_plugin_settings_link' ) );
		wp_register_style('carrental-client-area.css', CARRENTAL_CLIENT_AREA__PLUGIN_URL . '/assets/style.css', array());
		wp_enqueue_style('carrental-client-area.css');

		wp_register_script('carrental-client-area.js', CARRENTAL_CLIENT_AREA__PLUGIN_URL . 'assets/scripts.js', array('jquery'));
		wp_enqueue_script('carrental-client-area.js');

		add_filter('wp_title', array('CarRental_Client_Area', 'set_title'));

		add_rewrite_tag('%client-area%', '([0-9]+)');
		add_rewrite_rule(self::$url . '/?$', 'index.php?client-area=dashboard', 'top');
		add_rewrite_rule(self::$url . '/?([^/]*)/?', 'index.php?client-area=$matches[1]', 'top');

		flush_rewrite_rules();

		add_action('template_redirect', array('CarRental_Client_Area', 'fe_render_page'));

		add_action('wp_ajax_carrental_client_area_check_user', array('CarRental_Client_Area', 'ajax_check_user'));
		add_action('wp_ajax_nopriv_carrental_client_area_check_user', array('CarRental_Client_Area', 'ajax_check_user'));

		self::$initiated = true;
	}

	/**
	 * Callback for wp_title filter
	 * @param type $title
	 * @return type
	 */
	public function set_title($title) {
		return self::$title;
	}

	/**
	 * Initializes WordPress hooks
	 */
	public static function init_admin_hooks() {
		if (!current_user_can('manage_options')) {
			return;
		}

		add_action('admin_menu', array('CarRental_Client_Area', 'admin_menu'));
		add_action('admin_enqueue_scripts', array('CarRental_Client_Area', 'load_resources'));
		
		//add_filter( 'plugin_action_links_' . plugin_basename( plugin_dir_path( __FILE__ ) . 'carrental-client-area.php'), array( 'CarRental_Client_Area', 'admin_plugin_settings_link' ) );

		self::$initiated_admin = true;
	}

	public static function admin_menu() {

		$hook = add_menu_page(__('Car Rental Client Area', 'carrental'), __('Client Area', 'carrental'), 'manage_options', 'carrental-client-area', array('CarRental_Client_Area', 'display_page'), plugin_dir_url(__FILE__) . '/assets/carrental_menu_icon.png');
	}
	
	public static function display_page() {
		global $wpdb;
		
		if ($_GET['page'] == 'carrental-client-area') {
			if (isset($_GET['user_id'])) {
				$user = $wpdb->get_row($wpdb->prepare('SELECT *, (SELECT COUNT(*) FROM `' . $wpdb->prefix . 'carrental_booking` WHERE `id_user`=`user_id`) as `orders_count` FROM `' . $wpdb->prefix . 'carrental_users` WHERE `user_id` = %d LIMIT 1', $_GET['user_id']), ARRAY_A);
				if (!$user) {
					die('User not found.');
				}
				$sql = 'SELECT b.*,
								MD5(CONCAT(b.`id_order`, "' . CarRental::$hash_salt . '", b.`email`)) as `hash`,
								(SELECT SUM(bp.`price`) FROM `' . CarRental::$db['booking_prices'] . '` bp WHERE bp.`id_booking` = b.`id_booking`) as `total_rental`,
								(SELECT bp.`currency` FROM `' . CarRental::$db['booking_prices'] . '` bp WHERE bp.`id_booking` = b.`id_booking` LIMIT 1) as `currency`
							FROM `' . CarRental::$db['booking'] . '` b
							WHERE `b`.`id_user` = %d
							ORDER BY `b`.`enter_date` ASC';

				$bookings = $wpdb->get_results($wpdb->prepare($sql, $_GET['user_id']));
				self::$title = $user['email'].' ('.$user['first_name'].' '.$user['last_name'].') details';
				add_filter('wp_title', array('CarRental_Client_Area', 'set_title'));
				$tpl = array(
					'user' => $user,
					'bookings' => $bookings,
					'title' => self::$title
					);
				self::view('admin_detail', $tpl);
				exit;
			}
			
			self::$title = 'Car Rental Plugin - Client Area';
			$users = $wpdb->get_results('SELECT *, (SELECT COUNT(*) FROM `' . $wpdb->prefix . 'carrental_booking` WHERE `id_user`=`user_id`) as `orders_count` FROM `' . $wpdb->prefix . 'carrental_users`', ARRAY_A);
			$tpl = array(
				'data' => $users,
				'title' => self::$title
				);
			self::view('admin', $tpl);
		}
	}

	public static function ajax_check_user() {
		global $wpdb;
		$count = $wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM `' . $wpdb->prefix . 'carrental_users` WHERE `email` = %s', $_POST['email']));
		$ret = array();
		if ($count > 0) {
			$ret['status'] = 1;
			$ret['msg'] = CarRental::t('Sorry, this email is already in use. Please login or insert another email.');
		} else {
			$ret['status'] = 0;
		}
		echo json_encode($ret);
		exit;
	}

	public static function fe_render_page() {
		global $wpdb;
		if (get_query_var('client-area')) {
			switch (get_query_var('client-area')) {
				case 'dashboard':
					self::logged_in();
					$bookings = $wpdb->get_results($wpdb->prepare('SELECT * FROM `' . $wpdb->prefix . 'carrental_booking` WHERE `id_user` = %d ORDER BY `created` DESC', $_SESSION['user_id']), ARRAY_A);
					self::$title = CarRental::t('Client Area');
					self::view('my-bookings', array('bookings' => $bookings), true);
					break;
				case 'sign-in':
					self::$title = CarRental::t('Sign up to client area');
					self::view('sign-in', array(), true);
					break;
				case 'account-settings':
					self::logged_in();
					self::$title = CarRental::t('Account settings');
					self::view('account-settings', array(), true);
					break;
				case 'my-account':
					self::logged_in();
					self::$title = CarRental::t('My account');
					$user = $wpdb->get_row($wpdb->prepare('SELECT * FROM `' . $wpdb->prefix . 'carrental_users` WHERE `user_id` = %d', $_SESSION['user_id']), ARRAY_A);
					self::view('my-account', array('user' => $user), true);
					break;
				case 'logout':
					self::logged_in();
					unset($_SESSION['user_id']);
					unset($_SESSION['user_email']);
					$_SESSION['carrental_flash_msg'] = array('status' => 'success', 'msg' => CarRental::t('Login successful.'));
					header('Location: ' . home_url() . '/' . self::$url . '/login');
					Exit;
					break;
				case 'lost-password':
					self::$title = CarRental::t('Lost password');
					self::view('lost-password', array(), true);
					break;
				default: // login
					self::$title = CarRental::t('Log in to your account');
					self::view('login', array(), true);
					break;
			}
			exit;
		}
	}

	/**
	 * Include JS for submit form validation
	 */
	public static function book_js_submit_form() {
		include dirname(__FILE__) . '/assets/validation.js';
	}

	/**
	 * Include form to service booking
	 */
	public static function fe_include_form() {
		include dirname(__FILE__) . '/views/form.php';
	}

	public static function lost_password_action() {
		global $wpdb;
		// test if email is valid and is unique
		if (trim($_POST['email']) == '' || !self::validate_email($_POST['email'])) {
			$_SESSION['carrental_flash_msg'] = array('status' => 'error', 'msg' => CarRental::t('E-mail address is not valid.'));
			header('Location: ' . home_url() . '/' . self::$url . '/lost-password?error=1');
			Exit;
		}

		$user_id = $wpdb->get_var($wpdb->prepare('SELECT `user_id` FROM `' . $wpdb->prefix . 'carrental_users` WHERE `email` = %s', $_POST['email']));
		if (!$user_id) {
			$_SESSION['carrental_flash_msg'] = array('status' => 'error', 'msg' => CarRental::t('Sorry, but this email address is not assigned to any user.'));
			header('Location: ' . home_url() . '/' . self::$url . '/lost-password?error=1');
			Exit;
		}

		$password = self::generate_password();

		$arr = array('password' => sha1($password));
		$wpdb->update($wpdb->prefix . 'carrental_users', $arr, array('user_id' => $user_id));

		$company = unserialize(get_option('carrental_company_info'));
		$email = ((isset($company['email']) && !empty($company['email'])) ? $company['email'] : 'admin@' . $_SERVER['SERVER_NAME']);
		$name = ((isset($company['name']) && !empty($company['name'])) ? $company['name'] : 'Car Rental WP Plugin');
		add_filter('wp_mail_content_type', create_function('', 'return "text/html"; '));
		add_filter('wp_mail_from', create_function('', 'return "' . $email . '"; '));
		add_filter('wp_mail_from_name', create_function('', 'return "' . $name . '"; '));
		$emailBody = '<html><body>' . CarRental::t('Hi,

a password reset was requested for your account on [website]. Your new password is:
[password]

Your [website] team.') . '</body></html>';
		$emailBody = str_replace('[password]', $password, $emailBody);
		$emailBody = str_replace('[email]', $_POST['email'], $emailBody);
		$emailBody = str_replace('[login_url]', home_url() . '/' . self::$url . '/login', $emailBody);
		$emailBody = str_replace('[website]', get_bloginfo('name'), $emailBody);
		$emailBody = nl2br($emailBody);

		$res = wp_mail($_POST['email'], CarRental::t('Your account data'), $emailBody);

		$_SESSION['carrental_flash_msg'] = array('status' => 'success', 'msg' => CarRental::t('Your password has been changed. New password was sent to your email.'));
		header('Location: ' . home_url() . '/' . self::$url . '/lost-password?error=0');
		Exit;
	}

	/**
	 * Test if user is logged in, if not then redirect him
	 * @return boolean
	 */
	public static function logged_in() {
		if (isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0) {
			return true;
		}
		$_SESSION['carrental_flash_msg'] = array('status' => 'error', 'msg' => CarRental::t('Please log in.'));
		header('Location: ' . home_url() . '/' . self::$url . '/login');
		Exit;
	}

	/**
	 * Action for data from sign in form
	 */
	public static function sign_in_action() {
		global $wpdb;

		// test if email is valid and is unique
		if (trim($_POST['email']) == '' || !self::validate_email($_POST['email'])) {
			$_SESSION['carrental_flash_msg'] = array('status' => 'error', 'msg' => CarRental::t('E-mail address is not valid.'));
			header('Location: ' . home_url() . '/' . self::$url . '/sign-in?error=1');
			Exit;
		}

		$count = $wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM `' . $wpdb->prefix . 'carrental_users` WHERE `email` = %s', $_POST['email']));
		if ($count > 0) {
			$_SESSION['carrental_flash_msg'] = array('status' => 'error', 'msg' => CarRental::t('Sorry, but this email address is already registered.'));
			header('Location: ' . home_url() . '/' . self::$url . '/sign-in?error=1');
			Exit;
		}
		$password = self::generate_password();
		$arr = array('email' => $_POST['email'],
			'password' => sha1($password),
			'last_login_ip' => self::ip_check(),
			'registration_date' => current_time('mysql', 1),
		);

		if ($wpdb->insert($wpdb->prefix . 'carrental_users', $arr)) {
			// send email
			$company = unserialize(get_option('carrental_company_info'));
			$email = ((isset($company['email']) && !empty($company['email'])) ? $company['email'] : 'admin@' . $_SERVER['SERVER_NAME']);
			$name = ((isset($company['name']) && !empty($company['name'])) ? $company['name'] : 'Car Rental WP Plugin');
			add_filter('wp_mail_content_type', create_function('', 'return "text/html"; '));
			add_filter('wp_mail_from', create_function('', 'return "' . $email . '"; '));
			add_filter('wp_mail_from_name', create_function('', 'return "' . $name . '"; '));
			$emailBody = '<html><body>' . CarRental::t('Hi,

thank you for registering on our website. Your password is:

[password]

You can log in and change your details at <a href="[login_url]">this link</a>.

Your user name is [email]

Your [website] team.') . '</body></html>';
			$emailBody = str_replace('[password]', $password, $emailBody);
			$emailBody = str_replace('[email]', $_POST['email'], $emailBody);
			$emailBody = str_replace('[login_url]', home_url() . '/' . self::$url . '/login', $emailBody);
			$emailBody = str_replace('[website]', get_bloginfo('name'), $emailBody);
			$emailBody = nl2br($emailBody);

			$res = wp_mail($_POST['email'], CarRental::t('Your account data'), $emailBody);

			$_SESSION['carrental_flash_msg'] = array('status' => 'success', 'msg' => CarRental::t('Your account has been created. Access data has been sent to the email you provided.'));
			header('Location: ' . home_url() . '/' . self::$url . '/sign-in?error=0');
			Exit;
		} else {
			$_SESSION['carrental_flash_msg'] = array('status' => 'error', 'msg' => CarRental::t('The user has not created from because an unknown error.'));
			header('Location: ' . home_url() . '/' . self::$url . '/sign-in?error=1');
			Exit;
		}
	}

	/**
	 * Change password action
	 */
	public static function change_password_action() {
		self::logged_in();
		global $wpdb;

		// validations
		if (strlen($_POST['new_password']) < 5) {
			$_SESSION['carrental_flash_msg'] = array('status' => 'error', 'msg' => CarRental::t('The minimum password length is 5 characters.'));
			header('Location: ' . home_url() . '/' . self::$url . '/account-settings?error=1');
			Exit;
		}

		if ($_POST['new_password'] != $_POST['new_password_confirm']) {
			$_SESSION['carrental_flash_msg'] = array('status' => 'error', 'msg' => CarRental::t('New password authentication disagree.'));
			header('Location: ' . home_url() . '/' . self::$url . '/account-settings?error=1');
			Exit;
		}

		$user = $wpdb->get_row($wpdb->prepare('SELECT * FROM `' . $wpdb->prefix . 'carrental_users` WHERE `user_id` = %d', $_SESSION['user_id']), ARRAY_A);
		if (sha1($_POST['current_password']) != $user['password']) {
			$_SESSION['carrental_flash_msg'] = array('status' => 'error', 'msg' => CarRental::t('Please enter a valid password.'));
			header('Location: ' . home_url() . '/' . self::$url . '/account-settings?error=1');
			Exit;
		}

		$arr = array('password' => sha1($_POST['new_password']));
		$wpdb->update($wpdb->prefix . 'carrental_users', $arr, array('user_id' => $_SESSION['user_id']));

		$_SESSION['carrental_flash_msg'] = array('status' => 'success', 'msg' => CarRental::t('Your password was successfully changed.'));
		header('Location: ' . home_url() . '/' . self::$url . '/account-settings?error=0');
		Exit;
	}

	/**
	 * Change email action
	 */
	public static function change_email_action() {
		self::logged_in();
		global $wpdb;

		// test if email is valid and is unique
		if (trim($_POST['email']) == '' || !self::validate_email($_POST['email'])) {
			$_SESSION['carrental_flash_msg'] = array('status' => 'error', 'msg' => CarRental::t('E-mail address is not valid.'));
			header('Location: ' . home_url() . '/' . self::$url . '/account-settings?error=1');
			Exit;
		}

		$count = $wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM `' . $wpdb->prefix . 'carrental_users` WHERE `email` = %s', $_POST['email']));
		if ($count > 0) {
			$_SESSION['carrental_flash_msg'] = array('status' => 'error', 'msg' => CarRental::t('Sorry, but this email address is already registered.'));
			header('Location: ' . home_url() . '/' . self::$url . '/account-settings?error=1');
			Exit;
		}

		$user = $wpdb->get_row($wpdb->prepare('SELECT * FROM `' . $wpdb->prefix . 'carrental_users` WHERE `user_id` = %d', $_SESSION['user_id']), ARRAY_A);
		if (sha1($_POST['password']) != $user['password']) {
			$_SESSION['carrental_flash_msg'] = array('status' => 'error', 'msg' => CarRental::t('Please enter valid current password.'));
			header('Location: ' . home_url() . '/' . self::$url . '/account-settings?error=1');
			Exit;
		}

		$arr = array('email' => $_POST['email']);
		$wpdb->update($wpdb->prefix . 'carrental_users', $arr, array('user_id' => $_SESSION['user_id']));

		$_SESSION['carrental_flash_msg'] = array('status' => 'success', 'msg' => CarRental::t('Your login email was successfully changed.'));
		header('Location: ' . home_url() . '/' . self::$url . '/account-settings?error=0');
		Exit;
	}

	/**
	 * Save my details
	 */
	public static function change_my_details_action() {
		self::logged_in();
		global $wpdb;
		$arr = array('first_name' => $_POST['first_name'],
			'last_name' => $_POST['last_name'],
			'phone' => $_POST['phone'],
			'street' => $_POST['street'],
			'city' => $_POST['city'],
			'zip' => $_POST['zip'],
			'country' => $_POST['country'],
			'company' => $_POST['company'],
			'id_card' => $_POST['id_card'],
			'license' => $_POST['license'],
			'vat' => $_POST['vat']);
		$wpdb->update($wpdb->prefix . 'carrental_users', $arr, array('user_id' => $_SESSION['user_id']));
		$_SESSION['carrental_flash_msg'] = array('status' => 'success', 'msg' => CarRental::t('Information successfully updated.'));
		header('Location: ' . home_url() . '/' . self::$url . '/my-account?error=0');
		Exit;
	}

	/**
	 * Action for log in to account
	 */
	public static function log_in_action() {
		global $wpdb;

		$user = $wpdb->get_row($wpdb->prepare('SELECT * FROM `' . $wpdb->prefix . 'carrental_users` WHERE `email` = %s', $_POST['email']), ARRAY_A);

		if (!$user) {
			$_SESSION['carrental_flash_msg'] = array('status' => 'error', 'msg' => CarRental::t('Your username or password is invalid.'));
			header('Location: ' . home_url() . '/' . self::$url . '/login?error=1');
			Exit;
		}

		if (sha1($_POST['password']) != $user['password']) {
			$_SESSION['carrental_flash_msg'] = array('status' => 'error', 'msg' => CarRental::t('Your username or password is invalid.'));
			header('Location: ' . home_url() . '/' . self::$url . '/login?error=1');
			Exit;
		}

		$_SESSION['user_id'] = $user['user_id'];
		$_SESSION['user_email'] = $user['email'];
		$arr = array('last_login_ip' => self::ip_check(),
			'last_login' => current_time('mysql', 1));
		$wpdb->update($wpdb->prefix . 'carrental_users', $arr, array('user_id' => $user['user_id']));

		header('Location: ' . home_url() . '/' . self::$url . '/');
		Exit;
	}

	/**
	 * Return user id, if logged in, register user if POST data and return his id
	 */
	public static function return_or_register_user() {
		global $wpdb;

		if (isset($_SESSION['user_id'])) {
			return $_SESSION['user_id'];
		}

		// log in?
		if (isset($_POST['create_account']) && isset($_POST['password']) && $_POST['password'] != '') {
			$user = $wpdb->get_row($wpdb->prepare('SELECT * FROM `' . $wpdb->prefix . 'carrental_users` WHERE `email` = %s', $_POST['email']), ARRAY_A);

			if (!$user) {
				return 0;
			}

			if (sha1($_POST['password']) != $user['password']) {
				return 0;
			}

			$_SESSION['user_id'] = $user['user_id'];
			$arr = array('last_login_ip' => self::ip_check(),
				'last_login' => current_time('mysql', 1));
			$wpdb->update($wpdb->prefix . 'carrental_users', $arr, array('user_id' => $user['user_id']));
			return $user['user_id'];
		}

		if (isset($_POST['create_account'])) {
			// test if email is valid and is unique
			if (trim($_POST['email']) == '' || !self::validate_email($_POST['email'])) {
				return 0;
			}

			$count = $wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM `' . $wpdb->prefix . 'carrental_users` WHERE `email` = %s', $_POST['email']));
			if ($count > 0) {
				return 0;
			}
			$password = self::generate_password();
			$arr = array('email' => $_POST['email'],
				'password' => sha1($password),
				'last_login_ip' => self::ip_check(),
				'registration_date' => current_time('mysql', 1),
				'first_name' => $_POST['first_name'],
				'last_name' => $_POST['last_name'],
				'phone' => $_POST['phone'],
				'street' => $_POST['street'],
				'city' => $_POST['city'],
				'zip' => $_POST['zip'],
				'country' => $_POST['country'],
				'company' => $_POST['company'],
				'vat' => $_POST['vat'],
				'id_card' => $_POST['id_card'],
				'license' => $_POST['license'],
			);

			if ($wpdb->insert($wpdb->prefix . 'carrental_users', $arr)) {
				// send email
				$company = unserialize(get_option('carrental_company_info'));
				$email = ((isset($company['email']) && !empty($company['email'])) ? $company['email'] : 'admin@' . $_SERVER['SERVER_NAME']);
				$name = ((isset($company['name']) && !empty($company['name'])) ? $company['name'] : 'Car Rental WP Plugin');
				add_filter('wp_mail_content_type', create_function('', 'return "text/html"; '));
				add_filter('wp_mail_from', create_function('', 'return "' . $email . '"; '));
				add_filter('wp_mail_from_name', create_function('', 'return "' . $name . '"; '));
				$emailBody = '<html><body>' . CarRental::t('Hi,

	thank you for registering on our website. Your password is:

	[password]

	You can log in and change your details at <a href="[login_url]">this link</a>.

	Your user name is [email]

	Your [website] team.') . '</body></html>';
				$emailBody = str_replace('[password]', $password, $emailBody);
				$emailBody = str_replace('[email]', $_POST['email'], $emailBody);
				$emailBody = str_replace('[login_url]', home_url() . '/' . self::$url . '/login', $emailBody);
				$emailBody = str_replace('[website]', get_bloginfo('name'), $emailBody);
				$emailBody = nl2br($emailBody);

				$res = wp_mail($_POST['email'], CarRental::t('Your account data'), $emailBody);

				return $wpdb->insert_id;
			}
		}
		return 0;
	}

	/**
	 * Generate and return random password
	 */
	private static function generate_password() {
		$chars = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "a", "c", "d", "e", "e", "n", "r", "s", "t", "u", "u", "y", "z", "A", "C", "D", "E", "E", "L", "N", "R", "S", "T", "U", "U", "Y", "Z");
		$max = count($chars);
		$password = '';
		for ($i = 0; $i < 5; $i++) {
			$password .= $chars[mt_rand(0, $max)];
		}
		return $password;
	}

	private static function ip_check() {
		if (getenv('HTTP_CLIENT_IP')) {
			$ip = getenv('HTTP_CLIENT_IP');
		} elseif (getenv('HTTP_X_FORWARDED_FOR')) {
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif (getenv('HTTP_X_FORWARDED')) {
			$ip = getenv('HTTP_X_FORWARDED');
		} elseif (getenv('HTTP_FORWARDED_FOR')) {
			$ip = getenv('HTTP_FORWARDED_FOR');
		} elseif (getenv('HTTP_FORWARDED')) {
			$ip = getenv('HTTP_FORWARDED');
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		if (empty($ip)) {
			$ip = '00.000.000.000';
		}

		return $ip;
	}

	/**
	 * Insert login link, or my account link if already logged in
	 */
	public static function fe_header_links() {
		echo '<div class="carrental-client-area-links">';
		if (isset($_SESSION['user_id'])) {
			// logged in user
			echo '<a href="' . get_site_url() . '/' . self::$url . '/' . '">' . CarRental::t('Client Area') . ' - '.$_SESSION['user_email'].'</a> (<a href="'.get_site_url().'/'.self::$url.'/logout">'.CarRental::t('Logout').'</a>)';
		} else {
			// not logged in
			echo '<a href="' . get_site_url() . '/' . self::$url . '/login' . '">' . CarRental::t('Log in') . '</a>';
		}
		echo '</div>';
	}

	/**
	 * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
	 * @static
	 */
	public static function plugin_activation() {
		global $wpdb, $carrental_db;

		try {

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			$charset_collate = '';

			if (!empty($wpdb->charset)) {
				$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
			}

			if (!empty($wpdb->collate)) {
				$charset_collate .= " COLLATE {$wpdb->collate}";
			}

			$sql = "CREATE TABLE `" . $wpdb->prefix . "carrental_users` (
							  `user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
								`email` VARCHAR(100) NOT NULL,
								`password` VARCHAR(40) NOT NULL,
								`registration_date` DATETIME NULL DEFAULT NULL,
								`last_login` DATETIME NULL DEFAULT NULL,
								`last_login_ip` VARCHAR(16) NOT NULL DEFAULT ''	,
								`first_name` VARCHAR(255) NOT NULL DEFAULT '',
								`last_name` VARCHAR(255) NOT NULL DEFAULT '',
								`phone` VARCHAR(255) NOT NULL DEFAULT '',
								`street` VARCHAR(255) NOT NULL DEFAULT '',
								`city` VARCHAR(255) NOT NULL DEFAULT '',
								`zip` VARCHAR(30) NOT NULL DEFAULT '',
								`country` VARCHAR(2) NOT NULL DEFAULT '',
								`company` VARCHAR(255) NOT NULL DEFAULT '',
								`vat` VARCHAR(50) NOT NULL DEFAULT '',
								`license` VARCHAR(255) NOT NULL DEFAULT '',
								`id_card` VARCHAR(255) NOT NULL DEFAULT '',
								PRIMARY KEY (`user_id`),
								UNIQUE INDEX `email` (`email`)
							)
							ENGINE=InnoDB
							{$charset_collate}";
			dbDelta($sql);

			self::get_plugin_translations();

			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public static function get_plugin_translations() {
		global $wpdb;

		$dirs = array(dirname(__FILE__), dirname(__FILE__) . '/views');

		foreach ($dirs as $dir) {
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
			}
		}
	}

	public static function view($name, array $args = array(), $include_header = false) {

		foreach ($args AS $key => $val) {
			$$key = $val;
		}

		$cr_title = ucfirst(end(explode('-', $name)));

		$file = CARRENTAL_CLIENT_AREA__PLUGIN_DIR . 'views/' . $name . '.php';
		if ($include_header) {
			get_header();
			include get_template_directory() . '/intro.php';
			echo '<section class="content carrental-client-area">	
					<div class="container">';
		}
		include($file);
		if ($include_header) {
			echo '</div></div>';
			get_footer();
		}
	}

	/**
	 * Return current user info as array
	 */
	public static function get_current_user() {
		global $wpdb;
		if (isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0) {
			return $wpdb->get_row($wpdb->prepare('SELECT * FROM `' . $wpdb->prefix . 'carrental_users` WHERE `user_id` = %d', $_SESSION['user_id']), ARRAY_A);
		}
		return array();
	}

	public static function load_resources() {
		global $hook_suffix;

		$arr = array('carrental-client-area');

		$exp = explode('_', $hook_suffix);
		$page = end($exp);

		if (in_array($page, $arr)) {


			wp_register_style('bootstrap.css', CARRENTAL_CLIENT_AREA__PLUGIN_URL . 'assets/bootstrap.css', array());
			wp_enqueue_style('bootstrap.css');

			wp_register_style('carrental-client-area-admin.css', CARRENTAL_CLIENT_AREA__PLUGIN_URL . 'assets/admin.css', array());
			wp_enqueue_style('carrental-client-area-admin.css');

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

			wp_register_script('bootstrap.min.js', CARRENTAL_CLIENT_AREA__PLUGIN_URL . 'assets/bootstrap.min.js', array());
			wp_enqueue_script('bootstrap.min.js');

			wp_register_style('jquery.dataTables.css', '//cdn.datatables.net/1.10.0/css/jquery.dataTables.css', array());
			wp_enqueue_style('jquery.dataTables.css');

			wp_register_script('jquery.dataTables.js', '//cdn.datatables.net/1.10.0/js/jquery.dataTables.js', array());
			wp_enqueue_script('jquery.dataTables.js');
		}
	}

	public function auto_check_plugin_update() {

		if (isset($_SESSION['carrental_client_area_auto_check_update']) && ($_SESSION['carrental_client_area_auto_check_update'] + 86400) > time()) {
			return true;
		}

		$check = unserialize(get_option('carrental_client_area_update_check'));
		if (isset($check['last']) && strtotime($check['last']) != false && (strtotime($check['last']) + 86400) < time()) {
			self::check_plugin_update();
		}
		$_SESSION['carrental_client_area_auto_check_update'] = time();

		return true;
	}

	public function check_plugin_update() {
		global $wpdb;

		try {

			$apikey = unserialize(get_option('carrental_api_key'));

			if (empty($apikey) || empty($apikey['api_key'])) {
				throw new Exception('Invalid API key.');
			}

			if (defined("CARRENTAL_CLIENT_AREA_UPDATE_URL")) {
				if (CARRENTAL_CLIENT_AREA_UPDATE_URL != '') {
					$url = CARRENTAL_CLIENT_AREA_UPDATE_URL . '&api=' . $apikey['api_key'] . '&url=' . urlencode($_SERVER['SERVER_NAME']) . '&server=' . urlencode($_SERVER['SERVER_NAME']) . '&version=' . urlencode(CARRENTAL_CLIENT_AREA_VERSION);
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

				if (defined("CARRENTAL_CLIENT_AREA_VERSION")) {
					$current_version = CARRENTAL_CLIENT_AREA_VERSION;
					if ($current_version != $data->version) {
						$check['new_version'] = $data->version;
						$check['new_version_date'] = $data->date;
						$check['new_version_url'] = $data->url;
						$check['update_available'] = true;
					}
				}

				update_option('carrental_client_area_update_check', serialize($check));
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
			if (!file_exists(dirname(__FILE__) . '/backup/')) {
				mkdir(dirname(__FILE__) . '/backup/', 0777);
			}
			if (!file_exists(dirname(__FILE__) . '/assets/swf/')) {
				mkdir(dirname(__FILE__) . '/assets/swf/', 0777);
			}

			$backupFolder = dirname(__FILE__) . '/';
			$time = time();
			$finalZip = dirname(__FILE__) . '/backup/backup_' . $time . '.zip';
			$exclude = array('carrental-client-area/backup', 'carrental-client-area/zip', 'carrental-client-area/download');
			$eza = new ExtZipArchive;
			$res = $eza->open($finalZip, ZipArchive::CREATE);

			if ($res === TRUE) {
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
			$check = unserialize(get_option('carrental_client_area_update_check'));
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
			update_option('carrental_client_area_do_database_update', 1);
			$log .= 'Done: ' . Date('Y-m-d H:i:s') . "\r\n";

			update_option('carrental_client_area_update_check', '');

			@file_put_contents(dirname(__FILE__) . '/backup/log_' . $time . '.txt', $log);

			self::get_plugin_translations();

			// Redirect to rewrite files
			self::set_flash_msg('success', __('Plugin was successfully updated.', 'carrental'));
			Header('Location: ' . CARRENTAL_CLIENT_AREA__PLUGIN_URL . 'carrental-client-area-plugin-updater.php?key=e7c4c0ce5&time=' . $time);
			Exit;

			return true;
		} catch (Exception $e) {
			exit($e->getMessage());
			return false;
		}
	}

	/**
	 * Test if input email is valid email address
	 * @param type $email
	 * @return boolean
	 */
	private static function validate_email($email) {
		if (trim($email) == '')
			return false;
		if (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", $email)) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function get_page_url($page = 'carrental-client-area') {
		return add_query_arg(array('page' => $page), admin_url('admin.php'));
	}

	public function rrmdir($dir) {
		foreach (glob($dir . '/*') as $file) {
			if (is_dir($file))
				self::rrmdir($file);
			else
				unlink($file);
		} rmdir($dir);
	}

}
