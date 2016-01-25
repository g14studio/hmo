<section class="intro">
	<div>
		<?php $theme_options = unserialize(get_option('carrental_theme_options')); ?>
		<div class="slideshow-item static"<?php if (isset($theme_options['picture_otherpages']) && !empty($theme_options['picture_otherpages'])) { ?> style="background-image:url('<?= htmlspecialchars($theme_options['picture_otherpages']) ?>');"<?php } ?>>

			<div class="slideshow-item-wrap">						
				<div class="slideshow-item-content">
					<div class="row">
						<div class="h2">
							<span><?= CarRental::t('Feel the Joy.') ?></span> <?= CarRental::t('Fully inclusive Rates') ?>
						</div>
					</div>
					<div class="row">
						<p><?= CarRental::t('Save up to 35%') ?> <span><?= CarRental::t('Pay Now Rates') ?></span></p>
					</div>
				</div>

			</div>
		</div>
	</div>
</section>