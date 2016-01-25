
<form action="" method="post" class="form form-request form-vertical form-size-100">
	<fieldset>
		
		<h1><?= CarRental::t('Manage your booking') ?></h1>

		<div class="control-group">
			<div class="control-label">
				<label for="carrental_order_number"><?= CarRental::t('Order Number') ?>:</label>
			</div>
			<div class="control-field">
				<input type="text" name="id_order" id="carrental_order_number" class="control-input">
			</div>
		</div>
		
		<div class="control-group">
			<div class="control-label">
				<label for="carrental_order_email"><?= CarRental::t('Your E-mail') ?>:</label>
			</div>
			<div class="control-field">
				<input type="text" name="email" id="carrental_order_email" class="control-input">
			</div>
		</div>
		
		<div class="control-group">
			<div class="control-field align-right">
				<input type="hidden" name="page" value="carrental">
				<button type="submit" name="manage_booking" class="btn btn-primary"><?= CarRental::t('SHOW ORDER DETAILS') ?></button>	
			</div>
		</div>
		
	</fieldset>
</form>