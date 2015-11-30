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
require get_template_directory() . '/inc/pattern-maker.php';
get_header(); ?>

	<div id="primary" class="content-area">

		<section id="intro" role="main">
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

		<section id="types" role="main">
			<div class="wrap">
				<div class="spotlight-controls">
					<?php echo file_get_contents( esc_url( get_template_directory_uri() ) . '/assets/img/spotlight.svg' ); ?>
				</div>
				<div class="theme-slider">

					<?php
					require get_template_directory() . '/components/theme-types.php';
					// Randomise order of types so as not to favour any in particular
					shuffle( $types );
					// Iterate through each theme type and output formatted text
					foreach ( $types as $type ) : ?>
						<div class="theme-type" data-type="<?php echo esc_attr( $type['filename'] ); ?>">
							<div class="theme-image">
								<?php echo file_get_contents( esc_url( get_template_directory_uri() ) . '/assets/img/robot-' . $type['filename'] . '.svg' ); ?>
							</div>
							<div class="theme-text">
								<h2 class="theme-type-title"><?php echo $type['title']; ?></h2>
								<p><?php echo $type['text']; ?></p>
								<div class="theme-input">
									<input type="text" placeholder="Theme Name">
									<input type="text" placeholder="Author">
									<input type="text" placeholder="Theme URI">
								</div>
								<a href="<?php echo esc_url( get_template_directory_uri() ) . '/downloads/' . $type['filename'] . '.zip' ?>" class="download button">Build Theme!</a>
							</div>
						</div><!-- .theme-type -->
					<?php endforeach; ?>

				</div><!-- .theme-slider -->

				<div class="slider-nav">
					<div class="slider-nav-bg"></div>
					<div class="slider-lever">
						<?php echo file_get_contents( esc_url( get_template_directory_uri() ) . '/assets/img/lever.svg' ); ?>
					</div>
				</div><!-- .slider-nav -->

			</div><!-- .wrap -->
		</section><!-- #types -->

		<section id="download-all" role="main">
			<div class="wrap">
				<?php echo file_get_contents( esc_url( get_template_directory_uri() ) . '/assets/img/gear.svg' ); ?>

				<div class="content-wrapper theme-type" data-type="base">
					<h2>Just the basics, please</h2>
					<p>Can&rsquo;t decide? Want to concoct your own starter theme?<br>
						Don&rsquo;t need any bells or whistles? Our base package is for you.</p>
					<a href="<?php echo esc_url( get_template_directory_uri() ) . '/downloads/base.zip' ?>" class="download button">Download the base!</a>
				</div><!-- .content-wrapper -->
			</div><!-- .wrap -->
		</section>


		<section id="extra-info" role="main">
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

	</div><!-- #primary -->

<?php get_footer(); ?>