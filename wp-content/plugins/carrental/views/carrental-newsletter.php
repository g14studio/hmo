<div class="carrental-wrapper">
	
	<?php include CARRENTAL__PLUGIN_DIR . 'views/header.php'; ?>
	
	<div class="row">
	
		<div class="col-md-12 carrental-main-wrapper">
			<div class="carrental-main-content">
				
				<?php include CARRENTAL__PLUGIN_DIR . 'views/flash_msg.php'; ?>
				
				<div class="row">
					<div class="col-md-12">
						
						<?php if (isset($newsletter) && !empty($newsletter)) { ?>
						<label class="label_select_all"><input type="checkbox" name="select_all" value="1" class="data_table_select_all" data-id="carrental-newsletter" /> Select all</label>
							<table class="table table-striped" id="carrental-newsletter">
					      <thead>
					        <tr>
							  <th>#</th>
					          <th>Created</th>
					          <th>First name</th>
					          <th>Last name</th>
					          <th>E-mail</th>
					        </tr>
					      </thead>
					      <tbody>
								<?php foreach ($newsletter as $key => $val) { ?>
				      		<tr>
								<td>
											<input type="checkbox" class="input-control batch_processing" name="batch[]" value="<?= $val->id_booking ?>">&nbsp;
											<abbr><?= $val->id_booking ?></abbr></td>
					          <td><?= (!empty($val->created) ? $val->created : '- Unknown -') ?></td>
					          <td><?= (!empty($val->first_name) ? $val->first_name : '- Unknown -') ?></td>
										<td><?= (!empty($val->last_name) ? $val->last_name : '- Unknown -') ?></td>
										<td><?= (!empty($val->email) ? $val->email : '- Unknown -') ?></td>
					        </tr>
				      	<?php } ?>
					    	</tbody>
					  	</table>
						<label class="label_select_all"><input type="checkbox" name="select_all" value="1" class="data_table_select_all" data-id="carrental-newsletter" /> Select all</label>
						<div>
						<a class="btn btn-warning" href="<?= CarRental_Admin::get_page_url('carrental-newsletter') ?>&amp;carrental-newsletter-export=csv">Export all as CSV</a>
						</div>
						<h4>Batch action on selected items</h4>
						
						<form action="" method="post" class="form" role="form" onsubmit="if (jQuery('[name=batch_processing_values]').val() == '') { alert('No items is selected to remove.'); return false }; return confirm('<?= __('Do you really want to remove selected items?', 'carrental') ?>');">
								<div class="form-group">
									<input type="hidden" name="batch_processing_values" value="">
									<button name="batch_delete_newsletter" class="btn btn-danger">Remove <span class="batch_processing_count"></span>selected Items</button>
								</div>
							</form>
							
						<?php } else { ?>
							<div class="alert alert-info">
								<span class="glyphicon glyphicon-info-sign"></span>&nbsp;&nbsp;
								<?= esc_html__( 'You do not have any User in Newsletter yet.', 'carrental' ); ?>
							</div>
						<?php } ?>
						
					</div>
				</div>
				
				
				
			</div>
		</div>
	</div>
	
</div>
