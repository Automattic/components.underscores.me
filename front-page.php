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
			<section id="selection">
				<div class="types">
					<div class="svg-container">
						<?php echo file_get_contents( get_template_directory() . '/assets/img/type-cart.svg' ); ?>
					</div>
					<h2>Types</h2>
					<p>Lay the groundwork for your WordPress theme with our <strong>ready-made Types</strong>. Crafting an elegant artist's <strong>portfolio</strong>? An information-filled <strong>magazine</strong> theme or <strong>business</strong> site? How about a modern or classic <strong>blog</strong>? Choose the type that fits the bill and you'll be on your way in no time.
</p>
					<a href="#theme-types-panel" class="toggle button">Choose a Type</a>
				</div>
				<div class="custom">
					<div class="svg-container">
						<?php echo file_get_contents( get_template_directory() . '/assets/img/toolbox.svg' ); ?>
					</div>
					<h2>Build Your Own</h2>
					<p>Go the custom route and <strong>keep control</strong> over every element of your starter theme, picking-and-choosing only the components you need to create your <strong>perfect custom starter theme</strong>. You'll get solid, reliable code, with only the pieces your project needs.
</p>
					<a href="#custom-build-panel" class="toggle button">Configure</a>
				</div>
			</section><!-- #selection -->
			<section id="theme-types-panel" class="panel">
				<section id="base">
					<div class="theme-type wrap" data-type="base">
						<div class="theme-image">
							<div class="standard-robot">
								<?php echo file_get_contents( get_template_directory() . '/assets/img/layout-base.svg' ); ?>
								<?php echo file_get_contents( get_template_directory() . '/assets/img/robot-base.svg' ); ?>
							</div>
							<div class="mobile-robot">
								<?php echo file_get_contents( get_template_directory() . '/assets/img/mobile-robot-base.svg' ); ?>
							</div>
						</div>
						<div class="theme-text">
							<h2 class="theme-type-title">Just the basics, please</h2>
							<p>Want to concoct your own starter theme? Don&rsquo;t need any bells or whistles? Our base package is for you.</p>
							<a href="#generator" class="download button" data-type="base">Build Theme!</a>
						</div>
					</div><!-- .theme-type -->

				</section><!-- #base -->

				<section id="types">
					<?php
						require get_template_directory() . '/components/theme-types.php';
						// Randomise order of types so as not to favour any in particular
						shuffle( $types );
						// Prepend the Base type
						// Iterate through each theme type and output formatted text
						$i = 0;
						foreach ( $types as $type ) :
							if ( 0 == $i % 2 ) {
								echo $i > 0 ? '</div>' : ''; // close div if it's not the first
								echo '<div class="wrap types-row">';
							}
							?>
							<div class="theme-type" data-type="<?php echo esc_attr( $type['filename'] ); ?>">
								<h2 class="theme-type-title"><?php echo $type['title']; ?></h2>
								<div class="theme-image">
									<div class="standard-robot">
										<?php echo file_get_contents( get_template_directory() . '/assets/img/layout-' . $type['filename'] . '.svg' ); ?>

										<?php echo file_get_contents( get_template_directory() . '/assets/img/robot-' . $type['filename'] . '.svg' ); ?>
									</div>
									<div class="mobile-robot">
										<?php echo file_get_contents( get_template_directory() . '/assets/img/mobile-robot-' . $type['filename'] . '.svg' ); ?>
									</div>
								</div>
								<div class="theme-text">
									<p><?php echo $type['text']; ?></p>
									<a href="#generator" class="download button" data-type="<?php echo esc_attr( $type['filename'] ); ?>">Build Theme!</a>
								</div>
							</div><!-- .theme-type -->

							<?php $i++; ?>
						<?php endforeach; ?>

						</div> <!-- .types-row -->

				</section><!-- #types -->

				<?php do_action( 'components_generator_print_form' ); ?>

			</section><!-- #theme-types-panel -->

			<?php
			$sections = array( 'types-form', 'extra-info', 'contributors' );
			foreach ( $sections as $section ) {
				get_template_part( 'section', $section );
			}
			?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
