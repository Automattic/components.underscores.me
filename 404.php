<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package components
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

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

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
