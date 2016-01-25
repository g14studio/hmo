<?php

//define( 'SHORTINIT', true );
require_once( dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php' );

/**
 *  PayPal IPN Listener
 *
 *  A class to listen for and handle Instant Payment Notifications (IPN) from 
 *  the PayPal server.
 *
 *  https://github.com/Quixotix/PHP-PayPal-IPN
 *
 *  @package    PHP-PayPal-IPN
 *  @author     Micah Carrick
 *  @copyright  (c) 2012 - Micah Carrick
 *  @version    2.1.0
 */
class IpnListener {

	/**
	 *  If true, the recommended cURL PHP library is used to send the post back 
	 *  to PayPal. If flase then fsockopen() is used. Default true.
	 *
	 *  @var boolean
	 */
	public $use_curl = true;

	/**
	 *  If true, explicitly sets cURL to use SSL version 3. Use this if cURL
	 *  is compiled with GnuTLS SSL.
	 *
	 *  @var boolean
	 */
	public $force_ssl_v3 = false;

	/**
	 *  If true, cURL will use the CURLOPT_FOLLOWLOCATION to follow any 
	 *  "Location: ..." headers in the response.
	 *
	 *  @var boolean
	 */
	public $follow_location = false;

	/**
	 *  If true, an SSL secure connection (port 443) is used for the post back 
	 *  as recommended by PayPal. If false, a standard HTTP (port 80) connection
	 *  is used. Default true.
	 *
	 *  @var boolean
	 */
	public $use_ssl = true;

	/**
	 *  If true, the paypal sandbox URI www.sandbox.paypal.com is used for the
	 *  post back. If false, the live URI www.paypal.com is used. Default false.
	 *
	 *  @var boolean
	 */
	public $use_sandbox = false;

	/**
	 *  The amount of time, in seconds, to wait for the PayPal server to respond
	 *  before timing out. Default 30 seconds.
	 *
	 *  @var int
	 */
	public $timeout = 30;
	private $post_data = array();
	private $post_uri = '';
	private $response_status = '';
	private $response = '';

	const PAYPAL_HOST = 'www.paypal.com';
	const SANDBOX_HOST = 'www.sandbox.paypal.com';

	/**
	 *  Post Back Using cURL
	 *
	 *  Sends the post back to PayPal using the cURL library. Called by
	 *  the processIpn() method if the use_curl property is true. Throws an
	 *  exception if the post fails. Populates the response, response_status,
	 *  and post_uri properties on success.
	 *
	 *  @param  string  The post data as a URL encoded string
	 */
	protected function curlPost($encoded_data) {

		if ($this->use_ssl) {
			$uri = 'https://' . $this->getPaypalHost() . '/cgi-bin/webscr';
			$this->post_uri = $uri;
		} else {
			$uri = 'http://' . $this->getPaypalHost() . '/cgi-bin/webscr';
			$this->post_uri = $uri;
		}

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_SSLVERSION, 4);
		curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . "/api_cert_chain.crt");
		curl_setopt($ch, CURLOPT_URL, $uri);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded_data);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $this->follow_location);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true);

		if ($this->force_ssl_v3) {
			curl_setopt($ch, CURLOPT_SSLVERSION, 3);
		}

		$this->response = curl_exec($ch);
		$this->response_status = strval(curl_getinfo($ch, CURLINFO_HTTP_CODE));

		if ($this->response === false || $this->response_status == '0') {
			$errno = curl_errno($ch);
			$errstr = curl_error($ch);
			throw new Exception("cURL error: [$errno] $errstr");
		}
	}

	/**
	 *  Post Back Using fsockopen()
	 *
	 *  Sends the post back to PayPal using the fsockopen() function. Called by
	 *  the processIpn() method if the use_curl property is false. Throws an
	 *  exception if the post fails. Populates the response, response_status,
	 *  and post_uri properties on success.
	 *
	 *  @param  string  The post data as a URL encoded string
	 */
	protected function fsockPost($encoded_data) {

		if ($this->use_ssl) {
			$uri = 'ssl://' . $this->getPaypalHost();
			$port = '443';
			$this->post_uri = $uri . '/cgi-bin/webscr';
		} else {
			$uri = $this->getPaypalHost(); // no "http://" in call to fsockopen()
			$port = '80';
			$this->post_uri = 'http://' . $uri . '/cgi-bin/webscr';
		}

		$fp = fsockopen($uri, $port, $errno, $errstr, $this->timeout);

		if (!$fp) {
			// fsockopen error
			throw new Exception("fsockopen error: [$errno] $errstr");
		}

		$header = "POST /cgi-bin/webscr HTTP/1.1\r\n";
		$header .= "Host: " . $this->getPaypalHost() . "\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($encoded_data) . "\r\n";
		$header .= "Connection: Close\r\n\r\n";

		fputs($fp, $header . $encoded_data . "\r\n\r\n");

		while (!feof($fp)) {
			if (empty($this->response)) {
				// extract HTTP status from first line
				$this->response .= $status = fgets($fp, 1024);
				$this->response_status = trim(substr($status, 9, 4));
			} else {
				$this->response .= fgets($fp, 1024);
			}
		}

		fclose($fp);
	}

	private function getPaypalHost() {
		if ($this->use_sandbox)
			return self::SANDBOX_HOST;
		else
			return self::PAYPAL_HOST;
	}

	/**
	 *  Get POST URI
	 *
	 *  Returns the URI that was used to send the post back to PayPal. This can
	 *  be useful for troubleshooting connection problems. The default URI
	 *  would be "ssl://www.sandbox.paypal.com:443/cgi-bin/webscr"
	 *
	 *  @return string
	 */
	public function getPostUri() {
		return $this->post_uri;
	}

	/**
	 *  Get Response
	 *
	 *  Returns the entire response from PayPal as a string including all the
	 *  HTTP headers.
	 *
	 *  @return string
	 */
	public function getResponse() {
		return $this->response;
	}

	/**
	 *  Get Response Status
	 *
	 *  Returns the HTTP response status code from PayPal. This should be "200"
	 *  if the post back was successful. 
	 *
	 *  @return string
	 */
	public function getResponseStatus() {
		return $this->response_status;
	}

	/**
	 *  Get Text Report
	 *
	 *  Returns a report of the IPN transaction in plain text format. This is
	 *  useful in emails to order processors and system administrators. Override
	 *  this method in your own class to customize the report.
	 *
	 *  @return string
	 */
	public function getTextReport() {

		$r = '';

		// date and POST url
		for ($i = 0; $i < 80; $i++) {
			$r .= '-';
		}
		$r .= "\n[" . date('m/d/Y g:i A') . '] - ' . $this->getPostUri();
		if ($this->use_curl)
			$r .= " (curl)\n";
		else
			$r .= " (fsockopen)\n";

		// HTTP Response
		for ($i = 0; $i < 80; $i++) {
			$r .= '-';
		}
		$r .= "\n{$this->getResponse()}\n";

		// POST vars
		for ($i = 0; $i < 80; $i++) {
			$r .= '-';
		}
		$r .= "\n";

		foreach ($this->post_data as $key => $value) {
			$r .= str_pad($key, 25) . "$value\n";
		}
		$r .= "\n\n";

		return $r;
	}

	/**
	 *  Process IPN
	 *
	 *  Handles the IPN post back to PayPal and parsing the response. Call this
	 *  method from your IPN listener script. Returns true if the response came
	 *  back as "VERIFIED", false if the response came back "INVALID", and 
	 *  throws an exception if there is an error.
	 *
	 *  @param array
	 *
	 *  @return boolean
	 */
	public function processIpn($post_data = null) {

		$encoded_data = 'cmd=_notify-validate';

		if ($post_data === null) {
			// use raw POST data 
			if (!empty($_POST)) {
				$this->post_data = $_POST;
				$encoded_data .= '&' . file_get_contents('php://input');
			} else {
				throw new Exception("No POST data found.");
			}
		} else {
			// use provided data array
			$this->post_data = $post_data;

			foreach ($this->post_data as $key => $value) {
				$encoded_data .= "&$key=" . urlencode($value);
			}
		}

		if ($this->use_curl)
			$this->curlPost($encoded_data);
		else
			$this->fsockPost($encoded_data);

		if (strpos($this->response_status, '200') === false) {
			throw new Exception("Invalid response status: " . $this->response_status);
		}

		if (strpos($this->response, "VERIFIED") !== false) {
			return true;
		} elseif (strpos($this->response, "INVALID") !== false) {
			return false;
		} else {
			throw new Exception("Unexpected response from PayPal.");
		}
	}

	/**
	 *  Require Post Method
	 *
	 *  Throws an exception and sets a HTTP 405 response header if the request
	 *  method was not POST. 
	 */
	public function requirePostMethod() {
		// require POST requests
		if ($_SERVER['REQUEST_METHOD'] && $_SERVER['REQUEST_METHOD'] != 'POST') {
			header('Allow: POST', true, 405);
			throw new Exception("Invalid HTTP request method.");
		}
	}

}

$listener = new IpnListener();
$listener->use_sandbox = false;

try {
	$verified = $listener->processIpn();
} catch (Exception $e) {
	// fatal error trying to process IPN.

	file_put_contents('paypal_ipn.log', $e. "\n\n-------------------------------------\n\n", FILE_APPEND);
	exit(0);
}

file_put_contents('paypal_ipn.log', $listener->getTextReport(), FILE_APPEND);

if ($verified) {
	// IPN response was "VERIFIED"
	$wpdb->query($wpdb->prepare('UPDATE ' . CarRental::$db['booking'] . ' SET `paid_online` = ' . ((float) $_POST['mc_gross']) . ', `status` = 1 WHERE MD5(CONCAT(`id_order`, %s, `email`)) = %s', CarRental::$hash_salt, $_POST['item_number']));
	file_put_contents('paypal_ipn.log', '***VERIFIED*** - ' . $wpdb->prepare('UPDATE ' . CarRental::$db['booking'] . ' SET `paid_online` = ' . ((float) $_POST['mc_gross']) . ', `status` = 1 WHERE MD5(CONCAT(`id_order`, %s, `email`)) = %s', CarRental::$hash_salt, $_POST['item_number']), FILE_APPEND);

	// Send e-mail
	if (isset($_POST['custom']) && !empty($_POST['custom'])) {
		$emailBody = get_option('carrental_reservation_email_' . $_POST['custom']);
		if ($emailBody == '') {
			$emailBody = get_option('carrental_reservation_email_en_GB');
		}
		
		$emailSubject = get_option('carrental_reservation_email_subject_' . $_POST['custom']);
		if ($emailSubject == '') {
			$emailSubject = get_option('carrental_reservation_email_subject_en_GB');
		}
	} else {
		$emailBody = get_option('carrental_reservation_email_en_GB');
		$emailSubject = get_option('carrental_reservation_email_subject_en_GB');
	}

	if (!empty($emailBody)) {
		$data = $wpdb->get_row($wpdb->prepare('SELECT * FROM `' . CarRental::$db['booking'] . '` WHERE MD5(CONCAT(`id_order`, %s, `email`)) = %s LIMIT 1', CarRental::$hash_salt, $_POST['item_number']), ARRAY_A);
		
		if ($data) {
			$theme_options = unserialize(get_option('carrental_theme_options'));
			if (isset($theme_options['date_format'])) {
				// reformat dates
				$data['enter_date'] = date(CarRental::date_format_php($theme_options['date_format'], 'auto'), strtotime($data['enter_date']));
				$data['return_date'] = date(CarRental::date_format_php($theme_options['date_format'], 'auto'), strtotime($data['return_date']));
			}
			
			$emailBody = str_replace('[CustomerName]', $data['first_name'] . " " . $data['last_name'], $emailBody);
			$emailBody = str_replace('[ReservationDetails]', $data['vehicle']. ', ' . $data['enter_date'] . ' (' . $data['enter_loc'] . ') - ' . $data['return_date'] . ' (' . $data['return_loc'] . ')', $emailBody);
			$emailBody = str_replace('[ReservationNumber]', $data['id_order'], $emailBody);
			$emailBody = str_replace('[ReservationLink]', home_url() . '?page=carrental&summary=' . $_POST['item_number'], $emailBody);
			$emailBody = str_replace('[ReservationLinkStart]', '<a href="' . home_url() . '?page=carrental&summary=' . $_POST['item_number'] . '">', $emailBody);
			$emailBody = str_replace('[ReservationLinkEnd]', '</a>', $emailBody);
			$emailBody = str_replace('[customer_comment]', $data['comment'], $emailBody);
			$emailBody = '<html><body>' . $emailBody . '</body></html>';
			$emailBody = nl2br($emailBody);

			$recipient = $data['email'];
			if ($emailSubject == '') {
				$subject =  "Reservation confirmation #" . $data['id_order'];
			} else {
				$subject = $emailSubject;
				$subject = str_replace('[CustomerName]', $data['first_name'] . " " . $data['last_name'], $subject);
				$subject = str_replace('[ReservationDetails]', $data['vehicle'] . ', ' . $data['enter_date'] . ' (' . $data['enter_loc'] . ') - ' . $data['return_date'] . ' (' . $data['return_loc'] . ')', $subject);
				$subject = str_replace('[Car]', $data['vehicle'], $subject);
				$subject = str_replace('[pickupdate]', $data['enter_date'], $subject);
				$subject = str_replace('[dropoffdate]', $data['return_date'], $subject);
				$subject = str_replace('[pickup_location]', $data['enter_loc'], $subject);
				$subject = str_replace('[dropoff_location]', $data['return_loc'], $subject);
				$subject = str_replace('[ReservationNumber]', $data['id_order'], $subject);
				$subject = str_replace('[ReservationLink]', home_url() . '?page=carrental&summary=' . $_POST['item_number'], $subject);
				$subject = str_replace('[ReservationLinkStart]', '<a href="' . home_url() . '?page=carrental&summary=' . $_POST['item_number'] . '">', $subject);
				$subject = str_replace('[ReservationLinkEnd]', '</a>', $subject);
			}

			$company = unserialize(get_option('carrental_company_info'));

			$email = ((isset($company['email']) && !empty($company['email'])) ? $company['email'] : 'admin@' . $_SERVER['SERVER_NAME']);
			$name = ((isset($company['name']) && !empty($company['name'])) ? $company['name'] : 'Car Rental WP Plugin');

			add_filter('wp_mail_content_type', create_function('', 'return "text/html"; '));
			add_filter('wp_mail_from', create_function('', 'return "' . $email . '"; '));
			add_filter('wp_mail_from_name', create_function('', 'return "' . $name . '"; '));

			$book_send_email = get_option('carrental_book_send_email');
			if (empty($book_send_email)) {
				$book_send_email = array('client' => 1, 'admin' => 1);
			} else {
				$book_send_email = unserialize($book_send_email);
				if (!is_array($book_send_email)) {
					$book_send_email = array();
				}
				if (!isset($book_send_email['client'])) {
					$book_send_email['client'] = 1;
				}
				if (!isset($book_send_email['admin'])) {
					$book_send_email['admin'] = 1;
				}
			}
			
			$attachments = array();
			$attachments = apply_filters('carrental_email_attachments', $attachments, $data['id_order']);

			if ($book_send_email['client'] == 1) {
				$res = wp_mail($recipient, $subject, $emailBody, '', $attachments);
			}

			// Copy to admin
			if (isset($company['email']) && !empty($company['email']) && $book_send_email['admin'] == 1) {
				@wp_mail($company['email'], $subject, $emailBody, '', $attachments);
			}
		}
	}

	//Header('Location: ' . home_url() . '?page=carrental&summary=' . $_POST['item_number']); Exit;
} else {
	// IPN response was "INVALID"
	//Header('Location: ' . home_url() . '?page=carrental&paymentError=1'); Exit;
	file_put_contents('paypal_ipn.log', '***INVALID***', FILE_APPEND);
}
file_put_contents('paypal_ipn.log', "\n\n-------------------------------------\n\n", FILE_APPEND);
