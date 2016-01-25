jQuery(document).ready(function () {
	if (jQuery('#carrental-client-area-create-account-checkbox').length) {

		jQuery('#carrental-client-area-hidden-login p.close-win').on('click', function() {
			jQuery('#carrental-client-area-hidden-login').fadeOut(400);
			jQuery('.booking-client-area-form-overflow').fadeOut(800);
		}); 
		
		jQuery('.booking-client-area-form-overflow').on('click', function() {
			jQuery('#carrental-client-area-hidden-login').fadeOut(400);
			jQuery(this).fadeOut(800);
		}); 
		
		jQuery('#carrental-client-area-close-login-button').on('click', function(e) {
			e.preventDefault();
			jQuery('#carrental-client-area-hidden-login').fadeOut(400);
			jQuery('.booking-client-area-form-overflow').fadeOut(800);
		}); 	
	}
});