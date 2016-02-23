<?php
/**
 * The template part for displaying a message that posts cannot be found.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package components
 */

?>

<section class="error-404 not-found">
	<div class="wrap">
		<div class="content-404">
			<header class="page-header">
				<h2 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'components' ); ?></h2>
			</header><!-- .page-header -->

			<div class="page-content">
				<a href="/"><?php esc_html_e( '&laquo; Head back home', 'components' ); ?></a>
			</div><!-- .page-content -->
		</div>
		<div class="image-404">
			<?php echo file_get_contents( get_template_directory() . '/assets/img/robot-sad.svg' ); ?>
		</div>
	</div>
</section><!-- .error-404 -->