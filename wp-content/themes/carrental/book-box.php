<div class="tabs" style="<?php echo isset($params['width']) && (int)$params['width'] > 0 ? 'width:'.(int)$params['width'].'px;': '';?>">
	<ul class="tabs-navigation">
		<li class="tabs-navigation-active">
			<a href="javascript:void(0);" data-tab-target="quick-book"><?= CarRental::t('QUICK BOOK') ?></a>
		</li>
		<li class="tabs-navigation-link">
			<a href="javascript:void(0);" data-tab-target="manage-booking"><?= CarRental::t('MANAGE BOOKING') ?></a>
		</li>
	</ul>

	<div class="tabs-content">

		<div data-tab-id="quick-book" class="tabs-content-tab tabs-content-tab-active">

			<?php carrental_get_booking_form(); ?>

		</div>
		<!-- .tabs-content-tab -->

		<div data-tab-id="manage-booking" class="tabs-content-tab">
			<form action="" method="post" class="form form-request form-vertical form-size-100">
				<fieldset>

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
		</div>
		<!-- .tabs-content-tab -->

	</div>
	<!-- .tabs-content -->
</div>
<!-- .tabs -->