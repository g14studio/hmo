<div class="carrental-client-area-login-div">
	<h2><?php echo CarRental::t('Client Area'); ?></h2>
	<form method="POST">
		<?php include dirname(__FILE__).'/flash_msg.php';?>
		<label for="carrental-client-area-login-email"><?php echo CarRental::t('E-mail'); ?></label>
		<input class="control-input" type="text" name="email" id="carrental-client-area-login-email" />
		<label for="carrental-client-area-login-password"><?php echo CarRental::t('Password'); ?></label>
		<input class="control-input" type="password" name="password" id="carrental-client-area-login-password" />
		<button class="btn btn-primary" name="carrental-client-area-login"><?php echo CarRental::t('Log in'); ?></button>
		<div>
			<a href="<?php echo get_site_url(); ?>/client-area/lost-password"><?php echo CarRental::t('Lost password'); ?></a>
			<a href="<?php echo get_site_url(); ?>/client-area/sign-in"><?php echo CarRental::t('Sign up'); ?></a>
		</div>
	</form>
</div>