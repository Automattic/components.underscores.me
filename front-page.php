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
			</section>
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

			<section id="custom-build-panel" class="panel">
				<div class="wrap">
					<form>
						<fieldset>
							<h3>Layout</h3>

							<div class="components-radio-group">
								<input class="components-input" type="radio" name="theme-layout" value="layout1" id="layout1">
								<label class="components-label" for="layout1">2 Col Right</label>
							</div><!-- .components-checkbox-group-->

							<div class="components-radio-group">
								<input class="components-input" type="radio" name="theme-layout" value="layout2" id="layout2">
								<label class="components-label" for="layout2">2 Col Left</label>
							</div><!-- .components-checkbox-group-->


							<div class="components-radio-group">
								<input class="components-input" type="radio" name="theme-layout" value="layout3" id="layout3">
								<label class="components-label" for="layout3">Single Column</label>
							</div><!-- .components-checkbox-group-->

							<div class="components-radio-group">
								<input class="components-input" type="radio" name="theme-layout" value="layout4" id="layout4">
								<label class="components-label" for="layout4">3 Column Right</label>
							</div><!-- .components-checkbox-group-->

							<div class="components-radio-group">
								<input class="components-input" type="radio" name="theme-layout" value="layout5" id="layout5">
								<label class="components-label" for="layout5">3 Column Left</label>
							</div><!-- .components-checkbox-group-->
						</fieldset>


						<fieldset>
							<h3>Types</h3>
							<div class="components-radio-group">
								<input class="components-input" type="radio" name="theme-type" value="type1" id="type1">
								<label class="components-label" for="type1">Modern Blog</label>
							</div><!-- .components-checkbox-group-->

							<div class="components-radio-group">
								<input class="components-input" type="radio" name="theme-type" value="type2" id="type2">
								<label class="components-label" for="type2">Business</label>
							</div><!-- .components-checkbox-group-->

							<div class="components-radio-group">
								<input class="components-input" type="radio" name="theme-type" value="type3" id="type3">
								<label class="components-label" for="type3">Portfolio</label>
							</div><!-- .components-checkbox-group-->

							<div class="components-radio-group">
								<input class="components-input" type="radio" name="theme-type" value="type4" id="type4">
								<label class="components-label" for="type4">Magazine</label>
							</div><!-- .components-checkbox-group-->


							<div class="components-radio-group">
								<input class="components-input" type="radio" name="theme-type" value="type5" id="type5">
								<label class="components-label" for="type5">E-Commerce</label>
							</div><!-- .components-checkbox-group-->

							<div class="components-radio-group">
								<input class="components-input" type="radio" name="theme-type" value="type6" id="type6">
								<label class="components-label" for="type6">One Page</label>
							</div><!-- .components-checkbox-group-->

							<div class="components-radio-group">
								<input class="components-input" type="radio" name="theme-type" value="type7" id="type7">
								<label class="components-label" for="type7">Restaurant</label>
							</div><!-- .components-checkbox-group-->
						</fieldset>

						<fieldset>
							<h3>Components</h3>

							<h4>Header</h4>

							<div class="components-checkbox-group">
								<input class="components-input" type="checkbox" name="theme-component" value="component1" id="component1">
								<label class="components-label" for="component1">Archive Header</label>
							</div><!-- .components-checkbox-group-->

							<div class="components-checkbox-group">
								<input class="components-input" type="checkbox" name="theme-component" value="component2" id="component2">
								<label class="components-label" for="component2">Branding</label>
							</div><!-- .components-checkbox-group-->

							<div class="components-checkbox-group">
								<input class="components-input" type="checkbox" name="theme-component" value="component3" id="component3">
								<label class="components-label" for="component3">Custom Header</label>
							</div><!-- .components-checkbox-group-->

							<div class="components-checkbox-group">
								<input class="components-input" type="checkbox" name="theme-component" value="component4" id="component4">
								<label class="components-label" for="component4">Site Logo</label>
							</div><!-- .components-checkbox-group-->

							<h4>Menu</h4>

							<div class="components-checkbox-group">
								<input class="components-input" type="checkbox" name="theme-component" value="component5" id="component5">
								<label class="components-label" for="component5">Menu Toggle</label>
							</div><!-- .components-checkbox-group-->

							<div class="components-checkbox-group">
								<input class="components-input" type="checkbox" name="theme-component" value="component6" id="component6">
								<label class="components-label" for="component6">Social Menu</label>
							</div><!-- .components-checkbox-group-->

							<div class="components-checkbox-group">
								<input class="components-input" type="checkbox" name="theme-component" value="component7" id="component7">
								<label class="components-label" for="component7">Top Navigation</label>
							</div><!-- .components-checkbox-group-->

							<h4>Content</h4>
							<div class="components-checkbox-group">
								<input class="components-input" type="checkbox" name="theme-component" value="component8" id="component8">
								<label class="components-label" for="component8">Content None</label>
							</div><!-- .components-checkbox-group-->

							<div class="components-checkbox-group">
								<input class="components-input" type="checkbox" name="theme-component" value="component9" id="component9">
								<label class="components-label" for="component9">Content Page</label>
							</div><!-- .components-checkbox-group-->

							<div class="components-checkbox-group">
								<input class="components-input" type="checkbox" name="theme-component" value="component10" id="component10">
								<label class="components-label" for="component10">Content Search</label>
							</div><!-- .components-checkbox-group-->

							<div class="components-checkbox-group">
								<input class="components-input" type="checkbox" name="theme-component" value="component11" id="component11">
								<label class="components-label" for="component11">Content Single</label>
							</div><!-- .components-checkbox-group-->

							<div class="components-checkbox-group">
								<input class="components-input" type="checkbox" name="theme-component" value="component12" id="component12">
								<label class="components-label" for="component12">Content</label>
							</div><!-- .components-checkbox-group-->

							<h4>Comments</h4>

							<div class="components-checkbox-group">
								<input class="components-input" type="checkbox" name="theme-component" value="component13" id="component13">
								<label class="components-label" for="component13">Comment List</label>
							</div><!-- .components-checkbox-group-->

							<div class="components-checkbox-group">
								<input class="components-input" type="checkbox" name="theme-component" value="component14" id="component14">
								<label class="components-label" for="component14">Comment Navigation</label>
							</div><!-- .components-checkbox-group-->

							<div class="components-checkbox-group">
								<input class="components-input" type="checkbox" name="theme-component" value="component15" id="component15">
								<label class="components-label" for="component15">Comment Title</label>
							</div><!-- .components-checkbox-group-->

							<h4>Footer</h4>

							<div class="components-checkbox-group">
								<input class="components-input" type="checkbox" name="theme-component" value="component16" id="component16">
								<label class="components-label" for="component16">Site Info</label>
							</div><!-- .components-checkbox-group-->

							<h4>Extras</h4>

							<div class="components-checkbox-group">
								<input class="components-input" type="checkbox" name="theme-component" value="component17" id="component17">
								<label class="components-label" for="component17">Error</label>
							</div><!-- .components-checkbox-group-->

							<div class="components-checkbox-group">
								<input class="components-input" type="checkbox" name="theme-component" value="component18" id="component18">
								<label class="components-label" for="component18">Gallery</label>
							</div><!-- .components-checkbox-group-->


							<div class="components-checkbox-group">
								<input class="components-input" type="checkbox" name="theme-component" value="component19" id="component19">
								<label class="components-label" for="component19">Search Form</label>
							</div><!-- .components-checkbox-group-->

							<div class="components-checkbox-group">
								<input class="components-input" type="checkbox" name="theme-component" value="component20" id="component20">
								<label class="components-label" for="component20">Search Header</label>
							</div><!-- .components-checkbox-group-->

						</fieldset>

						<fieldset>
							<h3>Features</h3>

							<div class="components-checkbox-group">
								<input class="components-input" type="checkbox" name="theme-component" value="component21" id="component21">
								<label class="components-label" for="component21">Testimonials</label>
							</div><!-- .components-checkbox-group-->

							<div class="components-checkbox-group">
								<input class="components-input" type="checkbox" name="theme-component" value="component22" id="component22">
								<label class="components-label" for="component22">Portfolio CPT</label>
							</div><!-- .components-checkbox-group-->

							<div class="components-checkbox-group">
								<input class="components-input" type="checkbox" name="theme-component" value="component23" id="component23">
								<label class="components-label" for="component23">Featured Content</label>
							</div><!-- .components-checkbox-group-->

							<div class="components-checkbox-group">
								<input class="components-input" type="checkbox" name="theme-component" value="component24" id="component24">
								<label class="components-label" for="component24">WooCommerce</label>
							</div><!-- .components-checkbox-group-->

							<div class="components-checkbox-group">
								<input class="components-input" type="checkbox" name="theme-component" value="component25" id="component25">
								<label class="components-label" for="component25">Site Logo</label>
							</div><!-- .components-checkbox-group-->

							<div class="components-checkbox-group">
								<input class="components-input" type="checkbox" name="theme-component" value="component26" id="component26">
								<label class="components-label" for="component26">Slider</label>
							</div><!-- .components-checkbox-group-->

							<div class="components-checkbox-group">
								<input class="components-input" type="checkbox" name="theme-component" value="component27" id="component27">
								<label class="components-label" for="component27">Restaurant CPT</label>
							</div><!-- .components-checkbox-group-->

						</fieldset>

						<fieldset>
							<h3>Templates</h3>

							<div class="components-checkbox-group">
								<input class="components-input" type="checkbox" name="theme-component" value="component28" id="component28">
								<label class="components-label" for="component28">Front Page Template</label>
							</div><!-- .components-checkbox-group-->

						</fieldset>

						<fieldset class="components-form-fields">
							<div class="components-form-field">
								<label class="components-label" for="components-name">Theme Name<span class="required"><span class="screen-reader-text">Required</span></span></label>
								<input type="text" id="components-name" class="components-input" name="components_name" placeholder="Awesome Theme" required="" aria-required="true">
							</div>

							<div class="components-form-field">
								<label class="components-label" for="components-slug">Theme Slug</label>
								<input type="text" id="components-slug" class="components-input" name="components_slug" placeholder="awesome-theme">
							</div>

							<div class="components-form-field">
								<label class="components-label" for="components-author">Author Name</label>
								<input type="text" id="components-author" class="components-input" name="components_author" placeholder="Your Name">
							</div>

							<div class="components-form-field">
								<label class="components-label" for="components-author-uri">Author URI</label>
								<input type="url" id="components-author-uri" class="components-input" name="components_author_uri" placeholder="http://themeshaper.com/">
							</div>

							<div class="components-form-field">
								<label class="components-label" for="components-description">Theme description</label>
								<input type="text" id="components-description" class="components-input" name="components_description" placeholder="A brief description of your awesome theme">
							</div>

							<div class="generator-form-submit">
								<input type="submit" name="components_generate_submit" value="Download Theme">
								<button type="button" class="components-form-cancel">Cancel</button>
							</div><!-- .generator-form-submit -->
						</fieldset>

					</form>
				</div><!-- .wrap -->
			</section><!-- #custom-build-panel -->


			<?php
			$sections = array( 'extra-info', 'contributors' );
			foreach ( $sections as $section ) {
				get_template_part( 'section', $section );
			}
			?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
