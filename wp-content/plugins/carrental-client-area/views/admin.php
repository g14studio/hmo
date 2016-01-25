<div class="carrental-wrapper">
	
	<?php include CARRENTAL_CLIENT_AREA__PLUGIN_DIR . 'views/header.php'; ?>
	
	<div class="row">
		<div class="col-md-12 carrental-main-wrapper">
			<div class="carrental-main-content">
				
				<?php include CARRENTAL_CLIENT_AREA__PLUGIN_DIR . 'views/flash_msg.php'; ?>
				
				<!-- Automatic upgrade //-->
				<div class="panel panel-default">
					<div class="panel-heading"><h4>Automatic plugin update</h4></div>
					<div class="panel-body">
					  
						<div class="row">
							<div class="col-md-12">
								<?php $check = unserialize(get_option('carrental_client_area_update_check')); ?>
								<?php $apikey = unserialize(get_option('carrental_api_key')); ?>
								<?php $apikey_exp = get_option('carrental_api_key_expiration'); ?>
								<div class="row">
									<div class="col-md-6">
										
										<?php if (!$apikey || empty($apikey)) { ?>
										<p>You must set API KEY in main carrental plugin first.</p>
										<?php } else { ?>
											<form action="" method="post" role="form" class="form-horizontal">
												<div class="form-group">
												  <label class="col-sm-3 control-label" style="padding-top:0;">Last check</label>
												  <div class="col-sm-9">

													  <?php if (isset($check['last']) && strtotime($check['last']) != false) { ?>
															  <?= Date('Y-m-d', strtotime($check['last'])) ?>
														  <?php } else { ?>
															  <em>- never -</em>
														  <?php } ?>

														  &nbsp;&nbsp;<button type="submit" name="client_area_check_plugin_update" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-refresh"></span>&nbsp;&nbsp;Check plugin update manually</button>

														  <?php if (!isset($check['update_available']) || $check['update_available'] == false) { ?>
															  <br><br>Current plugin version: <strong><?= CARRENTAL_CLIENT_AREA_VERSION ?></strong>
															  <br><br><em>There is no new plugin update available.</em>
														  <?php } ?>
												  </div>
												</div>
											</form>

											<?php if (isset($check['update_available']) && $check['update_available'] == true) { ?>
												<form action="" method="post" role="form" class="form-horizontal">
													<div class="form-group">
													  <label class="col-sm-3 control-label">Plugin update is available!</label>
													  <div class="col-sm-9">

														  Current version: <?= CARRENTAL_CLIENT_AREA_VERSION ?><br>
															  <strong>New version: <?= $check['new_version'] ?></strong> (<?= $check['new_version_date'] ?>)<br><br>

															  <button type="submit" name="client_area_plugin_update" class="btn btn-success"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Download, backup and install update</button>

															  <br /><em>* Do not close this window while installing new version. Backup will be created automatically and saved under wp-content/plugins/carrental-client-area/backup.</em>

													  </div>
													</div>
												</form>
											<?php } ?>
									  <?php } ?>
								  </div>
							  </div>
							  
							</div>
						</div>
					</div>
				</div>
				
				<!-- GLOBAL SETTINGS //-->
				<div class="panel panel-default">
					<div class="panel-heading"><h4 id="global-settings">Registered users</h4></div>
					<div class="panel-body">
						<?php if (count($data) > 0) { ?>
							<table class="table table-striped" id="carrental-client-area">
								<thead>
									<tr><th>Email</th><th>Name</th><th>Orders count</th><th>Last login date</th><th>Last login IP</th><th>Action</th></tr>
								</thead>
								<tbody>
									<?php foreach ($data as $d) { ?>
									<tr><td><?php echo $d['email'];?></td><td><?php echo $d['first_name'].' '.$d['last_name'];?></td><td><?php echo $d['orders_count'];?></td><td><?php echo date('Y-m-d H:i:s', strtotime($d['last_login']));?></td><td><?php echo $d['last_login_ip'];?></td><td><a href="<?php echo esc_url(CarRental_Client_Area::get_page_url()) . '&amp;user_id='.$d['user_id']?>">Show details</a></td></tr>
									<?php } ?>
								</tbody>
							</table>
						<?php } else { ?>
							<p>No users found.</p>
						<?php } ?>
					</div>
					<!-- .panel-body //-->
				</div>
				<!-- .panel .panel-default //-->
				
				
			</div>
		</div>
	</div>
	
</div>