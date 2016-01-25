<div class="columns-2 break-md aside-on-left carrental-client-area">
	<div class="column column-fixed">
		<?php include dirname(__FILE__).'/menu.php';?>
	</div>
	<!-- .column -->

	<div class="column column-fluid">

		<div class="bordered-content">
			<div class="bordered-content-title">
				<h2 class="mb0"><?php echo CarRental::t('Account settings');?></h2>				
			</div>
			<!-- .bordered-content-title -->
			<div class="list-item list-item-car box box-white box-inner">
				<form method="POST" class="subform">
					<h3><?php echo CarRental::t('Change password');?></h3>
					<?php include dirname(__FILE__).'/flash_msg.php';?>
					<label for="carrental-client-area-old-password"><?php echo CarRental::t('Current password');?></label>
					<input class="control-input" type="password" name="current_password" id="carrental-client-area-old-password" />
					<label for="carrental-client-area-new-password"><?php echo CarRental::t('New password');?></label>
					<input class="control-input" type="password" name="new_password" id="carrental-client-area-new-password" />
					<label for="carrental-client-area-new-password-confirm"><?php echo CarRental::t('Confirm new password');?></label>
					<input class="control-input" type="password" name="new_password_confirm" id="carrental-client-area-new-password-confirm" />
					<button class="btn btn-primary" name="carrental-client-area-change-password"><?php echo CarRental::t('Change password');?></button>
				</form>
			</div>
			<div class="list-item list-item-car box box-white box-inner">
				<form method="POST" class="subform">
					<h3><?php echo CarRental::t('Change your login email');?></h3>
					<label for="carrental-client-area-new-email"><?php echo CarRental::t('New email');?></label>
					<input class="control-input" type="text" name="email" id="carrental-client-area-new-email" />
					<label for="carrental-client-area-your-password"><?php echo CarRental::t('Your password');?></label>
					<input class="control-input" type="password" name="password" id="carrental-client-area-your-password" />
					<button class="btn btn-primary" name="carrental-client-area-change-email"><?php echo CarRental::t('Change email');?></button>
				</form>
			</div>
		</div>
		<!-- .bordered-content -->

	</div>
	<!-- .column -->

</div>