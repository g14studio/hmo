<?php
/**
 * The template for displaying Category pages
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage CarRental
 * @since CarRental 3.0.0
 */
get_header();
?>

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

<section class="content">	

	<div class="container">

		<div class="columns-2 break-md aside-on-right">

			<div class="column column-fluid">
				<section id="primary" class="site-content">
					<div id="content" role="main">

						<?php
// Check if there are any posts to display
						if (have_posts()) :
							?>


							<h1 class="archive-title"><?php echo __('Category:', 'twentyfourteen')?> <?php echo single_cat_title('', false); ?></h1>


							<?php
// Display optional category description
							if (category_description()) :
								?>
								<div class="archive-meta"><?php echo category_description(); ?></div>
							<?php endif; ?>


							<?php
// The Loop
							while (have_posts()) : the_post();
								?>
								<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php echo __('Permanent Link to', 'twentyfourteen')?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
								<small><?php the_time('F jS, Y') ?> by <?php the_author_posts_link() ?></small>

								<div class="entry">
										<?php the_content(); ?>

									<p class="postmetadata"><?php
										comments_popup_link('No comments yet', '1 comment', '% comments', 'comments-link', 'Comments closed');
										?></p>
								</div>

							<?php endwhile;

						else:
							?>
							<p><?php echo __('Sorry, no posts matched your criteria.', 'twentyfourteen')?></p>


<?php endif; ?>
					</div>
				</section>
			</div>

<?php if (is_active_sidebar('page-sidebar')) { ?>
				<div class="column column-fixed sidebar">
					<div class="box box-clean">
						<div class="box-inner-small">
							<div class="invert-columns-2 init-md">
	<?php dynamic_sidebar('page-sidebar'); ?>
							</div>
						</div>
					</div>
				</div>
<?php } ?>

		</div>
	</div>
	<!-- .container -->

</section>
<!-- .content -->	

<?php
get_footer();
