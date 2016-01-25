<?php

//define( 'SHORTINIT', true );
require_once( dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php' );

$payments_others = unserialize(get_option('carrental_available_payments_others'));
if (is_file(dirname(dirname(__FILE__)) . '/carrental-payments-mercadopago/mercadopago.php') && $payments_others && !empty($payments_others) && isset($payments_others['eway']) && $payments_others['eway']['enabled'] == 'yes') {
	require_once(dirname(dirname(__FILE__)) . '/carrental-payments-mercadopago/mercadopago.php');

	$mp = new MP($payments_others['mercadopago']['client-id'], $payments_others['mercadopago']['client-secret']);
	$mp->sandbox_mode(false);
	try {
		$response = $mp->get_payment($_GET['id']);
		file_put_contents('mercadopago_ipn.log', print_r($response, true)."\n\n-------------------------------------\n\n", FILE_APPEND);
	} catch (Exception $e) {
		file_put_contents('mercadopago_ipn.log', print_r($response, true). "\n\n-------------------------------------\n\n", FILE_APPEND );
		file_put_contents('mercadopago_ipn.log', 'ERROR: '.$e->getMessage(). "\n\n-------------------------------------\n\n", FILE_APPEND);
		exit;
	}
	
	//file_put_contents('mercadopago_ipn.log', print_r($response, true), FILE_APPEND . "\n\n-------------------------------------\n\n");

	if ($response) {
		if (isset($response['response']) && isset($response['response']['collection']) && isset($response['response']['collection']['status']) && $response['response']['collection']['status'] == 'approved') {
			// IPN response was "VERIFIED"
			list($payment_id, $lang) = explode('#',$response['response']['collection']['external_reference']);
			$wpdb->query($wpdb->prepare('UPDATE ' . CarRental::$db['booking'] . ' SET `paid_online` = ' . ((float) $response['response']['collection']['total_paid_amount']) . ', `status` = 1 WHERE MD5(CONCAT(`id_order`, %s, `email`)) = %s', CarRental::$hash_salt, $payment_id));
			file_put_contents('mercadopago_ipn.log', '***VERIFIED*** - ' . $wpdb->prepare('UPDATE ' . CarRental::$db['booking'] . ' SET `paid_online` = ' . ((float) $response['response']['collection']['total_paid_amount']) . ', `status` = 1 WHERE MD5(CONCAT(`id_order`, %s, `email`)) = %s', CarRental::$hash_salt, $payment_id), FILE_APPEND);

			// Send e-mail
			if (isset($lang) && !empty($lang)) {
				$emailBody = get_option('carrental_reservation_email_' . $lang);
				if ($emailBody == '') {
					$emailBody = get_option('carrental_reservation_email_en_GB');
				}
				
				$emailSubject = get_option('carrental_reservation_email_subject_' . $lang);
				if ($emailSubject == '') {
					$emailSubject = get_option('carrental_reservation_email_subject_en_GB');
				}
			} else {
				$emailBody = get_option('carrental_reservation_email_en_GB');
				$emailSubject = get_option('carrental_reservation_email_subject_en_GB');
			}

			if (!empty($emailBody)) {
				$data = $wpdb->get_row($wpdb->prepare('SELECT * FROM `' . CarRental::$db['booking'] . '` WHERE MD5(CONCAT(`id_order`, %s, `email`)) = %s LIMIT 1', CarRental::$hash_salt, $payment_id), ARRAY_A);

				if ($data) {
					$theme_options = unserialize(get_option('carrental_theme_options'));
					if (isset($theme_options['date_format'])) {
						// reformat dates
						$data['enter_date'] = date(CarRental::date_format_php($theme_options['date_format'], 'auto'), strtotime($data['enter_date']));
						$data['return_date'] = date(CarRental::date_format_php($theme_options['date_format'], 'auto'), strtotime($data['return_date']));
					}
			
					$emailBody = str_replace('[CustomerName]', $data['first_name'] . " " . $data['last_name'], $emailBody);
					$emailBody = str_replace('[ReservationDetails]', $data['vehicle'] . ', ' . $data['enter_date'] . ' (' . $data['enter_loc'] . ') - ' . $data['return_date'] . ' (' . $data['return_loc'] . ')', $emailBody);
					$emailBody = str_replace('[ReservationNumber]', $data['id_order'], $emailBody);
					$emailBody = str_replace('[ReservationLink]', home_url() . '?page=carrental&summary=' . $payment_id, $emailBody);
					$emailBody = str_replace('[ReservationLinkStart]', '<a href="' . home_url() . '?page=carrental&summary=' . $payment_id . '">', $emailBody);
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
						$subject = str_replace('[ReservationLink]', home_url() . '?page=carrental&summary=' . $payment_id, $subject);
						$subject = str_replace('[ReservationLinkStart]', '<a href="' . home_url() . '?page=carrental&summary=' . $payment_id . '">', $subject);
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
			exit;
		}
	}
	file_put_contents('paypal_ipn.log', '***INVALID***', FILE_APPEND);
	exit;
}
