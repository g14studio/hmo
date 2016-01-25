<?php if (!isset($_SESSION['user_id'])) { ?>
	<div class="columns-1 control-group carrental-clinet-area-form-section">
		<div class="column">
			<div class="control-field">
				<label><input type="checkbox" value="1" id="carrental-client-area-create-account-checkbox" class="" name="create_account"> <?= CarRental::t('Create account or login if already have one.') ?></label>
			</div>
		</div>
	</div>

	<div id="carrental-client-area-hidden-login">
		<p class="close-win">×</p>
		<h3><?= CarRental::t('Login to your account') ?></h3>
		<p><?= CarRental::t('E-mail') ?> <span id="carrental-client-area-span-email"></span> <?= CarRental::t('is already used by another user. If it is your account then enter password to login.'); ?></p>
		<label><?= CarRental::t('Password') ?></label>
		<input type="password" name="password" class="control-input" id="carrental-client-area-password" />
		<button type="submit" class="btn btn-primary" name="confirm_reservation"><?= CarRental::t('Login and Confirm Reservation'); ?></button>
		<a href="#" class="btn btn-primary" id="carrental-client-area-close-login-button"><?= CarRental::t('Change e-mail address'); ?></a>
	</div>
	<div class="booking-client-area-form-overflow"></div>
<?php } ?>