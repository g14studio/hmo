<?php

/*
  Version: 3.0.1

  @created: 2015-09-21
  @todo:

 */

class CarRental_Admin {

	private static $initiated = false;
	private static $available_languages;
	private static $additional_parameters;
	public static $booking_statuses = array(1 => 'Confirmado', 2 => 'Pendiente de Pago', 3 => 'Pago Anticipado', 4 => 'Reserva en curso', 5 => 'Reserva Finalizada');
	public static $types_of_rental = array('car' => 'Car rental', 'motorbike' => 'Motorbike rental', 'scooter' => 'Scooter', 'bike' => 'Bike rental', 'boat' => 'Boat rental', 'rv' => 'RV rental', 'other_motorized' => 'Other motorized vehicle rental', 'other_vehicle' => 'Other vehicle rental', 'other' => 'Other rental');
	public static $fleet_parameter_types = array(1 => 'range', 2 => 'values');

	public static function init() {
		global $wpdb;

		if (!self::$initiated) {
			self::init_hooks();
		}
		
		if (isset($_GET['news'])) {
			self::ajax_ecalypse_news();
			die();
		}

		// Demo data
		if (isset($_POST['import_demo_data'])) {
			self::import_demo_data();
			self::set_flash_msg('success', __('Demo data was successfully imported.', 'carrental'));
			Header('Location: ' . self::get_page_url('carrental'));
			Exit;
		}

		$update_db = get_option('carrental_do_database_update');
		if ($update_db && $update_db == 1) {
			CarRental::plugin_activation('carrental_do_database_update');
			update_option('carrental_do_database_update', 0);
			self::set_flash_msg('success', __('Plugin was successfully updated.', 'carrental'));
			Header('Location: ' . self::get_page_url('carrental-settings'));
			Exit;
		}


		//////////
		// AJAX //
		//////////
		if (isset($_GET['get_day_ranges']) && (int) $_GET['get_day_ranges'] > 0) {
			print self::print_pricing_ranges((int) $_GET['get_day_ranges']);
			exit;
		}

		if (isset($_GET['get_onetime_price']) && (int) $_GET['get_onetime_price'] > 0) {
			print self::print_onetime_price((int) $_GET['get_onetime_price']);
			exit;
		}

		if (isset($_GET['get_extras_price_schemes']) && (int) $_GET['get_extras_price_schemes'] > 0) {
			print self::print_price_schemes('extras', (int) $_GET['get_extras_price_schemes']); // id_extras
			exit;
		}

		if (isset($_GET['get_fleet_price_schemes']) && (int) $_GET['get_fleet_price_schemes'] > 0) {
			print self::print_price_schemes('fleet', (int) $_GET['get_fleet_price_schemes']); // id_extras
			exit;
		}

		if (isset($_POST['send_test_email'])) {
			print self::send_test_email();
			exit;
		}

		////////////////
		// NEWSLETTER //
		////////////////
		
		// BULK DELETE
		if (isset($_POST['batch_delete_newsletter']) && !empty($_POST['batch_processing_values'])) {
			$bookings = explode(',', $_POST['batch_processing_values']);
			$report = $msg = array();
			foreach ($bookings as $id_booking) {
				$ret = self::remove_newsletter((int) $id_booking);
				if ($ret === true) {
					$report[] = 'ok';
					$msg[] = (int) $id_booking . ' - ok';
				} else {
					$report[] = 'error';
					$msg[] = (int) $id_booking . ' - error';
				}
			}
			
			if (!in_array('error', $report)) {
				self::set_flash_msg('success', __('Emails was successfully deleted.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-newsletter'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__("Emails was not deleted due to error (%s).", 'carrental'), implode(', ', $msg)));
				Header('Location: ' . self::get_page_url('carrental-newsletter'));
				Exit;
			}
		}

		if (isset($_GET['carrental-newsletter-export'])) {
			self::newsletter_export($_GET['carrental-newsletter-export']);
		}

		//////////////
		// SETTINGS //
		//////////////

		if (isset($_POST['edit_settings'])) {
			$msg = self::update_settings();
			if ($msg === true) {
				self::set_flash_msg('success', __('Settings was successfully saved.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-settings'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__('Settings was not saved due to error  (%s).', 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-settings'));
				Exit;
			}
		}

		if (isset($_POST['edit_company_info'])) {
			$msg = self::update_company_info();
			if ($msg === true) {
				self::set_flash_msg('success', __('Company info was successfully saved.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-settings'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__('Company info was not saved due to error  (%s).', 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-settings'));
				Exit;
			}
		}

		if (isset($_POST['update_vehicle_categories'])) {
			$msg = self::update_vehicle_categories();
			if ($msg === true) {
				self::set_flash_msg('success', __('Vehicle Categories was successfully saved.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-settings') . '#vehicle-categories');
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__('Vehicle Categories was not saved due to error  (%s).', 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-settings') . '#vehicle-categories');
				Exit;
			}
		}

		if (isset($_POST['add_vehicle_category'])) {
			$msg = self::add_vehicle_category();
			if ($msg === true) {
				self::set_flash_msg('success', __('Vehicle Category was successfully added.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-settings') . '#vehicle-categories');
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__('Vehicle Category was not added due to error  (%s).', 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-settings') . '#vehicle-categories');
				Exit;
			}
		}
		
		if (isset($_POST['save_reservation_inputs'])) {
			$inputs = array();
			$inputs_list = array('company' => 'Company', 'vat' => 'VAT', 'flight' => 'Flight number', 'license' => 'License number', 'id_card' => 'ID / Passport number', 'partner_code' => 'Partner code');
			foreach ($inputs_list as $k => $v) {
				if (!isset($_POST['carrental_inputs'][$k])) {
					$inputs[$k] = 1;
				}
			}
			
			update_option('carrental_reservation_inputs', serialize($inputs));
			$msg = true;
				
			if ($msg === true) {
				self::set_flash_msg('success', __('Reservation inputs was successfully updated.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-settings') . '#reservation-inputs');
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__('Reservation inputs was not updated due to error  (%s).', 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-settings') . '#reservation-inputs');
				Exit;
			}
		}
		
		if (isset($_POST['save_holidays'])) {
			$holidays = array();
			if (isset($_POST['carrental_holidays'])) {
				foreach ($_POST['carrental_holidays'] as $date) {
					$holidays[$date] = $date;
				}
			}
			$msg = true;
			update_option('carrental_holidays', serialize($holidays));
				
			if ($msg === true) {
				self::set_flash_msg('success', __('Holidays was successfully updated.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-settings') . '#holidays');
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__('Holidays was not updated due to error  (%s).', 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-settings') . '#holidays');
				Exit;
			}
		}
		
		if (isset($_POST['save_min_booking_time'])) {
			$min_rent_times = array();
			if (isset($_POST['minimum_rental_time']) && is_array($_POST['minimum_rental_time'])) {
				foreach ($_POST['minimum_rental_time'] as $month => $value) {
					$min_rent_times[$month] = (int)$value > 31 ? 31 : ((int)$value < 0 ? 0 : (int)$value);
				}
			}
			update_option('carrental_minimum_rental_time', serialize($min_rent_times));
			self::set_flash_msg('success', __('Minimum booking time per month successfully saved.', 'carrental'));
			Header('Location: ' . self::get_page_url('carrental-settings') . '#min-booking-time');
			Exit;
		}

		if (isset($_POST['replace_price_scheme']) && (int) $_POST['price_scheme_original'] > 0 && (int) $_POST['price_scheme_new'] > 0) {
			$msg = self::replace_price_scheme((int) $_POST['price_scheme_original'], (int) $_POST['price_scheme_new']);
			if ($msg === true) {
				self::set_flash_msg('success', __('Price scheme was successfully replaced.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-settings'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__('Price scheme was not replaced due to error  (%s).', 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-settings'));
				Exit;
			}
		}

		if (isset($_POST['save_smtp_settings'])) {
			$msg = self::update_smtp_settings();
			if ($msg === true) {
				self::set_flash_msg('success', __('SMTP Settings was successfully replaced.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-settings'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__('SMTP Settings was not replaced due to error  (%s).', 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-settings'));
				Exit;
			}
		}

		if (isset($_POST['save_api_key']) && !empty($_POST['api_key'])) {
			$msg = self::update_api_key();
			if ($msg === true) {
				self::set_flash_msg('success', __('API key was successfully updated.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-settings'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__('API key was not updated due to error  (%s).', 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-settings'));
				Exit;
			}
		}

		if (isset($_POST['check_plugin_update'])) {
			$msg = self::check_plugin_update();
			if ($msg === true) {
				self::set_flash_msg('success', __('Plugin updates was successfully checked.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-settings'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__('Plugin updates was not checked due to error (%s).', 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-settings'));
				Exit;
			}
		}

		if (isset($_POST['export_database'])) {
			$sql = self::export_database();
			$fileName = 'car_rental_plugin_db_' . Date('Y-m-d') . '.sql';

			header('Content-Type: application/octet-stream');
			header("Content-Transfer-Encoding: Binary");
			header("Content-disposition: attachment; filename=\"" . $fileName . "\"");
			echo $sql;
			exit;
		}

		if (isset($_POST['plugin_update'])) {
			$msg = self::process_plugin_update();
			if ($msg === true) {
				self::set_flash_msg('success', __('Plugin was successfully updated.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-settings'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__('Plugin was not updated due to error (%s).', 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-settings'));
				Exit;
			}
		}


		//////////////
		// BRANCHES //
		//////////////
		// ADD / MODIFY
		if (isset($_POST['add_branch'])) {
			$msg = self::add_branch();
			if ($msg === true) {
				self::set_flash_msg('success', ((isset($_POST['id_branch'])) ? __('Branch was successfully modified.', 'carrental') : __('New branch was successfully added.', 'carrental')));
				Header('Location: ' . self::get_page_url('carrental-branches'));
				Exit;
			} else {
				self::set_flash_msg('danger', ((isset($_POST['id_branch'])) ? sprintf(__('Branch was not modified due to error (%s).', 'carrental'), $msg) : sprintf(__('New branch was not added due to error (%s).', 'carrental'), $msg)));
				Header('Location: ' . self::get_page_url('carrental-branches'));
				Exit;
			}
		}

		// COPY
		if (isset($_POST['copy_branch']) && !empty($_POST['id_branch'])) {
			$msg = self::copy_branch((int) $_POST['id_branch']);
			if ($msg === true) {
				self::set_flash_msg('success', __('Branch was successfully copied.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-branches'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__('Branch was not copied due to error (%s).', 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-branches'));
				Exit;
			}
		}

		// BULK COPY
		if (isset($_POST['batch_copy_branch']) && !empty($_POST['batch_processing_values'])) {
			$branches = explode(',', $_POST['batch_processing_values']);
			$report = $msg = array();
			foreach ($branches as $id_branch) {
				$ret = self::copy_branch((int) $id_branch);
				if ($ret === true) {
					$report[] = 'ok';
					$msg[] = (int) $id_branch . ' - ok';
				} else {
					$report[] = 'error';
					$msg[] = (int) $id_branch . ' - error';
				}
			}

			if (!in_array('error', $report)) {
				self::set_flash_msg('success', __('Branches was successfully copied.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-branches'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__("Branches was not copied due to error (%s).", 'carrental'), implode(', ', $msg)));
				Header('Location: ' . self::get_page_url('carrental-branches'));
				Exit;
			}
		}

		// DELETE
		if (isset($_POST['delete_branch']) && !empty($_POST['id_branch'])) {
			$msg = self::delete_branch((int) $_POST['id_branch']);
			if ($msg === true) {
				self::set_flash_msg('success', __('Branch was successfully deleted.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-branches'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__('Branch was not deleted due to error (%s).', 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-branches'));
				Exit;
			}
		}

		// BULK DELETE
		if (isset($_POST['batch_delete_branch']) && !empty($_POST['batch_processing_values'])) {
			$branches = explode(',', $_POST['batch_processing_values']);
			$report = $msg = array();
			foreach ($branches as $id_branch) {
				$ret = self::delete_branch((int) $id_branch);
				if ($ret === true) {
					$report[] = 'ok';
					$msg[] = (int) $id_branch . ' - ok';
				} else {
					$report[] = 'error';
					$msg[] = (int) $id_branch . ' - error';
				}
			}

			if (!in_array('error', $report)) {
				self::set_flash_msg('success', __('Branches was successfully deleted.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-branches'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__("Branches was not deleted due to error (%s).", 'carrental'), implode(', ', $msg)));
				Header('Location: ' . self::get_page_url('carrental-branches'));
				Exit;
			}
		}

		// RESTORE
		if (isset($_POST['restore_branch']) && !empty($_POST['id_branch'])) {
			$msg = self::restore_branch((int) $_POST['id_branch']);
			if ($msg === true) {
				self::set_flash_msg('success', __('Branch was successfully restored.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-branches'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__('Branch was not restored due to error (%s).', 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-branches'));
				Exit;
			}
		}

		////////////
		// EXTRAS //
		////////////
		// ADD / MODIFY
		if (isset($_POST['add_extras'])) {
			$msg = self::add_extras();
			if ($msg === true) {
				self::set_flash_msg('success', ((isset($_POST['id_extras'])) ? __('Item was successfully modified.', 'carrental') : __('New item was successfully added.', 'carrental')));
				Header('Location: ' . self::get_page_url('carrental-extras'));
				Exit;
			} else {
				self::set_flash_msg('danger', ((isset($_POST['id_extras'])) ? sprintf(__('Item was not modified due to error (%s).', 'carrental'), $msg) : sprintf(__('New item was not added due to error (%s).', 'carrental'), $msg)));
				Header('Location: ' . self::get_page_url('carrental-extras'));
				Exit;
			}
		}

		// COPY
		if (isset($_POST['copy_extras']) && !empty($_POST['id_extras'])) {
			$msg = self::copy_extras((int) $_POST['id_extras']);
			if ($msg === true) {
				self::set_flash_msg('success', __('Item was successfully copied.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-extras'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__('Item was not copied due to error (%s).', 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-extras'));
				Exit;
			}
		}

		// BULK COPY
		if (isset($_POST['batch_copy_extras']) && !empty($_POST['batch_processing_values'])) {
			$extras = explode(',', $_POST['batch_processing_values']);
			$report = $msg = array();
			foreach ($extras as $id_extras) {
				$ret = self::copy_extras((int) $id_extras);
				if ($ret === true) {
					$report[] = 'ok';
					$msg[] = (int) $id_extras . ' - ok';
				} else {
					$report[] = 'error';
					$msg[] = (int) $id_extras . ' - error';
				}
			}

			if (!in_array('error', $report)) {
				self::set_flash_msg('success', __('Items was successfully copied.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-extras'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__("Items was not copied due to error (%s).", 'carrental'), implode(', ', $msg)));
				Header('Location: ' . self::get_page_url('carrental-extras'));
				Exit;
			}
		}

		// DELETE
		if (isset($_POST['delete_extras']) && !empty($_POST['id_extras'])) {
			$msg = self::delete_extras((int) $_POST['id_extras']);
			if ($msg === true) {
				self::set_flash_msg('success', __('Item was successfully deleted.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-extras'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__('Item was not deleted due to error (%s).', 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-extras'));
				Exit;
			}
		}

		// BULK DELETE
		if (isset($_POST['batch_delete_extras']) && !empty($_POST['batch_processing_values'])) {
			$extras = explode(',', $_POST['batch_processing_values']);
			$report = $msg = array();
			foreach ($extras as $id_extras) {
				$ret = self::delete_extras((int) $id_extras);
				if ($ret === true) {
					$report[] = 'ok';
					$msg[] = (int) $id_extras . ' - ok';
				} else {
					$report[] = 'error';
					$msg[] = (int) $id_extras . ' - error';
				}
			}

			if (!in_array('error', $report)) {
				self::set_flash_msg('success', __('Items was successfully deleted.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-extras'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__("Items was not deleted due to error (%s).", 'carrental'), implode(', ', $msg)));
				Header('Location: ' . self::get_page_url('carrental-extras'));
				Exit;
			}
		}

		// RESTORE
		if (isset($_POST['restore_extras']) && !empty($_POST['id_extras'])) {
			$msg = self::restore_extras((int) $_POST['id_extras']);
			if ($msg === true) {
				self::set_flash_msg('success', __('Item was successfully restored.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-extras'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__('Item was not restored due to error (%s).', 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-extras'));
				Exit;
			}
		}

		///////////
		// FLEET //
		///////////
		// ADD / MODIFY
		if (isset($_POST['add_fleet'])) {
			$msg = self::add_fleet();
			if ($msg === true) {
				self::set_flash_msg('success', ((isset($_POST['id_fleet'])) ? __('Vehicle was successfully modified.', 'carrental') : __('New vehicle was successfully added.', 'carrental')));
				Header('Location: ' . self::get_page_url('carrental-fleet'));
				Exit;
			} else {
				self::set_flash_msg('danger', ((isset($_POST['id_fleet'])) ? sprintf(__('Vehicle was not modified due to error (%s).', 'carrental'), $msg) : sprintf(__('New vehicle was not added due to error (%s).', 'carrental'), $msg)));
				Header('Location: ' . self::get_page_url('carrental-fleet'));
				Exit;
			}
		}

		// COPY
		if (isset($_POST['copy_fleet']) && !empty($_POST['id_fleet'])) {
			$msg = self::copy_fleet((int) $_POST['id_fleet']);
			if ($msg === true) {
				self::set_flash_msg('success', __('Vehicle was successfully copied.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-fleet'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__("Vehicle was not copied due to error (%s).", 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-fleet'));
				Exit;
			}
		}
		
		// RESTORE
		if (isset($_POST['restore_fleet']) && !empty($_POST['id_fleet'])) {
			$msg = self::restore_fleet((int) $_POST['id_fleet']);
			if ($msg === true) {
				self::set_flash_msg('success', __('Vehicle was successfully restored.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-fleet'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__("Vehicle was not restored due to error (%s).", 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-fleet'));
				Exit;
			}
		}
		
		// BULK COPY
		if (isset($_POST['batch_copy_fleet']) && !empty($_POST['batch_processing_values'])) {
			$vehicles = explode(',', $_POST['batch_processing_values']);
			$report = $msg = array();
			foreach ($vehicles as $id_fleet) {
				$ret = self::copy_fleet((int) $id_fleet);
				if ($ret === true) {
					$report[] = 'ok';
					$msg[] = (int) $id_fleet . ' - ok';
				} else {
					$report[] = 'error';
					$msg[] = (int) $id_fleet . ' - error';
				}
			}

			if (!in_array('error', $report)) {
				self::set_flash_msg('success', __('Vehicles was successfully copied.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-fleet'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__("Vehicles was not copied due to error (%s).", 'carrental'), implode(', ', $msg)));
				Header('Location: ' . self::get_page_url('carrental-fleet'));
				Exit;
			}
		}

		// DELETE
		if (isset($_POST['delete_fleet']) && !empty($_POST['id_fleet'])) {
			$msg = self::delete_fleet((int) $_POST['id_fleet']);
			if ($msg === true) {
				self::set_flash_msg('success', __('Vehicle was successfully deleted.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-fleet'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__('Vehicle was not deleted due to error (%s).', 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-fleet'));
				Exit;
			}
		}

		// BULK DELETE
		if (isset($_POST['batch_delete_fleet']) && !empty($_POST['batch_processing_values'])) {
			$vehicles = explode(',', $_POST['batch_processing_values']);
			$report = $msg = array();
			foreach ($vehicles as $id_fleet) {
				$ret = self::delete_fleet((int) $id_fleet);
				if ($ret === true) {
					$report[] = 'ok';
					$msg[] = (int) $id_fleet . ' - ok';
				} else {
					$report[] = 'error';
					$msg[] = (int) $id_fleet . ' - error';
				}
			}

			if (!in_array('error', $report)) {
				self::set_flash_msg('success', __('Vehicles was successfully deleted.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-fleet'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__("Vehicles was not deleted due to error (%s).", 'carrental'), implode(', ', $msg)));
				Header('Location: ' . self::get_page_url('carrental-fleet'));
				Exit;
			}
		}

		// DELETE FROM DATABASE
		if (isset($_POST['batch_delete_db_fleet']) && !empty($_POST['batch_processing_values'])) {
			$vehicles = explode(',', $_POST['batch_processing_values']);
			$report = $msg = array();
			foreach ($vehicles as $id_fleet) {
				$ret = self::delete_fleet_from_db((int) $id_fleet);
				if ($ret === true) {
					$report[] = 'ok';
					$msg[] = (int) $id_fleet . ' - ok';
				} else {
					$report[] = 'error';
					$msg[] = (int) $id_fleet . ' - error';
				}
			}

			if (!in_array('error', $report)) {
				self::set_flash_msg('success', __('Vehicles was successfully deleted from database.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-fleet') . '&deleted');
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__("Vehicles was not deleted from database due to error (%s).", 'carrental'), implode(', ', $msg)));
				Header('Location: ' . self::get_page_url('carrental-fleet') . '&deleted');
				Exit;
			}
		}
		
		///////////
		// FLEET PARAMETERS //
		///////////
		// ADD / MODIFY
		if (isset($_POST['add_fleet_parameter'])) {
			$msg = self::add_fleet_parameter();
			if ($msg === true) {
				self::set_flash_msg('success', ((isset($_POST['id_fleet_parameter'])) ? __('Parameter was successfully modified.', 'carrental') : __('New parameter was successfully added.', 'carrental')));
				Header('Location: ' . self::get_page_url('carrental-fleet-parameters'));
				Exit;
			} else {
				self::set_flash_msg('danger', ((isset($_POST['id_fleet_parameter'])) ? sprintf(__('Parameter was not modified due to error (%s).', 'carrental'), $msg) : sprintf(__('New parameter was not added due to error (%s).', 'carrental'), $msg)));
				Header('Location: ' . self::get_page_url('carrental-fleet-parameters'));
				Exit;
			}
		}

		// COPY
		if (isset($_POST['copy_fleet_parameter']) && !empty($_POST['id_fleet_parameter'])) {
			$msg = self::copy_fleet_parameter((int) $_POST['id_fleet_parameter']);
			if ($msg === true) {
				self::set_flash_msg('success', __('Parameter was successfully copied.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-fleet-parameters'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__("Parameter was not copied due to error (%s).", 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-fleet-parameters'));
				Exit;
			}
		}

		// DELETE
		if (isset($_POST['delete_fleet_parameter']) && !empty($_POST['id_fleet_parameter'])) {
			$msg = self::delete_fleet_parameter((int) $_POST['id_fleet_parameter']);
			if ($msg === true) {
				self::set_flash_msg('success', __('Parameter was successfully deleted.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-fleet-parameters'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__('Parameter was not deleted due to error (%s).', 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-fleet-parameters'));
				Exit;
			}
		}


		/////////////
		// PRICING //
		/////////////
		// ADD / MODIFY
		if (isset($_POST['add_pricing'])) {
			$msg = self::add_pricing();
			if ($msg === true) {
				self::set_flash_msg('success', ((isset($_POST['id_pricing'])) ? __('Price scheme was successfully modified.', 'carrental') : __('New price scheme was successfully added.', 'carrental')));
				Header('Location: ' . self::get_page_url('carrental-pricing'));
				Exit;
			} else {
				self::set_flash_msg('danger', ((isset($_POST['id_pricing'])) ? sprintf(__('Price scheme was not modified due to error (%s).', 'carrental'), $msg) : sprintf(__('New price scheme was not added due to error (%s).', 'carrental'), $msg)));
				Header('Location: ' . self::get_page_url('carrental-pricing'));
				Exit;
			}
		}

		// COPY
		if (isset($_POST['copy_pricing']) && !empty($_POST['id_pricing'])) {
			$msg = self::copy_pricing((int) $_POST['id_pricing']);
			if ($msg === true) {
				self::set_flash_msg('success', __('Price scheme was successfully copied.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-pricing'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__("Price scheme was not copied due to error (%s).", 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-pricing'));
				Exit;
			}
		}

		// BULK COPY
		if (isset($_POST['batch_copy_pricing']) && !empty($_POST['batch_processing_values'])) {
			$pricing = explode(',', $_POST['batch_processing_values']);
			$report = $msg = array();
			foreach ($pricing as $id_pricing) {
				$ret = self::copy_pricing((int) $id_pricing);
				if ($ret === true) {
					$report[] = 'ok';
					$msg[] = (int) $id_pricing . ' - ok';
				} else {
					$report[] = 'error';
					$msg[] = (int) $id_pricing . ' - error';
				}
			}

			if (!in_array('error', $report)) {
				self::set_flash_msg('success', __('Pricing schemes was successfully copied.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-pricing'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__("Pricing schemes was not copied due to error (%s).", 'carrental'), implode(', ', $msg)));
				Header('Location: ' . self::get_page_url('carrental-pricing'));
				Exit;
			}
		}

		// DELETE
		if (isset($_POST['delete_pricing']) && !empty($_POST['id_pricing'])) {
			$msg = self::delete_pricing((int) $_POST['id_pricing']);
			if ($msg === true) {
				self::set_flash_msg('success', __('Price scheme was successfully deleted.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-pricing'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__('Price scheme was not deleted due to error (%s).', 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-pricing'));
				Exit;
			}
		}

		// BULK DELETE
		if (isset($_POST['batch_delete_pricing']) && !empty($_POST['batch_processing_values_delete'])) {
			$pricing = explode(',', $_POST['batch_processing_values_delete']);
			$report = $msg = array();
			foreach ($pricing as $id_pricing) {
				$ret = self::delete_pricing((int) $id_pricing);
				if ($ret === true) {
					$report[] = 'ok';
					$msg[] = (int) $id_pricing . ' - ok';
				} else {
					$report[] = 'error';
					$msg[] = (int) $id_pricing . ' - error';
				}
			}

			if (!in_array('error', $report)) {
				self::set_flash_msg('success', __('Pricing schemes was successfully deleted.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-pricing'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__("Pricing schemes was not deleted due to error (%s).", 'carrental'), implode(', ', $msg)));
				Header('Location: ' . self::get_page_url('carrental-pricing'));
				Exit;
			}
		}

		// RESTORE
		if (isset($_POST['restore_pricing']) && !empty($_POST['id_pricing'])) {
			$msg = self::restore_pricing((int) $_POST['id_pricing']);
			if ($msg === true) {
				self::set_flash_msg('success', __('Price scheme was successfully restored.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-pricing'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__('Price scheme was not restored due to error (%s).', 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-pricing'));
				Exit;
			}
		}

		/////////////
		// BOOKING //
		/////////////
		// ADD / MODIFY
		if (isset($_POST['add_booking']) || isset($_POST['add_booking_emails'])) {
			$msg = self::add_booking();
			if ($msg === true) {
				self::set_flash_msg('success', ((isset($_POST['id_booking'])) ? __('Booking was successfully modified.', 'carrental') : __('New booking was successfully added.', 'carrental')));
				Header('Location: ' . self::get_page_url('carrental-booking'));
				Exit;
			} else {
				self::set_flash_msg('danger', ((isset($_POST['id_booking'])) ? sprintf(__('Booking was not modified due to error (%s).', 'carrental'), $msg) : sprintf(__('New booking was not added due to error (%s).', 'carrental'), $msg)));
				Header('Location: ' . self::get_page_url('carrental-booking'));
				Exit;
			}
		}

		// COPY
		if (isset($_POST['copy_booking']) && !empty($_POST['id_booking'])) {
			$msg = self::copy_booking((int) $_POST['id_booking']);
			if ($msg === true) {
				self::set_flash_msg('success', __('Booking was successfully copied.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-booking'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__("Booking was not copied due to error (%s).", 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-booking'));
				Exit;
			}
		}
		
		// RESEND CONFIRMATION EMAIL
		if (isset($_POST['resend_email']) && !empty($_POST['id_booking'])) {
			$msg = self::resend_email((int) $_POST['id_booking']);
			if ($msg === true) {
				self::set_flash_msg('success', __('Email was successfully sent.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-booking'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__("Email was not sent due to error (%s).", 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-booking'));
				Exit;
			}
		}

		// BULK COPY
		if (isset($_POST['batch_copy_booking']) && !empty($_POST['batch_processing_values'])) {
			$booking = explode(',', $_POST['batch_processing_values']);
			$report = $msg = array();
			foreach ($booking as $id_booking) {
				$ret = self::copy_booking((int) $id_booking);
				if ($ret === true) {
					$report[] = 'ok';
					$msg[] = (int) $id_booking . ' - ok';
				} else {
					$report[] = 'error';
					$msg[] = (int) $id_booking . ' - error';
				}
			}

			if (!in_array('error', $report)) {
				self::set_flash_msg('success', __('Bookings was successfully copied.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-booking'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__("Bookings was not copied due to error (%s).", 'carrental'), implode(', ', $msg)));
				Header('Location: ' . self::get_page_url('carrental-booking'));
				Exit;
			}
		}

		// DELETE
		if (isset($_POST['delete_booking']) && !empty($_POST['id_booking'])) {
			$msg = self::delete_booking((int) $_POST['id_booking']);
			if ($msg === true) {
				self::set_flash_msg('success', __('Booking was successfully archived.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-booking'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__('Booking was not archived due to error (%s).', 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-booking'));
				Exit;
			}
		}

		// BULK DELETE
		if (isset($_POST['batch_delete_booking']) && !empty($_POST['batch_processing_values'])) {
			$booking = explode(',', $_POST['batch_processing_values']);
			$report = $msg = array();
			foreach ($booking as $id_booking) {
				$ret = self::delete_booking_total((int) $id_booking);
				if ($ret === true) {
					$report[] = 'ok';
					$msg[] = (int) $id_booking . ' - ok';
				} else {
					$report[] = 'error';
					$msg[] = (int) $id_booking . ' - error';
				}
			}

			if (!in_array('error', $report)) {
				self::set_flash_msg('success', __('Bookings was successfully deleted.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-booking'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__("Bookings was not deleted due to error (%s).", 'carrental'), implode(', ', $msg)));
				Header('Location: ' . self::get_page_url('carrental-booking'));
				Exit;
			}
		}

		// BULK ARCHIVE
		if (isset($_POST['batch_archive_booking']) && !empty($_POST['batch_processing_values'])) {
			$booking = explode(',', $_POST['batch_processing_values']);
			$report = $msg = array();
			foreach ($booking as $id_booking) {
				$ret = self::delete_booking((int) $id_booking);
				if ($ret === true) {
					$report[] = 'ok';
					$msg[] = (int) $id_booking . ' - ok';
				} else {
					$report[] = 'error';
					$msg[] = (int) $id_booking . ' - error';
				}
			}

			if (!in_array('error', $report)) {
				self::set_flash_msg('success', __('Bookings was successfully archived.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-booking'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__("Bookings was not archived due to error (%s).", 'carrental'), implode(', ', $msg)));
				Header('Location: ' . self::get_page_url('carrental-booking'));
				Exit;
			}
		}

		// RESTORE
		if (isset($_POST['restore_booking']) && !empty($_POST['id_booking'])) {
			$msg = self::restore_booking((int) $_POST['id_booking']);
			if ($msg === true) {
				self::set_flash_msg('success', __('Booking was successfully restored.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-booking'));
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf(__('Booking was not restored due to error (%s).', 'carrental'), $msg));
				Header('Location: ' . self::get_page_url('carrental-booking'));
				Exit;
			}
		}

		//////////////
		// LANGUAGE //
		//////////////

		if (isset($_POST['add_language']) && !empty($_POST['language'])) {

			include dirname(realpath(__FILE__)) . '/languages.php';
			$available_languages = unserialize(get_option('carrental_available_languages'));
			if (empty($available_languages)) {
				$available_languages = array();
			}

			if (isset($languages[$_POST['language']])) {
				$available_languages[$_POST['language']] = $languages[$_POST['language']];
				update_option('carrental_available_languages', serialize($available_languages));
				$available_languages = unserialize(get_option('carrental_available_languages'));

				$email_body = get_option('carrental_reservation_email_' . $_POST['language']);
				if (empty($email_body)) {
					$email_body = 'Dear [CustomerName],

thank you for your reservation. Here are your reservation details:
[ReservationDetails]
[ReservationNumber]

You can return to your reservation summary page anytime by going to this link:
[ReservationLink]

We are also sending this information to the email address you have provided.

If you would like to change the reservation details, you can do so by calling our office at:
+123 456 789 or by email example@example.org

[ReservationLinkStart]Click here[ReservationLinkEnd] to print your reservation - takes them to reservation summary print out.

Thank you for your business!';
					update_option('carrental_reservation_email_' . $_POST['language'], $email_body);
				}

				self::set_flash_msg('success', __('Language was successfully added.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-translations'));
				Exit;
			}
		}

		if (isset($_POST['primary_language']) && !empty($_POST['language'])) {
			update_option('carrental_primary_language', $_POST['language']);
			self::set_flash_msg('success', __('Primary language was successfully updated.', 'carrental'));
			Header('Location: ' . self::get_page_url('carrental-translations'));
			Exit;
		}

		if (isset($_POST['disable_language']) && !empty($_POST['language'])) {
			$available_languages = unserialize(get_option('carrental_available_languages'));
			if (empty($available_languages)) {
				$available_languages = array();
			}
			if (isset($available_languages[$_POST['language']])) {
				unset($available_languages[$_POST['language']]);
				update_option('carrental_available_languages', serialize($available_languages));
				self::set_flash_msg('success', __('Language was successfully disabled.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-translations'));
				Exit;
			}
		}

		if (isset($_POST['deactivate_language']) && !empty($_POST['language'])) {
			$available_languages = unserialize(get_option('carrental_available_languages'));
			if (empty($available_languages)) {
				$available_languages = array();
			}
			if (isset($available_languages[$_POST['language']])) {
				$available_languages[$_POST['language']]['active'] = false;
				update_option('carrental_available_languages', serialize($available_languages));
				self::set_flash_msg('success', __('Language was successfully deactivated.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-translations') . '&language=' . $_POST['language']);
				Exit;
			}
		}

		if (isset($_POST['activate_language']) && !empty($_POST['language'])) {
			$available_languages = unserialize(get_option('carrental_available_languages'));
			if (empty($available_languages)) {
				$available_languages = array();
			}
			if (isset($available_languages[$_POST['language']])) {
				$available_languages[$_POST['language']]['active'] = true;
				update_option('carrental_available_languages', serialize($available_languages));
				self::set_flash_msg('success', __('Language was successfully activated.', 'carrental'));
				Header('Location: ' . self::get_page_url('carrental-translations') . '&language=' . $_POST['language']);
				Exit;
			}
		}

		if (isset($_POST['language_save_email']) && !empty($_POST['language'])) {
			update_option('carrental_reservation_email_' . $_POST['language'], $_POST['reservation_email']);
			update_option('carrental_reservation_email_subject_' . $_POST['language'], $_POST['reservation_email_subject']);
			self::set_flash_msg('success', __('E-mail was successfully updated.', 'carrental'));
			Header('Location: ' . self::get_page_url('carrental-translations') . '&language=' . $_POST['language']);
			Exit;
		}
		
		if (isset($_POST['language_save_email_reminder']) && !empty($_POST['language'])) {
			update_option('carrental_reminder_email_' . $_POST['language'], $_POST['reminder_email']);
			update_option('carrental_reminder_subject_' . $_POST['language'], $_POST['reminder_subject']);
			self::set_flash_msg('success', __('Automatic reminder e-mail was successfully updated.', 'carrental'));
			Header('Location: ' . self::get_page_url('carrental-translations') . '&language=' . $_POST['language']);
			Exit;
		}
		
		if (isset($_POST['language_save_email_thank_you']) && !empty($_POST['language'])) {
			update_option('carrental_thank_you_email_' . $_POST['language'], $_POST['thank_you_email']);
			update_option('carrental_thank_you_email_subject_' . $_POST['language'], $_POST['thank_you_email_subject']);
			self::set_flash_msg('success', __('Thank you email was successfully updated.', 'carrental'));
			Header('Location: ' . self::get_page_url('carrental-translations') . '&language=' . $_POST['language']);
			Exit;
		}
		
		if (isset($_POST['language_save_email_status_pending_other']) && !empty($_POST['language'])) {
			update_option('carrental_email_status_pending_other_' . $_POST['language'], $_POST['email_status_pending_other']);
			update_option('carrental_email_status_pending_other_subject_' . $_POST['language'], $_POST['email_status_pending_other_subject']);
			self::set_flash_msg('success', __('E-mail for status "pending other" was successfully updated.', 'carrental'));
			Header('Location: ' . self::get_page_url('carrental-translations') . '&language=' . $_POST['language']);
			Exit;
		}
		
		if (isset($_POST['language_save_email_status_pending']) && !empty($_POST['language'])) {
			update_option('carrental_email_status_pending_' . $_POST['language'], $_POST['email_status_pending']);
			update_option('carrental_email_status_pending_subject_' . $_POST['language'], $_POST['email_status_pending_subject']);
			self::set_flash_msg('success', __('E-mail for status "pending payment" was successfully updated.', 'carrental'));
			Header('Location: ' . self::get_page_url('carrental-translations') . '&language=' . $_POST['language']);
			Exit;
		}

		if (isset($_POST['language_save_terms']) && !empty($_POST['language'])) {
			update_option('carrental_terms_conditions_' . $_POST['language'], $_POST['terms_conditions']);
			self::set_flash_msg('success', __('Terms and Conditions was successfully updated.', 'carrental'));
			Header('Location: ' . self::get_page_url('carrental-translations') . '&language=' . $_POST['language']);
			Exit;
		}

		if (isset($_POST['language_save_theme_translations']) && !empty($_POST['language'])) {
			self::update_theme_translations($_POST['language'], $_POST['translation']);
			unset($_SESSION['carrental_translations']);
			self::set_flash_msg('success', __('Translations was successfully updated.', 'carrental'));
			Header('Location: ' . self::get_page_url('carrental-translations') . '&language=' . $_POST['language']);
			Exit;
		}

		// Import language
		if (isset($_POST['import_language'])) {
			$msg = self::import_language();
			if ($msg === true) {
				self::set_flash_msg('success', 'Language file successfully imported.');
				Header('Location: ' . self::get_page_url('carrental-translations') . '&language=' . $_POST['language']);
				Exit;
			} else {
				self::set_flash_msg('danger', sprintf('Language was not imported due to error (%s).', $msg));
				Header('Location: ' . self::get_page_url('carrental-translations') . '&language=' . $_POST['language']);
				Exit;
			}
		}

		// Export language
		if (isset($_POST['export_language'])) {
			self::export_language();
			exit;
		}
	}

	public static function init_hooks() {

		self::$initiated = true;

		add_action('admin_init', array('CarRental_Admin', 'admin_init'));
		add_action('admin_menu', array('CarRental_Admin', 'admin_menu'));
		add_action('admin_enqueue_scripts', array('CarRental_Admin', 'load_resources'));
		add_filter('plugin_action_links', array('CarRental_Admin', 'plugin_action_links'), 10, 2);
		add_filter('plugin_action_links_' . plugin_basename(plugin_dir_path(__FILE__) . 'carrental.php'), array('CarRental_Admin', 'admin_plugin_settings_link'));
		add_action('wp_ajax_carrental_save_branch_order', array('CarRental_Admin', 'ajax_save_branch_order'));
		add_action('wp_ajax_carrental_save_fleet_order', array('CarRental_Admin', 'ajax_save_fleet_order'));
		add_action('wp_ajax_carrental_load_ecalypse_news', array('CarRental_Admin', 'ajax_ecalypse_news'));
		add_action('wp_ajax_carrental_feed_actions', array('CarRental_Admin', 'ajax_feed_actions'));
	}

	public static function admin_init() {

		//load_plugin_textdomain('carrental');
	}

	public static function admin_menu() {
		self::load_menu();
	}

	public static function admin_head() {
		if (!current_user_can('manage_options')) {
			return;
		}
	}

	public static function admin_plugin_settings_link($links) {
		$settings_link = '<a href="' . self::get_page_url() . '">' . __('Settings', 'carrental') . '</a>';
		array_unshift($links, $settings_link);
		return $links;
	}

	public static function load_menu() {

		// Add Top level menu and sub-menu
		$hook = add_menu_page(__('Car Rental', 'carrental'), __('Car Rental', 'carrental'), 'manage_options', 'carrental', array('CarRental_Admin', 'display_page'), plugin_dir_url(__FILE__) . '/assets/carrental_menu_icon.png');
		add_submenu_page('carrental', __('Fleet - Car Rental', 'carrental'), __('Fleet', 'carrental'), 'manage_options', 'carrental-fleet', array('CarRental_Admin', 'display_page'));
		add_submenu_page('carrental', __('Extras - Car Rental', 'carrental'), __('Extras', 'carrental'), 'manage_options', 'carrental-extras', array('CarRental_Admin', 'display_page'));
		add_submenu_page('carrental', __('Branches - Car Rental', 'carrental'), __('Branches', 'carrental'), 'manage_options', 'carrental-branches', array('CarRental_Admin', 'display_page'));
		add_submenu_page('carrental', __('Pricing - Car Rental', 'carrental'), __('Pricing', 'carrental'), 'manage_options', 'carrental-pricing', array('CarRental_Admin', 'display_page'));
		add_submenu_page('carrental', __('Booking - Car Rental', 'carrental'), __('Booking', 'carrental'), 'manage_options', 'carrental-booking', array('CarRental_Admin', 'display_page'));
		add_submenu_page('carrental', __('Translations - Car Rental', 'carrental'), __('Translations', 'carrental'), 'manage_options', 'carrental-translations', array('CarRental_Admin', 'display_page'));
		add_submenu_page('carrental', __('Settings - Car Rental', 'carrental'), __('Settings', 'carrental'), 'manage_options', 'carrental-settings', array('CarRental_Admin', 'display_page'));
		add_submenu_page('carrental', __('Newsletter - Car Rental', 'carrental'), __('Newsletter', 'carrental'), 'manage_options', 'carrental-newsletter', array('CarRental_Admin', 'display_page'));
		add_submenu_page(null, __('Fleet parameters', 'carrental'), __('Fleet parameters', 'carrental'), 'manage_options', 'carrental-fleet-parameters', array('CarRental_Admin', 'display_page'));
	}

	public static function load_resources() {
		global $hook_suffix;

		$arr = array(
			'carrental',
			'carrental-fleet',
			'carrental-extras',
			'carrental-branches',
			'carrental-pricing',
			'carrental-booking',
			'carrental-translations',
			'carrental-settings',
			'carrental-newsletter',
			'carrental-fleet-parameters'
		);

		$exp = explode('_', $hook_suffix);
		$page = end($exp);
		if (in_array($page, $arr)) {

			wp_enqueue_media();

			wp_register_style('bootstrap.css', CARRENTAL__PLUGIN_URL . 'assets/bootstrap.css', array(), CARRENTAL_VERSION);
			wp_enqueue_style('bootstrap.css');

			wp_register_style('carrental.css', CARRENTAL__PLUGIN_URL . 'assets/carrental.css', array(), CARRENTAL_VERSION);
			wp_enqueue_style('carrental.css');

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

			wp_register_script('jquery.dataTables.js', '//cdn.datatables.net/1.10.0/js/jquery.dataTables.js', array());
			wp_enqueue_script('jquery.dataTables.js');

			if ($page == 'carrental-newsletter') {

				wp_register_style('dataTables.tableTools.css', '//cdn.datatables.net/tabletools/2.2.3/css/dataTables.tableTools.css', array());
				wp_enqueue_style('dataTables.tableTools.css');

				wp_register_script('dataTables.tableTools.min.js', '//cdn.datatables.net/tabletools/2.2.3/js/dataTables.tableTools.min.js', array());
				wp_enqueue_script('dataTables.tableTools.min.js');
			}

			wp_register_script('carrental.js', CARRENTAL__PLUGIN_URL . 'assets/carrental.js', array(), CARRENTAL_VERSION, true);
			wp_enqueue_script('carrental.js');
		}
	}

	public static function plugin_action_links($links, $file) {
		if ($file == plugin_basename(CARRENTAL__PLUGIN_URL . '/carrental.php')) {
			$links[] = '<a href="' . esc_url(self::get_page_url()) . '">' . esc_html__('Settings', 'carrental') . '</a>';
		}
		return $links;
	}

	public static function get_page_url($page = 'carrental') {

		$arr = array('carrental-fleet', 'carrental-extras', 'carrental-branches', 'carrental-pricing',
			'carrental-booking', 'carrental-translations', 'carrental-settings', 'carrental-newsletter', 'carrental-fleet-parameters');

		if (in_array($page, $arr)) {
			$url = add_query_arg(array('page' => $page), admin_url('admin.php'));
		} else {
			$url = add_query_arg(array('page' => 'carrental'), admin_url('admin.php'));
		}

		return $url;
	}

	public static function set_flash_msg($status = 'info', $msg = NULL) {
		$_SESSION['carrental_flash_msg'] = array('status' => $status, 'msg' => $msg);
		return true;
	}

	public static function display_page() {
		global $wpdb;
		$arr = array('carrental-fleet', 'carrental-extras', 'carrental-branches', 'carrental-pricing',
			'carrental-booking', 'carrental-translations', 'carrental-settings', 'carrental-newsletter', 'carrental-fleet-parameters');

		// Branches
		if ($_GET['page'] == 'carrental-fleet') {

			$tpl = array('fleet' => self::get_fleet(),
				'vehicle_categories' => self::get_vehicle_categories(),
				'extras' => self::get_extras(),
				'branches' => self::get_branches(),
				'pricing' => self::get_pricing('p.`name` ASC', 2),
				'params' => self::get_fleet_parameters(true),
				'params_values' => array()
				);

			if (isset($_GET['edit']) && !empty($_GET['edit'])) {
				$tpl['detail'] = self::get_fleet_detail((int) $_GET['edit']);
				$tpl['params_values'] = self::get_fleet_parameter_values((int) $_GET['edit']);
				$tpl['edit'] = true;

				$all_additional_parameters = array();
				$sql = $wpdb->get_results('SELECT `additional_parameters` FROM `' . CarRental::$db['fleet']);
				foreach ($sql as $s) {
					$params = unserialize($s->additional_parameters);
					if (!empty($params)) {
						foreach ($params as $lng => $p) {
							if (!is_array($p)) {
								continue;
							}
							if (!isset($all_additional_parameters[$lng])) {
								$all_additional_parameters[$lng] = array();
							}
							foreach ($p as $pp) {
								if (!isset($pp['name'])) {
									continue;
								}
								$all_additional_parameters[$lng][$pp['name']] = $pp['name'];
							}
						}
					}
				}
				
				$tpl['all_additional_parameters'] = $all_additional_parameters;
			}

			CarRental::view($_GET['page'], $tpl);
		} elseif ($_GET['page'] == 'carrental-extras') {

			$tpl = array('extras' => self::get_extras(),
				'pricing' => self::get_pricing('p.`name` ASC'));
			$tpl['edit'] = false;

			if (isset($_GET['edit']) && !empty($_GET['edit'])) {
				$tpl['detail'] = self::get_extras_detail((int) $_GET['edit']);
				$tpl['edit'] = true;
			}

			CarRental::view($_GET['page'], $tpl);
		} elseif ($_GET['page'] == 'carrental-branches') {

			$tpl = array('branches' => self::get_branches());
			$tpl['edit'] = false;

			if (isset($_GET['edit']) && !empty($_GET['edit'])) {
				$tpl['detail'] = self::get_branch_detail((int) $_GET['edit']);
				$tpl['edit'] = true;
			}

			CarRental::view($_GET['page'], $tpl);
		} elseif ($_GET['page'] == 'carrental-pricing') {

			$tpl = array('pricing' => self::get_pricing());
			$tpl['edit'] = false;

			if (isset($_GET['edit']) && !empty($_GET['edit'])) {
				$tpl['detail'] = self::get_pricing_detail((int) $_GET['edit']);
				$tpl['edit'] = true;
			}

			CarRental::view($_GET['page'], $tpl);
		} elseif ($_GET['page'] == 'carrental-booking') {

			$tpl = array('booking' => self::get_booking(),
				'branches' => self::get_branches(),
				'fleet' => self::get_fleet());
			$tpl['edit'] = false;

			if (isset($_GET['edit']) && !empty($_GET['edit'])) {
				$tpl['detail'] = self::get_booking_detail((int) $_GET['edit']);
				$tpl['edit'] = true;
			}

			CarRental::view($_GET['page'], $tpl);
		} elseif ($_GET['page'] == 'carrental-translations') {

			include dirname(realpath(__FILE__)) . '/languages.php';
			$tpl = array('languages' => $languages);

			if (isset($_GET['language']) && !empty($_GET['language'])) {
				$tpl['translations_theme'] = self::get_theme_translations($_GET['language']);
			}

			CarRental::view($_GET['page'], $tpl);
		} elseif ($_GET['page'] == 'carrental-settings') {

			self::auto_check_plugin_update();
			$tpl = array('vehicle_categories' => self::get_vehicle_categories(),
				'pricing' => self::get_pricing('p.`name` ASC'));
			CarRental::view($_GET['page'], $tpl);
		} elseif ($_GET['page'] == 'carrental-newsletter') {

			$tpl = array('newsletter' => self::get_newsletter());
			CarRental::view($_GET['page'], $tpl);
		} elseif ($_GET['page'] == 'carrental-fleet-parameters') {
			
			$tpl = array('params' => self::get_fleet_parameters(), 'types' => self::$fleet_parameter_types);
			
			if (isset($_GET['edit']) && !empty($_GET['edit'])) {
				$tpl['detail'] = self::get_fleet_parameter_detail((int) $_GET['edit']);
				$tpl['edit'] = true;
			}
			
			CarRental::view($_GET['page'], $tpl);
		} else {

			$tpl = array('quick_info' => self::get_quick_info());

			if (isset($_GET['deleted'])) {
				$tpl['deleted'] = self::get_deleted_items();
			}

			CarRental::view('carrental', $tpl);
		}
	}
	
	
	public static function get_fleet_parameters($only_active = false) {
		global $wpdb;

		try {

			$params = $wpdb->get_results('SELECT *
												 FROM `'.$wpdb->prefix.'carrental_fleet_parameters`'.($only_active ? ' WHERE `active` = 1' : '')
										);
			return $params;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public static function get_country_list() {
		return CarRental::get_country_list();
	}

	public static function get_day_name($day) {
		return CarRental::get_day_name($day);
	}

	public static function export_language() {
		global $wpdb;

		$preklad = $wpdb->get_results($wpdb->prepare('SELECT `original`, `translation` FROM ' . CarRental::$db['translations'] . ' WHERE `lang` = %s GROUP BY `original`', $_POST['language']), ARRAY_A);

		$file = json_encode($preklad);

		header('Content-Type: text/plain');
		header("Content-Transfer-Encoding: Binary");
		header('Pragma: no-cache');
		header("Content-disposition: attachment; filename=\"carrental_language_" . $_POST['language'] . ".clng\"");
		echo $file;
		exit;
	}

	public static function import_language() {
		global $wpdb;

		try {
			if (!isset($_FILES['input_file']) || empty($_FILES['input_file']) || empty($_FILES['input_file']['tmp_name'])) {
				throw new Exception('Input language file is required.');
			}

			if (substr($_FILES['input_file']['name'], -4) != 'clng') {
				throw new Exception('Only carrental language files are accepted.');
			}

			if (!function_exists('wp_handle_upload')) {
				require_once(ABSPATH . 'wp-admin/includes/file.php');
			}

			$uploadedfile = $_FILES['input_file'];
			$upload_overrides = array('test_form' => false, 'mimes' => array('clng' => 'text/plain'));
			$movefile = wp_handle_upload($uploadedfile, $upload_overrides);

			if ($movefile) {
				$lng_file = $movefile['file'];
			} else {
				throw new Exception('Upload error.');
			}
			$json = file_get_contents($lng_file);
			$lng_array = json_decode($json);

			if (!is_array($lng_array) || empty($lng_array)) {
				throw new Exception('Language file error.');
			}

			foreach ($lng_array as $lng_line) {
				$lng_line = (array) $lng_line;

				if (trim($lng_line['original']) == '' || trim($lng_line['translation']) == '') {
					continue;
				}

				$line = $wpdb->query($wpdb->prepare('INSERT INTO `' . CarRental::$db['translations'] . '`
					SET `lang` = %s,
						`original` = %s,
						`translation` = %s
						ON DUPLICATE KEY UPDATE `translation` = %s
						', trim($_POST['language']), trim($lng_line['original']), trim($lng_line['translation']), trim($lng_line['translation'])
				));
			}

			unlink($lng_file);
			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	/**
	 * 	Add Branch from $_POST
	 */
	public function add_branch() {
		global $wpdb;

		try {

			$edit = false;
			if (isset($_POST['id_branch']) && !empty($_POST['id_branch'])) {
				$edit = true;
				$id_branch = (int) $_POST['id_branch'];
				if ($id_branch <= 0) {
					throw new Exception("Invalid Branch ID");
				}
			}

			// Save uploaded picture
			$picture_filename = (isset($_POST['current_picture']) ? $_POST['current_picture'] : NULL);
			if (isset($_FILES['picture']) && !empty($_FILES['picture']['tmp_name'])) {
				if (!function_exists('wp_handle_upload')) {
					require_once(ABSPATH . 'wp-admin/includes/file.php');
				}
				$uploadedfile = $_FILES['picture'];
				$upload_overrides = array('test_form' => false);
				$movefile = wp_handle_upload($uploadedfile, $upload_overrides);
				if ($movefile) {
					$picture_filename = $movefile['url'];
				}
			}
			if (isset($_POST['delete_picture']) && $_POST['delete_picture'] == 1) {
				$picture_filename = '';
			}

			// Save Branch to DB
			$arr = array('name' => $_POST['name'],
				'country' => $_POST['country'],
				'state' => $_POST['state'],
				'city' => $_POST['city'],
				'zip' => $_POST['zip'],
				'street' => $_POST['street'],
				'email' => $_POST['email'],
				'phone' => $_POST['phone'],
				'gps' => $_POST['gps'],
				'description' => $_POST['description'],
				'picture' => $picture_filename,
				'active' => $_POST['active'],
				'is_default' => (int) $_POST['is_default'],
				'enter_hours' => serialize($_POST['enter_hours']),
				'return_hours' => serialize($_POST['return_hours']),
				'specific_times' => isset($_POST['specific_times']) ? 1 : 0,
				'bid' => $_POST['bid']
			);

			if ($edit == true) {

				// Update Branch
				$arr['updated'] = Date('Y-m-d H:i:s');
				$wpdb->update(CarRental::$db['branch'], $arr, array('id_branch' => $id_branch));

				// Delete previous Branch hours
				$wpdb->delete(CarRental::$db['branch_hours'], array('id_branch' => $id_branch), array('%d'));
			} else {

				// Add Branch
				$wpdb->insert(CarRental::$db['branch'], $arr);
				$id_branch = $wpdb->insert_id;
			}

			if ((int) $_POST['is_default'] == 1) {
				// set this branch as default - set other branches to not default
				$wpdb->query('UPDATE ' . CarRental::$db['branch'] . ' SET `is_default` = 0 WHERE `id_branch` <> ' . (int) $id_branch);
			}

			// Save Business Hours to DB
			if (!empty($_POST['hours']['from'])) {
				foreach ($_POST['hours']['from'] as $key => $val) {
					$from = $val;
					$to = $_POST['hours']['to'][$key];
					$from_2 = !empty($_POST['hours']['from_2'][$key]) ? $_POST['hours']['from_2'][$key] : null;
					$to_2 = !empty($_POST['hours']['to_2'][$key]) ? $_POST['hours']['to_2'][$key] : null;

					if (!empty($from) && !empty($to)) {

						$day = $key + 1;
						$arr = array('id_branch' => $id_branch,
							'day' => $day,
							'hours_from' => $from . ':00',
							'hours_to' => $to . ':00',
							'hours_from_2' => $from_2,
							'hours_to_2' => $to_2);

						$wpdb->insert(CarRental::$db['branch_hours'], $arr);
					}
				}
			}

			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	
	private static function get_branch($id, $branches) {
		if (empty($branches)) {
			return false;
		}
		foreach ($branches as $b) {
			if ($b->id_branch == $id) {
				return $b;
			}
		}
		return false;
	}

	/**
	 * 	Get branches
	 */
	public function get_branches() {
		global $wpdb;

		try {

			$where = '`deleted` IS NULL';
			$order = '`ordering` DESC';

			if (isset($_GET['deleted'])) {
				$where = '`deleted` IS NOT NULL';
				$order = '`deleted` DESC';
			}

			$branches = $wpdb->get_results('SELECT * FROM `' . CarRental::$db['branch'] . '` WHERE ' . $where . ' ORDER BY ' . $order);
			
			if ($branches && !empty($branches)) {
				foreach ($branches as $key => $val) {

					$branches[$key]->hours = $wpdb->get_results(
						$wpdb->prepare('SELECT * FROM `' . CarRental::$db['branch_hours'] . '`
																								 	 WHERE `id_branch` = %d ORDER BY `day` ASC', $val->id_branch));
				}
			}

			return $branches;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function get_branch_detail($id_branch) {
		global $wpdb;

		try {

			$branches = $wpdb->get_row($wpdb->prepare('SELECT * FROM `' . CarRental::$db['branch'] . '` WHERE `id_branch` = %d', $id_branch));
			$hours = $wpdb->get_results($wpdb->prepare('SELECT * FROM `' . CarRental::$db['branch_hours'] . '` WHERE `id_branch` = %d', $id_branch));
			$branches->hours = array();
			
			$branches->enter_hours = unserialize($branches->enter_hours);
			$branches->return_hours = unserialize($branches->return_hours);
			
			if ($hours && !empty($hours)) {
				foreach ($hours as $key => $val) {
					$branches->hours[$val->day] = array('hours_from' => substr($val->hours_from, 0, 5),
						'hours_to' => substr($val->hours_to, 0, 5));
					if (isset($val->hours_from_2) && $val->hours_from_2 != '00:00:00') {
						$branches->hours[$val->day]['hours_from_2'] = substr($val->hours_from_2, 0, 5);
						$branches->hours[$val->day]['hours_to_2'] = substr($val->hours_to_2, 0, 5);
					}
				}
			}

			return $branches;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function copy_branch($id_branch) {
		global $wpdb;

		try {

			$branches = $wpdb->get_row($wpdb->prepare('SELECT * FROM `' . CarRental::$db['branch'] . '` WHERE `id_branch` = %d', $id_branch));
			$hours = $wpdb->get_results($wpdb->prepare('SELECT * FROM `' . CarRental::$db['branch_hours'] . '` WHERE `id_branch` = %d', $id_branch));

			// Save Branch to DB
			$arr = array('name' => $branches->name . ' (copy)',
				'country' => $branches->country,
				'state' => $branches->state,
				'city' => $branches->city,
				'zip' => $branches->zip,
				'street' => $branches->street,
				'email' => $branches->email,
				'phone' => $branches->phone,
				'description' => $branches->description,
				'picture' => $branches->picture,
				'active' => $branches->active,
				'enter_hours' => $branches->enter_hours,
				'return_hours' => $branches->return_hours,
				'specific_times' => $branches->specific_times,
				'bid' => $branches->bid
			);
			$wpdb->insert(CarRental::$db['branch'], $arr);
			$id_branch = $wpdb->insert_id;

			// Save Business Hours to DB
			if (!empty($hours)) {
				foreach ($hours as $key => $val) {
					$arr = array('id_branch' => $id_branch,
						'day' => $val->day,
						'hours_from' => $val->hours_from,
						'hours_to' => $val->hours_to);
					$wpdb->insert(CarRental::$db['branch_hours'], $arr);
				}
			}

			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function delete_branch($id_branch) {
		global $wpdb;

		try {

			$arr = array('deleted' => Date('Y-m-d H:i:s'));
			$wpdb->update(CarRental::$db['branch'], $arr, array('id_branch' => $id_branch), array('%s'));
			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function restore_branch($id_branch) {
		global $wpdb;

		try {

			$wpdb->query('UPDATE ' . CarRental::$db['branch'] . ' SET `deleted` = NULL WHERE `id_branch` = ' . (int) $id_branch);
			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function ajax_save_branch_order() {
		global $wpdb;
		$r = count($_POST['ordering']);
		foreach ($_POST['ordering'] as $o) {
			$wpdb->query($wpdb->prepare('UPDATE ' . CarRental::$db['branch'] . ' SET `ordering` = %d WHERE `id_branch` = %d LIMIT 1', $r, (int) $o));
			$r--;
		}
		echo '1';
		exit;
	}

	/**
	 * 	EXTRAS
	 */
	public function add_extras() {
		global $wpdb;

		try {

			$edit = false;
			if (isset($_POST['id_extras']) && !empty($_POST['id_extras'])) {
				$edit = true;
				$id_extras = (int) $_POST['id_extras'];
				if ($id_extras <= 0) {
					throw new Exception("Invalid Extras ID");
				}
			}

			// Save uploaded picture
			$picture_filename = (isset($_POST['current_picture']) ? $_POST['current_picture'] : NULL);
			if (isset($_FILES['picture']) && !empty($_FILES['picture']['tmp_name'])) {
				if (!function_exists('wp_handle_upload')) {
					require_once(ABSPATH . 'wp-admin/includes/file.php');
				}
				$uploadedfile = $_FILES['picture'];
				$upload_overrides = array('test_form' => false);
				$movefile = wp_handle_upload($uploadedfile, $upload_overrides);
				if ($movefile) {
					$picture_filename = $movefile['url'];
				}
			}

			// Save Extras to DB
			$arr = array('name' => $_POST['name'],
				'name_admin' => $_POST['name_admin'],
				'name_translations' => serialize($_POST['name_translations']),
				'description' => $_POST['description'],
				'description_translations' => serialize($_POST['description_translations']),
				'global_pricing_scheme' => $_POST['global_pricing_scheme'],
				'internal_id' => $_POST['internal_id'],
				'max_additional_drivers' => $_POST['max_additional_drivers'],
				'mandatory' => (isset($_POST['mandatory']) ? (int) $_POST['mandatory'] : 0),
				'picture' => $picture_filename
			);

			if ($edit == true) {

				// Update Extras
				$arr['updated'] = Date('Y-m-d H:i:s');
				$wpdb->update(CarRental::$db['extras'], $arr, array('id_extras' => $id_extras));

				// Delete previous Price schemes
				$wpdb->delete(CarRental::$db['extras_pricing'], array('id_extras' => $id_extras), array('%d'));
			} else {

				// Add Extras
				$wpdb->insert(CarRental::$db['extras'], $arr);
				$id_extras = $wpdb->insert_id;
			}

			// Set Pricing schemes
			if (isset($_POST['pricing']) && !empty($_POST['pricing'])) {
				$priority = 1;
				foreach ($_POST['pricing'] as $key => $id_pricing) {
					if ((int) $id_pricing > 0) {
						$from = ((isset($_POST['pricing_from'][$key]) && !empty($_POST['pricing_from'][$key])) ? Date('Y-m-d', strtotime($_POST['pricing_from'][$key])) : NULL);
						$to = ((isset($_POST['pricing_to'][$key]) && !empty($_POST['pricing_to'][$key])) ? Date('Y-m-d', strtotime($_POST['pricing_to'][$key])) : NULL);
						$arr = array('id_extras' => $id_extras,
							'id_pricing' => $id_pricing,
							'priority' => $priority,
							'valid_from' => $from,
							'valid_to' => $to);
						$wpdb->insert(CarRental::$db['extras_pricing'], $arr);
						++$priority;
					}
				}
			}

			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	/**
	 * 	Get extras
	 */
	public function get_extras() {
		global $wpdb;

		try {

			$where = 'e.`deleted` IS NULL';
			$order = 'e.`id_extras` DESC';

			if (isset($_GET['deleted'])) {
				$where = 'e.`deleted` IS NOT NULL';
				$order = 'e.`deleted` DESC';
			}

			$extras = $wpdb->get_results('SELECT e.*, p.`name` as `pricing_name`, p.`type` as `pricing_type`,
																			(SELECT COUNT(*) FROM `' . CarRental::$db['extras_pricing'] . '` pr WHERE pr.`id_extras` = e.`id_extras`) as `pricing_count`
																		FROM `' . CarRental::$db['extras'] . '` e
																		LEFT JOIN `' . CarRental::$db['pricing'] . '` p ON p.`id_pricing` = e.`global_pricing_scheme`
																		WHERE ' . $where . ' ORDER BY ' . $order);

			return $extras;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function get_extras_detail($id_extras) {
		global $wpdb;

		try {

			$extras = $wpdb->get_row($wpdb->prepare('SELECT * FROM `' . CarRental::$db['extras'] . '` WHERE `id_extras` = %d', $id_extras));

			// Pricing schemes
			$extras->pricing = $wpdb->get_results($wpdb->prepare('SELECT * FROM `' . CarRental::$db['extras_pricing'] . '` WHERE `id_extras` = %d ORDER BY `priority` ASC', $id_extras));

			return $extras;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function copy_extras($id_extras) {
		global $wpdb;

		try {

			$extras = $wpdb->get_row($wpdb->prepare('SELECT * FROM `' . CarRental::$db['extras'] . '` WHERE `id_extras` = %d', $id_extras));

			// Save Extras to DB
			$arr = array('name' => $extras->name . ' (copy)',
				'description' => $extras->description,
				'global_pricing_scheme' => $extras->global_pricing_scheme,
				'internal_id' => $extras->internal_id,
				'max_additional_drivers' => $extras->max_additional_drivers,
				'picture' => $extras->picture
			);

			$wpdb->insert(CarRental::$db['extras'], $arr);

			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function delete_extras($id_extras) {
		global $wpdb;

		try {

			$arr = array('deleted' => Date('Y-m-d H:i:s'));
			$wpdb->update(CarRental::$db['extras'], $arr, array('id_extras' => $id_extras), array('%s'));
			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function restore_extras($id_extras) {
		global $wpdb;

		try {

			$wpdb->query('UPDATE ' . CarRental::$db['extras'] . ' SET `deleted` = NULL WHERE `id_extras` = ' . (int) $id_extras);
			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	
	/**
	 * 	FLEET PARAMETERS
	 */
	public function add_fleet_parameter() {
		global $wpdb;

		try {

			$edit = false;
			if (isset($_POST['id_fleet_parameter']) && !empty($_POST['id_fleet_parameter'])) {
				$edit = true;
				$id_fleet_parameter = (int) $_POST['id_fleet_parameter'];
				if ($id_fleet_parameter <= 0) {
					throw new Exception("Invalid Fleet Parameter ID");
				}
			}
			
			if (trim($_POST['name']['gb'] == '')) {
				throw new Exception("Name of parameter in english must be set.");
			}

			if (isset($_POST['values']) && !empty($_POST['values'])) {
				$values = array();
				$langs = array();
				foreach ($_POST['values'] as $lng => $param) {
					$langs[] = $lng;
					$values[$lng] = array();
				}
				foreach ($_POST['values'] as $lng => $params) {					
					foreach ($params as $id => $param) {
						if (trim($param) == '') {
							// if is empty in all languages then unset it
							$empty = true;
							foreach ($langs as $l) {
								if (isset($_POST['values'][$l][$id]) && trim($_POST['values'][$l][$id]) != '') {
									$empty = false;
									break;
								}
							}
							if ($empty) {
								unset($_POST['values'][$lng][$id]);
								continue;
							}
						}
						$values[$lng][$id] = $param;
					}
				}
				$values_array = $values['gb'];
				$values = serialize($values);
			} else {
				$values = null;
				$values_array = array();
			}
			

			// Save Fleet to DB
			$arr = array('name' => serialize($_POST['name']),
				'values' => $values,
				'type' => $_POST['type'],
				'range_from' => $_POST['range_from'],
				'range_to' => $_POST['range_to'],
				'active' => $_POST['active']
			);

			if ($edit == true) {
				// Update Fleet Parameter
				unset($arr['type']);
				
				// get old parameter values
				$fleet_parameter = $wpdb->get_row($wpdb->prepare('SELECT * FROM `'.$wpdb->prefix.'carrental_fleet_parameters` WHERE `id_fleet_parameter` = %d LIMIT 1', $id_fleet_parameter));
				// check if any values was deleted
				$old_values = $fleet_parameter->values != '' ? unserialize($fleet_parameter->values) : array();
				if (is_array($old_values) && isset($old_values['gb'])) {
					$to_delete = array();
					foreach ($old_values['gb'] as $k => $v) {
						if (!isset($values_array[$k])) {
							$to_delete[] = (int)$k;
						}
					}
					if (count($to_delete) > 0) {
						$wpdb->query($wpdb->prepare('DELETE FROM `'.$wpdb->prefix.'carrental_fleet_parameters_values` WHERE `fleet_parameters_id` = %d AND `value` IN ('.implode(',', $to_delete).')', $id_fleet_parameter));
					}
				}
				
				$wpdb->update($wpdb->prefix."carrental_fleet_parameters", $arr, array('id_fleet_parameter' => $id_fleet_parameter));
			} else {

				// Add Fleet Parameter
				$wpdb->insert($wpdb->prefix."carrental_fleet_parameters", $arr);
				$id_fleet_parameter = $wpdb->insert_id;
			}

			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	
	public function get_fleet_parameter_values($id_fleet) {
		global $wpdb;
		$return = array();
		$param_values = $wpdb->get_results($wpdb->prepare('SELECT * FROM `'.$wpdb->prefix.'carrental_fleet_parameters_values` WHERE `fleet_id` = %d', $id_fleet));
		foreach ($param_values as $p) {
			$return[$p->fleet_parameters_id] = $p->value;
		}
		return $return;
	}
	
	public function get_fleet_parameter_detail($id_fleet_parameter) {
		global $wpdb;

		try {

			$fleet_parameter = $wpdb->get_row($wpdb->prepare('SELECT *
																							FROM `'.$wpdb->prefix.'carrental_fleet_parameters` WHERE `id_fleet_parameter` = %d', $id_fleet_parameter));

			return $fleet_parameter;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	
	public function copy_fleet_parameter($id_fleet_parameter) {
		global $wpdb;

		try {

			$param = $wpdb->get_row($wpdb->prepare('SELECT * FROM `'.$wpdb->prefix.'carrental_fleet_parameters` WHERE `id_fleet_parameter` = %d', $id_fleet_parameter));

			$name = unserialize($param->name);
			$name['gb'] = $name['gb'].' (copy)';
			// Save Fleet Parameter to DB
			$arr = array('name' => serialize($name),
				'values' => $param->values,
				'type' => $param->type,
				'range_from' => $param->range_from,
				'range_to' => $param->range_to,
				'active' => $param->active
			);

			$wpdb->insert($wpdb->prefix.'carrental_fleet_parameters', $arr);

			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function delete_fleet_parameter($id_fleet_parameter) {
		global $wpdb;

		try {

			$wpdb->query('DELETE FROM `'.$wpdb->prefix.'carrental_fleet_parameters` WHERE `id_fleet_parameter` = ' . (int) $id_fleet_parameter . ' LIMIT 1');
			$wpdb->query('DELETE FROM `'.$wpdb->prefix.'carrental_fleet_parameters_values` WHERE `fleet_parameters_id` = ' . (int) $id_fleet_parameter);
			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	/**
	 * 	FLEET
	 */
	public function add_fleet() {
		global $wpdb;

		try {

			$edit = false;
			if (isset($_POST['id_fleet']) && !empty($_POST['id_fleet'])) {
				$edit = true;
				$id_fleet = (int) $_POST['id_fleet'];
				if ($id_fleet <= 0) {
					throw new Exception("Invalid Fleet ID");
				}
			}

			// Save uploaded picture
			$picture_filename = (isset($_POST['current_picture']) ? $_POST['current_picture'] : NULL);
			if (isset($_FILES['picture']) && !empty($_FILES['picture']['tmp_name'])) {
				if (!function_exists('wp_handle_upload')) {
					require_once(ABSPATH . 'wp-admin/includes/file.php');
				}
				$uploadedfile = $_FILES['picture'];
				$upload_overrides = array('test_form' => false);
				$movefile = wp_handle_upload($uploadedfile, $upload_overrides);
				if ($movefile) {
					$picture_filename = $movefile['url'];
				}
			}
			
			$similar_cars = array();
			if (isset($_POST['similar_cars']) && is_array($_POST['similar_cars'])) {
				foreach ($_POST['similar_cars'] as $carId) {
					$similar_cars[$carId] = $carId;
				}
				$similar_cars = serialize($similar_cars);
			} else {
				$similar_cars = null;
			}

			if (isset($_POST['additional-pictures']) && !empty($_POST['additional-pictures'])) {
				$additionalPictures = serialize($_POST['additional-pictures']);
			} else {
				$additionalPictures = null;
			}

			if (isset($_POST['additional_parameters']) && !empty($_POST['additional_parameters'])) {
				$parameters = array();
				$langs = array();
				foreach ($_POST['additional_parameters'] as $lng => $param) {
					$langs[] = $lng;
					$parameters[$lng] = array();
				}
				foreach ($_POST['additional_parameters'] as $lng => $params) {
					$i = 1;
					foreach ($params as $id => $param) {
						if (trim($param['name']) == '') {
							// if is empty in all languages then unset it
							$empty = true;
							foreach ($langs as $l) {
								if (isset($_POST['additional_parameters'][$l][$id]['name']) && trim($_POST['additional_parameters'][$l][$id]['name']) != '') {
									$empty = false;
									break;
								}
							}
							if ($empty) {
								unset($_POST['additional_parameters'][$lng][$id]);
								continue;
							}
						}
						$parameters[$lng][$i] = $param;
						$i++;
					}
				}
				$additionalParameters = serialize($parameters);
			} else {
				$additionalParameters = null;
			}

			// Save Fleet to DB
			$arr = array('name' => $_POST['name'],
				'id_category' => $_POST['id_category'],
				'id_branch' => $_POST['id_branch'],
				'global_pricing_scheme' => $_POST['global_pricing_scheme'],
				'min_rental_time' => $_POST['min_rental_time'],
				'seats' => $_POST['seats'],
				'doors' => $_POST['doors'],
				'luggage' => $_POST['luggage'],
				'transmission' => $_POST['transmission'],
				'free_distance' => $_POST['free_distance'],
				'ac' => $_POST['ac'],
				'fuel' => $_POST['fuel'],
				'number_vehicles' => $_POST['number_vehicles'],
				'consumption' => $_POST['consumption'],
				'deposit' => $_POST['deposit'],
				'license' => $_POST['license'],
				'vin' => $_POST['vin'],
				'internal_id' => $_POST['internal_id'],
				'class_code' => $_POST['class_code'],
				'price_from' => (float)$_POST['price_from'],
				'description' => (is_array($_POST['description']) ? serialize($_POST['description']) : $_POST['description']),
				'picture' => $picture_filename,
				'additional_pictures' => $additionalPictures,
				'additional_parameters' => $additionalParameters,
				'similar_cars' => $similar_cars
			);

			if ($edit == true) {

				// Update Fleet
				$arr['updated'] = Date('Y-m-d H:i:s');
				$wpdb->update(CarRental::$db['fleet'], $arr, array('id_fleet' => $id_fleet));

				// Delete extras
				$wpdb->delete(CarRental::$db['fleet_extras'], array('id_fleet' => $id_fleet), array('%d'));

				// Delete previous Price schemes
				$wpdb->delete(CarRental::$db['fleet_pricing'], array('id_fleet' => $id_fleet), array('%d'));
				
				// Delete previous Fleet parameters
				$wpdb->delete($wpdb->prefix.'carrental_fleet_parameters_values', array('fleet_id' => $id_fleet), array('%d'));
			} else {

				// Add Fleet
				$wpdb->insert(CarRental::$db['fleet'], $arr);
				$id_fleet = $wpdb->insert_id;
			}

			// Add extras
			if (isset($_POST['extras']) && !empty($_POST['extras'])) {
				foreach ($_POST['extras'] as $kD => $vD) {
					$wpdb->insert(CarRental::$db['fleet_extras'], array('id_fleet' => $id_fleet, 'id_extras' => $vD));
				}
			}

			// Set Pricing schemes
			if (isset($_POST['pricing']) && !empty($_POST['pricing'])) {
				$priority = 1;
				foreach ($_POST['pricing'] as $key => $id_pricing) {
					$repeat = 0;
					if ((int) $id_pricing > 0) {
						$from = ((isset($_POST['pricing_from'][$key]) && !empty($_POST['pricing_from'][$key])) ? Date('Y-m-d', strtotime($_POST['pricing_from'][$key])) : NULL);
						$to = ((isset($_POST['pricing_to'][$key]) && !empty($_POST['pricing_to'][$key])) ? Date('Y-m-d', strtotime($_POST['pricing_to'][$key])) : NULL);
						$repeat = ((isset($_POST['pricing_repeat'][$key]) && $_POST['pricing_repeat'][$key] == 1) ? 1 : 0);
						$arr = array('id_fleet' => $id_fleet,
							'id_pricing' => $id_pricing,
							'priority' => $priority,
							'valid_from' => $from,
							'valid_to' => $to,
							'repeat' => $repeat);
						$wpdb->insert(CarRental::$db['fleet_pricing'], $arr);
						++$priority;
					}
				}
			}
			
			// save custom parameters
			if (isset($_POST['custom_parameters']) && !empty($_POST['custom_parameters'])) {
				foreach ($_POST['custom_parameters'] as $key => $value) {
					if (trim($value) == '' || (int)$value < 1) {
						continue;
					}
					$sql = 'INSERT INTO `' . $wpdb->prefix.'carrental_fleet_parameters_values` (`fleet_id`, `fleet_parameters_id`, `value`)
									VALUES (%d, %d, %d)';
					$wpdb->query($wpdb->prepare($sql, $id_fleet, $key, $value));
				}
			}

			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	/**
	 * return first non empty parameter name in languages
	 * @param type $i
	 * @param type $lng
	 */
	public static function fleet_placeholder_param($i, $additional_parameters) {
		if (empty(self::$available_languages)) {
			$available_languages_all = unserialize(get_option('carrental_available_languages'));
			$available_languages = array('gb');
			foreach ($available_languages_all as $l) {
				$available_languages[] = $l['country-www'];
			}
			self::$available_languages = $available_languages;
		}

		self::$additional_parameters = $additional_parameters;
		if (self::$available_languages && !empty(self::$available_languages)) {
			foreach (self::$available_languages as $key => $val) {
				if (isset(self::$additional_parameters[$val][$i]) && isset(self::$additional_parameters[$val][$i]['name']) && self::$additional_parameters[$val][$i]['name'] != '') {
					return self::$additional_parameters[$val][$i]['name'];
				}
			}
		}
		return '';
	}

	/**
	 * 	Get fleet
	 */
	public function get_fleet() {
		global $wpdb;

		try {

			$where = 'f.`deleted` IS NULL';
			$order = 'f.`ordering` DESC';

			if (isset($_GET['deleted'])) {
				$where = 'f.`deleted` IS NOT NULL';
				$order = 'f.`deleted` DESC';
			}

			$fleet = $wpdb->get_results('SELECT f.*, p.`name` as `pricing_name`, p.`type` as `pricing_type`,
																		 (SELECT GROUP_CONCAT(`id_extras`) FROM `' . CarRental::$db['fleet_extras'] . '` fe WHERE fe.`id_fleet` = f.`id_fleet`) as `extras`,
																		 (SELECT `name` FROM `' . CarRental::$db['branch'] . '` b WHERE b.`id_branch` = f.`id_branch`) as `branch_name`,
																		 (SELECT COUNT(*) FROM `' . CarRental::$db['fleet_pricing'] . '` pr WHERE pr.`id_fleet` = f.`id_fleet`) as `pricing_count`
																	 FROM `' . CarRental::$db['fleet'] . '` f
																	 LEFT JOIN `' . CarRental::$db['pricing'] . '` p ON p.`id_pricing` = f.`global_pricing_scheme`
																	 WHERE ' . $where . ' ORDER BY ' . $order);
			return $fleet;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function get_fleet_detail($id_fleet) {
		global $wpdb;

		try {

			$fleet = $wpdb->get_row($wpdb->prepare('SELECT f.*,
																								(SELECT GROUP_CONCAT(`id_extras`) FROM `' . CarRental::$db['fleet_extras'] . '` fe WHERE fe.`id_fleet` = f.`id_fleet`) as `extras`
																							FROM `' . CarRental::$db['fleet'] . '` f WHERE f.`id_fleet` = %d', $id_fleet));

			// Pricing schemes
			$fleet->pricing = $wpdb->get_results($wpdb->prepare('SELECT * FROM `' . CarRental::$db['fleet_pricing'] . '` WHERE `id_fleet` = %d ORDER BY `priority` ASC', $id_fleet));

			return $fleet;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function copy_fleet($id_fleet) {
		global $wpdb;

		try {

			$fleet = $wpdb->get_row($wpdb->prepare('SELECT * FROM `' . CarRental::$db['fleet'] . '` WHERE `id_fleet` = %d', $id_fleet));

			// Save Fleet to DB
			$arr = array('name' => $fleet->name . ' (copy)',
				'id_category' => $fleet->id_category,
				'id_branch' => $fleet->id_branch,
				'global_pricing_scheme' => $fleet->global_pricing_scheme,
				'min_rental_time' => $fleet->min_rental_time,
				'seats' => $fleet->seats,
				'doors' => $fleet->doors,
				'luggage' => $fleet->luggage,
				'transmission' => $fleet->transmission,
				'free_distance' => $fleet->free_distance,
				'ac' => $fleet->ac,
				'fuel' => $fleet->fuel,
				'number_vehicles' => $fleet->number_vehicles,
				'consumption' => $fleet->consumption,
				'deposit' => $fleet->deposit,
				'license' => $fleet->license,
				'vin' => $fleet->vin,
				'internal_id' => $fleet->internal_id,
				'class_code' => $fleet->class_code,
				'description' => $fleet->description,
				'picture' => $fleet->picture,
				'price_from' => $fleet->price_from,
				'additional_parameters' => $fleet->additional_parameters
			);

			$wpdb->insert(CarRental::$db['fleet'], $arr);

			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function delete_fleet($id_fleet) {
		global $wpdb;

		try {

			$arr = array('deleted' => Date('Y-m-d H:i:s'));
			$wpdb->update(CarRental::$db['fleet'], $arr, array('id_fleet' => $id_fleet), array('%s'));
			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function delete_fleet_from_db($id_fleet) {
		global $wpdb;

		try {

			$arr = array('deleted' => Date('Y-m-d H:i:s'));
			$wpdb->query('DELETE FROM ' . CarRental::$db['fleet'] . ' WHERE `id_fleet` = ' . (int) $id_fleet . ' LIMIT 1');
			$wpdb->query('DELETE FROM ' . CarRental::$db['fleet_extras'] . ' WHERE `id_fleet` = ' . (int) $id_fleet);
			$wpdb->query('DELETE FROM ' . CarRental::$db['fleet_pricing'] . ' WHERE `id_fleet` = ' . (int) $id_fleet);
			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function restore_fleet($id_fleet) {
		global $wpdb;

		try {

			$wpdb->query('UPDATE ' . CarRental::$db['fleet'] . ' SET `deleted` = NULL WHERE `id_fleet` = ' . (int) $id_fleet);
			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function ajax_save_fleet_order() {
		global $wpdb;
		$r = count($_POST['ordering']);
		foreach ($_POST['ordering'] as $o) {
			$wpdb->query($wpdb->prepare('UPDATE ' . CarRental::$db['fleet'] . ' SET `ordering` = %d WHERE `id_fleet` = %d LIMIT 1', $r, (int) $o));
			$r--;
		}
		echo '1';
		exit;
	}

	/**
	 * 	BOOKING
	 */
	public function add_booking() {
		global $wpdb;

		try {

			$edit = false;
			if (isset($_POST['id_booking']) && !empty($_POST['id_booking'])) {
				$edit = true;
				$id_booking = (int) $_POST['id_booking'];
				if ($id_booking <= 0) {
					throw new Exception("Invalid Booking ID");
				}
			}

			$enter_date = Date('Y-m-d H:i:s', strtotime($_POST['enter_date'] . ' ' . $_POST['enter_date_hour']));
			$return_date = Date('Y-m-d H:i:s', strtotime($_POST['return_date'] . ' ' . $_POST['return_date_hour']));
			
			$branches = self::get_branches();
			$enter_loc = self::get_branch($_POST['id_enter_branch'], $branches) ? self::get_branch($_POST['id_enter_branch'], $branches)->name : '';
			$return_loc = self::get_branch($_POST['id_return_branch'], $branches) ? self::get_branch($_POST['id_return_branch'], $branches)->name : '';

			// Save booking to DB
			$arr = array('first_name' => $_POST['first_name'],
				'last_name' => $_POST['last_name'],
				'email' => $_POST['email'],
				'phone' => $_POST['phone'],
				'street' => $_POST['street'],
				'city' => $_POST['city'],
				'zip' => $_POST['zip'],
				'country' => $_POST['country'],
				'company' => $_POST['company'],
				'vat' => $_POST['vat'],
				'flight' => $_POST['flight'],
				'license' => $_POST['license'],
				'id_card' => $_POST['id_card'],
				'enter_loc' => $enter_loc,
				'id_enter_branch' => $_POST['id_enter_branch'],
				'enter_date' => $enter_date,
				'return_loc' => $return_loc,
				'id_return_branch' => $_POST['id_return_branch'],
				'return_date' => $return_date,
				'payment_option' => $_POST['payment_option'],
				'comment' => $_POST['comment'],
				'partner_code' => $_POST['partner_code'],
				'status' => (int) $_POST['status'],
				'paid_online' => (float) $_POST['paid_online'],
			);
	
			if ((int) $_POST['change_vehicle'] > 0) {

				$vehicle = CarRental::get_vehicle_parameters((int) $_POST['change_vehicle']);
				$vehicle->consumption_metric = get_option('carrental_consumption');
				$currency = get_option('carrental_global_currency');
				$distance_metric = get_option('carrental_distance_metric');

				$vehicle_arr = array(
					'vehicle' => $vehicle->name,
					'vehicle_id' => $vehicle->id_fleet,
					'vehicle_picture' => $vehicle->picture,
					'vehicle_ac' => $vehicle->ac,
					'vehicle_luggage' => $vehicle->luggage,
					'vehicle_seats' => $vehicle->seats,
					'vehicle_fuel' => $vehicle->fuel,
					'vehicle_consumption' => $vehicle->consumption,
					'vehicle_consumption_metric' => $vehicle->consumption_metric,
					'vehicle_transmission' => $vehicle->transmission,
					'vehicle_free_distance' => $vehicle->free_distance . ' ' . $distance_metric,
					'vehicle_deposit' => $vehicle->deposit . ' ' . $currency,
				);

				$arr = array_merge($arr, $vehicle_arr);
			}

			if ($edit == true) {

				// Update booking
				$arr['updated'] = Date('Y-m-d H:i:s');
				$wpdb->update(CarRental::$db['booking'], $arr, array('id_booking' => $id_booking));
			
				// Delete drivers
				$wpdb->delete(CarRental::$db['booking_drivers'], array('id_booking' => $id_booking), array('%d'));

				// Delete prices
				$wpdb->delete(CarRental::$db['booking_prices'], array('id_booking' => $id_booking), array('%d'));
			} else {

				// Add booking
				$id_order = CarRental::generate_unique_order_id();
				$arr['id_order'] = $id_order;
				$arr['terms'] = 1;

				$wpdb->insert(CarRental::$db['booking'], $arr);
				$id_booking = $wpdb->insert_id;
			}
			
			if (isset($_POST['add_booking_emails'])) {
				switch ($_POST['status']) {
					case 1:
						// confirmed
						self::resend_email($id_booking, 'carrental_reservation_email');
						break;
					case 2:
						// pending payment
						self::resend_email($id_booking, 'carrental_email_status_pending');
						break;
					case 3:
						// panding other
						self::resend_email($id_booking, 'carrental_email_status_pending_other');
						break;
				}
			}

			// Add drivers
			if ($_POST['drv'] && !empty($_POST['drv'])) {
				foreach ($_POST['drv']['email'] as $key => $val) {
					if (!empty($val) && !empty($_POST['drv']['first_name'][$key]) && !empty($_POST['drv']['last_name'][$key]) && !empty($_POST['drv']['phone'][$key])) {
						$arr = array('id_booking' => $id_booking,
							'first_name' => $_POST['drv']['first_name'][$key],
							'last_name' => $_POST['drv']['last_name'][$key],
							'email' => $val,
							'phone' => $_POST['drv']['phone'][$key],
							'street' => $_POST['drv']['street'][$key],
							'city' => $_POST['drv']['city'][$key],
							'zip' => $_POST['drv']['zip'][$key],
							'country' => $_POST['drv']['country'][$key],
							'license' => $_POST['drv']['license'][$key],
							'id_card' => $_POST['drv']['id_card'][$key]
						);
						$wpdb->insert(CarRental::$db['booking_drivers'], $arr);
					}
				}
			}

			// Add prices
			if ($_POST['prices'] && !empty($_POST['prices'])) {
				foreach ($_POST['prices']['name'] as $key => $val) {
					if (!empty($val) && !empty($_POST['prices']['price'][$key]) && !empty($_POST['prices']['currency'][$key])) {
						$arr = array('id_booking' => $id_booking,
							'name' => $val,
							'price' => $_POST['prices']['price'][$key],
							'currency' => $_POST['prices']['currency'][$key],
						);
						$wpdb->insert(CarRental::$db['booking_prices'], $arr);
					}
				}
			}
			
			do_action( 'carrental_admin_after_booking_save', $id_booking );

			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	/**
	 * 	Get booking
	 */
	public function get_booking() {
		global $wpdb;

		try {

			$where = 'b.`deleted` IS NULL';
			$order = 'b.`enter_date` ASC';

			if (isset($_GET['deleted'])) {
				$where = 'b.`deleted` IS NOT NULL';
				$order = 'b.`deleted` DESC';
			}
			
			if (isset($_GET['q']) && trim($_GET['q']) != '') {
				$where .= $wpdb->prepare(" AND (`b`.`first_name` LIKE %s OR `b`.`last_name` LIKE %s OR `b`.`email` LIKE %s OR `b`.`phone` LIKE %s OR `b`.`id_order` LIKE %s OR `b`.`vehicle` LIKE %s OR `b`.`id_booking` LIKE %s OR MD5(CONCAT(`id_order`, %s, `email`)) = %s)", '%'.$_GET['q'].'%', '%'.$_GET['q'].'%', '%'.$_GET['q'].'%', '%'.$_GET['q'].'%', '%'.$_GET['q'].'%', '%'.$_GET['q'].'%', '%'.$_GET['q'].'%', CarRental::$hash_salt, $_GET['q']);
			}
			if (isset($_GET['filter_from']) && trim($_GET['filter_from']) != '' && strtotime($_GET['filter_from']) !== false) {
				$where .= $wpdb->prepare(" AND b.`return_date` >= %s", $_GET['filter_from']);
			} else {
				$where .= ' AND b.`return_date` >= NOW()';
			}
			
			if (isset($_GET['filter_to']) && trim($_GET['filter_to']) != '' && strtotime($_GET['filter_to']) !== false) {
				$where .= $wpdb->prepare(" AND b.`return_date` <= %s", $_GET['filter_to']);
			}

			$sql = 'SELECT b.*,
								MD5(CONCAT(b.`id_order`, "' . CarRental::$hash_salt . '", b.`email`)) as `hash`,
								(SELECT SUM(bp.`price`) FROM `' . CarRental::$db['booking_prices'] . '` bp WHERE bp.`id_booking` = b.`id_booking`) as `total_rental`,
								(SELECT bp.`currency` FROM `' . CarRental::$db['booking_prices'] . '` bp WHERE bp.`id_booking` = b.`id_booking` LIMIT 1) as `currency`
							FROM `' . CarRental::$db['booking'] . '` b
							WHERE ' . $where . '
							ORDER BY ' . $order;

			$booking = $wpdb->get_results($sql);

			return $booking;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	/**
	 * 	Get booking detail
	 */
	public function get_booking_detail($id_booking) {
		global $wpdb;

		try {

			$data = array();
			$data['info'] = $wpdb->get_row($wpdb->prepare('SELECT *, MD5(CONCAT(`id_order`, "' . CarRental::$hash_salt . '", `email`)) as `hash` FROM `' . CarRental::$db['booking'] . '`
																										 WHERE `id_booking` = %d', $id_booking));

			if ($data['info'] && !empty($data['info'])) {

				$data['prices'] = $wpdb->get_results($wpdb->prepare('SELECT * FROM `' . CarRental::$db['booking_prices'] . '`
																										 		 		 WHERE `id_booking` = %d', $data['info']->id_booking));

				$data['drivers'] = $wpdb->get_results($wpdb->prepare('SELECT * FROM `' . CarRental::$db['booking_drivers'] . '`
																										 		 			WHERE `id_booking` = %d', $data['info']->id_booking));
			}

			return $data;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	
	/**
	 * 	Resend confirmation email
	 */
	public static function resend_email($id_booking, $email_type = 'carrental_reservation_email') {
		global $wpdb;

		try {
			$data = $wpdb->get_row($wpdb->prepare('SELECT * FROM `' . CarRental::$db['booking'] . '` WHERE `id_booking` = %d', $id_booking), ARRAY_A);
			// Send e-mail
			if (isset($data['lng']) && !empty($data['lng'])) {
				$emailBody = get_option($email_type.'_' . $data['lng']);
				$emailSubject = get_option($email_type.'_subject_' . $data['lng']);
				if ($emailBody == '') {
					$emailBody = get_option($email_type.'_en_GB');
				}
				if ($emailSubject == '') {
					$emailSubject = get_option($email_type.'_subject_en_GB');
				}
			} else {
				$emailBody = get_option($email_type.'_en_GB');
				$emailSubject = get_option($email_type.'_subject_en_GB');
			}
			
			$prices = $wpdb->get_results($wpdb->prepare('SELECT * FROM `' . CarRental::$db['booking_prices'] . '` WHERE `id_booking` = %d', $id_booking));
			$total_amount = 0;
			foreach ($prices as $key => $val) {
				$total_amount += number_format($val->price, 2, '.', '');
			}
			
			if (!empty($emailBody)) {
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

				if ($data) {
					$order_id = md5($data['id_order'] . CarRental::$hash_salt . $data['email']);
					$emailBody = str_replace('[CustomerName]', $data['first_name'] . " " . $data['last_name'], $emailBody);
					$emailBody = str_replace('[ReservationDetails]', $data['vehicle'] . ', ' . $date_from . ' (' . $data['enter_loc'] . ') - ' . $date_to . ' (' . $data['return_loc'] . ')', $emailBody);
					$emailBody = str_replace('[ReservationNumber]', $data['id_order'], $emailBody);
					$emailBody = str_replace('[ReservationLink]', home_url() . '?page=carrental&summary=' . $order_id, $emailBody);
					$emailBody = str_replace('[ReservationLinkStart]', '<a href="' . home_url() . '?page=carrental&summary=' . $order_id . '">', $emailBody);
					$emailBody = str_replace('[ReservationLinkEnd]', '</a>', $emailBody);
					$emailBody = str_replace('[Car]', $data['vehicle'], $emailBody);
					$emailBody = str_replace('[pickupdate]', $date_from, $emailBody);
					$emailBody = str_replace('[dropoffdate]', $date_to, $emailBody);
					$emailBody = str_replace('[pickup_location]', $data['enter_loc'], $emailBody);
					$emailBody = str_replace('[dropoff_location]', $data['return_loc'], $emailBody);
					$emailBody = str_replace('[total_payment]', round($total_amount,2), $emailBody);
					$emailBody = str_replace('[rate]', round($total_amount / $diff_days, 2), $emailBody);
					$emailBody = str_replace('[rental_days]', $diff_days, $emailBody);
					$emailBody = '<html><body>' . $emailBody . '</body></html>';
					$emailBody = nl2br($emailBody);

					$recipient = $data['email'];
					if ($emailSubject == '') {
						$subject =  "Reservation confirmation #" . $data['id_order'];
					} else {
						$subject = $emailSubject;
						$subject = str_replace('[CustomerName]', $data['first_name'] . " " . $data['last_name'], $subject);
						$subject = str_replace('[ReservationDetails]', $data['vehicle'] . ', ' . $date_from . ' (' . $data['enter_loc'] . ') - ' . $date_to . ' (' . $data['return_loc'] . ')', $subject);
						$subject = str_replace('[ReservationNumber]', $data['id_order'], $subject);
						$subject = str_replace('[ReservationLink]', home_url() . '?page=carrental&summary=' . $order_id, $subject);
						$subject = str_replace('[ReservationLinkStart]', '<a href="' . home_url() . '?page=carrental&summary=' . $order_id . '">', $subject);
						$subject = str_replace('[ReservationLinkEnd]', '</a>', $subject);
						$subject = str_replace('[Car]', $data['vehicle'], $subject);
						$subject = str_replace('[pickupdate]', $date_from, $subject);
						$subject = str_replace('[dropoffdate]', $date_to, $subject);
						$subject = str_replace('[pickup_location]', $data['enter_loc'], $subject);
						$subject = str_replace('[dropoff_location]', $data['return_loc'], $subject);
						$subject = str_replace('[total_payment]', round($total_amount,2), $subject);
						$subject = str_replace('[rate]', round($total_amount / $diff_days, 2), $subject);
						$subject = str_replace('[rental_days]', $diff_days, $subject);
					}
					
					$company = unserialize(get_option('carrental_company_info'));

					$email = ((isset($company['email']) && !empty($company['email'])) ? $company['email'] : 'admin@' . $_SERVER['SERVER_NAME']);
					$name = ((isset($company['name']) && !empty($company['name'])) ? $company['name'] : 'Car Rental WP Plugin');

					add_filter('wp_mail_content_type', create_function('', 'return "text/html"; '));
					add_filter('wp_mail_from', create_function('', 'return "' . $email . '"; '));
					add_filter('wp_mail_from_name', create_function('', 'return "' . $name . '"; '));					
					
					$attachments = array();
					$attachments = apply_filters('carrental_email_attachments', $attachments, $data['id_order']);
					
					$res = wp_mail($recipient, $subject, $emailBody, '', $attachments);
					return true;
				}
			}		

		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	/**
	 * 	Copy booking
	 */
	public function copy_booking($id_booking) {
		global $wpdb;

		try {

			$booking = $wpdb->get_row($wpdb->prepare('SELECT * FROM `' . CarRental::$db['booking'] . '` WHERE `id_booking` = %d', $id_booking));
			$prices = $wpdb->get_results($wpdb->prepare('SELECT * FROM `' . CarRental::$db['booking_prices'] . '` WHERE `id_booking` = %d', $id_booking));
			$drivers = $wpdb->get_results($wpdb->prepare('SELECT * FROM `' . CarRental::$db['booking_drivers'] . '` WHERE `id_booking` = %d', $id_booking));

			// Save Branch to DB
			$arr = array();
			foreach ($booking as $key => $val) {
				$arr[$key] = (!is_null($val) ? $val : NULL);
			}

			// Generate new Order ID
			$arr['id_order'] = CarRental::generate_unique_order_id();

			unset($arr['id_booking']);
			unset($arr['updated']);
			unset($arr['deleted']);
			$wpdb->insert(CarRental::$db['booking'], $arr);
			$id_booking = $wpdb->insert_id;

			// Save Prices
			if (!empty($prices)) {
				foreach ($prices as $key => $val) {
					$arr = array('id_booking' => $id_booking,
						'name' => $val->name,
						'price' => $val->price,
						'currency' => $val->currency);
					$wpdb->insert(CarRental::$db['booking_prices'], $arr);
				}
			}

			// Save Drivers
			if (!empty($drivers)) {
				foreach ($drivers as $key => $val) {
					$arr = array();
					foreach ($val as $kD => $vD) {
						$arr[$kD] = $vD;
					}
					$arr['id_booking'] = $id_booking;
					unset($arr['id_driver']);
					$wpdb->insert(CarRental::$db['booking_drivers'], $arr);
				}
			}

			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	/**
	 * 	Delete booking
	 */
	public function delete_booking($id_booking) {
		global $wpdb;

		try {

			$arr = array('deleted' => Date('Y-m-d H:i:s'));
			$wpdb->update(CarRental::$db['booking'], $arr, array('id_booking' => $id_booking), array('%s'));
			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	/**
	 * 	Delete booking from database
	 */
	public function delete_booking_total($id_booking) {
		global $wpdb;

		try {


			$wpdb->delete(CarRental::$db['booking'], array('id_booking' => $id_booking), array('%d'));
			$wpdb->delete(CarRental::$db['booking_drivers'], array('id_booking' => $id_booking), array('%d'));
			$wpdb->delete(CarRental::$db['booking_prices'], array('id_booking' => $id_booking), array('%d'));
			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	/**
	 * 	Restore booking
	 */
	public function restore_booking($id_booking) {
		global $wpdb;

		try {

			$wpdb->query('UPDATE ' . CarRental::$db['booking'] . ' SET `deleted` = NULL WHERE `id_booking` = ' . (int) $id_booking);
			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	/**
	 * 	Update settings
	 */
	public function update_settings() {
		try {

			// Update easy options
			$opts = array('carrental_type_of_rental','carrental_global_currency', 'carrental_consumption', 'carrental_delivery_price',
				'carrental_overbooking', 'carrental_any_location_search', 'carrental_paypal',
				'carrental_require_payment', 'carrental_distance_metric', 'carrental_show_vat', 'carrental_reminder_days', 'carrental_thank_you_days', 'carrental_detail_page', 'carrental_disable_time', 'carrental_min_before_days', 'carrental_max_before_days');

			foreach ($opts as $val) {
				update_option($val, $_POST[$val]);
			}

			update_option('carrental_disclaimer', serialize($_POST['carrental_disclaimer']));

			// Update available currencies
			$av_currencies = array();
			if (isset($_POST['av_currencies_cc']) && !empty($_POST['av_currencies_cc'])) {
				foreach ($_POST['av_currencies_cc'] as $key => $cc) {
					$rate = null;
					if (isset($_POST['av_currencies_rate'][$key]) && !empty($_POST['av_currencies_rate'][$key])) {
						$rate = number_format((float) $_POST['av_currencies_rate'][$key], 3);
					}
					if (!empty($cc) && !empty($rate)) {
						$av_currencies[$cc] = $rate;
					}
				}
			}
			update_option('carrental_available_currencies', serialize($av_currencies));

			// Update where to send email after booking
			$carrental_book_send_email = array('client' => 1, 'admin' => 1, 'other' => 0);
			if (!isset($_POST['carrental_book_send_email']['client'])) {
				$carrental_book_send_email['client'] = 0;
			}
			if (!isset($_POST['carrental_book_send_email']['admin'])) {
				$carrental_book_send_email['admin'] = 0;
			}
			
			if (isset($_POST['carrental_book_send_email']['other'])) {
				$carrental_book_send_email['other'] = 1;
			}
			
			$carrental_book_send_email['other_email'] = '';
			if (isset($_POST['carrental_book_send_email']['other_email'])) {
				$carrental_book_send_email['other_email'] = $_POST['carrental_book_send_email']['other_email'];
			}

			update_option('carrental_book_send_email', serialize($carrental_book_send_email));

			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	/**
	 * Update company info
	 */
	public function update_company_info() {
		try {

			// Update easy options
			$opts = array('name', 'id', 'vat', 'email', 'phone', 'fax', 'street', 'city', 'zip', 'country', 'web');
			$info = array();
			foreach ($opts as $val) {
				$info[$val] = $_POST[$val];
			}

			update_option('carrental_company_info', serialize($info));
			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	/**
	 * Update SMTP settings
	 */
	public function update_smtp_settings() {
		try {

			unset($_POST['save_smtp_settings']);
			update_option('carrental_smtp', serialize($_POST));
			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	/**
	 * Update API key
	 */
	public function update_api_key() {
		try {

			update_option('carrental_api_key', serialize(array('api_key' => $_POST['api_key'], 'date' => Date('Y-m-d H:i:s'))));
			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	/**
	 * 	Get Quick info for Homepage
	 */
	public function get_quick_info() {
		global $wpdb;
		try {

			$info = array();
			$info['fleet'] = $wpdb->get_var('SELECT COUNT(*) FROM `' . CarRental::$db['fleet'] . '` WHERE `deleted` IS NULL');

			$info['extras'] = $wpdb->get_var('SELECT COUNT(*) FROM `' . CarRental::$db['extras'] . '` WHERE `deleted` IS NULL');

			$info['branches'] = $wpdb->get_var('SELECT COUNT(*) FROM `' . CarRental::$db['branch'] . '` WHERE `deleted` IS NULL');

			$info['pricing'] = $wpdb->get_var('SELECT COUNT(*) FROM `' . CarRental::$db['pricing'] . '` WHERE `deleted` IS NULL AND `active` = 1');

			$info['booking_progress'] = $wpdb->get_var('SELECT COUNT(*) FROM `' . CarRental::$db['booking'] . '`
																									WHERE `enter_date` < NOW() AND `return_date` > NOW() AND `deleted` IS NULL');

			$info['booking_future'] = $wpdb->get_var('SELECT COUNT(*) FROM `' . CarRental::$db['booking'] . '`
																								WHERE `enter_date` > NOW() AND `deleted` IS NULL');

			$info['deleted'] = $wpdb->get_var('SELECT ((SELECT COUNT(*) FROM `' . CarRental::$db['fleet'] . '` WHERE `deleted` IS NOT NULL) + 
																								(SELECT COUNT(*) FROM `' . CarRental::$db['extras'] . '` WHERE `deleted` IS NOT NULL) + 
																								(SELECT COUNT(*) FROM `' . CarRental::$db['branch'] . '` WHERE `deleted` IS NOT NULL) + 
																								(SELECT COUNT(*) FROM `' . CarRental::$db['pricing'] . '` WHERE `deleted` IS NOT NULL) +
																								(SELECT COUNT(*) FROM `' . CarRental::$db['booking'] . '` WHERE `deleted` IS NOT NULL))
																								');

			return $info;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * 	Get vehicle categories
	 */
	public function get_vehicle_categories() {
		global $wpdb;

		try {

			return $wpdb->get_results('SELECT vc.*,
																	 (SELECT COUNT(*) FROM `' . CarRental::$db['fleet'] . '` f
																	  WHERE f.`id_category` = vc.`id_category` AND f.`deleted` IS NULL) as `no_vehicles`
																 FROM `' . CarRental::$db['vehicle_categories'] . '` vc
																 WHERE `deleted` IS NULL ORDER BY `id_category` ASC');
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function update_vehicle_categories() {
		global $wpdb;
		try {

			$vehicle_cats = array();

			if (isset($_POST['vehicle_categories_name']) && !empty($_POST['vehicle_categories_name'])) {
				if (!function_exists('wp_handle_upload')) {
					require_once(ABSPATH . 'wp-admin/includes/file.php');
				}

				foreach ($_POST['vehicle_categories_name'] as $key => $name) {

					$picture = '';

					// Previous picture
					if (isset($_POST['vehicle_categories_picture'][$key]) && !empty($_POST['vehicle_categories_picture'][$key])) {
						$picture = $_POST['vehicle_categories_picture'][$key];
					}

					// Save uploaded picture
					if (isset($_FILES['vehicle_categories_file']['name'][$key]) && !empty($_FILES['vehicle_categories_file']['tmp_name'][$key])) {
						$thisfile = array(
							'name' => $_FILES['vehicle_categories_file']['name'][$key],
							'type' => $_FILES['vehicle_categories_file']['type'][$key],
							'tmp_name' => $_FILES['vehicle_categories_file']['tmp_name'][$key],
							'error' => $_FILES['vehicle_categories_file']['error'][$key],
							'size' => $_FILES['vehicle_categories_file']['size'][$key]
						);
						$uploadedfile = $thisfile;
						$upload_overrides = array('test_form' => false);
						$movefile = wp_handle_upload($uploadedfile, $upload_overrides);
						if ($movefile) {
							$picture = $movefile['url'];
						}
					}

					if (isset($_POST['vehicle_categories_delete'][$key]) && $_POST['vehicle_categories_delete'][$key] == 1) {

						$wpdb->update(CarRental::$db['vehicle_categories'], array('deleted' => Date('Y-m-d H:i:s')), array('id_category' => $key), array('%s'));
					} else {

						// Save to database
						$arr = array('name' => $name,
							'picture' => $picture,
							'updated' => Date('Y-m-d H:i:s'));

						$wpdb->update(CarRental::$db['vehicle_categories'], $arr, array('id_category' => $key));
					}
				}
			}

			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	private static function wp_exist_post_by_title($title_str) {
		global $wpdb;
		return $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_posts WHERE post_title = %s", $title_str), 'ARRAY_A');
	}

	public function add_vehicle_category() {
		global $wpdb;

		try {

			// Save uploaded picture
			$picture = '';
			if (isset($_FILES['vehicle_category_picture']) && !empty($_FILES['vehicle_category_picture']['tmp_name'])) {
				if (!function_exists('wp_handle_upload')) {
					require_once(ABSPATH . 'wp-admin/includes/file.php');
				}
				$uploadedfile = $_FILES['vehicle_category_picture'];
				$upload_overrides = array('test_form' => false);
				$movefile = wp_handle_upload($uploadedfile, $upload_overrides);
				if ($movefile) {
					$picture = $movefile['url'];
				}
			}

			// Save to database
			$arr = array('name' => $_POST['vehicle_category_name'],
				'picture' => $picture);

			$wpdb->insert(CarRental::$db['vehicle_categories'], $arr, array('%s', '%s'));

			if (!self::wp_exist_post_by_title($_POST['vehicle_category_name'])) {
				$post_category = array(
					'post_title' => $_POST['vehicle_category_name'],
					'post_name' => sanitize_title($_POST['vehicle_category_name']),
					'post_content' => '[carrental_category id="' . $wpdb->insert_id . '"]',
					'post_status' => 'publish',
					'post_author' => 1,
					'post_type' => 'page',
					'page_template' => 'our-cars-template.php'
				);
				wp_insert_post($post_category);
			}

			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	public function replace_price_scheme($original_id, $new_id) {
		global $wpdb;

		try {

			// Extras
			$wpdb->query(
				$wpdb->prepare('UPDATE `' . CarRental::$db['extras'] . '`
												SET `global_pricing_scheme` = %d
												WHERE `global_pricing_scheme` = %d', $new_id, $original_id));

			$wpdb->query(
				$wpdb->prepare('UPDATE `' . CarRental::$db['extras_pricing'] . '`
												SET `id_pricing` = %d
												WHERE `id_pricing` = %d', $new_id, $original_id));

			// Fleet
			$wpdb->query(
				$wpdb->prepare('UPDATE `' . CarRental::$db['fleet'] . '`
												SET `global_pricing_scheme` = %d
												WHERE `global_pricing_scheme` = %d', $new_id, $original_id));

			$wpdb->query(
				$wpdb->prepare('UPDATE `' . CarRental::$db['fleet_pricing'] . '`
												SET `id_pricing` = %d
												WHERE `id_pricing` = %d', $new_id, $original_id));

			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * 	PRICING
	 */
	public function add_pricing() {
		global $wpdb;

		try {

			$edit = false;
			if (isset($_POST['id_pricing']) && !empty($_POST['id_pricing'])) {
				$edit = true;
				$id_pricing = (int) $_POST['id_pricing'];
				if ($id_pricing <= 0) {
					throw new Exception("Invalid Pricing ID");
				}
			}

			// Save Pricing scheme to DB
			$arr = array('type' => $_POST['type'],
				'name' => $_POST['name'],
				'currency' => $_POST['currency'],
				'onetime_price' => $_POST['onetime_price'],
				'maxprice' => $_POST['maxprice'],
				'promocode' => $_POST['promocode'],
				'active' => $_POST['active'],
				'vat' => $_POST['vat'],
				'rate_id' => $_POST['rate_id'],
				'active_days' => implode(';', $_POST['active_days']),
			);

			if ($edit == true) {

				// Update Scheme
				$arr['updated'] = Date('Y-m-d H:i:s');
				$wpdb->update(CarRental::$db['pricing'], $arr, array('id_pricing' => $id_pricing));

				// Delete previous Ranges
				$wpdb->delete(CarRental::$db['pricing_ranges'], array('id_pricing' => $id_pricing), array('%d'));
			} else {

				// Add Scheme
				$wpdb->insert(CarRental::$db['pricing'], $arr);
				$id_pricing = $wpdb->insert_id;
			}


			// TIME BASED
			if ((int) $_POST['type'] == 2) {

				// Update day ranges
				if (isset($_POST['days_price']) && !empty($_POST['days_price'])) {
					foreach ($_POST['days_price'] as $key => $price) {
						$from = (isset($_POST['days']['from'][$key]) ? (int) $_POST['days']['from'][$key] : NULL);
						$to = (isset($_POST['days']['to'][$key]) ? (int) $_POST['days']['to'][$key] : NULL);

						if ($from > 0 && $price > 0) {
							$arr = array('id_pricing' => $id_pricing,
								'type' => 1, // DAYS
								'no_from' => $from,
								'no_to' => $to,
								'price' => $price
							);

							$wpdb->insert(CarRental::$db['pricing_ranges'], $arr);
						}
					}
				}

				// Update hour ranges
				if (isset($_POST['hours_price']) && !empty($_POST['hours_price'])) {
					foreach ($_POST['hours_price'] as $key => $price) {
						$from = (isset($_POST['hours']['from'][$key]) ? (int) $_POST['hours']['from'][$key] : NULL);
						$to = (isset($_POST['hours']['to'][$key]) ? (int) $_POST['hours']['to'][$key] : NULL);

						if ($from > 0 && $price > 0) {
							$arr = array('id_pricing' => $id_pricing,
								'type' => 2, // HOURS
								'no_from' => $from,
								'no_to' => $to,
								'price' => $price
							);

							$wpdb->insert(CarRental::$db['pricing_ranges'], $arr);
						}
					}
				}
			}

			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function get_pricing($sort = NULL, $type = NULL) {
		global $wpdb;

		try {

			if (empty($sort)) {
				$sort = 'p.`id_pricing` DESC';
				if (isset($_GET['deleted'])) {
					$sort = 'p.`deleted` DESC';
				}
			}

			$where = ' p.`deleted` IS NULL ';
			if (isset($_GET['deleted'])) {
				$where = ' p.`deleted` IS NOT NULL ';
			}

			if (!empty($type)) {
				$where .= " AND p.`type` = " . (int) $type . " ";
			}



			$pricing = $wpdb->get_results('SELECT p.*,
																		   ((SELECT COUNT(*) FROM `' . CarRental::$db['extras_pricing'] . '` ep WHERE ep.`id_pricing` = p.`id_pricing`) + 
																			  (SELECT COUNT(*) FROM `' . CarRental::$db['extras'] . '` e WHERE e.`global_pricing_scheme` = p.`id_pricing`)) as `extras_usage`,
																		   ((SELECT COUNT(*) FROM `' . CarRental::$db['fleet_pricing'] . '` fp WHERE fp.`id_pricing` = p.`id_pricing`) + 
																			  (SELECT COUNT(*) FROM `' . CarRental::$db['fleet'] . '` f WHERE f.`global_pricing_scheme` = p.`id_pricing`)) as `fleet_usage`
																		   
																	 	 FROM `' . CarRental::$db['pricing'] . '` p 
																		 WHERE ' . $where . '
																		 ORDER BY ' . $sort);
			return $pricing;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function get_pricing_detail($id_pricing) {
		global $wpdb;

		try {

			$pricing = $wpdb->get_row($wpdb->prepare('SELECT p.* FROM `' . CarRental::$db['pricing'] . '` p WHERE p.`id_pricing` = %d', $id_pricing));

			// Days and hours
			$ranges = $wpdb->get_results($wpdb->prepare('SELECT pr.* FROM `' . CarRental::$db['pricing_ranges'] . '` pr WHERE pr.`id_pricing` = %d ORDER BY pr.`type`, pr.`no_from`', $id_pricing));
			if ($ranges && !empty($ranges)) {
				foreach ($ranges as $key => $val) {
					$type = (((int) $val->type == 1) ? 'days' : 'hours');
					if (!isset($pricing->$type)) {
						$pricing->$type = array();
					}

					array_push($pricing->$type, array('from' => $val->no_from,
						'to' => $val->no_to,
						'price' => $val->price));
				}
			}

			return $pricing;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function delete_pricing($id_pricing) {
		global $wpdb;

		try {

			$arr = array('deleted' => Date('Y-m-d H:i:s'));
			$wpdb->update(CarRental::$db['pricing'], $arr, array('id_pricing' => $id_pricing), array('%s'));
			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function restore_pricing($id_pricing) {
		global $wpdb;

		try {

			$wpdb->query('UPDATE ' . CarRental::$db['pricing'] . ' SET `deleted` = NULL WHERE `id_pricing` = ' . (int) $id_pricing);
			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function print_pricing_ranges($id_pricing) {
		global $wpdb;

		try {

			// Days and hours
			$ranges = $wpdb->get_results($wpdb->prepare('SELECT pr.*, p.`currency`
																									 FROM `' . CarRental::$db['pricing_ranges'] . '` pr
																									 LEFT JOIN `' . CarRental::$db['pricing'] . '` p ON p.`id_pricing` = pr.`id_pricing` 
																									 WHERE pr.`id_pricing` = %d
																									 ORDER BY pr.`type`, pr.`no_from`', $id_pricing));
			if ($ranges && !empty($ranges)) {
				$set_type = 0;
				foreach ($ranges as $key => $val) {

					if ($set_type != $val->type) {
						if ($set_type > 0) {
							echo '</table></div>';
						}
						echo '<div style="width:48%;float:left;margin-right:10px;"><h4>' . (($val->type == 1) ? 'Days range' : 'Hours range') . '</h4>';
						echo '<table class="table table-striped">';
						$set_type = $val->type;
					}

					echo '<tr>';
					echo '<td class="text-right" style="width:2em;">' . $val->no_from . '</td>';
					echo '<td class="text-center" style="width:2em;">&mdash;</td>';
					echo '<td class="text-right" style="width:2em;">' . $val->no_to . '</td>';
					echo '<td class="text-center">' . (($val->type == 1) ? 'days' : 'hours') . '</td>';
					echo '<td class="text-right"><strong>' . $val->price . '&nbsp;' . $val->currency . '</strong> ' . (($val->type == 1) ? '/ day' : '/ hour') . '</td>';
					echo '</tr>';
				}
				echo '</table>';
			} else {
				echo '<h4>Day or hour ranges are not set. Please "Modify" your Price scheme.</h4>';
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function print_onetime_price($id_pricing) {
		global $wpdb;

		try {

			$pricing = $wpdb->get_row($wpdb->prepare('SELECT p.* FROM `' . CarRental::$db['pricing'] . '` p WHERE p.`id_pricing` = %d', $id_pricing));

			if ($pricing && !empty($pricing)) {
				echo '<h5>Name: <strong>' . $pricing->name . '</strong></h5>';
				echo '<h5>One Time Price: <strong>' . $pricing->onetime_price . '&nbsp;' . $pricing->currency . '</strong></h5>';
				echo '<h5>VAT: ' . $pricing->vat . '%</h5>';
				echo '<h5>Active: ' . (($pricing->active == 1) ? 'yes' : 'no') . '</h5>';
				echo '<h5>Created: ' . $pricing->created . '</h5>';
				echo '<h5>Updated: ' . (!empty($pricing->updated) ? $pricing->updated : '&ndash;') . '</h5>';
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function print_price_schemes($type, $id) {
		global $wpdb;

		try {

			$pricing = $wpdb->get_results($wpdb->prepare('SELECT ep.*, p.`name`, p.`type`
																										FROM `' . CarRental::$db[$type . '_pricing'] . '` ep
																										INNER JOIN `' . CarRental::$db['pricing'] . '` p ON p.`id_pricing` = ep.`id_pricing`
																										WHERE ep.`id_' . $type . '` = %d
																										ORDER BY ep.`priority`', $id));

			if ($pricing && !empty($pricing)) {
				echo '<table class="table table-striped">';
				echo '<thead><tr>';
				echo '<th>Priority</th>';
				echo '<th>Name</th>';
				echo '<th>Valid from</th>';
				echo '<th>Valid to</th>';
				echo '</tr></thead><tbody>';
				foreach ($pricing as $key => $val) {
					echo '<tr>';
					echo '<td>' . $val->priority . '</td>';
					echo '<td><a href="' . esc_url(CarRental_Admin::get_page_url('carrental-pricing')) . '&amp;' . (($val->type == 1) ? 'get_onetime_price' : 'get_day_ranges') . '=' . $val->id_pricing . '" class="carrental_show_ranges">' . $val->name . '</a></td>';
					echo '<td>' . (($val->valid_from != '0000-00-00') ? $val->valid_from : '&ndash;') . '</td>';
					echo '<td>' . (($val->valid_to != '0000-00-00') ? $val->valid_to : '&ndash;') . '</td>';
					echo '</tr>';
				}
				echo '</tbody></table>';
			}
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function copy_pricing($id_pricing) {
		global $wpdb;

		try {

			$pricing = $wpdb->get_row($wpdb->prepare('SELECT * FROM `' . CarRental::$db['pricing'] . '` WHERE `id_pricing` = %d', $id_pricing));
			$ranges = $wpdb->get_results($wpdb->prepare('SELECT * FROM `' . CarRental::$db['pricing_ranges'] . '` WHERE `id_pricing` = %d', $id_pricing));

			// Save Pricing to DB
			$arr = array('type' => $pricing->type,
				'name' => $pricing->name . ' (copy)',
				'currency' => $pricing->currency,
				'onetime_price' => $pricing->onetime_price,
				'maxprice' => $pricing->maxprice,
				'promocode' => $pricing->promocode,
				'active' => $pricing->active,
				'vat' => $pricing->vat,
				'rate_id' => $pricing->rate_id,
				'active_days' => $pricing->active_days,
			);

			$wpdb->insert(CarRental::$db['pricing'], $arr);
			$id_pricing = $wpdb->insert_id;

			// Save Ranges to DB
			if (!empty($ranges)) {
				foreach ($ranges as $key => $val) {
					$arr = array('id_pricing' => $id_pricing,
						'type' => $val->type,
						'no_from' => $val->no_from,
						'no_to' => $val->no_to,
						'price' => $val->price);
					$wpdb->insert(CarRental::$db['pricing_ranges'], $arr);
				}
			}

			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	/**
	 * 	Get newsletter users
	 */
	public function get_newsletter() {
		global $wpdb;

		try {

			return $wpdb->get_results('SELECT `created`, `first_name`, `last_name`, `email`, `id_booking`
																 FROM `' . CarRental::$db['booking'] . '`
																 WHERE `newsletter` = 1
																 GROUP BY `email`
																 ORDER BY `id_booking` DESC');
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	
	/**
	 * Remove email from newsletter
	 * @param type $booking_id
	 */
	function remove_newsletter($id_booking) {
		global $wpdb;

		try {

			$wpdb->query('UPDATE ' . CarRental::$db['booking'] . ' SET `newsletter` = 0 WHERE `id_booking` = ' . (int) $id_booking.' LIMIT 1');
			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	/**
	 * Export all newsletter emails as CSV
	 * @param type $format
	 */
	private static function newsletter_export($format) {
		$newsletter = self::get_newsletter();
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=newsletter_export.csv');
		$output = fopen('php://output', 'w');
		foreach ($newsletter as $news) {
			fputcsv($output, (array) $news, ';');
		}

		exit;
	}

	/**
	 * 	TRANSLATIONS
	 */
	public function get_theme_translations($lang) {
		global $wpdb;

		try {

			$sql = 'SELECT t.`original`, tt.`translation`
							FROM `' . CarRental::$db['translations'] . '` t
							LEFT JOIN `' . CarRental::$db['translations'] . '` tt ON tt.`original` = t.`original` AND tt.`lang` = %s
							WHERE t.`lang` = "en_GB"
							ORDER BY t.`original` ASC';

			$data = $wpdb->get_results($wpdb->prepare($sql, $lang));

			$translations = array();

			if ($data && !empty($data)) {
				foreach ($data as $val) {
					if (stripslashes($val->original) == $val->original) {
						$translations[$val->original] = $val->translation;
					} else {
						// delete it from database
						$data = $wpdb->query($wpdb->prepare('DELETE FROM `' . CarRental::$db['translations'] . '` WHERE `original` = %s', $val->original));
					}
				}
			}

			return $translations;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function update_theme_translations($lang, $translations) {
		global $wpdb;

		try {

			if ($translations && !empty($translations)) {
				foreach ($translations['key'] as $key => $val) {

					$tt = (isset($translations['val'][$key]) ? $translations['val'][$key] : '');
					$sql = 'INSERT INTO `' . CarRental::$db['translations'] . '` (`lang`, `original`, `translation`)
									VALUES (%s, %s, %s)
									ON DUPLICATE KEY UPDATE
										`translation` = %s';
					$wpdb->query($wpdb->prepare($sql, $lang, stripslashes($val), stripslashes($tt), stripslashes($tt)));
				}
			}

			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function send_test_email() {


		$company = unserialize(get_option('carrental_company_info'));

		$email = ((isset($company['email']) && !empty($company['email'])) ? $company['email'] : 'admin@' . $_SERVER['SERVER_NAME']);
		$name = ((isset($company['name']) && !empty($company['name'])) ? $company['name'] : 'Car Rental WP Plugin');

		add_filter('wp_mail_content_type', create_function('', 'return "text/html"; '));
		add_filter('wp_mail_from', create_function('', 'return "' . $email . '"; '));
		add_filter('wp_mail_from_name', create_function('', 'return "' . $name . '"; '));
		$res = wp_mail($_POST['user'], "Testing e-mail from Car Rental WP plugin.", "<h1>E-mailing from the Wordpress works fine.</h1><p>Lorem ipsum dolor sit amet consectetuer metus montes Vestibulum ipsum congue. Ridiculus quis sed enim odio natoque dui et lobortis Nulla hendrerit. Eget semper Phasellus orci eu risus scelerisque tellus at Aliquam feugiat. Libero nulla eros accumsan ut dui diam et id Curabitur lacus. Phasellus odio et nunc condimentum Curabitur.</p>");
		if ($res == true) {
			echo 'Test e-mail sent.';
		} else {
			echo 'Something went wrong :(';
		}
		exit;

		// PHPmailer
		require 'phpmailer.class.php';
		require 'phpmailer.smtp.class.php';

		$mail = new PHPMailer;

		$mail->isSMTP();
		$mail->Host = $_POST['server'];
		$mail->SMTPAuth = true;
		$mail->Username = $_POST['user'];
		$mail->Password = $_POST['pass'];
		$mail->SMTPDebug = 1;
		$mail->SMTPSecure = (!empty($_POST['secure']) ? $_POST['secure'] : 'tls');
		$mail->Port = (!empty($_POST['port']) ? $_POST['port'] : 587);

		$company = unserialize(get_option('carrental_company_info'));
		if (isset($company['email']) && !empty($company['email'])) {
			$mail->From = $company['email'];
		} else {
			$mail->From = 'admin@' . $_SERVER['SERVER_NAME'];
		}

		$mail->FromName = $_SERVER['SERVER_NAME'];
		$mail->addAddress($_POST['user']);
		$mail->isHTML(true);

		$mail->Subject = "Testing e-mail from Car Rental WP plugin.";
		$emailBody = "<h1>SMTP settings works fine.</h1>";
		$mail->Body = nl2br($emailBody);
		$mail->AltBody = strip_tags($emailBody);



		/* if(!$mail->send()) {
		  echo 'Message could not be sent.';
		  echo "\n";
		  echo 'Mailer Error: ' . $mail->ErrorInfo;
		  } else {
		  echo 'Message has been sent';
		  } */
	}

	public function export_database() {
		try {
			global $wpdb;
			$sql = '-- Car Rental Wordpress Plugin' . "\n";
			$sql .= '-- Date: ' . Date('Y-m-d H:i:s') . "\n\n";

			if (isset($_POST['export_structure']) && $_POST['export_structure'] == 1) {
				foreach (CarRental::$db as $key => $table) {
					$tbl = $wpdb->get_row('SHOW CREATE TABLE ' . $table);
					$tt = 'Create Table';
					$sql .= "-- Table: " . $table . "\n";
					$sql .= "DROP TABLE " . $table . ";\n";
					$sql .= $tbl->$tt . ";" . "\n\n";
				}
			}


			if (isset($_POST['export_data']) && $_POST['export_data'] == 1) {
				foreach (CarRental::$db as $key => $table) {
					$data = $wpdb->get_results('SELECT * FROM ' . $table);
					$sql .= "-- Data for table: " . $table . "\n";

					foreach ($data as $kD => $vD) {
						$subSql = 'INSERT INTO `' . $table . '` ';

						$vD = (array) $vD;
						$subSql .= '(`' . implode('`,`', array_keys($vD)) . '`) VALUES (';
						foreach ($vD as $kE => $vE) {
							$subSql .= '"' . addslashes($vE) . '"' . ',';
						}
						$subSql = substr($subSql, 0, -1);
						$subSql .= ');';
						$subSql .= "\n";

						$sql .= $subSql;
					}
				}

				// Options data
				$options = array('carrental_available_languages', 'carrental_primary_language', 'carrental_global_currency',
					'carrental_consumption', 'carrental_delivery_price', 'carrental_available_currencies',
					'carrental_overbooking', 'carrental_any_location_search', 'carrental_paypal', 'carrental_smtp',
					'carrental_require_payment', 'carrental_distance_metric', 'carrental_company_info',
					'carrental_reservation_email_en_GB', 'carrental_terms_conditions_en_GB', 'carrental_book_send_email');
				foreach ($options as $key) {
					$value = get_option($key);
					if (!empty($value)) {
						$sql .= "INSERT INTO `" . $wpdb->prefix . "options` (`option_name`, `option_value`) VALUES ('" . addslashes($key) . "', '" . addslashes($value) . "')
										 ON DUPLICATE KEY UPDATE `option_value` = '" . addslashes($value) . "';" . "\n";
					}
				}

				$available_languages = unserialize(get_option('carrental_available_languages'));
				if ($available_languages && !empty($available_languages)) {
					foreach ($available_languages as $lang => $val) {
						$value = get_option('carrental_reservation_email_' . $lang);
						if (!empty($value)) {
							$sql .= "INSERT INTO `" . $wpdb->prefix . "options` (`option_name`, `option_value`) VALUES ('" . addslashes('carrental_reservation_email_' . $lang) . "', '" . addslashes($value) . "')
											 ON DUPLICATE KEY UPDATE `option_value` = '" . addslashes($value) . "';" . "\n";
						}

						$value = get_option('carrental_terms_conditions_' . $lang);
						if (!empty($value)) {
							$sql .= "INSERT INTO `" . $wpdb->prefix . "options` (`option_name`, `option_value`) VALUES ('" . addslashes('carrental_terms_conditions_' . $lang) . "', '" . addslashes($value) . "')
											 ON DUPLICATE KEY UPDATE `option_value` = '" . addslashes($value) . "';" . "\n";
						}
					}
				}
			}

			/* echo $sql;
			  exit; */

			return $sql;
		} catch (PDOException $e) {
			return false;
		}
	}

	public function import_demo_data() {
		global $wpdb;

		try {



			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function auto_check_plugin_update() {

		if (isset($_SESSION['carrental_auto_check_update']) && ($_SESSION['carrental_auto_check_update'] + 86400) > time()) {
			return true;
		}

		$check = unserialize(get_option('carrental_update_check'));
		if (isset($check['last']) && strtotime($check['last']) != false && (strtotime($check['last']) + 86400) < time()) {
			self::check_plugin_update();
		}
		$_SESSION['carrental_auto_check_update'] = time();

		return true;
	}

	public function check_plugin_update() {
		global $wpdb;

		try {

			$apikey = unserialize(get_option('carrental_api_key'));

			if (empty($apikey) || empty($apikey['api_key'])) {
				throw new Exception('Invalid API key.');
			}

			if (defined("CARRENTAL_UPDATE_URL")) {
				if (CARRENTAL_UPDATE_URL != '') {
					$url = CARRENTAL_UPDATE_URL . '&api=' . $apikey['api_key'] . '&url=' . urlencode($_SERVER['SERVER_NAME']) . '&server=' . urlencode($_SERVER['SERVER_NAME']) . '&version=' . urlencode(CARRENTAL_VERSION);
				} else {
					throw new Exception('Undefined update URL.');
				}
			} else {
				throw new Exception('Undefined update URL.');
			}

			$data = json_decode(@file_get_contents($url));

			if ($data && !empty($data)) {
				
				if (isset($data->err) && $data->err != '') {
					throw new Exception($data->err);
				}

				$check = array();
				$check['last'] = Date('Y-m-d H:i:s');
				$check['update_available'] = false;

				if (isset($data->api_expiration)) {
					update_option('carrental_api_key_expiration', $data->api_expiration);
				}

				if (defined("CARRENTAL_VERSION")) {
					$current_version = CARRENTAL_VERSION;
					if ($current_version != $data->version) {
						$check['new_version'] = $data->version;
						$check['new_version_date'] = $data->date;
						$check['new_version_url'] = $data->url;
						$check['update_available'] = true;
					}
				}

				update_option('carrental_update_check', serialize($check));
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

			// Backup DB
			$log .= 'Backuping database...' . "\r\n";
			$_POST['export_structure'] = $_POST['export_data'] = 1;
			file_put_contents(dirname(__FILE__) . '/backup/sql_' . $time . '.sql', self::export_database());
			$log .= 'Done: ' . Date('Y-m-d H:i:s') . "\r\n";

			// Download new files and unzip
			$log .= 'Downloading...' . "\r\n";
			$check = unserialize(get_option('carrental_update_check'));
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
			update_option('carrental_do_database_update', 1);
			$log .= 'Done: ' . Date('Y-m-d H:i:s') . "\r\n";

			update_option('carrental_update_check', '');

			@file_put_contents(dirname(__FILE__) . '/backup/log_' . $time . '.txt', $log);

			// Redirect to rewrite files
			self::set_flash_msg('success', __('Plugin was successfully updated.', 'carrental'));
			Header('Location: ' . CARRENTAL__PLUGIN_URL . 'carrental-plugin-updater.php?key=f7dc05d5&time=' . $time);
			Exit;

			return true;
		} catch (Exception $e) {
			exit($e->getMessage());
			return false;
		}
	}

	public function rrmdir($dir) {
		foreach (glob($dir . '/*') as $file) {
			if (is_dir($file))
				self::rrmdir($file);
			else
				unlink($file);
		} rmdir($dir);
	}
	
	public static function ajax_ecalypse_news() {
		global $wpdb;
		
		$data = array();
		// collect data
		$data['domain'] = $_SERVER['SERVER_NAME'];
		$data['plugin_version'] = CARRENTAL_VERSION;
		$data['wp_version'] = get_bloginfo( 'version' );
		
		$type_of_rental = get_option('carrental_type_of_rental');
		if ($type_of_rental == '') {
			$type_of_rental = 'car';
		}
		$data['type_of_rental'] = $type_of_rental;
		
		// get all carrental plugins and their versions
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$plugins_raw = get_plugins();
		$plugins = array();
		foreach ($plugins_raw as $k => $v) {
			if (strpos($k, 'carrental') !== false) {
				$plugins[$v['TextDomain']] = $v['Version'];
			}
		}
		$data['plugins'] = $plugins;
		
		// get country from location
		$branch = $wpdb->get_row('SELECT `country` FROM `' . CarRental::$db['branch'] . '` WHERE country <> \'\' LIMIT 1', ARRAY_A);
		if (isset($branch['country'])) {
			$data['country'] = $branch['country'];
		}
		
		// get last bookings
		$last_date = get_option('carrental_last_news');
		if ((int)$last_date < 1) {
			$last_date = time() - (24 * 3600 * 30);
		}
		$sql = 'SELECT b.*,
								MD5(CONCAT(b.`id_order`, "' . CarRental::$hash_salt . '", b.`email`)) as `hash`,
								(SELECT SUM(bp.`price`) FROM `' . CarRental::$db['booking_prices'] . '` bp WHERE bp.`id_booking` = b.`id_booking`) as `total_rental`,
								(SELECT bp.`currency` FROM `' . CarRental::$db['booking_prices'] . '` bp WHERE bp.`id_booking` = b.`id_booking` LIMIT 1) as `currency`
							FROM `' . CarRental::$db['booking'] . '` b
							WHERE b.`created` > \''.date('Y-m-d H:i:s', $last_date).'\'';
		
		$bookings_raw = $wpdb->get_results($sql, ARRAY_A);
		$bookings = array();
		foreach ($bookings_raw as $booking) {
			$bookings[$booking['id_booking']] = array('created' => $booking['created'], 'from' => $booking['enter_date'], 'to' => $booking['return_date'], 'total_rental' => round($booking['total_rental'],2), 'currency' => $booking['currency']);
		}
		$data['bookings'] = $bookings;
		
		//update_option('carrental_last_news', time());
		
		$send_data = array();
		$send_data['data'] = base64_encode(serialize($data));
		
		$deleted = get_option('ecalypse_deleted_feeds');
		
		$send_data['deleted'] = base64_encode($deleted);
		
		$postvars = '';
		foreach($send_data as $key => $value) {
			$postvars .= $key . "=" . $value . "&";
		}
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,ECALYPSE_NEWS_URL);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
		curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,3);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);

		$data = curl_exec ($ch);
		curl_close ($ch);
		
		echo json_encode(unserialize(base64_decode($data)));
		exit;
	}
	
	public static function ajax_feed_actions() {
		
		switch ($_POST['type']) {
			case 'delete':
				$deleted = get_option('ecalypse_deleted_feeds');
				$deleted = unserialize($deleted);
				if (!is_array($deleted)) {
					$deleted = array();
				}
				$deleted[] = $_POST['id'];
				update_option('ecalypse_deleted_feeds', serialize($deleted));
				echo 1;
			break;
			case 'confirm':
				$send_data['readed'] = (int)$_POST['id'];
				$send_data['domain'] = $_SERVER['SERVER_NAME'];
		
				$postvars = '';
				foreach($send_data as $key => $value) {
					$postvars .= $key . "=" . $value . "&";
				}

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL,ECALYPSE_NEWS_URL);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,3);
				curl_setopt($ch, CURLOPT_TIMEOUT, 20);

				$data = curl_exec ($ch);

				curl_close ($ch);
				
				if ($data == 1) {
					$deleted = get_option('ecalypse_deleted_feeds');
					$deleted = unserialize($deleted);
					if (!is_array($deleted)) {
						$deleted = array();
					}
					$deleted[] = $_POST['id'];
					update_option('ecalypse_deleted_feeds', serialize($deleted));
					echo 1;
					exit;
				}
				
				echo 0;
			break;
		}
		
		exit;
	}

}

class ExtZipArchive extends ZipArchive {

	public function addDir($location, $name, $exclude = array()) {

		if (!empty($exclude) && in_array($name, $exclude)) {
			// skip
		} else {
			$this->addEmptyDir($name);
			$this->addDirDo($location, $name, $exclude);
		}
	}

	private function addDirDo($location, $name, $exclude) {
		$name .= '/';
		$location .= '/';

		$dir = opendir($location);
		while ($file = readdir($dir)) {
			if ($file == '.' || $file == '..')
				continue;

			if (filetype($location . $file) == 'dir') {
				$this->addDir($location . $file, $name . $file, $exclude);
			} else {
				$this->addFile($location . $file, $name . $file);
			}
		}
	}
	
}
