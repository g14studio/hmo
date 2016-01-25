<div class="row">
  <div class="col-md-12">
  	<h2><?= ($cr_title != 'Carrental' ? $cr_title . ' - ' : '') ?><?= __('Car Rental Plugin', 'carrental') ?></h2>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<ul class="nav nav-tabs">
		  <li<?= (($_GET['page'] == 'carrental') ? ' class="active"' : '') ?>><a href="<?= esc_url(CarRental_Admin::get_page_url('carrental')); ?>"><span class="glyphicon glyphicon-home"></span>&nbsp;&nbsp;<?= __('Home', 'carrental') ?></a></li>
		  <li<?= (($_GET['page'] == 'carrental-fleet') ? ' class="active"' : '') ?>><a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-fleet')); ?>"><span class="glyphicon glyphicon-road"></span>&nbsp;&nbsp;<?= __('Fleet', 'carrental') ?></a></li>
		  <li<?= (($_GET['page'] == 'carrental-extras') ? ' class="active"' : '') ?>><a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-extras')); ?>"><span class="glyphicon glyphicon-asterisk"></span>&nbsp;&nbsp;<?= __('Extras', 'carrental') ?></a></li>
		  <li<?= (($_GET['page'] == 'carrental-branches') ? ' class="active"' : '') ?>><a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-branches')); ?>"><span class="glyphicon glyphicon-map-marker"></span>&nbsp;&nbsp;<?= __('Branches', 'carrental') ?></a></li>
		  <li<?= (($_GET['page'] == 'carrental-pricing') ? ' class="active"' : '') ?>><a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-pricing')); ?>"><span class="glyphicon glyphicon-usd"></span>&nbsp;&nbsp;<?= __('Pricing', 'carrental') ?></a></li>
		  <li<?= (($_GET['page'] == 'carrental-booking') ? ' class="active"' : '') ?>><a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-booking')); ?>"><span class="glyphicon glyphicon-calendar"></span>&nbsp;&nbsp;<?= __('Booking', 'carrental') ?></a></li>
		  <li<?= (($_GET['page'] == 'carrental-translations') ? ' class="active"' : '') ?>><a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-translations')); ?>"><span class="glyphicon glyphicon-globe"></span>&nbsp;&nbsp;<?= __('Translations', 'carrental') ?></a></li>
		  <li<?= (($_GET['page'] == 'carrental-settings') ? ' class="active"' : '') ?>><a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-settings')); ?>"><span class="glyphicon glyphicon-cog"></span>&nbsp;&nbsp;<?= __('Settings', 'carrental') ?></a></li>
		  <li<?= (($_GET['page'] == 'carrental-newsletter') ? ' class="active"' : '') ?>><a href="<?= esc_url(CarRental_Admin::get_page_url('carrental-newsletter')); ?>"><span class="glyphicon glyphicon-envelope"></span>&nbsp;&nbsp;<?= __('Newsletter', 'carrental') ?></a></li>
		</ul>
	</div>
</div>