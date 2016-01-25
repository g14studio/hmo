<?php if (isset($_SESSION['carrental_flash_msg']) && !empty($_SESSION['carrental_flash_msg']) && !empty($_SESSION['carrental_flash_msg']['msg'])) { ?>

	<div class="row">
	  <div class="col-md-12">
	  	<div class="alert alert-<?= $_SESSION['carrental_flash_msg']['status'] ?> alert-dismissable">
	  		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	  		<span class="glyphicon glyphicon-<?= (($_SESSION['carrental_flash_msg']['status'] == 'success') ? 'ok' : 'remove') ?>"></span>&nbsp;&nbsp;
				<?= $_SESSION['carrental_flash_msg']['msg'] ?>
			</div>
		</div>
	</div>
	
	<?php unset($_SESSION['carrental_flash_msg']); // Delete flash msg ?>
<?php } ?>