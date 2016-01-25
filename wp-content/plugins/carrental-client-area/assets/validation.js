if (jQuery('#carrental-client-area-create-account-checkbox').length) {
	if (jQuery('#carrental-client-area-create-account-checkbox').is(':checked')) {
		// test if email already exists
		var email = jQuery('input.control-input[name=email]').val();
		r_email	= new RegExp("^([a-zA-Z0-9_.-]+@([a-zA-Z0-9_-]+\.)+[a-z]{2,4}){0,1}$");
		var errors = [];
		if (!r_email.test(email)) {
			errors.push('You have to enter valid email.');
			jQuery('#carrental_confirm_errors').html('<li>' + errors.join('</li><li>') + '</li>');
			return false;
		}

		if (jQuery('#carrental-client-area-password').val() != '') {
			return true;
		}

		var submit_form = false;
		jQuery.ajax({
			url: ajaxurl,
			type: "POST",
			cache: false,
			dataType: 'json',
			async: false,
			data: 'fe_ajax=1&email=' + email + '&action=carrental_client_area_check_user',
			success: function (data) {
				if (data['status'] == 0) {
					submit_form = true;
					return true;
				}
				// email login already exists = show login dialog
				jQuery('#carrental-client-area-span-email').text(email);
				jQuery('#carrental-client-area-hidden-login').fadeIn(800); 
				jQuery('.booking-client-area-form-overflow').fadeIn(400); 
			}
		});
		if (submit_form) {
			return true;
		} else {
			return false;
		}
	} else {
		return true;
	}
}