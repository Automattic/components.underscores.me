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

			<section id="intro">
				<div class="wrap">

					<div class="chute-wrapper">
						<?php echo file_get_contents( esc_url( get_template_directory_uri() ) . '/assets/img/pipe-chute.svg' ); ?>
						<?php echo file_get_contents( esc_url( get_template_directory_uri() ) . '/assets/img/tap.svg' ); ?>
					</div>

					<div class="content-wrapper">
						<?php echo file_get_contents( esc_url( get_template_directory_uri() ) . '/assets/img/pipe-left.svg' ); ?>

						<div id="stretchy-pipe">&nbsp;</div>

						<?php echo file_get_contents( esc_url( get_template_directory_uri() ) . '/assets/img/pipe-rightcrook.svg' ); ?>

						<div class="intro-content">

							<div class="site-branding">
								<?php echo file_get_contents( esc_url( get_template_directory_uri() ) . '/assets/img/components.svg' ); ?>
								<h2 class="site-description">Want to build a WordPress theme turbo-fast?</h2>
							</div><!-- .site-branding -->

							<p>Components is a library of <strong>shareable, reusable patterns</strong> for WordPress themes. Instead of starting from scratch, mix and match from a collection of pre-made components to build your own <strong>custom starter theme</strong>.</p>

							<p>If you&rsquo;re just starting out, it&rsquo;ll get you <strong>booted up</strong> without needing to reinvent the wheel or write a lot of custom code. If you&rsquo;re an experienced theme developer, you&rsquo;ll find <strong>well-organized, easy-to use code</strong> that you can remix to your heart&rsquo;s delight!</p>
						</div><!-- .intro-content -->
					</div><!-- .content-wrapper -->

				</div><!-- .wrap -->
			</section><!-- #intro -->

			<section id="base">
				<div class="theme-type wrap" data-type="base">
					<div class="theme-image">
						<div class="standard-robot">
							<?php echo file_get_contents( esc_url( get_template_directory_uri() ) . '/assets/img/layout-base.svg' ); ?>
							<?php echo file_get_contents( esc_url( get_template_directory_uri() ) . '/assets/img/robot-base.svg' ); ?>
						</div>
						<div class="mobile-robot">
							<?php echo file_get_contents( esc_url( get_template_directory_uri() ) . '/assets/img/mobile-robot-base.svg' ); ?>
						</div>
					</div>
					<div class="theme-text">
						<h2 class="theme-type-title">Just the basics, please</h2>
						<p>Can&rsquo;t decide? Want to concoct your own starter theme? Don&rsquo;t need any bells or whistles? Our base package is for you.</p>
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
									<?php echo file_get_contents( esc_url( get_template_directory_uri() ) . '/assets/img/layout-' . $type['filename'] . '.svg' ); ?>

									<?php echo file_get_contents( esc_url( get_template_directory_uri() ) . '/assets/img/robot-' . $type['filename'] . '.svg' ); ?>
								</div>
								<div class="mobile-robot">
									<?php echo file_get_contents( esc_url( get_template_directory_uri() ) . '/assets/img/mobile-robot-' . $type['filename'] . '.svg' ); ?>
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

			<section id="extra-info">
				<div class="wrap">
					<div class="col">
						<h2>What&rsquo;s in the box?</h2>
						<p>Every Components package comes with:</p>
						<ul>
							<li>Design-agnostic layout patterns</li>
							<li>Well-organized SCSS</li>
							<li>Mobile-first layouts</li>
							<li>Mobile and desktop menus</li>
							<li>A simple base</li>
						</ul>
					</div>

					<div class="octocat-robot">
						<?php echo file_get_contents( esc_url( get_template_directory_uri() ) . '/assets/img/robot-octocat.svg' ); ?>
					</div>

					<div class="col">
						<h2>Want to contribute?</h2>
						<p>Components is a new project, and we&rsquo;re looking for your input! Have a pattern to share? Want to add a new feature? Found a bug in the code? Head over to the <a href="https://github.com/Automattic/theme-pattern-library">GitHub repo</a>, check out the <a href="https://github.com/Automattic/theme-pattern-library/blob/master/CONTRIBUTING.md">contributor guidelines</a>, and get involved!</p>

					</div>
				</div><!-- .wrap -->
			</section><!-- #extra-info -->

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
