<?php
/**
 * The front page template file.
 *
 * If the user has selected a static page for their homepage, this is what will
 * appear. Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 *
 * @package components
 */
/**
 * Load pattern maker file.
 */
get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php
			$sections = array( 'selection', 'types', 'types-form', 'extra-info', 'contributors' );
			foreach ( $sections as $section ) {
				get_template_part( 'section', $section );
			}
			?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
