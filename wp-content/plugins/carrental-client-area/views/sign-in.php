<div class="carrental-client-area-login-div">
	<h2><?php echo CarRental::t('Sign up to client area'); ?></h2>
	<form method="POST">
		<?php include dirname(__FILE__).'/flash_msg.php';?>
		
		<label for="carrental-client-area-login-email"><?php echo CarRental::t('E-mail'); ?></label>
		<input class="control-input" type="text" name="email" id="carrental-client-area-login-email" />
		
		<p><?php echo CarRental::t('You will get your new password to this email address.');?></p>
		
		<button class="btn btn-primary" name="carrental-client-area-sign-in"><?php echo CarRental::t('Sign up'); ?></button>
		<div>
			<a href="<?php echo get_site_url(); ?>/client-area/login"><?php echo CarRental::t('Back to login page'); ?></a>			
		</div>
	</form>
</div>