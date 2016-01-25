<div class="box box-clean">

			<div data-target="modify-search" class="box-title mobile-toggle mobile-toggle-md"><?php echo CarRental::t('Client Area');?></div>
			<!-- .box-title -->

			<div class="box-inner-small box-border-bottom md-hidden" data-id="modify-search">			
				<ul class="carrental-client-area-menu">
					<li><a href="<?php echo get_site_url().'/'.self::$url.'/';?>"><?php echo CarRental::t('My bookings');?></a></li>
					<li><a href="<?php echo get_site_url().'/'.self::$url.'/my-account';?>"><?php echo CarRental::t('My account');?></a></li>
					<li><a href="<?php echo get_site_url().'/'.self::$url.'/account-settings';?>"><?php echo CarRental::t('Account settings');?></a></li>
					<li><a href="<?php echo get_site_url().'/'.self::$url.'/logout';?>"><?php echo CarRental::t('Logout');?></a></li>
				</ul>
			</div>

		</div>