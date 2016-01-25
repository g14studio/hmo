<!-- Ver 3.0.0 -->
<div class="carrental-wrapper">

	<div class="row">
		<div class="col-md-12">
			<h2><?= __('Car Rental Slider settings', 'carrental') ?></h2>
		</div>
	</div>

	<br>

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
		<br>
		<?php unset($_SESSION['carrental_flash_msg']); // Delete flash msg ?>
	<?php } ?>

	<div class="row">
		<div class="col-md-10">

			<div class="panel panel-default">
				<div class="panel-body">

					<h4>Link plugin to pages</h4><br>

					<form action="" class="form" role="form" method="post" enctype="multipart/form-data">
						<div class="form-group row">
							<label for="carrental-type" class="col-sm-2 control-label">Slider margin</label>
							<div class="col-sm-10">
								<input type="text" name="slider-margin" class="form-control" placeholder="5" value="<?= ((isset($theme_slider_options['slider-margin'])) ? $theme_slider_options['slider-margin'] : '') ?>">
								<p class="help-block">Time margin between each slide in seconds.</p>
							</div>
						</div>

						<div class="form-group row">
							<label for="carrental-type" class="col-sm-2 control-label">Slider height</label>
							<div class="col-sm-10">
								<input type="text" name="slider-height" class="form-control" placeholder="340" value="<?= ((isset($theme_slider_options['slider-height'])) ? $theme_slider_options['slider-height'] : '') ?>">
								<p class="help-block">Height in pixels.</p>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 control-label">Slider transition</label>
							<div class="col-sm-10">
								<select name="slider-transition">
									<option value="fade">fade</option>
									<option value="horizontal"<?= ((isset($theme_slider_options['slider-transition']) && $theme_slider_options['slider-transition'] == 'horizontal') ? ' selected="selected"' : '') ?>>horizontal</option>
									<option value="vertical"<?= ((isset($theme_slider_options['slider-transition']) && $theme_slider_options['slider-transition'] == 'vertical') ? ' selected="selected"' : '') ?>>vertical</option>
								</select>
								<p class="help-block">Type of transition between slides.</p>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 control-label">Show pager</label>
							<div class="col-sm-10">
								<select name="slider-pager">
									<option value="0">no</option>
									<option value="1"<?= ((isset($theme_slider_options['slider-pager']) && $theme_slider_options['slider-pager'] == 1) ? ' selected="selected"' : '') ?>>yes</option>
								</select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 control-label">Show controls</label>
							<div class="col-sm-10">
								<select name="slider-controls">
									<option value="0">no</option>
									<option value="1"<?= ((isset($theme_slider_options['slider-controls']) && $theme_slider_options['slider-controls'] == 1) ? ' selected="selected"' : '') ?>>yes</option>
								</select>
							</div>
						</div>
						
						<!-- Slider pictures //-->
						<div class="form-group row">
							<label for="carrental-picture" class="col-sm-2 control-label">Slider pictures</label>
							<div class="col-sm-10">
								<div class="panel panel-info">
									<div class="panel-heading">Slider pictures</div>
									<div class="panel-body">
										<ul class="slider-pictures" id="slider-pictures-ul">

											<?php if (isset($theme_slider_options['slider-pictures']) && !empty($theme_slider_options['slider-pictures'])) { ?>
												<?php if (is_array($theme_slider_options['slider-pictures'])) { ?>
													<?php foreach ($theme_slider_options['slider-pictures'] as $picture) { ?>
														<li><input type="hidden" name="slider-pictures[]" value="<?php echo $picture; ?>" class="media-input" /><img src="<?php echo $picture; ?>" /><div class="buttons"><a href="#" class="btn btn-danger btn-block delete-button">X</a></div></li>
													<?php } ?>
												<?php } ?>
											<?php } ?>
										</ul>
									</div>
								</div>
								<button class="media-button">Add new picture</button>
							</div>
						</div>

						

						<br>

						<div class="form-group">
							<button type="submit" class="btn btn-warning" name="save_slider_settings"><span class="glyphicon glyphicon-save"></span>&nbsp;&nbsp;Confirm &amp; Save</button>
						</div>
					</form>


				</div>
			</div>




		</div>
	</div>

</div>

<script language="JavaScript">
	var gk_media_init = function (button_selector) {
		jQuery(button_selector).click(function (event) {
			event.preventDefault();

			// check for media manager instance
			if (wp.media.frames.gk_frame) {
				wp.media.frames.gk_frame.open();
				return;
			}
			// configuration of the media manager new instance
			wp.media.frames.gk_frame = wp.media({
				title: 'Select image',
				multiple: true,
				library: {
					type: 'image'
				},
				button: {
					text: 'Use selected image'
				}
			});

			// Function used for the image selection and media manager closing
			var gk_media_set_image = function () {
				var selection = wp.media.frames.gk_frame.state().get('selection');

				// no selection
				if (!selection) {
					return;
				}
				console.log(selection);
				// iterate through selected elements
				selection.each(function (attachment) {
					var url = attachment.attributes.url;
					// add to slider images
					jQuery('#slider-pictures-ul').append('<li><input type="hidden" name="slider-pictures[]" value="' + url + '" class="media-input" /><img src="' + url + '" /><div class="buttons"><a href="#" class="btn btn-danger btn-block delete-button">X</a></div></li>');
				});
			};

			// closing event for media manger
			//wp.media.frames.gk_frame.on('close', gk_media_set_image);
			// image selection event
			wp.media.frames.gk_frame.on('select', gk_media_set_image);
			// showing media manager
			wp.media.frames.gk_frame.open();
		});

	};

	gk_media_init('.media-button');

	jQuery(document).ready(function () {
		jQuery("#slider-pictures-ul").sortable({
			handle: 'img',
			cursor: 'move'
		});
		jQuery("#slider-pictures-ul").disableSelection();

		jQuery(document).on('mouseover', "#slider-pictures-ul li", function () {
			jQuery(this).children('.buttons').show();
		});

		jQuery(document).on('mouseout', "#slider-pictures-ul li", function () {
			jQuery(this).children('.buttons').hide();
		});

		jQuery(document).on('click', "#slider-pictures-ul li .delete-button", function (event) {
			event.preventDefault();
			jQuery(this).parent().parent().remove();
		});

		var fixHelper = function (e, ui) {
			ui.children().each(function () {
				jQuery(this).width(jQuery(this).width());
			});
			return ui;
		};
	});
</script>