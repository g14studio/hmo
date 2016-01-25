<div class="columns-2 break-md aside-on-left carrental-client-area">
	<div class="column column-fixed">
		<?php include dirname(__FILE__) . '/menu.php'; ?>
	</div>
	<!-- .column -->

	<div class="column column-fluid">

		<div class="bordered-content">

			<div class="bordered-content-title">
				<h2 class="mb0"><?php echo CarRental::t('My bookings'); ?></h2>
			</div>

			<div class="list-item list-item-car box box-white box-inner">
				<?php if (count($bookings) > 0) { ?>
					<table class="carrental-cliet-area-bookings">
						<thead>
							<tr>
								<th><span><?php echo CarRental::t('ID order'); ?></span><span class="resp-table-show"><?php echo CarRental::t('Vehicle'); ?></span></th>
								<th class="resp-table-hide"><?php echo CarRental::t('Vehicle'); ?></th>
								<th><span><?php echo CarRental::t('Enter date'); ?></span><span class="resp-table-show"><?php echo CarRental::t('Return date'); ?></span></th>
								<th class="resp-table-hide"><?php echo CarRental::t('Return date'); ?></th>
								<th><?php echo CarRental::t('Action'); ?></th>
							</tr>
						</thead>
						<tbody>
					<?php $theme_options = unserialize(get_option('carrental_theme_options')); ?>
					<?php foreach ($bookings as $b) { ?>
							<tr>
								<td><span><?php echo $b['id_order'];?></span><span class="resp-table-show"><?php echo $b['vehicle'];?></span></td>
								<td class="resp-table-hide"><?php echo $b['vehicle'];?></td>
								<td><span><?php echo Date(date_format_php(isset($theme_options['date_format']) ? $theme_options['date_format'] : '').' H:i:s', strtotime($b['enter_date']));?></span><span class="resp-table-show"><?php echo Date(date_format_php(isset($theme_options['date_format']) ? $theme_options['date_format'] : '').' H:i:s', strtotime($b['return_date']));?></span></td>
								<td class="resp-table-hide"><?php echo Date(date_format_php(isset($theme_options['date_format']) ? $theme_options['date_format'] : '').' H:i:s', strtotime($b['return_date']));?></td>
								<td><a href="<?php echo home_url() . '?page=carrental&summary=' . CarRental::generate_hash($b['id_order'], $b['email']);?>" target="_blank"><?php echo CarRental::t('show details'); ?></a></td>
							</tr>
					<?php } ?>
						</tbody>
					</table>
				<?php } else { ?>
					<?php echo CarRental::t('You do not have any bookings yet.'); ?>
				<?php } ?>
			</div>

		</div>
		<!-- .bordered-content -->

	</div>
	<!-- .column -->

</div>