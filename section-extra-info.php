<section id="extra-info">
	<div class="wrap">

		<?php
		$extra_info_data = get_content_extra_info();
		$octo_robot_output = true;
		?>

		<?php foreach ( $extra_info_data as $key => $value ) { ?>
			<div class="col">
				<h2><?php echo $value['title']; ?></h2>
				<?php echo $value['content']; ?>
			</div>

			<?php if ( $octo_robot_output ) { $octo_robot_output = false; ?>
			<div class="octocat-robot">
				<?php echo file_get_contents( get_template_directory() . '/assets/img/robot-octocat.svg' ); ?>
			</div>
			<?php } ?>

		<?php } ?>

	</div><!-- .wrap -->
</section><!-- #extra-info -->