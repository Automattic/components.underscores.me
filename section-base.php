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