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
				<?php $hero_type = get_content_types( 'hero' ); ?>
				<?php foreach ( $hero_type as $type ) { ?>
					<h2 class="theme-type-title"><?php echo esc_attr( $type['title'] ); ?></h2>
					<p><?php echo $type['text']; ?></p>
					<a href="#generator" class="download button" data-type="<?php echo esc_attr( $type['filename'] ); ?>">Build Theme!</a>
				<?php } ?>
			</div>
		</div><!-- .theme-type -->

	</section><!-- #base -->

	<section id="types">
		<?php
			$types = get_content_types();
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