<?php

//define( 'SHORTINIT', true );
require_once( dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php' );

// check if payment was successfull
if (is_file(dirname(dirname(__FILE__)) . '/carrental-payments-eway/RapidAPI.php')) {
	require_once(dirname(dirname(__FILE__)) . '/carrental-payments-eway/RapidAPI.php');

	$payments_others = unserialize(get_option('carrental_available_payments_others'));
	if ($payments_others && !empty($payments_others) && isset($payments_others['eway']) && $payments_others['eway']['enabled'] == 'yes') {
		$request = new eWAY\GetAccessCodeResultRequest();
		$request->AccessCode = $_GET['AccessCode'];

		$service = new eWAY\RapidAPI(
			$payments_others['eway']['api-key'], $payments_others['eway']['api-password']
			//, array('sandbox' => true)
		);

		$result = $service->GetAccessCodeResult($request);
		if ($result) {
			if ($result->ResponseMessage) {
				if (substr($result->ResponseMessage, 0, 2) == 'A2' && $result->TotalAmount > 0) {
					// payment approved
					$wpdb->query($wpdb->prepare('UPDATE ' . CarRental::$db['booking'] . ' SET `paid_online` = ' . ((float) ($result->TotalAmount / 100)) . ', `status` = 1 WHERE MD5(CONCAT(`id_order`, %s, `email`)) = %s', CarRental::$hash_salt, $result->InvoiceReference));
					$lang = 'en_GB';
					if (isset($result->Options) && isset($result->Options[0]) && isset($result->Options[0]->Value) && !empty($result->Options[0]->Value)) {
						$lang = $result->Options[0]->Value;
					}

					// Send e-mail
					$emailBody = get_option('carrental_reservation_email_' . $lang);
					if ($emailBody == '') {
						$emailBody = get_option('carrental_reservation_email_en_GB');
					}
					
					$emailSubject = get_option('carrental_reservation_email_subject_' . $lang);
					if ($emailSubject == '') {
						$emailSubject = get_option('carrental_reservation_email_subject_en_GB');
					}
					
					if (!empty($emailBody)) {
						$data = $wpdb->get_row($wpdb->prepare('SELECT * FROM `' . CarRental::$db['booking'] . '` WHERE MD5(CONCAT(`id_order`, %s, `email`)) = %s LIMIT 1', CarRental::$hash_salt, $result->InvoiceReference), ARRAY_A);
						
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
							$emailBody = str_replace('[ReservationLink]', home_url() . '?page=carrental&summary=' . $result->InvoiceReference, $emailBody);
							$emailBody = str_replace('[ReservationLinkStart]', '<a href="' . home_url() . '?page=carrental&summary=' . $result->InvoiceReference . '">', $emailBody);
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
								$subject = str_replace('[ReservationLink]', home_url() . '?page=carrental&summary=' . $result->InvoiceReference, $subject);
								$subject = str_replace('[ReservationLinkStart]', '<a href="' . home_url() . '?page=carrental&summary=' . $result->InvoiceReference . '">', $subject);
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
					Header('Location: ' . home_url() . '?page=carrental&summary=' . $result->InvoiceReference); Exit;
				}
			}
		}
	}
}
echo 'Error - we can not verify receipt of your payment.';