<div class="columns-2 break-md aside-on-left carrental-client-area">
	<div class="column column-fixed">
		<?php include dirname(__FILE__) . '/menu.php'; ?>
	</div>
	<!-- .column -->

	<div class="column column-fluid">

		<div class="bordered-content">
			<div class="bordered-content-title">
				<h2 class="mb0"><?php echo CarRental::t('My account'); ?></h2>				
			</div>
			<!-- .bordered-content-title -->
			<div class="list-item list-item-car box box-white box-inner">
				<form method="POST" class="subform">
					<h3><?php echo CarRental::t('My details'); ?></h3>
					<?php include dirname(__FILE__) . '/flash_msg.php'; ?>

					<div class="columns-2 control-group">
						<div class="column">
							<label for="carrental-client-area-first_name"><?php echo CarRental::t('First name'); ?></label>
							<input class="control-input" type="text" name="first_name" value="<?php echo $user['first_name']; ?>" id="carrental-client-area-first_name" />

							<label for="carrental-client-area-phone"><?php echo CarRental::t('Phone'); ?></label>
							<input class="control-input" type="text" name="phone" value="<?php echo $user['phone']; ?>" id="carrental-client-area-phone" />

							<label for="carrental-client-area-city"><?php echo CarRental::t('City'); ?></label>
							<input class="control-input" type="text" name="city" value="<?php echo $user['city']; ?>" id="carrental-client-area-city" />

							<label for="carrental-client-area-country"><?php echo CarRental::t('Country'); ?></label>
							<select style="width:46%;" name="country" id="carrental-client-area-country">
								<option value=""><?= CarRental::t('Country') ?></option>
								<?php $countries = CarRental::get_country_list(); ?>
								<?php foreach ($countries as $key => $val) { ?>
									<option value="<?= $key ?>"<?php echo $user['country'] == $key ? ' selected="selected"' : ''; ?>><?= $val ?></option>
								<?php } ?>
							</select>

							<label for="carrental-client-area-vat"><?php echo CarRental::t('VAT'); ?></label>
							<input class="control-input" type="text" name="vat" value="<?php echo $user['vat']; ?>" id="carrental-client-area-vat" />
							
							<label for="carrental-client-area-license"><?php echo CarRental::t('License number'); ?></label>
							<input class="control-input" type="text" name="license" value="<?php echo $user['license']; ?>" id="carrental-client-area-license" />
						</div>

						<div class="column">
							<label for="carrental-client-area-last_name"><?php echo CarRental::t('Last name'); ?></label>
							<input class="control-input" type="text" name="last_name" value="<?php echo $user['last_name']; ?>" id="carrental-client-area-last_name" />

							<label for="carrental-client-area-street"><?php echo CarRental::t('Street'); ?></label>
							<input class="control-input" type="text" name="street" value="<?php echo $user['street']; ?>" id="carrental-client-area-street" />

							<label for="carrental-client-area-zip"><?php echo CarRental::t('ZIP'); ?></label>
							<input class="control-input" type="text" name="zip" value="<?php echo $user['zip']; ?>" id="carrental-client-area-zip" />

							<label for="carrental-client-area-company"><?php echo CarRental::t('Company'); ?></label>
							<input class="control-input" type="text" name="company" value="<?php echo $user['company']; ?>" id="carrental-client-area-company" />
							
							<label for="carrental-client-area-id_card"><?php echo CarRental::t('ID / Passport number'); ?></label>
							<input class="control-input" type="text" name="id_card" value="<?php echo $user['id_card']; ?>" id="carrental-client-area-id_card" />
						</div>
					</div>
					<button class="btn btn-primary" name="carrental-client-area-change-my-details"><?php echo CarRental::t('Save'); ?></button>
				</form>
			</div>
		</div>
		<!-- .bordered-content -->

	</div>
	<!-- .column -->

</div>