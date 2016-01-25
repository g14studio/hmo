<div class="carrental-wrapper">

	<?php include CARRENTAL_CLIENT_AREA__PLUGIN_DIR . 'views/header.php'; ?>
	<a href="<?= esc_url(CarRental_Client_Area::get_page_url()); ?>" class="btn btn-default">Back to all users</a>
	<div class="carrental-client-area-client-details">
		<ul>
			<li><label>First name: </label><span><?php echo $user['first_name'];?></span></li>
			<li><label>Last name: </label><span><?php echo $user['last_name'];?></span></li>
			<li><label>Email: </label><span><?php echo $user['email'];?></span></li>
			<li><label>Phone: </label><span><?php echo $user['phone'];?></span></li>
			<li><label>Street: </label><span><?php echo $user['street'];?></span></li>
			<li><label>City: </label><span><?php echo $user['city'];?></span></li>
			<li><label>ZIP: </label><span><?php echo $user['zip'];?></span></li>
			<?php $countries = CarRental::get_country_list(); ?>
			<li><label>Country: </label><span><?php echo isset($countries[$user['country']]) ? $countries[$user['country']] : $user['country'];?></span></li>
			<li><label>Company: </label><span><?php echo $user['company'];?></span></li>
			<li><label>VAT: </label><span><?php echo $user['vat'];?></span></li>
			<li><label>License number: </label><span><?php echo $user['license'];?></span></li>
			<li><label>ID / Passport number: </label><span><?php echo $user['id_card'];?></span></li>
		</ul>
		<div class="clear"></div>
	</div>

	<div class="row">
		<div class="col-md-12 carrental-main-wrapper">
			<div class="carrental-main-content">

				<?php include CARRENTAL_CLIENT_AREA__PLUGIN_DIR . 'views/flash_msg.php'; ?>

				<div class="row">
					<div class="col-md-12">
						<?php if (isset($bookings) && !empty($bookings)) { ?>
							<table class="table table-striped" id="carrental-client-area-bookings">
								<thead>
									<tr>
										<th>#</th>
										<th>Name</th>
										<th>Email</th>
										<th>Vehicle</th>
										<th>Enter date</th>
										<th>Enter loc.</th>
										<th>Return date</th>
										<th>Return loc.</th>
										<th>Price</th>
										<th>Order ID</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>

									<?php foreach ($bookings as $key => $val) { ?>
										<tr>
											<td>
												<abbr title="Created: <?= $val->created ?>

													  <?= (!empty($val->updated) ? 'Updated: ' . $val->updated : '') ?>"><?= $val->id_booking ?></abbr></td>
											<td><?= $val->first_name . ' ' . $val->last_name; ?></td>
											<td><?= $val->email; ?></td>
											<td><strong><?= (!empty($val->vehicle) ? $val->vehicle : '- Unknown -') ?></strong></td>
											<td><?= $val->enter_date ?></td>
											<td><?= $val->enter_loc ?></td>
											<td><?= $val->return_date ?></td>
											<td><?= $val->return_loc ?></td>
											<td><?= CarRental::get_currency_symbol('before', $val->currency) ?><?= number_format($val->total_rental, 2, '.', ',') ?><?= CarRental::get_currency_symbol('after', $val->currency) ?></td>
											<td><a href="<?= esc_url(home_url('/')); ?>?page=carrental&summary=<?= $val->hash ?>" target="_blank" class="btn btn-info btn-xs">Show #<?= $val->id_order ?></a></td>
											<td>
												<form action="" method="post" class="form-inline" role="form">
													<div class="form-group">
														<a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-booking')); ?>&amp;edit=<?= $val->id_booking ?>" class="btn btn-xs btn-primary">Modify</a>
													</div>
												</form>
											</td>
										</tr>
									<?php } ?>
								</tbody>
							</table>

						<?php } else { ?>
							<div class="alert alert-info">
								<span class="glyphicon glyphicon-info-sign"></span>&nbsp;&nbsp;
								<?= esc_html__('There are no Bookings.', 'carrental'); ?>
							</div>
						<?php } ?>

					</div>
				</div>



			</div>
		</div>
	</div>

</div>
<script type="text/javascript">
	jQuery(document).ready(function($) { 
		$('#carrental-client-area-bookings').dataTable({ stateSave: true }); 
	});
</script>