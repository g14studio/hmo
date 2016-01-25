<div class="carrental-client-area-login-div">
	<h2><?php echo CarRental::t('Lost password'); ?></h2>
	<form method="POST">
		<?php include dirname(__FILE__).'/flash_msg.php';?>
		
		<label for="carrental-client-area-login-email"><?php echo CarRental::t('E-mail'); ?></label>
		<input class="control-input" type="text" name="email" id="carrental-client-area-login-email" />
		
		<p><?php echo CarRental::t('Enter your account email.');?></p>
		
		<button class="btn btn-primary" name="carrental-client-area-lost-password"><?php echo CarRental::t('Reset password'); ?></button>
		<div>
			<a href="<?php echo get_site_url(); ?>/client-area/login"><?php echo CarRental::t('Back to login page'); ?></a>			
		</div>
	</form>
</div>