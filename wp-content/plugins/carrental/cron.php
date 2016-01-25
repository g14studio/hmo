<?php

/*
 * Cron file for automatic email reminder
 * Send reminder email X days before order enter date.
 * You have to set cron on your hosting for every day for this file.
 */

//define( 'SHORTINIT', true );
require_once( dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php' );

$automatic_reminder = get_option('carrental_reminder_days');
if ((int) $automatic_reminder > 0) {
	$data_set = $wpdb->get_results($wpdb->prepare('SELECT * FROM `' . CarRental::$db['booking'] . '` WHERE DATE(`enter_date`) = %s AND `status` = 1', date('Y-m-d', strtotime('+' . (int) $automatic_reminder . ' day')), ARRAY_A));

	$sent_mails = 0;
	foreach ($data_set as $data) {
		$data = (array) $data;

		$lang = 'en_GB';
		if (isset($data['lng']) && !empty($data['lng'])) {
			$lang = $data['lng'];
		}

		// Send e-mail
		$emailBody = get_option('carrental_reminder_email_' . $lang);
		if ($emailBody == '') {
			$emailBody = get_option('carrental_reminder_email_en_GB');
		}
		
		$emailSubject = get_option('carrental_reminder_subject_' . $lang);
		if ($emailSubject == '') {
			$emailSubject = get_option('carrental_reminder_subject_en_GB');
		}

		if (!empty($emailBody)) {

			$date_from = $data['enter_date'];
			$date_to = $data['return_date'];

			$date_diff = abs(strtotime($data['return_date']) - strtotime($data['enter_date']));
			$diff_days = intval($date_diff / 86400);
			$diff_hours = intval(($date_diff % 86400) / 3600);
			$diff_minutes = intval(($date_diff % 86400) / 60);

			if ($diff_days >= 1 && ($diff_hours > 0 || $diff_minutes > 0)) {
				++$diff_days; // If you pass by 30 minutes and more, it 1 day more
			}

			$theme_options = unserialize(get_option('carrental_theme_options'));
			if (isset($theme_options['date_format'])) {
				// reformat dates
				$date_from = date(CarRental::date_format_php($theme_options['date_format'], 'auto'), strtotime($data['enter_date']));
				$date_to = date(CarRental::date_format_php($theme_options['date_format'], 'auto'), strtotime($data['return_date']));
			}

			$order_id = md5($data['id_order'] . CarRental::$hash_salt . $data['email']);
			$emailBody = str_replace('[CustomerName]', $data['first_name'] . " " . $data['last_name'], $emailBody);
			$emailBody = str_replace('[ReservationDetails]', $data['vehicle'] . ', ' . $data['enter_date'] . ' (' . $data['enter_loc'] . ') - ' . $data['return_date'] . ' (' . $data['return_loc'] . ')', $emailBody);
			$emailBody = str_replace('[Car]', $data['vehicle'], $emailBody);
			$emailBody = str_replace('[pickupdate]', $date_from, $emailBody);
			$emailBody = str_replace('[dropoffdate]', $date_to, $emailBody);
			$emailBody = str_replace('[pickup_location]', $data['enter_loc'], $emailBody);
			$emailBody = str_replace('[dropoff_location]', $data['return_loc'], $emailBody);
			$emailBody = str_replace('[rental_days]', $diff_days, $emailBody);
			$emailBody = str_replace('[ReservationNumber]', $data['id_order'], $emailBody);
			$emailBody = str_replace('[ReservationLink]', home_url() . '?page=carrental&summary=' . $order_id, $emailBody);
			$emailBody = str_replace('[ReservationLinkStart]', '<a href="' . home_url() . '?page=carrental&summary=' . $order_id . '">', $emailBody);
			$emailBody = str_replace('[ReservationLinkEnd]', '</a>', $emailBody);
			$emailBody = '<html><body>' . $emailBody . '</body></html>';
			$emailBody = removeslashes(nl2br($emailBody));
			
			$emailSubject = str_replace('[CustomerName]', $data['first_name'] . " " . $data['last_name'], $emailSubject);
			$emailSubject = str_replace('[ReservationDetails]', $data['vehicle'] . ', ' . $data['enter_date'] . ' (' . $data['enter_loc'] . ') - ' . $data['return_date'] . ' (' . $data['return_loc'] . ')', $emailSubject);
			$emailSubject = str_replace('[Car]', $data['vehicle'], $emailSubject);
			$emailSubject = str_replace('[pickupdate]', $date_from, $emailSubject);
			$emailSubject = str_replace('[dropoffdate]', $date_to, $emailSubject);
			$emailSubject = str_replace('[pickup_location]', $data['enter_loc'], $emailSubject);
			$emailSubject = str_replace('[dropoff_location]', $data['return_loc'], $emailSubject);
			$emailSubject = str_replace('[rental_days]', $diff_days, $emailSubject);
			$emailSubject = str_replace('[ReservationNumber]', $data['id_order'], $emailSubject);
			$emailSubject = str_replace('[ReservationLink]', home_url() . '?page=carrental&summary=' . $order_id, $emailSubject);
			$emailSubject = str_replace('[ReservationLinkStart]', '<a href="' . home_url() . '?page=carrental&summary=' . $order_id . '">', $emailSubject);
			$emailSubject = str_replace('[ReservationLinkEnd]', '</a>', $emailSubject);

			$recipient = $data['email'];

			$company = unserialize(get_option('carrental_company_info'));

			$email = ((isset($company['email']) && !empty($company['email'])) ? $company['email'] : 'admin@' . $_SERVER['SERVER_NAME']);
			$name = ((isset($company['name']) && !empty($company['name'])) ? $company['name'] : 'Car Rental WP Plugin');

			add_filter('wp_mail_content_type', create_function('', 'return "text/html"; '));
			add_filter('wp_mail_from', create_function('', 'return "' . $email . '"; '));
			add_filter('wp_mail_from_name', create_function('', 'return "' . $name . '"; '));

			$res = wp_mail($recipient, $emailSubject, $emailBody);
			$sent_mails++;
		}
	}
	echo date('Y-m-d H:i:s') . ' - Cron status: OK, sent emails (automatic reminder): ' . $sent_mails . "\n";
} else {
	echo date('Y-m-d H:i:s') . ' - Reminder set to 0 or less days. Exit.' . "\n";
}


$ty_days = get_option('carrental_thank_you_days');
if ((int) $ty_days > 0) {
	$data_set = $wpdb->get_results($wpdb->prepare('SELECT * FROM `' . CarRental::$db['booking'] . '` WHERE DATE(`enter_date`) = %s AND `status` = 1', date('Y-m-d', strtotime('-' . (int) $ty_days . ' day')), ARRAY_A));

	$sent_mails = 0;
	foreach ($data_set as $data) {
		$data = (array) $data;

		$lang = 'en_GB';
		if (isset($data['lng']) && !empty($data['lng'])) {
			$lang = $data['lng'];
		}

		// Send e-mail
		$emailBody = get_option('carrental_thank_you_email_' . $lang);
		if ($emailBody == '') {
			$emailBody = get_option('carrental_thank_you_email_en_GB');
		}
		
		$emailSubject = get_option('carrental_thank_you_email_subject_' . $lang);
		if ($emailSubject == '') {
			$emailSubject = get_option('carrental_thank_you_email_subject_en_GB');
		}

		if (!empty($emailBody)) {

			$date_from = $data['enter_date'];
			$date_to = $data['return_date'];

			$date_diff = abs(strtotime($data['return_date']) - strtotime($data['enter_date']));
			$diff_days = intval($date_diff / 86400);
			$diff_hours = intval(($date_diff % 86400) / 3600);
			$diff_minutes = intval(($date_diff % 86400) / 60);

			if ($diff_days >= 1 && ($diff_hours > 0 || $diff_minutes > 0)) {
				++$diff_days; // If you pass by 30 minutes and more, it 1 day more
			}

			$theme_options = unserialize(get_option('carrental_theme_options'));
			if (isset($theme_options['date_format'])) {
				// reformat dates
				$date_from = date(CarRental::date_format_php($theme_options['date_format'], 'auto'), strtotime($data['enter_date']));
				$date_to = date(CarRental::date_format_php($theme_options['date_format'], 'auto'), strtotime($data['return_date']));
			}

			$order_id = md5($data['id_order'] . CarRental::$hash_salt . $data['email']);
			$emailBody = str_replace('[CustomerName]', $data['first_name'] . " " . $data['last_name'], $emailBody);
			$emailBody = str_replace('[ReservationDetails]', $data['vehicle'] . ', ' . $data['enter_date'] . ' (' . $data['enter_loc'] . ') - ' . $data['return_date'] . ' (' . $data['return_loc'] . ')', $emailBody);
			$emailBody = str_replace('[Car]', $data['vehicle'], $emailBody);
			$emailBody = str_replace('[pickupdate]', $date_from, $emailBody);
			$emailBody = str_replace('[dropoffdate]', $date_to, $emailBody);
			$emailBody = str_replace('[pickup_location]', $data['enter_loc'], $emailBody);
			$emailBody = str_replace('[dropoff_location]', $data['return_loc'], $emailBody);
			$emailBody = str_replace('[rental_days]', $diff_days, $emailBody);
			$emailBody = str_replace('[ReservationNumber]', $data['id_order'], $emailBody);
			$emailBody = str_replace('[ReservationLink]', home_url() . '?page=carrental&summary=' . $order_id, $emailBody);
			$emailBody = str_replace('[ReservationLinkStart]', '<a href="' . home_url() . '?page=carrental&summary=' . $order_id . '">', $emailBody);
			$emailBody = str_replace('[ReservationLinkEnd]', '</a>', $emailBody);
			$emailBody = '<html><body>' . $emailBody . '</body></html>';
			$emailBody = removeslashes(nl2br($emailBody));
			
			$emailSubject = str_replace('[CustomerName]', $data['first_name'] . " " . $data['last_name'], $emailSubject);
			$emailSubject = str_replace('[ReservationDetails]', $data['vehicle'] . ', ' . $data['enter_date'] . ' (' . $data['enter_loc'] . ') - ' . $data['return_date'] . ' (' . $data['return_loc'] . ')', $emailSubject);
			$emailSubject = str_replace('[Car]', $data['vehicle'], $emailSubject);
			$emailSubject = str_replace('[pickupdate]', $date_from, $emailSubject);
			$emailSubject = str_replace('[dropoffdate]', $date_to, $emailSubject);
			$emailSubject = str_replace('[pickup_location]', $data['enter_loc'], $emailSubject);
			$emailSubject = str_replace('[dropoff_location]', $data['return_loc'], $emailSubject);
			$emailSubject = str_replace('[rental_days]', $diff_days, $emailSubject);
			$emailSubject = str_replace('[ReservationNumber]', $data['id_order'], $emailSubject);
			$emailSubject = str_replace('[ReservationLink]', home_url() . '?page=carrental&summary=' . $order_id, $emailSubject);
			$emailSubject = str_replace('[ReservationLinkStart]', '<a href="' . home_url() . '?page=carrental&summary=' . $order_id . '">', $emailSubject);
			$emailSubject = str_replace('[ReservationLinkEnd]', '</a>', $emailSubject);

			$recipient = $data['email'];
			$subject = $emailSubject == '' ? "Thank you email" : $emailSubject;

			$company = unserialize(get_option('carrental_company_info'));

			$email = ((isset($company['email']) && !empty($company['email'])) ? $company['email'] : 'admin@' . $_SERVER['SERVER_NAME']);
			$name = ((isset($company['name']) && !empty($company['name'])) ? $company['name'] : 'Car Rental WP Plugin');

			add_filter('wp_mail_content_type', create_function('', 'return "text/html"; '));
			add_filter('wp_mail_from', create_function('', 'return "' . $email . '"; '));
			add_filter('wp_mail_from_name', create_function('', 'return "' . $name . '"; '));

			$res = wp_mail($recipient, $subject, $emailBody);
			$sent_mails++;
		}
	}
	echo date('Y-m-d H:i:s') . ' - Cron status: OK, sent emails (thank you): ' . $sent_mails . "\n";
} else {
	echo date('Y-m-d H:i:s') . ' - Thank you email days set to 0 or less days. Exit.' . "\n";
}

function removeslashes($string)
	{
		$string=implode("",explode("\\",$string));
		return stripslashes(trim($string));
	}